
@php
    header('Content-Transfer-Encoding: none');
    header('Content-type: application/vnd-ms-excel');
    header('Content-type: application/x-msexcel');
    header('Content-Disposition: attachment; filename=DATA-SUMMARY-SO.xls');
@endphp
<style>
    th,td {
        border: 1px solid #ddd;
    }
</style>
<table class="table" width="100%">
    <thead>
        <tr>
            <th class="text-font">NO</th>
            <th class="text-font">CUSTOMER</th>
            <th class="text-font">MEMO HEADER</th>
            <th class="text-font">TGL SO</th>
            <th class="text-font">TGL KIRIM</th>
            <th class="text-font">USER</th>
            <th class="text-font">NO SO</th>
            <th class="text-font">STATUS SO</th>
            <th class="text-font">ID</th>
            <th class="text-font">SKU</th>
            <th class="text-font">Item</th>
            <th class="text-font">Parting</th>
            <th class="text-font">Qty</th>
            <th class="text-font">Berat</th>
            <th class="text-font">Plastik</th>
            <th class="text-font">Bumbu</th>
            <th class="text-font">Memo Item</th>
            <th class="text-font">Jam Buat SO</th>
            <th class="text-font">Jam SO Sukses</th>
            <th class="text-font">Jam SO Gagal</th>
            <th class="text-font">Jam SO Update</th>
            <th class="text-font">Harga</th>
            <th class="text-font">Total</th>
            <th class="text-font">#</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($output as $row)
            @php 
                $total_harga    = 0;
                $total_berat    = 0;
                $total_qty      = 0;
            @endphp
            <tr
                @if($row['status_so'] == '2' || $row['status_so'] == '1') style="background-color:#bed9ee" @endif
                @if($row['status_so'] == '3') style="background-color:#e7fddd" @endif
                @if($row['status_so'] == '0') style="background-color:#fde0dd" @endif
            >
                <td class="text-font">{{ $loop->iteration }}</td>
                <td class="text-font">{{ $row['customer'] ?? '#CUSTOMERHILANG' }}</td>
                <td class="text-font">{{ $row['memo_so'] }}</td>
                <td class="text-font">{{ $row['tanggal_so'] }}</td>
                <td class="text-font">{{ $row['tanggal_kirim'] }}</td>
                <td class="text-font">{{ $row['marketing'] }}</td>
                <td class="text-font">{{ $row['no_so'] ?? "#" }}</td>
                <td class="text-font">
                    @if($row['status_so'] == '2' || $row['status_so'] == '1')
                        <span class="status status-info text-font"> <strong>PROCESS</strong> </span>
                    @endif
                    @if($row['status_so']=='3')
                        <span class="status status-success text-font"><strong>VERIFIED</strong></span>
                    @endif
                    @if($row['status_so']=='0')
                        <span class="status status-danger text-font"><strong>VOID/BATAL</strong></span>
                    @endif
                </td>
                <td class="text-font">#{{ $row['id'] }}_{{ $row['line_id'] }}</td>
                <td class="text-font">{{ $row['sku'] ?? '#' }}</td>
                <td class="text-font">{{ $row['nama_item'] ?? $row['item_nama'] }}</td>
                <td class="text-font">{{ $row['parting'] }}</td>
                <td class="text-right">{{ number_format($row['qty']) }}
                </td>
                <td class="text-right">
                    {{ number_format($row['berat'], 2) }}
                </td>
                <td class="text-font">
                    {{ $row['plastik'] }}
                    @if($row['plastik'] =="")
                    Curah
                    @elseif($row['plastik'] =="1")
                    Meyer
                    @elseif($row['plastik'] =="2")
                    Avida
                    @elseif($row['plastik'] =="3")
                    Polos
                    @elseif($row['plastik'] =="4")
                    Bukan Plastik
                    @elseif($row['plastik'] =="5")
                    Mojo
                    @elseif($row['plastik'] =="5")
                    Other
                    @endif
                </td>
                <td class="text-font">{{ $row['bumbu'] }}</td>
                <td class="text-font">{{ $row['memo'] }}</td>
                <td class="text-font">{{ $row['created_so'] }}</td>
                <td class="text-font">{{ $row['response_time'] }}</td>
                <td class="text-font">{{ $row['failed_time'] }}</td>
                <td class="text-font">{{ $row['updated_time'] }}</td>
                <td class="text-right">Rp {{ number_format($row['harga']) }} ({{$row['harga_cetakan'] == '1' ? 'Kilogram' : 'Ekor/Pcs/Pack'}})</td>
                <td class="text-right">
                    @php 
                        
                        if($row['harga_cetakan'] =="1"){
                            $harga = $row['harga'] * $row['berat'];
                        }else{
                            $harga = $row['harga'] * $row['qty'];
                        }
                        if (!$row['deleted_at']){
                            $total_qty      += $row['qty'];
                            $total_berat    += $row['berat'];
                            $total_harga    = $total_harga + $harga;
                        }
                    @endphp
                    Rp {{ number_format($harga) ?? ""}}
                </td>
                <td class="text-font">
                    @if ($row['so_deleted_at'])
                        <span class="status status-danger">VOID/BATAL</span>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>                            