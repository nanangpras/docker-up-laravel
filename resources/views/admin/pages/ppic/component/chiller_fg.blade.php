<input type="checkbox" id="non-abf">
<label for="non-abf">Non ABF</label>

<span class="ml-3">
    <input type="checkbox" id="selonjor">
    <label for="selonjor">Selonjor</label>
</span>

<span class="ml-3">
    <a href="javascript:void(0)" id="download-abf">Download</a>
</span>

<div class="mt-3" id="chiller_fg"></div>

<script>
var tanggal     =   $("#tanggal").val() ;
var tanggalend  =   $("#tanggalend").val() ;
var non_abf     =   $('#non-abf').is(':checked');
var selonjor    =   $('#selonjor').is(':checked');
var field       =   $("#field").val() ;
var orderby     =   $("#orderby").val() ;
var cari_data   =   $("#cari_data").val() ;
var status      =   $("#status_acc").val() ;

loadChillerFg();

$('#non-abf').on('change', function(){
    loadChillerFg();
});

$('#selonjor').on('change', function(){
    loadChillerFg();
});

$('.tanggal').change(function() {
    loadChillerFg();
});

$('#cari_data').on('keyup', function() {
    loadChillerFg();
});

function loadChillerFg(){
    tanggal     =   $("#tanggal").val() ;
    tanggalend  =   $("#tanggalend").val() ;
    non_abf     =   $('#non-abf').is(':checked');
    selonjor    =   $('#selonjor').is(':checked');
    field       =   $("#field").val() ;
    orderby     =   $("#orderby").val() ;
    cari_data   =   encodeURIComponent($("#cari_data").val()) ;
    status      =   $("#status_acc").val() ;

    url_stock = "{{ route('ppic.index', ['view' => 'chiller_fg']) }}&tanggal=" + tanggal +"&tanggalend=" + tanggalend + "&field=" + field + "&orderby=" + orderby + "&cari=" + cari_data + "&status=" + status +"&abf=" + non_abf + "&selonjor=" + selonjor;
    $("#chiller_fg").load(url_stock);
}

$('#download-abf').on('click', function(){
    tanggal     =   $("#tanggal").val() ;
    tanggalend  =   $("#tanggalend").val() ;
    non_abf     =   $('#non-abf').is(':checked');
    selonjor    =   $('#selonjor').is(':checked');
    field       =   $("#field").val() ;
    orderby     =   $("#orderby").val() ;
    cari_data   =   encodeURIComponent($("#cari_data").val()) ;
    status      =   $("#status_acc").val() ;

    window.location.href = "{{ route('ppic.index', ['view' => 'chiller_fg']) }}&tanggal=" + tanggal +"&tanggalend=" + tanggalend + "&field=" + field + "&orderby=" + orderby + "&cari=" + cari_data + "&status=" + status +"&abf=" + non_abf + "&selonjor=" + selonjor + "&action=unduh";
})

</script>

<script>
$(document).on('click', '.toabf_fg', function() {
    var chiller         =   $(this).data('chiller');
    var item_jumlah     =   $("#kirim_jumlah" + chiller).val() ;
    var item_berat      =   $("#kirim_berat" + chiller).val() ;
    var tanggal         =   $("#tanggal_fg").val() ;
    var non_abf         =   $('#non-abf').is(':checked');
    var tanggal         =   $("#tanggal").val() ;
    var tanggalend      =   $("#tanggalend").val() ;
    var field           =   $("#field").val() ;
    var orderby         =   $("#orderby").val() ;
    var cari_data       =   $("#cari_data").val() ;
    var status          =   $("#status_acc").val() ;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url: "{{ route('ppic.toabf_fg') }}",
        method: "POST",
        data: {
            chiller         :   chiller,
            item_jumlah     :   item_jumlah,
            item_berat      :   item_berat,
        },
        success: function(data) {
            if (data.status == 400) {
                showAlert(data.msg) ;
            } else {
                if(non_abf==true){
                    url_stock = "{{ route('ppic.index', ['view' => 'chiller_fg']) }}&tanggal=" + tanggal +"&tanggalend=" + tanggalend + "&field=" + field + "&orderby=" + orderby + "&cari=" + cari_data + "&status=" + status +"&abf=false";
                }else{
                    url_stock = "{{ route('ppic.index', ['view' => 'chiller_fg']) }}&tanggal=" + tanggal +"&tanggalend=" + tanggalend + "&field=" + field + "&orderby=" + orderby + "&cari=" + cari_data + "&status=" + status +"&abf=true";
                }
                $("#chiller_fg").load(url_stock);
                $('#abf-stock').load("{{ route('abf.stock') }}");
                showNotif('Kirim ke ABF berhasil');
            }
        }
    });
})
</script>
