<table class="table default-table">
    <thead>
        <tr>
            <th class="center" rowspan="2" width="5%">No</th>
            <th class="center" rowspan="2">Tanggal Produksi</th>
            <th class="center" colspan="4">Produk Retur</th>
        </tr>
        <tr>
            <th>Customer</th>
            <th> Nama Item </th>
            <th> Ekor </th>
            <th> Kg </th>
        </tr>
    </thead>
    <tbody>
        @if(count($data['dataretur']) > 0)
            @foreach($data['dataretur'] as $row)
            <tr>
                <td class="center">{{ $loop->iteration }}</td>
                <td class="center">{{ $row['tanggal'] }}</td>
                <td class="center">{{ $row['customer'] }}</td>
                <td class="center">{{ $row['namaitem'] }}</td>
                <td class="center">{{ $row['qty'] }}</td>
                <td class="center">{{ $row['berat'] }}</td>
            </tr>
            @endforeach
        @else
            <tr>
                <td class="center" colspan="6"> Tidak ada data</td>
            </tr>
        @endif
    </tbody>
</table>