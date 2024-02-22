@extends('admin.layout.template')

@section('title', 'Laporan QC')

@section('content')
{{-- 
<div class="row mb-2">
    <div class="col"></div>
    <div class="col-6 py-1 text-center">
        <b>Laporan QC</b>
    </div>
    <div class="col"></div>
</div>
--}}


<section class="panel">
    <div class="card card-primary card-outline card-tabs">
        <ul class="nav nav-tabs" id="custom-tabs-tab" role="tablist">
            <li class="nav-item">
                <a class="nav-link tab-link" id="custom-tabs-laporanqcumum-tab" data-toggle="pill" href="#custom-tabs-laporanqcumum" role="tab" aria-controls="custom-tabs-laporanqcumum" aria-selected="true">Laporan Umum</a>
            </li>
            @if(Session::get('subsidiary') == 'CGL')
            <li class="nav-item">
                <a class="nav-link tab-link" id="custom-tabs-kematianayam-tab" data-toggle="pill" href="#custom-tabs-kematianayam" role="tab" aria-controls="custom-tabs-kematianayam" aria-selected="true">Laporan Kematian Ayam</a>
            </li>
            @endif
        </ul>
        <div class="card-body">
            <div class="tab-content" id="custom-tabs-tabContent">
                <div class="tab-pane fade " id="custom-tabs-laporanqcumum" role="tabpanel" aria-labelledby="custom-tabs-laporanqcumum-tab">
                    @include('admin.pages.bukubesar.laporan_qc')
                </div>
                <div class="tab-pane fade " id="custom-tabs-kematianayam" role="tabpanel" aria-labelledby="custom-tabs-kematianayam-tab">
                    @include('admin.pages.bukubesar.laporan_kematian_ayam')
                </div>
            </div>
        </div>
    </div>
</section>
@stop

@section('footer')
<script>

    var hash = window.location.hash.substr(1);
    var href = window.location.href;

    defaultPage();

    function defaultPage() {
        if (hash == undefined || hash == "") {
            hash = "custom-tabs-laporanqcumum";
        }

        $('#' + hash + '-tab').addClass('active').siblings().removeClass('active');
        $('#' + hash).addClass('active show').siblings().removeClass('active show');
    }

    $('.tab-link').click(function(e) {
        e.preventDefault();
        status                  = $(this).attr('aria-controls');
        window.location.hash    = status;
        href                    = window.location.href;

    });
    if(hash === "custom-tabs-laporanqcumum"){
        loadqcumum();
    }else 
    if(hash === "custom-tabs-kematianayam"){
        loadkematianayam()
    }

    $("#custom-tabs-laporanqcumum-tab").on('click', function(){
        loadqcumum();
    });

    $("#custom-tabs-kematianayam-tab").on('click', function(){
        loadkematianayam();
    });

</script>
@stop