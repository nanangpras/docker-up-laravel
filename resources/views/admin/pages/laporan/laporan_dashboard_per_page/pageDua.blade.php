
<div style="overflow-x: scroll">
<div style="width: 3000px">
<div id="container-sebaran-karkas2"></div>
</div>
</div>
<figure class="highcharts-figure">
    <div id="container-presentase-lb"></div>
</figure>
{{-- <div id="container-sebaran-karkas3"></div> --}}
<div class="row">
    <div class="col-lg-4">
        <div id="returnon"></div>
    </div>
    <div class="col-lg-4">
        <div id="returbar"></div>
    </div>
    <div class="col-lg-4">
        <div id="returpercent"></div>
    </div>
</div>
<div id="dashboard-loading-pageTiga" style="height: 30px">
    <div class="text-center mb-2 mt-2" style="position: absolute; left: 0; right: 0;">
        <img src="{{asset('loading.gif')}}" style="width: 20px"> Loading ...
    </div>
</div>



<script>
    Highcharts.chart('returnon', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: 'Retur'
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
            name: '',
            colorByPoint: true,
            data: [{
                name: 'Kualitas',
                y: {!! $returkualitas !!},
            }, {
                name: 'Non Kualitas',
                y: {!! $returnonkualitas !!},
            }]
        }]
    });
</script>

<script>
    Highcharts.chart('returpercent', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: 'Retur'
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
            name: '',
            colorByPoint: true,
            data: {!!json_encode($retur_percent)!!}
        }]
    });
</script>

<script>
    Highcharts.chart('returbar', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: 'Retur Alasan'
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
            name: '',
            colorByPoint: true,
            data: {!!json_encode($retur_alasan)!!}
        }]
    });
</script>



{{-- <script>
    Highcharts.chart('container-sebaran-karkas3', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Kg Sebaran Grading/Supplier'
        },
        subtitle: {
            text: ''
        },
        xAxis: {
            categories: {!! $dataKarkasSupplier !!}
        },
        yAxis: {
            title: {
                text: 'Jumlah'
            }
        },
        plotOptions: {
            line: {
                dataLabels: {
                    enabled: true
                },
                enableMouseTracking: true
            }
        },
        series: {!! $supplier !!}
    });
</script> --}}

<script>
    Highcharts.chart('container-sebaran-karkas2', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Prosentase Sebaran Grading/Supplier'
        },
        subtitle: {
            text: ''
        },
        xAxis: {
            categories: {!! $dataKarkasSupplier !!}
        },
        yAxis: {
            title: {
                text: 'Jumlah'
            },
            labels: {
            format: '{value}%'
            },
        },
        tooltip: {
            formatter: function() {
            var numerator = 250;
            return (this.y).toFixed(1) + '%';
            }
        },
        plotOptions: {
            line: {
                dataLabels: {
                    enabled: true
                },
                enableMouseTracking: true
            }
        },
        series: {!! $supplier !!}
    });
</script>

<script>
Highcharts.chart('container-presentase-lb', {
    title: {
        text: 'Grafik Chart Harga LB Berdasarkan Ukuran'
    },
    xAxis: {
        categories: {!! $getTanggalPotongChartLB !!},
    },
    plotOptions: {
        series: {
        label: {
            connectorAllowed: false
        },
        }
    },
    yAxis: {
            title: {
                text: 'Harga'
            }
        },

    series: {!! $chartLB !!},

    responsive: {
        rules: [{
        condition: {
            maxWidth: 500
        },
        }]
    }
});
</script>
