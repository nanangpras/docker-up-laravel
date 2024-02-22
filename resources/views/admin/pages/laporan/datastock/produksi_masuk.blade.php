<a href="{{ route('datastock.laporan', ['key' => 'produksi_masuk']) }}&mulai={{ $mulai }}&akhir={{ $akhir }}" class="btn btn-success float-left">Unduh</a>
<table class="table default-table"  id="produksimasukTable" width="100%">
    <thead>
        <tr>
            <th rowspan="2">Item</th>
            <th rowspan="2">SKU</th>
            <th rowspan="2">Jenis</th>
            <th rowspan="2">Tipe</th>
            <th rowspan="2">Tanggal Produksi</th>
            <th colspan="2">Masuk</th>
            <th colspan="2">Stock</th>
        </tr>
        <tr>
            <th>Qty</th>
            <th>Berat</th>
            <th>Qty</th>
            <th>Berat</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($prod_masuk as $row)
        <tr>
            <td>{{ $row->item_name }}</td>
            <td>{{ App\Models\Item::find($row->item_id)->sku }}</td>
            <td>{{ $row->jenis }}</td>
            <td>{{ $row->type }}</td>
            <td>{{ $row->tanggal_produksi }}</td>
            <td class="text-right">{{ number_format($row->qty) }}</td>
            <td class="text-right">{{ number_format($row->berat, 2) }}</td>
            <td class="text-right">{{ number_format($row->stock_qty) }}</td>
            <td class="text-right">{{ number_format($row->stock_berat, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

@section('header')
    <link rel="stylesheet" type="text/css" href="{{asset('')}}plugin/DataTables/datatables.min.css"/>
@stop

@section('footer')
    <script type="text/javascript" src="{{asset('')}}plugin/DataTables/datatables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#produksimasukTable').DataTable({
                "bInfo": false,
                responsive: true,
                scrollY:        500,
                scrollX:        true,
                scrollCollapse: true,
                paging:         false,
            });

            $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });
        } );
    </script>
@stop

