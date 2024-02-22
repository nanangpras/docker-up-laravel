<div class="row">
    <div class="col">
        <div class="form-group">
            <label for="tanggal">Filter</label>
            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                @endif name="tanggal" class="form-control tanggal" id="tanggal" value="{{ date('Y-m-d', strtotime("-7 days", time())) }}" autocomplete="off">
        </div>
    </div>
    <div class="col">
        <div class="form-group">
            <label for="tanggalend">&nbsp;</label>
            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                @endif name="tanggal" class="form-control tanggal" id="tanggalend" value="{{ date('Y-m-d') }}"
                autocomplete="off">
        </div>
    </div>
    <div class="col">
        <div class="form-group">
            <label for="field">Kolom</label>
            <select name="field" id="field" class="form-control tanggal">
                <option value="item_name">Nama</option>
                <option value="stock_item">Ekor/Pcs/Pack</option>
                <option value="stock_berat">Berat (Kg)</option>
                <option value="tanggal_produksi">Tanggal Bahan Baku</option>
            </select>
        </div>
    </div>
    <div class="col">
        <div class="form-group">
            <label for="orderby">Order By</label>
            <select name="orderby" id="orderby" class="form-control tanggal">
                <option value="asc">Ascending</option>
                <option value="desc">Descending</option>
            </select>
        </div>
    </div>
    <div class="col">
        <div class="form-group">
            <label for="status_acc">Status</label>
            <select name="status_acc" id="status_acc" class="form-control tanggal">
                <option value="ready">Pending</option>
                <option value="dipindahkan">Selesai</option>
            </select>
        </div>
    </div>
</div>

<div class="row">
    <div class="col">
        <div class="form-group">
            <label for="kategori">Kategori</label>
            @php
            $kat = App\Models\Category::where('id', '<=', 20)->get();
                @endphp
                <select name="kategori" id="kategori" class="form-control kategori">
                    <option value="0">- Semua Kategori -</option>
                    @foreach($kat as $k)
                    <option value="{{$k->id}}">{{$k->nama}}</option>
                    @endforeach
                </select>
        </div>
    </div>
    <div class="col">
        <div class="form-group">
            <label for="cari_data">Pencarian</label>
            <input type="text" autocomplete="off" placeholder="Cari..." id="cari_data" class="form-control">
        </div>
    </div>
</div>



@if(env('NET_SUBSIDIARY', 'CGL')=='CGL')
<input type="checkbox" id="non-abf">
<label for="non-abf">Non ABF (Chiller FG)</label>
@endif

@if(env('NET_SUBSIDIARY', 'CGL')=='EBA')
<span class="ml-3">
    <input type="checkbox" id="selonjor">
    <label for="selonjor">Selonjor</label>
</span>
<span class="ml-3">
    <input type="checkbox" id="boneless">
    <label for="boneless">Boneless</label>
</span>
<span class="ml-3">
    <input type="checkbox" id="parting">
    <label for="parting">Parting</label>
</span>
<span class="ml-3">
    <input type="checkbox" id="memar">
    <label for="memar">Memar</label>
</span>
@endif

<span class="ml-3">
    {{-- <a href="javascript:void(0)" id="download-abf">Download</a> --}}
</span>
<div id="spinerabfstock" class="text-center" style="display: block">
    <img src="{{ asset('loading.gif') }}" width="30px">
</div>
<div class="mt-3" id="chiller_fg"></div>

<script>
    if (window.location.hash.substr(1) == "#custom-tabs-diterima") {
    load_abf_diterima();
}
$("#tabs-chiller-fg-tab").on('click', function(){
    loadChillerFg();
});
$('#non-abf,#selonjor,#memar,#boneless,#parting,#kategori,.tanggal').on('change', function(){
    loadChillerFg();
});
$('#cari_data').on('keyup', function() {
    setTimeout(() => {
        loadChillerFg();
    }, 1000);
});
function loadChillerFg(){
    $("#spinerabfstock").show()
    var tanggal     =   $("#tanggal").val() ;
    var tanggalend  =   $("#tanggalend").val() ;
    var non_abf     =   $('#non-abf').is(':checked');
    var selonjor    =   $('#selonjor').is(':checked');
    var memar       =   $('#memar').is(':checked');
    var boneless    =   $('#boneless').is(':checked');
    var parting     =   $('#parting').is(':checked');
    var field       =   $("#field").val() ;
    var orderby     =   $("#orderby").val() ;
    var kategori    =   $("#kategori").val() ;
    var cari_data   =   encodeURIComponent($("#cari_data").val()) ;
    var status      =   $("#status_acc").val() ;

    $.ajax({
        url : "{{ route('abf.chiller_abf_stock') }}?tanggal=" + tanggal +"&tanggalend=" + tanggalend + "&field=" + field + "&orderby=" + orderby + "&cari=" + cari_data + "&status=" + status + "&kategori="+kategori+"&abf=" + non_abf + "&selonjor=" + selonjor+ "&action=data" + "&memar=" + memar + "&boneless=" + boneless + "&parting=" + parting,
        method: "GET",
        cache: false,
        success: function(data){
            $("#chiller_fg").html(data);
            $("#spinerabfstock").hide();
        }
    });
}

$('#download-abf').on('click', function(){
    var tanggal     =   $("#tanggal").val() ;
    var tanggalend  =   $("#tanggalend").val() ;
    var field       =   $("#field").val() ;
    var orderby     =   $("#orderby").val() ;
    var kategori    =   $("#kategori").val() ;
    var status      =   $("#status_acc").val() ;
    var non_abf     =   $('#non-abf').is(':checked');
    var selonjor    =   $('#selonjor').is(':checked');
    var memar       =   $('#memar').is(':checked');
    var boneless    =   $('#boneless').is(':checked');
    var parting     =   $('#parting').is(':checked');
    var cari_data   =   encodeURIComponent($("#cari_data").val()) ;

    window.location.href = "{{ route('abf.chiller_abf_stock') }}?tanggal=" + tanggal +"&tanggalend=" + tanggalend + "&field=" + field + "&orderby=" + orderby + "&cari=" + cari_data + "&status=" + status + "&kategori="+kategori+"&abf=" + non_abf + "&selonjor=" + selonjor + "&action=unduh" + "&memar=" + memar + "&boneless=" + boneless + "&parting=" + parting;
})
</script>

<script>
    var subsidiary = "{{env('NET_SUBSIDIARY', 'CGL')}}";

$(document).on('click', '.toabf_fg', function() {
    $('.toabf_fg').hide()
    var chiller         =   $(this).data('chiller');
    var nama            =   $(this).data('nama');
    var item_jumlah     =   $("#kirim_jumlah" + chiller).val() ;
    var item_berat      =   $("#kirim_berat" + chiller).val() ;
    var tanggal_terima  =   $("#tanggal_terima" + chiller).val() ;
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

    if(subsidiary=="CGL"){

        if(confirm("Kirim "+nama+" ke ABF sebanyak "+item_jumlah+"pcs/pack/ekor dan "+item_berat+"kg ?")){

        }else{
            showAlert('Dibatalkan');
            return false;
        }

    }

    if(item_berat=="" || item_berat==undefined){
        showAlert('Berat masih kosong');
        return false;
    }

    $.ajax({
        url: "{{ route('abf.chiller_kirim_abf') }}",
        method: "POST",
        data: {
            chiller         :   chiller,
            item_jumlah     :   item_jumlah,
            item_berat      :   item_berat,
            tanggal         :   tanggal_terima
        },
        success: function(data) {
            if (data.status == 400) {
                showAlert(data.msg) ;
            } else {
                loadChillerFg();
                load_abf_diterima();
                showNotif('Kirim ke ABF berhasil');
            }
            $('.toabf_fg').show()
        }
    });
})
</script>