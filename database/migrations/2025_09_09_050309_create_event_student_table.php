<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_student', function (Blueprint $table) {
            $table->unsignedBigInteger('event_id');
            $table->string('student_id');           
            $table->unsignedBigInteger('user_id');
            $table->enum('status', ['Pending', 'Paid', 'Not Paid'])->default('Pending');
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->timestamps();

            $table->primary(['event_id', 'student_id']);

            $table->foreign('event_id')->references('id')->on('events')->cascadeOnDelete();
            $table->foreign('student_id')->references('student_id')->on('students')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_student');
    }
};
