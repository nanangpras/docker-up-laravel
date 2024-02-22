<div class="row">
    <div class="col">
        <div class="form-group">
            <label for="tanggal_mulai_order">Filter Tanggal Awal</label>
            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                @endif class="form-control" id="tanggal_mulai_order" value="{{ date('Y-m-d',strtotime('tomorrow')) }}">
        </div>
    </div>
    <div class="col">
        <div class="form-group">
            <label for="tanggal_akhir_order">Filter Tanggal Akhir</label>
            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                @endif class="form-control" id="tanggal_akhir_order" value="{{ date('Y-m-d',strtotime('tomorrow')) }}">
        </div>
    </div>
    {{-- <div class="col">
        <div class="form-group">
            <label for="field_order">Kolom</label>
            <select name="field" id="field_order" class="form-control tanggal">
                <option value="nama">Nama</option>
                <option value="qty">Qty/Pcs/Ekor</option>
                <option value="berat">Berat (Kg)</option>
                <option value="tanggal">Tanggal</option>
            </select>
        </div>
    </div>
    <div class="col">
        <div class="form-group">
            <label for="orderby_order">Order By</label>
            <select name="orderby" id="orderby_order" class="form-control tanggal">
                <option value="asc">Ascending</option>
                <option value="desc">Descending</option>
            </select>
        </div>
    </div> --}}
    <div class="col">
        <div class="form-group">
            <label for="sales">Sales</label>
            <select class="form-control select2" name="sales" id="sales">
                <option value="">Semua</option>
                <option value="117762">SONY</option>
                <option value="117759">SETYO</option>
                <option value="117674">IFAN</option>
                <option value="117786">MILAGRO</option>
                <option value="119822">ANDI</option>
                <option value="119821">SUBANDI</option>
            </select>
        </div>
    </div>
</div>
<div class="row">
    <div class="col">
        <div class="form-group">
            <div id="daftarordercust">

            </div>
        </div>
    </div>
    <div class="col">
        <div class="form-group">
            <label for="kategoricustomer">Kategori Customer</label>
            <select name="kategoricustomer" id="kategoricustomer" class="form-control select2">
                @php
                $customer = App\Models\Customer::where('kategori', '!=', NULL)->groupBy('kategori')->get();
                @endphp
                <option value="">Semua</option>
                @foreach ($customer as $cust)
                <option value="{{ $cust->kategori }}">{{ $cust->kategori }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col">
        <div class="form-group">
            <label for="areawilayah">Wilayah</label>
            <select name="areawilayah" id="areawilayah" class="form-control select2">
                @php
                $wilayah = App\Models\Order::select('wilayah')->where('wilayah', '!=', NULL)->orWhere('wilayah', '!=', '
                ')->groupBy('wilayah')->get();
                @endphp
                <option value="">Semua</option>
                @foreach ($wilayah as $area)
                <option value="{{ $area->wilayah }}">{{ $area->wilayah }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col">
        <div class="form-group">
            <label for="filter_stock_order">Pencarian</label>
            <input type="text" class="form-control" id="filter_stock_order" name="search" value="{{ $search ?? '' }}"
                placeholder="Cari..." autocomplete="off">
        </div>
    </div>
</div>

<div class="form-group">
    <input type="radio" id="input-semua" name="jenis" checked> <label for="input-semua">Semua</label> &nbsp
    <input type="radio" id="input-fresh" name="jenis"> <label for="input-fresh">Fresh</label> &nbsp
    <input type="radio" id="input-frozen" name="jenis"> <label for="input-frozen">Frozen</label>
</div>

{{-- <div class="col"> --}}
    <div class="form-group">
        <button type="submit" class="form-control btn btn-success" id="refreshTableDaftarOrder">Refresh Table</button>
    </div>
    {{--
</div> --}}

<h5 id="loading_summary" style="display: none" class="text-center"><i class="fa fa-refresh fa-spin"></i> Loading....
</h5>
<div id="warehouse-order">
</div>


<script>
    $('.select2').select2({
        theme: 'bootstrap4',
    })
</script>


<script>
    var hash = window.location.hash;
    if(hash === "#custom-tabs-three-order" || hash==='' || hash=== undefined){
        daftarorder();
    }
    $('#refreshTableDaftarOrder').on('click', function() {
        daftarorder();
    })

    $("#tanggal_mulai_order,#areawilayah,#kategoricustomer,#tanggal_akhir_order,#input-fresh,#input-frozen,#input-semua,#sales").on('change', function() {
        daftarorder();
    });

    $("#filter_stock_order").on('keyup', function() {
        daftarorder();
    }) ;

    function item_order(){
        daftarorder();
    }

    daftarorder();
    function daftarorder(){
        var mulai       =   $("#tanggal_mulai_order").val() ;
        var akhir       =   $("#tanggal_akhir_order").val() ;
        var filter      =   encodeURIComponent($("#filter_stock_order").val()) ;
        var fresh       =   $("#input-fresh:checked").val();
        var frozen      =   $("#input-frozen:checked").val();
        var semua       =   $("#input-semua:checked").val();
        let itemorder   =   encodeURIComponent($(".item_order").val());
        let sales       =   encodeURIComponent($("#sales").val());
        let katcustomer =   encodeURIComponent($("#kategoricustomer").val());
        let wilayah     =   encodeURIComponent($("#areawilayah").val());
        
        $("#loading_summary").attr('style', 'display: block') ;
        $('#daftarordercust').load("{{ route('warehouse.order') }}?tanggal_mulai=" + mulai + "&tanggal_akhir=" + akhir + "&fresh=" + fresh + "&frozen=" + frozen + "&semua=" + semua + "&filter=" + filter + "&key=daftarordercust" + "&itemorder=" + itemorder + "&sales=" + sales + "&katcustomer=" + katcustomer + "&wilayah=" + wilayah);
        $('#warehouse-order').load("{{ route('warehouse.order') }}?tanggal_mulai=" + mulai + "&tanggal_akhir=" + akhir + "&fresh=" + fresh + "&frozen=" + frozen + "&semua=" + semua + "&filter=" + filter + "&itemorder=" + itemorder + "&sales=" + sales + "&katcustomer=" + katcustomer + "&wilayah=" + wilayah, function() {
            $("#loading_summary").attr('style', 'display: none') ;
        }) ;
    }
</script>