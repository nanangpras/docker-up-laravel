<table class="default-table">
    <thead>
        <tr>
            <th class="center" width="5%" rowspan="3">No</th>
            <th class="center" colspan="4" >SISA PENYIAPAN</th>
        </tr>
        <tr>
            <th class="center"> Customer </th>
            <th class="center"> Item </th>
            <th class="center"> Ekor</th>
            <th class="center" width="10%"> Kg</th>
        </tr>
    </thead>
    <tbody>
        @if(count($data['datafulfillment']) > 0)
            @foreach($data['datafulfillment'] as $row)
            <tr>
                <td class="center">{{ $loop->iteration }}</td>
                <td class="center">{{ $row['customer'] }}</td>
                <td class="center">{{ $row['namaitem'] }}</td>
                <td class="center">{{ $row['qty'] }}</td>
                <td class="center">{{ $row['berat'] }}</td>
            </tr>
            @endforeach
        @else
            <tr>
                <td class="center" colspan="5"> Tidak ada data</td>
            </tr>
        @endif
    </tbody>
</table>