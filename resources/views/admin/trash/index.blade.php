@extends('layouts.app')

@section('title', 'Trash — Deleted Records')

@section('content')
<div class="space-y-6" x-data="{ tab: '{{ $tab }}' }">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-poppins">Trash</h1>
            <p class="text-gray-600">View and restore soft-deleted records</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Users
        </a>
    </div>

    <!-- Tab Toggle -->
    <div class="flex bg-gray-100 rounded-xl p-1 w-fit">
        <button @click="tab = 'users'" :class="tab === 'users' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500 hover:text-gray-700'" class="px-5 py-2 rounded-lg text-sm font-medium transition-all">
            Users <span class="ml-1 text-xs px-1.5 py-0.5 rounded-full" :class="tab === 'users' ? 'bg-red-100 text-red-700' : 'bg-gray-200 text-gray-500'">{{ $trashedUsers->count() }}</span>
        </button>
        <button @click="tab = 'reservations'" :class="tab === 'reservations' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500 hover:text-gray-700'" class="px-5 py-2 rounded-lg text-sm font-medium transition-all">
            Reservations <span class="ml-1 text-xs px-1.5 py-0.5 rounded-full" :class="tab === 'reservations' ? 'bg-red-100 text-red-700' : 'bg-gray-200 text-gray-500'">{{ $trashedReservations->count() }}</span>
        </button>
    </div>

    <!-- Trashed Users -->
    <div x-show="tab === 'users'" x-transition class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left px-6 py-4 font-semibold text-gray-600 uppercase tracking-wide text-xs">Name</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600 uppercase tracking-wide text-xs">Email</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600 uppercase tracking-wide text-xs">Role</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600 uppercase tracking-wide text-xs">Deleted</th>
                        <th class="text-right px-6 py-4 font-semibold text-gray-600 uppercase tracking-wide text-xs">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($trashedUsers as $user)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $user->name }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-gray-100 text-gray-700">{{ ucfirst($user->role) }}</span>
                        </td>
                        <td class="px-6 py-4 text-gray-500 text-xs">{{ $user->deleted_at->diffForHumans() }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <form method="POST" action="{{ route('admin.trash.users.restore', $user->id) }}" x-data @submit.prevent="$dispatch('open-confirm-modal', { form: $el, title: 'Restore User', message: 'Restore ' + '{{ $user->name }}' + ' back to the system?', type: 'success' })">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-green-50 text-green-700 hover:bg-green-100 ring-1 ring-green-200/60 transition-all">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                        Restore
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.trash.users.force-delete', $user->id) }}" x-data @submit.prevent="$dispatch('open-confirm-modal', { form: $el, title: 'Permanently Delete', message: 'This will permanently remove ' + '{{ $user->name }}' + ' and all associated data. This cannot be undone.', type: 'danger' })">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-red-50 text-red-700 hover:bg-red-100 ring-1 ring-red-200/60 transition-all">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Delete Forever
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                                <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </div>
                            <p class="text-sm text-gray-400">No deleted users</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Trashed Reservations -->
    <div x-show="tab === 'reservations'" x-transition class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left px-6 py-4 font-semibold text-gray-600 uppercase tracking-wide text-xs">ID</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600 uppercase tracking-wide text-xs">User</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600 uppercase tracking-wide text-xs">Room</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600 uppercase tracking-wide text-xs">Schedule</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600 uppercase tracking-wide text-xs">Status</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600 uppercase tracking-wide text-xs">Deleted</th>
                        <th class="text-right px-6 py-4 font-semibold text-gray-600 uppercase tracking-wide text-xs">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($trashedReservations as $reservation)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 font-medium text-gray-900">#{{ $reservation->id }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ $reservation->user?->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ $reservation->room?->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-xs text-gray-600">
                            {{ \Carbon\Carbon::parse($reservation->start_datetime)->format('M d, Y g:ia') }}
                            <br>to {{ \Carbon\Carbon::parse($reservation->end_datetime)->format('M d, Y g:ia') }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-gray-100 text-gray-700">{{ ucfirst($reservation->status) }}</span>
                        </td>
                        <td class="px-6 py-4 text-gray-500 text-xs">{{ $reservation->deleted_at->diffForHumans() }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <form method="POST" action="{{ route('admin.trash.reservations.restore', $reservation->id) }}" x-data @submit.prevent="$dispatch('open-confirm-modal', { form: $el, title: 'Restore Reservation', message: 'Restore reservation #{{ $reservation->id }} back to the system?', type: 'success' })">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-green-50 text-green-700 hover:bg-green-100 ring-1 ring-green-200/60 transition-all">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                        Restore
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.trash.reservations.force-delete', $reservation->id) }}" x-data @submit.prevent="$dispatch('open-confirm-modal', { form: $el, title: 'Permanently Delete', message: 'This will permanently remove this reservation. This cannot be undone.', type: 'danger' })">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-red-50 text-red-700 hover:bg-red-100 ring-1 ring-red-200/60 transition-all">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Delete Forever
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                                <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <p class="text-sm text-gray-400">No deleted reservations</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
