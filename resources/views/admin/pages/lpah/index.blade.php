@extends('admin.layout.template')

@section('title', 'Data Penerimaan Ayam Hidup')

@section('header')
<link rel="stylesheet" type="text/css" href="{{asset('')}}plugin/DataTables/datatables.min.css" />
@stop

@section('content')
<div class="text-center my-4 text-uppercase"><b>Data Penerimaan Ayam Hidup</b></div>

<section class="panel">
    <div class="card-body">
        <div class="row">
            <div class="col-lg-4 col-6">
                Pencarian Tanggal Awal
                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                    @endif class="form-control" id="tanggalawal" name="tanggalawal" value="{{ date('Y-m-d') }}">
            </div>
            <div class="col-lg-4 col-6">
                Pencarian Tanggal Akhir
                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                    @endif class="form-control" id="tanggalakhir" name="tanggalakhir" value="{{ date('Y-m-d') }}">
            </div>
            <div class="col-lg-4 text-right">
                <a href="{{ route('laporan.lpah') }}" class="btn btn-primary mt-3 float-right">Laporan</a>
            </div>
        </div>
        <div id="spinnerpenerimaanlpah" class="text-center mb-2">
            <img src="{{ asset('loading.gif') }}" style="width: 30px">
        </div>
        <div id="showpenerimaanLpah"></div>

        <hr>

        <div id="spinnerdatapotong" class="text-center mb-2">
            <img src="{{ asset('loading.gif') }}" style="width: 30px">
        </div>
        <div id="showhasilpotong"></div>
        <div id="showhitungtotal"></div>
    </div>
</section>
@endsection

@section('footer')
<script type="text/javascript" src="{{asset('')}}plugin/DataTables/datatables.min.js"></script>
<script>
    $(document).ready(function() {
            $('#tanggalawal,#tanggalakhir').change(function() {
                var tanggalawal         = $('#tanggalawal').val();
                var tanggalakhir        = $('#tanggalakhir').val();

                setTimeout(() =>{
                    loadDataLPAH()
                },500)
            });

            loadDataLPAH()

            function loadDataLPAH(){
                var tanggalawal         = $('#tanggalawal').val();
                var tanggalakhir        = $('#tanggalakhir').val();

                $.ajax({
                    url     : "{{ route('lpah.index') }}",
                    method  : "GET",
                    data    : {
                        tanggalawal     : tanggalawal,
                        tanggalakhir    : tanggalakhir,
                        key             : "penerimaanlpah"
                    },
                    beforeSend : function() {
                        $("#spinnerpenerimaanlpah").show();
                    },
                    success : function(data){
                        $("#showpenerimaanLpah").html(data);
                        $("#spinnerpenerimaanlpah").hide();
                    }
                })
                $.ajax({
                    url     : "{{ route('lpah.index') }}",
                    method  : "GET",
                    data    : {
                        tanggalawal     : tanggalawal,
                        tanggalakhir    : tanggalakhir,
                        key             : "hitungtotal"
                    },
                    beforeSend : function() {

                    },
                    success : function(data){
                        $("#showhitungtotal").html(data);
                    }
                })

                $.ajax({
                    url     : "{{ route('kepalaproduksi.hasilpotong') }}",
                    method  : "GET",
                    data    : {
                        tanggal     : tanggalawal,
                        tanggalend  : tanggalakhir
                    },
                    beforeSend : function() {
                        $("#spinnerdatapotong").show();
                    },
                    success : function(data){
                        $("#showhasilpotong").html(data);
                        $("#spinnerdatapotong").hide();
                    }
                })
            }

            $('#lpahTable').DataTable({
                "bInfo": false,
                responsive: true,
                scrollY: 500,
                scrollX: true,
                scrollCollapse: true,
                paging: false,
            });
        });
</script>
@stop