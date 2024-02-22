@php
header('Content-Transfer-Encoding: none');
header("Content-type: application/vnd-ms-excel");
header("Content-type: application/x-msexcel");
header("Content-Disposition: attachment; filename=" . env('NET_SUBSIDIARY', 'CGL') . " - LAPORAN KONTROL WO TOTAL ".$tanggal_awal."_".$tanggal_akhir.".xls");
@endphp

<table>
    <tr>
        <th colspan="6">{{ env('NET_SUBSIDIARY', 'CGL') }} - LAPORAN KONTROL WO TOTAL</th>
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
            <th>Tanggal WO Build</th>
            <th>No. WO Build Build</th>
            <th>Label</th>
            <th>Nama BOM</th>
            <th>Components Qty</th>
            <th>Result Qty</th>
            <th>%</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($netsuite as $row)
        @php
            $json   =   json_decode($row->data_content) ;
        @endphp
        @php
            $qty_component  =   0 ;
            $qty_fg =   0 ;
        @endphp
        @for ($i = 0; $i < COUNT($json->data[0]->items); $i++)
            @if ($json->data[0]->items[$i]->type == 'Component')
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

            @if ($json->data[0]->items[$i]->type == 'Finished Goods' || $json->data[0]->items[$i]->type ==  'By Product')
            @php
                $qty_fg = $qty_fg + (float)$json->data[0]->items[$i]->qty ;
            @endphp
            @endif
        @endfor
        <tr>
            <td>{{ $row->trans_date }}</td>
            <td>{{ $row->document_no }}
            </td>
            <td>{{ $row->label }}</td>
            <td>
                @foreach ($json->data as $item)
                    {{ $item->item_assembly ?? '' }}
                @endforeach
            </td>
            <td>{{ str_replace('.',',', $qty_component) }}</td>
            <td>{{ str_replace('.',',', $qty_fg) }}</td>
            <td>
                @if($qty_component>0)
                {{ str_replace('.',',', number_format((($qty_fg - $qty_component) / $qty_component * 100), 2)) }}
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
