@extends('admin.layout.template')

@section('title', 'Laporan Penerimaan Ayam Merah')

@section('content')
    <div class="col text-primary"><a href="{{ route('purchasing.index') }}"><i class="fa fa-arrow-left"></i> Back</a></div>
    <div class="my-4 text-center"><b>Laporan Penerimaan Ayam Merah</b></div>
    <section class="panel mt-2">
        <div class="card-body">
            <form action="{{ route('laporan.lpah') }}" method="get">
                <div class="row">
                    <div class="col-md-4">
                        <label>Tanggal Awal</label>
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01" @endif class="form-control" name="tanggal_mulai" id="tanggal_mulai" value="{{ $tanggal_mulai }}" placeholder="Cari...">
                    </div>
                    <div class="col-md-4">
                        <label>Tanggal Akhir</label>
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01" @endif class="form-control" name="tanggal_selesai" id="tanggal_selesai" value="{{ $tanggal_selesai }}" placeholder="Cari...">
                    </div>
                    <div class="col-md-4">
                        <label>Jenis Ekspedisi</label>
                        <select class="select2 form-control"  name="jenis_ekspedisi" id="jenis_ekspedisi">
                            <option value="all"> Semua </option>
                            <option value="kirim"> Kirim </option>
                            <option value="tangkap"> Tangkap </option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </section>
    <div id="loadingayammerah" class="text-center" style="display: block">
        <img src="{{ asset('loading.gif') }}" width="30px">
    </div>
    <div id="laporanayammerah"></div>
@endsection

@section('header')
<style>
    .default-table td {
        min-width: 100px;
    }
</style>
@endsection

@section('footer')
<script>
    laporanayammerah();

    $('#tanggal_mulai,#tanggal_selesai,#jenis_ekspedisi').change(function() {
        setTimeout(() => {
            laporanayammerah();            
        }, 800);
    });

    function laporanayammerah(){
        var tanggal_mulai   = $("#tanggal_mulai").val();
        var tanggal_selesai = $("#tanggal_selesai").val();
        var jenis_ekspedisi = $("#jenis_ekspedisi").val();
        
        $.ajax({
            url         : "{{ route('laporan.laporanayammerah') }}",
            method      : "GET",
            data        : {
                'key'           : "showData",
                tanggal_mulai   : tanggal_mulai,
                tanggal_selesai : tanggal_selesai,
                jenis_ekspedisi : jenis_ekspedisi,
            },
            beforeSend  : function(){
                $("#loadingayammerah").show();
            },
            success: function(data){
                $("#laporanayammerah").html(data);
                $("#loadingayammerah").hide();
            }
        })
    }
</script>
@endsection