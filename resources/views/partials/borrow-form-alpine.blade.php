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

        init() {
            if (this.hasOverdue) {
                this.cart = [];
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
                return;
            }
            this.cart.push({
                id: item.id,
                name: item.name,
                category: item.category,
                laboratory: item.laboratory || '',
                stock: item.stock,
                image_url: item.image_url || '',
                quantity: 1,
            });
        },

        removeFromCart(itemId) {
            this.cart = this.cart.filter(line => line.id !== itemId);
        },

        incrementQty(itemId) {
            const line = this.cart.find(l => l.id === itemId);
            if (line && line.quantity < line.stock) {
                line.quantity++;
            }
        },

        decrementQty(itemId) {
            const line = this.cart.find(l => l.id === itemId);
            if (line && line.quantity > 1) {
                line.quantity--;
            }
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
            const target = this.buildFilterUrl();
            if (!this.filtersMatchUrl()) {
                window.location.href = target;
            }
        },

        filterByCategory() { this.applyFilters(); },

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
            this.searchQuery = item.name;
            this.addToCart({
                id: item.id,
                name: item.name,
                category: item.category,
                laboratory: item.laboratory || '',
                stock: item.available_stock,
                image_url: item.image_url || '',
            });
            this.applyFilters();
        },

        handleSubmit(event) {
            if (this.isSubmitting || !this.canSubmit) {
                event.preventDefault();
                return;
            }
            this.isSubmitting = true;
        },
    };
}
</script>
