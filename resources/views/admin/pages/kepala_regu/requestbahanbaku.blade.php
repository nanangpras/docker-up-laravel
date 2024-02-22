@extends('admin.layout.template')

@section('title', 'Kepala Produksi')

@section('content')
    <div class="row mb-4">
        <div class="col">
            <a href="{{ $redirect }}" class="btn btn-outline btn-sm btn-back"> <i class="fa fa-arrow-left"></i>
                Back
            </a>
        </div>
        <div class="col text-center">
            <b>Preparation Kepala Regu</b>
        </div>
        <div class="col"></div>
    </div>
    <section class="panel">
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label>Item Order</label>
                        <input class="form-control" type="text" value="{{ $item->nama_detail }}" readonly>
                    </div>
                    <div class="form-group">
                        <label>Nama Customer</label>
                        <input class="form-control" type="text" value="{{ $detail->nama }}" readonly>
                    </div>
                </div>
                <div class="col">
                    <div class="row">
                        <div class="col-4">
                            <label>Berat Order</label>
                            <input class="form-control" type="text" value="{{ $item->berat }}" readonly>
                        </div>
                        <div class="col-4">
                            <label>Qty Order</label>
                            <input class="form-control" type="text" value="{{ $item->qty }}" readonly>
                        </div>
                        <div class="col-4">
                            <label>Qty Proses</label>
                            <input class="form-control" type="text" value="{{ $bahan['jml_item'] }}" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <label>Berat Proses</label>
                            <input class="form-control" type="text" value="{{ $bahan['jml_berat'] }}" readonly>
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
                    <table width="100%" class="table default-table">
                        <thead>
                            <tr>
                                <th class="text-center">Produk</th>
                                <th class="text-center">Tanggal Potong</th>
                                <th class="text-center">Qty</th>
                                <th class="text-center">Kg </th>
                                <th class="text-center">Order Qty</th>
                                <th class="text-center">Order Berat</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="cart">
                            @foreach ($chiller as $chill)
                                <tr>
                                    <td>{{ $chill->item_name }}</td>
                                    <td class="text-center">{{ $chill->tanggal_produksi }}</td>
                                    <td class="text-center">{{ $chill->stock_item }}</td>
                                    <td class="text-center">{{ $chill->stock_berat }}</td>
                                    @if ($chill->asal_tujuan == 'free_boneless')
                                        <td class="text-center" width="200px">

                                        </td>
                                    @else
                                        <td class="text-center" width="200px">
                                            <input type="number" min="0" name="qty" id="qty{{ $chill->id }}"
                                                class="form-control">
                                        </td>
                                    @endif
                                    <td class="text-center" width="200px">
                                        <input type="number" min="0" name="berat" id="berat{{ $chill->id }}"
                                            class="form-control">
                                    </td>
                                    <td class="text-center">
                                        <button type="submit" class="btn btn-success btn-sm masuk"
                                            data-order="{{ $detail->id }}" data-chiller="{{ $chill->id }}"
                                            data-item="{{ $item->id }}" data-stock="{{ $chill->stock_item }}"
                                            data-prod="{{ $chill->production_id }}"
                                            data-stockberat="{{ $chill->stock_berat }}"
                                            data-truck="{{ $chill->chilprod->no_urut ?? '' }}">Ajukan</button>
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
                var pending = $(this).data('pending');
                var qty = $('#qty' + chiller).val();
                var berat = $('#berat' + chiller).val();
                var prod = $('#prod').val();
                var stock = $(this).data('stock');

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                if (qty > (stock - pending)) {
                    swal({
                        position: 'top-end',
                        title: "Order Melebihi Stock!",
                        text: "",
                        type: "error",
                        showCancelButton: false,
                        showConfirmButton: false,
                        timer: 2000
                    }, function() {
                        swal.close();
                    });
                }
                //  else if (qty == '') {
                //     swal({
                //         position: 'top-end',
                //         title: "Order Kosong!",
                //         text: "",
                //         type: "error",
                //         showCancelButton: false,
                //         showConfirmButton: false,
                //         timer: 2000
                //     }, function() {
                //         swal.close();
                //     });
                // }
                // else if ("{{ $bahan['jml_item'] }}" >= "{{ $item->qty }}") {
                //     swal({
                //         position: 'top-end',
                //         title: "Order Sudah Terpenuhi",
                //         text: "",
                //         type: "error",
                //         showCancelButton: false,
                //         showConfirmButton: false,
                //         timer: 2000
                //     }, function() {
                //         swal.close();
                //     });
                // } else if (qty > "{{ $item->qty - $bahan['jml_item'] }}") {
                //     swal({
                //         position: 'top-end',
                //         title: "Order Terlalu Banyak",
                //         text: "",
                //         type: "error",
                //         showCancelButton: false,
                //         showConfirmButton: false,
                //         timer: 2000
                //     }, function() {
                //         swal.close();
                //     });
                // }
                else {
                    $.ajax({
                        url: "{{ route('kepalaregu.storerequestbahanbaku') }}",
                        method: "POST",
                        data: {
                            item: item,
                            chiller: chiller,
                            order: order,
                            truck: truck,
                            qty: qty,
                            berat: berat,
                            prod: prod
                        },
                        success: function(data) {
                            swal({
                                title: "Success!",
                                type: "success",
                                showCancelButton: false,
                                showConfirmButton: false,
                                timer: 2000
                            }, function() {
                                swal.close();
                                location.reload();
                            });

                        }
                    });
                }
            })
        });

    </script>
@stop
