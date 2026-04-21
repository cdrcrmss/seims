@extends('layouts.app')

@section('title', 'QR Scanner')

@section('content')
<div class="space-y-8" x-data="qrScanner()">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 animate-fade-in-up">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-poppins">QR Scanner</h1>
            <p class="text-gray-600">Scan to issue, return, check-in, or look up assets</p>
        </div>
        <div class="flex space-x-3">
            @if(in_array(auth()->user()->role, ['staff', 'admin']))
            <a href="{{ route('qr.scan-history') }}" class="bg-gray-900 hover:bg-gray-800 text-white px-5 py-2.5 rounded-xl font-semibold transition-all duration-200 shadow-sm hover:shadow-md flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>Scan Log</span>
            </a>
            <form method="POST" action="{{ route('qr.batch-generate') }}">
                @csrf
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-xl font-semibold transition-all duration-200 shadow-sm hover:shadow-md flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>Batch Generate</span>
                </button>
            </form>
            @endif
        </div>
    </div>

    <!-- Action Mode Selector -->
    <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-4 animate-fade-in-up">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Scan Mode</p>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
            <button @click="scanMode = 'lookup'" 
                    :class="scanMode === 'lookup' ? 'ring-2 ring-gray-900 bg-gray-50' : 'hover:bg-gray-50'"
                    class="flex flex-col items-center p-3 rounded-xl border border-gray-200 transition-all">
                <svg class="w-6 h-6 mb-1 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <span class="text-xs font-semibold text-gray-700">Lookup</span>
            </button>
            @if(in_array(auth()->user()->role, ['staff', 'admin']))
            <button @click="scanMode = 'issue'" 
                    :class="scanMode === 'issue' ? 'ring-2 ring-blue-500 bg-blue-50' : 'hover:bg-blue-50'"
                    class="flex flex-col items-center p-3 rounded-xl border border-gray-200 transition-all">
                <svg class="w-6 h-6 mb-1 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span class="text-xs font-semibold text-gray-700">Issue</span>
            </button>
            <button @click="scanMode = 'return'" 
                    :class="scanMode === 'return' ? 'ring-2 ring-purple-500 bg-purple-50' : 'hover:bg-purple-50'"
                    class="flex flex-col items-center p-3 rounded-xl border border-gray-200 transition-all">
                <svg class="w-6 h-6 mb-1 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                <span class="text-xs font-semibold text-gray-700">Return</span>
            </button>
            @endif
            <button @click="scanMode = 'room_check_in'" 
                    :class="scanMode === 'room_check_in' ? 'ring-2 ring-green-500 bg-green-50' : 'hover:bg-green-50'"
                    class="flex flex-col items-center p-3 rounded-xl border border-gray-200 transition-all">
                <svg class="w-6 h-6 mb-1 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                <span class="text-xs font-semibold text-gray-700">Room Check-in</span>
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Scanner Panel -->
        <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-6 animate-fade-in-up stagger-1">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center space-x-2">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                <span>Camera Scanner</span>
            </h2>
            
            <div class="relative bg-gray-900 rounded-xl overflow-hidden aspect-video mb-4">
                <video x-ref="video" class="w-full h-full object-cover" autoplay playsinline></video>
                <canvas x-ref="canvas" class="hidden"></canvas>
                
                <!-- Scan overlay -->
                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                    <div class="w-48 h-48 border-2 border-green-400 rounded-xl relative">
                        <div class="absolute top-0 left-0 w-6 h-6 border-t-4 border-l-4 border-green-400 rounded-tl-lg"></div>
                        <div class="absolute top-0 right-0 w-6 h-6 border-t-4 border-r-4 border-green-400 rounded-tr-lg"></div>
                        <div class="absolute bottom-0 left-0 w-6 h-6 border-b-4 border-l-4 border-green-400 rounded-bl-lg"></div>
                        <div class="absolute bottom-0 right-0 w-6 h-6 border-b-4 border-r-4 border-green-400 rounded-br-lg"></div>
                    </div>
                </div>
                
                <div x-show="!cameraActive" class="absolute inset-0 flex items-center justify-center bg-gray-900/80">
                    <div class="text-center text-white">
                        <svg class="w-12 h-12 mx-auto mb-3 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                        <p class="text-sm opacity-80">Click Start Camera to begin scanning</p>
                    </div>
                </div>
            </div>

            <div class="flex space-x-3">
                <button @click="startCamera()" x-show="!cameraActive" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2.5 rounded-xl font-semibold transition-all duration-200 flex items-center justify-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    <span>Start Camera</span>
                </button>
                <button @click="stopCamera()" x-show="cameraActive" class="flex-1 bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 rounded-xl font-semibold transition-all duration-200 flex items-center justify-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"></path></svg>
                    <span>Stop Camera</span>
                </button>
            </div>

            <!-- Manual Code Entry -->
            <div class="mt-6 pt-6 border-t border-gray-100">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Or enter QR code manually</h3>
                <div class="flex space-x-3">
                    <input type="text" x-model="manualCode" placeholder="e.g., INNO-A1B2C3D4" class="flex-1 border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all">
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

            <!-- Empty State -->
            <template x-if="!scanResult && !loading">
                <div class="flex flex-col items-center justify-center h-64 text-gray-400">
                    <svg class="w-16 h-16 mb-4 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                    <p>No item scanned yet</p>
                    <p class="text-sm mt-1">Select a mode and scan a QR code</p>
                </div>
            </template>

            <!-- Loading -->
            <template x-if="loading">
                <div class="flex items-center justify-center h-64">
                    <div class="animate-spin w-8 h-8 border-4 border-green-600 border-t-transparent rounded-full"></div>
                </div>
            </template>

            <!-- Outcome Display -->
            <template x-if="scanResult && !loading">
                <div class="space-y-4">
                    <!-- Outcome Banner -->
                    <div class="rounded-xl p-4 flex items-start space-x-3"
                         :class="{
                            'bg-green-50 ring-1 ring-green-200': scanResult.outcome === 'success',
                            'bg-yellow-50 ring-1 ring-yellow-200': scanResult.outcome === 'warning',
                            'bg-red-50 ring-1 ring-red-200': scanResult.outcome === 'blocked'
                         }">
                        <!-- Icon -->
                        <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center"
                             :class="{
                                'bg-green-100': scanResult.outcome === 'success',
                                'bg-yellow-100': scanResult.outcome === 'warning',
                                'bg-red-100': scanResult.outcome === 'blocked'
                             }">
                            <template x-if="scanResult.outcome === 'success'">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </template>
                            <template x-if="scanResult.outcome === 'warning'">
                                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </template>
                            <template x-if="scanResult.outcome === 'blocked'">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                            </template>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-bold"
                               :class="{
                                  'text-green-800': scanResult.outcome === 'success',
                                  'text-yellow-800': scanResult.outcome === 'warning',
                                  'text-red-800': scanResult.outcome === 'blocked'
                               }"
                               x-text="scanResult.outcome === 'success' ? 'Success' : (scanResult.outcome === 'warning' ? 'Warning' : 'Blocked')"></p>
                            <p class="text-sm mt-0.5"
                               :class="{
                                  'text-green-700': scanResult.outcome === 'success',
                                  'text-yellow-700': scanResult.outcome === 'warning',
                                  'text-red-700': scanResult.outcome === 'blocked'
                               }"
                               x-text="scanResult.message"></p>
                        </div>
                    </div>

                    <!-- Borrowings list (for return mode) -->
                    <template x-if="scanResult.borrowings && scanResult.borrowings.length > 0">
                        <div class="space-y-2">
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Select Borrowing to Return</p>
                            <template x-for="b in scanResult.borrowings" :key="b.id">
                                <div class="rounded-xl p-3 flex items-center justify-between"
                                     :class="b.is_overdue ? 'bg-red-50 ring-1 ring-red-200' : 'bg-gray-50 ring-1 ring-gray-200'">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900" x-text="b.user_name"></p>
                                        <p class="text-xs text-gray-500">
                                            Qty: <span x-text="b.quantity"></span> |
                                            Due: <span x-text="b.expected_return_date || 'N/A'"></span>
                                            <span x-show="b.is_overdue" class="text-red-600 font-bold ml-1">OVERDUE</span>
                                        </p>
                                    </div>
                                    <button @click="selectBorrowingForReturn(b.id)" class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition-all">
                                        Return
                                    </button>
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- Item details (for lookup mode) -->
                    <template x-if="scanResult.item && scanMode === 'lookup'">
                        <div class="grid grid-cols-2 gap-3 mt-2">
                            <div class="bg-gray-50 rounded-xl p-3">
                                <p class="text-xs text-gray-500 uppercase">Name</p>
                                <p class="text-sm font-semibold text-gray-900 mt-0.5" x-text="scanResult.item.name"></p>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-3">
                                <p class="text-xs text-gray-500 uppercase">Category</p>
                                <p class="text-sm font-semibold text-gray-900 mt-0.5" x-text="scanResult.item.category || 'N/A'"></p>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-3">
                                <p class="text-xs text-gray-500 uppercase">Stock</p>
                                <p class="text-sm font-semibold text-gray-900 mt-0.5" x-text="(scanResult.item.available_stock || 0) + ' / ' + (scanResult.item.total_stock || '?')"></p>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-3">
                                <p class="text-xs text-gray-500 uppercase">Status</p>
                                <p class="text-sm font-semibold mt-0.5" :class="scanResult.item.available_stock > 0 ? 'text-green-600' : 'text-red-600'" x-text="scanResult.item.available_stock > 0 ? 'Available' : 'Out of Stock'"></p>
                            </div>
                        </div>
                    </template>

                    <!-- Room details (for check-in) -->
                    <template x-if="scanResult.room">
                        <div class="bg-gray-50 rounded-xl p-3 mt-2">
                            <p class="text-xs text-gray-500 uppercase">Room</p>
                            <p class="text-sm font-semibold text-gray-900 mt-0.5" x-text="scanResult.room.name"></p>
                        </div>
                    </template>

                    <!-- Reservation info (after check-in) -->
                    <template x-if="scanResult.reservation">
                        <div class="bg-green-50 rounded-xl p-3 mt-2 ring-1 ring-green-200">
                            <p class="text-xs text-green-600 uppercase font-bold">Active Reservation</p>
                            <p class="text-sm font-semibold text-gray-900 mt-1" x-text="scanResult.reservation.start + ' - ' + scanResult.reservation.end"></p>
                            <p class="text-xs text-gray-600 mt-0.5" x-text="scanResult.reservation.purpose"></p>
                        </div>
                    </template>

                    <!-- Reset button -->
                    <button @click="scanResult = null" class="w-full mt-3 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-semibold transition-colors">
                        Scan Another
                    </button>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
function qrScanner() {
    return {
        cameraActive: false,
        manualCode: '',
        scanResult: null,
        scanMode: 'lookup',
        loading: false,
        stream: null,

        async startCamera() {
            try {
                this.stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
                this.$refs.video.srcObject = this.stream;
                this.cameraActive = true;
                this.startScanning();
            } catch (e) {
                this.scanResult = { outcome: 'blocked', message: 'Camera access denied. Please allow camera permissions.' };
            }
        },

        stopCamera() {
            if (this.stream) {
                this.stream.getTracks().forEach(t => t.stop());
                this.stream = null;
            }
            this.cameraActive = false;
        },

        startScanning() {
            // Use BarcodeDetector API if available, otherwise fall back to manual entry
            if ('BarcodeDetector' in window) {
                const detector = new BarcodeDetector({ formats: ['qr_code'] });
                const video = this.$refs.video;
                const scan = async () => {
                    if (!this.cameraActive || this.loading) {
                        if (this.cameraActive) requestAnimationFrame(scan);
                        return;
                    }
                    try {
                        const barcodes = await detector.detect(video);
                        if (barcodes.length > 0) {
                            const code = barcodes[0].rawValue;
                            this.manualCode = code;
                            await this.executeScan(code);
                            return; // Stop scanning after successful read
                        }
                    } catch (e) { /* ignore detection errors */ }
                    if (this.cameraActive) requestAnimationFrame(scan);
                };
                requestAnimationFrame(scan);
            }
        },

        async lookupByCode() {
            if (!this.manualCode.trim()) return;
            await this.executeScan(this.manualCode.trim());
        },

        async executeScan(code) {
            this.loading = true;
            this.scanResult = null;

            try {
                const resp = await fetch('/qr/scan-action', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ qr_code: code, action: this.scanMode })
                });
                const data = await resp.json();
                this.scanResult = data;
            } catch (e) {
                this.scanResult = { outcome: 'blocked', message: 'Network error. Please try again.' };
            }
            this.loading = false;
        },

        selectBorrowingForReturn(borrowingId) {
            document.getElementById('scanReturnBorrowingId').value = borrowingId;
            document.getElementById('scanReturnForm').action = `/staff/borrowings/${borrowingId}/return`;
            document.getElementById('scanReturnModal').classList.remove('hidden');
        }
    };
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
