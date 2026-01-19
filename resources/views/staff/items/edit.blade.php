@extends('layouts.app')

@section('title', 'Edit Equipment')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-purple-900">
    <!-- Header -->
    <div class="bg-white/20 dark:bg-white/10 backdrop-blur-md border-b border-white/30 dark:border-white/20 shadow-lg">
        <div class="max-w-7xl mx-auto px-6 py-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-red-600 rounded-2xl flex items-center justify-center mr-6 shadow-xl">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-4xl font-bold bg-gradient-to-r from-orange-600 to-red-600 bg-clip-text text-transparent">
                            Edit Equipment
                        </h1>
                        <p class="text-gray-700 dark:text-gray-200 mt-2 text-lg">Update information for {{ $item->name }}</p>
                    </div>
                </div>
                <a href="{{ route('staff.items.index') }}" 
                   class="px-8 py-4 bg-gray-600 hover:bg-gray-700 text-white rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105 font-semibold">
                    📋 Back to Equipment
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-6 py-8">
        <div class="bg-white/30 dark:bg-white/10 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/40 dark:border-white/20 overflow-hidden">
            <div class="bg-gradient-to-r from-orange-500/10 to-red-500/10 p-8 border-b border-white/20">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">Update Equipment Details</h2>
                <p class="text-gray-600 dark:text-gray-300">Modify the equipment information below</p>
            </div>
            
            <div class="p-10">
                <form action="{{ route('staff.items.update', $item) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                    @csrf
                    @method('PUT')
                    
                    <!-- Current Image Display -->
                    @if($item->image_path)
                        <div class="bg-white/20 dark:bg-white/5 rounded-2xl p-6 border border-white/30">
                            <label class="block text-lg font-semibold text-gray-800 dark:text-white mb-4">
                                Current Equipment Image
                            </label>
                            <div class="flex items-center space-x-6">
                                <div class="w-32 h-32 bg-gray-100 dark:bg-gray-800 rounded-2xl overflow-hidden shadow-lg border-4 border-white/50">
                                    <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                                </div>
                                <div class="text-gray-600 dark:text-gray-300">
                                    <p class="font-medium">{{ $item->name }}</p>
                                    <p class="text-sm">{{ $item->category }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <!-- Name -->
                    <div class="bg-white/20 dark:bg-white/5 rounded-2xl p-6 border border-white/30">
                        <label class="block text-lg font-semibold text-gray-800 dark:text-white mb-3">
                            Equipment Name <span class="text-red-500 text-xl">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name', $item->name) }}" required
                               class="w-full px-6 py-4 rounded-xl border-2 border-gray-300 dark:border-gray-500 bg-white/70 dark:bg-gray-700/50 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-4 focus:ring-orange-500/30 focus:border-orange-500 transition-all duration-300 text-lg font-medium shadow-lg">
                        @error('name')
                            <p class="text-red-500 text-sm mt-2 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="bg-white/20 dark:bg-white/5 rounded-2xl p-6 border border-white/30">
                        <label class="block text-lg font-semibold text-gray-800 dark:text-white mb-3">
                            Description
                        </label>
                        <textarea name="description" rows="4"
                                  class="w-full px-6 py-4 rounded-xl border-2 border-gray-300 dark:border-gray-500 bg-white/70 dark:bg-gray-700/50 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-4 focus:ring-orange-500/30 focus:border-orange-500 transition-all duration-300 text-lg font-medium shadow-lg">{{ old('description', $item->description) }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-sm mt-2 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Category -->
                    <div class="bg-white/20 dark:bg-white/5 rounded-2xl p-6 border border-white/30">
                        <label class="block text-lg font-semibold text-gray-800 dark:text-white mb-3">
                            Category <span class="text-red-500 text-xl">*</span>
                        </label>
                        <div class="relative">
                            <select name="category" required
                                    class="w-full px-6 py-4 rounded-xl border-2 border-gray-300 dark:border-gray-500 bg-white/70 dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-4 focus:ring-orange-500/30 focus:border-orange-500 transition-all duration-300 text-lg font-medium shadow-lg appearance-none cursor-pointer">
                                <option value="electronics" {{ old('category', $item->category) == 'electronics' ? 'selected' : '' }}>Electronics</option>
                                <option value="mechanical" {{ old('category', $item->category) == 'mechanical' ? 'selected' : '' }}>Mechanical</option>
                                <option value="chemical" {{ old('category', $item->category) == 'chemical' ? 'selected' : '' }}>Chemical</option>
                                <option value="optical" {{ old('category', $item->category) == 'optical' ? 'selected' : '' }}>Optical</option>
                                <option value="measuring" {{ old('category', $item->category) == 'measuring' ? 'selected' : '' }}>Measuring</option>
                                <option value="computing" {{ old('category', $item->category) == 'computing' ? 'selected' : '' }}>Computing</option>
                                <option value="other" {{ old('category', $item->category) == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-6 pointer-events-none">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                        @error('category')
                            <p class="text-red-500 text-sm mt-2 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Stock Information -->
                    <div class="bg-white/20 dark:bg-white/5 rounded-2xl p-6 border border-white/30">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-6">Stock Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-md font-semibold text-gray-800 dark:text-white mb-3">
                                    Total Stock <span class="text-red-500 text-xl">*</span>
                                </label>
                                <input type="number" name="total_stock" value="{{ old('total_stock', $item->total_stock) }}" min="1" required
                                       class="w-full px-6 py-4 rounded-xl border-2 border-gray-300 dark:border-gray-500 bg-white/70 dark:bg-gray-700/50 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-4 focus:ring-orange-500/30 focus:border-orange-500 transition-all duration-300 text-lg font-medium shadow-lg">
                                @error('total_stock')
                                    <p class="text-red-500 text-sm mt-2 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-md font-semibold text-gray-800 dark:text-white mb-3">
                                    Available Stock <span class="text-red-500 text-xl">*</span>
                                </label>
                                <input type="number" name="available_stock" value="{{ old('available_stock', $item->available_stock) }}" min="0" required
                                       class="w-full px-6 py-4 rounded-xl border-2 border-gray-300 dark:border-gray-500 bg-white/70 dark:bg-gray-700/50 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-4 focus:ring-orange-500/30 focus:border-orange-500 transition-all duration-300 text-lg font-medium shadow-lg">
                                @error('available_stock')
                                    <p class="text-red-500 text-sm mt-2 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="mt-4 p-4 bg-orange-50/50 dark:bg-orange-900/20 rounded-xl border border-orange-200 dark:border-orange-800">
                            <p class="text-sm text-orange-800 dark:text-orange-300">
                                💡 <strong>Current Stock Status:</strong> {{ $item->available_stock }}/{{ $item->total_stock }} available
                            </p>
                        </div>
                    </div>

                    <!-- Image Upload -->
                    <div class="bg-white/20 dark:bg-white/5 rounded-2xl p-6 border border-white/30">
                        <label class="block text-lg font-semibold text-gray-800 dark:text-white mb-3">
                            Update Equipment Image
                        </label>
                        <div class="relative">
                            <input type="file" name="image" accept="image/*" id="image-upload"
                                   class="w-full px-6 py-4 rounded-xl border-2 border-dashed border-gray-400 dark:border-gray-500 bg-white/50 dark:bg-gray-700/30 text-gray-900 dark:text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100 file:cursor-pointer cursor-pointer transition-all duration-300">
                            <div class="mt-4 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                                    Click to upload new image or drag and drop
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    Leave empty to keep current image • JPG, PNG, GIF up to 2MB
                                </p>
                            </div>
                        </div>
                        @error('image')
                            <p class="text-red-500 text-sm mt-2 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col sm:flex-row justify-end space-y-4 sm:space-y-0 sm:space-x-6 pt-8">
                        <a href="{{ route('staff.items.index') }}" 
                           class="px-8 py-4 bg-gray-600 hover:bg-gray-700 text-white rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105 font-semibold text-center">
                            Cancel
                        </a>
                        <button type="submit"
                                class="px-10 py-4 bg-gradient-to-r from-orange-600 to-red-600 text-white rounded-xl hover:from-orange-700 hover:to-red-700 focus:ring-4 focus:ring-orange-500/30 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105 font-semibold text-lg">
                            Update Equipment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Add some interactivity for stock validation
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