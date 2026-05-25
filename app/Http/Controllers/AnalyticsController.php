<?php

namespace App\Http\Controllers;

use App\Services\MaintenanceAutoScheduleService;
use App\Services\PredictiveAnalyticsService;
use App\Models\Item;
use App\Models\Borrowing;
use App\Models\Reservation;
use App\Models\MaintenanceRecord;
use App\Models\ProcurementRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

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
        MaintenanceAutoScheduleService::syncAllScheduledMaintenance();
        $dashboardData = $this->analyticsService->getDashboardAnalytics();
        $criticalUnits = MaintenanceAutoScheduleService::criticalUnits();
        
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
            'monthlyMaintenanceCosts',
            'criticalUnits'
        ));
    }

    /**
     * Show demand forecasting analytics
     */
    public function demandForecast()
    {
        $items = Item::with(['borrowings', 'maintenanceRecords', 'procurementRequests'])
            ->whereHas('borrowings', function ($q) {
                $q->where('requested_date', '>=', now()->subDays(60));
            })
            ->limit(120)
            ->get();

        if ($items->isEmpty()) {
            $items = Item::with(['borrowings', 'maintenanceRecords', 'procurementRequests'])
                ->whereHas('borrowings')
                ->limit(120)
                ->get();
        }

        if ($items->isEmpty()) {
            $items = Item::with(['borrowings', 'maintenanceRecords', 'procurementRequests'])
                ->orderBy('name')
                ->limit(40)
                ->get();
        }

        $forecasts = [];

        foreach ($items as $item) {
            $forecast = $this->analyticsService->forecastDemand($item, 30);
            $forecasts[] = [
                'item' => $item,
                'forecast' => $forecast,
            ];
        }

        usort($forecasts, function ($a, $b) {
            $diff = ($b['forecast']['predicted_demand'] ?? 0) <=> ($a['forecast']['predicted_demand'] ?? 0);

            return $diff !== 0 ? $diff : strcmp($a['item']->name, $b['item']->name);
        });

        return view('analytics.demand-forecast', compact('forecasts'));
    }

    /**
     * Show equipment utilization analytics
     */
    public function utilization(Request $request)
    {
        $statusFilter = $request->query('status');
        if (! in_array($statusFilter, ['high', 'moderate', 'low'], true)) {
            $statusFilter = null;
        }

        // Only analyze items that have been borrowed recently
        $items = Item::with(['borrowings'])
            ->whereHas('borrowings', function ($q) {
            $q->where('issued_date', '>=', now()->subDays(30));
        })->get();

        $allUtilizations = [];

        foreach ($items as $item) {
            $utilization = $this->analyticsService->calculateUtilizationRate($item, 30);
            $allUtilizations[] = [
                'item' => $item,
                'utilization' => $utilization,
            ];
        }

        usort($allUtilizations, function ($a, $b) {
            return $b['utilization']['utilization_rate'] <=> $a['utilization']['utilization_rate'];
        });

        $utilizations = $statusFilter
            ? array_values(array_filter(
                $allUtilizations,
                fn ($row) => ($row['utilization']['status'] ?? '') === $statusFilter
            ))
            : $allUtilizations;

        return view('analytics.utilization', compact('utilizations', 'allUtilizations', 'statusFilter'));
    }

    /**
     * Show maintenance predictions
     */
    public function maintenancePredictions(Request $request)
    {
        $urgencyFilter = $request->query('urgency');
        if (! in_array($urgencyFilter, ['critical', 'high', 'moderate', 'low'], true)) {
            $urgencyFilter = null;
        }

        $items = Item::with(['maintenanceRecords', 'borrowings'])
            ->where(function ($q) {
                $q->where('wear_level', '>', 0)
                    ->orWhereHas('maintenanceRecords')
                    ->orWhereHas('borrowings', function ($bq) {
                        $bq->where('requested_date', '>=', now()->subDays(365));
                    });
            })
            ->limit(300)
            ->get();

        if ($items->isEmpty()) {
            $items = Item::with(['maintenanceRecords', 'borrowings'])
                ->orderByDesc('wear_level')
                ->orderBy('name')
                ->limit(120)
                ->get();
        }

        $predictions = [];
        foreach ($items as $item) {
            $predictions[] = [
                'item' => $item,
                'prediction' => $this->analyticsService->predictMaintenanceNeeds($item),
            ];
        }

        $urgencyOrder = ['critical' => 4, 'high' => 3, 'moderate' => 2, 'low' => 1];
        usort($predictions, function ($a, $b) use ($urgencyOrder) {
            $scoreA = $urgencyOrder[$a['prediction']['urgency']] ?? 0;
            $scoreB = $urgencyOrder[$b['prediction']['urgency']] ?? 0;
            $diff = $scoreB <=> $scoreA;

            return $diff !== 0 ? $diff : strcmp($a['item']->name, $b['item']->name);
        });

        $allPredictions = $predictions;

        MaintenanceAutoScheduleService::syncAllScheduledMaintenance();
        $criticalUnits = MaintenanceAutoScheduleService::criticalUnits();
        $criticalItemIds = $criticalUnits->pluck('item_id')->unique()->all();

        $wearPredictions = array_values(array_filter(
            $allPredictions,
            fn ($row) => ! in_array($row['item']->id, $criticalItemIds, true)
        ));

        $allPredictions = $wearPredictions;
        $predictions = $urgencyFilter
            ? array_values(array_filter(
                $wearPredictions,
                fn ($row) => ($row['prediction']['urgency'] ?? '') === $urgencyFilter
            ))
            : $wearPredictions;

        return view('analytics.maintenance-predictions', compact(
            'predictions',
            'allPredictions',
            'urgencyFilter',
            'criticalUnits'
        ));
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

        return view('analytics.procurement', compact(
            'procurementAnalytics'
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
     * Export analytics report as PDF.
     */
    public function exportReport(Request $request)
    {
        $request->validate([
            'type'       => 'nullable|in:all,comprehensive,demand_forecast,utilization,maintenance',
            'format'     => 'nullable|in:pdf',
            'date_from'  => 'nullable|date',
            'date_to'    => 'nullable|date|after_or_equal:date_from',
        ]);

        $type = $request->input('type', 'comprehensive');

        $sectionLabels = [
            'comprehensive' => 'Comprehensive Overview',
            'demand_forecast' => 'Demand Forecast',
            'utilization' => 'Equipment Utilization',
            'maintenance' => 'Maintenance Predictions',
        ];

        $reportTitles = [
            'all' => 'All Analytics Reports',
            'comprehensive' => 'Comprehensive Analytics Report',
            'demand_forecast' => 'Demand Forecast Report',
            'utilization' => 'Equipment Utilization Report',
            'maintenance' => 'Maintenance Predictions Report',
        ];

        $reportDescriptions = [
            'all' => 'Combined export of all analytics report types.',
            'comprehensive' => 'Complete overview of inventory, borrowings, and maintenance metrics.',
            'demand_forecast' => 'Predicted demand for equipment over the next 30 days.',
            'utilization' => 'Equipment usage rates and efficiency analysis.',
            'maintenance' => 'Maintenance schedules, costs, and critical items requiring attention.',
        ];

        $sections = null;
        $data = [];

        if ($type === 'all') {
            $sections = [
                'comprehensive' => $this->generateComprehensiveReport(),
                'demand_forecast' => $this->generateDemandForecastReport(),
                'utilization' => $this->generateUtilizationReport(),
                'maintenance' => $this->generateMaintenanceReport(),
            ];
        } else {
            $data = match ($type) {
                'demand_forecast' => $this->generateDemandForecastReport(),
                'utilization' => $this->generateUtilizationReport(),
                'maintenance' => $this->generateMaintenanceReport(),
                default => $this->generateComprehensiveReport(),
            };
        }

        $filename = 'seims_' . $type . '_report_' . now()->format('Y-m-d') . '.pdf';

        $pdf = Pdf::loadView('analytics.report-pdf', [
            'data' => $data,
            'sections' => $sections,
            'section_labels' => $sectionLabels,
            'type' => $type,
            'generated_at' => now()->format('F d, Y h:i A'),
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
            'report_title' => $reportTitles[$type] ?? 'Analytics Report',
            'report_description' => $reportDescriptions[$type] ?? '',
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf->download($filename);
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
            }
        } elseif ($type === 'maintenance') {
            $rows[] = ['Metric', 'Value'];
            $rows[] = ['Total Records', $data['total_records'] ?? 0];
            $rows[] = ['Upcoming', $data['upcoming'] ?? 0];
            $rows[] = ['Overdue', $data['overdue'] ?? 0];
            $rows[] = ['Completed This Year', $data['completed_this_year'] ?? 0];
            // Total costs removed from export by client request
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
            // Total spending removed from export by client request
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
            'auto_generated_count' => ProcurementRequest::autoGenerated()->count(),
            'low_stock_items' => Item::lowStock()->select('name', 'available_stock', 'low_stock_threshold', 'category')->get()->toArray(),
        ];
    }
}
