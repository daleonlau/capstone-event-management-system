<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Student extends Model
{
    use HasFactory;

    protected $primaryKey = 'student_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'student_id',
        'firstname',
        'lastname',
        'email',
        'yearlevel',
        'course',
        'department',
        'user_id',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        
        static::created(function ($student) {
            Log::info('Student created - syncing with events', ['student_id' => $student->student_id]);
            $student->syncWithEvents();
        });
        
        static::updated(function ($student) {
            // Check if eligibility criteria changed
            if ($student->wasChanged('department') || 
                $student->wasChanged('course') || 
                $student->wasChanged('yearlevel')) {
                Log::info('Student details changed - re-syncing with events', [
                    'student_id' => $student->student_id,
                    'old_department' => $student->getOriginal('department'),
                    'new_department' => $student->department,
                ]);
                $student->syncWithEvents();
            }
        });
        
        static::deleted(function ($student) {
            Log::info('Student deleted - removing from all events', ['student_id' => $student->student_id]);
            EventStudent::where('student_id', $student->student_id)->delete();
        });
    }

    /**
     * Sync student with all eligible events
     */
    public function syncWithEvents()
    {
        // Get all events for this organization
        $events = Event::where('user_id', $this->user_id)->get();
        
        foreach ($events as $event) {
            // Check if student is eligible for this event
            $isEligible = true;
            
            if (!empty($event->departments) && !in_array($this->department, $event->departments)) {
                $isEligible = false;
            }
            
            if (!empty($event->courses) && !in_array($this->course, $event->courses)) {
                $isEligible = false;
            }
            
            if (!empty($event->year_levels) && !in_array($this->yearlevel, $event->year_levels)) {
                $isEligible = false;
            }
            
            if ($isEligible) {
                // Add or update student in event
                EventStudent::updateOrCreate(
                    [
                        'event_id' => $event->id,
                        'student_id' => $this->student_id,
                    ],
                    [
                        'user_id' => $this->user_id,
                        'status' => $event->payment === 'Payment' ? 'Pending' : 'Paid',
                        'amount_paid' => $event->payment === 'Payment' ? 0 : $event->event_fee,
                    ]
                );
            } else {
                // Remove student from event if exists
                EventStudent::where('event_id', $event->id)
                    ->where('student_id', $this->student_id)
                    ->delete();
            }
        }
    }

    public function organization()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_student', 'student_id', 'event_id')
                    ->using(EventStudent::class)
                    ->withPivot(['status', 'amount_paid', 'user_id'])
                    ->withTimestamps();
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class, 'student_id', 'student_id');
    }
}