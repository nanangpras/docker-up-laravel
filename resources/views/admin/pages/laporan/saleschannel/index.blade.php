@extends('admin.layout.template')

@section('title', 'Summary Sales Channel')

@section('footer')
<script>
    var channel =   encodeURIComponent("{{ $request->channel }}") ;
    $("#data_view").load("{{ route('dashboard.saleschannel', ['key' => 'view']) }}&channel=" + channel + "&tanggal_awal={{ $request->tanggal_awal }}&tanggal_akhir={{ $request->tanggal_akhir }}")

    $("#cari_data").on('keyup', function() {
        var cari    =   $(this).val() ;
        var status  =   $("#status_data").val() ;
        $("#data_view").load("{{ route('dashboard.saleschannel', ['key' => 'view']) }}&channel=" + channel + "&tanggal_awal={{ $request->tanggal_awal }}&tanggal_akhir={{ $request->tanggal_akhir }}&page=1&cari=" + cari + "&status=" + status);
    });

    $("#status_data").on('change', function() {
        var cari    =   $("#cari_data").val() ;
        var status  =   $(this).val() ;
        $("#data_view").load("{{ route('dashboard.saleschannel', ['key' => 'view']) }}&channel=" + channel + "&tanggal_awal={{ $request->tanggal_awal }}&tanggal_akhir={{ $request->tanggal_akhir }}&page=1&cari=" + cari + "&status=" + status);
    });
</script>
@endsection

@section('content')
<div class="row mb-3">
    <div class="col">
        <a href="{{ route('dashboard') }}"><i class="fa fa-arrow-left"></i> Back</a>
    </div>
    <div class="col font-weight-bold text-uppercase text-center">
        Summary Sales Channel
    </div>
    <div class="col"></div>
</div>

<section class="panel">
    <div class="card-body">
        <div class="row">
            <div class="col pr-1">
                <input type="text" placeholder="Pencarian..." id="cari_data" class="form-control" autocomplete="off">
            </div>

            <div class="col pl-1">
                <select class="form-control" id="status_data">
                    <option value="all">Semua</option>
                    <option value="selesai">Selesai</option>
                    <option value="pending">Pending</option>
                </select>
            </div>
        </div>
    </div>
</section>

<div id="data_view"></div>
@endsection
