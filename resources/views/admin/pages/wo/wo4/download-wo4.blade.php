@php
header('Content-Transfer-Encoding: none');
header('Content-type: application/vnd-ms-excel');
header('Content-type: application/x-msexcel');
header('Content-Disposition: attachment; filename=WO4-Download-' . $tanggal . '.xls');
@endphp

<table class="table default-table table-small table-hover" border="1">
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Item</th>
            <th>Pcs</th>
            <th>Kg</th>
            <th>Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($thawing as $i => $row)
            <tr>
                <td>{{ ++$i }}</td>
                <td>{{ $row->tanggal_request }}</td>
                <td>
                    @foreach (json_decode($row->item) as $i => $item)
                        <div class="border-bottom p-1">
                            {{ ++$i }}. {{ App\Models\Item::find($item->item)->nama }}
                        </div>
                    @endforeach
                </td>
                <td>
                    @foreach (json_decode($row->item) as $i => $item)
                        <div class="border-bottom p-1">
                            <span class="status status-success">{{ number_format($item->qty) }} Pcs</span>

                        </div>
                    @endforeach
                </td>
                <td>
                    @foreach (json_decode($row->item) as $i => $item)
                        <div class="border-bottom p-1">
                            <span class="status status-success">{{ number_format($item->berat, 2) }} Kg</span>

                        </div>
                    @endforeach
                </td>
                <td>
                    @foreach (json_decode($row->item) as $i => $item)
                        <div class="border-bottom p-1">
                            <span class="status status-success">{{ $item->keterangan ?? '' }}</span>

                        </div>
                    @endforeach
                </td>
            </tr>
            <tr>
                <td colspan="6">
                    @foreach ($row->thawing_list as $list)
                        <div class="border-bottom p-1">
                            <div>TW-{{ $list->id }}. {{ $list->gudang->nama }}</div>
                            <span class="p-1 status status-success">{{ number_format($list->qty) }} pcs</span> <span
                                class="p-1 status status-info">{{ number_format($list->berat, 2) }} kg</span>
                        </div>
                    @endforeach
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
