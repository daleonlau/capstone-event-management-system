<?php

namespace App\Jobs;

use App\Models\Evaluation;
use App\Services\AIAnalysisService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AnalyzeEvaluationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Evaluation $evaluation;
    protected ?string $eventDate;
    protected bool $generateAll;
    
    public $timeout = 120;
    public $tries = 3;
    
    public function __construct(Evaluation $evaluation, ?string $eventDate = null, bool $generateAll = false)
    {
        $this->evaluation = $evaluation;
        $this->generateAll = $generateAll;
        
        // Normalize date if provided
        if ($eventDate) {
            try {
                $this->eventDate = Carbon::parse($eventDate)->format('Y-m-d');
            } catch (\Exception $e) {
                $this->eventDate = $eventDate;
            }
        } else {
            $this->eventDate = null;
        }
    }

    public function handle(AIAnalysisService $aiService): void
    {
        if ($this->generateAll) {
            // Generate insights for all dates and overall
            Log::info('Generating all insights for evaluation', [
                'evaluation_id' => $this->evaluation->id
            ]);
            
            $results = $aiService->generateAllInsights($this->evaluation, true);
            
            Log::info('All insights generation completed', [
                'evaluation_id' => $this->evaluation->id,
                'generated_count' => count($results)
            ]);
        } else {
            // Generate insights for specific date or overall
            $dateLabel = $this->eventDate ?? 'overall';
            
            Log::info('Starting AI analysis for evaluation', [
                'evaluation_id' => $this->evaluation->id,
                'event_date' => $dateLabel
            ]);
            
            $result = $aiService->analyzeEvaluation($this->evaluation, $this->eventDate);
            
            if ($result) {
                Log::info('AI analysis completed successfully', [
                    'evaluation_id' => $this->evaluation->id,
                    'event_date' => $dateLabel,
                    'satisfaction' => $result['predicted_satisfaction'] ?? 'N/A'
                ]);
            } else {
                Log::warning('AI analysis returned no results', [
                    'evaluation_id' => $this->evaluation->id,
                    'event_date' => $dateLabel
                ]);
            }
        }
    }
    
    public function failed(\Throwable $exception): void
    {
        $dateLabel = $this->eventDate ?? 'overall';
        
        Log::error('AI analysis job permanently failed', [
            'evaluation_id' => $this->evaluation->id,
            'event_date' => $dateLabel,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}