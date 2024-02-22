@extends('admin.layout.template')

@section('title', 'Retur Authorization')

@section('content')

<div class="row mb-4">
    <div class="col"></div>
    <div class="col text-center">
        <b>RETUR</b>
    </div>
    <div class="col"></div>
</div>

<section class="panel">

    <div class="row">
        <div class="col-12 col-sm-12">
            <div class="card card-primary card-outline card-tabs">
                <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link tab-link" id="custom-tabs-three-so-tab" data-toggle="pill"
                            href="#custom-tabs-three-so" role="tab" aria-controls="custom-tabs-three-so"
                            aria-selected="true">Retur SO/DO</a>
                    </li>
                    {{-- <li class="nav-item">
                        <a class="nav-link tab-link" id="custom-tabs-three-nonso-tab" data-toggle="pill"
                            href="#custom-tabs-three-nonso" role="tab" aria-controls="custom-tabs-three-nonso"
                            aria-selected="false">Retur Non SO</a>
                    </li> --}}
                    <li class="nav-item">
                        <a class="nav-link tab-link" id="custom-tabs-three-summary-tab" data-toggle="pill"
                            href="#custom-tabs-three-summary" role="tab" aria-controls="custom-tabs-three-summary"
                            aria-selected="false">Summary</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link tab-link" id="custom-tabs-three-list-tab" data-toggle="pill"
                            href="#custom-tabs-three-list" role="tab" aria-controls="custom-tabs-three-list"
                            aria-selected="false">List Detail</a>
                    </li>
                </ul>
                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-three-tabContent">
                        <div class="tab-pane fade" id="custom-tabs-three-so" role="tabpanel"
                            aria-labelledby="custom-tabs-three-so-tab">
                            <div class="form-group">
                                <a href="{{ route('retur.nonso') }}" class="btn btn-blue mb-2">Tambah Retur Non SO</a>
                                &nbsp;
                                <a href="{{ route('retur.nonso', ['key' => 'nonnetsuite']) }}"
                                    class="btn btn-blue mb-2">Tambah Retur Non Integrasi</a> &nbsp;
                                <a href="{{ route('retur.meyer') }}" class="btn btn-outline-danger mb-2">Tambah Retur
                                    Meyer</a>
                            </div>
                            <b>Pencarian Bedasarkan Tanggal</b>
                            <form action="{{ route('retur.bycustomer') }}" method="GET" id="form-search">
                                <div class="row mt-2">
                                    <div class="col-md-4 col-sm-6 col-xs-12">
                                        <div class="form-group">
                                            Tanggal Kirim
                                            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' )
                                                min="2023-01-01" @endif id="tanggal" name="tanggal"
                                                value="{{ $tanggal ?? date('Y-m-d') }}"
                                                class="form-control form-control-sm" required>
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-sm-6 col-xs-12">
                                        <div id="loading" style="display: none"><i class="fa fa-refresh fa-spin"></i>
                                            Loading....</div>
                                        <div class="form-group" id="pilih_do">
                                            DO
                                            <select name="customer_id" class="form-control select2"
                                                id="customer_id"></select>
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-sm-6 col-xs-12">
                                        &nbsp;
                                        <button type="submit" class="btn btn-primary btn-block">Cari</button>
                                    </div>

                                </div>
                            </form>

                            <div id="list-customer-order"></div>
                        </div>
                        <div class="tab-pane fade" id="custom-tabs-three-nonso" role="tabpanel"
                            aria-labelledby="custom-tabs-three-nonso-tab">
                            <div class="table-responsive">


                            </div>
                        </div>
                        <div class="tab-pane fade" id="custom-tabs-three-list" role="tabpanel"
                            aria-labelledby="custom-tabs-three-list-tab">
                            <div class="table-responsive">
                                {{-- <form action="{{ route('qc.export') }}" method="POST"> --}}
                                    <form>
                                        @csrf
                                        <div class="row">
                                            <div class="col">
                                                <div class="form-group">
                                                    <label for="tanggalsummarylist">Filter Tanggal Retur</label>
                                                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' )
                                                        min="2023-01-01" @endif name="tanggal" class="form-control"
                                                        id="tanggalsummarylist" placeholder="Tuliskan "
                                                        value="{{ date('Y-m-d') }}" autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group">
                                                    <label for="tanggalakhirsummarylist">&nbsp;</label>
                                                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' )
                                                        min="2023-01-01" @endif name="tanggalakhir" class="form-control"
                                                        id="tanggalakhirsummarylist" placeholder="Tuliskan "
                                                        value="{{ date('Y-m-d') }}" autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group">
                                                    <label for="tujuanlist">Tujuan</label>
                                                    <select id="tujuanlist" class="form-control" name="tujuan">
                                                        <option value="">- Semua -</option>
                                                        <option value="chillerfg">Chiller FG</option>
                                                        <option value="chillerbb">Chiller BB</option>
                                                        <option value="gudang">Gudang</option>
                                                        <option value="musnahkan">Musnahkan</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group">
                                                    <label for="satuanlist">Satuan</label>
                                                    <select class="form-control" id="satuanlist" name="satuan">
                                                        <option value="">- Semua -</option>
                                                        <option value="kg">Kg</option>
                                                        <option value="ekor">Ekor/Pcs/Pack</option>
                                                        <option value="pack">Package</option>
                                                        <option value="karung">Karung</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group">
                                                    <label for="penangananlist">Penanganan</label>
                                                    <select class="form-control" id="penangananlist" name="penanganan">
                                                        <option value="">- Semua -</option>
                                                        <option value="Produksi">Reproses Produksi</option>
                                                        <option value="Sampingan">Sampingan</option>
                                                        <option value="freezer">Kembali Ke Freezer</option>
                                                        <option value="musnahkan">Musnahkan</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group">
                                                    <label for="katalist">Pencarian Kata</label>
                                                    <input type="text" name="kata" class="form-control" id="katalist"
                                                        autocomplete="off" placeholder="Pencarian" name="pencarian">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col">
                                                <button type="button"
                                                    class="btn btn-warning mb-2 export downloadRetur"><i
                                                        class="fa fa-spinner fa-spin spinerloading"
                                                        style="display:none;"></i>
                                                    <span id="text">Export</span></button>
                                            </div>
                                        </div>
                                    </form>
                                    <div id="loading-list-detail" class="text-center" style="display: none">
                                        <img src="{{ asset('loading.gif') }}" style="width: 20px"> Loading ...
                                    </div>
                                    <div id="retur-summary-list"></div>

                            </div>
                        </div>
                        <div class="tab-pane fade" id="custom-tabs-three-summary" role="tabpanel"
                            aria-labelledby="custom-tabs-three-summary-tab">
                            @csrf
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="tanggalsummary">Filter Tanggal Retur</label>
                                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) min="2023-01-01"
                                            @endif name="tanggal" class="form-control" id="tanggalsummary"
                                            value="{{ date('Y-m-d') }}" autocomplete="off">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label for="tanggalakhirsummary">&nbsp;</label>
                                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) min="2023-01-01"
                                            @endif name="tanggalakhir" class="form-control" id="tanggalakhirsummary"
                                            value="{{ date('Y-m-d') }}" autocomplete="off">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <h6>Customer</h6>
                                        <div id="customer-retur-loading" class="text-center mb-2 mt-2"
                                            style="position: absolute; left: 0; right: 0;">
                                            <img src="{{ asset('loading.gif') }}" style="width: 20px"> Loading
                                            ...
                                        </div>
                                        <div id="customer_select"></div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label for="alasan">Alasan</label>
                                        <select name="alasan" id="alasan" class="form-control select2">
                                            <option value="">Semua</option>
                                            @foreach ($alasan as $alasan)
                                            <option value="{{ $alasan->nama }}">{{ $alasan->nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label for="refresh"></label>
                                        &nbsp;
                                        <button name="refresh" id="refresh" class="btn btn-success btn-block mt-2"><span
                                                class="fa fa-refresh"></span> &nbsp Refresh Halaman Pencarian</button>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <!-- <label for=""></label> -->
                                        &nbsp;
                                        <a href="{{ route('retur.summary', ['key' => 'exportcsv']) }}" type="submit"
                                            class="btn btn-warning btn-block mt-2">Data Retur</a>
                                    </div>
                                </div>
                            </div>

                            <div id="retur-loading" class="text-center mb-2 mt-2"
                                style="position: absolute; left: 0; right: 0;">
                                <img src="{{ asset('loading.gif') }}" style="width: 20px"> Loading ...
                            </div>

                            <div id="retur-summary"></div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@stop

@section('footer')
<script>
    $('.select2').select2({
            theme: 'bootstrap4'
        });


        var hash = window.location.hash.substr(1);
        var href = window.location.href;

        var customer_id = "";
        var tanggal = "";

        function deafultPage() {
            if (hash == undefined || hash == "") {
                hash = "custom-tabs-three-so";
            }

            $('.nav-item a[href="#' + hash + '"]').addClass('active').siblings().removeClass('active');
            $('#' + hash).addClass('active show').siblings().removeClass('active show');
        }


        $('.tab-link').click(function(e) {
            e.preventDefault();
            status = $(this).attr('aria-controls');
            window.location.hash = status;
            href = window.location.href;

        });

        deafultPage();

        if (window.location.hash.substr(1) == "custom-tabs-three-so") {
            returSODO();
        } else if (window.location.hash.substr(1) == "custom-tabs-three-summary") {
            searchSummary();
            konsumen_pilih();
        } else if (window.location.hash.substr(1) == "custom-tabs-three-list") {
            searchSummaryList();
        } else {
            returSODO();
        }

        // ----------------------------------------------------------------

        $("#custom-tabs-three-so-tab").on("click", function() {
            returSODO();
        });

        $("#custom-tabs-three-summary-tab").on("click", function() {
            searchSummary();
            konsumen_pilih();
        });

        $("#custom-tabs-three-list-tab").on("click", function() {
            searchSummaryList();
        })

        // ----------------------------------------------------------------

        $('#tanggalsummarylist').change(function() {
            searchSummaryList();
        })

        $('#tanggalakhirsummarylist').change(function() {
            searchSummaryList();
        })

        $('#katalist').change(function() {
            searchSummaryList();
        })
        $('#satuanlist').change(function() {
            searchSummaryList();
        })
        $('#tujuanlist').change(function() {
            searchSummaryList();
        })
        $('#penangananlist').change(function() {
            searchSummaryList();
        })
        $('#katalist').keyup(function() {
            searchSummaryList();
        })

        function searchSummaryList() {
            $('#loading-list-detail').show();
            var tanggal = $("#tanggalsummarylist").val();
            var tanggal_akhir = $("#tanggalakhirsummarylist").val();
            var kata = encodeURIComponent($("#katalist").val());
            var tujuan = $("#tujuanlist").val();
            var satuan = $("#satuanlist").val();
            var penanganan = $("#penangananlist").val();
            // $('#loading').show();
            var url_list = "{{ url('admin/retur/summary-list?tanggal=') }}" + tanggal + "&akhir=" + tanggal_akhir +
                "&kata=" + kata + "&satuan=" + satuan + "&tujuan=" + tujuan + "&penanganan=" + penanganan;
            $('#retur-summary-list').load(url_list, function() {
                $('#loading-list-detail').hide();
            });
            console.log(penanganan);


            $(".downloadRetur").on('click', () => {
                var tanggal = $("#tanggalsummarylist").val();
                var tanggal_akhir = $("#tanggalakhirsummarylist").val();
                var kata = encodeURIComponent($("#katalist").val());
                var tujuan = $("#tujuanlist").val();
                var satuan = $("#satuanlist").val();
                var penanganan = $("#penangananlist").val();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: "{{ route('retur.summary-list') }}",
                    method: "GET",
                    data: {
                        cetak: true,
                        tanggal,
                        tanggal_akhir,
                        kata,
                        tujuan,
                        penanganan,
                        satuan,

                    },
                    beforeSend: function() {
                        $('.downloadRetur').attr('disabled');
                        $(".spinerloading").show();
                        $("#text").text('Downloading...');
                    },
                    success: function(data) {
                        window.location = "{{ url('admin/retur/summary-list?tanggal=') }}" + tanggal +
                            "&akhir=" + tanggal_akhir +
                            "&kata=" + kata + "&satuan=" + satuan + "&tujuan=" + tujuan +
                            "&penanganan=" + penanganan;
                        $(".spinerloading").hide();
                    }
                });
            })
        }
        var filterPencarianTimeout        = null;  

        function searchSummary() {
            $('#retur-summary').html('');
            var tanggal = $("#tanggalsummary").val();
            var tanggal_akhir = $("#tanggalakhirsummary").val();
            var customer = $("#customer_data").val();
            var kata = '';
            let alasan = encodeURIComponent($("#alasan").val());
            // var kata            =   $("#kata").val();
            // console.log(alasan)
            if (filterPencarianTimeout != null) {
                clearTimeout(filterPencarianTimeout);
            }


            $('#retur-loading').show();
            filterPencarianTimeout = setTimeout(function() {
                filterPencarianTimeout = null;  

                $('#retur-summary').load("{{ route('retur.summary') }}?tanggal=" + tanggal + "&akhir=" + tanggal_akhir +
                    "&kata=" + kata + "&customer=" + customer + "&alasan=" + alasan,
                    function() {
                        $('#retur-loading').hide();
                    });
                $("#customer_select").load("{{ route('retur.summary', ['key' => 'customer']) }}&tanggal=" + tanggal +
                    "&akhir=" + tanggal_akhir + "&kata=" + kata + "&alasan=" + alasan);
            }, 1000);
        }

        function konsumen_pilih() {
            var tanggal = $("#tanggalsummary").val();
            var tanggal_akhir = $("#tanggalakhirsummary").val();
            var customer = $("#customer_data").val();
            var kata = '';
            let alasan = encodeURIComponent($("#alasan").val());
            // var kata            =   $("#kata").val();
            $('#customer-retur-loading').show();
            $('#retur-summary').load("{{ route('retur.summary') }}?tanggal=" + tanggal + "&akhir=" + tanggal_akhir +
                "&kata=" + kata + "&customer=" + customer + "&alasan=" + alasan,
                function() {
                    $('#customer-retur-loading').hide();
                });
        }

        $("#refresh").on('click', function() {
            searchSummary();
        })
        $('#tanggalsummary').change(function() {
            searchSummary();
        })

        $('#tanggalakhirsummary').change(function() {
            searchSummary();
        })

        $('#customer_data').on('change', function() {
            searchSummary();
        })

        $('#alasan').change(function() {
            searchSummary();
        })



        // ---------------------------------------------------------------- Load Awal Page ------------------------ //


        function returSODO() {
            $('#form-search').on('submit', function(e) {
                e.preventDefault();

                var form = $(this); //wrap this in jQuery

                tanggal = $('#tanggal').val();
                customer_id = $('#customer_id').val();
                var length = form.length;

                loadOrder(form);
            })

            $("#tanggal").change(function() {
                $("#loading").attr('style', 'display: block');
                $("#pilih_do").attr('style', 'display: none');

                var tanggal = $(this).val();

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: "{{ route('retur.tanggal') }}",
                    method: "POST",
                    data: {
                        tanggal: tanggal
                    },
                    success: function(data) {
                        var html = '';
                        var i;
                        html = '<option value="">Pilih DO</option>';
                        for (i = 0; i < data.length; i++) {
                            if (data[i].no_do != null) {
                                html += '<option value=' + data[i].id + '>' + data[i].no_do + ' || ' +
                                    data[i].nama + '</option>';
                            }
                        }
                        $("#loading").attr('style', 'display: none');
                        $("#pilih_do").attr('style', 'display: block');
                        $('#customer_id').html(html);
                    }
                })
            })

            $("#loading").attr('style', 'display: block');
            $("#pilih_do").attr('style', 'display: none');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{ route('retur.tanggal') }}",
                method: "POST",
                data: {
                    tanggal: tanggal
                },
                success: function(data) {
                    var html = '';
                    var i;
                    html = '<option value="">Pilih DO</option>';
                    for (i = 0; i < data.length; i++) {
                        if (data[i].no_do != null) {
                            html += '<option value=' + data[i].id + '>' + data[i].no_do + ' || ' + data[i]
                                .nama + '</option>';
                        }
                    }
                    $("#loading").attr('style', 'display: none');
                    $("#pilih_do").attr('style', 'display: block');
                    $('#customer_id').html(html);
                }
            })
        }

        function loadOrder(form) {
            $('#list-customer-order').load(form.attr('action') + "?tanggal=" + tanggal + "&customer_id=" + customer_id);
        }
</script>

@endsection