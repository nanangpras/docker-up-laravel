@foreach ($produksi as $row)
<section class="panel">
    <div class="card-body p-2">
        <table class="table default-table">
            <thead>
                <tr>
                    <th colspan="2" class="text-uppercase">
                        {{ $row->tanggal }} | Kepala regu {{ $row->regu }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        Bahan Baku :
                        <table class="table default-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Item</th>
                                    <th>Qty</th>
                                    <th>Berat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $bb_qty     =   0 ;
                                    $bb_berat   =   0 ;
                                @endphp
                                @foreach ($row->listfreestock as $i => $bb)
                                @php
                                    $bb_qty     +=  $bb->qty ;
                                    $bb_berat   +=  $bb->berat ;
                                @endphp
                                <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>{{ $bb->item->nama }}
                                        <div>Ambil : <a target="_blank" href="{{ route('chiller.show', $bb->chiller_id) }}">{{ $bb->chiller_id }}</a></div>
                                    </td>
                                    <td class="text-right">{{ $bb->qty }}</td>
                                    <td class="text-right">{{ number_format($bb->berat, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2">Total</th>
                                    <th class="text-right">{{ $bb_qty }}</th>
                                    <th class="text-right">{{ number_format($bb_berat, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </td>

                    <td>
                        Hasil Produksi :
                        <table class="table default-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Item</th>
                                    <th>Qty</th>
                                    <th>Berat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $fg_qty     =   0 ;
                                    $fg_berat   =   0 ;
                                @endphp
                                @foreach ($row->freetemp as $i => $fg)
                                @php
                                    $fg_qty     +=  $fg->qty ;
                                    $fg_berat   +=  $fg->berat ;
                                    $id         =   $fg->freetempchiller->id ;
                                    $arr_fg     =   [] ;
                                @endphp
                                <tr>
                                    <td><a target="_blank" href="{{ route('chiller.show', $id) }}">{{ $id }}</a></td>
                                    <td>
                                        {{ $fg->item->nama }}
                                        <div>
                                            <ul class="m-0">
                                            @foreach ($fg->freetempchiller->alokasi_order as $ord)
                                            @php
                                                $retur      =   App\Models\ReturItem::select('retur_id')->where('orderitem_id', $ord->order_item_id)->get() ;
                                                $order      =   App\Models\Order::find($ord->order_id) ;
                                            @endphp
                                                <li>
                                                    <a target="_blank" href="{{url('admin/laporan/sales-order/'.$ord->bahanbborder->id)}}" target="_blank">{{$order->no_so ?? ""}}</a>
                                                    <ul>
                                                        <li><a class="text-danger" target="_blank" href="{{ route('editso.index', $order->id) }}">{{ $order->no_do }}</a></li>
                                                        @if (COUNT($retur))
                                                        <ul>
                                                            @foreach ($retur as $rtn)
                                                                <li>
                                                                    <a class='text-success' target="_blank" href='{{ route('retur.detail', $rtn->retur_id) }}'>
                                                                    @php
                                                                        $ns = \App\Models\Netsuite::select('response')->where('tabel_id', $rtn->retur_id)
                                                                            ->where('label', 'receipt_return')
                                                                            ->where('tabel', 'retur')
                                                                            ->first();
                                                                        if ($ns) {
                                                                            try {
                                                                                $resp = json_decode($ns->response, true);
                                                                                echo $resp[0]['message'] ;
                                                                            } catch (\Throwable $th) {
                                                                                echo $th->getMessage();
                                                                            }
                                                                        }
                                                                    @endphp
                                                                    </a>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                        @endif
                                                    </ul>
                                                </li>
                                            @endforeach
                                            </ul>
                                        </div>
                                    </td>
                                    <td class="text-right">
                                        {{ $fg->qty ?? 0 }}
                                    </td>
                                    <td class="text-right">
                                        {{ number_format($fg->berat, 2) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2">Total</th>
                                    <th class="text-right">{{ $fg_qty }}</th>
                                    <th class="text-right">{{ number_format($fg_berat, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>

        @if ($row->netsuite_id)
            @php

                $sub = App\Models\Netsuite::where('id', $row->netsuite_id)->with('data_children')->first();    // function loop_child($data_child){

            @endphp

            <h6>Netsuite Paket</h6>

            <table class="table default-table">
                <thead>
                    <tr>
                        <th>
                            <input type="checkbox" id="ns-checkall">
                        </th>
                        <th>ID</th>
                        <th>C&U Date</th>
                        <th>TransDate</th>
                        <th>Label</th>
                        <th>Activity</th>
                        <th>Location</th>
                        <th>IntID</th>
                        <th>Paket</th>
                        <th width="100px">Data</th>
                        <th width="100px">Action</th>
                        <th>Response</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>

                    @php
                        $netsuite = $sub;
                    @endphp
                    @if($netsuite ?? false)
                    @include('admin.pages.log.netsuite_one', ($netsuite = $sub))
                    @endif
                    @if($netsuite->data_children ?? false)
                    @include('admin.pages.log.netsuite_one', ($netsuite = $netsuite->data_children))
                    @php
                        $sub2 = App\models\Netsuite::where('id', $netsuite->data_children->id ?? null)->with('data_children')->first();    // function loop_child($data_child){
                    @endphp

                        @if($sub2 ?? false)
                        @include('admin.pages.log.netsuite_one', ($netsuite = $sub2))
                        @endif
                        @if($sub2->data_children ?? false)
                        @include('admin.pages.log.netsuite_one', ($netsuite = $sub2->data_children))
                        @endif
                    @endif



                </tbody>
            </table>
        @endif
    </div>
</section>
@endforeach

<div class="paginate">
    {{ $produksi->appends($_GET)->onEachSide(1)->links() }}
</div>

<script>
$('.paginate .pagination a').on('click', function(e) {
    e.preventDefault();
    $('#text-notif').html('Loading...');
    $('#topbar-notification').fadeIn();

    url = $(this).attr('href');
    $.ajax({
        url: url,
        method: "GET",
        success: function(response) {
            $('#data_view').html(response).after($('#topbar-notification').fadeOut())
        }

    });
});
</script>
