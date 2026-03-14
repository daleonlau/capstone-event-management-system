<?php

namespace App\Http\Controllers\Adviser;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\OrganizationUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

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

        // Get statistics
        $pendingApproval = Event::where('user_id', $this->organizationId)
            ->where('approval_status', 'pending_approval')
            ->count();

        $pendingDocument = Event::where('user_id', $this->organizationId)
            ->where('approval_status', 'pending_document')
            ->count();

        $approvedEvents = Event::where('user_id', $this->organizationId)
            ->where('approval_status', 'approved')
            ->count();

        $rejectedEvents = Event::where('user_id', $this->organizationId)
            ->where('approval_status', 'rejected')
            ->count();

        // Get events pending approval
        $pendingEvents = Event::where('user_id', $this->organizationId)
            ->where('approval_status', 'pending_approval')
            ->with(['eventType'])
            ->orderBy('created_at', 'asc')
            ->limit(5)
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'event_name' => $event->event_name,
                    'event_type' => $event->eventType,
                    'event_date_start' => $event->event_date_start,
                    'event_date_end' => $event->event_date_end,
                    'created_at' => $event->created_at->diffForHumans(),
                    'signed_document_path' => $event->signed_document_path,
                ];
            });

        $stats = [
            'pending_approval' => $pendingApproval,
            'pending_document' => $pendingDocument,
            'approved' => $approvedEvents,
            'rejected' => $rejectedEvents,
        ];

        return Inertia::render('Adviser/Dashboard', [
            'stats' => $stats,
            'pendingEvents' => $pendingEvents,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ]);
    }
}