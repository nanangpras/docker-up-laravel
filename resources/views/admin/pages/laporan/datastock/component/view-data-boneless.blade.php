<table class="default-table">
    <thead>
        <tr>
            <th class="center" width="5%"  rowspan="3" >No</th>
            <th class="center" width="45%" rowspan="3" >PRODUK BONELESS</th>
            <th class="center" colspan="2" >STOK</th>
        </tr>
        <tr>
            <th class="center"> Ekor</th>
            <th class="center"> Kg</th>
        </tr>
    </thead>
    <tbody>
        @if(count($data['databoneless']) > 0)
            @foreach ($data['databoneless'] as $row)
            <tr>
                <td class="center">{{ $loop->iteration}}</td>
                <td class="center">{{ $row['namaitem'] }}</td>
                <td class="center">{{ number_format($row['qty'],1) }}</td>
                <td class="center">{{ number_format($row['berat'],1) }}</td>
            </tr>
            @endforeach
        @else
            <tr>
                <td class="center" colspan="4"> Tidak ada data</td>
            </tr>
        @endif
    </tbody>
</table>