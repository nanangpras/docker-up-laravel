@php
    header('Content-Transfer-Encoding: none');
    header("Content-type: application/vnd-ms-excel");
    header("Content-type: application/x-msexcel");
    header("Content-Disposition: attachment; filename=Proses Produksi Tanggal " . $tanggal . ".xls");
@endphp


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
            <td>{{ $row->kg }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<table>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
</table>

<table border="1">
    <thead>
        <tr>
            <th>No</th>
            <th>Regu</th>
            <th>Kategori</th>
            <th>SKU</th>
            <th>Item</th>
            <th>Ekor/Pcs/Pack</th>
            <th>Berat</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($produksi as $i => $row)
        <tr>
            <td>{{ ++$i }}</td>
            <td>{{ $row->free_stock->regu }}</td>
            <td>
                @if($row->kategori=="1")
                ABF
                @elseif($row->kategori=="2")
                EKSPEDISI
                @elseif($row->kategori=="3")
                TITIP CS
                @else
                CHILLER
                @endif
            </td>
            <td>{{ $row->item->sku }}</td>
            <td>{{ $row->nama }}</td>
            <td>{{ $row->jumlah }}</td>
            <td>{{ str_replace(".", ",",$row->kg) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
