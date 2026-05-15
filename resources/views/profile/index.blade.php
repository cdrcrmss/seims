@extends('layouts.app')

@section('title', 'User Profile')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-poppins">User Profile</h1>
            <p class="text-gray-600">Manage your personal information and settings</p>
        </div>
        <a href="{{ route('dashboard') }}"
           class="inline-flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Dashboard
        </a>
    </div>

    <div class="max-w-5xl">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Profile Information Card -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl ring-1 ring-gray-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100">
                        <h2 class="text-lg font-bold text-gray-900">Profile Information</h2>
                        <p class="text-sm text-gray-500 mt-0.5">Update your personal details and account information</p>
                    </div>
                    
                    <div class="p-6">
                        <form action="{{ route('profile.update') }}" method="POST" class="space-y-5">
                            @csrf
                            @method('PUT')
                            
                            <!-- Name -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                    Full Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                       class="w-full px-4 py-2.5 rounded-xl border border-gray-300 bg-white text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-green-500/30 focus:border-green-500 transition-colors text-sm">
                                @error('name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                    Email Address <span class="text-red-500">*</span>
                                </label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                       class="w-full px-4 py-2.5 rounded-xl border border-gray-300 bg-white text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-green-500/30 focus:border-green-500 transition-colors text-sm">
                                @error('email')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Student/Employee ID -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                    {{ $user->role === 'student' ? 'Student ID' : 'Employee ID' }}
                                </label>
                                <input type="text" name="student_id" value="{{ old('student_id', $user->student_id) }}"
                                       class="w-full px-4 py-2.5 rounded-xl border border-gray-300 bg-white text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-green-500/30 focus:border-green-500 transition-colors text-sm"
                                       placeholder="Enter your ID number">
                                @error('student_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Save Profile Button -->
                            <div class="pt-4 border-t border-gray-100">
                                <button type="submit"
                                        class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-sm hover:shadow-md transition-all duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Update Profile
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Security & Account Settings -->
            <div class="space-y-8">
                <!-- Account Info -->
                <div class="bg-white rounded-2xl ring-1 ring-gray-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-base font-bold text-gray-900">Account Details</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Role</span>
                            @php
                                $roleStyle = match($user->role) {
                                    'admin' => 'bg-red-100 text-red-800',
                                    'staff' => 'bg-blue-100 text-blue-800',
                                    default => 'bg-green-100 text-green-800',
                                };
                            @endphp
                            <span class="px-3 py-1 {{ $roleStyle }} rounded-full text-sm font-medium">
                                {{ ucfirst($user->role) }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Member Since</span>
                            <span class="text-gray-800 font-medium">{{ $user->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Last Updated</span>
                            <span class="text-gray-800 font-medium">{{ $user->updated_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Change Password -->
                <div class="bg-white rounded-2xl ring-1 ring-gray-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-base font-bold text-gray-900">Change Password</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Update your account password</p>
                    </div>
                    <div class="p-6">
                        <form action="{{ route('profile.password.update') }}" method="POST" class="space-y-6">
                            @csrf
                            @method('PUT')
                            
                            <!-- Current Password -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-800 mb-2">
                                    Current Password
                                </label>
                                <input type="password" name="current_password" required
                                       class="w-full px-4 py-3 rounded-xl border-2 border-gray-300 bg-gray-50 text-gray-900 focus:ring-4 focus:ring-green-500/30 focus:border-green-500 transition-all duration-300">
                                @error('current_password')
                                    <p class="text-red-500 text-sm mt-1 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- New Password -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-800 mb-2">
                                    New Password
                                </label>
                                <input type="password" name="password" required
                                       class="w-full px-4 py-3 rounded-xl border-2 border-gray-300 bg-gray-50 text-gray-900 focus:ring-4 focus:ring-green-500/30 focus:border-green-500 transition-all duration-300">
                                @error('password')
                                    <p class="text-red-500 text-sm mt-1 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-800 mb-2">
                                    Confirm Password
                                </label>
                                <input type="password" name="password_confirmation" required
                                       class="w-full px-4 py-3 rounded-xl border-2 border-gray-300 bg-gray-50 text-gray-900 focus:ring-4 focus:ring-green-500/30 focus:border-green-500 transition-all duration-300">
                            </div>

                            <!-- Change Password Button -->
                            <button type="submit"
                                    class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-sm hover:shadow-md transition-all duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                Change Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
