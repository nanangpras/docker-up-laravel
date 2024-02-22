@php
    header('Content-Transfer-Encoding: none');
    header("Content-type: application/vnd-ms-excel");
    header("Content-type: application/x-msexcel");
    header("Content-Disposition: attachment; filename=Laporan Produksi " . ($tanggal ?? date('Y-m-d')) . " - " . ($tanggalend ?? date('Y-m-d')) . ".xls");
@endphp

<div  id="export-table">

    <table class="table default-table" width="100%">
        <style>
            .text {
                mso-number-format:"\@";
                border:thin solid black;
            }
        </style>
        <thead>
            <tr class="text-center">
                <th  class="text"  rowspan="2">NO</th>
                <th  class="text"  width="25%" rowspan="2">{!! \App\Models\Production::wordwraptext("TANGGAL POTONG","8") !!}</th>
                <th  class="text"  rowspan="2">{!! \App\Models\Production::wordwraptext("NO URUT","4") !!}</th>
                <th  class="text"  rowspan="2">NO PO</th>
                <th  class="text"  rowspan="2">UKURAN</th>
                <th  class="text"  rowspan="2">VENDOR</th>
                <th  class="text"  rowspan="2">JENIS</th>
                <th class="text"   rowspan="2">% TOLERANSI</th>
                <th  class="text"  rowspan="2">No. DO</th>
                <th  class="text"  rowspan="2">DRIVER</th>
                <th  class="text"  rowspan="2">No. MOBIL</th>
                <th  class="text"  colspan="3">DO</th>
                <th  class="text"  colspan="4">TIMBANG LPAH</th>
                <th  class="text"  rowspan="2">{!! \App\Models\Production::wordwraptext("SELISIH EKOR DO","8") !!}</th>
                <th  class="text"  rowspan="2">{!! \App\Models\Production::wordwraptext("SELISIH BERAT DO","8") !!}</th>
                <th  class="text"  colspan="2">{!! \App\Models\Production::wordwraptext("SUSUT DO-TIMBANG","10") !!}</th>
                <th  class="text"  colspan="3">MATI</th>
                <th  class="text"  rowspan="2">{!! \App\Models\Production::wordwraptext("PROSENTASE MATI","12") !!}</th>
            </tr>
            <tr class="text-center">
                <th  class="text" >Eko/Pcs/<br>Pack</th>
                <th  class="text" >Kg</th>
                <th  class="text" >Rata2 Kg</th>
                <th  class="text" >Ekor/Pcs/<br/>Pack</th>
                <th  class="text" >Kg</th>
                <th  class="text" >Rata2 Kg</th>
                <th  class="text" >Tembolok</th>
                <th  class="text" >Kg</th>
                <th  class="text" >%</th>
                <th  class="text" >Ekor/Pcs/<br/>Pack</th>
                <th  class="text" >Kg</th>
                <th  class="text" >Rata2 Kg</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $i => $val)
                @php
                    $toleransi  =   App\Models\Target::where('alamat', 'like', '%' . preg_replace('/\s+/', '', $val->sc_wilayah) . '%')->orderBy('id', 'DESC')->first()->target ?? 0 ;
                @endphp
                @php
                    $gradberat = 0;
                    $graditem = 0;
                    $evisberat = 0;
                    $evisekor = 0;
                @endphp
                @php
                    $summary = \App\Models\Grading::where('trans_id', $val->id)
                        ->where('keranjang', 0)
                        ->orderBy('id', 'DESC')
                        ->get();
                    $gradberat = 0;
                    $graditem = 0;
                    foreach ($summary as $row) {
                        $gradberat += $row->berat_item;
                        $graditem += $row->total_item;
                    }
                @endphp
                @php
                    $evis = \App\Models\Evis::where('production_id', $val->id)->get();
                    $evisberat = 0;
                    $evisekor = 0;
                    foreach ($evis as $key) {
                        $evisekor += $key->stock_item;
                        $evisberat += $key->berat_stock;
                    }
                @endphp
                <tr>
                    <td class="text">{{ ++$i }}</td>
                    <td class="text">{{ $val->prodpur->tanggal_potong }}</td>
                    <td class="text">{{ $val->no_urut }}</td>
                    <td class="text">{{ $val->no_po }}</td>
                    <td class="text">@if ($val->prodpur->ukuran_ayam == '&lt; 1.1') {{ '<1.1' }} @else {{ $val->prodpur->ukuran_ayam }} @endif</td>
                    <td class="text">{!! \App\Models\Production::wordwraptext($val->prodpur->purcsupp->nama ?? 'VENDOR TIDAK DITEMUKAN' ,"11") !!}</td>
                    <td class="text">{{ $val->prodpur->type_ekspedisi }}</td>
                    <td class="text">{{ number_format($val->lpah_persen_susut ? $toleransi : 0, 2) }}</td>
                    <td class="text">{{ $val->no_do }}</td>
                    <td class="text">{!! \App\Models\Production::wordwraptext($val->sc_pengemudi,"8") !!} </td>
                    <td class="text">{{ $val->sc_no_polisi }}</td>
                    <td class="text">{{ number_format($val->sc_ekor_do, 0) }}</td>
                    <td class="text">{{ number_format($val->sc_berat_do, 2) }}</td>
                    <td class="text">{{ number_format($val->sc_rerata_do, 2) }}</td>
                    <td class="text">{{ number_format($val->ekoran_seckle, 0) }}</td>
                    <td class="text">{{ number_format($val->lpah_berat_terima, 2) }}</td>
                    <td class="text">
                        @if ($val->ekoran_seckle != 0)
                        {{ number_format($val->lpah_berat_terima / $val->ekoran_seckle, 2) ?? '###' }}
                        @endif
                    </td>
                    <td class="text">{{ ($val->qc_tembolok) }}</td>
                    <td class="text">{{ ($val->sc_ekor_do - $val->ekoran_seckle) }}</td>
                    <td class="text">{{ ($val->sc_berat_do - $val->lpah_berat_terima) }}</td>
                    <td class="text">{{ number_format($val->lpah_berat_susut, 2) }}</td>
                    <td class="text">{{ $val->lpah_persen_susut }} %</td>
                    <td class="text">{{ $val->qc_ekor_ayam_mati }}</td>
                    <td class="text">{{ $val->qc_berat_ayam_mati }}</td>
                    <td class="text">@if($val->qc_ekor_ayam_mati>0){{ number_format($val->qc_berat_ayam_mati/$val->qc_ekor_ayam_mati, 2) }} @else 0 @endif</td>
                    <td class="text">{{ number_format($val->qc_ekor_ayam_mati != 0 ? ($val->qc_ekor_ayam_mati / $val->sc_ekor_do) * 100 : 0, 2) }}
                                        %
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="9" class="text text-center"><b>Total</b></td>
            </tr>
        </tfoot>
    </table>

    <table class="table default-table" width="100%">
        <style>
            .text {
                mso-number-format:"\@";
                border:thin solid black;
            }
            /* .wraptext-50{
                word-wrap: break-word;
                width: 5%;
            }
            .wraptext-100{
                word-wrap: break-word;
                width: 10%;
            }
            .wraptext-150{
                word-wrap: break-word;
                width: 15%;
            } */
        </style>
        <thead>
            <tr class="text-center">
                <th  class="text"  rowspan="2">NO</th>
                <th  class="text"  width="25%" rowspan="2">{!! \App\Models\Production::wordwraptext("TANGGAL POTONG","8") !!}</th>
                <th  class="text"  rowspan="2">{!! \App\Models\Production::wordwraptext("NO URUT","4") !!}</th>
                <th  class="text"  rowspan="2">NO PO</th>
                <th  class="text"  colspan="3">AYAM MERAH</th>
                <th  class="text"  rowspan="2">{!! \App\Models\Production::wordwraptext("PROSENTASE AYAM MERAH (%)","12") !!}</th>
                <th  class="text"  rowspan="2">KONDISI AYAM</th>
                <th  class="text"  colspan="3">GRADING</th>
                <th  class="text"  rowspan="2">{!! \App\Models\Production::wordwraptext("YIELD PRODUKSI","8") !!}</th>
                <th  class="text"  rowspan="2">RENDEMEN</th>
                <th  class="text"  colspan="3">EVIS</th>
                <th  class="text"  rowspan="2">YEILD EVIS</th>
                <th  class="text"  rowspan="2">NOTE</th>
            </tr>
            <tr class="text-center">
                
                <th  class="text" >{!! \App\Models\Production::wordwraptext("Ekor/Pcs/Pack","8") !!}</th>
                <th  class="text" >Kg</th>
                <th  class="text" >Rata2 Kg</th>
                <th  class="text" >{!! \App\Models\Production::wordwraptext("Ekor/Pcs/Pack","8") !!}</th>
                <th  class="text" >Kg</th>
                <th  class="text" >Rata2</th>
                <th  class="text" >{!! \App\Models\Production::wordwraptext("Ekor/Pcs/Pack","8") !!}</th>
                <th  class="text" >Kg</th>
                <th  class="text" >%</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $i => $val)
                @php
                    $gradberat = 0;
                    $graditem = 0;
                    $evisberat = 0;
                    $evisekor = 0;
                @endphp
                @php
                    $summary = \App\Models\Grading::where('trans_id', $val->id)
                        ->where('keranjang', 0)
                        ->orderBy('id', 'DESC')
                        ->get();
                    $gradberat = 0;
                    $graditem = 0;
                    foreach ($summary as $row) {
                        $gradberat += $row->berat_item;
                        $graditem += $row->total_item;
                    }
                @endphp
                @php
                    $evis = \App\Models\Evis::where('production_id', $val->id)->get();
                    $evisberat = 0;
                    $evisekor = 0;
                    foreach ($evis as $key) {
                        $evisekor += $key->stock_item;
                        $evisberat += $key->berat_stock;
                    }
                @endphp
                <tr>
                    <td class="text">{{ ++$i }}</td>
                    <td class="text">{{ $val->prodpur->tanggal_potong }}</td>
                    <td class="text">{{ $val->no_urut }}</td>
                    <td class="text">{{ $val->no_po }}</td>
                    <td class="text">{{ $val->qc_ekor_ayam_merah ?? '0' }}</td>
                    <td class="text">{{ $val->qc_berat_ayam_merah ?? '0' }}</td>
                    <td class="text">{{ $val->qc_hitung_ayam_merah ?? '0' }}</td>
                    <td class="text">{{ $val->qc_persen_ayam_merah ?? '0' }} %</td>
                    <td class="text">{{ $val->kondisi_ayam }}</td>
                    <td class="text">{{ number_format($graditem, 0) }}</td>
                    <td class="text">{{ number_format($gradberat, 2) }}</td>
                    <td class="text">{{ number_format($graditem != 0 ? $gradberat / $graditem : '0', 2) }}</td>
                    <td class="text">
                        @php
                            if ($val->lpah_berat_terima != 0) {
                                $yield_produksi = $val->prod_yield_produksi;
                            } else {
                                $yield_produksi = 0;
                            }
                        @endphp
                        {{ number_format($yield_produksi, 2) }} %
                    </td>
                    <td class="text">
                        @if ($val->prodpur->type_ekspedisi == 'tangkap')
                            {{ number_format($val->sc_berat_do != 0 ? ($gradberat / $val->sc_berat_do) * 100 : '0', 2) }}
                        @else
                            {{ number_format($val->lpah_berat_terima != 0 ? ($gradberat / $val->lpah_berat_terima) * 100 : '0', 2) }}
                        @endif

                        %
                    </td>
                    <td class="text">{{ number_format($evisekor, 0) }}</td>
                    <td class="text">{{ number_format($evisberat, 2) }}</td>
                    <td class="text">{{ number_format($val->lpah_berat_terima != 0 ? ($evisberat / $val->lpah_berat_terima) * 100 : '0', 2) }}
                    </td>
                    <td class="text">
                        @php
                            if ($val->lpah_berat_terima != 0) {
                                $yield_evis = ($evisberat / $val->lpah_berat_terima) * 100;
                            } else {
                                $yield_evis = 0;
                            }
                        @endphp
                        {{ number_format($yield_evis, 2) }} %
                    </td>
                    <td class="text"></td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="9" class="text text-center"><b>Total</b></td>
            </tr>
        </tfoot>
    </table>

</div>