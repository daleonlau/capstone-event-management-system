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
            Log::info('Generating all analyses for evaluation', [
                'evaluation_id' => $this->evaluation->id
            ]);
            
            $results = $aiService->generateAllInsights($this->evaluation, true);
            
            Log::info('All analyses generation completed', [
                'evaluation_id' => $this->evaluation->id,
                'results_count' => count($results)
            ]);
        } else {
            Log::info('Starting single analysis generation', [
                'evaluation_id' => $this->evaluation->id,
                'event_date' => $this->eventDate ?? 'overall'
            ]);
            
            $result = $aiService->analyzeEvaluation($this->evaluation, $this->eventDate, true);
            
            Log::info('Single analysis generation completed', [
                'evaluation_id' => $this->evaluation->id,
                'event_date' => $this->eventDate ?? 'overall',
                'has_result' => !is_null($result)
            ]);
        }
    }
    
    public function failed(\Throwable $exception): void
    {
        $dateLabel = $this->eventDate ?? 'overall';
        
        Log::error('Analysis job failed', [
            'evaluation_id' => $this->evaluation->id,
            'event_date' => $dateLabel,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}