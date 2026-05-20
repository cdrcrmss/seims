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
        'notes',
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
     * Check for scheduling conflicts (checks both pending and approved reservations)
     */
    public function hasConflict()
    {
        $query = self::whereIn('status', ['approved', 'pending'])
            ->where('id', '!=', $this->id ?? 0);

        // Build resource conflict conditions using OR logic
        $query->where(function ($resourceQuery) {
            $hasCondition = false;
            if ($this->item_id) {
                $resourceQuery->where('item_id', $this->item_id);
                $hasCondition = true;
            }
            if ($this->room_id) {
                if ($hasCondition) {
                    $resourceQuery->orWhere('room_id', $this->room_id);
                } else {
                    $resourceQuery->where('room_id', $this->room_id);
                }
            }
        });

        return $query
            ->where('start_datetime', '<', $this->end_datetime)
            ->where('end_datetime', '>', $this->start_datetime)
            ->exists();
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
