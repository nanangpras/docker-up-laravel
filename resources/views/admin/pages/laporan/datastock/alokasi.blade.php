<a href="{{ route('datastock.laporan', ['key' => 'laporan_alokasi']) }}&mulai={{ $mulai }}&akhir={{ $akhir }}" class="btn btn-success float-left">Unduh</a>
<table class="table default-table" id="alokasiTable" width="100%">
    <thead>
        <tr>
            <th>Item</th>
            <th>SKU</th>
            <th>Jenis</th>
            <th>Tipe</th>
            <th>Tanggal Produksi</th>
            <th>Qty</th>
            <th>Berat</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($alokasi as $row)
        <tr>
            <td>{{ $row->item_name }}</td>
            <td>{{ App\Models\Item::find($row->item_id)->sku }}</td>
            <td>{{ $row->jenis }}</td>
            <td>{{ $row->type }}</td>
            <td>{{ $row->tanggal_produksi }}</td>
            <td>{{ $row->qty }}</td>
            <td>{{ $row->berat }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<link rel="stylesheet" type="text/css" href="{{asset('')}}plugin/DataTables/datatables.min.css"/>
<script type="text/javascript" src="{{asset('')}}plugin/DataTables/datatables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#alokasiTable').DataTable({
            "bInfo"         : false,
            responsive      : true,
            scrollY         : 500,
            scrollX         : true,
            scrollCollapse  : true,
            paging          : false,
        });

        $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
            $($.fn.dataTable.tables(true)).DataTable()
                .columns.adjust();
        });
    } );
</script>
