// database/migrations/2026_03_21_000003_add_analysis_columns_to_ai_analyses.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_analyses', function (Blueprint $table) {
            if (!Schema::hasColumn('ai_analyses', 'low_scoring_questions')) {
                $table->json('low_scoring_questions')->nullable()->after('category_breakdown');
            }
            if (!Schema::hasColumn('ai_analyses', 'year_level_analysis')) {
                $table->json('year_level_analysis')->nullable()->after('low_scoring_questions');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ai_analyses', function (Blueprint $table) {
            $table->dropColumn(['low_scoring_questions', 'year_level_analysis']);
        });
    }
};