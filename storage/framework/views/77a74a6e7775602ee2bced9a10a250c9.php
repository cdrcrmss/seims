

<?php $__env->startSection('title', 'Borrow an Item'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6" x-data="borrowForm()">

    <!-- Header -->
    <div class="flex items-center gap-4 animate-fade-in-up">
        <a href="<?php echo e(route('student.borrowings.index')); ?>" class="p-2 rounded-xl hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 font-poppins">Borrow an Item</h1>
            <p class="text-sm text-gray-500">Browse available items and submit a borrowing request</p>
        </div>
    </div>

    <!-- Alerts -->
    <?php if($hasOverdue): ?>
    <div class="flex items-center gap-3 bg-red-50 rounded-xl p-4 ring-1 ring-red-200 animate-fade-in-up">
        <div class="w-9 h-9 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <p class="text-sm font-semibold text-red-800">You have overdue items!</p>
            <p class="text-xs text-red-600">Please return your overdue items before making new borrowing requests.</p>
        </div>
    </div>
    <?php endif; ?>

    <?php if($activeBorrowCount >= $maxItems): ?>
    <div class="flex items-center gap-3 bg-amber-50 rounded-xl p-4 ring-1 ring-amber-200 animate-fade-in-up">
        <div class="w-9 h-9 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <p class="text-sm font-semibold text-amber-800">Borrowing limit reached (<?php echo e($activeBorrowCount); ?>/<?php echo e($maxItems); ?>)</p>
            <p class="text-xs text-amber-600">Please wait for existing requests to be completed or cancel existing ones.</p>
        </div>
    </div>
    <?php endif; ?>

    <?php if($errors->has('rate_limit')): ?>
    <div class="bg-red-50 ring-1 ring-red-200 text-red-700 rounded-xl p-4 text-sm flex items-center gap-3 animate-fade-in-up">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <?php echo e($errors->first('rate_limit')); ?>

    </div>
    <?php endif; ?>

    <?php if($errors->any() && !$errors->has('rate_limit')): ?>
    <div class="bg-red-50 rounded-xl p-4 ring-1 ring-red-200 animate-fade-in-up">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div>
                <p class="text-sm font-semibold text-red-800 mb-1">Please fix the following:</p>
                <ul class="list-disc list-inside text-sm text-red-700 space-y-0.5">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($error !== $errors->first('rate_limit')): ?>
                            <li><?php echo e($error); ?></li>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
    <div class="bg-red-50 ring-1 ring-red-200 text-red-700 rounded-xl p-4 text-sm flex items-center gap-3 animate-fade-in-up">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <?php echo e(session('error')); ?>

    </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
        <!-- Left: Item Browsing (3 cols) -->
        <div class="lg:col-span-3 space-y-4 animate-fade-in-up stagger-1">
            <!-- Search & Filter -->
            <form method="GET" action="<?php echo e(route('student.borrow.form')); ?>" class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-4">
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1 relative">
                        <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" name="search" value="<?php echo e($search ?? ''); ?>"
                               placeholder="Search by name, description, or code..."
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-white">
                    </div>
                    <select name="category" onchange="this.form.submit()"
                            class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-700 focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-white sm:w-44">
                        <option value="">All Categories</option>
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($cat); ?>" <?php echo e(($category ?? '') === $cat ? 'selected' : ''); ?>><?php echo e($cat); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-xl transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        Search
                    </button>
                </div>
            </form>

            <!-- Result Count -->
            <div class="flex items-center justify-between px-1">
                <p class="text-xs text-gray-500">
                    Showing <span class="font-semibold text-gray-700"><?php echo e($availableItems->count()); ?></span> of <span class="font-semibold text-gray-700"><?php echo e($availableItems->total()); ?></span> items
                    <?php if($search): ?> for "<span class="font-semibold text-gray-700"><?php echo e($search); ?></span>" <?php endif; ?>
                    <?php if($category): ?> in <span class="font-semibold text-gray-700"><?php echo e($category); ?></span> <?php endif; ?>
                </p>
                <?php if($search || $category): ?>
                    <a href="<?php echo e(route('student.borrow.form')); ?>" class="text-xs text-green-600 hover:text-green-700 font-semibold">Clear filters</a>
                <?php endif; ?>
            </div>

            <!-- Item Cards Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <?php $__empty_1 = true; $__currentLoopData = $availableItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div @click="selectItem(<?php echo e($item->id); ?>, <?php echo e(json_encode($item->name)); ?>, <?php echo e(json_encode($item->category)); ?>, <?php echo e($item->available_stock); ?>)"
                     :class="selectedItemId == <?php echo e($item->id); ?> ? 'ring-2 ring-green-500 bg-green-50/60' : 'ring-1 ring-gray-200 hover:ring-green-300 hover:shadow-md'"
                     class="bg-white rounded-xl p-4 cursor-pointer transition-all duration-200 group relative">

                    
                    <div x-show="selectedItemId == <?php echo e($item->id); ?>" x-transition class="absolute top-3 right-3">
                        <span class="w-6 h-6 rounded-full bg-green-500 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </span>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center overflow-hidden flex-shrink-0">
                            <?php if($item->image_path): ?>
                                <img src="<?php echo e($item->image_url); ?>" alt="<?php echo e($item->name); ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            <?php endif; ?>
                        </div>
                        <div class="flex-1 min-w-0 pr-6">
                            <h3 class="text-sm font-semibold text-gray-900 truncate group-hover:text-green-700 transition-colors"><?php echo e($item->name); ?></h3>
                            <p class="text-xs text-gray-500"><?php echo e($item->category); ?></p>
                            <?php if($item->description): ?>
                                <p class="text-xs text-gray-400 mt-1 line-clamp-1"><?php echo e(Str::limit($item->description, 60)); ?></p>
                            <?php endif; ?>
                            <div class="flex items-center gap-3 mt-2">
                                <span class="inline-flex items-center gap-1 text-xs font-medium <?php echo e($item->available_stock > 5 ? 'text-green-600' : ($item->available_stock > 2 ? 'text-amber-600' : 'text-red-600')); ?>">
                                    <span class="w-1.5 h-1.5 rounded-full <?php echo e($item->available_stock > 5 ? 'bg-green-500' : ($item->available_stock > 2 ? 'bg-amber-500' : 'bg-red-500')); ?>"></span>
                                    <?php echo e($item->available_stock); ?> available
                                </span>
                                <?php if($item->asset_code): ?>
                                    <span class="text-[10px] text-gray-400 font-mono"><?php echo e($item->asset_code); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="sm:col-span-2 bg-white rounded-xl ring-1 ring-gray-200 p-12 text-center">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    <p class="text-sm font-medium text-gray-500">No items found</p>
                    <p class="text-xs text-gray-400 mt-1">Try adjusting your search or filters</p>
                    <?php if($search || $category): ?>
                        <a href="<?php echo e(route('student.borrow.form')); ?>" class="inline-flex items-center gap-1 text-xs text-green-600 hover:text-green-700 font-semibold mt-3">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            Clear filters
                        </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if($availableItems->hasPages()): ?>
            <div class="mt-2">
                <?php echo e($availableItems->links()); ?>

            </div>
            <?php endif; ?>
        </div>

        <!-- Right: Form + Sidebar (2 cols) -->
        <div class="lg:col-span-2 space-y-4 animate-fade-in-up stagger-2">
            <!-- Borrow Form Card -->
            <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm sticky top-6 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h2 class="text-base font-bold text-gray-900 font-poppins">Borrowing Request</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Select an item, then fill in the details</p>
                </div>

                <form method="POST" action="<?php echo e(route('student.borrow')); ?>" @submit="handleSubmit($event)" class="p-6 space-y-5">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="item_id" :value="selectedItemId">

                    <!-- Selected Item -->
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Selected Item</label>

                        <div x-show="!selectedItemId" class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center">
                            <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            <p class="text-sm text-gray-400 font-medium">No item selected</p>
                            <p class="text-xs text-gray-300 mt-0.5">Click an item from the list</p>
                        </div>

                        <div x-show="selectedItemId" x-cloak class="bg-green-50 ring-1 ring-green-200 rounded-xl p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-green-900" x-text="selectedItemName"></p>
                                        <p class="text-xs text-green-700" x-text="selectedItemCategory"></p>
                                    </div>
                                </div>
                                <button type="button" @click="clearSelection()" class="p-1.5 text-green-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                            <div class="mt-2 pt-2 border-t border-green-200 flex items-center justify-between">
                                <span class="text-xs text-green-600">Stock available</span>
                                <span class="text-sm font-bold text-green-700" x-text="selectedItemStock"></span>
                            </div>
                        </div>
                        <?php $__errorArgs = ['item_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-600 mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Quantity & Return Date -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="quantity" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Quantity</label>
                            <input type="number" id="quantity" name="quantity"
                                   x-model="quantity"
                                   :max="Math.min(selectedItemStock || 10, 10)"
                                   min="1" value="<?php echo e(old('quantity', 1)); ?>"
                                   class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                   :disabled="!selectedItemId">
                            <p class="text-[11px] text-gray-400 mt-1">Max: <span x-text="Math.min(selectedItemStock || 10, 10)"></span></p>
                            <?php $__errorArgs = ['quantity'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-600 mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div>
                            <label for="expected_return_date" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Return Date</label>
                            <input type="date" id="expected_return_date" name="expected_return_date"
                                   value="<?php echo e(old('expected_return_date')); ?>"
                                   min="<?php echo e(now()->addDay()->toDateString()); ?>"
                                   max="<?php echo e(now()->addDays($maxDays)->toDateString()); ?>"
                                   class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                   :disabled="!selectedItemId">
                            <p class="text-[11px] text-gray-400 mt-1">Within <?php echo e($maxDays); ?> days</p>
                            <?php $__errorArgs = ['expected_return_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-600 mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <!-- Purpose -->
                    <div>
                        <label for="purpose" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                            Purpose <span class="font-normal normal-case text-gray-400">(min 10 characters)</span>
                        </label>
                        <textarea id="purpose" name="purpose" rows="3"
                                  x-model="purpose" maxlength="500"
                                  placeholder="Describe why you need to borrow this item..."
                                  class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-green-500 focus:border-green-500 resize-none"
                                  :disabled="!selectedItemId"><?php echo e(old('purpose')); ?></textarea>
                        <div class="flex justify-between mt-1">
                            <?php $__errorArgs = ['purpose'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <span class="text-xs ml-auto" :class="purpose.length >= 10 ? 'text-green-600' : 'text-gray-400'" x-text="purpose.length + '/500'"></span>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div>
                        <label for="notes" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                            Additional Notes <span class="font-normal normal-case text-gray-400">(optional)</span>
                        </label>
                        <textarea id="notes" name="notes" rows="2"
                                  maxlength="500"
                                  placeholder="Any additional information..."
                                  class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-green-500 focus:border-green-500 resize-none"
                                  :disabled="!selectedItemId"><?php echo e(old('notes')); ?></textarea>
                    </div>

                    <!-- Info Bar -->
                    <div class="bg-gray-50 rounded-xl p-4 space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-500">Active Requests</span>
                            <div class="flex items-center gap-2">
                                <div class="w-16 bg-gray-200 rounded-full h-1.5">
                                    <div class="h-1.5 rounded-full <?php echo e($activeBorrowCount >= $maxItems ? 'bg-red-500' : 'bg-green-500'); ?>" style="width: <?php echo e(min(($activeBorrowCount / $maxItems) * 100, 100)); ?>%"></div>
                                </div>
                                <span class="text-xs font-bold <?php echo e($activeBorrowCount >= $maxItems ? 'text-red-600' : 'text-gray-900'); ?>"><?php echo e($activeBorrowCount); ?>/<?php echo e($maxItems); ?></span>
                            </div>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-500">Borrow Period</span>
                            <span class="text-xs font-bold text-gray-900"><?php echo e($maxDays); ?> days max</span>
                        </div>
                        <div class="flex justify-between items-center pt-1.5 border-t border-gray-200">
                            <span class="text-xs text-gray-400 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Rate limit
                            </span>
                            <span class="text-xs text-gray-400">3 requests / 5 min</span>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="flex gap-3 pt-1">
                        <button type="submit"
                                :disabled="!canSubmit || isSubmitting"
                                :class="canSubmit && !isSubmitting ? 'bg-green-600 hover:bg-green-700 shadow-sm hover:shadow-md' : 'bg-gray-300 cursor-not-allowed'"
                                class="flex-1 py-3 text-white font-semibold rounded-xl text-sm transition-all duration-200 flex items-center justify-center gap-2">
                            <template x-if="isSubmitting">
                                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            </template>
                            <template x-if="!isSubmitting">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </template>
                            <span x-text="isSubmitting ? 'Submitting...' : 'Submit Request'"></span>
                        </button>
                        <a href="<?php echo e(route('student.borrowings.index')); ?>" class="inline-flex items-center justify-center px-5 py-3 text-sm font-semibold text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 rounded-xl transition-colors">
                            Cancel
                        </a>
                    </div>

                    <p class="text-[10px] text-gray-400 text-center leading-relaxed">
                        By submitting, you agree to return the item in good condition by the expected return date.
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function borrowForm() {
    return {
        selectedItemId: <?php echo e(old('item_id', $selectedItem?->id ?? 'null')); ?>,
        selectedItemName: <?php echo json_encode(old('item_id') ? (\App\Models\Item::find(old('item_id'))?->name ?? '') : ($selectedItem?->name ?? '')); ?>,
        selectedItemCategory: <?php echo json_encode(old('item_id') ? (\App\Models\Item::find(old('item_id'))?->category ?? '') : ($selectedItem?->category ?? '')); ?>,
        selectedItemStock: <?php echo e(old('item_id') ? (\App\Models\Item::find(old('item_id'))?->available_stock ?? 0) : ($selectedItem?->available_stock ?? 0)); ?>,
        quantity: <?php echo e(old('quantity', 1)); ?>,
        purpose: <?php echo json_encode(old('purpose', '')); ?>,
        isSubmitting: false,

        get canSubmit() {
            return this.selectedItemId &&
                   this.quantity > 0 &&
                   this.purpose.length >= 10 &&
                   !<?php echo e($hasOverdue ? 'true' : 'false'); ?> &&
                   <?php echo e($activeBorrowCount); ?> < <?php echo e($maxItems); ?>;
        },

        selectItem(id, name, category, stock) {
            if (<?php echo e($hasOverdue ? 'true' : 'false'); ?> || <?php echo e($activeBorrowCount); ?> >= <?php echo e($maxItems); ?>) return;

            this.selectedItemId = id;
            this.selectedItemName = name;
            this.selectedItemCategory = category;
            this.selectedItemStock = stock;

            if (this.quantity > Math.min(stock, 10)) {
                this.quantity = Math.min(stock, 10);
            }
        },

        clearSelection() {
            this.selectedItemId = null;
            this.selectedItemName = '';
            this.selectedItemCategory = '';
            this.selectedItemStock = 0;
            this.quantity = 1;
        },

        handleSubmit(event) {
            if (!this.canSubmit || this.isSubmitting) {
                event.preventDefault();
                return;
            }
            this.isSubmitting = true;
        }
    }
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Cedric\SEIMS\resources\views/student/borrowings/borrow.blade.php ENDPATH**/ ?>