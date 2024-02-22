@extends('admin.layout.template')

@section('title', 'Laporan')

@section('content')
<div class="mb-4 text-center font-weight-bold">Laporan Admin</div>

<section class="panel">
    <div class="card-body">
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label for="tanggalstart">Mulai</label>
                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                        min="2023-01-01" @endif name="tanggalstart" class="form-control" id="tanggalstart"
                        placeholder="Tuliskan " value="{{ date("Y-m-d") }}" autocomplete="off">
                    @error('tanggalstart') <div class="small text-danger">{{ message }}</div> @enderror
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label for="tanggalend">Akhir</label>
                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                        min="2023-01-01" @endif name="tanggalend" class="form-control" id="tanggalend"
                        placeholder="Tuliskan " value="{{ date("Y-m-d") }}" autocomplete="off">
                    @error('tanggalend') <div class="small text-danger">{{ message }}</div> @enderror
                </div>
            </div>
        </div>

        <div class="radio-toolbar row">
            <div class="col-lg-3 col-4">
                <div class="form-group">
                    <input type="radio" name="tujuan" value="lpah" class="tujuan" id="lpah">
                    <label for="lpah">LPAH</label>
                </div>
            </div>
            <div class="col-lg-3 col-4">
                <div class="form-group">
                    <input type="radio" name="tujuan" value="evis" class="tujuan" id="evis">
                    <label for="evis">Evis</label>
                </div>
            </div>
            <div class="col-lg-3 col-4">
                <div class="form-group">
                    <input type="radio" name="tujuan" value="grading" class="tujuan" id="grading">
                    <label for="grading">Grading</label>
                </div>
            </div>
            <div class="col-lg-3 col-4">
                <div class="form-group">
                    <input type="radio" name="tujuan" value="hasilproduksi" class="tujuan" id="boneless">
                    <label for="boneless">Hasil Produksi</label>
                </div>
            </div>
            <div class="col-lg-3 col-4">
                <div class="form-group">
                    <input type="radio" name="tujuan" value="ambilbb" class="tujuan" id="parting">
                    <label for="parting">Pengambilan Bahan Baku</label>
                </div>
            </div>
            <div class="col-lg-3 col-4">
                <div class="form-group">
                    <input type="radio" name="tujuan" value="siapkirim" class="tujuan" id="marinasi">
                    <label for="marinasi">Siap Kirim</label>
                </div>
            </div>
            <div class="col-lg-3 col-4">
                <div class="form-group">
                    <input type="radio" name="tujuan" value="sisachiller" class="tujuan" id="sisa">
                    <label for="sisa">Sisa Chiller</label>
                </div>
            </div>
            <div class="col-lg-3 col-4">
                <div class="form-group">
                    <input type="radio" name="tujuan" value="abf" class="tujuan" id="abf">
                    <label for="abf">ABF</label>
                </div>
            </div>
            <div class="col-lg-3 col-4">
                <div class="form-group">
                    <input type="radio" name="tujuan" value="gudang" class="tujuan" id="gudang">
                    <label for="gudang">Gudang</label>
                </div>
            </div>
            <div class="col-lg-3 col-4">
                <div class="form-group">
                    <input type="radio" name="tujuan" value="retur" class="tujuan" id="retur">
                    <label for="retur">Retur</label>
                </div>
            </div>
        </div>

        <div class="form-group">
            <button type="button" class="btn btn-primary exportlaporan" disabled="disabled"> <i
                    class="fa fa-spinner fa-spin spinerloading" style="display:none;"></i> <span id="text">Export
                    Excel</span></button>
        </div>
    </div>
</section>

<section class="panel">
    <div class="card-body">
        <div id="loading" class="text-center" style="display: none">
            <img src="{{ asset('loading.gif') }}" width="30px">
        </div>
        <div id="show"></div>
    </div>
</section>

<script>
    var tglmulai    = $('#tanggalstart').val();
        var tglselesai  = $('#tanggalend').val();
        var tujuan      = $('.tujuan:checked').val();

        $('.tujuan').click(function() {
            $(".exportlaporan").removeAttr('disabled');
            loadTujuan()
        });
        
        $('#tanggalend,#tanggalstart').on('change', function() {
            loadTujuan()
        });
        function loadTujuan(){

            tglmulai    = $('#tanggalstart').val();
            tglselesai  = $('#tanggalend').val();
            tujuan      = $('.tujuan:checked').val();

            $.ajax({
                url     : "{{ route('laporanadmin.laporan') }}",
                method  : "GET",
                cache   : true,
                data    :{
                    'tglmulai'      : tglmulai,
                    'tglselesai'    : tglselesai,
                    'tujuan'        : tujuan
                },
                beforeSend:function(){
                    $('#loading').show();
                    $("#show").hide();
                },
                success: function(data){
                    $("#show").html(data);
                    $("#show").show();
                    $('#loading').hide();
                }
            });
        }

        $(".exportlaporan").click(function(){
            tglmulai    = $('#tanggalstart').val();
            tglselesai  = $('#tanggalend').val();
            tujuan      = $('.tujuan:checked').val();

            if(tujuan === undefined || tujuan === null){
                showAlert('Pilih Salah Satu Data');
                return false;
            }

            $.ajax({
                url     : "{{ route('laporanadmin.export') }}",
                method  : "POST",
                cache   : false,
                data    :{
                    'tglmulai'      : tglmulai,
                    'tglselesai'    : tglselesai,
                    'tujuan'        : tujuan,
                    "_token"        : "{{ csrf_token() }}"
                },
                beforeSend: function() {
                    if(tujuan ===undefined && tujuan === ''){
                        $('.exportlaporan').addAttr('disabled');
                        $(".spinerloading").show();
                        setTimeout(function(){ 
                            $(".exportlaporan").removeAttr('disabled');
                            $(".spinerloading").show();
                        }, 2000);        
                    }
                    if(tujuan){
                        $('.exportlaporan').attr('disabled');
                        $(".spinerloading").show(); 
                        $("#text").text('Downloading...');
                    }    
                },
                success: function(data) {
                    $(".exportlaporan").attr('disabled');
                    setTimeout(() => {
                        $("#text").text('Export Excel');
                        $(".spinerloading").hide();
                        window.location.href = "{{ url('admin/laporanadmin/export') }}?tglmulai=" + tglmulai + "&tglselesai=" + tglselesai + "&tujuan=" + tujuan;
                    }, 3000);
                }
            });
        })
</script>
@endsection