<?php

namespace App\Http\Controllers\President;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Evaluation;
use App\Models\EvaluationCategory;
use App\Models\EvaluationQuestion;
use App\Models\EvaluationResponse;
use App\Models\AIAnalysis;
use App\Models\Course;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Inertia\Inertia;
use App\Jobs\AnalyzeEvaluationJob;
use App\Imports\EvaluationResponsesImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\AIAnalysisService;

class EvaluationController extends Controller
{
    protected $organizationId;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::guard('org_user')->user();
            if (!$user) {
                return redirect()->route('login');
            }
            $this->organizationId = $user->organization_id;
            return $next($request);
        });
    }

    /**
     * Display list of evaluations
     */
    public function index()
    {
        try {
            $evaluations = Evaluation::with(['event'])
                ->where('organization_id', $this->organizationId)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($evaluation) {
                    return [
                        'id' => $evaluation->id,
                        'title' => $evaluation->title,
                        'form_number' => $evaluation->form_number,
                        'event_name' => $evaluation->event->event_name,
                        'event_status' => $evaluation->event->status,
                        'status' => $evaluation->status,
                        'categories_count' => $evaluation->categories()->count(),
                        'questions_count' => $evaluation->questions()->count(),
                        'responses_count' => $evaluation->total_responses,
                        'can_generate_qr' => $evaluation->canGenerateQR(),
                        'created_at' => $evaluation->created_at->format('Y-m-d'),
                    ];
                });

            // Get finished events that don't have evaluations yet
            $availableEvents = Event::where('user_id', $this->organizationId)
                ->where('status', 'Finished')
                ->whereDoesntHave('evaluations')
                ->get(['id', 'event_name']);

            return Inertia::render('President/Evaluations/Index', [
                'evaluations' => $evaluations,
                'availableEvents' => $availableEvents
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to load evaluations', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Failed to load evaluations.');
        }
    }

    /**
     * Show form to create evaluation
     */
    public function create(Request $request)
    {
        try {
            $eventId = $request->get('event_id');
            $event = null;
            
            if ($eventId) {
                $event = Event::where('id', $eventId)
                    ->where('user_id', $this->organizationId)
                    ->firstOrFail();
            }

            return Inertia::render('President/Evaluations/Create', [
                'event' => $event
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to load create form', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('president.evaluations.index')
                ->with('error', 'Failed to load create form.');
        }
    }

    /**
     * Store evaluation with categories and questions
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'event_id' => 'required|exists:events,id',
                'title' => 'required|string|max:255',
                'form_number' => 'required|string',
                'revision' => 'required|string',
                'date_effectivity' => 'required|string',
                'available_from' => 'nullable|date',
                'available_until' => 'nullable|date|after:available_from',
                'categories' => 'required|array|min:1',
                'categories.*.name' => 'required|string',
                'categories.*.questions' => 'required|array|min:1',
                'categories.*.questions.*.text' => 'required|string',
                'comments' => 'array',
                'comments.*.text' => 'required|string',
            ]);

            // Verify event belongs to organization
            $event = Event::where('id', $request->event_id)
                ->where('user_id', $this->organizationId)
                ->firstOrFail();

            DB::beginTransaction();

            // Create evaluation
            $evaluation = Evaluation::create([
                'event_id' => $event->id,
                'organization_id' => $this->organizationId,
                'title' => $request->title,
                'form_number' => $request->form_number,
                'revision' => $request->revision,
                'date_effectivity' => $request->date_effectivity,
                'available_from' => $request->available_from,
                'available_until' => $request->available_until,
                'status' => 'draft',
            ]);

            // Create categories and their questions
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

            // Create comment sections
            if ($request->has('comments')) {
                foreach ($request->comments as $cIndex => $cData) {
                    EvaluationQuestion::create([
                        'evaluation_id' => $evaluation->id,
                        'category_id' => null,
                        'question_text' => $cData['text'],
                        'question_type' => 'comment',
                        'order' => $cIndex,
                        'is_required' => $cData['required'] ?? true,
                    ]);
                }
            }

            DB::commit();

            Log::info('Evaluation created', [
                'evaluation_id' => $evaluation->id,
                'event_id' => $event->id
            ]);

            return redirect()->route('president.evaluations.index')
                ->with('success', 'Evaluation created successfully.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create evaluation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Failed to create evaluation: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show evaluation details and results
     */
    public function show(Evaluation $evaluation)
    {
        try {
            if ($evaluation->organization_id !== $this->organizationId) {
                abort(403);
            }

            $evaluation->load(['event', 'categories.questions', 'questions' => function ($q) {
                $q->where('question_type', 'comment');
            }]);

            // Get all responses
            $responses = EvaluationResponse::where('evaluation_id', $evaluation->id)->get();

            // Calculate statistics for likert questions
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

            // Get comments
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

            // Get AI insights if available
            $aiInsights = AIAnalysis::where('evaluation_id', $evaluation->id)->first();

            return Inertia::render('President/Evaluations/Show', [
                'evaluation' => [
                    'id' => $evaluation->id,
                    'title' => $evaluation->title,
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
                    'ai_insights' => $aiInsights ? [
                        'summary' => $aiInsights->summary,
                        'strengths' => json_decode($aiInsights->strengths, true),
                        'weaknesses' => json_decode($aiInsights->weaknesses, true),
                        'recommendations' => json_decode($aiInsights->recommendations, true),
                        'predicted_satisfaction' => $aiInsights->predicted_satisfaction,
                        'success_probability' => $aiInsights->success_probability,
                        'category_breakdown' => json_decode($aiInsights->category_breakdown, true),
                        'analyzed_at' => $aiInsights->analyzed_at,
                    ] : null,
                ],
                'stats' => $stats,
                'comments' => $comments,
                'canGenerateQR' => $evaluation->canGenerateQR(),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to show evaluation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('president.evaluations.index')
                ->with('error', 'Failed to load evaluation details.');
        }
    }

    /**
     * Edit evaluation (only if draft)
     */
    public function edit(Evaluation $evaluation)
    {
        try {
            if ($evaluation->organization_id !== $this->organizationId) {
                abort(403);
            }

            if ($evaluation->status !== 'draft') {
                return redirect()->route('president.evaluations.show', $evaluation->id)
                    ->with('error', 'Cannot edit evaluation that is already active or closed.');
            }

            $evaluation->load(['categories.questions', 'questions' => function ($q) {
                $q->where('question_type', 'comment');
            }]);

            return Inertia::render('President/Evaluations/Edit', [
                'evaluation' => [
                    'id' => $evaluation->id,
                    'title' => $evaluation->title,
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
                'event' => $evaluation->event,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to load edit form', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('president.evaluations.show', $evaluation->id)
                ->with('error', 'Failed to load edit form.');
        }
    }

    /**
     * Update evaluation
     */
    public function update(Request $request, Evaluation $evaluation)
    {
        try {
            if ($evaluation->organization_id !== $this->organizationId) {
                abort(403);
            }

            if ($evaluation->status !== 'draft') {
                return response()->json(['error' => 'Cannot update evaluation that is already active.'], 400);
            }

            $request->validate([
                'title' => 'required|string|max:255',
                'form_number' => 'required|string',
                'revision' => 'required|string',
                'date_effectivity' => 'required|string',
                'available_from' => 'nullable|date',
                'available_until' => 'nullable|date|after:available_from',
                'categories' => 'required|array|min:1',
                'comments' => 'array',
            ]);

            DB::beginTransaction();

            // Update evaluation
            $evaluation->update([
                'title' => $request->title,
                'form_number' => $request->form_number,
                'revision' => $request->revision,
                'date_effectivity' => $request->date_effectivity,
                'available_from' => $request->available_from,
                'available_until' => $request->available_until,
            ]);

            // Delete old categories and questions
            $evaluation->categories()->delete();
            $evaluation->questions()->delete();

            // Create categories and their questions
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

            // Create comment sections
            if ($request->has('comments')) {
                foreach ($request->comments as $cIndex => $cData) {
                    EvaluationQuestion::create([
                        'evaluation_id' => $evaluation->id,
                        'category_id' => null,
                        'question_text' => $cData['text'],
                        'question_type' => 'comment',
                        'order' => $cIndex,
                        'is_required' => $cData['required'] ?? true,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('president.evaluations.show', $evaluation->id)
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

    /**
     * Activate evaluation and redirect to QR page
     */
    public function activateQR(Evaluation $evaluation)
    {
        try {
            if ($evaluation->organization_id !== $this->organizationId) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            if ($evaluation->event->status !== 'Finished') {
                return response()->json(['error' => 'QR code can only be generated when the event is marked as finished.'], 400);
            }

            if ($evaluation->status !== 'draft') {
                return response()->json(['error' => 'QR code can only be generated for draft evaluations.'], 400);
            }

            $evaluation->update([
                'status' => 'active',
                'qr_code_url' => route('evaluations.form', $evaluation->id)
            ]);

            Log::info('Evaluation activated', ['evaluation_id' => $evaluation->id]);

            return response()->json([
                'success' => true,
                'evaluation_url' => route('evaluations.form', $evaluation->id)
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to activate evaluation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Failed to activate evaluation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show QR code page
     */
    public function showQRCode(Evaluation $evaluation)
    {
        if ($evaluation->organization_id !== $this->organizationId) {
            abort(403);
        }

        if ($evaluation->status !== 'active') {
            return redirect()->route('president.evaluations.show', $evaluation->id)
                ->with('error', 'Evaluation must be active to view QR code.');
        }

        $url = route('evaluations.form', $evaluation->id);
        
        return Inertia::render('President/Evaluations/QRCode', [
            'evaluation' => [
                'id' => $evaluation->id,
                'title' => $evaluation->title,
                'event_name' => $evaluation->event->event_name,
            ],
            'qr_data' => $url
        ]);
    }

    public function close(Evaluation $evaluation)
{
    try {
        if ($evaluation->organization_id !== $this->organizationId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($evaluation->status !== 'active') {
            return response()->json(['error' => 'Only active evaluations can be closed.'], 400);
        }

        DB::beginTransaction();
        
        $evaluation->update(['status' => 'closed']);

        DB::commit();

        // FORCE immediate execution with dispatchSync
        if ($evaluation->total_responses > 0) {
            try {
                // This will run right now, no queue needed
                AnalyzeEvaluationJob::dispatchSync($evaluation);
                    
                Log::info('✅ AI analysis completed for evaluation', [
                    'evaluation_id' => $evaluation->id,
                    'responses' => $evaluation->total_responses
                ]);
            } catch (\Exception $e) {
                Log::error('❌ AI analysis failed', [
                    'evaluation_id' => $evaluation->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        return response()->json(['success' => true]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Failed to close evaluation', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json(['error' => 'Failed to close evaluation.'], 500);
    }
}

    /**
     * Reopen a closed evaluation
     */
    public function reopen(Evaluation $evaluation)
    {
        try {
            if ($evaluation->organization_id !== $this->organizationId) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            if ($evaluation->status !== 'closed') {
                return response()->json(['error' => 'Only closed evaluations can be reopened.'], 400);
            }

            DB::beginTransaction();
            
            $evaluation->update(['status' => 'active']);

            DB::commit();

            Log::info('Evaluation reopened', ['evaluation_id' => $evaluation->id]);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to reopen evaluation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Failed to reopen evaluation.'], 500);
        }
    }

    /**
     * Get AI insights for an evaluation
     */
    public function getAIInsights(Evaluation $evaluation)
    {
        if ($evaluation->organization_id !== $this->organizationId) {
            abort(403);
        }
        
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
            'critical_factors' => json_decode($analysis->critical_factors ?? '[]', true),
            'category_breakdown' => json_decode($analysis->category_breakdown, true),
            'analyzed_at' => $analysis->analyzed_at,
        ]);
    }

    /**
     * Delete evaluation
     */
    public function destroy(Evaluation $evaluation)
    {
        try {
            if ($evaluation->organization_id !== $this->organizationId) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            if ($evaluation->status !== 'draft') {
                return response()->json(['error' => 'Only draft evaluations can be deleted.'], 400);
            }

            if ($evaluation->qr_code_path) {
                Storage::disk('public')->delete($evaluation->qr_code_path);
            }

            $evaluation->delete();

            Log::info('Evaluation deleted', [
                'evaluation_id' => $evaluation->id
            ]);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Failed to delete evaluation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Failed to delete evaluation.'], 500);
        }
    }

    /**
 * Manually generate AI insights (checks threshold)
 */
public function generateInsights(Evaluation $evaluation)
{
    try {
        if ($evaluation->organization_id !== $this->organizationId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($evaluation->status !== 'closed') {
            return response()->json(['error' => 'Evaluation must be closed to generate insights.'], 400);
        }

        // FIX: Use resolve() instead of app()
        $aiService = resolve(AIAnalysisService::class);
        
        // Check threshold (optional)
        $event = $evaluation->event;
        $totalEligible = \App\Models\EventStudent::where('event_id', $event->id)->count();
        $responseRate = $totalEligible > 0 
            ? round(($evaluation->total_responses / $totalEligible) * 100, 1) 
            : 0;
        
        // Generate insights with force=true to bypass threshold
        $insights = $aiService->analyzeEvaluation($evaluation, true);
        
        if ($insights) {
            $message = 'AI insights generated successfully!';
            if ($responseRate < 75) {
                $message .= " Note: Response rate is only {$responseRate}% (below 75% threshold).";
            }
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'response_rate' => $responseRate
            ]);
        } else {
            return response()->json(['error' => 'Failed to generate insights.'], 500);
        }

    } catch (\Exception $e) {
        Log::error('Generate insights failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'error' => 'Failed to generate insights: ' . $e->getMessage()
        ], 500);
    }
}
/**
 * Bulk upload responses via CSV
 */
public function bulkUploadResponses(Request $request, Evaluation $evaluation)
{
    try {
        if ($evaluation->organization_id !== $this->organizationId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($evaluation->status !== 'active') {
            return response()->json(['error' => 'Evaluation must be active to upload responses.'], 400);
        }

        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240', // Max 10MB
        ]);

        $file = $request->file('csv_file');
        
        // Create import instance
        $import = new EvaluationResponsesImport($evaluation);
        
        // Process the import
        Excel::import($import, $file);
        
        // Get import stats
        $stats = $import->getStats();
        
        // Update evaluation total responses count
        $evaluation->update([
            'total_responses' => EvaluationResponse::where('evaluation_id', $evaluation->id)->count()
        ]);

        $message = "✅ Successfully imported {$stats['success']} responses.";
        if ($stats['errors'] > 0) {
            $message .= " Failed: {$stats['errors']}. Check logs for details.";
            
            // Log errors
            Log::warning('CSV Import errors', [
                'evaluation_id' => $evaluation->id,
                'errors' => $stats['error_details']
            ]);
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

/**
 * Download CSV template for bulk upload
 */
public function downloadCsvTemplate(Evaluation $evaluation)
{
    if ($evaluation->organization_id !== $this->organizationId) {
        abort(403);
    }

    $headers = [
        'student_id',
        'email',
        'name',
        'department',
        'course',
        'year_level',
    ];

    // Add question columns
    $questions = $evaluation->questions()->where('question_type', 'likert')->get();
    foreach ($questions as $q) {
        $headers[] = 'q_' . $q->id;
    }

    // Add comment columns
    $comments = $evaluation->questions()->where('question_type', 'comment')->get();
    foreach ($comments as $c) {
        $headers[] = 'c_' . $c->id;
    }

    // Create sample data row
    $sampleRow = [
        'CEIT-2024-0001',
        'student@example.com',
        'Juan Dela Cruz',
        'College of Engineering',
        'BSIT',
        '2nd Year',
    ];

    // Add sample ratings (all 4s for questions)
    foreach ($questions as $q) {
        $sampleRow[] = 4;
    }

    // Add sample comments (empty)
    foreach ($comments as $c) {
        $sampleRow[] = '';
    }

    $callback = function() use ($headers, $sampleRow) {
        $file = fopen('php://output', 'w');
        
        // Add headers
        fputcsv($file, $headers);
        
        // Add sample row
        fputcsv($file, $sampleRow);
        
        // Add a second empty row for user to fill
        $emptyRow = array_fill(0, count($headers), '');
        fputcsv($file, $emptyRow);
        
        fclose($file);
    };

    $filename = 'evaluation_' . $evaluation->id . '_template.csv';
    
    return response()->stream($callback, 200, [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
    ]);
}
}