<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chiller;
use App\Models\VWMutasiChiller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MutasiStockController extends Controller
{
    //
    public function chiller_index(Request $request){
        
        if($request->type == "data"){
            
            $mulai      =   $request->mulai ?? date('Y-m-d');
            $sampai     =   $request->sampai ?? date('Y-m-d');
            $kosong     =   $request->kosong;

            $stock      =   VWMutasiChiller::select(DB::raw('nama, sum(saldo_qty) as saldo_qty, sum(saldo_berat) as saldo_berat'))
                            ->groupBy('nama')
                            ->whereBetween('tanggal_produksi', [$mulai, $sampai])->get();
                        
            return view('admin.pages.mutasistock.chiller-data', compact('stock'));
        }else{
            return view('admin.pages.mutasistock.chiller-index');
        }
    }
}
