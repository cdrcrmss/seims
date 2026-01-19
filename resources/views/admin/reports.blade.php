@extends('layouts.app')

@section('title', 'System Reports')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white font-poppins">System Reports</h1>
            <p class="text-gray-600 dark:text-gray-400">Comprehensive reports and analytics</p>
        </div>
        <div class="flex items-center space-x-4">
            <select class="px-4 py-2 bg-white/10 dark:bg-white/5 border border-white/20 dark:border-white/10 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="monthly">Monthly</option>
                <option value="quarterly">Quarterly</option>
                <option value="yearly">Yearly</option>
            </select>
            <button class="btn-primary px-6 py-2 rounded-xl font-semibold flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                </svg>
                <span>Export Report</span>
            </button>
        </div>
    </div>

    <!-- Overview Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="glass rounded-2xl p-6 shadow-xl card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide">Total Users</p>
                    <p class="text-3xl font-bold text-blue-600 dark:text-blue-400 font-poppins">{{ $totalUsers }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">+{{ $newUsersThisMonth }} this month</p>
                </div>
                <div class="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="glass rounded-2xl p-6 shadow-xl card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide">Total Items</p>
                    <p class="text-3xl font-bold text-green-600 dark:text-green-400 font-poppins">{{ $totalItems }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $availableItems }} available</p>
                </div>
                <div class="w-12 h-12 bg-green-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="glass rounded-2xl p-6 shadow-xl card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide">Active Loans</p>
                    <p class="text-3xl font-bold text-orange-600 dark:text-orange-400 font-poppins">{{ $activeBorrowings }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $pendingRequests }} pending</p>
                </div>
                <div class="w-12 h-12 bg-orange-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="glass rounded-2xl p-6 shadow-xl card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide">Overdue Items</p>
                    <p class="text-3xl font-bold text-red-600 dark:text-red-400 font-poppins">{{ $overdueItems }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Need attention</p>
                </div>
                <div class="w-12 h-12 bg-red-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Borrowing Trends Chart -->
        <div class="glass rounded-3xl p-6 shadow-xl">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Borrowing Trends</h3>
                <span class="text-sm text-gray-500 dark:text-gray-400">Last 6 months</span>
            </div>
            <div class="h-64 flex items-center justify-center bg-gradient-to-br from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20 rounded-2xl">
                <div class="text-center">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <p class="text-gray-600 dark:text-gray-400">Chart visualization would be implemented here</p>
                    <p class="text-sm text-gray-500 dark:text-gray-500 mt-2">Using libraries like Chart.js or D3.js</p>
                </div>
            </div>
        </div>

        <!-- Item Categories Distribution -->
        <div class="glass rounded-3xl p-6 shadow-xl">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Item Categories</h3>
                <span class="text-sm text-gray-500 dark:text-gray-400">Distribution</span>
            </div>
            <div class="space-y-4">
                @foreach($itemsByCategory as $category => $count)
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $category }}</span>
                        <div class="flex items-center space-x-3">
                            <div class="w-24 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-gradient-to-r from-blue-500 to-purple-600 h-2 rounded-full" style="width: {{ ($count / $totalItems) * 100 }}%"></div>
                            </div>
                            <span class="text-sm text-gray-500 dark:text-gray-400 w-12 text-right">{{ $count }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Recent Activity & Top Users -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Activity -->
        <div class="glass rounded-3xl p-6 shadow-xl">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Recent Activity</h3>
            <div class="space-y-4">
                @foreach($recentActivity as $activity)
                    <div class="flex items-center space-x-4 p-4 bg-white/5 dark:bg-white/5 rounded-xl">
                        <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $activity['action'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $activity['user'] }} • {{ $activity['time'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Top Borrowers -->
        <div class="glass rounded-3xl p-6 shadow-xl">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Top Borrowers</h3>
            <div class="space-y-4">
                @foreach($topBorrowers as $index => $borrower)
                    <div class="flex items-center space-x-4">
                        <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-sm font-bold">
                            {{ $index + 1 }}
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $borrower['name'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $borrower['count'] }} items borrowed</p>
                        </div>
                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $borrower['role'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Detailed Reports -->
    <div class="glass rounded-3xl p-6 shadow-xl">
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Detailed Reports</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <a href="#" class="group p-6 bg-white/5 dark:bg-white/5 rounded-2xl hover:bg-white/10 dark:hover:bg-white/10 transition-colors">
                <div class="flex items-center space-x-4 mb-4">
                    <div class="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Usage Analytics</h4>
                </div>
                <p class="text-gray-600 dark:text-gray-300 text-sm">Detailed usage patterns and trends</p>
            </a>

            <a href="#" class="group p-6 bg-white/5 dark:bg-white/5 rounded-2xl hover:bg-white/10 dark:hover:bg-white/10 transition-colors">
                <div class="flex items-center space-x-4 mb-4">
                    <div class="w-12 h-12 bg-green-500/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Inventory Report</h4>
                </div>
                <p class="text-gray-600 dark:text-gray-300 text-sm">Stock levels and item conditions</p>
            </a>

            <a href="#" class="group p-6 bg-white/5 dark:bg-white/5 rounded-2xl hover:bg-white/10 dark:hover:bg-white/10 transition-colors">
                <div class="flex items-center space-x-4 mb-4">
                    <div class="w-12 h-12 bg-purple-500/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white">User Activity</h4>
                </div>
                <p class="text-gray-600 dark:text-gray-300 text-sm">User engagement and behavior</p>
            </a>
        </div>
    </div>
</div>
@endsection