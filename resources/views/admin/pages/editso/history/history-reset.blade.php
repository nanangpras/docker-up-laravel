@php
$log_reset = App\Models\Adminedit::where('table_id', $table_id)
    ->where('table_name', 'order_bahan_baku')
    ->where('type', 'reset')
    ->get();
$json = [];
$dataedit = [];
$lists = [];
@endphp
@foreach ($log_reset as $key => $row)
    @php
        $json[] = json_decode($row->data, true);
        $dataedit[] = $row->content;
    @endphp

    @if (isset($json[$key]['header']))
        <table class="table default-table">
            <thead>
                <tr>
                    <th>NO DO</th>
                    <th>ID Chiller</th>
                    <th>Nama</th>
                    <th>Qty</th>
                    <th>Berat</th>
                </tr>
            </thead>
            <tbody>
                @for ($i = 0; $i < count($json[$key]['header']); $i++)
                    <tr>
                        <td>{{ $json[$key]['header'][$i]['no_do'] }}</td>
                        @if ($json[$key]['header'][$i]['proses_ambil'] == 'frozen')
                        <td><a href="{{ route('warehouse.tracing', $json[$key]['header'][$i]['chiller_out']) }}"target="_blank">{{ $json[$key]['header'][$i]['chiller_out'] }}</a> </td>
                        @else
                        <td><a href="{{ route('chiller.show', $json[$key]['header'][$i]['chiller_out']) }}"target="_blank">{{ $json[$key]['header'][$i]['chiller_out'] }}</a> </td>
                        @endif
                        <td>{{ $json[$key]['header'][$i]['nama'] }}</td>
                        <td>{{ $json[$key]['header'][$i]['bb_item'] }}</td>
                        <td>{{ $json[$key]['header'][$i]['bb_berat'] }}</td>
                @endfor
            </tbody>
        </table>
    @endif
@endforeach
