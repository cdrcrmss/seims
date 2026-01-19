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
        Schema::table('borrowings', function (Blueprint $table) {
            // Change status enum to include 'rejected'
            $table->string('status')->default('pending')->change();
            
            // Add rejection tracking fields
            $table->text('rejection_reason')->nullable()->after('notes');
            $table->foreignId('rejected_by')->nullable()->constrained('users')->nullOnDelete()->after('rejection_reason');
            $table->timestamp('rejected_date')->nullable()->after('rejected_by');
            
            // Add approval and issue tracking fields
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete()->after('approved_date');
            $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete()->after('issued_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            $table->dropForeign(['rejected_by']);
            $table->dropForeign(['approved_by']);
            $table->dropForeign(['issued_by']);
            $table->dropColumn(['rejection_reason', 'rejected_by', 'rejected_date', 'approved_by', 'issued_by']);
        });
    }
};
