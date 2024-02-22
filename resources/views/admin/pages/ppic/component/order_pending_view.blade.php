<div class="my-2">
    @foreach ($pending as $i => $val)
        @php
            $qty = 0;
            $berat = 0;
            $sum = 0;
        @endphp

        <table class="table default-table">
            <thead>
                <tr>
                    <th colspan="6">
                            {{ $val->no_so }} || {{ $val->nama }} <span class="pull-right"> {{ $val->sales_channel }} || {{date('d/m/y H:i:s', strtotime($val->created_at))}}</span>

                    </th>
                </tr>
                <tr>
                    <th width="35%">Item</th>
                    <th>Order</th>
                    <th>Fulfill</th>
                    <th>Persen</th>
                    <th width="15%">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach (Order::item_order($val->id) as $i => $item)
                    @php
                        $qty += $item->qty;
                        $berat += $item->berat;
                        $total = 0;
                        $totalberat = 0;
                        $persen = 0;
                        $persenberat = 0;
                        $idchill = '';
                    @endphp
                    @foreach (Order::bahan_baku($val->id, $item->id) as $bahan)
                        @php
                            $total += $bahan->bb_item;
                            $totalberat += $bahan->bb_berat;
                            $persen = $item->qty != 0 ? ($total / $item->qty) * 100 : 0;
                            $persenberat = $item->berat != 0 ? ($totalberat / $item->berat) * 100 : 0;
                            $idchill = $bahan->chiller_alokasi;
                        @endphp
                    @endforeach
                    <tr>
                        <td>{{ $item->nama_detail }}</td>
                        <td>
                            {{ $item->qty ?? '0' }} Pcs ||
                            {{ $item->berat ?? '0' }} Kg
                        </td>
                        <td>
                            {{ $item->fulfillment_qty ?? '0' }} Pcs ||
                            {{ $item->fulfillment_berat ?? '0' }} Kg
                        </td>
                        <td>{{ number_format($persen, 2) }}% || {{ number_format($persenberat, 2) }}%</td>
                        <td>{!!$val->status_order!!} </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    @endforeach
</div>

<div class="paginate_pending">
    {{ $pending->appends($_GET)->onEachSide(1)->links() }}
</div>

<script>
$('.paginate_pending .pagination a').on('click', function(e) {
    e.preventDefault();
    showNotif('Menunggu');

    url = $(this).attr('href');
    $.ajax({
        url: url,
        method: "GET",
        success: function(response) {
            $('#order_pending').html(response);
        }

    });
});
</script>

