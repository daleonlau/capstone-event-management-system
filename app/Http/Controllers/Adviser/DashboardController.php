<?php

namespace App\Http\Controllers\Adviser;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Evaluation;
use App\Models\AIAnalysis;
use App\Models\OrganizationUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
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

    /**
     * Safe JSON decode helper - handles both strings and arrays
     */
    private function safeJsonDecode($data, $default = [])
    {
        if (is_null($data)) return $default;
        if (is_array($data)) return $data;
        if (is_string($data)) {
            $decoded = json_decode($data, true);
            return is_array($decoded) ? $decoded : $default;
        }
        return $default;
    }

    public function index()
    {
        $user = Auth::guard('org_user')->user();

        // ==================== BASIC STATS (Organization Only) ====================
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

        $totalEvents = Event::where('user_id', $this->organizationId)->count();
        $finishedEvents = Event::where('user_id', $this->organizationId)
            ->where('status', 'Finished')
            ->count();

        // ==================== EVENTS PENDING APPROVAL ====================
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

        // ==================== AI INSIGHTS (Organization Only) ====================
        $aiInsightsList = [];
        
        try {
            // Get finished events that have AI insights
            $eventsWithInsights = Event::where('user_id', $this->organizationId)
                ->where('status', 'Finished')
                ->whereHas('evaluations.aiAnalyses')
                ->with(['evaluations.aiAnalyses' => function($q) {
                    $q->whereNull('event_date')->latest();
                }])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
            
            foreach ($eventsWithInsights as $event) {
                foreach ($event->evaluations as $evaluation) {
                    $analysis = $evaluation->aiAnalyses->first();
                    if ($analysis) {
                        $sentimentAnalysis = $this->safeJsonDecode($analysis->sentiment_analysis);
                        
                        $aiInsightsList[] = [
                            'id' => $evaluation->id,
                            'event_name' => $event->event_name,
                            'evaluation_title' => $evaluation->title,
                            'predicted_satisfaction' => $analysis->predicted_satisfaction,
                            'success_probability' => $analysis->success_probability,
                            'positive_percentage' => $sentimentAnalysis['positive_percentage'] ?? 0,
                            'negative_percentage' => $sentimentAnalysis['negative_percentage'] ?? 0,
                            'neutral_percentage' => $sentimentAnalysis['neutral_percentage'] ?? 0,
                            'total_responses' => $evaluation->total_responses,
                            'analyzed_at' => $analysis->analyzed_at,
                        ];
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error('Failed to fetch AI insights for adviser: ' . $e->getMessage());
        }

        // ==================== APPROVAL TRENDS (Last 6 months) ====================
        $approvalTrends = Event::where('user_id', $this->organizationId)
            ->whereNotNull('updated_at')
            ->where('updated_at', '>=', now()->subMonths(6))
            ->select(
                DB::raw('DATE_FORMAT(updated_at, "%Y-%m") as month'),
                DB::raw('SUM(CASE WHEN approval_status = "approved" THEN 1 ELSE 0 END) as approved'),
                DB::raw('SUM(CASE WHEN approval_status = "rejected" THEN 1 ELSE 0 END) as rejected')
            )
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        // ==================== AVERAGE APPROVAL TIME ====================
        $avgApprovalTime = Event::where('user_id', $this->organizationId)
            ->whereNotNull('approved_at')
            ->select(DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, approved_at)) as avg_hours'))
            ->value('avg_hours');
        
        $avgApprovalTime = $avgApprovalTime ? round($avgApprovalTime, 1) : 0;

        // ==================== RECENT APPROVALS ====================
        $recentApprovals = Event::where('user_id', $this->organizationId)
            ->whereNotNull('approved_at')
            ->orderBy('approved_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($event) {
                return [
                    'event_name' => $event->event_name,
                    'status' => $event->approval_status,
                    'approved_at' => $event->approved_at ? $event->approved_at->diffForHumans() : null,
                    'rejection_reason' => $event->rejection_reason,
                ];
            });

        // ==================== EVENT TYPE DISTRIBUTION ====================
        $eventTypeDistribution = Event::where('user_id', $this->organizationId)
            ->select('event_type_id', DB::raw('count(*) as total'))
            ->with(['eventType' => function($q) {
                $q->select('id', 'name');
            }])
            ->groupBy('event_type_id')
            ->get()
            ->map(function ($item) {
                return [
                    'type' => $item->eventType->name ?? 'Unknown',
                    'total' => $item->total,
                ];
            });

        // ==================== MONTHLY EVENT CREATION TRENDS ====================
        $monthlyEventTrends = Event::where('user_id', $this->organizationId)
            ->where('created_at', '>=', now()->subMonths(6))
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('count(*) as total')
            )
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        $stats = [
            'pending_approval' => $pendingApproval,
            'pending_document' => $pendingDocument,
            'approved' => $approvedEvents,
            'rejected' => $rejectedEvents,
            'total_events' => $totalEvents,
            'finished_events' => $finishedEvents,
            'avg_approval_time_hours' => $avgApprovalTime,
            'approval_rate' => $totalEvents > 0 ? round(($approvedEvents / $totalEvents) * 100, 1) : 0,
        ];

        return Inertia::render('Adviser/Dashboard', [
            'stats' => $stats,
            'pendingEvents' => $pendingEvents,
            'aiInsightsList' => $aiInsightsList,
            'approvalTrends' => $approvalTrends,
            'recentApprovals' => $recentApprovals,
            'eventTypeDistribution' => $eventTypeDistribution,
            'monthlyEventTrends' => $monthlyEventTrends,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ]);
    }
}