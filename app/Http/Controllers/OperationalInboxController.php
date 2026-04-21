<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\MaintenanceRecord;
use App\Models\Reservation;
use App\Models\ScanEvent;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OperationalInboxController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'staff_or_admin']);
    }

    /**
     * Staff operational inbox — today's prioritized actions.
     */
    public function index()
    {
        $today = today();
        $now = now();

        // === URGENT ACTIONS ===

        // Overdue borrowings (need follow-up)
        $overdueBorrowings = Borrowing::with(['user', 'item'])
            ->where('status', 'issued')
            ->whereNotNull('expected_return_date')
            ->where('expected_return_date', '<', $now)
            ->orderBy('expected_return_date', 'asc')
            ->get()
            ->map(function ($b) use ($now) {
                $b->overdue_days = (int) $now->diffInDays($b->expected_return_date);
                return $b;
            });

        // Open maintenance tickets (SLA: open, critical/high priority)
        $urgentMaintenance = MaintenanceRecord::with('item')
            ->openTickets()
            ->whereIn('priority', ['critical', 'high'])
            ->orderByRaw("CASE priority WHEN 'critical' THEN 1 WHEN 'high' THEN 2 ELSE 3 END")
            ->limit(10)
            ->get();

        // === TODAY'S SCHEDULE ===

        // Borrowings pending issuance (approved, awaiting pickup)
        $pendingIssuance = Borrowing::with(['user', 'item'])
            ->where('status', 'approved')
            ->orderBy('approved_date', 'asc')
            ->limit(10)
            ->get();

        // Reservations today (need check-in monitoring)
        $todayReservations = Reservation::with(['user', 'room'])
            ->whereDate('start_datetime', $today)
            ->whereIn('status', ['approved', 'checked_in'])
            ->orderBy('start_datetime', 'asc')
            ->get();

        // Reservations past their end time (auto-complete candidates)
        $completableReservations = Reservation::with(['user', 'room'])
            ->where('status', 'checked_in')
            ->where('end_datetime', '<', $now)
            ->get();

        // Reservations past check-in window (no-show candidates, 15 min after start)
        $noShowCandidates = Reservation::with(['user', 'room'])
            ->where('status', 'approved')
            ->where('start_datetime', '<', $now->copy()->subMinutes(15))
            ->whereDate('start_datetime', $today)
            ->get();

        // === RECENT ACTIVITY ===

        // Today's scan events
        $todayScans = ScanEvent::with('user')
            ->whereDate('created_at', $today)
            ->orderBy('created_at', 'desc')
            ->limit(15)
            ->get();

        // Items with critical wear (>= 80%)
        $criticalItems = Item::where('wear_level', '>=', 80)
            ->where('status', '!=', 'retired')
            ->orderBy('wear_level', 'desc')
            ->limit(5)
            ->get();

        // === STATS ===
        $stats = [
            'overdue_count' => $overdueBorrowings->count(),
            'pending_issuance' => $pendingIssuance->count(),
            'open_tickets' => MaintenanceRecord::openTickets()->count(),
            'today_reservations' => $todayReservations->count(),
            'today_scans' => ScanEvent::whereDate('created_at', $today)->count(),
            'items_critical' => $criticalItems->count(),
        ];

        return view('staff.operational-inbox', compact(
            'overdueBorrowings',
            'urgentMaintenance',
            'pendingIssuance',
            'todayReservations',
            'completableReservations',
            'noShowCandidates',
            'todayScans',
            'criticalItems',
            'stats'
        ));
    }
}
