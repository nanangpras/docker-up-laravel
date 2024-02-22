@php
    header('Content-Transfer-Encoding: none');
    header("Content-type: application/vnd-ms-excel");
    header("Content-type: application/x-msexcel");
    header("Content-Disposition: attachment; filename=Summary Thawing Request " . ($mulai ?? date('Y-m-d')) . " - " . ($sampai ?? date('Y-m-d')) . ".xls");
@endphp

<table border="1">
    <thead>
        <tr>
            <th>No</th>
            <th>GudangID</th>
            <th>ThawingID</th>
            <th>Tanggal Outbound</th>
            <th>Nama</th>
            <th>Sub</th>
            <th>Ekor/Pcs/Pack</th>
            <th>Berat </th>
        </tr>
    </thead>
    <tbody>
        @php
            $total_qty = 0;
            $total_berat = 0;
        @endphp
        @foreach ($thawing as $i => $val)
            <tr>
                <td>{{ ++$i }}</td>
                <td>ID-{{ $val->item_id }}</td>
                <td>@if ($val->thawing_id) TH-{{ $val->thawing_id }} @endif</td>
                <td>{{ $val->created_at }}</td>
                <td>{{ $val->gudang->nama }}</td>
                <td>{{ $val->gudang->sub_item ?? 'Free Stock' }}</td>
                <td>{{ number_format($val->qty) ?: '0' }} ekor</td>
                <td>{{ number_format($val->berat, 2) ?: '0' }} Kg</td>
            </tr>
            @php
                $total_qty += $val->qty;
                $total_berat += $val->berat;
            @endphp
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="6">Total</td>
            <td>{{ number_format($total_qty) ?: '0' }} ekor</td>
            <td>{{ number_format($total_berat, 2) ?: '0' }} Kg</td>
            
        </tr>
    </tfoot>
</table>
