<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bahanbaku;
use App\Models\Chiller;
use App\Models\Freestock;
use App\Models\FreestockList;
use App\Models\FreestockTemp;
use App\Models\Grading;
use App\Models\Item;
use App\Models\OrderItem;
use App\Models\Production;
use App\Models\Purchasing;
use App\Models\VWStockProduksiFrozen;
use App\Models\VWStockTempFrozen;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WeeklyController extends Controller
{
    //

    public function index(Request $request)
    {
        $mulai  =   $request->mulai ?? date('Y-m-d') ;
        $akhir  =   $request->akhir ?? date('Y-m-d') ;
        if ($request->key == 'hasil_produksi') {
            $data   =   Production::select('lpah_tanggal_potong', 'lpah_status', DB::raw("COUNT(id) AS jumlah_mobil"), DB::raw("SUM(lpah_berat_terima) AS berat_potong"))
                        ->whereBetween('lpah_tanggal_potong', [$mulai, $akhir])
                        ->whereIN('lpah_status',[1])
                        ->groupBy('lpah_tanggal_potong')
                        ->get();

            $range_tanggal      = $this->date_range($mulai, $akhir);
            // $AllData            = DB::select("CALL getHitungProduksiAll('$mulai','$akhir','gradinggabungan','baru')");
            $AllData            = FreestockList::hitung_produksi_update($mulai,$akhir,'baru');
            $AllDataSampingan   = FreestockList::get_all_bahanbaku_sampingan($mulai,$akhir);
            $AllDataSisaChiller = FreestockList::get_all_sisachiller($mulai,$akhir);

            $collect            = collect($AllData);
            $collectSampingan   = collect($AllDataSampingan);
            $collectSisaChiller = collect($AllDataSisaChiller);

            $collection         = array();
            foreach($range_tanggal as $dt){
                $tanggal        = $dt;
                $jumlah_mobil   = 0;
                $berat_potong   = 0;
                $bb_gr          = 0;
                $qty_gr         = 0;
                $prod_yield     = 0;
                $qtyfs          = 0;
                $bbfs           = 0;

                $tanggal_kosong = true;
                foreach($data as $row){
                if($dt == $row->lpah_tanggal_potong){
                    $tanggal_kosong = false;
                    $tanggal        = $row->lpah_tanggal_potong;
                    $jumlah_mobil   = $row->jumlah_mobil;
                    $berat_potong   = $row->berat_potong;
                    $prod_yield     = Production::hitungYieldHarian($row->lpah_tanggal_potong);
                    $bb_gr          = Grading::whereIn('trans_id', Production::select('id')->whereDate('lpah_tanggal_potong', $row->lpah_tanggal_potong))->sum('berat_item') ;
                    $qty_gr         = Grading::whereIn('trans_id', Production::select('id')->whereDate('lpah_tanggal_potong', $row->lpah_tanggal_potong))->sum('total_item') ;

                    // WHOLE CHICKEN
                    $qtywc          = $collect->where('tanggal',$row->lpah_tanggal_potong)->where('regu','whole')->sum('qty');
                    $bbwc           = $collect->where('tanggal',$row->lpah_tanggal_potong)->where('regu','whole')->sum('berat');

                    // PARTING
                    $qtypt          = $collect->where('tanggal',$row->lpah_tanggal_potong)->where('regu','parting')->sum('qty');
                    $bbpt           = $collect->where('tanggal',$row->lpah_tanggal_potong)->where('regu','parting')->sum('berat');

                    // MARINASI
                    $qtymr          = $collect->where('tanggal',$row->lpah_tanggal_potong)->where('regu','marinasi')->sum('qty');
                    $bbmr           = $collect->where('tanggal',$row->lpah_tanggal_potong)->where('regu','marinasi')->sum('berat');

                    // BONELESS
                    $qtybn          = $collect->where('tanggal',$row->lpah_tanggal_potong)->where('regu','boneless')->sum('qty');
                    $bbbn           = $collect->where('tanggal',$row->lpah_tanggal_potong)->where('regu','boneless')->sum('berat');

                    // FROZEN
                    $qtyfz          = $collect->where('tanggal',$row->lpah_tanggal_potong)->where('regu','frozen')->sum('qty');
                    $bbfz           = $collect->where('tanggal',$row->lpah_tanggal_potong)->where('regu','frozen')->sum('berat');

                    // SAMPINGAN
                    $qtysp          = $collectSampingan->where('tanggal_produksi',$row->lpah_tanggal_potong)->sum('qty_item');
                    $bbsp           = $collectSampingan->where('tanggal_produksi',$row->lpah_tanggal_potong)->sum('berat_item');

                    // SUB TOTAL HARIAN
                    $qtytt          = $qtywc + $qtypt + $qtymr + $qtybn + $qtyfz + $qtysp;
                    $bbtt           = $bbwc + $bbpt + $bbmr + $bbbn + $bbfz + $bbsp;

                    // Sisa chiller
                    $qtyfs          = $qty_gr - $qtywc - $qtypt - $qtymr - $qtybn - $qtyfz - $qtysp;
                    $bbfs           = $bb_gr - $bbwc - $bbpt - $bbmr - $bbbn - $bbfz - $bbsp;

                }}
                if($tanggal_kosong==true){

                    // WHOLE CHICKEN
                    $qtywc          = $collect->where('tanggal',$dt)->where('regu','whole')->sum('qty');
                    $bbwc           = $collect->where('tanggal',$dt)->where('regu','whole')->sum('berat');

                    // PARTING
                    $qtypt          = $collect->where('tanggal',$dt)->where('regu','parting')->sum('qty');
                    $bbpt           = $collect->where('tanggal',$dt)->where('regu','parting')->sum('berat');

                    // MARINASI
                    $qtymr          = $collect->where('tanggal',$dt)->where('regu','marinasi')->sum('qty');
                    $bbmr           = $collect->where('tanggal',$dt)->where('regu','marinasi')->sum('berat');

                    // BONELESS
                    $qtybn          = $collect->where('tanggal',$dt)->where('regu','boneless')->sum('qty');
                    $bbbn           = $collect->where('tanggal',$dt)->where('regu','boneless')->sum('berat');

                    // FROZEN
                    $qtyfz          = $collect->where('tanggal',$dt)->where('regu','frozen')->sum('qty');
                    $bbfz           = $collect->where('tanggal',$dt)->where('regu','frozen')->sum('berat');

                    // SISA CHILLER
                    $qtysp          = $collectSisaChiller->where('tanggal_produksi',$dt)->sum('total_bb_item');
                    $bbsp           = $collectSisaChiller->where('tanggal_produksi',$dt)->sum('total_bb_berat');

                    // SUB TOTAL HARIAN
                    $qtytt          = $qtywc + $qtypt + $qtymr + $qtybn + $qtyfz + $qtysp;
                    $bbtt           = $bbwc + $bbpt + $bbmr + $bbbn + $bbfz + $bbsp;

                }

                $collection[]    = array(
                    'lpah_tanggal_potong'   => $tanggal,
                    'jumlah_mobil'          => $jumlah_mobil,
                    'berat_potong'          => $berat_potong,
                    'bb_gr'                 => $bb_gr,
                    'qty_gr'                => $qty_gr,
                    'prod_yield'            => $prod_yield,
                    'qtywc'                 => $qtywc,
                    'bbwc'                  => $bbwc,
                    'qtypt'                 => $qtypt,
                    'bbpt'                  => $bbpt,
                    'qtymr'                 => $qtymr,
                    'bbmr'                  => $bbmr,
                    'qtybn'                 => $qtybn,
                    'bbbn'                  => $bbbn,
                    'qtyfz'                 => $qtyfz,
                    'bbfz'                  => $bbfz,
                    'qtysp'                 => $qtysp,
                    'bbsp'                  => $bbsp,
                    'qtyfs'                 => $qtyfs,
                    'bbfs'                  => $bbfs,
                    'qtytt'                 => $qtytt,
                    'bbtt'                  => $bbtt
                );
            }

            return view('admin.pages.weekly.component.bb_fresh', compact('data', 'collection', 'range_tanggal'));
        } else if ($request->key == 'bb_lama') {
            $data   =   Production::select('lpah_tanggal_potong', DB::raw("COUNT(id) AS jumlah_mobil"), DB::raw("SUM(lpah_berat_terima) AS berat_potong"))
                        ->whereBetween('lpah_tanggal_potong', [$mulai, $akhir])
                        ->where('lpah_status', 1)
                        ->groupBy('lpah_tanggal_potong')
                        ->get();

            // $AllData            = DB::select("CALL getHitungProduksiAll('$mulai','$akhir','gradinggabungan','lama')");
            $AllData            = FreestockList::hitung_produksi_update($mulai,$akhir,'lama');
            $collect            = collect($AllData);

            $range_tanggal      = $this->date_range($mulai, $akhir);

            $collection         = array();
            foreach($range_tanggal as $dt){
                $tanggal        = $dt;
                $jumlah_mobil   = 0;
                $berat_potong   = 0;

                $tanggal_kosong = true;
                foreach($data as $row){
                if($dt == $row->lpah_tanggal_potong){
                    $tanggal_kosong = false;
                    $tanggal        = $row->lpah_tanggal_potong;
                    $jumlah_mobil   = $row->jumlah_mobil;
                    $berat_potong   = $row->berat_potong;

                    // WHOLE CHICKEN
                    $qtywc          = $collect->where('tanggal',$row->lpah_tanggal_potong)->where('regu','whole')->sum('qty');
                    $bbwc           = $collect->where('tanggal',$row->lpah_tanggal_potong)->where('regu','whole')->sum('berat');

                    // PARTING
                    $qtypt          = $collect->where('tanggal',$row->lpah_tanggal_potong)->where('regu','parting')->sum('qty');
                    $bbpt           = $collect->where('tanggal',$row->lpah_tanggal_potong)->where('regu','parting')->sum('berat');

                    // MARINASI
                    $qtymr          = $collect->where('tanggal',$row->lpah_tanggal_potong)->where('regu','marinasi')->sum('qty');
                    $bbmr           = $collect->where('tanggal',$row->lpah_tanggal_potong)->where('regu','marinasi')->sum('berat');

                    // BONELESS
                    $qtybn          = $collect->where('tanggal',$row->lpah_tanggal_potong)->where('regu','boneless')->sum('qty');
                    $bbbn           = $collect->where('tanggal',$row->lpah_tanggal_potong)->where('regu','boneless')->sum('berat');

                    // FROZEN
                    $qtyfz          = $collect->where('tanggal',$row->lpah_tanggal_potong)->where('regu','frozen')->sum('qty');
                    $bbfz           = $collect->where('tanggal',$row->lpah_tanggal_potong)->where('regu','frozen')->sum('berat');

                    // SUB TOTAL HARIAN
                    $qtytt          = $qtywc + $qtypt + $qtymr + $qtybn + $qtyfz ;
                    $bbtt           = $bbwc + $bbpt + $bbmr + $bbbn + $bbfz ;

                }}
                if($tanggal_kosong==true){

                    // WHOLE CHICKEN
                    $qtywc          = $collect->where('tanggal',$dt)->where('regu','whole')->sum('qty');
                    $bbwc           = $collect->where('tanggal',$dt)->where('regu','whole')->sum('berat');

                    // PARTING
                    $qtypt          = $collect->where('tanggal',$dt)->where('regu','parting')->sum('qty');
                    $bbpt           = $collect->where('tanggal',$dt)->where('regu','parting')->sum('berat');

                    // MARINASI
                    $qtymr          = $collect->where('tanggal',$dt)->where('regu','marinasi')->sum('qty');
                    $bbmr           = $collect->where('tanggal',$dt)->where('regu','marinasi')->sum('berat');

                    // BONELESS
                    $qtybn          = $collect->where('tanggal',$dt)->where('regu','boneless')->sum('qty');
                    $bbbn           = $collect->where('tanggal',$dt)->where('regu','boneless')->sum('berat');

                    // FROZEN
                    $qtyfz          = $collect->where('tanggal',$dt)->where('regu','frozen')->sum('qty');
                    $bbfz           = $collect->where('tanggal',$dt)->where('regu','frozen')->sum('berat');

                    // SUB TOTAL HARIAN
                    $qtytt          = $qtywc + $qtypt + $qtymr + $qtybn + $qtyfz;
                    $bbtt           = $bbwc + $bbpt + $bbmr + $bbbn + $bbfz;

                }

                $collection[]    = array(
                    'lpah_tanggal_potong'   => $tanggal,
                    'jumlah_mobil'          => $jumlah_mobil,
                    'berat_potong'          => $berat_potong,
                    'qtywc'                 => $qtywc,
                    'bbwc'                  => $bbwc,
                    'qtypt'                 => $qtypt,
                    'bbpt'                  => $bbpt,
                    'qtymr'                 => $qtymr,
                    'bbmr'                  => $bbmr,
                    'qtybn'                 => $qtybn,
                    'bbbn'                  => $bbbn,
                    'qtyfz'                 => $qtyfz,
                    'bbfz'                  => $bbfz,
                    'qtytt'                 => $qtytt,
                    'bbtt'                  => $bbtt
                );
            }

            // dd($collection);

            $download = false;
            if ($request->download == true) {
                $mulai  =   $request->mulai ?? date('Y-m-d') ;
                $akhir  =   $request->akhir ?? date('Y-m-d') ;
                $download =  true;
            }

            return view('admin.pages.weekly.component.bb_lama', compact('data', 'collection','range_tanggal', 'download','mulai','akhir'));
        }
        else if ($request->key == 'lama'){
            // $data   =   FreestockList::join('free_stock', 'free_stock.id', '=', 'free_stocklist.freestock_id')
            //             ->join('chiller','chiller.id', '=', 'free_stocklist.chiller_id')
            //             ->select('chiller.tanggal_potong', 'free_stock.tanggal', DB::raw('sum(free_stocklist.qty) as qty'), DB::raw('sum(free_stocklist.berat) as berat'))
            //             ->whereBetween('free_stock.tanggal',[$mulai,$akhir])
            //             ->where('free_stock.status',3)
            //             ->whereIn('free_stock.regu',['whole', 'frozen', 'parting', 'marinasi', 'boneless' ])
            //             ->where('free_stocklist.bb_kondisi', 'lama')
            //             ->where('chiller.asal_tujuan', 'gradinggabungan')
            //             ->groupBy('chiller.tanggal_potong')
            //             ->groupBy('free_stock.tanggal')
            //             ->get();

            $data = [];
            $range_tanggal = $this->date_range($mulai, $akhir);

            return view('admin.pages.weekly.component.lama', compact('data','range_tanggal'));
        }
        else

        if ($request->key == 'thawing') {
            $data   =   Production::select('lpah_tanggal_potong', DB::raw("COUNT(id) AS jumlah_mobil"), DB::raw("SUM(lpah_berat_terima) AS berat_potong"))
                        ->whereBetween('lpah_tanggal_potong', [$mulai, $akhir])
                        ->where('lpah_status', 1)
                        ->groupBy('lpah_tanggal_potong')
                        ->get();

            // $AllData            = DB::select("CALL getHitungProduksiAll('$mulai','$akhir','thawing','thawing')");
            $AllData            = FreestockList::hitung_produksi_update($mulai,$akhir,'thawing');
            $AllBahanBakuThawing= FreestockList::get_all_bahanbaku_thawing($mulai,$akhir);

            $collect            = collect($AllData);
            $collectBBThawing   = collect($AllBahanBakuThawing);

            $range_tanggal      = $this->date_range($mulai, $akhir);

            $collection         = array();
            foreach($range_tanggal as $dt){
                $tanggal        = $dt;

                $tanggal_kosong = true;
                foreach($data as $row){
                if($dt == $row->lpah_tanggal_potong){
                    $tanggal_kosong = false;
                    $tanggal        = $row->lpah_tanggal_potong;

                    // BAHAN BAKU Thawing
                    $qtythawing     = $collectBBThawing->where('tanggal_produksi',$row->lpah_tanggal_potong)->sum('qty_item');
                    $bbthawing      = $collectBBThawing->where('tanggal_produksi',$row->lpah_tanggal_potong)->sum('berat_item');

                    // WHOLE CHICKEN
                    $qtywc          = $collect->where('tanggal',$row->lpah_tanggal_potong)->where('regu','whole')->sum('qty');
                    $bbwc           = $collect->where('tanggal',$row->lpah_tanggal_potong)->where('regu','whole')->sum('berat');

                    // PARTING
                    $qtypt          = $collect->where('tanggal',$row->lpah_tanggal_potong)->where('regu','parting')->sum('qty');
                    $bbpt           = $collect->where('tanggal',$row->lpah_tanggal_potong)->where('regu','parting')->sum('berat');

                    // MARINASI
                    $qtymr          = $collect->where('tanggal',$row->lpah_tanggal_potong)->where('regu','marinasi')->sum('qty');
                    $bbmr           = $collect->where('tanggal',$row->lpah_tanggal_potong)->where('regu','marinasi')->sum('berat');

                    // BONELESS
                    $qtybn          = $collect->where('tanggal',$row->lpah_tanggal_potong)->where('regu','boneless')->sum('qty');
                    $bbbn           = $collect->where('tanggal',$row->lpah_tanggal_potong)->where('regu','boneless')->sum('berat');

                    // SUB TOTAL HARIAN
                    $qtytt          = $qtywc + $qtypt + $qtymr + $qtybn ;
                    $bbtt           = $bbwc + $bbpt + $bbmr + $bbbn ;

                    // SISA
                    $qtysisa        = $qtythawing - $qtytt;
                    $bbsisa         = $bbthawing - $bbtt;
                }}
                if($tanggal_kosong==true){

                    // BAHAN BAKU Thawing
                    $qtythawing     = $collectBBThawing->where('tanggal_produksi',$dt)->sum('qty_item');
                    $bbthawing      = $collectBBThawing->where('tanggal_produksi',$dt)->sum('berat_item');

                    // WHOLE CHICKEN
                    $qtywc          = $collect->where('tanggal',$dt)->where('regu','whole')->sum('qty');
                    $bbwc           = $collect->where('tanggal',$dt)->where('regu','whole')->sum('berat');

                    // PARTING
                    $qtypt          = $collect->where('tanggal',$dt)->where('regu','parting')->sum('qty');
                    $bbpt           = $collect->where('tanggal',$dt)->where('regu','parting')->sum('berat');

                    // MARINASI
                    $qtymr          = $collect->where('tanggal',$dt)->where('regu','marinasi')->sum('qty');
                    $bbmr           = $collect->where('tanggal',$dt)->where('regu','marinasi')->sum('berat');

                    // BONELESS
                    $qtybn          = $collect->where('tanggal',$dt)->where('regu','boneless')->sum('qty');
                    $bbbn           = $collect->where('tanggal',$dt)->where('regu','boneless')->sum('berat');

                    // SUB TOTAL HARIAN
                    $qtytt          = $qtywc + $qtypt + $qtymr + $qtybn ;
                    $bbtt           = $bbwc + $bbpt + $bbmr + $bbbn ;

                    // SISA
                    $qtysisa        = $qtythawing - $qtytt;
                    $bbsisa         = $bbthawing - $bbtt;

                }

                $collection[]    = array(
                    'lpah_tanggal_potong'   => $tanggal,
                    'qtythawing'            => $qtythawing,
                    'bbthawing'             => $bbthawing,
                    'qtywc'                 => $qtywc,
                    'bbwc'                  => $bbwc,
                    'qtypt'                 => $qtypt,
                    'bbpt'                  => $bbpt,
                    'qtymr'                 => $qtymr,
                    'bbmr'                  => $bbmr,
                    'qtybn'                 => $qtybn,
                    'bbbn'                  => $bbbn,
                    'qtytt'                 => $qtytt,
                    'bbtt'                  => $bbtt,
                    'qtysisa'               => $qtysisa,
                    'bbsisa'                => $bbsisa
                );
            }

            return view('admin.pages.weekly.component.bb_thawing', compact('data','collection','range_tanggal'));
        } else

        if ($request->key == 'retur') {
            $data   =   Production::select('lpah_tanggal_potong', DB::raw("COUNT(id) AS jumlah_mobil"), DB::raw("SUM(lpah_berat_terima) AS berat_potong"))
                        ->whereBetween('lpah_tanggal_potong', [$mulai, $akhir])
                        ->where('lpah_status', 1)
                        ->groupBy('lpah_tanggal_potong')
                        ->get();

            // $AllData            = DB::select("CALL getHitungProduksiAll('$mulai','$akhir','retur','retur')");
            $AllData            = FreestockList::hitung_produksi_update($mulai,$akhir,'retur');
            $collect            = collect($AllData);

            $range_tanggal      = $this->date_range($mulai, $akhir);

            $collection         = array();
            foreach($range_tanggal as $dt){
                $tanggal        = $dt;

                $tanggal_kosong = true;
                foreach($data as $row){
                if($dt == $row->lpah_tanggal_potong){
                    $tanggal_kosong = false;
                    $tanggal        = $row->lpah_tanggal_potong;

                    // WHOLE CHICKEN
                    $qtywc          = $collect->where('tanggal',$row->lpah_tanggal_potong)->where('regu','whole')->sum('qty');
                    $bbwc           = $collect->where('tanggal',$row->lpah_tanggal_potong)->where('regu','whole')->sum('berat');

                    // PARTING
                    $qtypt          = $collect->where('tanggal',$row->lpah_tanggal_potong)->where('regu','parting')->sum('qty');
                    $bbpt           = $collect->where('tanggal',$row->lpah_tanggal_potong)->where('regu','parting')->sum('berat');

                    // MARINASI
                    $qtymr          = $collect->where('tanggal',$row->lpah_tanggal_potong)->where('regu','marinasi')->sum('qty');
                    $bbmr           = $collect->where('tanggal',$row->lpah_tanggal_potong)->where('regu','marinasi')->sum('berat');

                    // BONELESS
                    $qtybn          = $collect->where('tanggal',$row->lpah_tanggal_potong)->where('regu','boneless')->sum('qty');
                    $bbbn           = $collect->where('tanggal',$row->lpah_tanggal_potong)->where('regu','boneless')->sum('berat');

                    // SUB TOTAL HARIAN
                    $qtytt          = $qtywc + $qtypt + $qtymr + $qtybn ;
                    $bbtt           = $bbwc + $bbpt + $bbmr + $bbbn ;

                }}
                if($tanggal_kosong==true){

                    // WHOLE CHICKEN
                    $qtywc          = $collect->where('tanggal',$dt)->where('regu','whole')->sum('qty');
                    $bbwc           = $collect->where('tanggal',$dt)->where('regu','whole')->sum('berat');

                    // PARTING
                    $qtypt          = $collect->where('tanggal',$dt)->where('regu','parting')->sum('qty');
                    $bbpt           = $collect->where('tanggal',$dt)->where('regu','parting')->sum('berat');

                    // MARINASI
                    $qtymr          = $collect->where('tanggal',$dt)->where('regu','marinasi')->sum('qty');
                    $bbmr           = $collect->where('tanggal',$dt)->where('regu','marinasi')->sum('berat');

                    // BONELESS
                    $qtybn          = $collect->where('tanggal',$dt)->where('regu','boneless')->sum('qty');
                    $bbbn           = $collect->where('tanggal',$dt)->where('regu','boneless')->sum('berat');

                    // SUB TOTAL HARIAN
                    $qtytt          = $qtywc + $qtypt + $qtymr + $qtybn ;
                    $bbtt           = $bbwc + $bbpt + $bbmr + $bbbn ;

                }

                $collection[]    = array(
                    'lpah_tanggal_potong'   => $tanggal,
                    'qtywc'                 => $qtywc,
                    'bbwc'                  => $bbwc,
                    'qtypt'                 => $qtypt,
                    'bbpt'                  => $bbpt,
                    'qtymr'                 => $qtymr,
                    'bbmr'                  => $bbmr,
                    'qtybn'                 => $qtybn,
                    'bbbn'                  => $bbbn,
                    'qtytt'                 => $qtytt,
                    'bbtt'                  => $bbtt
                );
            }
            // dd($collection);
            return view('admin.pages.weekly.component.bb_retur', compact('data','collection','range_tanggal'));
        } else
        if ($request->key == 'beli') {
            $data   =   Production::select('lpah_tanggal_potong', DB::raw("COUNT(id) AS jumlah_mobil"), DB::raw("SUM(lpah_berat_terima) AS berat_potong"))
                        ->whereBetween('lpah_tanggal_potong', [$mulai, $akhir])
                        ->where('lpah_status', 1)
                        ->groupBy('lpah_tanggal_potong')
                        ->get();

            // $AllData            = DB::select("CALL getHitungProduksiAll('$mulai','$akhir','beli','beli')");
            $AllData            = FreestockList::hitung_produksi_update($mulai,$akhir,'beli');
            $AllBahanBakuBeli   = FreestockList::get_all_bahanbaku_beli($mulai,$akhir);

            $collect            = collect($AllData);
            $collectBBBeli      = collect($AllBahanBakuBeli);

            $range_tanggal      = $this->date_range($mulai, $akhir);

            $collection         = array();
            foreach($range_tanggal as $dt){
                $tanggal        = $dt;

                $tanggal_kosong = true;
                foreach($data as $row){
                if($dt == $row->lpah_tanggal_potong){
                    $tanggal_kosong = false;
                    $tanggal        = $row->lpah_tanggal_potong;

                    // BAHAN BAKU BELI
                    $qtybeli        = $collectBBBeli->where('tanggal_produksi',$row->lpah_tanggal_potong)->sum('qty_item');
                    $bbbeli         = $collectBBBeli->where('tanggal_produksi',$row->lpah_tanggal_potong)->sum('berat_item');

                    // WHOLE CHICKEN
                    $qtywc          = $collect->where('tanggal',$row->lpah_tanggal_potong)->where('regu','whole')->sum('qty');
                    $bbwc           = $collect->where('tanggal',$row->lpah_tanggal_potong)->where('regu','whole')->sum('berat');

                    // PARTING
                    $qtypt          = $collect->where('tanggal',$row->lpah_tanggal_potong)->where('regu','parting')->sum('qty');
                    $bbpt           = $collect->where('tanggal',$row->lpah_tanggal_potong)->where('regu','parting')->sum('berat');

                    // MARINASI
                    $qtymr          = $collect->where('tanggal',$row->lpah_tanggal_potong)->where('regu','marinasi')->sum('qty');
                    $bbmr           = $collect->where('tanggal',$row->lpah_tanggal_potong)->where('regu','marinasi')->sum('berat');

                    // BONELESS
                    $qtybn          = $collect->where('tanggal',$row->lpah_tanggal_potong)->where('regu','boneless')->sum('qty');
                    $bbbn           = $collect->where('tanggal',$row->lpah_tanggal_potong)->where('regu','boneless')->sum('berat');

                    // SUB TOTAL HARIAN
                    $qtytt          = $qtywc + $qtypt + $qtymr + $qtybn ;
                    $bbtt           = $bbwc + $bbpt + $bbmr + $bbbn ;

                    // SISA
                    $qtysisa        = $qtybeli - $qtytt;
                    $bbsisa         = $bbbeli - $bbtt;

                }}
                if($tanggal_kosong==true){

                    // BAHAN BAKU BELI
                    $qtybeli        = $collectBBBeli->where('tanggal_produksi',$dt)->sum('qty_item');
                    $bbbeli         = $collectBBBeli->where('tanggal_produksi',$dt)->sum('berat_item');

                    // WHOLE CHICKEN
                    $qtywc          = $collect->where('tanggal',$dt)->where('regu','whole')->sum('qty');
                    $bbwc           = $collect->where('tanggal',$dt)->where('regu','whole')->sum('berat');

                    // PARTING
                    $qtypt          = $collect->where('tanggal',$dt)->where('regu','parting')->sum('qty');
                    $bbpt           = $collect->where('tanggal',$dt)->where('regu','parting')->sum('berat');

                    // MARINASI
                    $qtymr          = $collect->where('tanggal',$dt)->where('regu','marinasi')->sum('qty');
                    $bbmr           = $collect->where('tanggal',$dt)->where('regu','marinasi')->sum('berat');

                    // BONELESS
                    $qtybn          = $collect->where('tanggal',$dt)->where('regu','boneless')->sum('qty');
                    $bbbn           = $collect->where('tanggal',$dt)->where('regu','boneless')->sum('berat');

                    // SUB TOTAL HARIAN
                    $qtytt          = $qtywc + $qtypt + $qtymr + $qtybn ;
                    $bbtt           = $bbwc + $bbpt + $bbmr + $bbbn ;

                    // SISA
                    $qtysisa        = $qtybeli - $qtytt;
                    $bbsisa         = $bbbeli - $bbtt;

                }

                $collection[]    = array(
                    'lpah_tanggal_potong'   => $tanggal,
                    'qtybeli'               => $qtybeli,
                    'bbbeli'                => $bbbeli,
                    'qtywc'                 => $qtywc,
                    'bbwc'                  => $bbwc,
                    'qtypt'                 => $qtypt,
                    'bbpt'                  => $bbpt,
                    'qtymr'                 => $qtymr,
                    'bbmr'                  => $bbmr,
                    'qtybn'                 => $qtybn,
                    'bbbn'                  => $bbbn,
                    'qtytt'                 => $qtytt,
                    'bbtt'                  => $bbtt,
                    'qtysisa'               => $qtysisa,
                    'bbsisa'                => $bbsisa
                );
            }
            // dd($collection);

            return view('admin.pages.weekly.component.bb_beli', compact('data','collection','range_tanggal'));
        }else

        if ($request->key == 'stock_frozen') {
            $data   =   VWStockProduksiFrozen::whereBetween('tanggal', [$mulai,$akhir])->get();
            $item   =   Item::whereIn('id', FreestockTemp::select('item_id')->whereIn('freestock_id', Freestock::select('id')->where('regu', 'frozen')->whereBetween('tanggal', [$mulai,$akhir])))->where('nama','like', '% karkas %')->groupBy('nama')->orderBy('id')->get();
            $detail =   VWStockTempFrozen::whereBetween('tanggal', [$mulai,$akhir])->get();

            $range_tanggal = $this->date_range($mulai, $akhir);


            return view('admin.pages.weekly.component.stock_frozen', compact('data', 'detail', 'item', 'range_tanggal'));
        }

        elseif ($request->key == 'grafik_pemotongan') {
            $data =   Production::select('lpah_tanggal_potong', DB::raw('count(id) as count'))->whereBetween('lpah_tanggal_potong', [$mulai, $akhir])->where('sc_status', 1)->groupBy('lpah_tanggal_potong')->get();
            $berat      =   '' ;
            $tgl =   '[' ;
            foreach ($data as $row) {
                $berat      .=   $row->count . ",";
                $tgl .=  "'" . (string)date('d-M', strtotime($row->lpah_tanggal_potong)) . "'," ;
            }
            $tgl     .=  ']';

            $list    =   "[" ;
            $list    .=  "{name: 'Jumlah Pemotongan',data: [" . $berat . "]},";
            $list    .=  "]";

            return view('admin.pages.weekly.component.grafik_pemotongan', compact('tgl','list'));
        } elseif ($request->key == 'grafik_parting') {
            $data   =   Freestock::join('free_stocktemp', 'free_stock.id', '=', 'free_stocktemp.freestock_id')->where('status', '3')->whereIn('free_stock.regu',['parting','marinasi'])->whereBetween('free_stock.tanggal',[$mulai,$akhir])->select('free_stock.tanggal', DB::raw('Sum(free_stocktemp.berat) as berat'))->groupBy('free_stock.tanggal')->get();
            $berat  =   '' ;
            $tgl =   '[' ;
            foreach ($data as $row) {
                $berat      .=   $row->berat . ",";
                $tgl .=  "'" . (string)date('d-M', strtotime($row->tanggal)) . "'," ;
            }
            $tgl     .=  ']';

            $list    =   "[" ;
            $list    .=  "{name: 'Total Parting',data: [" . $berat . "]},";
            $list    .=  "]";

            return view('admin.pages.weekly.component.grafik_parting', compact('tgl','list'));
        }

        elseif ($request->key == 'grafik_whole') {
            $data   =   Freestock::join('free_stocktemp', 'free_stock.id', '=', 'free_stocktemp.freestock_id')->where('status', '3')->whereIn('free_stock.regu',['whole'])->whereBetween('free_stock.tanggal',[$mulai,$akhir])->select('free_stock.tanggal', DB::raw('Sum(free_stocktemp.berat) as berat'))->groupBy('free_stock.tanggal')->get();
            $berat  =   '' ;
            $tgl =   '[' ;
            foreach ($data as $row) {
                $berat      .=   $row->berat . ",";
                $tgl .=  "'" . (string)date('d-M', strtotime($row->tanggal)) . "'," ;
            }
            $tgl     .=  ']';

            $list    =   "[" ;
            $list    .=  "{name: 'Total Whole',data: [" . $berat . "]},";
            $list    .=  "]";

            return view('admin.pages.weekly.component.grafik_whole', compact('tgl','list'));
        }
        elseif ($request->key == 'grafik_boneless') {
            $data   =   Freestock::join('free_stocktemp', 'free_stock.id', '=', 'free_stocktemp.freestock_id')->where('status', '3')->whereIn('free_stock.regu',['boneless'])->whereBetween('free_stock.tanggal',[$mulai,$akhir])->select('free_stock.tanggal', DB::raw('Sum(free_stocktemp.berat) as berat'))->groupBy('free_stock.tanggal')->get();
            $berat  =   '' ;
            $tgl =   '[' ;
            foreach ($data as $row) {
                $berat      .=   $row->berat . ",";
                $tgl .=  "'" . (string)date('d-M', strtotime($row->tanggal)) . "'," ;
            }
            $tgl     .=  ']';

            $list    =   "[" ;
            $list    .=  "{name: 'Total Boneless',data: [" . $berat . "]},";
            $list    .=  "]";

            return view('admin.pages.weekly.component.grafik_boneless', compact('tgl','list'));
        }
        elseif ($request->key == 'grafik_frozen') {
            $data   =   Freestock::join('free_stocktemp', 'free_stock.id', '=', 'free_stocktemp.freestock_id')->where('status', '3')->whereIn('free_stock.regu',['frozen'])->whereBetween('free_stock.tanggal',[$mulai,$akhir])->select('free_stock.tanggal', DB::raw('Sum(free_stocktemp.berat) as berat'))->groupBy('free_stock.tanggal')->get();
            $berat  =   '' ;
            $tgl =   '[' ;
            foreach ($data as $row) {
                $berat      .=   $row->berat . ",";
                $tgl .=  "'" . (string)date('d-M', strtotime($row->tanggal)) . "'," ;
            }
            $tgl     .=  ']';

            $list    =   "[" ;
            $list    .=  "{name: 'Total Frozen',data: [" . $berat . "]},";
            $list    .=  "]";

            return view('admin.pages.weekly.component.grafik_frozen', compact('tgl','list'));
        }
        elseif ($request->key == 'grafik_total') {
            $data   =   Freestock::join('free_stocktemp', 'free_stock.id', '=', 'free_stocktemp.freestock_id')->where('status', '3')->whereBetween('free_stock.tanggal',[$mulai,$akhir])->select('free_stock.tanggal', DB::raw('Sum(free_stocktemp.berat) as berat'))->groupBy('free_stock.tanggal')->get();
            $berat  =   '' ;
            $tgl =   '[' ;
            foreach ($data as $row) {
                $berat      .=   $row->berat . ",";
                $tgl .=  "'" . (string)date('d-M', strtotime($row->tanggal)) . "'," ;
            }
            $tgl     .=  ']';

            $list    =   "[" ;
            $list    .=  "{name: 'Total Produksi',data: [" . $berat . "]},";
            $list    .=  "]";

            return view('admin.pages.weekly.component.grafik_total', compact('tgl','list'));
        }

        elseif ($request->key == 'perbandingan_mingguan') {

            $pengurangan    = 6;
            $data           = [];

            for($no=0; $no<4 ; $no++){
                $tanggal_akhir  = $akhir;
                $tanggal_awal   = date('Y-m-d', strtotime("-6 Day", strtotime($akhir)));

                $mulai      = $tanggal_awal;
                $akhir      = $tanggal_akhir;

                $total_potong           =   Production::select(DB::raw('count(id) as total_potong'))->whereBetween('lpah_tanggal_potong', [$mulai, $akhir])->where('lpah_status', 1)->first();
                $total_parting          =   Freestock::join('free_stocktemp', 'free_stock.id', '=', 'free_stocktemp.freestock_id')->where('status', '3')->whereIn('free_stock.regu',['parting'])->whereBetween('free_stock.tanggal',[$mulai,$akhir])->select('free_stock.tanggal', DB::raw('Sum(free_stocktemp.berat) as berat'))->first();
                $total_marinasi         =   Freestock::join('free_stocktemp', 'free_stock.id', '=', 'free_stocktemp.freestock_id')->where('status', '3')->whereIn('free_stock.regu',['marinasi'])->whereBetween('free_stock.tanggal',[$mulai,$akhir])->select('free_stock.tanggal', DB::raw('Sum(free_stocktemp.berat) as berat'))->first();
                $total_whole            =   Freestock::join('free_stocktemp', 'free_stock.id', '=', 'free_stocktemp.freestock_id')->where('status', '3')->whereIn('free_stock.regu',['whole'])->whereBetween('free_stock.tanggal',[$mulai,$akhir])->select('free_stock.tanggal', DB::raw('Sum(free_stocktemp.berat) as berat'))->first();
                $total_frozen           =   Freestock::join('free_stocktemp', 'free_stock.id', '=', 'free_stocktemp.freestock_id')->where('status', '3')->whereIn('free_stock.regu',['frozen'])->whereBetween('free_stock.tanggal',[$mulai,$akhir])->select('free_stock.tanggal', DB::raw('Sum(free_stocktemp.berat) as berat'))->first();
                $sampingan              =   Bahanbaku::whereIn('order_item_id', OrderItem::select('id')->whereIn('chiller_out', Chiller::select('id')->whereBetween('tanggal_produksi', [$mulai,$akhir])))
                ->whereIn('order_item_id', OrderItem::select('id')->whereIn('item_id', Item::select('id')->where('category_id', 1)))
                ->where('proses_ambil', 'sampingan')
                ->sum('bb_berat');

                $summary = array(
                    'tanggal'       => date('d M Y', strtotime($mulai))." - ".date('d M Y', strtotime($akhir)),
                    'total_potong'  => $total_potong->total_potong,
                    'whole'         => $total_whole->berat,
                    'parting'       => $total_parting->berat,
                    'marinasi'      => $total_marinasi->berat,
                    'frozen'        => $total_frozen->berat,
                    'sampingan'     => $sampingan
                );

                $data[]     = $summary;
                $akhir      = date('Y-m-d', strtotime("-1 Day", strtotime($tanggal_awal)));

            }

            $data_range     = $data;
            return view('admin.pages.weekly.component.perbandingan_mingguan', compact(['data_range']));
        }
        else if($request->key == "hasilKey"){
            $tanggal        = $request->tanggal;
            $asalTujuan     = $request->asalTujuan;
            $jenis          = $request->jenis;
            $regu           = $request->regu;

            $data           = DB::select("call getHitungProduksi('$tanggal', '$regu', '$asalTujuan', '$jenis')");
            return view('admin.pages.weekly.modal.bahan_baku_lama',compact(['data','regu']));
        }
        else {

            $range_tanggal = $this->date_range($mulai, $akhir);
            return view('admin.pages.weekly.index', compact(['range_tanggal']));
        }

    }

    public function export_weekly(Request $request){

        $html = $request->html;
        $file = $request->filename;

        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=$file");
        echo $html;
    }

    function date_range($first, $last, $step = '+1 day', $output_format = 'Y-m-d' ) {

        $dates = array();
        $current = strtotime($first);
        $last = strtotime($last);

        while( $current <= $last ) {

            $dates[] = date($output_format, $current);
            $current = strtotime($step, $current);
        }

        return $dates;
    }

}
