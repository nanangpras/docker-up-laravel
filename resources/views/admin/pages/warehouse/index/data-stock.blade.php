<section class="panel">
    <div class="card-body">
        <div class="row">
            <div class="col-sm-3">
                <div class="form-group">
                    <label for="tahun_stock">Tahun</label>
                    <select class="form-control selectpicker" id="tahun_stock" name="tahun" data-width="100px">
                        <?php  for ($year = (int)date('Y'); 2021 <= $year; $year--) : ?>
                        <option value="{{ $year }}">{{ $year }}</option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>
            <div class="col-sm-3 ">
                <div class="form-group">
                    <label for="bulan_stock">Bulan</label>
                    <select class="form-control selectpicker" id="bulan_stock" name="bulan" data-width="120px">
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
            </div>
            <div class="col px-1">
                <div class="form-group">
                    <label for="filter_stock">Pencarian</label>
                    <input type="text" class="form-control" id="filter_stock" name="search" value="{{ $search ?? '' }}" placeholder="Cari..." autocomplete="off">
                </div>
            </div>
            <div class="col pl-1">
                <div class="form-group">
                    <label for="lokasi_gudang">Gudang</label>
                    <select class="form-control select2" name="gudang_id" id="lokasi_gudang">
                        <option value="">Semua</option>
                        @foreach($gudang as $g)
                        <option value="{{$g->id}}">{{$g->code}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="card-body">
    <h5 style="display: none" id="loading-stock-new"  class="text-center"><i class="fa fa-refresh fa-spin"></i> Loading</h5>
    <div id="warehouse-all-filter"></div>
</div>

<script>
    var hash = window.location.hash;
    if(hash === "#custom-tabs-three-datastock" || hash==='' || hash=== undefined){
        $('#loading-stock-new').attr('style', 'display: block') ;
        $("#warehouse-all-filter").load("{{ route('warehouse.showstock', ['key' => 'allFilter']) }}", function() {
            $('#loading-stock-new').attr('style', 'display: none') ;
        });
    }
</script>

<script>
    $('#tahun_stock').change(function() {
        let gudang = $('#lokasi_gudang').val()
        let tahun = $('#tahun_stock').val()
        let bulan = $('#bulan_stock').val()
        let filter = encodeURIComponent($("#filter_stock").val())
        $('#loading-stock-new').attr('style', 'display: block') ;
        $("#warehouse-all-filter").load("{{ route('warehouse.showstock', ['key' => 'allFilter']) }}&gudang=" + gudang + "&filter=" + filter + "&key=allFilter" + "&bulan=" + bulan + "&tahun=" + tahun, function() {
            $('#loading-stock-new').attr('style', 'display: none') ;
        });
    })
    $('#bulan_stock').change(function() {
        let gudang = $('#lokasi_gudang').val()
        let tahun = $('#tahun_stock').val()
        let bulan = $('#bulan_stock').val()
        let filter = encodeURIComponent($("#filter_stock").val())
        $('#loading-stock-new').attr('style', 'display: block') ;
        $("#warehouse-all-filter").load("{{ route('warehouse.showstock', ['key' => 'allFilter']) }}&gudang=" + gudang + "&filter=" + filter + "&key=allFilter" + "&bulan=" + bulan + "&tahun=" + tahun, function() {
            $('#loading-stock-new').attr('style', 'display: none') ;
        });
    })

    $('#lokasi_gudang').change(function() {
        let gudang = $('#lokasi_gudang').val()
        let tahun = $('#tahun_stock').val()
        let bulan = $('#bulan_stock').val()
        let filter = encodeURIComponent($("#filter_stock").val())
        $('#loading-stock-new').attr('style', 'display: block') ;
        $("#warehouse-all-filter").load("{{ route('warehouse.showstock', ['key' => 'allFilter']) }}&gudang=" + gudang + "&filter=" + filter + "&key=allFilter" + "&bulan=" + bulan + "&tahun=" + tahun, function() {
            $('#loading-stock-new').attr('style', 'display: none') ;
        });
    })

    $("#filter_stock").on('keyup', function() {
        let gudang = $('#lokasi_gudang').val()
        let tahun = $('#tahun_stock').val()
        let bulan = $('#bulan_stock').val()
        let filter = encodeURIComponent($("#filter_stock").val())
        $('#loading-stock-new').attr('style', 'display: block') ;
        $("#warehouse-all-filter").load("{{ route('warehouse.showstock', ['key' => 'allFilter']) }}&gudang=" + gudang + "&filter=" + filter + "&key=allFilter" + "&bulan=" + bulan + "&tahun=" + tahun, function() {
            $('#loading-stock-new').attr('style', 'display: none') ;
        });
    })

</script>