<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Abf;
use App\Models\Adminedit;
use App\Models\Bahanbaku;
use App\Models\Bom;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Chiller;
use App\Models\Customer;
use App\Models\CustomerBumbu;
use App\Models\Freestock;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\FreestockList;
use App\Models\FreestockTemp;
use App\Models\Item;
use App\Models\Log;
use App\Models\MarketingSO;
use App\Models\MarketingSOList;
use App\Models\Netsuite;
use App\Models\Order;
use App\Models\Bumbu;
use App\Models\BumbuDetail;
use App\Models\OrderItem;
use App\Models\Product_gudang;
use App\Models\Purchasing;
use App\Models\Returalasan;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Claims\Custom;

class ReguController extends Controller
{
    public function index(Request $request)
    {
        // if (User::setIjin(8) || User::setIjin(9) || User::setIjin(10) || User::setIjin(11) || User::setIjin(12) || User::setIjin(33)) {

            $tanggal            = $request->tanggal ?? date('Y-m-d');
            $tanggalend         = $request->tanggalend ?? $tanggal;
            $today = Carbon::today();
            $nextday=[];
            for ($i=0; $i < 7; $i++) {
                $nextday[]=$today->format('Y-m-d');
                $today->addDay();
            }

            if ($request->kategori) {

                // dd($request->kategori);
                if (($request->kategori == 'boneless') or ($request->kategori == 'parting') or ($request->kategori == 'marinasi') or ($request->kategori == 'whole') or ($request->kategori == 'frozen') or ($request->kategori == 'meyer') or ($request->kategori == 'admin-produksi') or ($request->kategori == 'byproduct')) {
                    if ($request->kategori == 'boneless') {
                        $regu   =   "Boneless";
                    }
                    if ($request->kategori == 'parting') {
                        $regu   =   "Parting";
                    }
                    if ($request->kategori == 'marinasi') {
                        $regu   =   "Parting Marinasi";
                    }
                    if ($request->kategori == 'whole') {
                        $regu   =   "Whole Chicken";
                    }
                    if ($request->kategori == 'frozen') {
                        $regu   =   "Frozen";
                    }
                    if ($request->kategori == 'meyer') {
                        $regu   =   "Meyer";
                    }
                    if ($request->kategori == 'byproduct') {
                        $regu   =   "By Product";
                    }
                    if ($request->kategori == 'admin-produksi') {
                        $regu   =   "Admin Produksi";
                    }

                    if ($request->produksi) {

                        $data   =   Freestock::where('id', $request->produksi)->first();
                        if ($data) {
                            return view('admin.pages.regu.detail', compact('request', 'regu', 'data'));

                        } else {
                            return redirect()->route("index");
                        }
                        // if (Auth::user()->account_role != 'superadmin') {

                        // } else {
                        //     dd($request->all());
                        //     if ($data->regu != $request->kategori) {
                        //         return redirect()->route("index");
                        //     } else {
                        //     }
                        // }

                    } else

                    if ($request->order) {
                        return $request->order ;
                    } else

                    {

                if ((Auth::user()->account_role != 'superadmin') and (!User::setIjin(33)) and (Auth::user()->email != 'regumeyer@cgl.com') and (Auth::user()->email != 'kepalaregu@cgl.com') and (Auth::user()->email != 'kepalaproduksi@cgl.com') and (Auth::user()->email != 'kepalaproduksi@eba.com')) {
                            if (User::setIjin(8)) {
                                if ($request->kategori != 'boneless') {
                                    // return "1";
                                    // return redirect()->route('regu.index', ['kategori' => 'boneless']);
                                }
                            }

                            if (User::setIjin(9)) {
                                if ($request->kategori != 'parting') {
                                // return redirect()->route("supplier.index");
                                    // return redirect()->route('regu.index', ['kategori' => 'parting']);
                                }
                            }
                            if (User::setIjin(10)) {
                                if ($request->kategori != 'marinasi') {
                                    // return redirect()->route('regu.index', ['kategori' => 'marinasi']);
                                }
                            }
                            if (User::setIjin(11)) {
                                if ($request->kategori != 'whole') {
                                    // return redirect()->route('regu.index', ['kategori' => 'whole']);
                                }
                            }
                            if (User::setIjin(12)) {
                                if ($request->kategori != 'frozen') {
                                    // return redirect()->route('regu.index', ['kategori' => 'frozen']);
                                }
                            }
                        }
                        return view('admin.pages.regu.show', compact('request', 'regu', 'tanggal','tanggalend','nextday'));
                    }
                }
            } else

            if ($request->key == 'sameday') {

                $regu   =   $request->regu ;

                $data   =   OrderItem::select('order_items.*', 'orders.nama', 'orders.no_so', 'orders.tanggal_kirim', 'orders.sales_id',
                                DB::raw("orders.nama as cust_nama"),
                                DB::raw("orders.keterangan as memo_header"),
                                DB::raw("orders.created_at as created_at_order"),
                                DB::raw("order_items.edited as edit_item"),
                                DB::raw("order_items.deleted_at as delete_at_item"),
                                DB::raw("order_items.edited as edit_item"),
                                DB::raw("order_items.id as id"),
                                DB::raw("orders.status_so as order_status_so"),
                                DB::raw("marketing.nama_alias as marketing_nama"),
                                DB::raw("order_items.deleted_at as delete_at_item")
                            )
                            ->leftJoin('orders', 'orders.id', '=', 'order_items.order_id')
                            ->leftJoin('items', 'items.id', '=', 'order_items.item_id')
                            ->leftJoin('marketing', 'marketing.netsuite_internal_id', '=', 'orders.sales_id')
                            // ->whereNotIn('sales_channel', ["By Product - Paket", "By Product - Retail"])
                            ->whereRaw('DATE_FORMAT(orders.created_at, "%Y-%m-%d") = orders.tanggal_kirim')
                            ->where(function($query) use ($regu) {

                                if(env('NET_SUBSIDIARY', 'CGL')=='CGL'){
                                    if ($regu == 'boneless') {
                                        $query->whereIn('items.category_id', [5, 11]) ;
                                    }
                                    if ($regu == 'parting') {
                                        $query->whereIn('items.category_id', [2]) ;
                                    }
                                    if ($regu == 'marinasi') {
                                        $query->whereIn('items.category_id', [3, 9]) ;
                                    }
                                    if ($regu == 'whole') {
                                        $query->whereIn('items.category_id', [1]) ;
                                    }
                                    if ($regu == 'frozen') {
                                        $query->whereIn('items.category_id', [7, 8, 9, 13]) ;
                                    }
                                }

                            })
                            ->whereDate('tanggal_kirim', date("Y-m-d"))
                            ->get() ;

                return view('admin.pages.regu.same_day.index', compact('data', 'regu')) ;
            } else

            if ($request->key == 'parking_orders') {
                $tanggal_mulai_parking_orders   =   $request->tanggal_mulai_parking_order ?? '';
                $tanggal_akhir_parking_orders   =   $request->tanggal_akhir_parking_order ?? '';
                $filter_parking_order           =   $request->filter_parking_order ?? '';
                $customer_parking_order         =   $request->customer_parking_order ?? '';
                $marketing_parking_order        =   $request->marketing_parking_order ?? '';
                $data                           =   MarketingSOList::select(
                                                        'marketing_so.tanggal_so',
                                                        'marketing_so.no_so',
                                                        'marketing_so.created_at as created_at_order',
                                                        'marketing_so.tanggal_kirim',
                                                        'customers.nama AS nama_customer',
                                                        'customers.id AS customer_id',
                                                        'users.name AS nama_marketing',
                                                        'users.id AS user_id',
                                                        'marketing_so_list.*'
                                                    )
                                                    ->leftJoin('marketing_so', 'marketing_so.id', '=', 'marketing_so_list.marketing_so_id')
                                                    ->leftJoin('customers', 'customers.id', '=', 'marketing_so.customer_id')
                                                    ->leftJoin('users', 'users.id', '=', 'marketing_so.user_id')
                                                    ->where(function ($query) use ($tanggal_mulai_parking_orders, $tanggal_akhir_parking_orders) {
                                                        if ($tanggal_mulai_parking_orders && $tanggal_akhir_parking_orders) {
                                                            $query->whereBetween('marketing_so.tanggal_kirim', [$tanggal_mulai_parking_orders, $tanggal_akhir_parking_orders]);
                                                        }

                                                    })
                                                    // ->where('marketing_so.no_so', '!=', NULL)
                                                    ->where('marketing_so.subsidiary', Session::get('subsidiary'))
                                                    ->where(function($query) use ($filter_parking_order) {
                                                        if ($filter_parking_order) {
                                                            $query->orWhere('marketing_so_list.item_nama', 'like', '%' . $filter_parking_order . '%');
                                                            $query->orWhere('marketing_so_list.memo', 'like', '%' . $filter_parking_order . '%');
                                                            $query->orWhere('marketing_so.no_so', 'like', '%' . $filter_parking_order . '%');
                                                            $query->orWhere('customers.nama', 'like', '%' . $filter_parking_order . '%');
                                                        }
                                                    })
                                                    ->where(function($query) use ($customer_parking_order){
                                                        if ($customer_parking_order) {
                                                            $query->orWhere('customer_id', $customer_parking_order);
                                                        }
                                                    })
                                                    ->where(function($query) use ($marketing_parking_order){
                                                        if ($marketing_parking_order) {
                                                            $query->orWhere('user_id', $marketing_parking_order);
                                                        }
                                                    });

                if ($request->get == 'unduh') {
                    $datas   =   $data->where('marketing_so.status', '!=', 3)->get() ;
                return view('admin.pages.regu.parking_order.excel', compact('datas'));
                } else {
                    $queryDataQty           = clone $data;
                    $queryDataBerat         = clone $data;
                    $queryDataCustomer      = clone $data;
                    $queryDataFresh         = clone $data;
                    $queryDataFrozen        = clone $data;
                    $queryDataSO            = clone $data;
                    $queryDataSOBelumVerif  = clone $data;
                    $queryData              = clone $data;
                    $datas                  =   $queryData->where(function($query) {
                                                        $query->where('marketing_so.status', '!=', 3);
                                                        $query->orWhere('marketing_so.status', NULL);
                                                    })->get() ;
                    $totalsum               =   [
                                                    'sumqty'          =>  $queryDataQty->where('marketing_so.status', '!=', 3)->sum('marketing_so_list.qty'),
                                                    'sumberat'        =>  $queryDataBerat->where('marketing_so.status', '!=', 3)->sum('marketing_so_list.berat'),
                                                    'sumcustomer'     =>  $queryDataCustomer->where('marketing_so.status', '!=', 3)->groupBy('marketing_so.customer_id')->get()->count('marketing_so.customer_id'),
                                                    'sumso'           =>  $queryDataSO->where('marketing_so.status', 3)->count(),
                                                    'sumsobelumverif' =>  $queryDataSOBelumVerif->where('marketing_so.status', 2)->count(),
                                                ];
                    return view('admin.pages.regu.parking_order.index', compact('datas','totalsum')) ;
                }


            } else

            if($request->key == 'customer_parking_orders'){
                $tanggal_mulai_parking_orders = $request->tanggal_mulai_parking_order ?? '';
                $tanggal_akhir_parking_orders = $request->tanggal_akhir_parking_order ?? '';
                $customer_parking_order       = $request->customer_parking_order ?? '';

                $customer   =   MarketingSOList::select('marketing_so.tanggal_so', 'customers.nama AS nama_customer', 'customers.id AS id_customer', 'marketing_so_list.*')
                            ->leftJoin('marketing_so', 'marketing_so.id', '=', 'marketing_so_list.marketing_so_id')
                            ->leftJoin('customers', 'customers.id', '=', 'marketing_so.customer_id')
                            ->where(function ($query) use ($tanggal_mulai_parking_orders, $tanggal_akhir_parking_orders) {
                                if ($tanggal_mulai_parking_orders && $tanggal_akhir_parking_orders) {
                                    $query->whereBetween('marketing_so.tanggal_kirim', [$tanggal_mulai_parking_orders, $tanggal_akhir_parking_orders]);
                                }
                            })
                            ->where('marketing_so.subsidiary', Session::get('subsidiary'))
                            ->groupBy('id_customer')
                            ->get();
                return view('admin.pages.regu.parking_order.customer', compact('customer','customer_parking_order')); ;
            } else

            if($request->key == 'marketing_parking_orders'){
                $tanggal_mulai_parking_orders = $request->tanggal_mulai_parking_order ?? '';
                $tanggal_akhir_parking_orders = $request->tanggal_akhir_parking_order ?? '';
                $marketing_parking_order       = $request->marketing_parking_order ?? '';

                $marketing   =   MarketingSOList::select('marketing_so.tanggal_so', 'users.name AS nama_marketing', 'users.id AS id_user', 'marketing_so_list.*')
                            ->leftJoin('marketing_so', 'marketing_so.id', '=', 'marketing_so_list.marketing_so_id')
                            ->leftJoin('users', 'users.id', '=', 'marketing_so.user_id')
                            ->where(function ($query) use ($tanggal_mulai_parking_orders, $tanggal_akhir_parking_orders) {
                                if ($tanggal_mulai_parking_orders && $tanggal_akhir_parking_orders) {
                                    $query->whereBetween('marketing_so.tanggal_kirim', [$tanggal_mulai_parking_orders, $tanggal_akhir_parking_orders]);
                                }
                            })
                            ->where('marketing_so.subsidiary', Session::get('subsidiary'))
                            ->groupBy('id_user')
                            ->get();
                return view('admin.pages.regu.parking_order.marketing', compact('marketing','marketing_parking_order')); ;
            } else

            if ($request->key == 'data_produksi') {
                $data   =   Freestock::with('freetemp')->find($request->produksi);
                $cs     =   Customer::all();
                $plastik            =   Item::where('category_id', 25)->where('subsidiary', env('NET_SUBSIDIARY', 'EBA'))->pluck('nama', 'id') ;
                return view('admin.pages.regu.detail_produksi', compact('data', 'cs', 'plastik'));
            } else

            if ($request->key == 'selesaikan') {
                $freestock  =   Freestock::where('status', 1)
                    // ->where(function($query) use ($request) {
                    //     if ($request->orderitem) {
                    //         $query->where('orderitem_id', $request->orderitem) ;
                    //     } else {
                    //         $query->where('orderitem_id', NULL) ;
                    //         if ((Auth::user()->account_role != 'superadmin') || (!User::setIjin(33))) {
                    //             $query->where('user_id', Auth::user()->id);
                    //         }
                    //     }
                    // })
                    ->where('regu', $request->kat)
                    ->orderBy('id','desc')
                    ->first();

                return view('admin.pages.regu.component.selesaikan', compact('freestock'));
            } else

            if ($request->key == 'bahan_baku') {
                $bahan_baku =   Chiller::where('type', 'bahan-baku')
                                ->whereIn('asal_tujuan', ['baru', 'free_stock', 'retur', 'thawing', 'open_balance'])
                                ->where('stock_berat', '>', '0')
                                ->where('status', 2)
                                ->get();

                $freestock  =   Freestock::with(['listfreestock', 'listfreestock.chiller'])->where('status', 1)
                                ->where('regu', $request->kat)
                                ->orderBy('id','desc')
                                ->first();

                return view('admin.pages.regu.component.ambil_bb', compact('bahan_baku', 'freestock', 'request', 'tanggal'));
            } else

            if ($request->key == 'hasil_produksi') {

                if ($request->kat == 'boneless') {
                    $id_assembly    =   BOM::bom_netid(env("NET_SUBSIDIARY", "CGL") . ' - KARKAS - BONELESS BROILER');
                } else
                if ($request->kat == 'parting') {
                    $id_assembly    =   BOM::bom_netid(env("NET_SUBSIDIARY", "CGL") . ' - AYAM PARTING BROILER');
                } else
                if ($request->kat == 'marinasi') {
                    $id_assembly    =   BOM::bom_netid(env("NET_SUBSIDIARY", "CGL") . ' - AYAM PARTING MARINASI BROILER');
                } else
                if ($request->kat == 'whole') {
                    $id_assembly    =   BOM::bom_netid(env("NET_SUBSIDIARY", "CGL") . ' - AYAM KARKAS BROILER');
                } else
                if ($request->kat == 'frozen') {
                    $id_assembly    =   BOM::bom_netid(env("NET_SUBSIDIARY", "CGL") . ' - AYAM KARKAS FROZEN');
                }

                $plastik    =   Bom::where('netsuite_internal_id', $id_assembly)->first();

                $jenis_plastik = Item::where('category_id', '25')
                                ->where('subsidiary', env('NET_SUBSIDIARY', 'EBA'))
                                ->where('status', '1')
                                ->where(function ($query) use ($request) {
                                    $query->where('access', 'LIKE', '%' . $request->kat . '%');
                                })
                                ->get();
                                

                // $customer   =   Customer::all();

                // return view('admin.pages.regu.component.hasil_produksi', compact('request', 'plastik', 'tanggal', 'customer'));
                
                $customer           =   Customer::with('customer_bumbu')->get();
                $bumbu              =   Bumbu::all();
                $customer_bumbu     =   CustomerBumbu::all();

                return view('admin.pages.regu.component.hasil_produksi', compact('request', 'plastik', 'tanggal', 'customer','bumbu','customer_bumbu','jenis_plastik'));
            } else

            if ($request->key == 'finished_good') {

                $freestock  =   FreestockTemp::with(['item', 'konsumen'])
                                ->where('regu', $request->kat)
                                ->whereIn(
                                    'freestock_id',Freestock::select('id')
                                                    ->where('regu', $request->kat)
                                                    ->where('status', 1)
                                                    ->orderBy('id', 'desc')
                                )
                                ->get();

                return view('admin.pages.regu.component.hasil_jadi', compact('request', 'freestock', 'tanggal'));
            } else

            if ($request->key == 'daftar_order') {
                $data   =   OrderItem::where('item_id', $request->item)
                            ->leftJoin('orders', 'orders.id', '=', 'order_items.order_id')
                            ->where('customer_id', $request->cust)
                            ->where('order_items.status', NULL)
                            ->whereNotIn('sales_channel', ["By Product - Paket", "By Product - Retail"])
                            ->orderBy('tanggal_kirim', 'DESC')
                            ->get();

                return view('admin.pages.regu.component.waiting_order', compact('data'));
            } else

            if ($request->key == 'hasil_harian') {

                $tanggal    =   $request->tanggal ?? date('Y-m-d');
                $cari       =   $request->cari ?? '';

                $clone  =   Freestock::with(['listfreestock', 'freetemp', 'listfreestock.item', 'getHistoryDeleteList' , 'freetemp.konsumen', 'listfreestock.chiller', 'freetemp.item', 'getHistoryDeleteTemp', 'orderitem', 'orderitem.itemorder', 'freetemp.tempchiller'])
                                ->select('free_stock.*')
                                ->where(function ($query) use ($cari){
                                    if($cari){
                                        $query->orWhere('items.nama', 'like', '%'.$cari.'%');
                                        $query->orWhere('free_stocktemp.plastik_nama', 'like', '%'.$cari.'%');
                                        $query->orWhere('customers.nama', 'like', '%'.$cari.'%');
                                        $query->orWhere('free_stocktemp.prod_nama', 'like', '%'.$cari.'%');
                                    }
                                })
                                ->where(function($query) use ($request, $tanggal) {
                                    if ($request->orderitem) {
                                        $query->where('free_stock.orderitem_id', $request->orderitem) ;
                                    } else {
                                        $query->where('free_stock.regu', $request->kat)->whereDate('tanggal', $tanggal);
                                    }
                                })
                                ->where(function($query) use ($request) {
                                    if ($request->selesaikan == 'on') {
                                        $query->where('free_stock.status', 2) ;
                                    } else {
                                        $query->whereIn('free_stock.status', [2, 3]) ;
                                    }
                                })
                                ->where(function($query) use ($request) {
                                    if ($request->selonjor == 'on') {
                                        $query->where('free_stocktemp.selonjor', 1) ;
                                    }
                                })

                                ->orderBy('free_stock.id', 'DESC')
                                ->groupBy('free_stock.id');


                $dataget    =   clone $clone;
                $freestock  =   $dataget->get();
                $datalog    =   clone $clone;
                $datacek    =   $datalog->get();
                $kategori   =   $request->kat;
                $customer   =   Customer::all();
                $item_list  =   Item::whereNotIn('category_id', ['21', '22', '23', '24', '25', '26', '27', '28', '29', '30'])->get();
                $bumbu      =   Bumbu::all();
                
                $ceklogs='';
                foreach ($datacek as $row) {
                    $ceklogs = Freestock::where('tanggal', $row->tanggal)
                    ->whereNotNull('deleted_at')
                    ->where('regu',$row->regu)
                    ->withTrashed()
                    ->get();
                }
                $ceklogdelete=json_decode($ceklogs);

                if ($request->subkey == 'logdelete') {
                    $datalist = Adminedit::where('key',$request->tanggal)->where('activity',$request->regu)->get();
                    $frestok = Freestock::withTrashed()->with(['listfreestock','freetemp'])->where('tanggal',$request->tanggal)->whereNotNull('deleted_at')->get();
                    // dd($frestok);
                    return view('admin.pages.regu.log.history_delete_hasilharian',compact('datalist','frestok','request','tanggal'));
                }

                $netsuite = Netsuite::where('label', 'like', '%'.$request->kat.'%')->where('label', '!=', 'item_receipt_frozen')->where('trans_date', $tanggal)->get();
                // return view('admin.pages.regu.component.hasil_harian', compact('request', 'freestock', 'tanggal', 'progress', 'kategori', 'item_list', 'customer','ceklogdelete','bumbu'));
                return view('admin.pages.regu.component.hasil_harian_new', compact('request', 'freestock', 'tanggal', 'kategori', 'item_list', 'customer','ceklogdelete','bumbu','netsuite'));
            } else

            if ($request->key == 'item_boneless') {
                $idcategory     = array();
                $query          = Category::where('nama','LIKE','%boneless%')->get();
                if($query->count() > 0){
                    foreach($query as $lp){
                        $idcategory[]  = $lp->id;
                    }
                }
                $newData            = $idcategory;
                if($newData){
                    $stringData     = implode(",",$newData);
                    $item           = Item::where(function($q){
                                                $q->where('subsidiary','LIKE', '%'.Session::get('subsidiary').'%');
                                                $q->orWhere('code_item','LIKE','%'.Session::get('subsidiary').'%');
                                            })
                                            ->where(function($q2) use ($stringData){
                                                $q2->whereIn('category_id',[$stringData]);
                                            })
                                            ->where(function ($query) use ($request) {
                                                if ($request->tipe == 'broiler') {
                                                    $query->where('slug', 'LIKE', '%broiler');
                                                }
                                                if ($request->tipe == 'pejantan') {
                                                    $query->where('slug', 'LIKE', '%pejantan');
                                                }
                                                if ($request->tipe == 'kampung') {
                                                    $query->where('slug', 'LIKE', '%kampung');
                                                }
                                                if ($request->tipe == 'parent') {
                                                    $query->where('slug', 'LIKE', '%parent');
                                                }
                                            })
                                            ->orWhere('access','LIKE','%boneless%')
                                            ->pluck('nama', 'id');
                }else{
                    return [];
                }

                return view('admin.pages.regu.component.item_boneless', compact('item', 'tanggal'));
            } else

            if ($request->key == 'history_delete_bb') {
                $history = FreestockList::where('freestock_id',$request->produksi)->whereNotNull('deleted_at')->withTrashed()->get();
                return view('admin.pages.regu.history_delete_bahanbaku',compact('history'));
            }else

            if ($request->key == 'stockbyitem') {

                $listayam4feb       = Product_gudang::select('nama', DB::raw('SUM(qty) AS total'), DB::raw('SUM(berat) AS kg'))
                ->where('production_date', '>=', '2022-02-04')
                ->where('status', 2)
                // ->where('qty' ,'>', 0)
                ->orderBy("nama")
                ->groupBy('nama')
                ->get();

                // dd($listayam4feb);
                return view('admin.pages.regu.stockbyitem.index',compact('listayam4feb'));
            }else

            // if ($request->key = 'history_delete_hp') {
            //     $history = FreestockTemp::where('freestock_id',$request->produksi)->where('deleted_at','!=','')->withTrashed()->get();
            //     return view('admin.pages.regu.history_delete_hasilproduksi',compact('history'));

            if ($request->key == 'history_delete_hp') {
                $history = FreestockTemp::where('freestock_id',$request->produksi)->whereNotNull('deleted_at')->withTrashed()->get();
                // if ($history->bumbu_detail_id) {
                //     $bumbu       = Bumbu::find($history->bumbu_id);
                //     $berat_bumbu = BumbuDetail::find($history->bumbu_detail_id);
                //     $bumbu->berat += $berat_bumbu->berat;
                //     // dd($bumbu);
                //     $bumbu->save();
                //     $berat_bumbu->delete();
                // }

                return view('admin.pages.regu.history_delete_hasilproduksi',compact('history'));

            } else
                if ($request->key == 'dashboardregu') {
                    $regu           = $request->regu;
                    $tanggal_awal   = $request->tanggal_awal ?? date("Y-m-d");
                    $tanggal_akhir  = $request->tanggal_akhir ?? date("Y-m-d") ;
                    if ($regu == 'Boneless') {
                        $kategori   =   "boneless";
                    }
                    if ($regu == 'Parting') {
                        $kategori   =   "parting";
                    }
                    if ($regu == 'Parting Marinasi') {
                        $kategori   =   "marinasi";
                    }
                    if ($regu == 'Whole Chicken') {
                        $kategori   =   "whole";
                    }
                    if ($regu == 'Frozen') {
                        $kategori   =   "frozen";
                    }

                    $bb_regu           =   FreestockList::select(DB::raw('SUM(free_stocklist.qty) AS total'), DB::raw('SUM(free_stocklist.berat) AS kg'), 'free_stocklist.item_id', 'chiller.asal_tujuan', 'chiller.tanggal_produksi', 'free_stocklist.bb_kondisi')
                                            ->leftJoin('chiller', 'chiller.id', '=', 'free_stocklist.chiller_id')
                                            ->whereIn('free_stocklist.freestock_id', Freestock::select('id')->whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])->where('status', 3))
                                            ->where('free_stocklist.regu', $kategori)
                                            ->groupBy('free_stocklist.item_id', 'chiller.asal_tujuan', 'chiller.tanggal_produksi', 'free_stocklist.bb_kondisi')
                                            ->get();

                    $fg_regu           =   FreestockTemp::select(DB::raw('SUM(qty) AS total'), DB::raw('SUM(berat) AS kg'), DB::raw('SUM(plastik_qty) AS plastik'), 'item_id')
                                            ->where('regu', $kategori)
                                            ->whereIn('freestock_id', Freestock::select('id')->whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])->where('status', 3))
                                            ->groupBy('item_id')
                                            ->get();

                    $waktu_awal_regu   =   FreestockList::where('regu', $kategori)
                                            ->whereIn('freestock_id', Freestock::select('id')->whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])->where('status', 3))
                                            ->orderBy('id', 'asc')
                                            ->where('created_at', 'like', '%'.$tanggal.'%')
                                            ->first();

                    $waktu_akhir_regu  =   FreestockTemp::where('regu', $kategori)
                                            ->whereIn('freestock_id', Freestock::select('id')->whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])->where('status', 3))
                                            ->orderBy('id', 'desc')
                                            ->where('created_at', 'like', '%'.$tanggal.'%')
                                            ->first();

                    $jam_kerja_regu    =    ((strtotime($waktu_akhir_regu->created_at ?? "00:00:00") - strtotime($waktu_awal_regu->created_at ?? "00:00:00")));

                    $waktu_regu        =    array(
                                                'awal'          => (date('H:i:s', strtotime($waktu_awal_regu->created_at ?? "00:00:00")) ?? "-" ),
                                                'akhir'         => (date('H:i:s', strtotime($waktu_akhir_regu->created_at ?? "00:00:00")) ?? "-" ),
                                                'jam_kerja'     => $jam_kerja_regu
                                            );

                    $produksi          = [
                        'bb_regu'          =>  $bb_regu,
                        'bb_tt_regu'       =>  $bb_regu->sum('kg'),
                        'bb_qty_regu'      =>  $bb_regu->sum('total'),
                        'fg_regu'          =>  $fg_regu,
                        'fg_tt_regu'       =>  $fg_regu->sum('kg'),
                        'fg_qty_regu'      =>  $fg_regu->sum('total'),
                        'fg_pe_regu'       =>  $fg_regu->sum('plastik'),
                        'waktu_regu'       =>  $waktu_regu,
                    ];

                    if ($request->view == 'loadDashboardRegu') {
                        return view('admin.pages.regu.loadDashboardRegu', compact('regu', 'kategori', 'tanggal_awal', 'tanggal_akhir', 'produksi'));
                    } else {
                        return view('admin.pages.regu.dashboardregu', compact('regu', 'kategori', 'tanggal_awal', 'tanggal_akhir', 'produksi'));
                    }
            } else {
                # code...
                return view('admin.pages.regu.index', compact('tanggal'));
            }
        // }
        // return redirect()->route("index");
    }


    public function bahanbaku(Request $request)
    {
        $kategori       = $request->input('kategori');
        $tanggal        = $request->tanggal ?? date('Y-m-d');
        $tanggal_end    = $request->tanggal_end ?? date('Y-m-d');
        $sql            =   Chiller::select('chiller.*', 'customers.nama')
                            ->where(function ($query) use ($request, $tanggal, $tanggal_end) {
                                if (!$request->tanggal_end) {
                                    $query->where('tanggal_produksi', $tanggal);
                                } else {
                                    $query->whereBetween('tanggal_produksi', [$tanggal, $tanggal_end]);
                                }

                            })
                            ->leftJoin('customers', 'customers.id', '=', 'chiller.customer_id')
                            ->where(function ($query) use ($request) {
                                $query->orWhere('item_name', 'LIKE', "%" . $request->search . "%");
                                $query->orWhere('asal_tujuan', 'LIKE', "%" . $request->search . "%");
                                $query->orWhere('label', 'LIKE', "%" . $request->search . "%");
                                $query->orWhere('customers.nama', 'LIKE', "%" . $request->search . "%");
                            })
                            ->where(function($query) use ($request) {
                                if ($request->karkas == 'true') {
                                    $query->orWhere('asal_tujuan', 'gradinggabungan');
                                    $query->orWhere('asal_tujuan', 'evisgabungan');
                                    $query->orWhere('asal_tujuan', 'karkasbeli');
                                }

                                if ($request->non_karkas == 'true') {
                                    $query->orWhere('asal_tujuan', 'free_stock');
                                    $query->orWhere('asal_tujuan', 'hasilbeli');
                                }

                                if ($request->bb_retur == "true") {
                                    $query->orWhere('asal_tujuan', 'retur');
                                }

                                if ($request->bb_thawing == "true") {
                                    $query->orWhere('asal_tujuan', 'thawing');
                                }

                                if ($request->bb_abf == "true") {
                                    $query->where('chiller.kategori', 1);
                                }
                            })
                            ->where(function ($query) use ($request) {
                                if($request->search2=="stock"){
                                    $query->orWhere('stock_berat', '>', 0);
                                }
                                if($request->type == "false"){
                                    $query->where('type','bahan-baku'); 
                                }
                                if($request->type == "true"){
                                    $query->where('type','hasil-produksi');
                                }
                            })
                            ->where(function($qs){
                                // $qs->Orwhere('chiller.stock_item', '>', 0);
                                $qs->Orwhere('chiller.stock_berat', '>', '0.01');
                                $qs->where('chiller.stock_berat', 'NOT LIKE','%-%');
                            })
                            ->where('status_cutoff',NULL)
                            ->where('chiller.status', 2)
                            ->orderBy('item_name', 'ASC');

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
                $alokasi    = DB::select("select chiller_out,SUM(bb_item) AS total_qty_alokasi, ROUND(sum(bb_berat),2) AS total_berat_alokasi 
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
                                    GROUP BY chiller_id");
                $musnahkan  = DB::select("select item_id, SUM(qty) AS total_qty_musnahkan, ROUND(sum(berat),2) AS total_berat_musnahkan 
                                    FROM musnahkan_temp JOIN musnahkan on musnahkan.id=musnahkan_temp.musnahkan_id WHERE gudang_id IN (2,4,23,24) AND item_id IN(".$stringData.")
                                    AND musnahkan.deleted_at IS NULL GROUP BY item_id ");
            }
            $arraymodification      = [];
            foreach($arrayData as $data){
                $total_qty_alokasi          = 0;
                $total_berat_alokasi        = 0;
                $total_qty_abf              = 0;
                $total_berat_abf            = 0;
                $total_qty_freestock        = 0;
                $total_berat_freestock      = 0;
                $total_qty_musnahkan        = 0;
                $total_berat_musnahkan      = 0;
    
                foreach($alokasi as $val){
                    if($data->id == $val->chiller_out){
                        $total_qty_alokasi      = $val->total_qty_alokasi;
                        $total_berat_alokasi    = floatval($val->total_berat_alokasi) ?? 0;
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
                    "id"                        => $data->id,
                    "production_id"             => $data->production_id,
                    "table_name"                => $data->table_name,
                    "table_id"                  => $data->table_id,
                    "asal_tujuan"               => $data->asal_tujuan,
                    "item_id"                   => $data->item_id,
                    "item_name"                 => $data->item_name,
                    "jenis"                     => $data->jenis,
                    "type"                      => $data->type,
                    "kategori"                  => $data->kategori,
                    "regu"                      => $data->regu,
                    "label"                     => $data->label,
                    "plastik_sku"               => $data->plastik_sku,
                    "plastik_nama"              => $data->plastik_nama,
                    "plastik_qty"               => $data->plastik_qty,
                    "plastik_group"             => $data->plastik_group,
                    "parting"                   => $data->parting,
                    "sub_item"                  => $data->sub_item,
                    "selonjor"                  => $data->selonjor,
                    "kode_produksi"             => $data->kode_produksi,
                    "unit"                      => $data->unit,
                    "customer_id"               => $data->customer_id,
                    "qty_item"                  => floatval($data->qty_item),
                    "berat_item"                => floatval($data->berat_item),
                    "tanggal_potong"            => $data->tanggal_potong,
                    "no_mobil"                  => $data->no_mobil,
                    "tanggal_produksi"          => $data->tanggal_produksi,
                    "keranjang"                 => $data->keranjang,
                    "berat_keranjang"           => $data->berat_keranjang,
                    "stock_item"                => $data->stock_item,
                    "stock_berat"               => $data->stock_berat,
                    "status"                    => $data->status,
                    "status_cutoff"             => $data->status_cutoff,
                    "key"                       => $data->key,
                    "created_at"                => $data->created_at ? date('Y-m-d H:i:s', strtotime($data->created_at)) : null,
                    "updated_at"                => $data->updated_at ? date('Y-m-d H:i:s', strtotime($data->updated_at)) : null,
                    "deleted_at"                => $data->deleted_at ? date('Y-m-d H:i:s', strtotime($data->deleted_at)) : null,
                    "nama"                      => $data->nama,
                    'total_qty_alokasi'         => $total_qty_alokasi,
                    'total_berat_alokasi'       => $total_berat_alokasi,
                    'total_qty_abf'             => $total_qty_abf,
                    'total_berat_abf'           => $total_berat_abf,
                    'total_qty_freestock'       => $total_qty_freestock,
                    'total_berat_freestock'     => $total_berat_freestock,
                    'total_qty_musnahkan'       => $total_qty_musnahkan,
                    'total_berat_musnahkan'     => $total_berat_musnahkan,
                    'sisaQty'                   => $data->qty_item - $total_qty_alokasi - $total_qty_abf - $total_qty_freestock - $total_qty_musnahkan,
                    'sisaBerat'                 => $data->berat_item - $total_berat_alokasi - $total_berat_abf - $total_berat_freestock - $total_berat_musnahkan

                ];
            }
            $databahanbaku              = json_decode(json_encode($arraymodification));
            // dd($databahanbaku);
            $bahan_baku                 = $this->paginate($databahanbaku,10);

            if(isset($request->inputID)){
                $inputID = $request->inputID;
            }else{
                $inputID = "";
            }

        return view('admin.pages.regu.component.list_bb', compact('bahan_baku','inputID','kategori'));
    }


    public function ambilbb(Request $request)
    {
        $kategori   =   $request->kategori;
        $orderitem  =   $request->orderitem ?? NULL;

        DB::beginTransaction();

        $freestock  =   Freestock::where(function ($query) use ($request, $kategori) {
                            if ($request->produksi) {
                                $query->where('id', $request->produksi);
                            } else {
                                $query->where('regu', $kategori)->where('status', 1);

                                // if ((Auth::user()->account_role != 'superadmin') || (!User::setIjin(33))) {
                                //     $query->where('user_id', Auth::user()->id);
                                // }
                            }
                        })
                        ->first();

        if (!$freestock) {
            $freestock                  =   new Freestock;
            $freestock->nomor           =   Freestock::get_nomor();
            $freestock->tanggal         =   Carbon::now();
            $freestock->user_id         =   Auth::user()->id;
            $freestock->regu            =   $kategori;
            $freestock->orderitem_id    =   $orderitem;
            $freestock->status          =   1;
            if (!$freestock->save()) {
                DB::rollBack();
                return back()->with('status', 2)->with('message', 'Proses gagal');
            }
        }
        $available_jenis_bb = [];

        foreach ($freestock->listfreestock as $row) :
            if (!in_array(($row->chiller->type ?? ''), $available_jenis_bb)) {
                $available_jenis_bb[] = $row->chiller->type ?? '';
            }
        endforeach;

        for ($x = 0; $x < COUNT($request->x_code); $x++) {

            if ($request->berat[$x]) {
                $chiller                        =   Chiller::find($request->x_code[$x]);
                try {
                    Chiller::recalculate_chiller($chiller->id);
                } catch (\Throwable $th) {

                }

                $sisaQtyChiller             = Chiller::ambilsisachiller($chiller->id,'qty_item','qty','bb_item');
                $sisaBeratChiller           = Chiller::ambilsisachiller($chiller->id,'berat_item','berat','bb_berat');
                $convertSisaBerat           = number_format((float)$sisaBeratChiller, 2, '.', '');
                
                if($kategori != 'boneless'){
                    if ($request->qty[$x] > $sisaQtyChiller) {
                        DB::rollBack() ;
                        return back()->with('status', 2)->with('message', 'Proses gagal, bahan baku mungkin sudah digunakan regu lain')->with('tabs', $request->type_input);
                    }
                }

                if ($request->berat[$x] > $convertSisaBerat) {
                    DB::rollBack() ;
                    return back()->with('status', 2)->with('message', 'Proses gagal, bahan baku mungkin sudah digunakan regu lain')->with('tabs', $request->type_input);
                }

                $list                           =   new FreestockList;
                $list->freestock_id             =   $freestock->id;
                $list->chiller_id               =   $chiller->id;
                $list->item_id                  =   $chiller->item_id;

                if ($chiller->asal_tujuan == "gradinggabungan") {
                    if ($chiller->tanggal_produksi >= date('Y-m-d', strtotime($freestock->tanggal))) {
                        $list->bb_kondisi       =   "baru";
                    } else {
                        $list->bb_kondisi       =   "lama";
                    }
                } else {
                    $list->bb_kondisi           =   $chiller->asal_tujuan;
                }

                $list->qty                      =   $request->qty[$x];
                $list->regu                     =   $freestock->regu ;
                $list->berat                    =   $request->berat[$x];
                $list->sisa                     =   $request->qty[$x];
                $list->catatan                  =   $request->catatan[$x];
                if (!$list->save()) {
                    DB::rollBack();
                    return back()->with('status', 2)->with('message', 'Proses gagal');
                }
                // return $list;

                if ($freestock->status == 3) {
                    $outchiller                     =   new Chiller;
                    $outchiller->table_name         =   'free_stocklist';
                    $outchiller->table_id           =   $list->id;
                    $outchiller->asal_tujuan        =   'free_stock';
                    $outchiller->item_id            =   $list->item_id;
                    $outchiller->item_name          =   $list->item->nama;
                    $outchiller->jenis              =   'keluar';
                    $outchiller->type               =   'pengambilan-bahan-baku';
                    $outchiller->regu               =   $freestock->regu;
                    $outchiller->no_mobil           =   $list->chiller->no_mobil;
                    $outchiller->qty_item           =   $list->qty;
                    $outchiller->berat_item         =   $list->berat;
                    $outchiller->stock_item         =   $list->qty;
                    $outchiller->stock_berat        =   $list->berat;
                    $outchiller->tanggal_potong     =   $freestock->tanggal;
                    $outchiller->tanggal_produksi   =   $freestock->tanggal;
                    $outchiller->status             =   4;
                    if (!$outchiller->save()) {
                        DB::rollBack();
                        return back()->with('status', 2)->with('message', 'Proses gagal');
                    }

                    $list->outchiller               =   $outchiller->id;
                    if (!$list->save()) {
                        DB::rollBack();
                        return back()->with('status', 2)->with('message', 'Proses gagal');
                    }

                    $chill                          =   Chiller::find($list->chiller_id);
                    
                    $chill->stock_berat             =   $chill->stock_berat - $list->berat;
                    $chill->stock_item              =   $chill->stock_item - $list->qty;
                    if (!$chill->save()) {
                        DB::rollBack();
                        return back()->with('status', 2)->with('message', 'Proses gagal');
                    }


                }
            }
        }

        DB::commit();
        // try {
        //     Chiller::recalculate_chiller($chill->id);
        // } catch (\Throwable $th) {

        // }
        return back()->with('status', 1)->with('message', 'Tambah bahan baku berhasil')->with('tabs', $request->type_input);
    }

    public function viewmodaledit(Request $request){
        if($request->key == 'viewmodaledit'){
            $id             = $request->id;
            $nama           = $request->nama;
            $qty            = $request->qty;
            $berat          = $request->berat;
            $sisaQty        = Chiller::ambilsisachiller($request->chiller_id,'qty_item','qty','bb_item',$request->id);
            $calsisaBerat   = Chiller::ambilsisachiller($request->chiller_id,'berat_item','berat','bb_berat',$request->id);
            $sisaBerat      = number_format((float)$calsisaBerat, 2, '.', '');

            return view('admin.pages.regu.component.modal.modal_bb_edit',compact('id','nama','qty','berat','sisaQty','sisaBerat'));
        }
        if($request->key == 'viewmodaleditevis'){
            $id             = $request->id;
            $nama           = $request->nama;
            $qty            = $request->qty;
            $berat          = $request->berat;
            $sisaQty        = Chiller::ambilsisachiller($request->chiller_id,'qty_item','qty','bb_item',$request->id);
            $calsisaBerat   = Chiller::ambilsisachiller($request->chiller_id,'berat_item','berat','bb_berat',$request->id);
            $sisaBerat      = number_format((float)$calsisaBerat, 2, '.', '');
            return view('admin.pages.regu.component.modal.modal_bb_edit_evis',compact('id','nama','qty','berat','sisaQty','sisaBerat'));
        }
    }
    public function editproduksi(Request $request)
    {
        if ($request->key == 'bahan_baku') {
                $freestocklist      =   FreestockList::find($request->x_code);
                $itemName           =   Item::where('id',$freestocklist->item_id)->select('nama')->first();
                if ($freestocklist) {
                    DB::beginTransaction();

                    $sisaQty    = Chiller::ambilsisachiller($freestocklist->chiller_id,'qty_item','qty','bb_item',$request->x_code);
                    $sisaBerat  = Chiller::ambilsisachiller($freestocklist->chiller_id,'berat_item','berat','bb_berat',$request->x_code);
                    $convertSisaBerat   = number_format((float)$sisaBerat, 2, '.', '');
                    
                    // if ($request->qty != NULL) {
                    //     if ($request->qty > $sisaQty) {
                    //         DB::rollBack();
                    //         return back()->with('status', 2)->with('message', 'Qty melebihi batas maksimum!');
                    //     }
                    // }
                    
                    if ($request->berat != NULL) {
                        if ($request->berat > $convertSisaBerat) {
                            DB::rollBack();
                            return back()->with('status', 2)->with('message', 'Berat melebihi batas maksimum!');
                        }
                    }


                    $outchiller                     =   Chiller::find($freestocklist->outchiller);
                    if ($outchiller) {

                        $outchiller->stock_item         =   ($outchiller->stock_item - $freestocklist->qty) + $request->qty;
                        $outchiller->stock_berat        =   ($outchiller->stock_berat - $freestocklist->berat) + $request->berat;

                        if (!$outchiller->save()) {
                            DB::rollBack();
                            return back()->with('status', 2)->with('message', 'Proses gagal');
                        }

                    }
                    $getchiller                     =   Chiller::find($freestocklist->chiller_id);

                    if ($getchiller) {
                        $getchiller->qty_item           =   (($getchiller->qty_item + ($freestocklist->qty - $request->qty)) - ($freestocklist->qty - $request->qty)) ;
                        $getchiller->berat_item         =   (($getchiller->berat_item + ($freestocklist->berat - $request->berat)) - ($freestocklist->berat - $request->berat)) ;
                        $getchiller->stock_item         =   ($getchiller->stock_item + $freestocklist->qty) - $request->qty;
                        $getchiller->stock_berat        =   ($getchiller->stock_berat + $freestocklist->berat) - $request->berat;
                        if (!$getchiller->save()) {
                            DB::rollBack();
                            return back()->with('status', 2)->with('message', 'Proses gagal');
                        }
                    }

                    $origin = [
                        "freestock_id"      =>  $freestocklist->id,
                        "item_id"           =>  $freestocklist->item_id,
                        "item_name"         =>  $itemName->nama,
                        "qty"               =>  $freestocklist->qty,
                        "berat"             =>  $freestocklist->berat,
                    ];

                    $freestocklist->qty     =   $request->qty;
                    $freestocklist->sisa    =   $request->qty;
                    $freestocklist->berat   =   $request->berat;
                    if (!$freestocklist->save()) {
                        DB::rollBack();
                        return back()->with('status', 2)->with('message', 'Proses gagal');
                    }


                    $updated=[
                        "freestock_id"      =>$freestocklist->id,
                        "item_id"           =>$freestocklist->item_id,
                        "item_name"         => $itemName->nama,
                        "qty"               =>$request->qty,
                        "berat"             =>$request->berat,
                    ];
                    // if ($itemName) {

                    // } else {
                    //     DB::rollBack();
                    //     return back()->with('status', 2)->with('message', 'Proses gagal, item tidak ditemukan');
                    // }

                    // $change =   $updated['qty'] == $origin['qty'] && $updated['berat'] == $origin['berat'];
                    // $freestock_status =  $freestocklist->free_stock->status;
                    // if($change == false &&  $freestock_status  != 2 ){
                        $log                        =   new Adminedit();
                        $log->user_id               =   Auth::user()->id ;
                        $log->table_name            =   'chiller' ;
                        $log->table_id              =   $freestocklist->id ;
                        $log->type                  =   'edit' ;
                        $log->activity              =   'kepala_regu_bb';
                        $log->content               =   'Edit Ambil Bahan Baku' . ' '  . date("Y-m-d H:i:s");
                        $log->data                  =   json_encode([
                                'before_update'     => $origin,
                                'after_update'      => $updated
                        ]) ;
                        if (!$log->save()) {
                            DB::rollBack();
                            return back()->with('status', 2)->with('message', 'Proses simpan log gagal');
                        }
                    // }
                    DB::commit();
                    if ($getchiller) {
                        Chiller::recalculate_chiller($getchiller->id);
                    }

                    if ($outchiller) {
                        Chiller::recalculate_chiller($outchiller->id);
                    }
                    return back()->with('status', 1)->with('message', 'Ubah ambil bahan baku berahsil');
                }
                return back()->with('status', 2)->with('message', 'Proses gagal');
        } else

            if ($request->key == 'hasil_produksi') {
                $cariplastik        =   Item::where('nama', $request->plastik)->first();
                $freestocktemp      =   FreestockTemp::with('bumbu','bumbu_detail')->find($request->x_code);
                // dd($request->all() , $freestocktemp);

                // dd($bumbu);

                $itemName=DB::table('items')->where('id',$freestocktemp->item_id)->select('nama')->first();
                // $freestocktemplog   =   FreestockTemp::find($request->x_code);

                $origin =[
                    "freestock_temp"      =>$freestocktemp->id,
                    "item_id"             =>$freestocktemp->item_id,
                    "item_name"           => $itemName->nama,
                    "jumlah_parting"      =>$freestocktemp->parting,
                    "qty"                 =>$freestocktemp->qty,
                    "berat"               =>$freestocktemp->berat,
                    "lokasi"              =>$freestocktemp->kategori,
                    "plastik"             =>[
                                            "sku" =>$freestocktemp->plastik_sku,
                                            "nama"=>$freestocktemp->plastik_nama,
                                            "qty"=>$freestocktemp->plastik_qty

                    ],
                    "customer_id"           =>$freestocktemp->customer_id,
                    "keterangan"            =>$freestocktemp->keterangan,
                    "bumbu_id"              => $freestocktemp->bumbu_id,
                    "bumbu_berat"           => $freestocktemp->bumbu_berat,
                ];
                $exp    =   json_decode($freestocktemp->label);
                $label  =   json_encode([
                    'plastik'       =>  [
                        'sku'       =>  $cariplastik->sku ?? NULL,
                        'jenis'     =>  $cariplastik->nama ?? NULL,
                        'qty'       =>  $request->jumlah_plastik ?? NULL
                    ],
                    'parting'       =>  [
                        'qty'       =>  $request->parting ?? $exp->parting->qty
                    ],
                    'additional'    =>  [
                        'tunggir'   =>  $exp->additional->tunggir,
                        'lemak'     =>  $exp->additional->lemak,
                        'maras'     =>  $exp->additional->maras,
                    ],
                    'sub_item'      =>  $request->keterangan,

                ]);

                if ($freestocktemp) {
                    DB::beginTransaction();
                    $oldBumbuId                     = $freestocktemp->bumbu_id;
                    $oldBerat                       = $freestocktemp->bumbu_berat;

                    $freestocktemp->item_id         =   $request->item ?? $freestocktemp->item_id;
                    $freestocktemp->qty             =   $request->qty ?? $freestocktemp->qty;
                    $freestocktemp->prod_nama       =   $freestocktemp->item->nama ?? $freestocktemp->prod_nama;
                    $freestocktemp->berat           =   $request->berat ?? $freestocktemp->berat;
                    $freestocktemp->kategori        =   $request->kategori ?? $freestocktemp->kategori ;
                    $freestocktemp->customer_id     =   $request->customer ?? $freestocktemp->customer_id;
                    $freestocktemp->label           =   $label ?? $freestocktemp->label;
                    $freestocktemp->plastik_qty     =   $request->jumlah_plastik ??  $freestocktemp->plastik_qty;
                    $freestocktemp->plastik_nama    =   $request->plastik ?? $freestocktemp->plastik_nama;
                    $freestocktemp->plastik_sku     =   $cariplastik->sku ?? NULL;
                    $freestocktemp->parting         =   $request->parting ?? $exp->parting->qty ;
                    $freestocktemp->sub_item        =   $request->keterangan ?? $freestocktemp->sub_item;
                    $freestocktemp->bumbu_id        =   $request->bumbu_id ?? NULL;

                    $new_bumbu_berat                =   $request->bumbu_berat ?? NULL;
                    $freestocktemp->bumbu_berat     =   $new_bumbu_berat;

                    if (!empty($freestocktemp->bumbu_detail_id)) {
                        // Cari bumbu_detail berdasarkan ID
                        $bumbuDetail            = BumbuDetail::find($freestocktemp->bumbu_detail_id);
                        $berat_baru             = $new_bumbu_berat - $bumbuDetail->berat;
                        
                        // Periksa apakah bumbu_detail ditemukan
                        if ($bumbuDetail) {
                            if ($oldBumbuId == $freestocktemp->bumbu_id) {
                            
                            // Update berat di bumbu_detail
                            $bumbuDetail->bumbu_id  = $request->bumbu_id;
                            $bumbuDetail->berat     = $new_bumbu_berat;
                            $bumbuDetail->save();

                            // update total berat bumbu
                            $add_bumbu = Bumbu::find($request->bumbu_id);
                            $add_bumbu->berat = $add_bumbu->berat - $berat_baru;
                            $add_bumbu->save();
                            } else {
                                // Update berat di bumbu_detail yang lama
                                $oldBumbuDetail = BumbuDetail::where('bumbu_id', $oldBumbuId)->first();

                                if ($oldBumbuDetail) {
                                    $oldBumbuDetail->berat = $oldBumbuDetail->berat + $oldBerat;
                                    $oldBumbuDetail->save();

                                    // Update berat dalam bumbu yang lama
                                    $oldBumbu = Bumbu::find($oldBumbuId);

                                    $oldBumbu->berat = $oldBumbu->berat + $oldBerat;
                                    $oldBumbu->save();
                                }
                                
                                // Update berat di bumbu_detail yang baru
                                $bumbuDetail->berat = $new_bumbu_berat;
                                $bumbuDetail->save();
                                
                                // Update berat dalam bumbu yang baru
                                $add_bumbu = Bumbu::find($request->bumbu_id);
                                $add_bumbu->berat = $add_bumbu->berat - $new_bumbu_berat;
                                $add_bumbu->save();
                                
                            }
                        }
                    }

                    

                    if (!$freestocktemp->save()) {
                        DB::rollBack();
                        return back()->with('status', 2)->with('message', 'Proses gagal');
                    }

                    $chiller    =   Chiller::where('table_name', 'free_stocktemp')
                                    ->where('table_id', $freestocktemp->id)
                                    ->first();

                    if ($chiller) {
                        $item_name              = Item::find($request->item);
                        
                        if ($item_name) {
                            $chiller->item_id   =   $request->item;
                            $chiller->item_name =   $item_name->nama;
                        }
                        
                        $chiller->qty_item      =   $request->qty;
                        $chiller->berat_item    =   $request->berat;
                        $chiller->stock_item    =   $request->qty;
                        $chiller->stock_berat   =   $request->berat;
                        $chiller->kategori      =   $request->kategori ?? $chiller->kategori ;
                        $chiller->customer_id   =   $request->customer;
                        $chiller->label         =   $label;

                        $chiller->plastik_qty     =   $request->jumlah_plastik ;
                        $chiller->plastik_nama    =   $request->plastik ;
                        $chiller->plastik_sku     =   $cariplastik->sku ?? NULL;

                        // return $chiller;

                        if (!$chiller->save()) {
                            DB::rollBack();
                            return back()->with('status', 2)->with('message', 'Proses gagal');
                        }
                    }

                    $abf    =   Abf::where('table_id', $freestocktemp->id)
                                ->where('asal_tujuan', 'free_stock')
                                ->first();
                    if ($abf) {
                        $abf->berat_item        =   ($abf->berat_awal - $abf->berat_item) + $request->berat ;
                        $abf->qty_item          =   ($abf->qty_awal - $abf->qty_item) + $request->qty ;
                        $abf->berat_awal        =   $request->berat ;
                        $abf->qty_awal          =   $request->qty ;
                        $abf->customer_id       =   $request->customer ;

                        if (!$abf->save()) {
                            DB::rollBack();
                            return back()->with('status', 2)->with('message', 'Proses gagal');
                        }
                    }

                    // dd($request->all());freestocktemp
                    $itemName   =  DB::table('items')->where('id',$freestocktemp->item_id)->select('nama')->first();
                    $updated = [
                        "freestock_temp"      =>$freestocktemp->id,
                        "item_id"             =>$request->item_id,
                        "item_name"           => $itemName->nama,
                        "jumlah_parting"      =>$request->parting,
                        "qty"                 =>$request->qty,
                        "berat"               =>$request->berat,
                        "lokasi"              =>$request->kategori,
                        "plastik"             =>[
                                                "sku"   =>    $cariplastik->sku ?? NULL,
                                                "nama"  =>    $request->plastik ?? NULL,
                                                "qty"   =>    $request->jumlah_plastik ?? NULL

                        ],
                        "customer_id" =>$request->customer_id,
                        "keterangan" =>$request->keterangan

                    ];

                    $freestock_status =  $freestocktemp->free_stock->status;
                    // dd($freestock_status);
                    if ($freestock_status != 2) {
                        $log                        =   new Adminedit();
                        $log->user_id               =   Auth::user()->id ;
                        $log->table_name            =   'chiller';
                        $log->table_id              =   $freestocktemp->id ;
                        $log->table_id              =   $chiller->id ;
                        $log->type                  =   'edit' ;
                        $log->activity              =   'kepala_regu_hp';
                        $log->content               =   'Edit Hasil Produksi' . ' '  . date("Y-m-d H:i:s");
                        $log->data                  =   json_encode([
                                'before_update'     => $origin,
                                'after_update'      => $updated
                        ]) ;
                        if (!$log->save()) {
                            DB::rollBack();
                            return back()->with('status', 2)->with('message', 'Proses simpan log gagal');
                        }
                    }
                    DB::commit();
                    try {
                        Chiller::recalculate_chiller($chiller->id);
                    } catch (\Throwable $th) {

                    }

                return back()->with('status', 1)->with('message', 'Ubah hasil produksi berhasil');
            }
            return back()->with('status', 2)->with('message', 'Proses gagal');
        }
    }
    public function bahanBakuHistory(Request $request) {
        $tableId    =   $request->id;
        if ($request->key == 'riwayat_edit_produksi') {

            $adminEdit  =   Adminedit::where('table_id', $tableId)->where('activity', 'kepala_regu_hp')->where('type', 'edit')->get();

            return view('admin.pages.chiler.history_edit',compact('adminEdit') );
        } else if ($request->key == 'riwayat_edit_bb') {

            $adminEdit  =   Adminedit::where('table_id', $tableId)->where('activity', 'kepala_regu_bb')->where('type', 'edit')->get();

            return view('admin.pages.chiler.history_edit',compact('adminEdit') );
        }
    }

    public function store(Request $request)
    {

        if ($request->key == 'ubahtanggal') {
            DB::beginTransaction();
            $freestock              =   Freestock::find($request->row_id);
            $freestock->tanggal     =   $request->tanggal;
            $freestock->regu        =   $request->regu;
            // if($request->netsuite_send!=""){
            //     if($request->netsuite_send=="FALSE"){
            //         $freestock->netsuite_send      =   NULL;
            //     }else{
            //         $freestock->netsuite_send      =   0;
            //     }
            // }
            $freestock->save();

            FreestockTemp::where('freestock_id', $freestock->id)->update([
                'tanggal_produksi'  => $request->tanggal,
                'regu'              => $request->regu
            ]);

            $chiller = Chiller::where('table_name', 'free_stocktemp')
                                ->whereIn('table_id', FreestockTemp::select('id')->where('freestock_id', $freestock->id))
                                ->where('jenis', 'masuk')->update([
                                        'tanggal_produksi'  => $request->tanggal,
                                        'regu'              => $request->regu
                                ]);

            $freestocklistupdate = FreestockList::where('freestock_id', $freestock->id)->get();
            if($freestocklistupdate->count() > 0){
                foreach ($freestocklistupdate as $listUpdate) {
                    $checkTanggalProduksi = FreestockList::CheckTanggalProduksi($listUpdate->chiller_id);
                    if($listUpdate->bb_kondisi == 'baru' || $listUpdate->bb_kondisi == 'lama'){
                        $listUpdate->bb_kondisi = $checkTanggalProduksi >= $request->tanggal ? 'baru' : 'lama';
                    }

                    $listUpdate->regu = $request->regu;
                    if(!$listUpdate->save()){
                        DB::rollBack();
                        $response['status'] =   400;
                        $response['msg']    =   'Proses Gagal';
                        return $response;
                    }
                }
            }
            DB::commit();

        } else

        if ($request->key == 'selesaikan') {


            if ($request->cast == 'removed') {

                DB::beginTransaction();

                $freestock          = Freestock::where('regu', $request->jenis)
                                    ->where('id', $request->id)
                                    ->first();
                $data               = FreestockTemp::where('freestock_id',$freestock->id)->first();
                // $freestocklist      = FreestockList::where('freestock_id', $freestock->id)->first();

                if ($freestock) {

                    if ($freestock->status == 2) {
                        $freestock->status  =   0;
                        if (!$freestock->save()) {
                            DB::rollBack();
                            $response['status'] =   400;
                            $response['msg']    =   'Proses Gagal';
                            return $response;
                        }

                        FreestockTemp::where('freestock_id',$freestock->id)->delete();
                        FreestockList::where('freestock_id', $freestock->id)->delete();

                        // if($data){
                        //     $data->delete();
                        // }

                        // if($freestocklist){
                        //     $freestocklist->delete();
                        // }

                        if($freestock){
                            $freestock->delete();
                        }
                        
                        $log                        =   new Adminedit();
                        $log->user_id               =   Auth::user()->id ;
                        $log->table_name            =   'chiller' ;
                        $log->table_id              =   $freestock->id ;
                        $log->type                  =   'delete' ;
                        $log->activity              =   'kepala_regu_bb';
                        $log->content               =   'Batalkan Ambil Bahan Baku';
                        $log->data                  =   json_encode([
                                'data'              => $freestock,
                        ]) ;
                        $log->save();
                    }

                    if ($freestock->status == 3) {
                        if ($data) {
                            foreach ($data as $item) {
                                $abf        =         $item->freetempchiller->ambil_abf ?? "";
                                $ambil      =         $item->freetempchiller->ambil_chiller ?? "";
                                $order      =         $item->freetempchiller->alokasi_order ?? "";
                                if ($abf != "") {
                                    if (count($abf) > 0) {
                                        $result['status']   =   400;
                                        $result['msg']      =   "Gagal, Item sudah dialokasikan ke ABF";
                                        return $result;
                                    }
                                }
                                if ($ambil != "") {
                                    if (count($ambil) > 0) {
                                        $result['status']   =   400;
                                        $result['msg']      =   "Gagal, Item sudah dialokasikan ke ABF";
                                        return $result;
                                    }
                                }

                                if ($order != "") {
                                    if(count($order) > 0){
                                        $result['status']   =   400;
                                        $result['msg']      =   "Gagal, Item sudah dialokasikan ke pengiriman";
                                        return $result;
                                    }
                                }


                            }
                        }
                        $childelete = null;
                        $freelistdelete = null;
                        foreach ($freestock->listfreestock as $row) {
                            $chill                  =   Chiller::find($row->chiller_id);
                            $chill->stock_berat     =   $chill->stock_berat + $row->berat;
                            $chill->stock_item      =   $chill->stock_item + $row->qty;

                            if (!$chill->save()) {
                                DB::rollBack();
                                $response['status'] =   400;
                                $response['msg']    =   'Proses Gagal';
                                return $response;
                            }

                            $childelete     = Chiller::find($row->outchiller);
                            $childelete->delete();
                            $freelistdelete = FreestockList::find($row->id);
                            $freelistdelete->delete();

                        }

                        foreach ($freestock->freetemp as $row) {
                            $abfData = Abf::where('table_name', 'free_stocktemp')->where('table_id', $row->id)->first();
                            $chillerData = Chiller::where('table_name', 'free_stocktemp')->where('table_id', $row->id)->first();
                            
                            if ($freestock->regu == 'frozen') {
                                if($abfData){
                                    $abfData->delete();
                                }
                            } else {
                                if($chillerData){
                                    $chillerData->delete();
                                }
                            }

                            $freetemp = FreestockTemp::find($row->id);
                            $freetemp->delete();
                        }

                        $freestock->delete();
                        if (!$freestock->save()) {
                            DB::rollBack();
                            $response['status'] =   400;
                            $response['msg']    =   'Proses Gagal';
                            return $response;
                        }

                        $logdelete              = new Adminedit();
                        $logdelete->user_id     = Auth::user()->id;
                        $logdelete->table_name  = 'free_stock';
                        $logdelete->table_id    = $freestock->id;
                        $logdelete->type        = 'delete';
                        $logdelete->activity    = $freestock->regu;
                        $logdelete->content     = 'batalkan/delete hasil produksi harian '. ($freestock->regu);
                        $logdelete->key         = $freestock->tanggal;
                        $logdelete->data        = json_encode([
                            'chiller'   => $childelete,
                            'stocklist' => $freelistdelete,
                            'freestock' => $freestock,
                            'freetemp'  => $freetemp,
                            'frozen'    => $abfData
                        ]);
                        $logdelete->save();
                        if (!$logdelete->save()) {
                            DB::rollBack();
                            $response['status'] =   400;
                            $response['msg']    =   'Proses Simpan Log Gagal';
                            return $response;
                        }

                    }



                    DB::commit();
                    try {
                        Chiller::recalculate_chiller($chill->id);
                    } catch (\Throwable $th) {

                    }

                } else {
                    DB::rollBack();
                    $response['status'] =   400;
                    $response['msg']    =   'Proses Gagal, tidak sesuai regu yang menginput';
                    return $response;
                }
            } else

            if ($request->cast == 'approve') {

                $freestock  =   Freestock::where('regu', $request->jenis)
                                ->where('id', $request->id)
                                ->where('status', 2)
                                ->first();

                if (COUNT($freestock->listfreestock)) {

                    foreach ($freestock->listfreestock as $row) {
                        //validasi double
                        DB::beginTransaction();

                        $cek = Chiller::where('asal_tujuan','free_stock')->where('table_id',$row->id)->where('type','pengambilan-bahan-baku')->get();
                        if (count($cek) <= 0) {
                            // Insert ke chiller sebagai ambil bb
                            $outchiller                     =   new Chiller;
                            $outchiller->table_name         =   'free_stocklist';
                            $outchiller->table_id           =   $row->id;
                            $outchiller->asal_tujuan        =   'free_stock';
                            $outchiller->item_id            =   $row->item_id;
                            $outchiller->item_name          =   $row->item->nama;
                            $outchiller->jenis              =   'keluar';
                            $outchiller->type               =   'pengambilan-bahan-baku';
                            $outchiller->regu               =   $freestock->regu;
                            $outchiller->no_mobil           =   $row->chiller->no_mobil ?? '';
                            $outchiller->qty_item           =   $row->qty;
                            $outchiller->berat_item         =   $row->berat;
                            $outchiller->stock_item         =   $row->qty;
                            $outchiller->stock_berat        =   $row->berat;
                            $outchiller->tanggal_potong     =   $row->chiller->tanggal_potong;
                            $outchiller->tanggal_produksi   =   $row->chiller->tanggal_produksi;
                            $outchiller->status             =   4;
                            if (!$outchiller->save()) {
                                DB::rollBack();
                                $response = array(
                                    'code'    => "0",
                                    'status'   => "Failed",
                                    'message'   => "Process failed"
                                );
                                return $response;
                            }

                            // Ubah field outchiller untuk diisikan data id ambil bb
                            $row->outchiller                =   $outchiller->id;
                            if (!$row->save()) {
                                DB::rollBack();
                                $response = array(
                                    'code'    => "0",
                                    'status'   => "Failed",
                                    'message'   => "Process failed"
                                );
                                return $response;
                            }

                            // Mengurangi stock chiller
                            $chiller                        =   Chiller::find($row->chiller_id);
                            $chiller->stock_berat           =   $chiller->stock_berat - $row->berat;
                            $chiller->stock_item            =   $chiller->stock_item - $row->qty;

                            if (!$chiller->save()) {
                                DB::rollBack();
                                $response = array(
                                    'code'    => "0",
                                    'status'   => "Failed",
                                    'message'   => "Process failed"
                                );
                                return $response;
                            }

                        } else {
                            // DB::rollBack();
                            // $response = array(
                            //     'code'    => "0",
                            //     'status'   => "Failed",
                            //     'message'   => "Proses bahan baku sudah dilakukan"
                            // );
                            // return $response;
                        }

                        DB::commit();
                    }
                }

                if (COUNT($freestock->freetemp)) {
                    foreach ($freestock->freetemp as $row) {
                        DB::beginTransaction();
                        if($row->berat == '' || $row->berat == NULL || $row->berat == '0') {
                            DB::rollBack();
                            $response = array(
                                'code'    => "400",
                                'status'   => "Failed",
                                'message'   => "Terdapat berat hasil produksi yang kosong"
                            );
                            return $response;
                        } else {
                            //validasi double
                            $cek = Chiller::where('asal_tujuan','free_stock')->where('jenis','masuk')->where('table_id',$row->id)->where('type','hasil-produksi')->get();
                            if (count($cek) <= 0) {
                                // Send to chiller
                                $chiller                    =   new Chiller;
                                $chiller->table_name        =   'free_stocktemp';
                                $chiller->table_id          =   $row->id;
                                $chiller->asal_tujuan       =   'free_stock';
                                $chiller->item_id           =   $row->item_id;
                                $chiller->item_name         =   $row->item->nama;
                                $chiller->jenis             =   'masuk';
                                $chiller->type              =   'hasil-produksi';
                                $chiller->regu              =   $freestock->regu;
                                $chiller->parting           =   $row->parting;
                                $chiller->label             =   $row->label;
                                $chiller->customer_id       =   $row->customer_id;
                                $chiller->selonjor          =   $row->selonjor;
                                $chiller->berat_item        =   $row->berat;
                                $chiller->qty_item          =   $row->qty;
                                $chiller->stock_berat       =   $chiller->berat_item;
                                $chiller->stock_item        =   $chiller->qty_item;
                                $chiller->plastik_qty       =   $row->plastik_qty ;
                                $chiller->plastik_nama      =   $row->plastik_nama ;
                                $chiller->plastik_sku       =   $row->plastik_sku ?? NULL;
                                $chiller->keranjang         =   $row->keranjang;
                                $chiller->kode_produksi     =   $row->kode_produksi;
                                $chiller->unit              =   $row->unit;
                                $chiller->tanggal_produksi  =   $row->tanggal_produksi;

                                $chiller->kategori      =   $row->kategori;

                                $chiller->status            =   2;

                                if (!$chiller->save()) {
                                    DB::rollBack();
                                    $response = array(
                                        'code'    => "0",
                                        'status'   => "Failed",
                                        'message'   => "insert chiller " . $row->id . " failed"
                                    );
                                    return $response;
                                }


                                if ($freestock->orderitem_id) {
                                    $order                              =   OrderItem::find($freestock->orderitem_id) ;
                                }

                            } else {
                                // DB::rollBack();
                                // $response = array(
                                //     'code'    => "0",
                                //     'status'   => "Failed",
                                //     'message'   => "Proses hasil produksi sudah dilakukan"
                                // );
                                // return $response;
                            }


                        }
                        DB::commit();
                    }
                }

                DB::beginTransaction();

                $freestock->status  =   3;


                // if ($request->netsuite_send != "") {
                //     if ($request->netsuite_send == "FALSE") {
                //         $freestock->netsuite_send      =   NULL;
                //     } else {
                //         $freestock->netsuite_send      =   0;
                //     }
                // }


                if (!$freestock->save()) {
                    DB::rollBack();
                    $response = array(
                        'code'    => "440",
                        'status'   => "Failed",
                        'message'   => "Process failed"
                    );
                    return $response;
                }

                DB::commit();

                try {
                    Chiller::recalculate_chiller($chiller->id);
                } catch (\Throwable $th) {

                }

                $response = array(
                    'code'    => "200",
                    'status'   => "Success",
                    'message'   => "Process Success"
                );
                return $response;

            } else
            if ($request->cast == 'back') {
                DB::beginTransaction();
                $freestock  =   Freestock::where('regu', $request->jenis)
                    ->where('id', $request->id)
                    ->where('status', 2)
                    ->first();

                if ($freestock) {
                    $freestock->status  =   1;
                }
                // $freestock->tanggal = Carbon::now();
                if (!$freestock->save()) {
                    DB::rollBack();
                    $response = array(
                        'code'    => "0",
                        'status'   => "Failed",
                        'message'   => "Process failed"
                    );
                    return $response;
                }

                DB::commit();
            } else {
                DB::beginTransaction();
                // simpan Kepala Regu
                $freestock  =   Freestock::where('regu', $request->jenis)
                                // ->where(function ($query) {
                                //     if ((Auth::user()->account_role != 'superadmin') || (!User::setIjin(33))) {
                                //         $query->where('user_id', Auth::user()->id);
                                //     }
                                // })
                                ->where(function($query) use ($request) {
                                    if ($request->orderitem) {
                                        $query->where('orderitem_id', $request->orderitem) ;
                                    }
                                    // else {
                                    //     $query->where('orderitem_id', NULL) ;
                                    // }
                                })
                                ->where('status', 1)
                                ->orderBy('id','desc')
                                ->first();
                if(env('NET_SUBSIDIARY', 'CGL')=="CGL"){
                    if (FreestockTemp::where('freestock_id', $freestock->id)->count() < 1) {
                        $result['status']   =   400;
                        $result['msg']      =   'Hasil produksi masih kosong';
                        return $result;
                    }
                }

                if (!$freestock) {
                    $result['status']   =   400;
                    $result['msg']      =   'Terjadi kesalahan';
                    return $result;
                }

                $freestock->status  =   2;
                $freestock->tanggal = Carbon::now();

                // validasi ketika FG dan tidak kirim WO
                //freetemp = hasil produksi
                //freestocklist = bahanbaku
                // $cek_hasil = null;
                // $cek_bb = null;
                // if ($request->idbb) {
                //     $cek_bb     = FreestockList::select('item_id')->whereIn('id',$request->idbb)->get();
                // }
                // if ($request->idhp){
                //     $cek_hasil  = FreestockTemp::select('item_id')->whereIn('id',$request->idhp)->get();
                //     // dd($cek_hasil);
                // }
                // dd($cek_hasil);
                // if ($cek_bb !== null && $cek_hasil !== null) {
                //     foreach ($cek_bb as $bahan) {
                //         foreach ($cek_hasil as $hasil) {
                //             if ($bahan->item_id == $hasil->item_id) {
                //                 $freestock->netsuite_send = Freestock::tidak_kirim_wo;
                //             }else{
                                // if($request->netsuite_send!=""){
                                //     if($request->netsuite_send=="FALSE"){
                                //         $freestock->netsuite_send      =   Freestock::kirim_wo;
                                //     }else{
                                //         $freestock->netsuite_send      =   Freestock::tidak_kirim_wo;
                                //     }
                                // }
                //             }
                //         }
                //     }
                // }
                // return response()->json($cek_bb);




                if (!$freestock->save()) {
                    DB::rollBack();
                    $response = array(
                        'code'    => "0",
                        'status'   => "Failed",
                        'message'   => "Process failed"
                    );
                    return $response;
                }

                DB::commit();
                $result['freestock_id'] =   $freestock->id ;

                // return $request->all();
                return $result ;
            }
        } else {
            // dd($request->all());

            // return response()->json($request->all());
            // Cek validasi di database jika sudah ada orderitem yang terdaftar
            // Hapus validasi untuk double input
            // if ($request->orderitem) {
            //     $cekDoubleDataFreeStock = Freestock::where('orderitem_id', $request->orderitem)->first();
            //     if ($cekDoubleDataFreeStock) {
            //         $response['status'] =   400;
            //         $response['msg']    =   'Proses Gagal, data sudah diinput sebelumnya.';
            //         return $response;
            //     }
            // }

            $tunggir    =   FALSE;
            $lemak      =   FALSE;
            $maras      =   FALSE;
            if ($request->additional) {
                for ($x = 0; $x < COUNT($request->additional); $x++) {
                    if ($request->additional[$x] == 'tunggir') {
                        $tunggir   =   TRUE;
                    };
                    if ($request->additional[$x] == 'lemak') {
                        $lemak       =   TRUE;
                    };
                    if ($request->additional[$x] == 'maras') {
                        $maras       =   TRUE;
                    };
                }
            }

            if ($request->plastik != 'Curah') {
                $plastik =   Item::find($request->plastik);
            }

            $freestock  =   Freestock::where(function ($query) use ($request) {
                                if ($request->produksi) {
                                    $query->where('id', $request->produksi);
                                } else {
                                    $query->where('regu', $request->jenis)->where('status', 1);

                                    // if ((Auth::user()->account_role != 'superadmin') || (!User::setIjin(33))) {
                                    //     $query->where('user_id', Auth::user()->id);
                                    // }
                                }
                            })
                            ->where(function($query) use ($request) {
                                if ($request->orderitem) {
                                    $query->where('orderitem_id', $request->orderitem) ;
                                } else {
                                    $query->where('orderitem_id', NULL) ;
                                }
                            })
                            ->first();

            if (!$freestock) {
                $freestock                  =   new Freestock;
                $freestock->nomor           =   Freestock::get_nomor();
                $freestock->tanggal         =   Carbon::now();
                $freestock->user_id         =   Auth::user()->id;
                $freestock->regu            =   $request->jenis;
                $freestock->orderitem_id   =   $request->orderitem ?? NULL;
                $freestock->status          =   1;
                $freestock->save() ;
            }

            if ($request->orderitem) {
                $orderitem  =   OrderItem::find($request->orderitem) ;
            }

            $customer   =   Customer::find($orderitem->itemorder->customer_id ?? $request->customer);

            $label      =   [
                'plastik'       =>  [
                    'sku'       =>  $plastik->sku ?? NULL,
                    'jenis'     =>  $plastik->nama ?? NULL,
                    'qty'       =>  $request->jumlah_plastik ?? NULL
                ],
                'parting'       =>  [
                    'qty'       =>  $request->parting ?? NULL
                ],
                'additional'    =>  [
                    'tunggir'   =>  $tunggir,
                    'lemak'     =>  $lemak,
                    'maras'     =>  $maras,
                ],
                'sub_item'      =>  $request->sub_item
            ];

            $temp                   =   new FreestockTemp;
            $temp->freestock_id     =   $freestock->id;
            $temp->item_id          =   $request->item;
            $temp->prod_nama        =   Item::find($orderitem->item_id ?? $request->item)->nama;
            $temp->customer_id      =   $customer->id ?? NULL;
            $temp->regu             =   $request->regu ?? $request->jenis;
            $temp->tanggal_produksi =   $freestock->tanggal;
            $temp->label            =   json_encode($label);
            $temp->berat            =   $request->berat;
            $temp->order_item_id    =   $request->orderitem ?? NULL;

            if ($freestock->regu == 'boneless') {
                if (env('NET_SUBSIDIARY', 'EBA') == 'EBA') {
                    $temp->qty          =   $request->jumlah ?? $request->jumlah_plastik;
                } else {
                    $temp->qty          =   $request->jumlah;
                }
            }else{
                    $temp->qty          =   $request->jumlah;
            }

            $temp->selonjor         =   $request->selonjor ? 1 : NULL ;
            $temp->plastik_sku      =   $plastik->sku ?? NULL;
            $temp->plastik_nama     =   $plastik->nama ?? NULL;
            $temp->plastik_qty      =   $request->jumlah_plastik ?? NULL;
            $temp->unit             =   $request->unit ?? NULL ;
            $temp->kode_produksi    =   $request->kode_produksi ?? NULL ;
            $temp->keranjang        =   $request->jumlah_keranjang ?? NULL ;
            $temp->parting          =   $request->parting ?? NULL ;
            $temp->sub_item         =   $request->sub_item ;

            $order_free                             = OrderItem::find($request->orderitem);
            if($request->alasan){
                $order_free->tidak_terkirim             = 1;
                $order_free->tidak_terkirim_catatan     = $request->alasan;
                $order_free->save();
            }


            if ($freestock->regu == 'frozen') {
                $temp->kategori            =   1;
            } else {
                $temp->kategori         =   $request->tujuan_produksi;
            }

            $temp->save();

            if($freestock->regu == 'marinasi'){
                // dd($freestock->regu);
                $bumbu                  = Bumbu::where('id',$request->bumbu_id)->first();

                if ($bumbu) {
                    $customerBumbu          = CustomerBumbu::where('customer_id', $temp->customer_id)
                                                ->where('bumbu_id', $bumbu->id)
                                                ->first();
    
    
                    if ($bumbu->berat < $request->bumbu_berat ) {
                        
                        $result['status']   =   400;
                        $result['msg']      =   "Gagal, Stok Bumbu Kurang";
    
                        return $result;
                    } else {
                        $bumbu->berat                   = $bumbu->berat - $request->bumbu_berat;
                        $bumbu->save();
                        
                        $logbumbu                       = new BumbuDetail();
                        $logbumbu->bumbu_id             = $request->bumbu_id;
                        $logbumbu->stock                = 0;
                        $logbumbu->berat                = $request->bumbu_berat;
                        $logbumbu->regu                 = $request->jenis;
                        $logbumbu->status               = 'keluar';
                        $logbumbu->tanggal              = Carbon::now()->format('Y-m-d');
                        $logbumbu->bumbu_customer_id    = $customerBumbu->id;
                        // $temp->bumbu_id                 = $logbumbu->id;
                        $logbumbu->save();
                        
                        $logbumbu_id                    = $logbumbu->id;
                        $temp->bumbu_id                 = $request->bumbu_id;
                        $temp->bumbu_berat              = $request->bumbu_berat;
                        $temp->save();
                    }

                }
            }

            if ($freestock->status == 3) {
                if ($request->act == 'tambahan') {
                    // Send to chiller
                    $chiller                    =   new Chiller;
                    $chiller->table_name        =   'free_stocktemp';
                    $chiller->table_id          =   $temp->id;
                    $chiller->asal_tujuan       =   'free_stock';
                    $chiller->item_id           =   $temp->item_id;
                    $chiller->item_name         =   $temp->item->nama;
                    $chiller->jenis             =   'masuk';
                    $chiller->type              =   'hasil-produksi';
                    $chiller->regu              =   $freestock->regu;
                    $chiller->parting           =   $temp->parting;
                    $chiller->label             =   $temp->label;
                    $chiller->customer_id       =   $temp->customer_id ;
                    $chiller->selonjor          =   $temp->selonjor ;
                    $chiller->berat_item        =   $temp->berat;
                    $chiller->qty_item          =   $temp->qty;
                    $chiller->plastik_qty       =   $temp->plastik_qty ;
                    $chiller->plastik_nama      =   $temp->plastik_nama ;
                    $chiller->plastik_sku       =   $temp->plastik_sku ?? NULL;
                    $chiller->stock_berat       =   $chiller->berat_item;
                    $chiller->stock_item        =   $chiller->qty_item;
                    $chiller->tanggal_produksi  =   $temp->tanggal_produksi;
                    $chiller->keranjang         =   $temp->keranjang;

                    // Jika kirim ke abf dari chiller
                    if ($temp->kategori == "1") {
                        $chiller->kategori      =   "1";
                    }

                    $chiller->status            =   2;
                    $chiller->save();
                }
            }

            // free tunggir
            if ($tunggir == TRUE) {

                $label      =   [
                    'plastik'       =>  [
                        'sku'       =>  NULL,
                        'jenis'     =>  NULL,
                        'qty'       =>  NULL
                    ],
                    'parting'       =>  [
                        'qty'       =>  NULL
                    ],
                    'additional'    =>  [
                        'tunggir'   =>  "",
                        'lemak'     =>  "",
                        'maras'     =>  "",
                    ],
                    'sub_item'      => ""
                ];

                if ($request->berattunggir > 0) {
                    $temp                   =   new FreestockTemp;
                    $temp->freestock_id     =   $freestock->id;
                    $temp->item_id          =   $request->itemtunggir;
                    $temp->prod_nama        =   Item::find($request->itemtunggir)->nama;
                    $temp->regu             =   $request->jenis;
                    $temp->tanggal_produksi =   $freestock->tanggal;
                    $temp->label            =   json_encode($label);
                    $temp->berat            =   $request->berattunggir;
                    $temp->qty              =   $request->jumlahtunggir;
                    $temp->save();

                    if ($freestock->status == 3) {
                        if ($request->act == 'tambahan') {
                            $chiller                    =   new Chiller;
                            $chiller->table_name        =   'free_stocktemp';
                            $chiller->table_id          =   $temp->id;
                            $chiller->asal_tujuan       =   'free_stock';
                            $chiller->item_id           =   $temp->item_id;
                            $chiller->item_name         =   $temp->item->nama;
                            $chiller->jenis             =   'masuk';
                            $chiller->type              =   'hasil-produksi';
                            $chiller->regu              =   $freestock->regu;
                            $chiller->parting           =   $temp->parting;
                            $chiller->label             =   $temp->label;
                            $chiller->berat_item        =   $temp->berat;
                            $chiller->qty_item          =   $temp->qty;
                            $chiller->plastik_qty       =   $temp->plastik_qty ;
                            $chiller->plastik_nama      =   $temp->plastik_nama ;
                            $chiller->plastik_sku       =   $temp->plastik_sku ?? NULL;
                            $chiller->stock_berat       =   $chiller->berat_item;
                            $chiller->stock_item        =   $chiller->qty_item;
                            $chiller->tanggal_produksi  =   $temp->tanggal_produksi;

                            // Jika kirim ke abf dari chiller
                            if ($temp->kategori == "1") {
                                $chiller->kategori      =   "1";
                            }

                            $chiller->status            =   2;
                            $chiller->save();

                        }
                    }
                }
            }
            // free maras
            if ($maras == TRUE) {

                $label      =   [
                    'plastik'       =>  [
                        'sku'       =>  NULL,
                        'jenis'     =>  NULL,
                        'qty'       =>  NULL
                    ],
                    'parting'       =>  [
                        'qty'       =>  NULL
                    ],
                    'additional'    =>  [
                        'tunggir'   =>  "",
                        'lemak'     =>  "",
                        'maras'     =>  "",
                    ],
                    'sub_item'      => ""
                ];

                if ($request->beratmaras > 0) {
                    $temp                   =   new FreestockTemp;
                    $temp->freestock_id     =   $freestock->id;
                    $temp->item_id          =   $request->itemmaras;
                    $temp->prod_nama        =   Item::find($request->itemmaras)->nama;
                    $temp->regu             =   $request->jenis;
                    $temp->tanggal_produksi =   $freestock->tanggal;
                    $temp->label            =   json_encode($label);
                    $temp->berat            =   $request->beratmaras;
                    $temp->qty              =   $request->jumlahmaras;
                    $temp->save();


                    if ($freestock->status == 3) {
                        if ($request->act == 'tambahan') {
                            $chiller                    =   new Chiller;
                            $chiller->table_name        =   'free_stocktemp';
                            $chiller->table_id          =   $temp->id;
                            $chiller->asal_tujuan       =   'free_stock';
                            $chiller->item_id           =   $temp->item_id;
                            $chiller->item_name         =   $temp->item->nama;
                            $chiller->jenis             =   'masuk';
                            $chiller->type              =   'hasil-produksi';
                            $chiller->regu              =   $freestock->regu;
                            $chiller->parting           =   $temp->parting;
                            $chiller->label             =   $temp->label;
                            $chiller->berat_item        =   $temp->berat;
                            $chiller->qty_item          =   $temp->qty;
                            $chiller->plastik_qty       =   $temp->plastik_qty ;
                            $chiller->plastik_nama      =   $temp->plastik_nama ;
                            $chiller->plastik_sku       =   $temp->plastik_sku ?? NULL;
                            $chiller->stock_berat       =   $chiller->berat_item;
                            $chiller->stock_item        =   $chiller->qty_item;
                            $chiller->tanggal_produksi  =   $temp->tanggal_produksi;

                            // Jika kirim ke abf dari chiller
                            if ($temp->kategori == "1") {
                                $chiller->kategori      =   "1";
                            }

                            $chiller->status            =   2;
                            $chiller->save();

                        }
                    }
                }
            }
            // free lemak
            if ($lemak == TRUE) {

                $label      =   [
                    'plastik'       =>  [
                        'sku'       =>  NULL,
                        'jenis'     =>  NULL,
                        'qty'       =>  NULL
                    ],
                    'parting'       =>  [
                        'qty'       =>  NULL
                    ],
                    'additional'    =>  [
                        'tunggir'   =>  "",
                        'lemak'     =>  "",
                        'maras'     =>  "",
                    ],
                    'sub_item'      => ""
                ];

                if ($request->beratlemak > 0) {
                    $temp                   =   new FreestockTemp;
                    $temp->freestock_id     =   $freestock->id;
                    $temp->item_id          =   $request->itemlemak;
                    $temp->prod_nama        =   Item::find($request->itemlemak)->nama;
                    $temp->regu             =   $request->jenis;
                    $temp->tanggal_produksi =   $freestock->tanggal;
                    $temp->label            =   json_encode($label);
                    $temp->berat            =   $request->beratlemak;
                    $temp->qty              =   $request->jumlahlemak;
                    $temp->save();

                    if ($freestock->status == 3) {
                        if ($request->act == 'tambahan') {
                            $chiller                    =   new Chiller;
                            $chiller->table_name        =   'free_stocktemp';
                            $chiller->table_id          =   $temp->id;
                            $chiller->asal_tujuan       =   'free_stock';
                            $chiller->item_id           =   $temp->item_id;
                            $chiller->item_name         =   $temp->item->nama;
                            $chiller->jenis             =   'masuk';
                            $chiller->type              =   'hasil-produksi';
                            $chiller->regu              =   $freestock->regu;
                            $chiller->parting           =   $temp->parting ?? 0;
                            $chiller->label             =   $temp->label;
                            $chiller->berat_item        =   $temp->berat;
                            $chiller->qty_item          =   $temp->qty;
                            $chiller->plastik_qty       =   $temp->plastik_qty ;
                            $chiller->plastik_nama      =   $temp->plastik_nama ;
                            $chiller->plastik_sku       =   $temp->plastik_sku ?? NULL;
                            $chiller->stock_berat       =   $chiller->berat_item;
                            $chiller->stock_item        =   $chiller->qty_item;
                            $chiller->tanggal_produksi  =   $temp->tanggal_produksi;

                            // Jika kirim ke abf dari chiller
                            if ($temp->kategori == "1") {
                                $chiller->kategori      =   "1";
                            }

                            $chiller->status            =   2;
                            $chiller->save();


                        }
                    }
                }
            }

            if($request->data_form){
                $dat_id = array();
                $dat_qty = array();
                $dat_berat = array();
                $dat_catatan = array();
                if(count($request->data_form)>0){
                    for($x=0; $x<count($request->data_form); $x++){
                        if($request->data_form[$x]['name']=="x_code[]"){
                            $dat_id[] = $request->data_form[$x]['value'];
                        }
                        if($request->data_form[$x]['name']=="qty[]"){
                            $dat_qty[] = $request->data_form[$x]['value'];
                        }
                        if($request->data_form[$x]['name']=="berat[]"){
                            $dat_berat[] = $request->data_form[$x]['value'];
                        }
                        if($request->data_form[$x]['name']=="catatan[]"){
                            $dat_catatan[] = $request->data_form[$x]['value'];
                        }
                    }
                };

                for($p=0; $p<count($dat_id); $p++){

                    if ($dat_berat[$p]) {
                        $chiller                        =   Chiller::find($dat_id[$p]);

                        $list                           =   new FreestockList;
                        $list->freestock_id             =   $freestock->id;
                        $list->chiller_id               =   $chiller->id;
                        $list->item_id                  =   $chiller->item_id;

                        if ($chiller->asal_tujuan == "gradinggabungan") {
                            if ($chiller->tanggal_produksi >= date('Y-m-d', strtotime($freestock->tanggal))) {
                                $list->bb_kondisi       =   "baru";
                            } else {
                                $list->bb_kondisi       =   "lama";
                            }
                        } else {
                            $list->bb_kondisi           =   $chiller->asal_tujuan;
                        }

                        $list->qty                      =   $dat_qty[$p];
                        $list->regu                     =   $freestock->regu ;
                        $list->berat                    =   $dat_berat[$p];
                        $list->sisa                     =   $dat_qty[$p];
                        $list->catatan                  =   $dat_catatan[$p];
                        if (!$list->save()) {
                            DB::rollBack();
                            return back()->with('status', 2)->with('message', 'Proses gagal');
                        }
                        // return $list;

                        if ($freestock->status == 3) {
                            $outchiller                     =   new Chiller;
                            $outchiller->table_name         =   'free_stocklist';
                            $outchiller->table_id           =   $list->id;
                            $outchiller->asal_tujuan        =   'free_stock';
                            $outchiller->item_id            =   $list->item_id;
                            $outchiller->item_name          =   $list->item->nama;
                            $outchiller->jenis              =   'keluar';
                            $outchiller->type               =   'pengambilan-bahan-baku';
                            $outchiller->regu               =   $freestock->regu;
                            $outchiller->no_mobil           =   $list->chiller->no_mobil;
                            $outchiller->qty_item           =   $list->qty;
                            $outchiller->berat_item         =   $list->berat;
                            $outchiller->stock_item         =   $list->qty;
                            $outchiller->stock_berat        =   $list->berat;
                            $outchiller->tanggal_potong     =   $freestock->tanggal;
                            $outchiller->tanggal_produksi   =   $freestock->tanggal;
                            $outchiller->status             =   4;
                            if (!$outchiller->save()) {
                                DB::rollBack();
                                return back()->with('status', 2)->with('message', 'Proses gagal');
                            }

                            $list->outchiller               =   $outchiller->id;
                            if (!$list->save()) {
                                DB::rollBack();
                                return back()->with('status', 2)->with('message', 'Proses gagal');
                            }

                            $chill                          =   Chiller::find($list->chiller_id);
                            $chill->stock_berat             =   $chill->stock_berat - $list->berat;
                            $chill->stock_item              =   $chill->stock_item - $list->qty;
                            if (!$chill->save()) {
                                DB::rollBack();
                                return back()->with('status', 2)->with('message', 'Proses gagal');
                            }
                        }
                    }
                }

            }
        }
    }


    public function destroy(Request $request)
    {
        // dd($request->all());
        if ($request->key == 'bahan_baku') {
            $data   =   FreestockList::find($request->row_id);

            if (Freestock::find($data->freestock_id)->status == 3) {
                $chiller                =   Chiller::find($data->chiller_id);
                $chiller->stock_item    =   ($chiller->stock_item + $data->qty);
                $chiller->stock_berat   =   ($chiller->stock_berat + $data->berat);
                $chiller->save();

                Chiller::find($data->outchiller)->delete();
            }

            if (Freestock::find($data->freestock_id)->status == 1) {
                if (FreestockList::where('freestock_id', $data->freestock_id)->count() < 2 && FreestockTemp::where('freestock_id', $data->freestock_id)->count() < 1) {
                    Freestock::find($data->freestock_id)->delete();
                }
            }

            return $data->delete();

            try {
                Chiller::recalculate_chiller($chiller->id);
            } catch (\Throwable $th) {

            }
        } else

        if ($request->key == 'bb_detail') {
            $data   =   FreestockList::find($request->row_id);

            if ($data->outchiller) {
                $getchiller                     =   Chiller::find($data->chiller_id);

                if($getchiller){
                    $getchiller->stock_item         =   ($getchiller->stock_item + $data->qty);
                    $getchiller->stock_berat        =   ($getchiller->stock_berat + $data->berat);
                    $getchiller->save();
                }

                Chiller::find($data->outchiller)->delete();
            }

            $origin = $data->toArray();

            $log                        =   new Adminedit();
            $log->user_id               =   Auth::user()->id ;
            $log->table_name            =   'chiller' ;
            $log->table_id              =   $data->id ;
            $log->type                  =   'delete' ;
            $log->activity              =   'kepala_regu_bb';
            $log->content               =   'Delete Bahan Baku' . ' '  . date("Y-m-d H:i:s");
            $log->data                  =   json_encode([
                    'data'              => $origin,
            ]) ;
            $log->save();

            return $data->delete();
            
        } else

        if ($request->key == 'hapus_fg') {
            $data               = FreestockTemp::find($request->row_id);
            if (Freestock::find($data->freestock_id)->status == 3) {
                $cekDataInAbf       = $data->freetempchiller->ambil_abf;
                $cekDataChillerBB   = $data->freetempchiller->ambil_chiller;
                $cekDataEkspedisi   = $data->freetempchiller->alokasi_order;
                if(count($cekDataInAbf) > 0){
                    $result['status']   =   400;
                    $result['msg']      =   "Gagal, Item sudah dialokasikan ke ABF";
                    return $result;
                }else
                if(count($cekDataChillerBB) > 0 ){
                    $result['status']   =   400;
                    $result['msg']      =   "Gagal, Item sudah dialokasikan sebagai Bahan Baku";
                    return $result;
                }else
                if(count($cekDataEkspedisi) > 0 ){
                    $result['status']   =   400;
                    $result['msg']      =   "Gagal, Item sudah dialokasikan ke pengiriman";
                    return $result;
                }else{

                    $chiller    =   Chiller::where('table_name', 'free_stocktemp')
                                            ->where('table_id', $data->id)
                                            ->first();
                    if($chiller){
                        $origin = $chiller->toArray();
                        $chiller->delete();

                        $log                        =   new Adminedit();
                        $log->user_id               =   Auth::user()->id ;
                        $log->table_name            =   'chiller' ;
                        $log->table_id              =   $chiller->id ;
                        $log->type                  =   'delete' ;
                        $log->activity              =   'kepala_regu_bb';
                        $log->content               =   'Delete Bahan Baku';
                        $log->data                  =   json_encode([
                                'data'              => $origin,
                        ]) ;
                        $log->save();

                    }
                }
            }
            $origin = $data->toArray();
            $log                        =   new Adminedit();
            $log->user_id               =   Auth::user()->id ;
            $log->table_name            =   'chiller' ;
            $log->table_id              =   $data->id ;
            $log->type                  =   'delete' ;
            $log->activity              =   'kepala_regu_bb';
            $log->content               =   'Delete Bahan Baku Freshgood';
            $log->data                  =   json_encode([
                    'data'              => $origin,
            ]) ;
            $log->save();

            return $data->delete();

            
            try {
                Chiller::recalculate_chiller($chiller->id);
            } catch (\Throwable $th) {

            }

        } else

        if ($request->key == 'hapus_produksi') {
            $dataHasilProduksi              = FreestockTemp::find($request->row_id);
            $freestoktemp                   = FreestockTemp::find($request->row_id);

            if ($freestoktemp) {
                if ($freestoktemp->bumbu_detail_id) {
                    $bumbu                  = Bumbu::find($freestoktemp->bumbu_id);
                    $berat_bumbu            = BumbuDetail::find($freestoktemp->bumbu_detail_id);
                    $bumbu->berat           += $berat_bumbu->berat;
                    // dd($bumbu);
                    $bumbu->save();
                    $berat_bumbu->delete();
                }
            }
            return FreestockTemp::find($request->row_id)->delete();

            if (Freestock::find($dataHasilProduksi->freestock_id)->status == 1) {
                if (FreestockList::where('freestock_id', $dataHasilProduksi->freestock_id)->count() < 1 && FreestockTemp::where('freestock_id', $dataHasilProduksi->freestock_id)->count() < 2) {
                    Freestock::find($dataHasilProduksi->freestock_id)->delete();
                }
            }

            $origin = $dataHasilProduksi->toArray();
            $log                        =   new Adminedit();
            $log->user_id               =   Auth::user()->id ;
            $log->table_name            =   'chiller' ;
            $log->table_id              =   $dataHasilProduksi->id ;
            $log->type                  =   'delete' ;
            $log->activity              =   'kepala_regu_bb';
            $log->content               =   'Delete Bahan Baku Freshgood';
            $log->data                  =   json_encode([
                    'data'              => $origin,
            ]) ;
            $log->save();
            return $dataHasilProduksi->delete();
        }

    }


    public function request_order(Request $request)
    {

            if ($request->key == 'view') {
                $regu     =   $request->regu ;
                $frozen   =   $request->frozen ;
                $fresh    =   $request->fresh ;
                $semua    =   $request->semua ;
                $jenis    =   "fresh";

                if($fresh=="on"){
                    $jenis    =   "fresh";
                }

                if($frozen=="on"){
                    $jenis    =   "frozen";
                }

                if($semua=="on"){
                    $jenis    =   "semua";
                }

                $data   =   OrderItem::select('order_items.*', 'orders.nama', 'orders.no_so', 'orders.tanggal_kirim', 'orders.tanggal_so', 'orders.sales_id', 'orders.no_do',
                                DB::raw("orders.nama as cust_nama"),
                                DB::raw("orders.created_at as created_at_order"),
                                DB::raw("order_items.edited as edit_item"),
                                DB::raw("order_items.deleted_at as delete_at_item"),
                                DB::raw("order_items.id as id"),
                                DB::raw("orders.status_so as order_status_so"),
                                DB::raw("marketing.nama_alias as marketing_nama"),
                                DB::raw("orders.keterangan as memo_header")
                            )
                            ->leftJoin('orders', 'orders.id', '=', 'order_items.order_id')
                            ->leftJoin('items', 'items.id', '=', 'order_items.item_id')
                            ->leftJoin('category', 'category.id', '=', 'items.category_id')
                            ->leftJoin('marketing', 'marketing.netsuite_internal_id', '=', 'orders.sales_id')
                            ->whereDate('orders.tanggal_kirim', ($request->tanggal ?? date("Y-m-d", strtotime('tomorrow'))))
                            ->where('sales_channel', '!=', 'By Product - Paket')
                            ->where(function($query) use ($regu) {
                                if(env('NET_SUBSIDIARY', 'CGL')=='CGL'){
                                    if ($regu == 'boneless') {
                                        $query->whereIn('items.category_id', [5, 11]) ;
                                    }
                                    if ($regu == 'parting') {
                                        $query->whereIn('items.category_id', [2]) ;
                                    }
                                    if ($regu == 'marinasi') {
                                        $query->whereIn('items.category_id', [3, 9]) ;
                                    }
                                    if ($regu == 'whole') {
                                        $query->whereIn('items.category_id', [1]) ;
                                    }
                                    if ($regu == 'frozen') {
                                        $query->whereIn('items.category_id', [7, 8, 9, 13]) ;
                                    }
                                }
                                if ($regu == 'byproduct') {
                                    $query->where('category.nama', 'By Product') ;
                                }
                                if ($regu == 'meyer') {
                                    $query->orWhere('orders.nama', 'like', '%' . $regu . '%') ;
                                }
                                if ($regu == 'admin-produksi') {
                                    $query->orWhere('orders.nama', 'like', '%' . "KARYAWAN CITRAGUNA" . '%') ;
                                }
                            })
                            ->where(function($query) use ($request) {
                                if ($request->cari) {
                                    $query->orWhere('orders.nama', 'like', '%' . $request->cari . '%') ;
                                    $query->orWhere('orders.no_so', 'like', '%' . $request->cari . '%') ;
                                    $query->orWhere('order_items.nama_detail', 'like', '%' . $request->cari . '%') ;
                                    $query->orWhere('category.nama', 'like', '%' . $request->cari . '%') ;
                                }
                            });

                if ($request->get == 'unduh') {
                    $dataUndurExcel = clone $data;
                    $dataUnduh           =   $dataUndurExcel->where(function($query) use ($jenis) {
                                                if ($jenis == 'semua') {
                                                }else{
                                                    if ($jenis == 'fresh') {
                                                        $query->where('order_items.nama_detail', 'not like', '%frozen%') ;
                                                    }
                                                    if ($jenis == 'frozen') {
                                                        $query->where('order_items.nama_detail', 'like', '%frozen%') ;
                                                    }
                                                }
                                            })
                                            ->where('orders.status_so', '!=', 'closed')
                                            ->orderBy('id', 'asc')
                                            ->get() ;
                    return view('admin.pages.regu.request_order.excel', compact('dataUnduh', 'regu', 'request')) ;
                } else {
                    $all                    = clone $data;
                    $alldata                = $all->where(function($query) use ($jenis) {
                                                if ($jenis == 'semua') {
                                                }else{
                                                    if ($jenis == 'fresh') {
                                                        $query->where('order_items.nama_detail', 'not like', '%frozen%') ;
                                                    }
                                                    if ($jenis == 'frozen') {
                                                        $query->where('order_items.nama_detail', 'like', '%frozen%') ;
                                                    }
                                                }
                                            })->orderBy('id', 'asc')->withTrashed()->get() ;

                    $queryDataQty           = clone $data;
                    $queryDataBerat         = clone $data;
                    $queryDataCustomer      = clone $data;
                    $queryDataFresh         = clone $data;
                    $queryDataFrozen        = clone $data;
                    $totalsum       =   [
                        'sumqty'          =>  $queryDataQty->sum('order_items.qty'),
                        'sumberat'        =>  $queryDataBerat->sum('order_items.berat'),
                        'sumcustomer'     =>  $queryDataCustomer->groupBy('orders.customer_id')->get()->count('orders.customer_id'),
                        'sumitemfresh'    =>  $queryDataFresh->where('order_items.nama_detail', 'not like', '%frozen%')->count(),
                        'sumitemfrozen'   =>  $queryDataFrozen->where('order_items.nama_detail', 'like', '%frozen%')->count(),
                    ];
                    // dd($alldata[0], $alldata[1]);
                    return view('admin.pages.regu.request_order.data', compact('alldata', 'regu', 'request','totalsum'));
                }
            }
            if ($request->key == 'view_modal_byorder') {
                // $produksiId =   Freestock::select('id')->where('orderitem_id',$request->id)->first();
                // $data       =   Freestock::with('freetemp')->find($request->id);
                $data       =   FreestockTemp::where('order_item_id',$request->id)->get();
                $cs         =   Customer::all();
                $plastik    =   Item::where('category_id', 25)->where('subsidiary', env('NET_SUBSIDIARY', 'EBA'))->pluck('nama', 'id') ;
                return view('admin.pages.regu.request_order.view_part',compact('data','cs','plastik')) ;
            }
            else {
                return view('admin.pages.regu.request_order.show.index') ;
            }

    }


    public function request_view(Request $request, $id)
    {
        // if (User::setIjin(45)) {
            $data   =   OrderItem::find($id) ;

            if ($data) {
                $regu       =   $request->regu ;
                $freestock  =   Freestock::where('orderitem_id', $data->id)
                                ->whereIn('status', [2,3])
                                ->first() ;

                return view('admin.pages.regu.request_order.show.index', compact('data', 'regu', 'freestock')) ;
            }

            return redirect()->route('regu.request_order') ;
        // }

        // return redirect()->route('index');
    }


    public function inject(Request $request)
    {
        $data   =   FreestockTemp::whereDate('tanggal_produksi', $request->tanggal)
                    ->where('regu', '!=', 'byproduct')
                    ->get();

        $view   =   '' ;
        foreach ($data as $row) {
            $exp        =   json_decode($row->label);
            $sub        =   explode(' || ', $exp->sub_item);

            if ($sub[0]) {
                $konsumen   =   Customer::where('nama', $sub[0])->first();
                $row->customer_id   =   $konsumen->id ;
                $row->save() ;

                $view   .=  '(' . $row->id . ') - ' . $row->customer_id . ' // ' . $sub[0] . "<br><br>" ;
            }
        }

        return $view ;
    }

    public function injectplastik(Request $request)
    {
        $data   =   FreestockTemp::whereDate('tanggal_produksi', $request->tanggal)
                    ->get();

        $run    =   '' ;
        foreach ($data as $row) {
            $exp        =   json_decode($row->label);

            if ($exp->plastik) {
                $run    .=  'FS-' . $row->freestock_id . ' // ' . $row->item->nama . ' // ' . $exp->plastik->sku . ' - ' . $exp->plastik->jenis . ' (' . $exp->plastik->qty . ')<br>' ;
                $row->prod_nama         =   Item::find($row->item_id)->nama ;
                $row->plastik_sku       =   $exp->plastik->sku ;
                $row->plastik_nama      =   $exp->plastik->jenis ;
                $row->plastik_qty       =   $exp->plastik->qty ;
                $row->save() ;
            }
        }

        return $run ;
    }

    public function parking_order(Request $request){

        $search             =   $request->search ?? '';
        $filterCustomer     =   $request->customer ?? '';
        $filterMarketing    =   $request->marketing ?? '';
        $tanggalKirim       =   $request->tanggalkirim ?? 0;

        $data       =   MarketingSO::where(function($query) use ($search, $filterCustomer, $filterMarketing, $tanggalKirim, $request) {
            if (Auth::user()->account_role != 'superadmin') {
                if ((!User::setIjin(40) && !User::setIjin(38)) || (!User::setIjin(41) && !User::setIjin(38))) {
                    $query->where('user_id', Auth::user()->id) ;
                }
            }
            if($search) {
                $query->orWhere('memo', 'like', '%' . $search . '%') ;
                $query->orWhere('po_number', 'like', '%' . $search . '%') ;
            }
            if($filterCustomer !== '') {
                $query->where('customer_id', $filterCustomer) ;
            }
            if($filterMarketing !== '') {
                $query->where('user_id', $filterMarketing);
            }
            if($tanggalKirim == 1){
                $query->whereBetween('tanggal_kirim', [($request->tanggal_awal ?? date("Y-m-d")), ($request->tanggal_akhir ?? date("Y-m-d"))]);
            } else {
                $query->whereBetween('tanggal_so', [($request->tanggal_awal ?? date("Y-m-d")), ($request->tanggal_akhir ?? date("Y-m-d"))]);
            }
        })
        ->where('marketing_so.subsidiary', Session::get('subsidiary'))
        ->orderBy('id', 'desc')
        ->paginate(10);
        return view('admin.pages.regu.tab_order.data-list-marketing-so', compact('data','customer','item','plastik', 'cust'));
    }

    public function purchasing_retur(Request $request, $id)
    {
        $data   =   Purchasing::where('id', $id)
                    ->where('type_po', '!=', 'PO LB')
                    ->first() ;

        if ($data) {
            $alasan =   Returalasan::get() ;
            return view('admin.pages.purchasing.retur.index', compact('data', 'alasan'));
        } else {
            return redirect()->route('purchasing.index');
        }

    }

    public function order_produksi(Request $request)
    {
        $tanggal = $request->tanggal_kirim ?? date('Y-m-d');
        $cari = $request->cari_order;
        $today = Carbon::today();
        $nextday=[];
        for ($i=0; $i < 7; $i++) {
            $nextday[]=$today->format('Y-m-d');
            $today->addDay();
        }
        $data_order = MarketingSOList::select(
            'marketing_so.tanggal_so',
            'marketing_so.no_so',
            'marketing_so.created_at as created_at_order',
            'marketing_so.tanggal_kirim',
            'marketing_so.status as status_so_order',
            'customers.nama AS nama_customer',
            'customers.id AS customer_id',
            'users.name AS nama_marketing',
            'users.id AS user_id',
            'marketing_so_list.*',
        )
        ->leftJoin('marketing_so', 'marketing_so.id', '=', 'marketing_so_list.marketing_so_id')
        ->leftJoin('customers', 'customers.id', '=', 'marketing_so.customer_id')
        ->leftJoin('users', 'users.id', '=', 'marketing_so.user_id')
        ->leftJoin('items', 'items.id', '=', 'marketing_so_list.item_id')
        ->where('marketing_so.tanggal_kirim', $tanggal)
        ->where(function($query) use ($cari) {
            if ($cari) {
                $query->orWhere('users.name', 'like', '%' . $cari . '%');
                $query->orWhere('customers.nama', 'like', '%' . $cari . '%');
                $query->orWhere('items.nama', 'like', '%' . $cari . '%');
            }
        })
        ->orderBy('marketing_so_list.updated_at','desc');
        // ->where('marketing_so.status',3);


        $data = (clone $data_order)->paginate(15);
        $summary = [
            'total_qty'         => (clone $data_order)->sum('marketing_so_list.qty'),
            'total_berat'       => (clone $data_order)->sum('marketing_so_list.berat'),
            'total_order'       => (clone $data_order)->count(),
            'total_customer'    => (clone $data_order)->groupBy('marketing_so.customer_id')->get()->count('marketing_so.customer_id'),
            'total_pending'     => (clone $data_order)->where('marketing_so.status',1)->count(),
            'total_verifikasi'  => (clone $data_order)->where('marketing_so.status',3)->count(),
            'total_batal'       => (clone $data_order)->where('marketing_so.status',0)->count(),
            'total_edit'        => (clone $data_order)->whereNotNull('marketing_so_list.edited')->count(),

        ];
        return view('admin.pages.regu.tab_order.order-produksi',compact('data','tanggal','nextday','summary'));
    }

    public function paginate($items, $perPage = 10, $page = null, $options = [])
    {
        $options = [
            'path'      => LengthAwarePaginator::resolveCurrentPath(),
            'pageName'  => 'page'
        ];
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
}
