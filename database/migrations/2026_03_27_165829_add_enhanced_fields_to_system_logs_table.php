<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('system_logs', function (Blueprint $table) {
            // Check if columns exist before adding (safe approach)
            if (!Schema::hasColumn('system_logs', 'request_url')) {
                $table->string('request_url')->nullable()->after('user_agent');
            }
            
            if (!Schema::hasColumn('system_logs', 'request_method')) {
                $table->string('request_method', 10)->nullable()->after('request_url');
            }
            
            if (!Schema::hasColumn('system_logs', 'response_status')) {
                $table->integer('response_status')->nullable()->after('request_method');
            }
            
            // Add more detailed indexes for better query performance
            $table->index(['log_type', 'created_at'])->after('created_at');
            $table->index(['action', 'created_at'])->after('log_type_created_at_index');
            
            // If you want to track duration of operations
            if (!Schema::hasColumn('system_logs', 'duration_ms')) {
                $table->integer('duration_ms')->nullable()->after('response_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('system_logs', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex(['log_type', 'created_at']);
            $table->dropIndex(['action', 'created_at']);
            
            // Drop columns
            $table->dropColumn([
                'request_url',
                'request_method',
                'response_status',
                'duration_ms'
            ]);
        });
    }
};