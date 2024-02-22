<div id="show_order"></div>

<script>
    var tanggalend = $('#tanggalend').val();
    $("#show_order").load(
        "{{ route('kepalaproduksi.index', ['key' => 'order']) }}&tanggal={{ $tanggal }}&tanggalend=" +
        tanggalend);
</script>
