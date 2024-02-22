@extends('admin.layout.template')

@section('title', 'Proses Timbang Produksi Evis')

@section('content')
<div class="row mb-4">
    <div class="col">
        <a href="{{ route('evis.index') }}" class="btn btn-outline btn-sm btn-back">
            <i class="fa fa-arrow-left"></i> Back
        </a>
    </div>
    <div class="col-7 py-1 text-center">
        <b>PROSES TIMBANG PRODUKSI EVIS</b>
    </div>
    <div class="col"></div>
</div>

<section class="panel">
    <div class="card card-primary card-outline card-tabs">
        <ul class="nav nav-tabs" id="custom-tabs-tab" role="tablist">
            <li class="nav-item">
                <a class="nav-link tab-link active" id="custom-tabs-timbang-tab" data-toggle="pill"
                    href="#custom-tabs-timbang" role="tab" aria-controls="custom-tabs-timbang"
                    aria-selected="true">Timbang</a>
            </li>
            <li class="nav-item">
                <a class="nav-link tab-link" id="custom-tabs-summary-tab" data-toggle="pill" href="#custom-tabs-summary"
                    role="tab" aria-controls="custom-tabs-summary" aria-selected="false">Summary Global</a>
            </li>
            <li class="nav-item">
                <a class="nav-link tab-link" id="custom-tabs-order-tab" data-toggle="pill" href="#custom-tabs-order"
                    role="tab" aria-controls="custom-tabs-order" aria-selected="false">Daftar Order</a>
            </li>
        </ul>

        <div class="card-body">
            <div class="tab-content" id="custom-tabs-tabContent">
                <div class="tab-pane fade active show" id="custom-tabs-timbang" role="tabpanel"
                    aria-labelledby="custom-tabs-timbang-tab">
                    <div class="row">
                        <div class="col-md-6 pr-md-1 mb-4">

                            <div class="border p-2 mb-3">
                                <div class="form-group">
                                    <div class="bg-light small">Pencarian Tanggal Bahan Baku</div>
                                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) min="2023-01-01"
                                        @endif name="tanggal" class="form-control rounded-0 px-1 py-0"
                                        value="{{ date('Y-m-d') }}" id="pencarian" placeholder="Cari...."
                                        autocomplete="off">
                                </div>

                                @if(User::setIjin(33))
                                @endif
                                <input type="radio" name="bbtype" value="evis_fg" id="evis_fg"> <label for="evis_fg">Hasil Finished
                                    Goods</label>
                                <input type="radio" name="bbtype" value="evis_karkas" id="evis_karkas"> <label
                                    for="evis_karkas">Karkas/Chiller BB</label>
                                {{-- <input type="radio" name="bbtype" value="evis_retur" id="evis_retur"> <label
                                    for="evis_retur">Retur</label> --}}
                                <input type="radio" name="bbtype" value="evis_thawing" id="evis_thawing"> <label
                                    for="evis_thawing">Thawing</label>
                                {{-- <input type="radio" name="bbtype" value="evis_abf"> <label
                                    for="evis_abf">ABF</label> --}}

                                <input type="hidden" name="freestock_id" id="freestock_id"
                                    value="{{ $freestock->id ?? '' }}">
                                <div id="loading-bahanbaku" class="text-center mb-2">
                                    <img src="{{ asset('loading.gif') }}" style="width: 30px">
                                </div>
                                <div id="list_bahan_baku"></div>
                            </div>

                            <div id="bbperuntukan"></div>
                        </div>

                        <div class="col-md-6 pl-md-1 mb-4">
                            <div class="card mb-3">
                                <div class="card-body p-2">
                                    <div class="row mb-2">
                                        <div class="col-8 pr-1">
                                            <select name="item" class="form-control select2 item" data-width="100%"
                                                data-placeholder="Pilih Produk Evis" id="item">
                                                <option value=""></option>
                                                @foreach ($item as $id => $list)
                                                <option value="{{ $list->id }}">{{ $list->sku }} - {{ $list->nama }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-2 pl-0">
                                            <input type="number" name="qty" id="qtyproduksi" autocomplete="off"
                                                class="form-control px-1" step="0.01" placeholder="Qty">
                                        </div>
                                        <div class="col-2 px-1">
                                            <input type="number" name="berat" id="beratproduksi" autocomplete="off"
                                                class="form-control px-1" step="0.01" placeholder="Berat">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        Plastik
                                        <div class="row">
                                            <div class="col-8 pr-1">
                                                <select name="item" class="form-control select2 plastik"
                                                    data-width="100%" data-placeholder="Pilih Plastik" id="plastik">
                                                    <option value="Curah">Curah</option>
                                                    @php
                                                    $plastik = \App\Models\Item::where('category_id',
                                                    '25')->where('subsidiary', env('NET_SUBSIDIARY',
                                                    'EBA'))->where('status', '1')->get();
                                                    @endphp
                                                    @foreach ($plastik as $p)
                                                    <option value="{{ $p->id }}">{{ $p->nama }} - {{$p->subsidiary}}{{ $p->netsuite_internal_id }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-4 pl-1">
                                                <input type="number" name="qtyplastik" id="qtyplastik"
                                                    class="form-control" placeholder="Qty">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-6 pr-1">
                                                Nama Customer
                                                <select name="customer" class="form-control select2" id="customer"
                                                    data-width="100%" data-placeholder="Pilih Customer">
                                                    <option value=""></option>
                                                    @foreach ($customer as $cus)
                                                    <option value="{{ $cus->id }}">{{ $cus->nama }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-6 pl-1">
                                                Keterangan
                                                <input type="text" name="sub_item" class="form-control form-control-sm"
                                                    id="keterangan" placeholder="Keterangan" autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>
                                            <input type="radio" name="tujuan_produksi" value="0" checked> Chiller Hasil
                                            Produksi
                                        </label>
                                        &nbsp; &nbsp;
                                        <label>
                                            <input type="radio" name="tujuan_produksi" value="1"> Kirim ABF
                                        </label>
                                    </div>
                                    <button type="submit"
                                        class="mt-3 btn btn-sm btn-primary btn-block tambahproduksi">Tambah</button>
                                </div>

                            </div>

                            <div id="hasil-evis">
                                <div id="hasilproduksi"></div>
                            </div>
                        </div>
                    </div>

                    <div id="selesaikan"></div>

                    <hr>

                    <h3>Hasil Peruntukan</h3>

                    <form action="{{ route('evis.hasilperuntukan') }}" method="GET">
                        <div class="row">
                            <div class="col-md-4 col-sm-4 col-xs-6">
                                <div class="form-group">
                                    <label for="tglhasil">Pencarian</label>
                                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) min="2023-01-01"
                                        @endif name="tanggal" class="form-control change-date" value="{{ $tanggal }}"
                                        id="tglhasil" placeholder="Cari...." autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </form>

                    <div id="loading-harian" class="text-center mb-2">
                        <img src="{{ asset('loading.gif') }}" style="width: 30px">
                    </div>
                    <div id="hasil_peruntukan"></div>
                </div>
                <div class="tab-pane fade" id="custom-tabs-summary" role="tabpanel"
                    aria-labelledby="custom-tabs-summary-tab">
                    <div id="list_summary"></div>
                </div>
                <div class="tab-pane fade" id="custom-tabs-order" role="tabpanel"
                    aria-labelledby="custom-tabs-order-tab">
                    <div class="form-group row">
                        <div class="col-6">
                            <label for="tanggalorder">Filter</label>
                            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                min="2023-01-01" @endif name="tanggalorder" class="form-control" id="tanggalorder"
                                placeholder="Tuliskan " value="{{ $tanggal }}" autocomplete="off">
                        </div>
                        <div class="col-6">
                            <label for="search-filter-order">Filter Nama Customer</label>
                            <input type="text" class="form-control change-filter-order" autocomplete="off"
                                id="search-filter-order" name="search" value="{{ $search ?? '' }}" placeholder="Kata">
                        </div>
                    </div>
                    <div id="list_order"></div>
                </div>
            </div>
        </div>
</section>


@stop
@section('footer')
<script>
    var url_route_sumary            =   "{{ route('produksi.summary', ['regu' => 'byproduct']) }}&tanggal={{ $tanggal }}";

        $("#list_summary").load(url_route_sumary);

        //function that display value
        function dis(val) {
            document.getElementById("result").value += val
        }

        function disberat(val) {
            document.getElementById("berat").value += val
        }

        //function that evaluates the digit and return result
        function solve() {
            let x = document.getElementById("result").value
            let y = eval(x)
            document.getElementById("result").value = y
        }

        //function that clear the display
        function clr() {
            document.getElementById("result").value = ""
        }

        function clrberat() {
            document.getElementById("berat").value = ""
        }

        function beratbersih() {
            var berat = document.getElementById("berat").value;

            if (berat != 0) {

                if ($('.keranjang:checked').val()) {
                    var total = berat - $('.keranjang:checked').val();
                } else {
                    var total = 0;
                }

            } else {
                var total = 0;
            }

            $('#jumlah').val(total);
        }
</script>

<script>
    $(document).ready(function() {
            $("#gabung").load("{{ route('evis.cartgabung') }}");
            $("#list_bahan_baku").load("{{ route('evis.cartbahanbaku') }}", function() {
                $('#loading-bahanbaku').hide();
            });
            $("#hasil_peruntukan").load("{{ route('evis.hasilperuntukan') }}", function() {
                $('#loading-harian').hide();
            });
            $("#bbperuntukan").load("{{ route('evis.bbperuntukan') }}");
            $("#hasilproduksi").load("{{ route('evis.hasilproduksi') }}");
            $("#selesaikan").load("{{ route('evis.peruntukan', ['key' => 'selesai']) }}");
            $("#list_order").load("{{ route('evis.orders') }}");
            $('#tanggalorder').on('change', function() {
                var tanggal = $(this).val();
                url_route_order = "{{ url('admin/evis/orders?tanggal=') }}" + tanggal;
                $("#list_order").load(url_route_order);
            });


            var searchFilterOrderTimeout = null;  

            $('#search-filter-order').on('keyup', function() {

                var tanggal = $('#tanggalorder').val();
                var search = $(this).val();
                if (searchFilterOrderTimeout != null) {
                    clearTimeout(searchFilterOrderTimeout);
                }
                searchFilterOrderTimeout = setTimeout(function() {
                    searchFilterOrderTimeout = null;  
                    //ajax code
                    url_route_order_produksi = "{{ url('admin/evis/orders?tanggal=') }}" +
                        tanggal + "&search=" + search;
                    $("#list_order").load(url_route_order_produksi);
                    
                }, 1000);

            })


            $('.change-date').change(function() {
                $('#loading-harian').show();
                var tanggal = $('#tglhasil').val();
                var url = "{{ route('evis.hasilperuntukan') }}" + "?tanggal=" + tanggal;
                $("#hasil_peruntukan").load(url, function() {
                    $('#loading-harian').hide();
                });
                
            });

            var searchBbTimeout = null;  

            $("input[name='bbtype']").on('change', function () {
                if (searchBbTimeout != null) {
                    clearTimeout(searchBbTimeout);
                }

                searchBbTimeout = setTimeout(function() {
                    searchBbTimeout = null;  
                    listEvisBB();
                }, 1000);  
            });

            $('#pencarian').on('change', function() {
                if (searchBbTimeout != null) {
                    clearTimeout(searchBbTimeout);
                }

                searchBbTimeout = setTimeout(function() {
                    searchBbTimeout = null;  
                    listEvisBB();
                }, 1000);  
            })

            //list bahan baku evis
            function listEvisBB() {
                $('#loading-bahanbaku').show();
                tanggal = $('#pencarian').val();
                bbtype =  $('input[name="bbtype"]:checked').val();
                $("#list_bahan_baku").load("{{ url('admin/evis/gabung/bahanbaku?tanggal=') }}" + tanggal + "&bbtype="+bbtype, function(){
                    $('#loading-bahanbaku').hide();
                });
                let cek = "{{ url('admin/evis/gabung/bahanbaku?tanggal=') }}" + tanggal + "&bbtype=" + bbtype;
            }


            $('.simpanbb').click(function() {
                var xcode = document.getElementsByClassName("xcode");
                var xitemid = document.getElementsByClassName("xitemid");
                var bberat = document.getElementsByClassName("bbberat");
                var item = document.getElementsByClassName("bbitem");
                var tanggal = $('#pencarian').val();
                var freestock_id = $('#freestock_id').val();
                var qty = [];
                var berat = [];
                var x_code = [];
                var x_item_id = [];
                for (var i = 0; i < bberat.length; ++i) {
                    x_code.push(xcode[i].value);
                    x_item_id.push(xitemid[i].value);
                    qty.push(item[i].value);
                    berat.push(bberat[i].value);
                }

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: "{{ route('evis.simpanbahanbaku') }}",
                    method: "POST",
                    data: {
                        x_code: x_code,
                        x_item_id: x_item_id,
                        berat: berat,
                        qty: qty,
                        tanggal: tanggal,
                        freestock_id: freestock_id,
                    },
                    success: function(data) {
                        showNotif('Berhasil Simpan');
                        $("#bbperuntukan").load("{{ route('evis.bbperuntukan') }}");
                        $('#pilihBB').modal('hide');
                    }
                })
            });

            $('.tambahproduksi').click(function() {
                var berat           =   $('#beratproduksi').val();
                var qty             =   $('#qtyproduksi').val();
                var item            =   $('#item').val();
                var plastik         =   $("#plastik").val();
                var qtyplastik      =   $("#qtyplastik").val();
                var freestock_id    =   $('#freestock_id').val();
                var customer        =   $('#customer').val();
                var keterangan      =   $('#keterangan').val();
                var tujuan_produksi =   $('input[name="tujuan_produksi"]:checked').val();
 
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: "{{ route('evis.evisfreestockstore') }}",
                    method: "POST",
                    data: {
                        berat           :   berat,
                        qty             :   qty,
                        item            :   item,
                        plastik         :   plastik,
                        qtyplastik      :   qtyplastik,
                        freestock_id    :   freestock_id,
                        tujuan_produksi :   tujuan_produksi,
                        customer        :   customer,
                        keterangan      :   keterangan,
                    },
                    success: function(data) {
                        if (data.status == 400) {
                            showAlert(data.msg);
                        } else {
                            showNotif('Buat free stock berhasi');
                            $("#hasilproduksi").load("{{ route('evis.hasilproduksi') }}");
                            $('#beratproduksi').val('');
                            $('#qtyproduksi').val('');
                            $('#qtyplastik').val('');
                            $('#item').val(null).trigger('change');
                            $('#plastik').val(null).trigger('change');
                        }
                    }
                })
            })

            // Tambah cart
            $('#add_cart').click(function() {
                var berat = $('#berat').val();
                var result = $('#result').val();
                var part = $('.part:checked').val();
                var peruntukan = $('.peruntukan:checked').val();
                var jumlah_keranjang = $('.keranjang:checked').val();
                var jenis = $('#jenis').val();
                var idedit = $('#idedit').val();
                var item = new Array();

                $('.purchase:checked').each(function() {
                    item.push(this.value);
                });

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "{{ route('evis.addgabung') }}",
                    method: "POST",
                    data: {
                        idedit: idedit,
                        berat: berat,
                        result: result,
                        part: part,
                        peruntukan: peruntukan,
                        jumlah_keranjang: jumlah_keranjang,
                        jenis: jenis,
                        item: item
                    },
                    success: function(data) {
                        $('#result').val('').focus();;
                        $('#jumlah').val('');
                        $('#berat').val('');
                        $('input[type="radio"]').prop('checked', false);
                        $('#custom-tabs-three-profile-tab').tab('show');
                        $('#gabung').load("{{ route('evis.cartgabung') }}");
                        $("#list_bahan_baku").load("{{ route('evis.cartbahanbaku') }}");
                    }
                });
            });

            // Edit cart
            $(document).on('click', '.edit_cart', function() {
                var row_id = $(this).data('kode');
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "{{ route('evis.gabungedit') }}",
                    method: "POST",
                    data: {
                        row_id: row_id
                    },
                    success: function(data) {
                        $('#custom-tabs-three-home-tab').tab('show');
                        $('[name="result"]').val(data.total_item);
                        $('[name="idedit"]').val(data.id);
                        $('input:radio[name=part][value=' + data.item_id + ']')[0].checked =
                            true;
                        $('input:radio[name=peruntukan][value=' + data.peruntukan + ']')[0]
                            .checked = true;
                        $('[name="jumlah"]').val(data.berat_item);
                        $('input:radio[name=jumlah_keranjang][value=' + data.keranjang + ']')[0]
                            .checked = true;
                        document.getElementById('add_cart').innerHTML = 'Ubah';

                    }
                });
            });
        });
</script>

<script>
    $('.select2').select2({
            theme: 'bootstrap4'
        })
</script>

<script>
    $(document).on('click', '#submitBB', function() {
        var xcode       =   document.getElementsByClassName("xcode") ;
        var xitemid     =   document.getElementsByClassName('xitemid') ;
        var bbberat     =   document.getElementsByClassName('bbberat') ;
        var bbitem      =   document.getElementsByClassName('bbitem') ;
        var tanggal     =   $("#pencarian").val();
        var free_stock  =   "{{ $freestock->id ?? '' }}" ;

        var x_code      =   [] ;
        var x_item_id   =   [] ;
        var berat       =   [] ;
        var qty         =   [] ;

        var hasError    = false;

        for (var i = 0; i < bbberat.length; ++i) {
            x_code.push(xcode[i].value);
            x_item_id.push(xitemid[i].value);
            berat.push(bbberat[i].value);
            qty.push(bbitem[i].value);

            var maxQty          = parseInt(bbitem[i].max);
            var maxBerat        = parseFloat(bbberat[i].max);
            var minQty          = 0;
            var minBerat        = 0;

            var enteredQty      = parseInt(bbitem[i].value);
            var enteredBerat    = parseFloat(bbberat[i].value);

            // kondisi jika nilai melebihi nilai max atau min
            if (enteredQty > maxQty || enteredBerat > maxBerat || enteredQty <= 0 || enteredBerat <= 0) {
                hasError = true;
            } 

            // kembalikan nilai hasError ketika sesuai kondisi  
            if (hasError) {
                return;
            }
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: "{{ route('evis.simpanbahanbaku') }}",
            method: "POST",
            data: {
                tanggal     :   tanggal ,
                x_code      :   x_code ,
                x_item_id   :   x_item_id ,
                berat       :   berat ,
                qty         :   qty ,
                free_stock  :   free_stock,
            },
            success: function(data) {
                if (data.status == 400) {
                    showAlert(data.msg) ;
                } else {
                    $("#freestock_id").val(data.freestock_id) ;
                    $("#list_bahan_baku").load("{{ url('admin/evis/gabung/bahanbaku?tanggal=') }}" + tanggal + "&produksi=" + free_stock);
                    $("#bbperuntukan").load("{{ route('evis.bbperuntukan') }}");
                    $("#selesaikan").load("{{ route('evis.peruntukan', ['key' => 'selesai']) }}");
                    showNotif('Tambah bahan baku berhasil') ;
                }
            },
        });
    });

</script>

@endsection