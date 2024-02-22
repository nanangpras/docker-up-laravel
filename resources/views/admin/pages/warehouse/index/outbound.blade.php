<div class="row">
    <div class="col">
        <div class="form-group">
            <label for="tanggal_mulai_outbound">Filter outbond</label>
            @if (env('NET_SUBSIDIARY', 'CGL')=='CGL')
            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                @endif class="form-control" id="tanggal_mulai_outbound" value="{{ $tanggal_mulai }}">
            {{-- <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                @endif class="form-control" id="tanggal_mulai_outbound" value="{{ $tanggal_mulai }}" min="2023-05-27">
            --}}
            @else
            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                @endif class="form-control" id="tanggal_mulai_outbound" value="{{ $tanggal_mulai }}">
            {{-- <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                @endif class="form-control" id="tanggal_mulai_outbound" value="{{ $tanggal_mulai }}" min="2023-05-05">
            --}}
            @endif
        </div>
    </div>
    <div class="col">
        <div class="form-group">
            <label for="tanggal_akhir_outbound">&nbsp;</label>
            @if (env('NET_SUBSIDIARY', 'CGL')=='CGL')
            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                @endif class="form-control" id="tanggal_akhir_outbound" value="{{ $tanggal_akhir }}">
            {{-- <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                @endif class="form-control" id="tanggal_akhir_outbound" value="{{ $tanggal_akhir }}" min="2023-05-27">
            --}}
            @else
            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                @endif class="form-control" id="tanggal_akhir_outbound" value="{{ $tanggal_akhir }}">
            {{-- <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                @endif class="form-control" id="tanggal_akhir_outbound" value="{{ $tanggal_akhir }}" min="2023-05-05">
            --}}
            @endif
        </div>
    </div>
</div>
<div class="row">
    <div class="col">
        <label for="item_inout">Nama Item</label>
        <select class="form-control select2" id="item_inout">
            <option value="">- Semua Item -</option>
            @foreach($list_item as $li)
            <option value="{{$li->id}}">{{$li->nama}}</option>
            @endforeach
        </select>
    </div>
    <div class="col">
        <label for="category_inout">Kategori</label>
        <select class="form-control select2" id="category_inout">
            <option value="">- Semua -</option>
            @foreach($list_category as $lc)
            @if($lc->nama != NULL)
            <option value="{{$lc->id}}">{{$lc->nama}}</option>
            @endif
            @endforeach
        </select>
    </div>
    <div class="col">
        <label for="marinated_inout">Status M</label>
        <select class="form-control select2" id="marinated_inout">
            <option value="">- Semua -</option>
            <option value="(M)">M</option>
            <option value="non">Non M</option>
        </select>
    </div>
    <div class="col">
        <label for="subitem_inout">Sub Item</label>
        <select class="form-control select2" id="subitem_inout">
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
        <label for="grade_inout">Grade</label>
        <select class="form-control select2" id="grade_inout">
            <option value="">- Semua -</option>
            <option value="grade a">Grade A</option>
            <option value="grade b">Grade B</option>
        </select>
    </div>
    <div class="col">
        <label for="customername_inout">Customer</label>
        <select class="form-control select2" id="customername_inout">
            <option value="">- Semua -</option>
            @foreach($list_customername as $key => $cst)
            @if($cst != NULL || $cst != "")
            <option value="{{ $cst }}">{{ App\Models\Customer::find($cst)->nama ?? "#" }}</option>
            @endif
            @endforeach
        </select>
    </div>
    <div class="col">
        <label for="ordering_inout">Ordering</label>
        <select class="form-control select2" id="ordering_inout">
            <option value="">- Urutkan Berdasar -</option>
            <option value="customer">Nama Customer</option>
            <option value="item">Nama Item</option>
            <option value="qty">Quantity</option>
            <option value="berat">Berat</option>
        </select>
    </div>
    <div class="col">
        <Label for="sort_by_inout">Sort By</Label>
        <select class="form-control select2" id="sort_by_inout">
            <option value="asc">ASC</option>
            <option value="desc">DESC</option>
        </select>
    </div>
</div>
<div class="row mt-3">
    <div class="col">
        <Label for="cari_inout">Pencarian Kata</Label>
        <input type="text" placeholder="Cari..." id="cari_inout" class="form-control">
    </div>
</div>
<hr>
<br>
<h5 id="spineroutbound" style="display: none" class="text-center"><i class="fa fa-refresh fa-spin"></i> Loading....</h5>
<div id="warehouse-keluar"></div>


<script>
    var DefaultTimeout = null;

    //STOCK OUTBOUND
    var hash = window.location.hash;
    if(hash === "#custom-tabs-three-keluar"){
        LoadDataOutbound();
    }
    $("#custom-tabs-three-keluar-tab").on('click', function(){
        LoadDataOutbound()
    });
    $("#tanggal_mulai_outbound,#tanggal_akhir_outbound,#item_inout,#category_inout,#marinated_inout,#subitem_inout").on('change', function() {
        LoadDataOutbound()
    });
    $("#grade_inout,#customername_inout,#ordering_inout,#sort_by_inout").on("change", function () {
        LoadDataOutbound();
    });
    $("#cari_inout").on('keyup', function() {
        if (DefaultTimeout != null) {
            clearTimeout(DefaultTimeout);
        }
        DefaultTimeout = setTimeout(function() {
            DefaultTimeout = null;  
            LoadDataOutbound()
        }, 1000);  
        // $("#warehouse-keluar").load("{{ route('warehouse.masuk') }}?tanggal_mulai=" + mulai + "&tanggal_akhir=" + akhir + "&lokasi=" + lokasi + "&filter=" + filter + "&field=" + field + "&orderby=" + orderby + "&plastik=" + plastik) ;
    });

    function LoadDataOutbound(){
        $('#spineroutbound').show();
        $("#warehouse-keluar").hide();

        var mulai       =   $("#tanggal_mulai_outbound").val() ;
        var akhir       =   $("#tanggal_akhir_outbound").val() ;
        var lokasi      =   $("#lokasi_gudang_outbound").val() ;
        var filter      =   encodeURIComponent($("#cari_inout").val());
        var nama_item   =   $("#item_inout").val();
        var kategori    =   $("#category_inout").val();
        var marinasi    =   $("#marinated_inout").val();
        var sub_item    =   $("#subitem_inout").val();
        var grade       =   $("#grade_inout").val() ;
        var customer    =   $("#customername_inout").val();
        var orderby     =   $("#ordering_inout").val();
        var sortby     =    $("#sort_by_inout").val();

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
                'jenis'         : 'warehouse_keluar'
            },
            success: function(data){
                $("#warehouse-keluar").html(data);
                $("#warehouse-keluar").show();
                $('#spineroutbound').hide();
            }
        });
    }
</script>