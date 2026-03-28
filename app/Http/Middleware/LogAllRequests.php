<?php

namespace App\Http\Middleware;

use App\Services\LogService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogAllRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        // Only log non-GET requests and specific GET requests that are important
        if ($request->method() !== 'GET' || $this->shouldLogGetRequest($request)) {
            $user = Auth::user() ?: Auth::guard('org_user')->user();
            
            $action = strtolower($request->method()) . '_' . str_replace('/', '_', trim($request->path(), '/'));
            $description = $request->method() . ' request to ' . $request->path();
            
            $details = [
                'response_status' => $response->status(),
                'response_size' => strlen($response->content()),
            ];
            
            // Log based on response status
            if ($response->status() >= 500) {
                LogService::error($action, $description . ' (ERROR)', null, $details);
            } elseif ($response->status() >= 400) {
                LogService::security($action, $description . ' (FAILED)', $user, $details);
            } else {
                LogService::action($action, $description, $user, $details);
            }
        }
        
        return $response;
    }
    
    /**
     * Determine if we should log this GET request
     */
    private function shouldLogGetRequest(Request $request)
    {
        // Log GET requests that are important (like viewing reports, exports, etc.)
        $importantPaths = [
            'admin/logs/export',
            'admin/reports',
            'admin/evaluations/*/qr',
            'president/events/*/guests',
            'treasurer/reports',
        ];
        
        foreach ($importantPaths as $pattern) {
            if ($request->is($pattern)) {
                return true;
            }
        }
        
        return false;
    }
}