<a href="{{ route('datastock.laporan', ['key' => 'open_balance']) }}&mulai={{ $mulai }}&akhir={{ $akhir }}" class="btn btn-success float-left">Unduh</a>
<table class="table default-table"  id="openbalanceTable" width="100%">
    <thead>
        <tr>
            <th rowspan="2">Item</th>
            <th rowspan="2">SKU</th>
            <th colspan="2">Masuk</th>
            <th colspan="2">Keluar</th>
            <th colspan="2">Open Balance</th>
            <th colspan="2">Sisa</th>
        </tr>
        <tr>
            <th class="text-center">Berat</th>
            <th class="text-center">Qty</th>
            <th class="text-center">Berat</th>
            <th class="text-center">Qty</th>
            <th class="text-center">Berat</th>
            <th class="text-center">Qty</th>
            <th class="text-center">Berat</th>
            <th class="text-center">Qty</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($open as $row)
        <tr>
            <td>{{ $row->nama }}</td>
            <td>{{ App\Models\Item::find($row->idb)->sku }}</td>
            <td class="text-right">{{ number_format($row->beratmasuk, 2) }}</td>
            <td class="text-right">{{ number_format($row->qtymasuk) }}</td>
            <td class="text-right">{{ number_format($row->beratkeluar, 2) }}</td>
            <td class="text-right">{{ number_format($row->qtykeluar) }}</td>
            <td class="text-right">{{ number_format($row->beratop, 2) }}</td>
            <td class="text-right">{{ number_format($row->qtyop) }}</td>
            <td class="text-right">{{ number_format($row->total_berat_stock, 2) }}</td>
            <td class="text-right">{{ number_format($row->total_qty_stock) }}</td>
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
            $('#openbalanceTable').DataTable({
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
