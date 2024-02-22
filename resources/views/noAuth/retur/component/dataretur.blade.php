<table class="table default-table">
    <thead>
        <tr>
            <th width=10px>No</th>
            <th>Tanggal</th>
            <th>Customer</th>
            <th>No SO</th>
            <th>Item</th>
            <th>Tujuan</th>
            <th>Penanganan</th>
            <th>Retur Qty</th>
            <th>Retur Berat</th>
            <th>Alasan</th>
            <th>Kategori</th>
            <th>Satuan</th>
            <th>Sopir</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>

        @foreach ($retur_list as $i => $row)
            <tr>
                <td>{{ ++$i }}</td>
                <td>{{ date('d/m/Y', strtotime($row->to_retur->tanggal_retur)) ?? '' }}</td>
                <td>{{ $row->to_retur->to_customer->nama ?? '' }}</td>
                <td>{{ $row->to_retur->no_so ?? '#NonSO' }}<br>{{ $row->to_retur->no_ra ?? 'Tidak ada RA' }}</td>
                <td>{{ $row->to_item->nama ?? '' }}</td>
                <td>{{ $row->unit ?? '' }}</td>
                <td>{{ $row->penanganan ?? '' }}</td>
                <td>{{ $row->qty ?? '' }}</td>
                <td>{{ $row->berat ?? '' }}</td>
                <td>{{ $row->catatan }}</td>
                <td>{{ $row->kategori }}</td>
                <td>{{ $row->satuan }}</td>
                <td>{{ $row->todriver->nama ?? '' }}</td>
                <th>
                    @if ($row->status == 1)
                        <span class="status status-danger">Belum Selesai</span>
                    @else
                        <span class="status status-success">Selesai</span>
                    @endif
                </th>
            </tr>
        @endforeach
    </tbody>
</table>
