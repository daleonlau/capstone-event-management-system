<?php

namespace App\Http\Controllers\Treasurer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventStudent;
use App\Models\Student;
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

        // Get all approved events
        $events = Event::where('user_id', $this->organizationId)
            ->where('approval_status', 'approved')
            ->with('eventType')
            ->orderBy('event_date_start', 'desc')
            ->get()
            ->map(function ($event) {
                // Get target students count based on event filters
                $targetStudents = Student::where('user_id', $this->organizationId);
                
                if (!empty($event->courses)) {
                    $targetStudents->whereIn('course', $event->courses);
                }
                if (!empty($event->year_levels)) {
                    $targetStudents->whereIn('yearlevel', $event->year_levels);
                }
                
                $targetCount = $targetStudents->count();
                
                // Get paid students count
                $paidCount = EventStudent::where('event_id', $event->id)
                    ->where('status', 'Paid')
                    ->count();
                
                // Get total collected amount
                $collectedAmount = EventStudent::where('event_id', $event->id)
                    ->where('status', 'Paid')
                    ->sum('amount_paid');
                
                return [
                    'id' => $event->id,
                    'event_name' => $event->event_name,
                    'event_type' => $event->eventType,
                    'event_date_start' => $event->event_date_start,
                    'event_date_end' => $event->event_date_end,
                    'event_fee' => $event->event_fee,
                    'target_count' => $targetCount,
                    'paid_count' => $paidCount,
                    'collected_amount' => $collectedAmount,
                    'status' => $event->status,
                ];
            });

        // Calculate total collections
        $totalCollected = EventStudent::whereHas('event', function ($query) {
                $query->where('user_id', $this->organizationId)
                      ->where('approval_status', 'approved');
            })
            ->where('status', 'Paid')
            ->sum('amount_paid');

        // Get pending payments count
        $pendingPayments = EventStudent::whereHas('event', function ($query) {
                $query->where('user_id', $this->organizationId)
                      ->where('approval_status', 'approved');
            })
            ->where('status', 'Pending')
            ->count();

        $stats = [
            'total_events' => $events->count(),
            'total_collected' => $totalCollected,
            'pending_payments' => $pendingPayments,
            'total_students' => Student::where('user_id', $this->organizationId)->count(),
        ];

        return Inertia::render('Treasurer/Dashboard', [
            'stats' => $stats,
            'events' => $events,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ]);
    }

    /**
     * Get dashboard statistics for API
     */
    public function getStats()
    {
        $stats = [
            'total_events' => Event::where('user_id', $this->organizationId)
                ->where('approval_status', 'approved')
                ->count(),
            'total_collected' => EventStudent::whereHas('event', function ($query) {
                    $query->where('user_id', $this->organizationId);
                })
                ->where('status', 'Paid')
                ->sum('amount_paid'),
            'pending_payments' => EventStudent::whereHas('event', function ($query) {
                    $query->where('user_id', $this->organizationId);
                })
                ->where('status', 'Pending')
                ->count(),
        ];

        return response()->json($stats);
    }
}