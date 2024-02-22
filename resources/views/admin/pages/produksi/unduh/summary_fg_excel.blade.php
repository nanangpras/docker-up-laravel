@php
    header('Content-Transfer-Encoding: none');
    header("Content-type: application/vnd-ms-excel");
    header("Content-type: application/x-msexcel");
    header("Content-Disposition: attachment; filename=Produksi Hasil Produksi Tanggal " . $tanggal . ".xls");
@endphp

<table>
    <tbody>
        <tr>
            <th colspan="10"></th>
        </tr>
        <tr>
            <th colspan="10">Produksi Hasil Produksi Tanggal {{ $tanggal }}</th>
        </tr>
        <tr>
            <th colspan="10"></th>
        </tr>
    </tbody>
</table>
<table border="1">
    <thead>
        <tr>
            <th>No</th>
            <th>Regu</th>
            <th>Kategori</th>
            <th>Customer</th>
            <th>SKU</th>
            <th>Item</th>
            <th>Ekor/Pcs/Pack</th>
            <th>Berat</th>
            <th>Plastik</th>
            <th>Qty Plastik</th>
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
            <td>{{ $row->konsumen->nama ?? '' }}</td>
            <td>{{ $row->item->sku }}</td>
            <td>{{ $row->nama }}</td>
            <td>{{ $row->jumlah }}</td>
            <td>{{ str_replace(".", ",",$row->kg) }}</td>
            <td>{{ $row->plastik_nama }}</td>
            <td>{{ str_replace(".", ",",$row->plastik_qty) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
