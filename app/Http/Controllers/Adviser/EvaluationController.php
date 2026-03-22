<?php

namespace App\Http\Controllers\Adviser;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Evaluation;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class EvaluationController extends Controller
{
    protected $organizationId;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::guard('org_user')->user();
            if (!$user || $user->role !== 'adviser') {
                return redirect()->route('login');
            }
            $this->organizationId = $user->organization_id;
            return $next($request);
        });
    }

    public function index()
    {
        $evaluations = Evaluation::with(['event', 'event.eventType'])
            ->where('organization_id', $this->organizationId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($evaluation) {
                return [
                    'id' => $evaluation->id,
                    'title' => $evaluation->title,
                    'event_name' => $evaluation->event->event_name,
                    'event_status' => $evaluation->event->status,
                    'status' => $evaluation->status,
                    'responses_count' => $evaluation->total_responses,
                    'created_at' => $evaluation->created_at->format('Y-m-d'),
                ];
            });

        return Inertia::render('Adviser/Evaluations/Index', [
            'evaluations' => $evaluations
        ]);
    }

    public function show(Evaluation $evaluation)
    {
        if ($evaluation->organization_id !== $this->organizationId) {
            abort(403);
        }

        $evaluation->load(['event', 'categories.questions', 'questions' => function ($q) {
            $q->where('question_type', 'comment');
        }]);

        $responses = \App\Models\EvaluationResponse::where('evaluation_id', $evaluation->id)->get();
        
        $stats = [];
        $likertQuestions = \App\Models\EvaluationQuestion::where('evaluation_id', $evaluation->id)
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
        $commentQuestions = \App\Models\EvaluationQuestion::where('evaluation_id', $evaluation->id)
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

        $aiInsights = \App\Models\AIAnalysis::where('evaluation_id', $evaluation->id)->first();

        // Calculate category breakdown and feature importance from AI insights if available
        $categoryBreakdown = [];
        $featureImportance = [];
        $sentimentAnalysis = [];
        $whatIfTargeted = [];
        $whatIfOptimistic = [];

        if ($aiInsights) {
            // Get category scores from the AI analysis
            $categoryBreakdown = json_decode($aiInsights->category_breakdown, true) ?? [];
            
            // Get feature importance
            $featureImportance = json_decode($aiInsights->feature_importance, true) ?? [];
            
            // Get sentiment analysis data
            $sentimentAnalysis = json_decode($aiInsights->sentiment_analysis, true) ?? [];
            
            // Get what-if analysis data
            $whatIfData = json_decode($aiInsights->what_if_analysis, true) ?? [];
            $whatIfTargeted = $whatIfData['targeted'] ?? [];
            $whatIfOptimistic = $whatIfData['optimistic'] ?? [];
            
            // If category breakdown is empty, calculate it from the category scores
            if (empty($categoryBreakdown) && $evaluation->categories) {
                $categoryScores = [];
                foreach ($evaluation->categories as $category) {
                    $totalScore = 0;
                    $questionCount = 0;
                    foreach ($category->questions as $question) {
                        if (isset($stats[$question->id]['average'])) {
                            $totalScore += $stats[$question->id]['average'];
                            $questionCount++;
                        }
                    }
                    if ($questionCount > 0) {
                        $categoryScores[$category->category_name] = round($totalScore / $questionCount, 2);
                    }
                }
                $categoryBreakdown = $categoryScores;
            }
        }

        return Inertia::render('Adviser/Evaluations/Show', [
            'evaluation' => [
                'id' => $evaluation->id,
                'title' => $evaluation->title,
                'status' => $evaluation->status,
                'qr_code_url' => $evaluation->qr_code_url ?? route('evaluations.form', $evaluation->id),
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
            ],
            'stats' => $stats,
            'comments' => $comments,
            'aiInsights' => $aiInsights ? [
                'summary' => $aiInsights->summary,
                'strengths' => json_decode($aiInsights->strengths, true),
                'weaknesses' => json_decode($aiInsights->weaknesses, true),
                'recommendations' => json_decode($aiInsights->recommendations, true),
                'predicted_satisfaction' => $aiInsights->predicted_satisfaction,
                'success_probability' => $aiInsights->success_probability,
                'response_rate' => $aiInsights->response_rate,
                'total_respondents' => $aiInsights->total_respondents,
                'analyzed_at' => $aiInsights->analyzed_at,
                'category_breakdown' => $categoryBreakdown,
                'feature_importance' => $featureImportance,
                'sentiment_analysis' => $sentimentAnalysis,
                'what_if_targeted' => $whatIfTargeted,
                'what_if_optimistic' => $whatIfOptimistic,
            ] : null,
        ]);
    }
}