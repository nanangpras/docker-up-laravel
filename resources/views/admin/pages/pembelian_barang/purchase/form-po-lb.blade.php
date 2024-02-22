<section class="">
    <form method="POST" enctype="multipart/form-data" action="{{route('pembelian.polb')}}">
        @csrf
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label for="supplier">Supplier</label>
                    <select id="supplier" name="supplier" class="form-control select2" data-placeholder="Pilih Supplier"
                        data-width="100%" required>
                        <option value=""></option>
                        @foreach ($supplier as $row)
                        <option value="{{ $row->id }}">{{ $row->kode }} - {{ $row->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="row">
                    <div class="col-md-6 col-xs-12">
                        <div class="form-group">
                            <label for="type_po">Type PO</label>
                            <select id="type_po" class="form-control" name="type_po" data-placeholder="Pilih Form"
                                data-width="100%" required>
                                <option value="PO LB">PO LB</option>
                                <option value="PO Transit">PO Transit</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 col-xs-12">
                        <div class="form-group">
                            <label for="form_id">Form PO</label>
                            <select id="form_id" class="form-control" name="form_id" data-placeholder="Pilih Form"
                                data-width="100%" required readonly>
                                <option value="{{ Session::get('subsidiary') == 'CGL' ? '131' : '156' }}">{{ Session::get('subsidiary') }} - Form Purchase Order Ayam</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-xs-12">
                        <div class="form-group">
                            <label for="tanggal">Tanggal PO</label>
                            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                min="2023-01-01" @endif id="tanggal" name="tanggal" class="form-control"
                                value="{{date('Y-m-d')}}" min="{{date('Y-m-d')}}" required>
                        </div>
                    </div>
                    <div class="col-md-6 col-xs-12">
                        <div class="form-group">
                            <label for="tanggal_kirim">Tanggal Kirim</label>
                            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                min="2023-01-01" @endif id="tanggal_kirim" name="tanggal_kirim" class="form-control"
                                value="{{date('Y-m-d', strtotime('+1 day'))}}" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label for="jenis_ekspedisi">Jenis Ekspedisi</label>
                            <select id="jenis_ekspedisi" class="form-control" name="jenis_ekspedisi"
                                data-placeholder="Pilih Form" data-width="100%" required>
                                <option value="Kirim">Kirim</option>
                                <option value="Tangkap">Tangkap</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label for="link_url">Link File</label>
                            <textarea id="link_url" type="text" class="form-control" name="url_link" value=""
                                placeholder="https://drive.google.com/diasuhdkahs991823ku2hiuh/123i123hu98/1293"></textarea>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label for="memo">Memo</label>
                            <textarea id="keterangan" rows="2" name="memo" placeholder="Tuliskan Memo (opsional)"
                                class="form-control"></textarea>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col">
                <div id="history_po_lb">
                    {{-- Load PO LB --}}
                </div>
                <div class="row mt-2">
                    <div class="col col-xs-12">
                        <div class="form-group">
                        <label for="items">Item</label>
                        <select name="item" class="form-control select2" data-width="100%" data-placeholder="Pilih Item" id="items">
                            <option value=""></option>
                            @foreach ($items as $item)
                                <option value="{{ $item->id }}">{{ $item->nama }}</option>
                            @endforeach
                        </select>
                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="col-md-6 col-xs-12">
                        <div class="form-group">
                            <label for="hargaPOLB">Harga</label>
                            <div class="input-group">
                                <input type="text" id="hargaPOLB" class="form-control rounded-0 p-1 input-amount"
                                    autocomplete="off" placeholder="Harga" value="" name="harga" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xs-12">
                        <div class="form-group">
                            <label for="uc">Harga Cetakan</label>
                            <div class="input-group">
                                <select class="form-control" name="unit_cetakan" id="uc">
                                    <option value="1" selected> Kg </option>
                                    <option value="2"> Ekor/Pcs/Pack </option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-6 col-xs-12">
                        <div class="form-group">
                            <label for="jumlah_do">Jumlah DO</label>
                            <div class="input-group">
                                <input type="number" id="jumlah_do" class="form-control rounded-0 p-1"
                                    autocomplete="off" min="1" placeholder="DO" value="" name="jumlah_do" required>
                                <div class="input-group-prepend">
                                    <div class="input-group-text">Rit/Mobil</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xs-12">
                        <div class="form-group">
                            <label for="ukuran_ayam">Ukuran Ayam</label>
                            <div class="input-group">
                                <select class="form-control" id="ukuran_ayam" name="ukuran_ayam">
                                    <option value="1">
                                        < 1.1 </option>
                                    <option value="2"> 1.1-1.3 </option>
                                    <option value="3"> 1.2-1.4 </option>
                                    <option value="4"> 1.3-1.5 </option>
                                    <option value="5" selected> 1.4-1.6 </option>
                                    <option value="6"> 1.5-1.7 </option>
                                    <option value="7"> 1.6-1.8 </option>
                                    <option value="8"> 1.7-1.9 </option>
                                    <option value="9"> 1.8-2.0 </option>
                                    <option value="10"> 1.9-2.1 </option>
                                    {{-- <option value="11"> 2.0-2.2 </option> --}}
                                    {{-- <option value="12"> 2.2 Up</option> --}}
                                    {{-- <option value="14"> 1.2-1.5</option> --}}
                                    <option value="15"> 1.3-1.6</option>
                                    <option value="16"> 1.4-1.7</option>
                                    <option value="17"> 1.5-1.8</option>
                                    {{-- <option value="18"> 1.6-1.9</option> --}}
                                    {{-- TAMBAHAN BARU --}}
                                    <option value="18"> 2.0-2.5</option>
                                    <option value="19"> 2.5-3.0</option>
                                    <option value="20"> 3.0 Up</option>
                                    <option value="21"> 4.0 Up</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-6 col-xs-12">
                        <div class="form-group">
                            <label for="ekor">Ekor DO</label>
                            <div class="input-group">
                                <input type="number" id="ekor" class="form-control rounded-0 p-1" autocomplete="off"
                                    min="1" placeholder="Tulis Ekor" value="" name="qty" required>
                                <div class="input-group-prepend">
                                    <div class="input-group-text">Ekor/Pcs/Pack</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xs-12">
                        <div class="form-group">
                            <label for="beratdo">Berat DO</label>
                            <div class="input-group">
                                <input type="number" id="beratdo" class="form-control rounded-0 p-1" autocomplete="off"
                                    min="1" placeholder="Tulis Berat" value="" name="berat" required>
                                <div class="input-group-prepend">
                                    <div class="input-group-text">Kg</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <button type="submit" class="btn btn-green btn-block"
            onclick="return confirm('Submit PO? pastikan data sudah benar')">Submit PO</button>
    </form>
</section>


<script>
    var jumlah_do   = 0;
    var ukuran_ayam = 5;
    var ekor_do     = 0;
    var berat_do    = 0;
    $('#jumlah_do').on('keyup', function(){
        jumlah_do = $(this).val();
        recalculateDO()
    })
    $('#ukuran_ayam').on('change', function(){
        ukuran_ayam = $(this).val();
        recalculateDO()
    })

    function recalculateDO(){

        berat_do    = 0;
        ekor_do     = 0;

        // <option value="1"> < 1.1 </option>
        // <option value="2"> 1.1-1.3 </option>
        // <option value="3"> 1.2-1.4 </option>
        // <option value="4"> 1.3-1.5 </option>
        // <option value="5" selected> 1.4-1.6 </option>
        // <option value="6"> 1.5-1.7 </option>
        // <option value="7"> 1.6-1.8 </option>
        // <option value="8"> 1.7-1.9 </option>
        // <option value="9"> 1.8-2.0 </option>
        // <option value="10"> 1.9-2.1 </option>
        // <option value="11"> 2.0-2.2 </option>
        // <option value="12"> 2.2 Up</option>
        // <option value="13"> 1.2-1.5</option>
        // <option value="14"> 1.3-1.6</option>
        // <option value="15"> 1.5-1.8</option>
        // Math.round((jumlah_do*ekor_do*2.05))

        if(ukuran_ayam=="1"){
            ekor_do  = 2400;
            berat_do = 3500;
        }
        if(ukuran_ayam=="2" || ukuran_ayam=="3" || ukuran_ayam=="4" || ukuran_ayam=="13" || ukuran_ayam=="14"){
            ekor_do  = 2300;
            berat_do = 3500;
        }
        if(ukuran_ayam=="5" || ukuran_ayam=="6" || ukuran_ayam=="16" || ukuran_ayam=="15" || ukuran_ayam=="17"){
            ekor_do  = 2200;
            berat_do = 4000;
        }
        if(ukuran_ayam=="7" || ukuran_ayam=="8"){
            ekor_do  = 2000;
            berat_do = 4000;
        }
        if(ukuran_ayam=="10"){
            ekor_do  = 2000;
            berat_do = 4000;
        }
        if(ukuran_ayam=="9" || ukuran_ayam=="11" || ukuran_ayam=="12"){
            ekor_do  = 1700;
            berat_do = 4000;
        }

        $('#ekor').val(jumlah_do*ekor_do);
        $('#beratdo').val(jumlah_do*berat_do);

    }

    function inputRupiahPOLB() {
        $('#hargaPOLB').val(formatAmount($('#hargaPOLB').val()));
    }
</script>