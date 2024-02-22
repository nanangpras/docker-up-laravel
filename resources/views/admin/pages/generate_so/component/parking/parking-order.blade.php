<section class="panel">
    <div class="card-body">
        <h6>Filter</h6>
        <div class="row">
            <div class="col pr-1">
                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                    @endif value="{{ date("Y-m-d", strtotime('tomorrow')) }}" id="tanggal_mulai_parking_order"
                    class="form-control">
            </div>
            <div class="col pl-1">
                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                    @endif value="{{ date("Y-m-d", strtotime('tomorrow')) }}" id="tanggal_akhir_parking_order"
                    class="form-control">
            </div>
        </div>
        <div class="row mt-2">
            <div class="col pr-1">
                <label for="filter_parking_order">Pencarian</label>
                <input type="text" id="filter_parking_order" class="form-control" placeholder="Cari...">
            </div>
        </div>
        <div class="row mt-2">
            <div class="col pr-1" id="customer_parking_order">

            </div>
            <div class="col pl-1" id="marketing_parking_order">

            </div>
        </div>

        <hr>
        <div id="parking_orders"></div>
    </div>
</section>

<script>
    /**********************************************************************/
    /*                                                                    */
    /*                      INI BAGIAN PARKING ORDER                      */
    /*                                                                    */
    /**********************************************************************/

    $("#tanggal_mulai_parking_order,#tanggal_akhir_parking_order").on('change', function(){
        loadParkingOrders()
    })

    $("#filter_parking_order").on('keyup', function(){
        loadParkingOrders()
    })

    function customer_parking_order(){
        loadParkingOrders()
    }
    function marketing_parking_order(){
        loadParkingOrders()
    }

    function loadParkingOrders(){
        let tanggal_mulai_parking_order                =   $("#tanggal_mulai_parking_order").val()
        let tanggal_akhir_parking_order                =   $("#tanggal_akhir_parking_order").val()
        let filter_parking_order                       =   encodeURIComponent($("#filter_parking_order").val())
        let customer_parking_order                     =   $("#filter_customer_parking_order").val() ?? ''
        let marketing_parking_order                    =   $("#filter_marketing_parking_order").val() ?? ''
        
        let onparkingorderdata = {
            'tanggal_mulai_parking_order'   : tanggal_mulai_parking_order,
            'tanggal_akhir_parking_order'   : tanggal_akhir_parking_order,
            'filter_parking_order'          : filter_parking_order,
            'customer_parking_order'        : customer_parking_order,
            'marketing_parking_order'       : marketing_parking_order,
        }
        
        $.ajax({
            url     : "{{ route('regu.index', ['key' => 'parking_orders']) }}",
            method  : "GET",
            data    : onparkingorderdata,
            beforeSend: function(){
            },
            success: function(data){
                $("#parking_orders").html(data);
            }
        });
        $.ajax({
            url     : "{{ route('regu.index', ['key' => 'customer_parking_orders']) }}",
            method  : "GET",
            data    : onparkingorderdata,
            beforeSend: function(){

            },
            success: function(data){
                $("#customer_parking_order").html(data);
            }
        });
        $.ajax({
            url     : "{{ route('regu.index', ['key' => 'marketing_parking_orders']) }}",
            method  : "GET",
            data    : onparkingorderdata,
            beforeSend: function(){

            },
            success: function(data){
                $("#marketing_parking_order").html(data);
            }
        });
    }

</script>