<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluationRequest extends Model
{
    protected $fillable = [
        'event_id',
        'organization_id',
        'requested_by',
        'title',
        'activity_date',
        'venue',
        'speaker_name',
        'topics',
        'has_food',
        'status',
        'evaluation_id',
        'form_type',
        'notes',
    ];

    protected $casts = [
        'topics' => 'array',
        'activity_date' => 'date',
        'has_food' => 'boolean',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organization_id');
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(OrganizationUser::class, 'requested_by');
    }

    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(Evaluation::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function canCreateEvaluation(): bool
    {
        return $this->status === 'pending' && is_null($this->evaluation_id);
    }
}