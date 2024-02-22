@php
    header('Content-Transfer-Encoding: none');
    header('Content-type: application/vnd-ms-excel');
    header('Content-type: application/x-msexcel');
    header('Content-Disposition: attachment; filename=Download-Item-Purchase-Request.xls');
@endphp
<style>
    .tengah {
        vertical-align: middle;
        text-align: center;
    }
</style>

<table border="1">

    <thead>
        <tr class="text-center">
            <th class="text">NO</th>
            <th class="text">SKU</th>
            <th class="text">INTERNAL ID</th>
            <th class="text">NAMA ITEM</th>
            <th class="text">KATEGORI</th>
            <th class="text">SUBSIDIARY</th>
            <th class="text">STATUS</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($item as $row)
            <td>{{ $loop->iteration }}</td>
            <td>{{ $row->sku }}</td>
            <td>{{ $row->netsuite_internal_id }}</td>
            <td>{{ $row->nama }}</td>
            <td>{{ $row->itemkat->nama }}</td>
            <td>{{ $row->subsidiary }}</td>
            <td>{{ $row->status == 1 ? 'Active' : 'Inactive' }}</td>
        @endforeach
    </tbody>
</table>
