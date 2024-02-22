@php
    header('Content-Transfer-Encoding: none');
    header("Content-type: application/vnd-ms-excel");
    header("Content-type: application/x-msexcel");
    header("Content-Disposition: attachment; filename=Produksi Ambil Bahan Baku Tanggal " . $tanggal . ".xls");
@endphp

<table>
    <tbody>
        <tr>
            <th colspan="5"></th>
        </tr>
        <tr>
            <th colspan="5">Produksi Ambil Bahan Baku Tanggal {{ $tanggal }}</th>
        </tr>
        <tr>
            <th colspan="5"></th>
        </tr>
    </tbody>
</table>
<table border="1">
    <thead>
        <tr>
            <th>No</th>
            <th>Regu</th>
            <th>SKU</th>
            <th>Item</th>
            <th>Ekor/Pcs/Pack</th>
            <th>Berat</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($bahan_baku as $i => $row)
        <tr>
            <td>{{ ++$i }}</td>
            <td>{{ $row->free_stock->regu }}</td>
            <td>{{ $row->sku }}</td>
            <td>{{ $row->nama }}</td>
            <td>{{ $row->jumlah }}</td>
            <td>{{ str_replace(".", ",",$row->kg) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
