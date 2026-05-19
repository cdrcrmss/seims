{{-- Force server round-trip when page is restored from browser back/forward cache (bfcache). --}}
<script>
(function () {
    window.addEventListener('pageshow', function (event) {
        if (event.persisted) {
            window.location.reload();
        }
    });
})();
</script>
