<?php

namespace App\Http\Controllers\Adviser;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventApproval;
use App\Models\EventStudent;
use App\Models\Student;
use App\Models\Course;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ApprovalController extends Controller
{
    protected $organizationId;
    protected $adviserId;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::guard('org_user')->user();
            if (!$user) {
                return redirect()->route('login');
            }
            $this->organizationId = $user->organization_id;
            $this->adviserId = $user->id;
            return $next($request);
        });
    }

    /**
     * Display a listing of events based on status.
     */
    public function index(Request $request)
    {
        $query = Event::where('user_id', $this->organizationId)
            ->with(['eventType']);

        if ($request->tab === 'approved') {
            $query->where('approval_status', 'approved');
        } elseif ($request->tab === 'rejected') {
            $query->where('approval_status', 'rejected');
        } else {
            $query->whereIn('approval_status', ['pending_document', 'pending_approval']);
        }

        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('event_name', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%");
            });
        }

        if ($request->event_type) {
            $query->where('event_type_id', $request->event_type);
        }

        $events = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->through(function ($event) {
                return [
                    'id' => $event->id,
                    'event_name' => $event->event_name,
                    'event_type' => $event->eventType,
                    'event_date_start' => $event->event_date_start,
                    'event_date_end' => $event->event_date_end,
                    'created_at' => $event->created_at->format('M d, Y'),
                    'signed_document_path' => $event->signed_document_path,
                    'has_document' => !is_null($event->signed_document_path),
                    'approval_status' => $event->approval_status,
                    'payment' => $event->payment,
                    'event_fee' => $event->event_fee,
                    'rejection_reason' => $event->rejection_reason,
                    'eligible_students_count' => $this->getEligibleStudentsCount($event),
                ];
            });

        $eventTypes = \App\Models\EventType::all();

        return Inertia::render('Adviser/Approvals/Index', [
            'events' => $events,
            'filters' => $request->only(['search', 'event_type', 'tab']),
            'eventTypes' => $eventTypes,
        ]);
    }

    /**
     * Display the specified event for approval.
     */
    public function show(Event $event)
    {
        if ($event->user_id !== $this->organizationId) {
            abort(403, 'Unauthorized access.');
        }

        $event->load(['eventType']);

        $departments = Department::all();
        $courses = Course::all();

        // Get department names from IDs
        $departmentNames = [];
        if ($event->departments) {
            foreach ($event->departments as $deptId) {
                $dept = $departments->firstWhere('id', $deptId);
                if ($dept) {
                    $departmentNames[] = $dept->name;
                }
            }
        }

        // Get course names from IDs
        $courseNames = [];
        if ($event->courses) {
            foreach ($event->courses as $courseId) {
                $course = $courses->firstWhere('id', $courseId);
                if ($course) {
                    $courseNames[] = $course->name;
                }
            }
        }

        $eligibleStudentsCount = $this->getEligibleStudentsCount($event);

        return Inertia::render('Adviser/Approvals/Show', [
            'event' => [
                'id' => $event->id,
                'event_name' => $event->event_name,
                'event_type' => $event->eventType,
                'event_date_start' => $event->event_date_start,
                'event_date_end' => $event->event_date_end,
                'description' => $event->description ?? 'No description provided',
                'payment' => $event->payment,
                'event_fee' => $event->event_fee,
                'departments' => $event->departments,
                'department_names' => $departmentNames,
                'courses' => $event->courses,
                'course_names' => $courseNames,
                'year_levels' => $event->year_levels,
                'signed_document_path' => $event->signed_document_path,
                'has_document' => !is_null($event->signed_document_path),
                'approval_status' => $event->approval_status,
                'created_at' => $event->created_at,
                'created_by' => $event->creator->name ?? 'Unknown',
                'rejection_reason' => $event->rejection_reason,
                'eligible_students_count' => $eligibleStudentsCount,
            ],
            'document_url' => $event->signed_document_path 
                ? asset('storage/' . $event->signed_document_path) 
                : null,
            'departments' => $departments,
            'courses' => $courses,
        ]);
    }

    /**
     * FIXED: Approve the event AND populate event_student with all eligible students
     */
    public function approve(Event $event)
    {
        if ($event->user_id !== $this->organizationId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (!$event->signed_document_path) {
            return back()->withErrors(['error' => 'Cannot approve event without signed document.']);
        }

        if ($event->approval_status !== 'pending_approval') {
            return back()->withErrors(['error' => 'This event is not pending approval.']);
        }

        DB::beginTransaction();

        try {
            // 1. Update event status
            $event->update([
                'approval_status' => 'approved',
                'status' => 'Approved',
            ]);

            // 2. Record approval
            EventApproval::create([
                'event_id' => $event->id,
                'approved_by' => $this->adviserId,
                'role' => 'adviser',
                'approved_at' => now(),
            ]);

            // 3. CRITICAL: Populate event_student with ALL eligible students
            $this->populateEventStudents($event);

            DB::commit();

            Log::info('Event approved and students populated', [
                'event_id' => $event->id,
                'event_name' => $event->event_name
            ]);

            return redirect()->route('adviser.approvals.index')
                ->with('success', 'Event approved successfully. All eligible students have been added to the event.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Event approval failed', [
                'event_id' => $event->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['error' => 'Failed to approve event: ' . $e->getMessage()]);
        }
    }

    /**
     * Populate event_student table with all eligible students for the event
     */
    private function populateEventStudents(Event $event)
    {
        // Convert course IDs to names (students table stores course names)
        $courseNames = [];
        if (!empty($event->courses) && is_array($event->courses)) {
            $courseNames = Course::whereIn('id', $event->courses)
                ->pluck('name')
                ->toArray();
        }

        // Build query for eligible students based on event filters
        $query = Student::where('user_id', $event->user_id); // organization_id

        // Apply course filter
        if (!empty($courseNames)) {
            $query->whereIn('course', $courseNames);
        }

        // Apply year level filter
        if (!empty($event->year_levels) && is_array($event->year_levels)) {
            $query->whereIn('yearlevel', $event->year_levels);
        }

        // Get all eligible students
        $eligibleStudents = $query->get();

        $insertedCount = 0;
        $skippedCount = 0;

        foreach ($eligibleStudents as $student) {
            // Check if student already exists in event_student
            $exists = EventStudent::where('event_id', $event->id)
                ->where('student_id', $student->student_id)
                ->exists();

            if (!$exists) {
                // Insert new record with Pending status (even for free events)
                EventStudent::create([
                    'event_id' => $event->id,
                    'student_id' => $student->student_id,
                    'user_id' => $event->user_id,
                    'status' => 'Pending', // Not paid yet, but can still evaluate
                    'amount_paid' => 0,
                    'payment_method' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $insertedCount++;
            } else {
                $skippedCount++;
            }
        }

        Log::info('Event students populated', [
            'event_id' => $event->id,
            'inserted' => $insertedCount,
            'skipped' => $skippedCount,
            'total_eligible' => $eligibleStudents->count()
        ]);

        return [
            'inserted' => $insertedCount,
            'skipped' => $skippedCount,
            'total' => $eligibleStudents->count()
        ];
    }

    /**
     * Get count of eligible students for an event
     */
    private function getEligibleStudentsCount(Event $event)
    {
        $courseNames = [];
        if (!empty($event->courses) && is_array($event->courses)) {
            $courseNames = Course::whereIn('id', $event->courses)
                ->pluck('name')
                ->toArray();
        }

        $query = Student::where('user_id', $event->user_id);

        if (!empty($courseNames)) {
            $query->whereIn('course', $courseNames);
        }

        if (!empty($event->year_levels) && is_array($event->year_levels)) {
            $query->whereIn('yearlevel', $event->year_levels);
        }

        return $query->count();
    }

    /**
     * Reject the specified event
     */
    public function reject(Request $request, Event $event)
    {
        if ($event->user_id !== $this->organizationId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            $event->update([
                'approval_status' => 'rejected',
                'status' => 'Pending',
                'rejection_reason' => $request->rejection_reason,
            ]);

            DB::commit();

            Log::info('Event rejected', [
                'event_id' => $event->id,
                'adviser_id' => $this->adviserId,
                'reason' => $request->rejection_reason
            ]);

            return redirect()->route('adviser.approvals.index')
                ->with('success', 'Event rejected.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Event rejection failed', [
                'event_id' => $event->id,
                'error' => $e->getMessage()
            ]);
            return back()->withErrors(['error' => 'Failed to reject event.']);
        }
    }

    /**
     * Get statistics for dashboard
     */
    public function getStats()
    {
        $stats = [
            'pending_approval' => Event::where('user_id', $this->organizationId)
                ->where('approval_status', 'pending_approval')
                ->count(),
            'pending_document' => Event::where('user_id', $this->organizationId)
                ->where('approval_status', 'pending_document')
                ->count(),
            'approved' => Event::where('user_id', $this->organizationId)
                ->where('approval_status', 'approved')
                ->count(),
            'rejected' => Event::where('user_id', $this->organizationId)
                ->where('approval_status', 'rejected')
                ->count(),
        ];

        return response()->json($stats);
    }
}