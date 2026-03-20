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
     * Show evaluation form with verification section
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

            // Get basic info from form_customizations
            $basicInfo = $evaluation->form_customizations ?? [];

            return Inertia::render('Public/Evaluations/Form', [
                'evaluation' => [
                    'id' => $evaluation->id,
                    'title' => $evaluation->title,
                    'form_number' => $evaluation->form_number,
                    'revision' => $evaluation->revision,
                    'date_effectivity' => $evaluation->date_effectivity,
                    'form_type' => $evaluation->form_type,
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
     * Verify student eligibility via AJAX
     */
    public function verifyStudent(Request $request, $evaluationId)
    {
        try {
            Log::info('=== VERIFY STUDENT CALLED ===', [
                'evaluation_id' => $evaluationId,
                'student_id' => $request->student_id
            ]);

            $request->validate([
                'student_id' => 'required|string',
            ]);

            $evaluation = Evaluation::with('event')
                ->where('id', $evaluationId)
                ->where('status', 'active')
                ->firstOrFail();

            $event = $evaluation->event;

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
                return response()->json([
                    'success' => false,
                    'message' => 'Student ID not found in event participants. Please check your ID or contact your organization.'
                ], 422);
            }

            // Check if already submitted
            $existing = EvaluationResponse::where('evaluation_id', $evaluation->id)
                ->where('student_id', $request->student_id)
                ->exists();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'already_submitted' => true,
                    'message' => 'You have already submitted an evaluation for this event.'
                ], 422);
            }

            // Get student details from students table
            $student = Student::where('student_id', $request->student_id)
                ->where('user_id', $event->user_id)
                ->first();

            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student record not found. Please contact your organization.'
                ], 422);
            }

            // Return student data
            return response()->json([
                'success' => true,
                'student' => [
                    'student_id' => $student->student_id,
                    'email' => $student->email,
                    'name' => $student->firstname . ' ' . $student->lastname,
                    'department' => $student->department,
                    'course' => $student->course,
                    'year_level' => $student->yearlevel,
                ]
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

            // Validate request with all profile fields
            $request->validate([
                'student_id' => 'required|string',
                'email' => 'required|email',
                'name' => 'nullable|string',
                'age' => 'nullable|string',
                'sex' => 'nullable|string',
                'agency_office' => 'nullable|string',
                'position' => 'nullable|string',
                'respondent_type' => 'nullable|string',
                'title_prefix' => 'nullable|string',
                'department' => 'required|string',
                'course' => 'required|string',
                'year_level' => 'required|string',
                'speaker_topic' => 'nullable|string',
                'speaker_name' => 'nullable|string',
                'likert_responses' => 'required|array',
                'comment_responses' => 'nullable|array',
            ]);

            // Verify student is eligible for this event
            $isEligible = EventStudent::where('event_id', $evaluation->event_id)
                ->where('student_id', $request->student_id)
                ->exists();

            if (!$isEligible) {
                return back()->withErrors([
                    'student_id' => 'Student ID not found in event participants. Please check your ID or contact your organization.'
                ])->withInput();
            }

            // Check if already submitted
            $existing = EvaluationResponse::where('evaluation_id', $evaluation->id)
                ->where('student_id', $request->student_id)
                ->exists();

            if ($existing) {
                return redirect()->route('evaluations.already-submitted', $evaluation->id);
            }

            // Get student record for verification
            $student = Student::where('student_id', $request->student_id)
                ->where('user_id', $evaluation->event->user_id)
                ->first();

            if (!$student) {
                return back()->withErrors([
                    'student_id' => 'Student record not found. Please contact your organization.'
                ])->withInput();
            }

            DB::beginTransaction();

            // Create response
            $response = EvaluationResponse::create([
                'evaluation_id' => $evaluation->id,
                'event_id' => $evaluation->event_id,
                'student_id' => $request->student_id,
                'email' => $request->email,
                'name' => $request->name ?? ($student->firstname . ' ' . $student->lastname),
                'age' => $request->age,
                'sex' => $request->sex,
                'agency_office' => $request->agency_office,
                'position' => $request->position,
                'respondent_type' => $request->respondent_type,
                'title_prefix' => $request->title_prefix,
                'department' => $request->department,
                'course' => $request->course,
                'year_level' => $request->year_level,
                'speaker_topic' => $request->speaker_topic,
                'speaker_name' => $request->speaker_name,
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