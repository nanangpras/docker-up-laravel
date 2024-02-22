<div class="row">
    <div class="col-lg-6">
        <div class="font-weight-bold">Summary Retur</div>
        <div class="form-group outer-table-scroll">
            <table class="table default-table">
                <thead>
                    <tr>
                        <th>Nama Item</th>
                        <th>Qty</th>
                        <th>Berat</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $retur_qty = 0;
                        $retur_berat = 0;
                    @endphp
                    @foreach ($retur as $i => $row)
                        <tr>
                            <td>{{ $row->to_item->nama }}</td>
                            <td class="text-right">{{ number_format($row->total) }}</td>
                            <td class="text-right">{{ number_format($row->kg, 2) }}</td>
                        </tr>
                        @php
                            $retur_qty = $retur_qty + $row->total;
                            $retur_berat = $retur_berat + $row->kg;
                        @endphp
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th>Total</th>
                        <th class="text-right">{{ number_format($retur_qty) }}</th>
                        <th class="text-right">{{ number_format($retur_berat, 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="font-weight-bold">Produksi Evis</div>
        <div class="form-group outer-table-scroll">
            <table class="table default-table">
                <thead>
                    <tr>
                        <th>Nama Item</th>
                        <th>Berat</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $evis_qty = 0;
                        $evis_berat = 0;
                    @endphp
                    @foreach ($produksi_evis as $row)
                        <tr>
                            <td>{{ $row->eviitem->nama ?? '' }}</td>
                            <td class="text-right">{{ number_format($row->total, 2) }}</td>
                        </tr>
                        @php
                            $evis_qty = 0;
                            $evis_berat = $evis_berat + $row->total;
                        @endphp
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th>Total</th>
                        <th class="text-right">{{ number_format($evis_berat, 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    
</div>

<div id="dashboard-loading-pageEmpat" style="height: 30px">
    <div class="text-center mb-2 mt-2" style="position: absolute; left: 0; right: 0;">
        <img src="{{asset('loading.gif')}}" style="width: 20px"> Loading ...
    </div>
</div>
