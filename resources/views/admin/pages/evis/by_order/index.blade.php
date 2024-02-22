@extends('admin.layout.template')

@section('title', 'Input By Order Evis')

@section('content')
<div class="my-4 row">
    <div class="col"><a href="{{ route('evis.index') }}"><i class="fa fa-arrow-left"></i> Back</a></div>
    <div class="col-8 font-weight-bold text-center">INPUT BY ORDER EVIS</div>
    <div class="col"></div>
</div>

<section class="panel">
    <div class="card-body">
        <div class="row">
            <div class="col pr-1">
                <label for="tanggal_request">Tanggal Kirim</label>
                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01" @endif
                    id="tanggal_request" class="form-control" value="{{ date("Y-m-d", strtotime("tomorrow")) }}">
            </div>
            <div class="col pl-1">
                <label for="cari_request">Cari Data</label>
                <input type="text" placeholder="Cari..." autocomplete="off" id="cari_request" class="form-control">
            </div>
        </div>
        <div class="mt-2">
            <input type="checkbox" id="menunggu"> <label for="menunggu">Pending Fulfillment</label> &nbsp
            <input type="radio" id="input-fresh" name="jenis" checked> <label for="input-fresh">Fresh</label> &nbsp
            <input type="radio" id="input-frozen" name="jenis"> <label for="input-frozen">Frozen</label> &nbsp
            <input type="radio" id="input-semua" name="jenis"> <label for="input-semua">Semua</label> &nbsp
        </div>

        <h5 id="loading_request" style="display: none"><i class="fa fa-refresh fa-spin"></i> Loading.....</h5>
        <div id="data_request"></div>

    </div>
</section>
@endsection

@section('footer')
<script>
    $("#loading_request").attr("style", "display: block") ;
$("#data_request").load("{{ route('regu.request_order', ['key' => 'view']) }}&regu=byproduct", function() {
    $("#loading_request").attr("style", "display: none") ;
}) ;
</script>

<script>
    var menunggu    =   $("#menunggu:checked").val();
var tanggal     =   $("#tanggal_request").val();
var cari        =   encodeURIComponent($("#cari_request").val());
var fresh       =   $("#input-fresh:checked").val();
var frozen      =   $("#input-frozen:checked").val();
var semua      =   $("#input-semua:checked").val();

$("#input-frozen").on('change', function() {
    reloadDataOrder();
});

$("#input-fresh").on('change', function() {
    reloadDataOrder();
});
$("#input-semua").on('change', function() {
    reloadDataOrder();
});

$("#menunggu").on('change', function() {
    reloadDataOrder();
});

$("#tanggal_request").on('change', function() {
    reloadDataOrder();
});

$("#cari_request").on('keyup', function() {
    reloadDataOrder();
});

function reloadDataOrder(){

    fresh   =   $("#input-fresh:checked").val();
    frozen  =   $("#input-frozen:checked").val();
    semua   =   $("#input-semua:checked").val();
    menunggu=   $("#menunggu:checked").val();
    tanggal =   $("#tanggal_request").val();
    cari    =   encodeURIComponent($("#cari_request").val());

    var load_url = "{{ route('regu.request_order', ['key' => 'view']) }}&regu=byproduct&tanggal=" + tanggal + "&cari=" + cari + "&menunggu=" + menunggu + "&fresh=" + fresh + "&frozen=" + frozen + "&semua=" + semua;
    console.log(load_url);

    $("#data_request").attr("style", "display: none") ;
    $("#loading_request").attr("style", "display: block") ;
    $("#data_request").load(load_url, function() {
        $("#data_request").attr("style", "display: block") ;
        $("#loading_request").attr("style", "display: none") ;
    }) ;
}

</script>
@endsection