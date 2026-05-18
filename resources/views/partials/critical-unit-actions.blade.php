@props(['unit'])

@php
    $record = $unit->activeMaintenanceRecord;
@endphp

<div class="flex flex-col sm:flex-row sm:items-center gap-2 shrink-0">
    @if($record)
        <span class="text-xs font-semibold text-orange-700 bg-orange-50 px-2.5 py-1 rounded-lg whitespace-nowrap">
            Corrective · {{ $record->scheduled_date->format('M d, Y') }}
        </span>
        <a href="{{ route('maintenance.index') }}" class="text-xs font-semibold text-red-600 hover:text-red-700 whitespace-nowrap">View maintenance list</a>
    @else
        <span class="text-xs font-semibold text-amber-700 bg-amber-50 px-2.5 py-1 rounded-lg">Scheduling maintenance…</span>
    @endif
    @if(in_array($unit->status, ['damaged', 'maintenance', 'needs_repair'], true))
        <button type="button"
                onclick="disposeCriticalUnit({{ $unit->item_id }}, {{ $unit->id }}, @json($unit->unit_code))"
                class="text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 px-2.5 py-1 rounded-lg whitespace-nowrap">
            Dispose unit (beyond repair)
        </button>
    @endif
</div>
