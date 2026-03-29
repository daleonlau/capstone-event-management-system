<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
     * This uses polymorphic relationship.
     */
    public function causer()
    {
        return $this->morphTo();
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