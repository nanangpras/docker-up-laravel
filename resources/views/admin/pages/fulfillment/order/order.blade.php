@if (count($pending) == '0')
<div class="alert alert-danger">Item Order Belum Tersedia</div>
@endif

<div class="">
    <div class="row">
        <div class="col">
            <b>Total Order : {{ $status_order['semua_order'] }}</b>
        </div>
        <div class="col">
            <b>Pending  : {{ $status_order['pending_order'] }}</b>
        </div>
        <div class="col">
            <b>Selesai : {{ $status_order['selesai_order'] }}</b>
        </div>
        <div class="col">
            <b>Closed : {{ $status_order['batal_order'] }}</b>
        </div>
        <div class="col text-right">
            <a href="{{ route('fulfillment.index', ['key' => 'unduh']) }}&tanggal={{ $tanggal }}"
                class="btn btn-success"><span class="fa fa-file"></span> Export CSV</a>
        </div>
    </div>
</div>
<hr>

@foreach ($pending as $i => $val)

<div @if($val->status_so=="Closed")
    style="border: 2px solid red; padding: 5px; border-radius: 3px"
    @endif
    >
    <b style="font-size: 9pt">
        @if($val->status_so=="Closed")
        <div class="status status-danger">ORDER CLOSED</div>
        @endif
        <a href="{{url('/admin/laporan/sales-order/'.$val->id)}}" target="_blank">
            {{ $loop->iteration + ($pending->currentpage() - 1) * $pending->perPage() }}.
        </a>
        <a href="{{ route('editso.index', $val->id) }}" target="_blank">{{ $val->no_so }}</a>
        || {{ $val->nama }} ||
        <a href="javascript:void(0)" id="reload-order-{{$val->id}}"><span class="fa fa-refresh"></span></a>
        <span class="pull-right">
            {{ $val->sales_channel }} || Kirim : {{ date('d/m/y', strtotime($val->tanggal_kirim)) }}
        </span>
        @if($val->status_so==NULL)
        <span class="status status-warning">PENDING</span>
        @endif
    </b>


    @if ($val->keterangan)
    <br>{{ $val->keterangan }}
    @endif

    <div id="order-item-{{$val->id}}" class="load-order-item" data-id="{{ $val->id }}"
        id="order_bahan_baku{{ $val->id }}">
        <img src="{{ asset('loading.gif') }}" style="width: 18px"> Loading ...
    </div>
</div>
<hr>

@endforeach



<div style="min-width: 200px">

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        @csrf
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form action="{{ route('fulfillment.simpanalokasi') }}" method="POST" id="submit-alokasi">
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
                                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) @endif id="selected_tanggal" class="form-control" value="{{ date('Y-m-d', strtotime('-3 day')) }}" min="2023-05-27">
                            </div>
                            <div class="col pl-1">
                                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) @endif id="selected_tanggal_akhir" class="form-control" value="{{ date('Y-m-d') }}" min="2023-05-27">
                            </div>
                            @else
                            <div class="col pr-1">
                                <input type="date" @if(env('NET_SUBSIDIARY', 'EBA' )=='EBA' ) @endif id="selected_tanggal" class="form-control" value="{{ date('Y-m-d', strtotime('-3 day')) }}" min="2023-05-05">
                            </div>
                            <div class="col pl-1">
                                <input type="date" @if(env('NET_SUBSIDIARY', 'EBA' )=='EBA' ) @endif id="selected_tanggal_akhir" class="form-control" value="{{ date('Y-m-d') }}" min="2023-05-05">
                            </div>
                            @endif
                        </div>
                        <div class="row">
                            <div class="col">
                                <input type="text" id="pencarian-stock" class="form-control mt-2" value=""
                                    placeholder="Pencarian">
                                <a href="javascript:void(0)" class="btn btn-sm btn-red"
                                    style="float: right; margin-top: -32px; margin-right: 7px "
                                    onclick="return clearSearch()">Clear</a>
                            </div>
                        </div>
                        <div class="mb-2 mt-2" id="alokasi-tab">
                            <a href="javascript:void(0)" class="btn btn-outline-success select-asal" id="select-chiller-fg">Chiller FG</a>
                            <a href="javascript:void(0)" class="btn btn-outline-success select-asal" id="select-chiller-bb">Chiller BB</a>
                            <a href="javascript:void(0)" class="btn btn-outline-success select-asal" id="select-gudang">Gudang CS</a>
                        </div>

                        <div id="info_order"></div>
                        <div class="my-2">
                            Item sudah diambil :
                            <div id="riwayat_ambil"></div>
                        </div>
                        <div id="loading-ambil" class="text-center" style="display: none">
                            <img src="{{ asset('loading.gif') }}" width="20px">
                        </div>
                        <div id="list-data-stock"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary alokasi_input">Simpan</button>
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

    // var btnContainer = document.getElementById("alokasi-tab");
    // var btns = btnContainer.getElementsByClassName("select-asal");

    // // Loop through the buttons and add the active class to the current/clicked button
    // for (var i = 0; i < btns.length; i++) {
    //     btns[i].addEventListener("click", function() {
    //         var current = document.getElementsByClassName("active");
    //         current[0].className = current[0].className.replace("active", "");
    //         this.className += " active";
    //     });
    // }
    $(document).ready(function () {
        $(".select-asal").on('click', function () {
            var tanggal = $(this).val();
            $(this).siblings().removeClass('active');
            $(this).addClass('active');

        });
    });
</script>

<style>
    #alokasi-tab .select-asal.active {
        background: rgb(0, 81, 0);
    }
</style>

<script>
    var item_id                 = "";
    var order_id                = "";
    var order_item_id           = "";
    var divisi                  = "{{$divisi}}";
    var nama_customer           = "";
    var lokasi                  = "chiller-fg";
    var tanggal                 = $('#selected_tanggal').val();
    var selected_tanggal_akhir  = $('#selected_tanggal_akhir').val();
    var pencarian               = encodeURIComponent($('#pencarian-stock').val());
    var jenis                   = $('#select-jenis').val();


    $('.load-order-item').each(function(i) {
        var id = $(this).attr('data-id');
        loadOrderItem(id)

    });

    function loadOrderItem(id){

        var url_load_order_list = "{{ route('fulfillment.orderitem') }}" + "?id=" + id + "&jenis=" + jenis + "&divisi=" + divisi;
        // console.log(url_load_order_list);

        $('#order-item-' + id).load(url_load_order_list, function(){
            $('#reload-order-'+id).on('click', function(){
                loadOrderItem(id);
            })

        })
    }

    // load_bahan_baku();

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
        var url_pemenuhan = "{{ route('fulfillment.pemenuhan') }}" + "?order_item_id=" + id;
        $('#order_bahan_baku' + id).load(url_pemenuhan)
    });

    function deletePemenuhan(delete_id, orderitemid, order_id) {
        $.ajax({
            url: "{{ route('fulfillment.deletealokasi') }}?id=" + delete_id,
            type: 'get',
            success: function(data) {
                var url_pemenuhan = "{{ route('fulfillment.pemenuhan') }}" + "?order_item_id=" +
                    orderitemid;
                $('#order_bahan_baku' + orderitemid).load(url_pemenuhan)
                $('#info_order').load("{{ route('fulfillment.pemenuhan') }}?key=info&order_item_id=" +
                    orderitemid);
                $('#riwayat_ambil').load("{{ route('fulfillment.pemenuhan') }}?order_item_id=" +
                    orderitemid);
                load_bahan_baku()
                loadOrderItem(order_id);
            }
        });
    }



    $('#submit-alokasi').on('submit', function(e) {
        e.preventDefault();

        $(".alokasi_input").hide() ;

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{ route('fulfillment.simpanalokasi') }}",
            type: 'post',
            dataType: 'json',
            data: $(this).serialize(),
            beforeSend: function(){
                showNotif("Sedang Diproses ...")
            },
            success: function(data) {

                if (data.status == 400) {
                    showAlert(data.msg);
                    $(".alokasi_input").show() ;
                } else {
                    id = $('#selected_order_item_id').val();
                    order_id = $('#selected_order_id').val();
    
                    var url_pemenuhan = "{{ route('fulfillment.pemenuhan') }}" + "?order_item_id=" +
                        id;
                    // console.log(url_pemenuhan);
                    $('#order_bahan_baku' + id).load(url_pemenuhan);
                    $('#riwayat_ambil').load("{{ route('fulfillment.pemenuhan') }}?order_item_id=" +
                        id);
                    $('#info_order').load(
                        "{{ route('fulfillment.pemenuhan') }}?key=info&order_item_id=" + id);
                    $(".qty_item").val('');
                    $(".berat_item").val('');
                    load_bahan_baku();
    
                    loadOrderItem(order_id);
                    $(".alokasi_input").show() ;
                }
                // $('#exampleModal').modal('toggle');

            }
        });

    })

    function selected_id(orderid, orderitemid, itemid, item_lokasi, nama_c) {

        // console.log(itemid)
        $('#selected_order_id').val(orderid);
        $('#selected_item_id').val(itemid);
        $('#selected_order_item_id').val(orderitemid);

        item_id         = $('#selected_item_id').val();
        order_id        = $('#selected_order_id').val();
        order_item_id   = $('#selected_order_item_id').val();
        nama_customer   = nama_c;
        
        subsidiary      = "{{env('NET_SUBSIDIARY')}}";
        
        if(subsidiary=="EBA"){
            pencarian       = encodeURIComponent(nama_customer);
            $('#pencarian-stock').val(nama_customer);
        }

        lokasi          = item_lokasi;

        if(lokasi=="frozen"){
            // tanggal         = "{{date('Y-m-d',strtotime('-3 month'))}}";
            $('#selected_tanggal').val(tanggal)
        }else{
            tanggal         = $('#selected_tanggal').val();
        }
        selected_tanggal_akhir = $('#selected_tanggal_akhir').val();

        $('#info_order').load("{{ route('fulfillment.pemenuhan') }}?key=info&order_item_id=" + orderitemid);
        $('#riwayat_ambil').load("{{ route('fulfillment.pemenuhan') }}?order_item_id=" + orderitemid);

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
        pencarian = encodeURIComponent($(this).val());
        load_bahan_baku();
    })

    function clearSearch(){
        $('#pencarian-stock').val("");
        pencarian = "";
        load_bahan_baku();
    }

    $(".batalkanorder").click(function() {

        if (!confirm('Batalkan Fulfill?')) return false;

        var id = $(this).data('batal');
        // console.log(id);
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

    // $(".close_order").click(function() {
    //     if ($(this).data('status') == 0) {
    //         if (!confirm('Open Orderan?')) return false;
    //     } else {
    //         if (!confirm('Close Orderan?')) return false;
    //     }

    //     var id = $(this).data('batal');
    //     console.log(id);
    //     $.ajaxSetup({
    //         headers: {
    //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //         }
    //     });

    //     $.ajax({
    //         url: "{{ route('penyiapan.batalorder') }}",
    //         method: "POST",
    //         data: {
    //             id: id,
    //             key: 'close'
    //         },
    //         success: function(data) {
    //             showNotif(data.msg);
    //             location.reload()
    //         }
    //     })
    // })

    function load_bahan_baku() {

        // console.log(lokasi);

        if (lokasi == "chiller-fg") {
            load_chiller_fg();
        }
        if (lokasi == "chiller-bb") {
            load_chiller_bb();
        }
        if (lokasi == "frozen") {
            load_product_gudang();
        }
    }

    var filterLoadChillerFG = null;  

    function load_chiller_fg() {

        $('#loading-ambil').show();
        if (filterLoadChillerFG != null) {
            clearTimeout(filterLoadChillerFG);
        }

        filterLoadChillerFG = setTimeout(function() {
            filterLoadChillerFG = null;  
            $.ajax({
                url: "{{ route('fulfillment.data_chiller_fg') }}" + '?tanggal=' + tanggal + '&item_id=' + item_id+'&tanggal_akhir='+ selected_tanggal_akhir+'&pencarian='+ pencarian,
                method: "GET",
                success: function(data) {
                    $('#list-data-stock').html(data);
                    $('.selected-penyiapan-chiller').on('click', function() {
                        var id = $(this).attr('data-id');
                        var nama = $(this).attr('data-nama');
                        var berat = $(this).attr('data-berat');
                        focusCode(id, id, nama, berat);
                    })
                    $('#loading-ambil').hide();
                }
            })
        }, 1000); 

    }


    var filterLoadProductGudang = null;  
    function load_product_gudang() {

        $('#loading-ambil').show();

        if (filterLoadProductGudang != null) {
            clearTimeout(filterLoadProductGudang);
        }

        filterLoadProductGudang = setTimeout(function() {
            filterLoadProductGudang = null;  
            $.ajax({
                url: "{{ route('fulfillment.data_product_gudang') }}" + '?tanggal=' + tanggal + '&item_id=' + item_id +'&tanggal_akhir='+ selected_tanggal_akhir+'&pencarian='+ pencarian,
                method: "GET",
                success: function(data) {
                    $('#list-data-stock').html(data);
                    $('.selected-penyiapanfrozen-storage').on('click', function() {
                        var id = $(this).attr('data-id');
                        var nama = $(this).attr('data-nama');
                        var berat = $(this).attr('data-berat');

                        focusCode(id, id, nama, berat);
                    })
                    $('#loading-ambil').hide();
                }
            })
        }, 1000); 
    }


    var filterLoadChillerBB = null;  
    function load_chiller_bb() {
        $('#loading-ambil').show();

        var url_sampingan = "{{ route('fulfillment.data_chiller_bb') }}" + '?tanggal=' + tanggal + '&item_id=' + item_id +'&tanggal_akhir='+ selected_tanggal_akhir;
       
        if (filterLoadChillerBB != null) {
            clearTimeout(filterLoadChillerBB);
        }

        filterLoadChillerBB = setTimeout(function() {
            filterLoadChillerBB = null;  
            $.ajax({
                url: "{{ route('fulfillment.data_chiller_bb') }}" + '?tanggal=' + tanggal + '&item_id=' + item_id +'&tanggal_akhir='+ selected_tanggal_akhir,
                method: "GET",
                success: function(data) {
                    $('#list-data-stock').html(data);

                    $('.selected-sampingan-chiller').on('click', function() {
                        var id = $(this).attr('data-id');
                        var nama = $(this).attr('data-nama');
                        var berat = $(this).attr('data-berat');

                        focusCode(id, id, nama, berat);
                    })
                    $('#loading-ambil').hide();
                }
            })
        }, 1000); 
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

        $('.item-id-ket').val(id_it);
        $('.order-id-ket').val(id_order_it);
        $('.item-ket').val(keterangan);
    });
</script>