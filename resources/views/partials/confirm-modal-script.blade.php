<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('confirmModal', () => ({
        open: false,
        title: '',
        message: '',
        type: 'warning',
        confirmLabel: '',
        alertOnly: false,
        processing: false,
        pendingForm: null,
        pendingAction: null,
        pendingDisposeUrl: null,
        pendingDisposeUnitCode: null,
        requireReason: false,
        reasonField: 'rejection_reason',
        reasonLabel: 'Reason',
        reasonPlaceholder: '',
        reasonText: '',
        reasonError: '',

        show(detail) {
            this.title = detail.title || 'Confirm Action';
            this.message = detail.message || 'Are you sure you want to proceed?';
            this.type = detail.type || 'warning';
            this.confirmLabel = detail.confirmLabel || '';
            this.alertOnly = detail.alertOnly === true;
            this.processing = false;
            this.pendingForm = detail.form || null;
            this.pendingAction = detail.action || null;
            this.pendingDisposeUrl = detail.disposeUrl || null;
            this.pendingDisposeUnitCode = detail.unitCode || null;
            this.requireReason = detail.requireReason === true;
            this.reasonField = detail.reasonField || 'rejection_reason';
            this.reasonLabel = detail.reasonLabel || 'Reason';
            this.reasonPlaceholder = detail.reasonPlaceholder || 'Enter a reason...';
            this.reasonText = '';
            this.reasonError = '';
            this.open = true;
        },

        confirmButtonLabel() {
            if (this.confirmLabel) {
                return this.confirmLabel;
            }
            const t = (this.title || '').toLowerCase();
            if (this.type === 'success') {
                if (t.includes('approve')) return 'Approve';
                if (t.includes('restore')) return 'Restore';
                return 'Confirm';
            }
            if (this.type === 'danger') {
                if (t.includes('reject')) return 'Reject';
                if (t.includes('cancel')) return 'Cancel';
                if (t.includes('delete')) return 'Delete';
                if (t.includes('dispose')) return 'Dispose';
                return 'Confirm';
            }
            return 'Confirm';
        },

        async proceed() {
            if (this.processing) {
                return;
            }
            if (this.alertOnly) {
                this.reset();
                return;
            }
            if (this.pendingForm) {
                if (this.requireReason) {
                    const reason = (this.reasonText || '').trim();
                    if (!reason) {
                        this.reasonError = 'Please enter a reason before continuing.';
                        return;
                    }
                    const fieldName = this.reasonField || 'rejection_reason';
                    let input = this.pendingForm.querySelector(`[name="${fieldName}"]`);
                    if (!input) {
                        input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = fieldName;
                        this.pendingForm.appendChild(input);
                    }
                    input.value = reason;
                }
                const form = this.pendingForm;
                this.reset();
                form.submit();
                return;
            }
            if (this.pendingAction === 'dispose-unit') {
                await this.disposeUnit();
                return;
            }
            this.reset();
        },

        async disposeUnit() {
            const url = this.pendingDisposeUrl;
            const unitCode = this.pendingDisposeUnitCode || 'this unit';

            if (!url) {
                this.showError('Unable to dispose', 'Dispose action is not available for this unit.');
                return;
            }

            this.processing = true;
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            try {
                const response = await fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({}),
                });

                const data = await response.json().catch(() => ({}));

                if (!response.ok) {
                    this.showError('Unable to dispose', data.message || ('Could not dispose unit ' + unitCode + '.'));
                    return;
                }

                this.reset();
                window.location.reload();
            } catch (e) {
                this.showError('Unable to dispose', 'Could not dispose unit ' + unitCode + '. Please try again.');
            } finally {
                if (!this.alertOnly) {
                    this.processing = false;
                }
            }
        },

        showError(title, message) {
            this.title = title;
            this.message = message;
            this.type = 'warning';
            this.confirmLabel = 'OK';
            this.alertOnly = true;
            this.processing = false;
            this.pendingForm = null;
            this.pendingAction = null;
            this.pendingDisposeUrl = null;
            this.pendingDisposeUnitCode = null;
            this.open = true;
        },

        reset() {
            this.open = false;
            this.pendingForm = null;
            this.pendingAction = null;
            this.pendingDisposeUrl = null;
            this.pendingDisposeUnitCode = null;
            this.alertOnly = false;
            this.processing = false;
            this.requireReason = false;
            this.reasonText = '';
            this.reasonError = '';
        },

        cancel() {
            this.reset();
        },
    }));
});
</script>
