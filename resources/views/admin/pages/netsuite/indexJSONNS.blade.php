@extends('admin.layout.template')

@section('title', 'Data Item')

@section('content')
    <div class="row mb-4">
        <div class="col">
            <a href="{{ route('dashboard') }}" class="btn btn-outline btn-sm btn-back"> <i class="fa fa-arrow-left"></i>
                Back</a>

        </div>
        <div class="col text-center">
            <b>Data Deleted JSON NS</b>
        </div>
        <div class="col"></div>
    </div>

    <section class="panel">
        <div class="card-body">
            <div class="row">
                    <div class="col">
                        <label for="tanggal_awal">Tanggal Awal</label>
                        <input type="date" value="{{ $tanggalAwal }}" id="tanggal_awal" class="form-control">
                    </div>
                    <div class="col">
                        <label for="tanggal_akhir">Tanggal Akhir</label>
                        <input type="date" value="{{ $tanggalAkhir }}" id="tanggal_akhir" class="form-control">
                    </div>
                <div class="col-md-4 mb-3">
                    <label for="cari">Cari</label>
                    <input type="text" placeholder="Cari..." autocomplete="off" id="cari" class="form-control">
                </div>
            </div>
            <h5 id="loading" style="display: none" class="text-center"><i class="fa fa-refresh fa-spin"></i> Loading....
            </h5>
            <div id="dataJSON"></div>
        </div>
    </section>

@stop

@section('footer')
<script>
    $("#loading").attr("style", 'display: block');

    loadDataTrashedNS()
    $("#tanggal_awal").on('change', function() {
        loadDataTrashedNS()
    })

    $("#tanggal_akhir").on('change', function() {
        loadDataTrashedNS()
    })

    $("#cari").on('keyup', function() {
        loadDataTrashedNS()
    })

    function loadDataTrashedNS(){

        $("#loading").attr("style", 'display: block');

        let tanggal_awal         =   $("#tanggal_awal").val() ;
        let tanggal_akhir        =   $("#tanggal_akhir").val() ;
        let pencarian            =   encodeURIComponent($("#cari").val() ?? '') ;
        
        $("#dataJSON").load("{{ route('netsuite.index', ['key' => 'loadPageNS']) }}&tanggal_awal=" + tanggal_awal + "&tanggal_akhir=" + tanggal_akhir + "&pencarian=" + pencarian, function() {
            $("#loading").attr("style", 'display: none');
        });

    }

</script>

@stop
