@extends('layouts.app')

@section('title', 'User Activity Report')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-poppins">User Activity Report</h1>
            <p class="text-gray-600">Student borrowing activity and compliance — last {{ $days }} days</p>
        </div>
        <div class="flex gap-2 items-center">
            <form method="GET" class="flex gap-2 items-center">
                <select name="days" class="text-sm border border-gray-300 rounded-lg px-3 py-1.5" onchange="this.form.submit()">
                    @foreach([7, 30, 60, 90] as $d)
                    <option value="{{ $d }}" {{ $days == $d ? 'selected' : '' }}>{{ $d }} days</option>
                    @endforeach
                </select>
            </form>
            <a href="{{ route('reports.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900">&larr; Reports</a>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-bold text-gray-500 uppercase">Student</th>
                        <th class="text-left px-6 py-3 text-xs font-bold text-gray-500 uppercase">Student ID</th>
                        <th class="text-center px-6 py-3 text-xs font-bold text-gray-500 uppercase">Borrowings</th>
                        <th class="text-center px-6 py-3 text-xs font-bold text-gray-500 uppercase">Overdue Now</th>
                        <th class="text-center px-6 py-3 text-xs font-bold text-gray-500 uppercase">Reservations</th>
                        <th class="text-center px-6 py-3 text-xs font-bold text-gray-500 uppercase">No-Shows</th>
                        <th class="text-center px-6 py-3 text-xs font-bold text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50 {{ $user->overdue_borrowings > 0 || $user->no_shows >= 3 ? 'bg-red-50/50' : '' }}">
                        <td class="px-6 py-3 font-semibold text-gray-900">{{ $user->name }}</td>
                        <td class="px-6 py-3 text-gray-600">{{ $user->student_id ?? '-' }}</td>
                        <td class="px-6 py-3 text-center font-bold text-gray-900">{{ $user->total_borrowings }}</td>
                        <td class="px-6 py-3 text-center">
                            @if($user->overdue_borrowings > 0)
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700">{{ $user->overdue_borrowings }}</span>
                            @else
                            <span class="text-gray-400">0</span>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-center text-gray-700">{{ $user->total_reservations }}</td>
                        <td class="px-6 py-3 text-center">
                            @if($user->no_shows >= 3)
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700">{{ $user->no_shows }} (blocked)</span>
                            @elseif($user->no_shows > 0)
                            <span class="text-orange-600 font-bold">{{ $user->no_shows }}</span>
                            @else
                            <span class="text-gray-400">0</span>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-center">
                            <a href="{{ route('timeline.user', $user) }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800">Timeline</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-400">No student activity found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $users->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
