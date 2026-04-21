<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReservationRequest;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\Item;
use App\Models\Notification;
use App\Models\User;
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
                
                // Staff/admin see all reservations
                if (in_array($user->role, ['staff', 'admin'])) {
                    $query->orWhereIn('status', ['approved', 'checked_in', 'pending']);
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
        $user = Auth::user();

        // Check no-show rate limit
        $reserveCheck = self::canUserReserve($user->id);
        if (!$reserveCheck['can_reserve']) {
            return redirect()->route('reservations.index')
                ->withErrors(['blocked' => $reserveCheck['message']]);
        }

        $rooms = Room::where('status', 'available')->get();

        $activeReservationCount = Reservation::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved', 'checked_in'])
            ->count();

        $noShowCount = $reserveCheck['no_show_count'];

        return view('reservations.create', compact('rooms', 'activeReservationCount', 'noShowCount'));
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

        // Conflict Detective: Check for scheduling conflicts
        if ($reservation->hasConflict()) {
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

        // Prevent cancelling reservations in terminal states
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

    /**
     * Mark a reservation as completed (after end time passes or manually by staff).
     */
    public function complete(Reservation $reservation)
    {
        if (!in_array($reservation->status, ['checked_in', 'approved'])) {
            return back()->withErrors(['error' => 'Only checked-in or approved reservations can be completed.']);
        }

        $reservation->update(['status' => 'completed']);

        return back()->with('success', 'Reservation marked as completed.');
    }

    /**
     * Mark a reservation as no-show (check-in window expired).
     */
    public function markNoShow(Reservation $reservation)
    {
        if ($reservation->status !== 'approved') {
            return back()->withErrors(['error' => 'Only approved reservations can be marked as no-show.']);
        }

        $reservation->update(['status' => 'no_show']);

        // Track user no-show count
        $userNoShowCount = Reservation::where('user_id', $reservation->user_id)
            ->where('status', 'no_show')
            ->count();

        // Notify the user
        Notification::create([
            'user_id' => $reservation->user_id,
            'type' => 'warning',
            'title' => 'Reservation No-Show',
            'message' => 'You were marked as no-show for your reservation of ' . ($reservation->room?->name ?? 'a room') . '. No-show count: ' . $userNoShowCount . '/3. Repeated no-shows may result in booking restrictions.',
            'action_url' => route('reservations.index'),
            'priority' => $userNoShowCount >= 2 ? 'high' : 'normal',
        ]);

        return back()->with('success', 'Reservation marked as no-show. User notified (no-show count: ' . $userNoShowCount . ').');
    }

    /**
     * Suggest alternative rooms/times when a conflict exists.
     */
    public function suggestAlternatives(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after:start_datetime',
        ]);

        $requestedStart = $request->start_datetime;
        $requestedEnd = $request->end_datetime;
        $duration = strtotime($requestedEnd) - strtotime($requestedStart);

        $alternatives = [];

        // 1. Find other available rooms at the same time
        $allRooms = Room::where('status', 'available')
            ->where('id', '!=', $request->room_id)
            ->get();

        foreach ($allRooms as $room) {
            $conflict = Reservation::whereIn('status', ['pending', 'approved', 'checked_in'])
                ->where('room_id', $room->id)
                ->where(function ($q) use ($requestedStart, $requestedEnd) {
                    $q->whereBetween('start_datetime', [$requestedStart, $requestedEnd])
                        ->orWhereBetween('end_datetime', [$requestedStart, $requestedEnd])
                        ->orWhere(function ($q2) use ($requestedStart, $requestedEnd) {
                            $q2->where('start_datetime', '<=', $requestedStart)
                                ->where('end_datetime', '>=', $requestedEnd);
                        });
                })->exists();

            if (!$conflict) {
                $alternatives[] = [
                    'type' => 'different_room',
                    'room_id' => $room->id,
                    'room_name' => $room->name,
                    'building' => $room->building,
                    'capacity' => $room->capacity,
                    'start_datetime' => $requestedStart,
                    'end_datetime' => $requestedEnd,
                ];
            }

            if (count($alternatives) >= 3) break;
        }

        // 2. Find next available slot for the same room (within next 3 days)
        $checkStart = now()->max(\Carbon\Carbon::parse($requestedStart));
        $checkEnd = $checkStart->copy()->addDays(3);

        $existingReservations = Reservation::where('room_id', $request->room_id)
            ->whereIn('status', ['pending', 'approved', 'checked_in'])
            ->whereBetween('start_datetime', [$checkStart, $checkEnd])
            ->orderBy('end_datetime')
            ->get();

        foreach ($existingReservations as $existing) {
            $slotStart = \Carbon\Carbon::parse($existing->end_datetime);
            $slotEnd = $slotStart->copy()->addSeconds($duration);

            // Check this slot doesn't conflict
            $slotConflict = Reservation::where('room_id', $request->room_id)
                ->whereIn('status', ['pending', 'approved', 'checked_in'])
                ->where(function ($q) use ($slotStart, $slotEnd) {
                    $q->whereBetween('start_datetime', [$slotStart, $slotEnd])
                        ->orWhereBetween('end_datetime', [$slotStart, $slotEnd])
                        ->orWhere(function ($q2) use ($slotStart, $slotEnd) {
                            $q2->where('start_datetime', '<=', $slotStart)
                                ->where('end_datetime', '>=', $slotEnd);
                        });
                })->exists();

            if (!$slotConflict && $slotStart->isAfter(now())) {
                $alternatives[] = [
                    'type' => 'different_time',
                    'room_id' => (int) $request->room_id,
                    'room_name' => Room::find($request->room_id)->name,
                    'start_datetime' => $slotStart->toDateTimeString(),
                    'end_datetime' => $slotEnd->toDateTimeString(),
                ];
                break;
            }
        }

        return response()->json(['alternatives' => $alternatives]);
    }

    /**
     * Check user no-show rate and determine if they can make reservations.
     */
    public static function canUserReserve(int $userId): array
    {
        $noShowCount = Reservation::where('user_id', $userId)
            ->where('status', 'no_show')
            ->where('updated_at', '>=', now()->subDays(30))
            ->count();

        $blocked = $noShowCount >= 3;

        return [
            'can_reserve' => !$blocked,
            'no_show_count' => $noShowCount,
            'message' => $blocked ? 'You have been temporarily blocked from making reservations due to ' . $noShowCount . ' no-shows in the past 30 days.' : null,
        ];
    }
}
