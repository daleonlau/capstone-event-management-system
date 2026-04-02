<?php

namespace App\Http\Controllers\President;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventType;
use App\Models\OrganizationSetting;
use App\Models\Course;
use App\Models\Department;
use App\Models\EvaluationRequest;
use App\Models\Student;
use App\Models\EventStudent;
use App\Models\EventGuest;
use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Carbon\Carbon;

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

    private function syncEligibleStudents(Event $event)
    {
        try {
            $eligibleStudents = Student::where('user_id', $this->organizationId)
                ->whereIn('department', $event->departments)
                ->whereIn('course', $event->courses)
                ->whereIn('yearlevel', $event->year_levels)
                ->get();

            EventStudent::where('event_id', $event->id)->delete();

            foreach ($eligibleStudents as $student) {
                EventStudent::create([
                    'event_id' => $event->id,
                    'student_id' => $student->student_id,
                    'user_id' => $this->organizationId,
                    'status' => $event->payment === 'Payment' ? 'Pending' : 'Paid',
                    'amount_paid' => $event->payment === 'Payment' ? 0 : $event->event_fee,
                ]);
            }

            return $eligibleStudents->count();
        } catch (\Exception $e) {
            Log::error('Failed to sync students: ' . $e->getMessage());
            
            $this->logError('sync_students_failed', 'Failed to sync students for event: ' . $event->id, $e, [
                'event_id' => $event->id,
                'event_name' => $event->event_name,
            ]);
            
            return 0;
        }
    }

    private function getEligibleStudentsForEvent(Event $event)
    {
        $query = Student::where('user_id', $this->organizationId);
        
        if (!empty($event->departments) && is_array($event->departments)) {
            $departmentNames = Department::whereIn('id', $event->departments)->pluck('name')->toArray();
            if (!empty($departmentNames)) {
                $query->whereIn('department', $departmentNames);
            }
        }
        
        if (!empty($event->courses) && is_array($event->courses)) {
            $courseNames = Course::whereIn('id', $event->courses)->pluck('name')->toArray();
            if (!empty($courseNames)) {
                $query->whereIn('course', $courseNames);
            }
        }
        
        if (!empty($event->year_levels) && is_array($event->year_levels)) {
            $query->whereIn('yearlevel', $event->year_levels);
        }
        
        return $query->get();
    }

    public function syncAllEligibleStudents(Event $event)
    {
        if ($event->user_id !== $this->organizationId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        try {
            $eligibleStudents = $this->getEligibleStudentsForEvent($event);
            
            $existingStudents = EventStudent::where('event_id', $event->id)
                ->pluck('student_id')
                ->toArray();
            
            $studentsToAdd = array_diff($eligibleStudents->pluck('student_id')->toArray(), $existingStudents);
            $studentsToRemove = array_diff($existingStudents, $eligibleStudents->pluck('student_id')->toArray());
            
            $added = 0;
            $removed = 0;
            
            foreach ($eligibleStudents as $student) {
                if (in_array($student->student_id, $studentsToAdd)) {
                    EventStudent::updateOrCreate(
                        [
                            'event_id' => $event->id,
                            'student_id' => $student->student_id,
                        ],
                        [
                            'user_id' => $this->organizationId,
                            'status' => $event->payment === 'Payment' ? 'Pending' : 'Paid',
                            'amount_paid' => $event->payment === 'Payment' ? 0 : $event->event_fee,
                        ]
                    );
                    $added++;
                }
            }
            
            foreach ($studentsToRemove as $studentId) {
                EventStudent::where('event_id', $event->id)
                    ->where('student_id', $studentId)
                    ->delete();
                $removed++;
            }
            
            $this->logAction('sync_event_students', 'Synced eligible students for event: ' . $event->event_name, [
                'event_id' => $event->id,
                'event_name' => $event->event_name,
                'added' => $added,
                'removed' => $removed,
                'total_eligible' => $eligibleStudents->count(),
            ]);
            
            $updatedStudents = EventStudent::where('event_id', $event->id)
                ->with('student')
                ->get()
                ->map(function ($es) {
                    return [
                        'student_id' => $es->student->student_id,
                        'firstname' => $es->student->firstname,
                        'lastname' => $es->student->lastname,
                        'department' => $es->student->department,
                        'course' => $es->student->course,
                        'yearlevel' => $es->student->yearlevel,
                        'status' => $es->status,
                        'amount_paid' => $es->amount_paid,
                    ];
                });
            
            return response()->json([
                'success' => true,
                'students' => $updatedStudents,
                'total' => $updatedStudents->count(),
                'added' => $added,
                'removed' => $removed,
                'message' => "Updated student list: +{$added} added, -{$removed} removed"
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to sync event students: ' . $e->getMessage());
            
            $this->logError('sync_event_students_failed', 'Failed to sync students: ' . $e->getMessage(), $e, [
                'event_id' => $event->id,
                'event_name' => $event->event_name,
            ]);
            
            return response()->json([
                'error' => 'Failed to sync students: ' . $e->getMessage()
            ], 500);
        }
    }

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
                    'eligible_students' => EventStudent::where('event_id', $event->id)->count(),
                    'eligible_guests' => EventGuest::where('event_id', $event->id)->count(),
                    'created_at' => $event->created_at,
                ];
            });

        return Inertia::render('President/Events/Index', [
            'events' => $events
        ]);
    }

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

        DB::beginTransaction();

        try {
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

            $studentsSynced = $this->syncEligibleStudents($event);

            DB::commit();

            $this->logAction('create_event', 'Created event: ' . $event->event_name, [
                'event_id' => $event->id,
                'event_name' => $event->event_name,
                'event_type' => $eventType->name,
                'event_date_start' => $event->event_date_start,
                'event_date_end' => $event->event_date_end,
                'payment' => $event->payment,
                'event_fee' => $event->event_fee,
                'students_synced' => $studentsSynced,
            ]);

            return redirect()->route('president.events.index')
                ->with('success', 'Event created successfully. Please upload the signed document when ready.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create event: ' . $e->getMessage());
            
            $this->logError('create_event_failed', 'Failed to create event: ' . $e->getMessage(), $e, [
                'event_name' => $request->event_name,
                'event_type_id' => $request->event_type_id,
            ]);
            
            return back()->withErrors(['error' => 'Failed to create event.']);
        }
    }

    public function show(Event $event)
    {
        if ($event->user_id !== $this->organizationId) {
            abort(403);
        }
    
        $event->load('eventType');
        
        $departments = Department::all();
        $courses = Course::all();
    
        $eligibleStudents = EventStudent::where('event_id', $event->id)
            ->with('student')
            ->get()
            ->map(function ($es) {
                return [
                    'student_id' => $es->student->student_id,
                    'firstname' => $es->student->firstname,
                    'lastname' => $es->student->lastname,
                    'department' => $es->student->department,
                    'course' => $es->student->course,
                    'yearlevel' => $es->student->yearlevel,
                    'status' => $es->status,
                    'amount_paid' => $es->amount_paid,
                ];
            });
    
        $eligibleGuests = EventGuest::where('event_id', $event->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($guest) {
                return [
                    'id' => $guest->id,
                    'guest_id' => $guest->guest_id,
                    'name' => $guest->name,
                    'email' => $guest->email,
                    'agency_office' => $guest->agency_office,
                    'position' => $guest->position,
                    'status' => $guest->status,
                    'created_at' => $guest->created_at->format('Y-m-d'),
                ];
            });
    
        $totalEligibleStudents = $eligibleStudents->count();
        $totalEligibleGuests = $eligibleGuests->count();
        $paidCount = $eligibleStudents->where('status', 'Paid')->count();
        $pendingCount = $eligibleStudents->where('status', 'Pending')->count();
    
        $stats = [
            'total_students' => $totalEligibleStudents,
            'total_guests' => $totalEligibleGuests,
            'total_participants' => $totalEligibleStudents + $totalEligibleGuests,
            'paid' => $paidCount,
            'pending' => $pendingCount,
            'evaluations' => $event->evaluations()->count(),
            'evaluation_id' => $event->evaluations()->first()?->id,
            'can_be_finished' => $event->canBeFinished(),
        ];
    
        $evaluationRequest = EvaluationRequest::where('event_id', $event->id)->first();
    
        return Inertia::render('President/Events/Show', [
            'event' => [
                'id' => $event->id,
                'event_name' => $event->event_name,
                'event_type' => $event->eventType,
                // FIX: Format dates as Y-m-d without timezone
                'event_date_start' => $event->event_date_start instanceof Carbon ? $event->event_date_start->format('Y-m-d') : date('Y-m-d', strtotime($event->event_date_start)),
                'event_date_end' => $event->event_date_end instanceof Carbon ? $event->event_date_end->format('Y-m-d') : date('Y-m-d', strtotime($event->event_date_end)),
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
            'eligibleStudents' => $eligibleStudents,
            'eligibleGuests' => $eligibleGuests,
            'hasEvaluationRequest' => !is_null($evaluationRequest),
            'evaluationRequestStatus' => $evaluationRequest?->status,
            'evaluationRequest' => $evaluationRequest,
        ]);
    }

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

        DB::beginTransaction();

        try {
            $oldData = [
                'event_name' => $event->event_name,
                'event_type_id' => $event->event_type_id,
                'event_date_start' => $event->event_date_start,
                'event_date_end' => $event->event_date_end,
                'event_fee' => $event->event_fee,
            ];

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
            $studentsSynced = $this->syncEligibleStudents($event);

            DB::commit();

            $this->logAction('update_event', 'Updated event: ' . $event->event_name, [
                'event_id' => $event->id,
                'event_name' => $event->event_name,
                'changes' => $oldData['event_name'] !== $event->event_name ? ['name_changed' => true] : null,
                'students_synced' => $studentsSynced,
                'document_uploaded' => $request->hasFile('signed_document'),
            ]);

            return redirect()->route('president.events.index')
                ->with('success', 'Event updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update event: ' . $e->getMessage());
            
            $this->logError('update_event_failed', 'Failed to update event: ' . $e->getMessage(), $e, [
                'event_id' => $event->id,
                'event_name' => $event->event_name,
            ]);
            
            return back()->withErrors(['error' => 'Failed to update event.']);
        }
    }

    public function uploadDocument(Request $request, Event $event)
    {
        if ($event->user_id !== $this->organizationId) {
            abort(403);
        }

        $request->validate([
            'signed_document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        try {
            if ($event->signed_document_path) {
                Storage::disk('public')->delete($event->signed_document_path);
            }

            $path = $request->file('signed_document')->store('signed-documents', 'public');
            
            $event->update([
                'signed_document_path' => $path,
                'approval_status' => 'pending_approval',
            ]);

            $this->logAction('upload_event_document', 'Uploaded signed document for event: ' . $event->event_name, [
                'event_id' => $event->id,
                'event_name' => $event->event_name,
                'document_size' => $request->file('signed_document')->getSize(),
            ]);

            return back()->with('success', 'Document uploaded successfully. Event is now pending adviser approval.');
        } catch (\Exception $e) {
            $this->logError('upload_document_failed', 'Failed to upload document: ' . $e->getMessage(), $e, [
                'event_id' => $event->id,
                'event_name' => $event->event_name,
            ]);
            
            return back()->with('error', 'Failed to upload document.');
        }
    }

    public function markAsFinished(Event $event)
    {
        if ($event->user_id !== $this->organizationId) {
            abort(403);
        }

        if (!$event->canBeFinished()) {
            return back()->with('error', 'Event cannot be marked as finished. It must be approved first.');
        }

        try {
            $oldStatus = $event->status;
            $event->update(['status' => 'Finished']);

            $this->logAction('mark_event_finished', 'Marked event as finished: ' . $event->event_name, [
                'event_id' => $event->id,
                'event_name' => $event->event_name,
                'old_status' => $oldStatus,
            ]);

            return back()->with('success', 'Event marked as finished successfully. You can now request evaluation services.');
        } catch (\Exception $e) {
            $this->logError('mark_event_finished_failed', 'Failed to mark event as finished: ' . $e->getMessage(), $e, [
                'event_id' => $event->id,
                'event_name' => $event->event_name,
            ]);
            
            return back()->with('error', 'Failed to mark event as finished.');
        }
    }

   /**
 * Request evaluation service for an event - FIXED for Philippines timezone
 */
public function requestEvaluation(Request $request, Event $event)
{
    if ($event->user_id !== $this->organizationId) {
        abort(403);
    }

    if (!$event->canRequestEvaluation()) {
        return back()->with('error', 'This event cannot request evaluation service.');
    }

    // Generate ALL inclusive dates between event_date_start and event_date_end
    // FIX: Use only the date part, no time
    $inclusiveDates = [];
    
    // Parse dates as Carbon and format as Y-m-d only
    $start = Carbon::parse($event->event_date_start)->startOfDay();
    $end = Carbon::parse($event->event_date_end)->startOfDay();
    
    for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
        $inclusiveDates[] = $date->format('Y-m-d');
    }

    $request->validate([
        'title' => 'required|string|max:255',
        'venue' => 'required|string|max:255',
        'speaker_name' => 'required|string|max:255',
        'topics' => 'required|array|min:1',
        'topics.*' => 'required|string',
        'has_food' => 'boolean',
        'notes' => 'nullable|string',
    ]);

    try {
        $evaluationRequest = EvaluationRequest::create([
            'event_id' => $event->id,
            'organization_id' => $this->organizationId,
            'requested_by' => Auth::guard('org_user')->id(),
            'title' => $request->title,
            'activity_date' => $event->event_date_start, // This will be saved as is
            'event_dates' => $inclusiveDates, // This will be array of Y-m-d strings
            'venue' => $request->venue,
            'speaker_name' => $request->speaker_name,
            'topics' => $request->topics,
            'has_food' => $request->has_food ?? false,
            'notes' => $request->notes,
            'status' => 'pending',
        ]);

        $this->logAction('request_evaluation', 'Requested evaluation service for event: ' . $event->event_name, [
            'event_id' => $event->id,
            'event_name' => $event->event_name,
            'evaluation_request_id' => $evaluationRequest->id,
            'title' => $request->title,
            'venue' => $request->venue,
            'speaker_name' => $request->speaker_name,
            'topics_count' => count($request->topics),
            'event_dates' => $inclusiveDates,
            'event_dates_count' => count($inclusiveDates),
        ]);

        return redirect()->route('president.events.show', $event->id)
            ->with('success', 'Evaluation service request submitted successfully with ' . count($inclusiveDates) . ' event dates.');
    } catch (\Exception $e) {
        $this->logError('request_evaluation_failed', 'Failed to request evaluation: ' . $e->getMessage(), $e, [
            'event_id' => $event->id,
            'event_name' => $event->event_name,
        ]);
        
        return back()->with('error', 'Failed to submit evaluation request: ' . $e->getMessage());
    }
}
    public function destroy(Event $event)
    {
        if ($event->user_id !== $this->organizationId) {
            abort(403);
        }

        try {
            $eventName = $event->event_name;
            $eventId = $event->id;
            
            if ($event->signed_document_path) {
                Storage::disk('public')->delete($event->signed_document_path);
            }

            $event->delete();

            $this->logAction('delete_event', 'Deleted event: ' . $eventName, [
                'event_id' => $eventId,
                'event_name' => $eventName,
            ]);

            return redirect()->route('president.events.index')
                ->with('success', 'Event deleted successfully.');
        } catch (\Exception $e) {
            $this->logError('delete_event_failed', 'Failed to delete event: ' . $e->getMessage(), $e, [
                'event_id' => $event->id,
                'event_name' => $event->event_name,
            ]);
            
            return redirect()->route('president.events.index')
                ->with('error', 'Failed to delete event.');
        }
    }

    public function refreshEligibleStudents(Event $event)
    {
        if ($event->user_id !== $this->organizationId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        try {
            $added = $this->syncEligibleStudents($event);
            
            $eligibleStudents = EventStudent::where('event_id', $event->id)
                ->with('student')
                ->get()
                ->map(function ($es) {
                    return [
                        'student_id' => $es->student->student_id,
                        'firstname' => $es->student->firstname,
                        'lastname' => $es->student->lastname,
                        'department' => $es->student->department,
                        'course' => $es->student->course,
                        'yearlevel' => $es->student->yearlevel,
                        'status' => $es->status,
                        'amount_paid' => $es->amount_paid,
                    ];
                });
            
            $this->logAction('refresh_eligible_students', 'Refreshed eligible students for event: ' . $event->event_name, [
                'event_id' => $event->id,
                'event_name' => $event->event_name,
                'students_added' => $added,
            ]);
            
            return response()->json([
                'success' => true,
                'students' => $eligibleStudents,
                'total' => $eligibleStudents->count(),
                'added' => $added,
            ]);
        } catch (\Exception $e) {
            $this->logError('refresh_eligible_students_failed', 'Failed to refresh students: ' . $e->getMessage(), $e, [
                'event_id' => $event->id,
                'event_name' => $event->event_name,
            ]);
            
            return response()->json([
                'error' => 'Failed to refresh students: ' . $e->getMessage()
            ], 500);
        }
    }

    public function showGuests(Event $event)
    {
        if ($event->user_id !== $this->organizationId) {
            abort(403);
        }
        
        $guests = EventGuest::where('event_id', $event->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($guest) {
                return [
                    'id' => $guest->id,
                    'guest_id' => $guest->guest_id,
                    'name' => $guest->name,
                    'email' => $guest->email,
                    'agency_office' => $guest->agency_office,
                    'position' => $guest->position,
                    'status' => $guest->status,
                    'created_at' => $guest->created_at->format('Y-m-d'),
                ];
            });
        
        return Inertia::render('President/Events/Guests', [
            'event' => [
                'id' => $event->id,
                'event_name' => $event->event_name,
            ],
            'guests' => $guests,
        ]);
    }

    public function addGuest(Request $request, Event $event)
    {
        if ($event->user_id !== $this->organizationId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'agency_office' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
        ]);
        
        try {
            $guest = EventGuest::create([
                'event_id' => $event->id,
                'guest_id' => EventGuest::generateGuestId($event->id),
                'name' => $request->name,
                'email' => $request->email,
                'agency_office' => $request->agency_office,
                'position' => $request->position,
                'status' => 'Pending',
            ]);
            
            $this->logAction('add_event_guest', 'Added guest to event: ' . $event->event_name, [
                'event_id' => $event->id,
                'event_name' => $event->event_name,
                'guest_name' => $guest->name,
                'guest_email' => $guest->email,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Guest added successfully',
                'guest' => [
                    'id' => $guest->id,
                    'guest_id' => $guest->guest_id,
                    'name' => $guest->name,
                    'email' => $guest->email,
                ],
            ]);
        } catch (\Exception $e) {
            $this->logError('add_guest_failed', 'Failed to add guest: ' . $e->getMessage(), $e, [
                'event_id' => $event->id,
                'event_name' => $event->event_name,
                'guest_name' => $request->name,
                'guest_email' => $request->email,
            ]);
            
            return response()->json(['error' => 'Failed to add guest'], 500);
        }
    }

    public function bulkAddGuests(Request $request, Event $event)
    {
        if ($event->user_id !== $this->organizationId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:5120',
        ]);
        
        $file = $request->file('csv_file');
        $handle = fopen($file->getPathname(), 'r');
        $headers = fgetcsv($handle);
        
        $successCount = 0;
        $errorCount = 0;
        $errors = [];
        
        while (($row = fgetcsv($handle)) !== false) {
            $data = array_combine($headers, $row);
            
            try {
                EventGuest::create([
                    'event_id' => $event->id,
                    'guest_id' => EventGuest::generateGuestId($event->id),
                    'name' => $data['name'] ?? null,
                    'email' => $data['email'] ?? null,
                    'agency_office' => $data['agency_office'] ?? null,
                    'position' => $data['position'] ?? null,
                    'status' => 'Pending',
                ]);
                $successCount++;
            } catch (\Exception $e) {
                $errorCount++;
                $errors[] = "Row with email {$data['email']}: " . $e->getMessage();
            }
        }
        
        fclose($handle);
        
        $this->logAction('bulk_add_event_guests', 'Bulk added guests to event: ' . $event->event_name, [
            'event_id' => $event->id,
            'event_name' => $event->event_name,
            'success_count' => $successCount,
            'error_count' => $errorCount,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => "Imported {$successCount} guests, {$errorCount} failed",
            'stats' => [
                'success' => $successCount,
                'errors' => $errorCount,
                'error_details' => $errors,
            ],
        ]);
    }

    public function deleteGuest(Event $event, EventGuest $guest)
    {
        if ($event->user_id !== $this->organizationId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        try {
            $guestName = $guest->name;
            $guest->delete();
            
            $this->logAction('delete_event_guest', 'Deleted guest from event: ' . $event->event_name, [
                'event_id' => $event->id,
                'event_name' => $event->event_name,
                'guest_name' => $guestName,
            ]);
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            $this->logError('delete_guest_failed', 'Failed to delete guest: ' . $e->getMessage(), $e, [
                'event_id' => $event->id,
                'event_name' => $event->event_name,
                'guest_name' => $guest->name,
            ]);
            
            return response()->json(['error' => 'Failed to delete guest'], 500);
        }
    }

    public function downloadGuestTemplate(Event $event)
    {
        $headers = ['name', 'email', 'agency_office', 'position'];
        $sampleRow = ['Juan Dela Cruz', 'juan@example.com', 'Company Name', 'Manager'];
        
        $callback = function() use ($headers, $sampleRow) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");
            fputcsv($file, $headers);
            fputcsv($file, $sampleRow);
            fclose($file);
        };
        
        $filename = 'guest_template_' . $event->id . '.csv';
        
        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private function logAction($action, $description, $details = [])
    {
        try {
            $user = Auth::guard('org_user')->user();
            if ($user) {
                LogService::action($action, $description, $user, $details);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to log action: ' . $e->getMessage());
        }
    }
    
    private function logError($action, $description, $exception = null, $details = [])
    {
        try {
            LogService::error($action, $description, $exception, $details);
        } catch (\Exception $e) {
            Log::warning('Failed to log error: ' . $e->getMessage());
        }
    }
}