<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Grading;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product_gudang;
use App\Models\Production;
use App\Models\Purchasing;
use App\Models\ReturItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ReportProgressController extends Controller
{
    public function view_progress(Request $request){

        if($request->role == 'marketing'){
            if($request->key == 'marketing'){
                $tanggal        =   $request->tanggal ?? Carbon::now()->format('Y-m-d');
                $tanggalakhir   =   $request->tanggalakhir ?? Carbon::now()->format('Y-m-d');
                $tokenGenerate  = "S29kZSBhY2FrIGtvZGUgZGlhY2FrIGFjYWsgYWNhayBhY2FrIGtvZGUgYmlhciBkYXBhdCBrb2RlIGtvZGUgeWFuZyBkaWFjYWs=";
                $token          = "LbFMmcNeGOHXiTHMAarQMSDAafVhGILWqe3C2qeDKK7BOAwgRAVXdFB4KZCy.1db55a1e47f0154d8eb0b61c29de0904701c84ee89971576fad2efdc27c07710";
                $link           = $request->getUri();
                $subsidiary     = $request->name;
                $tToken         = $request->_token;
                $gettoken       = $request->GenerateToken;
                if($tokenGenerate == $gettoken && $subsidiary != '' && $token == $tToken){
                    $cust       = self::dd_customer();
                    return view('noAuth.marketing.index',compact('tanggal','tanggalakhir','link','subsidiary','tToken','gettoken','cust'));
                }else{
                    abort(404);
                }
            }else
            if($request->key == 'retur'){
                $tanggalawal    =   $request->tanggalawal ?? date('Y-m-d');
                $tanggalakhir   =   $request->tanggalakhir ?? date('Y-m-d');

                $tokenGenerate  = "aHlwZXJsaW5rIHVudHVrIFNPIENHTCBkYW4gZGl0dWp1a2FuIHVudHVrIG1lbGloYXQgcHJvZ3Jlc3MgUUMgUmV0dXI=";
                $token          = "LbFMmcNeGOHXiTHMAarQMSDAafVhGILWqe3C2qeDKK7BOAwgRAVXdFB4KZCy.1db55a1e47f0154d8eb0b61c29de0904701c84ee89971576fad2efdc27c07710";
                $link           = $request->getUri();
                $subsidiary     = $request->name;
                $tToken         = $request->_token;
                $gettoken       = $request->GenerateToken;
                if($tokenGenerate == $gettoken && $subsidiary != '' && $token == $tToken){
                    return view('noAuth.retur.index',compact('tanggalawal','tanggalakhir','link','subsidiary','tToken','gettoken'));
                }else{
                    abort(404);
                }
            }
        }
        else
        if($request->role == 'purchasing'){
            $tanggalawal    =   $request->tanggalawal ?? date('Y-m-d');
            $tanggalakhir   =   $request->tanggalakhir ?? date('Y-m-d');

            $tokenGenerate  = "bG9yIHJlbCBkdWwgcmVsIGxvciByZWwgZG9sIHJlbCByZWwgZGkgbG9yIHJlbCBlbmNrcnlwc2kgYWNhayBhY2FrYWFu";
            $token          = "LbFMmcNeGOHXiTHMAarQMSDAafVhGILWqe3C2qeDKK7BOAwgRAVXdFB4KZCy.1db55a1e47f0154d8eb0b61c29de0904701c84ee89971576fad2efdc27c07710";
            $link           = $request->getUri();
            $subsidiary     = $request->name;
            $tToken         = $request->_token;
            $gettoken       = $request->GenerateToken;
            if($tokenGenerate == $gettoken && $subsidiary != '' && $token == $tToken){
                return view('noAuth.purchasing.index',compact('tanggalawal','tanggalakhir','link','subsidiary','tToken','gettoken'));

            }else{
                abort(404);
            }
        }
        // else
        // if($request->key == 'progress_retur'){
        //     $tanggalawal    =   $request->tanggalawal ?? date('Y-m-d');
        //     $tanggalakhir   =   $request->tanggalakhir ?? date('Y-m-d');

        //     $tokenGenerate  = "aHlwZXJsaW5rIHVudHVrIFNPIENHTCBkYW4gZGl0dWp1a2FuIHVudHVrIG1lbGloYXQgcHJvZ3Jlc3MgUUMgUmV0dXI=";
        //     $link           = $request->getUri();
        //     $subsidiary     = $request->name;
        //     $tToken         = $request->_token;
        //     $gettoken       = $request->GenerateToken;
        //     if($tokenGenerate == $gettoken && $subsidiary != ''){
        //         return view('noAuth.retur.index',compact('tanggalawal','tanggalakhir','link','subsidiary','tToken','gettoken'));
        //     }else{
        //         abort(404);
        //     }
        // }
    }

    public function view_data(Request $request){
        if($request->subkey == 'view_data_marketing'){
            $tanggal        =   $request->tanggal ?? Carbon::now()->format('Y-m-d');
            $caricustomer   =   $request->searchcustomer ?? '';
            $fulfillment    =   Order::whereIn('id', OrderItem::select('order_id')
                                ->where(function($q) use ($caricustomer){
                                    if($caricustomer != 'all'){
                                        $q->where('customer_id',$caricustomer);
                                    }
                                })
                                ->where('status', '>=', 2))
                                ->where('tanggal_kirim', $tanggal)
                                ->get();
            if($request->detailkey == 'detail_order_fulfillment'){
                $fulfillment_detail    =  OrderItem::where('order_id',$request->id)->get();
                return view('noAuth.marketing.component.fulfillment.detailfulfillment',compact('fulfillment_detail'));
            }
            return view('noAuth.marketing.component.fulfillment.datamarketing',compact('fulfillment','tanggal'));
        }
        else
        if($request->subkey == 'view_data_stockbyitem'){
            $tanggalawal        =   $request->tanggalawal ?? date('Y-m-d');
            $tanggalakhir       =   $request->tanggalakhir ?? date('Y-m-d');

            // UNTUK PER 4 FEBRUARI

            $listayam4feb       = Product_gudang::selectRaw('nama, (SUM(IF(jenis_trans="masuk",qty,0)) - SUM(IF(jenis_trans="masuk",0,qty))) as jumlah_nama')->groupBy('nama')
                                ->whereBetween('production_date', [$tanggalawal, $tanggalakhir])
                                ->where('production_date', '>=', '2022-02-04')
                                ->where('status', 2)
                                ->where('qty' ,'>', 0)
                                ->orderBy("nama")
                                ->get();

            $listplastik4feb    = Product_gudang::selectRaw('packaging, (SUM(IF(jenis_trans="masuk",qty,0)) - SUM(IF(jenis_trans="masuk",0,qty))) as jumlah_plastik')->groupBy('packaging')
                                ->whereBetween('production_date', [$tanggalawal, $tanggalakhir])
                                ->where('production_date', '>=', '2022-02-04')
                                ->where('packaging', '!=', null)
                                ->where('qty' ,'>', 0)
                                ->where('status', 2)
                                ->orderBy('packaging')
                                ->get();

            $listkonsumen4feb   = Product_gudang::with('konsumen')->select('customer_id')->selectRaw('(SUM(IF(jenis_trans="masuk",qty,0)) - SUM(IF(jenis_trans="masuk",0,qty))) as jumlah_konsumen')
                                ->whereBetween('production_date', [$tanggalawal, $tanggalakhir])
                                ->where('production_date', '>=', '2022-02-04')
                                ->where('status', 2)
                                ->where('qty' ,'>', 0)
                                ->groupBy('customer_id')
                                ->get();

            $liststock4feb      = Product_gudang::with('productgudang')->select('gudang_id')->selectRaw('(SUM(IF(jenis_trans="masuk",qty,0)) - SUM(IF(jenis_trans="masuk",0,qty))) as jumlah_stock')
                                ->whereBetween('production_date', [$tanggalawal, $tanggalakhir])
                                ->where('production_date', '>=', '2022-02-04')
                                ->where('status', 2)
                                ->where('qty' ,'>', 0)
                                ->groupBy('gudang_id')
                                ->get();

            if($request->detailkey == 'detailkonsumen'){
                $tanggalawal    =   $request->tanggalawal ?? date('Y-m-d');
                $tanggalakhir   =   $request->tanggalakhir ?? date('Y-m-d');

                $data           =   Product_gudang::where('customer_id', $request->customer_id)
                                    ->whereBetween('production_date', [$tanggalawal, $tanggalakhir])
                                    ->where('production_date', '>=', '2022-02-04')
                                    ->orderBy('nama', 'asc')
                                    ->paginate(20);

                $gudang         =   Product_gudang::selectRaw('gudang_id, (SUM(IF(jenis_trans="masuk",qty,0)) - SUM(IF(jenis_trans="masuk",0,qty))) as jumlah_qty, (SUM(IF(jenis_trans="masuk",berat,0)) - SUM(IF(jenis_trans="masuk",0,berat))) as jumlah_berat')->groupBy('gudang_id')
                                    ->whereBetween('production_date', [$tanggalawal, $tanggalakhir])
                                    ->where('status', 2)
                                    ->where('customer_id', $request->customer_id)
                                    ->orderBy("gudang_id", "asc")
                                    ->get();

                return view('noAuth.marketing.component.soh.detail_konsumen', compact('tanggalawal', 'tanggalakhir', 'data','gudang'));
            }
            return view('noAuth.marketing.component.soh.stock', compact('request', 'listayam4feb', 'listplastik4feb', 'listkonsumen4feb', 'liststock4feb', 'tanggalawal','tanggalakhir'));
        }
        else
        if($request->subkey == 'view_data_purchasing'){
            $tanggalawal    =   $request->tanggalawal ?? date('Y-m-d');
            $tanggalakhir   =   $request->tanggalakhir ?? date('Y-m-d');
            $tanggal        =   $tanggalakhir;
            $data           =   Production::where('no_urut', '!=', NULL)
                                        ->whereIn('purchasing_id', Purchasing::select('id')
                                        ->whereIn('type_po', ['PO LB','PO Maklon']))
                                        ->whereBetween('prod_tanggal_potong', [$tanggalawal, $tanggalakhir])
                                        // ->where(function ($query) use ($tanggal) {
                                        //     if ($tanggal != '') {
                                        //         $query->whereDate('prod_tanggal_potong', $tanggal);
                                        //     } else {
                                        //         $query->whereDate('prod_tanggal_potong', Carbon::now());
                                        //     }
                                        // })
                                        ->whereIn('sc_status', [1, 0])
                                        ->orderByRaw('prod_tanggal_potong ASC, no_urut ASC')
                                        ->get();

            $mobil_lama     =   Production::where('no_urut', '!=', NULL)
                                ->whereIn('purchasing_id', Purchasing::select('id')
                                        ->whereIn('type_po', ['PO LB','PO Maklon'])
                                        ->where(function ($query) use ($tanggal) {
                                            $query->whereDate('tanggal_potong', date('Y-m-d', strtotime('yesterday')));
                                        })
                                    )
                                ->whereBetween('prod_tanggal_potong', [$tanggalawal, $tanggalakhir])
                                ->where('sc_status', 1)
                                ->whereIn('lpah_status', [NULL, 1, 2])
                                ->whereIn('lpah_tanggal_potong', [date('Y-m-d')])
                                ->get();
                                $done       =   Production::where('lpah_status', 1)
                                ->whereIn('purchasing_id', Purchasing::select('id'))
                                ->whereBetween('prod_tanggal_potong', [$tanggalawal, $tanggalakhir])
                                // ->where(function ($query) use ($tanggal) {
                                //     if ($tanggal != '') {
                                //         $query->whereDate('prod_tanggal_potong', $tanggal);
                                //     } else {
                                //         $query->whereDate('prod_tanggal_potong', Carbon::now());
                                //     }
                                // })
                                ->count('id');

                $pending    =   Production::where('sc_status', 1)
                                ->whereIn('purchasing_id', Purchasing::select('id'))
                                ->whereBetween('prod_tanggal_potong', [$tanggalawal, $tanggalakhir])
                                // ->where(function ($query) use ($tanggal) {
                                //     if ($tanggal != '') {
                                //         $query->whereDate('prod_tanggal_potong', $tanggal);
                                //     } else {
                                //         $query->whereDate('prod_tanggal_potong', Carbon::now());
                                //     }
                                // })
                                ->count('id');

                $berat      =   Production::where('sc_status', 1)
                                ->whereIn('purchasing_id', Purchasing::select('id'))
                                ->whereBetween('prod_tanggal_potong', [$tanggalawal, $tanggalakhir])
                                // ->where(function ($query) use ($tanggal) {
                                //     if ($tanggal != '') {
                                //         $query->whereDate('prod_tanggal_potong', $tanggal);
                                //     } else {
                                //         $query->whereDate('prod_tanggal_potong', Carbon::now());
                                //     }
                                // })
                                ->sum('sc_berat_do');

                $ekor      =   Production::where('sc_status', 1)
                                ->whereIn('purchasing_id', Purchasing::select('id'))
                                ->whereBetween('prod_tanggal_potong', [$tanggalawal, $tanggalakhir])
                                // ->where(function ($query) use ($tanggal) {
                                //     if ($tanggal != '') {
                                //         $query->whereDate('prod_tanggal_potong', $tanggal);
                                //     } else {
                                //         $query->whereDate('prod_tanggal_potong', Carbon::now());
                                //     }
                                // })
                                ->sum('sc_ekor_do');

            $hitung =   [
                'done'          =>  $done,
                'pending'       =>  $pending,
                'berat_total'   =>  $berat,
                'total_ekor'    =>  $ekor,
            ];
            return view('noAuth.purchasing.component.lpah.datalpah',compact('tanggalawal','tanggalakhir','data','mobil_lama','hitung'));
        }
        else
        if($request->subkey == 'view_data_retur'){
            $tanggalawal    =   $request->tanggalawal ?? date('Y-m-d');
            $tanggal_akhir  =   $request->tanggalakhir ?? date('Y-m-d');
            $kata           =   $request->kata ?? '';
            $retur_list     =   ReturItem::whereBetween('retur.tanggal_retur', [$tanggalawal, $tanggal_akhir])
                                            ->where(function ($query2) use ($request, $kata) {
                                                if ($request->tujuan != "") {
                                                    $query2->orWhere('unit', $request->tujuan);
                                                    $query2->orWhere('tujuan', $request->tujuan);
                                                }

                                                if ($kata != "") {
                                                    $query2->orWhere('catatan', 'like', "%" . $kata . "%");
                                                    $query2->orWhere('tujuan', 'like', '%' . $kata . '%');
                                                    $query2->orWhere('retur_item.kategori', 'like', '%' . $kata . '%');
                                                    $query2->orWhere('satuan', 'like', '%' . $kata . '%');
                                                    $query2->orWhere('penanganan', 'like', '%' . $kata . '%');
                                                    $query2->orWhere('retur.no_so', 'like', "%" . $kata . "%");
                                                    $query2->orWhere('retur.no_ra', 'like', '%' . $kata . '%');
                                                    $query2->orWhere('customers.nama', 'like', '%' . $kata . '%');
                                                }

                                                if ($request->kategori != "") {
                                                    $query2->orWhere('kategori', $request->kategori);
                                                }

                                                if ($request->satuan != "") {
                                                    $query2->orWhere('satuan', $request->satuan);
                                                }

                                                if ($request->penanganan != "") {
                                                    $query2->orWhere('penanganan', 'like', '%' . $request->penanganan . '%');
                                                }
                                            })
                                            ->leftjoin('retur', 'retur.id', '=', 'retur_item.retur_id')
                                            ->leftjoin('customers', 'retur.customer_id', '=', 'customers.id')
                                            ->orderBy('retur.id', 'desc')
                                            ->whereIn('retur.status', [1, 2])
                                            ->get();

            return view('noAuth.retur.component.dataretur',compact('retur_list', 'tanggalawal'));
        }
        else
        if($request->subkey == 'view_data_livebird'){

            $tanggal_awal                   = $request->tanggal_awal;
            $tanggal_akhir                  = $request->tanggal_akhir;
            $filterLB                       =  Production::Leftjoin('purchasing', 'productions.purchasing_id', '=', 'purchasing.id')
                                                        ->Leftjoin('supplier', 'supplier.id', '=', 'purchasing.supplier_id')
                                                        ->where('purchasing.jenis_po', 'PO LB')
                                                        ->select('productions.*', 'supplier.nama', 'purchasing.jenis_po','purchasing.ukuran_ayam')
                                                        ->whereBetween('productions.lpah_tanggal_potong', [$tanggal_awal, $tanggal_akhir]);

            $cloneSebaranLB                 = clone $filterLB;
            $cloneGroupByItem               = clone $filterLB;
            $cloneGetDataTotal              = clone $filterLB;

            $groupByItem                    = $cloneGroupByItem->groupBy('purchasing.ukuran_ayam')->pluck('purchasing.ukuran_ayam');

            $defaultukuran                  = '';
            $filter_ukuran_lb               = self::filter_all_ukuran_lb($groupByItem, $defaultukuran);

            if($request->loadSupplier == 'YES'){
                $itemid                     = $request->itemid;
                $tanggal_awal               = $request->tanggal_awal;
                $tanggal_akhir              = $request->tanggal_akhir;

                $dataSebaranLB              = $cloneSebaranLB->select('supplier.nama AS nama_supplier', 'supplier.id AS id_supplier')
                                                            ->where(function($q) use ($itemid){
                                                                if($itemid != 'all'){
                                                                    $q->where('ukuran_ayam',$itemid);
                                                                }
                                                            })
                                                            ->groupBy('supplier.id')
                                                            ->get();

                $filter_supplier_lb         = self::filter_supplier_lb($dataSebaranLB);

                if($request->itemid == 'all'){
                    $getDataSupplier            = [];
                    foreach($groupByItem as $dataItem) {
                        $getDataSupplier[]      = Production::sebaran_lb_supplier($dataSebaranLB, $dataItem, $tanggal_awal, $tanggal_akhir);
                    }
                    $supplier                   = [] ;
                    $dataLBSupplier             = $dataSebaranLB->pluck('nama_supplier');

                    for($i = 0; $i < count($getDataSupplier); $i ++) {
                        $data_LB   = [];
                        for($x = 0; $x < count($dataLBSupplier); $x ++) {
                            $data_LB[] = isset($getDataSupplier[$i][$x]['qty_ekor_lb']) ? (integer)$getDataSupplier[$i][$x]['qty_ekor_lb'] : 0;
                        }

                        $supplier[] = array(
                            'name' => $groupByItem[$i],
                            'data' => $data_LB
                        );
                    }

                    $supplier = json_encode($supplier);

                }else{
                    $getDataSupplier            = [];
                    $getDataSupplier[]          = Production::sebaran_lb_supplier($dataSebaranLB, $itemid, $tanggal_awal, $tanggal_akhir);

                    $supplier                   = [] ;
                    $dataLBSupplier             = $dataSebaranLB->pluck('nama_supplier');

                    for($i = 0; $i < count($getDataSupplier); $i ++) {
                        $data_LB   = [];
                        for($x = 0; $x < count($dataLBSupplier); $x ++) {
                            $data_LB[] = isset($getDataSupplier[$i][$x]['qty_ekor_lb']) ? (integer)$getDataSupplier[$i][$x]['qty_ekor_lb'] : 0;
                        }

                        $supplier[] = array(
                            'data' => $data_LB,
                            'name' => $itemid
                        );
                    }

                    $supplier = json_encode($supplier);
                }
                return view('noAuth.purchasing.component.sebaran_lb.filter_all_supplier_lb', compact('supplier', 'dataLBSupplier','filter_supplier_lb'));
            }

            if($request->loadDetailSupplier == 'YES'){
                // dd($request->all());
                $itemid                 = $request->itemid;
                $supplier               = $request->supplier;
                $dataDetail             = clone $filterLB;
                $getData                = $dataDetail
                                                    ->where(function($query) use ($itemid,$supplier){
                                                        if($itemid != 'all' && $supplier == NULL){
                                                            $query->where('ukuran_ayam',$itemid);
                                                        }
                                                        if($itemid != 'all' && $supplier != NULL){
                                                            $query->where('ukuran_ayam',$itemid);
                                                            $query->where('supplier_id',$supplier);
                                                        }
                                                        if($itemid == 'all' && $supplier != NULL){
                                                            $query->where('supplier_id',$supplier);
                                                        }
                                                    })
                                                    ->groupBy('supplier.id','productions.sc_nama_kandang','productions.lpah_tanggal_potong','purchasing.ukuran_ayam')
                                                    ->orderBy('lpah_tanggal_potong')
                                                    ->get();
                $sku                    = '12111';
                $DataItem               = Item::where('sku','LIKE',$sku.'%')->where('sku','NOT LIKE','%00000')->take(23)->get();
                $arrayItem              = array();
                foreach($getData as $item){
                    $dataGrading            = Grading::join('items','grading.item_id','items.id')->where('trans_id',$item->id)->select('trans_id','stock_item','stock_berat','nama')->get();
                    $dataArray          = array();

                    foreach($dataGrading as $databaru){
                        $dataArray[]    = array(
                            'namabaru'  => substr($databaru->nama,-5),
                            'itembaru'  => $databaru->stock_item,
                            'beratbaru' => $databaru->stock_berat
                        );
                    }

                    $uk03_04        = 0;
                    $uk04_05        = 0;
                    $uk05_06        = 0;
                    $uk06_07        = 0;
                    $uk07_08        = 0;
                    $uk08_09        = 0;
                    $uk09_10        = 0;
                    $uk10_11        = 0;
                    $uk11_12        = 0;
                    $uk12_13        = 0;
                    $uk13_14        = 0;
                    $uk14_15        = 0;
                    $uk15_16        = 0;
                    $uk16_17        = 0;
                    $uk17_18        = 0;
                    $uk18_19        = 0;
                    $uk19_20        = 0;
                    $uk20_21        = 0;
                    $uk21_22        = 0;
                    $uk22_23        = 0;
                    $uk23_24        = 0;
                    $uk24_25        = 0;
                    $uk25_UP        = 0;

                    foreach($dataArray as $rawitem){
                        if($rawitem['namabaru'] == '03-04'){
                            $uk03_04    += $rawitem['beratbaru'];
                        }
                    }
                    foreach($dataArray as $rawitem){
                        if($rawitem['namabaru'] == '04-05'){
                            $uk04_05    += $rawitem['beratbaru'];
                        }
                    }
                    foreach($dataArray as $rawitem){
                        if($rawitem['namabaru'] == '05-06'){
                            $uk05_06    += $rawitem['beratbaru'];
                        }
                    }
                    foreach($dataArray as $rawitem){
                        if($rawitem['namabaru'] == '06-07'){
                            $uk06_07    += $rawitem['beratbaru'];
                        }
                    }
                    foreach($dataArray as $rawitem){
                        if($rawitem['namabaru'] == '07-08'){
                            $uk07_08    += $rawitem['beratbaru'];
                        }
                    }
                    foreach($dataArray as $rawitem){
                        if($rawitem['namabaru'] == '08-09'){
                            $uk08_09    += $rawitem['beratbaru'];
                        }
                    }
                    foreach($dataArray as $rawitem){
                        if($rawitem['namabaru'] == '09-10'){
                            $uk09_10    += $rawitem['beratbaru'];
                        }
                    }
                    foreach($dataArray as $rawitem){
                        if($rawitem['namabaru'] == '10-11'){
                            $uk10_11    += $rawitem['beratbaru'];
                        }
                    }
                    foreach($dataArray as $rawitem){
                        if($rawitem['namabaru'] == '11-12'){
                            $uk11_12    += $rawitem['beratbaru'];
                        }
                    }
                    foreach($dataArray as $rawitem){
                        if($rawitem['namabaru'] == '12-13'){
                            $uk12_13    += $rawitem['beratbaru'];
                        }
                    }
                    foreach($dataArray as $rawitem){
                        if($rawitem['namabaru'] == '13-14'){
                            $uk13_14    += $rawitem['beratbaru'];
                        }
                    }
                    foreach($dataArray as $rawitem){
                        if($rawitem['namabaru'] == '14-15'){
                            $uk14_15    += $rawitem['beratbaru'];
                        }
                    }
                    foreach($dataArray as $rawitem){
                        if($rawitem['namabaru'] == '15-16'){
                            $uk15_16    += $rawitem['beratbaru'];
                        }
                    }
                    foreach($dataArray as $rawitem){
                        if($rawitem['namabaru'] == '16-17'){
                            $uk16_17    += $rawitem['beratbaru'];
                        }
                    }
                    foreach($dataArray as $rawitem){
                        if($rawitem['namabaru'] == '17-18'){
                            $uk17_18    += $rawitem['beratbaru'];
                        }
                    }
                    foreach($dataArray as $rawitem){
                        if($rawitem['namabaru'] == '18-19'){
                            $uk18_19    += $rawitem['beratbaru'];
                        }
                    }
                    foreach($dataArray as $rawitem){
                        if($rawitem['namabaru'] == '19-20'){
                            $uk19_20    += $rawitem['beratbaru'];
                        }
                    }
                    foreach($dataArray as $rawitem){
                        if($rawitem['namabaru'] == '20-21'){
                            $uk20_21    += $rawitem['beratbaru'];
                        }
                    }
                    foreach($dataArray as $rawitem){
                        if($rawitem['namabaru'] == '21-22'){
                            $uk21_22    += $rawitem['beratbaru'];
                        }
                    }
                    foreach($dataArray as $rawitem){
                        if($rawitem['namabaru'] == '22-23'){
                            $uk22_23    += $rawitem['beratbaru'];
                        }
                    }
                    foreach($dataArray as $rawitem){
                        if($rawitem['namabaru'] == '23-24'){
                            $uk23_24    += $rawitem['beratbaru'];
                        }
                    }
                    foreach($dataArray as $rawitem){
                        if($rawitem['namabaru'] == '24-25'){
                            $uk24_25    += $rawitem['beratbaru'];
                        }
                    }
                    foreach($dataArray as $rawitem){
                        if($rawitem['namabaru'] == '25 UP'){
                            $uk25_UP    += $rawitem['beratbaru'];
                        }
                    }

                    $arrayItem[]    = array(
                        // 'trans_id'          => $item->id,
                        'tanggal'           => $item->lpah_tanggal_potong,
                        'supplier'          => $item->nama,
                        // 'supplier'          => $item->nama."(". $item->id .")",
                        'kandang'           => $item->sc_nama_kandang,
                        'ukuran'            => $item->ukuran_ayam,
                        'rerata_terima'     => $item->lpah_rerata_terima,
                        'persen_susut'      => $item->lpah_persen_susut,
                        'ekor_mati'         => $item->qc_ekor_ayam_mati,
                        'uk03_04'           => $uk03_04,
                        'uk04_05'           => $uk04_05,
                        'uk05_06'           => $uk05_06,
                        'uk06_07'           => $uk06_07,
                        'uk07_08'           => $uk07_08,
                        'uk08_09'           => $uk08_09,
                        'uk09_10'           => $uk09_10,
                        'uk10_11'           => $uk10_11,
                        'uk11_12'           => $uk11_12,
                        'uk12_13'           => $uk12_13,
                        'uk13_14'           => $uk13_14,
                        'uk14_15'           => $uk14_15,
                        'uk15_16'           => $uk15_16,
                        'uk16_17'           => $uk16_17,
                        'uk17_18'           => $uk17_18,
                        'uk18_19'           => $uk18_19,
                        'uk19_20'           => $uk19_20,
                        'uk20_21'           => $uk20_21,
                        'uk21_22'           => $uk21_22,
                        'uk22_23'           => $uk22_23,
                        'uk23_24'           => $uk23_24,
                        'uk24_25'           => $uk24_25,
                        'uk25_UP'           => $uk25_UP
                    );
                }
                $arrayBroiler            = [];
                foreach($DataItem as $new){
                    $arrayBroiler[] = "Broiler ".substr($new->nama,-5);
                }

                $arrayBroiler           = json_encode($arrayBroiler);
                return view('noAuth.purchasing.component.sebaran_lb.detail_filter_lb',compact('getData','DataItem','arrayItem','arrayBroiler'));
            }
            return view('noAuth.purchasing.component.sebaran_lb.view_all_supplier_lb', compact('filter_ukuran_lb','tanggal_awal','tanggal_akhir'));
        }
    }

    public static function dd_customer(){
        // $cust           = Customer::select('id','nama','kode')->where('kode','!=',null)->orderBy('id','DESC')->get();
        $cust           = Order::select('customer_id','nama')->groupBy('customer_id','nama')->orderBy('id','DESC')->get();
        $html           = '<select class="form-control select2 d-inline" id="searchcustomer" name="searchcustomer" style="width:400px" >"n"';
            $html           .= '<option value="all"> Semua </option>"n"';
            foreach ($cust as $value) {
                // if($subsidiary == substr($value->kode,1,3)){
                    $html .= '<option value="' . $value->customer_id . '">' . $value->nama . '</option>"n"';
                // }
            }
            $html           .= "</select>";
            return $html;
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
    public static  function filter_all_supplier_lb($supplier)
    {
        $html           = '<select class="form-control select2 d-inline" id="allsuppliername" name="allsuppliername" style="width:200px" >"n"';
        $html           .= '<option value=""> Pilih Supplier</option>"n"';
            foreach ($supplier as $spl) {
                $html .= '<option value="' . $spl->id_supplier . '">' . $spl->nama_supplier . '</option>"n"';
            }
        $html           .= "</select>";

        return $html;
    }
    public static  function filter_all_ukuran_lb($item,$default)
    {
        // dd($item, $id);
        if ($default === '' || $default=== 'all') {
            $html           = '<select class="form-control select2 d-inline" id="all_ukuran_lb" name="all_ukuran_lb" style="width:200px" >"n"';
            $html           .= '<option value="all"> Pilih Ukuran </option>"n"';
            $items          = $item;
            foreach ($items as $value) {
                $html .= '<option value="' . $value . '">' . $value . '</option>"n"';
            }
            $html           .= "</select>";
        }
        else{

            $html           = '<select class="form-control select2 d-inline" id="all_ukuran_lb" name="all_ukuran_lb" style="width:200px" >"n"';
            $html           .= '<option value="all"> Pilih Ukuran </option>"n"';
            $items          = $item;
            foreach ($items as $value) {
                $html .= '<option value="' . $value . '">' . $value . '</option>"n"';
            }
            $html           .= "</select>";

        }
        return $html;
    }
}
