<div class="accordion" id="accordionOrderPending">

    {{-- <div id="cart"></div> --}}

    @if (count($pending) == '0')
        <div class="alert alert-danger">Item Order Belum Tersedia</div>
    @endif
    <section class="panel">
        <div class="card-body">
            <table class="table default-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th width="100px">Nama</th>
                        <th>Item</th>
                    </tr>
                </thead>
                <tbody>
                    {{ $pending }}
                    @foreach ($pending as $i => $full)
                        @php
                            $berat = 0;
                            $item = 0;
                        @endphp
                        @foreach ($full->order_regu($full->id, 'evis') as $tot)
                            @php
                                $berat = $berat + $tot->berat;
                                $item = $item + $tot->qty;
                            @endphp
                        @endforeach
                        <tr>
                            <td>{{ $loop->iteration + ($pending->currentpage() - 1) * $pending->perPage() }}</td>
                            <td>
                                {{ $full->no_so }} <br> {{ $full->nama }} <br>
                                {{ date('d/m/y H:i:s', strtotime($full->created_at)) }}

                            </td>
                            <td>
                                <table class="table default-table">
                                    <thead>
                                        <tr>
                                            <th>Nama</th>
                                            <th>Qty</th>
                                            <th>Berat</th>
                                            <th>Part</th>
                                            <th>Bumbu</th>
                                            <th>Memo</th>
                                            <th>#</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($full->order_regu($full->id, 'evis') as $detail)
                                            <tr>
                                                <td>{{ $detail->item->sku }} {{ $detail->item->nama }}</td>
                                                <td>{{ number_format($detail->qty, 0) }}</td>
                                                <td>{{ number_format($detail->berat, 2) }} kg</td>
                                                <td>
                                                    @if ($detail->part != '')
                                                        {{ $detail->part }}
                                                    @endif
                                                </td>
                                                <td>{{ $detail->bumbu }}</td>
                                                <td>{{ $detail->memo }}</td>
                                                <td>
                                                    @if ($detail->fulfillment_berat > 0)
                                                        <span class="status status-success">Terpenuhi</span>
                                                    @endif
                                                    @if ($detail->fulfillment_berat == 0)
                                                        <span class="status status-danger">Pending</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <br>
    {{ $pending->appends(\Illuminate\Support\Facades\Request::except('page'))->links() }}
</div>

<script>
    $('.pagination a').on('click', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        $.ajax({
            url: url,
            method: "GET",
            success: function(response) {
                $('#list_order').html(response);
            }

        });
    });
</script>

<style>
    #accordionOrderPending .card .card-header {
        padding: 8px;
        text-align: left;
        border-bottom: 0px;
        background: #fafafa;
    }

    #accordionOrderPending .card a {
        color: #000000;
        padding: 0px;
    }

</style>
