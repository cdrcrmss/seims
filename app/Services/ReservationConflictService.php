<?php

namespace App\Services;

use App\Models\Reservation;
use Illuminate\Database\Eloquent\Builder;

class ReservationConflictService
{
    /**
     * Statuses considered "active" for conflict detection.
     */
    public const ACTIVE_STATUSES = ['pending', 'approved', 'checked_in'];

    /**
     * Apply the standard time-overlap conditions to a query.
     */
    public static function applyOverlapConditions(Builder $query, string $start, string $end): Builder
    {
        return $query->where(function ($q) use ($start, $end) {
            $q->whereBetween('start_datetime', [$start, $end])
                ->orWhereBetween('end_datetime', [$start, $end])
                ->orWhere(function ($q2) use ($start, $end) {
                    $q2->where('start_datetime', '<=', $start)
                        ->where('end_datetime', '>=', $end);
                });
        });
    }

    /**
     * Check if a given reservation conflicts with any existing active reservation.
     */
    public static function hasConflict(Reservation $reservation): bool
    {
        $query = Reservation::whereIn('status', self::ACTIVE_STATUSES)
            ->where('id', '!=', $reservation->id ?? 0);

        $query->where(function ($resourceQuery) use ($reservation) {
            $hasCondition = false;
            if ($reservation->item_id) {
                $resourceQuery->where('item_id', $reservation->item_id);
                $hasCondition = true;
            }
            if ($reservation->room_id) {
                if ($hasCondition) {
                    $resourceQuery->orWhere('room_id', $reservation->room_id);
                } else {
                    $resourceQuery->where('room_id', $reservation->room_id);
                }
            }
        });

        return self::applyOverlapConditions($query, $reservation->start_datetime, $reservation->end_datetime)
            ->exists();
    }

    /**
     * Check if a user already has an overlapping reservation for the same room.
     */
    public static function userHasDuplicate(int $userId, int $roomId, string $start, string $end): bool
    {
        $query = Reservation::where('user_id', $userId)
            ->whereIn('status', ['pending', 'approved'])
            ->where('room_id', $roomId);

        return self::applyOverlapConditions($query, $start, $end)->exists();
    }

    /**
     * Check if a room has any approved conflict for a time period.
     */
    public static function roomHasApprovedConflict(int $roomId, string $start, string $end): bool
    {
        $query = Reservation::where('status', 'approved')
            ->where('room_id', $roomId);

        return self::applyOverlapConditions($query, $start, $end)->exists();
    }

    /**
     * Get conflicting reservations for a resource in a time window.
     */
    public static function getConflicts(?int $itemId, ?int $roomId, string $start, string $end): array
    {
        $conflicts = [];

        if ($itemId) {
            $query = Reservation::whereIn('status', self::ACTIVE_STATUSES)
                ->where('item_id', $itemId);

            $itemConflicts = self::applyOverlapConditions($query, $start, $end)->get();

            foreach ($itemConflicts as $conflict) {
                $conflicts[] = [
                    'type' => 'item',
                    'reservation_id' => $conflict->id,
                    'start_datetime' => $conflict->start_datetime->toDateTimeString(),
                    'end_datetime' => $conflict->end_datetime->toDateTimeString(),
                ];
            }
        }

        if ($roomId) {
            $query = Reservation::whereIn('status', self::ACTIVE_STATUSES)
                ->where('room_id', $roomId);

            $roomConflicts = self::applyOverlapConditions($query, $start, $end)->get();

            foreach ($roomConflicts as $conflict) {
                $conflicts[] = [
                    'type' => 'room',
                    'reservation_id' => $conflict->id,
                    'start_datetime' => $conflict->start_datetime->toDateTimeString(),
                    'end_datetime' => $conflict->end_datetime->toDateTimeString(),
                ];
            }
        }

        return $conflicts;
    }
}
