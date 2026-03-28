<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_logs', function (Blueprint $table) {
            $table->id();
            $table->string('log_type'); // 'auth' or 'action'
            $table->string('action'); // e.g., 'login', 'logout', 'create_organization', 'update_user', etc.
            $table->string('description');
            $table->morphs('causer'); // user_id and user_type (for admin or organization_user)
            $table->json('details')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();
            
            // Indexes for faster queries
            $table->index('log_type');
            $table->index('action');
            $table->index('created_at');
            $table->index(['causer_id', 'causer_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_logs');
    }
};