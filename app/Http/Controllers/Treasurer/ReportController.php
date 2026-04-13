<?php

namespace App\Http\Controllers\Treasurer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Student;
use App\Models\EventStudent;
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
            $totalStudents = Student::where('user_id', $this->organizationId)->count();
            
            $paidStudents = EventStudent::where('event_id', $event->id)->where('status', 'Paid')->count();
            $pendingStudents = EventStudent::where('event_id', $event->id)->where('status', 'Pending')->count();
            $totalCollected = EventStudent::where('event_id', $event->id)->where('status', 'Paid')->sum('amount_paid');
            $expectedTotal = $totalStudents * $event->event_fee;
            $collectionRate = $expectedTotal > 0 ? round(($totalCollected / $expectedTotal) * 100, 2) : 0;

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
            $event = Event::findOrFail($eventId);
            
            if ($event->user_id !== $this->organizationId) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            // SIMPLE APPROACH - Get data directly from database with joins
            $students = \DB::table('students')
                ->leftJoin('event_student', function($join) use ($event) {
                    $join->on('students.student_id', '=', 'event_student.student_id')
                         ->where('event_student.event_id', '=', $event->id);
                })
                ->where('students.user_id', $this->organizationId)
                ->select(
                    'students.student_id',
                    'students.firstname',
                    'students.lastname',
                    'students.course',
                    'students.yearlevel',
                    'event_student.status',
                    'event_student.amount_paid',
                    'event_student.updated_at as paid_at',
                    'event_student.receipt_number'
                )
                ->get();
            
            $studentData = [];
            $paidCount = 0;
            $pendingCount = 0;
            $totalCollected = 0;
            
            foreach ($students as $student) {
                $status = $student->status ?? 'Not Paid';
                $amount = floatval($student->amount_paid ?? 0);
                
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
                    'paid_at' => ($status === 'Paid' && $student->paid_at) 
                        ? date('M d, Y', strtotime($student->paid_at)) 
                        : null,
                    'receipt_number' => $student->receipt_number ?? '—',
                ];
            }
            
            $totalStudents = count($studentData);
            $expectedTotal = $totalStudents * floatval($event->event_fee);
            
            $data = [
                'event' => $event,
                'students' => $studentData,
                'summary' => [
                    'total_students' => $totalStudents,
                    'paid_students' => $paidCount,
                    'pending_students' => $pendingCount,
                    'not_paid_students' => $totalStudents - $paidCount - $pendingCount,
                    'total_collected' => $totalCollected,
                    'expected_total' => $expectedTotal,
                    'collection_rate' => $expectedTotal > 0 ? round(($totalCollected / $expectedTotal) * 100, 2) : 0,
                ],
                'org_name' => $this->organizationName,
                'report_date' => now()->format('F d, Y'),
                'generated_by' => Auth::guard('org_user')->user()->name,
                'header_image' => null,
            ];
            
            // Create directory
            $path = storage_path('app/public/collection-reports');
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }
            
            // Generate PDF
            $pdf = Pdf::loadView('pdfs.collection-report', $data);
            $pdf->setPaper('A4', 'portrait');
            
            $filePath = $path . '/event_' . $event->id . '.pdf';
            $pdf->save($filePath);
            
            return response()->json([
                'success' => true,
                'message' => 'Report generated successfully',
                'report_path' => '/storage/collection-reports/event_' . $event->id . '.pdf'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Generate error: ' . $e->getMessage() . ' on line ' . $e->getLine());
            return response()->json([
                'error' => $e->getMessage() . ' on line ' . $e->getLine()
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
            abort(404, 'Report not found');
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