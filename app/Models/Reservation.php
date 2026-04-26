<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'item_id',
        'room_id',
        'reservation_type',
        'start_datetime',
        'end_datetime',
        'purpose',
        'status',
        'approved_by',
        'approved_at',
        'cancelled_at',
        'cancellation_reason',
        'conflict_detected',
        'conflict_resolution_notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_datetime' => 'datetime',
            'end_datetime' => 'datetime',
            'approved_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'conflict_detected' => 'boolean',
        ];
    }

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class)->withDefault([
            'name' => 'Deleted User',
            'student_id' => null,
            'role' => 'unknown',
        ]);
    }

    public function item()
    {
        return $this->belongsTo(Item::class)->withDefault([
            'name' => 'Deleted Item',
            'image_path' => null,
        ]);
    }

    public function room()
    {
        return $this->belongsTo(Room::class)->withDefault([
            'name' => 'Deleted Room',
        ]);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Check for scheduling conflicts (delegates to ReservationConflictService)
     */
    public function hasConflict()
    {
        return \App\Services\ReservationConflictService::hasConflict($this);
    }

    /**
     * Scope for active reservations
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'approved'])
            ->where('end_datetime', '>=', now());
    }
}
