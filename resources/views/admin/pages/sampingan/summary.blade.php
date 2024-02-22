<div class="table-responsive">
    <table class="table default-table" width="100%">
        <thead>
            <th>No</th>
            <th>Nama Order</th>
            <th>Nama Item</th>
            <th>Ekor/Pcs/Pack</th>
            <th>Berat</th>
        </thead>
        <tbody>
            @foreach ($summary as $i => $summ)
                <tr>
                    <td>{{ ++$i }}</td>
                    <td>{{ $summ->chillerorderbb->bahanbborder->nama ?? "#" }}</td>
                    <td>{{ $summ->item_name }}</td>
                    <td>{{ number_format($summ->qty_item ?? '0') }}</td>
                    <td>{{ number_format(($summ->berat_item ?? '0'), 2) }} Kg</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
