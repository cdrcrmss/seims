@props(['record'])

@php
    $unit = $record->itemUnit;
    $canDispose = $unit && in_array($unit->status, ['damaged', 'maintenance', 'needs_repair'], true);
@endphp

<div class="flex flex-wrap items-center justify-end gap-2 shrink-0" x-data>
    <a href="{{ route('maintenance.index', ['status' => $record->isScheduleOverdue() ? 'overdue' : 'upcoming']) }}"
       class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg bg-amber-50 text-amber-800 hover:bg-amber-100 ring-1 ring-amber-200 transition-colors whitespace-nowrap">
        View details
    </a>
    @if($record->status === 'scheduled')
    <button type="button"
            @click="$dispatch('open-complete-maintenance', { id: {{ $record->id }} })"
            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg bg-green-50 text-green-700 hover:bg-green-100 ring-1 ring-green-200 transition-colors whitespace-nowrap">
        Complete
    </button>
    @endif
    @if($canDispose)
    <form method="POST" action="{{ route('maintenance.units.dispose', $unit) }}" x-ref="disposeForm" class="hidden">
        @csrf
        @method('PATCH')
    </form>
    <button type="button"
            @click="$dispatch('open-confirm-modal', {
                title: 'Dispose Unit',
                message: {{ \Illuminate\Support\Js::from('Mark unit ' . $unit->unit_code . ' as disposed? It cannot be borrowed.') }},
                type: 'danger',
                confirmLabel: 'Dispose',
                form: $refs.disposeForm
            })"
            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg bg-white text-gray-700 hover:bg-gray-50 ring-1 ring-gray-300 transition-colors whitespace-nowrap">
        Dispose
    </button>
    @endif
</div>
