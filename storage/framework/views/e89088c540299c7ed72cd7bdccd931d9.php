<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'type' => 'info',
    'message',
    'actionUrl' => null,
    'actionLabel' => 'View',
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'type' => 'info',
    'message',
    'actionUrl' => null,
    'actionLabel' => 'View',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $typeMap = [
        'danger'  => ['bg' => 'bg-red-50',    'ring' => 'ring-red-100',    'icon_bg' => 'bg-red-100',    'icon' => 'text-red-600',    'text' => 'text-red-800',    'link' => 'text-red-700 hover:text-red-900'],
        'warning' => ['bg' => 'bg-yellow-50',  'ring' => 'ring-yellow-100',  'icon_bg' => 'bg-yellow-100',  'icon' => 'text-yellow-600',  'text' => 'text-yellow-800',  'link' => 'text-yellow-700 hover:text-yellow-900'],
        'info'    => ['bg' => 'bg-blue-50',    'ring' => 'ring-blue-100',    'icon_bg' => 'bg-blue-100',    'icon' => 'text-blue-600',    'text' => 'text-blue-800',    'link' => 'text-blue-700 hover:text-blue-900'],
        'success' => ['bg' => 'bg-green-50',   'ring' => 'ring-green-100',   'icon_bg' => 'bg-green-100',   'icon' => 'text-green-600',   'text' => 'text-green-800',   'link' => 'text-green-700 hover:text-green-900'],
    ];
    $s = $typeMap[$type] ?? $typeMap['info'];
?>

<div class="flex items-center space-x-3 <?php echo e($s['bg']); ?> rounded-xl p-4 ring-1 <?php echo e($s['ring']); ?>" role="alert">
    <div class="w-9 h-9 <?php echo e($s['icon_bg']); ?> rounded-lg flex items-center justify-center flex-shrink-0" aria-hidden="true">
        <svg class="w-4 h-4 <?php echo e($s['icon']); ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
    </div>
    <div class="flex-1 min-w-0">
        <p class="text-sm font-semibold <?php echo e($s['text']); ?>"><?php echo e($message); ?></p>
    </div>
    <?php if($actionUrl): ?>
    <a href="<?php echo e($actionUrl); ?>" class="text-xs font-semibold <?php echo e($s['link']); ?> whitespace-nowrap"><?php echo e($actionLabel); ?> &rarr;</a>
    <?php endif; ?>
</div>
<?php /**PATH C:\Users\Cedric\SEIMS\resources\views/components/alert-banner.blade.php ENDPATH**/ ?>