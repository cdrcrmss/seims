<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('maintenance_records', function (Blueprint $table) {
            // SLA tracking
            $table->timestamp('started_at')->nullable()->after('scheduled_date');
            $table->string('sla_status')->default('open')->after('status');
            // open, in_progress, waiting_parts, completed, verified

            // Auto-ticket source
            $table->unsignedBigInteger('triggered_by_borrowing_id')->nullable()->after('notes');
            $table->string('trigger_source')->nullable()->after('triggered_by_borrowing_id');
            // return_inspection, predictive_alert, manual, usage_threshold

            // Repair data
            $table->string('fault_type')->nullable()->after('issues_found');
            $table->json('parts_used')->nullable()->after('fault_type');
            $table->integer('labor_minutes')->nullable()->after('parts_used');
            $table->string('condition_evidence_before')->nullable()->after('condition_before');
            $table->string('condition_evidence_after')->nullable()->after('condition_after');

            // Priority
            $table->string('priority')->default('normal')->after('sla_status');
            // low, normal, high, critical

            // Indexes
            $table->index('sla_status');
            $table->index('trigger_source');
            $table->index('priority');
        });

        // Update status enum to include new SLA states (MySQL)
        // Since Laravel doesn't support enum modification cleanly, we'll change to string
        Schema::table('maintenance_records', function (Blueprint $table) {
            $table->string('status')->default('scheduled')->change();
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_records', function (Blueprint $table) {
            $table->dropIndex(['sla_status']);
            $table->dropIndex(['trigger_source']);
            $table->dropIndex(['priority']);
            $table->dropColumn([
                'started_at', 'sla_status', 'triggered_by_borrowing_id',
                'trigger_source', 'fault_type', 'parts_used', 'labor_minutes',
                'condition_evidence_before', 'condition_evidence_after', 'priority',
            ]);
        });
    }
};
