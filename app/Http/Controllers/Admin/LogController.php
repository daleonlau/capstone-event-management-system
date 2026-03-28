<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemLog;
use App\Services\LogService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class LogController extends Controller
{
    /**
     * Display logs page
     */
    public function index(Request $request)
    {
        // Get all auth logs for session pairing
        $authLogs = SystemLog::with('causer')
            ->where('log_type', 'auth')
            ->orderBy('created_at', 'asc')
            ->get();
        
        // Pair login/logout events into sessions
        $pairedSessions = $this->pairSessions($authLogs);
        
        // Apply filters to sessions
        if ($request->search) {
            $pairedSessions = array_filter($pairedSessions, function($session) use ($request) {
                return stripos($session['user_name'], $request->search) !== false ||
                       stripos($session['user_email'], $request->search) !== false ||
                       stripos($session['ip_address'], $request->search) !== false;
            });
        }
        
        if ($request->date_from) {
            $pairedSessions = array_filter($pairedSessions, function($session) use ($request) {
                $sessionDate = $session['login_time'] ?: $session['logout_time'];
                return $sessionDate && $sessionDate >= $request->date_from;
            });
        }
        
        if ($request->date_to) {
            $pairedSessions = array_filter($pairedSessions, function($session) use ($request) {
                $sessionDate = $session['login_time'] ?: $session['logout_time'];
                return $sessionDate && $sessionDate <= $request->date_to;
            });
        }
        
        if ($request->status) {
            $pairedSessions = array_filter($pairedSessions, function($session) use ($request) {
                return $session['status'] === $request->status;
            });
        }
        
        // Sort sessions by most recent
        usort($pairedSessions, function($a, $b) {
            $timeA = $a['logout_time'] ?? $a['login_time'] ?? '';
            $timeB = $b['logout_time'] ?? $b['login_time'] ?? '';
            return strtotime($timeB) - strtotime($timeA);
        });
        
        // Get action logs (excluding auth events)
        $actionQuery = SystemLog::with('causer')
            ->where('log_type', 'action')
            ->orderBy('created_at', 'desc');
        
        if ($request->search) {
            $actionQuery->where(function($q) use ($request) {
                $q->where('description', 'like', "%{$request->search}%")
                  ->orWhere('action', 'like', "%{$request->search}%")
                  ->orWhereHas('causer', function($sq) use ($request) {
                      $sq->where('name', 'like', "%{$request->search}%")
                         ->orWhere('email', 'like', "%{$request->search}%");
                  });
            });
        }
        
        if ($request->date_from) {
            $actionQuery->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->date_to) {
            $actionQuery->whereDate('created_at', '<=', $request->date_to);
        }
        
        if ($request->action_type) {
            $actionQuery->where('action', 'like', "%{$request->action_type}%");
        }
        
        $actionLogs = $actionQuery->get()->map(function($log) {
            return [
                'id' => $log->id,
                'action' => $log->action,
                'description' => $log->description,
                'user_name' => $log->causer ? $log->causer->name : 'System',
                'user_email' => $log->causer ? $log->causer->email : 'N/A',
                'user_role' => $log->causer && isset($log->causer->role) ? $log->causer->role : ($log->causer ? ($log->causer->role ?? 'user') : 'system'),
                'ip_address' => $log->ip_address,
                'device' => $this->getDeviceInfo($log->user_agent),
                'created_at' => $log->created_at->format('Y-m-d H:i:s'),
                'created_at_readable' => $log->created_at->diffForHumans(),
            ];
        });
        
        // Get statistics
        $stats = [
            'total_sessions' => count($pairedSessions),
            'completed_sessions' => count(array_filter($pairedSessions, fn($s) => $s['status'] === 'completed')),
            'active_sessions' => count(array_filter($pairedSessions, fn($s) => $s['status'] === 'active')),
            'total_actions' => SystemLog::where('log_type', 'action')->count(),
            'total_logs' => SystemLog::count(),
            'unique_users' => SystemLog::distinct('causer_id')->count('causer_id'),
            'today_logs' => SystemLog::whereDate('created_at', today())->count(),
        ];
        
        // Get unique actions for filter dropdown
        $actionTypes = SystemLog::where('log_type', 'action')
            ->distinct()
            ->pluck('action');
        
        // Get unique users for filter dropdown
        $users = SystemLog::with('causer')
            ->get()
            ->pluck('causer.name')
            ->filter()
            ->unique()
            ->values();
        
        return Inertia::render('Admin/Logs/Index', [
            'sessions' => $pairedSessions,
            'actionLogs' => $actionLogs,
            'stats' => $stats,
            'actionTypes' => $actionTypes,
            'users' => $users,
            'filters' => [
                'search' => $request->search,
                'date_from' => $request->date_from,
                'date_to' => $request->date_to,
                'status' => $request->status,
                'action_type' => $request->action_type,
            ],
        ]);
    }
    
    /**
     * Pair login and logout events into sessions
     */
    private function pairSessions($logs)
    {
        $sessions = [];
        $currentUser = '';
        $loginEvent = null;
        $sessionCount = 0;
        
        foreach ($logs as $log) {
            $userName = $log->causer ? $log->causer->name : 'System';
            $action = strtolower($log->action);
            
            // New user detected - handle any pending login
            if ($currentUser != $userName) {
                if ($loginEvent) {
                    // This case shouldn't happen normally, but handle it just in case
                    $sessions[] = $this->createIncompleteSession($loginEvent, $sessionCount);
                    $sessionCount++;
                }
                $currentUser = $userName;
                $loginEvent = null;
            }
            
            // Check if it's a login event
            if (strpos($action, 'login') !== false && strpos($action, 'failed') === false) {
                // If there's already a pending login, create an incomplete session
                if ($loginEvent) {
                    $sessions[] = $this->createIncompleteSession($loginEvent, $sessionCount);
                    $sessionCount++;
                }
                $loginEvent = $log;
            } 
            // Check if it's a logout event
            elseif (strpos($action, 'logout') !== false) {
                if ($loginEvent) {
                    // Calculate duration
                    $duration = null;
                    if ($loginEvent->created_at && $log->created_at) {
                        $loginTime = Carbon::parse($loginEvent->created_at);
                        $logoutTime = Carbon::parse($log->created_at);
                        $diff = $loginTime->diff($logoutTime);
                        
                        $duration = '';
                        if ($diff->d > 0) $duration .= $diff->d . 'd ';
                        if ($diff->h > 0) $duration .= $diff->h . 'h ';
                        if ($diff->i > 0) $duration .= $diff->i . 'm ';
                        if ($diff->s > 0) $duration .= $diff->s . 's';
                        if (empty($duration)) $duration = '0s';
                    }
                    
                    $sessions[] = [
                        'session_id' => 'SESS-' . str_pad(++$sessionCount, 4, '0', STR_PAD_LEFT),
                        'user_name' => $currentUser,
                        'user_email' => $loginEvent->causer ? $loginEvent->causer->email : 'N/A',
                        'login_time' => $loginEvent->created_at->format('Y-m-d H:i:s'),
                        'login_time_formatted' => $loginEvent->created_at->format('h:i:s A'),
                        'logout_time' => $log->created_at->format('Y-m-d H:i:s'),
                        'logout_time_formatted' => $log->created_at->format('h:i:s A'),
                        'ip_address' => $loginEvent->ip_address,
                        'device' => $this->getDeviceInfo($loginEvent->user_agent),
                        'duration' => $duration,
                        'status' => 'completed',
                    ];
                    $loginEvent = null;
                } else {
                    // Orphaned logout (no matching login)
                    $sessions[] = [
                        'session_id' => 'SESS-' . str_pad(++$sessionCount, 4, '0', STR_PAD_LEFT),
                        'user_name' => $userName,
                        'user_email' => $log->causer ? $log->causer->email : 'N/A',
                        'login_time' => null,
                        'login_time_formatted' => null,
                        'logout_time' => $log->created_at->format('Y-m-d H:i:s'),
                        'logout_time_formatted' => $log->created_at->format('h:i:s A'),
                        'ip_address' => $log->ip_address,
                        'device' => $this->getDeviceInfo($log->user_agent),
                        'duration' => null,
                        'status' => 'incomplete',
                    ];
                }
            }
        }
        
        // Add any remaining login event (active session)
        if ($loginEvent) {
            $sessions[] = [
                'session_id' => 'SESS-' . str_pad(++$sessionCount, 4, '0', STR_PAD_LEFT),
                'user_name' => $currentUser,
                'user_email' => $loginEvent->causer ? $loginEvent->causer->email : 'N/A',
                'login_time' => $loginEvent->created_at->format('Y-m-d H:i:s'),
                'login_time_formatted' => $loginEvent->created_at->format('h:i:s A'),
                'logout_time' => null,
                'logout_time_formatted' => null,
                'ip_address' => $loginEvent->ip_address,
                'device' => $this->getDeviceInfo($loginEvent->user_agent),
                'duration' => null,
                'status' => 'active',
            ];
        }
        
        return $sessions;
    }
    
    /**
     * Create an incomplete session array (for login without logout)
     */
    private function createIncompleteSession($loginEvent, $sessionCount)
    {
        return [
            'session_id' => 'SESS-' . str_pad($sessionCount + 1, 4, '0', STR_PAD_LEFT),
            'user_name' => $loginEvent->causer ? $loginEvent->causer->name : 'System',
            'user_email' => $loginEvent->causer ? $loginEvent->causer->email : 'N/A',
            'login_time' => $loginEvent->created_at->format('Y-m-d H:i:s'),
            'login_time_formatted' => $loginEvent->created_at->format('h:i:s A'),
            'logout_time' => null,
            'logout_time_formatted' => null,
            'ip_address' => $loginEvent->ip_address,
            'device' => $this->getDeviceInfo($loginEvent->user_agent),
            'duration' => null,
            'status' => 'incomplete',
        ];
    }
    
    /**
     * Get device info from user agent
     */
    private function getDeviceInfo($userAgent)
    {
        if (strpos($userAgent, 'Windows') !== false) return 'Windows';
        if (strpos($userAgent, 'Mac') !== false) return 'macOS';
        if (strpos($userAgent, 'Linux') !== false) return 'Linux';
        if (strpos($userAgent, 'iPhone') !== false || strpos($userAgent, 'Android') !== false) return 'Mobile';
        return 'Unknown';
    }
    
    /**
     * Export logs to CSV (Admin can export but NOT delete)
     */
    public function export(Request $request)
    {
        $type = $request->type ?? 'sessions';
        
        if ($type === 'sessions') {
            $authLogs = SystemLog::with('causer')
                ->where('log_type', 'auth')
                ->orderBy('created_at', 'asc')
                ->get();
            
            $sessions = $this->pairSessions($authLogs);
            
            $filename = 'user_sessions_' . date('Y-m-d_H-i-s') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ];
            
            $callback = function () use ($sessions) {
                $file = fopen('php://output', 'w');
                fwrite($file, "\xEF\xBB\xBF");
                
                fputcsv($file, [
                    'Session ID', 'User Name', 'User Email', 'Login Time', 'Logout Time', 
                    'Duration', 'IP Address', 'Device', 'Status'
                ]);
                
                foreach ($sessions as $session) {
                    fputcsv($file, [
                        $session['session_id'],
                        $session['user_name'],
                        $session['user_email'],
                        $session['login_time'] ?? 'N/A',
                        $session['logout_time'] ?? 'Still Active',
                        $session['duration'] ?? 'N/A',
                        $session['ip_address'],
                        $session['device'],
                        $session['status'],
                    ]);
                }
                
                fclose($file);
            };
            
            return response()->stream($callback, 200, $headers);
        } else {
            // Export actions
            $actions = SystemLog::with('causer')
                ->where('log_type', 'action')
                ->orderBy('created_at', 'desc')
                ->get();
            
            $filename = 'user_actions_' . date('Y-m-d_H-i-s') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ];
            
            $callback = function () use ($actions) {
                $file = fopen('php://output', 'w');
                fwrite($file, "\xEF\xBB\xBF");
                
                fputcsv($file, [
                    'User Name', 'User Email', 'Action', 'Description', 'IP Address', 'Device', 'Created At'
                ]);
                
                foreach ($actions as $log) {
                    fputcsv($file, [
                        $log->causer ? $log->causer->name : 'System',
                        $log->causer ? $log->causer->email : 'N/A',
                        $log->action,
                        $log->description,
                        $log->ip_address,
                        $this->getDeviceInfo($log->user_agent),
                        $log->created_at->format('Y-m-d H:i:s'),
                    ]);
                }
                
                fclose($file);
            };
            
            return response()->stream($callback, 200, $headers);
        }
    }
    
    /**
     * Clear logs is DISABLED for security - Admin cannot delete logs
     */
    public function clear(Request $request)
    {
        // Security: Admin cannot delete logs
        return response()->json([
            'success' => false,
            'message' => 'Log deletion is not allowed for security reasons. Logs are permanent for audit purposes.',
        ], 403);
    }
    
    /**
     * Get auth logs only (for AJAX requests)
     */
    public function getAuthLogs(Request $request)
    {
        $logs = SystemLog::with('causer')
            ->where('log_type', 'auth')
            ->orderBy('created_at', 'desc')
            ->paginate(50);
            
        return response()->json($logs);
    }
    
    /**
     * Get action logs only (for AJAX requests)
     */
    public function getActionLogs(Request $request)
    {
        $logs = SystemLog::with('causer')
            ->where('log_type', 'action')
            ->orderBy('created_at', 'desc')
            ->paginate(50);
            
        return response()->json($logs);
    }
}