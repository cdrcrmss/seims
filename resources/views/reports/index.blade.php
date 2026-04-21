@extends('layouts.app')

@section('title', 'Reports')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 font-poppins">Report Packs</h1>
        <p class="text-gray-600">Role-based reports with drill-down and CSV export</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Inventory Report -->
        <a href="{{ route('reports.inventory') }}" class="group bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-6 hover:ring-blue-300 hover:shadow-md transition-all">
            <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">Inventory Report</h3>
            <p class="text-sm text-gray-500 mt-1">Stock levels, wear distribution, status breakdown, asset valuation</p>
        </a>

        <!-- Borrowing Report -->
        <a href="{{ route('reports.borrowing') }}" class="group bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-6 hover:ring-green-300 hover:shadow-md transition-all">
            <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 group-hover:text-green-600 transition-colors">Borrowing Report</h3>
            <p class="text-sm text-gray-500 mt-1">Trends, overdue rate, turnaround, top borrowers and items</p>
        </a>

        <!-- Maintenance Report -->
        <a href="{{ route('reports.maintenance') }}" class="group bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-6 hover:ring-orange-300 hover:shadow-md transition-all">
            <div class="w-12 h-12 rounded-xl bg-orange-100 flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 group-hover:text-orange-600 transition-colors">Maintenance Report</h3>
            <p class="text-sm text-gray-500 mt-1">SLA compliance, costs, ticket throughput, trigger sources</p>
        </a>

        <!-- Reservation Report -->
        <a href="{{ route('reports.reservations') }}" class="group bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-6 hover:ring-purple-300 hover:shadow-md transition-all">
            <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 group-hover:text-purple-600 transition-colors">Reservation Report</h3>
            <p class="text-sm text-gray-500 mt-1">Room utilization, no-show rate, popular rooms, cancellations</p>
        </a>

        <!-- User Activity Report -->
        @if(auth()->user()->isAdmin())
        <a href="{{ route('reports.user-activity') }}" class="group bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-6 hover:ring-red-300 hover:shadow-md transition-all">
            <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 group-hover:text-red-600 transition-colors">User Activity</h3>
            <p class="text-sm text-gray-500 mt-1">Per-student borrowing activity, overdue counts, no-show history</p>
            <span class="inline-flex mt-2 px-2 py-0.5 text-xs font-bold bg-red-100 text-red-700 rounded-full">Admin Only</span>
        </a>
        @endif
    </div>
</div>
@endsection
