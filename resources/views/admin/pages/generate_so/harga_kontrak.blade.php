@if (COUNT($data))
<section class="panel mt-2">
    <div class="card-body p-2">
        <table class="table table-sm table-striped m-0">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Harga</th>
                    <th>Unit</th>
                    <th>Mulai</th>
                    <th>Selesai</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $row)
                <tr class="{{ strtotime("-3 days", strtotime($row->sampai)) < time() ? 'text-danger' : '   ' }}">
                    <td>{{ $row->item->nama }}</td>
                    <td class="text-right">{{ number_format($row->harga) }}
                        <input id="harga-{{$row->item->id}}" value="{{$row->harga}}" type="hidden">
                        @if ($row->unit == 'Kg')
                        <input id="unit-{{$row->item->id}}" value="1" type="hidden">
                        
                        @else
                        <input id="unit-{{$row->item->id}}" value="2" type="hidden">
                            
                        @endif
                    </td>
                    <td>{{ $row->unit }}</td>
                    <td>{{ $row->mulai }}</td>
                    <td>{{ $row->sampai }}</td>
                </tr>
                @if ($row->keterangan)
                <tr>
                    <td colspan="5" class="small">Keterangan :<br>{{ $row->keterangan }}</td>
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
    </div>
</section>
@endif
