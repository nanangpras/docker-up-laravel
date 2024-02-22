@if ($download == true)
    @php
    header('Content-Transfer-Encoding: none');
    header("Content-type: application/vnd-ms-excel");
    header("Content-type: application/x-msexcel");
    header("Content-Disposition: attachment; filename=Retur Per Mingguan.xls");
    @endphp
@endif
<style>
    th,td {
        border: 1px solid #000000;
    }
    .table thead th {
        vertical-align: bottom;
        border: 1px solid #cecece;
        font-weight: bold ;
        /* style="background-color: #edeff6;" */
    }
</style>
<div class="table-responsive">
    <table class="table table-sm table-hover table-striped table-bordered table-small" border="1">
        <thead>
            <tr class="text-center" width="100%">
                <th rowspan="2" width="3%">No</th>
                <th rowspan="2" width="15%">Tanggal</th>
                <th colspan="3" width="35%">Jumlah Retur (Kg) </th>
                <th colspan="3" width="35%">Jumlah Retur (%) </th>
                <th rowspan="2" width="12%">Total Pengiriman </th>
            </tr>
            <tr class="text-center" width="100%">
                <th>Kualitas</th>
                <th>Non Kualitas</th>
                <th>Total</th>
                <th>Kualitas</th>
                <th>Non Kualitas</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data_range as $row)
                <tr class="text-center">
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $row['tanggal'] }} </td>
                    <td>{{number_format($row['kualitas'],2,',', '.') }} Kg </td>
                    <td>{{number_format($row['nonkualitas'],2,',', '.') }} Kg</td>
                    <td>{{number_format(($row['kualitas']+$row['nonkualitas']),2,',', '.') }} Kg</td>
                    <td>@if ($row['kualitas'] !== 0 && $row['totalpengiriman'] !== 0){{ number_format( (($row['kualitas'] / $row['totalpengiriman']) * 100),2,',', '.') }} % @endif</td>
                    <td>@if ($row['nonkualitas'] !== 0 && $row['totalpengiriman'] !== 0){{ number_format( (($row['nonkualitas'] / $row['totalpengiriman'] ) * 100),2,',', '.') }} % @endif</td>
                    <td>@if ($row['kualitas'] + $row['nonkualitas'] !== 0 && $row['totalpengiriman'] !== 0){{ number_format( (( ($row['kualitas'] + $row['nonkualitas']) / $row['totalpengiriman']) * 100),2,',', '.')  }} % @endif</td>
                    <td>{{ number_format($row['totalpengiriman'],2,',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<script>

</script>