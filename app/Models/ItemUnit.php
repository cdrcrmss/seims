<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'unit_code',
        'qr_code',
        'status',
        'current_borrower_id',
        'borrowing_id',
        'condition',
        'notes',
    ];

    /**
     * Relationships
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function currentBorrower()
    {
        return $this->belongsTo(User::class, 'current_borrower_id');
    }

    public function borrowing()
    {
        return $this->belongsTo(Borrowing::class);
    }

    public function borrowings()
    {
        return $this->hasMany(Borrowing::class, 'item_unit_id');
    }

    public function maintenanceRecords()
    {
        return $this->hasMany(MaintenanceRecord::class);
    }

    public function activeMaintenanceRecord()
    {
        return $this->hasOne(MaintenanceRecord::class)
            ->where('status', 'scheduled')
            ->latestOfMany();
    }

    /**
     * Check if this unit is available
     */
    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    public function isDisposed(): bool
    {
        return $this->status === 'disposed';
    }

    /**
     * Mark unit as borrowed
     */
    public function markBorrowed(int $userId, int $borrowingId): void
    {
        $this->update([
            'status' => 'borrowed',
            'current_borrower_id' => $userId,
            'borrowing_id' => $borrowingId,
        ]);
    }

    /**
     * Mark unit as returned
     */
    public function markReturned(): void
    {
        $this->update([
            'status' => 'available',
            'current_borrower_id' => null,
            'borrowing_id' => null,
        ]);
    }

    /**
     * Mark unit as damaged and auto-schedule maintenance.
     */
    public function markDamaged(array $context = []): void
    {
        $this->update([
            'status' => 'damaged',
            'current_borrower_id' => null,
            'borrowing_id' => null,
        ]);

        app(\App\Services\MaintenanceAutoScheduleService::class)
            ->scheduleForDamagedUnit($this->fresh(), $context);
    }

    /**
     * Mark unit as needs repair
     */
    public function markNeedsRepair(): void
    {
        $this->update([
            'status' => 'needs_repair',
            'current_borrower_id' => null,
            'borrowing_id' => null,
        ]);
    }
}
