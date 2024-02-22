@extends('admin.layout.template')

@section('title', 'Chiller')

@section('content')

<div class="row mb-4">
    <div class="col">
        <a href="{{ route('index') }}" class="btn btn-outline btn-sm btn-back"> <i class="fa fa-arrow-left"></i>
            Back</a>
    </div>
    <div class="col text-center">
        <b>EDIT SO</b>
    </div>
    <div class="col"></div>
</div>

<section class="panel">
    <div class="card-body">
        <b style="font-size: 9pt">
            {{ $order->no_so }} || {{ $order->nama }}
            <span class="pull-right">
                {{ $order->sales_channel }} || Kirim : {{ date('d/m/y', strtotime($order->tanggal_kirim)) }}
            </span>
        </b>

        <table class="table default-table table-small">
            <thead>
                <tr>
                    <th width="30%">Nama</th>
                    <th width="60">Qty</th>
                    <th width="60">Berat</th>
                    <th width="50%">Pengalokasian</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @php
                $close_order = false;
                @endphp
                @foreach (Order::item_order($order->id) as $i => $item)
                @php
                if ($item->status == 2) {
                $close_order = true;
                }
                @endphp
                <tr>
                    <td>{{ $item->nama_detail }}
                        @if ($item->memo != '')
                        <br><span class="status status-info">{{ $item->memo }}</span>
                        @endif
                        @if ($item->part != '')
                        <br><span class="status status-warning">Potong {{ $item->part }}</span>
                        @endif
                        @if ($item->bumbu != '')
                        <br><span class="status status-success">{{ $item->bumbu }}</span>
                        @endif
                    </td>
                    <td>{{ $item->qty ?? '0' }}</td>
                    <td>{{ $item->berat ?? '0' }} kg</td>
                    <td>
                        <div class="order_item_bahan_baku" data-id="{{ $item->id }}"
                            id="order_bahan_baku{{ $item->id }}"><img src="{{ asset('loading.gif') }}"
                                style="width: 18px"> Loading ...</div>

                        @if ($order->status == '0')
                        <span class="status status-danger">Dibatalkan</span>
                        @else
                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#exampleModal"
                            id="form-item-name{{ $item->id }}"
                            onclick="return selected_id('{{ $order->id }}','{{ $item->id }}','{{ $item->item_id }}')">
                            Pilih Item Dari Chiller <span class="fa fa-chevron-down"></span>
                        </button>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('editso.batalkan', $item->id) }}" class="btn btn-danger btn-sm">Batalkan</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div style="min-width: 200px">

            <!-- Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                @csrf
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form action="{{ route('editso.simpanalokasi') }}" method="POST" id="submit-alokasi">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Item Chiller</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="order_id" id="selected_order_id" value="">
                                <input type="hidden" name="order_item_id" id="selected_order_item_id" value="">
                                <input type="hidden" name="item_id" id="selected_item_id" value="">
                                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                    min="2023-01-01" @endif id="selected_tanggal" class="form-control"
                                    value="{{ date('Y-m-d') }}">

                                <div class="mb-2 mt-2" id="alokasi-tab">
                                    <a href="javascript:void(0)" class="btn btn-green select-asal"
                                        id="select-chiller-bb">Sampingan</a>
                                    <a href="javascript:void(0)" class="btn btn-green select-asal"
                                        id="select-chiller-fg">Chiller</a>
                                    <a href="javascript:void(0)" class="btn btn-green select-asal"
                                        id="select-gudang">Frozen</a>
                                </div>

                                <div id="info_order"></div>
                                <div class="my-2">
                                    Item sudah diambil :
                                    <div id="riwayat_ambil"></div>
                                </div>
                                <div id="loading-ambil" class="text-center" style="display: none">
                                    <img src="{{ asset('loading.gif') }}" width="20px">
                                </div>
                                <div id="list-sampingan-chiller"></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>

</section>

<script>
    var item_id = "";
        var order_id = "";
        var order_item_id = "";
        var lokasi = "chiller-bb";
        var tanggal = $('#selected_tanggal').val();

        load_bahan_baku();

        $('#select-chiller-fg').on('click', function() {
            lokasi = "chiller-fg";
            load_bahan_baku()
        });
        $('#select-chiller-bb').on('click', function() {
            lokasi = "chiller-bb";
            load_bahan_baku()
        });
        $('#select-gudang').on('click', function() {
            lokasi = "frozen";
            load_bahan_baku()
        });


        $('.order_item_bahan_baku').each(function(i) {
            var id = $(this).attr('data-id');
            var url_pemenuhan = "{{ route('editso.pemenuhansampingan') }}" + "?order_item_id=" + id;
            $('#order_bahan_baku' + id).load(url_pemenuhan)

        });

        $('.fulfill-item').on('click', function() {

            // if (!confirm('Konfirmasi?')) return false;

            var orderitemid = $(this).attr('data-id');
            $(".fulfill-item").hide();
            console.log("{{ route('sampingan.fulfillitem') }}?order_item_id=" + orderitemid)
            $.ajax({
                url: "{{ route('sampingan.fulfillitem') }}?order_item_id=" + orderitemid,
                type: 'get',
                success: function(data) {
                    console.log(data);
                    var url_pemenuhan = "{{ route('editso.pemenuhansampingan') }}" + "?order_item_id=" +
                        orderitemid;

                    if (data.status == "400") {
                        showAlert(data.msg)
                        $(".fulfill-item").show();
                    } else {
                        showNotif("Alokasi diselesaikan")
                        $('#order_bahan_baku' + orderitemid).load(url_pemenuhan)
                        $(".fulfill-item").show();
                        $('#fulfill-item' + orderitemid).remove();
                        $('#form-item-name' + orderitemid).hide();
                        $('.status' + orderitemid).show();
                    }
                }
            });
        })


        $('#submit-alokasi').on('submit', function(e) {
            e.preventDefault();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('editso.simpanalokasi') }}",
                type: 'post',
                dataType: 'json',
                data: $(this).serialize(),
                success: function(data) {
                    console.log(data);
                    if (data.status == 400) {
                        showAlert(data.msg)
                    } else {

                        id = $('#selected_order_item_id').val();
                        var url_pemenuhan = "{{ route('editso.pemenuhansampingan') }}" +
                            "?order_item_id=" +
                            id;
                        console.log(url_pemenuhan);
                        $('#order_bahan_baku' + id).load(url_pemenuhan);
                        $('#riwayat_ambil').load(
                            "{{ route('editso.pemenuhansampingan') }}?order_item_id=" + id);
                        $('#info_order').load(
                            "{{ route('editso.pemenuhansampingan') }}?key=info&order_item_id=" + id);
                        $(".qty_ambil").val('');
                        $(".berat_ambil").val('');
                        load_sampingan();
                        // $('#exampleModal').modal('toggle');
                    }

                }
            });

        })

        $(".batalkanorder").click(function() {
            if (!confirm('Batalkan Fulfill?')) return false;

            var id = $(this).data('batal');
            console.log(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{ route('sampingan.batalorder') }}",
                method: "POST",
                data: {
                    id: id
                },
                success: function(data) {
                    showNotif(data.msg);
                    location.reload()
                }
            })
        })

        $(".close_order").click(function() {
            var id = $(this).data('batal');
            console.log(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{ route('sampingan.batalorder') }}",
                method: "POST",
                data: {
                    id: id,
                    key: 'close'
                },
                success: function(data) {
                    showNotif(data.msg);
                    location.reload()
                }
            })
        })

        function selected_id(orderid, orderitemid, itemid) {
            console.log(orderid);
            console.log(orderitemid);
            console.log(itemid);
            $('#selected_order_id').val(orderid);
            $('#selected_item_id').val(itemid);
            $('#selected_order_item_id').val(orderitemid);

            item_id = $('#selected_item_id').val();
            order_id = $('#selected_order_id').val();
            order_item_id = $('#selected_order_item_id').val();

            tanggal = $('#selected_tanggal').val();

            $('#info_order').load("{{ route('editso.pemenuhansampingan') }}?key=info&order_item_id=" + orderitemid);
            $('#riwayat_ambil').load("{{ route('editso.pemenuhansampingan') }}?order_item_id=" + orderitemid);

            load_bahan_baku();
        }

        $('#selected_tanggal').on('change', function() {
            tanggal = $(this).val();
            load_bahan_baku();
        })



        function load_bahan_baku() {
            if (lokasi == "chiller-fg") {
                load_penyiapan();
            }
            if (lokasi == "chiller-bb") {
                load_sampingan();
            }
            if (lokasi == "frozen") {
                load_penyiapanfrozen();
            }
        }

        function load_penyiapan() {

            $('#loading-ambil').show();
            $.ajax({
                url: "{{ route('penyiapan.chiller') }}" + '?tanggal=' + tanggal + '&item_id=' + item_id,
                method: "GET",
                success: function(data) {
                    $('#list-sampingan-chiller').html(data);
                    console.log("{{ route('penyiapan.chiller') }}" + '?tanggal=' + tanggal + '&item_id=' +
                        item_id)
                    $('.selected-penyiapan-chiller').on('click', function() {
                        var id = $(this).attr('data-id');
                        var nama = $(this).attr('data-nama');
                        var berat = $(this).attr('data-berat');
                        focusCode(id, id, nama, berat);
                    })
                    $('#loading-ambil').hide();

                }
            })

        }

        function load_penyiapanfrozen() {

            $('#loading-ambil').show();
            $.ajax({
                url: "{{ route('penyiapanfrozen.storage') }}" + '?tanggal=' + tanggal + '&item_id=' + item_id,
                method: "GET",
                success: function(data) {
                    $('#list-sampingan-chiller').html(data);
                    console.log("{{ route('penyiapanfrozen.storage') }}" + '?tanggal=' + tanggal +
                        '&item_id=' + item_id)
                    $('.selected-penyiapanfrozen-storage').on('click', function() {
                        var id = $(this).attr('data-id');
                        var nama = $(this).attr('data-nama');
                        var berat = $(this).attr('data-berat');

                        focusCode(id, id, nama, berat);
                    })
                    $('#loading-ambil').hide();
                }
            })

        }

        function load_sampingan() {

            $('#loading-ambil').show();
            $.ajax({
                url: "{{ route('sampingan.chiller') }}" + '?tanggal=' + tanggal + '&item_id=' + item_id,
                method: "GET",
                success: function(data) {
                    $('#list-sampingan-chiller').html(data);

                    $('.selected-sampingan-chiller').on('click', function() {
                        var id = $(this).attr('data-id');
                        var nama = $(this).attr('data-nama');
                        var berat = $(this).attr('data-berat');

                        focusCode(id, id, nama, berat);
                    })
                    $('#loading-ambil').hide();

                }
            })

        }



        function focusCode(id, code, name, berat) {
            console.log(name)
            console.log($('#form-item' + id).length);
            $('#form-item' + id).val(code);
            $('#form-item-name' + id).html(name);
            $('#berat' + id).on('keyup', function() {
                var input_val = $(this).val();
                if (input_val > berat) {
                    $(this).val(berat);
                    showAlert("Max item tidak boleh lebih dari stock");
                }
            });
        }
</script>

<script>
    $('.pagination a').on('click', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');
            $.ajax({
                url: url,
                method: "GET",
                success: function(response) {
                    $('#order').html(response);
                }

            });
        });
</script>

<style>
    #alokasi-tab .select-asal.active {
        background: rgb(0, 81, 0);
    }
</style>
@stop