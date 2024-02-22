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
$itemNonKarkas = Item::where('nama', 'like', '%'.'evis'.'%')
->orWhere('nama', 'like', '%'.'boneless'.'%')
->orWhereIn('category_id', ['4','5','6', '10', '11', '16'])
->get();
@endphp

<script>
    let jumlahPONonKarkas = 1 + parseInt($('#jumlahPONonKarkas').val());
    function addRowPONonKarkas(){
        row = `
                <div class="card card-body mb-3 list-itemPONonKarkas" id="list-itemPONonKarkas-`+jumlahPONonKarkas+`">
                    <input type="hidden" name="idlistpononkarkas[]" value="">
                        <div class="bg-light text-right"><span onclick="deleteRow(`+jumlahPONonKarkas+`)" class="cursor text-danger"><i class="fa fa-trash"></i> Hapus</span></div>
                        <div class="form-group">
                            Item
                                <select required name="item[]" class="form-control select2" data-placeholder="Pilih Item" data-width="100%">
                                    <option value=""></option>
                                    @foreach ($itemNonKarkas as $item)
                                    <option value="{{ $item->id }}">{{ $item->sku }}. {{ $item->nama }}</option>
                                    @endforeach
                                </select>
                        </div>
                        <div class="row mt-2">
                            <div class="col pr-1">
                                <div class="form-group">
                                    <label>Qty/Pcs/Pack</label>
                                    <div class="input-group">
                                        <input type="number" id="qty"
                                            class="form-control rounded-0 p-1" autocomplete="off" placeholder="Qty/Pcs/Pack" value="" name="qty[]">
                                    </div>
                                </div>
                            </div>
                            <div class="col pl-1">
                                <div class="form-group">
                                    <label>Berat</label>
                                    <div class="input-group">
                                        <input type="number" id="berat"
                                            class="form-control rounded-0 p-1" autocomplete="off" placeholder="Berat" value="" name="berat[]" step="0.01" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col pr-1">
                                <div class="form-group">
                                    <label>Harga</label>
                                    <div class="input-group">
                                        <input type="text" 
                                            class="form-control rounded-0 p-1 input-amountNonKarkas-${jumlahPONonKarkas}" onkeyup="inputRupiahPONonKarkas(${jumlahPONonKarkas})" autocomplete="off" placeholder="Harga" value="" name="harga[]" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col pl-1">
                                <div class="form-group">
                                    <label>Harga Cetakan</label>
                                    <div class="input-group">
                                        <select class="form-control" name="unit_cetakan[]">
                                            <option value="1" selected> Kg </option>
                                            <option value="2"> Ekor/Pcs/Pack </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Gudang</label>
                            <div class="input-group">
                                <select class="form-control" name="gudang[]" required>
                                    <option value="{{env('NET_SUBSIDIARY', 'CGL')}} - Chiller Bahan Baku">{{env('NET_SUBSIDIARY', 'CGL')}} - Chiller Bahan Baku</option>
                                    <option value="{{env('NET_SUBSIDIARY', 'CGL')}} - Chiller Finished Good">{{env('NET_SUBSIDIARY', 'CGL')}} - Chiller Finished Good</option>
                                    <option value="{{env('NET_SUBSIDIARY', 'CGL')}} - Storage ABF">{{env('NET_SUBSIDIARY', 'CGL')}} - Storage ABF</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            Keterangan
                            <input type="text" name="keterangan[]" placeholder="Tulis keterangan" class="form-control" autocomplete="off">
                        </div>
                    </div>
        `;
        $('#add-list').append(row);
        $('.select2').select2({
            theme: 'bootstrap4'
        })
        
        $('#jumlahPONonKarkas').val(jumlahPONonKarkas);
        jumlahPONonKarkas++;
    }
    
    function deleteRow(rowid,idPONonKarkas){
        if(idPONonKarkas != undefined){
            $.ajax({
                url: "{{ route('pembelian.destroy') }}",
                type: "POST",
                data: {
                    id: idPONonKarkas,
                    _token: '{{ csrf_token() }}'
                },
                dataType: "JSON",
                success: function(data) {
                    console.log(data)
                    if(data.status == 'success'){
                        $('#list-itemPONonKarkas-'+rowid).remove();
                        showNotif(data.msg)
                    }
                }
            });
        } else {
            $('#list-itemPONonKarkas-'+rowid).remove();
        }
    }
    
    function inputRupiahPONonKarkas(e) {
        $('.input-amountNonKarkas-'+e).val(formatAmount($('.input-amountNonKarkas-'+e).val()));
        // console.log(e)
    }
</script>
@endsection

@section('content')
<div class="row my-4">
    <div class="col"><a href="{{ route('pembelian.purchase') }}"><i class="fa fa-arrow-left"></i> Kembali</a>
    </div>
    <div class="col-8 font-weight-bold text-uppercase text-center">Edit PO Non Karkas</div>
    <div class="col"></div>
</div>


<section class="panel">
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data"
            action="{{route('pembelian.purchasestore',['key' => 'updatePONonKarkas'])}}">
            @csrf
            <input type="hidden" value="{{ $data->id }}" name="idEditPONonKarkas">
            <div class="row">
                <div class="col">
                    <div class="card-header mb-2">
                        Dokumen PO
                    </div>
                    <div class="form-group">
                        Supplier
                        <select name="supplier" class="form-control select2" data-placeholder="Pilih Supplier"
                            data-width="100%" required>
                            <option value=""></option>
                            @foreach ($supplier as $row)
                            <option value="{{ $row->id }}" {{ $row->id == $data->supplier_id ? 'selected' : '' }}>{{ $row->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col pr-1">
                            <div class="form-group">
                                Type PO
                                <select id="type_po" class="form-control" name="type_po" data-placeholder="Pilih Form"
                                    data-width="100%" required>
                                    <option value="PO Non Karkas" {{ $data->type_po == 'PO Non Karkas' ? 'selected' : ''
                                        }}>PO Non Karkas</option>
                                    <option value="PO Evis" {{ $data->type_po == 'PO Evis' ? 'selected' : ''}}>PO Evis
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col pl-1">
                            <div class="form-group">
                                Form PO
                                <select id="form_id" class="form-control" name="form_id" data-placeholder="Pilih Form"
                                    data-width="100%" required readonly>
                                    <option value="{{ Session::get('subsidiary') == 'CGL' ? '131' : '156' }}">{{ Session::get('subsidiary') }} - Form Purchase Order Ayam</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col pr-1">
                            <div class="form-group">
                                Tanggal PO
                                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                    min="2023-01-01" @endif id="tanggal" name="tanggal" class="form-control"
                                    value="{{ $data->tanggal ?? date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col pr-1">
                            <div class="form-group">
                                Tanggal Kirim
                                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                    min="2023-01-01" @endif id="tanggal_kirim" name="tanggal_kirim" class="form-control"
                                    value="{{ $data->tanggal_kirim ?? date('Y-m-d') }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col pr-1">
                            <div class="form-group">
                                Jenis Ekspedisi
                                <select id="jenis_ekspedisi" class="form-control" name="jenis_ekspedisi"
                                    data-placeholder="Pilih Form" data-width="100%" required>
                                    <option value="Tangkap" {{ $data->jenis_ekspedisi == 'Tangkap' ? 'selected' : ''
                                        }}>Tangkap
                                    </option>
                                    <option value="Kirim" {{ $data->jenis_ekspedisi == 'Kirim' ? 'selected' : ''
                                        }}>Kirim
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col pr-1">
                            <div class="form-group">
                                Link File
                                <textarea id="link_url" type="text" class="form-control" name="url_link" value=""
                                    placeholder="https://drive.google.com/diasuhdkahs991823ku2hiuh/123i123hu98/1293">{{ $data->link_url }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <input type="hidden" name="jumlahPONonKarkas" id="jumlahPONonKarkas" value="{{ count($list) }}">

                    <div class="data-loop">
                        <div class="card-header mb-2">
                            List Item
                        </div>
                        @foreach ($list as $row)
                        <input type="hidden" name="id_listpononkarkas[]" value="{{ $row->id }}">
                        <input type="hidden" name="idlistpononkarkas[]" value="{{ $row->id }}">
                        <div class="card card-body mb-3" id="list-itemPONonKarkas-{{ $loop->iteration }}">
                            <div class="bg-light text-right"><span
                                    onclick="deleteRow({{ $loop->iteration }},{{ $row->id }})"
                                    class="cursor text-danger"><i class="fa fa-trash"></i> Hapus</span></div>
                            <div class="form-group">
                                Item
                                <select required name="item[]" class="form-control select2"
                                    data-placeholder="Pilih Item" data-width="100%">
                                    <option value=""></option>
                                    @foreach ($itemNonKarkas as $item)
                                    {{-- {{ $item }} --}}
                                    <option value="{{ $item->id }}" {{ $item->id == $row->item_id ? 'selected' : ''
                                        }}>{{ $item->sku }}. {{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="row mt-2">
                                <div class="col pr-1">
                                    <div class="form-group">
                                        <label>Qty/Pcs/Pack</label>
                                        <div class="input-group">
                                            <input type="number" id="qty" class="form-control rounded-0 p-1"
                                                autocomplete="off" placeholder="Qty/Pcs/Pack" value="{{ $row->qty }}"
                                                name="qty[]">
                                        </div>
                                    </div>
                                </div>
                                <div class="col pl-1">
                                    <div class="form-group">
                                        <label>Berat</label>
                                        <div class="input-group">
                                            <input type="number" id="berat" class="form-control rounded-0 p-1"
                                                autocomplete="off" placeholder="Berat" value="{{ $row->berat }}"
                                                name="berat[]" step="0.01">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col pr-1">
                                    <div class="form-group">
                                        <label>Harga</label>
                                        <div class="input-group">
                                            <input type="text"
                                                class="form-control rounded-0 p-1 input-amountNonKarkas-{{ $loop->iteration }}"
                                                onkeyup="inputRupiahPONonKarkas({{ $loop->iteration }})"
                                                autocomplete="off" placeholder="Harga"
                                                value="{{ number_format($row->harga, 0, ',', '.') }}" name="harga[]"
                                                required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col pl-1">
                                    <div class="form-group">
                                        <label>Harga Cetakan</label>
                                        <div class="input-group">
                                            <select class="form-control" name="unit_cetakan[]">
                                                <option value="1" {{ $row->unit_cetakan == '1' ? 'selected' : '' }}> Kg
                                                </option>
                                                <option value="2" {{ $row->unit_cetakan == '2' ? 'selected' : '' }}>
                                                    Ekor/Pcs/Pack </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Gudang</label>
                                <div class="input-group">
                                    <select class="form-control" name="gudang[]" required>
                                        @foreach ($gudangPO as $gudangPOKarkas)
                                        <option value="{{ $gudangPOKarkas->code }}" {{ $row->gudang ==
                                            $gudangPOKarkas->netsuite_internal_id ? 'selected' : '' }}>
                                            {{ $gudangPOKarkas->code }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                Keterangan
                                <input type="text" value="{{ $row->keterangan }}" name="keterangan[]"
                                    placeholder="Tulis keterangan" class="form-control" autocomplete="off">
                            </div>
                        </div>
                        @endforeach

                        <div id="add-list"></div>
                        <a href="javascript:void(0)" class="btn btn-blue btn-sm mb-4"
                            onclick="addRowPONonKarkas()">Tambah</a>
                    </div>

                </div>
            </div>
            <button type="submit" class="btn btn-green btn-block"
                onclick="return confirm('Submit PO? pastikan data sudah benar')">UPDATE PO</button>
        </form>
    </div>
</section>



@endsection