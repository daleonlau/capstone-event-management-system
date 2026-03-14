<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use App\Models\EvaluationResponse;
use App\Models\Student;
use App\Models\EventStudent;
use App\Models\Event;
use App\Models\Department;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class EvaluationController extends Controller
{
    /**
     * Show evaluation form to students
     */
    public function form($evaluationId)
    {
        try {
            Log::info('Loading evaluation form', ['evaluation_id' => $evaluationId]);

            $evaluation = Evaluation::with(['event', 'categories.questions', 'questions' => function ($q) {
                    $q->where('question_type', 'comment');
                }])
                ->where('id', $evaluationId)
                ->where('status', 'active')
                ->firstOrFail();

            // Check if evaluation is still available
            if ($evaluation->available_until && now() > $evaluation->available_until) {
                return Inertia::render('Public/Evaluations/Expired', [
                    'message' => 'This evaluation form has expired.'
                ]);
            }

            // Get eligible departments, courses, year levels from event
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

            return Inertia::render('Public/Evaluations/Form', [
                'evaluation' => [
                    'id' => $evaluation->id,
                    'title' => $evaluation->title,
                    'form_number' => $evaluation->form_number,
                    'revision' => $evaluation->revision,
                    'date_effectivity' => $evaluation->date_effectivity,
                    'event' => [
                        'id' => $evaluation->event->id,
                        'event_name' => $evaluation->event->event_name,
                        'event_date_start' => $evaluation->event->event_date_start,
                        'venue' => 'CSUCC Gymnasium',
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
     * Store evaluation responses
     */
    public function store(Request $request, $evaluationId)
    {
        try {
            Log::info('Starting evaluation submission', [
                'evaluation_id' => $evaluationId,
                'student_id' => $request->student_id
            ]);

            $evaluation = Evaluation::with('event')
                ->where('id', $evaluationId)
                ->where('status', 'active')
                ->firstOrFail();

            // Validate request
            $request->validate([
                'student_id' => 'required|string',
                'email' => 'required|email',
                'name' => 'nullable|string',
                'department' => 'required|string',
                'course' => 'required|string',
                'year_level' => 'required|string',
                'likert_responses' => 'required|array',
                'comment_responses' => 'nullable|array',
            ]);

            // Check if student is eligible for this event
            $isEligible = EventStudent::where('event_id', $evaluation->event_id)
                ->where('student_id', $request->student_id)
                ->exists();

            if (!$isEligible) {
                return response()->json([
                    'error' => 'You are not part of this event. Only students who are registered for this event can submit an evaluation.'
                ], 403);
            }

            // Check if already submitted
            $existing = EvaluationResponse::where('evaluation_id', $evaluation->id)
                ->where('student_id', $request->student_id)
                ->exists();

            if ($existing) {
                return Inertia::render('Public/Evaluations/AlreadySubmitted', [
                    'evaluation' => [
                        'event_name' => $evaluation->event->event_name,
                    ]
                ]);
            }

            DB::beginTransaction();

            // Create response
            $response = EvaluationResponse::create([
                'evaluation_id' => $evaluation->id,
                'event_id' => $evaluation->event_id,
                'student_id' => $request->student_id,
                'email' => $request->email,
                'name' => $request->name,
                'department' => $request->department,
                'course' => $request->course,
                'year_level' => $request->year_level,
                'likert_responses' => $request->likert_responses,
                'comment_responses' => $request->comment_responses ?? [],
            ]);

            // Update response count
            $evaluation->increment('total_responses');

            DB::commit();

            Log::info('Evaluation submitted successfully', [
                'evaluation_id' => $evaluation->id,
                'student_id' => $request->student_id
            ]);

            return redirect()->route('evaluations.thankyou');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to submit evaluation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Failed to submit evaluation. Please try again.');
        }
    }

    /**
     * Show thank you page
     */
    public function thankyou()
    {
        return Inertia::render('Public/Evaluations/ThankYou');
    }

    /**
     * Verify student eligibility - MUST RETURN JSON
     */
    public function verifyStudent(Request $request)
    {
        try {
            Log::info('=== VERIFY STUDENT CALLED ===', [
                'all_data' => $request->all(),
                'event_id' => $request->event_id,
                'student_id' => $request->student_id,
                'url' => request()->fullUrl()
            ]);

            $request->validate([
                'event_id' => 'required|exists:events,id',
                'student_id' => 'required|string',
            ]);

            $event = Event::find($request->event_id);
            
            if (!$event) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Event not found.'
                ]);
            }

            // Check if student exists in event_student table
            $isEligible = EventStudent::where('event_id', $event->id)
                ->where('student_id', $request->student_id)
                ->exists();

            Log::info('EventStudent check', [
                'exists' => $isEligible,
                'event_id' => $event->id,
                'student_id' => $request->student_id
            ]);

            if (!$isEligible) {
                // Debug: Show what records exist for this event
                $records = EventStudent::where('event_id', $event->id)->get();
                Log::warning('Student not found in event_student', [
                    'count' => $records->count(),
                    'records' => $records->map(fn($r) => [
                        'student_id' => $r->student_id,
                        'status' => $r->status
                    ])->toArray()
                ]);

                return response()->json([
                    'valid' => false,
                    'message' => 'Student ID not found in event participants.'
                ]);
            }

            // Get student details from students table
            $student = Student::where('student_id', $request->student_id)
                ->where('user_id', $event->user_id)
                ->first();

            return response()->json([
                'valid' => true,
                'student' => $student ? [
                    'name' => $student->firstname . ' ' . $student->lastname,
                    'email' => $student->email,
                ] : null,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', ['errors' => $e->errors()]);
            return response()->json([
                'valid' => false,
                'message' => 'Validation failed: ' . json_encode($e->errors())
            ], 422);
        } catch (\Exception $e) {
            Log::error('Student verification failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'valid' => false,
                'message' => 'Verification failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Debug endpoint to check event_student records (remove in production)
     */
    public function debugEventStudents($eventId)
    {
        try {
            $records = EventStudent::where('event_id', $eventId)->get();
            
            return response()->json([
                'event_id' => $eventId,
                'count' => $records->count(),
                'students' => $records->map(fn($r) => [
                    'student_id' => $r->student_id,
                    'status' => $r->status,
                    'amount_paid' => $r->amount_paid,
                    'created_at' => $r->created_at
                ])
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}