<div class="table-responsive">
    <table class="table default-table" width="100%" id='gudangTable'>
        <thead>
            <tr>
                <th>#</th>
                <th>Gudang</th>
                <th>Sub Item</th>
                <th>SKU</th>
                <th>Item</th>
                <th>Packaging</th>
                <th>Tanggal Produksi</th>
                <th>Qty</th>
                <th>Berat Timbang</th>
                <th>Berat ABF</th>
                <th>Pallete</th>
                <th>Expired</th>
                <th>Stock</th>
                <th>Jenis Transaksi</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<link rel="stylesheet" type="text/css" href="{{asset('')}}plugin/DataTables/datatables.min.css"/>
<script type="text/javascript" src="{{asset('')}}plugin/DataTables/datatables.min.js"></script>
<script>
    $(document).ready(function() {
        var tanggalmulai    = $("#tanggalstart").val();
        var tanggalselesai  = $("#tanggalend").val();
        $('#gudangTable').DataTable({
            "bInfo"         : false,
            responsive      : true,
            scrollY         : 500,
            scrollX         : true,
            scrollCollapse  : true,
            paging          : true,
            searching       : true,
            processing      : true,
			serverSide      : true,
            ajax            : {
                url : "{{ route('laporanadmin.showDataTableGudang') }}",
                type: 'GET',
                data: {
                    "tglmulai"    : tanggalmulai,
                    "tglselesai"  : tanggalselesai,
                }
            } 
        });

        $('a[data-toggle="pill"]').on('shown.bs.tab', function(e) {
            $($.fn.dataTable.tables(true)).DataTable()
                .columns.adjust();
        });
    });
</script>
