<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'label',
    'value',
    'color' => 'green',
    'icon' => null,
    'subtitle' => null,
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
    'label',
    'value',
    'color' => 'green',
    'icon' => null,
    'subtitle' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $colorMap = [
        'green'  => ['border' => 'bg-green-500',  'text' => 'text-green-600',  'bg' => 'bg-green-50'],
        'blue'   => ['border' => 'bg-blue-500',   'text' => 'text-blue-600',   'bg' => 'bg-blue-50'],
        'orange' => ['border' => 'bg-orange-500',  'text' => 'text-orange-600',  'bg' => 'bg-orange-50'],
        'red'    => ['border' => 'bg-red-500',    'text' => 'text-red-600',    'bg' => 'bg-red-50'],
        'purple' => ['border' => 'bg-purple-500',  'text' => 'text-purple-600',  'bg' => 'bg-purple-50'],
        'yellow' => ['border' => 'bg-yellow-500',  'text' => 'text-yellow-600',  'bg' => 'bg-yellow-50'],
    ];
    $c = $colorMap[$color] ?? $colorMap['green'];
?>

<div class="bg-white rounded-2xl p-6 ring-1 ring-gray-100 shadow-sm card-hover relative overflow-hidden">
    <div class="absolute top-0 left-0 w-1 h-full <?php echo e($c['border']); ?> rounded-r-full"></div>
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide"><?php echo e($label); ?></p>
            <p class="text-3xl font-bold <?php echo e($c['text']); ?> font-poppins"><?php echo e($value); ?></p>
            <?php if($subtitle): ?>
                <p class="text-xs text-gray-400 mt-1"><?php echo e($subtitle); ?></p>
            <?php endif; ?>
            <?php echo e($slot); ?>

        </div>
        <?php if($icon): ?>
        <div class="w-12 h-12 <?php echo e($c['bg']); ?> rounded-xl flex items-center justify-center flex-shrink-0" aria-hidden="true">
            <?php echo $icon; ?>

        </div>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH C:\Users\Cedric\SEIMS\resources\views/components/stat-card.blade.php ENDPATH**/ ?>