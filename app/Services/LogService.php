<?php

namespace App\Services;

use App\Models\SystemLog;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Log;

class LogService
{
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
     * Main logging method
     */
    private static function log($logType, $action, $description, $user, $details = [])
    {
        try {
            $logData = [
                'log_type' => $logType,
                'action' => $action,
                'description' => $description,
                'details' => array_merge($details, [
                    'request_url' => Request::fullUrl(),
                    'request_method' => Request::method(),
                ]),
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
                'created_at' => now(),
            ];
            
            // Handle user - could be from web guard or org_user guard
            if ($user) {
                $logData['causer_id'] = $user->id;
                $logData['causer_type'] = get_class($user);
            } else {
                // Try to get authenticated user from any guard
                $webUser = auth()->user();
                $orgUser = auth()->guard('org_user')->user();
                
                if ($webUser) {
                    $logData['causer_id'] = $webUser->id;
                    $logData['causer_type'] = get_class($webUser);
                } elseif ($orgUser) {
                    $logData['causer_id'] = $orgUser->id;
                    $logData['causer_type'] = get_class($orgUser);
                }
            }
            
            // Add request data for non-GET requests (excluding sensitive info)
            if (Request::method() !== 'GET' && !empty(Request::all())) {
                $requestData = Request::except(['password', 'password_confirmation', 'current_password', 'token']);
                if (!empty($requestData)) {
                    $logData['details']['request_data'] = $requestData;
                }
            }
            
            return SystemLog::create($logData);
        } catch (\Exception $e) {
            \Log::error('Failed to create log: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get log counts for dashboard
     */
    public static function getLogStats()
    {
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
    }
}