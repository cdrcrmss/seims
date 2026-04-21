<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceRecord extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // SLA Status constants
    const SLA_OPEN = 'open';
    const SLA_IN_PROGRESS = 'in_progress';
    const SLA_WAITING_PARTS = 'waiting_parts';
    const SLA_COMPLETED = 'completed';
    const SLA_VERIFIED = 'verified';

    // Trigger sources
    const TRIGGER_RETURN_INSPECTION = 'return_inspection';
    const TRIGGER_PREDICTIVE = 'predictive_alert';
    const TRIGGER_MANUAL = 'manual';
    const TRIGGER_USAGE = 'usage_threshold';

    protected $fillable = [
        'item_id',
        'maintenance_type',
        'scheduled_date',
        'started_at',
        'completed_date',
        'performed_by',
        'condition_before',
        'condition_after',
        'condition_evidence_before',
        'condition_evidence_after',
        'wear_level',
        'issues_found',
        'fault_type',
        'parts_used',
        'labor_minutes',
        'actions_taken',
        'cost',
        'next_maintenance_date',
        'status',
        'sla_status',
        'priority',
        'notes',
        'predictive_alert_sent',
        'triggered_by_borrowing_id',
        'trigger_source',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'scheduled_date' => 'date',
            'started_at' => 'datetime',
            'completed_date' => 'date',
            'next_maintenance_date' => 'date',
            'cost' => 'decimal:2',
            'wear_level' => 'integer',
            'predictive_alert_sent' => 'boolean',
            'parts_used' => 'array',
        ];
    }

    /**
     * Relationships
     */
    public function item()
    {
        return $this->belongsTo(Item::class)->withDefault([
            'name' => 'Deleted Item',
            'image_path' => null,
        ]);
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'performed_by')->withDefault([
            'name' => 'Unknown Technician',
        ]);
    }

    /**
     * Predict next maintenance based on usage patterns
     */
    public static function predictNextMaintenance(Item $item)
    {
        $lastMaintenance = $item->maintenanceRecords()
            ->where('status', 'completed')
            ->orderBy('completed_date', 'desc')
            ->first();

        if (!$lastMaintenance) {
            return now()->addMonths(3);
        }

        // Calculate average wear per day
        $daysSinceLastMaintenance = now()->diffInDays($lastMaintenance->completed_date);
        $wearPerDay = $lastMaintenance->wear_level / max($daysSinceLastMaintenance, 1);

        // Predict when wear will reach critical level (80)
        $currentWear = $item->wear_level ?? 0;
        $daysUntilCritical = max(1, (80 - $currentWear) / max($wearPerDay, 0.1));

        return now()->addDays((int) $daysUntilCritical);
    }

    /**
     * Scope for upcoming maintenance
     */
    public function scopeUpcoming($query)
    {
        return $query->where('status', 'scheduled')
            ->where('scheduled_date', '>=', now())
            ->where('scheduled_date', '<=', now()->addDays(30));
    }

    /**
     * Scope for overdue maintenance
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', 'scheduled')
            ->where('scheduled_date', '<', now());
    }

    /**
     * Auto-create a maintenance ticket from a return inspection.
     */
    public static function createFromReturnInspection(int $itemId, int $borrowingId, string $returnCondition, ?string $notes = null): ?self
    {
        // Prevent duplicate open tickets for the same item
        $existing = static::where('item_id', $itemId)
            ->whereIn('sla_status', [self::SLA_OPEN, self::SLA_IN_PROGRESS, self::SLA_WAITING_PARTS])
            ->exists();

        if ($existing) return null;

        $item = Item::find($itemId);
        $priority = $returnCondition === 'damaged' ? 'critical' : 'high';
        $maintenanceType = $returnCondition === 'damaged' ? 'emergency' : 'corrective';

        return static::create([
            'item_id' => $itemId,
            'maintenance_type' => $maintenanceType,
            'scheduled_date' => now(),
            'condition_before' => $returnCondition,
            'wear_level' => $item->wear_level ?? 0,
            'status' => 'scheduled',
            'sla_status' => self::SLA_OPEN,
            'priority' => $priority,
            'notes' => 'Auto-generated from return inspection. ' . ($notes ?? ''),
            'triggered_by_borrowing_id' => $borrowingId,
            'trigger_source' => self::TRIGGER_RETURN_INSPECTION,
            'predictive_alert_sent' => false,
        ]);
    }

    /**
     * Auto-create maintenance from usage threshold (wear level).
     */
    public static function createFromUsageThreshold(Item $item): ?self
    {
        // Don't create if there's already an open ticket for this item
        $existing = static::where('item_id', $item->id)
            ->whereIn('sla_status', [self::SLA_OPEN, self::SLA_IN_PROGRESS, self::SLA_WAITING_PARTS])
            ->exists();

        if ($existing) return null;

        return static::create([
            'item_id' => $item->id,
            'maintenance_type' => 'preventive',
            'scheduled_date' => now()->addDays(7),
            'condition_before' => 'wear_threshold_reached',
            'wear_level' => $item->wear_level,
            'status' => 'scheduled',
            'sla_status' => self::SLA_OPEN,
            'priority' => $item->wear_level >= 80 ? 'high' : 'normal',
            'notes' => 'Auto-generated: item wear level reached ' . $item->wear_level . '%.',
            'trigger_source' => self::TRIGGER_USAGE,
            'predictive_alert_sent' => true,
        ]);
    }

    /**
     * Calculate Mean Time To Repair (MTTR) for an item in hours.
     */
    public static function mttr(?int $itemId = null): ?float
    {
        $query = static::where('status', 'completed')
            ->whereNotNull('started_at')
            ->whereNotNull('completed_date');

        if ($itemId) {
            $query->where('item_id', $itemId);
        }

        $records = $query->get();
        if ($records->isEmpty()) return null;

        $totalHours = $records->sum(function ($r) {
            return $r->started_at->diffInHours($r->completed_date);
        });

        return round($totalHours / $records->count(), 1);
    }

    /**
     * Calculate repeat failure rate for an item (tickets in last 90 days).
     */
    public static function repeatFailureRate(int $itemId): int
    {
        return static::where('item_id', $itemId)
            ->where('trigger_source', self::TRIGGER_RETURN_INSPECTION)
            ->where('created_at', '>=', now()->subDays(90))
            ->count();
    }

    /**
     * Scope for open SLA tickets.
     */
    public function scopeOpenTickets($query)
    {
        return $query->whereIn('sla_status', [self::SLA_OPEN, self::SLA_IN_PROGRESS, self::SLA_WAITING_PARTS]);
    }

    /**
     * Borrowing that triggered this ticket.
     */
    public function triggerBorrowing()
    {
        return $this->belongsTo(\App\Models\Borrowing::class, 'triggered_by_borrowing_id');
    }
}
