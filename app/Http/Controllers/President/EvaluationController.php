<?php

namespace App\Http\Controllers\President;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use App\Models\EvaluationResponse;
use App\Models\EvaluationQuestion;
use App\Models\AIAnalysis;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Carbon\Carbon;

class EvaluationController extends Controller
{
    protected $organizationId;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::guard('org_user')->user();
            if (!$user || $user->role !== 'president') {
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

        return Inertia::render('President/Evaluations/Index', [
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
        
        // Get all AI insights (overall and per date)
        $allAiInsights = AIAnalysis::where('evaluation_id', $evaluation->id)
            ->orderBy('event_date', 'asc')
            ->get()
            ->map(function ($insight) {
                return [
                    'event_date' => $insight->event_date,
                    'summary' => $insight->summary,
                    'strengths' => json_decode($insight->strengths, true) ?: [],
                    'weaknesses' => json_decode($insight->weaknesses, true) ?: [],
                    'recommendations' => json_decode($insight->recommendations, true) ?: [],
                    'predicted_satisfaction' => $insight->predicted_satisfaction,
                    'success_probability' => $insight->success_probability,
                    'response_rate' => $insight->response_rate,
                    'total_respondents' => $insight->total_respondents,
                    'analyzed_at' => $insight->analyzed_at,
                    'category_breakdown' => json_decode($insight->category_breakdown, true) ?: [],
                    'feature_importance' => json_decode($insight->feature_importance, true) ?: [],
                    'sentiment_analysis' => json_decode($insight->sentiment_analysis, true) ?: [],
                    'what_if_analysis' => json_decode($insight->what_if_analysis, true) ?: [],
                    'critical_factors' => json_decode($insight->critical_factors, true) ?: [],
                    'low_scoring_questions' => json_decode($insight->low_scoring_questions, true) ?: [],
                ];
            })->toArray();
        
        // Separate overall insights from date-specific insights
        $overallInsights = null;
        $dateInsights = [];
        
        foreach ($allAiInsights as $insight) {
            if ($insight['event_date'] === null) {
                $overallInsights = $insight;
            } else {
                $dateInsights[] = $insight;
            }
        }
        
        // Get available dates from responses
        $availableDates = EvaluationResponse::where('evaluation_id', $evaluation->id)
            ->distinct()
            ->pluck('event_date')
            ->map(function($date) {
                return Carbon::parse($date)->format('Y-m-d');
            })
            ->toArray();
        
        // Calculate stats for overall
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

        // Calculate category breakdown from stats if no AI insights
        $categoryBreakdown = [];
        if ($overallInsights && !empty($overallInsights['category_breakdown'])) {
            $categoryBreakdown = $overallInsights['category_breakdown'];
        } elseif ($evaluation->categories) {
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
                    $categoryBreakdown[$category->category_name] = round($totalScore / $questionCount, 2);
                }
            }
        }

        return Inertia::render('President/Evaluations/Show', [
            'evaluation' => [
                'id' => $evaluation->id,
                'title' => $evaluation->title,
                'status' => $evaluation->status,
                'qr_code_url' => $evaluation->qr_code_url ?? route('evaluations.form', $evaluation->id),
                'event' => [
                    'id' => $evaluation->event->id,
                    'event_name' => $evaluation->event->event_name,
                    'event_dates' => $evaluation->event_dates ?: [],
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
                'overall_satisfaction' => $overallSatisfaction,
            ],
            'stats' => $stats,
            'comments' => $comments,
            'aiInsights' => $overallInsights,
            'dateInsights' => $dateInsights,
            'availableDates' => $availableDates,
            'categoryBreakdown' => $categoryBreakdown,
        ]);
    }
}