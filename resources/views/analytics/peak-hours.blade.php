@extends('layouts.app')

@section('title', 'Peak Hours Analysis')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-poppins">Peak Hours Analysis</h1>
            <p class="text-gray-600">Activity heatmaps — last 90 days of borrowing and reservation patterns</p>
        </div>
        <a href="{{ route('analytics.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900">&larr; Back to Analytics</a>
    </div>

    <!-- Borrowing Heatmap -->
    <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Borrowing Requests by Day & Hour</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr>
                        <th class="text-left py-1 pr-2 text-gray-500 font-bold w-12">Day</th>
                        @for($h = 7; $h <= 20; $h++)
                        <th class="text-center py-1 px-1 text-gray-400 font-medium">{{ $h }}:00</th>
                        @endfor
                    </tr>
                </thead>
                <tbody>
                    @for($d = 1; $d <= 6; $d++)
                    {{-- Mon(1) through Sat(6), skip Sun(0) --}}
                    <tr>
                        <td class="py-1 pr-2 font-bold text-gray-700">{{ $dayNames[$d] }}</td>
                        @for($h = 7; $h <= 20; $h++)
                        @php
                            $val = $borrowMatrix[$d][$h] ?? 0;
                            $maxBorrow = max(1, collect($borrowMatrix)->flatten()->max());
                            $intensity = $val > 0 ? max(0.1, $val / $maxBorrow) : 0;
                        @endphp
                        <td class="text-center py-1 px-1">
                            <div class="w-8 h-8 mx-auto rounded flex items-center justify-center text-xs font-bold
                                {{ $val === 0 ? 'bg-gray-50 text-gray-300' : '' }}"
                                @if($val > 0) style="background: rgba(59, 130, 246, {{ $intensity }}); color: {{ $intensity > 0.5 ? '#fff' : '#1e40af' }}" @endif>
                                {{ $val ?: '' }}
                            </div>
                        </td>
                        @endfor
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>
        <div class="flex items-center gap-2 mt-4 text-xs text-gray-500">
            <span>Low</span>
            <div class="flex gap-0.5">
                <div class="w-4 h-3 rounded" style="background: rgba(59, 130, 246, 0.1)"></div>
                <div class="w-4 h-3 rounded" style="background: rgba(59, 130, 246, 0.3)"></div>
                <div class="w-4 h-3 rounded" style="background: rgba(59, 130, 246, 0.5)"></div>
                <div class="w-4 h-3 rounded" style="background: rgba(59, 130, 246, 0.7)"></div>
                <div class="w-4 h-3 rounded" style="background: rgba(59, 130, 246, 1.0)"></div>
            </div>
            <span>High</span>
        </div>
    </div>

    <!-- Reservation Heatmap -->
    <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Room Reservations by Day & Hour</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr>
                        <th class="text-left py-1 pr-2 text-gray-500 font-bold w-12">Day</th>
                        @for($h = 7; $h <= 20; $h++)
                        <th class="text-center py-1 px-1 text-gray-400 font-medium">{{ $h }}:00</th>
                        @endfor
                    </tr>
                </thead>
                <tbody>
                    @for($d = 1; $d <= 6; $d++)
                    <tr>
                        <td class="py-1 pr-2 font-bold text-gray-700">{{ $dayNames[$d] }}</td>
                        @for($h = 7; $h <= 20; $h++)
                        @php
                            $val = $reserveMatrix[$d][$h] ?? 0;
                            $maxReserve = max(1, collect($reserveMatrix)->flatten()->max());
                            $intensity = $val > 0 ? max(0.1, $val / $maxReserve) : 0;
                        @endphp
                        <td class="text-center py-1 px-1">
                            <div class="w-8 h-8 mx-auto rounded flex items-center justify-center text-xs font-bold
                                {{ $val === 0 ? 'bg-gray-50 text-gray-300' : '' }}"
                                @if($val > 0) style="background: rgba(16, 185, 129, {{ $intensity }}); color: {{ $intensity > 0.5 ? '#fff' : '#065f46' }}" @endif>
                                {{ $val ?: '' }}
                            </div>
                        </td>
                        @endfor
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>
        <div class="flex items-center gap-2 mt-4 text-xs text-gray-500">
            <span>Low</span>
            <div class="flex gap-0.5">
                <div class="w-4 h-3 rounded" style="background: rgba(16, 185, 129, 0.1)"></div>
                <div class="w-4 h-3 rounded" style="background: rgba(16, 185, 129, 0.3)"></div>
                <div class="w-4 h-3 rounded" style="background: rgba(16, 185, 129, 0.5)"></div>
                <div class="w-4 h-3 rounded" style="background: rgba(16, 185, 129, 0.7)"></div>
                <div class="w-4 h-3 rounded" style="background: rgba(16, 185, 129, 1.0)"></div>
            </div>
            <span>High</span>
        </div>
    </div>
</div>
@endsection
