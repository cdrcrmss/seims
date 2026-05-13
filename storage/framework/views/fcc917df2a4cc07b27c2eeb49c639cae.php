

<?php $__env->startSection('title', 'Borrowing Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 animate-fade-in-up">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-poppins"><?php echo e($archived === '1' ? 'Archived Borrowings' : 'Borrowing Management'); ?></h1>
            <p class="text-gray-600"><?php echo e($archived === '1' ? 'View archived borrowing records' : 'View and manage all borrowing transactions'); ?></p>
        </div>
        <div>
            <?php if($archived === '1'): ?>
                <a href="<?php echo e(route('admin.borrowings')); ?>" class="inline-flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/></svg>
                    Back to Active
                </a>
            <?php else: ?>
                <a href="<?php echo e(route('admin.borrowings', ['archived' => '1'])); ?>" class="inline-flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                    View Archives
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-6 animate-fade-in-up stagger-1 relative z-10">
        <form method="GET" action="<?php echo e(route('admin.borrowings')); ?>" class="flex flex-col md:flex-row gap-4">
            <?php if($archived === '1'): ?><input type="hidden" name="archived" value="1"><?php endif; ?>
            <div class="flex-1 relative" x-data="borrowingSearchComponent()" @click.away="showSuggestions = false">
                <div class="relative">
                    <input type="text" name="search" value="<?php echo e($search); ?>" placeholder="Search by user or item name..."
                        x-model="query"
                        @input="filterRows(); updateSuggestions()"
                        @focus="if(query.length > 0) showSuggestions = true"
                        @keydown.escape="showSuggestions = false"
                        @keydown.arrow-down.prevent="highlightNext()"
                        @keydown.arrow-up.prevent="highlightPrev()"
                        @keydown.enter.prevent="selectHighlighted()"
                        autocomplete="off"
                        class="w-full pl-10 pr-8 py-2.5 rounded-xl ring-1 ring-gray-200 focus:ring-2 focus:ring-green-500 focus:outline-none text-sm">
                    <svg class="absolute left-3 top-3 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <button x-show="query.length > 0" @click="query = ''; filterRows(); showSuggestions = false" type="button" class="absolute right-3 top-3 text-gray-400 hover:text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Suggestions Dropdown -->
                <div x-show="showSuggestions && suggestions.length > 0" 
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute left-0 mt-2 w-full max-w-sm bg-white rounded-xl shadow-lg border border-gray-200 z-50 max-h-60 overflow-y-auto">
                    <template x-for="(suggestion, index) in suggestions" :key="index">
                        <button type="button" @click="selectSuggestion(suggestion)" 
                                :class="{ 'bg-green-50': highlightedIndex === index }"
                                class="w-full text-left px-4 py-2.5 text-sm hover:bg-gray-50 transition-colors flex items-center gap-3 border-b border-gray-50 last:border-0">
                            <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate" x-text="suggestion.name"></p>
                                <p class="text-xs text-gray-500 truncate" x-text="suggestion.detail"></p>
                            </div>
                        </button>
                    </template>
                </div>
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
                ['label' => 'Pending', 'count' => $allBorrowings->where('status', 'pending')->count(), 'color' => 'yellow', 'status' => 'pending'],
                ['label' => 'Approved', 'count' => $allBorrowings->where('status', 'approved')->count(), 'color' => 'blue', 'status' => 'approved'],
                ['label' => 'Issued', 'count' => $allBorrowings->where('status', 'issued')->count(), 'color' => 'green', 'status' => 'issued'],
                ['label' => 'Returned', 'count' => $allBorrowings->where('status', 'returned')->count(), 'color' => 'gray', 'status' => 'returned'],
                ['label' => 'Overdue', 'count' => $allBorrowings->where('status', 'issued')->where('expected_return_date', '<', now())->count(), 'color' => 'red', 'status' => 'issued'],
            ];
        ?>
        <?php $__currentLoopData = $stats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route('admin.borrowings', ['status' => $stat['status']])); ?>" class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-4 text-center border-l-4 border-<?php echo e($stat['color']); ?>-500 hover:ring-<?php echo e($stat['color']); ?>-300 transition-all cursor-pointer block">
            <p class="text-2xl font-bold text-gray-900 font-poppins"><?php echo e($stat['count']); ?></p>
            <p class="text-xs text-gray-500 uppercase font-semibold"><?php echo e($stat['label']); ?></p>
        </a>
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
                        <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wide">Actions</th>
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
                    <tr class="hover:bg-gray-50 transition-colors borrowing-row" data-search="<?php echo e(strtolower(($borrowing->user?->name ?? '') . ' ' . ($borrowing->item?->name ?? '') . ' ' . $borrowing->status . ' ' . ($borrowing->user?->role ?? ''))); ?>">
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
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1.5">
                                <?php if($borrowing->status === 'pending'): ?>
                                    <form method="POST" action="<?php echo e(route('staff.borrowings.approve', $borrowing)); ?>" class="inline">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PATCH'); ?>
                                        <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg bg-green-600 text-white hover:bg-green-700 transition-colors">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            Approve
                                        </button>
                                    </form>
                                    <form method="POST" action="<?php echo e(route('staff.borrowings.reject', $borrowing)); ?>" class="inline" onsubmit="return confirm('Are you sure you want to reject this request?')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PATCH'); ?>
                                        <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg text-red-700 bg-red-50 hover:bg-red-100 ring-1 ring-red-200/60 transition-colors">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            Reject
                                        </button>
                                    </form>
                                <?php elseif($borrowing->status === 'approved'): ?>
                                    <form method="POST" action="<?php echo e(route('staff.borrowings.issue', $borrowing)); ?>" class="inline">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PATCH'); ?>
                                        <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition-colors">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            Issue
                                        </button>
                                    </form>
                                <?php elseif($borrowing->status === 'issued'): ?>
                                    <form method="POST" action="<?php echo e(route('staff.borrowings.return', $borrowing)); ?>" class="inline">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PATCH'); ?>
                                        <input type="hidden" name="return_condition" value="good">
                                        <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg bg-purple-600 text-white hover:bg-purple-700 transition-colors">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                            Return
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <?php if($archived === '1'): ?>
                                    <form method="POST" action="<?php echo e(route('admin.borrowings.unarchive', $borrowing)); ?>" class="inline">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PATCH'); ?>
                                        <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg text-blue-700 bg-blue-50 hover:bg-blue-100 ring-1 ring-blue-200/60 transition-colors">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                            Restore
                                        </button>
                                    </form>
                                <?php elseif(in_array($borrowing->status, ['returned', 'rejected', 'cancelled'])): ?>
                                    <form method="POST" action="<?php echo e(route('admin.borrowings.archive', $borrowing)); ?>" class="inline">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PATCH'); ?>
                                        <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg text-gray-600 bg-gray-50 hover:bg-gray-100 ring-1 ring-gray-200/60 transition-colors">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                                            Archive
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
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
            <?php echo e($borrowings->appends(['status' => $status, 'search' => $search, 'archived' => $archived])->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>

<script>
    function borrowingSearchComponent() {
        return {
            query: '<?php echo e($search); ?>',
            suggestions: [],
            showSuggestions: false,
            highlightedIndex: -1,

            filterRows() {
                const q = this.query.toLowerCase().trim();
                document.querySelectorAll('.borrowing-row').forEach(row => {
                    const data = row.getAttribute('data-search') || '';
                    row.style.display = (q === '' || data.includes(q)) ? '' : 'none';
                });
            },

            updateSuggestions() {
                const q = this.query.toLowerCase().trim();
                if (q.length === 0) {
                    this.suggestions = [];
                    this.showSuggestions = false;
                    return;
                }

                const seen = new Set();
                const results = [];
                document.querySelectorAll('.borrowing-row').forEach(row => {
                    const data = row.getAttribute('data-search') || '';
                    if (data.includes(q)) {
                        const userEl = row.querySelector('td:first-child .font-semibold');
                        const itemEl = row.querySelector('td:nth-child(2) .font-medium');
                        const user = userEl ? userEl.textContent.trim() : '';
                        const item = itemEl ? itemEl.textContent.trim() : '';
                        const key = user + '|' + item;
                        if (!seen.has(key)) {
                            seen.add(key);
                            results.push({ name: user, detail: item });
                        }
                    }
                });
                this.suggestions = results.slice(0, 6);
                this.showSuggestions = results.length > 0;
                this.highlightedIndex = -1;
            },

            selectSuggestion(suggestion) {
                this.query = suggestion.name;
                this.showSuggestions = false;
                this.filterRows();
            },

            highlightNext() {
                if (this.suggestions.length === 0) return;
                this.highlightedIndex = (this.highlightedIndex + 1) % this.suggestions.length;
            },

            highlightPrev() {
                if (this.suggestions.length === 0) return;
                this.highlightedIndex = this.highlightedIndex <= 0 ? this.suggestions.length - 1 : this.highlightedIndex - 1;
            },

            selectHighlighted() {
                if (this.highlightedIndex >= 0 && this.highlightedIndex < this.suggestions.length) {
                    this.selectSuggestion(this.suggestions[this.highlightedIndex]);
                }
            }
        };
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Cedric\SEIMS\resources\views/admin/borrowings/index.blade.php ENDPATH**/ ?>