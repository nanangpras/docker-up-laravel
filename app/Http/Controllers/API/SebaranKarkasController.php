<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Grading;
use App\Models\Item;
use App\Models\LaporanSebarankarkas;
use App\Models\Production;
use App\Models\Purchasing;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SebaranKarkasController extends Controller
{
    public function store_sebaran()
    {
        DB::beginTransaction() ;

        $karkas =   Item::where('category_id', 1)->get();
        $subsidiary_id  =   env('NET_SUBSIDIARY_ID', '2');
        $subsidiary     =   env('NET_SUBSIDIARY', 'CGL');

        foreach ($karkas as $item) {
            if (Grading::sebaran_karkas($item->id, date("Y-m-d"), date("Y-m-d"))) {
                $sebaran                    =   LaporanSebarankarkas::where('item_id', $item->id)
                                                ->where('tanggal', date('Y-m-d'))
                                                ->where('subsidiary_id', $subsidiary_id)
                                                ->where('subsidiary', $subsidiary)
                                                ->first() ?? new LaporanSebarankarkas ;

                $sebaran->tanggal           =   date("Y-m-d") ;
                $sebaran->subsidiary_id     =   $subsidiary_id ;
                $sebaran->subsidiary        =   $subsidiary ;
                $sebaran->item_id           =   $item->id ;
                $sebaran->sku               =   $item->sku ;
                $sebaran->nama              =   $item->nama ;
                $sebaran->qty               =   Grading::sebaran_karkas($item->id, date("Y-m-d"), date("Y-m-d")) ;
                $sebaran->berat             =   Grading::sebaran_karkas($item->id, date("Y-m-d"), date("Y-m-d"), 'berat_item') ;
                if (!$sebaran->save()) {
                    DB::rollback() ;
                    $result['status']   =   400 ;
                    $result['msg']      =   'Proses Gagal' ;
                    return $result ;
                }
            }
        }

        DB::commit();
        $result['status']   =   200;
        $result['msg']      =   'Sukses';
        return $result;

    }
}
