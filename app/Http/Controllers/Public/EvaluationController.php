<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use App\Models\EvaluationResponse;
use App\Models\Student;
use App\Models\EventStudent;
use App\Models\Event;
use App\Models\EventGuest;
use App\Models\Department;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Carbon\Carbon;

class EvaluationController extends Controller
{
    /**
     * Display the evaluation form
     */
    public function form($evaluationId)
    {
        try {
            $evaluation = Evaluation::with(['event', 'categories.questions', 'questions' => function ($q) {
                    $q->where('question_type', 'comment');
                }])
                ->where('id', $evaluationId)
                ->where('status', 'active')
                ->firstOrFail();

            if ($evaluation->available_until && now() > $evaluation->available_until) {
                return Inertia::render('Public/Evaluations/Expired', [
                    'message' => 'This evaluation form has expired.'
                ]);
            }

            $event = $evaluation->event;
            
            $departments = [];
            if (!empty($event->departments)) {
                $departments = Department::whereIn('id', $event->departments)
                    ->get(['id', 'name', 'code']);
            }

            $courses = [];
            if (!empty($event->courses)) {
                $courses = Course::whereIn('id', $event->courses)
                    ->get(['id', 'name', 'code']);
            }

            $yearLevels = $event->year_levels ?? [];
            $basicInfo = $evaluation->form_customizations ?? [];

            $eventDates = $evaluation->event_dates ?: [];
            
            if (empty($eventDates) && $event->event_date_start && $event->event_date_end) {
                $start = Carbon::parse($event->event_date_start);
                $end = Carbon::parse($event->event_date_end);
                for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                    $eventDates[] = $date->format('Y-m-d');
                }
            }

            return Inertia::render('Public/Evaluations/Form', [
                'evaluation' => [
                    'id' => $evaluation->id,
                    'title' => $evaluation->title,
                    'form_number' => $evaluation->form_number,
                    'revision' => $evaluation->revision,
                    'date_effectivity' => $evaluation->date_effectivity,
                    'form_type' => $evaluation->form_type,
                    'event_dates' => $eventDates,
                    'customizations' => $basicInfo,
                    'event' => [
                        'id' => $evaluation->event->id,
                        'event_name' => $evaluation->event->event_name,
                    ],
                    'categories' => $evaluation->categories->map(function ($cat) {
                        return [
                            'id' => $cat->id,
                            'name' => $cat->category_name,
                            'questions' => $cat->questions->map(function ($q) {
                                return [
                                    'id' => $q->id,
                                    'text' => $q->question_text,
                                    'required' => $q->is_required,
                                ];
                            }),
                        ];
                    }),
                    'comments' => $evaluation->questions->where('question_type', 'comment')->map(function ($q) {
                        return [
                            'id' => $q->id,
                            'text' => $q->question_text,
                            'required' => $q->is_required,
                        ];
                    })->values(),
                ],
                'departments' => $departments,
                'courses' => $courses,
                'yearLevels' => $yearLevels,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to load evaluation form', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return Inertia::render('Public/Evaluations/Expired', [
                'message' => 'Evaluation form not found or is no longer active.'
            ]);
        }
    }

    /**
     * Verify student or guest - Guests can verify using their NAME
     */
    public function verifyStudent(Request $request, $evaluationId)
    {
        try {
            Log::info('=== VERIFY STUDENT/GUEST CALLED ===');
            Log::info('Input: ' . $request->student_id);
            Log::info('Event Date: ' . $request->event_date);
            
            $request->validate([
                'student_id' => 'required|string',
                'event_date' => 'required|date',
            ]);

            $evaluation = Evaluation::with('event')
                ->where('id', $evaluationId)
                ->where('status', 'active')
                ->firstOrFail();

            $event = $evaluation->event;
            $eventDates = $evaluation->event_dates ?: [];

            // Verify the selected date is valid
            if (!in_array($request->event_date, $eventDates)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid date selected.'
                ], 422);
            }

            // First, check if it's a GUEST (by name - case insensitive)
            $guest = EventGuest::where('event_id', $event->id)
                ->where('name', 'LIKE', $request->student_id)
                ->first();
            
            $isGuest = false;
            $studentData = null;
            $identifier = '';
            
            if ($guest) {
                // It's a guest! Use their data
                $isGuest = true;
                $identifier = $guest->guest_id;
                $studentData = [
                    'student_id' => $guest->guest_id,
                    'email' => $guest->email,
                    'name' => $guest->name,
                    'agency_office' => $guest->agency_office ?? '',
                    'position' => $guest->position ?? '',
                    'respondent_type' => 'Guest',
                    'is_guest' => true
                ];
                Log::info('Guest verified: ' . $guest->name . ' (ID: ' . $guest->guest_id . ')');
            } else {
                // Not a guest, check if it's a student by Student ID
                $eventStudent = EventStudent::where('event_id', $event->id)
                    ->where('student_id', $request->student_id)
                    ->first();
                
                if ($eventStudent) {
                    $student = Student::where('student_id', $request->student_id)
                        ->where('user_id', $event->user_id)
                        ->first();

                    if ($student) {
                        $identifier = $student->student_id;
                        $studentData = [
                            'student_id' => $student->student_id,
                            'email' => $student->email,
                            'name' => $student->firstname . ' ' . $student->lastname,
                            'agency_office' => $student->department ?? '',
                            'position' => '',
                            'respondent_type' => 'Student',
                            'is_guest' => false
                        ];
                        Log::info('Student verified: ' . $student->student_id);
                    }
                }
            }

            if (!$studentData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Name or ID not found for this event. Please check your Student ID or Guest Name.'
                ], 422);
            }

            // Check if already submitted for this date
            $existing = EvaluationResponse::where('evaluation_id', $evaluation->id)
                ->where('student_id', $identifier)
                ->where('event_date', $request->event_date)
                ->exists();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already submitted for ' . Carbon::parse($request->event_date)->format('F d, Y') . '.',
                    'already_submitted' => true
                ], 422);
            }

            return response()->json([
                'success' => true,
                'message' => 'Verification successful!',
                'student' => $studentData,
                'event_date' => $request->event_date,
                'is_guest' => $isGuest
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Student verification failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Verification failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get student's already submitted dates
     */
    public function getStudentSubmissions($evaluationId, Request $request)
    {
        try {
            $studentId = $request->get('student_id');
            
            if (!$studentId) {
                return response()->json(['submitted_dates' => []]);
            }
            
            $submittedDates = EvaluationResponse::where('evaluation_id', $evaluationId)
                ->where('student_id', $studentId)
                ->pluck('event_date')
                ->map(function($date) {
                    return $date instanceof \DateTime ? $date->format('Y-m-d') : $date;
                })
                ->toArray();
            
            return response()->json([
                'submitted_dates' => $submittedDates
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get student submissions', [
                'error' => $e->getMessage()
            ]);
            return response()->json(['submitted_dates' => []], 500);
        }
    }

    /**
     * Store evaluation response
     */
    public function store(Request $request, $evaluationId)
    {
        try {
            Log::info('=== EVALUATION SUBMISSION START ===');
            Log::info('Evaluation ID: ' . $evaluationId);
            Log::info('Student ID: ' . $request->input('student_id'));
            Log::info('Event Date: ' . $request->input('event_date'));
            Log::info('Is Guest: ' . (str_starts_with($request->input('student_id'), 'GUEST-') ? 'Yes' : 'No'));

            $evaluation = Evaluation::with('event')
                ->where('id', $evaluationId)
                ->where('status', 'active')
                ->firstOrFail();

            $event = $evaluation->event;
            $eventDates = $evaluation->event_dates ?: [];

            $validated = $request->validate([
                'student_id' => 'required|string',
                'email' => 'required|email',
                'name' => 'nullable|string',
                'age' => 'nullable|string',
                'sex' => 'nullable|string',
                'agency_office' => 'nullable|string',
                'position' => 'nullable|string',
                'respondent_type' => 'nullable|string',
                'title_prefix' => 'nullable|string',
                'event_date' => 'required|date',
                'speaker_topic' => 'nullable|string',
                'speaker_name' => 'nullable|string',
                'likert_responses' => 'required|array',
                'comment_responses' => 'nullable|array',
            ]);

            // Verify event date is valid
            if (!in_array($validated['event_date'], $eventDates)) {
                return response()->json([
                    'error' => 'Invalid event date selected. Valid dates: ' . implode(', ', $eventDates)
                ], 422);
            }

            // Check for duplicate submission for this specific date
            $existing = EvaluationResponse::where('evaluation_id', $evaluation->id)
                ->where('student_id', $validated['student_id'])
                ->where('event_date', $validated['event_date'])
                ->exists();

            if ($existing) {
                return response()->json([
                    'error' => 'You have already submitted for ' . Carbon::parse($validated['event_date'])->format('F d, Y') . '.'
                ], 422);
            }

            // Verify eligibility
            $isGuest = str_starts_with($validated['student_id'], 'GUEST-');
            
            if (!$isGuest) {
                // Check if student is in event_student
                $eventStudent = EventStudent::where('event_id', $event->id)
                    ->where('student_id', $validated['student_id'])
                    ->first();

                if (!$eventStudent) {
                    return response()->json([
                        'error' => 'Student ID not found for this event.'
                    ], 422);
                }
            } else {
                // Check if guest exists
                $guest = EventGuest::where('event_id', $event->id)
                    ->where('guest_id', $validated['student_id'])
                    ->first();
                    
                if (!$guest) {
                    return response()->json([
                        'error' => 'Guest ID not found for this event.'
                    ], 422);
                }
            }

            // Calculate date index
            $dateIndex = array_search($validated['event_date'], $eventDates) + 1;

            DB::beginTransaction();

            // Create the response
            $response = EvaluationResponse::create([
                'evaluation_id' => $evaluation->id,
                'event_id' => $event->id,
                'event_date' => $validated['event_date'],
                'date_index' => $dateIndex,
                'student_id' => $validated['student_id'],
                'email' => $validated['email'],
                'name' => $validated['name'],
                'age' => $validated['age'],
                'sex' => $validated['sex'],
                'agency_office' => $validated['agency_office'],
                'position' => $validated['position'],
                'respondent_type' => $validated['respondent_type'],
                'title_prefix' => $validated['title_prefix'],
                'speaker_topic' => $validated['speaker_topic'],
                'speaker_name' => $validated['speaker_name'],
                'likert_responses' => $validated['likert_responses'],
                'comment_responses' => $validated['comment_responses'] ?? [],
            ]);

            $evaluation->increment('total_responses');

            DB::commit();

            Log::info('Response created - ID: ' . $response->id);
            Log::info('event_date saved: ' . $response->event_date);
            Log::info('date_index saved: ' . $response->date_index);

            return response()->json([
                'success' => true,
                'message' => 'Evaluation submitted successfully!',
                'response_id' => $response->id
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', ['errors' => $e->errors()]);
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            Log::error('Database error during submission', [
                'error' => $e->getMessage(),
                'sql' => $e->getSql()
            ]);
            
            // Check if it's a duplicate entry error
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                return response()->json([
                    'error' => 'You have already submitted for this date.'
                ], 422);
            }
            
            return response()->json([
                'error' => 'Database error: ' . $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to submit evaluation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Failed to submit evaluation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available dates for an evaluation
     */
    public function getAvailableDates($evaluationId)
    {
        try {
            $evaluation = Evaluation::findOrFail($evaluationId);
            $eventDates = $evaluation->event_dates ?: [];
            
            if (empty($eventDates) && $evaluation->event->event_date_start && $evaluation->event->event_date_end) {
                $start = Carbon::parse($evaluation->event->event_date_start);
                $end = Carbon::parse($evaluation->event->event_date_end);
                for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                    $eventDates[] = $date->format('Y-m-d');
                }
            }
            
            return response()->json([
                'dates' => $eventDates,
                'event_name' => $evaluation->event->event_name,
            ]);
        } catch (\Exception $e) {
            return response()->json(['dates' => []], 500);
        }
    }

    /**
     * Show already submitted page
     */
    public function alreadySubmitted($evaluationId)
    {
        $evaluation = Evaluation::with('event')->findOrFail($evaluationId);
        
        return Inertia::render('Public/Evaluations/AlreadySubmitted', [
            'evaluation' => [
                'event_name' => $evaluation->event->event_name,
            ]
        ]);
    }

    /**
     * Show thank you page
     */
    public function thankyou()
    {
        return Inertia::render('Public/Evaluations/ThankYou');
    }
}