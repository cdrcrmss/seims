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
        Schema::create('maintenance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            $table->enum('maintenance_type', ['preventive', 'corrective', 'predictive', 'routine', 'emergency'])->default('routine');
            $table->date('scheduled_date');
            $table->date('completed_date')->nullable();
            $table->foreignId('performed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('condition_before')->nullable();
            $table->string('condition_after')->nullable();
            $table->integer('wear_level')->default(0)->comment('0-100 scale');
            $table->text('issues_found')->nullable();
            $table->text('actions_taken')->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            $table->date('next_maintenance_date')->nullable();
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled');
            $table->text('notes')->nullable();
            $table->boolean('predictive_alert_sent')->default(false);
            $table->timestamps();

            $table->index(['item_id', 'status']);
            $table->index('scheduled_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_records');
    }
};
