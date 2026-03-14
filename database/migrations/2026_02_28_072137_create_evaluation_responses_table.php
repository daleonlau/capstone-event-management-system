<?php
// database/migrations/[timestamp]_create_evaluation_responses_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('evaluation_responses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('evaluation_id');
            $table->unsignedBigInteger('event_id');
            $table->string('student_id');
            $table->string('email');
            $table->string('name')->nullable();
            $table->string('department');
            $table->string('course');
            $table->string('year_level');
            
            // Store all responses as JSON
            $table->json('likert_responses'); // { "question_id": rating, ... }
            $table->json('comment_responses'); // { "question_id": "comment text", ... }
            
            $table->timestamps();

            $table->foreign('evaluation_id')->references('id')->on('evaluations')->onDelete('cascade');
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->foreign('student_id')->references('student_id')->on('students')->onDelete('cascade');
            
            // Ensure one response per student per evaluation
            $table->unique(['evaluation_id', 'student_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('evaluation_responses');
    }
};