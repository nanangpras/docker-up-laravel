@php
    header('Content-Transfer-Encoding: none');
    header("Content-type: application/vnd-ms-excel");
    header("Content-type: application/x-msexcel");
    header("Content-Disposition: attachment; filename=Sales Order By Item.xls");
@endphp

<style>
    th, td{
        border: 1px solid #ddd;
    }
</style>
<table class="table table-sm table-hover table-striped table-bordered table-small">
    <thead>
        <tr>
            <th class="text-center" rowspan="2">NO</th>
            <th class="text-center" rowspan="2">MKT</th>
            <th class="text-center" rowspan="2">CUSTOMER</th>
            <th class="text-center" rowspan="2">NO SO</th>
            <th class="text-center" rowspan="2">TGL SO</th>
            <th class="text-center" rowspan="2">TGL KIRIM</th>
            <th class="text-center" rowspan="2">ITEM</th>
            <th class="text-center" rowspan="2">KATEGORI</th>
            <th class="text-center" rowspan="2">JENIS</th>
            <th class="text-center" rowspan="2">BUMBU</th>
            <th class="text-center" rowspan="2">MEMO</th>
            <th class="text-center" colspan="3">ORDER</th>
            <th class="text-center" colspan="2">AKTUAL</th>
            <th class="text-center" rowspan="2">ALASAN TIDAK TERPENUHI</th>
            <th class="text-center" rowspan="2">TIMESTAMP</th>
            <th class="text-center" rowspan="2">NO DO</th>
        </tr>
        <tr>
            <th class="text-center">PART</th>
            <th class="text-center">Ekor/Pcs/Pack</th>
            {{-- <th class="text-center">Pack</th> --}}
            <th class="text-center">BERAT</th>
            <th class="text-center">Ekor/Pcs/Pack</th>
            {{-- <th class="text-center">Pack</th> --}}
            <th class="text-center">BERAT</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($dataUnduh as $i => $row)
        <tr
        @if($row->edit_item==1) style="background-color: #FFFF8F" @endif
        @if($row->edit_item==2) style="background-color: #FFEA00" @endif
        @if($row->edit_item==3) style="background-color: #FDDA0D" @endif
        {{-- @if($row->delete_at_item!=NULL) style="background-color: red; color:white" @endif
        @if($row->order_status_so=="Closed") style="background-color: red; color:white" @endif --}}
        >
            <td>{{ ++$i }}</td>
            <td>
                {{$row->marketing_nama ?? $row->sales_id}}
            </td>
            <td>{{ $row->nama }}
            </td>
            <td>{{ $row->no_so }}</td>
            <td>{{ $row->tanggal_so }}</td>
            <td>{{ $row->tanggal_kirim }}</td>
            <td>{{ $row->nama_detail }}
                @if(date('Y-m-d', strtotime($row->created_at_order))==$row->tanggal_kirim)
                    <br><span class="small red">*Prioritas Same Day</span>
                @endif
            </td>
            <td>{{ $row->item->itemkat->nama }}</td>
            <td>
                @php
                    $jenis = "<span class='small'>FRESH</span>";
                    if (str_contains($row->nama_detail, 'FROZEN')) {
                        $jenis = "<span class='small'>FROZEN</span>";
                    }
                @endphp
                {!!$jenis!!}
            </td>
            <td>{{ $row->bumbu }}</td>
            <td>@if($row->memo_header){{ $row->memo_header }} || @endif {{ $row->memo }}</td>
            <td class="text-right"> {{ $row->part }}</td>
            <td class="text-right">{{ $row->qty }}</td>
            {{-- <td class="text-right">@if(strpos(strtolower($row->memo), "pack") || strtolower($row->memo) == "pack"){{ $row->qty }} @endif</td> --}}
            <td class="text-right">{{  str_replace(".", ",",$row->berat) }}</td>

            <td class="text-right">{{ str_replace(".", ",",$row->fulfillment_qty) }}</td>
            {{-- <td class="text-right">@if(strpos(strtolower($row->memo), "pack") || strtolower($row->memo) == "pack"){{ str_replace(".", ",",$row->fulfillment_qty) }} @endif</td> --}}
            <td class="text-right">{{ str_replace(".", ",",$row->fulfillment_berat) }}</td>
            <td>{{ $row->tidak_terkirim_catatan }}</td>
            <td>{{ $row->created_at_order }}</td>
            <td>{{ $row->no_do }}</td>
        </tr>

        @endforeach
    </tbody>
</table>
