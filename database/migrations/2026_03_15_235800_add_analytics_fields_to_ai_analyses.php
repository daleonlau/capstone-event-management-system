<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_analyses', function (Blueprint $table) {
            $table->json('feature_importance')->nullable()->after('recommendations');
            $table->json('sentiment_analysis')->nullable()->after('feature_importance');
            $table->json('what_if_analysis')->nullable()->after('sentiment_analysis');
        });
    }

    public function down(): void
    {
        Schema::table('ai_analyses', function (Blueprint $table) {
            $table->dropColumn(['feature_importance', 'sentiment_analysis', 'what_if_analysis']);
        });
    }
};