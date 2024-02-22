@php
    $logDelete  = App\Models\Adminedit::where('table_id', $table_id)
                    ->where('table_name', 'order_items')
                    ->where('type', 'delete')
                    ->get();

    $json       = [];
    $jsonDelete = [];
    $lists      = [];

@endphp

@foreach ($logDelete as $key => $row)
    @php
        $json[]             = json_decode($row->data, true);
        $jsonDelete[]       = $row->content;
    @endphp

    @if (isset($json[$key]['list']))
        <table class="table default-table">
            <thead>
                <tr>
                    <th>ID Chiller</th>
                    <th>Nama</th>
                    <th>Qty</th>
                    <th>Berat</th>
                    <th>Tanggal Delete</th>
                </tr>
            </thead>
            <tbody>
                @for ($i = 0; $i < COUNT($logDelete); $i++)
                    <tr>
                        @if ($json[$key]['list']['proses_ambil'] == 'frozen')
                        <td><a href="{{ route('warehouse.tracing', $json[$key]['list']['chiller_out']) }}"target="_blank">{{ $json[$key]['list']['chiller_out'] }}</a> </td>
                        @else
                        <td><a href="{{ route('chiller.show', $json[$key]['list']['chiller_out']) }}"target="_blank">{{ $json[$key]['list']['chiller_out'] }}</a> </td>
                        @endif
                        <td>{{ $json[$key]['list']['nama'] }}</td>
                        <td>{{ $json[$key]['list']['bb_item'] }}</td>
                        <td>{{ $json[$key]['list']['bb_berat'] }}</td>
                        <td>{{ $row->created_at }}</td>
                @endfor
            </tbody>
        </table>    
    @endif

@endforeach
