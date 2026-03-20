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

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'event_student', 'event_id', 'student_id')
                    ->using(EventStudent::class)
                    ->withPivot(['status', 'amount_paid', 'user_id'])
                    ->withTimestamps();
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class);
    }

    public function eventType()
    {
        return $this->belongsTo(EventType::class);
    }

    public function approvals()
    {
        return $this->hasMany(EventApproval::class);
    }

    public function evaluationRequest()
    {
        return $this->hasOne(EvaluationRequest::class);
    }

    public function hasEvaluationRequest(): bool
    {
        return $this->evaluationRequest()->exists();
    }

    public function canRequestEvaluation(): bool
    {
        return $this->approval_status === 'approved' && 
               $this->status !== 'Finished' && 
               !$this->hasEvaluationRequest();
    }

    public function isApprovedByAdviser()
    {
        return $this->approvals()->where('role', 'adviser')->exists();
    }

    public function getEvaluationLinkAttribute()
    {
        return route('evaluations.form', ['event' => $this->id]);
    }

    public function canBeFinished(): bool
    {
        return $this->approval_status === 'approved' && $this->status !== 'Finished';
    }

    public function hasActiveEvaluation(): bool
    {
        return $this->evaluations()->where('status', 'active')->exists();
    }

    public function getActiveEvaluation()
    {
        return $this->evaluations()->where('status', 'active')->first();
    }

    public function getEligibleDepartments()
    {
        if (empty($this->departments)) {
            return collect();
        }
        
        return Department::whereIn('id', $this->departments)->get();
    }

    public function getEligibleCourses()
    {
        if (empty($this->courses)) {
            return collect();
        }
        
        return Course::whereIn('id', $this->courses)->get();
    }

    public function getEligibleYearLevels()
    {
        return $this->year_levels ?? [];
    }

    public function isStudentEligible($studentId): bool
    {
        return EventStudent::where('event_id', $this->id)
            ->where('student_id', $studentId)
            ->exists();
    }

    public function getEligibleStudent($studentId)
    {
        if (!$this->isStudentEligible($studentId)) {
            return null;
        }
        
        return Student::where('student_id', $studentId)->first();
    }

    public function getTotalEligibleStudentsAttribute(): int
    {
        return EventStudent::where('event_id', $this->id)->count();
    }

    public function getPaidCountAttribute(): int
    {
        return EventStudent::where('event_id', $this->id)
            ->where('status', 'Paid')
            ->count();
    }

    public function getPendingCountAttribute(): int
    {
        return EventStudent::where('event_id', $this->id)
            ->where('status', 'Pending')
            ->count();
    }

    public function getTotalCollectedAttribute(): float
    {
        return EventStudent::where('event_id', $this->id)
            ->where('status', 'Paid')
            ->sum('amount_paid');
    }

    public function getCollectionProgressAttribute(): float
    {
        $total = $this->total_eligible_students;
        if ($total === 0) return 0;
        return round(($this->paid_count / $total) * 100, 2);
    }
}