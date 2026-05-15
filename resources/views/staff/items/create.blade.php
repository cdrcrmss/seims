@extends('layouts.app')

@section('title', 'Add New Equipment')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-poppins">Add New Equipment</h1>
            <p class="text-gray-600">Add new laboratory equipment to the inventory</p>
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
                <p class="text-sm text-gray-500 mt-0.5">Fill in the information below to add new equipment</p>
            </div>

            <div class="p-6">
                <form action="{{ route('staff.items.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf

                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Equipment Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               placeholder="e.g., Digital Microscope Model XYZ"
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
                                  placeholder="Detailed description, specifications, and usage instructions..."
                                  class="w-full px-4 py-2.5 rounded-xl border border-gray-300 bg-white text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-green-500/30 focus:border-green-500 transition-colors text-sm">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Category & Laboratory -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Category -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                Category <span class="text-red-500">*</span>
                            </label>
                            @include('staff.items.partials.category-input', [
                                'categories' => $categories,
                                'value' => old('category'),
                            ])
                            @error('category')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Laboratory -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                Laboratory <span class="text-red-500">*</span>
                            </label>
                            <select name="laboratory" required
                                    class="w-full px-4 py-2.5 rounded-xl border border-gray-300 bg-white text-gray-900 focus:ring-2 focus:ring-green-500/30 focus:border-green-500 transition-colors text-sm">
                                <option value="">Select Laboratory</option>
                                <option value="Alfresco" {{ old('laboratory') == 'Alfresco' ? 'selected' : '' }}>Alfresco</option>
                                <option value="Kitchen" {{ old('laboratory') == 'Kitchen' ? 'selected' : '' }}>Kitchen</option>
                                <option value="Food Lab" {{ old('laboratory') == 'Food Lab' ? 'selected' : '' }}>Food Lab</option>
                                <option value="Hotel" {{ old('laboratory') == 'Hotel' ? 'selected' : '' }}>Hotel</option>
                            </select>
                            @error('laboratory')
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
                                <input type="number" name="total_stock" value="{{ old('total_stock') }}" min="1" required
                                       placeholder="Enter total quantity"
                                       class="w-full px-4 py-2.5 rounded-xl border border-gray-300 bg-white text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-green-500/30 focus:border-green-500 transition-colors text-sm">
                                @error('total_stock')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1.5">
                                    Available Stock <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="available_stock" value="{{ old('available_stock') }}" min="0" required
                                       placeholder="Available for borrowing"
                                       class="w-full px-4 py-2.5 rounded-xl border border-gray-300 bg-white text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-green-500/30 focus:border-green-500 transition-colors text-sm">
                                @error('available_stock')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="mt-2 px-3 py-2 bg-blue-50 rounded-lg border border-blue-100">
                            <p class="text-xs text-blue-700">
                                <strong>Tip:</strong> Available stock should be less than or equal to total stock.
                            </p>
                        </div>
                    </div>

                    <!-- Image Upload -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Equipment Image
                        </label>
                        <input type="file" name="image" accept="image/*"
                               class="w-full px-4 py-2.5 rounded-xl border border-dashed border-gray-300 bg-gray-50 text-gray-900 file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 file:cursor-pointer cursor-pointer transition-colors text-sm">
                        <p class="text-xs text-gray-500 mt-1">JPG, PNG, GIF up to 2MB</p>
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
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                            Add Equipment
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
