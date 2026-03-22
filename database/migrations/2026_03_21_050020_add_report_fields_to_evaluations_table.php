// database/migrations/2026_03_21_000004_add_report_fields_to_evaluations_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('evaluations', function (Blueprint $table) {
            if (!Schema::hasColumn('evaluations', 'report_generated_at')) {
                $table->timestamp('report_generated_at')->nullable()->after('qr_code_url');
            }
            if (!Schema::hasColumn('evaluations', 'report_sent_at')) {
                $table->timestamp('report_sent_at')->nullable()->after('report_generated_at');
            }
            if (!Schema::hasColumn('evaluations', 'report_path')) {
                $table->string('report_path')->nullable()->after('report_sent_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('evaluations', function (Blueprint $table) {
            $table->dropColumn(['report_generated_at', 'report_sent_at', 'report_path']);
        });
    }
};