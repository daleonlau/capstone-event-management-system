<?php

namespace App\Http\Controllers\President;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Student;
use App\Models\Evaluation;
use App\Models\EvaluationResponse;
use App\Models\AIAnalysis;
use App\Models\EventStudent;
use App\Models\EventGuest;
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
        $totalEvents = Event::where('user_id', $this->organizationId)->count();
        $pendingEvents = Event::where('user_id', $this->organizationId)
            ->where('status', 'Pending')->count();
        $approvedEvents = Event::where('user_id', $this->organizationId)
            ->where('status', 'Approved')->count();
        $finishedEvents = Event::where('user_id', $this->organizationId)
            ->where('status', 'Finished')->count();
        $totalStudents = Student::where('user_id', $this->organizationId)->count();

        // Events by approval status
        $pendingDocument = Event::where('user_id', $this->organizationId)
            ->where('approval_status', 'pending_document')->count();
        $pendingApproval = Event::where('user_id', $this->organizationId)
            ->where('approval_status', 'pending_approval')->count();
        $rejectedEvents = Event::where('user_id', $this->organizationId)
            ->where('approval_status', 'rejected')->count();

        // ==================== RECENT EVENTS ====================
        $recentEvents = Event::where('user_id', $this->organizationId)
            ->with(['eventType'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'event_name' => $event->event_name,
                    'event_type' => $event->eventType,
                    'event_date_start' => $event->event_date_start,
                    'status' => $event->status,
                    'approval_status' => $event->approval_status,
                    'created_at' => $event->created_at,
                ];
            });

        // ==================== STUDENTS BY DEPARTMENT ====================
        $studentsByDepartment = Student::where('user_id', $this->organizationId)
            ->select('department', DB::raw('count(*) as total'))
            ->groupBy('department')
            ->get()
            ->map(function ($item) {
                return [
                    'department' => $item->department ?? 'Unspecified',
                    'total' => $item->total,
                ];
            });

        // ==================== EVENT TRENDS (Last 6 months) ====================
        $eventTrends = Event::where('user_id', $this->organizationId)
            ->where('created_at', '>=', now()->subMonths(6))
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('count(*) as total')
            )
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        // ==================== EVALUATION RESPONSE TRENDS ====================
        $evaluationTrends = Evaluation::whereHas('event', function($q) {
                $q->where('user_id', $this->organizationId);
            })
            ->where('created_at', '>=', now()->subMonths(6))
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('SUM(total_responses) as total_responses')
            )
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        // ==================== AI INSIGHTS (Organization Only) ====================
        $aiInsightsList = [];
        
        try {
            // Get finished events that have evaluations with responses
            $finishedEventsWithEvals = Event::where('user_id', $this->organizationId)
                ->where('status', 'Finished')
                ->whereHas('evaluations', function($q) {
                    $q->where('total_responses', '>', 0);
                })
                ->with(['evaluations'])
                ->orderBy('created_at', 'desc')
                ->get();
            
            foreach ($finishedEventsWithEvals as $event) {
                foreach ($event->evaluations as $evaluation) {
                    // Get AI analysis for this evaluation
                    $aiAnalysis = AIAnalysis::where('evaluation_id', $evaluation->id)
                        ->whereNull('event_date')
                        ->latest()
                        ->first();
                    
                    if ($aiAnalysis) {
                        // Safely decode JSON data
                        $sentimentAnalysis = $this->safeJsonDecode($aiAnalysis->sentiment_analysis);
                        $recommendations = $this->safeJsonDecode($aiAnalysis->recommendations);
                        $strengths = $this->safeJsonDecode($aiAnalysis->strengths);
                        $weaknesses = $this->safeJsonDecode($aiAnalysis->weaknesses);
                        $categoryBreakdown = $this->safeJsonDecode($aiAnalysis->category_breakdown);
                        
                        // Calculate response rate
                        $eventDates = $evaluation->event_dates ?: [];
                        $totalStudentsCount = EventStudent::where('event_id', $event->id)->count();
                        $totalGuestsCount = EventGuest::where('event_id', $event->id)->count();
                        $totalExpected = ($totalStudentsCount * max(count($eventDates), 1)) + $totalGuestsCount;
                        $responseRate = $totalExpected > 0 ? round(($evaluation->total_responses / $totalExpected) * 100, 1) : 0;
                        
                        $aiInsightsList[] = [
                            'id' => $evaluation->id,
                            'evaluation_id' => $evaluation->id,
                            'event_id' => $event->id,
                            'event_name' => $event->event_name,
                            'evaluation_title' => $evaluation->title,
                            'total_responses' => $evaluation->total_responses,
                            'response_rate' => $responseRate,
                            'analyzed_at' => $aiAnalysis->analyzed_at,
                            'predicted_satisfaction' => $aiAnalysis->predicted_satisfaction,
                            'success_probability' => $aiAnalysis->success_probability,
                            'positive_percentage' => $sentimentAnalysis['positive_percentage'] ?? 0,
                            'negative_percentage' => $sentimentAnalysis['negative_percentage'] ?? 0,
                            'neutral_percentage' => $sentimentAnalysis['neutral_percentage'] ?? 0,
                            'strengths' => array_slice($strengths, 0, 5),
                            'weaknesses' => array_slice($weaknesses, 0, 5),
                            'recommendations' => is_array($recommendations) ? array_slice($recommendations, 0, 5) : [],
                            'category_breakdown' => $categoryBreakdown,
                            'common_themes' => array_slice($sentimentAnalysis['common_themes'] ?? [], 0, 8),
                            'sample_positive_comments' => array_slice($sentimentAnalysis['positive_comments'] ?? [], 0, 5),
                            'sample_negative_comments' => array_slice($sentimentAnalysis['negative_comments'] ?? [], 0, 5),
                        ];
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error('Failed to fetch AI insights for president: ' . $e->getMessage());
        }

        // ==================== OVERALL SATISFACTION SCORE ====================
        $overallSatisfaction = AIAnalysis::whereHas('evaluation.event', function($q) {
                $q->where('user_id', $this->organizationId);
            })
            ->whereNull('event_date')
            ->avg('predicted_satisfaction');
        
        $overallSatisfaction = $overallSatisfaction ? round($overallSatisfaction, 2) : 0;

        // ==================== RECENT ACTIVITIES ====================
        $recentActivities = collect();
        
        // Add recent events
        Event::where('user_id', $this->organizationId)
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get()
            ->each(function ($event) use ($recentActivities) {
                $recentActivities->push([
                    'type' => 'event_created',
                    'description' => "Created event '{$event->event_name}'",
                    'time' => $event->created_at->diffForHumans(),
                    'icon' => 'calendar',
                    'status' => $event->approval_status,
                ]);
            });

        // Add recent students
        Student::where('user_id', $this->organizationId)
            ->orderBy('created_at', 'desc')
            ->limit(2)
            ->get()
            ->each(function ($student) use ($recentActivities) {
                $recentActivities->push([
                    'type' => 'student_added',
                    'description' => "Added student {$student->firstname} {$student->lastname}",
                    'time' => $student->created_at->diffForHumans(),
                    'icon' => 'user',
                ]);
            });

        // Add AI insight availability
        foreach (array_slice($aiInsightsList, 0, 2) as $insight) {
            $recentActivities->push([
                'type' => 'insight_available',
                'description' => "AI insights ready for '{$insight['event_name']}' - Score: {$insight['predicted_satisfaction']}/5.0",
                'time' => $insight['analyzed_at'] ? Carbon::parse($insight['analyzed_at'])->diffForHumans() : 'recent',
                'icon' => 'star',
                'evaluation_id' => $insight['evaluation_id'],
            ]);
        }

        // Sort activities by time
        $recentActivities = $recentActivities
            ->sortByDesc(function ($activity) {
                return $activity['time'];
            })
            ->values()
            ->take(5);

        // ==================== CALCULATE RATES ====================
        $eventCompletionRate = $totalEvents > 0 ? round(($finishedEvents / $totalEvents) * 100, 1) : 0;
        $approvalRate = $totalEvents > 0 ? round(($approvedEvents / $totalEvents) * 100, 1) : 0;

        $stats = [
            'total_events' => $totalEvents,
            'pending_events' => $pendingEvents,
            'approved_events' => $approvedEvents,
            'finished_events' => $finishedEvents,
            'total_students' => $totalStudents,
            'pending_document' => $pendingDocument,
            'pending_approval' => $pendingApproval,
            'rejected_events' => $rejectedEvents,
            'event_completion_rate' => $eventCompletionRate,
            'approval_rate' => $approvalRate,
            'overall_satisfaction' => $overallSatisfaction,
        ];

        return Inertia::render('President/Dashboard', [
            'stats' => $stats,
            'recentEvents' => $recentEvents,
            'studentsByDepartment' => $studentsByDepartment,
            'eventTrends' => $eventTrends,
            'evaluationTrends' => $evaluationTrends,
            'recentActivities' => $recentActivities,
            'aiInsightsList' => $aiInsightsList,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ]);
    }
}