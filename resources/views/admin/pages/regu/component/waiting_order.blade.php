@if (COUNT($data))
<div class="border p-2 mb-2">
    <table class="table mb-0 default-table">
        <thead>
            <tr>
                <th class="table-warning" colspan="6">SALES ORDER TERKAIT</th>
            </tr>
            <tr>
                <th>Tanggal Kirim</th>
                <th>Item</th>
                <th>Qty</th>
                <th>Berat</th>
                <th>Parting</th>
                <th>Bumbu</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $row)
            <tr>
                <td>{{ $row->tanggal_kirim }}</td>
                <td>{{ $row->nama_detail }}</td>
                <td>{{ $row->qty }}</td>
                <td>{{ $row->berat }}</td>
                <td>{{ $row->part }}</td>
                <td>{{ $row->bumbu }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
