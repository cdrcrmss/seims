<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('reservations')) {
            return;
        }

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE reservations MODIFY COLUMN status ENUM('pending', 'ongoing', 'approved', 'rejected', 'cancelled', 'completed') NOT NULL DEFAULT 'pending'");
        }

        DB::table('reservations')->where('status', 'approved')->update(['status' => 'ongoing']);
    }

    public function down(): void
    {
        if (! Schema::hasTable('reservations')) {
            return;
        }

        DB::table('reservations')->where('status', 'ongoing')->update(['status' => 'approved']);

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE reservations MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'cancelled', 'completed') NOT NULL DEFAULT 'pending'");
        }
    }
};
