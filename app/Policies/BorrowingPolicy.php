<?php

namespace App\Policies;

use App\Models\Borrowing;
use App\Models\User;

class BorrowingPolicy
{
    /**
     * Staff and admin can view all borrowings.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['staff', 'admin']);
    }

    /**
     * Users can view their own borrowings; staff/admin can view all.
     */
    public function view(User $user, Borrowing $borrowing): bool
    {
        return $user->id === $borrowing->user_id
            || in_array($user->role, ['staff', 'admin']);
    }

    /**
     * Only students can create borrow requests.
     */
    public function create(User $user): bool
    {
        return $user->role === 'student';
    }

    /**
     * Only staff/admin can approve borrowings.
     */
    public function approve(User $user, Borrowing $borrowing): bool
    {
        return in_array($user->role, ['staff', 'admin']) && $borrowing->status === 'pending';
    }

    /**
     * Only staff/admin can reject borrowings.
     */
    public function reject(User $user, Borrowing $borrowing): bool
    {
        return in_array($user->role, ['staff', 'admin']) && $borrowing->status === 'pending';
    }

    /**
     * Only staff/admin can issue borrowings.
     */
    public function issue(User $user, Borrowing $borrowing): bool
    {
        return in_array($user->role, ['staff', 'admin']) && $borrowing->status === 'approved';
    }

    /**
     * Only staff/admin can process returns.
     */
    public function return(User $user, Borrowing $borrowing): bool
    {
        return in_array($user->role, ['staff', 'admin']) && $borrowing->status === 'issued';
    }

    /**
     * Owner can cancel their own pending borrowing.
     */
    public function cancel(User $user, Borrowing $borrowing): bool
    {
        return $user->id === $borrowing->user_id && $borrowing->status === 'pending';
    }
}
