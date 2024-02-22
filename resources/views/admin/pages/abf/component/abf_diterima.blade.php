<div class="row">
    <div class="col">
        <div class="form-group">
            <label for="abf_diterima_tanggal">Filter Tanggal Masuk</label>
            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                @endif name="tanggal" class="form-control tanggal" id="abf_diterima_tanggal"
                value="{{ date('Y-m-d', strtotime('-7 days', time())) }}" autocomplete="off">
        </div>
        <input type="checkbox" id="tglprodacc">
        <label for="tglprodacc">Filter Tanggal Dibuat</label>
    </div>
    <div class="col">
        <div class="form-group">
            <label for="abf_diterima_tanggalend">&nbsp;</label>
            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                @endif name="tanggal" class="form-control tanggal" id="abf_diterima_tanggalend"
                value="{{ date('Y-m-d') }}" autocomplete="off">
        </div>
    </div>
    <div class="col">
        <div class="form-group">
            <label for="abf_diterima_field">Kolom</label>
            <select name="abf_diterima_field" id="abf_diterima_field" class="form-control filter">
                <option value="item_name">Item</option>
                <option value="stock_qty">Qty</option>
                <option value="stock_berat">Berat</option>
            </select>
        </div>
    </div>
    <div class="col">
        <div class="form-group">
            <label for="orderby">Order By</label>
            <select name="abf_diterima_orderby" id="abf_diterima_orderby" class="form-control filter">
                <option value="asc">Ascending</option>
                <option value="desc">Descending</option>
            </select>
        </div>
    </div>
    <div class="col">
        <div class="form-group">
            <label for="abf_diterima_status_acc">Status</label>
            <select name="abf_diterima_status_acc" id="abf_diterima_status_acc" class="form-control filter">
                <option value="0">Pilih</option>
                <option value="1">Pending</option>
                <option value="2">Selesai</option>
            </select>
        </div>
    </div>
</div>

<div class="row">
    <div class="col">
        <div class="form-group">
            <label for="kategori">Kategori</label>
            @php
            $kat = App\Models\Category::where('id', '<=', 20)->get();
                @endphp
                <select name="abf_diterima_kategori" id="abf_diterima_kategori"
                    class="form-control abf_diterima_kategori">
                    <option value="0">- Semua Kategori -</option>
                    @foreach ($kat as $k)
                    <option value="{{ $k->id }}">{{ $k->nama }}</option>
                    @endforeach
                </select>
        </div>
    </div>
    <div class="col">
        <div class="form-group">
            <label for="cari_data">Pencarian</label>
            <input type="text" autocomplete="off" placeholder="Cari..." id="abf_diterima_cari_data"
                class="form-control">
        </div>
    </div>
    <div class="col">

        <div class="form-group">
            <label for="kategori">Asal</label>
            <select name="abf_asal_tujuan" id="abf_asal_tujuan" class="form-control abf_asal_tujuan">
                <option value="0">- Semua Asal -</option>
                <option value="abf">Abf</option>
                <option value="retur">Retur</option>
                <option value="open_balance">Open Balance</option>
                <option value="order_karkas">Order Karkas</option>
                <option value="kepala_produksi">Produksi</option>
            </select>
            </select>
        </div>
    </div>
</div>

@if (env('NET_SUBSIDIARY', 'CGL') == 'CGL')
<input type="checkbox" id="non-abf">
<label for="non-abf">Non ABF (Chiller FG)</label>
@endif

@if (env('NET_SUBSIDIARY', 'CGL') == 'EBA')
<span class="ml-3">
    <input type="checkbox" id="abf_diterima_selonjor">
    <label for="selonjor">Selonjor</label>
</span>
<span class="ml-3">
    <input type="checkbox" id="abf_diterima_boneless">
    <label for="boneless">Boneless</label>
</span>
<span class="ml-3">
    <input type="checkbox" id="abf_diterima_parting">
    <label for="parting">Parting</label>
</span>
<span class="ml-3">
    <input type="checkbox" id="abf_diterima_memar">
    <label for="memar">Memar</label>
</span>
@endif

<div class="row">
    <div class="col">
        <div id="spinerabfoutbound" class="text-center" style="display: block">
            <img src="{{ asset('loading.gif') }}" width="30px">
        </div>
        <div class="mt-3" id="abf_diterima_view"></div>
    </div>
</div>

<script>
    var hashOutbound = window.location.hash.substr(1);
    if (hashOutbound === '#custom-tab-diterima') {
        load_abf_diterima();
    }

    $("#custom-tabs-diterima-tab").click(function() {
        load_abf_diterima();
    });

    $('#abf_diterima_non-abf,#abf_diterima_selonjor,#abf_diterima_tanggal,#abf_diterima_tanggalend,#abf_diterima_boneless,#abf_diterima_parting,#abf_diterima_memar,#abf_diterima_kategori,#tglprodacc,.filter,#abf_asal_tujuan')
        .on('change', function() {
            load_abf_diterima();
        });

    $('#abf_diterima_cari_data').on('keyup', function() {
        load_abf_diterima();
    });

    function load_abf_diterima() {
        $("#spinerabfoutbound").show()
        var tanggal = $("#abf_diterima_tanggal").val();
        var tanggalend = $("#abf_diterima_tanggalend").val();
        var non_abf = $('#abf_diterima_non-abf').is(':checked');
        var selonjor = $('#abf_diterima_selonjor').is(':checked');
        var boneless = $('#abf_diterima_boneless').is(':checked');
        var parting = $('#abf_diterima_parting').is(':checked');
        var memar = $('#abf_diterima_memar').is(':checked');
        var field = $("#abf_diterima_field").val();
        var orderby = $("#abf_diterima_orderby").val();
        var cari_data = encodeURIComponent($("#abf_diterima_cari_data").val());
        var status = $("#abf_diterima_status_acc").val();
        var abf_diterima_kategori = $("#abf_diterima_kategori").val();
        var tglprodacc = $('#tglprodacc').is(':checked') ? 'true' : 'false';
        var totaldata = $('#totaldataoutboundabf').val();
        var abf_asal_tujuan = $("#abf_asal_tujuan").val();
        $.ajax({
            url: "{{ route('abf.abf_diterima') }}?tanggal=" + tanggal + "&tanggalend=" + tanggalend +
                "&field=" + field + "&orderby=" + orderby + "&cari=" + cari_data + "&status=" + status +
                "&abf=" + non_abf + "&selonjor=" + selonjor + "&boneless=" + boneless + "&parting=" + parting +
                "&memar=" + memar + "&view=data" + "&kategori=" + abf_diterima_kategori + "&tglprodacc=" +
                tglprodacc + "&abf_asal_tujuan=" + abf_asal_tujuan,
            method: "GET",
            cache: false,
            success: function(data) {
                $("#abf_diterima_view").html(data);
                $("#spinerabfoutbound").hide();
            }
        });
    }

    function approveToABF(abf_id) {
        $.ajax({
            url: "{{ url('admin/abf/chiller_kirim_abf_acc') }}/" + abf_id,
            method: "GET",
            success: function(data) {
                if (data.status == 400) {
                    showAlert(data.msg);
                } else {
                    load_abf_diterima();
                    showNotif('Kirim ke ABF berhasil');
                }
            }
        });
    }
</script>