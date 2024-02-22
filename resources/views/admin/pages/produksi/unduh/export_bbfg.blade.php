@php
header('Content-Transfer-Encoding: none');
header('Content-type: application/vnd-ms-excel');
header('Content-type: application/x-msexcel');
header('Content-Disposition: attachment; filename=Laporan Bahan Baku dan Finish Good ' . ($tanggal ?? date('Y-m-d')) . '.xls');
@endphp

<div id="export-table">
    <table class="table default-table" border="1">
        <thead>
            <tr>
                <th class="text-info" colspan="4">Bahan Baku</th>
            </tr>
            <tr>
                <th>No</th>
                <th>Nama Item</th>
                <th>Jenis</th>
                <th>Ekor/Pcs/Pack</th>
                <th>Berat</th>
            </tr>
        </thead>
        <tbody>
            @php
                $ekor = 0;
                $berat = 0;
            @endphp
            @foreach ($clonebb as $i => $row)
                <tr>
                    <td>{{ ++$i }}</td>
                    <td>{{ $row->nama }}</td>
                    <td>
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
                <td></td>
                <td>Total</td>
                <td>{{ $ekor }}</td>
                <td>{{ number_format($berat, 2) }} Kg</td>
            </tr>
        </tbody>
    </table>
    {{-- hasil produksi --}}
    <table class="table default-table" border="1">
        <thead>
            <tr>
                <th class="text-info" colspan="4">Hasil Produksi</th>
            </tr>
            <tr>
                <th>No</th>
                <th>Nama Item</th>
                <th>Jenis</th>
                <th>Ekor/Pcs/Pack</th>
                <th>Berat</th>
            </tr>
        </thead>
        <tbody>
            @php
                $ekor = 0;
                $berat = 0;
            @endphp
            @foreach ($clonefg as $i => $row)
                @php
                    foreach ($bom as $item) {
                        $bom_item = \App\Models\BomItem::where('sku', $row->sku)
                            ->where('bom_id', $item->id)
                            ->first();
                    
                        $item_cat = \App\Models\Item::find($row->item_id);
                    
                        $type = ($item_cat->category_id == 4 or $item_cat->category_id == 6 or $item_cat->category_id == 10 or $item_cat->category_id == 16) ? 'By Product' : 'Finished Goods';
                        if ($bom_item) {
                            $type = $bom_item->kategori;
                        }
                    }
                @endphp
                <tr>
                    <td>{{ ++$i }}</td>
                    <td>{{ $row->nama }}</td>
                    <td>
                        @if ($type == 'Finished Goods')
                            <span class="status status-success">{{ $type }}</span>
                        @else
                            <span class="status status-warning">{{ $type }}</span>
                        @endif
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
                <td></td>
                <td>Total</td>
                <td>{{ $ekor }}</td>
                <td>{{ number_format($berat, 2) }} Kg</td>
            </tr>
        </tbody>
    </table>

</div>
