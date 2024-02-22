@extends('admin.layout.template')

@section('title', 'Warehouse')

@section('footer')
<script>
    $('.select2').select2({
        theme: 'bootstrap4'
    });

    $('#loading_filter').attr('style', 'display: block') ;
    $("#warehouse-all-filter").load("{{ route('warehouse.showstock', ['key' => 'allFilter']) }}", function() {
        $('#loading_filter').attr('style', 'display: none') ;
    });

</script>

<script>
    $('#tahun').change(function() {
        let gudang = $('#lokasi_gudang').val()
        let tahun = $('#tahun').val()
        let bulan = $('#bulan').val()
        let filter = encodeURIComponent($("#filter_stock").val())
        $('#loading_filter').attr('style', 'display: block') ;
        $("#warehouse-all-filter").load("{{ route('warehouse.showstock', ['key' => 'allFilter']) }}&gudang=" + gudang + "&filter=" + filter + "&key=allFilter" + "&bulan=" + bulan + "&tahun=" + tahun, function() {
            $('#loading_filter').attr('style', 'display: none') ;
        });
    })
    $('#bulan').change(function() {
        let gudang = $('#lokasi_gudang').val()
        let tahun = $('#tahun').val()
        let bulan = $('#bulan').val()
        let filter = encodeURIComponent($("#filter_stock").val())
        $('#loading_filter').attr('style', 'display: block') ;
        $("#warehouse-all-filter").load("{{ route('warehouse.showstock', ['key' => 'allFilter']) }}&gudang=" + gudang + "&filter=" + filter + "&key=allFilter" + "&bulan=" + bulan + "&tahun=" + tahun, function() {
            $('#loading_filter').attr('style', 'display: none') ;
        });
    })

    $('#lokasi_gudang').change(function() {
        let gudang = $('#lokasi_gudang').val()
        let tahun = $('#tahun').val()
        let bulan = $('#bulan').val()
        let filter = encodeURIComponent($("#filter_stock").val())
        $('#loading_filter').attr('style', 'display: block') ;
        $("#warehouse-all-filter").load("{{ route('warehouse.showstock', ['key' => 'allFilter']) }}&gudang=" + gudang + "&filter=" + filter + "&key=allFilter" + "&bulan=" + bulan + "&tahun=" + tahun, function() {
            $('#loading_filter').attr('style', 'display: none') ;
        });
    })

    $("#filter_stock").on('keyup', function() {
        let gudang = $('#lokasi_gudang').val()
        let tahun = $('#tahun').val()
        let bulan = $('#bulan').val()
        let filter = encodeURIComponent($("#filter_stock").val())
        $('#loading_filter').attr('style', 'display: block') ;
        $("#warehouse-all-filter").load("{{ route('warehouse.showstock', ['key' => 'allFilter']) }}&gudang=" + gudang + "&filter=" + filter + "&key=allFilter" + "&bulan=" + bulan + "&tahun=" + tahun, function() {
            $('#loading_filter').attr('style', 'display: none') ;
        });
    })

</script>
@endsection

@section('content')
<div class="row my-4">
    <div class="col"><a href="{{ route('warehouse.index') }}"><i class="fa fa-arrow-left"></i> Kembali</a></div>
    <div class="col text-center font-weight-bold">WAREHOUSE STOCK</div>
    <div class="col"></div>
</div>

<section class="panel">
    <div class="card-body">
        <div class="row">
            <div class="col-sm-3">
                <select class="form-control selectpicker" id="tahun" name="tahun" data-width="100px">
                    <?php  for ($year = (int)date('Y'); 2021 <= $year; $year--) : ?>
                    <option value="{{ $year }}">{{ $year }}</option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-sm-3 ">
                <select class="form-control selectpicker" id="bulan" name="bulan" data-width="120px">
                    <option value="01" @if ($bulan=="01" ) selected @endif>Januari</option>
                    <option value="02" @if ($bulan=="02" ) selected @endif>Februari</option>
                    <option value="03" @if ($bulan=="03" ) selected @endif>Maret</option>
                    <option value="04" @if ($bulan=="04" ) selected @endif>April</option>
                    <option value="05" @if ($bulan=="05" ) selected @endif>Mei</option>
                    <option value="06" @if ($bulan=="06" ) selected @endif>Juni</option>
                    <option value="07" @if ($bulan=="07" ) selected @endif>Juli</option>
                    <option value="08" @if ($bulan=="08" ) selected @endif>Agustus</option>
                    <option value="09" @if ($bulan=="09" ) selected @endif>September</option>
                    <option value="10" @if ($bulan=="10" ) selected @endif>Oktober</option>
                    <option value="11" @if ($bulan=="11" ) selected @endif>November</option>
                    <option value="12" @if ($bulan=="12" ) selected @endif>Desember</option>
                </select>
            </div>
            <div class="col px-1">
                <input type="text" class="form-control" id="filter_stock" name="search" value="{{ $search ?? '' }}" placeholder="Cari..." autocomplete="off">
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
            <h5 style="display: none" id="loading_filter"><i class="fa fa-refresh fa-spin"></i> Loading</h5>
            <div id="warehouse-all-filter"></div>
        </div>
    </div>
</section>

@stop
