<div class="table-responsive">
    <table class="table default-table" width="100%" id="evisTable">
        <thead>
            <tr class="text-center">
                <th>No</th>
                <th>Tanggal</th>
                <th>FARM</th>
                <th>MOBIL</th>
                <th>No. DO</th>
                <th>DRIVER</th>
                <th>JAM MASUK</th>
                <th>JAM BONGKAR</th>
                <th>ITEM</th>
                <th>Qty</th>
                <th>BERAT</th>
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
        $('#evisTable').DataTable({
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
                url : "{{ route('laporanadmin.showDataTableEvis') }}",
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