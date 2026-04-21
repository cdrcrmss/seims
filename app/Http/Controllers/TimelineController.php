<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\User;
use App\Models\Borrowing;
use App\Models\MaintenanceRecord;
use App\Models\ScanEvent;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class TimelineController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'staff_or_admin']);
    }

    /**
     * Unified asset timeline — full history of an item.
     */
    public function assetTimeline(Item $item)
    {
        $events = collect();

        // Borrowing events
        $borrowings = Borrowing::with('user')
            ->where('item_id', $item->id)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        foreach ($borrowings as $b) {
            $events->push([
                'type' => 'borrowing',
                'icon' => 'clipboard',
                'color' => $this->borrowingColor($b->status),
                'title' => $this->borrowingTitle($b),
                'detail' => 'By ' . ($b->user?->name ?? 'Unknown') . ' · Qty: ' . $b->quantity,
                'timestamp' => $b->created_at,
                'metadata' => [
                    'status' => $b->status,
                    'condition' => $b->return_condition,
                ],
            ]);

            if ($b->returned_date) {
                $events->push([
                    'type' => 'return',
                    'icon' => 'arrow-left',
                    'color' => $this->conditionColor($b->return_condition),
                    'title' => 'Returned (' . ucfirst(str_replace('_', ' ', $b->return_condition ?? 'good')) . ')',
                    'detail' => 'By ' . ($b->user?->name ?? 'Unknown'),
                    'timestamp' => $b->returned_date,
                    'metadata' => ['notes' => $b->return_notes],
                ]);
            }
        }

        // Maintenance events
        $maintenanceRecords = MaintenanceRecord::with('technician')
            ->where('item_id', $item->id)
            ->orderBy('created_at', 'desc')
            ->limit(30)
            ->get();

        foreach ($maintenanceRecords as $m) {
            $events->push([
                'type' => 'maintenance',
                'icon' => 'wrench',
                'color' => $m->priority === 'critical' ? 'red' : ($m->priority === 'high' ? 'orange' : 'blue'),
                'title' => ucfirst($m->maintenance_type) . ' maintenance (' . ($m->sla_status ?? $m->status) . ')',
                'detail' => ($m->technician?->name ?? 'Unassigned') . ($m->trigger_source ? ' · Source: ' . str_replace('_', ' ', $m->trigger_source) : ''),
                'timestamp' => $m->created_at,
                'metadata' => [
                    'priority' => $m->priority,
                    'fault_type' => $m->fault_type,
                    'cost' => $m->cost,
                ],
            ]);
        }

        // Scan events for this item
        $scans = ScanEvent::with('user')
            ->where('target_type', 'item')
            ->where('target_id', $item->id)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        foreach ($scans as $s) {
            $events->push([
                'type' => 'scan',
                'icon' => 'qr-code',
                'color' => $s->outcome === 'success' ? 'green' : ($s->outcome === 'warning' ? 'yellow' : 'red'),
                'title' => ucfirst(str_replace('_', ' ', $s->action_type)),
                'detail' => ($s->user?->name ?? 'System') . ' · ' . $s->outcome_message,
                'timestamp' => $s->created_at,
                'metadata' => ['outcome' => $s->outcome],
            ]);
        }

        // Sort all events by timestamp descending
        $events = $events->sortByDesc('timestamp')->values();

        // Reliability KPIs
        $kpis = [
            'total_borrows' => $borrowings->count(),
            'repair_count' => $maintenanceRecords->where('trigger_source', 'return_inspection')->count(),
            'mttr_hours' => MaintenanceRecord::mttr($item->id),
            'repeat_failures_90d' => MaintenanceRecord::repeatFailureRate($item->id),
            'current_wear' => $item->wear_level,
        ];

        return view('timelines.asset', compact('item', 'events', 'kpis'));
    }

    /**
     * Unified user timeline — full activity history of a user.
     */
    public function userTimeline(User $user)
    {
        $events = collect();

        // Borrowing history
        $borrowings = Borrowing::with('item')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        foreach ($borrowings as $b) {
            $events->push([
                'type' => 'borrowing',
                'icon' => 'clipboard',
                'color' => $this->borrowingColor($b->status),
                'title' => $this->borrowingTitle($b) . ' — ' . ($b->item?->name ?? 'Unknown'),
                'detail' => 'Qty: ' . $b->quantity . ($b->return_condition ? ' · Returned: ' . ucfirst(str_replace('_', ' ', $b->return_condition)) : ''),
                'timestamp' => $b->created_at,
                'metadata' => ['status' => $b->status],
            ]);
        }

        // Reservation history
        $reservations = Reservation::with('room')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(30)
            ->get();

        foreach ($reservations as $r) {
            $events->push([
                'type' => 'reservation',
                'icon' => 'calendar',
                'color' => $r->status === 'no_show' ? 'orange' : ($r->status === 'checked_in' ? 'blue' : 'green'),
                'title' => 'Reservation: ' . ($r->room?->name ?? 'Room') . ' (' . ucfirst(str_replace('_', ' ', $r->status)) . ')',
                'detail' => $r->start_datetime?->format('M d, g:ia') . ' - ' . $r->end_datetime?->format('g:ia'),
                'timestamp' => $r->created_at,
                'metadata' => ['status' => $r->status, 'purpose' => $r->purpose],
            ]);
        }

        // Scan events by this user
        $scans = ScanEvent::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        foreach ($scans as $s) {
            $events->push([
                'type' => 'scan',
                'icon' => 'qr-code',
                'color' => $s->outcome === 'success' ? 'green' : ($s->outcome === 'warning' ? 'yellow' : 'red'),
                'title' => ucfirst(str_replace('_', ' ', $s->action_type)),
                'detail' => $s->outcome_message ?? $s->outcome,
                'timestamp' => $s->created_at,
                'metadata' => ['outcome' => $s->outcome],
            ]);
        }

        $events = $events->sortByDesc('timestamp')->values();

        // User KPIs
        $kpis = [
            'total_borrows' => $borrowings->count(),
            'active_borrows' => $borrowings->whereIn('status', ['approved', 'issued'])->count(),
            'overdue_count' => $borrowings->filter(fn($b) => $b->status === 'issued' && $b->expected_return_date && $b->expected_return_date < now())->count(),
            'no_show_count' => $reservations->where('status', 'no_show')->count(),
            'total_reservations' => $reservations->count(),
        ];

        return view('timelines.user', compact('user', 'events', 'kpis'));
    }

    private function borrowingColor(string $status): string
    {
        return match ($status) {
            'pending' => 'yellow',
            'approved' => 'blue',
            'issued' => 'green',
            'returned' => 'gray',
            'rejected' => 'red',
            default => 'gray',
        };
    }

    private function borrowingTitle(Borrowing $b): string
    {
        return match ($b->status) {
            'pending' => 'Borrow requested',
            'approved' => 'Borrow approved',
            'issued' => 'Item issued',
            'returned' => 'Item returned',
            'rejected' => 'Request rejected',
            default => 'Borrowing ' . $b->status,
        };
    }

    private function conditionColor(?string $condition): string
    {
        return match ($condition) {
            'good' => 'green',
            'fair' => 'yellow',
            'needs_repair' => 'orange',
            'damaged' => 'red',
            default => 'gray',
        };
    }
}
