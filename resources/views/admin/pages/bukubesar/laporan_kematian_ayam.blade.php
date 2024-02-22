<div class="row">
    <div class="col-md-6 col-sm-6 col-xs-6">
        <label for="startdateqc">Pencarian Tanggal Awal</label>
        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01" @endif
            class="form-control " name="startdateqc" id="startdateqc" value="{{ $tanggalawal }}"
            placeholder="Tanggal Awal...">
    </div>
    <div class="col-md-6 col-sm-6 col-xs-6">
        <label for="enddateqc">Pencarian Tanggal Akhir</label>
        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01" @endif
            class="form-control " name="enddateqc" id="enddateqc" value="{{ $tanggalakhir }}"
            placeholder="Tanggal Akhir...">
    </div>
</div>

<div id="loadingkematianayam" class="text-center">
    <img src="{{ asset('loading.gif') }}" style="width: 18px;"> Loading ...
</div>
<div id="tampildatakematianayam"></div>

<script>
    $('#startdateqc,#enddateqc').change(function() {
        setTimeout(() => {
            loadkematianayam();
        },1000);
    });

    function loadkematianayam(){
        var startdateqc     = $("#startdateqc").val();
        var enddateqc       = $("#enddateqc").val();

        $.ajax({
            url         : "{{ route('laporan.qc') }}",
            method      : "GET",
            cache       : false,
            data        : {
                key         : 'kematianayam',
                startdateqc : startdateqc,
                enddateqc   : enddateqc,
            },
            beforeSend  : function(){
                $('#loadingkematianayam').show();
            },
            success: function(data){
                $("#tampildatakematianayam").html(data)
                $('#loadingkematianayam').hide();
            }

        })
    }

</script>