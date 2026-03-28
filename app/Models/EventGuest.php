<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventGuest extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'guest_id',
        'name',
        'email',
        'agency_office',
        'position',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Generate a unique guest ID
     */
    public static function generateGuestId($eventId)
    {
        $year = date('Y');
        $prefix = "GUEST-{$eventId}-{$year}-";
        
        $lastGuest = self::where('event_id', $eventId)
            ->where('guest_id', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastGuest) {
            $lastNumber = intval(substr($lastGuest->guest_id, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return $prefix . $newNumber;
    }

    /**
     * Find a guest by name for a specific event (case-insensitive)
     */
    public static function findByEventAndName($eventId, $name)
    {
        return self::where('event_id', $eventId)
            ->where('name', 'LIKE', $name)
            ->first();
    }

    /**
     * Check if guest has already submitted for a specific evaluation date
     */
    public function hasSubmittedForDate($evaluationId, $eventDate)
    {
        return EvaluationResponse::where('evaluation_id', $evaluationId)
            ->where('student_id', $this->guest_id)
            ->where('event_date', $eventDate)
            ->exists();
    }
}