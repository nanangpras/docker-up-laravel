@extends('admin.layout.template')

@section('title', 'Chiller')

@section('content')
<div class="mb-4 text-center">
    <b>CHILLER</b>
</div>

<nav>
    <div class="nav nav-tabs" id="nav-tab" role="tablist">
        <a class="nav-item nav-link tab-link" id="tabs-stock-tab" data-toggle="pill" href="#tabs-stock" role="tab"
            aria-controls="tabs-stock" aria-selected="false">
            Stock
        </a>
        <a class="nav-item nav-link tab-link" id="tabs-keluar-tab" data-toggle="pill" href="#tabs-keluar" role="tab"
            aria-controls="tabs-keluar" aria-selected="true">
            Item Keluar
        </a>
        <a class="nav-item nav-link tab-link" id="tabs-masuk-tab" data-toggle="pill" href="#tabs-masuk" role="tab"
            aria-controls="tabs-masuk" aria-selected="false">
            Item Masuk
        </a>
    </div>
</nav>
<div class="card-body box-border">
    <div class="tab-content" id="tabs-three-tabContent">
        <div class="tab-pane fade" id="tabs-stock" role="tabpanel" aria-labelledby="tabs-stock-tab">

            <button type="button" class="btn btn-success float-right unduhstock">Unduh</button>

            <form method="get" action="{{ route('chiller.stock') }}" id="filter-form-submit">
                <div class="row mb-3">
                    <div class="col-lg-3 col-6">
                        <label for="stockmulai">Mulai</label>
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                            min="2023-01-01" @endif class="form-control change-filter mulai" id="stockmulai"
                            name="mulai" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-lg-3 col-6">
                        <label for="stocksampai">Sampai</label>
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                            min="2023-01-01" @endif class="form-control change-filter sampai" id="stocksampai"
                            name="sampai" value="{{ date('Y-m-d') }}">
                    </div>
                </div>
            </form>

            <div class="form-group">
                <label>
                    <input id="stock-kosong" type="checkbox" class="btn btn-blue" name="stock-kosong"> Stock Kosong
                </label>
            </div>
            <div id="spiner-chiller-stock" class="text-center mb-2">
                <img src="{{ asset('loading.gif') }}" style="width: 30px">
            </div>
            <div id="chiller-stock"></div>
        </div>
        <div class="tab-pane fade" id="tabs-keluar" role="tabpanel" aria-labelledby="tabs-keluar-tab">

            <button type="button" class="btn btn-success float-right unduhkeluar">Unduh</button>

            <form method="get" action="{{route('chiller.keluar')}}" id="filter-form-submit-keluar">
                <div class="row mb-3">
                    <div class="col-lg-2 col-6">
                        <label for="keluarmulai">Mulai</label>
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                            min="2023-01-01" @endif class="form-control change-filter-keluar" id='keluarmulai'
                            name="mulai" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-lg-2 col-6">
                        <label for="keluarsampai">Sampai</label>
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                            min="2023-01-01" @endif class="form-control change-filter-keluar" id='keluarsampai'
                            name="sampai" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-lg-2 col-6">
                        <div class="form-group">
                            <label for="regulist">Regu</label>
                            <select id="regulist" name="regu" class="form-control change-regu-keluar">
                                <option value="">- Semua -</option>
                                <option value="byproduct">Byproduct</option>
                                <option value="parting">Parting</option>
                                <option value="whole">Whole</option>
                                <option value="marinasi">Marinasi</option>
                                <option value="boneless">Boneless</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-2 col-6">
                        <div class="form-group">
                            <label for="asallist">Asal Tujuan</label>
                            <select id="asallist" name="asal_tujuan" class="form-control change-asal-tujuan-keluar">
                                <option value="">- Semua -</option>
                                <option value="freestok">Free Stok</option>
                                <option value="siapkirim">Siap Kirim</option>
                                <option value="jualsampingan">Jual Sampingan</option>
                                <option value="abf">Abf</option>
                                <option value="retur">Retur</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="form-group">
                            <label for="typelist">Asal Tujuan</label>
                            <select id="typelist" name="type" class="form-control change-type-keluar">
                                <option value="">- Semua -</option>
                                <option value="pengambilan-bahan-baku">Pengambilan Bahan Baku</option>
                                <option value="alokasi-order">Alokasi Order</option>
                                <option value="musnahkan">Musnahkan</option>
                            </select>
                        </div>
                    </div>
                </div>
            </form>
            <div id="spiner-chiller-out" class="text-center mb-2">
                <img src="{{ asset('loading.gif') }}" style="width: 30px">
            </div>
            <div id="chiller-keluar"></div>
        </div>
        <div class="tab-pane fade" id="tabs-masuk" role="tabpanel" aria-labelledby="tabs-masuk-tab">

            <button type="button" class="btn btn-success float-right unduhmasuk">Unduh</button>

            <form method="get" action="{{ route('chiller.masuk') }}" id="filter-form-submit-masuk">
                <div class="row mb-3">
                    <div class="col-lg-3 col-6">
                        <label for="masukmulai">Mulai</label>
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                            min="2023-01-01" @endif class="form-control change-filter-masuk" id='masukmulai'
                            name="mulai" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-lg-3 col-6">
                        <label for="masuksampai">Sampai</label>
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                            min="2023-01-01" @endif class="form-control change-filter-masuk" id='masuksampai'
                            name="sampai" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-lg-3 col-6">
                        <label for="masukcari">Pencarian</label>
                        <input type="text" class="form-control change-search-masuk" id='masukcari' name="masukcari"
                            placeholder="Cari..." autocomplete="off">
                    </div>
                </div>
            </form>
            <div id="spiner-chiller-in" class="text-center mb-2">
                <img src="{{ asset('loading.gif') }}" style="width: 30px">
            </div>
            <div id="chiller-masuk"></div>
        </div>
    </div>
</div>

@stop

@section('header')
<link rel="stylesheet" type="text/css" href="{{asset('')}}plugin/DataTables/datatables.min.css" />
@stop

@section('footer')
<script type="text/javascript" src="{{asset('')}}plugin/DataTables/datatables.min.js"></script>
<script>
    $('#spiner-chiller-stock').show();
        $('#spiner-chiller-in').show();
        $('#spiner-chiller-out').show();
        $('#chiller-masuk').load("{{route('chiller.masuk')}}", function() {
            $('#spiner-chiller-in').hide();
        });
        $('#chiller-stock').load("{{route('chiller.stock')}}", function() {
            $('#spiner-chiller-stock').hide();
        });
        $('#chiller-keluar').load("{{route('chiller.keluar')}}", function() {
            $('#spiner-chiller-out').hide();
        });

</script>

<script>
    var hash = window.location.hash.substr(1);
    var href = window.location.href;

    defaultPage();

    function defaultPage(){
        if (hash == undefined || hash == "") {
            hash = "tabs-stock";
        }

        $('#' + hash +'-tab').addClass('active').siblings().removeClass('active');
        $('#' + hash).addClass('active show').siblings().removeClass('active show');

    }


    $('.tab-link').click(function(e) {
        e.preventDefault();
        status = $(this).attr('aria-controls');
        window.location.hash = status;
        href = window.location.href;

    });

    $(document).ready(function() {
        $(document).on('click', '.unduhstock', function() {
            var stockmulai  =   $("#stockmulai").val() ;
            var stocksampai =   $("#stocksampai").val() ;

            location.href   =   "{{ route('chiller.stock', ['key' => 'unduh']) }}&mulai=" + stockmulai + "&sampai=" + stocksampai ;
        });
    });
</script>

<script>
    $(document).ready(function() {
    $(document).on('click', '.unduhkeluar', function() {

        var regu        =   $('#regulist').val();
        var asaltujuan  =   $('#asallist').val();
        var type        =   $('#typelist').val();
        var keluarmulai =   $("#keluarmulai").val() ;
        var keluarsampai=   $("#keluarsampai").val() ;

        location.href   =   "{{ route('chiller.keluar', ['key' => 'unduh']) }}&mulai=" + keluarmulai + "&sampai=" + keluarsampai + "&regulist=" + regulist + "&asallist=" + asallist + "&typelist=" + typelist;
    });
});
</script>

<script>
    $(document).ready(function() {
    $(document).on('click', '.unduhmasuk', function() {
        var masukmulai =   $("#masukmulai").val() ;
        var masuksampai=   $("#masuksampai").val() ;

        location.href   =   "{{ route('chiller.masuk', ['key' => 'unduh']) }}&mulai=" + masukmulai + "&sampai=" + masuksampai;
    });
});
</script>

<script>
    $(document).ready(function() {
        // $('#chillerkeluar').DataTable({
        //     "bInfo": false,
        //     responsive: true,
        //     scrollY:        500,
        //     scrollX:        true,
        //     scrollCollapse: true,
        //     paging:         false,
        // });

        // $('#chillermasuk').DataTable({
        //     "bInfo": false,
        //     responsive: true,
        //     scrollY:        500,
        //     scrollX:        true,
        //     scrollCollapse: true,
        //     paging:         false,
        // });

        $('a[data-toggle="pill"]').on('shown.bs.tab', function(e){
            $($.fn.dataTable.tables(true)).DataTable()
                .columns.adjust();
        });
    } );
</script>

<script>
    // filterChillerKeluar();

    //  $('#regulist').change(function () {
    //     filterChillerKeluar();
    // });

    // var regu        = $('#regulist').val();
    var url_url = "{{route('chiller.keluar')}}";
    $("#tabs-keluar-tab").on('click', function(){
        $('#spiner-chiller-out').show();
        setTimeout(() => {
            $('#spiner-chiller-out').hide();
            filterChillerKeluar()
        }, 500);
    });
    $('#filter-form-submit-keluar').on('submit', function(e){
        e.preventDefault();
        url_url = $(this).attr('action')+"?"+$(this).serialize();
        filterChillerKeluar();
    })

    $('.change-filter-keluar').on('change', function(){
        $('#filter-form-submit-keluar').submit();
        // filterChiller();
    })
    $('.change-regu-keluar').on('change', function(){
        $('#filter-form-submit-keluar').submit();
        // filterChiller();
    })
    $('.change-asal-tujuan-keluar').on('change', function(){
        $('#filter-form-submit-keluar').submit();
        // filterChiller();
    })
    $('.change-type-keluar').on('change', function(){
        $('#filter-form-submit-keluar').submit();
    })

    function filterChillerKeluar(){
        $('#spiner-chiller-out').show();
        $.ajax({
            url: url_url,
            method: "GET",
            success: function(response) {
                $('#spiner-chiller-out').hide();
                $('#chiller-keluar').html(response);
            }
        });
    }
</script>


<script>
    var url_masuk = "{{ route('chiller.masuk') }}";
     $("#tabs-masuk-tab").on('click', function(){
        $('#spiner-chiller-in').show();
        setTimeout(() => {
            $('#spiner-chiller-in').hide();
            filterChillerMasuk()
        }, 500);
    });
    $('#filter-form-submit-masuk').on('submit', function(e) {
        e.preventDefault();
        url_masuk = $(this).attr('action') + "?" + $(this).serialize();
        filterChillerMasuk();
    })

    $('.change-filter-masuk').on('change', function() {
        $('#filter-form-submit-masuk').submit();
    })

    $('.change-search-masuk').on('keyup', function() {
        $('#filter-form-submit-masuk').submit();
    })

    function filterChillerMasuk() {
        $('#spiner-chiller-in').show();
        $.ajax({
            url: url_masuk,
            method: "GET",
            success: function(response) {
                $('#spiner-chiller-in').hide();
                $('#chiller-masuk').html(response);
            }
        });
    }
</script>

<script>
    var url_stock = "{{ route('chiller.stock') }}";
     $("#tabs-stock-tab").on('click', function(){
        $('#spiner-chiller-in').show();
        setTimeout(() => {
            $('#spiner-chiller-stock').hide();
            filterChillerStock()
        }, 500);
    });
    $('#filter-form-submit').on('submit', function(e) {
        e.preventDefault();
        url_stock = $(this).attr('action') + "?" + $(this).serialize();
        filterChillerStock();
    })

    $('.change-filter').on('change', function() {
        $('#filter-form-submit').submit();
    })

    $('#stock-kosong').on('change', function() {
        filterChillerStock();
    })


    function filterChillerStock() {
        $('#spiner-chiller-stock').show();
        mulai = $('.mulai').val();
        sampai = $('.sampai').val();
        kosong = $('#stock-kosong').is(':checked');
        if(kosong==true){
            url_stock = "{{ url('admin/chiller-stock?mulai=') }}" + mulai + "&sampai=" +sampai+ "&kosong="+kosong;
        }else{
            url_stock = "{{ url('admin/chiller-stock?mulai=') }}" + mulai + "&sampai=" +sampai+ "&kosong=false";
        }

        $.ajax({
            url: url_stock,
            method: "GET",
            success: function(response) {
                $('#spiner-chiller-stock').hide();
                $('#chiller-stock').html(response);
            }
        });
    }
</script>
@stop