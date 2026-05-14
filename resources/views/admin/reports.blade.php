@extends('layouts.app')

@section('title', 'System Reports')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-poppins">System Reports</h1>
            <p class="text-gray-600">Comprehensive reports and analytics</p>
        </div>
        <div class="flex items-center space-x-4">
            <select class="px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="monthly">Monthly</option>
                <option value="quarterly">Quarterly</option>
                <option value="yearly">Yearly</option>
            </select>
            <form method="POST" action="{{ route('analytics.export') }}" class="inline">
                @csrf
                <input type="hidden" name="type" value="comprehensive">
                <button type="submit" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-sm hover:shadow-md transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                    </svg>
                    Export Report
                </button>
            </form>
        </div>
    </div>

    <!-- Overview Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl p-6 ring-1 ring-gray-100 shadow-sm card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Total Users</p>
                    <p class="text-3xl font-bold text-blue-600 font-poppins">{{ $totalUsers }}</p>
                    <p class="text-xs text-gray-500 mt-1">+{{ $newUsersThisMonth }} this month</p>
                </div>
                <div class="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 ring-1 ring-gray-100 shadow-sm card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Total Items</p>
                    <p class="text-3xl font-bold text-green-600 font-poppins">{{ $totalItems }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $availableItems }} available</p>
                </div>
                <div class="w-12 h-12 bg-green-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 ring-1 ring-gray-100 shadow-sm card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Active Borrowings</p>
                    <p class="text-3xl font-bold text-orange-600 font-poppins">{{ $activeBorrowings }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $pendingRequests }} pending</p>
                </div>
                <div class="w-12 h-12 bg-orange-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 ring-1 ring-gray-100 shadow-sm card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Overdue Items</p>
                    <p class="text-3xl font-bold text-red-600 font-poppins">{{ $overdueItems }}</p>
                    <p class="text-xs text-gray-500 mt-1">Need attention</p>
                </div>
                <div class="w-12 h-12 bg-red-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Borrowing Trends Chart -->
        <div class="bg-white rounded-2xl p-6 ring-1 ring-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900">Borrowing Trends</h3>
                <span class="text-sm text-gray-500">Last 30 days</span>
            </div>
            <div class="h-64">
                <canvas id="borrowingTrendsChart"></canvas>
            </div>

            <!-- Most Borrowed Items -->
            <div class="mt-6 pt-6 border-t border-gray-100">
                <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wide mb-4">Most Borrowed Items</h4>
                @if(count($mostBorrowedItems) > 0)
                    <div class="space-y-3">
                        @foreach($mostBorrowedItems as $index => $item)
                            <div class="flex items-center gap-3">
                                <span class="text-xs font-bold text-gray-400 w-5">{{ $index + 1 }}</span>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between mb-1">
                                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $item['name'] }}</p>
                                        <span class="text-xs font-bold text-indigo-600 ml-2">{{ $item['count'] }}x</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-1.5">
                                        <div class="h-1.5 rounded-full bg-gradient-to-r from-indigo-500 to-purple-500" style="width: {{ ($item['count'] / max($mostBorrowedItems[0]['count'], 1)) * 100 }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-400 text-center py-4">No borrowings in the last 30 days</p>
                @endif
            </div>
        </div>

        <!-- Item Categories Distribution -->
        <div class="bg-white rounded-2xl p-6 ring-1 ring-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900">Item Categories</h3>
                <span class="text-sm text-gray-500">Distribution</span>
            </div>
        </div>
    </div>

    <!-- Recent Activity & Top Users -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Activity -->
        <div class="bg-white rounded-2xl p-6 ring-1 ring-gray-100 shadow-sm">
            <h3 class="text-xl font-bold text-gray-900 mb-6">Recent Activity</h3>
            <div class="space-y-4">
                @foreach($recentActivity as $activity)
                    <div class="flex items-center space-x-4 p-4 bg-white/5 rounded-xl">
                        <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">{{ $activity['action'] }}</p>
                            <p class="text-xs text-gray-500">{{ $activity['user'] }} • {{ $activity['time'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Top Borrowers -->
        <div class="bg-white rounded-2xl p-6 ring-1 ring-gray-100 shadow-sm">
            <h3 class="text-xl font-bold text-gray-900 mb-6">Top Borrowers</h3>
            <div class="space-y-4">
                @foreach($topBorrowers as $index => $borrower)
                    <div class="flex items-center space-x-4">
                        <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-sm font-bold">
                            {{ $index + 1 }}
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">{{ $borrower['name'] }}</p>
                            <p class="text-xs text-gray-500">{{ $borrower['count'] }} items borrowed</p>
                        </div>
                        <span class="text-sm text-gray-500">{{ $borrower['role'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('borrowingTrendsChart').getContext('2d');
    const trendsData = @json($borrowingTrends);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: trendsData.map(d => d.label),
            datasets: [{
                label: 'Borrowings',
                data: trendsData.map(d => d.count),
                borderColor: '#6366f1',
                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                borderWidth: 2.5,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#6366f1',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 3,
                pointHoverRadius: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1f2937',
                    titleFont: { size: 12 },
                    bodyFont: { size: 13, weight: 'bold' },
                    padding: 10,
                    cornerRadius: 8,
                    callbacks: {
                        title: (items) => items[0].label,
                        label: (item) => item.raw + ' borrowing' + (item.raw !== 1 ? 's' : '')
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { 
                        maxTicksLimit: 7,
                        font: { size: 11 },
                        color: '#9ca3af'
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: '#f3f4f6' },
                    ticks: { 
                        stepSize: 1,
                        font: { size: 11 },
                        color: '#9ca3af'
                    }
                }
            }
        }
    });
});
</script>
@endpush
@endsection