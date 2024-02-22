<div class="modal-body">
    <label><b>NO SO : {{ $order->no_so }} {{ $order->status }}</b></label>
    <div class="row">
        <div class="col-md-12">
            <table class="table default-table">
                <thead>
                    <tr>
                        <td>No</td>
                        <td>Item</td>
                        <td>Qty</td>
                        <td>Berat</td>
                    </tr>
                </thead>
                <tbody>
                    @if ($order->status == 3)
                        @php
                            $getDataOrders  = App\Models\Order::where('no_so', $order->no_so)->first();
                            if ($getDataOrders) {

                                $getFulfillment = App\Models\Bahanbaku::where('order_id', $getDataOrders->id)->get();

                            }
                        @endphp


                        @if (isset($getFulfillment))
                            @foreach ($getFulfillment as $item)

                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->nama }}</td>
                                <td>{{ $item->bb_item }}</td>
                                <td>{{ $item->bb_berat }}</td>
                            </tr>

                            @endforeach
                        @endif

                    @else
                        @php
                            $getDataMarketingSOList = App\Models\MarketingSOList::where('marketing_so_id', $order->id)->get();
                        
                        @endphp
            
                        @if (isset($getDataMarketingSOList))
                            @foreach ($getDataMarketingSOList as $itemList)

                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $itemList->item_nama }}</td>
                                <td>{{ $itemList->qty }}</td>
                                <td>{{ $itemList->berat }}</td>
                            </tr>

                            @endforeach
                        @endif

                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
