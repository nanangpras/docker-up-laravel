<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Rekap Kiriman Sopir {{ $data->nama }} Tanggal {{ $data->tanggal }}</title>
    <style>
        @page { size: 21cm 30cm; }
        body { font-size : 9.2px ; text-transform: uppercase; font-family: sans-serif }
        table {
            border-collapse: collapse
        }
        table th, table td { padding: 3px }
    </style>
</head>
<body>
    <div style="text-transform: uppercase; font-size: 13px">
        <img src="https://app.citragunalestari.co.id/logo.png" style="width:170px">
    </div>

    <div style="margin-top: 20px; text-align: center; font-size: 15px"><b>REKAP KIRIMAN</b></div>

@php
function hari($tanggal){
	$hari   =   date('D', $tanggal);

	switch($hari){
		case 'Sun': $hari_ini = "Minggu"; break ;
		case 'Mon': $hari_ini = "Senin"; break ;
		case 'Tue': $hari_ini = "Selasa"; break ;
		case 'Wed': $hari_ini = "Rabu"; break ;
		case 'Thu': $hari_ini = "Kamis"; break ;
		case 'Fri': $hari_ini = "Jumat"; break ;
		case 'Sat': $hari_ini = "Sabtu"; break ;
		default: $hari_ini = "Tidak di ketahui"; break ;

    }
	return $hari_ini ;
}
@endphp

    <div>
        <div style="float:left">
            <table style="width: 200px; font-size: 10px">
                <tbody>
                    <tr>
                        <td style="width: 30px">NOMOR</td>
                        <td style="width: 10px; padding-left: 10px">:</td>
                        <td>{{ $data->nomor_do }}</td>
                    </tr>
                    <tr>
                        <td style="width: 30px">TANGGAL</td>
                        <td style="width: 10px; padding-left: 10px">:</td>
                        <td>{{ hari(strtotime($data->tanggal)) . ', ' . date('d/m/Y', strtotime($data->tanggal)) }}</td>
                    </tr>
                    <tr>
                        <td style="width: 30px">SOPIR</td>
                        <td style="width: 10px; padding-left: 10px">:</td>
                        <td>{{ $data->nama }}</td>
                    </tr>
                    <tr>
                        <td style="width: 30px">KENEK/SALES</td>
                        <td style="width: 10px; padding-left: 10px">:</td>
                        <td>{{ $data->kernek }}</td>
                    </tr>
                    <tr>
                        <td style="width: 30px">WILAYAH</td>
                        <td style="width: 10px; padding-left: 10px">:</td>
                        <td>{{ \App\Models\Wilayah::find($data->wilayah_id)->nama ?? "" }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div style="float: right;">
            <div style="border: 2px solid #000; width: 200px!important; text-align: center!important; padding: 5px; padding-right: 10px; margin-left: 100px; margin-top: 20px;">
                JUMLAH BARANG DALAM KEADAAN NETTO / SUDAH DI TES
            </div>
        </div>
    </div>
    <div style="clear: both;"></div>

    <div style="padding-top: 20px">
        <table border="2" style="border-color: #000" width="100%">
            <thead>
                <tr>
                    <th rowspan="2">No</th>
                    <th rowspan="2" style="width: 120px">Customer / No Order</th>
                    <th rowspan="2">Jenis Barang</th>
                    <th colspan="2">Jumlah SO</th>
                    <th colspan="2">Jumlah DO</th>
                    <th rowspan="2">Status SO</th>
                    <th rowspan="2" style="width: 150px">Keterangan</th>
                </tr>
                <tr>
                    <th>EKOR / PCS / PACK</th>
                    <th>KG</th>
                    <th>EKOR / PCS / PACK</th>
                    <th>KG</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $no_so  =   '' ;
                    $qty_do = 0;
                    $berat_do=0;
                @endphp
                @foreach ($data->eksrute as $x => $row)

                    {{-- INI JIKA DIA SUDAH VERIFIED --}}
                    {{-- {{ dd($row->order_so->list_order ) }} --}}
                    @if ($row->marketing_so->status == 3)
                        @foreach ($row->order_so->daftar_order_bb as $i => $item)
                        @php
                            $qty_so     = App\Models\OrderItem::select('qty')->where('id', $item->order_item_id)->first();
                            $berat_so   = App\Models\OrderItem::select('berat')->where('id', $item->order_item_id)->first();
                            $dataDetail =  App\Models\OrderItem::where('id', $item->order_item_id)->first();
                        @endphp
                        <tr>
                            @if ($no_so != $row->no_so)
                            <td style="text-align: center" @if ($no_so != $row->no_so) rowspan="{{ COUNT($row->order_so->daftar_order_bb) }}" @endif>
                                @if ($no_so != $row->no_so)
                                {{ ++$x }}
                                @endif
                            </td>
                            @endif
                            @if ($no_so != $row->no_so)
                            <td @if ($no_so != $row->no_so) rowspan="{{ COUNT($row->order_so->list_order) }}" @endif>
                                @if ($no_so != $row->no_so)
                                {{ $row->order_so->nama ?? '' }}<br>{{ $row->no_so ?? '' }}
                                @endif
                            </td>
                            @endif
                            <td>
                                {{ $dataDetail->nama_detail }}
                                @if ($dataDetail->part)
                                <div>--- PARTING {{ $dataDetail->part }}</div>
                                @endif
                                @if ($dataDetail->bumbu)
                                <div>--- BUMBU {{ $dataDetail->bumbu }}</div>
                                @endif
                                @if ($dataDetail->karung)
                                <div>--- KARUNG {{ $dataDetail->karung }}</div>
                                @endif
                                @if ($dataDetail->keranjang)
                                <div>--- KERANJANG {{ $dataDetail->keranjang }}</div>
                                @endif
                            </td>
                            <td style="text-align: right; font-size: 14px">{{ number_format($qty_so->qty, 0, ",", ".") }}</td>
                            <td style="text-align: right; font-size: 14px">{{ number_format($berat_so->berat, 2, ",", ".") }}</td>
                            <td style="text-align: right; font-size: 14px">{{ number_format($item->bb_item, 0, ",", ".") }}</td>
                            <td style="text-align: right; font-size: 14px">{{ number_format($item->bb_berat, 2, ",", ".") }}</td>
                            <td>Verified</td>
                            <td>{{ $dataDetail->keterangan ?? '-' }}</td>
                        </tr>
                        @php
                            $no_so  =   $row->no_so ;
                        @endphp
                        @endforeach
                    @else



                        {{-- INI JIKA BELUM VERIFIED --}}
                        @foreach ($row->marketing_so->itemActual as $i => $itemSO)
                        {{-- @php
                            $qty_do = App\Models\Bahanbaku::select('bb_item')->where('order_item_id',$item->id)->first();
                            $berat_do = App\Models\Bahanbaku::select('bb_berat')->where('order_item_id',$item->id)->first();
                        @endphp --}}
                        <tr>
                            @if ($no_so != $row->no_so)
                            <td style="text-align: center" @if ($no_so != $row->no_so) rowspan="{{ COUNT($row->marketing_so->itemActual) }}" @endif>
                                @if ($no_so != $row->no_so)
                                    {{ ++$x }}
                                @endif
                            </td>
                            @endif
                            @if ($no_so != $row->no_so)
                            <td @if ($no_so != $row->no_so) rowspan="{{ COUNT($row->marketing_so->itemActual) }}" @endif>
                                @if ($no_so != $row->no_so)
                                {{ $row->marketing_so->socustomer->nama ?? '' }}<br>{{ $row->no_so ?? '' }}
                                @endif
                            </td>
                            @endif
                            <td>
                                {{ $itemSO->item_nama }}
                                @if ($itemSO->part)
                                <div>--- PARTING {{ $itemSO->parting }}</div>
                                @endif
                                @if ($itemSO->bumbu)
                                <div>--- BUMBU {{ $itemSO->bumbu }}</div>
                                @endif
                                @if ($itemSO->karung)
                                <div>--- KARUNG {{ $itemSO->karung ?? ''}}</div>
                                @endif
                                @if ($itemSO->keranjang)
                                <div>--- KERANJANG {{ $itemSO->keranjang ?? ''}}</div>
                                @endif
                            </td>
                            <td style="text-align: right; font-size: 14px">{{ $itemSO->qty }}</td>
                            <td style="text-align: right; font-size: 14px">{{ $itemSO->berat }}</td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            <td>Unverified</td>
                            <td>{{ $itemSO->description_item }}</td>
                        </tr>
                        @php
                            $no_so  =   $row->no_so ;
                        @endphp
                        @endforeach
                    @endif


                @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top: 10px">
        <table width="100%">
            <tbody>
                <tr>
                    <td style="text-align: center">DITERIMA OLEH :<br>SALES</td>
                    <td style="text-align: center">DISERAHKAN OLEH :</td>
                    <td style="text-align: center">MENGETAHUI :<br>Q.A.</td>
                </tr>
                <tr>
                    <td><br><br><br><br><br></td>
                    <td><br><br><br><br><br></td>
                    <td><br><br><br><br><br></td>
                </tr>
                <tr>
                    <td style="text-align: center">(&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;)</td>
                    <td style="text-align: center">(&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;)</td>
                    <td style="text-align: center">(&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;)</td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
