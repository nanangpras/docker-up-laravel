@extends('admin.layout.template')

@section('title', 'Timbang ABF')

@section('content')

<div class="row mb-4">
    <div class="col">
    </div>
    <div class="col-7 pt-2 text-center">
        <b>KONFIRMASI DATA TIMBANG ABF</b>
    </div>
    <div class="col"></div>
</div>


@php
    $pendingGudang = App\Models\Product_gudang::where('table_name', 'abf')->where('table_id', $data->id)->where('status', 0)->first();   
@endphp

<section class="panel sticky-top">
    <div class="card-body">
        <a href="{{ url('admin/abf#custom-tabs-diterima') }}" class="btn btn-outline btn-sm btn-back"> <i
                class="fa fa-arrow-left"></i> Back</a>
        <div class="row">
            <div class="col-md-3 col-6">
                <div class="small">ITEM</div>
                <b>{{ $data->item_name ?? 'TIDAK ADA' }} @if($data->grade_item)<br> <span
                        class="text-primary font-weight-bold uppercase"> // Grade B </span> @endif </b><br>
                <div class="small">JENIS</div>
                <b>
                    @if (strpos($data->item_name, 'FROZEN') !== false)
                    <span class="status status-danger">FROZEN</span>
                    @else
                    <span class="status status-info">FRESH</span>
                    @endif
                    @if ($data->selonjor)
                    <span class="status status-success font-weight-bold">SELONJOR</span>
                    @endif
                </b><br>
                <div class="small">ASAL TUJUAN</div>
                <b>{{ $data->table_name ?? '-' }}</b><br>

                @if($data->table_name=="chiller")
                <div class="small">CHILLER</div>
                <b><a href="{{url('admin/chiller', $data->table_id)}}">Ke Chiller Detail</a></b><br>
                @endif
                @if($data->table_name=="retur_item")
                <div class="small">RETUR</div>
                <b><a href="{{url('admin/retur/detail', $data->table_id)}}">Ke Retur Detail</a></b><br>
                @endif

            </div>
            <div class="col-md-2 col-6">
                <div class="small">QTY AWAL</div>
                <b>{{ $data->qty_awal }} Pcs/Ekr/Pack</b>
                <br>
                <div class="small">SISA QTY</div>
                <b>{{ $data->sisa_qty }} Pcs/Ekr/Pack</b>
                <br>
                <div class="small">TANGGAL</div>
                <b>{{ $data->tanggal_masuk }}</b>
                <br>
            </div>
            <div class="col-md-2 col-6">
                <div class="small">BERAT AWAL</div>
                <b>{{ $data->berat_awal }} Kg</b>
                <br>
                <div class="small">SISA BERAT</div>
                <b>{{ number_format($data->sisa_berat,2) }} Kg</b>
                <br>
            </div>
            <div class="col-md-5 col-6">
                <div class="small">PACKAGING</div>
                <b> {{ $data->packaging ?? 'TIDAK ADA' }}</b>
                <br>
                <div class="small">SUB ITEM</div>
                <b>{{ $data->konsumen->nama ?? '-' }}</b>
                <br>
                <div class="small">KETERANGAN</div>
                <b>{{ $sub_item->sub_item ?? '-' }}</b>
                <br>
            </div>
            <div class="col-md-12">
                @if($data->type=="gabungan")
                <span class="status status-danger">GABUNGAN</span>

                @php
                $gabs = App\Models\Abf::where('parent_abf', $data->id)->get();
                @endphp

                @foreach($gabs as $gb)
                <li><a href="{{url('admin/abf/timbang',$gb->id)}}">GABUNG#{{$gb->id}} {{$gb->item_name}}
                        [{{$gb->gabung_qty}} || {{$gb->gabung_berat}}]</a></li>
                @endforeach
                @endif

                @if($data->parent_abf!="")
                <span class="status status-danger"><a
                        href="{{url('admin/abf/timbang',$data->parent_abf)}}">GABUNGKE#ABF{{$data->parent_abf}}</a>
                    {{$data->item_name}}</span>
                @endif
            </div>
        </div>
    </div>
</section>

<section class="panel">
    <div class="card-body" style="padding: 20px">
        <form action="{{ route('abf.storetimbang') }}" id="form-timbang" method="POST" class="mb-4">
            @csrf <input type="hidden" name="id" id="id" value="{{ $data->id }}">
            <div class="" style="padding: 10px">

                @if(env('NET_SUBSIDIARY', 'CGL')=='EBA')
                <div class="form-group">
                    <h6>Asal ABF</h6>
                    <div class="radio-toolbar row">
                        @php
                        $abf = DataOption::getOption('jumlah_abf');
                        @endphp
                        @for($i = 0; $i < $abf; $i++) <div class="col pr-1">
                            <div class="form-group text-center">
                                <input type="radio" name="asal_abf" value="abf_{{ $i+1 }}" class="abf"
                                    id="abf{{ $i+1 }}">
                                <label for="abf{{ $i+1 }}">ABF {{ $i+1 }}</label>
                            </div>
                    </div>
                    @endfor
                    @if(env('NET_SUBSIDIARY', 'CGL')=='EBA')
                    <div class="col pr-1">
                        <div class="form-group text-center">
                            <input type="radio" name="asal_abf" value="abf_sewa_1" class="abf" id="abf_sewa_1">
                            <label for="abf_sewa_1">ABF SEWA 1</label>
                        </div>
                    </div>
                    <div class="col pl-1">
                        <div class="form-group text-center">
                            <input type="radio" name="asal_abf" value="abf_sewa_2" class="abf" id="abf_sewa_2">
                            <label for="abf_sewa_2">ABF SEWA 2</label>
                        </div>
                    </div>
                    @endif
                    <div class="col pl-1">
                        <div class="form-group text-center">
                            <input type="radio" name="asal_abf" value="abf_beli" class="abf" id="abf_beli">
                            <label for="abf_beli">BELI</label>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="mb-3 border-bottom">
                <h6>Tujuan</h6>
                <div class="radio-toolbar row">
                    @foreach ($warehouse as $w)
                    <div class="col">
                        <div class="form-group">
                            <input type="radio" name="tujuan" value="{{ $w->id }}" class="tujuan"
                                id="gudang{{ $w->id }}"  {{ $pendingGudang ? $pendingGudang->gudang_id == $w->id ? 'checked' : ''  : FALSE }}>
                            <label for="gudang{{ $w->id }}">{{ $w->code }}</label>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
    </div>

    <input type="checkbox" id="input_keyboard" @if(env('NET_SUBSIDIARY', 'CGL' )=='EBA' ) checked @endif>
    <label for="input_keyboard">Input menggunakan keyboard</label>
    <div class="row">
        <div class="col">
            <div class="form-group">
                <h6>Total Item</h6>
                <input type="text" id="qty" name="qty" readonly class="form-control label-timbang" autocomplete="off"
                    step="0.01" value="{{ $pendingGudang->qty_awal ?? $data->sisa_qty }}">
            </div>
            <div class="input_calculator">
                <div class="row my-3">
                    <div class="col pr-1">
                        <input type="button" style="font-size: 23px" class="btn btn-default btn-block form-control"
                            value="7" onclick="disitem('7')" />
                    </div>
                    <div class="col px-1">
                        <input type="button" style="font-size: 23px" class="btn btn-default btn-block form-control"
                            value="8" onclick="disitem('8')" />
                    </div>
                    <div class="col pl-1">
                        <input type="button" style="font-size: 23px" class="btn btn-default btn-block form-control"
                            value="9" onclick="disitem('9')" />
                    </div>
                </div>
                <div class="row my-3">
                    <div class="col pr-1">
                        <input type="button" style="font-size: 23px" class="btn btn-default btn-block form-control"
                            value="4" onclick="disitem('4')" />
                    </div>
                    <div class="col px-1">
                        <input type="button" style="font-size: 23px" class="btn btn-default btn-block form-control"
                            value="5" onclick="disitem('5')" />
                    </div>
                    <div class="col pl-1">
                        <input type="button" style="font-size: 23px" class="btn btn-default btn-block form-control"
                            value="6" onclick="disitem('6')" />
                    </div>
                </div>
                <div class="row my-3">
                    <div class="col pr-1">
                        <input type="button" style="font-size: 23px" class="btn btn-default btn-block form-control"
                            value="1" onclick="disitem('1')" />
                    </div>
                    <div class="col px-1">
                        <input type="button" style="font-size: 23px" class="btn btn-default btn-block form-control"
                            value="2" onclick="disitem('2')" />
                    </div>
                    <div class="col pl-1">
                        <input type="button" style="font-size: 23px" class="btn btn-default btn-block form-control"
                            value="3" onclick="disitem('3')" />
                    </div>
                </div>
                <div class="row my-3">
                    <div class="col pr-1">
                        <input type="button" style="font-size: 23px" class="btn btn-default btn-block form-control"
                            value="DEL" onclick="clrqtyspac()" />
                    </div>
                    <div class="col px-1">
                        <input type="button" style="font-size: 23px" class="btn btn-default btn-block form-control"
                            value="0" onclick="disitem('0')" />
                    </div>
                    <div class="col pl-1">
                        <input type="button" style="font-size: 23px" class="btn btn-default btn-block form-control"
                            value="." onclick="disitem('.')" />
                    </div>

                </div>
                <div class="row my-3">
                    <div class="col px-3">
                        <input type="button" style="font-size: 23px" class="btn btn-default btn-block form-control"
                            value="CLEAR" onclick="clrqty()" />
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <h6>Berat Setelah ABF</h6>
            <input type="text" id="result_abf" name="result_abf" class="form-control label-timbang" autocomplete="off"
                step="0.01" readonly value="{{ $pendingGudang->berat_awal ?? $data->sisa_berat }}">
            <div class="input_calculator">
                <div class="row my-3">
                    <div class="col pr-1">
                        <input type="button" style="font-size: 23px" class="btn btn-default btn-block form-control"
                            value="7" onclick="dist('7')" />
                    </div>
                    <div class="col px-1">
                        <input type="button" style="font-size: 23px" class="btn btn-default btn-block form-control"
                            value="8" onclick="dist('8')" />
                    </div>
                    <div class="col pl-1">
                        <input type="button" style="font-size: 23px" class="btn btn-default btn-block form-control"
                            value="9" onclick="dist('9')" />
                    </div>
                </div>
                <div class="row my-3">
                    <div class="col pr-1">
                        <input type="button" style="font-size: 23px" class="btn btn-default btn-block form-control"
                            value="4" onclick="dist('4')" />
                    </div>
                    <div class="col px-1">
                        <input type="button" style="font-size: 23px" class="btn btn-default btn-block form-control"
                            value="5" onclick="dist('5')" />
                    </div>
                    <div class="col pl-1">
                        <input type="button" style="font-size: 23px" class="btn btn-default btn-block form-control"
                            value="6" onclick="dist('6')" />
                    </div>
                </div>
                <div class="row my-3">
                    <div class="col pr-1">
                        <input type="button" style="font-size: 23px" class="btn btn-default btn-block form-control"
                            value="1" onclick="dist('1')" />
                    </div>
                    <div class="col px-1">
                        <input type="button" style="font-size: 23px" class="btn btn-default btn-block form-control"
                            value="2" onclick="dist('2')" />
                    </div>
                    <div class="col pl-1">
                        <input type="button" style="font-size: 23px" class="btn btn-default btn-block form-control"
                            value="3" onclick="dist('3')" />
                    </div>
                </div>

                <div class="row my-3">
                    <div class="col pr-1">
                        <input type="button" style="font-size: 23px" class="btn btn-default btn-block form-control"
                            value="DEL" onclick="clrtspac()" />
                    </div>
                    <div class="col px-1">
                        <input type="button" style="font-size: 23px" class="btn btn-default btn-block form-control"
                            value="0" onclick="dist('0')" />
                    </div>
                    <div class="col pl-1">
                        <input type="button" style="font-size: 23px" class="btn btn-default btn-block form-control"
                            value="." onclick="dist('.')" />
                    </div>
                </div>
                <div class="row my-3">
                    <div class="col px-3">
                        <input type="button" style="font-size: 23px" class="btn btn-default btn-block form-control"
                            value="CLEAR" onclick="clrt()" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row pt-3">
        <div class="col-md-6 mb-3">
            <h6>Packaging</h6>

            <div class="form-group">
                <input type="text" name="packaging" class="form-control" id="packaging" placeholder="Tuliskan "
                    value=" {{ $data->packaging ?? '' }}" readonly autocomplete="off">
            </div>

            <h6>Customer</h6>
            @if ($data->table_name == 'openbalance' && $data->asal_tujuan == 'open_balance')
            <select name="konsumen" id="konsumen" class="form-control select2">
                @foreach ($customer as $item)
                <option value="{{$item->id}} {{$data->customer_id == $item->id ? 'selected' : ''}}">{{$item->nama}}
                </option>
                @endforeach
            </select>
            @else
            <div class="form-group">
                <input type="text" name="konsumen" class="form-control" id="konsumen"
                    value="{{ $data->konsumen->nama ?? '' }}" autocomplete="off" readonly>
            </div>
            @endif

            <hr>

            <h6>Nama Item <span class="small red">*</span></h6>
            <div class="form-group">
                <input type="text" name="item" class="form-control" id="item" value="{{ $data->item_name }}"
                    autocomplete="off" readonly>
            </div>


            @if (Auth::user()->name != 'gudang')
            <button type="button" class="btn btn-outline-success btn-sm mb-1" data-toggle="modal"
            data-target="#exampleModal">Tambah Item Name</button>
            @endif
            <h6>Sub Item/Item Name <span class="small red">*</span></h6>
            <h5 id="loadingItemName" style="display: none" class="text-center"><i class="fa fa-refresh fa-spin"></i>
                Loading....</h5>
            <div id="loadItemName">
            </div>

            <h6>Parting <span class="small red">*</span></h6>
            <div class="form-group">
                <input type="text" name="parting" class="form-control" id="parting" value="{{ $pendingGudang->parting ?? $data->parting }}"
                    autocomplete="off" placeholder="contoh : 9">
            </div>

            <button type="button" class="btn btn-outline-success btn-sm mb-1" data-toggle="modal"
                data-target="#plastikModal">Tambah Plastik Group</button>
            <h6>Plastik (AVIDA,POLOS,MEYER,MOJO) <span class="small red">*</span></h6>
            <h5 id="loadingPlastikGroup" style="display: none" class="text-center"><i class="fa fa-refresh fa-spin"></i>
                Loading....</h5>
            <div id="loadPlastikGroup">

            </div>
            {{-- <div class="form-group"> --}}
                {{-- <input type="text" name="plastik_group" class="form-control" id="plastik_group"
                    value="{{ $data->plastik_group ?? '' }}" autocomplete="off" placeholder="contoh : AVIDA"> --}}

                {{-- </div> --}}
        </div>
        <div class="col-md-6 mb-3">
            <div class="form-group">
                <h6>Tanggal Kemasan</h6>
                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                    @endif name="tanggal_kemasan" id="tanggal_kemasan" class="form-control"
                    value="{{ $pendingGudang->tanggal_kemasan ?? $data->tanggal_masuk }}" placeholder="B1022C">
            </div>
            <div class="form-group">
                <h6>Kode Produksi (Jika input manual)</h6>
                <input type="text" name="kode_produksi" id="kode_produksi" class="form-control"
                    value="{{ $pendingGudang->production_code ?? $data->production_code }}">
            </div>

            <h6>Expired Date</h6>
            <div class="radio-toolbar row">
                <div class="col pr-1">
                    <div class="form-group text-center">
                        <input type="radio" name="expired" value="1" class="expired" id="satu" {{ $pendingGudang ? $pendingGudang->expired == 1 ? 'checked' : '' : FALSE }}>
                        <label for="satu">1 Bulan</label>
                    </div>
                </div>
                <div class="col px-1">
                    <div class="form-group text-center">
                        <input type="radio" name="expired" value="3" class="expired" id="tiga" {{ $pendingGudang ? $pendingGudang->expired == 3 ? 'checked' : '' : FALSE }}>
                        <label for="tiga">3 Bulan</label>
                    </div>
                </div>
                <div class="col px-1">
                    <div class="form-group text-center">
                        <input type="radio" name="expired" value="6" class="expired" id="enam" {{ $pendingGudang ? $pendingGudang->expired == 6 ? 'checked' : '' : FALSE }}>
                        <label for="enam">6 Bulan</label>
                    </div>
                </div>
                <div class="col px-1">
                    <div class="form-group text-center">
                        <input type="radio" name="expired" value="12" class="expired" id="duabelas" {{ $pendingGudang ? $pendingGudang->expired == 12 ? 'checked' : '' : FALSE }}>
                        <label for="duabelas">12 Bulan</label>
                    </div>
                </div>
                <div class="col pl-1">
                    <div class="form-group">
                        <input type="number" name="expired_custom" placeholder="Tulis Manual" id="exp_manual"
                            class="px-1 text-center form-control" value="{{ $pendingGudang ? $pendingGudang->expired != 1 && $pendingGudang->expired != 3 && $pendingGudang->expired != 6 && $pendingGudang->expired != 12 ? $pendingGudang->expired : '' : FALSE }}">
                    </div>
                </div>
            </div>
            <h6>Stock</h6>
            <div class="radio-toolbar row">
                <div class="col pr-1">
                    <div class="form-group">
                        <input type="radio" name="stock" value="free" class="stock" id="free" {{ $pendingGudang ? $pendingGudang->stock_type == 'free' ? 'checked' : '' : FALSE }}>
                        <label for="free">Free</label>
                    </div>
                </div>
                <div class="col pl-1">
                    <div class="form-group">
                        <input type="radio" name="stock" value="booking" class="stock" id="booking" {{  $pendingGudang ? $pendingGudang->stock_type == 'booking' ? 'checked' : '' : FALSE }}>
                        <label for="booking">Booking</label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <h6>Sub Packaging</h6>
                <input type="text" name="subpack" placeholder="Tulis Sub Packaging" id="subpack" class="form-control" value={{ $pendingGudang->subpack ?? '' }}>
            </div>

            <div class="form-group">
                @php
                $krng = App\Models\Item::where('subsidiary', 'like', '%'.Session::get('subsidiary').'%')
                ->where(function ($item) {
                $item->where('nama', 'like', 'KARUNG%')->orWhere('nama', 'like', 'KARTON%');
                $item->orWhere('nama', 'like', 'KARUNG%')->orWhere('nama', 'like', 'KARUNG%');
                })
                ->get();
                @endphp
                <h6>Karung</h6>
                <div class="row">
                    <div class="col">
                        <select name="karung" class="form-control" required>
                            <option value="">- Select Karung -</option>
                            <option value="0" {{ $pendingGudang ? $pendingGudang->karung == 0  ? 'selected' : '' : FALSE }}>NONE</option>
                            @foreach ($krng as $krs)
                            <option value="{{ $krs->sku }}" {{ $pendingGudang ? $pendingGudang->karung == $krs->sku ? 'selected' : '' : FALSE }}>{{ $krs->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col">
                        <input type="number" name="karung_qty" value="{{ $pendingGudang->karung_qty ?? '' }}" placeholder="Qty Karung" id="subpack"
                            class="form-control" required>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <h6>Isi Karung</h6>
                <input type="number" name="karung_isi" placeholder="Isi Karung" value="{{ $pendingGudang->karung_isi ?? '' }}" id="karung_isi" class="form-control">
            </div>

            @if(env('NET_SUBSIDIARY', 'CGL')=='CGL')
            <div class="form-group">
                <h6>Asal ABF</h6>
                <div class="radio-toolbar row">

                    @php
                    $abf            = DataOption::getOption('jumlah_abf');
                    if ($pendingGudang) {
                        $explodeIdABF   = explode("_", $pendingGudang->asal_abf);
                    }
                    @endphp

                    @if ($pendingGudang)

                    @for($i = 0; $i < $abf; $i++) <div class="col pr-1">
                        <div class="form-group text-center">
                            <input type="radio" name="asal_abf" value="abf_{{ $i+1 }}" class="abf" id="abf{{ $i+1 }}" @isset($explodeIdABF[1])
                                {{ $explodeIdABF[1] == $i+1 ? 'checked' : '' }}
                            @endisset>
                            <label for="abf{{ $i+1 }}" >ABF {{ $i+1 }}</label>
                        </div>
                    </div>
                    @endfor
                    @else
                    @for($i = 0; $i < $abf; $i++) <div class="col pr-1">
                        <div class="form-group text-center">
                            <input type="radio" name="asal_abf" value="abf_{{ $i+1 }}" class="abf" id="abf{{ $i+1 }}">
                            <label for="abf{{ $i+1 }}" >ABF {{ $i+1 }}</label>
                        </div>
                    </div>
                    @endfor
                @endif
            </div>
        </div>
        @endif

        {{-- <div class="form-group">
            <div class="radio-toolbar">
                <div class="form-group text-center">
                    <input type="checkbox" name="barang_titipan" value="1" id="titipan">
                    <label for="titipan">Barang Titipan</label>
                </div>
            </div>
        </div> --}}
    </div>
    </div>

    <button type="submit" class="btn btn-primary btnHiden btn-block btnSimpan" data-id="{{ $data->id }}">Simpan</button>
    </form>
    {{-- @endif --}}


    {{-- @if ($data->status != '2') --}}
    <div class="table-responsive">
        <hr>
        <h4>Pending Timbang</h4>
        <table class="table default-table mt-4" width="100%">
            <thead>
                <tr>
                    <th rowspan="2">#</th>
                    <th rowspan="2">Gudang</th>
                    <th rowspan="2">Konsumen / Sub Item</th>
                    <th rowspan="2">Item</th>
                    <th colspan="2" class="text-center">Tanggal</th>
                    <th colspan="2" class="text-center">Kemasan</th>
                    <th rowspan="2">ABF</th>
                    <th rowspan="2">Qty</th>
                    <th rowspan="2">Berat</th>
                    <th rowspan="2">Karung Isi</th>
                    <th rowspan="2">Pallete</th>
                    <th rowspan="2">Expired</th>
                    <th rowspan="2">Stock</th>
                    <th rowspan="2"></th>
                </tr>
                <tr>
                    <th>Diterima ABF</th>
                    <th>Kemasan</th>
                    <th>Packaging</th>
                    <th>SubPack</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data->hasil_timbang as $i => $row)
                <tr>
                    <td>{{ ++$i }}</td>
                    <td>{{ $row->productgudang->code ?? '' }}</td>
                    <td>{{ $row->konsumen->nama ?? '' }}
                        @if($row->grade_item)<br> <span class="text-primary font-weight-bold uppercase"> // Grade B
                        </span> @endif
                        <br>
                        @if ($row->sub_item)
                        Keterangan : {{ $row->sub_item }}
                        @endif
                    </td>
                    <td>
                        <div>{{ $row->productitems->nama ?? '' }}</div>
                        @if ($row->selonjor)
                        <div class="font-weight-bold text-danger">SELONJOR</div>
                        @endif
                        @if ($row->barang_titipan)
                        <div class="font-weight-bold text-primary">BARANG TITIPAN</div>
                        @endif
                    </td>
                    <td>{{ date('Y-m-d', strtotime($row->gudangabf->tanggal_masuk)) }}</td>
                    <td>{{ date('Y-m-d', strtotime($row->tanggal_kemasan)) }}<br>{{$row->production_code}}</td>
                    <td>{{ $row->packaging }}</td>
                    <td>{{ $row->subpack }}</td>
                    <td>{{ $row->asal_abf }}</td>
                    <td>{{ number_format($row->qty) }}</td>
                    <td>{{ number_format($row->berat, 2) }}</td>
                    <td>{{ $row->karung_isi }}</td>
                    <td>{{ number_format($row->palete) }}</td>
                    <td>{{ number_format($row->expired) }} Bulan</td>
                    <td>{{ $row->stock_type }}</td>
                    <td>
                        <i class="fa fa-trash text-danger hapus_timbang" data-id="{{ $row->id }}"></i>
                        <i class="fa fa-edit text-info edit_timbang" data-toggle="modal" data-target="#abf_timbang"
                            onclick="editABFTimbang($(this).data('id'))" data-id="{{ $row->id }}"></i>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{-- @php
    $pesentase_pending = ($data->sisa_berat / $data->berat_awal) * 100;
    @endphp
    @if ($pesentase_pending < 5) --}} 
        @if(count($data->hasil_timbang)>0)
            <div class="form-group">
                @if (strpos($data->item_name, 'FROZEN') !== false)
                <form action="{{ route('abf.selesai', $data->id) }}" method="post" id="formSubmitTI">
                    @csrf
                    <div class="form-group">
                        <label for="">Tanggal Bongkar *bisa diganti jika merupakan transaksi backdate</label>
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                            min="2023-01-01" @endif name="tanggal" class="form-control" id="tanggal"
                            value="{{ $data->tanggal_masuk }}">
                    </div>
                    <button type="submit" class="btn btn-success btn-block btnSubmitTI"><i
                            class="fa fa-spinner fa-spin spinerloading"
                            style="display:none; margin-right:2px;"></i>Selesaikan TI</button>
                </form>
                @else

                <form action="{{ route('abf.selesai', $data->id) }}" method="post" id="formSubmitWO">
                    @csrf
                    <div class="form-group">
                        <label for="">Tanggal Bongkar *bisa diganti jika merupakan transaksi backdate</label>
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                            min="2023-01-01" @endif name="tanggal" class="form-control" id="tanggal"
                            value="{{ $data->tanggal_masuk }}">
                    </div>
                    <button type="submit" class="btn btn-success btn-block btnSubmitWO"><i
                            class="fa fa-spinner fa-spin spinerloading"
                            style="display:none; margin-right:2px;"></i>Selesaikan WO</button>
                </form>
                @endif
            </div>
        @endif
        {{-- @else
        <div class="status status-danger">Presentase belum sesuai dengan benchmark
            {{ number_format($pesentase_pending, 2) }}% silahkan cek data inputan</div>
        @endif --}}

        {{-- @endif --}}
        <div class="table-responsive">
            <hr>
            <h4>Selesai Timbang</h4>
            <table class="table default-table mt-4" width="100%">
                <thead>
                    <tr>
                        <th rowspan="2">#</th>
                        <th rowspan="2">Gudang</th>
                        <th rowspan="2">Konsumen / Sub Item</th>
                        <th rowspan="2">Item</th>
                        <th colspan="2" class="text-center">Tanggal</th>
                        <th colspan="2" class="text-center">Kemasan</th>
                        <th rowspan="2">ABF</th>
                        <th rowspan="2">Qty Awal</th>
                        <th rowspan="2">Berat Awal</th>
                        <th rowspan="2">Qty</th>
                        <th rowspan="2">Berat</th>
                        <th rowspan="2">Karung Isi</th>
                        <th rowspan="2">Pallete</th>
                        <th rowspan="2">Expired</th>
                        <th rowspan="2">Stock</th>
                        <th rowspan="2">Aksi</th>
                    </tr>
                    <tr>
                        <th>Produksi</th>
                        <th>Kemasan</th>
                        <th>Packaging</th>
                        <th>SubPack</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data->hasil_timbang_selesai as $i => $row)
                    <tr>
                        <td>{{ ++$i }}</td>
                        <td>{{ $row->productgudang->code ?? '' }}</td>
                        <td>{{ $row->konsumen->nama ?? '' }}<br>
                            @if ($row->sub_item)
                            Keterangan : {{ $row->sub_item }}
                            @endif
                        </td>
                        <td>
                            <div>{{ $row->productitems->nama ?? '' }}</div>
                            @if ($row->selonjor)
                            <div class="font-weight-bold text-danger">SELONJOR</div>
                            @endif
                            @if ($row->barang_titipan)
                            <div class="font-weight-bold text-primary">BARANG TITIPAN</div>
                            @endif
                        </td>
                        <td>{{ date('Y-m-d', strtotime($row->production_date)) }}</td>
                        <td>{{ date('Y-m-d', strtotime($row->tanggal_kemasan)) }}<br>{{$row->production_code}}</td>
                        <td>{{ $row->packaging }}</td>
                        <td>{{ $row->subpack }}</td>
                        <td>{{ $row->asal_abf }}</td>
                        <td>{{ number_format($row->qty_awal) }}</td>
                        <td>{{ number_format($row->berat_awal, 2) }}</td>
                        <td>{{ number_format($row->qty) }}</td>
                        <td>{{ number_format($row->berat, 2) }}</td>
                        <td>{{ $row->karung_isi }}</td>
                        <td>{{ number_format($row->palete) }}</td>
                        <td>{{ number_format($row->expired) }} Bulan</td>
                        <td>{{ $row->stock_type }}</td>
                        <td><a href="{{ route('warehouse.tracing', $row->id) }}" class="btn btn-sm btn-blue"
                                target="_blank">Detail</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        </div>
</section>



<div class="modal fade" id="abf_timbang" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="editabftimbang" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editabftimbang">Edit ABF</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form>
                @csrf
                <input type="hidden" name="idabfedit" id="idabfedit" value="">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class=form-group>
                                <label> Qty</label>
                                <input class="form-control" type="text" name="qtyabfedit" value="" id="qtyabfedit"
                                    required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label> Berat</label>
                                <input type="number" step="0.01" name="beratabfedit" id="beratabfedit" value=""
                                    class="form-control" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class=form-group>
                                <label> Kode Produksi</label>
                                <input class="form-control" type="text" name="kodeproduksiabfedit" value=""
                                    id="kodeproduksiabfedit" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary edit_abftimbang">Edit</button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- MODAL TAMBAH ITEM NAME --}}
<div class="modal fade" id="exampleModal" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Item Name</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {{-- <form action="{{ route('abf.storetimbang') }}" method="post"> --}}
                {{-- @csrf --}}
                <input type="hidden" name="key" id="key" value="itemname">
                <div class="modal-body">

                    <div class="form-group">
                        PENCARIAN
                        <input type="text" id="searchItemName" name="searchItemName" placeholder="Tulis Pencarian"
                            class="form-control" autocomplete="off">
                    </div>

                    <section class="panel">
                        <div class="card-body">
                            <div id="tableListItemName">

                            </div>
                        </div>
                    </section>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary submitItemName">Submit</button>
                </div>
                {{--
            </form> --}}
        </div>
    </div>
</div>
{{-- END ITEM NAME --}}


{{-- MODAL TAMBAH PLASTIK GROUP --}}
<div class="modal fade" id="plastikModal" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Plastik Group</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {{-- <form action="{{ route('abf.storetimbang') }}" method="post"> --}}
                {{-- @csrf --}}
                <input type="hidden" name="key" id="keyPlastikGroup" value="plastikGroup">
                <div class="modal-body">
                    <div class="form-group">
                        Nama Plastik Group
                        <input type="text" id="plastikGroup" name="plastikGroup" placeholder="Tuliskan Plastik Group"
                            class="form-control" autocomplete="off" required>
                    </div>
                    <section class="panel">
                        <div class="card-body">
                            <div id="tablePlastikGroup">

                            </div>
                        </div>
                    </section>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary submitPlastikGroup">Submit</button>
                </div>
                {{--
            </form> --}}
        </div>
    </div>
</div>
{{-- END PLASTIK GROUP --}}

@if (User::setIjin('superadmin'))
<section class="panel">
    <div class="card-body">
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
                @php
                $ns = \App\Models\Netsuite::where('document_code', 'like', '%'.$data->id.'%')->where('label', 'like',
                '%abf%')->get();
                @endphp
                @foreach ($ns as $i => $n)
                @include('admin.pages.log.netsuite_one', ($netsuite = $n))
                @endforeach
            </tbody>
        </table>
    </div>
</section>

@endif

@stop
@section('footer')
<script>
    var keyboard    =   $("#input_keyboard:checked").val() ;
        $("#input_keyboard").on('change', function() {
            keyboard    =   $("#input_keyboard:checked").val() ;

        });

        function changeKeyboard(){

            if (keyboard == 'on') {
                $(".input_calculator").attr('style', 'display: none') ;
                $("#qty").attr('readonly', false) ;
                $("#result_abf").attr('readonly', false) ;
            } else {
                $(".input_calculator").attr('style', 'display: block') ;
                $("#qty").attr('readonly', true) ;
                $("#result_abf").attr('readonly', true) ;
            }
        }

        changeKeyboard();

</script>

<script>
    $('.select2').select2({
        theme: 'bootstrap4'
    });



</script>


{{-- SCRIPT PENCARIAN ITEM NAME--}}
<script>
    $("#searchItemName").on('keyup', function() {
        var itemName =  encodeURIComponent($(this).val());
        $("#tableListItemName").load("{{ route('abf.index') }}?key=loadItemNamePaginate&subKey=searchItemName&search="+itemName);

    })
</script>

{{-- END SCRIPT PENCARIAN ITEM NAME --}}

{{-- SCRIPT ITEM NAME --}}
<script>
    var loadItemName = `    <div class="form-group">
                                    <select name="subitem" id="selectSubItem" data-placeholder="Pilih Item Name" class="form-control select2 mt-2" required>
                                        <option value=""></option>
                                        <option value="NONE">NONE</option>

                                        @foreach ($item_name as $name)
                                            <option value="{{ $name->id }}" {{ $pendingGudang ?  $pendingGudang->sub_item == $name->data ? 'selected' : '' : FALSE }}>{{ $name->data }}</option>
                                        @endforeach
                                    </select>
                                </div>`;

        $("#loadingItemName").attr('style', 'display: block');
        $('#loadItemName').append(loadItemName).after($("#loadingItemName").attr('style', 'display: none'));
        


        $('.submitItemName').on('click', function(){
            var key         =   $("#key").val() ;
            var itemname    =   $("#itemname").val() ;
            $("#loadingItemName").attr('style', 'display: block');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('abf.storetimbang') }}",
                data: {
                    key,
                    itemname
                },
                method: 'POST',
                success: function(data){
                    console.log(data)
                    if (data.status == '200') {
                        showNotif(data.msg)
                        $('#selectSubItem').append('<option value="' + data.id + '" selected="selected">' + itemname + '</option>'); 
                        $("#loadingItemName").attr('style', 'display: none')
                        $("#itemname").val('');
                        $("#tableListItemName").load("{{ route('abf.index') }}?key=loadItemNamePaginate");
                        $('#exampleModal').modal('hide');
                    } else {
                        showAlert(data.msg)
                        $("#loadingItemName").attr('style', 'display: none')
                    }
                }
            })
        })


        $("#tableListItemName").load("{{ route('abf.index') }}?key=loadItemNamePaginate");

</script>
{{-- END SCRIPT ITEM NAME --}}


{{-- SCRIPT PLASTIK GROUP --}}
<script>
    var loadPlastikGroup = `    <div class="form-group">
                                        <select name="plastik_group" id="selectPlastikGroup" data-width=100% data-placeholder="Pilih Plastik" class="form-control select2 mt-2" required>
                                            <option value=""></option>
                                            @foreach ($plastikGroup as $plastik)
                                                <option value="{{ $plastik->id }}" {{ $pendingGudang ? $pendingGudang->plastik_group == $plastik->data ? 'selected' : '' : FALSE }}>{{ $plastik->data }}</option>
                                            @endforeach
                                        </select>
                                    </div>`;

        $("#loadingPlastikGroup").attr('style', 'display: block');
        $('#loadPlastikGroup').append(loadPlastikGroup).after($("#loadingPlastikGroup").attr('style', 'display: none'));
        


        $('.submitPlastikGroup').on('click', function(){
            var key             =   $("#keyPlastikGroup").val() ;
            var plastikGroup    =   $("#plastikGroup").val() ;
            $("#loadingPlastikGroup").attr('style', 'display: block');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('abf.storetimbang') }}",
                data: {
                    key,
                    plastikGroup
                },
                method: 'POST',
                success: function(data){
                    console.log(data)
                    if (data.status == '200') {
                        showNotif(data.msg)
                        $('#selectPlastikGroup').append('<option value="' + data.id + '" selected="selected">' + plastikGroup + '</option>'); 
                        $("#loadingPlastikGroup").attr('style', 'display: none')
                        $("#plastikGroup").val('');
                        $("#tablePlastikGroup").load("{{ route('abf.index') }}?key=loadPlastikGroupPaginate");
                        $('#plastikModal').modal('hide');
                    } else {
                        showAlert(data.msg)
                        $("#loadingPlastikGroup").attr('style', 'display: none')
                    }
                }
            })
        })


        $("#tablePlastikGroup").load("{{ route('abf.index') }}?key=loadPlastikGroupPaginate");

</script>
{{-- END SCRIPT PLASTIK GROUP --}}



<script>
    // $('.btnSimpan').on('click', function () {
    //     $(this).attr('disabled', 'disabled');
    //     $('.spinerloading').show();
    // })

    $('.btnSubmitTI').on('click', function () {
            $('.btnSubmitTI').attr('disabled', 'disabled');
            $(".spinerloading").show();
            $('#formSubmitTI').submit();
        })

        $('.btnSubmitWO').on('click', function () {
            $('.btnSubmitWO').attr('disabled', 'disabled');
            $(".spinerloading").show();
            $('#formSubmitWO').submit();
        })

        function enableSubmitButtons() {
        $('.btnSubmitTI').removeAttr('disabled');
        $('.btnSubmitWO').removeAttr('disabled');
    }

    function disableSubmitButtons() {
        $('.btnSubmitTI').attr('disabled', 'disabled');
        $('.btnSubmitWO').attr('disabled', 'disabled');
    }

    function handleAJAXSuccess() {
        enableSubmitButtons();
        $(".spinerloading").hide();
    }

    function handleAJAXError() {
        enableSubmitButtons(); // Enable the buttons in case of error
        $(".spinerloading").hide();
    }
</script>

<script>
    function editABFTimbang(id){
            console.log(id)
            $.ajax({
                url: "{{ route('abf.index') }}",
                data: {
                    id: id,
                    key: 'editabftimbang'
                },
                success: function(data){
                    // console.log(data.result.id)
                    $("#idabfedit").val(data.result.id)
                    $("#qtyabfedit").val(data.result.qty)
                    $("#beratabfedit").val(data.result.berat)
                    $("#kodeproduksiabfedit").val(data.result.production_code)
                }
            })
        }

        $('.edit_abftimbang').on('click', function(e){
            e.preventDefault();
            let id = $("#idabfedit").val()
            let qty = $("#qtyabfedit").val()
            let berat = $("#beratabfedit").val()
            let kodeproduksi = $("#kodeproduksiabfedit").val()
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('abf.togudang') }}",
                data: {
                    id: id,
                    qty: qty,
                    berat: berat,
                    production_code: kodeproduksi,
                    key: 'updateabftimbang'
                },
                method: 'POST',
                success: function(data){
                    console.log(data)
                    if (data.status == '200') {
                        location.reload()
                        showNotif(data.msg)
                    }
                }
            })
        })

</script>

<script>
    var id = $(this).data('id');
        var berat = $('#berat').val();
        var qty = $('#result').val();
        var subitem = $('#subitem').val();
        var tujuan = $('.tujuan:checked').val();
        var pallete = $('#pallete').val();
        var packaging = $('#packaging').val();
        var expired = $('.expired:checked').val();
        var stock = $('.stock:checked').val();

        $("#exp_manual").on('keyup', function() {
            $(".expired").prop("checked", false);

            expired = $(this).val();
            console.log(expired);
        });

        $(".expired").on('click', function() {
            $("#exp_manual").val("");
            expired = $(this).val();
            console.log(expired);
        });


        //function that display value
        function dist(val) {
            document.getElementById("result_abf").value += val
        }

        function dis(val) {
            document.getElementById("result").value += val
        }

        function disitem(val) {
            document.getElementById("qty").value += val
        }

        //function that evaluates the digit and return result
        function solve() {
            let x = document.getElementById("result").value
            let y = eval(x)
            document.getElementById("result").value = y
        }

        // ======== Berat Setelah ABF
        function clrt() {
            document.getElementById("result_abf").value = ""
        }

        function clrtspac() {
            let r = document.getElementById("result_abf").value;
            document.getElementById("result_abf").value = r.slice(0, -1);
        }
        // ======== Berat Setelah ABF

        // ======== Total Item
        function clrqty() {
            document.getElementById("qty").value = ""
        }

        function clrqtyspac() {
            let r = document.getElementById("qty").value;
            document.getElementById("qty").value = r.slice(0, -1);
        }
        // ======== Total Item

        $('.select2').select2({
            theme: 'bootstrap4'
        });

        $('.hapus_timbang').click(function() {
            var id = $(this).data('id');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{ route('abf.hapustimbang', $data->id) }}",
                method: "DELETE",
                data: {
                    id: id,
                },
                success: function(data) {
                    window.location.reload();
                }
            })
        });

        $('.prosestimbangabf').click(function() {

            id = $(this).data('id');
            berat = $('#berat').val();
            qty = $('#result').val();
            subitem = $('#subitem').val();
            tujuan = $('.tujuan:checked').val();
            pallete = $('#pallete').val();
            packaging = $('#packaging').val();
            kode_produksi = $('#kode_produksi').val();
            stock = $('.stock:checked').val();

            console.log(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });



            $.ajax({
                url: "{{ route('abf.storetimbang') }}",
                method: "POST",
                data: {
                    id: id,
                    berat: berat,
                    qty: qty,
                    tujuan: tujuan,
                    pallete: pallete,
                    kode_produksi: kode_produksi,
                    packaging: packaging,
                    expired: expired,
                    stock: stock,
                    subitem: subitem,
                },
                success: function(data) {
                    showNotif('Berhasil Simpan');
                    $('#berat').val('');
                    $('#qty').val('');
                    $('#subitem').val('');
                    $('.tujuan:checked').val(null).trigger('change');
                    $('#pallete').val('');
                    $('.stock:checked').val(null).trigger('change');
                    $('.expired:checked').val(null).trigger('change');
                    handleAJAXSuccess();
                }
            })
        })
</script>
@endsection