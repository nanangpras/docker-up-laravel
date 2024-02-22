<h6 class="text-center">LAPORAN HASIL PRODUKSI</h6>
<div class="table-responsive">
    
    <table class="table default-table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Jumlah Potong</th>
                <th>Whole</th>
                <th>Parting</th>
                <th>Marinasi</th>
                <th>Frozen</th>
                <th>Total Produksi</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $totalpot = 0;
                $totalwh = 0;
                $totalpart = 0;
                $totalmar = 0;
                $totalfz = 0;
                $total_total = 0;
            @endphp
            @foreach($data_range as $row)
                @php 
                    $totalpot = $totalpot + $row['total_potong'];
                    $totalwh = $totalwh + $row['whole'];
                    $totalpart = $totalpart + $row['parting'];
                    $totalmar = $totalmar + $row['total_potong'];
                    $totalfz = $totalfz + $row['frozen'];
                    $total_total = $total_total+ ($row['whole']+$row['parting']+$row['marinasi']+$row['frozen']);
                @endphp
            <tr>
                <td>{{$row['tanggal']}}</td>
                <td>{{number_format($row['total_potong'])}}</td>
                <td>{{number_format($row['whole'],1)}}</td>
                <td>{{number_format($row['parting'],1)}}</td>
                <td>{{number_format($row['marinasi'],1)}}</td>
                <td>{{number_format($row['frozen'],1)}}</td>
                <td>{{number_format(($row['whole']+$row['parting']+$row['marinasi']+$row['frozen']),1)}}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <th>Total</th>
            <th>{{number_format($totalpot)}}</th>
            <th>{{number_format($totalwh, 1)}}</th>
            <th>{{number_format($totalpart, 1)}}</th>
            <th>{{number_format($totalmar, 1)}}</th>
            <th>{{number_format($totalfz, 1)}}</th>
            <th>{{number_format($total_total, 1)}}</th>
        </tfoot>
    </table>
</div>
    