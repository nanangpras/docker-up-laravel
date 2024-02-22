@extends('admin.layout.template')

@section('title', 'Produksi x Plastik')

@section('footer')
<script>

    loadPlastic()
    function loadPlastic(){
        $('#spinerplastic').show();
        $("#data_view").load("{{ route('dashboard.produksiplastik', ['key' => 'view']) }}&regu={{ $request->regu }}&tanggal_awal={{ $request->tanggal_awal }}&tanggal_akhir={{ $request->tanggal_akhir }}",  function () { 
            $('#spinerplastic').hide();
        });
    }

    $("#cari_data").on('keyup', function() {
        var cari    =   $(this).val() ;
        setTimeout(() => {
            $('#spinerplastic').show();
            $("#data_view").load("{{ route('dashboard.produksiplastik', ['key' => 'view']) }}&regu={{ $request->regu }}&tanggal_awal={{ $request->tanggal_awal }}&tanggal_akhir={{ $request->tanggal_akhir }}&page=1&cari=" + encodeURIComponent(cari), function () { 
                $('#spinerplastic').hide();
            });
        }, 1000);
    });
</script>
@endsection

@section('content')
<div class="row mb-3">
    <div class="col">
        <a href="{{ route('dashboard') }}"><i class="fa fa-arrow-left"></i> Back</a>
    </div>
    <div class="col font-weight-bold text-uppercase text-center">
        Produksi x Plastik
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
        <div id="spinerplastic" class="text-center" style="display: none">
            <img src="{{ asset('loading.gif') }}" width="30px">
        </div>
        <div id="data_view"></div>
    </div>
</section>
@endsection
