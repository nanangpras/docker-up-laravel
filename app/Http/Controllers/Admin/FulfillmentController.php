<?php

namespace App\Http\Controllers\Admin;

use App\Classes\Applib;
use App\Http\Controllers\Controller;
use App\Models\Abf;
use App\Models\Bahanbaku;
use App\Models\Bom;
use App\Models\Category;
use App\Models\Chiller;
use App\Models\Customer;
use App\Models\Freestock;
use App\Models\Gudang;
use App\Models\Item;
use App\Models\Netsuite;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product_gudang;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FulfillmentController extends Controller
{
    //
    public const PAGINASI 		= 5;

    public function index(Request $request){
         if(env('NET_SUBSIDIARY', 'CGL')=='CGL'){
            $tanggal    =   $request->tanggal ?? date('Y-m-d');
            $tanggal_end    =   $request->tanggal_end ?? date('Y-m-d');
         }else{
            $tanggal    =   $request->tanggal ?? date('Y-m-d', strtotime('tomorrow'));
            $tanggal_end    =   $request->tanggal_end ?? date('Y-m-d', strtotime('tomorrow'));
        }

        $search     =   $request->search ?? "";
        $customer   =   $request->customer ?? "";
        $search     =   $request->search ?? "";
        $key        =   $request->key ?? "";
        $jenis      =   $request->jenis ?? "";
        $kategori   =   $request->kategori ?? "";

        $divisi         =   $request->divisi ?? "";

        if(User::setIjin(26)){
            $divisi = "sampingan";
        }
        if(User::setIjin(27)){
            $divisi = "siap-kirim";
        }
        if(User::setIjin(31)){
            $divisi = "siap-kirim-meyer";
        }
        if(User::setIjin(15)){
            $divisi = "frozen";
        }
        if(User::setIjin(15) && User::setIjin(26)){
            $divisi = $request->divisi;
        }
        if(User::setIjin('superadmin')){
            $divisi = $request->divisi;
        }

        $today = Carbon::today();
        $nextday=[];
        for ($i=0; $i < 7; $i++) { 
            $nextday[]=$today->format('Y-m-d');
            $today->addDay();
        }

        $cat        = Customer::select('kategori')->where('kategori', '!=', null)->distinct()->get();
        if (User::setIjin(27) || User::setIjin(31) || User::setIjin(15) || User::setIjin(26)){

            if ($request->key == 'unduh') {
                return view('admin.pages.penyiapan.export');
            } else {
                return view('admin.pages.fulfillment.index', compact('tanggal', 'customer', 'search', 'key', 'divisi', 'jenis', 'tanggal_end', 'kategori','nextday','cat'));
            }

        }else{
            return redirect()->route("index");
        }
    }

    /**********************************************************************/
    /*                                                                    */
    /*           OPTIMALISASI WITH ORM SINGLE COMPONENT VERSI 1           */
    /*                                                                    */
    /**********************************************************************/

    public function orderListV1(Request $request)
    {

        $divisi         =   $request->divisi ?? "";
        $urutan         =   $request->urutan ?? "ASC";

        if($urutan!="ASC" && $urutan!="DESC"){
            $urutan = "ASC";
        }

        if(User::setIjin(26)){
            $divisi = "sampingan";
        }
        if(User::setIjin(27)){
            $divisi = "siap-kirim";
        }
        if(User::setIjin(31)){
            $divisi = "siap-kirim-meyer";
        }
        if(User::setIjin(15)){
            $divisi = "frozen";
        }
        if(User::setIjin(15) && User::setIjin(26)){
            $divisi = $request->divisi;
        }
        if(User::setIjin('superadmin')){
            $divisi = $request->divisi;
        }

        $customer       =   $request->customer ?? "";

        if($divisi=="siap-kirim-meyer"){
            $search         =   "Meyer";
        }else{
            $search         =   $request->search ?? "";
        }
        $key            =   $request->key ?? "";
        $jenis          =   $request->jenis ?? "semua";
        $kategori       =   $request->kategori ?? "semua";

        if(env('NET_SUBSIDIARY', 'CGL')=='CGL'){
            $tanggal        =   $request->tanggal ?? date('Y-m-d');
            $tanggal_end        =   $request->tanggal_end ?? date('Y-m-d');
        }else{
            $tanggal        =   $request->tanggal ?? date('Y-m-d', strtotime('tomorrow'));
            $tanggal_end        =   $request->tanggal_end ?? date('Y-m-d', strtotime('tomorrow'));
        }

        $tanggalKirim   =   $request->tanggalkirimfulfillment ?? 0;
        $pending        =   Order::with(['daftar_order_full', 'daftar_order_full.item', 'daftar_order_full.bahan_baku', 'daftar_order_full.getNetsuite', 'cekDataOrderBahanBaku', 'getBahanBaku.relasi_netsuite', 'getBahanBaku.bahanbborder'])
                            ->where(function ($query) use ($tanggal, $tanggal_end, $tanggalKirim) {
                                if ($tanggalKirim == 0) {
                                    $query->whereBetween('tanggal_so', [$tanggal, $tanggal_end]);
                                } else {
                                    $query->whereBetween('tanggal_kirim', [$tanggal, $tanggal_end]);
                                }
                            })
                            ->where(function($query) use ($customer, $search, $key){
                                if ($customer) {
                                    $query->where('customer_id', $customer);
                                }
                                if ($search) {
                                    $query->orWhere('nama', 'like', '%'.$search.'%');
                                    $query->orWhere('no_so', 'like', '%'.$search.'%');
                                    $query->orWhere('no_do', 'like', '%'.$search.'%');
                                    $query->orWhere('keterangan', 'like', '%'.$search.'%');
                                    $query->orWhere('sales_channel', 'like', '%'.$search.'%');
                                }
                                
                            })
                            ->where(function ($query3) use ($key){
                                    if ($key) {
                                    if ($key == 'selesai') {
                                        $query3->where('status', 10);
                                    } else
                                    if ($key == 'proses' || $key == 'partial') {
                                        $query3->where('status_so', 'Pending Fulfillment');
                                        $query3->where('status', null);
                                    } else
                                    if ($key == 'batal') {
                                        $query3->where('status_so', "Closed");
                                    } else
                                    if ($key == 'gagal') {
                                        $query3->where('status', 10)->where('no_do', null);
                                    }

                                    if($key=="partial"){
                                        $query3->whereIn('id', OrderItem::select('order_id')->whereNotNull('fulfillment_berat'));
                                    }
                                }
                            })
                            ->where(function ($query2) use ($divisi, $kategori){
                                if($divisi!="sampingan"){
                                    $query2->whereNotIn('sales_channel', ["By Product - Paket", "By Product - Retail"]);
                                }

                                if($divisi=="sampingan"){
                                    $query2->whereIn('sales_channel', ["By Product - Paket", "By Product - Retail"]);
                                }

                                if($kategori!="semua"){
                                    $query2->whereIn('sales_channel', [$kategori]);
                                }
                            })
                            ->where(function ($query3) use ($jenis){

                                if($jenis=="frozen"){
                                    $kategori       =   Category::where('nama', 'LIKE', "%FROZEN%")->pluck('id');
                                    $query3->whereIn('id', OrderItem::select('order_id')->whereIn('item_id', Item::select('id')->whereIn('category_id', $kategori)));
                                }

                                if($jenis=="fresh"){
                                    $kategori       =   Category::where('nama', 'NOT LIKE', "%FROZEN%")->pluck('id');
                                    $query3->whereIn('id', OrderItem::select('order_id')->whereIn('item_id', Item::select('id')->whereIn('category_id', $kategori)));
                                }

                            });

        if($urutan=="ASC"){
            $pending = $pending->orderBy('id', "ASC");
            $pending = $pending->paginate(self::PAGINASI);
        }else{

            $pending = $pending->orderBy('id', "DESC");
            $pending = $pending->paginate(self::PAGINASI);
        }

        $semua_order    =   Order::where(function ($query) use ($tanggal, $tanggal_end, $tanggalKirim) {
                                if ($tanggalKirim == 0) {
                                    $query->whereBetween('tanggal_so', [$tanggal, $tanggal_end]);
                                } else {
                                    $query->whereBetween('tanggal_kirim', [$tanggal, $tanggal_end]);
                                }
                            })
                            ->where(function ($query2) use ($divisi){
                                if($divisi!="sampingan"){
                                    $query2->whereNotIn('sales_channel', ["By Product - Paket", "By Product - Retail"]);
                                }
                                if($divisi=="sampingan"){
                                    $query2->whereIn('sales_channel', ["By Product - Paket", "By Product - Retail"]);
                                }
                            })
                            ->orderBy('id', 'desc')
                            ->count();

        $selesai_order  =   Order::where(function ($query) use ($tanggal, $tanggal_end, $tanggalKirim) {
                                if ($tanggalKirim == 0) {
                                    $query->whereBetween('tanggal_so', [$tanggal, $tanggal_end]);
                                } else {
                                    $query->whereBetween('tanggal_kirim', [$tanggal, $tanggal_end]);
                                }
                            })
                            ->where(function ($query2) use ($divisi){
                                if($divisi!="sampingan"){
                                    $query2->whereNotIn('sales_channel', ["By Product - Paket", "By Product - Retail"]);
                                }
                                if($divisi=="sampingan"){
                                    $query2->whereIn('sales_channel', ["By Product - Paket", "By Product - Retail"]);
                                }
                            })
                            ->orderBy('id', 'desc')
                            ->where('status', '10')
                            ->count();

        $pending_order  =   Order::where(function ($query) use ($tanggal, $tanggal_end, $tanggalKirim) {
                                if ($tanggalKirim == 0) {
                                    $query->whereBetween('tanggal_so', [$tanggal, $tanggal_end]);
                                } else {
                                    $query->whereBetween('tanggal_kirim', [$tanggal, $tanggal_end]);
                                }
                            })
                            ->where(function ($query2) use ($divisi){
                                if($divisi!="sampingan"){
                                    $query2->whereNotIn('sales_channel', ["By Product - Paket", "By Product - Retail"]);
                                }
                                if($divisi=="sampingan"){
                                    $query2->whereIn('sales_channel', ["By Product - Paket", "By Product - Retail"]);
                                }
                            })
                            ->orderBy('id', 'desc')
                            ->whereNull('status')
                            ->where('status_so', 'Pending Fulfillment')
                            ->count();
        $batal_order  =   Order::where(function ($query) use ($tanggal, $tanggal_end, $tanggalKirim) {
                                if ($tanggalKirim == 0) {
                                    $query->whereBetween('tanggal_so', [$tanggal, $tanggal_end]);
                                } else {
                                    $query->whereBetween('tanggal_kirim', [$tanggal, $tanggal_end]);
                                }
                            })
                            ->where(function ($query2) use ($divisi){
                                if($divisi!="sampingan"){
                                    $query2->whereNotIn('sales_channel', ["By Product - Paket", "By Product - Retail"]);
                                }
                                if($divisi=="sampingan"){
                                    $query2->whereIn('sales_channel', ["By Product - Paket", "By Product - Retail"]);
                                }
                            })
                            ->orderBy('id', 'desc')
                            ->where('status_so', 'Closed')
                            ->count();

        $status_order   =   [
            'semua_order'       =>  $semua_order ,
            'selesai_order'     =>  $selesai_order ,
            'pending_order'     =>  $pending_order ,
            'batal_order'       =>  $batal_order ,
        ] ;

        return view('admin.pages.fulfillment.order.order', compact('pending', 'tanggal', 'customer', 'search', 'status_order', 'key', 'divisi', 'jenis', 'kategori'));
    }


    public function orderItemV1(Request $request){
        $id     = $request->id;
        $jenis  = $request->jenis ?? "semua";
        $divisi  = $request->divisi ?? "semua";
        $order  = Order::find($id);

        if($jenis=="frozen"){
            $kategori       =   Category::where('nama', 'LIKE', "%FROZEN%")->pluck('id');
        }

        if($jenis=="fresh"){
            $kategori       =   Category::where('nama', 'NOT LIKE', "%FROZEN%")->pluck('id');
        }

        if($jenis=="semua"){
            $kategori       =   Category::pluck('id');
        }

        if($id && $order){
            $order_item = OrderItem::where('order_id', $id)->get();
            return view('admin.pages.fulfillment.order.order-item', compact('order', 'order_item', 'jenis', 'kategori', 'divisi'));
        }else{
            return redirect(route('fulfillment.index'));
        }

    }

    public function pemenuhanAlokasiV1(Request $request){

        $order_item_id = $request->order_item_id;
        if ($request->key == 'info') {
            $order  =   OrderItem::find($order_item_id);
            $qty    =   Bahanbaku::where('order_item_id', $order_item_id)->sum('bb_item');
            $berat  =   Bahanbaku::where('order_item_id', $order_item_id)->sum('bb_berat');
            return view('admin.pages.fulfillment.order.order-item-info', compact('order', 'qty', 'berat'));
        } else {
            $pemenuhan = Bahanbaku::where('order_item_id', $order_item_id)->get();
            return view('admin.pages.fulfillment.order.order-bahan-baku-list', compact('pemenuhan'));
        }
    }

    /**********************************************************************/
    /*                                                                    */
    /*       END OF OPTIMALISASI WITH ORM SINGLE COMPONENT VERSI 1        */
    /*                                                                    */
    /**********************************************************************/


    /**********************************************************************/
    /*                                                                    */
    /*           OPTIMALISASI WITH ORM MULTIPLE COMPONENT VERSI 2         */
    /*                                                                    */
    /**********************************************************************/

    public function orderList(Request $request)
    {

        $divisi                             =   $request->divisi ?? "";
        $urutan                             =   $request->urutan ?? "ASC";

        if($urutan!="ASC" && $urutan!="DESC"){
            $urutan                         =   "ASC";
        }

        if(User::setIjin(26)){
            $divisi                         =   "sampingan";
        }
        if(User::setIjin(27)){
            $divisi                         =   "siap-kirim";
        }
        if(User::setIjin(31)){
            $divisi                         =   "siap-kirim-meyer";
        }
        if(User::setIjin(15)){
            $divisi                         =   "frozen";
        }
        if(User::setIjin(15) && User::setIjin(26)){
            $divisi                         =   $request->divisi;
        }
        if(User::setIjin('superadmin')){
            $divisi                         =   $request->divisi;
        }

        $customer                           =   $request->customer ?? "";

        if($divisi =="siap-kirim-meyer"){
            $search                         =   "Meyer";
        }else{
            $search                         =   $request->search ?? "";
        }

        $key                                =   $request->key ?? "";
        $jenis                              =   $request->jenis ?? "semua";
        $kategori                           =   $request->kategori ?? "semua";

        if(env('NET_SUBSIDIARY', 'CGL')=='CGL'){
            $tanggal                        =   $request->tanggal ?? date('Y-m-d');
            $tanggal_end                    =   $request->tanggal_end ?? date('Y-m-d');
        }else{
            $tanggal                        =   $request->tanggal ?? date('Y-m-d', strtotime('tomorrow'));
            $tanggal_end                    =   $request->tanggal_end ?? date('Y-m-d', strtotime('tomorrow'));
        }

        $tanggalKirim                       =   $request->tanggalkirimfulfillment ?? 0;

        if($jenis=="frozen"){
            $inkategori     =   Category::where('nama', 'LIKE', "%FROZEN%")->pluck('id');
        }

        if($jenis=="fresh"){
            $inkategori     =   Category::where('nama', 'NOT LIKE', "%FROZEN%")->pluck('id');
        }

        if($jenis=="semua"){
            $inkategori     =   Category::pluck('id');
        }

        $pending                            =   Order::with(['daftar_order_full', 'daftar_order_full.item','daftar_order_full.bahan_baku'])
                                                ->where(function ($query) use ($tanggal, $tanggal_end, $tanggalKirim) {
                                                    if ($tanggalKirim == 0) {
                                                        $query->whereBetween('tanggal_so', [$tanggal, $tanggal_end]);
                                                    } else {
                                                        $query->whereBetween('tanggal_kirim', [$tanggal, $tanggal_end]);
                                                    }
                                                })
                                                ->where(function($query) use ($customer, $search, $key){
                                                    if ($customer) {
                                                        $query->where('customer_id', $customer);
                                                    }
                                                    if ($search) {
                                                        $query->orWhere('nama', 'like', '%'.$search.'%');
                                                        $query->orWhere('no_so', 'like', '%'.$search.'%');
                                                        $query->orWhere('no_do', 'like', '%'.$search.'%');
                                                        $query->orWhere('keterangan', 'like', '%'.$search.'%');
                                                        $query->orWhere('sales_channel', 'like', '%'.$search.'%');
                                                    }
                                                    
                                                })
                                                ->where(function ($query3) use ($key){
                                                        if ($key) {
                                                        if ($key == 'selesai') {
                                                            $query3->where('status', 10);
                                                        } else
                                                        if ($key == 'proses' || $key == 'partial') {
                                                            $query3->where('status_so', 'Pending Fulfillment');
                                                            $query3->where('status', null);
                                                        } else
                                                        if ($key == 'batal') {
                                                            $query3->where('status_so', "Closed");
                                                        } else
                                                        if ($key == 'gagal') {
                                                            $query3->where('status', 10)->where('no_do', null);
                                                        }

                                                        if($key=="partial"){
                                                            $query3->whereIn('id', OrderItem::select('order_id')->whereNotNull('fulfillment_berat'));
                                                        }
                                                    }
                                                })
                                                ->where(function ($query2) use ($divisi, $kategori){
                                                    if($divisi!="sampingan"){
                                                        $query2->whereNotIn('sales_channel', ["By Product - Paket", "By Product - Retail"]);
                                                    }

                                                    if($divisi=="sampingan"){
                                                        $query2->whereIn('sales_channel', ["By Product - Paket", "By Product - Retail"]);
                                                    }

                                                    if($kategori!="semua"){
                                                        $query2->whereIn('sales_channel', [$kategori]);
                                                    }
                                                })
                                                ->where(function ($query3) use ($jenis){

                                                    if($jenis=="frozen"){
                                                        $kategori       =   Category::where('nama', 'LIKE', "%FROZEN%")->pluck('id');
                                                        $query3->whereIn('id', OrderItem::select('order_id')->whereIn('item_id', Item::select('id')->whereIn('category_id', $kategori)));
                                                    }

                                                    if($jenis=="fresh"){
                                                        $kategori       =   Category::where('nama', 'NOT LIKE', "%FROZEN%")->pluck('id');
                                                        $query3->whereIn('id', OrderItem::select('order_id')->whereIn('item_id', Item::select('id')->whereIn('category_id', $kategori)));
                                                    }

                                                });
        $pending                            =   $urutan == "ASC" ? $pending->orderBy('id', "ASC") : $pending->orderBy('id', "DESC");
        $pending                            =   $pending->paginate(self::PAGINASI);

        $semua_order                        =   Order::where(function ($query) use ($tanggal, $tanggal_end, $tanggalKirim) {
                                                    if ($tanggalKirim == 0) {
                                                        $query->whereBetween('tanggal_so', [$tanggal, $tanggal_end]);
                                                    } else {
                                                        $query->whereBetween('tanggal_kirim', [$tanggal, $tanggal_end]);
                                                    }
                                                })
                                                ->where(function ($query2) use ($divisi){
                                                    if($divisi!="sampingan"){
                                                        $query2->whereNotIn('sales_channel', ["By Product - Paket", "By Product - Retail"]);
                                                    }
                                                    if($divisi=="sampingan"){
                                                        $query2->whereIn('sales_channel', ["By Product - Paket", "By Product - Retail"]);
                                                    }
                                                })
                                                ->orderBy('id', 'desc')
                                                ->count();

        $selesai_order                      =   Order::where(function ($query) use ($tanggal, $tanggal_end, $tanggalKirim) {
                                                    if ($tanggalKirim == 0) {
                                                        $query->whereBetween('tanggal_so', [$tanggal, $tanggal_end]);
                                                    } else {
                                                        $query->whereBetween('tanggal_kirim', [$tanggal, $tanggal_end]);
                                                    }
                                                })
                                                ->where(function ($query2) use ($divisi){
                                                    if($divisi!="sampingan"){
                                                        $query2->whereNotIn('sales_channel', ["By Product - Paket", "By Product - Retail"]);
                                                    }
                                                    if($divisi=="sampingan"){
                                                        $query2->whereIn('sales_channel', ["By Product - Paket", "By Product - Retail"]);
                                                    }
                                                })
                                                ->orderBy('id', 'desc')
                                                ->where('status', '10')
                                                ->count();

        $pending_order                      =   Order::where(function ($query) use ($tanggal, $tanggal_end, $tanggalKirim) {
                                                    if ($tanggalKirim == 0) {
                                                        $query->whereBetween('tanggal_so', [$tanggal, $tanggal_end]);
                                                    } else {
                                                        $query->whereBetween('tanggal_kirim', [$tanggal, $tanggal_end]);
                                                    }
                                                })
                                                ->where(function ($query2) use ($divisi){
                                                    if($divisi!="sampingan"){
                                                        $query2->whereNotIn('sales_channel', ["By Product - Paket", "By Product - Retail"]);
                                                    }
                                                    if($divisi=="sampingan"){
                                                        $query2->whereIn('sales_channel', ["By Product - Paket", "By Product - Retail"]);
                                                    }
                                                })
                                                ->orderBy('id', 'desc')
                                                ->whereNull('status')
                                                ->where('status_so', 'Pending Fulfillment')
                                                ->count();
        $batal_order                        =   Order::where(function ($query) use ($tanggal, $tanggal_end, $tanggalKirim) {
                                                    if ($tanggalKirim == 0) {
                                                        $query->whereBetween('tanggal_so', [$tanggal, $tanggal_end]);
                                                    } else {
                                                        $query->whereBetween('tanggal_kirim', [$tanggal, $tanggal_end]);
                                                    }
                                                })
                                                ->where(function ($query2) use ($divisi){
                                                    if($divisi!="sampingan"){
                                                        $query2->whereNotIn('sales_channel', ["By Product - Paket", "By Product - Retail"]);
                                                    }
                                                    if($divisi=="sampingan"){
                                                        $query2->whereIn('sales_channel', ["By Product - Paket", "By Product - Retail"]);
                                                    }
                                                })
                                                ->orderBy('id', 'desc')
                                                ->where('status_so', 'Closed')
                                                ->count();

        $status_order   =   [
            'semua_order'                   =>  $semua_order,
            'selesai_order'                 =>  $selesai_order,
            'pending_order'                 =>  $pending_order,
            'batal_order'                   =>  $batal_order,
        ];

        return view('admin.pages.fulfillment.optimalisasi_order_v2.order', compact('pending', 'tanggal', 'customer', 'search', 'status_order', 'key', 'divisi', 'jenis', 'kategori','inkategori'));
    }


    public function orderItem(Request $request){
        if($request->key == 'parent'){
            // id ini berasal dari Order ID
            $getID          = OrderItem::where('order_id',$request->id)->pluck('id');
            return response()->json($getID);
        }
        else if($request->key == 'integrasinetsuite'){
            $orderid                        = $request->id;
            $jenis                          = $request->jenis ?? "semua";
            $divisi                         = $request->divisi ?? "semua";
            $order                          = Order::with(['fulfillNetsuite'])->find($orderid);
            return view('admin.pages.fulfillment.optimalisasi_order_v2.integrasi-data',compact('order','divisi','jenis'));
        }
        else{
            // Kalau id ini berasal dari orderitemid
            $id             = $request->id;
            $jenis          = $request->jenis ?? "semua";
            $divisi         = $request->divisi ?? "semua";
            
            $pemenuhan      = OrderItem::with(['bahan_baku','bahan_baku.relasi_netsuite_one','bahan_baku.bahanbborder'])->find($id);
            return view('admin.pages.fulfillment.optimalisasi_order_v2.order-bahan-baku-list', compact('pemenuhan', 'jenis', 'divisi'));
        }

    }

    public function pemenuhanAlokasi(Request $request){

        $order_item_id = $request->order_item_id;
        if ($request->key == 'info') {
            $order          =   OrderItem::find($order_item_id);
            $qty            =   Bahanbaku::where('order_item_id', $order_item_id)->sum('bb_item');
            $berat          =   Bahanbaku::where('order_item_id', $order_item_id)->sum('bb_berat');
            return view('admin.pages.fulfillment.optimalisasi_order_v2.order-item-info', compact('order', 'qty', 'berat'));
        } else {
            $pemenuhan      = OrderItem::with(['bahan_baku','bahan_baku.relasi_netsuite_one','bahan_baku.bahanbborder'])->find($order_item_id);
            return view('admin.pages.fulfillment.optimalisasi_order_v2.order-bahan-baku-list', compact('pemenuhan'));
        }
    }

    /**********************************************************************/
    /*                                                                    */
    /*     END OF OPTIMALISASI WITH ORM MULTIPLE COMPONENT VERSI 2        */
    /*                                                                    */
    /**********************************************************************/


    /**********************************************************************/
    /*                                                                    */
    /*               OPTIMALISASI WITHOUT ORM VERSI 3                     */
    /*                                                                    */
    /**********************************************************************/
   

    public function orderListV3(Request $request)
    {

        $divisi                             =   $request->divisi ?? "";
        $urutan                             =   $request->urutan ?? "ASC";

        if($urutan!="ASC" && $urutan!="DESC"){
            $urutan                         =   "ASC";
        }

        if(User::setIjin(26)){
            $divisi                         =   "sampingan";
        }
        if(User::setIjin(27)){
            $divisi                         =   "siap-kirim";
        }
        if(User::setIjin(31)){
            $divisi                         =   "siap-kirim-meyer";
        }
        if(User::setIjin(15)){
            $divisi                         =   "frozen";
        }
        if(User::setIjin(15) && User::setIjin(26)){
            $divisi                         =   $request->divisi;
        }
        if(User::setIjin('superadmin')){
            $divisi                         =   $request->divisi;
        }

        $customer                           =   $request->customer ?? "";

        if($divisi =="siap-kirim-meyer"){
            $search                         =   "Meyer";
        }else{
            $search                         =   $request->search ?? "";
        }
        $key                                =   $request->key ?? "";
        $jenis                              =   $request->jenis ?? "semua";
        $kategori                           =   $request->kategori ?? "semua";

        if(env('NET_SUBSIDIARY', 'CGL')=='CGL'){
            $tanggal                        =   $request->tanggal ?? date('Y-m-d');
            $tanggal_end                    =   $request->tanggal_end ?? date('Y-m-d');
        }else{
            $tanggal                        =   $request->tanggal ?? date('Y-m-d', strtotime('tomorrow'));
            $tanggal_end                    =   $request->tanggal_end ?? date('Y-m-d', strtotime('tomorrow'));
        }

        $tanggalKirim                       =   $request->tanggalkirimfulfillment ?? 0;

        if($jenis=="frozen"){
            $inkategori                     =   Category::where('nama', 'LIKE', "%FROZEN%")->pluck('id');
        }

        if($jenis=="fresh"){
            $inkategori                     =   Category::where('nama', 'NOT LIKE', "%FROZEN%")->pluck('id');
        }

        if($jenis=="semua"){
            $inkategori                     =   Category::pluck('id');
        }

        $pending                            =   Order::where(function ($query) use ($tanggal, $tanggal_end, $tanggalKirim) {
                                                    if ($tanggalKirim == 0) {
                                                        $query->whereBetween('tanggal_so', [$tanggal, $tanggal_end]);
                                                    } else {
                                                        $query->whereBetween('tanggal_kirim', [$tanggal, $tanggal_end]);
                                                    }
                                                })
                                                ->where(function($query) use ($customer, $search, $key){
                                                    if ($customer) {
                                                        $query->where('customer_id', $customer);
                                                    }
                                                    if ($search) {
                                                        $query->orWhere('nama', 'like', '%'.$search.'%');
                                                        $query->orWhere('no_so', 'like', '%'.$search.'%');
                                                        $query->orWhere('no_do', 'like', '%'.$search.'%');
                                                        $query->orWhere('keterangan', 'like', '%'.$search.'%');
                                                        $query->orWhere('sales_channel', 'like', '%'.$search.'%');
                                                    }
                                                    
                                                })
                                                ->where(function ($query3) use ($key){
                                                        if ($key) {
                                                        if ($key == 'selesai') {
                                                            $query3->where('status', 10);
                                                        } else
                                                        if ($key == 'proses' || $key == 'partial') {
                                                            $query3->where('status_so', 'Pending Fulfillment');
                                                            $query3->where('status', null);
                                                        } else
                                                        if ($key == 'batal') {
                                                            $query3->where('status_so', "Closed");
                                                        } else
                                                        if ($key == 'gagal') {
                                                            $query3->where('status', 10)->where('no_do', null);
                                                        }

                                                        if($key=="partial"){
                                                            $query3->whereIn('id', OrderItem::select('order_id')->whereNotNull('fulfillment_berat'));
                                                        }
                                                    }
                                                })
                                                ->where(function ($query2) use ($divisi, $kategori){
                                                    if($divisi!="sampingan"){
                                                        $query2->whereNotIn('sales_channel', ["By Product - Paket", "By Product - Retail"]);
                                                    }

                                                    if($divisi=="sampingan"){
                                                        $query2->whereIn('sales_channel', ["By Product - Paket", "By Product - Retail"]);
                                                    }

                                                    if($kategori!="semua"){
                                                        $query2->whereIn('sales_channel', [$kategori]);
                                                    }
                                                })
                                                ->where(function ($query3) use ($jenis){

                                                    if($jenis=="frozen"){
                                                        $kategori       =   Category::where('nama', 'LIKE', "%FROZEN%")->pluck('id');
                                                        $query3->whereIn('id', OrderItem::select('order_id')->whereIn('item_id', Item::select('id')->whereIn('category_id', $kategori)));
                                                    }

                                                    if($jenis=="fresh"){
                                                        $kategori       =   Category::where('nama', 'NOT LIKE', "%FROZEN%")->pluck('id');
                                                        $query3->whereIn('id', OrderItem::select('order_id')->whereIn('item_id', Item::select('id')->whereIn('category_id', $kategori)));
                                                    }

                                                });
        $masterQuery                        =   $urutan == "ASC" ? $pending->orderBy('id', "ASC") : $pending->orderBy('id', "DESC");
        $cloneAll                           =   clone $masterQuery;
        $clonePerPage                       =   clone $masterQuery;

        $dataid                             =   array();
        foreach($clonePerPage->paginate(self::PAGINASI) as $row){
            $dataid[]                       =   $row->id;
        }

        $stringData                         =   implode(",",$dataid);
        $masterData                         =   array();
        if($stringData){
            $masterData                     = OrderItem::leftJoin('items','items.id','order_items.item_id')
                                                        ->select('order_items.*','items.category_id')
                                                        ->whereRaw("order_id IN (".$stringData.")")
                                                        ->get();
        }

        if($request->key == 'integrasinetsuite'){
            $relasinetsuite                 =   Netsuite::where("tabel_id",$request->id)
                                                        ->where('label', 'itemfulfill')
                                                        ->where('tabel', 'orders')
                                                        ->whereYear('trans_date','>=','2024')
                                                        ->select('id','failed','status','response_id','document_code','trans_date','document_no','count','respon_time','tabel_id')
                                                        ->first();
            $order                          = Order::find($request->id);
            return view('admin.pages.fulfillment.optimalisasi_order_v3.integrasi-data',compact('order','relasinetsuite','divisi','jenis'));
        }
        
        $dataNS                             = "";
        $storeAll                           = array();
        foreach($cloneAll->get() as $rowdata){
            $storeAll[] = [
                "id"                        => $rowdata->id,
                "customer_id"               => $rowdata->customer_id,
                "netsuite_internal_id"      => $rowdata->netsuite_internal_id,
                "id_so"                     => $rowdata->id_so,
                "no_so"                     => $rowdata->no_so,
                "no_po"                     => $rowdata->no_po,
                "no_do"                     => $rowdata->no_do,
                "status_so"                 => $rowdata->status_so,
                "tanggal_so"                => $rowdata->tanggal_so,
                "sales_id"                  => $rowdata->sales_id,
                "sales_channel"             => $rowdata->sales_channel,
                "wilayah"                   => $rowdata->wilayah,
                "nama"                      => $rowdata->nama,
                "partner"                   => $rowdata->partner,
                "tanggal_kirim"             => $rowdata->tanggal_kirim,
                "alamat"                    => $rowdata->alamat,
                "alamat_kirim"              => $rowdata->alamat_kirim,
                "keterangan"                => $rowdata->keterangan,
                "kode"                      => $rowdata->kode,
                "no_invoice"                => $rowdata->no_invoice,
                "invoice_created_at"        => $rowdata->invoice_created_at,
                "telp"                      => $rowdata->telp,
                "kelurahan"                 => $rowdata->kelurahan,
                "kecamatan"                 => $rowdata->kecamatan,
                "kota"                      => $rowdata->kota,
                "provinsi"                  => $rowdata->provinsi,
                "kode_pos"                  => $rowdata->kode_pos,
                "kp_proses"                 => $rowdata->kp_proses,
                "kp_selesai"                => $rowdata->kp_selesai,
                "kr_proses"                 => $rowdata->kr_proses,
                "kr_selesai"                => $rowdata->kr_selesai,
                "status"                    => $rowdata->status,
                "key"                       => $rowdata->key,
                "ekspedisi"                 => $rowdata->ekspedisi,
                "created_at"                => $rowdata->created_at ? date('Y-m-d H:i:s', strtotime($rowdata->created_at)) : null,
                "updated_at"                => $rowdata->updated_at ? date('Y-m-d H:i:s', strtotime($rowdata->updated_at)) : null,
                "deleted_at"                => $rowdata->deleted_at ? date('Y-m-d H:i:s', strtotime($rowdata->deleted_at)) : null,
            ];
        }
        $storedataall                       = json_decode(json_encode($storeAll));
        $pending                            = Applib::paginate($storedataall,self::PAGINASI);


        $storeData                          = array();
        foreach($masterData as $val){
            $internalMemo                   = "";
            // $internalMemo                = Order::getInternalMemo($order->no_so, $val->id);
            $category_id                    = $val->category_id;
            $storeData[] = [
                "id"                        => $val->id,
                "order_id"                  => $val->order_id,
                "line_id"                   => $val->line_id,
                "netsuite_send"             => $val->netsuite_send,
                "item_id"                   => $val->item_id,
                "nama_detail"               => $val->nama_detail,
                "no_so"                     => $val->no_so,
                "partner"                   => $val->partner,
                "alamat_kirim"              => $val->alamat_kirim,
                "wilayah"                   => $val->wilayah,
                "part"                      => $val->part,
                "bumbu"                     => $val->bumbu,
                "memo"                      => $val->memo,
                "description_item"          => $val->description_item,
                "unit"                      => $val->unit,
                "rate"                      => $val->rate,
                "sku"                       => $val->sku,
                "potong"                    => $val->potong,
                "keterangan"                => $val->keterangan,
                "kode"                      => $val->kode,
                "qty"                       => $val->qty,
                "fulfillment_qty"           => $val->fulfillment_qty,
                "berat"                     => $val->berat,
                "fulfillment_berat"         => $val->fulfillment_berat,
                "harga"                     => $val->harga,
                "kr_proses"                 => $val->kr_proses,
                "kr_selesai"                => $val->kr_selesai,
                "retur_tujuan"              => $val->retur_tujuan,
                "retur_status"              => $val->retur_status,
                "retur_qty"                 => $val->retur_qty,
                "tidak_terkirim"            => $val->tidak_terkirim,
                "tidak_terkirim_catatan"    => $val->tidak_terkirim_catatan,
                "retur_berat"               => $val->retur_berat,
                "retur_notes"               => $val->retur_notes,
                "status"                    => $val->status,
                "key"                       => $val->key,
                "created_at"                => $val->created_at ? date('Y-m-d H:i:s', strtotime($val->created_at)) : null,
                "updated_at"                => $val->updated_at ? date('Y-m-d H:i:s', strtotime($val->updated_at)) : null,
                "deleted_at"                => $val->deleted_at ? date('Y-m-d H:i:s', strtotime($val->deleted_at)) : null,
                "edited"                    => $val->edited,
                "category_id"               => $category_id,
                "internalMemo"              => $internalMemo ?? "#",
            ];
        }

        $store                              = json_decode(json_encode($storeData));
        $semua_order                        =   Order::where(function ($query) use ($tanggal, $tanggal_end, $tanggalKirim) {
                                                    if ($tanggalKirim == 0) {
                                                        $query->whereBetween('tanggal_so', [$tanggal, $tanggal_end]);
                                                    } else {
                                                        $query->whereBetween('tanggal_kirim', [$tanggal, $tanggal_end]);
                                                    }
                                                })
                                                ->where(function ($query2) use ($divisi){
                                                    if($divisi!="sampingan"){
                                                        $query2->whereNotIn('sales_channel', ["By Product - Paket", "By Product - Retail"]);
                                                    }
                                                    if($divisi=="sampingan"){
                                                        $query2->whereIn('sales_channel', ["By Product - Paket", "By Product - Retail"]);
                                                    }
                                                })
                                                ->orderBy('id', 'desc')
                                                ->count();

        $selesai_order                      =   Order::where(function ($query) use ($tanggal, $tanggal_end, $tanggalKirim) {
                                                    if ($tanggalKirim == 0) {
                                                        $query->whereBetween('tanggal_so', [$tanggal, $tanggal_end]);
                                                    } else {
                                                        $query->whereBetween('tanggal_kirim', [$tanggal, $tanggal_end]);
                                                    }
                                                })
                                                ->where(function ($query2) use ($divisi){
                                                    if($divisi!="sampingan"){
                                                        $query2->whereNotIn('sales_channel', ["By Product - Paket", "By Product - Retail"]);
                                                    }
                                                    if($divisi=="sampingan"){
                                                        $query2->whereIn('sales_channel', ["By Product - Paket", "By Product - Retail"]);
                                                    }
                                                })
                                                ->orderBy('id', 'desc')
                                                ->where('status', '10')
                                                ->count();

        $pending_order                      =   Order::where(function ($query) use ($tanggal, $tanggal_end, $tanggalKirim) {
                                                    if ($tanggalKirim == 0) {
                                                        $query->whereBetween('tanggal_so', [$tanggal, $tanggal_end]);
                                                    } else {
                                                        $query->whereBetween('tanggal_kirim', [$tanggal, $tanggal_end]);
                                                    }
                                                })
                                                ->where(function ($query2) use ($divisi){
                                                    if($divisi!="sampingan"){
                                                        $query2->whereNotIn('sales_channel', ["By Product - Paket", "By Product - Retail"]);
                                                    }
                                                    if($divisi=="sampingan"){
                                                        $query2->whereIn('sales_channel', ["By Product - Paket", "By Product - Retail"]);
                                                    }
                                                })
                                                ->orderBy('id', 'desc')
                                                ->whereNull('status')
                                                ->where('status_so', 'Pending Fulfillment')
                                                ->count();
        $batal_order                        =   Order::where(function ($query) use ($tanggal, $tanggal_end, $tanggalKirim) {
                                                    if ($tanggalKirim == 0) {
                                                        $query->whereBetween('tanggal_so', [$tanggal, $tanggal_end]);
                                                    } else {
                                                        $query->whereBetween('tanggal_kirim', [$tanggal, $tanggal_end]);
                                                    }
                                                })
                                                ->where(function ($query2) use ($divisi){
                                                    if($divisi!="sampingan"){
                                                        $query2->whereNotIn('sales_channel', ["By Product - Paket", "By Product - Retail"]);
                                                    }
                                                    if($divisi=="sampingan"){
                                                        $query2->whereIn('sales_channel', ["By Product - Paket", "By Product - Retail"]);
                                                    }
                                                })
                                                ->orderBy('id', 'desc')
                                                ->where('status_so', 'Closed')
                                                ->count();

        $status_order   =   [
            'semua_order'                   =>  $semua_order,
            'selesai_order'                 =>  $selesai_order,
            'pending_order'                 =>  $pending_order,
            'batal_order'                   =>  $batal_order,
        ];

        return view('admin.pages.fulfillment.optimalisasi_order_v3.order', compact('pending', 'tanggal', 'customer', 'search', 'status_order', 'key', 'divisi', 'jenis', 'kategori','masterData','inkategori'));
    }


    public function orderItemV3(Request $request){
        if($request->key == 'parent'){
            $getID = OrderItem::where('order_id',$request->id)->pluck('id');
            return response()->json($getID);
        }else{
            $id             = $request->id;
            $jenis          = $request->jenis ?? "semua";
            $divisi         = $request->divisi ?? "semua";

            if($id){
                $pemenuhan  = BahanBaku::leftJoin('orders','orders.id','order_bahan_baku.order_id')->select('order_bahan_baku.*','orders.status')->where("order_item_id",$id)->get();
                $store      = array();
                foreach($pemenuhan as $val){
                    $nomorTI                = Netsuite::join('order_bahan_baku', 'order_bahan_baku.netsuite_id', '=', 'netsuite.id')
                                                        ->where('record_type', 'transfer_inventory')
                                                        ->where('netsuite.id', $val->netsuite_id)
                                                        ->whereYear('netsuite.trans_date','>=','2024')
                                                        ->select('netsuite.document_no')
                                                        ->first();
                    $store[] = [
                        "id"                => $val->id,
                        "nama"              => $val->nama,
                        "chiller_id"        => $val->chiller_id,
                        "chiller_out"       => $val->chiller_out,
                        "order_id"          => $val->order_id,
                        "chiller_alokasi"   => $val->chiller_alokasi,
                        "order_item_id"     => $val->order_item_id,
                        "type"              => $val->type,
                        "proses_ambil"      => $val->proses_ambil,
                        "data_chiller"      => $val->data_chiller,
                        "data_order"        => $val->data_order,
                        "data_order_item"   => $val->data_order_item,
                        "bb_item"           => $val->bb_item,
                        "bb_berat"          => $val->bb_berat,
                        "doc_number"        => $val->doc_number,
                        "no_do"             => $val->no_do,
                        "keterangan"        => $val->keterangan,
                        "keranjang"         => $val->keranjang,
                        "unit"              => $val->unit,
                        "netsuite_id"       => $val->netsuite_id,
                        "created_at"        => $val->created_at ? date('Y-m-d H:i:s', strtotime($val->created_at)) : null,
                        "updated_at"        => $val->updated_at ? date('Y-m-d H:i:s', strtotime($val->updated_at)) : null,
                        "deleted_at"        => $val->deleted_at ? date('Y-m-d H:i:s', strtotime($val->deleted_at)) : null,
                        "key"               => $val->key,
                        "status"            => $val->status,
                        "ekspedisi"         => $val->ekspedisi,
                        "nomorTI"           => $nomorTI,
                        "statusbahanbborder"=> $val->bahanbborder->status
                    ];
                }

                $store                      = json_decode(json_encode($store));
                return view('admin.pages.fulfillment.optimalisasi_order_v3.order-item', compact('jenis', 'divisi','store'));
            }
            else{
                return redirect(route('fulfillment.index'));
            }
        }
    }

    public function pemenuhanAlokasiV3(Request $request){

        $order_item_id = $request->order_item_id;
        if ($request->key == 'info') {
            $order  =   OrderItem::find($order_item_id);
            $qty    =   Bahanbaku::where('order_item_id', $order_item_id)->sum('bb_item');
            $berat  =   Bahanbaku::where('order_item_id', $order_item_id)->sum('bb_berat');
            return view('admin.pages.fulfillment.optimalisasi_order_v3.order-item-info', compact('order', 'qty', 'berat'));
        } else {
            $pemenuhan = Bahanbaku::where('order_item_id', $order_item_id)->get();
            return view('admin.pages.fulfillment.optimalisasi_order_v3.order-bahan-baku-list', compact('pemenuhan'));
        }
    }


    /**********************************************************************/
    /*                                                                    */
    /*        END OF OPTIMALISASI WITHOUT ORM VERSI 3                     */
    /*                                                                    */
    /**********************************************************************/






    public function data_chiller_fg(Request $request){
        $tanggal        = $request->tanggal ?? date('Y-m-d');
        $tanggal_akhir  = $request->tanggal_akhir ?? $tanggal;
        $item_id        = $request->item_id ?? "";
        $item           = Item::find($item_id);
        $pencarian      = $request->pencarian ?? "";
        $cs             = Customer::select('id')->where('nama', 'like', '%'.$pencarian.'%')->get();

        $sql            = Chiller::whereIn('asal_tujuan', ['free_stock', 'retur', 'karkasbeli', 'evisbeli', 'hasilbeli', 'open_balance', 'thawing'])
                        ->where('jenis', 'masuk')
                        ->whereIn('type', ['hasil-produksi', 'bahan-baku'])
                        ->whereIn('item_name', Item::select('nama')->where('netsuite_internal_id', $item->netsuite_internal_id))
                        ->whereBetween('tanggal_produksi', [$tanggal, $tanggal_akhir])
                        ->where('stock_berat', '>', 0)
                        ->where('status_cutoff', NULL)
                        ->orderBy('tanggal_produksi', 'desc');

                        if($pencarian!=""){
                            $produk = $sql->where(function($query) use ($pencarian, $cs) {
                                $query->orWhere('plastik_nama', 'like', '%'.$pencarian.'%');
                                $query->orWhere('label', 'like', '%'.$pencarian.'%');

                                if(count($cs)>0){
                                $query->orWhere(function($query) use ($pencarian) {
                                        $query->whereIn('customer_id', Customer::select('id')->where('nama', 'like', '%'.$pencarian.'%'));
                                    });
                                }
                            });
                        }

        $sql            = $sql;

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
            $alokasi    = DB::select("select chiller_out,SUM(bb_item) AS total_qty_alokasi, ROUND(sum(bb_berat),2) AS total_berat_alokasi, sum(keranjang) as total_keranjang
                                FROM order_bahan_baku WHERE chiller_out IN(".$stringData.") 
                                AND `status` IN(1,2) AND deleted_at IS NULL 
                                GROUP BY chiller_out");
            $ambilabf   = DB::select("select table_id, sum(qty_awal) as total_qty_abf, round(sum(berat_awal),2) as total_berat_abf 
                                FROM abf where table_name='chiller' AND table_id IN(".$stringData.")
                                AND deleted_at IS NULL GROUP BY table_id");
            $ambilbb    = DB::select("select chiller_id, sum(qty) as total_qty_freestock, round(sum(berat),2) AS total_berat_freestock
                                FROM free_stocklist JOIN free_stock ON free_stocklist.freestock_id=free_stock.id 
                                WHERE free_stocklist.chiller_id IN(".$stringData.") and free_stock.status IN (1,2,3)
                                AND free_stock.deleted_at IS NULL AND free_stocklist.deleted_at IS NULL
                                GROUP BY chiller_id
                                ");
            $musnahkan  = DB::select("select item_id, SUM(qty) AS total_qty_musnahkan, ROUND(sum(berat),2) AS total_berat_musnahkan 
                                FROM musnahkan_temp JOIN musnahkan on musnahkan.id=musnahkan_temp.musnahkan_id WHERE gudang_id IN (2,4,23,24) AND item_id IN(".$stringData.")
                                AND musnahkan.deleted_at IS NULL GROUP BY item_id ");
        }
        $arraymodification      = [];
        foreach($arrayData as $data){
            $total_qty_alokasi      = 0;
            $total_berat_alokasi    = 0;
            $total_keranjang        = 0;
            $total_qty_abf          = 0;
            $total_berat_abf        = 0;
            $total_qty_freestock    = 0;
            $total_berat_freestock  = 0;
            $total_qty_musnahkan    = 0;
            $total_berat_musnahkan  = 0;

            foreach($alokasi as $val){
                if($data->id == $val->chiller_out){
                    $total_qty_alokasi      = $val->total_qty_alokasi;
                    $total_berat_alokasi    = floatval($val->total_berat_alokasi) ?? 0;
                    $total_keranjang        = $val->total_keranjang ?? 0;
                }
            }
            foreach($ambilabf as $valabf){
                if($data->id == $valabf->table_id){
                    $total_qty_abf          = $valabf->total_qty_abf;
                    $total_berat_abf        = floatval($valabf->total_berat_abf) ?? 0;
                }
            }
            foreach($ambilbb as $valbb){
                if($data->id == $valbb->chiller_id){
                    $total_qty_freestock    = $valbb->total_qty_freestock;
                    $total_berat_freestock  = floatval($valbb->total_berat_freestock) ?? 0;
                }
            }

            foreach($musnahkan as $valmus){
                if($data->id == $valmus->item_id){
                    $total_qty_musnahkan    = $valmus->total_qty_musnahkan;
                    $total_berat_musnahkan  = floatval($valmus->total_berat_musnahkan);
                }
            }

            $arraymodification[] = [
                "id"                    => $data->id,
                "production_id"         => $data->production_id,
                "table_name"            => $data->table_name,
                "table_id"              => $data->table_id,
                "asal_tujuan"           => $data->asal_tujuan,
                "item_id"               => $data->item_id,
                "item_name"             => $data->item_name,
                "jenis"                 => $data->jenis,
                "type"                  => $data->type,
                "kategori"              => $data->kategori,
                "regu"                  => $data->regu,
                "label"                 => $data->label,
                "plastik_sku"           => $data->plastik_sku,
                "plastik_nama"          => $data->plastik_nama,
                "plastik_qty"           => $data->plastik_qty,
                "plastik_group"         => $data->plastik_group,
                "parting"               => $data->parting,
                "sub_item"              => $data->sub_item,
                "selonjor"              => $data->selonjor,
                "kode_produksi"         => $data->kode_produksi,
                "unit"                  => $data->unit,
                "customer_id"           => $data->customer_id,
                "customer_name"         => $data->konsumen->nama ?? 'FREE STOCK',
                "qty_item"              => floatval($data->qty_item),
                "berat_item"            => floatval($data->berat_item),
                "tanggal_potong"        => $data->tanggal_potong,
                "no_mobil"              => $data->no_mobil,
                "tanggal_produksi"      => $data->tanggal_produksi,
                "keranjang"             => $data->keranjang,
                "berat_keranjang"       => $data->berat_keranjang,
                "stock_item"            => $data->stock_item,
                "stock_berat"           => floatval($data->stock_berat),
                "status"                => $data->status,
                "status_cutoff"         => $data->status_cutoff,
                "key"                   => $data->key,
                "created_at"            => $data->created_at ? date('Y-m-d H:i:s', strtotime($data->created_at)) : null,
                "updated_at"            => $data->updated_at ? date('Y-m-d H:i:s', strtotime($data->updated_at)) : null,
                "deleted_at"            => $data->deleted_at ? date('Y-m-d H:i:s', strtotime($data->deleted_at)) : null,
                "nama"                  => $data->nama,
                'total_qty_alokasi'     => $total_qty_alokasi,
                'total_berat_alokasi'   => $total_berat_alokasi,
                'total_qty_abf'         => $total_qty_abf,
                'total_berat_abf'       => $total_berat_abf,
                'total_qty_freestock'   => $total_qty_freestock,
                'total_berat_freestock' => $total_berat_freestock,
                'total_keranjang'       => $total_keranjang,
                'total_qty_musnahkan'   => $total_qty_musnahkan,
                'total_berat_musnahkan' => $total_berat_musnahkan,
                'sisaQty'               => $data->qty_item - $total_qty_alokasi - $total_qty_abf - $total_qty_freestock - $total_qty_musnahkan,
                'sisaBerat'             => $data->berat_item - $total_berat_alokasi - $total_berat_abf - $total_berat_freestock - $total_berat_musnahkan
            ];
        }
        $produk                     = json_decode(json_encode($arraymodification));
        // dd($produk);

        return view('admin.pages.fulfillment.data.data-chiller-fg', compact(['produk']));
    }

    public function data_chiller_bb(Request $request){
        $tanggal        = $request->tanggal ?? date('Y-m-d');
        $tanggal_akhir  = $request->tanggal_akhir ?? $tanggal;
        $item_id        = $request->item_id ?? "";
        $pencarian      = $request->pencarian ?? "";
        $cs             = Customer::select('id')->where('nama', 'like', '%'.$pencarian.'%')->get();
        $item           = Item::find($item_id);

        $sql            = Chiller::whereIn('asal_tujuan', ['evisgabungan', 'retur', 'open_balance', 'thawing', 'gradinggabungan'])
                        ->where('jenis', 'masuk')
                        ->whereIn('type', ['hasil-produksi', 'bahan-baku'])
                        ->where('status_cutoff', NULL)
                        ->whereIn('item_name', Item::select('nama')->where('netsuite_internal_id', $item->netsuite_internal_id));
                        // ->whereDate('tanggal_produksi', $tanggal)
                        // ->get();

                        if($pencarian!=""){
                            $produk = $sql->where(function($query) use ($pencarian, $cs) {
                                $query->orWhere('plastik_nama', 'like', '%'.$pencarian.'%');
                                $query->orWhere('label', 'like', '%'.$pencarian.'%');

                                if(count($cs)>0){
                                $query->orWhere(function($query) use ($pencarian) {
                                        $query->whereIn('customer_id', Customer::select('id')->where('nama', 'like', '%'.$pencarian.'%'));
                                    });
                                }
                            });
                        }

        $sql            = $sql->whereBetween('tanggal_produksi', [$tanggal, $tanggal_akhir]);
        $sql            = $sql->orderBy('tanggal_produksi', 'desc');

        $master         = clone $sql;
        $arrayData      = $master->get();
        
        // dd($arrayData);
        $arrayId        = array();
        foreach($arrayData as $item){
            $arrayId[]  = $item->id;
        }
        $stringData     = implode(",",$arrayId);

        if($stringData){
            $alokasi    = DB::select("select chiller_out,SUM(bb_item) AS total_qty_alokasi, ROUND(sum(bb_berat),2) AS total_berat_alokasi, sum(keranjang) as total_keranjang
                                FROM order_bahan_baku WHERE chiller_out IN(".$stringData.") 
                                AND `status` IN(1,2) AND deleted_at IS NULL 
                                GROUP BY chiller_out");
            
            $ambilabf   = DB::select("select table_id, sum(qty_awal) as total_qty_abf, round(sum(berat_awal),2) as total_berat_abf 
                                FROM abf where table_name='chiller' AND table_id IN(".$stringData.")
                                AND deleted_at IS NULL GROUP BY table_id");
            $ambilbb    = DB::select("select chiller_id, sum(qty) as total_qty_freestock, round(sum(berat),2) AS total_berat_freestock
                                FROM free_stocklist JOIN free_stock ON free_stocklist.freestock_id=free_stock.id 
                                WHERE free_stocklist.chiller_id IN(".$stringData.") and free_stock.status IN (1,2,3)
                                AND free_stock.deleted_at IS NULL AND free_stocklist.deleted_at IS NULL
                                GROUP BY chiller_id
                                ");
            $musnahkan  = DB::select("select item_id, SUM(qty) AS total_qty_musnahkan, ROUND(sum(berat),2) AS total_berat_musnahkan 
                                FROM musnahkan_temp JOIN musnahkan on musnahkan.id=musnahkan_temp.musnahkan_id WHERE gudang_id IN (2,4,23,24) AND item_id IN(".$stringData.")
                                AND musnahkan.deleted_at IS NULL GROUP BY item_id ");
        }
        $arraymodification      = [];
        foreach($arrayData as $data){
            $total_qty_alokasi      = 0;
            $total_berat_alokasi    = 0;
            $total_keranjang        = 0;
            $total_qty_abf          = 0;
            $total_berat_abf        = 0;
            $total_qty_freestock    = 0;
            $total_berat_freestock  = 0;
            $total_qty_musnahkan    = 0;
            $total_berat_musnahkan  = 0;

            foreach($alokasi as $val){
                if($data->id == $val->chiller_out){
                    $total_qty_alokasi      = $val->total_qty_alokasi;
                    $total_berat_alokasi    = floatval($val->total_berat_alokasi) ?? 0;
                    $total_keranjang        = $val->total_keranjang ?? 0;
                }
            }
            foreach($ambilabf as $valabf){
                if($data->id == $valabf->table_id){
                    $total_qty_abf          = $valabf->total_qty_abf;
                    $total_berat_abf        = floatval($valabf->total_berat_abf) ?? 0;
                }
            }
            foreach($ambilbb as $valbb){
                if($data->id == $valbb->chiller_id){
                    $total_qty_freestock    = $valbb->total_qty_freestock;
                    $total_berat_freestock  = floatval($valbb->total_berat_freestock) ?? 0;
                }
            }

            foreach($musnahkan as $valmus){
                if($data->id == $valmus->item_id){
                    $total_qty_musnahkan    = $valmus->total_qty_musnahkan;
                    $total_berat_musnahkan  = floatval($valmus->total_berat_musnahkan) ?? 0;
                }
            }
            $arraymodification[] = [
                "id"                    => $data->id,
                "production_id"         => $data->production_id,
                "table_name"            => $data->table_name,
                "table_id"              => $data->table_id,
                "asal_tujuan"           => $data->asal_tujuan,
                "item_id"               => $data->item_id,
                "item_name"             => $data->item_name,
                "jenis"                 => $data->jenis,
                "type"                  => $data->type,
                "kategori"              => $data->kategori,
                "regu"                  => $data->regu,
                "label"                 => $data->label,
                "plastik_sku"           => $data->plastik_sku,
                "plastik_nama"          => $data->plastik_nama,
                "plastik_qty"           => $data->plastik_qty,
                "plastik_group"         => $data->plastik_group,
                "parting"               => $data->parting,
                "sub_item"              => $data->sub_item,
                "selonjor"              => $data->selonjor,
                "kode_produksi"         => $data->kode_produksi,
                "unit"                  => $data->unit,
                "customer_id"           => $data->customer_id,
                "customer_name"         => $data->konsumen->nama ?? "FREE STOCK",
                "qty_item"              => floatval($data->qty_item),
                "berat_item"            => floatval($data->berat_item),
                "tanggal_potong"        => $data->tanggal_potong,
                "no_mobil"              => $data->no_mobil,
                "tanggal_produksi"      => $data->tanggal_produksi,
                "keranjang"             => $data->keranjang,
                "berat_keranjang"       => $data->berat_keranjang,
                "stock_item"            => $data->stock_item,
                "stock_berat"           => floatval($data->stock_berat),
                "status"                => $data->status,
                "status_cutoff"         => $data->status_cutoff,
                "key"                   => $data->key,
                "created_at"            => $data->created_at ? date('Y-m-d H:i:s', strtotime($data->created_at)) : null,
                "updated_at"            => $data->updated_at ? date('Y-m-d H:i:s', strtotime($data->updated_at)) : null,
                "deleted_at"            => $data->deleted_at ? date('Y-m-d H:i:s', strtotime($data->deleted_at)) : null,
                "nama"                  => $data->nama,
                'total_qty_alokasi'     => $total_qty_alokasi,
                'total_berat_alokasi'   => $total_berat_alokasi,
                'total_keranjang'       => $total_keranjang,
                'total_qty_abf'         => $total_qty_abf,
                'total_berat_abf'       => $total_berat_abf,
                'total_qty_freestock'   => $total_qty_freestock,
                'total_berat_freestock' => $total_berat_freestock,
                'total_qty_musnahkan'   => $total_qty_musnahkan,
                'total_berat_musnahkan' => $total_berat_musnahkan,
                'sisaQty'               => $data->qty_item - $total_qty_alokasi - $total_qty_abf - $total_qty_freestock - $total_qty_musnahkan,
                'sisaBerat'             => $data->berat_item - $total_berat_alokasi - $total_berat_abf - $total_berat_freestock - $total_berat_musnahkan
            ];
        }
        $produk                     = json_decode(json_encode($arraymodification));

        return view('admin.pages.fulfillment.data.data-chiller-bb', compact('produk', 'item_id'));
    }

    public function data_product_gudang(Request $request){
        $tanggal        = $request->tanggal ?? date('Y-m-d');
        $tanggal_akhir  = $request->tanggal_akhir ?? $tanggal;
        $item_id        = $request->item_id ?? "";
        $pencarian      = $request->pencarian ?? "";
        $cs             = Customer::select('id')->where('nama', 'like', '%'.$pencarian.'%')->get();
        $item           = Item::find($item_id);

        $sql            = Product_gudang::where('jenis_trans', 'masuk')
                                            ->whereNotIn('type',['inventory_adjustment'])
                                            ->whereIn('nama', Item::select('nama')->where('netsuite_internal_id', $item->netsuite_internal_id))
                                            ->whereIn('status', [2])
                                            ->where(function($query) use ($pencarian,$cs) {
                                                if($pencarian !=""){
                                                    $query->orWhere('sub_item', 'like', '%'.$pencarian.'%');
                                                    $query->orWhere('packaging', 'like', '%'.$pencarian.'%');
                                                    $query->orWhere('label', 'like', '%'.$pencarian.'%');

                                                    if(count($cs) > 0){
                                                    $query->orWhere(function($query2) use ($pencarian) {
                                                            $query2->whereIn('customer_id', Customer::select('id')->where('nama', 'like', '%'.$pencarian.'%'));
                                                        });
                                                    }
                                                }
                                            })
                                            ->whereBetween('production_date', [$tanggal, $tanggal_akhir])
                                            ->orderBy('production_date', 'desc');

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
                    $total_qty_orderthawing          = $valthawing->total_qty_orderthawing;
                    $total_berat_orderthawing        = floatval($valthawing->total_berat_orderthawing) ?? 0;
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
                "product_id"                => $data->product_id,
                "nama"                      => $data->nama,
                "kategori"                  => $data->kategori,
                "sub_item"                  => $data->sub_item,
                "table_name"                => $data->table_name,
                "table_id"                  => $data->table_id,
                "no_so"                     => $data->no_so,
                "order_id"                  => $data->order_id,
                "order_item_id"             => $data->order_item_id,
                "order_bb_id"               => $data->order_bb_id,
                "qty_awal"                  => $data->qty_awal,
                "berat_awal"                => floatval($data->berat_awal),
                "qty"                       => $data->qty,
                "berat_timbang"             => $data->berat_timbang,
                "berat"                     => floatval($data->berat),
                "notes"                     => $data->notes,
                "label"                     => $data->label,
                "subpack"                   => $data->subpack,
                "packaging"                 => $data->packaging,
                "plastik_group"             => $data->plastik_group,
                "plastik_qty"               => $data->plastik_qty,
                "keterangan"                => $data->keterangan,
                "grade_item"                => $data->grade_item,
                "parting"                   => $data->parting,
                "karung"                    => $data->karung,
                "karung_qty"                => $data->karung_qty,
                "karung_isi"                => $data->karung_isi,
                "karung_awal"               => $data->karung_awal,
                "selonjor"                  => $data->selonjor,
                "customer_id"               => $data->customer_id,
                "customer_name"             => $data->konsumen->nama ?? "",
                "palete"                    => $data->palete,
                "potong"                    => $data->potong,
                "expired"                   => $data->expired,
                "production_date"           => $data->production_date,
                "tanggal_kemasan"           => $data->tanggal_kemasan,
                "production_code"           => $data->production_code,
                "type"                      => $data->type,
                "request_thawing"           => $data->request_thawing,
                "stock_type"                => $data->stock_type,
                "jenis_trans"               => $data->jenis_trans,
                "abf_id"                    => $data->abf_id,
                "gudang_id"                 => $data->gudang_id,
                "kode_gudang"               => $data->productgudang->code ?? '#',
                "asal_abf"                  => $data->asal_abf,
                "barang_titipan"            => $data->barang_titipan,
                "no_urut"                   => $data->no_urut,
                "chiller_id"                => $data->chiller_id,
                "gudang_id_keluar"          => $data->gudang_id_keluar,
                "status"                    => $data->status,
                "key"                       => $data->key,
                "created_at"                => $data->created_at ? date('Y-m-d H:i:s', strtotime($data->created_at)) : null,
                "updated_at"                => $data->updated_at ? date('Y-m-d H:i:s', strtotime($data->updated_at)) : null,
                "deleted_at"                => $data->deleted_at ? date('Y-m-d H:i:s', strtotime($data->deleted_at)) : null,
                "total_qty_alokasi"         => $total_qty_alokasi,
                "total_berat_alokasi"       => $total_berat_alokasi,
                "total_keranjang"           => $total_keranjang,
                "total_qty_orderthawing"    => $total_qty_orderthawing,
                "total_berat_orderthawing"  => $total_berat_orderthawing,
                "total_qty_regrading"       => $total_qty_regrading,
                "total_berat_regrading"     => $total_berat_regrading,
                'total_qty_musnahkan'       => $total_qty_musnahkan,
                'total_berat_musnahkan'     => $total_berat_musnahkan,
                'sisaQty'                   => $data->qty_awal - $total_qty_alokasi - $total_qty_orderthawing - $total_qty_regrading - $total_qty_musnahkan,
                'sisaBerat'                 => $data->berat_awal - $total_berat_alokasi - $total_berat_orderthawing - $total_berat_regrading - $total_berat_musnahkan
            ];
        }
        $collection                         = json_decode(json_encode($arraymodification));
        $stock                              = array_filter($collection, function($vn){
            return $vn->sisaBerat > 0;
        });
        
        return view('admin.pages.fulfillment.data.data-product-gudang', compact(['stock']));
    }

    public function simpanAlokasi(Request $request){


        DB::beginTransaction();

        if ($request->berat == null) {
            DB::rollBack() ;
            $data['status'] =   400 ;
            $data['msg']    =   'Isi Bahan Baku' ;
            return $data ;
        }

        for ($x = 0; $x < COUNT($request->x_code); $x++) {

                if (substr(strval($request->berat[$x]), 0, 1) == "-") {
                    DB::rollBack() ;
                    $data['status'] =   400 ;
                    $data['msg']    =   'Berat Tidak boleh minus' ;
                    return $data ;
                }

                if (substr(strval($request->keranjang[$x]), 0, 1) == "-") {
                    DB::rollBack() ;
                    $data['status'] =   400 ;
                    $data['msg']    =   'Keranjang tidak boleh minus' ;
                    return $data ;
                }

                if (substr(strval($request->qty[$x]), 0, 1) == "-") {
                    DB::rollBack() ;
                    $data['status'] =   400 ;
                    $data['msg']    =   'Qty tidak boleh minus' ;
                    return $data ;
                }
                

            if ($request->berat[$x] || $request->qty[$x]) {

                $proses_ambil = $request->lokasi_asal;

                if($proses_ambil=="chillerfg"){

                    $chiller                        = Chiller::find($request->x_code[$x]);

                    $sisaQtyChiller                 = Chiller::ambilsisachiller($chiller->id,'qty_item','qty','bb_item');
                    $sisaBeratChiller               = Chiller::ambilsisachiller($chiller->id,'berat_item','berat','bb_berat');
                    $convertSisaBerat               = number_format((float)$sisaBeratChiller, 2, '.', '');
                    
                    if ($request->qty[$x] > $sisaQtyChiller) {
                        DB::rollBack() ;
                        $data['status'] =   400;
                        $data['msg']    =   'Qty Bahan baku mungkin sudah digunakan regu lain';
                        return $data;
                        // return back()->with('status', 2)->with('message', 'Proses gagal, bahan baku mungkin sudah digunakan regu lain')->with('tabs', $request->type_input);
                    }

                    if ($request->berat[$x] > $convertSisaBerat) {
                        DB::rollBack() ;
                        $data['status'] =   400;
                        $data['msg']    =   'Pengambilan Lebih Besar dari Stock Atau Sudah digunakan Regu lain, Refresh dahulu';
                        return $data;
                        // return back()->with('status', 2)->with('message', 'Proses gagal, bahan baku mungkin sudah digunakan regu lain')->with('tabs', $request->type_input);
                    }

                    // if ($request->berat[$x] > $chiller->stock_berat && $request->berat[$x] < 0) {
                    //     DB::rollBack() ;
                    //     $data['status'] =   400 ;
                    //     $data['msg']    =   'Pengambilan Lebih Besar dari Stock' ;
                    //     return $data ;
                    // }

                    if ($request->berat[$x] == 0) {
                        DB::rollBack() ;
                        $data['status'] =   400 ;
                        $data['msg']    =   'Pengambilan tidak boleh 0' ;
                        return $data ;
                    }

                    // if ($request->qty[$x] == 0) {
                    //     DB::rollBack() ;
                    //     $data['status'] =   400 ;
                    //     $data['msg']    =   'Pengambilan tidak boleh 0' ;
                    //     return $data ;
                    // }

                    $orderItem                          = OrderItem::find($request->order_item_id);
                    if ($orderItem) {
                        if ($chiller->item_name != $orderItem->nama_detail) {
                            DB::rollBack() ;
                            $data['status'] =   400 ;
                            $data['msg']    =   'Terjadi kesalahan, Item Order berbeda dengan fulfill' ;
                            return $data ;
                        }

                    } else {
                        DB::rollBack() ;
                        $data['status'] =   400 ;
                        $data['msg']    =   'Terjadi kesalahan, Item Order tidak ditemukan' ;
                        return $data ;
                    }

                    $orderBahanBaku                     =   new Bahanbaku;
                    $orderBahanBaku->chiller_out        =   $chiller->id;
                    $orderBahanBaku->order_id           =   $request->order_id;
                    $orderBahanBaku->nama               =   $chiller->item_name;
                    $orderBahanBaku->proses_ambil       =   $proses_ambil;
                    $orderBahanBaku->order_item_id      =   $request->order_item_id;
                    $orderBahanBaku->bb_item            =   $request->qty[$x];
                    $orderBahanBaku->bb_berat           =   $request->berat[$x];
                    $orderBahanBaku->keranjang          =   $request->keranjang[$x];
                    $orderBahanBaku->status             =   1;
                    if (!$orderBahanBaku->save()) {
                        DB::rollBack() ;
                        $data['status'] =   400 ;
                        $data['msg']    =   'Terjadi kesalahan proses order' ;
                        return $data ;
                    }

                } elseif($proses_ambil=="frozen"){

                    $storage                        = Product_gudang::find($request->x_code[$x]);
                    $sisaQtyGudang                  = Product_gudang::ambilsisaproductgudang($storage->id,'qty_awal','qty','bb_item');
                    $sisaBeratGudang                = Product_gudang::ambilsisaproductgudang($storage->id,'berat_awal','berat','bb_berat');
                    $convertSisaBerat               = number_format((float)$sisaBeratGudang, 2, '.', '');


                    if ($request->berat[$x] > $convertSisaBerat) {
                        DB::rollBack() ;
                        $data['status'] =   400 ;
                        $data['msg']    =   'Pengambilan Lebih Besar dari Stock' ;
                        return $data ;
                    }

                    if ($request->berat[$x] == 0) {
                        DB::rollBack();
                        $data['status'] = 400;
                        $data['msg'] = 'Pengambilan tidak boleh 0';
                        return $data;
                    }
                

                    // if ($request->berat[$x] > ($storage->berat - $storage->total_bb_berat) || $request->qty[$x] > ($storage->qty - $storage->total_bb_item)) {
                    //     DB::rollBack() ;
                    //     $data['status'] =   400 ;
                    //     $data['msg']    =   'Pengambilan Lebih Besar dari Stock' ;
                    //     return $data ;
                    // }

                    // if ($request->berat[$x] == 0) {
                    //     DB::rollBack() ;
                    //     $data['status'] =   400 ;
                    //     $data['msg']    =   'Pengambilan tidak boleh 0' ;
                    //     return $data ;
                    // }

                    // if ($request->qty[$x] == 0) {
                    //     DB::rollBack() ;
                    //     $data['status'] =   400 ;
                    //     $data['msg']    =   'Pengambilan tidak boleh 0' ;
                    //     return $data ;
                    // }
                    

                    $orderItem                          = OrderItem::find($request->order_item_id);
                    if ($orderItem) {
                        if ($storage->nama != $orderItem->nama_detail) {
                            DB::rollBack() ;
                            $data['status'] =   400 ;
                            $data['msg']    =   'Terjadi kesalahan, Item Order berbeda dengan fulfill' ;
                            return $data ;
                        }

                    } else {
                        DB::rollBack() ;
                        $data['status'] =   400 ;
                        $data['msg']    =   'Terjadi kesalahan, Item Order tidak ditemukan' ;
                        return $data ;
                    }

                    $orderBahanBaku                     =   new Bahanbaku;
                    $orderBahanBaku->chiller_out        =   $storage->id;
                    $orderBahanBaku->order_id           =   $request->order_id;
                    $orderBahanBaku->nama               =   $storage->productitems->nama;
                    $orderBahanBaku->proses_ambil       =   $proses_ambil;
                    $orderBahanBaku->order_item_id      =   $request->order_item_id;
                    $orderBahanBaku->bb_item            =   $request->qty[$x];
                    $orderBahanBaku->bb_berat           =   $request->berat[$x];
                    $orderBahanBaku->keranjang          =   $request->keranjang[$x];
                    $orderBahanBaku->status             =   1;
                    if (!$orderBahanBaku->save()) {
                        DB::rollBack() ;
                        $data['status'] =   400 ;
                        $data['msg']    =   'Terjadi kesalahan proses order' ;
                        return $data ;
                    }

                }elseif($proses_ambil=="sampingan"){

                    $chiller                        =   Chiller::find($request->x_code[$x]);

                    $sisaQtyChiller                 = Chiller::ambilsisachiller($chiller->id,'qty_item','qty','bb_item');
                    $sisaBeratChiller               = Chiller::ambilsisachiller($chiller->id,'berat_item','berat','bb_berat');
                    $convertSisaBerat               = number_format((float)$sisaBeratChiller, 2, '.', '');
                    
                    if ($request->qty[$x] > $sisaQtyChiller) {
                        DB::rollBack() ;
                        $data['status'] =   400;
                        $data['msg']    =   'bahan baku mungkin sudah digunakan regu lain';
                        return $data;
                        // return back()->with('status', 2)->with('message', 'Proses gagal, bahan baku mungkin sudah digunakan regu lain')->with('tabs', $request->type_input);
                    }

                    if ($request->berat[$x] > $convertSisaBerat) {
                        DB::rollBack() ;
                        $data['status'] =   400;
                        $data['msg']    =   'Pengambilan Lebih Besar dari Stock';
                        return $data;
                        // return back()->with('status', 2)->with('message', 'Proses gagal, bahan baku mungkin sudah digunakan regu lain')->with('tabs', $request->type_input);
                    }

                    // if ($request->berat[$x] > $chiller->stock_berat) {
                    //     DB::rollBack() ;
                    //     $data['status'] =   400 ;
                    //     $data['msg']    =   'Pengambilan Lebih Besar dari Stock' ;
                    //     return $data ;
                    // }

                    if ($request->berat[$x] == 0) {
                        DB::rollBack() ;
                        $data['status'] =   400 ;
                        $data['msg']    =   'Pengambilan tidak boleh 0' ;
                        return $data ;
                    }

                    // if ($request->qty[$x] == 0) {
                    //     DB::rollBack() ;
                    //     $data['status'] =   400 ;
                    //     $data['msg']    =   'Pengambilan tidak boleh 0' ;
                    //     return $data ;
                    // }

                    $orderItem                          = OrderItem::find($request->order_item_id);
                    if ($orderItem) {
                        if ($chiller->item_name != $orderItem->nama_detail) {
                            DB::rollBack() ;
                            $data['status'] =   400 ;
                            $data['msg']    =   'Terjadi kesalahan, Item Order berbeda dengan fulfill' ;
                            return $data ;
                        }

                    } else {
                        DB::rollBack() ;
                        $data['status'] =   400 ;
                        $data['msg']    =   'Terjadi kesalahan, Item Order tidak ditemukan' ;
                        return $data ;
                    }

                    $orderBahanBaku                     =   new Bahanbaku;
                    $orderBahanBaku->chiller_out        =   $chiller->id;
                    $orderBahanBaku->order_id           =   $request->order_id;
                    $orderBahanBaku->nama               =   $chiller->item_name;
                    $orderBahanBaku->proses_ambil       =   $proses_ambil;
                    $orderBahanBaku->order_item_id      =   $request->order_item_id;
                    $orderBahanBaku->bb_item            =   $request->qty[$x];
                    $orderBahanBaku->bb_berat           =   $request->berat[$x];
                    $orderBahanBaku->keranjang          =   $request->keranjang[$x];
                    $orderBahanBaku->status             =   1;
                    if (!$orderBahanBaku->save()) {
                        DB::rollBack() ;
                        $data['status'] =   400 ;
                        $data['msg']    =   'Terjadi kesalahan proses order' ;
                        return $data ;
                    }
                }

            }
        }

        DB::commit();

        $data['status'] =   200 ;
        $data['msg']    =   'Data telah diproses' ;
        return $data ;

    }

    public function fulfillItem(Request $request){

        return OrderItem::fulfillItem($request->order_item_id);

    }


    public function selesaikan(Request $request){
        DB::beginTransaction();

        $order_id   =   $request->order_id;
        $order      =   Order::find($order_id);

        // CEK BAHAN BAKU

        $cekBahanBaku = Bahanbaku::where('order_id', $order_id)->where('status', 1)->get();
        
        if (COUNT($cekBahanBaku) > 0) {
            $order_item = OrderItem::where('order_id', $order_id)->get();
            
            foreach($order_item as $oi):
                $multi_pemenuhan = Bahanbaku::where('order_item_id', $oi->id)->whereNull('netsuite_id')->where('deleted_at', NULL)->get();
                
                foreach ($multi_pemenuhan as $bahan) {
                    
                    if ($bahan->nama != $oi->nama_detail) {
                        DB::rollBack() ;
                        $data['status'] =   400 ;
                        $data['msg']    =   'Terjadi kesalahan, Item Order berbeda dengan fulfill' ;
                        return $data ;
                    }  
                }
                
                OrderItem::fulfillItem($oi->id);
            endforeach;
    
    
            $net_fulfill = Netsuite::item_fulfill_tambahan('orders', $request->order_id, 'itemfulfill', null, null);
    
            $order->status = 10;
            $order->save();
    
            DB::commit();
    
            $data['status'] =   200 ;
            $data['msg']    =   'Order berhasil difulfillment' ;
            return $data ;

        } else {

            DB::rollBack() ;
            $data['status'] =   400;
            $data['msg']    =   'Terjadi kesalahan proses order' ;
            return $data ;
        }

        
    }

    public function selesaikan_fulfillment(Request $request){
        $order_id   =   $request->order_id;
        $order      =   Order::find($order_id);

        if($order){

            if(count($order->daftar_order_full)==0){
                $data['status'] =   400 ;
                $data['msg']    =   'Data fulfillment masih kosong' ;
                return $data ;
            }

            foreach($order->daftar_order_full as $row){
                // dd($row);
                if ($row->order_item_bb != null) {
                    if ($row->order_item_bb->status == 1) {
                        $data['status'] =   400 ;
                        $data['msg']    =   'Data item belum tersimpan' ;
                        return $data ;
                    }
                }
            }

            $order->status = 10;
            $order->save();

            $net_fulfill = Netsuite::item_fulfill_sampingan('orders', $request->order_id, 'itemfulfill', null, null);

            foreach($order->daftar_order_full as $row){
                if($row->status==3){
                    $row->status = 2;
                    $row->save();
                }
            }

            $data['status'] =   200 ;
            $data['msg']    =   'Order berhasil difulfillment' ;
            return $data ;
        }

    }

    public function deleteAlokasi(Request $request){
        DB::beginTransaction();

        $pemenuhan              = Bahanbaku::find($request->id);

        if ($pemenuhan->proses_ambil == 'frozen') {
            $gudang             =   Product_gudang::find($pemenuhan->chiller_out);
            if($pemenuhan->status == '1'){
                $gudang->berat      =   $gudang->berat ;
                $gudang->qty        =   $gudang->qty ;
                $gudang->save();
            }else{
                $gudang->berat      =   $gudang->berat + $pemenuhan->bb_berat;
                $gudang->qty        =   $gudang->qty + $pemenuhan->bb_item;
                $gudang->save();
            }

        } else {

            $chiller                =   Chiller::find($pemenuhan->chiller_out);
            $chiller->stock_berat   =   $chiller->stock_berat + $pemenuhan->bb_berat;
            $chiller->stock_item    =   $chiller->stock_item + $pemenuhan->bb_item;
            $chiller->save();
        }

        $chil       =   Chiller::where('table_id', $pemenuhan->id)->where('table_name', 'order_bahanbaku')->first();

        if ($chil) {
            $chil->delete();
        }

        $pemenuhan->delete();

        $fulfill_item_id = OrderItem::find($pemenuhan->order_item_id);

        if($fulfill_item_id){

            $fulfill_item_id->fulfillment_qty       = OrderItem::recalculate_fulfill_qty($fulfill_item_id->id);
            $fulfill_item_id->fulfillment_berat     = OrderItem::recalculate_fulfill_berat($fulfill_item_id->id);
            $fulfill_item_id->status                = NULL;
            $fulfill_item_id->save();
        }
        
        DB::commit();

        try {
            Chiller::recalculate_chiller($chiller->id);
        } catch (\Throwable $th) {
            
        }
        
        try {
            Chiller::recalculate_chiller($chil->id);
        } catch (\Throwable $th) {
            
        }

        $data['status'] =   200;
        $data['msg']    =   'Data telah diproses';
        return $data;
    }

    public function storeprosesorder(Request $request)
    {
        // return $data['status']  =   400 ;
        // if (User::setIjin(7)) {
        $qty    =  json_decode(json_encode($request->qty, FALSE));
        $berat  =  json_decode(json_encode($request->berat, FALSE));
        $item   =  json_decode(json_encode($request->item, FALSE));
        $order  =  json_decode(json_encode($request->order, FALSE));
        $xcode  =  json_decode(json_encode($request->xcode, FALSE));

        $nama_gudang_expedisi = env('NET_SUBSIDIARY', 'CGL')." - Storage Expedisi";

        DB::beginTransaction();

        $total  =   0 ;
        $net    =   [] ;
        for ($x = 0; $x < COUNT($order); $x++) {
            if ($berat[$x] > 0) {

                if ($qty[$x] < 1) {
                    DB::rollBack() ;
                    $data['status'] =   400 ;
                    $data['msg']    =   'Qty tidak boleh kosong' ;
                    return $data ;
                }

                $total  +=  1 ;

                $chiler                     =   new Bahanbaku();
                $chiler->chiller_out        =   $item[$x];
                $chiler->order_id           =   $xcode[$x];
                $chiler->order_item_id      =   $order[$x];
                $chiler->bb_item            =   $qty[$x];
                $chiler->bb_berat           =   $berat[$x];
                $chiler->status             =   1;
                if (!$chiler->save()) {
                    DB::rollBack() ;
                    $data['status'] =   400;
                    $data['msg']    =   'Terjadi kesalahan proses order' ;
                    return $data ;
                }

                $bahan                      =   Bahanbaku::where('chiller_out', $item[$x])
                                                ->where('order_id', $xcode[$x])
                                                ->where('order_item_id', $order[$x])
                                                ->first();

                $cekchiller                 =   Chiller::find($bahan->chiller_out);


                if ($request->regu == 'Frozen') {
                    $decode                 =   json_decode(Chiller::find($bahan->orderitem->item_id)->label) ;

                    $abf                    =   new Abf;
                    $abf->table_name        =   'order_bahanbaku';
                    $abf->table_id          =   $bahan->id;
                    $abf->asal_tujuan       =   'orderproduksi';
                    $abf->tanggal_masuk     =   date('Y-m-d');
                    $abf->item_id           =   $bahan->orderitem->item_id;
                    $abf->item_id_lama      =   $bahan->orderitem->item_id;
                    $abf->item_name         =   $bahan->orderitem->nama_detail;
                    $abf->packaging         =   $decode->plastik->jenis ?? NULL ;
                    $abf->qty_awal          =   $qty[$x];
                    $abf->berat_awal        =   $berat[$x];
                    $abf->qty_item          =   $qty[$x];
                    $abf->berat_item        =   $berat[$x];
                    $abf->jenis             =   'masuk';
                    $abf->type              =   'hasil-produksi';
                    $abf->status            =   1;
                    if (!$abf->save()) {
                        DB::rollBack() ;
                        $data['status'] =   400;
                        $data['msg']    =   'Terjadi kesalahan proses order' ;
                        return $data ;
                    }

                    $label  =   "ti_order_abf" ;
                    $to     =   "123" ;
                } else {

                    $chiler                     =   new Chiller();
                    $chiler->table_name         =   'order_bahanbaku';
                    $chiler->table_id           =   $bahan->id;
                    $chiler->asal_tujuan        =   'orderproduksi';
                    $chiler->item_id            =   $bahan->orderitem->item_id;
                    $chiler->item_name          =   $bahan->orderitem->nama_detail;
                    $chiler->qty_item           =   $qty[$x];
                    $chiler->berat_item         =   $berat[$x];
                    $chiler->jenis              =   'masuk';
                    $chiler->type               =   'hasil-produksi';
                    $chiler->kategori           =   $cekchiller->kategori;
                    $chiler->tanggal_potong     =   Carbon::now();
                    $chiler->tanggal_produksi   =   Carbon::now();
                    $chiler->status             =   4;
                    $chiler->save();
                    if (!$chiler->save()) {
                        DB::rollBack() ;
                        $data['status'] =   400;
                        $data['msg']    =   'Terjadi kesalahan proses order' ;
                        return $data ;
                    }

                    $cekchiller->stock_berat    =   $cekchiller->stock_berat - $chiler->berat_item ;

                    $cekchiller->stock_item     =   $cekchiller->stock_item - $chiler->qty_item ;

                    if (!$cekchiller->save()) {
                        DB::rollBack() ;
                        $data['status'] =   400;
                        $data['msg']    =   'Terjadi kesalahan proses order' ;
                        return $data ;
                    }



                    $label  =   "ti_fg_ekspedisi" ;
                    $to     =   Gudang::gudang_netid($nama_gudang_expedisi) ;
                }

                $so                         =   Order::find($xcode[$x]);
                $so->status                 =   5;
                if (!$so->save()) {
                    DB::rollBack() ;
                    $data['status'] =   400;
                    $data['msg']    =   'Terjadi kesalahan proses order' ;
                    return $data ;
                }

                $oritem                     =   OrderItem::find($order[$x]);
                $oritem->fulfillment_berat  =   $berat[$x];
                $oritem->fulfillment_qty    =   $qty[$x];
                $oritem->status             =   1;
                $order = Order::find($oritem->order_id);

                if (!$oritem->save()) {
                    DB::rollBack() ;
                    $data['status'] =   400;
                    $data['msg']    =   'Terjadi kesalahan proses order' ;
                    return $data ;
                }

                $net[]  =   [
                    'nama_tabel'    =>  "order_items" ,
                    "id_tabel"      =>  $oritem->id ,
                    "label"         =>  $label ,
                    "document_code" =>  $order->no_so ?? $order[$x],
                    "id_location"   =>  Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL').' - Chiller Finished Good') ,
                    "location"      =>  env('NET_SUBSIDIARY', 'CGL')." - Chiller Finished Good" ,
                    "from"          =>  Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL').' - Chiller Finished Good') ,
                    "to"            =>  $to ,
                    "transfer"      =>  [
                        [
                            "internal_id_item"  =>  (string)$oritem->item->netsuite_internal_id ,
                            "item"              =>  (string)$oritem->item->sku ,
                            "qty_to_transfer"   =>  (string)$berat[$x]
                        ]
                    ]
                ] ;

            }
        }

        if ($total == 0) {
            DB::rollBack() ;
            $data['status'] =   400 ;
            $data['msg']    =   'Order Kosong' ;
            return $data ;
        }

        DB::commit();

        for ($x=0; $x < COUNT($net); $x++) {
            Netsuite::transfer_inventory_doc($net[$x]['nama_tabel'], $net[$x]['id_tabel'], $net[$x]['label'], $net[$x]['id_location'], $net[$x]['location'], $net[$x]['from'], $net[$x]['to'], $net[$x]['transfer'], NULL, date('Y-m-d'),$net[$x]['document_code']) ;
        }

    }

    public function batalorder(Request $request)
    {
        $order   =   Order::find($request->id);

        if ($request->key == 'close') {
            // Close Order
            $order->status = $order->status == '0' ? NULL : '0' ;
            $data['msg']    =   'Berhasil Close Order' ;
        } else {
            // Batalkan Fulfill
            $order->status = 6;
            $data['msg']    =   'Berhasil Batalkan Fulfill' ;
            return $data ;
        }

        $data['status'] =   400;
        $order->save();
        return $data;

    }

    public function siapKirimExport(Request $request){

        $awal           = $request->awal ?? date('Y-m-d');
        $akhir          = $request->akhir ?? date('Y-m-d');
        $keterangan     = $request->keterangan ?? "";

        header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=siapkirim-" . $awal . "-" .$akhir . ".csv");
            $fp = fopen('php://output', 'w');
            fputcsv($fp, ["sep=,"]);

            $data = array(
                "No",
                "No SO",
                "No DO",
                "Nama",
                "Channel",
                "Tanggal SO",
                "Tanggal Kirim",
                "Keterangan Header",
                "SKU",
                "Item",
                "Part",
                "Bumbu",
                "Memo",
                "Order Item",
                "Order Berat",
                "Fulfillment Item",
                "Fulfillment Berat",
                "Fresh/Frozen",
                "Tidak Terkirim",
                "Tidak Terkirim Item",
                "Tidak Terkirim Berat"
            );
            fputcsv($fp, $data);

            $order = Order::whereBetween('tanggal_kirim', [$awal." 00:00:00", $akhir." 23:59:59"])->whereNotIn('sales_channel', ["By Product - Paket", "By Product - Retail"])->get();

            $urut = 0;
            foreach($order as $no => $o):

                foreach(OrderItem::where('order_id', $o->id)->get() as $sub_no => $item):

                    $jenis = "FRESH";

                    if (str_contains($item->nama_detail, 'FROZEN')) {
                        $jenis = "FROZEN";
                    }

                    $tidak_terkirim_qty     = ($item->qty - $item->fulfillment_qty)*(-1);
                    $tidak_terkirim_berat   = ($item->berat - $item->fulfillment_berat)*(-1);

                    if($keterangan=="tidak-terkirim"){

                        if($item->tidak_terkirim == "1"){
                            $data = array(
                                ($urut + 1),
                                $o->no_so,
                                $o->no_do,
                                $o->nama,
                                $o->sales_channel,
                                $o->tanggal_so,
                                $o->tanggal_kirim,
                                $o->keterangan,
                                $item->sku,
                                $item->nama_detail,
                                $item->part,
                                $item->bumbu,
                                $item->memo,
                                str_replace(".", ",", $item->qty),
                                str_replace(".", ",", $item->berat),
                                str_replace(".", ",", $item->fulfillment_qty),
                                str_replace(".", ",", $item->fulfillment_berat),
                                $jenis,
                                $item->tidak_terkirim_catatan,
                                str_replace(".", ",", $tidak_terkirim_qty),
                                str_replace(".", ",", $tidak_terkirim_berat)

                            );
                            fputcsv($fp, $data);
                            $urut++;
                        }

                    }else{
                        $data = array(
                            ($urut + 1),
                            $o->no_so,
                            $o->no_do,
                            $o->nama,
                            $o->sales_channel,
                            $o->tanggal_so,
                            $o->tanggal_kirim,
                            $o->keterangan,
                            $item->sku,
                            $item->nama_detail,
                            $item->part,
                            $item->bumbu,
                            $item->memo,
                            str_replace(".", ",", $item->qty),
                            str_replace(".", ",", $item->berat),
                            str_replace(".", ",", $item->fulfillment_qty),
                            str_replace(".", ",", $item->fulfillment_berat),
                            $jenis,
                            "",
                            str_replace(".", ",", $tidak_terkirim_qty),
                            str_replace(".", ",", $tidak_terkirim_berat)
                        );
                        fputcsv($fp, $data);
                        $urut++;
                    }
                endforeach;

            endforeach;

            return "";
    }


    public function simpanketerangan(Request $request)
    {

        $order_item = OrderItem::where('order_id', $request->order_id)
            ->where('item_id', $request->item_id);
        $change_status = $order_item->update([
            'tidak_terkirim' => 1,
            'tidak_terkirim_catatan' => $request->keterangan
        ]);

        // if ($change_status) {
        //     $data['status'] =   200;
        //     $data['msg']    =   'Berhasil membuat keterangan';
        //     return $data;
        // }
        // return $change_status;
        return back()->with('status', 1)->with('message', 'Data Tersimpan');
    }

}
