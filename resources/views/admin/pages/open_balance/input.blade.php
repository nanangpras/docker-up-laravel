<div class="row">
    <div class="col-7 col-md  pr-1">
        <div class="form-group">
            Item
            <select name="item" id="item" class="form-control select2" data-placeholder="Pilih Item" data-width="100%">
                <option value=""></option>
                @foreach ($item as $row)
                <option value="{{ $row->id }}">{{ $row->nama }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-5 col-md-2 px-md-1 pl-1">
        <div class="form-group">
            Tanggal Produksi
            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                @endif name="tanggal" id="tanggal" class="form-control">
        </div>
    </div>
    <div class="col col-md-3 pl-md-1">
        <div class="form-group">
            Jenis Gudang
            <select name="gudang" id="gudang" class="form-control">
                <option value="" disabled selected hidden>Pilih Jenis Gudang</option>
                <option value="chiller">Chiller</option>
                <option value="abf">Storage ABF</option>
                <option value="cold1">Cold Storage</option>
                {{-- <option value="cold2">Cold Storage 2</option>
                <option value="cold3">Cold Storage 3</option>
                <option value="cold4">Cold Storage 4</option> --}}
            </select>
        </div>
    </div>
</div>

<div class="row">
    <div class="col pr-1">
        <div class="form-group">
            Tipe
            <select id="tipe_item" class="form-control">
                <option value="" selected hidden disabled>Pilih Tipe Item</option>
                <option value="hasil-produksi">Hasil Produksi</option>
                <option value="bahan-baku">Bahan Baku</option>
            </select>
        </div>
    </div>
    <div class="col-3 col-md-2 px-md-1 px-1">
        <div class="form-group">
            Qty
            <input type="number" id="qty" class="form-control px-1" min="1" placeholder="QTY">
        </div>
    </div>
    <div class="col-3 col-md-2 pl-1">
        <div class="form-group">
            Berat
            <input type="number" id="berat" class="form-control px-1" step="0.01" min="0" placeholder="Berat">
        </div>
    </div>
    <div id="abf" style="display: none">
        <div class="pr-3">
            <div class="form-group">
                Keterangan
                <input type="text" id="label_abf" class="form-control px-1" placeholder="Keterangan">
            </div>
        </div>
    </div>
    <div id="chiller">
        <div class="row">
            <div class="col-3 col-md-4">
                <div class="form-group">
                    Keterangan
                    <input type="text" id="label" class="form-control px-1" placeholder="Keterangan">
                </div>
            </div>
            <div class="col-3 col-md-4">
                <div class="form-group">
                    Sub Item
                    <input type="text" id="sub_item" class="form-control px-1" placeholder="Sub Item">
                </div>
            </div>
            <div class="col-3 col-md-4">
                <div class="form-group">
                    Parting
                    <input type="number" id="parting" class="form-control px-1" step="0.01" min="0"
                        placeholder="Parting">
                </div>
            </div>
        </div>
    </div>

</div>

<div id="warehouse" style="display: none">
    <div class="row">
        <div class="col-md col-12">
            <div class="form-group">
                Pallete
                <select name="pallete" class="form-control select2" id="pallete" data-width="100%"
                    data-placeholder="Pilih">
                    <option value=""></option>
                    @for ($i = 1; $i <= 20; $i++) <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                </select>
            </div>
        </div>
        <div class="col-md col-12">
            <div class="form-group">
                Tujuan
                <select name="tujuan" id='tujuan' data-placeholder="Pilih Tujuan" data-width="100%"
                    class="form-control select2">
                    <option value=""></option>
                    @foreach ($warehouse as $item)
                    <option value="{{ $item->id }}">{{ $item->code }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md col-12">
            <div class="form-group">
                Sub Item
                <div class="form-group">
                    <input type="text" id="sub_item" class="form-control px-1" placeholder="Sub Item">
                </div>
            </div>
        </div>
        <div class="col-md col-12">
            <div class="form-group">
                Packaging
                <select name="packaging" id='packaging' data-placeholder="Pilih Plastik" data-width="100%"
                    class="form-control select2">
                    <option value="">- Tanpa Plastik -</option>
                    @foreach ($plastik as $item)
                    <option value="{{ $item->id }}">{{ $item->nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col pr-1">
            <div class="form-group">
                Expired Date
                <div class="radio-toolbar row">
                    <div class="col pr-1">
                        <div class="form-group">
                            <input type="radio" name="expired" value="6" class="expired" id="enam">
                            <label for="enam">6 Bulan</label>
                        </div>
                    </div>
                    <div class="col pl-1">
                        <div class="form-group">
                            <input type="radio" name="expired" value="12" class="expired" id="duabelas">
                            <label for="duabelas">12 Bulan</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col pl-1">
            <div class="form-group">
                Stock
                <div class="radio-toolbar row">
                    <div class="col pr-1">
                        <div class="form-group">
                            <input type="radio" name="stock" value="free" class="stock" id="free">
                            <label for="free">Free</label>
                        </div>
                    </div>
                    <div class="col pl-1">
                        <div class="form-group">
                            <input type="radio" name="stock" value="booking" class="stock" id="booking">
                            <label for="booking">Booking</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="form-group">
                Keterangan
                <input type="text" id="label_cs" class="form-control px-1" placeholder="Keterangan">
            </div>
        </div>
    </div>
</div>

<button class="btn btn-primary btn-block" id="input_balance">Submit</button>

<script>
    $('.select2').select2({
    theme: 'bootstrap4'
});
</script>

<script>
    $(document).ready(function() {
        $(document).on('change', '#gudang', function() {
            var selected    =   $(this).val() ;
            console.log(selected);
            if ((selected == 'cold1') || (selected == 'cold2') || (selected == 'cold3') || (selected == 'cold4')) {
                document.getElementById('warehouse').style  =   'display: block' ;
                document.getElementById('chiller').style  =   'display: none' ;
                document.getElementById('abf').style  =   'display: none' ;
            }else if((selected == 'abf')){
                document.getElementById('abf').style  =   'display: none' ;
                document.getElementById('chiller').style  =   'display: none' ;
                document.getElementById('warehouse').style  =   'display: none' ;
            } 
            else {
                document.getElementById('warehouse').style  =   'display: none' ;
                document.getElementById('chiller').style  =   'display: block' ;
                document.getElementById('abf').style  =   'display: none' ;
            }
        });
    });
</script>