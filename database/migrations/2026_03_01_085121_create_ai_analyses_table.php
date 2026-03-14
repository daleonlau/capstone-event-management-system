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
            $table->unsignedBigInteger('evaluation_id')->unique();
            $table->text('summary');
            $table->json('strengths');
            $table->json('weaknesses');
            $table->json('recommendations');
            $table->decimal('predicted_satisfaction', 3, 2);
            $table->decimal('success_probability', 3, 2);
            $table->json('critical_factors')->nullable();
            $table->json('category_breakdown');
            $table->decimal('response_rate', 3, 2)->nullable();
            $table->integer('total_respondents')->nullable();
            $table->timestamp('analyzed_at')->nullable();
            $table->timestamps();
            
            $table->foreign('evaluation_id')
                  ->references('id')
                  ->on('evaluations')
                  ->onDelete('cascade');
                  
            $table->index('analyzed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_analyses');
    }
};