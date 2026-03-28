<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_analyses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('evaluation_id');
            $table->date('event_date')->nullable(); // For date-specific insights
            $table->text('summary');
            $table->json('strengths');
            $table->json('weaknesses');
            $table->json('recommendations');
            $table->decimal('predicted_satisfaction', 3, 2);
            $table->decimal('success_probability', 3, 2);
            $table->json('critical_factors')->nullable();
            $table->json('category_breakdown');
            $table->json('feature_importance')->nullable();
            $table->json('sentiment_analysis')->nullable();
            $table->json('what_if_analysis')->nullable();
            $table->json('low_scoring_questions')->nullable();
            $table->json('year_level_analysis')->nullable();
            $table->decimal('response_rate', 3, 2)->nullable();
            $table->integer('total_respondents')->nullable();
            $table->timestamp('analyzed_at')->nullable();
            $table->timestamps();
            
            $table->foreign('evaluation_id')
                  ->references('id')
                  ->on('evaluations')
                  ->onDelete('cascade');
                  
            $table->index('analyzed_at');
            $table->index(['evaluation_id', 'event_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_analyses');
    }
};