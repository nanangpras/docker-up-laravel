@foreach ($data as $row)
<section class="panel">
    <div class="card-body">

        <div class="row">
            <div class="col">
                <div class="small">Tanggal Kirim</div>
                {{ $row->tanggal_kirim }}
            </div>
            <div class="col">
                <div class="form-group">
                    <div class="small">Nama Customer</div>
                    {{ $row->nama }}
                </div>
            </div>
            <div class="col">
                <div class="small">No SO</div>
                {{ $row->no_so }}
            </div>
            <div class="col">
                <div class="small">Keterangan</div>
                {{ $row->keterangan }}
            </div>
            <div class="col">
                <div class="small">Alamat</div>
                {{ $row->alamat }}
            </div>
            <div class="col">
                <div class="small">Telepon</div>
                {{ $row->telp }}
            </div>
        </div>

        <input type="hidden" name="order_id" value="{{ $row->id }}">
        <table class="table default-table">
            <thead>
                <tr>
                    <th width=10px>No</th>
                    <th>Nama Item</th>
                    <th>Qty Order</th>
                    <th>Berat Order</th>
                    <th>Qty Fulfill</th>
                    <th>Berat Fulfill</th>

                </tr>
            </thead>
            <tbody>
                @php
                    $total = 0;
                    $berat = 0;
                    $count_retur = 0;

                    $retur = \App\Models\Retur::where('id_so', $row->no_so)->first();
                @endphp
                @foreach ($row->daftar_order_full as $i => $list)
                    @php
                        $total += $list->qty;
                        $berat += $list->berat;

                        $retur_item = \App\Models\ReturItem::where('orderitem_id', $list->id)->first();
                    @endphp
                    <tr>
                        <td>{{ ++$i }}
                            <input type="hidden" name="orderitem_id[]" value="{{ $list->id }}"
                                id="orderitem_id{{ $list->id }}">
                            <input type="hidden" name="line_id[]" value="{{ $list->line_id }}"
                                id="line_id{{ $list->line_id }}">
                        </td>
                        <td>{{ $list->nama_detail }}</td>
                        <td>{{ number_format($list->qty) }}</td>
                        <td>{{ number_format($list->berat, 2) }}</td>
                        <td>{{ number_format($list->fulfillment_qty) }}</td>
                        <td>{{ number_format($list->fulfillment_berat, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <a href="{{ route('retur.returdo', ['id' => $row->id]) }}" class="btn btn-primary">Proses</a>

        <div>Total Qty : {{ number_format($total) }}</div>
        <div>Total Berat : {{ number_format($berat, 2) }}</div>

    </div>
</section>
@endforeach
