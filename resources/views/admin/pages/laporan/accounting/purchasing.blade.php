<div class="table-responsive">
    <table class="table default-table">
        <thead>
            <tr>
                <th colspan="10">LAPORAN RETUR PURCHASE</th>
            </tr>
            <tr>
                <th class="text-center" colspan="5">Purchase</th>
                <th class="text-center" colspan="2">Item</th>
                <th class="text-center" colspan="3">Retur</th>
            </tr>
            <tr>
                <th class="text-center">Net ID</th>
                <th class="text-center">Tanggal</th>
                <th class="text-center">Nomor PO</th>
                <th class="text-center">Jenis PO</th>
                <th class="text-center">Supplier</th>
                <th class="text-center">SKU</th>
                <th class="text-center">Item</th>
                <th class="text-center">Qty</th>
                <th class="text-center">Berat</th>
                <th class="text-center">Alasan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($retur_po as $row)
            <tr>
                <td>{{ $row->purchase->internal_id_po }}</td>
                <td>{{ $row->tanggal }}</td>
                <td>{{ $row->purchase->no_po }}</td>
                <td>{{ $row->purchase->type_po }}</td>
                <td>{{ $row->purchase->purcsupp->nama }}</td>
                <td>{{ $row->purchase_item->item_po }}</td>
                <td>{{ $row->purchase_item->description }}</td>
                <td class="text-right">{{ number_format($row->qty) }}</td>
                <td class="text-right">{{ number_format($row->berat, 2) }}</td>
                <td>{{ $row->get_alasan->nama }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div id="paginate_purc">
    {{ $retur_po->appends($_GET)->onEachSide(1)->links() }}
</div>

<script>
$('#paginate_purc .pagination a').on('click', function(e) {
    e.preventDefault();
    showNotif('Menunggu');

    url = $(this).attr('href');
    $.ajax({
        url: url,
        method: "GET",
        success: function(response) {
            $('#data_purc').html(response);
        }

    });
});
</script>
