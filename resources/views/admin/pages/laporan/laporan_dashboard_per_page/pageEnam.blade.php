<div class="mb-2 font-weight-bold">SUMMARY SALES CHANNEL (Bedasarkan Tanggal Kirim)</div>
<div class="row mb-3 border-bottom">
    @foreach ($sales_channel as $row)
    <div class="col-3">
        <div class="card mb-3">
            <div class="card-header">
                {{ $row->sales_channel }}
                <a href="{{ route('dashboard.saleschannel', ['channel' => $row->sales_channel, 'tanggal_awal' => $tanggal_awal, 'tanggal_akhir' => $tanggal_akhir]) }}" target="_blank"><span class="fa fa-share"></span></a>
            </div>
            <div class="card-body p-2">
                <div class="border-bottom">
                    <div class="row">
                        <div class="col pr-1">Order</div>
                        <div class="col pl-1 text-right font-weight-bold pl-1">{{ $row->total }}</div>
                    </div>
                </div>
                <div class="border-bottom">
                    <div class="row">
                        <div class="col pr-1">Pending</div>
                        <div class="col pl-1 text-right font-weight-bold pl-1">{{ $row->pending }}</div>
                    </div>
                </div>
                <div class="border-bottom">
                    <div class="row">
                        <div class="col pr-1">Selesai</div>
                        <div class="col pl-1 text-right font-weight-bold pl-1">{{ $row->selesai }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="row mb-3 border-bottom">
    <div class="col-md-6">
        <figure class="highcharts-figure">
            <div id="container-order"></div>
        </figure>
    </div>

    <div class="col-md-6">
        <div class="font-weight-bold">Order Item Pending</div>
        <div class="small text-info">*) Data bedasarkan tanggal kirim</div>
        <div class="form-group mt-2 outer-table-scroll">
            <table class="table default-table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>tanggal</th>
                        <th>Qty</th>
                        <th>Berat</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $qty    =   0;
                        $kg     =   0;
                    @endphp
                    @foreach ($item_pending as $row)
                    @php
                        $qty        +=  $row->total ;
                        $kg         +=  $row->kg ;
                    @endphp
                    <tr>
                        <td>
                            <a href="{{ route('dashboard.itempending', ['item' => $row->item_id, 'tanggal_awal' => $tanggal_awal, 'tanggal_akhir' => $tanggal_akhir]) }}">
                                {{ $row->nama_detail }}
                            </a>
                        </td>
                        <td>{{ $row->tanggal_kirim }}</td>
                        <td class="text-right">{{ $row->total ?? 0 }}</td>
                        <td class="text-right">{{ $row->kg ?? 0 }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="2">Total</th>
                        <th class="text-right">{{ number_format($qty) }}</th>
                        <th class="text-right">{{ number_format($kg, 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<div id="dashboard-loading-pageTujuh" style="height: 30px">
    <div class="text-center mb-2 mt-2" style="position: absolute; left: 0; right: 0;">
        <img src="{{asset('loading.gif')}}" style="width: 20px"> Loading ...
    </div>
</div>

<script>
    Highcharts.chart('container-order', {
        chart: {
            type: 'line'
        },
        title: {
            text: 'Pemenuhan Alokasi Order'
        },
        subtitle: {
            text: 'Data bedasarkan tanggal kirim'
        },
        xAxis: {
            categories: {!! $tgl_order !!}
        },
        yAxis: {
            title: {
                text: 'Jumlah SO'
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