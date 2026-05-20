@props(['unit'])

@php
    $record = $unit->activeMaintenanceRecord;
@endphp

<div class="flex flex-wrap items-center justify-end gap-2 shrink-0 sm:ml-4" x-data>
    @if($record)
        <span class="text-xs font-semibold text-orange-700 bg-orange-50 px-2.5 py-1 rounded-lg whitespace-nowrap">
            Repair scheduled · {{ $record->scheduled_date->format('M d, Y') }}
        </span>
        <a href="{{ route('maintenance.index', ['status' => 'overdue']) }}"
           class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg bg-amber-50 text-amber-800 hover:bg-amber-100 ring-1 ring-amber-200 transition-colors whitespace-nowrap">
            View details
        </a>
        <button type="button"
                @click="$dispatch('open-complete-maintenance', { id: {{ $record->id }} })"
                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg bg-green-50 text-green-700 hover:bg-green-100 ring-1 ring-green-200 transition-colors whitespace-nowrap">
            Complete
        </button>
    @else
        <span class="text-xs font-semibold text-amber-700 bg-amber-50 px-2.5 py-1 rounded-lg">Scheduling maintenance…</span>
    @endif
    @if(in_array($unit->status, ['damaged', 'maintenance', 'needs_repair'], true))
        <form method="POST" action="{{ route('maintenance.units.dispose', $unit) }}" x-ref="disposeForm" class="hidden">
            @csrf
            @method('PATCH')
        </form>
        <button type="button"
                @click="$dispatch('open-confirm-modal', {
                    title: 'Dispose Unit',
                    message: {{ \Illuminate\Support\Js::from('Mark unit ' . $unit->unit_code . ' as disposed? It cannot be borrowed. Any scheduled maintenance for this unit will be cancelled.') }},
                    type: 'danger',
                    confirmLabel: 'Dispose',
                    form: $refs.disposeForm
                })"
                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg bg-white text-gray-700 hover:bg-gray-50 ring-1 ring-gray-300 transition-colors whitespace-nowrap">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            Dispose
        </button>
    @endif
</div>
