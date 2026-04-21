<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\Item;
use App\Models\MaintenanceRecord;
use App\Models\Reservation;
use App\Models\ScanEvent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'staff_or_admin']);
    }

    /**
     * Report hub — shows available report packs by role.
     */
    public function index()
    {
        return view('reports.index');
    }

    // ─── INVENTORY REPORT ────────────────────────────────────────────

    /**
     * Inventory snapshot — stock levels, wear, status distribution.
     */
    public function inventory(Request $request)
    {
        $items = Item::query()
            ->when($request->category, fn($q, $c) => $q->where('category', $c))
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->orderBy('name')
            ->get();

        $statusCounts = Item::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $categoryCounts = Item::selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();

        $wearBuckets = [
            'good' => Item::where('wear_level', '<', 30)->count(),
            'moderate' => Item::whereBetween('wear_level', [30, 59])->count(),
            'worn' => Item::whereBetween('wear_level', [60, 79])->count(),
            'critical' => Item::where('wear_level', '>=', 80)->count(),
        ];

        $totalValue = Item::whereNotNull('unit_price')
            ->selectRaw('SUM(total_stock * unit_price) as total')
            ->value('total') ?? 0;

        $categories = Item::distinct()->pluck('category')->sort()->values();

        return view('reports.inventory', compact(
            'items', 'statusCounts', 'categoryCounts', 'wearBuckets', 'totalValue', 'categories'
        ));
    }

    // ─── BORROWING REPORT ────────────────────────────────────────────

    /**
     * Borrowing activity — trends, top borrowers, overdue rate, turnaround.
     */
    public function borrowing(Request $request)
    {
        $days = (int) $request->input('days', 30);
        $since = now()->subDays($days);

        // Summary stats
        $totalBorrowings = Borrowing::where('created_at', '>=', $since)->count();
        $returnedCount = Borrowing::where('status', 'returned')->where('returned_date', '>=', $since)->count();
        $overdueCount = Borrowing::where('status', 'issued')
            ->whereNotNull('expected_return_date')
            ->where('expected_return_date', '<', now())
            ->count();
        $activeCount = Borrowing::whereIn('status', ['approved', 'issued'])->count();

        // Overdue rate (returned late / total returned)
        $returnedLateCount = Borrowing::where('status', 'returned')
            ->where('returned_date', '>=', $since)
            ->whereNotNull('expected_return_date')
            ->whereColumn('returned_date', '>', 'expected_return_date')
            ->count();
        $overdueRate = $returnedCount > 0 ? round(($returnedLateCount / $returnedCount) * 100, 1) : 0;

        // Avg turnaround
        $driver = DB::connection()->getDriverName();
        if ($driver === 'sqlite') {
            $diffExpr = "julianday(returned_date) - julianday(issued_date)";
        } else {
            $diffExpr = "DATEDIFF(returned_date, issued_date)";
        }
        $avgTurnaround = Borrowing::where('status', 'returned')
            ->where('returned_date', '>=', $since)
            ->whereNotNull('issued_date')
            ->selectRaw("ROUND(AVG({$diffExpr}), 1) as avg_days")
            ->value('avg_days');

        // Top borrowers
        $topBorrowers = Borrowing::select('user_id', DB::raw('COUNT(*) as borrow_count'))
            ->where('created_at', '>=', $since)
            ->groupBy('user_id')
            ->orderByDesc('borrow_count')
            ->limit(10)
            ->with('user')
            ->get();

        // Top items
        $topItems = Borrowing::select('item_id', DB::raw('COUNT(*) as borrow_count'))
            ->where('created_at', '>=', $since)
            ->groupBy('item_id')
            ->orderByDesc('borrow_count')
            ->limit(10)
            ->with('item')
            ->get();

        // Return condition breakdown
        $conditionBreakdown = Borrowing::where('status', 'returned')
            ->where('returned_date', '>=', $since)
            ->whereNotNull('return_condition')
            ->selectRaw('return_condition, COUNT(*) as count')
            ->groupBy('return_condition')
            ->pluck('count', 'return_condition')
            ->toArray();

        return view('reports.borrowing', compact(
            'days', 'totalBorrowings', 'returnedCount', 'overdueCount', 'activeCount',
            'overdueRate', 'avgTurnaround', 'returnedLateCount',
            'topBorrowers', 'topItems', 'conditionBreakdown'
        ));
    }

    // ─── MAINTENANCE REPORT ──────────────────────────────────────────

    /**
     * Maintenance report — SLA compliance, cost, ticket throughput.
     */
    public function maintenance(Request $request)
    {
        $days = (int) $request->input('days', 90);
        $since = now()->subDays($days);

        // Ticket counts by SLA status
        $slaCounts = MaintenanceRecord::selectRaw('sla_status, COUNT(*) as count')
            ->groupBy('sla_status')
            ->pluck('count', 'sla_status')
            ->toArray();

        // Tickets by priority
        $priorityCounts = MaintenanceRecord::selectRaw('priority, COUNT(*) as count')
            ->groupBy('priority')
            ->pluck('count', 'priority')
            ->toArray();

        // Avg resolution time (started_at → completed_date)
        $mttr = MaintenanceRecord::mttr();

        // Cost summary
        $totalCost = MaintenanceRecord::where('status', 'completed')
            ->where('completed_date', '>=', $since)
            ->sum('cost');
        $avgCost = MaintenanceRecord::where('status', 'completed')
            ->where('completed_date', '>=', $since)
            ->avg('cost');

        // Tickets by trigger source
        $triggerCounts = MaintenanceRecord::selectRaw('trigger_source, COUNT(*) as count')
            ->whereNotNull('trigger_source')
            ->groupBy('trigger_source')
            ->pluck('count', 'trigger_source')
            ->toArray();

        // Tickets by type
        $typeCounts = MaintenanceRecord::selectRaw('maintenance_type, COUNT(*) as count')
            ->groupBy('maintenance_type')
            ->pluck('count', 'maintenance_type')
            ->toArray();

        // Items with most tickets
        $frequentItems = MaintenanceRecord::select('item_id', DB::raw('COUNT(*) as ticket_count'))
            ->groupBy('item_id')
            ->orderByDesc('ticket_count')
            ->limit(10)
            ->with('item')
            ->get();

        return view('reports.maintenance', compact(
            'days', 'slaCounts', 'priorityCounts', 'mttr', 'totalCost', 'avgCost',
            'triggerCounts', 'typeCounts', 'frequentItems'
        ));
    }

    // ─── ROOM/RESERVATION REPORT ─────────────────────────────────────

    /**
     * Room reservation report — utilization, no-shows, peak times.
     */
    public function reservations(Request $request)
    {
        $days = (int) $request->input('days', 30);
        $since = now()->subDays($days);

        $totalReservations = Reservation::where('created_at', '>=', $since)->count();
        $completedCount = Reservation::where('status', 'completed')->where('created_at', '>=', $since)->count();
        $noShowCount = Reservation::where('status', 'no_show')->where('updated_at', '>=', $since)->count();
        $cancelledCount = Reservation::where('status', 'cancelled')->where('created_at', '>=', $since)->count();

        $noShowRate = ($completedCount + $noShowCount) > 0
            ? round(($noShowCount / ($completedCount + $noShowCount)) * 100, 1)
            : 0;

        // Status breakdown
        $statusCounts = Reservation::where('created_at', '>=', $since)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Top rooms by reservation count
        $topRooms = Reservation::select('room_id', DB::raw('COUNT(*) as reservation_count'))
            ->where('created_at', '>=', $since)
            ->whereNotNull('room_id')
            ->groupBy('room_id')
            ->orderByDesc('reservation_count')
            ->limit(10)
            ->with('room')
            ->get();

        // Top no-show users
        $topNoShows = Reservation::select('user_id', DB::raw('COUNT(*) as no_show_count'))
            ->where('status', 'no_show')
            ->where('updated_at', '>=', $since)
            ->groupBy('user_id')
            ->orderByDesc('no_show_count')
            ->limit(10)
            ->with('user')
            ->get();

        return view('reports.reservations', compact(
            'days', 'totalReservations', 'completedCount', 'noShowCount', 'cancelledCount',
            'noShowRate', 'statusCounts', 'topRooms', 'topNoShows'
        ));
    }

    // ─── USER ACTIVITY REPORT ────────────────────────────────────────

    /**
     * User activity report — admin only, drill-down per user.
     */
    public function userActivity(Request $request)
    {
        $days = (int) $request->input('days', 30);
        $since = now()->subDays($days);

        // User summary stats
        $users = User::withCount([
                'borrowings as total_borrowings' => fn($q) => $q->where('created_at', '>=', $since),
                'borrowings as overdue_borrowings' => fn($q) => $q->where('status', 'issued')
                    ->whereNotNull('expected_return_date')
                    ->where('expected_return_date', '<', now()),
                'reservations as total_reservations' => fn($q) => $q->where('created_at', '>=', $since),
                'reservations as no_shows' => fn($q) => $q->where('status', 'no_show')
                    ->where('updated_at', '>=', $since),
            ])
            ->where('role', 'student')
            ->orderByDesc('total_borrowings')
            ->paginate(25);

        return view('reports.user-activity', compact('days', 'users'));
    }

    // ─── EXPORT ──────────────────────────────────────────────────────

    /**
     * Export any report as CSV.
     */
    public function export(Request $request)
    {
        $type = $request->input('type', 'inventory');
        $days = (int) $request->input('days', 30);
        $since = now()->subDays($days);

        $rows = [];
        $filename = "seims_{$type}_report_" . now()->format('Y-m-d');

        switch ($type) {
            case 'inventory':
                $rows[] = ['Name', 'Category', 'Asset Code', 'Status', 'Total Stock', 'Available', 'Wear Level', 'Unit Price'];
                Item::orderBy('name')->chunk(200, function ($items) use (&$rows) {
                    foreach ($items as $item) {
                        $rows[] = [
                            $item->name, $item->category, $item->asset_code,
                            $item->status, $item->total_stock, $item->available_stock,
                            $item->wear_level . '%', $item->unit_price ?? 'N/A',
                        ];
                    }
                });
                break;

            case 'borrowing':
                $rows[] = ['Date', 'Item', 'Borrower', 'Status', 'Issued', 'Expected Return', 'Returned', 'Condition'];
                Borrowing::with(['user', 'item'])->where('created_at', '>=', $since)
                    ->orderByDesc('created_at')->chunk(200, function ($borrowings) use (&$rows) {
                        foreach ($borrowings as $b) {
                            $rows[] = [
                                $b->created_at->format('Y-m-d'), $b->item?->name, $b->user?->name,
                                $b->status, $b->issued_date?->format('Y-m-d') ?? '',
                                $b->expected_return_date?->format('Y-m-d') ?? '',
                                $b->returned_date?->format('Y-m-d') ?? '',
                                $b->return_condition ?? '',
                            ];
                        }
                    });
                break;

            case 'maintenance':
                $rows[] = ['Item', 'Type', 'Priority', 'SLA Status', 'Scheduled', 'Completed', 'Cost', 'Trigger'];
                MaintenanceRecord::with('item')->orderByDesc('scheduled_date')
                    ->chunk(200, function ($records) use (&$rows) {
                        foreach ($records as $r) {
                            $rows[] = [
                                $r->item?->name, $r->maintenance_type, $r->priority ?? 'normal',
                                $r->sla_status ?? 'open', $r->scheduled_date?->format('Y-m-d') ?? '',
                                $r->completed_date?->format('Y-m-d') ?? '',
                                $r->cost ?? '0', $r->trigger_source ?? 'manual',
                            ];
                        }
                    });
                break;

            case 'reservations':
                $rows[] = ['Date', 'Room', 'User', 'Status', 'Start', 'End', 'Purpose'];
                Reservation::with(['user', 'room'])->where('created_at', '>=', $since)
                    ->orderByDesc('created_at')->chunk(200, function ($reservations) use (&$rows) {
                        foreach ($reservations as $r) {
                            $rows[] = [
                                $r->created_at->format('Y-m-d'), $r->room?->name, $r->user?->name,
                                $r->status, $r->start_datetime->format('Y-m-d H:i'),
                                $r->end_datetime->format('Y-m-d H:i'), $r->purpose ?? '',
                            ];
                        }
                    });
                break;

            default:
                return back()->withErrors(['error' => 'Unknown report type.']);
        }

        $callback = function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
        ]);
    }
}
