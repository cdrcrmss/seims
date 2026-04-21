<?php

namespace App\Policies;

use App\Models\Reservation;
use App\Models\User;

class ReservationPolicy
{
    /**
     * Staff and admin can view any reservation
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Users can view their own reservations; staff/admin can view all
     */
    public function view(User $user, Reservation $reservation): bool
    {
        return $user->id === $reservation->user_id
            || in_array($user->role, ['staff', 'admin']);
    }

    /**
     * Any authenticated user can create a reservation
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Staff/admin can update (approve) any reservation
     */
    public function update(User $user, Reservation $reservation): bool
    {
        return in_array($user->role, ['staff', 'admin']);
    }

    /**
     * Owner can cancel their own pending/approved reservation; staff/admin can cancel any non-completed
     */
    public function delete(User $user, Reservation $reservation): bool
    {
        // Cannot cancel already completed or cancelled reservations
        if (in_array($reservation->status, ['cancelled', 'completed'])) {
            return false;
        }

        return $user->id === $reservation->user_id
            || in_array($user->role, ['staff', 'admin']);
    }
}
