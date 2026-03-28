<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use App\Models\EvaluationRequest;
use App\Models\Event;
use App\Models\OrganizationUser;
use App\Models\EvaluationResponse;
use App\Models\AIAnalysis;
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

        // Get recent evaluations
        $recentEvaluations = [];
        try {
            $recentEvaluations = Evaluation::with(['event', 'event.creator'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($evaluation) {
                    $event = $evaluation->event;
                    $eventDates = $evaluation->event_dates ?: [];
                    $totalStudents = \App\Models\EventStudent::where('event_id', $event->id)->count();
                    $totalGuests = \App\Models\EventGuest::where('event_id', $event->id)->count();
                    $totalExpected = ($totalStudents * max(count($eventDates), 1)) + $totalGuests;
                    $responseRate = $totalExpected > 0 ? round(($evaluation->total_responses / $totalExpected) * 100, 1) : 0;
                    
                    return [
                        'id' => $evaluation->id,
                        'title' => $evaluation->title ?? 'N/A',
                        'event_name' => $evaluation->event->event_name ?? 'N/A',
                        'organization' => $evaluation->event->creator->name ?? 'N/A',
                        'status' => $evaluation->status ?? 'draft',
                        'responses' => $evaluation->total_responses ?? 0,
                        'expected' => $totalExpected,
                        'response_rate' => $responseRate,
                        'created_at' => $evaluation->created_at ? $evaluation->created_at->format('Y-m-d') : 'N/A',
                    ];
                })->toArray();
        } catch (\Exception $e) {
            $recentEvaluations = [];
        }

        // Get pending evaluation requests
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

        // Get AI Insights for closed evaluations - ONE CARD PER EVALUATION
        $aiInsightsList = [];
        try {
            // Get all closed evaluations that have responses
            $closedEvaluations = Evaluation::where('status', 'closed')
                ->where('total_responses', '>', 0)
                ->orderBy('created_at', 'desc')
                ->get();
            
            foreach ($closedEvaluations as $evaluation) {
                // Get all insights for this evaluation (overall and per date)
                $allInsights = AIAnalysis::where('evaluation_id', $evaluation->id)->get();
                
                if ($allInsights->count() > 0) {
                    $overallInsight = $allInsights->whereNull('event_date')->first();
                    $dateInsights = $allInsights->whereNotNull('event_date')->map(function ($insight) {
                        return [
                            'event_date' => $insight->event_date,
                            'formatted_date' => Carbon::parse($insight->event_date)->format('F d, Y'),
                            'summary' => $insight->summary,
                            'strengths' => json_decode($insight->strengths, true) ?: [],
                            'weaknesses' => json_decode($insight->weaknesses, true) ?: [],
                            'recommendations' => json_decode($insight->recommendations, true) ?: [],
                            'predicted_satisfaction' => $insight->predicted_satisfaction,
                            'success_probability' => $insight->success_probability,
                            'response_rate' => $insight->response_rate,
                            'total_respondents' => $insight->total_respondents,
                            'analyzed_at' => $insight->analyzed_at,
                            'category_breakdown' => json_decode($insight->category_breakdown, true) ?: [],
                            'feature_importance' => json_decode($insight->feature_importance, true) ?: [],
                            'sentiment_analysis' => json_decode($insight->sentiment_analysis, true) ?: [],
                            'what_if_analysis' => json_decode($insight->what_if_analysis, true) ?: [],
                            'critical_factors' => json_decode($insight->critical_factors, true) ?: [],
                            'low_scoring_questions' => json_decode($insight->low_scoring_questions, true) ?: [],
                            'positive_percentage' => json_decode($insight->sentiment_analysis, true)['positive_percentage'] ?? 0,
                            'negative_percentage' => json_decode($insight->sentiment_analysis, true)['negative_percentage'] ?? 0,
                            'neutral_percentage' => json_decode($insight->sentiment_analysis, true)['neutral_percentage'] ?? 0,
                        ];
                    })->toArray();
                    
                    // Get event details for response rate calculation
                    $event = $evaluation->event;
                    $eventDates = $evaluation->event_dates ?: [];
                    $totalStudents = \App\Models\EventStudent::where('event_id', $event->id)->count();
                    $totalGuests = \App\Models\EventGuest::where('event_id', $event->id)->count();
                    $totalExpected = ($totalStudents * max(count($eventDates), 1)) + $totalGuests;
                    $responseRate = $totalExpected > 0 ? round(($evaluation->total_responses / $totalExpected) * 100, 1) : 0;
                    
                    $overallData = [
                        'id' => $evaluation->id,
                        'evaluation_id' => $evaluation->id,
                        'event_name' => $evaluation->event->event_name,
                        'evaluation_title' => $evaluation->title,
                        'total_responses' => $evaluation->total_responses,
                        'total_expected' => $totalExpected,
                        'response_rate' => $responseRate,
                        'has_insights' => true,
                        'overall' => $overallInsight ? [
                            'summary' => $overallInsight->summary,
                            'strengths' => json_decode($overallInsight->strengths, true) ?: [],
                            'weaknesses' => json_decode($overallInsight->weaknesses, true) ?: [],
                            'recommendations' => json_decode($overallInsight->recommendations, true) ?: [],
                            'predicted_satisfaction' => $overallInsight->predicted_satisfaction,
                            'success_probability' => $overallInsight->success_probability,
                            'response_rate' => $overallInsight->response_rate,
                            'total_respondents' => $overallInsight->total_respondents,
                            'analyzed_at' => $overallInsight->analyzed_at,
                            'category_breakdown' => json_decode($overallInsight->category_breakdown, true) ?: [],
                            'feature_importance' => json_decode($overallInsight->feature_importance, true) ?: [],
                            'sentiment_analysis' => json_decode($overallInsight->sentiment_analysis, true) ?: [],
                            'what_if_analysis' => json_decode($overallInsight->what_if_analysis, true) ?: [],
                            'critical_factors' => json_decode($overallInsight->critical_factors, true) ?: [],
                            'low_scoring_questions' => json_decode($overallInsight->low_scoring_questions, true) ?: [],
                            'positive_percentage' => json_decode($overallInsight->sentiment_analysis, true)['positive_percentage'] ?? 0,
                            'negative_percentage' => json_decode($overallInsight->sentiment_analysis, true)['negative_percentage'] ?? 0,
                            'neutral_percentage' => json_decode($overallInsight->sentiment_analysis, true)['neutral_percentage'] ?? 0,
                        ] : null,
                        'date_insights' => $dateInsights,
                        'analyzed_at' => $overallInsight ? $overallInsight->analyzed_at : null,
                    ];
                    
                    $aiInsightsList[] = $overallData;
                }
            }
        } catch (\Exception $e) {
            \Log::error('Failed to fetch AI insights: ' . $e->getMessage());
            $aiInsightsList = [];
        }

        // Get monthly evaluation data for chart
        $monthlyEvaluations = [];
        $months = [];
        
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

        // Get top performing organizations
        $topOrganizations = [];
        try {
            $topOrganizations = OrganizationUser::select('organization_id', DB::raw('COUNT(*) as event_count'))
                ->join('events', 'organization_users.organization_id', '=', 'events.user_id')
                ->groupBy('organization_id')
                ->orderBy('event_count', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($org) {
                    $user = User::find($org->organization_id);
                    return [
                        'name' => $user ? $user->name : 'Organization ' . $org->organization_id,
                        'event_count' => $org->event_count,
                    ];
                })->toArray();
        } catch (\Exception $e) {
            $topOrganizations = [];
        }

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recentEvaluations' => $recentEvaluations,
            'pendingRequests' => $pendingRequests,
            'monthlyEvaluations' => $monthlyEvaluations,
            'monthlyResponses' => $monthlyResponses,
            'statusDistribution' => $statusDistribution,
            'topOrganizations' => $topOrganizations,
            'aiInsightsList' => $aiInsightsList,
            'adminName' => $adminName,
        ]);
    }
}