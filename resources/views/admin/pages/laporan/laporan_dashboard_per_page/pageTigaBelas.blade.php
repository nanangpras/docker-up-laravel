<div class="border-bottom pb-1 mb-2 font-weight-bold">
    SUMMARY DETAIL KONSUMEN PERIODE {{ $tanggal_awal }}{{ $tanggal_akhir != $tanggal_awal ? ' - ' . $tanggal_akhir : '' }}
</div>

<div class="border mb-3 p-2">
    <div class="row">
        <div class="col-md-4 pr-md-1">
            <div class="font-weight-bold">10 Konsumen Order Qty Terbanyak</div>
            <table class="table default-table">
                <thead>
                    <tr>
                        <th>Konsumen</th>
                        <th>Qty</th>
                        <th>Berat</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($konsumen['top10qty'] as $row)
                    <tr>
                        <td>
                            <a href="{{ route('dashboard.konsumenorder', ['konsumen' => $row->customer_id, 'tanggal_awal' => $tanggal_awal, 'tanggal_akhir' => $tanggal_akhir]) }}">
                                {{ $row->nama }}
                            </a>
                        </td>
                        <td class="text-right">{{ number_format($row->jumlah ?? 0) }}</td>
                        <td class="text-right">{{ number_format($row->kg, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="col-md-4 px-md-1">
            <div class="font-weight-bold">10 Konsumen Order Berat Terbanyak</div>
            <table class="table default-table">
                <thead>
                    <tr>
                        <th>Konsumen</th>
                        <th>Berat</th>
                        <th>%</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($konsumen['top10berat'] as $row)
                    <tr>
                        <td>
                            <a href="{{ route('dashboard.konsumenorder', ['konsumen' => $row->customer_id, 'tanggal_awal' => $tanggal_awal, 'tanggal_akhir' => $tanggal_akhir]) }}">
                                {{ $row->nama }}
                            </a>
                        </td>
                        <td class="text-right">{{ number_format($row->kg, 2) }}</td>
                        <td class="text-right">{{ number_format($row->kg/$konsumen['totalberat'] * 100, 2) }} %</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="col-md-4 pl-md-1">
            <div class="font-weight-bold">10 Konsumen Retur Terbanyak</div>
            <table class="table default-table">
                <thead>
                    <tr>
                        <th>Konsumen</th>
                        <th>Berat</th>
                        <th>%</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($konsumen['top10retur'] as $row)
                    <tr>
                        <td>
                            <a href="{{ route('dashboard.konsumenretur', ['konsumen' => $row->customer_id, 'tanggal_awal' => $tanggal_awal, 'tanggal_akhir' => $tanggal_akhir]) }}">
                                {{ $row->konsumen }}
                            </a>
                        </td>
                        <td class="text-right">{{ number_format($row->kg, 2) }}</td>
                        <td class="text-right">@if($row->kg !== 0 && $konsumen['totalkiriman'] !== 0) {{ number_format($row->kg/$konsumen['totalkiriman'] * 100, 2 ?? 0) }} % @endif</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>