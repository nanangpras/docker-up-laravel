<section class="panel">
    <div class="card-header font-weight-bold">DETAIL GRAFIK GUDANG</div>
    <div class="card-body">
        <table class="table default-table table-bordered">
            <thead>
                <tr>
                    <th rowspan="2">No</th>
                    <th rowspan="2" class="text-center">Gudang</th>
                    <th rowspan="2" class="text-center">Tanggal</th>
                    <th class="text-center" colspan="2">Saldo Awal</th>
                    <th class="text-center" colspan="2">Inbound</th>
                    <th class="text-center" colspan="2">Outbound</th>
                    <th class="text-center" colspan="2">Saldo Akhir</th>
                </tr>
                <tr>
                    <th class="text-center">Ekor/Pcs/Pack</th>
                    <th class="text-center">Kg</th>
                    <th class="text-center">Ekor/Pcs/Pack</th>
                    <th class="text-center">Kg</th>
                    <th class="text-center">Ekor/Pcs/Pack</th>
                    <th class="text-center">Kg</th>
                    <th class="text-center">Ekor/Pcs/Pack</th>
                    <th class="text-center">Kg</th>
                </tr>
            </thead>
            <tbody>
                @php 
                    $totalsaldoawalqty      = 0; 
                    $totalsaldoawalberat    = 0;
                    $totalqtyinbound        = 0; 
                    $totalberatinbound      = 0;
                    $totalqtyoutbound       = 0;
                    $totalberatoutbound     = 0;
                    $totalsisaqtyinbound    = 0;
                    $totalsisaberatinbound  = 0;
                    $totalsaldoakhirqty     = 0;
                    $totalsaldoakhirberat   = 0;
                @endphp
                @foreach($dataArray as $data)
                <tr>
                    <td>{{ $loop->iteration}}</td>
                    <td class="text-center"><b>{{ $data['nama_gudang'] }}</b></td>
                    <td class="text-center">{{ $data['tanggal'] }}</td>
                    <td class="text-center">{{ number_format($data['qtysaldoawal']) }}</td>
                    <td class="text-center">{{ number_format($data['beratsaldoawal'],2) }}</td>
                    <td class="text-center">{{ number_format($data['qty_inbound'],0) }}</td>
                    <td class="text-center">{{ number_format($data['berat_inbound'],2) }}</td>
                    <td class="text-center">{{ number_format($data['qty_outbound'],0) }}</td>
                    <td class="text-center">{{ number_format($data['berat_outbound'],2) }}</td>
                    <td class="text-center">{{ number_format(($data['qtysaldoawal'] + $data['qty_inbound']) - $data['qty_outbound'] ,0) }}</td>
                    <td class="text-center">{{ number_format(($data['beratsaldoawal'] + $data['berat_inbound']) - $data['berat_outbound'] ,2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>
