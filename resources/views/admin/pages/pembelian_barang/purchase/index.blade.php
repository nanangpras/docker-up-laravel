@extends('admin.layout.template')

@section('title', 'Purchase Pembelian Barang')

@section('footer')
<script>
    loadSummaryPO()
// loadDataView()
dataList()

$("#loading_view").attr('style', 'display: block') ;
$("#purchase-info").load("{{ route('pembelian.purchase', ['key' => 'info']) }}", function() {
    $("#loading_view").attr('style', 'display: none') ;
    $('.select2').select2({
        theme: 'bootstrap4'
    })
}) ;


function dataList(){
    let filteritemdraft = encodeURIComponent($("#filteritemdraft").val())
    $("#loading_list").attr('style', 'display: block') ;
    $("#data_list").load("{{ route('pembelian.purchase', ['key' => 'list']) }}&filteritemdraft=" + filteritemdraft, function() {
        $("#loading_list").attr('style', 'display: none') ;
    }) ;  
}

$('#filteritemdraft').on('keyup', function(){
    dataList()
})

function loadSummaryPO(){
    let tanggal_mulai   =   $("#tanggal_mulai").val() ;
    let tanggal_akhir   =   $("#tanggal_akhir").val() ;
    let filterSummaryPO = encodeURIComponent($("#filterSummaryPO").val()) ;
    let netsuiteFilterPO = $('#netsuite_filterPO').val() ;
    let vendorPO        = $("#vendorPO").val() ?? ''
    $("#loading_summary").attr('style', 'display: block') ;
    $("#loading_summaryVendor").attr('style', 'display: block') ;
    $("#data_summary").load("{{ route('pembelian.purchase', ['key' => 'summary']) }}&tanggal_mulai=" + tanggal_mulai + "&tanggal_akhir=" + tanggal_akhir+ "&filterSummaryPO=" + filterSummaryPO + "&vendorPO=" + vendorPO + "&netsuiteFilterPO=" + netsuiteFilterPO, function() {
        $("#loading_summary").attr('style', 'display: none') ;
    }) ;
    $("#vendor_po").load("{{ route('pembelian.purchase', ['key' => 'vendorPO']) }}&tanggal_mulai=" + tanggal_mulai + "&tanggal_akhir=" + tanggal_akhir+ "&filterSummaryPO=" + filterSummaryPO + "&vendorPO=" + vendorPO + "&netsuiteFilterPO=" + netsuiteFilterPO, function() {
        $("#loading_summaryVendor").attr('style', 'display: none') ;
    }) ;
    // loadDataView()
    if($('input[id="peritempr"]:checked').length > 0){
        loadDataPerItem()
    } else {
        loadDataView()
    }
}

function loadDataView(){
    let itemsisa                = $("#itemsisa").is(':checked');
    let tanggal_mulai_data_view = $("#tanggal_mulai_data_view").val()
    let tanggal_akhir_data_view = $("#tanggal_akhir_data_view").val()
    let filterListPR            = encodeURIComponent($("#filterListPR").val())
    $("#loading_view").attr('style', 'display: block') ;
    $("#data_view").load("{{ route('pembelian.purchase', ['key' => 'view']) }}&tanggal_mulai_data_view=" + tanggal_mulai_data_view + "&tanggal_akhir_data_view=" + tanggal_akhir_data_view + "&filterListPR=" + filterListPR + "&itemsisa=" + itemsisa, function() {
        $("#loading_view").attr('style', 'display: none') ;
    }) ;
}

function loadDataPerItem(){
    let itemsisa                = $("#itemsisa").is(':checked');
    let tanggal_mulai_data_view = $("#tanggal_mulai_data_view").val()
    let tanggal_akhir_data_view = $("#tanggal_akhir_data_view").val()
    let filterListPR            = encodeURIComponent($("#filterListPR").val())
    $("#loading_view").attr('style', 'display: block') ;
    $("#data_view").load("{{ route('pembelian.purchase', ['key' => 'viewperitem']) }}&tanggal_mulai_data_view=" + tanggal_mulai_data_view + "&tanggal_akhir_data_view=" + tanggal_akhir_data_view + "&filterListPR=" + filterListPR + "&itemsisa=" + itemsisa, function() {
        $("#loading_view").attr('style', 'display: none') ;
    }) ;
}

$('#itemsisa').on('change', function(){
    if($('input[id="peritempr"]:checked').length > 0){
        loadDataPerItem()
    } else {
        loadDataView()
    }
})

$("#tanggal_mulai_data_view").on('change', function() {
    if($('input[id="peritempr"]:checked').length > 0){
        loadDataPerItem()
    } else {
        loadDataView()
    }
})
$("#tanggal_akhir_data_view").on('change', function() {
        if($('input[id="peritempr"]:checked').length > 0){
        loadDataPerItem()
    } else {
        loadDataView()
    }
})

$("#filterListPR").on('keyup', function() {
        if($('input[id="peritempr"]:checked').length > 0){
        loadDataPerItem()
    } else {
        loadDataView()
    }
})

$("#tanggal_mulai").on('change', function() {
    loadSummaryPO()
});

$("#tanggal_akhir").on('change', function() {
    loadSummaryPO()
});

$("#filterSummaryPO").on('keyup', function() {
    loadSummaryPO()
});

$('#netsuite_filterPO').on('change', function() {
    loadSummaryPO()
})

function vendorPO(){
    loadSummaryPO()
}

</script>

<script>
    $('.select2').select2({
        theme: 'bootstrap4',
    })
</script>

<script>
    $('input[name="pilihitempr"]').on('click', function(e) {
    $('input[name="pilihitempr"]').prop('checked', false);
    $(this).prop('checked', true);
    $('#data_view').empty();
    // console.log($(this).attr('id'))
    if($(this).attr('id') == 'peritempr'){
        loadDataPerItem()
    } else {
        loadDataView()
    }
});
</script>
@endsection

@section('content')
<div class="row my-4">
    <div class="col"><a href="{{ route('pembelian.index') }}"><i class="fa fa-arrow-left"></i> Kembali</a></div>
    <div class="col-8 font-weight-bold text-uppercase text-center">Purchase Order / PO</div>
    <div class="col"></div>
</div>


<ul class="nav nav-tabs" role="tablist">
    <li class="nav-item" role="presentation">
        <a class="nav-link tab-link" id="purchase-tab" data-toggle="tab" href="#purchase" role="tab"
            aria-controls="purchase" aria-selected="true">PO UMUM</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link tab-link" id="polb-tab" data-toggle="tab" href="#polb" role="tab" aria-controls="polb"
            aria-selected="false">PO LB</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link tab-link" id="pokarkas-tab" data-toggle="tab" href="#pokarkas" role="tab"
            aria-controls="pokarkas" aria-selected="false">PO KARKAS</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link tab-link" id="pokarkasfrozen-tab" data-toggle="tab" href="#pokarkasfrozen" role="tab"
            aria-controls="pokarkasfrozen" aria-selected="false">PO KARKAS FROZEN</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link tab-link" id="pononkarkas-tab" data-toggle="tab" href="#pononkarkas" role="tab"
            aria-controls="pononkarkas" aria-selected="false">PO NON KARKAS</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link tab-link" id="summary-tab" data-toggle="tab" href="#summary" role="tab"
            aria-controls="summary" aria-selected="false">SUMMARY</a>
    </li>
</ul>

<div class="tab-content mt-2">
    <div class="tab-pane fade" id="purchase" role="tabpanel" aria-labelledby="purchase-tab">
        <div class="row card-body p-2">
            <div class="col-md-6 pl-md-1">
                <div id="purchase-info"></div>
                <section class="panel">
                    <div class="card-header font-weight-bold">List Item Draft</div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col">
                                <label for="">Pencarian</label>
                                <input type="text" id="filteritemdraft" class="form-control" placeholder="Cari...">
                            </div>
                        </div>
                        <h5 id="loading_list" style="display: none"><i class="fa fa-refresh fa-spin"></i> Loading....
                        </h5>
                        <div id="data_list"></div>
                    </div>
                </section>
            </div>
            <div class="col-md-6 pr-md-1">
                <section class="panel">
                    <div class="card-header font-weight-bold">List Item PR</div>
                    <div class="card-body">
                        <label for="">Filter</label>
                        <div class="row">
                            <div class="col pr-1">
                                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                    min="2023-01-01" @endif value="{{ date("Y-m-d", strtotime('yesterday')) }}"
                                    id="tanggal_mulai_data_view" class="form-control">
                            </div>
                            <div class="col pl-1">
                                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                    min="2023-01-01" @endif value="{{ date("Y-m-d") }}" id="tanggal_akhir_data_view"
                                    class="form-control">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col">
                                <label for="">Pencarian</label>
                                <input type="text" id="filterListPR" class="form-control" placeholder="Cari...">
                            </div>
                        </div>
                        <label class="mt-2 px-2 pt-2 rounded status-info">
                            <input id="pernomorpr" type="checkbox" name="pilihitempr" checked> <label
                                for="pernomorpr">Per Nomor PR</label>
                        </label>
                        <label class="mt-2 px-2 pt-2 rounded status-success">
                            <input id="peritempr" type="checkbox" name="pilihitempr"> <label for="peritempr">Per
                                Item</label>
                        </label>
                        <label class="mt-2 px-2 pt-2 rounded status-warning float-right">
                            <input id="itemsisa" type="checkbox" name="itemsisapr"> <label for="itemsisa">Item
                                Sisa</label>
                        </label>
                        <hr>
                        <h5 id="loading_view" style="display: none" class="text-center"><i
                                class="fa fa-refresh fa-spin"></i> Loading....</h5>
                        <div id="data_view"></div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="summary" role="tabpanel" aria-labelledby="summary-tab">
        <section class="panel">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <label for="">Tanggal Mulai </label>
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                            min="2023-01-01" @endif value="{{ date("Y-m-d",strtotime('-7 days')) }}" id="tanggal_mulai"
                            class="form-control">
                    </div>
                    <div class="col">
                        <label for="">Tanggal Akhir </label>
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                            min="2023-01-01" @endif value="{{ date("Y-m-d") }}" id="tanggal_akhir"
                            class="form-control">
                    </div>
                    <div class="col">
                        <label for="">Pencarian</label>
                        <input type="text" id="filterSummaryPO" class="form-control" placeholder="Cari...">
                    </div>
                    <div class="col">
                        <label for="">Vendor</label>
                        <h5 id="loading_summaryVendor" style="display: none" class="text-center"><i
                                class="fa fa-refresh fa-spin"></i> Loading....</h5>
                        <div id="vendor_po"></div>
                    </div>
                    <div class="col">
                        <label for="">Status Netsuite</label>
                        <select name="netsuite_filterPO" id="netsuite_filterPO" class="form-control select2"
                            placeholder="">
                            <option value="">Semua</option>
                            <option value="9">PO Pending</option>
                            <option value="1">Netsuite Terbentuk</option>
                            <option value="2">Pending Integrasi</option>
                            <option value="3">Netsuite Terkirim</option>
                            <option value="4">PO Closed</option>
                        </select>
                    </div>
                </div>
            </div>
        </section>

        <h5 id="loading_summary" style="display: none" class="text-center"><i class="fa fa-refresh fa-spin"></i>
            Loading....</h5>
        <div id="data_summary"></div>

    </div>
    <div class="tab-pane fade" id="pokarkas" role="tabpanel" aria-labelledby="pokarkas-tab">
        <section class="panel">
            <div class="card-body">
                @include('admin.pages.pembelian_barang.purchase.form-po-karkas')
            </div>
        </section>
    </div>
    <div class="tab-pane fade" id="pononkarkas" role="tabpanel" aria-labelledby="pononkarkas-tab">
        <section class="panel">
            <div class="card-body">
                @include('admin.pages.pembelian_barang.purchase.form-po-non-karkas')
            </div>
        </section>
    </div>
    <div class="tab-pane fade" id="pokarkasfrozen" role="tabpanel" aria-labelledby="pokarkasfrozen-tab">
        <section class="panel">
            <div class="card-body">
                @include('admin.pages.pembelian_barang.purchase.form-po-karkasfrozen')
            </div>
        </section>
    </div>
    <div class="tab-pane fade" id="polb" role="tabpanel" aria-labelledby="pobl-tab">
        <section class="panel">
            <div class="card-body">
                @include('admin.pages.pembelian_barang.purchase.form-po-lb')
            </div>
        </section>
    </div>
</div>


<script>
    var hash = window.location.hash.substr(1);
    var href = window.location.href;

    deafultPage();

    function deafultPage() {
        if (hash == undefined || hash == "") {
            hash = "purchase";
        }

        $('#' + hash + '-tab').addClass('active').siblings().removeClass('active');
        $('#' + hash).addClass('active show').siblings().removeClass('active show');

        // Load History
        loadHistoryPO()
    }


    $('.tab-link').click(function(e) {
        e.preventDefault();
        status = $(this).attr('aria-controls');
        window.location.hash = status;
        href = window.location.href;

        // Load History
        loadHistoryPO()

    });

    function loadHistoryPO(){
        const url_history = window.location.hash.substr(1);
        if (url_history == "purchase") {
            $('#history_po_karkasfrozen').empty();
            $('#history_po_nonkarkas').empty();
            $('#history_po_karkas').empty();
            $('#history_po_lb').empty();
        } else if (url_history == 'polb'){
            $('#history_po_karkasfrozen').empty();
            $('#history_po_nonkarkas').empty();
            $('#history_po_karkas').empty();
            $('#history_po_lb').load("{{ route('pembelian.purchase', ['key' => 'historyPO']) }}&subkey=polb");
        } else if (url_history == 'pokarkas'){
            $('#history_po_lb').empty();
            $('#history_po_karkasfrozen').empty();
            $('#history_po_nonkarkas').empty();
            $('#history_po_karkas').load("{{ route('pembelian.purchase', ['key' => 'historyPO']) }}&subkey=pokarkas");
        } else if (url_history == 'pokarkasfrozen'){
            $('#history_po_lb').empty();
            $('#history_po_karkas').empty();
            $('#history_po_nonkarkas').empty();
            // $('#history_po_karkasfrozen').load("{{ route('pembelian.purchase', ['key' => 'historyPO']) }}&subkey=pokarkasfrozen");
        } else if (url_history == 'pononkarkas'){
            $('#history_po_lb').empty();
            $('#history_po_karkas').empty();
            $('#history_po_karkasfrozen').empty();
            $('#history_po_nonkarkas').load("{{ route('pembelian.purchase', ['key' => 'historyPO']) }}&subkey=pononkarkas");
        }
    }
</script>

@endsection