

<?php $__env->startSection('title', 'Equipment Utilization'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-8">
    <!-- Header -->
    <div class="flex items-center space-x-4 animate-fade-in-up">
        <a href="<?php echo e(route('analytics.index')); ?>" class="p-2 rounded-xl hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-poppins">Equipment Utilization</h1>
            <p class="text-gray-600">Usage rates and efficiency metrics for all equipment (last 30 days)</p>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 animate-fade-in-up stagger-1">
        <?php
            $highUtil = collect($utilizations)->where('utilization.status', 'high')->count();
            $modUtil = collect($utilizations)->where('utilization.status', 'moderate')->count();
            $lowUtil = collect($utilizations)->where('utilization.status', 'low')->count();
        ?>
        <div class="bg-white rounded-2xl ring-1 ring-gray-200 p-6 border-l-4 border-green-500">
            <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">High Utilization</p>
            <p class="text-3xl font-bold text-green-600 mt-1 font-poppins"><?php echo e($highUtil); ?></p>
            <p class="text-xs text-gray-500 mt-1">≥ 80% usage</p>
        </div>
        <div class="bg-white rounded-2xl ring-1 ring-gray-200 p-6 border-l-4 border-yellow-500">
            <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Moderate</p>
            <p class="text-3xl font-bold text-yellow-600 mt-1 font-poppins"><?php echo e($modUtil); ?></p>
            <p class="text-xs text-gray-500 mt-1">50-80% usage</p>
        </div>
        <div class="bg-white rounded-2xl ring-1 ring-gray-200 p-6 border-l-4 border-gray-400">
            <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Low Utilization</p>
            <p class="text-3xl font-bold text-gray-600 mt-1 font-poppins"><?php echo e($lowUtil); ?></p>
            <p class="text-xs text-gray-500 mt-1">< 50% usage</p>
        </div>
    </div>

    <!-- Utilization Table -->
    <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm overflow-hidden animate-fade-in-up stagger-2">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left px-6 py-4 font-semibold text-gray-600 uppercase tracking-wide text-xs">Equipment</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600 uppercase tracking-wide text-xs">Category</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600 uppercase tracking-wide text-xs">Utilization Rate</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600 uppercase tracking-wide text-xs">Hours Used</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600 uppercase tracking-wide text-xs">Status</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600 uppercase tracking-wide text-xs">Stock</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php $__empty_1 = true; $__currentLoopData = $utilizations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 font-medium text-gray-900"><?php echo e($data['item']->name); ?></td>
                        <td class="px-6 py-4 text-gray-600"><?php echo e($data['item']->category); ?></td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-32 bg-gray-200 rounded-full h-2.5">
                                    <?php
                                        $rate = $data['utilization']['utilization_rate'];
                                        $barColor = $rate >= 80 ? 'bg-green-500' : ($rate >= 50 ? 'bg-yellow-500' : 'bg-gray-400');
                                    ?>
                                    <div class="h-2.5 rounded-full <?php echo e($barColor); ?>" style="width: <?php echo e(min($rate, 100)); ?>%"></div>
                                </div>
                                <span class="text-sm font-bold text-gray-700 w-14"><?php echo e($rate); ?>%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-700"><?php echo e(number_format($data['utilization']['total_hours_used'], 1)); ?>h</td>
                        <td class="px-6 py-4">
                            <?php
                                $statusColors = ['high' => 'bg-green-50 text-green-700', 'moderate' => 'bg-yellow-50 text-yellow-700', 'low' => 'bg-gray-100 text-gray-600'];
                            ?>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold <?php echo e($statusColors[$data['utilization']['status']] ?? 'bg-gray-100 text-gray-600'); ?>">
                                <?php echo e(ucfirst($data['utilization']['status'])); ?>

                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-700">
                            <?php echo e($data['item']->available_stock); ?> / <?php echo e($data['item']->total_stock); ?>

                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                            <p>No utilization data available</p>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Cedric\SEIMS\resources\views/analytics/utilization.blade.php ENDPATH**/ ?>