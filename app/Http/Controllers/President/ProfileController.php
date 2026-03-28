<?php

namespace App\Http\Controllers\President;

use App\Http\Controllers\Controller;
use App\Services\LogService;
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
        
        return Inertia::render('President/Profile', [
            'user' => $user
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::guard('org_user')->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:organization_users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        try {
            // Store old data for logging
            $oldData = [
                'name' => $user->name,
                'email' => $user->email,
                'password_changed' => false,
            ];

            $changes = [];
            
            // Check if name changed
            if ($user->name !== $request->name) {
                $changes['name'] = [
                    'old' => $user->name,
                    'new' => $request->name
                ];
                $user->name = $request->name;
            }

            // Check if email changed
            if ($user->email !== $request->email) {
                $changes['email'] = [
                    'old' => $user->email,
                    'new' => $request->email
                ];
                $user->email = $request->email;
            }

            // Check if password changed
            $passwordChanged = false;
            if ($request->password) {
                $passwordChanged = true;
                $changes['password'] = 'changed';
                $user->password = Hash::make($request->password);
            }

            // Only save if there are changes
            if (!empty($changes)) {
                $user->save();
                
                // Log profile update
                $this->logAction('update_profile', 'Updated profile information', [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_email' => $user->email,
                    'user_role' => $user->role,
                    'changes' => $changes,
                    'password_changed' => $passwordChanged,
                ]);

                $message = 'Profile updated successfully.';
                if ($passwordChanged) {
                    $message .= ' Please use your new password next time you log in.';
                }
                
                return back()->with('success', $message);
            } else {
                // No changes made
                return back()->with('info', 'No changes were made to your profile.');
            }

        } catch (\Exception $e) {
            Log::error('Failed to update profile: ' . $e->getMessage());
            
            $this->logError('update_profile_failed', 'Failed to update profile: ' . $e->getMessage(), $e, [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
            ]);
            
            return back()->withErrors(['error' => 'Failed to update profile. Please try again.']);
        }
    }

    /**
     * Helper method to log profile actions
     */
    private function logAction($action, $description, $details = [])
    {
        try {
            $user = Auth::guard('org_user')->user();
            if ($user) {
                LogService::action($action, $description, $user, $details);
            }
        } catch (\Exception $e) {
            // Silent fail - don't let logging break the application
            Log::warning('Failed to log action: ' . $e->getMessage());
        }
    }
    
    /**
     * Helper method to log errors
     */
    private function logError($action, $description, $exception = null, $details = [])
    {
        try {
            LogService::error($action, $description, $exception, $details);
        } catch (\Exception $e) {
            // Silent fail
            Log::warning('Failed to log error: ' . $e->getMessage());
        }
    }
}