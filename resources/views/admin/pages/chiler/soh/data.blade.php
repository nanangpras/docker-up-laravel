<table class="table table-sm table-striped table-bordered" border="1">
    <thead class="sticky-top bg-white">
        <tr>
            <th class="text-center" rowspan="2">Item</th>
            <th class="text-center" colspan="2">Saldo Awal</th>
            <th class="text-center" colspan="2">Masuk</th>
            <th class="text-center" colspan="2">Keluar</th>
            <th class="text-center" colspan="2">Saldo Akhir</th>
        </tr>
        <tr>
            <th class="text-center">Qty/Pcs</th>
            <th class="text-center">Kg</th>
            <th class="text-center">Qty/Pcs</th>
            <th class="text-center">Kg</th>
            <th class="text-center">Qty/Pcs</th>
            <th class="text-center">Kg</th>
            <th class="text-center">Qty/Pcs</th>
            <th class="text-center">Kg</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $row)
        <tr>
            <td>{{ $row->item_name }}</td>
            <td class="text-right">{{ number_format($row->qty_saldo_awal) }}</td>
            <td class="text-right">{{ number_format($row->berat_saldo_awal, 2) }}</td>
            <td class="text-right">{{ number_format($row->qty_inbound,2) }}</td>
            <td class="text-right">{{ number_format($row->berat_inbound, 2) }}</td>
            <td class="text-right">{{ number_format($row->qty_outbound,2) }}</td>
            <td class="text-right">{{ number_format($row->berat_outbound, 2) }}</td>
            <td class="text-right">{{ number_format($row->qty_saldo_akhir,2) }}</td>
            <td class="text-right">{{ number_format($row->berat_saldo_akhir, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
