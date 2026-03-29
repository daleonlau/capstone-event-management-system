<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class SystemLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'log_type',
        'action',
        'description',
        'causer_id',
        'causer_type',
        'details',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'details' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Get the causer (user) that performed the action.
     * This uses polymorphic relationship with error handling.
     */
    public function causer()
    {
        try {
            // Check if causer_type exists and is valid
            if ($this->causer_type && class_exists($this->causer_type)) {
                return $this->morphTo();
            }
            
            // If class doesn't exist, return null
            return null;
        } catch (\Exception $e) {
            // Log the error but don't break the application
            Log::warning('Failed to load causer for system log: ' . $e->getMessage(), [
                'log_id' => $this->id,
                'causer_type' => $this->causer_type,
                'causer_id' => $this->causer_id,
            ]);
            return null;
        }
    }

    /**
     * Safely get causer name
     */
    public function getCauserNameAttribute()
    {
        try {
            if ($this->causer) {
                return $this->causer->name ?? 'Unknown';
            }
            return 'System';
        } catch (\Exception $e) {
            return 'System';
        }
    }

    /**
     * Safely get causer email
     */
    public function getCauserEmailAttribute()
    {
        try {
            if ($this->causer) {
                return $this->causer->email ?? 'N/A';
            }
            return 'N/A';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    // Scopes for filtering
    public function scopeAuthLogs($query)
    {
        return $query->where('log_type', 'auth');
    }

    public function scopeActionLogs($query)
    {
        return $query->where('log_type', 'action');
    }

    public function scopeSystemLogs($query)
    {
        return $query->where('log_type', 'system');
    }

    public function scopeErrorLogs($query)
    {
        return $query->where('log_type', 'error');
    }

    public function scopeSecurityLogs($query)
    {
        return $query->where('log_type', 'security');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('causer_id', $userId);
    }

    public function scopeByAction($query, $action)
    {
        return $query->where('action', 'like', "%{$action}%");
    }

    public function scopeByIp($query, $ip)
    {
        return $query->where('ip_address', $ip);
    }
}