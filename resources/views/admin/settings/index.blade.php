@extends('layouts.app')

@section('title', 'System Settings')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-purple-900">
    <!-- Header -->
    <div class="bg-white/10 dark:bg-white/5 backdrop-blur-md border-b border-white/20 dark:border-white/10">
        <div class="max-w-7xl mx-auto px-6 py-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                        System Settings
                    </h1>
                    <p class="text-gray-600 dark:text-gray-300 mt-2">Configure system-wide settings and preferences</p>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-6 py-8">
        <!-- Settings Sections -->
        <div class="grid gap-8">
            
            <!-- General Settings -->
            <div class="bg-white/10 dark:bg-white/5 backdrop-blur-md rounded-2xl shadow-lg border border-white/20 dark:border-white/10">
                <div class="p-6 border-b border-gray-200/20 dark:border-gray-700/30">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">General Settings</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Basic application configuration</p>
                </div>
                
                <form action="{{ route('admin.settings.update') }}" method="POST" class="p-6 space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <!-- System Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            System Name
                        </label>
                        <input type="text" name="system_name" value="{{ config('app.name', 'INNOTRACK') }}"
                               class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white/50 dark:bg-gray-800/50 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                    </div>

                    <!-- Default User Role -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Default New User Role
                        </label>
                        <select name="default_role" 
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white/50 dark:bg-gray-800/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                            <option value="student">Student</option>
                            <option value="staff">Staff</option>
                        </select>
                    </div>

                    <!-- Maximum Borrowing Days -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Maximum Borrowing Days
                        </label>
                        <input type="number" name="max_borrowing_days" value="7" min="1" max="30"
                               class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white/50 dark:bg-gray-800/50 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Default number of days for equipment borrowing</p>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                                class="px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl hover:from-blue-600 hover:to-purple-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 shadow-lg">
                            Save General Settings
                        </button>
                    </div>
                </form>
            </div>

            <!-- Email Settings -->
            <div class="bg-white/10 dark:bg-white/5 backdrop-blur-md rounded-2xl shadow-lg border border-white/20 dark:border-white/10">
                <div class="p-6 border-b border-gray-200/20 dark:border-gray-700/30">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Email Notifications</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Configure email notification settings</p>
                </div>
                
                <div class="p-6 space-y-6">
                    <!-- Email Notifications Toggle -->
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">Email Notifications</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Send email notifications for borrowing activities</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    <!-- Overdue Notifications -->
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">Overdue Reminders</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Send reminders for overdue equipment</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Security Settings -->
            <div class="bg-white/10 dark:bg-white/5 backdrop-blur-md rounded-2xl shadow-lg border border-white/20 dark:border-white/10">
                <div class="p-6 border-b border-gray-200/20 dark:border-gray-700/30">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Security Settings</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">System security configuration</p>
                </div>
                
                <div class="p-6 space-y-6">
                    <!-- Session Timeout -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Session Timeout (minutes)
                        </label>
                        <select class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white/50 dark:bg-gray-800/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                            <option value="30">30 minutes</option>
                            <option value="60" selected>60 minutes</option>
                            <option value="120">2 hours</option>
                            <option value="480">8 hours</option>
                        </select>
                    </div>

                    <!-- Password Requirements -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                            Password Requirements
                        </label>
                        <div class="space-y-3">
                            <label class="flex items-center">
                                <input type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" checked>
                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-300">Minimum 8 characters</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" checked>
                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-300">Require uppercase letters</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" checked>
                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-300">Require numbers</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-300">Require special characters</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="button"
                                class="px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl hover:from-blue-600 hover:to-purple-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 shadow-lg">
                            Save Security Settings
                        </button>
                    </div>
                </div>
            </div>

            <!-- System Information -->
            <div class="bg-white/10 dark:bg-white/5 backdrop-blur-md rounded-2xl shadow-lg border border-white/20 dark:border-white/10">
                <div class="p-6 border-b border-gray-200/20 dark:border-gray-700/30">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">System Information</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Current system status and information</p>
                </div>
                
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Laravel Version</dt>
                            <dd class="text-sm text-gray-900 dark:text-white mt-1">{{ app()->version() }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">PHP Version</dt>
                            <dd class="text-sm text-gray-900 dark:text-white mt-1">{{ PHP_VERSION }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Environment</dt>
                            <dd class="text-sm text-gray-900 dark:text-white mt-1">{{ app()->environment() }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Debug Mode</dt>
                            <dd class="text-sm text-gray-900 dark:text-white mt-1">{{ config('app.debug') ? 'Enabled' : 'Disabled' }}</dd>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection