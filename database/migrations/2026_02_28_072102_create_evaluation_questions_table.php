<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluation_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('evaluation_id');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->text('question_text');
            $table->enum('question_type', ['likert', 'comment'])->default('likert');
            $table->integer('order')->default(0);
            $table->boolean('is_required')->default(true);
            $table->timestamps();

            $table->foreign('evaluation_id')->references('id')->on('evaluations')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('evaluation_categories')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluation_questions');
    }
};