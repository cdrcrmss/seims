@once
@push('scripts')
<script>
async function disposeCriticalUnit(button) {
    const url = button.dataset.disposeUrl;
    const unitCode = button.dataset.unitCode || 'this unit';

    if (!url) {
        alert('Dispose action is not available for this unit.');
        return;
    }

    if (!confirm('Mark unit ' + unitCode + ' as disposed? It cannot be borrowed. Any scheduled maintenance for this unit will be cancelled.')) {
        return;
    }

    button.disabled = true;

    try {
        const response = await fetch(url, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({}),
        });

        const data = await response.json().catch(() => ({}));

        if (!response.ok) {
            alert(data.message || 'Could not dispose this unit.');
            button.disabled = false;
            return;
        }

        window.location.reload();
    } catch (e) {
        alert('Could not dispose this unit. Please try again.');
        button.disabled = false;
    }
}
</script>
@endpush
@endonce
