<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AIAnalysis extends Model
{
    protected $table = 'ai_analyses';
    
    protected $fillable = [
        'evaluation_id',
        'summary',
        'strengths',
        'weaknesses',
        'recommendations',
        'feature_importance',
        'sentiment_analysis',
        'what_if_analysis',
        'predicted_satisfaction',
        'success_probability',
        'category_breakdown',
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
        'predicted_satisfaction' => 'float',
        'success_probability' => 'float',
        'response_rate' => 'float',
        'total_respondents' => 'integer',
        'analyzed_at' => 'datetime',
    ];
    
    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(Evaluation::class);
    }
}