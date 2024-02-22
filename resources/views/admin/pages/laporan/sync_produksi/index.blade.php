@extends('admin.layout.template')

@section('title', 'Tracing Produksi')

@section('content')
<div class="row mb-4">
    <div class="col"></div>
    <div class="col text-center">
        <b>Tracing Produksi</b>
    </div>
    <div class="col"></div>
</div>

@if (!$request->paket)
<section class="panel">
    <div class="card-body">
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                        min="2023-01-01" @endif id="mulai" value="{{ date("Y-m-d") }}" class="form-control">
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                        min="2023-01-01" @endif id="akhir" value="{{ date("Y-m-d") }}" class="form-control">
                </div>
            </div>
        </div>
    </div>
    <div id="ns-loading" class="text-center mb-2 mt-2">
        <img src="{{asset('loading.gif')}}" style="width: 20px"> Loading ...
    </div>
</section>
@endif


<div id="data_view"></div>
@endsection

@section('footer')
<script>
    var syncProdTimeout = null;  

    $("#mulai, #akhir").change(function(){

        if (syncProdTimeout != null) {
            clearTimeout(syncProdTimeout);
        }
        syncProdTimeout = setTimeout(function() {
            syncProdTimeout = null;  
            //ajax code
            $("#ns-loading").show();
            var mulai   =   $("#mulai").val() ;
            var akhir   =   $("#akhir").val() ;

            $("#data_view").load("{{ route('syncprod.index', ['key' => 'view']) }}&paket={{ $request->paket }}&mulai=" + mulai + "&akhir=" + akhir, function(){
                $("#ns-loading").hide() ;
            });
        }, 1000);  
    });

    $("#data_view").load("{{ route('syncprod.index', ['key' => 'view']) }}&paket={{ $request->paket }}", function(){
        $("#ns-loading").hide() ;
    });
</script>
@endsection