@extends('admin.layout.template')

@section('title', 'Warehouse Dashboard')

@section('content')
<div class="mb-4 text-center font-weight-bold">Warehouse Dashboard</div>

@if ($thawing)
<div class="alert alert-danger text-center mb-3">
    {{ $thawing }} Request Thawing Pending
</div>
@endif

<section class="panel pb-0 sticky-top">
    <div class="card-body p-2">
        <div class="row">
            <div class="col-md-4">
                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-05-27"
                    @else min="2023-05-05" @endif class="form-control change-date mb-2" id="pencarian_awal" value="{{ $tanggal_awal}}"
                    placeholder="Cari...">
                {{-- <label>
                    <input type="checkbox" name="range-tanggal" id="ganti-tanggal"> Tanggal Awal & Akhir
                </label> --}}
            </div>
            <div class="col-md-4" id="panel-tanggal-akhir">
                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-05-27"
                    @else min="2023-05-05" @endif class="form-control change-date mb-2" id="pencarian_akhir" value="{{ $tanggal_akhir}}"
                    placeholder="Cari...">
            </div>
        </div>
        <div id="dashboard-loading" style="height: 30px">
            <div class="text-center mb-2 mt-2" style="position: absolute; left: 0; right: 0;">
                <img src="{{asset('loading.gif')}}" style="width: 20px"> Loading ...
            </div>
        </div>
    </div>
</section>
<section class="panel pb-0">
    <div id="data_view"></div>
</section>
@endsection

@section('header')
<link rel="stylesheet" type="text/css" href="{{asset('')}}highcharts/highcharts-style.css" />
@endsection

@section('footer')

<script src="{{ asset("highcharts/highcharts.js") }}"></script>
<script src="{{ asset("highcharts/highcharts-more.js") }}"></script>
<script src="{{ asset("highcharts/exporting.js") }}"></script>
<script src="{{ asset("highcharts/export-data.js") }}"></script>
<script src="{{ asset("highcharts/accessibility.js") }}"></script>
<script>
    var tanggal_awal    =   $("#pencarian_awal").val();
    var tanggal_akhir   =   $("#pencarian_akhir").val();

    $('#pencarian_awal,#pencarian_akhir').on('change', function() {
        tanggal_awal        = $('#pencarian_awal').val();
        tanggal_akhir       = $("#pencarian_akhir").val();
        setTimeout(function(){
            loadGraphicAllGudang(tanggal_awal,tanggal_akhir)
        },500);
       
    })

    loadGraphicAllGudang(tanggal_awal, tanggal_akhir);

    function loadGraphicAllGudang(tanggal_awal, tanggal_akhir) {
        $('#data_view').load("{{ route('warehouse_dash.dashboard', ['key' => 'view']) }}&tanggal_awal=" + tanggal_awal + "&tanggal_akhir=" + tanggal_akhir , function(){$('#dashboard-loading').hide()});
    }

</script>
@endsection