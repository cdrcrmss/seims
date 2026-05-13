@props([
    'label',
    'value',
    'color' => 'green',
    'icon' => null,
    'subtitle' => null,
    'href' => null,
])

@php
    $colorMap = [
        'green'  => ['border' => 'bg-green-500',  'text' => 'text-green-600',  'bg' => 'bg-green-50',  'ring' => 'hover:ring-green-200'],
        'blue'   => ['border' => 'bg-blue-500',   'text' => 'text-blue-600',   'bg' => 'bg-blue-50',   'ring' => 'hover:ring-blue-200'],
        'orange' => ['border' => 'bg-orange-500',  'text' => 'text-orange-600',  'bg' => 'bg-orange-50',  'ring' => 'hover:ring-orange-200'],
        'red'    => ['border' => 'bg-red-500',    'text' => 'text-red-600',    'bg' => 'bg-red-50',    'ring' => 'hover:ring-red-200'],
        'purple' => ['border' => 'bg-purple-500',  'text' => 'text-purple-600',  'bg' => 'bg-purple-50',  'ring' => 'hover:ring-purple-200'],
        'yellow' => ['border' => 'bg-yellow-500',  'text' => 'text-yellow-600',  'bg' => 'bg-yellow-50',  'ring' => 'hover:ring-yellow-200'],
    ];
    $c = $colorMap[$color] ?? $colorMap['green'];
    $tag = $href ? 'a' : 'div';
@endphp

<{{ $tag }} @if($href) href="{{ $href }}" @endif class="bg-white rounded-2xl p-6 ring-1 ring-gray-100 shadow-sm card-hover relative overflow-hidden {{ $href ? 'cursor-pointer ' . $c['ring'] . ' transition-all block' : '' }}">
    <div class="absolute top-0 left-0 w-1 h-full {{ $c['border'] }} rounded-r-full"></div>
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">{{ $label }}</p>
            <p class="text-3xl font-bold {{ $c['text'] }} font-poppins">{{ $value }}</p>
            @if($subtitle)
                <p class="text-xs text-gray-400 mt-1">{{ $subtitle }}</p>
            @endif
            {{ $slot }}
        </div>
        @if($icon)
        <div class="w-12 h-12 {{ $c['bg'] }} rounded-xl flex items-center justify-center flex-shrink-0" aria-hidden="true">
            {!! $icon !!}
        </div>
        @endif
    </div>
</{{ $tag }}>
