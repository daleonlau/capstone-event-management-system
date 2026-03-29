<?php

namespace App\Services;

use App\Models\SystemLog;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Log;

class LogService
{
    /**
     * Valid user classes that can be used in logs
     */
    protected static $validUserClasses = [
        'App\Models\User',
        'App\Models\OrganizationUser',
    ];

    /**
     * Log an authentication event (login/logout)
     */
    public static function auth($action, $description, $user, $details = [])
    {
        return self::log('auth', $action, $description, $user, $details);
    }

    /**
     * Log a user action event
     */
    public static function action($action, $description, $user, $details = [])
    {
        return self::log('action', $action, $description, $user, $details);
    }

    /**
     * Log a system event (no user context)
     */
    public static function system($action, $description, $details = [])
    {
        return self::log('system', $action, $description, null, $details);
    }

    /**
     * Log an error event
     */
    public static function error($action, $description, $exception = null, $details = [])
    {
        $errorDetails = $details;
        if ($exception) {
            $errorDetails['exception'] = [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ];
        }
        
        return self::log('error', $action, $description, null, $errorDetails);
    }

    /**
     * Log a security event (failed logins, suspicious activity)
     */
    public static function security($action, $description, $user = null, $details = [])
    {
        return self::log('security', $action, $description, $user, $details);
    }

    /**
     * Get the correct class name for a user with validation
     */
    private static function getUserClass($user)
    {
        if (!$user) {
            return null;
        }
        
        $className = get_class($user);
        
        // Validate the class is one of our allowed user classes
        if (in_array($className, self::$validUserClasses)) {
            return $className;
        }
        
        // If it's not a valid class, try to map it
        $classLower = strtolower($className);
        
        if (str_contains($classLower, 'organizationuser') || str_contains($classLower, 'org_user')) {
            return 'App\Models\OrganizationUser';
        }
        
        if (str_contains($classLower, 'user')) {
            return 'App\Models\User';
        }
        
        // If we can't determine, return null (will be logged as system)
        Log::warning('Unknown user class encountered in logging', [
            'class' => $className,
            'user_id' => $user->id ?? null,
        ]);
        
        return null;
    }

    /**
     * Safely get the current authenticated user from any guard
     */
    private static function getCurrentUser()
    {
        try {
            // Check web guard (admin users)
            $webUser = auth()->user();
            if ($webUser) {
                return $webUser;
            }
            
            // Check org_user guard (president, adviser, treasurer)
            $orgUser = auth()->guard('org_user')->user();
            if ($orgUser) {
                return $orgUser;
            }
            
            return null;
        } catch (\Exception $e) {
            Log::warning('Failed to get current user for logging: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Sanitize request data to remove sensitive information
     */
    private static function sanitizeRequestData($data)
    {
        if (!is_array($data)) {
            return $data;
        }
        
        $sensitiveFields = [
            'password', 'password_confirmation', 'current_password', 
            'token', 'api_token', 'secret', 'key', 'credit_card'
        ];
        
        foreach ($sensitiveFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = '[REDACTED]';
            }
        }
        
        return $data;
    }

    /**
     * Main logging method
     */
    private static function log($logType, $action, $description, $user, $details = [])
    {
        try {
            // Prepare base log data
            $logData = [
                'log_type' => $logType,
                'action' => $action,
                'description' => $description,
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
                'created_at' => now(),
            ];
            
            // Prepare details with request information
            $requestDetails = [
                'request_url' => Request::fullUrl(),
                'request_method' => Request::method(),
            ];
            
            // Add request data for non-GET requests (excluding sensitive info)
            if (Request::method() !== 'GET' && !empty(Request::all())) {
                $requestData = Request::except(['password', 'password_confirmation', 'current_password', 'token']);
                if (!empty($requestData)) {
                    $requestDetails['request_data'] = self::sanitizeRequestData($requestData);
                }
            }
            
            // Merge all details
            $logData['details'] = array_merge($requestDetails, $details);
            
            // Handle user information
            $actualUser = $user;
            if (!$actualUser) {
                $actualUser = self::getCurrentUser();
            }
            
            if ($actualUser) {
                $userClass = self::getUserClass($actualUser);
                
                // Only add user info if we have a valid class
                if ($userClass && in_array($userClass, self::$validUserClasses)) {
                    $logData['causer_id'] = $actualUser->id;
                    $logData['causer_type'] = $userClass;
                } else {
                    // Log as system if user class is invalid
                    $logData['log_type'] = 'system';
                    $logData['description'] = '[User context lost] ' . $description;
                    Log::warning('Logging with invalid user class, falling back to system', [
                        'original_log_type' => $logType,
                        'original_action' => $action,
                        'user_class' => get_class($actualUser),
                        'user_id' => $actualUser->id,
                    ]);
                }
            }
            
            // Ensure we're not storing invalid causer_type
            if (isset($logData['causer_type']) && !in_array($logData['causer_type'], self::$validUserClasses)) {
                unset($logData['causer_id']);
                unset($logData['causer_type']);
            }
            
            // Create the log entry
            return SystemLog::create($logData);
            
        } catch (\Exception $e) {
            // Log to Laravel log for debugging
            Log::error('Failed to create system log: ' . $e->getMessage(), [
                'log_type' => $logType,
                'action' => $action,
                'description' => $description,
                'user_id' => $user ? $user->id : null,
                'user_class' => $user ? get_class($user) : null,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return null;
        }
    }

    /**
     * Get log counts for dashboard with error handling
     */
    public static function getLogStats()
    {
        try {
            return [
                'total_logs' => SystemLog::count(),
                'total_auth_logs' => SystemLog::where('log_type', 'auth')->count(),
                'total_action_logs' => SystemLog::where('log_type', 'action')->count(),
                'total_system_logs' => SystemLog::where('log_type', 'system')->count(),
                'total_error_logs' => SystemLog::where('log_type', 'error')->count(),
                'total_security_logs' => SystemLog::where('log_type', 'security')->count(),
                'today_logs' => SystemLog::whereDate('created_at', today())->count(),
                'week_logs' => SystemLog::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                'month_logs' => SystemLog::whereMonth('created_at', now()->month)->count(),
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get log stats: ' . $e->getMessage());
            return [
                'total_logs' => 0,
                'total_auth_logs' => 0,
                'total_action_logs' => 0,
                'total_system_logs' => 0,
                'total_error_logs' => 0,
                'total_security_logs' => 0,
                'today_logs' => 0,
                'week_logs' => 0,
                'month_logs' => 0,
            ];
        }
    }

    /**
     * Clean old logs (optional - for maintenance)
     */
    public static function cleanOldLogs($daysToKeep = 90)
    {
        try {
            $cutoffDate = now()->subDays($daysToKeep);
            $deleted = SystemLog::where('created_at', '<', $cutoffDate)->delete();
            
            Log::info('Old logs cleaned', [
                'days_kept' => $daysToKeep,
                'deleted_count' => $deleted,
            ]);
            
            return $deleted;
        } catch (\Exception $e) {
            Log::error('Failed to clean old logs: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get logs for a specific user
     */
    public static function getUserLogs($userId, $userType = null, $limit = 100)
    {
        try {
            $query = SystemLog::where('causer_id', $userId);
            
            if ($userType) {
                $query->where('causer_type', $userType);
            }
            
            return $query->orderBy('created_at', 'desc')->limit($limit)->get();
        } catch (\Exception $e) {
            Log::error('Failed to get user logs: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Get logs by IP address
     */
    public static function getLogsByIp($ipAddress, $limit = 100)
    {
        try {
            return SystemLog::where('ip_address', $ipAddress)
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        } catch (\Exception $e) {
            Log::error('Failed to get logs by IP: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Get log summary for a specific date range
     */
    public static function getLogSummary($startDate, $endDate)
    {
        try {
            $logs = SystemLog::whereBetween('created_at', [$startDate, $endDate])->get();
            
            return [
                'total' => $logs->count(),
                'by_type' => $logs->groupBy('log_type')->map->count(),
                'by_action' => $logs->groupBy('action')->map->count(),
                'unique_users' => $logs->whereNotNull('causer_id')->unique('causer_id')->count(),
                'unique_ips' => $logs->whereNotNull('ip_address')->unique('ip_address')->count(),
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get log summary: ' . $e->getMessage());
            return [
                'total' => 0,
                'by_type' => collect([]),
                'by_action' => collect([]),
                'unique_users' => 0,
                'unique_ips' => 0,
            ];
        }
    }
}