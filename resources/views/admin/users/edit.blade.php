@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-purple-900">
    <!-- Header -->
    <div class="bg-white/20 dark:bg-white/10 backdrop-blur-md border-b border-white/30 dark:border-white/20 shadow-lg">
        <div class="max-w-7xl mx-auto px-6 py-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                        Edit User: {{ $user->name }}
                    </h1>
                    <p class="text-gray-700 dark:text-gray-200 mt-3 text-lg">Update user information and permissions</p>
                </div>
                <a href="{{ route('admin.users.index') }}" 
                   class="px-8 py-4 bg-gray-600 hover:bg-gray-700 text-white rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105 font-semibold">
                    ← Back to Users
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-6 py-8">
        <div class="bg-white/25 dark:bg-white/10 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/40 dark:border-white/20 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500/10 to-purple-500/10 p-8 border-b border-white/20">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">User Information</h2>
                <p class="text-gray-600 dark:text-gray-300">Update the user's personal details and system permissions</p>
            </div>
            <div class="p-10">
                <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-8">
                    @csrf
                    @method('PUT')
                    
                    <!-- Name -->
                    <div class="bg-white/15 dark:bg-white/5 rounded-2xl p-6 border border-white/30">
                        <label class="block text-lg font-semibold text-gray-800 dark:text-white mb-3">
                            Full Name <span class="text-red-500 text-xl">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                               class="w-full px-6 py-4 rounded-xl border-2 border-gray-300 dark:border-gray-500 bg-white/70 dark:bg-gray-700/50 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-4 focus:ring-blue-500/30 focus:border-blue-500 transition-all duration-300 text-lg font-medium shadow-lg">
                        @error('name')
                            <p class="text-red-500 text-sm mt-2 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="bg-white/15 dark:bg-white/5 rounded-2xl p-6 border border-white/30">
                        <label class="block text-lg font-semibold text-gray-800 dark:text-white mb-3">
                            Email Address <span class="text-red-500 text-xl">*</span>
                        </label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                               class="w-full px-6 py-4 rounded-xl border-2 border-gray-300 dark:border-gray-500 bg-white/70 dark:bg-gray-700/50 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-4 focus:ring-blue-500/30 focus:border-blue-500 transition-all duration-300 text-lg font-medium shadow-lg">
                        @error('email')
                            <p class="text-red-500 text-sm mt-2 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="bg-white/15 dark:bg-white/5 rounded-2xl p-6 border border-white/30">
                        <label class="block text-lg font-semibold text-gray-800 dark:text-white mb-3">
                            Password
                        </label>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">Leave blank to keep current password</p>
                        <input type="password" name="password"
                               class="w-full px-6 py-4 rounded-xl border-2 border-gray-300 dark:border-gray-500 bg-white/70 dark:bg-gray-700/50 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-4 focus:ring-blue-500/30 focus:border-blue-500 transition-all duration-300 text-lg font-medium shadow-lg"
                               placeholder="Enter new password">
                        @error('password')
                            <p class="text-red-500 text-sm mt-2 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="bg-white/15 dark:bg-white/5 rounded-2xl p-6 border border-white/30">
                        <label class="block text-lg font-semibold text-gray-800 dark:text-white mb-3">
                            Confirm Password
                        </label>
                        <input type="password" name="password_confirmation"
                               class="w-full px-6 py-4 rounded-xl border-2 border-gray-300 dark:border-gray-500 bg-white/70 dark:bg-gray-700/50 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-4 focus:ring-blue-500/30 focus:border-blue-500 transition-all duration-300 text-lg font-medium shadow-lg"
                               placeholder="Confirm new password">
                    </div>

                    <!-- Role -->
                    <div class="bg-white/15 dark:bg-white/5 rounded-2xl p-6 border border-white/30">
                        <label class="block text-lg font-semibold text-gray-800 dark:text-white mb-3">
                            User Role <span class="text-red-500 text-xl">*</span>
                        </label>
                        <select name="role" required
                                class="w-full px-6 py-4 rounded-xl border-2 border-gray-300 dark:border-gray-500 bg-white/70 dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-4 focus:ring-blue-500/30 focus:border-blue-500 transition-all duration-300 text-lg font-medium shadow-lg">
                            <option value="student" {{ old('role', $user->role) == 'student' ? 'selected' : '' }}>Student</option>
                            <option value="staff" {{ old('role', $user->role) == 'staff' ? 'selected' : '' }}>Staff Member</option>
                            <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Administrator</option>
                        </select>
                        @error('role')
                            <p class="text-red-500 text-sm mt-2 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Student ID -->
                    <div class="bg-white/15 dark:bg-white/5 rounded-2xl p-6 border border-white/30">
                        <label class="block text-lg font-semibold text-gray-800 dark:text-white mb-3">
                            Student/Employee ID
                        </label>
                        <input type="text" name="student_id" value="{{ old('student_id', $user->student_id) }}"
                               class="w-full px-6 py-4 rounded-xl border-2 border-gray-300 dark:border-gray-500 bg-white/70 dark:bg-gray-700/50 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-4 focus:ring-blue-500/30 focus:border-blue-500 transition-all duration-300 text-lg font-medium shadow-lg"
                               placeholder="Enter ID number">
                        @error('student_id')
                            <p class="text-red-500 text-sm mt-2 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end space-x-6 pt-8 border-t border-white/20">
                        <a href="{{ route('admin.users.index') }}" 
                           class="px-8 py-4 bg-gray-600 hover:bg-gray-700 text-white rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 font-semibold text-lg">
                            Cancel
                        </a>
                        <button type="submit"
                                class="px-10 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl hover:from-blue-700 hover:to-purple-700 focus:ring-4 focus:ring-blue-500/30 transition-all duration-300 shadow-xl hover:shadow-2xl transform hover:scale-105 font-semibold text-lg">
                            Update User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection