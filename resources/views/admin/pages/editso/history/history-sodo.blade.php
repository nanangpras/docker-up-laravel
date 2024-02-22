@php
$jsondata = App\Models\Adminedit::where('table_id', $orderid)
    ->where('table_name', 'orders')
    ->where('type', 'edit')
    ->get();
$json = [];
$dataedit = [];
$lists = [];
@endphp
@foreach ($jsondata as $key => $row)
    <table class="table default-table">
        <thead>
            <tr>
                <th>No</th>
                @if ($key == 0)
                    <th>Waktu SO </th>
                @else
                    <th>Waktu Edit </th>
                @endif
                <th>Riwayat</th>
                <th>Tanggal Kirim</th>
                <th>Tanggal SO</th>
                <th>NO SO</th>
                <th>NO DO</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @php
                $json[] = json_decode($row->data, true);
                $dataedit[] = $row->content;
            @endphp

            @if (isset($json[$key]['header']))
                <tr>
                    <td>{{ $key + 1 }}</td>
                    @if ($key == 0)
                        <td>{{ date('d-m-Y H:i:s', strtotime($json[$key]['header']['created_at'])) }}</td>
                    @else
                        <td>{{ $row->created_at }}</td>
                    @endif
                    <td @if ($row->content == 'Penghapusan Item') style="background-color: #fde0dd" @endif>
                        {{ $row->content }}</td>
                    <td @if ($json[$key - 1]['header']['tanggal_kirim'] ?? (false && $json[$key]['header']['tanggal_kirim'] ?? false)) @if ($json[$key]['header']['tanggal_kirim'] != $json[$key - 1]['header']['tanggal_kirim'])
                            style="background-color: #fde0dd" @endif
                        @endif>{{ $json[$key]['header']['tanggal_kirim'] }}
                    </td>
                    <td @if ($json[$key - 1]['header']['tanggal_so'] ?? (false && $json[$key]['header']['tanggal_so'] ?? false)) @if ($json[$key]['header']['tanggal_so'] != $json[$key - 1]['header']['tanggal_so'])
                            style="background-color: #fde0dd" @endif
                        @endif>{{ $json[$key]['header']['tanggal_so'] }}
                    </td>
                    <td>{{ $json[$key]['header']['no_so'] ?? '#' }} </td>
                    <td @if ($json[$key - 1]['header']['no_do'] ?? (false && $json[$key]['header']['no_do'] ?? false)) @if ($json[$key]['header']['no_do'] != $json[$key - 1]['header']['no_do'])
                                style="background-color: #fde0dd" @endif
                        @endif
                        >{{ $json[$key]['header']['no_do'] }}</td>
                    <td @if ($key > 0) @if ($json[$key - 1]['header']['status'] ?? (false && $json[$key]['header']['status'] ?? false))
                            @if ($json[$key]['header']['status'] != $json[$key - 1]['header']['status'])
                                style="background-color: #fde0dd" @endif
                        @endif
            @endif>
            @if ($json[$key]['header']['status'] == '10')
                SELESAI
            @elseif ($json[$key]['header']['status'] == '11')
                BATAL
            @else
                PENDING
            @endif
            </td>
            </tr>
@endif
</tbody>

</table>
@endforeach
<hr>
