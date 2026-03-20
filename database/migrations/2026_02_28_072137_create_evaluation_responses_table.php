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
            $table->string('student_id');
            $table->string('email');
            $table->string('name')->nullable();
            
            // Profile fields from Part I
            $table->string('age')->nullable();
            $table->string('sex')->nullable();
            $table->string('agency_office')->nullable();
            $table->string('position')->nullable();
            $table->string('respondent_type')->nullable();
            $table->string('title_prefix')->nullable();
            
            // Student info
            $table->string('department');
            $table->string('course');
            $table->string('year_level');
            
            // Response data
            $table->json('likert_responses');
            $table->json('comment_responses');
            
            // Speaker fields (for forms with speaker)
            $table->string('speaker_topic')->nullable();
            $table->string('speaker_name')->nullable();
            
            $table->timestamps();

            $table->foreign('evaluation_id')->references('id')->on('evaluations')->onDelete('cascade');
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->foreign('student_id')->references('student_id')->on('students')->onDelete('cascade');
            
            $table->unique(['evaluation_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluation_responses');
    }
};