@extends('admin.layout.template')

@section('title', 'Customer Stock')

@section('content')

<div class="text-center mb-4">
    <b>CUSTOMER STOCK</b>
</div>

<section class="panel">
    <div class="card-body">
        {{-- <form action="{{ route('hasilproduksi.index') }}" method="GET"> --}}
            <div class="row">
                <div class="col-md-4 col-sm-4 col-xs-6">
                    <div class="form-group">
                        Pencarian
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                            min="2023-01-01" @endif name="tanggalawal" id="tanggalawal" class="form-control change-date"
                            value="{{ $tanggal }}" id="pencarian" placeholder="Cari...." autocomplete="off">
                    </div>
                </div>
                <div class="col-md-4 col-sm-4 col-xs-6">
                    &nbsp;
                    <div class="form-group">
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                            min="2023-01-01" @endif name="tanggalakhir" id="tanggalakhir"
                            class="form-control change-date" value="{{ $tanggal }}" id="pencarian"
                            placeholder="Cari...." autocomplete="off">
                    </div>
                </div>
                <div class="col-md-4 col-sm-4 col-xs-6">
                    <div id="get_customer"></div>
                </div>
                {{-- <div class="col-md-4 col-sm-4 col-xs-6">
                    Customer
                    <div class="form-group">
                        <select name="customer" id="customer" class="form-control select2">
                            @foreach ($customer as $item => $value)
                            <option value="{{ $value }}" {{ $filter_customer==$value ? 'selected' : '' }}>
                                {{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div> --}}
            </div>
            {{--
        </form> --}}
        <div id="loading-customer-stock" class="text-center" style="display: none">
            <img src="{{ asset('loading.gif') }}" style="width: 20px"> Loading ...
        </div>
        <div id="result_customer_stock"></div>
    </div>
</section>



@stop
@section('footer')
{{-- <script>
    $(".select2").select2({
            theme: "bootstrap4"
        });
</script> --}}

<script>
    data_customer_stock();

        $("#tanggalawal,#tanggalakhir").on('change', function() {
            // console.log('ok');
            data_customer_stock();
        });

        function getCust() {
            let customer = $('#customer_stok').val() ?? '';
            console.log(customer);
            data_customer_stock();
        }



        function data_customer_stock() {
            var tanggal = $('#tanggalawal').val();
            var tanggalakhir = $('#tanggalakhir').val();
            let customer = $('#customer_stok').val() ?? '';
            $('#loading-customer-stock').show();
            // console.log(customer);
            $('#result_customer_stock').load(
                "{{ route('customer.stock', ['key' => 'data_customer_stock']) }}&tanggalawal=" +
                tanggal + "&tanggalakhir=" + tanggalakhir + "&customer=" + customer,
                function() {
                    $('#loading-customer-stock').hide();
                });
            $("#get_customer").load("{{ route('customer.stock', ['key' => 'getcustomer']) }}&tanggalawal=" + tanggal +
                "&tanggalakhir=" + tanggalakhir);
        }



        // function getcustomer() {
        //     var tanggal = $('#tanggalawal').val();
        //     var tanggalakhir = $('#tanggalakhir').val();
        //     console.log(tanggal);
        //     console.log(tanggalakhir);
        //     $.ajax({
        //         type: "get",
        //         url: "{{ route('customer.stock') }}",
        //         data: {
        //             tanggal: tanggal,
        //             tanggalakhir: tanggalakhir,
        //             key: 'dd_customer'

        //         },
        //         success: function(response) {
        //             console.log(response);
        //             $("#customer").html(response);
        //         }
        //     });
        // }
</script>
@stop