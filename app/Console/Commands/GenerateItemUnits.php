<?php

namespace App\Console\Commands;

use App\Models\Item;
use App\Support\InventoryCodes;
use App\Models\ItemUnit;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateItemUnits extends Command
{
    protected $signature = 'items:generate-units';
    protected $description = 'Generate individual trackable units for all existing items that do not have units yet';

    public function handle(): int
    {
        $items = Item::doesntHave('units')->get();

        if ($items->isEmpty()) {
            $this->info('All items already have units.');
            return 0;
        }

        $totalUnits = 0;

        foreach ($items as $item) {
            $itemPad = str_pad($item->id, 6, '0', STR_PAD_LEFT);

            for ($i = 1; $i <= $item->total_stock; $i++) {
                $unitCode = InventoryCodes::unitCode($item->id, $i);
                $qrCode = $unitCode . '-' . strtoupper(Str::random(6));

                ItemUnit::create([
                    'item_id' => $item->id,
                    'unit_code' => $unitCode,
                    'qr_code' => $qrCode,
                    'status' => 'available',
                    'condition' => 'good',
                ]);

                $totalUnits++;
            }

            $this->line("  Created {$item->total_stock} units for: {$item->name}");
        }

        $this->info("Done! Generated {$totalUnits} units for {$items->count()} items.");
        return 0;
    }
}
