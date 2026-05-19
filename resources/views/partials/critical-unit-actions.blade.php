@props(['unit'])

@php
    $record = $unit->activeMaintenanceRecord;
@endphp

<div class="flex flex-wrap items-center justify-end gap-2 shrink-0 sm:ml-4">
    @if($record)
        <span class="text-xs font-semibold text-orange-700 bg-orange-50 px-2.5 py-1 rounded-lg whitespace-nowrap">
            Repair scheduled · {{ $record->scheduled_date->format('M d, Y') }}
        </span>
        <a href="{{ route('maintenance.index') }}"
           class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg bg-white text-green-700 hover:bg-green-50 ring-1 ring-green-200 transition-colors whitespace-nowrap">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
            Maintenance list
        </a>
    @else
        <span class="text-xs font-semibold text-amber-700 bg-amber-50 px-2.5 py-1 rounded-lg">Scheduling maintenance…</span>
    @endif
    @if(in_array($unit->status, ['damaged', 'maintenance', 'needs_repair'], true))
        <button type="button"
                data-dispose-url="{{ route('maintenance.units.dispose', $unit) }}"
                data-unit-code="{{ $unit->unit_code }}"
                @click="$dispatch('open-confirm-modal', {
                    title: 'Dispose Unit',
                    message: @json('Mark unit ' . $unit->unit_code . ' as disposed? It cannot be borrowed. Any scheduled maintenance for this unit will be cancelled.'),
                    type: 'danger',
                    confirmLabel: 'Dispose',
                    action: 'dispose-unit',
                    target: $el
                })"
                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg bg-white text-gray-700 hover:bg-gray-50 ring-1 ring-gray-300 transition-colors whitespace-nowrap disabled:opacity-50">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            Dispose unit
        </button>
    @endif
</div>
