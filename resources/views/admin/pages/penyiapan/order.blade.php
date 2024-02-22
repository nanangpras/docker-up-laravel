<div class="row">
    @if (count($pending) == '0')
    <div class="col">
        <div class="alert alert-danger">Item Order Belum Tersedia</div>
    </div>
    @else
    <div class="col">
        <label for="to">Total Order</label>
        <b id="to">{{ $status_order['semua_order'] }}</b>
    </div>
    <div class="col">
        <label for="pf">Pending Fulfillment</label>
        <b id="pf">{{ $status_order['pending_order'] }}</b>
    </div>
    <div class="col">
        <label for="sel">Selesai</label>
        <b id="sel">{{ $status_order['selesai_order'] }}</b>
    </div>
    <div class="col text-right">
        <a href="{{ route('penyiapan.index', ['key' => 'unduh']) }}&tanggal={{ $tanggal }}"
            class="btn btn-success">Unduh</a>
    </div>
    @endif
</div>
<hr>

@foreach ($pending as $i => $val)

<b style="font-size: 9pt">
    <a href="{{url('/admin/laporan/sales-order/'.$val->id)}}" target="_blank">
        {{ $loop->iteration + ($pending->currentpage() - 1) * $pending->perPage() }}.
    </a>
    <a href="{{ route('editso.index', $val->id) }}" target="_blank">{{ $val->no_so }}</a>
    || {{ $val->nama }}
    <span class="pull-right">
        {{ $val->sales_channel }} || Kirim : {{ date('d/m/y', strtotime($val->tanggal_kirim)) }}
    </span>
</b>
@if ($val->keterangan)
<br>{{ $val->keterangan }}
@endif


<table class="table default-table table-small">
    <thead>
        <tr>
            <th width="30%">Nama</th>
            <th width="60">Qty</th>
            <th width="60">Berat</th>
            <th width="50%">Pengalokasian
            </th>
        </tr>
    </thead>
    <tbody>
        @php
        $close_order = false;
        @endphp
        @foreach ($val->daftar_order_full as $item)
        @php
        if ($item->status == 2) {
        $close_order = true;
        }
        @endphp
        <tr>
            <td>
                {{ $item->nama_detail }}

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
            <td>{{ number_format($item->qty ?? '0') }}</td>
            <td>{{ number_format($item->berat ?? '0', 2) }} kg</td>
            <td>
                <div class="order_item_bahan_baku" data-id="{{ $item->id }}" id="order_bahan_baku{{ $item->id }}"> <img
                        src="{{ asset('loading.gif') }}" style="width: 18px"> Loading ...</div>

                @if ($val->status == '0')
                <span class="status status-danger pull-right">Dibatalkan</span>
                @else

                <a href="#" class="btn btn-default btn-sm show-ket pull-right blue" data-toggle="modal"
                    data-target="#keteranganModal" data-item_id="{{ $item->item_id }}"
                    data-order_id="{{ $item->order_id }}" data-order_item_id="{{ $item->id }}"
                    data-keterangan="{{ $item->tidak_terkirim_catatan }}">Ket Tdk Terkirim</a>

                @if ($item->status == null)
                @if ($val->status != '10')
                <button type="button" class="btn btn-default" data-toggle="modal" data-target="#exampleModal"
                    id="form-item-name{{ $item->id }}"
                    onclick="return selected_id('{{ $val->id }}','{{ $item->id }}','{{ $item->item_id }}')">
                    Pilih Item Dari Chiller <span class="fa fa-chevron-down"></span>
                </button>
                <a href="javascript:void(0)" class="btn btn-green btn-sm fulfill-item" data-id="{{ $item->id }}"
                    id="fulfill-item{{ $item->id }}">Simpan</a>
                @endif
                <div class="status status-warning status{{ $item->id }}" style="display: none">
                    Selesai dialokasikan
                </div>
                @else
                <div class="status status-success">Terpenuhi</div>
                @endif
                @endif

                @if($item->tidak_terkirim == "1")
                <br><span class="status status-danger">Tidak terkirim : {{$item->tidak_terkirim_catatan}}</span>
                @endif
            </td>
        </tr>
        @endforeach
        <tr>
            <td colspan="4">
                <span class="green">Alamat : {{$val->alamat_kirim}}</span><br>
                <span style="color: #bbbbbb">SO Masuk :
                    {{ date('d/m/y H:i:s', strtotime($val->created_at)) }}</span>
                @if ($val->status == '0')
                <br><a href="javascript:void(0)" class="green close_order" data-status="{{ $val->status }}"
                    data-batal="{{ $val->id }}"><span class="fa fa-history"></span>Buka Kembali Order</a>
                @else
                <br><a href="javascript:void(0)" class="close_order red" data-status="{{ $val->status }}"
                    data-batal="{{ $val->id }}"><span class="fa fa-times"></span>Tutup Order</a>
                @endif

                @if ($val->status == '0')
                <div class="status status-danger pull-right">Dibatalkan</div>
                @else

                @if ($close_order == true or $val->status > 6)

                <div class="pull-right">

                    @php
                    $ns = \App\Models\Netsuite::where('tabel_id', $val->id)
                    ->where('label', 'itemfulfill')
                    ->where('tabel', 'orders')
                    ->first();


                    $ns_gagal = \App\Models\Netsuite::where('tabel_id', $val->id)
                    ->where('label', 'itemfulfill')
                    ->where('tabel', 'orders')
                    ->whereIn('status', [0,4,5,6])
                    ->get();
                    @endphp

                    @if($val->no_do=="")
                    @if ($ns)
                    @if (!empty($ns->failed))
                    <div class="status status-danger">
                        @php
                        //code...
                        $resp = json_decode($ns->failed);
                        @endphp

                        @if(is_array($resp))
                        Tidak terfulfill : {{ $resp[0]->message->message ?? '' }}
                        @endif
                    </div>
                    @endif
                    @endif

                    @else
                    <span class='status status-danger'>{{$val->no_do}}</span>
                    @endif

                    @foreach($ns_gagal as $no => $g)
                    @if ($g)
                    @if (!empty($g->failed))
                    <div class="status status-danger">
                        @php
                        //code...
                        $resp = json_decode($g->failed);
                        @endphp

                        @if(is_array($resp))
                        DO {{$no+1}}. Tidak terfulfill : {{ $resp[0]->message->message ?? '' }} || {{$g->failed_time}}
                        @endif
                    </div>

                    @endif
                    @if($g->status==6)
                    <span class="status status-purple">INTEGRASI HOLD</span>
                    <a href="{{ route('buatso.netsuite_retry', $ns->id) }}" type="button" class="btn btn-blue btn-sm">
                        Proses Ulang
                    </a>
                    @endif
                    @endif
                    @endforeach

                    @if($ns->status==2)
                    <span class="status status-info">INTEGRASI PENDING</span>
                    @endif
                    @if($ns->status==4)
                    <span class="status status-other">INTEGRASI ANTRIAN</span>
                    @endif

                </div>

                <div class="status status-success pull-right">Telah Selesai</div>
                @if (User::setIjin('superadmin'))
                || <a href="javascript:void(0)" class="orange batalkanorder" data-batal="{{ $val->id }}"><span
                        class="fa fa-times"></span> Batalkan
                    Fulfill</a>
                @endif
                @else
                <a href="{{ route('penyiapan.closeorder') }}?order_id={{ $val->id }}"
                    class="btn btn-red btn-sm btnHiden pull-right">Selesaikan</a>
                @endif

                @endif
            </td>
        </tr>
    </tbody>
</table>
@endforeach



<div style="min-width: 200px">

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        @csrf
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('penyiapan.simpanalokasi') }}" method="POST" id="submit-alokasi">
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
                        <div class="row">
                            @if(env('NET_SUBSIDIARY', 'CGL')=='CGL')
                            <div class="col pr-1">
                                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) @endif
                                    id="selected_tanggal" class="form-control"
                                    value="{{ date('Y-m-d', strtotime('-3 day')) }}" min="2023-05-27">
                            </div>
                            <div class="col pl-1">
                                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) @endif
                                    id="selected_tanggal_akhir" class="form-control" value="{{ date('Y-m-d') }}"
                                    min="2023-05-27">
                            </div>
                            @else
                            <div class="col pr-1">
                                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) @endif
                                    id="selected_tanggal" class="form-control"
                                    value="{{ date('Y-m-d', strtotime('-3 day')) }}" min="2023-05-05">
                            </div>
                            <div class="col pl-1">
                                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) @endif
                                    id="selected_tanggal_akhir" class="form-control" value="{{ date('Y-m-d') }}"
                                    min="2023-05-05">
                            </div>
                            @endif
                        </div>
                        <div class="row">
                            <div class="col">
                                <input type="text" id="pencarian-stock" class="form-control mt-2" value=""
                                    placeholder="Pencarian">
                            </div>
                        </div>
                        <div class="mb-2 mt-2" id="alokasi-tab">
                            <a href="javascript:void(0)" class="btn btn-green select-asal"
                                id="select-chiller-fg">Chiller FG</a>
                            <a href="javascript:void(0)" class="btn btn-green select-asal"
                                id="select-chiller-bb">Chiller BB</a>
                            <a href="javascript:void(0)" class="btn btn-green select-asal" id="select-gudang">Gudang
                                CS</a>
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
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<br>
{{ $pending->appends($_GET)->links() }}


{{-- modalket --}}
<form action="{{ route('penyiapan.simpanketerangan') }}" method="post" id="Fket">
    @csrf
    <div class="modal fade" id="keteranganModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Input Alasan Tidak Terkirim</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <h6>Alasan</h6>
                        <input type="hidden" class="form-control item-id-ket" name="item_id" placeholder="item_id">
                        <input type="hidden" class="form-control order-item-id-ket" name="id" placeholder="item_id">
                        <input type="hidden" class="form-control order-id-ket" name="order_id" placeholder="item_id">
                        <input type="text" class="form-control item-ket" name="keterangan" placeholder="Keterangan">
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="product_id" class="product_id">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary ">Simpan</button>
                </div>
            </div>
        </div>
    </div>
</form>

</div>

<script>
    $('.pagination a').on('click', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        $.ajax({
            url: url,
            method: "GET",
            success: function(response) {
                $('#show').html(response);
            }

        });
    });

    var btnContainer = document.getElementById("alokasi-tab");
    var btns = btnContainer.getElementsByClassName("select-asal");

    // Loop through the buttons and add the active class to the current/clicked button
    for (var i = 0; i < btns.length; i++) {
        btns[i].addEventListener("click", function() {
            var current = document.getElementsByClassName("active");
            current[0].className = current[0].className.replace("active", "");
            this.className += " active";
        });
    }
</script>

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
    var selected_tanggal_akhir = $('#selected_tanggal_akhir').val();
    var pencarian = $('#pencarian-stock').val();

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
        var url_pemenuhan = "{{ route('penyiapan.pemenuhan') }}" + "?order_item_id=" + id;
        $('#order_bahan_baku' + id).load(url_pemenuhan)
    });

    $('.fulfill-item').on('click', function() {
        var orderitemid = $(this).attr('data-id');
        $(".fulfill-item").hide();
        console.log("{{ route('penyiapan.fulfillitem') }}?order_item_id=" + orderitemid)
        $.ajax({
            url: "{{ route('penyiapan.fulfillitem') }}?order_item_id=" + orderitemid,
            type: 'get',
            success: function(data) {
                var url_pemenuhan = "{{ route('penyiapan.pemenuhan') }}" + "?order_item_id=" +
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
            url: "{{ route('penyiapan.simpanalokasi') }}",
            type: 'post',
            dataType: 'json',
            data: $(this).serialize(),
            success: function(data) {

                if (data.status == 400) {
                    showAlert(data.msg);
                } else {
                    id = $('#selected_order_item_id').val();
                    var url_pemenuhan = "{{ route('penyiapan.pemenuhan') }}" + "?order_item_id=" +
                        id;
                    console.log(url_pemenuhan);
                    $('#order_bahan_baku' + id).load(url_pemenuhan);
                    $('#riwayat_ambil').load("{{ route('penyiapan.pemenuhan') }}?order_item_id=" +
                        id);
                    $('#info_order').load(
                        "{{ route('penyiapan.pemenuhan') }}?key=info&order_item_id=" + id);
                    $(".qty_item").val('');
                    $(".berat_item").val('');
                    load_bahan_baku();

                }
                // $('#exampleModal').modal('toggle');

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
        selected_tanggal_akhir = $('#selected_tanggal_akhir').val();

        $('#info_order').load("{{ route('penyiapan.pemenuhan') }}?key=info&order_item_id=" + orderitemid);
        $('#riwayat_ambil').load("{{ route('penyiapan.pemenuhan') }}?order_item_id=" + orderitemid);

        load_bahan_baku();
    }

    $('#selected_tanggal').on('change', function() {
        tanggal = $(this).val();
        load_bahan_baku();
    })
    $('#selected_tanggal_akhir').on('change', function() {
        selected_tanggal_akhir = $(this).val();
        load_bahan_baku();
    })

    $('#pencarian-stock').on('keyup', function() {
        pencarian = $(this).val();
        load_bahan_baku();
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
        console.log(id);
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
            url: "{{ route('penyiapan.chiller') }}" + '?tanggal=' + tanggal + '&item_id=' + item_id+'&tanggal_akhir='+ selected_tanggal_akhir+'&pencarian='+ pencarian,
            method: "GET",
            success: function(data) {
                $('#list-penyiapan-chiller').html(data);
                console.log("{{ route('penyiapan.chiller') }}" + '?tanggal=' + tanggal + '&item_id=' + item_id +'&tanggal_akhir='+ selected_tanggal_akhir+'&pencarian='+ pencarian)
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
            url: "{{ route('penyiapanfrozen.storage') }}" + '?tanggal=' + tanggal + '&item_id=' + item_id +'&tanggal_akhir='+ selected_tanggal_akhir+'&pencarian='+ pencarian,
            method: "GET",
            success: function(data) {
                $('#list-penyiapan-chiller').html(data);
                console.log("{{ route('penyiapanfrozen.storage') }}" + '?tanggal=' + tanggal + '&item_id=' + item_id +'&tanggal_akhir='+ selected_tanggal_akhir+'&pencarian='+ pencarian)
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

        var url_sampingan = "{{ route('sampingan.chiller') }}" + '?tanggal=' + tanggal + '&item_id=' + item_id +'&tanggal_akhir='+ selected_tanggal_akhir;
        console.log(url_sampingan);

        $.ajax({
            url: "{{ route('sampingan.chiller') }}" + '?tanggal=' + tanggal + '&item_id=' + item_id +'&tanggal_akhir='+ selected_tanggal_akhir,
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

    
</script>

<script>
    $('.show-ket').on('click', function(e) {
        const id_it = $(this).data('item_id');
        const id_order_it = $(this).data('order_id');
        const keterangan = $(this).data('keterangan');
        const order_item_id = $(this).data('order_item_id');

        $('.item-id-ket').val(id_it);
        $('.order-id-ket').val(id_order_it);
        $('.item-ket').val(keterangan);
        $('.order-item-id-ket').val(order_item_id);
    });
</script>