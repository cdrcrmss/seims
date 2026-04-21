<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Fixes:
     * - Add `returned_to` column to borrowings (was being set but column didn't exist in fillable)
     * - Add `cancellation_reason` column to borrowings (for soft cancel instead of hard delete)
     * - Add performance indexes on frequently queried columns
     * - Add `student_id` column to users
     */
    public function up(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            if (!Schema::hasColumn('borrowings', 'returned_to')) {
                $table->unsignedBigInteger('returned_to')->nullable()->after('returned_date');
                $table->foreign('returned_to')->references('id')->on('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('borrowings', 'cancellation_reason')) {
                $table->string('cancellation_reason')->nullable()->after('rejection_reason');
            }
        });

        // Add indexes safely using raw SQL with IF NOT EXISTS logic
        $this->addIndexSafely('borrowings', 'expected_return_date', 'borrowings_expected_return_date_index');
        $this->addIndexSafely('borrowings', 'status', 'borrowings_status_index');
        $this->addIndexSafely('items', 'available_stock', 'items_available_stock_index');
        $this->addIndexSafely('items', 'status', 'items_status_index');
        $this->addIndexSafely('items', 'wear_level', 'items_wear_level_index');
        $this->addIndexSafely('items', 'category', 'items_category_index');
        // Note: reservations.status index already created in create_reservations_table migration

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'student_id')) {
                $table->string('student_id', 50)->nullable()->after('role');
            }
        });
    }

    /**
     * Safely add an index, skipping if it already exists (SQLite compatible)
     */
    private function addIndexSafely(string $table, string $column, string $indexName): void
    {
        try {
            Schema::table($table, function (Blueprint $table) use ($column, $indexName) {
                $table->index($column, $indexName);
            });
        } catch (\Exception $e) {
            // Index already exists, skip
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            if (Schema::hasColumn('borrowings', 'returned_to')) {
                try { $table->dropForeign(['returned_to']); } catch (\Exception $e) {}
                $table->dropColumn('returned_to');
            }
            if (Schema::hasColumn('borrowings', 'cancellation_reason')) {
                $table->dropColumn('cancellation_reason');
            }
        });

        // Drop indexes safely
        $this->dropIndexSafely('borrowings', 'borrowings_expected_return_date_index');
        $this->dropIndexSafely('borrowings', 'borrowings_status_index');
        $this->dropIndexSafely('items', 'items_available_stock_index');
        $this->dropIndexSafely('items', 'items_status_index');
        $this->dropIndexSafely('items', 'items_wear_level_index');
        $this->dropIndexSafely('items', 'items_category_index');
        // Note: reservations.status index belongs to create_reservations_table, not this migration

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'student_id')) {
                $table->dropColumn('student_id');
            }
        });
    }

    private function dropIndexSafely(string $table, string $indexName): void
    {
        try {
            Schema::table($table, function (Blueprint $table) use ($indexName) {
                $table->dropIndex($indexName);
            });
        } catch (\Exception $e) {
            // Index doesn't exist, skip
        }
    }
};
