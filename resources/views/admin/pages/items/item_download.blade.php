@php
    header('Content-Transfer-Encoding: none');
    header("Content-type: application/vnd-ms-excel");
    header("Content-type: application/x-msexcel");
    header("Content-Disposition: attachment; filename=Data-Item-Download-.xls");
@endphp

<table class="table default-table table-small table-hover"  border="1">
    <thead>
        <tr>
            <th>No</th>
            <th>App ID</th>
            <th>Nama</th>
            <th>Subsidiary</th>
            <th>Netsuite ID</th>
            <th>SKU</th>
            <th>Kategori</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($dataunduh as $i => $row)
            <tr>
                <td>{{++$i}}</td>
                <td>{{ $row->id }}</td>
                <td>{{ $row->nama }}</td>
                <td>{{ $row->subsidiary }}</td>
                <td>{{ $row->netsuite_internal_id }}</td>
                <td>{{ $row->sku }}</td>
                <td>{{ $row->itemkat->nama ?? '' }}</td>
                <td>{{ $row->status == 1 ? 'Active' : 'Inactive' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>