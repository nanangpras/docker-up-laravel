@extends('admin.layout.template')

@section('title', 'Warehouse')

@section('footer')
{{-- <script>
    $('#warehouse-masuk').load("{{ route('warehouse.masuk') }}");
    $('#warehouse-keluar').load("{{ route('warehouse.keluar') }}");
    $('#warehouse-order').load("{{ route('warehouse.order') }}") ;
    $('#warehouse-nonlb').load("{{ route('warehouse.nonlb') }}");
    $(".loading_approve").attr('style', 'display: block') ;
    $("#approve_abf").load("{{ route('warehouse.index', ['key' => 'approve_abf']) }}", function() {
        $(".loading_approve").attr('style', 'display: none') ;
    }) ;
    $("#approve_chiller").load("{{ route('warehouse.index', ['key' => 'approve_chiller']) }}", function() {
        $(".loading_approve").attr('style', 'display: none') ;
    }) ;
</script> --}}
@endsection

@section('content')
@if ($thawing)
<div class="alert alert-danger text-center mb-3">
    {{ $thawing }} Request Thawing Pending
</div>
@endif
<div class="row my-4">
    <div class="col"></div>
    <div class="col text-center font-weight-bold">WAREHOUSE</div>
    <div class="col"></div>
    {{-- <div class="col text-right"><a href="{{ route('warehouse.showstock') }}" class="btn btn-sm btn-success">Data
            Stock</a></div> --}}
</div>


<ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
    <li class="nav-item">
        <a class="nav-link tab-link" id="custom-tabs-three-soh-tab" data-toggle="pill" href="#custom-tabs-three-soh"
            role="tab" aria-controls="custom-tabs-three-soh" aria-selected="true">
            SOH
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link tab-link" id="custom-tabs-three-rangesoh-tab" data-toggle="pill"
            href="#custom-tabs-three-rangesoh" role="tab" aria-controls="custom-tabs-three-rangesoh"
            aria-selected="true">
            RANGE TANGGAL SOH
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link tab-link" id="custom-tabs-three-stock-tab" data-toggle="pill" href="#custom-tabs-three-stock"
            role="tab" aria-controls="custom-tabs-three-stock" aria-selected="true">
            STOCK BY ITEM
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link tab-link" id="custom-tabs-three-masuk-tab" data-toggle="pill" href="#custom-tabs-three-masuk"
            role="tab" aria-controls="custom-tabs-three-masuk" aria-selected="false">
            INBOUND
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link tab-link" id="custom-tabs-three-keluar-tab" data-toggle="pill"
            href="#custom-tabs-three-keluar" role="tab" aria-controls="custom-tabs-three-keluar" aria-selected="false">
            OUTBOUND
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link tab-link" id="custom-tabs-three-order-tab" data-toggle="pill" href="#custom-tabs-three-order"
            role="tab" aria-controls="custom-tabs-three-order" aria-selected="false">
            DAFTAR ORDER
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link tab-link" id="custom-tabs-three-sameday-tab" data-toggle="pill"
            href="#custom-tabs-three-sameday" role="tab" aria-controls="custom-tabs-three-sameday"
            aria-selected="false">
            SAMEDAY
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link tab-link" id="custom-tabs-three-ordermarketing-tab" data-toggle="pill"
            href="#custom-tabs-three-ordermarketing" role="tab" aria-controls="custom-tabs-three-ordermarketing"
            aria-selected="false">
            PARKING ORDER
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link tab-link" id="custom-tabs-three-datastock-tab" data-toggle="pill"
            href="#custom-tabs-three-datastock" role="tab" aria-controls="custom-tabs-three-datastock"
            aria-selected="false">
            DATA STOCK
        </a>
    </li>
    {{-- <li class="nav-item">
        <a class="nav-link tab-link" id="custom-tabs-three-approveabf-tab" data-toggle="pill"
            href="#custom-tabs-three-approveabf" role="tab" aria-controls="custom-tabs-three-approveabf"
            aria-selected="false">
            APPROVE ABF
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link tab-link" id="custom-tabs-three-approvechiller-tab" data-toggle="pill"
            href="#custom-tabs-three-approvechiller" role="tab" aria-controls="custom-tabs-three-approvechiller"
            aria-selected="false">
            APPROVE CHILLER
        </a>
    </li> --}}
</ul>

<section class="panel">
    <div class="card-body card-tabs">
        <div class="tab-content" id="custom-tabs-three-tabContent">
            {{-- Stock Akhir --}}
            <div class="tab-pane fade" id="custom-tabs-three-soh" role="tabpanel"
                aria-labelledby="custom-tabs-three-soh-tab">

                <div class="row mb-3">
                    <div class="col">
                        @if(env('NET_SUBSIDIARY', 'CGL')=='CGL')
                        <label for="tanggal_soh">Tanggal</label>
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                            min="2023-01-01" @endif value="{{ date("Y-m-d") }}" id="tanggal_soh" class="form-control"
                            min="2023-05-27">
                        @else
                        <label for="tanggal_soh">Tanggal</label>
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                            min="2023-01-01" @endif value="{{ date("Y-m-d") }}" id="tanggal_soh" class="form-control"
                            min="2023-05-05">
                        @endif
                    </div>
                    <div class="col">
                        <label for="item_soh">Nama Item</label>
                        <select class="form-control select2" id="item_soh">
                            <option value="">- Semua Item -</option>
                            @foreach($list_item as $li)
                            <option value="{{$li->id}}">{{$li->nama}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col">
                        <label for="category_soh">Kategori</label>
                        <select class="form-control select2" id="category_soh">
                            <option value="">- Semua -</option>
                            @foreach($list_category as $lc)
                            @if($lc->nama != NULL)
                            <option value="{{$lc->id}}">{{$lc->nama}}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col">
                        <label for="marinated_soh">Status M</label>
                        <select class="form-control select2" id="marinated_soh">
                            <option value="">- Semua -</option>
                            <option value="(M)">M</option>
                            <option value="non">Non M</option>
                        </select>
                    </div>
                    <div class="col">
                        <label for="itemname_soh">Sub Item</label>
                        <select class="form-control select2" id="itemname_soh">
                            <option value="">- Semua -</option>
                            @foreach($list_itemname as $key => $lin)
                            @if($lin != NULL || $lin != "")
                            <option value="{{ $lin }}">{{ $lin }}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row mt-1">
                    <div class="col">
                        <label for="grade_soh">Grade</label>
                        <select class="form-control select2" id="grade_soh">
                            <option value="">- Semua -</option>
                            <option value="grade a">Grade A</option>
                            <option value="grade b">Grade B</option>
                        </select>
                    </div>
                    <div class="col">
                        <label for="customername_soh">Customer</label>
                        <select class="form-control select2" id="customername_soh">
                            <option value="">- Semua -</option>
                            @foreach($list_customername as $key => $cst)
                            @if($cst != NULL || $cst != "")
                            <option value="{{ $cst }}">{{ App\Models\Customer::find($cst)->nama ?? "#" }}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col">
                        <label for="ordering">Ordering</label>
                        <select class="form-control select2" id="ordering">
                            <option value="">- Urutkan Berdasar -</option>
                            <option value="customer">Nama Customer</option>
                            <option value="item">Nama Item</option>
                            <option value="qty">Quantity</option>
                            <option value="berat">Berat</option>
                        </select>
                    </div>
                    <div class="col">
                        <Label for="order_by">Sort By</Label>
                        <select class="form-control select2" id="order_by">
                            <option value="asc">ASC</option>
                            <option value="desc">DESC</option>
                        </select>
                    </div>
                    <div class="col">
                        <Label for="cari_soh">Pencarian Kata</Label>
                        <input type="text" placeholder="Cari..." id="cari_soh" class="form-control">
                    </div>
                </div>
                <br>
                <hr>
                <h5 id="refresh-soh" style="display: none" class="text-center"><i class="fa fa-refresh fa-spin"></i>
                    Loading....</h5>
                <div id="soh-stock"></div>
            </div>


            <div class="tab-pane fade" id="custom-tabs-three-rangesoh" role="tabpanel"
                aria-labelledby="custom-tabs-three-rangesoh-tab">

                <div class="row mb-3">
                    <div class="col">
                        <label for="tanggal_awalsoh">Tanggal Awal</label>
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                            min="2023-01-01" @endif value="{{ date("Y-m-d") }}" id="tanggal_awalsoh"
                            class="form-control">
                    </div>
                    <div class="col">
                        <label for="tanggal_akhirsoh">Tanggal Akhir</label>
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                            min="2023-01-01" @endif value="{{ date("Y-m-d") }}" id="tanggal_akhirsoh"
                            class="form-control">
                    </div>
                    <div class="col">
                        <label for="item_rangesoh">Nama Item</label>
                        <select class="form-control select2" id="item_rangesoh">
                            <option value="">- Semua Item -</option>
                            @foreach($list_item as $li)
                            <option value="{{$li->id}}">{{$li->nama}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col">
                        <label for="category_rangesoh">Kategori</label>
                        <select class="form-control select2" id="category_rangesoh">
                            <option value="">- Semua -</option>
                            @foreach($list_category as $lc)
                            @if($lc->nama != NULL)
                            <option value="{{$lc->id}}">{{$lc->nama}}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col">
                        <label for="marinated_rangesoh">Status M</label>
                        <select class="form-control select2" id="marinated_rangesoh">
                            <option value="">- Semua -</option>
                            <option value="(M)">M</option>
                            <option value="non">Non M</option>
                        </select>
                    </div>
                </div>
                <div class="row mt-1">
                    <div class="col">
                        <label for="itemname_rangesoh">Sub Item</label>
                        <select class="form-control select2" id="itemname_rangesoh">
                            <option value="">- Semua -</option>
                            @foreach($list_itemname as $key => $lin)
                            @if($lin != NULL || $lin != "")
                            <option value="{{ $lin }}">{{ $lin }}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col">
                        <label for="grade_rangesoh">Grade</label>
                        <select class="form-control select2" id="grade_rangesoh">
                            <option value="">- Semua -</option>
                            <option value="grade a">Grade A</option>
                            <option value="grade b">Grade B</option>
                        </select>
                    </div>
                    <div class="col">
                        <label for="customername_rangesoh">Customer</label>
                        <select class="form-control select2" id="customername_rangesoh">
                            <option value="">- Semua -</option>
                            @foreach($list_customername as $key => $cst)
                            @if($cst != NULL || $cst != "")
                            <option value="{{ $cst }}">{{ App\Models\Customer::find($cst)->nama ?? "#" }}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col">
                        <label for="ordering_rangesoh">Ordering</label>
                        <select class="form-control select2" id="ordering_rangesoh">
                            <option value="">- Urutkan Berdasar -</option>
                            <option value="customer">Nama Customer</option>
                            <option value="item">Nama Item</option>
                            <option value="qty">Quantity</option>
                            <option value="berat">Berat</option>
                        </select>
                    </div>
                    <div class="col">
                        <Label for="order_by_rangesoh">Sort By</Label>
                        <select class="form-control select2" id="order_by_rangesoh">
                            <option value="asc">ASC</option>
                            <option value="desc">DESC</option>
                        </select>
                    </div>
                </div>
                <div class="row mt-1">
                    <div class="col">
                        <Label for="cari_rangesoh">Pencarian Kata</Label>
                        <input type="text" placeholder="Cari..." id="cari_rangesoh" class="form-control">
                    </div>
                </div>
                <br>
                <hr>
                <h5 id="refresh-rangesoh" style="display: none" class="text-center"><i
                        class="fa fa-refresh fa-spin"></i> Loading....</h5>
                <div id="rangesoh-stock"></div>
            </div>
            {{-- Stock Akhir --}}
            <div class="tab-pane fade show active" id="custom-tabs-three-stock" role="tabpanel"
                aria-labelledby="custom-tabs-three-stock-tab">
                @include('admin.pages.warehouse.index.stock')
            </div>

            {{-- Pemasukan Barang --}}
            <div class="tab-pane fade tab-inout" id="custom-tabs-three-masuk" role="tabpanel"
                aria-labelledby="custom-tabs-three-masuk-tab">
                @include('admin.pages.warehouse.index.inbound')
            </div>

            {{-- Pengeluaran Barang --}}
            <div class="tab-pane fade tab-inout" id="custom-tabs-three-keluar" role="tabpanel"
                aria-labelledby="custom-tabs-three-keluar-tab">
                @include('admin.pages.warehouse.index.outbound')
            </div>

            {{-- Daftar Order --}}
            <div class="tab-pane fade" id="custom-tabs-three-order" role="tabpanel"
                aria-labelledby="custom-tabs-three-order-tab">
                @include('admin.pages.warehouse.index.order')
            </div>

            {{-- Item Order --}}
            <div class="tab-pane fade" id="custom-tabs-three-ordermarketing" role="tabpanel"
                aria-labelledby="custom-tabs-three-ordermarketing-tab">
                <section class="panel">
                    <div class="card-body">
                        <h6>Filter</h6>
                        <div class="row">
                            <div class="col pr-1">
                                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                    min="2023-01-01" @endif value="{{ date("Y-m-d", strtotime('tomorrow')) }}"
                                    id="tanggal_mulai_parking_order" class="form-control">
                            </div>
                            <div class="col pl-1">
                                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                    min="2023-01-01" @endif value="{{ date("Y-m-d", strtotime('tomorrow')) }}"
                                    id="tanggal_akhir_parking_order" class="form-control">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col pr-1">
                                <label for="filter_parking_order">Pencarian</label>
                                <input type="text" id="filter_parking_order" class="form-control" placeholder="Cari...">
                            </div>
                            <div class="col pl-1" id="customer_parking_order"></div>
                        </div>
                        <hr>
                        <h5 id="spinerparkingorder" style="display: none" class="text-center"><i
                                class="fa fa-refresh fa-spin"></i> Loading....</h5>
                        <div id="parking_orders"></div>
                    </div>
                </section>
            </div>

            {{-- Approve ABF --}}
            <div class="tab-pane fade" id="custom-tabs-three-approveabf" role="tabpanel"
                aria-labelledby="custom-tabs-three-approveabf-tab">
                <h5 class="loading_approve" style="display: none"><i class="fa fa-refresh fa-spin"></i> Loading....</h5>
                {{-- <div id="approve_abf"></div> --}}
            </div>

            {{-- Approve Chiller --}}
            <div class="tab-pane fade" id="custom-tabs-three-approvechiller" role="tabpanel"
                aria-labelledby="custom-tabs-three-approvechiller-tab">
                <h5 class="loading_approve" style="display: none"><i class="fa fa-refresh fa-spin"></i> Loading....</h5>
                {{-- <div id="approve_chiller"></div> --}}
            </div>

            {{-- SAMEDAY --}}
            <div class="tab-pane fade" id="custom-tabs-three-sameday" role="tabpanel"
                aria-labelledby="custom-tabs-three-sameday-tab">
                <h5 id="spinersameday" style="display: none" class="text-center"><i class="fa fa-refresh fa-spin"></i>
                    Loading....</h5>
                <h6><b>Prioritas Order {{ date("Y-m-d") }}</b></h6>
                <div id="sameday_view"></div>
            </div>

            {{-- Data Stock --}}
            <div class="tab-pane fade" id="custom-tabs-three-datastock" role="tabpanel"
                aria-labelledby="custom-tabs-three-datastock-tab">
                {{-- <p>data stock</p> --}}
                @include('admin.pages.warehouse.index.data-stock')
            </div>
        </div>
    </div>
</section>

@if(env('NET_SUBSIDIARY', 'EBA')=='EBA' || env('NET_SUBSIDIARY', 'EBA')=='CGL')
<script>
    $('.select2').select2({
        theme: 'bootstrap4',
    })
    var hash = window.location.hash.substr(1);
    var href = window.location.href;
    defaultPage();
    console.log(hash);
    function defaultPage() {
        if (hash == undefined || hash == "") {
            hash = "custom-tabs-three-stock";
        }

        $('#' + hash + '-tab').addClass('active').siblings().removeClass('active');
        $('#' + hash).addClass('active show').siblings().removeClass('active show');

    }

    $('.tab-link').click(function(e) {
        e.preventDefault();
        status = $(this).attr('aria-controls');
        window.location.hash = status;
        href = window.location.href;
    });

    if(hash === "custom-tabs-three-soh"){
        LoadDataSOH();
    }else
    if(hash === "custom-tabs-three-rangesoh"){
        LoadDataRangeSOH();
    }else
    if(hash === "custom-tabs-three-sameday"){
        LoadDataSameday()
    }else
    if(hash === "custom-tabs-three-ordermarketing"){
        loadParkingOrders()
    }else{
        //
    }

    //STOCK ON HAND

    var sohTimeout = null;
    var cari_soh            =   encodeURIComponent($('#cari_soh').val());
    var tanggal_soh         =   $('#tanggal_soh').val();
    var customer_soh        =   $('#customer_soh').val();
    var item_soh            =   $('#item_soh').val();
    var category_soh        =   $('#category_soh').val();
    var marinated_soh       =   $('#marinated_soh').val();
    var itemname_soh        =   $('#itemname_soh').val();
    var grade_soh           =   $('#grade_soh').val();
    var customername_soh    =   $('#customername_soh').val();
    var ordering            =   $('#ordering').val();
    var order_by            =   $('#order_by').val();
    var input_type          =   $('input[name=input_type]:checked').val();

    $("#custom-tabs-three-soh-tab").on('click', function(){
        if (sohTimeout != null) {
            clearTimeout(sohTimeout);
        }
        sohTimeout = setTimeout(function() {
            sohTimeout = null;
            //ajax code
            LoadDataSOH()
        }, 1000);
    });

    $('input[name=input_type]').on('change', function(){
        input_type = $(this).val();
        LoadDataSOH();
    })

    $("#tanggal_soh").on('change', function(){
        if (sohTimeout != null) {
            clearTimeout(sohTimeout);
        }
        sohTimeout = setTimeout(function() {
            sohTimeout = null;
            //ajax code
            LoadDataSOH()
        }, 1000);
    })
    $("#customer_soh").on('change', function(){
        if (sohTimeout != null) {
            clearTimeout(sohTimeout);
        }
        sohTimeout = setTimeout(function() {
            sohTimeout = null;
            //ajax code
            LoadDataSOH()
        }, 1000);
    })

    $("#item_soh").on('change', function(){
        if (sohTimeout != null) {
            clearTimeout(sohTimeout);
        }
        sohTimeout = setTimeout(function() {
            sohTimeout = null;
            //ajax code
            LoadDataSOH()
        }, 1000);
    })
    $("#category_soh,#marinated_soh,#itemname_soh,#grade_soh,#customername_soh").on('change', function(){
        if (sohTimeout != null) {
            clearTimeout(sohTimeout);
        }
        sohTimeout = setTimeout(function() {
            sohTimeout = null;
            //ajax code
            LoadDataSOH()
        }, 1000);
    })
    $("#ordering").on('change', function(){
        if (sohTimeout != null) {
            clearTimeout(sohTimeout);
        }
        sohTimeout = setTimeout(function() {
            sohTimeout = null;
            //ajax code
            LoadDataSOH()
        }, 1000);
    })
    $("#order_by").on('change', function(){
        if (sohTimeout != null) {
            clearTimeout(sohTimeout);
        }
        sohTimeout = setTimeout(function() {
            sohTimeout = null;
            //ajax code
            LoadDataSOH()
        }, 1000);
    })

    $("#cari_soh").on('keyup', function(){
        if (sohTimeout != null) {
            clearTimeout(sohTimeout);
        }
        sohTimeout = setTimeout(function() {
            sohTimeout = null;
            //ajax code
            LoadDataSOH()
        }, 1000);
    })

    function LoadDataSOH(){
        $('#refresh-soh').show();
        // $("#soh-stock").hide();

        cari_soh            =   encodeURIComponent($('#cari_soh').val());
        tanggal_soh         =   $('#tanggal_soh').val();

        customer_soh        =   $('#customer_soh').val();
        item_soh            =   $('#item_soh').val();
        category_soh        =   $('#category_soh').val();
        marinated_soh       =   $('#marinated_soh').val();
        itemname_soh        =   encodeURIComponent($('#itemname_soh').val());
        grade_soh           =   encodeURIComponent($('#grade_soh').val());
        customername_soh    =   $('#customername_soh').val();
        ordering            =   $('#ordering').val();
        order_by            =   $('#order_by').val();
        // input_type          =   $('input[name=input_type]:checked').val();
        // var url_soh         = "{{ url('admin/dashboard-warehouse') }}?key=soh&tanggal_gudang=" +tanggal_soh+"&cari="+cari_soh+"&customer="+customer_soh+"&item="+item_soh+"&ordering="+ordering+"&order_by="+order_by;
        var url_soh         = "{{ url('admin/dashboard-warehouse') }}?key=soh&tanggal_gudang=" +tanggal_soh+"&cari="+cari_soh+"&customer="+customer_soh+"&item="+item_soh+"&ordering="+ordering+
                                "&order_by="+order_by+"&category_soh="+category_soh+"&marinated_soh="+marinated_soh+"&itemname_soh="+itemname_soh+"&grade_soh="+grade_soh+"&customername_soh="+customername_soh;
        console.log(url_soh);

        $("#soh-stock").load(url_soh, function(){
            $('#refresh-soh').hide()
        });

    }
    
    //STOCK ON HAND RANGE TANGGAL

    var cari_rangesoh           =   encodeURIComponent($('#cari_soh').val());
    var tanggal_awalsoh         =   $('#tanggal_awalsoh').val();
    var tanggal_akhirsoh        =   $('#tanggal_akhirsoh').val();
    var customer_rangesoh       =   $('#customer_rangesoh').val();
    var item_rangesoh           =   $('#item_rangesoh').val();
    var category_rangesoh       =   $('#category_rangesoh').val();
    var marinated_rangesoh      =   $('#marinated_rangesoh').val();
    var itemname_rangesoh       =   $('#itemname_rangesoh').val();
    var grade_rangesoh          =   $('#grade_rangesoh').val();
    var customername_rangesoh   =   $('#customername_rangesoh').val();
    var ordering_rangesoh       =   $('#ordering_rangesoh').val();
    var order_by_rangesoh       =   $('#order_by_rangesoh').val();
    var input_type              =   $('input[name=input_type]:checked').val();

    $("#custom-tabs-three-rangesoh-tab").on('click', function(){
        if (sohTimeout != null) {
            clearTimeout(sohTimeout);
        }
        sohTimeout = setTimeout(function() {
            sohTimeout = null;
            //ajax code
            LoadDataRangeSOH()
        }, 1000);
    });

    // $('input[name=input_type]').on('change', function(){
    //     input_type = $(this).val();
    //     LoadDataRangeSOH();
    // })

    $("#tanggal_awalsoh,#tanggal_akhirsoh,#customer_rangesoh,#item_rangesoh,#category_rangesoh,#marinated_rangesoh,#itemname_rangesoh,#grade_rangesoh,#customername_rangesoh,#ordering_rangesoh,#order_by_rangesoh").on('change', function(){
        if (sohTimeout != null) {
            clearTimeout(sohTimeout);
        }
        sohTimeout = setTimeout(function() {
            sohTimeout = null;
            //ajax code
            LoadDataRangeSOH()
        }, 1000);
    })
    
    $("#cari_rangesoh").on('keyup', function(){
        if (sohTimeout != null) {
            clearTimeout(sohTimeout);
        }
        sohTimeout = setTimeout(function() {
            sohTimeout = null;
            //ajax code
            LoadDataRangeSOH()
        }, 1000);
    })

    function LoadDataRangeSOH(){
        $('#refresh-rangesoh').show();
        // $("#soh-stock").hide();

        cari_rangesoh          =   encodeURIComponent($('#cari_rangesoh').val());
        tanggal_awalsoh        =   $('#tanggal_awalsoh').val();
        tanggal_akhirsoh       =   $('#tanggal_akhirsoh').val();

        customer_rangesoh      =   $('#customer_rangesoh').val();
        item_rangesoh          =   $('#item_rangesoh').val();
        category_rangesoh      =   $('#category_rangesoh').val();
        marinated_rangesoh     =   $('#marinated_rangesoh').val();
        itemname_rangesoh      =   encodeURIComponent($('#itemname_rangesoh').val());
        grade_rangesoh         =   encodeURIComponent($('#grade_rangesoh').val());
        customername_rangesoh  =   $('#customername_rangesoh').val();
        ordering_rangesoh      =   $('#ordering_rangesoh').val();
        order_by_rangesoh      =   $('#order_by_rangesoh').val();
        // input_type          =   $('input[name=input_type]:checked').val();
        // var url_soh         = "{{ url('admin/dashboard-warehouse') }}?key=soh&tanggal_gudang=" +tanggal_soh+"&cari="+cari_soh+"&customer="+customer_soh+"&item="+item_soh+"&ordering="+ordering+"&order_by="+order_by;
        var url_rangesoh       = "{{ url('admin/dashboard-warehouse') }}?key=rangesoh&tanggal_awal_soh=" +tanggal_awalsoh+"&tanggal_akhir_soh="+ tanggal_akhirsoh +"&cari_rangesoh="+cari_rangesoh+"&item_rangesoh="+item_rangesoh+"&ordering_rangesoh="+ordering_rangesoh+
                                "&order_by_rangesoh="+order_by_rangesoh+"&category_rangesoh="+category_rangesoh+"&marinated_rangesoh="+marinated_rangesoh+"&itemname_rangesoh="+itemname_rangesoh+"&grade_rangesoh="+grade_rangesoh+"&customername_rangesoh="+customername_rangesoh;
        console.log(url_rangesoh);

        $("#rangesoh-stock").load(url_rangesoh, function(){
            $('#refresh-rangesoh').hide()
        });

    }

    //SAMEDAY TAB
    $("#custom-tabs-three-sameday-tab").on('click', function(){
        LoadDataSameday()
    });

    function LoadDataSameday(){
        $('#spinersameday').show();
        $("#sameday_view").hide();

        $.ajax({
            url : "{{ route('regu.index') }}",
            method: "GET",
            data :{
                'key': "sameday",
            },
            success: function(data){
                $("#sameday_view").html(data);
                $("#sameday_view").show();
                $('#spinersameday').hide()
            }
        });
    }

    //PARKING_ORDER TAB
    $("#custom-tabs-three-ordermarketing-tab").on('click', function(){
        loadParkingOrders()
    });

    $("#tanggal_mulai_parking_order,#tanggal_akhir_parking_order").on('change', function(){
        loadParkingOrders()
    })
    $("#filter_parking_order").on('keyup', function(){
        loadParkingOrders()
    })

    function customer_parking_order(){
        loadParkingOrders()
    }

    loadParkingOrders()
    function loadParkingOrders(){
        $("#parking_orders").hide();
        $("#customer_parking_order").hide();
        $('#spinerparkingorder').show()

        let tanggal_mulai_parking_order         =   $("#tanggal_mulai_parking_order").val()
        let tanggal_akhir_parking_order         =   $("#tanggal_akhir_parking_order").val()
        let filter_parking_order                =   encodeURIComponent($("#filter_parking_order").val())
        let customer_parking_order              =   $("#filter_customer_parking_order").val() ?? ''

        $.ajax({
            url : "{{ route('regu.index') }}",
            method: "GET",
            data :{
                'key'                           : "parking_orders",
                'tanggal_mulai_parking_order'   : tanggal_mulai_parking_order,
                'tanggal_akhir_parking_order'   : tanggal_akhir_parking_order,
                'filter_parking_order'          : filter_parking_order,
                'customer_parking_order'        : customer_parking_order
            },
            success: function(data){
                $("#parking_orders").html(data);
                $("#parking_orders").show();
                $('#spinerparkingorder').hide()
            }
        });

        $.ajax({
            url : "{{ route('regu.index') }}",
            method: "GET",
            data :{
                'key'                           : "customer_parking_orders",
                'tanggal_mulai_parking_order'   : tanggal_mulai_parking_order,
                'tanggal_akhir_parking_order'   : tanggal_akhir_parking_order,
                'filter_parking_order'          : filter_parking_order,
                'customer_parking_order'        : customer_parking_order
            },
            success: function(data){
                $("#customer_parking_order").html(data);
                $("#customer_parking_order").show();
                $('#spinerparkingorder').hide()
            }
        });
    }

    



</script>

@endif
@stop