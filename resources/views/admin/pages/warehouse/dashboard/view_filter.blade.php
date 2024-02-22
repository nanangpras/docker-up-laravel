<div class="card mb-4">
    <div class="card-body p-2">
        <div style="float: right;">
            <div class="row">
                <div class="col-6">
                    {!! $filter_supplier_lb !!}
                </div>
                <div class="col-6">
                    {!! $filter_ukuran_lb !!}
                </div>
            </div>
        </div>
    </div>
    <div id="filterlbsupplier"></div>
</div>
<script>

    $('.select2').select2({
        theme: 'bootstrap4'
    })

    var tanggal_awal    = $("#tanggal_awal").val();
    var tanggal_akhir   = $("#tanggal_akhir").val();

    var hash            = window.location.hash;
    var idsupplier      = '';

    defaultPage();

    function defaultPage() {
        if (hash == undefined || hash == "") {
            reloadfilterlb(idsupplier)
        }
    }

    function reloadfilterlb(idsupplier) {
        let itemid              = $("#ukuran_lb").val();
        let tanggal_awal        = "{{ $tanggal_awal}}";
        let tanggal_akhir       = "{{ $tanggal_akhir }}";
        
        $.ajax({
            url: "{{ route('warehouse_dash.view_filter') }}",
            type: "GET",
            data: {
                'key'               : 'pageSatu',
                'filter_lb_supplier': 'filter_lb_supplier',
                'itemid'            : itemid,
                'idsupplier'        : idsupplier,
                'tanggal_awal'      : tanggal_awal,
                'tanggal_akhir'     : tanggal_akhir,
            },
            success: function(data) {
                $("#filterlbsupplier").html(data);
            }
        });
    }

    $("#suppliername").on('change', function() {
        let idsupplier          = $("#suppliername").val();
        let tanggal_awal        = "{{ $tanggal_awal}}";
        let tanggal_akhir       = "{{ $tanggal_akhir }}";
        $.ajax({
            url: "{{ route('warehouse_dash.view_filter') }}",
            type: "GET",
            data: {
                'key'           : 'pageSatu',
                'cast'          : 'filter_item_supplier',
                idsupplier      : idsupplier,
                tanggal_awal    : tanggal_awal,
                tanggal_akhir   : tanggal_akhir,
            },
            beforeSend: function() {
                $("#ukuran_lb").val('all');
            },
            success: function (data) {
                reloadfilterlb(idsupplier);
                $("#ukuran_lb").html(data);
            }
        });
    });

    $("#ukuran_lb").on('change', function () {
        let itemid          = $("#ukuran_lb").val();
        let idsupplier      = $("#suppliername").val();
        let tanggal_awal    = "{{ $tanggal_awal}}";
        let tanggal_akhir   = "{{ $tanggal_akhir }}";
        console.log(idsupplier)
        if(idsupplier === ''){
            alert('Pilih Supplier')
            return false;
        }
        $.ajax({
            url: "{{ route('warehouse_dash.view_filter') }}",
            type: "GET",
            data: {
                'key'               : 'pageSatu',
                'filter_lb_supplier': 'filter_lb_supplier',
                'itemid'            : itemid,
                idsupplier          : idsupplier,
                tanggal_awal        : tanggal_awal,
                tanggal_akhir       : tanggal_akhir,
            },
            success: function (data) {
                reloadfilterlb(idsupplier);
                // $("#filterlbsupplier").html(data);
            }
        });
    });
</script>