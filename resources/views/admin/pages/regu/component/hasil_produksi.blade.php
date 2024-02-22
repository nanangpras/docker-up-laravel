@if ($request->kat == 'boneless')
<div class="form-group border-top pt-3 radio-toolbar">
    <div class="mb-1">Pilih Jenis Boneless</div>
    <div class="row">
        <div class="col-4 pr-1">
            <div class="form-group">
                <input type="radio" name="tipe" value="broiler" class="tipe" id="broiler">
                <label for="broiler">Broiler</label>
            </div>
        </div>
        <div class="col-4 px-1">
            <div class="form-group">
                <input type="radio" name="tipe" value="pejantan" class="tipe" id="pejantan">
                <label for="pejantan">Pejantan</label>
            </div>
        </div>
        <div class="col-4 pl-1">
            <div class="form-group">
                <input type="radio" name="tipe" value="kampung" class="tipe" id="kampung">
                <label for="kampung">Kampung</label>

            </div>
        </div>
        <div class="col-4 pr-1">
            <div class="form-group">
                <input type="radio" name="tipe" value="parent" class="tipe" id="parent">
                <label for="parent">Parent</label>
            </div>
        </div>
    </div>
</div>

<div id="jenisayam"></div>

<script>
    $(document).on('change', '.tipe', function() {
            var jen = $('.tipe:checked').val();

            if (jen == 'broiler') {
                $('#jenisayam').load("{{ route('regu.index', ['key' => 'item_boneless']) }}&tipe=broiler");
            } else if (jen == 'pejantan') {
                $('#jenisayam').load("{{ route('regu.index', ['key' => 'item_boneless']) }}&tipe=pejantan");
            } else if (jen == 'kampung') {
                $('#jenisayam').load("{{ route('regu.index', ['key' => 'item_boneless']) }}&tipe=kampung");
            } else if (jen == 'parent') {
                $('#jenisayam').load("{{ route('regu.index', ['key' => 'item_boneless']) }}&tipe=parent");
            }
        });
</script>
@else
<div class="row mb-3">
    <div class="col-8 pr-1">
        Item
        <select name="itemfree" class="form-control select2" data-width="100%" data-placeholder="Pilih Item" id="itemfree">
            <option value=""></option>
            @foreach (Item::daftar_sku_update($request->kat) as $list)
                <option value="{{ $list->id }}">{{ $list->nama }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-2 px-1">
        Qty
        <input type="number" name="jumlah" id="jumlah" class="form-control form-control-sm p-1" placeholder="Qty" autocomplete="off">
    </div>
    <div class="col-2 pl-0">
        Berat
        <input type="number" name="berat" id="berat" class="form-control form-control-sm p-1" step="0.01" placeholder="Berat" autocomplete="off">
    </div>
</div>
@endif


<div class="row">
    @if ($request->kat == 'parting' or $request->kat == 'marinasi')
    <div class="col-4 pr-0">
        <div class="form-group">
            Jumlah Parting
            <input type="number" name="part" class="form-control form-control-sm p-1" id="part" placeholder="Parting" autocomplete="off">
        </div>
    </div>
    @endif
    <div class="{{ ($request->kat == 'parting' or $request->kat == 'marinasi') ? 'col-8' : 'col' }}">
        <div class="row">
            <div class="col-9 pr-1">
                <div class="form-group">
                    Plastik
                    <select name="plastik" id='plastik' class="form-control select2" data-width="100%" data-placeholder="Pilih Plastik">
                        <option value=""></option>
                        <option value="Curah">Curah</option>
                        <!-- @php
                        $plastik = \App\Models\Item::where('category_id', '25')->where('subsidiary', env('NET_SUBSIDIARY', 'EBA'))->where('status', '1')->get();
                        @endphp -->
                        @foreach ($jenis_plastik as $p)
                        <option value="{{ $p->id }}">{{ $p->nama }} - {{$p->subsidiary}}{{ $p->netsuite_internal_id }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-3 pl-1">
                &nbsp;
                <input type="number" name="jumlah_plastik" id="jumlah_plastik" class="form-control form-control-sm" placeholder="Jumlah">
            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-6 pr-1">
        <div class="form-group">
            Nama Customer
            <select name="customer" class="form-control select2" id="customer" data-width="100%" data-placeholder="Pilih Customer">
                <option value=""></option>
                @foreach ($customer as $cus)
                <option value="{{ $cus->id }}" data-customer="{{ $cus->id }}">{{ $cus->nama }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-6 pl-1">
        <div class="form-group">
            Nama Bumbu / Keterangan
            <input type="text" name="sub_item" class="form-control form-control-sm" id="sub_item" placeholder="Keterangan" autocomplete="off">
        </div>
    </div>
</div>

<div class="row" id="bumbu-el">
    <div class="col pr-1">
        <div class="form-group">
            Bumbu
            <select name="bumbu_id" class="form-control form-control-sm select2" id="bumbu_id" data-width="100%" data-placeholder="Pilih Bumbu">
                <option value=""></option>
            </select>
        </div>
    </div>
    <div class="col pr-1">
        <div class="form-group">
            Bumbu Berat
            <input type="number" name="bumbu_berat" class="form-control form-control-sm" id="bumbu_berat" placeholder="Berat Bumbu" autocomplete="off">
        </div>
    </div>
</div>

<div class="row">
    <div class="col-4 pr-1">
        <div class="form-group">
            Unit
            <input type="text" id="unit" placeholder="Tuliskan Unit" value="" class="form-control">
        </div>
    </div>
    <div class="col-4 px-1">
        <div class="form-group">
            Jumlah Keranjang
            <input type="number" min="0" id="jumlah_keranjang" placeholder="Tuliskan Jumlah Keranjang" value="" class="form-control">
        </div>
    </div>
    <div class="col-4 pl-1">
        <div class="form-group">
            Kode Produksi
            <input type="text" id="kode_produksi" placeholder="Tuliskan Kode Produksi" class="form-control">
        </div>
    </div>
</div>

<div id="daftar_order"></div>
@if ((Auth::user()->account_role == 'superadmin') OR (env('NET_SUBSIDIARY', 'EBA') == 'EBA'))
<ol class="switches table-success p-1">
    <li>
        <input type="checkbox" name="selonjor" class="additional" id="selonjor">
        <label for="selonjor">
            <span class="text-success pl-2 font-weight-bold">Ayam Selonjor</span>
            <span></span>
        </label>
    </li>
</ol>
@endif

@if ($request->kat == 'whole' || $request->kat == 'parting' || $request->kat == 'marinasi' || $request->kat == 'frozen')
<div class="form-group">
    <ol class="switches">
        <li>
            <input type="checkbox" name="additional" class="additional" value="tunggir" id="tunggir">
            <label for="tunggir">
                <span>Tanpa Tunggir</span>
                <span></span>
            </label>
        </li>

    </ol>
    <div class="row mb-3 form-tunggir" style="display: none">
        <div class="col-8 pr-1">
            <select name="itemtunggir" class="form-control select2" data-width="100%" data-placeholder="Pilih Item" id="itemtunggir">
                @foreach (Item::whereIn('sku', ['1211700013', '1212700013', '1213700013', '1214700013', '1221700013',
                '1222700013', '1223700013', '1224700013', '1215700013', '1216700013', '1225700013'])->get() as $id =>
                $list)
                <option value="{{ $list->id }}">{{ $list->nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-2 px-1">
            <input type="hidden" name="jumlahtunggir" id="jumlahtunggir" class="form-control" placeholder="Qty" autocomplete="off">
            <input type="number" name="berattunggir" id="berattunggir" class="form-control form-control-sm" step="0.01" placeholder="Berat" autocomplete="off">
        </div>
    </div>

    <ol class="switches">
        <li>
            <input type="checkbox" name="additional" class="additional" value="lemak" id="lemak">
            <label for="lemak">
                <span>Tanpa Lemak</span>
                <span></span>
            </label>
        </li>
    </ol>

    <div class="row mb-3 form-lemak" style="display: none">
        <div class="col-8 pr-1">
            <select name="itemlemak" class="form-control select2" data-width="100%" data-placeholder="Pilih Item"
                id="itemlemak">
                @foreach (Item::whereIn('sku', [1211700003, 1211700006, 1211700007, 1212700003, 1212700006, 1212700007,
                1213700003, 1213700006, 1213700007, 1214700003, 1214700006, 1214700007, 1221700003, 1221700006,
                1221700007, 1222700003, 1222700006, 1222700007, 1223700003, 1223700006, 1223700007, 1224700003,
                1224700006, 1224700007, 1215700003, 1215700006, 1215700007, 1216700003, 1216700006, 1216700007,
                1225700003, 1225700006, 1225700007])->get() as $id => $list)
                <option value="{{ $list->id }}">{{ $list->nama }}</option>
                @endforeach
            </select>
        </div>
        {{-- <div class="col-2 pl-1">
        </div> --}}
        <div class="col-2 px-1">
            <input type="hidden" name="jumlahlemak" id="jumlahlemak" class="form-control" placeholder="Qty" autocomplete="off">
            <input type="number" name="beratlemak" id="beratlemak" class="form-control form-control-sm" step="0.01" placeholder="Berat" autocomplete="off">
        </div>
    </div>

    <ol class="switches">
        <li>
            <input type="checkbox" name="additional" class="additional" value="maras" id="maras">
            <label for="maras">
                <span>Tanpa Maras</span>
                <span></span>
            </label>
        </li>
    </ol>

    <div class="row mb-3 form-maras" style="display: none">
        <div class="col-8 pr-1">
            <select name="itemmaras" class="form-control select2" data-width="100%" data-placeholder="Pilih Item" id="itemmaras">
                @foreach (Item::whereIn('sku', [1211820001, 1212820001, 1213820001, 1214820001, 1221820001, 1222820001, 1223820001, 1224820001, 1215820001, 1216820001, 1225820001])->get() as $id => $list)
                <option value="{{ $list->id }}">{{ $list->nama }}</option>
                @endforeach
            </select>
        </div>
        <input type="hidden" name="jumlahmaras" id="jumlahmaras" class="form-control" placeholder="Qty"
            autocomplete="off">
        <div class="col-2 px-1">
            <input type="number" name="beratmaras" id="beratmaras" class="form-control form-control-sm" step="0.01" placeholder="Berat" autocomplete="off">
        </div>
    </div>


    <script>
         $(document).ready(function () {
            $('#customer').on('change', function () {
                var selectedCustomerId = $(this).val();
                var bumbuDropdown = $('#bumbu_id');
                bumbuDropdown.empty();

                if (selectedCustomerId) {
                    var customerData = $('#customer option:selected').data('customer');
                    if (customerData) {
                        $.ajax({
                            url: 'get-bumbu/' + customerData,
                            method: 'GET',
                            success: function (bumbu) {
                                $.each(bumbu, function (key, value) {
                                    bumbuDropdown.append($('<option>', {
                                        value: value.id,
                                        text: value.nama + ' - (' + (value.berat !== null ? value.berat + ' Kg' : '0 Kg') + ')'
                                    }));
                                });
                            }
                        });
                    }
                }

                bumbuDropdown.select2(); // Inisialisasi ulang dropdown bumbu (jika Anda menggunakan select2)
            });
        });

        $('#tunggir').on('change', function() {
                if ($(this).is(':checked')) {
                    console.log('tunggir check');
                    $('.form-tunggir').show();
                } else {
                    console.log('tunggir uncheck');
                    $('.form-tunggir').hide();
                }
            })
            $('#lemak').on('change', function() {
                if ($(this).is(':checked')) {
                    console.log('lemak check');
                    $('.form-lemak').show();
                } else {
                    console.log('lemak uncheck');
                    $('.form-lemak').hide();
                }
            })
            $('#maras').on('change', function() {
                if ($(this).is(':checked')) {
                    console.log('maras check');
                    $('.form-maras').show();
                } else {
                    console.log('maras uncheck');
                    $('.form-maras').hide();
                }
            })
    </script>

</div>
@endif


@if ($request->kat != 'frozen')
<div class="form-group">
    <label>
        <input type="radio" name="tujuan_produksi" value="0" checked> Chiller Hasil Produksi
    </label>
    &nbsp; &nbsp;
    <label>
        <input type="radio" name="tujuan_produksi" value="1"> Kirim ABF
    </label>
    &nbsp; &nbsp;
    @if(env('NET_SUBSIDIARY', 'CGL')=='CGL')
    <label>
        <input type="radio" name="tujuan_produksi" value="2"> Kirim Expedisi
    </label>
    @endif
    &nbsp; &nbsp;
    <label>
        <input type="radio" name="tujuan_produksi" value="3"> Titipan CS
    </label>
</div>
@endif


<div class="form-group">
    <button class="btn input_freestock btn-primary btn-block btnHiden" data-dismiss="modal" aria-label="Close">Tambah</button>
</div>

<script>
    $('.select2').select2({
        theme: 'bootstrap4',
    })
</script>

<style>
    .select2 {
        height: 30px !important
    }

    .select2-container--bootstrap4 .select2-selection--single {
        height: calc(2rem + 2px) !important;
    }

    .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
        line-height: 2rem;
    }

    .select2-container--bootstrap4 .select2-selection--single .select2-selection__placeholder {
        line-height: 0;
    }
</style>

<script>
    $("#customer").on('change', function(){
        var customer    =   $(this).val() ;
        var item        =   $("#itemfree").val() ;
        $("#daftar_order").load("{{ route('regu.index', ['key' => 'daftar_order']) }}&cust=" + customer + "&item=" + item);
    })

    $("#itemfree").on('change', function(){
        var item        =   $(this).val() ;
        var customer    =   $("#customer").val() ;
        $("#daftar_order").load("{{ route('regu.index', ['key' => 'daftar_order']) }}&cust=" + customer + "&item=" + item);
    })

    $(document).ready(function() {
        // Mendapatkan URL saat ini
        var currentURL = window.location.href;

        // Membuat objek URLSearchParams dari URL
        let params = new URLSearchParams(document.location.search);

        // Mengambil nilai parameter 'kategori' dari URL
        let kategori = params.get('kategori');

        if(kategori == 'marinasi')
        {
            $('#bumbu-el').show();
        }else 
        {
            $('#bumbu-el').hide();
        }
    });
</script>
