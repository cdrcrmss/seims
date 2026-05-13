

<?php $__env->startSection('title', 'My Borrowings'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-poppins">My Borrowings</h1>
            <p class="text-gray-600">View and manage your borrowing requests</p>
        </div>
        <div class="flex items-center space-x-4">
            <a href="<?php echo e(route('student.borrow.form')); ?>" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-sm hover:shadow-md transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                New Request
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <a href="<?php echo e(route('student.borrowings.index', ['status' => 'pending'])); ?>" class="bg-white rounded-2xl p-6 ring-1 ring-gray-100 shadow-sm card-hover cursor-pointer hover:ring-amber-200 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Pending</p>
                    <p class="text-3xl font-bold text-amber-600 font-poppins"><?php echo e($borrowings->where('status', 'pending')->count()); ?></p>
                </div>
                <div class="w-12 h-12 bg-amber-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </a>

        <a href="<?php echo e(route('student.borrowings.index', ['status' => 'approved'])); ?>" class="bg-white rounded-2xl p-6 ring-1 ring-gray-100 shadow-sm card-hover cursor-pointer hover:ring-teal-200 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Approved</p>
                    <p class="text-3xl font-bold text-teal-600 font-poppins"><?php echo e($borrowings->where('status', 'approved')->count()); ?></p>
                </div>
                <div class="w-12 h-12 bg-teal-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>
        </a>

        <a href="<?php echo e(route('student.borrowings.index', ['status' => 'issued'])); ?>" class="bg-white rounded-2xl p-6 ring-1 ring-gray-100 shadow-sm card-hover cursor-pointer hover:ring-green-200 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Active</p>
                    <p class="text-3xl font-bold text-green-600 font-poppins"><?php echo e($borrowings->where('status', 'issued')->count()); ?></p>
                </div>
                <div class="w-12 h-12 bg-green-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </a>

        <a href="<?php echo e(route('student.borrowings.index', ['status' => 'returned'])); ?>" class="bg-white rounded-2xl p-6 ring-1 ring-gray-100 shadow-sm card-hover cursor-pointer hover:ring-emerald-200 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Returned</p>
                    <p class="text-3xl font-bold text-emerald-600 font-poppins"><?php echo e($borrowings->where('status', 'returned')->count()); ?></p>
                </div>
                <div class="w-12 h-12 bg-emerald-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                    </svg>
                </div>
            </div>
        </a>
    </div>

    <!-- Borrowing List -->
    <div class="bg-white rounded-2xl ring-1 ring-gray-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <h2 class="text-xl font-bold text-gray-900">All Borrowings</h2>
            </div>
        </div>

        <div class="divide-y divide-white/10">
            <?php $__empty_1 = true; $__currentLoopData = $borrowings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $borrowing): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="p-6 hover:bg-white/5 transition-colors">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                        <!-- Request Info -->
                        <div class="flex-1">
                            <div class="flex items-start space-x-4">
                                <!-- Item Image -->
                                <div class="w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl flex items-center justify-center overflow-hidden flex-shrink-0">
                                    <?php if($borrowing->item?->image_path): ?>
                                        <img src="<?php echo e($borrowing->item->image_url); ?>" alt="<?php echo e($borrowing->item->name); ?>" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                    <?php endif; ?>
                                </div>

                                <!-- Request Details -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center space-x-2 mb-1">
                                        <h3 class="text-lg font-semibold text-gray-900 truncate"><?php echo e($borrowing->item?->name ?? 'Deleted Item'); ?></h3>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            <?php echo e($borrowing->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                               ($borrowing->status === 'approved' ? 'bg-blue-100 text-blue-800' : 
                                                ($borrowing->status === 'issued' ? 'bg-green-100 text-green-800' : 
                                                 ($borrowing->status === 'returned' ? 'bg-purple-100 text-purple-800' : 
                                                  'bg-red-100 text-red-800')))); ?>">
                                            <?php echo e(ucfirst($borrowing->status)); ?>

                                        </span>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm mt-3">
                                        <div>
                                            <p class="text-gray-500">Quantity</p>
                                            <p class="font-medium text-gray-900"><?php echo e($borrowing->quantity); ?></p>
                                        </div>
                                        <div>
                                            <p class="text-gray-500">Requested Date</p>
                                            <p class="font-medium text-gray-900"><?php echo e($borrowing->created_at->format('M d, Y')); ?></p>
                                        </div>
                                        <?php if($borrowing->expected_return_date): ?>
                                            <div>
                                                <p class="text-gray-500">Expected Return</p>
                                                <p class="font-medium text-gray-900"><?php echo e($borrowing->expected_return_date->format('M d, Y')); ?></p>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <?php if($borrowing->notes): ?>
                                        <div class="mt-3">
                                            <p class="text-sm text-gray-600">
                                                <span class="font-medium">Notes:</span> <?php echo e($borrowing->notes); ?>

                                            </p>
                                        </div>
                                    <?php endif; ?>

                                    <?php if($borrowing->status === 'rejected' && $borrowing->rejection_reason): ?>
                                        <div class="mt-3 p-3 bg-red-50 rounded-lg border border-red-200">
                                            <p class="text-sm text-red-800">
                                                <span class="font-medium">Rejection Reason:</span> <?php echo e($borrowing->rejection_reason); ?>

                                            </p>
                                        </div>
                                    <?php endif; ?>

                                    <?php if($borrowing->status === 'issued' && $borrowing->issued_date): ?>
                                        <div class="mt-3 p-3 bg-green-50 rounded-lg border border-green-200">
                                            <p class="text-sm text-green-800">
                                                <span class="font-medium">Issued on:</span> <?php echo e($borrowing->issued_date->format('M d, Y')); ?>

                                            </p>
                                        </div>
                                    <?php endif; ?>

                                    <?php if($borrowing->status === 'returned' && $borrowing->returned_date): ?>
                                        <div class="mt-3 p-3 bg-purple-50 rounded-lg border border-purple-200">
                                            <p class="text-sm text-purple-800">
                                                <span class="font-medium">Returned on:</span> <?php echo e($borrowing->returned_date->format('M d, Y')); ?>

                                            </p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex flex-wrap gap-2.5 lg:flex-col lg:items-end">
                            <?php if($borrowing->status === 'pending'): ?>
                                <form method="POST" action="<?php echo e(route('student.borrowings.cancel', $borrowing)); ?>" class="inline" x-data @submit.prevent="$dispatch('open-confirm-modal', { form: $el, title: 'Cancel Request', message: 'Are you sure you want to cancel this borrow request?', type: 'danger' })">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold rounded-xl bg-gradient-to-r from-red-500 to-rose-600 text-white hover:from-red-600 hover:to-rose-700 shadow-md shadow-red-200/50 hover:shadow-lg hover:shadow-red-300/50 transition-all duration-200 hover:-translate-y-0.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        Cancel Request
                                    </button>
                                </form>
                            <?php endif; ?>

                            <?php if($borrowing->status === 'issued'): ?>
                                <div class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl bg-gradient-to-r from-amber-50 to-orange-50 text-amber-700 ring-1 ring-amber-200/80 shadow-sm">
                                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Return by <?php echo e($borrowing->expected_return_date->format('M d, Y')); ?>

                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="p-12 text-center">
                    <div class="flex flex-col items-center justify-center">
                        <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">No borrowing history</h3>
                        <p class="text-gray-600 mb-4">You haven't made any borrowing requests yet.</p>
                        <a href="<?php echo e(route('student.borrow.form')); ?>" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-sm hover:shadow-md transition-all duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            Browse Items
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if($borrowings->hasPages()): ?>
            <div class="px-6 py-4 border-t border-gray-200">
                <?php echo e($borrowings->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Cedric\SEIMS\resources\views/student/borrowings/index.blade.php ENDPATH**/ ?>