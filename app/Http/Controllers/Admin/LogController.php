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
            // Safely get causer information
            $userName = 'System';
            $userEmail = 'N/A';
            $userRole = 'system';
            
            try {
                $causer = $log->causer;
                
                if ($causer) {
                    // Check if it's a User (admin) or OrganizationUser
                    $causerClass = get_class($causer);
                    
                    if ($causerClass === 'App\Models\User') {
                        $userName = $causer->name ?? 'Admin';
                        $userEmail = $causer->email ?? 'N/A';
                        $userRole = $causer->role ?? 'admin';
                    } elseif ($causerClass === 'App\Models\OrganizationUser') {
                        $userName = $causer->name ?? 'Organization User';
                        $userEmail = $causer->email ?? 'N/A';
                        $userRole = $causer->role ?? 'organization';
                    } else {
                        // Fallback for any other type
                        $userName = 'User';
                        $userEmail = $causer->email ?? 'N/A';
                        $userRole = 'user';
                    }
                }
            } catch (\Exception $e) {
                // If there's an error loading the causer, use system defaults
                \Log::warning('Failed to load causer for log ID: ' . $log->id, [
                    'error' => $e->getMessage(),
                    'causer_type' => $log->causer_type,
                    'causer_id' => $log->causer_id,
                ]);
                $userName = 'System';
                $userEmail = 'N/A';
                $userRole = 'system';
            }
            
            return [
                'id' => $log->id,
                'action' => $log->action,
                'description' => $log->description,
                'user_name' => $userName,
                'user_email' => $userEmail,
                'user_role' => $userRole,
                'ip_address' => $log->ip_address,
                'device' => $this->getDeviceInfo($log->user_agent),
                'created_at' => $log->created_at ? $log->created_at->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s'),
                'created_at_readable' => $log->created_at ? $log->created_at->diffForHumans() : 'Just now',
            ];
        });
        
        // Get statistics - with safe error handling
        try {
            $stats = [
                'total_sessions' => count($pairedSessions),
                'completed_sessions' => count(array_filter($pairedSessions, fn($s) => $s['status'] === 'completed')),
                'active_sessions' => count(array_filter($pairedSessions, fn($s) => $s['status'] === 'active')),
                'total_actions' => SystemLog::where('log_type', 'action')->count(),
                'total_logs' => SystemLog::count(),
                'unique_users' => SystemLog::whereNotNull('causer_id')->distinct('causer_id')->count('causer_id'),
                'today_logs' => SystemLog::whereDate('created_at', today())->count(),
            ];
        } catch (\Exception $e) {
            \Log::error('Failed to get log stats: ' . $e->getMessage());
            $stats = [
                'total_sessions' => count($pairedSessions),
                'completed_sessions' => count(array_filter($pairedSessions, fn($s) => $s['status'] === 'completed')),
                'active_sessions' => count(array_filter($pairedSessions, fn($s) => $s['status'] === 'active')),
                'total_actions' => 0,
                'total_logs' => 0,
                'unique_users' => 0,
                'today_logs' => 0,
            ];
        }
        
        // Get unique actions for filter dropdown - with safe error handling
        try {
            $actionTypes = SystemLog::where('log_type', 'action')
                ->distinct()
                ->pluck('action');
        } catch (\Exception $e) {
            $actionTypes = collect([]);
        }
        
        // Get unique users for filter dropdown - with safe error handling
        $users = collect([]);
        try {
            $users = SystemLog::with('causer')
                ->get()
                ->filter(function($log) {
                    try {
                        return $log->causer && $log->causer->name;
                    } catch (\Exception $e) {
                        return false;
                    }
                })
                ->pluck('causer.name')
                ->unique()
                ->values();
        } catch (\Exception $e) {
            \Log::warning('Failed to get unique users for filter: ' . $e->getMessage());
        }
        
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
            // Safely get user name
            $userName = 'System';
            $userEmail = 'N/A';
            
            try {
                $causer = $log->causer;
                if ($causer) {
                    $causerClass = get_class($causer);
                    if ($causerClass === 'App\Models\User' || $causerClass === 'App\Models\OrganizationUser') {
                        $userName = $causer->name ?? 'System';
                        $userEmail = $causer->email ?? 'N/A';
                    }
                }
            } catch (\Exception $e) {
                // If error, keep default values
            }
            
            $action = strtolower($log->action);
            
            // New user detected - handle any pending login
            if ($currentUser != $userName) {
                if ($loginEvent) {
                    $sessions[] = $this->createIncompleteSession($loginEvent, $sessionCount);
                    $sessionCount++;
                }
                $currentUser = $userName;
                $loginEvent = null;
            }
            
            // Check if it's a login event
            if (strpos($action, 'login') !== false && strpos($action, 'failed') === false) {
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
                        try {
                            $loginTime = Carbon::parse($loginEvent->created_at);
                            $logoutTime = Carbon::parse($log->created_at);
                            $diff = $loginTime->diff($logoutTime);
                            
                            $duration = '';
                            if ($diff->d > 0) $duration .= $diff->d . 'd ';
                            if ($diff->h > 0) $duration .= $diff->h . 'h ';
                            if ($diff->i > 0) $duration .= $diff->i . 'm ';
                            if ($diff->s > 0) $duration .= $diff->s . 's';
                            if (empty($duration)) $duration = '0s';
                        } catch (\Exception $e) {
                            $duration = 'N/A';
                        }
                    }
                    
                    $sessions[] = [
                        'session_id' => 'SESS-' . str_pad(++$sessionCount, 4, '0', STR_PAD_LEFT),
                        'user_name' => $userName,
                        'user_email' => $userEmail,
                        'login_time' => $loginEvent->created_at ? $loginEvent->created_at->format('Y-m-d H:i:s') : null,
                        'login_time_formatted' => $loginEvent->created_at ? $loginEvent->created_at->format('h:i:s A') : null,
                        'logout_time' => $log->created_at ? $log->created_at->format('Y-m-d H:i:s') : null,
                        'logout_time_formatted' => $log->created_at ? $log->created_at->format('h:i:s A') : null,
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
                        'user_email' => $userEmail,
                        'login_time' => null,
                        'login_time_formatted' => null,
                        'logout_time' => $log->created_at ? $log->created_at->format('Y-m-d H:i:s') : null,
                        'logout_time_formatted' => $log->created_at ? $log->created_at->format('h:i:s A') : null,
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
            $userName = 'System';
            $userEmail = 'N/A';
            
            try {
                $causer = $loginEvent->causer;
                if ($causer) {
                    $causerClass = get_class($causer);
                    if ($causerClass === 'App\Models\User' || $causerClass === 'App\Models\OrganizationUser') {
                        $userName = $causer->name ?? 'System';
                        $userEmail = $causer->email ?? 'N/A';
                    }
                }
            } catch (\Exception $e) {
                // Keep default values
            }
            
            $sessions[] = [
                'session_id' => 'SESS-' . str_pad(++$sessionCount, 4, '0', STR_PAD_LEFT),
                'user_name' => $userName,
                'user_email' => $userEmail,
                'login_time' => $loginEvent->created_at ? $loginEvent->created_at->format('Y-m-d H:i:s') : null,
                'login_time_formatted' => $loginEvent->created_at ? $loginEvent->created_at->format('h:i:s A') : null,
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
        $userName = 'System';
        $userEmail = 'N/A';
        
        try {
            $causer = $loginEvent->causer;
            if ($causer) {
                $causerClass = get_class($causer);
                if ($causerClass === 'App\Models\User' || $causerClass === 'App\Models\OrganizationUser') {
                    $userName = $causer->name ?? 'System';
                    $userEmail = $causer->email ?? 'N/A';
                }
            }
        } catch (\Exception $e) {
            // Keep default values
        }
        
        return [
            'session_id' => 'SESS-' . str_pad($sessionCount + 1, 4, '0', STR_PAD_LEFT),
            'user_name' => $userName,
            'user_email' => $userEmail,
            'login_time' => $loginEvent->created_at ? $loginEvent->created_at->format('Y-m-d H:i:s') : null,
            'login_time_formatted' => $loginEvent->created_at ? $loginEvent->created_at->format('h:i:s A') : null,
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
        if (empty($userAgent)) return 'Unknown';
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
                    $userName = 'System';
                    $userEmail = 'N/A';
                    
                    try {
                        $causer = $log->causer;
                        if ($causer) {
                            $causerClass = get_class($causer);
                            if ($causerClass === 'App\Models\User' || $causerClass === 'App\Models\OrganizationUser') {
                                $userName = $causer->name ?? 'System';
                                $userEmail = $causer->email ?? 'N/A';
                            }
                        }
                    } catch (\Exception $e) {
                        // Keep default values
                    }
                    
                    fputcsv($file, [
                        $userName,
                        $userEmail,
                        $log->action,
                        $log->description,
                        $log->ip_address,
                        $this->getDeviceInfo($log->user_agent),
                        $log->created_at ? $log->created_at->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s'),
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
        try {
            $logs = SystemLog::with('causer')
                ->where('log_type', 'auth')
                ->orderBy('created_at', 'desc')
                ->paginate(50);
            
            return response()->json($logs);
        } catch (\Exception $e) {
            \Log::error('Failed to get auth logs: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch logs'], 500);
        }
    }
    
    /**
     * Get action logs only (for AJAX requests)
     */
    public function getActionLogs(Request $request)
    {
        try {
            $logs = SystemLog::with('causer')
                ->where('log_type', 'action')
                ->orderBy('created_at', 'desc')
                ->paginate(50);
            
            return response()->json($logs);
        } catch (\Exception $e) {
            \Log::error('Failed to get action logs: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch logs'], 500);
        }
    }
}