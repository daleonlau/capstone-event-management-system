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
        'form_number',
        'revision',
        'date_effectivity',
        'status',
        'available_from',
        'available_until',
        'qr_code_path',
        'qr_code_url',
        'total_responses',
    ];

    protected $casts = [
        'available_from' => 'datetime',
        'available_until' => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
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

    public function canGenerateQR(): bool
    {
        return $this->event->status === 'Finished' && $this->status === 'draft';
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
}