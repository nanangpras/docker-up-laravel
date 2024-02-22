@php
    header('Content-Transfer-Encoding: none');
    header("Content-type: application/vnd-ms-excel");
    header("Content-type: application/x-msexcel");
    header("Content-Disposition: attachment; filename=export-soh-$tanggal.xls");
@endphp

<style>
    th, td{
        border: 1px solid #ddd;
    }
</style>
<div class="table-responsive">
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
                <th class="text-center" colspan="9">Inbound</th>
                <th class="text-center" colspan="6">Outbound</th>
            </tr>
            <tr>
                <th class="text-center" colspan="3">Production</th>
                <th class="text-center" colspan="3">Tolak/Retur</th>
                <th class="text-center" colspan="3">Other</th>
                <th class="text-center" colspan="3">Kiriman</th>
                <th class="text-center" colspan="3">Reproduksi</th>
            </tr>
            <tr>
                @for ($i = 0; $i <= 6; $i++)
                <th>E/P</th>
                <th>KG</th>
                <th>KRG</th>
                @endfor
            </tr>
        </thead>
        <tbody class="tbodyLoadedData">
            @foreach ($data as $no => $row)

                @php $item_qty_akhir    = 0 @endphp 
                @php $item_berat_akhir  = 0 @endphp 
                @php $item_krg_akhir    = 0 @endphp 
                
                @if(($row->berat_saldo_awal !=0) || ($row->berat_saldo_awal!=$row->berat_saldo_akhir))

                @php $x   =   App\Models\Product_gudang::wh_soh($tanggal, $row->product_id, $row->packaging, $row->subpack, $row->customer_id) ; @endphp
                @php $qty_prod      = $x['inbound']['production']['ep'] ?? "0" @endphp 
                @php $berat_prod    = $x['inbound']['production']['kg'] ?? "0" @endphp 
                @php $krg_prod      = $x['inbound']['production']['krg'] ?? "0" @endphp 
                @php $qty_retur     = $x['inbound']['retur']['ep'] ?? "0" @endphp 
                @php $berat_retur   = $x['inbound']['retur']['kg'] ?? "0" @endphp 
                @php $krg_retur     = $x['inbound']['retur']['krg'] ?? "0" @endphp 
                @php $qty_other     = $x['inbound']['other']['ep'] ?? "0" @endphp 
                @php $berat_other   = $x['inbound']['other']['kg'] ?? "0" @endphp 
                @php $krg_other     = $x['inbound']['other']['krg'] ?? "0" @endphp 
                @php $qty_kiriman   = $x['outbond']['kiriman']['ep'] ?? "0" @endphp 
                @php $berat_kiriman = $x['outbond']['kiriman']['kg'] ?? "0" @endphp 
                @php $krg_kiriman   = $x['outbond']['kiriman']['krg'] ?? "0" @endphp 
                @php $qty_reprod    = $x['outbond']['reprod']['ep'] ?? "0" @endphp 
                @php $berat_reprod  = $x['outbond']['reprod']['kg'] ?? "0" @endphp 
                @php $krg_reprod    = $x['outbond']['reprod']['krg'] ?? "0" @endphp 

                @php $item_qty_akhir    = $row->qty_saldo_akhir @endphp 
                @php $item_berat_akhir  = $row->berat_saldo_akhir @endphp 
                @php $item_krg_akhir    = $row->karung_saldo_akhir @endphp 

            
                    <tr>
                        <td>{{ $no+1 }}</td> 
                        <td><div style="width:300px">{{$row->nama }}
                        {{-- {{$tanggal."-||-".$row->product_id."-||-".$row->packaging."-||-".$row->subpack."-||-".$row->customer_id}} --}}
                        </div> 
                        </td>
                        <td><div style="width:220px">{{ $row->packaging }}</div></td>
                        <td><div style="width:120px">{{ $row->subpack }}</div></td>
                        <td><div style="width:90px">{{ $row->nama_konsumen ?? '' }}</div></td>

                        <td>{{ number_format($item_qty_akhir, 2) }}</td>
                        <td>{{ number_format($item_berat_akhir, 2) }}</td>
                        <td>{{ number_format($item_krg_akhir) }}</td>

                        <td>{{ number_format($row->qty_saldo_awal, 2) }}</td>
                        <td>{{ number_format($row->berat_saldo_awal, 2) }}</td>
                        <td>{{ number_format($row->karung_saldo_awal) }}</td>

                    
                        <td @if($qty_prod>0) style="background-color: pink" @endif>
                            {{$qty_prod}}</td>
                        <td @if($berat_prod>0) style="background-color: pink" @endif>
                            {{$berat_prod}}</td>
                        <td @if($krg_prod>0) style="background-color: pink" @endif>
                            {{$krg_prod}}</td>
                        <td @if($qty_retur>0) style="background-color: pink" @endif>
                            {{$qty_retur}}</td>
                        <td @if($berat_retur>0) style="background-color: pink" @endif>
                            {{$berat_retur}}</td>
                        <td @if($krg_retur>0) style="background-color: pink" @endif>
                            {{$krg_retur}}</td>
                        <td @if($qty_other>0) style="background-color: pink" @endif>
                            {{$qty_other}}</td>
                        <td @if($berat_other>0) style="background-color: pink" @endif>
                            {{$berat_other}}</td>
                        <td @if($krg_other>0) style="background-color: pink" @endif>
                            {{$krg_other}}</td>
                        <td @if($qty_kiriman>0) style="background-color: pink" @endif>
                            {{$qty_kiriman}}</td>
                        <td @if($berat_kiriman>0) style="background-color: pink" @endif>
                            {{$berat_kiriman}}</td>
                        <td @if($krg_kiriman>0) style="background-color: pink" @endif>
                            {{$krg_kiriman}}</td>
                        <td @if($qty_reprod>0) style="background-color: pink" @endif>
                            {{$qty_reprod}}</td>
                        <td @if($berat_reprod>0) style="background-color: pink" @endif>
                            {{$berat_reprod}}</td>
                        <td @if($krg_reprod>0) style="background-color: pink" @endif>
                            {{$krg_reprod}}</td>


                    </tr>
                @endif

            @endforeach
        </tbody>
    </table>
</div>