@php
    $jsondata   = App\Models\Adminedit::where('table_id', $id)->where('table_name', 'marketing_so')->get();
    $json       = [];
    $dataedit   = [];
    $lists      = [];
@endphp
@foreach ($jsondata as $key => $row)
<table class="table default-table">
    <thead>
        <tr>
            <th>No</th>
            @if($key == 0)
                <th>Waktu SO </th>
            @else
                <th>Waktu Edit </th>
            @endif
            <th>Riwayat</th>
            <th>Tanggal SO</th>
            <th>Tanggal Kirim</th>
            <th>Customer</th>
            <th>PO Number</th>
            <th>Memo</th>
        </tr>
    </thead>
    <tbody>
        @php
            $json[]     = json_decode($row->data, true);
            $dataedit[] = $row->content;
        @endphp
        
        @if(isset($json[$key]['header']))
            <tr>
                <td>{{ $key+1 }}</td>
                @if($key == 0)
                <td>{{ date('d-m-Y H:i:s',strtotime($json[$key]['header']['created_at'])) }}</td>
                @else
                <td>{{ $row->created_at }}</td>
                @endif
                <td @if ($row->content == 'Penghapusan Item')
                    style="background-color: #fde0dd"
                @endif>{{ $row->content }}</td>
                <td>{{ $json[$key]['header']['tanggal_so'] ?? '#' }} </td>
                <td>{{ $json[$key]['header']['tanggal_kirim'] ?? '#' }} </td>
                <td>{{ App\Models\Customer::logsocustomer($json[$key]['header']['customer_id']) }} </td>
                <td
                @if($key > 0)
                    @if($json[$key]['header']['po_number'] ?? FALSE && $json[$key-1]['header']['po_number'] ?? FALSE)
                        @if(isset($json[$key]['header']['po_number']) != isset($json[$key-1]['header']['po_number']))
                            style="background-color: #fde0dd"
                        @endif
                    @endif
                @endif
                >
                {{ $json[$key]['header']['po_number'] ?? "-" }} 
                </td>
                <td
                    @if($key > 0)
                        @if($json[$key]['header']['memo'] ?? FALSE && $json[$key-1]['header']['memo'] ?? FALSE)
                            @if(isset($json[$key]['header']['memo']) != isset($json[$key-1]['header']['memo']))
                                style="background-color: #fde0dd"
                            @endif
                        @endif
                    @endif
                    >{{ $json[$key]['header']['memo'] ?? '-' }} 
                </td>
            </tr>
            <tr>
                <td colspan="9">
                    <div>
                        <table class="table default-table">
                            <thead>
                                <tr>
                                    <th>SKU</th>
                                    <th>Item</th>
                                    <th>Parting</th>
                                    <th>Qty</th>
                                    <th>Berat</th>
                                    <th>Plastik</th>
                                    <th>Bumbu</th>
                                    <th>Memo</th>
                                    <th>Internal Memo</th>
                                    <th>Description</th>
                                    <th>Harga</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $total_hargalog = 0;
                                    $total_beratlog = 0;
                                    $total_qtylog = 0;
                                @endphp
                                @for($i = 0; $i < count($json[$key]['list']); $i++)
                                    <tr @if($json[$key]['list'][$i]['deleted_at'] && $row->content !== 'Data Awal (Original)') style="background-color:#fde0dd; color: #f44336" @endif
                                        @if($key > 0)
                                            @if(!isset($json[$key-1]['list'][$i])) style="background-color: #87CEFA" @endif
                                        @endif
                                    >
                                        <td>{{ App\Models\Item::logso('sku',$json[$key]['list'][$i]['item_id']) }}</td>
                                        <td>{{ $json[$key]['list'][$i]['item_nama'] }}</td>
                                        <td 
                                            @if($json[$key-1]['list'][$i]['parting'] ?? FALSE && $json[$key]['list'][$i]['parting'] ?? FALSE)
                                                @if($json[$key]['list'][$i]['parting'] != $json[$key-1]['list'][$i]['parting'] )
                                                    style="background-color: #fde0dd"
                                                @endif
                                            @endif
                                        >{{ $json[$key]['list'][$i]['parting'] }}</td>
                                        <td class="text-right"
                                            @if($json[$key-1]['list'][$i]['qty'] ?? FALSE && $json[$key]['list'][$i]['qty'] ?? FALSE)
                                                @if($json[$key]['list'][$i]['qty'] != $json[$key-1]['list'][$i]['qty'])
                                                    style="background-color: #fde0dd"
                                                @endif
                                            @endif
                                        >{{ $json[$key]['list'][$i]['qty'] }}</td>
                                        <td class="text-right"
                                            @if($json[$key-1]['list'][$i]['berat'] ?? FALSE && $json[$key]['list'][$i]['berat'] ?? FALSE)
                                                @if($json[$key]['list'][$i]['berat'] != $json[$key-1]['list'][$i]['berat'])
                                                    style="background-color: #fde0dd"
                                                @endif
                                            @endif
                                        >{{ number_format($json[$key]['list'][$i]['berat'], 2) }}</td>
                                        <td 
                                            @if($json[$key-1]['list'][$i]['plastik'] ?? FALSE && $json[$key]['list'][$i]['plastik'] ?? FALSE)
                                                @if($json[$key]['list'][$i]['plastik'] != $json[$key-1]['list'][$i]['plastik'])
                                                    style="background-color: #fde0dd"
                                                @endif
                                            @endif
                                        >
                                            @if($json[$key]['list'][$i]['plastik']=="")
                                            Curah
                                            @elseif($json[$key]['list'][$i]['plastik']=="1")
                                            Meyer
                                            @elseif($json[$key]['list'][$i]['plastik']=="2")
                                            Avida
                                            @elseif($json[$key]['list'][$i]['plastik']=="3")
                                            Polos
                                            @elseif($json[$key]['list'][$i]['plastik']=="4")
                                            Bukan Plastik
                                            @elseif($json[$key]['list'][$i]['plastik']=="5")
                                            Mojo
                                            @elseif($json[$key]['list'][$i]['plastik']=="6")
                                            Other
                                            @endif</td>
                                        <td
                                            @if($json[$key-1]['list'][$i]['bumbu'] ?? FALSE && $json[$key]['list'][$i]['bumbu'] ?? FALSE)
                                                @if($json[$key]['list'][$i]['bumbu'] != $json[$key-1]['list'][$i]['bumbu'])
                                                    style="background-color: #fde0dd"
                                                @endif
                                            @endif
                                        >{{ $json[$key]['list'][$i]['bumbu'] }}</td>
                                        <td
                                            @if($json[$key-1]['list'][$i]['memo'] ?? FALSE && $json[$key]['list'][$i]['memo'] ?? FALSE)
                                                @if($json[$key]['list'][$i]['memo'] != $json[$key-1]['list'][$i]['memo'])
                                                    style="background-color: #fde0dd"
                                                @endif
                                            @endif
                                        >{{ $json[$key]['list'][$i]['memo'] }}</td>
                                        <td
                                            @if($json[$key-1]['list'][$i]['internal_memo'] ?? FALSE && $json[$key]['list'][$i]['internal_memo'] ?? FALSE)
                                                @if($json[$key]['list'][$i]['internal_memo'] != $json[$key-1]['list'][$i]['internal_memo'])
                                                    style="background-color: #fde0dd"
                                                @endif
                                            @endif
                                        >{{ $json[$key]['list'][$i]['internal_memo'] ?? '-' }}</td>
                                        <td
                                            @if($json[$key-1]['list'][$i]['description_item'] ?? FALSE && $json[$key]['list'][$i]['description_item'] ?? FALSE)
                                                @if($json[$key]['list'][$i]['description_item'] != $json[$key-1]['list'][$i]['description_item'])
                                                    style="background-color: #fde0dd"
                                                @endif
                                            @endif
                                        >{{ $json[$key]['list'][$i]['description_item'] ?? '-' }}</td>
                                        <td 
                                            @if($json[$key-1]['list'][$i]['harga'] ?? FALSE && $json[$key]['list'][$i]['harga'] ?? FALSE)
                                                @if($json[$key]['list'][$i]['harga'] != $json[$key-1]['list'][$i]['harga'])
                                                    style="background-color: #fde0dd"
                                                @endif
                                            @endif
                                        >Rp {{ number_format($json[$key]['list'][$i]['harga']) }} ({{$json[$key]['list'][$i]['harga_cetakan'] == '1' ? 'Kilogram' : 'Ekor/Pcs/Pack'}})</td>
                                        <td 
                                            @if($json[$key-1]['list'][$i]['harga'] ?? FALSE && $json[$key]['list'][$i]['harga'] ?? FALSE)
                                                @if($json[$key]['list'][$i]['harga'] != $json[$key-1]['list'][$i]['harga'])
                                                    style="background-color: #fde0dd"
                                                @endif
                                            @endif
                                        >
                                        @php 
                                            if($json[$key]['list'][$i]['harga_cetakan']=="1"){
                                                $hargalog = $json[$key]['list'][$i]['harga'] * $json[$key]['list'][$i]['berat'];
                                            }else{
                                                $hargalog = $json[$key]['list'][$i]['harga'] * $json[$key]['list'][$i]['qty'];
                                            }
                                            if (!$json[$key]['list'][$i]['deleted_at'] || $row->content == 'Data Awal (Original)'){
                                                $total_qtylog      += $json[$key]['list'][$i]['qty'];
                                                $total_beratlog    += $json[$key]['list'][$i]['berat'];
                                                $total_hargalog    += $hargalog;
                                            }
                                        @endphp
                                        Rp {{ number_format($hargalog) ?? ""}}
                                    </td>
                                @endfor
                                </tr>
                                <tr>
                                    <td>Total</td>
                                    <td colspan="3" class="text-right">{{ $total_qtylog }}</td>
                                    <td colspan="1" class="text-right">{{ number_format($total_beratlog,2) }}</td>
                                    <td colspan="6" class="text-right">Rp {{ number_format($total_hargalog) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </td>
            </tr>
        @endif
    </tbody>
</table>
@endforeach