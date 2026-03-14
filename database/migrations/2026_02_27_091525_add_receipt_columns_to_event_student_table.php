<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('event_student', function (Blueprint $table) {
            // Check if columns exist before adding them
            if (!Schema::hasColumn('event_student', 'receipt_number')) {
                $table->string('receipt_number')->nullable()->unique()->after('amount_paid');
            }
            
            if (!Schema::hasColumn('event_student', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('receipt_number');
            }
            
            if (!Schema::hasColumn('event_student', 'payment_notes')) {
                $table->text('payment_notes')->nullable()->after('payment_method');
            }
            
            if (!Schema::hasColumn('event_student', 'receipt_pdf_path')) {
                $table->string('receipt_pdf_path')->nullable()->after('payment_notes');
            }
            
            if (!Schema::hasColumn('event_student', 'receipt_sent_at')) {
                $table->timestamp('receipt_sent_at')->nullable()->after('receipt_pdf_path');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_student', function (Blueprint $table) {
            $columns = ['receipt_number', 'payment_method', 'payment_notes', 'receipt_pdf_path', 'receipt_sent_at'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('event_student', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};