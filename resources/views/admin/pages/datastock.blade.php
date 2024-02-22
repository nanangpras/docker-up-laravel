<table border="1">
    <thead>
        <tr>
            <th rowspan="2">Item</th>
            <th colspan="2">Open Balance</th>
            <th colspan="2">Stock Masuk</th>
            <th colspan="2">Total Masuk</th>
            <th colspan="2">Stock Keluar</th>
            <th colspan="2">Total Stock</th>
        </tr>
        <tr>
            <th>Berat</th>
            <th>Qty</th>
            <th>Berat</th>
            <th>Qty</th>
            <th>Berat</th>
            <th>Qty</th>
            <th>Berat</th>
            <th>Qty</th>
            <th>Berat</th>
            <th>Qty</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $row)
        <tr>
            <td>{{ $row->nama }}</td>
            <td style="{{ $row->beratop < 0 ? 'background-color:#dd3' : '' }}">{{ number_format($row->beratop, 2) }}</td>
            <td style="{{ $row->qtyop < 0 ? 'background-color:#dd3' : '' }}">{{ number_format($row->qtyop) }}</td>
            <td style="{{ $row->beratmasuk < 0 ? 'background-color:#dd3' : '' }}">{{ number_format($row->beratmasuk, 2) }}</td>
            <td style="{{ $row->qtymasuk < 0 ? 'background-color:#dd3' : '' }}">{{ number_format($row->qtymasuk) }}</td>
            <td>{{ number_format(($row->beratop + $row->beratmasuk), 2) }}</td>
            <td>{{ number_format($row->qtyop + $row->qtymasuk) }}</td>
            <td style="{{ $row->beratkeluar < 0 ? 'background-color:#dd3' : '' }}">{{ number_format($row->beratkeluar, 2) }}</td>
            <td style="{{ $row->qtykeluar < 0 ? 'background-color:#dd3' : '' }}">{{ number_format($row->qtykeluar) }}</td>
            <td style="{{ ($row->beratop + $row->beratmasuk) - $row->beratkeluar < 0 ? 'background-color:#dd3' : '' }}">{{ number_format(($row->beratop + $row->beratmasuk) - $row->beratkeluar, 2) }}</td>
            <td style="{{ ($row->qtyop + $row->qtymasuk) - $row->qtykeluar < 0 ? 'background-color:#dd3' : '' }}">{{ number_format(($row->qtyop + $row->qtymasuk) - $row->qtykeluar) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
