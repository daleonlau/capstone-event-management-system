<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id');
            $table->unsignedBigInteger('organization_id');
            $table->string('title')->default('EVENT EVALUATION FORM');
            $table->enum('form_type', ['type1', 'type2', 'type3', 'type4', 'type5'])->nullable();
            $table->json('form_customizations')->nullable();
            $table->string('form_number')->nullable();
            $table->string('revision')->nullable();
            $table->string('date_effectivity')->nullable();
            $table->enum('status', ['draft', 'active', 'closed'])->default('draft');
            $table->dateTime('available_from')->nullable();
            $table->dateTime('available_until')->nullable();
            $table->string('qr_code_path')->nullable();
            $table->string('qr_code_url')->nullable();
            $table->integer('total_responses')->default(0);
            $table->timestamps();

            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->foreign('organization_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};