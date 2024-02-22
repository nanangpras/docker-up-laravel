<div class="">
    <div class="row">
        <div class="col-lg-4 col-6">
            <label for="tanggal_awal">Tanggal Awal</label>
            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                @endif class="form-control mb-2" id="tanggal_awal"
                value="{{ $tanggalawal ? $tanggalawal : date('Y-m-d') }}" placeholder="Cari...">
        </div>
        <div class="col-lg-4 col-6">
            <label for="tanggal_akhir"> Tanggal Akhir</label>
            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                @endif class="form-control mb-2" id="tanggal_akhir"
                value="{{ $tanggalakhir ? $tanggalakhir : date('Y-m-d') }}" placeholder="Cari...">
        </div>
    </div>
    <div id="show_view_page"></div>
</div>

<link rel="stylesheet" type="text/css" href="{{asset('')}}highcharts/highcharts-style.css" />

<script src="{{ asset("highcharts/highcharts.js") }}"></script>
<script src="{{ asset("highcharts/highcharts-more.js") }}"></script>
<script src="{{ asset("highcharts/exporting.js") }}"></script>
<script src="{{ asset("highcharts/export-data.js") }}"></script>
<script src="{{ asset("highcharts/accessibility.js") }}"></script>
<script>
    var tanggal_awal    = $("#tanggal_awal").val();
    var tanggal_akhir   = $("#tanggal_akhir").val();

    $('#tanggal_awal,#tanggal_akhir').on('change', function() {
        // tanggal_awal    = $("#tanggal_awal").val();
        // tanggal_akhir   = $("#tanggal_akhir").val();
        setTimeout(function(){
            loadGraphicAllSupplierLb()
            // loadGraphicAllSupplierLb(tanggal_awal,tanggal_akhir)
        },1000);
    })

    loadGraphicAllSupplierLb();
    function loadGraphicAllSupplierLb() {
        var tanggal_awal    = $("#tanggal_awal").val();
        var tanggal_akhir   = $("#tanggal_akhir").val();
        $('#show_view_page').load("{{ route('view_progress', ['key' => 'purchasing']) }}&role=purchasing&_token={{$tToken}}&name={{$subsidiary}}&GenerateToken={{$gettoken}}&subkey=view_data_livebird&tanggal_awal=" + tanggal_awal + "&tanggal_akhir=" + tanggal_akhir)
    }
</script>