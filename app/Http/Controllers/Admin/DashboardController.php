<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use App\Models\EvaluationRequest;
use App\Models\Event;
use App\Models\OrganizationUser;
use App\Models\EvaluationResponse;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // Get the logged in admin user
        $admin = auth()->user();
        $adminName = $admin ? $admin->name : 'Admin';

        // Get statistics with fallbacks
        $stats = [
            'total_evaluations' => Evaluation::count(),
            'active_evaluations' => Evaluation::where('status', 'active')->count(),
            'closed_evaluations' => Evaluation::where('status', 'closed')->count(),
            'draft_evaluations' => Evaluation::where('status', 'draft')->count(),
            'total_organizations' => OrganizationUser::whereNotNull('organization_id')->distinct('organization_id')->count('organization_id'),
            'total_events' => Event::count(),
            'total_responses' => EvaluationResponse::count(),
            'pending_requests' => EvaluationRequest::where('status', 'pending')->count(),
            'completed_requests' => EvaluationRequest::where('status', 'completed')->count(),
        ];

        // Get recent evaluations with error handling
        $recentEvaluations = [];
        try {
            $recentEvaluations = Evaluation::with(['event', 'event.creator'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($evaluation) {
                    return [
                        'id' => $evaluation->id,
                        'title' => $evaluation->title ?? 'N/A',
                        'event_name' => $evaluation->event->event_name ?? 'N/A',
                        'organization' => $evaluation->event->creator->name ?? 'N/A',
                        'status' => $evaluation->status ?? 'draft',
                        'responses' => $evaluation->total_responses ?? 0,
                        'created_at' => $evaluation->created_at ? $evaluation->created_at->format('Y-m-d') : 'N/A',
                    ];
                })->toArray();
        } catch (\Exception $e) {
            $recentEvaluations = [];
        }

        // Get pending evaluation requests with error handling
        $pendingRequests = [];
        try {
            $pendingRequests = EvaluationRequest::with(['event', 'organization', 'requestedBy'])
                ->where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($request) {
                    return [
                        'id' => $request->id,
                        'event_name' => $request->event->event_name ?? 'N/A',
                        'organization' => $request->organization->name ?? 'N/A',
                        'title' => $request->title ?? 'N/A',
                        'requested_by' => $request->requestedBy->name ?? 'N/A',
                        'activity_date' => $request->activity_date ? Carbon::parse($request->activity_date)->format('M d, Y') : 'N/A',
                        'created_at' => $request->created_at ? $request->created_at->format('M d, Y') : 'N/A',
                    ];
                })->toArray();
        } catch (\Exception $e) {
            $pendingRequests = [];
        }

        // Get monthly evaluation data for chart
        $monthlyEvaluations = [];
        $months = [];
        
        // Get last 6 months
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthKey = $month->format('Y-m');
            $monthLabel = $month->format('M Y');
            $months[$monthLabel] = $monthKey;
            $monthlyEvaluations[$monthLabel] = 0;
        }
        
        try {
            $evaluationsData = Evaluation::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as total')
            )
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('month')
            ->get();
            
            foreach ($evaluationsData as $data) {
                foreach ($months as $label => $monthKey) {
                    if ($data->month == $monthKey) {
                        $monthlyEvaluations[$label] = $data->total;
                        break;
                    }
                }
            }
        } catch (\Exception $e) {
            $monthlyEvaluations = array_fill_keys(array_keys($months), 0);
        }

        // Get monthly response data for chart
        $monthlyResponses = [];
        foreach ($months as $label => $monthKey) {
            $monthlyResponses[$label] = 0;
        }
        
        try {
            $responsesData = EvaluationResponse::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as total')
            )
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('month')
            ->get();
            
            foreach ($responsesData as $data) {
                foreach ($months as $label => $monthKey) {
                    if ($data->month == $monthKey) {
                        $monthlyResponses[$label] = $data->total;
                        break;
                    }
                }
            }
        } catch (\Exception $e) {
            $monthlyResponses = array_fill_keys(array_keys($months), 0);
        }

        // Get evaluation status distribution
        $statusDistribution = [
            'Active' => Evaluation::where('status', 'active')->count(),
            'Closed' => Evaluation::where('status', 'closed')->count(),
            'Draft' => Evaluation::where('status', 'draft')->count(),
        ];

        // Get top performing organizations with error handling
        $topOrganizations = [];
        try {
            $topOrganizations = OrganizationUser::select('organization_id', DB::raw('COUNT(*) as event_count'))
                ->join('events', 'organization_users.organization_id', '=', 'events.user_id')
                ->groupBy('organization_id')
                ->orderBy('event_count', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($org) {
                    return [
                        'name' => 'Organization ' . $org->organization_id,
                        'event_count' => $org->event_count,
                    ];
                })->toArray();
        } catch (\Exception $e) {
            $topOrganizations = [];
        }

        // Get recent responses with error handling
        $recentResponses = [];
        try {
            $recentResponses = EvaluationResponse::with(['evaluation', 'evaluation.event'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($response) {
                    return [
                        'id' => $response->id,
                        'student_name' => $response->name ?? 'N/A',
                        'student_id' => $response->student_id ?? 'N/A',
                        'evaluation_title' => $response->evaluation->title ?? 'N/A',
                        'event_name' => $response->evaluation->event->event_name ?? 'N/A',
                        'submitted_at' => $response->created_at ? $response->created_at->format('M d, Y H:i') : 'N/A',
                    ];
                })->toArray();
        } catch (\Exception $e) {
            $recentResponses = [];
        }

        // Get overall satisfaction average
        $avgSatisfaction = 0;
        try {
            $allResponses = EvaluationResponse::whereNotNull('likert_responses')->get();
            $totalRatings = 0;
            $sumRatings = 0;
            
            foreach ($allResponses as $response) {
                $likertResponses = is_string($response->likert_responses) 
                    ? json_decode($response->likert_responses, true) 
                    : ($response->likert_responses ?? []);
                
                if (is_array($likertResponses)) {
                    foreach ($likertResponses as $rating) {
                        if (is_numeric($rating)) {
                            $sumRatings += $rating;
                            $totalRatings++;
                        }
                    }
                }
            }
            
            if ($totalRatings > 0) {
                $avgSatisfaction = round($sumRatings / $totalRatings, 2);
            }
        } catch (\Exception $e) {
            $avgSatisfaction = 0;
        }

        // Get response rate
        $responseRate = 0;
        try {
            $eventsWithResponses = DB::table('evaluations')
                ->where('total_responses', '>', 0)
                ->count();
            $totalEvents = Event::count();
            $responseRate = $totalEvents > 0 ? round(($eventsWithResponses / $totalEvents) * 100, 1) : 0;
        } catch (\Exception $e) {
            $responseRate = 0;
        }

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recentEvaluations' => $recentEvaluations,
            'pendingRequests' => $pendingRequests,
            'monthlyEvaluations' => $monthlyEvaluations,
            'monthlyResponses' => $monthlyResponses,
            'statusDistribution' => $statusDistribution,
            'topOrganizations' => $topOrganizations,
            'recentResponses' => $recentResponses,
            'avgSatisfaction' => $avgSatisfaction,
            'responseRate' => $responseRate,
            'adminName' => $adminName,
        ]);
    }
}