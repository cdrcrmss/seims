

<?php $__env->startSection('title', 'Edit Equipment'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-poppins">Edit Equipment</h1>
            <p class="text-gray-600">Update information for <?php echo e($item->name); ?></p>
        </div>
        <a href="<?php echo e(route('staff.items.index')); ?>"
           class="inline-flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Equipment
        </a>
    </div>

    <div class="max-w-3xl">
        <div class="bg-white rounded-2xl ring-1 ring-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="text-lg font-bold text-gray-900">Equipment Details</h2>
                <p class="text-sm text-gray-500 mt-0.5">Modify the equipment information below</p>
            </div>

            <div class="p-6">
                <form action="<?php echo e(route('staff.items.update', $item)); ?>" method="POST" enctype="multipart/form-data" class="space-y-5">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>

                    <!-- Current Image Display -->
                    <?php if($item->image_path): ?>
                        <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl border border-gray-100">
                            <div class="w-20 h-20 bg-white rounded-xl overflow-hidden ring-1 ring-gray-200 flex-shrink-0">
                                <img src="<?php echo e(asset('storage/' . $item->image_path)); ?>" alt="<?php echo e($item->name); ?>" class="w-full h-full object-cover">
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900"><?php echo e($item->name); ?></p>
                                <p class="text-xs text-gray-500"><?php echo e($item->category); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Equipment Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" value="<?php echo e(old('name', $item->name)); ?>" required
                               class="w-full px-4 py-2.5 rounded-xl border border-gray-300 bg-white text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-green-500/30 focus:border-green-500 transition-colors text-sm">
                        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Description
                        </label>
                        <textarea name="description" rows="3"
                                  class="w-full px-4 py-2.5 rounded-xl border border-gray-300 bg-white text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-green-500/30 focus:border-green-500 transition-colors text-sm"><?php echo e(old('description', $item->description)); ?></textarea>
                        <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Category & Status Row -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Category -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                Category <span class="text-red-500">*</span>
                            </label>
                            <select name="category" required
                                    class="w-full px-4 py-2.5 rounded-xl border border-gray-300 bg-white text-gray-900 focus:ring-2 focus:ring-green-500/30 focus:border-green-500 transition-colors text-sm">
                                <option value="electronics" <?php echo e(old('category', $item->category) == 'electronics' ? 'selected' : ''); ?>>Electronics</option>
                                <option value="mechanical" <?php echo e(old('category', $item->category) == 'mechanical' ? 'selected' : ''); ?>>Mechanical</option>
                                <option value="chemical" <?php echo e(old('category', $item->category) == 'chemical' ? 'selected' : ''); ?>>Chemical</option>
                                <option value="optical" <?php echo e(old('category', $item->category) == 'optical' ? 'selected' : ''); ?>>Optical</option>
                                <option value="measuring" <?php echo e(old('category', $item->category) == 'measuring' ? 'selected' : ''); ?>>Measuring</option>
                                <option value="computing" <?php echo e(old('category', $item->category) == 'computing' ? 'selected' : ''); ?>>Computing</option>
                                <option value="other" <?php echo e(old('category', $item->category) == 'other' ? 'selected' : ''); ?>>Other</option>
                            </select>
                            <?php $__errorArgs = ['category'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select name="status" required
                                    class="w-full px-4 py-2.5 rounded-xl border border-gray-300 bg-white text-gray-900 focus:ring-2 focus:ring-green-500/30 focus:border-green-500 transition-colors text-sm">
                                <option value="available" <?php echo e(old('status', $item->status) == 'available' ? 'selected' : ''); ?>>Available</option>
                                <option value="in_use" <?php echo e(old('status', $item->status) == 'in_use' ? 'selected' : ''); ?>>In Use</option>
                                <option value="maintenance" <?php echo e(old('status', $item->status) == 'maintenance' ? 'selected' : ''); ?>>Under Maintenance</option>
                                <option value="damaged" <?php echo e(old('status', $item->status) == 'damaged' ? 'selected' : ''); ?>>Damaged</option>
                                <option value="lost" <?php echo e(old('status', $item->status) == 'lost' ? 'selected' : ''); ?>>Lost</option>
                                <option value="retired" <?php echo e(old('status', $item->status) == 'retired' ? 'selected' : ''); ?>>Retired</option>
                            </select>
                            <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <!-- Stock Information -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Stock Information</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1.5">
                                    Total Stock <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="total_stock" value="<?php echo e(old('total_stock', $item->total_stock)); ?>" min="1" required
                                       class="w-full px-4 py-2.5 rounded-xl border border-gray-300 bg-white text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-green-500/30 focus:border-green-500 transition-colors text-sm">
                                <?php $__errorArgs = ['total_stock'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1.5">
                                    Available Stock <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="available_stock" value="<?php echo e(old('available_stock', $item->available_stock)); ?>" min="0" required
                                       class="w-full px-4 py-2.5 rounded-xl border border-gray-300 bg-white text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-green-500/30 focus:border-green-500 transition-colors text-sm">
                                <?php $__errorArgs = ['available_stock'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                        <div class="mt-2 px-3 py-2 bg-amber-50 rounded-lg border border-amber-100">
                            <p class="text-xs text-amber-700">
                                <strong>Current Status:</strong> <?php echo e($item->available_stock); ?>/<?php echo e($item->total_stock); ?> available
                            </p>
                        </div>
                    </div>

                    <!-- Image Upload -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Update Equipment Image
                        </label>
                        <input type="file" name="image" accept="image/*"
                               class="w-full px-4 py-2.5 rounded-xl border border-dashed border-gray-300 bg-gray-50 text-gray-900 file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 file:cursor-pointer cursor-pointer transition-colors text-sm">
                        <p class="text-xs text-gray-500 mt-1">Leave empty to keep current image. JPG, PNG, GIF up to 2MB</p>
                        <?php $__errorArgs = ['image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end items-center gap-3 pt-4 border-t border-gray-100">
                        <a href="<?php echo e(route('staff.items.index')); ?>"
                           class="inline-flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200">
                            Cancel
                        </a>
                        <button type="submit"
                                class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-sm hover:shadow-md transition-all duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Update Equipment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const totalStockInput = document.querySelector('input[name="total_stock"]');
    const availableStockInput = document.querySelector('input[name="available_stock"]');

    function validateStock() {
        const total = parseInt(totalStockInput.value) || 0;
        const available = parseInt(availableStockInput.value) || 0;

        if (available > total) {
            availableStockInput.setCustomValidity('Available stock cannot exceed total stock');
            availableStockInput.classList.add('border-red-500');
            availableStockInput.classList.remove('border-gray-300');
        } else {
            availableStockInput.setCustomValidity('');
            availableStockInput.classList.remove('border-red-500');
            availableStockInput.classList.add('border-gray-300');
        }
    }

    totalStockInput.addEventListener('input', validateStock);
    availableStockInput.addEventListener('input', validateStock);
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Cedric\SEIMS\resources\views/staff/items/edit.blade.php ENDPATH**/ ?>