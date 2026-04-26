<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Reservation;
use Illuminate\Http\Request;

class TrashController extends Controller
{
    /**
     * Display trashed records.
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'users');

        $trashedUsers = User::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->get();

        $trashedReservations = Reservation::onlyTrashed()
            ->with(['user', 'room'])
            ->orderBy('deleted_at', 'desc')
            ->get();

        return view('admin.trash.index', compact('trashedUsers', 'trashedReservations', 'tab'));
    }

    /**
     * Restore a soft-deleted user.
     */
    public function restoreUser(int $id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();

        return back()->with('success', $user->name . ' has been restored.');
    }

    /**
     * Permanently delete a user.
     */
    public function forceDeleteUser(int $id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $name = $user->name;
        $user->forceDelete();

        return back()->with('success', $name . ' has been permanently deleted.');
    }

    /**
     * Restore a soft-deleted reservation.
     */
    public function restoreReservation(int $id)
    {
        $reservation = Reservation::onlyTrashed()->findOrFail($id);
        $reservation->restore();

        return back()->with('success', 'Reservation #' . $reservation->id . ' has been restored.');
    }

    /**
     * Permanently delete a reservation.
     */
    public function forceDeleteReservation(int $id)
    {
        $reservation = Reservation::onlyTrashed()->findOrFail($id);
        $reservation->forceDelete();

        return back()->with('success', 'Reservation has been permanently deleted.');
    }
}
