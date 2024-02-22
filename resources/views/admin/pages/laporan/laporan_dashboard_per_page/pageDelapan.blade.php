<div class="row mb-3">
    <div class="col-md-6">
        <div class="font-weight-bold">Summary Bahan Baku M</div>
        <div class="form-group outer-table-scroll">
            <table class="table default-table">
                <thead>
                    <tr>
                        <th>Bahan Baku</th>
                        <th>Kondisi BB</th>
                        <th>Asal</th>
                        <th>Ekor/Pcs/Pack</th>
                        <th>Berat</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $ekor = 0;
                        $berat = 0;
                    @endphp
                    @foreach ($produksi['bb_marinasi'] as $row)
                        @php
                            $ekor += $row->total;
                            $berat += $row->kg;
                        @endphp
                        <tr>
                            <td>{{ $row->item->nama }}</td>
                            <td>{{ $row->asal_tujuan == 'gradinggabungan' ? $row->bb_kondisi : $row->asal_tujuan }}<br>{{ date('d/m/Y', strtotime($row->tanggal_produksi)) }}
                            </td>
                            <td>{{ $row->asal_tujuan }}</td>
                            <td class="text-right">{{ number_format($row->total) }}</td>
                            <td class="text-right">{{ number_format($row->kg, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3">Total Bahan Baku M</th>
                        <th class="text-right">{{ number_format($ekor) }}</th>
                        <th class="text-right">{{ number_format($berat, 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <div class="col-md-6">
        <div class="font-weight-bold">Summary Produksi M</div>
        <div class="form-group outer-table-scroll">
            <table class="table default-table">
                <thead>
                    <tr>
                        <th>Nama Item</th>
                        <th>Tujuan</th>
                        <th>Ekor/Pcs/Pack</th>
                        <th>Berat</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $ekor = 0;
                        $berat = 0;
                    @endphp
                    @foreach ($produksi['fg_marinasi'] as $row)
                        @php
                            $ekor += $row->total;
                            $berat += $row->kg;
                        @endphp
                        <tr>
                            <td>
                                <a href="{{ route('dashboard.detailproduksi', ['item' => $row->item_id, 'tanggal_awal' => $tanggal_awal, 'tanggal_akhir' => $tanggal_akhir]) }}">
                                    {{ $row->item->nama }} {{$row->id}}
                                </a>
                            </td>
                            <td>
                                @if($row->kategori=="1")
                                    <span class="status status-danger">[ABF]</span>
                                @elseif($row->kategori=="2")
                                    <span class="status status-warning">[EKSPEDISI]</span>
                                @elseif($row->kategori=="3")
                                    <span class="status status-warning">[TITIP CS]</span>
                                @else
                                    <span class="status status-info">[CHILLER]</span>
                                @endif
                            </td>
                            <td class="text-right">{{ number_format($row->total) }}</td>
                            <td class="text-right">{{ number_format($row->kg, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th>Total Hasil Produksi M</th>
                        <th class="text-right" colspan="2">{{ number_format($ekor) }}</th>
                        <th class="text-right" colspan="2">{{ number_format($berat, 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <div class="col-md-12">
        <div class="mb-1"><b>Produktivitas Regu M</b></div>
        <div class="row mb-3">
            <div class="col pr-1">
                <div class="border rounded p-1 mb-2">
                    <small>Jam Awal Input</small>
                    <div class="font-weight-bold">{{ $produksi['waktu_marinasi']['awal'] ?? "-" }}</div>
                </div>
            </div>
            <div class="col px-1">
                <div class="border rounded p-1 mb-2">
                    <small>Jam Akhir Input</small>
                    <div class="font-weight-bold">{{ $produksi['waktu_marinasi']['akhir'] ?? "-" }}</div>
                </div>
            </div>
            <div class="col pl-1">
                <div class="border rounded p-1 mb-2">
                    <small>Total Jam Input</small>
                    <div class="font-weight-bold">{{ gmdate("H:i", $produksi['waktu_marinasi']['jam_kerja'] ?? "0")}} Jam/Menit</div>
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col pr-1">
                <div class="border rounded p-1 mb-2">
                    <small><span class="status status-danger">[ABF]</span></small>
                    <div class="font-weight-bold">{{ $produksi['countabf'] ?? "0" }}</div>
                </div>
            </div>
            <div class="col px-1">
                <div class="border rounded p-1 mb-2">
                    <small><span class="status status-warning">[EKSPEDISI]</span></small>
                    <div class="font-weight-bold">{{ $produksi['countekspedisi'] ?? "0" }}</div>
                </div>
            </div>
            <div class="col pl-1">
                <div class="border rounded p-1 mb-2">
                    <small><span class="status status-warning">[TITIP CS]</span></small>
                    <div class="font-weight-bold">{{ $produksi['countcs'] ?? "0"}}</div>
                </div>
            </div>
            <div class="col pl-1">
                <div class="border rounded p-1 mb-2">
                    <small><span class="status status-info">[CHILLER]</span></small>
                    <div class="font-weight-bold">{{ $produksi['countchiller'] ?? "0"}}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="dashboard-loading-pageSembilan" style="height: 30px">
    <div class="text-center mb-2 mt-2" style="position: absolute; left: 0; right: 0;">
        <img src="{{asset('loading.gif')}}" style="width: 20px"> Loading ...
    </div>
</div>