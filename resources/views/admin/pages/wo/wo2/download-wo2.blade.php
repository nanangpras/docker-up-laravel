@php
header('Content-Transfer-Encoding: none');
header('Content-type: application/vnd-ms-excel');
header('Content-type: application/x-msexcel');
header('Content-Disposition: attachment; filename=WO2-Download-' . $tanggal . '.xls');
@endphp

{{-- <table class="table default-table table-small table-hover"  border="1">
    <thead>
        <tr>
            <th>No</th>
            <th>Item</th>
            <th>Tanggal</th>
            <th>Asal</th>
            <th>Qty Awal</th>
            <th>Berat Awal</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($abf as $i => $row)
            <tr>
                <td>{{++$i}}</td>
                <td>{{$row->item_name}}</td>
                <td>{{date('d/m/Y', strtotime($row->tanggal_masuk))}}</td>
                <td>{{$row->asal}}</td>
                <td>{{ $row->qty_awal ?: '0' }}</td>
                <td>{{ $row->berat_awal ?: '0' }}</td>
            </tr>
        @endforeach
    </tbody>
</table> --}}
@foreach ($produksi as $p)
    REGU : {{ $p['regu'] }}<br>
    <div class="row">
        @php
            $total_bb = 0;
            $total_fg = 0;
            $bahan_baku = $p['bb'];
            $fg = $p['fg'];
            $regu = $p['regu'];
        @endphp
        <div class="col-md-6">
            <div class="table-responsive">
                <table class="table default-table" border="1" width="100%">
                    <thead>
                        <tr>
                            <th class="text-info" colspan="4">Bahan Baku</th>
                        </tr>
                        <tr>
                            <th>No</th>
                            <th>Nama Item</th>
                            <th>Ekor/Pcs/Pack</th>
                            <th>Berat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $ekor = 0;
                            $berat = 0;
                        @endphp
                        @foreach ($bahan_baku as $i => $row)
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>
                                    {{ $row->nama }}
                                    @if ($row->type == 'hasil-produksi')
                                        <span class="status status-info">FG</span>
                                    @elseif($row->type == 'bahan-baku')
                                        <span class="status status-danger">BB</span>
                                    @endif
                                </td>
                                <td>{{ number_format($row->jumlah) }}</td>
                                <td>{{ number_format($row->kg, 2) }} Kg</td>
                            </tr>
                            @php
                                $ekor += $row->jumlah;
                                $berat += $row->kg;
                            @endphp
                        @endforeach
                        <tr>
                            <td></td>
                            <td>Total</td>
                            <td>{{ $ekor }}</td>
                            <td>{{ number_format($berat, 2) }} Kg</td>
                        </tr>
                    </tbody>
                </table>
                @php
                    $total_bb = $berat;
                @endphp
            </div>
            <div class="table-responsive">
                <table class="table default-table" border="1" width="100%">
                    <thead>
                        <tr>
                            <th class="text-info" colspan="4">Hasil Produksi</th>
                        </tr>
                        <tr>
                            <th>No</th>
                            <th>Nama Item</th>
                            <th>Ekor/Pcs/Pack</th>
                            <th>Berat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $ekor = 0;
                            $berat = 0;
                            $ekor_fg = 0;
                            $berat_fg = 0;
                            $ekor_bp = 0;
                            $berat_bp = 0;
                        @endphp
                        @foreach ($fg as $i => $row)
                            @php
                                
                                if ($regu == 'boneless') {
                                    $bom = \App\Models\Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL') . ' - KARKAS - BONELESS BROILER')->first();
                                    $id_assembly = $bom->netsuite_internal_id;
                                } elseif ($regu == 'parting') {
                                    $bom = \App\Models\Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL') . ' - AYAM PARTING BROILER')->first();
                                    $id_assembly = $bom->netsuite_internal_id;
                                } elseif ($regu == 'marinasi') {
                                    $bom = \App\Models\Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL') . ' - AYAM PARTING MARINASI BROILER')->first();
                                    $id_assembly = $bom->netsuite_internal_id;
                                } elseif ($regu == 'whole') {
                                    $bom = \App\Models\Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL') . ' - AYAM KARKAS BROILER')->first();
                                    $id_assembly = $bom->netsuite_internal_id;
                                } elseif ($regu == 'frozen') {
                                    $bom = \App\Models\Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL') . ' - AYAM KARKAS FROZEN')->first();
                                    $id_assembly = $bom->netsuite_internal_id;
                                } else {
                                    $bom = \App\Models\Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL') . ' - KARKAS - BONELESS BROILER')->first();
                                    $id_assembly = $bom->netsuite_internal_id;
                                }
                                $bom_item = \App\Models\BomItem::where('sku', $row->sku)
                                    ->where('bom_id', $bom->id)
                                    ->first();
                                
                                $item_cat = \App\Models\Item::find($row->item_id);
                                
                                $type = ($item_cat->category_id == 4 or $item_cat->category_id == 6 or $item_cat->category_id == 10 or $item_cat->category_id == 16) ? 'By Product' : 'Finished Goods';
                                if ($bom_item) {
                                    $type = $bom_item->kategori;
                                }
                            @endphp
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>{{ $row->nama }}

                                    @if ($type == 'Finished Goods')
                                        <span class="status status-success">{{ $type }}</span>
                                    @else
                                        <span class="status status-warning">{{ $type }}</span>
                                    @endif

                                    @php
                                        if ($type == 'Finished Goods') {
                                            $ekor_fg = $ekor_fg + $row->jumlah;
                                            $berat_fg = $berat_fg + $row->kg;
                                        } else {
                                            $ekor_bp = $ekor_bp + $row->jumlah;
                                            $berat_bp = $berat_bp + $row->kg;
                                        }
                                    @endphp
                                </td>
                                <td>{{ number_format($row->jumlah) }}</td>
                                <td>{{ number_format($row->kg, 2) }} Kg</td>
                            </tr>
                            @php
                                $ekor = $ekor + $row->jumlah;
                                $berat = $berat + $row->kg;
                            @endphp
                        @endforeach
                        <tr>
                            <td></td>
                            <td>Total Finished Good</td>
                            <td>{{ $ekor_fg }}</td>
                            <td>{{ number_format($berat_fg, 2) }} Kg</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Total By Product</td>
                            <td>{{ $ekor_bp }}</td>
                            <td>{{ number_format($berat_bp, 2) }} Kg</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Total Global</td>
                            <td>{{ $ekor }}</td>
                            <td>{{ number_format($berat, 2) }} Kg</td>
                        </tr>
                    </tbody>
                </table>
                @php
                    $total_fg = $berat;
                @endphp

            </div>
        </div>
    </div>
@endforeach
