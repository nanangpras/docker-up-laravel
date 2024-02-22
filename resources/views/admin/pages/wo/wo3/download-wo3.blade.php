@php
    header('Content-Transfer-Encoding: none');
    header("Content-type: application/vnd-ms-excel");
    header("Content-type: application/x-msexcel");
    header("Content-Disposition: attachment; filename=WO3-Download-".$tanggal.".xls");
@endphp

<table class="table default-table table-small table-hover"  border="1">
    <thead>
        <tr>
            <th>No</th>
            <th>Item</th>
            <th>Tanggal</th>
            <th>Asal</th>
            <th>Qty Awal</th>
            <th>Berat Awal</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($abf as $i => $row)
            <tr>
                <td>{{++$i}}</td>
                <td>{{$row->item_name}}</td>
                <td>{{date('d/m/Y', strtotime($row->tanggal_masuk))}}</td>
                <td>{{$row->asal}}</td>
                <td>{{ $row->qty_awal ?: '0' }}</td>
                <td>{{ $row->berat_awal ?: '0' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>