@props(['record'])

<div class="min-w-0 flex-1">
    <div class="flex flex-wrap items-center gap-2">
        <p class="font-medium text-gray-900">{{ $record->item?->name ?? 'Unknown' }}</p>
        @if($record->isScheduleOverdue())
            <span class="text-[10px] font-bold uppercase tracking-wide px-2 py-0.5 rounded-full bg-red-100 text-red-700">Overdue</span>
        @endif
        <span class="text-[10px] font-bold uppercase tracking-wide px-2 py-0.5 rounded-full bg-gray-100 text-gray-700">{{ $record->typeLabel() }}</span>
    </div>
    <p class="text-xs text-gray-500 mt-1">
        {{ $record->item?->category ?? '—' }}
        &middot;
        <span class="font-mono text-gray-600">{{ $record->equipmentCodeDisplay() }}</span>
    </p>
</div>
