

<?php $__env->startSection('title', 'QR Code - ' . $item->name); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-8">
    <!-- Header -->
    <div class="flex items-center space-x-4 animate-fade-in-up">
        <a href="<?php echo e(route('qr.scanner')); ?>" class="p-2 rounded-xl hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-poppins">QR Code</h1>
            <p class="text-gray-600"><?php echo e($item->name); ?></p>
        </div>
    </div>

    <div class="max-w-lg mx-auto">
        <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-8 text-center animate-fade-in-up stagger-1">
            <!-- QR Code Image (generated via JS) -->
            <div class="inline-block bg-white p-4 rounded-2xl ring-1 ring-gray-100 mb-6">
                <div id="qrcode" class="w-64 h-64 mx-auto flex items-center justify-center"></div>
            </div>

            <!-- Item Info -->
            <h2 class="text-xl font-bold text-gray-900"><?php echo e($item->name); ?></h2>
            <p class="text-sm text-gray-500 mt-1"><?php echo e($item->category); ?></p>
            
            <div class="mt-4 inline-flex items-center px-4 py-2 bg-gray-100 rounded-xl">
                <span class="text-sm font-mono font-semibold text-gray-700"><?php echo e($item->qr_code); ?></span>
            </div>

            <div class="mt-6 grid grid-cols-2 gap-4 text-left">
                <div class="bg-gray-50 rounded-xl p-3">
                    <p class="text-xs text-gray-500 uppercase">Item ID</p>
                    <p class="text-sm font-semibold text-gray-900">#<?php echo e($item->id); ?></p>
                </div>
                <div class="bg-gray-50 rounded-xl p-3">
                    <p class="text-xs text-gray-500 uppercase">Stock</p>
                    <p class="text-sm font-semibold text-gray-900"><?php echo e($item->available_stock); ?> / <?php echo e($item->total_stock); ?></p>
                </div>
                <div class="bg-gray-50 rounded-xl p-3">
                    <p class="text-xs text-gray-500 uppercase">Status</p>
                    <p class="text-sm font-semibold <?php echo e($item->available_stock > 0 ? 'text-green-600' : 'text-red-600'); ?>">
                        <?php echo e($item->available_stock > 0 ? 'Available' : 'Out of Stock'); ?>

                    </p>
                </div>
                <div class="bg-gray-50 rounded-xl p-3">
                    <p class="text-xs text-gray-500 uppercase">Location</p>
                    <p class="text-sm font-semibold text-gray-900"><?php echo e($item->location ?? 'N/A'); ?></p>
                </div>
            </div>

            <!-- Print Button -->
            <div class="mt-6 flex space-x-3 justify-center">
                <button onclick="window.print()" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2.5 rounded-xl font-semibold transition-all duration-200 flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    <span>Print QR Code</span>
                </button>
                <a href="<?php echo e(route('qr.scanner')); ?>" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2.5 rounded-xl font-semibold transition-all duration-200">
                    Back to Scanner
                </a>
            </div>

            <p class="mt-4 text-xs text-gray-400">Unique ID: <?php echo e($item->qr_code); ?></p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        new QRCode(document.getElementById('qrcode'), {
            text: <?php echo json_encode($qrData, 15, 512) ?>,
            width: 256,
            height: 256,
            colorDark: '#000000',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.H
        });
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Cedric\SEIMS\resources\views/qr/display.blade.php ENDPATH**/ ?>