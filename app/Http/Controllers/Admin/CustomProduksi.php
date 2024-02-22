<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chiller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class CustomProduksi extends Controller
{
    //
    
    public function index(Request $request){
        return view('admin/pages/customproduksi.index');
    }

    public function produksiDetail(Request $request){

        $client     = $request->client;
      
        return view('admin/pages/customproduksi.detail', compact(['client']));
    }

    public function summary(Request $request){

        $client     = $request->client;
        $tanggal    = $request->tanggal ?? date('Y-m-d');
        $order    = [];
        if($client=='meyerfood'){
            $order    =   Order::where('nama', 'MEYER PROTEINDO PRAKARSA. PT            ')
                                ->where('tanggal_so', $tanggal)
                                ->paginate(15);
        }


        $produk =   Chiller::whereIn('asal_tujuan', ['free_stock', 'evisampingan'])->where('jenis', 'masuk')->whereIn('type', ['hasil-produksi', 'bahan-baku'])->get();


        return view('admin/pages/customproduksi.summary', compact(['order', 'client', 'produk']));
    }
}
