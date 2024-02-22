@extends('admin.layout.template')

@section('title', 'ABF')

@section('content')
<div class="row mb-4">
    <div class="col"></div>
    <div class="col text-center">
        <b>ABF</b>
    </div>
    <div class="col"></div>
</div>

@if ($thawing)
<div class="alert alert-danger text-center mb-3">
    {{ $thawing }} Request Thawing Pending
</div>
@endif

<section class="panel">
    <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
        <li class="nav-item">
            <a class="nav-link tab-link" id="tabs-chiller-fg-tab" data-toggle="tab" href="#tabs-chiller-fg" role="tab"
                aria-controls="tabs-chiller-fg" aria-selected="false">ACC ABF (INBOUND)</a>
        </li>
        <li class="nav-item">
            <a class="nav-link tab-link" id="custom-tabs-diterima-tab" data-toggle="tab" href="#custom-tabs-diterima"
                role="tab" aria-controls="custom-tabs-diterima" aria-selected="false">DITERIMA ABF (BONGKAR KE CS)</a>
        </li>
        <li class="nav-item">
            <a class="nav-link tab-link " id="custom-tabs-three-summary-tab" data-toggle="tab"
                href="#custom-tabs-three-summary" role="tab" aria-controls="custom-tabs-three-summary"
                aria-selected="true">SUMMARY ABF</a>
        </li>
        {{-- <li class="nav-item">
            <a class="nav-link tab-link " id="custom-tabs-three-grading-tab" data-toggle="tab"
                href="#custom-tabs-three-grading" role="tab" aria-controls="custom-tabs-three-grading"
                aria-selected="true">SUMMARY GRADING ULANG</a>
        </li> --}}
        @if(env('NET_SUBSIDIARY', 'CGL')=='CGL')
        <li class="nav-item">
            <a class="nav-link tab-link " id="custom-tabs-three-nonlb-tab" data-toggle="tab"
                href="#custom-tabs-three-nonlb" role="tab" aria-controls="custom-tabs-three-nonlb"
                aria-selected="true">PO NONLB (BELI AYAM)</a>
        </li>
        @endif
        @if (User::setIjin('superadmin') or User::setIjin(33))
        <li class="nav-item">
            <a class="nav-link tab-link " id="custom-tabs-three-netsuite-tab" data-toggle="tab"
                href="#custom-tabs-three-netsuite" role="tab" aria-controls="custom-tabs-three-netsuite"
                aria-selected="true">NETSUITE</a>
        </li>
        @endif
    </ul>

    <div class="card-body card-primary card-outline card-tabs">
        <div class="tab-content" id="custom-tabs-three-tabContent">

            <!-- CHILLER ABF INBOUND  -->
            <div class="tab-pane fade" id="tabs-chiller-fg" role="tabpanel" aria-labelledby="tabs-chiller-fg">
                @include('admin.pages.abf.component.chiller_fg')
            </div>

            <!-- CHILLER ABF OUTBOUND  -->
            <div class="tab-pane fade" id="custom-tabs-diterima" role="tabpanel"
                aria-labelledby="custom-tabs-diterima-tab">
                @include('admin.pages.abf.component.abf_diterima')
            </div>

            <!-- CHILLER OPEN BALANCE  -->
            <div class="tab-pane fade" id="custom-tabs-three-input" role="tabpanel"
                aria-labelledby="custom-tabs-three-input-tab">
                <div id="input_open"></div>
            </div>

            <!-- CHILLER ABF SUMMARY  -->
            <div class="tab-pane fade" id="custom-tabs-three-summary" role="tabpanel"
                aria-labelledby="custom-tabs-three-summary-tab">
                <section class="panel">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <label for="mulai_sumary">Filter Tanggal Masuk</label>
                                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                    min="2023-01-01" @endif id="mulai_sumary" value="{{ date('Y-m-d', strtotime("-7 days", time())) }}" class="form-control filter_tanggal form-control-sm">
                            </div>

                            <div class="col">
                                <label for="akhir_sumary">&nbsp</label>
                                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                    min="2023-01-01" @endif id="akhir_sumary" value="{{ date('Y-m-d') }}"
                                    class="form-control filter_tanggal form-control-sm">
                            </div>

                            <div class="col">
                                <label for="plastik_summary">Filter Plastik</label>
                                <select name="plastik_summary" id="plastik_summary" class="form-control select2 filter_tanggal">
                                    <option value="" disabled selected hidden>Pilih Plastik</option>
                                    @foreach ($plastik as $item)
                                    <option value="{{$item->nama}}"> {{$item->nama}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col">
                                <label for="gudang_summary">Filter Gudang</label>
                                <select name="gudang_summary" id="gudang_summary" class="form-control filter_tanggal">
                                    <option value="" disabled selected hidden>Pilih Gudang</option>
                                    @foreach ($gudang as $row)
                                    <option value="{{ $row->id }}">{{ $row->code }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <input type="checkbox" id="tglprod">
                        <label for="tglprod">Filter Tanggal Produksi</label>
                        <div class="row">
                            <div class="col">
                                <label for="cari_summary">Filter Cari</label>
                                <input type="text" placeholder="Cari..." autocomplete="off" id="cari_summary"
                                    class="form-control">
                            </div>
                        </div>
                    </div>
                </section>
                <div id="loading" class="text-center" style="display: none">
                    <img src="{{ asset('loading.gif') }}" width="30px">
                </div>
                <div id="summary_data"></div>
            </div>

            <!-- SUMMARY GRADING ULANG  -->
            <div class="tab-pane fade" id="custom-tabs-three-grading" role="tabpanel"
                aria-labelledby="custom-tabs-grading-tab">
                <section class="panel">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <label for="tanggalMulaiGradingUlang">Filter Tanggal Grading Ulang</label>
                                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                    min="2023-01-01" @endif id="tanggalMulaiGradingUlang" value="{{ date('Y-m-d', strtotime("-7 days", time())) }}" class="form-control filter_tanggal form-control-sm">
                            </div>

                            <div class="col">
                                <label for="tanggalAkhirGradingUlang">&nbsp</label>
                                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                    min="2023-01-01" @endif id="tanggalAkhirGradingUlang" value="{{ date('Y-m-d') }}"
                                    class="form-control form-control-sm">
                            </div>

                            <div class="col">
                                <label for="plastikSummaryGradingUlang">Filter Plastik</label>
                                <select name="plastikSummaryGradingUlang" id="plastikSummaryGradingUlang" class="form-control select2">
                                    <option value="" disabled selected hidden>Pilih Plastik</option>
                                    @foreach ($plastik as $item)
                                    <option value="{{$item->nama}}"> {{$item->nama}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col">
                                <label for="gudangSummaryGradingUlang">Filter Gudang</label>
                                <select name="gudangSummaryGradingUlang" id="gudangSummaryGradingUlang" class="form-control">
                                    <option value="" disabled selected hidden>Pilih Gudang</option>
                                    @foreach ($gudang as $row)
                                    <option value="{{ $row->id }}">{{ $row->code }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <label for="cariSummaryGradingUlang">Filter Cari</label>
                                <input type="text" placeholder="Cari..." autocomplete="off" id="cariSummaryGradingUlang"
                                    class="form-control">
                            </div>
                        </div>
                    </div>
                </section>
                <div id="loadingSummaryGradingUlang" class="text-center" style="display: none">
                    <img src="{{ asset('loading.gif') }}" width="30px">
                </div>
                <div id="summaryGradingUlang"></div>
            </div>

            <!-- CHILLER ABF NON LB  -->
            <div class="tab-pane fade" id="custom-tabs-three-nonlb" role="tabpanel"
                aria-labelledby="custom-tabs-three-nonlb-tab">
                <form action="{{ route('abf.nonlb') }}" method="GET">
                    <div class="row">
                        <div class="col-md-4 col-sm-4 col-xs-6">
                            <div class="form-group">
                                <label for="pencarian-non-lb">Awal</label>
                                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                    min="2023-01-01" @endif name="tanggal" class="form-control change-date-nonlb"
                                    value="{{ $tanggal }}" id="pencarian-non-lb" placeholder="Cari...."
                                    autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4 col-xs-6">
                            <div class="form-group">
                                <label for="pencarian-non-lb-akhir">Akhir</label>
                                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                    min="2023-01-01" @endif name="tanggal" class="form-control change-date-nonlb"
                                    value="{{ $tanggal_akhir }}" id="pencarian-non-lb-akhir" placeholder="Cari...."
                                    autocomplete="off">
                            </div>
                        </div>
                    </div>
                </form>
                <div id="abf-nonlb"></div>
            </div>

            <!-- TAB NETSUITE -->
            <div class="tab-pane fade" id="custom-tabs-three-netsuite" role="tabpanel"
                aria-labelledby="custom-tabs-three-netsuite-tab">
                <div class="row">
                    <div class="col-md-4 col-sm-4 col-xs-6">
                        <div class="form-group">
                            <label for="tanggalmulai">Pencarian</label>
                            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                min="2023-01-01" @endif name="tanggalmulai" class="form-control change-date-netsuite"
                                value="{{ date('Y-m-d') }}" id="tanggalmulai" placeholder="Cari...." autocomplete="off">
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-4 col-xs-6">
                        <div class="form-group">
                            <label for="tanggalend"></label>
                            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                min="2023-01-01" @endif name="tanggalend" class="form-control change-date-netsuite"
                                value="{{ date('Y-m-d') }}" id="tanggalend" placeholder="Cari...." autocomplete="off">
                        </div>
                    </div>
                </div>
                <div id="spinernetsuite" class="text-center" style="display: none">
                    <img src="{{ asset('loading.gif') }}" width="30px">
                </div>
                <div id="netsuitedata"></div>
            </div>
        </div>
    </div>
</section>



<script>
    $(document).ready(function(){
            $('[data-toggle="pill"]:first').click();
        });
</script>
@endsection

@section('header')
<link rel="stylesheet" type="text/css" href="{{asset('')}}plugin/DataTables/datatables.min.css" />
@stop

@section('footer')
<script type="text/javascript" src="{{asset('')}}plugin/DataTables/datatables.min.js"></script>
<script>
        $('.select2').select2({
            theme: 'bootstrap4',
        })

        $(document).ready(function() {
            $('#abfPendingTable').DataTable({
                "bInfo": false,
                responsive: true,
                scrollY: 500,
                scrollX: true,
                scrollCollapse: true,
                paging: false,
            });

            $('a[data-toggle="pill"]').on('shown.bs.tab', function(e) {
                $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
            });
        });
</script>

<script>
    $('[data-toggle="tab"]').click(function () { 
            var hash = $(this).attr('href');
            if (hash == "#custom-tabs-three-summary") {
                loadSummaryAbf();
            } else 
            if (hash == "#custom-tabs-three-nonlb") {
                loadAbfNonLB();
            } else 
            if (hash == "#custom-tabs-three-netsuite") {
                loadNetsuite();
            } else
            if (hash == "#custom-tabs-three-grading") {
                loadSummaryGradingUlang();
            }
        });

        function loadAbf() {
            if (window.location.hash.substr(1) == "custom-tabs-three-summary") {
                loadSummaryAbf();
            } else 
            if (window.location.hash.substr(1) == "custom-tabs-three-grading") {
                loadSummaryGradingUlang();
            } else 
            if (window.location.hash.substr(1) == "custom-tabs-diterima") {
                load_abf_diterima();
            } else 
            if (window.location.hash.substr(1) == "custom-tabs-three-netsuite") {
                loadNetsuite();
            } else 
            if (window.location.hash.substr(1) == "tabs-chiller-fg") {
                loadChillerFg();
            } else {
                loadChillerFg();
            }
        };

        loadAbf();

        function loadInputOpen() {
            $("#input_open").load("{{ route('abf.index', ['key' => 'open']) }}",function () { 
                $('#spinerinbound').hide();
            }) ;
        }

        //TAB  SUMMARY ABF
        $('.filter_tanggal,.filter_plastik,#tglprod').change(function() {
            loadSummaryAbf();
        });

        $('#cari_summary').on('keyup', function() {
            loadSummaryAbf();
        });

        function loadSummaryAbf() {
            $("#loading").show();

            var mulai   =   $("#mulai_sumary").val();
            var akhir   =   $("#akhir_sumary").val();
            var gudang  =   $("#gudang_summary").val();
            var plastik =   encodeURIComponent($("#plastik_summary").val());
            var cari    =   encodeURIComponent($("#cari_summary").val());
            let tglprod =   $('#tglprod').is(':checked') ? 'true' : 'false';


            $.ajax({
                url : "{{ route('abf.index', ['key' => 'summary']) }}&mulai=" + mulai + "&akhir=" + akhir + "&gudang=" + gudang + "&cari=" + cari + "&tglprod=" + tglprod + "&plastik=" + plastik,
                method: "GET",
                success: function(data){
                    $("#summary_data").html(data);
                    $("#loading").hide();
                }
            });
        }

         //TAB  NON LB
        $('.change-date-nonlb').change(function() {
            var tanggal         =   $(this).val();
            var tanggalend      =   $('#tanggalend').val();
            $('#abf-nonlb').load("{{ route('abf.nonlb') }}?tanggal=" + tanggal+ '&tanggal_akhir=' +tanggalend);
        });

        function loadAbfNonLB() {
            $('#loading').show();
            $('#abf-nonlb').load("{{ route('abf.nonlb') }}?tanggal=" + tanggal,function () { 
                $('#loading').hide();
            });
        }
        
        //TAB  NETSUITE
        var netsuiteTimeout = null;  

        $('.change-date-netsuite').change(function() {
            if (netsuiteTimeout != null) {
                clearTimeout(netsuiteTimeout);
            }
            netsuiteTimeout = setTimeout(function() {
                netsuiteTimeout = null;  
                //ajax code
                loadNetsuite();
            }, 1000);  
        })

        function loadNetsuite() {  
            $('#spinernetsuite').show();

            var tanggalmulai    =   $('#tanggalmulai').val();
            var tanggalend      =   $('#tanggalend').val();

            $('#netsuitedata').load("{{ route('abf.netsuite') }}?tanggalmulai=" + tanggalmulai + "&tanggalend=" + tanggalend, function () { 
                $('#spinernetsuite').hide();
            });
        }

            //TAB  SUMMARY GRADING ULANG
        $('#tanggalMulaiGradingUlang, #tanggalAkhirGradingUlang, #plastikSummaryGradingUlang, #gudangSummaryGradingUlang').on('change', function() {
            loadSummaryGradingUlang();

        })

        $('#cariSummaryGradingUlang').on('keyup', function() {
            loadSummaryGradingUlang();
        });

        function loadSummaryGradingUlang() {
            $("#loadingSummaryGradingUlang").show();
            var mulai   =   $("#tanggalMulaiGradingUlang").val();
            var akhir   =   $("#tanggalAkhirGradingUlang").val();
            var gudang  =   $("#gudangSummaryGradingUlang").val();
            var plastik =   encodeURIComponent($("#plastikSummaryGradingUlang").val());
            var cari    =   encodeURIComponent($("#cariSummaryGradingUlang").val());

            console.log(mulai, akhir)


            $.ajax({
                url : "{{ route('abf.index', ['key' => 'summaryGradingUlang']) }}&mulai=" + mulai + "&akhir=" + akhir + "&gudang=" + gudang + "&cari=" + cari + "&plastik=" + plastik,
                method: "GET",
                success: function(data){
                    $("#summaryGradingUlang").html(data);
                    $("#loadingSummaryGradingUlang").hide();
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
                hash = "tabs-chiller-fg";
            }
            // console.log(hash)
    
            $('#' + hash + '-tab').addClass('active').siblings().removeClass('active');
            $('#' + hash).addClass('active show').siblings().removeClass('active show');
    
        }
    
    
        $('.tab-link').click(function(e) {
            e.preventDefault();
            status = $(this).attr('aria-controls');
            window.location.hash = status;
            href = window.location.href;
    
        });
</script>
@stop