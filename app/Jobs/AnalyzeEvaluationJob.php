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

class AnalyzeEvaluationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Evaluation $evaluation;
    
    public $timeout = 120;
    public $tries = 3;
    
    public function __construct(Evaluation $evaluation)
    {
        $this->evaluation = $evaluation;
    }

    public function handle(AIAnalysisService $aiService): void
    {
        Log::info('Starting AI analysis for evaluation', [
            'evaluation_id' => $this->evaluation->id
        ]);
        
        try {
            $result = $aiService->analyzeEvaluation($this->evaluation);
            
            if ($result) {
                Log::info('AI analysis completed successfully', [
                    'evaluation_id' => $this->evaluation->id
                ]);
            } else {
                Log::warning('AI analysis returned no results', [
                    'evaluation_id' => $this->evaluation->id
                ]);
            }
        } catch (\Exception $e) {
            Log::error('AI analysis job failed', [
                'evaluation_id' => $this->evaluation->id,
                'error' => $e->getMessage()
            ]);
            
            $this->fail($e);
        }
    }
    
    public function failed(\Throwable $exception): void
    {
        Log::error('AI analysis job permanently failed', [
            'evaluation_id' => $this->evaluation->id,
            'error' => $exception->getMessage()
        ]);
    }
}