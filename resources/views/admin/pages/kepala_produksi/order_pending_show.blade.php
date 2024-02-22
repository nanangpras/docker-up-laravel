<div class="accordion" id="accordionOrderPending">
    <div id="cart"></div>
    @foreach ($pending as $i => $val)
        <div class="card">
            <a class="btn btn-link" data-toggle="collapse" data-target="#collapseOne{{ $val->id }}"
                aria-expanded="true" aria-controls="collapseOne">
                <div class="card-header" id="headingOne{{ $i }}">

                    {{ $loop->iteration + ($pending->currentpage() - 1) * $pending->perPage() }}. {{ $val->nama }}
                    {{ $val->kode }}

                </div>
            </a>

            <div id="collapseOne{{ $val->id }}" class="collapse" aria-labelledby="headingOne{{ $i }}"
                data-parent="#accordionOrderPending">
                <div class="card-body">
                    <div class="row">

                        @csrf <input type="hidden" name="kode" id="kode" value="{{ $val->id }}">
                        <div class="col-12">

                            <table class="table default-table">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Qty</th>
                                        <th>Berat</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $qty = 0;
                                        $berat = 0;
                                    @endphp
                                    @foreach (Order::item_order($val->id) as $i => $item)
                                        @php
                                            $qty += $item->qty;
                                            $berat += $item->berat;
                                        @endphp
                                        <tr>
                                            <td>{{ $item->nama_detail }}</td>
                                            <td>{{ $item->qty ?? '0' }}</td>
                                            <td>{{ $item->berat ?? '0' }} kg</td>
                                            <td></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                {{-- <tfoot>
                                    <tr>
                                        <td><input type="text" name="catatan" class="form-control" id="catatan"
                                                placeholder="Catatan untuk KR"></td>
                                        <td>{{ number_format($qty) }}</td>
                                        <td>{{ number_format($berat) }} Kg</td>
                                        <td><button type="submit" data-kode="{{ $val->id }}"
                                                class="btn btn-primary btn-block proses">Proses</button></td>
                                    </tr>
                                </tfoot> --}}
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

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
                $('#kp-list-order-pending').html(response);
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
