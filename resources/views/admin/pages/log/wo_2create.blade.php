@extends('admin.layout.template')

@section('title', 'WO-2 Create')

@section('content')
<div class="row mb-4">
    <div class="col">
        <a href="{{ route('sync.index') }}" class="btn btn-outline btn-sm btn-back"> <i class="fa fa-arrow-left"></i>
            Back</a>
    </div>
    <div class="col text-center py-1">
        <b>WO-2 Create</b>
    </div>
    <div class="col"></div>
</div>

<section class="panel">
    <div class="card-body">
        Pencarian Tanggal
        <div class="row">
            <div class="col pr-1">
                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                    @endif id="tanggal_awal" name="tanggal_awal" value="{{ $tanggal_awal }}" class="form-control"
                    required>
            </div>
            <div class="col pl-1">
                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                    @endif id="tanggal_akhir" name="tanggal_akhir" value="{{ $tanggal_akhir }}" class="form-control"
                    required>
            </div>
            <div class="col pl-1">
                <select name="filterregu" id="filterregu" class="form-control select2">
                    <option value="">Pilih Regu</option>
                    <option value="boneless">BONELESS</option>
                    <option value="parting">PARTING</option>
                    <option value="marinasi">M</option>
                    <option value="whole">WHOLE CHICKEN</option>
                    <option value="frozen">FROZEN</option>
                </select>
            </div>
        </div>
    </div>
</section>

<div id="loading-data" class="text-center mb-2">
    <img src="{{ asset('loading.gif') }}" style="width: 30px">
</div>
<div id="data_wo"></div>

<script>
    $('.select2').select2({
    theme: 'bootstrap4'
})

var tanggal_awal    =   $("#tanggal_awal").val() ;
var tanggal_akhir   =   $("#tanggal_akhir").val() ;
var filterregu      =   $("#filterregu").val() ;


ns_reload();

$('#tanggal_awal,#tanggal_akhir,#filterregu').on('change', function(){
    ns_reload();
});


function ns_reload(){
    $('#loading-data').show();
    tanggal_awal    =   $("#tanggal_awal").val() ;
    tanggal_akhir   =   $("#tanggal_akhir").val() ;
    filterregu      =   $("#filterregu").val() ;

    $("#data_wo").load("{{ route('sync.wo2', ['key' => 'data']) }}&tanggal_awal=" + tanggal_awal + "&tanggal_akhir=" + tanggal_akhir + "&filterregu=" + filterregu , function() {
        $('#loading-data').hide();
    }) ;

    url = "{{route('sync.wo2')}}?tanggal_awal=" + tanggal_awal + "&tanggal_akhir=" + tanggal_akhir;
    window.history.pushState('Netsuite', 'Netsuite', url);

}
</script>
@endsection