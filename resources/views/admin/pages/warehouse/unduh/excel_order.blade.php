@php
    header('Content-Transfer-Encoding: none');
    header("Content-type: application/vnd-ms-excel");
    header("Content-type: application/x-msexcel");
    header("Content-Disposition: attachment; filename=Daftar Order " . ($request->tanggal_mulai ?? date('Y-m-d')) . " - " . ($request->tanggal_akhir ?? date('Y-m-d')) . ".xls");
@endphp

<table border="1">
    <thead>
        <tr>
            <th class="text-center" rowspan="2">NO</th>
            <th class="text-center" rowspan="2">MKT</th>
            <th class="text-center" rowspan="2">CUSTOMER</th>
            <th class="text-center" rowspan="2">WILAYAH</th>
            <th class="text-center" rowspan="2">TANGGAL KIRIM</th>
            <th class="text-center" rowspan="2">ITEM</th>
            <th class="text-center" rowspan="2">JENIS</th>
            <th class="text-center" rowspan="2">BUMBU</th>
            <th class="text-center" rowspan="2">MEMO</th>
            <th class="text-center" colspan="4">ORDER</th>
            <th class="text-center" colspan="4">AKTUAL</th>
            <th class="text-center" rowspan="2">KERANJANG</th>
        </tr>
        <tr>
            <th class="text-center">PART</th>
            <th class="text-center">Ekor/Pcs/Pack</th>
            <th class="text-center">Package</th>
            <th class="text-center">BERAT</th>
            <th class="text-center">PART</th>
            <th class="text-center">Ekor/Pcs/Pack</th>
            <th class="text-center">Package</th>
            <th class="text-center">BERAT</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $i => $row)
        <tr
                @if($row->order_status_so=="Closed") style="background-color: red; color:white" @endif
                @if($row->order_status_so=="Pending Fulfillment")
                    @if($row->edit_item==1) style="background-color: #FFFF8F" @endif
                    @if($row->edit_item==2) style="background-color: #FFEA00" @endif
                    @if($row->edit_item==3) style="background-color: #FDDA0D" @endif
                @endif
                @if($row->delete_at_item!=NULL) style="background-color: red; color:white" @endif
                >
            <td>{{ ++$i }}</td>
            <td>
                {{$row->marketing_nama ?? $row->sales_id}}
            </td>
            <td>{{ $row->nama }} </td>
            <td>{{ $row->wilayah }} </td>
            <td>{{ $row->tanggal_kirim }}</td>
            <td>{{ $row->nama_detail }}
                @if(date('Y-m-d', strtotime($row->created_at_order))==$row->tanggal_kirim)
                <span class="small red">*Prioritas Same Day</span>
                @endif
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
            <td>{{ $row->bumbu }}</td>
            <td>@if($row->memo_header){{ $row->memo_header }} || @endif{{ $row->memo }}</td>
            <td class="text-right"> {{ $row->part }}</td>
            <td class="text-right">
                {{-- @if(strpos(strtolower($row->memo), "ekor") || strtolower($row->memo) == "ekor") --}}
                {{ str_replace(".", ",",$row->qty) }}
                {{-- @endif --}}
            </td>
            <td class="text-right">
                {{-- @if(strpos(strtolower($row->memo), "pack") || strtolower($row->memo) == "pack") --}}
                {{ str_replace(".", ",",$row->qty) }}
                {{-- @endif --}}
            </td>
            <td class="text-right">{{ str_replace(".", ",",$row->berat) }}</td>
            <td class="text-right"> {{ $row->part }}</td>
            <td class="text-right">
                {{-- @if(strpos(strtolower($row->memo), "ekor") || strtolower($row->memo) == "ekor") --}}
                {{ str_replace(".", ",",$row->fulfillment_qty) }}
                {{-- @endif --}}
            </td>
            <td class="text-right">
                {{-- @if(strpos(strtolower($row->memo), "pack") || strtolower($row->memo) == "pack") --}}
                {{ str_replace(".", ",",$row->fulfillment_qty) }}
                {{-- @endif --}}
            </td>
            <td class="text-right">{{ str_replace(".", ",",$row->fulfillment_berat) }}</td>
            <td>
                @php
                    $krj = \App\Models\Bahanbaku::total_keranjang($row->id);
                @endphp
                {{$krj}}
            </td>
        </tr>

        @endforeach
    </tbody>
</table>
