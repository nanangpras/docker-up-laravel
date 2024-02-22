@extends('admin.layout.template')

@section('title', 'Data Supplier')

@section('footer')
<script>
$("#loading").attr("style", "display: block") ;
$("#supplier_view").load("{{ route('supplier.index', ['key' => 'view']) }}", function() {
    $("#loading").attr("style", "display: none") ;
});

$("#cari").on('keyup', function() {
    var cari    =   encodeURIComponent($("#cari").val()) ;
    $("#loading").attr("style", "display: block") ;
    $("#supplier_view").load("{{ route('supplier.index', ['key' => 'view']) }}&cari=" + cari, function() {
        $("#loading").attr("style", "display: none") ;
    });
})
</script>
@endsection

@section('content')
<div class="my-4 row">
    <div class="col-md"></div>
    <div class="col-md-7 text-center"><b>Data Supplier</b></div>
    <div class="col-md text-right"><a href="{{ route('supplier.tukar') }}" class="btn btn-success">Tukar Supplier</a></div>
</div>

<section class="panel">
    <div class="card-body p-2">
        <div class="row">
            <div class="col">
                <input type="text" placeholder="Cari..." autocomplete="off" id="cari" class="form-control">
            </div>
        </div>
    </div>
</section>

<section class="panel">
    <div class="card-body p-2">
        <h5 id="loading" style="display: none"><i class="fa fa-refresh fa-spin"></i> Loading.....</h5>
        <div id="supplier_view"></div>
    </div>
</section>
@stop
