<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add room_code to rooms table for QR-based room identification
        Schema::table('rooms', function (Blueprint $table) {
            $table->string('room_code')->nullable()->unique()->after('status');
        });

        // Add check-in fields to reservations table
        Schema::table('reservations', function (Blueprint $table) {
            $table->timestamp('checked_in_at')->nullable()->after('status');
            $table->integer('no_show_count')->default(0)->after('checked_in_at');
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn('room_code');
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn(['checked_in_at', 'no_show_count']);
        });
    }
};
