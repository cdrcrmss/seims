<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('maintenanceCompleteModal', () => ({
        open: false,
        completeId: null,
        conditionAfter: 'good',
        wearMap: @json(\App\Models\MaintenanceRecord::WEAR_BY_CONDITION_AFTER),

        get predictedWear() {
            return this.wearMap[this.conditionAfter] ?? 25;
        },

        wearBarColor() {
            const w = this.predictedWear;
            if (w >= 70) return 'bg-red-500';
            if (w >= 40) return 'bg-yellow-500';
            return 'bg-green-500';
        },

        completeUrl() {
            return this.completeId ? '{{ url('/maintenance') }}/' + this.completeId + '/complete' : '#';
        },

        show(detail) {
            this.completeId = detail?.id ?? null;
            this.conditionAfter = 'good';
            this.open = this.completeId !== null;
        },

        close() {
            this.open = false;
            this.completeId = null;
        },
    }));
});
</script>
