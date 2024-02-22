<div id="report-chart-2"></div>
<script>
Highcharts.chart('report-chart-2', {
    chart: {
        type: 'column'
    },
    title: {
        text: 'Hasil Produksi Evis'
    },
    xAxis: {
        categories: {!! $tgl_evis !!},
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
    series: {!! $list_evis !!}
});
</script>
