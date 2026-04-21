

<?php $__env->startSection('title', 'Staff Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">

    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold text-gray-900 font-poppins">Welcome back, <?php echo e(auth()->user()->name); ?></h1>
        <p class="text-sm text-gray-500"><?php echo e(now()->format('l, F d, Y')); ?></p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
        <?php if (isset($component)) { $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stat-card','data' => ['label' => 'Total Items','value' => $totalItems,'color' => 'green','icon' => '<svg class=\'w-6 h-6 text-green-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4\'></path></svg>']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Total Items','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($totalItems),'color' => 'green','icon' => '<svg class=\'w-6 h-6 text-green-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4\'></path></svg>']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $attributes = $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $component = $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>

        <?php if (isset($component)) { $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stat-card','data' => ['label' => 'Low Stock','value' => $lowStockItems,'color' => 'orange','icon' => '<svg class=\'w-6 h-6 text-orange-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z\'></path></svg>']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Low Stock','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($lowStockItems),'color' => 'orange','icon' => '<svg class=\'w-6 h-6 text-orange-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z\'></path></svg>']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $attributes = $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $component = $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>

        <?php if (isset($component)) { $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stat-card','data' => ['label' => 'Pending','value' => $pendingRequests,'color' => 'blue','icon' => '<svg class=\'w-6 h-6 text-blue-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z\'></path></svg>']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Pending','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($pendingRequests),'color' => 'blue','icon' => '<svg class=\'w-6 h-6 text-blue-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z\'></path></svg>']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $attributes = $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $component = $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>

        <?php if (isset($component)) { $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stat-card','data' => ['label' => 'Overdue','value' => $overdueItems,'color' => 'red','icon' => '<svg class=\'w-6 h-6 text-red-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z\'></path></svg>']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Overdue','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($overdueItems),'color' => 'red','icon' => '<svg class=\'w-6 h-6 text-red-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z\'></path></svg>']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $attributes = $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $component = $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>
    </div>

    <!-- Alerts -->
    <?php if($overdueItems > 0 || $maintenanceDue > 0): ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <?php if($overdueItems > 0): ?>
            <?php if (isset($component)) { $__componentOriginal0d56272e151b61c739c6026f4156b193 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0d56272e151b61c739c6026f4156b193 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.alert-banner','data' => ['type' => 'danger','message' => $overdueItems . ' Overdue Return' . ($overdueItems > 1 ? 's' : ''),'actionUrl' => route('staff.borrowings.index'),'actionLabel' => 'View']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('alert-banner'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'danger','message' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($overdueItems . ' Overdue Return' . ($overdueItems > 1 ? 's' : '')),'action-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('staff.borrowings.index')),'action-label' => 'View']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0d56272e151b61c739c6026f4156b193)): ?>
<?php $attributes = $__attributesOriginal0d56272e151b61c739c6026f4156b193; ?>
<?php unset($__attributesOriginal0d56272e151b61c739c6026f4156b193); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0d56272e151b61c739c6026f4156b193)): ?>
<?php $component = $__componentOriginal0d56272e151b61c739c6026f4156b193; ?>
<?php unset($__componentOriginal0d56272e151b61c739c6026f4156b193); ?>
<?php endif; ?>
        <?php endif; ?>
        <?php if($maintenanceDue > 0): ?>
            <?php if (isset($component)) { $__componentOriginal0d56272e151b61c739c6026f4156b193 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0d56272e151b61c739c6026f4156b193 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.alert-banner','data' => ['type' => 'warning','message' => $maintenanceDue . ' Maintenance Due','actionUrl' => route('maintenance.dashboard'),'actionLabel' => 'View']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('alert-banner'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'warning','message' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($maintenanceDue . ' Maintenance Due'),'action-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('maintenance.dashboard')),'action-label' => 'View']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0d56272e151b61c739c6026f4156b193)): ?>
<?php $attributes = $__attributesOriginal0d56272e151b61c739c6026f4156b193; ?>
<?php unset($__attributesOriginal0d56272e151b61c739c6026f4156b193); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0d56272e151b61c739c6026f4156b193)): ?>
<?php $component = $__componentOriginal0d56272e151b61c739c6026f4156b193; ?>
<?php unset($__componentOriginal0d56272e151b61c739c6026f4156b193); ?>
<?php endif; ?>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Pending Borrow Requests -->
    <div class="bg-white rounded-xl ring-1 ring-gray-100 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <h2 class="text-base font-bold text-gray-900 font-poppins">Pending Borrow Requests</h2>
            <a href="<?php echo e(route('staff.borrowings.index')); ?>" class="text-xs font-semibold text-green-600 hover:text-green-700">View All &rarr;</a>
        </div>
        <div class="divide-y divide-gray-50">
            <?php $__empty_1 = true; $__currentLoopData = $pendingBorrowings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $borrowing): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="px-5 py-3.5 hover:bg-gray-50 transition-colors">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center space-x-3 flex-1 min-w-0">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0 text-green-700 text-xs font-bold">
                            <?php echo e(strtoupper(substr($borrowing->user?->name ?? 'U', 0, 1))); ?>

                        </div>
                        <div class="min-w-0">
                            <p class="font-semibold text-gray-900 text-sm truncate"><?php echo e($borrowing->user?->name ?? 'Unknown'); ?></p>
                            <p class="text-xs text-gray-400 truncate"><?php echo e($borrowing->item?->name ?? 'Unknown'); ?> &middot; Qty: <?php echo e($borrowing->quantity); ?> &middot; <?php echo e($borrowing->created_at->diffForHumans()); ?></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 flex-shrink-0">
                        <form method="POST" action="<?php echo e(route('staff.borrowings.approve', $borrowing)); ?>" class="inline" x-data @submit.prevent="$dispatch('open-confirm-modal', { form: $el, title: 'Approve Request', message: 'Are you sure you want to approve this borrow request?', type: 'success' })">
                            <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-green-50 text-green-700 hover:bg-green-100 ring-1 ring-green-200/60 transition-all duration-200">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Approve
                            </button>
                        </form>
                        <form method="POST" action="<?php echo e(route('staff.borrowings.reject', $borrowing)); ?>" class="inline" x-data @submit.prevent="$dispatch('open-confirm-modal', { form: $el, title: 'Reject Request', message: 'Are you sure you want to reject this borrow request?', type: 'danger' })">
                            <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-red-50 text-red-700 hover:bg-red-100 ring-1 ring-red-200/60 transition-all duration-200">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                Reject
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="px-5 py-10 text-center">
                <p class="text-sm text-gray-400">No pending requests</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Overdue Borrowings -->
    <?php if($overdueBorrowings->count() > 0): ?>
    <div class="bg-white rounded-xl ring-1 ring-gray-100 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <h2 class="text-base font-bold text-gray-900 font-poppins">Overdue Borrowings</h2>
            <a href="<?php echo e(route('staff.borrowings.index')); ?>" class="text-xs font-semibold text-red-600 hover:text-red-700">Manage &rarr;</a>
        </div>
        <div class="divide-y divide-gray-50">
            <?php $__currentLoopData = $overdueBorrowings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $borrowing): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="px-5 py-3 hover:bg-gray-50 transition-colors flex items-center justify-between gap-4">
                <div class="flex items-center space-x-3 min-w-0">
                    <div class="w-7 h-7 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0 text-red-600 text-xs font-bold">
                        <?php echo e(strtoupper(substr($borrowing->user?->name ?? 'U', 0, 1))); ?>

                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-gray-900 truncate"><?php echo e($borrowing->user?->name ?? 'Unknown'); ?></p>
                        <p class="text-xs text-gray-400 truncate"><?php echo e($borrowing->item?->name ?? 'Unknown'); ?></p>
                    </div>
                </div>
                <span class="text-xs font-semibold text-red-600 whitespace-nowrap">Due <?php echo e($borrowing->expected_return_date->diffForHumans()); ?></span>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Cedric\SEIMS\resources\views/dashboard/staff.blade.php ENDPATH**/ ?>