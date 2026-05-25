<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Each physical unit of an item gets its own row for individual tracking.
     */
    public function up(): void
    {
        Schema::create('item_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            $table->string('unit_code')->unique(); // e.g. SEIMS-000001-U001
            $table->string('qr_code')->unique()->nullable(); // unique QR for this specific unit
            $table->enum('status', ['available', 'borrowed', 'maintenance', 'lost', 'retired'])->default('available');
            $table->foreignId('current_borrower_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('borrowing_id')->nullable()->constrained('borrowings')->nullOnDelete();
            $table->string('condition')->default('good'); // good, fair, poor, damaged
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Add unit_id to borrowings for tracking which specific unit was borrowed
        Schema::table('borrowings', function (Blueprint $table) {
            $table->foreignId('item_unit_id')->nullable()->after('item_id')->constrained('item_units')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            $table->dropForeign(['item_unit_id']);
            $table->dropColumn('item_unit_id');
        });

        Schema::dropIfExists('item_units');
    }
};
