<div class="">
    <div class="col-12">
        <div class="row">
            <div class="col-5">
                <label>Tanggal Kirim</label>
                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' )
                    min="2023-01-01" @endif name="tanggal" class="form-control"
                    value="{{ $tanggal ? $tanggal : date('Y-m-d') }}" id="tglpotong">
            </div>
            <div class="col-5">
                <label>Customer</label>
                {!! $cust !!}
            </div>
            <div class="col-2 px-2 py-4 mt-1">
                <button type="button"
                    class="btn btn-primary btn-block py-2 filterdata">FILTER</button>
            </div>
        </div>

    </div>
    <div id="spinerdatamarketing" class="text-center mb-2">
        <img src="{{ asset('loading.gif') }}" style="width: 30px;">
    </div>
    <div id="dataprogressmarketing"></div>
</div>
<script>
    function loaddatamarketing(){
        var tanggal         = $("#tglpotong").val();
        var searchcustomer  = $("#searchcustomer").val();
        $("#spinerdatamarketing").show()
        $.ajax({
            url: "{{ route('view_progress') }}",
            method: "GET",
            data:{
                'role'          : 'marketing',
                'key'           : 'marketing',
                '_token'        : "{{ $tToken }}",
                'name'          : "{{ $subsidiary }}",
                'GenerateToken' : "{{ $gettoken }}",
                'subkey'        : 'view_data_marketing',
                'tanggal'       : tanggal,
                'searchcustomer': searchcustomer
            },
            success: function(data){
                $("#dataprogressmarketing").html(data);
                $("#spinerdatamarketing").hide()
            }
        });
    }

    $(".filterdata").click(function(){
        loaddatamarketing();
    });
</script>
