// database/migrations/2026_03_21_000000_update_sentiment_analysis_fields.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_analyses', function (Blueprint $table) {
            // These columns should already exist, but ensure they are there
            if (!Schema::hasColumn('ai_analyses', 'feature_importance')) {
                $table->json('feature_importance')->nullable();
            }
            if (!Schema::hasColumn('ai_analyses', 'sentiment_analysis')) {
                $table->json('sentiment_analysis')->nullable();
            }
            if (!Schema::hasColumn('ai_analyses', 'what_if_analysis')) {
                $table->json('what_if_analysis')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('ai_analyses', function (Blueprint $table) {
            $table->dropColumn(['feature_importance', 'sentiment_analysis', 'what_if_analysis']);
        });
    }
};