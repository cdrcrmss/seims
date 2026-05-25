{{-- Global maintenance complete modal (listens for open-complete-maintenance window event) --}}
<div x-data="maintenanceCompleteModal"
     @open-complete-maintenance.window="show($event.detail)"
     @keydown.escape.window="if (open) close()"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-[110] flex items-center justify-center p-4"
     style="display: none;"
     x-transition:enter="ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">

    <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-md" @click="close()"></div>

    <div @click.stop
         class="relative z-10 bg-white rounded-2xl shadow-xl p-6 w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95 translate-y-2"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-2">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Complete Maintenance</h3>
        <form :action="completeUrl()" method="POST" class="space-y-4">
            @csrf
            @method('PATCH')
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Completed Date</label>
                <input type="date" name="completed_date" value="{{ now()->format('Y-m-d') }}" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Condition After</label>
                <select name="condition_after" x-model="conditionAfter" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500" required>
                    <option value="excellent">Excellent</option>
                    <option value="good">Good</option>
                    <option value="fair">Fair</option>
                    <option value="poor">Poor</option>
                    <option value="critical">Critical</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Predicted wear level</label>
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
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Actions Taken</label>
                <textarea name="actions_taken" rows="2" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500" required></textarea>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Issues Found (optional)</label>
                <textarea name="issues_found" rows="2" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500"></textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold">
                    Mark Complete
                </button>
                <button type="button" @click="close()" class="flex-1 px-5 py-2.5 text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl">Cancel</button>
            </div>
        </form>
    </div>
</div>
