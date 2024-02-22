@extends('admin.layout.template')

@section('title', 'Warehouse')

@section('footer')
<script>
    $('.select2').select2({
        theme: 'bootstrap4'
    });
        $("#warehouse-stock").load("{{ route('warehouse.data_filter') }}") ;
</script>
<script>
    $('#tanggal_akhir').change(function(){
        let gudang = $('#lokasi_gudang').val()
        let tanggal = $('#tanggal_akhir').val()
        let filter  =   encodeURIComponent($("#filter_stock").val())
        $("#warehouse-stock").load("{{ route('warehouse.data_filter') }}?tanggal_akhir="+ tanggal + "&gudang="+ gudang + "&filter=" + filter) ;
    })

    $('#lokasi_gudang').change(function() {
        let gudang = $('#lokasi_gudang').val()
        let tanggal = $('#tanggal_akhir').val()
        let filter  =   encodeURIComponent($("#filter_stock").val())
        $("#warehouse-stock").load("{{ route('warehouse.data_filter') }}?tanggal_akhir="+ tanggal + "&gudang="+ gudang + "&filter=" + filter) ;
    })

    $("#filter_stock").on('keyup', function(){
        let gudang = $('#lokasi_gudang').val()
        let tanggal = $('#tanggal_akhir').val()
        let filter  =   encodeURIComponent($("#filter_stock").val())
        $("#warehouse-stock").load("{{ route('warehouse.data_filter') }}?tanggal_akhir="+ tanggal + "&gudang="+ gudang + "&filter=" + filter) ;
    })
</script>
@endsection

@section('content')

<div class="text-center my-4 font-weight-bold">WAREHOUSE FILTERING</div>

<section class="panel">
    <div class="card-body">
        Pencarian Tanggal
        <div class="row">
            <div class="col pr-1">
                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                    @endif class="form-control" id="tanggal_akhir" value="{{ date("Y-m-d") }}">
            </div>
            <div class="col px-1">
                <input type="text" class="form-control" id="filter_stock" name="search" value="{{ $search ?? '' }}"
                    placeholder="Cari..." autocomplete="off">
            </div>
            <div class="col pl-1">
                <select class="form-control select2" name="gudang_id" id="lokasi_gudang">
                    <option value="">Semua</option>
                    @foreach($gudang as $g)
                    <option value="{{$g->id}}">{{$g->code}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</section>

<section class="panel">
    <div class="card-body card-tabs">
        <div class="tab-content" id="custom-tabs-three-tabContent">
            {{-- Stock Akhir --}}
            <div class="tab-pane fade show active" id="custom-tabs-three-stock" role="tabpanel"
                aria-labelledby="custom-tabs-three-stock-tab">
                {{-- <div class="mb-3" id="subitem_select"></div> --}}
                <div id="warehouse-stock"></div>
            </div>
        </div>
    </div>
</section>

@stop