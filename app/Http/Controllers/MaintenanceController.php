<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRecord;
use App\Models\Item;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaintenanceController extends Controller
{
    /**
     * Display a listing of maintenance records
     */
    public function index()
    {
        $maintenanceRecords = MaintenanceRecord::with(['item', 'technician'])
            ->orderBy('scheduled_date', 'desc')
            ->paginate(15);

        $upcomingMaintenance = MaintenanceRecord::upcoming()->count();
        $overdueMaintenance = MaintenanceRecord::overdue()->count();

        return view('maintenance.index', compact('maintenanceRecords', 'upcomingMaintenance', 'overdueMaintenance'));
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
}
