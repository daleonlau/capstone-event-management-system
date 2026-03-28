<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluation_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('organization_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('requested_by')->constrained('organization_users')->onDelete('cascade');
            $table->string('title');
            $table->date('activity_date');
            $table->json('event_dates')->nullable(); // NEW - stores all inclusive dates from event
            $table->string('venue');
            $table->string('speaker_name');
            $table->json('topics');
            $table->boolean('has_food')->default(false);
            $table->enum('status', ['pending', 'processing', 'completed'])->default('pending');
            $table->foreignId('evaluation_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('form_type', ['type1', 'type2', 'type3', 'type4', 'type5'])->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluation_requests');
    }
};