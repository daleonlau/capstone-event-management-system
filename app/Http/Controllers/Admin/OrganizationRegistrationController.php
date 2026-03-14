<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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

            $organization = User::create([
                'name' => $request->organization_name,
                'email' => $request->organization_email,
                'password' => Hash::make($request->password),
                'role' => 'organization',
            ]);

            OrganizationSetting::create([
                'organization_id' => $organization->id,
                'assigned_departments' => $request->assigned_departments,
                'assigned_courses' => $request->assigned_courses,
            ]);

            OrganizationUser::create([
                'organization_id' => $organization->id,
                'name' => $request->president_name,
                'email' => $request->president_email,
                'password' => Hash::make($request->president_password),
                'role' => 'president',
            ]);

            OrganizationUser::create([
                'organization_id' => $organization->id,
                'name' => $request->adviser_name,
                'email' => $request->adviser_email,
                'password' => Hash::make($request->adviser_password),
                'role' => 'adviser',
            ]);

            OrganizationUser::create([
                'organization_id' => $organization->id,
                'name' => $request->treasurer_name,
                'email' => $request->treasurer_email,
                'password' => Hash::make($request->treasurer_password),
                'role' => 'treasurer',
            ]);

            DB::commit();

            return redirect()->route('admin.organizations.index')
                ->with('success', 'Organization and all accounts created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Registration failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Registration failed: ' . $e->getMessage()]);
        }
    }
}