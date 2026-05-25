<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\ItemUnit;
use App\Models\MaintenanceRecord;
use App\Services\MaintenanceAutoScheduleService;
use App\Models\Item;
use App\Models\User;
use App\Models\Notification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaintenanceController extends Controller
{
    /**
     * Display a listing of maintenance records
     */
    public function index(Request $request)
    {
        $status = $request->get('status');

        MaintenanceAutoScheduleService::syncAllScheduledMaintenance();

        $query = MaintenanceRecord::with(['item', 'itemUnit', 'technician']);

        if ($status === 'upcoming') {
            $query->upcoming();
        } elseif ($status === 'overdue') {
            $query->overdue();
        }

        $maintenanceRecords = $query->orderBy('scheduled_date', 'desc')
            ->paginate(15)
            ->appends($request->query());

        $upcomingMaintenance = MaintenanceRecord::upcoming()->count();
        $overdueMaintenance = MaintenanceRecord::overdue()->count();
        $totalRecords = MaintenanceRecord::count();

        return view('maintenance.index', compact(
            'maintenanceRecords',
            'upcomingMaintenance',
            'overdueMaintenance',
            'totalRecords',
            'status'
        ));
    }

    /**
     * Show the form for creating a new maintenance record
     */
    public function create()
    {
        $items = Item::all();
        $technicians = User::where('role', 'staff')->orWhere('role', 'admin')->get();

        return view('maintenance.create', compact('items', 'technicians'));
    }

    /**
     * Store a newly created maintenance record
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'maintenance_type' => 'required|in:preventive,corrective,predictive,routine,emergency',
            'scheduled_date' => 'required|date',
            'performed_by' => 'nullable|exists:users,id',
            'condition_before' => 'nullable|string',
            'wear_level' => 'nullable|integer|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        // Create with only validated fields, set server-controlled defaults
        $maintenance = MaintenanceRecord::create([
            'item_id' => $validated['item_id'],
            'maintenance_type' => $validated['maintenance_type'],
            'scheduled_date' => $validated['scheduled_date'],
            'performed_by' => $validated['performed_by'] ?? null,
            'condition_before' => $validated['condition_before'] ?? null,
            'wear_level' => $validated['wear_level'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'status' => 'scheduled', // Server-controlled
            'predictive_alert_sent' => false, // Server-controlled
        ]);

        // Send notification to assigned technician
        if ($maintenance->performed_by) {
            Notification::createMaintenanceAlert(
                $maintenance->item,
                $maintenance,
                User::find($maintenance->performed_by)
            );
        }

        return redirect()->route('maintenance.index')
            ->with('success', 'Maintenance record created successfully!');
    }

    /**
     * Display a single maintenance record with equipment and unit context.
     */
    public function show(Request $request, MaintenanceRecord $maintenance)
    {
        $maintenance->load(['item', 'itemUnit']);

        $returnBorrowing = null;
        if ($maintenance->item_unit_id) {
            $returnBorrowing = Borrowing::query()
                ->where('item_unit_id', $maintenance->item_unit_id)
                ->whereIn('return_condition', ['damaged', 'needs_repair'])
                ->whereNotNull('returned_date')
                ->orderByDesc('returned_date')
                ->first();
        }

        $issuesFoundDisplay = $maintenance->issuesFoundDisplay($returnBorrowing);

        $backUrl = match ($request->query('from')) {
            'dashboard' => route('maintenance.dashboard'),
            'index' => route('maintenance.index'),
            default => url()->previous() && url()->previous() !== url()->current()
                ? url()->previous()
                : route('maintenance.dashboard'),
        };

        return view('maintenance.show', compact('maintenance', 'backUrl', 'returnBorrowing', 'issuesFoundDisplay'));
    }

    /**
     * Update maintenance record as completed
     */
    public function complete(Request $request, MaintenanceRecord $maintenance)
    {
        $request->validate([
            'completed_date' => 'required|date',
            'condition_after' => 'required|in:excellent,good,fair,poor',
            'issues_found' => 'nullable|string',
            'actions_taken' => 'required|string',
            'cost' => 'nullable|numeric|min:0',
        ]);

        $wearLevel = MaintenanceRecord::predictiveWearForCondition($request->condition_after);

        $maintenance->update([
            'status' => 'completed',
            'completed_date' => $request->completed_date,
            'condition_after' => $request->condition_after,
            'wear_level' => $wearLevel,
            'issues_found' => $request->issues_found,
            'actions_taken' => $request->actions_taken,
            'cost' => $request->cost,
            'next_maintenance_date' => MaintenanceRecord::predictNextMaintenance($maintenance->item),
        ]);

        // Update item wear level from predictive condition mapping
        $maintenance->item->update([
            'wear_level' => $wearLevel,
            'last_maintenance_date' => $request->completed_date,
            'next_maintenance_date' => $maintenance->next_maintenance_date,
        ]);

        if ($maintenance->item_unit_id) {
            $unit = ItemUnit::find($maintenance->item_unit_id);
            if ($unit) {
                app(MaintenanceAutoScheduleService::class)
                    ->restoreUnitAfterCompletedMaintenance($unit, $request->condition_after);
            }
        }

        return redirect()->back()
            ->with('success', 'Maintenance record completed successfully!');
    }

    /**
     * Show maintenance dashboard with analytics
     */
    public function dashboard()
    {
        $buckets = MaintenanceAutoScheduleService::scheduledMaintenanceDashboardBuckets();
        $criticalUnits = $buckets['critical_units'];
        $criticalUnitsOverdue = $buckets['critical_units_overdue'];
        $criticalUnitsUpcoming = $buckets['critical_units_upcoming'];
        $upcomingMaintenance = $buckets['upcoming'];
        $overdueMaintenance = $buckets['overdue'];
        $overdueCount = $buckets['overdue_count'];
        $scheduledDisplayCount = $buckets['display_count'];

        $recentlyCompleted = MaintenanceRecord::where('status', 'completed')
            ->orderBy('completed_date', 'desc')
            ->take(10)
            ->with('item')
            ->get();
        $itemIdsWithCriticalUnits = $criticalUnits->pluck('item_id')->unique();
        $criticalItems = Item::where('wear_level', '>=', 70)
            ->whereNotIn('id', $itemIdsWithCriticalUnits)
            ->get();
        $criticalCount = $criticalUnits->count() + $criticalItems->count();
        $maintenanceCosts = MaintenanceRecord::where('status', 'completed')
            ->whereYear('completed_date', now()->year)
            ->sum('cost');

        return view('maintenance.dashboard', compact(
            'upcomingMaintenance',
            'overdueMaintenance',
            'overdueCount',
            'recentlyCompleted',
            'criticalUnits',
            'criticalUnitsOverdue',
            'criticalUnitsUpcoming',
            'criticalItems',
            'criticalCount',
            'maintenanceCosts',
            'scheduledDisplayCount'
        ));
    }

    /**
     * Export filtered maintenance records as PDF.
     */
    public function exportReport(Request $request)
    {
        $request->validate([
            'date_from'        => 'nullable|date',
            'date_to'          => 'nullable|date|after_or_equal:date_from',
            'status'           => 'nullable|string',
            'maintenance_type' => 'nullable|string',
            'format'           => 'nullable|in:pdf',
        ]);

        $query = MaintenanceRecord::with('item', 'technician');

        if ($request->filled('date_from')) {
            $query->whereDate('scheduled_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('scheduled_date', '<=', $request->date_to);
        }
        if ($request->filled('status') && $request->status !== 'all') {
            if ($request->status === 'overdue') {
                $query->where('status', 'scheduled')
                    ->whereDate('scheduled_date', '<', now());
            } else {
                $query->where('status', $request->status);
            }
        }
        if ($request->filled('maintenance_type') && $request->maintenance_type !== 'all') {
            $query->where('maintenance_type', $request->maintenance_type);
        }

        $records = $query->orderBy('scheduled_date', 'desc')->get();

        $dateLabel = ($request->date_from ?? 'all') . '_to_' . ($request->date_to ?? 'all');
        $filename  = 'seims_maintenance_' . $dateLabel;

        $pdf = Pdf::loadView('admin.maintenance-report-pdf', [
            'records'    => $records,
            'date_from'  => $request->date_from,
            'date_to'    => $request->date_to,
            'status'     => $request->status,
            'maint_type' => $request->maintenance_type,
            'generated'  => now()->format('F d, Y h:i A'),
        ]);
        $pdf->setPaper('a4', 'landscape');

        return $pdf->download($filename . '.pdf');
    }

    /**
     * Mark a critical unit as disposed (removes from circulation, cancels scheduled work).
     */
    public function disposeUnit(Request $request, ItemUnit $unit)
    {
        if ($unit->status === 'borrowed') {
            return $this->disposeUnitResponse(
                $request,
                'Return this unit before marking it as disposed.',
                null,
                422
            );
        }

        app(MaintenanceAutoScheduleService::class)
            ->cancelScheduledMaintenanceForUnit($unit, 'Unit disposed — beyond repair.');

        $unit->update([
            'status' => 'disposed',
            'current_borrower_id' => null,
            'borrowing_id' => null,
        ]);

        $item = $unit->item;
        if ($item) {
            if ($item->units()->where('status', '!=', 'disposed')->count() === 0) {
                $item->applyDisposedState();
            } else {
                if ($item->status === 'disposed') {
                    $item->update(['status' => 'available']);
                }
                $item->syncStockFromUnits();
            }
        }

        $message = 'Unit ' . $unit->unit_code . ' marked as disposed.';

        return $this->disposeUnitResponse($request, $message, $unit->fresh());
    }

    /**
     * @param  int  $errorStatus  HTTP status when $success is false (JSON/AJAX only).
     */
    protected function disposeUnitResponse(Request $request, string $message, ?ItemUnit $unit = null, int $errorStatus = 200): \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
    {
        $success = $unit !== null;

        if ($request->expectsJson() || $request->ajax()) {
            if (! $success) {
                return response()->json(['message' => $message], $errorStatus);
            }

            return response()->json([
                'message' => $message,
                'unit' => $unit,
            ]);
        }

        return redirect()
            ->back()
            ->with($success ? 'success' : 'error', $message);
    }
}
