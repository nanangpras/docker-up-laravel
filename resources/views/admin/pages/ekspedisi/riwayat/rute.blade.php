@php
    header('Content-Transfer-Encoding: none');
    header("Content-type: application/vnd-ms-excel");
    header("Content-type: application/x-msexcel");
    header("Content-Disposition: attachment; filename=Ekspedisi - Rute " . $ekspedisi->nama . ' - ' . $ekspedisi->tanggal . ".xls");
@endphp


<table border="1">
    <thead>
        <tr>
            <th colspan="14">RUTE PENGIRIMAN TANGGAL : {{date('d-M-Y', strtotime($ekspedisi->tanggal))}}</th>
        </tr>
        <tr>
            <th colspan="14">SUPIR : {{strtoupper($ekspedisi->nama)}} || NO POLISI : {{$ekspedisi->no_polisi}}</th>
        </tr>
        <tr>
            <th class="text-center" rowspan="2">NO</th>
            <th class="text-center" rowspan="2">MKT</th>
            <th class="text-center" rowspan="2">PRIORITAS KIRIM</th>
            <th class="text-center" rowspan="2">CUSTOMER</th>
            <th class="text-center" rowspan="2">ITEM</th>
            <th class="text-center" rowspan="2">JENIS</th>
            <th class="text-center" colspan="4">ORDER</th>
            <th class="text-center" colspan="3">AKTUAL</th>
            <th class="text-center" rowspan="2">KERANJANG</th>
            <th class="text-center" rowspan="2">MEMO</th>
        </tr>
        <tr>
            <th class="text-center">PART</th>
            <th class="text-center">Ekor/Pcs/Pack</th>
            <th class="text-center">Package</th>
            <th class="text-center">BERAT</th>
            <th class="text-center">Ekor/Pcs/Pack</th>
            <th class="text-center">Package</th>
            <th class="text-center">BERAT</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $i => $row)
        <tr>
            <td>{{ ++$i }}</td>
            <td>
                {{$row->marketing_nama ?? $row->sales_id}}
            </td>
            <td></td>
            <td>{{ $row->nama }}</td>
            <td>{{ $row->nama_detail }}
            </td>
            <td>
                @php
                    $jenis = "<span class='small'>FRESH</span>";
                    if (str_contains($row->nama_detail, 'FROZEN')) {
                        $jenis = "<span class='small'>FROZEN</span>";
                    }
                @endphp
                {!!$jenis!!}
            </td>
            <td class="text-right"> {{ $row->part }}</td>
            <td class="text-right">@if(strpos(strtolower($row->memo), "ekor") || strtolower($row->memo) == "ekor"){{ str_replace(".", ",",$row->qty) }} @endif</td>
            <td class="text-right">@if(strpos(strtolower($row->memo), "pack") || strtolower($row->memo) == "pack"){{ str_replace(".", ",",$row->qty) }} @endif</td>
            <td class="text-right">{{ str_replace(".", ",",$row->berat) }}</td>
            <td class="text-right">@if(strpos(strtolower($row->memo), "ekor") || strtolower($row->memo) == "ekor"){{ str_replace(".", ",",$row->fulfillment_qty) }} @endif</td>
            <td class="text-right">@if(strpos(strtolower($row->memo), "pack") || strtolower($row->memo) == "pack"){{ str_replace(".", ",",$row->fulfillment_qty) }} @endif</td>
            <td class="text-right">{{ str_replace(".", ",",$row->fulfillment_berat) }}</td>
            <td class="text-right">{{ $row->keranjang }}</td>
            <td>{{ $row->memo }}</td>
        </tr>

        @endforeach
    </tbody>
</table>
