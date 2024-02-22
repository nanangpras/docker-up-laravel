<div id="report-frozen"></div>
<script>
    Highcharts.chart('report-frozen', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Perbandingan Produksi Frozen'
        },
        xAxis: {
            categories: {!! $tgl !!}
        },
        yAxis: {
            title: {
                text: ''
            }
        },
        colors: ['#811010', '#50B432', '#ED561B', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263',
            '#6AF9C4'
        ],

        plotOptions: {
            line: {
                dataLabels: {
                    enabled: true
                },
                enableMouseTracking: false
            },
            column: {
                colorByPoint: true
            }
        },
        series: {!! $list !!}
    });
</script>
