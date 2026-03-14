<?php

namespace App\Http\Controllers\Adviser;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Evaluation;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class EvaluationController extends Controller
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
     * Display a listing of finished events with evaluations.
     */
    public function index()
    {
        $events = Event::where('user_id', $this->organizationId)
            ->where('status', 'Finished')
            ->withCount('evaluations')
            ->with('eventType')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($event) {
                // Calculate average rating
                $avgRating = Evaluation::where('event_id', $event->id)
                    ->get()
                    ->pluck('answers')
                    ->flatten()
                    ->avg();
                
                $event->average_rating = $avgRating ? round($avgRating, 2) : null;
                return $event;
            });

        return Inertia::render('Adviser/Evaluations/Index', [
            'events' => $events
        ]);
    }

    /**
     * Display evaluation results for a specific event.
     */
    public function results(Event $event)
    {
        // Check if event belongs to this organization
        if ($event->user_id !== $this->organizationId) {
            abort(403);
        }

        $evaluations = $event->evaluations()->get();
        
        if ($evaluations->isEmpty()) {
            return Inertia::render('Adviser/Evaluations/Results', [
                'event' => $event,
                'evaluationData' => [],
                'summary' => [
                    'total_responses' => 0,
                    'overall_average' => 0,
                    'highest_question' => null,
                    'lowest_question' => null,
                ]
            ]);
        }

        // Standard evaluation questions
        $questions = [
            "The objectives of the event were clearly defined.",
            "The event was well organized and executed.",
            "The speakers or facilitators were engaging and knowledgeable.",
            "The event activities were relevant and enjoyable.",
            "Overall, the event met my expectations.",
        ];

        // Process answers by question
        $answersByQuestion = [];
        foreach ($evaluations as $eval) {
            $answers = is_string($eval->answers) ? json_decode($eval->answers, true) : $eval->answers;
            foreach ($answers as $qIndex => $answer) {
                if (!isset($answersByQuestion[$qIndex])) {
                    $answersByQuestion[$qIndex] = [];
                }
                $answersByQuestion[$qIndex][] = $answer;
            }
        }

        $evaluationData = [];
        $totalAverage = 0;

        foreach ($answersByQuestion as $qIndex => $answers) {
            $average = array_sum($answers) / count($answers);
            $totalAverage += $average;
            
            // Calculate distribution of ratings (1-5 stars)
            $distribution = array_count_values($answers);
            
            $evaluationData[] = [
                'question_number' => $qIndex + 1,
                'question' => $questions[$qIndex] ?? "Question " . ($qIndex + 1),
                'average' => round($average, 2),
                'distribution' => [
                    1 => $distribution[1] ?? 0,
                    2 => $distribution[2] ?? 0,
                    3 => $distribution[3] ?? 0,
                    4 => $distribution[4] ?? 0,
                    5 => $distribution[5] ?? 0,
                ]
            ];
        }

        // Find highest and lowest rated questions
        $sorted = collect($evaluationData)->sortByDesc('average')->values();
        
        $summary = [
            'total_responses' => $evaluations->count(),
            'overall_average' => $totalAverage > 0 ? round($totalAverage / count($evaluationData), 2) : 0,
            'highest_question' => $sorted->first()['question'] ?? null,
            'lowest_question' => $sorted->last()['question'] ?? null,
        ];

        return Inertia::render('Adviser/Evaluations/Results', [
            'event' => $event,
            'evaluationData' => $evaluationData,
            'summary' => $summary
        ]);
    }
}