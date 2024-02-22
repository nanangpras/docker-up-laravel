
<table width="100%" id="chillerstock" class="table default-table">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Qty</th>
            <th>Berat (Kg)</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @php
            $qty = 0;
            $berat = 0;
        @endphp
        @foreach ($stock as $i => $item)
            <tr>
                <td>{{ ++$i }}</td>
                <td>{{ $item->nama }}</td>
                <td>{{ $item->saldo_qty }}</td>
                <td>{{ number_format($item->saldo_berat, 2) }} Kg</td>
                <td></td>
                <td></td>
            </tr>
        @endforeach
    </tbody>
</table>
