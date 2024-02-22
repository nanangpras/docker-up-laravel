@extends('admin.layout.template')

@section('title', 'Detail Ekspedisi')

@section('footer')
<script>
    $('.select2').select2({
        theme: 'bootstrap4'
    })
</script>

<script>
    $("#wilayah_check").on('change', function() {
    if ($("#wilayah_check:checked").val() == 'on') {
        $("#wilayah_select").attr("style", "display: none") ;
        $("#input_wilayah").attr("style", "display: block") ;
        $("#input_wilayah").val("") ;
    } else {
        $("#wilayah_select").attr("style", "display: block") ;
        $("#input_wilayah").attr("style", "display: none") ;
        $("#input_wilayah").val("") ;
    }
})

$("#nopol_check").on('change', function() {
    if ($("#nopol_check:checked").val() == 'on') {
        $("#nopol_select").attr("style", "display: none") ;
        $("#input_nopol").attr("style", "display: block") ;
        $("#input_nopol").val("") ;
    } else {
        $("#nopol_select").attr("style", "display: block") ;
        $("#input_nopol").attr("style", "display: none") ;
        $("#input_nopol").val("") ;
    }
})

$("#driver_check").on('change', function() {
    if ($("#driver_check:checked").val() == 'on') {
        $("#driver_select").attr("style", "display: none") ;
        $("#input_driver").attr("style", "display: block") ;
        $("#telp_driver").attr("style", "display: block") ;
        $("#input_driver").val("") ;
        $("#telp_driver").val("") ;
    } else {
        $("#driver_select").attr("style", "display: block") ;
        $("#input_driver").attr("style", "display: none") ;
        $("#telp_driver").attr("style", "display: none") ;
        $("#input_driver").val("") ;
        $("#telp_driver").val("") ;
    }
})
</script>

<script>
    $("#loading").attr('style', 'display: block') ;
$("#data_so").load("{{ route('ekspedisi.index', ['key' => 'sales_order']) }}&id={{ $ekspedisi->id }}", function() {
    $("#loading").attr('style', 'display: none') ;
});
$("#show_rute").load("{{ route('ekspedisi.index', ['key' => 'show_rute']) }}&id={{ $ekspedisi->id }}");

$("#tanggal_kirim").on('change', function() {
    $("#loading").attr('style', 'display: block') ;
    $("#data_so").attr('style', 'display: none') ;
    var tanggal_kirim   =   $("#tanggal_kirim").val() ;
    var cari            =   encodeURIComponent($("#cari").val()) ;
    $("#data_so").load("{{ route('ekspedisi.index', ['key' => 'sales_order']) }}&tanggal_kirim=" + tanggal_kirim + "&cari=" + cari, function() {
        $("#loading").attr('style', 'display: none') ;
        $("#data_so").attr('style', 'display: block') ;
    }) ;
})

$("#cari").on('keyup', function() {
    $("#loading").attr('style', 'display: block') ;
    $("#data_so").attr('style', 'display: none') ;
    var tanggal_kirim   =   $("#tanggal_kirim").val() ;
    var cari            =   encodeURIComponent($("#cari").val()) ;
    $("#data_so").load("{{ route('ekspedisi.index', ['key' => 'sales_order']) }}&tanggal_kirim=" + tanggal_kirim + "&cari=" + cari, function() {
        $("#loading").attr('style', 'display: none') ;
        $("#data_so").attr('style', 'display: block') ;
    }) ;
})
</script>

<script>
    $('#selesaikan').click(function() {
        var tanggal_kirim   =   $("#tanggal_kirim").val() ;
        var cari            =   encodeURIComponent($("#cari").val()) ;

        var wilayah         =   $("#wilayah").val() ;
        var driver          =   $("#driver").val() ;
        var kernek          =   $("#kernek").val() ;
        var no_polisi       =   $("#no_polisi").val() ;
        var tanggal         =   $("#tanggal").val() ;

        var input_driver    =   $("#input_driver").val() ;
        var telp_driver     =   $("#telp_driver").val() ;
        var input_nopol     =   $("#input_nopol").val() ;
        var input_wilayah   =   $("#input_wilayah").val() ;

        var ekspedisi       =   "{{ $ekspedisi->id }}" ;

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#selesaikan').hide() ;

        $.ajax({
            url: "{{ route('ekspedisi.store') }}",
            method: "POST",
            data: {
                wilayah         :   wilayah ,
                driver          :   driver ,
                kernek          :   kernek ,
                no_polisi       :   no_polisi ,
                tanggal         :   tanggal ,
                input_driver    :   input_driver ,
                telp_driver     :   telp_driver ,
                input_nopol     :   input_nopol ,
                input_wilayah   :   input_wilayah ,
                ekspedisi       :   ekspedisi ,
                key             :   'selesaikan' ,
            },
            success: function(data) {
                if (data.status == 400) {
                    showAlert(data.msg);
                } else {
                    showNotif(data.msg);
                    $("#loading").attr('style', 'display: block') ;
                    $("#data_so").load("{{ route('ekspedisi.index', ['key' => 'sales_order']) }}&id=" + ekspedisi + "&tanggal_kirim=" + tanggal_kirim + "&cari=" + cari, function() {
                        $("#loading").attr('style', 'display: none') ;
                        $("#data_so").attr('style', 'display: block') ;
                    }) ;
                    $("#show_rute").load("{{ route('ekspedisi.index', ['key' => 'show_rute']) }}&id=" + ekspedisi);
                }
                $('#selesaikan').show() ;
            }
        });
    })
</script>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col"><a href="{{ route('ekspedisi.index') }}"><i class="fa fa-arrow-left"></i> Kembali</a></div>
    <div class="col-7 text-center font-weight-bold">DETAIL EKSPEDISI</div>
    <div class="col"></div>
</div>

<div class="row mt-3">
    <div class="col-lg-6 pr-lg-1">
        <section class="panel">
            <div class="card-header font-weight-bold">DAFTAR SALES ORDER</div>
            <div class="card-body p-2">
                <div class="border-bottom pt-1 mb-3 sticky-top bg-white">
                    <div class="row">
                        <div class="col pr-1">
                            <div class="form-group">
                                <label for="tanggal_kirim">Tanggal Kirim</label>
                                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                    min="2023-01-01" @endif value="{{ date("Y-m-d") }}" id="tanggal_kirim"
                                    class="form-control rounded-0">
                            </div>
                        </div>
                        <div class="col pl-1">
                            <div class="form-group">
                                <label for="cari">Cari</label>
                                <input type="text" placeholder="Cari Data..." autocomplete="off" id="cari"
                                    class="form-control rounded-0">
                            </div>
                        </div>
                    </div>
                </div>

                <h5 class="text-center" id="loading" style="display: none"><i class="fa fa-refresh fa-spin"></i>
                    Loading...</h5>
                <div id="data_so"></div>
            </div>
        </section>
    </div>

    <div class="col-lg-6 pl-lg-1">
        <section class="panel sticky-top">
            <div class="card-body p-2">
                <div class="form-group">
                    <label for="driver">Driver</label>
                    <div id="driver_select">
                        <select name="driver" id="driver" data-placeholder="Pilih Driver" data-width="100%"
                            class="form-control select2">
                            <option value=""></option>
                            @foreach ($driver as $row)
                            <option value="{{ $row->id }}" {{ $ekspedisi->nama == $row->nama ? 'selected' : '' }}>{{ $row->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col pr-1">
                            <input type="text" id="input_driver" class="form-control" placeholder="Tulis driver baru"
                                style="display: none">
                        </div>
                        <div class="col pl-1">
                            <input type="number" id="telp_driver" class="form-control" placeholder="Tulis nomor telepon"
                                style="display: none">
                        </div>
                    </div>
                    <input type="checkbox" id="driver_check"> <label for="driver_check">Tambah driver baru</label>
                </div>

                <div class="form-group">
                    <label for="kernek">Kernek</label>
                    <input type="text" autocomplete="off" placeholder="Tulis Kernek"
                        value="{{ $ekspedisi->kernek ?? '' }}" id="kernek" class="form-control">
                </div>

                <div class="row">
                    <div class="col pr-1">
                        <div class="form-group">
                            <label for="no_polisi">Nomor Polisi</label>
                            <div id="nopol_select">
                                <select name="no_polisi" id="no_polisi" class="form-control select2"
                                    data-placeholder="Pilih Nomor Polisi" data-width="100%">
                                    <option value=""></option>
                                    @foreach ($nopol as $id => $row)
                                    <option value="{{ $row }}" {{ $ekspedisi->no_polisi == $row ? 'selected' : '' }}>{{ $row }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <input type="text" id="input_nopol" class="form-control" placeholder="Tulis Nomor Polisi"
                                style="display: none">
                            <input type="checkbox" id="nopol_check"> <label for="nopol_check">Tambah nomor polisi baru</label>
                        </div>
                    </div>

                    <div class="col pl-1">
                        <div class="form-group">
                            <label for="wilayah">Wilayah</label>
                            <div id="wilayah_select">
                                <select name="wilayah" id="wilayah" data-placeholder="Pilih Wilayah" data-width="100%"
                                    class="form-control select2">
                                    <option value=""></option>
                                    @foreach ($wilayah as $row)
                                    <option value="{{ $row->id }}" {{ $ekspedisi->wilayah_id == $row->id ? 'selected' : '' }}>{{ $row->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <input type="text" id="input_wilayah" class="form-control" placeholder="Tulis wilayah"
                                style="display: none">
                            <input type="checkbox" id="wilayah_check"> <label for="wilayah_check">Tambah wilayah
                                baru</label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="no_polisi">Tanggal Ekspedisi</label>
                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                        min="2023-01-01" @endif id="tanggal" value="{{ $ekspedisi->tanggal }}" class="form-control">
                </div>

                <div class="mt-3 pt-3" id="show_rute"></div>
            </div>
            <div class="card-footer text-right">
                <button class="btn btn-primary" id="selesaikan">Ubah</button>
            </div>
        </section>
    </div>
</div>
@endsection