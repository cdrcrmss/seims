{{-- Multi-item borrow cart panel. Expects parent x-data="borrowForm()" --}}
@php
    $isRequestMode = !empty($isRequestMode);
@endphp
<div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm sticky top-6 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
        <h2 class="text-base font-bold text-gray-900 font-poppins">{{ $formTitle }}</h2>
        <p class="text-xs text-gray-500 mt-0.5">{{ $formSubtitle }}</p>
    </div>

    <form method="POST" action="{{ $formAction }}" @submit="handleSubmit($event)" class="p-6 space-y-5">
        @csrf

        <div id="cart-hidden-fields"></div>

        <div>
            <div class="flex items-center justify-between gap-2 mb-2">
                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Selected Items</label>
                <button type="button"
                        x-show="cart.length > 0"
                        x-cloak
                        @click="clearCart()"
                        class="text-xs font-semibold text-red-600 hover:text-red-700 hover:underline transition-colors">
                    Clear all
                </button>
            </div>

            <div x-show="cart.length === 0" class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center">
                <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                <p class="text-sm text-gray-400 font-medium">No items selected</p>
                <p class="text-xs text-gray-300 mt-0.5">Click items to add them here</p>
            </div>

            <div x-show="cart.length > 0" x-cloak class="space-y-2 max-h-64 overflow-y-auto pr-1">
                <template x-for="line in cart" :key="line.id">
                    <div class="bg-green-50 ring-1 ring-green-200 rounded-xl p-3">
                        <div class="flex items-start gap-3">
                            <div class="w-12 h-12 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0 ring-1 ring-green-200">
                                <img x-show="line.image_url" :src="line.image_url" :alt="line.name" class="w-full h-full object-cover">
                                <div x-show="!line.image_url" class="w-full h-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-green-900 truncate" x-text="line.name"></p>
                                <p class="text-xs text-green-700" x-text="line.category"></p>
                                <p class="text-xs font-semibold text-emerald-800 mt-0.5" x-show="line.laboratory" x-text="'Lab: ' + line.laboratory"></p>
                            </div>
                            <div class="flex flex-col items-end gap-0.5 flex-shrink-0">
                                <div class="flex items-center gap-1.5">
                                <div class="flex items-stretch border border-green-200 rounded-md bg-white overflow-hidden">
                                    <input type="number"
                                           :value="line.quantity"
                                           @input="setQuantity(line.id, $event.target.value)"
                                           @change="setQuantity(line.id, $event.target.value)"
                                           @blur="setQuantity(line.id, $event.target.value)"
                                           min="1"
                                           :max="line.stock"
                                           class="w-10 py-1 text-sm font-bold text-gray-900 text-center border-0 focus:ring-0 focus:outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                                    <div class="flex flex-col border-l border-green-200 divide-y divide-green-100">
                                        <button type="button" @click="incrementQty(line.id)" :disabled="line.quantity >= line.stock"
                                                class="px-1.5 py-0.5 text-gray-500 hover:bg-gray-50 disabled:opacity-40" aria-label="Increase quantity">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"/></svg>
                                        </button>
                                        <button type="button" @click="decrementQty(line.id)" :disabled="line.quantity <= 1"
                                                class="px-1.5 py-0.5 text-gray-500 hover:bg-gray-50 disabled:opacity-40" aria-label="Decrease quantity">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                        </button>
                                    </div>
                                </div>
                                <button type="button" @click="removeFromCart(line.id)" class="p-1 text-green-400 hover:text-red-500 hover:bg-red-50 rounded-lg" aria-label="Remove item">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                                </div>
                                <span class="text-[10px] font-medium text-emerald-700" x-text="line.stock + ' available'"></span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
            @error('items') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="return_hours" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Return</label>
            <select id="return_hours" name="return_hours" x-model="returnHours"
                    class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-white"
                    :disabled="cart.length === 0">
                @foreach([3, 4, 5, 6, 7, 8] as $hours)
                    <option value="{{ $hours }}" @selected(old('return_hours', 8) == $hours)>{{ $hours }} hours</option>
                @endforeach
            </select>
            @error('return_hours') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        @if(!empty($showPurpose))
        <div>
            <label for="purpose" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                Purpose <span class="font-normal normal-case text-gray-400">(min 10 characters)</span>
            </label>
            <textarea id="purpose" name="purpose" rows="3" x-model="purpose" maxlength="100"
                      placeholder="Describe why you need these items..."
                      class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm resize-none focus:ring-2 focus:ring-green-500"
                      :disabled="cart.length === 0">{{ old('purpose') }}</textarea>
            <div class="flex justify-between mt-1">
                @error('purpose') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                <span class="text-xs ml-auto" :class="purpose.length >= 10 ? 'text-green-600' : 'text-gray-400'" x-text="purpose.length + '/100'"></span>
            </div>
        </div>
        @endif

        <div class="bg-gray-50 rounded-xl p-4 space-y-2">
            <div class="flex justify-between items-center">
                <span class="text-xs text-gray-500">Return window</span>
                <span class="text-xs font-bold text-gray-900">3–8 hours</span>
            </div>
            <div class="flex justify-between items-center pt-1.5 border-t border-gray-200">
                @if($isRequestMode)
                <span class="text-xs text-gray-400 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Borrow request
                </span>
                <span class="text-xs text-gray-400">Awaiting staff approval</span>
                @else
                <span class="text-xs text-gray-400 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Direct borrowing
                </span>
                <span class="text-xs text-gray-400">Issued immediately</span>
                @endif
            </div>
        </div>

        <div class="flex gap-3 pt-1">
            <button type="submit"
                    :disabled="isSubmitting || !canSubmit"
                    class="flex-1 py-3 text-white font-semibold rounded-xl text-sm transition-all flex items-center justify-center gap-2 disabled:bg-gray-300 disabled:cursor-not-allowed enabled:bg-green-600 enabled:hover:bg-green-700 enabled:shadow-sm">
                <svg x-show="isSubmitting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span x-text="isSubmitting ? 'Processing...' : submitLabel"></span>
            </button>
            <a href="{{ $cancelUrl }}" class="inline-flex items-center justify-center px-5 py-3 text-sm font-semibold text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 rounded-xl">Cancel</a>
        </div>

        <p class="text-[10px] text-gray-400 text-center leading-relaxed">
            @if($isRequestMode)
                Staff will review your request before items are issued.
            @else
                Return all items in good condition before the return time ends.
            @endif
        </p>
    </form>
</div>
