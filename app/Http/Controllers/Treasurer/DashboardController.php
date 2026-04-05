<?php

namespace App\Http\Controllers\Treasurer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Student;
use App\Models\EventStudent;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;

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

        // Get all approved payment events
        $events = Event::where('user_id', $this->organizationId)
            ->where('approval_status', 'approved')
            ->where('payment', 'Payment')
            ->where('status', '!=', 'Finished')
            ->with(['eventType'])
            ->orderBy('event_date_start', 'asc')
            ->get()
            ->map(function ($event) {
                // Get course names from IDs
                $courseNames = [];
                if (!empty($event->courses) && is_array($event->courses)) {
                    $courseNames = Course::whereIn('id', $event->courses)
                        ->pluck('name')
                        ->toArray();
                }

                // Count eligible students
                $studentQuery = Student::where('user_id', $this->organizationId);
                
                if (!empty($courseNames)) {
                    $studentQuery->whereIn('course', $courseNames);
                }
                if (!empty($event->year_levels) && is_array($event->year_levels)) {
                    $studentQuery->whereIn('yearlevel', $event->year_levels);
                }
                
                $totalStudents = $studentQuery->count();

                // Get payment stats
                $paidStudents = EventStudent::where('event_id', $event->id)
                    ->where('status', 'Paid')
                    ->count();
                    
                $pendingStudents = EventStudent::where('event_id', $event->id)
                    ->where('status', 'Pending')
                    ->count();
                    
                $totalCollected = EventStudent::where('event_id', $event->id)
                    ->where('status', 'Paid')
                    ->sum('amount_paid');
                    
                $expectedTotal = $totalStudents * $event->event_fee;
                $progress = $expectedTotal > 0 ? round(($totalCollected / $expectedTotal) * 100, 1) : 0;
                $paidPercentage = $totalStudents > 0 ? round(($paidStudents / $totalStudents) * 100, 1) : 0;

                return [
                    'id' => $event->id,
                    'event_name' => $event->event_name,
                    'event_type' => $event->eventType,
                    'event_date_start' => $event->event_date_start,
                    'event_date_end' => $event->event_date_end,
                    'event_fee' => $event->event_fee,
                    'target_count' => $totalStudents,
                    'paid_count' => $paidStudents,
                    'pending_count' => $pendingStudents,
                    'collected_amount' => $totalCollected,
                    'expected_amount' => $expectedTotal,
                    'progress' => $progress,
                    'paid_percentage' => $paidPercentage,
                    'remaining_amount' => $expectedTotal - $totalCollected,
                    'days_remaining' => $this->getDaysRemaining($event->event_date_end),
                    'status' => $this->getCollectionStatus($progress, $paidPercentage),
                ];
            })
            ->filter(function ($event) {
                // Only show events that are not 100% collected (or show all if you want)
                return $event['progress'] < 100;
            })
            ->values();

        // Calculate stats
        $totalEvents = $events->count();
        $totalCollected = $events->sum('collected_amount');
        $totalExpected = $events->sum('expected_amount');
        $totalPendingPayments = $events->sum('pending_count');
        $totalStudents = Student::where('user_id', $this->organizationId)->count();
        
        // Calculate overall collection rate
        $overallRate = $totalExpected > 0 ? round(($totalCollected / $totalExpected) * 100, 1) : 0;

        // Get recent payment activities
        $recentPayments = EventStudent::whereHas('event', function($q) {
                $q->where('user_id', $this->organizationId);
            })
            ->where('status', 'Paid')
            ->with(['event', 'student'])
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($payment) {
                return [
                    'student_name' => $payment->student ? $payment->student->firstname . ' ' . $payment->student->lastname : 'Unknown',
                    'event_name' => $payment->event->event_name,
                    'amount' => $payment->amount_paid,
                    'paid_at' => $payment->updated_at->diffForHumans(),
                    'receipt_number' => $payment->receipt_number,
                ];
            });

        // Get monthly collection trend (last 6 months)
        $monthlyTrend = EventStudent::whereHas('event', function($q) {
                $q->where('user_id', $this->organizationId);
            })
            ->where('status', 'Paid')
            ->where('updated_at', '>=', now()->subMonths(6))
            ->select(
                DB::raw('DATE_FORMAT(updated_at, "%Y-%m") as month'),
                DB::raw('SUM(amount_paid) as total')
            )
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get()
            ->map(function ($item) {
                $date = Carbon::createFromFormat('Y-m', $item->month);
                return [
                    'month' => $date->format('M Y'),
                    'total' => $item->total,
                ];
            });

        // Get top paying events
        $topEvents = $events->sortByDesc('collected_amount')->take(3)->values();

        $stats = [
            'total_events' => $totalEvents,
            'total_collected' => $totalCollected,
            'total_expected' => $totalExpected,
            'overall_rate' => $overallRate,
            'pending_payments' => $totalPendingPayments,
            'total_students' => $totalStudents,
        ];

        return Inertia::render('Treasurer/Dashboard', [
            'stats' => $stats,
            'events' => $events,
            'recentPayments' => $recentPayments,
            'monthlyTrend' => $monthlyTrend,
            'topEvents' => $topEvents,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ]);
    }

    private function getDaysRemaining($endDate)
    {
        if (!$endDate) return null;
        $end = Carbon::parse($endDate);
        $now = Carbon::now();
        if ($end->lt($now)) return 0;
        return $end->diffInDays($now);
    }

    private function getCollectionStatus($progress, $paidPercentage)
    {
        if ($progress >= 100 || $paidPercentage >= 100) {
            return ['label' => 'Complete', 'color' => 'green', 'icon' => 'check-circle'];
        } elseif ($progress >= 75 || $paidPercentage >= 75) {
            return ['label' => 'Almost Complete', 'color' => 'emerald', 'icon' => 'trending-up'];
        } elseif ($progress >= 50 || $paidPercentage >= 50) {
            return ['label' => 'Halfway', 'color' => 'yellow', 'icon' => 'clock'];
        } elseif ($progress > 0 || $paidPercentage > 0) {
            return ['label' => 'Started', 'color' => 'blue', 'icon' => 'play'];
        } else {
            return ['label' => 'Not Started', 'color' => 'gray', 'icon' => 'circle'];
        }
    }
}