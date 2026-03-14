<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluationResponse extends Model
{
    protected $fillable = [
        'evaluation_id',
        'event_id',
        'student_id',
        'email',
        'name',
        'department',
        'course',
        'year_level',
        'likert_responses',
        'comment_responses',
    ];

    protected $casts = [
        'likert_responses' => 'array',
        'comment_responses' => 'array',
    ];

    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(Evaluation::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function getRatingForQuestion($questionId)
    {
        return $this->likert_responses[$questionId] ?? null;
    }

    public function getCommentForQuestion($questionId)
    {
        return $this->comment_responses[$questionId] ?? null;
    }
}