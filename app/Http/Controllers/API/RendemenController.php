<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Chiller;
use App\Models\Evis;
use App\Models\Freestock;
use App\Models\FreestockList;
use App\Models\FreestockTemp;
use App\Models\Grading;
use App\Models\Item;
use App\Models\LaporanRendemen;
use App\Models\Production;
use App\Models\Purchasing;
use App\Models\ReturItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RendemenController extends Controller
{
    public function store_rendemen()
    {
        $count_purchase     =   Purchasing::where('jenis_po', 'PO LB')->whereDate('tanggal_potong', Carbon::now())->count();
        $count_production   =   Production::whereIn('purchasing_id', Purchasing::select('id')
                                ->where('jenis_po', 'PO LB')
                                ->whereDate('tanggal_potong', Carbon::now()))
                                ->count();

        $data_product       =   Production::select(
                                    DB::raw("SUM(sc_ekor_do) AS ekor"),
                                    DB::raw("SUM(sc_berat_do) AS berat"),
                                    DB::raw("(SUM(sc_berat_do) / SUM(sc_ekor_do)) AS rerata"),
                                    DB::raw("SUM(ekoran_seckle) AS seckle"),
                                    DB::raw("SUM(lpah_berat_terima) AS kg_terima"),
                                    DB::raw("(SUM(lpah_berat_terima) / SUM(ekoran_seckle)) AS rata_terima"),
                                    DB::raw("SUM(lpah_berat_susut) AS susut_berat"),
                                    DB::raw("SUM(qc_ekor_ayam_mati) AS susut_ekor")
                                )
                                ->whereIn('purchasing_id', Purchasing::select('id')->where('jenis_po', 'PO LB'))->whereDate('lpah_tanggal_potong', Carbon::now())
                                ->where('no_urut', '!=', NULL)
                                ->first();

        $data_diproses      =   Production::whereIn('purchasing_id', Purchasing::select('id')
                                ->where('jenis_po', 'PO LB'))
                                ->whereDate('lpah_tanggal_potong', Carbon::now())
                                ->whereIn('grading_status', [1,2])
                                ->count();

        $susut_tangkap      =   Production::whereIn('purchasing_id', Purchasing::select('id')->where('jenis_po', 'PO LB'))->whereDate('lpah_tanggal_potong', Carbon::now())
                                ->select(DB::raw("((SUM(sc_berat_do)-SUM(lpah_berat_terima)) / SUM(sc_berat_do)) * 100 AS susut"))
                                ->where('po_jenis_ekspedisi', 'tangkap')
                                ->where('no_urut', '!=', NULL)
                                ->where('grading_status', '1')
                                ->first();



        $susut_kirim    =   Production::whereIn('purchasing_id', Purchasing::select('id')->where('jenis_po', 'PO LB'))
            ->whereDate('lpah_tanggal_potong', Carbon::now())
            ->select(DB::raw("((SUM(sc_berat_do)-SUM(lpah_berat_terima)) / SUM(sc_berat_do)) * 100 AS susut"))
            ->where('po_jenis_ekspedisi', 'kirim')
            ->where('no_urut', '!=', NULL)
            ->where('grading_status', '1')
            ->first();

        $seckle         =   Production::whereIn('purchasing_id', Purchasing::select('id')->where('jenis_po', 'PO LB'))
            ->whereDate('lpah_tanggal_potong', Carbon::now())
            ->select(DB::raw("(SUM(sc_ekor_do) - SUM(ekoran_seckle)) AS seckle"))
            ->where('no_urut', '!=', NULL)
            ->where('grading_status', '1')
            ->first();

        $lpah_kirim    =   Production::whereIn('purchasing_id', Purchasing::select('id')->where('jenis_po', 'PO LB'))
            ->whereDate('lpah_tanggal_potong', Carbon::now())
            ->where('po_jenis_ekspedisi', 'kirim')
            ->where('no_urut', '!=', NULL)
            ->where('grading_status', '1')
            ->sum('lpah_berat_terima');

        $mobil_kirim    =   Production::whereIn('purchasing_id', Purchasing::select('id')->where('jenis_po', 'PO LB'))
            ->whereDate('lpah_tanggal_potong', Carbon::now())
            ->where('po_jenis_ekspedisi', 'kirim')
            ->where('no_urut', '!=', NULL)
            ->where('grading_status', '1')->count();

        $grading_kirim  =   Grading::whereIn('trans_id', Production::select('id')
                            ->whereIn('purchasing_id', Purchasing::select('id')
                            ->where('jenis_po', 'PO LB')
                            ->where('po_jenis_ekspedisi', 'kirim'))
                            ->whereDate('lpah_tanggal_potong', Carbon::now())
                            ->where('grading_status', '1'))
                            ->select(DB::raw("SUM(total_item) AS ekor"), DB::raw("SUM(berat_item) AS berat"), DB::raw("(SUM(berat_item) / SUM(total_item)) AS ratarata"))
                            ->first();

        $do_tangkap    =   Production::whereIn('purchasing_id', Purchasing::select('id')->where('jenis_po', 'PO LB'))
        ->whereDate('lpah_tanggal_potong', Carbon::now())
            ->where('po_jenis_ekspedisi', 'tangkap')
            ->where('no_urut', '!=', NULL)
            ->where('grading_status', '1')
            ->sum('sc_berat_do');

        $mobil_tangkap    =   Production::whereIn('purchasing_id', Purchasing::select('id')->where('jenis_po', 'PO LB'))
            ->whereDate('lpah_tanggal_potong', Carbon::now())
            ->where('po_jenis_ekspedisi', 'tangkap')
            ->where('no_urut', '!=', NULL)
            ->where('grading_status', '1')->count();

        $grading_tangkap    =   Grading::whereIn('trans_id', Production::select('id')->whereIn('purchasing_id', Purchasing::select('id')->where('jenis_po', 'PO LB')->where('po_jenis_ekspedisi', 'tangkap'))
            ->whereDate('lpah_tanggal_potong', Carbon::now())->where('grading_status', '1'))
            ->select(DB::raw("SUM(total_item) AS ekor"), DB::raw("SUM(berat_item) AS berat"), DB::raw("(SUM(berat_item) / SUM(total_item)) AS ratarata"))
            ->first();

        $grading_rpa =   Grading::whereIn('trans_id', Production::select('id')->whereIn('purchasing_id', Purchasing::select('id')->where('jenis_po', 'PO LB'))->whereDate('lpah_tanggal_potong', Carbon::now())
            ->where('grading_status', '1')
            ->where('evis_status', '1'))
            ->select(DB::raw("SUM(total_item) AS ekor"), DB::raw("SUM(berat_item) AS berat"), DB::raw("(SUM(berat_item) / SUM(total_item)) AS ratarata"))
            ->first();

        $evis_rpa    =   Evis::whereIn('production_id', Production::select('id')->whereIn('purchasing_id', Purchasing::select('id')->where('jenis_po', 'PO LB'))->whereDate('lpah_tanggal_potong', Carbon::now())
            ->where('grading_status', '1')
            ->where('evis_status', '1'))
            ->select(DB::raw(DB::raw("SUM(berat_item) AS berat")))
            ->first();

        $terima_rpa   =   Production::whereIn('purchasing_id', Purchasing::select('id')->where('jenis_po', 'PO LB'))->whereDate('lpah_tanggal_potong', Carbon::now())
            ->select(
                DB::raw("SUM(ekoran_seckle) AS seckle"),
                DB::raw("SUM(lpah_berat_terima) AS kg_terima")
            )
            ->where('grading_status', '1')
            ->where('evis_status', '1')
            ->where('no_urut', '!=', NULL)
            ->first();

        $rendemen_kirim     =   0;
        $rendemen_tangkap   =   0;

        $berat_rpa     = 0;
        $ekor_rpa     = 0;
        if($terima_rpa){
            $berat_rpa      = $terima_rpa->kg_terima;
            $ekor_rpa       = $terima_rpa->seckle;
        }

        $berat_evis     = 0;
        if($evis_rpa){
            $berat_evis      = $evis_rpa->berat;
        }

        $berat_grading  = 0;
        $ekor_grading  = 0;
        if($grading_rpa){
            $berat_grading      = $grading_rpa->berat;
            $ekor_grading       = $grading_rpa->ekor;
        }

        if ($do_tangkap) {
            $rendemen_tangkap   =   (($grading_tangkap->berat / $do_tangkap) * 100);
        }

        if ($lpah_kirim) {
            $rendemen_kirim      =  (($grading_kirim->berat / $lpah_kirim) * 100);
        };

        $data_grading   =   Grading::whereIn('trans_id', Production::select('id')->whereIn('purchasing_id', Purchasing::select('id')->where('jenis_po', 'PO LB'))->whereDate('lpah_tanggal_potong', Carbon::now()))
            ->select(DB::raw("SUM(total_item) AS ekor"), DB::raw("SUM(berat_item) AS berat"), DB::raw("(SUM(berat_item) / SUM(total_item)) AS ratarata"))
            ->first();

        $data_evis      =   Evis::whereIn('production_id', Production::select('id')->whereIn('purchasing_id', Purchasing::select('id')->where('jenis_po', 'PO LB'))->whereDate('lpah_tanggal_potong', Carbon::now()))
            ->select(DB::raw("SUM(total_item) AS item"), DB::raw("SUM(berat_item) AS berat"), DB::raw("(SUM(berat_item) / SUM(total_item)) AS ratarata"))
            ->first();

        $data_chiller   =   Chiller::whereIn('production_id', Production::select('id')->whereIn('purchasing_id', Purchasing::select('id')->where('jenis_po', 'PO LB'))->whereDate('lpah_tanggal_potong', Carbon::now()))
            ->select(DB::raw("SUM(qty_item) AS item"), DB::raw("SUM(berat_item) AS berat"))
            ->where('jenis', 'masuk')
            ->where('status', 2)
            ->first();

        $bb_chiller     =   Chiller::whereIn('production_id', Production::select('id')->whereIn('purchasing_id', Purchasing::select('id')->where('jenis_po', 'PO LB'))->whereDate('lpah_tanggal_potong', Carbon::now()))
            ->select(DB::raw("SUM(qty_item) AS item"))
            ->where('jenis', 'masuk')
            ->where('type', 'bahan-baku')
            ->where('status', 2)
            ->first();

        $freestock      =   Chiller::whereIn('production_id', Production::select('id')->whereIn('purchasing_id', Purchasing::select('id')->where('jenis_po', 'PO LB'))->whereDate('lpah_tanggal_potong', Carbon::now()))
            ->select(DB::raw("SUM(qty_item) AS item"))
            ->where('jenis', 'masuk')
            ->where('type', 'hasil-produksi')
            ->where('table_name', 'free_stock')
            ->where('status', 2)
            ->first();

        $bbout_chiller  =   Chiller::whereIn('production_id', Production::select('id')->whereIn('purchasing_id', Purchasing::select('id')->where('jenis_po', 'PO LB'))->whereDate('lpah_tanggal_potong', Carbon::now()))
            ->select(DB::raw("SUM(qty_item) AS item"))
            ->where('jenis', 'keluar')
            ->where('type', 'pengambilan-bahan-baku')
            ->where('status', 4)
            ->first();


        $karkas         =   Item::where('category_id', 1)->get();
        $evis           =   Item::where('category_id', 4)->get();

        $ayam_utuh      =   FreestockTemp::whereIn('freestock_id', Freestock::select('id')->whereDate('tanggal', Carbon::now())->where('status', 3)->where('regu', 'whole'))->sum('berat');
        $parting        =   FreestockTemp::whereIn('freestock_id', Freestock::select('id')->whereDate('tanggal', Carbon::now())->where('status', 3)->where('regu', 'parting'))->sum('berat');
        $parting_mari   =   FreestockTemp::whereIn('freestock_id', Freestock::select('id')->whereDate('tanggal', Carbon::now())->where('status', 3)->where('regu', 'marinasi'))->sum('berat');
        $boneless       =   FreestockTemp::whereIn('freestock_id', Freestock::select('id')->whereDate('tanggal', Carbon::now())->where('status', 3)->where('regu', 'boneless'))->sum('berat');
        $frozen         =   FreestockTemp::whereIn('freestock_id', Freestock::select('id')->whereDate('tanggal', Carbon::now())->where('status', 3)->where('regu', 'frozen'))->sum('berat');

        $retur          =   ReturItem::select('retur_item.item_id', DB::raw("SUM(berat) AS kg"), DB::raw("SUM(qty) AS total"))
            ->leftJoin('retur', 'retur.id', '=', 'retur_item.retur_id')
            ->whereDate('tanggal_retur', Carbon::now())
            ->groupBy('item_id')
            ->get();

        $thawing        =   Chiller::select('item_id', 'item_name', DB::raw("SUM(qty_item) AS qty"), DB::raw("SUM(berat_item) AS berat"))
            ->where('asal_tujuan', 'thawing')
            ->whereDate('tanggal_produksi', Carbon::now())
            ->groupBy('item_id', 'item_name')
            ->get();

        $ambil_bb       =   FreestockList::select('chiller.asal_tujuan', 'chiller.tanggal_produksi', DB::raw("SUM(qty) AS total"), DB::raw("SUM(berat) AS kg"))
            ->whereIn('freestock_id', Freestock::select('id')->whereDate('tanggal', Carbon::now())->where('status', 3))
            ->leftJoin('chiller', 'chiller.id', '=', 'free_stocklist.chiller_id')
            ->groupBy('chiller.asal_tujuan', 'chiller.tanggal_produksi')
            ->orderByRaw('chiller.asal_tujuan ASC, chiller.tanggal_produksi DESC')
            ->get();

        $produksi_evis  =   Evis::select(DB::raw("SUM(berat_item) AS total"), 'item_id')
                            ->whereDate('tanggal_potong', Carbon::now())
                            ->groupBy('item_id')
                            ->get();

        $data   =   [
            'data_production'   =>  $data_product,
            'data_grading'      =>  $data_grading,
            'data_diproses'     =>  $data_diproses,
            'data_evis'         =>  $data_evis,
            'data_chiller'      =>  $data_chiller,
            'ekor_rpa'          =>  $ekor_rpa,
            'ekor_grading'      =>  $ekor_grading,
            'berat_rpa'         =>  $berat_rpa,
            'berat_grading'     =>  $berat_grading,
            'berat_evis'        =>  $berat_evis,
            'bb_chiller'        =>  $bb_chiller,
            'bbout_chiller'     =>  $bbout_chiller,
            'freestock'         =>  $freestock,
            'ayam_utuh'         =>  $ayam_utuh,
            'parting'           =>  $parting,
            'parting_marinasi'  =>  $parting_mari,
            'boneless'          =>  $boneless,
            'frozen'            =>  $frozen,
            'susut_tangkap'     =>  $susut_tangkap->susut,
            'susut_kirim'       =>  $susut_kirim->susut,
            'seckle'            =>  $seckle->seckle,
            'count_purchase'    =>  $count_purchase,
            'count_production'  =>  $count_production,
            'rendemen_kirim'    =>  $rendemen_kirim,
            'rendemen_tangkap'  =>  $rendemen_tangkap,
            'mobil_tangkap'     =>  $mobil_tangkap,
            'mobil_kirim'       =>  $mobil_kirim,
            'retur'             =>  $retur,
            'thawaing'          =>  $thawing,
            'ambil_bb'          =>  $ambil_bb,
            'ambil_bb_sum'      =>  $ambil_bb->sum('kg'),
        ];


        $rt =   $data['rendemen_tangkap'];
        $rk =   $data['rendemen_kirim'];
        $rendemen_total = 0;
        if ($rt == 0) {
            $rendemen_total =   $rk;
        }

        if ($rk == 0) {
            $rendemen_total =   $rt;
        }

        if ($rk != 0 && $rt != 0) {
            $rendemen_total =   (($rk * $data['mobil_kirim']) + ($rt * $data['mobil_tangkap'])) / ($data['mobil_tangkap'] + $data['mobil_kirim']);
        }


        DB::beginTransaction() ;

        $subsidiary     =   env('NET_SUBSIDIARY_ID', '2') ;

        $rendemen                           =   LaporanRendemen::whereDate('tanggal', Carbon::now())
                                                ->where('subsidiary_id', $subsidiary)
                                                ->first() ?? new LaporanRendemen ;
        $rendemen->tanggal                  =   Carbon::now() ;
        $rendemen->subsidiary_id            =   env('NET_SUBSIDIARY_ID', '2') ;
        $rendemen->subsidiary               =   env('NET_SUBSIDIARY', 'CGL') ;
        $rendemen->rendemen_total           =   round($rendemen_total, 2) ;
        $rendemen->rendemen_tangkap         =   round($data['rendemen_tangkap'], 2) ;
        $rendemen->rendemen_kirim           =   round($data['rendemen_kirim'], 2) ;
        $rendemen->berat_rpa                =   round($data['berat_rpa'], 2) ;
        $rendemen->berat_grading            =   round($data['berat_grading'], 2) ;
        $rendemen->berat_evis               =   round($data['berat_evis'], 2) ;
        $rendemen->darah_bulu               =   round((($data['berat_rpa']) - $data['berat_grading'] - $data['berat_evis']), 2) ;
        $rendemen->ekor_rpa                 =   $data['ekor_rpa'] ;
        $rendemen->ekor_grading             =   $data['ekor_grading'] ;
        $rendemen->selisih_ekor             =   ($data['ekor_rpa']-$data['ekor_grading']) ;
        $rendemen->jumlah_supplier          =   $data['count_purchase'] ;
        $rendemen->jumlah_po_mobil          =   $data['count_production'] ;
        $rendemen->selesai_potong            =   $data['data_diproses'] ;
        $rendemen->ekor_do                  =   $data['data_production']->ekor ;
        $rendemen->berat_do                 =   round($data['data_production']->berat, 2) ;
        $rendemen->rerata_do                =   round($data['data_production']->rerata, 2) ;
        $rendemen->ekoran_seckel            =   $data['data_production']->seckle ;
        $rendemen->kg_terima                =   round($data['data_production']->kg_terima, 2) ;
        $rendemen->rerata_terima_lb         =   round($data['data_production']->rata_terima, 2) ;
        $rendemen->susut_tangkap            =   round($data['susut_tangkap'], 2) ;
        $rendemen->susut_kirim              =   round($data['susut_kirim'], 2) ;
        $rendemen->susut_seckel             =   $data['seckle'] ;
        $rendemen->ekoran_grading           =   $data['data_grading']->ekor ;
        $rendemen->selisih_seckel_grading   =   (($data['data_production']->seckle ?? 0) - ($data['data_grading']->ekor ?? 0)) ;
        $rendemen->rerata_grading           =   round($data['data_grading']->ratarata, 2) ;
        if (!$rendemen->save()) {
            DB::rollBack() ;
            $result['status']   =   400 ;
            $result['msg']      =   'Proses gagal' ;
            return $result ;
        }

        DB::commit();
        $result['status']   =   200;
        $result['msg']      =   'Sukses';
        return $result;
    }
}
