<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SentimentService
{
    protected string $apiUrl;
    protected int $timeout;
    
    public function __construct()
    {
        $this->apiUrl = env('AI_SERVICE_URL', 'http://127.0.0.1:8001');
        $this->timeout = env('AI_SERVICE_TIMEOUT', 60);
    }
    
    /**
     * Analyze sentiment of comments using Python AI service
     */
    public function analyze(array $comments): array
    {
        if (empty($comments)) {
            return $this->getEmptySentimentResult();
        }
        
        try {
            $response = Http::timeout($this->timeout)->post("{$this->apiUrl}/analyze", [
                'positive_comments' => $comments,
                'suggestion_comments' => [],
                'total_respondents' => count($comments),
                'response_rate' => 1.0,
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                return [
                    'positive_percentage' => $data['positive_percentage'] ?? 0,
                    'negative_percentage' => $data['negative_percentage'] ?? 0,
                    'neutral_percentage' => $data['neutral_percentage'] ?? 0,
                    'sentiment_score' => $data['sentiment_score'] ?? 0.5,
                    'total_comments' => $data['total_comments'] ?? 0,
                    'common_themes' => $data['common_themes'] ?? [],
                    'positive_comments' => $data['positive_comments'] ?? [],
                    'negative_comments' => $data['negative_comments'] ?? [],
                    'neutral_comments' => $data['neutral_comments'] ?? [],
                ];
            }
            
            Log::warning('Sentiment analysis failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Sentiment analysis exception: ' . $e->getMessage());
        }
        
        return $this->getFallbackSentimentResult($comments);
    }
    
    /**
     * Get empty sentiment result when no comments
     */
    protected function getEmptySentimentResult(): array
    {
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
        ];
    }
    
    /**
     * Fallback keyword-based sentiment analysis when AI service is unavailable
     */
    protected function getFallbackSentimentResult(array $comments): array
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
        ];
    }
    
    /**
     * Extract common themes from comments
     */
    protected function extractCommonThemes(array $comments): array
    {
        $stopwords = ['the', 'and', 'is', 'in', 'to', 'of', 'it', 'that', 'was', 'for', 'this', 'but', 'with', 'as', 'are', 'be', 'at', 'from', 'by', 'an', 'on', 'have', 'has', 'were', 'had', 'been', 'not', 'very', 'so', 'a', 'i', 'we', 'they', 'he', 'she', 'you', 'also'];
        
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
}