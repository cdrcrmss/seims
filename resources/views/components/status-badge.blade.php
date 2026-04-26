@props([
    'status',
])

@php
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
        'checked_in' => 'bg-blue-100 text-blue-700',
        'no_show'   => 'bg-amber-100 text-amber-700',
        'ordered'   => 'bg-blue-100 text-blue-700',
        'received'  => 'bg-green-100 text-green-700',
    ];
    $style = $statusStyles[strtolower($status)] ?? 'bg-gray-100 text-gray-600';
@endphp

<span {{ $attributes->merge(['class' => "text-xs font-semibold px-2 py-0.5 rounded-md $style"]) }}>
    {{ ucfirst($status) }}
</span>
