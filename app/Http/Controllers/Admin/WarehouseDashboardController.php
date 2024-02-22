<?php

namespace App\Http\Controllers\Admin;

use App\Classes\Applib;
use App\Http\Controllers\Controller;
use App\Models\Abf;
use App\Models\Grading;
use App\Models\Gudang;
use App\Models\Item;
use App\Models\Product_gudang;
use App\Models\Production;
use App\Models\Purchasing;
use App\Models\Thawing;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WarehouseDashboardController extends Controller
{
    public function index(Request $request)
    {

        $customer           =   $request->customer ?? NULL;
        $item               =   $request->item ?? NULL;
        $ordering           =   $request->ordering ?? 'product_gudang.nama';
        $order_by           =   $request->order_by ?? 'desc';
        $category_soh       =   $request->category_soh ?? "";
        $marinated_soh      =   $request->marinated_soh ?? "";
        $itemname_soh       =   $request->itemname_soh ?? "";
        $grade_soh          =   $request->grade_soh ?? "";
        $customername_soh   =   $request->customername_soh ?? "";

        // $input_type         =   'semua';
        $input_type         =   'semua';

        $thawing        =   Thawing::where('status', 1)->where('tanggal_request', date('Y-m-d'))->count() ;

        if($order_by=="desc"){
            $order_by = "desc";
        }else{
            $order_by = "asc";
        }

        if($ordering=="customer"){
            $ordering = "customers.nama";
        }else
        if($ordering=="item"){
            $ordering = "product_gudang.nama";
        }else
        if($ordering=="qty"){
            $ordering = "qty_saldo_akhir";
        }else
        if($ordering=="berat"){
            $ordering = "berat_saldo_akhir";
        }else{
            $ordering       =   'product_gudang.nama';
        }

        $tanggal        =   ($request->tanggal_gudang ?? date("Y-m-d")) ;

        $tahunGudang    =   date('Y', strtotime($tanggal));
        $bulanGudang    =   date('m', strtotime($tanggal));
        $hariGudang     =   date('d', strtotime($tanggal));

        $firstMonth     = $tahunGudang.'-'.$bulanGudang.'-01';

        $cari           =   $request->cari ?? "" ;

        $tanggal_awal   =   $request->tanggal_awal ?? date("Y-m-d") ;
        $tanggal_akhir  =   $request->tanggal_akhir ?? date("Y-m-d") ;

        $list_customer = Product_gudang::select('customers.id', 'customers.nama')
                                                ->join('customers', 'customers.id', '=', 'product_gudang.customer_id')
                                                ->groupBy('customer_id')
                                                ->get();

        $day_before = date( 'Y-m-d', strtotime( $tanggal . ' -1 day' ) );

        if ($request->key == 'soh') {

                    if (env('NET_SUBSIDIARY', 'CGL')=='CGL') {
                        if ($tanggal == '2023-05-27') {
                            $data       = DB::select("CALL getSOHOpenBalanceCGL('$tanggal','$item','$category_soh','$marinated_soh','$itemname_soh','$grade_soh','$customername_soh','$cari','$ordering','$order_by','$firstMonth')");
                            $data       = collect($data)->select(DB::raw("SUM(qty_saldo_awal) AS total_saep"));
                            
                        } else {
                            $data       = DB::select("CALL getSOHCGL('$tanggal','$item','$category_soh','$marinated_soh','$itemname_soh','$grade_soh','$customername_soh','$cari','$ordering','$order_by','$firstMonth')");

                        }
                    }else
                    if (env('NET_SUBSIDIARY', 'EBA')=='EBA'){
                        if ($tanggal == '2023-05-05') {
                            $data       = DB::select("CALL getSOHOpenBalanceEBA('$tanggal','$item','$category_soh','$marinated_soh','$itemname_soh','$grade_soh','$customername_soh','$cari','$ordering','$order_by','$firstMonth')");

                        } else {
                            $data       = DB::select("CALL getSOHEBA('$tanggal','$item','$category_soh','$marinated_soh','$itemname_soh','$grade_soh','$customername_soh','$cari','$ordering','$order_by','$firstMonth')");

                        }
                    }


                    return view('admin.pages.warehouse.soh.data', compact('data', 'tanggal', 'list_customer', 'customer', 'order_by', 'ordering','cari','item','order_by','firstMonth','category_soh','marinated_soh','itemname_soh','grade_soh','customername_soh'));


        } if ($request->key == 'rangesoh') {
            // dd($request->all());
            $tanggal_awal_soh       =   $request->tanggal_awal_soh ?? date("Y-m-d") ;
            $tanggal_akhir_soh      =   $request->tanggal_akhir_soh ?? date("Y-m-d") ;
            $item_rangesoh          =   $request->item_rangesoh ?? "";
            $category_rangesoh      =   $request->category_rangesoh ?? "";
            $marinated_rangesoh     =   $request->marinated_rangesoh ?? "";
            $itemname_rangesoh      =   $request->itemname_rangesoh ?? "";
            $grade_rangesoh         =   $request->grade_rangesoh ?? "";
            $customername_rangesoh  =   $request->customername_rangesoh ?? "";
            $cari_rangesoh          =   $request->cari_rangesoh ?? "" ;
            $ordering_rangesoh      =   $request->ordering_rangesoh ?? 'product_gudang.nama';
            $order_by_rangesoh      =   $request->order_by_rangesoh ?? 'desc';

            $beforeDate             = date( 'Y-m-d', strtotime( $tanggal_awal_soh . ' -1 day' ) );

            if($order_by_rangesoh=="desc"){
                $order_by_rangesoh  = "desc";
            }else{
                $order_by_rangesoh  = "asc";
            }

            if($ordering_rangesoh =="customer"){
                $ordering_rangesoh  = "nama_konsumen";
            }else
            if($ordering_rangesoh =="item"){
                $ordering_rangesoh  = "product_gudang.nama";
            }else
            if($ordering_rangesoh =="qty"){
                $ordering_rangesoh  = "qty_saldo_akhir";
            }else
            if($ordering_rangesoh =="berat"){
                $ordering_rangesoh  = "berat_saldo_akhir";
            }else{
                $ordering_rangesoh  =   'product_gudang.nama';
            }

            if (env('NET_SUBSIDIARY', 'CGL')=='CGL') {
                $data                   = DB::select("CALL getRangeSOHCGL('$tanggal_awal_soh','$beforeDate','$tanggal_akhir_soh','$item_rangesoh','$category_rangesoh','$marinated_rangesoh','$itemname_rangesoh','$grade_rangesoh','$customername_rangesoh','$cari_rangesoh','$ordering_rangesoh','$order_by_rangesoh')");
            }else{
                $data                   = DB::select("CALL getRangeSOHEBA('$tanggal_awal_soh','$beforeDate','$tanggal_akhir_soh','$item_rangesoh','$category_rangesoh','$marinated_rangesoh','$itemname_rangesoh','$grade_rangesoh','$customername_rangesoh','$cari_rangesoh','$ordering_rangesoh','$order_by_rangesoh')");
            }
            return view('admin.pages.warehouse.soh.data-range-soh',compact('data','tanggal_awal_soh','tanggal_akhir_soh','item_rangesoh','category_rangesoh','marinated_rangesoh','itemname_rangesoh','grade_rangesoh','customername_rangesoh','cari_rangesoh','ordering_rangesoh','order_by_rangesoh','beforeDate'));
        }
        else
        if ($request->key == 'unduhsoh') {
            $tanggal    = $request->tanggal;
            $item       = $request->item;
            $cari       = $request->cari;

            $ordering   = $request->ordering;
            $order_by   = $request->order_by;

            $day_before = date( 'Y-m-d', strtotime( $tanggal . ' -1 day' ) );

            if (env('NET_SUBSIDIARY', 'CGL')=='CGL') {
                if ($tanggal == '2023-05-27') {
                    $data       = DB::select("CALL getSOHOpenBalanceCGL('$request->tanggal','$request->item','$request->category','$request->marinated','$request->subitem','$request->grade','$request->customer','$request->cari','$request->ordering','$request->order_by','$request->firstMonth')");

                } else {
                    $data       = DB::select("CALL getSOHCGL('$request->tanggal','$request->item','$request->category','$request->marinated','$request->subitem','$request->grade','$request->customer','$request->cari','$request->ordering','$request->order_by','$request->firstMonth')");

                }
            }else
            if (env('NET_SUBSIDIARY', 'EBA')=='EBA'){
                if ($tanggal == '2023-05-05') {
                    $data       = DB::select("CALL getSOHOpenBalanceEBA('$request->tanggal','$request->item','$request->category','$request->marinated','$request->subitem','$request->grade','$request->customer','$request->cari','$request->ordering','$request->order_by','$request->firstMonth')");

                } else {
                    $data       = DB::select("CALL getSOHEBA('$request->tanggal','$request->item','$request->category','$request->marinated','$request->subitem','$request->grade','$request->customer','$request->cari','$request->ordering','$request->order_by','$request->firstMonth')");

                }
            }
            return view('admin.pages.warehouse.soh.download-soh',compact(['data','tanggal']));
        }else
        if($request->key == 'unduhrangesoh'){
            $tanggal_awal_soh       = $request->tanggal_awal_soh ?? date("Y-m-d") ;
            $tanggal_akhir_soh      = $request->tanggal_akhir_soh ?? date("Y-m-d") ;
            $item_rangesoh          = $request->item_rangesoh ?? "";
            $category_rangesoh      = $request->category_rangesoh ?? "";
            $marinated_rangesoh     = $request->marinated_rangesoh ?? "";
            $itemname_rangesoh      = $request->itemname_rangesoh ?? "";
            $grade_rangesoh         = $request->grade_rangesoh ?? "";
            $customername_rangesoh  = $request->customername_rangesoh ?? "";
            $cari_rangesoh          = $request->cari_rangesoh ?? "" ;
            $ordering_rangesoh      = $request->ordering_rangesoh ?? 'product_gudang.nama';
            $order_by_rangesoh      = $request->order_by_rangesoh ?? 'desc';

            $beforeDate             = date( 'Y-m-d', strtotime( $tanggal_awal_soh . ' -1 day' ) );

            if($order_by_rangesoh =="desc"){
                $order_by_rangesoh  = "desc";
            }else{
                $order_by_rangesoh  = "asc";
            }

            if($ordering_rangesoh =="customer"){
                $ordering_rangesoh  = "nama_konsumen";
            }else
            if($ordering_rangesoh =="item"){
                $ordering_rangesoh  = "product_gudang.nama";
            }else
            if($ordering_rangesoh =="qty"){
                $ordering_rangesoh  = "qty_saldo_akhir";
            }else
            if($ordering_rangesoh =="berat"){
                $ordering_rangesoh  = "berat_saldo_akhir";
            }else{
                $ordering_rangesoh  =   'product_gudang.nama';
            }
            if (env('NET_SUBSIDIARY', 'CGL')=='CGL') {
                $data                   = DB::select("CALL getRangeSOHCGL('$tanggal_awal_soh','$beforeDate','$tanggal_akhir_soh','$item_rangesoh','$category_rangesoh','$marinated_rangesoh','$itemname_rangesoh','$grade_rangesoh','$customername_rangesoh','$cari_rangesoh','$ordering_rangesoh','$order_by_rangesoh')");
            }else{
                $data                   = DB::select("CALL getRangeSOHEBA('$tanggal_awal_soh','$beforeDate','$tanggal_akhir_soh','$item_rangesoh','$category_rangesoh','$marinated_rangesoh','$itemname_rangesoh','$grade_rangesoh','$customername_rangesoh','$cari_rangesoh','$ordering_rangesoh','$order_by_rangesoh')");
            }
            return view('admin.pages.warehouse.soh.download-range-soh',compact(['data','tanggal_awal_soh','tanggal_akhir_soh']));
        }
        else {
            return view('admin.pages.warehouse.dashboard.index', compact('tanggal', 'list_customer', 'customer', 'order_by', 'ordering', 'thawing'));

        }

    }

    public function dashboard(Request $request){
        $tanggal_awal                       =   $request->tanggal_awal ?? date('Y-m-01');
        $tanggal_akhir                      =   $request->tanggal_akhir ?? date("Y-m-d") ;
        $thawing                            =   Thawing::where('status', 1)->where('tanggal_request', date('Y-m-d'))->count() ;

        if ($request->key == 'view') {
            $gudang                         =   Gudang::where('kategori', 'warehouse')
                                                    ->where('subsidiary', env('NET_SUBSIDIARY', 'CGL'))
                                                    ->where('code', 'NOT LIKE', "%abf%")
                                                    ->where('status', 1)
                                                    ->orderBy('code');

            $cloneFilterGudang              = clone $gudang;
            $cloneAll                       = clone $gudang;

            $groupByItem                    = $cloneAll->get();

            $defaultgudang                  = '';
            $filter_gudang                  = self::filter_all_gudang($groupByItem, $defaultgudang);
            
            if($request->loadGrafik == 'YES'){
                $gudangid                   = $request->gudangid;
                $filtergudangid             = $gudangid == 'all' ? $cloneFilterGudang->pluck('id') : array($gudangid);

                $start                      = new DateTime($tanggal_awal);
                $last                       = new DateTime($tanggal_akhir);
                $last->modify('+1 day');
                $interval                   = DateInterval::createFromDateString ('+1 day') ;
                $periods                    = new DatePeriod($start, $interval, $last) ;

                $dailyTransaction           = Product_gudang::dailyWarehouseTransaction($tanggal_awal,$tanggal_akhir,$filtergudangid);
                foreach($periods as $dt){
                    $qty_inbound            = 0;
                    $berat_inbound          = 0;
                    $qty_outbound           = 0;
                    $berat_outbound         = 0;
                    
                    foreach($dailyTransaction as $detail){
                        if($dt->format('Y-m-d') == $detail->production_date){
                            $qty_inbound    = $detail->qty_inbound;
                            $berat_inbound  = $detail->berat_inbound;
                            $qty_outbound   = $detail->qty_outbound;
                            $berat_outbound = $detail->berat_outbound;
                        }
                    }
                    $dataArray[] = array(
                        'tanggal'           => $dt->format('Y-m-d'),
                        'qty_inbound'       => $qty_inbound,
                        'berat_inbound'     => $berat_inbound,
                        'qty_outbound'      => $qty_outbound,
                        'berat_outbound'    => $berat_outbound
                    );
                }
                $qtymasuk                   = array();
                $beratmasuk                 = array();
                $qtykeluar                  = array();
                $beratkeluar                = array();
                $tanggal                    = array();
                foreach($dataArray as $ad){
                    $tanggal[]              = $ad['tanggal'];
                    $qtymasuk[]             = intval($ad['qty_inbound']);
                    $beratmasuk[]           = floatval($ad['berat_inbound']);
                    $qtykeluar[]            = intval($ad['qty_outbound']);
                    $beratkeluar[]          = floatval($ad['berat_outbound']);
                }
                
                $arrayData = [
                    "tanggal"               => $tanggal, 
                    "qtymasuk"              => $qtymasuk, 
                    "beratmasuk"            => $beratmasuk, 
                    "qtykeluar"             => $qtykeluar, 
                    "beratkeluar"           => $beratkeluar
                ];
                    
                return view('admin.pages.warehouse.gudang.component.warehouse_activity',compact('arrayData'));
            }
            if($request->loadTableActivity == 'YES'){
                $gudangid                   = $request->gudangid;
                $filtergudangid             = $gudangid == 'all' ? $cloneFilterGudang->pluck('id') : array($gudangid);
                $nama_gudang                = $gudangid == 'all' ? "Semua Gudang" : Applib::getName('gudang',$gudangid,'code');

                $start                      = new DateTime($tanggal_awal);
                $last                       = new DateTime($tanggal_akhir);
                $last->modify('+1 day');
                $interval                   = DateInterval::createFromDateString ('+1 day') ;
                $periods                    = new DatePeriod($start, $interval, $last) ;

                $dailyTransaction           = Product_gudang::dailyWarehouseTransaction($tanggal_awal,$tanggal_akhir,$filtergudangid);
                foreach($periods as $dt){
                    $qtysaldoawal           = 0;
                    $beratsaldoawal         = 0;
                    $qty_inbound            = 0;
                    $berat_inbound          = 0;
                    $qty_outbound           = 0;
                    $berat_outbound         = 0;
                    
                    foreach($dailyTransaction as $detail){
                        if($dt->format('Y-m-d') == $detail->production_date){
                            $qty_inbound    = $detail->qty_inbound;
                            $berat_inbound  = $detail->berat_inbound;
                            $qty_outbound   = $detail->qty_outbound;
                            $berat_outbound = $detail->berat_outbound;
                        }
                    }
                    $dataArray[] = array(
                        'tanggal'           => $dt->format('Y-m-d'),
                        'nama_gudang'       => $nama_gudang,
                        'qtysaldoawal'      => Product_gudang::getSaldoAwal($dt->format('Y-m-d'),$filtergudangid,'qty_saldo_akhir'),
                        'beratsaldoawal'    => Product_gudang::getSaldoAwal($dt->format('Y-m-d'),$filtergudangid,'berat_saldo_akhir'),
                        'qty_inbound'       => $qty_inbound,
                        'berat_inbound'     => $berat_inbound,
                        'qty_outbound'      => $qty_outbound,
                        'berat_outbound'    => $berat_outbound,
                        'sisaqtyinbound'    => Product_gudang::getSaldoAwal($dt->format('Y-m-d'),$filtergudangid,'sisa_qty_inbound'),
                        'sisaberatinbound'  => Product_gudang::getSaldoAwal($dt->format('Y-m-d'),$filtergudangid,'sisa_berat_inbound')
                    );
                }
                return view('admin.pages.warehouse.gudang.component.detail_activity',compact('dataArray'));

            }
            if($request->loadStockGudang == 'YES'){
                $gudangid                   = $request->gudangid;
                $filtergudangid             = $gudangid == 'all' ? $cloneFilterGudang->pluck('id') : array($gudangid);
                $stock_booking              = Product_gudang::getItemByStockType($tanggal_akhir,'booking',$filtergudangid);
                $stock_free                 = Product_gudang::getItemByStockType($tanggal_akhir,'free',$filtergudangid);

                
                $clonedata                  = clone $gudang;
                $stockgudang                = $clonedata->whereIn('id',$filtergudangid)->get();
                
                $konsumen                   = [
                    "stock_book"            =>  $stock_booking ,
                    "stock_free"            =>  $stock_free ,
                ];
                return view('admin.pages.warehouse.gudang.component.stock_gudang',compact('stockgudang','konsumen','tanggal_awal','tanggal_akhir'));

            }
            return view('admin.pages.warehouse.gudang.view_filter_gudang', compact('filter_gudang','tanggal_awal','tanggal_akhir'));

        }

        return view('admin.pages.warehouse.dashboard.index', compact('tanggal_awal','tanggal_akhir','thawing'));

    }

    public function filter_lb(Request $request){
        $tanggal_awal           =   $request->tanggal_awal ?? date('Y-m-d');
        $tanggal_akhir          =   $request->tanggal_akhir ?? date('Y-m-d');

        if ($request->key == 'view_page') {
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
                return view('admin.pages.warehouse.dashboard.component.filter_all_supplier_lb', compact('supplier', 'dataLBSupplier','filter_supplier_lb'));
            }

            if($request->loadDetailSupplier == 'YES'){
                // dd($request->all());
                $itemid                 = $request->itemid;
                $supplier               = $request->supplier;
                $tanggal_awal           = $request->tanggal_awal;
                $tanggal_akhir          = $request->tanggal_akhir;
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
                                                    // ->groupBy('supplier.id','productions.sc_nama_kandang','productions.lpah_tanggal_potong','purchasing.ukuran_ayam')
                                                    ->orderBy('lpah_tanggal_potong')
                                                    ->get();
                $sku                    = '12111';
                $DataItem               = Item::where('sku','LIKE',$sku.'%')->where('sku','NOT LIKE','%00000')->take(23)->get();
                $arrayItem              = array();
                foreach($getData as $item){
                    $dataGrading            = Grading::join('items','grading.item_id','items.id')->where('trans_id',$item->id)->select('trans_id','total_item','berat_item','nama')->get();
                    $dataArray          = array();

                    foreach($dataGrading as $databaru){
                        $dataArray[]    = array(
                            'namabaru'  => substr($databaru->nama,-5),
                            'itembaru'  => $databaru->total_item,
                            'beratbaru' => $databaru->berat_item
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

                    $uk03_04_ekor   = 0;
                    $uk04_05_ekor   = 0;
                    $uk05_06_ekor   = 0;
                    $uk06_07_ekor   = 0;
                    $uk07_08_ekor   = 0;
                    $uk08_09_ekor   = 0;
                    $uk09_10_ekor   = 0;
                    $uk10_11_ekor   = 0;
                    $uk11_12_ekor   = 0;
                    $uk12_13_ekor   = 0;
                    $uk13_14_ekor   = 0;
                    $uk14_15_ekor   = 0;
                    $uk15_16_ekor   = 0;
                    $uk16_17_ekor   = 0;
                    $uk17_18_ekor   = 0;
                    $uk18_19_ekor   = 0;
                    $uk19_20_ekor   = 0;
                    $uk20_21_ekor   = 0;
                    $uk21_22_ekor   = 0;
                    $uk22_23_ekor   = 0;
                    $uk23_24_ekor   = 0;
                    $uk24_25_ekor   = 0;
                    $uk25_UP_ekor   = 0;

                    foreach($dataArray as $rawitem){
                        if($rawitem['namabaru'] == '03-04'){
                            $uk03_04_ekor   += $rawitem['itembaru'];
                            $uk03_04        += $rawitem['beratbaru'];
                        }
                    
                        if($rawitem['namabaru'] == '04-05'){
                            $uk04_05_ekor   += $rawitem['itembaru'];
                            $uk04_05        += $rawitem['beratbaru'];
                        }
                    
                        if($rawitem['namabaru'] == '05-06'){
                            $uk05_06_ekor   += $rawitem['itembaru'];
                            $uk05_06        += $rawitem['beratbaru'];
                        }
                    
                        if($rawitem['namabaru'] == '06-07'){
                            $uk06_07_ekor   += $rawitem['itembaru'];
                            $uk06_07        += $rawitem['beratbaru'];
                        }
                    
                        if($rawitem['namabaru'] == '07-08'){
                            $uk07_08_ekor   += $rawitem['itembaru'];
                            $uk07_08        += $rawitem['beratbaru'];
                        }
                    
                        if($rawitem['namabaru'] == '08-09'){
                            $uk08_09_ekor   += $rawitem['itembaru'];
                            $uk08_09        += $rawitem['beratbaru'];
                        }
                    
                        if($rawitem['namabaru'] == '09-10'){
                            $uk09_10_ekor   += $rawitem['itembaru'];
                            $uk09_10        += $rawitem['beratbaru'];
                        }
                    
                        if($rawitem['namabaru'] == '10-11'){
                            $uk10_11_ekor   += $rawitem['itembaru'];
                            $uk10_11        += $rawitem['beratbaru'];
                        }
                    
                        if($rawitem['namabaru'] == '11-12'){
                            $uk11_12_ekor   += $rawitem['itembaru'];
                            $uk11_12        += $rawitem['beratbaru'];
                        }
                    
                        if($rawitem['namabaru'] == '12-13'){
                            $uk12_13_ekor   += $rawitem['itembaru'];
                            $uk12_13        += $rawitem['beratbaru'];
                        }
                    
                        if($rawitem['namabaru'] == '13-14'){
                            $uk13_14_ekor   += $rawitem['itembaru'];
                            $uk13_14        += $rawitem['beratbaru'];
                        }
                    
                        if($rawitem['namabaru'] == '14-15'){
                            $uk14_15_ekor   += $rawitem['itembaru'];
                            $uk14_15        += $rawitem['beratbaru'];
                        }
                    
                        if($rawitem['namabaru'] == '15-16'){
                            $uk15_16_ekor  += $rawitem['itembaru'];
                            $uk15_16        += $rawitem['beratbaru'];
                        }
                    
                        if($rawitem['namabaru'] == '16-17'){
                            $uk16_17_ekor   += $rawitem['itembaru'];
                            $uk16_17        += $rawitem['beratbaru'];
                        }
                    
                        if($rawitem['namabaru'] == '17-18'){
                            $uk17_18_ekor   += $rawitem['itembaru'];
                            $uk17_18        += $rawitem['beratbaru'];
                        }
                    
                        if($rawitem['namabaru'] == '18-19'){
                            $uk18_19_ekor   += $rawitem['itembaru'];
                            $uk18_19        += $rawitem['beratbaru'];
                        }
                    
                        if($rawitem['namabaru'] == '19-20'){
                            $uk19_20_ekor   += $rawitem['itembaru'];
                            $uk19_20        += $rawitem['beratbaru'];
                        }
                    
                        if($rawitem['namabaru'] == '20-21'){
                            $uk20_21_ekor   += $rawitem['itembaru'];
                            $uk20_21        += $rawitem['beratbaru'];
                        }
                    
                        if($rawitem['namabaru'] == '21-22'){
                            $uk21_22_ekor   += $rawitem['itembaru'];
                            $uk21_22        += $rawitem['beratbaru'];
                        }
                    
                        if($rawitem['namabaru'] == '22-23'){
                            $uk22_23_ekor   += $rawitem['itembaru'];
                            $uk22_23        += $rawitem['beratbaru'];
                        }
                    
                        if($rawitem['namabaru'] == '23-24'){
                            $uk23_24_ekor   += $rawitem['itembaru'];
                            $uk23_24        += $rawitem['beratbaru'];
                        }
                    
                        if($rawitem['namabaru'] == '24-25'){
                            $uk24_25_ekor   += $rawitem['itembaru'];
                            $uk24_25        += $rawitem['beratbaru'];
                        }
                    
                        if($rawitem['namabaru'] == '25 UP'){
                            $uk25_UP_ekor   += $rawitem['itembaru'];
                            $uk25_UP        += $rawitem['beratbaru'];
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
                        'grading_selesai'   => $item->grading_selesai,
                        'uk03_04_ekor'      => $uk03_04_ekor,
                        'uk03_04'           => $uk03_04,
                        'uk04_05_ekor'      => $uk04_05_ekor,
                        'uk04_05'           => $uk04_05,
                        'uk05_06_ekor'      => $uk05_06_ekor,
                        'uk05_06'           => $uk05_06,
                        'uk06_07_ekor'      => $uk06_07_ekor,
                        'uk06_07'           => $uk06_07,
                        'uk07_08_ekor'      => $uk07_08_ekor,
                        'uk07_08'           => $uk07_08,
                        'uk08_09_ekor'      => $uk08_09_ekor,
                        'uk08_09'           => $uk08_09,
                        'uk09_10_ekor'      => $uk09_10_ekor,
                        'uk09_10'           => $uk09_10,
                        'uk10_11_ekor'      => $uk10_11_ekor,
                        'uk10_11'           => $uk10_11,
                        'uk11_12_ekor'      => $uk11_12_ekor,
                        'uk11_12'           => $uk11_12,
                        'uk12_13_ekor'      => $uk12_13_ekor,
                        'uk12_13'           => $uk12_13,
                        'uk13_14_ekor'      => $uk13_14_ekor,
                        'uk13_14'           => $uk13_14,
                        'uk14_15_ekor'      => $uk14_15_ekor,
                        'uk14_15'           => $uk14_15,
                        'uk15_16_ekor'      => $uk15_16_ekor,
                        'uk15_16'           => $uk15_16,
                        'uk16_17_ekor'      => $uk16_17_ekor,
                        'uk16_17'           => $uk16_17,
                        'uk17_18_ekor'      => $uk17_18_ekor,
                        'uk17_18'           => $uk17_18,
                        'uk18_19_ekor'      => $uk18_19_ekor,
                        'uk18_19'           => $uk18_19,
                        'uk19_20_ekor'      => $uk19_20_ekor,
                        'uk19_20'           => $uk19_20,
                        'uk20_21_ekor'      => $uk20_21_ekor,
                        'uk20_21'           => $uk20_21,
                        'uk21_22_ekor'      => $uk21_22_ekor,
                        'uk21_22'           => $uk21_22,
                        'uk22_23_ekor'      => $uk22_23_ekor,
                        'uk22_23'           => $uk22_23,
                        'uk23_24_ekor'      => $uk23_24_ekor,
                        'uk23_24'           => $uk23_24,
                        'uk24_25_ekor'      => $uk24_25_ekor,
                        'uk24_25'           => $uk24_25,
                        'uk25_UP_ekor'      => $uk25_UP_ekor,
                        'uk25_UP'           => $uk25_UP
                    );
                }
                $GradingSelesaiNotNull        = array();
                if(count($arrayItem) > 0){
                    foreach($arrayItem as $newRoom){
                        if($newRoom['grading_selesai'] != null){
                            $GradingSelesaiNotNull[] = $newRoom;
                        }
                    }
                }
                $arrayBroiler            = [];
                foreach($DataItem as $new){
                    $arrayBroiler[] = "Broiler ".substr($new->nama,-5);
                }

                $arrayBroiler           = json_encode($arrayBroiler);
                return view('admin.pages.warehouse.dashboard.component.detail_filter_lb',compact('getData','DataItem','arrayItem','arrayBroiler','GradingSelesaiNotNull','itemid','supplier','tanggal_awal','tanggal_akhir'));
            }
            return view('admin.pages.warehouse.dashboard.view_all_supplier_lb', compact('filter_ukuran_lb','tanggal_awal','tanggal_akhir'));
        }
        return view('admin.pages.warehouse.dashboard.index_filter_lb', compact('tanggal_awal', 'tanggal_akhir'));
    }

    public function export_supplier_lb(Request $request){
        $tanggal_awal           =   $request->tanggal_awal ?? date('Y-m-d');
        $tanggal_akhir          =   $request->tanggal_akhir ?? date('Y-m-d');
        $itemid                 =   $request->itemid;
        $supplier               =   $request->supplier;

        $filterLB               =   Production::Leftjoin('purchasing', 'productions.purchasing_id', '=', 'purchasing.id')
                                            ->Leftjoin('supplier', 'supplier.id', '=', 'purchasing.supplier_id')
                                            ->where('purchasing.jenis_po', 'PO LB')
                                            ->select('productions.*', 'supplier.nama', 'purchasing.jenis_po','purchasing.ukuran_ayam')
                                            ->whereBetween('productions.lpah_tanggal_potong', [$tanggal_awal, $tanggal_akhir]);

            
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
                                            // ->groupBy('supplier.id','productions.sc_nama_kandang','productions.lpah_tanggal_potong','purchasing.ukuran_ayam')
                                            ->orderBy('lpah_tanggal_potong')
                                            ->get();
        $sku                    = '12111';
        $DataItem               = Item::where('sku','LIKE',$sku.'%')->where('sku','NOT LIKE','%00000')->take(23)->get();
        $arrayItem              = array();
        foreach($getData as $item){
            $dataGrading            = Grading::join('items','grading.item_id','items.id')->where('trans_id',$item->id)->select('trans_id','total_item','berat_item','nama')->get();
            $dataArray          = array();

            foreach($dataGrading as $databaru){
                $dataArray[]    = array(
                    'namabaru'  => substr($databaru->nama,-5),
                    'itembaru'  => $databaru->total_item,
                    'beratbaru' => $databaru->berat_item
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

            $uk03_04_ekor   = 0;
            $uk04_05_ekor   = 0;
            $uk05_06_ekor   = 0;
            $uk06_07_ekor   = 0;
            $uk07_08_ekor   = 0;
            $uk08_09_ekor   = 0;
            $uk09_10_ekor   = 0;
            $uk10_11_ekor   = 0;
            $uk11_12_ekor   = 0;
            $uk12_13_ekor   = 0;
            $uk13_14_ekor   = 0;
            $uk14_15_ekor   = 0;
            $uk15_16_ekor   = 0;
            $uk16_17_ekor   = 0;
            $uk17_18_ekor   = 0;
            $uk18_19_ekor   = 0;
            $uk19_20_ekor   = 0;
            $uk20_21_ekor   = 0;
            $uk21_22_ekor   = 0;
            $uk22_23_ekor   = 0;
            $uk23_24_ekor   = 0;
            $uk24_25_ekor   = 0;
            $uk25_UP_ekor   = 0;

            foreach($dataArray as $rawitem){
                if($rawitem['namabaru'] == '03-04'){
                    $uk03_04_ekor   += $rawitem['itembaru'];
                    $uk03_04        += $rawitem['beratbaru'];
                }
            
                if($rawitem['namabaru'] == '04-05'){
                    $uk04_05_ekor   += $rawitem['itembaru'];
                    $uk04_05        += $rawitem['beratbaru'];
                }
            
                if($rawitem['namabaru'] == '05-06'){
                    $uk05_06_ekor   += $rawitem['itembaru'];
                    $uk05_06        += $rawitem['beratbaru'];
                }
            
                if($rawitem['namabaru'] == '06-07'){
                    $uk06_07_ekor   += $rawitem['itembaru'];
                    $uk06_07        += $rawitem['beratbaru'];
                }
            
                if($rawitem['namabaru'] == '07-08'){
                    $uk07_08_ekor   += $rawitem['itembaru'];
                    $uk07_08        += $rawitem['beratbaru'];
                }
            
                if($rawitem['namabaru'] == '08-09'){
                    $uk08_09_ekor   += $rawitem['itembaru'];
                    $uk08_09        += $rawitem['beratbaru'];
                }
            
                if($rawitem['namabaru'] == '09-10'){
                    $uk09_10_ekor   += $rawitem['itembaru'];
                    $uk09_10        += $rawitem['beratbaru'];
                }
            
                if($rawitem['namabaru'] == '10-11'){
                    $uk10_11_ekor   += $rawitem['itembaru'];
                    $uk10_11        += $rawitem['beratbaru'];
                }
            
                if($rawitem['namabaru'] == '11-12'){
                    $uk11_12_ekor   += $rawitem['itembaru'];
                    $uk11_12        += $rawitem['beratbaru'];
                }
            
                if($rawitem['namabaru'] == '12-13'){
                    $uk12_13_ekor   += $rawitem['itembaru'];
                    $uk12_13        += $rawitem['beratbaru'];
                }
            
                if($rawitem['namabaru'] == '13-14'){
                    $uk13_14_ekor   += $rawitem['itembaru'];
                    $uk13_14        += $rawitem['beratbaru'];
                }
            
                if($rawitem['namabaru'] == '14-15'){
                    $uk14_15_ekor   += $rawitem['itembaru'];
                    $uk14_15        += $rawitem['beratbaru'];
                }
            
                if($rawitem['namabaru'] == '15-16'){
                    $uk15_16_ekor  += $rawitem['itembaru'];
                    $uk15_16        += $rawitem['beratbaru'];
                }
            
                if($rawitem['namabaru'] == '16-17'){
                    $uk16_17_ekor   += $rawitem['itembaru'];
                    $uk16_17        += $rawitem['beratbaru'];
                }
            
                if($rawitem['namabaru'] == '17-18'){
                    $uk17_18_ekor   += $rawitem['itembaru'];
                    $uk17_18        += $rawitem['beratbaru'];
                }
            
                if($rawitem['namabaru'] == '18-19'){
                    $uk18_19_ekor   += $rawitem['itembaru'];
                    $uk18_19        += $rawitem['beratbaru'];
                }
            
                if($rawitem['namabaru'] == '19-20'){
                    $uk19_20_ekor   += $rawitem['itembaru'];
                    $uk19_20        += $rawitem['beratbaru'];
                }
            
                if($rawitem['namabaru'] == '20-21'){
                    $uk20_21_ekor   += $rawitem['itembaru'];
                    $uk20_21        += $rawitem['beratbaru'];
                }
            
                if($rawitem['namabaru'] == '21-22'){
                    $uk21_22_ekor   += $rawitem['itembaru'];
                    $uk21_22        += $rawitem['beratbaru'];
                }
            
                if($rawitem['namabaru'] == '22-23'){
                    $uk22_23_ekor   += $rawitem['itembaru'];
                    $uk22_23        += $rawitem['beratbaru'];
                }
            
                if($rawitem['namabaru'] == '23-24'){
                    $uk23_24_ekor   += $rawitem['itembaru'];
                    $uk23_24        += $rawitem['beratbaru'];
                }
            
                if($rawitem['namabaru'] == '24-25'){
                    $uk24_25_ekor   += $rawitem['itembaru'];
                    $uk24_25        += $rawitem['beratbaru'];
                }
            
                if($rawitem['namabaru'] == '25 UP'){
                    $uk25_UP_ekor   += $rawitem['itembaru'];
                    $uk25_UP        += $rawitem['beratbaru'];
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
                'grading_selesai'   => $item->grading_selesai,
                'uk03_04_ekor'      => $uk03_04_ekor,
                'uk03_04'           => $uk03_04,
                'uk04_05_ekor'      => $uk04_05_ekor,
                'uk04_05'           => $uk04_05,
                'uk05_06_ekor'      => $uk05_06_ekor,
                'uk05_06'           => $uk05_06,
                'uk06_07_ekor'      => $uk06_07_ekor,
                'uk06_07'           => $uk06_07,
                'uk07_08_ekor'      => $uk07_08_ekor,
                'uk07_08'           => $uk07_08,
                'uk08_09_ekor'      => $uk08_09_ekor,
                'uk08_09'           => $uk08_09,
                'uk09_10_ekor'      => $uk09_10_ekor,
                'uk09_10'           => $uk09_10,
                'uk10_11_ekor'      => $uk10_11_ekor,
                'uk10_11'           => $uk10_11,
                'uk11_12_ekor'      => $uk11_12_ekor,
                'uk11_12'           => $uk11_12,
                'uk12_13_ekor'      => $uk12_13_ekor,
                'uk12_13'           => $uk12_13,
                'uk13_14_ekor'      => $uk13_14_ekor,
                'uk13_14'           => $uk13_14,
                'uk14_15_ekor'      => $uk14_15_ekor,
                'uk14_15'           => $uk14_15,
                'uk15_16_ekor'      => $uk15_16_ekor,
                'uk15_16'           => $uk15_16,
                'uk16_17_ekor'      => $uk16_17_ekor,
                'uk16_17'           => $uk16_17,
                'uk17_18_ekor'      => $uk17_18_ekor,
                'uk17_18'           => $uk17_18,
                'uk18_19_ekor'      => $uk18_19_ekor,
                'uk18_19'           => $uk18_19,
                'uk19_20_ekor'      => $uk19_20_ekor,
                'uk19_20'           => $uk19_20,
                'uk20_21_ekor'      => $uk20_21_ekor,
                'uk20_21'           => $uk20_21,
                'uk21_22_ekor'      => $uk21_22_ekor,
                'uk21_22'           => $uk21_22,
                'uk22_23_ekor'      => $uk22_23_ekor,
                'uk22_23'           => $uk22_23,
                'uk23_24_ekor'      => $uk23_24_ekor,
                'uk23_24'           => $uk23_24,
                'uk24_25_ekor'      => $uk24_25_ekor,
                'uk24_25'           => $uk24_25,
                'uk25_UP_ekor'      => $uk25_UP_ekor,
                'uk25_UP'           => $uk25_UP
            );
        }
        $GradingSelesaiNotNull        = array();
        if(count($arrayItem) > 0){
            foreach($arrayItem as $newRoom){
                if($newRoom['grading_selesai'] != null){
                    $GradingSelesaiNotNull[] = $newRoom;
                }
            }
        }
        
        return view('admin.pages.warehouse.dashboard.component.download', compact('DataItem','GradingSelesaiNotNull'));
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

    public static  function filter_all_gudang($item,$default)
    {
        // dd($item, $id);
        if ($default === '' || $default=== 'all') {
            $html           = '<select class="form-control select2 d-inline" id="all_gudang" name="all_gudang" style="width:200px" >"n"';
            $html           .= '<option value="all"> Pilih Gudang </option>"n"';
            $items          = $item;
            foreach ($items as $value) {
                $html .= '<option value="' . $value->id . '">' . $value->code . '</option>"n"';
            }
            $html           .= "</select>";
        }
        else{

            $html           = '<select class="form-control select2 d-inline" id="all_gudang" name="all_gudang" style="width:200px" >"n"';
            $html           .= '<option value="all"> Pilih Gudang </option>"n"';
            $items          = $item;
            foreach ($items as $value) {
                $html .= '<option value="' . $value->id . '">' . $value->code . '</option>"n"';
            }
            $html           .= "</select>";

        }
        return $html;
    }
}
