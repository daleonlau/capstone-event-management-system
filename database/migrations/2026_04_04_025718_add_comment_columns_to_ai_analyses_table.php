<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ai_analyses', function (Blueprint $table) {
            $table->longText('positive_comments')->nullable()->after('sentiment_analysis');
            $table->longText('negative_comments')->nullable()->after('positive_comments');
            $table->longText('neutral_comments')->nullable()->after('negative_comments');
        });
    }

    public function down()
    {
        Schema::table('ai_analyses', function (Blueprint $table) {
            $table->dropColumn(['positive_comments', 'negative_comments', 'neutral_comments']);
        });
    }
};