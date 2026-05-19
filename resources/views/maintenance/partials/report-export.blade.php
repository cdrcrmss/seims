<!-- Maintenance Report Export -->
    <div class="bg-white rounded-2xl ring-1 ring-gray-100 shadow-sm overflow-hidden {{ $class ?? '' }}">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between cursor-pointer select-none"
             onclick="this.nextElementSibling.classList.toggle('hidden')">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-amber-500/10 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">Maintenance Report Export</h3>
                    <p class="text-xs text-gray-500">Filter and download maintenance records as PDF</p>
                </div>
            </div>
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>

        <div class="px-6 py-5 {{ ($collapsed ?? false) ? 'hidden' : '' }}">
            <form method="POST" action="{{ route('maintenance.export') }}" data-file-download>
                @csrf
                <input type="hidden" name="format" value="pdf">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
                    <!-- Date From -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Date From</label>
                        <input type="date" name="date_from"
                               class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-gray-50"
                               value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                    </div>
                    <!-- Date To -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Date To</label>
                        <input type="date" name="date_to"
                               class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-gray-50"
                               value="{{ now()->format('Y-m-d') }}">
                    </div>
                    <!-- Status -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status</label>
                        <select name="status"
                                class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-gray-50">
                            <option value="all">All Statuses</option>
                            <option value="scheduled">Scheduled</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="overdue">Overdue</option>
                        </select>
                    </div>
                    <!-- Type -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Maintenance Type</label>
                        <select name="maintenance_type"
                                class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-gray-50">
                            <option value="all">All Types</option>
                            <option value="preventive">Preventive</option>
                            <option value="corrective">Repair</option>
                            <option value="predictive">Predictive</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit"
                            class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-sm hover:shadow-md transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        Export PDF Report
                    </button>
                </div>
            </form>
        </div>
    </div>