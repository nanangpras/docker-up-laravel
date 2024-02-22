@php
$netsuite = \App\Models\Netsuite::where('label', 'like', '%'.$regu.'%')->where('trans_date', $tanggal)->get();
@endphp

@if(count($netsuite)>0)
<hr>
<h6>Netsuite Terbentuk</h6>

<table class="table default-table">
    <thead>
        <tr>
            <th>
                <input type="checkbox" id="ns-checkall">
            </th>
            <th>ID</th>
            <th>C&U Date</th>
            <th>TransDate</th>
            <th>Label</th>
            <th>Activity</th>
            <th>Location</th>
            <th>IntID</th>
            <th>Paket</th>
            <th width="100px">Data</th>
            <th width="100px">Action</th>
            <th>Response</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>

        @foreach ($netsuite as $no => $field_value)
            @include('admin.pages.log.netsuite_one', ($netsuite = $field_value))
        @endforeach

    </tbody>
</table>
@endif
<br />
@foreach($evisselesai as $row)
<div class="border border-info rounded p-2 mb-3">

    Produksi {{date('d/m/Y', strtotime($row->tanggal))}}

    @if($row->netsuite_send=="0")
        &nbsp <span class="status status-danger">TIDAK KIRIM WO</span>
    @endif
    <div class="text-right mb-2">
        @if ($row->status == 2)
        <button type="button" class="btn btn-sm btn-green btn-sm approved" data-id="{{ $row->id }}">Selesaikan</button>
        @endif
        <a href="{{ route('evis.peruntukan', ['produksi' => $row->id]) }}" class="btn btn-sm btn-info">Detail</a>
    </div>

    <div class="row">
        @php
            $total_bb = 0;
            $total_fg = 0;
            $berat    = 0;
        @endphp
        <div class="col-sm-6 pr-sm-1">

            @if($row->orderitem_id)
            <div class="col">
                <label>Input By Order</label>
                @php
                    $order_item = \App\Models\OrderItem::find($row->orderitem_id);
                @endphp
                <span class="status status-success">{{$order_item->itemorder->no_so ?? "#"}} {{$order_item->itemorder->nama ?? "#"}} </span>
            </div>
            @endif
            
            @if(count($row->listfreestock)>0 && ($row->deleted_at == null))
            <table class="table default-table table-small">
                <thead>
                    <tr>
                        <th colspan="4" class="text-info">Bahan Baku</th>
                    </tr>
                    <tr>
                        <th>Nama</th>
                        <th>Asal</th>
                        <th>Qty</th>
                        <th>Berat</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $total  =   0 ;
                        $berat  =   0 ;
                    @endphp
                    @foreach ($row->listfreestock as $raw)
                    @php
                        $total  +=  $raw->qty ;
                        $berat  +=  $raw->berat ;
                    @endphp
                    <tr>
                        <td>{{ $raw->chiller->item_name ?? '#TIDAKADACHILLER' }}</td>
                        <td>{{ $raw->chiller->tujuan ?? ''}}</td>
                        <td class="text-right">{{ number_format($raw->qty) }} Pcs</td>
                        <td class="text-right">{{ number_format($raw->berat, 2) }} Kg</td>
                        @if($netsuite)
                            @if(Auth::user()->account_role == 'superadmin')
                                <td class="text-right"><i class="fa fa-edit text-primary px-1 edit-bb-open" data-toggle="modal" data-nama="{{$raw->chiller->item_name ?? '#TIDAKADACHILLER'}}" data-id="{{$raw->id}}" data-qty="{{$raw->qty}}" data-berat="{{$raw->berat}}" data-chillerid="{{ $raw->chiller_id }}" data-target="#bb-edit"></i></td>
                            @endif
                        @else
                        <td class="text-right"><i class="fa fa-edit text-primary px-1 edit-bb-open" data-toggle="modal" data-nama="{{$raw->chiller->item_name ?? '#TIDAKADACHILLER'}}" data-id="{{$raw->id}}" data-qty="{{$raw->qty}}" data-berat="{{$raw->berat}}" data-chillerid="{{ $raw->chiller_id }}" data-target="#bb-edit"></i></td>
                        @endif

                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th>Total</th>
                            <th class="text-right">{{ number_format($total) }} PCS</th>
                            <th class="text-right">{{ number_format($berat, 2) }} Kg</th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
            @else
            <div class="col">
                <div class="status status-danger">Item Bahan baku tidak ada</div>
            </div>
            @php
                $total_bb = $berat;
            @endphp
            @endif
        </div>
        <div class="col-sm-6 pl-sm-1">
            @if(count($row->freetemp)>0 && ($row->deleted_at == null))
            <table class="table default-table table-small">
                <thead>
                    <tr>
                        <th colspan="4" class="text-info">Hasil Produksi</th>
                    </tr>
                    <tr>
                        <th>Nama</th>
                        <th>Qty</th>
                        <th>Berat</th>
                        @if ($row->status != 3)
                        <th></th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @php
                        $total  =   0 ;
                        $berat  =   0 ;
                    @endphp
                    @foreach ($row->freetemp as $raw)
                    @php
                        $exp    =   json_decode($raw->label) ;
                        $total  +=  $raw->qty ;
                        $berat  +=  $raw->berat ;
                    @endphp
                    <tr>
                        <td>
                            @if($raw->kategori=="1")
                            <span class="status status-danger">[ABF]</span>
                            @elseif($raw->kategori=="2")
                            <span class="status status-warning">[EKSPEDISI]</span>
                            @elseif($raw->kategori=="3")
                            <span class="status status-warning">[TITIP CS]</span>
                            @else
                            <span class="status status-info">[CHILLER]</span>
                            @endif
                        {{ $raw->item->nama ?? '#' }}</td>
                        <td class="text-right">{{ number_format($raw->qty) }} Pcs</td>
                        <td class="text-right">{{ number_format($raw->berat, 2) }} Kg</td>
                        @if ($row->status != 3)
                        <td class="text-right"><i class="fa fa-edit text-primary px-2 edit-hasil-open" data-toggle="modal" data-target="#hasil-edit"  data-nama="{{$raw->item->nama ?? ''}}" data-id="{{$raw->id ?? ''}}"  data-itemid="{{$raw->item->id ?? ''}}" data-qty="{{$raw->qty}}" data-berat="{{$raw->berat}}"></i></td>
                        @endif
                    </tr>
                    @if ($raw->plastik_sku)
                    <tr>
                        <td colspan="4">
                            <div class="status status-success">
                                <div class="row">
                                    <div class="col pr-1">{{ $raw->plastik_nama }}</div>
                                    <div class="col-auto pl-1">// {{ $raw->plastik_qty }} PCS</div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endif
                    @if($raw->keterangan)
                    <tr>
                        <td colspan="4">
                            <div class="rounded-1 status status-warning">KETERANGAN: {{ $raw->keterangan }}
                            </div>
                        </td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th>Total</th>
                        <th class="text-right">{{ number_format($total) }} PCS</th>
                        <th class="text-right">{{ number_format($berat, 2) }} Kg</th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
            @elseif(count($row->freetemp)>0 && $row->deleted_at !== null)
            <div class="alert alert-danger">Item Hasil Produksi telah dihapus</div>
            @else
            <div class="alert alert-info">Belum ada item hasil produksi</div>
            @php
                $total_fg = $berat;
            @endphp
            @endif
        </div>

                @php
                $total_fg   =   $berat;
                $bb_total   =   $total_bb - $total_fg ;
                $selisih    =   ($bb_total) * (-1);
                $presentase =   $bb_total && $total_bb ? (($bb_total / $total_bb * 100) * (-1)) : 0 ;
                @endphp

                <div class="col-sm-12">
                <hr>
                <div class="row">
                    @if($total_bb>0)
                    <div class="col-2">
                        <div class="px-2">
                            <label>Selisih</label><br>
                            @if($presentase > 5 || $presentase < -5)
                            <b class="red">{{number_format($selisih,2)}} Kg</b>
                            @else
                            <b class="blue">{{number_format($selisih,2)}} Kg</b>
                            @endif
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="px-2">
                            <label>Presentase</label><br>
                            @if($presentase > 5 || $presentase < -5)
                            <b class="red">{{number_format($presentase, 2)}} %</b>
                            @else
                            <b class="blue">{{number_format($presentase, 2)}} %</b>
                            @endif
                        </div>
                    </div>
                    <div class="col-8">
                        <label>Keterangan</label><br>
                        @if($presentase > 5 || $presentase < -5)
                            <div class="status status-warning">Presentasi susut masih diatas atau dibawah benchmark 5%</div>
                        @else
                            <div class="status status-success">Presentasi susut sesuai dengan benchmark 5%</div>
                        @endif
                    </div>
                    @endif
                    <hr>
                        @if($total_bb>0 && $total_fg==0)
                            <div class="col-12"><span class="status status-warning">Penginputan Bahan Baku</span></div>
                        @endif
                        @if($total_fg>0 && $total_bb==0)
                            <div class="col-12"><span class="status status-info">Penginputan Hasil Produksi</span></div>
                        @endif
                </div>
        </div>

    </div>
</div>
@endforeach

<script>
    $('.edit-bb-open').on('click', function(){

        var id          =   $(this).data('id');
        var nama        =   $(this).data('nama');
        var qty         =   $(this).data('qty');
        var berat       =   $(this).data('berat');
        var chillerid   =   $(this).attr('data-chillerid');

        $.ajax({
            url : "{{ route('regu.viewmodaledit', ['key' => 'viewmodaleditevis']) }}",
            type: "GET",
            data: {
                id          : id,
                nama        : nama,
                qty         : qty,
                berat       : berat,
                chiller_id  : chillerid
            },
            success: function(data){
                $('#content_modal_bb_edit').html(data);
            }
        });
    })
</script>
<div class="modal fade" id="bb-edit" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modalLpahLabel" aria-hidden="false">
    <div class="modal-dialog">
        <div id="content_modal_bb_edit"></div>
    </div>
</div>
{{-- <div class="modal fade" id="bb-edit" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="bbLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bbLabel">Edit Ambil Bahan Baku</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('evis.updateperuntukan', ['key' => 'bahan_baku']) }}" method="post">
                @csrf @method('patch')
                <input type="hidden" name="x_code" id="form-edit-id">

                <div class="modal-body">
                    <div class="form-group">
                        <div>Item</div>
                        <b id="form-edit-nama"></b>
                    </div>
                    <div class="row">
                        <div class="col pr-1">
                            <div class="form-group">
                                Ekor/Qty
                                <input type="number" name="qty" class="form-control" id="form-edit-qty">
                            </div>
                        </div>
                        <div class="col pl-1">
                            <div class="form-group">
                                Berat
                                <input type="number" name="berat" step="0.01" class="form-control" id="form-edit-berat">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Edit</button>
                </div>
            </form>
        </div>
    </div>
</div> --}}

<script>
    $('.edit-hasil-open').on('click', function(){
        var id = $(this).attr('data-id');
        var nama = $(this).attr('data-nama');
        var qty = $(this).attr('data-qty');
        var berat = $(this).attr('data-berat');
        var item = $(this).attr('data-itemid');

        $('#form-edit-id-hasil').val(id);
        $('#form-edit-nama-hasil').html(nama);
        $('#form-edit-qty-hasil').val(qty);
        $('#form-edit-berat-hasil').val(berat);
        $('#form-item-id').val(item);
    })
</script>

<div class="modal fade" id="hasil-edit" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="hasilLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="hasilLabel">Edit Hasil Produksi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('evis.updateperuntukan', ['key' => 'hasil_produksi']) }}" method="post">
                @csrf @method('patch')
                <input type="hidden" name="x_code" value="" id="form-edit-id-hasil">
                <div class="modal-body">
                    <div class="form-group">
                        Item
                        <div><b id="form-edit-nama-hasil"></b></div>
                        <input id="form-item-id" type="hidden" value="" name="item">
                    </div>

                    <div class="row">
                        <div class="col pr-1">
                            <div class="form-group">
                                Ekor/Qty
                                <input type="number" name="qty" value="" class="form-control" id="form-edit-qty-hasil">
                            </div>
                        </div>
                        <div class="col pl-1">
                            <div class="form-group">
                                Berat
                                <input type="number" name="berat" value="" step="0.01" class="form-control" id="form-edit-berat-hasil">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Edit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$('.approved').click(function() {
    var freestock_id    =   $(this).data('id');

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(".approved").hide() ;
    showNotif('Menunggu produksi diselesaikan');

    $.ajax({
        url: "{{ route('evis.peruntukanselesai') }}",
        method: 'POST',
        data: {
            freestock_id: freestock_id
        },
        success: function(data) {
            if (data.status == 400) {
                showAlert(data.msg);
            } else {
                $("#hasil_peruntukan").load("{{ route('evis.hasilperuntukan') }}?tanggal={{$tanggal}}");
                showNotif('Berhasil Approve');
            }
        }
    })
})
</script>

<script>
$('.edit_evis').click(function() {
    var freestock_id    =   $(this).data('id');

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url: "{{ route('evis.peruntukanselesai') }}",
        method: 'POST',
        data: {
            freestock_id: freestock_id,
            key : 'edit'
        },
        success: function(data) {
            if (data.status == 400) {
                showAlert(data.msg);
            } else {
                showNotif('Silahkan Perbaharui Data');
                $("#gabung").load("{{ route('evis.cartgabung') }}");
                $("#list_bahan_baku").load("{{ route('evis.cartbahanbaku') }}");
                $("#hasil_peruntukan").load("{{ route('evis.hasilperuntukan') }}?tanggal={{$tanggal}}");
                $("#bbperuntukan").load("{{ route('evis.bbperuntukan') }}");
                $("#hasilproduksi").load("{{ route('evis.hasilproduksi') }}");
                $("#selesaikan").load("{{ route('evis.peruntukan', ['key' => 'selesai']) }}");
            }
        }
    })
})
</script>
