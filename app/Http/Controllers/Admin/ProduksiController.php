<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Abf;
use App\Models\Adminedit;
use App\Models\Bahanbaku;
use App\Models\Bom;
use App\Models\Chiller;
use App\Models\Customer;
use App\Models\DataOption;
use App\Models\Freestock;
use App\Models\FreestockList;
use App\Models\FreestockTemp;
use App\Models\Gudang;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Netsuite;
use App\Models\Log;
use App\Models\MarketingSO;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Mockery\Undefined;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ProduksiController extends Controller
{

    public function store(Request $request)
    {
        if ($request->key == 'selesaikan') {

            DB::beginTransaction();

            $freestock  =   Freestock::where('status', 1)
                ->where(function ($query) use ($request) {
                    if ($request->jenis == 'boneless') {
                        $query->whereIn('kategori', [5, 11]);
                    } else
                                if ($request->jenis == 'parting') {
                        $query->where('kategori', 2);
                    } else
                                if ($request->jenis == 'marinasi') {
                        $query->where('kategori', 3);
                    } else
                                if ($request->jenis == 'whole') {
                        $query->where('kategori', 1);
                    } else
                                if ($request->jenis == 'frozen') {
                        $query->whereIn('kategori', [7, 8, 9, 13]);
                    }
                })
                ->first();


            $bb_list        =   FreestockList::where('freestock_id', $freestock->id)->get();

            $data_transfer  =   [];
            $item_netsuite  =   [];
            foreach ($bb_list as $row) {
                $data_transfer[]    =   [
                    "internal_id_item"  =>  (string)$row->item->netsuite_internal_id,
                    "item"              =>  (string)$row->item->sku,
                    "qty_to_transfer"   =>  (string)$row->berat
                ];

                $item_netsuite  =   [
                    [
                        "type"              =>  "Component",
                        "internal_id_item"  =>  (string)$row->item->netsuite_internal_id,
                        "item"              =>  (string)$row->item->sku,
                        "description"       =>  (string)$row->item->nama,
                        "qty"               =>  (string)$row->berat,
                    ]
                ];
            }

            // ==============================================
            // Transfer Inventory
            // ==============================================
            $netsuite                   =   new Netsuite;
            $netsuite->record_type      =   "transfer_inventory";
            $netsuite->label            =   "ti_bahanbaku_produksi";
            $netsuite->trans_date       =   Carbon::now();
            $netsuite->user_id          =   Auth::user()->id;
            $netsuite->tabel            =   "free_stock";
            $netsuite->tabel_id         =   $freestock->id;
            $netsuite->subsidiary_id    =   env("NET_SUBSIDIARY_ID", "6");
            $netsuite->subsidiary       =   env("NET_SUBSIDIARY", "CGL");
            $netsuite->id_location      =   Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Chiller Bahan Baku");
            $netsuite->location         =   env('NET_SUBSIDIARY', 'CGL')." - Chiller Bahan Baku";

            if (!$netsuite->save()) {
                DB::rollBack();
                $response = array(
                    'code'    => "0",
                    'status'   => "Failed",
                    'message'   => "insert transfer inventory failed"
                );
                return $response;
            }

            $net    =   Netsuite::find($netsuite->id);
            $data   =   [
                "record_type"   =>  "transfer_inventory",
                "data"          =>  [
                    [
                        "appsid"                    =>  env('NET_SUBSIDIARY', 'CGL')."-"."$net->id",
                        "transaction_date"          =>  date("d-M-Y"),
                        "memo"                      =>  "",
                        "from_gudang"               =>  Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Chiller Bahan Baku"),
                        "to_gudang"                 =>  Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Storage Produksi (WIP)"),
                        "internal_id_subsidiary"    =>  env("NET_SUBSIDIARY_ID", "6"),
                        "subsidiary"                =>  env("NET_SUBSIDIARY", "CGL"),
                        "line"                      =>  $data_transfer,
                    ]
                ]
            ];

            $net->script            =   '228';
            $net->deploy            =   '1';
            $net->data_content      =   json_encode($data);
            $net->status            =   2;

            if (!$net->save()) {
                DB::rollBack();
                $response = array(
                    'code'    => "0",
                    'status'   => "Failed",
                    'message'   => "update data transfer inventory failed"
                );
                return $response;
            }


            // ==============================================
            // Work Order - 2
            // ==============================================
            $produk         =   FreestockTemp::where('freestock_id', $freestock->id)->get();

            $netsuite                   =   new Netsuite;
            $netsuite->record_type      =   "work_order";
            $netsuite->label            =   "wo-2";
            $netsuite->trans_date       =   Carbon::now();
            $netsuite->user_id          =   Auth::user()->id;
            $netsuite->tabel            =   "free_stock";
            $netsuite->tabel_id         =   $freestock->id;
            $netsuite->subsidiary_id    =   env("NET_SUBSIDIARY_ID", "6");
            $netsuite->subsidiary       =   env("NET_SUBSIDIARY", "CGL");
            $netsuite->id_location      =   Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Storage Produksi (WIP)");
            $netsuite->location         =   env('NET_SUBSIDIARY', 'CGL')." - Storage Produksi (WIP)";

            if (!$netsuite->save()) {
                DB::rollBack();
                $response = array(
                    'code'    => "0",
                    'status'   => "Failed",
                    'message'   => "insert work order failed"
                );
                return $response;
            }

            if ($request->jenis == 'boneless') {
                $id_item_assembly   =   Bom::where('bom_name', env("NET_SUBSIDIARY", "CGL") . " - KARKAS - BONELESS BROILER")->first()->netsuite_internal_id ;
                $item_assembly      =   env("NET_SUBSIDIARY", "CGL") . " - KARKAS - BONELESS BROILER";
            } else
            if ($request->jenis == 'parting') {
                $id_item_assembly   =   Bom::where('bom_name', env("NET_SUBSIDIARY", "CGL") . " - AYAM PARTING BROILER")->first()->netsuite_internal_id ;
                $item_assembly      =   env("NET_SUBSIDIARY", "CGL") . " - AYAM PARTING BROILER";
            } else
            if ($request->jenis == 'marinasi') {
                $id_item_assembly   =   Bom::where('bom_name', env("NET_SUBSIDIARY", "CGL") . " - AYAM PARTING MARINASI BROILER")->first()->netsuite_internal_id ;
                $item_assembly      =   env("NET_SUBSIDIARY", "CGL") . " - AYAM PARTING MARINASI BROILER";
            } else
            if ($request->jenis == 'whole') {
                $id_item_assembly   =   Bom::where('bom_name', env("NET_SUBSIDIARY", "CGL") . " - AYAM KARKAS BROILER")->first()->netsuite_internal_id ;
                $item_assembly      =   env("NET_SUBSIDIARY", "CGL") . " - AYAM KARKAS BROILER";
            } else
            if ($request->jenis == 'frozen') {
                $id_item_assembly   =   Bom::where('bom_name', env("NET_SUBSIDIARY", "CGL") . " - AYAM KARKAS FROZEN")->first()->netsuite_internal_id ;
                $item_assembly      =   env("NET_SUBSIDIARY", "CGL") . " - AYAM KARKAS FROZEN";
            }


            $total              =   0;
            $data_produksi      =   [];
            $data_components    =   [];
            foreach ($produk as $row) {
                $chiller                    =   new Chiller;
                $chiller->table_name        =   'free_stocktemp';
                $chiller->table_id          =   $row->id;
                $chiller->asal_tujuan       =   'free_stock';
                $chiller->item_id           =   $row->item_id;
                $chiller->item_name         =   $row->item->nama;
                $chiller->jenis             =   'masuk';
                $chiller->type              =   'hasil-produksi';
                $chiller->kategori          =   $row->kategori;
                $chiller->label             =   $row->label;
                $chiller->berat_item        =   $row->qty;
                $chiller->tanggal_produksi  =   $row->tanggal_produksi;
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

                $total  +=  $row->qty;

                $data_produksi[]    =   [
                    "type"              =>  "Finished Goods",
                    "internal_id_item"  =>  (string)$row->item->netsuite_internal_id,
                    "item"              =>  (string)$row->item->sku,
                    "description"       =>  (string)$row->item->nama,
                    "qty"               =>  (string)$row->qty,
                ];

                if ($row->label) {
                    // $exp    =   json_decode($row->label);
                    // if ($exp->plastik->sku) {
                    //     $data_components[]  =   [
                    //         "type"              =>  "Components",
                    //         "internal_id_item"  =>  (string)Item::item_sku($exp->plastik->sku)->netsuite_internal_id ,
                    //         "item"              =>  (string)$exp->plastik->sku ,
                    //         "description"       =>  (string)$exp->plastik->jenis ,
                    //         "qty"               =>  (string)$exp->plastik->qty,
                    //     ];
                    // }
                    // if ($exp->parting->qty) {
                    //     $data_components[]  =   [
                    //         "type"              =>  "Components",
                    //         "internal_id_item"  =>  (string)Item::item_sku($exp->plastik->sku)->netsuite_internal_id ,
                    //         "item"              =>  (string)$exp->plastik->sku ,
                    //         "description"       =>  (string)$exp->plastik->nama ,
                    //         "qty"               =>  (string)$exp->plastik->qty,
                    //     ];
                    // }
                }
            }

            // ==============================================
            // Work Order - 2 WO
            // ==============================================


            $net    =   Netsuite::find($netsuite->id);

            $data   =   [
                "record_type"     =>     "work_order",
                "data"          =>  [
                    [
                        "appsid"                    =>  env('NET_SUBSIDIARY', 'CGL')."-"."$net->id",
                        "internal_id_subsidiary"    =>  env("NET_SUBSIDIARY_ID", "6"),
                        "subsidiary"                =>  env("NET_SUBSIDIARY", "CGL"),
                        "transaction_date"          =>  date("d-M-Y"),
                        "internal_id_customer"      =>  "",
                        "customer"                  =>  "",
                        "id_item_assembly"          =>  $id_item_assembly,
                        "item_assembly"             =>  $item_assembly,
                        "id_location"               =>  "120",
                        "location"                  =>  env('NET_SUBSIDIARY', 'CGL')." - Storage Produksi (WIP)",
                        "plan_qty"                  =>  "$total",
                        "items"                     =>  $item_netsuite
                    ]
                ]
            ];

            $net->script            =   '226';
            $net->deploy            =   '1';
            $net->data_content      =   json_encode($data);
            $net->status            =   2;

            if (!$net->save()) {
                DB::rollBack();
                $response = array(
                    'code'    => "0",
                    'status'   => "Failed",
                    'message'   => "update data work order failed"
                );
                return $response;
            }


            // ==============================================
            // Work Order - 2 BUILD
            // ==============================================
            $netsuite                   =   new Netsuite;
            $netsuite->record_type      =   "wo_build";
            $netsuite->label            =   "wo-2-build";
            $netsuite->trans_date       =   Carbon::now();
            $netsuite->user_id          =   Auth::user()->id;
            $netsuite->tabel            =   "free_stock";
            $netsuite->tabel_id         =   $freestock->id;
            $netsuite->subsidiary_id    =   env("NET_SUBSIDIARY_ID", "6");
            $netsuite->subsidiary       =   env("NET_SUBSIDIARY", "CGL");
            $netsuite->id_location      =   "120";
            $netsuite->location         =   env('NET_SUBSIDIARY', 'CGL')." - Storage Produksi (WIP)";

            if (!$netsuite->save()) {
                DB::rollBack();
                $response = array(
                    'code'    => "0",
                    'status'   => "Failed",
                    'message'   => "insert wo build failed"
                );
                return $response;
            }

            $net    =   Netsuite::find($netsuite->id);
            $data   =   [
                "record_type"     =>     "wo_build",
                "data"          =>  [
                    [
                        "appsid"                    =>  env('NET_SUBSIDIARY', 'CGL')."-".(string)$net->id,
                        "transaction_date"          =>  date("d-M-Y"),
                        "qty_to_build"              =>  "$total",
                        "created_from_wo"           =>  "",
                        "items"                     =>  array_merge($data_produksi, $data_components)
                    ]
                ]
            ];

            $net->script            =   '229';
            $net->deploy            =   '1';
            $net->data_content      =   json_encode($data);
            $net->status            =   2;

            if (!$net->save()) {
                DB::rollBack();
                $response = array(
                    'code'    => "0",
                    'status'   => "Failed",
                    'message'   => "update data wo build failed"
                );
                return $response;
            }

            $freestock->status      =   2;
            if (!$freestock->save()) {
                DB::rollBack();
                $response = array(
                    'code'    => "0",
                    'status'   => "Failed",
                    'message'   => "update freestock failed"
                );
                return $response;
            }
        } else {
            $tunggir    =   FALSE;
            $lemak      =   FALSE;
            $maras      =   FALSE;

            if ($request->jenis == 'whole_chicken') {
                $kategori   =   1;
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
            } else
            if ($request->jenis == 'parting') {
                $kategori   =   2;
            } else
            if ($request->jenis == 'parting_marinasi') {
                $kategori   =   3;
            } else
            if ($request->jenis == 'boneless') {
                $kategori   =   5;
            } else
            if ($request->jenis == 'boneless_frozen') {
                $kategori   =   11;
            } else
            if ($request->jenis == 'frozen') {
                $kategori   =   Item::find($request->item)->category_id;
            }

            if ($request->plastik != 'Curah') {
                $plastik    =   Item::find($request->plastik);
            }


            $freestock  =   Freestock::where('status', 1)
                ->where(function ($query) use ($request) {
                    if ($request->jenis == 'boneless') {
                        $query->whereIn('kategori', [5, 11]);
                    } else
                        if ($request->jenis == 'parting') {
                        $query->where('kategori', 2);
                    } else
                        if ($request->jenis == 'marinasi') {
                        $query->where('kategori', 3);
                    } else
                        if ($request->jenis == 'whole') {
                        $query->where('kategori', 1);
                    } else
                        if ($request->jenis == 'frozen') {
                        $query->whereIn('kategori', [7, 8, 9, 13]);
                    }
                })
                ->first();

            $label                  =   [
                'plastik'   =>  [
                    'sku'   =>  $plastik->sku ?? NULL,
                    'jenis' =>  $plastik->nama ?? NULL,
                    'qty'   =>  $request->jumlah_plastik ?? NULL
                ],
                'parting'   =>  [
                    'qty'   =>  $request->parting ?? NULL
                ],
                'additional' =>  [
                    'tunggir'   =>  $tunggir,
                    'lemak'     =>  $lemak,
                    'maras'     =>  $maras,
                ]
            ];

            // "#" . $request->plastik . ($request->parting ? "#Parting " . $request->parting : ''). ($additional ? ('#' . $additional) : '');

            $temp                   =   new FreestockTemp;
            $temp->freestock_id     =   $freestock->id;
            $temp->item_id          =   $request->item;
            $temp->kategori         =   $kategori;
            $temp->tanggal_produksi =   Carbon::now();
            $temp->label            =   json_encode($label);
            $temp->qty              =   $request->berat;
            $temp->save();
        }

        DB::commit();

        $response = array(
            'code'    => "1",
            'status'   => "Sukses",
            'message'   => "insert all data sukses"
        );
        return $response;
    }

    public function destroy(Request $request)
    {
        if ($request->key == 'bahan_baku') {
            $data                   =   FreestockList::find($request->row_id)->first();

            $chiller                =   Chiller::find($data->chiller_id);
            $chiller->berat_item    =   $chiller->berat_item + $data->qty;
            $chiller->stock_berat   =   $chiller->stock_berat + $data->qty;
            $chiller->save();

            
            $data->delete();
            
            try {
                Chiller::recalculate_chiller($chiller->id);
            } catch (\Throwable $th) {
                
            }
        } else

        if ($request->key == 'hapus_produksi') {
            return FreestockTemp::find($request->row_id)->delete();
        }
    }

    public function ambilBB(Request $request)
    {
        DB::beginTransaction();

        for ($x = 0; $x < COUNT($request->x_code); $x++) {
            if ($request->qty[$x]) {
                if ($request->kategori == 'whole') {
                    $kategori   =   1;
                } else
                if ($request->kategori == 'parting') {
                    $kategori   =   2;
                } else
                if ($request->kategori == 'marinasi') {
                    $kategori   =   3;
                } else
                if ($request->kategori == 'boneless') {
                    $kategori   =   5;
                }
                if ($request->kategori == 'frozen') {
                    $kategori   =   7;
                }

                $freestock  =   Freestock::where('kategori', $kategori)->where('status', 1)->first();

                if (!$freestock) {
                    $freestock                  =   new Freestock;
                    $freestock->nomor           =   Freestock::get_nomor();
                    $freestock->tanggal         =   Carbon::now();
                    $freestock->user_id         =   Auth::user()->id;
                    $freestock->kategori        =   $kategori;
                    $freestock->status          =   1;
                    if (!$freestock->save()) {
                        DB::rollBack();
                    }
                }

                $chiller                        =   Chiller::find($request->x_code[$x]);

                $sisaQtyChiller                 = Chiller::ambilsisachiller($chiller->id,'qty_item','qty','bb_item');
                $sisaBeratChiller               = Chiller::ambilsisachiller($chiller->id,'berat_item','berat','bb_berat');
                $convertSisaBerat               = number_format((float)$sisaBeratChiller, 2, '.', '');
                if($request->kategori != 'boneless'){
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
                $list->qty                      =   $request->qty[$x];
                $list->berat                    =   $request->berat[$x];
                $list->sisa                     =   $request->qty[$x];
                if (!$list->save()) {
                    DB::rollBack();
                }

                // $chiller->berat_item            =   $chiller->berat_item - $request->qty[$x];
                $chiller->stock_berat           =   $chiller->stock_berat - $request->berat[$x];
                $chiller->stock_item            =   $chiller->stock_item - $request->qty[$x];
                if (!$chiller->save()) {
                    DB::rollBack();
                }

                $outchiller                     =   new Chiller;
                $outchiller->table_name         =   'free_stock';
                $outchiller->table_id           =   $freestock->id;
                $outchiller->asal_tujuan        =   'free_stock';
                $outchiller->item_id            =   $chiller->item_id;
                $outchiller->item_name          =   $chiller->item_name;
                $outchiller->jenis              =   'keluar';
                $outchiller->type               =   'pengambilan-bahan-baku';
                $outchiller->no_mobil           =   $chiller->no_mobil;
                $outchiller->qty_item           =   $request->qty[$x];
                $outchiller->berat_item         =   $request->berat[$x];
                $outchiller->stock_item         =   $request->qty[$x];
                $outchiller->stock_berat        =   $request->berat[$x];
                $outchiller->tanggal_potong     =   Carbon::now();
                $outchiller->tanggal_produksi   =   Carbon::now();
                $outchiller->status             =   4;
                if (!$outchiller->save()) {
                    DB::rollBack();
                }
            }
        }

        DB::commit();
        try {
            Chiller::recalculate_chiller($chiller->id);
        } catch (\Throwable $th) {
            
        }
        return back()->with('status', 1)->with('message', 'Tambah bahan baku berhasil');
    }

    public function salesOrder(Request $request)
    {

        $regu           =   strtolower($request->regu);
        $exp            =   explode('_', $regu);
        $tanggal        =   $exp[1] ?? '';
        $search         =   $exp[2] ?? '';
        $jenis          =   $request->jenis ?? '' ;

        // $searchValues = explode(' ', $search);
        $regu_select    =   "";
        if($tanggal==''){
            $tanggal = date('Y-m-d');
        }
        $kategori = [];
        if ($exp[0] == 'boneless') {
            $kategori       =   [5, 11];
            $regu_select    =   "boneless";
        } elseif ($exp[0] == 'parting') {
            $kategori       =   [2];
            $regu_select    =   "parting";
        } elseif ($exp[0] == 'parting marinasi') {
            $kategori       =   [3,9];
            $regu_select    =   "marinasi";
        } elseif ($exp[0] == 'whole chicken') {
            $kategori       =   [1];
            $regu_select    =   "whole";
        } elseif ($exp[0] == 'frozen') {
            $kategori       =   [7, 8, 9, 13];
            $regu_select    =   "frozen";
        }

        $pending    = OrderItem::select("*", DB::raw("orders.nama as cust_nama"),
                                        DB::raw("orders.created_at as created_at_order"),
                                        DB::raw("order_items.edited as edit_item"),
                                        DB::raw("order_items.id as id"),
                                        DB::raw("orders.status_so as order_status_so"),
                                        DB::raw("marketing.nama_alias as marketing_nama"),
                                        DB::raw("order_items.deleted_at as delete_at_item"))
                                ->join('orders', 'orders.id', '=', 'order_items.order_id')
                                ->join('items', 'items.id', '=', 'order_items.item_id')
                                ->join('category', 'category.id', '=', 'items.category_id')
                                ->leftJoin('marketing', 'marketing.netsuite_internal_id', '=', 'orders.sales_id')
                                ->whereNotIn('sales_channel', ["By Product - Paket", "By Product - Retail"])
                                ->where(function($query) use ($kategori, $jenis, $search){
                                    if ($kategori) {
                                        $query->whereIn('items.category_id', $kategori);
                                    }

                                    if ($jenis) {
                                        if ($jenis == 'pending') {
                                            $query->where('orders.status', NULL) ;
                                        }
                                        if ($jenis == 'selesai') {
                                            $query->where('orders.status', 10) ;
                                        }
                                    }
                                })
                                ->where(function($query) use ($kategori, $jenis, $search){
                                    if ($search) {
                                        $query->orWhere('items.nama','like', '%'.$search.'%');
                                        $query->orWhere('orders.nama','like', '%'.$search.'%');
                                    }
                                })
                                ->where('orders.tanggal_kirim', $tanggal)
                                ->withTrashed()
                                ->get();

        $regu = $regu_select;

        // return $pending;

        return view('admin.pages.regu.tab_order.list-order', compact('pending', 'regu', 'regu_select', 'tanggal', 'kategori', 'search'));
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

                $chiler                     =   new Bahanbaku;
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
                    $to     =   Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL').' - Storage ABF') ;
                } else {

                    $chiler                     =   new Chiller;
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
                    $cekchiller->berat_item     =   $cekchiller->berat_item - $chiler->berat_item ;

                    $cekchiller->stock_item     =   $cekchiller->stock_item - $chiler->qty_item ;
                    $cekchiller->qty_item       =   $cekchiller->qty_item - $chiler->qty_item ;
                    if (!$cekchiller->save()) {
                        DB::rollBack() ;
                        $data['status'] =   400;
                        $data['msg']    =   'Terjadi kesalahan proses order' ;
                        return $data ;
                    }

                    $label  =   "ti_fg_ekspedisi" ;
                    $to     =   Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL').' - Storage Expedisi') ;
                }

                $countorderitem             =   OrderItem::where('order_id', $order[$x])->where('status', null)->count();
                if ($countorderitem == 0) {
                    $so                         =   Order::find($xcode[$x]);
                    $so->status                 =   5;
                    if (!$so->save()) {
                        DB::rollBack() ;
                        $data['status'] =   400;
                        $data['msg']    =   'Terjadi kesalahan proses order' ;
                        return $data ;
                    }
                }

                $oritem                     =   OrderItem::find($order[$x]);
                $oritem->fulfillment_berat  =   $berat[$x];
                $oritem->fulfillment_qty    =   $qty[$x];
                $oritem->status             =   1;
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
            Netsuite::transfer_inventory($net[$x]['nama_tabel'], $net[$x]['id_tabel'], $net[$x]['label'], $net[$x]['id_location'], $net[$x]['location'], $net[$x]['from'], $net[$x]['to'], $net[$x]['transfer'], null) ;
        }

        return back()->getTargetUrl()."?tanggal=".$request->tanggal;

    }

    public function summary(Request $request)
    {
        $tanggal        =   $request->tanggal ?? date('Y-m-d');
        $regu           =   $request->regu ;

        $clone_data     =   FreestockList::select(DB::raw("SUM(berat) AS kg"), DB::raw("SUM(qty) AS jumlah"), 'free_stocklist.item_id', 'free_stocklist.freestock_id', 'items.nama', 'items.sku', 'chiller.type')
                                ->where('free_stock.regu', $regu)
                                ->where('free_stock.status', '3')
                                ->where('free_stock.tanggal', $tanggal)
                                ->leftJoin('items', 'items.id', '=', 'free_stocklist.item_id')
                                ->leftJoin('chiller', 'chiller.id', '=', 'free_stocklist.chiller_id')
                                ->leftJoin('free_stock', 'free_stock.id', '=', 'free_stocklist.freestock_id')
                                ->orderBy('items.nama')
                                ->groupBy('items.nama')
                                ->groupBy('chiller.type');
                        

        $clone_produksi   =   FreestockTemp::select(DB::raw("SUM(qty) AS jumlah"), DB::raw("SUM(berat) AS kg"), 'free_stocktemp.item_id', 'items.nama','items.sku', 'free_stocktemp.*')
                                ->where('free_stock.regu', $regu)
                                ->where('free_stock.status', '3')
                                ->where('free_stock.tanggal', $tanggal)
                                ->leftJoin('items', 'items.id', '=', 'free_stocktemp.item_id')
                                ->leftJoin('free_stock', 'free_stock.id', '=', 'free_stocktemp.freestock_id')
                                ->orderBy('items.nama')
                                ->groupBy('items.nama');


        $bahan_baku          = clone $clone_data;
        $produksi            = clone $clone_produksi;

        $netsuite_null_bb           = clone $clone_data;
        $netsuite_null_pb           = clone $clone_produksi;

        $inputbyorder_bb           = clone $clone_data;
        $inputbyorder_pb           = clone $clone_produksi;

        // $bahan_baku = $bahan_baku->whereNull('free_stock.netsuite_send')->get(); 
        // $produksi   = $produksi->whereNull('free_stock.netsuite_send')->get();
        $bahan_baku = (clone $clone_data)->whereNull('free_stock.netsuite_send')->get();
        $produksi = (clone $clone_produksi)->whereNull('free_stock.netsuite_send')->get();


        $getDataFG      = clone $clone_produksi;
        $getDataNONWO   = clone $clone_data;
        
        $netsuite_null_bb = $netsuite_null_bb->where('free_stock.netsuite_send', '0')->whereNull('free_stock.orderitem_id')->get();
        $netsuite_null_pb = $netsuite_null_pb->where('free_stock.netsuite_send', '0')->whereNull('free_stock.orderitem_id')->get();

        $inputbyorder_bb = $inputbyorder_bb->where('free_stock.netsuite_send', '0')->whereNotNull('free_stock.orderitem_id')->get();
        $inputbyorder_pb = $inputbyorder_pb->where('free_stock.netsuite_send', '0')->whereNotNull('free_stock.orderitem_id')->get();

        $berat_bb=[];
        $qty_bb=[];
        $qty_prod=0;
        $berat_prod=0;
        $id_item_bb=[];
        $id_item_prod=[];
        $item_sama_bb=[];
        $cek_bb    = FreestockList::cek_bb_non_wo($regu,$tanggal, $tanggal);
        $cek_prod  = FreestockTemp::cek_non_wo_produksi($regu,$tanggal,$tanggal);




        $arrayBBWO                  = [];
        $arrayFGWO                  = [];
        $arrayNONWOBB               = [];
        $arrayNONWOFG               = [];
        $collectionQueryBBWO        = new Collection();
        $collectionQueryFGWO        = new Collection();
        $collectionNetsuiteNullBB   = new Collection();
        $collectionNetsuiteNullFG   = new Collection();

        // NEW
            foreach ($cek_bb as $item_bb) {
                foreach ($cek_prod as $item_prod) {
                    if ($item_bb->item_id == $item_prod->item_id) {
                        
                        // JIKA BB LEBIH BANYAK DARI FG
                        if ($item_bb->berat > $item_prod->berat) {

                            // SIMPAN DATA ITEM KE ARRAY
                            $id_item_bb[]   = $item_bb->item_id;

                            // $arrayBBWO[] = collect([
                            //     'item_id'   => $item_bb->item_id,
                            //     'berat'     => $item_bb->berat - $item_prod->berat,
                            //     'qty'       => $item_bb->qty,
                            //     'type'      => $item_bb->type
                            // ]);

                            $collectionQueryBBWO->push((object)
                            [
                                'item_id'   => $item_bb->item_id,
                                'berat'     => $item_bb->berat - $item_prod->berat,
                                'qty'       => $item_bb->qty - $item_prod->qty,
                                'type'      => $item_bb->type
            
                            ]);


                            $collectionNetsuiteNullBB->push((object)
                            [
                                'item_id'   => $item_prod->item_id,
                                'berat'     => $item_prod->berat,
                                'qty'       => $item_prod->qty,
                                'type'      => $item_bb->type
            
                            ]);

                            $collectionNetsuiteNullFG->push((object)
                            [
                                'item_id'   => $item_prod->item_id,
                                'berat'     => $item_prod->berat,
                                'qty'       => $item_prod->qty,
                                'type'      => $item_bb->type
            
                            ]);
                            
                        } else if ($item_prod->berat > $item_bb->berat) {
                            // dd($item_prod->berat);
                            // SIMPAN DATA ITEM KE ARRAY
                            $id_item_prod[]   = $item_prod->item_id;

                            $collectionQueryFGWO->push((object)
                            [
                                'item_id'   => $item_prod->item_id,
                                'berat'     => $item_prod->berat - $item_bb->berat,
                                'qty'       => $item_prod->qty - $item_bb->qty,
                                'type'      => $item_prod->type
            
                            ]);


                            $collectionNetsuiteNullBB->push((object)
                            [
                                'item_id'   => $item_bb->item_id,
                                'berat'     => $item_bb->berat,
                                'qty'       => $item_bb->qty,
                                'type'      => $item_bb->type
            
                            ]);

                            $collectionNetsuiteNullFG->push((object)
                            [
                                'item_id'   => $item_bb->item_id,
                                'berat'     => $item_bb->berat,
                                'qty'       => $item_bb->qty,
                                'type'      => $item_bb->type
            
                            ]);

                            // dd($arrayNONWO);
                        }
                    }
                }
            }

            // dd($collectionNetsuiteNullBB);
            // QUERY BAHAN BAKU KIRIM WO
            // $queryBBWO                 = (clone $clone_data)->whereNull('free_stock.netsuite_send')
            //                                 ->where(function($q) use ($id_item_bb){
            //                                     $q->whereNotIn('free_stocklist.item_id', $id_item_bb);
            //                                     // $q->where('chiller.type','bahan-baku');
            //                                 })->get();

            // QUERY BB NON WO
            $queryBBWO               = (clone $clone_data)->whereNull('free_stock.netsuite_send')
                                            ->where(function($query) use ($id_item_prod, $id_item_bb) {
                                                $query
                                                ->whereNotIn('free_stocklist.item_id', $id_item_prod)
                                                ->whereNotIn('free_stocklist.item_id', $id_item_bb)
                                                ->orWhere('chiller.type', '=', 'bahan-baku')
                                                ;
                                            })
                                            ->get();


            // QUERY HASIL PRODUKSI KIRIM WO
            // $queryFGWO                  = (clone $clone_produksi)->whereNull('free_stock.netsuite_send')->get();


            // QUERY HASIL PRODUKSI WO
            $queryFGWO               = (clone $getDataFG)->whereNull('free_stock.netsuite_send')
                                                ->where(function($query) use ($id_item_prod, $id_item_bb) {
                                                    $query->whereNotIn('free_stocktemp.item_id', $id_item_prod)
                                                    ->whereNotIn('free_stocktemp.item_id', $id_item_bb);
                                            })
                                            ->get();


            $netsuite_null_bb       = (clone $getDataNONWO)->where('free_stock.netsuite_send', '0')->whereNull('free_stock.orderitem_id')
                                        ->get();

            $netsuite_null_fg       = (clone $getDataFG)->where('free_stock.netsuite_send', '0')->whereNull('free_stock.orderitem_id')
                                        ->get();


            foreach($queryBBWO as $dataBBWO){
                $collectionQueryBBWO->push((object)
                [
                    'item_id'   => $dataBBWO->item_id,
                    'berat'     => $dataBBWO->kg,
                    'qty'       => $dataBBWO->jumlah,
                    'type'      => $dataBBWO->type

                ]);
            }

            foreach($queryFGWO as $dataFGWO){
                $collectionQueryFGWO->push((object)
                [
                    'item_id'   => $dataFGWO->item_id,
                    'berat'     => $dataFGWO->kg,
                    'qty'       => $dataFGWO->jumlah,
                    'type'      => $dataFGWO->type

                ]);
            }

            
            foreach($netsuite_null_bb as $dataNONWOBB){
                $collectionNetsuiteNullBB->push((object)
                [
                    'item_id'   => $dataNONWOBB->item_id,
                    'berat'     => $dataNONWOBB->kg,
                    'qty'       => $dataNONWOBB->jumlah,
                    'type'      => $dataNONWOBB->type

                ]);
            }


            foreach($netsuite_null_fg as $dataNONWOFG){
                $collectionNetsuiteNullFG->push((object)
                [
                    'item_id'   => $dataNONWOFG->item_id,
                    'berat'     => $dataNONWOFG->kg,
                    'qty'       => $dataNONWOFG->jumlah,
                    'type'      => $dataNONWOFG->type

                ]);
            }


        // END NEW
        // dd($arrayBBWO, $arrayFGWO, $arrayNONWOBB, $arrayNONWOFG);

        // dd($collectionNetsuiteNullFG->sortByDesc('item_id'), $collectionNetsuiteNullBB->sortByDesc('item_id'), $collectionQueryFGWO->sortByDesc('item_id'), $collectionQueryBBWO->sortByDesc('item_id'));
        
        $collectionNetsuiteNullFG = $collectionNetsuiteNullFG->sortBy('item_id');
        $collectionNetsuiteNullBB = $collectionNetsuiteNullBB->sortBy('item_id');
        $collectionQueryFGWO = $collectionQueryFGWO->sortBy('item_id');
        $collectionQueryBBWO = $collectionQueryBBWO->sortBy('item_id');



        $tambahan_jumlah=0;
        $tambahan_kg=0;
        foreach ($item_sama_bb as $i => $prod) {
            $tambahan_jumlah += $prod->jumlah;
            $tambahan_kg += $prod->kg;
        }

        // dd($item_sama_bb);

        if ($request->key == 'unduh_bb') {
            return view('admin.pages.produksi.unduh.summary_bb_excel', compact('bahan_baku', 'tanggal')) ;
        } else
        if ($request->key == 'unduh_fg') {
            return view('admin.pages.produksi.unduh.summary_fg_excel', compact('produksi', 'tanggal'));
        } else
        if ($request->key == 'unduh_all') {
            return view('admin.pages.produksi.unduh.summary_all_excel', compact('bahan_baku', 'produksi', 'tanggal'));
        } else {
            if ($regu == 'boneless') {
                $bom   =   Bom::where('bom_name', env("NET_SUBSIDIARY", "CGL") . " - KARKAS - BONELESS BROILER")->first();
                $id_assembly    =  $bom->netsuite_internal_id;
            } else
            if ($regu == 'parting') {
                $bom   =   Bom::where('bom_name', env("NET_SUBSIDIARY", "CGL") . " - AYAM PARTING BROILER")->first();
                $id_assembly    =  $bom->netsuite_internal_id;
            } else
            if ($regu == 'marinasi') {
                $bom   =   Bom::where('bom_name', env("NET_SUBSIDIARY", "CGL") . " - AYAM PARTING MARINASI BROILER")->first();
                $id_assembly    =  $bom->netsuite_internal_id;
            } else
            if ($regu == 'whole') {
                $bom   =   Bom::where('bom_name', env("NET_SUBSIDIARY", "CGL") . " - AYAM KARKAS BROILER")->first();
                $id_assembly    =  $bom->netsuite_internal_id;
            } else
            if ($regu == 'frozen') {
                $bom   =   Bom::where('bom_name', env("NET_SUBSIDIARY", "CGL") . " - AYAM KARKAS FROZEN")->first();
                $id_assembly    =  $bom->netsuite_internal_id;
            }else{
                $bom   =   Bom::where('bom_name', env("NET_SUBSIDIARY", "CGL") . " - KARKAS - BONELESS BROILER")->first();
                $id_assembly    =  $bom->netsuite_internal_id;
            }

            return view('admin.pages.produksi.summary', compact('regu', 'tanggal', 'bahan_baku', 'produksi', 'bom','netsuite_null_bb','netsuite_null_pb', 'inputbyorder_bb', 'inputbyorder_pb','berat_bb','qty_bb','id_item_bb','id_item_prod','qty_prod','berat_prod','item_sama_bb','tambahan_jumlah','tambahan_kg', 'collectionQueryFGWO', 'collectionQueryBBWO', 'collectionNetsuiteNullBB', 'collectionNetsuiteNullFG'));
        }

    }

    public function regu(Request $request)
    {
        $tanggal        = $request->tanggal ?? date('Y-m-d');
        $tanggalakhir   = $request->tanggalend ?? date('Y-m-d');

            // $freestock  =   Freestock::whereDate('tanggal', $tanggal)
            $freestock  =   Freestock::whereBetween(DB::raw('DATE(tanggal)'), [$tanggal, $tanggalakhir])
                            ->whereIn('status', [2, 3])
                            ->where('regu', $request->regu)
                            ->orderBy('id', 'DESC')
                            ->get() ;

            $progress   =   Freestock::where('regu', 'byproduct')
                            ->where('status', 1)
                            // ->whereDate('tanggal', $tanggal)
                            ->whereBetween(DB::raw('DATE(tanggal)'), [$tanggal, $tanggalakhir])
                            ->count() ;

            $kategori   = $request->kat;

        return view('admin.pages.kepala_produksi.component.regu', compact('request', 'freestock','progress','tanggal','tanggalakhir','kategori'));
    }

    public function alokasi(Request $request)
    {
        if ($request->key == 'actualorder') {
            $dataDiff               = '';
            $dataActual             = [];
            $datas             = MarketingSO::where('subsidiary', Session::get('subsidiary'))->where('tanggal_kirim', $request->tanggal)->get();
            foreach ($datas as $data) {
                $dataProduksi       = Order::where('no_so', $data->no_so)->first();
                if (isset($dataProduksi->list_order)) {
                    if (COUNT($data->itemActual) > COUNT($dataProduksi->list_order)) {
                        $dataActual[]           = MarketingSO::where('no_so', $data->no_so)->first();
                        $dataListActual         = [];
                        $dataListProduksi       = [];
                        foreach ($data->itemActual as $key => $actual) {
                            $dataListActual[]   = $actual->line_id;
                        }
                        foreach ($dataProduksi->list_order as $key => $produksi) {
                            $dataListProduksi[] = $produksi->line_id;
                        }
                        $dataDiff = array_diff($dataListActual, $dataListProduksi);
                    }
                }
            }

            // dd($dataDiff);

            return view('admin.pages.regu.actual_order.index', compact('dataActual', 'dataDiff'));
        } else {
            $tanggal        =   $request->tanggal ?? date('Y-m-d');
            $regu           =   $request->regu ;
    
            $bahanbaku      =   Chiller::where('type', 'hasil-produksi')->where('tanggal_produksi', $tanggal)->where('regu', $regu)->get();
    
            return view('admin.pages.regu.component.alokasi', compact('tanggal', 'regu', 'bahanbaku'));
        }
    }

    public function summaryprod(Request $request)
    {
        if($request->key == 'logsummary'){
            // return response()->json($request->id);
            // $data = Log::where('table_id', $request->id)->get();
            $data = Adminedit::where('table_name','chiller')->where('table_id', $request->id)->get();
            return response()->json($data);
        } else
        if($request->key == 'view_data_code'){
            $id             = $request->id;
            $regu           = $request->regu;
            $namaitem       = $request->nama;
            $kategori       = $request->kategori;

            return view('admin.pages.regu.component.view.view-table',compact('id','regu','namaitem','kategori'));
        } 
        else {

        $regu                           =   strtolower($request->regu);
        $tanggal                        =   $request->tanggal ?? date('Y-m-d');
        $tanggalend                     =   $request->tanggalend ?? date('Y-m-d');
        $filterresult                   =   $request->filterresult;
        // $filterekspedisi                =   $request->filterekspedisi;
        // $filterchiller                  =   $request->filterchiller;
        // $filterabf                      =   $request->filterabf;
        $search                         =   $request->search ?? '';
        $cariSummaryProduksi            =   $request->cariSummaryProduksi ?? '';

        
        $regu_select    =   "";
        $kategori = [];
        if ($regu == 'boneless') {
            $kategori       =   [5, 11];
            $regu_select    =   "boneless";
        } elseif ($regu == 'parting') {
            $kategori       =   [2];
            $regu_select    =   "parting";
        } elseif ($regu == 'parting marinasi') {
            $kategori       =   [3,9];
            $regu_select    =   "marinasi";
        } elseif ($regu == 'whole chicken') {
            $kategori       =   [1];
            $regu_select    =   "whole";
        } elseif ($regu == 'frozen') {
            $kategori       =   [7, 8, 9, 13];
            $regu_select    =   "frozen";
        }
        

        $freestocks  =   DB::table('free_stocktemp as b')
                                ->leftJoin('free_stock as a','a.id','b.freestock_id')
                                ->leftJoin('items as c','b.item_id','c.id')
                                ->leftJoin('order_items as d','a.orderitem_id','d.id')
                                ->leftJoin('customers as e','b.customer_id','e.id')
                                ->whereBetween('a.tanggal', [$tanggal,$tanggalend])
                                ->whereIn('a.status', [2, 3])
                                // ->whereRaw('a.status IN ([2,3])')
                                ->where('a.regu', $regu_select)
                                ->orderBy('a.id', 'DESC')
                                ->where('b.deleted_at',null)
                                ->select(
                                    'a.id as idFreestock','a.tanggal','a.regu','a.orderitem_id','a.status','a.netsuite_send',
                                    'b.*',
                                    'c.id as idItem','c.nama',
                                    'd.id as idOrderItem','d.no_so',
                                    'e.id as idCustomer','e.nama as nama_konsumen')
                                ->where(function($query) use ($cariSummaryProduksi) {
                                    if ($cariSummaryProduksi !== '') {
                                        $query->where('c.nama', 'like', '%'. $cariSummaryProduksi. '%');
                                        $query->orWhere('b.plastik_nama', 'like', '%'. $cariSummaryProduksi. '%');
                                        $query->orWhere('d.no_so', 'like', '%'. $cariSummaryProduksi. '%');
                                        $query->orWhere('e.nama', 'like', '%'. $cariSummaryProduksi. '%');
                                    }
                                })
                                ->where(function($q) use ($filterresult) {
                                    if($filterresult == 'filterabf'){
                                        $q->where('b.kategori','=','1') ;
                                    } else if($filterresult == 'filterekspedisi'){
                                        $q->where('b.kategori','=','2') ;
                                    } else if ($filterresult == 'filterchiller') {
                                        $q->where('b.kategori','=','0') ;
                                        $q->orwhere('b.kategori','=','3') ;
                                        $q->orwhere('b.kategori','=','') ;
                                        $q->orwhere('b.kategori','=',null) ;
                                    }else if($filterresult == 'undefined' || $filterresult == 'semua'){
                                        $q->where('b.kategori','=','0') ;
                                        $q->orwhere('b.kategori','=','1') ;
                                        $q->orWhere('b.kategori','=','2') ;
                                        $q->orWhere('b.kategori','=','3') ;
                                        $q->orWhere('b.kategori','=','') ;
                                        $q->orwhere('b.kategori','=',null) ;
                                    }
                                });

        $master             = clone $freestocks;
        $all                = $master->get();

        $freestock          = $master->paginate(25);

        $totabf             = 0;
        $toteks             = 0;
        $totalchiller       = 0;
        $totprod            = 0;        
        
        foreach ($all as $item) {
            if($item->kategori == 1){
                $totabf         += $item->berat;
            }else 
            if ($item->kategori == 2) {
                $toteks         += $item->berat;
            }else 
            if ($item->kategori == 0) {
                $totalchiller   += $item->berat;
            }

            if ($item->kategori <= 3 || $item->kategori == null || $item->kategori == '') {
                $totprod        += $item->berat;
            }
        }
        $clone_order        = clone $freestocks;
        $byorder            = $clone_order->where('a.orderitem_id' ,'!=',null)->paginate(25);
        // $freestocks  =   Freestock::whereBetween(DB::raw('DATE(tanggal)'), [$tanggal,$tanggalend])
        //                         ->whereIn('status', [2, 3])
        //                         ->where('regu', $regu_select)
        //                         ->orderBy('id', 'DESC')
        //                         ->whereIn('id', FreestockTemp::select('freestock_id'))
        //                         ->where(function($query) use ($cariSummaryProduksi) {
        //                             if ($cariSummaryProduksi !== '') {
        //                                 $query->whereHas('freetemp.item', function($q) use ($cariSummaryProduksi) {
        //                                     $q->where('items.nama', 'like', '%'. $cariSummaryProduksi. '%');
        //                                 })->orWhereHas('freetemp', function($q) use ($cariSummaryProduksi) {
        //                                     $q->where('plastik_nama', 'like', '%'. $cariSummaryProduksi. '%');
        //                                 })->orWherehas('orderitem.itemorder', function ($q) use ($cariSummaryProduksi) {
        //                                     $q->where('no_so', 'like', '%'. $cariSummaryProduksi. '%');
        //                                 });
        //                             }
        //                         });
        //                         if($filterabf == 'true' && $filterchiller=='true' && $filterekspedisi=='true'){
        //                             $freestock = $freestocks->whereIn('id', FreestockTemp::select('freestock_id')
        //                                 ->where(function($query){
        //                                 $query->orWhere('kategori','=','0') ;
        //                                 $query->orWhere('kategori','=','1') ;
        //                                 $query->orWhere('kategori','=','2') ;
        //                                 $query->orWhere('kategori','=','3') ;
        //                             }));
        //                         }
        //                         if($filterabf == 'true' && $filterchiller=='true' && $filterekspedisi=='false'){
        //                             $freestock = $freestocks->whereIn('id', FreestockTemp::select('freestock_id')
        //                                 ->where(function($query){
        //                                 $query->orWhere('kategori','=','0') ;
        //                                 $query->orWhere('kategori','=','1') ;
        //                                 $query->orWhere('kategori','=','3') ;
        //                             }));
        //                         }
        //                         if($filterabf == 'true' && $filterchiller == 'false' && $filterekspedisi =='false'){
        //                             $freestock = $freestocks->whereIn('id', FreestockTemp::select('freestock_id')
        //                                 ->where(function($query){
        //                                 $query->where('kategori','=','1') ;
        //                             }));
        //                         }
        //                         if($filterabf == 'false' && $filterchiller == 'true' && $filterekspedisi =='true'){
        //                             $freestock = $freestocks->whereIn('id', FreestockTemp::select('freestock_id')
        //                                 ->where(function($query){
        //                                 $query->orWhere('kategori','=','0') ;
        //                                 $query->orWhere('kategori','=','2') ;
        //                                 $query->orWhere('kategori','=','3') ;
        //                             }));
        //                         }
        //                         if($filterabf == 'false' && $filterchiller == 'true' && $filterekspedisi =='false'){
        //                             $freestock = $freestocks->whereIn('id', FreestockTemp::select('freestock_id')
        //                                 ->where(function($query){
        //                                 $query->where('kategori','!=','1') ;
        //                                 $query->where('kategori','!=','2') ;
        //                             }));
        //                         }
        //                         if($filterabf == 'false' && $filterchiller == 'false' && $filterekspedisi =='true'){
        //                             $freestock = $freestocks->whereIn('id', FreestockTemp::select('freestock_id')
        //                                 ->where(function($query){
        //                                 $query->where('kategori','=','2') ;
        //                             }));
        //                         }
                                
                                // ->where(function($query) use ($filterabf, $filterchiller, $filterekspedisi) {
                                //     if($filterabf == 'true' && $filterchiller=='true' && $filterekspedisi=='true'){
                                //         $query->orWhere('kategori','=','0') ;
                                //         $query->orWhere('kategori','=','1') ;
                                //         $query->orWhere('kategori','=','2') ;
                                //         $query->orWhere('kategori','=','3') ;
                                //     } else if ($filterabf == 'true' && $filterchiller=='true' && $filterekspedisi=='false') {
                                //         $query->orWhere('kategori','=','0') ;
                                //         $query->orWhere('kategori','=','1') ;
                                //         $query->orWhere('kategori','=','3') ;
                                //     } else if ($filterabf == 'true' && $filterchiller == 'false' && $filterekspedisi =='false') {
                                //         $query->orWhere('kategori','=','1') ;
                                //     } else if ($filterabf == 'false' && $filterchiller == 'true' && $filterekspedisi =='true') {
                                //         $query->orWhere('kategori','=','0') ;
                                //         $query->orWhere('kategori','=','2') ;
                                //         $query->orWhere('kategori','=','3') ;
                                //     } else if ($filterabf == 'false' && $filterchiller == 'true' && $filterekspedisi =='false') {
                                //         $query->where('kategori','!=','1') ;
                                //         $query->where('kategori','!=','2') ;
                                //     } else if ($filterabf == 'false' && $filterchiller == 'false' && $filterekspedisi =='true') {
                                //         $query->where('kategori','=','2') ;
                                //     }
                                // });

        // $clone_order    = clone $freestocks;
        // $clonefreestock = clone $freestocks;
        // $freestock      = $clonefreestock->paginate(25);
        // $byorder        = $clone_order->where('orderitem_id' ,'!=',null)->paginate(25);
        // $totabf=0;
        // $toteks=0;
        // $totalchiller=0;
        // $totprod=0;
        // foreach ($freestock as $key => $value) {
        //     foreach ($value->freetemp as $xs => $item) {
        //         if($item->kategori == 1){
        //             $totabf += $item->berat;
        //         }else if ($item->kategori == 2) {
        //             $toteks += $item->berat;
        //         } else if ($item->kategori == 0) {
        //             $totalchiller += $item->berat;
        //         }
        //         if ($item->kategori < 3) {
        //             $totprod += $item->berat;
        //         }
        //     }
        // }
        if ($request->key == 'unduh') {
            // $clonefreestock2    =   clone $freestocks;
            // $freestock          =   $clonefreestock2->get();
            // $tanggal            =   $request->tanggal ?? date('Y-m-d');
            // $tanggalend         =   $request->tanggalend ?? date('Y-m-d');
            // $filterekspedisi    =   $request->filterekspedisi;
            // $filterchiller      =   $request->filterchiller;
            // $filterabf          =   $request->filterabf;
            // $filename           =  "summary-produksi-tanggal-". $tanggal . "-" . $tanggalend . ".xls";
            // return view('admin.pages.regu.component.download-summary-produksi',compact('freestock','regu','tanggal','tanggalend','filterabf','filterchiller','filterekspedisi','filename'));

            $freestockDownload  =   clone $all;

            // KUMPULKAN ARRAY DATA FREETEMP dan AKAN DIGUNAKAN DI LOOPING UTAMA DIBAWAH
            // $InID               = array();
            // $ambilIDchiller     = array();
            // $codeAbf            = array();
            // foreach($freestockDownload as $value){
            //     // $InID[]         = $value->id;
            //     $ambilIDchiller = FreestockTemp::getKodeChiller($value->id);
            // }

            // $arrayFST           = Freestock::ArrayFreeTemp('free_stocktemp',$InID);

            // MENGUMPULKKAN DATA YANG AKAN DI LOOPING DI VIEW BLADE
            $arrayData          = array();
            $qty                = 0;
            $berat              = 0;
            $totalekor          = 0;
            $totalquantity      = 0;
            $no                 = 1;
            foreach($freestockDownload as $fs){
                    
                $exp        = json_decode($fs->label);
                
                // if($fs->created_at != $fs->updated_at){
                //     $edit =  "( EDIT )";
                // }else{
                    $edit = "";
                // }

                if($exp->additional){
                    $tunggir    = $exp->additional->tunggir ? 'Tanpa Tunggir, ' : '';
                    $lemak      = $exp->additional->lemak ? 'Tanpa Lemak, ' : '' ;
                    $maras      = $exp->additional->maras ? 'Tanpa Maras' : '' ;
                    $additional = $tunggir."".$lemak."".$maras;
                }
                
                if($exp->sub_item){
                    $subitem    = $exp->sub_item;
                }else{
                    $subitem    = '';
                }

                if($exp->parting->qty){
                    $parting    = 'Parting '.$exp->parting->qty;
                }else{
                    $parting    = '';
                }

                if($exp->plastik->jenis){
                    $jenisplastik = $exp->plastik->jenis;
                }else{
                    $jenisplastik = '';
                }

                if($exp->plastik->qty){
                    $qtyplastik = $exp->plastik->qty;
                }else{
                    $qtyplastik = '';
                }

                // $ambilIDchiller = FreestockTemp::getKodeChiller($fs->id);
                // $codeAbf        = FreestockTemp::getKodeABF($ambilIDchiller);
                
                // if($ambilIDchiller != ''){
                //     foreach($codeAbf as $kode){
                //         $kodeABF= "<div class='text-center text-secondary small'>#ABF-".$kode->id."</div>";
                //     }
                // }else{
                //         $kodeABF= "<div class='text-center text-secondary small'></div>";
                // }

                $arrayData[] = array(
                    'no'            => $no,
                    'fs_id'         => $fs->freestock_id,
                    'nama_item'     => Freestock::getNameData('items',$fs->item_id,'nama'),
                    'diedit'        => $edit,
                    'kategori'      => $fs->kategori,
                    'created_at'    => $fs->created_at,
                    'updated_at'    => $fs->updated_at,
                    'konsumen'      => Freestock::getNameData('customers',$fs->customer_id,'nama'),
                    'tanggal'       => $fs->tanggal,
                    'additional'    => $additional,
                    'sub_item'      => $subitem,
                    'parting'       => $parting,
                    // 'kode_abf'      => $kodeABF,
                    'plastik_name'  => $fs->plastik_nama,
                    'plastik_qty'   => $fs->plastik_qty,
                    'plastik_jenis' => $jenisplastik,
                    'qty'           => number_format($fs->qty,2),
                    'berat'         => number_format($fs->berat,2),
                );
                $no++;
            }
            $filename           =  "summary-produksi-tanggal-". $tanggal . "-" . $tanggalend . ".xls";
            return view('admin.pages.regu.component.export-summary-produksi',compact('filename','arrayData','freestockDownload'));
        } else if ($request->key == 'viewExportTracing') {

            $requestItem        =   $request->item;
            $requestRegu        =   $request->regu;
            $requestCustomer    =   $request->customer;
            $tanggalAwal        =   $request->tanggal_awal;
            $tanggalAkhir       =   $request->tanggal_akhir;

            $dataChillerMaster  =   Chiller::whereBetween('tanggal_produksi', [$tanggalAwal, $tanggalAkhir])
                                    ->where('status', '2')
                                    ->where(function ($query) use ($requestItem) {
                                        if ($requestItem !== 'semuaItem') {
                                            $query->where('item_name', 'like', '%'.$requestItem.'%');
                                        }
                                    })
                                    ->where(function ($query) use ($requestCustomer) {
                                        if ($requestCustomer !== 'semuaCustomer') {
                                            $query->where('customer_id', $requestCustomer);
                                        }
                                    })
                                    ->where(function($q) use ($requestRegu) {
                                        if($requestRegu != 'all'){
                                            $q->where('regu', $requestRegu);
                                        }
                                    })
                                    ->orderBy('id','DESC');
            
            
            $dataABF            =   DB::table('chiller as a')
                                        ->LeftJoin('abf as b','a.id','b.table_id')
                                        ->where('b.table_name','chiller')
                                        ->whereBetween('a.tanggal_produksi', [$tanggalAwal, $tanggalAkhir])
                                        ->where('a.status', '2')
                                        ->where(function ($query) use ($requestItem) {
                                            if ($requestItem !== 'semuaItem') {
                                                $query->where('a.item_name', 'like', '%'.$requestItem.'%');
                                            }
                                        })
                                        ->where(function ($query) use ($requestCustomer) {
                                            if ($requestCustomer !== 'semuaCustomer') {
                                                $query->where('a.customer_id', $requestCustomer);
                                            }
                                        })
                                        ->where(function($q) use ($requestRegu) {
                                            if($requestRegu != 'all'){
                                                $q->where('a.regu', $requestRegu);
                                            }
                                        })
                                        ->select('a.*','b.table_id as table_id_abf','b.qty_awal as Qty_Awal','b.berat_awal as Berat_Awal','b.qty_item as Sisa_Qty', 'b.berat_item as Sisa_Berat')
                                        ->orderBy('a.item_name', 'DESC')
                                        ->get();
            $dataABFGudang      =   DB::table('abf as a')
                                        ->LeftJoin('product_gudang as b','a.id','b.table_id')
                                        ->where('b.table_name','abf')
                                        ->where('b.jenis_trans','masuk')
                                        ->whereBetween('a.tanggal_masuk', [$tanggalAwal, $tanggalAkhir])
                                        ->select('a.*','b.qty_awal as Qty_Awal_Gudang','b.berat_awal as Berat_Awal_Gudang','b.qty as Sisa_Qty_Gudang','b.berat as Sisa_Berat_Gudang')
                                        ->get();

            
            $customer           =   Customer::where('nama', '!=', '')
                                        ->where('netsuite_internal_id', '!=', NULL)
                                        ->where('netsuite_internal_id', '!=', 0)
                                        ->orderBy('nama')->get();

            $item               =   Item::where('category_id', '<=', 20)
                                        ->where('subsidiary', 'like', '%'.Session::get('subsidiary').'%')
                                        ->where('status', '1')
                                        ->get();
            
            if ($request->subKey == 'viewDataTracing') {
                $download = false;
                $clonedatachiller       = clone $dataChillerMaster;
                $dataChiller            = $clonedatachiller->paginate(25);

                $arrayYuk               = array();        
                $getChiller             = $clonedatachiller->select('id')->paginate(25);

                foreach($getChiller as $kodeChiller){
                    $arrayYuk[] = $kodeChiller->id;
                }

                $newData                = $arrayYuk;
                $stringData             = implode(",",$newData);
                if($stringData != ''){

                    $dataAlokasiOrder   =   DB::table('order_bahan_baku')
                                            ->where('proses_ambil', '!=', 'frozen')
                                            ->whereRaw("chiller_out  IN (".$stringData.")")
                                            ->where('deleted_at',null)
                                            ->get();

                    $dataOrderBahanBaku =   DB::table('free_stocklist')
                                            ->whereRaw("chiller_id  IN (".$stringData.")")
                                            ->where('deleted_at',null)
                                            ->get();
                }else{
                    $dataAlokasiOrder   = [];
                    $dataOrderBahanBaku = [];
                }
                return view('admin.pages.regu.component.dataTracingProduksi', compact('dataChiller','dataABF','dataABFGudang','dataAlokasiOrder','dataOrderBahanBaku','download'));
            } else if ($request->subKey == 'downloadDataTracing') {
                $clonedatachiller       = clone $dataChillerMaster;
                // $dataChiller            = $clonedatachiller->get();
                $dataChiller            = $clonedatachiller->take(1000)->get();

                $arrayYuk               = array();        
                // $getChiller             = $clonedatachiller->select('id')->get();
                $getChiller             = $clonedatachiller->select('id')->take(1000)->get();

                foreach($getChiller as $kodeChiller){
                    $arrayYuk[] = $kodeChiller->id;
                }

                $newData                = $arrayYuk;
                $stringData             = implode(",",$newData);
                if($stringData != ''){

                    $dataAlokasiOrder   =   DB::table('order_bahan_baku')
                                            ->where('proses_ambil', '!=', 'frozen')
                                            ->whereRaw("chiller_out  IN (".$stringData.")")
                                            ->where('deleted_at',null)
                                            ->get();

                    $dataOrderBahanBaku =   DB::table('free_stocklist')
                                            ->whereRaw("chiller_id  IN (".$stringData.")")
                                            ->where('deleted_at',null)
                                            ->get();
                }else{
                    $dataAlokasiOrder   = [];
                    $dataOrderBahanBaku = [];
                }

                $arrayData = array();
                $no           = 1;
                foreach($dataChiller as $data){
                    $qtyabfawal                 = 0;
                    $beratabfawal               = 0;
                    $qtyabfitem                 = 0;
                    $beratabfitem               = 0;
                    $qtyAwalGudang              = 0;
                    $beratAwalGudang            = 0;
                    $SisaQtyGudang              = 0;
                    $SisaBeratGudang            = 0;
                    $qtyBahanBaku               = 0;
                    $beratBahanBaku             = 0;
                    $qtyAlokasi                 = 0;
                    $beratAlokasi               = 0;
                    
                    foreach($dataABF as $abf){
                        if($abf->table_id_abf == $data->id){
                            $qtyabfawal         += $abf->Qty_Awal;
                            $beratabfawal       += $abf->Berat_Awal;
                            $qtyabfitem         += $abf->Sisa_Qty;
                            $beratabfitem       += $abf->Sisa_Berat;
                        }
                    }

                    foreach($dataABFGudang as $gudang){
                        if($gudang->table_id == $data->id){
                            $qtyAwalGudang          += $gudang->Qty_Awal_Gudang;
                            $beratAwalGudang        += $gudang->Berat_Awal_Gudang;
                            $SisaQtyGudang          += $gudang->Sisa_Qty_Gudang;
                            $SisaBeratGudang        += $gudang->Sisa_Berat_Gudang;
                        }
                    }

                    foreach ($dataOrderBahanBaku as $orderbb){
                        if($orderbb->chiller_id == $data->id){
                            $qtyBahanBaku               += $orderbb->qty;
                            $beratBahanBaku             += $orderbb->berat;
                        }
                    }
                        
                    foreach ($dataAlokasiOrder as $alokasi){
                        if($alokasi->chiller_out == $data->id){
                            $qtyAlokasi               += $alokasi->bb_item;
                            $beratAlokasi             += $alokasi->bb_berat;
                        }
                    }
                        
                    $exp    = json_decode($data->label);
                    $arrayData[] = array(
                        'type'                      => $data->type,
                        'id'                        => $data->id,
                        'regu'                      => $data->regu,
                        'no'                        => $no,
                        'regu'                      => $data->regu,
                        'nama_item'                 => $data->item_name,
                        'konsumen'                  => $data->konsumen->nama ?? '#',
                        'sub_item'                  => $exp->sub_item ?? '#',                
                        'tanggal'                   => $data->tanggal_produksi,
                        'asal_tujuan'               => $data->asal_tujuan,
                        'qty_item'                  => number_format($data->qty_item,2),
                        'berat_item'                => number_format($data->berat_item, 2),
                        'stock_item'                => number_format($data->stock_item,2),
                        'stock_berat'               => number_format($data->stock_berat,2),
                        'qty_abf_awal'              => number_format($qtyabfawal, 2),
                        'berat_abf_awal'            => number_format($beratabfawal,2),
                        'qty_abf_item'              => number_format($qtyabfitem, 2),
                        'berat_abf_item'            => number_format($beratabfitem, 2),
                        'gudang_qty_awal'           => number_format($qtyAwalGudang,2),
                        'gudang_berat_awal'         => number_format($beratAwalGudang,2),
                        'gudang_qty_akhir'          => number_format($SisaQtyGudang,2),
                        'gudang_berat_akhir'        => number_format($SisaBeratGudang,2),
                        'qtyBahanBaku'              => number_format($qtyBahanBaku, 2),
                        'beratBahanBaku'            => number_format($beratBahanBaku, 2),
                        'qtyAlokasi'                => number_format($qtyAlokasi, 2),
                        'beratAlokasi'              => number_format($beratAlokasi, 2),
                    );
                    $no++;
                }


                return view('admin.pages.regu.component.view.download-export-tracing-chiller', compact('arrayData'));
            }
            return view('admin.pages.regu.component.tracingProduksi', compact('customer', 'item'));
            // $customer           =   Customer::where('nama', '!=', '')
            //                         ->where('netsuite_internal_id', '!=', NULL)
            //                         ->where('netsuite_internal_id', '!=', 0)
            //                         ->orderBy('nama')->get();

            // $item               =   Item::where('category_id', '<=', 20)
            //                         ->where('subsidiary', 'like', '%'.Session::get('subsidiary').'%')
            //                         ->where('status', '1')
            //                         ->get();
            
            // $dataChiller        =   Chiller::whereBetween('tanggal_produksi', [$tanggalAwal, $tanggalAkhir])
            //                         ->where('status', '2')
            //                         ->where(function ($query) use ($requestItem, $requestCustomer) {
            //                             if ($requestItem !== 'semuaItem') {
            //                                 $query->where('item_name', 'like', '%'.$requestItem.'%');
            //                             }

            //                             if ($requestCustomer !== 'semuaCustomer') {
            //                                 $query->where('customer_id', $requestCustomer);
            //                             }
            //                         })
            //                         ->orderBy('item_name', 'DESC')
            //                         ->get();

            // if ($request->subKey == 'viewDataTracing') {
            //     $download = false;
            //     return view('admin.pages.regu.component.dataTracingProduksi', compact('dataChiller', 'download'));
            // } else if ($request->subKey == 'downloadDataTracing') {
            //     $download = true;
            //     return view('admin.pages.regu.component.dataTracingProduksi', compact('dataChiller', 'download'));
            // }
            // return view('admin.pages.regu.component.tracingProduksi', compact('customer', 'item'));
        } 
        // return view('admin.pages.regu.component.summaryprod', compact('freestock','regu','tanggal','tanggalend','filterabf','filterchiller','filterekspedisi','totabf','toteks','totprod','byorder', 'totalchiller'));
        return view('admin.pages.regu.component.summaryproduksi', compact('freestock','regu','tanggal','tanggalend','filterresult','totabf','toteks','totprod','byorder', 'totalchiller'));
        }
    }
    public function paginate($items, $perPage = 25, $page = null, $options = [])
    {
        $options = [
            'path'      => LengthAwarePaginator::resolveCurrentPath(),
            'pageName'  => 'page'
        ];
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
    public function siap_kirim_export(Request $request){
        $tanggal    =   $request->tanggal ?? date('Y-m-d');
        $search    =   $request->search ?? "";
        return view('admin.pages.penyiapan.export');
    }


}
