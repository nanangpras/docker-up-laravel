<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Abf;
use App\Models\AppKey;
use App\Models\Bom;
use App\Models\Category;
use App\Models\Chiller;
use App\Models\Customer;
use App\Models\Freestock;
use App\Models\FreestockList;
use App\Models\FreestockTemp;
use App\Models\Gudang;
use App\Models\Item;
use App\Models\Marketing;
use App\Models\Netsuite;
use App\Models\Openbalance as ModelsOpenbalance;
use App\Models\Product_gudang;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Excel;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Session;

class OpenBalance extends Controller
{
    public function index(Request $request)
    {
        if ($request->key == 'input') {
            $item       =   Item::select('items.*')
                            ->join('category', 'category.id', '=', 'items.category_id')->where(function($query) {
                                $query->orWhere('category_id', '<', '21');
                                $query->orWhere('category.slug', 'like', 'ags');
                                $query->orWhere('items.nama', 'like', '%AY - S%');
                                $query->orWhere('category.slug', 'like', 'ags%');
                            })
                            ->get();
            $plastik    =   Item::where('category_id', 25)->where('subsidiary', env('NET_SUBSIDIARY', 'EBA'))->get();
            $warehouse  =   Gudang::where('kategori', 'warehouse')->where('status', 1)->get();
            return view('admin.pages.open_balance.input', compact('item', 'warehouse', 'plastik'));
        } else {
            $chiller    =   Gudang::where('kategori', 'Production')
                            ->where("code", "LIKE", "%chiller%")
                            ->where('status', 1)
                            ->where('subsidiary', env('NET_SUBSIDIARY', 'CGL'))
                            ->get();

            $warehouse  =   Gudang::where('kategori', 'warehouse')
                            ->where('status', 1)
                            ->where('subsidiary', env('NET_SUBSIDIARY', 'CGL'))
                            ->where("code", "NOT LIKE", "%abf%")
                            ->get();

            return view('admin.pages.open_balance.index', compact('chiller', 'warehouse'));
        }
    }

    public function data(Request $request){
        $tanggalawal    = $request->tanggalawal ?? date('Y-m-d');
        $tanggalakhir   = $request->tanggalakhir ?? date('Y-m-d');
        $opbal = ModelsOpenbalance::whereBetween('tanggal', [$tanggalawal, $tanggalakhir])->get();
        return view('admin.pages.open_balance.data', compact('opbal', 'tanggalawal', 'tanggalakhir'));
    }

    public function store(Request $request)
    {

        $item   =   Item::where('id', $request->item)->first() ;

        if (!$item) {
            $result['status']   =   400 ;
            $result['msg']      =   "Item tidak ditemukan" ;
            return $result ;
        }

        if (!$request->tanggal) {
            $result['status']   =   400;
            $result['msg']      =   "Pilih tanggal";
            return $result;
        }

        if (!$request->tipe_item) {
            $result['status']   =   400;
            $result['msg']      =   "Pilih tipe item";
            return $result;
        }

        if ($request->berat < 1) {
            $result['status']   =   400;
            $result['msg']      =   "Berat wajib diisikan";
            return $result;
        }



        if (($request->gudang == "cold1") || ($request->gudang == "cold2") || ($request->gudang == "cold3") || ($request->gudang == "cold4")) {
            if (!$request->pallete) {
                $result['status']   =   400;
                $result['msg']      =   "Pallete wajib diisikan";
                return $result;
            }

            $tujuan =   Gudang::where('kategori', 'warehouse')
                        ->where('id', $request->tujuan)
                        ->where('status', 1)
                        ->first() ;

            if (!$tujuan) {
                $result['status']   =   400;
                $result['msg']      =   "Tujuan tidak ditemukan";
                return $result;
            }

            $packaging  =   Item::where('category_id', 25)
                            ->where('id', $request->packaging)
                            ->first();

            // if (!$packaging) {
            //     $result['status']   =   400;
            //     $result['msg']      =   "Packaging tidak ditemukan";
            //     return $result;
            // }

            if (!$request->expired) {
                $result['status']   =   400;
                $result['msg']      =   "Pilih expired";
                return $result;
            }

            if (!$request->stock) {
                $result['status']   =   400;
                $result['msg']      =   "Pilih stock";
                return $result;
            }
        }

        DB::beginTransaction();


        $open               =   new ModelsOpenbalance ;
        $open->user_id      =   Auth::user()->id ;
        $open->gudang       =   $request->gudang ;
        $open->item_id      =   $item->id ;
        $open->tipe_item    =   $request->tipe_item ;
        $open->tanggal      =   $request->tanggal ;
        $open->qty          =   $request->qty ;
        $open->berat        =   $request->berat ;
        if (!$open->save()) {
            DB::rollBack() ;
            $result['status']   =   400;
            $result['msg']      =   "Proses gagal";
            return $result;
        }

        if ($request->gudang == 'chiller') {
            $chiler                     =   new Chiller ;
            $chiler->table_name         =   'openbalance';
            $chiler->table_id           =   $open->id;
            $chiler->asal_tujuan        =   'open_balance';
            $chiler->type               =   $open->tipe_item;
            $chiler->item_id            =   $item->id;
            $chiler->item_name          =   $item->nama;
            $chiler->qty_item           =   $open->qty;
            $chiler->tanggal_potong     =   $open->tanggal;
            $chiler->berat_item         =   $open->berat;
            $chiler->tanggal_produksi   =   $open->tanggal;
            $chiler->stock_item         =   $open->qty;
            $chiler->stock_berat        =   $open->berat;
            $chiler->status             =   2;
            $chiler->key                =   AppKey::generate();
            $chiler->jenis              =   'masuk';
            $chiler->label              =   $request->label;
            $chiler->sub_item           =   $request->sub_item;
            $chiler->parting            =   $request->parting;
            if (!$chiler->save()) {
                DB::rollBack();
                $result['status']   =   400;
                $result['msg']      =   "Proses gagal";
                return $result;
            }
        } else
        if ($request->gudang == 'abf') {
            $abf                    =   new Abf;
            $abf->table_name        =   'openbalance';
            $abf->table_id          =   $open->id;
            $abf->asal_tujuan       =   'open_balance';
            // $abf->tanggal_masuk     =   date('Y-m-d');
            $abf->tanggal_masuk     =   $request->tanggal ;
            $abf->item_id           =   $item->id;
            $abf->item_id_lama      =   $item->id;
            $abf->item_name         =   $item->nama;
            $abf->jenis             =   'masuk';
            $abf->type              =   $open->tipe_item;
            $abf->qty_awal          =    $open->qty;
            $abf->berat_awal        =    $open->berat;
            $abf->qty_item          =    $open->qty;
            $abf->berat_item        =    $open->berat;
            // $abf->label             =    $request->label_abf;
            $abf->status            =   1;
            if (!$abf->save()) {
                DB::rollBack();
                $result['status']   =   400;
                $result['msg']      =   "Proses gagal";
                return $result;
            }
        } else
        if (($request->gudang == "cold1") || ($request->gudang == "cold2") || ($request->gudang == "cold3") || ($request->gudang == "cold4")) {
            // $abf                    =   new Abf;
            // $abf->table_name        =   'openbalance';
            // $abf->table_id          =   $open->id;
            // $abf->asal_tujuan       =   'open_balance';
            // $abf->tanggal_masuk     =   $request->tanggal;
            // $abf->item_id           =   $item->id;
            // $abf->item_id_lama      =   $item->id;
            // $abf->item_name         =   $item->nama;
            // $abf->jenis             =   'masuk';
            // $abf->type              =   $open->tipe_item;
            // $abf->qty_awal          =    $open->qty;
            // $abf->berat_awal        =    $open->berat;
            // $abf->qty_item          =    $open->qty;
            // $abf->berat_item        =    $open->berat;

            // $abf->pallete           =   $request->pallete;
            // $abf->tujuan            =   $tujuan->id;
            // $abf->packaging         =   $packaging->nama ?? NULL;
            // $abf->expired           =   $request->expired;
            // $abf->jenis_stock       =   $request->stock;
            // $abf->label             =   $request->label_cs;
            // $abf->status            =   2;
            // if (!$abf->save()) {
            //     DB::rollBack();
            //     $result['status']   =   400;
            //     $result['msg']      =   "Proses gagal";
            //     return $result;
            // }

            $gudang                     =   new Product_gudang;
            $gudang->table_name         =   'open_balance';
            $gudang->table_id           =   $open->id;
            $gudang->product_id         =   $item->id;
            $gudang->nama               =   $item->nama;
            $gudang->qty_awal           =   $open->qty;
            $gudang->berat_awal         =   $open->berat;
            $gudang->qty                =   $open->qty;
            $gudang->berat              =   $open->berat;
            $gudang->packaging          =   $packaging->nama ?? NULL;
            $gudang->palete             =   $request->pallete;
            $gudang->expired            =   $request->expired;
            $gudang->production_date    =   $request->tanggal;
            $gudang->sub_item           =   $request->sub_item;
            $gudang->type               =   $open->tipe_item;
            $gudang->gudang_id          =   $request->tujuan;
            $gudang->stock_type         =   $request->stock;
            $gudang->label              =   $request->label_cs;
            $gudang->jenis_trans        =   'masuk';
            $gudang->status             =   2;
            if (!$gudang->save()) {
                DB::rollBack();
                $result['status']   =   400;
                $result['msg']      =   "Proses gagal";
                return $result;
            }
        }

        DB::commit();
    }

    public function update(Request $request)
    {
        // dd($request->all());
        $open = ModelsOpenbalance::find($request->idopen);
        
        if ($open) {
            DB::beginTransaction();
            $open->qty      = $request->qty;
            $open->berat    = $request->berat;
            $open->save();
            if ($open->gudang == "chiller") {
                $chiler_open             = Chiller::where('table_name', 'openbalance')->where('table_id', $open->id)->where('status',2)->first();
                $chiler_open->stock_item = $request->qty;
                $chiler_open->stock_berat= $request->berat;
                // $chiler_open->save();
                if (!$chiler_open->save()) {
                    DB::rollBack();
                    $result['status']   =   400;
                    $result['msg']      =   "Proses gagal";
                    return $result;
                }
            } else
            if ($open->gudang == "abf") {
                $abf_open               = Abf::where('table_name', 'openbalance')->where('table_id', $open->id)->where('status',1)->first();
                $abf_open->qty_awal     = $request->qty;
                $abf_open->berat_awal   = $request->berat;
                $abf_open->qty_item     = $request->qty;
                $abf_open->berat_item   = $request->berat;
                // $abf_open->save();
                if (!$abf_open->save()) {
                    DB::rollBack();
                    $result['status']   =   400;
                    $result['msg']      =   "Proses gagal";
                    return $result;
                }
            } else if(($open->gudang == "cold1") || ($open->gudang == "cold2") || ($open->gudang == "cold3") || ($open->gudang == "cold4")) {
                // $abfopen                = Abf::where('table_name', 'openbalance')->where('table_id', $open->id)->where('status',2)->first();
                // $abfopen->qty_awal      = $request->qty;
                // $abfopen->berat_awal    = $request->berat;
                // $abfopen->qty_item      = $request->qty;
                // $abfopen->berat_item    = $request->berat;
                // // $abfopen->save();
                // if (!$abfopen->save()) {
                //     DB::rollBack();
                //     $result['status']   =   400;
                //     $result['msg']      =   "Proses gagal";
                //     return $result;
                // }

                $gudang                 = Product_gudang::where('table_name', 'abf')->where('table_id', $open->id)->where('status',2)->first();
                $gudang->qty_awal       = $request->qty;
                $gudang->berat_awal     = $request->berat;
                $gudang->qty            = $request->qty;
                $gudang->berat          = $request->berat;
                // $gudang->save();
                if (!$gudang->save()) {
                    DB::rollBack();
                    $result['status']   =   400;
                    $result['msg']      =   "Proses gagal";
                    return $result;
                }

            }
            
            DB::commit();
            return back()->with('status', 1)->with('message', 'Ubah open balance berahsil');
        }
        return back()->with('status', 2)->with('message', 'Proses Update Gagal');
        
        
    }

    public function destroy(Request $request,$id)
    {
        $open = ModelsOpenbalance::find($id);
        if ($open) {
            DB::beginTransaction();
            $open->delete();
            if ($open->gudang == "chiller") {
                $chiler_open             = Chiller::where('table_name', 'openbalance')->where('table_id', $open->id)->where('status',2)->first();
                $chiler_open->delete();
            } else
            if ($open->gudang == "abf") {
                $abf_open               = Abf::where('table_name', 'openbalance')->where('table_id', $open->id)->where('status',1)->first();
                $abf_open->delete();
            } else if(($open->gudang == "cold1") || ($open->gudang == "cold2") || ($open->gudang == "cold3") || ($open->gudang == "cold4")) {
                $abfopen                = Abf::where('table_name', 'openbalance')->where('table_id', $open->id)->where('status',2)->first();
                $abfopen->delete();

                $gudang                 = Product_gudang::where('table_name', 'abf')->where('table_id', $abfopen->id)->where('status',2)->first();
                $gudang->delete();
            }
            DB::commit();
            return back()->with('status', 1)->with('message', 'Hapus open balance berahsil');
        }
        return back()->with('status', 2)->with('message', 'Proses Hapus Gagal');
    }

    public function import(Request $request)
    {
        if ($request->hasFile('file')) {

            $path       =   $request->file('file') ;
            try {
                $prod_import =  Excel::toArray([], $path);
            } catch (\Throwable $th) {
                return "Format Tidak didukung, ulangi lagi dengan format excel";
            }

            try {

            // return $prod_import[0];
            foreach ($prod_import[0] as $urut => $row) {
                // return $row;
                if ($urut != 0) {

                    try {
                        $produk      =   Item::where('nama', substr($row[1], 1))->first();
                        
                        if($produk){
                            $exp         =   str_replace('', ' FROZEN', $produk->nama);
                            $produkexp   =   Item::where('nama', $exp)->first();


                            if($produkexp){
                                $prodgudang  =   Gudang::where('code', $row[0])->first();
                            }
                        }
                        
                        if ($produk && $produkexp ) {

                            $open               =   new ModelsOpenbalance;
                            $open->user_id      =   Auth::user()->id;
                            $open->gudang       =   'chiller';
                            $open->item_id      =   $produk->id;
                            $open->tipe_item    =   'hasil-produksi';
                            $open->tanggal      =   date('Y-m-d');
                            $open->qty          =   $row[2];
                            $open->berat        =   (float)$row[4];
                            if (!$open->save()) {
                                DB::rollBack();
                                return back()->with('status', 2)->with('message', 'Proses gagal (PKG-12)');
                            }

                            $abf                    =   new Abf;
                            $abf->table_name        =   'openbalance';
                            $abf->table_id          =   $open->id;
                            $abf->asal_tujuan       =   'open_balance';
                            $abf->tanggal_masuk     =   date('Y-m-d');
                            $abf->item_id           =   $produk->id;
                            $abf->item_id_lama      =   $produkexp->id;
                            $abf->item_name         =   $produk->nama;
                            $abf->jenis             =   'masuk';
                            $abf->type              =   $open->tipe_item;
                            $abf->tujuan            =   $prodgudang->id;
                            $abf->qty_awal          =   $open->qty;
                            $abf->berat_awal        =   $open->berat;
                            $abf->qty_item          =   $open->qty;
                            $abf->berat_item        =   $open->berat;

                            $abf->status            =   2;
                            if (!$abf->save()) {
                                DB::rollBack();
                                return back()->with('status', 2)->with('message', 'Proses gagal (BFF-32)');
                            }


                            $gudang                     =   new Product_gudang;
                            $gudang->table_name         =   'abf';
                            $gudang->table_id           =   $abf->id;
                            $gudang->sub_item           =   $row[10];
                            $gudang->product_id         =   $produk->id;
                            $gudang->nama               =   $produk->nama;
                            $gudang->qty_awal           =   (float)$row[2];
                            $gudang->berat_awal         =   (float)$row[3];
                            $gudang->qty                =   (float)$row[2];
                            $gudang->berat              =   (float)$row[3];
                            $gudang->palete             =   $row[6];
                            $gudang->gudang_id          =   $prodgudang->id;
                            $gudang->expired            =   NULL;
                            $gudang->production_date    =   date('Y-m-d');
                            $gudang->type               =   "freestock";
                            $gudang->stock_type         =   $row[9];
                            $gudang->jenis_trans        =   'masuk';
                            $gudang->status             =   2;

                            $packaging  =   Item::where('nama', $row[7])->first() ;

                            if ($packaging) {
                                $gudang->packaging          =   $packaging->nama;
                            }else{
                                $gudang->packaging          = $row[10];
                            }

                            $storage = Gudang::where('code', $row[0])->first();
                            if ($storage) {
                                $gudang->gudang_id          =   $storage->id;
                            }else{
                                $gudang->gudang_id          = 7;
                            }

                            if (!$gudang->save()) {
                                DB::rollBack();
                                return back()->with('status', 2)->with('message', 'Proses gagal (GND-10)');
                            }

                        }else{
                            return back()->with('status', 2)->with('message', 'Proses gagal (GND-11)');
                        }

                    }catch (\Throwable $th) {
                        DB::rollBack();
                        return back()->with('status', 2)->with('message', $th->getMessage());
                    }
                }

            }

                //code...
            } catch (\Throwable $th) {
                //throw $th;

                return back()->with('status', 2)->with('message', $th->getMessage());
            }

        }

    }

    public function upload_stock_cs(Request $request){

        DB::beginTransaction();

        if ($request->hasFile('file')) {

            $path       =   $request->file('file') ;
            try {
                $prod_import =  Excel::toArray([], $path);
            } catch (\Throwable $th) {
                return "Format Tidak didukung, ulangi lagi dengan format excel";
            }

            try {

            // return $prod_import[0];
            foreach ($prod_import[0] as $urut => $row) {
                if ($urut > 0) {

                    try {
                        $produk      =   Item::where('nama', $row[4])->first();

                        // return $row[4];

                        $tanggal     = date('Y-m-d', strtotime($row[0]));
                        
                        if($produk){
                            $produkexp   =   Item::item_frozen_to_fresh($produk->id, $produk->nama);
                            // return $produkexp;
                            if($produkexp){
                        
                                $gdg = $row[1];
                                $code = env('NET_SUBSIDIARY', 'CGL')." - Cold Storage 1";
                                if($gdg=="MANIS 1" || $gdg=="MANIS 2"){
                                    $code = env('NET_SUBSIDIARY', 'CGL')." - Manis";
                                }
                                if($gdg=="1"){
                                    $code = env('NET_SUBSIDIARY', 'CGL')." - Cold Storage 1";
                                }
                                if($gdg=="2"){
                                    $code = env('NET_SUBSIDIARY', 'CGL')." - Cold Storage 2";
                                }
                                if($gdg=="3"){
                                    $code = env('NET_SUBSIDIARY', 'CGL')." - Cold Storage 3";
                                }
                                if($gdg=="4"){
                                    $code = env('NET_SUBSIDIARY', 'CGL')." - Cold Storage 4";
                                }
                                $prodgudang = Gudang::where('code', $code)->first();

                            }
                        }
                        
                        if ($produk && $produkexp ) {

                            $qty    = (integer)str_replace(",", ".", str_replace(".", "", $row[2])) ?? 0;
                            $berat  = (float)str_replace(",", ".", str_replace(".", "", $row[3])) ?? 0;

                            
                            if($berat>0){
                                    
                                $open               =   new ModelsOpenbalance;
                                $open->user_id      =   Auth::user()->id;
                                $open->gudang       =   'chiller';
                                $open->item_id      =   $produk->id;
                                $open->tipe_item    =   'hasil-produksi';
                                $open->tanggal      =   $tanggal;
                                $open->qty          =   $qty;
                                $open->berat        =   $berat;
                                if (!$open->save()) {
                                    DB::rollBack();
                                    return back()->with('status', 2)->with('message', 'Proses gagal (PKG-12)');
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
                                $abf->tujuan            =   $prodgudang->id;
                                $abf->qty_awal          =   $open->qty;
                                $abf->berat_awal        =   $open->berat;
                                $abf->qty_item          =   $open->qty;
                                $abf->berat_item        =   $open->berat;

                                $abf->status            =   2;
                                if (!$abf->save()) {
                                    DB::rollBack();
                                    return back()->with('status', 2)->with('message', 'Proses gagal (BFF-32)');
                                }


                                $gudang                     =   new Product_gudang;
                                $gudang->table_name         =   'abf';
                                $gudang->table_id           =   $abf->id;
                                $gudang->nama               =   $produk->nama;
                                $gudang->sub_item           =   $row[7] ?? NULL;
                                $gudang->product_id         =   $produk->id;
                                $gudang->qty_awal           =   $open->qty;
                                $gudang->berat_awal         =   $open->berat;
                                $gudang->qty                =   $open->qty;
                                $gudang->berat              =   $open->berat;
                                $gudang->label              =   $row[6];
                                $gudang->parting            =   preg_replace('/[^0-9]/', '', $row[5]) ?? NULL;
                                $gudang->plastik_group      =   Item::plastik_group($row[6]);
                                $gudang->palete             =   NULL;
                                $gudang->gudang_id          =   $prodgudang->id;
                                $gudang->expired            =   NULL;
                                $gudang->production_date    =   $tanggal;

                                if($row[7]=="FREE"){
                                    $gudang->stock_type         =   "freestock";
                                }else{
                                    $gudang->stock_type         =   "booking";
                                }

                                $gudang->type               =   "openbalance";

                                $gudang->jenis_trans        =   'masuk';
                                $gudang->status             =   2;

                                $packaging  =   Item::where('nama', $row[6])->first() ;

                                if ($packaging) {
                                    $gudang->packaging          =   $packaging->nama;
                                }else{
                                    $gudang->packaging          = $row[6];
                                }

                                $storage = Gudang::where('code', $code)->first();
                                if ($storage) {
                                    $gudang->gudang_id          =   $storage->id;
                                }else{
                                    $gudang->gudang_id          = 140;
                                }

                                if (!$gudang->save()) {
                                    DB::rollBack();
                                    return back()->with('status', 2)->with('message', 'Proses gagal (GND-10)');
                                }

                            }

                        }else{
                            // DB::rollBack();
                            // return back()->with('status', 2)->with('message', 'Proses gagal (GND-11)');
                        }

                    }catch (\Throwable $th) {
                        DB::rollBack();
                        return back()->with('status', 2)->with('message', $th->getMessage());
                    }
                }

            }

            DB::commit();
            return back()->with('status', 1)->with('message', 'Proses Selesai');

                //code...
            } catch (\Throwable $th) {
                //throw $th;
                DB::rollBack();
                return back()->with('status', 2)->with('message', $th->getMessage());
            }

        }

    }

    public function upload_stock_opname(Request $request){
        DB::beginTransaction();

        if ($request->hasFile('file')) {

            $path       =   $request->file('file') ;
            try {
                $ImportOB =  Excel::toArray([], $path);
            } catch (\Throwable $th) {
                return "Format Tidak didukung, ulangi lagi dengan format excel";
            }

            $count  = count($ImportOB[0]);
            try {

                if($count > 3 ){

                    $rebase_array   = [];
                    foreach ($ImportOB[0] as $urut => $row) {
                        if($urut >= 3 && $row[0] !== NULL){
                            $rebase_array[]     = $row;
                            $EXCEL_DATE         = $row[1];
                            $UNIX_DATE          = ($EXCEL_DATE - 25569) * 86400;
                            $newDate            = gmdate('Y-m-d', $UNIX_DATE);
                            try {
                                $produk         =   Item::where('nama', $row[3])->first();
                                // dd($row[15]);
                                // if($produk){
                                    if($row[14] == '-' || $row[14] == NULL){
                                        $qty = 0;
                                    }else{
                                        $qty    = (integer)str_replace(",", ".", str_replace(".", "", $row[14])) ?? 0;
                                    }
                                    if($row[15] == '-' || $row[15] == NULL){
                                        $berat = 0;
                                    }else{
                                        $berat  = (float)str_replace(".", ".", str_replace(",", "", $row[15])) ?? 0;
                                    }
                                    if($row[16] == '-' || $row[16] == NULL){
                                        $karung = 0;
                                    }else{
                                        $karung  = (float)str_replace(",", ".", str_replace(".", "", $row[16])) ?? 0;
                                    }
                                    // $gdg    = $row[13];

                                    // if(Session::get('subsidiary') == 'EBA'){
                                    //     if($gdg == '' || $gdg == Null){
                                    //         $code = '';
                                    //         // $code = 'EBA - Cold Storage';
                                    //     }else
                                    //     if($gdg == 'CS 1'){
                                    //         $code = 'EBA - Cold Storage 1';
                                    //     }
                                    //     else if($gdg == 'CS 2'){
                                    //         $code = 'EBA - Cold Storage 2';
                                    //     }
                                    //     else if($gdg == 'CS 3'){
                                    //         $code = 'EBA - Cold Storage 3';
                                    //     }
                                    //     else if($gdg == 'CS 4'){
                                    //         $code = 'EBA - Cold Storage 4';
                                    //     }
                                    //     else if($gdg == 'CS 5'){
                                    //         $code = 'EBA - Cold Storage 5';
                                    //     }
                                    //     else if($gdg == 'CS 6'){
                                    //         $code = 'EBA - Cold Storage 6';
                                    //     }
                                    //     else if($gdg == 'CS 7'){
                                    //         $code = 'EBA - Cold Storage 7';
                                    //     }
                                    // }

                                    // if(Session::get('subsidiary') == 'CGL'){
                                    //     if($gdg == '' || $gdg == Null){
                                    //         $code = 'CGL - Cold Storage';
                                    //         $code = '';
                                    //     }else
                                    //     if($gdg == 'CS 1'){
                                    //         $code = 'CGL - Cold Storage 1';
                                    //     }
                                    //     else if($gdg == 'CS 2'){
                                    //         $code = 'CGL - Cold Storage 2';
                                    //     }
                                    //     else if($gdg == 'CS 3'){
                                    //         $code = 'CGL - Cold Storage 3';
                                    //     }
                                    //     else if($gdg == 'CS 4'){
                                    //         $code = 'CGL - Cold Storage 4';
                                    //     }
                                    //     else if($gdg == 'CS 5'){
                                    //         $code = 'CGL - Cold Storage 5';
                                    //     }
                                    //     else if($gdg == 'CS 6'){
                                    //         $code = 'CGL - Cold Storage 6';
                                    //     }
                                    //     else if($gdg == 'CS 7'){
                                    //         $code = 'CGL - Cold Storage 7';
                                    //     }
                                    // }
                                    
                                    // if($berat > 0){
                                        
                                        if($row[9] != "" || $row[9] != NULL){
                                            $cek_customer               = Customer::where('nama','LIKE','%'.$row[9].'%')->get()->count();
                                        }else{
                                            $cek_customer               = 0;
                                        }
                                        
                                        if($cek_customer > 0){
                                            $customer               = Customer::where('nama','LIKE','%'.$row[9].'%')->where('parent_id',NULL)->first()->id ?? NULL;
                                        }else{
                                            $customer               = NULL;
                                        }

                                        $gradeItem                  = $row[8] ?? NULL;
                                        $gudang                     = new Product_gudang;
                                        $gudang->table_name         = 'open_balance';
                                        $gudang->table_id           = NULL;
                                        $gudang->nama               = $produk->nama ?? $row[3];
                                        $gudang->sub_item           = $row[6] ?? NULL;
                                        if ($gradeItem != NULL) {
                                            $gudang->grade_item         = strtolower($gradeItem) == 'grade b' ? strtolower($gradeItem) : NULL;
                                        } else {
                                            $gudang->grade_item         = NULL;
                                        }
                                        $gudang->karung_qty         = $karung;
                                        $gudang->karung_isi         = $row[7];
                                        $gudang->stock_type         = $row[10] ?? NULL;
                                        $gudang->product_id         = $produk->id ?? NULL;
                                        $gudang->qty_awal           = $qty;
                                        $gudang->berat_awal         = $berat;
                                        $gudang->karung_awal        = $row[7];
                                        $gudang->qty                = $qty;
                                        $gudang->berat              = $berat;
                                        $gudang->plastik_group      = Item::plastik_group($row[12]);
                                        $gudang->expired            = NULL;

                                        
                                        $gudang->production_date    = $newDate ?? date('Y-m-d');

                                        $gudang->type               = "openbalance";

                                        $gudang->jenis_trans        = 'masuk';
                                        $gudang->status             = 2;
                                        $gudang->customer_id        = $customer;
                                        // Untuk membackup data Jika Nama Customer tidak ditemukan
                                        $gudang->keterangan         = $row[9];



                                        // $packaging                  =   Item::where('nama', $row[6])->first() ;

                                        // if ($packaging) {
                                        //     $gudang->packaging      =   $packaging->nama;
                                        // }else{
                                        //     $gudang->packaging      = $row[6];
                                        // }

                                        $storage                    = Gudang::where('code', $row[13])->first();
                                        if ($storage) {
                                            $gudang->gudang_id          = $storage->id;
                                        }else{
                                            $gudang->gudang_id          = NULL;
                                        }

                                        $gudang->notes              = $row[56];
                                        if (!$gudang->save()) {
                                            DB::rollBack();
                                            return back()->with('status', 2)->with('message', 'Proses gagal (GND-10)');
                                        }

                                    // }

                                // }else{
                                    // DB::rollBack();
                                    // return back()->with('status', 2)->with('message', 'Proses gagal (GND-11)');
                                // }

                            }catch (\Throwable $th) {
                                DB::rollBack();
                                return back()->with('status', 2)->with('message', $th->getMessage());
                                // return back()->with('status', 2)->with('message', 'Tidak ada data yang terupload');
                            }
                        }
                    }
                    // dd($rebase_array);

                }
                

                DB::commit();
                return back()->with('status', 1)->with('message', 'Proses Selesai');

                    //code...
            } catch (\Throwable $th) {
                //throw $th;
                DB::rollBack();
                return back()->with('status', 2)->with('message', $th->getMessage());
            }

        }

    }

    public function upload_stock_chiller_fg(Request $request){

        DB::beginTransaction();

        if ($request->hasFile('file')) {

            $path = $request->file('file');

            try {
                //code...
                $prod_import = Excel::toArray([],$path);
            } catch (\Throwable $th) {
                DB::rollBack() ;
                //throw $th;
                return "Format Tidak didukung, ulangi lagi dengan format excel";
            }

            $resp = [];
            foreach ($prod_import[0] as $urut => $line) {

                if ($urut != 0) {

                    $item                       =   Item::where('nama', $line[0])->first();

                    if ($item) {

                        $proceed    = true;
                        $chiller    = new Chiller();
                        $chiller->asal_tujuan        =   'free_stock';
                        $chiller->item_id            =   $item->id;
                        $chiller->item_name          =   $item->nama;
                        $chiller->qty_item           =   $line[2];
                        $chiller->berat_item         =   str_replace(",",".",$line[3]);
                        $chiller->stock_item         =   $line[2];
                        $chiller->stock_berat        =   str_replace(",",".",$line[3]);
                        $chiller->jenis              =   'masuk';
                        $chiller->type               =   'hasil-produksi';
                        $chiller->tanggal_potong     =   date('Y-m-d', strtotime($line[4]));
                        $chiller->tanggal_produksi   =   date('Y-m-d', strtotime($line[4]));

                        $chiller->status             =   2;
                        $chiller->save();
                        $resp[] = $chiller;
                    }
                }
            }

            DB::commit();

            return back()->with('status', 1)->with('message', 'Import berhasil');
        }else{
            DB::rollBack() ;
            return back()->with('status', 2)->with('message', 'Import gagal');
        }

    }

    public function upload_stock_chiller_bb(Request $request){

        DB::beginTransaction();

        if ($request->hasFile('file')) {

            $path = $request->file('file');

            try {
                //code...
                $prod_import = Excel::toArray([],$path);
            } catch (\Throwable $th) {
                //throw $th;
                DB::rollBack() ;
                return "Format Tidak didukung, ulangi lagi dengan format excel";
            }

            $resp = [];
            foreach ($prod_import[0] as $urut => $line) {

                if ($urut != 0) {

                    $item                       =   Item::where('nama', $line[0])->first();

                    if ($item) {

                        $chiller   =   Chiller::whereDate('tanggal_potong', date('Y-m-d', strtotime($line[4])))
                                    ->where('item_id', $item->id)
                                    ->where('status', 2)
                                    ->where('type', 'bahan-baku')
                                    ->where('asal_tujuan', 'gradinggabungan')
                                    ->first();
                        $proceed    = true;
                        if(!$chiller){
                            $chiller    = new Chiller();
                        }

                        $chiller->item_id            =   $item->id;
                        $chiller->item_name          =   $item->nama;
                        $chiller->asal_tujuan        =   "gradinggabungan";
                        // $chiller->qty_item           =   $line[2];
                        // $chiller->berat_item         =   str_replace(",",".",$line[3]);
                        $chiller->stock_item         =   $line[2];
                        $chiller->stock_berat        =   str_replace(",",".",$line[3]);
                        $chiller->jenis              =   'masuk';
                        $chiller->type               =   'bahan-baku';
                        $chiller->tanggal_potong     =   date('Y-m-d', strtotime($line[4]));
                        $chiller->tanggal_produksi   =   date('Y-m-d', strtotime($line[4]));

                        $chiller->status             =   2;
                        $chiller->save();
                        $resp[] = $chiller;
                    }
                }
            }

            DB::commit();

            return back()->with('status', 1)->with('message', 'Import berhasil');
        }else{
            DB::rollBack() ;
            return back()->with('status', 2)->with('message', 'Import gagal');
        }

    }

    public function upload_abf_cs(Request $request){

        DB::beginTransaction();

        if ($request->hasFile('file')) {

            $path = $request->file('file');

            try {
                //code...
                $prod_import = Excel::toArray([],$path);
            } catch (\Throwable $th) {
                //throw $th;
                DB::rollBack() ;
                return "Format Tidak didukung, ulangi lagi dengan format excel";
            }

            $resp = [];
            foreach ($prod_import[0] as $urut => $line) {

                if ($urut != 0) {

                    $item_lama                       =   Item::where('nama', str_replace(" frozen", "", $line[3]))->first();
                    $item                       =   Item::where('nama', $line[3])->first();
                    $tanggal                    =   date('Y-m-d', strtotime($line[0]));

                    if ($item) {

                            $open               =   new ModelsOpenbalance;
                            $open->user_id      =   Auth::user()->id;
                            $open->gudang       =   'chiller';
                            $open->item_id      =   $item->id;
                            $open->tipe_item    =   'hasil-produksi';
                            $open->tanggal      =   date('Y-m-d');
                            $open->qty          =   (integer)str_replace(",", ".", str_replace(".", "", $line[4])) ?? 0;
                            $open->berat        =   (float)str_replace(",", ".", str_replace(".", "", $line[5])) ?? 0;
                            if (!$open->save()) {
                                DB::rollBack();
                                return back()->with('status', 2)->with('message', 'Proses gagal (PKG-12)');
                            }

                            $gdg = $line[1];
                            $code = env('NET_SUBSIDIARY', 'CGL')." - Cold Storage 1";
                            if($gdg=="MANIS 1" || $gdg=="MANIS 2"){
                                $code = env('NET_SUBSIDIARY', 'CGL')." - Manis";
                            }
                            if($gdg=="CGL 1"){
                                $code = env('NET_SUBSIDIARY', 'CGL')." - Cold Storage 1";
                            }
                            if($gdg=="CGL 2"){
                                $code = env('NET_SUBSIDIARY', 'CGL')." - Cold Storage 2";
                            }
                            if($gdg=="CGL 3"){
                                $code = env('NET_SUBSIDIARY', 'CGL')." - Cold Storage 3";
                            }
                            if($gdg=="CGL 4"){
                                $code = env('NET_SUBSIDIARY', 'CGL')." - Cold Storage 4";
                            }
                            $gudang = Gudang::where('code', $code)->first();

                            $abf                    =   new Abf;
                            $abf->table_name        =   'openbalance';
                            $abf->table_id          =   $open->id;
                            $abf->asal_tujuan       =   'open_balance';
                            $abf->tanggal_masuk     =   $tanggal;
                            $abf->item_id           =   $item->id;
                            $abf->tanggal_keluar    =   $tanggal;
                            $abf->item_id_lama      =   $item_lama->id;
                            $abf->item_name         =   $item_lama->nama;
                            $abf->jenis             =   'masuk';
                            $abf->type              =   $open->tipe_item;
                            $abf->tujuan            =   $gudang->id;
                            $abf->qty_awal          =   $open->qty;
                            $abf->berat_awal        =   $open->berat;
                            $abf->qty_item          =   $open->qty;
                            $abf->berat_item        =   $open->berat;

                            $abf->status            =   2;
                            if (!$abf->save()) {
                                DB::rollBack();
                                return back()->with('status', 2)->with('message', 'Proses gagal (BFF-32)');
                            }

                            $prod_gudang                     =   new Product_gudang;
                            $prod_gudang->table_name         =   'abf';
                            $prod_gudang->table_id           =   $abf->id;
                            $prod_gudang->sub_item           =   $line[9];
                            $prod_gudang->parting            =   (integer)$line[6];
                            $prod_gudang->product_id         =   $item->id;
                            $prod_gudang->nama               =   $item->nama;
                            $prod_gudang->qty_awal           =   (float)$open->qty;
                            $prod_gudang->berat_awal         =   (float)$open->berat;
                            $prod_gudang->qty                =   (float)$open->qty;
                            $prod_gudang->berat              =   (float)$open->berat;
                            $prod_gudang->palete             =   '1';
                            $prod_gudang->gudang_id          =   $gudang->id;
                            $prod_gudang->expired            =   NULL;
                            $prod_gudang->production_date    =   $tanggal;
                            $prod_gudang->type               =   "freestock";
                            $prod_gudang->stock_type         =   "free";
                            $prod_gudang->jenis_trans        =   'masuk';
                            $prod_gudang->status             =   2;

                            if (!$prod_gudang->save()) {
                                DB::rollBack();
                                return back()->with('status', 2)->with('message', 'Proses gagal (GND-12)');
                            }


                            $transfer_akhir = [];
                            // ===================    TRANSFER INVENTORY IN FINISHED GOOD    ===================
                            $nama_tabel     =   "abf";
                            $id_tabel       =   $abf->id;

                            $from           =   Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Storage ABF");

                            $nama_gudang_cs = env('NET_SUBSIDIARY', 'CGL')." - Cold Storage";
                            $gudang_baru    =   Gudang::where('code', $nama_gudang_cs)->first();
                            $label          =   "ti_abf_cs_".str_replace(" ","-",str_replace("-","",strtolower($gudang_baru->code)))."_custom";
                            $to             =   $gudang_baru->netsuite_internal_id;
                            $location_from       =   env('NET_SUBSIDIARY', 'CGL')." - Storage ABF" ;
                            $id_location_from    =   Gudang::gudang_netid($location_from) ;

                            $transfer_akhir[] =   [
                                "internal_id_item"  =>  (string)$item->netsuite_internal_id,
                                "item"              =>  (string)$item->sku,
                                "qty_to_transfer"   =>  (string)$open->berat
                            ];

                            Netsuite::transfer_inventory_date($nama_tabel, $id_tabel, $label, $id_location_from, $location_from, $from, $to, $transfer_akhir, null, $tanggal);
                    }
                }
            }

            DB::commit();

            return back()->with('status', 1)->with('message', 'Import berhasil');
        }else{
            DB::rollBack() ;
            return back()->with('status', 2)->with('message', 'Import gagal');
        }

    }

    public function generate_ti_custom(Request $request){

        DB::beginTransaction();

        if ($request->hasFile('file')) {

            $path = $request->file('file');

            try {
                //code...
                $prod_import = Excel::toArray([],$path);
            } catch (\Throwable $th) {
                //throw $th;
                DB::rollBack() ;
                return "Format Tidak didukung, ulangi lagi dengan format excel";
            }

            $resp = [];
            foreach ($prod_import[0] as $urut => $line) {

                if ($urut != 0) {

                    $item                       =   Item::where('nama', $line[2])->first();
                    $tanggal                    =   date('Y-m-d', strtotime($line[0]));

                    if ($item) {

                            $transfer_akhir = [];
                            // ===================    TRANSFER INVENTORY IN FINISHED GOOD    ===================
                            $nama_tabel     =   null;
                            $id_tabel       =   null;

                            $from           =   Gudang::gudang_netid($line[1]);

                            $gudang_lama    =   Gudang::where('code', $line[1])->first();
                            $gudang_baru    =   Gudang::where('code', $line[4])->first();
                            $label          =   "ti_".str_replace(" ","-",str_replace("-","",strtolower($gudang_lama->code)))."_".str_replace(" ","-",str_replace("-","",strtolower($gudang_baru->code)))."_custom";
                            $to             =   $gudang_baru->netsuite_internal_id;
                            $location_from       =   $line[1] ;
                            $id_location_from    =   Gudang::gudang_netid($location_from) ;

                            $berat          = (float)str_replace(",", "", $line[3]) ?? 0;

                            $transfer_akhir[] =   [
                                "internal_id_item"  =>  (string)$item->netsuite_internal_id,
                                "item"              =>  (string)$item->sku,
                                "qty_to_transfer"   =>  (string)$berat
                            ];

                            Netsuite::transfer_inventory_date($nama_tabel, $id_tabel, $label, $id_location_from, $location_from, $from, $to, $transfer_akhir, null, $tanggal);
                    }
                }
            }

            DB::commit();

            return back()->with('status', 1)->with('message', 'Import berhasil');
        }else{
            DB::rollBack() ;
            return back()->with('status', 2)->with('message', 'Import gagal');
        }

    }


    public function upload_abf_cs_wo(Request $request)
    {

        DB::beginTransaction();

        if ($request->hasFile('file')) {

            $path = $request->file('file');

            try {
                //code...
                $prod_import = Excel::toArray([],$path);
            } catch (\Throwable $th) {
                //throw $th;
                DB::rollBack() ;
                return "Format Tidak didukung, ulangi lagi dengan format excel";
            }

            $resp = [];

            try {
                //code...

                foreach ($prod_import[0] as $urut => $line) {

                    if ($urut != 0) {
    
                        // return $line;
    
                        $item_lama                  =   Item::where('nama', str_replace(" FROZEN", "", $line[2]))->first();
                        $item                       =   Item::where('nama', $line[2])->first();
                        $tanggal                    =   date('Y-m-d', strtotime($line[0]));
    
                        if ($item) {
    
                                $open               =   new ModelsOpenbalance;
                                $open->user_id      =   Auth::user()->id;
                                $open->gudang       =   'chiller';
                                $open->item_id      =   $item->id;
                                $open->tipe_item    =   'hasil-produksi';
                                $open->tanggal      =   $tanggal;
                                $open->qty          =   (float)str_replace(",", "", $line[3]) ?? 0;
                                $open->berat        =   (float)str_replace(",", "", $line[4]) ?? 0;
                                if (!$open->save()) {
                                    DB::rollBack();
                                    return back()->with('status', 2)->with('message', 'Proses gagal (PKG-12)');
                                }
    
                                $gdg = $line[1];
                                $code = env('NET_SUBSIDIARY', 'CGL')." - Cold Storage 1";
                                if($gdg=="MANIS 1" || $gdg=="MANIS 2"){
                                    $code = env('NET_SUBSIDIARY', 'CGL')." - Manis";
                                }
                                if($gdg=="CGL 1"){
                                    $code = env('NET_SUBSIDIARY', 'CGL')." - Cold Storage 1";
                                }
                                if($gdg=="CGL 2"){
                                    $code = env('NET_SUBSIDIARY', 'CGL')." - Cold Storage 2";
                                }
                                if($gdg=="CGL 3"){
                                    $code = env('NET_SUBSIDIARY', 'CGL')." - Cold Storage 3";
                                }
                                if($gdg=="CGL 4"){
                                    $code = env('NET_SUBSIDIARY', 'CGL')." - Cold Storage 4";
                                }
                                $gudang = Gudang::where('code', $code)->first();
    
                                $abf                    =   new Abf;
                                $abf->table_name        =   'openbalance';
                                $abf->table_id          =   $open->id;
                                $abf->asal_tujuan       =   'open_balance';
                                $abf->item_id           =   $item_lama->id;
                                $abf->tanggal_masuk     =   $tanggal;
                                $abf->tanggal_keluar    =   $tanggal;
                                $abf->item_id_lama      =   $item_lama->id;
                                $abf->item_name         =   $item_lama->nama;
                                $abf->jenis             =   'masuk';
                                $abf->type              =   $open->tipe_item;
                                $abf->tujuan            =   $gudang->id;
                                $abf->qty_awal          =   $open->qty;
                                $abf->berat_awal        =   $open->berat;
                                $abf->qty_item          =   0;
                                $abf->berat_item        =   0;
    
                                $abf->status            =   2;
                                if (!$abf->save()) {
                                    DB::rollBack();
                                    return back()->with('status', 2)->with('message', 'Proses gagal (BFF-32)');
                                }
    
                                $prod_gudang                     =   new Product_gudang;
                                $prod_gudang->table_name         =   'abf';
                                $prod_gudang->table_id           =   $abf->id;
                                $prod_gudang->sub_item           =   $line[9];
                                $prod_gudang->parting            =   (integer)$line[6];
                                $prod_gudang->product_id         =   $item->id;
                                $prod_gudang->nama               =   $item->nama;
                                $prod_gudang->qty_awal           =   (float)$open->qty;
                                $prod_gudang->berat_awal         =   (float)$open->berat;
                                $prod_gudang->qty                =   (float)$open->qty;
                                $prod_gudang->berat              =   (float)$open->berat;
                                $prod_gudang->palete             =   '1';
                                $prod_gudang->gudang_id          =   $gudang->id;
                                $prod_gudang->expired            =   NULL;
                                $prod_gudang->production_date    =   $tanggal;
                                $prod_gudang->type               =   "freestock";
                                $prod_gudang->stock_type         =   "free";
                                $prod_gudang->jenis_trans        =   'masuk';
                                $prod_gudang->status             =   2;
    
                                if (!$prod_gudang->save()) {
                                    DB::rollBack();
                                    return back()->with('status', 2)->with('message', 'Proses gagal (GND-12)');
                                }
    
    
                                $transfer_fg_abf = [];
                                $transfer_awal = [];
                                $transfer_akhir = [];
    
                                // ===================    TRANSFER INVENTORY IN ABF    ===================
    
                                $nama_tabel     =   "abf";
                                $id_tabel       =   $abf->id;
    
                                $from           =   Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Chiller Finished Good");
    
                                $label          =   "ti_fg_abf_custom";
                                $to             =   Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Storage ABF");
                                $location_from       =   env('NET_SUBSIDIARY', 'CGL')." - Chiller Finished Good" ;
                                $id_location_from    =   Gudang::gudang_netid($location_from) ;
    
                                $transfer_fg_abf[] =   [
                                    "internal_id_item"  =>  (string)$item_lama->netsuite_internal_id,
                                    "item"              =>  (string)$item_lama->sku,
                                    "qty_to_transfer"   =>  (string)$open->berat
                                ];
    
                                if($item_lama->category_id!="1"){
                                    $ti = Netsuite::transfer_inventory_doc($nama_tabel, $id_tabel, $label, $id_location_from, $location_from, $from, $to, $transfer_fg_abf, null, $tanggal, $abf->id.".FG-ABF.WO-3");
                                }
    
                                // ===================    WO3 IN ABF    ===================
    
                                $finished_good          =   [] ;
                                $component              =   [] ;
                                $proses                 =   [] ;
    
                                $location       =   env('NET_SUBSIDIARY', 'CGL')." - Storage ABF" ;
                                $id_location    =   Gudang::gudang_netid($location) ;
    
                                $label          =   'wo-3-abf-cs';
    
                                $bom_kategori = Item::find($item_lama->id);
                                $item_assembly = env('NET_SUBSIDIARY', 'CGL')." - AYAM KARKAS FROZEN";
    
                                $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL')." - AYAM KARKAS FROZEN")
                                        ->first();
    
                                if($bom_kategori){
                                    if($bom_kategori->category_id=="5"){
                                        $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL')." - BONELESS BROILER FROZEN")
                                        ->first();
                                        $item_assembly = env('NET_SUBSIDIARY', 'CGL')." - BONELESS BROILER FROZEN";
    
                                    }elseif($bom_kategori->category_id=="3"){
                                        $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL')." - AYAM PARTING MARINASI BROILER FROZEN")
                                        ->first();
                                        $item_assembly = env('NET_SUBSIDIARY', 'CGL')." - AYAM PARTING MARINASI BROILER FROZEN";
    
                                    }elseif($bom_kategori->category_id=="2"){
                                        $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL')." - AYAM PARTING BROILER FROZEN")
                                        ->first();
                                        $item_assembly = env('NET_SUBSIDIARY', 'CGL')." - AYAM PARTING BROILER FROZEN";
    
                                    }else{
                                        $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL')." - AYAM KARKAS FROZEN")
                                        ->first();
                                        $item_assembly = env('NET_SUBSIDIARY', 'CGL')." - AYAM KARKAS FROZEN";
    
                                    }
                                }
    
                                $nama_assembly  =   $bom->bom_name ;
                                $id_assembly    =   $bom->netsuite_internal_id ;
                                $bom_id         =   $bom->id;
    
                                $transfer_awal[] =   [
                                    "internal_id_item"  =>  (string)$item_lama->netsuite_internal_id,
                                    "item"              =>  (string)$item_lama->sku,
                                    "qty_to_transfer"   =>  (string)$open->berat
                                ];
    
                                $component[]        =   [
                                    "type"              =>  "Component",
                                    "internal_id_item"  =>  (string)$item_lama->netsuite_internal_id,
                                    "item"              =>  (string)$item_lama->sku,
                                    "description"       =>  (string)$item_lama->nama,
                                    "qty"               =>  (string)$open->berat,
                                ];
    
                                if ($item == '') {
                                    return 'Item Kosong';
                                }
    
                                $wo_id = NULL;
                                $wob_id = NULL;
                               
                                foreach ($bom->bomproses as $row) {
                                    $proses[]   =   [
                                        "type"              =>  "Component",
                                        "internal_id_item"  =>  (string)Item::item_sku($row->sku)->netsuite_internal_id,
                                        "item"              =>  $row->sku,
                                        "description"       =>  (string)Item::item_sku($row->sku)->nama,
                                        "qty"               =>  (string)Item::where('nama', 'AY - S')->first()->sku == $row->sku ? $row->qty_per_assembly : ($row->qty_per_assembly * $open->berat),
                                    ];
                                }

                                $finished_good[]         =   [
                                    "type"              =>  "Finished Goods",
                                    "internal_id_item"  =>  (string)$item->netsuite_internal_id,
                                    "item"              =>  (string)$item->sku,
                                    "description"       =>  (string)$item->nama,
                                    "qty"               =>  (string)$open->berat,
                                ];


                                $produksi       =   array_merge($component, $proses, $finished_good);

                                if($item_lama->category_id!="1"){
                                    $wo     =   Netsuite::work_order_doc('abf', $abf->id, $label, $id_assembly, $item_assembly, $id_location, $location, $produksi, $ti->id, $tanggal, $abf->id.".ABF.WO-3");
                                }else{
                                    $wo     =   Netsuite::work_order_doc('abf', $abf->id, $label, $id_assembly, $item_assembly, $id_location, $location, $produksi, NULL, $tanggal, $abf->id.".ABF.WO-3");
                                }


                                $label  =   'wo-3-build-abf-cs';
                                $total  =   $open->berat;
                                $wob    =   Netsuite::wo_build_doc('abf', $abf->id, $label, $id_assembly, $item_assembly, $id_location, $location, $total, $produksi, $wo->id, $tanggal, $abf->id.".ABF.WO-3");

                                // ===================    TRANSFER INVENTORY IN CS    ===================
                                $nama_tabel     =   "abf";
                                $id_tabel       =   $abf->id;
    
                                $from           =   Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Storage ABF");

                                $gudang_baru    =   Gudang::where('code', env('NET_SUBSIDIARY', 'CGL').' - Cold Storage')->first();

                                $label          =   "ti_abf_cs_".str_replace(" ","-",str_replace("-","",strtolower($gudang_baru->code)))."_custom";
                                $to             =   $gudang_baru->netsuite_internal_id;
                                $location_from       =   env('NET_SUBSIDIARY', 'CGL')." - Storage ABF" ;
                                $id_location_from    =   Gudang::gudang_netid($location_from) ;
    
                                $transfer_akhir[] =   [
                                    "internal_id_item"  =>  (string)$item->netsuite_internal_id,
                                    "item"              =>  (string)$item->sku,
                                    "qty_to_transfer"   =>  (string)$open->berat
                                ];
    
                                Netsuite::transfer_inventory_doc($nama_tabel, $id_tabel, $label, $id_location_from, $location_from, $from, $to, $transfer_akhir, $wob->id, $tanggal, $abf->id.".ABF-CS.".$gudang_baru->netsuite_internal_id.".WO-3");

    
                        }
                    }
                }

            } catch (\Throwable $th) {
                //throw $th;
                DB::rollBack() ;
                return $th->getMessage();
            }
            

            DB::commit();

            return back()->with('status', 1)->with('message', 'Import berhasil');
        }else{
            DB::rollBack() ;
            return back()->with('status', 2)->with('message', 'Import gagal');
        }

    }


    public function upload_abf_cs_ti(Request $request)
    {

        DB::beginTransaction();

        if ($request->hasFile('file')) {

            $path = $request->file('file');

            try {
                //code...
                $prod_import = Excel::toArray([],$path);
            } catch (\Throwable $th) {
                //throw $th;
                DB::rollBack() ;
                return "Format Tidak didukung, ulangi lagi dengan format excel";
            }

            $resp = [];

            try {
                //code...

                foreach ($prod_import[0] as $urut => $line) {

                    if ($urut != 0) {
    
                        // return $line;
    
                        $item_lama                  =   Item::where('nama', str_replace(" FROZEN", "", $line[2]))->first();
                        $item                       =   Item::where('nama', $line[2])->first();
                        $tanggal                    =   date('Y-m-d', strtotime($line[0]));
    
                        if ($item) {
    
                                $open               =   new ModelsOpenbalance;
                                $open->user_id      =   Auth::user()->id;
                                $open->gudang       =   'chiller';
                                $open->item_id      =   $item->id;
                                $open->tipe_item    =   'hasil-produksi';
                                $open->tanggal      =   $tanggal;
                                $open->qty          =   (float)str_replace(",", "", $line[3]) ?? 0;
                                $open->berat        =   (float)str_replace(",", "", $line[4]) ?? 0;
                                if (!$open->save()) {
                                    DB::rollBack();
                                    return back()->with('status', 2)->with('message', 'Proses gagal (PKG-12)');
                                }
    
                                $gdg = $line[1];
                                $code = env('NET_SUBSIDIARY', 'CGL')." - Cold Storage 1";
                                if($gdg=="MANIS 1" || $gdg=="MANIS 2"){
                                    $code = env('NET_SUBSIDIARY', 'CGL')." - Manis";
                                }
                                if($gdg=="CGL 1"){
                                    $code = env('NET_SUBSIDIARY', 'CGL')." - Cold Storage 1";
                                }
                                if($gdg=="CGL 2"){
                                    $code = env('NET_SUBSIDIARY', 'CGL')." - Cold Storage 2";
                                }
                                if($gdg=="CGL 3"){
                                    $code = env('NET_SUBSIDIARY', 'CGL')." - Cold Storage 3";
                                }
                                if($gdg=="CGL 4"){
                                    $code = env('NET_SUBSIDIARY', 'CGL')." - Cold Storage 4";
                                }
                                $gudang = Gudang::where('code', $code)->first();
    
                                $prod_gudang                     =   new Product_gudang;
                                $prod_gudang->table_name         =   'open_balance';
                                $prod_gudang->table_id           =   $open->id;
                                $prod_gudang->sub_item           =   $line[9];
                                $prod_gudang->parting            =   (integer)$line[6]==0 ? NULL : (integer)$line[6];
                                $prod_gudang->product_id         =   $item->id;
                                $prod_gudang->nama               =   $item->nama;
                                $prod_gudang->qty_awal           =   (float)$open->qty;
                                $prod_gudang->berat_awal         =   (float)$open->berat;
                                $prod_gudang->qty                =   (float)$open->qty;
                                $prod_gudang->berat              =   (float)$open->berat;
                                $prod_gudang->palete             =   '1';
                                $prod_gudang->gudang_id          =   $gudang->id;
                                $prod_gudang->expired            =   NULL;
                                $prod_gudang->production_date    =   $tanggal;
                                $prod_gudang->type               =   "freestock";
                                $prod_gudang->stock_type         =   "free";
                                $prod_gudang->jenis_trans        =   'masuk';
                                $prod_gudang->status             =   2;
    
                                if (!$prod_gudang->save()) {
                                    DB::rollBack();
                                    return back()->with('status', 2)->with('message', 'Proses gagal (GND-12)');
                                }
    
                                // ===================    TRANSFER INVENTORY IN CS    ===================
                                $nama_tabel     =   "open_balance";
                                $id_tabel       =   $open->id;
    
                                $from           =   Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Storage ABF");
    
                                $gudang_baru    =   Gudang::where('code', env('NET_SUBSIDIARY', 'CGL').' - Cold Storage')->first();
                                $label          =   "ti_abf_cs_".str_replace(" ","-",str_replace("-","",strtolower($gudang_baru->code)))."_custom";
                                $to             =   $gudang_baru->netsuite_internal_id;
                                $location_from       =   env('NET_SUBSIDIARY', 'CGL')." - Storage ABF" ;
                                $id_location_from    =   Gudang::gudang_netid($location_from) ;
    
                                $transfer_akhir     = [];
                                $transfer_akhir[]   =   [
                                    "internal_id_item"  =>  (string)$item->netsuite_internal_id,
                                    "item"              =>  (string)$item->sku,
                                    "qty_to_transfer"   =>  (string)$open->berat
                                ];
    
                                Netsuite::transfer_inventory_doc($nama_tabel, $id_tabel, $label, $id_location_from, $location_from, $from, $to, $transfer_akhir, NULL, $tanggal, ".ABF-CS.".$gudang_baru->netsuite_internal_id.".TI");

                        }
                    }
                }

            } catch (\Throwable $th) {
                //throw $th;
                DB::rollBack() ;
                return $th->getMessage();
            }
            

            DB::commit();

            return back()->with('status', 1)->with('message', 'Import berhasil');
        }else{
            DB::rollBack() ;
            return back()->with('status', 2)->with('message', 'Import gagal');
        }

    }

    public function upload_wo_thawing(Request $request){

        DB::beginTransaction();

        if ($request->hasFile('file')) {

            $path = $request->file('file');

            try {
                //code...
                $prod_import = Excel::toArray([],$path);
            } catch (\Throwable $th) {
                //throw $th;
                DB::rollBack() ;
                return "Format Tidak didukung, ulangi lagi dengan format excel";
            }

            $resp = [];
            foreach ($prod_import[0] as $urut => $line) {

                if ($urut != 0) {

                    $tanggal                    =   date('Y-m-d', strtotime($line[0]));

                    $gdg = $line[1];
                    $code = env('NET_SUBSIDIARY', 'CGL')." - Cold Storage 1";
                    if($gdg=="MANIS 1" || $gdg=="MANIS 2"){
                        $code = env('NET_SUBSIDIARY', 'CGL')." - Manis";
                    }
                    if($gdg=="CGL 1"){
                        $code = env('NET_SUBSIDIARY', 'CGL')." - Cold Storage 1";
                    }
                    if($gdg=="CGL 2"){
                        $code = env('NET_SUBSIDIARY', 'CGL')." - Cold Storage 2";
                    }
                    if($gdg=="CGL 3"){
                        $code = env('NET_SUBSIDIARY', 'CGL')." - Cold Storage 3";
                    }
                    if($gdg=="CGL 4"){
                        $code = env('NET_SUBSIDIARY', 'CGL')." - Cold Storage 4";
                    }

                    $gudang = Gudang::where('code', $code)->first();

                    $berat   =   (float)(str_replace(",", ".", str_replace(".", "", $line[3])) ?? 0);
                    $item    =   Item::where('nama', $line[2])
                                        ->first();

                    $item_finish    =   Item::where('nama', str_replace(' FROZEN', '', $line[2]))
                                        ->first();

                        // MASUK CHILLER BB JIKA KARKAS

                        if($item_finish->category_id=="1"){
                            $label_ti   =   "ti_cs_".str_replace(" ","-",str_replace("-","",strtolower($gudang->code)))."_chiller_bb_custom";
                            $to         =   Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Chiller Bahan Baku");
                        }else if(in_array($item_finish->category_id, [2,3,4,5,6])){
                            $label_ti   =   "ti_cs_".str_replace(" ","-",str_replace("-","",strtolower($gudang->code)))."_chiller_fg_custom";
                            $to         =   Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Chiller Finished Good");
                        }else{
                            $label_ti   =   "ti_cs_".str_replace(" ","-",str_replace("-","",strtolower($gudang->code)))."_expedisi_custom";
                            $to         =   Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Storage Expedisi");
                        }

                        $transfer   =   [
                            [
                                "internal_id_item"  =>  (string)$item->netsuite_internal_id,
                                "item"              =>  (string)$item->sku,
                                "qty_to_transfer"   =>  (string)(str_replace(",", ".", str_replace(".", "", $line[3])) ?? 0)
                            ]
                        ];

                    $id_location    =   $gudang->netsuite_internal_id;
                    $location       =   $gudang->code;
                    $from           =   $id_location;

                    $label          =   'wo-4-custom';

                    $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL').' - THAWING AYAM KARKAS BROILER FROZEN')->first();
                    $id_assembly    =   $bom->netsuite_internal_id;
                    $nama_assembly  =   $bom->bom_name ;
                    $item_assembly  =   env('NET_SUBSIDIARY', 'CGL')." - THAWING AYAM KARKAS BROILER FROZEN";


                    $component      =   [[
                        "type"              =>  "Component",
                        "internal_id_item"  =>  (string)$item->netsuite_internal_id,
                        "item"              =>  (string)$item->sku,
                        "description"       =>  (string)$item->nama,
                        "qty"               =>  (string)($berat),
                    ]];

                    $proses =   [];
                    foreach ($bom->bomproses as $row) {
                        $proses[]   =   [
                            "type"              =>  "Component",
                            "internal_id_item"  =>  (string)Item::item_sku($row->sku)->netsuite_internal_id,
                            "item"              =>  $row->sku,
                            "description"       =>  (string)Item::item_sku($row->sku)->nama,
                            "qty"               =>  (string)Item::where('nama', 'AY - S')->first()->sku == $row->sku ? $row->qty_per_assembly : ($row->qty_per_assembly * $berat),
                        ];
                    }

                    if($item_finish->category_id=="1"){
                        $label_ti   =   "ti_cs_".str_replace(" ","-",str_replace("-","",strtolower($gudang->code)))."_chiller_bb_custom";
                        $to         =   Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Chiller Bahan Baku");

                        $finished_good  =   [[
                            "type"              =>  "Finished Goods",
                            "internal_id_item"  =>  (string)Item::item_sku("1100000001")->netsuite_internal_id,
                            "item"              =>  "1100000001",
                            "description"       =>  "AYAM KARKAS BROILER (RM)",
                            "qty"               =>  (string)($berat),
                        ]];

                    }else if(in_array($item_finish->category_id, [2,3,4,5,6])){
                        $label_ti   =   "ti_cs_".str_replace(" ","-",str_replace("-","",strtolower($gudang->code)))."_chiller_fg_custom";
                        $to         =   Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Chiller Finished Good");

                        $finished_good  =   [[
                            "type"              =>  "Finished Goods",
                            "internal_id_item"  =>  (string)$item_finish->netsuite_internal_id,
                            "item"              =>  (string)$item_finish->sku,
                            "description"       =>  (string)$item_finish->nama,
                            "qty"               =>  (string)($berat),
                        ]];

                    }else{
                        $label_ti   =   "ti_cs_".str_replace(" ","-",str_replace("-","",strtolower($gudang->code)))."_expedisi_custom";
                        $to         =   Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Storage Expedisi");

                        $finished_good  =   [[
                            "type"              =>  "Finished Goods",
                            "internal_id_item"  =>  (string)$item_finish->netsuite_internal_id,
                            "item"              =>  (string)$item_finish->sku,
                            "description"       =>  (string)$item_finish->nama,
                            "qty"               =>  (string)($berat),
                        ]];
                    }



                    $produksi       =   array_merge($component, $proses, $finished_good);
                    $nama_tabel     =   "thawing";
                    $id_tabel       =   "";

                    // $ti = Netsuite::transfer_inventory_date($nama_tabel, $id_tabel, $label_ti, $id_location, $location, $from, $to, $transfer, null, $tanggal);

                    $gudang_wo         =   Gudang::where('code', env('NET_SUBSIDIARY', 'CGL')." - Chiller Bahan Baku")->first();
                    $id_location       =   $gudang_wo->netsuite_internal_id;
                    $location          =   $gudang_wo->code;

                    $wo = Netsuite::work_order_date($nama_tabel, $id_tabel, $label, $id_assembly, $item_assembly, $id_location, $location, $produksi, NULL, $tanggal);

                    $label          =   'wo-4-build-custom';
                    $total          =   $berat;
                    $wob = Netsuite::wo_build_date($nama_tabel, $id_tabel, $label, $id_assembly, $item_assembly, $id_location, $location, $total, $produksi, $wo->id, $tanggal);

                }

                DB::commit();

            }

                return back()->with('status', 1)->with('message', 'Import berhasil');
            }else{
                DB::rollBack() ;
                return back()->with('status', 2)->with('message', 'Import gagal');
            }
    }

    public function upload_wb3_recreate(Request $request){

        DB::beginTransaction();

        if ($request->hasFile('file')) {

            $path = $request->file('file');

            try {
                //code...
                $prod_import = Excel::toArray([],$path);
            } catch (\Throwable $th) {
                //throw $th;
                DB::rollBack() ;
                return "Format Tidak didukung, ulangi lagi dengan format excel";
            }

            $resp = [];
            foreach ($prod_import[0] as $urut => $line) {

                $ns_wb = Netsuite::where('document_no', $line[0])->first();
                if($ns_wb){
                    $ns_wo = Netsuite::where('id', $ns_wb->paket_id)->first();

                    $item_1   =   [
                        "line"              => "",
                        "type"              =>  "Component",
                        "internal_id_item"  =>  (string)Item::item_sku('2210000001')->netsuite_internal_id,
                        "item"              =>  '2210000001',
                        "description"       =>  (string)Item::item_sku(2210000001)->nama,
                        "qty"               =>  "87.38"
                    ];
                    $item_2   =   [
                        "line"              => "",
                        "type"              =>  "Component",
                        "internal_id_item"  =>  (string)Item::item_sku('2300000001')->netsuite_internal_id,
                        "item"              =>  '2300000001',
                        "description"       =>  (string)Item::item_sku(2300000001)->nama,
                        "qty"               =>  "325.58"
                    ];
                    $item_3   =   [
                        "line"              => "",
                        "type"              =>  "Component",
                        "internal_id_item"  =>  (string)Item::item_sku('2300000002')->netsuite_internal_id,
                        "item"              =>  '2300000002',
                        "description"       =>  (string)Item::item_sku(2300000002)->nama,
                        "qty"               =>  "98.92"
                    ];

                    $tambahan           = array(
                        $item_1, $item_2, $item_3
                    );

                    $new_ns_wo          = $ns_wo->replicate();
                    $new_ns_wo->status  = 5;
                    $new_ns_wo->paket_id            = NULL;
                    $new_ns_wo->response_id         = NULL;
                    $new_ns_wo->response            = NULL;
                    $new_ns_wo->document_no         = NULL;
                    $new_ns_wo->save();

                    $exp_wo            = json_decode($new_ns_wo->data_content, TRUE) ;
                    $new_line_item_wo  = [];

                    if($exp_wo['data'] ?? FALSE){
                        
                        $new_line_item_wo                      = $exp_wo['data'][0]['items'];
                        
                        $json_wo   =   [
                            "record_type"     =>     "work_order",
                                "data"          =>  [
                                    [
                                        "appsid"                    =>  env('NET_SUBSIDIARY')."-".$new_ns_wo->id,
                                        "internal_id_subsidiary"    =>  $exp_wo['data'][0]['internal_id_subsidiary'],
                                        "subsidiary"                =>  $exp_wo['data'][0]['subsidiary'],
                                        "transaction_date"          =>  $exp_wo['data'][0]['transaction_date'],
                                        "internal_id_customer"      =>  "",
                                        "customer"                  =>  "",
                                        "id_item_assembly"          =>  $exp_wo['data'][0]['id_item_assembly'],
                                        "item_assembly"             =>  $exp_wo['data'][0]['item_assembly'],
                                        "id_location"               =>  $exp_wo['data'][0]['id_location'],
                                        "location"                  =>  $exp_wo['data'][0]['location'],
                                        "plan_qty"                  =>  $exp_wo['data'][0]['plan_qty'],
                                        "items"                     =>  array_merge($new_line_item_wo, $tambahan)
                                    ]
                                ]
                        ] ;
    
                        $new_ns_wo->data_content  =   json_encode($json_wo) ;
                        $new_ns_wo->save() ;
                    }
                    
                    $new_ns_wb              = $ns_wb->replicate();
                    $new_ns_wb->paket_id    = $new_ns_wo->id;
                    $new_ns_wb->status      = 5;
                    $new_ns_wb->response_id         = NULL;
                    $new_ns_wb->response            = NULL;
                    $new_ns_wb->document_no         = NULL;
                    $new_ns_wb->save();

                    $exp_wb            = json_decode($new_ns_wb->data_content, TRUE) ;
                    $new_line_item  = [];

                    if($exp_wb['data'] ?? FALSE){
                        
                        $new_line_item                      = $exp_wb['data'][0]['items'];
                        
                        $json   =   [
                            "record_type"   =>  "wo_build",
                            "data"          =>  [
                                [
                                    "appsid"            =>  env('NET_SUBSIDIARY')."-".$new_ns_wb->id,
                                    "transaction_date"  =>  $exp_wb['data'][0]['transaction_date'],
                                    "qty_to_build"      =>  $exp_wb['data'][0]['qty_to_build'],
                                    "created_from_wo"   =>  "$new_ns_wo->response_id",
                                    "items"             =>  array_merge($new_line_item, $tambahan)
                                ]
                            ]
                        ] ;
    
                        $new_ns_wb->data_content  =   json_encode($json) ;
                        $new_ns_wb->save() ;
                    }

                    $resp [] = $new_ns_wo->id;
                    $resp [] = $new_ns_wb->id;

                }
            }

        }

        DB::commit();

        return $resp;
        
    }


    public function upload_vendor(Request $request){
        DB::beginTransaction();

        if ($request->hasFile('file')) {

            $path = $request->file('file');

            try {
                //code...
                $prod_import = Excel::toArray([],$path);
            } catch (\Throwable $th) {
                //throw $th;
                DB::rollBack() ;
                return "Format Tidak didukung, ulangi lagi dengan format excel";
            }

            $resp = [];
            foreach ($prod_import[0] as $urut => $line) {

                $data = Supplier::where('nama', $line[2])->first();

                if($data){
                    
                }else{
                    $data = new Supplier();
                }

                $data->nama                 = $line[2];
                $data->wilayah              = NULL;
                $data->peruntukan           = $line[3];
                $data->kode                 = $line[1];
                $data->netsuite_internal_id = $line[0];

                $data->save();
               
            }

        }

        DB::commit();

        return "OK";
    }

    public function upload_item(Request $request){
        DB::beginTransaction();

        if ($request->hasFile('file')) {

            $path = $request->file('file');

            try {
                //code...
                $prod_import = Excel::toArray([],$path);
            } catch (\Throwable $th) {
                //throw $th;
                DB::rollBack() ;
                return "Format Tidak didukung, ulangi lagi dengan format excel";
            }

            $resp = [];
            foreach ($prod_import[0] as $urut => $line) {

                $data = Item::where('nama', $line[2])->where('subsidiary', env('NET_SUBSIDIARY', 'CGL'))->first();

                $data = false;
                if($data){
                }else{
                    
                    $data = Item::where('sku', $line[1])->first();
                    if(!$data){
                        $data = new Item();
                    }else{
                    }
                }

                $category               = Category::where('nama', $line[6])->first();

                if($category){
                    $data->category_id      = $category->id ?? "23";
                }else{
                    $category           = new Category();
                    $category->nama     = $line[6];
                    $category->slug     = strtolower(str_replace(" ", "-", preg_replace('/[^A-Za-z0-9 !@#$%^&*()-.]/u','', strip_tags(strtolower(str_replace(" ", "-", $line[6]))))));
                    $category->save();

                    $data->category_id      = $category->id ?? "23";
                }

                $data->nama                 = $line[2];
                $data->nama_alias           = $line[2];
                $data->sku                  = $line[1];
                $data->code_item            = NULL;
                $data->tax_code             = $line[4];
                $data->tax_code_id          = NULL;
                $data->tax_rate             = NULL;
                $data->subsidiary           = env('NET_SUBSIDIARY', 'CGL');
                $data->netsuite_internal_id = $line[0];
                $data->slug                 = strtolower(str_replace(" ", "-", preg_replace('/[^A-Za-z0-9 !@#$%^&*()-.]/u','', strip_tags(strtolower(str_replace(" ", "-", $line[2]))))));

                $data->save();
               
            }

        }

        DB::commit();

        return "OK";
    }

    public function upload_customer(Request $request){

        DB::beginTransaction();

        if ($request->hasFile('file')) {

            $path = $request->file('file');

            try {
                //code...
                $prod_import = Excel::toArray([],$path);
            } catch (\Throwable $th) {
                //throw $th;
                DB::rollBack() ;
                return "Format Tidak didukung, ulangi lagi dengan format excel";
            }

            $resp = [];
            foreach ($prod_import[0] as $urut => $line) {

                $nama_pecah                 =   explode(" - ",$line[0]);

                if($nama_pecah[2] ?? FALSE){
                    $nama_cust                  =   ($nama_pecah[1] ?? "")." - ".($nama_pecah[2] ?? "");
                }else{
                    $nama_cust                  =   ($nama_pecah[1] ?? "");
                }
                $kode_cust                  =   ($nama_pecah[0] ?? "");

                $c                          =   Customer::where('nama', $nama_cust)->first();
                if(!$c){
                    $c                      = new Customer();
                }else{
                }

                $c->netsuite_internal_id        =   $line[2] ?? NULL;
                $c->nama                        =   $nama_cust ?? NULL;
                $c->kode                        =   $kode_cust ?? NULL;
                
                if($line[3] ?? FALSE){
                    $parent_id = Customer::where('kode', (explode(" - ",$line[3])[0] ?? NULL))->first();
                    if($parent_id){
                        $c->parent_id           =   $parent_id->id ?? NULL;
                    }
                }
                

                if($line[1] ?? FALSE){
                    $marketing_pecah            = explode(" - ",$line[1]);
                    $marketing_id           = Marketing::where('nama', ($marketing_pecah[2] ?? NULL))->first();
                    if(!$marketing_id){
                        $marketing_id       = new Marketing();
                    }

                    $marketing_id->nama                  = ($marketing_pecah[2] ?? NULL);
                    $marketing_id->netsuite_internal_id  = ($marketing_pecah[1] ?? NULL);
                    $marketing_id->save();

                    $c->marketing_id    = $marketing_id->id;
                    $c->nama_marketing  = $marketing_id->nama;
                }

                $c->save();
               
            }

        }

        DB::commit();

        return "OK";

    }


    public function upload_wo2_regu(Request $request){
        DB::beginTransaction();

        if ($request->hasFile('file')) {

            $path = $request->file('file');

            try {
                //code...
                $prod_import = Excel::toArray([],$path);
            } catch (\Throwable $th) {
                //throw $th;
                DB::rollBack() ;
                return "Format Tidak didukung, ulangi lagi dengan format excel";
            }

            $resp = [];

            foreach ($prod_import[0] as $urut => $line) {

                if($urut>1){
                    
                    if($line[1]!=""){
                        $freestock              = new Freestock();
                        $freestock->regu        = $line[3];
                        $freestock->tanggal     = date('Y-m-d',strtotime($line[2]));
                        $freestock->status      = 2;

                        if($line[0]=="WO"){
                            $freestock->netsuite_send = NULL;
                        }elseif($line[0]=="Non WO"){
                            $freestock->netsuite_send = 0;
                        }
                        $freestock->save();
                    }

                    if($freestock){
                        $item = Item::where('nama', $line[4])->first();
                        if($item && (float)$line[7]!=0){
                            $fs_list                    = new FreestockList();
                            $fs_list->freestock_id      = $freestock->id;
                            $fs_list->item_id           = $item->id;
                            $fs_list->regu              = $line[3];
                            $fs_list->qty               = (float)$line[6];
                            $fs_list->berat             = (float)$line[7];

                            // BB Ambil chiller
                            $bb_chiller = Chiller::where('tanggal_produksi', $line[5])
                                                    ->where('item_id', $item->id)
                                                    ->where('asal_tujuan', $line[8])
                                                    ->where('status', '2')
                                                    ->first();

                            $fs_list->chiller_id        = $bb_chiller->id ?? NULL;

                            if($bb_chiller){
                                if ($bb_chiller->asal_tujuan == "evisgabungan") {
                                        if ($bb_chiller->tanggal_produksi >= date('Y-m-d', strtotime($freestock->tanggal))) {
                                            $fs_list->bb_kondisi       =   "baru";
                                        } else {
                                            $fs_list->bb_kondisi       =   "lama";
                                        }
                                    } else {
                                        $fs_list->bb_kondisi           =   $bb_chiller->asal_tujuan;
                                    }
                            }

                            $fs_list->save();

                        }


                        $item = Item::where('nama', $line[9])->first();
                        
                        if($item && (float)$line[11]!=0){

                            $fg_list                = new FreestockTemp();
                            $fg_list->freestock_id  = $freestock->id;
                            $fg_list->item_id       = $item->id;
                            $fg_list->prod_nama     = $item->nama;

                            $cust                   = Customer::where('nama', $line[14])->first();
                            if($cust){
                                $fg_list->customer_id  = $cust->id;
                            }

                            if($line[15]=="ABF"){
                                $kat = 1;
                            }elseif($line[16]=="Chiller FG"){
                                $kat = 0;
                            }
                            $fg_list->kategori      = $kat ?? NULL;
                            $fg_list->regu          = $line[3];

                            $plastik = Item::where('nama', $line[12])->first();
                            if($plastik){
                                $fg_list->plastik_sku          = $plastik->sku;
                                $fg_list->plastik_nama         = $plastik->nama;
                                $fg_list->plastik_qty          = (float)$line[13];
                            }

                            $fg_list->qty       = (float)$line[10];
                            $fg_list->berat     = (float)$line[11];

                            $fg_list->save();
                        }
                    }

                }

            }

        }

        DB::commit();

        return "OK";
    }

    public static function getGudangName($params){
        $sql  = Gudang::where('code', $params)->first();
        if($sql->count() > 0){
            foreach($sql as $q ){
                $data = $q->id;
            }
        }else{
            $data = '';
        }

        return $data;
    }

    public function tanggalImport($date) {
        $slash = explode('/',$date);
        if(count($slash) == 3) {
            $date = $slash[2].'-'.$slash[1].'-'.$slash[0];
        }
        return $date;
    }
}
