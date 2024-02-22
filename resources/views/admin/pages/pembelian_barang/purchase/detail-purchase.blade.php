@extends('admin.layout.template')

@section('title', 'Purchase Pembelian Barang')

@section('footer')
<script>
    $('.select2').select2({
        theme: 'bootstrap4',
    })
    $('.item').attr("disabled", true);
</script>
@php
$unit_measure = ['Piece', 'Roll', 'Lembar', 'Rim', 'Unit', 'Balok', 'Pack', 'Galon', 'Sachet', 'Tabung', 'Kaleng',
'Botol', 'Box', 'Buku', 'Drg', 'Dus', 'Kotak', 'Pasang', 'Slop', 'Tablet', 'Tube', 'Batang', 'Lusin', 'Set', 'Sak',
'Lot', 'Zak', 'Keranjang', 'Ekor', 'Meter', 'Centimeter', 'Liter', 'Mililiter', 'Kilogram', 'Gram', 'Ton', "Dump",
"Rit",'Jam', 'Menit', 'Detik'];
@endphp
<script>
    let x = parseInt($('#jumlahItemPO').val()) + 1;
function addRow(){
        let row = `<section class="panel deletePOList${x}">
            <input type="hidden" name="idlistpo[]" value="">
                <div class="bg-info px-2 text-light text-right"><span class="cursor"
                            onclick="deleteRow(${x})"><i
                                class="fa fa-trash"></i> Hapus</span></div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-12">
                                <input type="hidden" name="id_list[]" id="id_list${x}">
                                Item
                                <select required name="item[]" onchange="itemChange(${x})" class="form-control select2 item"
                                    data-placeholder="Pilih Item" data-width="100%" id="item_list${x}">
                                    <option value=""></option>
                                    @foreach ($daftar_pembelian as $daftar)
                                        <option value="{{ $daftar->item_id }}|{{ $daftar->id }}" data-id="{{ $daftar->id }}" data-category="{{ $daftar->item->category_id ?? '' }}">
                                            [No. PR : {{ $daftar->pembelian->no_pr ?? '' }}]
                                            {{ $daftar->item->sku ?? 'Sku Tidak terdaftar' }}.
                                            {{ $daftar->item->nama ?? 'Item tidak terdaftar' }} ({{ $daftar->unit ?? '' }})</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-6 pr-1">
                                Qty *Unit sudah otomatis sesuai item
                                <input type="number" required name="qty[]" class="form-control px-2"
                                    placeholder="Qty" autocomplete="off">
                            </div>

                        </div>

                        <div class="row mt-3">
                            <div class="col">
                                <div class="form-group">
                                    Keterangan
                                    <input type="text" name="keterangan[]"
                                        placeholder="Tulis keterangan" class="form-control"
                                        autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    Link URL
                                    <input type="text" name="link_url[]" placeholder="Tulis Link URL"
                                        class="form-control" autocomplete="off">
                                </div>
                            </div>
                        </div>

                        <div class="row ">
                            <div class="col">
                                <label for="harga${x}">Harga Unit</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">Rp</div>
                                    </div>
                                    <input type="number" id="harga${x}"
                                        class="form-control rounded-0 p-1" autocomplete="off" min="0" step="0.01"
                                        placeholder="Harga Unit" name="harga[]">
                                </div>
                            </div>
                        </div>
                        <div class="categorydibawah${x}" hidden>
                            <div class="row mt-2">
                                <div class="col pr-1">
                                    <div class="form-group">
                                        <label for="berat${x}">Berat DO</label>
                                        <div class="input-group">
                                            <input type="number" id="berat${x}"
                                                class="form-control rounded-0 p-1" autocomplete="off"
                                                min="1" placeholder="Tulis Berat" name="berat[]">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">Kg</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col pl-1">
                                    <div class="form-group">
                                        <label for="ukuran_ayam${x}">Ukuran Ayam</label>
                                        <div class="input-group">
                                            <select class="form-control"
                                                id="ukuran_ayam${x}" name="ukuran_ayam[]">
                                                <option value="1" >  < 1.1 </option>
                                                <option value="2" > 1.1-1.3 </option>
                                                <option value="3" > 1.2-1.4 </option>
                                                <option value="4" > 1.3-1.5 </option>
                                                <option value="5" > 1.4-1.6 </option>
                                                <option value="6" > 1.7-1.9 </option>
                                                <option value="7" > 1.8-2.0 </option>
                                                <option value="8" > 1.9-2.1 </option>
                                                <option value="9" > 2.0 Up</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col pr-1">
                                    <div class="form-group">
                                        <label for="jumlah_do${x}">Jumlah DO</label>
                                        <div class="input-group">
                                            <input type="number" id="jumlah_do${x}"
                                                class="form-control rounded-0 p-1" autocomplete="off"
                                                min="1" placeholder="DO" name="jumlah_do[]">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">Mbl</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col pl-1">
                                    <div class="form-group">
                                        <label for="unit_cetakan${x}">Harga
                                            Cetakan</label>
                                        <div class="input-group">
                                            <select class="form-control"
                                                id="unit_cetakan${x}" name="unit_cetakan[]">
                                                <option value="1" > Kg </option>
                                                <option value="2" > Ekor/Pcs/Pack </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col px-1 mb-3">
                            <div class="form-group">
                                <label for="gudang${x}">Gudang</label>
                                <div class="input-group">
                                    <select class="form-control select2" id="gudang${x}" name="gudang[]" required>
                                        <option value=""> - Pilih Gudang - </option>
                                        @php
                                            $gudang = App\Models\Gudang::where('subsidiary', Session::get('subsidiary'))->get();
                                        @endphp
                                        @foreach ($gudang as $g)
                                            <option value="{{ $g->netsuite_internal_id }}">
                                                {{ $g->code }} </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    </section>`;
    $('#data-loop').append(row);
    $('.select2').select2({
        theme: 'bootstrap4'
    })
    $('#jumlahItemPO').val(x)
    x++;
}

function itemChange(id){
    let idchange = $('#id_list'+id).val($('#item_list'+id).find(':selected').data('id'));
    let category = $('#item_list'+id).find(':selected').data('category');
    if(category < 23){
        $('.categorydibawah'+id).attr('hidden',false)
    } else{
        $('.categorydibawah'+id).attr('hidden',true)
        $('#berat'+id).val('')
        $('#ukuran_ayam'+id).val('')
        $('#jumlah_do'+id).val('')
        $('#unit_cetakan'+id).val('')
    }
}

function deleteRow(rowid,idPOList){
// console.log(idPOList)
    if(idPOList != undefined){
        $.ajax({
            url: "{{ route('pembelian.purchasestore') }}",
            type: "POST",
            data: {
                key: 'destroy_edit_list',
                idPOList: idPOList,
                _token: '{{ csrf_token() }}'
            },
            dataType: "JSON",
            success: function(data) {
                console.log(data)
                if(data.status == 'success'){
                    $('.deletePOList'+rowid).remove();
                    showNotif(data.msg)
                }
            }
        });
    } else {
        // console.log(rowid)
        $('.deletePOList'+rowid).remove();
    }
}
</script>
@endsection

@section('content')
<div class="row my-4">
    <div class="col"><a href="{{ route('pembelian.purchase') }}#summary"><i class="fa fa-arrow-left"></i> Kembali</a>
    </div>
    <div class="col-8 font-weight-bold text-uppercase text-center">Edit Purchase Pembelian Barang</div>
    <div class="col"></div>
</div>

<div class="tab-content mt-2">
    <div class="tab-pane fade show active" id="purchase" role="tabpanel" aria-labelledby="purchase-tab">
        <form action="{{ route('pembelian.purchasestore', ['key' => 'updateAllPurchaseList']) }}" method="post">
            @csrf
            <div class="row">
                <div class="col-md-6 pl-md-1">
                    <section class="panel">
                        <div class="card-body">
                            <div id="data_list">
                                <section class="panel">
                                    <div class="card-body">
                                        {{-- <div class="form-group">
                                            NO PO APP
                                            <input type="text" value="{{ $data->app_po ?? '#NOPOAPPS' }}"
                                                class="form-control" readonly>
                                        </div> --}}
                                        <input type="hidden" name="ideditpurchase" value="{{ $data->id }}">


                                        <div class="form-group">
                                            Supplier
                                            <select id="supplier" name="supplier" class="form-control select2"
                                                data-placeholder="Pilih Supplier" data-width="100%" required>
                                                <option value=""></option>
                                                @foreach ($supplier as $row)
                                                <option value="{{ $row->id }}" {{ $row->id == $data->supplier_id ?
                                                    'selected' : '' }}>
                                                    {{ $row->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            Nama Vendor *(KHUSUS ONE TIME VENDOR)
                                            <input type="text" id="vendor_name" name="vendor_name" class="form-control"
                                                value="{{ $data->vendor_name ?? ''}}"
                                                placeholder="Diisi Khusus One Time Vendor">
                                        </div>


                                        <div class="form-group">
                                            Type PO
                                            <select id="type_po" name="type_po" class="form-control select2"
                                                data-placeholder="Pilih Form" data-width="100%" required>
                                                <option value="PO Asset" {{ $data->type_po == 'PO Asset' ? 'selected' :
                                                    '' }}>PO Asset</option>
                                                <option value="PO Other Inventory" {{ $data->type_po == 'PO Other
                                                    Inventory' ? 'selected' : '' }}>PO
                                                    Other
                                                    Inventory</option>
                                                <option value="PO Packaging" {{ $data->type_po == 'PO Packaging' ?
                                                    'selected' : '' }}>PO Packaging
                                                </option>
                                                {{-- <option value="PO Ekspense" {{ $data->type_po == 'PO Ekspense' ?
                                                    'selected' : '' }}>PO Ekspense
                                                </option> --}}
                                                <option value="PO LB" {{ $data->type_po == 'PO LB' ? 'selected' : ''
                                                    }}>PO
                                                    LB</option>
                                                <option value="PO Karkas" {{ $data->type_po == 'PO Karkas' ? 'selected'
                                                    : '' }}>PO Karkas
                                                </option>
                                                <option value="PO Maklon" {{ $data->type_po == 'PO Maklon' ? 'selected'
                                                    : '' }}>PO Maklon
                                                </option>
                                                <option value="PO Evis" {{ $data->type_po == 'PO Evis' ? 'selected' : ''
                                                    }}>PO Evis</option>
                                                <option value="PO Non Karkas" {{ $data->type_po == 'PO Non Karkas' ?
                                                    'selected' : '' }}>PO Non
                                                    Karkas</option>
                                                {{-- <option value="PO Transit" {{ $data->type_po == 'PO Transit' ?
                                                    'selected' : '' }}>PO Transit
                                                </option> --}}
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            Form PO
                                            <select id="form_id" name="form_id" class="form-control select2"
                                                data-placeholder="Pilih Form" data-width="100%" required>
                                                @if (Session::get('subsidiary') == 'EBA')
                                                <option value="156" {{ $data->form_id == '156' ? 'selected' : '' }}>EBA
                                                    -
                                                    Form Purchase Order Ayam</option>
                                                <option value="157" {{ $data->form_id == '157' ? 'selected' : '' }}>EBA
                                                    -
                                                    Form Purchase Order Non Ayam</option>
                                                @else
                                                <option value="131" {{ $data->form_id == '131' ? 'selected' : '' }}>CGL
                                                    -
                                                    Form Purchase Order Ayam</option>
                                                <option value="132" {{ $data->form_id == '132' ? 'selected' : '' }}>CGL
                                                    -
                                                    Form Purchase Order Non Ayam</option>
                                                @endif
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            Jenis Ekspedisi
                                            <select id="jenis_ekspedisi" class="form-control select2"
                                                data-placeholder="Pilih Form" name="jenis_ekspedisi" data-width="100%"
                                                required>
                                                <option value="Other" {{ $data->jenis_ekspedisi == 'Other' ? 'selected'
                                                    : '' }}>Other
                                                </option>
                                                <option value="Tangkap" {{ $data->jenis_ekspedisi == 'Tangkap' ?
                                                    'selected' : '' }}>Tangkap
                                                </option>
                                                <option value="Kirim" {{ $data->jenis_ekspedisi == 'Kirim' ? 'selected'
                                                    : '' }}>Kirim
                                                </option>
                                            </select>
                                        </div>

                                        <div class="row">
                                            <div class="col">
                                                <div class="form-group">
                                                    Tanggal PO
                                                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' )
                                                        min="2023-01-01" @endif id="tanggal" class="form-control"
                                                        value="{{ $data->tanggal ?? date('Y-m-d') }}" name="tanggal"
                                                        required>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group">
                                                    Tanggal Kirim
                                                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' )
                                                        min="2023-01-01" @endif id="tanggal_kirim" class="form-control"
                                                        value="{{ $data->tanggal_kirim ?? date('Y-m-d') }}"
                                                        name="tanggal_kirim" required>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            Link File
                                            <textarea id="link_url" type="text" class="form-control" name="url_link"
                                                value="{{ $data->link_url }}"
                                                placeholder="https://drive.google.com/diasuhdkahs991823ku2hiuh/123i123hu98/1293">{{ $data->link_url }}</textarea>
                                        </div>

                                        <div class="form-group">
                                            Franko / Loko
                                            <select id="franko_loko" name="franko_loko" class="form-control"
                                                data-placeholder="Pilih Form" data-width="100%" required>
                                                <option {{ $data->franco_loco == "" ? 'selected' : '' }} value="">-
                                                    Pilih Alamat -</option>
                                                <option {{ $data->franco_loco == "Toko" ? 'selected' : '' }}
                                                    value="Toko">Toko</option>
                                                <option {{ $data->franco_loco == "Jl. KH. Wachid Hasyim, Sawo, Kec.
                                                    Jetis, Mojokerto" ? 'selected' : '' }} value="Jl. KH. Wachid Hasyim,
                                                    Sawo, Kec. Jetis, Mojokerto">Jl. KH. Wachid Hasyim, Sawo, Kec.
                                                    Jetis, Mojokerto</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            Memo
                                            <textarea id="keterangan_header" rows="2"
                                                placeholder="Tuliskan Memo (opsional)" class="form-control"
                                                name="keterangan_header">{{ $data->memo }}</textarea>
                                        </div>
                                        <div class="form-group">
                                            Created by {{ \App\Models\User::find($data->user_id ?? '')->name ?? '' }} ||
                                            {{ $data->created_at ?? '' }}
                                        </div>

                                    </div>
                                </section>
                            </div>
                        </div>
                    </section>
                </div>


                <div class="col-md-6 pl-md-1">
                    <section class="panel">
                        <div class="card-body">
                            <input type="hidden" id="jumlahItemPO" value="{{ count($list) }}">
                            <div id="data-loop">
                                @foreach ($list as $row)
                                @if (App\Models\Item::find($row->item_id)->sku == '7000000009')
                                @elseif(App\Models\Item::find($row->item_id)->sku == '7000000011')
                                @elseif(App\Models\Item::find($row->item_id)->sku == '7000000012')
                                @else
                                <section class="panel deletePOList{{ $loop->iteration }}">
                                    <input type="hidden" name="id_list[]" value="{{ $row->id }}">
                                    <input type="hidden" name="idlistpo[]" value="{{ $row->id }}">
                                    <div class="bg-info px-2 text-light text-right"><span class="cursor"
                                            onclick="deleteRow({{ $loop->iteration }}, {{ $row->id }})"><i
                                                class="fa fa-trash"></i> Hapus</span></div>
                                    <div class="card-body">
                                        <div class="row mb-2">
                                            <input type="hidden" value="{{ $row->item_id }}|{{ $row->id }}"
                                                name="item[]">
                                            <div class="col-12">
                                                Item
                                                <select required class="form-control item" data-placeholder="Pilih Item"
                                                    data-width="100%">
                                                    <option value="{{$row->item_id}}">[No. PR : {{ $row->pembelian->no_pr ?? '' }}] {{ App\Models\Item::find($row->item_id)->sku ?? "" }}. {{ App\Models\Item::find($row->item_id)->nama ?? "" }}</option>
                                                </select>
                                            </div>

                                        </div>

                                        <div class="row">
                                            <div class="col-6 pr-1">
                                                Qty
                                                <input type="number" required name="qty[]" class="form-control px-2"
                                                    placeholder="Qty" autocomplete="off" value="{{ $row->qty }}">
                                            </div>

                                            <div class="col-6 pl-1">
                                                Unit
                                                <input type="text" name="unit[]" placeholder="Unit" class="form-control"
                                                    autocomplete="off" value="{{ $row->unit }}">
                                            </div>
                                        </div>

                                        <div class="row mt-3">
                                            <div class="col">
                                                <div class="form-group">
                                                    Keterangan
                                                    <input type="text" name="keterangan[]"
                                                        placeholder="Tulis keterangan" class="form-control"
                                                        autocomplete="off" value="{{ $row->keterangan}}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="form-group">
                                                    Link URL
                                                    <input type="text" name="link_url[]" placeholder="Tulis Link URL"
                                                        class="form-control" autocomplete="off"
                                                        value="{{ $row->link_url}}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row ">
                                            <div class="col">
                                                <label for="harga{{ $loop->iteration }}">Harga Unit</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">Rp</div>
                                                    </div>
                                                    <input type="number" id="harga{{ $loop->iteration }}"
                                                        class="form-control rounded-0 p-1" autocomplete="off" min="0"
                                                        step="0.01" placeholder="Total Harga" value="{{ $row->harga }}"
                                                        name="harga[]">
                                                </div>
                                            </div>
                                        </div>


                                        @php
                                        $data_item = App\Models\Item::find($row->item_id);
                                        @endphp
                                        @if ($data_item->category_id < 23) <div class="row mt-2">
                                            <div class="col pr-1">
                                                <div class="form-group">
                                                    <label for="berat{{ $loop->iteration }}">Berat DO</label>
                                                    <div class="input-group">
                                                        <input type="number" id="berat{{ $loop->iteration }}"
                                                            class="form-control rounded-0 p-1" autocomplete="off"
                                                            min="1" placeholder="Tulis Berat" value="{{ $row->berat }}"
                                                            name="berat[]">
                                                        <div class="input-group-prepend">
                                                            <div class="input-group-text">Kg</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col pl-1">
                                                <div class="form-group">
                                                    <label for="ukuran_ayam{{ $loop->iteration }}">Ukuran Ayam</label>
                                                    <div class="input-group">
                                                        <select class="form-control"
                                                            id="ukuran_ayam{{ $loop->iteration }}" name="ukuran_ayam[]">
                                                            <option value="1" {{ $row->ukuran_ayam == '1' ? 'selected' :
                                                                ''}}> < 1.1 </option>
                                                            <option value="2" {{ $row->ukuran_ayam == '2' ? 'selected' :
                                                                '' }}> 1.1-1.3 </option>
                                                            <option value="3" {{ $row->ukuran_ayam == '3' ? 'selected' :
                                                                '' }}> 1.2-1.4 </option>
                                                            <option value="4" {{ $row->ukuran_ayam == '4' ? 'selected' :
                                                                '' }}> 1.3-1.5 </option>
                                                            <option value="5" {{ $row->ukuran_ayam == '5' ? 'selected' :
                                                                '' }}> 1.4-1.6 </option>
                                                            <option value="6" {{ $row->ukuran_ayam == '6' ? 'selected' :
                                                                '' }}> 1.7-1.9 </option>
                                                            <option value="7" {{ $row->ukuran_ayam == '7' ? 'selected' :
                                                                '' }}> 1.8-2.0 </option>
                                                            <option value="8" {{ $row->ukuran_ayam == '8' ? 'selected' :
                                                                '' }}> 1.9-2.1 </option>
                                                            <option value="9" {{ $row->ukuran_ayam == '9' ? 'selected' :
                                                                '' }}> 2.0 Up</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col pr-1">
                                            <div class="form-group">
                                                <label for="jumlah_do{{ $loop->iteration }}">Jumlah DO</label>
                                                <div class="input-group">
                                                    <input type="number" id="jumlah_do{{ $loop->iteration }}"
                                                        class="form-control rounded-0 p-1" autocomplete="off" min="1"
                                                        placeholder="DO" value="{{ $row->jumlah_do }}"
                                                        name="jumlah_do[]">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">Mbl</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col pl-1">
                                            <div class="form-group">
                                                <label for="unit_cetakan{{ $loop->iteration }}">Harga
                                                    Cetakan</label>
                                                <div class="input-group">
                                                    <select class="form-control" id="unit_cetakan{{ $loop->iteration }}"
                                                        name="unit_cetakan[]">
                                                        <option value="1" {{ $row->unit_cetakan == '1' ? 'selected' :
                                                            ''}}> Kg </option>
                                                        <option value="2" {{ $row->unit_cetakan == '2' ? 'selected' : ''
                                                            }}> Ekor/Pcs/Pack </option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    <div class="col px-1 mb-3">
                                        <div class="form-group">
                                            <label for="gudang{{ $loop->iteration }}">Gudang</label>
                                            <div class="input-group">
                                                <select class="form-control" id="gudang{{ $loop->iteration }}"
                                                    name="gudang[]" required>
                                                    <option value=""> - Pilih Gudang - </option>
                                                    @php
                                                    $gudang = App\Models\Gudang::where('subsidiary',
                                                    Session::get('subsidiary'))->get();
                                                    @endphp
                                                    @foreach ($gudang as $g)
                                                    <option value="{{ $g->netsuite_internal_id }}" {{ $row->gudang ==
                                                        $g->netsuite_internal_id ? 'selected' : '' }}>
                                                        {{ $g->code }} </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                    </section>
                    @endif
                    @endforeach
                </div>
            </div>
            </section>

            <span onclick="addRow()" class="cursor btn btn-green btn-sm mb-3"><i class="fa fa-plus"></i> Tambah</span>

            @if (Session::get('subsidiary') == 'EBA')
            <section class="panel">
                <div class="card-body">
                    <label for="ongkir">Ongkos Kirim</label>
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text">Rp</div>
                        </div>
                        <input type="number" autocomplete="off" value="{{ $data->list_ongkir->harga ?? '' }}" min="0"
                            step="0.01" placeholder="Tuliskan Ongkos Kirim" id="ongkir" name="ongkir"
                            class="form-control">

                    </div>
                    <div class="form-group">
                        @php
                        $item_ongkir = App\Models\Item::where('nama', 'like', '%BIAYA KIRIM%')->get();
                        @endphp
                        TYPE ONGKIR
                        <select name="ongkir_sku" id="ongkir_sku" class="form-control">
                            @foreach($item_ongkir as $ok)
                            <option value="{{$ok->sku}}" @if($ok->id == ($data->list_ongkir->item_id ?? 0)) selected
                                @endif>{{$ok->nama}}</option>
                            @endforeach
                        </select>
                    </div>
                    <label class="mt-2 px-2 pt-2 rounded status-danger">
                        <input id="hapusOngkir" type="checkbox" name="hapusOngkir"> <label for="hapusOngkir">Hapus
                            Ongkos Kirim</label>
                    </label>
                </div>
            </section>
            @endif
    </div>
</div>
@if ($data->status == 3)
<button class="btn btn-primary btn-block" onclick="return confirm('Update Purchase Pembelian Barang?')">UPDATE</button>
@else
<div class="row mb-4">
    <div class="col-md-6">
        <button class="btn btn-warning btn-block" name="pending" value="pending">DRAFT</button>
    </div>
    <div class="col-md-6">
        <button class="btn btn-primary btn-block"
            onclick="return confirm('Update Purchase Pembelian Barang?')">UPDATE</button>
    </div>
</div>
@endif
</form>
</div>
</div>
@endsection