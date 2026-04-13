<?php

namespace App\Http\Controllers\Treasurer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Student;
use App\Models\EventStudent;
use App\Models\OrganizationSetting;
use App\Models\Course;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Inertia\Inertia;

class ReportController extends Controller
{
    protected $organizationId;
    protected $organizationName;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::guard('org_user')->user();
            if (!$user) {
                return redirect()->route('login');
            }
            $this->organizationId = $user->organization_id;
            $this->organizationName = $user->organization_name;
            return $next($request);
        });
    }

    /**
     * Show reports dashboard - Collection Reports Index
     */
    public function index(Request $request)
    {
        // Get all events with payment collection
        $query = Event::where('user_id', $this->organizationId)
            ->where('approval_status', 'approved')
            ->where('payment', 'Payment')
            ->with(['eventType'])
            ->orderBy('event_date_start', 'desc');

        // Apply date filters
        if ($request->date_from) {
            $query->whereDate('event_date_start', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('event_date_start', '<=', $request->date_to);
        }

        $events = $query->get()->map(function ($event) {
            // Get department names from IDs
            $departmentNames = [];
            if (!empty($event->departments) && is_array($event->departments)) {
                $departmentNames = Department::whereIn('id', $event->departments)->pluck('name')->toArray();
            }

            // Get course names from IDs
            $courseNames = [];
            if (!empty($event->courses) && is_array($event->courses)) {
                $courseNames = Course::whereIn('id', $event->courses)->pluck('name')->toArray();
            }

            // Count eligible students based on event criteria
            $studentQuery = Student::where('user_id', $this->organizationId);
            
            if (!empty($departmentNames)) {
                $studentQuery->whereIn('department', $departmentNames);
            }
            if (!empty($courseNames)) {
                $studentQuery->whereIn('course', $courseNames);
            }
            if (!empty($event->year_levels) && is_array($event->year_levels)) {
                $studentQuery->whereIn('yearlevel', $event->year_levels);
            }
            
            $totalStudents = $studentQuery->count();

            // Get payment stats from event_students table
            $paidStudents = EventStudent::where('event_id', $event->id)
                ->where('status', 'Paid')
                ->count();
                
            $pendingStudents = EventStudent::where('event_id', $event->id)
                ->where('status', 'Pending')
                ->count();
                
            $notPaidStudents = EventStudent::where('event_id', $event->id)
                ->where('status', 'Not Paid')
                ->count();
                
            $totalCollected = EventStudent::where('event_id', $event->id)
                ->where('status', 'Paid')
                ->sum('amount_paid');
                
            $expectedTotal = $totalStudents * $event->event_fee;
            $collectionRate = $expectedTotal > 0 ? round(($totalCollected / $expectedTotal) * 100, 2) : 0;

            // Check if report exists
            $reportPath = null;
            $reportGeneratedAt = null;
            
            $reportFile = storage_path("app/public/collection-reports/event_{$event->id}.pdf");
            if (file_exists($reportFile)) {
                $reportPath = "/storage/collection-reports/event_{$event->id}.pdf";
                $reportGeneratedAt = filemtime($reportFile);
                $reportGeneratedAt = $reportGeneratedAt ? date('Y-m-d H:i:s', $reportGeneratedAt) : null;
            }

            return [
                'id' => $event->id,
                'event_name' => $event->event_name,
                'event_date' => $event->event_date_start instanceof \Carbon\Carbon 
                    ? $event->event_date_start->format('Y-m-d') 
                    : date('Y-m-d', strtotime($event->event_date_start)),
                'organization_id' => $event->user_id,
                'organization_name' => $this->organizationName,
                'event_fee' => $event->event_fee,
                'total_students' => $totalStudents,
                'paid_students' => $paidStudents,
                'pending_students' => $pendingStudents,
                'not_paid_students' => $notPaidStudents,
                'total_collected' => $totalCollected,
                'expected_total' => $expectedTotal,
                'collection_rate' => $collectionRate,
                'report_path' => $reportPath,
                'report_generated_at' => $reportGeneratedAt,
            ];
        })->values();

        // Apply collection status filter
        if ($request->collection_status) {
            $events = $events->filter(function ($event) use ($request) {
                if ($request->collection_status === 'completed') {
                    return $event['collection_rate'] == 100;
                } elseif ($request->collection_status === 'partial') {
                    return $event['collection_rate'] > 0 && $event['collection_rate'] < 100;
                } elseif ($request->collection_status === 'pending') {
                    return $event['collection_rate'] == 0;
                }
                return true;
            })->values();
        }

        return Inertia::render('Treasurer/Reports/Index', [
            'events' => $events,
            'filters' => [
                'date_from' => $request->date_from,
                'date_to' => $request->date_to,
                'collection_status' => $request->collection_status,
            ]
        ]);
    }

    /**
     * Generate Collection Report PDF
     */
    public function generate(Request $request, $eventId)
    {
        try {
            $event = Event::with(['eventType'])->findOrFail($eventId);

            // Check if event belongs to organization
            if ($event->user_id !== $this->organizationId) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            // Get department names from IDs
            $departmentNames = [];
            if (!empty($event->departments) && is_array($event->departments)) {
                $departmentNames = Department::whereIn('id', $event->departments)->pluck('name')->toArray();
            }

            // Get course names from IDs
            $courseNames = [];
            if (!empty($event->courses) && is_array($event->courses)) {
                $courseNames = Course::whereIn('id', $event->courses)->pluck('name')->toArray();
            }

            // Build query for eligible students based on event criteria
            $query = Student::where('user_id', $this->organizationId);

            if (!empty($departmentNames)) {
                $query->whereIn('department', $departmentNames);
            }
            if (!empty($courseNames)) {
                $query->whereIn('course', $courseNames);
            }
            if (!empty($event->year_levels) && is_array($event->year_levels)) {
                $query->whereIn('yearlevel', $event->year_levels);
            }

            // Get all students with payment status from event_students table
            $students = $query->get()->map(function ($student) use ($event) {
                $payment = EventStudent::where('event_id', $event->id)
                    ->where('student_id', $student->student_id)
                    ->first();

                return [
                    'student_id' => $student->student_id,
                    'name' => $student->firstname . ' ' . $student->lastname,
                    'course' => $student->course,
                    'year_level' => $student->yearlevel,
                    'status' => $payment ? $payment->status : 'Not Paid',
                    'amount' => $payment ? floatval($payment->amount_paid) : 0,
                    'paid_at' => $payment && $payment->updated_at ? $payment->updated_at->format('M d, Y h:i A') : null,
                    'receipt_number' => $payment && $payment->receipt_number ? $payment->receipt_number : null,
                ];
            });

            // Calculate totals
            $totalStudents = $students->count();
            $paidStudents = $students->where('status', 'Paid')->count();
            $pendingStudents = $students->where('status', 'Pending')->count();
            $notPaidStudents = $students->where('status', 'Not Paid')->count();
            $totalCollected = $students->where('status', 'Paid')->sum('amount');
            $expectedTotal = $totalStudents * floatval($event->event_fee);
            $collectionRate = $expectedTotal > 0 ? round(($totalCollected / $expectedTotal) * 100, 2) : 0;

            // Get organization details
            $orgName = $this->organizationName;
            $currentDate = now()->format('F d, Y');

            // Load header image - Try multiple paths
            $headerImage = $this->loadHeaderImage();

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
                    'collection_rate' => $collectionRate,
                ],
                'org_name' => $orgName,
                'school_name' => 'CSUCC - Caraga State University Cabadbaran Campus',
                'report_date' => $currentDate,
                'generated_by' => Auth::guard('org_user')->user()->name,
                'header_image' => $headerImage,
            ];

            // Create directory if it doesn't exist
            $path = storage_path('app/public/collection-reports');
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }
            
            // Generate PDF
            $pdf = Pdf::loadView('pdfs.collection-report', $data);
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOptions([
                'defaultFont' => 'sans-serif',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'chroot' => public_path(),
            ]);
            
            // Save PDF with generic name (overwrites old one)
            $genericPath = $path . '/event_' . $event->id . '.pdf';
            $pdf->save($genericPath);
            
            // Also save a timestamped version for history
            $timestampedPath = $path . '/collection-report-event-' . $event->id . '-' . now()->format('Y-m-d-His') . '.pdf';
            copy($genericPath, $timestampedPath);
            
            Log::info('Report generated successfully for event: ' . $event->id);
            
            return response()->json([
                'success' => true,
                'message' => 'Report generated successfully',
                'report_path' => '/storage/collection-reports/event_' . $event->id . '.pdf'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Generate report error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'error' => 'Failed to generate report: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Load header image from multiple possible paths
     */
    private function loadHeaderImage()
    {
        // First try: Organization logo from settings
        $orgSetting = OrganizationSetting::where('organization_id', $this->organizationId)->first();
        if ($orgSetting && $orgSetting->logo) {
            $logoPath = storage_path('app/public/' . $orgSetting->logo);
            if (file_exists($logoPath)) {
                Log::info('Header image loaded from organization logo');
                return base64_encode(file_get_contents($logoPath));
            }
        }
        
        // Second try: Public images directory
        $possiblePaths = [
            public_path('images/pdfheader.png'),
            public_path('images/header.png'),
            public_path('img/pdfheader.png'),
            public_path('img/header.png'),
            base_path('public/images/pdfheader.png'),
            base_path('public/images/header.png'),
            storage_path('app/public/images/pdfheader.png'),
            storage_path('app/public/images/header.png'),
        ];
        
        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                Log::info('Header image loaded from: ' . $path);
                return base64_encode(file_get_contents($path));
            }
        }
        
        Log::warning('No header image found in any path');
        return null;
    }

    /**
     * View Collection Report
     */
    public function view($eventId)
    {
        try {
            $event = Event::findOrFail($eventId);
            
            // Check if event belongs to organization
            if ($event->user_id !== $this->organizationId) {
                abort(403);
            }
            
            // Find the report file
            $path = storage_path('app/public/collection-reports');
            $genericFile = $path . '/event_' . $eventId . '.pdf';
            
            if (file_exists($genericFile)) {
                return response()->file($genericFile, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="collection-report-' . $event->event_name . '.pdf"'
                ]);
            }
            
            // Try to find a timestamped version
            $files = glob($path . '/collection-report-event-' . $eventId . '-*.pdf');
            if (!empty($files)) {
                $latestFile = end($files);
                return response()->file($latestFile, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="collection-report-' . $event->event_name . '.pdf"'
                ]);
            }
            
            abort(404, 'Report not found. Please generate the report first.');
            
        } catch (\Exception $e) {
            Log::error('View report error: ' . $e->getMessage());
            abort(500, 'Error viewing report: ' . $e->getMessage());
        }
    }

    /**
     * Download Collection Report
     */
    public function download($eventId)
    {
        try {
            $event = Event::findOrFail($eventId);
            
            // Check if event belongs to organization
            if ($event->user_id !== $this->organizationId) {
                abort(403);
            }
            
            // Find the report file
            $path = storage_path('app/public/collection-reports');
            $genericFile = $path . '/event_' . $eventId . '.pdf';
            
            if (file_exists($genericFile)) {
                return response()->download($genericFile, 'collection-report-' . $event->event_name . '.pdf');
            }
            
            // Try to find a timestamped version
            $files = glob($path . '/collection-report-event-' . $eventId . '-*.pdf');
            if (!empty($files)) {
                $latestFile = end($files);
                return response()->download($latestFile, 'collection-report-' . $event->event_name . '.pdf');
            }
            
            abort(404, 'Report not found. Please generate the report first.');
            
        } catch (\Exception $e) {
            Log::error('Download report error: ' . $e->getMessage());
            abort(500, 'Error downloading report: ' . $e->getMessage());
        }
    }

    /**
     * Regenerate Collection Report PDF (includes latest payment data)
     */
    public function regenerate(Request $request, $eventId)
    {
        try {
            $event = Event::with(['eventType'])->findOrFail($eventId);

            // Check if event belongs to organization
            if ($event->user_id !== $this->organizationId) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            // Generate fresh report (this will overwrite the existing generic file)
            return $this->generate($request, $eventId);
            
        } catch (\Exception $e) {
            Log::error('Regenerate report error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to regenerate report: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate Summary Report PDF
     */
    public function summaryReport(Request $request)
    {
        try {
            $request->validate([
                'date_from' => 'required|date',
                'date_to' => 'required|date|after_or_equal:date_from',
            ]);

            // Get all approved payment events within date range
            $events = Event::where('user_id', $this->organizationId)
                ->where('approval_status', 'approved')
                ->where('payment', 'Payment')
                ->whereBetween('event_date_start', [
                    $request->date_from, 
                    $request->date_to
                ])
                ->with(['eventType'])
                ->get();

            $mappedEvents = $events->map(function ($event) {
                // Get department names from IDs
                $departmentNames = [];
                if (!empty($event->departments) && is_array($event->departments)) {
                    $departmentNames = Department::whereIn('id', $event->departments)->pluck('name')->toArray();
                }

                // Get course names from IDs
                $courseNames = [];
                if (!empty($event->courses) && is_array($event->courses)) {
                    $courseNames = Course::whereIn('id', $event->courses)->pluck('name')->toArray();
                }

                // Count eligible students
                $studentQuery = Student::where('user_id', $this->organizationId);
                
                if (!empty($departmentNames)) {
                    $studentQuery->whereIn('department', $departmentNames);
                }
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

            // Load header image
            $headerImage = $this->loadHeaderImage();

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
                'org_name' => $this->organizationName,
                'school_name' => 'CSUCC - Caraga State University Cabadbaran Campus',
                'report_date' => now()->format('F d, Y'),
                'generated_by' => Auth::guard('org_user')->user()->name,
                'header_image' => $headerImage,
            ];

            $pdf = Pdf::loadView('pdfs.summary-report', $data);
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOptions([
                'defaultFont' => 'sans-serif',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'chroot' => public_path(),
            ]);
            
            return $pdf->download('summary-report-' . now()->format('Y-m-d') . '.pdf');
            
        } catch (\Exception $e) {
            Log::error('Summary report error: ' . $e->getMessage());
            return back()->with('error', 'Failed to generate summary report: ' . $e->getMessage());
        }
    }
}