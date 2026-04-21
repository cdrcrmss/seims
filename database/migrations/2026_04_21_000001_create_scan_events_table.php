<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scan_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('action_type'); // borrow_issue, borrow_return, room_check_in, maintenance_start, maintenance_complete, item_lookup
            $table->string('target_type'); // item, room, borrowing, reservation, maintenance
            $table->unsignedBigInteger('target_id');
            $table->string('outcome'); // success, warning, blocked
            $table->string('outcome_message')->nullable();
            $table->string('qr_code_value')->nullable();
            $table->json('metadata')->nullable(); // extra context (condition, notes, etc.)
            $table->string('ip_address')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'action_type']);
            $table->index(['target_type', 'target_id']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scan_events');
    }
};
