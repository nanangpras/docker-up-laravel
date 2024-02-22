@extends('admin.layout.template')

@section('content')
<section class="panel pb-0 sticky-top">
    <div class="card-body p-2">
        <div class="row">
            <div class="col-md-4">
                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                    @endif class="form-control change-date mb-2" id="pencarian_awal" value="{{ $tanggal }}"
                    placeholder="Cari...">
                <label>
                    <input type="checkbox" name="range-tanggal" id="ganti-tanggal"> Tanggal Awal & Akhir
                </label>
            </div>
            <div class="col-md-4" id="panel-tanggal-akhir" style="display: none">
                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                    @endif class="form-control change-date mb-2" id="pencarian_akhir" value="{{ $tanggal }}"
                    placeholder="Cari...">
            </div>
        </div>
        <div id="dashboard-loading-pageAwal" style="height: 30px">
            <div class="text-center mb-2 mt-2" style="position: absolute; left: 0; right: 0;">
                <img src="{{asset('loading.gif')}}" style="width: 20px"> Loading ...
            </div>
        </div>
    </div>
</section>

<section class="panel">
    <div class="card-body">
        <div class="mb-3 border-bottom pb-3">
            <a href="{{ route('dashboard.mingguan') }}" class="btn btn-outline-info">Dashboard Perbandingan Mingguan</a>
        </div>
        <div id="pageSatu"></div>

        <hr>

        <div id="pageDua"></div>

        <div id="pageTiga"></div>

        <div class="my-4" id="pageEmpat"></div>

        <div id="pageLima"></div>

        <div id="pageEnam"></div>

        <div id="pageTujuh"></div>

        <div id="pageDelapan"></div>

        <div id="pageSembilan"></div>

        <div id="pageSepuluh"></div>

        <div id="pageSebelas"></div>

        <div id="pageDuaBelas"></div>

        <div id="pageTigaBelas"></div>
    </div>
</section>
@stop

@section('header')
<link rel="stylesheet" type="text/css" href="{{asset('')}}highcharts/highcharts-style.css" />
@stop

@section('footer')
<script src="{{ asset("highcharts/highcharts.js") }}"></script>
<script src="{{ asset("highcharts/highcharts-more.js") }}"></script>
<script src="{{ asset("highcharts/exporting.js") }}"></script>
<script src="{{ asset("highcharts/export-data.js") }}"></script>
<script src="{{ asset("highcharts/accessibility.js") }}"></script>
<script>
    var sama            = true;
        var tanggal_awal    =   $("#pencarian_awal").val();
        var tanggal_akhir   =   $("#pencarian_akhir").val();

        $('#ganti-tanggal').on('change', function(){
            if ($(this).is(':checked')) {
                console.log('Checked',tanggal_awal, tanggal_akhir);
                $('#panel-tanggal-akhir').show();
                sama = false;
            }else{
                $('#panel-tanggal-akhir').hide();
                sama = true;
                $('#dashboard-loading-pageAwal').show()
                tanggal_awal    =   tanggal_awal;
                tanggal_akhir   =   tanggal_awal;
                setTimeout(function(){
                    loadDashboard();
                },200); 
                console.log('Unchecked',tanggal_akhir);
            }
        });

        // $('#report-purc-dev').load("{{ route('dashboard.purchdev') }}");

        $('#pencarian_awal').on('change', function() {

            $('#dashboard-loading-pageAwal').show()
            console.log('Checked',tanggal_awal, tanggal_akhir);
            if(sama == true){
                tanggal_awal    =   $(this).val();
                tanggal_akhir   =   tanggal_awal;
            }else{
                tanggal_awal    =   $(this).val();
                tanggal_akhir   =   $("#pencarian_akhir").val();
            }
            setTimeout(function(){
                loadDashboard();
            },200);
        })

        $('#pencarian_akhir').on('change', function() {
            console.log('Checked',tanggal_awal, tanggal_akhir);
            $('#dashboard-loading-pageAwal').show()
            tanggal_awal    =   $("#pencarian_awal").val();
            tanggal_akhir   =   $(this).val();
            loadDashboard();
        })
        
        loadDashboard();

        function loadDashboard() {
        $('#pageSatu').load("{{ route('dashboard.purchdev', ['key' => 'pageSatu']) }}&tanggal_awal=" + tanggal_awal + "&tanggal_akhir=" + tanggal_akhir, function() {
            $('#dashboard-loading-pageAwal').hide()
            // Load page dua
            $('#pageDua').load("{{ route('dashboard.purchdev', ['key' => 'pageDua']) }}&tanggal_awal=" + tanggal_awal + "&tanggal_akhir=" + tanggal_akhir, function() {
                $('#dashboard-loading-pageDua').hide()
                // Load page tiga
                $('#pageTiga').load("{{ route('dashboard.purchdev', ['key' => 'pageTiga']) }}&tanggal_awal=" + tanggal_awal + "&tanggal_akhir=" + tanggal_akhir, function() {
                    $('#dashboard-loading-pageTiga').hide()
                    // Load page empat
                    $('#pageEmpat').load("{{ route('dashboard.purchdev', ['key' => 'pageEmpat']) }}&tanggal_awal=" + tanggal_awal + "&tanggal_akhir=" + tanggal_akhir, function() {
                        $('#dashboard-loading-pageEmpat').hide()
                        // Load page lima
                        $('#pageLima').load("{{ route('dashboard.purchdev', ['key' => 'pageLima']) }}&tanggal_awal=" + tanggal_awal + "&tanggal_akhir=" + tanggal_akhir, function() {
                            $('#dashboard-loading-pageLima').hide()
                            // Load page Enam
                            $('#pageEnam').load("{{ route('dashboard.purchdev', ['key' => 'pageEnam']) }}&tanggal_awal=" + tanggal_awal + "&tanggal_akhir=" + tanggal_akhir, function() {
                                $('#dashboard-loading-pageEnam').hide()
                                // Load page Tujuh
                                $('#pageTujuh').load("{{ route('dashboard.purchdev', ['key' => 'pageTujuh']) }}&tanggal_awal=" + tanggal_awal + "&tanggal_akhir=" + tanggal_akhir, function() {
                                    $('#dashboard-loading-pageTujuh').hide()
                                    // Load page Delapan
                                    $('#pageDelapan').load("{{ route('dashboard.purchdev', ['key' => 'pageDelapan']) }}&tanggal_awal=" + tanggal_awal + "&tanggal_akhir=" + tanggal_akhir, function() {
                                        $('#dashboard-loading-pageDelapan').hide()
                                        // Load page Sembilan
                                        $('#pageSembilan').load("{{ route('dashboard.purchdev', ['key' => 'pageSembilan']) }}&tanggal_awal=" + tanggal_awal + "&tanggal_akhir=" + tanggal_akhir, function() {
                                            $('#dashboard-loading-pageSembilan').hide()
                                            // Load page Sepuluh
                                            $('#pageSepuluh').load("{{ route('dashboard.purchdev', ['key' => 'pageSepuluh']) }}&tanggal_awal=" + tanggal_awal + "&tanggal_akhir=" + tanggal_akhir, function() {
                                                $('#dashboard-loading-pageSepuluh').hide()
                                                // Load page Sebelas
                                                $('#pageSebelas').load("{{ route('dashboard.purchdev', ['key' => 'pageSebelas']) }}&tanggal_awal=" + tanggal_awal + "&tanggal_akhir=" + tanggal_akhir, function() {
                                                    $('#dashboard-loading-pageSebelas').hide()
                                                    // Load page DuaBelas
                                                    $('#pageDuaBelas').load("{{ route('dashboard.purchdev', ['key' => 'pageDuaBelas']) }}&tanggal_awal=" + tanggal_awal + "&tanggal_akhir=" + tanggal_akhir, function() {
                                                        $('#dashboard-loading-pageDuaBelas').hide()
                                                        // Load page DuaBelas
                                                        $('#pageTigaBelas').load("{{ route('dashboard.purchdev', ['key' => 'pageTigaBelas']) }}&tanggal_awal=" + tanggal_awal + "&tanggal_akhir=" + tanggal_akhir, function() {
                                                            $('#dashboard-loading-pageTigaBelas').hide()
                                                        })
                                                    })
                                                })
                                            })
                                        })
                                    })
                                })
                            })
                        })
                    })
                })
            })
        })
    }

</script>
@stop


<style>
    #container {
        height: 400px;
    }

    .highcharts-figure,
    .highcharts-data-table table {
        min-width: 310px;
        max-width: 800px;
        margin: 1em auto;
    }

    .highcharts-data-table table {
        font-family: Verdana, sans-serif;
        border-collapse: collapse;
        border: 1px solid #EBEBEB;
        margin: 10px auto;
        text-align: center;
        width: 100%;
        max-width: 500px;
    }

    .highcharts-data-table caption {
        padding: 1em 0;
        font-size: 1.2em;
        color: #555;
    }

    .highcharts-data-table th {
        font-weight: 600;
        padding: 0.5em;
    }

    .highcharts-data-table td,
    .highcharts-data-table th,
    .highcharts-data-table caption {
        padding: 0.5em;
    }

    .highcharts-data-table thead tr,
    .highcharts-data-table tr:nth-child(even) {
        background: #f8f8f8;
    }

    .highcharts-data-table tr:hover {
        background: #f1f7ff;
    }

    .thin-bar {
        height: 5px;
    }

    .outer-table-scroll {
        max-height: 300px;
        overflow-y: auto;
        width: 100%;
    }
</style>