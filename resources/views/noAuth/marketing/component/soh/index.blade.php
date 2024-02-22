<div class="">
    <div class="col-12">
        <div class="row">
            <div class="col-lg-6 col-6">
                Pencarian Tanggal Awal
                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                    @endif class="form-control" id="tanggalawal" name="tanggalawal"
                    value="{{ $tanggal ? $tanggal : date('Y-m-d') }}">
            </div>
            <div class="col-lg-6 col-6">
                Pencarian Tanggal Akhir
                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                    @endif class="form-control" id="tanggalakhir" name="tanggalakhir"
                    value="{{ $tanggalakhir ? $tanggalakhir : date('Y-m-d') }}">
            </div>
        </div>

    </div>
    <div id="spinerdatastockbyitem" class="text-center mt-4 mb-2">
        <img src="{{ asset('loading.gif') }}" style="width: 30px;">
    </div>
    <div id="datastockbyitem" class="mt-4"></div>
</div>
<script>
    $('#tanggalawal, #tanggalakhir').change(function() {
        var tanggalawal     =   $('#tanggalawal').val();
        var tanggalakhir    =   $('#tanggalakhir').val();
        setTimeout(function(){
            loaddatastockbyitem();
        },1000)
    });

    function loaddatastockbyitem(){
        var tanggalawal     =   $('#tanggalawal').val();
        var tanggalakhir    =   $('#tanggalakhir').val();
        $("#spinerdatastockbyitem").show()
        $.ajax({
            url: "{{ route('view_progress') }}",
            method: "GET",
            data:{
                'role'          : 'marketing',
                'key'           : 'marketing',
                '_token'        : "{{ $tToken }}",
                'name'          : "{{ $subsidiary }}",
                'GenerateToken' : "{{ $gettoken }}",
                'subkey'        : 'view_data_stockbyitem',
                'tanggalawal'   : tanggalawal,
                'tanggalakhir'  : tanggalakhir
            },
            success: function(data){
                $("#datastockbyitem").html(data);
                $("#spinerdatastockbyitem").hide()
            }
        });
    }
</script>
