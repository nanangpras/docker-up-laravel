@extends('admin.layout.template')

@section('title', 'Chiller SOH')

@section('content')
<div class="my-4 text-center font-weight-bold">CHILLER SOH</div>

<section class="panel">
    <div class="card-body p-2">
        @if(env('NET_SUBSIDIARY', 'CGL')=='CGL')
        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01" @endif
            value="{{ date("Y-m-d") }}" id="tanggal_soh" class="form-control" min="2023-05-27">
        @else
        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01" @endif
            value="{{ date("Y-m-d") }}" id="tanggal_soh" class="form-control" min="2023-05-05">
        @endif
    </div>
</section>

<section class="panel">
    <div class="card-body p-2">
        <div id="spinersoh" class="text-center mb-2">
            <img src="{{ asset('loading.gif') }}" style="width: 30px">
        </div>
        <div id="data_view"></div>
    </div>
</section>
@endsection

@section('footer')
<script>
    $("#data_view").load("{{ route('chiller.soh', ['key' => 'view']) }}", function() {
        $("#spinersoh").hide() ;
    }) ;

    $("#tanggal_soh").on('change', function() {
        $("#spinersoh").show() ;
        var tanggal =   $("#tanggal_soh").val() ;
        $("#data_view").load("{{ route('chiller.soh', ['key' => 'view']) }}&tanggal=" + tanggal, function() {
            $("#spinersoh").hide();
        }) ;
    })
</script>
@endsection