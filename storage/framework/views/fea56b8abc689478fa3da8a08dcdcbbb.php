<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'status',
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
    'status',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $statusStyles = [
        'pending'   => 'bg-yellow-100 text-yellow-700',
        'approved'  => 'bg-blue-100 text-blue-700',
        'issued'    => 'bg-green-100 text-green-700',
        'returned'  => 'bg-gray-100 text-gray-600',
        'rejected'  => 'bg-red-100 text-red-600',
        'cancelled' => 'bg-gray-100 text-gray-500',
        'overdue'   => 'bg-red-100 text-red-700',
        'active'    => 'bg-green-100 text-green-700',
        'completed' => 'bg-gray-100 text-gray-600',
        'ordered'   => 'bg-blue-100 text-blue-700',
        'received'  => 'bg-green-100 text-green-700',
    ];
    $style = $statusStyles[strtolower($status)] ?? 'bg-gray-100 text-gray-600';
?>

<span <?php echo e($attributes->merge(['class' => "text-xs font-semibold px-2 py-0.5 rounded-md $style"])); ?>>
    <?php echo e(ucfirst($status)); ?>

</span>
<?php /**PATH C:\Users\Cedric\SEIMS\resources\views/components/status-badge.blade.php ENDPATH**/ ?>