@extends('admin.layout.template')

@section('title', 'Chiller Data Stock')

@section('content')
<div class="text-center mb-4">
    <b>CHILLER DATA STOCK BAHAN BAKU</b>
</div>
<section class="panel">
    <div class="card-body p-2">
        <div class="row">
            <div class="col-6">
                <label for="mulai">Tanggal Awal</label>
                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                    @endif id="mulai" class="form-control input-lg" value="{{ $akhir }}">
            </div>
            <div class="col-6">
                <label for="mulai">Tanggal Akhir</label>
                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                    @endif id="akhir" class="form-control" value="{{ $akhir }}">
            </div>
        </div>
    </div>
</section>

<section class="panel">
    <div class="card-body p-2 mb-4">
        <div id="show_view_page"></div>
    </div>
</section>
@endsection
@section('footer')
<script>
    var mulai = $("#mulai").val();
    var akhir = $("#akhir").val();

    $("#mulai,#akhir").change(function() {
        $("#loading-chiller-stock").show();
        mulai = $("#mulai").val();
        akhir = $("#akhir").val();
        setTimeout(function(){
            loadPage(mulai, akhir);
        },800)
    });

    loadPage(mulai, akhir);

    function loadPage(mulai, akhir) {
        $('#show_view_page').load("{{ route('laporan.chillerdatastock', ['key' => 'view_page']) }}&mulai=" + mulai + "&akhir=" + akhir, function() {
            $("#loading-chiller-stock").hide();
        })
    }
</script>
@stop