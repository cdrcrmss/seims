@extends('layouts.app')

@section('title', 'Schedule Maintenance')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex items-center space-x-4 animate-fade-in-up">
        <a href="{{ route('maintenance.index') }}" class="p-2 rounded-xl hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-poppins">Schedule Maintenance</h1>
            <p class="text-gray-600">Create a new maintenance record for equipment</p>
        </div>
    </div>

    <div class="max-w-2xl">
        <form method="POST" action="{{ route('maintenance.store') }}"
              x-data="scheduleMaintenanceForm(@js($wearByCondition), @js(old('condition_before', 'good')))"
              class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-8 space-y-6 animate-fade-in-up stagger-1">
            @csrf

            <!-- Equipment -->
            <div>
                <label for="item_id" class="block text-sm font-semibold text-gray-700 mb-2">Equipment</label>
                <select name="item_id" id="item_id" class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all" required>
                    <option value="">Select equipment...</option>
                    @foreach($items as $item)
                        <option value="{{ $item->id }}" {{ (int) old('item_id', $selectedItemId) === (int) $item->id ? 'selected' : '' }}>
                            {{ $item->name }} — {{ $item->category }}
                        </option>
                    @endforeach
                </select>
                @error('item_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Maintenance Type -->
            <div>
                <label for="maintenance_type" class="block text-sm font-semibold text-gray-700 mb-2">Maintenance Type</label>
                <select name="maintenance_type" id="maintenance_type" class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all" required>
                    <option value="preventive" {{ old('maintenance_type') === 'preventive' ? 'selected' : '' }}>Preventive — Regular scheduled maintenance</option>
                    <option value="corrective" {{ old('maintenance_type') === 'corrective' ? 'selected' : '' }}>Repair — Fix existing issues</option>
                    <option value="predictive" {{ old('maintenance_type') === 'predictive' ? 'selected' : '' }}>Predictive — Based on analytics data</option>
                    <option value="routine" {{ old('maintenance_type') === 'routine' ? 'selected' : '' }}>Routine — Standard inspection</option>
                    <option value="emergency" {{ old('maintenance_type') === 'emergency' ? 'selected' : '' }}>Emergency — Urgent repair needed</option>
                </select>
                @error('maintenance_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Schedule Date -->
            <div>
                <label for="scheduled_date" class="block text-sm font-semibold text-gray-700 mb-2">Scheduled Date</label>
                <input type="date" name="scheduled_date" id="scheduled_date" value="{{ old('scheduled_date', now()->format('Y-m-d')) }}" class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all" required>
                @error('scheduled_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Condition Before -->
            <div>
                <label for="condition_before" class="block text-sm font-semibold text-gray-700 mb-2">Current Condition</label>
                <select name="condition_before" id="condition_before" x-model="conditionBefore" class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all">
                    <option value="excellent">Excellent</option>
                    <option value="good">Good</option>
                    <option value="fair">Fair</option>
                    <option value="poor">Poor</option>
                    <option value="critical">Critical</option>
                </select>
            </div>

            <!-- Predicted wear from condition -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Current Wear Level</label>
                <input type="hidden" name="wear_level" :value="predictedWear">
                <div class="rounded-xl bg-gray-50 ring-1 ring-gray-200 px-4 py-3">
                    <div class="flex items-center justify-between gap-3 mb-2">
                        <span class="text-2xl font-bold text-gray-900" x-text="predictedWear + '%'"></span>
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Auto from condition</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="h-2.5 rounded-full transition-all duration-200"
                             :class="wearBarColor()"
                             :style="'width: ' + predictedWear + '%'"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">
                        Excellent 10% · Good 25% · Fair 45% · Poor 65% · Critical 80%
                    </p>
                </div>
                @error('wear_level') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Notes -->
            <div>
                <label for="notes" class="block text-sm font-semibold text-gray-700 mb-2">Notes (optional)</label>
                <textarea name="notes" id="notes" rows="3" placeholder="Additional notes or observations..." class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all">{{ old('notes') }}</textarea>
            </div>

            <!-- Submit -->
            <div class="flex space-x-3 pt-4">
                <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-xl text-sm font-semibold shadow-sm hover:shadow-md transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Schedule Maintenance
                </button>
                <a href="{{ route('maintenance.index') }}" class="inline-flex items-center justify-center px-6 py-3 text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all duration-200">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('scheduleMaintenanceForm', (wearMap, initialCondition) => ({
        wearMap: wearMap || {},
        conditionBefore: initialCondition || 'good',

        get predictedWear() {
            return this.wearMap[this.conditionBefore] ?? 25;
        },

        wearBarColor() {
            const w = this.predictedWear;
            if (w >= 70) return 'bg-red-500';
            if (w >= 40) return 'bg-yellow-500';
            return 'bg-green-500';
        },
    }));
});
</script>
@endpush
