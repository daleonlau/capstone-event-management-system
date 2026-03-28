<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AIAnalysis extends Model
{
    protected $table = 'ai_analyses';
    
    protected $fillable = [
        'evaluation_id',
        'event_date',
        'summary',
        'strengths',
        'weaknesses',
        'recommendations',
        'feature_importance',
        'sentiment_analysis',
        'what_if_analysis',
        'predicted_satisfaction',
        'success_probability',
        'critical_factors',
        'category_breakdown',
        'low_scoring_questions',
        'year_level_analysis',
        'response_rate',
        'total_respondents',
        'analyzed_at',
    ];
    
    protected $casts = [
        'strengths' => 'array',
        'weaknesses' => 'array',
        'recommendations' => 'array',
        'feature_importance' => 'array',
        'sentiment_analysis' => 'array',
        'what_if_analysis' => 'array',
        'category_breakdown' => 'array',
        'critical_factors' => 'array',
        'low_scoring_questions' => 'array',
        'year_level_analysis' => 'array',
        'predicted_satisfaction' => 'float',
        'success_probability' => 'float',
        'response_rate' => 'float',
        'total_respondents' => 'integer',
        'analyzed_at' => 'datetime',
        'event_date' => 'date',
    ];
    
    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(Evaluation::class);
    }
    
    public function scopeForDate($query, $eventDate = null)
    {
        if ($eventDate) {
            return $query->whereDate('event_date', $eventDate);
        }
        return $query->whereNull('event_date');
    }
}