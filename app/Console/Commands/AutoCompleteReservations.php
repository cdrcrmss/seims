<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use Illuminate\Console\Command;

class AutoCompleteReservations extends Command
{
    protected $signature = 'reservations:auto-complete';
    protected $description = 'Auto-complete approved reservations whose end_datetime has passed';

    public function handle(): int
    {
        $count = Reservation::whereIn('status', ['approved', 'checked_in'])
            ->where('end_datetime', '<', now())
            ->update(['status' => 'completed']);

        $this->info("Auto-completed {$count} past reservation(s).");

        return Command::SUCCESS;
    }
}
