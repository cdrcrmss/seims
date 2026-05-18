@props(['laboratory' => null, 'size' => 'xs'])

@if($laboratory)
    @php
        $labColors = [
            'Alfresco' => 'bg-emerald-100 text-emerald-800',
            'Kitchen' => 'bg-amber-100 text-amber-800',
            'Food Lab' => 'bg-purple-100 text-purple-800',
            'Hotel' => 'bg-cyan-100 text-cyan-800',
        ];
        $labColor = $labColors[$laboratory] ?? 'bg-gray-100 text-gray-800';
        $sizeClass = $size === 'sm' ? 'text-sm px-2.5 py-0.5' : 'text-xs px-2 py-0.5';
    @endphp
    <span class="inline-flex items-center rounded-full font-medium {{ $sizeClass }} {{ $labColor }}">
        {{ $laboratory }}
    </span>
@endif
