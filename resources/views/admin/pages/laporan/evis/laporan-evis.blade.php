@extends('admin.layout.template')

@section('title', 'Laporan Evis')

@section('content')
<div class="row mb-4">
    <div class="col">
        <a href="{{ route('evis.index') }}" class="btn btn-outline btn-sm btn-back"> <i class="fa fa-arrow-left"></i>
            Back</a>
    </div>
    <div class="col text-center">
        <b>Laporan Evis</b>
    </div>
    <div class="col"></div>
</div>


<ul class="nav nav-tabs" role="tablist">
    <li class="nav-item" role="presentation">
        <a class="nav-link tab-link" id="umum-tab" data-toggle="tab" href="#umum" role="tab" aria-controls="umum"
            aria-selected="true">Laporan Umum</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link tab-link" id="perminggu-tab" data-toggle="tab" href="#perminggu" role="tab"
            aria-controls="perminggu" aria-selected="false">Laporan Per Minggu</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link tab-link" id="perbandingan-tab" data-toggle="tab" href="#perbandingan" role="tab"
            aria-controls="perbandingan" aria-selected="false">Laporan Perbandingan</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link tab-link" id="persentase-tab" data-toggle="tab" href="#persentase" role="tab"
            aria-controls="persentase" aria-selected="false">Laporan Persentase Penjualan</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link tab-link" id="penjualan-tab" data-toggle="tab" href="#penjualan" role="tab"
            aria-controls="penjualan" aria-selected="false">Laporan Perbandingan Penjualan</a>
    </li>
</ul>
<div class="tab-content mt-2">
    <section class="panel">
        <div class="card-body">
            <b>Pencarian Bedasarkan Tanggal</b>
            <div class="row mt-2">
                <div class="col-md col-6">
                    <div class="form-group">
                        Tanggal Mulai
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                            min="2023-01-01" @endif id="tanggalMulai" name="mulai" value="{{ $mulai }}"
                            class="form-control form-control-sm" required>
                    </div>
                </div>

                <div class="col-md col-6">
                    <div class="form-group">
                        Tanggal Selesai
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                            min="2023-01-01" @endif id="tanggalSelesai" name="selesai" value="{{ $selesai }}"
                            class="form-control form-control-sm" required>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="tab-pane fade" id="umum" role="tabpanel" aria-labelledby="umum-tab">
        <div id="loadingLaporanUmum" class="text-center" style="display: none">
            <img src="{{ asset('loading.gif') }}" width="30px">
        </div>
        <div id="laporanUmum"></div>
    </div>

    <div class="tab-pane fade" id="perminggu" role="tabpanel" aria-labelledby="perminggu-tab">
        <div id="loadingLaporanPerminggu" class="text-center" style="display: none">
            <img src="{{ asset('loading.gif') }}" width="30px">
        </div>
        <div id="laporanPerminggu"></div>
    </div>

    <div class="tab-pane fade" id="perbandingan" role="tabpanel" aria-labelledby="perbandingan-tab">
        <div id="loadingLaporanPerbandingan" class="text-center" style="display: none">
            <img src="{{ asset('loading.gif') }}" width="30px">
        </div>
        <div id="laporanPerbandingan"></div>
    </div>

    <div class="tab-pane fade" id="persentase" role="tabpanel" aria-labelledby="persentase-tab">
        <div id="loadingLaporanPersentase" class="text-center" style="display: none">
            <img src="{{ asset('loading.gif') }}" width="30px">
        </div>
        <div id="laporanPersentase"></div>
    </div>

    <div class="tab-pane fade" id="penjualan" role="tabpanel" aria-labelledby="penjualan-tab">
        <div id="loadingLaporanPerbandinganPenjualan" class="text-center" style="display: none">
            <img src="{{ asset('loading.gif') }}" width="30px">
        </div>
        <div id="laporanPerbandinganPenjualan"></div>
    </div>

</div>

<script>
    var hash = window.location.hash.substr(1);
    var href = window.location.href;

    deafultPage();

    function deafultPage() {
        if (hash == undefined || hash == "") {
            hash = "umum";
        }

        $('#' + hash + '-tab').addClass('active').siblings().removeClass('active');
        $('#' + hash).addClass('active show').siblings().removeClass('active show');

    }


    $('.tab-link').click(function(e) {
        e.preventDefault();
        status = $(this).attr('aria-controls');
        window.location.hash = status;
        href = window.location.href;
    });


    function loadLaporanUmum() {
        $("#loadingLaporanUmum").show();
        const tanggalMulai      = $("#tanggalMulai").val();
        const tanggalSelesai    = $("#tanggalSelesai").val();
        $("#laporanUmum").load("{{ route('evis.laporan', ['key' => 'laporanUmum']) }}&tanggalMulai=" + tanggalMulai + "&tanggalSelesai=" + tanggalSelesai, () => {
            $("#loadingLaporanUmum").hide();
        })
    }

    function loadLaporanPerbandingan() {
        $("#loadingLaporanPerbandingan").show();
        const tanggalMulai      = $("#tanggalMulai").val();
        const tanggalSelesai    = $("#tanggalSelesai").val();
        $("#laporanPerbandingan").load("{{ route('evis.laporan', ['key' => 'laporanPerbandingan']) }}&tanggalMulai=" + tanggalMulai + "&tanggalSelesai=" + tanggalSelesai, () => {
            $("#loadingLaporanPerbandingan").hide();
        })
    }

    function loadLaporanPerminggu() {
        $("#loadingLaporanPerminggu").show();
        // $(".utama").attr('style', 'display: none');
        // $(".perminggu").attr('style', 'display: block');
        const tanggalMulai      = $("#tanggalMulai").val();
        console.log(tanggalMulai);
        const tanggalSelesai    = $("#tanggalSelesai").val();
        $("#laporanPerminggu").load("{{ route('evis.laporan', ['key' => 'laporanPerminggu']) }}&tanggalMulai=" + tanggalMulai + "&tanggalSelesai="+tanggalSelesai, () => {
            $("#loadingLaporanPerminggu").hide();
        })
    }

    function loadLaporanPersentase() {
        $("#loadingLaporanPersentase").show();
        const tanggalMulai      = $("#tanggalMulai").val();
        const tanggalSelesai    = $("#tanggalSelesai").val();
        $("#laporanPersentase").load("{{ route('evis.laporan', ['key' => 'laporanPersentase']) }}&tanggalMulai=" + tanggalMulai + "&tanggalSelesai=" + tanggalSelesai, () => {
            $("#loadingLaporanPersentase").hide();
        })
    }

    function loadLaporanPerbandinganPenjualan() {
        $("#loadingLaporanPerbandinganPenjualan").show();
        const tanggalMulai      = $("#tanggalMulai").val();
        const tanggalSelesai    = $("#tanggalSelesai").val();
        $("#laporanPerbandinganPenjualan").load("{{ route('evis.laporan', ['key' => 'laporanPenjualan']) }}&tanggalMulai=" + tanggalMulai + "&tanggalSelesai=" + tanggalSelesai, () => {
            $("#loadingLaporanPerbandinganPenjualan").hide();
        })
    }


    $('[data-toggle="tab"]').click(function () {
        var hash = $(this).attr('href');
        if (hash == "#umum") {
            loadLaporanUmum();
        } else
        if (hash == "#perminggu") {
            loadLaporanPerminggu();
        } else
        if (hash == "#perbandingan") {
            loadLaporanPerbandingan();
        } else if (hash == "#persentase") {
            loadLaporanPersentase();
        } else if (hash == "#penjualan") {
            loadLaporanPerbandinganPenjualan();
        }
    });


    function loadLaporan() {
            if (window.location.hash.substr(1) == "umum-tab") {
                loadLaporanUmum();
            } else
            if (window.location.hash.substr(1) == "perminggu") {
                loadLaporanPerminggu();
            } else
            if (window.location.hash.substr(1) == "perbandingan") {
                loadLaporanPerbandingan();
            } else
            if (window.location.hash.substr(1) == "persentase") {
                loadLaporanPersentase();
            } else if (window.location.hash.substr(1) == "penjualan"){
                loadLaporanPerbandinganPenjualan();
            } else {
                loadLaporanUmum();
            }
        };

    loadLaporan();

    var searchTimeout = null;
    $("#tanggalMulai, #tanggalSelesai").on('change', () => {
        if (searchTimeout != null) {
            clearTimeout(searchTimeout);
        }
        searchTimeout = setTimeout(function() {
            searchTimeout = null;
            //ajax code
            loadLaporan();
        }, 500);
    })

</script>



@stop