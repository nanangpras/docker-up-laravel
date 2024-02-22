@php
header('Content-Transfer-Encoding: none');
header("Content-type: application/vnd-ms-excel");
header("Content-type: application/x-msexcel");
header("Content-Disposition: attachment; filename=Export-Customers.xls");
@endphp

<table border="1">
    <thead>
        <tr>
            <th class="text-center" rowspan="2">Kode</th>
            <th class="text-center" rowspan="2">Nama</th>
            <th class="text-center" rowspan="2">Marketing</th>
            <th class="text-center" colspan="4">Order</th>
            <th class="text-center" rowspan="2">Status</th>
        </tr>
        <tr>
            <th class="text-center">Terakhir</th>
            <th class="text-center">Total</th>
            <th class="text-center">Alokasi</th>
            <th class="text-center">Pending</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($result as $row)
        <tr>
            <td>{{ $row->kode }}</td>
            <td>{{ $row->nama }}</td>
            <td>{{ $row->nama_marketing ?? '' }}</td>
            <td>{{ $row->tanggal_so ?? '' }}</td>
            <td class="text-center">{{ $row->total_order ?? '' }}</td>
            <td class="text-center">{{ $row->alokasi ?? '' }}</td>
            <td class="text-center {{ $row->pending ? 'table-warning' : '' }}">{{ $row->pending ?? '' }}</td>
            <td class="text-center {{ $row->deleted_at == NULL ? 'status status-success' : 'status status-danger' }}">{{ $row->deleted_at == NULL ? 'Aktif': 'Tidak Aktif' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
