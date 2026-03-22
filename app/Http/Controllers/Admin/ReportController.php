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
                
                return [
                    'id' => $evaluation->id,
                    'title' => $evaluation->title,
                    'form_type' => $evaluation->form_type,
                    'event_name' => $evaluation->event->event_name,
                    'event_date' => $evaluation->event->event_date_start,
                    'organization_name' => $organization ? $organization->name : 'N/A',
                    'responses_count' => $evaluation->total_responses,
                    'has_ai_insights' => AIAnalysis::where('evaluation_id', $evaluation->id)->exists(),
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
     * Generate PDF report for an evaluation
     */
    public function generateReport(Evaluation $evaluation)
    {
        try {
            Log::info('Starting report generation for evaluation: ' . $evaluation->id);
            
            // Load evaluation with relationships
            $evaluation->load(['event', 'categories.questions']);
            
            // Get all responses
            $responses = EvaluationResponse::where('evaluation_id', $evaluation->id)->get();
            
            if ($responses->isEmpty()) {
                return response()->json(['error' => 'No responses found for this evaluation.'], 400);
            }
            
            // Get AI insights
            $aiInsights = AIAnalysis::where('evaluation_id', $evaluation->id)->first();
            
            // Calculate category scores
            $categoryScores = $this->calculateCategoryScoresForReport($evaluation, $responses);
            
            // Get profile statistics
            $totalResp = $responses->count();
            
            // Gender counts with all options
            $gender_counts = [
                'Male' => $responses->where('sex', 'Male')->count(),
                'Female' => $responses->where('sex', 'Female')->count(),
                'Nonbinary/Intersex' => $responses->where('sex', 'Nonbinary/Intersex')->count(),
                'Prefer not to say' => $responses->where('sex', 'Prefer not to say')->count(),
            ];
            
            // Age distribution with detailed brackets
            $age_groups = [
                '18 years old' => $responses->filter(function($response) { return (int)$response->age == 18; })->count(),
                '19 years old' => $responses->filter(function($response) { return (int)$response->age == 19; })->count(),
                '20 years old' => $responses->filter(function($response) { return (int)$response->age == 20; })->count(),
                '21 years old' => $responses->filter(function($response) { return (int)$response->age == 21; })->count(),
                '22 years old' => $responses->filter(function($response) { return (int)$response->age == 22; })->count(),
                '23-25 years old' => $responses->filter(function($response) { 
                    $age = (int)$response->age; 
                    return $age >= 23 && $age <= 25; 
                })->count(),
                '27-29 years old' => $responses->filter(function($response) { 
                    $age = (int)$response->age; 
                    return $age >= 27 && $age <= 29; 
                })->count(),
            ];
            
            // Remove empty age groups
            $age_groups = array_filter($age_groups, function($count) { return $count > 0; });
            
            // Respondent type counts with all options
            $respondent_type_counts = [
                'Student' => $responses->where('respondent_type', 'Student')->count(),
                'Faculty' => $responses->where('respondent_type', 'Faculty')->count(),
                'Admin Personnel' => $responses->where('respondent_type', 'Admin Personnel')->count(),
                'Guest' => $responses->where('respondent_type', 'Guest')->count(),
                'Visiting Committee/Officers' => $responses->where('respondent_type', 'Visiting Committee/Officers')->count(),
            ];
            
            // Remove empty types
            $respondent_type_counts = array_filter($respondent_type_counts, function($count) { return $count > 0; });
            
            // Title counts
            $title_counts = [
                'Dr.' => $responses->where('title_prefix', 'Dr.')->count(),
                'Prof.' => $responses->where('title_prefix', 'Prof.')->count(),
                'Mr.' => $responses->where('title_prefix', 'Mr.')->count(),
                'Ms.' => $responses->where('title_prefix', 'Ms.')->count(),
                'Mx.' => $responses->where('title_prefix', 'Mx.')->count(),
            ];
            
            // Remove empty titles
            $title_counts = array_filter($title_counts, function($count) { return $count > 0; });
            
            // Year level counts
            $year_level_counts = [];
            foreach (['1st Year', '2nd Year', '3rd Year', '4th Year'] as $level) {
                $year_level_counts[$level] = $responses->where('year_level', $level)->count();
            }
            
            // COLLECT ALL COMMENTS - NO FILTERING, NO DEDUPLICATION
            $allPositiveComments = [];
            $allNegativeComments = [];
            $allNeutralComments = [];
            
            foreach ($responses as $response) {
                $comments = $response->comment_responses;
                if (is_string($comments)) {
                    $comments = json_decode($comments, true);
                }
                if (is_array($comments)) {
                    foreach ($comments as $comment) {
                        if (!empty($comment) && is_string($comment) && strlen(trim($comment)) > 0) {
                            $commentTrimmed = trim($comment);
                            // Simple sentiment classification based on keywords
                            $commentLower = strtolower($commentTrimmed);
                            if (strpos($commentLower, 'bad') !== false || 
                                strpos($commentLower, 'poor') !== false ||
                                strpos($commentLower, 'disappoint') !== false ||
                                strpos($commentLower, 'terrible') !== false ||
                                strpos($commentLower, 'cold') !== false ||
                                strpos($commentLower, 'not enough') !== false ||
                                strpos($commentLower, 'hungry') !== false ||
                                strpos($commentLower, 'broken') !== false ||
                                strpos($commentLower, 'damaged') !== false ||
                                strpos($commentLower, 'unsafe') !== false ||
                                strpos($commentLower, 'issue') !== false ||
                                strpos($commentLower, 'problem') !== false) {
                                $allNegativeComments[] = $commentTrimmed;
                            } elseif (strpos($commentLower, 'good') !== false || 
                                strpos($commentLower, 'great') !== false ||
                                strpos($commentLower, 'amazing') !== false ||
                                strpos($commentLower, 'excellent') !== false ||
                                strpos($commentLower, 'best') !== false ||
                                strpos($commentLower, 'awesome') !== false ||
                                strpos($commentLower, 'fantastic') !== false ||
                                strpos($commentLower, 'perfect') !== false ||
                                strpos($commentLower, 'love') !== false) {
                                $allPositiveComments[] = $commentTrimmed;
                            } else {
                                $allNeutralComments[] = $commentTrimmed;
                            }
                        }
                    }
                }
            }
            
            // Get sentiment data from AI insights if available
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
            
            if ($aiInsights) {
                $sentimentAnalysis = json_decode($aiInsights->sentiment_analysis, true);
                $positivePercentage = $sentimentAnalysis['positive_percentage'] ?? 0;
                $negativePercentage = $sentimentAnalysis['negative_percentage'] ?? 0;
                $neutralPercentage = $sentimentAnalysis['neutral_percentage'] ?? 0;
                $totalComments = $sentimentAnalysis['total_comments'] ?? 0;
                $positiveCommentsList = $sentimentAnalysis['positive_comments'] ?? [];
                $negativeCommentsList = $sentimentAnalysis['negative_comments'] ?? [];
                $neutralCommentsList = $sentimentAnalysis['neutral_comments'] ?? [];
                $commonThemes = $sentimentAnalysis['common_themes'] ?? [];
                
                $whatIfAnalysis = json_decode($aiInsights->what_if_analysis, true);
                $whatIfOptimistic = $whatIfAnalysis['optimistic'] ?? [];
                $whatIfTargeted = $whatIfAnalysis['targeted'] ?? [];
            }
            
            // Use AI insights if available, otherwise use extracted comments
            // IMPORTANT: DO NOT slice or limit comments - show ALL comments
            $finalPositiveComments = !empty($positiveCommentsList) ? $positiveCommentsList : $allPositiveComments;
            $finalNegativeComments = !empty($negativeCommentsList) ? $negativeCommentsList : $allNegativeComments;
            $finalNeutralComments = !empty($neutralCommentsList) ? $neutralCommentsList : $allNeutralComments;
            
            // Calculate percentages based on actual comment counts if AI insights not available
            $totalCommentCount = count($finalPositiveComments) + count($finalNegativeComments) + count($finalNeutralComments);
            if ($totalCommentCount > 0 && ($positivePercentage == 0 && $negativePercentage == 0 && $neutralPercentage == 0)) {
                $positivePercentage = round((count($finalPositiveComments) / $totalCommentCount) * 100, 1);
                $negativePercentage = round((count($finalNegativeComments) / $totalCommentCount) * 100, 1);
                $neutralPercentage = round((count($finalNeutralComments) / $totalCommentCount) * 100, 1);
                $totalComments = $totalCommentCount;
            }
            
            // Get strengths and weaknesses
            $strengths = $aiInsights ? json_decode($aiInsights->strengths, true) : [];
            $weaknesses = $aiInsights ? json_decode($aiInsights->weaknesses, true) : [];
            $recommendations = $aiInsights ? json_decode($aiInsights->recommendations, true) : [];
            
            // Calculate overall satisfaction
            $overallSatisfaction = $aiInsights ? $aiInsights->predicted_satisfaction : 3.0;
            $responseRate = $evaluation->total_responses > 0 ? round(($evaluation->total_responses / $responses->count()) * 100, 1) : 0;
            
            // Get event details
            $event = $evaluation->event;
            $eventDate = $event->event_date_start ? Carbon::parse($event->event_date_start)->format('F d, Y') : 'N/A';
            $venue = is_array($evaluation->form_customizations) ? ($evaluation->form_customizations['venue'] ?? 'CSUCC Gymnasium') : 'CSUCC Gymnasium';
            $totalEligible = EventStudent::where('event_id', $event->id)->count();
            
            // Get organization name
            $organization = User::find($event->user_id);
            $organizationName = $organization ? $organization->name : 'Organization';
            
            // AI Summary
            $aiSummary = $aiInsights ? $aiInsights->summary : 'No AI insights available for this evaluation.';
            
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
                'venue' => $venue,
                'total_eligible' => $totalEligible,
                'total_responses' => $evaluation->total_responses,
                'response_rate' => $responseRate,
                'overall_satisfaction' => $overallSatisfaction,
                'satisfaction_interpretation' => $this->getInterpretation($overallSatisfaction),
                'ai_summary' => $aiSummary,
                'category_scores' => $categoryScores,
                'gender_counts' => $gender_counts,
                'age_groups' => $age_groups,
                'title_counts' => $title_counts,
                'year_level_counts' => $year_level_counts,
                'respondent_type_counts' => $respondent_type_counts,
                'totalResp' => $totalResp,
                'positive_percentage' => $positivePercentage,
                'negative_percentage' => $negativePercentage,
                'neutral_percentage' => $neutralPercentage,
                'total_comments' => $totalComments,
                // SHOW ALL COMMENTS - NO LIMITING
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
            Log::info('Comments count - Positive: ' . count($finalPositiveComments) . ', Negative: ' . count($finalNegativeComments) . ', Neutral: ' . count($finalNeutralComments));
            
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
     * Regenerate report (even if already generated)
     */
    public function regenerateReport(Evaluation $evaluation)
    {
        try {
            Log::info('Starting report regeneration for evaluation: ' . $evaluation->id);
            
            // Delete old report file if exists
            if ($evaluation->report_path && Storage::disk('public')->exists($evaluation->report_path)) {
                Storage::disk('public')->delete($evaluation->report_path);
                Log::info('Deleted old report file: ' . $evaluation->report_path);
            }
            
            // Reset the report fields
            $evaluation->update([
                'report_generated_at' => null,
                'report_path' => null,
            ]);
            
            Log::info('Reset evaluation report fields, generating new report...');
            
            // Generate new report
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
     * Calculate category scores for report
     */
    private function calculateCategoryScoresForReport($evaluation, $responses)
    {
        $questions = EvaluationQuestion::where('evaluation_id', $evaluation->id)
            ->with('category')
            ->get();
        
        $categoryScores = [];
        $categoryCounts = [];
        
        foreach ($questions as $question) {
            $categoryName = $question->category ? $question->category->category_name : 'Other';
            $questionText = $question->question_text;
            
            if (!isset($categoryScores[$categoryName])) {
                $categoryScores[$categoryName] = [];
                $categoryCounts[$categoryName] = [];
            }
            
            if (!isset($categoryScores[$categoryName][$questionText])) {
                $categoryScores[$categoryName][$questionText] = 0;
                $categoryCounts[$categoryName][$questionText] = 0;
            }
        }
        
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
                
                if (isset($categoryScores[$categoryName][$questionText])) {
                    $categoryScores[$categoryName][$questionText] += $rating;
                    $categoryCounts[$categoryName][$questionText]++;
                }
            }
        }
        
        $result = [];
        foreach ($categoryScores as $categoryName => $questions) {
            $totalCategoryScore = 0;
            $totalCategoryCount = 0;
            $formattedQuestions = [];
            
            foreach ($questions as $questionText => $totalScore) {
                $count = $categoryCounts[$categoryName][$questionText] ?? 0;
                if ($count > 0) {
                    $average = $totalScore / $count;
                    $formattedQuestions[$questionText] = $average;
                    $totalCategoryScore += $average;
                    $totalCategoryCount++;
                }
            }
            
            $categoryAverage = $totalCategoryCount > 0 ? $totalCategoryScore / $totalCategoryCount : 0;
            
            $result[$categoryName] = [
                'average' => $categoryAverage,
                'questions' => $formattedQuestions
            ];
        }
        
        return $result;
    }

    /**
     * Reset report (for regeneration)
     */
    public function resetReport(Evaluation $evaluation)
    {
        try {
            if ($evaluation->report_path && Storage::disk('public')->exists($evaluation->report_path)) {
                Storage::disk('public')->delete($evaluation->report_path);
            }
            
            $evaluation->update([
                'report_generated_at' => null,
                'report_sent_at' => null,
                'report_path' => null,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Report reset successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to reset report: ' . $e->getMessage()
            ], 500);
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