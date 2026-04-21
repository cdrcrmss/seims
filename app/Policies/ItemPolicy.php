<?php

namespace App\Policies;

use App\Models\Item;
use App\Models\User;

class ItemPolicy
{
    /**
     * Anyone can view item listings.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Anyone can view item details.
     */
    public function view(User $user, Item $item): bool
    {
        return true;
    }

    /**
     * Only staff/admin can create items.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['staff', 'admin']);
    }

    /**
     * Only staff/admin can update items.
     */
    public function update(User $user, Item $item): bool
    {
        return in_array($user->role, ['staff', 'admin']);
    }

    /**
     * Only admin can delete items.
     */
    public function delete(User $user, Item $item): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Only staff/admin can generate QR codes.
     */
    public function generateQr(User $user, Item $item): bool
    {
        return in_array($user->role, ['staff', 'admin']);
    }
}
