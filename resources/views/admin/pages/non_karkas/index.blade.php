@extends('admin.layout.template')

@section('title', 'Penerimaan Non LB')

@section('content')
<div class="my-4 text-center text-uppercase"><b>Penerimaan Item Receipt</b></div>

<section class="panel">
    <div class="card-body">
        {{-- <form action="{{ route('nonkarkas.index') }}" method="get"> --}}
            <div class="row">
                <div class="col-md-4 col-sm-4 col-6">
                    Tanggal Awal
                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                        min="2023-01-01" @endif class="form-control change-date" name="tanggalawal" id="tanggalawal"
                        value="{{ $tanggalawal }}" placeholder="Cari...">
                </div>
                <div class="col-md-4 col-sm-4 col-6">
                    Tanggal Akhir
                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                        min="2023-01-01" @endif class="form-control change-date" name="tanggalakhir" id="tanggalakhir"
                        value="{{ $tanggalakhir }}" placeholder="Cari...">
                </div>
                <div class="col-md-4 col-sm-4 col-6">
                    Cari No. PO
                    <input type="text" class="form-control" name="nomor_po" id="nomor_po" value=""
                        placeholder="Cari no PO ...">
                </div>
            </div>
            {{--
        </form> --}}
    </div>
</section>
<div class="card mt-4">
    <ul class="nav nav-tabs" id="tabs-tab" role="tablist">
        <li class="nav-item">
            <a class="nav-link tab-link" id="tabs-orders-tab" data-toggle="pill" href="#tabs-orders" role="tab"
                aria-controls="tabs-orders" aria-selected="true">
                Penerimaan
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link tab-link" id="tabs-pending-tab" data-toggle="pill" href="#tabs-pending" role="tab"
                aria-controls="tabs-pending" aria-selected="false">
                Timbang
            </a>
        </li>
    </ul>
    <div class="card-body">
        <div class="tab-content" id="tabs-tabContent">
            <div class="tab-pane fade " id="tabs-orders" role="tabpanel" aria-labelledby="tabs-orders-tab">
                {{-- @include('admin.pages.non_karkas.penerimaan') --}}
                <h5 class="text-center loading-nonlb"><i class="fa fa-refresh fa-spin"></i> Loading....</h5>
                <div id="daftar_nonlb"></div>

            </div>
            <div class="tab-pane fade" id="tabs-pending" role="tabpanel" aria-labelledby="tabs-pending">
                <h5 class="text-center loading-timbang"><i class="fa fa-refresh fa-spin"></i> Loading....</h5>
                {{-- @include('admin.pages.non_karkas.timbang') --}}
                <div id="timbang_nonkarkas"></div>
            </div>
        </div>
    </div>
</div>


@stop


@section('footer')
<script>
    var hash = window.location.hash.substr(1);
        var href = window.location.href;

        deafultPage();

        function deafultPage() {
            if (hash == undefined || hash == "") {
                hash = "tabs-orders";
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

        // ---------------------------------------------------------------- END DECLARE FIRST TAB -------------------------------- //

        // ------------------------------ FUNCTION KETIKA PAGE DI RELOAD / AWAL RELOAD  -------------------------------- //

        function loadPenerimaanNonLB() {
            if (window.location.hash.substr(1) == "tabs-orders") {
                loadNonLB();
            } else if (window.location.hash.substr(1) == "tabs-pending") {
                loadTimbangNonKarkas();
            } else {
                loadNonLB();
            }
        }

        loadPenerimaanNonLB();

        // ----------------------------- END SECTION --------------------------------------------------- //

        // ----------------------------- DECLARE VARIABLE & ON CHANGE ----------------------------- //

        var tanggalawal     = $('#tanggalawal').val();
        var tanggalakhir    = $('#tanggalakhir').val();
        var nomor_po         = $('#nomor_po').val();

        $('#tanggalawal,#tanggalakhir').change(function() {
            loadPenerimaanNonLB();
        });

        $('#nomor_po').keyup(function() {
            loadPenerimaanNonLB();
        });

        // ------------------------------- END ON CHANGE ------------------------------- //

        // -------------------------------- KETIKA DI ON KLIK PER TAB ---------------------------------------------------------------- //

        $('#tabs-orders-tab').on('click', function() {
            loadNonLB();
        });

        $('#tabs-pending-tab').on('click', function() {
            loadTimbangNonKarkas();
        });

        // ------------------------------- END SECTIONS -------------------------------- //


        // ------------------------------- FUNCTION PER TAB -------------------------------------------------------------------- //

        
        function loadNonLB() {

            setTimeout(function() {
                $(".loading-nonlb").attr('style', 'display: block');
                tanggalawal     = $('#tanggalawal').val();
                tanggalakhir    = $('#tanggalakhir').val();
                nomor_po         = $('#nomor_po').val();
                $("#daftar_nonlb").load("{{ route('nonkarkas.index', ['view' => 'non_lb']) }}&tanggalawal=" + tanggalawal + "&tanggalakhir=" +tanggalakhir +"&nomor_po="+nomor_po, function() {
                    $(".loading-nonlb").attr('style', 'display: none');
                });
            },500)

        }

        function loadTimbangNonKarkas() {
            setTimeout(function() {
                $(".loading-timbang").attr('style', 'display: block');
                tanggalawal     = $('#tanggalawal').val();
                tanggalakhir    = $('#tanggalakhir').val();
                nomor_po         = $('#nomor_po').val();
                $('#timbang_nonkarkas').load("{{ route('nonkarkas.index', ['view' => 'timbang']) }}&tanggalawal=" + tanggalawal + "&tanggalakhir=" + tanggalakhir +"&nomor_po="+nomor_po, function() {
                    $(".loading-timbang").attr('style', 'display: none');
                });
            },500)
        }

        // ------------------------------ END FUNCTION -------------------------------- //


</script>
@stop