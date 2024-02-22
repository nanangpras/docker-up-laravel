<div class="">
    <div class="row">
        <div class="col-lg-4 col-6">
            Pencarian Tanggal Awal
            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                @endif class="form-control" id="tanggalawal" name="tanggalawal"
                value="{{ $tanggalawal ? $tanggalawal : date('Y-m-d') }}">
        </div>
        <div class="col-lg-4 col-6">
            Pencarian Tanggal Akhir
            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                @endif class="form-control" id="tanggalakhir" name="tanggalakhir"
                value="{{ $tanggalakhir ? $tanggalakhir : date('Y-m-d') }}">
        </div>
    </div>
    <div id="spinerdatapurchasing" class="text-center mb-2">
        <img src="{{ asset('loading.gif') }}" style="width: 30px;">
    </div>
    <div id="dataprogresspurchasing"></div>
</div>
<script>
    $('#tanggalawal, #tanggalakhir').change(function() {
        var tanggalawal     =   $('#tanggalawal').val();
        var tanggalakhir    =   $('#tanggalakhir').val();
        setTimeout(function(){
            loaddatapurchasing();
        },1000)
    });

    function loaddatapurchasing(){
        var tanggalawal     =   $('#tanggalawal').val();
        var tanggalakhir    =   $('#tanggalakhir').val();

        $("#spinerdatapurchasing").show()
        $.ajax({
            url: "{{ route('view_progress') }}",
            method: "GET",
            data:{
                'role'          : 'purchasing',
                'key'           : 'purchasing',
                '_token'        : "{{ $tToken }}",
                'name'          : "{{ $subsidiary }}",
                'GenerateToken' : "{{ $gettoken }}",
                'subkey'        : 'view_data_purchasing',
                'tanggalawal'   : tanggalawal,
                'tanggalakhir'  : tanggalakhir
            },
            success: function(data){
                $("#dataprogresspurchasing").html(data);
                $("#spinerdatapurchasing").hide()
            }
        });
    }
</script>