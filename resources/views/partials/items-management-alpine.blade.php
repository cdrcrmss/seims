<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('itemsManagement', () => ({
        showAddItemModal: @json($openAddItemModal ?? false),
        showImportModal: false,
        importFileName: '',
        importFileSize: '',
        showQrModal: false,
        qrItem: null,
        showUnitsModal: false,
        unitsData: { item_id: null, item_name: '', units: [], total: 0 },
        unitsLoading: false,

        init() {
            @if(session('success') || session('error') || session('info'))
            this.showImportModal = false;
            @endif
        },

        openImportModal() {
            this.clearImportFile();
            this.showImportModal = true;
        },

        closeImportModal() {
            this.showImportModal = false;
        },

        onImportFileSelected(event) {
            const file = event.target.files?.[0];
            if (!file) {
                this.clearImportFile();
                return;
            }
            this.importFileName = file.name;
            const mb = file.size / (1024 * 1024);
            this.importFileSize = mb >= 0.1
                ? mb.toFixed(1) + ' MB'
                : Math.max(1, Math.round(file.size / 1024)) + ' KB';
        },

        clearImportFile() {
            this.importFileName = '';
            this.importFileSize = '';
            if (this.$refs.importFileInput) {
                this.$refs.importFileInput.value = '';
            }
        },

        submitImportForm() {
            this.showImportModal = false;
            this.$refs.importForm?.submit();
        },

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
