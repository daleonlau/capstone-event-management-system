<?php

namespace App\Http\Controllers\Treasurer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Student;
use App\Models\EventStudent;
use App\Models\OrganizationSetting;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Inertia\Inertia;

class ReportController extends Controller
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
     * Show reports dashboard
     */
    public function index()
    {
        $events = Event::where('user_id', $this->organizationId)
            ->where('approval_status', 'approved')
            ->where('payment', 'Payment')
            ->with(['eventType'])
            ->orderBy('event_date_start', 'desc')
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'event_name' => $event->event_name,
                    'event_date_start' => $event->event_date_start,
                    'event_fee' => $event->event_fee,
                ];
            });

        return Inertia::render('Treasurer/Reports/Index', [
            'events' => $events
        ]);
    }

    /**
     * Generate Collection Report PDF
     */
    public function collectionReport(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'status' => 'nullable|in:all,paid,pending,not_paid'
        ]);

        $event = Event::with(['eventType'])->findOrFail($request->event_id);

        // Check if event belongs to organization
        if ($event->user_id !== $this->organizationId) {
            abort(403);
        }

        // Get course names from IDs
        $courseNames = [];
        if (!empty($event->courses) && is_array($event->courses)) {
            $courseNames = Course::whereIn('id', $event->courses)
                ->pluck('name')
                ->toArray();
        }

        // Build query for eligible students
        $query = Student::where('user_id', $this->organizationId);

        if (!empty($courseNames)) {
            $query->whereIn('course', $courseNames);
        }
        if (!empty($event->year_levels) && is_array($event->year_levels)) {
            $query->whereIn('yearlevel', $event->year_levels);
        }

        // Get all students with payment status
        $students = $query->get()->map(function ($student) use ($event, $request) {
            $payment = EventStudent::where('event_id', $event->id)
                ->where('student_id', $student->student_id)
                ->first();

            $status = $payment ? $payment->status : 'Not Paid';
            $paidAt = $payment ? $payment->updated_at : null;

            // Filter by date range if provided
            if ($request->date_from && $paidAt) {
                if ($paidAt < $request->date_from) return null;
            }
            if ($request->date_to && $paidAt) {
                if ($paidAt > $request->date_to) return null;
            }

            // Filter by status if provided
            if ($request->status && $request->status !== 'all') {
                $expectedStatus = $request->status === 'paid' ? 'Paid' : 
                                 ($request->status === 'pending' ? 'Pending' : 'Not Paid');
                if ($status !== $expectedStatus) return null;
            }

            return [
                'student_id' => $student->student_id,
                'name' => $student->firstname . ' ' . $student->lastname,
                'course' => $student->course,
                'year_level' => $student->yearlevel,
                'status' => $status,
                'amount' => $payment ? $payment->amount_paid : 0,
                'paid_at' => $paidAt ? $paidAt->format('M d, Y h:i A') : 'N/A',
            ];
        })->filter()->values();

        // Calculate totals
        $totalStudents = $students->count();
        $paidStudents = $students->where('status', 'Paid')->count();
        $pendingStudents = $students->where('status', 'Pending')->count();
        $notPaidStudents = $students->where('status', 'Not Paid')->count();
        $totalCollected = $students->where('status', 'Paid')->sum('amount');
        $expectedTotal = $totalStudents * $event->event_fee;

        // Get organization details
        $orgName = Auth::guard('org_user')->user()->organization->name ?? 'Organization';
        $currentDate = now()->format('F d, Y');

        // Prepare data for PDF
        $data = [
            'event' => $event,
            'students' => $students,
            'summary' => [
                'total_students' => $totalStudents,
                'paid_students' => $paidStudents,
                'pending_students' => $pendingStudents,
                'not_paid_students' => $notPaidStudents,
                'total_collected' => $totalCollected,
                'expected_total' => $expectedTotal,
                'collection_rate' => $expectedTotal > 0 ? round(($totalCollected / $expectedTotal) * 100, 2) : 0,
            ],
            'filters' => [
                'date_from' => $request->date_from,
                'date_to' => $request->date_to,
                'status' => $request->status,
            ],
            'org_name' => $orgName,
            'school_name' => 'CSUCC - Caraga State University Cabadbaran Campus',
            'report_date' => $currentDate,
            'generated_by' => Auth::guard('org_user')->user()->name,
        ];

        // Generate PDF
        $pdf = Pdf::loadView('pdfs.collection-report', $data);
        
        // Return PDF for download
        return $pdf->download('collection-report-' . $event->event_name . '-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Generate Summary Report PDF - FIXED VERSION
     */
    public function summaryReport(Request $request)
    {
        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
        ]);

        Log::info('Summary Report Request', [
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
            'org_id' => $this->organizationId
        ]);

        // Get all approved payment events within date range
        $events = Event::where('user_id', $this->organizationId)
            ->where('approval_status', 'approved')
            ->where('payment', 'Payment')
            ->whereBetween('created_at', [
                $request->date_from . ' 00:00:00', 
                $request->date_to . ' 23:59:59'
            ])
            ->with(['eventType'])
            ->get();

        Log::info('Events found', ['count' => $events->count()]);

        $mappedEvents = $events->map(function ($event) {
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
            $paidCount = EventStudent::where('event_id', $event->id)
                ->where('status', 'Paid')
                ->count();
                
            $totalCollected = EventStudent::where('event_id', $event->id)
                ->where('status', 'Paid')
                ->sum('amount_paid');

            return [
                'event_name' => $event->event_name,
                'event_date' => $event->event_date_start instanceof \Carbon\Carbon 
                    ? $event->event_date_start->format('M d, Y') 
                    : date('M d, Y', strtotime($event->event_date_start)),
                'event_fee' => $event->event_fee,
                'total_students' => $totalStudents,
                'paid_count' => $paidCount,
                'total_collected' => $totalCollected,
                'collection_rate' => $totalStudents > 0 ? round(($paidCount / $totalStudents) * 100, 2) : 0,
            ];
        });

        // Calculate overall totals
        $totalEvents = $mappedEvents->count();
        $totalStudents = $mappedEvents->sum('total_students');
        $totalPaid = $mappedEvents->sum('paid_count');
        $totalCollected = $mappedEvents->sum('total_collected');
        $overallRate = $totalStudents > 0 ? round(($totalPaid / $totalStudents) * 100, 2) : 0;

        // Get organization details
        $orgName = Auth::guard('org_user')->user()->organization->name ?? 'Organization';
        $currentDate = now()->format('F d, Y');

        $data = [
            'events' => $mappedEvents,
            'summary' => [
                'total_events' => $totalEvents,
                'total_students' => $totalStudents,
                'total_paid' => $totalPaid,
                'total_collected' => $totalCollected,
                'overall_rate' => $overallRate,
            ],
            'date_range' => [
                'from' => $request->date_from,
                'to' => $request->date_to,
            ],
            'org_name' => $orgName,
            'school_name' => 'CSUCC - Caraga State University Cabadbaran Campus',
            'report_date' => $currentDate,
            'generated_by' => Auth::guard('org_user')->user()->name,
        ];

        // Debug log
        Log::info('Summary Report Data', $data);

        // Generate PDF
        $pdf = Pdf::loadView('pdfs.summary-report', $data);
        
        return $pdf->download('summary-report-' . now()->format('Y-m-d') . '.pdf');
    }
}