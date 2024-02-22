<div class="table-responsive" id="export-soh">
    <table class="table table-small default-table" border="1">
    <thead>
        <tr>
            <th rowspan="3">No</th>
            <th rowspan="3">Item</th>
            <th rowspan="3">Packaging</th>
            <th rowspan="3">Sub Pack</th>
            <th rowspan="3">Konsumen</th>
            <th colspan="3" rowspan="2">Saldo Akhir</th>
            <th colspan="3" rowspan="2">Saldo Awal</th>
            <th class="text-center" colspan="3">Inbound</th>
            <th class="text-center" colspan="3">Outbound</th>
        </tr>
        <tr>
            @for ($i = 0; $i < 2; $i++)
            <th>E/P</th>
            <th>KG</th>
            <th>KRG</th>
            @endfor
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $no => $row)

            @php $item_qty_akhir    = 0 @endphp 
            @php $item_berat_akhir  = 0 @endphp 
            @php $item_krg_akhir    = 0 @endphp 
            
            @if(($row->berat_saldo_awal !=0) || ($row->berat_saldo_awal!=$row->berat_saldo_akhir))

            @php $item_qty_akhir    = $row->qty_saldo_akhir @endphp 
            @php $item_berat_akhir  = $row->berat_saldo_akhir @endphp 
            @php $item_krg_akhir    = $row->karung_saldo_akhir @endphp 

        
                <tr>
                    <td>{{$no+1 }}</div> 
                    <td><div style="width:300px">{{$row->nama }}
                    </div> 
                    </td>
                    <td><div style="width:220px">{{ $row->plastik_group }}</div></td>
                    <td><div style="width:120px">{{ $row->subpack }}</div></td>
                    <td><div style="width:90px">{{ $row->nama_konsumen ?? '' }}</div></td>

                    <td>{{ number_format($item_qty_akhir, 2) }}</td>
                    <td>{{ number_format($item_berat_akhir, 2) }}</td>
                    <td>{{ number_format($item_krg_akhir) }}</td>

                    <td>{{ number_format($row->qty_saldo_awal, 2) }}</td>
                    <td>{{ number_format($row->berat_saldo_awal, 2) }}</td>
                    <td>{{ number_format($row->karung_saldo_awal) }}</td>

                    <td>{{ number_format($row->whin_qty, 2) }}</td>
                    <td>{{ number_format($row->whin_berat, 2) }}</td>
                    <td>{{ number_format($row->whin_keranjang) }}</td>

                    <td>{{ number_format($row->whout_qty, 2) }}</td>
                    <td>{{ number_format($row->whout_berat, 2) }}</td>
                    <td>{{ number_format($row->whout_keranjang) }}</td>
                </tr>

            @endif

        @endforeach
    </tbody>
</table>