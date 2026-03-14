<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->foreignId('event_type_id')->nullable()->after('user_id')->constrained('event_types');
            $table->string('signed_document_path')->nullable()->after('status');
            $table->enum('approval_status', [
                'pending_document', 
                'pending_approval', 
                'approved', 
                'rejected'
            ])->default('pending_document')->after('status');
            $table->text('rejection_reason')->nullable()->after('approval_status');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['event_type_id']);
            $table->dropColumn([
                'event_type_id', 
                'signed_document_path', 
                'approval_status', 
                'rejection_reason'
            ]);
        });
    }
};