@extends('admin.layout.template')

@section('title', 'Thawing')

@section('content')
<div class="text-center my-4">
    <b>THAWING</b>
</div>
@if ($thawing)
<div class="alert alert-danger text-center mb-3">
    {{ $thawing }} Request Thawing Pending
</div>
@endif

<section class="panel">
    <div class="card-body">
        Pencarian Tanggal
        <div class="row">
            <div class="col pr-1">
                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                    @endif class="form-control" id="tanggal_mulai" value="{{ date("Y-m-d") }}">
            </div>
            <div class="col pl-1">
                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                    @endif class="form-control" id="tanggal_akhir" value="{{ date("Y-m-d") }}">
            </div>

            <button class="btn btn-primary" id="download-item">
                <i class="fa fa-download"></i> Download Excel
            </button>
        </div>
    </div>
</section>

<section class="panel">
    <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
        <li class="nav-item">
            <a class="nav-link tab-link" id="custom-tabs-three-requestthawing-tab" data-toggle="pill"
                href="#custom-tabs-three-requestthawing" role="tab" aria-controls="custom-tabs-three-requestthawing"
                aria-selected="false">
                Thawing Request
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link tab-link" id="custom-tabs-three-thawing-tab" data-toggle="pill"
                href="#custom-tabs-three-thawing" role="tab" aria-controls="custom-tabs-three-thawing"
                aria-selected="false">
                Summary Thawing Request
            </a>
        </li>
        {{-- <li class="nav-item">
            <a class="nav-link tab-link" id="custom-tabs-three-thawingfg-tab" data-toggle="pill"
                href="#custom-tabs-three-thawingfg" role="tab" aria-controls="custom-tabs-three-thawingfg"
                aria-selected="false">
                Thawing Langsung tanpa Request
            </a>
        </li> --}}
        <li class="nav-item">
            <a class="nav-link tab-link" id="custom-tabs-three-stockgudang-tab" data-toggle="pill"
                href="#custom-tabs-three-stockgudang" role="tab" aria-controls="custom-tabs-three-stockgudang"
                aria-selected="false">
                Stock By Item
            </a>
        </li>
    </ul>

    <div class="card-body">
        <div class="tab-content" id="custom-tabs-three-tabContent">
            <div class="tab-pane fade show active" id="custom-tabs-three-requestthawing" role="tabpanel"
                aria-labelledby="custom-tabs-three-requestthawing-tab">
                <div id="spinerrequestthawing" class="text-center" style="display: block">
                    <img src="{{ asset('loading.gif') }}" width="30px">
                </div>
                <div id="warehouse-requestthawing"></div>
            </div>
            <div class="tab-pane fade" id="custom-tabs-three-thawing" role="tabpanel"
                aria-labelledby="custom-tabs-three-thawing-tab">
                <div id="spinersummarythawing" class="text-center" style="display: block">
                    <img src="{{ asset('loading.gif') }}" width="30px">
                </div>
                <div id="warehouse-thawing"></div>
            </div>
            <div class="tab-pane fade" id="custom-tabs-three-thawingfg" role="tabpanel"
                aria-labelledby="custom-tabs-three-thawingfg-tab">
                <div id="spinerthawingfg" class="text-center" style="display: block">
                    <img src="{{ asset('loading.gif') }}" width="30px">
                </div>
                <div id="warehouse-thawingfg"></div>
            </div>
            <div class="tab-pane fade" id="custom-tabs-three-stockgudang" role="tabpanel"
                aria-labelledby="custom-tabs-three-stockgudang-tab">
                <div id="spinerwarehousestock" class="text-center" style="display: block">
                    <img src="{{ asset('loading.gif') }}" width="30px">
                </div>
                <div id="warehouse-stock"></div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('footer')
    <script>
        var hash = window.location.hash.substr(1);
        var href = window.location.href;

        defaultPage();

        function defaultPage() {
            if (hash == undefined || hash == "") {
                hash = "custom-tabs-three-requestthawing";
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

        $("#tanggal_mulai,#tanggal_akhir,#lokasi_gudang,#filter_stock").on('change', function(){
            var mulai   =   $("#tanggal_mulai").val();
            var akhir   =   $("#tanggal_akhir").val();
            var lokasi  =   $("#lokasi_gudang").val();
            var filter  =   encodeURIComponent($("#filter_stock").val());
            var subitem =   encodeURIComponent($("#sub_item").val());
            var hash    =   window.location.hash.substr(1);
            
            if(hash === 'custom-tabs-three-requestthawing'){
                loadRequestThawing();
            }else if(hash === 'custom-tabs-three-thawing'){
                loadSummaryThawing();
            }else if(hash === 'custom-tabs-three-thawingfg'){
                loadThawingFg();
            }else if(hash === 'custom-tabs-three-stockgudang'){
                loadThawingStock();
            }else{
                loadRequestThawing();
            }
        });

        // TAB REQUEST THAWING
        if(hash === "custom-tabs-three-requestthawing"){
            loadRequestThawing()
        }
        $("#custom-tabs-three-requestthawing-tab").on('click', function(){
            loadRequestThawing()
        });
       
        // loadRequestThawing()
        function loadRequestThawing(){
            $("#spinerrequestthawing").show();
            var mulai   =   $("#tanggal_mulai").val();
            var akhir   =   $("#tanggal_akhir").val();
            $('#warehouse-requestthawing').load("{{ route('warehouse.requestthawing') }}?mulai=" + mulai + "&akhir=" + akhir, function(){            
                $("#spinerrequestthawing").hide();
            });
            
        }
        // END OF TAB REQUEST THAWING

        $(document).ready(function() {
                // Dapatkan nilai ID dari query parameter URL
                var urlParams = new URLSearchParams(window.location.search);
                var id = urlParams.get('id');

                // Jika ID ada, lakukan pemrosesan
                if (id !== null) {
                    loadSummaryDataById(id);
                }
        });

        // TAB SUMMARY THAWING
        if(hash === "custom-tabs-three-thawing"){
                if (id !== null) {
                    loadSummaryDataById(id);
                } 
                loadSummaryThawing()
        }
        $("#custom-tabs-three-thawing-tab").on('click', function(){
                loadSummaryThawing()
        });
        // loadSummaryThawing()
        function loadSummaryThawing(){
            $("#spinersummarythawing").show();
            var mulai   =   $("#tanggal_mulai").val();
            var akhir   =   $("#tanggal_akhir").val();
            $('#warehouse-thawing').load("{{ route('warehouse.thawing') }}?mulai=" + mulai + "&sampai=" + akhir, function(){            
                $("#spinersummarythawing").hide();
            });
        }

        
        function loadSummaryDataById(id){
            $("#spinersummarythawing").show();
            var mulai   =   $("#tanggal_mulai").val();
            var akhir   =   $("#tanggal_akhir").val();
            $('#warehouse-thawing').load("{{ route('warehouse.thawing') }}?id=" + id, function(){            
                $("#spinersummarythawing").hide();
            });
        }
        // END OF TAB SUMMARY THAWING
        

        // TAB SUMMARY THAWING FG
        if(hash === "custom-tabs-three-thawingfg"){
            loadThawingFg()
        }
        $("#custom-tabs-three-thawingfg-tab").on('click', function(){
            loadThawingFg()
        });

        // loadThawingFg()
        function loadThawingFg(){
            $("#spinerthawingfg").show();
            var mulai   =   $("#tanggal_mulai").val();
            var akhir   =   $("#tanggal_akhir").val();
            $('#warehouse-thawingfg').load("{{ route('warehouse.thawingfg') }}?mulai=" + mulai + "&sampai=" + akhir, function(){            
                $("#spinerthawingfg").hide();
            });
        }
        // END OF TAB SUMMARY THAWING FG
       

        // TAB THAWING STOCK
        if(hash === "custom-tabs-three-stockgudang"){
            loadThawingStock()
        }
        $("#custom-tabs-three-stockgudang-tab").on('click', function(){
            loadThawingStock()
        });
        // loadThawingStock()
        function loadThawingStock(){
            $("#spinerwarehousestock").show();
            var mulai   =   $("#tanggal_mulai").val();
            var akhir   =   $("#tanggal_akhir").val();
            var lokasi  =   $("#lokasi_gudang").val();
            var filter  =   encodeURIComponent($("#filter_stock").val());
            var subitem =   encodeURIComponent($("#sub_item").val());
            $("#warehouse-stock").load("{{ route('warehouse.stock') }}?tanggal_mulai=" + mulai + "&tanggal_akhir=" + akhir + "&lokasi=" + lokasi + "&filter=" + filter + "&subitem=" + subitem, function(){
                $("#spinerwarehousestock").hide();
            });
        }
        // END OF TAB THAWING STOCK
        
        // $("#subitem_select").load("{{ route('warehouse.stock', ['key' => 'subitem']) }}") ;

        // $("#filter_stock").on('keyup', function(){
        //     var mulai   =   $("#tanggal_mulai").val();
        //     var akhir   =   $("#tanggal_akhir").val();
        //     var lokasi  =   $("#lokasi_gudang").val();
        //     var filter  =   encodeURIComponent($("#filter_stock").val());
        //     var subitem =   encodeURIComponent($("#sub_item").val());
        //     $('#warehouse-requestthawing').load("{{ route('warehouse.requestthawing') }}?mulai=" + mulai + "&akhir=" + akhir);
        //     $('#warehouse-thawing').load("{{ route('warehouse.thawing') }}?mulai=" + mulai + "&sampai=" + akhir) ;
        //     $('#warehouse-thawingfg').load("{{ route('warehouse.thawingfg') }}?mulai=" + mulai + "&sampai=" + akhir) ;
        //     $("#warehouse-stock").load("{{ route('warehouse.stock') }}?tanggal_mulai=" + mulai + "&tanggal_akhir=" + akhir + "&lokasi=" + lokasi + "&filter=" + filter + "&subitem=" + subitem) ;
        // }) ;

        // function subitem_select() {
        //     var mulai   =   $("#tanggal_mulai").val();
        //     var akhir   =   $("#tanggal_akhir").val();
        //     var lokasi  =   $("#lokasi_gudang").val();
        //     var filter  =   encodeURIComponent($("#filter_stock").val());
        //     var subitem =   encodeURIComponent($("#sub_item").val());
        //     $('#warehouse-requestthawing').load("{{ route('warehouse.requestthawing') }}?mulai=" + mulai + "&akhir=" + akhir);
        //     $('#warehouse-thawing').load("{{ route('warehouse.thawing') }}?mulai=" + mulai + "&sampai=" + akhir) ;
        //     $('#warehouse-thawingfg').load("{{ route('warehouse.thawingfg') }}?mulai=" + mulai + "&sampai=" + akhir) ;
        //     $("#warehouse-stock").load("{{ route('warehouse.stock') }}?tanggal_mulai=" + mulai + "&tanggal_akhir=" + akhir + "&lokasi=" + lokasi + "&filter=" + filter + "&subitem=" + subitem) ;
        // }

        $(document).ready(function() {
            $(document).on('click', '.hapus_thawing', function() {
                var id      =   $(this).data('id') ;
                var mulai   =   $("#tanggal_mulai").val() ;
                var sampai  =   $("#tanggal_akhir").val() ;

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $(".hapus_thawing").hide() ;

                $.ajax({
                    url: "{{ route('thawingproses.delete') }}",
                    method: "DELETE",
                    data: {
                        id  :   id,
                    },
                    success: function(data) {
                        if (data.status == 400) {
                            showAlert(data.msg);
                        } else {
                            $('#warehouse-requestthawing').load("{{ route('warehouse.requestthawing') }}?mulai=" + mulai + "&akhir=" + sampai);
                            showNotif(data.msg);
                        }
                        $(".hapus_thawing").show() ;
                    }
                });
            })

            $('#download-item').on("click", function(){
                    var mulai    = $('#tanggal_mulai').val();
                    var sampai   = $('#tanggal_akhir').val();

                    if(id){
                        var url = "{{ route('thawingproses.download') }}" +
                            "?key=unduh" +
                            "&id=" + id;
                    }else {
                        var url = "{{ route('thawingproses.download') }}" +
                            "?key=unduh" +
                            "&mulai=" + mulai +
                            "&sampai=" + sampai;
                    }

                    window.location.href = url;

            });
        });
</script>
@endsection