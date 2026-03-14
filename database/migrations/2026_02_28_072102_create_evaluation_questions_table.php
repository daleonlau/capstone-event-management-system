<?php
// database/migrations/[timestamp]_create_evaluation_questions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('evaluation_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('evaluation_id');
            $table->unsignedBigInteger('category_id')->nullable(); // null for comment sections
            $table->string('question_text'); // e.g., "a. Timeliness of sending invites"
            $table->enum('question_type', ['likert', 'comment'])->default('likert');
            $table->integer('order')->default(0);
            $table->boolean('is_required')->default(true);
            $table->timestamps();

            $table->foreign('evaluation_id')->references('id')->on('evaluations')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('evaluation_categories')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('evaluation_questions');
    }
};