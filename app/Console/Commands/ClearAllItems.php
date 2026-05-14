<?php

namespace App\Console\Commands;

use App\Models\Item;
use App\Models\ItemUnit;
use App\Models\Borrowing;
use App\Models\Reservation;
use App\Models\MaintenanceRecord;
use Illuminate\Console\Command;

class ClearAllItems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clear-all-items';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all items and related data from the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->confirm('This will permanently delete ALL items and related data (borrowings, reservations, maintenance records). Are you sure?')) {
            $this->info('Operation cancelled.');
            return;
        }

        $this->info('Clearing all items and related data...');

        // Delete related records
        $this->info('Deleting borrowings...');
        Borrowing::query()->delete();

        $this->info('Deleting reservations...');
        Reservation::query()->delete();

        $this->info('Deleting maintenance records...');
        MaintenanceRecord::query()->delete();

        $this->info('Deleting item units...');
        ItemUnit::query()->delete();

        $this->info('Deleting items...');
        Item::query()->delete();

        $this->info('All items and related data have been cleared successfully.');
    }
}
