@extends('layouts.app')

@section('title', 'Maintenance Details')

@section('content')
@php
    $item = $maintenance->item;
    $unit = $maintenance->itemUnit;
    $statusColors = [
        'scheduled' => 'bg-yellow-50 text-yellow-700 ring-yellow-200',
        'in_progress' => 'bg-blue-50 text-blue-700 ring-blue-200',
        'completed' => 'bg-green-50 text-green-700 ring-green-200',
        'cancelled' => 'bg-red-50 text-red-700 ring-red-200',
    ];
    $typeColors = [
        'preventive' => 'bg-blue-50 text-blue-700',
        'corrective' => 'bg-orange-50 text-orange-700',
        'predictive' => 'bg-purple-50 text-purple-700',
        'routine' => 'bg-gray-100 text-gray-700',
        'emergency' => 'bg-red-50 text-red-700',
    ];
@endphp

<div class="space-y-8"
     x-data="{ completeModal: false, completeId: null }"
     @open-complete-maintenance.window="completeModal = true; completeId = $event.detail.id">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 animate-fade-in-up">
        <div class="flex items-start gap-4 min-w-0">
            <a href="{{ $backUrl }}" class="p-2 rounded-xl hover:bg-gray-100 transition-colors shrink-0 mt-1">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 font-poppins truncate">{{ $item?->name ?? 'Unknown equipment' }}</h1>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold ring-1 {{ $statusColors[$maintenance->status] ?? 'bg-gray-100 text-gray-700 ring-gray-200' }}">
                        {{ ucfirst(str_replace('_', ' ', $maintenance->status)) }}
                    </span>
                    @if($maintenance->status === 'scheduled' && $maintenance->isScheduleOverdue())
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-red-100 text-red-700">Overdue</span>
                    @endif
                </div>
                <p class="text-gray-600 mt-1">Maintenance record #{{ $maintenance->id }}</p>
            </div>
        </div>
        @if($maintenance->status === 'scheduled')
        <div class="flex flex-wrap items-center gap-2 shrink-0">
            <button type="button"
                    @click="completeModal = true; completeId = {{ $maintenance->id }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl bg-green-600 hover:bg-green-700 text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Mark complete
            </button>
            @php
                $unit = $maintenance->itemUnit;
                $canDispose = $unit && in_array($unit->status, ['damaged', 'maintenance', 'needs_repair'], true);
            @endphp
            @if($canDispose)
            <form method="POST" action="{{ route('maintenance.units.dispose', $unit) }}" x-ref="disposeForm" class="hidden">
                @csrf
                @method('PATCH')
            </form>
            <button type="button"
                    x-data
                    @click="$dispatch('open-confirm-modal', {
                        title: 'Dispose Unit',
                        message: {{ \Illuminate\Support\Js::from('Mark unit ' . $unit->unit_code . ' as disposed? It cannot be borrowed.') }},
                        type: 'danger',
                        confirmLabel: 'Dispose',
                        form: $refs.disposeForm
                    })"
                    class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl bg-white text-gray-700 hover:bg-gray-50 ring-1 ring-gray-300 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Dispose unit
            </button>
            @endif
        </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Equipment -->
            <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-6 animate-fade-in-up stagger-1">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Equipment</h2>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500">Name</dt>
                        <dd class="font-medium text-gray-900 mt-0.5">{{ $item?->name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Category</dt>
                        <dd class="font-medium text-gray-900 mt-0.5">{{ $item?->category ?? '—' }}</dd>
                    </div>
                    @if($unit)
                    <div>
                        <dt class="text-gray-500">Unit ID</dt>
                        <dd class="font-mono font-medium text-gray-900 mt-0.5">{{ $unit->unit_code }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Unit status</dt>
                        <dd class="mt-0.5">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-semibold uppercase {{ in_array($unit->status, ['damaged', 'maintenance']) ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700' }}">
                                {{ $unit->status }}
                            </span>
                        </dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-gray-500">Item wear level</dt>
                        <dd class="font-medium text-gray-900 mt-0.5">{{ $item?->wear_level ?? 0 }}%</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Available stock</dt>
                        <dd class="font-medium text-gray-900 mt-0.5">{{ $item?->available_stock ?? '—' }} / {{ $item?->total_stock ?? '—' }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Schedule & type -->
            <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-6 animate-fade-in-up stagger-2">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Schedule</h2>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500">Maintenance type</dt>
                        <dd class="mt-0.5">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold {{ $typeColors[$maintenance->maintenance_type] ?? 'bg-gray-100 text-gray-700' }}">
                                {{ $maintenance->typeLabel() }}
                            </span>
                            @if($maintenance->predictive_alert_sent)
                                <span class="text-xs text-purple-600 ml-1">· Predictive alert sent</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Scheduled date</dt>
                        <dd class="font-medium text-gray-900 mt-0.5">
                            {{ $maintenance->scheduled_date?->format('F d, Y') ?? '—' }}
                            @if($maintenance->isScheduleOverdue())
                                <span class="text-red-600 text-xs font-semibold block">Overdue ({{ $maintenance->scheduled_date->diffForHumans() }})</span>
                            @endif
                        </dd>
                    </div>
                    @if($maintenance->next_maintenance_date)
                    <div>
                        <dt class="text-gray-500">Next maintenance (predicted)</dt>
                        <dd class="font-medium text-gray-900 mt-0.5">{{ $maintenance->next_maintenance_date->format('F d, Y') }}</dd>
                    </div>
                    @endif
                    @if($maintenance->completed_date)
                    <div>
                        <dt class="text-gray-500">Completed date</dt>
                        <dd class="font-medium text-gray-900 mt-0.5">{{ $maintenance->completed_date->format('F d, Y') }}</dd>
                    </div>
                    @endif
                    @if($maintenance->wear_level !== null)
                    <div>
                        <dt class="text-gray-500">Wear at record</dt>
                        <dd class="mt-0.5 flex items-center gap-2">
                            <div class="w-24 bg-gray-200 rounded-full h-2">
                                <div class="h-2 rounded-full {{ $maintenance->wear_level >= 70 ? 'bg-red-500' : ($maintenance->wear_level >= 40 ? 'bg-yellow-500' : 'bg-green-500') }}" style="width: {{ $maintenance->wear_level }}%"></div>
                            </div>
                            <span class="font-medium text-gray-900">{{ $maintenance->wear_level }}%</span>
                        </dd>
                    </div>
                    @endif
                </dl>
            </div>

            <!-- Work notes -->
            <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-6 animate-fade-in-up stagger-3">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Work details</h2>
                <div class="space-y-4 text-sm">
                    @if($maintenance->issues_found)
                    <div>
                        <p class="text-gray-500 font-medium">Issues found</p>
                        <p class="text-gray-900 mt-1 whitespace-pre-wrap">{{ $maintenance->issues_found }}</p>
                    </div>
                    @endif
                    @if($maintenance->notes)
                    <div>
                        <p class="text-gray-500 font-medium">Notes</p>
                        <p class="text-gray-900 mt-1 whitespace-pre-wrap">{{ $maintenance->notes }}</p>
                    </div>
                    @endif
                    @if($maintenance->condition_before)
                    <div>
                        <p class="text-gray-500 font-medium">Condition before</p>
                        <p class="text-gray-900 mt-1 capitalize">{{ $maintenance->condition_before }}</p>
                    </div>
                    @endif
                    @if($maintenance->status === 'completed')
                    @if($maintenance->condition_after)
                    <div>
                        <p class="text-gray-500 font-medium">Condition after</p>
                        <p class="text-gray-900 mt-1 capitalize">{{ $maintenance->condition_after }}</p>
                    </div>
                    @endif
                    @if($maintenance->actions_taken)
                    <div>
                        <p class="text-gray-500 font-medium">Actions taken</p>
                        <p class="text-gray-900 mt-1 whitespace-pre-wrap">{{ $maintenance->actions_taken }}</p>
                    </div>
                    @endif
                    @if($maintenance->cost)
                    <div>
                        <p class="text-gray-500 font-medium">Cost</p>
                        <p class="text-gray-900 mt-1">₱{{ number_format($maintenance->cost, 2) }}</p>
                    </div>
                    @endif
                    @endif
                    @if(!$maintenance->issues_found && !$maintenance->notes && !$maintenance->condition_before && $maintenance->status !== 'completed')
                    <p class="text-gray-400">No additional work details recorded yet.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-6 animate-fade-in-up stagger-2">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Record info</h2>
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-gray-500">Record ID</dt>
                        <dd class="font-mono font-medium text-gray-900">#{{ $maintenance->id }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Created</dt>
                        <dd class="font-medium text-gray-900">{{ $maintenance->created_at->format('M d, Y g:i A') }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Last updated</dt>
                        <dd class="font-medium text-gray-900">{{ $maintenance->updated_at->format('M d, Y g:i A') }}</dd>
                    </div>
                    @if($maintenance->technician?->name)
                    <div>
                        <dt class="text-gray-500">Technician</dt>
                        <dd class="font-medium text-gray-900">{{ $maintenance->technician->name }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            @if($maintenance->status === 'scheduled')
            <div class="bg-amber-50 rounded-2xl ring-1 ring-amber-100 p-5 text-sm text-amber-900">
                <p class="font-semibold mb-1">Pending work</p>
                <p class="text-amber-800 text-xs">Complete this record when repair or maintenance is finished. The unit will return to available stock if linked to a unit.</p>
            </div>
            @endif
        </div>
    </div>

    @include('partials.maintenance-complete-modal')
</div>
@endsection
