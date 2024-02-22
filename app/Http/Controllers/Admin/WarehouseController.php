<?php

namespace App\Http\Controllers\Admin;

use App\Classes\Applib;
use App\Http\Controllers\Controller;
use App\Models\Abf;
use App\Models\Adminedit;
use App\Models\Bahanbaku;
use App\Models\Bom;
use App\Models\BomItem;
use App\Models\Category;
use App\Models\Chiller;
use App\Models\Customer;
use App\Models\DataOption;
use App\Models\Freestock;
use App\Models\FreestockTemp;
use App\Models\Gudang;
use App\Models\Item;
use App\Models\Netsuite;
use App\Models\Openbalance;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product_gudang;
use App\Models\Production;
use App\Models\PurchaseItem;
use App\Models\Purchasing;
use App\Models\Thawing;
use App\Models\Thawinglist;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;

class WarehouseController extends Controller
{

    public $nama_gudang_expedisi;
    public $nama_gudang_bb;
    public $nama_gudang_fg;
    public $nama_gudang_abf;

    public function __construct(Request $request)
    {
        $this->nama_gudang_expedisi     = env('NET_SUBSIDIARY', 'CGL')." - Storage Expedisi";
        $this->nama_gudang_bb           = env('NET_SUBSIDIARY', 'CGL')." - Chiller Bahan Baku";
        $this->nama_gudang_fg           = env('NET_SUBSIDIARY', 'CGL')." - Chiller Finished Good";
        $this->nama_gudang_abf          = env('NET_SUBSIDIARY', 'CGL')." - Storage ABF";
    }


    public function index(Request $request)
    {
        if (User::setIjin(15)) {
            if ($request->key == 'inject_kode') {
                $total  =   0 ;
                foreach (Product_gudang::whereDate('production_date', $request->tanggal)->get() as $row) {
                    if ($row->production_code == NULL) {
                        $total  +=  1 ;
                        $row->production_code   =   Gudang::kode_produksi($row->id) ;
                        $row->save() ;
                    }
                }

                return 'Ada ' . $total . ' data yang diperbaharui' ;
            } else

            if (($request->key == 'approve_abf') || ($request->key == 'approve_chiller')) {
                $data   =   FreestockTemp::whereIn('freestock_id', Freestock::select('id')->whereBetween('tanggal', [($request->tanggal_mulai ?? date("Y-m-d")), ($request->tanggal_akhir ?? date("Y-m-d"))]))
                            ->where(function($query) use ($request) {
                                if ($request->key == 'approve_abf') {
                                    $query->where('kategori', 1) ;
                                } else {
                                    $query->where('kategori', '!=', 1) ;
                                }

                            })
                            ->orderByDesc('id')
                            ->get() ;

                return view('admin.pages.warehouse.approve.index', compact('data', 'request')) ;
            } else if($request->key == 'riwayat_approve'){
                $data = Adminedit::where('table_id', $request->id)->first();
                if($data){
                    return response()->json([
                        'data' => $data,
                        'hasil' => '1'
                    ]);
                } else {
                    return response()->json([
                        'hasil' => '0'
                    ]);
                }
            // } else if ($request->key == 'searchDetailSOH'){

            }

            else {
                // dd($request->all());
                $tanggal        =   $request->tanggal ?? date('Y-m-d') ;
                $tanggal_mulai  =   $request->tanggal_mulai ?? date('Y-m-d') ;
                $tanggal_akhir  =   $request->tanggal_akhir ?? date('Y-m-d') ;
                $thawing        =   Thawing::where('status', 1)->where('tanggal_request', $tanggal)->count() ;
                $gudang         =   Gudang::where('subsidiary', env('NET_SUBSIDIARY', 'CGL'))
                                    ->where('kategori', 'warehouse')
                                    ->where('status', '1')
                                    ->get();
                $bulan      =   $request->bulan ?? date('m');

                $customer       =   $request->customer ?? NULL;
                $item           =   $request->item ?? NULL;
                $ordering       =   $request->ordering ?? 'ASC';
                $order_by       =   $request->order_by ?? 'id';
                $cari           =   $request->cari ?? "" ;

                $id             =   $request->id ?? '';
                $search         =   $request->search ?? '';

                $list_customer  =   Product_gudang::select('customers.id', 'customers.nama')
                                    ->join('customers', 'customers.id', '=', 'product_gudang.customer_id')
                                    ->groupBy('customer_id')
                                    ->get();

                $list_item      =   Item::select('items.id', 'items.nama')
                                    ->where('items.nama', 'like', '%FROZEN%')
                                    // ->join('items', 'items.id', '=', 'product_gudang.product_id')
                                    // ->groupBy('product_id')
                                    ->get();

                $data_productid =  Product_gudang::select('product_id')->groupBy('product_id')->get();
                $collect            = array();

                if($data_productid->count() > 0 ){
                    foreach($data_productid as $ls){
                        $collect[] = $ls->productitems->category_id ?? NULL;
                    }
                }

                $array              = collect($collect)->unique();
                $filtered           = $array->filter(function ($value) {
                    return $value != NULL;
                });
                $list_category      = Category::select('id','nama')->whereIN('id',$filtered)->get();


                $data_itemname      =  Product_gudang::select('sub_item')->groupBy('sub_item')->get();
                $collect_itemname   = array();

                if($data_itemname->count() > 0 ){
                    foreach($data_itemname as $lin){
                        $collect_itemname[] = $lin->sub_item;
                    }
                }

                $list_itemname     = collect($collect_itemname)->unique();

                $data_customer     =  Product_gudang::select('customer_id')->groupBy('customer_id')->get();
                $collect_customer  = array();

                if($data_customer->count() > 0 ){
                    foreach($data_customer as $cust){
                        $collect_customer[] = $cust->customer_id;
                    }
                }

                $list_customername = collect($collect_customer)->unique();

                $jenis      =   $request->jenis ?? "";

                // return view('admin.pages.warehouse.index', compact('tanggal', 'thawing', 'gudang','tanggal_mulai','tanggal_akhir','customer', 'list_customer', 'ordering', 'order_by', 'list_item', 'item', 'id'));
                return view('admin.pages.warehouse.index', compact('tanggal', 'thawing', 'gudang','tanggal_mulai','tanggal_akhir','customer', 'list_customer', 'ordering', 'order_by', 'list_item', 'item', 'id','list_category','list_itemname','list_customername','bulan','jenis', 'search'));
            }

        }
        return redirect()->route("index");
    }

    public function store(Request $request)
    {
        if (User::setIjin(15)) {
            if ($request->key == 'approve') {

                if ($request->qty == "") {
                    $result['status']   =   400;
                    $result['msg']      =   "Qty belum diisikan";
                    return $result;
                }

                if (!$request->berat) {
                    $result['status']   =   400;
                    $result['msg']      =   "Berat belum diisikan";
                    return $result;
                }

                $data   =   FreestockTemp::find($request->id) ;


                if ($data) {
                    DB::beginTransaction() ;
                    $log                       =   new Adminedit;
                    $log->user_id              =   Auth::user()->id;
                    $log->type                 =  'input';
                    $log->activity             =  'confirm';
                    $log->content              =  'Konfirmasi Timbang';
                    $log->data                 =   json_encode([
                                                        'data_produksi'       =>  [
                                                            'berat' => $data->berat,
                                                            'qty' =>  $data->qty,
                                                        ],
                                                        'data_konfirmasi'     =>  [
                                                            'berat' => $request->berat,
                                                            'qty' => $request->qty,
                                                        ],
                                                    ]);
                    $log->table_name           =   'free_stocktemp';
                    $log->table_id             =   $data->id;
                    if (!$log->save()) {
                        DB::rollBack() ;
                        $result['status']   =   400 ;
                        $result['msg']      =   "Proses gagal" ;
                        return $result ;
                    }
                    $data->qty      =   $request->qty ;
                    $data->berat    =   $request->berat ;


                    if (!$data->save()) {
                        DB::rollBack() ;
                        $result['status']   =   400 ;
                        $result['msg']      =   "Proses gagal" ;
                        return $result ;
                    }

                    $chiller                    =   new Chiller;
                    $chiller->table_name        =   'free_stocktemp';
                    $chiller->table_id          =   $data->id;
                    $chiller->asal_tujuan       =   'free_stock';
                    $chiller->item_id           =   $data->item_id;
                    $chiller->item_name         =   $data->item->nama;
                    $chiller->jenis             =   'masuk';
                    $chiller->type              =   'hasil-produksi';
                    $chiller->regu              =   $data->free_stock->regu;
                    $chiller->label             =   $data->label;
                    $chiller->customer_id       =   $data->customer_id;
                    $chiller->selonjor          =   $data->selonjor;
                    $chiller->berat_item        =   $data->berat;
                    $chiller->qty_item          =   $data->qty;
                    $chiller->stock_berat       =   $chiller->berat_item;
                    $chiller->stock_item        =   $chiller->qty_item;
                    $chiller->tanggal_produksi  =   $data->tanggal_produksi;
                    $chiller->kategori          =   $data->kategori;
                    $chiller->keranjang         =   $data->jumlah_keranjang;
                    $chiller->kode_produksi     =   $data->kode_produksi;
                    $chiller->unit              =   $data->unit;
                    $chiller->status            =   2;
                    $chiller->save() ;

                    if (!$chiller->save()) {
                        DB::rollBack() ;
                        $result['status']   =   400 ;
                        $result['msg']      =   "Proses gagal" ;
                        return $result ;
                    }

                    $freestock  =   Freestock::find($data->freestock_id) ;


                    $count      =   0 ;
                    foreach ($freestock->freetemp as $row) {
                        $count  +=  $row->tempchiller ? 1 : 0 ;
                    }

                    if (COUNT($freestock->freetemp) == $count) {
                        $freestock->status  =   3 ;
                        if (!$freestock->save()) {
                            DB::rollBack();
                            $result['status']   =   400;
                            $result['msg']      =   "Proses gagal";
                            return $result;
                        }
                    }

                    DB::commit();
                    $result['status']   =   200;
                    $result['msg']      =   "Approve berhasil";
                    // Adminedit::orderBy('id', 'desc')->limit(1)->delete();
                    return $result;

                }

                $result['status']   =   400 ;
                $result['msg']      =   "Proses gagal" ;
                return $result ;

            } else {
                $data           =   Product_gudang::find($request->kode);

                $abf            =   Abf::find($data->table_id);

                $nama_tabel     =   "abf";
                $id_tabel       =   $abf->id;
                $location       =   $this->nama_gudang_abf;
                $from           =   Gudang::gudang_netid($location);

                $gdg            =   Gudang::find($request->tujuan);
                $to             =   Gudang::gudang_netid($gdg->code);
                $idgudang       =   Gudang::gudang_id($gdg->code);

                $id_location    =   Gudang::gudang_netid($location);
                $label          =   strtolower("ti_abf_" . str_replace(" ", "", $gdg->code));
                $transfer       =   [
                    [
                        "internal_id_item"  =>  (string)$abf->item->netsuite_internal_id,
                        "item"              =>  (string)$abf->item->sku,
                        "qty_to_transfer"   =>  (string)$data->berat
                    ]
                ];
                $data->gudang_id    =   $idgudang;
                $data->status       =   2;
                $data->save();
                Netsuite::transfer_inventory($nama_tabel, $id_tabel, $label, $id_location, $location, $from, $to, $transfer, null);
            }
        }
        return redirect()->route("index");
    }

    public function show(Request $request)
    {
        // $tanggal = date('Y-m-d');
        $mulai  =   $request->mulai ?? date('Y-m-d');
        $sampai =   $request->sampai ?? date('Y-m-d');

        if (User::setIjin(15)) {
            $abf        = Abf::get();
            $masuk      = Product_gudang::where('jenis_trans', 'masuk')->whereIn('status', [1, 2])->whereBetween('created_at', [$mulai, $sampai])->get();
            $keluar     = Product_gudang::where('jenis_trans', 'masuk')->whereIn('status', [2, 4])->get()->whereBetween('created_at', [$mulai, $sampai]);
            $stock      = Product_gudang::where('jenis_trans', 'masuk')->where('status', 2)->whereBetween('created_at', [$mulai, $sampai])->get();
            $thawing     = Product_gudang::where('jenis_trans', 'keluar')->whereIn('status', [3, 4])->whereBetween('created_at', [$mulai, $sampai])->get();
            return view('admin.pages.warehouse.data_show', compact('abf', 'stock', 'masuk', 'keluar', 'thawing','mulai', 'sampai'));
        }
        return redirect()->route("index");
    }

    public function data_filter(Request $request){
        // dd($request->key);
        $search     =   $request->filter ?? "";
        $tanggal    =   $request->tanggal_akhir ?? date('Y-m-d') ;
        $gudang     =   $request->gudang ?? '';
        if($request->key == 'detail'){
            $nama       = $request->nama ?? null;
            $konsumen   = $request->konsumen ?? null;
            $lokasi     = $request->lokasi ?? null;
            $kemasan    = $request->kemasan ?? null;
            $subitem    = $request->subitem ?? null;
            $customerid = $request->customerid ?? null;
            $tanggal    = $request->tanggal ?? date('Y-m-d');

            $data       = Product_gudang::where('product_id', $nama)
                            ->where('packaging', $kemasan)
                            ->where('sub_item', $subitem)
                            ->where('kategori', $konsumen)
                            ->where('gudang_id', $lokasi)
                            ->where('berat', '>', 0)
                            ->where('customer_id', $customerid)
                            ->where('production_date', '<=', $tanggal)
                            ->get();

            // dd($data);
            return view('admin.pages.warehouse.detail_filter', compact('data','tanggal','nama','konsumen','lokasi','kemasan','subitem','customerid'));
        } else

        {
            $stock      =   Product_gudang::select('id', 'product_id', 'customer_id', 'selonjor', 'sub_item', 'plastik_group', 'parting', 'production_date', 'gudang_id', DB::raw("SUM(qty) AS total"), DB::raw("SUM(berat) AS kg"))
                            ->where('jenis_trans', 'masuk')
                            // ->where('status', 2)
                            ->where('berat', '>', 0)
                            ->where('production_date','<=', $tanggal)
                            ->groupBy('product_id','sub_item', 'plastik_group','gudang_id', 'parting')
                            ->orderBy('nama', 'asc')
                            ->where(function($query) use ($search, $gudang){
                                if ($search) {
                                    $query->orWhere('label', 'like', '%' . $search . '%') ;
                                    $query->orWhere('sub_item', 'like', '%' . $search . '%') ;
                                    $query->orWhere('qty', 'like', '%' . $search . '%') ;
                                    $query->orWhere('berat_timbang', 'like', '%' . $search . '%') ;
                                    $query->orWhere('notes', 'like', '%' . $search . '%') ;
                                    $query->orWhere('packaging', 'like', '%' . $search . '%') ;
                                    $query->orWhere('palete', 'like', '%' . $search . '%') ;
                                    $query->orWhereIn('product_id', Item::select('id')->where('nama', 'like', '%' . $search . '%'));
                                }
                                if ($gudang) {
                                    $query->Where('gudang_id',  $gudang);
                                }
                            })->paginate(30);
            return view('admin.pages.warehouse.data_filter', compact('tanggal', 'gudang', 'stock'));
        }
    }

    public function stock(Request $request){
        if ($request->key == 'allFilter') {
            $search =   $request->filter ?? "";
            $tanggal=   $request->tanggal_akhir ?? date('Y-m-d');
            $gudang =   $request->gudang ?? '';
            $tahun  =   $request->tahun ?? date('Y');
            $bulan  =   $request->bulan ?? date('m');

            $stock  =   Product_gudang::select('id', 'product_id', 'customer_id', 'selonjor', 'sub_item', 'parting', 'plastik_group', 'production_date', 'gudang_id', DB::raw("SUM(qty) AS total"), DB::raw("SUM(berat) AS kg"))
                        ->where('jenis_trans', 'masuk')
                        ->where('berat', '>', 0)
                        ->whereMonth('production_date','<=' , $bulan)
                        ->whereYear('production_date', '<=', $tahun)
                        ->where('production_date', '>=', '2022-02-04')
                        ->where('nama','NOT LIKE', '%AY - S%')
                        ->groupBy('product_id','sub_item', 'plastik_group','gudang_id', 'parting')
                        ->orderBy('nama', 'asc')
                        ->where(function($query) use ($search, $gudang){
                            if ($search) {
                                $query->orWhere('label', 'like', '%' . $search . '%') ;
                                $query->orWhere('sub_item', 'like', '%' . $search . '%') ;
                                $query->orWhere('qty', 'like', '%' . $search . '%') ;
                                $query->orWhere('berat_timbang', 'like', '%' . $search . '%') ;
                                $query->orWhere('notes', 'like', '%' . $search . '%') ;
                                $query->orWhere('plastik_group', 'like', '%' . $search . '%') ;
                                $query->orWhere('palete', 'like', '%' . $search . '%') ;
                                $query->orWhereIn('product_id', Item::select('id')->where('nama', 'like', '%' . $search . '%'));
                            }
                            if ($gudang) {
                                $query->Where('gudang_id',  $gudang);
                            }
                        })
                        ->paginate(15);

            return view('admin.pages.warehouse.all_fiter', compact('tanggal', 'gudang', 'stock','tahun','bulan','search'));

        } else {
            $tahun      =   $request->tahun ?? date('Y');
            $bulan      =   $request->bulan ?? date('m');
            $tanggal    =   $request->tanggal_akhir ?? date('Y-m-d') ;
            $gudang     =   Gudang::where('subsidiary', env('NET_SUBSIDIARY', 'CGL'))
                            ->where('kategori', 'warehouse')
                            ->where('status', '1')
                            ->get();

            return view('admin.pages.warehouse.dashboard_all_filter', compact('tanggal', 'gudang','tahun','bulan'));
        }
    }

    public function soh_ia(Request $request){

        // {"_token":"OiGQzH0450A0sfkub1EjEcQEemFMSosmVERhg7wH","item_name":"AYAM KARKAS BROILER 05-06 FROZEN","packaging":"POLOS","sub_pack":"FREE","customer":null,"qty":"1356","berat":"790.2"}
        $item_name      = $request->namaItem;
        $product_id     = Item::where('nama', $request->namaItem)->first()->id ?? 0;
        $packaging      = $request->packaging;
        $parting        = $request->parting;
        $sub_item       = $request->subItem;
        $sub_pack       = $request->sub_pack;
        $customer       = $request->customer;
        $customer_id    = $request->customerId;
        $qty            = $request->qty;
        $berat          = $request->berat;
        $tanggal        = $request->tanggal;
        $grade          = $request->grade == 'A' ? NULL : 'B';
        $gudang_id      = $request->gudangId;

        $data   =   Product_gudang::select(
                        'product_gudang.product_id', 'product_gudang.nama', 'product_gudang.plastik_group', 'product_gudang.parting', 'product_gudang.subpack', 'product_gudang.sub_item', 'product_gudang.customer_id', 'customers.nama AS nama_konsumen', 'product_gudang.gudang_id',
                        DB::raw("(SUM(IF(product_gudang.production_date!='$tanggal',IF(product_gudang.status=2,product_gudang.berat_awal,0),0)) - SUM(IF(product_gudang.production_date!='$tanggal',IF(product_gudang.status=4,product_gudang.berat,0),0))) AS berat_saldo_awal"),
                        DB::raw("(SUM(IF(product_gudang.production_date!='$tanggal',IF(product_gudang.status=2,product_gudang.qty_awal,0),0)) - SUM(IF(product_gudang.production_date!='$tanggal',IF(product_gudang.status=4,product_gudang.qty,0),0))) AS qty_saldo_awal"),
                        DB::raw("(SUM(IF(product_gudang.production_date!='$tanggal',IF(product_gudang.status=2,product_gudang.karung_qty,0),0)) - SUM(IF(product_gudang.production_date!='$tanggal',IF(product_gudang.status=4,product_gudang.karung_qty,0),0))) AS karung_saldo_awal"),

                        DB::raw("(SUM(IF(product_gudang.production_date<='$tanggal',IF(product_gudang.status=2,product_gudang.berat_awal,0),0)) - SUM(IF(product_gudang.production_date<='$tanggal',IF(product_gudang.status=4,product_gudang.berat,0),0))) AS berat_saldo_akhir"),
                        DB::raw("(SUM(IF(product_gudang.production_date<='$tanggal',IF(product_gudang.status=2,product_gudang.qty_awal,0),0)) - SUM(IF(product_gudang.production_date<='$tanggal',IF(product_gudang.status=4,product_gudang.qty,0),0))) AS qty_saldo_akhir"),
                        DB::raw("(SUM(IF(product_gudang.production_date<='$tanggal',IF(product_gudang.status=2,product_gudang.karung_qty,0),0)) - SUM(IF(product_gudang.production_date<='$tanggal',IF(product_gudang.status=4,product_gudang.karung_qty,0),0))) AS karung_saldo_akhir"),

                        // // Inbound Produksi
                        DB::raw("SUM(IF(product_gudang.production_date='$tanggal',IF(product_gudang.status=2,product_gudang.qty_awal,0),0)) AS inb_prod_qty"),
                        DB::raw("SUM(IF(product_gudang.production_date='$tanggal',IF(product_gudang.status=2,product_gudang.berat_awal,0),0)) AS inb_prod_bb"),
                        DB::raw("SUM(IF(product_gudang.production_date='$tanggal',IF(product_gudang.status=2,product_gudang.karung_qty,0),0)) AS inb_prod_krg"),

                        // // Outbound Reprocess
                        DB::raw("SUM(IF(product_gudang.production_date='$tanggal',IF(product_gudang.status=4,product_gudang.qty_awal,0),0)) AS out_prod_qty"),
                        DB::raw("SUM(IF(product_gudang.production_date='$tanggal',IF(product_gudang.status=4,product_gudang.berat_awal,0),0)) AS out_prod_bb"),
                        DB::raw("SUM(IF(product_gudang.production_date='$tanggal',IF(product_gudang.status=4,product_gudang.karung_qty,0),0)) AS out_prod_krg"),

                    )
                    ->leftJoin('abf', 'abf.id', '=', 'product_gudang.table_id')
                    ->leftJoin('customers', 'customers.id', '=', 'product_gudang.customer_id')
                    ->where('product_gudang.product_id', $product_id)
                    ->where('product_gudang.plastik_group', $packaging)
                    ->where('product_gudang.sub_item', $sub_item)
                    ->where('product_gudang.customer_id', $customer_id)
                    ->where('product_gudang.grade_item', $grade)
                    ->where('product_gudang.gudang_id', $gudang_id)

                    ->groupBy('product_gudang.product_id')
                    ->groupBy('product_gudang.plastik_group')
                    ->groupBy('product_gudang.sub_item')
                    ->groupBy('product_gudang.customer_id')
                    ->groupBy('product_gudang.grade_item')
                    ->groupBy('product_gudang.gudang_id')
                    ->first();
                    // dd($data);
                    DB::beginTransaction();

                    if($data){
                        

                        try {
                            $produk      =   Item::where('nama', $request->namaItem)->first();

                            $tanggal     = date('Y-m-d', strtotime($tanggal));

                            if($produk){
                                $produkexp   =   Item::item_frozen_to_fresh($produk->id, $produk->nama);
                                if($produkexp){
                                    $prodgudang = $data->gudang_id;
                                }
                            }

                            if ($produk && $produkexp ) {

                                $qty_selisih        = $qty - $data->qty_saldo_akhir;
                                $berat_selisih      = $berat - $data->berat_saldo_akhir;


                                $gudang                     =   new Product_gudang();
                                $gudang->table_name         =   'abf';
                                $gudang->nama               =   $produk->nama;
                                $gudang->sub_item           =   $sub_item ?? NULL;
                                $gudang->subpack            =   $sub_pack ?? NULL;
                                $gudang->product_id         =   $produk->id;
                                $gudang->parting            =   $parting ?? 0;
                                $gudang->customer_id            =  $customer_id;
                                $gudang->plastik_group      =   $packaging;
                                $gudang->palete             =   NULL;
                                $gudang->gudang_id          =   $prodgudang;
                                $gudang->expired            =   NULL;
                                $gudang->type               =   "inventory_adjustment";
                                $gudang->production_date    =   $tanggal;


                                if ($qty_selisih < 0 || $berat_selisih < 0) {
                                    if($qty_selisih < 0){
                                        $gudang->qty_awal           =   -1*$qty_selisih;
                                        $gudang->qty                =   -1*$qty_selisih;
                                    }else{
                                        $gudang->qty_awal           =   $qty_selisih;
                                        $gudang->qty                =   $qty_selisih;
                                    }

                                    if($berat_selisih < 0){
                                        $gudang->berat_awal         =   -1*$berat_selisih;
                                        $gudang->berat              =   -1*$berat_selisih;
                                    }else{
                                        $gudang->berat_awal         =   $berat_selisih;
                                        $gudang->berat              =   $berat_selisih;
                                    }

                                    $gudang->jenis_trans            =   "keluar" ;
                                    $gudang->stock_type             =   "negatif" ;
                                    $gudang->status                 =   4 ;
                                } else {
                                    $open                           =   new Openbalance();
                                    $open->user_id                  =   Auth::user()->id;
                                    $open->gudang                   =   'chiller';
                                    $open->item_id                  =   $produk->id;
                                    $open->tipe_item                =   'hasil-produksi';
                                    $open->tanggal                  =   $tanggal;
                                    $open->qty                      =   $qty_selisih;
                                    $open->berat                    =   $berat_selisih;
                                    if (!$open->save()) {
                                        DB::rollBack();
                                        // return back()->with('status', 2)->with('message', 'Proses gagal (PKG-12)');
                                        return response()->json([
                                            'status'        =>   400,
                                            'msg'           =>   'Proses gagal (PKG-12)',
                                        ]);
                                    }

                                    $abf                    =   new Abf;
                                    $abf->table_name        =   'openbalance';
                                    $abf->table_id          =   $open->id;
                                    $abf->asal_tujuan       =   'open_balance';
                                    $abf->tanggal_masuk     =   $tanggal;
                                    $abf->item_id           =   $produk->id;
                                    $abf->item_id_lama      =   $produkexp->id;
                                    $abf->item_name         =   $produk->nama;
                                    $abf->jenis             =   'masuk';
                                    $abf->type              =   $open->tipe_item;
                                    $abf->tujuan            =   $prodgudang;
                                    $abf->qty_awal          =   $qty_selisih;
                                    $abf->berat_awal        =   $berat_selisih;
                                    $abf->qty_item          =   $qty_selisih;
                                    $abf->berat_item        =   $berat_selisih;

                                    $abf->status            =   2;
                                    if (!$abf->save()) {
                                        DB::rollBack();
                                        // return back()->with('status', 2)->with('message', 'Proses gagal (BFF-32)');
                                        return response()->json([
                                            'status'        =>   400,
                                            'msg'           =>   'Proses gagal (BFF-32)',
                                        ]);
                                        
                                    }


                                    $gudang->qty_awal           =   $qty_selisih;
                                    $gudang->berat_awal         =   $berat_selisih;
                                    $gudang->qty                =   $qty_selisih;
                                    $gudang->berat              =   $berat_selisih;
                                    $gudang->status             =   2 ;
                                    $gudang->jenis_trans        =   "masuk" ;
                                    $gudang->stock_type         =   "positif" ;
                                    $gudang->table_id           =   $abf->id;
                                }

                                $packaging  =   Item::where('nama', $packaging)->first() ;

                                if ($packaging) {
                                    $gudang->packaging          = $packaging->nama;
                                }else{
                                    $gudang->packaging          = $packaging;
                                }

                                $gudang->gudang_id          =   $prodgudang;

                                if (!$gudang->save()) {
                                    DB::rollBack();
                                    // return back()->with('status', 2)->with('message', 'Proses gagal (GND-10)');
                                    return response()->json([
                                        'status'        =>   400,
                                        'msg'           =>   'Proses gagal (GND-10)',
                                    ]);
                                }

                            } else {

                            }

                        } catch (\Throwable $th) {
                            DB::rollBack();
                            // return back()->with('status', 2)->with('message', $th->getMessage());
                            return response()->json([
                                'status'        =>   400,
                                'msg'           =>   $th->getMessage(),
                            ]);
                        }


                        DB::commit();
                        return response()->json([
                            'status'        =>   200,
                            'msg'           =>  'Proses Inventory Adjustment Sukses',
                            'data'          =>  $data,
                        ]);

                    } else {
                        DB::rollBack();
                        return response()->json([
                            'status'        =>   400,
                            'msg'           =>  'Data tidak ditemukan',
                        ]);

                    }

        // return back()->with('status', 1)->with('message', 'Proses Inventory Adjustment Sukses');
    }

    public function filter(Request $request){
        $tanggal    =   $request->tanggal ?? date('Y-m-d') ;
        $gudang     =   Gudang::where('subsidiary', env('NET_SUBSIDIARY', 'CGL'))
        ->where('kategori', 'warehouse')
        ->where('status', '1')
        ->get();
        return view('admin.pages.warehouse.filter', compact('tanggal', 'gudang'));
    }

    public function request_thawing(Request $request, $id)
    {
        if (User::setIjin(15)) {
            // if ($request->key == 'data_stock') {
            //     $tanggal    =   $request->tanggal ?? date("Y-m-d") ;
            //     $akhir      =   $request->akhir ?? date("Y-m-d") ;
            //     $stock      =   Product_gudang::where('jenis_trans', 'masuk')
            //                     ->where('status', 2)
            //                     ->whereBetween('production_date', [$tanggal, $akhir])
            //                     ->orderBy('production_date', 'ASC')
            //                     ->whereNotIn('type', ['inventory_adjustment'])
            //                     ->where(function($query) use ($request) {
            //                         if ($request->cari) {
            //                             $query->orWhere('production_date', 'like', '%' . $request->cari . '%') ;
            //                             $query->orWhere('nama', 'like', '%' . $request->cari . '%') ;
            //                             $query->orWhere('sub_item', 'like', '%' . $request->cari . '%') ;
            //                             $query->orWhere('packaging', 'like', '%' . $request->cari . '%') ;
            //                         }
            //                     })
            //                     ->paginate(15);

            //     return view('admin.pages.warehouse.requeststock', compact('stock', 'tanggal', 'akhir', 'id'));
            // } else
            if ($request->key == 'data_stock') {
                $tanggal    =   $request->tanggal ?? date("Y-m-d") ;
                $akhir      =   $request->akhir ?? date("Y-m-d") ;
                $sql        =   Product_gudang::
                                select('gudang.code','product_gudang.id', 'product_gudang.production_date', 'product_gudang.nama', 'product_gudang.sub_item', 'product_gudang.packaging', 'product_gudang.qty_awal', 'product_gudang.berat_awal', 'customers.nama as nama_customer')
                                ->leftjoin('customers','customers.id','product_gudang.customer_id')
                                ->leftjoin('gudang','gudang.id','product_gudang.gudang_id')
                                ->where('jenis_trans', 'masuk')
                                ->where('product_gudang.status', 2)
                                ->whereNotIn('product_gudang.type',['inventory_adjustment'])
                                ->whereBetween('production_date', [$tanggal, $akhir])
                                ->orderBy('production_date', 'ASC')
                                ->whereNotIn('product_gudang.type', ['inventory_adjustment'])
                                ->where(function($query) use ($request) {
                                    if ($request->cari) {
                                        $query->where('product_id', $request->cari);
                                        // $query->orWhere('production_date', 'like', '%' . $request->cari . '%') ;
                                        // $query->orWhere('product_gudang.nama', 'like', '%' . $request->cari . '%') ;
                                        // $query->orWhere('customers.nama', 'like', '%' . $request->cari . '%') ;
                                        // $query->orWhere('sub_item', 'like', '%' . $request->cari . '%') ;
                                        // $query->orWhere('packaging', 'like', '%' . $request->cari . '%') ;
                                    }
                                });

                $master         = clone $sql;
                $arrayData      = $master->get();
                
                // dd($arrayData);
                $arrayId        = array();
                foreach($arrayData as $item){
                    $arrayId[]  = $item->id;
                }
                $stringData     = implode(",",$arrayId);
                // dd($stringData);
                if($stringData){
                    $alokasi            = DB::select("select chiller_out,SUM(bb_item) AS total_qty_alokasi, ROUND(sum(bb_berat),2) AS total_berat_alokasi, sum(keranjang) as total_keranjang
                                                        FROM order_bahan_baku WHERE chiller_out IN(".$stringData.") 
                                                        AND `status` IN(1,2) AND proses_ambil ='frozen' AND deleted_at IS NULL GROUP BY chiller_out");
                    $alokasithawing     = DB::select("select item_id,SUM(qty) AS total_qty_orderthawing, ROUND(SUM(berat),2) AS total_berat_orderthawing
                                                        FROM thawing_requestlist WHERE item_id IN(".$stringData.") 
                                                        GROUP BY item_id");
                    $regrading          = DB::select("select gudang_id_keluar,SUM(qty_awal) AS total_qty_regrading, ROUND(sum(berat_awal),2) AS total_berat_regrading
                                                        FROM product_gudang WHERE gudang_id_keluar IN(".$stringData.") 
                                                        AND `status`='4' AND `type`='grading_ulang'
                                                        GROUP BY gudang_id_keluar");
                    $musnahkan          = DB::select("select item_id, SUM(qty) AS total_qty_musnahkan, ROUND(sum(berat),2) AS total_berat_musnahkan 
                                                        FROM musnahkan_temp JOIN musnahkan on musnahkan.id=musnahkan_temp.musnahkan_id WHERE gudang_id NOT IN (2,4,23,24) AND item_id IN(".$stringData.")
                                                        AND musnahkan.deleted_at IS NULL GROUP BY item_id ");
                }

                $arraymodification      = [];
                foreach($arrayData as $data){
                    $total_qty_alokasi          = 0;
                    $total_berat_alokasi        = 0;
                    $total_keranjang            = 0;
                    $total_qty_orderthawing     = 0;
                    $total_berat_orderthawing   = 0;
                    $total_qty_regrading        = 0;
                    $total_berat_regrading      = 0;
                    $total_qty_musnahkan        = 0;
                    $total_berat_musnahkan      = 0;

                    foreach($alokasi as $val){
                        if($data->id == $val->chiller_out){
                            $total_qty_alokasi              = $val->total_qty_alokasi;
                            $total_berat_alokasi            = floatval($val->total_berat_alokasi) ?? 0;
                            $total_keranjang                = $val->total_keranjang ?? 0;
                        }
                    }
                    foreach($alokasithawing as $valthawing){
                        if($data->id == $valthawing->item_id){
                            $total_qty_orderthawing         = $valthawing->total_qty_orderthawing;
                            $total_berat_orderthawing       = floatval($valthawing->total_berat_orderthawing) ?? 0;
                        }
                    }
                    foreach($regrading as $val2){
                        if($data->id == $val2->gudang_id_keluar){
                            $total_qty_regrading            = $val2->total_qty_regrading;
                            $total_berat_regrading          = floatval($val2->total_berat_regrading) ?? 0;
                        }
                    }
                    foreach($musnahkan as $valmus){
                        if($data->id == $valmus->item_id){
                            $total_qty_musnahkan            = $valmus->total_qty_musnahkan;
                            $total_berat_musnahkan          = floatval($valmus->total_berat_musnahkan) ?? 0;
                        }
                    }
                    $arraymodification[] = [
                        "id"                        => $data->id,
                        "code"                      => $data->code,
                        "production_date"           => $data->production_date,
                        "nama"                      => $data->nama,
                        "sub_item"                  => $data->sub_item,
                        "packaging"                 => $data->packaging,
                        "qty_awal"                  => $data->qty_awal,
                        "berat_awal"                => floatval($data->berat_awal),
                        "nama_customer"             => $data->nama_customer,
                        "total_qty_alokasi"         => $total_qty_alokasi,
                        "total_berat_alokasi"       => $total_berat_alokasi,
                        "total_keranjang"           => $total_keranjang,
                        "total_qty_orderthawing"    => $total_qty_orderthawing,
                        "total_berat_orderthawing"  => $total_berat_orderthawing,
                        "total_qty_regrading"       => $total_qty_regrading,
                        "total_berat_regrading"     => $total_berat_regrading,
                        "total_qty_musnahkan"       => $total_qty_musnahkan,
                        "total_berat_musnahkan"     => $total_berat_musnahkan,
                        'sisaQty'                   => $data->qty_awal - $total_qty_alokasi - $total_qty_orderthawing - $total_qty_regrading - $total_qty_musnahkan,
                        'sisaBerat'                 => $data->berat_awal - $total_berat_alokasi - $total_berat_orderthawing - $total_berat_regrading - $total_berat_musnahkan
                    ];
                }
                $collection                         = json_decode(json_encode($arraymodification));
                // dd($collection);
                $source                             = array_filter($collection, function($vn){
                    return $vn->sisaBerat > 0;
                });
                $stock                              = Applib::paginate($source,15);
                // dd($stock);
                return view('admin.pages.warehouse.requeststock', compact('stock', 'tanggal', 'akhir', 'id'));
            } else

            if ($request->key == 'requestthawinglist') {
                $data   =   Thawinglist::where('thawing_id', $id)->get();
                return view('admin.pages.warehouse.requestthawinglist', compact('data'));
            } else

            if ($request->key == 'editThawing') {
                $data   =   Thawing::where('id', $id)
                            ->first();

                if ($data) {
                    $key        =   $request->key;
                    $tanggal    =   $request->tanggal ?? date("Y-m-d");
                    $akhir      =   $request->akhir ?? date("Y-m-d");

                    return view('admin.pages.warehouse.requesttimbang', compact('data', 'tanggal', 'akhir', 'key'));
                }
                return redirect()->route('warehouse.index');
            }

            else {
                $data   =   Thawing::where('id', $id)
                            ->where('status', 1)
                            ->first();

                if ($data) {
                    $tanggal    =   $request->tanggal ?? date("Y-m-d");
                    $akhir      =   $request->akhir ?? date("Y-m-d");

                    return view('admin.pages.warehouse.requesttimbang', compact('data', 'tanggal', 'akhir'));
                }
                return redirect()->route('warehouse.index');
            }
        }
        return redirect()->route("index");
    }


    public function request_thawingdestroy(Request $request, $id)
    {
        if (User::setIjin(15)) {
            // GET THAWING HEADER
            $checkThawing       = Thawing::where('id', $id)->first();

            // GET THAWING ITEM 
            $getThawingListItem = Thawinglist::where('thawing_id', $id)->where('id', $request->id)->first();

            
            
            // CEK JIKA ADA HEADER
            if ($checkThawing) {
                DB::beginTransaction();
                // CEK JIKA STATUSNYA SUDAH PERNAH DISIMPAN
                // dd($getThawingListItem);
                if ($checkThawing->status == 2) {
                    // dd($checkThawing->status);
                    // dd($getThawingListItem->status);

                    if ($getThawingListItem ->status == NULL) {

                        if (!$getThawingListItem->delete()) {
                            DB::rollBack();
                            return response()->json([
                                'status'        =>   400,
                                'msg'           =>   'Item tidak dapat didelete.',
                            ]);
                        }
                    } else {

                        // GET DATA CHILLER
                        $checkItemChiller = Item::where('nama', str_replace(' FROZEN', '', ($getThawingListItem->gudang->nama)))->first();
                        $checkDataChiller = Chiller::where('table_name', 'thawing_request')->where('item_name', $checkItemChiller->nama)->where('tanggal_produksi', $checkThawing->tanggal_request)->first();
                        if ($checkDataChiller) {
    
                            if ($checkDataChiller->stock_berat == $getThawingListItem->berat && $checkDataChiller->stock_item == $getThawingListItem->qty) {
    
                                // DELETE DI CHILLER JIKA HANYA 1 ITEM DAN SAMA
                                if (!$checkDataChiller->delete()) {
                                    DB::rollBack();
                                    return response()->json([
                                        'status'        =>   400,
                                        'msg'           =>   'Item tidak dapat didelete.',
                                    ]);
                                }
                                
    
                            } else {
                                // DIKURANGI DI CHILLER
                                if ($getThawingListItem->status != NULL) {

                                    if ($checkDataChiller->stock_berat >= $getThawingListItem->berat && $checkDataChiller->stock_item >= $getThawingListItem->qty) {
        
                                        $checkDataChiller->stock_berat  -=  $getThawingListItem->berat;
                                        $checkDataChiller->stock_item   -=  $getThawingListItem->qty;
                                        $checkDataChiller->berat_item   -=  $getThawingListItem->berat;
                                        $checkDataChiller->qty_item     -=  $getThawingListItem->qty;
                                        // $checkDataChiller->save();
        
                                        if (!$checkDataChiller->save()) {
                                            DB::rollBack();
                                            return response()->json([
                                                'status'        =>   400,
                                                'msg'           =>   'Item tidak dapat didelete.',
                                            ]);
                                        }
                                    } else {
                                        // RETURN TIDAK BISA DELETE KARENA KURANG QTY / BERAT
                                        DB::rollBack();
                                        return response()->json([
                                            'status'        =>   400,
                                            'msg'           =>   'Item tidak dapat didelete karena qty/berat kurang pada chiller.',
                                        ]);
        
                                    }

                                } 
                            }
                            
    
                            // KEMBALIKAN CS
                            $getDataGudangThawing   = Product_gudang::where('type', 'thawing_request')->where('request_thawing', $request->id)->first();
                            $getDataGudangMasuk     = Product_gudang::where('id', $getThawingListItem->item_id)->first();
                            if ($getDataGudangThawing) {
                                // KEMBALIKAN STOCK
                                if ($getDataGudangMasuk) {
    
                                    $getDataGudangMasuk->qty    += $getDataGudangThawing->qty;
                                    $getDataGudangMasuk->berat  += $getDataGudangThawing->berat;
    
                                    // LALU DELETE DATA THAWING DAN SAVE DATA GUDANG
                                    // $getDataGudangMasuk->save();
                                    // $getDataGudangThawing->delete();
                                    
                                    
                                    if (!$getDataGudangMasuk->save()) {
                                        DB::rollBack();
                                        return response()->json([
                                            'status'        =>   400,
                                            'msg'           =>   'Item tidak dapat didelete.',
                                        ]);
                                    }
    
                                    if (!$getDataGudangThawing->delete()) {
                                        DB::rollBack();
                                        return response()->json([
                                            'status'        =>   400,
                                            'msg'           =>   'Item tidak dapat didelete.',
                                        ]);
                                    }
                                }
                            }
    
                        }

                    }


                    // dd($checkDataChiller);
                    // DELETE DI THAWING LIST
                    // $getThawingListItem->delete() ;

                    // if (!$getThawingListItem->delete()) {
                    //     DB::rollBack();
                    //     return response()->json([
                    //         'status'        =>   400,
                    //         'msg'           =>   'Item tidak dapat didelete.',
                    //     ]);
                    // }

                    
                    if (!$getThawingListItem->delete()) {
                        DB::rollBack();
                        return response()->json([
                            'status'        =>   400,
                            'msg'           =>   'Item tidak dapat didelete.',
                        ]);
                    }

                    $dataThawingNetsuite    = Netsuite::where('document_code', 'TW-'.$getThawingListItem->id)->get();


                    // dd($dataThawingNetsuite);
                    if ($dataThawingNetsuite) {
                        foreach ($dataThawingNetsuite as $listNS) {
                            if (!$listNS->delete()) {
                                DB::rollBack();
                                return response()->json([
                                    'status'        =>   400,
                                    'msg'           =>   'Dokumen NS tidak dapat didelete.',
                                ]);
                            }

                        }

                    }



                } else {
                    // HANYA DELETE DI THAWING LIST JIKA BELUM PERNAH DISIMPAN SEBELUMNYA

                    if (!$getThawingListItem->delete()) {
                        DB::rollBack();
                        return response()->json([
                            'status'        =>   400,
                            'msg'           =>   'Item tidak dapat didelete.',
                        ]);
                    }
                }
    
                DB::commit();
                return;
            }

        }
        return redirect()->route("index");
    }


    public function request_thawingproses($id)
    {
        if (User::setIjin(15)) {
            $data   =   Thawing::find($id) ;

            if (COUNT($data->thawing_listUpdate) > 0) {
                DB::beginTransaction();

                // CHECK DATA NS

                foreach ($data->thawing_listUpdate as $row) {

                    


                    if($row->berat>0){

                        $code   = "TW-".$row->id;

                        $gudang             =   Product_gudang::find($row->item_id);

                        if(!$gudang){
                            DB::rollBack();
                            return back()->with('status', 2)->with('message', 'Item tidak tersedia');
                        }

                        $gudang->qty        =   $gudang->qty - $row->qty;
                        $gudang->berat      =   $gudang->berat - $row->berat;

                        $item_finish    =   Item::where('nama', str_replace(' FROZEN', '', ($gudang->productitems->nama)))->first();
                        
                        $chiller                    =   Chiller::where('asal_tujuan', 'thawing')->where('item_id', $item_finish->id)->where('tanggal_produksi', $data->tanggal_request)->first() ?? new Chiller;
                        // dd($chiller->stock_berat + $row->berat);

                        $chiller->table_id          =   NULL;
                        $chiller->table_name        =   'thawing_request';
                        $chiller->asal_tujuan       =   'thawing';
                        $chiller->type              =   ($item_finish->category_id == "1") ? 'bahan-baku' : 'hasil-produksi';
                        $chiller->item_id           =   $item_finish->id;
                        $chiller->item_name         =   $item_finish->nama;
                        $chiller->jenis             =   'masuk';
                        $chiller->tanggal_produksi  =   $data->tanggal_request ;
                        $chiller->tanggal_potong    =   $data->tanggal_request ;
                        $chiller->qty_item          =   $chiller->qty_item + $row->qty;
                        $chiller->berat_item        =   $chiller->berat_item + $row->berat;
                        $chiller->stock_item        =   $chiller->stock_item + $row->qty;
                        $chiller->stock_berat       =   $chiller->stock_berat + $row->berat;
                        $chiller->status            =   2;

                        if($gudang){
                            $gudang_keluar                       =   new Product_gudang;
                            $gudang_keluar->product_id           =   $gudang->product_id;
                            $gudang_keluar->nama                 =   $gudang->nama;
                            $gudang_keluar->sub_item             =   $gudang->sub_item;
                            $gudang_keluar->table_id             =   $gudang->table_id;
                            $gudang_keluar->table_name           =   $gudang->table_name;
                            $gudang_keluar->order_id             =   $gudang->order_id;
                            $gudang_keluar->order_item_id        =   $gudang->order_item_id;
                            $gudang_keluar->parting              =   $gudang->parting ?? 0;
                            $gudang_keluar->qty_awal             =   $row->qty;
                            $gudang_keluar->berat_awal           =   $row->berat;
                            $gudang_keluar->qty                  =   $row->qty;
                            $gudang_keluar->berat                =   $row->berat;
                            $gudang_keluar->packaging            =   $gudang->packaging;
                            $gudang_keluar->plastik_group        =   Item::plastik_group($gudang->packaging);
                            $gudang_keluar->production_date      =   $data->tanggal_request ;
                            $gudang_keluar->type                 =   'thawing_request';
                            $gudang_keluar->request_thawing      =   $row->id ;
                            $gudang_keluar->gudang_id            =   $gudang->gudang_id;
                            $gudang_keluar->stock_type           =   $gudang->stock_type;
                            $gudang_keluar->jenis_trans          =   'keluar';
                            $gudang_keluar->gudang_id_keluar     =   $gudang->id;
                            $gudang_keluar->status               =   4;
                            $gudang_keluar->save();
                        }

                        if (!$chiller->save()) {
                            DB::rollBack();
                            return back()->with('status', 2)->with('message', 'Terjadi kesalahan');
                        }


                        if (!$gudang->save()) {
                            DB::rollBack();
                            return back()->with('status', 2)->with('message', 'Terjadi kesalahan');
                        }


                        try {
                            //code...

                            $nama_gudang_cs = env('NET_SUBSIDIARY', 'CGL')." - Cold Storage";
                            $gudang_cs      =   Gudang::where('code', $nama_gudang_cs)->first();

                            if($gudang_cs){
                                $id_location    =   $gudang_cs->netsuite_internal_id;
                                $location       =   $gudang_cs->code;
                                $from           =   $id_location;
                            }else{
                                $id_location    =   Gudang::find($gudang->gudang_id)->netsuite_internal_id;
                                $location       =   Gudang::find($gudang->gudang_id)->code;
                                $from           =   $id_location;
                            }

                            $label          =   'wo-4-thawing';

                            try {
                                //code...

                                $bom_kategori = Item::find($gudang->product_id);
                                if($bom_kategori){
                                    if($bom_kategori->category_id=="8"){
                                        $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL')." - THAWING AYAM PARTING BROILER FROZEN")
                                        ->first();

                                    }elseif($bom_kategori->category_id=="9"){
                                        $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL')." - THAWING AYAM PARTING MARINASI BROILER FROZEN")
                                        ->first();

                                    }elseif($bom_kategori->category_id=="7"){
                                        $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL')." - THAWING AYAM KARKAS BROILER FROZEN")
                                        ->first();

                                    } elseif ($bom_kategori->category_id=="11"){
                                        $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL')." - THAWING BONELESS BROILER FROZEN")
                                        ->first();

                                    } elseif ($bom_kategori->category_id=="10"){
                                        $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL')." - THAWING EVIS FROZEN")
                                        ->first();

                                    } else {
                                        $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL').' - THAWING AYAM KARKAS BROILER FROZEN')
                                        ->first();

                                    }

                                }else{
                                    $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL').' - THAWING AYAM KARKAS BROILER FROZEN')
                                    ->first();

                                }

                                $bom_id         =   $bom->id;
                                $id_assembly    =   $bom->netsuite_internal_id;
                                $item_assembly  =   $bom->bom_name ;


                                $proses =   [];
                                foreach ($bom->bomproses as $list) {
                                    $proses[]   =   [
                                        "type"              =>  "Component",
                                        "internal_id_item"  =>  (string)Item::item_sku($list->sku)->netsuite_internal_id,
                                        "item"              =>  $list->sku,
                                        "description"       =>  (string)Item::item_sku($list->sku)->nama,
                                        "qty"               =>  (string)Item::where('nama', 'AY - S')->first()->sku == $list->sku ? $list->qty_per_assembly : ($list->qty_per_assembly * $row->berat),
                                    ];
                                }

                            } catch (\Throwable $th) {
                                //throw $th;
                                $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL').' - THAWING AYAM KARKAS BROILER FROZEN')->first();
                                $bom_id         =   $bom->id;
                                $id_assembly    =   $bom->netsuite_internal_id;
                                $item_assembly  =   $bom->bom_name ;


                                $proses =   [];
                                foreach ($bom->bomproses as $list) {
                                    $proses[]   =   [
                                        "type"              =>  "Component",
                                        "internal_id_item"  =>  (string)Item::item_sku($list->sku)->netsuite_internal_id,
                                        "item"              =>  $list->sku,
                                        "description"       =>  (string)Item::item_sku($list->sku)->nama,
                                        "qty"               =>  (string)Item::where('nama', 'AY - S')->first()->sku == $list->sku ? $list->qty_per_assembly : ($list->qty_per_assembly * $row->berat),
                                    ];
                                }

                            }



                            $component      =   [[
                                "type"              =>  "Component",
                                "internal_id_item"  =>  (string)$gudang->productitems->netsuite_internal_id,
                                "item"              =>  (string)$gudang->productitems->sku,
                                "description"       =>  (string)$gudang->productitems->nama,
                                "qty"               =>  (string)$row->berat,
                            ]];


                            $plastik    =   [];
                            // if ($gudang->packaging) {
                            //     $plast      =   Item::where('nama', $gudang->packaging)->first();
                            //     if ($plast) {
                            //         $itembom    =   BomItem::select('qty_per_assembly')->where('bom_id', $bom_id)->where('item_id', $plast->id)->first();

                            //         if($itembom){
                            //             $plastik    =   [[
                            //                 "type"              =>  "Component",
                            //                 "internal_id_item"  =>  (string)$plast->netsuite_internal_id,
                            //                 "item"              =>  (string)$plast->sku,
                            //                 "description"       =>  (string)$plast->nama,
                            //                 "qty"               =>  (string)($itembom->qty_per_assembly * $row->berat),
                            //             ]];
                            //         }
                            //     }
                            // }

                            // MASUK CHILLER

                            // if ($item_finish->category_id == "1") {
                            $label = $gudang->gudangabf != NULL ? $gudang->gudangabf->tujuan : $gudang->type;
                            if ($item_finish->category_id == '1') {



                                
                                $label_ti   =   "ti_storage" . $gudang->type . "_chillerbb-thawing";
                                $to         =   Gudang::gudang_netid($this->nama_gudang_bb);


                                if (substr($item_finish->sku, 0, 5) == "12111" || substr($item_finish->sku, 0, 5) == "12112") {
                                    $transfer   =   [
                                        [
                                            "internal_id_item"  =>  (string)Item::item_sku("1100000001")->netsuite_internal_id ,
                                            "item"              =>  "1100000001",
                                            "qty_to_transfer"   =>  (string)$row->berat
                                        ]
                                    ];

                                    $finished_good  =   [[
                                        "type"              =>  "Finished Goods",
                                        "internal_id_item"  =>  (string)Item::item_sku("1100000001")->netsuite_internal_id ,
                                        "item"              =>  "1100000001",
                                        "description"       =>  "AYAM KARKAS BROILER (RM)",
                                        "qty"               =>  (string)$row->berat
                                    ]];
                                //check ayam MEMAR
                                } elseif (substr($item_finish->sku, 0, 5) == "12113") {
                                    $transfer   =   [
                                        [
                                            "internal_id_item"  =>  (string)Item::item_sku("1100000003")->netsuite_internal_id ,
                                            "item"              =>  "1100000003",
                                            "qty_to_transfer"   =>  (string)$row->berat
                                        ]
                                    ];

                                    $finished_good  =   [[
                                        "type"              =>  "Finished Goods",
                                        "internal_id_item"  =>  (string)Item::item_sku("1100000003")->netsuite_internal_id ,
                                        "item"              =>  "1100000003",
                                        "description"       =>  "AYAM MEMAR (RM)",
                                        "qty"               =>  (string)$row->berat
                                    ]];
                                // check ayam KAMPUNG
                                }


                            }else{

                                $label_ti   =   "ti_storage" .  $label . "_chillerfg-thawing";
                                $to         =   Gudang::gudang_netid($this->nama_gudang_fg);
                                $transfer   =   [
                                    [
                                        "internal_id_item"  =>  (string)Item::item_sku($item_finish->sku)->netsuite_internal_id ,
                                        "item"              =>  $item_finish->sku,
                                        "qty_to_transfer"   =>  (string)$row->berat
                                    ]
                                ];

                                $finished_good  =   [[
                                    "type"              =>  "Finished Goods",
                                    "internal_id_item"  =>  (string)Item::item_sku($item_finish->sku)->netsuite_internal_id ,
                                    "item"              =>  $item_finish->sku,
                                    "description"       =>  $item_finish->nama,
                                    "qty"               =>  (string)$row->berat
                                ]];

                            }


                            $produksi       =   array_merge($component, $proses, $plastik, $finished_good);
                            $nama_tabel     =   'thawing_requestlist';
                            $id_tabel       =   $row->id;

                            $wo = Netsuite::work_order_doc($nama_tabel, $id_tabel, $label, $id_assembly, $item_assembly, $id_location, $location, $produksi, null, $data->tanggal_request, $code);

                            $label          =   'wo-4-build-thawing';
                            $total          =   $row->berat;
                            $wop = Netsuite::wo_build_doc($nama_tabel, $id_tabel, $label, $id_assembly, $item_assembly, $id_location, $location, $total, $produksi, $wo->id, $data->tanggal_request, $code);

                            Netsuite::transfer_inventory_doc($nama_tabel, $id_tabel, $label_ti, $id_location, $location, $from, $to, $transfer, $wop->id, $data->tanggal_request, $code);

                        } catch (\Throwable $th) {
                            //throw $th;

                            DB::rollBack();
                            return back()->with('status', 2)->with('message', "BOM tidak ditemukan, ". $th->getMessage());
                        }

                    }


                    // SAVE THAWING LIST STATUS 1 = SELESAI
                    $row->status = 1;
                    $row->save();

                }


                $thawing            =   Thawing::find($id) ;
                $thawing->status    =   2 ;
                if (!$thawing->save()) {
                    DB::rollBack();
                    return back()->with('status', 2)->with('message', 'Terjadi kesalahan');
                }

                DB::commit();

                // try {
                //     Chiller::recalculate_chiller($chiller->id);
                // } catch (\Throwable $th) {

                // }

                return redirect()->route('thawingproses.index')->with('status', 1)->with('message', 'Proses thawing berhasil diselesaikan');
            } else {
                return back()->with('status', 2)->with('message', 'Stock belum ada yang diambil');
            }
        }
        return redirect()->route("index");
    }


    public function request_thawingstore(Request $request, $id)
    {
        if (User::setIjin(15)) {
            // CEK DATA REQUEST

            
            $cekDataThawing         =   Thawing::find($id);
            $isInArray              =   [];
            foreach (json_decode($cekDataThawing->item) as $i => $item) {
                $isInArray[]        =   $item->item;
            }   
            
            $cekDataRequest         = Product_gudang::where('id', $request->id)->first()->product_id;
            $sisaQtyGudang          = Product_gudang::ambilsisaproductgudang($request->id,'qty_awal','qty','bb_item');
            $sisaBeratGudang        = Product_gudang::ambilsisaproductgudang($request->id,'berat_awal','berat','bb_berat');
            $convertSisaBerat       = number_format((float)$sisaBeratGudang, 2, '.', '');

            if ($request->berat > $convertSisaBerat) {
                // DB::rollBack() ;
                $data['status'] =   400 ;
                $data['msg']    =   'Berat Bahan Baku Kurang, silakan refresh halaman untuk melihat stock asli' ;
                return $data ;
            }

            if (in_array($cekDataRequest, $isInArray)) {
                $thawing                =   new Thawinglist;
                $thawing->thawing_id    =   $id ;
                $thawing->item_id       =   $request->id ; // Untuk yang ini menggunakan ID dari Produk Gudang ya sekarang, kalau yang dulu gak tahu pakai yang mana.
                $thawing->berat         =   $request->berat ;
                $thawing->qty           =   $request->qty ;
                $thawing->save() ;
            } else {
                return response()->json([
                    'status'        =>   400,
                    'msg'           =>   'Item tidak sesuai request thawing.',
                    'inArray'       =>   $isInArray
                ]);
            }

        }
        return redirect()->route("index");
    }


    public function edit($id)
    {
        if (User::setIjin(15)) {
            $gudang =   Product_gudang::find($id);
            $gudangambil = Product_gudang::find($gudang->gudang_id_keluar);

            return view('admin.pages.warehouse.timbang', compact('gudang','gudangambil'));
        }
        return redirect()->route("index");
    }

    public function timbang(Request $request, $id)
    {
        if (User::setIjin(15)) {

            $validator  =    Validator::make($request->all(), [
                'result' =>  'required',
                'berat' =>  'required',
            ]);

            if ($validator->fails()) {
                return back()->with('status', 2)->with('message', 'Data tidak lengkap. Silahkan ulangi kembali');
            }

            DB::beginTransaction();

            $gudang         =   Product_gudang::find($id);
            $qty            =   $gudang->qty - $request->result;
            $berat          =   $gudang->berat - $request->berat;

            $item_finish    =   Item::where('nama', str_replace(' FROZEN', '', $gudang->nama))
                                ->first();

            $code           =   'TW-'.$id ;
            $tanggal        =   date('Y-m-d') ;

            if ($berat == 0) {

                $data                   =   new Product_gudang;
                $data->nama             =   $gudang->nama;
                $data->product_id       =   $gudang->product_id;
                $data->sub_item         =   $gudang->sub_item;
                $data->table_name       =   $gudang->table_name;
                $data->table_id         =   $gudang->table_id;
                $data->order_id         =   $gudang->order_id;
                $data->order_item_id    =   $gudang->order_item_id;
                $data->qty_awal         =   $request->result;
                $data->berat_awal       =   $request->berat;
                $data->qty              =   $request->result;
                $data->berat            =   $request->berat;
                $data->packaging        =   $gudang->packaging;
                $data->parting          =   $gudang->parting ?? 0;
                $data->plastik_group        =   Item::plastik_group($gudang->packaging);
                $data->palete           =   $gudang->palete;
                $data->expired          =   $gudang->expired;
                $data->production_date  =   Carbon::now();
                $data->type             =   'thawing';
                $data->stock_type       =   'produksi';
                $data->jenis_trans      =   'keluar';
                $data->status           =   4;
                if (!$data->save()) {
                    DB::rollBack();
                    return back()->with('status', 2)->with('message', 'Terjadi kesalahan');
                }

                $chiller                    =   Chiller::where('asal_tujuan', 'thawing')->where('item_id', $item_finish->id)->where('tanggal_produksi', date('Y-m-d'))->first() ?? new Chiller;
                $chiller->table_id          =   $gudang->id;
                $chiller->table_name        =   'product_gudang';
                $chiller->asal_tujuan       =   'thawing';
                $chiller->type              =   'bahan-baku';
                $chiller->item_id           =   $item_finish->id;
                $chiller->item_name         =   $item_finish->nama;
                $chiller->jenis             =   'masuk';
                $chiller->tanggal_produksi  =   Carbon::now();
                $chiller->tanggal_potong    =   $gudang->gudangabf->abf_chiller->tanggal_potong ?? null;
                $chiller->qty_item          =   $chiller->qty_item + $request->result;
                $chiller->berat_item        =   $chiller->berat_item + $request->berat;
                $chiller->stock_item        =   $chiller->stock_item + $request->result;
                $chiller->stock_berat       =   $chiller->stock_berat + $request->berat;
                $chiller->status            =   2;

                if (!$chiller->save()) {
                    DB::rollBack();
                    return back()->with('status', 2)->with('message', 'Terjadi kesalahan');
                }

                // MASUK CHILLER
                $label      =   $data->gudangabf != NULL ? $data->gudangabf->tujuan : $data->type;
                $label_ti   =   "ti_storage" . $label . "_chillerbb";
                $to         =   Gudang::gudang_netid($this->nama_gudang_bb);
                $transfer   =   [
                    [
                        "internal_id_item"  =>  (string)$chiller->chillitem->netsuite_internal_id,
                        "item"              =>  (string)$chiller->chillitem->sku,
                        "qty_to_transfer"   =>  (string)$chiller->berat_item
                    ]
                ];

                $gudang->status     =   4;
                if (!$gudang->save()) {
                    DB::rollBack();
                    return back()->with('status', 2)->with('message', 'Terjadi kesalahan');
                }
            } else {

                $chiller                    =   Chiller::where('asal_tujuan', 'thawing')->where('item_id', $item_finish->id)->where('tanggal_produksi', date('Y-m-d'))->first() ?? new Chiller;
                $chiller->table_id          =   $gudang->id;
                $chiller->table_name        =   'product_gudang';
                $chiller->asal_tujuan       =   'thawing';
                $chiller->type              =   'bahan-baku';
                $chiller->item_id           =   $item_finish->id;
                $chiller->item_name         =   $item_finish->nama;
                $chiller->jenis             =   'masuk';
                $chiller->tanggal_produksi  =   Carbon::now();
                $chiller->tanggal_potong    =   $gudang->gudangabf->abf_chiller->tanggal_potong ?? null;
                $chiller->qty_item          =   $chiller->qty_item + $request->result;
                $chiller->berat_item        =   $chiller->berat_item + $request->berat;
                $chiller->stock_item        =   $chiller->stock_item + $request->result;
                $chiller->stock_berat       =   $chiller->stock_berat + $request->berat;
                $chiller->status            =   2;

                try {
                    Chiller::recalculate_chiller($chiller->id);
                } catch (\Throwable $th) {

                }

                if (!$chiller->save()) {
                    DB::rollBack();
                    return back()->with('status', 2)->with('message', 'Terjadi kesalahan');
                }

                $masyuk                     =   new Product_gudang;
                $masyuk->product_id         =   $gudang->product_id;
                $masyuk->nama               =   $gudang->nama;
                $masyuk->sub_item           =   $gudang->sub_item;
                $masyuk->table_name         =   $gudang->table_name;
                $masyuk->table_id           =   $gudang->table_id;
                $masyuk->order_id           =   $gudang->order_id;
                $masyuk->order_item_id      =   $gudang->order_item_id;
                $masyuk->qty_awal           =   $qty;
                $masyuk->berat_awal         =   $berat;
                $masyuk->qty                =   $qty;
                $masyuk->berat              =   $berat;
                $masyuk->palete             =   $gudang->palete;
                $masyuk->parting            =   $gudang->parting ?? 0;
                $masyuk->expired            =   $gudang->expired;
                $masyuk->gudang_id          =   $gudang->gudang_id;
                $masyuk->packaging          =   $gudang->packaging;
                $masyuk->plastik_group      =   Item::plastik_group($gudang->packaging);
                $masyuk->production_date    =   Carbon::now();
                $masyuk->type               =   $gudang->type;
                $masyuk->stock_type         =   $gudang->stock_type;
                $masyuk->jenis_trans        =   'masuk';
                $masyuk->status             =   2;

                // MASUK CHILLER
                $label      =   $masyuk->gudangabf != NULL ? $masyuk->gudangabf->tujuan : $masyuk->type;
                $label_ti   =   "ti_storage" . $label . "_chillerbb";
                $to         =   Gudang::gudang_netid($this->nama_gudang_bb);
                $transfer   =   [
                    [
                        "internal_id_item"  =>  (string)$chiller->chillitem->netsuite_internal_id,
                        "item"              =>  (string)$chiller->chillitem->sku,
                        "qty_to_transfer"   =>  (string)$chiller->berat_item
                    ]
                ];

                if (!$masyuk->save()) {
                    DB::rollBack();
                    return back()->with('status', 2)->with('message', 'Terjadi kesalahan');
                }

                $gudang->status     =   4;
                if (!$gudang->save()) {
                    DB::rollBack();
                    return back()->with('status', 2)->with('message', 'Terjadi kesalahan');
                }
            }

            $itemgudang                 =   Product_gudang::where('jenis_trans', 'masuk')
                                            ->where('type', '!=', 'thawing')
                                            ->where('gudang_id', $gudang->gudang_id)
                                            ->where('table_name', $gudang->table_name)
                                            ->where('table_id', $gudang->table_id)
                                            ->first() ;

            $itemgudang->qty            =   $itemgudang->qty - $request->result ;
            $itemgudang->berat          =   $itemgudang->berat - $request->berat ;
            if (!$itemgudang->save()) {
                DB::rollBack();
                return back()->with('status', 2)->with('message', 'Terjadi kesalahan');
            }

            DB::commit();

            try {
                Chiller::recalculate_chiller($chiller->id);
            } catch (\Throwable $th) {

            }

            $id_location    =   Gudang::find($gudang->gudang_id)->netsuite_internal_id;
            $location       =   Gudang::find($gudang->gudang_id)->code;
            $from           =   $id_location;
            $label          =   'wo-4';


            $component      =   [[
                "type"              =>  "Component",
                "internal_id_item"  =>  (string)Item::where('nama', $gudang->nama)->first()->netsuite_internal_id ?? NULL,
                "item"              =>  (string)Item::where('nama', $gudang->nama)->first()->sku ?? NULL,
                "description"       =>  (string)$gudang->nama,
                "qty"               =>  (string)$data->berat,
            ]];

            $bom_kategori = Item::find($gudang->product_id);
            if($bom_kategori){
                if($bom_kategori->category_id=="8"){
                    $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL')." - THAWING AYAM PARTING BROILER FROZEN")
                    ->first();

                }elseif($bom_kategori->category_id=="9"){
                    $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL')." - THAWING AYAM PARTING MARINASI BROILER FROZEN")
                    ->first();

                }elseif($bom_kategori->category_id=="7"){
                    $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL')." - THAWING AYAM KARKAS BROILER FROZEN")
                    ->first();

                }elseif($bom_kategori->category_id=="11"){
                    $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL')." - THAWING BONELESS BROILER FROZEN")
                    ->first();

                }elseif($bom_kategori->category_id=="10"){
                    $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL')." - THAWING EVIS FROZEN")
                    ->first();

                }else{
                    $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL').' - THAWING AYAM KARKAS BROILER FROZEN')
                    ->first();

                }

            }else{
                $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL').' - THAWING AYAM KARKAS BROILER FROZEN')
                ->first();

            }

            $bom_id         =   $bom->id;
            $id_assembly    =   $bom->netsuite_internal_id;
            $item_assembly  =   $bom->bom_name ;


            $proses =   [];
            foreach ($bom->bomproses as $list) {
                $proses[]   =   [
                    "type"              =>  "Component",
                    "internal_id_item"  =>  (string)Item::item_sku($list->sku)->netsuite_internal_id,
                    "item"              =>  $list->sku,
                    "description"       =>  (string)Item::item_sku($list->sku)->nama,
                    "qty"               =>  (string)Item::where('nama', 'AY - S')->first()->sku == $list->sku ? $list->qty_per_assembly : ($list->qty_per_assembly * $data->berat),
                ];
            }

            $plastik    =   [];
            // if ($gudang->packaging) {
            //     $plast      =   Item::where('nama', $gudang->packaging)->first();
            //     if($plast){
            //         $itembom    =   BomItem::select('qty_per_assembly')->where('bom_id', $bom_id)->where('item_id', $plast->id)->first();
            //         $plastik    =   [[
            //             "type"              =>  "Component",
            //             "internal_id_item"  =>  (string)$plast->netsuite_internal_id,
            //             "item"              =>  (string)$plast->sku,
            //             "description"       =>  (string)$plast->nama,
            //             "qty"               =>  (string)($itembom->qty_per_assembly * $data->berat),
            //             ]];
            //     }
            // }

            $item_finish    =   Item::where('nama', str_replace(' FROZEN', '', $gudang->productitems->nama))->first();
            $finished_good  =   [[
                "type"              =>  "Finished Goods",
                "internal_id_item"  =>  (string)$item_finish->netsuite_internal_id,
                "item"              =>  (string)$item_finish->sku,
                "description"       =>  (string)$item_finish->nama,
                "qty"               =>  (string)$data->berat,
            ]];

            $produksi       =   array_merge($component, $proses, $plastik, $finished_good);
            $nama_tabel     =   'product_gudang';
            $id_tabel       =   $data->id;

            $wo = Netsuite::work_order_doc($nama_tabel, $id_tabel, $label, $id_assembly, $item_assembly, $id_location, $location, $produksi, null, $tanggal, $code);

            $label          =   'wo-4-build';
            $total          =   $data->berat;
            $wop = Netsuite::wo_build_doc($nama_tabel, $id_tabel, $label, $id_assembly, $item_assembly, $id_location, $location, $total, $produksi, $wo->id, $tanggal, $code);

            Netsuite::transfer_inventory_doc($nama_tabel, $id_tabel, $label_ti, $id_location, $location, $from, $to, $transfer, $wop->id, $tanggal, $code);

            return redirect()->route('warehouse.index')->with('status', 1)->with('message', 'Data berhasil simpan');
        }
        return redirect()->route("index");
    }

    public function destroy($id)
    {
        if (User::setIjin(15)) {
            //
        }
        return redirect()->route("index");
    }


    public function warehouse_stock(Request $request)
    {
        if($request->key == 'detailkonsumen'){
            $tanggal_mulai  =   $request->tanggal_mulai ?? date('Y-m-d');
            $tanggal_akhir  =   $request->tanggal_akhir ?? date('Y-m-d');

            $data           =   Product_gudang::where('customer_id', $request->customer_id)
                                ->whereBetween('production_date', [$tanggal_mulai, $tanggal_akhir])
                                ->where('production_date', '>=', '2022-02-04')
                                ->orderBy('nama', 'asc')
                                ->paginate(20);

            $gudang         =   Product_gudang::selectRaw('gudang_id, (SUM(IF(jenis_trans="masuk",qty,0)) - SUM(IF(jenis_trans="masuk",0,qty))) as jumlah_qty, (SUM(IF(jenis_trans="masuk",berat,0)) - SUM(IF(jenis_trans="masuk",0,berat))) as jumlah_berat')->groupBy('gudang_id')
                                ->whereBetween('production_date', [$tanggal_mulai, $tanggal_akhir])
                                ->where('status', 2)
                                ->where('customer_id', $request->customer_id)
                                ->orderBy("gudang_id", "asc")
                                ->get();

            return view('admin.pages.warehouse.detail_konsumen', compact('tanggal_mulai', 'tanggal_akhir', 'data','gudang'));

        } else {

            $tanggal_mulai  =   $request->tanggal_mulai ?? date('Y-m-d');
            $tanggal_akhir  =   $request->tanggal_akhir ?? date('Y-m-d');

            // UNTUK PER 4 FEBRUARI

            $listayam4feb       = Product_gudang::selectRaw('nama, (SUM(IF(jenis_trans="masuk",qty,0)) - SUM(IF(jenis_trans="masuk",0,qty))) as jumlah_nama')->groupBy('nama')
                                ->whereBetween('production_date', [$tanggal_mulai, $tanggal_akhir])
                                ->where('production_date', '>=', '2022-02-04')
                                ->where('status', 2)
                                ->where('qty' ,'>', 0)
                                ->orderBy("nama")
                                ->get();

            $listplastik4feb    = Product_gudang::selectRaw('packaging, (SUM(IF(jenis_trans="masuk",qty,0)) - SUM(IF(jenis_trans="masuk",0,qty))) as jumlah_plastik')->groupBy('packaging')
                                ->whereBetween('production_date', [$tanggal_mulai, $tanggal_akhir])
                                ->where('production_date', '>=', '2022-02-04')
                                ->where('packaging', '!=', null)
                                ->where('qty' ,'>', 0)
                                ->where('status', 2)
                                ->orderBy('packaging')
                                ->get();

            $listkonsumen4feb   = Product_gudang::with('konsumen')->select('customer_id')->selectRaw('(SUM(IF(jenis_trans="masuk",qty,0)) - SUM(IF(jenis_trans="masuk",0,qty))) as jumlah_konsumen')
                                ->whereBetween('production_date', [$tanggal_mulai, $tanggal_akhir])
                                ->where('production_date', '>=', '2022-02-04')
                                ->where('status', 2)
                                ->where('qty' ,'>', 0)
                                ->groupBy('customer_id')
                                ->get();

            $liststock4feb      = Product_gudang::with('productgudang')->select('gudang_id')->selectRaw('(SUM(IF(jenis_trans="masuk",qty,0)) - SUM(IF(jenis_trans="masuk",0,qty))) as jumlah_stock')
                                ->whereBetween('production_date', [$tanggal_mulai, $tanggal_akhir])
                                ->where('production_date', '>=', '2022-02-04')
                                ->where('status', 2)
                                ->where('qty' ,'>', 0)
                                ->groupBy('gudang_id')
                                ->get();


            return view('admin.pages.warehouse.stock', compact('request', 'listayam4feb', 'listplastik4feb', 'listkonsumen4feb', 'liststock4feb', 'tanggal_mulai','tanggal_akhir'));
        }

    }

    public function injectGudang(){

        $gudang_nama = Product_gudang::whereNull('nama')->get();

        foreach($gudang_nama as $nama):
            $item = Item::find($nama->product_id);
            if($item){
                // echo $nama->product_id."<br>";
                // echo $item->nama."<br>";
                $nama->nama = $item->nama;
                $nama->save();
            }
        endforeach;


        // $gudang = Product_gudang::where('product_id', '!=', null)->where('nama', 'not like', '%frozen%')->where('jenis_trans', 'masuk')->get();
        // // $order_bb = Bahanbaku::where('nama', 'like', '%frozen%')->get();

        // // foreach($gudang as $gdg):
        // // endforeach;

        // foreach($gudang as $gdg):
        //     $new_item = Item::where('nama', $gdg->nama." FROZEN")->first();
        //     echo $gdg->nama.$new_item->nama;
        //     $gdg->product_id = $new_item->id;
        //     $gdg->nama = $new_item->nama;
        //     $gdg->save();
        //     echo "<br>";
        //     // $gdg->nama = $gdg->productitems->nama ?? "";
        //     // $gdg->save();
        // endforeach;

        // $keluar =   Product_gudang::where('jenis_trans', 'keluar')->where('nama', 'LIKE', '%frozen%')->get() ;

        // foreach ($keluar as $row) {
        //     $item   =   Item::where('nama', str_replace(' FROZEN', '', $row->nama))->first() ;

        //     $row->product_id    =   $item->id ;
        //     $row->nama          =   $item->nama ;
        //     $row->save() ;
        // }

        return count($gudang_nama);

    }

    public function warehouse_inout(Request $request)
    {
        // dd($request->search);
        $search             =   $request->filter ?? "";
        $mulai              =   $request->tanggal_mulai ?? date('Y-m-d');
        $sampai             =   $request->tanggal_akhir ?? date('Y-m-d');
        $jenis              =   $request->jenis ?? "";
        $nama_item          =   $request->nama_item ?? "";
        $kategori           =   $request->kategori ?? "";
        $marinasi           =   $request->marinasi ?? "";
        $sub_item           =   $request->sub_item ?? "";
        $grade              =   $request->grade ?? "";
        $customer           =   $request->customer ?? "";
        $orderby            =   $request->orderby ?? "";
        $sortby             =   $request->sortby ?? "";
        $id                 =   $request->id ?? "";
        $searchRedirect     = $request->search ?? 'no';

        if ($id != '' && $searchRedirect != 'no') {
            $masuk = Product_gudang::where('table_id', $id)->where('table_name', 'abf')->where('status', 2)->get();
        } else {
            $masuk      = Product_gudang::whereBetween('production_date', [$mulai, $sampai])
                                        ->where('production_date','>=', '2022-02-04')
                                        ->where(function($query) use ($jenis){
                                            if ($jenis == 'warehouse_masuk') {
                                                $query->where('jenis_trans', 'masuk');
                                                $query->whereIn('status', [1,2]);
                                            }
                                            if ($jenis == "warehouse_keluar") {
                                                $query->where('jenis_trans', 'keluar');
                                                $query->whereIn('status', [3,4]);
                                            }
                                        })
                                        ->where(function ($qs) use ($search){
                                            if ($search) {
                                                $qs->orWhere('label', 'like', '%' . $search . '%') ;
                                                $qs->orWhere('nama', 'like', '%' . $search . '%') ;
                                                $qs->orWhere('sub_item', 'like', '%' . $search . '%') ;
                                                $qs->orWhere('qty', 'like', '%' . $search . '%') ;
                                                $qs->orWhere('berat_timbang', 'like', '%' . $search . '%') ;
                                                $qs->orWhere('notes', 'like', '%' . $search . '%') ;
                                                $qs->orWhere('packaging', 'like', '%' . $search . '%') ;
                                                $qs->orWhere('palete', 'like', '%' . $search . '%') ;
                                                $qs->orWhereIn('product_id', Item::select('id')->where('nama', 'like', '%' . $search . '%'));
                                            }
                                        })
                                        ->where(function ($qup) use ($nama_item, $kategori, $marinasi, $sub_item){
                                            if ($nama_item) {
                                                $qup->where('product_id', $nama_item);
                                            }
    
                                            if ($kategori) {
                                                $qup->whereHas('productitems', function ($q) use ($kategori){
                                                    $q->where('items.category_id', $kategori);
                                                });
                                            }
    
                                            if ($marinasi == 'm') {
                                                $qup->where('nama', 'LIKE' , '%(M)%' );
                                            }
    
                                            if ($marinasi == 'non') {
                                                $qup->where('nama', 'NOT LIKE' , '%(M)%' );
                                            }
    
                                            if ($sub_item) {
                                                $qup->where('sub_item', 'LIKE' , '%'.$sub_item.'%');
                                            }
                                        })
                                        ->where(function ($qmid) use ($grade){
                                            if ($grade == 'grade a') {
                                                $qmid->where('grade_item', null);
                                            } else if ($grade == 'grade b') {
                                                $qmid->where('grade_item', 'grade b');
                                            }
                                        })
                                        ->where(function ($qmid) use ($customer,$orderby,$sortby){
                                            if ($customer) {
                                                $qmid->where('customer_id', $customer);
                                            }
                                        })
                                        ->where('nama', '!=', 'AY - S')
                                        ->orderBy('production_date', 'ASC');

        }
        
        // $result     =   [
        //     'qty'   =>  $masuk->whereNotIn('type',['inventory_adjustment'])->sum('qty_awal'),
        //     'kg'    =>  $masuk->whereNotIn('type',['inventory_adjustment'])->sum('berat_awal'),
        // ] ;
        if ($request->key == 'unduh') {
            $stock      =   $masuk->get();
            if ($jenis == 'warehouse_masuk') {
                $judul  =   "LAPORAN INBOUND";
            }
            if ($jenis == 'warehouse_keluar') {
                $judul = 'LAPORAN OUTBOUND';
            }
            return view('admin.pages.warehouse.unduh.excel_stock', compact('stock', 'judul', 'request','jenis'));
        } else {
            if ($orderby == 'customer') {
                $masuk = $masuk->orderBy('customer_id',$sortby == 'asc' ? 'ASC' : 'DESC');
            }
            if ($orderby == 'item') {
                $masuk = $masuk->orderBy('nama',$sortby == 'asc' ? 'ASC' : 'DESC');
            }
            if ($orderby == 'qty') {
                $masuk = $masuk->orderBy('qty_awal',$sortby == 'asc' ? 'ASC' : 'DESC');
            }
            if ($orderby == 'berat') {
                $masuk = $masuk->orderBy('berat_awal',$sortby == 'asc' ? 'ASC' : 'DESC');
            }
            $masuk      =   $masuk->paginate(20);

            $qty=null;
            $kg=null;
            foreach ($masuk as $val) {
                $qty += $val->qty_awal;
                $kg += $val->berat_awal;
            }
            $warehouse  =   Gudang::where('kategori', 'warehouse')
                            ->where('subsidiary', env('NET_SUBSIDIARY', 'CGL'))
                            ->where('code', 'NOT LIKE', "%abf%")
                            ->where('status', 1)
                            ->get();

            return view('admin.pages.warehouse.view_inout', compact('masuk', 'mulai', 'sampai', 'warehouse','jenis','qty','kg'));
        }
    }

    public function warehouse_keluar(Request $request)
    {
        $search     =   $request->filter ?? "";
        $gudang     =   $request->lokasi ?? "";
        $mulai      =   $request->tanggal_mulai ?? date('Y-m-d');
        $sampai     =   $request->tanggal_akhir ?? date('Y-m-d');
        $plastik    =   $request->plastik ?? "";
        $keluar     =   Product_gudang::where('jenis_trans', 'keluar')
                        ->whereIn('status', [3,4])
                        ->where(function($query) use ($search,$gudang,$plastik){
                            if ($search) {
                                $query->orWhere('label', 'like', '%' . $search . '%') ;
                                $query->orWhere('nama', 'like', '%' . $search . '%') ;
                                $query->orWhere('sub_item', 'like', '%' . $search . '%') ;
                                $query->orWhere('qty', 'like', '%' . $search . '%') ;
                                $query->orWhere('berat_timbang', 'like', '%' . $search . '%') ;
                                $query->orWhere('notes', 'like', '%' . $search . '%') ;
                                $query->orWhere('packaging', 'like', '%' . $search . '%') ;
                                $query->orWhere('palete', 'like', '%' . $search . '%') ;
                                $query->orWhereIn('product_id', Item::select('id')->where('nama', 'like', '%' . $search . '%'));
                            }
                            if($plastik){
                                $query->orWhere('packaging', 'like', '%' . $plastik . '%') ;
                            }

                            if ($gudang) {
                                $query->orWhere('gudang_id', 'like', '%' . $gudang . '%') ;
                            }
                        })
                        ->whereBetween('production_date', [$mulai, $sampai])
                        ->where('production_date','>=', '2022-02-04');

        $result     =   [
            'qty'   =>  $keluar->whereNotIn('type',['inventory_adjustment'])->sum('qty'),
            'kg'    =>  $keluar->whereNotIn('type',['inventory_adjustment'])->sum('berat'),
        ];

        if ($request->key == 'unduh') {
            $stock      =   $keluar->get();
            $judul      =   "LAPORAN OUTBOUND";
            return view('admin.pages.warehouse.unduh.excel_stock', compact('stock', 'judul', 'request'));
        } else {
            if ($request->field == 'nama') {
                $keluar =   $keluar->orderBy("nama", $request->orderby == 'asc' ? 'ASC' : 'DESC') ;
            }

            if ($request->field == 'qty') {
                $keluar =   $keluar->orderBy("qty", $request->orderby == 'asc' ? 'ASC' : 'DESC') ;
            }

            if ($request->field == 'berat') {
                $keluar =   $keluar->orderBy("berat", $request->orderby == 'asc' ? 'ASC' : 'DESC') ;
            }
            $keluar     =   $keluar->paginate(20);
            return view('admin.pages.warehouse.keluar', compact('keluar', 'mulai', 'sampai', 'result'));
        }
    }

    public function edit_warehouse_inout(Request $request)
    {
        $idinbound          = $request->id;
        $idabf              = $request->idabf;
        $warehouse          =   Gudang::where('kategori', 'warehouse')
                                ->where('subsidiary', env('NET_SUBSIDIARY', 'CGL'))
                                ->where('code', 'NOT LIKE', "%abf%")
                                ->where('status', 1)
                                ->get();
        $customer           = Customer::select('id','nama','kode')->get();
        $plastik            = Item::where('category_id',25)->where('subsidiary', env('NET_SUBSIDIARY', 'EBA'))->where('status', '1')->get();
        $sub_item           = Adminedit::where('type','item_name')->get();
        $plastikGroup       = Adminedit::where('type','plastik_group')->get();

        $data_edit          = Product_gudang::find($idinbound);

        $item               = Item::where('id',$data_edit->product_id)->first();
        $item_list          = Item::select('id','nama')->where('category_id',$item->category_id)->get();

        $karung             = Item::where('subsidiary', 'like', '%'.Session::get('subsidiary').'%')
                                ->where(function ($item) {
                                    $item->where('nama', 'like', 'KARUNG%')->orWhere('nama', 'like', 'KARTON%');
                                    $item->orWhere('nama', 'like', 'KARUNG%')->orWhere('nama', 'like', 'KARUNG%');
                                })
                                ->get();
        $sub_item_name   =    Adminedit::where('type','item_name')->get();

        $data_abf           = Abf::find($idabf);
        $cek_netsuite_wo='';
        if ($data_abf) {
            $cek_netsuite_wo    = Netsuite::select('id','record_type')->where('document_code', 'like', '%'.$data_abf->id.'%')->where('label', 'like', '%abf%')->where('record_type','work_order')->first();
        }

        return view('admin.pages.warehouse.modal.edit_inoutbound',compact('data_edit','cek_netsuite_wo','warehouse','customer','plastik','sub_item','plastikGroup','item_list','karung','sub_item_name'));
    }

    public function warehouse_masuk(Request $request)
    {
        // dd($request->filter);
        $search     =   $request->filter ?? "";
        $gudang     =   $request->lokasi ?? "";
        $mulai      =   $request->tanggal_mulai ?? date('Y-m-d');
        $sampai     =   $request->tanggal_akhir ?? date('Y-m-d');
        $plastik    =   $request->plastik ?? "";
        $titipan    =   $request->titipan ?? "";
        $masuk      =   Product_gudang::where('jenis_trans', 'masuk')
                        ->whereIn('status', [1, 2])
                        ->where(function($query) use ($search,$gudang,$plastik,$titipan){
                            if($search) {
                                $query->orWhere('label', 'like', '%' . $search . '%') ;
                                $query->orWhere('nama', 'like', '%' . $search . '%') ;
                                $query->orWhere('sub_item', 'like', '%' . $search . '%') ;
                                $query->orWhere('qty', 'like', '%' . $search . '%') ;
                                $query->orWhere('berat_timbang', 'like', '%' . $search . '%') ;
                                $query->orWhere('notes', 'like', '%' . $search . '%') ;
                                $query->orWhere('packaging', 'like', '%' . $search . '%') ;
                                $query->orWhere('palete', 'like', '%' . $search . '%') ;
                                $query->orWhere('table_id', 'like', '%' . $search . '%');
                                $query->orWhereIn('product_id', Item::select('id')->where('nama', 'like', '%' . $search . '%'));
                            }

                            if($plastik){
                                $query->orWhere('packaging', 'like', '%' . $plastik . '%') ;
                            }

                            if ($gudang) {
                                $query->orWhere('gudang_id', 'like', '%' . $gudang . '%') ;
                            }

                            if ($titipan == 'on') {
                                $query->orWhere('barang_titipan', 1) ;
                            }
                        })
                        ->where(function($query) use ($mulai, $sampai, $search) {
                            if (is_numeric($search)) {

                            } else {
                                $query->whereBetween('production_date', [$mulai, $sampai]);
                                $query->where('production_date','>=', '2022-02-04');
                            }
                        })->orderBy('production_date', 'ASC');
                        // ->whereBetween('created_at', [$mulai." 00:00:01", $sampai." 23:59:59"])
                        // ->where('production_date','>=', '2022-02-04');
        $result     =   [
            'qty'   =>  $masuk->whereNotIn('type',['inventory_adjustment'])->sum('qty_awal'),
            'kg'    =>  $masuk->whereNotIn('type',['inventory_adjustment'])->sum('berat_awal'),
        ] ;


        if ($request->key == 'unduh') {
            $stock      =   $masuk->get();
            $judul      =   "LAPORAN INBOUND";
            return view('admin.pages.warehouse.unduh.excel_stock', compact('stock', 'judul', 'request'));
        } else {
            if ($request->field == 'nama') {
                $masuk =   $masuk->orderBy("nama", $request->orderby == 'asc' ? 'ASC' : 'DESC') ;
            }

            if ($request->field == 'qty') {
                $masuk =   $masuk->orderBy("qty", $request->orderby == 'asc' ? 'ASC' : 'DESC') ;
            }

            if ($request->field == 'berat') {
                $masuk =   $masuk->orderBy("berat", $request->orderby == 'asc' ? 'ASC' : 'DESC') ;
            }

            if ($request->field == 'tanggal') {
                $masuk =   $masuk->orderBy("production_date", $request->orderby == 'asc' ? 'ASC' : 'DESC') ;
            }
            $masuk      =   $masuk->paginate(20);

            $warehouse  =   Gudang::where('kategori', 'warehouse')
                            ->where('subsidiary', env('NET_SUBSIDIARY', 'CGL'))
                            ->where('code', 'NOT LIKE', "%abf%")
                            ->where('status', 1)
                            ->get();

            return view('admin.pages.warehouse.masuk', compact('masuk', 'mulai', 'sampai', 'warehouse', 'result'));
        }
    }

    public function warehouse_requestthawing(Request $request)
    {
        $thawing    =   Thawing::whereBetween('tanggal_request', [$request->mulai ?? date('Y-m-d'), $request->akhir ?? date('Y-m-d')])
                        ->orderBy('id', 'DESC')
                        ->paginate(15) ;

        $items      =   Item::whereNotIn('category_id', ['21','22','23','24','25','26', '27', '28', '29', '30'])
                        ->get()
                        ->makeHidden(['sisa_qty', 'sisa_berat']);

        return view('admin.pages.warehouse.requestthawing', compact('thawing', 'items'));
    }

    public function warehouse_thawing(Request $request)
    {

        $mulai  = $request->mulai ?? date('Y-m-d');
        $sampai = $request->sampai ?? date('Y-m-d');
        $status = $request->status;
        $params = $request->params;
        $search = $request->search;
        $ordering = $request->ordering;
        $type   = $request->type;
        $id     = $request->id;
        // $thawing     =  Thawinglist::whereBetween('created_at', [$mulai . " 00:00:01", $sampai . " 23:59:59"])->with('relasi_thawing')->get();
        if(!$id){
            $thawing     =  Thawinglist::whereBetween('created_at', [$mulai . " 00:00:01", $sampai . " 23:59:59"])
                            ->get();
        } else {
            $thawing     =  Thawinglist::where('id',$id)
                            ->get();
        }

        // dd($thawing);

        if($request->key == 'unduh_thawing'){
            $thawing;
            $mulai  = $request->mulai ?? date('Y-m-d');
            $sampai = $request->sampai ?? date('Y-m-d');
            $id     = $request->id;
            return view('admin.pages.warehouse.unduh.excel_thawing_request', compact('thawing','request','mulai','sampai'));
        }

        // $total_timbang  = $thawing->total();

        return view('admin.pages.warehouse.thawing', compact('thawing', 'search', 'mulai', 'sampai', 'type','id'));
    }

    public function warehouse_abf(Request $request)
    {

        $mulai  =   $request->mulai ?? date('Y-m-d');
        $sampai =   $request->sampai ?? date('Y-m-d');
        $abf    =   Abf::whereBetween('created_at', [$mulai . " 00:00:01", $sampai . " 23:59:59"])
                    ->get();

        return view('admin.pages.warehouse.abf', compact('abf', 'mulai', 'sampai'));
    }

    public function warehouse_order(Request $request)
    {
        $search     =   $request->filter ?? "";
        $jenis      =   $request->jenis ?? "semua";
        $fresh      =   $request->fresh ?? "";
        $frozen     =   $request->frozen ?? "";
        $semua      =   $request->semua ?? "";
        $mulai      =   $request->tanggal_mulai ?? date('Y-m-d', strtotime('tomorrow'));
        $sampai     =   $request->tanggal_akhir ?? date('Y-m-d', strtotime('tomorrow'));
        $itemorder  =   $request->itemorder ?? "";
        $sales      =   $request->sales ?? "";
        $katcust    =   $request->katcustomer ?? "";
        $wilayah    =   $request->wilayah ?? "";

        $data   =   OrderItem::select('order_items.*', 'orders.nama', 'orders.no_so', 'orders.tanggal_kirim', 'orders.sales_id',
                            DB::raw("orders.nama as cust_nama"),
                            DB::raw("orders.created_at as created_at_order"),
                            DB::raw("order_items.edited as edit_item"),
                            DB::raw("order_items.deleted_at as delete_at_item"),
                            DB::raw("order_items.id as id"),
                            DB::raw("orders.status_so as order_status_so"),
                            DB::raw("marketing.nama_alias as marketing_nama"),
                            DB::raw("orders.keterangan as memo_header"),
                            DB::raw("orders.wilayah as wilayah")
                            )
                ->leftJoin('orders', 'orders.id', '=', 'order_items.order_id')
                ->leftJoin('items', 'items.id', '=', 'order_items.item_id')
                ->leftJoin('customers', 'customers.id', '=', 'orders.customer_id')
                ->leftJoin('marketing', 'marketing.netsuite_internal_id', '=', 'orders.sales_id')
                ->whereBetween('orders.tanggal_kirim', [$mulai,$sampai])
                ->where('sales_channel', '!=', 'By Product - Paket')
                ->where(function($query) use ($request, $search) {
                    if ($search) {
                        $query->orWhere('orders.nama', 'like', '%' . $search . '%') ;
                        $query->orWhere('orders.no_so', 'like', '%' . $search . '%') ;
                        $query->orWhere('order_items.nama_detail', 'like', '%' . $search . '%') ;
                    }
                })
                ->where(function($query) use ($katcust){
                    if($katcust !== ''){
                        $query->where('customers.kategori', $katcust);
                    }
                })
                ->where(function($query) use ($wilayah){
                    if($wilayah !== ''){
                        $query->where('orders.wilayah', $wilayah);
                    }
                })
                ->where(function($query) use ($jenis, $semua, $frozen, $fresh) {

                    if($semua=="on"){

                    }else{
                        if ($fresh == 'on') {
                            $query->where('order_items.nama_detail', 'not like', '%frozen%') ;
                        }
                        if ($frozen == 'on') {
                            $query->where('order_items.nama_detail', 'like', '%frozen%') ;
                        }
                    }
                });



        if ($request->key == 'unduh_order') {
            $data       =   $data->orderBy('id', 'asc')->get() ;
            return view('admin.pages.warehouse.unduh.excel_order', compact('data', 'request')) ;
        } else if($request->key == 'daftarordercust'){
            $clonedatacustomer      = clone $data;
            $daftarordercust = $clonedatacustomer->orderBy('id', 'asc')->groupBy('order_items.item_id')->withTrashed()->get();
            return view('admin.pages.warehouse.index.daftarordercust', compact('daftarordercust', 'request' , 'itemorder')) ;
        } else {

            $all                    = clone $data;
            $queryDataQty           = clone $data;
            $queryDataBeratSO       = clone $data;
            $queryDataCustomer      = clone $data;
            $queryDataFresh         = clone $data;
            $queryDataFrozen        = clone $data;
            $queryDataBeratDO       = clone $data;
            $queryDataEdited        = clone $data;
            $totalsum       =   [
                'sumqty'          =>  $queryDataQty->where('order_items.deleted_at', NULL)->where('status_so', '!=', 'closed')
                ->where(function ($query) use ($sales){
                    if ($sales != "") {
                        $query->where('orders.sales_id', $sales);
                    }
                })->where(function($query) use ($itemorder){
                    if($itemorder){
                        $query->where('order_items.nama_detail', $itemorder);
                    }
                })->sum('order_items.qty'),

                 // ------------------------------------------------------------------------------------------------------------------------------
                'sumberatso'        =>  $queryDataBeratSO->where('order_items.deleted_at', NULL)->where('status_so', '!=', 'closed')
                ->where(function ($query) use ($sales){
                    if ($sales != "") {
                        $query->where('orders.sales_id', $sales);
                    }
                })->where(function($query) use ($itemorder){
                    if($itemorder){
                        $query->where('order_items.nama_detail', $itemorder);
                    }
                })->sum('order_items.berat'),

                // ------------------------------------------------------------------------------------------------------------------------------
                'sumberatdo'        =>  (clone $data)->where('order_items.deleted_at', NULL)
                ->where(function ($query) use ($sales){
                    if ($sales != "") {
                        $query->where('orders.sales_id', $sales);
                    }
                })->where(function($query) use ($itemorder){
                    if($itemorder){
                        $query->where('order_items.nama_detail', $itemorder);
                    }
                })->sum('order_items.fulfillment_berat'),

                // ------------------------------------------------------------------------------------------------------------------------------
                'sumparting'        =>  $queryDataBeratSO->where('order_items.deleted_at', NULL)->where('status_so', '!=', 'closed')
                ->where(function ($query) use ($sales){
                    if ($sales != "") {
                        $query->where('orders.sales_id', $sales);
                    }
                })->where(function($query) use ($itemorder){
                    if($itemorder){
                        $query->where('order_items.nama_detail', $itemorder);
                    }
                })->sum('order_items.part'),

                // ------------------------------------------------------------------------------------------------------------------------------
                'sumqtydo'        =>  $queryDataBeratSO->where('order_items.deleted_at', NULL)->where('status_so', '!=', 'closed')
                ->where(function ($query) use ($sales){
                    if ($sales != "") {
                        $query->where('orders.sales_id', $sales);
                    }
                })->where(function($query) use ($itemorder){
                    if($itemorder){
                        $query->where('order_items.nama_detail', $itemorder);
                    }
                })->sum('order_items.fulfillment_berat'),

                 // ------------------------------------------------------------------------------------------------------------------------------
                'sumcustomer'     =>  $queryDataCustomer->groupBy('orders.customer_id')->where('status_so', '!=', 'closed')
                ->where(function ($query) use ($sales){
                    if ($sales != "") {
                        $query->where('orders.sales_id', $sales);
                    }
                })
                ->where(function($query) use ($itemorder){
                    if($itemorder){
                        $query->where('order_items.nama_detail', $itemorder);
                    }
                })->get()->count('orders.customer_id'),

                // ------------------------------------------------------------------------------------------------------------------------------
                'sumitemfresh'    =>  $queryDataFresh->where('order_items.nama_detail', 'not like', '%frozen%')->where('status_so', '!=', 'closed')
                ->where(function ($query) use ($sales){
                    if ($sales != "") {
                        $query->where('orders.sales_id', $sales);
                    }
                })->where(function($query) use ($itemorder){
                    if($itemorder){
                        $query->where('order_items.nama_detail', $itemorder);
                    }
                })->where('order_items.deleted_at', NULL)->count(),

                // ------------------------------------------------------------------------------------------------------------------------------
                'sumitemfrozen'   =>  $queryDataFrozen->where('order_items.nama_detail', 'like', '%frozen%')->where('status_so', '!=', 'closed')
                ->where(function ($query) use ($sales){
                    if ($sales != "") {
                        $query->where('orders.sales_id', $sales);
                    }
                })->where(function($query) use ($itemorder){
                    if($itemorder){
                        $query->where('order_items.nama_detail', $itemorder);
                    }
                })->where('order_items.deleted_at', NULL)->count(),

                'countedited'   =>  $queryDataEdited->where('order_items.edited', '!=', '0')->count(),
            ];

            $data       =   $data->orderBy('id', 'asc')
                            ->where(function ($query) use ($sales){
                                if ($sales != "") {
                                    $query->where('orders.sales_id', $sales);
                                }
                            })
                            ->where(function($query) use ($itemorder){
                                if($itemorder){
                                    $query->where('order_items.nama_detail', $itemorder);
                                }
                            })->withTrashed()->get() ;
            $alldata    =   $data;
            return view('admin.pages.warehouse.order', compact('alldata', 'totalsum', 'mulai', 'sampai', 'data', 'search', 'itemorder'));
        }

    }

    public function warehouse_nonlb(Request $request)
    {
        $mulai  =   $request->tanggal_mulai ?? date('Y-m-d');
        $sampai =   $request->tanggal_akhir ?? date('Y-m-d');

        $purchase   =   Purchasing::whereIn('id', PurchaseItem::select('purchasing_id')->where('description', 'like', '%FROZEN%'))
                        ->whereBetween('tanggal_potong', [$mulai, $sampai])
                        ->where('type_po', 'PO Karkas')
                        ->get();

        return view('admin.pages.warehouse.nonlb', compact('purchase'));
    }

    public function warehouse_export(Request $request)
    {

        $stock  = Product_gudang::where('jenis_trans', 'masuk')->where('status', '2')->get();


        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=cgl-chiller-" . date('Y-m-d-H:i:s') . ".csv");
        $fp = fopen('php://output', 'w');
        fputcsv($fp, ["sep=,"]);

        $data = array(
            "No",
            "Nama",
            "No Mobil",
            "Tanggal Produksi",
            "Qty",
            "Berat",
            "Asal Tujuan"
        );
        fputcsv($fp, $data);

        foreach ($stock as $no => $item) :

            $data = array(
                $no + 1,
                $item->item_name,
                $item->no_mobil ?? '',
                $item->tanggal_produksi,
                $item->stock_item,
                str_replace(".", ",", $item->stock_berat),
                $item->tujuan,
            );
            fputcsv($fp, $data);
        endforeach;

        fclose($fp);
    }

    public function thawingfg(Request $request)
    {
        $mulai  =   $request->mulai ?? date('Y-m-d');
        $sampai =   $request->sampai ?? date('Y-m-d');

        $stock  =   Product_gudang::where('jenis_trans', 'masuk')
                    ->whereIn('status', [2])
                    ->where('type', '!=', 'inventory_adjustment')
                    ->whereBetween('production_date', [$mulai, $sampai])
                    ->get();

        return view('admin.pages.thawing.thawingfg', compact('stock', 'mulai', 'sampai'));
    }

    public function storethawingfg(Request $request)
    {
        $thawing        =   Product_gudang::find($request->idgudang);

        $item           =   Item::find($thawing->product_id);
        $stritem        =   str_replace(' FROZEN','', $item->nama);

        $itembaru       =   Item::where('nama', $stritem)->first();


        $id_location    =   Gudang::find($thawing->gudang_id)->netsuite_internal_id;
        $location       =   Gudang::find($thawing->gudang_id)->code;
        $from           =   $id_location;

        $label          =   'wo-8';


        $code           =   'TW-'.$thawing->id ;
        $tanggal        =   date('Y-m-d') ;

        $component      =   [[
            "type"              =>  "Component",
            "internal_id_item"  =>  (string)$thawing->netsuite_internal_id,
            "item"              =>  (string)$thawing->sku,
            "description"       =>  (string)$thawing->nama,
            "qty"               =>  (string)$request->berat,
        ]];

        $bom_kategori = Item::find($thawing->product_id);
        if($bom_kategori){
            if($bom_kategori->category_id=="8"){
                $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL')." - THAWING AYAM PARTING BROILER FROZEN")
                ->first();

            }elseif($bom_kategori->category_id=="9"){
                $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL')." - THAWING AYAM PARTING MARINASI BROILER FROZEN")
                ->first();

            }elseif($bom_kategori->category_id=="7"){
                $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL')." - THAWING AYAM KARKAS BROILER FROZEN")
                ->first();

            }elseif($bom_kategori->category_id=="11"){
                $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL')." - THAWING BONELESS BROILER FROZEN")
                ->first();

            }elseif($bom_kategori->category_id=="10"){
                $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL')." - THAWING EVIS FROZEN")
                ->first();

            }else{
                $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL').' - THAWING AYAM KARKAS BROILER FROZEN')
                ->first();

            }

        }else{
            $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL').' - THAWING AYAM KARKAS BROILER FROZEN')
            ->first();

        }

        $bom_id         =   $bom->id;
        $id_assembly    =   $bom->netsuite_internal_id;
        $item_assembly  =   $bom->bom_name ;


        $proses =   [];
        foreach ($bom->bomproses as $list) {
            $proses[]   =   [
                "type"              =>  "Component",
                "internal_id_item"  =>  (string)Item::item_sku($list->sku)->netsuite_internal_id,
                "item"              =>  $list->sku,
                "description"       =>  (string)Item::item_sku($list->sku)->nama,
                "qty"               =>  (string)Item::where('nama', 'AY - S')->first()->sku == $list->sku ? $list->qty_per_assembly : ($list->qty_per_assembly * $request->berat),
            ];
        }

        // MASUK CHILLER
        $label      =   $thawing->gudangabf != NULL ? $thawing->gudangabf->tujuan : $thawing->type;

        $label_ti   =   "ti_storage" . $label. "_chillerfg";
        $to         =   Gudang::gudang_netid($this->nama_gudang_bb);
        $transfer   =   [
            [
                "internal_id_item"  =>  (string)Item::item_sku("1100000001")->netsuite_internal_id,
                "item"              =>  "1100000001",
                "qty_to_transfer"   =>  (string)$request->berat
            ]
        ];

        $finished_good  =   [[
            "type"              =>  "Finished Goods",
            "internal_id_item"  =>  (string)$itembaru->netsuite_internal_id,
            "item"              =>  $itembaru->sku,
            "description"       =>  $itembaru->nama,
            "qty"               =>  (string)$request->berat
        ]];

        $produksi       =   array_merge($component, $proses, $finished_good);
        $nama_tabel     =   'product_gudang';
        $id_tabel       =   $request->idgudang;

        $wo = Netsuite::work_order_doc($nama_tabel, $id_tabel, $label, $id_assembly, $item_assembly, $id_location, $location, $produksi, null, $tanggal, $code);

        $label          =   'wo-8-build';
        $total          =   $request->berat;
        $wop = Netsuite::wo_build_doc($nama_tabel, $id_tabel, $label, $id_assembly, $item_assembly, $id_location, $location, $total, $produksi, $wo->id, $tanggal, $code);

        Netsuite::transfer_inventory_doc($nama_tabel, $id_tabel, $label_ti, $id_location, $location, $from, $to, $transfer, $wop->id, $tanggal, $code);

        DB::beginTransaction() ;

        $gudang                     =   new Product_gudang();
        $gudang->nama               =   $thawing->nama;
        $gudang->product_id         =   $thawing->product_id;
        $gudang->sub_item           =   $thawing->sub_item;
        $gudang->table_name         =   $thawing->table_name;
        $gudang->table_id           =   $thawing->table_id;
        $gudang->qty_awal           =   $request->qty;
        $gudang->berat_awal         =   $request->berat;
        $gudang->qty                =   $request->qty;
        $gudang->berat              =   $request->berat;
        $gudang->berat_timbang      =   $thawing->berat_timbang;
        $gudang->berat              =   $request->berat;
        $gudang->palete             =   $thawing->palete;
        $gudang->parting            =   $thawing->parting;
        $gudang->production_date    =   Carbon::now();
        $gudang->type               =   $thawing->type;
        $gudang->packaging          =   $thawing->packaging;
        $gudang->plastik_group        =   Item::plastik_group($thawing->packaging);
        $gudang->selonjor           =   $thawing->selonjor;
        $gudang->customer_id        =   $thawing->customer_id;
        $gudang->stock_type         =   $thawing->stock_type;
        $gudang->jenis_trans        =   'keluar';
        $gudang->gudang_id          =   $thawing->gudang_id;
        $gudang->gudang_id_keluar   =   $thawing->id;
        $gudang->status             =   4;
        if (!$gudang->save()) {
            DB::rollBack() ;
            return back()->with('status', 2)->with('message', 'Proses gagal');
        }

        $chiller                    =   Chiller::where('asal_tujuan', 'thawing')->where('item_id', $itembaru->id)->where('tanggal_produksi', date('Y-m-d'))->first() ?? new Chiller();
        $chiller->table_name        =   'product_gudang';
        $chiller->table_id          =   $gudang->id;
        $chiller->asal_tujuan       =   'thawing';
        $chiller->item_id           =   $itembaru->id;
        $chiller->item_name         =   $itembaru->nama;
        $chiller->selonjor          =   $thawing->selonjor;
        $chiller->customer_id       =   $thawing->customer_id;
        $chiller->jenis             =   'masuk';
        $chiller->type              =   'hasil-produksi';
        $chiller->qty_item          =   $chiller->qty_item + $request->qty;
        $chiller->berat_item        =   $chiller->berat_item + $request->berat;
        $chiller->tanggal_produksi  =   Carbon::now();
        $chiller->stock_item        =   $chiller->stock_item + $request->qty;
        $chiller->stock_berat       =   $chiller->stock_berat + $request->berat;
        $chiller->status            =   2;

        if ($thawing->gudangabf->table_name == 'chiller') {
            $label  =   json_decode($thawing->gudangabf->abf_chiller->label) ;
            $array  =   json_encode([
                            'plastik'       =>  [
                                'sku'       =>  $label->plastik->sku,
                                'jenis'     =>  $label->plastik->jenis,
                                'qty'       =>  0
                            ],
                            'parting'       =>  [
                                'qty'       =>  $label->parting->qty
                            ],
                            'additional'    =>  [
                                'tunggir'   =>  $label->additional->tunggir,
                                'lemak'     =>  $label->additional->lemak,
                                'maras'     =>  $label->additional->maras,
                            ],
                            'sub_item'      =>  $thawing->sub_item
                        ]);
        }

        $chiller->label             =   $array ?? NULL ;
        if (!$chiller->save()) {
            DB::rollBack();
            return back()->with('status', 2)->with('message', 'Proses gagal');
        }

        $thawing->qty               =   $thawing->qty - $request->qty;
        $thawing->berat             =   $thawing->berat - $request->berat;
        if (!$thawing->save()) {
            DB::rollBack();
            return back()->with('status', 2)->with('message', 'Proses gagal');
        }

        DB::commit() ;

        try {
            Chiller::recalculate_chiller($chiller->id);
        } catch (\Throwable $th) {

        }

        return back()->with('status', 1)->with('message', 'Berhasil thawing');
    }

    public function editthawing(Request $request)
    {
        $chill      =   Chiller::find($request->x_code);

        $item       =   Item::find($request->item);

        $chill->item_id     =   $request->item;
        $chill->item_name   =   $item->nama;
        $chill->qty_item    =   $request->qty;
        $chill->berat_item  =   $request->berat;
        $chill->stock_item  =   $request->qty;
        $chill->stock_berat =   $request->berat;
        $chill->save();

        try {
            Chiller::recalculate_chiller($chill->id);
        } catch (\Throwable $th) {

        }

        return back()->with('status', 1)->with('message', 'Berhasil Update');

    }

    public function data_tracing($id)
    {
        $product_gudang = Product_gudang::
                                        withCount(['countOrderBB as TotalQtyBahanBaku' => function($query) {
                                            $query->select(DB::raw('sum(bb_item)'));
                                        }])
                                        ->withCount(['countOrderBB as TotalBeratBahanBaku' => function($query) {
                                            $query->select(DB::raw('sum(bb_berat)'));
                                        }])
                                        ->withCount(['countOrderThawing as total_qty_thawing' => function($query) {
                                            $query->select(DB::raw('sum(qty)'));
                                        }])
                                        ->withCount(['countOrderThawing as total_berat_thawing' => function($query) {
                                            $query->select(DB::raw('sum(berat)'));
                                        }])
                                        ->withCount(['countRegrading as total_qty_regrading' => function($query) {
                                            $query->select(DB::raw('sum(qty_awal)'))->where('type','grading_ulang');
                                        }])
                                        ->withCount(['countRegrading as total_berat_regrading' => function($query) {
                                            $query->select(DB::raw('sum(berat_awal)'))->where('type','grading_ulang');
                                        }])
                                        ->withCount(['countMusnahkan as total_qty_musnahkan' => function($query) {
                                            $query->select(DB::raw('sum(qty)'));
                                        }])
                                        ->withCount(['countMusnahkan as total_berat_musnahkan' => function($query) {
                                            $query->select(DB::raw('sum(berat)'));
                                        }])
                                        // ->withCount(['countInventoryAdjustment as total_qty_adjustment' => function($query) {
                                        //     $query->select(DB::raw('sum(qty_awal)'));
                                        // }])
                                        // ->withCount(['countInventoryAdjustment as total_berat_adjustment' => function($query) {
                                        //     $query->select(DB::raw('sum(berat_awal)'));
                                        // }])
                                        ->find($id);
        // dd($product_gudang);

        $data           = Product_gudang::with('gudangabf2')
                            ->where('gudang_id_keluar', $id)
                            ->where('type','inventory_adjustment')
                            ->where('table_name','abf')
                            ->get();

        $alokasi_order  = Product_gudang::with('gudangabf2')->where('gudang_id_keluar', $id)
                            ->where('type','siapkirim')
                            ->get();

        $request_thawing    = Product_gudang::with('gudangabf2')->where('gudang_id_keluar', $id)
                                ->where('type','thawing_request')
                                ->get();

        $musnahkan          = Product_gudang::with(['countMusnahkan','countMusnahkan.gudang','countMusnahkan.musnahkan'])->where('id', $id)->get();
// dd($musnahkan[0]->countMusnahkan[0]->musnahkan->status);
        return view('admin.pages.warehouse.data-tracing', compact('product_gudang','data','alokasi_order','request_thawing','musnahkan'));

    }

    public function inject(){

        $product_gudang = Product_gudang::where('product_gudang.gudang_id_keluar', NULL)
                            ->leftJoin('order_bahan_baku', 'order_bahan_baku.chiller_alokasi', '=', 'product_gudang.id')
                            ->where('product_gudang.type', 'siapkirim')
                            ->limit(500)
                            ->orderBy('product_gudang.id', 'desc')
                            ->get();

        $data = [];
        foreach($product_gudang as $no => $p):
            $data[$no]['gudang'] = $p;
            $order_bahan_baku = Bahanbaku::where('chiller_alokasi', $p->id)->first();
            $data[$no]['order'] = $order_bahan_baku;

            if($order_bahan_baku){
                $gudang_awal = Product_gudang::where('id', $order_bahan_baku->chiller_out)->first();
                $data[$no]['gudang_awal'] = $gudang_awal;

                if($gudang_awal){
                    $p->gudang_id_keluar = $gudang_awal->id;
                    $p->save();
                }
            }
        endforeach;

        return "sukses";

    }

    public function update_stock(Request $request)
    {
        // dd($request->all());
        
        // DB::beginTransaction() ;
        $data                   =   Product_gudang::where('id', $request->id)->first();
        
        $origin                 = clone $data;
        
        $data->sub_item         =   $request->sub_item;
        $data->qty              =   $request->qty ?? $data->qty;
        $data->berat            =   $request->berat ?? $data->berat;

        $dataItem               =   Item::find($request->namaItem);
        if ($dataItem) {
            $data->nama         =   $dataItem->nama ?? $request->namaItem;

        } 

        $data->qty_awal         =   $request->qtyAwal ?? $data->qty_awal;
        $data->berat_awal       =   $request->beratAwal ?? $data->berat_awal;
        $data->subpack          =   $request->subpack ?? NULL;
        $data->gudang_id        =   $request->lokasi ;
        $data->asal_abf         =   $request->abf ;
        $data->barang_titipan   =   $request->titipan ? 1 : NULL ;
        $data->karung_isi       =   $request->karung_isi ?? $data->karung_isi;
        $data->karung_qty       =   $request->karung_qty ?? $data->karung_qty;
        $data->packaging        =   $request->selectPackaging ?? $data->packaging;
        $data->karung           =   $request->karung ?? $data->karung;
        $data->plastik_group    =   $request->plastik ?? $data->plastik_group;
        $data->production_date  =   $request->tgl_produksi ?? $data->production_date;
        $data->customer_id      =   $request->customer ?? $data->customer_id;
        $data->parting          =   $request->parting ?? $data->parting;

        
        if ($request->jenis == 'warehouse_inout') {
            $updateya = Product_gudang::find($request->id);
            $hasil_qty              =   $request->qtyAwal - $updateya->qty_awal;
            $hasil_berat            =   $request->beratAwal - $updateya->berat_awal;
            // dd($hasil_qty);
            $data->qty              =  $updateya->qty + ($hasil_qty);
            $data->berat            =  $updateya->berat + ($hasil_berat);
        }

        if (!$data->save()) {
            DB::rollBack() ;
            $result['status']   =   400 ;
            $result['msg']      =   "Proses gagal" ;
            return $result ;
        }

        if ($data->save()) {
            $log             = new Adminedit();
            $log->user_id    = Auth::user()->id ;
            $log->table_id   = $data->id;
            $log->table_name = 'SOH';
            $log->type       = 'edit';
            $log->activity   = 'SOH';
            $log->content    = 'Edit Data SOH';
            $log->data                  =   json_encode([
                'before_update'     => $origin->toArray(),
                'after_update'      => $data->toArray()
                ]);
            if (!$log->save()) {
                return back()->with('status', 2)->with('message', 'Proses simpan log gagal');
            }
        }

        DB::commit() ;
        $result['status']   =   200 ;
        $result['msg']      =   "Berhasil Update" ;
        $result['request']      =   $request->all() ;
        return $result ;
    }

    public function wh_soh(Request $request){

        $tanggal    = $request->tanggal;
        $data       = DB::Select('SELECT
            product_gudang.product_id,
            product_gudang.nama,
            product_gudang.plastik_group,
            product_gudang.subpack,
            product_gudang.customer_id,
            customers.nama AS nama_konsumen,
            data_wh_in_out.*,
            (
                SUM(
                IF
                    ( product_gudang.production_date != CURRENT_DATE (), IF ( product_gudang.STATUS = 2, product_gudang.berat_awal, 0 ), 0 )) - SUM(
                IF
                ( product_gudang.production_date != CURRENT_DATE (), IF ( product_gudang.STATUS = 4, product_gudang.berat, 0 ), 0 ))) AS berat_saldo_awal,
            (
                SUM(
                IF
                    ( product_gudang.production_date != CURRENT_DATE (), IF ( product_gudang.STATUS = 2, product_gudang.qty_awal, 0 ), 0 )) - SUM(
                IF
                ( product_gudang.production_date != CURRENT_DATE (), IF ( product_gudang.STATUS = 4, product_gudang.qty, 0 ), 0 ))) AS qty_saldo_awal,
            (
                SUM(
                IF
                    ( product_gudang.production_date != CURRENT_DATE (), IF ( product_gudang.STATUS = 2, product_gudang.karung_qty, 0 ), 0 )) - SUM(
                IF
                ( product_gudang.production_date != CURRENT_DATE (), IF ( product_gudang.STATUS = 4, product_gudang.karung_qty, 0 ), 0 ))) AS karung_saldo_awal,
            (
                SUM(
                IF
                    ( product_gudang.production_date <= CURRENT_DATE (), IF ( product_gudang.STATUS = 2, product_gudang.berat_awal, 0 ), 0 )) - SUM(
                IF
                ( product_gudang.production_date <= CURRENT_DATE (), IF ( product_gudang.STATUS = 4, product_gudang.berat, 0 ), 0 ))) AS berat_saldo_akhir,
            (
                SUM(
                IF
                    ( product_gudang.production_date <= CURRENT_DATE (), IF ( product_gudang.STATUS = 2, product_gudang.qty_awal, 0 ), 0 )) - SUM(
                IF
                ( product_gudang.production_date <= CURRENT_DATE (), IF ( product_gudang.STATUS = 4, product_gudang.qty, 0 ), 0 ))) AS qty_saldo_akhir,
            (
                SUM(
                IF
                    ( product_gudang.production_date <= CURRENT_DATE (), IF ( product_gudang.STATUS = 2, product_gudang.karung_qty, 0 ), 0 )) - SUM(
                IF
                ( product_gudang.production_date <= CURRENT_DATE (), IF ( product_gudang.STATUS = 4, product_gudang.karung_qty, 0 ), 0 ))) AS karung_saldo_akhir
        FROM
            product_gudang
            LEFT JOIN abf ON abf.id = product_gudang.table_id
            LEFT JOIN customers ON customers.id = product_gudang.customer_id
            LEFT JOIN (
            SELECT
                product_id AS in_out_product_id,
                nama AS in_out_nama,
                subpack AS in_out_subpack,
                plastik_group AS in_out_plastik_group,
                customer_id AS in_out_customer_id,
                whin_qty,
                whin_berat,
                whin_keranjang,
                whout_qty,
                whout_berat,
                whout_keranjang
            FROM
                product_gudang
                LEFT JOIN (
                SELECT
                    product_id AS whin_product_id,
                    nama AS whin_nama,
                    subpack AS whin_subpack,
                    plastik_group AS whin_plastik_group,
                    customer_id AS whin_customer_id,
                    sum( qty_awal ) AS whin_qty,
                    sum( berat_awal ) AS whin_berat,
                    sum( karung_qty ) AS whin_keranjang
                FROM
                    product_gudang
                WHERE
                    product_gudang.STATUS = 2
                    AND product_gudang.production_date = "'.$tanggal.'"
                GROUP BY
                    product_gudang.product_id,
                    product_gudang.plastik_group,
                    product_gudang.subpack,
                    product_gudang.customer_id
                ) data_wh_in ON data_wh_in.whin_product_id = product_gudang.product_id
                AND data_wh_in.whin_plastik_group = product_gudang.plastik_group
                AND data_wh_in.whin_subpack = product_gudang.subpack
                AND data_wh_in.whin_customer_id = product_gudang.customer_id
                LEFT JOIN (
                SELECT
                    product_id AS whout_product_id,
                    nama AS whout_nama,
                    subpack AS whout_subpack,
                    plastik_group AS whout_plastik_group,
                    customer_id AS whout_customer_id,
                    sum( qty_awal ) AS whout_qty,
                    sum( berat_awal ) AS whout_berat,
                    sum( karung_qty ) AS whout_keranjang
                FROM
                    product_gudang
                WHERE
                    product_gudang.STATUS = 4
                    AND product_gudang.production_date = "'.$tanggal.'"
                GROUP BY
                    product_id,
                    plastik_group,
                    subpack,
                    customer_id
                ) data_wh_out ON data_wh_out.whout_product_id = product_gudang.product_id
                AND data_wh_out.whout_plastik_group = product_gudang.plastik_group
                AND data_wh_out.whout_subpack = product_gudang.subpack
                AND data_wh_out.whout_customer_id = product_gudang.customer_id
            ) data_wh_in_out ON data_wh_in_out.in_out_product_id = product_gudang.product_id
            AND data_wh_in_out.in_out_plastik_group = product_gudang.plastik_group
            AND data_wh_in_out.in_out_subpack = product_gudang.subpack
            AND data_wh_in_out.in_out_customer_id = product_gudang.customer_id
        WHERE
            date( product_gudang.production_date ) <= "'.$tanggal.'" AND production_date >= "2022-02-04"
            AND ( date( product_gudang.production_date ) > "2022-02-04" )
            AND product_gudang.deleted_at IS NULL
        GROUP BY
            product_gudang.product_id,
            product_gudang.plastik_group,
            product_gudang.subpack,
            product_gudang.customer_id
        ORDER BY
            product_gudang.nama ASC ');

        return view('admin.pages.warehouse.soh.view', compact(['data']));


    }

    public static function jsonToDebug($jsonText = '')
    {
        $arr = json_decode($jsonText, true);
        $html = "";
        if ($arr && is_array($arr)) {
            $html .= self::_arrayToHtmlTableRecursive($arr);
        }
        return $html;
    }

    private static function _arrayToHtmlTableRecursive($arr) {
        $str = "<table><tbody>";
        foreach ($arr as $key => $val) {
            $str .= "<tr>";
            $str .= "<td>$key</td>";
            $str .= "<td>";
            if (is_array($val)) {
                if (!empty($val)) {
                    $str .= self::_arrayToHtmlTableRecursive($val);
                }
            } else {
                $str .= "<strong>$val</strong>";
            }
            $str .= "</td></tr>";
        }
        $str .= "</tbody></table>";

        return $str;
    }


    public function inject_plastik_group(){
        $gudang = Product_gudang::where('plastik_group', NULL)->limit(1000)->get();

        foreach($gudang as $g){
            $g->plastik_group = Item::plastik_group($g->packaging);
            $g->save();
        }

        echo count($gudang);

    }

    public function soh_detail(Request $request){

        $item_name          = $request->item;
        $product_id         = Item::where('nama', $item_name)->orderBy('id', 'desc')->first()->id ?? 0;
        $packagingReq       = $request->plastik_group ?? NULL;
        $partingReq         = $request->parting ?? 0;
        $sub_itemReq        = $request->sub_item ?? NULL;
        $sub_packReq        = $request->sub_pack ?? NULL;
        $customerReq        = $request->customer ?? NULL;
        $tanggal            = $request->tanggal;
        $gudangReq          = $request->gudang;
        $grade_itemReq      = $request->grade_item;

        // return $request->all();

        // dd($partingReq);
        $datas          =   Product_gudang::select('product_gudang.*')
                            ->leftJoin('abf', 'abf.id', '=', 'product_gudang.table_id')
                            ->leftJoin('customers', 'customers.id', '=', 'product_gudang.customer_id')
                            ->where(function($query) use ($request, $tanggal) {
                                if ($request->key == 'searchDetailSOH') {
                                    if ($request->tanggal_soh_awal && $request->tanggal_soh_awal) {
                                        $query->whereBetween('product_gudang.production_date', [$request->tanggal_soh_awal, $request->tanggal_soh_akhir]);
                                    } else {
                                        if (env('NET_SUBSIDIARY', 'CGL')=='CGL') {
                                            $query->whereBetween('product_gudang.production_date', ['2023-05-27', $tanggal]);
                                        } else {
                                            $query->whereBetween('product_gudang.production_date', ['2023-05-05', $tanggal]);
                                        }
                                    }
                                    if ($request->status_detail_soh == 'masuk') {
                                        $query->where('product_gudang.status', 2);
                                    } else if ($request->status_detail_soh == 'keluar') {
                                        $query->where('product_gudang.status', 4);
                                    }
                                } else {
                                    if (env('NET_SUBSIDIARY', 'CGL')=='CGL') {
                                        $query->whereBetween('product_gudang.production_date', ['2023-05-27', $tanggal]);
                                    } else {
                                        $query->whereBetween('product_gudang.production_date', ['2023-05-05', $tanggal]);
                                    }
                                }
                            })
                            // ->whereDate('product_gudang.parting', "=", $tanggal)
                            ->where('product_gudang.product_id', $product_id)
                            ->where(function($query) use ($partingReq) {
                                if ($partingReq == 0 || $partingReq == NULL) {
                                    $query->where('product_gudang.parting', 0)->orWhere('product_gudang.parting', NULL);
                                } else {
                                    $query->where('product_gudang.parting', $partingReq);
                                }
                            })
                            ->where('product_gudang.plastik_group', $packagingReq)
                            ->where('product_gudang.sub_item', $sub_itemReq)
                            ->where('product_gudang.gudang_id', $gudangReq)
                            ->where('product_gudang.grade_item', $grade_itemReq)
                            ->where('product_gudang.customer_id', $customerReq);

        $history        = (clone $datas)->onlyTrashed()->get();
        $gudang         = Gudang::where('subsidiary', env('NET_SUBSIDIARY'))->get();
        $data           = (clone $datas)->get();
        $sub_item       = Adminedit::where('type','item_name')->get();
        $customer       = Customer::select('id','nama','kode')->get();
        $plastikGroup   = Adminedit::where('type','plastik_group')->get();

        $plastik        = Item::where('category_id',25)->where('subsidiary', env('NET_SUBSIDIARY', 'EBA'))->where('status', '1')->get();
        $karung         = Item::where('subsidiary', 'like', '%'.Session::get('subsidiary').'%')
                        ->where(function ($item) {
                            $item->where('nama', 'like', 'KARUNG%')->orWhere('nama', 'like', 'KARTON%');
                            $item->orWhere('nama', 'like', 'KARUNG%')->orWhere('nama', 'like', 'KARUNG%');
                        })
                        ->get();
        $sub_item_name   =    Adminedit::where('type','item_name')->get();

                        
        if ($request->key == 'searchDetailSOH') {
            // dd($request->all());
            return view('admin.pages.warehouse.soh.data-detail-soh', compact(['data', 'gudang', 'tanggal', 'sub_item', 'plastik', 'karung','customer','plastikGroup', 'item_name', 'gudangReq', 'sub_itemReq', 'customerReq', 'packagingReq', 'partingReq', 'grade_itemReq','sub_item_name']));

        }
        return view('admin.pages.warehouse.soh.detail-soh', compact(['data','history','gudang', 'tanggal', 'sub_item', 'plastik', 'karung','customer','plastikGroup', 'item_name', 'gudangReq', 'sub_itemReq', 'customerReq', 'packagingReq', 'partingReq', 'grade_itemReq','sub_item_name']));
    }

    public function soh_edit($id)
    {
        $edit       = Product_gudang::findOrFail($id);
        $item       = Item::where('id',$edit->product_id)->first();
        $item_list  = Item::select('id','nama')->where('category_id',$item->category_id)->get();
        $sub_item   = Adminedit::where('type','item_name')->get();
        $plastik    = Item::where('category_id',25)->where('subsidiary', env('NET_SUBSIDIARY', 'EBA'))->where('status', '1')->get();
        $karung     = Item::where('subsidiary', 'like', '%'.Session::get('subsidiary').'%')
                                            ->where(function ($item) {
                                                $item->where('nama', 'like', 'KARUNG%')->orWhere('nama', 'like', 'KARTON%');
                                                $item->orWhere('nama', 'like', 'KARUNG%')->orWhere('nama', 'like', 'KARUNG%');
                                            })
                                            ->get();
        return view('admin.pages.warehouse.soh.edit-soh',compact('edit','item_list','sub_item','plastik','karung'));
    }

    public function soh_update(Request $request,$id)
    {
        $gudang_lama = Product_gudang::find($id);
        // dd($id);
        foreach ($request->item as $no => $row) {
            $data = array (
                'product_id'        => $request->item[$no],
                'nama'              => Item::where('id',$request->item[$no])->first()->nama,
                'kategori'          => null,
                'table_name'        => 'product_gudang',
                'table_id'          => $id,
                'sub_item'          => $request->subitem[$no] ?? $gudang_lama->sub_item,
                'no_so'             => $gudang_lama->no_so,
                'order_id'          => $gudang_lama->order_id,
                'order_item_id'     => $gudang_lama->order_item_id,
                'qty_awal'          => $request->qty_awal[$no],
                'berat_awal'        => $request->berat_awal[$no],
                'berat'             => $request->berat[$no],
                'qty'               => $request->qty[$no],
                // 'packaging'         => Item::where('id',$request->packaging[$no])->first()->nama,
                'packaging'         => $request->packaging[$no] ?? $gudang_lama->packaging,
                'plastik_group'     => $request->plastik[$no],
                'parting'           => $request->parting[$no] ?? $gudang_lama->parting,
                'karung'            => $request->karung[$no],
                'karung_qty'        => $request->karung_qty[$no],
                'karung_isi'        => $request->karung_isi[$no],
                'customer_id'       => $gudang_lama->customer_id,
                'tanggal_kemasan'   => $request->tanggal_kemasan[$no] ?? $gudang_lama->tanggal_kemasan,
                'production_date'   => $gudang_lama->production_date ?? $request->tanggal_kemasan[$no],
                'expired'           => $request->expired[$no] ?? $request->expired_custom[$no] ?? $gudang_lama->expired,
                'asal_abf'          => $gudang_lama->asal_abf,
                'gudang_id'         => $gudang_lama->gudang_id,
                'gudang_id_keluar'  => $gudang_lama->gudang_id_keluar,
                'type'              => $gudang_lama->type,
                'stock_type'        => $request->stock[$no] ?? $gudang_lama->stock_type,
                'jenis_trans'       => $gudang_lama->jenis_trans,
                'status'            => $gudang_lama->status,
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            );
            $gudang_baru = Product_gudang::insert($data);

        }
        $gudang_lama->delete();
        $url = $request->url;
        return redirect()->to(url($url))->with('status', 1)->with('message', 'Update Transaksi berhasil');
    }


    public function downloadThawing(Request $request)
    {
        if($request->key == "unduh"){
            $mulai  = $request->mulai ?? date('Y-m-d');
            $sampai = $request->sampai ?? date('Y-m-d');

            $data = Thawing::whereBetween('tanggal_request', [$mulai, $sampai])
            ->orderBy('id', 'DESC')
            ->withTrashed()
            ->with(['thawing_list' => function ($query) {
                $query->select('thawing_id', DB::raw('SUM(qty) as sum_qty, SUM(berat) as sum_berat'))
                    ->groupBy('thawing_id');
            }])
            ->get();

            if (!empty($mulai) && !empty($sampai)) {
                $data->whereBetween('tanggal', [$mulai, $sampai]);
                $filename = "download-bumbu ".$mulai."-".$sampai;
            }

            // dd($data);
            // $data = $query->get();
            $filename = "download-thawing ".Carbon::now()->format('Y-m-d');

            return view('admin.pages.warehouse.requestthawing-download',compact('data','filename'));
        }
        
    }

    public function productAdjustment(Request $request, $id)
    {
        DB::beginTransaction();
        $data       = Product_gudang::find($id);

        $sisaQty     = $request->ubahQty - $data->qty;
        $sisaBerat   = $request->ubahBerat - $data->berat;

        $data->qty      = $request->ubahQty;
        $data->berat    = $request->ubahBerat;
        $data->save();

        $newData   = new Product_gudang();
        $newData->product_id    = $data->product_id;
        $newData->nama          = $data->nama;
        $newData->sub_item      = $data->sub_item;
        $newData->table_name    = $data->table_name;
        $newData->table_id      = $data->table_id;
        $newData->qty_awal      = $data->qty_awal;
        $newData->berat_awal    = $data->berat_awal;
        $newData->qty           = $sisaQty;
        $newData->berat         = $sisaBerat;
        $newData->packaging     = $data->packaging;
        $newData->plastik_group = $data->plastik_group;
        $newData->parting       = $data->parting;
        $newData->karung        = $data->karung;
        $newData->customer_id   = $data->customer_id;
        $newData->production_date   = $data->production_date;
        $newData->type              = 'inventory_adjustment';
        $newData->gudang_id         = $data->gudang_id;
        $newData->asal_abf          = $data->asal_abf;
        $newData->gudang_id_keluar  = $data->id;

        if($sisaQty < 0 || $sisaBerat < 0)
        {
            $newData->jenis_trans       = 'keluar';
            $newData->stock_type        = 'negatif';
            $newData->status            = 4;
        } else {
            $newData->jenis_trans       = 'masuk';
            $newData->stock_type        = 'positif';
            $newData->status            = 2;
        }

        if(!$newData->save()){
            DB::rollBack();
        }

        DB::commit();

        return back()->with('status', 1)->with('message', 'Data Diupdate');
    }
}
