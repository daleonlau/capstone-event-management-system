<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class OrganizationUserAuthController extends Controller
{
    public function showLoginForm()
    {
        return Inertia::render('Auth/OrganizationUserLogin');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('org_user')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::guard('org_user')->user();
            
            // Redirect based on role
            switch ($user->role) {
                case 'president':
                    return redirect()->intended('/president/dashboard');
                case 'adviser':
                    return redirect()->intended('/adviser/dashboard');
                case 'treasurer':
                    return redirect()->intended('/treasurer/dashboard');
                default:
                    Auth::guard('org_user')->logout();
                    return back()->withErrors([
                        'email' => 'Invalid role.',
                    ]);
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('org_user')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/org-user/login');
    }
}