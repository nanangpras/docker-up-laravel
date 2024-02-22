@extends('admin.layout.template')

@section('title', 'Tracing ABF')

@section('content')
<div class="row mb-4">
    <div class="col"></div>
    <div class="col text-center">
        <b>Tracing ABF</b>
    </div>
    <div class="col"></div>
</div>

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
    <div id="loading-tracingabf" class="text-center mb-2 mt-2">
        <img src="{{asset('loading.gif')}}" style="width: 20px"> Loading ...
    </div>
</section>

<div id="data_view"></div>
@endsection

@section('footer')
<script>
    var tracingAbfTimeout = null;  

    $("#mulai, #akhir").change(function(){
        if (tracingAbfTimeout != null) {
            clearTimeout(tracingAbfTimeout);
        }

        tracingAbfTimeout = setTimeout(function() {
            tracingAbfTimeout = null;  
            $("#loading-tracingabf").show() ;
            var mulai   =   $("#mulai").val() ;
            var akhir   =   $("#akhir").val() ;

            $("#data_view").load("{{ route('syncabf.index', ['key' => 'view']) }}&mulai=" + mulai + "&akhir=" + akhir, function() {
                $("#loading-tracingabf").hide() ;
            });
        }, 1000);  
    });

    $("#data_view").load("{{ route('syncabf.index', ['key' => 'view']) }}", function() {
        $("#loading-tracingabf").hide() ;
    });
</script>
@endsection