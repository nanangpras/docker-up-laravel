<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gudang;
use App\Models\Item;
use App\Models\Netsuite;
use Illuminate\Http\Request;

class GenerateTI extends Controller
{
    public function index(Request $request)
    {
        $item   =   Item::get();
        $gudang =   Gudang::where('subsidiary', env('NET_SUBSIDIARY', 'CGL'))->get();
        return view('admin.pages.generate_ti.index', compact('item', 'gudang')) ;
    }

    public function store(Request $request)
    {
        $dari           =   Gudang::find($request->gudang_from) ;
        $id_location    =   $dari->netsuite_internal_id ;
        $location       =   $dari->code ;
        $tanggal        =   $request->tanggal ;

        $from           =   $id_location ;
        $to             =   Gudang::find($request->gudang_to)->netsuite_internal_id ;

        $data   =   [] ;
        for ($x=0; $x < COUNT($request->item_from); $x++) {
            if ($request->qty_from[$x]) {
                $item   =   Item::find($request->item_from[$x]) ;
                $data[] = [
                    "internal_id_item"      => (string)$item->netsuite_internal_id,
                    "item"                  => (string)$item->sku,
                    "qty_to_transfer"       => $request->qty_from[$x]
                ];
            }
        }


        Netsuite::transfer_inventory_date('generate_ti', '', 'ti_generate', $id_location, $location, $from, $to, $data, NULL, $tanggal) ;
        return back()->with('status', 1)->with('message', 'Generate transfer inventory berhasil') ;
    }
}
