@php
header('Content-Transfer-Encoding: none');
header("Content-type: application/vnd-ms-excel");
header("Content-type: application/x-msexcel");
header("Content-Disposition: attachment; filename=" . env('NET_SUBSIDIARY', 'CGL') . " - LAPORAN KONTROL WO ".$tanggal_awal."_".$tanggal_akhir.".xls");
@endphp

<table>
    <tr>
        <th colspan="15">{{ env('NET_SUBSIDIARY', 'CGL') }} - LAPORAN KONTROL WO</th>
    </tr>
    <tr>
        <td colspan="3">Parameters</td>
    </tr>
    <tr>
        <td>Start Date</td>
        <td colspan="2">{{ date('d F Y', strtotime($request->tanggal_awal)) }}</td>
    </tr>
    <tr>
        <td>End Date</td>
        <td colspan="2">{{ date('d F Y', strtotime($request->tanggal_akhir)) }}</td>
    </tr>
    <tr>
        <td colspan="2"></td>
    </tr>
</table>

<table border="1">
    <thead>
        <tr>
            <th rowspan="2">Tanggal WO Build</th>
            <th rowspan="2">No. WO Build Build</th>
            <th rowspan="2">Label</th>
            <th rowspan="2">Nama BOM</th>
            <th colspan="6">Components</th>
            <th colspan="6">Result</th>
            {{-- <th colspan="2">Difference</th> --}}
        </tr>
        <tr>
            <th>Item NO</th>
            <th>Item Name</th>
            <th>Qty</th>
            <th>Unit</th>
            <th>Price</th>
            <th>Amount</th>
            <th>Item NO</th>
            <th>Item Name</th>
            <th>Qty</th>
            <th>Unit</th>
            <th>Price</th>
            <th>Amount</th>
            {{-- <th>Qty</th>
            <th>%</th> --}}
        </tr>
    </thead>
    <tbody>
        @foreach ($netsuite as $row)
        @php
            $json   =   json_decode($row->data_content) ;
        @endphp
        <tr>
            <td>{{ $row->trans_date }}</td>
            <td>{{ $row->document_no }}</td>
            <td>
                @foreach ($json->data as $item)
                    {{ $item->item_assembly ?? '' }}
                @endforeach
            </td>
            <td>{{$row->label}}</td>
            <td>
                @for ($i = 0; $i < COUNT($json->data[0]->items); $i++)
                    @if ($json->data[0]->items[$i]->type == 'Component')
                    {{ $json->data[0]->items[$i]->item }}<br>
                    @endif
                @endfor
            </td>
            <td>
                @for ($i = 0; $i < COUNT($json->data[0]->items); $i++)
                    @if ($json->data[0]->items[$i]->type == 'Component')
                    {{ $json->data[0]->items[$i]->description }}<br>
                    @endif
                @endfor
            </td>
            <td>
                @php
                    $qty_component  =   0 ;
                @endphp
                @for ($i = 0; $i < COUNT($json->data[0]->items); $i++)
                    @if ($json->data[0]->items[$i]->type == 'Component')
                    {{ str_replace('.',',', $json->data[0]->items[$i]->qty) }}<br>

                    @php
                        if($json->data[0]->items[$i]->item=="7000000001" || $json->data[0]->items[$i]->item=="7000000002"){

                        }else{
                            if(substr($json->data[0]->items[$i]->item,0,5) == "23000" || substr($json->data[0]->items[$i]->item,0,5) == "22000" || substr($json->data[0]->items[$i]->item,0,5) == "22100" ){

                            }else
                            if(preg_match("/[a-z]/i", $json->data[0]->items[$i]->item)){
                                //print "it has alphabet!";
                            }else{
                                $qty_component  +=  $json->data[0]->items[$i]->qty ;
                            }
                        }
                    @endphp
                    
                    @endif
                @endfor
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td>
                @for ($i = 0; $i < COUNT($json->data[0]->items); $i++)
                    @if ($json->data[0]->items[$i]->type == 'Finished Goods' || $json->data[0]->items[$i]->type ==  'By Product')
                    {{ $json->data[0]->items[$i]->item }}<br>
                    @endif
                @endfor
            </td>
            <td>
                @for ($i = 0; $i < COUNT($json->data[0]->items); $i++)
                    @if ($json->data[0]->items[$i]->type == 'Finished Goods' || $json->data[0]->items[$i]->type ==  'By Product')
                    {{ $json->data[0]->items[$i]->description }}<br>
                    @endif
                @endfor
            </td>
            <td>
                @php
                    $qty_fg =   0 ;
                @endphp
                @for ($i = 0; $i < COUNT($json->data[0]->items); $i++)
                    @if ($json->data[0]->items[$i]->type == 'Finished Goods' || $json->data[0]->items[$i]->type ==  'By Product')
                    {{ str_replace('.',',', $json->data[0]->items[$i]->qty) }}<br>
                    @php
                        $qty_fg +=  $json->data[0]->items[$i]->qty ;
                    @endphp
                    @endif
                @endfor
            </td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{ str_replace('.',',', $qty_component) }}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{ str_replace('.',',', $qty_fg) }}</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        @endforeach
    </tbody>
</table>
