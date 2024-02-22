<div class="row">
    <div class="col-md-4 col-sm-4 col-xs-6">
        <label for="tanggalawal">Pencarian Tanggal Awal</label>
        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01" @endif
            class="form-control" id="tanggalawal" name="tanggal" value="{{ $tanggal }}" placeholder="Cari...">
    </div>
    <div class="col-md-4 col-sm-4 col-xs-6">
        <label for="tanggalakhir">Pencarian Tanggal Akhir</label>
        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01" @endif
            class="form-control" id="tanggalakhir" name="tanggalend" value="{{ $tanggalend }}" placeholder="Cari...">
    </div>
    <div class="col-md-4 col-sm-4 col-xs-6">
        <label for="jenis_report">Jenis Report</label>
        <select name="report" id="jenis_report" class="form-control">
            <option value="all" {{ $request->report == 'all' ? 'selected' : '' }}>Semua</option>
            <option value="po_lb" {{ $request->report == 'po_lb' ? 'selected' : '' }}>PO LB</option>
            <option value="non_lb" {{ $request->report == 'non_lb' ? 'selected' : '' }}>PO Non LB</option>
        </select>
    </div>
</div>

<div id="loading" class="text-center">
    <img src="{{ asset('loading.gif') }}" style="width: 18px;"> Loading ...
</div>
<div id="tampildataqcumum"></div>

<script>
    $('#tanggalawal,#tanggalakhir,#jenis_report').change(function() {
        setTimeout(() => {
            loadqcumum();
        },1000);
    });

    function loadqcumum(){
        var tanggalawal     = $("#tanggalawal").val();
        var tanggalakhir    = $("#tanggalakhir").val();
        var report          = $("#jenis_report").val();

        $.ajax({
            url         : "{{ route('laporan.qc') }}",
            method      : "GET",
            cache       : false,
            data        : {
                key         : 'qcumum',
                tanggal     : tanggalawal,
                tanggalend  : tanggalakhir,
                report      : report
            },
            beforeSend  : function(){
                $('#loading').show();
            },
            success: function(data){
                $("#tampildataqcumum").html(data)
                $('#loading').hide();
            }

        })
    }
</script>