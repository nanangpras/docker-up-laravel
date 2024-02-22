@extends('admin.layout.template')

@section('title', 'Retur Detail')

@section('content')
@php
$ns = \App\Models\Netsuite::where('tabel_id', $data->id)
->where('label', 'receipt_return')
->where('tabel', 'retur')
->first();
@endphp
<div class="row mb-4">
    <div class="col">
        <a href="{{ route('retur.index') }}#custom-tabs-three-summary" class="btn btn-outline btn-sm btn-back"> <i
                class="fa fa-arrow-left"></i> Back</a>
    </div>
    <div class="col-7 col text-center py-2">
        <b class="text-uppercase">RETUR DETAIL</b>

    </div>
    <div class="col"></div>
</div>

<section class="panel">
    <div class="card-body">

        @if ($data->id_so != '')
        @php
        $order = \App\Models\Order::where('id_so', $data->id_so)->first();
        @endphp

        @if($order)
        <div class="row">
            <div class="col">
                <div class="small">Tanggal Retur</div>
                {{ $data->tanggal_retur }}
                <br>
                @if($data->status == '4')
                <span class="status status-info mt-1">NON INTEGRASI</span>
                @endif
            </div>
            <div class="col">
                <div class="form-group">
                    <div class="small">Nama Customer</div>
                    <td>{{ $data->to_customer->nama ?? '' }}</td>
                </div>
            </div>
            <div class="col">
                <div class="small">No SO</div>
                <span class="status status-success">{{ $order->no_so }}</span>
            </div>
            <div class="col">
                <div class="small">No DO</div>
                <span class="status status-success">{{ $order->no_do }}</span>
            </div>
            <div class="col">
                <div class="small">NO RA</div>
                @php
                if ($ns) {
                    try {
                        //code...
                        $resp = json_decode($ns->response, true);
                        echo "<span class='status status-info'>" . $resp[0]['message'] . '</span>';
                    } catch (\Throwable $th) {
                        //throw $th;
                        echo $th->getMessage();
                    }
                }
                @endphp
            </div>
        </div>

        {{-- <form method="post" action="{{ route('retur.sosubmit') }}" id="retur{{ $data->id }}" class="retur">
            @csrf --}}
            <input type="hidden" name="order_id" value="{{ $orderitem->id }}">
            <table class="table default-table">
                <thead>
                    <tr>
                        <th width=10px>No</th>
                        <th>Nama Item</th>
                        <th>Lokasi NS</th>
                        <th>Penanganan</th>
                        <th>Retur Qty</th>
                        <th>Retur Berat</th>
                        <th>Alasan</th>
                        <th>Bumbu</th>
                        <th>Kategori</th>
                        <th>Satuan</th>
                        <th>Sopir</th>
                        <th>Aksi</th>
                        <td></td>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $total = 0;
                    $berat = 0;
                    $count_retur = 0;
                    $rtr = \App\Models\ReturItem::where('retur_id', $data->id)->get();
                    @endphp
                    @foreach ($rtr as $i => $row)
                    @php
                    $total += $row->qty;
                    $berat += $row->berat;
                    $cekLog = \App\Models\Adminedit::where('table_name',
                    'retur_item')->where('type','retur')->where('activity', 'Retur Salah Item/Tidak Sesuai Pesanan')
                    ->where('table_id', $row->id)->first();
                    $cekedit = App\Models\Adminedit::where('table_id',$row->id)->where('type',
                    'edit')->where('activity', 'retur')->count();
                    @endphp
                    <tr @if($row->catatan == 'Salah Item' || $row->catatan == 'Barang Tidak Sesuai Pesanan/Order')
                        style="background-color: #FFFF8F" @endif>
                        <td>{{ ++$i }}
                            <input type="hidden" name="orderitem_id[]" value="{{ $row->id }}"
                                id="orderitem_id{{ $row->id }}">
                        </td>
                        <td>
                            @php
                            $chiller = null;
                            if($row->penanganan=="Reproses Produksi" || $row->penanganan=="Jual Sampingan"){
                            $chiller = \App\Models\Chiller::where('table_name', 'retur_item')->where('table_id',
                            $data->id)->where('item_id', $row->item_id)->first();
                            }

                            $abf = null;
                            if($row->penanganan=="Kembali ke Frezeer"){
                            $abf = \App\Models\Abf::where('table_name', 'retur_item')->where('table_id',
                            $data->id)->where('item_id', $row->item_id)->where('grade_item', $row->grade_item)->first();
                            }
                            @endphp
                            {{ $row->id}}. {{ \App\Models\Item::where('id', $row->item_id)->withTrashed()->first()->nama
                            }}@if($row->grade_item) <span class="text-primary pl-2 font-weight-bold uppercase"> // Grade
                                B </span> @endif
                            @if($row->catatan == 'Salah Item' || $row->catatan == 'Barang Tidak Sesuai Pesanan/Order')
                            <div class="row mt-1">
                                <div class="col-auto"><span class="text-info"><b>@if($cekLog) *ITEM DITUKAR DARI: <br>
                                            {{ $cekLog->data }} @else *ITEM TIDAK DITUKAR @endif</b></span></div>
                            </div>
                            @endif
                            @if($chiller)
                            <a href="{{url('admin/chiller/'.$chiller->id)}}" target="_blank"><span
                                    class="fa fa-share"></span></a>
                            @endif
                            @if($abf)
                            <a href="{{url('admin/abf/timbang/'.$abf->id)}}" target="_blank"><span
                                    class="fa fa-share"></span></a>
                            @endif
                        </td>
                        <td>{{ $row->unit }}</td>
                        <td>{{ $row->penanganan }} @if($ns) <button class="btn btn-info btn-sm float-right"
                                onclick="editPenanganan({{ $row->id }})" data-toggle="modal"
                                data-target="#editPenanganan">Edit</button> @endif </td>
                        <td>{{ $row->qty }}</td>
                        <td>{{ $row->berat }}</td>
                        <td>{{ $row->catatan }}</td>
                        <td>{{$row->orderitem->bumbu ?? '-'}}</td>
                        <td>{{ $row->kategori }}</td>
                        <td>{{ $row->satuan }}</td>
                        <td>{{ $row->todriver->nama ?? '' }}</td>
                        <td>
                            @if ($row->status == 1)
                            <button type="button" class="btn btn-primary" data-toggle="modal"
                                data-target="#editdo{{ $row->id }}"> Edit</button>
                            @else
                            <span class="status status-success">Retur Selesai</span>
                            {{-- <button type="button" class="btn btn-primary" data-toggle="modal"
                                data-target="#editdo{{ $row->id }}"> Edit</button> --}}
                            @endif
                        </td>
                        <td>
                            @if ($cekedit > 0)
                            <a href="{{route('retur.summary',['key' => 'logedit_retur'])}}&retur_id={{$row->id}}"
                                class="btn btn-warning" target="_blank"> History</a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            @php
            $rtr = \App\Models\ReturItem::where('retur_id', $data->id)->get();
            @endphp
            @foreach ($rtr as $i => $row)

            <div class="modal fade" id="editdo{{ $row->id }}" data-backdrop="static" data-keyboard="false"
                aria-labelledby="editdo{{ $row->id }}Label" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <form method="post" action="{{ route('retur.returdosubmit') }}">
                        @csrf
                        <input type="hidden" name="idedit" value="{{ $row->id }}">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="backretur{{ $row->id }}Label">Edit Retur DO</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <div class="modal-body">
                                <div class="form-group row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            Item
                                            <select name="item" class="form-control select2"
                                                data-placeholder="Pilih Item" data-width="100%">
                                                <option value=""></option>
                                                @foreach ($item as $it)
                                                <option value="{{ $it->id }}" @if ($row->item_id == $it->id) selected
                                                    @endif>{{ $it->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            Berat
                                            <input type="text" name="berat" class="form-control"
                                                value="{{ $row->berat }}" autocomplete="off">
                                        </div>

                                        <div class="form-group">
                                            QTY
                                            <input type="text" name="qty" class="form-control" value="{{ $row->qty }}"
                                                autocomplete="off">
                                        </div>
                                        <div class="form-group">
                                            Grade Item
                                            <ol class="switches">
                                                <li style="list-style-type:none;">
                                                    <input type="checkbox" name="gradeitem" class="additional-0"
                                                        value="grade b" @if ($row->grade_item == "grade b") checked
                                                    @endif id="grade_item">
                                                    <label for="grade_item">
                                                        <span>GRADE B</span>
                                                        <span></span>
                                                    </label>
                                                </li>
                                            </ol>
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="form-group">
                                            Alasan
                                            <select name="alasan" data-width="100%" class="form-control select2">
                                                <option value=""></option>
                                                @foreach ($alasan as $list)
                                                <option value="{{ $list->id }}" {{ ($row->catatan == $list->nama) ?
                                                    'selected' : '' }}>{{ $list->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            Tujuan
                                            <select name="tujuan" class="form-control">
                                                <option value="produksi" @if ($row->unit == 'chillerbb') selected
                                                    @endif>Reproses Produksi</option>
                                                <option value="chillerfg" @if ($row->unit == 'chillerfg') selected
                                                    @endif>Sampingan</option>
                                                <option value="gudang" @if ($row->unit == 'gudang') selected
                                                    @endif>Kembali Ke Frezeer</option>
                                                <option value="musnahkan" @if ($row->unit == 'musnahkan') selected
                                                    @endif>Musnahkan</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            Satuan
                                            <select name="satuan" class="form-control">
                                                <option value="ekor" @if ($row->satuan == 'ekor') selected
                                                    @endif>Ekor/Pcs/Pack</option>
                                                <option value="pcs" @if ($row->satuan == 'pcs') selected
                                                    @endif>Ekor/Pcs/Pack</option>
                                                <option value="karung" @if ($row->satuan == 'karung') selected
                                                    @endif>Karung</option>
                                                <option value="pack" @if ($row->satuan == 'pack') selected
                                                    @endif>Package</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary btnHiden btnHiden">Simpan</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @endforeach

            @if ($data->status == '1' || $data->status == '4')
            <form action="{{ route('retur.selesaikan') }}" method="POST">
                @csrf
                <input type="hidden" name="retur_id" id="retur_id{{ $data->id }}" value="{{ $data->id ?? '' }}">
                @if($data->status == '4') <input type="hidden" name="nonnetsuite" value="nonnetsuite"> @endif
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            Nama Penginput
                            <input type="text" class="form-control" id="operator" name="operator"
                                placeholder="Tuliskan Nama Penginput" required autocomplete="off">
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            Tanggal Retur
                            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                min="2023-01-01" @endif name="tanggal_input" value="{{ date('Y-m-d') }}"
                                id="tanggal_input" class="form-control">
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            Sopir
                            <select name="driver" class="form-control select2" data-placeholder="Pilih Sopir">
                                <option value=""></option>
                                @foreach ($sopir as $sop)
                                <option value="{{ $sop->id }}"> {{ $sop->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <button type="submit" id="sel" class="btn btn-red btn-block">Selesaikan</button>
            </form>

            @endif

            <div class="mt-3">
                <div>Total Qty : {{ number_format($total) }}</div>
                <div>Total Berat : {{ number_format($berat, 2) }}</div>
            </div>

            @else
            <div class="alert alert-danger">Order telah dihapus</div>
            @endif

            @else

            <div class="row">
                <div class="col mb-3">
                    <div class="small">Tanggal Retur</div>
                    {{ $data->tanggal_retur }}
                    <br>
                    @if($data->status == '4' || $data->status == '5')
                    <span class="status status-info mt-1">NON INTEGRASI</span>
                    @endif
                </div>
                <div class="col">
                    <div class="form-group">
                        <div class="small">Nama Customer</div>
                        <td>{{ $data->to_customer->nama ?? '' }}</td>
                    </div>
                </div>
                <div class="col">
                    <div class="small">No SO</div>
                    <span class="status status-danger">NON SO</span>
                </div>
                <div class="col">
                    <div class="small">NO RA</div>
                    @php
                    $ns = \App\Models\Netsuite::where('tabel_id', $data->id)
                    ->where('label', 'receipt_return')
                    ->where('tabel', 'retur')
                    ->first();
                    if ($ns) {
                    try {
                    //code...
                    $resp = json_decode($ns->response, true);
                    echo "<span class='status status-info'>" . $resp[0]['message'] . '</span>';
                    } catch (\Throwable $th) {
                    //throw $th;
                    echo $th->getMessage();
                    }
                    }
                    @endphp
                </div>
            </div>


            <table class="table default-table">
                <thead>
                    <tr>
                        <th width=10px>No</th>
                        <th>Nama Item</th>
                        <th>Lokasi NS</th>
                        <th>Penanganan</th>
                        <th>Retur Qty</th>
                        <th>Retur Berat</th>
                        <th>Alasan</th>
                        <th>Kategori</th>
                        <th>Satuan</th>
                        <th>Sopir</th>
                        <th>Aksi</th>
                        <td></td>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $rtr = \App\Models\ReturItem::where('retur_id', $data->id)->get();
                    @endphp
                    @foreach ($rtr as $i => $row)
                    @php
                    $cekedit =
                    App\Models\Adminedit::where('table_id',$row->id)->where('type','edit')->where('activity','retur')->count();
                    @endphp
                    <tr>
                        <td>{{ ++$i }}</td>
                        <td>{{ \App\Models\Item::where('id', $row->item_id)->withTrashed()->first()->nama }}
                            @if($row->grade_item) <span class="text-primary pl-2 font-weight-bold uppercase"> // Grade B
                            </span> @endif </td>
                        <td>{{ $row->unit }}</td>
                        <td>{{ $row->penanganan }}</td>
                        <td>{{ $row->qty }}</td>
                        <td>{{ $row->berat }}</td>
                        <td>{{ $row->catatan }}</td>
                        <td>{{ $row->kategori }}</td>
                        <td>{{ $row->satuan }}</td>
                        <td>{{ $row->todriver->nama ?? '' }}</td>
                        <td>
                            @if ($row->status == 1)
                            <button type="button" class="btn btn-primary" data-toggle="modal"
                                data-target="#edit{{ $row->id }}"> Edit</button>
                            @else
                            <span class="status status-success">Retur Selesai</span>
                            <button type="button" class="btn btn-primary" data-toggle="modal"
                                data-target="#edit{{ $row->id }}"> Edit</button>
                            @endif
                        </td>
                        <td>
                            @if ($cekedit > 0)
                            <a href="{{route('retur.summary',['key' => 'logedit_retur'])}}&retur_id={{$row->id}}"
                                class="btn btn-warning" target="_blank"> History</a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            @php
            $rtr = \App\Models\ReturItem::where('retur_id', $data->id)->get();
            @endphp
            @foreach ($rtr as $i => $row)

            <div class="modal fade" id="edit{{ $row->id }}" data-backdrop="static" data-keyboard="false" tabindex="-1"
                aria-labelledby="edit{{ $row->id }}Label" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <form method="post" action="{{ route('retur.nonsosubmit') }}">
                        @csrf
                        <input type="hidden" name="idedit" value="{{ $row->id }}">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="backretur{{ $row->id }}Label">Edit
                                    Retur
                                </h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <div class="modal-body">
                                <div class="form-group row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="">Item</label>
                                            <select name="item" class="form-control select2">
                                                <option value="" disabled selected hidden>Pilih Item
                                                </option>
                                                @foreach ($item as $it)
                                                <option value="{{ $it->id }}" @if ($row->item_id == $it->id) selected
                                                    @endif>{{ $it->nama }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="">Berat</label>
                                            <input type="text" name="berat" class="form-control" id="berat"
                                                placeholder="Tuliskan " value="{{ $row->berat }}" autocomplete="off">
                                            @error('berat') <div class="small text-danger">
                                                {{ message }}</div> @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="">QTY</label>
                                            <input type="text" name="qty" class="form-control" id="qty"
                                                placeholder="Tuliskan " value="{{ $row->qty }}" autocomplete="off">
                                            @error('qty') <div class="small text-danger">
                                                {{ message }}</div> @enderror
                                        </div>

                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            Alasan
                                            <select name="alasan" data-width="100%" class="form-control select2">
                                                <option value=""></option>
                                                @foreach ($alasan as $list)
                                                <option value="{{ $list->id }}" {{ ($row->catatan == $list->nama) ?
                                                    'selected' : '' }}>{{ $list->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="">Tujaun</label>
                                            <div class="form-group">
                                                <select name="tujuan" class="form-control" id="tujuan">
                                                    <option value="produksi" @if ($row->unit == 'chillerbb') selected
                                                        @endif>Reproses Produksi</option>
                                                    <option value="chillerfg" @if ($row->unit == 'chillerfg') selected
                                                        @endif>Sampingan</option>
                                                    <option value="gudang" @if ($row->unit == 'gudang') selected
                                                        @endif>Kembali Ke Freezer</option>
                                                    <option value="musnahkan" @if ($row->unit == 'musnahkan') selected
                                                        @endif>Musnahkan</option>
                                                </select>
                                                @error('tujuan') <div class="small text-danger">
                                                    {{ $message }}</div> @enderror
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="">Satuan</label>
                                            <select name="satuan" class="form-control">
                                                <option value="ekor" @if ($row->satuan == 'ekor') selected @endif>
                                                    Ekor
                                                </option>
                                                <option value="pcs" @if ($row->satuan == 'pcs') selected @endif>
                                                    Pcs
                                                </option>
                                                <option value="karung" @if ($row->satuan == 'karung') selected @endif>
                                                    Karung
                                                </option>
                                                <option value="pack" @if ($row->satuan == 'pack') selected @endif>
                                                    Pack</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary btnHiden">Simpan</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @endforeach

            @if ($data->status == '1' || $data->status == '4')
            <form action="{{ route('retur.selesaikan') }}" method="POST">
                @csrf
                <input type="hidden" name="retur_id" id="retur_id{{ $data->id }}" value="{{ $data->id ?? '' }}">
                @if($data->status == '4' || $data->status == '5') <input type="hidden" name="nonnetsuite"
                    value="nonnetsuite"> @endif
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            Nama Penginput
                            <input type="text" class="form-control" id="operator" name="operator"
                                placeholder="Tuliskan Nama Penginput" required autocomplete="off">
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            No DO
                            <input type="text" class="form-control" id="nodo" name="nodo"
                                placeholder="Tuliskan Nomer DO">
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            Tanggal Retur
                            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                min="2023-01-01" @endif name="tanggal_input" value="{{ date('Y-m-d') }}"
                                id="tanggal_input" class="form-control">
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            Sopir
                            <select name="driver" class="form-control select2" data-placeholder="Pilih Sopir">
                                <option value=""></option>
                                @foreach ($sopir as $sop)
                                <option value="{{ $sop->id }}"> {{ $sop->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <button type="submit" id="sel" class="btn btn-red btn-block btnHiden">Selesaikan</button>
            </form>
            @endif

            @endif

            @if ($data->operator)
            Penginput : {{ $data->operator }}
            @endif
    </div>
</section>

{{-- Modal Edit Penanganan --}}
@if($ns)
<div class="modal fade" id="editPenanganan" tabindex="-1" aria-labelledby="editPenangananLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Penanganan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('retur.edit') }}" method="POST" id="formModalEditPenanganan">
                <div class="modal-body">
                    <input type="hidden" name="key" value="updatePenanganan">
                    <input type="hidden" name="idReturItem" id="idReturItem" value="">
                    <select name="modalEditPenanganan" id="modalEditPenanganan" class="form-control">
                        <option value="" disabled selected hidden>Pilih Tujuan</option>
                        <option value="produksi">Reproses Produksi</option>
                        <option value="chillerfg">Sampingan</option>
                        <option value="gudang">Kembali Ke Freezer</option>
                        <option value="musnahkan">Musnahkan</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="updatePenanganan(this)">Update</button>
                </div>
            </form>
        </div>
        <output id="output"></output>
    </div>
</div>
@endif
{{-- End Modal Edit Penanganan --}}

@if ($data->status == 2)
@if ($ns)
<section class="panel">
    <div class="card-body">
        <h6>Netsuite Terbentuk</h6>

        <form method="post" action="{{route('sync.cancel')}}">
            @csrf
            <br>
            <button type="submit" class="btn btn-blue mb-1" name="status" value="approve">Approve Integrasi</button>
            &nbsp
            <button type="submit" class="btn btn-red mb-1" name="status" value="cancel">Batalkan Integrasi</button>
            &nbsp
            <button type="submit" class="btn btn-info mb-1" name="status" value="retry">Kirim Ulang</button> &nbsp
            <button type="submit" class="btn btn-success mb-1" name="status" value="completed">Selesaikan</button> &nbsp
            <button type="submit" class="btn btn-warning mb-1" name="status" value="hold">Hold</button> &nbsp
            <hr>
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
        </form>
    </div>
</section>
@else

<form action="{{ route('retur.selesaikan', ['key' => 'inject']) }}" method="POST">
    @csrf
    <input type="hidden" name="retur_id" id="retur_id{{ $data->id }}" value="{{ $data->id ?? '' }}">
    <button type="submit" class="btn btn-red btn-block btnHiden">Tembak Ulang</button>
</form>
@endif
@endif
@stop

@section('footer')
<script>
    $('#sel').on('click', function() {
        // edit
        $(this).hide();
        if ($("#operator").val() == '') {
            showAlert("Masukan nama penginput")
            //edit
            $(this).show();
        } 
    });
        
        
        
        $('.select2').select2({
            theme: 'bootstrap4'
        });
        
        
        $("#datacustomer").load("{{ route('retur.customer') }}");
        $("#itemretur").load("{{ route('retur.itemretur') }}");
        
</script>


{{-- Edit Retur Penanganan --}}
<script>
    editPenanganan = (id) => {
            const dataPenanganan = fetch("{{ route('retur.edit') }}", {
                headers: {
                    'Content-Type': 'application/json',
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                method: "POST",
                credentials: "same-origin",
                body: JSON.stringify({
                    id,
                    "key": "editPenanganan"
                })
            }).then((res) => {
                return res.json();
            }).then((data) => {
                var valuePenanganan = ""
                if(data.data.penanganan ==  "Reproses Produksi"){
                    valuePenanganan = "produksi"
                } else if (data.data.penanganan == "Jual Sampingan") {
                    valuePenanganan = "chillerfg"
                } else if (data.data.penanganan == "Kembali ke Frezeer"){
                    valuePenanganan = "gudang"
                } else {
                    valuePenanganan = "musnahkan"
                }
                // Set Value select option
                document.getElementById('modalEditPenanganan').value    = valuePenanganan
                document.getElementById('idReturItem').value            = id
            })
        }


        // Pada saat button modal edit diklik update
        updatePenanganan = e => {
            // console.log(e)
            const formPenanganan = document.getElementById('formModalEditPenanganan')
            const formData = new FormData(formPenanganan);
            var array = [];
            for(let [name, value] of formData) {
                array.push(value)
            }
            const updatePenanganan = fetch("{{ route('retur.edit') }}", {
                headers: {
                    'Content-Type': 'application/json',
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                method: "POST",
                credentials: "same-origin",
                body: JSON.stringify({
                    key: array[0],
                    id: array[1],
                    data: array[2]
                }),
            }).then((response) => {
                if(response.ok){
                    return response.json();
                }
            }).then((result) => {
                console.log(result)
                if(result.status == 'success'){
                    showNotif(result.message)
                    $('#editPenanganan').modal('hide')
                    location.reload()
                } else {
                    showAlert(result.message)
                }

            })

        }
</script>

@endsection