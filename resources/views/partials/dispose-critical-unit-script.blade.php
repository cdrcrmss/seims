@once
@push('scripts')
<script>
async function disposeCriticalUnit(itemId, unitId, unitCode) {
    if (!confirm('Mark unit ' + unitCode + ' as disposed? It cannot be borrowed. Any scheduled maintenance for this unit will be cancelled.')) {
        return;
    }

    const response = await fetch('/staff/items/' + itemId + '/units/' + unitId, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ status: 'disposed' }),
    });

    const data = await response.json().catch(() => ({}));

    if (!response.ok) {
        alert(data.message || 'Could not dispose this unit.');
        return;
    }

    window.location.reload();
}
</script>
@endpush
@endonce
