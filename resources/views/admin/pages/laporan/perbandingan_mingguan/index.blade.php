@extends('admin.layout.template')

@section('title', 'Dashboard Perbandingan Mingguan')

@section('footer')
<script>
    $("#data_view").load("{{ route('dashboard.mingguan', ['key' => 'view']) }}");

    $("#tanggal_cari").on('change', function() {
        var tanggal =   $("#tanggal_cari").val() ;
        $("#data_view").load("{{ route('dashboard.mingguan', ['key' => 'view']) }}&tanggal=" + tanggal);
    })
</script>
@endsection

@section('header')
<style>
    .thin-bar {
        height: 5px;
    }

    .outer-table-scroll {
        max-height: 300px;
        overflow-y: auto;
        width: 100%;
    }
</style>
@endsection

@section('content')
<div class="mb-3 font-weight-bold text-center">DASHBOARD PERBANDINGAN MINGGUAN</div>

<section class="panel">
    <div class="card-body ">
        Tanggal
        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01" @endif
            id="tanggal_cari" value="{{ date('Y-m-d') }}" class="form-control">
    </div>
</section>

<div id="data_view"></div>
@endsection