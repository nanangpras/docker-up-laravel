@php
$item = Item::where('nama', 'like', '%'.'frozen'.'%')
->get();
@endphp
<section class="">
    <form method="POST" enctype="multipart/form-data" action="{{route('pembelian.pokarkas', ['key' => 'frozen'])}}">
        @csrf
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
                        <option value="{{ $row->id }}">{{ $row->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="row">
                    <div class="col pr-1">
                        <div class="form-group">
                            Type PO
                            <select id="type_poKarkasFrozen" class="form-control" name="type_po"
                                data-placeholder="Pilih Form" data-width="100%" required readonly>
                                <option value="PO Karkas">PO Karkas</option>
                            </select>
                        </div>
                    </div>
                    <div class="col pl-1">
                        <div class="form-group">
                            Form PO
                            <select id="form_idPOKarkasFrozen" class="form-control" name="form_id"
                                data-placeholder="Pilih Form" data-width="100%" required readonly>
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
                                min="2023-01-01" @endif id="tanggalPOKarkasFrozen" name="tanggal" class="form-control"
                                value="{{date('Y-m-d')}}" min="{{date('Y-m-d')}}" required>
                        </div>
                    </div>
                    <div class="col pl-1">
                        <div class="form-group">
                            Tanggal Kirim
                            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                min="2023-01-01" @endif id="tanggal_kirimPOKarkasFrozen" name="tanggal_kirim"
                                class="form-control" value="{{date('Y-m-d', strtotime('+1 day'))}}" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col pr-1">
                        <div class="form-group">
                            Jenis Ekspedisi
                            <select id="jenis_ekspedisiPOKarkasFrozen" class="form-control" name="jenis_ekspedisi"
                                data-placeholder="Pilih Form" data-width="100%" required>
                                <option value="Kirim">Kirim</option>
                                <option value="Tangkap">Tangkap</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col pr-1">
                        <div class="form-group">
                            Link File
                            <textarea id="link_urlPOKarkasFrozen" type="text" class="form-control" name="url_link"
                                value=""
                                placeholder="https://drive.google.com/diasuhdkahs991823ku2hiuh/123i123hu98/1293"></textarea>
                        </div>
                        <div class="form-group">
                            Memo
                            <textarea id="keteranganPOKarkasFrozen" name="memo" rows="2"
                                placeholder="Tuliskan Memo (opsional)" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                {{-- <div id="history_po_karkasfrozen">
                </div> --}}
                <div class="data-loop">
                    <div class="card-header mb-2">
                        List Item
                    </div>
                    <div class="card card-body mb-3 list-itemPOKarkasFrozen-0">
                        <div class="form-group">
                            Item
                            <select required name="item[]" id="itemfrozen-0" class="form-control select2"
                                onchange="history_po_karkasfrozen(0)" data-placeholder="Pilih Item" data-width="100%">
                                <option value=""></option>
                                @foreach ($item as $row)
                                <option value="{{ $row->id }}">{{ $row->sku }}. {{ $row->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div id="history_po_karkasfrozen-0"></div>
                        <div class="row mt-2">
                            <div class="col pr-1">
                                <div class="form-group">
                                    <label>Qty/Pcs/Pack</label>
                                    <div class="input-group">
                                        <input type="number" id="qty" class="form-control rounded-0 p-1"
                                            autocomplete="off" placeholder="Qty/Pcs/Pack" value="" name="qty[]">
                                    </div>
                                </div>
                            </div>
                            <div class="col pl-1">
                                <div class="form-group">
                                    <label>Berat</label>
                                    <div class="input-group">
                                        <input type="number" id="berat" class="form-control rounded-0 p-1"
                                            autocomplete="off" placeholder="Berat" step="0.01" value="" name="berat[]"
                                            required>
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
                                            class="form-control rounded-0 p-1 input-amountPOKarkasFrozen-0"
                                            onkeyup="inputRupiahPoKarkasFrozen(0)" autocomplete="off"
                                            placeholder="Harga" value="" name="harga[]" required>
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
                                    <option value="{{Session::get('subsidiary')}} - Chiller Bahan Baku">
                                        {{Session::get('subsidiary')}} - Chiller Bahan Baku</option>
                                    <option value="{{Session::get('subsidiary')}} - Chiller Finished Good">
                                        {{Session::get('subsidiary')}} - Chiller Finished Good</option>
                                    <option value="{{Session::get('subsidiary')}} - Storage ABF">
                                        {{Session::get('subsidiary')}} - Storage ABF</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            Keterangan
                            <input type="text" name="keterangan[]" placeholder="Tulis keterangan" class="form-control"
                                autocomplete="off">
                        </div>
                    </div>
                    <div id="add-listPOKarkasFrozen"></div>
                    <a href="javascript:void(0)" class="btn btn-blue btn-sm mb-4"
                        onclick="addRowPoKarkasFrozen()">Tambah</a>
                </div>

            </div>
        </div>
        <button type="submit" class="btn btn-green btn-block"
            onclick="return confirm('Submit PO? pastikan data sudah benar')">Submit PO</button>
    </form>
</section>


<script>
    jumlahPOKarkasFrozen = 1;
function addRowPoKarkasFrozen(){
    row = `
            <div class="card card-body mb-3 list-itemPOKarkasFrozen" id="list-itemPOKarkasFrozen-`+jumlahPOKarkasFrozen+`">
                    <div class="bg-light text-right"><span onclick="deleteRow(`+jumlahPOKarkasFrozen+`)" class="cursor text-danger"><i class="fa fa-trash"></i> Hapus</span></div>
                    <div class="form-group">
                        Item
                            <select required name="item[]" id="itemfrozen-`+jumlahPOKarkasFrozen+`" class="form-control select2" onchange="history_po_karkasfrozen(`+jumlahPOKarkasFrozen+`)" data-placeholder="Pilih Item" data-width="100%">
                                <option value=""></option>
                                @foreach ($item as $row)
                                <option value="{{ $row->id }}">{{ $row->sku }}. {{ $row->nama }}</option>
                                @endforeach
                            </select>
                    </div>
                    <div id="history_po_karkasfrozen-`+jumlahPOKarkasFrozen+`"></div>
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
                                        class="form-control rounded-0 p-1" autocomplete="off" placeholder="Berat"  step="0.01" value="" name="berat[]" required>
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
                                        class="form-control rounded-0 p-1 input-amountPOKarkasFrozen-${jumlahPOKarkasFrozen}" onkeyup="inputRupiahPoKarkasFrozen(${jumlahPOKarkasFrozen})" autocomplete="off" placeholder="Harga" value="" name="harga[]" required>
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
                            <select class="form-control" name="gudang[]">
                                <option value="{{Session::get('subsidiary')}} - Chiller Bahan Baku">{{Session::get('subsidiary')}} - Chiller Bahan Baku</option>
                                <option value="{{Session::get('subsidiary')}} - Chiller Finished Good">{{Session::get('subsidiary')}} - Chiller Finished Good</option>
                                <option value="{{Session::get('subsidiary')}} - Storage ABF">{{Session::get('subsidiary')}} - Storage ABF</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        Keterangan
                        <input type="text" name="keterangan[]" placeholder="Tulis keterangan" class="form-control" autocomplete="off">
                    </div>
                </div>
    `;
    $('#add-listPOKarkasFrozen').append(row);
    $('.select2').select2({
        theme: 'bootstrap4'
    })
    
    jumlahPOKarkasFrozen++;
    
}

function deleteRow(rowid){
    $('#list-itemPOKarkasFrozen-'+rowid).remove();
}

function inputRupiahPoKarkasFrozen(e) {
    $('.input-amountPOKarkasFrozen-'+e).val(formatAmount($('.input-amountPOKarkasFrozen-'+e).val()));
    // console.log(e)
}

function history_po_karkasfrozen(rowid){
    const item = $('#itemfrozen-'+rowid+' option:selected').val();

    $('#history_po_karkasfrozen-'+rowid).load("{{ route('pembelian.purchase', ['key' => 'historyPO']) }}&subkey=pokarkasfrozen" + "&item_id=" + item + "&idrow=" + rowid);
}
</script>