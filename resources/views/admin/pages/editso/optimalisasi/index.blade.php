@extends('admin.layout.template')

@section('title', 'Chiller')

@section('content')

<div class="row mb-4">
    <div class="col"><a href="{{ route('fulfillment.index') }}"><i class="fa fa-arrow-left"></i> Kembali</a></div>
    <div class="col text-center">
        <b>EDIT SO</b>
    </div>
    <div class="col"></div>
</div>

<section class="panel">
    <div class="card-body">
        <b style="font-size: 9pt">
            {{ $order->no_so }}
            || {{ $order->nama }} , {{ $order->id }}
            <span class="pull-right">
                {{ $order->sales_channel }} || Kirim : {{ date('d/m/y', strtotime($order->tanggal_kirim)) }}
            </span>
        </b>
        @if ($order->keterangan)
        <br>{{ $order->keterangan }}
        @endif

        <table class="table default-table table-small">
            <thead>
                <tr>
                    <th width="30%">Nama</th>
                    <th width="10%">Order</th>
                    <th width="10%">Fulfill</th>
                    <th width="50%">Pengalokasian</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $close_order = false;
                @endphp
                @foreach ($order->daftar_order_full as $i => $item)
                @php
                if ($item->status == 2) {
                    $close_order = true;
                }
                @endphp
                <tr>
                    <td>
                        {{ $item->nama_detail }} 
                        <br>

                        @if ($item->memo != '')
                        <br>Memo: <span class="blue">{{ $item->memo }}</span>
                        @endif

                        @if (App\Models\Order::getInternalMemo($order->no_so, $item->id) != '')
                        <br> Internal Memo: <span class="status status-warning">{{ App\Models\Order::getInternalMemo($order->no_so, $item->id) }}</span> <br>
                        @endif

                        @if ($item->description_item != '')
                        <br> Description: <span class="status status-info">{{ $item->description_item }}</span>
                        @endif
                        @if ($item->part != '')
                        <br><span class="orange">Potong {{ $item->part }}</span>
                        @endif
                        @if ($item->bumbu != '')
                        <br><span class="green">{{ $item->bumbu }}</span>
                        @endif

                        
                        @if ($item->getDeletedBahanBaku)
                        <br>
                        <a href="{{ url('admin/editso/'.$item->id.'/riwayat')}}" class="btn btn-primary btn-sm historyDeleteBB mt-2"
                            data-toggle="modal" data-target="#riwayat" data-id="{{$item->id}}">
                            History Delete
                        </a>

                        @endif
                    </td>
                    <td><span class="status status-info">{{ number_format($item->qty ?? '0') }} || {{ number_format($item->berat ?? '0', 2) }} kg</span></td>
                    <td>
                        @if($item->fulfillment_berat>0)
                        <span class="status status-success">{{ number_format($item->fulfillment_qty ?? '0') }} || {{ number_format($item->fulfillment_berat ?? '0', 2) }} kg</span>
                        @else
                        <span class="status status-danger">{{ number_format($item->fulfillment_qty ?? '0') }} || {{ number_format($item->fulfillment_berat ?? '0', 2) }} kg</span>
                        @endif
                    </td>
                    <td>
                        <div class="order_item_bahan_baku" data-id="{{ $item->id }}" id="order_bahan_baku{{ $item->id }}">
                            <img src="{{ asset('loading.gif') }}" style="width: 18px"> Loading ...
                        </div>
                        @if ($order->status == '0')
                            <span class="status status-danger pull-right">Dibatalkan</span>
                        @else
                            <button type="button" class="btn btn-default mt-1" data-toggle="modal" data-target="#exampleModal"
                                id="form-item-name{{ $item->id }}"
                                onclick="return selected_id('{{ $order->id }}','{{ $item->id }}','{{ $item->item_id }}')">
                                Tambah Item <span class="fa fa-chevron-down"></span>
                            </button>
                        @endif
                    </td>
                    <td>
                        @if ($item->getHistoryReset)
                        <a href="{{ url('admin/editso/'.$item->id.'/riwayat')}}"
                            class="btn btn-primary btn-sm history_reset" data-toggle="modal" data-target="#riwayat"
                            data-id="{{$item->id}}">History</a>
                        @endif
                        <a href="{{ route('editso.batalkan', $item->id) }}" class="btn btn-red btn-sm">Reset</a>
                    </td>
                </tr>
                @endforeach

            </tbody>
            
            <tfoot>
                <tr>
                    <td colspan="5">
                        <div id="SpinnerIntegrasiNetsuite-{{$order->id}}" style="display:none; text-align:center;" >
                            <img src="{{ asset('loading.gif') }}" style="width: 20px"> Loading ...
                        </div>
                        <div id="goToIntegrasi-{{$order->id}}" class="load-integrasi-netsuite" data-id="{{ $order->id }}"></div>
                    </td>
                </tr>
            </tfoot>
            
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
                                <h5 class="modal-title" id="exampleModalLabel">Pengambilan Item</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="order_id" id="selected_order_id" value="">
                                <input type="hidden" name="order_item_id" id="selected_order_item_id" value="">
                                <input type="hidden" name="item_id" id="selected_item_id" value="">
                                {{-- <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) min="2023-01-01"
                                    @endif id="selected_tanggal" class="form-control" value="{{ date('Y-m-d') }}"> --}}
                                @if(env('NET_SUBSIDIARY', 'CGL')=='CGL')
                                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                    min="2023-01-01" @endif id="selected_tanggal" class="form-control"
                                    value="{{ date('Y-m-d') }}" min="2023-05-27">
                                @else
                                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                    min="2023-01-01" @endif id="selected_tanggal" class="form-control"
                                    value="{{ date('Y-m-d') }}" min="2023-05-05">
                                @endif

                                <div class="mb-2 mt-2" id="alokasi-tab">
                                    <a href="javascript:void(0)" class="btn btn-green select-asal"
                                        id="select-chiller-fg">Chiller FG</a>
                                    <a href="javascript:void(0)" class="btn btn-green select-asal"
                                        id="select-chiller-bb">Chiller BB</a>
                                    <a href="javascript:void(0)" class="btn btn-green select-asal"
                                        id="select-gudang">Gudang CS</a>
                                </div>

                                <div id="info_order"></div>
                                <div class="my-2">
                                    Item sudah diambil :
                                    <div id="riwayat_ambil"></div>
                                </div>
                                <div id="loading-ambil" class="text-center" style="display: none">
                                    <img src="{{ asset('loading.gif') }}" width="20px">
                                </div>
                                <div id="list-penyiapan-chiller"></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary btnHiden">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>


<section class="panel">
    <div class="card-body">
        <b style="font-size: 9pt">
            EDIT NO DO DAN STATUS
        </b>
        @php
        $ceklog = App\Models\Adminedit::where('table_id', $order->id)->where('table_name', 'orders')->where('type',
        'edit')->count();
        @endphp
        @if ($ceklog > 0)
        <a href="{{ url('admin/editso/'.$order->id.'/riwayat')}}" class="btn btn-primary btn-sm riwayat_edit_sodo"
            data-toggle="modal" data-target="#riwayat" data-id="{{$order->id}}" style="float: right;">
            history edit
        </a>
        {{-- <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#riwayat{{ $order->id }}"
            style="float: right;">history edit</button> --}}
        @endif
        <hr>

        <form action="{{ route('salesorder.edit', $order->id) }}" method="post">
            @csrf
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        NO SO
                        <input type="text" class="form-control" name="no_so" value="{{ $order->no_so }}" readonly>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        NO DO
                        <input type="text" class="form-control" name="no_do" value="{{ $order->no_do }}">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        Tanggal SO
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                            min="2023-01-01" @endif class="form-control" name="tanggal_so"
                            value="{{ $order->tanggal_so }}">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        Tanggal Kirim
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                            min="2023-01-01" @endif class="form-control" name="tanggal_kirim"
                            value="{{ $order->tanggal_kirim }}">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        STATUS
                        <select class="form-control" name="status">
                            <option value="0" @if($order->status=="") selected @endif>PENDING</option>
                            <option value="10" @if($order->status=="10") selected @endif>SELESAI</option>
                            <option value="11" @if($order->status=="11") selected @endif>BATAL</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <button type="submit" class="btn btn-blue">Update</button>
                </div>
                <div class="col">
                </div>
            </div>
        </form>
    </div>
</section>

{{-- modal reset dan history edit --}}
<div class="modal fade" id="riwayat" aria-labelledby="riwayatLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="width: 800px;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">History</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div id="content_riwayat"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>



<style>
    #alokasi-tab .select-asal.active {
        background: rgb(0, 81, 0);
    }
</style>

<script>
    var item_id = "";
    var order_id = "";
    var order_item_id = "";
    var lokasi = "chiller-fg";
    var tanggal = $('#selected_tanggal').val();


    $('.load-integrasi-netsuite').each(function(i) {
        var id          = $(this).attr('data-id');
        console.log(id);
        loadIntegrasiNetsuite(id)
    });

    function loadIntegrasiNetsuite(id){
        var uri = "{{ route('editso.getintegrasinetsuite') }}" + "?id=" + id+"&key=integrasinetsuitewithorm";
        
        $.ajax({
            url         : uri,
            method      : "GET",
            beforeSend  : function(){
                $("#SpinnerIntegrasiNetsuite-"+id).show()
                $("#goToIntegrasi-"+id).hide()
            },
            success     : function(data){
                $("#goToIntegrasi-"+id).html(data)
                $("#goToIntegrasi-"+id).show()
                $("#SpinnerIntegrasiNetsuite-"+id).hide();
            },
            error       : (err) => {
                // console.log(err)
                // return exit
            }
        })
    }
    
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
        $('#order_bahan_baku' + id).html("Loading ...");
        var id = $(this).attr('data-id');
        var url_pemenuhan = "{{ route('editso.pemenuhan') }}" + "?order_item_id=" + id + "&view=edit";
        $('#order_bahan_baku' + id).load(url_pemenuhan)
    });


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

                if (data.status == 400) {
                    showAlert(data.msg);
                } else {
                    id = $('#selected_order_item_id').val();
                    var url_pemenuhan = "{{ route('editso.pemenuhan') }}" + "?order_item_id=" + id + "&view=edit";
                    $('#order_bahan_baku' + id).load(url_pemenuhan);
                    $('#riwayat_ambil').load("{{ route('editso.pemenuhan') }}?order_item_id=" + id + "&view=edit");
                    $('#info_order').load("{{ route('editso.pemenuhan') }}?key=info&order_item_id=" + id + "&view=edit");
                    $(".qty_item").val('');
                    $(".berat_item").val('');
                    load_bahan_baku();
                    // $('#exampleModal').modal('toggle');

                    location.reload();

                }

            }
        });

    })

    function selected_id(orderid, orderitemid, itemid) {
        $('#selected_order_id').val(orderid);
        $('#selected_item_id').val(itemid);
        $('#selected_order_item_id').val(orderitemid);

        item_id = $('#selected_item_id').val();
        order_id = $('#selected_order_id').val();
        order_item_id = $('#selected_order_item_id').val();

        tanggal = $('#selected_tanggal').val();

        $('#info_order').load("{{ route('editso.pemenuhan') }}?key=info&order_item_id=" + orderitemid + "&view=edit");
        $('#riwayat_ambil').load("{{ route('editso.pemenuhan') }}?order_item_id=" + orderitemid + "&view=edit");

        load_bahan_baku();
    }

    $('#selected_tanggal').on('change', function() {
        tanggal = $(this).val();
        load_bahan_baku();
    })

    $(".batalkanorder").click(function() {

        if (!confirm('Batalkan Fulfill?')) return false;

        var id = $(this).data('batal');
        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: "{{ route('penyiapan.batalorder') }}",
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
        if ($(this).data('status') == 0) {
            if (!confirm('Open Orderan?')) return false;
        } else {
            if (!confirm('Close Orderan?')) return false;
        }

        var id = $(this).data('batal');
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: "{{ route('penyiapan.batalorder') }}",
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
                $('#list-penyiapan-chiller').html(data);
                
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
                $('#list-penyiapan-chiller').html(data);
                
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

        $.ajax({
            url: "{{ route('sampingan.chiller') }}" + '?tanggal=' + tanggal + '&item_id=' + item_id,
            method: "GET",
            success: function(data) {
                $('#list-penyiapan-chiller').html(data);

                $('.selected-sampingan-chiller').on('click', function() {
                    var id = $(this).attr('data-id');
                    var nama = $(this).attr('data-nama');
                    var berat = $(this).attr('data-berat');

                    focusCode(id, id, nama, berat);
                })

            }
        })

    }


    function focusCode(id, code, name, berat) {
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

    $(".history_reset").click( function(e){
            e.preventDefault();
            var id       = $(this).data('id');
            var href     = $(this).attr('href');

            $.ajax({
                url : href,
                type: "GET",
                data: {
                    id          : id,
                    key         : "riwayat_reset",
                },
                success: function(data){
                    $('#content_riwayat').html(data);
                }
            });
    });

    $(".historyDeleteBB").click( function(e){
            e.preventDefault();
            var id       = $(this).data('id');
            var href     = $(this).attr('href');

            $.ajax({
                url : href,
                type: "GET",
                data: {
                    id          : id,
                    key         : "historyDeleteBB",
                },
                success: function(data){
                    $('#content_riwayat').html(data);
                }
            });
    });


    $(".riwayat_edit_sodo").click( function(e){
            e.preventDefault();
            var id       = $(this).data('id');
            var href     = $(this).attr('href');

            $.ajax({
                url : href,
                type: "GET",
                data: {
                    id          : id,
                    key         : "riwayat_edit_sodo",
                },
                success: function(data){
                    $('#content_riwayat').html(data);
                }
            });
    });

    $(".btnHiden").click(function(){
        $('.kirimfulfilltambahan').addClass('disabled');
        $('.kirimtitambahan').addClass('disabled');
        $('#text-notif').html('Sedang Diproses ...');
        $('#topbar-notification').fadeIn();
    })
</script>
@stop