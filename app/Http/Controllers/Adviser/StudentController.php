<?php

namespace App\Http\Controllers\Adviser;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\OrganizationSetting;
use App\Models\Course;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class StudentController extends Controller
{
    protected $organizationId;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::guard('org_user')->user();
            if (!$user) {
                return redirect()->route('login');
            }
            $this->organizationId = $user->organization_id;
            return $next($request);
        });
    }

    /**
     * Display a listing of students (read-only for advisers)
     */
    public function index(Request $request)
    {
        $query = Student::where('user_id', $this->organizationId);

        // Search functionality
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('student_id', 'like', "%{$search}%")
                  ->orWhere('firstname', 'like', "%{$search}%")
                  ->orWhere('lastname', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('course', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%");
            });
        }

        // Filter by department
        if ($request->department) {
            $query->where('department', $request->department);
        }

        // Filter by year level
        if ($request->year_level) {
            $query->where('yearlevel', $request->year_level);
        }

        $students = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->through(function ($student) {
                return [
                    'student_id' => $student->student_id,
                    'firstname' => $student->firstname,
                    'lastname' => $student->lastname,
                    'email' => $student->email,
                    'course' => $student->course,
                    'department' => $student->department,
                    'yearlevel' => $student->yearlevel,
                ];
            });

        // Get unique departments for filter
        $departments = Student::where('user_id', $this->organizationId)
            ->whereNotNull('department')
            ->distinct()
            ->pluck('department');

        return Inertia::render('Adviser/Students/Index', [
            'students' => $students,
            'filters' => $request->only(['search', 'department', 'year_level']),
            'departments' => $departments,
            'yearLevels' => ['1st Year', '2nd Year', '3rd Year', '4th Year'],
        ]);
    }
}