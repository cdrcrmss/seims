

<?php $__env->startSection('title', 'Student Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6" x-data="{ showBorrowModal: false, selectedItem: null }">

    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold text-gray-900 font-poppins">Welcome back, <?php echo e(auth()->user()->name); ?></h1>
        <p class="text-sm text-gray-500"><?php echo e(now()->format('l, F d, Y')); ?></p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
        <a href="<?php echo e(route('student.borrowings.index', ['status' => 'issued'])); ?>" class="bg-white rounded-2xl p-6 ring-1 ring-gray-100 shadow-sm card-hover relative overflow-hidden cursor-pointer hover:ring-green-200 transition-all">
            <div class="absolute top-0 left-0 w-1 h-full bg-green-500 rounded-r-full"></div>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Active</p>
                    <p class="text-3xl font-bold text-green-600 font-poppins"><?php echo e($activeBorrowings->count()); ?></p>
                </div>
                <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                </div>
            </div>
        </a>

        <a href="<?php echo e(route('student.borrow.form')); ?>" class="bg-white rounded-2xl p-6 ring-1 ring-gray-100 shadow-sm card-hover relative overflow-hidden cursor-pointer hover:ring-blue-200 transition-all">
            <div class="absolute top-0 left-0 w-1 h-full bg-blue-500 rounded-r-full"></div>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Available Items</p>
                    <p class="text-3xl font-bold text-blue-600 font-poppins"><?php echo e($availableItems->count()); ?></p>
                </div>
                <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
            </div>
        </a>

        <a href="<?php echo e(route('student.borrowings.index', ['status' => 'returned'])); ?>" class="bg-white rounded-2xl p-6 ring-1 ring-gray-100 shadow-sm card-hover relative overflow-hidden cursor-pointer hover:ring-gray-200 transition-all">
            <div class="absolute top-0 left-0 w-1 h-full bg-gray-400 rounded-r-full"></div>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">History</p>
                    <p class="text-3xl font-bold text-gray-600 font-poppins"><?php echo e($borrowingHistory->count()); ?></p>
                </div>
                <div class="w-12 h-12 bg-gray-50 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </a>
    </div>

    <!-- Overdue Warning -->
    <?php if($overdueCount > 0): ?>
    <div class="flex items-center space-x-3 bg-red-50 rounded-xl p-4 ring-1 ring-red-100">
        <div class="w-9 h-9 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-red-800">You have <?php echo e($overdueCount); ?> overdue item<?php echo e($overdueCount > 1 ? 's' : ''); ?>! Please return them as soon as possible.</p>
        </div>
    </div>
    <?php endif; ?>

    <!-- Active Borrowings -->
    <div class="bg-white rounded-xl ring-1 ring-gray-100 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <h2 class="text-base font-bold text-gray-900 font-poppins">My Active Borrowings</h2>
            <a href="<?php echo e(route('student.borrowings.index')); ?>" class="text-xs font-semibold text-green-600 hover:text-green-700">View All &rarr;</a>
        </div>
        <div class="divide-y divide-gray-50">
            <?php $__empty_1 = true; $__currentLoopData = $activeBorrowings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $borrowing): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $isOverdue = $borrowing->status === 'issued' && $borrowing->expected_return_date < now();
                $statusColors = [
                    'pending' => 'bg-yellow-100 text-yellow-700',
                    'approved' => 'bg-blue-100 text-blue-700',
                    'issued' => $isOverdue ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700',
                ];
            ?>
            <div class="px-5 py-3 hover:bg-gray-50 transition-colors flex items-center justify-between gap-4">
                <div class="flex items-center space-x-3 min-w-0">
                    <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <?php if($borrowing->item?->image): ?>
                            <img src="<?php echo e(asset('storage/' . $borrowing->item->image)); ?>" class="w-8 h-8 rounded-lg object-cover" alt="">
                        <?php else: ?>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        <?php endif; ?>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-gray-900 truncate"><?php echo e($borrowing->item?->name ?? 'Unknown'); ?></p>
                        <p class="text-xs text-gray-400 truncate">Qty: <?php echo e($borrowing->quantity); ?> &middot; Due: <?php echo e($borrowing->expected_return_date ? $borrowing->expected_return_date->format('M d, Y') : 'N/A'); ?></p>
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <span class="text-xs font-semibold px-2 py-0.5 rounded-md <?php echo e($statusColors[$borrowing->status] ?? 'bg-gray-100 text-gray-600'); ?>">
                        <?php echo e($isOverdue ? 'Overdue' : ucfirst($borrowing->status)); ?>

                    </span>
                    <?php if($borrowing->status === 'pending'): ?>
                    <form method="POST" action="<?php echo e(route('student.borrowings.cancel', $borrowing)); ?>" class="inline" x-data @submit.prevent="$dispatch('open-confirm-modal', { form: $el, title: 'Cancel Request', message: 'Are you sure you want to cancel this borrow request?', type: 'danger' })">
                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-lg bg-red-50 text-red-700 hover:bg-red-100 ring-1 ring-red-200/60 transition-all duration-200">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            Cancel
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="px-5 py-10 text-center">
                <p class="text-sm text-gray-400">No active borrowings</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Notifications -->
    <?php if($notifications->count() > 0): ?>
    <div class="bg-white rounded-xl ring-1 ring-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="text-base font-bold text-gray-900 font-poppins">Notifications</h2>
        </div>
        <div class="divide-y divide-gray-50">
            <?php $__currentLoopData = $notifications->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="px-5 py-3 hover:bg-gray-50 transition-colors flex items-start space-x-3">
                <div class="w-7 h-7 bg-blue-50 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                    <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-sm text-gray-700"><?php echo e($notification->message); ?></p>
                    <p class="text-xs text-gray-400 mt-0.5"><?php echo e($notification->created_at->diffForHumans()); ?></p>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Cedric\SEIMS\resources\views/dashboard/student.blade.php ENDPATH**/ ?>