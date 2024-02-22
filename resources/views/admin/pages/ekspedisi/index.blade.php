@extends('admin.layout.template')

@section('title', 'Ekspedisi')

@section('footer')
<script>
    $('.select2').select2({
        theme: 'bootstrap4'
    })
</script>

<script>
    $("#loading").attr('style', 'display: block') ;
$("#data_so").load("{{ route('ekspedisi.index', ['key' => 'sales_order']) }}", function() {
    $("#loading").attr('style', 'display: none') ;
});
$("#show_rute").load("{{ route('ekspedisi.index', ['key' => 'show_rute']) }}");

$("#renderbypass").load("{{ route('ekspedisi.index', ['key' => 'renderbypass']) }}", function() {
    $("#loading").attr('style', 'display: none') ;
});

$("#tanggal_kirim").on('change', function() {
    $("#loading").attr('style', 'display: block') ;
    $("#data_so").attr('style', 'display: none') ;
    var tanggal_kirim   =   $("#tanggal_kirim").val() ;
    var cari            =   encodeURIComponent($("#cari").val()) ;
    $("#data_so").load("{{ route('ekspedisi.index', ['key' => 'sales_order']) }}&tanggal_kirim=" + tanggal_kirim + "&cari=" + cari, function() {
        $("#loading").attr('style', 'display: none') ;
        $("#data_so").attr('style', 'display: block') ;
    }) ;
    $("#renderbypass").load("{{ route('ekspedisi.index', ['key' => 'renderbypass']) }}&tanggal_kirim=" + tanggal_kirim, function() {
        $("#loading").attr('style', 'display: none') ;
    });
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
                key             :   'selesaikan' ,
            },
            success: function(data) {
                if (data.status == 400) {
                    showAlert(data.msg);
                } else {
                    showNotif(data.msg);
                    $("#wilayah").val('').trigger('change') ;
                    $("#driver").val('').trigger('change') ;
                    $("#kernek").val('') ;
                    $("#no_polisi").val('').trigger('change') ;
                    $("#tanggal").val('') ;
                    $("#loading").attr('style', 'display: block') ;
                    $("#data_so").load("{{ route('ekspedisi.index', ['key' => 'sales_order']) }}&tanggal_kirim=" + tanggal_kirim + "&cari=" + cari, function() {
                        $("#loading").attr('style', 'display: none') ;
                        $("#data_so").attr('style', 'display: block') ;
                    }) ;
                    $("#show_rute").load("{{ route('ekspedisi.index', ['key' => 'show_rute']) }}");
                    $("#data_riwayat").load("{{ route('ekspedisi.riwayat', ['key' => 'data_riwayat']) }}") ;
                }
                $('#selesaikan').show() ;
            }
        });
    })
</script>

<script>
    $("#data_riwayat").load("{{ route('ekspedisi.riwayat', ['key' => 'data_riwayat']) }}") ;

$("#tanggal_awal").on('change', function() {
    var awal    =   $("#tanggal_awal").val() ;
    var akhir   =   $("#tanggal_akhir").val() ;
    $("#loading_riwayat").attr('style', 'display: block') ;
    $("#data_riwayat").attr('style', 'display: none') ;
    $("#data_riwayat").load("{{ route('ekspedisi.riwayat', ['key' => 'data_riwayat']) }}&tanggal_awal=" + awal + "&tanggal_akhir=" + akhir, function() {
        $("#data_riwayat").attr('style', 'display: block') ;
        $("#loading_riwayat").attr('style', 'display: none') ;
    }) ;
})
$("#tanggal_akhir").on('change', function() {
    var awal    =   $("#tanggal_awal").val() ;
    var akhir   =   $("#tanggal_akhir").val() ;
    $("#loading_riwayat").attr('style', 'display: block') ;
    $("#data_riwayat").attr('style', 'display: none') ;
    $("#data_riwayat").load("{{ route('ekspedisi.riwayat', ['key' => 'data_riwayat']) }}&tanggal_awal=" + awal + "&tanggal_akhir=" + akhir, function() {
        $("#data_riwayat").attr('style', 'display: block') ;
        $("#loading_riwayat").attr('style', 'display: none') ;
    }) ;
})
$("#unduh_data").on('click', function() {
    var awal    =   $("#tanggal_awal").val() ;
    var akhir   =   $("#tanggal_akhir").val() ;
    window.location.href    =   "{{ route('ekspedisi.riwayat', ['key' => 'data_riwayat']) }}&tanggal_awal=" + awal + "&tanggal_akhir=" + akhir + "&get=unduh" ;
})
</script>

<script>
    $("#wilayah_check").on('change', function() {
    if ($("#wilayah_check:checked").val() == 'on') {
        $("#wilayah_select").attr("style", "display: none") ;
        $("#input_wilayah").attr("style", "display: block") ;
        $("#wilayah").val("").trigger('change') ;
        $("#input_wilayah").val("") ;
    } else {
        $("#wilayah_select").attr("style", "display: block") ;
        $("#input_wilayah").attr("style", "display: none") ;
        $("#wilayah").val("").trigger('change') ;
        $("#input_wilayah").val("") ;
    }
})

$("#nopol_check").on('change', function() {
    if ($("#nopol_check:checked").val() == 'on') {
        $("#nopol_select").attr("style", "display: none") ;
        $("#input_nopol").attr("style", "display: block") ;
        $("#no_polisi").val("").trigger('change') ;
        $("#input_nopol").val("") ;
    } else {
        $("#nopol_select").attr("style", "display: block") ;
        $("#input_nopol").attr("style", "display: none") ;
        $("#no_polisi").val("").trigger('change') ;
        $("#input_nopol").val("") ;
    }
})

$("#driver_check").on('change', function() {
    if ($("#driver_check:checked").val() == 'on') {
        $("#driver_select").attr("style", "display: none") ;
        $("#input_driver").attr("style", "display: block") ;
        $("#telp_driver").attr("style", "display: block") ;
        $("#driver").val("").trigger('change') ;
        $("#input_driver").val("") ;
        $("#telp_driver").val("") ;
    } else {
        $("#driver_select").attr("style", "display: block") ;
        $("#input_driver").attr("style", "display: none") ;
        $("#telp_driver").attr("style", "display: none") ;
        $("#driver").val("").trigger('change') ;
        $("#input_driver").val("") ;
        $("#telp_driver").val("") ;
    }
})

var tanggalKirim ="";
$(".btnkirim").on('click', function () {
    tanggalKirim = $(this).val();
    loadOrderProduksi($(this));;
});

loadOrderProduksi();
$(".cari_order").on('keyup', function () {
    loadOrderProduksi();
});

function loadOrderProduksi(button) {
    // ambil value ketika button aktif
    $(".btnkirim").removeClass('active');
    $(button).addClass('active');
    var btnTanggal = $(button).val();
    
    var text_cari = encodeURIComponent($(".cari_order").val());
    
    $.ajax({
        method: "GET",
        url: "{{route('regu.order_produksi')}}?tanggal_kirim="+tanggalKirim+"&cari_order="+text_cari,
        cache: false,
        beforeSend: function(){
            $("#loading-order-produksi").show();
        },
        success: function (response) {
            // console.log(btnTanggal);
            $("#data-order-produksi").html(response);
            $("#loading-order-produksi").hide();
        }
    });
}
</script>
@endsection

@section('content')
<div class="font-weight-bold text-center mb-4">Ekspedisi</div>

<ul class="nav nav-tabs" role="tablist">
    <li class="nav-item" role="presentation">
        <a class="nav-link active" id="inputRute-tab" data-toggle="tab" href="#inputRute" role="tab"
            aria-controls="inputRute" aria-selected="true">Input Rute</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link" id="sumary-tab" data-toggle="tab" href="#sumary" role="tab" aria-controls="sumary"
            aria-selected="false">Summary Rute</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link" id="order-produksi" data-toggle="tab" href="#orderProduksi" role="tab" aria-controls="order-produksi"
            aria-selected="false">Order Produksi</a>
    </li>
</ul>
<div class="tab-content">
    <div class="tab-pane fade show active" id="inputRute" role="tabpanel" aria-labelledby="inputRute-tab">
        <div id="renderbypass" class="mt-3"></div>
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
                                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) min="2023-01-01"
                                            @endif value="{{ date("Y-m-d", strtotime('+1 days', time())) }}"
                                            id="tanggal_kirim" class="form-control rounded-0">
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
                                    <option value="{{ $row->id }}">{{ $row->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="row">
                                <div class="col pr-1">
                                    <input type="text" id="input_driver" class="form-control"
                                        placeholder="Tulis driver baru" style="display: none">
                                </div>
                                <div class="col pl-1">
                                    <input type="number" id="telp_driver" class="form-control"
                                        placeholder="Tulis nomor telepon" style="display: none">
                                </div>
                            </div>
                            <input type="checkbox" id="driver_check"> <label for="driver_check">Tambah driver
                                baru</label>
                        </div>

                        <div class="form-group">
                            <label for="kernek">Kernek</label>
                            <input type="text" autocomplete="off" placeholder="Tulis Kernek" id="kernek"
                                class="form-control">
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
                                            <option value="{{ $row }}">{{ $row }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <input type="text" id="input_nopol" class="form-control"
                                        placeholder="Tulis Nomor Polisi" style="display: none">
                                    <input type="checkbox" id="nopol_check"> <label for="nopol_check">Tambah nomor
                                        polisi baru</label>
                                </div>
                            </div>

                            <div class="col pl-1">
                                <div class="form-group">
                                    <label for="wilayah">Wilayah</label>
                                    <div id="wilayah_select">
                                        <select name="wilayah" id="wilayah" data-placeholder="Pilih Wilayah"
                                            data-width="100%" class="form-control select2">
                                            <option value=""></option>
                                            @foreach ($wilayah as $row)
                                            <option value="{{ $row->id }}">{{ $row->nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <input type="text" id="input_wilayah" class="form-control"
                                        placeholder="Tulis wilayah" style="display: none">
                                    <input type="checkbox" id="wilayah_check"> <label for="wilayah_check">Tambah wilayah
                                        baru</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="no_polisi">Tanggal Ekspedisi</label>
                            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                min="2023-01-01" @endif id="tanggal" value="{{ date("Y-m-d", strtotime('+1 days',time())) }}" class="form-control">
                        </div>

                        <div class="mt-3 pt-3" id="show_rute"></div>
                    </div>
                    <div class="card-footer text-right">
                        <button class="btn btn-primary" id="selesaikan">Submit</button>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="sumary" role="tabpanel" aria-labelledby="sumary-tab">

        <section class="panel mt-3">
            <div class="card-body">
                Pencarian Tanggal
                <div class="row">
                    <div class="col pr-1">
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                            min="2023-01-01" @endif id="tanggal_awal" value="{{ date("Y-m-d") }}" class="form-control">
                    </div>
                    <div class="col px-1">
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                            min="2023-01-01" @endif id="tanggal_akhir" value="{{ date("Y-m-d", strtotime('+1 day')) }}"
                            class="form-control">
                    </div>
                    <div class="col-auto pl-1">
                        <button type="button" id="unduh_data" class="btn btn-success">Unduh</button>
                    </div>
                </div>
            </div>
        </section>

        <h5 id="loading_riwayat" style="display: none"><i class="fa fa-refresh fa-spin"></i> Loading.....</h5>
        <div id="data_riwayat"></div>

    </div>
    <div class="tab-fane fade" id="orderProduksi" role="tabpanel" aria-labelledby="order-produksi-tab">
        <section class="panel mt-3">
            <div class="card-body">
                <form class="form-inline">
                    <div class="form-group">
                        <h6 class="mr-3">Tanggal Kirim:</h6>
                        @foreach ($nextday as $i => $date)
                        <button type="button" name="tanggal" data-tgl="{{$date}}" value="{{ $date }}"class="btn btn-outline-primary mr-2 btnkirim" style="margin-bottom: 5px;" id="btn_tgl_plus">
                            {{ date('d/m/y', strtotime($date)) }}
                        </button>
                        @endforeach
                    </div>
                    <div class="form-group">
                        <input class="form-control form-control-sm cari_order" id="c_o" type="text" placeholder="cari customer atau item" style="margin-bottom: 5px;">
                    </div>
                </form>
            </div>
        </section>
        <section class="panel">
            <div class="card-body">
                <div id="loading-order-produksi" class="text-center" style="display: none">
                    <img src="{{ asset('loading.gif') }}" width="20px">
                </div>
                <div id="data-order-produksi"></div>
            </div>
        </section>
    </div>
</div>


@endsection