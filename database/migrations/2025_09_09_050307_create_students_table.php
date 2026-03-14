<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->string('student_id');
            $table->unsignedBigInteger('user_id'); // organization ID
            $table->string('firstname');
            $table->string('lastname');
            $table->string('email'); // Remove 'after' - it's not needed in create table
            $table->enum('yearlevel', ['1st Year','2nd Year','3rd Year','4th Year']);
            $table->string('course');
            $table->string('department')->nullable();
            $table->timestamps();

            $table->primary(['student_id', 'user_id']);
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};