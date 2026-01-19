@extends('layouts.app')

@section('title', 'Add New Equipment')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-purple-900">
    <!-- Header -->
    <div class="bg-white/20 dark:bg-white/10 backdrop-blur-md border-b border-white/30 dark:border-white/20 shadow-lg">
        <div class="max-w-7xl mx-auto px-6 py-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center mr-6 shadow-xl">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                            Add New Equipment
                        </h1>
                        <p class="text-gray-700 dark:text-gray-200 mt-2 text-lg">Add new laboratory equipment to the inventory</p>
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
            <div class="bg-gradient-to-r from-blue-500/10 to-purple-500/10 p-8 border-b border-white/20">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">Equipment Details</h2>
                <p class="text-gray-600 dark:text-gray-300">Fill in the information below to add new equipment to the inventory</p>
            </div>
            
            <div class="p-10">
                <form action="{{ route('staff.items.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                    @csrf
                    
                    <!-- Name -->
                    <div class="bg-white/20 dark:bg-white/5 rounded-2xl p-6 border border-white/30">
                        <label class="block text-lg font-semibold text-gray-800 dark:text-white mb-3">
                            Equipment Name <span class="text-red-500 text-xl">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               placeholder="e.g., Digital Microscope Model XYZ"
                               class="w-full px-6 py-4 rounded-xl border-2 border-gray-300 dark:border-gray-500 bg-white/70 dark:bg-gray-700/50 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-4 focus:ring-blue-500/30 focus:border-blue-500 transition-all duration-300 text-lg font-medium shadow-lg">
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
                                  placeholder="Detailed description of the equipment, its specifications, and usage instructions..."
                                  class="w-full px-6 py-4 rounded-xl border-2 border-gray-300 dark:border-gray-500 bg-white/70 dark:bg-gray-700/50 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-4 focus:ring-blue-500/30 focus:border-blue-500 transition-all duration-300 text-lg font-medium shadow-lg">{{ old('description') }}</textarea>
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
                                    class="w-full px-6 py-4 rounded-xl border-2 border-gray-300 dark:border-gray-500 bg-white/70 dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-4 focus:ring-blue-500/30 focus:border-blue-500 transition-all duration-300 text-lg font-medium shadow-lg appearance-none cursor-pointer">
                                <option value="">Select Equipment Category</option>
                                <option value="electronics" {{ old('category') == 'electronics' ? 'selected' : '' }}>🔌 Electronics</option>
                                <option value="mechanical" {{ old('category') == 'mechanical' ? 'selected' : '' }}>⚙️ Mechanical</option>
                                <option value="chemical" {{ old('category') == 'chemical' ? 'selected' : '' }}>🧪 Chemical</option>
                                <option value="optical" {{ old('category') == 'optical' ? 'selected' : '' }}>🔬 Optical</option>
                                <option value="measuring" {{ old('category') == 'measuring' ? 'selected' : '' }}>📏 Measuring</option>
                                <option value="computing" {{ old('category') == 'computing' ? 'selected' : '' }}>💻 Computing</option>
                                <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>📦 Other</option>
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
                                <input type="number" name="total_stock" value="{{ old('total_stock') }}" min="1" required
                                       placeholder="Enter total quantity"
                                       class="w-full px-6 py-4 rounded-xl border-2 border-gray-300 dark:border-gray-500 bg-white/70 dark:bg-gray-700/50 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-4 focus:ring-blue-500/30 focus:border-blue-500 transition-all duration-300 text-lg font-medium shadow-lg">
                                @error('total_stock')
                                    <p class="text-red-500 text-sm mt-2 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-md font-semibold text-gray-800 dark:text-white mb-3">
                                    Available Stock <span class="text-red-500 text-xl">*</span>
                                </label>
                                <input type="number" name="available_stock" value="{{ old('available_stock') }}" min="0" required
                                       placeholder="Available for borrowing"
                                       class="w-full px-6 py-4 rounded-xl border-2 border-gray-300 dark:border-gray-500 bg-white/70 dark:bg-gray-700/50 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-4 focus:ring-blue-500/30 focus:border-blue-500 transition-all duration-300 text-lg font-medium shadow-lg">
                                @error('available_stock')
                                    <p class="text-red-500 text-sm mt-2 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="mt-4 p-4 bg-blue-50/50 dark:bg-blue-900/20 rounded-xl border border-blue-200 dark:border-blue-800">
                            <p class="text-sm text-blue-800 dark:text-blue-300">
                                💡 <strong>Tip:</strong> Available stock should be less than or equal to total stock. 
                                This represents how many items are currently available for borrowing.
                            </p>
                        </div>
                    </div>

                    <!-- Image Upload -->
                    <div class="bg-white/20 dark:bg-white/5 rounded-2xl p-6 border border-white/30">
                        <label class="block text-lg font-semibold text-gray-800 dark:text-white mb-3">
                            Equipment Image
                        </label>
                        <div class="relative">
                            <input type="file" name="image" accept="image/*" id="image-upload"
                                   class="w-full px-6 py-4 rounded-xl border-2 border-dashed border-gray-400 dark:border-gray-500 bg-white/50 dark:bg-gray-700/30 text-gray-900 dark:text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 file:cursor-pointer cursor-pointer transition-all duration-300">
                            <div class="mt-4 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                                    Click to upload or drag and drop
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    JPG, PNG, GIF up to 2MB
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
                            ❌ Cancel
                        </a>
                        <button type="submit"
                                class="px-10 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl hover:from-blue-700 hover:to-purple-700 focus:ring-4 focus:ring-blue-500/30 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105 font-semibold text-lg">
                            ✅ Add Equipment
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