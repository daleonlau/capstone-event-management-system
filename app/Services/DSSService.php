<?php

namespace App\Services;

use App\Models\Evaluation;
use App\Models\EvaluationResponse;
use App\Models\EvaluationQuestion;

class DSSService
{
    // Scale mapping based on the provided interpretation
    const SCALE = [
        'outstanding' => ['min' => 4.50, 'max' => 5.00, 'label' => 'Outstanding', 'verbal' => 'Very Satisfied'],
        'very_satisfactory' => ['min' => 3.50, 'max' => 4.49, 'label' => 'Very Satisfactory', 'verbal' => 'Satisfied'],
        'satisfactory' => ['min' => 2.50, 'max' => 3.49, 'label' => 'Satisfactory', 'verbal' => 'Neither Satisfied nor Dissatisfied'],
        'poor' => ['min' => 1.50, 'max' => 2.49, 'label' => 'Poor', 'verbal' => 'Dissatisfied'],
        'very_poor' => ['min' => 1.00, 'max' => 1.49, 'label' => 'Very Poor', 'verbal' => 'Very Dissatisfied'],
    ];

    /**
     * Get interpretation for a given score
     */
    public function getInterpretation(float $score): array
    {
        if ($score >= 4.50) {
            return self::SCALE['outstanding'];
        } elseif ($score >= 3.50) {
            return self::SCALE['very_satisfactory'];
        } elseif ($score >= 2.50) {
            return self::SCALE['satisfactory'];
        } elseif ($score >= 1.50) {
            return self::SCALE['poor'];
        } else {
            return self::SCALE['very_poor'];
        }
    }

    /**
     * Get verbal interpretation for a score
     */
    public function getVerbalInterpretation(float $score): string
    {
        $interpretation = $this->getInterpretation($score);
        return $interpretation['verbal'];
    }

    /**
     * Calculate satisfaction directly from responses
     */
    public function calculateSatisfaction(Evaluation $evaluation, ?string $eventDate = null): array
    {
        $query = EvaluationResponse::where('evaluation_id', $evaluation->id);
        if ($eventDate) {
            $query->whereDate('event_date', $eventDate);
        }
        $responses = $query->get();
        
        if ($responses->isEmpty()) {
            return [
                'score' => 0,
                'interpretation' => null,
                'verbal_interpretation' => null,
                'success_probability' => 0,
                'total_respondents' => 0,
                'category_scores' => [],
                'raw_average' => 0,
                'has_data' => false
            ];
        }
        
        // Get questions with their categories
        $questions = EvaluationQuestion::where('evaluation_id', $evaluation->id)
            ->with('category')
            ->where('question_type', 'likert')
            ->get()
            ->keyBy('id');
        
        $totalSum = 0;
        $totalCount = 0;
        $categoryTotals = [];
        $categoryCounts = [];
        
        foreach ($responses as $response) {
            $likert = $response->likert_responses;
            if (is_string($likert)) {
                $likert = json_decode($likert, true);
            }
            
            if (!is_array($likert)) continue;
            
            foreach ($likert as $questionId => $rating) {
                if (!is_numeric($rating)) continue;
                
                $totalSum += $rating;
                $totalCount++;
                
                $question = $questions->get((int)$questionId);
                if ($question && $question->category) {
                    $categoryName = $question->category->category_name;
                    
                    if (!isset($categoryTotals[$categoryName])) {
                        $categoryTotals[$categoryName] = 0;
                        $categoryCounts[$categoryName] = 0;
                    }
                    $categoryTotals[$categoryName] += $rating;
                    $categoryCounts[$categoryName]++;
                }
            }
        }
        
        // Calculate category averages
        $categoryScores = [];
        foreach ($categoryTotals as $category => $total) {
            $count = $categoryCounts[$category];
            $score = $count > 0 ? round($total / $count, 2) : 0;
            $categoryScores[$category] = $score;
        }
        
        // Calculate raw average
        $rawAverage = $totalCount > 0 ? $totalSum / $totalCount : 0;
        
        // Apply business rules
        $finalScore = $this->applyBusinessRules($rawAverage, $categoryScores);
        
        // Get interpretation for final score
        $interpretation = $this->getInterpretation($finalScore);
        
        // Map to success probability
        $successProbability = $this->mapToProbability($finalScore);
        
        return [
            'score' => round($finalScore, 2),
            'interpretation' => $interpretation['label'],
            'verbal_interpretation' => $interpretation['verbal'],
            'success_probability' => $successProbability,
            'total_respondents' => $responses->count(),
            'category_scores' => $categoryScores,
            'raw_average' => round($rawAverage, 2),
            'has_data' => true,
        ];
    }

    /**
     * Apply business rules
     */
    private function applyBusinessRules(float $rawAverage, array $categoryScores): float
    {
        $finalScore = $rawAverage;
        
        foreach ($categoryScores as $score) {
            if ($score < 2.50) {
                $finalScore = min($finalScore, 3.49);
                break;
            }
        }
        
        foreach ($categoryScores as $category => $score) {
            if (stripos($category, 'food') !== false && $score < 2.50) {
                $finalScore -= 0.5;
            } elseif (stripos($category, 'food') !== false && $score < 3.50) {
                $finalScore -= 0.2;
            }
        }
        
        foreach ($categoryScores as $category => $score) {
            if (stripos($category, 'speaker') !== false && $score >= 4.50) {
                $finalScore += 0.2;
            }
        }
        
        $allGood = true;
        foreach ($categoryScores as $score) {
            if ($score < 3.50) {
                $allGood = false;
                break;
            }
        }
        if ($allGood && !empty($categoryScores)) {
            $finalScore += 0.1;
        }
        
        return max(1.00, min(5.00, $finalScore));
    }

    /**
     * Map score to success probability
     */
    private function mapToProbability(float $score): int
    {
        if ($score >= 4.50) return 95;
        if ($score >= 4.00) return 85;
        if ($score >= 3.50) return 75;
        if ($score >= 3.00) return 60;
        if ($score >= 2.50) return 45;
        if ($score >= 2.00) return 30;
        if ($score >= 1.50) return 20;
        return 10;
    }

    /**
     * Get strengths
     */
    public function getStrengths(array $categoryScores): array
    {
        $strengths = [];
        foreach ($categoryScores as $category => $score) {
            if ($score >= 3.50) {
                $strengths[] = "{$category}: {$score}/5.0";
            }
        }
        return $strengths;
    }

    /**
     * Get weaknesses
     */
    public function getWeaknesses(array $categoryScores): array
    {
        $weaknesses = [];
        foreach ($categoryScores as $category => $score) {
            if ($score < 3.50) {
                $severity = $score < 2.50 ? 'CRITICAL' : 'Needs Improvement';
                $weaknesses[] = "[{$severity}] {$category}: {$score}/5.0";
            }
        }
        return $weaknesses;
    }

    /**
     * Generate recommendations
     */
    public function generateRecommendations(array $categoryScores): array
    {
        $recommendations = [];
        
        foreach ($categoryScores as $category => $score) {
            if ($score < 3.50) {
                $priority = $score < 2.50 ? 'high' : ($score < 3.00 ? 'medium' : 'low');
                
                $recommendations[] = [
                    'priority' => $priority,
                    'category' => $category,
                    'current_score' => $score,
                    'target_score' => min(4.50, round($score + 0.8, 1)),
                    'title' => "Improve {$category}",
                    'problem_statement' => "{$category} is currently at {$score}/5.0, below the target of 3.50.",
                    'action_items' => $this->getActionItems($category),
                    'expected_outcome' => "Increase {$category} satisfaction to 4.0+",
                    'resources_needed' => ['Staff time', 'Budget allocation'],
                    'success_metrics' => ['Score improvement to 3.50+', 'Positive feedback increase']
                ];
            }
        }
        
        usort($recommendations, function($a, $b) {
            $order = ['high' => 0, 'medium' => 1, 'low' => 2];
            return $order[$a['priority']] <=> $order[$b['priority']];
        });
        
        return array_slice($recommendations, 0, 5);
    }

    /**
     * Get action items for a category
     */
    private function getActionItems(string $category): array
    {
        if (stripos($category, 'food') !== false) {
            return [
                'Increase food portions by 25%',
                'Improve food quality and variety',
                'Ensure timely food delivery'
            ];
        }
        if (stripos($category, 'information') !== false) {
            return [
                'Send invitations at least 2 weeks before event',
                'Use multiple communication channels',
                'Send reminders 3 days and 1 day before event'
            ];
        }
        if (stripos($category, 'time') !== false) {
            return [
                'Create detailed run-of-show document',
                'Start events ON TIME',
                'Add buffer time between sessions'
            ];
        }
        return [
            'Review current processes',
            'Gather more specific feedback',
            'Implement targeted improvements'
        ];
    }

    // ============================================================
    // DASHBOARD METHODS - FIXED
    // ============================================================

    /**
     * Get complete dashboard data
     */
    public function getDashboardData(Evaluation $evaluation, ?string $eventDate = null): array
    {
        // Get satisfaction data
        $satisfaction = $this->calculateSatisfaction($evaluation, $eventDate);
        
        if (!$satisfaction['has_data']) {
            return [
                'executive_summary' => [],
                'overall_metrics' => [],
                'category_analysis' => [],
                'priority_matrix' => ['critical' => [], 'important' => [], 'urgent' => [], 'monitor' => []],
                'improvement_potential' => [],
                'risk_assessment' => [],
                'progress_tracking' => ['history' => [], 'moving_average' => 0, 'prediction' => []]
            ];
        }
        
        $score = $satisfaction['score'];
        $categoryScores = $satisfaction['category_scores'];
        
        // Build category analysis
        $categoryAnalysis = [];
        foreach ($categoryScores as $category => $catScore) {
            $interpretation = $this->getInterpretation($catScore);
            $categoryAnalysis[$category] = [
                'score' => $catScore,
                'interpretation' => $interpretation['label'],
                'verbal_interpretation' => $interpretation['verbal'],
                'percentage' => round(($catScore / 5) * 100, 1),
                'effort_level' => $this->getEffortLevel($catScore),
                'time_to_improve' => $this->getTimeToImprove($catScore),
                'roi_potential' => $this->getROIPotential($catScore),
                'priority_score' => $this->getPriorityScore($catScore),
                'quick_wins' => $this->getQuickWins($category, $catScore),
            ];
        }
        
        // Build priority matrix
        $priorityMatrix = $this->buildPriorityMatrix($categoryScores);
        
        // Build improvement potential
        $improvementPotential = $this->buildImprovementPotential($categoryScores, $score);
        
        // Build risk assessment
        $riskAssessment = $this->buildRiskAssessment($score, $categoryScores);
        
        // Build overall metrics
        $overallMetrics = [
            'score' => $score,
            'interpretation' => $satisfaction['interpretation'],
            'verbal' => $satisfaction['verbal_interpretation'],
            'success_probability' => $satisfaction['success_probability'],
            'total_respondents' => $satisfaction['total_respondents'],
            'gauge_data' => [
                'value' => $score,
                'percentage' => round(($score / 5) * 100, 1),
                'color' => $this->getStatusColor($score),
                'label' => $satisfaction['interpretation']
            ],
            'radar_data' => $this->buildRadarData($categoryScores)
        ];
        
        // Build executive summary
        $executiveSummary = [
            'overall_score' => $score,
            'interpretation' => $satisfaction['interpretation'],
            'verbal_interpretation' => $satisfaction['verbal_interpretation'],
            'success_probability' => $satisfaction['success_probability'],
            'total_respondents' => $satisfaction['total_respondents'],
            'status' => $this->getOverallStatus($score),
            'status_color' => $this->getStatusColor($score),
            'trend' => 'stable',
            'trend_percentage' => 0,
            'key_metrics' => [
                'highest_category' => $this->getHighestCategory($categoryScores),
                'lowest_category' => $this->getLowestCategory($categoryScores),
                'categories_above_target' => count(array_filter($categoryScores, fn($s) => $s >= 3.50)),
                'categories_below_target' => count(array_filter($categoryScores, fn($s) => $s < 3.50)),
            ]
        ];
        
        return [
            'executive_summary' => $executiveSummary,
            'overall_metrics' => $overallMetrics,
            'category_analysis' => $categoryAnalysis,
            'priority_matrix' => $priorityMatrix,
            'improvement_potential' => $improvementPotential,
            'risk_assessment' => $riskAssessment,
            'progress_tracking' => [
                'history' => [],
                'moving_average' => $score,
                'prediction' => ['score' => $score, 'confidence' => 'low']
            ]
        ];
    }

    /**
     * Build priority matrix
     */
    private function buildPriorityMatrix(array $categoryScores): array
    {
        $matrix = [
            'critical' => [],
            'important' => [],
            'urgent' => [],
            'monitor' => []
        ];
        
        foreach ($categoryScores as $category => $score) {
            $urgency = $score < 2.5 ? 100 : ($score < 3.0 ? 70 : ($score < 3.5 ? 40 : 10));
            $importance = $this->getCategoryImportance($category);
            
            if ($urgency >= 70 && $importance >= 70) {
                $matrix['critical'][] = ['category' => $category, 'score' => $score];
            } elseif ($importance >= 70) {
                $matrix['important'][] = ['category' => $category, 'score' => $score];
            } elseif ($urgency >= 70) {
                $matrix['urgent'][] = ['category' => $category, 'score' => $score];
            } else {
                $matrix['monitor'][] = ['category' => $category, 'score' => $score];
            }
        }
        
        return $matrix;
    }

    /**
     * Build improvement potential
     */
    private function buildImprovementPotential(array $categoryScores, float $currentScore): array
    {
        $potentialScore = $currentScore;
        $improvements = [];
        $count = count($categoryScores);
        
        if ($count > 0) {
            $improvementsNeeded = 0;
            foreach ($categoryScores as $score) {
                if ($score < 3.5) {
                    $improvementsNeeded += (3.5 - $score);
                }
            }
            $avgImprovement = $improvementsNeeded / $count;
            $potentialScore = min(5.0, round($currentScore + $avgImprovement, 2));
            $potentialGain = round($potentialScore - $currentScore, 1);
            
            foreach ($categoryScores as $category => $score) {
                if ($score < 3.5) {
                    $improvements[] = [
                        'category' => $category,
                        'current' => $score,
                        'target' => 3.5,
                        'gap' => round(3.5 - $score, 1),
                    ];
                }
            }
            
            return [
                'current_score' => $currentScore,
                'potential_score' => $potentialScore,
                'potential_gain' => $potentialGain,
                'new_interpretation' => $this->getInterpretation($potentialScore)['label'],
                'improvements_needed' => count($improvements),
                'breakdown' => $improvements,
            ];
        }
        
        return [
            'current_score' => $currentScore,
            'potential_score' => $currentScore,
            'potential_gain' => 0,
            'new_interpretation' => $this->getInterpretation($currentScore)['label'],
            'improvements_needed' => 0,
            'breakdown' => [],
        ];
    }

    /**
     * Build risk assessment
     */
    private function buildRiskAssessment(float $score, array $categoryScores): array
    {
        $risks = [];
        
        if ($score < 3.5) {
            $risks[] = [
                'level' => 'High',
                'area' => 'Overall Satisfaction',
                'impact' => 'Event may not meet participant expectations',
                'mitigation' => 'Immediate improvement plan required'
            ];
        }
        
        foreach ($categoryScores as $category => $catScore) {
            if ($catScore < 2.5) {
                $risks[] = [
                    'level' => 'Critical',
                    'area' => $category,
                    'impact' => 'Participants are dissatisfied, may affect future attendance',
                    'mitigation' => "Urgent intervention needed for {$category}"
                ];
            } elseif ($catScore < 3.0) {
                $risks[] = [
                    'level' => 'Medium',
                    'area' => $category,
                    'impact' => 'Below acceptable levels, needs attention',
                    'mitigation' => "Include {$category} in next event improvement plan"
                ];
            }
        }
        
        return array_slice($risks, 0, 4);
    }

    /**
     * Build radar data
     */
    private function buildRadarData(array $categoryScores): array
    {
        $data = [];
        foreach ($categoryScores as $category => $score) {
            $data[] = [
                'category' => $this->shortenCategoryName($category),
                'score' => $score,
                'percentage' => round(($score / 5) * 100, 1),
                'target' => 3.5,
                'outstanding' => 4.5,
            ];
        }
        return $data;
    }

    /**
     * Shorten category name
     */
    private function shortenCategoryName(string $category): string
    {
        $replacements = [
            'Information Dissemination' => 'Info Dissem',
            'Design of the Event' => 'Event Design',
            'Outcomes of the Event' => 'Outcomes',
            'Resource Speaker' => 'Speaker',
            'Venue and other Facilities' => 'Venue',
            'Traffic Management' => 'Traffic',
        ];
        
        foreach ($replacements as $original => $short) {
            if (str_contains($category, $original)) {
                return $short;
            }
        }
        
        return substr($category, 0, 20);
    }

    /**
     * Get highest category
     */
    private function getHighestCategory(array $categoryScores): array
    {
        if (empty($categoryScores)) {
            return ['name' => 'N/A', 'score' => 0];
        }
        $maxScore = max($categoryScores);
        $name = array_search($maxScore, $categoryScores);
        return ['name' => $name, 'score' => $maxScore];
    }

    /**
     * Get lowest category
     */
    private function getLowestCategory(array $categoryScores): array
    {
        if (empty($categoryScores)) {
            return ['name' => 'N/A', 'score' => 0, 'gap_to_target' => 0];
        }
        $minScore = min($categoryScores);
        $name = array_search($minScore, $categoryScores);
        return ['name' => $name, 'score' => $minScore, 'gap_to_target' => round(3.5 - $minScore, 2)];
    }

    /**
     * Get category importance
     */
    private function getCategoryImportance(string $category): int
    {
        $importanceMap = [
            'Food' => 95,
            'Resource Speaker' => 90,
            'Outcomes' => 85,
            'Secretariat' => 80,
            'Facilities' => 75,
            'Design' => 70,
            'Information' => 65,
        ];
        
        foreach ($importanceMap as $key => $value) {
            if (str_contains($category, $key)) {
                return $value;
            }
        }
        return 50;
    }

    /**
     * Get effort level
     */
    private function getEffortLevel(float $score): string
    {
        if ($score >= 4.0) return 'Low';
        if ($score >= 3.5) return 'Medium-Low';
        if ($score >= 3.0) return 'Medium';
        if ($score >= 2.5) return 'Medium-High';
        return 'High';
    }

    /**
     * Get time to improve
     */
    private function getTimeToImprove(float $score): string
    {
        if ($score >= 4.0) return '1-2 weeks';
        if ($score >= 3.5) return '2-3 weeks';
        if ($score >= 3.0) return '3-4 weeks';
        if ($score >= 2.5) return '1-2 months';
        return '2-3 months';
    }

    /**
     * Get ROI potential
     */
    private function getROIPotential(float $score): int
    {
        if ($score < 2.5) return 90;
        if ($score < 3.0) return 75;
        if ($score < 3.5) return 60;
        if ($score < 4.0) return 40;
        return 25;
    }

    /**
     * Get priority score
     */
    private function getPriorityScore(float $score): int
    {
        $urgencyScore = (5 - $score) * 20;
        $roiScore = $this->getROIPotential($score) * 0.5;
        return min(100, round($urgencyScore + $roiScore));
    }

    /**
     * Get quick wins
     */
    private function getQuickWins(string $category, float $score): array
    {
        if (stripos($category, 'food') !== false) {
            return [
                'Increase portion sizes by 25%',
                'Add one more food station',
                'Provide bottled water instead of dispensers'
            ];
        }
        if (stripos($category, 'information') !== false) {
            return [
                'Send SMS reminders 24 hours before event',
                'Create WhatsApp group for updates',
                'Post QR code registration at campus locations'
            ];
        }
        if (stripos($category, 'time') !== false || stripos($category, 'design') !== false) {
            return [
                'Use countdown timer between sessions',
                'Assign timekeeper to enforce schedule',
                'Pre-load all presentations to avoid delays'
            ];
        }
        return [
            'Conduct short feedback session after event',
            'Send thank you email with improvement commitments',
            'Create action plan with specific timelines'
        ];
    }

    /**
     * Get overall status
     */
    private function getOverallStatus(float $score): string
    {
        if ($score >= 4.5) return 'Excellent';
        if ($score >= 4.0) return 'Good';
        if ($score >= 3.5) return 'Satisfactory';
        if ($score >= 3.0) return 'Below Average';
        if ($score >= 2.5) return 'Needs Improvement';
        return 'Critical';
    }

    /**
     * Get status color
     */
    private function getStatusColor(float $score): string
    {
        if ($score >= 4.5) return '#10B981';
        if ($score >= 4.0) return '#34D399';
        if ($score >= 3.5) return '#FBBF24';
        if ($score >= 3.0) return '#F97316';
        if ($score >= 2.5) return '#EF4444';
        return '#991B1B';
    }
}