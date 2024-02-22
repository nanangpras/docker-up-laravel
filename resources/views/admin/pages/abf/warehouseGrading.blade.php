@extends('admin.layout.template')

@section('title', 'Warehouse Grading Ulang')

@section('content')


<div class="row mb-4">
    <div class="col">
        <a href="javascript:void(0)" onclick="back()" data-url="{{url()->previous()}}"
            class="btn btn-outline btn-sm btn-back"> <i class="fa fa-arrow-left"></i> Back</a>
    </div>
    <div class="col-7 col text-center py-2">
        <b class="text-uppercase">WAREHOUSE GRADING ULANG</b>
    </div>
    <div class="col text-right"></div>
</div>

<section class="panel sticky-top">
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 col-6">
                <div class="small">ID</div>
                <b>{{ $getItemWarehouse->id }}</b>
                <div class="small">Nama Produk</div>
                <b>{{ $getItemWarehouse->product_id }} // {{ $getItemWarehouse->nama }}</b>
                <div class="small">Customer</div>
                <b>{{ $getItemWarehouse->konsumen->nama ?? '#'}}</b>
                <div class="small">Packaging</div>
                <b>{{ $getItemWarehouse->packaging }}</b>
            </div>
            <div class="col-md-4 col-6">
                <div class="small">Sub Item</div>
                <b>{{ $getItemWarehouse->sub_item ?? '#'}}</b>
                <div class="small">Plastik Group</div>
                <b>{{ $getItemWarehouse->plastik_group }}</b>
                <div class="small">Tanggal Produksi</div>
                <b>{{ $getItemWarehouse->production_date ?? '#'}} </b>
                <div class="small">Tanggal Kemasan</div>
                <b>{{$getItemWarehouse->tanggal_kemasan ?? '#'}}</b>
            </div>
            <div class="col-md-4 col-8">
                <div class="small">Qty/Pcs/Ekor Awal</div>
                <b>{{ $getItemWarehouse->qty_awal }} Pcs/Ekr/Pack</b>
                <div class="small">Berat (Kg) Awal</div>
                <b>{{ $getItemWarehouse->berat_awal }} Kg</b>
                <div class="small">Qty/Pcs/Ekor Sisa</div>
                <b>{{ $getItemWarehouse->qty }} Pcs/Ekr/Pack</b>
                <div class="small">Berat (Kg) Sisa</div>
                <b>{{ $getItemWarehouse->berat }} Kg</b>
            </div>
        </div>
        @if (Auth::user()->name != 'gudang')
        <br>
        <button type="button" class="btn btn-outline-success btn-sm mb-1 clickModalItemName" data-toggle="modal" data-id="sss0"
        data-target="#exampleModal">Tambah Item Name</button>
    
        <button type="button" class="btn btn-outline-success btn-sm mb-1 clickModalPlastik" data-toggle="modal" data-id="0"
        data-target="#plastikModal">Tambah Plastik Group</button>
        @endif
    </div>
</section>

<section class="panel">
    <div class="card mb-2">
        <div class="card-body">
            <form method="post" action="{{ route('abf.togudang', ['key' => 'storeWarehouseGrading']) }}">
                @csrf
                {{-- @method('patch') --}}
                <input type="hidden" name="id_product_gudang" value="{{ $getItemWarehouse->id }}">
                {{-- <input type="hidden" value="{{url()->previous()}}" name="url"> --}}

                <div class="data-loop mt-2 form-edit-soh0">
                    <section class="panel">
                        <div class="card-body">
                            
                            @if(env('NET_SUBSIDIARY', 'CGL')=='EBA')
                                <div class="form-group">
                                    <h6>Asal ABF</h6>
                                    <div class="radio-toolbar row">
                                        @php
                                        $abf = DataOption::getOption('jumlah_abf');
                                        @endphp
                                        @for($i = 0; $i < $abf; $i++) <div class="col pr-1">
                                            <div class="form-group text-center">
                                                <input type="radio" name="asal_abf0[]" value="abf_{{ $i+1 }}" class="abf0"
                                                    id="abf{{ $i+1 }}0">
                                                <label for="abf{{ $i+1 }}0">ABF {{ $i+1 }}</label>
                                            </div>
                                    </div>
                                    @endfor
                                    @if(env('NET_SUBSIDIARY', 'CGL')=='EBA')
                                    <div class="col pr-1">
                                        <div class="form-group text-center">
                                            <input type="radio" name="asal_abf0[]" value="abf_sewa_1" class="abf0" id="abf_sewa_10">
                                            <label for="abf_sewa_10">ABF SEWA 1</label>
                                        </div>
                                    </div>
                                    <div class="col pl-1">
                                        <div class="form-group text-center">
                                            <input type="radio" name="asal_abf0[]" value="abf_sewa_2" class="abf0" id="abf_sewa_20">
                                            <label for="abf_sewa_20">ABF SEWA 2</label>
                                        </div>
                                    </div>
                                    @endif
                                    <div class="col pl-1">
                                        <div class="form-group text-center">
                                            <input type="radio" name="asal_abf0[]" value="abf_beli" class="abf0" id="abf_beli0">
                                            <label for="abf_beli0">BELI</label>
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
                                            <input type="radio" name="tujuan0[]" value="{{ $w->id }}" class="tujuan0"
                                                id="gudang{{ $w->id }}">
                                            <label for="gudang{{ $w->id }}">{{ $w->code }}</label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="small mb-2">Item</div>
                                        {{-- <input type="hidden" name="item[]" value="{{$getItemWarehouse->product_id}}"> --}}
                                        {{-- <input type="text" value="{{$getItemWarehouse->nama}}" class="form-control"> --}}
                                        <select name="item[]" class="form-control select2 item"
                                            data-placeholder="Pilih Item" data-width="100%" required>
                                            <option value=""></option>
                                            @foreach ($getAllItem as $item)
                                            <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                            @endforeach
                                            
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <div class="small mb-2">Customer</div>
                                        <select name="konsumen[]" id="konsumen" class="form-control select2" data-width="100%" data-placeholder="Piilh Customer">
                                            <option value=""></option>
                                            @foreach ($customer as $cust)
                                            <option value="{{ $cust->id }}">{{ $cust->nama }} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <h6>Sub Item/Item Name <span class="small red">*</span></h6>
                                        <h5 id="loadingItemName0" style="display: none" class="text-center"><i class="fa fa-refresh fa-spin"></i>
                                            Loading....</h5>
                                        <div id="loadItemName0">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="small mb-2">Qty</div>
                                        <input type="number" name="qty[]" class="form-control"
                                            placeholder="Qty/pcs/ekor" value="" autocomplete="off" required
                                            step="0.01">
                                    </div>
                                    <div class="form-group">
                                        <div class="small mb-2">Berat</div>
                                        <input type="number" name="berat[]" class="form-control"
                                            placeholder="Berat" value="" autocomplete="off" required step="0.01">
                                    </div>
                                    <div class="form-group">
                                        <div class="small mb-2">Packaging</div>
                                        <select name="packaging[]" id="selectPackaging"
                                            data-placeholder="Pilih Item Name" class="form-control select2 mt-2" data-width="100%" required>
                                            {{-- <option value="{{$getItemWarehouse->packaging }}">{{$getItemWarehouse->packaging }}</option>
                                            --}}
                                            @foreach ($plastik as $p)
                                            <option value="{{ $p->nama }}" {{$getItemWarehouse->packaging == $p->nama ? 'selected' :
                                                ''}}>{{ $p->nama }} -
                                                {{ $p->subsidiary }}{{ $p->netsuite_internal_id }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">

                                    <div class="form-group">
                                        <div class="small mb-2">Parting</div>
                                        <input type="number" name="parting[]" class="form-control" placeholder="Parting"
                                            value="" autocomplete="off">
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="small mb-2">Karung</div>
                                            <select name="karung[]" id="selectSubItem"
                                                data-placeholder="Pilih Item Name" class="form-control mt-2" required>
                                                <option value="Curah">None</option>
                                                @foreach ($karung as $krg)
                                                <option value="{{ $krg->sku }}">{{ $krg->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col">
                                            <div class="form-group">
                                                <div class="small mb-2">Qty Karung</div>
                                                <input type="number" name="karung_qty[]" class="form-control"
                                                    placeholder="Qty" value="" autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group">
                                                <div class="small mb-2">Isi Karung</div>
                                                <input type="number" name="karung_isi[]" class="form-control"
                                                    placeholder="Isi Karung" value="" autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <h6>Plastik (AVIDA,POLOS,MEYER,MOJO) <span class="small red">*</span></h6>
                                        <h5 id="loadingPlastikGroup" style="display: none" class="text-center"><i class="fa fa-refresh fa-spin"></i>
                                            Loading....</h5>
                                        <div id="loadPlastikGroup">
                            
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="small mb-2">Tanggal Kemasan</div>
                                            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' )
                                                min="2023-01-01" @endif name="tanggal_kemasan[]" id="tanggal_kemasan"
                                                value="{{ $getItemWarehouse->tanggal_kemasan }}" class="form-control">
                                        </div>
                                        <div class="col">
                                            <div class="radio-toolbar row">
                                                <div class="col pr-2">
                                                    <div class="small mb-2">Stock</div>
                                                    <div class="form-group" id="typestock-0">
                                                        <input type="radio" name="stock[]" data-stock="0" value="free"
                                                            class="stock-0" id="free">
                                                        <label for="free">Free</label>
                                                    </div>
                                                </div>
                                                <div class="col pl-1">
                                                    <div class="small mb-2">Stock</div>
                                                    <div class="form-group" id="typestock-0">
                                                        <input type="radio" name="stock[]" data-stock="0"
                                                            value="booking" class="stock-0" id="booking">
                                                        <label for="booking">Booking</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="small">Expired Date</div>
                                    <div class="radio-toolbar row">
                                        <div class="col pr-1">
                                            <div class="form-group text-center">
                                                <input type="radio" name="expired[]" value="1" class="expired"
                                                    id="satu">
                                                <label for="satu">1 Bulan</label>
                                            </div>
                                        </div>
                                        <div class="col px-1">
                                            <div class="form-group text-center">
                                                <input type="radio" name="expired[]" value="3" class="expired"
                                                    id="tiga">
                                                <label for="tiga">3 Bulan</label>
                                            </div>
                                        </div>
                                        <div class="col px-1">
                                            <div class="form-group text-center">
                                                <input type="radio" name="expired[]" value="6" class="expired"
                                                    id="enam">
                                                <label for="enam">6 Bulan</label>
                                            </div>
                                        </div>
                                        <div class="col px-1">
                                            <div class="form-group text-center">
                                                <input type="radio" name="expired[]" value="12" class="expired"
                                                    id="duabelas">
                                                <label for="duabelas">12 Bulan</label>
                                            </div>
                                        </div>
                                        <div class="col pl-1">
                                            <div class="form-group">
                                                <input type="number" name="expired_custom[]"
                                                    class="px-1 text-center form-control" placeholder="Tulis Manual">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="small">Tanggal Input / Tanggal Produksi</div>
                                            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) min="2023-01-01" @endif value="{{ date('Y-m-d') }}" name="tanggal_input[]" id="tanggal_input" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    <div id="formNew"></div>
                </div>
                <a href="javascript:void(0)" type="button" class="btn btn-success btn-block btn-sm"
                    onclick="tambahForm()" id="tambahForm">Tambah Form</a>
                <button type="submit" class="btn btn-primary btn-block btn-sm">Simpan</button>
            </div>
        </form>
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
                            <input type="hidden" name="dataItemName" id="dataItemName" value=""/>
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

</section>

<script>
    var item = 1;
        function tambahForm() {
            // alert('ok');
            var rdio=$('.form-edit-soh'+item+' input[type=radio]').length;
            console.log(rdio);
            new_form = `
            <div class="data-loop mt-2 form-edit-soh${item}">
                    <section class="panel">
                        <div class="card-body">
                            
                            @if(env('NET_SUBSIDIARY', 'CGL')=='EBA')
                                <div class="form-group">
                                    <h6>Asal ABF</h6>
                                    <div class="radio-toolbar row">
                                        @php
                                        $abf = DataOption::getOption('jumlah_abf');
                                        @endphp
                                        @for($i = 0; $i < $abf; $i++) <div class="col pr-1">
                                            <div class="form-group text-center">
                                                <input type="radio" name="asal_abf${item}[]" value="abf_{{ $i+1 }}" class="abf${item}"
                                                    id="abf{{ $i+1 }}${item}">
                                                <label for="abf{{ $i+1 }}${item}">ABF {{ $i+1 }}</label>
                                            </div>
                                    </div>
                                    @endfor
                                    @if(env('NET_SUBSIDIARY', 'CGL')=='EBA')
                                    <div class="col pr-1">
                                        <div class="form-group text-center">
                                            <input type="radio" name="asal_abf${item}[]" value="abf_sewa_1" class="abf${item}" id="abf_sewa_1${item}">
                                            <label for="abf_sewa_1${item}">ABF SEWA 1</label>
                                        </div>
                                    </div>
                                    <div class="col pl-1">
                                        <div class="form-group text-center">
                                            <input type="radio" name="asal_abf${item}[]" value="abf_sewa_2" class="abf${item}" id="abf_sewa_2${item}">
                                            <label for="abf_sewa_2${item}">ABF SEWA 2</label>
                                        </div>
                                    </div>
                                    @endif
                                    <div class="col pl-1">
                                        <div class="form-group text-center">
                                            <input type="radio" name="asal_abf${item}[]" value="abf_beli" class="abf${item}" id="abf_beli${item}">
                                            <label for="abf_beli${item}">BELI</label>
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
                                            <input type="radio" name="tujuan${item}[]" value="{{ $w->id }}" class="tujuan${item}"
                                                id="gudang{{ $w->id }}${item}">
                                            <label for="gudang{{ $w->id }}${item}">{{ $w->code }}</label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="small mb-2">Item</div>
                                        {{-- <input type="hidden" name="item[]" value="{{$getItemWarehouse->product_id}}"> --}}
                                        {{-- <input type="text" value="{{$getItemWarehouse->nama}}" class="form-control"> --}}
                                        <select name="item[]" class="form-control select2 item"
                                            data-placeholder="Pilih Item" data-width="100%" required id="itemAll${item}">
                                            <option value=""></option>
                                            @foreach ($getAllItem as $item)
                                            <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                            @endforeach
                                            
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <div class="small mb-2">Customer</div>
                                        <select name="konsumen[]" id="konsumen${item}" class="form-control select2" data-width="100%" data-placeholder="Piilh Customer">
                                            <option value=""></option>
                                            @foreach ($customer as $cust)
                                            <option value="{{ $cust->id }}">{{ $cust->nama }} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <h6>Sub Item/Item Name <span class="small red">*</span></h6>
                                        <h5 id="loadingItemName${item}" style="display: none" class="text-center"><i class="fa fa-refresh fa-spin"></i>
                                            Loading....</h5>
                                        <div id="loadItemName${item}">
                                            
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="small mb-2">Qty</div>
                                        <input type="number" name="qty[]" class="form-control"
                                            placeholder="Qty/pcs/ekor" value="" autocomplete="off" required
                                            step="0.01">
                                    </div>
                                    <div class="form-group">
                                        <div class="small mb-2">Berat</div>
                                        <input type="number" name="berat[]" class="form-control"
                                            placeholder="Berat" value="" autocomplete="off" required step="0.01">
                                    </div>
                                    <div class="form-group">
                                        <div class="small mb-2">Packaging</div>
                                        <select name="packaging[]" id="selectPackaging${item}"
                                            data-placeholder="Pilih Item Name" class="form-control select2 mt-2" data-width="100%" required id="selectPackaging${item}">
                                            @foreach ($plastik as $p)
                                            <option value="{{ $p->nama }}" {{$getItemWarehouse->packaging == $p->nama ? 'selected' :
                                                ''}}>{{ $p->nama }} -
                                                {{ $p->subsidiary }}{{ $p->netsuite_internal_id }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">

                                    <div class="form-group">
                                        <div class="small mb-2">Parting</div>
                                        <input type="number" name="parting[]" class="form-control" placeholder="Parting"
                                            value="" autocomplete="off" id="parting${item}">
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="small mb-2">Karung</div>
                                            <select name="karung[]" id="selectSubItem${item}"
                                                data-placeholder="Pilih Item Name" class="form-control mt-2" required>
                                                <option value="Curah">None</option>
                                                @foreach ($karung as $krg)
                                                <option value="{{ $krg->sku }}">{{ $krg->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col">
                                            <div class="form-group">
                                                <div class="small mb-2">Qty Karung</div>
                                                <input type="number" name="karung_qty[]" class="form-control"
                                                    placeholder="Qty" value="" autocomplete="off" id="kartungqty${item}">
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group">
                                                <div class="small mb-2">Isi Karung</div>
                                                <input type="number" name="karung_isi[]" class="form-control"
                                                    placeholder="Isi Karung" value="" autocomplete="off" id="karingisi${item}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <h6>Plastik (AVIDA,POLOS,MEYER,MOJO) <span class="small red">*</span></h6>
                                        <h5 id="loadingPlastikGroup" style="display: none" class="text-center"><i class="fa fa-refresh fa-spin"></i>
                                            Loading....</h5>
                                        <div id="loadPlastikGroup${item}">
                                            <div class="form-group">
                                                <select name="plastik_group[]" id="selectPlastikGroup${item}" data-placeholder="Pilih Plastik" class="form-control select2 mt-2" required>
                                                        <option value=""></option>
                                                    @foreach ($plastikGroup as $plastik)
                                                        <option value="{{ $plastik->id }}">{{ $plastik->data }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="small mb-2">Tanggal Kemasan</div>
                                            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' )
                                                min="2023-01-01" @endif name="tanggal_kemasan[]" id="tanggal_kemasan${item}"
                                                value="{{ $getItemWarehouse->tanggal_kemasan }}" class="form-control">
                                        </div>
                                        <div class="col">
                                            <div class="radio-toolbar row">
                                                <div class="col pr-2">
                                                    <div class="small mb-2">Stock</div>
                                                    <div class="form-group" id="typestock-${item}">
                                                        <input type="radio" name="stock[]${item}" data-stock="${item}" value="free"
                                                            class="stock-${item}" id="free${item}">
                                                        <label for="free${item}">Free</label>
                                                    </div>
                                                </div>
                                                <div class="col pl-1">
                                                    <div class="small mb-2">Stock</div>
                                                    <div class="form-group" id="typestock-${item}">
                                                        <input type="radio" name="stock[]${item}" data-stock="${item}"
                                                            value="booking" class="stock-${item}" id="booking">
                                                        <label for="booking${item}">Booking</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="small">Expired Date</div>
                                    <div class="radio-toolbar row">
                                        <div class="col pr-1">
                                            <div class="form-group text-center">
                                                <input type="radio" name="expired[]${item}" value="1" class="expired"
                                                    id="satu${item}">
                                                <label for="satu${item}">1 Bulan</label>
                                            </div>
                                        </div>
                                        <div class="col px-1">
                                            <div class="form-group text-center">
                                                <input type="radio" name="expired[]${item}" value="3" class="expired"
                                                    id="tiga${item}">
                                                <label for="tiga${item}">3 Bulan</label>
                                            </div>
                                        </div>
                                        <div class="col px-1">
                                            <div class="form-group text-center">
                                                <input type="radio" name="expired[]${item}" value="6" class="expired"
                                                    id="enam${item}">
                                                <label for="enam${item}">6 Bulan</label>
                                            </div>
                                        </div>
                                        <div class="col px-1">
                                            <div class="form-group text-center">
                                                <input type="radio" name="expired[]${item}" value="12" class="expired"
                                                    id="duabelas${item}">
                                                <label for="duabelas${item}">12 Bulan</label>
                                            </div>
                                        </div>
                                        <div class="col pl-1">
                                            <div class="form-group">
                                                <input type="number" name="expired_custom[]${item}"
                                                    class="px-1 text-center form-control" placeholder="Tulis Manual">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="small">Tanggal Input / Tanggal Produksi</div>
                                            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) min="2023-01-01" @endif name="tanggal_input[]${item}" id="tanggal_input${item}" value="{{ date('Y-m-d') }}" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    <div id="formNew"></div>
                </div>
            `;
            $("#formNew").append(new_form);
            $('.select2').select2({theme: 'bootstrap4'});
            loadSecondItemName(item);

            item++;
        }


        function hapusForm(item) {
            $(".form-edit-soh"+item).remove();
            console.log(item);
            item--;
        }

        $(".btn-back").click(function (e) { 
            e.preventDefault();
            var url = $(this).attr('data-url');
            $(location).attr('href', url);
            window.close();
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

        function loadSecondItemName(id) {
            var loadSecondItem = `<div class="form-group">
                                <select name="subitem[]" id="selectSubItem${id}" data-placeholder="Pilih Item Name" class="form-control select2 mt-2" required>
                                    <option value=""></option>
                                    <option value="NONE">NONE</option>

                                    @foreach ($item_name as $name)
                                        <option value="{{ $name->id }}">{{ $name->data }}</option>
                                    @endforeach
                                </select>
                            </div>`;

            $("#loadingItemName"+ id).attr('style', 'display: block');
            $('#loadItemName'+ id).append(loadSecondItem).after($("#loadingItemName"+ id).attr('style', 'display: none'));
            $('.select2').select2({theme: 'bootstrap4'});
        }


        var loadItemName0 = `    <div class="form-group">
                                <select name="subitem[]" id="selectSubItem0" data-placeholder="Pilih Item Name" class="form-control select2 mt-2" required>
                                    <option value=""></option>
                                    <option value="NONE">NONE</option>

                                    @foreach ($item_name as $name)
                                        <option value="{{ $name->id }}">{{ $name->data }}</option>
                                    @endforeach
                                </select>
                            </div>`;

        $("#loadingItemName0").attr('style', 'display: block');
        $('#loadItemName0').append(loadItemName0).after($("#loadingItemName0").attr('style', 'display: none'));


        $('.submitItemName').on('click', function(){
            var key         =   $("#key").val() ;
            var itemname    =   $("#itemname").val() ;
            // $("#loadingItemName").attr('style', 'display: block');

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
                        // $('#selectSubItem').append('<option value="' + data.id + '" selected="selected">' + itemname + '</option>'); 
                        // $("#loadingItemName").attr('style', 'display: none')
                        $("#itemname").val('');
                        $("#tableListItemName").load("{{ route('abf.index') }}?key=loadItemNamePaginate");
                        $('#exampleModal').modal('hide');
                    } else {
                        showAlert(data.msg)
                        // $("#loadingItemName").attr('style', 'display: none')
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
                                        <select name="plastik_group[]" id="selectPlastikGroup" data-placeholder="Pilih Plastik" class="form-control select2 mt-2" required>
                                            <option value=""></option>
                                            @foreach ($plastikGroup as $plastik)
                                                <option value="{{ $plastik->id }}">{{ $plastik->data }}</option>
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
@stop

@section('footer')
<script>
    $('.select2').select2({
        theme: 'bootstrap4'
    });
</script>
@endsection