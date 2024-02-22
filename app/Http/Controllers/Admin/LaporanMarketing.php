<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\MarketingSO;
use App\Models\MarketingSOList;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanMarketing extends Controller
{
    public function index(Request $request)
    {
        if ($request->key == 'view') {
            $data       =   MarketingSO::select('marketing_so.*', DB::raw("COUNT(marketing_so.id) AS jumlah"), DB::raw("SUM(marketing_so_list.harga) AS nominal"))
                            ->leftJoin('marketing_so_list', 'marketing_so_list.marketing_so_id', '=', 'marketing_so.id')
                            ->leftJoin('customers', 'customers.id', '=', 'marketing_so.customer_id')
                            ->whereBetween('marketing_so.tanggal_so', [($request->tanggal_awal ?? date("Y-m-01")), ($request->tanggal_akhir ?? date("Y-m-d"))])
                            ->where(function($query) use ($request) {
                                if ($request->nama_marketing) {
                                    if ($request->nama_marketing != '') {
                                        $query->where('marketing_so.user_id', $request->nama_marketing);
                                    }
                                }
                            })
                            ->where(function($query) use ($request) {
                                if ($request->market) {
                                    if ($request->market != '') {
                                        $query->where('customers.kategori', $request->market);
                                    }
                                }
                            })
                            ->where(function($query) use ($request) {
                                if ($request->cari) {
                                    $query->orWhere('marketing_so.no_so', 'like', "%" . $request->cari . "%") ;
                                    $query->orWhere('marketing_so.tanggal_so', 'like', "%" . $request->cari . "%") ;
                                    $query->orWhere('marketing_so.tanggal_so', 'like', "%" . $request->cari . "%") ;
                                    $query->orWhere('customers.nama', 'like', "%" . $request->cari . "%") ;
                                    $query->orWhere('customers.kategori', 'like', "%" . $request->cari . "%") ;
                                }
                            })
                            ->groupBy('marketing_so.id')
                            ->orderByDesc('marketing_so.tanggal_so')
                            ->paginate(20) ;

            return view('admin.pages.laporan.marketing.data', compact('data'));
        } else {
            $marketing  =   MarketingSO::select('user_id')
                            ->where('user_id', '!=', NULL)
                            ->groupBy('user_id')
                            ->get() ;

            $market     =   Customer::select('kategori')
                            ->where('kategori', '!=', NULL)
                            ->whereIn('id', MarketingSO::select('customer_id'))
                            ->groupBy('kategori')
                            ->get() ;

            return view('admin.pages.laporan.marketing.index', compact('marketing', 'market'));
        }
    }

    function date_range($first, $last, $step = '+7 day', $output_format = 'Y-m-d' ) {

        $dates = array();
        $current = strtotime($first);
        $last = strtotime($last);

        while( $current <= $last ) {

            $dates[] = date($output_format, $current);
            $current = strtotime($step, $current);
        }

        return $dates;
    }
}
