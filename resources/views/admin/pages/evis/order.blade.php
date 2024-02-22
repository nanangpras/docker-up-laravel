<div class="accordion mb-4" id="accrodeing">
    @foreach ($order as $i => $val)
        <div class="card">
            <div class="card-header" id="accrode{{ $val->id }}">
                <span data-toggle="collapse" data-target="#collapse{{ $val->id }}" aria-expanded="true" aria-controls="collapse{{ $val->id }}">
                Customer : {{ $val->nama }}
                </span>
            </div>

            <div id="collapse{{ $val->id }}" class="collapse" aria-labelledby="accrode{{ $val->id }}" data-parent="#accrodeing">
                <div class="card-body">
                    <div class="row">
                        @csrf <input type="hidden" name="xcode" id="xcode"
                            value="{{ $val->id }}">
                        <div class="col-6">
                            @php
                                $qty = 0;
                                $berat = 0;
                            @endphp
                            @foreach (Order::item_order($val->id, 'sampingan') as $i => $item)
                                @php
                                    $qty += $item->qty;
                                    $berat += $item->berat;
                                    $total = 0;
                                    $persen = 0;
                                    $idchill = '';
                                @endphp
                                @foreach (Order::bahan_baku($val->id, $item->id) as $bahan)
                                    @php
                                        $total += $bahan->bb_berat;
                                        $persen = $item->berat != 0 ? ($total / $item->berat) * 100 : 0;
                                        $idchill = $bahan->chiller_alokasi;
                                    @endphp
                                @endforeach
                                <div class="radio-toolbar">
                                    <input type="radio" id="do-{{ $item->id }}"
                                        onclick='' data-jenis='' value="{{ $item->id }}"
                                        name="purchase" required>
                                    <label for="do-{{ $item->id }}" class="text-left">
                                        {{ $item->nama_detail }}
                                        <span class=" pull-right">{{ $item->berat }} |
                                            {{ $total }} |
                                            {{ number_format($persen, 2) }} %
                                        </span>
                                    </label>
                                    </>
                                </div>
                            @endforeach
                        </div>
                        <div class="col-6">
                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Qty</label>
                                        <div class="input-group input-group-lg">
                                            <input type="text" style="height: 75px; font-size: 30pt" value="{{ number_format($qty) }}" name="jumlah" class="text-right form-control" id="qty{{ $val->id }}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Berat</label>
                                        <div class="input-group input-group-lg">
                                            <input type="text" style="height: 75px;font-size: 30pt" value="{{ number_format($berat, 2) }}" name="berat" class="text-right form-control" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

{{ $order->appends(\Illuminate\Support\Facades\Request::except('page'))->links() }}

<script>
    $('.pagination a').on('click', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        $.ajax({
            url: url,
            method: "GET",
            success: function(response) {
                $('#show_order').html(response);
            }

        });
    });

</script>
