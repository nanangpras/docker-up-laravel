@php
    header('Content-Transfer-Encoding: none');
    header("Content-type: application/vnd-ms-excel");
    header("Content-type: application/x-msexcel");
    header("Content-Disposition: attachment; filename=Ekspedisi - Driver " . $data->nama . ' - ' . $data->tanggal . ".xls");
@endphp
<table>
    <tbody>
        <tr>
            <td style="height: 50px">
                <img src="{{ asset(env('NET_SUBSIDIARY', 'CGL')."_export.png") }}">
            </td>
        </tr>
        <tr>
            <th style="text-align: left">Nama Driver</th>
            <td>{{ $data->nama }}</td>
            <td style="text-align: left"><b>Nama Sales : </b> {{ $data->kernek ?? '' }}</td>
            <td colspan="3"></td>
        </tr>
        <tr>
            <th style="text-align: left">Nomor Polisi</th>
            <td>{{ $data->nopol->nama ?? '' }}</td>
            <td style="text-align: left"><b>Tanggal Kirim : </b> {{ $data->tanggal }}</td>
            <td colspan="3"></td>
        </tr>
        <tr>
            <th style="text-align: left">Wilayah</th>
            <td>{{ $data->wilayah->nama }}</td>
            <th style="text-align: left"></th>
            <td colspan="3"></td>
        </tr>
        <tr>
            <td colspan="6"></td>
        </tr>
    </tbody>
</table>

<table border="1">
    <thead>
        <tr>
            <th>Nomor DO</th>
            <th>Nama Customer</th>
            <th>Item</th>
            <th>Qty</th>
            <th>Berat</th>
            <th>Keranjang</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data->eksrute as $row)
        <tr>
            <td>{{ $row->no_so ?? '' }}</td>
            <td>{{ $row->order_so->nama ?? '' }}</td>
            <td>
                @foreach ($row->order_so->list_order as $item)
                    <div>{{ $item->nama_detail }}</div>
                @endforeach
            </td>
            <td>
                @foreach ($row->order_so->list_order as $item)
                    <div>{{ str_replace(".", ",",$item->fulfillment_qty) }}</div>
                @endforeach
            </td>
            <td>
                @foreach ($row->order_so->list_order as $item)
                    <div>{{ str_replace(".", ",",$item->fulfillment_berat) }}</div>
                @endforeach
            </td>
            <td>
                @foreach ($row->order_so->list_order as $item)
                    <div>{{ str_replace(".", ",",$item->keranjang) }}</div>
                @endforeach
            </td>
            <td>{{ $row->berat ?? '' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<table>
    <tr><td colspan="6"></td></tr>
    <tr><td colspan="6"></td></tr>
    <tr>
        <td colspan="2"></td>
        <td style="text-align: center">Mengetahui,</td>
        <td colspan="3"></td>
    </tr>
    <tr>
        <td colspan="2"></td>
        <td><br><br><br><br></td>
        <td colspan="3"></td>
    </tr>
    <tr>
        <td colspan="2"></td>
        <td style="text-align: center">(......................................)</td>
        <td colspan="3"></td>
    </tr>
</table>
