<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Evaluation;
use App\Models\EvaluationResponse;
use App\Models\EvaluationQuestion;
use App\Models\EventStudent;
use App\Models\AIAnalysis;

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
    
    public function meetsThreshold(Evaluation $evaluation): bool
    {
        $event = $evaluation->event;
        $totalEligible = EventStudent::where('event_id', $event->id)->count();
        if ($totalEligible === 0) return false;
        
        $responseRate = $evaluation->total_responses / $totalEligible;
        return $responseRate >= self::RESPONSE_THRESHOLD;
    }
    
    public function analyzeEvaluation(Evaluation $evaluation, bool $force = false): ?array
    {
        Log::info('========== 🚀 AI ANALYSIS STARTED ==========', [
            'evaluation_id' => $evaluation->id,
            'form_type' => $evaluation->form_type,
            'total_responses' => $evaluation->total_responses,
            'force' => $force
        ]);
        
        if (!$force && !$this->meetsThreshold($evaluation)) {
            Log::info('⏸️ Evaluation does not meet response threshold', [
                'evaluation_id' => $evaluation->id
            ]);
            return null;
        }
        
        try {
            $evaluationData = $this->prepareEvaluationData($evaluation);
            
            if (!$evaluationData) {
                Log::error('❌ No evaluation data prepared');
                return null;
            }
            
            $event = $evaluation->event;
            $totalEligible = EventStudent::where('event_id', $event->id)->count();
            
            $payload = [
                'data' => $evaluationData['features'],
                'year_level' => 1,
                'respondent_type' => 0,
                'positive_comments' => $evaluationData['positive_comments'],
                'suggestion_comments' => $evaluationData['suggestion_comments'],
                'total_respondents' => $evaluation->total_responses,
                'response_rate' => $totalEligible > 0 ? round($evaluation->total_responses / $totalEligible, 2) : 0,
            ];
            
            Log::info('📤 SENDING TO AI SERVICE', [
                'evaluation_id' => $evaluation->id,
                'features_count' => count($evaluationData['features']),
                'positive_comments_count' => count($evaluationData['positive_comments']),
                'suggestion_comments_count' => count($evaluationData['suggestion_comments']),
                'sample_positive' => array_slice($evaluationData['positive_comments'], 0, 2),
                'sample_suggestion' => array_slice($evaluationData['suggestion_comments'], 0, 2)
            ]);
            
            $response = Http::timeout($this->timeout)->post("{$this->apiUrl}/analyze", $payload);
            
            if ($response->successful()) {
                $insights = $response->json();
                
                Log::info('✅ AI SERVICE RESPONSE RECEIVED', [
                    'evaluation_id' => $evaluation->id,
                    'satisfaction' => $insights['predicted_satisfaction'] ?? 'N/A',
                    'positive_percentage' => $insights['positive_percentage'] ?? 'N/A',
                    'negative_percentage' => $insights['negative_percentage'] ?? 'N/A',
                    'positive_comments_count' => count($insights['positive_comments'] ?? []),
                    'negative_comments_count' => count($insights['negative_comments'] ?? []),
                    'neutral_comments_count' => count($insights['neutral_comments'] ?? [])
                ]);
                
                $this->storeInsights($evaluation, $insights);
                
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
    
    protected function prepareEvaluationData(Evaluation $evaluation): ?array
    {
        Log::info('📊 PREPARING EVALUATION DATA', ['evaluation_id' => $evaluation->id]);
        
        $responses = EvaluationResponse::where('evaluation_id', $evaluation->id)->get();
        
        if ($responses->isEmpty()) {
            Log::warning('No responses found', ['evaluation_id' => $evaluation->id]);
            return null;
        }
        
        Log::info('📊 Found responses', ['count' => $responses->count()]);
        
        $questions = EvaluationQuestion::where('evaluation_id', $evaluation->id)
            ->where('question_type', 'likert')
            ->get()
            ->keyBy('id');
        
        if ($questions->isEmpty()) {
            Log::warning('No questions found', ['evaluation_id' => $evaluation->id]);
            return null;
        }
        
        Log::info('📊 Found questions', ['count' => $questions->count()]);
        
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
            
            // Extract comments
            $comments = $response->comment_responses;
            if (is_string($comments)) {
                $comments = json_decode($comments, true);
            }
            
            if (is_array($comments)) {
                foreach ($comments as $comment) {
                    if (!empty($comment) && is_string($comment)) {
                        $commentLower = strtolower($comment);
                        if (strpos($commentLower, 'suggest') !== false || 
                            strpos($commentLower, 'recommend') !== false ||
                            strpos($commentLower, 'improve') !== false ||
                            strpos($commentLower, 'better') !== false) {
                            $suggestionComments[] = $comment;
                        } else {
                            $positiveComments[] = $comment;
                        }
                    }
                }
            }
            
            $responseCount++;
        }
        
        if ($responseCount === 0) {
            Log::warning('No valid responses processed', ['evaluation_id' => $evaluation->id]);
            return null;
        }
        
        // Calculate averages
        foreach ($features as $key => $value) {
            if ($featureCounts[$key] > 0) {
                $features[$key] = round($value / $featureCounts[$key], 2);
            }
        }
        
        Log::info('📊 COMMENTS EXTRACTED', [
            'evaluation_id' => $evaluation->id,
            'positive_comments_count' => count($positiveComments),
            'suggestion_comments_count' => count($suggestionComments),
            'sample_positive' => array_slice($positiveComments, 0, 2),
            'sample_suggestion' => array_slice($suggestionComments, 0, 2)
        ]);
        
        return [
            'features' => $features,
            'form_type' => $evaluation->form_type,
            'year_level' => 1,
            'positive_comments' => $positiveComments,
            'suggestion_comments' => $suggestionComments,
        ];
    }
    
    protected function storeInsights(Evaluation $evaluation, array $insights): void
{
    Log::info('📝 STORING INSIGHTS IN DATABASE', [
        'evaluation_id' => $evaluation->id,
        'has_positive_comments' => isset($insights['positive_comments']),
        'positive_comments_count' => count($insights['positive_comments'] ?? []),
        'negative_comments_count' => count($insights['negative_comments'] ?? []),
        'neutral_comments_count' => count($insights['neutral_comments'] ?? [])
    ]);
    
    try {
        // Prepare sentiment analysis with all comment lists
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
        
        // Prepare low scoring questions
        $lowScoringQuestions = $insights['low_scoring_questions'] ?? [];
        
        // Prepare year level analysis
        $yearLevelAnalysis = $insights['year_level_analysis'] ?? [];
        
        // Prepare what-if analysis
        $whatIfAnalysis = [
            'optimistic' => $insights['what_if_optimistic'] ?? [],
            'targeted' => $insights['what_if_targeted'] ?? []
        ];
        
        $result = AIAnalysis::updateOrCreate(
            ['evaluation_id' => $evaluation->id],
            [
                'summary' => $insights['summary'] ?? '',
                'strengths' => json_encode($insights['strengths'] ?? [], JSON_UNESCAPED_UNICODE),
                'weaknesses' => json_encode($insights['weaknesses'] ?? [], JSON_UNESCAPED_UNICODE),
                'recommendations' => json_encode($insights['recommendations'] ?? [], JSON_UNESCAPED_UNICODE),
                'feature_importance' => json_encode($insights['feature_importance'] ?? [], JSON_UNESCAPED_UNICODE),
                'sentiment_analysis' => json_encode($sentimentAnalysis, JSON_UNESCAPED_UNICODE),
                'what_if_analysis' => json_encode($whatIfAnalysis, JSON_UNESCAPED_UNICODE),
                'predicted_satisfaction' => $insights['predicted_satisfaction'] ?? 0,
                'success_probability' => $insights['success_probability'] ?? 0,
                'category_breakdown' => json_encode($insights['category_breakdown'] ?? [], JSON_UNESCAPED_UNICODE),
                'response_rate' => $insights['response_rate'] ?? 0,
                'total_respondents' => $insights['total_respondents'] ?? 0,
                'low_scoring_questions' => json_encode($lowScoringQuestions, JSON_UNESCAPED_UNICODE),
                'year_level_analysis' => json_encode($yearLevelAnalysis, JSON_UNESCAPED_UNICODE),
                'analyzed_at' => now(),
            ]
        );
        
        Log::info('✅ INSIGHTS STORED SUCCESSFULLY', [
            'evaluation_id' => $evaluation->id,
            'analysis_id' => $result->id,
            'stored_positive_count' => count($sentimentAnalysis['positive_comments']),
            'stored_negative_count' => count($sentimentAnalysis['negative_comments']),
            'stored_neutral_count' => count($sentimentAnalysis['neutral_comments'])
        ]);
        
    } catch (\Exception $e) {
        Log::error('❌ FAILED TO STORE INSIGHTS', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
}
    public function getInsights(Evaluation $evaluation): ?array
    {
        $analysis = AIAnalysis::where('evaluation_id', $evaluation->id)->first();
        if (!$analysis) return null;
        
        $sentimentAnalysis = json_decode($analysis->sentiment_analysis, true);
        
        return [
            'summary' => $analysis->summary,
            'strengths' => json_decode($analysis->strengths, true),
            'weaknesses' => json_decode($analysis->weaknesses, true),
            'recommendations' => json_decode($analysis->recommendations, true),
            'feature_importance' => json_decode($analysis->feature_importance, true),
            'sentiment_analysis' => $sentimentAnalysis,
            'what_if_analysis' => json_decode($analysis->what_if_analysis, true),
            'predicted_satisfaction' => $analysis->predicted_satisfaction,
            'success_probability' => $analysis->success_probability,
            'category_breakdown' => json_decode($analysis->category_breakdown, true),
            'response_rate' => $analysis->response_rate,
            'total_respondents' => $analysis->total_respondents,
            'analyzed_at' => $analysis->analyzed_at,
            // Add these missing fields
            'sentiment_score' => $sentimentAnalysis['sentiment_score'] ?? 0,
            'positive_percentage' => $sentimentAnalysis['positive_percentage'] ?? 0,
            'negative_percentage' => $sentimentAnalysis['negative_percentage'] ?? 0,
            'neutral_percentage' => $sentimentAnalysis['neutral_percentage'] ?? 0,
            'total_comments' => $sentimentAnalysis['total_comments'] ?? 0,
            'common_themes' => $sentimentAnalysis['common_themes'] ?? [],
            'positive_comments' => $sentimentAnalysis['positive_comments'] ?? [],
            'negative_comments' => $sentimentAnalysis['negative_comments'] ?? [],
            'neutral_comments' => $sentimentAnalysis['neutral_comments'] ?? [],
            'low_scoring_questions' => $analysis->low_scoring_questions ? json_decode($analysis->low_scoring_questions, true) : [],
            'year_level_analysis' => $analysis->year_level_analysis ? json_decode($analysis->year_level_analysis, true) : [],
            'what_if_optimistic' => json_decode($analysis->what_if_analysis, true)['optimistic'] ?? [],
            'what_if_targeted' => json_decode($analysis->what_if_analysis, true)['targeted'] ?? [],
        ];
    }
    
    public function canGenerateInsights(Evaluation $evaluation): bool
    {
        return $this->meetsThreshold($evaluation);
    }
}