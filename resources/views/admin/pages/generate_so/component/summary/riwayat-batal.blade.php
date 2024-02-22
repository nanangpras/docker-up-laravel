@php
    $jsondata   = App\Models\Adminedit::where('table_id', $id)->where('table_name', 'marketing_so')->get();
    $json       = [];
    $dataedit   = [];
    $lists      = [];
@endphp
@foreach ($jsondata as $key => $row)
<table class="table default-table">
    <thead>
        <tr>
            <th>No</th>
            @if($key == 0)
                <th>Waktu SO </th>
            @else
                <th>Waktu Edit </th>
            @endif
            <th>Riwayat</th>
            <th>Tanggal SO</th>
            <th>Tanggal Kirim</th>
            <th>Customer</th>
            <th>PO Number</th>
            <th>Memo</th>
            <th>User</th>
        </tr>
    </thead>
    <tbody>
        @php
            $json[]     = json_decode($row->data, true);
            $dataedit[] = $row->content;
        @endphp
        
        @if(isset($json[$key]['header']))
            <tr>
                <td>{{ $key+1 }}</td>
                @if($key == 0)
                <td>{{ date('d-m-Y H:i:s',strtotime($json[$key]['header']['created_at'])) }}</td>
                @else
                <td>{{ $row->created_at }}</td>
                @endif
                <td @if ($row->content == 'Penghapusan Item')
                    style="background-color: #fde0dd"
                @endif>{{ $row->content }}</td>
                <td>{{ $json[$key]['header']['tanggal_so'] ?? '#' }} </td>
                <td>{{ $json[$key]['header']['tanggal_kirim'] ?? '#' }} </td>
                <td>{{ App\Models\Customer::logsocustomer($json[$key]['header']['customer_id']) }} </td>
                <td
                @if($key > 0)
                    @if($json[$key]['header']['po_number'] ?? FALSE && $json[$key-1]['header']['po_number'] ?? FALSE)
                        @if(isset($json[$key]['header']['po_number']) != isset($json[$key-1]['header']['po_number']))
                            style="background-color: #fde0dd"
                        @endif
                    @endif
                @endif
                >
                {{ $json[$key]['header']['po_number'] ?? "-" }} 
                </td>
                <td
                    @if($key > 0)
                        @if($json[$key]['header']['memo'] ?? FALSE && $json[$key-1]['header']['memo'] ?? FALSE)
                            @if(isset($json[$key]['header']['memo']) != isset($json[$key-1]['header']['memo']))
                                style="background-color: #fde0dd"
                            @endif
                        @endif
                    @endif
                    >{{ $json[$key]['header']['memo'] ?? '-' }} 
                </td>
                <td>
                    {{ App\User::find($json[$key]['header']['user_id'])->name ?? '' }}
                </td>
            </tr>
        @endif
    </tbody>
</table>
@endforeach