@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<div class="space-y-8" x-data="{
    showAddUserModal: false,
    showResetPasswordModal: false,
    resetUser: { id: null, name: '' },
    openResetPassword(user) {
        this.resetUser = { id: user.id, name: user.name };
        this.showResetPasswordModal = true;
    }
}">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-poppins">User Management</h1>
            <p class="text-gray-600">Manage system users and their roles</p>
        </div>
        <button @click="showAddUserModal = true" 
                class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-sm hover:shadow-md transition-all duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Add User
        </button>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <a href="{{ route('admin.users.index') }}" class="bg-white rounded-2xl p-6 ring-1 ring-gray-100 shadow-sm card-hover cursor-pointer hover:ring-green-200 transition-all block">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Total Users</p>
                    <p class="text-3xl font-bold text-green-600 font-poppins">{{ $roleCounts['total'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.users.index', ['role' => 'admin']) }}" class="bg-white rounded-2xl p-6 ring-1 ring-gray-100 shadow-sm card-hover cursor-pointer hover:ring-emerald-200 transition-all block">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Admins</p>
                    <p class="text-3xl font-bold text-emerald-600 font-poppins">{{ $roleCounts['admin'] }}</p>
                </div>
                <div class="w-12 h-12 bg-emerald-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.users.index', ['role' => 'staff']) }}" class="bg-white rounded-2xl p-6 ring-1 ring-gray-100 shadow-sm card-hover cursor-pointer hover:ring-teal-200 transition-all block">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Staff</p>
                    <p class="text-3xl font-bold text-teal-600 font-poppins">{{ $roleCounts['staff'] }}</p>
                </div>
                <div class="w-12 h-12 bg-teal-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2V6"></path>
                    </svg>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.users.index', ['role' => 'student']) }}" class="bg-white rounded-2xl p-6 ring-1 ring-gray-100 shadow-sm card-hover cursor-pointer hover:ring-lime-200 transition-all block">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Students</p>
                    <p class="text-3xl font-bold text-lime-600 font-poppins">{{ $roleCounts['student'] }}</p>
                </div>
                <div class="w-12 h-12 bg-lime-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-lime-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                    </svg>
                </div>
            </div>
        </a>
    </div>

    <!-- Pending Approval Alert -->
    @if($roleCounts['pending'] > 0)
    <div class="flex items-center space-x-3 bg-amber-50 rounded-xl p-4 ring-1 ring-amber-200">
        <div class="w-9 h-9 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-amber-800">{{ $roleCounts['pending'] }} student account{{ $roleCounts['pending'] > 1 ? 's' : '' }} pending approval</p>
            <p class="text-xs text-amber-600 mt-0.5">New registrations require admin approval before students can access the system.</p>
        </div>
        <a href="{{ route('admin.users.index', ['approval' => 'pending']) }}" class="text-xs font-semibold text-amber-700 hover:text-amber-900 whitespace-nowrap bg-amber-100 px-3 py-1.5 rounded-lg">Review &rarr;</a>
    </div>
    @endif

    <!-- Users Table -->
    <div class="bg-white rounded-2xl ring-1 ring-gray-100 shadow-sm overflow-hidden">
        <div class="p-4 sm:p-6 border-b border-gray-100">
            <div class="flex flex-col gap-4">
                <h2 class="text-xl font-bold text-gray-900">All Users</h2>
                <form method="GET" action="{{ route('admin.users.index') }}"
                      class="flex flex-col sm:flex-row sm:flex-wrap sm:items-center gap-3 w-full">
                    @if($approval)
                        <input type="hidden" name="approval" value="{{ $approval }}">
                    @endif
                    <div class="relative z-30 w-full sm:flex-1 sm:min-w-[200px] sm:max-w-md"
                         x-data="userSearchComponent()"
                         @click.outside="showSuggestions = false">
                        <input type="text" name="search" value="{{ $search }}"
                               placeholder="Search by name, email, or ID..."
                               x-model="query"
                               @input="onInput()"
                               @focus="onFocus()"
                               @keydown.escape="showSuggestions = false"
                               @keydown.arrow-down.prevent="highlightNext()"
                               @keydown.arrow-up.prevent="highlightPrev()"
                               @keydown.enter.prevent="selectHighlighted()"
                               autocomplete="off"
                               class="w-full pl-10 pr-8 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-gray-900 placeholder-gray-500 focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm">
                        <svg class="absolute left-3 top-3 w-5 h-5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <button x-show="query.length > 0" @click="clearSearch()" type="button"
                                class="absolute right-3 top-3 text-gray-400 hover:text-gray-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                        <div x-show="showSuggestions && suggestions.length > 0" x-cloak @mousedown.prevent
                             class="absolute left-0 right-0 top-full mt-1 bg-white rounded-xl shadow-lg border border-gray-200 z-[100] max-h-60 overflow-y-auto">
                            <template x-for="(suggestion, index) in suggestions" :key="suggestion.id">
                                <button type="button" @mousedown.prevent="selectSuggestion(suggestion)"
                                        :class="{ 'bg-green-50': highlightedIndex === index }"
                                        class="w-full text-left px-4 py-2.5 text-sm hover:bg-green-50 flex items-center gap-3 border-b border-gray-50 last:border-0">
                                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center shrink-0 text-green-700 text-xs font-bold"
                                         x-text="suggestion.name.substring(0, 2).toUpperCase()"></div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate" x-text="suggestion.name"></p>
                                        <p class="text-xs text-gray-500 truncate" x-text="suggestion.detail"></p>
                                    </div>
                                </button>
                            </template>
                        </div>
                    </div>
                    <select name="role" onchange="this.form.submit()"
                            class="w-full sm:w-auto px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-gray-900 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <option value="" @selected(!$role)>All Roles</option>
                        <option value="admin" @selected($role === 'admin')>Admin</option>
                        <option value="staff" @selected($role === 'staff')>Staff</option>
                        <option value="student" @selected($role === 'student')>Student</option>
                    </select>
                    <button type="submit"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-xl">
                        Search
                    </button>
                    @if($search || $role)
                    <a href="{{ route('admin.users.index', $approval ? ['approval' => $approval] : []) }}"
                       class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl">
                        Clear
                    </a>
                    @endif
                </form>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse($users as $user)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                        @if($user->student_id)
                                            <div class="text-sm text-gray-500">{{ $user->role === 'staff' ? 'Staff ID' : 'Student ID' }}: {{ $user->student_id }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium 
                                    {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : 
                                       ($user->role === 'staff' ? 'bg-green-100 text-green-800' : 
                                        'bg-blue-100 text-blue-800') }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if(!$user->is_approved)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                    Pending Approval
                                </span>
                                @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Active
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $user->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    @if(!$user->is_approved)
                                    <form action="{{ route('admin.users.approve', $user) }}" method="POST" class="inline" x-data @submit.prevent="$dispatch('open-confirm-modal', { form: $el, title: 'Approve Account', message: 'Are you sure you want to approve this student account?', type: 'success' })">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-green-50 text-green-700 hover:bg-green-100 ring-1 ring-green-200/60 transition-all duration-200" title="Approve">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            Approve
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.users.reject', $user) }}" method="POST" class="inline" x-data @submit.prevent="$dispatch('open-confirm-modal', { form: $el, title: 'Reject Registration', message: 'Are you sure you want to reject and remove this registration?', type: 'danger' })">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-red-50 text-red-700 hover:bg-red-100 ring-1 ring-red-200/60 transition-all duration-200" title="Reject">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            Reject
                                        </button>
                                    </form>
                                    @else
                                    <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:text-blue-600 hover:bg-blue-50 transition-all duration-200" title="Edit user">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    @if(!$user->isAdmin())
                                    <button type="button"
                                            @click="openResetPassword({ id: {{ $user->id }}, name: @js($user->name) })"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:text-amber-600 hover:bg-amber-50 transition-all duration-200"
                                            title="Reset password">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                    </button>
                                    @endif
                                    @if($user->id !== auth()->id() && !$user->isAdmin())
                                        <form action="{{ route('admin.users.delete', $user) }}" method="POST" class="inline" x-data @submit.prevent="$dispatch('open-confirm-modal', { form: $el, title: 'Delete User', message: 'Are you sure you want to delete this user? This action cannot be undone.', type: 'danger' })">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:text-red-600 hover:bg-red-50 transition-all duration-200" title="Delete user">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                    </svg>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No users found</h3>
                                    <p class="text-gray-600">Get started by adding your first user.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-white/10">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    <!-- Add User Modal -->
    <div x-show="showAddUserModal" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 transition-opacity bg-gray-900/75 backdrop-blur-sm" @click="showAddUserModal = false"></div>
            
            <!-- Modal panel -->
            <div class="relative z-10 w-full max-w-2xl bg-white rounded-3xl shadow-2xl transform transition-all"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                <div class="flex items-start justify-between p-6 border-b border-gray-200 rounded-t-3xl">
                    <h3 class="text-xl font-semibold text-gray-900">Add New User</h3>
                    <button type="button" @click="showAddUserModal = false" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                    </button>
                </div>
                <form method="POST" action="{{ route('admin.users.store') }}" class="p-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block mb-2 text-sm font-medium text-gray-900">Name</label>
                            <input type="text" id="name" name="name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-green-500 focus:border-green-500 block w-full p-2.5" required>
                        </div>
                        <div>
                            <label for="email" class="block mb-2 text-sm font-medium text-gray-900">Email</label>
                            <input type="email" id="email" name="email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-green-500 focus:border-green-500 block w-full p-2.5" required>
                        </div>
                        <div>
                            <label for="role" class="block mb-2 text-sm font-medium text-gray-900">Role</label>
                            <select id="role" name="role" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-green-500 focus:border-green-500 block w-full p-2.5" required>
                                <option value="student">Student</option>
                                <option value="staff">Staff</option>
                            </select>
                        </div>
                        <div id="role-id-field" style="display: none;">
                            <label id="role-id-label" for="student_id" class="block mb-2 text-sm font-medium text-gray-900">ID</label>
                            <input type="text" id="student_id" name="student_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-green-500 focus:border-green-500 block w-full p-2.5" placeholder="">
                        </div>
                        <div class="md:col-span-2">
                            <label for="password" class="block mb-2 text-sm font-medium text-gray-900">Password</label>
                            <input type="password" id="password" name="password" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-green-500 focus:border-green-500 block w-full p-2.5" required>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4 mt-6">
                        <button type="submit" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-sm hover:shadow-md transition-all duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                            Add User
                        </button>
                        <button type="button" @click="showAddUserModal = false" class="inline-flex items-center px-5 py-2.5 text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all duration-200">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reset Password Modal -->
    <div x-show="showResetPasswordModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm" @click="showResetPasswordModal = false"></div>
            <div class="relative z-10 w-full max-w-md bg-white rounded-2xl shadow-2xl p-6" @click.stop>
                <h3 class="text-lg font-bold text-gray-900 mb-1">Reset Password</h3>
                <p class="text-sm text-gray-500 mb-4">Set a new password for <span class="font-semibold text-gray-800" x-text="resetUser.name"></span>. Share it with them in person.</p>
                <form :action="'{{ url('/admin/users') }}/' + resetUser.id + '/reset-password'" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    @if($search)<input type="hidden" name="search" value="{{ $search }}">@endif
                    @if($role)<input type="hidden" name="role" value="{{ $role }}">@endif
                    @if($approval)<input type="hidden" name="approval" value="{{ $approval }}">@endif
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">New password</label>
                        <input type="password" name="password" required minlength="8"
                               class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirm password</label>
                        <input type="password" name="password_confirmation" required minlength="8"
                               class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm">
                    </div>
                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="showResetPasswordModal = false"
                                class="flex-1 px-4 py-2.5 text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl">Cancel</button>
                        <button type="submit"
                                class="flex-1 px-4 py-2.5 text-sm font-semibold text-white bg-green-600 hover:bg-green-700 rounded-xl">Update Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>

<script>
    function userSearchComponent() {
        return {
            query: @json($search ?? ''),
            suggestions: [],
            showSuggestions: false,
            highlightedIndex: -1,
            debounceTimer: null,
            roleFilter() {
                const sel = document.querySelector('select[name=role]');
                return sel ? sel.value : '';
            },
            onInput() {
                clearTimeout(this.debounceTimer);
                this.debounceTimer = setTimeout(() => this.fetchSuggestions(), 250);
            },
            onFocus() {
                if (this.query.trim().length >= 2) this.fetchSuggestions();
            },
            submitSearch() {
                const form = this.$el.closest('form');
                if (form) form.submit();
            },
            clearSearch() {
                this.query = '';
                this.suggestions = [];
                this.showSuggestions = false;
                this.submitSearch();
            },
            async fetchSuggestions() {
                const q = this.query.trim();
                if (q.length < 2) {
                    this.suggestions = [];
                    this.showSuggestions = false;
                    return;
                }
                try {
                    const role = this.roleFilter();
                    let url = '/admin/users/search?q=' + encodeURIComponent(q);
                    if (role) url += '&role=' + encodeURIComponent(role);
                    const res = await fetch(url, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                        }
                    });
                    const data = await res.json();
                    this.suggestions = data.users || [];
                    this.showSuggestions = this.suggestions.length > 0;
                    this.highlightedIndex = -1;
                } catch (e) {
                    this.suggestions = [];
                }
            },
            selectSuggestion(suggestion) {
                this.query = suggestion.name;
                this.showSuggestions = false;
                this.submitSearch();
            },
            highlightNext() {
                if (!this.suggestions.length) return;
                this.highlightedIndex = (this.highlightedIndex + 1) % this.suggestions.length;
            },
            highlightPrev() {
                if (!this.suggestions.length) return;
                this.highlightedIndex = this.highlightedIndex <= 0 ? this.suggestions.length - 1 : this.highlightedIndex - 1;
            },
            selectHighlighted() {
                if (this.highlightedIndex >= 0 && this.highlightedIndex < this.suggestions.length) {
                    this.selectSuggestion(this.suggestions[this.highlightedIndex]);
                } else {
                    this.submitSearch();
                }
            }
        };
    }

    function updateRoleIdField() {
        const role = document.getElementById('role').value;
        const field = document.getElementById('role-id-field');
        const label = document.getElementById('role-id-label');
        const input = document.getElementById('student_id');

        if (role === 'student' || role === 'staff') {
            field.style.display = 'block';
            label.textContent = role === 'student' ? 'Student ID' : 'Staff / Employee ID';
            input.placeholder = role === 'student' ? 'e.g. 2024-12345' : 'e.g. EMP-001';
            input.required = true;
        } else {
            field.style.display = 'none';
            input.required = false;
            input.value = '';
        }
    }

    document.getElementById('role').addEventListener('change', updateRoleIdField);
    updateRoleIdField();
</script>
@endsection