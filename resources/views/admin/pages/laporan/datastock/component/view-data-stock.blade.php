<style>
    .center{
        text-align: center;
    }
    .table-responsive{
        overflow-x: auto;
    }
    .color-light-blue{
        background-color: #D9E1F5;
    }
    .color-light-green{
        background-color: #C7DFB2;
    }
    .color-light-yellow{
        background-color: #EDEF6D;
    }
    .color-light-red{
        background-color: #F4CEAF;
    }
</style>
<div class="row">
    <div class="col-lg-12">
        <div class="mb-4">
            <div id="loading-karkas" class="text-center" style="display: none; justify-content:center;">
                <img src="{{ asset('loading.gif') }}" style="width: 20px"> Loading Stok Karkas...
            </div>
            <div id="viewdatakarkas"></div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="mt-0">
            <div id="loading-sampingan" class="text-center" style="display: none; justify-content:center;">
                <img src="{{ asset('loading.gif') }}" style="width: 20px"> Loading Stok Sampingan...
            </div>
            <div id="viewdatasampingan"></div>
        </div>
        <div class="mt-4">
            <div id="loading-fulfillment" class="text-center" style="display: none; justify-content:center;">
                <img src="{{ asset('loading.gif') }}" style="width: 20px"> Loading Sisa Penyiapan...
            </div>
            <div id="viewdatafulfillment"></div>
        </div>
       
    </div>
    <div class="col-lg-6">
        <div>
            <div id="loading-boneless" class="text-center" style="display: none; justify-content:center;">
                <img src="{{ asset('loading.gif') }}" style="width: 20px"> Loading Stok Boneless...
            </div>
            <div id="viewdataboneless"></div>
        </div>
        <div class="mt-4">
            <div id="loading-retur" class="text-center" style="display: none; justify-content:center;">
                <img src="{{ asset('loading.gif') }}" style="width: 20px"> Loading Retur...
            </div>
            <div id="viewdataretur"></div>
        </div>
        <div class="mt-4">
            <div id="loading-other" class="text-center" style="display: none; justify-content:center;">
                <img src="{{ asset('loading.gif') }}" style="width: 20px"> Loading Lain-Lain...
            </div>
            <div id="viewdataother"></div>
        </div>
    </div>
</div>

<script>
    var hash            = window.location.hash;

    defaultPage();
    function defaultPage() {
        if (hash == undefined || hash == "") {
            reloaddatastock()
        }
    }

    var mulai           = "{{ $mulai }}";
    var akhir           = "{{ $akhir }}";
        
    function reloaddatastock() {
        
        $.ajax({
            url: "{{ route('laporan.chillerdatastock') }}",
            type: "GET",
            data: {
                'key'               : 'view_page',
                'loadBoneless'      : 'YES',
                'mulai'             : mulai,
                'akhir'             : akhir,
            },
            beforeSend: function(){
                $("#loading-boneless").show()
            },
            success: function(data) {
                setTimeout(function(){
                    $("#viewdataboneless").html(data);
                    $("#loading-boneless").hide()
                },100)
            }
        });

        $.ajax({
            url: "{{ route('laporan.chillerdatastock') }}",
            type: "GET",
            data: {
                'key'               : 'view_page',
                'loadFulfillment'   : 'YES',
                'mulai'             : mulai,
                'akhir'             : akhir,
            },
            beforeSend: function(){
                $("#loading-fulfillment").show()
            },
            success: function(data) {
                setTimeout(function(){
                    $("#viewdatafulfillment").html(data);
                    $("#loading-fulfillment").hide()
                },100)
            }
        });

        $.ajax({
            url: "{{ route('laporan.chillerdatastock') }}",
            type: "GET",
            data: {
                'key'               : 'view_page',
                'loadRetur'         : 'YES',
                'mulai'             : mulai,
                'akhir'             : akhir,
            },
            beforeSend: function(){
                $("#loading-retur").show()
            },
            success: function(data) {
                setTimeout(function(){
                    $("#viewdataretur").html(data);
                    $("#loading-retur").hide()
                },100)
            }
        });

        $.ajax({
            url: "{{ route('laporan.chillerdatastock') }}",
            type: "GET",
            data: {
                'key'               : 'view_page',
                'loadOther'         : 'YES',
                'mulai'             : mulai,
                'akhir'             : akhir,
            },
            beforeSend: function(){
                $("#loading-other").show()
            },
            success: function(data) {
                setTimeout(function(){
                    $("#viewdataother").html(data);
                    $("#loading-other").hide()
                },100)
            }
        });

        $.ajax({
            url: "{{ route('laporan.chillerdatastock') }}",
            type: "GET",
            data: {
                'key'               : 'view_page',
                'loadkarkas'        : 'YES',
                'mulai'             : mulai,
                'akhir'             : akhir,
            },
            beforeSend: function(){
                $("#loading-karkas").show()
            },
            success: function(data) {
                $("#viewdatakarkas").html(data);
                $("#loading-karkas").hide()
            }
        });

        $.ajax({
            url: "{{ route('laporan.chillerdatastock') }}",
            type: "GET",
            data: {
                'key'               : 'view_page',
                'loadSampingan'     : 'YES',
                'mulai'             : mulai,
                'akhir'             : akhir,
            },
            beforeSend: function(){
                $("#loading-sampingan").show()
            },
            success: function(data) {
                setTimeout(function(){
                    $("#viewdatasampingan").html(data);
                    $("#loading-sampingan").hide()
                },100)
            }
        });
    }

</script>