<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReservationRequest;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\Item;
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
                
                // Staff/admin can see others' active reservations
                if (in_array($user->role, ['staff', 'admin'])) {
                    $query->orWhereIn('status', ['pending', 'approved', 'checked_in']);
                }
            })
            ->orderBy('start_datetime', 'desc')
            ->paginate(15);

        return view('reservations.index', compact('reservations'));
    }

    /**
     * Display calendar view for all users (students can view but not manage)
     */
    public function calendar()
    {
        $user = Auth::user();
        
        $reservations = Reservation::with(['user', 'room'])
            ->whereIn('status', ['approved', 'checked_in'])
            ->orderBy('start_datetime', 'asc')
            ->get();

        return view('reservations.calendar', compact('reservations'));
    }

    /**
     * Show the form for creating a new reservation
     */
    public function create()
    {
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
        $reservation->status = in_array(Auth::user()->role, ['staff', 'admin']) ? 'approved' : 'pending';

        // Conflict Detective: Check for scheduling conflicts
        if ($reservation->hasConflict()) {
            $reservation->conflict_detected = true;
            return back()->withErrors([
                'conflict' => 'A scheduling conflict was detected. The selected resource is already reserved for this time period.'
            ])->withInput();
        }

        $reservation->save();

        $message = in_array(Auth::user()->role, ['staff', 'admin'])
            ? 'Reservation confirmed successfully!'
            : 'Reservation request submitted successfully! Please wait for staff approval.';

        return redirect()->route('reservations.index')
            ->with('success', $message);
    }

    /**
     * Approve a reservation (Staff/Admin)
     */
    public function approve(Reservation $reservation)
    {
        $this->authorize('update', $reservation);

        // Recheck for conflicts before approval
        if ($reservation->hasConflict()) {
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

        // Prevent cancelling already completed/cancelled reservations
        if (in_array($reservation->status, ['cancelled', 'completed'])) {
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
     * Check availability for a time period (API endpoint)
     */
    public function checkAvailability(Request $request)
    {
        $request->validate([
            'item_id' => 'nullable|exists:items,id',
            'room_id' => 'nullable|exists:rooms,id',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after:start_datetime',
        ]);

        $available = true;
        $conflicts = [];

        if ($request->item_id) {
            $item = Item::find($request->item_id);

            // Query overlapping approved reservations for this item and time window
            $itemConflicts = Reservation::where('status', 'approved')
                ->where('item_id', $request->item_id)
                ->where(function ($q) use ($request) {
                    $q->whereBetween('start_datetime', [$request->start_datetime, $request->end_datetime])
                        ->orWhereBetween('end_datetime', [$request->start_datetime, $request->end_datetime])
                        ->orWhere(function ($q2) use ($request) {
                            $q2->where('start_datetime', '<=', $request->start_datetime)
                                ->where('end_datetime', '>=', $request->end_datetime);
                        });
                })
                ->get();

            if ($itemConflicts->isNotEmpty()) {
                $available = false;
                foreach ($itemConflicts as $conflict) {
                    $conflicts[] = [
                        'type' => 'item',
                        'reservation_id' => $conflict->id,
                        'start_datetime' => $conflict->start_datetime->toDateTimeString(),
                        'end_datetime' => $conflict->end_datetime->toDateTimeString(),
                    ];
                }
            }
        }

        if ($request->room_id) {
            $room = Room::find($request->room_id);
            if (!$room->isAvailable($request->start_datetime, $request->end_datetime)) {
                $available = false;

                // Also fetch the specific room conflicts for the response
                $roomConflicts = Reservation::where('status', 'approved')
                    ->where('room_id', $request->room_id)
                    ->where(function ($q) use ($request) {
                        $q->whereBetween('start_datetime', [$request->start_datetime, $request->end_datetime])
                            ->orWhereBetween('end_datetime', [$request->start_datetime, $request->end_datetime])
                            ->orWhere(function ($q2) use ($request) {
                                $q2->where('start_datetime', '<=', $request->start_datetime)
                                    ->where('end_datetime', '>=', $request->end_datetime);
                            });
                    })
                    ->get();

                foreach ($roomConflicts as $conflict) {
                    $conflicts[] = [
                        'type' => 'room',
                        'reservation_id' => $conflict->id,
                        'start_datetime' => $conflict->start_datetime->toDateTimeString(),
                        'end_datetime' => $conflict->end_datetime->toDateTimeString(),
                    ];
                }
            }
        }

        return response()->json([
            'available' => $available,
            'conflicts' => $conflicts,
        ]);
    }
}
