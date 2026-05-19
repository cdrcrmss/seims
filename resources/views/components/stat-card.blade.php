@props([
    'label',
    'value',
    'color' => 'neutral',
    'icon' => null,
    'subtitle' => null,
    'href' => null,
])

@php
    $colorMap = [
        'neutral' => [
            'accent' => 'bg-slate-300',
            'text' => 'text-slate-900',
            'bg' => 'bg-slate-100',
            'ring' => 'hover:ring-slate-200',
        ],
        'danger' => [
            'accent' => 'bg-rose-400',
            'text' => 'text-rose-700',
            'bg' => 'bg-rose-50',
            'ring' => 'hover:ring-rose-200',
        ],
        'red' => [
            'accent' => 'bg-rose-400',
            'text' => 'text-rose-700',
            'bg' => 'bg-rose-50',
            'ring' => 'hover:ring-rose-200',
        ],
    ];
    $legacy = ['green', 'blue', 'orange', 'purple', 'yellow'];
    $resolved = in_array($color, $legacy, true) ? 'neutral' : $color;
    $c = $colorMap[$resolved] ?? $colorMap['neutral'];
    $tag = $href ? 'a' : 'div';
@endphp

<{{ $tag }} @if($href) href="{{ $href }}" @endif class="bg-white rounded-2xl p-6 ring-1 ring-gray-200/80 shadow-sm card-hover relative overflow-hidden {{ $href ? 'cursor-pointer ' . $c['ring'] . ' transition-all block' : '' }}">
    <div class="absolute top-0 left-0 w-0.5 h-full {{ $c['accent'] }} rounded-r-full"></div>
    <div class="flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">{{ $label }}</p>
            <p class="text-3xl font-bold {{ $c['text'] }} font-poppins mt-0.5">{{ $value }}</p>
            @if($subtitle)
                <p class="text-xs text-slate-400 mt-1">{{ $subtitle }}</p>
            @endif
            {{ $slot }}
        </div>
        @if($icon)
        <div class="w-11 h-11 {{ $c['bg'] }} rounded-xl flex items-center justify-center flex-shrink-0 text-slate-600" aria-hidden="true">
            {!! $icon !!}
        </div>
        @endif
    </div>
</{{ $tag }}>
