@extends('admin.layout.template')

@section('title', 'Riwayat Musnahkan')

@section('footer')
<script>
    $("#data_view").load("{{ route('musnahkan.riwayat', ['key' => 'view']) }}");
$("#tanggal_awal").on('change', function() {
    var awal    =   $("#tanggal_awal").val() ;
    var akhir   =   $("#tanggal_akhir").val() ;
    $("#data_view").load("{{ route('musnahkan.riwayat', ['key' => 'view']) }}&awal=" + awal + "&akhir=" + akhir);
})

$("#tanggal_akhir").on('change', function() {
    var awal    =   $("#tanggal_awal").val() ;
    var akhir   =   $("#tanggal_akhir").val() ;
    $("#data_view").load("{{ route('musnahkan.riwayat', ['key' => 'view']) }}&awal=" + awal + "&akhir=" + akhir);
})
</script>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col"><a href="{{ route('musnahkan.index') }}"><i class="fa fa-arrow-left"></i> Kembali</a></div>
    <div class="col text-center font-weight-bold">Riwayat Musnahkan</div>
    <div class="col"></div>
</div>

<section class="panel">
    <div class="card-body">
        Tanggal
        <div class="row">
            <div class="col pr-1">
                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                    @endif id="tanggal_awal" value="{{ date("Y-m-d") }}" class="form-control">
            </div>
            <div class="col pl-1">
                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                    @endif id="tanggal_akhir" value="{{ date("Y-m-d") }}" class="form-control">
            </div>
        </div>
    </div>
</section>

<div id="data_view"></div>
@endsection