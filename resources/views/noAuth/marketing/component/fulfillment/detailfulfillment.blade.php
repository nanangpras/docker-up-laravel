<div class="table-responsive">
    <table class="table default-table" width="1200px">
        <thead>
            <tr>
                <th width="250px" rowspan="2" class="text-center"> Nama Item</th>
                <th colspan="2" class="text-center"> Item Order</th>
                <th colspan="2" class="text-center"> Fulfillment</th>
            </tr>
            <tr>
                <th class="text-center"> Qty</th>
                <th class="text-center"> Berat</th>
                <th class="text-center"> Qty</th>
                <th class="text-center"> Berat</th>
            </tr>
        </thead>
        <tbody>
            @foreach($fulfillment_detail as $detail)
            <tr>
                <td class="text-center">{{ $detail->item->nama }}</td>
                <td class="text-center">{{ $detail->qty ?? '0' }}</td>
                <td class="text-center">{{ $detail->berat ?? '0' }}</td>
                <td class="text-center">{{ $detail->fulfillment_qty ?? '0' }}</td>
                <td class="text-center">{{ $detail->fulfillment_berat ?? '0' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>