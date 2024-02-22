@extends('admin.layout.template')

@section('title', 'QC Retur')

@section('content')
<div class="row mb-4">
    <div class="col">
        <a href="{{ route('qc.index') }}" class="btn btn-outline btn-sm btn-back"> <i class="fa fa-arrow-left"></i>
            Back</a>
    </div>
    <div class="col text-center">
        <b>QC</b>
    </div>
    <div class="col"></div>
</div>

<section class="panel">
    <div class="card-body">
        {{-- <form method="GET" action="{{ route('laporan.qcexportretur') }}" enctype="multipart/form-data"> --}}
            <div class="row">
                <div class="col">
                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                        min="2023-01-01" @endif name="tanggalstart" class="form-control" id="tanggalstart"
                        placeholder="Tuliskan " value="{{ date('Y-m-d') }}" autocomplete="off">
                    @error('tanggal') <div class="small text-danger">{{ $message }}</div> @enderror
                </div>
                <div class="col">
                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                        min="2023-01-01" @endif name="tanggalend" class="form-control" id="tanggalend"
                        placeholder="Tuliskan " value="{{ date('Y-m-d') }}" autocomplete="off">
                    @error('tanggal') <div class="small text-danger">{{ $message }}</div> @enderror
                </div>
                <div class="col">
                    <div class="form-group">
                        <select name="customer" class="form-control select2" id="customer">
                            <option value="">Pilih Customer </option>
                            @foreach ($customer as $cus)
                            <option value="{{ $cus->id }}">{{ $cus->nama }}</option>
                            @endforeach
                        </select>
                        @error('customer') <div class="small text-danger">{{ message }}</div> @enderror
                    </div>

                </div>
                <div class="col">
                    <button type="button" class="btn btn-primary exportRetur"> <i class="fa fa-spinner fa-spin spinerloading" style="display: none"></i> <span id="text">Export Retur</span></button>
                    {{-- <button type="submit" class="btn btn-blue">Export Retur</button> --}}
                </div>
            </div>
        {{-- </form> --}}
        <br>
        <div id="loading">
            <img src="{{ asset('loading.gif') }}" style="display:none;width: 18px">
        </div>
        {{-- <div id="retur-summary"></div> --}}
        <br><br>
    </div>
</section>



<style>
    .border.rounded input {
        padding-left: 0px;
        padding-right: 0px;
        border: 0px;
    }
</style>

<script>
        $('.select2').select2({
            theme: 'bootstrap4',

        });

        var customer_id = "";
        var tanggal = "";

        // $('#retur-summary').load("{{ route('laporan.qc-retur-where') }}");
        // $("#datacustomer").load("{{ route('retur.customer') }}");
        // $("#itemretur").load("{{ route('retur.itemretur') }}");
        $('#tanggalend').change(function() {
            var tanggalstart = $('#tanggalstart').val();
            var tanggalend = $(this).val();
            // $('#loading').show();
            // $('#retur-summary').load("{{ url('admin/laporan/qc-retur/where?tanggalstart=') }}" + tanggalstart +
            //     '&tanggalend=' + tanggalend,
            //     function() {
            //         $('#loading').hide();
            //     });
        })

        $('#customer').change(function() {
            var tanggalstart = $('#tanggalstart').val();
            var tanggalend = $('#tanggalend').val();
            var customer = $(this).val();
            // $('#loading').show();
            // $('#retur-summary').load("{{ url('admin/laporan/qc-retur/where?tanggalstart=') }}" + tanggalstart +
            //     '&tanggalend=' + tanggalend + '&customer=' + customer,
            //     function() {
            //         $('#loading').hide();
            //     });
        })

        $(".exportRetur").click(function(){
            tglmulai    = $('#tanggalstart').val();
            tglselesai  = $('#tanggalend').val();
            customer    = $('#customer').val();

            $.ajax({
                url     : "{{ route('laporan.qcexportretur') }}",
                method  : "GET",
                cache   : false,
                data    :{
                    'tanggalstart'  : tglmulai,
                    'tanggalend'    : tglselesai,
                    'customer'      : customer
                },
                beforeSend: function() {
                    $('.exportRetur').attr('disabled');
                    $(".spinerloading").show(); 
                    $("#text").text('Downloading...');
                },
                success: function(data) {
                    $(".exportRetur").attr('disabled');
                    setTimeout(() => {
                        $("#text").text('Export Excel');
                        $(".spinerloading").hide();
                        window.location.href = "{{ route('laporan.qcexportretur') }}?tanggalstart=" + tglmulai + "&tanggalend=" + tglselesai + '&customer=' + customer;
                    }, 1000);
                }
            });
        })
</script>

@stop