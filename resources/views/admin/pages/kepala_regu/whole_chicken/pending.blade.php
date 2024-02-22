<div class="accordion mb-4" id="accrode">
    @foreach ($pending as $i => $val)
    <div class="card">
        <div class="card-header" id="heading{{ $val->id }}">
            <span data-toggle="collapse" data-target="#collapse{{ $val->id }}" aria-expanded="true" aria-controls="collapse{{ $val->id }}">
            Customer : {{ $val->nama }}
            </span>
        </div>

        <div id="collapse{{ $val->id }}" class="collapse" aria-labelledby="heading{{ $val->id }}" data-parent="#accrode">
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        @php
                            $qty = 0;
                            $berat = 0;
                        @endphp
                        @foreach (Order::item_order($val->id, 'whole') as $i => $item)
                            @php
                                $qty += $item->qty;
                                $berat += $item->berat;
                            @endphp
                            <div class="radio-toolbar">
                                <input type="radio" id="do-{{ $item->id }}"
                                    onclick='' data-jenis=''
                                    value="{{ $item->id }}" name="purchase"
                                    required>
                                <label for="do-{{ $item->id }}">
                                {{ $item->nama_detail }}
                                    <span class=" pull-right">
                                        Qty : <span class="label label-rounded-grey">{{ $item->qty ?? "0" }} </span> &nbsp
                                        Berat : <span class="label label-rounded-grey">{{ $item->berat ?? "0" }} kg </span> &nbsp

                                </label>
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

{{ $pending->appends(\Illuminate\Support\Facades\Request::except('page'))->links() }}

<script>
    $('.pagination a').on('click', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        $.ajax({
            url: url,
            method: "GET",
            success: function(response) {
                $('#pending').html(response);
            }

        });
    });

</script>

