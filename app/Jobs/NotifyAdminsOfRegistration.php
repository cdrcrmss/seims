<?php

namespace App\Jobs;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifyAdminsOfRegistration implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $userId,
        public string $userName,
        public string $studentId,
    ) {}

    public function handle(): void
    {
        $admins = User::where('role', 'admin')->get();

        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type' => 'warning',
                'title' => 'New Student Registration',
                'message' => $this->userName . ' (' . $this->studentId . ') has registered and is awaiting approval.',
                'action_url' => route('admin.users.index', ['role' => 'student', 'approval' => 'pending']),
                'priority' => 'high',
            ]);
        }
    }
}
