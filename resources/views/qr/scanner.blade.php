@extends('layouts.app')

@section('title', 'QR Scanner')

@section('content')
<div class="space-y-8" x-data="qrScanner()">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 animate-fade-in-up">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-poppins">QR Code Scanner</h1>
            <p class="text-gray-600">Scan equipment QR codes for instant identification & validation</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Scanner Panel -->
        <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-6 animate-fade-in-up stagger-1">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center space-x-2">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                <span>Camera Scanner</span>
            </h2>
            
            <div id="qr-reader" class="rounded-xl overflow-hidden mb-4" style="min-height: 300px;"></div>
            
            <style>
                #qr-reader video {
                    transform: scaleX(-1); /* Mirror the camera */
                }
            </style>

            <div class="flex space-x-3">
                <button @click="startScanner()" x-show="!cameraActive" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2.5 rounded-xl font-semibold transition-all duration-200 flex items-center justify-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    <span>Start Camera</span>
                </button>
                <button @click="stopScanner()" x-show="cameraActive" class="flex-1 bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 rounded-xl font-semibold transition-all duration-200 flex items-center justify-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"></path></svg>
                    <span>Stop Camera</span>
                </button>
            </div>

            <!-- Manual Code Entry -->
            <div class="mt-6 pt-6 border-t border-gray-100">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Or enter QR code manually</h3>
                <div class="flex space-x-3">
                    <input type="text" x-model="manualCode" @keydown.enter.prevent="lookupByCode()" placeholder="e.g., SEIS-000001-ABCD1234" class="flex-1 border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all">
                    <button @click="lookupByCode()" class="bg-gray-900 hover:bg-gray-800 text-white px-5 py-2.5 rounded-xl font-semibold transition-all duration-200">
                        Lookup
                    </button>
                </div>
            </div>
        </div>

        <!-- Result Panel -->
        <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-6 animate-fade-in-up stagger-2">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center space-x-2">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span>Scan Result</span>
            </h2>

            <template x-if="!scannedItem && !loading && !error">
                <div class="flex flex-col items-center justify-center h-64 text-gray-400">
                    <svg class="w-16 h-16 mb-4 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                    <p>No item scanned yet</p>
                    <p class="text-sm mt-1">Scan a QR code or enter it manually</p>
                </div>
            </template>

            <template x-if="loading">
                <div class="flex items-center justify-center h-64">
                    <div class="animate-spin w-8 h-8 border-4 border-green-600 border-t-transparent rounded-full"></div>
                </div>
            </template>

            <template x-if="scannedItem && !loading">
                <div class="space-y-4">
                    <!-- Item Header -->
                    <div class="flex items-center space-x-4 pb-4 border-b border-gray-100">
                        <div class="w-14 h-14 rounded-xl overflow-hidden ring-1 ring-gray-200 bg-gray-100 shrink-0">
                            <img x-show="scannedItem.image_url"
                                 :src="scannedItem.image_url"
                                 :alt="scannedItem.name"
                                 class="w-full h-full object-cover">
                            <div x-show="!scannedItem.image_url"
                                 class="w-full h-full flex items-center justify-center bg-gray-100 text-gray-400">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            </div>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-lg font-bold text-gray-900" x-text="scannedItem.name"></h3>
                            <p class="text-sm text-gray-500" x-text="scannedItem.category"></p>
                        </div>
                    </div>

                    <!-- Item Details Grid -->
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-gray-50 rounded-xl p-3">
                            <p class="text-xs text-gray-500 uppercase tracking-wide">Status</p>
                            <p class="text-sm font-semibold mt-1" :class="scannedItem.available_stock > 0 ? 'text-green-600' : 'text-red-600'" x-text="scannedItem.available_stock > 0 ? 'Available' : 'Out of Stock'"></p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-3">
                            <p class="text-xs text-gray-500 uppercase tracking-wide">Available</p>
                            <p class="text-sm font-semibold text-gray-900 mt-1" x-text="scannedItem.available_stock + (scannedItem.total_stock ? ' / ' + scannedItem.total_stock : '')"></p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-3">
                            <p class="text-xs text-gray-500 uppercase tracking-wide">Location</p>
                            <p class="text-sm font-semibold text-gray-900 mt-1" x-text="scannedItem.location || 'N/A'"></p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-3" x-show="scannedItem.qr_code">
                            <p class="text-xs text-gray-500 uppercase tracking-wide">QR ID</p>
                            <p class="text-xs font-mono font-semibold text-gray-900 mt-1" x-text="scannedItem.qr_code"></p>
                        </div>
                    </div>

                    <!-- Unit-specific info (when a unit QR is scanned) -->
                    <div class="bg-indigo-50 rounded-xl p-3 ring-1 ring-indigo-100" x-show="scannedItem.unit">
                        <p class="text-xs text-indigo-600 uppercase tracking-wide font-bold mb-2 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                            Unit Details
                        </p>
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div>
                                <p class="text-xs text-gray-500">Unit Code</p>
                                <p class="font-mono font-semibold text-gray-900 text-xs" x-text="scannedItem.unit?.unit_code"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Condition</p>
                                <p class="font-semibold text-gray-900 capitalize" x-text="scannedItem.unit?.condition"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Status</p>
                                <p class="font-semibold capitalize" :class="scannedItem.unit?.status === 'available' ? 'text-green-600' : 'text-amber-600'" x-text="scannedItem.unit?.status"></p>
                            </div>
                            <div x-show="scannedItem.unit?.current_borrower">
                                <p class="text-xs text-gray-500">Held By</p>
                                <p class="font-semibold text-gray-900" x-text="scannedItem.unit?.current_borrower"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Current Holder(s) -->
                    <div class="bg-blue-50 rounded-xl p-3 ring-1 ring-blue-100" x-show="scannedItem.current_holders && scannedItem.current_holders.length > 0">
                        <p class="text-xs text-blue-600 uppercase tracking-wide font-bold mb-2 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Currently Held By
                        </p>
                        <template x-for="holder in (scannedItem.current_holders || [])" :key="holder.user_name">
                            <div class="flex items-center justify-between py-1.5 border-b border-blue-100 last:border-0">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900" x-text="holder.user_name"></p>
                                    <p class="text-xs text-gray-500">Qty: <span x-text="holder.quantity"></span></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-[10px] text-gray-500 uppercase">Due</p>
                                    <p class="text-xs font-semibold text-gray-700" x-text="holder.expected_return_date"></p>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Wear Level (staff/admin only) -->
                    <div class="bg-gray-50 rounded-xl p-3" x-show="scannedItem.wear_level !== undefined">
                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Wear Level</p>
                        <div class="flex items-center space-x-2">
                            <div class="flex-1 bg-gray-200 rounded-full h-2">
                                <div class="h-2 rounded-full" :class="(scannedItem.wear_level || 0) >= 70 ? 'bg-red-500' : (scannedItem.wear_level || 0) >= 40 ? 'bg-yellow-500' : 'bg-green-500'" :style="'width:' + (scannedItem.wear_level || 0) + '%'"></div>
                            </div>
                            <span class="text-sm font-semibold" x-text="(scannedItem.wear_level || 0) + '%'"></span>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="bg-gray-50 rounded-xl p-3" x-show="scannedItem.description">
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Description</p>
                        <p class="text-sm text-gray-700 mt-1" x-text="scannedItem.description"></p>
                    </div>

                    <!-- Borrow Button (for students) -->
                    @if(auth()->user()->role === 'student')
                    <div class="pt-4 border-t border-gray-100" x-show="scannedItem.available_stock > 0">
                        <button @click="showBorrowModal = true" class="w-full bg-green-600 hover:bg-green-700 text-white px-5 py-3 rounded-xl font-semibold transition-all duration-200 flex items-center justify-center space-x-2 shadow-sm hover:shadow-md">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5v-1a1.5 1.5 0 013 0v1m0 0V11m0-5.5a1.5 1.5 0 013 0v3m0 0V11"></path></svg>
                            <span>Borrow This Item</span>
                        </button>
                    </div>
                    <div class="pt-4 border-t border-gray-100" x-show="scannedItem.available_stock <= 0">
                        <div class="w-full bg-red-50 border border-red-200 text-red-700 px-5 py-3 rounded-xl text-center text-sm font-medium">
                            This item is currently out of stock
                        </div>
                    </div>
                    @endif

                    <!-- Staff/Admin Actions -->
                    @if(auth()->user()->role !== 'student')
                    <!-- Quick Actions -->
                    <div class="pt-4 border-t border-gray-100">
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">Quick Actions</h4>
                        <div class="grid grid-cols-2 gap-3">
                            <a :href="'/staff/items/' + scannedItem.id + '/edit'" class="flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-xl font-semibold transition-all duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                Edit Item
                            </a>
                            <a :href="'/staff/borrow?item_id=' + scannedItem.id" x-show="scannedItem.available_stock > 0" class="flex items-center justify-center gap-2 bg-purple-600 hover:bg-purple-700 text-white px-4 py-3 rounded-xl font-semibold transition-all duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5v-1a1.5 1.5 0 013 0v1m0 0V11m0-5.5a1.5 1.5 0 013 0v3m0 0V11"></path></svg>
                                Borrow
                            </a>
                            <a :href="'/maintenance/create?item_id=' + scannedItem.id" class="flex items-center justify-center gap-2 bg-orange-500 hover:bg-orange-600 text-white px-4 py-3 rounded-xl font-semibold transition-all duration-200 ring-1 ring-orange-300">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                Maintenance
                            </a>
                            <a :href="'/qr/generate/' + scannedItem.id" class="col-span-2 flex items-center justify-center gap-2 bg-gray-900 hover:bg-gray-800 text-white px-4 py-3 rounded-xl font-semibold transition-all duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                View QR Code
                            </a>
                        </div>
                    </div>

                    <!-- Pending Issuances -->
                    <div class="pt-4 border-t border-gray-100" x-show="scannedItem.pending_borrowings && scannedItem.pending_borrowings.length > 0">
                        <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center space-x-2">
                            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>Pending Issuances</span>
                        </h4>
                        <template x-for="borrowing in (scannedItem.pending_borrowings || [])" :key="borrowing.id">
                            <div class="bg-amber-50 rounded-xl p-3 mb-2 flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-900" x-text="borrowing.user_name"></p>
                                    <p class="text-xs text-gray-500">Qty: <span x-text="borrowing.quantity"></span> | Status: <span x-text="borrowing.status" class="capitalize"></span></p>
                                </div>
                                <form method="POST" :action="'/staff/borrowings/' + borrowing.id + '/issue'" x-show="borrowing.status === 'approved'">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg text-sm font-semibold transition-all">
                                        Issue
                                    </button>
                                </form>
                            </div>
                        </template>
                    </div>

                    <!-- Active Borrowings -->
                    <div class="pt-4 border-t border-gray-100" x-show="scannedItem.issued_borrowings && scannedItem.issued_borrowings.length > 0">
                        <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center space-x-2">
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>Active Borrowings (Ready for Return)</span>
                        </h4>
                        <template x-for="borrowing in (scannedItem.issued_borrowings || [])" :key="borrowing.id">
                            <div class="bg-green-50 rounded-xl p-3 mb-2 flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-900" x-text="borrowing.user_name"></p>
                                    <p class="text-xs text-gray-500">
                                        Qty: <span x-text="borrowing.quantity"></span>
                                        <template x-if="borrowing.unit_code"><span> · Unit <span class="font-mono" x-text="borrowing.unit_code"></span></span></template>
                                        | Due: <span x-text="borrowing.expected_return_date"></span>
                                    </p>
                                </div>
                                <button type="button" @click="openReturnModalFromScan(borrowing.id)" class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-1.5 rounded-lg text-sm font-semibold transition-all">
                                    Return
                                </button>
                            </div>
                        </template>
                    </div>
                    @endif
                </div>
            </template>

            <template x-if="error && !loading">
                <div class="flex flex-col items-center justify-center h-64 text-red-500">
                    <svg class="w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <p x-text="error"></p>
                    <button @click="error = null" class="mt-3 text-sm text-gray-500 hover:text-gray-700 underline">Dismiss</button>
                </div>
            </template>
        </div>
    </div>

    <!-- Borrow Modal (for students) -->
    @if(auth()->user()->role === 'student')
    <div x-show="showBorrowModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-900/75 backdrop-blur-sm" @click="showBorrowModal = false"></div>
            
            <div class="relative z-10 w-full max-w-md bg-white rounded-3xl shadow-2xl transform transition-all"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95">
                <div class="p-6 border-b border-gray-200 rounded-t-3xl">
                    <h3 class="text-xl font-semibold text-gray-900">Borrow Item</h3>
                    <p class="text-sm text-gray-500 mt-1">Submit a borrowing request for this equipment</p>
                </div>
                <form method="POST" action="{{ route('student.borrow') }}" class="p-6 space-y-4">
                    @csrf
                    <input type="hidden" name="item_id" :value="scannedItem ? scannedItem.id : ''">
                    
                    <div class="bg-gray-50 rounded-xl p-4 flex items-center space-x-3">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900" x-text="scannedItem ? scannedItem.name : ''"></p>
                            <p class="text-xs text-gray-500" x-text="scannedItem ? scannedItem.category : ''"></p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Quantity <span class="text-red-500">*</span></label>
                        <input type="number" name="quantity" min="1" :max="scannedItem ? scannedItem.available_stock : 1" value="1" 
                               class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500" required>
                        <p class="text-xs text-gray-400 mt-1">Available: <span x-text="scannedItem ? scannedItem.available_stock : 0"></span></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Expected Return Date <span class="text-red-500">*</span></label>
                        <input type="date" name="expected_return_date" 
                               min="{{ now()->addDay()->toDateString() }}" 
                               max="{{ now()->addDays(7)->toDateString() }}"
                               class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Purpose <span class="text-red-500">*</span></label>
                        <textarea name="purpose" rows="2" minlength="10" maxlength="100"
                                  class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                  placeholder="What will you use this equipment for? (min 10 characters)" required></textarea>
                    </div>

                    <div class="flex items-center space-x-3 pt-2">
                        <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-xl font-semibold transition-all duration-200 shadow-sm">
                            Submit Borrow Request
                        </button>
                        <button type="button" @click="showBorrowModal = false" class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
function qrScanner() {
    return {
        cameraActive: false,
        manualCode: '',
        scannedItem: null,
        loading: false,
        error: null,
        scanner: null,
        showBorrowModal: false,

        startScanner() {
            this.scanner = new Html5Qrcode("qr-reader");
            
            // Get available cameras and select the back camera
            Html5Qrcode.getCameras().then(cameras => {
                if (cameras && cameras.length) {
                    // Find the back camera (usually has 'environment' or 'back' in label/id)
                    const backCamera = cameras.find(camera => 
                        camera.label.toLowerCase().includes('back') || 
                        camera.label.toLowerCase().includes('environment') ||
                        camera.label.toLowerCase().includes('rear')
                    ) || cameras[0];
                    
                    const config = backCamera.id 
                        ? { deviceId: { exact: backCamera.id } }
                        : { facingMode: "environment" };
                    
                    this.scanner.start(
                        config,
                        { fps: 10, qrbox: { width: 250, height: 250 } },
                        (decodedText) => {
                            this.handleScan(decodedText);
                        },
                        (errorMessage) => {}
                    ).then(() => {
                        this.cameraActive = true;
                    }).catch((err) => {
                        this.error = 'Camera access denied. Please allow camera permissions.';
                    });
                }
            }).catch(err => {
                this.error = 'Camera access denied. Please allow camera permissions.';
            });
        },

        stopScanner() {
            if (this.scanner) {
                this.scanner.stop().then(() => {
                    this.cameraActive = false;
                }).catch(() => {
                    this.cameraActive = false;
                });
            }
        },

        async handleScan(decodedText) {
            // Pause scanning while we process
            if (this.loading) return;
            
            // The QR code might contain a URL (e.g., /qr/lookup/5) or a code string (e.g., SEIS-000001-ABCDEF)
            let code = decodedText;
            
            // If it's a URL, extract the item ID
            const urlMatch = decodedText.match(/\/qr\/lookup\/(\d+)/);
            if (urlMatch) {
                await this.lookupById(urlMatch[1]);
                return;
            }
            
            // Otherwise treat as QR code string
            this.manualCode = code;
            await this.lookupByCode();
        },

        async lookupById(id) {
            this.loading = true;
            this.error = null;
            this.scannedItem = null;

            try {
                const resp = await fetch('/qr/lookup/' + id, {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                const data = await resp.json();
                if (data.success) {
                    this.scannedItem = data.item;
                } else {
                    this.error = data.message || 'Item not found.';
                }
            } catch (e) {
                this.error = 'Failed to lookup item. Please try again.';
            }
            this.loading = false;
        },

        async lookupByCode() {
            if (!this.manualCode.trim()) return;
            this.loading = true;
            this.error = null;
            this.scannedItem = null;

            try {
                const resp = await fetch('/qr/lookup?code=' + encodeURIComponent(this.manualCode.trim()), {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                const data = await resp.json();
                if (data.success) {
                    this.scannedItem = data.item;
                } else {
                    this.error = data.message || 'Item not found.';
                }
            } catch (e) {
                this.error = 'Failed to lookup item. Please try again.';
            }
            this.loading = false;
        }
    };
}

// Return modal functions for QR-based returns
function openReturnModalFromScan(borrowingId) {
    document.getElementById('scanReturnBorrowingId').value = borrowingId;
    document.getElementById('scanReturnForm').action = `/staff/borrowings/${borrowingId}/return`;
    document.getElementById('scanReturnModal').classList.remove('hidden');
}

function closeScanReturnModal() {
    document.getElementById('scanReturnModal').classList.add('hidden');
    document.getElementById('scanReturnForm').reset();
}
</script>

<!-- Return Modal with Condition Selection (for QR Scanner) -->
@if(auth()->user()->isStaff() || auth()->user()->isAdmin())
<div id="scanReturnModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-900/80 backdrop-blur-sm" onclick="closeScanReturnModal()"></div>
        
        <div class="relative z-10 w-full max-w-md bg-white rounded-2xl shadow-2xl">
            <form id="scanReturnForm" method="POST" action="">
                @csrf
                @method('PATCH')
                <input type="hidden" id="scanReturnBorrowingId" name="borrowing_id" value="">
                
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900">Return Item via QR Scan</h3>
                    <p class="text-sm text-gray-500 mt-1">Please inspect the item and report its condition</p>
                </div>
                
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Item Condition <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="relative flex items-center justify-center p-3 border-2 rounded-xl cursor-pointer hover:bg-green-50 transition-colors">
                                <input type="radio" name="return_condition" value="good" class="sr-only peer" required>
                                <div class="text-center peer-checked:text-green-600">
                                    <svg class="w-8 h-8 mx-auto mb-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span class="text-sm font-medium">Good</span>
                                </div>
                                <span class="absolute inset-0 border-2 border-transparent peer-checked:border-green-500 rounded-xl"></span>
                            </label>
                            <label class="relative flex items-center justify-center p-3 border-2 rounded-xl cursor-pointer hover:bg-yellow-50 transition-colors">
                                <input type="radio" name="return_condition" value="fair" class="sr-only peer">
                                <div class="text-center peer-checked:text-yellow-600">
                                    <svg class="w-8 h-8 mx-auto mb-1 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span class="text-sm font-medium">Fair</span>
                                </div>
                                <span class="absolute inset-0 border-2 border-transparent peer-checked:border-yellow-500 rounded-xl"></span>
                            </label>
                            <label class="relative flex items-center justify-center p-3 border-2 rounded-xl cursor-pointer hover:bg-orange-50 transition-colors">
                                <input type="radio" name="return_condition" value="needs_repair" class="sr-only peer">
                                <div class="text-center peer-checked:text-orange-600">
                                    <svg class="w-8 h-8 mx-auto mb-1 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    <span class="text-sm font-medium">Needs Repair</span>
                                </div>
                                <span class="absolute inset-0 border-2 border-transparent peer-checked:border-orange-500 rounded-xl"></span>
                            </label>
                            <label class="relative flex items-center justify-center p-3 border-2 rounded-xl cursor-pointer hover:bg-red-50 transition-colors">
                                <input type="radio" name="return_condition" value="damaged" class="sr-only peer">
                                <div class="text-center peer-checked:text-red-600">
                                    <svg class="w-8 h-8 mx-auto mb-1 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                    <span class="text-sm font-medium">Damaged</span>
                                </div>
                                <span class="absolute inset-0 border-2 border-transparent peer-checked:border-red-500 rounded-xl"></span>
                            </label>
                        </div>
                    </div>
                    
                    <div>
                        <label for="scan_return_notes" class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                        <textarea id="scan_return_notes" name="return_notes" rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                  placeholder="Any additional notes about the item's condition..."></textarea>
                    </div>
                </div>
                
                <div class="flex justify-end gap-3 p-6 border-t border-gray-200">
                    <button type="button" onclick="closeScanReturnModal()" 
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl hover:from-green-600 hover:to-green-700 transition-colors shadow-md">
                        Confirm Return
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
