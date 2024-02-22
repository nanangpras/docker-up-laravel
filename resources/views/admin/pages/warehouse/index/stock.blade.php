<div class="row">
    <div class="col">
        <div class="form-group">
            <label for="tanggal_mulai_stock">Filter</label>
            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                @endif class="form-control" id="tanggal_mulai_stock" value="{{ $tanggal_mulai }}">
        </div>
    </div>
    <div class="col">
        <div class="form-group">
            <label for="tanggal_akhir_stock">&nbsp;</label>
            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                @endif class="form-control" id="tanggal_akhir_stock" value="{{ $tanggal_akhir }}">
        </div>
    </div>
</div>
<h5 id="refresh-stockbyitem" style="display: none" class="text-center"><i class="fa fa-refresh fa-spin"></i> Loading....
</h5>
<div id="warehouse-stock"></div>

<script>
    var hash = window.location.hash;
    if(hash === "#custom-tabs-three-stock" || hash==='' || hash=== undefined){
        LoadDataStockByItem();
    }
    //STOCK BY ITEM
    $("#custom-tabs-three-stock-tab").on('click', function(){
        LoadDataStockByItem()
    });

    $("#tanggal_mulai_stock,#tanggal_akhir_stock").on('change', function(){
        LoadDataStockByItem()
    });

    function LoadDataStockByItem(){
        $('#refresh-stockbyitem').show();
        $("#warehouse-stock").hide();

        var mulai   =   $("#tanggal_mulai_stock").val();
        var akhir   =   $("#tanggal_akhir_stock").val();

        $.ajax({
            url : "{{ route('warehouse.stock') }}",
            method: "GET",
            data :{
                'tanggal_mulai' : mulai,
                'tanggal_akhir' : akhir
            },
            success: function(data){
                $("#warehouse-stock").html(data);
                $("#warehouse-stock").show();
                $('#refresh-stockbyitem').hide();
            }
        });
    }
   
    // $("#warehouse-stock").load("{{ route('warehouse.stock') }}") ;
    // $("#tanggal_mulai_stock").on('change', function() {
    //     var mulai   =   $("#tanggal_mulai_stock").val();
    //     var akhir   =   $("#tanggal_akhir_stock").val();
    //     $("#warehouse-stock").load("{{ route('warehouse.stock') }}?tanggal_mulai=" + mulai + "&tanggal_akhir=" + akhir) ;
    // });
    // $("#tanggal_akhir_stock").on('change', function() {
    //     var mulai   =   $("#tanggal_mulai_stock").val();
    //     var akhir   =   $("#tanggal_akhir_stock").val();
    //     $("#warehouse-stock").load("{{ route('warehouse.stock') }}?tanggal_mulai=" + mulai + "&tanggal_akhir=" + akhir) ;
    // });
</script>