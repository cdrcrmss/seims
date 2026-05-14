

<?php $__env->startSection('title', 'Schedule Maintenance'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-8">
    <!-- Header -->
    <div class="flex items-center space-x-4 animate-fade-in-up">
        <a href="<?php echo e(route('maintenance.index')); ?>" class="p-2 rounded-xl hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-poppins">Schedule Maintenance</h1>
            <p class="text-gray-600">Create a new maintenance record for equipment</p>
        </div>
    </div>

    <div class="max-w-2xl">
        <form method="POST" action="<?php echo e(route('maintenance.store')); ?>" class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-8 space-y-6 animate-fade-in-up stagger-1">
            <?php echo csrf_field(); ?>

            <!-- Equipment -->
            <div>
                <label for="item_id" class="block text-sm font-semibold text-gray-700 mb-2">Equipment</label>
                <select name="item_id" id="item_id" class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all" required>
                    <option value="">Select equipment...</option>
                    <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($item->id); ?>" <?php echo e(old('item_id') == $item->id ? 'selected' : ''); ?>>
                            <?php echo e($item->name); ?> — <?php echo e($item->category); ?> (Wear: <?php echo e($item->wear_level ?? 0); ?>%)
                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['item_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- Maintenance Type -->
            <div>
                <label for="maintenance_type" class="block text-sm font-semibold text-gray-700 mb-2">Maintenance Type</label>
                <select name="maintenance_type" id="maintenance_type" class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all" required>
                    <option value="preventive" <?php echo e(old('maintenance_type') === 'preventive' ? 'selected' : ''); ?>>Preventive — Regular scheduled maintenance</option>
                    <option value="corrective" <?php echo e(old('maintenance_type') === 'corrective' ? 'selected' : ''); ?>>Corrective — Fix existing issues</option>
                    <option value="predictive" <?php echo e(old('maintenance_type') === 'predictive' ? 'selected' : ''); ?>>Predictive — Based on analytics data</option>
                    <option value="routine" <?php echo e(old('maintenance_type') === 'routine' ? 'selected' : ''); ?>>Routine — Standard inspection</option>
                    <option value="emergency" <?php echo e(old('maintenance_type') === 'emergency' ? 'selected' : ''); ?>>Emergency — Urgent repair needed</option>
                </select>
                <?php $__errorArgs = ['maintenance_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- Schedule Date -->
            <div>
                <label for="scheduled_date" class="block text-sm font-semibold text-gray-700 mb-2">Scheduled Date</label>
                <input type="date" name="scheduled_date" id="scheduled_date" value="<?php echo e(old('scheduled_date', now()->format('Y-m-d'))); ?>" class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all" required>
                <?php $__errorArgs = ['scheduled_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- Technician -->
            <div>
                <label for="performed_by" class="block text-sm font-semibold text-gray-700 mb-2">Assign Technician (optional)</label>
                <select name="performed_by" id="performed_by" class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all">
                    <option value="">Unassigned</option>
                    <?php $__currentLoopData = $technicians; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tech): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($tech->id); ?>" <?php echo e(old('performed_by') == $tech->id ? 'selected' : ''); ?>>
                            <?php echo e($tech->name); ?> (<?php echo e(ucfirst($tech->role)); ?>)
                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['performed_by'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- Condition Before -->
            <div>
                <label for="condition_before" class="block text-sm font-semibold text-gray-700 mb-2">Current Condition</label>
                <select name="condition_before" id="condition_before" class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all">
                    <option value="excellent">Excellent</option>
                    <option value="good" selected>Good</option>
                    <option value="fair">Fair</option>
                    <option value="poor">Poor</option>
                    <option value="critical">Critical</option>
                </select>
            </div>

            <!-- Current Wear Level -->
            <div>
                <label for="wear_level" class="block text-sm font-semibold text-gray-700 mb-2">Current Wear Level (0-100)</label>
                <input type="number" name="wear_level" id="wear_level" min="0" max="100" value="<?php echo e(old('wear_level', 0)); ?>" class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all">
                <?php $__errorArgs = ['wear_level'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- Notes -->
            <div>
                <label for="notes" class="block text-sm font-semibold text-gray-700 mb-2">Notes (optional)</label>
                <textarea name="notes" id="notes" rows="3" placeholder="Additional notes or observations..." class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all"><?php echo e(old('notes')); ?></textarea>
            </div>

            <!-- Submit -->
            <div class="flex space-x-3 pt-4">
                <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-xl text-sm font-semibold shadow-sm hover:shadow-md transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Schedule Maintenance
                </button>
                <a href="<?php echo e(route('maintenance.index')); ?>" class="inline-flex items-center justify-center px-6 py-3 text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all duration-200">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Cedric\SEIMS\resources\views/maintenance/create.blade.php ENDPATH**/ ?>