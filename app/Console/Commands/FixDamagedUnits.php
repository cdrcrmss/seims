<?php

namespace App\Console\Commands;

use App\Models\Borrowing;
use App\Models\Item;
use App\Models\ItemUnit;
use Illuminate\Console\Command;

class FixDamagedUnits extends Command
{
    protected $signature = 'fix:damaged-units';
    protected $description = 'Fix units that were returned as damaged but still show as available';

    public function handle()
    {
        $this->info('Scanning for borrowings returned as damaged...');

        $damagedBorrowings = Borrowing::where('status', 'returned')
            ->where('return_condition', 'damaged')
            ->whereNotNull('item_unit_id')
            ->get();

        $fixed = 0;

        foreach ($damagedBorrowings as $borrowing) {
            $unit = ItemUnit::find($borrowing->item_unit_id);
            if ($unit && $unit->status !== 'damaged') {
                $unit->update(['status' => 'damaged']);
                $this->line("  Fixed unit {$unit->unit_code} → damaged");
                $fixed++;
            }
        }

        // Also fix borrowings returned as needs_repair
        $repairBorrowings = Borrowing::where('status', 'returned')
            ->where('return_condition', 'needs_repair')
            ->whereNotNull('item_unit_id')
            ->get();

        foreach ($repairBorrowings as $borrowing) {
            $unit = ItemUnit::find($borrowing->item_unit_id);
            if (! $unit) {
                continue;
            }

            $hasScheduled = \App\Models\MaintenanceRecord::where('item_unit_id', $unit->id)
                ->where('status', 'scheduled')
                ->exists();

            if (! $hasScheduled) {
                $returnNotes = $borrowing->return_notes ? ': ' . $borrowing->return_notes : '.';
                app(\App\Services\MaintenanceAutoScheduleService::class)
                    ->scheduleForDamagedUnit($unit, [
                        'return_condition' => 'needs_repair',
                        'issues_found' => 'Returned — needs repair' . $returnNotes,
                    ]);
                $this->line("  Scheduled maintenance for unit {$unit->unit_code} (needs repair return)");
                $fixed++;
            }
        }

        // Reset parent item status if it was set to 'damaged' but not all units are damaged
        $damagedItems = Item::where('status', 'damaged')->get();
        $itemsFixed = 0;

        foreach ($damagedItems as $item) {
            $totalUnits = $item->units()->count();
            $damagedUnits = $item->units()->where('status', 'damaged')->count();

            if ($totalUnits > 0 && $damagedUnits < $totalUnits) {
                $item->update(['status' => 'available']);
                $this->line("  Reset item '{$item->name}' status → available (only {$damagedUnits}/{$totalUnits} units damaged)");
                $itemsFixed++;
            }
        }

        $this->info("Done! Fixed {$fixed} unit(s) and {$itemsFixed} item status(es).");
    }
}
