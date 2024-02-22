@php
    header('Content-Transfer-Encoding: none');
    header("Content-type: application/vnd-ms-excel");
    header("Content-type: application/x-msexcel");
    header("Content-Disposition: attachment; filename=Rendemen " . ($tanggal ?? date('Y-m-d')) . " - " . ($tanggalend ?? date('Y-m-d')) . ".xls");
@endphp
<style>
    .tengah{
        vertical-align: middle; 
        text-align: center;
    }
    .text-right{
        vertical-align: middle; 
        text-align: right;
    }
</style>

    <table border="1">
        
        <thead>
            <tr class="text-center">
                <th  class="text"  rowspan="2">NO</th>
                <th  class="text"  rowspan="2">TANGGAL POTONG</th>
                <th  class="text"  rowspan="2">JENIS</th>
                <th  class="text"  rowspan="2">UKURAN</th>
                <th  class="text"  rowspan="2">SUPPLIER</th>
                <th  class="text"  rowspan="2">FARM</th>
                <th  class="text"  rowspan="2">WILAYAH</th>
                <th  class="text"  rowspan="2">No. DO</th>
                <th  class="text"  rowspan="2">DRIVER</th>
                <th  class="text"  rowspan="2">No. MOBIL</th>
                <th  class="text"  rowspan="2">OPERATOR</th>
                <th  class="text"  colspan="3">DO</th>
                <th  class="text"  colspan="4">TIMBANG LPAH</th>
                <th  class="text"  rowspan="2">SELISIH EKOR DO</th>
            </tr>
            <tr class="text-center">
                
                <th  class="text" >Ekor/Pcs/Pack</th>
                <th  class="text" >Kg</th>
                <th  class="text" >Rata2 Kg</th>
                <th  class="text" >Ekor/Pcs/Pack</th>
                <th  class="text" >Kg</th>
                <th  class="text" >Rata2 Kg</th>
                <th  class="text" >Tembolok</th>
                
            </tr>
        </thead>
        <tbody>
            @php
                $total_DO               = 0;
                $total_BeratDO          = 0;
                $total_RerataDO         = 0;
                $total_EkorSeckle       = 0;

                $total_BeratLPAH        = 0;
                $total_RerataLPAH       = 0;
                $total_Tembolok         = 0;
                
                $total_SelisihDO        = 0;

            @endphp
            @foreach ($newData as $i => $val)
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
                    <td class="text" >{{ ++$i }}</td>
                    <td class="text" >{{ $val->prodpur->tanggal_potong }}</td>
                    <td class="text">{{ $val->prodpur->type_ekspedisi }}</td>
                    <td class="text">@if ($val->prodpur->ukuran_ayam == '&lt; 1.1') {{ '<1.1' }} @else {{ $val->prodpur->ukuran_ayam }} @endif</td>
                    <td class="text">{{ ($val->prodpur->purcsupp->nama ?? 'VENDOR TIDAK DITEMUKAN') }}</td>
                    <td class="text">{{ $val->sc_nama_kandang }}</td>
                    <td class="text">{{ $val->sc_alamat_kandang }}</td>
                    <td class="text">{{ $val->no_do }}</td>
                    <td class="text">{{ $val->sc_pengemudi }}</td>
                    <td class="text">{{ $val->sc_no_polisi }}</td>
                    <td class="text">{{ $val->lpah_user_nama }}</td>
                    <td class="text">{{ number_format($val->sc_ekor_do, 0) }}</td>
                    <td class="text">{{ number_format($val->sc_berat_do, 1) }}</td>
                    <td class="text">{{ number_format($val->sc_rerata_do, 1) }}</td>
                    <td class="text">{{ number_format($val->ekoran_seckle, 0) }}</td>
                    <td class="text">{{ number_format($val->lpah_berat_terima, 1) }}</td>
                    <td class="text">
                        @if ($val->ekoran_seckle != 0)
                        {{ number_format($val->lpah_berat_terima / $val->ekoran_seckle, 1) }}
                        @else
                        0
                        @endif
                    </td>
                    <td class="text">{{ ($val->qc_tembolok) }}</td>
                    <td class="text">{{ ($val->sc_ekor_do - $val->ekoran_seckle) }}</td>
                    
                </tr>
                @php
                    if ($val->ekoran_seckle != 0){
                        $RataRataLPAH       = number_format($val->lpah_berat_terima / $val->ekoran_seckle, 1);
                    }else{
                        $RataRataLPAH       = number_format(0,1);
                    }

                    $total_DO               += $val->sc_ekor_do;
                    $total_BeratDO          += $val->sc_berat_do;
                    $total_RerataDO         += $val->sc_rerata_do;
                    $total_EkorSeckle       += $val->ekoran_seckle;

                    $total_BeratLPAH        += $val->lpah_berat_terima;
                    $total_RerataLPAH       += $RataRataLPAH;
                    $total_Tembolok         += $val->qc_tembolok;

                    $total_SelisihDO        += ($val->sc_ekor_do - $val->ekoran_seckle);
                @endphp
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="11" class="text text-center tengah"><b>Total</b></td>
                <td class="text text-center tengah"><b>{{ number_format($total_DO,0) }}</b></td>
                <td class="text text-center tengah"><b>{{ number_format($total_BeratDO,1) }}</b></td>
                <td class="text text-center tengah"><b>{{ number_format(($total_RerataDO / $NewArray['jumlahdata']),1) }}</b></td>
                <td class="text text-center tengah"><b>{{ number_format($total_EkorSeckle,1) }}</b></td>
                <td class="text text-center tengah"><b>{{ number_format($total_BeratLPAH,1) }}</b></td>
                <td class="text text-center tengah"><b>{{ number_format(($total_RerataLPAH / $NewArray['jumlahtimbanglpah']),1) }}</b></td>
                <td class="text text-center tengah"><b>{{ number_format($total_Tembolok,1) }}</b></td>
                <td class="text text-center tengah"><b>{{ number_format($total_SelisihDO,1) }}</b></td>
            </tr>
        </tfoot>
    </table>
    <br>
    <table border="1">
        
        <thead>
            <tr class="text-center">
                <th  class="text"  rowspan="2">NO</th>
                <th  class="text"  colspan="2">SUSUT DO-TIMBANG</th>
                <th  class="text"  colspan="3">MATI</th>
                <th  class="text"  rowspan="2">PROSENTASE MATI (%)</th>
                <th  class="text"  colspan="2">MERAH</th>
                <th  class="text"  rowspan="2">KONDISI AYAM</th>
                <th  class="text"  colspan="3">GRADING</th>
                <th  class="text"  rowspan="2">YEILD PRODUKSI</th>
                <th  class="text"  rowspan="2">RENDEMEN</th>
                <th  class="text"  colspan="3">EVIS</th>
                <th  class="text"  rowspan="2">YEILD EVIS</th>
                <th  class="text"  rowspan="2">ALASAN BENCHMARK</th>
                <th  class="text"  rowspan="2">YIELD BENCHMARK KARKAS</th>
                <th  class="text"  rowspan="2">YIELD BENCHMARK EVIS</th>
            </tr>
            <tr class="text-center">
                
                
                <th  class="text" >Kg</th>
                <th  class="text" >%</th>
                <th  class="text" >Ekor/Pcs/Pack</th>
                <th  class="text" >Kg</th>
                <th  class="text" >Rata2 Kg</th>
                <th  class="text" >Ekor/Pcs/Pack</th>
                <th  class="text" >Kg</th>
                <th  class="text" >Ekor/Pcs/Pack</th>
                <th  class="text" >Kg</th>
                <th  class="text" >Rata2</th>
                <th  class="text" >Ekor/Pcs/Pack</th>
                <th  class="text" >Kg</th>
                <th  class="text" >%</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $total_SusutDO          = 0;
                $total_PersenDO         = 0;

                $total_EkorMati         = 0;
                $total_BeratMati        = 0;
                $total_RerataMati       = 0;
                $total_PersenMati       = 0;

                $total_EkorMerah        = 0;
                $total_BeratMerah       = 0;

                $total_EkorGrading      = 0;
                $total_BeratGrading     = 0;
                $total_RerataGrading    = 0;
                $total_PersenProduksi   = 0;
                
                $total_EkorEvis         = 0;
                $total_BeratEvis        = 0;
                $total_RerataEvis       = 0;
                $total_PersenEvis       = 0;
                $total_YieldEvis        = 0;

                $total_RendemenProduksi = 0;
            @endphp
            @foreach ($newData as $i => $val)
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
                    $jumlahGradeItem  = $summary;
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
                    <td class="text text-right" >{{ ++$i }}</td>
                    
                    <td class="text text-right">{{ number_format($val->lpah_berat_susut, 1) }}</td>
                    <td class="text text-right">{{ $val->lpah_persen_susut }} %</td>
                    <td class="text text-right">{{ $val->qc_ekor_ayam_mati }}</td>
                    <td class="text text-right">{{ $val->qc_berat_ayam_mati }}</td>
                    <td class="text text-right">@if($val->qc_ekor_ayam_mati>0){{ number_format($val->qc_berat_ayam_mati/$val->qc_ekor_ayam_mati, 1) }} @else 0 @endif</td>
                    <td class="text text-right">{{ number_format($val->qc_ekor_ayam_mati != 0 ? ($val->qc_ekor_ayam_mati / $val->sc_ekor_do) * 100 : 0, 2) }}
                        %
                    </td>
                    <td class="text text-right">{{ $val->qc_ekor_ayam_merah }}</td>
                    <td class="text text-right">{{ $val->qc_berat_ayam_merah }}</td>
                    <td class="text text-right">{{ $val->kondisi_ayam }}</td>
                    <td class="text text-right">{{ number_format($graditem, 0) }}</td>
                    <td class="text text-right">{{ number_format($gradberat, 1) }}</td>
                    <td class="text text-right">{{ number_format($graditem != 0 ? $gradberat / $graditem : '0', 1) }}</td>
                    <td class="text text-right">
                        @php
                            if ($val->lpah_berat_terima != 0) {
                                $yield_produksi = $val->prod_yield_produksi;
                            } else {
                                $yield_produksi = 0;
                            }
                        @endphp
                        {{ number_format($yield_produksi, 2) }} %
                    </td>
                    <td class="text text-right">
                        @if ($val->prodpur->type_ekspedisi == 'tangkap')
                            {{ number_format($val->sc_berat_do != 0 ? ($gradberat / $val->sc_berat_do) * 100 : '0', 2) }}
                        @else
                            {{ number_format($val->lpah_berat_terima != 0 ? ($gradberat / $val->lpah_berat_terima) * 100 : '0', 2) }}
                        @endif

                        %
                    </td>
                    <td class="text text-right">{{ number_format($evisekor, 0) }}</td>
                    <td class="text text-right">{{ number_format($evisberat, 1) }}</td>
                    <td class="text text-right">{{ number_format($val->lpah_berat_terima != 0 ? ($evisberat / $val->lpah_berat_terima) * 100 : '0', 2) }}
                    </td>
                    <td class="text text-right">
                        @php
                            if ($val->lpah_berat_terima != 0) {
                                $yield_evis = ($evisberat / $val->lpah_berat_terima) * 100;
                            } else {
                                $yield_evis = 0;
                            }
                        @endphp
                        {{ number_format($yield_evis, 2) }} %
                    </td>

                    @php
                    $getDataYield = App\Models\Adminedit::where('activity', 'input_yield')->where('content', $val->prodpur->purchasing_item[0]->jenis_ayam )->where('type', $val->prodpur->ukuran_ayam)->first();
                    if ($getDataYield) {
                        $decodeYield = json_decode($getDataYield->data);
                    }
                    @endphp
                    <td> {{ $val->keterangan_benchmark ?? '-' }}</td>
                    <td> {{ $decodeYield->yield_karkas ?? '-' }}</td>
                    <td> {{ $decodeYield->yield_evis ?? '-' }}</td>  
                </tr>
                @php
                    if($val->qc_ekor_ayam_mati > 0){ 
                        $RataRataMati       = number_format($val->qc_berat_ayam_mati / $val->qc_ekor_ayam_mati, 1);
                    }else{
                        $RataRataMati       = number_format(0,1);
                    }
                    if($val->qc_ekor_ayam_mati != 0) {
                        $RataRataPersenMati = number_format(($val->qc_ekor_ayam_mati / $val->sc_ekor_do) * 100 , 1);
                    }else{
                        $RataRataPersenMati = number_format(0,1);
                    }
                    if($graditem != 0){
                        $RataRataGrading    = number_format(($gradberat / $graditem),1);
                    }else{
                        $RataRataGrading    = number_format(0,1);
                    }
                    if($val->lpah_berat_terima != 0){
                        $RataRataProduksi   = number_format($val->prod_yield_produksi,1);
                    }else{
                        $RataRataProduksi   = number_format(0,1);
                    }
                    if ($val->prodpur->type_ekspedisi == 'tangkap'){
                        if($val->sc_berat_do != 0){
                            $RataRataRendemen = number_format(($gradberat / $val->sc_berat_do) * 100, 1);
                        }else{
                            $RataRataRendemen = number_format(0,1);
                        }
                    }else {
                        if($val->lpah_berat_terima != 0){
                            $RataRataRendemen = number_format(($gradberat / $val->lpah_berat_terima) * 100, 1);
                        }else{
                            $RataRataRendemen = number_format(0,1);
                        }
                    }

                    if($val->lpah_berat_terima != 0){
                        $PersenEvis           = number_format(($evisberat / $val->lpah_berat_terima) * 100,1);
                    }else{
                        $PersenEvis           = number_format(0,1);
                    }

                    $total_SusutDO          += $val->lpah_berat_susut;
                    $total_PersenDO         += $val->lpah_persen_susut;

                    $total_EkorMati         += $val->qc_ekor_ayam_mati;
                    $total_BeratMati        += $val->qc_berat_ayam_mati;
                    $total_RerataMati       += $RataRataMati;
                    
                    $total_PersenMati       += $RataRataPersenMati;

                    $total_EkorMerah        += $val->qc_ekor_ayam_merah;
                    $total_BeratMerah       += $val->qc_berat_ayam_merah;

                    $total_EkorGrading      += $graditem;
                    $total_BeratGrading     += $gradberat;
                    $total_RerataGrading    += $RataRataGrading;
                    $total_PersenProduksi   += $RataRataProduksi;
                    
                    $total_EkorEvis         += $evisekor;
                    $total_BeratEvis        += $evisberat;
                    $total_PersenEvis       += $PersenEvis;
                    $total_YieldEvis        += $PersenEvis;
                    
                    $total_RendemenProduksi += $RataRataRendemen;

                @endphp
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td class="text text-right"><b></b></td>
                <td class="text text-right"><b>{{ number_format($total_SusutDO,1) }}</b></td>
                <td class="text text-right"><b>{{ number_format(($total_PersenDO / $NewArray['jumlahDataSusut']),2) }} %</b></td>
                <td class="text text-right"><b>{{ number_format($total_EkorMati,0) }}</b></td>
                <td class="text text-right"><b>{{ number_format($total_BeratMati,1) }} Kg</b></td>
                <td class="text text-right"><b>{{ number_format(($total_RerataMati / $NewArray['jumlahDataMati']),1) }} kg</b></td>
                <td class="text text-right"><b>{{ number_format(($total_PersenMati / $NewArray['jumlahDataMati']),2) }} %</b></td>
                <td class="text text-right"><b>{{ number_format($total_EkorMerah,1) }}</b></td>
                <td class="text text-right"><b>{{ number_format($total_BeratMerah,1) }}</b></td>
                <td class="text text-right"></td>
                <td class="text text-right"><b>{{ number_format($total_EkorGrading,1) }}</b></td>
                <td class="text text-right"><b>{{ number_format($total_BeratGrading,1) }}</b></td>
                <td class="text text-right"><b>{{ number_format(($total_RerataGrading / $NewArray['jumlahDataGrading']),1) }}</b></td>
                <td class="text text-right"><b>{{ number_format(($total_PersenProduksi / $NewArray['jumlahDataGrading']),2) }} %</b></td>
                <td class="text text-right"><b>{{ number_format(($total_RendemenProduksi / $NewArray['jumlahDataGrading']),2) }} %</b></td>
                <td class="text text-right"><b>{{ number_format($total_EkorEvis,1) }}</b></td>
                <td class="text text-right"><b>{{ number_format($total_BeratEvis,1) }}</b></td>
                <td class="text text-right"><b>{{ number_format(($total_PersenEvis / $NewArray['jumlahdataevis']),2) }} %</b></td>
                <td class="text text-right"><b>{{ number_format(($total_YieldEvis / $NewArray['jumlahdataevis']),2) }} %</b></td>
            </tr>
        </tfoot>
    </table>
    <br>
    <table border="1">
        <thead>
            <tr>
                <th rowspan="2">NO</th>
                @foreach ($item as $it)
                    <th colspan="2">{{$it->nama}}</th>
                @endforeach
                {{-- <th colspan="2">HATI AMPELA KOTOR</th>
                <th colspan="2">KAKI KOTOR</th>
                <th colspan="2">KEPALA LEHER</th>
                <th colspan="2">USUS</th>
                <th colspan="2">JANTUNG</th>
                <th colspan="2">HATI HANCUR</th>
                <th colspan="2">TEMBOLOK</th>
                <th colspan="2">BULU & DARAH</th> --}}
            </tr>
            <tr>
                <th>Kg</th>
                <th>%</th>
                <th>Kg</th>
                <th>%</th>
                <th>Kg</th>
                <th>%</th>
                <th>Kg</th>
                <th>%</th>
                <th>Kg</th>
                <th>%</th>
                <th>Kg</th>
                <th>%</th>
                <th>Kg</th>
                <th>%</th>
                <th>Kg</th>
                <th>%</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $evisHAKB               = [];
                $evisHHB                = [];
                $evisJB                 = [];
                $evisTB                 = [];
                $evisUB                 = [];
                $evisKKBB               = [];
                $evisKLB                = [];
                $evisBK                 = [];

                $total_HAKB             = 0;
                $total_PersenHAKB       = 0;
                $total_HHB              = 0;
                $total_PersenHHB        = 0;
                $total_JB               = 0;
                $total_PersenJB         = 0;
                $total_TB               = 0;
                $total_PersenTB         = 0;
                $total_UB               = 0;
                $total_PersenUB         = 0;
                $total_KKB              = 0;
                $total_PersenKKB        = 0;
                $total_KLB              = 0;
                $total_PersenKLB        = 0;
                $total_BK               = 0;
                $total_PersenBK         = 0;

            @endphp
            @foreach ($newData as $it)
            @php
                $evisberat = 0;
                $evis       =\App\Models\Evis::where('production_id', $it->id)
                            ->leftJoin('items','evis.item_id','=','items.id')
                            ->whereIn('items.id',[171,173,176,178,179,181,184])
                            ->select('items.nama','items.id as iditem','evis.*')
                            ->groupBy('evis.item_id')
                            ->get()
                            ->sum('berat_stock');
                $evisHAKB   = \App\Models\Evis::where('production_id', $it->id)
                            ->leftJoin('items','evis.item_id','=','items.id')
                            ->whereIn('items.id',[171])
                            ->select('items.nama','items.id as iditem','evis.*')
                            ->groupBy('evis.item_id')
                            ->get();
                $evisHHB   = \App\Models\Evis::where('production_id', $it->id)
                            ->leftJoin('items','evis.item_id','=','items.id')
                            ->whereIn('items.id',[173])
                            ->select('items.nama','items.id as iditem','evis.*')
                            ->groupBy('evis.item_id')
                            ->get();
                $evisJB   = \App\Models\Evis::where('production_id', $it->id)
                            ->leftJoin('items','evis.item_id','=','items.id')
                            ->whereIn('items.id',[176])
                            ->select('items.nama','items.id as iditem','evis.*')
                            ->groupBy('evis.item_id')
                            ->get();
                $evisTB   = \App\Models\Evis::where('production_id', $it->id)
                            ->leftJoin('items','evis.item_id','=','items.id')
                            ->whereIn('items.id',[178])
                            ->select('items.nama','items.id as iditem','evis.*')
                            ->groupBy('evis.item_id')
                            ->get();
                $evisUB   = \App\Models\Evis::where('production_id', $it->id)
                            ->leftJoin('items','evis.item_id','=','items.id')
                            ->whereIn('items.id',[179])
                            ->select('items.nama','items.id as iditem','evis.*')
                            ->groupBy('evis.item_id')
                            ->get();
                $evisKKB   = \App\Models\Evis::where('production_id', $it->id)
                            ->leftJoin('items','evis.item_id','=','items.id')
                            ->whereIn('items.id',[181])
                            ->select('items.nama','items.id as iditem','evis.*')
                            ->groupBy('evis.item_id')
                            ->get();
                $evisKLB   = \App\Models\Evis::where('production_id', $it->id)
                            ->leftJoin('items','evis.item_id','=','items.id')
                            ->whereIn('items.id',[184])
                            ->select('items.nama','items.id as iditem','evis.*')
                            ->groupBy('evis.item_id')
                            ->get();
                $evisBK     = \App\Models\Evis::where('production_id', $it->id)
                            ->leftJoin('items','evis.item_id','=','items.id')
                            ->whereIn('items.id',[183])
                            ->select('items.nama','items.id as iditem','evis.*')
                            ->groupBy('evis.item_id')
                            ->get();
            @endphp
                <tr>
                    <td>{{$loop->iteration}}</td>
                    @if($evisHAKB->count() > 0 )
                        @php 
                            $Prod                   = \App\Models\Production::find($evisHAKB[0]->production_id);
                        @endphp
                        @foreach ($evisHAKB as $i)
                            @php     
                                $total_HAKB         += $i->berat_stock ;
                                $total_PersenHAKB   += $i->berat_stock / $Prod->lpah_berat_terima * 100;
                            @endphp
                            <td class="text-right">{{ number_format($i->berat_stock, 1,',', '.') ?? '0' }}</td>
                            <td class="text-right">{{ number_format(($i->berat_stock / $Prod->lpah_berat_terima) * 100 , 2,',', '.') ?? '0' }} %</td>
                        @endforeach
                    @else 
                            <td class="text-right">{{ number_format(0,1) }}</td>
                            <td class="text-right">{{ number_format(0,2) }} %</td>
                    @endif
                    @if($evisHHB->count() > 0 )
                        @php 
                            $Prod                   = \App\Models\Production::find($evisHHB[0]->production_id);
                        @endphp
                        @foreach ($evisHHB as $i)
                            @php 
                                $total_HHB          += $i->berat_stock;
                                $total_PersenHHB    += $i->berat_stock/$Prod->lpah_berat_terima * 100;
                            @endphp
                            <td class="text-right">{{ number_format($i->berat_stock, 1,',', '.') ?? '0' }}</td>
                            <td class="text-right">{{ number_format(($i->berat_stock/$Prod->lpah_berat_terima) * 100 , 2,',', '.') ?? '0' }} %</td>
                        @endforeach
                    @else 
                            <td class="text-right">{{ number_format(0,1) }}</td>
                            <td class="text-right">{{ number_format(0,2) }} %</td>
                    @endif

                    @if($evisJB->count() > 0 )
                        @php 
                            $Prod                   = \App\Models\Production::find($evisJB[0]->production_id);
                        @endphp
                        @foreach ($evisJB as $i)
                            @php 
                                $total_JB           += $i->berat_stock;
                                $total_PersenJB     += $i->berat_stock/$Prod->lpah_berat_terima * 100;
                            @endphp
                            <td class="text-right">{{ number_format($i->berat_stock, 1,',', '.') ?? '0' }}</td>
                            <td class="text-right">{{ number_format(($i->berat_stock/$Prod->lpah_berat_terima) * 100 , 2,',', '.') ?? '0' }} %</td>
                        @endforeach
                    @else 
                            <td class="text-right">{{ number_format(0,1) }}</td>
                            <td class="text-right">{{ number_format(0,2) }} %</td>
                    @endif
                    @if($evisTB->count() > 0 )
                        @php 
                            $Prod                   = \App\Models\Production::find($evisTB[0]->production_id);
                        @endphp
                        @foreach ($evisTB as $i)
                            @php 
                                $total_TB           += $i->berat_stock;
                                $total_PersenTB     += $i->berat_stock / $Prod->lpah_berat_terima * 100;
                            @endphp
                            <td class="text-right">{{ number_format($i->berat_stock, 1,',', '.') ?? '0' }}</td>
                            <td class="text-right">{{ number_format(($i->berat_stock / $Prod->lpah_berat_terima) * 100 , 2,',', '.') ?? '0' }} %</td>
                        @endforeach
                    @else 
                            <td class="text-right">{{ number_format(0,1) }}</td>
                            <td class="text-right">{{ number_format(0,2) }} %</td>
                    @endif
                    @if($evisUB->count() > 0 )
                        @php 
                            $Prod                   = \App\Models\Production::find($evisUB[0]->production_id);
                        @endphp
                        @foreach ($evisUB as $i)
                            @php 
                                $total_UB           += $i->berat_stock;
                                $total_PersenUB     += $i->berat_stock / $Prod->lpah_berat_terima * 100;
                            @endphp
                            <td class="text-right">{{ number_format($i->berat_stock, 1,',', '.') ?? '0' }}</td>
                            <td class="text-right">{{ number_format(($i->berat_stock / $Prod->lpah_berat_terima) * 100 , 2,',', '.') ?? '0' }} %</td>
                        @endforeach
                    @else 
                            <td class="text-right">{{ number_format(0,1) }}</td>
                            <td class="text-right">{{ number_format(0,2) }} %</td>
                    @endif
                    @if($evisKKB->count() > 0 )
                        @php 
                            $Prod                   = \App\Models\Production::find($evisKKB[0]->production_id);
                        @endphp
                        @foreach ($evisKKB as $i)
                            @php 
                                $total_KKB          += $i->berat_stock;
                                $total_PersenKKB    += $i->berat_stock / $Prod->lpah_berat_terima * 100;
                            @endphp
                            <td class="text-right">{{ number_format($i->berat_stock, 1,',', '.') ?? '0' }}</td>
                            <td class="text-right">{{ number_format(($i->berat_stock / $Prod->lpah_berat_terima) * 100 , 2,',', '.') ?? '0' }} %</td>
                        @endforeach
                    @else 
                            <td class="text-right">{{ number_format(0,1) }}</td>
                            <td class="text-right">{{ number_format(0,2) }} %</td>
                    @endif
                    @if($evisBK->count() > 0 )
                        @php 
                            $Prod                   = \App\Models\Production::find($evisBK[0]->production_id);
                        @endphp
                        @foreach ($evisBK as $i)
                            @php 
                                $total_BK          += $i->berat_stock;
                                $total_PersenBK    += $i->berat_stock / $Prod->lpah_berat_terima * 100;
                            @endphp
                            <td class="text-right">{{ number_format($i->berat_stock, 1,',', '.') ?? '0' }}</td>
                            <td class="text-right">{{ number_format(($i->berat_stock / $Prod->lpah_berat_terima) * 100 , 2,',', '.') ?? '0' }} %</td>
                        @endforeach
                    @else 
                            <td class="text-right">{{ number_format(0,1) }}</td>
                            <td class="text-right">{{ number_format(0,2) }} %</td>
                    @endif
                    @if($evisKLB->count() > 0 )
                        @php 
                            $Prod                   = \App\Models\Production::find($evisKLB[0]->production_id);
                        @endphp
                        @foreach ($evisKLB as $i)
                            @php 
                                $total_KLB          += $i->berat_stock;
                                $total_PersenKLB    += $i->berat_stock / $Prod->lpah_berat_terima * 100;
                            @endphp
                            <td class="text-right">{{ number_format($i->berat_stock, 1,',', '.') ?? '0' }}</td>
                            <td class="text-right">{{ number_format(($i->berat_stock / $Prod->lpah_berat_terima) * 100 , 2,',', '.') ?? '0' }} %</td>
                        @endforeach
                    @else 
                            <td class="text-right">{{ number_format(0,1) }}</td>
                            <td class="text-right">{{ number_format(0,2) }} %</td>
                    @endif
                    
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td class="text-right"><b></b></td>
                <td class="text-right"><b>{{ number_format($total_HAKB,1) }} Kg</b></td>
                <td class="text-right"><b>{{ number_format(($total_PersenHAKB / $NewArray['jumlahdataevis']),2) }} %</b></td>
                <td class="text-right"><b>{{ number_format($total_HHB,1) }} Kg</b></td>
                <td class="text-right"><b>{{ number_format(($total_PersenHHB / $NewArray['jumlahdataevis']),2) }} %</b></td>
                <td class="text-right"><b>{{ number_format($total_JB,1) }} Kg</b></td>
                <td class="text-right"><b>{{ number_format(($total_PersenJB / $NewArray['jumlahdataevis']),2) }} %</b></td>
                <td class="text-right"><b>{{ number_format($total_TB,1) }} Kg</b></td>
                <td class="text-right"><b>{{ number_format(($total_PersenTB / $NewArray['jumlahdataevis']),2) }} %</b></td>
                <td class="text-right"><b>{{ number_format($total_UB,1) }} Kg</b></td>
                <td class="text-right"><b>{{ number_format(($total_PersenUB / $NewArray['jumlahdataevis']),2) }} %</b></td>
                <td class="text-right"><b>{{ number_format($total_KKB,1) }} Kg</b></td>
                <td class="text-right"><b>{{ number_format(($total_PersenKKB / $NewArray['jumlahdataevis']),2) }} %</b></td>
                <td class="text-right"><b>{{ number_format($total_BK,1) }} Kg</b></td>
                <td class="text-right"><b>{{ number_format(($total_PersenBK / $NewArray['jumlahdataevis']),2) }} %</b></td>
                <td class="text-right"><b>{{ number_format($total_KLB,1) }} Kg</b></td>
                <td class="text-right"><b>{{ number_format(($total_PersenKLB / $NewArray['jumlahdataevis']),2) }} %</b></td>
            </tr>
        </tfoot>
    </table>