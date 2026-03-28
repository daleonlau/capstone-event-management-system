<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use App\Models\Evaluation;
use App\Models\EvaluationResponse;
use App\Models\EvaluationQuestion;
use App\Models\EventStudent;
use App\Models\EventGuest;
use App\Models\AIAnalysis;
use Carbon\Carbon;

class AIAnalysisService
{
    protected string $apiUrl;
    protected int $timeout;
    
    const RESPONSE_THRESHOLD = 0.75;
    
    public function __construct()
    {
        $this->apiUrl = env('AI_SERVICE_URL', 'http://127.0.0.1:8001');
        $this->timeout = env('AI_SERVICE_TIMEOUT', 60);
        
        Log::info('🔧 AIAnalysisService initialized', [
            'api_url' => $this->apiUrl,
            'timeout' => $this->timeout
        ]);
    }
    
    protected function normalizeDate($date): ?string
    {
        if (empty($date)) {
            return null;
        }
        
        try {
            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            Log::warning('Failed to parse date', ['date' => $date, 'error' => $e->getMessage()]);
            return null;
        }
    }
    
    public function meetsThreshold(Evaluation $evaluation, ?string $eventDate = null): bool
    {
        $normalizedDate = $eventDate ? $this->normalizeDate($eventDate) : null;
        
        if ($normalizedDate) {
            $totalEligible = $this->getTotalEligibleForDate($evaluation, $normalizedDate);
            $totalResponses = EvaluationResponse::where('evaluation_id', $evaluation->id)
                ->whereDate('event_date', $normalizedDate)
                ->count();
        } else {
            $totalEligible = $this->getTotalUniqueEligible($evaluation);
            $totalResponses = $evaluation->total_responses;
        }
        
        if ($totalEligible === 0) return true;
        
        $responseRate = $totalResponses / $totalEligible;
        return $responseRate >= self::RESPONSE_THRESHOLD;
    }
    
    public function getTotalEligibleForDate(Evaluation $evaluation, string $eventDate): int
    {
        $normalizedDate = $this->normalizeDate($eventDate);
        $event = $evaluation->event;
        
        $studentCount = EventStudent::where('event_id', $event->id)->count();
        
        $guestCount = EventGuest::where('event_id', $event->id)
            ->where(function($query) use ($normalizedDate) {
                if (Schema::hasColumn('event_guests', 'event_date')) {
                    $query->whereDate('event_date', $normalizedDate);
                }
            })
            ->count();
        
        return $studentCount + $guestCount;
    }
    
    public function getTotalUniqueEligible(Evaluation $evaluation): int
    {
        $event = $evaluation->event;
        $eventDates = $evaluation->event_dates ?: [];
        $numberOfDates = count($eventDates);
        
        $studentCount = EventStudent::where('event_id', $event->id)->count();
        $guestCount = EventGuest::where('event_id', $event->id)->count();
        
        return ($studentCount * max($numberOfDates, 1)) + $guestCount;
    }
    
    public function getAvailableDates(Evaluation $evaluation): array
    {
        return EvaluationResponse::where('evaluation_id', $evaluation->id)
            ->distinct()
            ->orderBy('event_date')
            ->pluck('event_date')
            ->map(function($date) {
                return Carbon::parse($date)->format('Y-m-d');
            })
            ->toArray();
    }
    
    public function generateAllInsights(Evaluation $evaluation, bool $force = false): array
    {
        $results = [];
        $dates = $this->getAvailableDates($evaluation);
        
        foreach ($dates as $date) {
            Log::info('Generating insights for date', [
                'evaluation_id' => $evaluation->id,
                'date' => $date
            ]);
            
            $result = $this->analyzeEvaluation($evaluation, $date, $force);
            if ($result) {
                $results[$date] = $result;
            }
        }
        
        Log::info('Generating overall insights', [
            'evaluation_id' => $evaluation->id
        ]);
        
        $overallResult = $this->analyzeEvaluation($evaluation, null, $force);
        if ($overallResult) {
            $results['overall'] = $overallResult;
        }
        
        return $results;
    }
    
    public function analyzeEvaluation(Evaluation $evaluation, ?string $eventDate = null, bool $force = false): ?array
    {
        $normalizedDate = $eventDate ? $this->normalizeDate($eventDate) : null;
        $dateLabel = $normalizedDate ?? 'overall';
        
        Log::info('========== 🚀 AI ANALYSIS STARTED ==========', [
            'evaluation_id' => $evaluation->id,
            'event_date' => $dateLabel,
            'form_type' => $evaluation->form_type,
            'force' => $force
        ]);
        
        if (!$force && !$this->meetsThreshold($evaluation, $normalizedDate)) {
            Log::info('⏸️ Evaluation does not meet response threshold', [
                'evaluation_id' => $evaluation->id,
                'event_date' => $dateLabel
            ]);
            return null;
        }
        
        try {
            $evaluationData = $this->prepareEvaluationData($evaluation, $normalizedDate);
            
            if (!$evaluationData || $evaluationData['total_respondents'] === 0) {
                Log::error('❌ No evaluation data prepared', [
                    'evaluation_id' => $evaluation->id,
                    'event_date' => $dateLabel
                ]);
                return null;
            }
            
            $payload = [
                'data' => $evaluationData['features'],
                'year_level' => 1,
                'respondent_type' => 0,
                'positive_comments' => $evaluationData['positive_comments'],
                'suggestion_comments' => $evaluationData['suggestion_comments'],
                'total_respondents' => $evaluationData['total_respondents'],
                'response_rate' => $evaluationData['response_rate'],
                'event_date' => $normalizedDate,
            ];
            
            Log::info('📤 SENDING TO AI SERVICE', [
                'evaluation_id' => $evaluation->id,
                'event_date' => $dateLabel,
                'features_count' => count($evaluationData['features']),
                'positive_comments_count' => count($evaluationData['positive_comments']),
                'response_rate' => $evaluationData['response_rate'],
                'total_responses' => $evaluationData['total_respondents']
            ]);
            
            $response = Http::timeout($this->timeout)->post("{$this->apiUrl}/analyze", $payload);
            
            if ($response->successful()) {
                $insights = $response->json();
                
                Log::info('✅ AI SERVICE RESPONSE RECEIVED', [
                    'evaluation_id' => $evaluation->id,
                    'event_date' => $dateLabel,
                    'satisfaction' => $insights['predicted_satisfaction'] ?? 'N/A',
                    'strengths_count' => count($insights['strengths'] ?? []),
                    'weaknesses_count' => count($insights['weaknesses'] ?? []),
                    'recommendations_count' => count($insights['recommendations'] ?? [])
                ]);
                
                $this->storeInsights($evaluation, $insights, $normalizedDate);
                
                return $insights;
            } else {
                Log::error('❌ AI service error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('💥 AI analysis exception', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
        }
        
        return null;
    }
    
    protected function prepareEvaluationData(Evaluation $evaluation, ?string $eventDate = null): ?array
    {
        $normalizedDate = $eventDate ? $this->normalizeDate($eventDate) : null;
        
        Log::info('📊 PREPARING EVALUATION DATA', [
            'evaluation_id' => $evaluation->id,
            'event_date' => $normalizedDate ?? 'overall'
        ]);
        
        $query = EvaluationResponse::where('evaluation_id', $evaluation->id);
        
        if ($normalizedDate) {
            $query->whereDate('event_date', $normalizedDate);
        }
        
        $responses = $query->get();
        
        if ($responses->isEmpty()) {
            Log::warning('No responses found', [
                'evaluation_id' => $evaluation->id,
                'event_date' => $normalizedDate ?? 'overall'
            ]);
            return null;
        }
        
        Log::info('📊 Found responses', ['count' => $responses->count()]);
        
        $questions = EvaluationQuestion::where('evaluation_id', $evaluation->id)
            ->where('question_type', 'likert')
            ->orderBy('order')
            ->get()
            ->keyBy('id');
        
        if ($questions->isEmpty()) {
            Log::warning('No likert questions found', ['evaluation_id' => $evaluation->id]);
            return null;
        }
        
        $features = [];
        $featureCounts = [];
        $responseCount = 0;
        $positiveComments = [];
        $suggestionComments = [];
        
        foreach ($responses as $response) {
            $likert = $response->likert_responses;
            
            if (is_string($likert)) {
                $likert = json_decode($likert, true);
            }
            
            if (!is_array($likert) || empty($likert)) {
                continue;
            }
            
            foreach ($likert as $questionId => $rating) {
                $questionId = (int)$questionId;
                $rating = (float)$rating;
                
                if (!$questions->has($questionId)) {
                    continue;
                }
                
                $key = "q_{$questionId}";
                
                if (!isset($features[$key])) {
                    $features[$key] = 0;
                    $featureCounts[$key] = 0;
                }
                
                $features[$key] += $rating;
                $featureCounts[$key]++;
            }
            
            $comments = $response->comment_responses;
            if (is_string($comments)) {
                $comments = json_decode($comments, true);
            }
            
            if (is_array($comments)) {
                foreach ($comments as $comment) {
                    if (!empty($comment) && is_string($comment) && strlen(trim($comment)) > 0) {
                        $positiveComments[] = $comment;
                    }
                }
            }
            
            $responseCount++;
        }
        
        if ($responseCount === 0) {
            Log::warning('No valid responses processed', [
                'evaluation_id' => $evaluation->id,
                'event_date' => $normalizedDate ?? 'overall'
            ]);
            return null;
        }
        
        foreach ($features as $key => $value) {
            if ($featureCounts[$key] > 0) {
                $features[$key] = round($value / $featureCounts[$key], 2);
            }
        }
        
        if ($normalizedDate) {
            $totalEligible = $this->getTotalEligibleForDate($evaluation, $normalizedDate);
        } else {
            $totalEligible = $this->getTotalUniqueEligible($evaluation);
        }
        
        $responseRate = $totalEligible > 0 
            ? min(1, round($responseCount / $totalEligible, 2)) 
            : 0;
        
        return [
            'features' => $features,
            'form_type' => $evaluation->form_type,
            'year_level' => 1,
            'positive_comments' => $positiveComments,
            'suggestion_comments' => $positiveComments,
            'total_respondents' => $responseCount,
            'response_rate' => $responseRate,
        ];
    }
    
    protected function storeInsights(Evaluation $evaluation, array $insights, ?string $eventDate = null): void
    {
        $normalizedDate = $eventDate ? $this->normalizeDate($eventDate) : null;
        
        Log::info('📝 STORING INSIGHTS IN DATABASE', [
            'evaluation_id' => $evaluation->id,
            'event_date' => $normalizedDate ?? 'overall'
        ]);
    
        try {
            $sentimentAnalysis = [
                'sentiment_score' => $insights['sentiment_score'] ?? 0,
                'positive_percentage' => $insights['positive_percentage'] ?? 0,
                'negative_percentage' => $insights['negative_percentage'] ?? 0,
                'neutral_percentage' => $insights['neutral_percentage'] ?? 0,
                'total_comments' => $insights['total_comments'] ?? 0,
                'common_themes' => $insights['common_themes'] ?? [],
                'positive_comments' => $insights['positive_comments'] ?? [],
                'negative_comments' => $insights['negative_comments'] ?? [],
                'neutral_comments' => $insights['neutral_comments'] ?? []
            ];
            
            $whatIfAnalysis = [
                'optimistic' => [
                    'scenario' => $insights['what_if_optimistic']['scenario'] ?? 'Optimistic Scenario',
                    'current_satisfaction' => $insights['what_if_optimistic']['current_satisfaction'] ?? $insights['predicted_satisfaction'] ?? 0,
                    'projected_satisfaction' => $insights['what_if_optimistic']['projected_satisfaction'] ?? 0,
                    'gain' => $insights['what_if_optimistic']['gain'] ?? 0
                ],
                'targeted' => [
                    'scenario' => $insights['what_if_targeted']['scenario'] ?? 'Targeted Improvements',
                    'current_satisfaction' => $insights['what_if_targeted']['current_satisfaction'] ?? $insights['predicted_satisfaction'] ?? 0,
                    'projected_satisfaction' => $insights['what_if_targeted']['projected_satisfaction'] ?? 0,
                    'gain' => $insights['what_if_targeted']['gain'] ?? 0,
                    'improvements' => $insights['what_if_targeted']['improvements'] ?? []
                ]
            ];
            
            $recommendations = [];
            if (isset($insights['recommendations']) && is_array($insights['recommendations'])) {
                foreach ($insights['recommendations'] as $rec) {
                    $recommendations[] = [
                        'priority' => $rec['priority'] ?? 'medium',
                        'category' => $rec['category'] ?? 'General',
                        'title' => $rec['title'] ?? 'Improvement Opportunity',
                        'problem_statement' => $rec['problem_statement'] ?? $rec['description'] ?? '',
                        'action_items' => $rec['action_items'] ?? [],
                        'expected_outcome' => $rec['expected_outcome'] ?? '',
                        'resources_needed' => $rec['resources_needed'] ?? [],
                        'success_metrics' => $rec['success_metrics'] ?? []
                    ];
                }
            }
            
            $strengths = is_array($insights['strengths'] ?? null) ? $insights['strengths'] : [];
            $weaknesses = is_array($insights['weaknesses'] ?? null) ? $insights['weaknesses'] : [];
            $categoryBreakdown = is_array($insights['category_breakdown'] ?? null) ? $insights['category_breakdown'] : [];
            $criticalFactors = is_array($insights['critical_factors'] ?? null) ? $insights['critical_factors'] : [];
            $lowScoringQuestions = is_array($insights['low_scoring_questions'] ?? null) ? $insights['low_scoring_questions'] : [];
            $yearLevelAnalysis = is_array($insights['year_level_analysis'] ?? null) ? $insights['year_level_analysis'] : [];
            $featureImportance = is_array($insights['feature_importance'] ?? null) ? $insights['feature_importance'] : [];
            
            $data = [
                'summary' => $insights['summary'] ?? '',
                'strengths' => json_encode($strengths, JSON_UNESCAPED_UNICODE),
                'weaknesses' => json_encode($weaknesses, JSON_UNESCAPED_UNICODE),
                'recommendations' => json_encode($recommendations, JSON_UNESCAPED_UNICODE),
                'feature_importance' => json_encode($featureImportance, JSON_UNESCAPED_UNICODE),
                'sentiment_analysis' => json_encode($sentimentAnalysis, JSON_UNESCAPED_UNICODE),
                'what_if_analysis' => json_encode($whatIfAnalysis, JSON_UNESCAPED_UNICODE),
                'predicted_satisfaction' => $insights['predicted_satisfaction'] ?? 0,
                'success_probability' => $insights['success_probability'] ?? 0,
                'critical_factors' => json_encode($criticalFactors, JSON_UNESCAPED_UNICODE),
                'category_breakdown' => json_encode($categoryBreakdown, JSON_UNESCAPED_UNICODE),
                'low_scoring_questions' => json_encode($lowScoringQuestions, JSON_UNESCAPED_UNICODE),
                'year_level_analysis' => json_encode($yearLevelAnalysis, JSON_UNESCAPED_UNICODE),
                'response_rate' => $insights['response_rate'] ?? 0,
                'total_respondents' => $insights['total_respondents'] ?? 0,
                'analyzed_at' => now(),
            ];
            
            if ($normalizedDate) {
                $data['event_date'] = $normalizedDate;
            }
            
            $result = AIAnalysis::updateOrCreate(
                [
                    'evaluation_id' => $evaluation->id,
                    'event_date' => $normalizedDate
                ],
                $data
            );
            
            Log::info('✅ INSIGHTS STORED SUCCESSFULLY', [
                'evaluation_id' => $evaluation->id,
                'event_date' => $normalizedDate ?? 'overall',
                'analysis_id' => $result->id
            ]);
            
        } catch (\Exception $e) {
            Log::error('❌ FAILED TO STORE INSIGHTS', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    
    public function getInsights(Evaluation $evaluation, ?string $eventDate = null): ?array
    {
        $normalizedDate = $eventDate ? $this->normalizeDate($eventDate) : null;
        
        $query = AIAnalysis::where('evaluation_id', $evaluation->id);
        
        if ($normalizedDate) {
            $query->whereDate('event_date', $normalizedDate);
        } else {
            $query->whereNull('event_date');
        }
        
        $analysis = $query->first();
        
        if (!$analysis) return null;
        
        $sentimentAnalysis = json_decode($analysis->sentiment_analysis, true);
        $whatIfAnalysis = json_decode($analysis->what_if_analysis, true);
        $recommendations = json_decode($analysis->recommendations, true) ?: [];
        
        return [
            'summary' => $analysis->summary,
            'strengths' => json_decode($analysis->strengths, true) ?: [],
            'weaknesses' => json_decode($analysis->weaknesses, true) ?: [],
            'recommendations' => $recommendations,
            'feature_importance' => json_decode($analysis->feature_importance, true) ?: [],
            'sentiment_analysis' => $sentimentAnalysis,
            'what_if_analysis' => $whatIfAnalysis,
            'predicted_satisfaction' => $analysis->predicted_satisfaction,
            'success_probability' => $analysis->success_probability,
            'critical_factors' => json_decode($analysis->critical_factors, true) ?: [],
            'category_breakdown' => json_decode($analysis->category_breakdown, true) ?: [],
            'low_scoring_questions' => json_decode($analysis->low_scoring_questions, true) ?: [],
            'year_level_analysis' => json_decode($analysis->year_level_analysis, true) ?: [],
            'response_rate' => $analysis->response_rate,
            'total_respondents' => $analysis->total_respondents,
            'analyzed_at' => $analysis->analyzed_at,
            'sentiment_score' => $sentimentAnalysis['sentiment_score'] ?? 0,
            'positive_percentage' => $sentimentAnalysis['positive_percentage'] ?? 0,
            'negative_percentage' => $sentimentAnalysis['negative_percentage'] ?? 0,
            'neutral_percentage' => $sentimentAnalysis['neutral_percentage'] ?? 0,
            'total_comments' => $sentimentAnalysis['total_comments'] ?? 0,
            'common_themes' => $sentimentAnalysis['common_themes'] ?? [],
            'positive_comments' => $sentimentAnalysis['positive_comments'] ?? [],
            'negative_comments' => $sentimentAnalysis['negative_comments'] ?? [],
            'neutral_comments' => $sentimentAnalysis['neutral_comments'] ?? [],
            'what_if_optimistic' => $whatIfAnalysis['optimistic'] ?? [],
            'what_if_targeted' => $whatIfAnalysis['targeted'] ?? [],
        ];
    }
    
    public function getAllInsights(Evaluation $evaluation): array
    {
        $allInsights = [];
        
        $overall = $this->getInsights($evaluation, null);
        if ($overall) {
            $allInsights['overall'] = $overall;
        }
        
        $dates = $this->getAvailableDates($evaluation);
        foreach ($dates as $date) {
            $insight = $this->getInsights($evaluation, $date);
            if ($insight) {
                $allInsights[$date] = $insight;
            }
        }
        
        return $allInsights;
    }
    
    public function getResponseRateForDate(Evaluation $evaluation, ?string $eventDate = null): float
    {
        $normalizedDate = $eventDate ? $this->normalizeDate($eventDate) : null;
        
        if ($normalizedDate) {
            $totalEligible = $this->getTotalEligibleForDate($evaluation, $normalizedDate);
            $totalResponses = EvaluationResponse::where('evaluation_id', $evaluation->id)
                ->whereDate('event_date', $normalizedDate)
                ->count();
        } else {
            $totalEligible = $this->getTotalUniqueEligible($evaluation);
            $totalResponses = $evaluation->total_responses;
        }
        
        if ($totalEligible === 0) return 1;
        
        return round($totalResponses / $totalEligible, 2);
    }
    
    public function canGenerateInsights(Evaluation $evaluation, ?string $eventDate = null): bool
    {
        return $this->meetsThreshold($evaluation, $eventDate);
    }
}