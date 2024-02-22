<div id="daftar_nonlb"></div>

<script>
    var tanggalend = $('#tanggalend').val();
    $("#daftar_nonlb").load("{{ route('ppic.index', ['view' => 'non_lb']) }}&tanggal={{ $tanggal }}&tanggalend" +
        tanggalend);
</script>
