<div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-6 animate-fade-in-up stagger-3">
    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center space-x-2">
        <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        <span>Critical Maintenance</span>
    </h2>

    @if($criticalUnits->count() > 0)
        <p class="text-xs font-semibold text-red-700 uppercase tracking-wide mb-2">Damaged units</p>
        @foreach($criticalUnits as $unit)
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 p-3 bg-red-50 rounded-xl mb-2">
            <div>
                <p class="font-medium text-gray-900">{{ $unit->item?->name ?? 'Unknown' }}</p>
                <p class="text-xs text-gray-500">Unit ID: <span class="font-mono font-semibold text-red-700">{{ $unit->unit_code }}</span></p>
            </div>
            @include('partials.critical-unit-actions', ['unit' => $unit])
        </div>
        @endforeach
    @endif

    @if($dashboardData['critical_maintenance_items']->count() > 0)
        <p class="text-xs font-semibold text-orange-700 uppercase tracking-wide mb-2 {{ $criticalUnits->count() > 0 ? 'mt-4' : '' }}">High wear (&ge; 70%)</p>
        @foreach($dashboardData['critical_maintenance_items'] as $item)
        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl mb-2">
            <div>
                <p class="font-medium text-gray-900">{{ $item->name }}</p>
                <p class="text-xs text-gray-500">{{ $item->category }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <div class="w-24 bg-gray-200 rounded-full h-2.5">
                    <div class="h-2.5 rounded-full {{ $item->wear_level >= 80 ? 'bg-red-500' : 'bg-orange-500' }}" style="width: {{ $item->wear_level }}%"></div>
                </div>
                <span class="text-sm font-bold {{ $item->wear_level >= 80 ? 'text-red-600' : 'text-orange-600' }} w-14 text-right">{{ $item->wear_level }}%</span>
            </div>
        </div>
        @endforeach
    @endif

    @if($criticalUnits->count() === 0 && $dashboardData['critical_maintenance_items']->count() === 0)
        <div class="text-center py-6 text-gray-400">
            <svg class="w-10 h-10 mx-auto mb-2 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <p>No critical maintenance right now.</p>
            <p class="text-xs mt-1">Damaged units and high-wear items appear here.</p>
        </div>
    @endif

    <a href="{{ route('analytics.maintenance-predictions', ['urgency' => 'critical']) }}" class="block text-center text-sm text-green-600 font-semibold mt-3 hover:text-green-700">View critical items →</a>
</div>
