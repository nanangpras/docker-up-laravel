<div class="table-responsive">
    <table id="chillerkeluar" width="100%" class="table default-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Regu</th>
                <th>Tanggal</th>
                <th>Ekor/Pcs/Pack</th>
                <th>Berat (Kg)</th>
                <th>Transaksi</th>
                <th>Asal/Tujuan</th>
                <th>Status</th>
                <th>Type</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($keluar as $i => $item)
                <tr>
                    <td>{{ ++$i }}</td>
                    <td>{{ $item->item_name}}
                        <br>
                        @if ($item->asal_tujuan == 'retur')
                            <span class="status status-info">{{ $item->label }}</span>
                        @endif
                    </td>
                    <td class="text-center">{{ $item->regu ?? '' }}</td>
                    <td>{{ $item->tanggal_produksi }}</td>
                    <td class="text-center">{{ number_format($item->qty_item) }}</td>
                    <td class="text-right">{{ number_format($item->berat_item, 2) }}</td>
                    <td class="text-capitalize">{{ $item->jenis ? : $item->chilprod->prodpur->nama_po}}</td>
                    <td>{{ $item->tujuan }}</td>
                    <td>{{ $item->status_chiler }}</td>
                    <td class="text-capitalize">{{ str_replace("-", " ", $item->type) }}</td>
                    <td><a href="{{ route('chiller.show', $item->id) }}" class="btn btn-sm btn-info">Detail</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@section('header')
    <!-- <link rel="stylesheet" type="text/css" href="{{asset('')}}plugin/DataTables/datatables.min.css"/> -->
@stop


@section('footer')
<!-- <script type="text/javascript" src="{{asset('')}}plugin/DataTables/datatables.min.js"></script> -->
<script>
    $(document).ready(function() {
        // if ($.fn.DataTable.isDataTable('#chillerkeluar')) {
        //     $('#chillerkeluar').DataTable().destroy();
        // }
        // $('#chillerkeluar').DataTable({
        //     "bInfo": false,
        //     responsive: true,
        //     scrollY:        500,
        //     scrollX:        true,
        //     scrollCollapse: true,
        //     paging:         false,
        // });
    });

</script>
@stop