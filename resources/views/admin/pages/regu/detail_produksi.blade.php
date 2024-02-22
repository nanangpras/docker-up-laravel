<h6>Status Produksi</h6>
@if ($data->status == 3)
<div class="alert alert-success">Proses Telah diselesaikan</div>
@endif
@if ($data->status == 2)
<div class="alert alert-warning">Proses Masih Berlangsung</div>
@endif
@php
$so = \App\Models\OrderItem::find($data->orderitem_id);
$ns = \App\Models\Netsuite::where('id',$data->netsuite_id)->first();
@endphp
<div class="row">
    <div class="col-md">
        <div class="form-group">
            Tanggal
            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                @endif name="tanggal_produksi" id="tanggal_produksi" value="{{ $data->tanggal }}" class="form-control">
        </div>

    </div>
    <div class="col-md">
        <div class="form-group">
            Ubah Regu
            <select name="grupregu" id="grupregu" class="form-control select2">
                <option value="boneless" <?php if ($data->regu == "boneless") : ?>selected
                    <?php endif; ?>>BONELESS
                </option>
                <option value="parting" <?php if ($data->regu == "parting") : ?>selected
                    <?php endif; ?>>PARTING
                </option>
                <option value="marinasi" <?php if ($data->regu == "marinasi") : ?>selected
                    <?php endif; ?>>MARINASI
                </option>
                <option value="whole" <?php if ($data->regu == "whole") : ?>selected
                    <?php endif; ?>>WHOLE CHICKEN
                </option>
                <option value="frozen" <?php if ($data->regu == "frozen") : ?>selected
                    <?php endif; ?>>FROZEN
                </option>
                <option value="byproduct" <?php if ($data->regu == "byproduct") : ?>selected
                    <?php endif; ?>>BY PRODUCT
                </option>
            </select>
        </div>
    </div>
    {{-- <div class="col-md mt-4">
        <div class="form-group">
            <label><input type="checkbox" id="netsuite_send" {{ ($data->netsuite_send == '0' ? 'checked' : '') }}> <span
                    class="status status-danger" style="font-size: 15px;"><b>Tidak Proses WO</b></span> </label>
        </div>
    </div> --}}

    <div class="col-md">
        <div class="form-group text-center">
            <button type="button" class="btn btn-primary tanggal_produksi" data-id="{{ $data->id }}">Update</button>
            @if ($so)
            <button type="button" class="btn btn-danger detail_batalkan" data-id="{{ $data->id }}">Batalkan</button>
            @endif
        </div>
    </div>
</div>
<script>
    $('.select2').select2({
            theme: 'bootstrap4'
        })
</script>
<div class="row">
    <div class="col-md-12">
        @php

        if($ns){

        try {
        echo "<span class='status status-danger'> Document No : ".$ns->document_no."</span><br>";
        } catch (\Throwable $th) {
        //throw $th;
        }
        }
        @endphp

        Tanggal Produksi : {{date('d/m/y',strtotime($data->tanggal))}}<br>
        Regu : {{$data->regu}}<br>
        Created : {{date('d/m/y H:i:s',strtotime($data->created_at))}}<br>
        Updated : {{date('d/m/y H:i:s',strtotime($data->updated_at))}}<br>
        {{-- WO Status :
        @if($data->netsuite_send=="0")
        &nbsp <span class="status status-danger">TIDAK KIRIM WO</span>
        @else
        &nbsp <span class="status status-success">KIRIM WO</span>
        @endif --}}
        <br>
        <hr>

    </div>

    <div class="col-lg-6 pr-lg-1">
        <table class="table default-table table-small">
            <thead>
                <th>Bahan Baku</th>
                <th>Tanggal</th>
                <th>Asal</th>
                <th>Keterangan</th>
                <th>Ekor/Pcs/Pack</th>
                <th>Berat</th>
                <th></th>
            </thead>
            <tbody>
                @php
                $item = 0;
                $berat = 0;
                @endphp
                @foreach ($data->listfreestock as $no => $rfs)
                @php
                // $CalculateSisaQty     = \App\Models\Chiller::ambilsisachiller($rfs->chiller_id,'qty_item','qty','bb_item',$rfs->id);
                // $CalculateSisaBerat   = \App\Models\Chiller::ambilsisachiller($rfs->chiller_id,'berat_item','berat','bb_berat',$rfs->id);

                $item               += $rfs->qty;
                $berat              += $rfs->berat;
                @endphp
                <tr>
                    @if ($rfs->chiller)
                    <td>
                        <a href="{{ route('chiller.show', $rfs->chiller->id) }}" target="_blank">{{ ++$no }}.</a>
                        {{ $rfs->chiller->item_name }}

                        @if ($rfs->chiller->label != '' && $rfs->chiller->type == 'bahan-baku')
                        <br><span class="status status-info">{{ $rfs->chiller->label }}</span>
                        @endif

                    </td>
                    <td>{{ $rfs->chiller->tanggal_produksi }}<br>{{ $rfs->bb_kondisi }}</td>
                    <td>{{ $rfs->chiller->tujuan }}</td>
                    <td>{{ $rfs->catatan }}</td>
                    <td>{{ number_format($rfs->qty) }}</td>
                    <td class="text-right">{{ number_format($rfs->berat, 2) }} Kg</td>
                    <td>
                        @if($ns)
                            @if(Auth::user()->account_role == 'superadmin')
                                <i class="fa fa-edit text-primary px-1 edit-bb-open" data-toggle="modal"
                                    data-nama="{{ $rfs->chiller->item_name }}" data-id="{{ $rfs->id }}"
                                    data-qty="{{ $rfs->qty }}" data-berat="{{ $rfs->berat }}" data-chillerid="{{ $rfs->chiller_id }}" data-target="#bb-edit"></i>
                                <i class="fa fa-trash text-danger hapus_bb px-1" data-id="{{ $rfs->id }}"></i>
                            @elseif(Auth::user()->account_role == 'admin' &&  App\Models\User::setIjin(33) && !$ns)
                                <i class="fa fa-edit text-primary px-1 edit-bb-open" data-toggle="modal"
                                    data-nama="{{ $rfs->chiller->item_name }}" data-id="{{ $rfs->id }}"
                                    data-qty="{{ $rfs->qty }}" data-berat="{{ $rfs->berat }}" data-chillerid="{{ $rfs->chiller_id }}" data-target="#bb-edit"></i>
                                <i class="fa fa-trash text-danger hapus_bb px-1" data-id="{{ $rfs->id }}"></i>
                            @else
                                <span class="status status-info">Netsuite Terbentuk</span>
                            @endif
                        @else
                            <i class="fa fa-edit text-primary px-1 edit-bb-open" data-toggle="modal"
                                    data-nama="{{ $rfs->chiller->item_name }}" data-id="{{ $rfs->id }}"
                                    data-qty="{{ $rfs->qty }}" data-berat="{{ $rfs->berat }}" data-chillerid="{{ $rfs->chiller_id }}" data-target="#bb-edit"></i>
                            <i class="fa fa-trash text-danger hapus_bb px-1" data-id="{{ $rfs->id }}"></i>
                        @endif
                    </td>
                    @else
                    <td colspan="7" style="background: #fde0dd">ID {{ $rfs->id }} : ITEM TELAH DIHAPUS <i
                            class="fa fa-trash text-danger hapus_bb px-1 pull-right" data-id="{{ $rfs->id }}"></i> </td>
                    @endif
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" class="text-center">Total</th>
                    <th> {{ number_format($item) }}</th>
                    <th class="text-right">{{ number_format($berat, 2) }} Kg</th>
                    @if (Auth::user()->account_role == 'superadmin')
                    <td></td>
                    @endif
                </tr>
                @php
                $cekhistory = App\Models\FreestockList::where('freestock_id', $data->id)
                ->whereNotNull('deleted_at')
                ->withTrashed()
                ->count();
                @endphp
                @if ($cekhistory > 0)
                <tr>
                    <th colspan="6"><a
                            href="{{route('regu.index',['key' =>'history_delete_bb'])}}&produksi={{ $data->id }}"
                            class="btn btn-sm btn-info" target="_blank">History Delete Bahan Baku</a></th>
                </tr>
                @endif
            </tfoot>
        </table>

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
                    url : "{{ route('regu.viewmodaledit', ['key' => 'viewmodaledit']) }}",
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
                    <form action="{{ route('regu.editproduksi', ['key' => 'bahan_baku']) }}" method="post">
                        @csrf @method('put')
                        <input type="hidden" name="x_code" value="" id="form-edit-id">
                        <input type="hidden" name="form-edit-nama-item" value="" id="form-edit-nama2">
                        <input type="hidden" id="sisaQty" >
                        <div class="modal-body">
                            <div class="form-group">
                                <div>Item</div>
                                <b id="form-edit-nama"></b>
                            </div>
                            <div class="row">
                                <div class="col pr-1">
                                    <div class="form-group">
                                        Ekor/Qty
                                        <input type="number" name="qty" class="form-control sisaQty" id="form-edit-qty" min="0" max="{{$sisaQtynya}}">
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


    </div>
    <div class="col-lg-6 pl-lg-1">
        <table class="table default-table table-small">
            <thead>
                <th>Hasil Produksi</th>
                <th>Ekor/Pcs/Pack</th>
                <th>Berat</th>
                <th></th>
            </thead>
            <tbody>
                @php
                $qty = 0;
                $berat = 0;
                @endphp
                @foreach ($data->freetemp as $no => $item)
                @php
                $qty += $item->qty;
                $berat += $item->berat;
                $exp = json_decode($item->label);
                @endphp
                <tr>
                    <td>
                        <a href="{{ route('chiller.show', $item->freetempchiller->id ?? '') }}" target="_blank">{{ ++$no
                            }}.</a>
                        {{ $item->item->nama ?? '' }}
                        @if ($item->kategori == '1')
                        <span class="status status-danger">[ABF]</span>
                        @elseif($item->kategori == '2')
                        <span class="status status-warning">[EKSPEDISI]</span>
                        @elseif($item->kategori == '3')
                        <span class="status status-warning">[TITIP CS]</span>
                        @else
                        <span class="status status-info">[CHILLER]</span>
                        @endif
                        <!-- (<span class="text-primary text-bold text-2xl"> {{ $item->freetempchiller->id ?? '' }} </span> ) -->
                    </td>
                    <td>{{ number_format($item->qty) }}</td>
                    <td class="text-right">{{ number_format($item->berat, 2) }} Kg</td>
                    <td class="text-right">

                        @if($ns)
                            @if(Auth::user()->account_role == 'superadmin')
                            <i class="fa fa-edit text-primary px-2 edit-hasil-open" data-toggle="modal"
                                data-target="#hasil-edit" data-nama="{{ $item->item->nama ?? '' }}"
                                data-id="{{ $item->id }}" data-itemid="{{ $item->item_id }}"
                                data-parting="{{ $exp->parting->qty ?? '' }}" data-qty="{{ $item->qty }}"
                                data-berat="{{ $item->berat }}" data-kategori="{{ $item->kategori }}"
                                data-plastik="{{ $item->plastik_nama }}" data-qtyplastik="{{ $item->plastik_qty }}"
                                data-customer="{{ $item->customer_id }}" data-subitem="{{ $exp->sub_item ?? '' }}">
                            </i>
                            <i class="fa fa-trash text-danger hapus_fg px-1" data-id="{{ $item->id }}"
                                data-nama="{{ $item->item->nama }}"></i>
                            @elseif(Auth::user()->account_role == 'admin' &&  App\Models\User::setIjin(33) && !$ns)
                            <i class="fa fa-edit text-primary px-2 edit-hasil-open" data-toggle="modal"
                                data-target="#hasil-edit" data-nama="{{ $item->item->nama ?? '' }}"
                                data-id="{{ $item->id }}" data-itemid="{{ $item->item_id }}"
                                data-parting="{{ $exp->parting->qty ?? '' }}" data-qty="{{ $item->qty }}"
                                data-berat="{{ $item->berat }}" data-kategori="{{ $item->kategori }}"
                                data-plastik="{{ $item->plastik_nama }}" data-qtyplastik="{{ $item->plastik_qty }}"
                                data-customer="{{ $item->customer_id }}" data-subitem="{{ $exp->sub_item ?? '' }}">
                            </i>
                            @else
                            <span class="status status-info">Netsuite Terbentuk</span>
                            @endif
                        @else
                            <i class="fa fa-edit text-primary px-2 edit-hasil-open" data-toggle="modal"
                                data-target="#hasil-edit" data-nama="{{ $item->item->nama ?? '' }}"
                                data-id="{{ $item->id }}" data-itemid="{{ $item->item_id }}"
                                data-parting="{{ $exp->parting->qty ?? '' }}" data-qty="{{ $item->qty }}"
                                data-berat="{{ $item->berat }}" data-kategori="{{ $item->kategori }}"
                                data-plastik="{{ $item->plastik_nama }}" data-qtyplastik="{{ $item->plastik_qty }}"
                                data-customer="{{ $item->customer_id }}" data-subitem="{{ $exp->sub_item ?? '' }}">
                            </i>
                            <i class="fa fa-trash text-danger hapus_fg px-1" data-id="{{ $item->id }}"
                                data-nama="{{ $item->item->nama }}"></i>
                        @endif
                        {{-- @if (!$so)

                        @endif --}}
                        <!-- <i class="fa fa-trash text-danger hapus_fg px-1" onclick="hapus_fg('{{ $item->id }}')" data-id="{{ $item->id }}" data-nama="{{ $item->item->nama }}" ></i> -->
                    </td>
                </tr>
                <tr>
                    <td colspan="5">
                        <div class="row">
                            <div class="col pr-1">
                                @if ($item->kode_produksi)
                                Kode Produksi : {{ $item->kode_produksi }}
                                @endif
                            </div>
                            <div class="col pl-1 text-right">
                                @if ($item->unit)
                                Unit : {{ $item->unit }}
                                @endif
                            </div>
                        </div>
                        @if ($item->keranjang)
                        <div>{{ $item->keranjang }} Keranjang</div>
                        @endif
                        @if ($exp->plastik->jenis ?? FALSE)
                        <div class="status status-success">
                            <div class="row">
                                <div class="col pr-1">
                                    {{ $exp->plastik->jenis ?? '' }}
                                </div>
                                <div class="col-auto pl-1">
                                    @if ($exp->plastik->qty > 0)
                                    <span class="float-right">// {{ $exp->plastik->qty ?? '' }} Pcs</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif

                        @if ($exp->additional ?? FALSE)
                        {{ $exp->additional->tunggir ? 'Tanpa Tunggir, ' : '' }}
                        {{ $exp->additional->lemak ? 'Tanpa Lemak, ' : '' }}
                        {{ $exp->additional->maras ? 'Tanpa Maras' : '' }}
                        @endif
                        <div class="row mt-1 text-info">
                            <div class="col pr-1">
                                @if ($item->customer_id)
                                <div>Customer : {{ $item->konsumen->nama ?? '-' }}</div>
                                @endif
                                @if ($exp->sub_item ?? FALSE)
                                <div>Keterangan : {{ $exp->sub_item ?? '' }}</div>
                                @endif
                            </div>
                            <div class="col-auto pl-1 text-right">
                                @if ($item->selonjor)
                                <div class="text-danger font-weight-bold">SELONJOR</div>
                                @endif
                                @if ($exp->parting->qty ?? FALSE) 
                                Parting : {{ $exp->parting->qty ?? ''}}
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th>Total</th>
                    <th> {{ $qty }} Ekor</th>
                    <th class="text-right">{{ $berat }} Kg</th>
                    @if (Auth::user()->account_role == 'superadmin')
                    <td></td>
                    @endif
                </tr>
                @php
                $cekhistoryhasilproduksi = App\Models\FreestockTemp::where('freestock_id', $data->id)
                ->whereNotNull('deleted_at')
                ->withTrashed()
                ->count();
                @endphp
                @if ($cekhistoryhasilproduksi > 0)
                <tr>
                    <th colspan="6"><a
                            href="{{route('regu.index',['key' =>'history_delete_hp'])}}&produksi={{ $data->id }}"
                            class="btn btn-sm btn-info" target="_blank">History Delete
                            Hasil Produksi</a></th>
                </tr>
                @endif
            </tfoot>
        </table>

        <script>
            $('.edit-hasil-open').on('click', function() {
                console.log("okee");
                var id = $(this).data('id');
                var nama = $(this).data('nama');
                var qty = $(this).data('qty');
                var berat = $(this).data('berat');
                var parting = $(this).data('parting');
                var item = $(this).data('itemid');
                var kategori = $(this).data('kategori');
                var plastik = $(this).data('plastik');
                var qtyplastik = $(this).data('qtyplastik');
                var customer = $(this).data('customer');
                var subitem = $(this).data('subitem');

                $('#form-edit-id-hasil').val(id);
                $('#form-edit-nama-hasil').html(nama);
                $('#form-edit-qty-hasil').val(qty);
                $('#form-edit-berat-hasil').val(berat);
                $('#form-edit-parting').val(parting);
                $('#selected_item').val(item).trigger('change');
                $('#form-edit-plastik-hasil').val(plastik).trigger('change');
                $('#form-edit-qtyplastik-hasil').val(qtyplastik);
                $('#form-edit-keterangan-hasil').val(subitem);
                $('#form-edit-kategori').val(kategori);

                $('.select2').select2({
                    theme: 'bootstrap4',
                    tags: true,
                    dropdownParent: "#hasil-edit"
                })

                $("#customers").val(customer).trigger('change');

                if (plastik) {
                    document.getElementById('dataplastik').style = 'display:block';
                } else {
                    document.getElementById('dataplastik').style = 'display:none';
                }

            })
        </script>

        <div class="modal fade mymodal" id="hasil-edit" data-backdrop="static" data-keyboard="false" tabindex="-1"
            aria-labelledby="hasilLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="hasilLabel">Edit Hasil Produksi</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('regu.editproduksi', ['key' => 'hasil_produksi']) }}" method="post">
                        @csrf @method('put')
                        <input type="hidden" name="x_code" value="" id="form-edit-id-hasil">
                        <input type="hidden" name="freestock_status" value="{{ $data->status }}">
                        <div class="modal-body">

                            <div class="form-group">
                                <label for="selected_item">Item </label>
                                <select name="item" class="form-control select2" id="selected_item" {{ isset($ns) && $ns ? 'disabled' : '' }}>
                                    @foreach (\App\Models\Item::whereNotIn('category_id', ['21', '22', '23', '24', '25',
                                    '26', '27', '28', '29', '30'])->get() as $key)
                                    <option value="{{ $key->id }}">{{ $key->nama }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="row">
                                <div class="col pr-1">
                                    <div class="form-group">
                                        Jumlah Parting
                                        <input type="number" name="parting" value="" class="form-control"
                                            id="form-edit-parting">
                                    </div>
                                </div>
                                <div class="col pr-1">
                                    <div class="form-group">
                                        Ekor/Qty
                                        <input type="number" name="qty" value="" class="form-control"
                                            id="form-edit-qty-hasil" >
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

                            <div class="form-group">
                                Lokasi
                                <select id="form-edit-kategori" name="kategori" class="form-control">
                                    <option value="0">Chiller FG</option>
                                    <option value="1">ABF</option>
                                    <option value="2">Ekspedisi</option>
                                </select>
                            </div>

                            <div id="dataplastik" style="display: none">
                                <div class="row">
                                    <div class="col pr-1">
                                        <div class="form-group">
                                            Plastik
                                            {{-- <input type="text" class="form-control" id="form-edit-plastik-hasil">
                                            --}}
                                            <select name="plastik" id="form-edit-plastik-hasil"
                                                class="form-control select2">
                                                <option value=""></option>
                                                @foreach ($plastik as $val)
                                                <option value="{{ $val }}">{{ $val }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-3 pl-1">
                                        <div class="form-group">
                                            Qty
                                            <input type="number" name="jumlah_plastik" class="form-control"
                                                id="form-edit-qtyplastik-hasil">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col pr-1">
                                    <div class="form-group">
                                        Nama Customer
                                        <select name="customer" id="customers" class="form-control select2"
                                            data-width="100%" data-placeholder="Pilih Item">
                                            <option value=''></option>
                                            @foreach ($cs as $cus)
                                            <option value="{{ $cus->id }}">{{ $cus->nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                </div>
                                <div class="col pl-1">
                                    <div class="form-group">
                                        Nama Bumbu / Keterangan
                                        <input type="text" name="keterangan" class="form-control"
                                            id="form-edit-keterangan-hasil">
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

    </div>
</div>
