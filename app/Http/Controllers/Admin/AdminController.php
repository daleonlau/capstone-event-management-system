<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\LogsActivity;
use App\Models\User;
use App\Models\OrganizationUser;
use App\Models\OrganizationSetting;
use App\Models\Department;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class AdminController extends Controller
{
    use LogsActivity;

    public function indexOrganizations()
    {
        $organizations = User::where('role', 'organization')
            ->with(['organizationSettings', 'organizationUsers'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($org) {
                return [
                    'id' => $org->id,
                    'name' => $org->name,
                    'email' => $org->email,
                    'members_count' => $org->organizationUsers->count(),
                    'departments_count' => $org->organizationSettings ? count($org->organizationSettings->assigned_departments ?? []) : 0,
                    'courses_count' => $org->organizationSettings ? count($org->organizationSettings->assigned_courses ?? []) : 0,
                    'created_at' => $org->created_at->format('Y-m-d'),
                ];
            });

        return Inertia::render('Admin/Organizations/Index', [
            'organizations' => $organizations
        ]);
    }

    public function showOrganization(User $organization)
    {
        if ($organization->role !== 'organization') {
            abort(404);
        }

        $organization->load(['organizationSettings', 'organizationUsers']);
        
        $departments = Department::with('courses')->get();
        $courses = Course::all();

        $activityStats = [
            'total_events' => $organization->events()->count(),
            'total_students' => $organization->students()->count(),
            'total_evaluations' => \App\Models\Evaluation::where('organization_id', $organization->id)->count(),
        ];

        return Inertia::render('Admin/Organizations/Show', [
            'organization' => [
                'id' => $organization->id,
                'name' => $organization->name,
                'email' => $organization->email,
                'created_at' => $organization->created_at->format('Y-m-d H:i:s'),
                'organization_settings' => $organization->organizationSettings,
                'organization_users' => $organization->organizationUsers->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                        'blocked_at' => $user->blocked_at,
                        'is_blocked' => $user->isBlocked(),
                        'created_at' => $user->created_at->format('Y-m-d'),
                    ];
                }),
            ],
            'departments' => $departments,
            'courses' => $courses,
            'activityStats' => $activityStats,
        ]);
    }

    public function destroyOrganization(User $organization)
    {
        if ($organization->role !== 'organization') {
            return response()->json(['error' => 'Invalid organization'], 400);
        }

        try {
            DB::beginTransaction();
            
            $orgName = $organization->name;
            $orgId = $organization->id;
            
            OrganizationUser::where('organization_id', $organization->id)->delete();
            OrganizationSetting::where('organization_id', $organization->id)->delete();
            $organization->delete();
            
            DB::commit();

            // Log the action
            $this->logAction(
                'delete_organization',
                "Deleted organization: {$orgName}",
                [
                    'organization_id' => $orgId,
                    'organization_name' => $orgName,
                ]
            );

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete organization: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to delete organization'], 500);
        }
    }

    public function blockUser(OrganizationUser $user)
    {
        try {
            $userName = $user->name;
            $userEmail = $user->email;
            $userRole = $user->role;
            $orgId = $user->organization_id;
            
            $user->block('Blocked by admin');
            
            // Log the action
            $this->logAction(
                'block_user',
                "Blocked user: {$userName} ({$userEmail}) - {$userRole}",
                [
                    'user_id' => $user->id,
                    'user_name' => $userName,
                    'user_email' => $userEmail,
                    'user_role' => $userRole,
                    'organization_id' => $orgId,
                ]
            );
            
            return response()->json([
                'success' => true,
                'message' => 'User has been blocked successfully',
                'user' => [
                    'id' => $user->id,
                    'name' => $userName,
                    'is_blocked' => true,
                    'blocked_at' => $user->blocked_at,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to block user: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to block user'], 500);
        }
    }

    public function unblockUser(OrganizationUser $user)
    {
        try {
            $userName = $user->name;
            $userEmail = $user->email;
            $userRole = $user->role;
            $orgId = $user->organization_id;
            
            $user->unblock();
            
            // Log the action
            $this->logAction(
                'unblock_user',
                "Unblocked user: {$userName} ({$userEmail}) - {$userRole}",
                [
                    'user_id' => $user->id,
                    'user_name' => $userName,
                    'user_email' => $userEmail,
                    'user_role' => $userRole,
                    'organization_id' => $orgId,
                ]
            );
            
            return response()->json([
                'success' => true,
                'message' => 'User has been unblocked successfully',
                'user' => [
                    'id' => $user->id,
                    'name' => $userName,
                    'is_blocked' => false,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to unblock user: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to unblock user'], 500);
        }
    }

    public function addOrganizationMember(Request $request, User $organization)
    {
        if ($organization->role !== 'organization') {
            return response()->json(['error' => 'Invalid organization'], 400);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:organization_users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:president,adviser,treasurer',
        ]);

        try {
            $member = OrganizationUser::create([
                'organization_id' => $organization->id,
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
            ]);

            // Log the action
            $this->logAction(
                'add_organization_member',
                "Added new {$validated['role']}: {$validated['name']} ({$validated['email']}) to organization: {$organization->name}",
                [
                    'organization_id' => $organization->id,
                    'organization_name' => $organization->name,
                    'member_id' => $member->id,
                    'member_name' => $validated['name'],
                    'member_email' => $validated['email'],
                    'member_role' => $validated['role'],
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Member added successfully',
                'member' => [
                    'id' => $member->id,
                    'name' => $member->name,
                    'email' => $member->email,
                    'role' => $member->role,
                    'created_at' => $member->created_at->format('Y-m-d'),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to add member: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to add member'], 500);
        }
    }

    public function updateOrganizationMember(Request $request, OrganizationUser $user)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:organization_users,email,' . $user->id,
            'role' => 'sometimes|in:president,adviser,treasurer',
        ]);

        try {
            $oldName = $user->name;
            $oldEmail = $user->email;
            $oldRole = $user->role;
            
            $user->update($validated);
            
            $changes = [];
            if (isset($validated['name']) && $validated['name'] != $oldName) $changes['name'] = "{$oldName} → {$validated['name']}";
            if (isset($validated['email']) && $validated['email'] != $oldEmail) $changes['email'] = "{$oldEmail} → {$validated['email']}";
            if (isset($validated['role']) && $validated['role'] != $oldRole) $changes['role'] = "{$oldRole} → {$validated['role']}";

            // Log the action
            $this->logAction(
                'update_organization_member',
                "Updated member: {$oldName} - Changes: " . implode(', ', $changes),
                [
                    'user_id' => $user->id,
                    'old_data' => ['name' => $oldName, 'email' => $oldEmail, 'role' => $oldRole],
                    'new_data' => $validated,
                    'organization_id' => $user->organization_id,
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Member updated successfully',
                'member' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update member: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update member'], 500);
        }
    }

    public function deleteOrganizationMember(OrganizationUser $user)
    {
        try {
            $userName = $user->name;
            $userEmail = $user->email;
            $userRole = $user->role;
            $orgId = $user->organization_id;
            
            $user->delete();
            
            // Log the action
            $this->logAction(
                'delete_organization_member',
                "Removed member: {$userName} ({$userEmail}) - {$userRole} from organization",
                [
                    'user_id' => $user->id,
                    'user_name' => $userName,
                    'user_email' => $userEmail,
                    'user_role' => $userRole,
                    'organization_id' => $orgId,
                ]
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Member removed successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete member: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to delete member'], 500);
        }
    }

    public function resetMemberPassword(Request $request, OrganizationUser $user)
    {
        $validated = $request->validate([
            'password' => 'required|string|min:6',
        ]);

        try {
            $userName = $user->name;
            $userEmail = $user->email;
            
            $user->update([
                'password' => Hash::make($validated['password'])
            ]);

            // Log the action
            $this->logAction(
                'reset_password',
                "Reset password for user: {$userName} ({$userEmail})",
                [
                    'user_id' => $user->id,
                    'user_name' => $userName,
                    'user_email' => $userEmail,
                    'organization_id' => $user->organization_id,
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to reset password: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to reset password'], 500);
        }
    }

    public function profile()
    {
        return Inertia::render('Admin/Profile');
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        ]);

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'required|string|min:6|confirmed',
            ]);
            $validated['password'] = Hash::make($request->password);
        }

        $oldName = $user->name;
        $oldEmail = $user->email;
        
        $user->update($validated);

        // Log the action
        $changes = [];
        if ($user->name != $oldName) $changes[] = "name: {$oldName} → {$user->name}";
        if ($user->email != $oldEmail) $changes[] = "email: {$oldEmail} → {$user->email}";
        
        if (!empty($changes)) {
            $this->logAction(
                'update_profile',
                "Updated profile: " . implode(', ', $changes),
                [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'changes' => $changes,
                ]
            );
        }

        return redirect()->back()->with('success', 'Profile updated successfully');
    }
}