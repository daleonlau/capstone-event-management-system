<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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
                return redirect('/admin/dashboard');
            }
            
            Auth::logout();
            return back()->withErrors(['email' => 'Invalid role']);
        }

        // Try organization user login
        if (Auth::guard('org_user')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::guard('org_user')->user();
            
            session()->forget('url.intended');
            session()->forget('_previous');
            
            session(['org_user_id' => $user->id]);
            session(['user_role' => $user->role]);
            
            Log::info('Organization user login', [
                'role' => $user->role, 
                'email' => $user->email,
                'id' => $user->id
            ]);
            
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

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        Auth::guard('org_user')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
}