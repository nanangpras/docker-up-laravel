<div class="card mb-4">
    <div class="card-header">Cash on Hand Gudang</div>
    {{-- <div class="card-body p-2">
        <table class="table default-table mb-0">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Booking</th>
                    <th>Free</th>
                    <th>Total - Kg</th>
                    <th>Total - %</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><a href="{{ route('dashboard.cashonhandgudang', ['regu' => 'whole', 'tanggal_awal' => $tanggal_awal, 'tanggal_akhir' => $tanggal_akhir]) }}">KARKAS</a></td>
                    <td class="text-right">{{ number_format(App\Models\Product_gudang::coh('whole', 'booking', $tanggal_awal, $tanggal_akhir), 2) }}</td>
                    <td class="text-right">{{ number_format(App\Models\Product_gudang::coh('whole', 'free', $tanggal_awal, $tanggal_akhir), 2) }}</td>
                    <td class="text-right">{{ number_format(App\Models\Product_gudang::coh('whole', 'all', $tanggal_awal, $tanggal_akhir), 2) }}</td>
                    <td class="text-right">{{ number_format(App\Models\Product_gudang::coh('whole', 'all', $tanggal_awal, $tanggal_akhir) ? ((App\Models\Product_gudang::coh('whole', 'all', $tanggal_awal, $tanggal_akhir) / App\Models\Product_gudang::coh(FALSE, 'all', $tanggal_awal, $tanggal_akhir)) * 100) : 0, 2) }}%</td>
                </tr>
                <tr>
                    <td><a href="{{ route('dashboard.cashonhandgudang', ['regu' => 'marinasi', 'tanggal_awal' => $tanggal_awal, 'tanggal_akhir' => $tanggal_akhir]) }}">MARINASI</a></td>
                    <td class="text-right">{{ number_format(App\Models\Product_gudang::coh('marinasi', 'booking', $tanggal_awal, $tanggal_akhir), 2) }}</td>
                    <td class="text-right">{{ number_format(App\Models\Product_gudang::coh('marinasi', 'free', $tanggal_awal, $tanggal_akhir), 2) }}</td>
                    <td class="text-right">{{ number_format(App\Models\Product_gudang::coh('marinasi', 'all', $tanggal_awal, $tanggal_akhir), 2) }}</td>
                    <td class="text-right">{{ number_format(App\Models\Product_gudang::coh('marinasi', 'all', $tanggal_awal, $tanggal_akhir) ? ((App\Models\Product_gudang::coh('marinasi', 'all', $tanggal_awal, $tanggal_akhir) / App\Models\Product_gudang::coh(FALSE, 'all', $tanggal_awal, $tanggal_akhir)) * 100) : 0, 2) }}%</td>
                </tr>
                <tr>
                    <td><a href="{{ route('dashboard.cashonhandgudang', ['regu' => 'parting', 'tanggal_awal' => $tanggal_awal, 'tanggal_akhir' => $tanggal_akhir]) }}">PARTING</a></td>
                    <td class="text-right">{{ number_format(App\Models\Product_gudang::coh('parting', 'booking', $tanggal_awal, $tanggal_akhir), 2) }}</td>
                    <td class="text-right">{{ number_format(App\Models\Product_gudang::coh('parting', 'free', $tanggal_awal, $tanggal_akhir), 2) }}</td>
                    <td class="text-right">{{ number_format(App\Models\Product_gudang::coh('parting', 'all', $tanggal_awal, $tanggal_akhir), 2) }}</td>
                    <td class="text-right">{{ number_format(App\Models\Product_gudang::coh('parting', 'all', $tanggal_awal, $tanggal_akhir) ? ((App\Models\Product_gudang::coh('parting', 'all', $tanggal_awal, $tanggal_akhir) / App\Models\Product_gudang::coh(FALSE, 'all', $tanggal_awal, $tanggal_akhir)) * 100) : 0, 2) }}%</td>
                </tr>
                <tr>
                    <td><a href="{{ route('dashboard.cashonhandgudang', ['regu' => 'boneless', 'tanggal_awal' => $tanggal_awal, 'tanggal_akhir' => $tanggal_akhir]) }}">BONELESS</a></td>
                    <td class="text-right">{{ number_format(App\Models\Product_gudang::coh('boneless', 'booking', $tanggal_awal, $tanggal_akhir), 2) }}</td>
                    <td class="text-right">{{ number_format(App\Models\Product_gudang::coh('boneless', 'free', $tanggal_awal, $tanggal_akhir), 2) }}</td>
                    <td class="text-right">{{ number_format(App\Models\Product_gudang::coh('boneless', 'all', $tanggal_awal, $tanggal_akhir), 2) }}</td>
                    <td class="text-right">{{ number_format(App\Models\Product_gudang::coh('boneless', 'all', $tanggal_awal, $tanggal_akhir) ? ((App\Models\Product_gudang::coh('boneless', 'all', $tanggal_awal, $tanggal_akhir) / App\Models\Product_gudang::coh(FALSE, 'all', $tanggal_awal, $tanggal_akhir)) * 100) : 0, 2) }}%</td>
                </tr>
                <tr>
                    <td><a href="{{ route('dashboard.cashonhandgudang', ['regu' => 'byproduct', 'tanggal_awal' => $tanggal_awal, 'tanggal_akhir' => $tanggal_akhir]) }}">BY PRODUCT</a></td>
                    <td class="text-right">{{ number_format(App\Models\Product_gudang::coh('byproduct', 'booking', $tanggal_awal, $tanggal_akhir), 2) }}</td>
                    <td class="text-right">{{ number_format(App\Models\Product_gudang::coh('byproduct', 'free', $tanggal_awal, $tanggal_akhir), 2) }}</td>
                    <td class="text-right">{{ number_format(App\Models\Product_gudang::coh('byproduct', 'all', $tanggal_awal, $tanggal_akhir), 2) }}</td>
                    <td class="text-right">{{ number_format(App\Models\Product_gudang::coh('byproduct', 'all', $tanggal_awal, $tanggal_akhir) ? ((App\Models\Product_gudang::coh('byproduct', 'all', $tanggal_awal, $tanggal_akhir) / App\Models\Product_gudang::coh(FALSE, 'all', $tanggal_awal, $tanggal_akhir)) * 100) : 0, 2) }}%</td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <th>TOTAL</th>
                    <th class="text-right">{{ number_format(App\Models\Product_gudang::coh(FALSE, 'booking', $tanggal_awal, $tanggal_akhir), 2) }}</th>
                    <th class="text-right">{{ number_format(App\Models\Product_gudang::coh(FALSE, 'free', $tanggal_awal, $tanggal_akhir), 2) }}</th>
                    <th class="text-right">{{ number_format(App\Models\Product_gudang::coh(FALSE, 'all', $tanggal_awal, $tanggal_akhir), 2) }}</th>
                    <th class="text-right">100,00%</th>
                </tr>
            </tfoot>
        </table>
    </div> --}}
</div>

<div class="row mb-4">
    <div class="col-md-6 pr-1">
        <div class="card">
            <div class="card-header">10 Stock Booking (Kg)</div>
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
            <div class="card-header">10 Stock Free (Kg)</div>
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

<div class="card mb-4">
    <div class="card-body p-2">
        {{-- <table class="table default-table">
            <thead>
                <tr>
                    <th>WH Activity</th>
                    @for ($i = 0; $i < 7; $i++)
                    <th>{{ Carbon\Carbon::parse(Carbon\Carbon::parse($tanggal_awal)->subDays(6))->addDays($i)->format('D - d') }} ({{ $i+1 }})</th>
                    @endfor
                    <th>TOTAL - KG</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>INBOUND FROZEN ABF</td>
                    @php
                        $hitung_inbound =   0 ;
                        $inbound        =   '[' ;
                    @endphp
                    @for ($i = 0; $i < 7; $i++)
                    @php
                        $inbon          =   App\Models\Product_gudang::inbound(Carbon\Carbon::parse(Carbon\Carbon::parse($tanggal_awal)->subDays(6))->addDays($i)) ;
                        $inbound        .=  $inbon. ',' ;
                        $hitung_inbound +=  $inbon;
                    @endphp
                    <td class="text-right">{{ number_format($inbon, 2) }}</td>
                    @endfor
                    <td class="text-right">{{ number_format($hitung_inbound, 2) }}</td>
                </tr>
                <tr>
                    <td>OUTBOUND FROZEN ABF</td>
                    @php
                        $inbound            .=  ']' ;
                        $hitung_outbound    =   0 ;
                        $outbound           =   '[' ;
                    @endphp
                    @for ($i = 0; $i < 7; $i++)
                    @php
                        $outbon         =   App\Models\Product_gudang::outbound(Carbon\Carbon::parse(Carbon\Carbon::parse($tanggal_awal)->subDays(6))->addDays($i)) ;
                        $outbound       .=  $outbon. ',' ;
                        $hitung_outbound +=  $outbon
                    @endphp
                    <td class="text-right">{{ number_format($outbon, 2) }}</td>
                    @endfor
                    <td class="text-right">{{ number_format($hitung_outbound, 2) }}</td>
                    @php
                        $outbound       .=  ']' ;
                    @endphp
                </tr>
            </tbody>
        </table> --}}

        <figure class="highcharts-figure">
            <div id="container-wh"></div>
        </figure>

    </div>
</div>

<div class="card mb-4">
    <div class="card-header font-weight-bold">STOCK GUDANG SAMPAI TANGGAL {{ $tanggal_akhir }}</div>
    <div class="card-body p-2">
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
                        @foreach ($gudang as $row)
                        @php
                            $masuk          =   App\Models\Gudang::stock_masuk($tanggal_awal, $tanggal_akhir, $row->id, 'masuk', 'berat') ;
                            $keluar         =   App\Models\Gudang::stock_masuk($tanggal_awal, $tanggal_akhir, $row->id, 'keluar', 'berat') ;
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
</div>

@php
 $beratmasuk    = json_encode($arrayData['beratmasuk']);
 $beratkeluar   = json_encode($arrayData['beratkeluar']);
 $xtanggal      = json_encode($arrayData['tanggal']);
@endphp
<script>
    $('.select2').select2({
        theme: 'bootstrap4'
    })
    var gudangid    = $("#all_gudang").val();
    var beratmasuk  = {!! $beratmasuk !!}
    var beratkeluar = {!! $beratkeluar !!}
    var xtanggal    = {!! $xtanggal !!}
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
