<div class="border my-2 p-1">
    <div class="row">
        <div class="col">
            <div class="form-group">
                Item
                <div>{{ $order->nama_detail }}</div>
                <div class="bg-light mt-2">
                    <div class="row">
                        <div class="col">
                            Qty : {!! ($order->qty - $qty) > 0 ? "<b class='text-danger'>KURANG</b>" : (($order->qty - $qty) == 0 ? "<b class='text-success'>CUKUP</b>" : "<b class='text-warning'>LEBIH</b>" ) !!}
                        </div>
                        <div class="col">
                            Berat : {!! ($order->berat - $berat) > 0 ? "<b class='text-danger'>KURANG</b>" : (($order->berat - $berat) == 0 ? "<b class='text-success'>CUKUP</b>" : "<b class='text-warning'>LEBIH</b>" ) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-auto">
            <div class="form-group">
                Qty
                <div>{{ number_format($order->qty) }} pcs</div>
                <div class="bg-light mt-2">{{ number_format($order->qty < 1 ? 0 : ($order->qty - $qty)) }} pcs</div>
            </div>
        </div>
        <div class="col-auto">
            Berat
            <div>{{ number_format($order->berat, 2) }} kg</div>
            <div class="bg-light mt-2">{{ number_format(($order->berat - $berat), 2) }} kg</div>
        </div>
    </div>
</div>
