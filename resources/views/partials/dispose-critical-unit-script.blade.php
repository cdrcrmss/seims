@once
@push('scripts')
<script>
function showAppAlert(title, message, type = 'warning') {
    window.dispatchEvent(new CustomEvent('open-confirm-modal', {
        detail: {
            title,
            message,
            type,
            confirmLabel: 'OK',
            alertOnly: true,
        },
    }));
}

async function disposeCriticalUnitConfirmed(button) {
    const url = button.dataset.disposeUrl;
    const unitCode = button.dataset.unitCode || 'this unit';

    if (!url) {
        showAppAlert('Unable to dispose', 'Dispose action is not available for this unit.');
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
            showAppAlert('Unable to dispose', data.message || 'Could not dispose unit ' + unitCode + '.');
            button.disabled = false;
            return;
        }

        window.location.reload();
    } catch (e) {
        showAppAlert('Unable to dispose', 'Could not dispose unit ' + unitCode + '. Please try again.');
        button.disabled = false;
    }
}
</script>
@endpush
@endonce
