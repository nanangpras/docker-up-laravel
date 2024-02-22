<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bom;
use App\Models\Customer;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Spesifikasi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{

    public function index(Request $request)
    {
        // if (User::setIjin(20)) {
            if ($request->key == 'view') {
                $primer   =   Customer::select('customers.nama', 'customers.kode', 'customers.id', 'customers.nama_marketing AS nama_marketing', 'customers.deleted_at', DB::raw("MAX(orders.tanggal_so) AS tanggal_so"), DB::raw("COUNT(orders.id) AS total_order"), DB::raw("SUM(IF(orders.status=10,1,0)) AS alokasi"), DB::raw("SUM(IF(orders.status=10,0,1)) AS pending"))
                            ->leftJoin('marketing', 'marketing.id', '=', 'customers.marketing_id')
                            ->leftJoin('orders', 'orders.customer_id', '=', 'customers.id')
                            ->where(function($query) use ($request) {
                                if ($request->cari) {
                                    $query->orWhere('customers.kode', 'like', '%' . $request->cari . '%') ;
                                    $query->orWhere('customers.nama', 'like', '%' . $request->cari . '%') ;
                                    $query->orWhere('customers.alamat', 'like', '%' . $request->cari . '%') ;
                                    $query->orWhere('customers.nama_marketing', 'like', '%' . $request->cari . '%') ;
                                }

                                if ($request->advance == 'last_order') {
                                    $query->orWhere('tanggal_so', '>', 0);
                                }

                                if ($request->advance == 'no_order') {
                                    $query->orWhere('tanggal_so', NULL);
                                }
                            })
                            ->where(function($query) use ($request) {
                                if ($request->parent) {
                                    if ($request->parent != 'none' && $request->parent != 'all') {
                                        $query->where('parent_nama', $request->parent);
                                    }
                                }
                            })
                            ->where(function($query) use ($request) {
                                if ($request->status == 'aktif') {
                                    $query->where('customers.deleted_at', NULL);
                                } else if ($request->status == 'tidakaktif') {
                                    $query->where('customers.deleted_at', '!=', NULL);
                                }
                            })
                            ->where('customers.nama', '!=', '')
                            ->withTrashed()
                            ->groupBy('customers.id')
                            ->orderBy(($request->advance == 'last_order') ? 'tanggal_so' : ($request->advance == 'max_order' ? 'total_order' : 'nama'), $request->advance == 'max_order' ? 'DESC' : 'ASC');

                            // ->paginate(20);
                $clone = clone $primer;
                // $clone = clone $primer;
                $data = $clone->paginate(20);

                if ($request->part == 'downloadCustomer') {
                    $clone = clone $primer;
                    $result = $clone->get();
                    return view('admin.pages.laporan.customer.download.customer',compact('result'));
                }
                return view('admin.pages.laporan.customer.data', compact('data'));
            } else

            if ($request->key == 'parent') {
                $data   =   Customer::select('parent_nama')
                            ->groupBy('parent_nama')
                            ->where('parent_nama', '!=', '')
                            ->pluck('parent_nama');

                return view('admin.pages.laporan.customer.parent_select', compact('data'));
            } else

            if ($request->key == 'chartso') {
                $tanggal_awal   =   $request->tanggal_awal ?? date("Y-m-d") ;
                $tanggal_akhir  =   $request->tanggal_akhir ?? date("Y-m-d") ;

                $data   =   OrderItem::select(DB::raw("SUM(IF(order_items.berat, order_items.berat, 0)) AS berat"), DB::raw("SUM(IF(order_items.fulfillment_berat, order_items.fulfillment_berat,0)) AS fulfill"), 'orders.tanggal_kirim AS tanggal')
                            ->leftJoin('orders', 'orders.id', '=', 'order_items.order_id')
                            ->where(function($query) use ($tanggal_awal, $tanggal_akhir) {
                                if ($tanggal_awal == $tanggal_akhir) {
                                    $query->whereBetween('orders.tanggal_kirim', [date('Y-m-d', strtotime("-7 Day", strtotime($tanggal_awal))), $tanggal_awal]) ;
                                } else {
                                    $query->whereBetween('orders.tanggal_kirim', [$tanggal_awal, $tanggal_akhir]) ;
                                }
                            })
                            ->where(function($query) use ($request) {
                                if ($request->konsumen) {
                                    if ($request->konsumen != 'all') {
                                        $query->where('nama', $request->konsumen) ;
                                    }
                                }
                            })
                            ->groupBy('tanggal')
                            ->get();

                $tanggal    =   "[" ;
                $berat      =   "[" ;
                $order      =   "[" ;

                foreach ($data as $row) {
                    $tanggal    .=  "'" . $row->tanggal . "', ";
                    $berat      .=  $row->berat . ", ";
                    $order      .=  $row->fulfill . ", ";
                }

                $tanggal    .=  "]" ;
                $berat      .=  "]" ;
                $order      .=  "]" ;

                return view('admin.pages.laporan.customer.chart.index', compact('tanggal', 'berat', 'order')) ;
            } else

            if ($request->key == 'show_so') {
                $tanggal_awal   =   $request->tanggal_awal ?? date("Y-m-d");
                $tanggal_akhir  =   $request->tanggal_akhir ?? date("Y-m-d");

                $list_order =   Order::select(DB::raw("SUM(IF(order_items.berat, order_items.berat, 0)) AS berat"), DB::raw("SUM(IF(order_items.fulfillment_berat, order_items.fulfillment_berat,0)) AS fulfill"), 'orders.nama AS nama', 'orders.tanggal_so AS tanggal_so', 'orders.tanggal_kirim AS tanggal_kirim', 'orders.no_so', 'orders.id')
                                ->leftJoin('order_items', 'order_items.order_id', '=', 'orders.id')
                                ->where(function($query) use ($tanggal_awal, $tanggal_akhir) {
                                    if ($tanggal_awal == $tanggal_akhir) {
                                        $query->whereBetween('orders.tanggal_kirim', [date('Y-m-d', strtotime("-7 Day", strtotime($tanggal_awal))), $tanggal_awal]) ;
                                    } else {
                                        $query->whereBetween('orders.tanggal_kirim', [$tanggal_awal, $tanggal_akhir]) ;
                                    }
                                })
                                ->where(function($query) use ($request) {
                                    if ($request->konsumen) {
                                        if ($request->konsumen != 'all') {
                                            $query->where('nama', $request->konsumen) ;
                                        }
                                    }
                                })
                                ->orderByRaw('orders.tanggal_kirim ASC, nama ASC')
                                ->groupBy('orders.id');

                $berat      =   0 ;
                $fulfill    =   0 ;
                foreach ($list_order->get() as $row) {
                    $berat  +=  $row->berat ;
                    $fulfill+=  $row->fulfill ;
                }

                $list_order =   $list_order->paginate(15);

                return view('admin.pages.laporan.customer.chart.show_so', compact('list_order', 'berat', 'fulfill')) ;
            } else

            if ($request->key == 'data_customer') {
                $tanggal_awal   =   $request->tanggal_awal ?? date("Y-m-d");
                $tanggal_akhir  =   $request->tanggal_akhir ?? date("Y-m-d");

                $list_order =   Order::select('nama')
                                ->where(function($query) use ($tanggal_awal, $tanggal_akhir) {
                                    if ($tanggal_awal == $tanggal_akhir) {
                                        $query->whereBetween('orders.tanggal_kirim', [date('Y-m-d', strtotime("-7 Day", strtotime($tanggal_awal))), $tanggal_awal]) ;
                                    } else {
                                        $query->whereBetween('orders.tanggal_kirim', [$tanggal_awal, $tanggal_akhir]) ;
                                    }
                                })
                                ->groupBy('nama')
                                ->orderBy('nama', 'ASC')
                                ->get();

                return view('admin.pages.laporan.customer.chart.data_customer', compact('list_order', 'request')) ;

            } else if ($request->key == 'updateDataCustomer') {
                $data   = Customer::where('id', $request->id)->withTrashed()->first();
                if ($data) {
                    if ($request->statusCustomer == 'tidakaktif') {
                        $data->delete();
                        return response()->json([
                            'data'      => $data, 
                            'status'    => 200
                        ]);
                    } else {
                        $data->deleted_at = NULL;
                        $data->nama       = $request->nama;
                        $data->kode       = $request->kode;
                        $data->save();
                        return response()->json([
                            'data'      => $data, 
                            'status'    => 200
                        ]);
                    }

                } else {
                    return response()->json([
                        'status'    => 404
                    ]);
                }
                
            } else if ($request->key == 'loadDataCustomer') {
                $data   = Customer::where('id', $request->id)->withTrashed()->first();
                if ($data) {
                    return response()->json([
                        'data'      => $data, 
                        'status'    => 200
                    ]);
                } else {
                    return response()->json([
                        'status'    => 404
                    ]);
                }
            } else {
                return view('admin.pages.laporan.customer.index');
            }
        // }
        // return redirect()->route("index");
    }


    public function show(Request $request, $id)
    {
        if (User::setIjin(20)) {
            $data   =   Customer::select('customers.id', 'customers.parent_nama', 'customers.kode', 'customers.nama', 'customers.parent_id', 'customers.alamat', 'customers.marketing_id', DB::raw("SUM(IF(orders.status=10,1,0)) AS alokasi"), DB::raw("SUM(IF(orders.status=10,0,1)) AS pending"))
                        ->leftJoin('orders', 'orders.customer_id', '=', 'customers.id')
                        ->where('customers.id', $id)
                        ->first() ;

            if ($data) {

                $parent = Customer::where('parent_id', NULL)->get();

                if ($request->key == 'summary') {
                    return view('admin.pages.laporan.customer.detail.summary', compact('data', 'id', 'parent'));
                } else

                if ($request->key == 'editParent') {
                    $parent = $request->id;
                    $findParent = Customer::find($id);
                    if ($findParent) {
                        $findParent->parent_id = $parent;
                        $findParent->save();
                        return response()->json([
                            'data'      => $findParent,
                            'message'   => 'success'
                        ]);
                    } else {
                        return response()->json([
                            'message'   => 'error'
                        ]);
                    }
                } else 
                if ($request->key == 'detail_view') {
                    $order  =   Order::where('customer_id', $data->id)
                                ->orderBy('tanggal_so', 'DESC')
                                ->where(function($query) use ($request) {
                                    if ($request->view == 'alokasi') {
                                        $query->where('status', 10);
                                    }
                                    if ($request->view == 'pending') {
                                        $query->where('status', NULL);
                                    }
                                })
                                ->paginate(15);

                    return view('admin.pages.laporan.customer.detail.view', compact('data', 'order', 'request'));
                } else

                {
                    return view('admin.pages.laporan.customer.detail.index', compact('id'));
                }

            }
            return redirect()->route('customer.index');
        }
        return redirect()->route("index");
    }
}
