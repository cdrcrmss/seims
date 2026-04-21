@extends('layouts.app')

@section('title', 'New Procurement Request')

@section('content')
<div class="space-y-8" x-data="procurementForm()">
    <!-- Header -->
    <div class="flex items-center space-x-4 animate-fade-in-up">
        <a href="{{ route('procurement.index') }}" class="p-2 rounded-xl hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-poppins">New Procurement Request</h1>
            <p class="text-gray-600">Request supplies or equipment replenishment</p>
        </div>
    </div>

    <div class="max-w-2xl">
        <form method="POST" action="{{ route('procurement.store') }}" class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-8 space-y-6 animate-fade-in-up stagger-1">
            @csrf

            <!-- Item -->
            <div>
                <label for="item_id" class="block text-sm font-semibold text-gray-700 mb-2">Item to Restock</label>
                <select name="item_id" id="item_id" x-model="selectedItem" @change="updateItemInfo()" class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all" required>
                    <option value="">Select item...</option>
                    @foreach($items as $item)
                        <option value="{{ $item->id }}" data-stock="{{ $item->available_stock }}" data-total="{{ $item->total_stock }}" data-price="{{ $item->unit_price ?? 0 }}">
                            {{ $item->name }} (Stock: {{ $item->available_stock }}/{{ $item->total_stock }})
                        </option>
                    @endforeach
                </select>
                @error('item_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                
                <!-- Stock info -->
                <div x-show="itemInfo" x-transition class="mt-2 p-3 bg-gray-50 rounded-xl text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Current Stock:</span>
                        <span class="font-semibold" x-text="itemInfo"></span>
                    </div>
                </div>
            </div>

            <!-- Supplier -->
            <div>
                <label for="supplier_id" class="block text-sm font-semibold text-gray-700 mb-2">Supplier</label>
                <select name="supplier_id" id="supplier_id" class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all" required>
                    <option value="">Select supplier...</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->name }} — {{ $supplier->contact_person ?? 'No contact' }}
                        </option>
                    @endforeach
                </select>
                @error('supplier_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Quantity & Price -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="quantity" class="block text-sm font-semibold text-gray-700 mb-2">Quantity</label>
                    <input type="number" name="quantity" id="quantity" x-model="quantity" min="1" value="{{ old('quantity', 10) }}" class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all" required>
                    @error('quantity') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="unit_price" class="block text-sm font-semibold text-gray-700 mb-2">Unit Price (₱)</label>
                    <input type="number" name="unit_price" id="unit_price" x-model="unitPrice" step="0.01" min="0" value="{{ old('unit_price', 0) }}" class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all" required>
                    @error('unit_price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Total Price Preview -->
            <div class="bg-green-50 rounded-xl p-4 flex items-center justify-between">
                <span class="text-sm font-semibold text-green-700">Estimated Total:</span>
                <span class="text-lg font-bold text-green-700" x-text="'₱' + (quantity * unitPrice).toFixed(2)"></span>
            </div>

            <!-- Urgency -->
            <div>
                <label for="urgency_level" class="block text-sm font-semibold text-gray-700 mb-2">Urgency Level</label>
                <div class="grid grid-cols-4 gap-3">
                    @foreach(['low', 'medium', 'high', 'critical'] as $level)
                    <label class="cursor-pointer">
                        <input type="radio" name="urgency_level" value="{{ $level }}" class="peer hidden" {{ old('urgency_level', 'medium') === $level ? 'checked' : '' }}>
                        <div class="peer-checked:ring-2 peer-checked:ring-green-500 peer-checked:bg-green-50 bg-gray-50 rounded-xl p-3 text-center transition-all hover:bg-gray-100">
                            <span class="text-sm font-medium capitalize">{{ $level }}</span>
                        </div>
                    </label>
                    @endforeach
                </div>
                @error('urgency_level') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Justification -->
            <div>
                <label for="justification" class="block text-sm font-semibold text-gray-700 mb-2">Justification</label>
                <textarea name="justification" id="justification" rows="3" placeholder="Explain why this procurement is needed..." class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all" required>{{ old('justification') }}</textarea>
                @error('justification') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Submit -->
            <div class="flex items-center gap-3 pt-4">
                <button type="submit" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-sm hover:shadow-md transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    Submit Request
                </button>
                <a href="{{ route('procurement.index') }}" class="inline-flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function procurementForm() {
    return {
        selectedItem: '{{ old("item_id", "") }}',
        quantity: {{ old('quantity', 10) }},
        unitPrice: {{ old('unit_price', 0) }},
        itemInfo: '',
        
        updateItemInfo() {
            const select = document.getElementById('item_id');
            const option = select.options[select.selectedIndex];
            if (option && option.value) {
                this.itemInfo = option.dataset.stock + ' / ' + option.dataset.total;
                if (parseFloat(option.dataset.price) > 0) {
                    this.unitPrice = parseFloat(option.dataset.price);
                }
            } else {
                this.itemInfo = '';
            }
        }
    };
}
</script>
@endsection
