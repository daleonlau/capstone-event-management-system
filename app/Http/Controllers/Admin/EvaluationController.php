<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use App\Models\EvaluationRequest;
use App\Models\Event;
use App\Models\EvaluationCategory;
use App\Models\EvaluationQuestion;
use App\Models\EvaluationResponse;
use App\Models\EventStudent;
use App\Models\EventGuest;
use App\Models\AIAnalysis;
use App\Models\Department;
use App\Models\Course;
use App\Models\Student;
use App\Jobs\AnalyzeEvaluationJob;
use App\Services\AIAnalysisService;
use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Carbon\Carbon;

class EvaluationController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        
        $evaluations = Evaluation::with(['event', 'event.creator'])
            ->when($search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhereHas('event', function ($q) use ($search) {
                        $q->where('event_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('event.creator', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($evaluation) {
                $event = $evaluation->event;
                $eventDates = $evaluation->event_dates ?: [];
                $numberOfDates = count($eventDates);
                
                $totalStudents = EventStudent::where('event_id', $event->id)->count();
                $totalGuests = EventGuest::where('event_id', $event->id)->count();
                $totalExpected = ($totalStudents * max($numberOfDates, 1)) + $totalGuests;
                
                $responseRate = $totalExpected > 0 
                    ? round(($evaluation->total_responses / $totalExpected) * 100, 1) 
                    : 0;
                
                return [
                    'id' => $evaluation->id,
                    'title' => $evaluation->title,
                    'form_type' => $evaluation->form_type,
                    'status' => $evaluation->status,
                    'event_name' => $evaluation->event->event_name,
                    'organization_name' => $evaluation->event->creator->name,
                    'responses_count' => $evaluation->total_responses,
                    'expected_count' => $totalExpected,
                    'response_rate' => $responseRate,
                    'students_count' => $totalStudents,
                    'guests_count' => $totalGuests,
                    'number_of_dates' => $numberOfDates,
                    'event_dates' => $eventDates,
                    'created_at' => $evaluation->created_at->format('Y-m-d'),
                ];
            });

        $pendingRequestsCount = EvaluationRequest::where('status', 'pending')->count();

        // Log view action (admin viewing evaluations list)
        $this->logAction('view_evaluations_list', 'Viewed evaluations list', [
            'total_evaluations' => $evaluations->count(),
            'pending_requests' => $pendingRequestsCount,
            'search_term' => $search,
        ]);

        return Inertia::render('Admin/Evaluations/Index', [
            'evaluations' => $evaluations,
            'pendingRequestsCount' => $pendingRequestsCount,
            'search' => $search,
        ]);
    }

    public function getPendingRequests()
    {
        $requests = EvaluationRequest::with(['event', 'organization', 'requestedBy'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($request) {
                return [
                    'id' => $request->id,
                    'title' => $request->title,
                    'event_name' => $request->event->event_name,
                    'event_status' => $request->event->status,
                    'organization_name' => $request->organization->name,
                    'requested_by' => $request->requestedBy->name,
                    'activity_date' => $request->activity_date->format('Y-m-d'),
                    'event_dates' => $request->event_dates ?: [],
                    'venue' => $request->venue,
                    'speaker_name' => $request->speaker_name,
                    'topics' => $request->topics,
                    'has_food' => $request->has_food,
                    'created_at' => $request->created_at->diffForHumans(),
                ];
            });

        return response()->json($requests);
    }

    public function create(Request $request)
    {
        $pendingRequests = EvaluationRequest::with(['event', 'organization'])
            ->where('status', 'pending')
            ->get()
            ->map(function ($request) {
                return [
                    'id' => $request->id,
                    'title' => $request->title,
                    'event_name' => $request->event->event_name,
                    'organization_name' => $request->organization->name,
                    'activity_date' => $request->activity_date->format('Y-m-d'),
                    'event_dates' => $request->event_dates ?: [],
                ];
            });

        $formTypes = [
            'type1' => '7 Quality Dimension (Standard)',
            'type2' => '5 Quality Dimension (Basic)',
            'type3' => '8 Quality Dimension (Comprehensive with Food & Speaker)',
            'type4' => '6 Quality Dimension (With Speaker, No Food)',
            'type5' => '6 Quality Dimension (With Food, No Speaker)',
        ];

        // Log view action
        $this->logAction('view_create_evaluation_form', 'Viewed evaluation creation form', [
            'pending_requests_count' => $pendingRequests->count(),
        ]);

        return Inertia::render('Admin/Evaluations/Create', [
            'pendingRequests' => $pendingRequests,
            'formTypes' => $formTypes,
            'selectedRequestId' => $request->query('request_id'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'evaluation_request_id' => 'required|exists:evaluation_requests,id',
            'form_type' => 'required|in:type1,type2,type3,type4,type5',
            'title' => 'required|string|max:255',
            'form_number' => 'required|string|max:50',
            'revision' => 'required|string|max:20',
            'date_effectivity' => 'required|string|max:50',
            'available_from' => 'nullable|date',
            'available_until' => 'nullable|date|after:available_from',
        ]);

        $evaluationRequest = EvaluationRequest::findOrFail($validated['evaluation_request_id']);
        $event = $evaluationRequest->event;

        DB::beginTransaction();

        try {
            $eventDates = $evaluationRequest->event_dates ?: [];

            $evaluation = Evaluation::create([
                'event_id' => $event->id,
                'organization_id' => $event->user_id,
                'title' => $validated['title'],
                'form_type' => $validated['form_type'],
                'form_customizations' => [
                    'original_title' => $evaluationRequest->title,
                    'activity_date' => $evaluationRequest->activity_date,
                    'event_dates' => $eventDates,
                    'venue' => $evaluationRequest->venue,
                    'speaker_name' => $evaluationRequest->speaker_name,
                    'topics' => $evaluationRequest->topics,
                    'has_food' => $evaluationRequest->has_food,
                ],
                'event_dates' => $eventDates,
                'form_number' => $validated['form_number'],
                'revision' => $validated['revision'],
                'date_effectivity' => $validated['date_effectivity'],
                'available_from' => $validated['available_from'],
                'available_until' => $validated['available_until'],
                'status' => 'draft',
            ]);

            $this->createEvaluationStructure($evaluation, $validated['form_type']);

            $evaluationRequest->update([
                'status' => 'completed',
                'evaluation_id' => $evaluation->id,
            ]);

            DB::commit();

            // Log evaluation creation
            $this->logAction('create_evaluation', 'Created evaluation: ' . $evaluation->title, [
                'evaluation_id' => $evaluation->id,
                'title' => $evaluation->title,
                'form_type' => $evaluation->form_type,
                'form_number' => $evaluation->form_number,
                'event_id' => $event->id,
                'event_name' => $event->event_name,
                'evaluation_request_id' => $evaluationRequest->id,
                'organization_id' => $event->user_id,
            ]);

            return redirect()->route('admin.evaluations.show', $evaluation->id)
                ->with('success', 'Evaluation created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create evaluation: ' . $e->getMessage());
            
            $this->logError('create_evaluation_failed', 'Failed to create evaluation: ' . $e->getMessage(), $e, [
                'evaluation_request_id' => $validated['evaluation_request_id'],
                'title' => $validated['title'],
                'form_type' => $validated['form_type'],
            ]);
            
            return back()->withErrors(['error' => 'Failed to create evaluation: ' . $e->getMessage()]);
        }
    }

    private function createEvaluationStructure(Evaluation $evaluation, $formType)
    {
        $templates = $this->getFormTemplates();
        $template = $templates[$formType] ?? $templates['type1'];

        foreach ($template['categories'] as $catIndex => $categoryData) {
            $category = EvaluationCategory::create([
                'evaluation_id' => $evaluation->id,
                'category_name' => $categoryData['name'],
                'order' => $catIndex + 1,
            ]);

            foreach ($categoryData['questions'] as $qIndex => $questionText) {
                EvaluationQuestion::create([
                    'evaluation_id' => $evaluation->id,
                    'category_id' => $category->id,
                    'question_text' => $questionText,
                    'question_type' => 'likert',
                    'order' => $qIndex + 1,
                    'is_required' => true,
                ]);
            }
        }

        foreach ($template['comments'] as $cIndex => $commentText) {
            EvaluationQuestion::create([
                'evaluation_id' => $evaluation->id,
                'category_id' => null,
                'question_text' => $commentText,
                'question_type' => 'comment',
                'order' => $cIndex + 1,
                'is_required' => false,
            ]);
        }
    }

    private function getFormTemplates()
    {
        return [
            'type1' => [
                'categories' => [
                    ['name' => 'I. Information Dissemination', 'questions' => [
                        'Timeliness of sending invites',
                        'Adequacy of information dissemination'
                    ]],
                    ['name' => 'II. Design of the Event', 'questions' => [
                        'Program / Order of activities',
                        'Relevance of the activities',
                        'Time allotment / pacing'
                    ]],
                    ['name' => 'III. Outcomes of the Event', 'questions' => [
                        'Attendance of participants',
                        'Participation to activities',
                        'Interaction',
                        'Teamwork'
                    ]],
                    ['name' => 'IV. Secretariat', 'questions' => [
                        'Sensitivity in providing assistance/needs to the participants',
                        'Management on the entire activities',
                        'Provision of information/feedback to the participants in a clear, concise manner'
                    ]],
                    ['name' => 'V. Facilities', 'questions' => [
                        'Overall appearance of the venue',
                        'Cleanliness and orderliness',
                        'Availability and functionality of applicable equipment'
                    ]],
                    ['name' => 'VI. Food', 'questions' => [
                        'Quality of food and beverages',
                        'Food and beverages presentation/setup',
                        'Timeliness of delivery of food',
                        'Quality of service provided',
                        'Sufficiency of foods',
                        'Quantity/Serving of food provided'
                    ]],
                    ['name' => 'VII. Resource Speaker', 'questions' => [
                        'Methods/strategy employed',
                        'Mastery of the subject matter',
                        'Ability to draw and maintain interest and participation',
                        'Relevancy and applicability of the topic/content discussed'
                    ]]
                ],
                'comments' => [
                    'VIII. Positive Comments',
                    'IX. Suggestions/Recommendations for Improvement'
                ]
            ],
            'type2' => [
                'categories' => [
                    ['name' => 'I. Information Dissemination', 'questions' => [
                        'Timeliness of sending invites',
                        'Adequacy of information dissemination'
                    ]],
                    ['name' => 'II. Design of the Event', 'questions' => [
                        'Program / Order of activities',
                        'Relevance of the activities',
                        'Time allotment / pacing'
                    ]],
                    ['name' => 'III. Outcomes of the Event', 'questions' => [
                        'Attendance of participants',
                        'Participation to activities',
                        'Interaction',
                        'Teamwork'
                    ]],
                    ['name' => 'IV. Secretariat', 'questions' => [
                        'Sensitivity in providing assistance/needs to the participants',
                        'Management on the entire activities',
                        'Provision of information/feedback to the participants in a clear, concise manner'
                    ]],
                    ['name' => 'V. Facilities', 'questions' => [
                        'Overall appearance of the venue',
                        'Cleanliness and orderliness',
                        'Availability and functionality of applicable equipment'
                    ]]
                ],
                'comments' => [
                    'VI. Positive Comments',
                    'VII. Suggestions/Recommendations for Improvement'
                ]
            ],
            'type3' => [
                'categories' => [
                    ['name' => 'I. Information Dissemination', 'questions' => [
                        'Timeliness of sending invites',
                        'Adequacy of information dissemination'
                    ]],
                    ['name' => 'II. Design of the Event', 'questions' => [
                        'Program / Order of activities',
                        'Relevance of the activities',
                        'Time allotment / pacing'
                    ]],
                    ['name' => 'III. Outcomes of the Event', 'questions' => [
                        'Attendance of participants',
                        'Participation to activities',
                        'Timeliness and orderliness of the overall event',
                        'Execution of awarding and recognition of graduates'
                    ]],
                    ['name' => 'IV. Secretariat', 'questions' => [
                        'Sensitivity in providing assistance to the participants',
                        'Management of the entire activities',
                        'Provision of information/feedback to the participants in a clear, concise manner'
                    ]],
                    ['name' => 'V. Venue and other Facilities', 'questions' => [
                        'Overall appearance of the venue',
                        'Cleanliness and orderliness',
                        'Comfortability of room temperature and ventilation',
                        'Functionality and quality of audio-visual equipment',
                        'Suitability of the venue for the number of participants/guests'
                    ]],
                    ['name' => 'VI. Food (For Students, Guests, Faculty and Working Committee)', 'questions' => [
                        'Quality of foods and beverages',
                        'Food and beverages presentation/setup',
                        'Timeliness in the delivery of food',
                        'Quality of services provided',
                        'Sufficiency of foods',
                        'Quantity/Serving of food provided'
                    ]],
                    ['name' => 'VII. Resource Speaker', 'questions' => [
                        'Methods/strategy employed',
                        'Mastery of the subject matter',
                        'Ability to draw and maintain interest and participation',
                        'Relevance and applicability of the topic/content discussed'
                    ]],
                    ['name' => 'VIII. Traffic Management', 'questions' => [
                        'Traffic control management',
                        'Clarity of signs and instruction',
                        'Traffic capacity and safety'
                    ]]
                ],
                'comments' => [
                    'IX. What went well?',
                    'X. What went not-so-well?',
                    'XI. What should we change for the next time we hold this event?',
                    'XII. Any recommendations for improvement?'
                ]
            ],
            'type4' => [
                'categories' => [
                    ['name' => 'I. Information Dissemination', 'questions' => [
                        'Timeliness of sending invites',
                        'Adequacy of information dissemination'
                    ]],
                    ['name' => 'II. Design of the Event', 'questions' => [
                        'Program / Order of activities',
                        'Relevance of the activities',
                        'Time allotment / pacing'
                    ]],
                    ['name' => 'III. Outcomes of the Event', 'questions' => [
                        'Attendance of participants',
                        'Participation to activities',
                        'Interaction',
                        'Teamwork'
                    ]],
                    ['name' => 'IV. Secretariat', 'questions' => [
                        'Sensitivity in providing assistance/needs to the participants',
                        'Management on the entire activities',
                        'Provision of information/feedback to the participants in a clear, concise manner'
                    ]],
                    ['name' => 'V. Facilities', 'questions' => [
                        'Overall appearance of the venue',
                        'Cleanliness and orderliness',
                        'Availability and functionality of applicable equipment'
                    ]],
                    ['name' => 'VI. Resource Speaker', 'questions' => [
                        'Methods/strategy employed',
                        'Mastery of the subject matter',
                        'Ability to draw and maintain interest and participation',
                        'Relevancy and applicability of the topic/content discussed'
                    ]]
                ],
                'comments' => [
                    'VII. Positive Comments',
                    'VIII. Suggestions/Recommendations for Improvement'
                ]
            ],
            'type5' => [
                'categories' => [
                    ['name' => 'I. Information Dissemination', 'questions' => [
                        'Timeliness of sending invites',
                        'Adequacy of information dissemination'
                    ]],
                    ['name' => 'II. Design of the Event', 'questions' => [
                        'Program / Order of activities',
                        'Relevance of the activities',
                        'Time allotment / pacing'
                    ]],
                    ['name' => 'III. Outcomes of the Event', 'questions' => [
                        'Attendance of participants',
                        'Participation to activities',
                        'Interaction',
                        'Teamwork'
                    ]],
                    ['name' => 'IV. Secretariat', 'questions' => [
                        'Sensitivity in providing assistance/needs to the participants',
                        'Management on the entire activities',
                        'Provision of information/feedback to the participants in a clear, concise manner'
                    ]],
                    ['name' => 'V. Facilities', 'questions' => [
                        'Overall appearance of the venue',
                        'Cleanliness and orderliness',
                        'Availability and functionality of applicable equipment'
                    ]],
                    ['name' => 'VI. Food', 'questions' => [
                        'Quality of food and beverages',
                        'Food and beverages presentation/setup',
                        'Timeliness of delivery of food',
                        'Quality of service provided',
                        'Sufficiency of foods',
                        'Quantity/Serving of food provided'
                    ]]
                ],
                'comments' => [
                    'VII. Positive Comments',
                    'VIII. Suggestions/Recommendations for Improvement'
                ]
            ]
        ];
    }

    public function show(Evaluation $evaluation)
    {
        $evaluation->load(['event', 'categories.questions', 'questions' => function ($q) {
            $q->where('question_type', 'comment');
        }]);

        $responses = EvaluationResponse::where('evaluation_id', $evaluation->id)->get();
        
        // ==================== CALCULATE OVERALL SATISFACTION FROM RAW RESPONSES ====================
        $totalRatingSum = 0;
        $totalRatingCount = 0;
        
        foreach ($responses as $response) {
            $likert = $response->likert_responses;
            if (is_string($likert)) {
                $likert = json_decode($likert, true);
            }
            if (is_array($likert)) {
                foreach ($likert as $rating) {
                    if (is_numeric($rating)) {
                        $totalRatingSum += $rating;
                        $totalRatingCount++;
                    }
                }
            }
        }
        
        $overallSatisfaction = $totalRatingCount > 0 ? round($totalRatingSum / $totalRatingCount, 2) : 0;
        
        $event = $evaluation->event;
        $eventDates = $evaluation->event_dates ?: [];
        $numberOfDates = count($eventDates);
        
        $totalStudents = EventStudent::where('event_id', $event->id)->count();
        $totalGuests = EventGuest::where('event_id', $event->id)->count();
        
        $perDateStats = [];
        $totalResponsesOverall = 0;
        $totalExpectedOverall = 0;
        
        foreach ($eventDates as $index => $date) {
            $dateResponses = EvaluationResponse::where('evaluation_id', $evaluation->id)
                ->where('event_date', $date)
                ->count();
            
            $dateGuests = EventGuest::where('event_id', $event->id)
                ->where(function($query) use ($date) {
                    if (Schema::hasColumn('event_guests', 'event_date')) {
                        $query->where('event_date', $date);
                    }
                })
                ->count();
            
            $dateExpected = $totalStudents + $dateGuests;
            $dateResponseRate = $dateExpected > 0 ? round(($dateResponses / $dateExpected) * 100, 1) : 0;
            
            // Calculate per-date satisfaction from raw responses
            $dateResponsesData = EvaluationResponse::where('evaluation_id', $evaluation->id)
                ->where('event_date', $date)
                ->get();
            
            $dateTotalRatingSum = 0;
            $dateTotalRatingCount = 0;
            foreach ($dateResponsesData as $resp) {
                $likert = $resp->likert_responses;
                if (is_string($likert)) {
                    $likert = json_decode($likert, true);
                }
                if (is_array($likert)) {
                    foreach ($likert as $rating) {
                        if (is_numeric($rating)) {
                            $dateTotalRatingSum += $rating;
                            $dateTotalRatingCount++;
                        }
                    }
                }
            }
            $dateOverallSatisfaction = $dateTotalRatingCount > 0 ? round($dateTotalRatingSum / $dateTotalRatingCount, 2) : 0;
            
            $perDateStats[] = [
                'date' => $date,
                'date_index' => $index + 1,
                'formatted_date' => Carbon::parse($date)->format('F d, Y'),
                'responses' => $dateResponses,
                'expected' => $dateExpected,
                'response_rate' => $dateResponseRate,
                'students' => $totalStudents,
                'guests' => $dateGuests,
                'overall_satisfaction' => $dateOverallSatisfaction,
            ];
            
            $totalResponsesOverall += $dateResponses;
            $totalExpectedOverall += $dateExpected;
        }
        
        if (empty($eventDates)) {
            $dateResponses = $evaluation->total_responses;
            $dateExpected = $totalStudents + $totalGuests;
            $dateResponseRate = $dateExpected > 0 ? round(($dateResponses / $dateExpected) * 100, 1) : 0;
            
            // Calculate satisfaction from all responses
            $allResponsesData = EvaluationResponse::where('evaluation_id', $evaluation->id)->get();
            $allRatingSum = 0;
            $allRatingCount = 0;
            foreach ($allResponsesData as $resp) {
                $likert = $resp->likert_responses;
                if (is_string($likert)) {
                    $likert = json_decode($likert, true);
                }
                if (is_array($likert)) {
                    foreach ($likert as $rating) {
                        if (is_numeric($rating)) {
                            $allRatingSum += $rating;
                            $allRatingCount++;
                        }
                    }
                }
            }
            $dateOverallSatisfaction = $allRatingCount > 0 ? round($allRatingSum / $allRatingCount, 2) : 0;
            
            $perDateStats[] = [
                'date' => $event->event_date_start,
                'date_index' => 1,
                'formatted_date' => Carbon::parse($event->event_date_start)->format('F d, Y'),
                'responses' => $dateResponses,
                'expected' => $dateExpected,
                'response_rate' => $dateResponseRate,
                'students' => $totalStudents,
                'guests' => $totalGuests,
                'overall_satisfaction' => $dateOverallSatisfaction,
            ];
            
            $totalResponsesOverall = $dateResponses;
            $totalExpectedOverall = $dateExpected;
        }
        
        $overallResponseRate = $totalExpectedOverall > 0 
            ? round(($totalResponsesOverall / $totalExpectedOverall) * 100, 1) 
            : 0;
        
        $stats = [];
        $likertQuestions = EvaluationQuestion::where('evaluation_id', $evaluation->id)
            ->where('question_type', 'likert')
            ->get();
        
        foreach ($likertQuestions as $question) {
            $ratings = [];
            foreach ($responses as $response) {
                $responses_array = $response->likert_responses ?? [];
                if (isset($responses_array[$question->id])) {
                    $ratings[] = $responses_array[$question->id];
                }
            }
            
            $total = count($ratings);
            if ($total > 0) {
                $distribution = [];
                for ($i = 1; $i <= 5; $i++) {
                    $count = count(array_filter($ratings, fn($r) => $r == $i));
                    $distribution[$i] = [
                        'count' => $count,
                        'percentage' => round(($count / $total) * 100, 2)
                    ];
                }
                
                $stats[$question->id] = [
                    'average' => round(array_sum($ratings) / $total, 2),
                    'distribution' => $distribution,
                    'total' => $total
                ];
            }
        }

        $comments = [];
        $commentQuestions = EvaluationQuestion::where('evaluation_id', $evaluation->id)
            ->where('question_type', 'comment')
            ->get();
            
        foreach ($commentQuestions as $question) {
            $commentResponses = [];
            foreach ($responses as $response) {
                $comments_array = $response->comment_responses ?? [];
                if (isset($comments_array[$question->id]) && !empty($comments_array[$question->id])) {
                    $commentResponses[] = $comments_array[$question->id];
                }
            }
            $comments[$question->id] = [
                'question' => $question->question_text,
                'responses' => $commentResponses
            ];
        }

        $aiInsights = AIAnalysis::where('evaluation_id', $evaluation->id)->first();

        $canGenerateQR = $evaluation->status === 'draft';

        // Log view action
        $this->logAction('view_evaluation_details', 'Viewed evaluation details: ' . $evaluation->title, [
            'evaluation_id' => $evaluation->id,
            'title' => $evaluation->title,
            'status' => $evaluation->status,
            'total_responses' => $evaluation->total_responses,
            'overall_response_rate' => $overallResponseRate,
            'overall_satisfaction' => $overallSatisfaction,
        ]);

        return Inertia::render('Admin/Evaluations/Show', [
            'evaluation' => [
                'id' => $evaluation->id,
                'title' => $evaluation->title,
                'form_type' => $evaluation->form_type,
                'form_number' => $evaluation->form_number,
                'revision' => $evaluation->revision,
                'date_effectivity' => $evaluation->date_effectivity,
                'status' => $evaluation->status,
                'available_from' => $evaluation->available_from,
                'available_until' => $evaluation->available_until,
                'total_responses' => $evaluation->total_responses,
                'total_responses_overall' => $totalResponsesOverall,
                'total_expected_overall' => $totalExpectedOverall,
                'overall_response_rate' => $overallResponseRate,
                'overall_satisfaction' => $overallSatisfaction,
                'students_count' => $totalStudents,
                'guests_count' => $totalGuests,
                'number_of_dates' => $numberOfDates,
                'created_at' => $evaluation->created_at->format('Y-m-d H:i'),
                'customizations' => $evaluation->form_customizations,
                'event_dates' => $evaluation->event_dates,
                'event' => [
                    'id' => $evaluation->event->id,
                    'event_name' => $evaluation->event->event_name,
                    'event_date_start' => $evaluation->event->event_date_start,
                    'event_date_end' => $evaluation->event->event_date_end,
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
            'stats' => $stats,
            'comments' => $comments,
            'aiInsights' => $aiInsights,
            'perDateStats' => $perDateStats,
            'canGenerateQR' => $canGenerateQR,
        ]);
    }

    public function edit(Evaluation $evaluation)
    {
        if ($evaluation->status !== 'draft') {
            return redirect()->route('admin.evaluations.show', $evaluation->id)
                ->with('error', 'Only draft evaluations can be edited.');
        }

        $formTypes = [
            'type1' => '7 Quality Dimension (Standard)',
            'type2' => '5 Quality Dimension (Basic)',
            'type3' => '8 Quality Dimension (Comprehensive with Food & Speaker)',
            'type4' => '6 Quality Dimension (With Speaker, No Food)',
            'type5' => '6 Quality Dimension (With Food, No Speaker)',
        ];

        // Log view action
        $this->logAction('view_edit_evaluation_form', 'Viewed evaluation edit form: ' . $evaluation->title, [
            'evaluation_id' => $evaluation->id,
            'title' => $evaluation->title,
            'form_type' => $evaluation->form_type,
        ]);

        return Inertia::render('Admin/Evaluations/Edit', [
            'evaluation' => [
                'id' => $evaluation->id,
                'title' => $evaluation->title,
                'form_type' => $evaluation->form_type,
                'form_number' => $evaluation->form_number,
                'revision' => $evaluation->revision,
                'date_effectivity' => $evaluation->date_effectivity,
                'available_from' => $evaluation->available_from,
                'available_until' => $evaluation->available_until,
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
            'formTypes' => $formTypes,
        ]);
    }

    public function update(Request $request, Evaluation $evaluation)
    {
        if ($evaluation->status !== 'draft') {
            $this->logSecurity('unauthorized_evaluation_update', 'Attempted to update non-draft evaluation', [
                'evaluation_id' => $evaluation->id,
                'title' => $evaluation->title,
                'current_status' => $evaluation->status,
            ]);
            return response()->json(['error' => 'Only draft evaluations can be edited.'], 400);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'form_type' => 'required|string',
            'form_number' => 'required|string|max:50',
            'revision' => 'required|string|max:20',
            'date_effectivity' => 'required|string|max:50',
            'available_from' => 'nullable|date',
            'available_until' => 'nullable|date|after:available_from',
            'categories' => 'required|array',
            'comments' => 'nullable|array',
        ]);

        DB::beginTransaction();

        try {
            $oldData = [
                'title' => $evaluation->title,
                'form_type' => $evaluation->form_type,
                'form_number' => $evaluation->form_number,
                'revision' => $evaluation->revision,
                'date_effectivity' => $evaluation->date_effectivity,
            ];

            $evaluation->update([
                'title' => $validated['title'],
                'form_type' => $validated['form_type'],
                'form_number' => $validated['form_number'],
                'revision' => $validated['revision'],
                'date_effectivity' => $validated['date_effectivity'],
                'available_from' => $validated['available_from'],
                'available_until' => $validated['available_until'],
            ]);

            $existingCategoryIds = [];
            foreach ($validated['categories'] as $catIndex => $categoryData) {
                if (isset($categoryData['id'])) {
                    $category = EvaluationCategory::find($categoryData['id']);
                    if ($category) {
                        $category->update([
                            'category_name' => $categoryData['name'],
                            'order' => $catIndex + 1,
                        ]);
                        $existingCategoryIds[] = $category->id;
                    }
                } else {
                    $category = EvaluationCategory::create([
                        'evaluation_id' => $evaluation->id,
                        'category_name' => $categoryData['name'],
                        'order' => $catIndex + 1,
                    ]);
                    $existingCategoryIds[] = $category->id;
                }

                if (isset($category)) {
                    $existingQuestionIds = [];
                    foreach ($categoryData['questions'] as $qIndex => $questionData) {
                        if (isset($questionData['id'])) {
                            $question = EvaluationQuestion::find($questionData['id']);
                            if ($question) {
                                $question->update([
                                    'question_text' => $questionData['text'],
                                    'is_required' => $questionData['required'],
                                    'order' => $qIndex + 1,
                                ]);
                                $existingQuestionIds[] = $question->id;
                            }
                        } else {
                            $question = EvaluationQuestion::create([
                                'evaluation_id' => $evaluation->id,
                                'category_id' => $category->id,
                                'question_text' => $questionData['text'],
                                'question_type' => 'likert',
                                'order' => $qIndex + 1,
                                'is_required' => $questionData['required'],
                            ]);
                            $existingQuestionIds[] = $question->id;
                        }
                    }

                    EvaluationQuestion::where('category_id', $category->id)
                        ->whereNotIn('id', $existingQuestionIds)
                        ->delete();
                }
            }

            EvaluationCategory::where('evaluation_id', $evaluation->id)
                ->whereNotIn('id', $existingCategoryIds)
                ->delete();

            $existingCommentIds = [];
            foreach ($validated['comments'] as $cIndex => $commentData) {
                if (isset($commentData['id'])) {
                    $comment = EvaluationQuestion::find($commentData['id']);
                    if ($comment) {
                        $comment->update([
                            'question_text' => $commentData['text'],
                            'is_required' => $commentData['required'],
                            'order' => $cIndex + 1,
                        ]);
                        $existingCommentIds[] = $comment->id;
                    }
                } else {
                    $comment = EvaluationQuestion::create([
                        'evaluation_id' => $evaluation->id,
                        'category_id' => null,
                        'question_text' => $commentData['text'],
                        'question_type' => 'comment',
                        'order' => $cIndex + 1,
                        'is_required' => $commentData['required'],
                    ]);
                    $existingCommentIds[] = $comment->id;
                }
            }

            EvaluationQuestion::where('evaluation_id', $evaluation->id)
                ->where('question_type', 'comment')
                ->whereNotIn('id', $existingCommentIds)
                ->delete();

            DB::commit();

            // Log evaluation update
            $this->logAction('update_evaluation', 'Updated evaluation: ' . $evaluation->title, [
                'evaluation_id' => $evaluation->id,
                'title' => $evaluation->title,
                'changes' => [
                    'old' => $oldData,
                    'new' => [
                        'title' => $evaluation->title,
                        'form_type' => $evaluation->form_type,
                        'form_number' => $evaluation->form_number,
                        'revision' => $evaluation->revision,
                        'date_effectivity' => $evaluation->date_effectivity,
                    ],
                ],
            ]);

            return redirect()->route('admin.evaluations.show', $evaluation->id)
                ->with('success', 'Evaluation updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update evaluation: ' . $e->getMessage());
            
            $this->logError('update_evaluation_failed', 'Failed to update evaluation: ' . $e->getMessage(), $e, [
                'evaluation_id' => $evaluation->id,
                'title' => $evaluation->title,
            ]);
            
            return back()->withErrors(['error' => 'Failed to update evaluation: ' . $e->getMessage()]);
        }
    }

    public function activate(Evaluation $evaluation)
    {
        if ($evaluation->status !== 'draft') {
            return response()->json(['error' => 'Evaluation must be in draft mode to activate'], 400);
        }

        DB::beginTransaction();

        try {
            $qrCodeData = route('evaluations.form', $evaluation->id);
            
            $evaluation->update([
                'status' => 'active',
                'qr_code_url' => $qrCodeData,
                'available_from' => now(),
            ]);

            DB::commit();

            // Log activation
            $this->logAction('activate_evaluation', 'Activated evaluation: ' . $evaluation->title, [
                'evaluation_id' => $evaluation->id,
                'title' => $evaluation->title,
                'event_id' => $evaluation->event_id,
            ]);

            return response()->json([
                'success' => true,
                'evaluation_id' => $evaluation->id,
                'message' => 'Evaluation activated successfully',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to activate evaluation: ' . $e->getMessage());
            
            $this->logError('activate_evaluation_failed', 'Failed to activate evaluation: ' . $e->getMessage(), $e, [
                'evaluation_id' => $evaluation->id,
                'title' => $evaluation->title,
            ]);
            
            return response()->json(['error' => 'Failed to activate evaluation'], 500);
        }
    }

    public function showQRCode(Evaluation $evaluation)
    {
        if ($evaluation->status !== 'active') {
            return redirect()->route('admin.evaluations.show', $evaluation->id)
                ->with('error', 'QR code is only available for active evaluations.');
        }

        $qrData = $evaluation->qr_code_url ?? route('evaluations.form', $evaluation->id);

        // Log QR code view
        $this->logAction('view_qr_code', 'Viewed QR code for evaluation: ' . $evaluation->title, [
            'evaluation_id' => $evaluation->id,
            'title' => $evaluation->title,
        ]);

        return Inertia::render('Admin/Evaluations/QRCode', [
            'evaluation' => [
                'id' => $evaluation->id,
                'title' => $evaluation->title,
                'event_name' => $evaluation->event->event_name,
            ],
            'qr_data' => $qrData,
        ]);
    }

    public function close(Evaluation $evaluation)
    {
        if ($evaluation->status !== 'active') {
            return response()->json(['error' => 'Only active evaluations can be closed'], 400);
        }

        $evaluation->update([
            'status' => 'closed',
            'available_until' => now(),
        ]);

        // Log closure
        $this->logAction('close_evaluation', 'Closed evaluation: ' . $evaluation->title, [
            'evaluation_id' => $evaluation->id,
            'title' => $evaluation->title,
            'total_responses' => $evaluation->total_responses,
        ]);

        return response()->json(['success' => true]);
    }

    public function reopen(Evaluation $evaluation)
    {
        if ($evaluation->status !== 'closed') {
            return response()->json(['error' => 'Only closed evaluations can be reopened'], 400);
        }

        $evaluation->update([
            'status' => 'active',
            'available_until' => null,
        ]);

        // Log reopening
        $this->logAction('reopen_evaluation', 'Reopened evaluation: ' . $evaluation->title, [
            'evaluation_id' => $evaluation->id,
            'title' => $evaluation->title,
        ]);

        return response()->json(['success' => true]);
    }

    public function generateInsights(Evaluation $evaluation, Request $request)
    {
        try {
            $eventDate = $request->get('event_date');
            $generateAll = $request->get('generate_all', false);
            
            $aiService = new AIAnalysisService();
            
            if ($generateAll) {
                $availableDates = $aiService->getAvailableDates($evaluation);
                if (empty($availableDates)) {
                    return response()->json([
                        'error' => 'No responses found to generate insights.'
                    ], 400);
                }
                
                dispatch(new AnalyzeEvaluationJob($evaluation, null, true));
                
                // Log insights generation
                $this->logAction('generate_all_insights', 'Started AI insights generation for all dates', [
                    'evaluation_id' => $evaluation->id,
                    'title' => $evaluation->title,
                    'available_dates' => $availableDates,
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'AI analysis for all dates and overall started. This may take a few minutes.'
                ]);
            }
            
            if ($eventDate) {
                try {
                    $eventDate = Carbon::parse($eventDate)->format('Y-m-d');
                } catch (\Exception $e) {}
            }
            
            if (!$aiService->canGenerateInsights($evaluation, $eventDate)) {
                $responseRate = $aiService->getResponseRateForDate($evaluation, $eventDate);
                return response()->json([
                    'error' => sprintf(
                        'Response rate is %.1f%%. At least 75%% of eligible participants must respond.',
                        $responseRate * 100
                    )
                ], 400);
            }
            
            dispatch(new AnalyzeEvaluationJob($evaluation, $eventDate));
            
            // Log insights generation
            $this->logAction('generate_insights', 'Started AI insights generation for evaluation', [
                'evaluation_id' => $evaluation->id,
                'title' => $evaluation->title,
                'event_date' => $eventDate,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => sprintf(
                    'AI analysis for %s started.',
                    $eventDate ?: 'overall evaluation'
                )
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in generateInsights: ' . $e->getMessage());
            
            $this->logError('generate_insights_failed', 'Failed to generate insights: ' . $e->getMessage(), $e, [
                'evaluation_id' => $evaluation->id,
                'title' => $evaluation->title,
            ]);
            
            return response()->json([
                'error' => 'Failed to start AI analysis: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getAIInsights(Evaluation $evaluation, Request $request)
    {
        try {
            $eventDate = $request->get('event_date');
            
            if ($eventDate) {
                try {
                    $eventDate = Carbon::parse($eventDate)->format('Y-m-d');
                } catch (\Exception $e) {}
            }
            
            $aiService = new AIAnalysisService();
            
            if (!$eventDate) {
                $allInsights = $aiService->getAllInsights($evaluation);
                $availableDates = $aiService->getAvailableDates($evaluation);
                $responseRate = $aiService->getResponseRateForDate($evaluation, null);
                
                return response()->json([
                    'insights' => $allInsights,
                    'available_dates' => $availableDates,
                    'current_date' => null,
                    'response_rate' => $responseRate,
                    'has_insights' => !empty($allInsights)
                ]);
            }
            
            $insights = $aiService->getInsights($evaluation, $eventDate);
            
            if (!$insights) {
                return response()->json([
                    'message' => sprintf('No insights available for %s yet.', $eventDate),
                    'available_dates' => $aiService->getAvailableDates($evaluation)
                ], 404);
            }
            
            $availableDates = $aiService->getAvailableDates($evaluation);
            $responseRate = $aiService->getResponseRateForDate($evaluation, $eventDate);
            
            return response()->json([
                'insights' => $insights,
                'available_dates' => $availableDates,
                'current_date' => $eventDate,
                'response_rate' => $responseRate
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in getAIInsights: ' . $e->getMessage());
            
            $this->logError('get_ai_insights_failed', 'Failed to fetch AI insights: ' . $e->getMessage(), $e, [
                'evaluation_id' => $evaluation->id,
            ]);
            
            return response()->json([
                'error' => 'Failed to fetch AI insights: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getStatsByDate(Evaluation $evaluation, Request $request)
    {
        $eventDate = $request->get('event_date');
        
        $query = EvaluationResponse::where('evaluation_id', $evaluation->id);
        
        if ($eventDate) {
            $query->where('event_date', $eventDate);
        }
        
        $responses = $query->get();
        
        $stats = [];
        $likertQuestions = EvaluationQuestion::where('evaluation_id', $evaluation->id)
            ->where('question_type', 'likert')
            ->get();
        
        foreach ($likertQuestions as $question) {
            $ratings = [];
            foreach ($responses as $response) {
                $responses_array = $response->likert_responses ?? [];
                if (isset($responses_array[$question->id])) {
                    $ratings[] = $responses_array[$question->id];
                }
            }
            
            $total = count($ratings);
            if ($total > 0) {
                $distribution = [];
                for ($i = 1; $i <= 5; $i++) {
                    $count = count(array_filter($ratings, fn($r) => $r == $i));
                    $distribution[$i] = [
                        'count' => $count,
                        'percentage' => round(($count / $total) * 100, 2)
                    ];
                }
                
                $stats[$question->id] = [
                    'average' => round(array_sum($ratings) / $total, 2),
                    'distribution' => $distribution,
                    'total' => $total
                ];
            }
        }
        
        return response()->json($stats);
    }

    
    public function getEligibilityInfo(Evaluation $evaluation)
    {
        $event = $evaluation->event;
        $eventDates = $evaluation->event_dates ?: [];
        
        $departments = Department::whereIn('id', $event->departments ?? [])->pluck('name');
        $courses = Course::whereIn('id', $event->courses ?? [])->pluck('name');
        $yearLevels = $event->year_levels ?? [];
        
        $totalStudents = EventStudent::where('event_id', $event->id)->count();
        $totalGuests = EventGuest::where('event_id', $event->id)->count();
        
        $perDateExpected = [];
        foreach ($eventDates as $date) {
            $dateGuests = EventGuest::where('event_id', $event->id)
                ->where(function($query) use ($date) {
                    if (Schema::hasColumn('event_guests', 'event_date')) {
                        $query->where('event_date', $date);
                    }
                })
                ->count();
            $perDateExpected[$date] = $totalStudents + $dateGuests;
        }
        
        $totalExpectedOverall = ($totalStudents * max(count($eventDates), 1)) + $totalGuests;

        return response()->json([
            'departments' => $departments,
            'courses' => $courses,
            'yearLevels' => $yearLevels,
            'event_dates' => $eventDates,
            'total_students' => $totalStudents,
            'total_guests' => $totalGuests,
            'total_expected_overall' => $totalExpectedOverall,
            'per_date_expected' => $perDateExpected,
            'number_of_dates' => count($eventDates),
        ]);
    }

    /**
 * Download CSV template for bulk upload
 */
public function downloadCsvTemplate(Evaluation $evaluation)
{
    $event = $evaluation->event;
    $eventDates = $evaluation->event_dates ?: [];
    
    // Format dates for Philippines timezone
    $formattedEventDates = array_map(function($date) {
        return Carbon::parse($date)->setTimezone('Asia/Manila')->format('Y-m-d');
    }, $eventDates);
    
    $headers = [
        'student_id', 'email', 'name', 'age', 'sex', 'agency_office',
        'position', 'respondent_type', 'title_prefix', 'event_date'
    ];
    
    $formType = $evaluation->form_type;
    if (in_array($formType, ['type1', 'type3', 'type4'])) {
        $headers[] = 'speaker_topic';
        $headers[] = 'speaker_name';
    }
    
    $categories = $evaluation->categories()
        ->with(['questions' => function($q) {
            $q->where('question_type', 'likert')->orderBy('order');
        }])
        ->orderBy('order')
        ->get();
    
    foreach ($categories as $category) {
        foreach ($category->questions as $question) {
            $headers[] = $question->question_text;
        }
    }
    
    $commentQuestions = EvaluationQuestion::where('evaluation_id', $evaluation->id)
        ->where('question_type', 'comment')
        ->orderBy('order')
        ->get();
    
    foreach ($commentQuestions as $question) {
        $headers[] = $question->question_text;
    }
    
    $sampleRow = [
        'STUDENT-001',
        'student@example.com',
        'Juan Dela Cruz',
        '20',
        'Male',
        'Office Name',
        'Student',
        'Student',
        'Mr.',
        $formattedEventDates[0] ?? date('Y-m-d'),
    ];
    
    if (in_array($formType, ['type1', 'type3', 'type4'])) {
        $sampleRow[] = 'Sample Topic';
        $sampleRow[] = 'Sample Speaker Name';
    }
    
    foreach ($categories as $category) {
        foreach ($category->questions as $question) {
            $sampleRow[] = '4';
        }
    }
    
    foreach ($commentQuestions as $question) {
        $sampleRow[] = 'Sample comment here';
    }
    
    $callback = function() use ($headers, $sampleRow, $formattedEventDates) {
        $file = fopen('php://output', 'w');
        fwrite($file, "\xEF\xBB\xBF");
        fputcsv($file, $headers);
        fputcsv($file, $sampleRow);
        fwrite($file, "# Note: event_date must be one of these dates: " . implode(', ', $formattedEventDates) . "\n");
        fclose($file);
    };
    
    $filename = 'evaluation_' . $evaluation->id . '_template.csv';
    
    $this->logAction('download_csv_template', 'Downloaded CSV template for evaluation: ' . $evaluation->title, [
        'evaluation_id' => $evaluation->id,
        'title' => $evaluation->title,
    ]);
    
    return response()->stream($callback, 200, [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
    ]);
}
    /**
     * Get raw responses - ORDERED BY CATEGORY
     */
    public function getRawResponses(Evaluation $evaluation, Request $request)
    {
        $eventDate = $request->get('event_date');
        
        $query = EvaluationResponse::where('evaluation_id', $evaluation->id);
        
        if ($eventDate) {
            $query->where('event_date', $eventDate);
        }
        
        $responses = $query->orderBy('created_at', 'desc')->get();
        
        // Get categories with their questions in order
        $categories = $evaluation->categories()
            ->with(['questions' => function($q) {
                $q->where('question_type', 'likert')->orderBy('order');
            }])
            ->orderBy('order')
            ->get();
        
        // Build likert questions list in category order
        $likertQuestions = [];
        foreach ($categories as $category) {
            foreach ($category->questions as $question) {
                $likertQuestions[] = $question;
            }
        }
        
        $commentQuestions = EvaluationQuestion::where('evaluation_id', $evaluation->id)
            ->where('question_type', 'comment')
            ->orderBy('order')
            ->get();
        
        $formattedResponses = $responses->map(function ($response) use ($likertQuestions, $commentQuestions) {
            $row = [
                'Student ID' => $response->student_id,
                'Student Name' => $response->name,
                'Email' => $response->email,
                'Event Date' => $response->event_date ? Carbon::parse($response->event_date)->format('F d, Y') : 'N/A',
                'Agency/Office' => $response->agency_office,
                'Position' => $response->position,
                'Respondent Type' => $response->respondent_type,
                'Sex' => $response->sex,
                'Age' => $response->age,
                'Title' => $response->title_prefix,
                'Submitted At' => $response->created_at->format('Y-m-d H:i:s'),
            ];
            
            $likertResponses = $response->likert_responses;
            if (is_string($likertResponses)) {
                $likertResponses = json_decode($likertResponses, true);
            }
            
            if (is_array($likertResponses)) {
                foreach ($likertQuestions as $question) {
                    $questionText = $question->question_text;
                    $rating = $likertResponses[$question->id] ?? null;
                    $row[$questionText] = $rating !== null ? (int)$rating : '—';
                }
            }
            
            $commentResponses = $response->comment_responses;
            if (is_string($commentResponses)) {
                $commentResponses = json_decode($commentResponses, true);
            }
            
            if (is_array($commentResponses)) {
                foreach ($commentQuestions as $question) {
                    $questionText = $question->question_text;
                    $comment = $commentResponses[$question->id] ?? null;
                    $row[$questionText] = $comment ?: '—';
                }
            }
            
            if ($response->speaker_topic) {
                $row['Speaker Topic'] = $response->speaker_topic;
            }
            if ($response->speaker_name) {
                $row['Speaker Name'] = $response->speaker_name;
            }
            
            return $row;
        });
        
        $this->logAction('export_raw_responses', 'Exported raw responses for evaluation: ' . $evaluation->title, [
            'evaluation_id' => $evaluation->id,
            'title' => $evaluation->title,
            'event_date' => $eventDate,
            'total_responses' => $responses->count(),
        ]);
        
        return response()->json($formattedResponses);
    }

    public function bulkUpload(Request $request, Evaluation $evaluation)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240',
        ]);
        
        $file = $request->file('csv_file');
        $handle = fopen($file->getPathname(), 'r');
        
        $firstLine = fgets($handle);
        rewind($handle);
        
        $delimiter = ',';
        if (strpos($firstLine, "\t") !== false) {
            $delimiter = "\t";
        } elseif (strpos($firstLine, ';') !== false) {
            $delimiter = ';';
        }
        
        $headers = fgetcsv($handle, 0, $delimiter);
        
        if (!$headers) {
            return response()->json(['error' => 'Invalid CSV file format - no headers found'], 400);
        }
        
        $headers = array_map(function($header) {
            return trim($header, "\xEF\xBB\xBF \t\n\r\0\x0B\"'");
        }, $headers);
        
        $likertQuestions = EvaluationQuestion::where('evaluation_id', $evaluation->id)
            ->where('question_type', 'likert')
            ->get();
        $commentQuestions = EvaluationQuestion::where('evaluation_id', $evaluation->id)
            ->where('question_type', 'comment')
            ->get();
        $event = $evaluation->event;
        $eventDates = $evaluation->event_dates ?: [];
        
        $successCount = 0;
        $errorCount = 0;
        $errors = [];
        
        DB::beginTransaction();
        
        try {
            $rowNumber = 0;
            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                $rowNumber++;
                
                if (count($row) == 1 && empty($row[0])) {
                    continue;
                }
                
                if (!empty($row[0]) && str_starts_with($row[0], '#')) {
                    continue;
                }
                
                if (count($row) < count($headers)) {
                    $row = array_pad($row, count($headers), '');
                } elseif (count($row) > count($headers)) {
                    $row = array_slice($row, 0, count($headers));
                }
                
                $data = [];
                foreach ($headers as $index => $header) {
                    $data[$header] = isset($row[$index]) ? trim($row[$index]) : '';
                }
                
                if (empty($data['student_id'])) {
                    $errors[] = "Row $rowNumber: student_id is empty";
                    $errorCount++;
                    continue;
                }
                
                if (empty($data['event_date'])) {
                    $errors[] = "Row $rowNumber: event_date is required";
                    $errorCount++;
                    continue;
                }
                
                if (!in_array($data['event_date'], $eventDates)) {
                    $errors[] = "Row $rowNumber: event_date '{$data['event_date']}' is not valid.";
                    $errorCount++;
                    continue;
                }
                
                $isGuest = str_starts_with($data['student_id'], 'GUEST-');
                
                if (!$isGuest) {
                    $eventStudent = EventStudent::where('event_id', $event->id)
                        ->where('student_id', $data['student_id'])
                        ->first();
                        
                    if (!$eventStudent) {
                        $errors[] = "Row $rowNumber: Student {$data['student_id']} is not eligible";
                        $errorCount++;
                        continue;
                    }
                    
                    $student = Student::where('student_id', $data['student_id'])->first();
                    if (!$student) {
                        $errors[] = "Row $rowNumber: Student {$data['student_id']} not found";
                        $errorCount++;
                        continue;
                    }
                } else {
                    $guest = EventGuest::where('event_id', $event->id)
                        ->where('guest_id', $data['student_id'])
                        ->first();
                        
                    if (!$guest) {
                        $errors[] = "Row $rowNumber: Guest {$data['student_id']} not found";
                        $errorCount++;
                        continue;
                    }
                }
                
                $existing = EvaluationResponse::where('evaluation_id', $evaluation->id)
                    ->where('student_id', $data['student_id'])
                    ->where('event_date', $data['event_date'])
                    ->exists();
                    
                if ($existing) {
                    $errors[] = "Row $rowNumber: Already submitted for {$data['event_date']}";
                    $errorCount++;
                    continue;
                }
                
                $likertResponses = [];
                foreach ($likertQuestions as $question) {
                    $questionText = $question->question_text;
                    $value = $data[$questionText] ?? '';
                    if (is_numeric($value)) {
                        $likertResponses[$question->id] = (int)$value;
                    }
                }
                
                $commentResponses = [];
                foreach ($commentQuestions as $question) {
                    $questionText = $question->question_text;
                    $value = $data[$questionText] ?? '';
                    if (!empty($value) && !is_numeric($value)) {
                        $commentResponses[$question->id] = $value;
                    }
                }
                
                $dateIndex = array_search($data['event_date'], $eventDates) + 1;
                
                if (!$isGuest) {
                    $student = Student::where('student_id', $data['student_id'])->first();
                    $agencyOffice = !empty($data['agency_office']) ? $data['agency_office'] : ($student->department ?? '');
                } else {
                    $guest = EventGuest::where('event_id', $event->id)
                        ->where('guest_id', $data['student_id'])
                        ->first();
                    $agencyOffice = !empty($data['agency_office']) ? $data['agency_office'] : ($guest->agency_office ?? '');
                }
                
                EvaluationResponse::create([
                    'evaluation_id' => $evaluation->id,
                    'event_id' => $event->id,
                    'event_date' => $data['event_date'],
                    'date_index' => $dateIndex,
                    'student_id' => $data['student_id'],
                    'email' => $data['email'] ?? '',
                    'name' => $data['name'] ?? '',
                    'age' => $data['age'] ?? null,
                    'sex' => $data['sex'] ?? null,
                    'agency_office' => $agencyOffice,
                    'position' => $data['position'] ?? null,
                    'respondent_type' => $data['respondent_type'] ?? ($isGuest ? 'Guest' : 'Student'),
                    'title_prefix' => $data['title_prefix'] ?? null,
                    'speaker_topic' => $data['speaker_topic'] ?? null,
                    'speaker_name' => $data['speaker_name'] ?? null,
                    'likert_responses' => $likertResponses,
                    'comment_responses' => $commentResponses,
                ]);
                
                $successCount++;
            }
            
            $evaluation->increment('total_responses', $successCount);
            
            DB::commit();
            
            // Log bulk upload success
            $this->logAction('bulk_upload_responses', 'Bulk uploaded responses for evaluation: ' . $evaluation->title, [
                'evaluation_id' => $evaluation->id,
                'title' => $evaluation->title,
                'success_count' => $successCount,
                'error_count' => $errorCount,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
            ]);
            
            return response()->json([
                'success' => true,
                'message' => "Imported {$successCount} responses, {$errorCount} failed",
                'stats' => [
                    'success' => $successCount,
                    'errors' => $errorCount,
                    'error_details' => $errors,
                ]
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk upload failed: ' . $e->getMessage());
            
            $this->logError('bulk_upload_failed', 'Bulk upload failed: ' . $e->getMessage(), $e, [
                'evaluation_id' => $evaluation->id,
                'title' => $evaluation->title,
                'file_name' => $file->getClientOriginalName(),
            ]);
            
            return response()->json([
                'error' => 'Upload failed: ' . $e->getMessage(),
                'details' => $errors
            ], 500);
        } finally {
            fclose($handle);
        }
    }

    public function destroy(Evaluation $evaluation)
    {
        if ($evaluation->status !== 'draft') {
            $this->logSecurity('unauthorized_evaluation_deletion', 'Attempted to delete non-draft evaluation', [
                'evaluation_id' => $evaluation->id,
                'title' => $evaluation->title,
                'current_status' => $evaluation->status,
            ]);
            return response()->json(['error' => 'Only draft evaluations can be deleted'], 400);
        }
        
        try {
            $evaluationTitle = $evaluation->title;
            $evaluationId = $evaluation->id;
            
            $evaluation->delete();
            
            // Log deletion
            $this->logAction('delete_evaluation', 'Deleted evaluation: ' . $evaluationTitle, [
                'evaluation_id' => $evaluationId,
                'title' => $evaluationTitle,
                'event_id' => $evaluation->event_id,
            ]);
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Failed to delete evaluation: ' . $e->getMessage());
            
            $this->logError('delete_evaluation_failed', 'Failed to delete evaluation: ' . $e->getMessage(), $e, [
                'evaluation_id' => $evaluation->id,
                'title' => $evaluation->title,
            ]);
            
            return response()->json(['error' => 'Failed to delete evaluation'], 500);
        }
    }

    /**
     * Helper method to log CRUD and critical actions only
     */
    private function logAction($action, $description, $details = [])
    {
        try {
            $user = Auth::user();
            if ($user) {
                LogService::action($action, $description, $user, $details);
            } else {
                LogService::system($action, $description, $details);
            }
        } catch (\Exception $e) {
            // Silent fail - don't let logging break the application
            Log::warning('Failed to log action: ' . $e->getMessage());
        }
    }
    
    /**
     * Helper method to log errors
     */
    private function logError($action, $description, $exception = null, $details = [])
    {
        try {
            LogService::error($action, $description, $exception, $details);
        } catch (\Exception $e) {
            // Silent fail
            Log::warning('Failed to log error: ' . $e->getMessage());
        }
    }
    
    /**
     * Helper method to log security events
     */
    private function logSecurity($action, $description, $details = [])
    {
        try {
            $user = Auth::user();
            LogService::security($action, $description, $user, $details);
        } catch (\Exception $e) {
            // Silent fail
            Log::warning('Failed to log security event: ' . $e->getMessage());
        }
    }
}