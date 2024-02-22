@if ($download == true)
    @php
        header('Content-Transfer-Encoding: none');
        header('Content-type: application/vnd-ms-excel');
        header('Content-type: application/x-msexcel');
        header('Content-Disposition: attachment; filename=Retur Per Kategori.xls');
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
<div style="overflow-x:auto;">
    <table class="table table-sm table-hover table-striped table-bordered table-small">
        <thead>
            <tr>
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
            @foreach ($dataReturExport as $key => $row)
                <tr>
                    <td>{{ App\Models\Category::where('id', $row->kategori_item)->first()->nama }}</td>
                    @foreach ($countTotalKualitas as $key => $perKualitas)
                    <td>{{ number_format( App\Models\Retur::join('retur_item', 'retur.id', '=', 'retur_item.retur_id')
                        ->join('items','items.id' ,'=' ,'retur_item.item_id')
                        ->where('retur.no_ra', '!=', NULL)
                        ->where('retur_item.catatan', $perKualitas->catatan)
                        ->where('items.category_id', $row->kategori_item)
                        ->whereBetween('tanggal_retur', [$tanggal, $tanggal_akhir])
                        ->whereIn('retur.status', [1, 2])
                        ->groupBy('catatan')
                        ->sum('retur_item.berat'),2,',', '.') }}</td>
                @endforeach
                @foreach ($countTotalNonKualitas as $perNoKualitas)
                <td>
                    {{ number_format( App\Models\Retur::join('retur_item', 'retur.id', '=', 'retur_item.retur_id')
                    ->join('items','items.id' ,'=' ,'retur_item.item_id')
                    ->where('retur.no_ra', '!=', NULL)
                    ->where('retur_item.catatan', $perNoKualitas->catatan)
                    ->where('items.category_id', $row->kategori_item)
                    ->whereBetween('tanggal_retur', [$tanggal, $tanggal_akhir])
                    ->whereIn('retur.status', [1, 2])
                    ->sum('retur_item.berat'),2,',', '.') }}
                </td>
            @endforeach
            @php
            $totalItem = App\Models\Retur::join('retur_item', 'retur.id', '=', 'retur_item.retur_id')
                ->join('items', 'items.id', '=','retur_item.item_id')
                ->where('retur.no_ra', '!=', NULL)
                ->where('items.category_id', $row->kategori_item)
                ->whereBetween('tanggal_retur', [$tanggal, $tanggal_akhir])
                ->whereIn('retur.status', [1, 2])
                ->groupBy('items.category_id')
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
                <td colspan="1">Total</td>
                @foreach ($countTotalKualitas as $itemKualitas)
                    <td>
                        {{ number_format(App\Models\ReturItem::join('retur', 'retur.id', '=', 'retur_item.retur_id')
                            ->where('retur.no_ra', '!=', NULL)
                            ->where('catatan', $itemKualitas->catatan)
                            ->whereBetween('retur.tanggal_retur', [$tanggal, $tanggal_akhir])
                            ->whereIn('retur.status', [1, 2])
                            ->sum('retur_item.berat'),2,',', '.') ?? '' }}
                    </td>
                @endforeach
                @foreach ($countTotalNonKualitas as $itemNonKualitas)
                    <td>
                        {{ number_format(App\Models\ReturItem::join('retur', 'retur.id', '=', 'retur_item.retur_id')
                        ->where('retur.no_ra', '!=', NULL)->where('catatan', $itemNonKualitas->catatan)
                        ->whereBetween('retur.tanggal_retur', [$tanggal, $tanggal_akhir])
                        ->whereIn('retur.status', [1, 2])
                        ->sum('retur_item.berat'),2,',', '.') ?? '' }}
                    </td>
                @endforeach
                <td>
                    {{ number_format( App\Models\Retur::join('retur_item', 'retur.id', '=', 'retur_item.retur_id')
                    ->where('retur.no_ra', '!=', NULL)
                    ->whereBetween('tanggal_retur', [$tanggal, $tanggal_akhir])
                    ->whereIn('retur.status', [1, 2])
                    ->sum('retur_item.berat'),2,',', '.') }}
                </td>
            </tr>
            @foreach ($countPenanganan as $itempenanganan)
                <tr class="table-warning">
                    <td colspan="1">{{ $itempenanganan->penanganan }}</td>
                    @foreach ($getcatatan as $itemcatatan)
                        <td>
                            {{ number_format(App\Models\ReturItem::join('retur', 'retur.id', '=', 'retur_item.retur_id')
                            ->where('retur.no_ra', '!=', NULL)->where('penanganan', $itempenanganan->penanganan)
                            ->where('catatan', $itemcatatan->catatan)
                            ->whereBetween('retur.tanggal_retur', [$tanggal, $tanggal_akhir])
                            ->whereIn('retur.status', [1, 2])
                            ->sum('berat'),2,',', '.')  ?? '' }}
                        </td>
                    @endforeach
                    <td>
                        {{ number_format(App\Models\ReturItem::join('retur', 'retur.id', '=', 'retur_item.retur_id')
                        ->where('retur.no_ra', '!=', NULL)->where('penanganan', $itempenanganan->penanganan)
                        ->whereBetween('retur.tanggal_retur', [$tanggal, $tanggal_akhir])
                        ->whereIn('retur.status', [1, 2])
                        ->sum('berat'),2,',', '.')  ?? '' }}
                    </td>
                </tr>
            @endforeach

        </tfoot>
    </table>
    @if ($totalPengiriman == null)
    {{-- <p>belum ada data</p> --}}
@else
    <table>
        <tr class="table-primary">
            <td>Total Retur karena Kualitas</td>
            <td>
                {{ number_format($totalReturKualitas,2,',', '.' )  }}
            </td>
        </tr>
        <tr class="table-primary">
            <td>Total Retur karena Non Kualitas</td>
            <td>{{ number_format($totalReturNonKualitas,2,',', '.') }}</td>
        </tr>
        <tr class="table-primary">
            <td>Total Retur</td>
            <td>{{ number_format($totalRetur,2,',', '.') }}</td>
        </tr>
        <tr class="table-primary">
            <td>Total Pengiriman</td>
            <td>{{ number_format($totalPengiriman,2,',', '.') }}</td>
        </tr>
        <tr class="table-primary">
            <td>Peresentase Retur karena Kualitas</td>
            <td>{{ number_format(($totalReturKualitas / $totalPengiriman) * 100, 2,',', '.' ) }} %</td>
        </tr>
        <tr class="table-primary">
            <td>Peresentase Retur karena Non Kualitas</td>
            <td>{{ number_format(($totalReturNonKualitas / $totalPengiriman) * 100, 2,',', '.') }} %</td>
        </tr>
        <tr class="table-primary">
            <td>Total Peresentase Retur</td>
            <td>{{ number_format(($totalRetur / $totalPengiriman) * 100, 2,',', '.') }} %</td>
        </tr>
    </table>
@endif
</div>

<hr>


<div class="row mb-3">
    <div class="col-md-6">
        <table class="table table-sm table-hover table-striped table-bordered table-small">
            <thead>
                <tr>
                    <th rowspan="2">Tanggal</th>
                    <th colspan="3">Persentase Retur</th>
                    {{-- <td>Persentase Retur</td> --}}
                </tr>
                <tr>
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
                            $tot_kualitas = ($row['kualitas'] / $row['totalpengiriman']) * 100;
                            $tot_non_kualitas = ($row['nonkualitas'] / $row['totalpengiriman']) * 100;
                        }
                    @endphp
                    <tr>
                        <td>{{ $row['tanggal'] }}</td>
                        <td>{{ number_format($tot_kualitas,2,',', '.') ?? '' }} %</td>
                        <td>{{ number_format($tot_non_kualitas,2,',', '.') ?? '' }} %</td>
                        <td>{{ number_format($tot_non_kualitas + $tot_kualitas,2,',', '.') ?? '' }} %</td>
                    </tr>
                @endforeach

            </tbody>
        </table>
    </div>
    <div class="col-md-6">
        <figure class="highcharts-figure">
            <div id="container-order"></div>
        </figure>
    </div>
</div>

{{-- @section('footer') --}}

<script>
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

