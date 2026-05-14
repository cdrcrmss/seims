<?php

namespace App\Jobs;

use App\Models\AuditLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CreateAuditLogJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly array $data
    ) {}

    public function handle(): void
    {
        AuditLog::create($this->data);
    }
}
