<div class="row mb-3">
    <div class="col-md-6">
        <div class="font-weight-bold">Summary Bahan Baku Frozen</div>
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
                    @foreach ($produksi['bb_frozen'] as $row)
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
                        <th colspan="3">Total Bahan Baku Frozen</th>
                        <th class="text-right">{{ number_format($ekor) }}</th>
                        <th class="text-right">{{ number_format($berat, 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <div class="col-md-6">
        <div class="font-weight-bold">Summary Produksi Frozen</div>
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
                    @foreach ($produksi['fg_frozen'] as $row)
                        @php
                            $ekor += $row->total;
                            $berat += $row->kg;
                        @endphp
                        <tr>
                            <td>{{ $row->item->nama }}</td>
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
                        <th>Total Hasil Produksi Frozen</th>
                        <th class="text-right" colspan="2">{{ number_format($ekor) }}</th>
                        <th class="text-right" colspan="2">{{ number_format($berat, 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="col-md-12">
        <div class="mb-1"><b>Produktivitas Regu Frozen</b></div>
        <div class="row mb-3">
        <div class="col pr-1">
            <div class="border rounded p-1 mb-2">
                <small>Jam Awal Input</small>
                <div class="font-weight-bold">{{ $produksi['waktu_frozen']['awal'] ?? "-" }}</div>
            </div>
        </div>
        <div class="col px-1">
            <div class="border rounded p-1 mb-2">
                <small>Jam Akhir Input</small>
                <div class="font-weight-bold">{{ $produksi['waktu_frozen']['akhir'] ?? "-" }}</div>
            </div>
        </div>
        <div class="col pl-1">
            <div class="border rounded p-1 mb-2">
                <small>Total Jam Input</small>
                <div class="font-weight-bold">{{ gmdate("H:i", $produksi['waktu_frozen']['jam_kerja'] ?? "0")}} Jam/Menit</div>
            </div>
        </div>
        </div>

        <div class="row mb-3">
            <div class="col pr-1">
                <div class="border rounded p-1 mb-2">
                    <small><span class="status status-danger">[ABF]</span></small>
                    <div class="font-weight-bold">{{ $produksi['countabf_frozen'] ?? "0" }}</div>
                </div>
            </div>
            <div class="col px-1">
                <div class="border rounded p-1 mb-2">
                    <small><span class="status status-warning">[EKSPEDISI]</span></small>
                    <div class="font-weight-bold">{{ $produksi['countekspedisi_frozen'] ?? "0" }}</div>
                </div>
            </div>
            <div class="col pl-1">
                <div class="border rounded p-1 mb-2">
                    <small><span class="status status-warning">[TITIP CS]</span></small>
                    <div class="font-weight-bold">{{ $produksi['countcs_frozen'] ?? "0"}}</div>
                </div>
            </div>
            <div class="col pl-1">
                <div class="border rounded p-1 mb-2">
                    <small><span class="status status-info">[CHILLER]</span></small>
                    <div class="font-weight-bold">{{ $produksi['countchiller_frozen'] ?? "0"}}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="font-weight-bold">Penjualan Sampingan Main Product</div>
        <div class="form-group outer-table-scroll">
            <table class="table default-table">
                <thead>
                    <tr>
                        <th>Nama Item</th>
                        <th>Ekor/Pcs/Pack</th>
                        <th>Berat</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $ekor = 0;
                        $berat = 0;
                    @endphp
                    @foreach ($produksi['main_product'] as $row)
                        @php
                            $ekor += $row->total;
                            $berat += $row->kg;
                        @endphp
                        <tr>
                            <td>{{ $row->nama }}</td>
                            <td class="text-right">{{ number_format($row->total) }}</td>
                            <td class="text-right">{{ number_format($row->kg, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th>Total Jual</th>
                        <th class="text-right">{{ number_format($ekor) }}</th>
                        <th class="text-right">{{ number_format($berat, 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <div class="col-md-4">
        <div class="font-weight-bold">Bahanbaku Jual Sampingan</div>
        <div class="form-group outer-table-scroll">
            <table class="table default-table">
                <thead>
                    <tr>
                        <th>Nama Item</th>
                        <th>Ekor/Pcs/Pack</th>
                        <th>Berat</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $ekor = 0;
                        $berat = 0;
                    @endphp
                    @foreach ($produksi['jual_sampingan'] as $row)
                        @php
                            $ekor += $row->total;
                            $berat += $row->kg;
                        @endphp
                        <tr>
                            <td>{{ $row->nama }}</td>
                            <td class="text-right">{{ number_format($row->total) }}</td>
                            <td class="text-right">{{ number_format($row->kg, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th>Total Jual Sampingan</th>
                        <th class="text-right">{{ number_format($ekor) }}</th>
                        <th class="text-right">{{ number_format($berat, 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="col-md-4">
        <div class="font-weight-bold">Stock on Hand Chiller</div>
        <div class="form-group outer-table-scroll">
            <table class="table default-table">
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
                        <td><a href="{{ route('dashboard.cashonhand', ['regu' => 'whole', 'tanggal_awal' => $tanggal_awal, 'tanggal_akhir' => $tanggal_akhir]) }}">KARKAS</a></td>
                        <td class="text-right">{{ number_format(App\Models\Chiller::coh('whole', 'booking', $tanggal_awal, $tanggal_akhir), 2) }}</td>
                        <td class="text-right">{{ number_format(App\Models\Chiller::coh('whole', 'free', $tanggal_awal, $tanggal_akhir), 2) }}</td>
                        <td class="text-right">{{ number_format(App\Models\Chiller::coh('whole', 'all', $tanggal_awal, $tanggal_akhir), 2) }}</td>
                        <td class="text-right">{{ number_format(App\Models\Chiller::coh('whole', 'all', $tanggal_awal, $tanggal_akhir) ? ((App\Models\Chiller::coh('whole', 'all', $tanggal_awal, $tanggal_akhir) / App\Models\Chiller::coh(FALSE, 'all', $tanggal_awal, $tanggal_akhir)) * 100) : 0, 2) }}%</td>
                    </tr>
                    <tr>
                        <td><a href="{{ route('dashboard.cashonhand', ['regu' => 'marinasi', 'tanggal_awal' => $tanggal_awal, 'tanggal_akhir' => $tanggal_akhir]) }}">M</a></td>
                        <td class="text-right">{{ number_format(App\Models\Chiller::coh('marinasi', 'booking', $tanggal_awal, $tanggal_akhir), 2) }}</td>
                        <td class="text-right">{{ number_format(App\Models\Chiller::coh('marinasi', 'free', $tanggal_awal, $tanggal_akhir), 2) }}</td>
                        <td class="text-right">{{ number_format(App\Models\Chiller::coh('marinasi', 'all', $tanggal_awal, $tanggal_akhir), 2) }}</td>
                        <td class="text-right">{{ number_format(App\Models\Chiller::coh('marinasi', 'all', $tanggal_awal, $tanggal_akhir) ? ((App\Models\Chiller::coh('marinasi', 'all', $tanggal_awal, $tanggal_akhir) / App\Models\Chiller::coh(FALSE, 'all', $tanggal_awal, $tanggal_akhir)) * 100) : 0, 2) }}%</td>
                    </tr>
                    <tr>
                        <td><a href="{{ route('dashboard.cashonhand', ['regu' => 'parting', 'tanggal_awal' => $tanggal_awal, 'tanggal_akhir' => $tanggal_akhir]) }}">PARTING</a></td>
                        <td class="text-right">{{ number_format(App\Models\Chiller::coh('parting', 'booking', $tanggal_awal, $tanggal_akhir), 2) }}</td>
                        <td class="text-right">{{ number_format(App\Models\Chiller::coh('parting', 'free', $tanggal_awal, $tanggal_akhir), 2) }}</td>
                        <td class="text-right">{{ number_format(App\Models\Chiller::coh('parting', 'all', $tanggal_awal, $tanggal_akhir), 2) }}</td>
                        <td class="text-right">{{ number_format(App\Models\Chiller::coh('parting', 'all', $tanggal_awal, $tanggal_akhir) ? ((App\Models\Chiller::coh('parting', 'all', $tanggal_awal, $tanggal_akhir) / App\Models\Chiller::coh(FALSE, 'all', $tanggal_awal, $tanggal_akhir)) * 100) : 0, 2) }}%</td>
                    </tr>
                    <tr>
                        <td><a href="{{ route('dashboard.cashonhand', ['regu' => 'boneless', 'tanggal_awal' => $tanggal_awal, 'tanggal_akhir' => $tanggal_akhir]) }}">BONELESS</a></td>
                        <td class="text-right">{{ number_format(App\Models\Chiller::coh('boneless', 'booking', $tanggal_awal, $tanggal_akhir), 2) }}</td>
                        <td class="text-right">{{ number_format(App\Models\Chiller::coh('boneless', 'free', $tanggal_awal, $tanggal_akhir), 2) }}</td>
                        <td class="text-right">{{ number_format(App\Models\Chiller::coh('boneless', 'all', $tanggal_awal, $tanggal_akhir), 2) }}</td>
                        <td class="text-right">{{ number_format(App\Models\Chiller::coh('boneless', 'all', $tanggal_awal, $tanggal_akhir) ? ((App\Models\Chiller::coh('boneless', 'all', $tanggal_awal, $tanggal_akhir) / App\Models\Chiller::coh(FALSE, 'all', $tanggal_awal, $tanggal_akhir)) * 100) : 0, 2) }}%</td>
                    </tr>
                    <tr>
                        <td><a href="{{ route('dashboard.cashonhand', ['regu' => 'byproduct', 'tanggal_awal' => $tanggal_awal, 'tanggal_akhir' => $tanggal_akhir]) }}">BY PRODUCT</a></td>
                        <td class="text-right">{{ number_format(App\Models\Chiller::coh('byproduct', 'booking', $tanggal_awal, $tanggal_akhir), 2) }}</td>
                        <td class="text-right">{{ number_format(App\Models\Chiller::coh('byproduct', 'free', $tanggal_awal, $tanggal_akhir), 2) }}</td>
                        <td class="text-right">{{ number_format(App\Models\Chiller::coh('byproduct', 'all', $tanggal_awal, $tanggal_akhir), 2) }}</td>
                        <td class="text-right">{{ number_format(App\Models\Chiller::coh('byproduct', 'all', $tanggal_awal, $tanggal_akhir) ? ((App\Models\Chiller::coh('byproduct', 'all', $tanggal_awal, $tanggal_akhir) / App\Models\Chiller::coh(FALSE, 'all', $tanggal_awal, $tanggal_akhir)) * 100) : 0, 2) }}%</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <th>TOTAL</th>
                        <th class="text-right">{{ number_format(App\Models\Chiller::coh(FALSE, 'booking', $tanggal_awal, $tanggal_akhir), 2) }}</th>
                        <th class="text-right">{{ number_format(App\Models\Chiller::coh(FALSE, 'free', $tanggal_awal, $tanggal_akhir), 2) }}</th>
                        <th class="text-right">{{ number_format(App\Models\Chiller::coh(FALSE, 'all', $tanggal_awal, $tanggal_akhir), 2) }}</th>
                        <th class="text-right">100,00%</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>

<div id="dashboard-loading-pageTigaBelas" style="height: 30px">
    <div class="text-center mb-2 mt-2" style="position: absolute; left: 0; right: 0;">
        <img src="{{asset('loading.gif')}}" style="width: 20px"> Loading ...
    </div>
</div>