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
     * Reservations that overlap a time window (pending + approved block booking).
     */
    public function overlappingReservations($startDateTime, $endDateTime, ?int $excludeReservationId = null)
    {
        return $this->reservations()
            ->whereIn('status', ['pending', 'approved'])
            ->when($excludeReservationId, fn ($q) => $q->where('id', '!=', $excludeReservationId))
            ->where('start_datetime', '<', $endDateTime)
            ->where('end_datetime', '>', $startDateTime);
    }

    /**
     * Check if room is available for a time period
     */
    public function isAvailable($startDateTime, $endDateTime, ?int $excludeReservationId = null): bool
    {
        return ! $this->overlappingReservations($startDateTime, $endDateTime, $excludeReservationId)->exists();
    }
}
