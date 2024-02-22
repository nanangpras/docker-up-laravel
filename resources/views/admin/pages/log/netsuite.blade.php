@extends('admin.layout.template')

@section('title', 'Data Logs')

@section('content')
<div class="row mb-4">
    <div class="col">
        <a href="{{ route('dashboard') }}" class="btn btn-outline btn-sm btn-back"> <i class="fa fa-arrow-left"></i>
            Back</a>
    </div>
    <div class="col text-center">
        <b>Data Netsuite</b>
    </div>
    <div class="col"></div>
</div>
<section class="panel">
    <div class="card-body">
        <form action="{{ route('sync.index') }}" method="GET">
            <div class="row mt-2">
                <div class="col col-6">
                    <div class="form-group">
                        <div class="form-group">
                            Pencarian Tanggal
                            <div class="row">
                                <div class="col pr-1 pr-sm-3 pl-sm-3 mb-sm-2">
                                    <input type="hidden" id="page" name="page" value="{{ $page }}" class="hidden">
                                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) min="2023-01-01"
                                        @endif id="tanggal_awal" name="tanggal_awal" value="{{ $tanggal_awal }}"
                                        class="form-control" required>
                                </div>
                                <div class="col pl-1 pl-sm-3 mb-sm-2">
                                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) min="2023-01-01"
                                        @endif id="tanggal_akhir" name="tanggal_akhir" value="{{ $tanggal_akhir }}"
                                        class="form-control" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col col-6">
                    <div class="form-group">
                        <b>Status</b>
                        <select class="form-control" name="status" id="status">
                            <option value="" @if($status=="" ) selected @endif>Semua</option>
                            <option value="2" @if($status=="2" ) selected @endif>Pending</option>
                            <option value="0" @if($status=="0" ) selected @endif>Gagal</option>
                            <option value="1" @if($status=="1" ) selected @endif>Sukses</option>
                            <option value="3" @if($status=="3" ) selected @endif>Batal</option>
                            <option value="4" @if($status=="4" ) selected @endif>Antrian</option>
                            <option value="5" @if($status=="5" ) selected @endif>Approval</option>
                            <option value="6" @if($status=="6" ) selected @endif>Hold</option>
                        </select>
                    </div>
                </div>
                <div class="col col-6">
                    <div class="form-group">
                        <b>Jenis</b>
                        <select class="form-control" name="type" id="type">
                            <option value="" @if($type=="" ) selected @endif>Semua</option>
                            <option value="itemfulfill" @if($type=="itemfulfill" ) selected @endif>Itemfulfill</option>
                            <option value="itemreceipt" @if($type=="itemreceipt" ) selected @endif>Item receipt</option>
                            <option value="return" @if($type=="return" ) selected @endif>Return Authorization</option>
                            <option value="transfer_inventory" @if($type=="transfer_inventory" ) selected @endif>
                                Transfer Inventory</option>
                            <option value="wo" @if($type=="wo" ) selected @endif>WO & WOB</option>
                            <option value="wo1" @if($type=="wo1" ) selected @endif>WO 1</option>
                            <option value="wo2" @if($type=="wo2" ) selected @endif>WO 2</option>
                            <option value="wo3" @if($type=="wo3" ) selected @endif>WO 3</option>
                            <option value="wo4" @if($type=="wo4" ) selected @endif>WO 4</option>
                            <option value="wo6" @if($type=="wo6" ) selected @endif>WO 6</option>
                            <option value="wo7" @if($type=="wo7" ) selected @endif>WO 7</option>
                        </select>
                    </div>
                </div>
                <div class="col col-6">
                    <div class="form-group">
                        <b>Pencarian Global</b>
                        <input type="text" id="search" class="form-control" value="{{$search ?? ''}}"
                            placeholder="Id, Doc number, Berat, Item, Nama, etc">
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>


<section class="panel">
    <div id="ns-loading" class="text-center mb-2 mt-2" style="position: absolute; left: 0; right: 0; z-index:10000">
        <img src="{{asset('loading.gif')}}" style="width: 20px"> Loading ...
    </div>
    <div id="netsuite_data"></div>
    <div class="text-center mb-1">
        <a href="{{route('syncProcessApproval')}}"
            onclick="return confirm('Proses semua integrasi dengan status approval?')">Proses semua</a> ||
        <a href="javascript:void(0)" data-toggle="collapse" data-target="#demo">Proses Sebagian</a>

        <div id="demo" class="collapse">
            <div class="card-body">
                <h6>Proses berdasar tanggal atau range ID</h6>
                <form action="{{url('admin/sync-process')}}" method="GET">
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <input name="tanggal" type="date" class="form-control" value="{{date('Y-m-d')}}">
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <input name="dari" type="number" class="form-control" value="0">
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <input name="sampai" type="number" class="form-control" value="0">
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <button type="submit" class="btn btn-blue form-control">Proses</button>
                            </div>
                        </div>
                    </div>
                </form>
                <hr>
                <h6>Proses berdasar ID</h6>
                <form action="{{url('admin/sync-process-id')}}" method="GET">
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <input name="id" type="number" class="form-control" value="0">
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <button type="submit" class="btn btn-blue form-control">Kirim</button>
                            </div>
                        </div>
                    </div>
                </form>
                <hr>
                <h6>Proses Custom Filter</h6>
                <form action="{{ url('admin/sync-process-custom') }}" method="GET">
                    <div class="row mt-2">
                        <div class="col col-6">
                            <div class="form-group">
                                <div class="form-group">
                                    Pencarian Tanggal
                                    <div class="row">
                                        <div class="col pr-1 pr-sm-3 pl-sm-3 mb-sm-2">
                                            <input type="hidden" id="page" name="page" value="{{ $page }}"
                                                class="hidden">
                                            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' )
                                                min="2023-01-01" @endif id="tanggal_awal" name="tanggal_awal"
                                                value="{{ $tanggal_awal }}" class="form-control" required>
                                        </div>
                                        <div class="col pl-1 pl-sm-3 mb-sm-2">
                                            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' )
                                                min="2023-01-01" @endif id="tanggal_akhir" name="tanggal_akhir"
                                                value="{{ $tanggal_akhir }}" class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col col-6">
                            <div class="form-group">
                                <b>Status</b>
                                <select class="form-control" name="status" id="status">
                                    <option value="" @if($status=="" ) selected @endif>Semua</option>
                                    <option value="2" @if($status=="2" ) selected @endif>Pending</option>
                                    <option value="0" @if($status=="0" ) selected @endif>Gagal</option>
                                    <option value="1" @if($status=="1" ) selected @endif>Sukses</option>
                                    <option value="3" @if($status=="3" ) selected @endif>Batal</option>
                                    <option value="4" @if($status=="4" ) selected @endif>Antrian</option>
                                    <option value="5" @if($status=="5" ) selected @endif>Approval</option>
                                    <option value="6" @if($status=="6" ) selected @endif>Hold</option>
                                </select>
                            </div>
                        </div>
                        <div class="col col-6">
                            <div class="form-group">
                                <b>Jenis</b>
                                <select class="form-control" name="type" id="type">
                                    <option value="" @if($type=="" ) selected @endif>Semua</option>
                                    <option value="itemfulfill" @if($type=="itemfulfill" ) selected @endif>Itemfulfill
                                    </option>
                                    <option value="itemreceipt" @if($type=="itemreceipt" ) selected @endif>Item receipt
                                    </option>
                                    <option value="return" @if($type=="return" ) selected @endif>Return Authorization
                                    </option>
                                    <option value="transfer_inventory" @if($type=="transfer_inventory" ) selected
                                        @endif>Transfer Inventory</option>
                                    <option value="wo" @if($type=="wo" ) selected @endif>WO & WOB</option>
                                    <option value="wo1" @if($type=="wo1" ) selected @endif>WO 1</option>
                                    <option value="wo2" @if($type=="wo2" ) selected @endif>WO 2</option>
                                    <option value="wo3" @if($type=="wo3" ) selected @endif>WO 3</option>
                                    <option value="wo4" @if($type=="wo4" ) selected @endif>WO 4</option>
                                    <option value="wo6" @if($type=="wo6" ) selected @endif>WO 6</option>
                                    <option value="wo7" @if($type=="wo7" ) selected @endif>WO 7</option>
                                </select>
                            </div>
                        </div>
                        <div class="col col-6">
                            <div class="form-group">
                                <b>Pencarian Global</b>
                                <input type="text" id="search" class="form-control" value="{{$search ?? ''}}"
                                    placeholder="Id, Doc number, Berat, Item, Nama, etc">
                            </div>
                        </div>
                        <div class="col col-12">
                            <div class="form-group">
                                <button type="submit" class="btn btn-blue form-control">Kirim</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<script>
    var tanggal_awal =   $("#tanggal_awal").val() ;
var tanggal_akhir =   $("#tanggal_akhir").val() ;
var search  =   $("#search").val() ;
var status  =   $("#status").val() ;
var type  =   $("#type").val() ;
var page  =   $("#page").val() ;

ns_reload();

$('#load-refresh').on('click', function(){
    ns_reload();
})

$('#tanggal_awal').on('change', function(){
    $("#page").val("");
    ns_reload();
});

$('#tanggal_akhir').on('change', function(){
    $("#page").val("");
    ns_reload();
});

$('#search').on('keyup', function(){
    $("#page").val("");
    ns_reload();
});

$('#status').on('change', function(){
    $("#page").val("");
    ns_reload();
});

$('#type').on('change', function(){
    $("#page").val("");
    ns_reload();
});

function ns_reload(){
    $('#ns-loading').show();
    tanggal_awal    =   $("#tanggal_awal").val() ;
    tanggal_akhir   =   $("#tanggal_akhir").val() ;
    search          =   encodeURIComponent($("#search").val()) ;
    type            =   $("#type").val() ;
    var status      =   $("#status").val() ;
    
    $("#netsuite_data").load("{{ route('sync.index', ['key' => 'show']) }}&tanggal_awal=" + tanggal_awal + "&tanggal_akhir=" + tanggal_akhir + "&search=" + search+ "&status=" + status+ "&type=" + type + "&page=" + page, function(){
        $('#ns-loading').hide();
        $('#load-refresh').on('click', function(){
            ns_reload();
        })
    }) ;

    url = "{{url('admin/sync')}}?tanggal_awal=" + tanggal_awal + "&tanggal_akhir=" + tanggal_akhir + "&search=" + search+ "&status=" + status+ "&type=" + type + "&page=" + page;
    window.history.pushState('Netsuite', 'Netsuite', url);

}
</script>

<style>
    .date-notif {
        font-size: 7pt;
        color: #999999
    }
</style>

@stop