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
    protected DSSService $dssService;
    
    const RESPONSE_THRESHOLD = 0.75;
    
    public function __construct(DSSService $dssService)
    {
        $this->dssService = $dssService;
        $this->apiUrl = env('AI_SERVICE_URL', 'http://127.0.0.1:8001');
        $this->timeout = env('AI_SERVICE_TIMEOUT', 60);
        
        Log::info('AIAnalysisService initialized', [
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
        
        Log::info('Starting AI analysis', [
            'evaluation_id' => $evaluation->id,
            'event_date' => $dateLabel,
            'form_type' => $evaluation->form_type,
            'force' => $force
        ]);
        
        if (!$force && !$this->meetsThreshold($evaluation, $normalizedDate)) {
            Log::info('Evaluation does not meet response threshold', [
                'evaluation_id' => $evaluation->id,
                'event_date' => $dateLabel
            ]);
            return null;
        }
        
        // Step 1: Calculate DSS satisfaction from Likert responses
        $satisfaction = $this->dssService->calculateSatisfaction($evaluation, $normalizedDate);
        
        if (!$satisfaction['has_data']) {
            Log::warning('No data available for analysis', [
                'evaluation_id' => $evaluation->id,
                'event_date' => $dateLabel
            ]);
            return null;
        }
        
        // Step 2: Get comments for sentiment analysis
        $comments = $this->getComments($evaluation, $normalizedDate);
        Log::info('Retrieved comments for analysis', ['count' => count($comments)]);
        
        // Step 3: Analyze sentiment using Python service
        $sentiment = $this->analyzeSentiment($comments);
        
        // Step 4: Extract categorized comments - THESE ARE THE COMMENTS FROM PYTHON SERVICE
        $positiveComments = $sentiment['positive_comments'] ?? [];
        $negativeComments = $sentiment['negative_comments'] ?? [];
        $neutralComments = $sentiment['neutral_comments'] ?? [];
        
        Log::info('Comments extracted from sentiment analysis', [
            'positive_count' => count($positiveComments),
            'negative_count' => count($negativeComments),
            'neutral_count' => count($neutralComments),
            'sample_positive' => array_slice($positiveComments, 0, 2),
            'sample_negative' => array_slice($negativeComments, 0, 2),
            'sample_neutral' => array_slice($neutralComments, 0, 2),
        ]);
        
        // Step 5: Generate recommendations from DSS
        $recommendations = $this->dssService->generateRecommendations($satisfaction['category_scores']);
        
        // Step 6: Get strengths and weaknesses
        $strengths = $this->dssService->getStrengths($satisfaction['category_scores']);
        $weaknesses = $this->dssService->getWeaknesses($satisfaction['category_scores']);
        
        // Step 7: Generate what-if analysis
        $whatIfAnalysis = $this->generateWhatIfAnalysis($satisfaction);
        
        // Step 8: Generate summary
        $summary = $this->generateSummary($satisfaction, $sentiment);
        
        // Step 9: Calculate response rate
        $responseRate = $this->getResponseRateForDate($evaluation, $normalizedDate);
        
        // Step 10: Get low scoring questions
        $lowScoringQuestions = $this->getLowScoringQuestions($evaluation, $normalizedDate);
        
        // Step 11: Get year level analysis
        $yearLevelAnalysis = $this->getYearLevelAnalysis($evaluation, $normalizedDate);
        
        // Step 12: Store all results in database
        $this->storeResults(
            $evaluation, 
            $normalizedDate,
            $satisfaction, 
            $sentiment, 
            $recommendations, 
            $strengths, 
            $weaknesses, 
            $whatIfAnalysis, 
            $summary,
            $responseRate,
            $lowScoringQuestions,
            $yearLevelAnalysis,
            $positiveComments,
            $negativeComments,
            $neutralComments
        );
        
        // Return combined results
        return [
            'summary' => $summary,
            'strengths' => $strengths,
            'weaknesses' => $weaknesses,
            'recommendations' => $recommendations,
            'feature_importance' => $this->calculateFeatureImportance($satisfaction['category_scores']),
            'sentiment_analysis' => $sentiment,
            'what_if_analysis' => $whatIfAnalysis,
            'predicted_satisfaction' => $satisfaction['score'],
            'success_probability' => $satisfaction['success_probability'] / 100,
            'critical_factors' => $this->getCriticalFactors($satisfaction['category_scores']),
            'category_breakdown' => $satisfaction['category_scores'],
            'low_scoring_questions' => $lowScoringQuestions,
            'year_level_analysis' => $yearLevelAnalysis,
            'response_rate' => $responseRate,
            'total_respondents' => $satisfaction['total_respondents'],
            'analyzed_at' => now()->toISOString(),
            'event_date' => $normalizedDate,
            'positive_comments' => $positiveComments,
            'negative_comments' => $negativeComments,
            'neutral_comments' => $neutralComments,
        ];
    }
    
    private function getComments(Evaluation $evaluation, ?string $eventDate = null): array
    {
        $query = EvaluationResponse::where('evaluation_id', $evaluation->id);
        if ($eventDate) {
            $query->whereDate('event_date', $eventDate);
        }
        
        $responses = $query->get();
        $comments = [];
        
        foreach ($responses as $response) {
            $commentResponses = $response->comment_responses;
            if (is_string($commentResponses)) {
                $commentResponses = json_decode($commentResponses, true);
            }
            if (is_array($commentResponses)) {
                foreach ($commentResponses as $comment) {
                    if (!empty($comment) && is_string($comment) && strlen(trim($comment)) > 0) {
                        $comments[] = trim($comment);
                    }
                }
            }
        }
        
        return $comments;
    }
    private function analyzeSentiment(array $comments): array
{
    if (empty($comments)) {
        return [
            'positive_percentage' => 0,
            'negative_percentage' => 0,
            'neutral_percentage' => 0,
            'sentiment_score' => 0.5,
            'total_comments' => 0,
            'common_themes' => [],
            'positive_comments' => [],
            'negative_comments' => [],
            'neutral_comments' => [],
            'method_used' => 'none'
        ];
    }
    
    // Split comments into batches of 50 to avoid timeout
    $batchSize = 50;
    $batches = array_chunk($comments, $batchSize);
    $totalBatches = count($batches);
    
    Log::info('Starting batch sentiment analysis', [
        'total_comments' => count($comments),
        'batch_size' => $batchSize,
        'total_batches' => $totalBatches
    ]);
    
    $allPositiveComments = [];
    $allNegativeComments = [];
    $allNeutralComments = [];
    
    foreach ($batches as $batchIndex => $batch) {
        try {
            Log::info('Processing batch ' . ($batchIndex + 1) . ' of ' . $totalBatches, [
                'comments_in_batch' => count($batch)
            ]);
            
            $response = Http::timeout(120)->post("{$this->apiUrl}/analyze", [
                'positive_comments' => [],
                'suggestion_comments' => $batch,
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                $allPositiveComments = array_merge($allPositiveComments, $data['positive_comments'] ?? []);
                $allNegativeComments = array_merge($allNegativeComments, $data['negative_comments'] ?? []);
                $allNeutralComments = array_merge($allNeutralComments, $data['neutral_comments'] ?? []);
                
                Log::info('Batch ' . ($batchIndex + 1) . ' completed', [
                    'positive' => count($data['positive_comments'] ?? []),
                    'negative' => count($data['negative_comments'] ?? []),
                    'neutral' => count($data['neutral_comments'] ?? [])
                ]);
            } else {
                Log::warning('Batch ' . ($batchIndex + 1) . ' failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('Batch ' . ($batchIndex + 1) . ' error: ' . $e->getMessage());
        }
    }
    
    $total = count($comments);
    $posCount = count($allPositiveComments);
    $negCount = count($allNegativeComments);
    $neuCount = count($allNeutralComments);
    
    Log::info('Sentiment analysis completed', [
        'total_comments' => $total,
        'positive' => $posCount,
        'negative' => $negCount,
        'neutral' => $neuCount
    ]);
    
    return [
        'positive_percentage' => $total > 0 ? round(($posCount / $total) * 100, 1) : 0,
        'negative_percentage' => $total > 0 ? round(($negCount / $total) * 100, 1) : 0,
        'neutral_percentage' => $total > 0 ? round(($neuCount / $total) * 100, 1) : 0,
        'sentiment_score' => $total > 0 ? round(($posCount + ($neuCount * 0.5)) / $total, 2) : 0.5,
        'total_comments' => $total,
        'common_themes' => $this->extractCommonThemes($comments),
        'positive_comments' => $allPositiveComments,
        'negative_comments' => $allNegativeComments,
        'neutral_comments' => $allNeutralComments,
        'method_used' => 'XLM-RoBERTa (Batched)'
    ];
}
public function forceGenerateInsights(Evaluation $evaluation, ?string $eventDate = null): ?array
{
    return $this->analyzeEvaluation($evaluation, $eventDate, true);
}
    
    private function fallbackSentimentAnalysis(array $comments): array
    {
        $positiveKeywords = [
            'good', 'great', 'excellent', 'awesome', 'nice', 'love', 'perfect', 'fantastic',
            'wonderful', 'amazing', 'helpful', 'clear', 'organized', 'satisfied', 'happy',
            'enjoyed', 'informative', 'well', 'best', 'outstanding', 'impressive'
        ];
        
        $negativeKeywords = [
            'bad', 'poor', 'terrible', 'awful', 'worst', 'disappointed', 'waste', 'horrible',
            'slow', 'boring', 'confusing', 'unclear', 'disorganized', 'unhelpful', 'rude',
            'unsatisfied', 'lacking', 'insufficient', 'delay', 'problem', 'issue'
        ];
        
        $positiveComments = [];
        $negativeComments = [];
        $neutralComments = [];
        
        foreach ($comments as $comment) {
            $commentLower = strtolower($comment);
            $posCount = count(array_filter($positiveKeywords, fn($kw) => str_contains($commentLower, $kw)));
            $negCount = count(array_filter($negativeKeywords, fn($kw) => str_contains($commentLower, $kw)));
            
            if ($posCount > $negCount) {
                $positiveComments[] = $comment;
            } elseif ($negCount > $posCount) {
                $negativeComments[] = $comment;
            } else {
                $neutralComments[] = $comment;
            }
        }
        
        $total = count($comments);
        $posCount = count($positiveComments);
        $negCount = count($negativeComments);
        $neuCount = count($neutralComments);
        
        return [
            'positive_percentage' => $total > 0 ? round(($posCount / $total) * 100, 1) : 0,
            'negative_percentage' => $total > 0 ? round(($negCount / $total) * 100, 1) : 0,
            'neutral_percentage' => $total > 0 ? round(($neuCount / $total) * 100, 1) : 0,
            'sentiment_score' => $total > 0 ? round(($posCount + ($neuCount * 0.5)) / $total, 2) : 0.5,
            'total_comments' => $total,
            'common_themes' => $this->extractCommonThemes($comments),
            'positive_comments' => $positiveComments,
            'negative_comments' => $negativeComments,
            'neutral_comments' => $neutralComments,
            'method_used' => 'fallback_keyword'
        ];
    }
    
    private function extractCommonThemes(array $comments): array
    {
        $stopwords = ['the', 'and', 'is', 'in', 'to', 'of', 'it', 'that', 'was', 'for', 'this', 'but', 'with', 'as', 'are', 'be', 'at', 'from', 'by', 'an', 'on', 'have', 'has', 'were', 'had', 'been', 'not', 'very', 'so', 'a', 'i', 'we', 'they', 'he', 'she', 'you', 'also', 'event', 'program'];
        
        $allWords = [];
        foreach ($comments as $comment) {
            $words = str_word_count(strtolower($comment), 1);
            $words = array_filter($words, fn($w) => strlen($w) > 3 && !in_array($w, $stopwords));
            $allWords = array_merge($allWords, $words);
        }
        
        $frequencies = array_count_values($allWords);
        arsort($frequencies);
        
        return array_slice(array_keys($frequencies), 0, 10);
    }
    
    private function generateWhatIfAnalysis(array $satisfaction): array
    {
        $currentScore = $satisfaction['score'];
        
        $optimisticGain = 0;
        $targetedGain = 0;
        
        $lowCategories = array_filter($satisfaction['category_scores'], fn($s) => $s < 4.0);
        if (!empty($lowCategories)) {
            $optimisticGain = round(min(1.0, count($lowCategories) * 0.15), 2);
            $targetedGain = round(min(0.5, $optimisticGain / 2), 2);
        }
        
        $improvements = [];
        $categoriesToImprove = array_slice(array_keys($lowCategories), 0, 3);
        foreach ($categoriesToImprove as $category) {
            $current = $satisfaction['category_scores'][$category];
            $improvements[] = [
                'category' => $category,
                'from' => $current,
                'to' => min(4.5, round($current + 0.8, 1)),
                'gain' => round(min(0.5, 0.8 - ($current - 3.0)), 1)
            ];
        }
        
        return [
            'optimistic' => [
                'scenario' => 'Optimistic Scenario',
                'description' => 'Improving all low-scoring categories',
                'current_satisfaction' => $currentScore,
                'projected_satisfaction' => round(min(5.0, $currentScore + $optimisticGain), 2),
                'gain' => $optimisticGain
            ],
            'targeted' => [
                'scenario' => 'Targeted Improvements',
                'description' => 'Focusing on top 3 lowest-scoring categories',
                'current_satisfaction' => $currentScore,
                'projected_satisfaction' => round(min(5.0, $currentScore + $targetedGain), 2),
                'gain' => $targetedGain,
                'improvements' => $improvements
            ]
        ];
    }
    
    private function generateSummary(array $satisfaction, array $sentiment): string
    {
        $score = $satisfaction['score'];
        $respondents = $satisfaction['total_respondents'];
        $sentimentScore = $sentiment['sentiment_score'] ?? 0.5;
        
        $satisfactionText = '';
        if ($score >= 4.50) {
            $satisfactionText = "Outstanding event!";
        } elseif ($score >= 3.50) {
            $satisfactionText = "Very satisfactory event.";
        } elseif ($score >= 2.50) {
            $satisfactionText = "Satisfactory event.";
        } elseif ($score >= 1.50) {
            $satisfactionText = "Poor event satisfaction.";
        } else {
            $satisfactionText = "Very poor event satisfaction.";
        }
        
        $sentimentText = '';
        if ($sentimentScore >= 0.7) {
            $sentimentText = "Participants expressed predominantly positive feedback.";
        } elseif ($sentimentScore >= 0.4) {
            $sentimentText = "Participant feedback was mixed with some positive and negative comments.";
        } else {
            $sentimentText = "Participants expressed significant concerns in their feedback.";
        }
        
        return "{$satisfactionText} Overall satisfaction is {$score}/5.0 based on {$respondents} responses. {$sentimentText}";
    }
    
    private function calculateFeatureImportance(array $categoryScores): array
    {
        if (empty($categoryScores)) return [];
        
        $importance = [];
        foreach ($categoryScores as $category => $score) {
            if ($score < 2.50) {
                $importance[$category] = 30;
            } elseif ($score < 3.50) {
                $importance[$category] = 20;
            } elseif ($score < 4.50) {
                $importance[$category] = 10;
            } else {
                $importance[$category] = 5;
            }
        }
        
        $sum = array_sum($importance);
        if ($sum > 0) {
            foreach ($importance as $category => $value) {
                $importance[$category] = round(($value / $sum) * 100, 1);
            }
        }
        
        return $importance;
    }
    
    private function getCriticalFactors(array $categoryScores): array
    {
        $criticalFactors = [];
        foreach ($categoryScores as $category => $score) {
            if ($score < 2.50) {
                $criticalFactors[] = [
                    'category' => $category,
                    'score' => $score,
                    'impact' => 0.3,
                    'description' => "Critical issue that needs immediate attention",
                    'status' => 'critical'
                ];
            } elseif ($score < 3.50) {
                $criticalFactors[] = [
                    'category' => $category,
                    'score' => $score,
                    'impact' => 0.15,
                    'description' => "Needs improvement to meet expectations",
                    'status' => 'needs_improvement'
                ];
            }
        }
        return array_slice($criticalFactors, 0, 5);
    }
    
    private function getLowScoringQuestions(Evaluation $evaluation, ?string $eventDate = null): array
    {
        $query = EvaluationResponse::where('evaluation_id', $evaluation->id);
        if ($eventDate) {
            $query->whereDate('event_date', $eventDate);
        }
        $responses = $query->get();
        
        if ($responses->isEmpty()) {
            return [];
        }
        
        $questions = EvaluationQuestion::where('evaluation_id', $evaluation->id)
            ->with('category')
            ->where('question_type', 'likert')
            ->get()
            ->keyBy('id');
        
        $questionSums = [];
        $questionCounts = [];
        
        foreach ($responses as $response) {
            $likert = $response->likert_responses;
            if (is_string($likert)) {
                $likert = json_decode($likert, true);
            }
            if (!is_array($likert)) continue;
            
            foreach ($likert as $questionId => $rating) {
                if (!is_numeric($rating)) continue;
                
                if (!isset($questionSums[$questionId])) {
                    $questionSums[$questionId] = 0;
                    $questionCounts[$questionId] = 0;
                }
                $questionSums[$questionId] += $rating;
                $questionCounts[$questionId]++;
            }
        }
        
        $lowScoring = [];
        foreach ($questionSums as $questionId => $sum) {
            $count = $questionCounts[$questionId];
            $avg = $sum / $count;
            
            if ($avg < 3.50) {
                $question = $questions->get((int)$questionId);
                if ($question) {
                    $lowScoring[] = [
                        'question_id' => $questionId,
                        'question_text' => $question->question_text,
                        'category' => $question->category ? $question->category->category_name : 'General',
                        'average_rating' => round($avg, 2),
                        'priority_level' => $avg < 2.50 ? 'High' : ($avg < 3.00 ? 'Medium' : 'Low'),
                        'recommendation' => $this->getRecommendationForQuestion($question->question_text, $avg)
                    ];
                }
            }
        }
        
        usort($lowScoring, fn($a, $b) => $a['average_rating'] <=> $b['average_rating']);
        return array_slice($lowScoring, 0, 10);
    }
    
    private function getRecommendationForQuestion(string $questionText, float $avg): string
    {
        $lowerText = strtolower($questionText);
        
        if (str_contains($lowerText, 'food')) {
            return 'Improve food quality and increase portions';
        }
        if (str_contains($lowerText, 'invite')) {
            return 'Send invitations earlier and use multiple channels';
        }
        if (str_contains($lowerText, 'time')) {
            return 'Improve time management and start on time';
        }
        return 'Review and address this area for improvement';
    }
    
    private function getYearLevelAnalysis(Evaluation $evaluation, ?string $eventDate = null): array
    {
        $query = EvaluationResponse::where('evaluation_id', $evaluation->id);
        if ($eventDate) {
            $query->whereDate('event_date', $eventDate);
        }
        $responses = $query->get();
        
        if ($responses->isEmpty()) {
            return [];
        }
        
        $yearLevelData = [];
        foreach ($responses as $response) {
            $yearLevel = $response->year_level ?? 'Unknown';
            if (!isset($yearLevelData[$yearLevel])) {
                $yearLevelData[$yearLevel] = ['sum' => 0, 'count' => 0];
            }
            
            $likert = $response->likert_responses;
            if (is_string($likert)) {
                $likert = json_decode($likert, true);
            }
            if (is_array($likert)) {
                foreach ($likert as $rating) {
                    if (is_numeric($rating)) {
                        $yearLevelData[$yearLevel]['sum'] += $rating;
                        $yearLevelData[$yearLevel]['count']++;
                    }
                }
            }
        }
        
        $analysis = [];
        foreach ($yearLevelData as $yearLevel => $data) {
            $avg = $data['count'] > 0 ? $data['sum'] / $data['count'] : 0;
            $analysis[] = [
                'year_level' => $yearLevel,
                'average_satisfaction' => round($avg, 2),
                'respondent_count' => $data['count'],
                'status' => $avg >= 4.50 ? 'Very Satisfied' : ($avg >= 3.50 ? 'Satisfied' : ($avg >= 2.50 ? 'Neutral' : 'Dissatisfied'))
            ];
        }
        
        return $analysis;
    }
    
    private function storeResults(
        Evaluation $evaluation,
        ?string $eventDate,
        array $satisfaction,
        array $sentiment,
        array $recommendations,
        array $strengths,
        array $weaknesses,
        array $whatIfAnalysis,
        string $summary,
        float $responseRate,
        array $lowScoringQuestions,
        array $yearLevelAnalysis,
        array $positiveComments,
        array $negativeComments,
        array $neutralComments
    ): void {
        try {
            $featureImportance = $this->calculateFeatureImportance($satisfaction['category_scores']);
            $criticalFactors = $this->getCriticalFactors($satisfaction['category_scores']);
            
            // Convert comments to JSON - THIS IS THE CRITICAL PART
            $positiveCommentsJson = json_encode(array_values($positiveComments), JSON_UNESCAPED_UNICODE);
            $negativeCommentsJson = json_encode(array_values($negativeComments), JSON_UNESCAPED_UNICODE);
            $neutralCommentsJson = json_encode(array_values($neutralComments), JSON_UNESCAPED_UNICODE);
            
            // Check if columns exist
            $hasCommentColumns = Schema::hasColumn('ai_analyses', 'positive_comments');
            
            Log::info('Storing analysis results', [
                'evaluation_id' => $evaluation->id,
                'event_date' => $eventDate ?? 'overall',
                'has_comment_columns' => $hasCommentColumns,
                'positive_comments_count' => count($positiveComments),
                'negative_comments_count' => count($negativeComments),
                'neutral_comments_count' => count($neutralComments),
                'positive_json_preview' => substr($positiveCommentsJson, 0, 200),
            ]);
            
            $data = [
                'summary' => $summary,
                'strengths' => json_encode($strengths, JSON_UNESCAPED_UNICODE),
                'weaknesses' => json_encode($weaknesses, JSON_UNESCAPED_UNICODE),
                'recommendations' => json_encode($recommendations, JSON_UNESCAPED_UNICODE),
                'feature_importance' => json_encode($featureImportance, JSON_UNESCAPED_UNICODE),
                'sentiment_analysis' => json_encode($sentiment, JSON_UNESCAPED_UNICODE),
                'what_if_analysis' => json_encode($whatIfAnalysis, JSON_UNESCAPED_UNICODE),
                'predicted_satisfaction' => $satisfaction['score'],
                'success_probability' => $satisfaction['success_probability'] / 100,
                'critical_factors' => json_encode($criticalFactors, JSON_UNESCAPED_UNICODE),
                'category_breakdown' => json_encode($satisfaction['category_scores'], JSON_UNESCAPED_UNICODE),
                'low_scoring_questions' => json_encode($lowScoringQuestions, JSON_UNESCAPED_UNICODE),
                'year_level_analysis' => json_encode($yearLevelAnalysis, JSON_UNESCAPED_UNICODE),
                'response_rate' => $responseRate,
                'total_respondents' => $satisfaction['total_respondents'],
                'analyzed_at' => now(),
            ];
            
            // Only add comment columns if they exist
            if ($hasCommentColumns) {
                $data['positive_comments'] = $positiveCommentsJson;
                $data['negative_comments'] = $negativeCommentsJson;
                $data['neutral_comments'] = $neutralCommentsJson;
            } else {
                Log::warning('Comment columns do not exist in ai_analyses table. Run migration to add them.');
            }
            
            $result = AIAnalysis::updateOrCreate(
                [
                    'evaluation_id' => $evaluation->id,
                    'event_date' => $eventDate
                ],
                $data
            );
            
            // Verify the data was saved
            if ($hasCommentColumns) {
                $savedRecord = AIAnalysis::find($result->id);
                Log::info('Storage verification', [
                    'id' => $savedRecord->id,
                    'positive_comments_saved' => !is_null($savedRecord->positive_comments),
                    'positive_comments_length' => strlen($savedRecord->positive_comments ?? ''),
                    'positive_comments_sample' => substr($savedRecord->positive_comments ?? '', 0, 100),
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('Failed to store analysis results: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
        }
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
        
        return [
            'summary' => $analysis->summary,
            'strengths' => json_decode($analysis->strengths, true) ?: [],
            'weaknesses' => json_decode($analysis->weaknesses, true) ?: [],
            'recommendations' => json_decode($analysis->recommendations, true) ?: [],
            'feature_importance' => json_decode($analysis->feature_importance, true) ?: [],
            'sentiment_analysis' => json_decode($analysis->sentiment_analysis, true) ?: [],
            'positive_comments' => json_decode($analysis->positive_comments, true) ?: [],
            'negative_comments' => json_decode($analysis->negative_comments, true) ?: [],
            'neutral_comments' => json_decode($analysis->neutral_comments, true) ?: [],
            'what_if_analysis' => json_decode($analysis->what_if_analysis, true) ?: [],
            'predicted_satisfaction' => $analysis->predicted_satisfaction,
            'success_probability' => $analysis->success_probability,
            'critical_factors' => json_decode($analysis->critical_factors, true) ?: [],
            'category_breakdown' => json_decode($analysis->category_breakdown, true) ?: [],
            'low_scoring_questions' => json_decode($analysis->low_scoring_questions, true) ?: [],
            'year_level_analysis' => json_decode($analysis->year_level_analysis, true) ?: [],
            'response_rate' => $analysis->response_rate,
            'total_respondents' => $analysis->total_respondents,
            'analyzed_at' => $analysis->analyzed_at,
            'event_date' => $analysis->event_date
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
    
    public function canGenerateInsights(Evaluation $evaluation, ?string $eventDate = null): bool
    {
        return $this->meetsThreshold($evaluation, $eventDate);
    }
}