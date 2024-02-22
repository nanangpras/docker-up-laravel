@extends('admin.layout.template')

@section('title', 'Kepala Regu ' . $regu)



@section('header')
<style>
    ol.switches {
        padding-left: 0 !important;
    }

    .switches li {
        position: relative;
        counter-increment: switchCounter;
        list-style-type: none;
    }

    .switches li:not(:last-child) {
        border-bottom: 1px solid var(--gray);
    }

    .switches label {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 7px
    }

    .switches span:last-child {
        position: relative;
        width: 50px;
        height: 26px;
        border-radius: 15px;
        box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.4);
        background: var(--gray);
        transition: all 0.3s;
    }

    .switches span:last-child::before,
    .switches span:last-child::after {
        content: "";
        position: absolute;
    }

    .switches span:last-child::before {
        left: 1px;
        top: 1px;
        width: 24px;
        height: 24px;
        background: var(--white);
        border-radius: 50%;
        z-index: 1;
        transition: transform 0.3s;
    }

    .switches span:last-child::after {
        top: 50%;
        right: 8px;
        width: 12px;
        height: 12px;
        transform: translateY(-50%);
        background: url(https://s3-us-west-2.amazonaws.com/s.cdpn.io/162656/uncheck-switcher.svg);
        background-size: 12px 12px;
    }

    .switches [type="checkbox"] {
        position: absolute;
        left: -9999px;
    }

    .switches [type="checkbox"]:checked+label span:last-child {
        background: var(--green);
    }

    .switches [type="checkbox"]:checked+label span:last-child::before {
        transform: translateX(24px);
    }

    .switches [type="checkbox"]:checked+label span:last-child::after {
        width: 14px;
        height: 14px;
        /*right: auto;*/
        left: 8px;
        background-image: url(https://s3-us-west-2.amazonaws.com/s.cdpn.io/162656/checkmark-switcher.svg);
        background-size: 14px 14px;
    }
</style>
@endsection

@section('content')

<div class="row mb-4">
    <div class="col">
        <a href="{{ route('regu.index') }}" class="btn btn-outline btn-sm btn-back"><i class="fa fa-arrow-left"></i>
            Back</a>
    </div>
    <div class="col-7 col text-center py-2">
        <b class="text-uppercase">KEPALA REGU {{ $regu }}</b>
    </div>
    <div class="col">
        @if ($regu != 'Meyer' && $regu != 'Admin Produksi')
        <a href="{{ route('regu.index', ['key' => 'dashboardregu']) }}&regu={{ $regu }}"
            class="btn btn-primary btn-block">Dashboard</a>
        @endif
    </div>
</div>

<section class="panel">
    <div class="card card-primary card-outline card-tabs">
        <ul class="nav nav-tabs" id="regu-tabs-tab" role="tablist">
            @if ($regu != 'Meyer' && $regu != 'Admin Produksi')
            <li class="nav-item">
                <a class="nav-link  tab-link" id="regu-tabs-produksi-tab" data-toggle="pill" href="#regu-tabs-produksi"
                    role="tab" aria-controls="regu-tabs-produksi" aria-selected="true">Produksi</a>
            </li>
            @endif
            {{-- @if (env('NET_SUBSIDIARY', 'EBA') == 'EBA') --}}
            <li class="nav-item">
                <a class="nav-link  tab-link" id="regu-tabs-request-tab" data-toggle="pill" href="#regu-tabs-request"
                    role="tab" aria-controls="regu-tabs-request" aria-selected="false">Input By Order</a>
            </li>
            <li class="nav-item">
                <a class="nav-link  tab-link" id="regu-tabs-stock-tab" data-toggle="pill" href="#regu-tabs-stock"
                    role="tab" aria-controls="regu-tabs-stock" aria-selected="false">Stock By Item Gudang</a>
            </li>
            <li class="nav-item">
                <a class="nav-link  tab-link" id="regu-tabs-sameday-tab" data-toggle="pill" href="#regu-tabs-sameday"
                    role="tab" aria-controls="regu-tabs-sameday" aria-selected="false">Same Day</a>
            </li>
            @if (env('NET_SUBSIDIARY', 'EBA') == 'EBA')
            <li class="nav-item">
                <a class="nav-link  tab-link" id="regu-tabs-parkingorder-tab" data-toggle="pill"
                    href="#regu-tabs-parkingorder" role="tab" aria-controls="regu-tabs-parkingorder"
                    aria-selected="false">Parking Order</a>
            </li>
            @endif
            <li class="nav-item">
                <a class="nav-link  tab-link" id="regu-tabs-actualorder-tab" data-toggle="pill"
                    href="#regu-tabs-actualorder" role="tab" aria-controls="regu-tabs-actualorder"
                    aria-selected="false">Actual Order</a>
            </li>
            <li class="nav-item">
                <a class="nav-link  tab-link" id="regu-tabs-orderproduksi-tab" data-toggle="pill"
                    href="#regu-tabs-orderproduksi" role="tab" aria-controls="regu-tabs-orderproduksi"
                    aria-selected="false">Order Produksi</a>
            </li>
            {{-- @endif --}}
            @if ($regu != 'Meyer' && $regu != 'Admin Produksi')
            <li class="nav-item">
                <a class="nav-link  tab-link" id="regu-tabs-summary-tab" data-toggle="pill" href="#regu-tabs-summary"
                    role="tab" aria-controls="regu-tabs-summary" aria-selected="false">Summary Global</a>
            </li>
            <li class="nav-item">
                <a class="nav-link  tab-link" id="regu-tabs-summaryprod-tab" data-toggle="pill"
                    href="#regu-tabs-summaryprod" role="tab" aria-controls="regu-tabs-summaryprod"
                    aria-selected="false">Summary Hasil Produksi</a>
            </li>
            @endif
            @if (env('NET_SUBSIDIARY', 'CGL') == 'CGL')
            @if ($regu != 'Meyer' && $regu != 'Admin Produksi')
            <li class="nav-item">
                <a class="nav-link  tab-link" id="regu-tabs-alokasi-tab" data-toggle="pill" href="#regu-tabs-alokasi"
                    role="tab" aria-controls="regu-tabs-alokasi" aria-selected="false">Alokasi Order</a>
            </li>
            @endif
            @endif
        </ul>

        <div class="card-body">
            <div class="tab-content" id="regu-tabs-tabContent">
                <div class="tab-pane fade show active" id="regu-tabs-produksi" role="tabpanel"
                    aria-labelledby="regu-tabs-produksi-tab">
@php
    $openMetode = false;
    if (env('NET_SUBSIDIARY', 'EBA') == 'EBA') {
        $openMetode = true;
    } else if (env('NET_SUBSIDIARY', 'CGL') == 'CGL') {
        if ($regu == 'Boneless') {
            $openMetode = true;
        }
    }
@endphp
                    @if ($openMetode == true)
                    <div class="border p-2 mb-3">
                        Metode Produksi
                        <div class="row text-center radio-toolbar">
                            <div class="col">
                                <input type="radio" name="activity" value="productions" class="activity"
                                    id="productions" @if (Session::get('tabs')) {{ Session::get('tabs')=='full_production' ? 'checked' : '' }} @else checked @endif>
                                <label for="productions">Bahan Baku + Hasil Produksi</label>
                            </div>

                            <div class="col">
                                <input type="radio" name="activity" value="bahan_baku" class="activity" id="bahan_baku"
                                    @if (Session::get('tabs')) {{ Session::get('tabs')=='ambil_bb' ? 'checked' : '' }}
                                    @endif>
                                <label for="bahan_baku">Ambil Bahan Baku</label>
                            </div>

                            <div class="col">
                                <input type="radio" name="activity" value="proses_produksi" class="activity"
                                    id="proses_produksi">
                                <label for="proses_produksi">Hasil Produksi</label>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6 pr-md-1 mb-4" id="proses_bahan_baku">
                            <h5>Bahan Baku Produksi</h5>
                            <div id="ambil_bahanbaku"></div>
                        </div>
                        <div class="col-md-6 pl-md-1 mb-4" id="proses_hasil_produksi">
                            <h5>Hasil Produksi</h5>
                            <div id="finished_good"></div>
                            <div id="hasil_produksi"></div>
                        </div>
                    </div>

                    <div id="selesaikan"></div>

                    <h3 class="border-top pt-4">Produksi Harian</h3>

                    <div class="row">
                        <div class="col-md-4 col-sm-4 col-xs-6">
                            <div class="form-group">
                                Pencarian
                                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                    min="2023-01-01" @endif name="tanggal" class="form-control change-date"
                                    value="{{ $tanggal }}" id="tglhasil" placeholder="Cari...." autocomplete="off">
                                <input type="checkbox" id="show_selesaikan" class="mt-3"> 
                                <label for="show_selesaikan">Tampilkan Belum Diselesaikan</label>

                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4 col-xs-6">
                            <div class="form-group">
                                Pencarian
                                <input type="text" class="form-control change-filter-hasil-harian mb-3"
                                    autocomplete="off" id="text-search" name="search-table" value="" placeholder="Kata">
                                <input type="checkbox" id="search_selonjor"> 
                                <label for="search_selonjor">Selonjor</label>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4 col-xs-6">
                            <div class="form-group">
                                &nbsp &nbsp
                                <br>
                                <button name="refresh" id="refresh" class="btn btn-success btn-sm mb-1"><span
                                        class="fa fa-refresh"></span>&nbsp Refresh Halaman Pencarian</button>
                            </div>
                        </div>
                    </div>

                    <div id="loading-harian" class="text-center mb-2">
                        <img src="{{ asset('loading.gif') }}" style="width: 30px">
                    </div>
                    <div id="hasil_harian"></div>
                </div>

                {{-- @if (env('NET_SUBSIDIARY', 'EBA') == 'EBA') --}}
                <div class="tab-pane fade " id="regu-tabs-request" role="tabpanel"
                    aria-labelledby="regu-tabs-request-tab">
                    <div class="row">
                        <div class="col pr-1">
                            <label for="tanggal_request">Tanggal Kirim</label>
                            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                min="2023-01-01" @endif id="tanggal_request" class="form-control" value="{{ date("Y-m-d", strtotime("tomorrow")) }}">
                        </div>
                        <div class="col pl-1">
                            <label for="cari_request">Cari Data</label>
                            <input type="text" placeholder="Cari..." autocomplete="off" id="cari_request"
                                class="form-control">
                        </div>
                    </div>
                    <div class="mt-2">
                        <input type="checkbox" id="menunggu"> <label for="menunggu">Pending Fulfillment</label> &nbsp
                        <input type="radio" id="input-fresh" name="jenis" checked> <label
                            for="input-fresh">Fresh</label> &nbsp
                        <input type="radio" id="input-frozen" name="jenis"> <label for="input-frozen">Frozen</label>
                        &nbsp
                        <input type="radio" id="input-semua" name="jenis"> <label for="input-semua">Semua</label> &nbsp
                    </div>

                    {{-- <h5 id="loading_request" style="display: none"><i class="fa fa-refresh fa-spin"></i>
                        Loading.....</h5> --}}
                    <div id="data_request"></div>
                    <div id="loading_request" class="text-center mb-2">
                        <img src="{{ asset('loading.gif') }}" style="width: 30px">
                    </div>
                </div>

                <div class="tab-pane fade" id="regu-tabs-sameday" role="tabpanel"
                    aria-labelledby="regu-tabs-sameday-tab">
                    <div id="sameday_view"></div>
                    <div id="spinersameday" class="text-center mb-2">
                        <img src="{{ asset('loading.gif') }}" style="width: 30px;">
                    </div>
                </div>

                <div class="tab-pane fade " id="regu-tabs-parkingorder" role="tabpanel"
                    aria-labelledby="regu-tabs-parkingorder-tab">
                    <h6>Filter</h6>
                    <div class="row">
                        <div class="col pr-1">
                            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                min="2023-01-01" @endif value="{{ date("Y-m-d", strtotime('tomorrow')) }}"
                                id="tanggal_mulai_parking_order" class="form-control">
                        </div>
                        <div class="col pl-1">
                            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                min="2023-01-01" @endif value="{{ date("Y-m-d", strtotime('tomorrow')) }}"
                                id="tanggal_akhir_parking_order" class="form-control">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col pr-1">
                            <label for="filter_parking_order">Pencarian</label>
                            <input type="text" id="filter_parking_order" class="form-control"
                                placeholder="Cari item...">
                        </div>
                        <div class="col pl-1" id="customer_parking_order">

                        </div>
                    </div>
                    <hr>
                    <div id="parking_orders"></div>
                    <div id="spinerparkingorder" class="text-center mb-2">
                        <img src="{{ asset('loading.gif') }}" style="width: 30px">
                    </div>
                </div>
                {{-- @endif --}}

                <div class="tab-pane fade " id="regu-tabs-summary" role="tabpanel"
                    aria-labelledby="regu-tabs-summary-tab">
                    <div id="list_summary"></div>
                    <div id="spinersummaryglobal" class="text-center mb-2">
                        <img src="{{ asset('loading.gif') }}" style="width: 30px">
                    </div>
                </div>

                <div class="tab-pane fade " id="regu-tabs-summaryprod" role="tabpanel"
                    aria-labelledby="regu-tabs-summaryprod-tab">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="tanggalprod">Filter Tanggal Produksi</label>
                                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                    min="2023-01-01" @endif name="tanggalprod" class="form-control tanggal"
                                    id="tanggalprod" value="{{ date('Y-m-d') }}" autocomplete="off">
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="tanggalend">&nbsp;</label>
                                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                    min="2023-01-01" @endif name="tanggalend" class="form-control tanggal"
                                    id="tanggalend" value="{{ date('Y-m-d') }}" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <span class="ml-3">
                            <input type="radio" id="filtersemua" name="filterresult" value="semua">
                            <label for="filtersemua">Semua</label>
                        </span>
                        <span class="ml-3">
                            <input type="radio" id="filterabf" name="filterresult" value="filterabf">
                            <label for="filterabf">ABF</label>
                            {{--
                            <input type="checkbox" id="filterabf">
                            <label for="filterabf">ABF</label>
                            --}}
                        </span>
                        <span class="ml-3">
                            <input type="radio" id="filterekspedisi" name="filterresult" value="filterekspedisi">
                            <label for="filterekspedisi">Ekspedisi</label>
                            {{--
                            <input type="checkbox" id="filterekspedisi">
                            <label for="filterekspedisi">Ekspedisi</label>
                            --}}
                        </span>
                        <span class="ml-3">
                            <input type="radio" id="filterchiller" name="filterresult" value="filterchiller">
                            <label for="filterchiller">Chiller</label>
                            {{--
                            <input type="checkbox" id="filterchiller">
                            <label for="filterchiller">Chiller</label>
                            --}}
                        </span>
                    </div>
                    <input type="text" class="form-control change-filter-prod mb-3" autocomplete="off"
                        id="search-filter-prod" name="search-table" value="" placeholder="Kata Kunci">
                    <div class="form-group">
                        <button class="form-control btn btn-green btn-block"
                            id="submitCariSummaryHasilProduksi">Cari</button>
                    </div>
                    <div id="list_summaryprod">
                    </div>
                </div>

                {{-- ACTUAL ORDER --}}
                <div class="tab-pane fade " id="regu-tabs-actualorder" role="tabpanel"
                    aria-labelledby="regu-tabs-actualorder-tab">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="tanggalactualorder">Filter Tanggal</label>
                                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                    min="2023-01-01" @endif name="tanggalactualorder" class="form-control"
                                    id="tanggalactualorder" value="{{ date('Y-m-d') }}" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div id="spinnerActualOrder" class="text-center mb-2">
                        <img src="{{ asset('loading.gif') }}" style="width: 30px">
                    </div>
                    <div id="actual_order">
                    </div>
                </div>

                {{-- END ACTUAL ORDER --}}

                {{-- ORDER PRODUKSI --}}
                <div class="tab-pane fade " id="regu-tabs-orderproduksi" role="tabpanel"
                    aria-labelledby="regu-tabs-orderproduksi-tab">
                    <div class="row">
                        <div class="col">
                            <div class="section">
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
                                            <input class="form-control form-control-sm cari_order" id="cari_orders" type="text" placeholder="cari customer atau item" style="margin-bottom: 5px;">
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <section class="panel">
                        <div class="card-body">
                            <div id="loading-order-produksi" class="text-center" style="display: none">
                                <img src="{{ asset('loading.gif') }}" width="20px">
                            </div>
                            <div id="data-order-produksi"></div>
                        </div>
                    </section>
                </div>

                {{-- END ORDER PRODUKSI --}}


                {{-- STOCK BY ITEM GUDANG --}}
                <div class="tab-pane fade " id="regu-tabs-stock" role="tabpanel" aria-labelledby="regu-tabs-stock-tab">
                    {{-- <div class="row mb-3">
                        <div class="col">
                            <label for="search-filter-stockbyitem">Filter Nama Item</label>
                            <input type="text" class="form-control" autocomplete="off" id="search-filter-stockbyitem"
                                name="search" placeholder="Nama Item">
                        </div>
                    </div> --}}
                    <div id="stockByItemGudang"></div>
                    <div id="spinerstockbyitem" class="text-center mb-2">
                        <img src="{{ asset('loading.gif') }}" style="width: 30px">
                    </div>

                </div>
                {{-- END STOCK BY ITEM GUDANG --}}



                <div class="tab-pane fade" id="regu-tabs-order" role="tabpanel" aria-labelledby="regu-tabs-order-tab">
                    <div class="row mb-3">
                        <div class="col-6">
                            <label for="tanggal">Filter Tanggal Produksi</label>
                            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                min="2023-01-01" @endif name="tanggal" class="form-control" id="tanggal"
                                value="{{ $tanggal }}" autocomplete="off">
                        </div>
                        <div class="col-6">
                            <label for="search-filter">Filter Nama Customer</label>
                            <input type="text" class="form-control change-filter" autocomplete="off" id="search-filter"
                                name="search" value="{{ $search ?? '' }}" placeholder="Kata">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-4">
                            <button class="btn btn-outline-primary btn-block" id="order_semua">Semua</button>
                        </div>
                        <div class="col-4">
                            <button class="btn btn-outline-danger btn-block" id="order_pending">Pending</button>
                        </div>
                        <div class="col-4">
                            <button class="btn btn-outline-success btn-block" id="order_selesai">Selesai</button>
                        </div>
                    </div>

                    <div id="list_order"></div>
                    <div id="loading-list-order" class="text-center mb-2">
                        <img src="{{ asset('loading.gif') }}" style="width: 30px">
                    </div>
                </div>
                @if (env('NET_SUBSIDIARY', 'CGL') == 'CGL')
                <div class="tab-pane fade " id="regu-tabs-alokasi" role="tabpanel"
                    aria-labelledby="regu-tabs-alokasi-tab">
                    <div id="list_alokasi"></div>
                    <div id="spineralokasi" class="text-center mb-2">
                        <img src="{{ asset('loading.gif') }}" style="width: 30px">
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

@endsection

@section('footer')

@if ($openMetode == true)
<script>
    $(".activity").on('click', function() {
        console.log('HALOOO')
    var activity    =   $(".activity:checked").val() ;

    if (activity == 'productions') {
        $("#type_input").val('full_production') ;
        $("#proses_bahan_baku").attr('class', 'col-md-6 pr-md-1 mb-4') ;
        $("#proses_bahan_baku").attr('style', 'display:block') ;
        $("#proses_hasil_produksi").attr('class', 'col-md-6 pl-md-1 mb-4') ;
        $("#proses_hasil_produksi").attr('style', 'display:block') ;
    }
    if (activity == 'bahan_baku') {
        $("#type_input").val('ambil_bb') ;
        $("#proses_bahan_baku").attr('class', 'col-12 mb-4') ;
        $("#proses_bahan_baku").attr('style', 'display:block') ;
        $("#proses_hasil_produksi").attr('style', 'display:none') ;
    }
    if (activity == 'proses_produksi') {
        $("#proses_bahan_baku").attr('style', 'display:none') ;
        $("#proses_hasil_produksi").attr('class', 'col-12 mb-4') ;
        $("#proses_hasil_produksi").attr('style', 'display:block') ;
    }
});

$(function() {
    var activity    =   $(".activity:checked").val() ;

    if (activity == 'productions') {
        $("#type_input").val('full_production') ;
        $("#proses_bahan_baku").attr('class', 'col-md-6 pr-md-1 mb-4') ;
        $("#proses_bahan_baku").attr('style', 'display:block') ;
        $("#proses_hasil_produksi").attr('class', 'col-md-6 pl-md-1 mb-4') ;
        $("#proses_hasil_produksi").attr('style', 'display:block') ;
    }
    if (activity == 'bahan_baku') {
        $("#type_input").val('ambil_bb') ;
        $("#proses_bahan_baku").attr('class', 'col-12 mb-4') ;
        $("#proses_bahan_baku").attr('style', 'display:block') ;
        $("#proses_hasil_produksi").attr('style', 'display:none') ;
    }
    if (activity == 'proses_produksi') {
        $("#proses_bahan_baku").attr('style', 'display:none') ;
        $("#proses_hasil_produksi").attr('class', 'col-12 mb-4') ;
        $("#proses_hasil_produksi").attr('style', 'display:block') ;
    }
})

</script>
@endif
<script>
    var hash = window.location.hash.substr(1);
        var href = window.location.href;
        var regu = getUrlVars()["kategori"];

        defaultPage();

        function defaultPage() {
            if (hash == undefined || hash == "") {
                hash = "regu-tabs-produksi";
            }if (regu == "meyer" || regu == "admin-produksi") {
                hash = "regu-tabs-request";
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

        // GET VALUE SEGMENT URL
        function getUrlVars()
        {
            var vars = [], hash;
            var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
            for(var i = 0; i < hashes.length; i++)
            {
                hash = hashes[i].split('=');
                vars.push(hash[0]);
                vars[hash[0]] = hash[1];
            }
            return vars;
        }
        // END GET VALUE

        var tanggal     =   "";
        var kategori    =   "";
        var selesaikan  =   "";

        // TAB PRODUKSI
        var filterTanggalTimeout        = null;
        var filterPencarianTimeout      = null;

        $('#tglhasil').on('change', function() {
            $('#loading-harian').show();
            if (filterTanggalTimeout != null) {
                clearTimeout(filterTanggalTimeout);
            }
            filterTanggalTimeout = setTimeout(function() {
                filterTanggalTimeout = null;
                //ajax code
                tanggal     =   $('#tglhasil').val();
                selonjor    =   $('#search_selonjor:checked').val();
                selesaikan  =   $('#show_selesaikan:checked').val();
                kategori    =   "{{ $request->kategori }}";
                cari        =   encodeURIComponent($('#text-search').val());
                $("#hasil_harian").load("{{ route('regu.index', ['key' => 'hasil_harian']) }}&kat=" + kategori + "&tanggal=" + tanggal + "&selesaikan=" + selesaikan + "&cari=" + cari + "&selonjor=" + selonjor, function() {
                    $('#loading-harian').hide();
                });
            }, 1000);
        })

        $('#text-search').bind('keyup change', function(ev) {
            $('body').removeHighlight();
            $('#loading-harian').show();
            if (filterPencarianTimeout != null) {
                clearTimeout(filterPencarianTimeout);
            }
            filterPencarianTimeout = setTimeout(function() {
                filterPencarianTimeout = null;
                tanggal         =   $('#tglhasil').val();
                selonjor        =   $('#search_selonjor:checked').val();
                selesaikan      =   $('#show_selesaikan:checked').val();
                kategori        =   "{{ $request->kategori }}";
                let cari        =   encodeURIComponent($('#text-search').val()) ?? '';
                var searchTerm  =   encodeURIComponent($('#text-search').val()) ?? '';
                $("#hasil_harian").load("{{ route('regu.index', ['key' => 'hasil_harian']) }}&kat=" + kategori + "&tanggal=" + tanggal + "&selesaikan=" + selesaikan + "&cari=" + cari + "&selonjor=" + selonjor, function() {
                    $('#loading-harian').hide();
                    if ( searchTerm ) {
                        $('body').highlight( searchTerm );
                    }
                });
            }, 1000)
        });

        $("#refresh").on('click', function() {
            $('body').removeHighlight();
            $('#loading-harian').show();
            if (filterPencarianTimeout != null) {
                clearTimeout(filterPencarianTimeout);
            }
            filterPencarianTimeout = setTimeout(function() {
                filterPencarianTimeout = null;
                tanggal         =   $('#tglhasil').val();
                selonjor        =   $('#search_selonjor:checked').val();
                selesaikan      =   $('#show_selesaikan:checked').val();
                kategori        =   "{{ $request->kategori }}";
                let cari        =   encodeURIComponent($('#text-search').val()) ?? '';
                var searchTerm  =   encodeURIComponent($('#text-search').val()) ?? '';
                $("#hasil_harian").load("{{ route('regu.index', ['key' => 'hasil_harian']) }}&kat=" + kategori + "&tanggal=" + tanggal + "&selesaikan=" + selesaikan + "&cari=" + cari + "&selonjor=" + selonjor, function() {
                    $('#loading-harian').hide();
                    if ( searchTerm ) {
                        $('body').highlight( searchTerm );
                    }
                });
            }, 1000)
        })

        $('#show_selesaikan').on('change', function() {
            $('#loading-harian').show();
            tanggal     =   $('#tglhasil').val();
            selesaikan  =   $('#show_selesaikan:checked').val();
            selonjor    =   $('#search_selonjor:checked').val();
            kategori    =   "{{ $request->kategori }}";
            cari        =   encodeURIComponent($('#text-search').val());

            $("#hasil_harian").load("{{ route('regu.index', ['key' => 'hasil_harian']) }}&kat=" + kategori + "&tanggal=" + tanggal + "&selesaikan=" + selesaikan + "&cari=" + cari + "&selonjor=" + selonjor, function() {
                $('#loading-harian').hide();
            });

        })

        $('#search_selonjor').on('change', function() {
            $('#loading-harian').show();
            tanggal     =   $('#tglhasil').val();
            selonjor    =   $('#search_selonjor:checked').val();
            selesaikan  =   $('#show_selesaikan:checked').val();
            kategori    =   "{{ $request->kategori }}";
            cari        =   encodeURIComponent($('#text-search').val());

            $("#hasil_harian").load("{{ route('regu.index', ['key' => 'hasil_harian']) }}&kat=" + kategori + "&tanggal=" + tanggal + "&selesaikan=" + selesaikan + "&cari=" + cari + "&selonjor=" + selonjor, function() {
                $('#loading-harian').hide();
            });

        })


        load_all();
        function load_all() {
            tanggal     =   $('#tglhasil').val();
            selesaikan  =   $('#show_selesaikan:checked').val();
            $("#ambil_bahanbaku").load("{{ route('regu.index', ['key' => 'bahan_baku']) }}&kat={{ $request->kategori }}");
            $("#hasil_produksi").load("{{ route('regu.index', ['key' => 'hasil_produksi']) }}&kat={{ $request->kategori }}");
            $("#finished_good").load("{{ route('regu.index', ['key' => 'finished_good']) }}&kat={{ $request->kategori }}");
            $("#selesaikan").load("{{ route('regu.index', ['key' => 'selesaikan']) }}&kat={{ $request->kategori }}");
            $("#hasil_harian").load("{{ route('regu.index', ['key' => 'hasil_harian']) }}&kat={{ $request->kategori }}" +  "&tanggal=" + tanggal + "&selesaikan=" + selesaikan, function() {
                $('#loading-harian').hide();
            });
        }


        $(document).ready(function() {
            $(document).on('click', '.hapus_bb', function() {
                var row_id = $(this).data('id');

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: "{{ route('regu.delete') }}",
                    method: "DELETE",
                    data: {
                        row_id: row_id,
                        key: 'bahan_baku'
                    },
                    success: function(data) {
                        load_all();
                        showNotif('Bahan baku diambil berhasil dihapus');
                    }
                });
            })
        });

        $(document).ready(function () {
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
        });

        $(document).on('click', '.input_freestock', function() {
            var plastik         =   $('#plastik').val();
            var jumlah_plastik  =   $('#jumlah_plastik').val();
            var parting         =   $('#part').val();
            var item            =   $('#itemfree').val();
            var berat           =   $('#berat').val();
            var jumlah          =   $('#jumlah').val();

            var itemtunggir     =   $('#itemtunggir').val();
            var berattunggir    =   $('#berattunggir').val();
            var jumlahtunggir   =   $('#jumlahtunggir').val();

            var itemmaras       =   $('#itemmaras').val();
            var beratmaras      =   $('#beratmaras').val();
            var jumlahmaras     =   $('#jumlahmaras').val();

            var itemlemak       =   $('#itemlemak').val();
            var beratlemak      =   $('#beratlemak').val();
            var jumlahlemak     =   $('#jumlahlemak').val();

            var sub_item        =   $('#sub_item').val();
            var customer        =   $('#customer').val();

            var unit            =   $('#unit').val();
            var jumlah_keranjang=   $('#jumlah_keranjang').val();
            var kode_produksi   =   $('#kode_produksi').val();

            var tujuan_produksi =   $('input[name="tujuan_produksi"]:checked').val();
            var selonjor        =   $("#selonjor:checked").val() ;

            var req_kat         =  "{{ $request->kategori }}";

            var bumbu_id        =  $('#bumbu_id').val();
            var bumbu_berat     =  $('#bumbu_berat').val();

            var additional = [];
            $('.additional').each(function() {
                if ($(this).is(":checked")) {
                    additional.push($(this).val());
                }
            });

            if(req_kat === 'boneless'){
                if(item === undefined || item === ''){
                    showAlert('Jenis Boneless wajib dipilih');
                }else if(jumlah === '' || jumlah === undefined){
                    showAlert('Qty wajib diisi');
                    return false;
                }
            }

            if (item == '') {
                showAlert('Item wajib dipilih');
            }else {

                if (tujuan_produksi == 1) {
                    if (plastik != 'Curah') {
                        if (jumlah_plastik > 0) {
                            var next = 'TRUE';
                        }
                    } else {
                        var next = 'TRUE';
                    }
                } 
                else {
                    if (plastik == 'Curah') {
                        var next = 'TRUE';
                    } else {
                        if (jumlah_plastik > 0) {
                            var next = 'TRUE';
                        }
                    }
                }


                if (next != 'TRUE') {
                    showAlert('Lengkapi data plastik');
                } else {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $.ajax({
                        url: "{{ route('regu.store') }}",
                        method: "POST",
                        data: {
                            jenis: "{{ $request->kategori }}",
                            item: item,
                            berat: berat,
                            jumlah: jumlah,
                            itemtunggir: itemtunggir,
                            berattunggir: berattunggir,
                            jumlahtunggir: jumlahtunggir,
                            itemlemak: itemlemak,
                            beratlemak: beratlemak,
                            jumlahlemak: jumlahlemak,
                            itemmaras: itemmaras,
                            beratmaras: beratmaras,
                            jumlahmaras: jumlahmaras,
                            parting: parting,
                            plastik: plastik,
                            jumlah_plastik: jumlah_plastik,
                            additional: additional,
                            tujuan_produksi: tujuan_produksi,
                            sub_item: sub_item,
                            customer: customer,
                            selonjor: selonjor,
                            unit: unit,
                            jumlah_keranjang: jumlah_keranjang,
                            kode_produksi: kode_produksi,
                            bumbu_berat,
                            bumbu_id
                        },
                        success: function(data) {
                            if (data.status == 400) {
                                showAlert(data.msg);
                            } else {
                                $('.modal-backdrop').remove();
                                $('body').removeClass('modal-open');
                                load_all();
                                showNotif('Produksi berhasil ditambahkan');
                            }
                        }
                    });
                }

            }

        });

        $(document).on('click', '.hapus_produksi', function() {
            var row_id = $(this).data('id');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('regu.delete') }}",
                method: "DELETE",
                data: {
                    row_id: row_id,
                    key: 'hapus_produksi'
                },
                success: function(data) {
                    load_all();
                    showNotif('Hasil produksi berhasil dihapus');
                }
            });
        });

        $(document).on('click', '.selesaikan', function() {
            var beratprod   = $('#beratprod').val();
            var beratbb     = $('#beratbb').val();
            var total       = beratprod / beratbb * 100;
            // var netsuite_send   = "TRUE";
            var arr_namabb  = [];
            var arr_namahp  = [];

            $('#ambil_bahanbaku .tabel-bb .id_bb').each(function () {
                    arr_namabb.push($(this).val());
            });

            $('#finished_good .tabel-hp .id_hp').each(function () {
                arr_namahp.push($(this).val());
            });


            console.log('bahan-baku',arr_namabb);
            console.log('hasil-produksi',arr_namahp);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // if($('#netsuite_send').get(0).checked) {
            //     // something when checked
            //     netsuite_send = "TRUE";
            // } else {
            //     // something else when not
            //     netsuite_send = "FALSE";
            // }

            // console.log(netsuite_send)

            $.ajax({
                url: "{{ route('regu.store') }}",
                method: "POST",
                data: {
                    key: 'selesaikan',
                    jenis: "{{ $request->kategori }}",
                    // netsuite_send: netsuite_send,
                    idbb : arr_namabb,
                    idhp : arr_namahp,
                },
                success: function(data) {

                    console.log(data)

                    if (data.status == 400) {
                        showAlert(data.msg);
                    } else {
                        showNotif('Produksi berhasil disimpan');
                        window.location.reload("{{ route('regu.index') }}");
                    }
                }
            });
        })

        $(document).ready(function() {
            $(document).on('click', '.approved', function() {
                var id = $(this).data('id');

                $(".approved").hide();

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: "{{ route('regu.store') }}",
                    method: "POST",
                    data: {
                        key: 'selesaikan',
                        jenis: "{{ $request->kategori }}",
                        cast: 'approve',
                        id: id,
                    },
                    success: function(data) {
                        if (data.status == 'Success') {
                            showNotif('Produksi berhasil diselesaikan');
                            load_all();
                        } else {
                            showAlert(data.message);
                            $('.approved').show();
                        }
                        // console.log(data)
                    }
                });
            })
        });

        $(document).on('click', '.removed', function() {
            var id = $(this).data('id');

            if(confirm("Batalkan inputan produksi? setelah dibatalkan tidak bisa dikembalikan lagi")){
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: "{{ route('regu.store') }}",
                    method: "POST",
                    data: {
                        key: 'selesaikan',
                        jenis: "{{ $request->kategori }}",
                        cast: 'removed',
                        id: id,
                    },
                    success: function(data) {
                        console.log(data);
                        if (data.status == 400) {
                            showAlert(data.msg);
                        } else {
                            load_all();
                            showNotif('Produksi berhasil dibatalkan');
                        }
                    },
                    error: function(xhr, status, error) {
                        // alert(xhr.responseJSON.message);
                        alert(xhr.responseText);
                    }
                });
            }else{
                    showAlert('Cancel');
            }

        });

        $(document).on('click', '.edit_regu', function() {
            var id = $(this).data('id');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{ route('regu.store') }}",
                method: "POST",
                data: {
                    key: 'selesaikan',
                    jenis: "{{ $request->kategori }}",
                    cast: 'back',
                    id: id,
                },
                success: function(data) {
                    load_all();
                }
            });
        });
        //END OF TAB PRODUKSI



        // TAB INPUT BY ORDER
        if(hash === "regu-tabs-request"){
            reloadDataOrder()
        }
        $("#regu-tabs-request-tab").on('click', function(){
            reloadDataOrder()
        });

        $("#input-frozen,#input-fresh,#input-semua,#menunggu,#tanggal_request").on('change', function() {
            reloadDataOrder();
        });
        $("#cari_request").on('keyup', function() {
            setTimeout(() =>{
                reloadDataOrder();
            },1200)
        });

        function reloadDataOrder(){
            $("#loading_request").show()
            var fresh       =   $("#input-fresh:checked").val();
            var frozen      =   $("#input-frozen:checked").val();
            var semua       =   $("#input-semua:checked").val();
            var menunggu    =   $("#menunggu:checked").val();
            var tanggal     =   $("#tanggal_request").val();
            var cari        =   encodeURIComponent($("#cari_request").val());

            var load_url = "{{ route('regu.request_order', ['key' => 'view']) }}&regu={{ $request->kategori }}&tanggal=" + tanggal + "&cari=" + cari + "&menunggu=" + menunggu + "&fresh=" + fresh + "&frozen=" + frozen + "&semua=" + semua;
            $("#data_request").load(load_url, function() {
                $("#loading_request").hide()
            });
        }
        // END OF TAB INPUT BY ORDER


        // TAB SAMEDAY
        if(hash === "regu-tabs-sameday"){
            loadDataSameday()
        }
        $("#regu-tabs-sameday-tab").on('click', function(){
            loadDataSameday()
        });
        function loadDataSameday(){
            $("#spinersameday").show()
            $("#sameday_view").load("{{ route('regu.index', ['key' => 'sameday']) }}&regu={{ $request->kategori }}", function(){
                $("#spinersameday").hide()
            }) ;
        }
        // END OF TAB SAMEDAY


        // TAB PARKING ORDER
        if(hash === "regu-tabs-parkingorder"){
            loadParkingOrders()
        }
        $("#regu-tabs-parkingorder-tab").on('click', function(){
            loadParkingOrders()
        });
        $("#tanggal_mulai_parking_order,#tanggal_akhir_parking_order").on('change', function(){
            loadParkingOrders()
        })
        $("#filter_parking_order").on('keyup', function(){
            loadParkingOrders()
        })

        function customer_parking_order(){
            loadParkingOrders()
        }

        loadParkingOrders()

        function loadParkingOrders(){
            $("#spinerparkingorder").show();
            let tanggal_mulai_parking_order                =   $("#tanggal_mulai_parking_order").val()
            let tanggal_akhir_parking_order                =   $("#tanggal_akhir_parking_order").val()
            let filter_parking_order                       =   encodeURIComponent($("#filter_parking_order").val())
            let customer_parking_order                     =   $("#filter_customer_parking_order").val() ?? ''

            $("#parking_orders").load("{{ route('regu.index', ['key' => 'parking_orders']) }}&kat={{ $request->kategori }}&tanggal_mulai_parking_order=" + tanggal_mulai_parking_order + "&tanggal_akhir_parking_order=" + tanggal_akhir_parking_order + "&filter_parking_order=" + filter_parking_order + "&customer_parking_order=" + customer_parking_order, function(){
                $("#spinerparkingorder").hide();
            })
            $("#customer_parking_order").load("{{ route('regu.index', ['key' => 'customer_parking_orders']) }}&kat={{ $request->kategori }}&tanggal_mulai_parking_order=" + tanggal_mulai_parking_order + "&tanggal_akhir_parking_order=" + tanggal_akhir_parking_order + "&filter_parking_order=" + filter_parking_order + "&customer_parking_order=" + customer_parking_order)
        }
        // END OF TAB PARKING ORDER


        // TAB SUMMARY GLOBAL
        if(hash === "regu-tabs-summary"){
            loadDataSummaryGlobal()
        }
        $("#regu-tabs-summary-tab").on('click', function(){
            loadDataSummaryGlobal()
        });

        function loadDataSummaryGlobal(){
            $("#spinersummaryglobal").show();
            var url_route_sumary            =   "{{ route('produksi.summary', ['regu' => $request->kategori]) }}&tanggal={{ $tanggal }}";
            $("#list_summary").load(url_route_sumary, function(){
                $("#spinersummaryglobal").hide()
            });
        }
        // END OF TAB SUMMARY GLOBAL


        if(hash === "regu-tabs-summaryprod"){
            loadDataSummaryProduksi()
        }
        $("#regu-tabs-summaryprod-tab").on('click', function(){
            loadDataSummaryProduksi()
        });


        $('#submitCariSummaryHasilProduksi').on('click', function() {
            // console.log($("#tanggalprod").val())
            // if (filterAbfTimeout != null) {
            //     clearTimeout(filterAbfTimeout);
            // }
            // filterAbfTimeout = setTimeout(function() {
            //     filterAbfTimeout = null;
                //ajax code
            // $('#loading-prod').show();
            $('#text-notif').html('Menunggu...');
            $('#topbar-notification').fadeIn();
            const cariSummaryProduksi   =   encodeURIComponent($('#search-filter-prod').val());
            let tanggal                 =   $("#tanggalprod").val();
            let tanggalend              =   $("#tanggalend").val();
            let filterresult            =   $('input[name="filterresult"]:checked').val();
            // let filterabf               =   $('#filterabf').is(':checked');
            // let filterchiller           =   $('#filterchiller').is(':checked');
            // let filterekspedisi         =   $('#filterekspedisi').is(':checked');
            // url_route_sumary_prod   =   "{{ route('produksi.summaryprod', ['regu' => $regu]) }}_" + tanggal + tanggalend;
            // url_route_sumary_prod          =   "{{ route('produksi.summaryprod', ['regu' => $regu]) }}&tanggal=" + tanggal +"&tanggalend="+ tanggalend+"&filterabf="+filterabf+"&filterekspedisi="+filterekspedisi+"&filterchiller="+filterchiller + "&cariSummaryProduksi=" + cariSummaryProduksi;
            url_route_sumary_prod          =   "{{ route('produksi.summaryprod', ['regu' => $regu]) }}&tanggal=" + tanggal +"&tanggalend="+ tanggalend+"&filterresult="+filterresult+ "&cariSummaryProduksi=" + cariSummaryProduksi;
            $("#list_summaryprod").load(url_route_sumary_prod, function() {
                $('#topbar-notification').fadeOut()
            });
        });

        // $('#submitCariSummaryHasilProduksi').on('click', function() {
        //     console.log($("#tanggalprod").val())
            // if (filterAbfTimeout != null) {
            //     clearTimeout(filterAbfTimeout);
            // }
            // filterAbfTimeout = setTimeout(function() {
            //     filterAbfTimeout = null;
            //     //ajax code
            //     $('#loading-prod').show();
            //     let tanggal                 =   $("#tanggalprod").val();
            //     let tanggalend              =   $("#tanggalend").val();
            //     let filterabf               =   $('#filterabf').is(':checked');
            //     let filterchiller           =   $('#filterchiller').is(':checked');
            //     let filterekspedisi         =   $('#filterekspedisi').is(':checked');
            //     // url_route_sumary_prod   =   "{{ route('produksi.summaryprod', ['regu' => $regu]) }}_" + tanggal + tanggalend;
            //     url_route_sumary_prod          =   "{{ route('produksi.summaryprod', ['regu' => $regu]) }}&tanggal=" + tanggal +"&tanggalend="+ tanggalend+"&filterabf="+filterabf+"&filterekspedisi="+filterekspedisi+"&filterchiller="+filterchiller;
            //     $("#list_summaryprod").load(url_route_sumary_prod, function() {
            //         $('#loading-prod').hide();
            //     });
            // }, 1000);
        // })

        function loadDataSummaryProduksi(){
            $("#loading-prod").show()
            var url_route_sumary_prod       =   "{{ route('produksi.summaryprod', ['regu' => $request->kategori]) }}&tanggal={{ $tanggal }}&tanggalend={{ $tanggalend }}";
            $("#list_summaryprod").load(url_route_sumary_prod, function() {
                $('#loading-prod').hide();
            });
        }


        //TAB LIST ORDER
        if(hash === "regu-tabs-order"){
            loadDataListOrder()
        }
        $("#regu-tabs-listorder-tab").on('click', function(){
            loadDataListOrder()
        });

        $('#tanggal').on('change', function() {
            $("#loading-list-order").show()
            var tanggal                 =   $(this).val();
            var url_route_order_produksi=   "{{ route('produksi.salesorder', ['regu' => $regu]) }}_{{ $tanggal }}";
            url_route_order_produksi    =   "{{ route('produksi.salesorder', ['regu' => $regu]) }}_" + tanggal;

            $("#list_order").load(url_route_order_produksi, function(){
                $('#loading-list-order').hide()
            });
        })

        var searchFilterTimeout = null;

        $('#search-filter').on('keyup', function() {
            $("#loading-list-order").show()
            var tanggal                     =   $('#tanggal').val();
            var search                      =   encodeURIComponent($(this).val());
            var url_route_order_produksi    =   "{{ route('produksi.salesorder', ['regu' => $regu]) }}_{{ $tanggal }}";
            if (searchFilterTimeout != null) {
                clearTimeout(searchFilterTimeout);
            }
            searchFilterTimeout = setTimeout(function() {
                searchFilterTimeout = null;
                //ajax code
                url_route_order_produksi    = "{{ route('produksi.salesorder', ['regu' => $regu]) }}_" + tanggal + '_' + search;
                $("#list_order").load(url_route_order_produksi, function(){
                    $('#loading-list-order').hide()
                });
            }, 1000);
        });

        $("#order_semua").on('click', function() {
            $("#loading-list-order").show()
            var tanggal =   $('#tanggal').val();
            var search  =   encodeURIComponent($(this).val());
            $("#list_order").load("{{ route('produksi.salesorder', ['regu' => $regu]) }}_" + tanggal + '_' + search + "&jenis=semua", function(){
                $('#loading-list-order').hide()
            });
        });

        $("#order_pending").on('click', function() {
            $("#loading-list-order").show()
            var tanggal =   $('#tanggal').val();
            var search  =   encodeURIComponent($(this).val());
            $("#list_order").load("{{ route('produksi.salesorder', ['regu' => $regu]) }}_" + tanggal + '_' + search + "&jenis=pending", function(){
                $('#loading-list-order').hide()
            });
        });

        $("#order_selesai").on('click', function() {
            $("#loading-list-order").show()
            var tanggal =   $('#tanggal').val();
            var search  =   encodeURIComponent($(this).val());
            $("#list_order").load("{{ route('produksi.salesorder', ['regu' => $regu]) }}_" + tanggal + '_' + search + "&jenis=selesai", function(){
                $('#loading-list-order').hide()
            });
        });

        function loadDataListOrder(){
            $("#loading-list-order").show()
            $("#list_order").load("{{ route('produksi.salesorder', ['regu' => $regu]) }}_", function(){
                $('#loading-list-order').hide()
            });
        }

        $(document).on('click', '.prosesorder', function() {
            var x_code      =   document.getElementsByClassName("xcode");
            var DB_qty      =   document.getElementsByClassName("qty");
            var DB_berat    =   document.getElementsByClassName("berat");
            var DB_nom      =   document.getElementsByClassName("item");
            var DB_order    =   document.getElementsByClassName("order");
            var regu        =   "{{ $regu }}";
            var qty         =   [];
            var item        =   [];
            var berat       =   [];
            var order       =   [];
            var xcode       =   [];
            for (var i = 0; i < DB_nom.length; ++i) {
                item.push(DB_nom[i].value);
                qty.push(DB_qty[i].value);
                berat.push(DB_berat[i].value);
                order.push(DB_order[i].value);
                xcode.push(x_code[i].value);
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{ route('produksi.prosesorder') }}",
                method: "POST",
                data: {
                    xcode   :   xcode,
                    qty     :   qty,
                    item    :   item,
                    berat   :   berat,
                    order   :   order,
                    regu    :   regu,
                },
                success: function(data) {
                    if (data.status == 400) {
                        showAlert(data.msg);
                    } else {
                        $('#qty').val('');
                        $('#berat').val('');
                        $("#list_order").load(url_route_order_produksi, function(){$('#loading-list-order').hide()});
                        showNotif('Berhasil Simpan');
                    }
                }
            });
        });
        //END OF TAB LIST ORDER

        //TAB ALOKASI ORDER
        if(hash === "regu-tabs-alokasi"){
            loadDataAlokasiOrder()
        }
        $("#regu-tabs-alokasi-tab").on('click', function(){
            loadDataAlokasiOrder()
        });
        function loadDataAlokasiOrder(){
            $("#spineralokasi").show()
            $("#list_alokasi").load("{{ route('produksi.alokasi', ['regu' => $request->kategori]) }}&tanggal={{ $tanggal }}", function(){
                $("#spineralokasi").hide()
            });
        }
        //END OF TAB ALOKASI ORDER


        // ACTUAL ORDER
        if(hash === "regu-tabs-actualorder"){
            loadDataActualOrder()
        }
        $("#regu-tabs-actualorder-tab").on('click', function(){
            loadDataActualOrder()
        });

        $("#tanggalactualorder").on('change', function(){
            loadDataActualOrder()
        })

        function loadDataActualOrder(){
            var tanggalActualOrder = $("#tanggalactualorder").val();
            $("#spinnerActualOrder").show()
            $("#actual_order").load("{{ route('produksi.alokasi', ['regu' => $request->kategori]) }}&tanggal=" + tanggalActualOrder + "&key=actualorder", function(){
                $("#spinnerActualOrder").hide()
            });
        }


        if (hash === "regu-tabs-stock") {
            stockByItemGudang()
        }

        $("#regu-tabs-stock-tab").on('click', function(){
            stockByItemGudang()
        });

        function stockByItemGudang(){

            // var tanggalActualOrder = $("#tanggalactualorder").val();
            $("#spinerstockbyitem").show()
            $("#stockByItemGudang").load("{{ route('regu.index', ['key' => 'stockbyitem']) }}&regu={{ $request->kategori }}", function(){
                $("#spinerstockbyitem").hide()
            });
        }

</script>

@endsection
