<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // organization
            $table->string('event_name');
            $table->date('event_date_start');
            $table->date('event_date_end');
            $table->enum('payment', ['No Payment', 'Payment'])->default('No Payment');
            $table->decimal('event_fee', 8, 2)->default(0);
            $table->json('departments')->nullable();
            $table->json('courses')->nullable();
            $table->json('year_levels')->nullable();
            $table->enum('status', ['Pending','Approved','Finished'])->default('Pending');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};