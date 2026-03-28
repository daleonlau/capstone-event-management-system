<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return Inertia::render('Auth/Login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        Log::info('=== LOGIN ATTEMPT ===', ['email' => $request->email]);

        // Clear any existing intended URLs
        session()->forget('url.intended');
        session()->forget('_previous');

        // Try admin login first
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();
            
            Log::info('Admin login successful', ['role' => $user->role]);
            
            if ($user->role === 'admin') {
                // Log successful admin login
                LogService::auth(
                    'login',
                    "Admin {$user->name} ({$user->email}) logged in",
                    $user,
                    [
                        'role' => $user->role,
                        'ip' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                    ]
                );
                
                return redirect('/admin/dashboard');
            }
            
            Auth::logout();
            return back()->withErrors(['email' => 'Invalid role']);
        }

        // Try organization user login
        if (Auth::guard('org_user')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::guard('org_user')->user();
            
            // Check if user is blocked
            if ($user->isBlocked()) {
                Auth::guard('org_user')->logout();
                Log::warning('Blocked user attempted login', [
                    'email' => $user->email,
                    'role' => $user->role,
                    'ip' => $request->ip()
                ]);
                return back()->withErrors([
                    'email' => 'Your account has been blocked. Please contact the administrator.',
                ]);
            }
            
            session()->forget('url.intended');
            session()->forget('_previous');
            
            session(['org_user_id' => $user->id]);
            session(['user_role' => $user->role]);
            
            Log::info('Organization user login', [
                'role' => $user->role, 
                'email' => $user->email,
                'id' => $user->id
            ]);
            
            // Log successful organization user login
            LogService::auth(
                'login',
                "Organization user {$user->name} ({$user->email}) - {$user->role} logged in",
                $user,
                [
                    'role' => $user->role,
                    'organization_id' => $user->organization_id,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]
            );
            
            // IMPORTANT: Force redirect based on role
            switch ($user->role) {
                case 'president':
                    return redirect('/president/dashboard');
                case 'adviser':
                    return redirect('/adviser/dashboard');
                case 'treasurer':
                    return redirect('/treasurer/dashboard');
                default:
                    Auth::guard('org_user')->logout();
                    return back()->withErrors(['email' => 'Invalid role']);
            }
        }

        // Log failed login attempt
        LogService::auth(
            'login_failed',
            "Failed login attempt for email: {$request->email}",
            auth()->user() ?? (object)['id' => 0, 'name' => 'Guest', 'email' => $request->email],
            [
                'email' => $request->email,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]
        );

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function destroy(Request $request)
    {
        $user = Auth::user();
        $orgUser = Auth::guard('org_user')->user();
        
        // Log admin logout
        if ($user) {
            LogService::auth(
                'logout',
                "Admin {$user->name} ({$user->email}) logged out",
                $user,
                [
                    'role' => $user->role,
                    'ip' => $request->ip(),
                ]
            );
        }
        
        // Log organization user logout
        if ($orgUser) {
            LogService::auth(
                'logout',
                "Organization user {$orgUser->name} ({$orgUser->email}) - {$orgUser->role} logged out",
                $orgUser,
                [
                    'role' => $orgUser->role,
                    'organization_id' => $orgUser->organization_id,
                    'ip' => $request->ip(),
                ]
            );
        }
        
        Auth::logout();
        Auth::guard('org_user')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
}