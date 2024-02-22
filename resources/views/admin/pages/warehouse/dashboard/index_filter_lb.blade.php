@extends('admin.layout.template')

@section('title', 'Dashboard Filter Sebaran Live Bird Supplier')

@section('content')

<div class="mb-4 mt-4 text-center font-weight-bold">Grafik Sebaran Live Bird Semua Supplier</div>

<div class="card mb-4">
    <div class="card-body p-2">
        <div style="float: left;">
            <div class="row">
                <div class="col-6">
                    <label for="tanggal_awal"> Tanggal Awal</label>
                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                        min="2023-01-01" @endif class="form-control mb-2" id="tanggal_awal" value="{{ $tanggal_akhir}}"
                        placeholder="Cari...">
                </div>
                <div class="col-6">
                    <label for="tanggal_akhir"> Tanggal Akhir</label>
                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                        min="2023-01-01" @endif class="form-control mb-2" id="tanggal_akhir"
                        value="{{ $tanggal_akhir }}" placeholder="Cari...">
                </div>
            </div>
        </div>
    </div>
    <div id="show_view_page"></div>
</div>

@stop

@section('header')
<link rel="stylesheet" type="text/css" href="{{asset('')}}highcharts/highcharts-style.css" />
@stop

@section('footer')
<script src="{{ asset("highcharts/highcharts.js") }}"></script>
<script src="{{ asset("highcharts/highcharts-more.js") }}"></script>
<script src="{{ asset("highcharts/exporting.js") }}"></script>
<script src="{{ asset("highcharts/export-data.js") }}"></script>
<script src="{{ asset("highcharts/accessibility.js") }}"></script>
<script>
    var tanggal_awal    = $("#tanggal_awal").val();
    var tanggal_akhir   = $("#tanggal_akhir").val();

    $('#tanggal_awal,#tanggal_akhir').on('change', function() {
        tanggal_awal    = $("#tanggal_awal").val();
        tanggal_akhir   = $("#tanggal_akhir").val();

        setTimeout(function(){
            loadGraphicAllSupplierLb(tanggal_awal,tanggal_akhir)
        },1500);
    })

    loadGraphicAllSupplierLb(tanggal_awal, tanggal_akhir);
    function loadGraphicAllSupplierLb(tanggal_awal, tanggal_akhir) {
        $('#show_view_page').load("{{ route('warehouse_dash.filter_lb', ['key' => 'view_page']) }}&tanggal_awal=" + tanggal_awal + "&tanggal_akhir=" + tanggal_akhir)
    }
</script>
@stop