@extends('admin.layout.template')

@section('title', 'Order Fulfillment')

@section('content')
    <div class="row">
        <div class="col"></div>
        <div class="col-7">
            <div class="mb-4 text-center text-uppercase">
                <b>Order Fulfillment {{ $divisi ?? '' }}</b>
            </div>
        </div>
        <div class="col text-right">
            <a href="{{ route('fulfillment.index', ['divisi' => $divisi == 'sampingan' ? '' : 'sampingan']) }}"
                class="btn btn-success">{{ $divisi == 'sampingan' ? 'Siap Kirim' : 'Sampingan' }}</a>
        </div>
    </div>
    

    <section class="panel">
        <div class="card card-primary card-outline card-tabs">
            <ul class="nav nav-tabs" id="siapkirim-tab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link tab-link active" id="tabs-siapkirim-tab" data-toggle="tab" href="#tabs-siapkirim"
                        role="tab" aria-controls="tabs-siapkirim" aria-selected="true">Siap Kirim</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link tab-link" id="tabs-orderproduksi-tab" data-toggle="tab" href="#tabs-orderproduksi"
                        role="tab" aria-controls="tabs-orderproduksi" aria-selected="true">Produksi</a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="siapkirim-tab">
                <div class="tab-pane fade show active" id="tabs-siapkirim" role="tabpanel" aria-labelledby="tabs-siapkirim-tab">
                    <div class="card-body">
                        <div class="form-group">
                            Pencarian Tanggal
                            <input type="hidden" name="customer" class="form-control" value="{{ $customer ?? '' }}" id="customer">
                            <div class="row">
                                <div class="col-md-3 col-xs-12 col-sm-12 pr-md-1 mb-2">
                                    @if (env('NET_SUBSIDIARY', 'CGL') == 'CGL')
                                        <input type="date"
                                            @if (env('NET_SUBSIDIARY', 'CGL') == 'CGL') onkeydown="return false" min="2023-01-01" @endif
                                            name="tanggal" class="form-control" value="{{ $tanggal ?? date('Y-m-d') }}" id="pencarian"
                                            placeholder="Cari...." autocomplete="off">
                                    @else
                                        <input type="date"
                                            @if (env('NET_SUBSIDIARY', 'CGL') == 'CGL') onkeydown="return false" min="2023-01-01" @endif
                                            name="tanggal" class="form-control"
                                            value="{{ $tanggal ?? date('Y-m-d', strtotime('tomorrow')) }}" id="pencarian"
                                            placeholder="Cari...." autocomplete="off">
                                    @endif
                                    <label class="mt-2 px-2 pt-2 rounded status-info"><input id="tanggalkirimfulfillment" type="checkbox"> <label for="tanggalkirimfulfillment">Pencarian Sesuai Tanggal Kirim</label></label>
                                    <br>
                                    <input id="urutan_asc" type="radio" name="urutan" value="ASC" checked> <label for="urutan_asc">Urutkan Asc</label> &nbsp
                                    <input id="urutan_desc" type="radio" name="urutan" value="DESC"> <label for="urutan_desc">Urutkan Desc</label>
                                </div>
                                <div class="col-md-3 col-xs-12 col-sm-12 pr-md-1 mb-2">
                                    @if (env('NET_SUBSIDIARY', 'CGL') == 'CGL')
                                        <input type="date"
                                            @if (env('NET_SUBSIDIARY', 'CGL') == 'CGL') onkeydown="return false" min="2023-01-01" @endif
                                            name="tanggal-end" class="form-control mb-2" value="{{ $tanggal_end ?? date('Y-m-d') }}"
                                            id="pencarian_end" placeholder="Cari...." autocomplete="off">
                                    @else
                                        <input type="date"
                                            @if (env('NET_SUBSIDIARY', 'CGL') == 'CGL') onkeydown="return false" min="2023-01-01" @endif
                                            name="tanggal-end" class="form-control mb-2"
                                            value="{{ $tanggal_end ?? date('Y-m-d', strtotime('tomorrow')) }}" id="pencarian_end"
                                            placeholder="Cari...." autocomplete="off">
                                    @endif
            
                                    @php
                                        $cat = App\Models\Customer::select('kategori')
                                            ->where('kategori', '!=', null)
                                            ->distinct()
                                            ->get();
                                    @endphp
            
                                    <select class="form-control select2" id="select-kategori" data-placeholder="Customer" name="kategori">
                                        <option value="semua" @if ($kategori == 'semua') selected @endif>- Semua Customer -
                                        </option>
                                        @foreach ($cat as $k)
                                            <option value="{{ $k->kategori }}" @if ($kategori == $k->kategori) selected @endif>
                                                {{ $k->kategori }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
            
                                <div class="col-md-3 col-xs-12 col-sm-12 pr-md-1 mb-2">
                                    <select class="form-control" id="select-jenis" name="jenis">
                                        <option value="semua" @if ($jenis == 'semua') selected @endif>- Semua Jenis -</option>
                                        <option value="fresh" @if ($jenis == 'fresh') selected @endif>Fresh</option>
                                        <option value="frozen" @if ($jenis == 'frozen') selected @endif>Frozen</option>
                                    </select>
                                </div>
                                <div class="col-md-3 col-xs-12 col-sm-12 pl-md-1 mb-2">
                                    <input type="text" name="search" class="form-control" value="{{ $search }}" id="search" placeholder="Cari...." autocomplete="off">
                                </div>
                            </div>
                        </div>
            
                        <div class="row">
                            <div class="mb-3 col pr-md-1">
                                <button type="submit" class="btn btn-outline-primary btn-block proses" data-data="">Semua</button>
                            </div>
                            <div class="mb-3 col px-md-1">
                                <button type="submit" class="btn btn-outline-success btn-block proses" data-data="selesai">Selesai</button>
                            </div>
                            <div class="mb-3 col px-md-1">
                                <button type="submit" class="btn btn-outline-info btn-block proses" data-data="proses">Pending</button>
                            </div>
                            <div class="mb-3 col px-md-1">
                                <button type="submit" class="btn btn-outline-danger btn-block proses" data-data="gagal">Gagal</button>
                            </div>
                            <div class="mb-3 col pl-md-1">
                                <button type="submit" class="btn btn-outline-warning btn-block proses" data-data="batal">Batal</button>
                            </div>
                            <div class="mb-3 col pl-md-1">
                                <button type="submit" class="btn btn-outline-default btn-block proses" data-data="partial">Partial</button>
                            </div>
                        </div>
                    </div>

                    <section class="panel">
                        <div class="card-body">
                            <div id="loading" class="text-center" style="display: none">
                                <img src="{{ asset('loading.gif') }}" width="20px">
                            </div>
                            <div id="show"></div>
                        </div>
                    </section>
                </div>

                <div class="tab-pane fade" id="tabs-orderproduksi" role="tabpanel" aria-labelledby="tabs-orderproduksi-tab">
                    <div class="row">
                        <div class="col">
                            <div class="section">
                                <div class="card-body">
                                    <div class="form-inline">
                                        <div class="form-group">
                                            <h6 class="mr-3 mt-1">Tanggal Kirim : </h6>
                                            @foreach ($nextday as $i => $date)
                                            <button type="button" name="tanggal_kirim" value="{{ $date }}" id="btn_tanggal_kirim"
                                                    class="btn btn-outline-primary mr-2 btnkirim"
                                                    style="margin-bottom: 5px;">
                                                    {{ date('d/m/y', strtotime($date)) }}
                                                </button>
                                            @endforeach
                                        </div>
                                        <div class="form-group">
                                            <input class="form-control form-control-sm cari_order" type="text" id="c_o" placeholder="cari customer atau item" style="margin-bottom: 5px;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <section class="panel">
                        <div class="card-body">
                            <div id="loading-order-produksi" class="text-center" style="display: none">
                                <img src="{{ asset('loading.gif') }}" width="20px">
                            </div>
                            <div id="data-order-produksi"></div>
                        </div>
                    </section>
                    {{-- @include('admin.pages.regu.tab_order.order-produksi') --}}

                </div>
            </div>
        </div>
        
    </section>
@stop

@section('footer')
    <script>
        $('.select2').select2({
            theme: 'bootstrap4'
        })

        var customer = $('#customer').val();
        var search = encodeURIComponent($('#search').val());
        var tanggal = "{{ $tanggal }}";
        var tanggal_end = "{{ $tanggal_end }}";
        var key = "{{ $key ?? '' }}";
        var divisi = "{{ $divisi ?? '' }}";
        var jenis = "{{ $jenis ?? '' }}";
        var kategori = "{{ $kategori ?? '' }}";
        var url = "";
        var url_page = "";
        var urutan = "ASC";

        $('#loading').show();
        filterOrder();

        $('#pencarian').on('change', function() {
            tanggal = $(this).val();
            tanggal_end = $('#pencarian_end').val();
            setTimeout(function() {
                $('#loading').show();
                filterOrder();
            }, 1000)
        })

        $('#pencarian_end').on('change', function() {
            tanggal = $(this).val();
            tanggal_end = $('#pencarian_end').val();
            setTimeout(function() {
                $('#loading').show();
                filterOrder();
            }, 1000)
        })


        $("#tanggalkirimfulfillment").on('change', function() {
            $('#loading').show();
            filterOrder();
        })

        $('#select-jenis').on('change', function() {
            jenis = $(this).val();
            $('#loading').show();
            filterOrder();
        })
        $('#select-kategori').on('change', function() {
            kategori = $(this).val();
            $('#loading').show();
            filterOrder();
        })

        $('.proses').click(function() {
            key = $(this).data('data');
            $('#loading').show();

            filterOrder();
        })

        $('#search').on('keyup', function() {
            $('#loading').show();
            key = $('.proses').data('data');
            search = encodeURIComponent($(this).val());

            filterOrder();
        })

        $("input[name='urutan']:radio").click(function() {
            if ($(this).val() == 'ASC') {
                urutan = "ASC";
            } else if ($(this).val() == 'DESC') {
                urutan = "DESC";
            }

            filterOrder();
        });

        function filterOrder() {
            let tanggalkirimfulfillment = ''

            if ($("#tanggalkirimfulfillment").is(':checked')) {
                tanggalkirimfulfillment = 1
            } else {
                tanggalkirimfulfillment = 0
            }

            tanggal = $('#pencarian').val();
            tanggal_end = $('#pencarian_end').val();

            // console.log(tanggal_end, tanggal)

            // url_page = "{{ route('fulfillment.index') }}?tanggal=" + tanggal + "&tanggal_end=" + tanggal_end +
            //     "&customer=" + customer + "&search=" + search + "&key=" + key + "&divisi=" + divisi + "&jenis=" + jenis +
            //     "&tanggalkirimfulfillment=" + tanggalkirimfulfillment + "&urutan=" + urutan + "&kategori=" +
            //     encodeURIComponent(kategori);
            url = "{{ route('fulfillment.order') }}?tanggal=" + tanggal + "&tanggal_end=" + tanggal_end + "&customer=" +
                customer + "&search=" + search + "&key=" + key + "&divisi=" + divisi + "&jenis=" + jenis +
                "&tanggalkirimfulfillment=" + tanggalkirimfulfillment + "&urutan=" + urutan + "&kategori=" +
                encodeURIComponent(kategori);

            window.history.pushState('Siap kirim', 'Siap kirim', url_page);

            $("#show").load(url, function() {
                $('#loading').hide();
            });
        }
    </script>
    <script>
        var tanggalKirim ="";
        $(".btnkirim").on('click', function () {
            tanggalKirim = $(this).val();
            loadOrderProduksi($(this));;
        });

        loadOrderProduksi();
        $(".cari_order").on('keyup', function () {
            loadOrderProduksi();
        });

        function loadOrderProduksi(button) {
            // ambil value ketika button aktif
            $(".btnkirim").removeClass('active');
            $(button).addClass('active');
            var btnTanggal = $(button).val();
            
            var text_cari = encodeURIComponent($(".cari_order").val());
            
            $.ajax({
                method: "GET",
                url: "{{route('regu.order_produksi')}}?tanggal_kirim="+tanggalKirim+"&cari_order="+text_cari,
                cache: false,
                beforeSend: function(){
                    $("#loading-order-produksi").show();
                },
                success: function (response) {
                    // console.log(btnTanggal);
                    $("#data-order-produksi").html(response);
                    $("#loading-order-produksi").hide();
                }
            });
        }
    </script>
    <script>
        var hash = window.location.hash.substr(1);
        var href = window.location.href;

        deafultPage();

        function deafultPage() {
            if (hash == undefined || hash == "") {
                hash = "tabs-siapkirim";
            }

            $('#' + hash + '-tab').addClass('active').siblings().removeClass('active');
            $('#' + hash).addClass('active show').siblings().removeClass('active show');
        }


        $('.tab-link').click(function(e) {
            e.preventDefault();
            status = $(this).attr('aria-controls');
            window.location.hash = status;
            href = window.location.href;
            // console.log(href);
        });
    </script>

@endsection
