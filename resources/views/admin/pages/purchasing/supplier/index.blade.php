@extends('admin.layout.template')

@section('title', 'Data Supplier')

@section('footer')
<script>
    $('.select2').select2({
    theme: 'bootstrap4',
})
</script>

<script>
    $("#supplier").on('change', function() {
        var supplier    =   $("#supplier").val() ;
        var awal        =   $("#tanggal_awal").val() ;
        var akhir       =   $("#tanggal_akhir").val() ;
        $("#view_data").load("{{ route('purchasing.supplier', ['key' => 'view']) }}&supplier=" + supplier + "&awal=" + awal + "&akhir=" + akhir);
    });

    $("#tanggal_awal").on('change', function() {
        var supplier    =   $("#supplier").val() ;
        var awal        =   $("#tanggal_awal").val() ;
        var akhir       =   $("#tanggal_akhir").val() ;
        $("#view_data").load("{{ route('purchasing.supplier', ['key' => 'view']) }}&supplier=" + supplier + "&awal=" + awal + "&akhir=" + akhir);
    });

    $("#tanggal_akhir").on('change', function() {
        var supplier    =   $("#supplier").val() ;
        var awal        =   $("#tanggal_awal").val() ;
        var akhir       =   $("#tanggal_akhir").val() ;
        $("#view_data").load("{{ route('purchasing.supplier', ['key' => 'view']) }}&supplier=" + supplier + "&awal=" + awal + "&akhir=" + akhir);
    });
</script>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col text-primary"><a href="{{ route('purchasing.index') }}"><i class="fa fa-arrow-left"></i> Kembali</a>
    </div>
    <div class="col text-center"><b>Data Supplier</b></div>
    <div class="col"></div>
</div>

<section class="panel">
    <div class="card-body">
        <div class="row">
            <div class="col pr-1">
                <select id="supplier" class="form-control select2" data-placeholder="Pilih Supplier" data-width="100%">
                    <option value=""></option>
                    @foreach ($supplier as $row)
                    <option value="{{ $row->id }}">{{ $row->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col px-1">
                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                    @endif id="tanggal_awal" value="{{ date("Y-m-d") }}" class="form-control">
            </div>
            <div class="col pl-1">
                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                    @endif id="tanggal_akhir" value="{{ date("Y-m-d") }}" class="form-control">
            </div>
        </div>
    </div>
</section>

<div id="view_data"></div>
@endsection