@extends('admin.layout.template')

@section('title', 'Kepala Produksi')

@section('header')
    <style>
        

    </style>
@endsection

@section('content')

    <div class="row mb-4">
        <div class="col">
            <a href="{{ route('kepalaregu.whole') }}" class="btn btn-outline btn-sm btn-back"> <i
                    class="fa fa-arrow-left"></i>
                Back</a>
        </div>
        <div class="col text-center">
            <b>Preparation Kepala Regu Parting</b>
        </div>
        <div class="col"></div>
    </div>
    <section class="panel">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <div class="form-group row">
                        <div class="col">
                            <label>Nama Customer</label>
                            <input class="form-control" type="text" name="under" id="under" value="{{ $detail->nama }}"
                                readonly>
                        </div>
                        <div class="col">
                            <label>Qty Order</label>
                            <input class="form-control" type="text" name="" id="" value="{{ $item->qty }}" readonly>
                        </div>
                        <div class="col">
                        </div>
                        <div class="col">
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="panel">
        <div class="card-body">
            <div class="row">
                <h6 class="text-center">Stock Chiller</h6>
                <div class="table-responsive">
                    <table width="100%" class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th class="text-center">Produk</th>
                                <th class="text-center">Tanggal Potong</th>
                                <th class="text-center">Qty</th>
                                <th class="text-center">Kg </th>
                                <th class="text-center">Order Qty</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="cart">
                            @foreach ($chiller as $chill)
                                <tr>
                                    <td >{{ $chill->item_name }}</td>
                                    <td class="text-center">{{ $chill->tanggal_produksi }}</td>
                                    <td class="text-center">{{ $chill->stock_item }}</td>
                                    <td class="text-center">{{ $chill->stock_berat }}</td>
                                    <td class="text-center" width="150px">
                                        <div class="form-group">
                                            <div class="col ">
                                                <input type="text" name="qty" id="qty" class="form-control">
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <button type="submit" class="btn btn-success btn-sm masuk"
                                            data-order="{{ $detail->id }}" data-chiller="{{ $chill->id }}"
                                            data-item="{{ $item->id }}" data-prod="{{ $chill->production_id }}"
                                            data-truck="{{ $chill->chilprod->no_urut }}">Ajukan</button>
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
    <script>
        $(document).ready(function() {

            $(document).on('click', '.masuk', function() {
                var item = $(this).data('item');
                var chiller = $(this).data('chiller');
                var order = $(this).data('order');
                var truck = $(this).data('truck');
                var qty = $('#qty').val();
                var prod = $('#prod').val();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "{{ route('kepalaregu.storewhole') }}",
                    method: "POST",
                    data: {
                        item: item,
                        chiller: chiller,
                        order: order,
                        truck: truck,
                        qty: qty,
                        prod: prod
                    },
                    success: function(data) {
                        location.reload()
                    }
                });
            })
        });

    </script>
@stop
