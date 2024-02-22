<section class="panel">
    <div class="card-header">Bahan Baku Evis <span class="float-right">(Kg)</span></div>
    <div class="card-body p-1">
        <table class="table table-sm table-hover">
            <tbody>
                @foreach ($produksi as $row)
                    <tr>
                        <td>{{ $row->eviitem->nama ?? '' }}</td>
                        <td class="text-right">{{ number_format($row->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" class="text-right"><a class="btn btn-sm btn-success" href="{{ route('evis.laporan', ['key' => 'laporan_produksi']) }}&mulai={{ $mulai }}&selesai={{ $selesai }}">Unduh Data</a></td>
                </tr>
            </tfoot>
        </table>
    </div>
</section>

<section class="panel">
    <div class="card-body p-2">
        <div class="table-responsive" id="table-bb-fresh">
            <table class="table default-table">
                <style>
                    .text {
                        mso-number-format:"\@";
                        border:thin solid black;
                    }
                </style>
                <style>
                    th,
                    td {
                        border: 1px solid #ddd;
                    }
                </style>
                <thead>
                    <tr>
                        <th class="text-center text" rowspan="2">Tanggal</th>
                        <th class="text-center text" colspan="3">Live Birds</th>
                        <th class="table-light-gray text-center text" colspan="2">Total NY By Product</th>
                        <th class="text-center text" colspan="2">Kepala Leher</th>
                        <th class="table-light-gray text-center text" colspan="2">Ceker</th>
                        <th class="text-center text" colspan="2">Usus</th>
                        <th class="table-light-gray text-center text" colspan="2">Ati Hancur</th>
                        <th class="text-center text" colspan="2">Tembolok</th>
                        <th class="table-light-gray text-center text" colspan="2">Hati Ampela Utuh</th>
                        <th class="text-center text" colspan="2">Jantung</th>
                    </tr>
                    <tr>
                        <th class="text-center text">Ekor/Pcs/Pack</th>
                        <th class="text-center text">Kg</th>
                        <th class="text-center text">Yield%</th>
                        <th class="table-light-gray text-center text">Berat(Kg)</th>
                        <th class="table-light-gray text-center text">%</th>
                        <th class="text-center text">Berat(Kg)</th>
                        <th class="text-center text">%</th>
                        <th class="table-light-gray text-center text">Berat(Kg)</th>
                        <th class="table-light-gray text-center text">%</th>
                        <th class="text-center text">Berat(Kg)</th>
                        <th class="text-center text">%</th>
                        <th class="table-light-gray text-center text">Berat(Kg)</th>
                        <th class="table-light-gray text-center text">%</th>
                        <th class="text-center text">Berat(Kg)</th>
                        <th class="text-center text">%</th>
                        <th class="table-light-gray text-center text">Berat(Kg)</th>
                        <th class="table-light-gray text-center text">%</th>
                        <th class="text-center text">Berat(Kg)</th>
                        <th class="text-center text">%</th>
                    </tr>
                    <tr>
                        <th class="text-center text" colspan="6">Bencmark</th>
                        <th class="text-center text" colspan="2">5%</th>
                        <th class="text-center text" colspan="2">3%</th>
                        <th class="text-center text" colspan="2">5%</th>
                        <th class="text-center text" colspan="2">0,10%</th>
                        <th class="text-center text" colspan="2">0,80%</th>
                        <th class="text-center text" colspan="2">6%</th>
                        <th class="text-center text" colspan="2">0,10%</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $tt_ekor_lb         =   0 ;

                        $tt_berat_lb        =   0 ;

                        $tt_yield           =   0 ;

                        $tt_berat_ny        =   0 ;
                        $prstt_ny           =   0 ;
                        $count_ny           =   0 ;

                        $tt_1211840002      =   0 ;
                        $prstt_1211840002   =   0 ;
                        $count_1211840002   =   0 ;

                        $tt_1211830002      =   0 ;
                        $prstt_1211830002   =   0 ;
                        $count_1211830002   =   0 ;

                        $tt_1211820005      =   0 ;
                        $prstt_1211820005   =   0 ;
                        $count_1211820005   =   0 ;

                        $tt_1211810007      =   0 ;
                        $prstt_1211810007   =   0 ;
                        $count_1211810007   =   0 ;

                        $tt_1211820004      =   0 ;
                        $prstt_1211820004   =   0 ;
                        $count_1211820004   =   0 ;

                        $tt_1211810006      =   0 ;
                        $prstt_1211810006   =   0 ;
                        $count_1211810006   =   0 ;

                        $tt_1211820002      =   0 ;
                        $prstt_1211820002   =   0 ;
                        $count_1211820002   =   0 ;

                    @endphp
                    @foreach ($period as $date)
                    @php
                        $ekor_lb        =   App\Models\Production::hitung_lb($date->format('Y-m-d'), 'ekoran_seckle') ;
                        $tt_ekor_lb     +=  $ekor_lb ;

                        $berat_lb       =   App\Models\Production::hitung_lb($date->format('Y-m-d'), 'lpah_berat_terima') ;
                        $tt_berat_lb    +=  $berat_lb ;

                        $yield       =   App\Models\Production::yieldProduksiHarian($date->format('Y-m-d')) ;
                        $tt_yield    +=  $yield ;

                        $berat_ny       =   App\Models\Evis::hitung_kotor($date->format('Y-m-d')) ; ;
                        $persen_ny      =   $berat_ny && $berat_lb ? (($berat_ny / $berat_lb) * 100) : 0 ;
                        $tt_berat_ny    +=  $berat_ny ;
                        $prstt_ny       +=  $persen_ny ;
                        $count_ny       +=  $persen_ny ? 1 : 0 ;

                        $it_1211840002      =   App\Models\Evis::produksi($date->format('Y-m-d'), 1211840002) ;
                        $persen_1211840002  =   $it_1211840002 && $berat_lb ? (($it_1211840002 / $berat_lb) * 100) : 0 ;
                        $tt_1211840002      +=  $it_1211840002 ;
                        $prstt_1211840002   +=  $persen_1211840002 ;
                        $count_1211840002   +=  $persen_1211840002 ? 1 : 0 ;

                        $it_1211830002      =   App\Models\Evis::produksi($date->format('Y-m-d'), 1211830002) ;
                        $persen_1211830002  =   $it_1211830002 && $berat_lb ? (($it_1211830002 / $berat_lb) * 100) : 0 ;
                        $tt_1211830002      +=  $it_1211830002 ;
                        $prstt_1211830002   +=  $persen_1211830002 ;
                        $count_1211830002   +=  $persen_1211830002 ? 1 : 0 ;

                        $it_1211820005      =   App\Models\Evis::produksi($date->format('Y-m-d'), 1211820005) ;
                        $persen_1211820005  =   $it_1211820005 && $berat_lb ? (($it_1211820005 / $berat_lb) * 100) : 0 ;
                        $tt_1211820005      +=  $it_1211820005 ;
                        $prstt_1211820005   +=  $persen_1211820005 ;
                        $count_1211820005   +=  $persen_1211820005 ? 1 : 0 ;

                        $it_1211810007      =   App\Models\Evis::produksi($date->format('Y-m-d'), 1211810007) ;
                        $persen_1211810007  =   $it_1211810007 && $berat_lb ? (($it_1211810007 / $berat_lb) * 100) : 0 ;
                        $tt_1211810007      +=  $it_1211810007 ;
                        $prstt_1211810007   +=  $persen_1211810007 ;
                        $count_1211810007   +=  $persen_1211810007 ? 1 : 0 ;

                        $it_1211820004      =   App\Models\Evis::produksi($date->format('Y-m-d'), 1211820004) ;
                        $persen_1211820004  =   $it_1211820004 && $berat_lb ? (($it_1211820004 / $berat_lb) * 100) : 0 ;
                        $tt_1211820004      +=  $it_1211820004 ;
                        $prstt_1211820004   +=  $persen_1211820004 ;
                        $count_1211820004   +=  $persen_1211820004 ? 1 : 0 ;

                        $it_1211810006      =   App\Models\Evis::produksi($date->format('Y-m-d'), 1211810006) ;
                        $persen_1211810006  =   $it_1211810006 && $berat_lb ? (($it_1211810006 / $berat_lb) * 100) : 0 ;
                        $tt_1211810006      +=  $it_1211810006 ;
                        $prstt_1211810006   +=  $persen_1211810006 ;
                        $count_1211810006   +=  $persen_1211810006 ? 1 : 0 ;

                        $it_1211820002      =   App\Models\Evis::produksi($date->format('Y-m-d'), 1211820002) ;
                        $persen_1211820002  =   $it_1211820002 && $berat_lb ? (($it_1211820002 / $berat_lb) * 100) : 0 ;
                        $tt_1211820002      +=  $it_1211820002 ;
                        $prstt_1211820002   +=  $persen_1211820002 ;
                        $count_1211820002   +=  $persen_1211820002 ? 1 : 0 ;
                    @endphp
                    <tr>
                        <td>{{ $date->format('d/m/y') }}</td>
                        <td class="text-right text">{{ number_format($ekor_lb) }}</td>
                        <td class="text-right text">{{ number_format($berat_lb, 2,',', '.') }}</td>
                        <td class="text-right text">{{ number_format($yield, 2,',', '.') }}%</td>
                        <td class="table-light-gray text-right text">{{ number_format($berat_ny, 2,',', '.') }}</td>
                        <td class="table-light-gray text-right text">{{ number_format($persen_ny, 2,',', '.') }}%</td>

                        <td class="text-right text">{{ number_format($it_1211840002, 2,',', '.') }}</td>
                        <td class="text-right text">{{ number_format($persen_1211840002, 2,',', '.') }}%</td>

                        <td class="table-light-gray text-right text">{{ number_format($it_1211830002, 2,',', '.') }}</td>
                        <td class="table-light-gray text-right text">{{ number_format($persen_1211830002, 2,',', '.') }}%</td>

                        <td class="text-right text">{{ number_format($it_1211820005, 2,',', '.') }}</td>
                        <td class="text-right text">{{ number_format($persen_1211820005, 2,',', '.') }}%</td>

                        <td class="table-light-gray text-right text">{{ number_format($it_1211810007, 2,',', '.') }}</td>
                        <td class="table-light-gray text-right text">{{ number_format($persen_1211810007, 2,',', '.') }}%</td>

                        <td class="text-right text">{{ number_format($it_1211820004, 2,',', '.') }}</td>
                        <td class="text-right text">{{ number_format($persen_1211820004, 2,',', '.') }}%</td>

                        <td class="table-light-gray text-right text">{{ number_format($it_1211810006, 2,',', '.') }}</td>
                        <td class="table-light-gray text-right text">{{ number_format($persen_1211810006, 2,',', '.') }}%</td>

                        <td class="text-right text">{{ number_format($it_1211820002, 2,',', '.') }}</td>
                        <td class="text-right text">{{ number_format($persen_1211820002, 2,',', '.') }}%</td>
                    </tr>
                    @endforeach
                </tbody>

                <tfoot>
                    <tr>
                        <th>TOTAL</th>
                        <th class="text-right text">{{ number_format($tt_ekor_lb) }}</th>
                        <th class="text-right text">{{ number_format($tt_berat_lb, 2,',', '.') }}</th>
                        <th class="text-right text">@if($tt_yield !== 0 && $count_ny !== 0){{ number_format($tt_yield/$count_ny, 2,',', '.') }}% @endif</th>
                        <th class="text-right text">{{ number_format($tt_berat_ny, 2,',', '.') }}</th>
                        <th class="text-right text">@if($prstt_ny !== 0 && $count_ny !== 0){{ number_format(($prstt_ny / $count_ny), 2,',', '.') }}% @endif</th>
                        <th class="text-right text">{{ number_format($tt_1211840002, 2,',', '.') }}</th>
                        <th class="text-right text">@if($prstt_1211840002 !== 0 && $count_1211840002 !== 0){{ number_format(($prstt_1211840002 / $count_1211840002), 2,',', '.') }}% @endif</th>
                        <th class="text-right text">{{ number_format($tt_1211830002, 2,',', '.') }}</th>
                        <th class="text-right text">@if($prstt_1211830002 > 0 && $count_1211830002 > 0){{ number_format(($prstt_1211830002 / $count_1211830002), 2,',', '.') }}%@endif</th>
                        <th class="text-right text">{{ number_format($tt_1211820005, 2,',', '.') }}</th>
                        <th class="text-right text">@if($prstt_1211820005 > 0 && $count_1211820005 > 0){{ number_format(($prstt_1211820005 / $count_1211820005), 2,',', '.') }}%@endif</th>
                        <th class="text-right text">{{ number_format($tt_1211810007, 2,',', '.') }}</th>
                        <th class="text-right text">@if($prstt_1211810007 > 0 && $count_1211810007 >0){{ number_format(($prstt_1211810007 / $count_1211810007), 2,',', '.') }}%@endif</th>
                        <th class="text-right text">{{ number_format($tt_1211820004, 2,',', '.') }}</th>
                        <th class="text-right text">@if($prstt_1211820004 > 0 && $count_1211820004 > 0){{ number_format(($prstt_1211820004 / $count_1211820004), 2,',', '.') }}%@endif</th>
                        <th class="text-right text">{{ number_format($tt_1211810006, 2,',', '.') }}</th>
                        <th class="text-right text">@if($prstt_1211810006 > 0 && $count_1211810006 > 0){{ number_format(($prstt_1211810006 / $count_1211810006), 2,',', '.') }}% @endif</th>
                        <th class="text-right text">{{ number_format($tt_1211820002, 2,',', '.') }}</th>
                        <th class="text-right text">@if($prstt_1211820002 > 0 && $count_1211820002 > 0) {{ number_format(($prstt_1211820002 / $count_1211820002), 2,',', '.') }}%@endif</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</section>
<form method="post" action="{{route('weekly.export')}}">
    @csrf
    <input name="filename" type="hidden" value="laporan-umum-evis.xls">
    <textarea name="html" style="display: none" id="html-bb-fresh"></textarea>
    <button type="submit" id="export-bb-fresh" class="btn btn-blue">Export</button>
</form>

<script>
    $(document).ready(function(){
        var html  = $('#table-bb-fresh').html();
        $('#html-bb-fresh').val(html);
    })
</script>