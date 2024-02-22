@php
    header('Content-Transfer-Encoding: none');
    header("Content-type: application/vnd-ms-excel");
    header("Content-type: application/x-msexcel");
    header("Content-Disposition: attachment; filename=Order Item Sales Order.xls");
@endphp

<table border="1">
    <thead>
        <tr>
            <th class="text-center" rowspan="2">No</th>
            <th class="text-center" rowspan="2">Marketing</th>
            <th class="text-center" rowspan="2">Kirim</th>
            <th class="text-center" rowspan="2">Nomor SO</th>
            <th class="text-center" rowspan="2">Customer</th>
            <th class="text-center" rowspan="2">Item</th>
            <th class="text-center" rowspan="2">Kategori</th>
            <th class="text-center" rowspan="2">Bumbu</th>
            <th class="text-center" rowspan="2">Memo</th>
            <th class="text-center" rowspan="2">Plastik</th>
            <th class="text-center" colspan="4">Order</th>
        </tr>
        <tr>
            <th class="text-center">Part</th>
            <th class="text-center">Ekor/Pcs</th>
            <th class="text-center">Pack</th>
            <th class="text-center">Berat</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($datas as $i => $row)
        <tr>
            <td>{{ ++$i }}</td>
            <td>{{ $row->so_marketing->souser->name ?? '' }}</td>
            <td>{{ date('d/m/Y', strtotime($row->tanggal_kirim)) }}</td>
            <td>{{ $row->no_so }}</td>
            <td>{{ $row->nama_customer }}</td>
            <td>{{ $row->item_nama }}
                @if(date('Y-m-d', strtotime($row->created_at_order))==$row->tanggal_kirim)
                <br><span class="small red">*Prioritas Same Day</span>
                @endif
            </td>
            <td>{{ $row->item->itemkat->nama }}</td>
            <td>{{ $row->bumbu }}</td>
            <td>{{ $row->memo }}</td>
            <td>
                @if ($row->plastik == '1') Meyer @endif
                @if ($row->plastik == '2') Avida @endif
                @if ($row->plastik == '3') Polos @endif
                @if ($row->plastik == '4') Curah @endif
                @if ($row->plastik == '5') Mojo @endif
                @if ($row->plastik == '6') Other @endif
            </td>
            <td class="text-right">{{ $row->parting }}</td>
            <td class="text-right">@if(strpos(strtolower($row->memo), "ekor") || strtolower($row->memo) == "ekor"){{ str_replace(".", ",",$row->qty) }} @endif</td>
            <td class="text-right">@if(strpos(strtolower($row->memo), "pack") || strtolower($row->memo) == "pack"){{ str_replace(".", ",",$row->qty) }} @endif</td>
            <td class="text-right"> {{number_format($row->berat)}}</td>
        </tr>
        @endforeach
    </tbody>
</table>
