<div class="card mb-3">
    <div class="card-body p-2">
        <div class="row">
            <div class="col-sm-4 col-lg mb-2 pr-sm-1">
                <div class="card">
                    <div class="card-header">Total Customer</div>
                    <div class="card-body p-2">
                        <div class="row mb-1">
                            <div class="col ">
                                <div class="border text-center">
                                    <div class="font-weight-bold">{{ number_format($totalsum['sumcustomer']) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 col-lg mb-2 px-sm-1">
                <div class="card">
                    <div class="card-header">Total Qty</div>
                    <div class="card-body p-2">
                        <div class="row mb-1">
                            <div class="col ">
                                <div class="border text-center">
                                    <div class="font-weight-bold">{{ number_format($totalsum['sumqty']) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 col-lg mb-2 px-sm-1">
                <div class="card">
                    <div class="card-header">Total Berat</div>
                    <div class="card-body p-2">
                        <div class="row mb-1">
                            <div class="col ">
                                <div class="border text-center">
                                    <div class="font-weight-bold">{{ number_format($totalsum['sumberat']) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 col-lg mb-2 pl-sm-1">
                <div class="card">
                    <div class="card-header">Total Order</div>
                    <div class="row">
                        <div class="col">
                            <div class="border text-center">
                                <div class="font-weight-bold">{{ number_format($totalsum['sumitemfresh'] +
                                    $totalsum['sumitemfrozen']) }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-2">
                        <div class="row mb-1">
                            <div class="col pr-1">
                                <div class="border text-center">
                                    <div class="small">Fresh</div>
                                    <div class="font-weight-bold">{{ number_format($totalsum['sumitemfresh']) }}</div>
                                </div>
                            </div>
                            <div class="col pl-1">
                                <div class="border text-center">
                                    <div class="small">Frozen</div>
                                    <div class="font-weight-bold">{{ number_format($totalsum['sumitemfrozen']) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<a href="{{ route('regu.request_order', array_merge(['get' => 'unduh'], $_GET)) }}"
    class="btn btn-sm btn-success float-right mb-3">Unduh</a>
<div class="table-responsive">
    <table class="table table-sm table-hover table-striped table-bordered table-small">
        <thead>
            <tr>
                <th class="text-center" rowspan="2">No</th>
                <th class="text-center" rowspan="2">MKT</th>
                <th class="text-center" rowspan="2">Customer</th>
                <th class="text-center" rowspan="2">Item</th>
                <th class="text-center" rowspan="2">Kat.Item</th>
                <th class="text-center" rowspan="2">Jenis</th>
                {{-- <th class="text-center" rowspan="2">Part</th> --}}
                <th class="text-center" rowspan="2">Bumbu</th>
                <th class="text-center" rowspan="2">Memo</th>
                <th class="text-center" rowspan="2">Memo Internal</th>
                <th class="text-center" colspan="3">Order</th>
                {{-- <th class="text-center" colspan="2">Fulfillment</th> --}}
                <th class="text-center" rowspan="2">Edit</th>
                <th class="text-center" rowspan="2">Input</th>
                <th class="text-center" rowspan="2">Aksi</th>
            </tr>
            <tr>
                <th class="text-center">Part</th>
                <th class="text-center">Ekor/Pcs/Pack</th>
                {{-- <th class="text-center">Pack</th> --}}
                <th class="text-center">Berat</th>
                {{-- <th class="text-center">Qty</th>
                <th class="text-center">Berat</th> --}}
            </tr>
        </thead>
        <tbody>
            @foreach ($alldata as $row)
            <tr class="small" @if($row->order_status_so=="Closed") style="background-color: red; color:white" @endif
                @if($row->edit_item==1) style="background-color: #FFFF8F" @endif
                @if($row->edit_item==2) style="background-color: #FFEA00" @endif
                @if($row->edit_item==3) style="background-color: #FDDA0D" @endif
                @if($row->delete_at_item!=NULL) style="background-color: red; color:white" @endif
                >
                <td>{{ $loop->iteration }}</td>
                <td>
                    {{$row->marketing_nama}}
                </td>
                <td>{{ $row->nama }}<br>
                    <span class="small">#{{$row->id}}||{{$row->no_so}}</span>
                </td>
                <td>{{ $row->nama_detail }}
                    @if(date('Y-m-d', strtotime($row->created_at_order))==$row->tanggal_kirim)
                    <br><span class="small red">*Prioritas Same Day</span>
                    @endif
                </td>
                <td>{{ $row->item->itemkat->nama }}</td>
                <td>
                    @php
                    $jenis = "<span class='small'>FRESH</span>";
                    if (str_contains($row->nama_detail, 'FROZEN')) {
                    $jenis = "<span class='small'>FROZEN</span>";
                    }
                    @endphp
                    {!!$jenis!!}
                </td>
                <td>{{ $row->bumbu }}</td>
                <td>@if($row->memo_header){{ $row->memo_header }}
                    <hr> @endif{{ $row->memo }}
                </td>
                <td>@if (App\Models\Order::getInternalMemo($row->itemorder->no_so, $row->id) != ''){{ App\Models\Order::getInternalMemo($row->no_so, $row->id) }} @else - @endif</td>
                <td class="text-right"> {{ $row->part }}</td>
                <td class="text-right">{{ number_format($row->qty, 2) }}</td>

                <td class="text-right">{{ number_format($row->berat, 2) }}</td>
                {{-- <td class="text-right">{{ number_format($row->fulfillment_qty) }}</td>
                <td class="text-right">{{ number_format($row->fulfillment_berat, 2) }}</td> --}}
                <td> @if($row->edit_item>0)<br><span class="text-small status status-warning">Edit{{$row->edit_item}}
                    </span>@endif
                    @if($row->delete_at_item!=NULL) <br><span class="text-small status status-danger">Batal
                    </span>@endif</td>
                <td>{{count($row->free_stock_multi)}}</td>
                <td>


                    @php
                    $show_true = FALSE;
                    @endphp

                    @if (($row->item->category_id == 5) || ($row->item->category_id == 11))
                    @php
                    $show_true = App\Models\User::setIjin(8) ? TRUE : FALSE ;
                    @endphp
                    @endif
                    @if ($row->item->category_id == 2)
                    @php
                    $show_true = App\Models\User::setIjin(9) ? TRUE : FALSE ;
                    @endphp
                    @endif
                    @if (($row->item->category_id == 3) || ($row->item->category_id == 9))
                    @php
                    $show_true = App\Models\User::setIjin(10) ? TRUE : FALSE ;
                    @endphp
                    @endif
                    @if ($row->item->category_id == 1)
                    @php
                    $show_true = App\Models\User::setIjin(11) ? TRUE : FALSE ;
                    @endphp
                    @endif
                    @if (($row->item->category_id == 7) || ($row->item->category_id == 8) ||
                    ($row->item->category_id == 9) || ($row->item->category_id == 13))
                    @php
                    $show_true = App\Models\User::setIjin(12) ? TRUE : FALSE ;
                    @endphp
                    @endif



                    @if (count($row->free_stock_multi)>0)

                    @foreach($row->free_stock_multi as $mt)
                    @if (((Auth::user()->account_role == 'superadmin') || App\Models\User::setIjin(33)) ? TRUE :
                    (($row->user_id == Auth::user()->id) ? TRUE : $show_true))
                    @if ($regu == 'byproduct')
                    <a href="{{ route('evis.peruntukan', ['produksi' => $mt->id]) }}"
                        class="btn btn-primary btn-block btn-sm py-0 rounded-0" target="_blank"><span
                            class="fa fa-eye"></span></a>
                    @else


                    @php
                    $adminDetail = App\Models\Freestock::where('id', $mt->id)->first()->regu ?? $regu;
                    @endphp

                    <a href="{{ route('regu.index', ['kategori' => $adminDetail, 'produksi' => $mt->id]) }}"
                        class="btn btn-primary btn-block btn-sm py-0 rounded-0" target="_blank"><span
                            class="fa fa-eye"></span></a>
                    @endif
                    @endif
                    @endforeach
                    <button class="btn btn-info btn-block btn-sm py-0 rounded-0 btn-process-input"
                        data-id="{{ $row->id }}" data-toggle="modal" data-target="#proses{{ $row->id }}"><span
                            class="fa fa-plus-circle"></span></button>

                    @else
                    @if (Session::get('subsidiary') == 'EBA')
                    <button class="btn btn-success btn-block btn-sm py-0 rounded-0 btn-process-input"
                        data-id="{{ $row->id }}" data-toggle="modal" data-target="#proses{{ $row->id }}">Input</button>
                    @endif
                    @endif

                    <div class="modal fade" id="proses{{ $row->id }}" aria-labelledby="proses{{ $row->id }}Label"
                        aria-hidden="true">
                        <div class="modal-dialog" style="max-width:1200px">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="proses{{ $row->id }}Label">Produksi Request Order</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">

                                    <div class="form-group">
                                        <label class="status status-danger form-control"> 
                                            <input type="checkbox" class="" name="netsuite_send" placeholder="WO" id="netsuite_send{{ $row->id }}"> <b>Input Bahan Baku</b>
                                        </label>
                                    </div>
                                    <div class="form-group">
                                        @if (count($row->free_stock_multi)>0)
                                        <a href="{{ route('regu.request_order',['key' => 'view_modal_byorder']) }}"
                                            class="btn btn-success btn-sm p-0 px-1 viewinputbyorder"
                                            title='Lihat Hasil Penginputan' data-toggle="modal" data-target="#modalView"
                                            data-inputbyid="{{$row->id}}">
                                            <button type="button" class="btn btn-success"> Lihat Hasil Penginputan
                                            </button>
                                        </a>
                                        @endif
                                    </div>
                                    <div id="select-item-bb{{ $row->id }}" style="display:none">
                                        <h6>Ambil Item Produksi</h6>
                                        <div class="form-group">
                                            Pencarian Tanggal
                                            <div class="row">
                                                <div class="col">
                                                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' )
                                                        min="2023-01-01" @endif name="tanggal"
                                                        class="form-control tanggal-fg" value="{{ date('Y-m-d') }}"
                                                        id="tanggal-fg{{ $row->id }}" placeholder="Cari...."
                                                        autocomplete="off">
                                                </div>
                                                <div class="col">
                                                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' )
                                                        min="2023-01-01" @endif name="tanggal"
                                                        class="form-control tanggal-fg-2" value="{{ date('Y-m-d') }}"
                                                        id="tanggal-fg-2{{ $row->id }}" placeholder="Cari...."
                                                        autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                        Sumber Asal Bahan Baku :  <br /> 
                                        {{-- <label class="btn btn-success">
                                            <input type="radio" name="bbtype{{$row->id}}" class="bbtype"
                                                value="bahan-baku"> Bahan Baku
                                        </label> --}}
                                        <label class="btn btn-warning">
                                            <input type="checkbox" name="bbtype{{$row->id}}" class="bbtype"
                                                value="hasil-produksi"> WIP
                                        </label>
                                        <div id="loading-input" class="text-center" style="display: none">
                                            <img src="{{ asset('loading.gif') }}" width="20px">
                                        </div>
                                        <form id="form-bb{{$row->id}}" method="post" enctype="multipart/form-data">
                                            <div id="bahanbaku-fg{{ $row->id }}"></div>
                                        </form>
                                    </div>
                                    <hr>
                                    <div class="row mb-3">
                                        <div class="col-8 pr-1">
                                            Item
                                            <input type="text" id="form-item{{ $row->id }}"
                                                value="{{ $row->nama_detail }}" class="form-control" readonly>
                                        </div>
                                        <div class="col-2 px-1">
                                            Qty
                                            <input type="number" name="jumlah" id="jumlah{{ $row->id }}"
                                                value="{{ $row->qty }}" class="form-control form-control-sm p-1"
                                                placeholder="Qty" autocomplete="off">
                                        </div>
                                        <div class="col-2 pl-0">
                                            Berat
                                            <input type="number" name="berat" id="berat{{ $row->id }}"
                                                value="{{ $row->berat }}" class="form-control form-control-sm p-1"
                                                step="0.01" placeholder="Berat" autocomplete="off">
                                        </div>
                                    </div>

                                    <div class="row">
                                        @if ($row->item->itemkat->nama == 'Parting' or $row->item->itemkat->nama =='Marinated' or $row->item->itemkat->nama == 'M' )
                                        <div class="col-4 pr-0">
                                            <div class="form-group">
                                                Jumlah Parting
                                                <input type="number" name="part"
                                                    class="form-control form-control-sm p-1" id="part{{ $row->id }}"
                                                    value="{{ $row->part ?? '' }}" placeholder="Parting"
                                                    autocomplete="off">
                                            </div>
                                        </div>
                                        @endif
                                        <div
                                            class="{{ ($row->item->itemkat->nama == 'Parting' or $row->item->itemkat->nama == 'Marinated') ? 'col-8' : 'col' }}">
                                            <div class="row">
                                                <div class="col-9 pr-1">
                                                    <div class="form-group">
                                                        Plastik
                                                        <select name="plastik" id="plastik{{ $row->id }}"
                                                            class="form-control select2" data-width="100%"
                                                            data-placeholder="Pilih Plastik">
                                                            <option value=""></option>
                                                            <option value="Curah">Curah</option>
                                                            @php
                                                            $plastik = \App\Models\Item::where('category_id',
                                                            '25')->where('subsidiary', env('NET_SUBSIDIARY',
                                                            'EBA'))->where('status', '1')->get();
                                                            @endphp
                                                            @foreach ($plastik as $p)
                                                            <option value="{{ $p->id }}">{{ $p->nama }} -
                                                                {{$p->subsidiary}}{{ $p->netsuite_internal_id }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-3 pl-1">
                                                    &nbsp;
                                                    <input type="number" name="jumlah_plastik"
                                                        id="jumlah_plastik{{ $row->id }}"
                                                        class="form-control form-control-sm" placeholder="Jumlah">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        Nama Bumbu / Keterangan
                                        <input type="text" name="sub_item" class="form-control form-control-sm"
                                            id="sub_item{{ $row->id }}" value="{{ $row->bumbu ?? '' }}"
                                            placeholder="Keterangan" autocomplete="off">
                                    </div>

                                    <div class="row">
                                        <div class="col-4 pr-1">
                                            <div class="form-group">
                                                Unit
                                                <input type="text" id="unit{{ $row->id }}" placeholder="Tuliskan Unit"
                                                    value="keranjang" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-4 px-1">
                                            <div class="form-group">
                                                Jumlah Keranjang
                                                <input type="number" min="0" id="jumlah_keranjang{{ $row->id }}"
                                                    placeholder="Tuliskan Jumlah Keranjang" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-4 pl-1">
                                            <div class="form-group">
                                                Kode Produksi
                                                <input type="text" id="kode_produksi{{ $row->id }}"
                                                    placeholder="Tuliskan Kode Produksi" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                    @if (env("NET_SUBSIDIARY", "EBA") == "EBA")
                                    <div class="form-group">
                                        Regu
                                        <select name="regu" id="regu{{ $row->id }}" class="form-control select2">
                                            <option value="boneless" {{ $regu=='boneless' ? 'selected' : '' }}>Boneless
                                            </option>
                                            <option value="parting" {{ $regu=='parting' ? 'selected' : '' }}>
                                                Parting</option>
                                            <option value="marinasi" {{ $regu=='marinasi' ? 'selected' : '' }}>Parting
                                                M</option>
                                            <option value="whole" {{ $regu=='whole' ? 'selected' : '' }}>Whole Chicken
                                            </option>
                                            <option value="byproduct" {{ $regu=='byproduct' ? 'selected' : '' }}>Evis
                                            </option>
                                        </select>
                                    </div>
                                    @else
                                    <input type="hidden" id="regu{{ $row->id }}" value="{{ $regu }}">
                                    @endif

                                    <div class="form-group">
                                        <label>Alasan Tidak Terpenuhi</label>
                                        <input type="text" class="form-control form-control-sm" name="alasan"
                                            placeholder="Alasan" id="alasan{{ $row->id }}">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary input_order"
                                        data-item="{{ $row->item_id }}" data-page="{{ $request->page ?? '' }}"
                                        data-id="{{ $row->id }}">Proses</button>
                                </div>
                            </div>
                        </div>
                </td>
            </tr>

            @endforeach
        </tbody>
    </table>
</div>

<div class="modal fade" id="modalView" data-keyboard="false" aria-labelledby="modalViewLabel" aria-hidden="false">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #f1f1f1;">
                <h5 class="modal-title">Hasil Penginputan </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="contentView"></div>
        </div>
    </div>

    <script>
        $(".select2").select2({
    theme: "bootstrap4"
});
    </script>

    <script>
        var id              =   "" ;
    var item            =   "" ;
    var page            =   "" ;

    $(".input_order").on('click', function() {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    id              =   $(this).data('id') ;
    item            =   $(this).data('item') ;
    page            =   $(this).data('page') ;

    // console.log(id);

    var menunggu        =   $("#menunggu:checked").val();
    var tanggal         =   $("#tanggal_request").val();
    var cari            =   encodeURIComponent($("#cari_request").val());

    var plastik         =   $('#plastik' + id).val();
    var jumlah_plastik  =   $('#jumlah_plastik' + id).val();
    var parting         =   $('#part' + id).val();
    var berat           =   $('#berat' + id).val();
    var jumlah          =   $('#jumlah' + id).val();
    var sub_item        =   $('#sub_item' + id).val();
    var regu            =   $('#regu' + id).val();
    var alasan          =   $('#alasan' + id).val();

    var unit                =   $("#unit" + id).val() ;
    var jumlah_keranjang    =   $("#jumlah_keranjang" + id).val() ;
    var kode_produksi       =   $("#kode_produksi" + id).val() ;

    if (plastik == 'Curah') {
        var next = 'TRUE';
    } else {
        if (jumlah_plastik > 0) {
            var next = 'TRUE';
        }
    }

    // var netsuite_send = "FALSE";
    // if($('#netsuite_send' + id).get(0).checked) {
    //     // something when checked
    //     netsuite_send = "TRUE";
    // }else{
    //     netsuite_send = "FALSE";
    // }

    // console.log("TIDAK KIRIM WO "+netsuite_send);

    // return false;

    var data_form = $('#form-bb'+id).serializeArray();

    if (next != 'TRUE') {
        showAlert('Lengkapi data plastik');
    } else {

        $(".input_order").hide() ;


        $.ajax({
            url: "{{ route('regu.store') }}",
            method: "POST",
            data: {
                jenis           :   regu,
                item            :   item,
                berat           :   berat,
                jumlah          :   jumlah,
                parting         :   parting,
                plastik         :   plastik,
                jumlah_plastik  :   jumlah_plastik,
                tujuan_produksi :   'chillerfg',
                sub_item        :   sub_item,
                alasan          :   alasan,
                unit            :   unit,
                jumlah_keranjang:   jumlah_keranjang,
                kode_produksi   :   kode_produksi,
                orderitem       :   id,
                tujuan_produksi :   '2',
                // netsuite_send   :   netsuite_send,
                data_form       :   data_form
            },
            success: function(data) {
                // console.log(data)

                if (data.status == 400) {
                    showAlert(data.msg);
                    $('.modal-backdrop').remove();
                    $('body').removeClass('modal-open');

                    $("#data_request").attr("style", "display: none") ;
                    $("#loading_request").attr("style", "display: block") ;
                    $("#data_request").load("{{ route('regu.request_order', ['key' => 'view']) }}&regu={{ $regu }}&tanggal=" + tanggal + "&cari=" + cari + "&menunggu=" + menunggu + "&page=" + page, function() {
                        $("#data_request").attr("style", "display: block") ;
                        $("#loading_request").attr("style", "display: none") ;
                    }) ;
                    // $(".input_order").show() ;
                } else {
                    $.ajax({
                        url: "{{ route('regu.store') }}",
                        method: "POST",
                        data: {
                            key         :   'selesaikan',
                            jenis       :   regu,
                            orderitem   :   id,
                            // netsuite_send: netsuite_send
                        },
                        success: function(data) {
                            if (data.status == 400) {
                                showAlert(data.msg);
                                $(".input_order").show() ;
                            } else {
                                $.ajax({
                                    url: "{{ route('regu.store') }}",
                                    method: "POST",
                                    data: {
                                        key     :   'selesaikan',
                                        jenis   :   regu,
                                        cast    :   'approve',
                                        id      :   data.freestock_id,
                                        // netsuite_send: netsuite_send
                                    },
                                    success: function(data) {
                                        $('.modal-backdrop').remove();
                                        $('body').removeClass('modal-open');

                                        $("#data_request").attr("style", "display: none") ;
                                        $("#loading_request").attr("style", "display: block") ;
                                        $("#data_request").load("{{ route('regu.request_order', ['key' => 'view']) }}&regu={{ $regu }}&tanggal=" + tanggal + "&cari=" + cari + "&menunggu=" + menunggu + "&page=" + page, function() {
                                            $("#data_request").attr("style", "display: block") ;
                                            $("#loading_request").attr("style", "display: none") ;
                                        }) ;
                                        showNotif('Produksi berhasil diselesaikan');
                                        $(".input_order").show() ;
                                    }
                                });
                            }
                        }
                    });
                }

            }
        });

    }

    loadbbfg();

})


$('.btn-process-input').on('click', function(){
    id              =   $(this).data('id') ;
    // console.log(id);
    loadbbfg();


    $('#netsuite_send' + id).on('change', function(){
            if($('#netsuite_send' + id).get(0).checked) {
                $('#select-item-bb'+id).show();
            } else {
                $('#select-item-bb'+id).hide();
            }
    })



})


$('.tanggal-fg').on('change', function(){
    $('#loading-input').show();
    loadbbfg();
})
$('.tanggal-fg-2').on('change', function(){
    $('#loading-input').show();
    loadbbfg();
})
$(".bbtype").on('change', function () {
    $('#loading-input').show();
    loadbbfg();
});

var tanggal_fg = $('#tanggal-fg'+id).val();
var tanggal_fg_2 = $('#tanggal-fg-2'+id).val();

function loadbbfg(){
    tanggal_fg      = $('#tanggal-fg'+id).val();
    tanggal_fg_2    = $('#tanggal-fg-2'+id).val();
    var item_cari   = $('#form-item'+id).val();
    // bbtype       = $('input[name="bbtype'+id+'"]:checked').val();
    var bbtype      = $('input[name="bbtype'+id+'"]').is(':checked') ? 'true' : 'false';
    var url_bb      = "{{ url('admin/produksi-regu/bahanbaku?tanggal=') }}" + tanggal_fg+"&tanggal_end=" + tanggal_fg_2+"&search="+encodeURIComponent(item_cari)+"&search2=stock&inputID=" + id +"&type="+bbtype;
    $("#bahanbaku-fg"+id).load(url_bb, function () {
        $('#loading-input').hide()
    });
}

$(".viewinputbyorder").click(function (e) {
    e.preventDefault();
    var id      = $(this).data('inputbyid');
    var href    = $(this).attr('href');

    $.ajax({
        url : href,
        type: "GET",
        data: {
            id      : id,
        },
        success: function(data){
            $('#contentView').html(data);
        }
    });
});

    </script>
