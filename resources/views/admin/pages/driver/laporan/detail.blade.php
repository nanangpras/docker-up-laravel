@extends('admin.layout.template')

@section('title', 'Detail Driver Summary Report')

@section('content')
<div class="row mb-4">
    <div class="col">
        <a href="{{ route('driver.laporan') }}" class="btn btn-outline btn-sm btn-back"> <i
                class="fa fa-arrow-left"></i>
            Back</a>
    </div>
    <div class="col text-center">
        <b>DETAIL DRIVER SUMMARY REPORT</b>
    </div>
    <div class="col"></div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <input type="hidden" id="id_driver" value="{{ $id }}">
            <div class="col">
                <div class="form-group">
                    Mulai
                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                        min="2023-01-01" @endif name="mulai" id="tanggal_mulai" class="form-control change-date"
                        value="{{ $mulai }}" autocomplete="off">
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    Akhir
                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                        min="2023-01-01" @endif name="akhir" id="tanggal_akhir" class="form-control change-date"
                        value="{{ $akhir }}" autocomplete="off">
                </div>
            </div>
            <div class="col-auto">
                <span class="d-none d-sm-block">&nbsp;</span>
            </div>
        </div>
        <div id="loading-detail-driver" class="text-center"><i class="fa fa-refresh fa-spin"></i> Loading....</div>
    </div>
</div>


@if($driver->countambil > 0 && $driver->countantar > 0)
<div id="datakirimdanorder"></div>


@elseif ($driver->countambil > 0)
<section class="panel">
    <div class="card-body">
        <div class="text-center mb-3">
            <b>SUMMARY PENGIRIMAN AYAM HIDUP</b>
            <div id="summarypengiriman"></div>
        </div>
    </div>
</section>
@elseif($driver->countantar > 0)
<div class="text-center mb-3">
    <b>SUMMARY PENGIRIMAN ORDER</b>
</div>
<div id="summarypengirimanorder">

</div>

@endif



@if($driver->countambil > 0 && $driver->countantar > 0)
<script>
    $("#datakirimdanorder").load("{{ route('driver.cari_driver') }}?id=" + $('#id_driver').val() + "&key=loaddatakirimdanorder", function() {
        $("#loading-detail-driver").hide();
    });
</script>

<script>
    let id = $('#id_driver').val()
    $('#tanggal_mulai').on('change', function() {
        $("#loading-detail-driver").show();
        let tanggal_mulai = $('#tanggal_mulai').val()
        $("#datakirimdanorder").load("{{ route('driver.cari_driver') }}?id=" + id + "&tanggal_mulai=" + tanggal_mulai + "&key=loaddatakirimdanorder", function() {
            $("#loading-detail-driver").hide();
        });
    })

    $('#tanggal_akhir').change(function(){
        $("#loading-detail-driver").show();
        let tanggal_akhir = $('#tanggal_akhir').val()
        $("#datakirimdanorder").load("{{ route('driver.cari_driver') }}?id=" + id + "&tanggal_akhir=" + tanggal_akhir + "&key=loaddatakirimdanorder", function() {
            $("#loading-detail-driver").hide();
        });
    })
</script>


@elseif($driver->countambil > 0)
<script>
    $("#summarypengiriman").load("{{ route('driver.cari_driver') }}?id=" + $('#id_driver').val() + "&key=loadorder", function() {
        $("#loading-detail-driver").hide();
    });
</script>

<script>
    let id = $('#id_driver').val()
    $('#tanggal_mulai').on('change', function() {
        $("#loading-detail-driver").show();
        let tanggal_mulai = $('#tanggal_mulai').val()
        $("#summarypengiriman").load("{{ route('driver.cari_driver') }}?id=" + id + "&tanggal_mulai=" + tanggal_mulai + "&key=loadorder", function() {
            $("#loading-detail-driver").hide();
        });
    })

    $('#tanggal_akhir').change(function(){
        $("#loading-detail-driver").show();
        let tanggal_akhir = $('#tanggal_akhir').val()
        $("#summarypengiriman").load("{{ route('driver.cari_driver') }}?id=" + id + "&tanggal_akhir=" + tanggal_akhir + "&key=loadorder", function() {
            $("#loading-detail-driver").hide();
        });
    })
</script>


@elseif($driver->countantar > 0)
<script>
    $("#summarypengirimanorder").load("{{ route('driver.cari_driver') }}?id=" + $('#id_driver').val() + "&key=loadkirim", function() {
        $("#loading-detail-driver").hide();
    });
</script>

<script>
    let id = $('#id_driver').val()
    $('#tanggal_mulai').on('change', function() {
        $("#loading-detail-driver").show();
        let tanggal_mulai = $('#tanggal_mulai').val()
        $("#summarypengirimanorder").load("{{ route('driver.cari_driver') }}?id=" + id + "&tanggal_mulai=" + tanggal_mulai + "&key=loadkirim", function() {
            $("#loading-detail-driver").hide();
        });
    })

    $('#tanggal_akhir').change(function(){
        $("#loading-detail-driver").show();
        let tanggal_akhir = $('#tanggal_akhir').val()
        $("#summarypengirimanorder").load("{{ route('driver.cari_driver') }}?id=" + id + "&tanggal_akhir=" + tanggal_akhir + "&key=loadkirim", function() {
            $("#loading-detail-driver").hide();
        });
    })
</script>
@endif
@stop