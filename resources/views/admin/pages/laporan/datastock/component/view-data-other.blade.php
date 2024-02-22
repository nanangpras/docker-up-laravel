<table class="default-table">
    <thead>
        <tr>
            <th class="center" width="5%" rowspan="3">No</th>
            <th class="center" colspan="3" >LAIN-LAIN</th>
        </tr>
        <tr>
            <th class="center"> Nama Item </th>
            <th class="center"> Ekor</th>
            <th class="center"> Kg</th>
        </tr>
    </thead>
    <tbody>
        @if(count($data['dataother']) > 0)
            @foreach($data['dataother'] as $row )
            <tr>
                <td class="center">{{ $loop->iteration }}</td>
                <td class="center">{{ $row['namaitem'] }}</td>
                <td class="center">{{ number_format($row['qty'],2) }}</td>
                <td class="center">{{ number_format($row['berat'],2) }}</td>
            </tr>
            @endforeach
        @else
            <tr>
                <td class="center" colspan="4"> Tidak ada data</td>
            </tr>
        @endif
    </tbody>
</table>