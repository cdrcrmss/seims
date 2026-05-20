{{-- Requires parent x-data with completeModal and completeId --}}
<div x-show="completeModal" x-cloak
     @keydown.escape.window="completeModal = false"
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     style="display: none;">
    <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm" @click="completeModal = false"></div>
    <div @click.stop class="relative bg-white rounded-2xl shadow-xl p-6 w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto z-10">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Complete Maintenance</h3>
        <form :action="'{{ url('/maintenance') }}/' + completeId + '/complete'" method="POST" class="space-y-4">
            @csrf
            @method('PATCH')
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Completed Date</label>
                <input type="date" name="completed_date" value="{{ now()->format('Y-m-d') }}" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Condition After</label>
                <select name="condition_after" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500" required>
                    <option value="excellent">Excellent</option>
                    <option value="good" selected>Good</option>
                    <option value="fair">Fair</option>
                    <option value="poor">Poor</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Wear Level (0-100)</label>
                <input type="number" name="wear_level" min="0" max="100" value="20" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500" required>
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
                <button type="button" @click="completeModal = false" class="flex-1 px-5 py-2.5 text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl">Cancel</button>
            </div>
        </form>
    </div>
</div>
