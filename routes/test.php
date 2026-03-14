<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/test-auth-simple', function(Request $request) {
    $output = [];
    
    // Check session
    $output['session_data'] = session()->all();
    
    // Check auth guards
    $output['auth_org_user_check'] = auth()->guard('org_user')->check();
    $output['auth_web_check'] = auth()->check();
    
    if (auth()->guard('org_user')->check()) {
        $user = auth()->guard('org_user')->user();
        $output['org_user'] = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ];
    }
    
    if (auth()->check()) {
        $user = auth()->user();
        $output['web_user'] = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ];
    }
    
    // Return as plain text for readability
    return response()->json($output, 200, [], JSON_PRETTY_PRINT);
});