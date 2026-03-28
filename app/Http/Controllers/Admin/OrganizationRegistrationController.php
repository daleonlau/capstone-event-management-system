<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\LogsActivity;
use App\Models\User;
use App\Models\OrganizationSetting;
use App\Models\OrganizationUser;
use App\Models\Department;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class OrganizationRegistrationController extends Controller
{
    use LogsActivity;

    public function create()
    {
        $departments = Department::with('courses')->get();
        
        return Inertia::render('Admin/Organizations/Register', [
            'departments' => $departments
        ]);
    }

    public function store(Request $request)
    {
        Log::info('=== ORGANIZATION REGISTRATION ===');
        Log::info('Request data:', $request->all());

        try {
            $validated = $request->validate([
                'organization_name' => 'required|string|max:255',
                'organization_email' => 'required|string|email|max:255|unique:users,email',
                'password' => 'required|string|min:6|confirmed',
                'assigned_departments' => 'required|array|min:1',
                'assigned_courses' => 'required|array|min:1',
                
                'president_name' => 'required|string|max:255',
                'president_email' => 'required|string|email|max:255|unique:organization_users,email',
                'president_password' => 'required|string|min:6|confirmed',
                
                'adviser_name' => 'required|string|max:255',
                'adviser_email' => 'required|string|email|max:255|unique:organization_users,email',
                'adviser_password' => 'required|string|min:6|confirmed',
                
                'treasurer_name' => 'required|string|max:255',
                'treasurer_email' => 'required|string|email|max:255|unique:organization_users,email',
                'treasurer_password' => 'required|string|min:6|confirmed',
            ]);

            DB::beginTransaction();

            // Create Organization (User with role 'organization')
            $organization = User::create([
                'name' => $request->organization_name,
                'email' => $request->organization_email,
                'password' => Hash::make($request->password),
                'role' => 'organization',
            ]);

            // Create Organization Settings
            OrganizationSetting::create([
                'organization_id' => $organization->id,
                'assigned_departments' => $request->assigned_departments,
                'assigned_courses' => $request->assigned_courses,
            ]);

            // Create President Account
            $president = OrganizationUser::create([
                'organization_id' => $organization->id,
                'name' => $request->president_name,
                'email' => $request->president_email,
                'password' => Hash::make($request->president_password),
                'role' => 'president',
            ]);

            // Create Adviser Account
            $adviser = OrganizationUser::create([
                'organization_id' => $organization->id,
                'name' => $request->adviser_name,
                'email' => $request->adviser_email,
                'password' => Hash::make($request->adviser_password),
                'role' => 'adviser',
            ]);

            // Create Treasurer Account
            $treasurer = OrganizationUser::create([
                'organization_id' => $organization->id,
                'name' => $request->treasurer_name,
                'email' => $request->treasurer_email,
                'password' => Hash::make($request->treasurer_password),
                'role' => 'treasurer',
            ]);

            DB::commit();

            // Log the action
            $this->logAction(
                'create_organization',
                "Created new organization: {$organization->name} with president: {$president->name}, adviser: {$adviser->name}, treasurer: {$treasurer->name}",
                [
                    'organization_id' => $organization->id,
                    'organization_name' => $organization->name,
                    'organization_email' => $organization->email,
                    'president_id' => $president->id,
                    'president_name' => $president->name,
                    'adviser_id' => $adviser->id,
                    'adviser_name' => $adviser->name,
                    'treasurer_id' => $treasurer->id,
                    'treasurer_name' => $treasurer->name,
                    'assigned_departments' => $request->assigned_departments,
                    'assigned_courses' => $request->assigned_courses,
                ]
            );

            return redirect()->route('admin.organizations.index')
                ->with('success', 'Organization and all accounts created successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Validation failed: ' . json_encode($e->errors()));
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Registration failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Registration failed: ' . $e->getMessage()])->withInput();
        }
    }

    public function edit(User $organization)
    {
        if ($organization->role !== 'organization') {
            abort(404);
        }

        $organization->load(['organizationSettings', 'organizationUsers']);
        $departments = Department::with('courses')->get();

        return Inertia::render('Admin/Organizations/Edit', [
            'organization' => [
                'id' => $organization->id,
                'name' => $organization->name,
                'email' => $organization->email,
                'assigned_departments' => $organization->organizationSettings->assigned_departments ?? [],
                'assigned_courses' => $organization->organizationSettings->assigned_courses ?? [],
            ],
            'departments' => $departments,
        ]);
    }

    public function update(Request $request, User $organization)
    {
        if ($organization->role !== 'organization') {
            return response()->json(['error' => 'Invalid organization'], 400);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $organization->id,
            'assigned_departments' => 'sometimes|array|min:1',
            'assigned_courses' => 'sometimes|array|min:1',
        ]);

        try {
            DB::beginTransaction();

            $oldName = $organization->name;
            $oldEmail = $organization->email;
            
            if (isset($validated['name']) || isset($validated['email'])) {
                $organization->update([
                    'name' => $validated['name'] ?? $organization->name,
                    'email' => $validated['email'] ?? $organization->email,
                ]);
            }

            $oldDepartments = null;
            $oldCourses = null;
            
            if (isset($validated['assigned_departments']) || isset($validated['assigned_courses'])) {
                $settings = $organization->organizationSettings;
                if ($settings) {
                    $oldDepartments = $settings->assigned_departments;
                    $oldCourses = $settings->assigned_courses;
                    
                    $settings->update([
                        'assigned_departments' => $validated['assigned_departments'] ?? $settings->assigned_departments,
                        'assigned_courses' => $validated['assigned_courses'] ?? $settings->assigned_courses,
                    ]);
                }
            }

            DB::commit();

            // Log changes
            $changes = [];
            if ($organization->name != $oldName) $changes[] = "name: {$oldName} → {$organization->name}";
            if ($organization->email != $oldEmail) $changes[] = "email: {$oldEmail} → {$organization->email}";
            if ($oldDepartments && $settings && $settings->assigned_departments != $oldDepartments) $changes[] = "departments updated";
            if ($oldCourses && $settings && $settings->assigned_courses != $oldCourses) $changes[] = "courses updated";
            
            if (!empty($changes)) {
                $this->logAction(
                    'update_organization',
                    "Updated organization: {$organization->name} - Changes: " . implode(', ', $changes),
                    [
                        'organization_id' => $organization->id,
                        'organization_name' => $organization->name,
                        'old_data' => ['name' => $oldName, 'email' => $oldEmail],
                        'new_data' => ['name' => $organization->name, 'email' => $organization->email],
                    ]
                );
            }

            return redirect()->route('admin.organizations.show', $organization->id)
                ->with('success', 'Organization updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update organization: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to update organization: ' . $e->getMessage()]);
        }
    }

    public function getOrganizationMembers(User $organization)
    {
        if ($organization->role !== 'organization') {
            return response()->json(['error' => 'Invalid organization'], 400);
        }

        $members = OrganizationUser::where('organization_id', $organization->id)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'is_blocked' => $user->isBlocked(),
                    'blocked_at' => $user->blocked_at,
                    'created_at' => $user->created_at->format('Y-m-d'),
                ];
            });

        return response()->json($members);
    }

    public function getOrganizationSettings(User $organization)
    {
        if ($organization->role !== 'organization') {
            return response()->json(['error' => 'Invalid organization'], 400);
        }

        $settings = $organization->organizationSettings;
        $departments = Department::whereIn('id', $settings->assigned_departments ?? [])->get();
        $courses = Course::whereIn('id', $settings->assigned_courses ?? [])->get();

        return response()->json([
            'departments' => $departments,
            'courses' => $courses,
        ]);
    }
}