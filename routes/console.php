<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule overdue borrowing reminders daily at 8 AM
Schedule::command('borrowings:send-overdue-reminders')->dailyAt('08:00');

// Auto-complete past reservations every hour
Schedule::command('reservations:auto-complete')->hourly();
