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
     * Scope for upcoming maintenance (today through next 30 days).
     */
    public function scopeUpcoming($query)
    {
        return $query->where('status', 'scheduled')
            ->whereDate('scheduled_date', '>=', now()->toDateString())
            ->whereDate('scheduled_date', '<=', now()->addDays(30)->toDateString());
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
