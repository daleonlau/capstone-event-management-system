<?php

namespace App\Http\Controllers\Treasurer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Student;
use App\Models\EventStudent;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
            // Get course names from event
            $courseNames = [];
            if (!empty($event->courses) && is_array($event->courses)) {
                $courseNames = Course::whereIn('id', $event->courses)
                    ->pluck('name')
                    ->toArray();
            }
            
            $yearLevels = $event->year_levels ?? [];
            
            // Count ONLY eligible students
            $studentsQuery = Student::where('user_id', $this->organizationId);
            
            if (!empty($courseNames)) {
                $studentsQuery->whereIn('course', $courseNames);
            }
            if (!empty($yearLevels) && is_array($yearLevels)) {
                $studentsQuery->whereIn('yearlevel', $yearLevels);
            }
            
            $totalEligibleStudents = $studentsQuery->count();
            $paidStudents = EventStudent::where('event_id', $event->id)->where('status', 'Paid')->count();
            $pendingStudents = EventStudent::where('event_id', $event->id)->where('status', 'Pending')->count();
            $totalCollected = EventStudent::where('event_id', $event->id)->where('status', 'Paid')->sum('amount_paid');
            
            // Unpaid = total eligible - paid (includes pending + not paid)
            $unpaidStudents = $totalEligibleStudents - $paidStudents;
            
            // Expected total based on UNPAID students only
            $expectedTotal = $unpaidStudents * $event->event_fee;
            
            // Collection rate based on paid vs total eligible
            $collectionRate = $totalEligibleStudents > 0 ? round(($paidStudents / $totalEligibleStudents) * 100, 2) : 0;

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
                'event_date' => $event->event_date_start->format('Y-m-d'),
                'event_fee' => $event->event_fee,
                'total_students' => $totalEligibleStudents,
                'paid_students' => $paidStudents,
                'pending_students' => $pendingStudents,
                'unpaid_students' => $unpaidStudents,
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
            // Increase memory for Railway
            ini_set('memory_limit', '512M');
            ini_set('max_execution_time', 300);
            ini_set('pcre.backtrack_limit', 10000000);
            ini_set('pcre.recursion_limit', 10000000);
            
            $event = Event::findOrFail($eventId);
            
            if ($event->user_id !== $this->organizationId) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            // Get course names from event
            $courseNames = [];
            if (!empty($event->courses) && is_array($event->courses)) {
                $courseNames = Course::whereIn('id', $event->courses)
                    ->pluck('name')
                    ->toArray();
            }
            
            $yearLevels = $event->year_levels ?? [];
            
            // Build query for ELIGIBLE students only
            $studentsQuery = Student::where('user_id', $this->organizationId);
            
            if (!empty($courseNames)) {
                $studentsQuery->whereIn('course', $courseNames);
            }
            if (!empty($yearLevels) && is_array($yearLevels)) {
                $studentsQuery->whereIn('yearlevel', $yearLevels);
            }
            
            // Get all payments for this event
            $payments = EventStudent::where('event_id', $event->id)
                ->get()
                ->keyBy('student_id');
            
            // Process ONLY eligible students
            $studentData = [];
            $paidCount = 0;
            $pendingCount = 0;
            $totalCollected = 0;
            $totalEligibleStudents = 0;
            
            // Use chunking to process eligible students
            $studentsQuery->chunk(100, function($students) use ($event, $payments, &$studentData, &$paidCount, &$pendingCount, &$totalCollected, &$totalEligibleStudents) {
                foreach ($students as $student) {
                    $totalEligibleStudents++;
                    $payment = $payments->get($student->student_id);
                    $status = $payment ? $payment->status : 'Not Paid';
                    $amount = ($status === 'Paid' && $payment) ? floatval($payment->amount_paid) : 0;
                    
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
                        'paid_at' => ($status === 'Paid' && $payment && $payment->updated_at) 
                            ? date('M d, Y', strtotime($payment->updated_at)) 
                            : null,
                        'receipt_number' => ($status === 'Paid' && $payment && $payment->receipt_number) 
                            ? $payment->receipt_number 
                            : '—',
                    ];
                }
            });
            
            // Calculate unpaid students (pending + not paid)
            $unpaidCount = $totalEligibleStudents - $paidCount;
            $expectedTotal = $unpaidCount * floatval($event->event_fee);
            $collectionRate = $totalEligibleStudents > 0 ? round(($paidCount / $totalEligibleStudents) * 100, 2) : 0;
            
            $data = [
                'event' => $event,
                'students' => $studentData,
                'summary' => [
                    'total_students' => $totalEligibleStudents,
                    'paid_students' => $paidCount,
                    'pending_students' => $pendingCount,
                    'unpaid_students' => $unpaidCount,
                    'total_collected' => $totalCollected,
                    'expected_total' => $expectedTotal,
                    'collection_rate' => $collectionRate,
                ],
                'org_name' => $this->organizationName,
                'report_date' => now()->format('F d, Y h:i A'),
                'generated_by' => Auth::guard('org_user')->user()->name,
                'header_image' => null,
            ];
            
            // Create directory
            $path = storage_path('app/public/collection-reports');
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }
            
            $tempPath = storage_path('app/temp');
            if (!file_exists($tempPath)) {
                mkdir($tempPath, 0755, true);
            }
            
            $fontsPath = storage_path('app/fonts');
            if (!file_exists($fontsPath)) {
                mkdir($fontsPath, 0755, true);
            }
            
            // Generate PDF
            $pdf = Pdf::loadView('pdfs.collection-report', $data);
            $pdf->setPaper('A4', 'portrait');
            
            $pdf->setOptions([
                'defaultFont' => 'sans-serif',
                'isHtml5ParserEnabled' => false,
                'isRemoteEnabled' => false,
                'tempDir' => $tempPath,
                'fontDir' => $fontsPath,
                'fontCache' => $fontsPath,
                'enable_font_subsetting' => false,
                'dpi' => 96,
            ]);
            
            $filePath = $path . '/event_' . $event->id . '.pdf';
            $pdf->save($filePath);
            
            // Clear memory
            unset($studentData);
            unset($data);
            
            return response()->json([
                'success' => true,
                'message' => 'Report generated successfully',
                'report_path' => '/storage/collection-reports/event_' . $event->id . '.pdf',
            ]);
            
        } catch (\Exception $e) {
            Log::error('Generate error: ' . $e->getMessage());
            Log::error('Line: ' . $e->getLine());
            Log::error('Trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'error' => 'PDF generation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function view($eventId)
    {
        $event = Event::findOrFail($eventId);
        
        if ($event->user_id !== $this->organizationId) {
            abort(403);
        }
        
        $filePath = storage_path('app/public/collection-reports/event_' . $eventId . '.pdf');
        
        if (!file_exists($filePath)) {
            abort(404, 'Report not found. Please generate the report first.');
        }
        
        return response()->file($filePath, [
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
        
        $filePath = storage_path('app/public/collection-reports/event_' . $eventId . '.pdf');
        
        if (!file_exists($filePath)) {
            abort(404, 'Report not found');
        }
        
        return response()->download($filePath, 'collection-report-' . $event->event_name . '.pdf');
    }

    public function regenerate(Request $request, $eventId)
    {
        try {
            $path = storage_path('app/public/collection-reports');
            $filePath = $path . '/event_' . $eventId . '.pdf';
            
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            
            return $this->generate($request, $eventId);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}   