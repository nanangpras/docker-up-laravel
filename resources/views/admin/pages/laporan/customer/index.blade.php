@extends('admin.layout.template')

@section('title', 'Customer Report')

@section('footer')
<script>
    $("#data_view").load("{{ route('customer.index', ['key' => 'view']) }}");
$("#parent_customer").load("{{ route('customer.index', ['key' => 'parent']) }}");

$("#cari").on('keyup', function() {
    pilih_parent()
})

$("#advance").on('change', function() {
    pilih_parent()
})

$("#statuscustomer").on('change', function() {
    pilih_parent()
})

function pilih_parent() {
    var cari    =   encodeURIComponent($("#cari").val()) ;
    var parent  =   encodeURIComponent($("#parent").val()) ;
    var status  =   encodeURIComponent($("#statuscustomer").val()) ;
    var advance =   $("#advance").val() ;
    // console.log
    $("#data_view").load("{{ route('customer.index', ['key' => 'view']) }}&cari=" + cari + "&advance=" + advance + "&parent=" + parent + "&status=" + status);
}
</script>

<script>
    $('.select2').select2({
    theme: 'bootstrap4'
});
</script>

<script>
    $("#chartso").load("{{ route('customer.index', ['key' => 'chartso']) }}");
$("#show_so").load("{{ route('customer.index', ['key' => 'show_so']) }}");
$("#data_customer").load("{{ route('customer.index', ['key' => 'data_customer']) }}");



$("#tanggal_awal").on('change', function() {
    konsumen()
})

$("#tanggal_akhir").on('change', function() {
    konsumen()
})

function konsumen() {
    var awal    =   $("#tanggal_awal").val() ;
    var akhir   =   $("#tanggal_akhir").val() ;
    var konsumen=   encodeURIComponent($("#konsumen").val()) ;
    $("#chartso").load("{{ route('customer.index', ['key' => 'chartso']) }}&tanggal_awal=" + awal + "&tanggal_akhir=" + akhir + "&konsumen=" + konsumen);
    $("#show_so").load("{{ route('customer.index', ['key' => 'show_so']) }}&tanggal_awal=" + awal + "&tanggal_akhir=" + akhir + "&konsumen=" + konsumen);
    $("#data_customer").load("{{ route('customer.index', ['key' => 'data_customer']) }}&tanggal_awal=" + awal + "&tanggal_akhir=" + akhir + "&konsumen=" + konsumen);
}

$(document).ready(function () {
    $("#export-customer").on('click', function () {
        var parent  =   encodeURIComponent($("#parent").val()) ;
        var status  =   encodeURIComponent($("#statuscustomer").val()) ;
        var advance =   $("#advance").val() ;

        $.ajax({
            type: "GET",
            url: "{{route('customer.index')}}",
            data: {
                'key':'view',
                'part':'download',
                advance : advance,
                parent : parent,
                status : status,

            },
            success: function (response) {
                window.location.href    =   "{{ route('customer.index', ['key' => 'view']) }}&advance=" + advance + "&parent=" + parent +"&status=" + status + "&part=downloadCustomer" ;
            }
        });
    });
});


</script>
@endsection

@section('content')
<div class="mb-4 text-center font-weight-bold">
    Customer Report
</div>


<ul class="nav nav-tabs mb-3" role="tablist">
    <li class="nav-item" role="presentation">
        <a class="nav-link active" id="dataKonsumen-tab" data-toggle="tab" href="#dataKonsumen" role="tab"
            aria-controls="dataKonsumen" aria-selected="true">Data Konsumen</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link" id="chartSO-tab" data-toggle="tab" href="#chartSO" role="tab" aria-controls="chartSO"
            aria-selected="false">Chart SO</a>
    </li>
</ul>
<div class="tab-content">
    <div class="tab-pane fade show active" id="dataKonsumen" role="tabpanel" aria-labelledby="dataKonsumen-tab">
        <section class="panel">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <input type="text" placeholder="Cari..." id="cari" class="form-control" autocomplete="off">
                    </div>
                    <div class="col">
                        <select id="advance" class="form-control select2" data-width="100%">
                            <option value="all">Semua Konsumen</option>
                            <option value="last_order">Terakhir Order</option>
                            <option value="no_order">Belum Ada Order</option>
                            <option value="max_order">Order Terbanyak</option>
                        </select>
                    </div>
                    <div class="col">
                        <select id="statuscustomer" class="form-control select2" data-width="100%">
                            <option value="all">Semua Status</option>
                            <option value="aktif">Aktif</option>
                            <option value="tidakaktif">Tidak Aktif</option>
                        </select>
                    </div>
                    <div class="col">
                        <div id="parent_customer"></div>
                    </div>
                    <div class="col">
                        <button type="button" id="export-customer" class="btn btn-success btn-sm"> <i
                                class="fa fa-download"></i> Unduh</button>
                        {{-- <a
                            href="{{route('customer.index',array_merge(['key' => 'view','part' => 'downloadCustomer'], $_GET))}}"
                            class="btn btn-success btn-sm"> <i class="fa fa-download"></i> download atas</a> --}}
                    </div>
                </div>
            </div>
        </section>

        <section class="panel">
            <div class="card-body">
                <div id="data_view"></div>
            </div>
        </section>
    </div>

    <div class="tab-pane fade" id="chartSO" role="tabpanel" aria-labelledby="chartSO-tab">

        <section class="panel">
            <div class="card-body">
                <div id="chartso"></div>
                <b>Pencarian Bedasarkan Tanggal Kirim</b>
                <div class="row">
                    <div class="col-6 pr-1">
                        <label for="tanggal_awal">Tanggal Awal</label>
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                            min="2023-01-01" @endif id="tanggal_awal" value="{{ date('Y-m-d', strtotime("-7 Day",
                            time())) }}" class="form-control">
                    </div>
                    <div class="col-6 pl-1">
                        <label for="tanggal_akhir">Tanggal Akhir</label>
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                            min="2023-01-01" @endif id="tanggal_akhir" value="{{ date("Y-m-d") }}"
                            class="form-control">
                    </div>
                </div>
                <div class="mt-3" id="data_customer"></div>
                <div class="mt-4" id="show_so"></div>
            </div>
        </section>

    </div>
</div>


@endsection