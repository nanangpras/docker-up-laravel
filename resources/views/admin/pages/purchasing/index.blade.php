@extends('admin.layout.template')

@section('title', 'Purchasing')

@section('content')
<div class="my-4 text-center"><b>PURCHASING ORDER</b></div>

<section class="panel">
    <div class="card mb-4">
        <div class="card-body">

            {{-- <form action="{{ route('purchasing.index') }}" method="GET"> --}}
                <div class="row">
                    <div class="col-lg-2 mb-lg-0 mb-1 col-6">
                        <div class="form-group">
                            Tanggal Potong Awal
                            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                min="2023-01-01" @endif class="form-control change-date" name="tanggal_potong_awal"
                                id="tanggal_potong_awal" value="{{ date('Y-m-d') }}" placeholder="Cari...">
                        </div>
                    </div>
                    <div class="col-lg-2 mb-lg-0 mb-1 col-6">
                        <div class="form-group">
                            Tanggal Potong Akhir
                            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                min="2023-01-01" @endif class="form-control change-date" name="tanggal_potong_akhir"
                                id="tanggal_potong_akhir" value="{{ date('Y-m-d') }}" placeholder="Cari...">
                        </div>
                    </div>
                    <div class="col text-lg-right mt-lg-4">
                        <div class="row">
                            <div class="col-md-12 justify-content-end">
                                <a href="{{ route('warehouse_dash.filter_lb') }}" class="btn btn-primary">Supplier LB</a>
                                <a href="{{ route('purchasing.target') }}" class="btn btn-primary">Daftar Toleransi</a>
                                <a href="{{ route('laporan.lpah') }}" class="btn btn-primary">Laporan Ayam Hidup</a>
                                <a href="javascript:void(0)" class="btn btn-primary" data-toggle="modal" data-target="#tambahdriver">Tambah Driver</a>
                            </div>
                            <div class="col-md-12 justify-content-end mt-2">
                                <a href="{{ route('purchasing.supplier') }}" class="btn btn-outline-info">Data Supplier</a>
                                <a href="{{ route('purchasing.bonus') }}" class="btn btn-outline-danger">Data Bonus</a>
                                <a href="{{ route('laporan.laporanayammerah') }}" class="btn btn-outline-success">Penerimaan Ayam Merah</a>
                            </div>
                        </div>
                    </div>
                </div>
                {{--
            </form> --}}
            <div id="spinerloading" class="text-center mb-2">
                <img src="{{ asset('loading.gif') }}" style="width: 30px">
            </div>
            <div id="purch"></div>
        </div>
    </div>
</section>

<div id="lpah"></div>

<div class="modal" id="tambahdriver" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Input Driver</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('driver.store') }}" method="post">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6">
                            <div class=form-group>
                                <label for="namasopir"> Nama Sopir</label>
                                <input class="form-control" type="text" name="namasopir" id="namasopir" required>
                                @error('namasopir') <div class="small text-danger">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="notelp">No. Telp</label>
                                <input type="number" name="notelp" id="notelp" class="form-control" required>
                                @error('notelp') <div class="small text-danger">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="jenis_driver">Jenis Driver</label>
                        <select name="jenis" id="jenis_driver" class="form-control">
                            <option value="" selected hidden disabled>Pilih Jenis Driver</option>
                            <option value="kirim">Kirim</option>
                            <option value="tangkap">Tangkap</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary ">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('footer')
<script>
    if(window.location.pathname === '/admin/purchasing'){
            LoadDataPurch();
        };

        var loadDataPurchTimeout = null;  

        $("#tanggal_potong_awal,#tanggal_potong_akhir").change(function(){
            if (loadDataPurchTimeout != null) {
                clearTimeout(loadDataPurchTimeout);
            }
            loadDataPurchTimeout = setTimeout(function() {
                loadDataPurchTimeout = null;  
                //ajax code
                LoadDataPurch();

            }, 1000); 
        })

        function LoadDataPurch(){
            $("#spinerloading").show();
            
            var tanggal_potong_awal     =   $('#tanggal_potong_awal').val();
            var tanggal_potong_akhir    =   $('#tanggal_potong_akhir').val();

            $.ajax({
                url : "{{ route('purchasing.purch') }}",
                method: "GET",
                data :{
                    // 'key'                   : 'purch',
                    'tanggal_potong_awal'   : tanggal_potong_awal,
                    'tanggal_potong_akhir'  : tanggal_potong_akhir
                },
                success: function(data){
                    $("#purch").html(data);
                    $('#spinerloading').hide()
                }
            });

            $.ajax({
                url : "{{ route('purchasing.lpah') }}",
                method: "GET",
                data :{
                    'tanggal_potong_awal'   : tanggal_potong_awal,
                    'tanggal_potong_akhir'  : tanggal_potong_akhir
                },
                success: function(res){
                    $("#lpah").html(res);
                }
            });
        }
</script>
@stop