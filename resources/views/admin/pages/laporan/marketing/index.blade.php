@extends('admin.layout.template')

@section('title', 'Laporan Marketing')

@section('footer')
<script>
    $('.select2').select2({
        theme: 'bootstrap4',
    })
</script>

<script>
    $("#loading").attr('style', 'display: block') ;
$("#data_view").load("{{ route('laporanmarketing.index', ['key' => 'view']) }}", function() {
    $("#loading").attr('style', 'display: none') ;
}) ;

$("#tanggal_awal").on('change', function() {
    var tanggal_awal    =   $("#tanggal_awal").val() ;
    var tanggal_akhir   =   $("#tanggal_akhir").val() ;
    var nama_marketing  =   encodeURIComponent($("#nama_marketing").val()) ;
    var market          =   encodeURIComponent($("#market").val()) ;
    var cari            =   encodeURIComponent($("#cari").val()) ;

    $("#data_view").attr('style', 'display: none') ;
    $("#loading").attr('style', 'display: block') ;
    $("#data_view").load("{{ route('laporanmarketing.index', ['key' => 'view']) }}&tanggal_awal=" + tanggal_awal + "&tanggal_akhir=" + tanggal_akhir + "&nama_marketing=" + nama_marketing + "&market=" + market + "&cari=" + cari, function() {
        $("#data_view").attr('style', 'display: block') ;
        $("#loading").attr('style', 'display: none') ;
    }) ;
});

$("#tanggal_akhir").on('change', function() {
    var tanggal_awal    =   $("#tanggal_awal").val() ;
    var tanggal_akhir   =   $("#tanggal_akhir").val() ;
    var nama_marketing  =   encodeURIComponent($("#nama_marketing").val()) ;
    var market          =   encodeURIComponent($("#market").val()) ;
    var cari            =   encodeURIComponent($("#cari").val()) ;

    $("#data_view").attr('style', 'display: none') ;
    $("#loading").attr('style', 'display: block') ;
    $("#data_view").load("{{ route('laporanmarketing.index', ['key' => 'view']) }}&tanggal_awal=" + tanggal_awal + "&tanggal_akhir=" + tanggal_akhir + "&nama_marketing=" + nama_marketing + "&market=" + market + "&cari=" + cari, function() {
        $("#data_view").attr('style', 'display: block') ;
        $("#loading").attr('style', 'display: none') ;
    }) ;
});

$("#nama_marketing").on('change', function() {
    var tanggal_awal    =   $("#tanggal_awal").val() ;
    var tanggal_akhir   =   $("#tanggal_akhir").val() ;
    var nama_marketing  =   encodeURIComponent($("#nama_marketing").val()) ;
    var market          =   encodeURIComponent($("#market").val()) ;
    var cari            =   encodeURIComponent($("#cari").val()) ;

    $("#data_view").attr('style', 'display: none') ;
    $("#loading").attr('style', 'display: block') ;
    $("#data_view").load("{{ route('laporanmarketing.index', ['key' => 'view']) }}&tanggal_awal=" + tanggal_awal + "&tanggal_akhir=" + tanggal_akhir + "&nama_marketing=" + nama_marketing + "&market=" + market + "&cari=" + cari, function() {
        $("#data_view").attr('style', 'display: block') ;
        $("#loading").attr('style', 'display: none') ;
    }) ;
});

$("#market").on('change', function() {
    var tanggal_awal    =   $("#tanggal_awal").val() ;
    var tanggal_akhir   =   $("#tanggal_akhir").val() ;
    var nama_marketing  =   encodeURIComponent($("#nama_marketing").val()) ;
    var market          =   encodeURIComponent($("#market").val()) ;
    var cari            =   encodeURIComponent($("#cari").val()) ;

    $("#data_view").attr('style', 'display: none') ;
    $("#loading").attr('style', 'display: block') ;
    $("#data_view").load("{{ route('laporanmarketing.index', ['key' => 'view']) }}&tanggal_awal=" + tanggal_awal + "&tanggal_akhir=" + tanggal_akhir + "&nama_marketing=" + nama_marketing + "&market=" + market + "&cari=" + cari, function() {
        $("#data_view").attr('style', 'display: block') ;
        $("#loading").attr('style', 'display: none') ;
    }) ;
});

$("#cari").on('keyup', function() {
    var tanggal_awal    =   $("#tanggal_awal").val() ;
    var tanggal_akhir   =   $("#tanggal_akhir").val() ;
    var nama_marketing  =   encodeURIComponent($("#nama_marketing").val()) ;
    var market          =   encodeURIComponent($("#market").val()) ;
    var cari            =   encodeURIComponent($("#cari").val()) ;

    $("#data_view").attr('style', 'display: none') ;
    $("#loading").attr('style', 'display: block') ;
    $("#data_view").load("{{ route('laporanmarketing.index', ['key' => 'view']) }}&tanggal_awal=" + tanggal_awal + "&tanggal_akhir=" + tanggal_akhir + "&nama_marketing=" + nama_marketing + "&market=" + market + "&cari=" + cari, function() {
        $("#data_view").attr('style', 'display: block') ;
        $("#loading").attr('style', 'display: none') ;
    }) ;
});
</script>
@endsection

@section('content')
<div class="my-4 text-center font-weight-bold">LAPORAN MARKETING</div>

<section class="panel">
    <div class="card-body">
        <div class="row">
            <div class="col-6 col-md">
                <div class="form-group">
                    <label for="tanggal_awal">Tanggal Awal</label>
                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                        min="2023-01-01" @endif id="tanggal_awal" class="form-control" autocomplete="off"
                        value="{{ date("Y-m-01") }}">
                </div>
            </div>
            <div class="col-6 col-md">
                <div class="form-group">
                    <label for="tanggal_akhir">Tanggal Akhir</label>
                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                        min="2023-01-01" @endif id="tanggal_akhir" class="form-control" autocomplete="off"
                        value="{{ date("Y-m-d") }}">
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label for="nama_marketing">Nama Marketing</label>
                    <select data-width="100%" id="nama_marketing" class="form-control select2">
                        <option value="">Semua Marketing</option>
                        @foreach ($marketing as $row)
                        @if ($row->souser)
                        <option value="{{ $row->user_id }}">{{ $row->souser->name ?? '' }}</option>
                        @endif
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label for="market">Market</label>
                    <select data-width="100%" id="market" class="form-control select2">
                        <option value="">Semua Market</option>
                        @foreach ($market as $row)
                        @if ($row->kategori)
                        <option value="{{ $row->kategori }}">{{ $row->kategori }}</option>
                        @endif
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label for="cari">Pencarian Kata</label>
                    <input type="text" placeholder="Cari..." id="cari" class="form-control">
                </div>
            </div>
        </div>
    </div>
</section>

<section class="panel">
    <div class="card-body p-2">
        <h5 id="loading" style="display: none"><i class="fa fa-refresh fa-spin"></i> Loading....</h5>
        <div id="data_view"></div>
        {{-- <table class="table table-sm table-striped table-bordered">
            <thead>
                <tr>
                    <th>DATE</th>
                    <th class="text-center" colspan="3">W1</th>
                    <th class="text-center" colspan="3">W2</th>
                    <th class="text-center" colspan="3">W3</th>
                    <th class="text-center" colspan="3">W4</th>
                    <th class="text-center" colspan="3">MONTHLY</th>
                </tr>
                <tr>
                    <th>ITEM CATEGORY</th>
                    <th class="text-center">SO</th>
                    <th class="text-center">DO</th>
                    <th class="text-center">CASEFILL</th>
                    <th class="text-center">SO</th>
                    <th class="text-center">DO</th>
                    <th class="text-center">CASEFILL</th>
                    <th class="text-center">SO</th>
                    <th class="text-center">DO</th>
                    <th class="text-center">CASEFILL</th>
                    <th class="text-center">SO</th>
                    <th class="text-center">DO</th>
                    <th class="text-center">CASEFILL</th>
                    <th class="text-center">SO</th>
                    <th class="text-center">DO</th>
                    <th class="text-center">CASEFILL</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>Parting</th>
                </tr>
                </tr>
                <th>Boneless</th>
                </tr>
                <tr>
                    <th>Sampingan</th>
                </tr>
                <tr>
                    <th>Boneless Sampingan</th>
                </tr>
            </tbody>
        </table> --}}
    </div>
</section>
@endsection