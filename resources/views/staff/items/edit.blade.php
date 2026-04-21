@extends('layouts.app')

@section('title', 'Edit Equipment')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-poppins">Edit Equipment</h1>
            <p class="text-gray-600">Update information for {{ $item->name }}</p>
        </div>
        <a href="{{ route('staff.items.index') }}"
           class="inline-flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Equipment
        </a>
    </div>

    <div class="max-w-3xl">
        <div class="bg-white rounded-2xl ring-1 ring-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="text-lg font-bold text-gray-900">Equipment Details</h2>
                <p class="text-sm text-gray-500 mt-0.5">Modify the equipment information below</p>
            </div>

            <div class="p-6">
                <form action="{{ route('staff.items.update', $item) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <!-- Current Image Display -->
                    @if($item->image_path)
                        <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl border border-gray-100">
                            <div class="w-20 h-20 bg-white rounded-xl overflow-hidden ring-1 ring-gray-200 flex-shrink-0">
                                <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $item->name }}</p>
                                <p class="text-xs text-gray-500">{{ $item->category }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Equipment Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name', $item->name) }}" required
                               class="w-full px-4 py-2.5 rounded-xl border border-gray-300 bg-white text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-green-500/30 focus:border-green-500 transition-colors text-sm">
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Description
                        </label>
                        <textarea name="description" rows="3"
                                  class="w-full px-4 py-2.5 rounded-xl border border-gray-300 bg-white text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-green-500/30 focus:border-green-500 transition-colors text-sm">{{ old('description', $item->description) }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Category & Status Row -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Category -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                Category <span class="text-red-500">*</span>
                            </label>
                            <select name="category" required
                                    class="w-full px-4 py-2.5 rounded-xl border border-gray-300 bg-white text-gray-900 focus:ring-2 focus:ring-green-500/30 focus:border-green-500 transition-colors text-sm">
                                <option value="electronics" {{ old('category', $item->category) == 'electronics' ? 'selected' : '' }}>Electronics</option>
                                <option value="mechanical" {{ old('category', $item->category) == 'mechanical' ? 'selected' : '' }}>Mechanical</option>
                                <option value="chemical" {{ old('category', $item->category) == 'chemical' ? 'selected' : '' }}>Chemical</option>
                                <option value="optical" {{ old('category', $item->category) == 'optical' ? 'selected' : '' }}>Optical</option>
                                <option value="measuring" {{ old('category', $item->category) == 'measuring' ? 'selected' : '' }}>Measuring</option>
                                <option value="computing" {{ old('category', $item->category) == 'computing' ? 'selected' : '' }}>Computing</option>
                                <option value="other" {{ old('category', $item->category) == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('category')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select name="status" required
                                    class="w-full px-4 py-2.5 rounded-xl border border-gray-300 bg-white text-gray-900 focus:ring-2 focus:ring-green-500/30 focus:border-green-500 transition-colors text-sm">
                                <option value="available" {{ old('status', $item->status) == 'available' ? 'selected' : '' }}>Available</option>
                                <option value="in_use" {{ old('status', $item->status) == 'in_use' ? 'selected' : '' }}>In Use</option>
                                <option value="maintenance" {{ old('status', $item->status) == 'maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                                <option value="damaged" {{ old('status', $item->status) == 'damaged' ? 'selected' : '' }}>Damaged</option>
                                <option value="lost" {{ old('status', $item->status) == 'lost' ? 'selected' : '' }}>Lost</option>
                                <option value="retired" {{ old('status', $item->status) == 'retired' ? 'selected' : '' }}>Retired</option>
                            </select>
                            @error('status')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Stock Information -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Stock Information</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1.5">
                                    Total Stock <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="total_stock" value="{{ old('total_stock', $item->total_stock) }}" min="1" required
                                       class="w-full px-4 py-2.5 rounded-xl border border-gray-300 bg-white text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-green-500/30 focus:border-green-500 transition-colors text-sm">
                                @error('total_stock')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1.5">
                                    Available Stock <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="available_stock" value="{{ old('available_stock', $item->available_stock) }}" min="0" required
                                       class="w-full px-4 py-2.5 rounded-xl border border-gray-300 bg-white text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-green-500/30 focus:border-green-500 transition-colors text-sm">
                                @error('available_stock')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="mt-2 px-3 py-2 bg-amber-50 rounded-lg border border-amber-100">
                            <p class="text-xs text-amber-700">
                                <strong>Current Status:</strong> {{ $item->available_stock }}/{{ $item->total_stock }} available
                            </p>
                        </div>
                    </div>

                    <!-- Image Upload -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Update Equipment Image
                        </label>
                        <input type="file" name="image" accept="image/*"
                               class="w-full px-4 py-2.5 rounded-xl border border-dashed border-gray-300 bg-gray-50 text-gray-900 file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 file:cursor-pointer cursor-pointer transition-colors text-sm">
                        <p class="text-xs text-gray-500 mt-1">Leave empty to keep current image. JPG, PNG, GIF up to 2MB</p>
                        @error('image')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end items-center gap-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('staff.items.index') }}"
                           class="inline-flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200">
                            Cancel
                        </a>
                        <button type="submit"
                                class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-sm hover:shadow-md transition-all duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Update Equipment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const totalStockInput = document.querySelector('input[name="total_stock"]');
    const availableStockInput = document.querySelector('input[name="available_stock"]');

    function validateStock() {
        const total = parseInt(totalStockInput.value) || 0;
        const available = parseInt(availableStockInput.value) || 0;

        if (available > total) {
            availableStockInput.setCustomValidity('Available stock cannot exceed total stock');
            availableStockInput.classList.add('border-red-500');
            availableStockInput.classList.remove('border-gray-300');
        } else {
            availableStockInput.setCustomValidity('');
            availableStockInput.classList.remove('border-red-500');
            availableStockInput.classList.add('border-gray-300');
        }
    }

    totalStockInput.addEventListener('input', validateStock);
    availableStockInput.addEventListener('input', validateStock);
});
</script>
@endsection
