@extends('admin.layout.template')

@section('title', 'Edit SOH')

@section('content')


<div class="row mb-4">
    <div class="col">
        <a href="javascript:void(0)" onclick="back()" data-url="{{url()->previous()}}"
            class="btn btn-outline btn-sm btn-back"> <i class="fa fa-arrow-left"></i> Back</a>
        {{-- <a href="javascript:void(0)" type="button" class="btn btn-success btn-block btn-sm" onclick="tambahForm()"
            id="tambahForm">Tambah Form</a> --}}
    </div>
    <div class="col-7 col text-center py-2">
        <b class="text-uppercase">EDIT SOH</b>
    </div>
    <div class="col text-right"></div>
</div>

<section class="panel sticky-top">
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 col-6">
                <div class="small">ID</div>
                <b>{{ $edit->id }}</b>
                <div class="small">Nama Produk</div>
                <b>{{ $edit->product_id }} // {{ $edit->nama }}</b>
                <div class="small">Customer</div>
                <b>{{ $edit->konsumen->nama ?? '#'}}</b>
                <div class="small">Packaging</div>
                <b>{{ $edit->packaging }}</b>
            </div>
            <div class="col-md-4 col-6">
                <div class="small">Sub Item</div>
                <b>{{ $edit->sub_item ?? '#'}}</b>
                <div class="small">Plastik Group</div>
                <b>{{ $edit->plastik_group }}</b>
                <div class="small">Tanggal Produksi</div>
                <b>{{ $edit->production_date ?? '#'}} </b>
                <div class="small">Tanggal Kemasan</div>
                <b>{{$edit->tanggal_kemasan ?? '#'}}</b>
            </div>
            <div class="col-md-4 col-8">
                <div class="small">Qty/Pcs/Ekor Awal</div>
                <b>{{ $edit->qty_awal }} Pcs/Ekr/Pack</b>
                <div class="small">Berat (Kg) Awal</div>
                <b>{{ $edit->berat_awal }} Kg</b>
                <div class="small">Qty/Pcs/Ekor Sisa</div>
                <b>{{ $edit->qty }} Pcs/Ekr/Pack</b>
                <div class="small">Berat (Kg) Sisa</div>
                <b>{{ $edit->berat }} Kg</b>
            </div>
        </div>
    </div>
</section>

<section class="panel">
    <div class="card mb-2">
        <div class="card-body">
            <form method="post" action="{{ route('warehouse.soh_update',$edit->id) }}" class="retur" id="retur">
                @csrf
                @method('patch')
                <input type="hidden" name="id_product_gudang" value="{{ $edit->id }}">
                <input type="hidden" value="{{url()->previous()}}" name="url">

                <div class="data-loop mt-2 form-edit-soh0">
                    <section class="panel">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="small mb-2">Item</div>
                                        <input type="hidden" name="item[]" value="{{$edit->product_id}}">
                                        <input type="text" value="{{$edit->nama}}" class="form-control" readonly>
                                        {{-- <select name="item[]" class="form-control select2 item"
                                            data-placeholder="Pilih Item" data-width="100%" required>
                                            <option value=""></option> --}}
                                            {{-- @foreach ($item_list as $item) --}}
                                            {{-- <option value="{{ $edit->product_id }}">{{ $edit->nama }}</option> --}}
                                            {{-- @endforeach --}}
                                            {{--
                                        </select> --}}
                                    </div>
                                    <div class="form-group">
                                        <div class="small mb-2">Sub Item</div>
                                        <select name="subitem[]" id="selectSubItem" data-placeholder="Pilih Item Name"
                                            class="form-control select2">
                                            <option value=""></option>
                                            <option value="NONE">NONE</option>
                                            @foreach ($sub_item as $name)
                                            <option value="{{ $name->data }}">{{ $name->data }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <div class="small mb-2">Qty Awal</div>
                                        <input type="number" name="qty_awal[]" class="form-control"
                                            placeholder="Qty/pcs/ekor awal" value="" autocomplete="off" required
                                            step="0.01">
                                    </div>
                                    <div class="form-group">
                                        <div class="small mb-2">Berat Awal</div>
                                        <input type="number" name="berat_awal[]" class="form-control"
                                            placeholder="Berat awal " value="" autocomplete="off" required step="0.01">
                                    </div>
                                    <div class="form-group">
                                        <div class="small mb-2">Qty Sisa</div>
                                        <input type="number" name="qty[]" class="form-control"
                                            placeholder="Qty/pcs/ekor sisa" value="" autocomplete="off" required
                                            step="0.01">
                                    </div>
                                    <div class="form-group">
                                        <div class="small mb-2">Berat Sisa</div>
                                        <input type="number" name="berat[]" class="form-control"
                                            placeholder="Berat sisa" value="" autocomplete="off" required step="0.01">
                                    </div>

                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="small mb-2">Packaging</div>
                                        <select name="packaging[]" id="selectPackaging"
                                            data-placeholder="Pilih Item Name" class="form-control select2 mt-2"
                                            required>
                                            {{-- <option value="{{$edit->packaging }}">{{$edit->packaging }}</option>
                                            --}}
                                            @foreach ($plastik as $p)
                                            <option value="{{ $p->nama }}" {{$edit->packaging == $p->nama ? 'selected' :
                                                ''}}>{{ $p->nama }} -
                                                {{ $p->subsidiary }}{{ $p->netsuite_internal_id }}</option>
                                            @endforeach
                                        </select>
                                    </div>
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
                                        <div class="small mb-2">Plastik (AVIDA,POLOS,MEYER,MOJO)</div>
                                        <input type="text" name="plastik[]" class="form-control"
                                            placeholder="Isi Karung" value="{{ $edit->plastik_group }}"
                                            autocomplete="off">
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="small mb-2">Tanggal Kemasan</div>
                                            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' )
                                                min="2023-01-01" @endif name="tanggal_kemasan[]" id="tanggal_kemasan"
                                                value="{{ $edit->tanggal_kemasan }}" class="form-control">
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
                                    <div class="small mb-2">Expired Date</div>
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
                                </div>
                            </div>
                        </div>
                    </section>
                    <div id="formNew"></div>
                </div>
                <a href="javascript:void(0)" type="button" class="btn btn-success btn-block btn-sm"
                    onclick="tambahForm()" id="tambahForm">Tambah Form</a>
                <button type="submit" class="btn btn-primary btn-block btn-sm" id="btnSimpanEdit">Simpan</button>
        </div>
        </form>
    </div>
    </div>

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
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="small mb-2">Item</div>
                                    <input type="hidden" name="item[]" value="{{$edit->product_id}}">
                                    <input type="text" value="{{$edit->nama}}" class="form-control" readonly>
                                </div>
                                <div class="form-group">
                                    <div class="small mb-2">Sub Item</div>
                                    <select name="subitem[]" id="selectItem" data-placeholder="Pilih Item Name" class="form-control select2">
                                        <option value=""></option>
                                        <option value="NONE">NONE</option>
                                        @foreach ($sub_item as $name)
                                            <option value="{{ $name->data }}">{{ $name->data }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <div class="small mb-2">Qty Awal</div>
                                    <input type="number" name="qty_awal[]" class="form-control" placeholder="Qty/pcs/ekor awal" value="" autocomplete="off" required step="0.01">
                                </div>
                                <div class="form-group">
                                    <div class="small mb-2">Berat Awal</div>
                                    <input type="number" name="berat_awal[]" class="form-control" placeholder="Berat awal "value="" autocomplete="off" required step="0.01">
                                </div>
                                <div class="form-group">
                                    <div class="small mb-2">Qty Sisa</div>
                                    <input type="number" name="qty[]" class="form-control" placeholder="Qty/pcs/ekor sisa" value="" autocomplete="off" required step="0.01">
                                </div>
                                <div class="form-group">
                                    <div class="small mb-2">Berat Sisa</div>
                                    <input type="number" name="berat[]" class="form-control" placeholder="Berat sisa" value="" autocomplete="off" required step="0.01">
                                </div>
                                
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="small mb-2">Packaging</div>
                                    <select name="packaging[]" id="selectSubPackaging" data-placeholder="Pilih Item Name" class="form-control select2 mt-2" required>
                                        @foreach ($plastik as $p)
                                            <option value="{{ $p->nama }}" {{$edit->packaging == $p->nama ? 'selected' : ''}}>{{ $p->nama }} -
                                                {{ $p->subsidiary }}{{ $p->netsuite_internal_id }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <div class="small mb-2">Parting</div>
                                    <input type="number" name="parting[]" class="form-control" placeholder="Parting" value="" autocomplete="off">
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <div class="small mb-2">Karung</div>
                                        <select name="karung[]" id="selectSubItem" data-placeholder="Pilih Item Name" class="form-control select2 mt-2" required>
                                            <option value="Curah">None</option>
                                            @foreach ($karung as $krg)
                                                <option value="{{ $krg->sku }}">{{ $krg->nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <div class="small mb-2">Qty Karung</div>
                                            <input type="number" name="karung_qty[]" class="form-control" placeholder="Qty" value="" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <div class="small mb-2">Isi Karung</div>
                                            <input type="number" name="karung_isi[]" class="form-control" placeholder="Isi Karung" value="" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="small mb-2">Plastik (AVIDA,POLOS,MEYER,MOJO)</div>
                                    <input type="text" name="plastik[]" class="form-control"placeholder="Isi Karung" value="{{ $edit->plastik_group }}" autocomplete="off">
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <div class="small mb-2">Tanggal Kemasan</div>
                                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL')=='CGL') min="2023-01-01" @endif name="tanggal_kemasan[]" id="tanggal_kemasan" value="{{ $edit->tanggal_kemasan }}" class="form-control">
                                    </div>
                                    <div class="col">
                                        <div class="radio-toolbar row">
                                            <div class="col pr-2">
                                                <div class="small mb-2">Stock</div>
                                                <div class="form-group" id="typestock-0">
                                                    <input type="radio" name="stock[]${item}" data-stock="${item}" value="free" class="stock-0"
                                                        id="free${item}">
                                                    <label for="free${item}">Free</label>
                                                </div>
                                            </div>
                                            <div class="col pl-1">
                                                <div class="small mb-2">Stock</div>
                                                <div class="form-group" id="typestock-0">
                                                    <input type="radio" name="stock[]${item}" data-stock="${item}" value="booking" class="stock-0"
                                                        id="booking${item}">
                                                    <label for="booking${item}">Booking</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="small mb-2">Expired Date</div>
                                <div class="radio-toolbar row">
                                    <div class="col pr-1">
                                        <div class="form-group text-center">
                                            <input type="radio" name="expired[]${item}" value="1" class="expired" id="satu${item}">
                                            <label for="satu${item}">1 Bulan</label>
                                        </div>
                                    </div>
                                    <div class="col px-1">
                                        <div class="form-group text-center">
                                            <input type="radio" name="expired[]${item}" value="3" class="expired" id="tiga${item}">
                                            <label for="tiga${item}">3 Bulan</label>
                                        </div>
                                    </div>
                                    <div class="col px-1">
                                        <div class="form-group text-center">
                                            <input type="radio" name="expired[]${item}" value="6" class="expired" id="enam${item}">
                                            <label for="enam${item}">6 Bulan</label>
                                        </div>
                                    </div>
                                    <div class="col px-1">
                                        <div class="form-group text-center">
                                            <input type="radio" name="expired[]${item}" value="12" class="expired" id="duabelas${item}">
                                            <label for="duabelas${item}">12 Bulan</label>
                                        </div>
                                    </div>
                                    <div class="col pl-1">
                                        <div class="form-group">
                                            <input type="number" name="expired_custom[]" class="px-1 text-center form-control" placeholder="Tulis Manual" id="custom${item}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="javascript:void(0)" type="button" class="btn btn-danger btn-block btn-sm mb-2" onclick="hapusForm(`+item+`)" id="hapusForm">Hapus Form</a>
                    </div>
                </section>
            </div>
            `;
            $("#formNew").append(new_form);
            $('.select2').select2({theme: 'bootstrap4'});

            item++;
            console.log('tambah',item);
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



@stop

@section('footer')
<script>
    $('.select2').select2({
        theme: 'bootstrap4'
    });
</script>
@endsection