<?php
// database/migrations/[timestamp]_create_evaluation_categories_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('evaluation_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('evaluation_id');
            $table->string('category_name'); // e.g., "I. Information Dissemination"
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->foreign('evaluation_id')->references('id')->on('evaluations')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('evaluation_categories');
    }
};