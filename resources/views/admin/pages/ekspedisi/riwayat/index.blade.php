@extends('admin.layout.template')

@section('title', 'Riwayat Ekspedisi')

@section('footer')
<script>
    $("#data_riwayat").load("{{ route('ekspedisi.riwayat', ['key' => 'data_riwayat']) }}") ;

$("#tanggal_awal").on('change', function() {
    var awal    =   $("#tanggal_awal").val() ;
    var akhir   =   $("#tanggal_akhir").val() ;
    $("#data_riwayat").load("{{ route('ekspedisi.riwayat', ['key' => 'data_riwayat']) }}&tanggal_awal=" + awal + "&tanggal_akhir=" + akhir) ;
})
$("#tanggal_akhir").on('change', function() {
    var awal    =   $("#tanggal_awal").val() ;
    var akhir   =   $("#tanggal_akhir").val() ;
    $("#data_riwayat").load("{{ route('ekspedisi.riwayat', ['key' => 'data_riwayat']) }}&tanggal_awal=" + awal + "&tanggal_akhir=" + akhir) ;
})
</script>
@endsection

@section('content')
<div class="row mb-3">
    <div class="col"><a href="{{ route('ekspedisi.index') }}"><i class="fa fa-arrow-left"></i> Kembali</a></div>
    <div class="col text-center font-weight-bold">Riwayat Ekspedisi</div>
    <div class="col"></div>
</div>

<section class="panel">
    <div class="card-body">
        Pencarian Tanggal
        <div class="row">
            <div class="col pr-1">
                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                    @endif id="tanggal_awal" value="{{ date("Y-m-d") }}" class="form-control">
            </div>
            <div class="col pl-1">
                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                    @endif id="tanggal_akhir" value="{{ date("Y-m-d", strtotime('+1 day')) }}" class="form-control">
            </div>
        </div>
    </div>
</section>

<div id="data_riwayat"></div>
@endsection