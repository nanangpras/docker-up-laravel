<section class="panel">
    <div class="card-header font-weight-bold">SISA PENYIAPAN SAMPAI TANGGAL {{ $tanggal_akhir }}</div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 pr-md-1">
                <figure class="highcharts-figure">
                    <div id="container-gudang"></div>
                </figure>
            </div>
            <div class="col-md-6 pl-md-1">
                <table class="table default-table">
                    <thead>
                        <tr>
                            <th>Gudang</th>
                            <th>Stock Masuk (Kg)</th>
                            <th>Stock Keluar (Kg)</th>
                            <th>Sisa (Kg)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $gudang_arr =   "[" ;
                        @endphp
                        @foreach ($stockgudang as $row)
                        @php
                            $masuk          =   App\Models\Gudang::stock_masuk($tanggal_awal, $tanggal_akhir, $row->id, 'masuk', 'berat_awal') ;
                            $keluar         =   App\Models\Gudang::stock_masuk($tanggal_awal, $tanggal_akhir, $row->id, 'keluar', 'berat_awal') ;
                            $gudang_arr     .=  "{ name: '" . $row->code . "', y: " . ($masuk - $keluar) . "}," ;
                        @endphp
                        <tr>
                            <td>{{ $row->code }}</td>
                            <td class="text-right">{{ number_format($masuk, 2) }}</td>
                            <td class="text-right">{{ number_format($keluar, 2) }}</td>
                            <td class="text-right {{ $masuk - $keluar < 0 ? 'text-danger font-weight-bold' : '' }}">{{ number_format($masuk - $keluar, 2) }}</td>
                        </tr>
                        @endforeach
                        @php
                            $gudang_arr .=  "]" ;
                        @endphp
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6 pr-1">
                <div class="card">
                    <div class="card-header"> Stock Booking (Kg)</div>
                    <div class="card-body p-2">
                        <table class="table default-table">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Berat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($konsumen['stock_book'] as $item)
                                <tr>
                                    <td>{{ $item->productitems->nama }}</td>
                                    <td class="text-right">{{ number_format($item->sisa, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
    
            <div class="col-md-6 pl-1">
                <div class="card">
                    <div class="card-header"> Stock Free (Kg)</div>
                    <div class="card-body p-2">
                        <table class="table default-table">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Berat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($konsumen['stock_free'] as $item)
                                <tr>
                                    <td>{{ $item->productitems->nama }}</td>
                                    <td class="text-right">{{ number_format($item->sisa, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

    Highcharts.chart('container-gudang', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: ''
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
            data: {!! $gudang_arr !!}
        }]
    });
</script>