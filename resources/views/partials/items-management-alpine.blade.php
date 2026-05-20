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
        disposeUnitUrlBase: @json(url('/maintenance/units')),

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

        openDisposeModal(unit) {
            const code = unit.unit_code || ('Unit #' + unit.id);
            this.$dispatch('open-confirm-modal', {
                title: 'Dispose Unit',
                message: 'Mark unit ' + code + ' as disposed? It cannot be borrowed. Any scheduled maintenance for this unit will be cancelled.',
                type: 'danger',
                confirmLabel: 'Dispose',
                action: 'dispose-unit',
                disposeUrl: this.disposeUnitUrlBase + '/' + unit.id + '/dispose',
                unitCode: code,
            });
        },
    }));
});
</script>
