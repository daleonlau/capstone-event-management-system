<?php

namespace App\Http\Controllers\President;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Event;
use App\Models\EventStudent;
use App\Models\OrganizationSetting;
use App\Models\Course;
use App\Models\Department;
use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use App\Imports\StudentsImport;
use Maatwebsite\Excel\Facades\Excel;

class StudentController extends Controller
{
    protected $organizationId;
    protected $settings;
    protected $allowedCourses = [];
    protected $allowedDepartments = [];

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::guard('org_user')->user();
            if (!$user) {
                return redirect()->route('login');
            }
            
            $this->organizationId = $user->organization_id;
            $this->settings = OrganizationSetting::where('organization_id', $this->organizationId)->first();
            
            // Get allowed courses based on organization settings
            if ($this->settings && $this->settings->assigned_courses) {
                $courseIds = $this->settings->assigned_courses;
                $this->allowedCourses = Course::whereIn('id', $courseIds)->pluck('name')->toArray();
            }
            
            // Get allowed departments based on organization settings
            if ($this->settings && $this->settings->assigned_departments) {
                $deptIds = $this->settings->assigned_departments;
                $this->allowedDepartments = Department::whereIn('id', $deptIds)->pluck('name')->toArray();
            }
            
            return $next($request);
        });
    }

    /**
     * Display a listing of students.
     */
    public function index(Request $request)
    {
        $query = Student::where('user_id', $this->organizationId);

        // Filter by assigned courses only
        if (!empty($this->allowedCourses)) {
            $query->whereIn('course', $this->allowedCourses);
        }

        // Filter by assigned departments only
        if (!empty($this->allowedDepartments)) {
            $query->whereIn('department', $this->allowedDepartments);
        }

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

        // Filter by course
        if ($request->course) {
            $query->where('course', $request->course);
        }

        // Filter by department
        if ($request->department) {
            $query->where('department', $request->department);
        }

        // Filter by year level
        if ($request->year_level) {
            $query->where('yearlevel', $request->year_level);
        }

        $students = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get courses for filter dropdown (with full details for display)
        $courses = [];
        if ($this->settings && $this->settings->assigned_courses) {
            $courses = Course::whereIn('id', $this->settings->assigned_courses)
                ->select('id', 'name', 'code')
                ->get();
        }

        // Get departments for filter dropdown (with full details for display)
        $departments = [];
        if ($this->settings && $this->settings->assigned_departments) {
            $departments = Department::whereIn('id', $this->settings->assigned_departments)
                ->select('id', 'name', 'code')
                ->get();
        }

        return Inertia::render('President/Students/Index', [
            'students' => $students,
            'filters' => $request->only(['search', 'course', 'department', 'year_level']),
            'courses' => $courses,
            'departments' => $departments,
            'yearLevels' => ['1st Year', '2nd Year', '3rd Year', '4th Year'],
            'allowedCourses' => $this->allowedCourses,
            'allowedDepartments' => $this->allowedDepartments,
        ]);
    }

    /**
     * Show the form for creating a new student.
     */
    public function create()
    {
        $courses = [];
        $departments = [];
        
        // Get full course details for dropdown
        if ($this->settings && $this->settings->assigned_courses) {
            $courses = Course::whereIn('id', $this->settings->assigned_courses)
                ->select('id', 'name', 'code')
                ->get();
        }
        
        // Get full department details for dropdown
        if ($this->settings && $this->settings->assigned_departments) {
            $departments = Department::whereIn('id', $this->settings->assigned_departments)
                ->select('id', 'name', 'code')
                ->get();
        }

        return Inertia::render('President/Students/Create', [
            'courses' => $courses,
            'departments' => $departments,
            'yearLevels' => ['1st Year', '2nd Year', '3rd Year', '4th Year'],
        ]);
    }

    /**
     * Store a newly created student.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|string|max:255|unique:students,student_id,NULL,id,user_id,' . $this->organizationId,
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'department' => [
                'required',
                'string',
                'max:255',
                Rule::in($this->allowedDepartments)
            ],
            'course' => [
                'required',
                'string',
                'max:255',
                Rule::in($this->allowedCourses)
            ],
            'yearlevel' => 'required|in:1st Year,2nd Year,3rd Year,4th Year',
        ]);

        try {
            $student = Student::create([
                'student_id' => $validated['student_id'],
                'firstname' => $validated['firstname'],
                'lastname' => $validated['lastname'],
                'email' => $validated['email'],
                'department' => $validated['department'],
                'course' => $validated['course'],
                'yearlevel' => $validated['yearlevel'],
                'user_id' => $this->organizationId,
            ]);

            // Sync student with all eligible events
            $this->syncStudentWithEvents($student);

            // Log student creation
            $this->logAction('create_student', 'Created student: ' . $student->firstname . ' ' . $student->lastname, [
                'student_id' => $student->student_id,
                'student_name' => $student->firstname . ' ' . $student->lastname,
                'student_email' => $student->email,
                'department' => $student->department,
                'course' => $student->course,
                'year_level' => $student->yearlevel,
            ]);

            return redirect()->route('president.students.index')
                ->with('success', 'Student added successfully and assigned to eligible events.');

        } catch (\Exception $e) {
            Log::error('Failed to create student: ' . $e->getMessage());
            
            $this->logError('create_student_failed', 'Failed to create student: ' . $e->getMessage(), $e, [
                'student_id' => $validated['student_id'] ?? null,
                'student_name' => ($validated['firstname'] ?? '') . ' ' . ($validated['lastname'] ?? ''),
                'student_email' => $validated['email'] ?? null,
            ]);
            
            return back()->withErrors(['error' => 'Failed to create student. Please try again.']);
        }
    }

    /**
     * Show the form for bulk upload.
     */
    public function bulkUpload()
    {
        // Get allowed departments and courses for display
        $departments = [];
        $courses = [];
        
        if ($this->settings && $this->settings->assigned_departments) {
            $departments = Department::whereIn('id', $this->settings->assigned_departments)
                ->select('id', 'name', 'code')
                ->get();
        }
        
        if ($this->settings && $this->settings->assigned_courses) {
            $courses = Course::whereIn('id', $this->settings->assigned_courses)
                ->select('id', 'name', 'code')
                ->get();
        }

        return Inertia::render('President/Students/BulkUpload', [
            'allowedCourses' => $this->allowedCourses,
            'allowedDepartments' => $this->allowedDepartments,
            'departments' => $departments,
            'courses' => $courses,
        ]);
    }

    /**
     * Handle bulk upload of students.
     */
    public function bulkStore(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        try {
            Log::info('Starting bulk upload', [
                'organization_id' => $this->organizationId,
                'allowed_courses' => $this->allowedCourses,
                'allowed_departments' => $this->allowedDepartments
            ]);

            // Import students with course and department validation
            Excel::import(
                new StudentsImport(
                    $this->organizationId, 
                    $this->allowedCourses, 
                    $this->allowedDepartments
                ), 
                $request->file('file')
            );
            
            // Log bulk upload success
            $this->logAction('bulk_upload_students', 'Bulk uploaded students', [
                'file_name' => $request->file('file')->getClientOriginalName(),
                'file_size' => $request->file('file')->getSize(),
            ]);
            
            return redirect()->route('president.students.index')
                ->with('success', 'Students imported successfully.');
                
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                $errorMessages[] = "Row {$failure->row()}: " . implode(', ', $failure->errors());
            }
            
            Log::error('Bulk upload validation failed', ['errors' => $errorMessages]);
            
            $this->logError('bulk_upload_students_failed', 'Bulk upload validation failed', $e, [
                'file_name' => $request->file('file')->getClientOriginalName(),
                'errors' => array_slice($errorMessages, 0, 5),
            ]);
            
            return back()->withErrors([
                'file' => 'Validation failed: ' . implode(' | ', array_slice($errorMessages, 0, 3))
            ]);
            
        } catch (\Exception $e) {
            Log::error('Bulk upload failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->logError('bulk_upload_students_failed', 'Bulk upload failed: ' . $e->getMessage(), $e, [
                'file_name' => $request->file('file')->getClientOriginalName(),
            ]);
            
            return back()->withErrors([
                'file' => 'Import failed: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Show the form for editing a student.
     */
    public function edit($student_id)
    {
        $student = Student::where('student_id', $student_id)
            ->where('user_id', $this->organizationId)
            ->firstOrFail();

        $courses = [];
        $departments = [];
        
        if ($this->settings && $this->settings->assigned_courses) {
            $courses = Course::whereIn('id', $this->settings->assigned_courses)
                ->select('id', 'name', 'code')
                ->get();
        }
        
        if ($this->settings && $this->settings->assigned_departments) {
            $departments = Department::whereIn('id', $this->settings->assigned_departments)
                ->select('id', 'name', 'code')
                ->get();
        }

        return Inertia::render('President/Students/Edit', [
            'student' => $student,
            'courses' => $courses,
            'departments' => $departments,
            'yearLevels' => ['1st Year', '2nd Year', '3rd Year', '4th Year'],
        ]);
    }

    /**
     * Update the specified student.
     */
    public function update(Request $request, $student_id)
    {
        $student = Student::where('student_id', $student_id)
            ->where('user_id', $this->organizationId)
            ->firstOrFail();

        $validated = $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'department' => [
                'required',
                'string',
                'max:255',
                Rule::in($this->allowedDepartments)
            ],
            'course' => [
                'required',
                'string',
                'max:255',
                Rule::in($this->allowedCourses)
            ],
            'yearlevel' => 'required|in:1st Year,2nd Year,3rd Year,4th Year',
        ]);

        try {
            $oldData = [
                'firstname' => $student->firstname,
                'lastname' => $student->lastname,
                'email' => $student->email,
                'department' => $student->department,
                'course' => $student->course,
                'yearlevel' => $student->yearlevel,
            ];
            
            $oldDepartment = $student->department;
            $oldCourse = $student->course;
            $oldYearLevel = $student->yearlevel;
            
            $student->update($validated);
            
            // If eligibility changed, resync with events
            $eligibilityChanged = false;
            if ($oldDepartment != $student->department || 
                $oldCourse != $student->course || 
                $oldYearLevel != $student->yearlevel) {
                $this->syncStudentWithEvents($student);
                $eligibilityChanged = true;
            }

            // Log student update
            $this->logAction('update_student', 'Updated student: ' . $student->firstname . ' ' . $student->lastname, [
                'student_id' => $student->student_id,
                'student_name' => $student->firstname . ' ' . $student->lastname,
                'changes' => [
                    'old' => $oldData,
                    'new' => [
                        'firstname' => $student->firstname,
                        'lastname' => $student->lastname,
                        'email' => $student->email,
                        'department' => $student->department,
                        'course' => $student->course,
                        'yearlevel' => $student->yearlevel,
                    ],
                ],
                'eligibility_changed' => $eligibilityChanged,
            ]);

            return redirect()->route('president.students.index')
                ->with('success', 'Student updated successfully.');
                
        } catch (\Exception $e) {
            Log::error('Failed to update student: ' . $e->getMessage());
            
            $this->logError('update_student_failed', 'Failed to update student: ' . $e->getMessage(), $e, [
                'student_id' => $student->student_id,
                'student_name' => $student->firstname . ' ' . $student->lastname,
            ]);
            
            return back()->withErrors(['error' => 'Failed to update student. Please try again.']);
        }
    }

    /**
     * Remove the specified student.
     */
    public function destroy($student_id)
    {
        $student = Student::where('student_id', $student_id)
            ->where('user_id', $this->organizationId)
            ->firstOrFail();

        try {
            $studentName = $student->firstname . ' ' . $student->lastname;
            $studentId = $student->student_id;
            
            // Remove from all events first
            EventStudent::where('student_id', $student->student_id)->delete();
            
            $student->delete();

            // Log student deletion
            $this->logAction('delete_student', 'Deleted student: ' . $studentName, [
                'student_id' => $studentId,
                'student_name' => $studentName,
                'student_email' => $student->email,
            ]);

            return redirect()->route('president.students.index')
                ->with('success', 'Student deleted successfully.');
                
        } catch (\Exception $e) {
            Log::error('Failed to delete student: ' . $e->getMessage());
            
            $this->logError('delete_student_failed', 'Failed to delete student: ' . $e->getMessage(), $e, [
                'student_id' => $student->student_id,
                'student_name' => $student->firstname . ' ' . $student->lastname,
            ]);
            
            return back()->withErrors(['error' => 'Failed to delete student. Please try again.']);
        }
    }

    /**
     * Sync a student with all eligible events
     */
    private function syncStudentWithEvents(Student $student)
    {
        // Import Event model if not already imported at the top
        if (!class_exists('App\Models\Event')) {
            $eventModel = 'App\Models\Event';
        } else {
            $eventModel = Event::class;
        }
        
        // Get all events for this organization
        $events = Event::where('user_id', $this->organizationId)->get();
        $syncedCount = 0;
        
        foreach ($events as $event) {
            // Check if student is eligible for this event
            $isEligible = true;
            
            if (!empty($event->departments) && !in_array($student->department, $event->departments)) {
                $isEligible = false;
            }
            
            if (!empty($event->courses) && !in_array($student->course, $event->courses)) {
                $isEligible = false;
            }
            
            if (!empty($event->year_levels) && !in_array($student->yearlevel, $event->year_levels)) {
                $isEligible = false;
            }
            
            if ($isEligible) {
                // Add or update student in event
                EventStudent::updateOrCreate(
                    [
                        'event_id' => $event->id,
                        'student_id' => $student->student_id,
                    ],
                    [
                        'user_id' => $this->organizationId,
                        'status' => $event->payment === 'Payment' ? 'Pending' : 'Paid',
                        'amount_paid' => $event->payment === 'Payment' ? 0 : $event->event_fee,
                    ]
                );
                $syncedCount++;
            } else {
                // Remove student from event
                EventStudent::where('event_id', $event->id)
                    ->where('student_id', $student->student_id)
                    ->delete();
            }
        }
        
        // Log sync if significant
        if ($syncedCount > 0) {
            $this->logAction('sync_student_with_events', 'Synced student with events', [
                'student_id' => $student->student_id,
                'student_name' => $student->firstname . ' ' . $student->lastname,
                'events_synced' => $syncedCount,
            ]);
        }
    }

    /**
     * Helper method to log CRUD and critical actions only
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