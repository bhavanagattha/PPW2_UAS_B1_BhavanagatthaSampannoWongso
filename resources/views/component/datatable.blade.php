<!-- Tambahkan CSS DataTable -->
<link rel="stylesheet" href="{{ asset('css/dataTables.bootstrap5.min.css') }}" />

<!-- Tambahkan library DataTable -->
<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('js/dataTables.bootstrap5.min.js') }}"></script>

<!-- Inisialisasi DataTable -->
<script>
    $(document).ready(function() {
        $('.datatable').DataTable(); // Pastikan kelas 'datatable' ada di tabel HTML Anda
    });
</script>
