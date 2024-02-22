@extends('admin.layout.template')

@section('title', 'Laporan Fulfillment')

@section('content')
<div class="my-4 font-weight-bold text-center">Laporan Fulfillment</div>
<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col">
                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                    @endif class="form-control change-date" name="tanggal" id="pencarian" value="{{ $tanggal }}"
                    placeholder="Cari...">
            </div>
            <div class="col">
                <select name="status" id="status" class="form-control">
                    <option value="all">Semua</option>
                    <option value="kirim">Terkirim</option>
                    <option value="pending">Tidak Terkirim</option>
                </select>
            </div>
        </div>
    </div>
</div>

<div id="report-laporan"></div>
@endsection


@section('footer')
<script>
    $('#report-laporan').load("{{ route('dashboard.laporan') }}");

    $('#pencarian').on('change', function() {
        var tanggal =   $(this).val();
        var status  =   $('#status').val();
        $("#report-laporan").load("{{ url('admin/laporan?tanggal=') }}" + tanggal + "&status=" + status);
    })

    $('#status').on('change', function() {
        var tanggal =   $("#pencarian").val();
        var status  =   $(this).val();
        $("#report-laporan").load("{{ url('admin/laporan?tanggal=') }}" + tanggal + "&status=" + status);
    })
</script>
@endsection