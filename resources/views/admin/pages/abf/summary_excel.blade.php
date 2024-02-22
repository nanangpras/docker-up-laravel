@php
header('Content-Transfer-Encoding: none');
header("Content-type: application/vnd-ms-excel");
header("Content-type: application/x-msexcel");
header("Content-Disposition: attachment; filename=Summary ABF.xls");
@endphp

<table border="1">
    <thead>
        <tr>
            <th rowspan="2">#</th>
            <th rowspan="2">ID ABF</th>
            <th rowspan="2">Item</th>
            <th rowspan="2">Customer</th>
            <th rowspan="2">Tanggal Bongkar</th>
            <th rowspan="2">Tanggal Produksi</th>
            <th rowspan="2">Gudang</th>
            <th rowspan="2">SubItem</th>
            <th rowspan="2">Packaging</th>
            <th class="text-center" colspan="2">Karung</th>
            <th class="text-center" colspan="2">Tanggal Kemasan</th>
            <th rowspan="2">Qty Awal</th>
            <th rowspan="2">Berat Awal</th>
            <th rowspan="2">Qty</th>
            <th rowspan="2">Berat</th>
            <th rowspan="2">Pallete</th>
            <th rowspan="2">Expired</th>
            <th rowspan="2">Stock</th>
        </tr>
        <tr>
            <th>Nama</th>
            <th>Jumlah</th>
            <th>Kemasan</th>
            <th>Kode</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($summary as $i => $row)
        <tr>
            <td>{{ ++$i }}</td>
            <td>{{ $row->gudangabf->id ?? $row->table_id  }}</td>
            <td>{{ $row->productitems->nama ?? ""}}</td>
            <td>{{ $row->konsumen->nama ?? ""}}</td>
            <td>{{ $row->production_date ?? ""}}</td>
            <td>{{ $row->gudangabf->tanggal_masuk ?? ""}}</td>
            <td>{{ $row->productgudang->code ?? "" }}</td>
            <td>{{ $row->sub_item ?? "" }}</td>
            <td>{{ $row->packaging }}</td>
            <td>{{ App\Models\Item::item_sku($row->karung)->nama ?? "" }}</td>
            <td>{{ $row->karung_qty }}</td>
            <td>{{ $row->tanggal_kemasan ? date('d/m/y', strtotime($row->tanggal_kemasan)) : '###' }}</td>
            <td>{{ $row->production_code }}</td>
            <td>{{ number_format($row->qty_awal) }}</td>
            <td>{{ number_format($row->berat_awal) }}</td>
            <td>{{ number_format($row->qty) }}</td>
            <td>{{ number_format($row->berat, 2) }}</td>
            <td>{{ number_format($row->palete) }}</td>
            <td>{{ number_format($row->expired) }} Bulan</td>
            <td>{{ $row->stock_type }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<br>
<br>
<br>
<table border="1">
    <thead>
        <tr>
            <th colspan="3">Petugas Gudang</th>
            <th colspan="3">Admin Produksi</th>
            <th colspan="3">BPI</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="3" rowspan="4"></td>
            <td colspan="3" rowspan="4"></td>
            <td colspan="3" rowspan="4"></td>
        </tr>
    </tbody>
</table>
