<div class="card-body">
    <b>Susut Bahan Baku - Produksi</b>
    <div class="col-sm-4 col-lg pr-sm-1 pr-lg-0 mb-2 px-lg-1">
        <div class="card">
            <div class="card-header">{{ $regu }}</div>
            <div class="card-body p-2">
                <div class="row mb-1">
                    <div class="col pr-1">
                        <div class="border text-center">
                            <div class="small">Bahan baku</div>
                            <div class="font-weight-bold"> {{ $produksi['bb_tt_regu'] }} kg</div>
                        </div>
                    </div>
                    <div class="col pl-1">
                        <div class="border text-center">
                            <div class="small">Produksi</div>
                            <div class="font-weight-bold"> {{ $produksi['fg_tt_regu'] }} kg</div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col pr-1">
                        <div class="border text-center">
                            <div class="small">Selisih</div>
                            <div class="font-weight-bold"> {{ number_format(($produksi['bb_tt_regu'] - $produksi['fg_tt_regu']) * -1, 2) }} kg</div>
                        </div>
                    </div>
                    <div class="col pl-1">
                        <div class="border text-center">
                            <div class="small">Persentase</div>
                            @php
                                $persentase = 0;
                                if($produksi['bb_tt_regu']>0){
                                    $persentase = number_format((($produksi['bb_tt_regu'] - $produksi['fg_tt_regu'])/$produksi['bb_tt_regu']*100) * -1, 2);
                                }
                            @endphp
                            <div class="font-weight-bold">
                                @if($persentase>0)
                                <span class="green">{{$persentase}}% </span> <span class="fa fa-caret-up green"></span>
                                @elseif($persentase<0)
                                <span class="red">{{$persentase}}% </span> <span class="fa fa-caret-down red"></span>
                                @else
                                <span class="blue">{{$persentase}}% </span>
                                @endif
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-header">Produksi x Plastik</div>

    <div class="col-sm-4 col-lg pr-sm-1 pr-lg-0 mb-2 px-lg-1">
        <div class="card">
            <div class="card-header">{{ $regu }}</div>
            <div class="card-body p-2">
                <div class="row mb-1">
                    <div class="col pr-1">
                        <div class="border text-center">
                            <div class="small">Qty</div>
                            <div class="font-weight-bold"> {{ $produksi['fg_qty_regu'] }}</div>
                        </div>
                    </div>
                    <div class="col pl-1">
                        <div class="border text-center">
                            <div class="small">Plastik</div>
                            <div class="font-weight-bold"> {{ $produksi['fg_pe_regu'] }}</div>
                        </div>
                    </div>
                </div>
                {{-- <a href="{{ route('dashboard.produksiplastik', ['regu' => $kategori, 'tanggal_awal' => $tanggal_awal, 'tanggal_akhir' => $tanggal_akhir]) }}" class="rounded-0 btn btn-sm p-0 btn-outline-info btn-block small">Detail</a> --}}
            </div>
        </div>
    </div>
</div>


<div class="card-body">
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="font-weight-bold">Summary Bahan Baku {{ $regu }}</div>
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
                        @foreach ($produksi['bb_regu'] as $row)
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
                            <th colspan="3">Total Bahan Baku {{ $regu }}</th>
                            <th class="text-right">{{ number_format($ekor) }}</th>
                            <th class="text-right">{{ number_format($berat, 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <div class="col-md-6">
            <div class="font-weight-bold">Summary Produksi {{ $regu }}</div>
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
                        @foreach ($produksi['fg_regu'] as $row)
                            @php
                                $ekor += $row->total;
                                $berat += $row->kg;
                            @endphp
                            <tr>
                                <td>
                                    <a href="{{ route('dashboard.detailproduksi', ['item' => $row->item_id, 'tanggal_awal' => $tanggal_awal, 'tanggal_akhir' => $tanggal_akhir]) }}">
                                        {{ $row->item->nama }}
                                    </a>
                                </td>
                                <td class="text-right">{{ number_format($row->total) }}</td>
                                <td class="text-right">{{ number_format($row->kg, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Total Hasil Produksi {{ $regu }}</th>
                            <th class="text-right">{{ number_format($ekor) }}</th>
                            <th class="text-right">{{ number_format($berat, 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <div class="col-md-12">
            <div class="mb-1"><b>Produktivitas Regu {{ $regu }}</b></div>
            <div class="row mb-3">
            <div class="col pr-1">
                <div class="border rounded p-1 mb-2">
                    <small>Jam Awal Input</small>
                    <div class="font-weight-bold">{{ $produksi['waktu_regu']['awal'] ?? "-" }}</div>
                </div>
            </div>
            <div class="col px-1">
                <div class="border rounded p-1 mb-2">
                    <small>Jam Akhir Input</small>
                    <div class="font-weight-bold">{{ $produksi['waktu_regu']['akhir'] ?? "-" }}</div>
                </div>
            </div>
            <div class="col pl-1">
                <div class="border rounded p-1 mb-2">
                    <small>Total Jam Input</small>
                    <div class="font-weight-bold">{{ gmdate("H:i", $produksi['waktu_regu']['jam_kerja'] ?? "0")}} Jam/Menit</div>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>