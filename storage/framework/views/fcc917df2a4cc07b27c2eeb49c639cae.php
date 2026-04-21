

<?php $__env->startSection('title', 'Borrowing Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 animate-fade-in-up">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-poppins">Borrowing Management</h1>
            <p class="text-gray-600">View and manage all borrowing transactions</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-6 animate-fade-in-up stagger-1">
        <form method="GET" action="<?php echo e(route('admin.borrowings')); ?>" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="<?php echo e($search); ?>" placeholder="Search by user or item name..."
                    class="w-full px-4 py-2.5 rounded-xl ring-1 ring-gray-200 focus:ring-2 focus:ring-green-500 focus:outline-none text-sm">
            </div>
            <div>
                <select name="status" class="w-full md:w-48 px-4 py-2.5 rounded-xl ring-1 ring-gray-200 focus:ring-2 focus:ring-green-500 focus:outline-none text-sm">
                    <option value="">All Statuses</option>
                    <option value="pending" <?php echo e($status === 'pending' ? 'selected' : ''); ?>>Pending</option>
                    <option value="approved" <?php echo e($status === 'approved' ? 'selected' : ''); ?>>Approved</option>
                    <option value="issued" <?php echo e($status === 'issued' ? 'selected' : ''); ?>>Issued</option>
                    <option value="returned" <?php echo e($status === 'returned' ? 'selected' : ''); ?>>Returned</option>
                    <option value="cancelled" <?php echo e($status === 'cancelled' ? 'selected' : ''); ?>>Cancelled</option>
                    <option value="rejected" <?php echo e($status === 'rejected' ? 'selected' : ''); ?>>Rejected</option>
                </select>
            </div>
            <button type="submit" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-sm hover:shadow-md transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                Filter
            </button>
            <?php if($search || $status): ?>
            <a href="<?php echo e(route('admin.borrowings')); ?>" class="inline-flex items-center px-5 py-2.5 text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all duration-200">
                Clear
            </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 animate-fade-in-up stagger-1">
        <?php
            $allBorrowings = \App\Models\Borrowing::all();
            $stats = [
                ['label' => 'Pending', 'count' => $allBorrowings->where('status', 'pending')->count(), 'color' => 'yellow'],
                ['label' => 'Approved', 'count' => $allBorrowings->where('status', 'approved')->count(), 'color' => 'blue'],
                ['label' => 'Issued', 'count' => $allBorrowings->where('status', 'issued')->count(), 'color' => 'green'],
                ['label' => 'Returned', 'count' => $allBorrowings->where('status', 'returned')->count(), 'color' => 'gray'],
                ['label' => 'Overdue', 'count' => $allBorrowings->where('status', 'issued')->where('expected_return_date', '<', now())->count(), 'color' => 'red'],
            ];
        ?>
        <?php $__currentLoopData = $stats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-4 text-center border-l-4 border-<?php echo e($stat['color']); ?>-500">
            <p class="text-2xl font-bold text-gray-900 font-poppins"><?php echo e($stat['count']); ?></p>
            <p class="text-xs text-gray-500 uppercase font-semibold"><?php echo e($stat['label']); ?></p>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <!-- Borrowings Table -->
    <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm overflow-hidden animate-fade-in-up stagger-2">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wide">User</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wide">Item</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wide">Qty</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wide">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wide">Requested</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wide">Return Date</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wide">Notes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $borrowings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $borrowing): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $isOverdue = $borrowing->status === 'issued' && $borrowing->expected_return_date && $borrowing->expected_return_date < now();
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-700',
                            'approved' => 'bg-blue-100 text-blue-700',
                            'issued' => $isOverdue ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700',
                            'returned' => 'bg-gray-100 text-gray-600',
                            'cancelled' => 'bg-gray-100 text-gray-500',
                            'rejected' => 'bg-red-100 text-red-600',
                        ];
                    ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div>
                                <p class="font-semibold text-gray-900 text-sm"><?php echo e($borrowing->user?->name ?? 'N/A'); ?></p>
                                <p class="text-xs text-gray-500"><?php echo e(ucfirst($borrowing->user?->role ?? '')); ?></p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-900 text-sm"><?php echo e($borrowing->item?->name ?? 'N/A'); ?></p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700"><?php echo e($borrowing->quantity); ?></td>
                        <td class="px-6 py-4">
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-lg <?php echo e($statusColors[$borrowing->status] ?? 'bg-gray-100 text-gray-600'); ?>">
                                <?php echo e($isOverdue ? 'Overdue' : ucfirst($borrowing->status)); ?>

                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600"><?php echo e($borrowing->requested_date ? \Carbon\Carbon::parse($borrowing->requested_date)->format('M d, Y') : 'N/A'); ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <?php echo e($borrowing->expected_return_date ? \Carbon\Carbon::parse($borrowing->expected_return_date)->format('M d, Y') : 'N/A'); ?>

                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 max-w-[200px] truncate"><?php echo e($borrowing->notes ?? '—'); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                            <p>No borrowing records found</p>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($borrowings->hasPages()): ?>
        <div class="px-6 py-4 border-t border-gray-200">
            <?php echo e($borrowings->appends(['status' => $status, 'search' => $search])->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Cedric\SEIMS\resources\views/admin/borrowings/index.blade.php ENDPATH**/ ?>