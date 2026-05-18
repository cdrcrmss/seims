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
     * When a unit is marked damaged, queue corrective maintenance immediately.
     */
    public function scheduleForDamagedUnit(ItemUnit $unit, array $context = []): ?MaintenanceRecord
    {
        $unit->loadMissing('item');
        $item = $unit->item;

        if (! $item) {
            return null;
        }

        $issues = $context['issues_found']
            ?? 'Unit ' . $unit->unit_code . ' reported as damaged'
            . (isset($context['return_condition']) ? ' on return (' . $context['return_condition'] . ').' : '.');

        $notes = trim(($context['notes'] ?? '') . ' Auto-scheduled from damaged unit #' . $unit->id . '.');

        $record = $this->createScheduledCorrectiveRecord($item, $issues, $notes);

        $unit->update(['status' => 'maintenance']);

        $this->syncItemMaintenanceState($item);
        $this->notifyStaff($item, $unit, $record);

        return $record;
    }

    protected function createScheduledCorrectiveRecord(Item $item, string $issuesFound, string $notes): MaintenanceRecord
    {
        $existing = MaintenanceRecord::where('item_id', $item->id)
            ->where('status', 'scheduled')
            ->where('maintenance_type', 'corrective')
            ->whereDate('scheduled_date', '<=', now()->addDay())
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
            if ($item->units()->where('status', 'maintenance')->exists()) {
                $item->update(['status' => 'maintenance']);
            }
        } else {
            $item->update(['status' => 'maintenance']);
        }
    }

    protected function notifyStaff(Item $item, ItemUnit $unit, MaintenanceRecord $record): void
    {
        $staffUsers = User::whereIn('role', ['staff', 'admin'])->get();

        foreach ($staffUsers as $staff) {
            Notification::create([
                'user_id' => $staff->id,
                'type' => 'warning',
                'title' => 'Maintenance Auto-Scheduled',
                'message' => '"' . $item->name . '" (unit ' . $unit->unit_code . ') was marked damaged and queued for maintenance on ' . $record->scheduled_date->format('M j, Y') . '.',
                'action_url' => route('maintenance.index'),
                'priority' => 'high',
            ]);
        }
    }
}
