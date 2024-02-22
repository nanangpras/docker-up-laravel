@php
    header('Content-Transfer-Encoding: none');
    header("Content-type: application/vnd-ms-excel");
    header("Content-type: application/x-msexcel");
    header("Content-Disposition: attachment; filename={$filename}.xls");
@endphp
<style>
    .text-center{
        vertical-align: middle; 
        text-align: center;
    }
    .hidden{
        display:none !important;
        visibility: hidden;
    }
    .text-left{
        text-align: left;
    }
    .float-right{
        float: right;
    }
    table {
        border-collapse: collapse;
    }

    table, th, td {
        border: 1px solid black;
    }
</style>
<script>
    let clonedTable = $("#filterdata").clone();
    clonedTable.find('[style*="display:none"]').remove();
</script>
<table width="100%" class="table default-table table-solid" id="warehouseRequestThawing" >
    <thead style="text-align: center">
        <tr>
            <th>No</th> 
            <th>ThawingID</th>
            <th>Tanggal Request</th>
            <th>Tanggal Input Request</th>
            <th>Item</th>
            <th>Qty thawing</th>
            <th>Berat thawing</th>
            <th>Request Qty</th>
            <th>Request berat</th>
        </tr>
    </thead>
    <tbody style="text-align: center">
        @php
            $item_qty = 0;
            $item_berat = 0;
            $total_qty = 0;
            $total_berat = 0;
            $total_item_setuju = 0;  
            $total_berat_setuju = 0; 
        @endphp
        @foreach ($data as $i => $row)
            
            <tr>
                <td>{{ ++$i }}</td>
                <td>TH-{{ $row->id }}</td>
                <td>{{ $row->tanggal_request ?? '' }}</td>
                <td>{{ $row->created_at }}</td>
                <td>
                    @foreach (json_decode($row->item) as $i => $item)
                     {{ App\Models\Item::find($item->item)->nama }}
                     <br>
                        @php
                            $total_qty += $item->qty;
                            $total_berat += $item->berat;
                        @endphp
                    @endforeach
                </td>
                <td>
                    @foreach (json_decode($row->item) as $i => $item)
                        {{ number_format($item->qty,2) }} 
                    <br>
                    @endforeach
                </td>
                <td>
                    @foreach (json_decode($row->item) as $i => $item)
                        {{ number_format($item->berat,2) }}
                        <br>
                    @endforeach
                </td>
                @foreach ($row->thawing_list as $list)
                        <td>
                            {{ number_format($list->sum_qty,2) }}
                        </td>
                @endforeach
                @foreach ($row->thawing_list as $list)
                        @if($list->sum_berat > $item->berat)
                            <td style="background-color: #fff782;">
                                {{ number_format($list->sum_berat,2) }}
                            </td>
                        @else
                            <td style="background-color: #bcfdbc">
                                {{ number_format($list->sum_berat,2) }}
                            </td>
                        @endif
                    @php
                        $total_item_setuju += $list->sum_qty;
                        $total_berat_setuju += $list->sum_berat;
                    @endphp
                @endforeach
            </tr>
            
        @endforeach
    </tbody>
    
    <tfoot>
        <tr>
            <td colspan="5" style="font-weight:bold;text-align:start;">Total</td>
            <td style="font-weight:bold;">
                {{$total_qty}} Pcs
            </td>
            <td style="font-weight:bold">
                {{$total_berat}} kg
            </td>
                <td style="background-color:#bcfdbc;font-weight:bold;">
                    {{$total_item_setuju}} Pcs
                </td>
                <td style="background-color: #bcfdbc;font-weight:bold;">
                    {{$total_berat_setuju}} kg
                </td>
        </tr>
    </tfoot>
</table>
