@php
    $jsondata = App\Models\Adminedit::where('table_id', $id)->where('table_name', 'lpah')->where('type','edit')->get();
    $itembaru = App\Models\Lpah::where('id',$id)->get();
    $json = [];
    $dataedit =[];
@endphp
<b>Item Lama</b>
<table class="table default-table">
    <thead>
        <tr>
            <th>NO</th>
            <th>WAKTU DIBUAT</th>
            <th>TIMBANG</th>
            <th>BERAT</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($jsondata as $key => $row)
        @php
            $json[] = json_decode($row->data, true);
            $dataedit[] = $row->content;
        @endphp
        <tr>
            <td>{{$key +1}}</td>
            <td>{{ date('d-m-Y H:i:s',strtotime($json[$key]['item_lama']['created_at'])) }}</td>
            <td>{{ $json[$key]['item_lama']['type'] }}</td>
            <td>{{ $json[$key]['item_lama']['berat'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<br>
<b>Item Sekarang</b>
<table class="table default-table">
    <thead>
        <tr>
            <th>NO</th>
            <th>WAKTU EDIT</th>
            <th>TIMBANG</th>
            <th>BERAT</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($itembaru as $row)
        
        <tr>
            <td>{{$loop->iteration}}</td>
            <td>{{ date('d-m-Y H:i:s',strtotime($row->updated_at))  }}</td>
            <td>{{ $row->type }}</td>
            <td>{{ $row->berat }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
