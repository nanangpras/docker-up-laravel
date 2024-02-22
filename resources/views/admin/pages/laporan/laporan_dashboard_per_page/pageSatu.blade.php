<div class="row">
    <div class="col-lg-5 pr-lg-1">

        <div class="row mb-3">
            <div class="col">
                <div class="border rounded p-1">
                    @php
                        $rt = $data['rendemen_tangkap'];
                        $rk = $data['rendemen_kirim'];
                        $rendemen_total = 0;
                        if ($rt == 0) {
                            $rendemen_total = $rk;
                        }

                        if ($rk == 0) {
                            $rendemen_total = $rt;
                        }

                        if ($rk != 0 && $rt != 0) {
                            $rendemen_total = ($rk * $data['mobil_kirim'] + $rt * $data['mobil_tangkap']) / ($data['mobil_tangkap'] + $data['mobil_kirim']);
                        }
                    @endphp
                    <small>Rendemen Total </small>
                    <div class="font-weight-bold">{{ number_format($rendemen_total, 2) }} %</div>
                    <div class="proj-progress-card">
                        <div class="">
                            <div class="progress thin-bar">
                                <div class="progress-bar progress-bar-default bg-default"
                                    style="width:{{ number_format($rendemen_total, 2) }}%"></div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col pr-1">
                <div class="border rounded p-1 mb-2">
                    <small>Rendemen Tangkap </small>
                    <div class="font-weight-bold">{{ number_format($data['rendemen_tangkap'], 2) }} %</div>
                    <small>({{ $data['mobil_tangkap'] }} Mobil)</small>
                    <div class="proj-progress-card">
                        <div class="">
                            <div class="progress thin-bar">
                                <div class="progress-bar progress-bar-info bg-info"
                                    style="width:{{ number_format($data['rendemen_tangkap'], 2) }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col pl-1">
                <div class="border rounded p-1 mb-2">
                    <small>Rendemen Kirim </small>
                    <div class="font-weight-bold">{{ number_format($data['rendemen_kirim'], 2) }} %</div>
                    <small>({{ $data['mobil_kirim'] }} Mobil)</small>
                    <div class="proj-progress-card">
                        <div class="">
                            <div class="progress thin-bar">
                                <div class="progress-bar progress-bar-success bg-success"
                                    style="width:{{ number_format($data['rendemen_kirim'], 2) }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-1"><b>Informasi Selesai Potong ({{ $data['mobil_tangkap'] + $data['mobil_kirim'] }}
                Mobil) <a href="{{ url('admin/bukubesar') }}" target="_blank"><span
                        class="fa fa-share"></span></a></b></div>
        <div class="row">
            <div class="col pr-1">
                <div class="border rounded p-1 mb-2">
                    <small>Berat RPA</small>
                    <div class="font-weight-bold">{{ number_format($data['berat_rpa'], 2) }}</div>
                    <small>100%</small>
                </div>
            </div>
            <div class="col px-1">
                <div class="border rounded p-1 mb-2">
                    <small>Berat Grading</small>
                    <div class="font-weight-bold">{{ number_format($data['berat_grading'], 2) }}</div>
                    @php
                        $percent_grading = 0;
                    @endphp
                    @if ($data['berat_rpa'] > 0)
                        <small>{{ number_format(($data['berat_grading'] / $data['berat_rpa']) * 100, 2) }}%</small>
                        @php
                            $percent_grading = number_format(($data['berat_grading'] / $data['berat_rpa']) * 100, 2);
                        @endphp
                    @endif
                    <small style="font-size: 6pt">(*70-72%)</small>
                </div>
            </div>
            <div class="col px-1">
                <div class="border rounded p-1 mb-2">
                    <small>Berat Evis</small>
                    <div class="font-weight-bold">{{ number_format($data['berat_evis'], 2) }}</div>
                    @php
                        $percent_evis = 0;
                    @endphp
                    @if ($data['berat_rpa'] > 0)
                        <small>{{ number_format(($data['berat_evis'] / $data['berat_rpa']) * 100, 2) }}%</small>
                        @php
                            $percent_evis = number_format(($data['berat_evis'] / $data['berat_rpa']) * 100, 2);
                        @endphp
                    @endif
                    <small style="font-size: 6pt">(*20-22%)</small>
                </div>
            </div>
            <div class="col pl-1">
                <div class="border rounded p-1 mb-2">
                    <small>Darah Bulu</small>
                    <div class="font-weight-bold">
                        {{ number_format( (6 * $data['berat_rpa']) /100 , 2) }}
                    </div>
                    @php
                        $percent_darah = 0;
                    @endphp
                    @if ($data['berat_rpa'] > 0)
                        <small>{{ number_format( 6,2) }}%</small>
                        @php
                            $percent_darah = number_format(6, 2);
                        @endphp
                    @endif
                    <small style="font-size: 6pt">(6%)</small>
                </div>
            </div>
            <div class="col pl-1">
                <div class="border rounded p-1 mb-2">
                    <small>Total</small>
                    @php 
                        $darahbulu          = (6 * $data['berat_rpa']) /100;
                        $totalBeratProduksi = $data['berat_grading'] + $data['berat_evis'] + $darahbulu;
                    @endphp
                    <div class="font-weight-bold">
                        {{ number_format($totalBeratProduksi) }}
                    </div>
                    <small><span id="totalBeratRPA"></span> %</small>
                </div>
            </div>
            {{-- 
            <div class="col pl-1">
                <div class="border rounded p-1 mb-2">
                    <small>Darah Bulu</small>
                    <div class="font-weight-bold">
                        {{number_format($data['berat_rpa'] - $data['berat_grading'] - $data['berat_evis'], 2) }}
                    </div>
                    @php
                        $percent_darah = 0;
                    @endphp
                    @if ($data['berat_rpa'] > 0)
                        <small>{{ number_format((($data['berat_rpa'] - $data['berat_grading'] - $data['berat_evis']) / $data['berat_rpa']) * 100, 2) }}%</small>
                        @php
                            $percent_darah = number_format((($data['berat_rpa'] - $data['berat_grading'] - $data['berat_evis']) / $data['berat_rpa']) * 100, 2);
                        @endphp
                    @endif
                    <small style="font-size: 6pt">(*6-8%)</small>
                </div>
            </div>
            --}}
        </div>
        <div class="row">
            <div class="col">
                <div class="progress">
                    <div class="progress-bar progress-bar-success bg-success" role="progressbar"
                        style="width:{{ $percent_grading }}%; color: white">
                        Grading {{ $percent_grading }}%
                    </div>
                    <div class="progress-bar progress-bar-default bg-default" role="progressbar"
                        style="width:{{ $percent_evis }}%; color: white">
                        Evis {{ $percent_evis }}%
                    </div>
                    <div class="progress-bar progress-bar-danger bg-danger" role="progressbar"
                        style="width:{{ $percent_darah }}%; color: white">
                        {{ $percent_darah }}%
                    </div>
                </div>
            </div>
        </div>
        @php 
            $beratTotalRPA = $percent_grading + $percent_evis + $percent_darah;
        @endphp
        <script>
            var totalBeratRPA = "{{ $beratTotalRPA }}"; 
            $("#totalBeratRPA").text(totalBeratRPA)
        </script>
        <div class="row mb-3 mt-3">
            <div class="col pr-1">
                <div class="border rounded p-1 mb-2">
                    <small>Ekor RPA</small>
                    <div class="font-weight-bold">{{ number_format($data['ekor_rpa']) }}</div>
                </div>
            </div>
            <div class="col px-1">
                <div class="border rounded p-1 mb-2">
                    <small>Ekor Grading</small>
                    <div class="font-weight-bold">{{ number_format($data['ekor_grading']) }}</div>
                </div>
            </div>
            <div class="col pl-1">
                <div class="border rounded p-1 mb-2">
                    <small>Selisih Ekor</small>
                    <div class="font-weight-bold">{{ number_format($data['ekor_rpa'] - $data['ekor_grading']) }}</div>
                </div>
            </div>
            <div class="col pl-1">
                <div class="border rounded p-1 mb-2">
                    <small>Kematian Ekor</small>
                    <div class="font-weight-bold">{{ number_format($ekor_mati) }}</div>
                </div>
            </div>
        </div>

        <div class="mb-1"><b>Informasi Mobil <a href="{{ url('admin/purchasing') }}" target="_blank"><span
                        class="fa fa-share"></span></a></b></div>
        <div class="row mb-3">
            <div class="col pr-1">
                <div class="border rounded p-1 mb-2">
                    <small>Jumlah Supplier</small>
                    <div class="font-weight-bold">{{ number_format($data['count_purchase']) }}</div>
                </div>
            </div>
            <div class="col px-1">
                <div class="border rounded p-1 mb-2">
                    <small>Jumlah PO Mobil</small>
                    <div class="font-weight-bold">{{ number_format($data['count_production']) }}</div>
                </div>
            </div>
            <div class="col pl-1">
                <div class="border rounded p-1 mb-2">
                    <small>Selesai Potong</small>
                    <div class="font-weight-bold">{{ number_format($data['data_diproses']) }}</div>
                </div>
            </div>
        </div>
        @if($data['data_po_pending']>0)
        <div class="row mb-3">
            <div class="col">
                <div class="border rounded p-1 mb-2">
                    <small>PO Tunda</small>
                    <div class="font-weight-bold">{{ number_format($data['data_po_pending']) }}</div>
                </div>
            </div>
        </div>
        @endif

        <div class="row">
            <div class="col-lg-4 col-6 pr-1">
                <div class="mb-1"><b>Informasi DO <a href="{{ url('admin/security') }}" target="_blank"><span
                                class="fa fa-share"></span></a></b></div>
                <div class="border rounded p-1 mb-2">
                    <small>Ekor DO</small>
                    <div class="font-weight-bold">{{ number_format($data['data_production']->ekor) }}</div>
                </div>
                <div class="border rounded p-1 mb-2">
                    <small>Berat DO</small>
                    <div class="font-weight-bold">{{ number_format($data['data_production']->berat, 2) }}</div>
                </div>
                <div class="border rounded p-1 mb-2">
                    <small>Rerata DO</small>
                    <div class="font-weight-bold">{{ number_format($data['data_production']->rerata, 2) }}</div>
                </div>
            </div>


            <div class="col-lg-8 col-6 pl-1">
                <div class="mb-1"><b>Informasi Terima LB <a href="{{ url('admin/lpah') }}"
                            target="_blank"><span class="fa fa-share"></span></a></b></div>
                <div class="row">
                    <div class="col-6 pr-1">
                        <div class="border rounded p-1 mb-2">
                            <small>Ekoran Seckel</small>
                            <div class="font-weight-bold">{{ number_format($data['data_production']->seckle) ?? 0 }}
                            </div>
                        </div>
                        <div class="border rounded p-1 mb-2">
                            <small>Kg Terima</small>
                            <div class="font-weight-bold">
                                {{ number_format($data['data_production']->kg_terima, 2) ?? 0 }}</div>
                        </div>
                        <div class="border rounded p-1 mb-2">
                            <small>Rerata Terima LB</small>
                            <div class="font-weight-bold">
                                {{ number_format($data['data_production']->rata_terima, 2) }}</div>
                        </div>
                    </div>
                    <div class="col-6 pl-1">
                        <div class="border rounded p-1 mb-2">
                            <small>Susut Tangkap</small>
                            <div class="font-weight-bold">{{ number_format($data['susut_tangkap'], 2) }} %</div>
                        </div>
                        <div class="border rounded p-1 mb-2">
                            <small>Susut Kirim</small>
                            <div class="font-weight-bold">{{ number_format($data['susut_kirim'], 2) }} %</div>
                        </div>
                        <div class="border rounded p-1 mb-2">
                            <small>Selisih Seckel</small>
                            <div class="font-weight-bold">{{ number_format($data['seckle']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-2 mb-1"><b>Informasi Grading <a href="{{ url('admin/grading') }}" target="_blank"><span
                        class="fa fa-share"></span></a></b></div>
        <div class="row">
            <div class="col-6 pr-1">
                <div class="border rounded p-1 mb-2">
                    <small>Ekoran Grading</small>
                    <div class="font-weight-bold">{{ number_format($data['data_grading']->ekor) ?? 0 }}</div>
                </div>
                <div class="border rounded p-1 mb-2">
                    <small>Berat Grading</small>
                    <div class="font-weight-bold">{{ number_format($data['data_grading']->berat, 2) ?? 0 }}</div>
                </div>
            </div>
            <div class="col-6 pl-1">
                <div class="border rounded p-1 mb-2">
                    <small>Selisih Seckel-Grading</small>
                    <div class="font-weight-bold">
                        {{ number_format(($data['data_production']->seckle ?? 0) - ($data['data_grading']->ekor ?? 0)) }}
                    </div>
                </div>
                <div class="border rounded p-1 mb-2">
                    <small>Rerata Grading</small>
                    <div class="font-weight-bold">{{ number_format($data['data_grading']->ratarata, 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-7 pl-lg-2">
        <div id="container-sebaran-karkas"></div>
        <hr>
        <div id="container-presentase-karkas"></div>
    </div>
</div>
<div class="row">
    <div class="col-lg-6">
        <div class="mt-lg-3" id="container-sebaran-produksi"></div>
    </div>
    <div class="col-lg-6">
        <div class="font-weight-bold">Presentase Sebaran Ayam</div>
        <div class="form-group outer-table-scroll">
            <table class="table default-table">
                <thead>
                    <tr>
                        <th>Nama Item</th>
                        <th>Ekor/Pcs/Pack</th>
                        <th>Persen</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $total = 0;
                        $total_item_grading = count($data_grading);

                        foreach ($data_grading as $i => $row):
                            $total = $total + $row[1];
                        endforeach;

                        $data_presentasi = [];
                    @endphp
                    @foreach ($data_grading as $i => $row)
                        @php
                            $data_presentasi[] = [
                                'name' => $row[0],
                                'y' => ($row[1] / $total) * 100,
                            ];
                        @endphp

                        <tr>
                            <td>{{ $row[0] }}</td>
                            <td>{{ $row[1] }}</td>
                            <td class="text-right">{{ number_format(($row[1] / $total) * 100, 2) }} %</td>
                        </tr>

                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<div id="dashboard-loading-pageDua" style="height: 30px">
    <div class="text-center mb-2 mt-2" style="position: absolute; left: 0; right: 0;">
        <img src="{{asset('loading.gif')}}" style="width: 20px"> Loading ...
    </div>
</div>

@php
$ayam_utuh = $data['ayam_utuh'];
$parting = $data['parting'];
$parting_marinasi = $data['parting_marinasi'];
$boneless = $data['boneless'];
$frozen = $data['frozen'];
@endphp

<script>
    $('.select2').select2({
        theme: 'bootstrap4'
    })
</script>

<script>
    Highcharts.chart('container-sebaran-produksi', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: 'Sebaran Produksi'
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
                name: 'Whole Chicken',
                y: {!! $ayam_utuh !!},
            }, {
                name: 'Parting',
                y: {!! $parting !!},
            }, {
                name: 'Parting M',
                y: {!! $parting_marinasi !!},
            }, {
                name: 'Boneless',
                y: {!! $boneless !!},
            }, {
                name: 'Frozen',
                y: {!! $frozen !!},
            }]
        }]
    });
</script>


<script>
    Highcharts.chart('container-presentase-karkas', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: 'Sebaran Karkas'
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
            data: {!! json_encode($data_presentasi) !!}
        }]
    });
</script>

<script>
    Highcharts.chart('container-sebaran-karkas', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Sebaran Karkas'
        },
        xAxis: {
            type: 'category',
            labels: {
                rotation: -90,
                style: {
                    fontSize: '10px',
                    fontFamily: 'Verdana, sans-serif'
                }
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Ekor'
            }
        },
        legend: {
            enabled: false
        },
        tooltip: {
            pointFormat: '<b>{point.y} ekor</b>'
        },
        series: [{
            name: 'Population',
            data: {!! $arr !!},
            dataLabels: {
                enabled: true,
                rotation: -90,
                color: '#FFFFFF',
                align: 'right',
                format: '{point.y}', // one decimal
                y: 10, // 10 pixels down from the top
                style: {
                    fontSize: '13px',
                    fontFamily: 'Verdana, sans-serif'
                }
            }
        }]
    });
</script>
