@if ($download == true)
    @php
        header('Content-Transfer-Encoding: none');
        header('Content-type: application/vnd-ms-excel');
        header('Content-type: application/x-msexcel');
        header('Content-Disposition: attachment; filename=Retur Per Item.xls');
    @endphp
@endif

{{-- @php
    $countTotalKualitas     = App\Models\ReturItem::where('kategori', 'Kualitas')->whereBetween('created_at', [$tanggal." 00:00:01", $tanggal_akhir." 23:59:59"])->groupBy('catatan')->get('catatan');
@endphp --}}
<style>
    th,
    td {
        border: 1px solid #ddd;
    }
</style>


<section class="panel">
        <div class="card-body">
            <div class="form-group">
                Jenis Item
                <select name="jenis" data-placeholder="Pilih Jenis" data-width="100%" class="form-control select2" id="jenisitem" required>
                    <option value=""></option>
                    <option value="semua" {{ $jenisitem == 'semua' ? 'selected': ''}}>Semua</option>
                    <option value="sampingan" {{ $jenisitem == 'sampingan' ? 'selected': ''}}>Sampingan</option>
                    <option value="nonsampingan" {{ $jenisitem == 'nonsampingan' ? 'selected': ''}}>Non Sampingan</option>
                </select>
            </div>
    </div>
</section>

<div style="overflow-x:auto;">
    <table class="table table-sm table-hover table-striped table-bordered table-small">
        <thead>
            <tr>
                <th rowspan="2">Item</th>
                <th rowspan="2">Kategori</th>
                @if (count($countTotalKualitas))
                    <th colspan="{{ count($countTotalKualitas) }}">Kualitas</th>
                @endif
                @if (count($countTotalNonKualitas))
                    <th colspan="{{ count($countTotalNonKualitas) }}">Non Kualitas</th>
                @endif
                <th rowspan="2">Total</th>
            </tr>
            <tr>
                @if ($countTotalKualitas)
                    @foreach ($countTotalKualitas as $item)
                        <th>{{ $item->catatan }}</th>
                    @endforeach
                @endif
                @if ($countTotalNonKualitas)
                    @foreach ($countTotalNonKualitas as $item)
                        <th>{{ $item->catatan }}</th>
                    @endforeach
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach ($dataReturExport as $row)
                <tr>
                    <td>{{ $row->nama_item }}</td>
                    <td>{{ App\Models\Category::where('id', $row->kategori_item)->first()->nama }}</td>
                    @foreach ($countTotalKualitas as $key => $perKualitas)
                        <td>{{ number_format( App\Models\Retur::join('retur_item', 'retur.id', '=', 'retur_item.retur_id')
                            ->where('retur.no_ra', '!=', NULL)
                            ->where('retur_item.catatan', $perKualitas->catatan)
                            ->where('retur_item.item_id', $row->id_item)
                            ->whereBetween('tanggal_retur', [$tanggal, $tanggal_akhir])
                            ->join('items', 'retur_item.item_id', '=', 'items.id')
                            ->where(function($query) use ($jenisitem) {
                                if ($jenisitem == 'sampingan') {
                                    $query->whereIn('items.category_id', ['4', '10', '16']);
                                } else if ($jenisitem == 'nonsampingan') {
                                    $query->whereNotIn('items.category_id', ['4', '10', '16']);
                                }
                            })
                            ->whereIn('retur.status', [1, 2])
                            ->groupBy('catatan')
                            ->sum('retur_item.berat'),2,',', '.') }}</td>
                    @endforeach
                    @foreach ($countTotalNonKualitas as $perNoKualitas)
                        <td>
                            {{ number_format( App\Models\Retur::join('retur_item', 'retur.id', '=', 'retur_item.retur_id')
                            ->where('retur.no_ra', '!=', NULL)
                            ->where('retur_item.catatan', $perNoKualitas->catatan)
                            ->where('retur_item.item_id', $row->id_item)
                            ->whereIn('retur.status', [1, 2])
                            ->join('items', 'retur_item.item_id', '=', 'items.id')
                            ->where(function($query) use ($jenisitem) {
                                if ($jenisitem == 'sampingan') {
                                    $query->whereIn('items.category_id', ['4', '10', '16']);
                                } else if ($jenisitem == 'nonsampingan') {
                                    $query->whereNotIn('items.category_id', ['4', '10', '16']);
                                }
                            })
                            // ->whereBetween('tanggal_retur', [$perNoKualitas->tanggal_retur, $perNoKualitas->tanggal_retur])
                            ->whereBetween('tanggal_retur', [$tanggal, $tanggal_akhir])
                            ->sum('retur_item.berat'),2,',', '.') }}
                        </td>
                    @endforeach
                    @php
                        $totalItem = App\Models\Retur::join('retur_item', 'retur.id', '=', 'retur_item.retur_id')
                            ->where('retur.no_ra', '!=', NULL)
                            ->where('retur_item.item_id', $row->id_item)
                            ->whereIn('retur.status', [1, 2])
                            ->join('items', 'retur_item.item_id', '=', 'items.id')
                            ->where(function($query) use ($jenisitem) {
                                if ($jenisitem == 'sampingan') {
                                    $query->whereIn('items.category_id', ['4', '10', '16']);
                                } else if ($jenisitem == 'nonsampingan') {
                                    $query->whereNotIn('items.category_id', ['4', '10', '16']);
                                }
                            })
                            ->whereBetween('tanggal_retur', [$tanggal, $tanggal_akhir])
                            ->sum('retur_item.berat');
                            // ->get('retur_item.berat')
                    @endphp
                    <td>
                        {{ number_format($totalItem,2,',', '.') ?? '' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2">Total</td>
                @foreach ($countTotalKualitas as $itemKualitas)
                    <td>
                        {{ number_format(App\Models\ReturItem::join('retur', 'retur.id', '=', 'retur_item.retur_id')
                        ->where('retur.no_ra', '!=', NULL)
                        ->where('retur_item.catatan', $itemKualitas->catatan)
                        ->join('items', 'retur_item.item_id', '=', 'items.id')
                        ->where(function($query) use ($jenisitem) {
                            if ($jenisitem == 'sampingan') {
                                $query->whereIn('items.category_id', ['4', '10', '16']);
                            } else if ($jenisitem == 'nonsampingan') {
                                $query->whereNotIn('items.category_id', ['4', '10', '16']);
                            }
                        })
                        ->whereIn('retur.status', [1, 2])
                        ->whereBetween('retur.tanggal_retur', [$tanggal, $tanggal_akhir])
                        ->sum('retur_item.berat'),2,',', '.') ?? '' }}
                    </td>
                @endforeach
                @foreach ($countTotalNonKualitas as $itemNonKualitas)
                    <td>
                        {{ number_format(App\Models\ReturItem::join('retur', 'retur.id', '=', 'retur_item.retur_id')
                        ->where('retur.no_ra', '!=', NULL)
                        ->join('items', 'retur_item.item_id', '=', 'items.id')
                        ->where(function($query) use ($jenisitem) {
                            if ($jenisitem == 'sampingan') {
                                $query->whereIn('items.category_id', ['4', '10', '16']);
                            } else if ($jenisitem == 'nonsampingan') {
                                $query->whereNotIn('items.category_id', ['4', '10', '16']);
                            }
                        })
                        ->where('retur_item.catatan', $itemNonKualitas->catatan)
                        ->whereIn('retur.status', [1, 2])
                        ->whereBetween('retur.tanggal_retur', [$tanggal, $tanggal_akhir])
                        ->sum('retur_item.berat'),2,',', '.') ?? '' }}
                    </td>
                @endforeach
                <td>
                    {{ number_format( App\Models\Retur::join('retur_item', 'retur.id', '=', 'retur_item.retur_id')
                    ->where('retur.no_ra', '!=', NULL)
                    ->join('items', 'retur_item.item_id', '=', 'items.id')
                    ->where(function($query) use ($jenisitem) {
                        if ($jenisitem == 'sampingan') {
                            $query->whereIn('items.category_id', ['4', '10', '16']);
                        } else if ($jenisitem == 'nonsampingan') {
                            $query->whereNotIn('items.category_id', ['4', '10', '16']);
                        }
                    })
                    ->whereIn('retur.status', [1, 2])
                    ->whereBetween('retur.tanggal_retur', [$tanggal, $tanggal_akhir])
                    ->sum('retur_item.berat'),2,',', '.') }}
                </td>
            </tr>
            @foreach ($countPenanganan as $itempenanganan)
                <tr class="table-warning">
                    <td colspan="2">{{ $itempenanganan->penanganan }}</td>
                    @foreach ($countTotalKualitas as $itemcatatan)
                        <td>
                            {{ number_format(App\Models\ReturItem::join('retur', 'retur.id', '=', 'retur_item.retur_id')
                            ->where('retur.no_ra', '!=', NULL)
                            ->where('retur_item.penanganan', $itempenanganan->penanganan)
                            ->join('items', 'retur_item.item_id', '=', 'items.id')
                            ->where(function($query) use ($jenisitem) {
                                if ($jenisitem == 'sampingan') {
                                    $query->whereIn('items.category_id', ['4', '10', '16']);
                                } else if ($jenisitem == 'nonsampingan') {
                                    $query->whereNotIn('items.category_id', ['4', '10', '16']);
                                }
                            })
                            ->whereIn('retur.status', [1, 2])
                            ->where('retur_item.catatan', $itemcatatan->catatan)->whereBetween('retur.tanggal_retur', [$tanggal, $tanggal_akhir])
                            ->sum('retur_item.berat'),2,',', '.')  ?? '' }}
                        </td>
                    @endforeach
                    @foreach ($countTotalNonKualitas as $itemNonKualitas)
                        
                        <td>
                            {{ number_format(App\Models\ReturItem::join('retur', 'retur.id', '=', 'retur_item.retur_id')
                            ->where('retur.no_ra', '!=', NULL)
                            ->join('items', 'retur_item.item_id', '=', 'items.id')
                            ->where(function($query) use ($jenisitem) {
                                if ($jenisitem == 'sampingan') {
                                    $query->whereIn('items.category_id', ['4', '10', '16']);
                                } else if ($jenisitem == 'nonsampingan') {
                                    $query->whereNotIn('items.category_id', ['4', '10', '16']);
                                }
                            })
                            ->where('retur_item.penanganan', $itempenanganan->penanganan)
                            ->where('retur_item.catatan', $itemNonKualitas->catatan)
                            ->whereBetween('retur.tanggal_retur', [$tanggal, $tanggal_akhir])
                            ->whereIn('retur.status', [1, 2])
                            ->sum('retur_item.berat'),2,',', '.')  ?? '' }}
                        </td>
                    @endforeach
                </tr>
            @endforeach

        </tfoot>
    </table>
    <table>
        <thead>
            <tr style="background-color: antiquewhite">
                <th></th>
                <th>Fresh</th>
                <th>Frozen</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Total Retur karena Kualitas</td>
                <td>{{number_format($dataTotalBawah['totKualitasfresh'],1,',', '.')}}</td>
                <td>{{number_format($dataTotalBawah['totKualitasfrozen'],1,',', '.')}}</td>
                <td>{{number_format($dataTotalBawah['totalkualitas'],1,',', '.')}}</td>
            </tr>
            <tr>
                <td>Total Retur karena Non Kualitas</td>
                <td>{{number_format($dataTotalBawah['totNonKualitasfresh'],1,',', '.')}}</td>
                <td>{{number_format($dataTotalBawah['totNonKualitasfrozen'],1,',', '.')}}</td>
                <td>{{number_format($dataTotalBawah['totalNonkualitas'],1,',', '.')}}</td>
            </tr>
            <tr>
                <td>Total Retur</td>
                <td>{{number_format($dataTotalBawah['returTotKualitasfresh'],1,',', '.')}}</td>
                <td>{{number_format($dataTotalBawah['returTotKualitasfrozen'],1,',', '.')}}</td>
                <td>{{number_format($dataTotalBawah['totalretur'],1,',', '.')}}</td>
            </tr>
            <tr>
                <td>Total Pengiriman</td>
                <td>{{number_format($dataTotalBawah['totPengirimanfresh'],1,',', '.')}}</td>
                <td>{{number_format($dataTotalBawah['totPengirimanfrozen'],1,',', '.')}}</td>
                <td>{{number_format($dataTotalBawah['totalpengiriman'],1,',', '.')}}</td>
            </tr>
            <tr>
                <td>Peresentase Retur karena Kualitas</td>
                <td>{{number_format($dataTotalBawah['prsReturKualitasfresh'],1,',', '.')}} %</td>
                <td>{{number_format($dataTotalBawah['prsReturKualitasfrozen'],1,',', '.')}} %</td>
                <td>{{number_format($dataTotalBawah['persentaseReturTotalFreshK'] ,1,',', '.') }} %</td>
            </tr>
            <tr>
                <td>Peresentase Retur karena Non Kualitas</td>
                <td>{{number_format($dataTotalBawah['prsRetrNonKualitasfresh'],1,',', '.')}} %</td>
                <td>{{number_format($dataTotalBawah['prsRetrNonKualitasfrozen'],1,',', '.')}} %</td>
                <td>{{number_format($dataTotalBawah['persentaseReturTotalFreshNK'],1,',', '.') }} %</td>
            </tr>
            <tr>
                <td>Total Peresentase Retur</td>
                <td>{{number_format($dataTotalBawah['prsReturFresh'],1,',', '.')}} %</td>
                <td>{{number_format($dataTotalBawah['prsReturFrozen'],1,',', '.')}} %</td>
                <td>{{number_format($dataTotalBawah['persentaseReturTotal'],1,',', '.') }} %</td>
            </tr>
        </tbody>
    </table>

</div>
<hr>
<div class="row mb-3">
    <div class="col">
        <table class="table table-sm table-hover table-striped table-bordered table-small">
            <thead>
                <tr>
                    <th rowspan="2" style="text-align: center">Tanggal</th>
                    <th colspan="3" style="text-align: center">Persentase Retur Fresh</th>
                    <th colspan="3" style="text-align: center">Persentase Retur Frozen</th>
                    <th colspan="3" style="text-align: center">Persentase Retur Gabungan</th>
                    {{-- <td>Persentase Retur</td> --}}
                </tr>
                <tr>
                    <th>Kualitas</th>
                    <th>Non Kualitas</th>
                    <th>Total</th>
                    <th>Kualitas</th>
                    <th>Non Kualitas</th>
                    <th>Total</th>
                    <th>Kualitas</th>
                    <th>Non Kualitas</th>
                    <th>Total</th>
                </tr>
            </thead>

            <tbody>
                @php
                    $tot_kualitas = 0;
                    $tot_non_kualitas = 0;
                @endphp
                @foreach ($date_range as $row)
                    @php
                        if ($row['totalpengiriman']) {

                            if ($row['kualitas'] !== 0 && $row['totalpengiriman'] !== 0) {
                                $tot_kualitas = ($row['kualitas'] / $row['totalpengiriman']) * 100;
                            } else {
                                $tot_kualitas = 0;
                            }

                            if ($row['nonkualitas'] !== 0 && $row['totalpengiriman'] !== 0) {
                                $tot_non_kualitas = ($row['nonkualitas'] / $row['totalpengiriman']) * 100;

                            } else {
                                $tot_non_kualitas = 0;

                            }

                            if ($row['kualitasfresh'] !== 0 && $row['totkirimfresh'] !== 0) {
                                $tot_kualitas_fresh = ($row['kualitasfresh'] / $row['totkirimfresh'] ) *100;

                            } else {
                                $tot_kualitas_fresh = 0;

                            }

                            if ($row['nonkualitasfresh'] !== 0 && $row['totkirimfresh'] !== 0) {
                                $tot_nonkualitas_fresh = ($row['nonkualitasfresh'] / $row['totkirimfresh'])*100;

                            } else {
                                $tot_nonkualitas_fresh = 0;

                            }

                            if ($row['kualitasfrozen'] !== 0 && $row['totkirimfrozen'] !== 0) {
                                $tot_kualitas_frozen = ($row['kualitasfrozen'] / $row['totkirimfrozen']) *100;

                            } else {
                                $tot_kualitas_frozen = 0;
                            }

                            if ($row['nonkualitasfrozen'] !== 0 && $row['totkirimfrozen'] !== 0) {
                                $tot_nonkualitas_frozen = ($row['nonkualitasfrozen'] / $row['totkirimfrozen']) *100;

                            } else {
                                $tot_nonkualitas_frozen = 0;
                            }

                            $totalReturFresh    = (($row['kualitasfresh'] + $row['nonkualitasfresh']) / $row['totkirimfresh']) * 100;
                            $totalReturFrozen   = (($row['kualitasfrozen'] + $row['nonkualitasfrozen'])/ $row['totkirimfrozen']) * 100;
                            $totalRetur         = (($row['kualitas'] + $row['nonkualitas'])/ $row['totalpengiriman']) * 100;
                        }
                    @endphp
                    <tr>
                        <td>{{ $row['tanggal'] }}</td>
                        <td>{{ number_format($tot_kualitas_fresh, 1) ?? '' }} %</td>
                        <td>{{ number_format($tot_nonkualitas_fresh, 1) ?? '' }} %</td>
                        <td>{{ number_format($totalReturFresh, 1) ?? '' }} %</td>
                        <td>{{ number_format($tot_kualitas_frozen,1) ?? '' }} %</td>
                        <td>{{ number_format($tot_nonkualitas_frozen, 1) ?? '' }} %</td>
                        <td>{{ number_format($totalReturFrozen, 1) ?? '' }} %</td>
                        <td>{{ number_format($tot_kualitas, 1) ?? '' }} %</td>
                        <td>{{ number_format($tot_non_kualitas, 1) ?? '' }} %</td>
                        <td>{{ number_format($totalRetur, 1) ?? '' }} %</td>
                    </tr>
                @endforeach

            </tbody>
        </table>
    </div>
    {{-- <div class="col-md-6">
        <figure class="highcharts-figure">
            <div id="container-order"></div>
        </figure>
    </div> --}}
</div>
<div class="row">
    <div class="col">
        <figure class="highcharts-figure">
            <div id="container-order"></div>
        </figure>
    </div>
</div>


{{-- @section('footer') --}}

<script>

    $("#jenisitem").on("change", function() {
        loadExportRetur();
    })


    $('.select2').select2({
        theme: 'bootstrap4'
    });

    Highcharts.chart('container-order', {
        chart: {
            type: 'line'
        },
        title: {
            text: 'Persentase Retur'
        },
        subtitle: {
            text: 'Data retur bedasarkan tanggal'
        },
        xAxis: {
            categories: {!! $tgl_mingguan !!}
        },
        yAxis: {
            title: {
                text: '%'
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
        series: {!! $alokasi !!}
    });
</script>
{{-- @endsection --}}
