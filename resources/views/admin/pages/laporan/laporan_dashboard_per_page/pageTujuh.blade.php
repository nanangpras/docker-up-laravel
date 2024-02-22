<div class="row mb-3">
    <div class="col-md-6">
        <div class="font-weight-bold">Pengambilan Bahan Baku</div>
        <div class="form-group outer-table-scroll">
            <table class="table default-table">
                <thead>
                    <tr>
                        <th>Asal</th>
                        <th>Tanggal</th>
                        <th>Qty</th>
                        <th>Berat</th>
                        <th>%</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $p_bb_qty = 0;
                        $p_bb_berat = 0;
                        $p_bb_percent = 0;
                    @endphp
                    @foreach ($data['ambil_bb'] as $row)

                        @if($row->asal_tujuan!="evisgabungan")
                            @php
                                $p_bb_qty = $p_bb_qty + $row->total;
                                $p_bb_berat = $p_bb_berat + $row->kg;
                            @endphp
                            <tr>
                                <td>{{ $row->asal_tujuan }}</td>
                                <td>{{ date('d/m/Y', strtotime($row->tanggal_produksi)) }}</td>
                                <td class="text-right">{{ number_format($row->total) }}</td>
                                <td class="text-right">{{ number_format($row->kg, 2) }}</td>
                                <td class="text-right">
                                    {{ number_format(($row->kg / $data['ambil_bb_sum']) * 100, 2) }} %</td>
                            </tr>
                        @endif

                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="2">Total</th>
                        <th class="text-right">{{ number_format($p_bb_qty, 2) }}</th>
                        <th class="text-right">{{ number_format($p_bb_berat, 2) }}</th>
                        <th class="text-right">100%</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <div class="col-md-6">
        <b>Informasi Thawing</b>
        <div class="form-group outer-table-scroll">
            <table class="table default-table mb-3">
                <thead>
                    <tr>
                        <th>Nama Item</th>
                        <th class="text-center">Qty</th>
                        <th class="text-center">Berat</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $th_qty = 0;
                        $th_berat = 0;
                    @endphp
                    @foreach ($data['thawaing'] as $i => $row)
                        <tr>
                            <td>{{ $row->item_name }}</td>
                            <td class="text-right">{{ number_format($row->qty) }}</td>
                            <td class="text-right">{{ number_format($row->berat, 2) }}</td>
                        </tr>
                        @php
                            $th_qty = $th_qty + $row->qty;
                            $th_berat = $th_berat + $row->berat;
                        @endphp
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th>Total</th>
                        <th class="text-right">{{ number_format($th_qty)}}</th>
                        <th class="text-right">{{ number_format($th_berat, 2)}}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<div id="dashboard-loading-pageDelapan" style="height: 30px">
    <div class="text-center mb-2 mt-2" style="position: absolute; left: 0; right: 0;">
        <img src="{{asset('loading.gif')}}" style="width: 20px"> Loading ...
    </div>
</div>