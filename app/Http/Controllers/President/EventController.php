<?php

namespace App\Http\Controllers\President;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventType;
use App\Models\OrganizationSetting;
use App\Models\Course;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class EventController extends Controller
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
     * Display a listing of events.
     */
    public function index()
    {
        $events = Event::where('user_id', $this->organizationId)
            ->with('eventType')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'event_name' => $event->event_name,
                    'event_type' => $event->eventType,
                    'event_date_start' => $event->event_date_start,
                    'event_date_end' => $event->event_date_end,
                    'status' => $event->status,
                    'approval_status' => $event->approval_status,
                    'signed_document_path' => $event->signed_document_path,
                    'has_document' => !is_null($event->signed_document_path),
                    'payment' => $event->payment,
                    'event_fee' => $event->event_fee,
                    'has_evaluation' => $event->evaluations()->exists(),
                    'evaluation_count' => $event->evaluations()->count(),
                    'created_at' => $event->created_at,
                ];
            });

        return Inertia::render('President/Events/Index', [
            'events' => $events
        ]);
    }

    /**
     * Show the form for creating a new event.
     */
    public function create()
    {
        $settings = OrganizationSetting::where('organization_id', $this->organizationId)->first();
        
        $departments = Department::whereIn('id', $settings->assigned_departments ?? [])
            ->with(['courses' => function($query) use ($settings) {
                $query->whereIn('id', $settings->assigned_courses ?? []);
            }])
            ->get();

        $eventTypes = EventType::all();
        $yearLevels = ['1st Year', '2nd Year', '3rd Year', '4th Year'];

        return Inertia::render('President/Events/Create', [
            'departments' => $departments,
            'eventTypes' => $eventTypes,
            'yearLevels' => $yearLevels,
        ]);
    }

    /**
     * Store a newly created event (NO DOCUMENT REQUIRED).
     */
    public function store(Request $request)
    {
        $request->validate([
            'event_name' => 'required|string|max:255',
            'event_type_id' => 'required|exists:event_types,id',
            'event_date_start' => 'required|date',
            'event_date_end' => 'required|date|after_or_equal:event_date_start',
            'departments' => 'required|array|min:1',
            'courses' => 'required|array|min:1',
            'year_levels' => 'required|array|min:1',
        ]);

        $eventType = EventType::find($request->event_type_id);
        
        if ($eventType->requires_payment) {
            $request->validate(['event_fee' => 'required|numeric|min:0']);
        }

        $event = Event::create([
            'user_id' => $this->organizationId,
            'event_name' => $request->event_name,
            'event_type_id' => $request->event_type_id,
            'event_date_start' => $request->event_date_start,
            'event_date_end' => $request->event_date_end,
            'payment' => $eventType->requires_payment ? 'Payment' : 'No Payment',
            'event_fee' => $request->event_fee ?? 0,
            'departments' => $request->departments,
            'courses' => $request->courses,
            'year_levels' => $request->year_levels,
            'status' => 'Pending',
            'approval_status' => 'pending_document',
            'signed_document_path' => null,
        ]);

        Log::info('Event created without document', [
            'event_id' => $event->id,
            'approval_status' => 'pending_document'
        ]);

        return redirect()->route('president.events.index')
            ->with('success', 'Event created successfully. Please upload the signed document when ready.');
    }

    /**
     * Display the specified event.
     */
    public function show(Event $event)
    {
        if ($event->user_id !== $this->organizationId) {
            abort(403);
        }

        $event->load('eventType');
        
        $departments = Department::all();
        $courses = Course::all();

        // Calculate stats
        $totalStudents = \App\Models\EventStudent::where('event_id', $event->id)->count();
        $paidCount = \App\Models\EventStudent::where('event_id', $event->id)
            ->where('status', 'Paid')
            ->count();
        $pendingCount = \App\Models\EventStudent::where('event_id', $event->id)
            ->where('status', 'Pending')
            ->count();

        $stats = [
            'total_students' => $totalStudents,
            'paid' => $paidCount,
            'pending' => $pendingCount,
            'evaluations' => $event->evaluations()->count(),
            'evaluation_id' => $event->evaluations()->first()?->id,
            'can_be_finished' => $event->canBeFinished(), // Check if can be marked as finished
        ];

        return Inertia::render('President/Events/Show', [
            'event' => [
                'id' => $event->id,
                'event_name' => $event->event_name,
                'event_type' => $event->eventType,
                'event_date_start' => $event->event_date_start,
                'event_date_end' => $event->event_date_end,
                'description' => $event->description,
                'payment' => $event->payment,
                'event_fee' => $event->event_fee,
                'departments' => $event->departments,
                'courses' => $event->courses,
                'year_levels' => $event->year_levels,
                'signed_document_path' => $event->signed_document_path,
                'has_document' => !is_null($event->signed_document_path),
                'approval_status' => $event->approval_status,
                'status' => $event->status,
                'created_at' => $event->created_at,
                'updated_at' => $event->updated_at,
            ],
            'departments' => $departments,
            'courses' => $courses,
            'stats' => $stats,
        ]);
    }

    /**
     * Show the form for editing the specified event.
     */
    public function edit(Event $event)
    {
        if ($event->user_id !== $this->organizationId) {
            abort(403);
        }

        $settings = OrganizationSetting::where('organization_id', $this->organizationId)->first();
        
        $departments = Department::whereIn('id', $settings->assigned_departments ?? [])
            ->with(['courses' => function($query) use ($settings) {
                $query->whereIn('id', $settings->assigned_courses ?? []);
            }])
            ->get();

        $eventTypes = EventType::all();
        $yearLevels = ['1st Year', '2nd Year', '3rd Year', '4th Year'];

        return Inertia::render('President/Events/Edit', [
            'event' => $event,
            'departments' => $departments,
            'eventTypes' => $eventTypes,
            'yearLevels' => $yearLevels,
        ]);
    }

    /**
     * Update the specified event.
     */
    public function update(Request $request, Event $event)
    {
        if ($event->user_id !== $this->organizationId) {
            abort(403);
        }

        $request->validate([
            'event_name' => 'required|string|max:255',
            'event_type_id' => 'required|exists:event_types,id',
            'event_date_start' => 'required|date',
            'event_date_end' => 'required|date|after_or_equal:event_date_start',
            'departments' => 'required|array|min:1',
            'courses' => 'required|array|min:1',
            'year_levels' => 'required|array|min:1',
        ]);

        $eventType = EventType::find($request->event_type_id);

        $data = [
            'event_name' => $request->event_name,
            'event_type_id' => $request->event_type_id,
            'event_date_start' => $request->event_date_start,
            'event_date_end' => $request->event_date_end,
            'payment' => $eventType->requires_payment ? 'Payment' : 'No Payment',
            'event_fee' => $request->event_fee ?? 0,
            'departments' => $request->departments,
            'courses' => $request->courses,
            'year_levels' => $request->year_levels,
        ];

        if ($request->hasFile('signed_document')) {
            $request->validate([
                'signed_document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            ]);
            
            if ($event->signed_document_path) {
                Storage::disk('public')->delete($event->signed_document_path);
            }
            
            $path = $request->file('signed_document')->store('signed-documents', 'public');
            $data['signed_document_path'] = $path;
            $data['approval_status'] = 'pending_approval';
        }

        $event->update($data);

        return redirect()->route('president.events.index')
            ->with('success', 'Event updated successfully.');
    }

    /**
     * Upload signed document for an event.
     */
    public function uploadDocument(Request $request, Event $event)
    {
        if ($event->user_id !== $this->organizationId) {
            abort(403);
        }

        $request->validate([
            'signed_document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        if ($event->signed_document_path) {
            Storage::disk('public')->delete($event->signed_document_path);
        }

        $path = $request->file('signed_document')->store('signed-documents', 'public');
        
        $event->update([
            'signed_document_path' => $path,
            'approval_status' => 'pending_approval',
        ]);

        Log::info('Document uploaded for event', [
            'event_id' => $event->id,
            'new_status' => 'pending_approval'
        ]);

        return back()->with('success', 'Document uploaded successfully. Event is now pending adviser approval.');
    }

    /**
     * Mark event as finished
     */
    public function markAsFinished(Event $event)
    {
        if ($event->user_id !== $this->organizationId) {
            abort(403);
        }

        // Check if event can be marked as finished
        if (!$event->canBeFinished()) {
            return back()->with('error', 'Event cannot be marked as finished. It must be approved first.');
        }

        $event->update(['status' => 'Finished']);

        Log::info('Event marked as finished', [
            'event_id' => $event->id,
            'event_name' => $event->event_name
        ]);

        return back()->with('success', 'Event marked as finished successfully. You can now create evaluations and generate QR codes.');
    }

    /**
     * Remove the specified event.
     */
    public function destroy(Event $event)
    {
        if ($event->user_id !== $this->organizationId) {
            abort(403);
        }

        if ($event->signed_document_path) {
            Storage::disk('public')->delete($event->signed_document_path);
        }

        $event->delete();

        return redirect()->route('president.events.index')
            ->with('success', 'Event deleted successfully.');
    }
}