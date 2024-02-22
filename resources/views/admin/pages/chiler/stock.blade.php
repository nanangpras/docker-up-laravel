
<table width="100%" id="chillerstock" class="table default-table">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama</th>
            {{-- <th>No Mobil</th> --}}
            <th>Tanggal</th>
            <th>Qty Awal</th>
            <th>Berat Awal (Kg)</th>
            <th>Ekor/Pcs/Pack</th>
            <th>Sisa Berat (Kg)</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($stock as $i => $item)
            @php
                $sisaQty        = $item->sisaQty;
                $sisaBerat      = number_format((float)$item->sisaBerat, 2, '.', '');
            @endphp
            <tr>
                <td>{{ ++$i }}</td>
                <td>{{ $item->item_name }}
                    <br>
                    @if ($item->asal_tujuan == 'retur')
                        <span class="status status-info">{{ $item->label }}</span>
                    @endif
                </td>
                {{-- <td>{{ $item->no_mobil ?? '' }}</td> --}}
                <td>{{ $item->tanggal_produksi }}</td>
                <td class="text-right">{{ number_format($item->qty_item) }}</td>
                <td class="text-right">{{ number_format($item->berat_item, 2) }}</td>
                <td class="text-right">{{ $sisaQty }}</td>
                <td class="text-right">{{ $sisaBerat }}</td>
                <td>{{ App\Models\Chiller::renameData($item->asal_tujuan) }}</td>
                <td>
                    <a href="{{ route('chiller.show', $item->id) }}" class="btn btn-info">Detail</a>
                    @if (User::setIjin('superadmin') or User::setIjin(33))
                        <a href="{{ route('chiller.tukar', $item->id) }}" class="btn btn-warning btn-sm">Tukar Item</a>
                    @endif
                </td>

            </tr>

        @endforeach
    </tbody>
</table>

@section('header')
    <!-- <link rel="stylesheet" type="text/css" href="{{asset('')}}plugin/DataTables/datatables.min.css"/> -->
@stop

@section('footer')


    <!-- <script type="text/javascript" src="{{asset('')}}plugin/DataTables/datatables.min.js"></script>
    <script>
        $(document).ready(function() {
        // if ($.fn.DataTable.isDataTable('#chillerstock')) {
        //     $('#chillerstock').DataTable().destroy();
        // }
        // $('#chillerstock').DataTable({
        //     "bInfo": false,
        //     responsive: true,
        //     scrollY: 500,
        //     scrollX: true,
        //     scrollCollapse: true,
        //     paging: false,
        // });
    });

        $(document).ready(function() {
            $('.dataTable').DataTable({
                "bInfo"         : false,
                responsive      : true,
                scrollY         : 500,
                scrollX         : true,
                scrollCollapse  : true,
                paging          : false,
            });
        });
    </script> -->
@stop
