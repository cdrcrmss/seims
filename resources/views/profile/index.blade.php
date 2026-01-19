@extends('layouts.app')

@section('title', 'User Profile')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-purple-900">
    <!-- Header -->
    <div class="bg-white/20 dark:bg-white/10 backdrop-blur-md border-b border-white/30 dark:border-white/20 shadow-lg">
        <div class="max-w-7xl mx-auto px-6 py-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center mr-6 shadow-xl">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                            User Profile
                        </h1>
                        <p class="text-gray-700 dark:text-gray-200 mt-2 text-lg">Manage your personal information and settings</p>
                    </div>
                </div>
                <a href="{{ route('dashboard') }}" 
                   class="px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105 font-semibold">
                    📊 Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-6xl mx-auto px-6 py-8">
        <!-- Success Messages -->
        @if(session('success'))
            <div class="mb-8 bg-green-500/20 border border-green-500/30 rounded-xl p-6 text-green-800 dark:text-green-200 backdrop-blur-sm">
                <div class="flex items-center">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Profile Information Card -->
            <div class="lg:col-span-2">
                <div class="bg-white/25 dark:bg-white/10 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/40 dark:border-white/20 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-500/10 to-purple-500/10 p-8 border-b border-white/20">
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">Profile Information</h2>
                        <p class="text-gray-600 dark:text-gray-300">Update your personal details and account information</p>
                    </div>
                    
                    <div class="p-10">
                        <form action="{{ route('profile.update') }}" method="POST" class="space-y-8">
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

                            <!-- Student/Employee ID -->
                            <div class="bg-white/15 dark:bg-white/5 rounded-2xl p-6 border border-white/30">
                                <label class="block text-lg font-semibold text-gray-800 dark:text-white mb-3">
                                    {{ $user->role === 'student' ? 'Student ID' : 'Employee ID' }}
                                </label>
                                <input type="text" name="student_id" value="{{ old('student_id', $user->student_id) }}"
                                       class="w-full px-6 py-4 rounded-xl border-2 border-gray-300 dark:border-gray-500 bg-white/70 dark:bg-gray-700/50 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-4 focus:ring-blue-500/30 focus:border-blue-500 transition-all duration-300 text-lg font-medium shadow-lg"
                                       placeholder="Enter your ID number">
                                @error('student_id')
                                    <p class="text-red-500 text-sm mt-2 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Save Profile Button -->
                            <div class="pt-6">
                                <button type="submit"
                                        class="w-full px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl hover:from-blue-700 hover:to-purple-700 focus:ring-4 focus:ring-blue-500/30 transition-all duration-300 shadow-xl hover:shadow-2xl transform hover:scale-105 font-semibold text-lg">
                                    💾 Update Profile
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Security & Account Settings -->
            <div class="space-y-8">
                <!-- Account Info -->
                <div class="bg-white/25 dark:bg-white/10 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/40 dark:border-white/20 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-500/10 to-blue-500/10 p-6 border-b border-white/20">
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white">Account Details</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 dark:text-gray-300">Role</span>
                            <span class="px-3 py-1 bg-{{ $user->role === 'admin' ? 'red' : ($user->role === 'staff' ? 'blue' : 'green') }}-100 dark:bg-{{ $user->role === 'admin' ? 'red' : ($user->role === 'staff' ? 'blue' : 'green') }}-800/20 text-{{ $user->role === 'admin' ? 'red' : ($user->role === 'staff' ? 'blue' : 'green') }}-800 dark:text-{{ $user->role === 'admin' ? 'red' : ($user->role === 'staff' ? 'blue' : 'green') }}-300 rounded-full text-sm font-medium">
                                {{ ucfirst($user->role) }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 dark:text-gray-300">Member Since</span>
                            <span class="text-gray-800 dark:text-white font-medium">{{ $user->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 dark:text-gray-300">Last Updated</span>
                            <span class="text-gray-800 dark:text-white font-medium">{{ $user->updated_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Change Password -->
                <div class="bg-white/25 dark:bg-white/10 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/40 dark:border-white/20 overflow-hidden">
                    <div class="bg-gradient-to-r from-orange-500/10 to-red-500/10 p-6 border-b border-white/20">
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white">Change Password</h3>
                        <p class="text-gray-600 dark:text-gray-300 text-sm mt-1">Update your account password</p>
                    </div>
                    <div class="p-6">
                        <form action="{{ route('profile.password.update') }}" method="POST" class="space-y-6">
                            @csrf
                            @method('PUT')
                            
                            <!-- Current Password -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-800 dark:text-white mb-2">
                                    Current Password
                                </label>
                                <input type="password" name="current_password" required
                                       class="w-full px-4 py-3 rounded-xl border-2 border-gray-300 dark:border-gray-500 bg-white/70 dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all duration-300">
                                @error('current_password')
                                    <p class="text-red-500 text-sm mt-1 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- New Password -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-800 dark:text-white mb-2">
                                    New Password
                                </label>
                                <input type="password" name="password" required
                                       class="w-full px-4 py-3 rounded-xl border-2 border-gray-300 dark:border-gray-500 bg-white/70 dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all duration-300">
                                @error('password')
                                    <p class="text-red-500 text-sm mt-1 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-800 dark:text-white mb-2">
                                    Confirm Password
                                </label>
                                <input type="password" name="password_confirmation" required
                                       class="w-full px-4 py-3 rounded-xl border-2 border-gray-300 dark:border-gray-500 bg-white/70 dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all duration-300">
                            </div>

                            <!-- Change Password Button -->
                            <button type="submit"
                                    class="w-full px-6 py-3 bg-gradient-to-r from-orange-600 to-red-600 text-white rounded-xl hover:from-orange-700 hover:to-red-700 focus:ring-4 focus:ring-orange-500/30 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 font-semibold">
                                🔒 Change Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection