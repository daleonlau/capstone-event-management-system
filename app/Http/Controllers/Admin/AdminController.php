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
use Inertia\Inertia;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalOrganizations = User::where('role', 'organization')->count();
        $totalOrganizationUsers = OrganizationUser::count();
        $totalEvents = \App\Models\Event::count();
        $pendingEvents = \App\Models\Event::where('approval_status', 'pending_approval')->count();

        return Inertia::render('Admin/Dashboard', [
            'totalOrganizations' => $totalOrganizations,
            'totalOrganizationUsers' => $totalOrganizationUsers,
            'totalEvents' => $totalEvents,
            'pendingEvents' => $pendingEvents,
        ]);
    }

    public function indexOrganizations()
    {
        $organizations = User::with(['organizationSettings', 'organizationUsers'])
            ->where('role', 'organization')
            ->get();

        return Inertia::render('Admin/Organizations', [
            'organizations' => $organizations,
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
        
        return Inertia::render('Admin/Organizations/Show', [
            'organization' => $organization,
            'departments' => $departments,
            'courses' => $courses
        ]);
    }

    public function destroyOrganization(User $organization)
    {
        if ($organization->role === 'organization') {
            $organization->delete();
        }

        return redirect()->back()->with('success', 'Organization deleted successfully!');
    }

    public function profile()
    {
        $admin = auth()->user();
        return Inertia::render('Admin/Profile', [
            'admin' => $admin
        ]);
    }

    public function updateProfile(Request $request)
    {
        $admin = auth()->user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $admin->id],
            'password' => ['nullable', 'confirmed', 'min:6'],
        ]);

        $admin->name = $request->name;
        $admin->email = $request->email;

        if ($request->password) {
            $admin->password = Hash::make($request->password);
        }

        $admin->save();

        return back()->with('success', 'Profile updated successfully.');
    }
}