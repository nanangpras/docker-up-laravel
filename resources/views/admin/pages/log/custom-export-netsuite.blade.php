@extends('admin.layout.template')

@section('title', 'Data Logs')

@section('content')
<div class="row mb-4">
    <div class="col">
        <a href="{{ route('sync.index') }}" class="btn btn-outline btn-sm btn-back"> <i class="fa fa-arrow-left"></i>
            Back</a>
    </div>
    <div class="col text-center">
        <b>Data Netsuite</b>
    </div>
    <div class="col"></div>
</div>
<section class="panel">
    <div class="card-body">
        <form action="{{ route('sync.download-custom') }}" method="POST">
            @csrf
            <div class="row mt-2">
                <div class="col">
                    <div class="form-group">
                        <div class="form-group">
                            Awal
                            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                min="2023-01-01" @endif onchange="view()" id="tanggal_awal" name="awal"
                                value="{{date('Y-m-d')}}" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <div class="form-group">
                            Akhir
                            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                min="2023-01-01" @endif onchange="view()" id="tanggal_akhir" name="akhir"
                                value="{{date('Y-m-d')}}" class="form-control" required>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="form-group">
                        <b>Status</b>
                        <select class="form-control" name="status" onchange="view()" id="status">
                            <option value="" selected>Semua</option>
                            <option value="0">Gagal</option>
                            <option value="1">Sukses</option>
                            <option value="2">Pending</option>
                            <option value="3">Batal</option>
                            <option value="4">Antrian</option>
                            <option value="5">Approval</option>
                        </select>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <b>Jenis</b>
                        <select class="form-control" name="type" onchange="view()" id="type">
                            <option value="" selected>Semua</option>
                            <option value="itemfulfill">Itemfulfill</option>
                            <option value="itemreceipt">Item receipt</option>
                            <option value="return">Return Authorization</option>
                            <option value="transfer_inventory">Transfer Inventory</option>
                            <option value="transfer_inventory_do">Transaksi Gudang Expedisi</option>
                            <option value="gudang_retur">Transaksi Gudang Retur</option>
                            <option value="gudang_lb">Gudang Live Bird</option>
                            <option value="wo">WO & WOB</option>
                            <option value="wo1">WO 1</option>
                            <option value="wo2">WO 2</option>
                            <option value="wo3">WO 3</option>
                            <option value="wo4">WO 4</option>
                            <option value="wo6">WO 6</option>
                            <option value="wo7">WO 7</option>
                        </select>
                    </div>
                </div>

                <div class="col">
                    <div class="form-group">
                        <b>Action</b><br>
                        <button type="submit" class="btn btn-blue">Download</button>
                    </div>
                </div>

            </div>
        </form>
    </div>
</section>

<section class="panel">
    <div class="card-body">
        <h5 class="text-center loading-exportcsv"><i class="fa fa-refresh fa-spin"></i> Loading....</h5>
        <div id="data_ns"></div>
    </div>
</section>

<script>
    var exportCsvTimeout = null;  

    function view() {

        $(".loading-exportcsv").attr('style', 'display: block');
        var tanggal_awal    =   $("#tanggal_awal").val() ;
        var tanggal_akhir   =   $("#tanggal_akhir").val() ;
        var status          =   $("#status").val() ;
        var type            =   $("#type").val() ;

        if (exportCsvTimeout != null) {
            clearTimeout(exportCsvTimeout);
        }
        exportCsvTimeout = setTimeout(function() {
            exportCsvTimeout = null;  
            //ajax code
            $("#data_ns").load("{{ route('sync.download-custom.view', ['key' => 'view']) }}&awal=" + tanggal_awal + "&akhir=" + tanggal_akhir + "&status=" + status + "&type=" + type, function() {
                $(".loading-exportcsv").attr('style', 'display: none');
            }) ;
        }, 1000); 
    }


    $("#data_ns").load("{{ route('sync.download-custom.view', ['key' => 'view']) }}&awal={{ date('Y-m-d') }}&akhir={{ date('Y-m-d') }}", function() {
        $(".loading-exportcsv").attr('style', 'display: none');
    }) ;

</script>
@stop