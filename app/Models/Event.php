<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_name',
        'event_date_start',
        'event_date_end',
        'payment',
        'event_fee',
        'departments',
        'courses',
        'year_levels',
        'status',
        'user_id',
        'event_type_id',
        'signed_document_path',
        'approval_status',
        'rejection_reason',
    ];

    protected $casts = [
        'departments' => 'array',
        'courses' => 'array',
        'year_levels' => 'array',
        'event_date_start' => 'date',
        'event_date_end' => 'date',
    ];

    /**
     * Get the creator/organization of the event
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the students associated with this event (pivot table)
     */
    public function students()
    {
        return $this->belongsToMany(Student::class, 'event_student', 'event_id', 'student_id')
                    ->using(EventStudent::class)
                    ->withPivot(['status', 'amount_paid', 'user_id'])
                    ->withTimestamps();
    }

    /**
     * Get the evaluations for this event
     */
    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class);
    }

    /**
     * Get the event type
     */
    public function eventType()
    {
        return $this->belongsTo(EventType::class);
    }

    /**
     * Get the approvals for this event
     */
    public function approvals()
    {
        return $this->hasMany(EventApproval::class);
    }

    /**
     * Check if event is approved by adviser
     */
    public function isApprovedByAdviser()
    {
        return $this->approvals()->where('role', 'adviser')->exists();
    }

    /**
     * Get the evaluation link for QR code
     */
    public function getEvaluationLinkAttribute()
    {
        return route('evaluations.form', ['event' => $this->id]);
    }

    /**
     * Check if event can be marked as finished
     */
    public function canBeFinished(): bool
    {
        return $this->approval_status === 'approved' && $this->status !== 'Finished';
    }

    /**
     * Check if event has active evaluation
     */
    public function hasActiveEvaluation(): bool
    {
        return $this->evaluations()->where('status', 'active')->exists();
    }

    /**
     * Get the active evaluation for this event
     */
    public function getActiveEvaluation()
    {
        return $this->evaluations()->where('status', 'active')->first();
    }

    /**
     * Get eligible departments for this event
     */
    public function getEligibleDepartments()
    {
        if (empty($this->departments)) {
            return collect();
        }
        
        return Department::whereIn('id', $this->departments)->get();
    }

    /**
     * Get eligible courses for this event
     */
    public function getEligibleCourses()
    {
        if (empty($this->courses)) {
            return collect();
        }
        
        return Course::whereIn('id', $this->courses)->get();
    }

    /**
     * Get eligible year levels for this event
     */
    public function getEligibleYearLevels()
    {
        return $this->year_levels ?? [];
    }

    /**
     * Check if a student is eligible for this event
     */
    public function isStudentEligible($studentId): bool
    {
        return EventStudent::where('event_id', $this->id)
            ->where('student_id', $studentId)
            ->exists();
    }

    /**
     * Get student's details if eligible
     */
    public function getEligibleStudent($studentId)
    {
        if (!$this->isStudentEligible($studentId)) {
            return null;
        }
        
        return Student::where('student_id', $studentId)->first();
    }

    /**
     * Get the total number of eligible students
     */
    public function getTotalEligibleStudentsAttribute(): int
    {
        return EventStudent::where('event_id', $this->id)->count();
    }

    /**
     * Get the number of paid students
     */
    public function getPaidCountAttribute(): int
    {
        return EventStudent::where('event_id', $this->id)
            ->where('status', 'Paid')
            ->count();
    }

    /**
     * Get the number of pending students
     */
    public function getPendingCountAttribute(): int
    {
        return EventStudent::where('event_id', $this->id)
            ->where('status', 'Pending')
            ->count();
    }

    /**
     * Get the total collected amount
     */
    public function getTotalCollectedAttribute(): float
    {
        return EventStudent::where('event_id', $this->id)
            ->where('status', 'Paid')
            ->sum('amount_paid');
    }

    /**
     * Get the collection progress percentage
     */
    public function getCollectionProgressAttribute(): float
    {
        $total = $this->total_eligible_students;
        if ($total === 0) return 0;
        return round(($this->paid_count / $total) * 100, 2);
    }
}