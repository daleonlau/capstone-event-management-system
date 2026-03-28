<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_analyses', function (Blueprint $table) {
            // Add index for faster queries if not already present
            $table->index(['evaluation_id', 'event_date'], 'idx_ai_analyses_eval_date');
        });
    }

    public function down(): void
    {
        Schema::table('ai_analyses', function (Blueprint $table) {
            $table->dropIndex('idx_ai_analyses_eval_date');
        });
    }
};