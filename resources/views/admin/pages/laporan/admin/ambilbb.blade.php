<div class="table-responsive">
    <table class="table default-table" width="100%" id="ambilbbTable">
        <thead>
            <tr class="text-center">
                <th>No</th>
                <th>TANGGAL BAHAN BAKU</th>
                <th>TANGGAL AMBIL</th>
                <th>ASAL</th>
                <th>REGU</th>
                <th>ITEM</th>
                <th>QTY BB</th>
                <th>BERAT BB</th>
                <th>QTY AMBIL</th>
                <th>BERAT AMBIL</th>
                <th>JENIS</th>
                {{-- <th>#</th> --}}
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
        $('#ambilbbTable').DataTable({
            "bInfo"         : false,
            responsive      : true,
            scrollY         : 500,
            scrollX         : true,
            scrollCollapse  : true,
            paging          : true,
            searching       : true,
            processing      : true,
			serverSide      : true,
            sort            : false,
            ajax            : {
                url : "{{ route('laporanadmin.showDataTableBB') }}",
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
