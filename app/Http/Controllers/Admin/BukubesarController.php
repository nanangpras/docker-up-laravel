<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chiller;
use App\Models\Evis;
use App\Models\Grading;
use App\Models\Production;
use App\Models\Purchasing;
use App\Models\Unifomity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BukubesarController extends Controller
{

    public function index(Request $request)
    {

        $tanggal    =   $request->tanggal ?? date('Y-m-d');
        $tanggalend =   $request->tanggalend ?? date('Y-m-d');
        $report     =   $request->report ?? '' ;
        $data       =   Production::whereIn('purchasing_id', Purchasing::select('id')->whereBetween('tanggal_potong', [$tanggal, $tanggalend])->where(function($query) use ($report){
                            if ($report) {
                                if ($report == 'po_lb') {
                                    $query->where('jenis_po', 'PO LB');
                                }
                                if ($report == 'non_lb') {
                                    $query->where('jenis_po', '!=', 'PO LB');
                                }
                            }
                        }))
                        ->where('sc_status', '1')
                        ->orderBy('prod_tanggal_potong')
                        ->orderBy('no_urut', 'ASC');
                        // ->get();


        $cloneNewData               = clone $data;

        $cloneJumlahData            = clone $data;      
        $cloneDataEvis              = clone $data;      
        $cloneTimbangLPAH           = clone $data;      
        $cloneDataSusut             = clone $data;      
        $cloneDataMati              = clone $data;      
        $cloneDataGrading           = clone $data;      



        $newData                    = $cloneNewData->get();
        $NewArray       = [
            "jumlahdata"            =>  $cloneJumlahData->whereNotNull('lpah_berat_terima')->get()->count(),
            "jumlahdataevis"        =>  $cloneDataEvis->whereNotNull('evis_status')->get()->count(),
            "jumlahtimbanglpah"     =>  $cloneTimbangLPAH->whereNotNull('ekoran_seckle')->get()->count(),
            "jumlahDataSusut"       =>  $cloneDataSusut->whereNotNull('lpah_persen_susut')->get()->count(),
            "jumlahDataMati"        =>  $cloneDataMati->whereNotNull('qc_persen_ayam_mati')->where('qc_persen_ayam_mati','!=',0)->get()->count(),
            "jumlahDataGrading"     =>  $cloneDataGrading->whereNotNull('ekoran_seckle')->get()->count()
        ];

        // dd($NewArray);

        $uniformArr =   [];
        $underArr   =   [];
        $overArr    =   [];
        // foreach ($data as $dat) {
        //     $uniformity =   Unifomity::where('production_id', $dat->id)->get();

        //     $uniform    =   0;
        //     $under      =   0;
        //     $over       =   0;
        //     foreach ($uniformity as $uni) {
        //         if ($uni->uniprod->prodpur->ukuran_ayam == '8-10') {
        //             if ($uni->berat >= '0.8' and $uni->berat <= '1') {
        //                 $uniform += 1;
        //             }
        //             if ($uni->berat < '0.8') {
        //                 $under += 1;
        //             }
        //             if ($uni->berat > '1') {
        //                 $over += 1;
        //             }
        //         }
        //         if ($uni->uniprod->prodpur->ukuran_ayam == '1.0-1.2') {
        //             if ($uni->berat >= '1' and $uni->berat <= '1.2') {
        //                 $uniform += 1;
        //             }
        //             if ($uni->berat < '1') {
        //                 $under += 1;
        //             }
        //             if ($uni->berat > '1.2') {
        //                 $over += 1;
        //             }
        //         }
        //         if ($uni->uniprod->prodpur->ukuran_ayam == '1.2-1.4') {
        //             if ($uni->berat >= '1.2' and $uni->berat <= '1.4') {
        //                 $uniform += 1;
        //             }
        //             if ($uni->berat < '1.2') {
        //                 $under += 1;
        //             }
        //             if ($uni->berat > '1.4') {
        //                 $over += 1;
        //             }
        //         }
        //         if ($uni->uniprod->prodpur->ukuran_ayam == '1.4-1.6') {
        //             if ($uni->berat >= '1.4' and $uni->berat <= '1.6') {
        //                 $uniform += 1;
        //             }
        //             if ($uni->berat < '1.4') {
        //                 $under += 1;
        //             }
        //             if ($uni->berat > '1.6') {
        //                 $over += 1;
        //             }
        //         }
        //         if ($uni->uniprod->prodpur->ukuran_ayam == '1.6-1.8') {
        //             if ($uni->berat >= '1.6' and $uni->berat <= '1.8') {
        //                 $uniform += 1;
        //             }
        //             if ($uni->berat < '1.6') {
        //                 $under += 1;
        //             }
        //             if ($uni->berat > '1.8') {
        //                 $over += 1;
        //             }
        //         }
        //         if ($uni->uniprod->prodpur->ukuran_ayam == '1.8-2.0') {
        //             if ($uni->berat >= '1.8' and $uni->berat <= '2') {
        //                 $uniform += 1;
        //             }
        //             if ($uni->berat < '1.8') {
        //                 $under += 1;
        //             }
        //             if ($uni->berat > '2') {
        //                 $over += 1;
        //             }
        //         }
        //         if ($uni->uniprod->prodpur->ukuran_ayam == '2.0-2.2') {
        //             if ($uni->berat >= '2.0' and $uni->berat <= '2.2') {
        //                 $uniform += 1;
        //             }
        //             if ($uni->berat < '2.0') {
        //                 $under += 1;
        //             }
        //             if ($uni->berat > '2.2') {
        //                 $over += 1;
        //             }
        //         }
        //     }

        //     $uniformArr[]   =   $uniform;
        //     $underArr[]     =   $under;
        //     $overArr[]      =   $over;
        // }

        $arr = [
            'under'     =>  $underArr,
            'over'      =>  $overArr,
            'uni'       =>  $uniformArr,
        ];

        $item = Evis::leftJoin('items','evis.item_id','=','items.id')
                    ->whereIn('items.id',[171,173,176,178,179,181,184,183])
                    ->select('items.nama','items.id as iditem','evis.*')
                    ->groupBy('evis.item_id')
                    ->get();

        if ($request->key == 'export') {
            $clonedata      =   clone $data;

            $newData        =   $clonedata->get();

            $tanggal    =   $request->tanggal ?? date('Y-m-d');
            $tanggalend =   $request->tanggalend ?? date('Y-m-d');
            return view('admin.pages.bukubesar.laporan-rendemen',compact('newData','request','tanggal','tanggalend','arr','item','NewArray'));
        }

        return view('admin.pages.bukubesar.index', compact('newData', 'arr', 'tanggal', 'tanggalend', 'request','item','NewArray'));
    }


    public function export(Request $request)
    {

        $tanggal    =   $request->tanggal ?? date('Y-m-d');
        $tanggalend =   $request->tanggalend ?? date('Y-m-d');
        $report     =   $request->report ?? '' ;
        $data       =   Production::whereIn('purchasing_id', Purchasing::select('id')->whereBetween('tanggal_potong', [$tanggal, $tanggalend])->where(function($query) use ($report){
                            if ($report) {
                                if ($report == 'po_lb') {
                                    $query->where('jenis_po', 'PO LB');
                                }
                                if ($report == 'non_lb') {
                                    $query->where('jenis_po', '!=', 'PO LB');
                                }
                            }
                        }))
                        ->where('sc_status', '1')
                        ->orderBy('prod_tanggal_potong')
                        ->orderBy('no_urut', 'ASC')
                        ->get();


        $file =  env('NET_SUBSIDIARY', 'CGL')."_rendemen_" . $tanggal . "-" . $tanggalend . ".xls";
        $html = '<style>.text {
            mso-number-format:"\@";
            border:thin solid black;
            }</style>';

        $html .= '
                <table class="table default-table" id="export-table">
                    <thead>
                        <tr class="text-center">
                            <th class="text" rowspan="2">NO</th>
                            <th class="text" rowspan="2">DATANG</th>
                            <th class="text" rowspan="2">NO URUT</th>
                            <th class="text" rowspan="2">KODE</th>
                            <th class="text" rowspan="2">JENIS</th>
                            <th class="text" rowspan="2">NAMA ITEM</th>
                            <th class="text" rowspan="2">FARM</th>
                            <th class="text" rowspan="2">NO. DO</th>
                            <th class="text" rowspan="2">DRIVER</th>
                            <th class="text" rowspan="2">NO. MOBIL</th>
                            <th class="text" colspan="3">DO</th>
                            <th class="text" colspan="3">TIMBANG LPAH</th>
                            <th rowspan="2">SELISIH EKOR DO</th>
                            <th class="text" colspan="2">SUSUT DO-TIMBANG</th>
                            <th class="text" colspan="2">MATI (kg)</th>
                            <th class="text" rowspan="2">MATI (%)</th>
                            <th class="text" rowspan="2">RERATA MATI</th>
                            <th class="text" rowspan="2">KONDISI</th>
                            <th class="text" colspan="3">GRADING</th>
                            <th rowspan="2">YEILD PRODUKSI</th>
                            <th class="text" rowspan="2">RENDEMEN</th>
                            <th colspan="2">EVIS</th>
                            <th rowspan="2">YEILD EVIS</th>
                        </tr>
                        <tr class="text-center">
                            <th class="text" >Ekor/Pcs/Pack</th>
                            <th class="text" >Kg</th>
                            <th class="text" >Rata2 Kg</th>
                            <th class="text" >Ekor/Pcs/Pack</th>
                            <th class="text" >Kg</th>
                            <th class="text" >Rata2 Kg</th>
                            <th class="text" >Kg</th>
                            <th class="text" >%</th>
                            <th class="text" >Ekor/Pcs/Pack</th>
                            <th class="text" >Kg</th>
                            <th class="text" >Ekor/Pcs/Pack</th>
                            <th class="text" >Kg</th>
                            <th class="text" >Rata2</th>
                            <th class="text">Ekor/Pcs/Pack</th>
                            <th class="text">Kg</th>
                        </tr>
                    </thead>';


        foreach ($data as $i => $val) :
            $gradberat = 0;
            $graditem = 0;

            $uniformity =   Unifomity::where('production_id', $val->id)->get();

            $uniform    =   0;
            $under      =   0;
            $over       =   0;
            foreach ($uniformity as $uni) {
                if ($uni->uniprod->prodpur->ukuran_ayam == '8-10') {
                    if ($uni->berat >= '0.8' and $uni->berat <= '1') {
                        $uniform += 1;
                    }
                    if ($uni->berat < '0.8') {
                        $under += 1;
                    }
                    if ($uni->berat > '1') {
                        $over += 1;
                    }
                }
                if ($uni->uniprod->prodpur->ukuran_ayam == '1.0-1.2') {
                    if ($uni->berat >= '1' and $uni->berat <= '1.2') {
                        $uniform += 1;
                    }
                    if ($uni->berat < '1') {
                        $under += 1;
                    }
                    if ($uni->berat > '1.2') {
                        $over += 1;
                    }
                }
                if ($uni->uniprod->prodpur->ukuran_ayam == '1.2-1.4') {
                    if ($uni->berat >= '1.2' and $uni->berat <= '1.4') {
                        $uniform += 1;
                    }
                    if ($uni->berat < '1.2') {
                        $under += 1;
                    }
                    if ($uni->berat > '1.4') {
                        $over += 1;
                    }
                }
                if ($uni->uniprod->prodpur->ukuran_ayam == '1.4-1.6') {
                    if ($uni->berat >= '1.4' and $uni->berat <= '1.6') {
                        $uniform += 1;
                    }
                    if ($uni->berat < '1.4') {
                        $under += 1;
                    }
                    if ($uni->berat > '1.6') {
                        $over += 1;
                    }
                }
                if ($uni->uniprod->prodpur->ukuran_ayam == '1.6-1.8') {
                    if ($uni->berat >= '1.6' and $uni->berat <= '1.8') {
                        $uniform += 1;
                    }
                    if ($uni->berat < '1.6') {
                        $under += 1;
                    }
                    if ($uni->berat > '1.8') {
                        $over += 1;
                    }
                }
                if ($uni->uniprod->prodpur->ukuran_ayam == '1.8-2.0') {
                    if ($uni->berat >= '1.8' and $uni->berat <= '2') {
                        $uniform += 1;
                    }
                    if ($uni->berat < '1.8') {
                        $under += 1;
                    }
                    if ($uni->berat > '2') {
                        $over += 1;
                    }
                }
                if ($uni->uniprod->prodpur->ukuran_ayam == '2.0-2.2') {
                    if ($uni->berat >= '2.0' and $uni->berat <= '2.2') {
                        $uniform += 1;
                    }
                    if ($uni->berat < '2.0') {
                        $under += 1;
                    }
                    if ($uni->berat > '2.2') {
                        $over += 1;
                    }
                }
            }



            $summary    =   Grading::where('trans_id', $val->id)->where('keranjang', 0)->orderBy('id', 'DESC')->get();
            $gradberat   =   0;
            $graditem    =   0;
            foreach ($summary as $row) {
                $gradberat   +=  $row->berat_item;
                $graditem    +=  $row->total_item;
            }
            $evis   =   Evis::where('production_id', $val->id)->get();
            $evisberat  =   0;
            $evisekor   =   0;
            foreach ($evis as $key) {
                $evisberat += $key->berat_stock;
                $evisekor += $key->stock_item;
            }

            $kenyataanekor      = $val->ekoran_seckle;
            $kenyataanberat     = $val->lpah_berat_terima;
            $kenyataan_rerata   = 0;
            $totalselish = $val->sc_ekor_do - $val->ekoran_seckle;
            $total_rata_mati = $val->qc_ekor_ayam_mati != 0 ? $val->qc_berat_ayam_mati / $val->qc_ekor_ayam_mati : 0;
            $total_persen_mati = number_format($val->qc_ekor_ayam_mati != 0 ? ($val->qc_ekor_ayam_mati / $val->sc_ekor_do) * 100 : 0, 2);

            if ($kenyataanberat > 0 && $kenyataanekor > 0) {
                $kenyataan_rerata = number_format($kenyataanberat / $kenyataanekor, 2);
            }

            $rendemen = 0;
            if ($val->prodpur->type_ekspedisi == 'tangkap') :
                $rendemen = number_format($val->sc_berat_do != 0 ? ($gradberat / $val->sc_berat_do) * 100 : '0', 2);
            else :
                $rendemen = number_format($val->lpah_berat_terima != 0 ? ($gradberat / $val->lpah_berat_terima) * 100 : '0', 2);
            endif;

            if ($val->lpah_berat_terima != 0) {
                $yield_produksi = $val->prod_yield_produksi;
            } else {
                $yield_produksi = 0;
            }

            if ($val->lpah_berat_terima != 0) {
                $yield_evis = ($evisberat / $val->lpah_berat_terima) * 100;
            } else {
                $yield_evis = 0;
            }

            $html .= '
                    <tr>
                        <td class="text">' . ++$i . '</td>
                        <td class="text">' . $val->sc_tanggal_masuk . '</td>
                        <td class="text">' . $val->no_urut . '</td>
                        <td class="text">' . $val->no_lpah . '</td>
                        <td class="text">' . $val->prodpur->type_ekspedisi . '</td>
                        <td class="text">' . $val->prodpur->ukuran_ayam . '</td>
                        <td class="text">' . ($val->prodpur->purcsupp->nama ?? 'VENDOR TIDAK DITEMUKAN') . '</td>
                        <td class="text">' . $val->no_do . '</td>
                        <td class="text">' . $val->sc_pengemudi . '</td>
                        <td class="text">' . $val->sc_no_polisi . '</td>
                        <td class="text">' . $val->sc_ekor_do . '</td>
                        <td class="text">' . $val->sc_berat_do . '</td>
                        <td class="text">' . $val->sc_rerata_do . '</td>
                        <td class="text">' . $kenyataanekor . '</td>
                        <td class="text">' . $kenyataanberat . '</td>
                        <td class="text">' . $kenyataan_rerata . '</td>
                        <td class="text">' . $totalselish . '</td>
                        <td class="text">' . $val->lpah_berat_susut . '</td>
                        <td class="text">' . $val->lpah_persen_susut . ' %</td>
                        <td class="text">' . $val->qc_ekor_ayam_mati . '</td>
                        <td class="text">' . $val->qc_berat_ayam_mati . '</td>
                        <td class="text">' . number_format($total_persen_mati, 2) . ' %</td>
                        <td class="text">' . number_format($total_rata_mati, 2) . '</td>
                        <td class="text">' . $val->kondisi_ayam . '</td>
                        <td class="text">' . $graditem . '</td>
                        <td class="text">' . $gradberat . '</td>
                        <td class="text">' . number_format($graditem != 0 ? $gradberat / $graditem : '0', 2) . '</td>
                        <td class="text"> ' . number_format($yield_produksi, 2) . ' %</td>
                        <td class="text">' . $rendemen . ' %</td>
                        <td class="text">' . $evisekor . '</td>
                        <td class="text">' . number_format($evisberat, 2) . '</td>
                        <td class="text">' . number_format($yield_evis, 2) . ' %</td>
                    </tr>';
        endforeach;

        $html .= '<tfoot>
                        <tr>
                            <td colspan="9" class="text-center"><b>Total</b></td>
                        </tr>
                    </tfoot>
                </table>
                ';

        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=$file");
        echo $html;
    }

    public function export_lpah(Request $request)
    {
        $tanggal_mulai      =   $request->tanggal_mulai ?? date('Y-m-d');
        $tanggal_selesai    =   $request->tanggal_selesai ?? date('Y-m-d');
        $produksi           =   Production::whereBetween('prod_tanggal_potong', [$tanggal_mulai, $tanggal_selesai])
                                ->whereIn('purchasing_id', Purchasing::select('id')->where('jenis_po', 'PO LB')->where(function($query) use ($request) {
                                    if ($request->supplier) {
                                        $query->where('supplier_id', $request->supplier);
                                    }
                                }))
                                ->where('lpah_status', '1')
                                ->where('no_lpah', '!=', NULL)
                                ->orderByRaw('prod_tanggal_potong ASC, no_urut ASC')
                                ->get();

        $file = env('NET_SUBSIDIARY', 'CGL')."__lpah__" . $tanggal_mulai . "__" . $tanggal_selesai . ".xls";
        $html = '<style>th,td {
            mso-number-format:"\@";
            border:thin solid black;
            }</style>';

        $html .= '
            <table class="table default-table" id="export-table-lpah">
            <thead>
            <tr class="text-center">
                <th>No.</th>
                <th>No Urut Mobil</th>
                <th colspan="2">Tanggal</th>
                <th colspan="2">Ukuran Ayam</th>
                <th colspan="2">No. DO</th>
                <th colspan="2">Jam Datang</th>
                <th colspan="2">Jam Bongkar</th>
                <th colspan="2">Jam Selesai</th>
                <th colspan="2">SUPPLIER</th>
                <th colspan="2">DRIVER</th>
                <th colspan="2">Jenis Ekspedisi</th>
                <th colspan="2">No. MOBIL</th>
            </tr>
        </thead>
        <tbody>';

        foreach ($produksi as $i => $val) {
            $html .= '<tr>
                <td>' . ++$i . '</td>
                <td>' . $val->no_urut . '</td>
                <td colspan="2">' . $val->prod_tanggal_potong . '</td>
                <td colspan="2">' . $val->prodpur->ukuran_ayam . '</td>
                <td colspan="2">' . $val->no_do . '</td>
                <td colspan="2">' . $val->sc_jam_masuk . ' </td>
                <td colspan="2">' . $val->lpah_jam_bongkar . ' </td>
                <td colspan="2">' . $val->lpah_jam_selesai . ' </td>
                <td colspan="2">' . ($val->prodpur->purcsupp->nama ?? 'VENDOR TIDAK DITEMUKAN') . '</td>
                <td colspan="2">' . $val->sc_pengemudi . '</td>
                <td colspan="2">' . $val->po_jenis_ekspedisi . '</td>
                <td colspan="2">' . $val->sc_no_polisi . '</td>
            </tr>
            ';
        }

        $html .= '
                    </table>
                    <br>
                    <br>';

        $html .= '
            <table class="table default-table" id="export-table-lpah">
            <thead>
            <tr class="text-center">
                <th rowspan="2">No.</th>
                <th colspan="6">TIMBANG KANDANG</th>
                <th colspan="6">KENYATAAN TERIMA</th>
                <th colspan="4">SUSUT TIMBANG</th>
                <th colspan="4">MATI</th>
                <th colspan="4">PROSENTASE MATI (%)</th>
            </tr>
            <tr class="text-center">
                <th colspan="2">Ekor/Pcs/Pack</th>
                <th colspan="2">Kg</th>
                <th colspan="2">Rata2 Kg</th>
                <th colspan="2">Ekor</th>
                <th colspan="2">Kg</th>
                <th colspan="2">Rata2 Kg</th>
                <th colspan="2">Kg</th>
                <th colspan="2">%</th>
                <th colspan="2">Ekor</th>
                <th colspan="2">Kg</th>
                <th colspan="2">Ekor</th>
                <th colspan="2">Kg</th>
            </tr>
        </thead>';

        foreach ($produksi as $i => $val) {
            $html .= '
            <tr>
                <td>' . ++$i . '</td>
                <td colspan="2">' . number_format($val->sc_ekor_do, 0) . '</td>
                <td colspan="2">' . number_format($val->sc_berat_do, 2) . '</td>
                <td colspan="2">' . $val->sc_rerata_do . '</td>
                <td colspan="2">' . number_format($val->ekoran_seckle, 0) . '</td>
                <td colspan="2">' . number_format($val->lpah_berat_terima, 2) . '</td>
                <td colspan="2">' . $val->lpah_rerata_terima . '</td>
                <td colspan="2">' . number_format($val->lpah_berat_susut, 2) . '</td>
                <td colspan="2">' . $val->lpah_persen_susut . '</td>
                <td colspan="2">' . number_format($val->qc_ekor_ayam_mati, 0) . '</td>
                <td colspan="2">' . $val->qc_berat_ayam_mati . '</td>
                <td colspan="2">' . number_format($val->sc_ekor_do != 0 ? ($val->qc_ekor_ayam_mati / $val->sc_ekor_do) * 100 : 0, 2) . '</td>
                <td colspan="2">' . number_format($val->sc_berat_do != 0 ? ($val->qc_berat_ayam_mati / $val->sc_berat_do) * 100 : 0, 2) . '</td>
            </tr>
            ';
        }

        $html .= '
                    </table>
                    <br>
                    <br>';

        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=$file");
        echo $html;
    }

    public function export_qc(Request $request)
    {
        $tanggal    =   $request->tanggal ?? date('Y-m-d');
        $tanggalend    =   $request->tanggalend ?? date('Y-m-d');
        $produksi   =   Production::whereIn('purchasing_id', Purchasing::select('id')->whereBetween('tanggal_potong', [$tanggal, $tanggalend]))->orderBy('prod_tanggal_potong')->orderBy('no_urut', 'asc')->get();

        $file = "QC_" . $tanggal . ".xls";
        $html = '<style>th,td {
            mso-number-format:"\@";
            border:thin solid black;
            }</style>';

        $html .= '
                <table class="table default-table" id="export-table-qc">
                <thead>
                <tr class="text-center">
                    <th rowspan="3">No</th>
                    <th rowspan="3">Supplier</th>
                    <th rowspan="3">Tanggal Pemotongan</th>
                    <th rowspan="3">No Urut Potong</th>
                    <th rowspan="3">Jam Kedatangan</th>
                    <th rowspan="3">Jam Bongkar</th>
                    <th rowspan="3">Ekor DO</th>
                    <th rowspan="3">Ukuran Ayam</th>
                    <th rowspan="3">Sopir</th>
                    <th rowspan="3">Jumlah Ayam Merah</th>
                    <th rowspan="3">Basah Bulu</th>
                    <th rowspan="3">Ayam Mati</th>
                    <th colspan="20">Hasil Sampling QC</th>
                </tr>
                <tr class="text-center">
                    <th colspan="3">Memar</th>
                    <th colspan="2">Patah</th>
                    <th colspan="5">Keropeng</th>
                    <th rowspan="2">Dengkul Hijau</th>
                    <th colspan="2">Tembolok</th>
                    <th rowspan="2">Hati</th>
                    <th rowspan="2">Jantung</th>
                    <th rowspan="2">Usus</th>
                    <th colspan="3">Uniformity</th>
                </tr>
                <tr>
                    <th>Dada</th>
                    <th>Paha</th>
                    <th>Sayap</th>
                    <th>Sayap</th>
                    <th>Kaki</th>
                    <th>Kaki</th>
                    <th>Dada</th>
                    <th>Sayap</th>
                    <th>Punggung</th>
                    <th>Dengkul</th>
                    <th>Prosentase</th>
                    <th>Berat</th>
                    <th>Under</th>
                    <th>Uniform</th>
                    <th>Over</th>
                </tr>
            </thead>
        ';

        foreach ($produksi as $i => $val) {
            $html .= '
            <tr>
            <td>' . ++$i . '</td>
            <td>' . ($val->prodpur->purcsupp->nama ?? 'VENDOR TIDAK DITEMUKAN') . '</td>
            <td>' . date('d-m-Y', strtotime($val->prodpur->tanggal_potong)) . '</td>
            <td>' . $val->no_urut . '</td>
            <td>' . $val->sc_jam_masuk . '</td>
            <td>' . $val->lpah_jam_bongkar . '</td>
            <td>' . number_format($val->sc_ekor_do, 0) . '</td>
            <td>' . $val->prodpur->ukuran_ayam . '</td>

            <td>' . $val->sc_pengemudi . '</td>
            <td>' . ($val->post ? $val->post->ayam_merah : "") . '</td>
            <td>' . ($val->antem ? $val->antem->basah_bulu : "") . '</td>

            <td>' . ($val->antem ? $val->antem->ayam_mati : "") . '</td>

            <td>' . ($val->post ? $val->post->memar_dada : "") . '</td>
            <td>' . ($val->post ? $val->post->memar_paha : "") . '</td>
            <td>' . ($val->post ? $val->post->memar_sayap : "") . '</td>
            <td>' . ($val->post ? $val->post->patah_sayap : "") . '</td>
            <td>' . ($val->post ? $val->post->patah_kaki : "") . '</td>
            <td>' . ($val->post ? $val->post->keropeng_kaki : "") . '</td>
            <td>' . ($val->post ? $val->post->keropeng_sayap : "") . '</td>
            <td>' . ($val->post ? $val->post->keropeng_dada : "") . '</td>
            <td>' . ($val->post ? $val->post->keropeng_pg : "") . '</td>
            <td>' . ($val->post ? $val->post->keropeng_dengkul : "") . '</td>
            <td>' . ($val->post ? $val->post->kehijauan : "") . '</td>
            <td></td>
            <td>' . ($val->post ? $val->post->tembolok_jumlah : "") . '</td>
            <td>' . ($val->post ? $val->post->jeroan_hati : "") . '</td>
            <td>' . ($val->post ? $val->post->jeroan_jantung : "") . '</td>
            <td>' . ($val->post ? $val->post->jeroan_usus : "") . '</td>
            <td>' . $val->qc_under . '</td>
            <td>' . $val->qc_uniform . '</td>
            <td>' . $val->qc_over . '</td>
        </tr>
        </tr>
            ';
        }

        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=$file");
        echo $html;
    }

    public function export_rendemen()
    {

    }
}
