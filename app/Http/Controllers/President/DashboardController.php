<?php

namespace App\Http\Controllers\President;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Student;
use App\Models\Evaluation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
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

    public function index()
    {
        $user = Auth::guard('org_user')->user();
        
        // Get real stats from database
        $totalEvents = Event::where('user_id', $this->organizationId)->count();
        $pendingEvents = Event::where('user_id', $this->organizationId)
            ->where('status', 'Pending')
            ->count();
        $approvedEvents = Event::where('user_id', $this->organizationId)
            ->where('status', 'Approved')
            ->count();
        $finishedEvents = Event::where('user_id', $this->organizationId)
            ->where('status', 'Finished')
            ->count();
        $totalStudents = Student::where('user_id', $this->organizationId)->count();

        // Get events by approval status
        $pendingDocument = Event::where('user_id', $this->organizationId)
            ->where('approval_status', 'pending_document')
            ->count();
        $pendingApproval = Event::where('user_id', $this->organizationId)
            ->where('approval_status', 'pending_approval')
            ->count();
        $rejectedEvents = Event::where('user_id', $this->organizationId)
            ->where('approval_status', 'rejected')
            ->count();

        // Get recent events with their relationships
        $recentEvents = Event::where('user_id', $this->organizationId)
            ->with(['eventType'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'event_name' => $event->event_name,
                    'event_type' => $event->eventType,
                    'event_date_start' => $event->event_date_start,
                    'status' => $event->status,
                    'approval_status' => $event->approval_status,
                    'created_at' => $event->created_at,
                ];
            });

        // Get student distribution by department
        $studentsByDepartment = Student::where('user_id', $this->organizationId)
            ->select('department', DB::raw('count(*) as total'))
            ->groupBy('department')
            ->get()
            ->map(function ($item) {
                return [
                    'department' => $item->department ?? 'Unspecified',
                    'total' => $item->total,
                ];
            });

        // Get event trends (last 6 months)
        $eventTrends = Event::where('user_id', $this->organizationId)
            ->where('created_at', '>=', now()->subMonths(6))
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('count(*) as total')
            )
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        // Get recent activities
        $recentActivities = collect();
        
        // Add recent events
        Event::where('user_id', $this->organizationId)
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get()
            ->each(function ($event) use ($recentActivities) {
                $recentActivities->push([
                    'type' => 'event_created',
                    'description' => "Created event '{$event->event_name}'",
                    'time' => $event->created_at->diffForHumans(),
                    'icon' => 'calendar',
                ]);
            });

        // Add recent students
        Student::where('user_id', $this->organizationId)
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get()
            ->each(function ($student) use ($recentActivities) {
                $recentActivities->push([
                    'type' => 'student_added',
                    'description' => "Added student {$student->firstname} {$student->lastname}",
                    'time' => $student->created_at->diffForHumans(),
                    'icon' => 'user',
                ]);
            });

        // Add recent evaluations
        Evaluation::whereHas('event', function ($query) {
                $query->where('user_id', $this->organizationId);
            })
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get()
            ->each(function ($evaluation) use ($recentActivities) {
                $recentActivities->push([
                    'type' => 'evaluation_submitted',
                    'description' => "New evaluation submitted",
                    'time' => $evaluation->created_at->diffForHumans(),
                    'icon' => 'star',
                ]);
            });

        // Sort activities by time (most recent first)
        $recentActivities = $recentActivities
            ->sortByDesc(function ($activity) {
                return $activity['time'];
            })
            ->values()
            ->take(5);

        // Calculate completion rates
        $eventCompletionRate = $totalEvents > 0 
            ? round(($finishedEvents / $totalEvents) * 100, 1) 
            : 0;
            
        $approvalRate = $totalEvents > 0
            ? round(($approvedEvents / $totalEvents) * 100, 1)
            : 0;

        $stats = [
            'total_events' => $totalEvents,
            'pending_events' => $pendingEvents,
            'approved_events' => $approvedEvents,
            'finished_events' => $finishedEvents,
            'total_students' => $totalStudents,
            'pending_document' => $pendingDocument,
            'pending_approval' => $pendingApproval,
            'rejected_events' => $rejectedEvents,
            'event_completion_rate' => $eventCompletionRate,
            'approval_rate' => $approvalRate,
        ];

        // Debug log to verify data
        \Log::info('President Dashboard Data:', [
            'organization_id' => $this->organizationId,
            'stats' => $stats,
            'studentsByDepartment' => $studentsByDepartment,
            'totalStudents' => $totalStudents,
            'recentEvents' => $recentEvents->count(),
            'recentActivities' => $recentActivities->count()
        ]);

        return Inertia::render('President/Dashboard', [
            'stats' => $stats,
            'recentEvents' => $recentEvents,
            'studentsByDepartment' => $studentsByDepartment,
            'eventTrends' => $eventTrends,
            'recentActivities' => $recentActivities,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ]);
    }

    /**
     * Get dashboard statistics for API (optional)
     */
    public function getStats()
    {
        $stats = [
            'total_events' => Event::where('user_id', $this->organizationId)->count(),
            'pending_events' => Event::where('user_id', $this->organizationId)
                ->where('status', 'Pending')->count(),
            'approved_events' => Event::where('user_id', $this->organizationId)
                ->where('status', 'Approved')->count(),
            'total_students' => Student::where('user_id', $this->organizationId)->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Get students by department for chart (API endpoint)
     */
    public function getStudentsByDepartment()
    {
        $data = Student::where('user_id', $this->organizationId)
            ->select('department', DB::raw('count(*) as total'))
            ->groupBy('department')
            ->get()
            ->map(function ($item) {
                return [
                    'department' => $item->department ?? 'Unspecified',
                    'total' => $item->total,
                ];
            });

        return response()->json($data);
    }

    /**
     * Get recent events for dashboard (API endpoint)
     */
    public function getRecentEvents()
    {
        $events = Event::where('user_id', $this->organizationId)
            ->with(['eventType'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'event_name' => $event->event_name,
                    'event_type' => $event->eventType,
                    'event_date_start' => $event->event_date_start,
                    'status' => $event->status,
                    'approval_status' => $event->approval_status,
                    'created_at' => $event->created_at,
                ];
            });

        return response()->json($events);
    }

    /**
     * Get recent activities for dashboard (API endpoint)
     */
    public function getRecentActivities()
    {
        $activities = collect();
        
        // Add recent events
        Event::where('user_id', $this->organizationId)
            ->orderBy('created_at', 'desc')
            ->limit(2)
            ->get()
            ->each(function ($event) use ($activities) {
                $activities->push([
                    'type' => 'event',
                    'description' => "Created event '{$event->event_name}'",
                    'time' => $event->created_at->diffForHumans(),
                    'icon' => 'calendar',
                ]);
            });

        // Add recent students
        Student::where('user_id', $this->organizationId)
            ->orderBy('created_at', 'desc')
            ->limit(2)
            ->get()
            ->each(function ($student) use ($activities) {
                $activities->push([
                    'type' => 'student',
                    'description' => "Added student {$student->firstname} {$student->lastname}",
                    'time' => $student->created_at->diffForHumans(),
                    'icon' => 'user',
                ]);
            });

        // Sort by time (most recent first)
        $activities = $activities->sortByDesc('time')->values()->take(5);

        return response()->json($activities);
    }
}