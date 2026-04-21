@extends('layouts.app')

@section('title', 'Staff Reports')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-poppins">Staff Reports</h1>
            <p class="text-gray-600">Equipment usage and borrowing reports</p>
        </div>
        <div class="flex items-center space-x-4">
            <select class="px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="weekly">Weekly</option>
                <option value="monthly">Monthly</option>
                <option value="quarterly">Quarterly</option>
            </select>
            <button class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-sm hover:shadow-md transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                </svg>
                Export Report
            </button>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl p-6 ring-1 ring-gray-100 shadow-sm card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Total Items</p>
                    <p class="text-3xl font-bold text-blue-600 font-poppins">{{ $totalItems }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $itemsAdded }} added this month</p>
                </div>
                <div class="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 ring-1 ring-gray-100 shadow-sm card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Active Loans</p>
                    <p class="text-3xl font-bold text-green-600 font-poppins">{{ $activeBorrowings }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $borrowingsToday }} today</p>
                </div>
                <div class="w-12 h-12 bg-green-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 ring-1 ring-gray-100 shadow-sm card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Pending Requests</p>
                    <p class="text-3xl font-bold text-yellow-600 font-poppins">{{ $pendingRequests }}</p>
                    <p class="text-xs text-gray-500 mt-1">Need review</p>
                </div>
                <div class="w-12 h-12 bg-yellow-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 ring-1 ring-gray-100 shadow-sm card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Due Soon</p>
                    <p class="text-3xl font-bold text-red-600 font-poppins">{{ $itemsDueSoon }}</p>
                    <p class="text-xs text-gray-500 mt-1">Next 7 days</p>
                </div>
                <div class="w-12 h-12 bg-red-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Item Usage Overview -->
    <div class="bg-white rounded-2xl p-6 ring-1 ring-gray-100 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-900">Most Popular Items</h3>
            <span class="text-sm text-gray-500">Last 30 days</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Item</th>
                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Category</th>
                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Times Borrowed</th>
                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Current Status</th>
                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Usage Rate</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($popularItems as $item)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="py-4 px-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-gray-100 to-gray-200 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $item['name'] }}</p>
                                        <p class="text-xs text-gray-500">Stock: {{ $item['stock'] }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $item['category'] }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-sm text-gray-900 font-medium">{{ $item['borrow_count'] }}</td>
                            <td class="py-4 px-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $item['status'] === 'available' ? 'bg-green-100 text-green-800' : 
                                       'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($item['status']) }}
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex items-center space-x-2">
                                    <div class="w-16 bg-gray-200 rounded-full h-2">
                                        <div class="bg-gradient-to-r from-blue-500 to-purple-600 h-2 rounded-full" style="width: {{ $item['usage_rate'] }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-500">{{ $item['usage_rate'] }}%</span>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Activity & Overdue Items -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Borrowing Activity -->
        <div class="bg-white rounded-2xl p-6 ring-1 ring-gray-100 shadow-sm">
            <h3 class="text-xl font-bold text-gray-900 mb-6">Recent Activity</h3>
            <div class="space-y-4">
                @foreach($recentActivity as $activity)
                    <div class="flex items-center space-x-4 p-4 bg-white/5 rounded-xl">
                        <div class="w-3 h-3 rounded-full 
                            {{ $activity['type'] === 'borrow' ? 'bg-blue-500' : 
                               ($activity['type'] === 'return' ? 'bg-green-500' : 'bg-yellow-500') }}">
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">{{ $activity['action'] }}</p>
                            <p class="text-xs text-gray-500">{{ $activity['user'] }} • {{ $activity['time'] }}</p>
                        </div>
                        <span class="text-xs text-gray-400">{{ $activity['item'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Items Due Soon -->
        <div class="bg-white rounded-2xl p-6 ring-1 ring-gray-100 shadow-sm">
            <h3 class="text-xl font-bold text-gray-900 mb-6">Items Due Soon</h3>
            <div class="space-y-4">
                @foreach($itemsDue as $item)
                    <div class="flex items-center space-x-4 p-4 bg-white/5 rounded-xl">
                        <div class="w-3 h-3 rounded-full 
                            {{ $item['days_until_due'] <= 1 ? 'bg-red-500' : 
                               ($item['days_until_due'] <= 3 ? 'bg-yellow-500' : 'bg-blue-500') }}">
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">{{ $item['item_name'] }}</p>
                            <p class="text-xs text-gray-500">{{ $item['borrower'] }} • Due {{ $item['due_date'] }}</p>
                        </div>
                        <span class="text-xs font-medium 
                            {{ $item['days_until_due'] <= 1 ? 'text-red-500' : 
                               ($item['days_until_due'] <= 3 ? 'text-yellow-500' : 'text-blue-500') }}">
                            {{ $item['days_until_due'] }} days
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Monthly Summary -->
    <div class="bg-white rounded-2xl p-6 ring-1 ring-gray-100 shadow-sm">
        <h3 class="text-xl font-bold text-gray-900 mb-6">Monthly Summary</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="p-6 bg-white/5 rounded-2xl">
                <div class="flex items-center space-x-4 mb-4">
                    <div class="w-12 h-12 bg-green-500/20 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900">{{ $monthlyStats['completed_requests'] }}</h4>
                        <p class="text-sm text-gray-600">Completed Requests</p>
                    </div>
                </div>
                <p class="text-xs text-gray-500">+{{ $monthlyStats['completion_rate'] }}% completion rate</p>
            </div>

            <div class="p-6 bg-white/5 rounded-2xl">
                <div class="flex items-center space-x-4 mb-4">
                    <div class="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900">{{ $monthlyStats['new_items'] }}</h4>
                        <p class="text-sm text-gray-600">Items Added</p>
                    </div>
                </div>
                <p class="text-xs text-gray-500">Inventory expansion</p>
            </div>

            <div class="p-6 bg-white/5 rounded-2xl">
                <div class="flex items-center space-x-4 mb-4">
                    <div class="w-12 h-12 bg-purple-500/20 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900">{{ $monthlyStats['active_students'] }}</h4>
                        <p class="text-sm text-gray-600">Active Students</p>
                    </div>
                </div>
                <p class="text-xs text-gray-500">Engaged users this month</p>
            </div>
        </div>
    </div>
</div>
@endsection