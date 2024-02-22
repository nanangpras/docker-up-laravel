<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bahanbaku;
use App\Models\Chiller;
use App\Models\Evis;
use App\Models\Grading;
use App\Models\Item;
use App\Models\Production;
use App\Models\Abf;
use App\Models\Customer;
use App\Models\Ekspedisi;
use App\Models\Ekspedisi_rute;
use App\Models\Freestock;
use App\Models\FreestockList;
use App\Models\FreestockTemp;
use App\Models\Lpah;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product_gudang;
use App\Models\Purchasing;
use App\Models\Retur;
use App\Models\ReturItem;
use App\Models\Supplier;
use App\Models\VWGrafikRetur;
use App\Classes\Applib;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\Cast\String_;

class ReportController extends Controller
{
    public function invoice($id)
    {
        $data   =   Order::where('id', $id)
            ->where('status', '>', 4)
            ->first();

        if ($data) {
            $pdf    =   App::make('dompdf.wrapper');
            $pdf->loadHTML(view('admin.pages.invoice', compact('data')));
            return $pdf->stream();
        }

        return redirect()->route('dashboard');
    }

    public function invoiceblank()
    {
        return redirect()->route('dashboard');
    }

    public function purchasedelivered(Request $request)
    {
        // dd(Applib::DefaultTanggalAudit());
        $tanggal_awal   =   $request->tanggal_awal ?? date('Y-m-d');
        $tanggal_akhir  =   $request->tanggal_akhir ?? date('Y-m-d');
        $tanggal        =   $tanggal_akhir;

        if ($request->key == 'pageSatu') {


            $ekor_mati          =   Production::whereIn('purchasing_id', Purchasing::select('id')->where('jenis_po', 'PO LB')->where('created_at','>=',Applib::DefaultTanggalAudit()))
                                    ->whereBetween('lpah_tanggal_potong', [$tanggal_awal, $tanggal_akhir])
                                    ->where('lpah_status', '1')
                                    ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                    ->sum('qc_ekor_ayam_mati');

            $do_tangkap         =   Production::whereIn('purchasing_id', Purchasing::select('id')->where('jenis_po', 'PO LB')->where('created_at','>=',Applib::DefaultTanggalAudit()))
                                    ->whereBetween('lpah_tanggal_potong', [$tanggal_awal, $tanggal_akhir])
                                    ->where('po_jenis_ekspedisi', 'tangkap')
                                    ->where('no_urut', '!=', NULL)
                                    ->where('grading_status', '1')
                                    ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                    ->sum('sc_berat_do');

            $grading_tangkap    =   Grading::whereIn('trans_id', Production::select('id')->whereIn('purchasing_id', Purchasing::select('id')->where('jenis_po', 'PO LB')->where('po_jenis_ekspedisi', 'tangkap')->where('created_at','>=',Applib::DefaultTanggalAudit()))
                                    ->whereBetween('lpah_tanggal_potong', [$tanggal_awal, $tanggal_akhir])->where('grading_status', '1'))
                                    ->select(DB::raw("SUM(total_item) AS ekor"), DB::raw("SUM(berat_item) AS berat"), DB::raw("(SUM(berat_item) / SUM(total_item)) AS ratarata"))
                                    ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                    ->first();

            $lpah_kirim         =   Production::whereIn('purchasing_id', Purchasing::select('id')->where('jenis_po', 'PO LB')->where('created_at','>=',Applib::DefaultTanggalAudit()))
                                    ->whereBetween('lpah_tanggal_potong', [$tanggal_awal, $tanggal_akhir])
                                    ->where('po_jenis_ekspedisi', 'kirim')
                                    ->where('no_urut', '!=', NULL)
                                    ->where('grading_status', '1')
                                    ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                    ->sum('lpah_berat_terima');

            $grading_kirim      =   Grading::whereIn('trans_id', Production::select('id')->whereIn('purchasing_id', Purchasing::select('id')->where('jenis_po', 'PO LB')->where('po_jenis_ekspedisi', 'kirim'))->whereBetween('lpah_tanggal_potong', [$tanggal_awal, $tanggal_akhir])->where('grading_status', '1')->where('created_at','>=',Applib::DefaultTanggalAudit()))
                                    ->select(DB::raw("SUM(total_item) AS ekor"), DB::raw("SUM(berat_item) AS berat"), DB::raw("(SUM(berat_item) / SUM(total_item)) AS ratarata"))
                                    ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                    ->first();

            $mobil_kirim        =   Production::whereIn('purchasing_id', Purchasing::select('id')->where('jenis_po', 'PO LB')->where('created_at','>=',Applib::DefaultTanggalAudit()))
                                    ->whereBetween('lpah_tanggal_potong', [$tanggal_awal, $tanggal_akhir])
                                    ->where('po_jenis_ekspedisi', 'kirim')
                                    ->where('no_urut', '!=', NULL)
                                    ->where('grading_status', '1')
                                    ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                    ->count();

            $mobil_tangkap      =   Production::whereIn('purchasing_id', Purchasing::select('id')->where('jenis_po', 'PO LB')->where('created_at','>=',Applib::DefaultTanggalAudit()))
                                    ->whereBetween('lpah_tanggal_potong', [$tanggal_awal, $tanggal_akhir])
                                    ->where('po_jenis_ekspedisi', 'tangkap')
                                    ->where('no_urut', '!=', NULL)
                                    ->where('grading_status', '1')
                                    ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                    ->count();

            $terima_rpa         =   Production::whereIn('purchasing_id', Purchasing::select('id')->where('jenis_po', 'PO LB')->where('created_at','>=',Applib::DefaultTanggalAudit()))->whereBetween('lpah_tanggal_potong', [$tanggal_awal, $tanggal_akhir])
                                    // ->select(
                                    //     DB::raw("SUM(ekoran_seckle) AS seckle"),
                                    //     DB::raw("SUM(berat_bersih_lpah) AS kg_terima")
                                    // )
                                    ->where('grading_status', '1')
                                    ->where('evis_status', '1')
                                    ->where('no_urut', '!=', NULL)
                                    ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                    ->get();

            $grading_rpa        =   Grading::whereIn('trans_id', Production::select('id')->whereIn('purchasing_id', Purchasing::select('id')->where('jenis_po', 'PO LB')->where('created_at','>=',Applib::DefaultTanggalAudit()))
                                    ->whereBetween('lpah_tanggal_potong', [$tanggal_awal, $tanggal_akhir])
                                    ->where('grading_status', '1')
                                    ->where('evis_status', '1'))
                                    ->select(DB::raw("SUM(total_item) AS ekor"), DB::raw("SUM(berat_item) AS berat"), DB::raw("(SUM(berat_item) / SUM(total_item)) AS ratarata"))
                                    ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                    ->first();

            $evis_rpa           =   Evis::whereIn('production_id', Production::select('id')->whereIn('purchasing_id', Purchasing::select('id')->where('jenis_po', 'PO LB')->where('created_at','>=',Applib::DefaultTanggalAudit()))
                                    ->whereBetween('lpah_tanggal_potong', [$tanggal_awal, $tanggal_akhir])
                                    ->where('grading_status', '1')
                                    ->where('evis_status', '1'))
                                    ->select(DB::raw(DB::raw("SUM(berat_item) AS berat")))
                                    ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                    ->first();


            $susut_tangkap      =   Production::whereIn('purchasing_id', Purchasing::select('id')->where('jenis_po', 'PO LB')->where('created_at','>=',Applib::DefaultTanggalAudit()))
                                    ->whereBetween('lpah_tanggal_potong', [$tanggal_awal, $tanggal_akhir])
                                    ->select(DB::raw("((SUM(sc_berat_do)-SUM(lpah_berat_terima)) / SUM(sc_berat_do)) * 100 AS susut"))
                                    ->where('po_jenis_ekspedisi', 'tangkap')
                                    ->where('no_urut', '!=', NULL)
                                    ->where('grading_status', '1')
                                    ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                    ->first();



            $susut_kirim        =   Production::whereIn('purchasing_id', Purchasing::select('id')->where('jenis_po', 'PO LB')->where('created_at','>=',Applib::DefaultTanggalAudit()))
                                    ->whereBetween('lpah_tanggal_potong', [$tanggal_awal, $tanggal_akhir])
                                    ->select(DB::raw("((SUM(sc_berat_do)-SUM(lpah_berat_terima)) / SUM(sc_berat_do)) * 100 AS susut"))
                                    ->where('po_jenis_ekspedisi', 'kirim')
                                    ->where('no_urut', '!=', NULL)
                                    ->where('grading_status', '1')
                                    ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                    ->first();

            $seckle             =   Production::whereIn('purchasing_id', Purchasing::select('id')->where('jenis_po', 'PO LB')->where('created_at','>=',Applib::DefaultTanggalAudit()))
                                    ->whereBetween('lpah_tanggal_potong', [$tanggal_awal, $tanggal_akhir])
                                    ->select(DB::raw("(SUM(sc_ekor_do) - SUM(ekoran_seckle)) AS seckle"))
                                    ->where('no_urut', '!=', NULL)
                                    ->where('grading_status', '1')
                                    ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                    ->first();

            $data_grading       =   Grading::whereIn('trans_id', Production::select('id')->whereIn('purchasing_id', Purchasing::select('id')->where('jenis_po', 'PO LB')->where('created_at','>=',Applib::DefaultTanggalAudit()))
                                    ->whereBetween('lpah_tanggal_potong', [$tanggal_awal, $tanggal_akhir]))
                                    ->select(DB::raw("SUM(total_item) AS ekor"), DB::raw("SUM(berat_item) AS berat"), DB::raw("(SUM(berat_item) / SUM(total_item)) AS ratarata"))
                                    ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                    ->first();

            $count_purchase     =   Purchasing::where('jenis_po', 'PO LB')->whereBetween('tanggal_potong', [$tanggal_awal, $tanggal_akhir])
                                    ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                    ->count();
            $count_production   =   Production::whereIn('purchasing_id', Purchasing::select('id')->where('jenis_po', 'PO LB')->whereBetween('tanggal_potong', [$tanggal_awal, $tanggal_akhir])->where('created_at','>=',Applib::DefaultTanggalAudit()))
                                    ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                    ->count();

            $data_diproses      =   Production::whereIn('purchasing_id', Purchasing::select('id')->where('jenis_po', 'PO LB')->where('created_at','>=',Applib::DefaultTanggalAudit()))
                                    ->whereBetween('lpah_tanggal_potong', [$tanggal_awal, $tanggal_akhir])->where('grading_status', 1)
                                    ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                    ->count();
            $data_po_pending    =   Production::whereIn('purchasing_id', Purchasing::select('id')->where('jenis_po', 'PO LB')->where('created_at','>=',Applib::DefaultTanggalAudit()))
                                    ->whereBetween('lpah_tanggal_potong', [$tanggal_awal, $tanggal_akhir])->where('prod_pending', '1')
                                    ->whereIn('grading_status', [1,2])
                                    ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                    ->count();

            $karkas             =   Item::where('category_id', 1)->get();

            $ayam_utuh          =   FreestockTemp::whereIn('freestock_id', Freestock::select('id')->whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])->where('status', 3)->where('regu', 'whole')->where('created_at','>=',Applib::DefaultTanggalAudit()))
                                    ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                    ->sum('berat');
            $parting            =   FreestockTemp::whereIn('freestock_id', Freestock::select('id')->whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])->where('status', 3)->where('regu', 'parting')->where('created_at','>=',Applib::DefaultTanggalAudit()))
                                    ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                    ->sum('berat');
            $parting_mari       =   FreestockTemp::whereIn('freestock_id', Freestock::select('id')->whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])->where('status', 3)->where('regu', 'marinasi')->where('created_at','>=',Applib::DefaultTanggalAudit()))
                                    ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                    ->sum('berat');
            $boneless           =   FreestockTemp::whereIn('freestock_id', Freestock::select('id')->whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])->where('status', 3)->where('regu', 'boneless')->where('created_at','>=',Applib::DefaultTanggalAudit()))
                                    ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                    ->sum('berat');
            $frozen             =   FreestockTemp::whereIn('freestock_id', Freestock::select('id')->whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])->where('status', 3)->where('regu', 'frozen')->where('created_at','>=',Applib::DefaultTanggalAudit()))
                                    ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                    ->sum('berat');

            $data_product       =   Production::whereIn('purchasing_id', Purchasing::select('id')->where('jenis_po', 'PO LB')->where('created_at','>=',Applib::DefaultTanggalAudit()))
                                    ->whereBetween('lpah_tanggal_potong', [$tanggal_awal, $tanggal_akhir])
                                    ->select(
                                        DB::raw("SUM(sc_ekor_do) AS ekor"),
                                        DB::raw("SUM(sc_berat_do) AS berat"),
                                        DB::raw("(SUM(sc_berat_do) / SUM(sc_ekor_do)) AS rerata"),
                                        DB::raw("SUM(ekoran_seckle) AS seckle"),
                                        DB::raw("SUM(lpah_berat_terima) AS kg_terima"),
                                        DB::raw("(SUM(lpah_berat_terima) / SUM(ekoran_seckle)) AS rata_terima"),
                                        DB::raw("SUM(lpah_berat_susut) AS susut_berat"),
                                        DB::raw("SUM(qc_ekor_ayam_mati) AS susut_ekor")
                                    )
                                    ->where('no_urut', '!=', NULL)
                                    ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                    ->first();

            $berat_rpa                  = 0;
            $ekor_rpa                   = 0;
            if($terima_rpa){
                foreach ($terima_rpa as $terimaRPA) {
                    // dd($terimaRPA->berat_bersih_lpah);
                    $berat_rpa              += $terimaRPA->lpah_berat_terima;
                    $ekor_rpa               += $terimaRPA->ekoran_seckle;
                }
            }

            $berat_grading              = 0;
            $ekor_grading               = 0;

            if($grading_rpa){
                $berat_grading          = $grading_rpa->berat;
                $ekor_grading           = $grading_rpa->ekor;
            }

            $rendemen_kirim             = 0;
            $rendemen_tangkap           = 0;

            if ($do_tangkap) {
                $rendemen_tangkap       =   (($grading_tangkap->berat / $do_tangkap) * 100);
            }

            if ($lpah_kirim) {
                $rendemen_kirim         =  (($grading_kirim->berat / $lpah_kirim) * 100);
            };

            $berat_evis                 = 0;
            if($evis_rpa){
                $berat_evis             = $evis_rpa->berat;
            }

            $data           =   [
                'data_production'   =>  $data_product,
                'data_grading'      =>  $data_grading,
                'data_diproses'     =>  $data_diproses,
                'data_po_pending'   =>  $data_po_pending,
                'ekor_rpa'          =>  $ekor_rpa,
                'ekor_grading'      =>  $ekor_grading,
                'berat_rpa'         =>  $berat_rpa,
                'berat_grading'     =>  $berat_grading,
                'berat_evis'        =>  $berat_evis,
                'ayam_utuh'         =>  $ayam_utuh,
                'parting'           =>  $parting,
                'parting_marinasi'  =>  $parting_mari,
                'boneless'          =>  $boneless,
                'frozen'            =>  $frozen,
                'tanggal'           =>  $tanggal,
                'susut_tangkap'     =>  $susut_tangkap->susut,
                'susut_kirim'       =>  $susut_kirim->susut,
                'seckle'            =>  $seckle->seckle,
                'count_purchase'    =>  $count_purchase,
                'count_production'  =>  $count_production,
                'rendemen_kirim'    =>  $rendemen_kirim,
                'rendemen_tangkap'  =>  $rendemen_tangkap,
                'mobil_tangkap'     =>  $mobil_tangkap,
                'mobil_kirim'       =>  $mobil_kirim,
            ];

            $data_grading = [];
            $arr    =   '[';
            foreach ($karkas as $item) {
                if (Grading::sebaran_karkas($item->id, $tanggal_awal, $tanggal_akhir)) {
                    $arr    .=  "['" . $item->nama . "', " . Grading::sebaran_karkas($item->id, $tanggal_awal, $tanggal_akhir) . "],";
                    $data_grading[] = array(
                        $item->nama,
                        Grading::sebaran_karkas($item->id, $tanggal_awal, $tanggal_akhir)
                        );
                    }
                }
            $arr    .=  ']';

            // ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

            return view('admin.pages.laporan.laporan_dashboard_per_page.pageSatu', compact('data', 'data_grading', 'tanggal', 'tanggal_awal', 'tanggal_akhir', 'arr', 'ekor_mati'));

        } else if ($request->key == 'pageDua') {

            $returnonkualitas           = ReturItem::whereIn('retur_id', Retur::select('id')->whereBetween(DB::raw('DATE(tanggal_retur)'), [$tanggal_awal, $tanggal_akhir])->where('created_at','>=',Applib::DefaultTanggalAudit()))
                                            ->where('kategori', 'Non Kualitas')
                                            ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                            ->sum('berat');
            $returkualitas              = ReturItem::whereIn('retur_id', Retur::select('id')->whereBetween(DB::raw('DATE(tanggal_retur)'), [$tanggal_awal, $tanggal_akhir])->where('created_at','>=',Applib::DefaultTanggalAudit()))
                                            ->where('kategori', 'Kualitas')
                                            ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                            ->sum('berat');
            $returcolumn                = ReturItem::select('penanganan',DB::raw("sum(berat) as berat"))->whereIn('retur_id', Retur::select('id')->whereBetween(DB::raw('DATE(tanggal_retur)'),[$tanggal_awal, $tanggal_akhir])
                                                ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                                ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                                ->groupBy('tanggal_retur'))
                                            ->groupBy('penanganan')->get();
            $returAlasan                = ReturItem::select('catatan',DB::raw("sum(berat) as berat"))->whereIn('retur_id', Retur::select('id')->whereBetween(DB::raw('DATE(tanggal_retur)'),[$tanggal_awal, $tanggal_akhir])
                                                ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                                ->groupBy('tanggal_retur'))
                                            ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                            ->groupBy('catatan')->get();
            $retur_percent              = [];
            $retur_alasan               = [];

            foreach ($returcolumn as $ret) {
                $retur_percent[] = array(
                    'name'  => $ret->penanganan,
                    'y'     => intval($ret->berat)
                );
            }
            foreach ($returAlasan as $key => $value) {
                $retur_alasan[] = array(
                    'name'  => $value->catatan,
                    'y'     =>  intval($value->berat)
                );
            }

            $retur_percent    =   [];
            foreach ($returcolumn as $ret) {
                $retur_percent[] = array(
                    'name'  => $ret->penanganan,
                    'y'     => intval($ret->berat)
                );
            }
            $cloneDataSebaranKarkas           = Grading::whereIn('trans_id', Production::select('id')->whereIn('purchasing_id', Purchasing::select('id')->where('jenis_po', 'PO LB')->where('created_at','>=',Applib::DefaultTanggalAudit()))->whereBetween('lpah_tanggal_potong', [$tanggal_awal, $tanggal_akhir])->where('created_at','>=',Applib::DefaultTanggalAudit()))
                                                ->join('productions', 'productions.id', '=', 'grading.trans_id')->join('purchasing', 'purchasing.id', '=', 'productions.purchasing_id')
                                               ->join('supplier', 'supplier.id', '=', 'purchasing.supplier_id');
            $cloneSebaranKarkas             = clone $cloneDataSebaranKarkas;
            $cloneGroupByItem               = clone $cloneDataSebaranKarkas;
            $cloneGetDataTotal              = clone $cloneDataSebaranKarkas;

            $groupByItem                    = $cloneGroupByItem
                                            ->where('grading.created_at','>=',Applib::DefaultTanggalAudit())
                                            ->groupBy('grading.item_id')->pluck('grading.item_id');
            $dataSebaranKarkas              = $cloneSebaranKarkas->select('supplier.nama AS nama_supplier', 'supplier.id AS id_supplier')
                                                ->where('grading.created_at','>=',Applib::DefaultTanggalAudit())
                                                ->groupBy('supplier.id')
                                                ->get();

            $getDataSupplier = [];
            foreach($groupByItem as $dataItem) {
                $getDataSupplier[]          = Grading::sebaran_karkas_supplier($dataSebaranKarkas, $dataItem, $tanggal_awal, $tanggal_akhir);
            }

            // $supplier                       =   '[' ;
            // $dataKarkasSupplier             = $dataSebaranKarkas->pluck('nama_supplier');
            // for($i = 0; $i < count($getDataSupplier); $i ++) {
            //     $supplier                   .=  "{name: '". Item::where('id', $groupByItem[$i])->first()->nama ."',";
            //     $supplier                   .=  "data: [";
            //         for($x = 0; $x < count($dataKarkasSupplier); $x ++) {
            //             $supplier           .= isset($getDataSupplier[$i][$x]['berat_grading']) ? $getDataSupplier[$i][$x]['berat_grading']."," : '0'.",";
            //         }
            //     $supplier                   .=  "]},";
            // }
            // $supplier                       .=  ']';


            $supplier                       =   [] ;
            $getDataTotal                   =   [] ;

            $dataKarkasSupplier             = $dataSebaranKarkas->pluck('nama_supplier');


            foreach ($dataSebaranKarkas as $key => $dataKarkas) {
                $getDataTotal[]   =  Production::whereBetween('lpah_tanggal_potong', [$tanggal_awal, $tanggal_akhir])
                                        ->join('purchasing', 'purchasing.id', '=', 'productions.purchasing_id')
                                        ->join('supplier', 'supplier.id', '=', 'purchasing.supplier_id')
                                        ->select(DB::raw("SUM(ekoran_seckle) AS ekoran_seckle"))->where('supplier.id', $dataKarkas->id_supplier)
                                        ->where('productions.created_at','>=',Applib::DefaultTanggalAudit())
                                        ->first()->ekoran_seckle;

            }


            for($i = 0; $i < count($getDataSupplier); $i ++) {

                $data_grading   = [];
                for($x = 0; $x < count($dataKarkasSupplier); $x ++) {
                    $data_grading[] = isset($getDataSupplier[$i][$x]['qty_grading']) ? (integer)$getDataSupplier[$i][$x]['qty_grading'] / $getDataTotal[$x] * 100 : 0;
                }

                $supplier[] = array(
                    'name' => Item::where('id', $groupByItem[$i])->first()->nama,
                    'data' => $data_grading
                );
            }

            $supplier = json_encode($supplier);

            // dd($getDataTotal, $dataKarkasSupplier, $getDataSupplier);


            // ------------------------------------------------------------------------------------------------------------------------------------------------------------------

            $querygetUkuranAyam                  = Purchasing::select('ukuran_ayam')->whereBetween('tanggal_potong', [$tanggal_awal, $tanggal_akhir])->where('type_po', 'PO LB')
                                                    ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                                    ->groupBy('ukuran_ayam')->get();
            $getTanggalPotongChartLB             = Purchasing::select('tanggal_potong')->groupBy('tanggal_potong')->where('type_po', 'PO LB')->whereBetween('tanggal_potong', [$tanggal_awal, $tanggal_akhir])
                                                    ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                                    ->orderBy('tanggal_potong', 'asc')->pluck('tanggal_potong');
            $chartLB                             =   '[' ;
            foreach ($querygetUkuranAyam as $key => $forChartLB) {
                $chartLB                        .=  "{name: '". $querygetUkuranAyam[$key]['ukuran_ayam'] ."',";
                $chartLB                        .=  "data: [";
                for($u = 0; $u < count($getTanggalPotongChartLB); $u ++) {
                    $queryGetAverageHarga        = Purchasing::select(DB::raw('round(AVG(harga_deal),0) as harga_deal'))->where('tanggal_potong', $getTanggalPotongChartLB[$u])->where('ukuran_ayam', $querygetUkuranAyam[$key]['ukuran_ayam'])->where('type_po', 'PO LB')
                                                    ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                                    ->groupBy('ukuran_ayam')->first();

                    $chartLB                    .= isset($queryGetAverageHarga->harga_deal) ?  $queryGetAverageHarga->harga_deal. "," : '0'.",";

                }
                $chartLB                        .=  "]},";
            }
            $chartLB                            .=  ']';

            // dd($getTanggalPotongChartLB, $querygetUkuranAyam, $chartLB);


            return view('admin.pages.laporan.laporan_dashboard_per_page.pageDua', compact('tanggal', 'tanggal_awal', 'tanggal_akhir', 'retur_percent', 'returkualitas', 'returnonkualitas', 'supplier', 'dataKarkasSupplier', 'getTanggalPotongChartLB', 'chartLB','retur_alasan'));

        } else if ($request->key == 'pageTiga') {
            $retur          =   ReturItem::select('retur_item.item_id', DB::raw("SUM(berat) AS kg"), DB::raw("SUM(qty) AS total"))
                                ->leftJoin('retur', 'retur.id', '=', 'retur_item.retur_id')
                                ->whereBetween(DB::raw('DATE(tanggal_retur)'), [$tanggal_awal, $tanggal_akhir])
                                ->where('retur.status', '!=', 3)
                                ->where('retur_item.created_at','>=',Applib::DefaultTanggalAudit())
                                ->where('retur.created_at','>=',Applib::DefaultTanggalAudit())
                                ->groupBy('item_id')
                                ->get();

            $produksi_evis   =   Evis::select(DB::raw("SUM(berat_item) AS total"), 'item_id')
                                ->where(function ($query) use ($tanggal_awal, $tanggal_akhir) {
                                    if ($tanggal_awal and $tanggal_akhir) {
                                        $query->whereBetween('tanggal_potong', [$tanggal_awal, $tanggal_akhir]);
                                    }
                                })
                                ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                ->groupBy('item_id')
                                ->get();

            return view('admin.pages.laporan.laporan_dashboard_per_page.pageTiga', compact('tanggal', 'tanggal_awal', 'tanggal_akhir', 'retur', 'produksi_evis'));

        } else if ($request->key == 'pageEmpat') {


            // TESTING DASHBOARD 1 function SAJA

            $getDataBonelessWOBB        =   Freestock::getDataWONONWO('boneless', $tanggal_awal, $tanggal_akhir, 'getDataWOBB', 'getDataWOFG','getDataNONWOBB', 'getDataNONWOFG');
            // $getDataBonelessWOFG        =   Freestock::getDataWONONWO('boneless', $tanggal_awal, $tanggal_akhir, 'getDataWOFG');
            // $getDataBonelessNONWOBB     =   Freestock::getDataWONONWO('boneless', $tanggal_awal, $tanggal_akhir, 'getDataNONWOBB');
            // $getDataBonelessNONWOFG     =   Freestock::getDataWONONWO('boneless', $tanggal_awal, $tanggal_akhir, 'getDataNONWOFG');
            
            $getDataPartingWOBB         =   Freestock::getDataWONONWO('parting', $tanggal_awal, $tanggal_akhir, 'getDataWOBB', 'getDataWOFG','getDataNONWOBB', 'getDataNONWOFG');
            // $getDataPartingWOFG         =   Freestock::getDataWONONWO('parting', $tanggal_awal, $tanggal_akhir, 'getDataWOFG');
            // $getDataPartingNONWOBB      =   Freestock::getDataWONONWO('parting', $tanggal_awal, $tanggal_akhir, 'getDataNONWOBB');
            // $getDataPartingNONWOFG      =   Freestock::getDataWONONWO('parting', $tanggal_awal, $tanggal_akhir, 'getDataNONWOFG');
            
            $getDataMarinasiWOBB        =   Freestock::getDataWONONWO('marinasi', $tanggal_awal, $tanggal_akhir, 'getDataWOBB', 'getDataWOFG','getDataNONWOBB', 'getDataNONWOFG');
            // $getDataMarinasiWOFG        =   Freestock::getDataWONONWO('marinasi', $tanggal_awal, $tanggal_akhir, 'getDataWOFG');
            // $getDataMarinasiNONWOBB     =   Freestock::getDataWONONWO('marinasi', $tanggal_awal, $tanggal_akhir, 'getDataNONWOBB');
            // $getDataMarinasiNONWOFG     =   Freestock::getDataWONONWO('marinasi', $tanggal_awal, $tanggal_akhir, 'getDataNONWOFG');

            $getDataWholeWOBB           =   Freestock::getDataWONONWO('whole', $tanggal_awal, $tanggal_akhir, 'getDataWOBB', 'getDataWOFG','getDataNONWOBB', 'getDataNONWOFG');
            // $getDataWholeWOFG           =   Freestock::getDataWONONWO('whole', $tanggal_awal, $tanggal_akhir, 'getDataWOFG');
            // $getDataWholeNONWOBB        =   Freestock::getDataWONONWO('whole', $tanggal_awal, $tanggal_akhir, 'getDataNONWOBB');
            // $getDataWholeNONWOFG        =   Freestock::getDataWONONWO('whole', $tanggal_awal, $tanggal_akhir, 'getDataNONWOFG');
            
            $getDataFrozenWOBB          =   Freestock::getDataWONONWO('frozen', $tanggal_awal, $tanggal_akhir, 'getDataWOBB', 'getDataWOFG','getDataNONWOBB', 'getDataNONWOFG');
            // $getDataFrozenWOFG          =   Freestock::getDataWONONWO('frozen', $tanggal_awal, $tanggal_akhir, 'getDataWOFG');
            // $getDataFrozenNONWOBB       =   Freestock::getDataWONONWO('frozen', $tanggal_awal, $tanggal_akhir, 'getDataNONWOBB');
            // $getDataFrozenNONWOFG       =   Freestock::getDataWONONWO('frozen', $tanggal_awal, $tanggal_akhir, 'getDataNONWOFG');

            
            // dd($getDataBonelessWOBB[0]->sum('berat'), $getDataPartingWOBB, $getDataMarinasiWOBB, $getDataWholeWOBB, $getDataFrozenWOBB);




            $data_bb_boneless   =   FreestockList::select(DB::raw('(IFNULL(sum(free_stocklist.qty), 0)) as total'),DB::raw('(IFNULL(sum(free_stocklist.berat), 0)) as kg'), 'free_stocklist.item_id', 'chiller.asal_tujuan', 'chiller.tanggal_produksi', 'free_stocklist.bb_kondisi')
                                    ->leftJoin('chiller', 'chiller.id', '=', 'free_stocklist.chiller_id')
                                    ->join('free_stock', 'free_stock.id', '=', 'free_stocklist.freestock_id')
                                    ->where([
                                        ['free_stock.regu', '=', 'boneless'],
                                        ['free_stock.status', '=', '3'],
                                    ])
                                    ->whereBetween('free_stock.tanggal', [$tanggal_awal,$tanggal_akhir])
                                    // ->whereIn('free_stocklist.freestock_id', Freestock::select('id')->whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])->where('status', 3)->where('regu', 'boneless'))
                                    ->groupBy('free_stocklist.item_id', 'chiller.asal_tujuan', 'chiller.tanggal_produksi', 'free_stocklist.bb_kondisi');
                                    // ->get();

            $data_fg_boneless   =   FreestockTemp::select(DB::raw('SUM(qty) AS total'), DB::raw('SUM(berat) AS kg'), 'item_id', DB::raw("SUM(plastik_qty) AS plastik"))
                                    ->join('free_stock', 'free_stock.id', '=', 'free_stocktemp.freestock_id')
                                    ->where([
                                        ['free_stock.regu', '=', 'boneless'],
                                        ['free_stock.status', '=', '3'],
                                    ])
                                    ->whereBetween('free_stock.tanggal', [$tanggal_awal,$tanggal_akhir])
                                    // ->whereNull('free_stock.netsuite_send')                        
                                    // ->whereIn('freestock_id', Freestock::select('id')->whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])->where('status', 3)->where('regu', 'boneless'))
                                    ->groupBy('item_id');
                                    // ->get();

                                    $bb_boneless        = (clone $data_bb_boneless)->whereNull('free_stock.netsuite_send')->get();
                                    $fg_boneless        = (clone $data_fg_boneless)->whereNull('free_stock.netsuite_send')->get();
                                    $cek_bb_boneless    = FreestockList::cek_bb_non_wo('boneless',$tanggal_awal, $tanggal_akhir);
                                    $cek_prod_boneless  = FreestockTemp::cek_non_wo_produksi('boneless',$tanggal_awal,$tanggal_akhir);

                                    $bb_boneless        = $this->get_list_item_non_wo($cek_bb_boneless,$cek_prod_boneless,'boneless',$tanggal_awal,$tanggal_akhir,'bb','');
                                    $non_wo_boneless    = $this->get_list_item_non_wo($cek_bb_boneless,$cek_prod_boneless,'boneless',$tanggal_awal,$tanggal_akhir,'','non_wo');
                                    
                                    $data_non_woBB_boneless = (clone $data_bb_boneless)->where('free_stock.netsuite_send', '0')->whereNull('free_stock.orderitem_id')->get();
                                    if ($data_non_woBB_boneless) {
                                        $non_wo_bb_bonless=$data_non_woBB_boneless->sum('kg');
                                    }else{
                                        $non_wo_bb_bonless=0;
                                    }
                                    
                                    $data_non_woFG_boneless = (clone $data_fg_boneless)->where('free_stock.netsuite_send', '0')->whereNull('free_stock.orderitem_id')->get();
                                    if ($data_non_woFG_boneless) {
                                        $non_wo_fg_bonless=$data_non_woFG_boneless->sum('kg');
                                    }else{
                                        $non_wo_fg_bonless=0;
                                    }

            $data_bb_parting    =   FreestockList::select(DB::raw('SUM(free_stocklist.qty) AS total'), DB::raw('SUM(free_stocklist.berat) AS kg'), 'free_stocklist.item_id', 'chiller.asal_tujuan', 'chiller.tanggal_produksi', 'free_stocklist.bb_kondisi')
                                    ->leftJoin('chiller', 'chiller.id', '=', 'free_stocklist.chiller_id')
                                    ->join('free_stock', 'free_stock.id', '=', 'free_stocklist.freestock_id')
                                    ->where([
                                        ['free_stock.regu', '=', 'parting'],
                                        ['free_stock.status', '=', '3'],
                                    ])
                                    ->whereBetween('free_stock.tanggal', [$tanggal_awal,$tanggal_akhir])
                                    // ->whereNull('free_stock.netsuite_send')
                                    // ->whereIn('free_stocklist.freestock_id', Freestock::select('id')->whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])->where('status', 3)->where('regu', 'parting'))
                                    ->groupBy('free_stocklist.item_id', 'chiller.asal_tujuan', 'chiller.tanggal_produksi', 'free_stocklist.bb_kondisi');
                                    // ->get();

            $data_fg_parting    =   FreestockTemp::select(DB::raw('SUM(qty) AS total'), DB::raw('SUM(berat) AS kg'), DB::raw('SUM(plastik_qty) AS plastik'), 'item_id')
                                    ->join('free_stock', 'free_stock.id', '=', 'free_stocktemp.freestock_id')
                                    ->where([
                                        ['free_stock.regu', '=', 'parting'],
                                        ['free_stock.status', '=', '3'],
                                    ])
                                    ->whereBetween('free_stock.tanggal', [$tanggal_awal,$tanggal_akhir])
                                    ->groupBy('item_id');

                                $bb_parting        = (clone $data_bb_parting)->whereNull('free_stock.netsuite_send')->get();
                                $fg_parting        = (clone $data_fg_parting)->whereNull('free_stock.netsuite_send')->get();
                                $cek_bb_parting    = FreestockList::cek_bb_non_wo('parting',$tanggal_awal, $tanggal_akhir);
                                $cek_prod_parting  = FreestockTemp::cek_non_wo_produksi('parting',$tanggal_awal,$tanggal_akhir);

                                $bb_parting        = $this->get_list_item_non_wo($cek_bb_parting,$cek_prod_parting,'parting',$tanggal_awal,$tanggal_akhir,'bb','');
                                $non_wo_parting    = $this->get_list_item_non_wo($cek_bb_parting,$cek_prod_parting,'parting',$tanggal_awal,$tanggal_akhir,'','non_wo');
                                // $non_wo_parting    =null;
                                // $id_prod_parting   =[];
                                // foreach ($cek_bb_parting as $item_bb_parting ) {
                                //     foreach ($cek_prod_parting as $item_prod_parting ) {
                                //         if ($item_bb_parting->item_id == $item_prod_parting->item_id) {
                                //             $id_prod_parting[] = $item_prod_parting->item_id;
                                //             $bb_parting        = (clone $data_bb_parting)->whereNull('free_stock.netsuite_send')->where(function($q) use ($id_prod_parting){
                                //                                                         $q->whereNotIn('free_stocklist.item_id', $id_prod_parting);
                                //                                                         $q->orWhere('chiller.type','bahan-baku');
                                //                                                     })
                                //                                                     ->get();
                                //             $data_non_wo_parting = (clone $data_bb_parting)->whereNull('free_stock.netsuite_send')->whereIn('free_stocklist.item_id',$id_prod_parting)->where('chiller.type','hasil-produksi')->get();
                                //             if ($data_non_wo_parting) {
                                //                 $non_wo_parting =$data_non_wo_parting->sum('kg');
                                //             }else{
                                //                 $non_wo_parting=0;
                                //             }
                                //         }
                                //     }
                                // }
                                // dd($non_wo_parting);
                                $data_non_wo_bb_parting  = (clone $data_bb_parting)->where('free_stock.netsuite_send', '0')->whereNull('free_stock.orderitem_id')->get();
                                if ($data_non_wo_bb_parting) {
                                    $non_wo_bb_parting = $data_non_wo_bb_parting->sum('kg');
                                }
                                // dd($non_wo_bb_parting);
                                $data_non_wo_fg_parting  = (clone $data_fg_parting)->where('free_stock.netsuite_send', '0')->whereNull('free_stock.orderitem_id')->get();
                                if ($data_non_wo_fg_parting ) {
                                    $non_wo_fg_parting = $data_non_wo_fg_parting->sum('kg');
                                }
            

            $data_bb_marinasi        =   FreestockList::select(DB::raw('SUM(free_stocklist.qty) AS total'), DB::raw('SUM(free_stocklist.berat) AS kg'), 'free_stocklist.item_id', 'chiller.asal_tujuan', 'chiller.tanggal_produksi', 'free_stocklist.bb_kondisi')
                                    ->leftJoin('items', 'items.id', '=', 'free_stocklist.item_id')                        
                                    ->leftJoin('chiller', 'chiller.id', '=', 'free_stocklist.chiller_id')
                                    ->join('free_stock', 'free_stock.id', '=', 'free_stocklist.freestock_id')
                                    ->where([
                                        ['free_stock.regu', '=', 'marinasi'],
                                        ['free_stock.status', '=', '3'],
                                    ])
                                    ->whereBetween('free_stock.tanggal', [$tanggal_awal,$tanggal_akhir])
                                    ->groupBy('items.nama')
                                    ->groupBy('chiller.type');
                                    // ->get();

            $data_fg_marinasi        =   FreestockTemp::select(DB::raw('SUM(qty) AS total'), DB::raw('SUM(berat) AS kg'), DB::raw('SUM(plastik_qty) AS plastik'), 'item_id')
                                    ->join('free_stock', 'free_stock.id', '=', 'free_stocktemp.freestock_id')
                                    ->where([
                                        ['free_stock.regu', '=', 'marinasi'],
                                        ['free_stock.status', '=', '3'],
                                    ])
                                    ->whereBetween('free_stock.tanggal', [$tanggal_awal,$tanggal_akhir])
                                    ->groupBy('item_id');

                                    $bb_marinasi        = (clone $data_bb_marinasi)->whereNull('free_stock.netsuite_send')->get();
                                    $fg_marinasi        = (clone $data_fg_marinasi)->whereNull('free_stock.netsuite_send')->get();
                                    $cek_bb_marinasi    = FreestockList::cek_bb_non_wo('marinasi',$tanggal_awal, $tanggal_akhir);
                                    $cek_prod_marinasi  = FreestockTemp::cek_non_wo_produksi('marinasi',$tanggal_awal,$tanggal_akhir);

                                    $bb_marinasi        = $this->get_list_item_non_wo($cek_bb_marinasi,$cek_prod_marinasi,'marinasi',$tanggal_awal,$tanggal_akhir,'bb','');
                                    $non_wo_marinasi    = $this->get_list_item_non_wo($cek_bb_marinasi,$cek_prod_marinasi,'marinasi',$tanggal_awal,$tanggal_akhir,'','non_wo');
                                    $data_non_woBB_marinasi = (clone $data_bb_marinasi)->where('free_stock.netsuite_send', '0')->whereNull('free_stock.orderitem_id')->get();
                                    if ($data_non_woBB_marinasi) {
                                        $non_wo_bb_marinasi=$data_non_woBB_marinasi->sum('kg');
                                    }else{
                                        $non_wo_bb_marinasi=0;
                                    }
                                    $data_non_woFG_marinasi = (clone $data_fg_marinasi)->where('free_stock.netsuite_send', '0')->whereNull('free_stock.orderitem_id')->get();
                                    if ($data_non_woFG_marinasi) {
                                        $non_wo_fg_marinasi=$data_non_woFG_marinasi->sum('kg');
                                    }else{
                                        $non_wo_fg_marinasi=0;
                                    }

            $data_bb_whole      =   FreestockList::select(DB::raw('SUM(free_stocklist.qty) AS total'), DB::raw('SUM(free_stocklist.berat) AS kg'), 'free_stocklist.item_id', 'chiller.asal_tujuan', 'chiller.tanggal_produksi', 'free_stocklist.bb_kondisi')
                                    ->leftJoin('chiller', 'chiller.id', '=', 'free_stocklist.chiller_id')
                                    ->join('free_stock', 'free_stock.id', '=', 'free_stocklist.freestock_id')
                                    ->where([
                                        ['free_stock.regu', '=', 'whole'],
                                        ['free_stock.status', '=', '3'],
                                    ])
                                    ->whereBetween('free_stock.tanggal', [$tanggal_awal,$tanggal_akhir])
                                    ->groupBy('free_stocklist.item_id', 'chiller.asal_tujuan', 'chiller.tanggal_produksi', 'free_stocklist.bb_kondisi');
                                    // ->get();;

            $data_fg_whole      =   FreestockTemp::select(DB::raw('SUM(qty) AS total'), DB::raw('SUM(berat) AS kg'), DB::raw('SUM(plastik_qty) AS plastik'), 'item_id')
                                    ->join('free_stock', 'free_stock.id', '=', 'free_stocktemp.freestock_id')
                                    ->where([
                                        ['free_stock.regu', '=', 'whole'],
                                        ['free_stock.status', '=', '3'],
                                    ])
                                    ->whereBetween('free_stock.tanggal', [$tanggal_awal,$tanggal_akhir])
                                    ->groupBy('item_id');
                                    // ->get();

                                    $bb_whole        = (clone $data_bb_whole)->whereNull('free_stock.netsuite_send')->get();
                                    $fg_whole        = (clone $data_fg_whole)->whereNull('free_stock.netsuite_send')->get();
                                    $cek_bb_whole    = FreestockList::cek_bb_non_wo('whole',$tanggal_awal, $tanggal_akhir);
                                    $cek_prod_whole  = FreestockTemp::cek_non_wo_produksi('whole',$tanggal_awal,$tanggal_akhir);
                                    // $bb_whole        = $this->get_list_item_non_wo($cek_bb_whole,$cek_prod_whole,'whole',$tanggal_awal,$tanggal_akhir,'bb','');
                                    // $non_wo_whole    = $this->get_list_item_non_wo($cek_bb_whole,$cek_prod_whole,'whole',$tanggal_awal,$tanggal_akhir,'','non_wo');

                                    $non_wo_whole    =null;
                                    $id_prod_whole   =[];
                                    foreach ($cek_bb_whole as $item_bb_whole ) {
                                        foreach ($cek_prod_whole as $item_prod_whole ) {
                                            if ($item_bb_whole->item_id == $item_prod_whole->item_id) {
                                                $id_prod_whole[] = $item_prod_whole->item_id;
                                                $bb_whole        = (clone $data_bb_whole)->whereNull('free_stock.netsuite_send')->where(function($q) use ($id_prod_whole){
                                                                                            $q->whereNotIn('free_stocklist.item_id', $id_prod_whole);
                                                                                            $q->orWhere('chiller.type','bahan-baku');
                                                                                        })
                                                                                        ->get();
                                                $data_non_wo_whole = (clone $data_bb_parting)->whereNull('free_stock.netsuite_send')->whereIn('free_stocklist.item_id',$id_prod_whole)->where('chiller.type','hasil-produksi')->get();
                                                if ($data_non_wo_whole) {
                                                    // dd($data_non_wo_whole->sum('kg'));
                                                    $non_wo_whole = $data_non_wo_whole->sum('kg');
                                                }else{
                                                    $non_wo_whole=0;
                                                }
                                            }
                                        }
                                    }

                                    $data_non_woBB_whole = (clone $data_bb_whole)->where('free_stock.netsuite_send', '0')->whereNull('free_stock.orderitem_id')->get();
                                    if ($data_non_woBB_whole) {
                                        $non_wo_bb_whole=$data_non_woBB_whole->sum('kg');
                                    }else{
                                        $non_wo_bb_whole=0;
                                    }
                                    $data_non_woFG_whole = (clone $data_fg_whole)->where('free_stock.netsuite_send', '0')->whereNull('free_stock.orderitem_id')->get();
                                    // $non_wo_fg_whole  = (clone $data_fg_whole)->where('free_stock.netsuite_send', '0')->whereNull('free_stock.orderitem_id')->get();
                                    if ($data_non_woFG_whole) {
                                        $non_wo_fg_whole=$data_non_woFG_whole->sum('kg');
                                    }else{
                                        $non_wo_fg_whole=0;
                                    }

            $data_bb_frozen     =   FreestockList::select(DB::raw('SUM(free_stocklist.qty) AS total'), DB::raw('SUM(free_stocklist.berat) AS kg'), 'free_stocklist.item_id', 'chiller.asal_tujuan', 'chiller.tanggal_produksi', 'free_stocklist.bb_kondisi')
                                    ->leftJoin('chiller', 'chiller.id', '=', 'free_stocklist.chiller_id')
                                    ->join('free_stock', 'free_stock.id', '=', 'free_stocklist.freestock_id')
                                    ->where([
                                        ['free_stock.regu', '=', 'frozen'],
                                        ['free_stock.status', '=', '3'],
                                    ])
                                    ->whereBetween('free_stock.tanggal', [$tanggal_awal,$tanggal_akhir])
                                    // ->whereNull('free_stock.netsuite_send')
                                    // ->whereIn('free_stocklist.freestock_id', Freestock::select('id')->whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])->where('status', 3)->where('regu', 'frozen'))
                                    ->groupBy('free_stocklist.item_id', 'chiller.asal_tujuan', 'chiller.tanggal_produksi', 'free_stocklist.bb_kondisi');
                                    // ->get();

            $data_fg_frozen     =   FreestockTemp::select(DB::raw('SUM(qty) AS total'), DB::raw('SUM(berat) AS kg'), DB::raw('SUM(plastik_qty) AS plastik'), 'item_id')
                                    ->join('free_stock', 'free_stock.id', '=', 'free_stocktemp.freestock_id')
                                    ->where([
                                        ['free_stock.regu', '=', 'frozen'],
                                        ['free_stock.status', '=', '3'],
                                    ])
                                    ->whereBetween('free_stock.tanggal', [$tanggal_awal,$tanggal_akhir])
                                    // ->whereNull('free_stock.netsuite_send')
                                    // ->whereIn('freestock_id', Freestock::select('id')->whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])->where('status', 3)->where('regu', 'frozen'))
                                    ->groupBy('item_id');
                                    // ->get();

                                    $bb_frozen        = (clone $data_bb_frozen)->whereNull('free_stock.netsuite_send')->get();
                                    $fg_frozen        = (clone $data_fg_frozen)->whereNull('free_stock.netsuite_send')->get();
                                    $cek_bb_frozen    = FreestockList::cek_bb_non_wo('frozen',$tanggal_awal, $tanggal_akhir);
                                    $cek_prod_frozen  = FreestockTemp::cek_non_wo_produksi('frozen',$tanggal_awal,$tanggal_akhir);

                                    $bb_frozen        = $this->get_list_item_non_wo($cek_bb_frozen,$cek_prod_frozen,'frozen',$tanggal_awal,$tanggal_akhir,'bb','');
                                    $non_wo_frozen    = $this->get_list_item_non_wo($cek_bb_frozen,$cek_prod_frozen,'frozen',$tanggal_awal,$tanggal_akhir,'','non_wo');
                                    
                                    $data_non_woBB_frozen  = (clone $data_bb_frozen)->where('free_stock.netsuite_send', '0')->whereNull('free_stock.orderitem_id')->get();
                                    if ($data_non_woBB_frozen) {
                                        $non_wo_bb_frozen=$data_non_woBB_frozen->sum('kg');
                                    }else{  
                                        $non_wo_bb_frozen=0;
                                    }
                                    $data_non_woFG_frozen  = (clone $data_fg_frozen)->where('free_stock.netsuite_send', '0')->whereNull('free_stock.orderitem_id')->get();
                                    if ($data_non_woFG_frozen) {
                                        $non_wo_fg_frozen=$data_non_woFG_frozen->sum('kg');
                                    }else{
                                        $non_wo_fg_frozen=0;
                                    }

            $produksi   =   [
                'bb_boneless'           =>  $bb_boneless,
                'bb_tt_boneless'        =>  $bb_boneless->sum('kg'),
                'bb_qty_boneless'       =>  $bb_boneless->sum('total'),
                'non_wo_bb_boneless'    =>  $non_wo_bb_bonless + $non_wo_boneless,
                'non_wo_fg_boneless'    =>  $non_wo_fg_bonless + $non_wo_boneless,
                'fg_boneless'           =>  $fg_boneless,
                'fg_tt_boneless'        =>  $fg_boneless->sum('kg'),
                'fg_qty_boneless'       =>  $fg_boneless->sum('total'),
                'fg_pe_boneless'        =>  $fg_boneless->sum('plastik'),
                'bb_parting'            =>  $bb_parting,
                'bb_tt_parting'         =>  $bb_parting->sum('kg'),
                'bb_qty_parting'        =>  $bb_parting->sum('total'),
                'non_wo_bb_parting'     =>  $non_wo_bb_parting + $non_wo_parting,
                'non_wo_fg_parting'     =>  $non_wo_fg_parting + $non_wo_parting,
                'fg_parting'            =>  $fg_parting,
                'fg_tt_parting'         =>  $fg_parting->sum('kg'),
                'fg_qty_parting'        =>  $fg_parting->sum('total'),
                'fg_pe_parting'         =>  $fg_parting->sum('plastik'),
                'bb_marinasi'           =>  $bb_marinasi,
                'bb_tt_marinasi'        =>  $bb_marinasi->sum('kg'),
                'bb_qty_marinasi'       =>  $bb_marinasi->sum('total'),
                'non_wo_bb_marinasi'    =>  $non_wo_bb_marinasi + $non_wo_marinasi,
                'non_wo_fg_marinasi'    =>  $non_wo_fg_marinasi + $non_wo_marinasi,
                'fg_marinasi'           =>  $fg_marinasi,
                'fg_tt_marinasi'        =>  $fg_marinasi->sum('kg'),
                'fg_qty_marinasi'       =>  $fg_marinasi->sum('total'),
                'fg_pe_marinasi'        =>  $fg_marinasi->sum('plastik'),
                'bb_whole'              =>  $bb_whole,
                'bb_tt_whole'           =>  $bb_whole->sum('kg'),
                'bb_qty_whole'          =>  $bb_whole->sum('total'),
                'non_wo_bb_whole'       =>  $non_wo_bb_whole + $non_wo_whole,
                'non_wo_fg_whole'       =>  $non_wo_fg_whole + $non_wo_whole,
                'fg_whole'              =>  $fg_whole,
                'fg_tt_whole'           =>  $fg_whole->sum('kg'),
                'fg_qty_whole'          =>  $fg_whole->sum('total'),
                'fg_pe_whole'           =>  $fg_whole->sum('plastik'),
                'bb_frozen'             =>  $bb_frozen,
                'bb_tt_frozen'          =>  $bb_frozen->sum('kg'),
                'bb_qty_frozen'         =>  $bb_frozen->sum('total'),
                'non_wo_bb_frozen'      =>  $non_wo_bb_frozen + $non_wo_frozen,
                'non_wo_fg_frozen'      =>  $non_wo_fg_frozen + $non_wo_frozen,
                'fg_frozen'             =>  $fg_frozen,
                'fg_tt_frozen'          =>  $fg_frozen->sum('kg'),
                'fg_qty_frozen'         =>  $fg_frozen->sum('total'),
                'fg_pe_frozen'          =>  $fg_frozen->sum('plastik'),
                
                'dataBonelessWOBB'      =>  $getDataBonelessWOBB[0]->sum('berat'),
                'dataBonelessWOFG'      =>  $getDataBonelessWOBB[1]->sum('berat'),
                'dataBonelessNONWOBB'   =>  $getDataBonelessWOBB[2]->sum('berat'),
                'dataBonelessNONWOFG'   =>  $getDataBonelessWOBB[3]->sum('berat'),

                'dataPartingWOBB'       =>  $getDataPartingWOBB[0]->sum('berat'),  
                'dataPartingWOFG'       =>  $getDataPartingWOBB[1]->sum('berat'),
                'dataPartingNONWOBB'    =>  $getDataPartingWOBB[2]->sum('berat'),
                'dataPartingNONWOFG'    =>  $getDataPartingWOBB[3]->sum('berat'),

                'dataMarinasiWOBB'      =>  $getDataMarinasiWOBB[0]->sum('berat'),
                'dataMarinasiWOFG'      =>  $getDataMarinasiWOBB[1]->sum('berat'),
                'dataMarinasiNONWOBB'   =>  $getDataMarinasiWOBB[2]->sum('berat'),
                'dataMarinasiNONWOFG'   =>  $getDataMarinasiWOBB[3]->sum('berat'),

                'dataWholeWOBB'         =>  $getDataWholeWOBB[0]->sum('berat'),
                'dataWholeWOFG'         =>  $getDataWholeWOBB[1]->sum('berat'),
                'dataWholeNONWOBB'      =>  $getDataWholeWOBB[2]->sum('berat'),
                'dataWholeNONWOFG'      =>  $getDataWholeWOBB[3]->sum('berat'),

                'dataFrozenWOBB'        =>  $getDataFrozenWOBB[0]->sum('berat'),
                'dataFrozenWOFG'        =>  $getDataFrozenWOBB[1]->sum('berat'),
                'dataFrozenNONWOBB'     =>  $getDataFrozenWOBB[2]->sum('berat'),
                'dataFrozenNONWOFG'     =>  $getDataFrozenWOBB[3]->sum('berat'),
            ];

            // dd($produksi['dataBonelessWOBB']);

            return view('admin.pages.laporan.laporan_dashboard_per_page.pageEmpat', compact('tanggal', 'tanggal_awal', 'tanggal_akhir', 'produksi'));

        } else if ($request->key == 'pageLima') {

            $ambil_pe       =   FreestockTemp::select(DB::raw("SUM(plastik_qty) AS jumlah"), 'plastik_nama', 'tanggal_produksi')
                                ->whereIn('freestock_id', Freestock::select('id')->whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])->where('status', 3)->where('created_at','>=',Applib::DefaultTanggalAudit()))
                                ->where('plastik_sku', '!=', NULL)
                                ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                ->groupBy('plastik_sku', 'tanggal_produksi')
                                ->orderByRaw('tanggal_produksi ASC, jumlah DESC')
                                ->get();

            $plastik        =   FreestockTemp::select('plastik_nama AS name', DB::raw("SUM(plastik_qty) AS y"))
                                ->whereIn('freestock_id', Freestock::select('id')->whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])->where('status', 3)->where('created_at','>=',Applib::DefaultTanggalAudit()))
                                ->where('plastik_sku', '!=', NULL)
                                ->where('plastik_nama', '!=', NULL)
                                ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                ->groupBy('plastik_sku')
                                ->orderBy('y', 'DESC')
                                ->limit(10)
                                ->get();

            $sales_channel  =   Order::select('sales_channel', 'id', DB::raw('COUNT(id) AS total'), DB::raw('SUM(IF(status>0,0,1)) AS pending'), DB::raw('SUM(IF(status>0,1,0)) AS selesai'))
                                ->whereIn('tanggal_kirim', [$tanggal_awal, $tanggal_akhir])
                                ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                ->groupBy('sales_channel')
                                ->get();

            $spider_channel =   "[" ;
            $spider_order   =   "[" ;
            $spider_alokasi =   "[" ;

            foreach ($sales_channel as $row) {
                $spider_channel     .=  "'". $row->sales_channel . "',";
                $order      =   0 ;
                $aloc       =   0 ;
                foreach ($row->list_order as $list) {
                    $order      +=  $list->berat ;
                    $aloc       +=  $list->fulfillment_berat ;
                }
                $spider_order       .=   $order. ",";
                $spider_alokasi     .=   $aloc. ",";
            }

            $spider_channel .=  "]" ;
            $spider_order   .=  "]";
            $spider_alokasi .=  "]";

            $plastik_pie    =   "[" ;
            foreach ($plastik as $row) {
                $plastik_pie    .=  "{" ;
                $plastik_pie    .=  "name: '" . $row->name . "'," ;
                $plastik_pie    .=  "y: " . $row->y . "," ;
                $plastik_pie    .=  "}," ;
            }
            $plastik_pie    .=  "]" ;

            $data          =   [
                'ambil_pe'          =>  $ambil_pe,
                'ambil_pe_sum'      =>  $ambil_pe->sum('jumlah'),
            ];

            return view('admin.pages.laporan.laporan_dashboard_per_page.pageLima', compact('tanggal', 'tanggal_awal', 'tanggal_akhir', 'data', 'sales_channel', 'spider_channel', 'spider_order', 'spider_alokasi', 'plastik_pie'));

        } else if ($request->key == 'pageEnam') {

            $sales_channel  =   Order::select('sales_channel', 'id', DB::raw('COUNT(id) AS total'), DB::raw('SUM(IF(status>0,0,1)) AS pending'), DB::raw('SUM(IF(status>0,1,0)) AS selesai'))
                                ->whereIn('tanggal_kirim', [$tanggal_awal, $tanggal_akhir])
                                ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                ->groupBy('sales_channel')
                                ->get();

            $item_pending   =   OrderItem::select('item_id', 'nama_detail', 'tanggal_kirim', DB::raw("SUM(berat) AS kg"), DB::raw("SUM(qty) AS total"))
                                ->where('order_items.status', NULL)
                                ->whereBetween('tanggal_kirim', [$tanggal_awal, $tanggal_akhir])
                                ->leftJoin('orders', 'orders.id', '=', 'order_items.order_id')
                                ->orderByRaw('tanggal_kirim ASC, nama_detail ASC')
                                ->where('order_items.created_at','>=',Applib::DefaultTanggalAudit())
                                ->where('orders.created_at','>=',Applib::DefaultTanggalAudit())
                                ->groupBy('item_id')
                                ->get() ;

            $tgl_order  =   Order::select('tanggal_kirim')
                            ->where(function($query) use ($tanggal_awal, $tanggal_akhir) {
                                if ($tanggal_awal == $tanggal_akhir) {
                                    // $query->whereBetween('tanggal_kirim', [date('Y-m-d', strtotime("-7 days", strtotime($tanggal_awal))), $tanggal_awal]);
                                    $query->whereBetween('tanggal_kirim', [date('Y-m-d', strtotime("-0 days", strtotime($tanggal_awal))), $tanggal_awal]);
                                } else {
                                    $query->whereBetween('tanggal_kirim', [$tanggal_awal, $tanggal_akhir])->where('created_at','>=',Applib::DefaultTanggalAudit());
                                }
                            })
                            ->where('created_at','>=',Applib::DefaultTanggalAudit())
                            ->groupBy('tanggal_kirim')
                            ->orderBy('tanggal_kirim', 'ASC') ;

            $order_alokasi  =   [] ;
            $order_pending  =   [] ;
            foreach ($tgl_order->get() as $row) {
                $order_alokasi[]    =   Order::where('tanggal_kirim', $row->tanggal_kirim)->where('status', '!=', NULL)
                                                ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                                ->count();
                $order_pending[]    =   Order::where('tanggal_kirim', $row->tanggal_kirim)->where('status', NULL)
                                                ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                                ->count();
            }

            $tgl_order  =   $tgl_order->pluck('tanggal_kirim') ;
            $alokasi    =   "[{name: 'Teralokasi',data: ";
            $alokasi    .=  json_encode($order_alokasi) ;
            $alokasi    .=  "}, {name: 'Pending',data: ";
            $alokasi    .=  json_encode($order_pending);
            $alokasi    .=  "}]";

            return view('admin.pages.laporan.laporan_dashboard_per_page.pageEnam', compact('tanggal', 'tanggal_awal', 'tanggal_akhir', 'alokasi', 'tgl_order', 'sales_channel', 'item_pending'));

        } else if ($request->key == 'pageTujuh') {

            $ambil_bb       =   FreestockList::select('chiller.asal_tujuan', 'chiller.tanggal_produksi', DB::raw("SUM(qty) AS total"), DB::raw("SUM(berat) AS kg"))
                                ->whereIn('freestock_id', Freestock::select('id')->whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])->where('status', 3)->where('created_at','>=',Applib::DefaultTanggalAudit()))
                                ->whereIn('free_stocklist.regu', ['parting', 'whole','boneless', 'frozen', 'marinasi'])
                                ->leftJoin('chiller', 'chiller.id', '=', 'free_stocklist.chiller_id')
                                ->where('free_stocklist.created_at','>=',Applib::DefaultTanggalAudit())
                                ->where('chiller.created_at','>=',Applib::DefaultTanggalAudit())
                                ->groupBy('chiller.asal_tujuan', 'chiller.tanggal_produksi')
                                ->orderByRaw('chiller.asal_tujuan ASC, chiller.tanggal_produksi DESC')
                                ->get();

            $thawing        =   Chiller::select('item_id', 'item_name', DB::raw("SUM(qty_item) AS qty"), DB::raw("SUM(berat_item) AS berat"))
                                ->where('asal_tujuan', 'thawing')
                                ->whereBetween('tanggal_produksi', [$tanggal_awal, $tanggal_akhir])
                                ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                ->groupBy('item_id', 'item_name')
                                ->get();


            $data           =   [
                'ambil_bb'          =>  $ambil_bb,
                'ambil_bb_sum'      =>  $ambil_bb->sum('kg'),
                'thawaing'          =>  $thawing,
            ];

            return view('admin.pages.laporan.laporan_dashboard_per_page.pageTujuh', compact('tanggal', 'tanggal_awal', 'tanggal_akhir', 'data'));

        } else if ($request->key == 'pageDelapan') {

            $bb_marinasi            =   FreestockList::select(DB::raw('SUM(free_stocklist.qty) AS total'), DB::raw('SUM(free_stocklist.berat) AS kg'), 'free_stocklist.item_id', 'chiller.asal_tujuan', 'chiller.tanggal_produksi', 'free_stocklist.bb_kondisi')
                                        ->leftJoin('chiller', 'chiller.id', '=', 'free_stocklist.chiller_id')
                                        ->whereIn('free_stocklist.freestock_id', Freestock::select('id')->whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])->where('status', 3)->where('created_at','>=',Applib::DefaultTanggalAudit()))
                                        ->where('free_stocklist.regu', 'marinasi')
                                        ->where('free_stocklist.created_at','>=',Applib::DefaultTanggalAudit())
                                        ->where('chiller.created_at','>=',Applib::DefaultTanggalAudit())
                                        ->groupBy('free_stocklist.item_id', 'chiller.asal_tujuan', 'chiller.tanggal_produksi', 'free_stocklist.bb_kondisi')
                                        ->get();

            $clone_fg_marinasi      =   FreestockTemp::select(DB::raw('SUM(qty) AS total'), DB::raw('SUM(berat) AS kg'), DB::raw('SUM(plastik_qty) AS plastik'), 'item_id','kategori')
                                        ->where('regu', 'marinasi')
                                        ->whereIn('freestock_id', Freestock::select('id')->whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])->where('status', 3)->where('created_at','>=',Applib::DefaultTanggalAudit()));
                                        // ->groupBy('item_id');
                                        // ->get();

            $clonefg                =   clone $clone_fg_marinasi;
            $cloneabf               =   clone $clone_fg_marinasi;
            $clonechiller           =   clone $clone_fg_marinasi;
            $cloneekspedisi         =   clone $clone_fg_marinasi;
            $clonecs                =   clone $clone_fg_marinasi;
            $fg_marinasi            =   $clonefg
                                        ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                        ->groupBy('item_id')->get();
            $countchiller           =   $clonechiller
                                        ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                        ->groupBy('kategori')->where('kategori',"0")->count();
            $countchiller2           =   $clonechiller
                                        ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                        ->groupBy('kategori')->where('kategori','')->count();
            $countabf               =   $cloneabf
                                        ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                        ->groupBy('kategori')->where('kategori',"1")->count();
            $countekspedisi         =   $cloneekspedisi
                                        ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                        ->groupBy('kategori')->where('kategori',"2")->count();
            $countcs                =   $clonecs
                                        ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                        ->groupBy('kategori')->where('kategori',"3")->count();

            $waktu_awal_marinasi    =   FreestockList::where('regu', 'marinasi')
                                        ->whereIn('freestock_id', Freestock::select('id')->whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])->where('status', 3)->where('created_at','>=',Applib::DefaultTanggalAudit()))
                                        ->where('created_at', 'like', '%'.$tanggal.'%')
                                        ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                        ->orderBy('id', 'asc')
                                        ->first();

            $waktu_akhir_marinasi   =   FreestockTemp::where('regu', 'marinasi')
                                        ->whereIn('freestock_id', Freestock::select('id')->whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])->where('status', 3)->where('created_at','>=',Applib::DefaultTanggalAudit()))
                                        ->where('created_at', 'like', '%'.$tanggal.'%')
                                        ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                        ->orderBy('id', 'desc')
                                        ->first();


            $jam_kerja_marinasi     = ((strtotime($waktu_akhir_marinasi->created_at ?? "00:00:00") - strtotime($waktu_awal_marinasi->created_at ?? "00:00:00")));

            $waktu_marinasi         = array(
                                        'awal'          => (date('H:i:s', strtotime($waktu_awal_marinasi->created_at ?? "00:00:00")) ?? "-" ),
                                        'akhir'         => (date('H:i:s', strtotime($waktu_akhir_marinasi->created_at ?? "00:00:00")) ?? "-" ),
                                        'jam_kerja'     => $jam_kerja_marinasi
                                    );
            $produksi   =   [
                'bb_marinasi'       =>  $bb_marinasi,
                'bb_tt_marinasi'    =>  $bb_marinasi->sum('kg'),
                'bb_qty_marinasi'   =>  $bb_marinasi->sum('total'),
                'fg_marinasi'       =>  $fg_marinasi,
                'fg_tt_marinasi'    =>  $fg_marinasi->sum('kg'),
                'fg_qty_marinasi'   =>  $fg_marinasi->sum('total'),
                'fg_pe_marinasi'    =>  $fg_marinasi->sum('plastik'),
                'countchiller'      =>  $countchiller + $countchiller2,
                'countabf'          =>  $countabf,
                'countekspedisi'    =>  $countekspedisi,
                'countcs'           =>  $countcs,
            ];

            return view('admin.pages.laporan.laporan_dashboard_per_page.pageDelapan', compact('tanggal', 'tanggal_awal', 'tanggal_akhir', 'produksi'));

        } else if ($request->key == 'pageSembilan') {
            $bb_parting             =   FreestockList::select(DB::raw('SUM(free_stocklist.qty) AS total'), DB::raw('SUM(free_stocklist.berat) AS kg'), 'free_stocklist.item_id', 'chiller.asal_tujuan', 'chiller.tanggal_produksi', 'free_stocklist.bb_kondisi')
                                        ->leftJoin('chiller', 'chiller.id', '=', 'free_stocklist.chiller_id')
                                        ->whereIn('free_stocklist.freestock_id', Freestock::select('id')->whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])->where('status', 3)->where('created_at','>=',Applib::DefaultTanggalAudit()))
                                        ->where('free_stocklist.regu', 'parting')
                                        ->where('free_stocklist.created_at','>=',Applib::DefaultTanggalAudit())
                                        ->where('chiller.created_at','>=',Applib::DefaultTanggalAudit())
                                        ->groupBy('free_stocklist.item_id', 'chiller.asal_tujuan', 'chiller.tanggal_produksi', 'free_stocklist.bb_kondisi')
                                        ->get();

            $clone_fg_parting        =   FreestockTemp::select(DB::raw('SUM(qty) AS total'), DB::raw('SUM(berat) AS kg'), DB::raw('SUM(plastik_qty) AS plastik'), 'item_id','kategori')
                                        ->where('regu', 'parting')
                                        ->whereIn('freestock_id', Freestock::select('id')->whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])->where('status', 3)->where('created_at','>=',Applib::DefaultTanggalAudit()));
                                        // ->groupBy('item_id');
                                        // ->get();
            $clonedata              =   clone $clone_fg_parting;
            $cloneabf_parting       =   clone $clone_fg_parting;
            $clonechiller_parting   =   clone $clone_fg_parting;
            $cloneekspedisi_parting =   clone $clone_fg_parting;
            $clonecs_parting        =   clone $clone_fg_parting;
            $fg_parting             =   $clonedata
                                        ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                        ->groupBy('item_id')->get();
            $countchiller_parting   =   $clonechiller_parting
                                        ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                        ->groupBy('kategori')->where('kategori',0)->count();
            $countchiller_parting2  =   $clonechiller_parting
                                        ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                        ->groupBy('kategori')->where('kategori','')->count();
            $countabf_parting       =   $cloneabf_parting
                                        ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                        ->groupBy('kategori')->where('kategori',1)->count();
            $countekspedisi_parting =   $cloneekspedisi_parting
                                        ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                        ->groupBy('kategori')->where('kategori',2)->count();
            $countcs_parting        =   $clonecs_parting
                                        ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                        ->groupBy('kategori')->where('kategori',3)->count();
            

            $waktu_awal_parting     =   FreestockList::where('regu', 'parting')
                                        ->whereIn('freestock_id', Freestock::select('id')->whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])->where('status', 3)->where('created_at','>=',Applib::DefaultTanggalAudit()))
                                        ->where('created_at', 'like', '%'.$tanggal.'%')
                                        ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                        ->orderBy('id', 'asc')
                                        ->first();

            $waktu_akhir_parting    =   FreestockTemp::where('regu', 'parting')
                                        ->whereIn('freestock_id', Freestock::select('id')->whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])->where('status', 3)->where('created_at','>=',Applib::DefaultTanggalAudit()))
                                        ->where('created_at', 'like', '%'.$tanggal.'%')
                                        ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                        ->orderBy('id', 'desc')
                                        ->first();


            $jam_kerja_parting      = ((strtotime($waktu_akhir_parting->created_at ?? "00:00:00") - strtotime($waktu_awal_parting->created_at ?? "00:00:00")));


            $waktu_parting          = array(
                                        'awal'          => (date('H:i:s', strtotime($waktu_awal_parting->created_at ?? "00:00:00")) ?? "-" ),
                                        'akhir'         => (date('H:i:s', strtotime($waktu_akhir_parting->created_at ?? "00:00:00")) ?? "-" ),
                                        'jam_kerja'     => $jam_kerja_parting
                                        );

            $produksi               =   [
                                        'bb_parting'        =>  $bb_parting,
                                        'bb_tt_parting'     =>  $bb_parting->sum('kg'),
                                        'bb_qty_parting'    =>  $bb_parting->sum('total'),
                                        'fg_parting'        =>  $fg_parting,
                                        'fg_tt_parting'     =>  $fg_parting->sum('kg'),
                                        'fg_qty_parting'    =>  $fg_parting->sum('total'),
                                        'fg_pe_parting'     =>  $fg_parting->sum('plastik'),
                                        'countabf_parting'      =>  $countabf_parting,
                                        'countchiller_parting'  =>  $countchiller_parting+$countchiller_parting2,
                                        'countekspedisi_parting'=>  $countekspedisi_parting,
                                        'countcs_parting'       =>  $countcs_parting,
                                        ];

            return view('admin.pages.laporan.laporan_dashboard_per_page.pageSembilan', compact('tanggal', 'tanggal_awal', 'tanggal_akhir', 'produksi'));

        } else if ($request->key == 'pageSepuluh') {
            $bb_whole           =   FreestockList::select(DB::raw('SUM(free_stocklist.qty) AS total'), DB::raw('SUM(free_stocklist.berat) AS kg'), 'free_stocklist.item_id', 'chiller.asal_tujuan', 'chiller.tanggal_produksi', 'free_stocklist.bb_kondisi')
                                    ->leftJoin('chiller', 'chiller.id', '=', 'free_stocklist.chiller_id')
                                    ->whereIn('free_stocklist.freestock_id', Freestock::select('id')->whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])->where('status', 3)->where('created_at','>=',Applib::DefaultTanggalAudit()))
                                    ->where('free_stocklist.regu', 'whole')
                                    ->where('free_stocklist.created_at','>=',Applib::DefaultTanggalAudit())
                                    ->where('chiller.created_at','>=',Applib::DefaultTanggalAudit())
                                    ->groupBy('free_stocklist.item_id', 'chiller.asal_tujuan', 'chiller.tanggal_produksi', 'free_stocklist.bb_kondisi')
                                    ->get();

            $clone_fg_whole           =   FreestockTemp::select(DB::raw('SUM(qty) AS total'), DB::raw('SUM(berat) AS kg'), DB::raw('SUM(plastik_qty) AS plastik'), 'item_id','kategori')
                                    ->where('regu', 'whole')
                                    ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                    ->whereIn('freestock_id', Freestock::select('id')->whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])->where('status', 3)->where('created_at','>=',Applib::DefaultTanggalAudit()));
                                    // ->groupBy('item_id');
                                    // ->get();
            $clonedata_whole      =   clone $clone_fg_whole;
            $cloneabf_whole       =   clone $clone_fg_whole;
            $clonechiller_whole   =   clone $clone_fg_whole;
            $cloneekspedisi_whole =   clone $clone_fg_whole;
            $clonecs_whole        =   clone $clone_fg_whole;
            $fg_whole             =   $clonedata_whole->groupBy('item_id')->get();
            $countchiller_whole   =   $clonechiller_whole->groupBy('kategori')->where('kategori',0)->count();
            $countchiller_whole2  =   $clonechiller_whole->groupBy('kategori')->where('kategori','')->count();
            $countabf_whole       =   $cloneabf_whole->groupBy('kategori')->where('kategori',1)->count();
            $countekspedisi_whole =   $cloneekspedisi_whole->groupBy('kategori')->where('kategori',2)->count();
            $countcs_whole        =   $clonecs_whole->groupBy('kategori')->where('kategori',3)->count();

            $waktu_awal_whole   =   FreestockList::where('regu', 'whole')
                                    ->whereIn('freestock_id', Freestock::select('id')->whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])->where('status', 3)->where('created_at','>=',Applib::DefaultTanggalAudit()))
                                    ->where('created_at', 'like', '%'.$tanggal.'%')
                                    ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                    ->orderBy('id', 'asc')
                                    ->first();

            $waktu_akhir_whole  =   FreestockTemp::where('regu', 'whole')
                                    ->whereIn('freestock_id', Freestock::select('id')->whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])->where('status', 3)->where('created_at','>=',Applib::DefaultTanggalAudit()))
                                    ->where('created_at', 'like', '%'.$tanggal.'%')
                                    ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                    ->orderBy('id', 'desc')
                                    ->first();

            $jam_kerja_whole   =    ((strtotime($waktu_akhir_whole->created_at ?? "00:00:00") - strtotime($waktu_awal_whole->created_at ?? "00:00:00")));

            $waktu_whole       =    array(
                                        'awal'          => (date('H:i:s', strtotime($waktu_awal_whole->created_at ?? "00:00:00")) ?? "-" ),
                                        'akhir'         => (date('H:i:s', strtotime($waktu_akhir_whole->created_at ?? "00:00:00")) ?? "-" ),
                                        'jam_kerja'     => $jam_kerja_whole
                                    );

            $produksi         =     [
                                    'waktu_whole'       =>  $waktu_whole,
                                    'bb_whole'          =>  $bb_whole,
                                    'bb_tt_whole'       =>  $bb_whole->sum('kg'),
                                    'bb_qty_whole'      =>  $bb_whole->sum('total'),
                                    'fg_whole'          =>  $fg_whole,
                                    'fg_tt_whole'       =>  $fg_whole->sum('kg'),
                                    'fg_qty_whole'      =>  $fg_whole->sum('total'),
                                    'fg_pe_whole'       =>  $fg_whole->sum('plastik'),
                                    'countabf_whole'      =>  $countabf_whole,
                                    'countchiller_whole'  =>  $countchiller_whole+$countchiller_whole2,
                                    'countekspedisi_whole'=>  $countekspedisi_whole,
                                    'countcs_whole'       =>  $countcs_whole,
                                    ];

            return view('admin.pages.laporan.laporan_dashboard_per_page.pageSepuluh', compact('tanggal', 'tanggal_awal', 'tanggal_akhir', 'produksi'));

        } else if ($request->key == 'pageSebelas') {

            $bb_boneless            =   FreestockList::select(DB::raw('SUM(free_stocklist.qty) AS total'), DB::raw('SUM(free_stocklist.berat) AS kg'), 'free_stocklist.item_id', 'chiller.asal_tujuan', 'chiller.tanggal_produksi', 'free_stocklist.bb_kondisi')
                                    ->leftJoin('chiller', 'chiller.id', '=', 'free_stocklist.chiller_id')
                                    ->whereIn('free_stocklist.freestock_id', Freestock::select('id')->whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])->where('status', 3)->where('created_at','>=',Applib::DefaultTanggalAudit()))
                                    ->where('free_stocklist.regu', 'boneless')
                                    ->where('free_stocklist.created_at','>=',Applib::DefaultTanggalAudit())
                                    ->where('chiller.created_at','>=',Applib::DefaultTanggalAudit())
                                    ->groupBy('free_stocklist.item_id', 'chiller.asal_tujuan', 'chiller.tanggal_produksi', 'free_stocklist.bb_kondisi')
                                    ->get();

            $clone_fg_boneless            =   FreestockTemp::select(DB::raw('SUM(qty) AS total'), DB::raw('SUM(berat) AS kg'), 'item_id', DB::raw("SUM(plastik_qty) AS plastik"),'kategori')
                                    ->where('regu', 'boneless')
                                    ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                    ->whereIn('freestock_id', Freestock::select('id')->whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])->where('status', 3)->where('created_at','>=',Applib::DefaultTanggalAudit()));
                                    // ->groupBy('item_id');
                                    // ->get();

            $clonedata_boneless      =   clone $clone_fg_boneless;
            $cloneabf_boneless       =   clone $clone_fg_boneless;
            $clonechiller_boneless   =   clone $clone_fg_boneless;
            $cloneekspedisi_boneless =   clone $clone_fg_boneless;
            $clonecs_boneless        =   clone $clone_fg_boneless;
            $fg_boneless             =   $clonedata_boneless->groupBy('item_id')->get();
            $countchiller_boneless   =   $clonechiller_boneless->groupBy('kategori')->where('kategori',0)->count();
            $countchiller_boneless2  =   $clonechiller_boneless->groupBy('kategori')->where('kategori','')->count();
            $countabf_boneless       =   $cloneabf_boneless->groupBy('kategori')->where('kategori',1)->count();
            $countekspedisi_boneless =   $cloneekspedisi_boneless->groupBy('kategori')->where('kategori',2)->count();
            $countcs_boneless        =   $clonecs_boneless->groupBy('kategori')->where('kategori',3)->count();

            $waktu_awal_boneless    =   FreestockList::where('regu', 'boneless')
                                    ->whereIn('freestock_id', Freestock::select('id')->whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])->where('status', 3)->where('created_at','>=',Applib::DefaultTanggalAudit()))
                                    ->where('created_at', 'like', '%'.$tanggal.'%')
                                    ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                    ->orderBy('id', 'asc')
                                    ->first();

            $waktu_akhir_boneless   =   FreestockTemp::where('regu', 'boneless')
                                    ->whereIn('freestock_id', Freestock::select('id')->whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])->where('status', 3)->where('created_at','>=',Applib::DefaultTanggalAudit()))
                                    ->where('created_at', 'like', '%'.$tanggal.'%')
                                    ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                    ->orderBy('id', 'desc')
                                    ->first();


            $jam_kerja_boneless     = ((strtotime($waktu_akhir_boneless->created_at ?? "00:00:00") - strtotime($waktu_awal_boneless->created_at ?? "00:00:00")));

            $waktu_boneless         = array(
                                        'awal'          => (date('H:i:s', strtotime($waktu_awal_boneless->created_at ?? "00:00:00")) ?? "-" ),
                                        'akhir'         => (date('H:i:s', strtotime($waktu_akhir_boneless->created_at ?? "00:00:00")) ?? "-" ),
                                        'jam_kerja'     => $jam_kerja_boneless
                                    );

            $produksi               =   [
                                        'waktu_boneless'    =>  $waktu_boneless,
                                        'bb_boneless'       =>  $bb_boneless,
                                        'bb_tt_boneless'    =>  $bb_boneless->sum('kg'),
                                        'bb_qty_boneless'   =>  $bb_boneless->sum('total'),
                                        'fg_boneless'       =>  $fg_boneless,
                                        'fg_tt_boneless'    =>  $fg_boneless->sum('kg'),
                                        'fg_qty_boneless'   =>  $fg_boneless->sum('total'),
                                        'fg_pe_boneless'    =>  $fg_boneless->sum('plastik'),
                                        'countabf_boneless'      =>  $countabf_boneless,
                                        'countchiller_boneless'  =>  $countchiller_boneless+$countchiller_boneless2,
                                        'countekspedisi_boneless'=>  $countekspedisi_boneless,
                                        'countcs_boneless'       =>  $countcs_boneless,
                                        ];

            return view('admin.pages.laporan.laporan_dashboard_per_page.pageSebelas', compact('tanggal', 'tanggal_awal', 'tanggal_akhir', 'produksi'));

        } else if ($request->key == 'pageDuaBelas') {
            $bb_frozen          =   FreestockList::select(DB::raw('SUM(free_stocklist.qty) AS total'), DB::raw('SUM(free_stocklist.berat) AS kg'), 'free_stocklist.item_id', 'chiller.asal_tujuan', 'chiller.tanggal_produksi', 'free_stocklist.bb_kondisi')
                                    ->leftJoin('chiller', 'chiller.id', '=', 'free_stocklist.chiller_id')
                                    ->whereIn('free_stocklist.freestock_id', Freestock::select('id')->whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])->where('status', 3)->where('created_at','>=',Applib::DefaultTanggalAudit()))
                                    ->where('free_stocklist.regu', 'frozen')
                                    ->where('free_stocklist.created_at','>=',Applib::DefaultTanggalAudit())
                                    ->where('chiller.created_at','>=',Applib::DefaultTanggalAudit())
                                    ->groupBy('free_stocklist.item_id', 'chiller.asal_tujuan', 'chiller.tanggal_produksi', 'free_stocklist.bb_kondisi')
                                    ->get();

            $clone_fg_frozen     =   FreestockTemp::select(DB::raw('SUM(qty) AS total'), DB::raw('SUM(berat) AS kg'), DB::raw('SUM(plastik_qty) AS plastik'), 'item_id','kategori')
                                    ->where('regu', 'frozen')
                                    ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                    ->whereIn('freestock_id', Freestock::select('id')->whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])->where('status', 3)->where('created_at','>=',Applib::DefaultTanggalAudit()));
                                    // ->groupBy('item_id');
                                    // ->get();
            $clonedata_frozen      =   clone $clone_fg_frozen;
            $cloneabf_frozen       =   clone $clone_fg_frozen;
            $clonechiller_frozen   =   clone $clone_fg_frozen;
            $cloneekspedisi_frozen =   clone $clone_fg_frozen;
            $clonecs_frozen        =   clone $clone_fg_frozen;
            $fg_frozen             =   $clonedata_frozen->groupBy('item_id')->get();
            $countchiller_frozen   =   $clonechiller_frozen->groupBy('kategori')->where('kategori',0)->count();
            $countchiller_frozen2  =   $clonechiller_frozen->groupBy('kategori')->where('kategori','')->count();
            $countabf_frozen       =   $cloneabf_frozen->groupBy('kategori')->where('kategori',1)->count();
            $countekspedisi_frozen =   $cloneekspedisi_frozen->groupBy('kategori')->where('kategori',2)->count();
            $countcs_frozen        =   $clonecs_frozen->groupBy('kategori')->where('kategori',3)->count();

            $waktu_awal_frozen  =   FreestockList::where('regu', 'frozen')
                                    ->whereIn('freestock_id', Freestock::select('id')->whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])->where('status', 3)->where('created_at','>=',Applib::DefaultTanggalAudit()))
                                    ->where('created_at', 'like', '%'.$tanggal.'%')
                                    ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                    ->orderBy('id', 'asc')
                                    ->first();

            $waktu_akhir_frozen =   FreestockTemp::where('regu', 'frozen')
                                    ->whereIn('freestock_id', Freestock::select('id')->whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])->where('status', 3)->where('created_at','>=',Applib::DefaultTanggalAudit()))
                                    ->where('created_at', 'like', '%'.$tanggal.'%')
                                    ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                    ->orderBy('id', 'desc')
                                    ->first();


            $jam_kerja_frozen   = ((strtotime($waktu_akhir_frozen->created_at ?? "00:00:00") - strtotime($waktu_awal_frozen->created_at ?? "00:00:00")));

            $waktu_frozen       = array(
                                    'awal'          => (date('H:i:s', strtotime($waktu_awal_frozen->created_at ?? "00:00:00")) ?? "-" ),
                                    'akhir'         => (date('H:i:s', strtotime($waktu_akhir_frozen->created_at ?? "00:00:00")) ?? "-" ),
                                    'jam_kerja'     => $jam_kerja_frozen
                                );

            $jual_sampingan    =    Bahanbaku::select(DB::raw('SUM(bb_item) AS total'), DB::raw('SUM(bb_berat) AS kg'), 'order_bahan_baku.*')->whereIn('order_item_id', OrderItem::select('id')->whereIn('chiller_out', Chiller::select('id')->whereBetween('tanggal_produksi', [$tanggal_awal, $tanggal_akhir]))->where('created_at','>=',Applib::DefaultTanggalAudit()))
                                    ->whereIn('order_item_id', OrderItem::select('id')->whereIn('item_id', Item::select('id')->where('category_id', 1)))
                                    ->where('proses_ambil', 'sampingan')
                                    ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                    ->groupBy('nama')
                                    ->get();

            $main_product      =    Bahanbaku::select(DB::raw('SUM(bb_item) AS total'), DB::raw('SUM(bb_berat) AS kg'), 'order_bahan_baku.*')->whereIn('order_item_id', OrderItem::select('id')->whereIn('chiller_out', Chiller::select('id')->whereBetween('tanggal_produksi', [$tanggal_awal, $tanggal_akhir]))->where('created_at','>=',Applib::DefaultTanggalAudit()))
                                    ->whereIn('order_item_id', OrderItem::select('id')->whereIn('item_id', Item::select('id')->where('category_id', 4))->where('created_at','>=',Applib::DefaultTanggalAudit()))
                                    ->where('proses_ambil', 'sampingan')
                                    ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                    ->groupBy('nama')
                                    ->get();

            // dd($jual_sampingan);

            $produksi           =   [
                                        'waktu_frozen'      =>  $waktu_frozen,
                                        'bb_frozen'         =>  $bb_frozen,
                                        'bb_tt_frozen'      =>  $bb_frozen->sum('kg'),
                                        'bb_qty_frozen'     =>  $bb_frozen->sum('total'),
                                        'fg_frozen'         =>  $fg_frozen,
                                        'fg_tt_frozen'      =>  $fg_frozen->sum('kg'),
                                        'fg_qty_frozen'     =>  $fg_frozen->sum('total'),
                                        'fg_pe_frozen'      =>  $fg_frozen->sum('plastik'),
                                        'jual_sampingan'    =>  $jual_sampingan,
                                        'main_product'      =>  $main_product,
                                        'countabf_frozen'      =>  $countabf_frozen,
                                        'countchiller_frozen'  =>  $countchiller_frozen+$countchiller_frozen2,
                                        'countekspedisi_frozen'=>  $countekspedisi_frozen,
                                        'countcs_frozen'       =>  $countcs_frozen,
                                    ];

            return view('admin.pages.laporan.laporan_dashboard_per_page.pageDuaBelas', compact('tanggal', 'tanggal_awal', 'tanggal_akhir', 'produksi'));

        } else if ($request->key == 'pageTigaBelas') {
            $order_berat    =   OrderItem::select('orders.nama', DB::raw("SUM(qty) AS jumlah"), DB::raw("SUM(berat) AS kg"), 'customer_id')
                                ->leftJoin('orders', 'orders.id', '=', 'order_items.order_id')
                                ->whereBetween('tanggal_so', [$tanggal_awal, $tanggal_akhir])
                                ->where('order_items.created_at','>=',Applib::DefaultTanggalAudit())
                                ->where('orders.created_at','>=',Applib::DefaultTanggalAudit())
                                ->groupBy('customer_id')
                                ->limit(10)
                                ->orderBy(DB::raw("SUM(berat)"), 'DESC')
                                ->get();

            $total_order_berat = OrderItem::leftJoin('orders', 'orders.id', '=', 'order_items.order_id')
                                ->whereBetween('orders.tanggal_so',[$tanggal_awal,$tanggal_akhir])
                                ->where('order_items.created_at','>=',Applib::DefaultTanggalAudit())
                                ->where('orders.created_at','>=',Applib::DefaultTanggalAudit())
                                ->sum('order_items.berat');

            $order_qty      =   OrderItem::select('orders.nama', DB::raw("SUM(qty) AS jumlah"), DB::raw("SUM(berat) AS kg"), 'customer_id')
                                ->leftJoin('orders', 'orders.id', '=', 'order_items.order_id')
                                ->whereBetween('tanggal_so', [$tanggal_awal, $tanggal_akhir])
                                ->where('order_items.created_at','>=',Applib::DefaultTanggalAudit())
                                ->where('orders.created_at','>=',Applib::DefaultTanggalAudit())
                                ->groupBy('customer_id')
                                ->limit(10)
                                ->orderBy(DB::raw("SUM(qty)"), 'DESC')
                                ->get();

            $retur_besar    =   ReturItem::select(DB::raw("SUM(qty) AS jumlah"), DB::raw("SUM(berat) AS kg"), 'customer_id', 'customers.nama AS konsumen')
                                ->leftJoin('retur', 'retur.id', '=', 'retur_item.retur_id')
                                ->leftJoin('customers', 'customers.id', '=', 'retur.customer_id')
                                ->whereBetween('tanggal_retur', [$tanggal_awal, $tanggal_akhir])
                                ->where('retur_item.created_at','>=',Applib::DefaultTanggalAudit())
                                ->where('retur.created_at','>=',Applib::DefaultTanggalAudit())
                                ->groupBy('customer_id')
                                ->limit(10)
                                ->orderBy(DB::raw("SUM(berat)"), 'DESC')
                                ->get() ;

            $total_retur_besar = ReturItem::leftJoin('retur', 'retur.id', '=', 'retur_item.retur_id')
                                ->leftJoin('customers', 'customers.id', '=', 'retur.customer_id')
                                ->whereBetween('retur.tanggal_retur', [$tanggal_awal, $tanggal_akhir])
                                ->where('retur_item.created_at','>=',Applib::DefaultTanggalAudit())
                                ->where('retur.created_at','>=',Applib::DefaultTanggalAudit())
                                ->groupBy('retur.customer_id')
                                ->sum('retur_item.berat') ;
            $totalPengiriman    = OrderItem::join('orders', 'orders.id', '=', 'order_items.order_id')
                                ->join('items','order_items.item_id','items.id')
                                ->whereBetween('orders.tanggal_kirim', [$tanggal, $tanggal_akhir])
                                ->where('order_items.created_at','>=',Applib::DefaultTanggalAudit())
                                ->groupBy('orders.customer_id')
                                ->sum('order_items.fulfillment_berat');

            $konsumen       =   [
                                    'top10berat'    =>  $order_berat,
                                    'totalberat'    =>  $total_order_berat,
                                    'top10qty'      =>  $order_qty,
                                    'top10retur'    =>  $retur_besar,
                                    'totalretur'    =>  $total_retur_besar,
                                    'totalkiriman'  =>  $totalPengiriman
                                ];

            return view('admin.pages.laporan.laporan_dashboard_per_page.pageTigaBelas', compact('tanggal', 'tanggal_awal', 'tanggal_akhir', 'konsumen','total_retur_besar'));

        }

        return view('admin.pages.laporan.laporan-dashboard', compact('tanggal', 'tanggal_awal', 'tanggal_akhir'));
    }

    public static function get_list_item_non_wo($cek_bb,$cek_fg,$regu,$tanggal_awal,$tanggal_akhir,$bb,$list_nowo){



        $data_bahan_baku = FreestockList::select(DB::raw('(IFNULL(sum(free_stocklist.qty), 0)) as total'),DB::raw('(IFNULL(sum(free_stocklist.berat), 0)) as kg'), 'free_stocklist.item_id', 'chiller.asal_tujuan', 'chiller.tanggal_produksi', 'free_stocklist.bb_kondisi')
                            ->leftJoin('chiller', 'chiller.id', '=', 'free_stocklist.chiller_id')
                            ->join('free_stock', 'free_stock.id', '=', 'free_stocklist.freestock_id')
                            ->where([
                                ['free_stock.regu', '=', $regu],
                                ['free_stock.status', '=', '3'],
                            ])
                            ->whereBetween('free_stock.tanggal', [$tanggal_awal,$tanggal_akhir])
                            ->groupBy('free_stocklist.item_id', 'chiller.asal_tujuan', 'chiller.tanggal_produksi', 'free_stocklist.bb_kondisi');

        $data_non_wo     = 0;
        $result_bb       = 0;
        $id_prod         = [];


        foreach ($cek_bb as $item_bb ) {
            foreach ($cek_fg as $item_prod ) {
                if ($item_bb->item_id == $item_prod->item_id) {
                    $id_prod[] = $item_prod->item_id;
                    $result_bb        = (clone $data_bahan_baku)->whereNull('free_stock.netsuite_send')->where(function($q) use ($id_prod){
                                            $q->whereNotIn('free_stocklist.item_id', $id_prod);
                                            $q->orWhere('chiller.type','bahan-baku');
                                        })
                                        ->get();
                    $data_non_wo_list = (clone $data_bahan_baku)->whereNull('free_stock.netsuite_send')->whereIn('free_stocklist.item_id',$id_prod)->where('chiller.type','hasil-produksi')->get();
                    
                    
                    if ($data_non_wo_list) {
                        $data_non_wo = $data_non_wo_list->sum('kg');
                    } else {
                        $data_non_wo=0;
                    
                    }
                }
            }
        }

        if ($bb == 'bb') {
            // if (count(array($result_bb)) !== 0) {
                
                return $result_bb=collect($result_bb);
            // }else{
            //     return $result_bb=0;

            // }
        }

        if ($list_nowo == 'non_wo') {
            return $data_non_wo;
        }
    }

    public function delivery($id)
    {
        $data   =   Ekspedisi::find($id);

        if ($data) {
            $pdf    =   App::make('dompdf.wrapper');
            $pdf->loadHTML(view('admin.pages.delivery_order', compact('data')));
            return $pdf->stream();
        }

        return redirect()->route('dashboard');
    }

    public function deliveryblank()
    {
        return redirect()->route('dashboard');
    }

    public function lap(Request $request)
    {

        $tanggal            =   $request->tanggal ?? date('Y-m-d');

        $fulfillment        =   Order::select('orders.id', 'orders.nama')
                                ->whereIn('orders.id', OrderItem::select('order_id')->where('created_at','>=',Applib::DefaultTanggalAudit()))
                                ->whereDate('tanggal_so', $tanggal)
                                ->whereNotIn('sales_channel', ["By Product - Paket", "By Product - Retail"])
                                ->leftJoin('order_items', 'order_items.order_id', '=', 'orders.id')
                                ->where(function($query) use($request){
                                    if ($request->status) {
                                        if ($request->status != 'all') {
                                            if ($request->status == 'pending') {
                                                $query->where('order_items.status', NULL);
                                            }
                                            if ($request->status == 'kirim') {
                                                $query->where('order_items.status', '!=', NULL);
                                            }
                                        }
                                    }
                                })
                                ->where('orders.created_at','>=',Applib::DefaultTanggalAudit())
                                ->where('order_items.created_at','>=',Applib::DefaultTanggalAudit())
                                ->groupBy('customer_id')
                                ->get();

        $ekspedisi          =   Ekspedisi::select(DB::raw('SUM(ekspedisi.qty) as totalitem, SUM(berat) as totalberat, COUNT(id) as count'))->get();

        $ekspedisicustomer  =   Ekspedisi_rute::select(DB::raw('count(orders.id) as countcustomer'))
                                ->join('ekspedisi', 'ekspedisi_rute.ekspedisi_id', '=', 'ekspedisi.id')
                                ->join('orders', 'orders.no_so', '=', 'ekspedisi_rute.no_so')
                                ->where('ekspedisi_rute.created_at','>=',Applib::DefaultTanggalAudit())
                                ->get();

        $gudang             =   Product_gudang::whereIn('product_id', Production::select('id')->whereIn('purchasing_id',Purchasing::select('id')->where('tanggal_potong', $tanggal)->where('created_at','>=',Applib::DefaultTanggalAudit())))->select(DB::raw('SUM(qty) as totalitem, SUM(berat) as totalberat'))
                                ->where('jenis_trans', 'masuk')
                                ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                ->get();

        $gudangbb           =   Product_gudang::whereIn('product_id', Production::select('id')->whereIn('purchasing_id', Purchasing::select('id')->where('tanggal_potong', $tanggal))->where('created_at','>=',Applib::DefaultTanggalAudit()))
                                ->select(DB::raw('SUM(qty) as totalitem, SUM(berat) as totalberat'))
                                ->where('type', 'free')
                                ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                ->get();

        $gudangkeluar       =   Product_gudang::whereIn('product_id', Production::select('id')->whereIn('purchasing_id', Purchasing::select('id')->where('tanggal_potong', $tanggal))->where('created_at','>=',Applib::DefaultTanggalAudit()))
                                ->select(DB::raw('order_items.nama_detail, SUM(product_gudang.qty) as totalitem, SUM(product_gudang.berat) as totalberat'))
                                ->where('product_gudang.jenis_trans', 'keluar')
                                ->join('order_items', 'product_gudang.order_id', '=', 'order_items.id')
                                ->where('product_gudang.created_at','>=',Applib::DefaultTanggalAudit())
                                ->groupBy('order_items.nama_detail')
                                ->get();

        $totalitemekspedisi =   0;
        $totalberatekspedisi=   0;
        $countsopir         =   0;
        $countcustomer      =   0;
        $countcus           =   0;

        foreach ($ekspedisi as $eksp) {
            $totalitemekspedisi     +=  $eksp->totalitem;
            $totalberatekspedisi    +=  $eksp->totalberat;
            $countsopir             +=  $eksp->count;
        }

        foreach ($ekspedisicustomer as $rute) {
            $countcus       =   $rute->countcustomer;
            $countcustomer +=   $rute->countcustomer;
        }

        $total  =   [
            'totalitemekspedisi'    => $totalitemekspedisi,
            'totalberatekspedisi'   => $totalberatekspedisi,
            'countsopir'            => $countsopir,
            'rataekspedisi'         => $countsopir != 0 ? ($totalberatekspedisi / $countsopir) : 0,
            'countcus'              => $countcustomer,
            'countcustomer'         => $countcus
        ];

        return view('admin.pages.laporan.fulfillment.data', compact('fulfillment', 'ekspedisi', 'ekspedisicustomer', 'gudang', 'gudangbb', 'gudangkeluar', 'total', 'request'));
    }

    public static  function filter_supplier_lb($supplier)
    {
        $html           = '<select class="form-control select2 d-inline" id="suppliername" name="suppliername" style="width:200px" >"n"';
        $html           .= '<option value=""> Pilih Supplier</option>"n"';
            foreach ($supplier as $spl) {
                $html .= '<option value="' . $spl->id_supplier . '">' . $spl->nama_supplier . '</option>"n"';
            }
        $html           .= "</select>";

        return $html;
    }
    public static  function filter_ukuran_lb($item,$id)
    {
        // dd($item, $id);
        if ($id === '' || $id=== 'all') {
            $html           = '<select class="form-control select2 d-inline" id="ukuran_lb" name="ukuran_lb" style="width:200px" >"n"';
            $html           .= '<option value="all"> Pilih Ukuran </option>"n"';
            $items          = $item->get();
            foreach ($items as $value) {
                $html .= '<option value="' . $value->ukuran_ayam . '">' . $value->ukuran_ayam . '</option>"n"';
            }
            $html           .= "</select>";
        }
        else{

            $html           = '<select class="form-control select2 d-inline" id="ukuran_lb" name="ukuran_lb" style="width:200px" >"n"';
            $html           .= '<option value="all"> Pilih Ukuran </option>"n"';
            $items          = $item;
            foreach ($items as $value) {
                $html .= '<option value="' . $value->ukuran_ayam . '">' . $value->ukuran_ayam . '</option>"n"';
            }
            $html           .= "</select>";

        }
        return $html;
    }
}
