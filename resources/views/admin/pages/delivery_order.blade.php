<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>DELIVERY ORDER {{ $data->nomor_do }}</title>
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
            <div><b>DELIVERY ORDER</b></div>
            <div style="font-size: x-small">
                {{ $data->nomor_do }}<br>
                {{ date('d F Y', strtotime($data->keluar)) }}
            </div>
        </div>
    </div>
    <div style="clear: both"></div>

    <div style="padding: 30px 0">
        <div style="width: 350px; float: left">
            <table>
                <tbody>
                    <tr>
                        <td><b>Nama Driver</b></td>
                        <td style="width: 20px; text-align: center">:</td>
                        <td>{{ $data->nama }}</td>
                    </tr>
                    <tr>
                        <td><b>Nomor Polisi</b></td>
                        <td style="width: 20px; text-align: center">:</td>
                        <td>{{ $data->no_polisi }}</td>
                    </tr>
                    <tr>
                        <td><b>Wilayah Pengiriman</b></td>
                        <td style="width: 20px; text-align: center">:</td>
                        <td>{{ $data->wilayah->nama }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div style="width: 350px; float: left">
            <table>
                <tbody>
                    <tr>
                        <td><b>Qty</b></td>
                        <td style="width: 20px; text-align: center">:</td>
                        <td>{{ $data->qty }}</td>
                    </tr>
                    <tr>
                        <td><b>Berat</b></td>
                        <td style="width: 20px; text-align: center">:</td>
                        <td>{{ $data->berat }} Kg</td>
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
                    <th>Nomor Invoice</th>
                    <th>Customer</th>
                    <th>Alamat</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data->ekspedisirute as $i => $item)
                <tr>
                    <td style="text-align: center">{{ ++$i }}</td>
                    <td>{{ $item->ruteorder->nomor_invoice }}</td>
                    <td>{{ $item->nama }}</td>
                    <td>{{ $item->alamat }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
