@extends('admin.layout.template')

@section('title', 'Data Proses Grading')
@section('content')
<div class="my-4 text-center"><b>DATA PROSES GRADING</b></div>

<section class="panel">
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 col-sm-4 col-xs-12">
                <div class="form-group">
                    Tanggal Awal
                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                        min="2023-01-01" @endif id="tanggalawal" name="tanggalawal" class="form-control change-date"
                        value="{{ $tanggalawal }}" id="pencarian" placeholder="Cari...." autocomplete="off">
                </div>
            </div>
            <div class="col-md-4 col-sm-4 col-xs-12">
                <div class="form-group">
                    Tanggal Akhir
                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                        min="2023-01-01" @endif id="tanggalakhir" name="tanggalakhir" class="form-control change-date"
                        value="{{ $tanggalakhir }}" id="pencarian" placeholder="Cari...." autocomplete="off">
                </div>
            </div>
        </div>
    </div>
</section>

<section class="panel">
    <div class="card card-primary card-outline card-tabs">
        <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
            <li class="nav-item">
                <a class="nav-link tab-link" id="custom-tabs-three-polb-tab" data-toggle="pill"
                    href="#custom-tabs-three-polb" role="tab" aria-controls="custom-tabs-three-polb"
                    aria-selected="false">PO LB</a>
            </li>
            <li class="nav-item">
                <a class="nav-link tab-link" id="custom-tabs-three-nonlb-tab" data-toggle="pill"
                    href="#custom-tabs-three-nonlb" role="tab" aria-controls="custom-tabs-three-nonlb"
                    aria-selected="true">PO NON LB</a>
            </li>

        </ul>
        <div class="card-body">
            <div class="tab-content" id="custom-tabs-three-tabContent">
                <div class="tab-pane fade show active" id="custom-tabs-three-polb" role="tabpanel"
                    aria-labelledby="custom-tabs-three-polb-tab">
                    <div id="loading-grading-polb" style="display: none;" class="text-center"><i
                            class="fa fa-refresh fa-spin"></i> Loading....</div>
                    <div id="grading-po-lb"></div>
                </div>
                <div class="tab-pane fade show" id="custom-tabs-three-nonlb" role="tabpanel"
                    aria-labelledby="custom-tabs-three-nonlb-tab">
                    <div id="loading-grading-po-nonlb" style="display: none" class="text-center"><i
                            class="fa fa-refresh fa-spin"></i> Loading....</div>
                    <div id="grading-po-nonlb"></div>
                </div>
            </div>
        </div>
    </div>
</section>

@stop
@section('header')
<link rel="stylesheet" type="text/css" href="{{asset('')}}plugin/DataTables/datatables.min.css" />
@stop

@section('footer')
<script type="text/javascript" src="{{asset('')}}plugin/DataTables/datatables.min.js"></script>
<script>
    $('.change-date').change(function() {
            loadGrading();
        });
</script>

<script>
    var hash = window.location.hash.substr(1);
        var href = window.location.href;

        function deafultPage() {
            if (hash == undefined || hash == "") {
                hash = "custom-tabs-three-polb";
            }

            $('.nav-item a[href="#' + hash + '"]').addClass('active').siblings().removeClass('active');
            $('#' + hash).addClass('active show').siblings().removeClass('active show');
        }


        $('.tab-link').click(function(e) {
            e.preventDefault();
            status = $(this).attr('aria-controls');
            window.location.hash = status;
            href = window.location.href;

        });

        deafultPage();


        function loadGrading() {
            if (window.location.hash.substr(1) == "custom-tabs-three-polb") {
            loadGradingPOLB();
            } else if (window.location.hash.substr(1) == "custom-tabs-three-nonlb") {
                loadGradingPONONLB();
            } else {
                loadGradingPOLB();
            }
        }

        loadGrading()


        $("#custom-tabs-three-polb-tab").on("click", function() {
            loadGradingPOLB();
        });

        $("#custom-tabs-three-nonlb-tab").on("click", function() {
            loadGradingPONONLB();
        })


        function loadGradingPOLB() {
            $('#loading-grading-polb').show();
            var tanggalawal     =   $('#tanggalawal').val();
            var tanggalakhir    =   $('#tanggalakhir').val();
            $("#grading-po-lb").load("{{ route('grading.index', ['key' => 'POLB']) }}&tanggalawal=" + tanggalawal + "&tanggalakhir=" + tanggalakhir, function() {
                $('#loading-grading-polb').hide();
            }) ;
        }

        function loadGradingPONONLB() {
            $('#loading-grading-po-nonlb').show();
            var tanggalawal     =   $('#tanggalawal').val();
            var tanggalakhir    =   $('#tanggalakhir').val();
            $("#grading-po-nonlb").load("{{ route('grading.index', ['key' => 'NONLB']) }}&tanggalawal=" + tanggalawal + "&tanggalakhir=" + tanggalakhir, function() {
                $('#loading-grading-po-nonlb').hide();
            }) ;
        }


</script>
@stop