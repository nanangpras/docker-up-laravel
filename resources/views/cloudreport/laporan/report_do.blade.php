<div id="report-chart-3"></div>
<script>
Highcharts.chart('report-chart-3', {
    chart: {
        type: 'column'
    },
    title: {
        text: 'Hasil Ekor DO - Penerimaan'
    },
    xAxis: {
        categories: {!! $tgl_do !!},
        crosshair: true
    },
    yAxis: {
        min: 0,
        title: {
            text: 'Ekor'
        }
    },
    tooltip: {
        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
            '<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
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
    series: {!! $list_do !!}
});
</script>
