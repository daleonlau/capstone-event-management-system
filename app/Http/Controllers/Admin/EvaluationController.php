<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use App\Models\EvaluationRequest;
use App\Models\Event;
use App\Models\EvaluationCategory;
use App\Models\EvaluationQuestion;
use App\Models\EvaluationResponse;
use App\Models\AIAnalysis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use App\Services\AIAnalysisService;

class EvaluationController extends Controller
{
    public function index()
    {
        $evaluations = Evaluation::with(['event', 'event.creator'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($evaluation) {
                return [
                    'id' => $evaluation->id,
                    'title' => $evaluation->title,
                    'form_type' => $evaluation->form_type,
                    'event_name' => $evaluation->event->event_name,
                    'organization_name' => $evaluation->event->creator->name,
                    'status' => $evaluation->status,
                    'responses_count' => $evaluation->total_responses,
                    'created_at' => $evaluation->created_at->format('Y-m-d'),
                ];
            });

        $pendingRequests = EvaluationRequest::with(['event', 'organization', 'requestedBy'])
            ->where('status', 'pending')
            ->count();

        return Inertia::render('Admin/Evaluations/Index', [
            'evaluations' => $evaluations,
            'pendingRequestsCount' => $pendingRequests
        ]);
    }

    public function getPendingRequests()
    {
        try {
            $requests = EvaluationRequest::with(['event', 'organization', 'requestedBy'])
                ->where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($request) {
                    return [
                        'id' => $request->id,
                        'event_id' => $request->event_id,
                        'event_name' => $request->event->event_name,
                        'event_status' => $request->event->status,
                        'organization_name' => $request->organization->name,
                        'title' => $request->title,
                        'activity_date' => $request->activity_date instanceof \Carbon\Carbon 
                            ? $request->activity_date->format('M d, Y') 
                            : date('M d, Y', strtotime($request->activity_date)),
                        'venue' => $request->venue,
                        'speaker_name' => $request->speaker_name,
                        'topics' => $request->topics,
                        'has_food' => $request->has_food,
                        'notes' => $request->notes,
                        'created_at' => $request->created_at instanceof \Carbon\Carbon 
                            ? $request->created_at->format('M d, Y h:i A') 
                            : date('M d, Y h:i A', strtotime($request->created_at)),
                    ];
                });

            return response()->json($requests);
        } catch (\Exception $e) {
            Log::error('Failed to fetch pending requests', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([], 200);
        }
    }

    public function create(Request $request)
    {
        $pendingRequests = EvaluationRequest::with(['event', 'organization', 'requestedBy'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($request) {
                return [
                    'id' => $request->id,
                    'event_id' => $request->event_id,
                    'event_name' => $request->event->event_name,
                    'event_status' => $request->event->status,
                    'organization_name' => $request->organization->name,
                    'title' => $request->title,
                    'activity_date' => $request->activity_date instanceof \Carbon\Carbon 
                        ? $request->activity_date->format('M d, Y') 
                        : date('M d, Y', strtotime($request->activity_date)),
                    'venue' => $request->venue,
                    'speaker_name' => $request->speaker_name,
                    'topics' => $request->topics,
                    'has_food' => $request->has_food,
                    'notes' => $request->notes,
                    'created_at' => $request->created_at->format('Y-m-d H:i'),
                ];
            });

        $formTypes = [
            'type1' => '7 Quality Dimension (F-EEF-018a)',
            'type2' => '5 Quality Dimension (F-EEF-018d)',
            'type3' => '8 Quality Dimension (F-EEF-018e)',
            'type4' => '6 Quality Dimension without Meals (F-EEF-018b)',
            'type5' => '6 Quality Dimension without Speaker (F-EEF-018c)',
        ];

        $selectedRequestId = $request->query('request_id');

        return Inertia::render('Admin/Evaluations/Create', [
            'pendingRequests' => $pendingRequests,
            'formTypes' => $formTypes,
            'selectedRequestId' => $selectedRequestId
        ]);
    }

    public function store(Request $request)
    {
        Log::info('=== EVALUATION STORE METHOD CALLED ===');
        Log::info('Request data:', $request->all());

        $validated = $request->validate([
            'evaluation_request_id' => 'required|exists:evaluation_requests,id',
            'form_type' => 'required|in:type1,type2,type3,type4,type5',
            'title' => 'required|string|max:255',
            'form_number' => 'required|string',
            'revision' => 'required|string',
            'date_effectivity' => 'required|string',
            'available_from' => 'nullable|date',
            'available_until' => 'nullable|date|after:available_from',
        ]);

        Log::info('Validation passed', $validated);

        $evaluationRequest = EvaluationRequest::findOrFail($request->evaluation_request_id);
        Log::info('Evaluation request found', ['id' => $evaluationRequest->id]);

        if (!$evaluationRequest->canCreateEvaluation()) {
            Log::warning('Evaluation request cannot be processed', [
                'id' => $evaluationRequest->id,
                'status' => $evaluationRequest->status
            ]);
            return back()->with('error', 'This request cannot be processed.');
        }

        DB::beginTransaction();

        try {
            $evaluation = Evaluation::create([
                'event_id' => $evaluationRequest->event_id,
                'organization_id' => $evaluationRequest->organization_id,
                'title' => $request->title,
                'form_type' => $request->form_type,
                'form_number' => $request->form_number,
                'revision' => $request->revision,
                'date_effectivity' => $request->date_effectivity,
                'available_from' => $request->available_from,
                'available_until' => $request->available_until,
                'status' => 'draft',
                'form_customizations' => [
                    'original_title' => $evaluationRequest->title,
                    'activity_date' => $evaluationRequest->activity_date,
                    'venue' => $evaluationRequest->venue,
                    'speaker_name' => $evaluationRequest->speaker_name,
                    'topics' => $evaluationRequest->topics,
                    'has_food' => $evaluationRequest->has_food,
                ]
            ]);

            Log::info('Evaluation created', ['id' => $evaluation->id]);

            $formTemplate = $this->getFormTemplate($request->form_type);
            Log::info('Form template retrieved', ['type' => $request->form_type]);

            foreach ($formTemplate['categories'] as $catIndex => $catData) {
                $category = EvaluationCategory::create([
                    'evaluation_id' => $evaluation->id,
                    'category_name' => $catData['name'],
                    'order' => $catIndex,
                ]);
                Log::info('Category created', ['id' => $category->id, 'name' => $catData['name']]);

                foreach ($catData['questions'] as $qIndex => $qData) {
                    EvaluationQuestion::create([
                        'evaluation_id' => $evaluation->id,
                        'category_id' => $category->id,
                        'question_text' => $qData['text'],
                        'question_type' => 'likert',
                        'order' => $qIndex,
                        'is_required' => true,
                    ]);
                }
                Log::info('Questions created for category', ['category_id' => $category->id, 'count' => count($catData['questions'])]);
            }

            foreach ($formTemplate['comments'] as $cIndex => $cData) {
                EvaluationQuestion::create([
                    'evaluation_id' => $evaluation->id,
                    'category_id' => null,
                    'question_text' => $cData['text'],
                    'question_type' => 'comment',
                    'order' => $cIndex,
                    'is_required' => $cData['required'] ?? false,
                ]);
            }
            Log::info('Comments created', ['count' => count($formTemplate['comments'])]);

            $evaluationRequest->update([
                'status' => 'processing',
                'evaluation_id' => $evaluation->id,
                'form_type' => $request->form_type,
            ]);
            Log::info('Evaluation request updated', ['id' => $evaluationRequest->id, 'status' => 'processing']);

            DB::commit();

            Log::info('=== EVALUATION CREATED SUCCESSFULLY ===', ['evaluation_id' => $evaluation->id]);

            return redirect()->route('admin.evaluations.show', $evaluation->id)
                ->with('success', 'Evaluation form created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('=== EVALUATION CREATION FAILED ===');
            Log::error('Error message: ' . $e->getMessage());
            Log::error('Error trace: ' . $e->getTraceAsString());
            
            return back()->with('error', 'Failed to create evaluation: ' . $e->getMessage());
        }
    }

    public function show(Evaluation $evaluation)
    {
        $evaluation->load(['event', 'categories.questions', 'questions' => function ($q) {
            $q->where('question_type', 'comment');
        }]);

        $evaluationRequest = EvaluationRequest::where('evaluation_id', $evaluation->id)->first();

        $responses = EvaluationResponse::where('evaluation_id', $evaluation->id)->get();
        
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

        return Inertia::render('Admin/Evaluations/Show', [
            'evaluation' => [
                'id' => $evaluation->id,
                'title' => $evaluation->title,
                'form_type' => $evaluation->form_type,
                'form_number' => $evaluation->form_number,
                'revision' => $evaluation->revision,
                'date_effectivity' => $evaluation->date_effectivity,
                'status' => $evaluation->status,
                'qr_code_url' => $evaluation->qr_code_url,
                'qr_code_path' => $evaluation->qr_code_path,
                'event' => [
                    'id' => $evaluation->event->id,
                    'event_name' => $evaluation->event->event_name,
                    'status' => $evaluation->event->status,
                ],
                'categories' => $evaluation->categories->map(function ($cat) {
                    return [
                        'id' => $cat->id,
                        'name' => $cat->category_name,
                        'questions' => $cat->questions->map(function ($q) {
                            return [
                                'id' => $q->id,
                                'text' => $q->question_text,
                            ];
                        }),
                    ];
                }),
                'comments' => $evaluation->questions->where('question_type', 'comment')->map(function ($q) {
                    return [
                        'id' => $q->id,
                        'text' => $q->question_text,
                    ];
                })->values(),
                'responses_count' => $evaluation->total_responses,
                'created_at' => $evaluation->created_at->format('Y-m-d H:i'),
                'customizations' => $evaluation->form_customizations,
            ],
            'evaluationRequest' => $evaluationRequest,
            'stats' => $stats,
            'comments' => $comments,
            'aiInsights' => $aiInsights ? [
                'summary' => $aiInsights->summary,
                'strengths' => json_decode($aiInsights->strengths, true),
                'weaknesses' => json_decode($aiInsights->weaknesses, true),
                'recommendations' => json_decode($aiInsights->recommendations, true),
                'predicted_satisfaction' => $aiInsights->predicted_satisfaction,
                'success_probability' => $aiInsights->success_probability,
                'category_breakdown' => json_decode($aiInsights->category_breakdown, true),
                'analyzed_at' => $aiInsights->analyzed_at,
            ] : null,
            'canGenerateQR' => $evaluation->status === 'draft',
        ]);
    }

    public function edit(Evaluation $evaluation)
    {
        if ($evaluation->status !== 'draft') {
            return redirect()->route('admin.evaluations.show', $evaluation->id)
                ->with('error', 'Cannot edit evaluation that is already active or closed.');
        }

        $evaluation->load(['categories.questions', 'questions' => function ($q) {
            $q->where('question_type', 'comment');
        }]);

        $formTypes = [
            'type1' => '7 Quality Dimension (F-EEF-018a)',
            'type2' => '5 Quality Dimension (F-EEF-018d)',
            'type3' => '8 Quality Dimension (F-EEF-018e)',
            'type4' => '6 Quality Dimension without Meals (F-EEF-018b)',
            'type5' => '6 Quality Dimension without Speaker (F-EEF-018c)',
        ];

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
            'event' => $evaluation->event,
        ]);
    }

    public function update(Request $request, Evaluation $evaluation)
    {
        if ($evaluation->status !== 'draft') {
            return response()->json(['error' => 'Cannot update evaluation that is already active.'], 400);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'form_type' => 'required|in:type1,type2,type3,type4,type5',
            'form_number' => 'required|string',
            'revision' => 'required|string',
            'date_effectivity' => 'required|string',
            'available_from' => 'nullable|date',
            'available_until' => 'nullable|date|after:available_from',
            'categories' => 'required|array|min:1',
            'comments' => 'array',
        ]);

        DB::beginTransaction();

        try {
            $evaluation->update([
                'title' => $request->title,
                'form_type' => $request->form_type,
                'form_number' => $request->form_number,
                'revision' => $request->revision,
                'date_effectivity' => $request->date_effectivity,
                'available_from' => $request->available_from,
                'available_until' => $request->available_until,
            ]);

            $evaluation->categories()->delete();
            $evaluation->questions()->delete();

            foreach ($request->categories as $catIndex => $catData) {
                $category = EvaluationCategory::create([
                    'evaluation_id' => $evaluation->id,
                    'category_name' => $catData['name'],
                    'order' => $catIndex,
                ]);

                foreach ($catData['questions'] as $qIndex => $qData) {
                    EvaluationQuestion::create([
                        'evaluation_id' => $evaluation->id,
                        'category_id' => $category->id,
                        'question_text' => $qData['text'],
                        'question_type' => 'likert',
                        'order' => $qIndex,
                        'is_required' => $qData['required'] ?? true,
                    ]);
                }
            }

            if ($request->has('comments')) {
                foreach ($request->comments as $cIndex => $cData) {
                    EvaluationQuestion::create([
                        'evaluation_id' => $evaluation->id,
                        'category_id' => null,
                        'question_text' => $cData['text'],
                        'question_type' => 'comment',
                        'order' => $cIndex,
                        'is_required' => $cData['required'] ?? false,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.evaluations.show', $evaluation->id)
                ->with('success', 'Evaluation updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update evaluation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Failed to update evaluation.');
        }
    }

    public function activate(Evaluation $evaluation)
    {
        if ($evaluation->status !== 'draft') {
            return response()->json(['error' => 'Only draft evaluations can be activated.'], 400);
        }

        try {
            $evaluationUrl = route('evaluations.form', $evaluation->id);
            
            $evaluation->update([
                'status' => 'active',
                'qr_code_url' => $evaluationUrl
            ]);

            EvaluationRequest::where('evaluation_id', $evaluation->id)
                ->update(['status' => 'completed']);

            Log::info('Evaluation activated', [
                'evaluation_id' => $evaluation->id,
                'qr_code_url' => $evaluationUrl
            ]);

            return response()->json([
                'success' => true, 
                'message' => 'Evaluation activated successfully',
                'evaluation_url' => $evaluationUrl,
                'evaluation_id' => $evaluation->id
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to activate evaluation', [
                'evaluation_id' => $evaluation->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'error' => 'Failed to activate evaluation: ' . $e->getMessage()
            ], 500);
        }
    }

    public function showQRCode(Evaluation $evaluation)
    {
        if ($evaluation->status !== 'active') {
            return redirect()->route('admin.evaluations.show', $evaluation->id)
                ->with('error', 'Evaluation must be active to view QR code.');
        }

        if (!$evaluation->qr_code_url) {
            $evaluation->update([
                'qr_code_url' => route('evaluations.form', $evaluation->id)
            ]);
            $evaluation->refresh();
        }

        return Inertia::render('Admin/Evaluations/QRCode', [
            'evaluation' => [
                'id' => $evaluation->id,
                'title' => $evaluation->title,
                'event_name' => $evaluation->event->event_name,
                'qr_code_url' => $evaluation->qr_code_url,
            ],
            'qr_data' => $evaluation->qr_code_url
        ]);
    }

    public function close(Evaluation $evaluation)
    {
        if ($evaluation->status !== 'active') {
            return back()->with('error', 'Only active evaluations can be closed.');
        }

        DB::beginTransaction();
        
        $evaluation->update(['status' => 'closed']);

        DB::commit();

        if ($evaluation->total_responses > 0) {
            try {
                $aiService = resolve(AIAnalysisService::class);
                $aiService->analyzeEvaluation($evaluation);
            } catch (\Exception $e) {
                Log::error('AI analysis failed', [
                    'evaluation_id' => $evaluation->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return redirect()->route('admin.evaluations.show', $evaluation->id)
            ->with('success', 'Evaluation closed successfully.');
    }

    public function reopen(Evaluation $evaluation)
    {
        if ($evaluation->status !== 'closed') {
            return back()->with('error', 'Only closed evaluations can be reopened.');
        }

        $evaluation->update(['status' => 'active']);

        return redirect()->route('admin.evaluations.show', $evaluation->id)
            ->with('success', 'Evaluation reopened.');
    }

    public function generateInsights(Evaluation $evaluation)
    {
        if ($evaluation->status !== 'closed') {
            return response()->json(['error' => 'Evaluation must be closed to generate insights.'], 400);
        }

        try {
            $aiService = resolve(AIAnalysisService::class);
            $insights = $aiService->analyzeEvaluation($evaluation, true);
            
            if ($insights) {
                return response()->json(['success' => true, 'message' => 'AI insights generated successfully!']);
            } else {
                return response()->json(['error' => 'Failed to generate insights.'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getAIInsights(Evaluation $evaluation)
    {
        $analysis = AIAnalysis::where('evaluation_id', $evaluation->id)->first();
        
        if (!$analysis) {
            return response()->json(null, 404);
        }
        
        return response()->json([
            'summary' => $analysis->summary,
            'strengths' => json_decode($analysis->strengths, true),
            'weaknesses' => json_decode($analysis->weaknesses, true),
            'recommendations' => json_decode($analysis->recommendations, true),
            'predicted_satisfaction' => $analysis->predicted_satisfaction,
            'success_probability' => $analysis->success_probability,
            'category_breakdown' => json_decode($analysis->category_breakdown, true),
            'analyzed_at' => $analysis->analyzed_at,
        ]);
    }

    public function bulkUpload(Request $request, Evaluation $evaluation)
    {
        if ($evaluation->status !== 'active') {
            return response()->json(['error' => 'Evaluation must be active to upload responses.'], 400);
        }

        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        $file = $request->file('csv_file');
        
        $import = new \App\Imports\EvaluationResponsesImport($evaluation);
        
        try {
            \Maatwebsite\Excel\Facades\Excel::import($import, $file);
            
            $stats = $import->getStats();
            
            $evaluation->update([
                'total_responses' => EvaluationResponse::where('evaluation_id', $evaluation->id)->count()
            ]);

            $message = "✅ Successfully imported {$stats['success']} responses.";
            if ($stats['errors'] > 0) {
                $message .= " Failed: {$stats['errors']}. Check logs for details.";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'stats' => $stats
            ]);

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = "Row {$failure->row()}: " . implode(', ', $failure->errors());
            }
            
            return response()->json([
                'error' => 'CSV validation failed',
                'details' => $errors
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Bulk upload failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Failed to process CSV: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadCsvTemplate(Evaluation $evaluation)
    {
        // Get all questions for this evaluation in order
        $likertQuestions = EvaluationQuestion::where('evaluation_id', $evaluation->id)
            ->where('question_type', 'likert')
            ->orderBy('order')
            ->get();
        
        $commentQuestions = EvaluationQuestion::where('evaluation_id', $evaluation->id)
            ->where('question_type', 'comment')
            ->orderBy('order')
            ->get();
    
        // Get event details for sample data
        $event = $evaluation->event;
        
        // Get eligible departments names
        $departments = [];
        if (!empty($event->departments)) {
            $departments = \App\Models\Department::whereIn('id', $event->departments)
                ->pluck('name')
                ->toArray();
        }
        
        // Get eligible courses names
        $courses = [];
        if (!empty($event->courses)) {
            $courses = \App\Models\Course::whereIn('id', $event->courses)
                ->pluck('name')
                ->toArray();
        }
        
        $eligibleYearLevels = $event->year_levels ?? ['1st Year', '2nd Year', '3rd Year', '4th Year'];
    
        // Build headers in order
        $headers = [
            'student_id',
            'email',
            'name',
            'age',
            'sex',
            'agency_office',
            'position',
            'respondent_type',
            'title_prefix',
            'department',
            'course',
            'year_level'
        ];
    
        // Add speaker fields for forms that have speaker
        $formType = $evaluation->form_type;
        if (in_array($formType, ['type1', 'type3', 'type4'])) {
            $headers[] = 'speaker_topic';
            $headers[] = 'speaker_name';
        }
    
        // Add likert questions with full text
        foreach ($likertQuestions as $question) {
            $headers[] = $question->question_text;
        }
    
        // Add comment questions with full text
        foreach ($commentQuestions as $question) {
            $headers[] = $question->question_text;
        }
    
        // Create sample row with realistic data
        $sampleRow = [
            'CEIT-2024-0001',
            'student@example.com',
            'Juan Dela Cruz',
            '20',
            'Male',
            'College of Engineering',
            'Student',
            'Student',
            'Mr.',
            $departments[0] ?? 'College of Engineering',
            $courses[0] ?? 'BSIT',
            $eligibleYearLevels[0] ?? '1st Year'
        ];
    
        // Add speaker fields sample
        if (in_array($formType, ['type1', 'type3', 'type4'])) {
            $sampleRow[] = 'Sample Topic';
            $sampleRow[] = $evaluation->form_customizations['speaker_name'] ?? 'Dr. John Smith';
        }
    
        // Add sample ratings (all 4s)
        foreach ($likertQuestions as $question) {
            $sampleRow[] = 4;
        }
    
        // Add sample comments (empty)
        foreach ($commentQuestions as $question) {
            $sampleRow[] = '';
        }
    
        $callback = function() use ($headers, $sampleRow) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for Excel compatibility
            fwrite($file, "\xEF\xBB\xBF");
            
            // Add headers
            fputcsv($file, $headers);
            
            // Add one sample data row
            fputcsv($file, $sampleRow);
            
            fclose($file);
        };
    
        $filename = 'evaluation_' . $evaluation->id . '_template.csv';
        
        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

private function getFormTypeName($formType)
{
    $types = [
        'type1' => '7 Quality Dimension (F-EEF-018a)',
        'type2' => '5 Quality Dimension (F-EEF-018d)',
        'type3' => '8 Quality Dimension (F-EEF-018e)',
        'type4' => '6 Quality Dimension without Meals (F-EEF-018b)',
        'type5' => '6 Quality Dimension without Speaker (F-EEF-018c)'
    ];
    return $types[$formType] ?? $formType;
}

    public function destroy(Evaluation $evaluation)
    {
        if ($evaluation->status !== 'draft') {
            return response()->json(['error' => 'Only draft evaluations can be deleted.'], 400);
        }

        $evaluation->delete();

        return response()->json(['success' => true]);
    }

    private function getFormTemplate($formType)
    {
        $templates = [
            // Type 1: 7 Quality Dimension (F-EEF-018a)
            'type1' => [
                'name' => '7 Quality Dimension',
                'form_number' => 'F-EEF-018a',
                'revision' => 'Rev. 0',
                'date_effectivity' => '04-28-2025',
                'categories' => [
                    [
                        'name' => 'I. Information Dissemination',
                        'questions' => [
                            ['text' => 'Timeliness of sending invites'],
                            ['text' => 'Adequacy of information dissemination'],
                        ]
                    ],
                    [
                        'name' => 'II. Design of the Event',
                        'questions' => [
                            ['text' => 'Program / Order of activities'],
                            ['text' => 'Relevance of the activities'],
                            ['text' => 'Time allotment / pacing'],
                        ]
                    ],
                    [
                        'name' => 'III. Outcomes of the Event',
                        'questions' => [
                            ['text' => 'Attendance of participants'],
                            ['text' => 'Participation to activities'],
                            ['text' => 'Interaction'],
                            ['text' => 'Teamwork'],
                        ]
                    ],
                    [
                        'name' => 'IV. Secretariat',
                        'questions' => [
                            ['text' => 'Sensitivity in providing assistance/needs to the participants'],
                            ['text' => 'Management on the entire activities'],
                            ['text' => 'Provision of information/feedback to the participants in a clear, concise manner'],
                        ]
                    ],
                    [
                        'name' => 'V. Facilities',
                        'questions' => [
                            ['text' => 'Overall appearance of the venue'],
                            ['text' => 'Cleanliness and orderliness'],
                            ['text' => 'Availability and functionality of applicable equipment'],
                        ]
                    ],
                    [
                        'name' => 'VI. Food',
                        'questions' => [
                            ['text' => 'Quality of food and beverages'],
                            ['text' => 'Food and beverages presentation/setup'],
                            ['text' => 'Timelines of delivery of food'],
                            ['text' => 'Quality of service provided'],
                            ['text' => 'Sufficiency of foods'],
                            ['text' => 'Quantity/Serving of food provided'],
                        ]
                    ],
                    [
                        'name' => 'VII. Resource Speaker',
                        'questions' => [
                            ['text' => 'Methods/strategy employed'],
                            ['text' => 'Mastery of the subject matter'],
                            ['text' => 'Ability to draw and maintain interest and participation'],
                            ['text' => 'Relevancy and applicability of the topic/content discussed'],
                        ]
                    ],
                ],
                'comments' => [
                    ['text' => 'VIII. Positive Comments', 'required' => false],
                    ['text' => 'IX. Suggestions/Recommendations for Improvement', 'required' => false],
                ],
                'has_speaker' => true,
                'has_food' => true,
            ],
            
            // Type 2: 5 Quality Dimension (F-EEF-018d)
            'type2' => [
                'name' => '5 Quality Dimension',
                'form_number' => 'F-EEF-018d',
                'revision' => 'Rev. 0',
                'date_effectivity' => '04-28-2025',
                'categories' => [
                    [
                        'name' => 'I. Information Dissemination',
                        'questions' => [
                            ['text' => 'Timelines of sending invites'],
                            ['text' => 'Adequacy of information dissemination'],
                        ]
                    ],
                    [
                        'name' => 'II. Design of the Event',
                        'questions' => [
                            ['text' => 'Program / Order of activities'],
                            ['text' => 'Relevance of the activities'],
                            ['text' => 'Time allotment / pacing'],
                        ]
                    ],
                    [
                        'name' => 'III. Outcomes of the Event',
                        'questions' => [
                            ['text' => 'Attendance of participants'],
                            ['text' => 'Participation to activities'],
                            ['text' => 'Interaction'],
                            ['text' => 'Teamwork'],
                        ]
                    ],
                    [
                        'name' => 'IV. Secretariat',
                        'questions' => [
                            ['text' => 'Sensitivity in providing assistance/needs to the participants'],
                            ['text' => 'Management on the entire activities'],
                            ['text' => 'Provision of information/feedback to the participants in a clear, concise manner'],
                        ]
                    ],
                    [
                        'name' => 'V. Facilities',
                        'questions' => [
                            ['text' => 'Overall appearance of the venue'],
                            ['text' => 'Cleanliness and orderliness'],
                            ['text' => 'Availability and functionality of applicable equipment'],
                        ]
                    ],
                ],
                'comments' => [
                    ['text' => 'VI. Positive Comments', 'required' => false],
                    ['text' => 'VII. Suggestions/Recommendations for Improvement', 'required' => false],
                ],
                'has_speaker' => false,
                'has_food' => false,
            ],
            
            // Type 3: 8 Quality Dimension (F-EEF-018e)
            'type3' => [
                'name' => '8 Quality Dimension',
                'form_number' => 'F-EEF-018e',
                'revision' => 'Rev. 0',
                'date_effectivity' => '06-16-2025',
                'categories' => [
                    [
                        'name' => 'I. Information Dissemination',
                        'questions' => [
                            ['text' => 'Timelines of sending invites'],
                            ['text' => 'Adequacy of information dissemination'],
                        ]
                    ],
                    [
                        'name' => 'II. Design of the Event',
                        'questions' => [
                            ['text' => 'Program / Order of activities'],
                            ['text' => 'Relevance of the activities'],
                            ['text' => 'Time allotment / pacing'],
                        ]
                    ],
                    [
                        'name' => 'III. Outcomes of the Event',
                        'questions' => [
                            ['text' => 'Attendance of participants'],
                            ['text' => 'Participation to activities'],
                            ['text' => 'Timeliness and orderliness of the overall event'],
                            ['text' => 'Execution of awarding and recognition of graduates'],
                        ]
                    ],
                    [
                        'name' => 'IV. Secretariat',
                        'questions' => [
                            ['text' => 'Sensitivity in providing assistance to the participants'],
                            ['text' => 'Management of the entire activities'],
                            ['text' => 'Provision of information/feedback to the participants in a clear, concise manner'],
                        ]
                    ],
                    [
                        'name' => 'V. Venue and other Facilities',
                        'questions' => [
                            ['text' => 'Overall appearance of the venue'],
                            ['text' => 'Cleanliness and orderliness'],
                            ['text' => 'Comfortability of room temperature and ventilation'],
                            ['text' => 'Functionality and quality of audio-visual equipment'],
                            ['text' => 'Suitability of the venue for the number of participants/guests'],
                        ]
                    ],
                    [
                        'name' => 'VI. Food (For Students, Guests, Faculty and Working Committee)',
                        'questions' => [
                            ['text' => 'Quality of foods and beverages'],
                            ['text' => 'Food and beverages presentation/setup'],
                            ['text' => 'Timeliness in the delivery of food'],
                            ['text' => 'Quality of services provided'],
                            ['text' => 'Sufficiency of foods'],
                            ['text' => 'Quantity/Serving of food provided'],
                        ]
                    ],
                    [
                        'name' => 'VII. Resource Speaker',
                        'questions' => [
                            ['text' => 'Methods/strategy employed'],
                            ['text' => 'Mastery of the subject matter'],
                            ['text' => 'Ability to draw and maintain interest and participation'],
                            ['text' => 'Relevance and applicability of the topic/content discussed'],
                        ]
                    ],
                    [
                        'name' => 'VIII. Traffic Management',
                        'questions' => [
                            ['text' => 'Traffic control management'],
                            ['text' => 'Clarity of signs and instruction'],
                            ['text' => 'Traffic capacity and safety'],
                        ]
                    ],
                ],
                'comments' => [
                    ['text' => 'IX. What went well?', 'required' => false],
                    ['text' => 'X. What went not-so-well?', 'required' => false],
                    ['text' => 'XI. What should we change for the next time we hold this event?', 'required' => false],
                    ['text' => 'XII. Any recommendations for improvement?', 'required' => false],
                ],
                'has_speaker' => true,
                'has_food' => true,
            ],
            
            // Type 4: 6 Quality Dimension without Meals (F-EEF-018b)
            'type4' => [
                'name' => '6 Quality Dimension (without Meals)',
                'form_number' => 'F-EEF-018b',
                'revision' => 'Rev. 0',
                'date_effectivity' => '04-28-2025',
                'categories' => [
                    [
                        'name' => 'I. Information Dissemination',
                        'questions' => [
                            ['text' => 'Timeliness of sending invites'],
                            ['text' => 'Adequacy of information dissemination'],
                        ]
                    ],
                    [
                        'name' => 'II. Design of the Event',
                        'questions' => [
                            ['text' => 'Program / Order of activities'],
                            ['text' => 'Relevance of the activities'],
                            ['text' => 'Time allotment / pacing'],
                        ]
                    ],
                    [
                        'name' => 'III. Outcomes of the Event',
                        'questions' => [
                            ['text' => 'Attendance of participants'],
                            ['text' => 'Participation to activities'],
                            ['text' => 'Interaction'],
                            ['text' => 'Teamwork'],
                        ]
                    ],
                    [
                        'name' => 'IV. Secretariat',
                        'questions' => [
                            ['text' => 'Sensitivity in providing assistance/needs to the participants'],
                            ['text' => 'Management on the entire activities'],
                            ['text' => 'Provision of information/feedback to the participants in a clear, concise manner'],
                        ]
                    ],
                    [
                        'name' => 'V. Facilities',
                        'questions' => [
                            ['text' => 'Overall appearance of the venue'],
                            ['text' => 'Cleanliness and orderliness'],
                            ['text' => 'Availability and functionality of applicable equipment'],
                        ]
                    ],
                    [
                        'name' => 'VI. Resource Speaker',
                        'questions' => [
                            ['text' => 'Methods/strategy employed'],
                            ['text' => 'Mastery of the subject matter'],
                            ['text' => 'Ability to draw and maintain interest and participation'],
                            ['text' => 'Relevancy and applicability of the topic/content discussed'],
                        ]
                    ],
                ],
                'comments' => [
                    ['text' => 'VII. Positive Comments', 'required' => false],
                    ['text' => 'VIII. Suggestions/Recommendations for Improvement', 'required' => false],
                ],
                'has_speaker' => true,
                'has_food' => false,
            ],
            
            // Type 5: 6 Quality Dimension without Speaker (F-EEF-018c)
            'type5' => [
                'name' => '6 Quality Dimension (without Speaker)',
                'form_number' => 'F-EEF-018c',
                'revision' => 'Rev. 0',
                'date_effectivity' => '04-28-2025',
                'categories' => [
                    [
                        'name' => 'I. Information Dissemination',
                        'questions' => [
                            ['text' => 'Timeliness of sending invites'],
                            ['text' => 'Adequacy of information dissemination'],
                        ]
                    ],
                    [
                        'name' => 'II. Design of the Event',
                        'questions' => [
                            ['text' => 'Program / Order of activities'],
                            ['text' => 'Relevance of the activities'],
                            ['text' => 'Time allotment / pacing'],
                        ]
                    ],
                    [
                        'name' => 'III. Outcomes of the Event',
                        'questions' => [
                            ['text' => 'Attendance of participants'],
                            ['text' => 'Participation to activities'],
                            ['text' => 'Interaction'],
                            ['text' => 'Teamwork'],
                        ]
                    ],
                    [
                        'name' => 'IV. Secretariat',
                        'questions' => [
                            ['text' => 'Sensitivity in providing assistance/needs to the participants'],
                            ['text' => 'Management on the entire activities'],
                            ['text' => 'Provision of information/feedback to the participants in a clear, concise manner'],
                        ]
                    ],
                    [
                        'name' => 'V. Facilities',
                        'questions' => [
                            ['text' => 'Overall appearance of the venue'],
                            ['text' => 'Cleanliness and orderliness'],
                            ['text' => 'Availability and functionality of applicable equipment'],
                        ]
                    ],
                    [
                        'name' => 'VI. Food',
                        'questions' => [
                            ['text' => 'Quality of food and beverages'],
                            ['text' => 'Food and beverages presentation/setup'],
                            ['text' => 'Timelines of delivery of food'],
                            ['text' => 'Quality of service provided'],
                            ['text' => 'Sufficiency of foods'],
                            ['text' => 'Quantity/Serving of food provided'],
                        ]
                    ],
                ],
                'comments' => [
                    ['text' => 'VII. Positive Comments', 'required' => false],
                    ['text' => 'VIII. Suggestions/Recommendations for Improvement', 'required' => false],
                ],
                'has_speaker' => false,
                'has_food' => true,
            ],
        ];

        return $templates[$formType] ?? $templates['type2'];
    }
    public function getEligibilityInfo(Evaluation $evaluation)
{
    $event = $evaluation->event;
    
    // Get department names
    $departments = [];
    if (!empty($event->departments)) {
        $departments = \App\Models\Department::whereIn('id', $event->departments)
            ->pluck('name')
            ->toArray();
    }
    
    // Get course names
    $courses = [];
    if (!empty($event->courses)) {
        $courses = \App\Models\Course::whereIn('id', $event->courses)
            ->pluck('name')
            ->toArray();
    }
    
    $yearLevels = $event->year_levels ?? ['1st Year', '2nd Year', '3rd Year', '4th Year'];
    
    return response()->json([
        'departments' => $departments,
        'courses' => $courses,
        'yearLevels' => $yearLevels,
        'event_name' => $event->event_name,
        'form_type' => $this->getFormTypeName($evaluation->form_type),
    ]);
}
}