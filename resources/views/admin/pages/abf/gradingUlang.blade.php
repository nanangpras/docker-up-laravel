@extends('admin.layout.template')

@section('title', 'Grading Ulang')

@section('content')
<div class="row mb-4">
    <div class="col"></div>
    <div class="col text-center">
        <b>Grading Ulang</b>
    </div>
    <div class="col"></div>
</div>

<section class="panel">
    <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
        <li class="nav-item">
            <a class="nav-link tab-link " id="custom-tabs-three-gradul-tab" data-toggle="tab"
                href="#custom-tabs-three-gradul" role="tab" aria-controls="custom-tabs-three-gradul"
                aria-selected="false">Grading Ulang</a>
        </li>
        <li class="nav-item">
            <a class="nav-link tab-link" id="custom-tabs-sumgradul-tab" data-toggle="tab" href="#custom-tabs-sumgradul"
                role="tab" aria-controls="custom-tabs-sumgradul" aria-selected="false">Summary Grading Ulang</a>
        </li>
    </ul>

    <div class="card-body card-primary card-outline card-tabs">
        <div class="tab-content" id="custom-tabs-three-tabContent">
            {{-- Grading Ulang --}}
            <div class="tab-pane fade show active" id="custom-tabs-three-gradul" role="tabpanel"
                aria-labelledby="custom-tabs-three-gradul-tab">
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
                                    <option value="null">Semua</option>
                                    @foreach ($plastik as $item)
                                    <option value="{{$item->nama}}"> {{$item->nama}}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col">
                                <label for="gudang_summary">Filter Gudang</label>
                                <select name="gudang_summary" id="gudang_summary" class="form-control filter_tanggal">
                                    <option value="" disabled selected hidden>Pilih Gudang</option>
                                    <option value="null">Semua</option>
                                    @foreach ($gudang as $row)
                                    <option value="{{ $row->id }}">{{ $row->code }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col">
                                <label for="cari_summary">Filter Cari</label>
                                <select type="text" id="cari_summary"
                                    class="form-control select2 mt-2" data-placeholder="Pilih Item">
                                    <option value="null">Semua</option>
                                    @foreach ($product as $item)
                                            <option value="{{ $item->nama }}">{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col text-right">
                                <button id="data-gradul" class="btn btn-primary">Cari</button>
                            </div>
                        </div>
                    </div>
                </section>
                <div id="loading" class="text-center" style="display: none">
                    <img src="{{ asset('loading.gif') }}" width="30px">
                </div>
                <div id="data_gradul"></div>
            </div>

            <!-- SUMMARY GRADING ULANG  -->
            <div class="tab-pane fade" id="custom-tabs-sumgradul" role="tabpanel"
                aria-labelledby="custom-tabs-sumgradul-tab">
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
                                    <option value="null">Semua</option>
                                    @foreach ($plastik as $item)
                                    <option value="{{$item->nama}}"> {{$item->nama}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col">
                                <label for="gudangSummaryGradingUlang">Filter Gudang</label>
                                <select name="gudangSummaryGradingUlang" id="gudangSummaryGradingUlang" class="form-control">
                                    <option value="" disabled selected hidden>Pilih Gudang</option>
                                    <option value="null">Semua</option>
                                    @foreach ($gudang as $row)
                                    <option value="{{ $row->id }}">{{ $row->code }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col">
                                <label for="cariSummaryGradingUlang">Filter Cari</label>
                                <select type="text" id="cariSummaryGradingUlang"
                                    class="form-control select2" data-placeholder="Pilih Item">
                                    <option value="null">Semua</option>
                                    @foreach ($product as $item)
                                        <option value="{{ $item->nama }}">{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col text-right">
                                <button id="gradul" class="btn btn-primary">Cari</button>
                            </div>
                        </div>
                    </div>
                </section>
                <div id="loadingSummaryGradingUlang" class="text-center" style="display: none">
                    <img src="{{ asset('loading.gif') }}" width="30px">
                </div>
                <div id="summaryGradingUlang"></div>
            </div>
        </div>
    </div>
</section>

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
            var hash = window.location.hash.substr(1);

            var href = window.location.href;

            deafultPage();

            function deafultPage() {
                
                if (hash == undefined || hash == "") {
                    hash = "custom-tabs-three-gradul-tab";
                }
                console.log(hash)

                $('#' + hash + '-tab').addClass('active').siblings().removeClass('active');
                $('#' + hash).addClass('active show').siblings().removeClass('active show');

            }


            $('.tab-link').click(function(e) {
                e.preventDefault();
                status = $(this).attr('aria-controls');
                window.location.hash = status;
                href = window.location.href;

            });


        // gradingUlang
        if(hash === "custom-tabs-three-gradul"){
            loadDataGradingUlang()
        }
        $('#custom-tabs-three-gradul-tab','#data-gradul').click(function(){
            loadDataGradingUlang();
        })
        $('#data-gradul').click(function(){
            loadDataGradingUlang();
        })

        function loadDataGradingUlang() {
            $("#loading").show();

            var mulai   =   $("#mulai_sumary").val();
            var akhir   =   $("#akhir_sumary").val();
            var gudang  =   $("#gudang_summary").val();
            var plastik =   encodeURIComponent($("#plastik_summary").val());
            var cari    =   encodeURIComponent($("#cari_summary").val());


            $.ajax({
                url : "{{ route('abf.index', ['key' => 'data-gradul']) }}&mulai=" + mulai + "&akhir=" + akhir + "&gudang=" + gudang + "&cari=" + cari + "&plastik=" + plastik,
                method: "GET",
                success: function(data){
                    $("#data_gradul").html(data);
                    $("#loading").hide();
                }
            });
        }

        // Summary grading ulang
        $(document).ready(function() {
                // Dapatkan nilai ID dari query parameter URL
                var urlParams = new URLSearchParams(window.location.search);
                var id = urlParams.get('id');

                // Jika ID ada, lakukan pemrosesan
                if (id !== null) {
                    loadSummaryDataById(id);
                }
        });

        $('#gradul').click(function(){
            loadSummaryGradingUlang();
        })

        if(hash === "custom-tabs-sumgradul"){
             if (id !== null) {
                    loadSummaryDataById(id);
                } 
            loadSummaryGradingUlang()
        }
        $('#custom-tabs-sumgradul-tab').click(function(){
            loadSummaryGradingUlang();
        })

        

        function loadSummaryGradingUlang() {
            $("#loadingSummaryGradingUlang").show();
            var mulai   =   $("#tanggalMulaiGradingUlang").val();
            var akhir   =   $("#tanggalAkhirGradingUlang").val();
            var gudang  =   $("#gudangSummaryGradingUlang").val();
            var plastik =   encodeURIComponent($("#plastikSummaryGradingUlang").val());
            var cari    =   encodeURIComponent($("#cariSummaryGradingUlang").val());

            $.ajax({
                url : "{{ route('abf.index', ['key' => 'summaryGradingUlang']) }}&mulai=" + mulai + "&akhir=" + akhir + "&gudang=" + gudang + "&cari=" + cari + "&plastik=" + plastik,
                method: "GET",
                success: function(data){
                    $("#summaryGradingUlang").html(data);
                    $("#loadingSummaryGradingUlang").hide();
                }
            });
        }

        function loadSummaryDataById(id) {
            $("#loadingSummaryGradingUlang").show();
            var mulai   =   $("#tanggalMulaiGradingUlang").val();
            var akhir   =   $("#tanggalAkhirGradingUlang").val();
            var gudang  =   $("#gudangSummaryGradingUlang").val();
            var plastik =   encodeURIComponent($("#plastikSummaryGradingUlang").val());
            var cari    =   encodeURIComponent($("#cariSummaryGradingUlang").val());

            $.ajax({
                url : "{{ route('abf.index', ['key' => 'summaryGradingUlang']) }}&id=" + id,
                method: "GET",
                success: function(data){
                    $("#summaryGradingUlang").html(data);
                    $("#loadingSummaryGradingUlang").hide();
                }
            });
        }

</script>
@stop