@extends('admin.layout.template')

@section('title', 'Tukar Supplier')

@section('footer')
<script>
    $("#data_security").attr("style", "display: none") ;
$("#loading").attr("style", "display: block") ;
$("#data_security").load("{{ route('supplier.tukar', ['key' => 'view']) }}", function() {
    $("#data_security").attr("style", "display: block") ;
    $("#loading").attr("style", "display: none") ;
})

$("#tanggal_supplier").on('change', function() {
    var tanggal =   $("#tanggal_supplier").val() ;
    $("#data_security").attr("style", "display: none") ;
    $("#loading").attr("style", "display: block") ;
    $("#data_security").load("{{ route('supplier.tukar', ['key' => 'view']) }}&tanggal_supplier=" + tanggal, function() {
        $("#data_security").attr("style", "display: block") ;
        $("#loading").attr("style", "display: none") ;
    })
});
</script>
@endsection

@section('content')
<div class="my-4 row">
    <div class="col-md"><a href="{{ route('supplier.index') }}"><i class="fa fa-arrow-left"></i> Kembali</a></div>
    <div class="col-md-7 text-center"><b>TUKAR SUPPLIER</b></div>
    <div class="col-md"></div>
</div>

<section class="panel">
    <div class="card-body">
        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01" @endif
            value="{{ date("Y-m-d") }}" id="tanggal_supplier" class="form-control">
    </div>
</section>

<section class="panel">
    <div class="card-body p-2">
        <h5 id="loading" style="display: none"><i class="fa fa-refresh fa-spin"></i> Loading .....</h5>
        <div id="data_security"></div>
    </div>
</section>
@endsection