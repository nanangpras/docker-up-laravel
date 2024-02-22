<div class="table-responsive">
    <table class="table default-table">
        <thead>
            <tr>
                <th colspan="12">LAPORAN RETUR QC</th>
            </tr>
            <tr>
                <th class="text-center" colspan="2">Tanggal</th>
                <th class="text-center" rowspan="2">Customer</th>
                <th class="text-center" colspan="3">Document</th>
                <th class="text-center" colspan="6">Item Retur</th>
            </tr>
            <tr>
                <th class="text-center">Retur</th>
                <th class="text-center">Input</th>
                <th class="text-center">Sales Order</th>
                <th class="text-center">Delivery Order</th>
                <th class="text-center">R.A.</th>
                <th class="text-center">SKU</th>
                <th class="text-center">Item</th>
                <th class="text-center">Qty</th>
                <th class="text-center">Berat</th>
                <th class="text-center">Alasan</th>
                <th class="text-center">Kategori</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($retur_qc as $row)
            <tr>
                <td>{{ $row->to_retur->tanggal_retur }}</td>
                <td>{{ date("Y-m-d", strtotime($row->to_retur->created_at)) }}</td>
                <td>{{ $row->to_retur->to_customer->nama }}</td>
                <td>{{ $row->to_retur->no_so }}</td>
                <td>{{ $row->to_retur->data_order->no_do ?? "" }}</td>
                <td>{{ $row->to_retur->no_ra ?? "" }}</td>
                <td>{{ $row->sku }}</td>
                <td>{{ $row->to_item->nama }}</td>
                <td class="text-right">{{ number_format($row->qty ?? 0) }}</td>
                <td class="text-right">{{ number_format(($row->berat ?? 0), 2) }}</td>
                <td>{{ $row->catatan }}</td>
                <td>{{ $row->kategori }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div id="paginate_qc">
    {{ $retur_qc->appends($_GET)->onEachSide(1)->links() }}
</div>

<script>
$('#paginate_qc .pagination a').on('click', function(e) {
    e.preventDefault();
    showNotif('Menunggu');

    url = $(this).attr('href');
    $.ajax({
        url: url,
        method: "GET",
        success: function(response) {
            $('#data_view').html(response);
        }

    });
});
</script>
