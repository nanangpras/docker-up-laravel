@extends('admin.layout.template')

@section('title', 'Kepala Produksi')

@section('content')
<div class="row mb-4">
    <div class="col"></div>
    <div class="col text-center">
        <b>KEPALA PRODUKSI</b>
    </div>
    <div class="col"></div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('kepalaproduksi.index') }}" method="GET">
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label for="">Filter</label>
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                            min="2023-01-01" @endif name="tanggal" class="form-control tanggal" id="tanggal"
                            value="{{ $tanggal }}" autocomplete="off">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="">Filter</label>
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                            min="2023-01-01" @endif name="tanggalend" class="form-control tanggal" id="tanggalend"
                            value="{{ $tanggalend }}" autocomplete="off">
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
            <a class="nav-link tab-link" id="tabs-chiller-tab" data-toggle="pill" href="#tabs-chiller" role="tab"
                aria-controls="tabs-chiller" aria-selected="false">
                Sisa Chiller
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link tab-link" id="tabs-chiller-fg-tab" data-toggle="pill" href="#tabs-chiller-fg" role="tab"
                aria-controls="tabs-chiller-fg" aria-selected="false">
                Chiller FG / Kirim ABF
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link tab-link" id="tabs-nonlb-tab" data-toggle="pill" href="#tabs-nonlb" role="tab"
                aria-controls="tabs-nonlb" aria-selected="false">
                PO Non LB
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link tab-link" id="tabs-inventory-tab" data-toggle="pill" href="#tabs-inventory" role="tab"
                aria-controls="tabs-inventory" aria-selected="false">
                Tranfer Inventory
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link tab-link" id="tabs-parting-tab" data-toggle="pill" href="#tabs-parting" role="tab"
                aria-controls="tabs-parting" aria-selected="false">
                Proses Produksi
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link tab-link" id="tabs-hasil-potong-tab" data-toggle="pill" href="#tabs-hasil-potong"
                role="tab" aria-controls="tabs-hasil-potong" aria-selected="false">
                Selisih Lpah & Grading
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link tab-link" id="tabs-summary-tab" data-toggle="pill" href="#tabs-summary" role="tab"
                aria-controls="tabs-summary" aria-selected="false">
                Summary Produksi
            </a>
        </li>
    </ul>

    <div class="card-body">
        <div class="tab-content" id="tabs-tabContent">
            <div class="tab-pane fade active show" id="tabs-orders" role="tabpanel" aria-labelledby="tabs-orders-tab">
                @include('admin.pages.kepala_produksi.component.order')
            </div>

            <div class="tab-pane fade" id="tabs-chiller" role="tabpanel" aria-labelledby="tabs-chiller-tab">
                @include('admin.pages.kepala_produksi.component.sisa_chiller')
            </div>

            <div class="tab-pane fade" id="tabs-chiller-fg" role="tabpanel" aria-labelledby="tabs-chiller-fg">
                @include('admin.pages.ppic.component.chiller_fg')
            </div>

            <div class="tab-pane fade" id="tabs-nonlb" role="tabpanel" aria-labelledby="tabs-nonlb-tab">
                @include('admin.pages.ppic.component.non_lb')
            </div>

            <div class="tab-pane fade" id="tabs-inventory" role="tabpanel" aria-labelledby="tabs-inventory-tab">
                <div id="showinventory"></div>
            </div>

            <div class="tab-pane fade" id="tabs-parting" role="tabpanel" aria-labelledby="tabs-parting-tab">
                <div class="radio-toolbar">
                    <div class="row">
                        <div class="form-group">
                            <div class="col-4 col-md-12">
                                <input type="radio" name="regu" class="regu" value="boneless" id="boneless">
                                <label for="boneless">Boneless</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-4 col-md-12">
                                <input type="radio" name="regu" class="regu" value="parting" id="parting">
                                <label for="parting">Parting</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-4 col-md-12">
                                <input type="radio" name="regu" class="regu" value="marinasi" id="marinasi">
                                <label for="marinasi">Parting M</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-4 col-md-12">
                                <input type="radio" name="regu" class="regu" value="whole" id="whole">
                                <label for="whole">Whole Chicken</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-4 col-md-12">
                                <input type="radio" name="regu" class="regu" value="frozen" id="frozen">
                                <label for="frozen">Frozen</label>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div id="regushow"></div>
            </div>
            <div class="tab-pane fade" id="tabs-hasil-potong" role="tabpanel" aria-labelledby="tabs-hasil-potong">
                <div id="showhasilpotong"></div>
            </div>
            <div class="tab-pane fade" id="tabs-summary" role="tabpanel" aria-labelledby="tabs-summary-tab">
                <div id="showsummary"></div>
            </div>
        </div>
    </div>
</div>

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
</script>


<script>
    var tanggal = $('#tanggal').val();
        var tanggalend = $('#tanggalend').val();
        var url_route_sumary = "{{ route('kepalaproduksi.summary') }}" + "?tanggal=" + tanggal +"&tanggalend=" +tanggalend;
        var url_route_hasil_potong = "{{ route('kepalaproduksi.hasilpotong') }}" + "?tanggal=" + tanggal +"&tanggalend=" +tanggalend;

        $("#showsummary").load(url_route_sumary);
        $("#showhasilpotong").load(url_route_hasil_potong);

        $('.tanggal').change(function() {
            $(this).closest("form").submit();
            tanggal = $('#tanggal').val();
        });

        $("#nonlb").load("{{ route('ppic.nonlb', ['tanggal' => $tanggal]) }}&tanggalend="+tanggalend);
        $("#showinventory").load("{{ route('kepalaproduksi.inventory') }}");

        $('.tanggal').change(function() {
            var form = $(this).closest("form");
            var hash = window.location.hash;
            form.attr("action", "{{ route('kepalaproduksi.index') }}" + hash);
            form.submit();
        });

        $("#nonlb").load("{{ route('ppic.nonlb', ['tanggal' => $tanggal]) }}&tanggalend="+tanggalend);
        $("#regushow").load("{{ route('kepalaproduksi.regu') }}");

        $('.regu').change(function() {
            tanggal         = $('#tanggal').val();
            tanggalakhir    = $('#tanggalend').val();
            var regu        = $(this).val();

            $('#regushow').load("{{ url('admin/kepala-produksi/regu?tanggal=') }}" + tanggal +"&tanggalend="+ tanggalend + "&regu=" + regu);
            console.log("{{ url('admin/kepala-produksi/regu?tanggal=') }}" + tanggal + "&tanggalend="+ tanggalend + "&regu=" + regu);
        })
</script>
@stop