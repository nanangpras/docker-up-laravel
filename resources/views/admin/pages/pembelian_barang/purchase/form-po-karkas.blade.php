<section class="">
    <form method="POST" enctype="multipart/form-data" action="{{route('pembelian.pokarkas')}}">
        @csrf
        <div class="row">
            <div class="col">
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
                            <select id="type_po" class="form-control" name="type_po" data-placeholder="Pilih Form"
                                data-width="100%" required readonly>
                                <option value="PO Karkas">PO Karkas</option>
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
                                value="{{date('Y-m-d')}}" min="{{date('Y-m-d')}}" required>
                        </div>
                    </div>
                    <div class="col pl-1">
                        <div class="form-group">
                            Tanggal Kirim
                            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                min="2023-01-01" @endif id="tanggal_kirim" name="tanggal_kirim" class="form-control"
                                value="{{date('Y-m-d', strtotime('+1 day'))}}" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col pr-1">
                        <div class="form-group">
                            Jenis Ekspedisi
                            <select id="jenis_ekspedisi" class="form-control" name="jenis_ekspedisi"
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
                            <textarea id="link_url" type="text" class="form-control" name="url_link" value=""
                                placeholder="https://drive.google.com/diasuhdkahs991823ku2hiuh/123i123hu98/1293"></textarea>
                        </div>
                        <div class="form-group">
                            Memo
                            <textarea id="keterangan" name="memo" rows="2" placeholder="Tuliskan Memo (opsional)"
                                class="form-control"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col">
                <div id="history_po_karkas">
                    {{-- Load PO LB --}}
                </div>
                <div class="data-loop">
                    <div class="card-header mb-2">
                        List Item
                    </div>
                    <div class="card card-body mb-3 list-itemPOKarkas">
                        <div class="form-group">
                            Item
                            <select required name="item[]" class="form-control" data-placeholder="Pilih Item"
                                data-width="100%">
                                <option value="1" selected>1100000001. AYAM KARKAS BROILER (RM)</option>
                                <option value="3">1100000003. AYAM MEMAR (RM)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            Keterangan
                            <select class="form-control" name="keterangan[]">
                                <option value="06-07">06-07</option>
                                <option value="07-08">07-08</option>
                                <option value="08-09">08-09</option>
                                <option value="09-10">09-10</option>
                                <option value="10-11">10-11</option>
                                <option value="11-12">11-12</option>
                                <option value="12-13">12-13</option>
                                <option value="13-14">13-14</option>
                                <option value="14-15">14-15</option>
                                <option value="15-16">15-16</option>
                                <option value="16-17">16-17</option>
                            </select>
                        </div>
                        <div class="row mt-2">
                            <div class="col pr-1">
                                <div class="form-group">
                                    <label>Qty</label>
                                    <div class="input-group">
                                        <input type="number" id="qty" class="form-control" autocomplete="off" min="1"
                                            placeholder="Qty" value="" name="qty[]" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col pl-1">
                                <div class="form-group">
                                    <label>Berat</label>
                                    <div class="input-group">
                                        <input type="number" id="berat" class="form-control" autocomplete="off"
                                            placeholder="Berat" value="" step="0.01" name="berat[]" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col pr-1">
                                <div class="form-group">
                                    <label>Harga</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control input-amountPOKarkas-0"
                                            autocomplete="off" onkeyup="inputRupiahPOKarkas(0)" placeholder="Harga"
                                            value="" name="harga[]" required>
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
                                    <option value="{{Session::get('subsidiary')}} - Chiller Bahan Baku">
                                        {{Session::get('subsidiary')}} - Chiller Bahan Baku</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div id="add-listPOKarkas"></div>
                    <a href="javascript:void(0)" class="btn btn-blue btn-sm mb-4" onclick="addRowPOKarkas()">Tambah</a>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-green btn-block"
            onclick="return confirm('Submit PO? pastikan data sudah benar')">Submit PO</button>
    </form>
</section>
<script>
    jumlahPOKarkas = 1;
function addRowPOKarkas(){
    row = `
            <div class="card card-body mb-3 list-itemPOKarkas" id="list-itemPOKarkas-`+jumlahPOKarkas+`">
                    <div class="bg-light text-right"><span onclick="deleteRowPOKarkas(`+jumlahPOKarkas+`)" class="cursor text-danger"><i class="fa fa-trash"></i> Hapus</span></div>
                    <div class="form-group">
                        Item
                            <select required name="item[]" class="form-control" data-placeholder="Pilih Item" data-width="100%">
                                <option value="1" selected>1100000001. AYAM KARKAS BROILER (RM)</option>
                                <option value="3">1100000003. AYAM MEMAR (RM)</option>
                            </select>
                    </div>
                    <div class="form-group">
                            Keterangan
                            <select class="form-control" name="keterangan[]">
                                <option value="06-07">06-07</option>
                                <option value="07-08">07-08</option>
                                <option value="08-09">08-09</option>
                                <option value="09-10">09-10</option>
                                <option value="10-11">10-11</option>
                                <option value="11-12">11-12</option>
                                <option value="12-13">12-13</option>
                                <option value="13-14">13-14</option>
                                <option value="14-15">14-15</option>
                                <option value="15-16">15-16</option>
                                <option value="16-17">16-17</option>
                            </select>
                        </div>
                    <div class="row mt-2">
                        <div class="col pr-1">
                            <div class="form-group">
                                <label>Qty</label>
                                <div class="input-group">
                                    <input type="number" id="qty"
                                        class="form-control" autocomplete="off"
                                        min="1" placeholder="Qty" value="" name="qty[]" required>
                                </div>
                            </div>
                        </div>
                        <div class="col pl-1">
                            <div class="form-group">
                                <label>Berat</label>
                                <div class="input-group">
                                    <input type="number" id="berat"
                                        class="form-control" autocomplete="off" placeholder="Berat" value="" name="berat[]" step="0.01" required>
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
                                        class="form-control input-amountPOKarkas-${jumlahPOKarkas}" onkeyup="inputRupiahPOKarkas(${jumlahPOKarkas})" autocomplete="off" placeholder="Harga" value="" name="harga[]" required>
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
                </div>
    `;
    $('#add-listPOKarkas').append(row);
    $('.select2').select2({
        theme: 'bootstrap4'
    })

    jumlahPOKarkas++;

}

function deleteRowPOKarkas(rowid){
    $('#list-itemPOKarkas-'+rowid).remove();
}


function inputRupiahPOKarkas(e) {
    $('.input-amountPOKarkas-'+e).val(formatAmount($('.input-amountPOKarkas-'+e).val()));
}
</script>