<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->json('departments')->change();
            $table->json('courses')->change();
            $table->json('year_levels')->change();
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->text('departments')->change();
            $table->text('courses')->change();
            $table->text('year_levels')->change();
        });
    }
};
