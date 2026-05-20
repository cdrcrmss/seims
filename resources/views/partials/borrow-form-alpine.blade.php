@php
    $initialCart = [];
    if (!empty($selectedItem) && $selectedItem->available_stock > 0) {
        $initialCart[] = [
            'id' => $selectedItem->id,
            'name' => $selectedItem->name,
            'category' => $selectedItem->category,
            'laboratory' => $selectedItem->laboratory ?? '',
            'stock' => $selectedItem->available_stock,
            'image_url' => $selectedItem->image_url,
            'quantity' => 1,
        ];
    }
@endphp
<script>
function borrowForm() {
    return {
        cart: @json($initialCart),
        returnHours: {{ (int) old('return_hours', 8) }},
        purpose: @json(old('purpose', '')),
        isSubmitting: false,
        searchQuery: @json($search ?? ''),
        searchResults: [],
        searchOpen: false,
        searchTimeout: null,
        filterTimeout: null,
        selectedCategory: @json($category ?? ''),
        hasOverdue: @json($hasOverdue ?? false),
        requirePurpose: @json($requirePurpose ?? false),
        searchApiUrl: @json($searchApiUrl),
        filterBaseUrl: @json($filterBaseUrl),
        submitLabel: @json($submitLabel ?? 'Submit'),
        cartStorageKey: @json($cartStorageKey ?? 'seims_borrow_cart'),

        init() {
            if (this.hasOverdue) {
                this.clearStoredCart();
                this.cart = [];
                return;
            }

            const stored = this.loadStoredCart();
            if (stored && stored.length > 0) {
                this.cart = stored;
            }

            const storedHours = sessionStorage.getItem(this.cartStorageKey + '_return_hours');
            if (storedHours && !@json((bool) old('return_hours'))) {
                this.returnHours = parseInt(storedHours, 10) || 8;
            }

            const storedPurpose = sessionStorage.getItem(this.cartStorageKey + '_purpose');
            if (storedPurpose && !@json((bool) old('purpose'))) {
                this.purpose = storedPurpose;
            }

            this.$watch('cart', () => this.persistCart(), { deep: true });
            this.$watch('returnHours', () => this.persistCart());
            this.$watch('purpose', () => this.persistCart());
        },

        loadStoredCart() {
            try {
                const raw = sessionStorage.getItem(this.cartStorageKey);
                return raw ? JSON.parse(raw) : null;
            } catch (e) {
                return null;
            }
        },

        persistCart() {
            if (this.hasOverdue) {
                return;
            }
            try {
                sessionStorage.setItem(this.cartStorageKey, JSON.stringify(this.cart));
                sessionStorage.setItem(this.cartStorageKey + '_return_hours', String(this.returnHours));
                sessionStorage.setItem(this.cartStorageKey + '_purpose', this.purpose || '');
            } catch (e) {
                // sessionStorage unavailable
            }
        },

        clearStoredCart() {
            try {
                sessionStorage.removeItem(this.cartStorageKey);
                sessionStorage.removeItem(this.cartStorageKey + '_return_hours');
                sessionStorage.removeItem(this.cartStorageKey + '_purpose');
            } catch (e) {
                // ignore
            }
        },

        inCart(itemId) {
            return this.cart.some(line => line.id === itemId);
        },

        addToCart(item) {
            if (this.hasOverdue) return;
            const existing = this.cart.find(line => line.id === item.id);
            if (existing) {
                if (existing.quantity < existing.stock) {
                    existing.quantity++;
                }
                this.persistCart();
                return;
            }
            this.cart.push({
                id: item.id,
                name: item.name,
                category: item.category,
                laboratory: item.laboratory || '',
                stock: item.stock ?? item.available_stock,
                image_url: item.image_url || '',
                quantity: 1,
            });
            this.persistCart();
        },

        removeFromCart(itemId) {
            this.cart = this.cart.filter(line => line.id !== itemId);
            this.persistCart();
        },

        clearCart() {
            this.cart = [];
            this.persistCart();
        },

        incrementQty(itemId) {
            const line = this.cart.find(l => l.id === itemId);
            if (line && line.quantity < line.stock) {
                line.quantity++;
                this.persistCart();
            }
        },

        decrementQty(itemId) {
            const line = this.cart.find(l => l.id === itemId);
            if (line && line.quantity > 1) {
                line.quantity--;
                this.persistCart();
            }
        },

        setQuantity(itemId, value) {
            const line = this.cart.find(l => l.id === itemId);
            if (!line) return;
            let qty = parseInt(value, 10);
            if (isNaN(qty) || qty < 1) qty = 1;
            if (qty > line.stock) qty = line.stock;
            line.quantity = qty;
            this.persistCart();
        },

        get canSubmit() {
            if (this.cart.length === 0 || this.hasOverdue) return false;
            if (this.requirePurpose && this.purpose.length < 10) return false;
            return true;
        },

        buildFilterUrl() {
            const params = new URLSearchParams();
            const q = this.searchQuery.trim();
            if (q) params.set('search', q);
            if (this.selectedCategory) params.set('category', this.selectedCategory);
            const qs = params.toString();
            return this.filterBaseUrl + (qs ? '?' + qs : '');
        },

        filtersMatchUrl() {
            const current = new URL(window.location.href);
            return (current.searchParams.get('search') || '') === this.searchQuery.trim()
                && (current.searchParams.get('category') || '') === (this.selectedCategory || '');
        },

        applyFilters() {
            this.persistCart();
            const target = this.buildFilterUrl();
            if (!this.filtersMatchUrl()) {
                window.location.href = target;
            }
        },

        filterByCategory() {
            this.persistCart();
            this.applyFilters();
        },

        clearSearch() {
            this.searchQuery = '';
            this.searchResults = [];
            this.searchOpen = false;
            this.applyFilters();
        },

        onSearchFocus() {
            this.searchOpen = true;
            if (this.searchQuery.trim().length >= 1) {
                this.performSearch();
            }
        },

        debouncedSearch() {
            clearTimeout(this.searchTimeout);
            clearTimeout(this.filterTimeout);
            this.searchTimeout = setTimeout(() => {
                if (this.searchQuery.trim().length >= 1) {
                    this.searchOpen = true;
                    this.performSearch();
                }
            }, 200);
            this.filterTimeout = setTimeout(() => this.applyFilters(), 450);
        },

        async performSearch() {
            const q = this.searchQuery.trim();
            if (q.length < 1) {
                this.searchResults = [];
                this.searchOpen = false;
                return;
            }
            try {
                const response = await fetch(this.searchApiUrl + '?q=' + encodeURIComponent(q));
                const data = await response.json();
                this.searchResults = Array.isArray(data) ? data : [];
                this.searchOpen = this.searchOpen && this.searchResults.length > 0;
            } catch (e) {
                this.searchResults = [];
                this.searchOpen = false;
            }
        },

        pickSearchResult(item) {
            if (this.hasOverdue) return;
            this.addToCart({
                id: item.id,
                name: item.name,
                category: item.category,
                laboratory: item.laboratory || '',
                stock: item.available_stock,
                image_url: item.image_url || '',
            });
            this.searchQuery = item.name;
            this.applyFilters();
        },

        handleSubmit(event) {
            if (this.isSubmitting || !this.canSubmit) {
                event.preventDefault();
                return;
            }
            this.isSubmitting = true;
            this.persistCart();
        },
    };
}
</script>
