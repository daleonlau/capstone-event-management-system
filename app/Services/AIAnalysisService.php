<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Evaluation;
use App\Models\EvaluationResponse;
use App\Models\EvaluationQuestion;
use App\Models\EvaluationCategory;
use App\Models\EventStudent;
use App\Models\AIAnalysis;
use Illuminate\Support\Facades\DB;

class AIAnalysisService
{
    protected string $apiUrl;
    protected int $timeout;
    
    // Threshold for analysis (75%)
    const RESPONSE_THRESHOLD = 0.75;
    
    public function __construct()
    {
        $this->apiUrl = env('AI_SERVICE_URL', 'http://127.0.0.1:8001');
        $this->timeout = env('AI_SERVICE_TIMEOUT', 30);
        
        Log::info('🔧 AIAnalysisService initialized', [
            'api_url' => $this->apiUrl,
            'timeout' => $this->timeout
        ]);
    }
    
    /**
     * Check if evaluation meets response threshold for analysis
     */
    public function meetsThreshold(Evaluation $evaluation): bool
    {
        // Get total eligible students for this event
        $event = $evaluation->event;
        $totalEligible = EventStudent::where('event_id', $event->id)->count();
        
        if ($totalEligible === 0) {
            return false;
        }
        
        $responseRate = $evaluation->total_responses / $totalEligible;
        
        Log::info('📊 Response rate check', [
            'evaluation_id' => $evaluation->id,
            'total_responses' => $evaluation->total_responses,
            'total_eligible' => $totalEligible,
            'response_rate' => $responseRate,
            'threshold' => self::RESPONSE_THRESHOLD,
            'meets_threshold' => $responseRate >= self::RESPONSE_THRESHOLD
        ]);
        
        return $responseRate >= self::RESPONSE_THRESHOLD;
    }
    
    /**
     * Analyze a single evaluation
     */
    public function analyzeEvaluation(Evaluation $evaluation, bool $force = false): ?array
    {
        Log::info('========== 🚀 AI ANALYSIS STARTED ==========', [
            'evaluation_id' => $evaluation->id,
            'force' => $force
        ]);
        
        // Check threshold unless forced
        if (!$force && !$this->meetsThreshold($evaluation)) {
            Log::info('⏸️ Evaluation does not meet response threshold', [
                'evaluation_id' => $evaluation->id
            ]);
            return null;
        }
        
        try {
            // Prepare evaluation data
            $evaluationData = $this->prepareEvaluationData($evaluation);
            
            if (!$evaluationData) {
                Log::error('❌ No evaluation data prepared');
                return null;
            }
            
            // Add metadata
            $event = $evaluation->event;
            $totalEligible = EventStudent::where('event_id', $event->id)->count();
            $evaluationData['total_respondents'] = $evaluation->total_responses;
            $evaluationData['response_rate'] = $totalEligible > 0 
                ? round($evaluation->total_responses / $totalEligible, 2) 
                : 0;
            
            Log::info('📤 Sending to AI service', [
                'url' => "{$this->apiUrl}/analyze",
                'data' => $evaluationData
            ]);
            
            $response = Http::timeout($this->timeout)
                ->post("{$this->apiUrl}/analyze", $evaluationData);
            
            if ($response->successful()) {
                $insights = $response->json();
                
                Log::info('✅ AI service response received', [
                    'insights' => $insights
                ]);
                
                $this->storeInsights($evaluation, $insights);
                
                Log::info('========== ✅ AI ANALYSIS COMPLETED ==========', [
                    'evaluation_id' => $evaluation->id,
                    'satisfaction' => $insights['predicted_satisfaction'] ?? 'N/A'
                ]);
                
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
        
        Log::info('========== ❌ AI ANALYSIS FAILED ==========');
        return null;
    }
    
    /**
     * Prepare evaluation data - DYNAMIC MAPPING with comments
     */
    protected function prepareEvaluationData(Evaluation $evaluation): ?array
    {
        $responses = EvaluationResponse::where('evaluation_id', $evaluation->id)->get();
        
        if ($responses->isEmpty()) {
            return null;
        }
        
        // Get all questions with their categories for this evaluation
        $questions = EvaluationQuestion::with('category')
            ->where('evaluation_id', $evaluation->id)
            ->get()
            ->keyBy('id');
        
        if ($questions->isEmpty()) {
            Log::warning('No questions found', ['evaluation_id' => $evaluation->id]);
            return null;
        }
        
        // Initialize ALL possible features with zeros
        $aggregated = [
            // Information Dissemination
            'info_timeliness' => 0,
            'info_adequacy' => 0,
            
            // Design of Activity
            'design_program' => 0,
            'design_relevance' => 0,
            'design_pacing' => 0,
            
            // Outcomes
            'outcomes_attendance' => 0,
            'outcomes_participation' => 0,
            'outcomes_interaction' => 0,
            'outcomes_teamwork' => 0,
            
            // Secretariat
            'secretariat_sensitivity' => 0,
            'secretariat_management' => 0,
            'secretariat_communication' => 0,
            
            // Facilities
            'facilities_appearance' => 0,
            'facilities_cleanliness' => 0,
            'facilities_equipment' => 0,
            
            // Food
            'food_quality' => 0,
            'food_presentation' => 0,
            'food_timeliness' => 0,
            'food_service' => 0,
            'food_sufficiency' => 0,
            'food_quantity' => 0,
        ];
        
        // Define keyword mapping for dynamic detection
        $keywordToFeature = [
            // Information Dissemination
            'timeliness' => 'info_timeliness',
            'sending invites' => 'info_timeliness',
            'adequacy' => 'info_adequacy',
            'information dissemination' => 'info_adequacy',
            
            // Design of Activity
            'program' => 'design_program',
            'order of activities' => 'design_program',
            'relevance' => 'design_relevance',
            'time allotment' => 'design_pacing',
            'pacing' => 'design_pacing',
            
            // Outcomes
            'attendance' => 'outcomes_attendance',
            'participation' => 'outcomes_participation',
            'interaction' => 'outcomes_interaction',
            'teamwork' => 'outcomes_teamwork',
            
            // Secretariat
            'sensitivity' => 'secretariat_sensitivity',
            'assistance' => 'secretariat_sensitivity',
            'management' => 'secretariat_management',
            'provision of information' => 'secretariat_communication',
            'feedback' => 'secretariat_communication',
            
            // Facilities
            'appearance' => 'facilities_appearance',
            'venue' => 'facilities_appearance',
            'cleanliness' => 'facilities_cleanliness',
            'orderliness' => 'facilities_cleanliness',
            'equipment' => 'facilities_equipment',
            'functionality' => 'facilities_equipment',
            
            // Food
            'quality of food' => 'food_quality',
            'food and beverages' => 'food_presentation',
            'presentation' => 'food_presentation',
            'timelines' => 'food_timeliness',
            'delivery' => 'food_timeliness',
            'service provided' => 'food_service',
            'sufficiency' => 'food_sufficiency',
            'quantity' => 'food_quantity',
        ];
        
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
            
            // Process ratings
            foreach ($likert as $questionId => $rating) {
                $questionId = (int)$questionId;
                $rating = (float)$rating;
                
                $question = $questions->get($questionId);
                
                if (!$question) {
                    continue;
                }
                
                $questionText = strtolower($question->question_text);
                
                // Find matching feature based on keywords
                $matchedFeature = null;
                foreach ($keywordToFeature as $keyword => $feature) {
                    if (strpos($questionText, strtolower($keyword)) !== false) {
                        $matchedFeature = $feature;
                        break;
                    }
                }
                
                if ($matchedFeature && isset($aggregated[$matchedFeature])) {
                    $aggregated[$matchedFeature] += $rating;
                }
            }
            
            // Process comments
            $comments = $response->comment_responses;
            if (is_string($comments)) {
                $comments = json_decode($comments, true);
            }
            
            if (is_array($comments)) {
                foreach ($comments as $comment) {
                    if (!empty($comment)) {
                        // Categorize based on comment type
                        if (strpos(strtolower($comment), 'suggest') !== false || 
                            strpos(strtolower($comment), 'recommend') !== false ||
                            strpos(strtolower($comment), 'improve') !== false) {
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
            return null;
        }
        
        // Calculate averages
        foreach ($aggregated as $key => $value) {
            if ($value > 0) {
                $aggregated[$key] = round($value / $responseCount, 2);
            }
        }
        
        // Add demographics
        $firstResponse = $responses->first();
        $aggregated['year_level'] = $this->mapYearLevel($firstResponse->year_level ?? '1st Year');
        $aggregated['respondent_type'] = 0;
        
        // Add comments for sentiment analysis
        $aggregated['positive_comments'] = $positiveComments;
        $aggregated['suggestion_comments'] = $suggestionComments;
        
        Log::info('✅ FINAL DATA TO AI SERVICE', [
            'evaluation_id' => $evaluation->id,
            'response_count' => $responseCount,
            'positive_comments' => count($positiveComments),
            'suggestion_comments' => count($suggestionComments)
        ]);
        
        return $aggregated;
    }
    
    /**
     * Map year level string to integer
     */
    protected function mapYearLevel(string $yearLevel): int
    {
        return match ($yearLevel) {
            '1st Year' => 1,
            '2nd Year' => 2,
            '3rd Year' => 3,
            '4th Year' => 4,
            default => 1,
        };
    }
    
    /**
     * Store AI insights in database
     */
    protected function storeInsights(Evaluation $evaluation, array $insights): void
    {
        Log::info('📝 Storing insights in database', [
            'evaluation_id' => $evaluation->id
        ]);
        
        try {
            $result = AIAnalysis::updateOrCreate(
                ['evaluation_id' => $evaluation->id],
                [
                    'summary' => $insights['summary'] ?? '',
                    'strengths' => json_encode($insights['strengths'] ?? []),
                    'weaknesses' => json_encode($insights['weaknesses'] ?? []),
                    'recommendations' => json_encode($insights['recommendations'] ?? []),
                    'feature_importance' => json_encode($insights['feature_importance'] ?? []),
                    'sentiment_analysis' => json_encode($insights['sentiment_analysis'] ?? []),
                    'what_if_analysis' => json_encode($insights['what_if_analysis'] ?? []),
                    'predicted_satisfaction' => $insights['predicted_satisfaction'] ?? 0,
                    'success_probability' => $insights['success_probability'] ?? 0,
                    'category_breakdown' => json_encode($insights['category_breakdown'] ?? []),
                    'response_rate' => $insights['response_rate'] ?? 0,
                    'total_respondents' => $insights['total_respondents'] ?? 0,
                    'analyzed_at' => now(),
                ]
            );
            
            Log::info('✅ Insights stored successfully', [
                'evaluation_id' => $evaluation->id,
                'analysis_id' => $result->id
            ]);
            
        } catch (\Exception $e) {
            Log::error('❌ Failed to store insights', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    
    /**
     * Get insights for an evaluation
     */
    public function getInsights(Evaluation $evaluation): ?array
    {
        $analysis = AIAnalysis::where('evaluation_id', $evaluation->id)->first();
        
        if (!$analysis) {
            return null;
        }
        
        return [
            'summary' => $analysis->summary,
            'strengths' => json_decode($analysis->strengths, true),
            'weaknesses' => json_decode($analysis->weaknesses, true),
            'recommendations' => json_decode($analysis->recommendations, true),
            'feature_importance' => json_decode($analysis->feature_importance, true),
            'sentiment_analysis' => json_decode($analysis->sentiment_analysis, true),
            'what_if_analysis' => json_decode($analysis->what_if_analysis, true),
            'predicted_satisfaction' => $analysis->predicted_satisfaction,
            'success_probability' => $analysis->success_probability,
            'category_breakdown' => json_decode($analysis->category_breakdown, true),
            'response_rate' => $analysis->response_rate,
            'total_respondents' => $analysis->total_respondents,
            'analyzed_at' => $analysis->analyzed_at,
        ];
    }
    
    /**
     * Check if analysis can be generated (meets threshold)
     */
    public function canGenerateInsights(Evaluation $evaluation): bool
    {
        return $this->meetsThreshold($evaluation);
    }
}