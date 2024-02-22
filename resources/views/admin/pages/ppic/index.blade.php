@extends('admin.layout.template')

@section('title', 'PPIC')

@section('footer')
<script>
    $('.tanggal').change(function() {
            var form = $(this).closest("form");
            var hash = window.location.hash;
            form.attr("action", "{{ route('ppic.index') }}" + hash);
            form.submit();
        });
</script>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col"></div>
    <div class="col pt-2 font-weight-bold text-center">
        PPIC
    </div>
    <div class="col"></div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('ppic.index') }}" method="GET">
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label for="">Filter</label>
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                            min="2023-01-01" @endif name="tanggal" class="form-control tanggal" id="tanggal"
                            value="{{ Request::get('tanggal') ?? date('Y-m-d') }}" autocomplete="off">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="">Filter</label>
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                            min="2023-01-01" @endif name="tanggalend" class="form-control tanggal" id="tanggalend"
                            value="{{ Request::get('tanggalend') ?? date('Y-m-d') }}" autocomplete="off">
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card mt-4">
    <ul class="nav nav-tabs" id="tabs-tab" role="tablist">
        <li class="nav-item">
            <a class="nav-link tab-link" id="tabs-orders-tab" data-toggle="pill" href="#tabs-orders" role="tab"
                aria-controls="tabs-orders" aria-selected="true">
                Orders
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link tab-link" id="tabs-pending-tab" data-toggle="pill" href="#tabs-pending" role="tab"
                aria-controls="tabs-pending" aria-selected="false">
                Order Pending
            </a>
        </li>
        {{-- DIPINDAH KE ABF --}}
        {{-- <li class="nav-item">
            <a class="nav-link tab-link" id="tabs-chiller-tab" data-toggle="pill" href="#tabs-chiller" role="tab"
                aria-controls="tabs-chiller" aria-selected="false">
                Chiller BB
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link tab-link" id="tabs-chiller-fg-tab" data-toggle="pill" href="#tabs-chiller-fg" role="tab"
                aria-controls="tabs-chiller-fg" aria-selected="false">
                Chiller FG
            </a>
        </li> --}}
        {{-- DIPINDAH KE ABF --}}
        <li class="nav-item">
            <a class="nav-link tab-link" id="tabs-chiller-penyiapan-tab" data-toggle="pill"
                href="#tabs-chiller-penyiapan" role="tab" aria-controls="tabs-chiller-penyiapan" aria-selected="false">
                Chiller Hasil Produksi
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link tab-link" id="tabs-evaluasi-tab" data-toggle="pill" href="#tabs-evaluasi" role="tab"
                aria-controls="tabs-evaluasi" aria-selected="false">
                Evaluasi Produksi
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link tab-link" id="tabs-nonlb-tab" data-toggle="pill" href="#tabs-nonlb" role="tab"
                aria-controls="tabs-nonlb" aria-selected="false">
                PO Non LB
            </a>
        </li>

        <li class="nav-item">
        <li class="nav-item">
            <a class="nav-link tab-link" id="tabs-lb-tab" data-toggle="pill" href="#tabs-lb" role="tab"
                aria-controls="tabs-lb" aria-selected="false">
                PO LB
            </a>
        </li>
        {{-- <li>
            <a class="nav-link tab-link" id="tabs-ukuran-tab" data-toggle="pill" href="#tabs-ukuran" role="tab"
                aria-controls="tabs-ukuran" aria-selected="false">
                Ukuran Ayam
            </a>
        </li> --}}
        <li>
            <a class="nav-link tab-link" id="tabs-stockchiller-tab" data-toggle="pill" href="#tabs-stockchiller"
                role="tab" aria-controls="tabs-stockchiller" aria-selected="false">
                Stock Chiller
            </a>
        </li>
    </ul>
    <div class="card-body">

        <div class="tab-content" id="tabs-tabContent">
            <div class="tab-pane fade " id="tabs-orders" role="tabpanel" aria-labelledby="tabs-orders-tab">
                @include('admin.pages.ppic.component.order')
            </div>
            <div class="tab-pane fade" id="tabs-pending" role="tabpanel" aria-labelledby="tabs-pending">
                @include('admin.pages.ppic.component.order_pending')
            </div>
            {{-- DIPINDAH KE ABF --}}
            {{-- <div class="tab-pane fade" id="tabs-chiller" role="tabpanel" aria-labelledby="tabs-chiller">
                @include('admin.pages.ppic.component.sisa_chiller')
            </div>
            <div class="tab-pane fade" id="tabs-chiller-fg" role="tabpanel" aria-labelledby="tabs-chiller-fg">
                @include('admin.pages.ppic.component.chiller_fg')
            </div> --}}
            {{-- DIPINDAH KE ABF --}}
            <div class="tab-pane fade" id="tabs-chiller-penyiapan" role="tabpanel"
                aria-labelledby="tabs-chiller-penyiapan">
                @include('admin.pages.ppic.component.chiller_penyiapan')
            </div>
            <div class="tab-pane fade" id="tabs-evaluasi" role="tabpanel" aria-labelledby="tabs-evaluasi">
                <div id="evaluasi"></div>
            </div>
            <div class="tab-pane fade" id="tabs-nonlb" role="tabpanel" aria-labelledby="tabs-nonlb">
                @include('admin.pages.ppic.component.non_lb')
            </div>
            <div class="tab-pane fade" id="tabs-lb" role="tabpanel" aria-labelledby="tabs-lb">
                @include('admin.pages.ppic.component.lb')
            </div>
            {{-- <div class="tab-pane fade" id="tabs-ukuran" role="tabpanel" aria-labelledby="tabs-ukuran">
                @include('admin.pages.ppic.component.ukuran')
            </div> --}}
            <div class="tab-pane fade" id="tabs-stockchiller" role="tabpanel" aria-labelledby="tabs-stockchiller">
                @include('admin.pages.ppic.component.stockchiller')
            </div>
        </div>
    </div>
</div>

<script>
    var tanggalend = $('#tanggalend').val();
        $("#evaluasi").load("{{ route('ppic.evaluasi', ['tanggal' => $tanggal]) }}&tanggalend="+tanggalend);
        $("#nonlb").load("{{ route('ppic.nonlb', ['tanggal' => $tanggal]) }}&tanggalend="+tanggalend);

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
</script>
@stop