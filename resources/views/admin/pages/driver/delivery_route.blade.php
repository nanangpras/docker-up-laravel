
@if ($ekspedisi)
<table class="table default-table table-small">
    <thead>
        <th>Nama</th>
        <th>Alamat</th>
        <th>Item</th>
        <th>Ekor/Pcs/Pack</th>
        <th>Berat</th>
        <th>#</th>
    </thead>
    <tbody>
        @foreach ($route as $row)
        <tr>
            <td>{{ $row->nama }}</td>
            <td>{{ $row->alamat }}</td>
            <td>{{ $row->ruteorderitem->nama_detail }}</td>
            <td>{{ $row->ruteorderitem->qty }}</td>
            <td>{{ $row->ruteorderitem->berat }} kg</td>
            <td>
                <button data-id='{{ $row->id }}' class="batal_route btn btn-danger btn-sm">Batalkan</button>
            </td>
        </tr>
    @endforeach

    </tbody>
</table>
@endif
