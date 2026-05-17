<!-- Analytics Report Export -->
<div class="bg-white rounded-2xl ring-1 ring-gray-100 shadow-sm overflow-hidden {{ $class ?? '' }}">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between cursor-pointer select-none"
         onclick="this.nextElementSibling.classList.toggle('hidden')">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-green-500/10 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-base font-bold text-gray-900">Analytics Report Export</h3>
                <p class="text-xs text-gray-500">Choose a report type and download analytics as PDF</p>
            </div>
        </div>
        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </div>

    <div class="px-6 py-5 {{ ($collapsed ?? false) ? 'hidden' : '' }}">
        <form method="POST" action="{{ route('analytics.export') }}" data-file-download>
            @csrf
            <input type="hidden" name="format" value="pdf">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-5">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Date From</label>
                    <input type="date" name="date_from"
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-gray-50"
                           value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Date To</label>
                    <input type="date" name="date_to"
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-gray-50"
                           value="{{ now()->format('Y-m-d') }}">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Report Type</label>
                    <select name="type"
                            class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-gray-50">
                        <option value="comprehensive">Comprehensive Overview</option>
                        <option value="demand_forecast">Demand Forecast</option>
                        <option value="utilization">Equipment Utilization</option>
                        <option value="maintenance">Maintenance Predictions</option>
                        <option value="procurement">Procurement Analytics</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit"
                        class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-sm hover:shadow-md transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    Export PDF Report
                </button>
            </div>
        </form>
    </div>
</div>
