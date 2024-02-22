@php
    header('Content-Transfer-Encoding: none');
    header("Content-type: application/vnd-ms-excel");
    header("Content-type: application/x-msexcel");
    header("Content-Disposition: attachment; filename=Ekspedisi - " . ($request->tanggal_awal ?? date("Y-m-d")) . " - " . ($request->tanggal_akhir ?? date("Y-m-d", strtotime('+1 day'))) . ".xls");
@endphp
<table border="1">
    <thead>
        <tr>
            <th>Nomor DO</th>
            <th>Nomor Polisi</th>
            <th>Driver</th>
            <th>Wilayah</th>
            <th>Nama Customer</th>
            <th>Item</th>
            <th>Qty</th>
            <th>Berat</th>
            <th>Keranjang</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $list)
            @foreach ($list->groupByRute as $row)
            <tr>
                <td>{{ $row->no_so ?? '' }}</td>
                <td>{{ $list->no_polisi}}</td>
                <td>{{ $list->nama}}</td>
                <td>{{ $list->wilayah->nama}}</td>
                <td>{{ $row->order_so->nama ?? '' }}</td>
                <td>
                    @foreach ($row->order_so->list_order as $item)
                        <div>{{ $item->nama_detail }}</div>
                    @endforeach
                </td>
                <td>
                    @foreach ($row->order_so->list_order as $item)
                        <div>{{ number_format($item->fulfillment_qty, 2)  }}</div>
                    @endforeach
                </td>
                <td>
                    @foreach ($row->order_so->list_order as $item)
                        <div>{{ number_format($item->fulfillment_berat, 2) }}</div>
                    @endforeach
                </td>
                <td>
                    @foreach ($row->order_so->list_order as $item)
                        <div>{{ number_format($item->keranjang, 2) }}</div>
                    @endforeach
                </td>
                <td>{{ number_format($row->berat, 2) }}</td>
            </tr>
            @endforeach
        @endforeach
    </tbody>
</table>
