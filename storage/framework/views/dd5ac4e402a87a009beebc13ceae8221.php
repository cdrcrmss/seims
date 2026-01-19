

<?php $__env->startSection('title', 'Borrowing Requests'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-8" x-data="{ statusFilter: '' }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white font-poppins">Borrowing Requests</h1>
            <p class="text-gray-600 dark:text-gray-400">Review and manage student borrowing requests</p>
        </div>
        <div class="flex items-center space-x-4">
            <!-- Status Filter with Alpine.js -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" type="button" 
                        class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-green-500 flex items-center justify-between min-w-[150px]">
                    <span x-text="statusFilter === '' ? 'All Status' : statusFilter.charAt(0).toUpperCase() + statusFilter.slice(1)">All Status</span>
                    <svg class="w-4 h-4 ml-2" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="open" 
                     @click.away="open = false"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 z-50">
                    <div class="py-1">
                        <button @click="statusFilter = ''; open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-green-50 dark:hover:bg-green-900/20 hover:text-green-600 dark:hover:text-green-400">All Status</button>
                        <button @click="statusFilter = 'pending'; open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-green-50 dark:hover:bg-green-900/20 hover:text-green-600 dark:hover:text-green-400">Pending</button>
                        <button @click="statusFilter = 'approved'; open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-green-50 dark:hover:bg-green-900/20 hover:text-green-600 dark:hover:text-green-400">Approved</button>
                        <button @click="statusFilter = 'issued'; open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-green-50 dark:hover:bg-green-900/20 hover:text-green-600 dark:hover:text-green-400">Issued</button>
                        <button @click="statusFilter = 'returned'; open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-green-50 dark:hover:bg-green-900/20 hover:text-green-600 dark:hover:text-green-400">Returned</button>
                        <button @click="statusFilter = 'rejected'; open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-green-50 dark:hover:bg-green-900/20 hover:text-green-600 dark:hover:text-green-400">Rejected</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
        <div class="glass rounded-2xl p-6 shadow-xl card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide">Pending</p>
                    <p class="text-3xl font-bold text-amber-600 dark:text-amber-400 font-poppins"><?php echo e($statusCounts['pending']); ?></p>
                </div>
                <div class="w-12 h-12 bg-amber-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="glass rounded-2xl p-6 shadow-xl card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide">Approved</p>
                    <p class="text-3xl font-bold text-teal-600 dark:text-teal-400 font-poppins"><?php echo e($statusCounts['approved']); ?></p>
                </div>
                <div class="w-12 h-12 bg-teal-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="glass rounded-2xl p-6 shadow-xl card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide">Issued</p>
                    <p class="text-3xl font-bold text-green-600 dark:text-green-400 font-poppins"><?php echo e($statusCounts['issued']); ?></p>
                </div>
                <div class="w-12 h-12 bg-green-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="glass rounded-2xl p-6 shadow-xl card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide">Returned</p>
                    <p class="text-3xl font-bold text-emerald-600 dark:text-emerald-400 font-poppins"><?php echo e($statusCounts['returned']); ?></p>
                </div>
                <div class="w-12 h-12 bg-emerald-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="glass rounded-2xl p-6 shadow-xl card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide">Rejected</p>
                    <p class="text-3xl font-bold text-red-600 dark:text-red-400 font-poppins"><?php echo e($statusCounts['rejected']); ?></p>
                </div>
                <div class="w-12 h-12 bg-red-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Borrowing Requests -->
    <div class="glass rounded-3xl shadow-xl overflow-hidden">
        <div class="p-6 border-b border-white/10">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">All Requests</h2>
                <div class="flex items-center space-x-4">
                    <!-- Search -->
                    <div class="relative">
                        <input type="text" id="searchInput" placeholder="Search requests..." 
                               class="pl-10 pr-4 py-2 bg-white/10 dark:bg-white/5 border border-white/20 dark:border-white/10 rounded-xl text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="divide-y divide-white/10">
            <?php $__empty_1 = true; $__currentLoopData = $borrowings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $borrowing): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="p-6 hover:bg-white/5 dark:hover:bg-white/5 transition-colors">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                        <!-- Request Info -->
                        <div class="flex-1">
                            <div class="flex items-start space-x-4">
                                <!-- Item Image -->
                                <div class="w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 rounded-xl flex items-center justify-center overflow-hidden flex-shrink-0">
                                    <?php if($borrowing->item->image_path): ?>
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
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white truncate"><?php echo e($borrowing->item->name); ?></h3>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            <?php echo e($borrowing->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800/20 dark:text-yellow-300' : 
                                               ($borrowing->status === 'approved' ? 'bg-blue-100 text-blue-800 dark:bg-blue-800/20 dark:text-blue-300' : 
                                                ($borrowing->status === 'issued' ? 'bg-green-100 text-green-800 dark:bg-green-800/20 dark:text-green-300' : 
                                                 ($borrowing->status === 'returned' ? 'bg-purple-100 text-purple-800 dark:bg-purple-800/20 dark:text-purple-300' : 
                                                  'bg-red-100 text-red-800 dark:bg-red-800/20 dark:text-red-300')))); ?>">
                                            <?php echo e(ucfirst($borrowing->status)); ?>

                                        </span>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
                                        <div>
                                            <p class="text-gray-500 dark:text-gray-400">Requested by</p>
                                            <p class="font-medium text-gray-900 dark:text-white"><?php echo e($borrowing->user->name); ?></p>
                                            <?php if($borrowing->user->student_id): ?>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">ID: <?php echo e($borrowing->user->student_id); ?></p>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <p class="text-gray-500 dark:text-gray-400">Quantity</p>
                                            <p class="font-medium text-gray-900 dark:text-white"><?php echo e($borrowing->quantity); ?></p>
                                        </div>
                                        <div>
                                            <p class="text-gray-500 dark:text-gray-400">Requested Date</p>
                                            <p class="font-medium text-gray-900 dark:text-white"><?php echo e($borrowing->created_at->format('M d, Y')); ?></p>
                                        </div>
                                        <?php if($borrowing->expected_return_date): ?>
                                            <div>
                                                <p class="text-gray-500 dark:text-gray-400">Expected Return</p>
                                                <p class="font-medium text-gray-900 dark:text-white"><?php echo e($borrowing->expected_return_date->format('M d, Y')); ?></p>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <?php if($borrowing->notes): ?>
                                        <div class="mt-3">
                                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                                <span class="font-medium">Notes:</span> <?php echo e($borrowing->notes); ?>

                                            </p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex flex-wrap gap-2 lg:flex-col lg:items-end">
                            <?php if($borrowing->status === 'pending'): ?>
                                <form method="POST" action="<?php echo e(route('staff.borrowings.approve', $borrowing)); ?>" class="inline">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PATCH'); ?>
                                    <button type="submit" class="px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 text-white text-sm font-medium rounded-lg hover:from-green-600 hover:to-green-700 transition-colors shadow-md">
                                        Approve
                                    </button>
                                </form>
                                <form method="POST" action="<?php echo e(route('staff.borrowings.reject', $borrowing)); ?>" class="inline">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PATCH'); ?>
                                    <button type="submit" class="px-4 py-2 bg-gradient-to-r from-red-500 to-red-600 text-white text-sm font-medium rounded-lg hover:from-red-600 hover:to-red-700 transition-colors shadow-md">
                                        Reject
                                    </button>
                                </form>
                            <?php elseif($borrowing->status === 'approved'): ?>
                                <form method="POST" action="<?php echo e(route('staff.borrowings.issue', $borrowing)); ?>" class="inline">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PATCH'); ?>
                                    <button type="submit" class="px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white text-sm font-medium rounded-lg hover:from-blue-600 hover:to-blue-700 transition-colors shadow-md">
                                        Issue Item
                                    </button>
                                </form>
                            <?php elseif($borrowing->status === 'issued'): ?>
                                <form method="POST" action="<?php echo e(route('staff.borrowings.return', $borrowing)); ?>" class="inline">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PATCH'); ?>
                                    <button type="submit" class="px-4 py-2 bg-gradient-to-r from-purple-500 to-purple-600 text-white text-sm font-medium rounded-lg hover:from-purple-600 hover:to-purple-700 transition-colors shadow-md">
                                        Mark Returned
                                    </button>
                                </form>
                            <?php endif; ?>

                            <button onclick="showBorrowingDetails(<?php echo e($borrowing->toJson()); ?>)" class="px-4 py-2 bg-white/10 dark:bg-white/5 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-white/20 dark:hover:bg-white/10 transition-colors">
                                Details
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="p-12 text-center">
                    <div class="flex flex-col items-center justify-center">
                        <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No borrowing requests</h3>
                        <p class="text-gray-600 dark:text-gray-400">No borrowing requests found matching your criteria.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if($borrowings->hasPages()): ?>
            <div class="px-6 py-4 border-t border-white/10">
                <?php echo e($borrowings->links()); ?>

            </div>
        <?php endif; ?>
    </div>

    <!-- Details Modal -->
    <div id="detailsModal" tabindex="-1" aria-hidden="true" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 transition-opacity bg-gray-900/80 backdrop-blur-sm" onclick="closeModal()"></div>
            
            <!-- Modal panel -->
            <div class="relative z-10 w-full max-w-2xl bg-white dark:bg-gray-800 rounded-3xl shadow-2xl">
                <div class="flex items-start justify-between p-6 border-b border-gray-200 dark:border-gray-700 rounded-t-3xl">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Borrowing Request Details</h3>
                    <button type="button" onclick="closeModal()" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                    </button>
                </div>
                <div id="modalContent" class="p-6">
                    <!-- Content will be populated by JavaScript -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function closeModal() {
        document.getElementById('detailsModal').classList.add('hidden');
    }

    function showBorrowingDetails(borrowing) {
        const content = `
            <div class="space-y-6">
                <!-- Item Info -->
                <div class="flex items-center space-x-4 p-4 bg-gray-100 dark:bg-gray-700 rounded-xl">
                    <div class="w-16 h-16 bg-gradient-to-br from-green-100 to-green-200 dark:from-green-800 dark:to-green-900 rounded-xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white">${borrowing.item.name}</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400">${borrowing.item.category}</p>
                    </div>
                </div>

                <!-- Request Details -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Requested by</label>
                        <p class="font-semibold text-gray-900 dark:text-white">${borrowing.user.name}</p>
                        ${borrowing.user.student_id ? `<p class="text-sm text-gray-500 dark:text-gray-400">ID: ${borrowing.user.student_id}</p>` : ''}
                    </div>
                    <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Email</label>
                        <p class="font-semibold text-gray-900 dark:text-white">${borrowing.user.email}</p>
                    </div>
                    <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Quantity</label>
                        <p class="font-semibold text-gray-900 dark:text-white">${borrowing.quantity}</p>
                    </div>
                    <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Status</label>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold
                            ${borrowing.status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300' : 
                              borrowing.status === 'approved' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300' : 
                              borrowing.status === 'issued' ? 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300' : 
                              borrowing.status === 'returned' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/50 dark:text-purple-300' : 
                              'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300'}">
                            ${borrowing.status.charAt(0).toUpperCase() + borrowing.status.slice(1)}
                        </span>
                    </div>
                    <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Request Date</label>
                        <p class="font-semibold text-gray-900 dark:text-white">${new Date(borrowing.created_at).toLocaleDateString()}</p>
                    </div>
                    ${borrowing.expected_return_date ? `
                        <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Expected Return</label>
                            <p class="font-semibold text-gray-900 dark:text-white">${new Date(borrowing.expected_return_date).toLocaleDateString()}</p>
                        </div>
                    ` : ''}
                </div>

                ${borrowing.notes ? `
                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Notes</label>
                        <p class="text-gray-900 dark:text-white">${borrowing.notes}</p>
                    </div>
                ` : ''}
            </div>
        `;
        
        document.getElementById('modalContent').innerHTML = content;
        document.getElementById('detailsModal').classList.remove('hidden');
    }

    // Simple search functionality
    document.getElementById('searchInput').addEventListener('input', function() {
        // This would typically be handled server-side with proper pagination
        // For now, we'll implement basic client-side search
        const searchTerm = this.value.toLowerCase();
        // Implementation would filter the displayed results
    });

    document.getElementById('statusFilter').addEventListener('change', function() {
        // This would typically trigger a page reload with the filter applied
        // For now, we'll implement basic client-side filtering
        const selectedStatus = this.value;
        // Implementation would filter the displayed results
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Cedric\innotrack\resources\views/staff/borrowings/index.blade.php ENDPATH**/ ?>