<?php

namespace App\Http\Controllers\Treasurer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Student;
use App\Models\EventStudent;
use App\Models\Course;
use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\PaymentReceiptMail;
use Inertia\Inertia;

class CollectionController extends Controller
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
     * Display a listing of approved PAYMENT events for collection.
     */
    public function index()
    {
        // Log all events first
        $allEvents = Event::where('user_id', $this->organizationId)
            ->with(['eventType'])
            ->get();
        
        Log::info('ALL EVENTS for org ' . $this->organizationId, [
            'count' => $allEvents->count(),
            'events' => $allEvents->map(fn($e) => [
                'id' => $e->id,
                'name' => $e->event_name,
                'payment' => $e->payment,
                'approval_status' => $e->approval_status,
                'has_eventType' => !is_null($e->eventType),
            ])
        ]);
    
        // Now get only approved payment events
        $events = Event::where('user_id', $this->organizationId)
            ->where('approval_status', 'approved')
            ->where('payment', 'Payment')
            ->with(['eventType'])
            ->orderBy('event_date_start', 'desc')
            ->get();
    
        Log::info('FILTERED EVENTS', [
            'count' => $events->count(),
            'events' => $events->map(fn($e) => [
                'id' => $e->id,
                'name' => $e->event_name
            ])
        ]);
    
        $mappedEvents = $events->map(function ($event) {
            // Convert course IDs to names for filtering
            $courseNames = [];
            if (!empty($event->courses) && is_array($event->courses)) {
                $courseNames = Course::whereIn('id', $event->courses)
                    ->pluck('name')
                    ->toArray();
            }
            
            // Get target students count based on event filters
            $targetStudents = Student::where('user_id', $this->organizationId);
            
            if (!empty($courseNames)) {
                $targetStudents->whereIn('course', $courseNames);
            }
            if (!empty($event->year_levels) && is_array($event->year_levels)) {
                $targetStudents->whereIn('yearlevel', $event->year_levels);
            }
            
            $targetCount = $targetStudents->count();
            
            // Get paid students count
            $paidCount = EventStudent::where('event_id', $event->id)
                ->where('status', 'Paid')
                ->count();
            
            // Get collected amount
            $collectedAmount = EventStudent::where('event_id', $event->id)
                ->where('status', 'Paid')
                ->sum('amount_paid');
            
            return [
                'id' => $event->id,
                'event_name' => $event->event_name,
                'event_type' => $event->eventType,
                'event_date_start' => $event->event_date_start,
                'event_fee' => $event->event_fee,
                'target_count' => $targetCount,
                'paid_count' => $paidCount,
                'collected_amount' => $collectedAmount,
                'progress' => $targetCount > 0 ? round(($paidCount / $targetCount) * 100, 1) : 0,
            ];
        });
    
        Log::info('MAPPED EVENTS', [
            'count' => $mappedEvents->count(),
            'events' => $mappedEvents
        ]);
    
        return Inertia::render('Treasurer/Collections/Index', [
            'events' => $mappedEvents
        ]);
    }

    /**
     * Display students for a specific PAYMENT event.
     */
    public function show(Event $event, Request $request)
    {
        // Check if event belongs to this organization
        if ($event->user_id !== $this->organizationId) {
            $this->logSecurity('unauthorized_event_access', 'Unauthorized attempt to view event collections', [
                'event_id' => $event->id,
                'event_name' => $event->event_name,
            ]);
            abort(403);
        }

        // Ensure this is a payment event
        if ($event->payment !== 'Payment') {
            return redirect()->route('treasurer.collections.index')
                ->with('error', 'This event does not require payment collections.');
        }

        // Convert course IDs to names
        $courseNames = [];
        if (!empty($event->courses) && is_array($event->courses)) {
            $courseNames = Course::whereIn('id', $event->courses)
                ->pluck('name')
                ->toArray();
        }

        // Get year levels
        $yearLevels = $event->year_levels ?? [];
        
        // Build query for eligible students
        $query = Student::where('user_id', $this->organizationId);

        if (!empty($courseNames)) {
            $query->whereIn('course', $courseNames);
        }
        if (!empty($yearLevels) && is_array($yearLevels)) {
            $query->whereIn('yearlevel', $yearLevels);
        }

        // Search functionality
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('student_id', 'like', "%{$search}%")
                  ->orWhere('firstname', 'like', "%{$search}%")
                  ->orWhere('lastname', 'like', "%{$search}%");
            });
        }

        // Get total count before pagination
        $totalStudents = $query->count();

        // Get students with pagination
        $students = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get payment data from event_student
        $payments = EventStudent::where('event_id', $event->id)
            ->get()
            ->keyBy('student_id');

        // Map students with payment status and receipt info
        $students->getCollection()->transform(function ($student) use ($event, $payments) {
            $payment = $payments->get($student->student_id);
            
            return [
                'student_id' => $student->student_id,
                'firstname' => $student->firstname,
                'lastname' => $student->lastname,
                'email' => $student->email,
                'course' => $student->course,
                'yearlevel' => $student->yearlevel,
                'department' => $student->department,
                'payment_status' => $payment ? $payment->status : 'Not Paid',
                'amount_paid' => $payment ? $payment->amount_paid : 0,
                'paid_at' => $payment ? $payment->updated_at : null,
                'receipt_number' => $payment ? $payment->receipt_number : null,
                'payment_id' => $payment ? $payment->id : null,
            ];
        });

        // Get payment statistics
        $paidCount = EventStudent::where('event_id', $event->id)
            ->where('status', 'Paid')
            ->count();
        
        $pendingCount = EventStudent::where('event_id', $event->id)
            ->where('status', 'Pending')
            ->count();
        
        $totalCollected = EventStudent::where('event_id', $event->id)
            ->where('status', 'Paid')
            ->sum('amount_paid');

        $summary = [
            'total_students' => $totalStudents,
            'paid_count' => $paidCount,
            'pending_count' => $pendingCount,
            'total_collected' => $totalCollected,
            'expected_total' => $totalStudents * $event->event_fee,
            'collection_rate' => $totalStudents > 0 
                ? round(($paidCount / $totalStudents) * 100, 1) 
                : 0,
        ];

        return Inertia::render('Treasurer/Collections/Show', [
            'event' => [
                'id' => $event->id,
                'event_name' => $event->event_name,
                'event_type' => $event->eventType,
                'event_date_start' => $event->event_date_start,
                'event_date_end' => $event->event_date_end,
                'event_fee' => $event->event_fee,
                'payment' => $event->payment,
                'courses' => $courseNames,
                'year_levels' => $yearLevels,
            ],
            'students' => $students,
            'summary' => $summary,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Mark a student as paid (CASH ONLY) and generate receipt with email.
     */
    public function pay(Request $request, Event $event, $studentId)
    {
        try {
            // Check if event belongs to this organization
            if ($event->user_id !== $this->organizationId) {
                $this->logSecurity('unauthorized_payment', 'Unauthorized attempt to record payment', [
                    'event_id' => $event->id,
                    'event_name' => $event->event_name,
                    'student_id' => $studentId,
                ]);
                return response()->json(['error' => 'Unauthorized access.'], 403);
            }

            // Ensure this is a payment event
            if ($event->payment !== 'Payment') {
                return response()->json(['error' => 'This event does not require payments.'], 400);
            }

            // Validate request
            $request->validate([
                'send_email' => 'nullable|boolean',
                'notes' => 'nullable|string|max:255',
            ]);

            // Check if student exists
            $student = Student::where('student_id', $studentId)
                ->where('user_id', $this->organizationId)
                ->first();

            if (!$student) {
                return response()->json(['error' => 'Student not found.'], 404);
            }

            DB::beginTransaction();

            // Generate unique receipt number
            $receiptNumber = $this->generateReceiptNumber();

            // Store old payment data if exists
            $oldPayment = EventStudent::where('event_id', $event->id)
                ->where('student_id', $studentId)
                ->first();
            
            $wasPreviouslyPaid = $oldPayment && $oldPayment->status === 'Paid';

            // Use updateOrCreateComposite instead of find/update
            $payment = EventStudent::updateOrCreateComposite(
                $event->id,
                $studentId,
                [
                    'status' => 'Paid',
                    'amount_paid' => $event->event_fee,
                    'user_id' => $this->organizationId,
                    'receipt_number' => $receiptNumber,
                    'payment_method' => 'cash',
                    'payment_notes' => $request->notes,
                ]
            );

            DB::commit();
            
            // Log payment recording
            $this->logAction('record_payment', 'Recorded payment for student: ' . $student->firstname . ' ' . $student->lastname, [
                'event_id' => $event->id,
                'event_name' => $event->event_name,
                'student_id' => $student->student_id,
                'student_name' => $student->firstname . ' ' . $student->lastname,
                'student_email' => $student->email,
                'amount' => $event->event_fee,
                'receipt_number' => $receiptNumber,
                'payment_notes' => $request->notes,
                'was_previously_paid' => $wasPreviouslyPaid,
                'payment_method' => 'cash',
            ]);

            // Generate PDF
            try {
                $this->generateReceiptPDF($payment, $student, $event);
            } catch (\Exception $e) {
                Log::warning('PDF generation failed but payment was recorded: ' . $e->getMessage());
                $this->logError('pdf_generation_failed', 'PDF generation failed for receipt: ' . $receiptNumber, $e, [
                    'event_id' => $event->id,
                    'student_id' => $student->student_id,
                    'receipt_number' => $receiptNumber,
                ]);
            }

            $sendEmail = $request->has('send_email') ? filter_var($request->send_email, FILTER_VALIDATE_BOOLEAN) : true;
            $emailSent = false;
            
            if ($sendEmail && $student->email) {
                try {
                    // Use send() instead of queue() to avoid serialization issues
                    Mail::to($student->email)->send(new PaymentReceiptMail($payment));
                    
                    DB::table('event_student')
                        ->where('event_id', $event->id)
                        ->where('student_id', $student->student_id)
                        ->update(['receipt_sent_at' => now()]);
                    
                    $emailSent = true;
                    
                    // Log email sent
                    $this->logAction('send_payment_receipt', 'Sent payment receipt email to student', [
                        'event_id' => $event->id,
                        'event_name' => $event->event_name,
                        'student_id' => $student->student_id,
                        'student_name' => $student->firstname . ' ' . $student->lastname,
                        'student_email' => $student->email,
                        'receipt_number' => $receiptNumber,
                    ]);
                } catch (\Exception $e) {
                    Log::warning('Email sending failed but payment was recorded: ' . $e->getMessage());
                    $this->logError('email_sending_failed', 'Failed to send receipt email', $e, [
                        'event_id' => $event->id,
                        'student_id' => $student->student_id,
                        'student_email' => $student->email,
                        'receipt_number' => $receiptNumber,
                    ]);
                }
            }

            $message = 'Payment recorded successfully. Receipt #: ' . $receiptNumber;
            if ($emailSent) {
                $message .= ' Email sent to ' . $student->email;
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'receipt_number' => $receiptNumber,
                'event_id' => $event->id,
                'student_id' => $student->student_id,
                'email_sent' => $emailSent,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment failed: ' . $e->getMessage());
            
            $this->logError('record_payment_failed', 'Failed to record payment: ' . $e->getMessage(), $e, [
                'event_id' => $event->id ?? null,
                'event_name' => $event->event_name ?? null,
                'student_id' => $studentId,
            ]);
            
            return response()->json(['error' => 'Payment failed: ' . $e->getMessage()], 500);
        }
    }

    /**
 * Generate unique receipt number
 */
private function generateReceiptNumber()
{
    $year = date('Y');
    $month = date('m');
    $prefix = "REC-{$year}{$month}-";
    
    // Get the latest receipt number for this month/year using raw SQL to avoid issues
    $lastReceipt = EventStudent::where('receipt_number', 'LIKE', $prefix . '%')
        ->orderBy('created_at', 'desc')
        ->first();
    
    if ($lastReceipt && $lastReceipt->receipt_number) {
        // Extract the number part (last 4 digits)
        $lastNumber = (int) substr($lastReceipt->receipt_number, -4);
        $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
    } else {
        $newNumber = '0001';
    }
    
    // Generate the full receipt number
    $newReceiptNumber = $prefix . $newNumber;
    
    // Safety check - if this number already exists, keep incrementing until we find a free one
    $counter = 0;
    while (EventStudent::where('receipt_number', $newReceiptNumber)->exists() && $counter < 100) {
        $newNumber = str_pad((int)$newNumber + 1, 4, '0', STR_PAD_LEFT);
        $newReceiptNumber = $prefix . $newNumber;
        $counter++;
    }
    
    Log::info('Generated receipt number: ' . $newReceiptNumber);
    
    return $newReceiptNumber;
}

    /**
     * Generate receipt PDF
     */
    private function generateReceiptPDF($payment, $student, $event)
    {
        try {
            Log::info('Generating PDF for receipt: ' . $payment->receipt_number);
            
            // Check if view exists
            if (!view()->exists('pdfs.receipt')) {
                Log::error('PDF view not found');
                return false;
            }
            
            $treasurer = Auth::guard('org_user')->user();
            
            // Load PDF view
            $pdf = Pdf::loadView('pdfs.receipt', [
                'payment' => $payment,
                'student' => $student,
                'event' => $event,
                'treasurer' => $treasurer,
            ]);
            
            $pdf->setPaper('A4', 'portrait');
            
            // Create receipts directory if it doesn't exist
            $receiptsDir = storage_path('app/public/receipts');
            if (!file_exists($receiptsDir)) {
                mkdir($receiptsDir, 0755, true);
                Log::info('Created receipts directory');
            }
            
            // Check if directory is writable
            if (!is_writable($receiptsDir)) {
                Log::error('Receipts directory is not writable: ' . $receiptsDir);
                return false;
            }
            
            // Save PDF file
            $pdfFileName = 'receipt-' . $payment->receipt_number . '.pdf';
            $pdfPath = 'receipts/' . $pdfFileName;
            $fullPath = storage_path('app/public/' . $pdfPath);
            
            $pdfOutput = $pdf->output();
            $bytesWritten = file_put_contents($fullPath, $pdfOutput);
            
            if ($bytesWritten === false || $bytesWritten === 0) {
                Log::error('Failed to write PDF file');
                return false;
            }
            
            Log::info('PDF saved successfully: ' . $fullPath . ' (' . $bytesWritten . ' bytes)');
            
            // Update PDF path in database
            DB::table('event_student')
                ->where('event_id', $payment->event_id)
                ->where('student_id', $payment->student_id)
                ->update(['receipt_pdf_path' => $pdfPath]);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('PDF generation error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return false;
        }
    }
    
    /**
     * Download receipt PDF
     */
    public function downloadReceipt($eventId, $studentId)
    {
        try {
            $payment = EventStudent::where('event_id', $eventId)
                ->where('student_id', $studentId)
                ->first();

            if (!$payment) {
                return response()->json(['error' => 'Payment not found.'], 404);
            }

            if ($payment->event->user_id !== $this->organizationId) {
                $this->logSecurity('unauthorized_receipt_download', 'Unauthorized attempt to download receipt', [
                    'event_id' => $eventId,
                    'student_id' => $studentId,
                    'receipt_number' => $payment->receipt_number,
                ]);
                return response()->json(['error' => 'Unauthorized access.'], 403);
            }

            // If no PDF path, generate one on the fly
            if (!$payment->receipt_pdf_path || !file_exists(storage_path('app/public/' . $payment->receipt_pdf_path))) {
                $student = Student::where('student_id', $studentId)->first();
                $event = Event::find($eventId);
                
                $this->generateReceiptPDF($payment, $student, $event);
                
                // Refresh payment to get updated path
                $payment = EventStudent::where('event_id', $eventId)
                    ->where('student_id', $studentId)
                    ->first();
            }

            if (!$payment->receipt_pdf_path) {
                return response()->json(['error' => 'Receipt file not found.'], 404);
            }

            $fullPath = storage_path('app/public/' . $payment->receipt_pdf_path);
            
            if (!file_exists($fullPath)) {
                return response()->json(['error' => 'Receipt file not found on disk.'], 404);
            }

            // Log receipt download
            $this->logAction('download_receipt', 'Downloaded payment receipt', [
                'event_id' => $eventId,
                'student_id' => $studentId,
                'receipt_number' => $payment->receipt_number,
            ]);

            return response()->download($fullPath, 'receipt-' . $payment->receipt_number . '.pdf');
            
        } catch (\Exception $e) {
            Log::error('Download failed: ' . $e->getMessage());
            $this->logError('receipt_download_failed', 'Failed to download receipt: ' . $e->getMessage(), $e, [
                'event_id' => $eventId,
                'student_id' => $studentId,
            ]);
            return response()->json(['error' => 'Download failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * View receipt in browser
     */
    public function viewReceipt($eventId, $studentId)
    {
        try {
            $payment = EventStudent::where('event_id', $eventId)
                ->where('student_id', $studentId)
                ->first();

            if (!$payment) {
                return response()->json(['error' => 'Payment not found.'], 404);
            }

            if ($payment->event->user_id !== $this->organizationId) {
                $this->logSecurity('unauthorized_receipt_view', 'Unauthorized attempt to view receipt', [
                    'event_id' => $eventId,
                    'student_id' => $studentId,
                    'receipt_number' => $payment->receipt_number,
                ]);
                return response()->json(['error' => 'Unauthorized access.'], 403);
            }

            // If no PDF path, generate one on the fly
            if (!$payment->receipt_pdf_path || !file_exists(storage_path('app/public/' . $payment->receipt_pdf_path))) {
                $student = Student::where('student_id', $studentId)->first();
                $event = Event::find($eventId);
                
                $this->generateReceiptPDF($payment, $student, $event);
                
                // Refresh payment to get updated path
                $payment = EventStudent::where('event_id', $eventId)
                    ->where('student_id', $studentId)
                    ->first();
            }

            if (!$payment->receipt_pdf_path) {
                return response()->json(['error' => 'Receipt file not found.'], 404);
            }

            $fullPath = storage_path('app/public/' . $payment->receipt_pdf_path);
            
            if (!file_exists($fullPath)) {
                return response()->json(['error' => 'Receipt file not found on disk.'], 404);
            }

            // Return PDF for viewing in browser
            return response()->file($fullPath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="receipt-' . $payment->receipt_number . '.pdf"'
            ]);
            
        } catch (\Exception $e) {
            Log::error('View receipt failed: ' . $e->getMessage());
            $this->logError('receipt_view_failed', 'Failed to view receipt: ' . $e->getMessage(), $e, [
                'event_id' => $eventId,
                'student_id' => $studentId,
            ]);
            return response()->json(['error' => 'View receipt failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Resend receipt email
     */
    public function resendReceiptEmail($eventId, $studentId)
    {
        try {
            Log::info('========== RESEND RECEIPT EMAIL STARTED ==========');
            Log::info('Event ID: ' . $eventId);
            Log::info('Student ID: ' . $studentId);

            // Find the payment with all relationships loaded
            $payment = EventStudent::with(['event', 'student', 'treasurer'])
                ->where('event_id', $eventId)
                ->where('student_id', $studentId)
                ->first();

            if (!$payment) {
                Log::error('Payment not found');
                return response()->json(['error' => 'Payment not found.'], 404);
            }

            // Check if payment belongs to this organization
            if ($payment->event->user_id !== $this->organizationId) {
                $this->logSecurity('unauthorized_email_resend', 'Unauthorized attempt to resend receipt email', [
                    'event_id' => $eventId,
                    'student_id' => $studentId,
                    'receipt_number' => $payment->receipt_number,
                ]);
                return response()->json(['error' => 'Unauthorized access.'], 403);
            }

            $student = $payment->student;

            if (!$student) {
                Log::error('Student not found');
                return response()->json(['error' => 'Student not found.'], 404);
            }

            if (!$student->email) {
                Log::error('Student has no email');
                return response()->json(['error' => 'Student does not have an email address.'], 400);
            }

            // Generate PDF if it doesn't exist
            if (!$payment->receipt_pdf_path || !file_exists(storage_path('app/public/' . $payment->receipt_pdf_path))) {
                Log::info('Generating PDF for resend');
                
                $student = Student::where('student_id', $studentId)->first();
                $event = Event::find($eventId);
                
                $pdfGenerated = $this->generateReceiptPDF($payment, $student, $event);
                
                if (!$pdfGenerated) {
                    Log::warning('PDF generation failed, but will still try to send email');
                }
                
                // Refresh payment to get updated path
                $payment = EventStudent::with(['event', 'student', 'treasurer'])
                    ->where('event_id', $eventId)
                    ->where('student_id', $studentId)
                    ->first();
            }

            // Use send() instead of queue() to avoid serialization issues
            Log::info('Sending email to: ' . $student->email);
            
            Mail::to($student->email)->send(new PaymentReceiptMail($payment));
            
            // Update sent timestamp
            DB::table('event_student')
                ->where('event_id', $eventId)
                ->where('student_id', $studentId)
                ->update(['receipt_sent_at' => now()]);
            
            Log::info('Email sent successfully');
            
            // Log email resend
            $this->logAction('resend_payment_receipt', 'Resent payment receipt email to student', [
                'event_id' => $eventId,
                'student_id' => $studentId,
                'student_name' => $student->firstname . ' ' . $student->lastname,
                'student_email' => $student->email,
                'receipt_number' => $payment->receipt_number,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Receipt email sent successfully to ' . $student->email
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to resend receipt email', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->logError('resend_receipt_email_failed', 'Failed to resend receipt email: ' . $e->getMessage(), $e, [
                'event_id' => $eventId,
                'student_id' => $studentId,
            ]);
            
            return response()->json([
                'error' => 'Failed to send email: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Bulk mark students as paid.
     */
    public function bulkPay(Request $request, Event $event)
    {
        // This method would be implemented similarly with logging
        // For now, returning placeholder
        return response()->json(['message' => 'Bulk pay feature coming soon'], 501);
    }
    
    /**
     * Show collection summary for an event.
     */
    public function summary(Event $event)
    {
        if ($event->user_id !== $this->organizationId) {
            $this->logSecurity('unauthorized_summary_view', 'Unauthorized attempt to view collection summary', [
                'event_id' => $event->id,
                'event_name' => $event->event_name,
            ]);
            abort(403);
        }

        if ($event->payment !== 'Payment') {
            return redirect()->route('treasurer.collections.index')
                ->with('error', 'This event does not require payment collections.');
        }

        // Convert course IDs to names for filtering
        $courseNames = [];
        if (!empty($event->courses) && is_array($event->courses)) {
            $courseNames = Course::whereIn('id', $event->courses)
                ->pluck('name')
                ->toArray();
        }

        // Get all eligible students
        $studentsQuery = Student::where('user_id', $this->organizationId);

        if (!empty($courseNames)) {
            $studentsQuery->whereIn('course', $courseNames);
        }
        if (!empty($event->year_levels) && is_array($event->year_levels)) {
            $studentsQuery->whereIn('yearlevel', $event->year_levels);
        }

        $totalStudents = $studentsQuery->count();

        // Get payment statistics
        $payments = EventStudent::where('event_id', $event->id)->get();
        
        $paidCount = $payments->where('status', 'Paid')->count();
        $pendingCount = $payments->where('status', 'Pending')->count();
        $notPaidCount = $totalStudents - $paidCount - $pendingCount;
        
        $totalCollected = $payments->where('status', 'Paid')->sum('amount_paid');
        $expectedTotal = $totalStudents * $event->event_fee;

        // Get receipts count
        $receiptCount = $payments->whereNotNull('receipt_number')->count();

        // Get payments by date for chart
        $paymentsByDate = EventStudent::where('event_id', $event->id)
            ->where('status', 'Paid')
            ->whereNotNull('updated_at')
            ->select(DB::raw('DATE(updated_at) as date'), DB::raw('count(*) as count'), DB::raw('sum(amount_paid) as total'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Log summary view
        $this->logAction('view_collection_summary', 'Viewed collection summary for event: ' . $event->event_name, [
            'event_id' => $event->id,
            'event_name' => $event->event_name,
            'total_students' => $totalStudents,
            'paid_count' => $paidCount,
            'total_collected' => $totalCollected,
            'collection_rate' => $expectedTotal > 0 ? round(($totalCollected / $expectedTotal) * 100, 2) : 0,
        ]);

        return Inertia::render('Treasurer/Collections/Summary', [
            'event' => [
                'id' => $event->id,
                'event_name' => $event->event_name,
                'event_type' => $event->eventType,
                'event_date_start' => $event->event_date_start,
                'event_date_end' => $event->event_date_end,
                'event_fee' => $event->event_fee,
                'payment' => $event->payment,
            ],
            'summary' => [
                'total_students' => $totalStudents,
                'paid_students' => $paidCount,
                'pending_students' => $pendingCount,
                'not_paid_students' => $notPaidCount,
                'total_collected' => $totalCollected,
                'expected_total' => $expectedTotal,
                'collection_rate' => $expectedTotal > 0 ? round(($totalCollected / $expectedTotal) * 100, 2) : 0,
                'payments_by_date' => $paymentsByDate,
                'receipts_generated' => $receiptCount,
            ],
        ]);
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
    
    /**
     * Helper method to log security events
     */
    private function logSecurity($action, $description, $details = [])
    {
        try {
            $user = Auth::guard('org_user')->user();
            LogService::security($action, $description, $user, $details);
        } catch (\Exception $e) {
            // Silent fail
            Log::warning('Failed to log security event: ' . $e->getMessage());
        }
    }
}