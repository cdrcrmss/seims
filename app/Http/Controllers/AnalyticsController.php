<?php

namespace App\Http\Controllers;

use App\Services\PredictiveAnalyticsService;
use App\Models\Item;
use App\Models\Borrowing;
use App\Models\Reservation;
use App\Models\MaintenanceRecord;
use App\Models\ProcurementRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    protected $analyticsService;

    public function __construct(PredictiveAnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Show the main analytics dashboard
     */
    public function index()
    {
        $dashboardData = $this->analyticsService->getDashboardAnalytics();
        
        // Get monthly trends (database-agnostic)
        $driver = DB::connection()->getDriverName();
        $monthExpr = $driver === 'sqlite' ? "CAST(strftime('%m', requested_date) AS INTEGER)" : "MONTH(requested_date)";
        $monthlyBorrowings = Borrowing::selectRaw("{$monthExpr} as month, COUNT(*) as count")
            ->whereYear('requested_date', now()->year)
            ->whereNotNull('requested_date')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $monthExprMaint = $driver === 'sqlite' ? "CAST(strftime('%m', completed_date) AS INTEGER)" : "MONTH(completed_date)";
        $monthlyMaintenanceCosts = MaintenanceRecord::selectRaw("{$monthExprMaint} as month, SUM(cost) as total_cost")
            ->where('status', 'completed')
            ->whereYear('completed_date', now()->year)
            ->whereNotNull('completed_date')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('analytics.index', compact(
            'dashboardData',
            'monthlyBorrowings',
            'monthlyMaintenanceCosts'
        ));
    }

    /**
     * Show demand forecasting analytics
     */
    public function demandForecast()
    {
        // Only analyze items that have borrowings (prevents loading ALL items)
        $items = Item::with(['borrowings', 'maintenanceRecords', 'procurementRequests'])
            ->whereHas('borrowings', function ($q) {
            $q->where('requested_date', '>=', now()->subDays(60));
        })->get();

        $forecasts = [];

        foreach ($items as $item) {
            $forecast = $this->analyticsService->forecastDemand($item, 30);
            if ($forecast['confidence'] !== 'low') {
                $forecasts[] = [
                    'item' => $item,
                    'forecast' => $forecast,
                ];
            }
        }

        // Sort by predicted demand (highest first)
        usort($forecasts, function ($a, $b) {
            return $b['forecast']['predicted_demand'] <=> $a['forecast']['predicted_demand'];
        });

        return view('analytics.demand-forecast', compact('forecasts'));
    }

    /**
     * Show equipment utilization analytics
     */
    public function utilization()
    {
        // Only analyze items that have been borrowed recently
        $items = Item::with(['borrowings'])
            ->whereHas('borrowings', function ($q) {
            $q->where('issued_date', '>=', now()->subDays(30));
        })->get();

        $utilizations = [];

        foreach ($items as $item) {
            $utilization = $this->analyticsService->calculateUtilizationRate($item, 30);
            $utilizations[] = [
                'item' => $item,
                'utilization' => $utilization,
            ];
        }

        // Sort by utilization rate (highest first)
        usort($utilizations, function ($a, $b) {
            return $b['utilization']['utilization_rate'] <=> $a['utilization']['utilization_rate'];
        });

        return view('analytics.utilization', compact('utilizations'));
    }

    /**
     * Show maintenance predictions
     */
    public function maintenancePredictions()
    {
        // Only predict for items that have wear or maintenance history
        $items = Item::with(['maintenanceRecords', 'borrowings'])
            ->where('wear_level', '>', 0)
            ->orWhereHas('maintenanceRecords')
            ->get();

        $predictions = [];

        foreach ($items as $item) {
            $prediction = $this->analyticsService->predictMaintenanceNeeds($item);
            if ($prediction['urgency'] !== 'low') {
                $predictions[] = [
                    'item' => $item,
                    'prediction' => $prediction,
                ];
            }
        }

        // Sort by urgency (critical first)
        $urgencyOrder = ['critical' => 4, 'high' => 3, 'moderate' => 2, 'low' => 1];
        usort($predictions, function ($a, $b) use ($urgencyOrder) {
            $urgencyA = $urgencyOrder[$a['prediction']['urgency']] ?? 0;
            $urgencyB = $urgencyOrder[$b['prediction']['urgency']] ?? 0;
            return $urgencyB <=> $urgencyA;
        });

        return view('analytics.maintenance-predictions', compact('predictions'));
    }

    /**
     * Show procurement analytics
     */
    public function procurement()
    {
        // Only analyze items with procurement history or low stock
        $items = Item::with(['procurementRequests', 'borrowings'])
            ->whereHas('procurementRequests')
            ->orWhere(function ($q) {
                $q->whereColumn('available_stock', '<=', 'low_stock_threshold');
            })
            ->get();

        $procurementAnalytics = [];

        foreach ($items as $item) {
            $analysis = $this->analyticsService->analyzeProcurementPatterns($item);
            $procurementAnalytics[] = [
                'item' => $item,
                'analysis' => $analysis,
            ];
        }

        $totalSpending = ProcurementRequest::where('status', 'received')
            ->whereYear('received_at', now()->year)
            ->sum('total_price');

        $pendingValue = ProcurementRequest::whereIn('status', ['pending', 'approved', 'ordered'])
            ->sum('total_price');

        return view('analytics.procurement', compact(
            'procurementAnalytics',
            'totalSpending',
            'pendingValue'
        ));
    }

    /**
     * Get analytics data for a specific item (API endpoint)
     */
    public function itemAnalytics(Item $item)
    {
        $forecast = $this->analyticsService->forecastDemand($item, 30);
        $utilization = $this->analyticsService->calculateUtilizationRate($item, 30);
        $maintenancePrediction = $this->analyticsService->predictMaintenanceNeeds($item);
        $procurementAnalysis = $this->analyticsService->analyzeProcurementPatterns($item);

        return response()->json([
            'item' => $item,
            'forecast' => $forecast,
            'utilization' => $utilization,
            'maintenance_prediction' => $maintenancePrediction,
            'procurement_analysis' => $procurementAnalysis,
        ]);
    }

    /**
     * Overdue risk scoring — rank issued borrowings by risk of becoming/staying overdue.
     */
    public function overdueRisk()
    {
        $issuedBorrowings = Borrowing::with(['user', 'item'])
            ->where('status', 'issued')
            ->whereNotNull('expected_return_date')
            ->orderBy('expected_return_date', 'asc')
            ->get();

        $riskItems = $issuedBorrowings->map(function ($b) {
            $daysUntilDue = now()->diffInDays($b->expected_return_date, false);
            $isOverdue = $daysUntilDue < 0;

            // Score: higher = more urgent
            // Overdue items: base 70 + 3 per overdue day (max 100)
            // Due soon: 50 - days remaining * 5 (so due today = 50, due in 3 days = 35)
            if ($isOverdue) {
                $score = min(100, 70 + abs($daysUntilDue) * 3);
            } else {
                $score = max(0, 50 - $daysUntilDue * 5);
            }

            // Factor in user's past overdue behavior
            $userPastOverdue = Borrowing::where('user_id', $b->user_id)
                ->where('status', 'returned')
                ->whereColumn('returned_date', '>', 'expected_return_date')
                ->count();
            $score = min(100, $score + $userPastOverdue * 5);

            return [
                'borrowing_id' => $b->id,
                'item_name' => $b->item?->name,
                'borrower' => $b->user?->name,
                'expected_return' => $b->expected_return_date->format('M d, Y'),
                'days_until_due' => (int) $daysUntilDue,
                'is_overdue' => $isOverdue,
                'risk_score' => $score,
                'risk_level' => $score >= 70 ? 'critical' : ($score >= 40 ? 'high' : ($score >= 20 ? 'medium' : 'low')),
                'user_past_overdue' => $userPastOverdue,
            ];
        })->sortByDesc('risk_score')->values();

        return view('analytics.overdue-risk', compact('riskItems'));
    }

    /**
     * Peak hours heatmap — borrowing and reservation activity by day/hour.
     */
    public function peakHours()
    {
        $driver = DB::connection()->getDriverName();

        // Borrowing activity by hour of day and day of week
        if ($driver === 'sqlite') {
            $hourExpr = "CAST(strftime('%H', created_at) AS INTEGER)";
            $dowExpr = "CAST(strftime('%w', created_at) AS INTEGER)";
        } else {
            $hourExpr = "HOUR(created_at)";
            $dowExpr = "DAYOFWEEK(created_at) - 1"; // 0=Sunday
        }

        $borrowingHeatmap = Borrowing::selectRaw("{$dowExpr} as day_of_week, {$hourExpr} as hour, COUNT(*) as count")
            ->where('created_at', '>=', now()->subDays(90))
            ->groupBy('day_of_week', 'hour')
            ->orderBy('day_of_week')
            ->orderBy('hour')
            ->get();

        // Reservation activity by hour/day
        if ($driver === 'sqlite') {
            $hourExprR = "CAST(strftime('%H', start_datetime) AS INTEGER)";
            $dowExprR = "CAST(strftime('%w', start_datetime) AS INTEGER)";
        } else {
            $hourExprR = "HOUR(start_datetime)";
            $dowExprR = "DAYOFWEEK(start_datetime) - 1";
        }

        $reservationHeatmap = Reservation::selectRaw("{$dowExprR} as day_of_week, {$hourExprR} as hour, COUNT(*) as count")
            ->where('start_datetime', '>=', now()->subDays(90))
            ->whereIn('status', ['approved', 'checked_in', 'completed'])
            ->groupBy('day_of_week', 'hour')
            ->orderBy('day_of_week')
            ->orderBy('hour')
            ->get();

        // Build heatmap matrix (7 days x 24 hours)
        $dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        $borrowMatrix = [];
        $reserveMatrix = [];
        for ($d = 0; $d < 7; $d++) {
            for ($h = 0; $h < 24; $h++) {
                $borrowMatrix[$d][$h] = 0;
                $reserveMatrix[$d][$h] = 0;
            }
        }
        foreach ($borrowingHeatmap as $row) {
            $borrowMatrix[(int)$row->day_of_week][(int)$row->hour] = (int)$row->count;
        }
        foreach ($reservationHeatmap as $row) {
            $reserveMatrix[(int)$row->day_of_week][(int)$row->hour] = (int)$row->count;
        }

        return view('analytics.peak-hours', compact('borrowMatrix', 'reserveMatrix', 'dayNames'));
    }

    /**
     * Operational analytics — SLA performance, scan throughput, no-show rates.
     */
    public function operational()
    {
        // Maintenance SLA metrics
        $completedTickets = MaintenanceRecord::where('status', 'completed')
            ->where('completed_date', '>=', now()->subDays(90))
            ->get();

        $avgResolutionHours = $completedTickets->isNotEmpty()
            ? round($completedTickets->avg(function ($t) {
                return $t->started_at && $t->completed_date
                    ? $t->started_at->diffInHours($t->completed_date)
                    : 0;
            }), 1)
            : null;

        $openTicketCount = MaintenanceRecord::openTickets()->count();
        $mttr = MaintenanceRecord::mttr();

        // No-show rate
        $totalReservations90d = Reservation::where('created_at', '>=', now()->subDays(90))
            ->whereIn('status', ['completed', 'checked_in', 'no_show', 'cancelled'])
            ->count();
        $noShows90d = Reservation::where('status', 'no_show')
            ->where('updated_at', '>=', now()->subDays(90))
            ->count();
        $noShowRate = $totalReservations90d > 0 ? round(($noShows90d / $totalReservations90d) * 100, 1) : 0;

        // Borrowing turnaround (avg days from issued to returned)
        $driver = DB::connection()->getDriverName();
        if ($driver === 'sqlite') {
            $diffExpr = "CAST(julianday(returned_date) - julianday(issued_date) AS INTEGER)";
        } else {
            $diffExpr = "DATEDIFF(returned_date, issued_date)";
        }
        $avgTurnaround = Borrowing::where('status', 'returned')
            ->where('returned_date', '>=', now()->subDays(90))
            ->whereNotNull('issued_date')
            ->selectRaw("AVG({$diffExpr}) as avg_days")
            ->value('avg_days');
        $avgTurnaround = $avgTurnaround ? round($avgTurnaround, 1) : null;

        // Return condition breakdown
        $conditionBreakdown = Borrowing::where('status', 'returned')
            ->where('returned_date', '>=', now()->subDays(90))
            ->selectRaw("return_condition, COUNT(*) as count")
            ->groupBy('return_condition')
            ->pluck('count', 'return_condition')
            ->toArray();

        $operationalData = [
            'mttr_hours' => $mttr,
            'avg_resolution_hours' => $avgResolutionHours,
            'open_tickets' => $openTicketCount,
            'no_show_rate' => $noShowRate,
            'no_shows_90d' => $noShows90d,
            'total_reservations_90d' => $totalReservations90d,
            'avg_turnaround_days' => $avgTurnaround,
            'condition_breakdown' => $conditionBreakdown,
        ];

        return view('analytics.operational', compact('operationalData'));
    }

    /**
     * Generate reports export (CSV or JSON)
     */
    public function exportReport(Request $request)
    {
        $type = $request->input('type', 'comprehensive');
        $format = $request->input('format', 'csv');
        
        // Generate report data based on type
        $data = [];
        
        switch ($type) {
            case 'demand_forecast':
                $data = $this->generateDemandForecastReport();
                break;
            case 'utilization':
                $data = $this->generateUtilizationReport();
                break;
            case 'maintenance':
                $data = $this->generateMaintenanceReport();
                break;
            case 'procurement':
                $data = $this->generateProcurementReport();
                break;
            default:
                $data = $this->generateComprehensiveReport();
        }

        $filename = 'seims_' . $type . '_report_' . now()->format('Y-m-d');

        if ($format === 'csv') {
            return $this->exportAsCsv($data, $filename, $type);
        }

        // Fall back to JSON
        return response()->json($data)
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '.json"');
    }

    /**
     * Export data as a CSV download.
     */
    private function exportAsCsv(array $data, string $filename, string $type)
    {
        $rows = $this->flattenReportToCsvRows($data, $type);

        $callback = function () use ($rows) {
            $handle = fopen('php://output', 'w');
            // BOM for Excel UTF-8
            fwrite($handle, "\xEF\xBB\xBF");
            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"',
        ]);
    }

    /**
     * Flatten report data into CSV rows (header + data).
     */
    private function flattenReportToCsvRows(array $data, string $type): array
    {
        $rows = [];

        if ($type === 'demand_forecast' && isset($data['items'])) {
            $rows[] = ['Item Name', 'Category', 'Current Stock', 'Predicted Demand', 'Confidence'];
            foreach ($data['items'] as $item) {
                $rows[] = [
                    $item['name'],
                    $item['category'],
                    $item['current_stock'],
                    $item['forecast']['predicted_demand'] ?? 'N/A',
                    $item['forecast']['confidence'] ?? 'N/A',
                ];
            }
        } elseif ($type === 'utilization' && isset($data['items'])) {
            $rows[] = ['Item Name', 'Category', 'Utilization Rate (%)', 'Total Hours', 'Days Analyzed'];
            foreach ($data['items'] as $item) {
                $rows[] = [
                    $item['name'],
                    $item['category'],
                    $item['utilization']['utilization_rate'] ?? 'N/A',
                    $item['utilization']['total_hours'] ?? 'N/A',
                    $item['utilization']['days_analyzed'] ?? 'N/A',
                ];
            }
        } elseif ($type === 'comprehensive') {
            $rows[] = ['Metric', 'Value'];
            $rows[] = ['Generated At', $data['generated_at'] ?? now()->toIso8601String()];
            if (isset($data['inventory_summary'])) {
                $rows[] = ['Total Items', $data['inventory_summary']['total_items']];
                $rows[] = ['Low Stock Items', $data['inventory_summary']['low_stock_items']];
                $rows[] = ['Total Inventory Value', $data['inventory_summary']['total_value']];
            }
            if (isset($data['borrowing_summary'])) {
                $rows[] = ['Total Borrowings', $data['borrowing_summary']['total_borrowings']];
                $rows[] = ['Active Borrowings', $data['borrowing_summary']['active_borrowings']];
                $rows[] = ['Overdue Returns', $data['borrowing_summary']['overdue_returns']];
            }
            if (isset($data['maintenance_summary'])) {
                $rows[] = ['Total Maintenance Records', $data['maintenance_summary']['total_maintenance']];
                $rows[] = ['Upcoming Maintenance', $data['maintenance_summary']['upcoming']];
                $rows[] = ['Overdue Maintenance', $data['maintenance_summary']['overdue']];
                $rows[] = ['Maintenance Costs (Year)', $data['maintenance_summary']['total_costs']];
            }
        } elseif ($type === 'maintenance') {
            $rows[] = ['Metric', 'Value'];
            $rows[] = ['Total Records', $data['total_records'] ?? 0];
            $rows[] = ['Upcoming', $data['upcoming'] ?? 0];
            $rows[] = ['Overdue', $data['overdue'] ?? 0];
            $rows[] = ['Completed This Year', $data['completed_this_year'] ?? 0];
            $rows[] = ['Total Costs This Year', $data['total_costs_this_year'] ?? 0];
            if (isset($data['critical_items'])) {
                $rows[] = [];
                $rows[] = ['Critical Items', 'Wear Level', 'Category'];
                foreach ($data['critical_items'] as $ci) {
                    $rows[] = [$ci['name'], $ci['wear_level'], $ci['category']];
                }
            }
        } elseif ($type === 'procurement') {
            $rows[] = ['Metric', 'Value'];
            $rows[] = ['Total Requests', $data['total_requests'] ?? 0];
            $rows[] = ['Pending', $data['pending'] ?? 0];
            $rows[] = ['Total Spending (Year)', $data['total_spending_this_year'] ?? 0];
            $rows[] = ['Auto-Generated Count', $data['auto_generated_count'] ?? 0];
            if (isset($data['low_stock_items'])) {
                $rows[] = [];
                $rows[] = ['Low Stock Item', 'Available', 'Threshold', 'Category'];
                foreach ($data['low_stock_items'] as $li) {
                    $rows[] = [$li['name'], $li['available_stock'], $li['low_stock_threshold'], $li['category']];
                }
            }
        } else {
            // Fallback: just dump key-value pairs
            $rows[] = ['Key', 'Value'];
            foreach ($data as $key => $value) {
                $rows[] = [$key, is_array($value) ? json_encode($value) : $value];
            }
        }

        return $rows;
    }

    /**
     * Generate comprehensive report data
     */
    private function generateComprehensiveReport(): array
    {
        return [
            'generated_at' => now()->toIso8601String(),
            'dashboard' => $this->analyticsService->getDashboardAnalytics(),
            'inventory_summary' => [
                'total_items' => Item::count(),
                'low_stock_items' => Item::lowStock()->count(),
                'total_value' => Item::sum(DB::raw('total_stock * unit_price')),
            ],
            'borrowing_summary' => [
                'total_borrowings' => Borrowing::count(),
                'active_borrowings' => Borrowing::whereIn('status', ['approved', 'issued'])->count(),
                'overdue_returns' => Borrowing::where('status', 'issued')
                    ->where('expected_return_date', '<', now())
                    ->count(),
            ],
            'maintenance_summary' => [
                'total_maintenance' => MaintenanceRecord::count(),
                'upcoming' => MaintenanceRecord::upcoming()->count(),
                'overdue' => MaintenanceRecord::overdue()->count(),
                'total_costs' => MaintenanceRecord::where('status', 'completed')
                    ->whereYear('completed_date', now()->year)
                    ->sum('cost'),
            ],
        ];
    }

    // Additional private methods for specific report types
    private function generateDemandForecastReport(): array
    {
        $items = Item::with(['borrowings', 'maintenanceRecords', 'procurementRequests'])->get();
        $report = [
            'generated_at' => now()->toIso8601String(),
            'type' => 'demand_forecast',
            'items' => [],
        ];
        foreach ($items as $item) {
            $forecast = $this->analyticsService->forecastDemand($item, 30);
            $report['items'][] = [
                'name' => $item->name,
                'category' => $item->category,
                'current_stock' => $item->available_stock,
                'forecast' => $forecast,
            ];
        }
        return $report;
    }

    private function generateUtilizationReport(): array
    {
        $items = Item::with(['borrowings'])->get();
        $report = [
            'generated_at' => now()->toIso8601String(),
            'type' => 'utilization',
            'items' => [],
        ];
        foreach ($items as $item) {
            $utilization = $this->analyticsService->calculateUtilizationRate($item, 30);
            $report['items'][] = [
                'name' => $item->name,
                'category' => $item->category,
                'utilization' => $utilization,
            ];
        }
        return $report;
    }

    private function generateMaintenanceReport(): array
    {
        return [
            'generated_at' => now()->toIso8601String(),
            'type' => 'maintenance',
            'total_records' => MaintenanceRecord::count(),
            'upcoming' => MaintenanceRecord::upcoming()->count(),
            'overdue' => MaintenanceRecord::overdue()->count(),
            'completed_this_year' => MaintenanceRecord::where('status', 'completed')
                ->whereYear('completed_date', now()->year)->count(),
            'total_costs_this_year' => MaintenanceRecord::where('status', 'completed')
                ->whereYear('completed_date', now()->year)->sum('cost'),
            'critical_items' => Item::where('wear_level', '>=', 70)
                ->select('name', 'wear_level', 'category')->get()->toArray(),
        ];
    }

    private function generateProcurementReport(): array
    {
        return [
            'generated_at' => now()->toIso8601String(),
            'type' => 'procurement',
            'total_requests' => ProcurementRequest::count(),
            'pending' => ProcurementRequest::pending()->count(),
            'total_spending_this_year' => ProcurementRequest::where('status', 'received')
                ->whereYear('received_at', now()->year)->sum('total_price'),
            'auto_generated_count' => ProcurementRequest::autoGenerated()->count(),
            'low_stock_items' => Item::lowStock()->select('name', 'available_stock', 'low_stock_threshold', 'category')->get()->toArray(),
        ];
    }
}
