<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use App\Models\EvaluationResponse;
use App\Models\EvaluationQuestion;
use App\Models\EventStudent;
use App\Models\User;
use App\Models\OrganizationUser;
use App\Models\AIAnalysis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display list of evaluations ready for report generation
     */
    public function index()
    {
        $evaluations = Evaluation::with(['event', 'event.creator'])
            ->where('status', 'closed')
            ->where('total_responses', '>', 0)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($evaluation) {
                $organization = User::find($evaluation->event->user_id);
                
                $eventDates = $evaluation->event_dates ?: [];
                $datesWithCounts = [];
                foreach ($eventDates as $date) {
                    $count = EvaluationResponse::where('evaluation_id', $evaluation->id)
                        ->where('event_date', $date)
                        ->count();
                    $datesWithCounts[] = [
                        'date' => $date,
                        'formatted_date' => Carbon::parse($date)->format('F d, Y'),
                        'response_count' => $count
                    ];
                }
                
                $hasOverallInsights = AIAnalysis::where('evaluation_id', $evaluation->id)
                    ->whereNull('event_date')
                    ->exists();
                
                $dateInsights = [];
                foreach ($eventDates as $date) {
                    $hasInsight = AIAnalysis::where('evaluation_id', $evaluation->id)
                        ->whereDate('event_date', $date)
                        ->exists();
                    if ($hasInsight) {
                        $dateInsights[] = $date;
                    }
                }
                
                return [
                    'id' => $evaluation->id,
                    'title' => $evaluation->title,
                    'form_type' => $evaluation->form_type,
                    'event_name' => $evaluation->event->event_name,
                    'event_date_start' => $evaluation->event->event_date_start,
                    'event_date_end' => $evaluation->event->event_date_end,
                    'event_dates' => $datesWithCounts,
                    'organization_name' => $organization ? $organization->name : 'N/A',
                    'responses_count' => $evaluation->total_responses,
                    'has_ai_insights' => $hasOverallInsights,
                    'has_date_insights' => !empty($dateInsights),
                    'date_insights_count' => count($dateInsights),
                    'created_at' => $evaluation->created_at->format('Y-m-d'),
                    'report_generated_at' => $evaluation->report_generated_at,
                    'report_sent_at' => $evaluation->report_sent_at,
                    'report_path' => $evaluation->report_path,
                ];
            });

        return Inertia::render('Admin/Reports/Index', [
            'evaluations' => $evaluations
        ]);
    }

    /**
     * Generate PDF report for an evaluation (with multi-date grouping and AI insights)
     */
    public function generateReport(Evaluation $evaluation)
    {
        try {
            Log::info('Starting report generation for evaluation: ' . $evaluation->id);
            
            // Load evaluation with relationships
            $evaluation->load(['event', 'categories.questions']);
            
            // Get all responses
            $allResponses = EvaluationResponse::where('evaluation_id', $evaluation->id)->get();
            
            if ($allResponses->isEmpty()) {
                return response()->json(['error' => 'No responses found for this evaluation.'], 400);
            }
            
            // Get event dates
            $eventDates = $evaluation->event_dates ?: [];
            
            // If no event dates, use the default from event
            if (empty($eventDates)) {
                $eventDates = [$evaluation->event->event_date_start];
            }
            
            // Get ALL AI insights (overall and per date)
            $overallInsights = AIAnalysis::where('evaluation_id', $evaluation->id)
                ->whereNull('event_date')
                ->first();
            
            $dateInsights = [];
            foreach ($eventDates as $date) {
                $insight = AIAnalysis::where('evaluation_id', $evaluation->id)
                    ->whereDate('event_date', $date)
                    ->first();
                if ($insight) {
                    $dateInsights[$date] = $insight;
                }
            }
            
            // Get overall stats
            $totalResp = $allResponses->count();
            
            // Gender counts
            $gender_counts = [
                'Male' => $allResponses->where('sex', 'Male')->count(),
                'Female' => $allResponses->where('sex', 'Female')->count(),
            ];
            
            // Age distribution
            $age_groups = [];
            foreach ($allResponses as $response) {
                $age = (int)$response->age;
                if ($age == 18) $age_groups['18 years old'] = ($age_groups['18 years old'] ?? 0) + 1;
                elseif ($age == 19) $age_groups['19 years old'] = ($age_groups['19 years old'] ?? 0) + 1;
                elseif ($age == 20) $age_groups['20 years old'] = ($age_groups['20 years old'] ?? 0) + 1;
                elseif ($age == 21) $age_groups['21 years old'] = ($age_groups['21 years old'] ?? 0) + 1;
                elseif ($age == 22) $age_groups['22 years old'] = ($age_groups['22 years old'] ?? 0) + 1;
                elseif ($age >= 23 && $age <= 25) $age_groups['23-25 years old'] = ($age_groups['23-25 years old'] ?? 0) + 1;
                elseif ($age >= 27 && $age <= 29) $age_groups['27-29 years old'] = ($age_groups['27-29 years old'] ?? 0) + 1;
            }
            
            // Respondent type counts
            $respondent_type_counts = [];
            foreach ($allResponses as $response) {
                $type = $response->respondent_type;
                if ($type) {
                    $respondent_type_counts[$type] = ($respondent_type_counts[$type] ?? 0) + 1;
                }
            }
            
            // Title counts
            $title_counts = [];
            foreach ($allResponses as $response) {
                $title = $response->title_prefix;
                if ($title) {
                    $title_counts[$title] = ($title_counts[$title] ?? 0) + 1;
                }
            }
            
            // Collect ALL comments
            $allPositiveComments = [];
            $allNegativeComments = [];
            $allNeutralComments = [];
            
            foreach ($allResponses as $response) {
                $comments = $response->comment_responses;
                if (is_string($comments)) {
                    $comments = json_decode($comments, true);
                }
                if (is_array($comments)) {
                    foreach ($comments as $comment) {
                        if (!empty($comment) && is_string($comment) && strlen(trim($comment)) > 0) {
                            $commentTrimmed = trim($comment);
                            $commentLower = strtolower($commentTrimmed);
                            if (strpos($commentLower, 'bad') !== false || 
                                strpos($commentLower, 'poor') !== false ||
                                strpos($commentLower, 'disappoint') !== false ||
                                strpos($commentLower, 'terrible') !== false) {
                                $allNegativeComments[] = $commentTrimmed;
                            } elseif (strpos($commentLower, 'good') !== false || 
                                strpos($commentLower, 'great') !== false ||
                                strpos($commentLower, 'amazing') !== false ||
                                strpos($commentLower, 'excellent') !== false) {
                                $allPositiveComments[] = $commentTrimmed;
                            } else {
                                $allNeutralComments[] = $commentTrimmed;
                            }
                        }
                    }
                }
            }
            
            // Get sentiment data from AI insights (overall)
            $positivePercentage = 0;
            $negativePercentage = 0;
            $neutralPercentage = 0;
            $totalComments = 0;
            $positiveCommentsList = [];
            $negativeCommentsList = [];
            $neutralCommentsList = [];
            $commonThemes = [];
            $whatIfOptimistic = [];
            $whatIfTargeted = [];
            $strengths = [];
            $weaknesses = [];
            $recommendations = [];
            
            if ($overallInsights) {
                $sentimentAnalysis = json_decode($overallInsights->sentiment_analysis, true);
                $positivePercentage = $sentimentAnalysis['positive_percentage'] ?? 0;
                $negativePercentage = $sentimentAnalysis['negative_percentage'] ?? 0;
                $neutralPercentage = $sentimentAnalysis['neutral_percentage'] ?? 0;
                $totalComments = $sentimentAnalysis['total_comments'] ?? 0;
                $positiveCommentsList = $sentimentAnalysis['positive_comments'] ?? [];
                $negativeCommentsList = $sentimentAnalysis['negative_comments'] ?? [];
                $neutralCommentsList = $sentimentAnalysis['neutral_comments'] ?? [];
                $commonThemes = $sentimentAnalysis['common_themes'] ?? [];
                
                $whatIfAnalysis = json_decode($overallInsights->what_if_analysis, true);
                $whatIfOptimistic = $whatIfAnalysis['optimistic'] ?? [];
                $whatIfTargeted = $whatIfAnalysis['targeted'] ?? [];
                
                $strengths = json_decode($overallInsights->strengths, true) ?: [];
                $weaknesses = json_decode($overallInsights->weaknesses, true) ?: [];
                $recommendations = json_decode($overallInsights->recommendations, true) ?: [];
            }
            
            $finalPositiveComments = !empty($positiveCommentsList) ? $positiveCommentsList : $allPositiveComments;
            $finalNegativeComments = !empty($negativeCommentsList) ? $negativeCommentsList : $allNegativeComments;
            $finalNeutralComments = !empty($neutralCommentsList) ? $neutralCommentsList : $allNeutralComments;
            
            $totalCommentCount = count($finalPositiveComments) + count($finalNegativeComments) + count($finalNeutralComments);
            if ($totalCommentCount > 0 && ($positivePercentage == 0 && $negativePercentage == 0 && $neutralPercentage == 0)) {
                $positivePercentage = round((count($finalPositiveComments) / $totalCommentCount) * 100, 1);
                $negativePercentage = round((count($finalNegativeComments) / $totalCommentCount) * 100, 1);
                $neutralPercentage = round((count($finalNeutralComments) / $totalCommentCount) * 100, 1);
                $totalComments = $totalCommentCount;
            }
            
            // Calculate overall satisfaction
            $overallSatisfaction = $overallInsights ? $overallInsights->predicted_satisfaction : 3.0;
            $responseRate = $totalResp > 0 ? round(($evaluation->total_responses / $totalResp) * 100, 1) : 0;
            
            // Get event details
            $event = $evaluation->event;
            $eventDate = $event->event_date_start ? Carbon::parse($event->event_date_start)->format('F d, Y') : 'N/A';
            $venue = is_array($evaluation->form_customizations) ? ($evaluation->form_customizations['venue'] ?? 'CSUCC Gymnasium') : 'CSUCC Gymnasium';
            $totalEligible = EventStudent::where('event_id', $event->id)->count();
            
            // Get organization name
            $organization = User::find($event->user_id);
            $organizationName = $organization ? $organization->name : 'Organization';
            
            // Prepare header image data
            $imageData = '';
            $possiblePaths = [
                public_path('images/pdfheader.png'),
                base_path('public/images/pdfheader.png'),
                storage_path('app/public/images/pdfheader.png'),
            ];
            foreach ($possiblePaths as $path) {
                if (file_exists($path)) {
                    $imageData = base64_encode(file_get_contents($path));
                    break;
                }
            }
            
            // Prepare data for PDF
            $data = [
                'evaluation' => $evaluation,
                'event' => $event,
                'event_date' => $eventDate,
                'event_dates' => $eventDates,
                'venue' => $venue,
                'total_eligible' => $totalEligible,
                'total_responses' => $evaluation->total_responses,
                'response_rate' => $responseRate,
                'overall_satisfaction' => $overallSatisfaction,
                'satisfaction_interpretation' => $this->getInterpretation($overallSatisfaction),
                'gender_counts' => $gender_counts,
                'age_groups' => $age_groups,
                'title_counts' => $title_counts,
                'respondent_type_counts' => $respondent_type_counts,
                'totalResp' => $totalResp,
                'positive_percentage' => $positivePercentage,
                'negative_percentage' => $negativePercentage,
                'neutral_percentage' => $neutralPercentage,
                'total_comments' => $totalComments,
                'positive_comments' => $finalPositiveComments,
                'negative_comments' => $finalNegativeComments,
                'neutral_comments' => $finalNeutralComments,
                'common_themes' => $commonThemes,
                'strengths' => $strengths,
                'weaknesses' => $weaknesses,
                'recommendations' => $recommendations,
                'what_if_optimistic' => $whatIfOptimistic,
                'what_if_targeted' => $whatIfTargeted,
                'organization_name' => $organizationName,
                'report_date' => now()->format('F d, Y'),
                'generated_by' => auth()->user()->name,
                'header_image' => $imageData,
                'per_date_data' => $this->getPerDateData($evaluation, $eventDates, $dateInsights, $totalEligible),
            ];
            
            // Generate PDF
            $pdf = Pdf::loadView('pdfs.evaluation-report', $data);
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOptions([
                'defaultFont' => 'sans-serif',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'chroot' => public_path(),
            ]);
            
            // Store PDF path
            $pdfPath = 'reports/evaluation_' . $evaluation->id . '_' . now()->format('Y-m-d_His') . '.pdf';
            $pdfContent = $pdf->output();
            Storage::disk('public')->put($pdfPath, $pdfContent);
            
            // Update evaluation with report info
            $evaluation->update([
                'report_generated_at' => now(),
                'report_path' => $pdfPath,
            ]);
            
            Log::info('Report generated successfully for evaluation: ' . $evaluation->id);
            
            return response()->json([
                'success' => true,
                'message' => 'Report generated successfully',
                'report_path' => asset('storage/' . $pdfPath),
                'report_id' => $evaluation->id
            ]);
        } catch (\Exception $e) {
            Log::error('Report generation failed: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'error' => 'Failed to generate report: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get per-date data by querying the responses table directly
     */
    private function getPerDateData($evaluation, $eventDates, $dateInsights, $totalEligible)
    {
        $perDateData = [];
        
        try {
            foreach ($eventDates as $index => $date) {
                $dateIndex = $index + 1;
                $insight = $dateInsights[$date] ?? null;
                
                // Get responses for this specific date
                $dateResponses = EvaluationResponse::where('evaluation_id', $evaluation->id)
                    ->where('event_date', $date)
                    ->get();
                
                $responseCount = $dateResponses->count();
                
                // Calculate category scores for this date
                $dateCategoryScores = $this->calculateCategoryScores($evaluation, $dateResponses);
                
                // Get demographic data for this date
                $gender_counts = [
                    'Male' => $dateResponses->where('sex', 'Male')->count(),
                    'Female' => $dateResponses->where('sex', 'Female')->count(),
                ];
                
                // Age distribution for this date
                $age_groups = [];
                foreach ($dateResponses as $response) {
                    $age = (int)$response->age;
                    if ($age == 18) $age_groups['18 years old'] = ($age_groups['18 years old'] ?? 0) + 1;
                    elseif ($age == 19) $age_groups['19 years old'] = ($age_groups['19 years old'] ?? 0) + 1;
                    elseif ($age == 20) $age_groups['20 years old'] = ($age_groups['20 years old'] ?? 0) + 1;
                    elseif ($age == 21) $age_groups['21 years old'] = ($age_groups['21 years old'] ?? 0) + 1;
                    elseif ($age == 22) $age_groups['22 years old'] = ($age_groups['22 years old'] ?? 0) + 1;
                    elseif ($age >= 23 && $age <= 25) $age_groups['23-25 years old'] = ($age_groups['23-25 years old'] ?? 0) + 1;
                }
                
                // Respondent types for this date
                $respondent_types = [];
                foreach ($dateResponses as $response) {
                    $type = $response->respondent_type;
                    if ($type) {
                        $respondent_types[$type] = ($respondent_types[$type] ?? 0) + 1;
                    }
                }
                
                // Title counts for this date
                $title_counts = [];
                foreach ($dateResponses as $response) {
                    $title = $response->title_prefix;
                    if ($title) {
                        $title_counts[$title] = ($title_counts[$title] ?? 0) + 1;
                    }
                }
                
                // Get comments for this date
                $positiveComments = [];
                $negativeComments = [];
                $neutralComments = [];
                
                foreach ($dateResponses as $response) {
                    $comments = $response->comment_responses;
                    if (is_string($comments)) {
                        $comments = json_decode($comments, true);
                    }
                    if (is_array($comments)) {
                        foreach ($comments as $comment) {
                            if (!empty($comment) && is_string($comment) && strlen(trim($comment)) > 0) {
                                $commentTrimmed = trim($comment);
                                $commentLower = strtolower($commentTrimmed);
                                
                                if (strpos($commentLower, 'bad') !== false || 
                                    strpos($commentLower, 'poor') !== false ||
                                    strpos($commentLower, 'disappoint') !== false ||
                                    strpos($commentLower, 'terrible') !== false) {
                                    $negativeComments[] = $commentTrimmed;
                                } elseif (strpos($commentLower, 'good') !== false || 
                                    strpos($commentLower, 'great') !== false ||
                                    strpos($commentLower, 'amazing') !== false ||
                                    strpos($commentLower, 'excellent') !== false) {
                                    $positiveComments[] = $commentTrimmed;
                                } else {
                                    $neutralComments[] = $commentTrimmed;
                                }
                            }
                        }
                    }
                }
                
                // Get AI insights for this date if available
                $dateStrengths = [];
                $dateWeaknesses = [];
                $dateRecommendations = [];
                $datePositiveComments = [];
                $dateNegativeComments = [];
                $dateNeutralComments = [];
                $positivePercentage = 0;
                $negativePercentage = 0;
                $neutralPercentage = 0;
                
                if ($insight) {
                    $dateStrengths = json_decode($insight->strengths, true) ?: [];
                    $dateWeaknesses = json_decode($insight->weaknesses, true) ?: [];
                    $dateRecommendations = json_decode($insight->recommendations, true) ?: [];
                    $dateSentimentData = json_decode($insight->sentiment_analysis, true) ?: [];
                    $datePositiveComments = $dateSentimentData['positive_comments'] ?? [];
                    $dateNegativeComments = $dateSentimentData['negative_comments'] ?? [];
                    $dateNeutralComments = $dateSentimentData['neutral_comments'] ?? [];
                    $positivePercentage = $dateSentimentData['positive_percentage'] ?? 0;
                    $negativePercentage = $dateSentimentData['negative_percentage'] ?? 0;
                    $neutralPercentage = $dateSentimentData['neutral_percentage'] ?? 0;
                }
                
                // Use AI comments if available, otherwise use basic classified comments
                $finalPositiveComments = !empty($datePositiveComments) ? $datePositiveComments : $positiveComments;
                $finalNegativeComments = !empty($dateNegativeComments) ? $dateNegativeComments : $negativeComments;
                $finalNeutralComments = !empty($dateNeutralComments) ? $dateNeutralComments : $neutralComments;
                
                // Calculate sentiment percentages if not provided by AI
                $totalDateComments = count($finalPositiveComments) + count($finalNegativeComments) + count($finalNeutralComments);
                if ($totalDateComments > 0 && $positivePercentage == 0 && $negativePercentage == 0 && $neutralPercentage == 0) {
                    $positivePercentage = round((count($finalPositiveComments) / $totalDateComments) * 100, 1);
                    $negativePercentage = round((count($finalNegativeComments) / $totalDateComments) * 100, 1);
                    $neutralPercentage = round((count($finalNeutralComments) / $totalDateComments) * 100, 1);
                }
                
                $perDateData[] = [
                    'date' => $date,
                    'date_index' => $dateIndex,
                    'formatted_date' => Carbon::parse($date)->format('F d, Y'),
                    'response_count' => $responseCount,
                    'response_rate' => $totalEligible > 0 ? round(($responseCount / $totalEligible) * 100, 1) : 0,
                    'category_scores' => $dateCategoryScores,
                    'gender_counts' => $gender_counts,
                    'age_groups' => $age_groups,
                    'respondent_types' => $respondent_types,
                    'title_counts' => $title_counts,
                    'has_ai_insights' => $insight ? true : false,
                    'strengths' => $dateStrengths,
                    'weaknesses' => $dateWeaknesses,
                    'recommendations' => $dateRecommendations,
                    'sentiment' => [
                        'positive_comments' => $finalPositiveComments,
                        'negative_comments' => $finalNegativeComments,
                        'neutral_comments' => $finalNeutralComments,
                        'positive_percentage' => $positivePercentage,
                        'negative_percentage' => $negativePercentage,
                        'neutral_percentage' => $neutralPercentage,
                    ],
                ];
            }
        } catch (\Exception $e) {
            Log::error('Error in getPerDateData: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
        }
        
        return $perDateData;
    }

    /**
     * Calculate category scores from responses
     */
    private function calculateCategoryScores($evaluation, $responses)
    {
        try {
            // Get all questions with their categories
            $questions = EvaluationQuestion::where('evaluation_id', $evaluation->id)
                ->with('category')
                ->where('question_type', 'likert')
                ->orderBy('order')
                ->get();
            
            if ($questions->isEmpty()) {
                return [];
            }
            
            // Initialize arrays for storing scores per category
            $categoryTotals = [];
            $categoryCounts = [];
            $questionTotals = [];
            $questionCounts = [];
            
            // Initialize structure
            foreach ($questions as $question) {
                $categoryName = $question->category ? $question->category->category_name : 'Other';
                $questionText = $question->question_text;
                
                if (!isset($categoryTotals[$categoryName])) {
                    $categoryTotals[$categoryName] = 0;
                    $categoryCounts[$categoryName] = 0;
                    $questionTotals[$categoryName] = [];
                    $questionCounts[$categoryName] = [];
                }
                
                $questionTotals[$categoryName][$questionText] = 0;
                $questionCounts[$categoryName][$questionText] = 0;
            }
            
            // Process each response
            foreach ($responses as $response) {
                $likert = $response->likert_responses;
                if (is_string($likert)) {
                    $likert = json_decode($likert, true);
                }
                
                if (!is_array($likert)) {
                    continue;
                }
                
                foreach ($likert as $questionId => $rating) {
                    $questionId = (int)$questionId;
                    $rating = (float)$rating;
                    
                    $question = $questions->firstWhere('id', $questionId);
                    if (!$question) {
                        continue;
                    }
                    
                    $categoryName = $question->category ? $question->category->category_name : 'Other';
                    $questionText = $question->question_text;
                    
                    // Add to category totals
                    if (isset($categoryTotals[$categoryName])) {
                        $categoryTotals[$categoryName] += $rating;
                        $categoryCounts[$categoryName]++;
                    }
                    
                    // Add to question totals
                    if (isset($questionTotals[$categoryName][$questionText])) {
                        $questionTotals[$categoryName][$questionText] += $rating;
                        $questionCounts[$categoryName][$questionText]++;
                    }
                }
            }
            
            // Calculate averages and format output
            $result = [];
            foreach ($categoryTotals as $categoryName => $totalScore) {
                $categoryAverage = $categoryCounts[$categoryName] > 0 
                    ? $totalScore / $categoryCounts[$categoryName] 
                    : 0;
                
                $questionsArray = [];
                foreach ($questionTotals[$categoryName] as $questionText => $total) {
                    $count = $questionCounts[$categoryName][$questionText] ?? 0;
                    if ($count > 0) {
                        $questionsArray[$questionText] = $total / $count;
                    }
                }
                
                $result[$categoryName] = [
                    'average' => $categoryAverage,
                    'questions' => $questionsArray
                ];
            }
            
            return $result;
        } catch (\Exception $e) {
            Log::error('Error in calculateCategoryScores: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Regenerate report (even if already generated)
     */
    public function regenerateReport(Evaluation $evaluation)
    {
        try {
            Log::info('Starting report regeneration for evaluation: ' . $evaluation->id);
            
            if ($evaluation->report_path && Storage::disk('public')->exists($evaluation->report_path)) {
                Storage::disk('public')->delete($evaluation->report_path);
                Log::info('Deleted old report file: ' . $evaluation->report_path);
            }
            
            $evaluation->update([
                'report_generated_at' => null,
                'report_path' => null,
            ]);
            
            Log::info('Reset evaluation report fields, generating new report...');
            
            return $this->generateReport($evaluation);
        } catch (\Exception $e) {
            Log::error('Report regeneration failed: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'error' => 'Failed to regenerate report: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * View the PDF report
     */
    public function viewReport(Evaluation $evaluation)
    {
        if (!$evaluation->report_path || !Storage::disk('public')->exists($evaluation->report_path)) {
            return redirect()->back()->with('error', 'Report not found. Please generate the report first.');
        }
        
        return response()->file(storage_path('app/public/' . $evaluation->report_path));
    }

    /**
     * Download the PDF report
     */
    public function downloadReport(Evaluation $evaluation)
    {
        if (!$evaluation->report_path || !Storage::disk('public')->exists($evaluation->report_path)) {
            return redirect()->back()->with('error', 'Report not found. Please generate the report first.');
        }
        
        $filename = 'evaluation_report_' . preg_replace('/[^a-zA-Z0-9]/', '_', $evaluation->event->event_name) . '_' . now()->format('Y-m-d') . '.pdf';
        
        return response()->download(storage_path('app/public/' . $evaluation->report_path), $filename);
    }

    /**
     * Send report to organization via email
     */
    public function sendReport(Request $request, Evaluation $evaluation)
    {
        try {
            if (!$evaluation->report_path || !Storage::disk('public')->exists($evaluation->report_path)) {
                return response()->json(['error' => 'Report not found. Please generate the report first.'], 400);
            }
            
            $organization = User::find($evaluation->event->user_id);
            
            if (!$organization) {
                return response()->json(['error' => 'Organization not found.'], 400);
            }
            
            $president = OrganizationUser::where('organization_id', $organization->id)
                ->where('role', 'president')
                ->first();
            
            $email = $president ? $president->email : $organization->email;
            
            if (!$email) {
                return response()->json(['error' => 'No email address found for the organization.'], 400);
            }
            
            Mail::to($email)->send(new \App\Mail\EvaluationReportMail($evaluation, $organization));
            
            $evaluation->update(['report_sent_at' => now()]);
            
            return response()->json([
                'success' => true,
                'message' => 'Report sent successfully to ' . $email,
                'sent_to' => $email
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send report email: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to send email: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get interpretation based on mean score
     */
    private function getInterpretation($mean)
    {
        if ($mean >= 4.50) {
            return 'Outstanding';
        } elseif ($mean >= 3.50) {
            return 'Very Satisfactory';
        } elseif ($mean >= 2.50) {
            return 'Satisfactory';
        } elseif ($mean >= 1.50) {
            return 'Poor';
        } else {
            return 'Very Poor';
        }
    }
}