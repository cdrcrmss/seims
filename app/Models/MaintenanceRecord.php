<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceRecord extends Model
{
    use HasFactory;

    /** Predictive wear % applied when maintenance is completed (matches item condition bands). */
    public const WEAR_BY_CONDITION_AFTER = [
        'excellent' => 10,
        'good' => 25,
        'fair' => 45,
        'poor' => 65,
        'critical' => 80,
    ];

    /** Wear % for scheduling from current condition (same bands as completion). */
    public static function wearForCondition(string $condition): int
    {
        return self::WEAR_BY_CONDITION_AFTER[$condition] ?? self::WEAR_BY_CONDITION_AFTER['good'];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'item_id',
        'item_unit_id',
        'maintenance_type',
        'scheduled_date',
        'completed_date',
        'performed_by',
        'condition_before',
        'condition_after',
        'wear_level',
        'issues_found',
        'actions_taken',
        'cost',
        'next_maintenance_date',
        'status',
        'notes',
        'predictive_alert_sent',
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
            'completed_date' => 'date',
            'next_maintenance_date' => 'date',
            'cost' => 'decimal:2',
            'wear_level' => 'integer',
            'predictive_alert_sent' => 'boolean',
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

    public function itemUnit()
    {
        return $this->belongsTo(ItemUnit::class);
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
     * Scheduled date is before today (date-only; same day is not overdue).
     */
    public function isScheduleOverdue(): bool
    {
        if ($this->status !== 'scheduled' || ! $this->scheduled_date) {
            return false;
        }

        return $this->scheduled_date->startOfDay()->lt(now()->startOfDay());
    }

    /**
     * Human-readable label for maintenance_type (DB value unchanged).
     */
    /**
     * Wear level predicted from post-maintenance condition (excellent → low wear, poor → high).
     */
    public static function predictiveWearForCondition(string $conditionAfter): int
    {
        return self::wearForCondition($conditionAfter);
    }

    /**
     * Issues found text enriched with return notes from the borrowing that triggered repair.
     */
    public function issuesFoundDisplay(?Borrowing $returnBorrowing = null): string
    {
        $text = trim((string) $this->issues_found);
        $returnNotes = trim((string) ($returnBorrowing?->return_notes ?? ''));

        if ($returnNotes === '') {
            return $text;
        }

        if ($returnNotes !== '' && str_contains($text, $returnNotes)) {
            return $text;
        }

        if ($text !== '') {
            return rtrim($text, '.') . '. Return notes: ' . $returnNotes;
        }

        $condition = $returnBorrowing?->return_condition;
        $prefix = match ($condition) {
            'damaged' => 'Returned damaged',
            'needs_repair' => 'Returned — needs repair',
            default => 'Returned',
        };

        return $prefix . ': ' . $returnNotes;
    }

    public function typeLabel(): string
    {
        return match ($this->maintenance_type) {
            'corrective' => 'Repair',
            'preventive' => 'Preventive',
            'predictive' => 'Predictive',
            'routine' => 'Routine inspection',
            'emergency' => 'Emergency',
            default => ucfirst((string) $this->maintenance_type),
        };
    }

    /** Unit code, item QR, or synthetic item id for list display. */
    public function equipmentCodeDisplay(): string
    {
        if ($this->itemUnit?->unit_code) {
            return $this->itemUnit->unit_code;
        }

        if ($this->relationLoaded('item') || $this->item) {
            $item = $this->item;
            if ($item?->qr_code) {
                return $item->qr_code;
            }
            if ($item?->asset_code) {
                return $item->asset_code;
            }
        }

        return $this->item_id
            ? 'ITEM-' . str_pad((string) $this->item_id, 6, '0', STR_PAD_LEFT)
            : '—';
    }

    /**
     * Scope for upcoming maintenance (scheduled today or later).
     */
    public function scopeUpcoming($query)
    {
        return $query->where('status', 'scheduled')
            ->whereDate('scheduled_date', '>=', now()->toDateString());
    }

    /**
     * Scope for overdue maintenance (scheduled before today).
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', 'scheduled')
            ->whereDate('scheduled_date', '<', now()->toDateString());
    }
}
