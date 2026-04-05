<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventStudent extends Model
{
    protected $table = 'event_student';
    
    protected $fillable = [
        'event_id',
        'student_id',
        'status',
        'amount_paid',
        'user_id',
        'receipt_number',
        'payment_method',
        'payment_notes',
        'receipt_pdf_path',
        'receipt_sent_at',
    ];

    protected $casts = [
        'receipt_sent_at' => 'datetime',
        'amount_paid' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public $timestamps = true;
    public $incrementing = false;
    protected $primaryKey = ['event_id', 'student_id'];

    /**
     * Set the keys for a save update query.
     * This is needed for composite keys.
     */
    protected function setKeysForSaveQuery($query)
    {
        $keys = $this->getKeyName();
        if (!is_array($keys)) {
            return parent::setKeysForSaveQuery($query);
        }

        foreach ($keys as $keyName) {
            $query->where($keyName, $this->getAttribute($keyName));
        }

        return $query;
    }

    /**
     * Get the primary key value.
     */
    protected function getKeyForSaveQuery()
    {
        $keys = $this->getKeyName();
        if (!is_array($keys)) {
            return parent::getKeyForSaveQuery();
        }

        // Return the first key value for compatibility
        return $this->getAttribute($keys[0]);
    }

    /**
     * Get the event that this student is associated with.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    /**
     * Get the student associated with this event registration.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    /**
     * Get the treasurer/organization user who processed this payment.
     */
    public function treasurer(): BelongsTo
    {
        return $this->belongsTo(OrganizationUser::class, 'user_id');
    }

    /**
     * Find a record by composite key.
     */
    public static function findByComposite($eventId, $studentId)
    {
        return self::where('event_id', $eventId)
            ->where('student_id', $studentId)
            ->first();
    }

    /**
     * Update or create a record with composite key.
     */
    public static function updateOrCreateComposite($eventId, $studentId, array $attributes = [])
    {
        $record = self::findByComposite($eventId, $studentId);
        
        if ($record) {
            $record->update($attributes);
            return $record;
        }
        
        return self::create(array_merge([
            'event_id' => $eventId,
            'student_id' => $studentId,
        ], $attributes));
    }

    /**
     * Check if the student has paid for this event.
     */
    public function isPaid(): bool
    {
        return $this->status === 'Paid';
    }

    /**
     * Check if the student is pending payment.
     */
    public function isPending(): bool
    {
        return $this->status === 'Pending';
    }

    /**
     * Mark this student as paid.
     */
    public function markAsPaid(float $amount, string $method = 'cash', ?string $notes = null): bool
    {
        $this->status = 'Paid';
        $this->amount_paid = $amount;
        $this->payment_method = $method;
        $this->payment_notes = $notes;
        
        return $this->save();
    }

    /**
     * Generate a unique receipt number.
     */
    public static function generateReceiptNumber(): string
    {
        $year = date('Y');
        $month = date('m');
        $prefix = "REC-{$year}{$month}-";
        
        $lastReceipt = self::where('receipt_number', 'like', $prefix . '%')
            ->orderBy('created_at', 'desc')
            ->first();
    
        if ($lastReceipt && $lastReceipt->receipt_number) {
            $lastNumber = intval(substr($lastReceipt->receipt_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
    
        return $prefix . $newNumber;
    }

    /**
     * Scope a query to only include paid students.
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'Paid');
    }

    /**
     * Scope a query to only include pending students.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    /**
     * Scope a query to only include students for a specific event.
     */
    public function scopeForEvent($query, $eventId)
    {
        return $query->where('event_id', $eventId);
    }

    /**
     * Scope a query to only include students for a specific organization.
     */
    public function scopeForOrganization($query, $organizationId)
    {
        return $query->where('user_id', $organizationId);
    }
}