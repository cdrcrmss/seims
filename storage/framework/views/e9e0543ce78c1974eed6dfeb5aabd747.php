

<?php $__env->startSection('title', 'Borrowing Requests'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-8" x-data="{ statusFilter: '' }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-poppins">Borrowing Requests</h1>
            <p class="text-gray-600">Review and manage student borrowing requests</p>
        </div>
        <div class="flex items-center space-x-4">
            <!-- Status Filter with Alpine.js -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" type="button" 
                        class="px-4 py-2 bg-white border border-gray-300 rounded-xl text-gray-900 focus:ring-2 focus:ring-green-500 focus:border-green-500 flex items-center justify-between min-w-[150px]">
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
                     class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-200 z-50">
                    <div class="py-1">
                        <button @click="statusFilter = ''; open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-600">All Status</button>
                        <button @click="statusFilter = 'pending'; open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-600">Pending</button>
                        <button @click="statusFilter = 'approved'; open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-600">Approved</button>
                        <button @click="statusFilter = 'issued'; open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-600">Issued</button>
                        <button @click="statusFilter = 'returned'; open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-600">Returned</button>
                        <button @click="statusFilter = 'rejected'; open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-600">Rejected</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
        <div class="bg-white rounded-2xl p-6 ring-1 ring-gray-100 shadow-sm card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Pending</p>
                    <p class="text-3xl font-bold text-amber-600 font-poppins"><?php echo e($statusCounts['pending']); ?></p>
                </div>
                <div class="w-12 h-12 bg-amber-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 ring-1 ring-gray-100 shadow-sm card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Approved</p>
                    <p class="text-3xl font-bold text-teal-600 font-poppins"><?php echo e($statusCounts['approved']); ?></p>
                </div>
                <div class="w-12 h-12 bg-teal-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 ring-1 ring-gray-100 shadow-sm card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Issued</p>
                    <p class="text-3xl font-bold text-green-600 font-poppins"><?php echo e($statusCounts['issued']); ?></p>
                </div>
                <div class="w-12 h-12 bg-green-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 ring-1 ring-gray-100 shadow-sm card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Returned</p>
                    <p class="text-3xl font-bold text-emerald-600 font-poppins"><?php echo e($statusCounts['returned']); ?></p>
                </div>
                <div class="w-12 h-12 bg-emerald-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 ring-1 ring-gray-100 shadow-sm card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Rejected</p>
                    <p class="text-3xl font-bold text-red-600 font-poppins"><?php echo e($statusCounts['rejected']); ?></p>
                </div>
                <div class="w-12 h-12 bg-red-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Borrowing Requests -->
    <div class="bg-white rounded-2xl ring-1 ring-gray-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <h2 class="text-xl font-bold text-gray-900">All Requests</h2>
                <div class="flex items-center space-x-4">
                    <!-- Search -->
                    <div class="relative">
                        <input type="text" id="searchInput" placeholder="Search requests..." 
                               class="pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
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

                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
                                        <div>
                                            <p class="text-gray-500">Requested by</p>
                                            <p class="font-medium text-gray-900"><?php echo e($borrowing->user?->name ?? 'Unknown User'); ?></p>
                                            <?php if($borrowing->user?->student_id): ?>
                                                <p class="text-xs text-gray-500">ID: <?php echo e($borrowing->user->student_id); ?></p>
                                            <?php endif; ?>
                                        </div>
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
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex flex-wrap gap-2 lg:flex-col lg:items-end">
                            <?php if($borrowing->status === 'pending'): ?>
                                <div class="flex items-center gap-2">
                                    <form method="POST" action="<?php echo e(route('staff.borrowings.approve', $borrowing)); ?>" class="inline">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PATCH'); ?>
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg bg-green-600 text-white hover:bg-green-700 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            Approve
                                        </button>
                                    </form>
                                    <form method="POST" action="<?php echo e(route('staff.borrowings.reject', $borrowing)); ?>" class="inline" x-data @submit.prevent="$dispatch('open-confirm-modal', { form: $el, title: 'Reject Request', message: 'Are you sure you want to reject this borrow request?', type: 'danger' })">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PATCH'); ?>
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg text-red-700 bg-red-50 hover:bg-red-100 ring-1 ring-red-200/60 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            Reject
                                        </button>
                                    </form>
                                </div>
                            <?php elseif($borrowing->status === 'approved'): ?>
                                <form method="POST" action="<?php echo e(route('staff.borrowings.issue', $borrowing)); ?>" class="inline">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PATCH'); ?>
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Issue Item
                                    </button>
                                </form>
                            <?php elseif($borrowing->status === 'issued'): ?>
                                <button type="button" 
                                        onclick="openReturnModal(<?php echo e($borrowing->id); ?>, '<?php echo e(addslashes($borrowing->item?->name ?? 'Item')); ?>', '<?php echo e(addslashes($borrowing->user?->name ?? 'User')); ?>')" 
                                        class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg bg-purple-600 text-white hover:bg-purple-700 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                    Mark Returned
                                </button>
                            <?php endif; ?>

                            <?php if($borrowing->status === 'returned' && $borrowing->return_condition): ?>
                                <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-md
                                    <?php echo e($borrowing->return_condition === 'good' ? 'bg-green-50 text-green-700' : 
                                       ($borrowing->return_condition === 'fair' ? 'bg-yellow-50 text-yellow-700' : 
                                        ($borrowing->return_condition === 'needs_repair' ? 'bg-orange-50 text-orange-700' : 
                                         'bg-red-50 text-red-700'))); ?>">
                                    <span class="w-1.5 h-1.5 rounded-full 
                                        <?php echo e($borrowing->return_condition === 'good' ? 'bg-green-500' : 
                                           ($borrowing->return_condition === 'fair' ? 'bg-yellow-500' : 
                                            ($borrowing->return_condition === 'needs_repair' ? 'bg-orange-500' : 'bg-red-500'))); ?>"></span>
                                    <?php echo e(ucfirst(str_replace('_', ' ', $borrowing->return_condition))); ?>

                                </span>
                            <?php endif; ?>

                            <button onclick="showBorrowingDetails(<?php echo e($borrowing->toJson()); ?>)" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg text-gray-600 bg-gray-50 hover:bg-gray-100 ring-1 ring-gray-200/60 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
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
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">No borrowing requests</h3>
                        <p class="text-gray-600">No borrowing requests found matching your criteria.</p>
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

    <!-- Details Modal -->
    <div id="detailsModal" tabindex="-1" aria-hidden="true" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" onclick="closeModal()"></div>
            
            <div class="relative z-10 w-full max-w-lg bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900">Request Details</h3>
                    <button type="button" onclick="closeModal()" class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div id="modalContent" class="px-6 py-5">
                    <!-- Content populated by JS -->
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
        const statusMap = {
            pending: { bg: 'bg-amber-50', text: 'text-amber-700', dot: 'bg-amber-500' },
            approved: { bg: 'bg-blue-50', text: 'text-blue-700', dot: 'bg-blue-500' },
            issued: { bg: 'bg-green-50', text: 'text-green-700', dot: 'bg-green-500' },
            returned: { bg: 'bg-purple-50', text: 'text-purple-700', dot: 'bg-purple-500' },
            rejected: { bg: 'bg-red-50', text: 'text-red-700', dot: 'bg-red-500' },
        };
        const s = statusMap[borrowing.status] || statusMap.pending;
        
        const itemName = borrowing.item ? borrowing.item.name : 'Deleted Item';
        const itemCategory = borrowing.item ? borrowing.item.category : '—';
        const userName = borrowing.user ? borrowing.user.name : 'Unknown User';
        const userEmail = borrowing.user ? borrowing.user.email : '—';
        const studentId = borrowing.user && borrowing.user.student_id ? borrowing.user.student_id : null;

        const content = `
            <div class="space-y-5">
                <!-- Item & Status -->
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-base font-bold text-gray-900">${itemName}</h4>
                        <p class="text-xs text-gray-500">${itemCategory}</p>
                    </div>
                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-md ${s.bg} ${s.text}">
                        <span class="w-1.5 h-1.5 rounded-full ${s.dot}"></span>
                        ${borrowing.status.charAt(0).toUpperCase() + borrowing.status.slice(1)}
                    </span>
                </div>

                <hr class="border-gray-100">

                <!-- Student Info -->
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Student Information</p>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <p class="text-xs text-gray-500">Name</p>
                            <p class="text-sm font-semibold text-gray-900">${userName}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Email</p>
                            <p class="text-sm font-semibold text-gray-900 truncate">${userEmail}</p>
                        </div>
                        ${studentId ? `
                        <div>
                            <p class="text-xs text-gray-500">Student ID</p>
                            <p class="text-sm font-semibold text-gray-900">${studentId}</p>
                        </div>` : ''}
                    </div>
                </div>

                <hr class="border-gray-100">

                <!-- Borrowing Info -->
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Borrowing Details</p>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <p class="text-xs text-gray-500">Quantity</p>
                            <p class="text-sm font-semibold text-gray-900">${borrowing.quantity}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Request Date</p>
                            <p class="text-sm font-semibold text-gray-900">${new Date(borrowing.created_at).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'})}</p>
                        </div>
                        ${borrowing.expected_return_date ? `
                        <div>
                            <p class="text-xs text-gray-500">Expected Return</p>
                            <p class="text-sm font-semibold text-gray-900">${new Date(borrowing.expected_return_date).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'})}</p>
                        </div>` : ''}
                        ${borrowing.approved_date ? `
                        <div>
                            <p class="text-xs text-gray-500">Approved Date</p>
                            <p class="text-sm font-semibold text-gray-900">${new Date(borrowing.approved_date).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'})}</p>
                        </div>` : ''}
                        ${borrowing.issued_date ? `
                        <div>
                            <p class="text-xs text-gray-500">Issued Date</p>
                            <p class="text-sm font-semibold text-gray-900">${new Date(borrowing.issued_date).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'})}</p>
                        </div>` : ''}
                        ${borrowing.returned_date ? `
                        <div>
                            <p class="text-xs text-gray-500">Returned Date</p>
                            <p class="text-sm font-semibold text-gray-900">${new Date(borrowing.returned_date).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'})}</p>
                        </div>` : ''}
                        ${borrowing.return_condition ? `
                        <div>
                            <p class="text-xs text-gray-500">Return Condition</p>
                            <p class="text-sm font-semibold text-gray-900">${borrowing.return_condition.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())}</p>
                        </div>` : ''}
                    </div>
                </div>

                ${borrowing.notes ? `
                <hr class="border-gray-100">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Notes</p>
                    <p class="text-sm text-gray-700 leading-relaxed">${borrowing.notes}</p>
                </div>` : ''}

                ${borrowing.rejection_reason ? `
                <div class="bg-red-50 rounded-lg p-3 mt-2">
                    <p class="text-xs font-semibold text-red-700 mb-0.5">Rejection Reason</p>
                    <p class="text-sm text-red-600">${borrowing.rejection_reason}</p>
                </div>` : ''}

                ${borrowing.return_notes ? `
                <div class="bg-gray-50 rounded-lg p-3 mt-2">
                    <p class="text-xs font-semibold text-gray-600 mb-0.5">Return Notes</p>
                    <p class="text-sm text-gray-700">${borrowing.return_notes}</p>
                </div>` : ''}
            </div>
        `;
        
        document.getElementById('modalContent').innerHTML = content;
        document.getElementById('detailsModal').classList.remove('hidden');
    }

    // Return Modal Functions
    function openReturnModal(borrowingId, itemName, userName) {
        document.getElementById('returnBorrowingId').value = borrowingId;
        document.getElementById('returnForm').action = `/staff/borrowings/${borrowingId}/return`;
        document.getElementById('returnItemName').textContent = itemName || 'Item';
        document.getElementById('returnUserName').textContent = userName || 'User';
        document.getElementById('returnModal').classList.remove('hidden');
        // Reset radio buttons
        document.querySelectorAll('#returnForm input[name="return_condition"]').forEach(r => r.checked = false);
        document.getElementById('return_notes').value = '';
    }

    function closeReturnModal() {
        document.getElementById('returnModal').classList.add('hidden');
    }
</script>

<!-- Return Modal -->
<div id="returnModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 py-8">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" onclick="closeReturnModal()"></div>
        
        <div class="relative z-10 w-full max-w-sm bg-white rounded-2xl shadow-xl overflow-hidden">
            <form id="returnForm" method="POST" action="">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PATCH'); ?>
                <input type="hidden" id="returnBorrowingId" name="borrowing_id" value="">
                
                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900">Return Item</h3>
                    <p class="text-xs text-gray-500 mt-0.5">
                        <span id="returnItemName" class="font-medium text-gray-700"></span>
                        &middot; by <span id="returnUserName" class="font-medium text-gray-700"></span>
                    </p>
                </div>
                
                <div class="px-6 py-5 space-y-4">
                    <!-- Condition -->
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Item Condition</label>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="cursor-pointer">
                                <input type="radio" name="return_condition" value="good" class="peer hidden" required>
                                <div class="peer-checked:ring-2 peer-checked:ring-green-500 peer-checked:bg-green-50 border border-gray-200 rounded-xl p-3 text-center transition-all hover:border-green-300">
                                    <svg class="w-6 h-6 mx-auto mb-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span class="text-xs font-semibold text-gray-700">Good</span>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="return_condition" value="fair" class="peer hidden">
                                <div class="peer-checked:ring-2 peer-checked:ring-yellow-500 peer-checked:bg-yellow-50 border border-gray-200 rounded-xl p-3 text-center transition-all hover:border-yellow-300">
                                    <svg class="w-6 h-6 mx-auto mb-1 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span class="text-xs font-semibold text-gray-700">Fair</span>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="return_condition" value="needs_repair" class="peer hidden">
                                <div class="peer-checked:ring-2 peer-checked:ring-orange-500 peer-checked:bg-orange-50 border border-gray-200 rounded-xl p-3 text-center transition-all hover:border-orange-300">
                                    <svg class="w-6 h-6 mx-auto mb-1 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span class="text-xs font-semibold text-gray-700">Needs Repair</span>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="return_condition" value="damaged" class="peer hidden">
                                <div class="peer-checked:ring-2 peer-checked:ring-red-500 peer-checked:bg-red-50 border border-gray-200 rounded-xl p-3 text-center transition-all hover:border-red-300">
                                    <svg class="w-6 h-6 mx-auto mb-1 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                    <span class="text-xs font-semibold text-gray-700">Damaged</span>
                                </div>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Notes -->
                    <div>
                        <label for="return_notes" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Notes <span class="font-normal normal-case text-gray-400">(optional)</span></label>
                        <textarea id="return_notes" name="return_notes" rows="2" 
                                  class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 resize-none"
                                  placeholder="Any observations about the item..."></textarea>
                    </div>
                </div>
                
                <!-- Footer -->
                <div class="flex gap-2 px-6 py-4 bg-gray-50 border-t border-gray-100">
                    <button type="button" onclick="closeReturnModal()" 
                            class="flex-1 px-4 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 rounded-xl transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="flex-1 inline-flex items-center justify-center gap-1.5 bg-green-600 hover:bg-green-700 text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Confirm Return
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Cedric\SEIMS\resources\views/staff/borrowings/index.blade.php ENDPATH**/ ?>