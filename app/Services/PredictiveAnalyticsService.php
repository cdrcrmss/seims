<?php

namespace App\Services;

use App\Models\Item;
use App\Models\ItemUnit;
use App\Models\Borrowing;
use App\Models\MaintenanceRecord;
use App\Models\ProcurementRequest;
use Illuminate\Support\Facades\DB;

class PredictiveAnalyticsService
{
    /**
     * Calculate demand forecast for item over next period
     */
    public function forecastDemand(Item $item, int $days = 30): array
    {
        // Get historical borrowing data
        $historicalBorrowings = Borrowing::where('item_id', $item->id)
            ->where('requested_date', '>=', now()->subDays($days * 2))
            ->selectRaw('DATE(requested_date) as date, SUM(quantity) as total_quantity')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        if ($historicalBorrowings->isEmpty()) {
            return [
                'average_daily_demand' => 0,
                'predicted_demand' => 0,
                'confidence' => 'low',
                'trend' => 'stable',
            ];
        }

        $totalQuantity = $historicalBorrowings->sum('total_quantity');
        $dataPoints = $historicalBorrowings->count();
        $averageDailyDemand = $totalQuantity / max($dataPoints, 1);

        // Simple linear regression for trend
        $trend = $this->calculateTrend($historicalBorrowings);

        // Predict demand for next period
        $predictedDemand = $averageDailyDemand * $days;
        
        // Apply trend adjustment
        if ($trend > 0.1) {
            $predictedDemand *= 1.2; // 20% increase for upward trend
        } elseif ($trend < -0.1) {
            $predictedDemand *= 0.8; // 20% decrease for downward trend
        }

        return [
            'average_daily_demand' => round($averageDailyDemand, 2),
            'predicted_demand' => round($predictedDemand, 0),
            'confidence' => $dataPoints >= 10 ? 'high' : ($dataPoints >= 5 ? 'medium' : 'low'),
            'trend' => $trend > 0.1 ? 'increasing' : ($trend < -0.1 ? 'decreasing' : 'stable'),
            'historical_data_points' => $dataPoints,
        ];
    }

    /**
     * Calculate equipment utilization rate
     */
    public function calculateUtilizationRate(Item $item, int $days = 30): array
    {
        $totalPossibleHours = $days * 24; // Assuming 24/7 availability
        
        $borrowings = Borrowing::where('item_id', $item->id)
            ->whereIn('status', ['issued', 'returned'])
            ->where('issued_date', '>=', now()->subDays($days))
            ->get();

        $totalUsedHours = 0;
        foreach ($borrowings as $borrowing) {
            $issueDate = $borrowing->issued_date;
            $returnDate = $borrowing->returned_date ?? now();
            $hoursUsed = $issueDate->diffInHours($returnDate);
            $totalUsedHours += $hoursUsed * $borrowing->quantity;
        }

        $utilizationRate = ($totalUsedHours / max($totalPossibleHours, 1)) * 100;

        return [
            'utilization_rate' => min(round($utilizationRate, 2), 100),
            'total_hours_used' => round($totalUsedHours, 2),
            'total_possible_hours' => $totalPossibleHours,
            'status' => $utilizationRate >= 80 ? 'high' : ($utilizationRate >= 50 ? 'moderate' : 'low'),
        ];
    }

    /**
     * Predict maintenance needs based on wear patterns
     */
    public function predictMaintenanceNeeds(Item $item): array
    {
        $recentMaintenance = MaintenanceRecord::where('item_id', $item->id)
            ->where('status', 'completed')
            ->orderBy('completed_date', 'desc')
            ->take(5)
            ->get();

        if ($recentMaintenance->isEmpty()) {
            $currentWear = $item->wear_level ?? 0;
            $urgency = 'low';
            if ($currentWear >= 70) {
                $urgency = 'critical';
            } elseif ($currentWear >= 50) {
                $urgency = 'high';
            } elseif ($currentWear >= 30) {
                $urgency = 'moderate';
            }

            // No baseline history: assume gradual wear accumulation for horizons only
            $assumedWearPerDay = 0.5;
            $criticalThreshold = 80;
            $wearRemaining = max(0.1, $criticalThreshold - $currentWear);
            $daysUntilCritical = max(14, round($wearRemaining / $assumedWearPerDay));

            return [
                'next_maintenance_date' => now()->addDays(min(90, (int) floor($daysUntilCritical * 0.8))),
                'current_wear_level' => $currentWear,
                'average_wear_rate' => 0,
                'days_until_critical' => (int) $daysUntilCritical,
                'urgency' => $urgency,
                'confidence' => 'low',
                'reasoning' => $currentWear > 0
                    ? "Wear level {$currentWear}% with limited maintenance history — schedule is an estimate."
                    : 'No completed maintenance baseline on file. Showing a preventive schedule assuming typical accumulation.',
            ];
        }

        // Calculate average wear increase per day
        $wearIncreaseRates = [];
        for ($i = 0; $i < $recentMaintenance->count() - 1; $i++) {
            $current = $recentMaintenance[$i];
            $previous = $recentMaintenance[$i + 1];
            
            $daysBetween = $previous->completed_date->diffInDays($current->completed_date);
            $wearIncrease = $current->wear_level - ($previous->wear_level ?? 0);
            
            if ($daysBetween > 0) {
                $wearIncreaseRates[] = $wearIncrease / $daysBetween;
            }
        }

        $averageWearRate = empty($wearIncreaseRates) ? 0.5 : array_sum($wearIncreaseRates) / count($wearIncreaseRates);
        
        // Current wear level
        $currentWear = $item->wear_level ?? 0;
        
        // Critical threshold is 80
        $criticalThreshold = 80;
        $wearRemaining = $criticalThreshold - $currentWear;
        
        // Predict days until critical
        $daysUntilCritical = max(1, $wearRemaining / max($averageWearRate, 0.1));
        
        $nextMaintenanceDate = now()->addDays((int) $daysUntilCritical * 0.8); // Schedule at 80% of predicted time

        $urgency = 'low';
        if ($currentWear >= 70) {
            $urgency = 'critical';
        } elseif ($currentWear >= 50) {
            $urgency = 'high';
        } elseif ($currentWear >= 30) {
            $urgency = 'moderate';
        }

        return [
            'next_maintenance_date' => $nextMaintenanceDate,
            'current_wear_level' => $currentWear,
            'average_wear_rate' => round($averageWearRate, 3),
            'days_until_critical' => round($daysUntilCritical, 0),
            'urgency' => $urgency,
            'confidence' => count($wearIncreaseRates) >= 3 ? 'high' : 'medium',
            'reasoning' => "Based on {$recentMaintenance->count()} maintenance records and current wear level of {$currentWear}%",
        ];
    }

    /**
     * Analyze procurement patterns
     */
    public function analyzeProcurementPatterns(Item $item): array
    {
        $procurements = ProcurementRequest::where('item_id', $item->id)
            ->where('status', 'received')
            ->orderBy('received_at', 'desc')
            ->take(10)
            ->get();

        if ($procurements->isEmpty()) {
            return [
                'average_order_quantity' => $item->reorder_quantity ?? 10,
                'average_delivery_time' => 7,
                'recommended_reorder_point' => $item->low_stock_threshold ?? 5,
                'optimal_order_quantity' => $item->reorder_quantity ?? 10,
            ];
        }

        $averageOrderQuantity = $procurements->avg('quantity');
        
        // Calculate average delivery time
        $deliveryTimes = [];
        foreach ($procurements as $procurement) {
            if ($procurement->ordered_at && $procurement->received_at) {
                $deliveryTimes[] = $procurement->ordered_at->diffInDays($procurement->received_at);
            }
        }
        $averageDeliveryTime = empty($deliveryTimes) ? 7 : array_sum($deliveryTimes) / count($deliveryTimes);

        // Get demand forecast
        $demandForecast = $this->forecastDemand($item, 30);
        $monthlyDemand = $demandForecast['predicted_demand'];

        // Calculate optimal reorder point (demand during lead time + safety stock)
        $leadTimeDemand = ($monthlyDemand / 30) * $averageDeliveryTime;
        $safetyStock = $leadTimeDemand * 0.5; // 50% safety stock
        $recommendedReorderPoint = ceil($leadTimeDemand + $safetyStock);

        // Economic Order Quantity (simplified)
        $optimalOrderQuantity = ceil($monthlyDemand / 2); // Order twice per month

        return [
            'average_order_quantity' => round($averageOrderQuantity, 0),
            'average_delivery_time' => round($averageDeliveryTime, 0),
            'recommended_reorder_point' => $recommendedReorderPoint,
            'optimal_order_quantity' => $optimalOrderQuantity,
            'monthly_demand_forecast' => round($monthlyDemand, 0),
        ];
    }

    /**
     * Generate comprehensive analytics dashboard data
     */
    public function getDashboardAnalytics(): array
    {
        $lowStockItems = Item::lowStock()->count();
        $maintenanceDueItems = Item::maintenanceDue()->count();
        $totalItems = Item::count();
        $activeReservations = DB::table('reservations')
            ->whereIn('status', ['pending', 'ongoing', 'approved'])
            ->count();

        $topUtilizedItems = Item::whereHas('borrowings', function ($q) {
            $q->where('issued_date', '>=', now()->subDays(30));
        })->take(20)->get()->map(function ($item) {
            $utilization = $this->calculateUtilizationRate($item, 30);
            return [
                'item' => $item,
                'utilization_rate' => $utilization['utilization_rate'],
            ];
        })->sortByDesc('utilization_rate')->take(5)->values();

        $criticalItemIds = ItemUnit::whereIn('status', ['maintenance', 'damaged'])
            ->pluck('item_id')
            ->unique();

        $criticalMaintenanceItems = Item::where('wear_level', '>=', 70)
            ->whereNotIn('id', $criticalItemIds)
            ->orderBy('wear_level', 'desc')
            ->take(10)
            ->get();

        return [
            'low_stock_items' => $lowStockItems,
            'maintenance_due_items' => $maintenanceDueItems,
            'total_items' => $totalItems,
            'active_reservations' => $activeReservations,
            'top_utilized_items' => $topUtilizedItems,
            'critical_maintenance_items' => $criticalMaintenanceItems,
        ];
    }

    /**
     * Calculate stock depletion speed and estimate days until stockout
     */
    public function calculateStockDepletionSpeed(Item $item, int $lookbackDays = 60): array
    {
        $borrowings = Borrowing::where('item_id', $item->id)
            ->where('requested_date', '>=', now()->subDays($lookbackDays))
            ->whereIn('status', ['approved', 'issued', 'returned'])
            ->get();

        $totalConsumed = $borrowings->sum('quantity');
        $dailyConsumption = $totalConsumed / max($lookbackDays, 1);

        $currentStock = $item->available_stock;
        $daysUntilStockout = $dailyConsumption > 0
            ? round($currentStock / $dailyConsumption, 0)
            : null; // null = no measurable depletion

        $daysUntilLowStock = $dailyConsumption > 0
            ? round(max(0, $currentStock - ($item->low_stock_threshold ?? 5)) / $dailyConsumption, 0)
            : null;

        // Weekly breakdown (last 4 weeks)
        $weeklyBreakdown = [];
        for ($w = 3; $w >= 0; $w--) {
            $weekStart = now()->subWeeks($w + 1);
            $weekEnd = now()->subWeeks($w);
            $weeklyCount = Borrowing::where('item_id', $item->id)
                ->whereBetween('requested_date', [$weekStart, $weekEnd])
                ->whereIn('status', ['approved', 'issued', 'returned'])
                ->sum('quantity');
            $weeklyBreakdown[] = [
                'week' => 'Week ' . (4 - $w),
                'quantity' => $weeklyCount,
            ];
        }

        return [
            'daily_consumption_rate' => round($dailyConsumption, 2),
            'days_until_stockout' => $daysUntilStockout,
            'days_until_low_stock' => $daysUntilLowStock,
            'current_stock' => $currentStock,
            'total_consumed_last_period' => $totalConsumed,
            'risk_level' => $this->assessStockRisk($daysUntilStockout),
            'weekly_breakdown' => $weeklyBreakdown,
        ];
    }

    /**
     * Get overall system health score (ISO/IEC 25010 reliability metric)
     */
    public function getSystemHealthScore(): array
    {
        $totalItems = Item::count();
        if ($totalItems === 0) {
            return ['score' => 100, 'grade' => 'A', 'factors' => []];
        }

        // Factor 1: Stock availability (25%)
        $availableRatio = Item::where('available_stock', '>', 0)->count() / $totalItems;
        $stockScore = $availableRatio * 100;

        // Factor 2: Maintenance health (25%)
        $overdueCount = MaintenanceRecord::overdue()->count();
        $maintenanceScore = max(0, 100 - ($overdueCount * 10));

        // Factor 3: Wear level average (25%)
        $avgWear = Item::whereNotNull('wear_level')->avg('wear_level') ?? 0;
        $wearScore = max(0, 100 - $avgWear);

        // Factor 4: Procurement efficiency (25%)
        $pendingProcurements = ProcurementRequest::pending()->count();
        $procurementScore = max(0, 100 - ($pendingProcurements * 5));

        $overallScore = ($stockScore * 0.25) + ($maintenanceScore * 0.25) + ($wearScore * 0.25) + ($procurementScore * 0.25);

        return [
            'score' => round($overallScore, 1),
            'grade' => $overallScore >= 90 ? 'A' : ($overallScore >= 75 ? 'B' : ($overallScore >= 60 ? 'C' : ($overallScore >= 40 ? 'D' : 'F'))),
            'factors' => [
                ['name' => 'Stock Availability', 'score' => round($stockScore, 1), 'weight' => '25%'],
                ['name' => 'Maintenance Health', 'score' => round($maintenanceScore, 1), 'weight' => '25%'],
                ['name' => 'Equipment Condition', 'score' => round($wearScore, 1), 'weight' => '25%'],
                ['name' => 'Procurement Efficiency', 'score' => round($procurementScore, 1), 'weight' => '25%'],
            ],
        ];
    }

    private function assessStockRisk(?int $daysUntilStockout): string
    {
        if ($daysUntilStockout === null) return 'none';
        if ($daysUntilStockout <= 3) return 'critical';
        if ($daysUntilStockout <= 7) return 'high';
        if ($daysUntilStockout <= 14) return 'moderate';
        return 'low';
    }

    /**
     * Calculate trend from historical data
     */
    private function calculateTrend($dataPoints): float
    {
        if ($dataPoints->count() < 2) {
            return 0;
        }

        $firstHalf = $dataPoints->take(ceil($dataPoints->count() / 2))->avg('total_quantity');
        $secondHalf = $dataPoints->skip(floor($dataPoints->count() / 2))->avg('total_quantity');

        if ($firstHalf == 0) {
            return 0;
        }

        return ($secondHalf - $firstHalf) / $firstHalf;
    }
}
