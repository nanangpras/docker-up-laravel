{{-- <div id="report-chart-4"></div>
<script>
Highcharts.chart('report-chart-4', {
    chart: {
        type: 'column'
    },
    title: {
        text: 'Hasil Produksi Karkas'
    },
    xAxis: {
        categories: {!! $tgl_karkas !!},
        crosshair: true
    },
    yAxis: {
        min: 0,
        title: {
            text: 'Kilogram (kg)'
        }
    },
    tooltip: {
        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
            '<td style="padding:0"><b>{point.y:.1f} kg</b></td></tr>',
        footerFormat: '</table>',
        shared: true,
        useHTML: true
    },
    plotOptions: {
        column: {
            pointPadding: 0.2,
            borderWidth: 0
        }
    },
    series: {!! json_encode($list_karkas) !!}
}); --}}
{{-- </script> --}}
<section>
    <table class="table table-sm default-table">
        <thead>
            <tr>
                <th rowspan="3">Nama Item</th>
            </tr>
            <tr>
                @foreach ($tgl_karkas as $i => $tgl)
                    @if($i!=0)
                    <td colspan="2">{{ $tgl }}</td>
                    @endif
                @endforeach
            </tr>
            <tr>
                @foreach ($tgl_karkas as $i => $tgl)
                    @if($i!=0)
                        <td>Qty</td>
                        <td>Berat</td>
                    @endif
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($karkas_group as $key)
                <tr>
                    <td>{{ $key->name }}</td>
                   

                    @foreach ($tgl_karkas as $no => $tgl)
                    
                        @php 
                            $table_tampil = "";
                            $max_no = 0;
                        @endphp
                        @foreach ($karkas as $val)
                            @if ($tgl == $val->tanggal && $key->name == $val->nama)
                                @php 
                                    $table_tampil = "<td>".$val->qty."</td><td>".$val->berat."</td>";
                                    $max_no = $no;
                                @endphp 
                            @endif
                        @endforeach

                        @if($no!=$max_no)
                            <td></td>
                            <td></td>
                        @else
                            {!!$table_tampil!!}
                        @endif
                    @endforeach


                </tr>
            @endforeach
        </tbody>
    </table>
</section>
