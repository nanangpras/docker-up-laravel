<div class="form-group">
    <label for="">Filter</label>
    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01" @endif
        name="tanggal" class="form-control filter_tanggal" id="tanggal_bb" value="{{ $request->tanggal ?? date("Y-m-d")
        }}" autocomplete="off">
</div>

<div id="sisa_chiller"></div>

<script>
    var tanggal =   $("#tanggal_bb").val() ;
$("#sisa_chiller").load("{{ route('ppic.index', ['view' => 'sisa_chiller']) }}&tanggal=" + tanggal);

$('.filter_tanggal').change(function() {
    tanggal =   $("#tanggal_bb").val() ;
    $("#sisa_chiller").load("{{ route('ppic.index', ['view' => 'sisa_chiller']) }}&tanggal=" + tanggal);
});
</script>

<script>
    $(document).on('click', '.toabf', function() {
    var chiller         =   $(this).data('chiller');
    var plastik         =   $("#plastik" + chiller).val() ;
    var jumlah          =   $("#jumlah" + chiller).val() ;
    var item_jumlah     =   $("#kirim_jumlah" + chiller).val() ;
    var item_berat      =   $("#kirim_berat" + chiller).val() ;
    var tanggal         =   $("#tanggal_bb").val() ;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url: "{{ route('kepalaproduksi.toabfchiller') }}",
        method: "POST",
        data: {
            chiller         :   chiller,
            plastik         :   plastik,
            jumlah          :   jumlah,
            item_jumlah     :   item_jumlah,
            item_berat      :   item_berat,
        },
        success: function(data) {
            if (data.status == 400) {
                showAlert(data.msg)
            } else {
                $("#sisa_chiller").load("{{ route('ppic.index', ['view' => 'sisa_chiller']) }}&tanggal=" + tanggal);
                $('#abf-stock').load("{{route('abf.stock')}}")
                showNotif('Kirim ke ABF Chiller BB Berhasil');
            }
        }
    });
})
</script>