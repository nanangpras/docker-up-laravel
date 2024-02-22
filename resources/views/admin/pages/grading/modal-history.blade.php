<table class="table table-default text-center" id="history-table" width="100%">
    <thead>
        <tr>
            <th>No.</th>
            <th>Tanggal Lama</th>
            <th>Tanggal Baru</th>
        </tr>
    </thead>
    <tbody>
        @if (!$histories))
            <tr>
                <td colspan="3">Data Kosong</td>
            </tr>
        @else
            @foreach ($histories as $i => $history)
                <tr>
                    <td>{{ ++$i }}</td>
                    <td>
                    {{ json_decode($history->data)->data_lama }}
                    </td>
                    <td>
                        {{ json_decode($history->data)->data_baru }}
                    </td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>