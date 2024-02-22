<div id="chiller_penyiapan"></div>

<script>
    var tanggalend = $('#tanggalend').val();
    $("#chiller_penyiapan").load(
        "{{ route('ppic.index', ['view' => 'chiller_penyiapan']) }}&tanggal={{ $tanggal }}&tanggalend="+tanggalend);
</script>
