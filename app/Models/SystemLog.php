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
     * The valid model types that can be used in the causer relationship
     */
    protected static $validCauserTypes = [
        'App\Models\User',
        'App\Models\OrganizationUser',
    ];

    /**
     * Get the causer (user) that performed the action.
     * This uses polymorphic relationship with error handling.
     */
    public function causer()
    {
        // Check if causer_type is valid
        if ($this->isValidCauserType()) {
            try {
                return $this->morphTo();
            } catch (\Exception $e) {
                Log::warning('Failed to load morph relationship for log: ' . $this->id, [
                    'error' => $e->getMessage(),
                    'causer_type' => $this->causer_type,
                ]);
                return null;
            }
        }
        
        return null;
    }

    /**
     * Check if the causer_type is valid
     */
    protected function isValidCauserType()
    {
        if (empty($this->causer_type)) {
            return false;
        }
        
        return in_array($this->causer_type, self::$validCauserTypes) && class_exists($this->causer_type);
    }

    /**
     * Get the causer model safely
     */
    public function getCauserModel()
    {
        if (!$this->isValidCauserType()) {
            return null;
        }

        try {
            // Try to load from relationship first
            if ($this->relationLoaded('causer')) {
                return $this->getRelation('causer');
            }
            
            // Manual load
            $model = new $this->causer_type;
            return $model->find($this->causer_id);
        } catch (\Exception $e) {
            Log::warning('Failed to load causer model: ' . $e->getMessage(), [
                'log_id' => $this->id,
                'causer_type' => $this->causer_type,
                'causer_id' => $this->causer_id,
            ]);
            return null;
        }
    }

    /**
     * Get causer name safely
     */
    public function getCauserNameAttribute()
    {
        try {
            $causer = $this->getCauserModel();
            if ($causer && isset($causer->name)) {
                return $causer->name;
            }
            if ($causer && isset($causer->first_name) && isset($causer->last_name)) {
                return $causer->first_name . ' ' . $causer->last_name;
            }
            return 'System';
        } catch (\Exception $e) {
            return 'System';
        }
    }

    /**
     * Get causer email safely
     */
    public function getCauserEmailAttribute()
    {
        try {
            $causer = $this->getCauserModel();
            if ($causer && isset($causer->email)) {
                return $causer->email;
            }
            return 'N/A';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    /**
     * Get causer role safely
     */
    public function getCauserRoleAttribute()
    {
        try {
            $causer = $this->getCauserModel();
            if (!$causer) {
                return 'system';
            }
            
            if ($this->causer_type === 'App\Models\User') {
                return $causer->role ?? 'admin';
            }
            
            if ($this->causer_type === 'App\Models\OrganizationUser') {
                return $causer->role ?? 'organization';
            }
            
            return 'user';
        } catch (\Exception $e) {
            return 'system';
        }
    }

    /**
     * Get causer type label
     */
    public function getCauserTypeLabelAttribute()
    {
        if (empty($this->causer_type)) {
            return 'System';
        }
        
        if ($this->causer_type === 'App\Models\User') {
            return 'Admin';
        }
        
        if ($this->causer_type === 'App\Models\OrganizationUser') {
            return 'Organization';
        }
        
        return 'Unknown';
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