<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Fix critical schema issues before deployment:
     *
     * 1. Add 'faculty' to users.role enum (was named but never implemented)
     * 2. Change onDelete('cascade') to onDelete('restrict') for tables with soft deletes
     *    - Prevents forceDelete() from bypassing child soft deletes
     * 3. Add soft deletes to rooms table (reservations already has soft deletes)
     * 4. Add missing performance indexes
     */
    public function up(): void
    {
        $isMysql = DB::getDriverName() === 'mysql';

        // 1. Add 'faculty' to users.role enum
        if ($isMysql) {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('student', 'staff', 'admin', 'faculty') NOT NULL DEFAULT 'student'");
        } else {
            // SQLite stores enums as strings, so 'faculty' is already accepted
            // No schema change needed
        }

        // 2. Add soft deletes to rooms table
        if (!Schema::hasColumn('rooms', 'deleted_at')) {
            Schema::table('rooms', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // 3. Fix foreign key cascades on tables with soft deletes
        //    Change CASCADE to RESTRICT so forceDelete() cannot silently destroy child records
        //    SQLite does not support ALTER TABLE for foreign keys, skip on SQLite
        if ($isMysql) {
            // -- borrowings.user_id: cascade -> restrict
            $this->changeForeignKey('borrowings', 'user_id', 'users', 'restrict');

            // -- borrowings.item_id: cascade -> restrict
            $this->changeForeignKey('borrowings', 'item_id', 'items', 'restrict');

            // -- reservations.user_id: cascade -> restrict
            $this->changeForeignKey('reservations', 'user_id', 'users', 'restrict');

            // -- reservations.item_id: cascade -> restrict (nullable)
            $this->changeForeignKey('reservations', 'item_id', 'items', 'restrict');

            // -- reservations.room_id: cascade -> restrict (nullable)
            $this->changeForeignKey('reservations', 'room_id', 'rooms', 'restrict');

            // -- procurement_requests.item_id: cascade -> restrict
            $this->changeForeignKey('procurement_requests', 'item_id', 'items', 'restrict');

            // -- procurement_requests.requested_by: cascade -> restrict
            $this->changeForeignKey('procurement_requests', 'requested_by', 'users', 'restrict');

            // -- maintenance_records.item_id: cascade -> restrict (preserves maintenance history)
            $this->changeForeignKey('maintenance_records', 'item_id', 'items', 'restrict');

            // -- notifications.user_id: cascade -> restrict (preserves notification records)
            $this->changeForeignKey('notifications', 'user_id', 'users', 'restrict');

            // -- item_supplier: keep cascade (no soft deletes, pivot table cleanup is fine)
        }

        // 4. Add missing performance indexes
        $this->addIndexSafely('users', 'role', 'users_role_index');
        $this->addIndexSafely('users', 'student_id', 'users_student_id_index');
        $this->addIndexSafely('users', 'is_approved', 'users_is_approved_index');
        $this->addIndexSafely('items', 'barcode', 'items_barcode_index');
        $this->addIndexSafely('items', 'name', 'items_name_index');
        $this->addIndexSafely('items', 'location', 'items_location_index');
        $this->addIndexSafely('borrowings', 'returned_date', 'borrowings_returned_date_index');
        $this->addIndexSafely('borrowings', 'extension_status', 'borrowings_extension_status_index');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $isMysql = DB::getDriverName() === 'mysql';

        // Revert users.role enum
        if ($isMysql) {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('student', 'staff', 'admin') NOT NULL DEFAULT 'student'");
        }

        // Remove soft deletes from rooms
        if (Schema::hasColumn('rooms', 'deleted_at')) {
            Schema::table('rooms', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        // Revert foreign keys back to cascade
        if ($isMysql) {
            $this->changeForeignKey('borrowings', 'user_id', 'users', 'cascade');
            $this->changeForeignKey('borrowings', 'item_id', 'items', 'cascade');
            $this->changeForeignKey('reservations', 'user_id', 'users', 'cascade');
            $this->changeForeignKey('reservations', 'item_id', 'items', 'cascade');
            $this->changeForeignKey('reservations', 'room_id', 'rooms', 'cascade');
            $this->changeForeignKey('procurement_requests', 'item_id', 'items', 'cascade');
            $this->changeForeignKey('procurement_requests', 'requested_by', 'users', 'cascade');
            $this->changeForeignKey('maintenance_records', 'item_id', 'items', 'cascade');
            $this->changeForeignKey('notifications', 'user_id', 'users', 'cascade');
        }

        // Drop added indexes
        $this->dropIndexSafely('users', 'users_role_index');
        $this->dropIndexSafely('users', 'users_student_id_index');
        $this->dropIndexSafely('users', 'users_is_approved_index');
        $this->dropIndexSafely('items', 'items_barcode_index');
        $this->dropIndexSafely('items', 'items_name_index');
        $this->dropIndexSafely('items', 'items_location_index');
        $this->dropIndexSafely('borrowings', 'borrowings_returned_date_index');
        $this->dropIndexSafely('borrowings', 'borrowings_extension_status_index');
    }

    /**
     * Drop and re-create a foreign key with a new onDelete action.
     */
    private function changeForeignKey(string $table, string $column, string $referencedTable, string $onDelete): void
    {
        Schema::table($table, function (Blueprint $t) use ($column) {
            $t->dropForeign([$column]);
        });

        Schema::table($table, function (Blueprint $t) use ($column, $referencedTable, $onDelete) {
            $t->foreign($column)
              ->references('id')
              ->on($referencedTable)
              ->onDelete($onDelete);
        });
    }

    private function addIndexSafely(string $table, string $column, string $indexName): void
    {
        try {
            Schema::table($table, function (Blueprint $t) use ($column, $indexName) {
                $t->index($column, $indexName);
            });
        } catch (\Exception $e) {
            // Index already exists, skip
        }
    }

    private function dropIndexSafely(string $table, string $indexName): void
    {
        try {
            Schema::table($table, function (Blueprint $t) use ($indexName) {
                $t->dropIndex($indexName);
            });
        } catch (\Exception $e) {
            // Index doesn't exist, skip
        }
    }
};
