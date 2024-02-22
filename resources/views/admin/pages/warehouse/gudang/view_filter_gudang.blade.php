<div class="card" style="background-color:#dae4e8;border: 0px solid rgba(0,0,0,.125);">
    <div class="card-body">
        <div style="float: right;">
            <div class="row">
                <div class="col-6">
                    {!! $filter_gudang !!}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card" style="border: 0px solid rgba(0,0,0,.125);">
    <div id="grafikactivity"></div>
    <div id="activityloading" style="height: 30px">
        <div class="text-center mb-2 mt-2" style="position: absolute; left: 0; right: 0;">
            <img src="{{asset('loading.gif')}}" style="width: 40px"> Loading ...
        </div>
    </div>
    <div id="detailActivityGudang"></div>
    <div id="stockGudangView"></div>
</div>
<script>

    $('.select2').select2({
        theme: 'bootstrap4'
    })
    var gudangid                        = $("#all_gudang").val();
    var hash                            = window.location.hash;
    defaultPage();
    function defaultPage() {
        if (hash == undefined || hash == "") {
            reloadgrafikgudang()
            reloadtabelactivity()
            reloadstockgudangview();
        }
    }
    $("#all_gudang").on('change', function () {
        reloadgrafikgudang();
        reloadtabelactivity();
        reloadstockgudangview();
    });

    function reloadgrafikgudang() {
        let gudangid                      = $("#all_gudang").val();
        
        $.ajax({
            url: "{{ route('warehouse_dash.dashboard') }}",
            type: "GET",
            data: {
                'key'                   : 'view',
                'loadGrafik'            : 'YES',
                'gudangid'              : gudangid,
                'tanggal_awal'          : tanggal_awal,
                'tanggal_akhir'         : tanggal_akhir,
            },
            success: function(data) {
                $("#grafikactivity").html(data);
            }
        });
    }
    function reloadtabelactivity() {
        let gudangid                      = $("#all_gudang").val();
        
        $.ajax({
            url: "{{ route('warehouse_dash.dashboard') }}",
            type: "GET",
            data: {
                'key'                   : 'view',
                'loadTableActivity'     : 'YES',
                'gudangid'              : gudangid,
                'tanggal_awal'          : tanggal_awal,
                'tanggal_akhir'         : tanggal_akhir,
            },
            beforeSend:function(){
                $("#activityloading").show()
            },
            success: function(data) {
                $("#detailActivityGudang").html(data);
                $("#activityloading").hide()
            }
        });
    }

    function reloadstockgudangview() {
        let gudangid                      = $("#all_gudang").val();
        
        $.ajax({
            url: "{{ route('warehouse_dash.dashboard') }}",
            type: "GET",
            data: {
                'key'                   : 'view',
                'loadStockGudang'       : 'YES',
                'gudangid'              : gudangid,
                'tanggal_awal'          : tanggal_awal,
                'tanggal_akhir'         : tanggal_akhir,
            },
            success: function(data) {
                $("#stockGudangView").html(data);
            }
        });
    }
</script>