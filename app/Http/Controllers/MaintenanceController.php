<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRecord;
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

        $query = MaintenanceRecord::with(['item', 'technician']);

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

        return view('maintenance.index', compact('maintenanceRecords', 'upcomingMaintenance', 'overdueMaintenance', 'status'));
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
     * Update maintenance record as completed
     */
    public function complete(Request $request, MaintenanceRecord $maintenance)
    {
        $request->validate([
            'completed_date' => 'required|date',
            'condition_after' => 'required|string',
            'wear_level' => 'required|integer|min:0|max:100',
            'issues_found' => 'nullable|string',
            'actions_taken' => 'required|string',
            'cost' => 'nullable|numeric|min:0',
        ]);

        $maintenance->update([
            'status' => 'completed',
            'completed_date' => $request->completed_date,
            'condition_after' => $request->condition_after,
            'wear_level' => $request->wear_level,
            'issues_found' => $request->issues_found,
            'actions_taken' => $request->actions_taken,
            'cost' => $request->cost,
            'next_maintenance_date' => MaintenanceRecord::predictNextMaintenance($maintenance->item),
        ]);

        // Update item wear level
        $maintenance->item->update([
            'wear_level' => $request->wear_level,
            'last_maintenance_date' => $request->completed_date,
            'next_maintenance_date' => $maintenance->next_maintenance_date,
        ]);

        return redirect()->route('maintenance.index')
            ->with('success', 'Maintenance record completed successfully!');
    }

    /**
     * Generate predictive maintenance alerts
     */
    public function generatePredictiveAlerts()
    {
        $items = Item::where('wear_level', '>=', 60)
            ->whereDoesntHave('maintenanceRecords', function ($query) {
                $query->where('status', 'scheduled')
                    ->where('maintenance_type', 'predictive');
            })
            ->get();

        $alertsGenerated = 0;

        foreach ($items as $item) {
            $nextMaintenanceDate = MaintenanceRecord::predictNextMaintenance($item);

            $maintenance = MaintenanceRecord::create([
                'item_id' => $item->id,
                'maintenance_type' => 'predictive',
                'scheduled_date' => $nextMaintenanceDate,
                'wear_level' => $item->wear_level,
                'status' => 'scheduled',
                'notes' => 'Auto-generated predictive maintenance alert based on wear analysis.',
                'predictive_alert_sent' => true,
            ]);

            // Notify staff
            $staffUsers = User::where('role', 'staff')->orWhere('role', 'admin')->get();
            foreach ($staffUsers as $staff) {
                Notification::createMaintenanceAlert($item, $maintenance, $staff);
            }

            $alertsGenerated++;
        }

        return back()->with('success', "Generated {$alertsGenerated} predictive maintenance alerts.");
    }

    /**
     * Show maintenance dashboard with analytics
     */
    public function dashboard()
    {
        $upcomingMaintenance = MaintenanceRecord::upcoming()->with('item')->get();
        $overdueMaintenance = MaintenanceRecord::overdue()->with('item')->get();
        $recentlyCompleted = MaintenanceRecord::where('status', 'completed')
            ->orderBy('completed_date', 'desc')
            ->take(10)
            ->with('item')
            ->get();

        $criticalItems = Item::where('wear_level', '>=', 80)->get();
        $maintenanceCosts = MaintenanceRecord::where('status', 'completed')
            ->whereYear('completed_date', now()->year)
            ->sum('cost');

        return view('maintenance.dashboard', compact(
            'upcomingMaintenance',
            'overdueMaintenance',
            'recentlyCompleted',
            'criticalItems',
            'maintenanceCosts'
        ));
    }

    /**
     * Export filtered maintenance records as CSV or PDF.
     */
    public function exportReport(Request $request)
    {
        $request->validate([
            'date_from'        => 'nullable|date',
            'date_to'          => 'nullable|date|after_or_equal:date_from',
            'status'           => 'nullable|string',
            'maintenance_type' => 'nullable|string',
            'format'           => 'nullable|in:csv,pdf',
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

        if ($request->format === 'pdf') {
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

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"',
        ];

        $callback = function () use ($records) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, [
                'ID', 'Item', 'Type', 'Status',
                'Scheduled Date', 'Completed Date', 'Performed By',
                'Condition Before', 'Condition After', 'Wear Level (%)',
                'Issues Found', 'Actions Taken', 'Cost', 'Next Maintenance', 'Notes',
            ]);
            foreach ($records as $r) {
                fputcsv($handle, [
                    $r->id,
                    $r->item->name ?? 'N/A',
                    ucfirst($r->maintenance_type),
                    ucfirst(str_replace('_', ' ', $r->status)),
                    $r->scheduled_date?->format('Y-m-d'),
                    $r->completed_date?->format('Y-m-d'),
                    $r->technician->name ?? 'N/A',
                    $r->condition_before,
                    $r->condition_after,
                    $r->wear_level,
                    $r->issues_found,
                    $r->actions_taken,
                    $r->cost ? number_format($r->cost, 2) : '',
                    $r->next_maintenance_date?->format('Y-m-d'),
                    $r->notes,
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
