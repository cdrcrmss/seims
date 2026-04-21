<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // The original reservations table uses enum for status which doesn't
        // include 'checked_in' or 'no_show'. Change to string to support
        // all current and future states.
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE reservations MODIFY COLUMN status VARCHAR(30) DEFAULT 'pending'");
        }
        // SQLite already stores enums as strings, no change needed.
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            // Revert non-standard statuses before shrinking the enum
            DB::table('reservations')
                ->where('status', 'checked_in')
                ->update(['status' => 'approved']);
            DB::table('reservations')
                ->where('status', 'no_show')
                ->update(['status' => 'cancelled']);

            DB::statement("ALTER TABLE reservations MODIFY COLUMN status ENUM('pending','approved','rejected','cancelled','completed') DEFAULT 'pending'");
        }
    }
};
