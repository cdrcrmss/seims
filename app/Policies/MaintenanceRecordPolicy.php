<?php

namespace App\Policies;

use App\Models\MaintenanceRecord;
use App\Models\User;

class MaintenanceRecordPolicy
{
    /**
     * Only staff/admin can view maintenance records.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['staff', 'admin']);
    }

    /**
     * Only staff/admin can view a maintenance record.
     */
    public function view(User $user, MaintenanceRecord $record): bool
    {
        return in_array($user->role, ['staff', 'admin']);
    }

    /**
     * Only staff/admin can create maintenance records.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['staff', 'admin']);
    }

    /**
     * Only staff/admin can update maintenance records.
     */
    public function update(User $user, MaintenanceRecord $record): bool
    {
        return in_array($user->role, ['staff', 'admin']);
    }

    /**
     * Only admin can delete maintenance records.
     */
    public function delete(User $user, MaintenanceRecord $record): bool
    {
        return $user->role === 'admin';
    }
}
