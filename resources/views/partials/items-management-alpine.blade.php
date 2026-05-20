<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('itemsManagement', () => ({
        showAddItemModal: @json($openAddItemModal ?? false),
        showImportModal: false,
        showQrModal: false,
        qrItem: null,
        showUnitsModal: false,
        unitsData: { item_id: null, item_name: '', units: [], total: 0 },
        unitsLoading: false,

        async loadUnits(itemId) {
            this.unitsLoading = true;
            this.showUnitsModal = true;
            try {
                const res = await fetch('/staff/items/' + itemId + '/units');
                this.unitsData = await res.json();
            } catch (e) {
                this.unitsData = { item_id: itemId, item_name: 'Error', units: [], total: 0 };
            }
            this.unitsLoading = false;
            this.$nextTick(() => {
                setTimeout(() => {
                    if (typeof generateUnitQr === 'function' && this.unitsData.units) {
                        this.unitsData.units.forEach(unit => {
                            generateUnitQr(unit.id, unit.qr_code);
                        });
                    }
                }, 150);
            });
        },
    }));
});
</script>
