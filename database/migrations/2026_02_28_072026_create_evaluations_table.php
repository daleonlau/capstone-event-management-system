<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id');
            $table->unsignedBigInteger('organization_id');
            $table->string('title')->default('EVENT EVALUATION FORM');
            $table->string('form_number')->default('F-EEF-018d');
            $table->string('revision')->default('Rev. 0');
            $table->string('date_effectivity')->default('04-28-2025');
            $table->enum('status', ['draft', 'active', 'closed'])->default('draft');
            $table->dateTime('available_from')->nullable();
            $table->dateTime('available_until')->nullable();
            $table->string('qr_code_path')->nullable();
            $table->string('qr_code_url')->nullable();
            $table->integer('total_responses')->default(0);
            $table->timestamps();

            // Fix the foreign key - reference the correct table
            // If organizations are stored in 'users' table:
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->foreign('organization_id')->references('id')->on('users')->onDelete('cascade');
            
            // OR if you have a separate organizations table, create it first
            // $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('evaluations');
    }
};