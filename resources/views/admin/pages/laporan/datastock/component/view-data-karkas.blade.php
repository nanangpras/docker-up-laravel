<div class="table-responsive">
    <table class="default-table" width="1400">
        <thead>
            <tr>
                <th class="center" width="5%"  rowspan="3" >No</th>
                <th class="center" width="9%" rowspan="3" >Ukuran</th>
                <th class="center" width="11%" colspan="2" >Ayam Karkas Baru</th>
                <th class="center" width="11%" colspan="2" >Ayam Karkas Baru Memar</th>
                <th class="center" width="11%" colspan="2" >Ayam Karkas Lama</th>
                <th class="center" width="11%" colspan="2" >Ayam Karkas Lama Memar</th>
                <th class="center" width="11%" colspan="2" >Ayam Utuh Baru</th>
                <th class="center" width="11%" colspan="2" >Ayam Utuh Baru Memar</th>
                <th class="center" width="11%" colspan="2" >Ayam Utuh Lama</th>
                <th class="center" width="11%" colspan="2" >Ayam Utuh Lama Memar</th>
            </tr>
            <tr>
                <th colspan="4" class="center"> Tgl : {{ $akhir }} </th>
                <th colspan="4" class="center"> Tgl : {{ $mulai}}  s/d  {{ date('Y-m-d', strtotime($akhir .'-1 days')) }}</th>
                <th colspan="4" class="center"> Tgl : {{ $akhir }} </th>
                <th colspan="4" class="center"> Tgl : {{ $mulai}}  s/d  {{ date('Y-m-d', strtotime($akhir .'-1 days')) }}</th>
            </tr>
            <tr>
                <th class="center"> Ekor</th>
                <th class="center"> Kg</th>
                <th class="center"> Ekor</th>
                <th class="center"> Kg</th>
                <th class="center"> Ekor</th>
                <th class="center"> Kg</th>
                <th class="center"> Ekor</th>
                <th class="center"> Kg</th>
                <th class="center"> Ekor</th>
                <th class="center"> Kg</th>
                <th class="center"> Ekor</th>
                <th class="center"> Kg</th>
                <th class="center"> Ekor</th>
                <th class="center"> Kg</th>
                <th class="center"> Ekor</th>
                <th class="center"> Kg</th>
            </tr>
        </thead>
        <tbody>
        @php 
            $total_qty_karkas       = 0;
            $total_berat_karkas     = 0;
            $total_qty_utuh         = 0;
            $total_berat_utuh       = 0;
        @endphp
        @foreach ($data['datakarkas'] as $row)
            <tr>
                <td class="center">{{ $loop->iteration }}</td>
                <td class="center">{{ $row['namaitem'] }}</td>
                <td class="center karkas">{{ number_format($row['qty_baru'],1) }}</td>
                <td class="center karkas">{{ number_format($row['berat_baru'],1) }}</td>
                <td class="center karkas">{{ number_format($row['qty_memar_baru'],1) }}</td>
                <td class="center karkas">{{ number_format($row['berat_memar_baru'],1) }}</td>
                <td class="center karkas">{{ number_format($row['qty_lama'],1) }}</td>
                <td class="center karkas">{{ number_format($row['berat_lama'],1) }}</td>
                <td class="center karkas">{{ number_format($row['qty_memar_lama'],1) }}</td>
                <td class="center karkas">{{ number_format($row['berat_memar_lama'],1) }}</td>
                <td class="center utuh">{{ number_format($row['qty_utuh_baru'],1) }}</td>
                <td class="center utuh">{{ number_format($row['berat_utuh_baru'],1) }}</td>
                <td class="center utuh">{{ number_format($row['qty_utuh_memar_baru'],1) }}</td>
                <td class="center utuh">{{ number_format($row['berat_utuh_memar_baru'],1) }}</td>
                <td class="center utuh">{{ number_format($row['qty_utuh_lama'],1) }}</td>
                <td class="center utuh">{{ number_format($row['berat_utuh_lama'],1) }}</td>
                <td class="center utuh">{{ number_format($row['qty_utuh_memar_lama'],1) }}</td>
                <td class="center utuh">{{ number_format($row['berat_utuh_memar_lama'],1) }}</td>
            </tr>
            @php 
                $total_qty_karkas       = $total_qty_karkas + $row['qty_baru'] + $row['qty_memar_baru'] + $row['qty_lama'] + $row['qty_memar_lama'];
                $total_berat_karkas     = $total_berat_karkas + $row['berat_baru'] + $row['berat_memar_baru'] + $row['berat_lama'] + $row['berat_memar_lama'];
                $total_qty_utuh         = $total_qty_utuh + $row['qty_utuh_baru'] + $row['qty_utuh_memar_baru'] + $row['qty_utuh_lama'] + $row['qty_utuh_memar_lama'];
                $total_berat_utuh       = $total_berat_utuh + $row['berat_utuh_baru'] + $row['berat_utuh_memar_baru'] + $row['berat_utuh_lama'] + $row['berat_utuh_memar_lama'];
            @endphp
        @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td></td>
                <td class="bold center" colspan="3"> Total Karkas </td>
                <td class="bold center" colspan="3"> {{ number_format($total_qty_karkas,1)}} Ekor</td>
                <td class="bold center" colspan="3"> {{ number_format($total_berat_karkas,1)}} Kg</td>
                <td class="bold center" colspan="2"> Total Ayam Utuh </td>
                <td class="bold center" colspan="3"> {{ number_format($total_qty_utuh,1)}} Ekor</td>
                <td class="bold center" colspan="3"> {{ number_format($total_berat_utuh,1)}} Kg</td>
            </tr>
        </tfoot>
    </table>
</div>
<style>
    .table-responsive{
        overflow-x: scroll;
    }
    .karkas{
        background-color: #F4FCFD;
    }
    .utuh{
        background-color: #FDF4F7;
    }
    .bold{
        font-weight: bold;
    }
    .right{
        right: 0;
    }
</style>