

<?php $__env->startSection('title', 'Analytics Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 animate-fade-in-up">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-poppins">Predictive Analytics</h1>
            <p class="text-gray-600">AI-powered insights for inventory management</p>
        </div>
        <form method="POST" action="<?php echo e(route('analytics.export')); ?>">
            <?php echo csrf_field(); ?>
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-xl font-semibold transition-all duration-200 flex items-center space-x-2 shadow-sm hover:shadow-md">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span>Export Report</span>
            </button>
        </form>
    </div>

    <!-- Quick Nav -->
    <div class="flex flex-wrap gap-3 animate-fade-in-up stagger-1">
        <a href="<?php echo e(route('analytics.demand-forecast')); ?>" class="px-4 py-2 bg-white ring-1 ring-gray-200 rounded-xl text-sm font-semibold text-gray-700 hover:bg-green-50 hover:text-green-700 hover:ring-green-200 transition-all">
            📈 Demand Forecast
        </a>
        <a href="<?php echo e(route('analytics.utilization')); ?>" class="px-4 py-2 bg-white ring-1 ring-gray-200 rounded-xl text-sm font-semibold text-gray-700 hover:bg-green-50 hover:text-green-700 hover:ring-green-200 transition-all">
            📊 Utilization
        </a>
        <a href="<?php echo e(route('analytics.maintenance-predictions')); ?>" class="px-4 py-2 bg-white ring-1 ring-gray-200 rounded-xl text-sm font-semibold text-gray-700 hover:bg-green-50 hover:text-green-700 hover:ring-green-200 transition-all">
            🔧 Maintenance
        </a>
        <a href="<?php echo e(route('analytics.overdue-risk')); ?>" class="px-4 py-2 bg-white ring-1 ring-gray-200 rounded-xl text-sm font-semibold text-gray-700 hover:bg-red-50 hover:text-red-700 hover:ring-red-200 transition-all">
            🚨 Overdue Risk
        </a>
        <a href="<?php echo e(route('analytics.peak-hours')); ?>" class="px-4 py-2 bg-white ring-1 ring-gray-200 rounded-xl text-sm font-semibold text-gray-700 hover:bg-purple-50 hover:text-purple-700 hover:ring-purple-200 transition-all">
            🕐 Peak Hours
        </a>
        <a href="<?php echo e(route('analytics.operational')); ?>" class="px-4 py-2 bg-white ring-1 ring-gray-200 rounded-xl text-sm font-semibold text-gray-700 hover:bg-blue-50 hover:text-blue-700 hover:ring-blue-200 transition-all">
            ⚙️ Operational
        </a>
    </div>

    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 animate-fade-in-up stagger-2">
        <div class="bg-white rounded-2xl ring-1 ring-gray-200 p-6 border-l-4 border-green-500">
            <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Total Items</p>
            <p class="text-3xl font-bold text-green-600 mt-1 font-poppins"><?php echo e($dashboardData['total_items']); ?></p>
        </div>
        <div class="bg-white rounded-2xl ring-1 ring-gray-200 p-6 border-l-4 border-red-500">
            <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Low Stock</p>
            <p class="text-3xl font-bold text-red-600 mt-1 font-poppins"><?php echo e($dashboardData['low_stock_items']); ?></p>
        </div>
        <div class="bg-white rounded-2xl ring-1 ring-gray-200 p-6 border-l-4 border-orange-500">
            <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Maintenance Due</p>
            <p class="text-3xl font-bold text-orange-600 mt-1 font-poppins"><?php echo e($dashboardData['maintenance_due_items']); ?></p>
        </div>
        <div class="bg-white rounded-2xl ring-1 ring-gray-200 p-6 border-l-4 border-blue-500">
            <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Active Reservations</p>
            <p class="text-3xl font-bold text-blue-600 mt-1 font-poppins"><?php echo e($dashboardData['active_reservations']); ?></p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Top Utilized Items -->
        <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-6 animate-fade-in-up stagger-3">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center space-x-2">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                <span>Top Utilized Equipment (30 Days)</span>
            </h2>
            <?php $__empty_1 = true; $__currentLoopData = $dashboardData['top_utilized_items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl mb-2">
                <div>
                    <p class="font-medium text-gray-900"><?php echo e($entry['item']->name); ?></p>
                    <p class="text-xs text-gray-500"><?php echo e($entry['item']->category); ?></p>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="w-24 bg-gray-200 rounded-full h-2.5">
                        <div class="h-2.5 rounded-full bg-blue-500" style="width: <?php echo e(min($entry['utilization_rate'], 100)); ?>%"></div>
                    </div>
                    <span class="text-sm font-bold text-gray-700 w-14 text-right"><?php echo e(number_format($entry['utilization_rate'], 1)); ?>%</span>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-gray-400 text-sm text-center py-4">No utilization data available</p>
            <?php endif; ?>
            <a href="<?php echo e(route('analytics.utilization')); ?>" class="block text-center text-sm text-green-600 font-semibold mt-3 hover:text-green-700">View All →</a>
        </div>

        <!-- Critical Maintenance Items -->
        <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-6 animate-fade-in-up stagger-3">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center space-x-2">
                <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <span>Critical Maintenance (Wear ≥ 70%)</span>
            </h2>
            <?php $__empty_1 = true; $__currentLoopData = $dashboardData['critical_maintenance_items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl mb-2">
                <div>
                    <p class="font-medium text-gray-900"><?php echo e($item->name); ?></p>
                    <p class="text-xs text-gray-500"><?php echo e($item->category); ?></p>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="w-24 bg-gray-200 rounded-full h-2.5">
                        <div class="h-2.5 rounded-full <?php echo e($item->wear_level >= 80 ? 'bg-red-500' : 'bg-orange-500'); ?>" style="width: <?php echo e($item->wear_level); ?>%"></div>
                    </div>
                    <span class="text-sm font-bold <?php echo e($item->wear_level >= 80 ? 'text-red-600' : 'text-orange-600'); ?> w-14 text-right"><?php echo e($item->wear_level); ?>%</span>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="text-center py-6 text-gray-400">
                <svg class="w-10 h-10 mx-auto mb-2 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <p>All equipment in good condition!</p>
            </div>
            <?php endif; ?>
            <a href="<?php echo e(route('analytics.maintenance-predictions')); ?>" class="block text-center text-sm text-green-600 font-semibold mt-3 hover:text-green-700">View Predictions →</a>
        </div>

        <!-- Monthly Borrowing Trends -->
        <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-6 animate-fade-in-up stagger-4">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Monthly Borrowing Trends (<?php echo e(now()->year); ?>)</h2>
            <div class="space-y-2">
                <?php $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']; ?>
                <?php $__currentLoopData = $monthlyBorrowings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex items-center space-x-3">
                    <span class="text-xs text-gray-500 w-8"><?php echo e($months[($data->month ?? 1) - 1] ?? 'N/A'); ?></span>
                    <div class="flex-1 bg-gray-100 rounded-full h-4">
                        <div class="h-4 bg-green-500 rounded-full flex items-center justify-end pr-2" style="width: <?php echo e(min(($data->count / max($monthlyBorrowings->max('count'), 1)) * 100, 100)); ?>%">
                            <?php if($data->count > 0): ?>
                            <span class="text-[10px] text-white font-bold"><?php echo e($data->count); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        <!-- Monthly Maintenance Costs -->
        <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-6 animate-fade-in-up stagger-4">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Monthly Maintenance Costs (<?php echo e(now()->year); ?>)</h2>
            <div class="space-y-2">
                <?php $__currentLoopData = $monthlyMaintenanceCosts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex items-center space-x-3">
                    <span class="text-xs text-gray-500 w-8"><?php echo e($months[($data->month ?? 1) - 1] ?? 'N/A'); ?></span>
                    <div class="flex-1 bg-gray-100 rounded-full h-4">
                        <?php $maxCost = $monthlyMaintenanceCosts->max('total_cost') ?? 1; ?>
                        <div class="h-4 bg-orange-500 rounded-full flex items-center justify-end pr-2" style="width: <?php echo e(min(($data->total_cost / max($maxCost, 1)) * 100, 100)); ?>%">
                            <?php if($data->total_cost > 0): ?>
                            <span class="text-[10px] text-white font-bold">₱<?php echo e(number_format($data->total_cost, 0)); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php if($monthlyMaintenanceCosts->isEmpty()): ?>
                <p class="text-gray-400 text-sm text-center py-4">No maintenance cost data yet</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Cedric\SEIMS\resources\views/analytics/index.blade.php ENDPATH**/ ?>