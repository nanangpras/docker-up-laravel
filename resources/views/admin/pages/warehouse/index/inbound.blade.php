<div class="row">
    <div class="col">
        <div class="form-group">
            <label for="tanggal_mulai_inbound">Filter</label>
            @if (env('NET_SUBSIDIARY', 'CGL')=='CGL')
            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                @endif class="form-control" id="tanggal_mulai_inbound"
                value="{{ date('Y-m-d', strtotime('-7 days', time())) }}">
            {{-- <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                @endif class="form-control" id="tanggal_mulai_inbound"
                value="{{ date('Y-m-d', strtotime('-7 days', time())) }}" min="2023-05-27"> --}}
            @else
            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                @endif class="form-control" id="tanggal_mulai_inbound"
                value="{{ date('Y-m-d', strtotime('-7 days', time())) }}">
            {{-- <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                @endif class="form-control" id="tanggal_mulai_inbound"
                value="{{ date('Y-m-d', strtotime('-7 days', time())) }}" min="2023-05-05"> --}}
            @endif
        </div>
    </div>
    <div class="col">
        <div class="form-group">
            <label for="tanggal_akhir_inbound">&nbsp;</label>
            @if (env('NET_SUBSIDIARY', 'CGL')=='CGL')
            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                @endif class="form-control" id="tanggal_akhir_inbound"
                value="{{ date('Y-m-d', strtotime('-7 days', time())) }}">
            {{-- <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                @endif class="form-control" id="tanggal_akhir_inbound"
                value="{{ date('Y-m-d', strtotime('-7 days', time())) }}" min="2023-05-27"> --}}
            @else
            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                @endif class="form-control" id="tanggal_akhir_inbound"
                value="{{ date('Y-m-d', strtotime('-7 days', time())) }}">
            {{-- <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                @endif class="form-control" id="tanggal_akhir_inbound"
                value="{{ date('Y-m-d', strtotime('-7 days', time())) }}" min="2023-05-05"> --}}
            @endif
        </div>
    </div>
</div>
<div class="row">

    <input type="hidden" id="idRedirect" value="{{ $id ?? '' }}">
    <input type="hidden" id="searchRedirect" value="{{ $search ?? 'no' }}">
    <div class="col">
        <label for="item_inbound">Nama Item</label>
        <select class="form-control select2" id="item_inbound">
            <option value="">- Semua Item -</option>
            @foreach($list_item as $li)
            <option value="{{$li->id}}">{{$li->nama}}</option>
            @endforeach
        </select>
    </div>
    <div class="col">
        <label for="category_inbound">Kategori</label>
        <select class="form-control select2" id="category_inbound">
            <option value="">- Semua -</option>
            @foreach($list_category as $lc)
            @if($lc->nama != NULL)
            <option value="{{$lc->id}}">{{$lc->nama}}</option>
            @endif
            @endforeach
        </select>
    </div>
    <div class="col">
        <label for="marinated_inbound">Status M</label>
        <select class="form-control select2" id="marinated_inbound">
            <option value="">- Semua -</option>
            <option value="m">M</option>
            <option value="non">Non M</option>
        </select>
    </div>
    <div class="col">
        <label for="subitem_inbound">Sub Item</label>
        <select class="form-control select2" id="subitem_inbound">
            <option value="">- Semua -</option>
            @foreach($list_itemname as $key => $lin)
            @if($lin != NULL || $lin != "")
            <option value="{{ $lin }}">{{ $lin }}</option>
            @endif
            @endforeach
        </select>
    </div>
</div>
<div class="row mt-3">
    <div class="col">
        <label for="grade_inbound">Grade</label>
        <select class="form-control select2" id="grade_inbound">
            <option value="">- Semua -</option>
            <option value="grade a">Grade A</option>
            <option value="grade b">Grade B</option>
        </select>
    </div>
    <div class="col">
        <label for="customername_inbound">Customer</label>
        <select class="form-control select2" id="customername_inbound">
            <option value="">- Semua -</option>
            @foreach($list_customername as $key => $cst)
            @if($cst != NULL || $cst != "")
            <option value="{{ $cst }}">{{ App\Models\Customer::find($cst)->nama ?? "#" }}</option>
            @endif
            @endforeach
        </select>
    </div>
    <div class="col">
        <label for="ordering_inbound">Ordering</label>
        <select class="form-control select2" id="ordering_inbound">
            <option value="">- Urutkan Berdasar -</option>
            <option value="customer">Nama Customer</option>
            <option value="item">Nama Item</option>
            <option value="qty">Quantity</option>
            <option value="berat">Berat</option>
        </select>
    </div>
    <div class="col">
        <Label for="sort_by_inbound">Sort By</Label>
        <select class="form-control select2" id="sort_by_inbound">
            <option value="asc">ASC</option>
            <option value="desc">DESC</option>
        </select>
    </div>
</div>
<div class="row mt-3">
    <div class="col">
        <Label for="cari_inbound">Pencarian Kata</Label>
        <input type="text" placeholder="Cari..." id="cari_inbound" class="form-control">
    </div>
</div>
<hr>
<br>
<h5 id="spinerinbound" style="display: none" class="text-center"><i class="fa fa-refresh fa-spin"></i> Loading....</h5>
<div id="warehouse-masuk"></div>

<script>
    $('.select2').select2({
        theme: 'bootstrap4',
    });
    var DefaultTimeout = null;
     //STOCK INBOUND
    var hash = window.location.hash;
    if(hash === "#custom-tabs-three-masuk"){
        LoadDataInbound();
    }
    $("#custom-tabs-three-masuk-tab").on('click', function(){
        $('#searchRedirect').val('no')
        LoadDataInbound()
    });
  
    $("#tanggal_mulai_inbound,#tanggal_akhir_inbound,#item_inbound,#category_inbound,#marinated_inbound,#subitem_inbound").on('change', function() {
        $('#searchRedirect').val('no')
        LoadDataInbound()
    });
    $("#grade_inbound,#customername_inbound,#ordering_inbound,#sort_by_inbound").on("change", function () {
        $('#searchRedirect').val('no')
        LoadDataInbound();
    });

    $("#cari_inbound").on('keyup', function() {
        $('#searchRedirect').val('no')
        if (DefaultTimeout != null) {
            clearTimeout(DefaultTimeout);
        }
        DefaultTimeout = setTimeout(function() {
            DefaultTimeout = null;  
            LoadDataInbound()
        }, 1000);  
    });

    function LoadDataInbound(){
        $('.selectInbound').each(function() {
            $(this).select2({
            theme: 'bootstrap4',
            dropdownParent: $(this).parent()
            });
        })
        $('#spinerinbound').show();
        $("#warehouse-masuk").hide();

        var mulai               =   $("#tanggal_mulai_inbound").val() ;
        var akhir               =   $("#tanggal_akhir_inbound").val() ;
        var lokasi              =   $("#lokasi_gudang_inbound").val() ;
        var filter              =   encodeURIComponent($("#cari_inbound").val()) ;
        var nama_item           =   $("#item_inbound").val() ;
        var kategori            =   $("#category_inbound").val();
        var marinasi            =   $("#marinated_inbound").val();
        var sub_item            =   $("#subitem_inbound").val();
        var grade               =   $("#grade_inbound").val() ;
        var customer            =   $("#customername_inbound").val();
        var orderby             =   $("#ordering_inbound").val();
        var sortby              =   $("#sort_by_inbound").val();
        var idRedirect          =   $("#idRedirect").val();
        var searchRedirect      =   $("#searchRedirect").val();

        // console.log(filter)
        $.ajax({
            url : "{{ route('warehouse.inout') }}",
            method: "GET",
            data :{
                'tanggal_mulai' : mulai,
                'tanggal_akhir' : akhir,
                'filter'        : filter,
                'nama_item'     : nama_item,
                'kategori'      : kategori,
                'marinasi'      : marinasi,
                'sub_item'      : sub_item,
                'grade'         : grade,
                'customer'      : customer,
                'orderby'       : orderby,
                'sortby'        : sortby,
                'jenis'         : 'warehouse_masuk',
                'id'            : idRedirect,
                'search'        : searchRedirect

            },
            success: function(data){
                $("#warehouse-masuk").html(data);
                $("#warehouse-masuk").show();
                $('#spinerinbound').hide();
            }
        });
    }
</script>