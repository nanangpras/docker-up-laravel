@extends('cloudreport.template')

@section('title', 'Laporan Dashboard')

@section('footer')
<script>
    var mulai   =   $("#mulai_sumary").val() ;
var akhir   =   $("#akhir_sumary").val() ;
var sub     =   $("#subsidiary").val() ;

filterChange()

$('.filter_tanggal').change(function() {
    filterChange();
});

function filterChange(){

    mulai   =   $("#mulai_sumary").val() ;
    akhir   =   $("#akhir_sumary").val() ;
    sub     =   $("#subsidiary").val() ;

    $('#rendemen_report').load("{{ route('cloud.report.dashboard', ['key' => 'rendemen']) }}&mulai=" + mulai + "&akhir=" + akhir + "&subsidiary=" + sub);
    $('#rendemen_table_report').load("{{ route('cloud.report.dashboard', ['key' => 'rendemen_table']) }}&mulai=" + mulai + "&akhir=" + akhir + "&subsidiary=" + sub);
    $('#produksi_evis').load("{{ route('cloud.report.dashboard', ['key' => 'evis']) }}&mulai=" + mulai + "&akhir=" + akhir + "&subsidiary=" + sub);
    $('#produksi_karkas').load("{{ route('cloud.report.dashboard', ['key' => 'karkas']) }}&mulai=" + mulai + "&akhir=" + akhir + "&subsidiary=" + sub);
    $('#info_do').load("{{ route('cloud.report.dashboard', ['key' => 'do']) }}&mulai=" + mulai + "&akhir=" + akhir + "&subsidiary=" + sub);
};

</script>
@endsection

@section('content')
<section class="panel">
    <div class="card-body">
        <div class="row">
            <div class="col">
                <select name="subsidiary" id="subsidiary" class="form-control">
                    <option value="2">CGL</option>
                    {{-- <option value="2">EBA</option> --}}
                </select>
            </div>
            <div class="col">
                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                    @endif id="mulai_sumary" value="{{ date('Y-m-d', strtotime(date('Y-m-d') . '-1 week')) }}"
                    class="filter_tanggal form-control">
            </div>
            <div class="col">
                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                    @endif id="akhir_sumary" value="{{ date("Y-m-d") }}" class="filter_tanggal form-control">
            </div>
        </div>
    </div>
</section>

<section class="panel">
    <div class="card-body">
        <div id="rendemen_report"></div>
    </div>
</section>

<section class="panel">
    <div class="card-body">
        <div id="rendemen_table_report"></div>
    </div>
</section>

<section class="panel">
    <div class="card-body">
        <div id="produksi_karkas"></div>
    </div>
</section>

<section class="panel">
    <div class="card-body">
        <div id="produksi_evis"></div>
    </div>
</section>

<section class="panel">
    <div class="card-body">
        <div id="info_do"></div>
    </div>
</section>
@stop