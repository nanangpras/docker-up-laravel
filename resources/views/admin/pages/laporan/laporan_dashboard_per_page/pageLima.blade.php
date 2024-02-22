<div class="row mb-3 border-bottom">
    <div class="col-md-6">
        <figure class="highcharts-figure">
            <div id="container-plastik"></div>
        </figure>
    </div>
    <div class="col-md-6">
        <div class="font-weight-bold">Penggunaan Plastik</div>
        <div class="form-group outer-table-scroll">
            <table class="table default-table">
                <thead>

                    <tr>
                        <th>Nama</th>
                        <th>Tanggal</th>
                        <th>Qty</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data['ambil_pe'] as $row)
                    <tr>
                        <td>{{ $row->plastik_nama }}</td>
                        <td>{{ $row->tanggal_produksi }}</td>
                        <td class="text-right">{{ $row->jumlah }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="2">Total</th>
                        <th class="text-right">{{ number_format($data['ambil_pe_sum']) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>


    {{-- <div id="container-sebaran-karkas2"></div> --}}
<div id="container-sales"></div>

<div id="dashboard-loading-pageEnam" style="height: 30px">
    <div class="text-center mb-2 mt-2" style="position: absolute; left: 0; right: 0;">
        <img src="{{asset('loading.gif')}}" style="width: 20px"> Loading ...
    </div>
</div>

<script>
    Highcharts.chart('container-plastik', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: 'Top 10 Penggunaan Plastik'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        accessibility: {
            point: {
                valueSuffix: '%'
            }
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                }
            }
        },
        series: [{
            name: 'Prosentase',
            colorByPoint: true,
            data: {!! $plastik_pie !!}
        }]
    });
    </script>

<script>
    Highcharts.chart('container-sales', {
    
        chart: {
            type: 'column'
        },
    
        title: {
            text: 'Order Item vs Alokasi (Dalam kg)',
        },
    
        xAxis: {
            categories: {!! $spider_channel !!},
            tickmarkPlacement: 'on',
        },
    
        yAxis: {
            gridLineInterpolation: 'polygon',
            lineWidth: 0,
        },
    
        tooltip: {
            shared: true,
            pointFormat: '<span style="color:{series.color}">{series.name}: <b>{point.y:,.0f} kg</b><br/>'
        },
    
        plotOptions: {
            series: {
            label: {
                connectorAllowed: false
                },
            }
        },
        series: [{
            name: 'Order',
            data: {!! $spider_order !!},
        }, {
            name: 'Alokasi',
            data: {!! $spider_alokasi !!},
        }],
    
    });
    </script>