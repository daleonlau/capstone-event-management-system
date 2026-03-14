<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrganizationUserMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        Log::info('OrganizationUserMiddleware', [
            'check' => Auth::guard('org_user')->check(),
            'roles_required' => $roles
        ]);

        if (!Auth::guard('org_user')->check()) {
            return redirect()->route('login');
        }

        $user = Auth::guard('org_user')->user();
        
        if (!empty($roles) && !in_array($user->role, $roles)) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}