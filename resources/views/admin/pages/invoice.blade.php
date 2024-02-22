<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>INVOICE {{ $data->nomor_invoice }}</title>
    <style>
        * {
            font-size: 13px
        }
        table {
            border-collapse: collapse
        }
        table td, table th {
            padding: 2px 5px
        }
    </style>
</head>
<body>

    <div>
        <div style="float: left">
            <img src="logo.png" style="width: 200px;">
            <div>ALAMAT</div>
        </div>

        <div style="float: right">
            <div><b>INVOICE ORDER</b></div>
            <div style="font-size: x-small">
                {{ $data->nomor_invoice }}<br>
                {{ date('d F Y', strtotime($data->invoice_created_at)) }}
            </div>
        </div>
    </div>
    <div style="clear: both"></div>

    <div style="padding: 30px 0">
        <div style="width: 350px; float: left">
            <table>
                <tbody>
                    <tr>
                        <td><b>Nama</b></td>
                        <td style="width: 20px; text-align: center">:</td>
                        <td>{{ $data->nama }}</td>
                    </tr>
                    <tr>
                        <td><b>Telepon</b></td>
                        <td style="width: 20px; text-align: center">:</td>
                        <td>{{ $data->telp }}</td>
                    </tr>
                    <tr>
                        <td><b>Tanggal Order</b></td>
                        <td style="width: 20px; text-align: center">:</td>
                        <td>{{ date('d F Y - H:i', strtotime($data->created_at)) }} WIB</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div style="width: 350px; float: left">
            <table>
                <tbody>
                    <tr>
                        <td><b>Alamat Pengiriman</b></td>
                        <td style="width: 20px; text-align: center">:</td>
                        <td>{{ $data->alamat }}</td>
                    </tr>
                    <tr>
                        <td><b>Tanggal Kirim</b></td>
                        <td style="width: 20px; text-align: center">:</td>
                        <td>{{ date('d F Y', strtotime($data->tanggal_kirim)) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div style="clear: both"></div>
    </div>

    <div>
        <table width='100%' border="1">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Produk</th>
                    <th>Qty</th>
                    <th>Berat</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data->daftar_order as $i => $item)
                <tr>
                    <td style="text-align: center">{{ ++$i }}</td>
                    <td>{{ $item->nama_detail }}</td>
                    <td style="text-align: center">{{ $item->qty }}</td>
                    <td style="text-align: center">{{ $item->berat }}</td>
                </tr>
                @endforeach
                {{--  --}}
            </tbody>
        </table>
    </div>
</body>
</html>
