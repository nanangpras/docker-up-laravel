<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bom;
use App\Models\Gudang;
use App\Models\Item;
use App\Models\Netsuite;
use Illuminate\Http\Request;

class GenerateWO extends Controller
{
    public function index(Request $request)
    {
        $item   =   Item::get();
        $gudang =   Gudang::where('subsidiary', env('NET_SUBSIDIARY', 'CGL'))->get();
        $bom    =   Bom::get();
        return view('admin.pages.generate_wo.index', compact('item', 'gudang', 'bom')) ;
    }

    public function store(Request $request)
    {
        // Component
        $component  =   [] ;
        for ($x=0; $x < COUNT($request->item_component); $x++) {
            if ($request->qty_component[$x]) {
                $item   =   Item::find($request->item_component[$x]) ;
                $component[]  =   [
                    "type"              =>  "Component",
                    "internal_id_item"  =>  (string)$item->netsuite_internal_id,
                    "item"              =>  $item->sku,
                    "description"       =>  $item->nama,
                    "qty"               =>  $request->qty_component[$x],
                ];
            }
        }

        $qty_berat  =   0 ;
        // Finished Goods
        $fg     =   [] ;
        for ($x=0; $x < COUNT($request->item_fg); $x++) {
            if ($request->qty_fg[$x]) {
                $qty_berat  +=  $request->qty_fg[$x] ;
                $item       =   Item::find($request->item_fg[$x]) ;
                $fg[]       =   [
                    "type"              =>  "Finished Goods",
                    "internal_id_item"  =>  (string)$item->netsuite_internal_id,
                    "item"              =>  $item->sku,
                    "description"       =>  $item->nama,
                    "qty"               =>  $request->qty_fg[$x],
                ];
            }
        }

        // By Product
        $product    =   [] ;
        for ($x=0; $x < COUNT($request->item_product); $x++) {
            if ($request->qty_product[$x]) {
                $qty_berat  +=  $request->qty_product[$x] ;
                $item       =   Item::find($request->item_product[$x]) ;
                $product[]  =   [
                    "type"              =>  "By Product",
                    "internal_id_item"  =>  (string)$item->netsuite_internal_id,
                    "item"              =>  $item->sku,
                    "description"       =>  $item->nama,
                    "qty"               =>  $request->qty_product[$x],
                ];
            }
        }

        $produksi       =   array_merge($component, $fg, $product) ;

        $bom            =   Bom::find($request->assembly) ;
        $id_assembly    =   $bom->netsuite_internal_id ;
        $nama_assembly  =   $bom->bom_name;

        $lokasi         =   Gudang::find($request->lokasi);
        $id_location    =   $lokasi->netsuite_internal_id;
        $location       =   $lokasi->code;


        $label  =   $request->wo ;
        $wo = Netsuite::work_order_date(null, null, $label, $id_assembly, $nama_assembly, $id_location, $location, $produksi, null, $request->tanggal);

        // ===================    WO - 2 - BUILD    ===================

        $label  =   $request->wo . "-build" ;
        Netsuite::wo_build_date(null, null, $label, $id_assembly, $nama_assembly, $id_location, $location, $qty_berat, $produksi, $wo->id, $request->tanggal);

        return back()->with('status', 1)->with('message', 'Generate WO - WOB berhasil');
    }
}
