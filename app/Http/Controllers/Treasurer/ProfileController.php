<?php

namespace App\Http\Controllers\Treasurer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::guard('org_user')->user();
        
        return Inertia::render('Treasurer/Profile', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'created_at' => $user->created_at,
            ]
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::guard('org_user')->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:organization_users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        try {
            $user->name = $validated['name'];
            $user->email = $validated['email'];

            if (!empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }

            $user->save();

            Log::info('Treasurer profile updated', ['user_id' => $user->id]);

            return back()->with('success', 'Profile updated successfully.');

        } catch (\Exception $e) {
            Log::error('Profile update failed', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Failed to update profile.']);
        }
    }
}