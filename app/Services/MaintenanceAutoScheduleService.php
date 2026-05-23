<?php

namespace App\Services;

use App\Models\Item;
use App\Models\ItemUnit;
use App\Models\MaintenanceRecord;
use App\Models\Notification;
use App\Models\User;

class MaintenanceAutoScheduleService
{
    /**
     * Run all automatic maintenance scheduling (corrective + predictive).
     */
    public static function syncAllScheduledMaintenance(): void
    {
        static::healUnitsStuckAfterCompletedMaintenance();
        static::ensureCorrectiveRecordsForCriticalUnits();
        static::ensurePredictiveRecordsForHighWearItems();
    }

    /**
     * Units left in damaged/maintenance after maintenance was marked completed (legacy data).
     */
    public static function healUnitsStuckAfterCompletedMaintenance(): int
    {
        $service = new self();
        $healed = 0;

        $units = ItemUnit::query()
            ->whereIn('status', ['maintenance', 'damaged', 'needs_repair'])
            ->whereDoesntHave('maintenanceRecords', fn ($q) => $q->where('status', 'scheduled'))
            ->whereHas('maintenanceRecords', fn ($q) => $q->where('status', 'completed'))
            ->with(['item'])
            ->get();

        foreach ($units as $unit) {
            $latest = $unit->maintenanceRecords()
                ->where('status', 'completed')
                ->orderByDesc('completed_date')
                ->first();

            if (! $latest) {
                continue;
            }

            $service->restoreUnitAfterCompletedMaintenance(
                $unit,
                $latest->condition_after ?? 'good'
            );
            $healed++;
        }

        return $healed;
    }

    /**
     * Units that need immediate attention (damaged or in corrective maintenance).
     */
    public static function criticalUnits()
    {
        return ItemUnit::query()
            ->with(['item', 'activeMaintenanceRecord'])
            ->whereIn('status', ['maintenance', 'damaged', 'needs_repair'])
            ->whereHas('item')
            ->orderBy('unit_code')
            ->get();
    }

    /**
     * Backfill corrective maintenance for units already flagged critical.
     */
    public static function ensureCorrectiveRecordsForCriticalUnits(): int
    {
        $service = new self();
        $created = 0;

        foreach (static::criticalUnits() as $unit) {
            $hasScheduled = MaintenanceRecord::where('item_unit_id', $unit->id)
                ->where('status', 'scheduled')
                ->exists();

            if (! $hasScheduled) {
                if (in_array($unit->status, ['damaged', 'needs_repair'], true)) {
                    $service->scheduleForDamagedUnit($unit, [
                        'return_condition' => $unit->status,
                    ]);
                } else {
                    $service->ensureCorrectiveRecord($unit);
                }
                $created++;
            }
        }

        return $created;
    }

    /**
     * Auto-schedule predictive maintenance for high-wear items (no active scheduled record).
     */
    public static function ensurePredictiveRecordsForHighWearItems(): int
    {
        $analytics = app(PredictiveAnalyticsService::class);
        $criticalItemIds = static::criticalUnits()->pluck('item_id')->unique();
        $created = 0;

        $items = Item::query()
            ->where('wear_level', '>=', 50)
            ->whereNotIn('status', ['disposed', 'retired', 'lost'])
            ->when($criticalItemIds->isNotEmpty(), fn ($q) => $q->whereNotIn('id', $criticalItemIds))
            ->get();

        foreach ($items as $item) {
            $hasScheduled = MaintenanceRecord::where('item_id', $item->id)
                ->where('status', 'scheduled')
                ->exists();

            if ($hasScheduled) {
                continue;
            }

            $prediction = $analytics->predictMaintenanceNeeds($item);
            $urgency = $prediction['urgency'] ?? 'low';

            if (! in_array($urgency, ['moderate', 'high', 'critical'], true)) {
                continue;
            }

            $scheduledDate = ($prediction['next_maintenance_date'] ?? now())->copy()->startOfDay();
            if ($urgency === 'critical' || $scheduledDate->lt(now()->startOfDay())) {
                $scheduledDate = now()->startOfDay();
            }

            MaintenanceRecord::create([
                'item_id' => $item->id,
                'maintenance_type' => 'predictive',
                'scheduled_date' => $scheduledDate->toDateString(),
                'status' => 'scheduled',
                'wear_level' => $item->wear_level,
                'issues_found' => $prediction['reasoning'] ?? 'Predictive schedule based on wear analysis.',
                'notes' => 'Auto-scheduled by predictive maintenance.',
                'predictive_alert_sent' => true,
            ]);

            $created++;
        }

        return $created;
    }

    public function ensureCorrectiveRecord(ItemUnit $unit): ?MaintenanceRecord
    {
        $unit->loadMissing('item');
        $item = $unit->item;

        if (! $item) {
            return null;
        }

        $unitCode = $unit->unit_code ?: ('UNIT-' . $unit->id);

        return $this->createScheduledCorrectiveRecord(
            $item,
            $unit,
            "Unit {$unitCode} requires corrective maintenance.",
            "Auto-scheduled for unit {$unitCode} (ID #{$unit->id})."
        );
    }

    public function cancelScheduledMaintenanceForUnit(ItemUnit $unit, string $reason = ''): void
    {
        $records = MaintenanceRecord::where('item_unit_id', $unit->id)
            ->where('status', 'scheduled')
            ->get();

        foreach ($records as $record) {
            $record->update([
                'status' => 'cancelled',
                'notes' => trim(($record->notes ? $record->notes . ' ' : '') . $reason),
            ]);
        }
    }

    /**
     * Return a repaired unit to circulation and refresh parent item availability.
     */
    public function restoreUnitAfterCompletedMaintenance(ItemUnit $unit, string $conditionAfter): void
    {
        if (! in_array($unit->status, ['maintenance', 'damaged', 'needs_repair'], true)) {
            return;
        }

        $unit->update([
            'status' => 'available',
            'condition' => $conditionAfter,
        ]);

        $item = $unit->item;
        if (! $item) {
            return;
        }

        $item->syncStockFromUnits();
        $this->syncItemMaintenanceState($item);
    }

    /**
     * When a unit is marked damaged, queue corrective maintenance for that unit only.
     */
    public function scheduleForDamagedUnit(ItemUnit $unit, array $context = []): ?MaintenanceRecord
    {
        $unit->loadMissing('item');
        $item = $unit->item;

        if (! $item) {
            return null;
        }

        $unitCode = $unit->unit_code ?: ('UNIT-' . $unit->id);

        $returnNotes = trim((string) ($context['notes'] ?? ''));
        $issues = $context['issues_found'] ?? null;
        if ($issues === null) {
            $issues = match ($context['return_condition'] ?? null) {
                'damaged' => 'Returned damaged' . ($returnNotes !== '' ? ': ' . $returnNotes : '.'),
                'needs_repair' => 'Returned — needs repair' . ($returnNotes !== '' ? ': ' . $returnNotes : '.'),
                default => "Unit {$unitCode} reported as damaged.",
            };
        }

        $notes = trim("Auto-scheduled for unit {$unitCode} (ID #{$unit->id}).");

        $record = $this->createScheduledCorrectiveRecord($item, $unit, $issues, $notes);

        $unit->update(['status' => 'maintenance']);

        $this->syncItemMaintenanceState($item);
        $this->notifyStaff($item, $unit, $record);

        return $record;
    }

    protected function createScheduledCorrectiveRecord(Item $item, ItemUnit $unit, string $issuesFound, string $notes): MaintenanceRecord
    {
        $existing = MaintenanceRecord::where('item_unit_id', $unit->id)
            ->where('status', 'scheduled')
            ->where('maintenance_type', 'corrective')
            ->first();

        if ($existing) {
            $existing->update([
                'issues_found' => $issuesFound,
                'notes' => trim(($existing->notes ? $existing->notes . ' ' : '') . $notes),
            ]);

            return $existing;
        }

        return MaintenanceRecord::create([
            'item_id' => $item->id,
            'item_unit_id' => $unit->id,
            'maintenance_type' => 'corrective',
            'scheduled_date' => now()->toDateString(),
            'status' => 'scheduled',
            'issues_found' => $issuesFound,
            'notes' => $notes,
            'predictive_alert_sent' => false,
        ]);
    }

    protected function syncItemMaintenanceState(Item $item): void
    {
        if ($item->units()->exists()) {
            $item->syncStockFromUnits();
            $hasUnavailableUnits = $item->units()
                ->whereIn('status', ['maintenance', 'damaged', 'needs_repair', 'lost'])
                ->exists();

            if ($hasUnavailableUnits && $item->available_stock === 0) {
                $item->update(['status' => 'maintenance']);
            } elseif ($item->available_stock > 0 && $item->status === 'maintenance') {
                $item->update(['status' => 'available']);
            }
        } else {
            $item->update(['status' => 'maintenance']);
        }
    }

    protected function notifyStaff(Item $item, ItemUnit $unit, MaintenanceRecord $record): void
    {
        $unitCode = $unit->unit_code ?: ('UNIT-' . $unit->id);
        $staffUsers = User::whereIn('role', ['staff', 'admin'])->get();

        foreach ($staffUsers as $staff) {
            Notification::create([
                'user_id' => $staff->id,
                'type' => 'warning',
                'title' => 'Unit Queued for Maintenance',
                'message' => $item->name . ' — unit ' . $unitCode . ' is damaged and scheduled for maintenance today. Other units of this item are unaffected.',
                'action_url' => route('analytics.maintenance-predictions', ['urgency' => 'critical']),
                'priority' => 'high',
            ]);
        }
    }
}
