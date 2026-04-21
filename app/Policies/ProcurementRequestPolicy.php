<?php

namespace App\Policies;

use App\Models\ProcurementRequest;
use App\Models\User;

class ProcurementRequestPolicy
{
    /**
     * Only staff/admin can view procurement requests.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['staff', 'admin']);
    }

    /**
     * Only staff/admin can view a procurement request.
     */
    public function view(User $user, ProcurementRequest $request): bool
    {
        return in_array($user->role, ['staff', 'admin']);
    }

    /**
     * Only staff/admin can create procurement requests.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['staff', 'admin']);
    }

    /**
     * Only admin can approve procurement requests.
     */
    public function approve(User $user, ProcurementRequest $request): bool
    {
        return $user->role === 'admin' && $request->status === 'pending';
    }

    /**
     * Only admin can reject procurement requests.
     */
    public function reject(User $user, ProcurementRequest $request): bool
    {
        return $user->role === 'admin' && $request->status === 'pending';
    }

    /**
     * Only admin can delete procurement requests.
     */
    public function delete(User $user, ProcurementRequest $request): bool
    {
        return $user->role === 'admin';
    }
}
