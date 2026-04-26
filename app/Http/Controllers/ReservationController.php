<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckAvailabilityRequest;
use App\Http\Requests\StoreReservationRequest;
use App\Models\Reservation;
use App\Models\Room;
use App\Services\ReservationConflictService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    /**
     * Display a listing of reservations
     */
    public function index()
    {
        $user = Auth::user();
        
        $reservations = Reservation::with(['user', 'item', 'room'])
            ->where(function($query) use ($user) {
                $query->where('user_id', $user->id);
                
                if (in_array($user->role, ['staff', 'admin'])) {
                    $query->orWhereIn('status', ['pending', 'approved', 'checked_in']);
                }
            })
            ->orderBy('start_datetime', 'desc')
            ->paginate(15);

        return view('reservations.index', compact('reservations'));
    }

    /**
     * Show the form for creating a new reservation
     */
    public function create()
    {
        $this->authorize('create', Reservation::class);

        $user = Auth::user();
        $rooms = Room::where('status', 'available')->get();

        $activeReservationCount = Reservation::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved'])
            ->count();

        return view('reservations.create', compact('rooms', 'activeReservationCount'));
    }

    /**
     * Store a newly created reservation
     */
    public function store(StoreReservationRequest $request)
    {
        $reservation = new Reservation([
            'reservation_type' => 'room',
            'start_datetime' => $request->input('start_datetime'),
            'end_datetime' => $request->input('end_datetime'),
            'purpose' => $request->input('purpose'),
            'room_id' => $request->input('room_id'),
        ]);
        $reservation->user_id = Auth::id();
        $reservation->status = 'pending';

        if (ReservationConflictService::hasConflict($reservation)) {
            $reservation->conflict_detected = true;
            return back()->withErrors([
                'conflict' => 'A scheduling conflict was detected. The selected resource is already reserved for this time period.'
            ])->withInput();
        }

        $reservation->save();

        return redirect()->route('reservations.index')
            ->with('success', 'Reservation request submitted successfully!');
    }

    /**
     * Approve a reservation (Staff/Admin)
     */
    public function approve(Reservation $reservation)
    {
        $this->authorize('update', $reservation);

        if (ReservationConflictService::hasConflict($reservation)) {
            return back()->withErrors([
                'conflict' => 'Cannot approve: A scheduling conflict exists.'
            ]);
        }

        $reservation->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Reservation approved successfully!');
    }

    /**
     * Cancel a reservation
     */
    public function cancel(Request $request, Reservation $reservation)
    {
        $this->authorize('delete', $reservation);

        if (in_array($reservation->status, ['cancelled', 'completed', 'no_show', 'rejected'])) {
            return back()->withErrors(['error' => 'This reservation cannot be cancelled.']);
        }

        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $reservation->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $request->input('reason'),
        ]);

        return back()->with('success', 'Reservation cancelled successfully.');
    }

    /**
     * Reject a reservation (Staff/Admin)
     */
    public function reject(Request $request, Reservation $reservation)
    {
        $this->authorize('update', $reservation);

        if ($reservation->status !== 'pending') {
            return back()->withErrors(['error' => 'Only pending reservations can be rejected.']);
        }

        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $reservation->update([
            'status' => 'rejected',
            'cancelled_at' => now(),
            'cancellation_reason' => $request->input('reason', 'Rejected by staff'),
        ]);

        return back()->with('success', 'Reservation rejected.');
    }

    /**
     * Mark a reservation as no-show (Staff/Admin)
     */
    public function markNoShow(Reservation $reservation)
    {
        $this->authorize('update', $reservation);

        if ($reservation->status !== 'approved') {
            return back()->withErrors(['error' => 'Only approved reservations can be marked as no-show.']);
        }

        $reservation->update([
            'status' => 'no_show',
        ]);

        return back()->with('success', 'Reservation marked as no-show.');
    }

    /**
     * Mark a reservation as completed (Staff/Admin)
     */
    public function complete(Reservation $reservation)
    {
        $this->authorize('update', $reservation);

        if (!in_array($reservation->status, ['approved', 'checked_in'])) {
            return back()->withErrors(['error' => 'Only approved or checked-in reservations can be completed.']);
        }

        $reservation->update([
            'status' => 'completed',
        ]);

        return back()->with('success', 'Reservation marked as completed.');
    }

    /**
     * Check availability for a time period (API endpoint)
     */
    public function checkAvailability(CheckAvailabilityRequest $request)
    {
        $conflicts = ReservationConflictService::getConflicts(
            $request->item_id,
            $request->room_id,
            $request->start_datetime,
            $request->end_datetime
        );

        return response()->json([
            'available' => empty($conflicts),
            'conflicts' => $conflicts,
        ]);
    }
}
