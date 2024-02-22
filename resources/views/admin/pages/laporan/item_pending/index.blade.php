@extends('admin.layout.template')

@section('title', 'Order Item Belum Terpenuhi')

@section('footer')
<script>
    $("#data_view").load("{{ route('dashboard.itempending', ['key' => 'view']) }}&item={{ $request->item }}&tanggal_awal={{ $request->tanggal_awal }}&tanggal_akhir={{ $request->tanggal_akhir }}")

    $("#cari_data").on('keyup', function() {
        var cari    =   $(this).val() ;
        $("#data_view").load("{{ route('dashboard.itempending', ['key' => 'view']) }}&item={{ $request->item }}&tanggal_awal={{ $request->tanggal_awal }}&tanggal_akhir={{ $request->tanggal_akhir }}&page=1&cari=" + cari);
    });
</script>
@endsection

@section('content')
<div class="row mb-3">
    <div class="col">
        <a href="{{ route('dashboard') }}"><i class="fa fa-arrow-left"></i> Back</a>
    </div>
    <div class="col font-weight-bold text-uppercase text-center">
        Order Item Belum Terpenuhi
    </div>
    <div class="col"></div>
</div>

<section class="panel">
    <div class="card-body">
        <input type="text" placeholder="Pencarian..." id="cari_data" class="form-control" autocomplete="off">
    </div>
</section>

<section class="panel">
    <div class="card-body">
        <div id="data_view"></div>
    </div>
</section>
@endsection
