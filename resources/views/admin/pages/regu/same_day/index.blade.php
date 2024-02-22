<div class="table-responsive">
    <table class="table table-sm table-hover table-striped table-bordered table-small">
        <thead>
            <tr>
                <th class="text-center" rowspan="2">No</th>
                <th class="text-center" rowspan="2">Tanggal</th>
                <th class="text-center" rowspan="2">MKT</th>
                <th class="text-center" rowspan="2">Customer</th>
                <th class="text-center" rowspan="2">Item</th>
                <th class="text-center" rowspan="2">Jenis</th>
                <th class="text-center" rowspan="2">Bumbu</th>
                <th class="text-center" rowspan="2">Memo Item</th>
                <th class="text-center" rowspan="2">Memo Header</th>
                <th class="text-center" colspan="4">Order</th>
                @if ($regu)
                <th class="text-center" rowspan="2">#</th>
                @endif
            </tr>
            <tr>
                <th class="text-center">Part</th>
                <th class="text-center">Ekor/Pcs</th>
                <th class="text-center">Pack</th>
                <th class="text-center">Berat</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $no => $row)
            <tr class="small"
                    @if($row->order_status_so=="Closed") style="background-color: red; color:white" @endif
                    @if($row->edit_item==1) style="background-color: #FFFF8F" @endif
                    @if($row->edit_item==2) style="background-color: #FFEA00" @endif
                    @if($row->edit_item==3) style="background-color: #FDDA0D" @endif
                    @if($row->delete_at_item!=NULL) style="background-color: red; color:white" @endif
                    >
                <td>{{ $no+1 }}</td>
                <td>{{ $row->tanggal_kirim }}</td>
                <td>
                    @if($row->sales_id=='117762')
                    SONY
                    @elseif($row->sales_id=='117759')
                    SETYO
                    @elseif($row->sales_id=='117674')
                    IFAN
                    @elseif($row->sales_id=='117786')
                    MILAGRO
                    @elseif($row->sales_id=='119822')
                    ANDI
                    @elseif($row->sales_id=='119821')
                    SUBANDI
                    @else
                    {{$row->sales_id}}
                    @endif
                </td>
                <td>{{ $row->nama }}</td>
                <td>{{ $row->nama_detail }}
                    @if(date('Y-m-d', strtotime($row->created_at_order))==$row->tanggal_kirim)
                        <br><span class="small red">*Prioritas Same Day</span>
                    @endif
                </td>
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
                <td>{{ $row->memo }}</td>
                <td>{{ $row->memo_header }}</td>
                <td class="text-right"> {{ $row->part }}</td>
                <td class="text-right">@if(strpos(strtolower($row->memo), "ekor") || strtolower($row->memo) == "ekor"){{ number_format($row->qty) }} @endif</td>
                <td class="text-right">@if(strpos(strtolower($row->memo), "pack") || strtolower($row->memo) == "pack"){{ number_format($row->qty) }} @endif</td>
                <td class="text-right">{{ number_format($row->berat, 2) }}</td>
                @if ($regu)
                <td>
                    @if ($row->free_stock)

                        @php
                            $show_true = FALSE;
                        @endphp

                        @if (($row->item->category_id == 5) || ($row->item->category_id == 11))
                            @php
                                $show_true  =   App\Models\User::setIjin(8) ? TRUE : FALSE ;
                            @endphp
                        @endif
                        @if ($row->item->category_id == 2)
                            @php
                                $show_true  =   App\Models\User::setIjin(9) ? TRUE : FALSE ;
                            @endphp
                        @endif
                        @if (($row->item->category_id == 3) || ($row->item->category_id == 9))
                            @php
                                $show_true  =   App\Models\User::setIjin(10) ? TRUE : FALSE ;
                            @endphp
                        @endif
                        @if ($row->item->category_id == 1)
                            @php
                                $show_true  =   App\Models\User::setIjin(11) ? TRUE : FALSE ;
                            @endphp
                        @endif
                        @if (($row->item->category_id == 7) || ($row->item->category_id == 8) || ($row->item->category_id == 9) || ($row->item->category_id == 13))
                            @php
                                $show_true  =   App\Models\User::setIjin(12) ? TRUE : FALSE ;
                            @endphp
                        @endif

                        @if (((Auth::user()->account_role == 'superadmin') || App\Models\User::setIjin(33)) ? TRUE : (($row->user_id == Auth::user()->id) ? TRUE : $show_true))
                        <a href="{{ route('regu.request_view', [$row->id, 'regu' => $regu]) }}" class="btn btn-primary btn-block btn-sm py-0 rounded-0">Lihat</a>
                        @endif
                    @else
                        {{-- @if (($row->fulfillment_qty == NULL) || ($row->fulfillment_berat == NULL)) --}}
                        <button class="btn btn-success btn-block btn-sm py-0 rounded-0"  data-toggle="modal" data-target="#prosessameday{{ $row->id }}">Input</button>
                        <div class="modal fade" id="prosessameday{{ $row->id }}" aria-labelledby="prosessameday{{ $row->id }}Label" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="prosessameday{{ $row->id }}Label">Produksi Request Order</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row mb-3">
                                            <div class="col-8 pr-1">
                                                Item
                                                <input type="text" value="{{ $row->nama_detail }}" class="form-control" readonly>
                                            </div>
                                            <div class="col-2 px-1">
                                                Qty
                                                <input type="number" name="jumlah" id="jumlahsameday{{ $row->id }}" value="{{ $row->qty }}" class="form-control form-control-sm p-1" placeholder="Qty" autocomplete="off">
                                            </div>
                                            <div class="col-2 pl-0">
                                                Berat
                                                <input type="number" name="berat" id="beratsameday{{ $row->id }}" value="{{ $row->berat }}" class="form-control form-control-sm p-1" step="0.01" placeholder="Berat" autocomplete="off">
                                            </div>
                                        </div>

                                        <div class="row">
                                            @if ($row->item->itemkat->nama == 'Parting' or $row->item->itemkat->nama == 'Marinated')
                                            <div class="col-4 pr-0">
                                                <div class="form-group">
                                                    Jumlah Parting
                                                    <input type="number" name="part" class="form-control form-control-sm p-1" id="partsameday{{ $row->id }}" value="{{ $row->part ?? '' }}" placeholder="Parting" autocomplete="off">
                                                </div>
                                            </div>
                                            @endif
                                            <div class="{{ ($row->item->itemkat->nama == 'Parting' or $row->item->itemkat->nama == 'Marinated') ? 'col-8' : 'col' }}">
                                                <div class="row">
                                                    <div class="col-9 pr-1">
                                                        <div class="form-group">
                                                            Plastik
                                                            <select name="plastik" id="plastiksameday{{ $row->id }}" class="form-control select2" data-width="100%" data-placeholder="Pilih Plastik">
                                                                <option value=""></option>
                                                                <option value="Curah">Curah</option>
                                                                @php
                                                                $plastik = \App\Models\Item::where('category_id', '25')->where('subsidiary', env('NET_SUBSIDIARY', 'EBA'))->where('status', '1')->get();
                                                                @endphp
                                                                @foreach ($plastik as $p)
                                                                <option value="{{ $p->id }}">{{ $p->nama }} - {{$p->subsidiary}}{{ $p->netsuite_internal_id }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-3 pl-1">
                                                        &nbsp;
                                                        <input type="number" name="jumlah_plastik" id="jumlah_plastiksameday{{ $row->id }}" class="form-control form-control-sm" placeholder="Jumlah">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            Nama Bumbu / Keterangan
                                            <input type="text" name="sub_item" class="form-control form-control-sm" id="sub_itemsameday{{ $row->id }}" value="{{ $row->bumbu ?? '' }} {{ $row->memo }}" placeholder="Keterangan" autocomplete="off">
                                        </div>

                                        <div class="row">
                                            <div class="col-4 pr-1">
                                                <div class="form-group">
                                                    Unit
                                                    <input type="text" id="unitsameday{{ $row->id }}" placeholder="Tuliskan Unit" value="keranjang" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-4 px-1">
                                                <div class="form-group">
                                                    Jumlah Keranjang
                                                    <input type="number" min="0" id="jumlah_keranjangsameday{{ $row->id }}" placeholder="Tuliskan Jumlah Keranjang" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-4 pl-1">
                                                <div class="form-group">
                                                    Kode Produksi
                                                    <input type="text" id="kode_produksisameday{{ $row->id }}" placeholder="Tuliskan Kode Produksi" class="form-control">
                                                </div>
                                            </div>
                                        </div>

                                        @if (env("NET_SUBSIDIARY", "EBA") == "EBA")
                                        <div class="form-group">
                                            Regu
                                            <select name="regu" id="regusameday{{ $row->id }}" class="form-control select2">
                                                <option value="boneless" {{ $regu == 'boneless' ? 'selected' : '' }}>Boneless</option>
                                                <option value="parting" {{ $regu == 'parting' ? 'selected' : '' }}>Parting</option>
                                                <option value="marinasi" {{ $regu == 'marinasi' ? 'selected' : '' }}>Parting M</option>
                                                <option value="whole" {{ $regu == 'whole' ? 'selected' : '' }}>Whole Chicken</option>
                                            </select>
                                        </div>
                                        @else
                                        <input type="hidden" id="regusameday{{ $row->id }}" value="{{ $regu }}">
                                        @endif

                                        <div class="form-group">
                                            <label>Alasan Tidak Terpenuhi</label>
                                            <input type="text" class="form-control form-control-sm" name="alasan" placeholder="Alasan" id="alasansameday{{ $row->id }}">
                                        </div>
                                        {{-- <div class="form-group">
                                            <label> <input type="checkbox" class="" name="netsuite_send" placeholder="WO" id="netsuite_send{{ $row->id }}" checked> Tidak Kirim WO</label>
                                        </div> --}}
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="button" class="btn btn-primary input_sameday" data-item="{{ $row->item_id }}" data-page="{{ $request->page ?? '' }}" data-id="{{ $row->id }}">Proses</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- @endif --}}
                    @endif
                    @if($row->edit_item>0)<br><span class="text-small status status-warning">EditKe{{$row->edit_item}} </span>@endif
                    @if($row->delete_at_item!=NULL) <br><span class="text-small status status-danger">Batal </span>@endif
                </td>
                @endif
            </tr>

            @endforeach
        </tbody>
    </table>
</div>

{{-- <div id="paginate_sameday">
    {{ $data->appends($_GET)->onEachSide(1)->links() }}
</div> --}}

<script>
$('#paginate_sameday .pagination a').on('click', function(e) {
    e.preventDefault();
    showNotif('Menunggu');

    url = $(this).attr('href');
    $.ajax({
        url: url,
        method: "GET",
        success: function(response) {
            $('#sameday_view').html(response);
        }

    });
});
</script>

<script>
$(".input_sameday").on('click', function() {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var id              =   $(this).data('id') ;
    var item            =   $(this).data('item') ;
    var page            =   $(this).data('page') ;
    var menunggu        =   $("#menunggu:checked").val();
    var tanggal         =   $("#tanggal_request").val();
    var cari            =   encodeURIComponent($("#cari_request").val());

    var plastik         =   $('#plastiksameday' + id).val();
    var jumlah_plastik  =   $('#jumlah_plastiksameday' + id).val();
    var parting         =   $('#partsameday' + id).val();
    var berat           =   $('#beratsameday' + id).val();
    var jumlah          =   $('#jumlahsameday' + id).val();
    var sub_item        =   $('#sub_itemsameday' + id).val();
    var regu            =   $('#regusameday' + id).val();
    var alasan          =   $('#alasansameday' + id).val();

    var unit                =   $("#unitsameday" + id).val() ;
    var jumlah_keranjang    =   $("#jumlah_keranjangsameday" + id).val() ;
    var kode_produksi       =   $("#kode_produksisameday" + id).val() ;

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
    // } else {
    //     // something else when not
    //     netsuite_send = "FALSE";
    // }

    if (next != 'TRUE') {
        showAlert('Lengkapi data plastik');
    } else {

        $(".input_sameday").hide() ;


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
                kode_produksi   :  kode_produksi,
                orderitem       :   id,
                tujuan_produksi :   '2',
            },
            success: function(data) {
                $.ajax({
                    url: "{{ route('regu.store') }}",
                    method: "POST",
                    data: {
                        key         :   'selesaikan',
                        jenis       :   regu,
                        orderitem   :   id,
                        //  netsuite_send: netsuite_send
                    },
                    success: function(data) {
                        if (data.status == 400) {
                            showAlert(data.msg);
                        } else {
                            $.ajax({
                                url: "{{ route('regu.store') }}",
                                method: "POST",
                                data: {
                                    key     :   'selesaikan',
                                    jenis   :   regu,
                                    cast    :   'approve',
                                    id      :   data.freestock_id,
                                },
                                success: function(data) {
                                    $('.modal-backdrop').remove();
                                    $('body').removeClass('modal-open');

                                    $("#data_request").attr("style", "display: none") ;
                                    $("#loading_request").attr("style", "display: block") ;
                                    $("#sameday_view").load("{{ route('regu.index', ['key' => 'sameday']) }}&regu={{ $regu }}") ;
                                    showNotif('Produksi berhasil diselesaikan');
                                    $(".input_sameday").show() ;
                                }
                            });
                        }
                    }
                });

            }
        });

    }
})
</script>
