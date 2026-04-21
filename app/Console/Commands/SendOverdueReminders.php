<?php

namespace App\Console\Commands;

use App\Models\Borrowing;
use App\Models\Notification;
use Illuminate\Console\Command;

class SendOverdueReminders extends Command
{
    protected $signature = 'borrowings:send-overdue-reminders';
    protected $description = 'Send notifications to students with overdue borrowings';

    public function handle(): int
    {
        $overdueBorrowings = Borrowing::with(['user', 'item'])
            ->where('status', 'issued')
            ->where('expected_return_date', '<', now())
            ->get();

        $count = 0;

        foreach ($overdueBorrowings as $borrowing) {
            if (!$borrowing->user) {
                continue;
            }

            $daysOverdue = now()->diffInDays($borrowing->expected_return_date);

            // Check if we already sent a reminder today for this borrowing
            $alreadyNotified = Notification::where('user_id', $borrowing->user_id)
                ->where('type', 'danger')
                ->where('data->borrowing_id', $borrowing->id)
                ->whereDate('created_at', today())
                ->exists();

            if ($alreadyNotified) {
                continue;
            }

            // Send notification to the student
            Notification::create([
                'user_id' => $borrowing->user_id,
                'type' => 'danger',
                'title' => 'Overdue Return Reminder',
                'message' => 'Your borrowing of "' . ($borrowing->item->name ?? 'Unknown Item') . '" is ' . $daysOverdue . ' day(s) overdue. Please return it as soon as possible.',
                'data' => json_encode(['borrowing_id' => $borrowing->id]),
                'action_url' => route('student.borrowings.index'),
                'priority' => 'high',
            ]);

            // Also notify staff
            $staffUsers = \App\Models\User::whereIn('role', ['staff', 'admin'])->get();
            foreach ($staffUsers as $staff) {
                Notification::create([
                    'user_id' => $staff->id,
                    'type' => 'danger',
                    'title' => 'Overdue Borrowing Alert',
                    'message' => ($borrowing->user->name ?? 'Unknown') . ' has not returned "' . ($borrowing->item->name ?? 'Unknown Item') . '" — ' . $daysOverdue . ' day(s) overdue.',
                    'data' => json_encode(['borrowing_id' => $borrowing->id]),
                    'action_url' => route('staff.borrowings.index', ['status' => 'issued']),
                    'priority' => 'high',
                ]);
            }

            $count++;
        }

        $this->info("Sent overdue reminders for {$count} borrowing(s).");

        return Command::SUCCESS;
    }
}
