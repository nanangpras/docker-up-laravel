@if (COUNT($fgood))
<div class="mt-3 pt-3 border-top border-danger">
    <b class="mb-2 text-uppercase">Hasil produksi alokasi khusus konsumen</b>
    <table class="table default-table">
        <thead>
            <tr>
                <th>Item</th>
                <th>Qty</th>
                <th>Berat</th>
                <th>Pengambilan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($fgood as $item)
            <tr>
                <td>
                    {{ $item->item_name }}
                    @php
                        $exp = json_decode($item->label);
                    @endphp
                    <div class="status status-success">
                        <div class="row">
                            <div class="col pr-1">
                                {{ $item->plastik_nama }}
                            </div>
                            <div class="col-auto pl-1">
                                <span class="float-right">// {{ $item->plastik_qty }} Pcs</span>
                            </div>
                        </div>
                    </div>

                    @if($exp)
                        
                        @if ($exp->parting->qty ?? "") Parting : {{ $exp->parting->qty }} <br> @endif
                        @if ($exp->additional) {{ $exp->additional->tunggir ? 'Tanpa Tunggir, ' : '' }} {{ $exp->additional->lemak ? 'Tanpa Lemak, ' : '' }} {{ $exp->additional->maras ? 'Tanpa Maras' : '' }} @endif
                        <div class="status status-success">
                            @if ($exp->sub_item ?? "") Customer : {{ $exp->sub_item }} @endif
                        </div>
                    @endif
                </td>
                <td>{{ $item->stock_item }}</td>
                <td>{{ $item->stock_berat }}</td>
                <td>
                    <input type="hidden" name="x_code[]" value="{{ $item->id }}">
                    <div class="row">
                        <div class="col pr-1">
                            <input type="number" name="qty[]" style="max-width: 100px" class="p-1 form-control qty_ambil form-control-sm" placeholder="Ekor">
                        </div>
                        <div class="col pl-1">
                            <input type="number" name="berat[]" style="max-width: 100px" class="p-1 form-control berat_ambil form-control-sm" step="0.01" placeholder="Berat">
                        </div>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
