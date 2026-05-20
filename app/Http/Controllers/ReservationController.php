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
        Reservation::markExpiredAsCompleted();

        $user = Auth::user();
        
        $reservations = Reservation::with(['user', 'item', 'room'])
            ->where(function($query) use ($user) {
                $query->where('user_id', $user->id);
                
                // Staff/admin can see others' active reservations
                if (in_array($user->role, ['staff', 'admin'])) {
                    $query->orWhereIn('status', ['pending', 'ongoing', 'approved', 'checked_in']);
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
        Reservation::markExpiredAsCompleted();

        $reservations = Reservation::with(['user', 'room'])
            ->whereIn('status', ['ongoing', 'approved', 'checked_in'])
            ->orderBy('start_datetime', 'asc')
            ->get();

        return view('reservations.calendar', compact('reservations'));
    }

    /**
     * Show the form for creating a new reservation
     */
    public function create()
    {
        Reservation::markExpiredAsCompleted();

        $user = Auth::user();
        $rooms = Room::where('status', 'available')->get();
        $isStaffOrAdmin = in_array($user->role, ['staff', 'admin'], true);

        $activeReservationCount = $isStaffOrAdmin
            ? 0
            : Reservation::where('user_id', $user->id)
                ->whereIn('status', Reservation::STUDENT_ACTIVE_STATUSES)
                ->count();

        return view('reservations.create', compact('rooms', 'activeReservationCount', 'isStaffOrAdmin'));
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
        $user = Auth::user();
        $isStaffOrAdmin = in_array($user->role, ['staff', 'admin'], true);

        $reservation->user_id = $user->id;
        if ($isStaffOrAdmin) {
            $reservation->status = 'ongoing';
            $reservation->approved_by = $user->id;
            $reservation->approved_at = now();
        } else {
            $reservation->status = 'pending';
        }

        // Conflict Detective: Check for scheduling conflicts
        if ($reservation->hasConflict()) {
            $room = Room::find($request->room_id);
            $roomName = $room?->name ?? 'This room';

            return back()->withErrors([
                'conflict' => $roomName . ' is already reserved for the time you selected. Pick another room or change your schedule.',
            ])->withInput();
        }

        $reservation->save();

        $message = $isStaffOrAdmin
            ? 'Reservation confirmed — room is now ongoing for your schedule.'
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
            'status' => 'ongoing',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Reservation is now ongoing.');
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

            $itemConflicts = Reservation::whereIn('status', Reservation::BLOCKING_STATUSES)
                ->where('item_id', $request->item_id)
                ->where('start_datetime', '<', $request->end_datetime)
                ->where('end_datetime', '>', $request->start_datetime)
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

        if ($request->boolean('check_all_rooms')) {
            $rooms = Room::where('status', 'available')->orderBy('name')->get();
            $roomStatuses = [];

            foreach ($rooms as $room) {
                $overlaps = $room->overlappingReservations($request->start_datetime, $request->end_datetime)->get();
                $roomStatuses[$room->id] = [
                    'available' => $overlaps->isEmpty(),
                    'room_name' => $room->name,
                    'conflicts' => $overlaps->map(function ($r) {
                        $start = $r->start_datetime;
                        $end = $r->end_datetime;
                        $bookedLabel = $start->isSameDay($end)
                            ? $start->format('M j, Y')
                            : $start->format('M j') . ' – ' . $end->format('M j, Y');

                        return [
                            'start' => $start->format('M j, Y g:i A'),
                            'end' => $end->format('M j, Y g:i A'),
                            'booked_label' => $bookedLabel,
                            'status' => $r->status,
                        ];
                    })->values()->all(),
                ];
            }

            return response()->json([
                'rooms' => $roomStatuses,
                'available' => collect($roomStatuses)->contains(fn ($s) => $s['available']),
            ]);
        }

        if ($request->room_id) {
            $room = Room::find($request->room_id);
            $roomConflicts = $room->overlappingReservations($request->start_datetime, $request->end_datetime)->get();

            if ($roomConflicts->isNotEmpty()) {
                $available = false;

                foreach ($roomConflicts as $conflict) {
                    $start = $conflict->start_datetime;
                    $end = $conflict->end_datetime;
                    $conflicts[] = [
                        'type' => 'room',
                        'reservation_id' => $conflict->id,
                        'start_datetime' => $start->toDateTimeString(),
                        'end_datetime' => $end->toDateTimeString(),
                        'start_label' => $start->format('M j, Y g:i A'),
                        'end_label' => $end->format('M j, Y g:i A'),
                        'booked_label' => $start->isSameDay($end)
                            ? $start->format('M j, Y')
                            : $start->format('M j') . ' – ' . $end->format('M j, Y'),
                        'status' => $conflict->status,
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
