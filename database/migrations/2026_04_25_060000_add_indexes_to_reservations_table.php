<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->index(['room_id', 'status', 'start_datetime', 'end_datetime'], 'reservations_room_status_schedule_index');
            $table->index(['user_id', 'status'], 'reservations_user_status_index');
            $table->index(['status', 'end_datetime'], 'reservations_status_end_index');
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropIndex('reservations_room_status_schedule_index');
            $table->dropIndex('reservations_user_status_index');
            $table->dropIndex('reservations_status_end_index');
        });
    }
};
