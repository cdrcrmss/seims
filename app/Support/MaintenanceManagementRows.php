<?php

namespace App\Support;

use App\Models\ItemUnit;
use App\Models\MaintenanceRecord;
use Illuminate\Support\Collection;

class MaintenanceManagementRows
{
    /**
     * Build a unified, priority-sorted list for the maintenance dashboard table.
     *
     * @param  Collection<int, ItemUnit>  $criticalUnits
     * @param  Collection<int, MaintenanceRecord>  $overdueMaintenance
     * @param  Collection<int, MaintenanceRecord>  $upcomingMaintenance
     * @return array<int, array<string, mixed>>
     */
    public static function build(Collection $criticalUnits, Collection $overdueMaintenance, Collection $upcomingMaintenance): array
    {
        $rows = [];
        $seenRecordIds = [];
        $seenUnitIds = [];

        foreach ($criticalUnits as $unit) {
            $record = $unit->activeMaintenanceRecord;
            if ($record) {
                $seenRecordIds[$record->id] = true;
            }
            $seenUnitIds[$unit->id] = true;

            $rows[] = [
                'sort' => 0,
                'priority' => 'critical',
                'priority_label' => 'Critical',
                'item_name' => $unit->item?->name ?? 'Unknown item',
                'category' => $unit->item?->category ?? '—',
                'asset_code' => $unit->unit_code,
                'maintenance_type' => $record ? ucfirst($record->maintenance_type) : 'Corrective',
                'scheduled_date' => $record?->scheduled_date,
                'status_label' => ucfirst(str_replace('_', ' ', $unit->status)),
                'unit' => $unit,
                'record' => null,
            ];
        }

        foreach ($overdueMaintenance as $record) {
            if (isset($seenRecordIds[$record->id])) {
                continue;
            }
            if ($record->item_unit_id && isset($seenUnitIds[$record->item_unit_id])) {
                continue;
            }
            $seenRecordIds[$record->id] = true;

            $rows[] = self::recordRow($record, 'overdue', 'Overdue', 1);
        }

        foreach ($upcomingMaintenance->sortBy('scheduled_date') as $record) {
            if (isset($seenRecordIds[$record->id])) {
                continue;
            }
            $seenRecordIds[$record->id] = true;

            $rows[] = self::recordRow($record, 'upcoming', 'Scheduled', 2);
        }

        usort($rows, function (array $a, array $b) {
            if ($a['sort'] !== $b['sort']) {
                return $a['sort'] <=> $b['sort'];
            }

            $dateA = $a['scheduled_date']?->timestamp ?? PHP_INT_MAX;
            $dateB = $b['scheduled_date']?->timestamp ?? PHP_INT_MAX;

            return $dateA <=> $dateB;
        });

        return $rows;
    }

    protected static function recordRow(MaintenanceRecord $record, string $priority, string $priorityLabel, int $sort): array
    {
        return [
            'sort' => $sort,
            'priority' => $priority,
            'priority_label' => $priorityLabel,
            'item_name' => $record->item?->name ?? 'Unknown',
            'category' => $record->item?->category ?? '—',
            'asset_code' => $record->itemUnit?->unit_code
                ?? ($record->item?->qr_code ? substr($record->item->qr_code, 0, 20) : '—'),
            'maintenance_type' => ucfirst($record->maintenance_type),
            'scheduled_date' => $record->scheduled_date,
            'status_label' => $priority === 'overdue' ? 'Overdue' : ucfirst($record->status),
            'unit' => $record->itemUnit,
            'record' => $record,
        ];
    }
}
