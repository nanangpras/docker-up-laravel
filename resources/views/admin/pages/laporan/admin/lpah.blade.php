<div class="table-responsive">
    <table class="table default-table" width="100%" id="lpahTable">
        <thead>
            <tr class="text-center">
                <th>No</th>
                <th>TANGGAL</th>
                <th>MOBIL</th>
                <th>SOPIR</th>
                <th>No. LPAH</th>
                <th>JAM MULAI</th>
                <th>JAM SELESAI</th>
                <th>EKOR DO</th>
                <th>KG DO</th>
                <th>RATA" DO</th>
                <th>EKOR SACKLE</th>
                <th>KG TIMBANGAN</th>
                <th>RATA" TIMBANGAN</th>
                <th>KEMATIAN</th>
                <th>AYAM SAKIT</th>
                <th>TEMBOLOK</th>
                <th>KEBERSIHAN KERANJANG</th>
                <th>SUSUT AYAM</th>
                <th>BASAH</th>
                <th>DOWN TIME</th>
            </tr>
        </thead>
        
    </table>
</div>

<link rel="stylesheet" type="text/css" href="{{asset('')}}plugin/DataTables/datatables.min.css"/>
<script type="text/javascript" src="{{asset('')}}plugin/DataTables/datatables.min.js"></script>
<script>
    $(document).ready(function() {
        var tanggalmulai    = $("#tanggalstart").val();
        var tanggalselesai  = $("#tanggalend").val();
        $('#lpahTable').DataTable({
            "bInfo"         : false,
            responsive      : true,
            // scrollY         : 500,
            scrollX         : true,
            scrollCollapse  : true,
            paging          : false,
            searching       : true,
            processing      : true,
			serverSide      : true,
            ajax            : {
                url : "{{ route('laporanadmin.showDataTableLpah') }}",
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
