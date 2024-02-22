@extends('admin.layout.template')

@section('title', 'Retur Meyer')

@section('footer')
<script>
    $("#data_retur").load("{{ route('retur.meyer', ['key' => 'view']) }}");

    $("#tanggal_kirim").on('change', function() {
        var tanggal =   $("#tanggal_kirim").val() ;
        $("#data_retur").load("{{ route('retur.meyer', ['key' => 'view']) }}&tanggal=" + tanggal);
    })
</script>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col"><a href="{{ route('retur.index') }}"><i class="fa fa-arrow-left"></i> Kembali</a></div>
    <div class="col text-center">
        <b>RETUR MEYER</b>
    </div>
    <div class="col"></div>
</div>

<section class="panel">
    <div class="card-body">
        <div class="form-group">
            <label for="tanggal_kirim">Tanggal Kirim</label>
            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                @endif id="tanggal_kirim" value="{{ date('Y-m-d') }}" class="form-control">
        </div>
    </div>
</section>

<div id="data_retur"></div>
@endsection