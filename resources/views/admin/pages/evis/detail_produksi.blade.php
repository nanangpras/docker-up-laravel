<h6>Status Produksi</h6>
@if($data->status==3)
<div class="alert alert-success">Proses Telah diselesaikan</div>
@endif
@if($data->status==2)
<div class="alert alert-warning">Proses Masih Berlangsung</div>
@endif
@php
$ns = \App\Models\Netsuite::where('id',$data->netsuite_id)->get();
@endphp

<div class="row">
    <div class="col-md">
        <div class="form-group">
            Tanggal
            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                @endif name="tanggal_produksi" id="tanggal_produksi" value="{{ $data->tanggal }}" class="form-control">
        </div>
        <div class="form-group">
            Ubah Regu
            <select name="grupregu" id="grupregu" class="form-control select2">
                <option value="byproduct" <?php if ($data->regu == "byproduct") : ?>selected
                    <?php endif; ?>>BY PRODUCT
                </option>
                <option value="boneless" <?php if ($data->regu == "boneless") : ?>selected
                    <?php endif; ?>>BONELESS
                </option>
                <option value="parting" <?php if ($data->regu == "parting") : ?>selected
                    <?php endif; ?>>PARTING
                </option>
                <option value="marinasi" <?php if ($data->regu == "marinasi") : ?>selected
                    <?php endif; ?>>M
                </option>
                <option value="whole" <?php if ($data->regu == "whole") : ?>selected
                    <?php endif; ?>>WHOLE CHICKEN
                </option>
                <option value="frozen" <?php if ($data->regu == "frozen") : ?>selected
                    <?php endif; ?>>FROZEN
                </option>
            </select>
        </div>
        {{-- <label><input type="checkbox" id="netsuite_send" {{ ($data->netsuite_send == '0' ? 'checked' : '') }}> <span
                class="status status-danger" style="font-size: 15px;"><b>Tidak Proses WO</b></span> </label> --}}


        @if(count($ns)>0)
        @if(Auth::user()->account_role == 'superadmin')
        <div class="form-group text-right">
            <button type="button" class="btn btn-primary ubah_produksi" data-id="{{ $data->id }}">Ubah Data</button>
        </div>
        @endif
        @else
        <div class="form-group text-right">
            <button type="button" class="btn btn-primary ubah_produksi" data-id="{{ $data->id }}">Ubah Data</button>
        </div>
        @endif


    </div>
    <div class="col-md">
        Tanggal Produksi : {{date('d/m/y',strtotime($data->tanggal))}}<br>
        Regu : {{$data->regu}}<br>
        Created : {{date('d/m/y H:i:s',strtotime($data->created_at))}}<br>
        Updated : {{date('d/m/y H:i:s',strtotime($data->updated_at))}}<br>
    </div>
</div>

<div class="row">
    <div class="col-sm-6 pr-sm-1">
        <table class="table default-table table-small">
            <thead>
                <tr>
                    <th colspan="4" class="text-info">Bahan Baku</th>
                </tr>
                <tr>
                    <th>Nama</th>
                    <th>Asal</th>
                    <th>Ekor/Pcs/Pack</th>
                    <th>Berat</th>
                    @if(count($ns)>0)
                    @if(Auth::user()->account_role == 'superadmin')
                    <th></th>
                    @endif
                    @else
                    <th></th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @php
                $total = 0 ;
                $berat = 0 ;
                @endphp
                @foreach ($data->listfreestock as $raw)
                @php
                // $CalculateSisaQty     = \App\Models\Chiller::ambilsisachiller($raw->chiller_id,'qty_item','qty','bb_item',$raw->id);
                // $CalculateSisaBerat   = \App\Models\Chiller::ambilsisachiller($raw->chiller_id,'berat_item','berat','bb_berat',$raw->id);

                $total += $raw->qty ;
                $berat += $raw->berat ;
                @endphp
                <tr>
                    <td><a href="{{route('chiller.show',  $raw->chiller->id)}}" target="_blank">{{ $raw->chiller->item_name }}</a></td>
                    <td>{{ $raw->chiller->tujuan ?? ''}}</td>
                    <td>{{ number_format($raw->qty) }} PCS</td>
                    <td>{{ number_format($raw->berat, 2) }} Kg</td>

                    @if(count($ns)>0)
                    @if(Auth::user()->account_role == 'superadmin')
                    <td class="text-right">
                        <i class="fa fa-edit text-primary px-1 edit-bb-open" data-toggle="modal"
                            data-nama="{{$raw->chiller->item_name}}" data-id="{{$raw->id}}" 
                            data-qty="{{$raw->qty}}" data-berat="{{$raw->berat}}" data-chillerid="{{ $raw->chiller_id }}" data-target="#bb-edit"></i>
                        <i class="fa fa-trash text-danger hapus_bb px-1" data-id="{{ $raw->id }}"></i>
                    </td>
                    @endif
                    @else
                    <td class="text-right">
                        <i class="fa fa-edit text-primary px-1 edit-bb-open" data-toggle="modal"
                            data-nama="{{$raw->chiller->item_name}}" data-id="{{$raw->id}}" 
                            data-qty="{{$raw->qty}}" data-berat="{{$raw->berat}}" data-chillerid="{{ $raw->chiller_id }}" data-target="#bb-edit"></i>
                        <i class="fa fa-trash text-danger hapus_bb px-1" data-id="{{ $raw->id }}"></i>
                    </td>
                    @endif

                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th>Total</th>
                    <th>{{ number_format($total) }} PCS</th>
                    <th>{{ number_format($berat, 2) }} Kg</th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="col-sm-6 pl-sm-1">
        <table class="table default-table table-small">
            <thead>
                <tr>
                    <th colspan="4" class="text-info">Hasil Produksi</th>
                </tr>
                <tr>
                    <th>Nama</th>
                    <th>Ekor/Pcs/Pack</th>
                    <th>Berat</th>
                    @if(count($ns)>0)
                    @if(Auth::user()->account_role == 'superadmin')
                    <th></th>
                    @endif
                    @else
                    <th></th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @php
                $total = 0 ;
                $berat = 0 ;
                @endphp
                @foreach ($data->freetemp as $raw)
                @php
                $exp = json_decode($raw->label) ;
                $total += $raw->qty ;
                $berat += $raw->berat ;
                @endphp
                <tr>
                    <td>
                        <a href="{{route('chiller.show',  ($raw->freetempchiller->id ?? ""))}}"
                            target="_blank">{{$raw->item->nama }}</a>
                        @if($raw->kategori=="1")
                        <span class="status status-danger">[ABF]</span>
                        @elseif($raw->kategori=="2")
                        <span class="status status-warning">[EKSPEDISI]</span>
                        @elseif($raw->kategori=="3")
                        <span class="status status-warning">[TITIP CS]</span>
                        @else
                        <span class="status status-info">[CHILLER]</span>
                        @endif

                    </td>
                    <td>{{ number_format($raw->qty) }} PCS</td>
                    <td>{{ number_format($raw->berat, 2) }} Kg</td>
                    @if(count($ns)>0)
                    @if(Auth::user()->account_role == 'superadmin')
                    <td class="text-right">
                        <i class="fa fa-edit text-primary px-2 edit-hasil-open" data-toggle="modal"
                            data-target="#hasil-edit" data-nama="{{$raw->item->nama ?? ''}}" data-id="{{$raw->id}}"
                            data-itemid="{{$raw->item->id}}" data-qty="{{$raw->qty}}" data-berat="{{$raw->berat}}"
                            data-kategori="{{$raw->kategori}}"></i>
                        <i class="fa fa-trash text-danger hapus_fg px-1" data-id="{{ $raw->id }}"></i>
                    </td>
                    @endif
                    @else
                    <td class="text-right">
                        <i class="fa fa-edit text-primary px-2 edit-hasil-open" data-toggle="modal"
                            data-target="#hasil-edit" data-nama="{{$raw->item->nama ?? ''}}" data-id="{{$raw->id}}"
                            data-itemid="{{$raw->item->id}}" data-qty="{{$raw->qty}}" data-berat="{{$raw->berat}}"
                            data-kategori="{{$raw->kategori}}"></i>
                        <i class="fa fa-trash text-danger hapus_fg px-1" data-id="{{ $raw->id }}"></i>
                    </td>
                    @endif
                </tr>
                @if ($raw->plastik_sku)
                <tr>
                    <td colspan="3">
                        <div class="status status-success">
                            <div class="row">
                                <div class="col pr-1">{{ $raw->plastik_nama }}</div>
                                <div class="col-auto pl-1">// {{ $raw->plastik_qty }} PCS</div>
                            </div>
                        </div>
                    </td>
                </tr>
                @endif
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th>Total</th>
                    <th>
                        {{-- {{ number_format($total) }} PCS --}}
                    </th>
                    <th>{{ number_format($berat, 2) }} Kg</th>
                    @if(count($ns)>0)
                    @if(Auth::user()->account_role == 'superadmin')
                    <th></th>
                    @endif
                    @else
                    <th></th>
                    @endif
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<script>
    $('.edit-bb-open').on('click', function(e) {
        e.preventDefault()
        var id          = $(this).attr('data-id');
        var nama        = $(this).attr('data-nama');
        var qty         = $(this).attr('data-qty');
        var berat       = $(this).attr('data-berat');
        var chiller_id  = $(this).attr('data-chillerid');
        // var sisaQty     = $(this).attr('data-sisa');
        // var sisaBerat   = $(this).attr('data-sisaberat');

        // $('#form-edit-id').val(id);
        // $('#form-edit-nama').html(nama);
        // $('#form-edit-qty').val(qty);
        // $('#form-edit-berat').val(berat);
        // $('#form-edit-nama2').val(nama);
        // $('.sisaQty').val(sisaQty);

        $.ajax({
            url : "{{ route('regu.viewmodaledit', ['key' => 'viewmodaleditevis']) }}",
            type: "GET",
            data: {
                id          : id,
                nama        : nama,
                qty         : qty,
                berat       : berat,
                chiller_id  : chiller_id
                // sisaQty     : sisaQty,
                // sisaBerat   : sisaBerat
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
{{-- <div class="modal fade" id="bb-edit" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="bbLabel" aria-hidden="true">
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
        var kategori = $(this).attr('data-kategori');

        $('#form-edit-id-hasil').val(id);
        $('#form-edit-nama-hasil').html(nama);
        $('#form-edit-qty-hasil').val(qty);
        $('#form-edit-berat-hasil').val(berat);
        $('#form-item-id').val(item);
        $('#form-edit-kategori').val(kategori);
    })
</script>

<div class="modal fade" id="hasil-edit" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="hasilLabel" aria-hidden="true">
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
                                <input type="number" name="berat" value="" step="0.01" class="form-control"
                                    id="form-edit-berat-hasil">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col pr-1">
                            <div class="form-group">
                                Lokasi
                                <select id="form-edit-kategori" name="kategori" class="form-control">
                                    <option value="0">Chiller FG</option>
                                    <option value="1">ABF</option>
                                    <option value="2">Ekspedisi</option>
                                </select>
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
    $(document).on('click', '.ubah_produksi', function() {
    var row_id  =   $(this).data('id');
    var tanggal =   $("#tanggal_produksi").val() ;
    var grupregu =   $("#grupregu").val() ;
    // var netsuite_send   = "TRUE";

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    // if($('#netsuite_send').get(0).checked) {
    //     // something when checked
    //     netsuite_send = "TRUE";
    // } else {
    //     // something else when not
    //     netsuite_send = "FALSE";
    // }

    $.ajax({
        url: "{{ route('regu.store') }}",
        method: "POST",
        data: {
            row_id  :   row_id,
            tanggal :   tanggal,
            key     :   'ubahtanggal',
            regu    :   grupregu,
            // netsuite_send : netsuite_send
        },
        success: function(data) {
            if (data.status == 400) {
                showAlert(data.msg) ;
            } else {
                $("#data_produksi").load("{{ route('evis.peruntukan', ['view' => 'data_produksi']) }}&produksi={{ $data->id }}");
                showNotif('Ubah tanggal berhasil');
            }
        }
    });
})
</script>
