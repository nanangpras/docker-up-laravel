<section class="panel">
    <div class="card-body">
        <div class="row">
            <div class="col-4">
                <div class="form-group">
                    <label for="tanggal_awal">Tanggal Awal</label>
                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                        min="2023-01-01" @endif id="tanggal_awal" value="{{ date("Y-m-d") }}" class="form-control">
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label for="tanggal_akhir">Tanggal Akhir</label>
                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                        min="2023-01-01" @endif id="tanggal_akhir" value="{{ date("Y-m-d") }}" class="form-control">
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label for="filter">Filter</label>
                    <select class="form-control" id="filterjenis" name="filter">
                        <option value="semua" @if($filterjenis=="semua" ) selected @endif>- Semua Jenis -</option>
                        <option value="fresh" @if($filterjenis=="fresh" ) selected @endif>Fresh</option>
                        <option value="frozen" @if($filterjenis=="frozen" ) selected @endif>Frozen</option>
                    </select>
                </div>
            </div>
        </div>
        <label class="mt-2 px-2 pt-2 rounded status-info">
            <input id="tanggalkirim" type="checkbox"> <label for="tanggalkirim">Tanggal Kirim</label>
        </label>
        <label class="mt-2 px-2 pt-2 rounded status-success">
            <input id="filterpendingso" type="checkbox"> <label for="filterpendingso">Pending ACC</label>
        </label>
        <label class="mt-2 px-2 pt-2 rounded status-danger">
            <input id="filterbatalso" type="checkbox"> <label for="filterbatalso">SO Batal</label>
        </label>
        <label class="mt-2 px-2 pt-2 rounded status-warning">
            <input id="filterbatalitemso" type="checkbox"> <label for="filterbatalitemso">Item Batal</label>
        </label>
        <label class="mt-2 px-2 pt-2 rounded status-info">
            <input id="filtereditso" type="checkbox"> <label for="filtereditso">SO Edit</label>
        </label>
        <label class="mt-2 px-2 pt-2 rounded status-danger">
            <input id="filtergagalso" type="checkbox"> <label for="filtergagalso">SO Gagal</label>
        </label>
        <label class="mt-2 px-2 pt-2 rounded status-purple">
            <input id="filterholdso" type="checkbox"> <label for="filterholdso">SO Retry</label>
        </label>
        <label class="mt-2 px-2 pt-2 rounded status-success">
            <input id="filterbyproduct" type="checkbox"> <label for="filterbyproduct">By Product</label>
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
        <div class="row" id="summaries" style="display: none;">
            <div class="col">
                <div class="form-group">
                    <a href="javascript:void(0);" class="btn btn-md btn-success float-right mb-2 downloadsummaryso"><i
                            class="fa fa-download spinerloading"></i> <span id="textdownload">Export Excel </span></a>
                </div>
            </div>
        </div>
    </div>
</section>

<h5 id="loading_summary" style="display: none" class="text-center"><i class="fa fa-refresh fa-spin"></i> Loading....
</h5>
<h5 id="loading_summary_data" style="display: none" class="text-center"><i class="fa fa-refresh fa-spin"></i>
    Loading....</h5>
<div id="data_summary"></div>


<script>
    /**********************************************************************/
    /*                                                                    */
    /*                      INI BAGIAN SUMMARY ORDER                      */
    /*                                                                    */
    /**********************************************************************/

    $(".select2").select2({
        theme: "bootstrap4"
    });

    $("#tanggalkirim,#filterbatalso,#filterbatalitemso,#filtereditso,#filterpendingso,#filtergagalso,#filterholdso,#filterjenis,#tanggal_awal,#tanggal_akhir,#filterbyproduct,#customer_dataSO,#marketing_dataSO").on('change', function() {
        setTimeout(() => {
            loadsummarySO()
            loadcustomerSO()
            loadmarketingSO()
        },500)
    })

    $("#filtersummarySO").on('keyup', function(){
        setTimeout(() => {
            loadsummarySO();
            loadcustomerSO();
            loadmarketingSO();
        },500)
    }) ;

    function customer_so(){
        loadsummarySO();
    }
    function marketing_so(){
        loadsummarySO();
    }
    
    function loadcustomerSO(){
        // loadsummarySO()
        var tanggal_awal    = $("#tanggal_awal").val() ;
        var tanggal_akhir   = $("#tanggal_akhir").val() ;
        $("#customer_select").load("{{ route('buatso.index', ['key' => 'summary']) }}&subkey=getfiltercustomer&tanggal_awal=" + tanggal_awal + "&tanggal_akhir=" + tanggal_akhir ,function() {
            
        }) ;
        
    }
    function loadmarketingSO(){
        // loadsummarySO()
        var tanggal_awal    = $("#tanggal_awal").val() ;
        var tanggal_akhir   = $("#tanggal_akhir").val() ;
        $("#marketing_select").load("{{ route('buatso.index', ['key' => 'summary']) }}&subkey=getfiltermarketing&tanggal_awal=" + tanggal_awal + "&tanggal_akhir=" + tanggal_akhir , function() {
            
        }) ;
    }

    function loadsummarySO(){
        let tanggalkirim    =  ''
        if($("#tanggalkirim").is(':checked')){
            tanggalkirim = 1

        } else {
            tanggalkirim = 0
        }

        let filterbatalso    =  ''
        if($("#filterbatalso").is(':checked')){
            filterbatalso = 1
        } else {
            filterbatalso = 0
        }

        let filterbatalitemso    =  ''
        if($("#filterbatalitemso").is(':checked')){
            filterbatalitemso = 1
        } else {
            filterbatalitemso = 0
        }

        let filterpendingso    =  ''
        if($("#filterpendingso").is(':checked')){
            filterpendingso = 1
        } else {
            filterpendingso = 0
        }

        let filtereditso    =  ''
        if($("#filtereditso").is(':checked')){
            filtereditso = 1
        } else {
            filtereditso = 0
        }

        let filtergagalso    =  ''
        if($("#filtergagalso").is(':checked')){
            filtergagalso = 1
        } else {
            filtergagalso = 0
        }

        let filterholdso    =  ''
        if($("#filterholdso").is(':checked')){
            filterholdso = 1
        } else {
            filterholdso = 0
        }

        let filterByProduct    =  ''
        if($("#filterbyproduct").is(':checked')){
            filterByProduct = 1
        } else {
            filterByProduct = 0
        }

        let filterjenis     = $("#filterjenis").val();
        var tanggal_awal    = $("#tanggal_awal").val() ;
        var tanggal_akhir   = $("#tanggal_akhir").val() ;
        let search          = encodeURIComponent($("#filtersummarySO").val()) ;
        let customer        = $("#customer_dataSO").val() ?? '' ;
        let marketing       = $("#marketing_dataSO").val() ?? '' ;

        $("#loading_summary_data").show();
        $("#summaries").hide();
        $("#data_summary").load("{{ route('buatso.index', ['key' => 'summary']) }}&tanggal_awal=" + tanggal_awal + "&tanggal_akhir=" + tanggal_akhir + "&search=" + search + "&customer=" + customer + "&marketing=" + marketing + "&tanggalkirim=" + tanggalkirim + "&filterbatalso=" + filterbatalso + "&filtereditso=" + filtereditso + "&filterpendingso=" + filterpendingso + "&filterbatalitemso=" + filterbatalitemso  + "&filtergagalso=" + filtergagalso + "&filterholdso="+filterholdso + "&filterjenis=" +filterjenis + "&filterbyproduct=" + filterByProduct, function() {
            $("#loading_summary_data").hide();
            $("#summaries").show();
        }) ;
        // $("#customer_select").load("{{ route('buatso.index', ['key' => 'customer']) }}&tanggal_awal=" + tanggal_awal + "&tanggal_akhir=" + tanggal_akhir + "&search=" + search + "&customer=" + customer + "&marketing=" + marketing + "&tanggalkirim=" + tanggalkirim + "&filterbatalso=" + filterbatalso + "&filtereditso=" + filtereditso + "&filterpendingso=" + filterpendingso + "&filterbatalitemso=" + filterbatalitemso  + "&filtergagalso=" + filtergagalso + "&filterholdso="+filterholdso, function() {
            
        // }) ;
        // $("#marketing_select").load("{{ route('buatso.index', ['key' => 'marketing']) }}&tanggal_awal=" + tanggal_awal + "&tanggal_akhir=" + tanggal_akhir + "&search=" + search + "&customer=" + customer + "&marketing=" + marketing + "&tanggalkirim=" + tanggalkirim + "&filterbatalso=" + filterbatalso + "&filtereditso=" + filtereditso + "&filterpendingso=" + filterpendingso + "&filterbatalitemso=" + filterbatalitemso  + "&filtergagalso=" + filtergagalso + "&filterholdso="+filterholdso, function() {
            
        // }) ;

    }

    $(document).ready(function(){
        $(".downloadsummaryso").click(function(e){
            e.preventDefault();
            var urldownload = "{{ route('buatso.index') }}"
            let tanggalkirim    =  ''
            if($("#tanggalkirim").is(':checked')){
                tanggalkirim = 1

            } else {
                tanggalkirim = 0
            }

            let filterbatalso    =  ''
            if($("#filterbatalso").is(':checked')){
                filterbatalso = 1
            } else {
                filterbatalso = 0
            }

            let filterbatalitemso    =  ''
            if($("#filterbatalitemso").is(':checked')){
                filterbatalitemso = 1
            } else {
                filterbatalitemso = 0
            }

            let filterpendingso    =  ''
            if($("#filterpendingso").is(':checked')){
                filterpendingso = 1
            } else {
                filterpendingso = 0
            }

            let filtereditso    =  ''
            if($("#filtereditso").is(':checked')){
                filtereditso = 1
            } else {
                filtereditso = 0
            }

            let filtergagalso    =  ''
            if($("#filtergagalso").is(':checked')){
                filtergagalso = 1
            } else {
                filtergagalso = 0
            }

            let filterholdso    =  ''
            if($("#filterholdso").is(':checked')){
                filterholdso = 1
            } else {
                filterholdso = 0
            }

            let filterByProduct    =  ''
            if($("#filterbyproduct").is(':checked')){
                filterByProduct = 1
            } else {
                filterByProduct = 0
            }

            let filterjenis     = $("#filterjenis").val();
            var tanggal_awal    = $("#tanggal_awal").val() ;
            var tanggal_akhir   = $("#tanggal_akhir").val() ;
            let search          = encodeURIComponent($("#filtersummarySO").val()) ;
            let customer        = $("#customer_dataSO").val() ?? '' ;
            let marketing       = $("#marketing_dataSO").val() ?? '' ;

            $.ajax({
                url     : urldownload,
                method  : "GET",
                cache   : false,
                data    : {
                    'key'               : 'summary',
                    'subkey'            : 'downloadsummaryso'
                }, 
                beforeSend: function(){
                    $(".spinerloading").removeClass('fa-download');
                    setTimeout(() =>{
                        $(".spinerloading").addClass('fa-spinner fa-spin');
                        $("#textdownload").text('Downloading...');
                    })
                },
                success: function(data){
                    setTimeout(() => {
                        $("#textdownload").text('Export Excel');
                        $(".spinerloading").removeClass('fa-spinner fa-spin');
                        $(".spinerloading").addClass('fa-download');
                        if(data.status === 1){
                            window.open("{{ route('buatso.index') }}?key=summary&subkey=unduhdata&tanggal_awal="+tanggal_awal +"&tanggal_akhir="+ tanggal_akhir +"&search="+ search +"&customer="+ customer +"&marketing="+ marketing 
                            +"&tanggalkirim="+ tanggalkirim +"&filterbataso="+ filterbatalso +"&filtereditso="+ filtereditso + "&filterpendingso="+filterpendingso +"&filterbatalitemso="+ filterbatalitemso +"&filtergagalso=" +filtergagalso + 
                            "&filterholdso="+filterholdso + "&filterjenis=" +filterjenis +"&filterbyproduct="+ filterByProduct, '_blank')
                        }
                    },500);
                }
            })
        })
    })

</script>