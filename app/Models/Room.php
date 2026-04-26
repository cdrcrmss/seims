<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'type',
        'capacity',
        'floor',
        'building',
        'description',
        'facilities',
        'status',
        'image_path',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
            'facilities' => 'array',
        ];
    }

    /**
     * Relationships
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Get active reservations
     */
    public function activeReservations()
    {
        return $this->reservations()
            ->whereIn('status', ['pending', 'approved'])
            ->where('end_datetime', '>=', now());
    }

    /**
     * Check if room is available for a time period
     */
    public function isAvailable($startDateTime, $endDateTime)
    {
        $query = $this->reservations()
            ->whereIn('status', \App\Services\ReservationConflictService::ACTIVE_STATUSES);

        return !\App\Services\ReservationConflictService::applyOverlapConditions($query, $startDateTime, $endDateTime)
            ->exists();
    }
}
