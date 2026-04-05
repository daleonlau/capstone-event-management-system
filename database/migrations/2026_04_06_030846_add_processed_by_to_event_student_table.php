<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('event_student', function (Blueprint $table) {
            $table->unsignedBigInteger('processed_by')->nullable()->after('user_id');
            // Remove the foreign key constraint for now
            // $table->foreign('processed_by')->references('id')->on('org_users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('event_student', function (Blueprint $table) {
            $table->dropColumn('processed_by');
        });
    }
};