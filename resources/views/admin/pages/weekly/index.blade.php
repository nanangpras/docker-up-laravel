@extends('admin.layout.template')

@section('title', 'Laporan Weekly')

@section('content')
<div class="row mb-4">
    <div class="col">
        <a href="{{ route('index') }}" class="btn btn-outline btn-sm btn-back"> <i class="fa fa-arrow-left"></i>
            Back</a>
    </div>
    <div class="col text-center">
        <b>Laporan Weekly</b>
    </div>
    <div class="col text-right">
        {{-- <a href="{{ route('laporan.qc') }}" class="btn btn-primary"> Export</a> --}}
    </div>
</div>

<section class="panel">
    <div class="card-body">
        <div class="row">
            <div class="col">
                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                    @endif id="mulai_sumary"
                    value="{{ Request::get('mulai') ?? date('Y-m-d', strtotime(date('Y-m-d') . '-6 Day')) }}"
                    class="filter_tanggal form-control">
            </div>
            <div class="col">
                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                    @endif id="akhir_sumary" value="{{ Request::get('akhir') ?? date('Y-m-d') }}"
                    class="filter_tanggal form-control">
            </div>
        </div>
    </div>
</section>

<section class="panel">
    <div class="card-body">
        <div id="hasil_produksi_loading" class="text-center">
            <img src="{{asset('loading.gif')}}" style="width: 30px">
        </div>
        <div id="hasil_produksi"></div>
    </div>
</section>
<section class="panel">
    <div class="card-body">
        <div id="bb_lama_loading" class="text-center">
            <img src="{{asset('loading.gif')}}" style="width: 30px">
        </div>
        <div id="bb_lama"></div>
    </div>
</section>
<section class="panel">
    <div class="card-body">
        <div id="lama_loading" class="text-center">
            <img src="{{asset('loading.gif')}}" style="width: 30px">
        </div>
        <div id="lama"></div>
    </div>
</section>
<section class="panel">
    <div class="card-body">
        <div id="thawing_loading" class="text-center">
            <img src="{{asset('loading.gif')}}" style="width: 30px">
        </div>
        <div id="thawing"></div>
    </div>
</section>
<section class="panel">
    <div class="card-body">
        <div id="retur_loading" class="text-center">
            <img src="{{asset('loading.gif')}}" style="width: 30px">
        </div>
        <div id="retur"></div>
    </div>
</section>
<section class="panel">
    <div class="card-body">
        <div id="frozen_loading" class="text-center">
            <img src="{{asset('loading.gif')}}" style="width: 30px">
        </div>
        <div id="stock_frozen"></div>
    </div>
</section>
<section class="panel">
    <div class="card-body">
        <div id="beli_loading" class="text-center">
            <img src="{{asset('loading.gif')}}" style="width: 30px">
        </div>
        <div id="beli"></div>
    </div>
</section>
<section class="panel">
    <div class="card-body">
        <div class="row">
            <div class="col-4">
                <div id="grafik_pemotongan"></div>
            </div>
            <div class="col-4">
                <div id="grafik_parting"></div>
            </div>
            <div class="col-4">
                <div id="grafik_whole"></div>
            </div>
            <div class="col-4">
                <div id="grafik_boneless"></div>
            </div>
            <div class="col-4">
                <div id="grafik_frozen"></div>
            </div>
            <div class="col-4">
                <div id="grafik_total"></div>
            </div>
        </div>
    </div>
</section>

<section class="panel">
    <div class="card-body">
        <div id="perbandingan_mingguan_loading" class="text-center">
            <img src="{{asset('loading.gif')}}" style="width: 30px">
        </div>
        <div id="perbandingan_mingguan"></div>
    </div>
</section>

@endsection
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
    var mulai = $("#mulai_sumary").val();
        var akhir = $("#akhir_sumary").val();
        var sub = $("#subsidiary").val();

        filterChange()

        $('.filter_tanggal').change(function() {
            filterChange();
        });

        function filterChange() {

            mulai = $("#mulai_sumary").val();
            akhir = $("#akhir_sumary").val();

            url = "{{ route('weekly.index') }}?mulai=" + mulai + "&akhir=" + akhir;

            window.history.pushState('Weekly', 'Weekly', url);

            $('#hasil_produksi_loading').show();
            $('#bb_lama_loading').show();
            $('#beli_loading').show();
            $('#lama_loading').show();
            $('#thawing_loading').show();
            $('#retur_loading').show();
            $('#frozen_loading').show();
            $('#perbandingan_mingguan_loading').show();

            $('#lama').load("{{ route('weekly.index', ['key' => 'lama']) }}&mulai=" + mulai + "&akhir=" + akhir, function(){ $('#lama_loading').hide(); });
            $("#hasil_produksi").load("{{ route('weekly.index', ['key' => 'hasil_produksi']) }}&mulai=" + mulai + "&akhir=" + akhir, function(){ $('#hasil_produksi_loading').hide(); });
            $("#bb_lama").load("{{ route('weekly.index', ['key' => 'bb_lama']) }}&mulai=" + mulai + "&akhir=" + akhir, function(){ $('#bb_lama_loading').hide(); });
            $("#beli").load("{{ route('weekly.index', ['key' => 'beli']) }}&mulai=" + mulai + "&akhir=" + akhir, function(){ $('#beli_loading').hide(); });
            $("#thawing").load("{{ route('weekly.index', ['key' => 'thawing']) }}&mulai=" + mulai + "&akhir=" + akhir, function(){ $('#thawing_loading').hide(); });
            $("#retur").load("{{ route('weekly.index', ['key' => 'retur']) }}&mulai=" + mulai + "&akhir=" + akhir, function(){ $('#retur_loading').hide(); });
            $("#stock_frozen").load("{{ route('weekly.index', ['key' => 'stock_frozen']) }}&mulai=" + mulai + "&akhir=" + akhir, function(){ $('#frozen_loading').hide(); });
            $("#grafik_pemotongan").load("{{ route('weekly.index', ['key' => 'grafik_pemotongan']) }}&mulai=" + mulai + "&akhir=" + akhir);
            $("#grafik_parting").load("{{ route('weekly.index', ['key' => 'grafik_parting']) }}&mulai=" + mulai + "&akhir=" + akhir);
            $("#grafik_whole").load("{{ route('weekly.index', ['key' => 'grafik_whole']) }}&mulai=" + mulai + "&akhir=" + akhir);
            $("#grafik_boneless").load("{{ route('weekly.index', ['key' => 'grafik_boneless']) }}&mulai=" + mulai + "&akhir=" + akhir);
            $("#grafik_frozen").load("{{ route('weekly.index', ['key' => 'grafik_frozen']) }}&mulai=" + mulai + "&akhir=" + akhir);
            $("#grafik_total").load("{{ route('weekly.index', ['key' => 'grafik_total']) }}&mulai=" + mulai + "&akhir=" + akhir);
            $("#perbandingan_mingguan").load("{{ route('weekly.index', ['key' => 'perbandingan_mingguan']) }}&mulai=" + mulai + "&akhir=" + akhir, function(){ $('#perbandingan_mingguan_loading').hide(); });
        };
</script>
@endsection