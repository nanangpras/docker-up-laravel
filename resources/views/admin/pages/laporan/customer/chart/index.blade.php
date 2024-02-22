<figure class="highcharts-figure">
    <div id="container-chartso"></div>
</figure>

@section('header')
    <link rel="stylesheet" type="text/css" href="{{asset('')}}highcharts/highcharts-style.css"/>
@stop

@section('footer')
    <script src="{{ asset("highcharts/highcharts.js") }}"></script>
    <script src="{{ asset("highcharts/highcharts-more.js") }}"></script>
    <script src="{{ asset("highcharts/exporting.js") }}"></script>
    <script src="{{ asset("highcharts/export-data.js") }}"></script>
    <script src="{{ asset("highcharts/accessibility.js") }}"></script>
    <script>
        Highcharts.chart('container-chartso', {
            chart: {
                type: 'line'
            },
            title: {
                text: 'Berat Order vs Fulfill Order'
            },
            xAxis: {
                categories: {!! $tanggal !!}
            },
            yAxis: {
                title: {
                    text: 'Kilogram (Kg)'
                }
            },
            plotOptions: {
                line: {
                    dataLabels: {
                        enabled: true
                    },
                    enableMouseTracking: false
                }
            },
            series: [{
                name: 'Berat Order',
                data: {!! $berat !!}
            }, {
                name: 'Fulfill Order',
                data: {!! $order !!}
            }]
        });
    </script>
@stop
