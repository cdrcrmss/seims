@if(session('success'))
@push('scripts')
<script>
(function () {
    const keys = @json($cartStorageKeys ?? []);
    keys.forEach(function (key) {
        try {
            sessionStorage.removeItem(key);
            sessionStorage.removeItem(key + '_return_hours');
            sessionStorage.removeItem(key + '_purpose');
        } catch (e) {}
    });
})();
</script>
@endpush
@endif
