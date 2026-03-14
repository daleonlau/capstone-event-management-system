<?php

namespace App\Http\Controllers\Adviser;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Department;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    public function index(Request $request)
    {
        $query = Event::where('user_id', $this->organizationId)
            ->with(['eventType']);

        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('event_name', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%");
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->approval_status) {
            $query->where('approval_status', $request->approval_status);
        }

        $events = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->through(function ($event) {
                $departmentNames = [];
                if ($event->departments) {
                    $departments = Department::whereIn('id', $event->departments)->get();
                    $departmentNames = $departments->pluck('name')->toArray();
                }

                $courseNames = [];
                if ($event->courses) {
                    $courses = Course::whereIn('id', $event->courses)->get();
                    $courseNames = $courses->pluck('name')->toArray();
                }

                return [
                    'id' => $event->id,
                    'event_name' => $event->event_name,
                    'event_type' => $event->eventType,
                    'event_date_start' => $event->event_date_start,
                    'event_date_end' => $event->event_date_end,
                    'status' => $event->status,
                    'approval_status' => $event->approval_status,
                    'payment' => $event->payment,
                    'event_fee' => $event->event_fee,
                    'has_document' => !is_null($event->signed_document_path),
                    'department_names' => $departmentNames,
                    'course_names' => $courseNames,
                    'year_levels' => $event->year_levels,
                    'created_at' => $event->created_at->format('M d, Y'),
                ];
            });

        return Inertia::render('Adviser/Events/Index', [
            'events' => $events,
            'filters' => $request->only(['search', 'status', 'approval_status']),
        ]);
    }

    public function show(Event $event)
    {
        if ($event->user_id !== $this->organizationId) {
            abort(403);
        }

        $event->load(['eventType']);

        $departmentNames = [];
        if ($event->departments) {
            $departments = Department::whereIn('id', $event->departments)->get();
            $departmentNames = $departments->pluck('name')->toArray();
        }

        $courseNames = [];
        if ($event->courses) {
            $courses = Course::whereIn('id', $event->courses)->get();
            $courseNames = $courses->pluck('name')->toArray();
        }

        return Inertia::render('Adviser/Events/Show', [
            'event' => [
                'id' => $event->id,
                'event_name' => $event->event_name,
                'event_type' => $event->eventType,
                'event_date_start' => $event->event_date_start,
                'event_date_end' => $event->event_date_end,
                'status' => $event->status,
                'approval_status' => $event->approval_status,
                'payment' => $event->payment,
                'event_fee' => $event->event_fee,
                'department_names' => $departmentNames,
                'course_names' => $courseNames,
                'year_levels' => $event->year_levels,
                'has_document' => !is_null($event->signed_document_path),
                'signed_document_path' => $event->signed_document_path,
                'rejection_reason' => $event->rejection_reason,
                'created_at' => $event->created_at,
                'created_by' => $event->creator->name ?? 'Unknown',
            ],
            'document_url' => $event->signed_document_path 
                ? asset('storage/' . $event->signed_document_path) 
                : null,
        ]);
    }
}