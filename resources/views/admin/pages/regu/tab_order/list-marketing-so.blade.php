<section class="panel">
    <div class="card-body">
        <div class="row">
            <div class="col-6">
                <div class="form-group">
                    <label for="tanggal_awal">Tanggal Awal</label>
                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                        min="2023-01-01" @endif id="tanggal_awal" value="{{ date("Y-m-d") }}" class="form-control">
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label for="tanggal_akhir">Tanggal Akhir</label>
                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                        min="2023-01-01" @endif id="tanggal_akhir" value="{{ date("Y-m-d") }}" class="form-control">
                </div>
            </div>
        </div>
        <label class="mt-2 px-2 pt-2 rounded status-info">
            <input id="tanggalkirim" type="checkbox"> <label for="">Pencarian Sesuai Tanggal Kirim</label>
        </label>

        <div class="form-group">
            <label for="">Pencarian</label>
            <input type="text" id="filtersummarySO" class="form-control " value="" placeholder="Cari Memo/NO SO..">
        </div>

        <div class="row">
            <div class="col">
                <div class="form-group">
                    <div id="customer_select"></div>
                </div>
            </div>
            @if (User::setijin(33) || User::setijin(41) || User::setijin(40))
            <div class="col">
                <div id="marketing_select"></div>
            </div>
            @endif
        </div>

    </div>
</section>

<h5 id="loading_summary" style="display: none" class="text-center"><i class="fa fa-refresh fa-spin"></i> Loading....
</h5>
<div id="data_summary"></div>


<script>
    loadsummarySO()
    
    function customer_so(){
        loadsummarySO()
    }
    function marketing_so(){
        loadsummarySO()
    }
    function loadsummarySO(){
        let tanggalkirim    =  ''
        if($("#tanggalkirim").is(':checked')){
            tanggalkirim = 1
            
        } else {
            tanggalkirim = 0
        }
        var tanggal_awal    =   $("#tanggal_awal").val() ;
        var tanggal_akhir   =   $("#tanggal_akhir").val() ;
        let search          =   encodeURIComponent($("#filtersummarySO").val()) ;
        let customer        =   $("#customer_dataSO").val() ?? '' ;
        let marketing       =   $("#marketing_dataSO").val() ?? '' ;
    
        $("#loading_summary").attr('style', 'display: block');
        $("#data_summary").load("{{ route('buatso.index', ['key' => 'summary']) }}&tanggal_awal=" + tanggal_awal + "&tanggal_akhir=" + tanggal_akhir + "&search=" + search + "&customer=" + customer + "&marketing=" + marketing + "&tanggalkirim=" + tanggalkirim, function() {
            $("#loading_summary").attr('style', 'display: none');
        }) ;
        $("#customer_select").load("{{ route('buatso.index', ['key' => 'customer']) }}&tanggal_awal=" + tanggal_awal + "&tanggal_akhir=" + tanggal_akhir + "&search=" + search + "&customer=" + customer + "&marketing=" + marketing + "&tanggalkirim=" + tanggalkirim, function() {
            $("#loading_summary").attr('style', 'display: none');
        }) ;
        $("#marketing_select").load("{{ route('buatso.index', ['key' => 'marketing']) }}&tanggal_awal=" + tanggal_awal + "&tanggal_akhir=" + tanggal_akhir + "&search=" + search + "&customer=" + customer + "&marketing=" + marketing + "&tanggalkirim=" + tanggalkirim, function() {
            $("#loading_summary").attr('style', 'display: none');
        }) ;
    }
    
    $("#tanggalkirim").on('change', function() {
        loadsummarySO()
    })
    
    $("#filtersummarySO").on('keyup', function(){
        loadsummarySO();
        }) ;
    
    $("#tanggal_awal").on('change', function() {
        loadsummarySO() ;
    });
    
    $("#tanggal_akhir").on('change', function() {
        loadsummarySO() ;
    });
</script>