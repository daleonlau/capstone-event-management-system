<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Evaluation extends Model
{
    protected $fillable = [
        'event_id',
        'organization_id',
        'title',
        'form_type',
        'form_customizations',
        'event_dates',
        'form_number',
        'revision',
        'date_effectivity',
        'status',
        'available_from',
        'available_until',
        'qr_code_path',
        'qr_code_url',
        'total_responses',
        'report_generated_at',
        'report_sent_at',
        'report_path',
    ];

    protected $casts = [
        'available_from' => 'datetime',
        'available_until' => 'datetime',
        'form_customizations' => 'array',
        'event_dates' => 'array', // CRITICAL: This ensures JSON is cast to array
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organization_id');
    }

    public function categories(): HasMany
    {
        return $this->hasMany(EvaluationCategory::class)->orderBy('order');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(EvaluationQuestion::class)->orderBy('order');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(EvaluationResponse::class);
    }

    public function evaluationRequest()
    {
        return $this->hasOne(EvaluationRequest::class);
    }

    public function canGenerateQR(): bool
    {
        return $this->status === 'draft';
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && 
               (!$this->available_until || now() <= $this->available_until);
    }

    public function getLikertQuestions()
    {
        return $this->questions()->where('question_type', 'likert')->get();
    }

    public function getCommentQuestions()
    {
        return $this->questions()->where('question_type', 'comment')->get();
    }

    public function getQuestionsByCategory()
    {
        $result = [];
        foreach ($this->categories as $category) {
            $result[$category->category_name] = $this->questions()
                ->where('category_id', $category->id)
                ->where('question_type', 'likert')
                ->orderBy('order')
                ->get();
        }
        return $result;
    }

    public function getResponsesByDate($date = null)
    {
        $query = $this->responses();
        if ($date) {
            $query->where('event_date', $date);
        }
        return $query->get();
    }

    public function getDatesWithResponseCounts()
    {
        $dates = $this->event_dates ?: [];
        $result = [];
        
        foreach ($dates as $index => $date) {
            $count = $this->responses()->where('event_date', $date)->count();
            $result[] = [
                'date' => $date,
                'index' => $index + 1,
                'response_count' => $count,
                'formatted_date' => date('F d, Y', strtotime($date))
            ];
        }
        
        return $result;
    }
}