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
        'predicted_satisfaction',
        'success_probability',
        'critical_factors',
        'category_breakdown',
        'response_rate',
        'total_respondents',
        'analyzed_at',
    ];
    
    protected $casts = [
        'strengths' => 'array',
        'weaknesses' => 'array',
        'recommendations' => 'array',
        'critical_factors' => 'array',
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