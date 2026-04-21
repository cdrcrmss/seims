@extends('layouts.app')

@section('title', 'Scan Event Log')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-poppins">Scan Event Log</h1>
            <p class="text-gray-600">Complete history of all QR scan actions</p>
        </div>
        <a href="{{ route('qr.scanner') }}" class="inline-flex items-center gap-2 bg-gray-900 hover:bg-gray-800 text-white px-5 py-2.5 rounded-xl font-semibold transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
            Scanner
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        @php
            $todayScans = $events->where('created_at', '>=', today())->count();
            $successCount = $events->where('outcome', 'success')->count();
            $warningCount = $events->where('outcome', 'warning')->count();
            $blockedCount = $events->where('outcome', 'blocked')->count();
        @endphp
        <div class="bg-white rounded-xl ring-1 ring-gray-200 p-4">
            <p class="text-xs font-bold text-gray-500 uppercase">Today</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $todayScans }}</p>
        </div>
        <div class="bg-green-50 rounded-xl ring-1 ring-green-200 p-4">
            <p class="text-xs font-bold text-green-600 uppercase">Success</p>
            <p class="text-2xl font-bold text-green-700 mt-1">{{ $successCount }}</p>
        </div>
        <div class="bg-yellow-50 rounded-xl ring-1 ring-yellow-200 p-4">
            <p class="text-xs font-bold text-yellow-600 uppercase">Warning</p>
            <p class="text-2xl font-bold text-yellow-700 mt-1">{{ $warningCount }}</p>
        </div>
        <div class="bg-red-50 rounded-xl ring-1 ring-red-200 p-4">
            <p class="text-xs font-bold text-red-600 uppercase">Blocked</p>
            <p class="text-2xl font-bold text-red-700 mt-1">{{ $blockedCount }}</p>
        </div>
    </div>

    <!-- Events Table -->
    <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-bold text-gray-500 uppercase">Time</th>
                        <th class="text-left px-6 py-3 text-xs font-bold text-gray-500 uppercase">User</th>
                        <th class="text-left px-6 py-3 text-xs font-bold text-gray-500 uppercase">Action</th>
                        <th class="text-left px-6 py-3 text-xs font-bold text-gray-500 uppercase">Target</th>
                        <th class="text-left px-6 py-3 text-xs font-bold text-gray-500 uppercase">Outcome</th>
                        <th class="text-left px-6 py-3 text-xs font-bold text-gray-500 uppercase">Message</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($events as $event)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-3 text-xs text-gray-600 whitespace-nowrap">
                            {{ $event->created_at->format('M d, g:ia') }}
                        </td>
                        <td class="px-6 py-3 text-sm font-medium text-gray-900">
                            {{ $event->user?->name ?? 'System' }}
                        </td>
                        <td class="px-6 py-3">
                            @php
                                $actionColors = [
                                    'borrow_issue' => 'bg-blue-100 text-blue-700',
                                    'borrow_return' => 'bg-purple-100 text-purple-700',
                                    'room_check_in' => 'bg-green-100 text-green-700',
                                    'maintenance_start' => 'bg-orange-100 text-orange-700',
                                    'maintenance_complete' => 'bg-teal-100 text-teal-700',
                                    'item_lookup' => 'bg-gray-100 text-gray-700',
                                ];
                            @endphp
                            <span class="inline-flex px-2 py-0.5 rounded-lg text-xs font-semibold {{ $actionColors[$event->action_type] ?? 'bg-gray-100 text-gray-700' }}">
                                {{ str_replace('_', ' ', $event->action_type) }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-xs text-gray-600">
                            {{ ucfirst($event->target_type) }} #{{ $event->target_id }}
                        </td>
                        <td class="px-6 py-3">
                            @if($event->outcome === 'success')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-xs font-semibold bg-green-100 text-green-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Success
                                </span>
                            @elseif($event->outcome === 'warning')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-xs font-semibold bg-yellow-100 text-yellow-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span> Warning
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-xs font-semibold bg-red-100 text-red-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Blocked
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-xs text-gray-600 max-w-[200px] truncate">
                            {{ $event->outcome_message }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400">No scan events recorded yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($events->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $events->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
