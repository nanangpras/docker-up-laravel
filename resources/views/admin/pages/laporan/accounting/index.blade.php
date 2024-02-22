@extends('admin.layout.template')

@section('title', 'Laporan Accounting')

@section('footer')
<script>
    var hash = window.location.hash.substr(1);
var href = window.location.href;

deafultPage();

function deafultPage() {
if (hash == undefined || hash == "") {
    hash = "custom-tabs-three-accounting";
}

$('#' + hash + '-tab').addClass('active').siblings().removeClass('active');
$('#' + hash).addClass('active show').siblings().removeClass('active show');

}


$("#loading").attr('style', 'display: block') ;
$("#data_view").load("{{ route('laporanaccounting.index', ['key' => 'view']) }}", function() {
    $("#loading").attr('style', 'display: none') ;
}) ;

$("#data_purc").load("{{ route('laporanaccounting.index', ['key' => 'purchase']) }}", function() {
    $("#loading").attr('style', 'display: none') ;
}) ;

$("#tanggal_awal").on('change', function() {
    var tanggal_awal    =   $("#tanggal_awal").val() ;
    var tanggal_akhir   =   $("#tanggal_akhir").val() ;
    $("#loading").attr('style', 'display: block') ;
    $("#data_view").load("{{ route('laporanaccounting.index', ['key' => 'view']) }}&tanggal_awal=" + tanggal_awal + "&tanggal_akhir=" + tanggal_akhir, function() {
        $("#loading").attr('style', 'display: none') ;
    }) ;
    $("#data_purc").load("{{ route('laporanaccounting.index', ['key' => 'purchase']) }}&tanggal_awal=" + tanggal_awal + "&tanggal_akhir=" + tanggal_akhir, function() {
        $("#loading").attr('style', 'display: none') ;
    }) ;
});

$("#tanggal_akhir").on('change', function() {
    var tanggal_awal    =   $("#tanggal_awal").val() ;
    var tanggal_akhir   =   $("#tanggal_akhir").val() ;
    $("#loading").attr('style', 'display: block') ;
    $("#data_view").load("{{ route('laporanaccounting.index', ['key' => 'view']) }}&tanggal_awal=" + tanggal_awal + "&tanggal_akhir=" + tanggal_akhir, function() {
        $("#loading").attr('style', 'display: none') ;
    }) ;
    $("#data_purc").load("{{ route('laporanaccounting.index', ['key' => 'purchase']) }}&tanggal_awal=" + tanggal_awal + "&tanggal_akhir=" + tanggal_akhir, function() {
        $("#loading").attr('style', 'display: none') ;
    }) ;
});
</script>
@endsection

@section('content')
<div class="my-4 text-center font-weight-bold text-uppercase">Laporan Accounting</div>
<ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
    <li class="nav-item">
        <a class="nav-link tab-link" id="custom-tabs-three-accounting-tab" data-toggle="pill"
            href="#custom-tabs-three-accounting" role="tab" aria-controls="custom-tabs-three-accounting"
            aria-selected="true">
            ACCOUNTING
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link tab-link" id="custom-tabs-three-order-tab" data-toggle="pill" href="#custom-tabs-three-order"
            role="tab" aria-controls="custom-tabs-three-order" aria-selected="false">
            REKAP ORDER
        </a>
    </li>
</ul>
<section class="panel">
    <div class="card-body card-tabs">
        <div class="tab-content" id="custom-tabs-three-tabContent">
            <div class="tab-pane fade show active" id="custom-tabs-three-accounting" role="tabpanel"
                aria-labelledby="custom-tabs-three-accounting-tab">
                <section class="panel">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                    min="2023-01-01" @endif value="{{ date("Y-m-d") }}" id="tanggal_awal"
                                    class="form-control">
                            </div>
                            <div class="col">
                                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                    min="2023-01-01" @endif value="{{ date("Y-m-d") }}" id="tanggal_akhir"
                                    class="form-control">
                            </div>
                        </div>
                    </div>
                </section>

                <section class="panel">
                    <div class="card-body">
                        <h5 id="loading" class="text-center"><i class="fa fa-refresh fa-spin"></i> Loading....</h5>
                        <div id="data_view"></div>
                        <div id="data_purc" class="mt-4"></div>
                    </div>
                </section>
            </div>

            <div class="tab-pane fade" id="custom-tabs-three-order" role="tabpanel"
                aria-labelledby="custom-tabs-three-order-tab">
                <div>@include('admin.pages.menu_order.rekap_order')</div>
            </div>
        </div>
    </div>
</section>

@endsection