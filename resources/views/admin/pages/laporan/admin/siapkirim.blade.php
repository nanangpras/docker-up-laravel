<div class="table-responsive">
    <table class="table default-table" width="100%" id="siapkirimTable">
        <thead>
            <tr class="text-center">
                <th>NO</th>
                <th>NO SO</th>
                <th>NO DO</th>
                <th>CUSTOMER</th>
                <th>SALES CHANNEL</th>
                <th>KATEGORI</th>
                <th>TANGGAL SO</th>
                <th>TANGGAL KIRIM</th>
                <th>KETERANGAN HEADER</th>
                <th>SKU</th>
                <th>ITEM</th>
                <th>PART</th>
                <th>BUMBU</th>
                <th>KETERANGAN ITEM</th>
                {{-- <th>QTY</th>
                <th>BERAT</th> --}}
                <th>FULFILLMENT QTY</th>
                <th>FULFILLMENT BERAT</th>
                <th>TIDAK TERKIRIM</th>
                <th>RETUR QTY</th>
                <th>RETUR BERAT</th>
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
        
        $('#siapkirimTable').DataTable({
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
                url : "{{ route('laporanadmin.showDataTableSiapKirim') }}",
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