<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluation_responses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('evaluation_id');
            $table->unsignedBigInteger('event_id');
            $table->date('event_date');
            $table->integer('date_index');
            $table->string('student_id');
            $table->string('email');
            $table->string('name')->nullable();
            $table->string('age')->nullable();
            $table->string('sex')->nullable();
            $table->string('agency_office')->nullable();
            $table->string('position')->nullable();
            $table->string('respondent_type')->nullable();
            $table->string('title_prefix')->nullable();
            $table->json('likert_responses');
            $table->json('comment_responses')->nullable();
            $table->string('speaker_topic')->nullable();
            $table->string('speaker_name')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('evaluation_id')->references('id')->on('evaluations')->onDelete('cascade');
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            // REMOVED: foreign key for student_id to allow guests
            
            // Composite unique constraint
            $table->unique(['evaluation_id', 'student_id', 'event_date'], 'unique_evaluation_student_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluation_responses');
    }
};