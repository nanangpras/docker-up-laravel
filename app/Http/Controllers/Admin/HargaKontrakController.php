<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\DataOption;
use App\Models\Hargakontrak;
use App\Models\Item;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class HargaKontrakController extends Controller
{
    public function index(Request $request)
    {
        if (User::setIjin(45)) {
            // BUAT CUSTOMER CGL
            $getDataOptionFresh =   DataOption::getOption('item_fresh_so');
            $dataOptionFresh    =   explode(', ', $getDataOptionFresh);
            if ($request->key == 'customerSampingan') {
                $customer           =   Customer::where('nama', '!=', '')
                                        ->where('kode', 'like', '%'. Session::get('subsidiary'). '%')
                                        ->where('netsuite_internal_id', '!=', NULL)
                                        ->where('netsuite_internal_id', '!=', 0)
                                        ->where('deleted_at', NULL)
                                        ->where(function($query) {
                                            $query->where('is_parent', 0)->orWhere('is_parent', NULL);
                                        })
                                        ->whereNotIn('id', Hargakontrak::select('customer_id')->where('subsidiary', 'like', '%'.Session::get('subsidiary').'%')->where('keterangan', '=', 'Customer Sampingan'))
                                        ->orderBy('nama')->get();

                
                $itemSampingan      =   Item::whereIn('category_id', [4,10])->
                                            where(function ($query) use ($dataOptionFresh) {
                                                foreach ($dataOptionFresh as $data) {
                                                    $query->where('nama', 'not like', '%'.$data.'%');
                                                }
                                                $query->where('nama', 'not like', '%PEJANTAN%');
                                                $query->where('nama', 'not like', '%TELUR%');
                                            })
                                            ->orWhere(function ($query) use ($dataOptionFresh) {
                                                $query->orWhere('nama', 'like', 'AY - S%');
                                            })
                                            ->where('status', '1')->get();     

                return view('admin.pages.customer.sampingan.index', compact('customer', 'itemSampingan')) ;
            } else if ($request->key == 'riwayat') {
                // dd($request->all());
                $data       =   Customer::whereIn('id', Hargakontrak::select('customer_id')->where('subsidiary', 'like', '%'.Session::get('subsidiary').'%'))
                                ->where(function($query) use ($request){
                                    if($request->customer){
                                        $query->where('id', $request->customer);
                                    }
                                })
                                ->paginate(20) ;

                $customer   =   Customer::orderBy('nama')->where('kode', 'like', '%'.Session::get('subsidiary').'%')
                                ->get() ;

                $id_cust    = $request->customer ?? '';

                return view('admin.pages.customer.harga_kontrak.riwayat', compact('data', 'customer', 'id_cust')) ;
            
            } else if ($request->key == 'riwayatCustomerSampingan') {
                $dataClone       =      Customer::where(function($query) use ($request){
                                            if($request->customer){
                                                $query->where('id', $request->customer);
                                            }
                                        })
                                        ->whereIn('id', Hargakontrak::select('customer_id')->where('subsidiary', 'like', '%'.Session::get('subsidiary').'%')->where('keterangan', '=', 'Customer Sampingan')->withTrashed())
                                        ->withTrashed();


                $dataRiwayat     =   clone $dataClone;
                $dataCustomer    =   clone $dataClone;

                $data            =   $dataRiwayat->paginate(20) ;
                $customer        =   $dataCustomer->get() ;
                // $customer   =   Customer::orderBy('nama')->where('kode', 'like', '%'.Session::get('subsidiary').'%')
                //                 ->withTrashed()
                //                 ->get() ;

                $id_cust    = $request->customer ?? '';

                return view('admin.pages.customer.sampingan.riwayat', compact('data', 'customer', 'id_cust')) ;
                
            } else if ($request->key == 'listItemSampingan') {
                $data               =   Customer::whereIn('id', Hargakontrak::select('customer_id')->where('subsidiary', 'like', '%'.Session::get('subsidiary').'%')->where('keterangan','Customer Sampingan')->where('customer_id', $request->customer)->withTrashed())
                                        ->first() ;

                $itemSampingan      =   Item::whereIn('category_id', [4,10])->
                                        where(function ($query) use ($dataOptionFresh, $request) {
                                            foreach ($dataOptionFresh as $data) {
                                                $query->where('nama', 'not like', '%'.$data.'%');
                                            }
                                            $query->where('nama', 'not like', '%PEJANTAN%');
                                            $query->where('nama', 'not like', '%TELUR%');
                                            $query->whereNotIn('id', Hargakontrak::select('item_id')->where('subsidiary', 'like', '%'.Session::get('subsidiary').'%')->where('keterangan','Customer Sampingan')->where('customer_id', $request->customer));

                                        })
                                        ->orWhere(function ($query) use ($request) {
                                            $query->where('nama', 'like', 'AY - S%');
                                            $query->whereNotIn('id', Hargakontrak::select('item_id')->where('subsidiary', 'like', '%'.Session::get('subsidiary').'%')->where('keterangan','Customer Sampingan')->where('customer_id', $request->customer));
                                        })
                                        ->where('status', '1')
                                        ->get();     

                return view('admin.pages.customer.sampingan.edit', compact('data', 'itemSampingan')) ;

            } else if($request->key == 'edit'){
                $customer = Customer::orderBy('nama')->where('kode', 'like', '%'.Session::get('subsidiary').'%')->get();
                $item     = Item::where('status', '1')->where('subsidiary', 'like', '%'.Session::get('subsidiary').'%')->get() ;
                $data     = HargaKontrak::find($request->id);
                return view('admin.pages.customer.harga_kontrak.edit', compact('data', 'customer', 'item'));

            } else if($request->key == 'hapusKontrak') {
                $data = Hargakontrak::find($request->id);
                if(!$data->delete()){
                    $result['status']   =   400 ;
                    $result['msg']      =   'Data gagal dihapus' ;
                    return $result ;
                }
                $result['status']   =   200 ;
                $result['msg']      =   'Harga kontrak berhasil dihapus' ;
                return $result ;

            } else if($request->key == 'hapusItemSampingan') {
                $data = Hargakontrak::where('id', $request->id)->withTrashed()->first();
                // return response()->json($data);
                if ($data->deleted_at != NULL) {
                    $data->restore();
                    $result['status']       =   200 ;
                    $result['msg']          =   'Berhasil mengaktifkan item' ;
                    return $result ;
                } else {
                    $data->delete();
                    $cekTotalDataCustomer       =   Hargakontrak::where('customer_id', $request->customer)->where('keterangan','Customer Sampingan')->count();

                    $result['reload']           =   $cekTotalDataCustomer == 0 ? 'true' : 'false';
                    $result['status']           =   200 ;
                    $result['msg']              =   'Berhasil nonaktifkan item' ;
                    return $result ;

                }

            } else if($request->key == 'hapusCustomerSampingan') {
                if ($request->status == 'aktif') {
                    $dataNonaktif   = Hargakontrak::where('customer_id', $request->id)->withTrashed()->get();
                    foreach ($dataNonaktif as $data) {
                        $data->restore();
                    }

                    $result['status']   =   200 ;
                    $result['msg']      =   'Berhasil mengaktifkan customer' ;
                    return $result ;

                } else {
                    $dataAktif      = Hargakontrak::where('customer_id', $request->id)->get();
                    foreach ($dataAktif as $data) {
                        $data->delete();
                    }

                    $result['status']   =   200 ;
                    $result['msg']      =   'Berhasil nonaktifkan customer' ;
                    return $result ;

                }

            } else {
                $customer   =   Customer::orderBy('nama')->where('kode', 'like', '%'.Session::get('subsidiary').'%')
                                // ->where('is_parent', '!=', NULL)
                                ->get() ;

                $item       =   Item::where('status', '1')->where('subsidiary', 'like', '%'.Session::get('subsidiary').'%')->get() ;

                $frozen     =   Item::select('items.*')->join('category', 'category.id', '=', 'items.category_id')->where(function($query) {
                                    $query->orWhere('category_id', '<=', 20);
                                    $query->orWhere('category.slug', 'like', 'ags%');
                                })
                                ->where('items.subsidiary', 'like', '%'.Session::get('subsidiary').'%')
                                ->whereNotIn('category_id', ['20', '19', '12', '13', '14', '15'])
                                ->where('items.nama', 'like', '%FROZEN%')->where('items.status', '1')
                                ->get();

                $fresh      =   Item::select('items.*')->join('category', 'category.id', '=', 'items.category_id')->where(function($query) {
                                    $query->orWhere('category_id', '<=', 20);
                                    $query->orWhere('category.slug', 'like', 'ags');
                                })
                                ->where('items.subsidiary', 'like', '%'.Session::get('subsidiary').'%')
                                ->whereNotIn('category_id', ['20', '19', '12', '13', '14', '15'])
                                ->where('items.nama', 'not like', '%FROZEN%')
                                ->where('items.status', '1')->get();

                return view('admin.pages.customer.harga_kontrak.index', compact('customer', 'item', 'fresh', 'frozen')) ;
            }


        }

        return redirect()->route('index') ;
    }


    public function store(Request $request)
    {
        if (User::setIjin(45)) {
            if ($request->key == 'storeCustomerSampingan') {
                DB::beginTransaction();
                for ($x = 0; $x < COUNT($request->item); $x++) {
                    $data                   = Hargakontrak::where('item_id', $request->item[$x])->where('customer_id', $request->cust)->withTrashed()->first();
                    if (!$data) {
                        $data               = new Hargakontrak();
                    }
                    $data->customer_id      = $request->cust;
                    $data->item_id          = $request->item[$x];
                    $data->min_berat        = $request->berat[$x];
                    $data->min_qty          = $request->qty[$x];
                    $data->keterangan       = 'Customer Sampingan';
                    $data->subsidiary       = Session::get('subsidiary');
                    if (!$data->save()) {
                        DB::rollBack() ;
                        $result['status']   =   400 ;
                        $result['msg']      =   'Proses gagal' ;
                        return $result ;
                    }
                } 

                DB::commit() ;
                $result['status']   =   200 ;
                $result['msg']      =   'Berhasil Simpan' ;
                return $result ;

            } else
            if($request->key == 'update'){
                DB::beginTransaction();

                $data = Hargakontrak::find($request->id);
                $data->customer_id = $request->customer;
                $data->item_id     = $request->item;
                $data->harga       = $request->harga;
                $data->unit        = $request->unit;
                $data->min_qty     = $request->qty;
                $data->mulai       = $request->mulai;
                $data->sampai      = $request->akhir;
                $data->keterangan  = $request->keterangan ;
                if(!$data->save()){
                    DB::rollBack() ;
                    $result['status']   =   400 ;
                    $result['msg']      =   'Data gagal diupdate' ;
                    return $result ;
                }

                DB::commit() ;
                $result['status']   =   200 ;
                $result['msg']      =   'Update harga kontrak berhasil' ;
                return $result ;

            } else {
                DB::beginTransaction() ;

                for ($x=0; $x < COUNT($request->customer); $x++) {

                    for ($y=0; $y < COUNT($request->item); $y++) {
                        if ($request->item[$y] && $request->harga[$y] && $request->mulai && $request->akhir) {
                            $kontrak                =   new Hargakontrak ;
                            $kontrak->customer_id   =   $request->customer[$x] ;
                            $kontrak->item_id       =   $request->item[$y] ;
                            $kontrak->harga         =   $request->harga[$y] ;
                            $kontrak->unit          =   $request->unit[$y] ;
                            $kontrak->min_qty       =   $request->qty[$y] ;
                            $kontrak->mulai         =   $request->mulai ;
                            $kontrak->sampai        =   $request->akhir ;
                            $kontrak->keterangan    =   $request->keterangan[$y] ;
                            $kontrak->subsidiary    =   Session::get('subsidiary');
                            if (!$kontrak->save()) {
                                DB::rollBack() ;
                                $result['status']   =   400 ;
                                $result['msg']      =   'Proses gagal' ;
                                return $result ;
                            }
                        }
                    }

                }


                DB::commit() ;
                $result['status']   =   200 ;
                $result['msg']      =   'Tambah harga kontrak berhasil' ;
                return $result ;
            }
            return redirect()->route('index');

        }
    }
}
