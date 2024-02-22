<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bom;
use App\Models\Chiller;
use App\Models\Gudang;
use App\Models\Item;
use App\Models\Netsuite;
use App\Models\Product_gudang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RepackController extends Controller
{
    public function index(Request $request)
    {
        if ($request->key == 'show') {
            $tanggal    =   $request->tanggal ?? date('Y-m-d');
            $id         =   $request->id ?? '';

            $gudang     =   Product_gudang::where('gudang_id', $request->id)
                            ->where('created_at', 'like', '%' . $tanggal . '%')
                            ->where('status', 2)
                            ->get();

            return view('admin.pages.repack.show', compact('gudang', 'id'));
        } else {
            $cold   =   Gudang::where('kategori', 'warehouse')
                        ->where('subsidiary', env('NET_SUBSIDIARY', 'CGL'))
                        ->where('status', 1)
                        ->get() ;

            return view('admin.pages.repack.index', compact('cold'));
        }

    }

    public function store(Request $request)
    {
        $produk =   Product_gudang::find($request->id) ;

        DB::beginTransaction();

        if ($produk->productitems->category_id == 8) {
            $item_assembly  =   env('NET_SUBSIDIARY', 'CGL').' - REPACK AYAM PARTING BROILER FROZEN';
            $id_assembly    =   Bom::where('bom_name', $item_assembly)->first()->netsuite_internal_id;
        } else
            if ($produk->productitems->category_id == 9) {
            $item_assembly  =   env('NET_SUBSIDIARY', 'CGL').' - REPACK AYAM PARTING MARINASI BROILER FROZEN';
            $id_assembly    =   Bom::where('bom_name', $item_assembly)->first()->netsuite_internal_id;
        } else {
            $item_assembly  =   env('NET_SUBSIDIARY', 'CGL').' - REPACK AYAM BROILER FROZEN';
            $id_assembly    =   Bom::where('bom_name', $item_assembly)->first()->netsuite_internal_id;
        }

        if (($request->qty == $produk->qty) || ($request->berat == $produk->berat)) {
            $produk->packaging      =   Item::find($request->plastik)->nama ;
            if (!$produk->save()) {
                DB::rollBack() ;
                $result['status']   =   400 ;
                $result['msg']      =   "Proses Gagal" ;
                return $result ;
            }

            $component      =   [];
            foreach (Bom::where('bom_name', $item_assembly)->first()->bomproses as $row) {
                $component[] =   [
                    "type"              =>  "Component",
                    "internal_id_item"  =>  (string)Item::item_sku($row->sku)->netsuite_internal_id,
                    "item"              =>  $row->sku,
                    "description"       =>  (string)Item::item_sku($row->sku)->nama,
                    "qty"               =>  (string)Item::where('nama', 'AY - S')->first()->sku == $row->sku ? $row->qty_per_assembly : ($row->qty_per_assembly * $produk->berat),
                ];
            }

            $component[] = [
                "type"              =>  "Component",
                "internal_id_item"  =>  (string)Item::find($request->plastik)->netsuite_internal_id,
                "item"              =>  (string)Item::find($request->plastik)->sku,
                "description"       =>  (string)Item::find($request->plastik)->nama,
                "qty"               =>  (string)$produk->berat,
            ];

            $component[] = [
                "type"              =>  "Finished Goods",
                "internal_id_item"  =>  (string)Item::find($produk->product_id)->netsuite_internal_id,
                "item"              =>  (string)Item::find($produk->product_id)->sku,
                "description"       =>  (string)Item::find($produk->product_id)->nama,
                "qty"               =>  (string)$produk->berat,
            ];

        } else {
            $produk->qty        =   ($request->qty == $produk->qty) ? $produk->qty : ($produk->qty - $request->qty) ;
            $produk->berat      =   ($request->berat == $produk->berat) ? $produk->berat : ($produk->berat - $request->berat);
            if (!$produk->save()) {
                DB::rollBack() ;
                $result['status']   =   400;
                $result['msg']      =   "Proses Gagal";
                return $result;
            }

            $newproduct                     =   new Product_gudang ;
            $newproduct->qty_awal           =   $request->qty ;
            $newproduct->berat_awal         =   $request->berat ;
            $newproduct->qty                =   $request->qty ;
            $newproduct->berat              =   $request->berat ;
            $newproduct->packaging          =   Item::find($request->plastik)->nama ;
            $newproduct->product_id         =   $produk->id ;
            $newproduct->table_name         =   $produk->table_name ;
            $newproduct->table_id           =   $produk->table_id ;
            $newproduct->no_so              =   $produk->no_so ;
            $newproduct->order_id           =   $produk->order_id ;
            $newproduct->order_item_id      =   $produk->order_item_id ;
            $newproduct->palete             =   $produk->palete ;
            $newproduct->potong             =   $produk->potong ;
            $newproduct->expired            =   $produk->expired ;
            $newproduct->production_date    =   $produk->production_date ;
            $newproduct->type               =   $produk->type ;
            $newproduct->stock_type         =   $produk->stock_type ;
            $newproduct->jenis_trans        =   $produk->jenis_trans ;
            $newproduct->gudang_id          =   $produk->gudang_id ;
            $newproduct->abf_id             =   $produk->abf_id ;
            $newproduct->no_urut            =   $produk->no_urut ;
            $newproduct->chiller_id         =   $produk->chiller_id ;
            $newproduct->gudang_id_keluar   =   $produk->gudang_id_keluar ;
            $newproduct->status             =   $produk->status;
            if (!$newproduct->save()) {
                DB::rollBack() ;
                $result['status']   =   400;
                $result['msg']      =   "Proses Gagal";
                return $result;
            }

            $component      =   [];
            foreach (Bom::where('bom_name', $item_assembly)->first()->bomproses as $row) {
                $component[] =   [
                    "type"              =>  "Component",
                    "internal_id_item"  =>  (string)Item::item_sku($row->sku)->netsuite_internal_id,
                    "item"              =>  $row->sku,
                    "description"       =>  (string)Item::item_sku($row->sku)->nama,
                    "qty"               =>  (string)Item::where('nama', 'AY - S')->first()->sku == $row->sku ? $row->qty_per_assembly : ($row->qty_per_assembly * $newproduct->berat),
                ];
            }

            $component[] = [
                "type"              =>  "Component",
                "internal_id_item"  =>  (string)Item::find($request->plastik)->netsuite_internal_id,
                "item"              =>  (string)Item::find($request->plastik)->sku,
                "description"       =>  (string)Item::find($request->plastik)->nama,
                "qty"               =>  (string)$newproduct->berat,
            ];

            $component[] = [
                "type"              =>  "Finished Goods",
                "internal_id_item"  =>  (string)Item::find($produk->product_id)->netsuite_internal_id,
                "item"              =>  (string)Item::find($produk->product_id)->sku,
                "description"       =>  (string)Item::find($produk->product_id)->nama,
                "qty"               =>  (string)$newproduct->berat,
            ];
        }

        $location       =   Gudang::find($request->cold)->code;
        $id_location    =   Gudang::find($request->cold)->netsuite_internal_id;

        Netsuite::work_order('product_gudang', ($newproduct->id ?? $produk->id), 'wo-5', $id_assembly, $item_assembly, $id_location, $location, $component, null) ;
        Netsuite::wo_build('product_gudang', ($newproduct->id ?? $produk->id), 'wo-5-build', $id_assembly, $item_assembly, $id_location, $location, ($newproduct->berat ?? $produk->berat), $component, null) ;

        DB::commit() ;
    }
}
