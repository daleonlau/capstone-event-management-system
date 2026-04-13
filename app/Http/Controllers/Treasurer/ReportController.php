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

    public function index(Request $request)
    {
        $query = Event::where('user_id', $this->organizationId)
            ->where('approval_status', 'approved')
            ->where('payment', 'Payment')
            ->orderBy('event_date_start', 'desc');

        if ($request->date_from) {
            $query->whereDate('event_date_start', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('event_date_start', '<=', $request->date_to);
        }

        $events = $query->get()->map(function ($event) {
            $totalStudents = Student::where('user_id', $this->organizationId)->count();
            
            $paidStudents = EventStudent::where('event_id', $event->id)->where('status', 'Paid')->count();
            $pendingStudents = EventStudent::where('event_id', $event->id)->where('status', 'Pending')->count();
            $totalCollected = EventStudent::where('event_id', $event->id)->where('status', 'Paid')->sum('amount_paid');
            $expectedTotal = $totalStudents * $event->event_fee;
            $collectionRate = $expectedTotal > 0 ? round(($totalCollected / $expectedTotal) * 100, 2) : 0;

            $reportPath = null;
            $reportGeneratedAt = null;
            
            $reportFile = 'collection-reports/event_' . $event->id . '.pdf';
            if (Storage::disk('public')->exists($reportFile)) {
                $reportPath = Storage::disk('public')->url($reportFile);
                $reportGeneratedAt = date('Y-m-d H:i:s', Storage::disk('public')->lastModified($reportFile));
            }

            return [
                'id' => $event->id,
                'event_name' => $event->event_name,
                'event_date' => $event->event_date_start->format('Y-m-d'),
                'event_fee' => $event->event_fee,
                'total_students' => $totalStudents,
                'paid_students' => $paidStudents,
                'pending_students' => $pendingStudents,
                'not_paid_students' => $totalStudents - $paidStudents - $pendingStudents,
                'total_collected' => $totalCollected,
                'expected_total' => $expectedTotal,
                'collection_rate' => $collectionRate,
                'report_path' => $reportPath,
                'report_generated_at' => $reportGeneratedAt,
            ];
        });

        return Inertia::render('Treasurer/Reports/Index', [
            'events' => $events,
            'filters' => $request->only(['date_from', 'date_to', 'collection_status'])
        ]);
    }

    public function generate(Request $request, $eventId)
    {
        try {
            Log::info('Starting collection report generation for event: ' . $eventId);
            
            $event = Event::findOrFail($eventId);
            
            if ($event->user_id !== $this->organizationId) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            // Get all students with their payment status
            $students = Student::where('user_id', $this->organizationId)->get();
            
            $studentData = [];
            $paidCount = 0;
            $pendingCount = 0;
            $totalCollected = 0;
            
            foreach ($students as $student) {
                $payment = EventStudent::where('event_id', $event->id)
                    ->where('student_id', $student->student_id)
                    ->first();
                
                $status = $payment ? $payment->status : 'Not Paid';
                $amount = $payment ? floatval($payment->amount_paid) : 0;
                
                if ($status === 'Paid') {
                    $paidCount++;
                    $totalCollected += $amount;
                } elseif ($status === 'Pending') {
                    $pendingCount++;
                }
                
                $studentData[] = [
                    'student_id' => $student->student_id,
                    'name' => $student->firstname . ' ' . $student->lastname,
                    'course' => $student->course ?? 'N/A',
                    'year_level' => $student->yearlevel ?? 'N/A',
                    'status' => $status,
                    'amount' => $amount,
                    'paid_at' => ($payment && $payment->status === 'Paid' && $payment->updated_at) 
                        ? $payment->updated_at->format('M d, Y') 
                        : null,
                    'receipt_number' => $payment && $payment->receipt_number ? $payment->receipt_number : '—',
                ];
            }
            
            $totalStudents = count($studentData);
            $notPaidCount = $totalStudents - $paidCount - $pendingCount;
            $expectedTotal = $totalStudents * floatval($event->event_fee);
            $collectionRate = $expectedTotal > 0 ? round(($totalCollected / $expectedTotal) * 100, 2) : 0;
            
            $data = [
                'event' => $event,
                'students' => $studentData,
                'summary' => [
                    'total_students' => $totalStudents,
                    'paid_students' => $paidCount,
                    'pending_students' => $pendingCount,
                    'not_paid_students' => $notPaidCount,
                    'total_collected' => $totalCollected,
                    'expected_total' => $expectedTotal,
                    'collection_rate' => $collectionRate,
                ],
                'org_name' => $this->organizationName,
                'report_date' => now()->format('F d, Y'),
                'generated_by' => Auth::guard('org_user')->user()->name,
                'header_image' => null,
            ];
            
            // Generate PDF
            $pdf = Pdf::loadView('pdfs.collection-report', $data);
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOptions([
                'defaultFont' => 'sans-serif',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
            ]);
            
            // Store PDF using Storage facade (like Admin controller)
            $pdfPath = 'collection-reports/event_' . $event->id . '.pdf';
            $pdfContent = $pdf->output();
            Storage::disk('public')->put($pdfPath, $pdfContent);
            
            Log::info('Collection report generated successfully for event: ' . $event->id);
            
            return response()->json([
                'success' => true,
                'message' => 'Report generated successfully',
                'report_path' => Storage::disk('public')->url($pdfPath)
            ]);
            
        } catch (\Exception $e) {
            Log::error('Generate report error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'error' => 'Failed to generate report: ' . $e->getMessage()
            ], 500);
        }
    }

    public function view($eventId)
    {
        $event = Event::findOrFail($eventId);
        
        if ($event->user_id !== $this->organizationId) {
            abort(403);
        }
        
        $pdfPath = 'collection-reports/event_' . $eventId . '.pdf';
        
        if (!Storage::disk('public')->exists($pdfPath)) {
            abort(404, 'Report not found. Please generate the report first.');
        }
        
        return response()->file(storage_path('app/public/' . $pdfPath), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="collection-report-' . $event->event_name . '.pdf"'
        ]);
    }

    public function download($eventId)
    {
        $event = Event::findOrFail($eventId);
        
        if ($event->user_id !== $this->organizationId) {
            abort(403);
        }
        
        $pdfPath = 'collection-reports/event_' . $eventId . '.pdf';
        
        if (!Storage::disk('public')->exists($pdfPath)) {
            abort(404, 'Report not found. Please generate the report first.');
        }
        
        return response()->download(storage_path('app/public/' . $pdfPath), 'collection-report-' . $event->event_name . '.pdf');
    }

    public function regenerate(Request $request, $eventId)
    {
        try {
            $pdfPath = 'collection-reports/event_' . $eventId . '.pdf';
            
            if (Storage::disk('public')->exists($pdfPath)) {
                Storage::disk('public')->delete($pdfPath);
                Log::info('Deleted old report for event: ' . $eventId);
            }
            
            return $this->generate($request, $eventId);
        } catch (\Exception $e) {
            Log::error('Regenerate report error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function summaryReport(Request $request)
    {
        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
        ]);

        $events = Event::where('user_id', $this->organizationId)
            ->where('approval_status', 'approved')
            ->where('payment', 'Payment')
            ->whereBetween('event_date_start', [$request->date_from, $request->date_to])
            ->get();

        $mappedEvents = $events->map(function ($event) {
            $totalStudents = Student::where('user_id', $this->organizationId)->count();
            $paidCount = EventStudent::where('event_id', $event->id)->where('status', 'Paid')->count();
            $totalCollected = EventStudent::where('event_id', $event->id)->where('status', 'Paid')->sum('amount_paid');

            return [
                'event_name' => $event->event_name,
                'event_date' => date('M d, Y', strtotime($event->event_date_start)),
                'event_fee' => $event->event_fee,
                'total_students' => $totalStudents,
                'paid_count' => $paidCount,
                'total_collected' => $totalCollected,
                'collection_rate' => $totalStudents > 0 ? round(($paidCount / $totalStudents) * 100, 2) : 0,
            ];
        });

        $totalEvents = $mappedEvents->count();
        $totalStudents = $mappedEvents->sum('total_students');
        $totalPaid = $mappedEvents->sum('paid_count');
        $totalCollected = $mappedEvents->sum('total_collected');
        $overallRate = $totalStudents > 0 ? round(($totalPaid / $totalStudents) * 100, 2) : 0;

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
            'report_date' => now()->format('F d, Y'),
            'generated_by' => Auth::guard('org_user')->user()->name,
            'header_image' => null,
        ];

        $pdf = Pdf::loadView('pdfs.summary-report', $data);
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->download('summary-report-' . now()->format('Y-m-d') . '.pdf');
    }

    public function collectionReport(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
        ]);

        return $this->generate($request, $request->event_id);
    }
}