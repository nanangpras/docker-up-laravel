<div class="mb-0">
    <div class="card-body p-2">
        <figure class="highcharts-figure">
            <div id="container-wh"></div>
        </figure>
    </div>
</div>

@php
 $beratmasuk    = json_encode($arrayData['beratmasuk']);
 $beratkeluar   = json_encode($arrayData['beratkeluar']);
 $xtanggal      = json_encode($arrayData['tanggal']);
@endphp
<script>

    var beratmasuk    = {!! $beratmasuk !!}
    var beratkeluar   = {!! $beratkeluar !!}
    var xtanggal      = {!! $xtanggal !!}
    
    Highcharts.chart('container-wh', {
        chart: {
            type: 'area'
        },
        title: {
            text: 'WH Activity'
        },
        xAxis: {
            categories: xtanggal
        },
        yAxis: {
            title: {
                text: 'Kilogram'
            },
            labels: {
                formatter: function () {
                    return this.value / 1000 + 'k';
                }
            }
        },
        tooltip: {
            pointFormat: '{series.name} <b>{point.y:,.0f}</b>'
        },
        plotOptions: {
            area: {
                pointStart: 0,
                marker: {
                    enabled: false,
                    symbol: 'circle',
                    radius: 2,
                    states: {
                        hover: {
                            enabled: true
                        }
                    }
                }
            }
        },
        series: [{
            name: 'INBOUND FROZEN ABF',
            data: beratmasuk
        }, {
            name: 'OUTBOUND FROZEN ABF',
            data: beratkeluar
        }]
    });

</script>
