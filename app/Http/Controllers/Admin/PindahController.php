<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chiller;
use App\Models\Gudang;
use App\Models\Item;
use App\Models\Netsuite;
use App\Models\Product_gudang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PindahController extends Controller
{
    public function index()
    {
        $cold     =   Gudang::whereIn('kategori', ['Warehouse'])
        ->where('code', 'LIKE', env('NET_SUBSIDIARY', 'CGL').' - %')
        ->where('status', 1)
        ->get();
        return view('admin.pages.pindahgudang.index', compact('cold'));
    }

    public function show(Request $request)
    {
        $tanggal    =   $request->tanggal ?? date('Y-m-d');
        $id         =   $request->id ?? '';
        $list_gudang     = Gudang::whereIn('kategori', ['Warehouse'])
                                    ->where('code', 'LIKE', env('NET_SUBSIDIARY', 'CGL').' - %')
                                    ->where('status', 1)
                                    ->get();
        if ($request->id == 2) {
            $gudang     =   Chiller::where('jenis', 'masuk')->where('type', 'bahan-baku')->where('tanggal_produksi', $tanggal)->get();

            $pindah     =   Gudang::whereIn('kategori', ['Warehouse','Production'])
                                    ->where('code', 'LIKE', env('NET_SUBSIDIARY', 'CGL').' - %')
                                    ->where('status', 1)
                                    ->get();

        } elseif ($request->id == 4) {
            $gudang     =   Chiller::where('jenis', 'masuk')->where('type', 'hasil-produksi')->where('tanggal_produksi', $tanggal)->get();
            $pindah     =   Gudang::whereIn('kategori', ['Warehouse','Production'])
            ->where('code', 'LIKE', env('NET_SUBSIDIARY', 'CGL').' - %')
            ->where('status', 1)
            ->get();
        }
        else if($request->key == 'summary_pindah_gudang'){
            $tgl_awal   = $request->tgl_awal;
            $tgl_akhir  = $request->tgl_akhir;

            $summary    = Product_gudang::whereBetween('production_date', [$tgl_awal, $tgl_akhir])
                                            ->where('type','pindah_gudang')
                                            ->paginate(10);
            // dd($summary);
            return view('admin.pages.pindahgudang.summary.show',compact('summary'));
        }
        else if($request->key == 'edit'){
            $id              = $request->id;
            $edit_data       = Product_gudang::find($id);
            return view('admin.pages.pindahgudang.summary.summary-edit',compact('edit_data','list_gudang'));
        }
        else if($request->key == 'detail'){
            $id              = $request->id;
            $detail          = Product_gudang::find($id);
            return view('admin.pages.pindahgudang.summary.summary-detail',compact('detail','list_gudang'));
        }
        else if($request->key == 'hapus'){
            $id              = $request->id;
            $hapus          = Product_gudang::find($id);
            return view('admin.pages.pindahgudang.summary.summary-hapus',compact('hapus','list_gudang'));
        } else {
            $gudang     =   Product_gudang::where('gudang_id', $request->id)->where('production_date', 'like', '%' . $tanggal . '%')->where('status', '2')->get();
            $pindah     =   Gudang::whereIn('kategori', ['Warehouse','Production'])
                                    ->where('code', 'LIKE', env('NET_SUBSIDIARY', 'CGL').' - %')
                                    ->where('status', 1)
                                    ->get();
        }
        return view('admin.pages.pindahgudang.show', compact('gudang', 'pindah', 'id'));
    }

    public function store(Request $request)
    {

        // return $request->all();
        // dd($request->all());
        DB::beginTransaction() ;
        $data                   =   Product_gudang::where('id', $request->id)->first();

        $sisa_qty               =   $request->qty - $data->qty ;
        $sisa_berat             =   $request->berat - $data->berat ;

        $data->sub_item         =   $request->sub_item;
        $data->qty              =   $request->qty;
        $data->berat            =   $request->berat;
        $data->gudang_id        =   $request->cold;
        $data->asal_abf         =   $request->abf ;
        $data->barang_titipan   =   $request->titipan ? 1 : NULL ;

        if (!$data->save()) {

            DB::rollBack() ;
            $result['status']   =   400 ;
            $result['msg']      =   "Proses gagal" ;
            return $result ;
        }

        $gudang_out                     =   new Product_gudang;
        $gudang_out->product_id         =   $data->product_id;
        $gudang_out->nama               =   $data->nama;
        $gudang_out->qty                =   $data->qty;
        $gudang_out->berat              =   $data->berat;
        $gudang_out->kategori           =   $data->kategori;
        $gudang_out->sub_item           =   $data->sub_item;
        // $gudang_out->table_name         =   $data->table_name;
        $gudang_out->table_name         =   'product_gudang';
        $gudang_out->table_id           =   $data->id;
        $gudang_out->qty_awal           =   $data->qty_awal;
        $gudang_out->berat_awal         =   $data->berat_awal;
        $gudang_out->subpack            =   $data->subpack;
        $gudang_out->karung             =   $data->karung;
        $gudang_out->karung_qty         =   $data->karung_qty;
        $gudang_out->notes              =   $data->notes;
        $gudang_out->palete             =   $data->palete;
        $gudang_out->potong             =   $data->potong;
        $gudang_out->expired            =   $data->expired;
        $gudang_out->production_date    =   $data->production_date;
        $gudang_out->packaging          =   $data->packaging;
        $gudang_out->gudang_id          =   $request->cold;
        $gudang_out->type               =   "pindah_gudang";
        // $gudang_out->gudang_id      =   $data->id;
        $gudang_out->jenis_trans        =   "keluar" ;
        $gudang_out->stock_type         =   "negatif" ;
        $gudang_out->status             =   4 ;
        $gudang_out->save();

        $gudang_in                     =   new Product_gudang;
        $gudang_in->product_id         =   $data->product_id;
        $gudang_in->nama               =   $data->nama;
        $gudang_in->qty                =   $data->qty;
        $gudang_in->berat              =   $data->berat;
        $gudang_in->kategori           =   $data->kategori;
        $gudang_in->sub_item           =   $data->sub_item;
    // $gudang_out->table_name         =   $data->table_name;
        $gudang_in->table_name        =   'product_gudang';
        $gudang_in->table_id          =   $data->id;
        $gudang_in->qty_awal           =   $data->qty_awal;
        $gudang_in->berat_awal         =   $data->berat_awal;
        $gudang_in->subpack            =   $data->subpack;
        $gudang_in->karung             =   $data->karung;
        $gudang_in->karung_qty         =   $data->karung_qty;
        $gudang_in->notes              =   $data->notes;
        $gudang_in->palete             =   $data->palete;
        $gudang_in->potong             =   $data->potong;
        $gudang_in->expired            =   $data->expired;
        $gudang_in->production_date    =   $request->tanggal ?? $data->production_date;
        $gudang_in->packaging          =   $data->packaging;
        $gudang_in->gudang_id          =   $request->tujuan;
        $gudang_in->type               =   "pindah_gudang";
        // $gudang_in->gudang_id      =   $data->id;
        $gudang_in->status             =   2 ;
        $gudang_in->jenis_trans        =   "masuk" ;
        $gudang_in->stock_type         =   "positif" ;
        $gudang_in->save();

        DB::commit() ;
        $result['status']   =   200 ;
        $result['msg']      =   "Berhasil Update" ;
        return $result ;

        // try {
        //     if (($request->cold == 2) or ($request->cold == 4)) {

        //         $chiller                    =   Chiller::find($request->id);

        //         $chill                      =   new Chiller;
        //         $chill->table_name          =   'chiller';
        //         $chill->table_id            =   $chiller->id;
        //         $chill->asal_tujuan         =   $chiller->asal_tujuan;
        //         $chill->item_id             =   $chiller->item_id;
        //         $chill->item_name           =   $chiller->item_name;
        //         $chill->jenis               =   $chiller->jenis;
        //         $chill->type                =   $chiller->type;
        //         $chill->kategori            =   $request->tujuan;
        //         $chill->qty_item            =   $request->qty;
        //         $chill->berat_item          =   $request->berat;
        //         $chill->stock_item          =   $request->qty;
        //         $chill->stock_berat         =   $request->berat;
        //         $chill->tanggal_potong      =   $chiller->tanggal_potong;
        //         $chill->tanggal_produksi    =   $chiller->tanggal_produksi;
        //         $chill->no_mobil            =   $chiller->no_mobil;
        //         $chill->status              =   2;

        //         if (!$chill->save()) {
        //             DB::rollback();
        //             $result['status'] = 400;
        //             $result['msg'] = 'Gagal';
        //             return $result;
        //         }


        //         $chiller->stock_item        =   $chiller->stock_item - $request->qty;
        //         $chiller->stock_berat       =   $chiller->stock_item - $request->berat;

        //         if (!$chiller->save()) {
        //             DB::rollback();
        //             $result['status'] = 400;
        //             $result['msg'] = 'Gagal';
        //             return $result;
        //         }

        //         $gdg            =   Gudang::find($request->tujuan);
        //         $gdg_baru       =   Gudang::find($request->tujuan);

        //         $nama_tabel     =   "chiller";
        //         $id_tabel       =   $chill->id;
        //         $location       =   $gdg->code;
        //         $from           =   Gudang::gudang_netid($location);

        //         $to         =   Gudang::gudang_netid($gdg_baru->code);
        //         $idgudang   =   Gudang::gudang_id($gdg_baru->code);

        //         $id_location    =   Gudang::gudang_netid($location);
        //         $label          =   strtolower("ti_" . str_replace(" ", "", $gdg->code) . "_" . str_replace(" ", "", $gdg_baru->code));

        //         $item           = Item::find($chill->item_id);

        //         $transfer       =   [
        //             [
        //                 "internal_id_item"  =>  (string)$item->netsuite_internal_id,
        //                 "item"              =>  (string)$item->sku,
        //                 "qty_to_transfer"   =>  (string)$chill->stock_berat
        //             ]
        //         ];

        //         DB::commit();
        //         // return Netsuite::transfer_inventory($nama_tabel, $id_tabel, $label, $id_location, $location, $from, $to, $transfer, null);

        //     } else {

        //         $gudang =   Product_gudang::find($request->id);


        //         $plas   =   Item::where('nama', $gudang->packaging)->first();
        //             $plastik      =   [
        //                 'plastik'       =>  [
        //                     'sku'       =>  $plas->sku ?? NULL ,
        //                     'jenis'     =>  $plas->nama ?? NULL ,
        //                 ],
        //                 'parting'       =>  [
        //                     'qty'       =>  NULL
        //                 ],
        //                 'additional'    =>  [
        //                     'tunggir'   =>  NULL,
        //                     'lemak'     =>  NULL,
        //                     'maras'     =>  NULL,
        //                 ],
        //                 'sub_item'      => NULL
        //             ];

        //         if ($gudang) {


        //             if (($request->tujuan == 2) or ($request->tujuan == 4)) {

        //                 $chiller1                =   new Chiller;
        //                 $chiller1->table_name    =   'product_gudang';
        //                 $chiller1->table_id      =   $gudang->id;
        //                 $chiller1->asal_tujuan   =   'pindah_gudang';
        //                 $chiller1->item_id       =   $gudang->gudangabf->item_id;
        //                 $chiller1->item_name     =   $gudang->nama;
        //                 $chiller1->jenis         =   'masuk';
        //                 if ($request->tujuan == 2) {
        //                     $chiller1->type      =   'bahan-baku';
        //                 } else {
        //                     $chiller1->type      =   'hasil-produksi';
        //                 }
        //                 $chiller1->label         =   json_encode($plastik);
        //                 $chiller1->qty_item      =   $request->qty;
        //                 $chiller1->berat_item    =   $request->berat;
        //                 $chiller1->stock_item    =   $request->qty;
        //                 $chiller1->stock_berat   =   $request->berat;
        //                 $chiller1->status        =   2;

        //                 if (!$chiller1->save()) {
        //                     DB::rollback();
        //                     $result['status'] = 400;
        //                     $result['msg'] = 'Gagal';
        //                     return $result;
        //                 }

        //                 return $chiller1;

        //             } else {

        //                 $data               =   new Product_gudang;
        //                 $data->table_name   =   $gudang->table_name;
        //                 $data->table_id     =   $gudang->table_id;
        //                 $data->product_id   =   $gudang->product_id;
        //                 $data->qty_awal     =   $request->qty;
        //                 $data->berat_awal   =   $request->berat;
        //                 $data->qty          =   $request->qty;
        //                 $data->berat        =   $request->berat;
        //                 $data->packaging    =   $gudang->packaging;
        //                 $data->palete       =   $gudang->palete;
        //                 $data->expired      =   $gudang->expired;
        //                 $data->type         =   $gudang->type;
        //                 $data->stock_type   =   $gudang->stock_type;
        //                 $data->jenis_trans  =   'masuk';
        //                 $data->gudang_id    =   $request->tujuan;
        //                 $data->status       =   1;

        //                 if (!$data->save()) {
        //                     DB::rollback();
        //                     $result['status'] = 400;
        //                     $result['msg'] = 'Gagal';
        //                     return $result;
        //                 }
        //             }

        //             $gudang->qty        =   $gudang->qty - $request->qty;
        //             $gudang->berat      =   $gudang->berat - $request->berat;

        //             if (!$gudang->save()) {
        //                 DB::rollback();
        //                 $result['status'] = 400;
        //                 $result['msg'] = 'Gagal';
        //                 return $result;
        //             }

        //             $gdg            =   Gudang::find($gudang->gudang_id);
        //             $gdg_baru       =   Gudang::find($request->tujuan);

        //             $nama_tabel     =   "product_gudang";
        //             $id_tabel       =   $data->id;
        //             $location       =   $gdg->code;
        //             $from           =   Gudang::gudang_netid($location);

        //             $to             =   Gudang::gudang_netid($gdg_baru->code);
        //             // $idgudang       =   Gudang::gudang_id($gdg_baru->code);

        //             $id_location    =   Gudang::gudang_netid($location);
        //             $label          =   strtolower("ti_" . str_replace(" ", "", $gdg->code) . "_" . str_replace(" ", "", $gdg_baru->code));

        //             $item           = Item::find($gudang->product_id);

        //             $transfer       =   [
        //                 [
        //                     "internal_id_item"  =>  (string)$item->netsuite_internal_id,
        //                     "item"              =>  (string)$item->sku,
        //                     "qty_to_transfer"   =>  (string)$data->berat
        //                 ]
        //             ];


        //             DB::commit();
        //             // return Netsuite::transfer_inventory($nama_tabel, $id_tabel, $label, $id_location, $location, $from, $to, $transfer, null);
        //         }
        //     }
        // } catch (\Throwable $th) {

        //     DB::rollback();
        //     return $th->getMessage();
        //     //throw $th;
        // }
    }

    public function update ($id, Request $request)
    {
        // dd($request->all());
        $update = Product_gudang::find($id);
        $update->qty        = $request->qty;
        $update->berat      = $request->berat;
        $update->gudang_id  = $request->gudang;
        $update->save();
        return back()->with('status', 1)->with('message', 'update pindah gudang berhasil');

    }

    public function delete ($id, Request $request)
    {
        $delete = Product_gudang::find($id);
        $delete->delete();
        return back()->with('status', 1)->with('message', 'data berhasil dihapus');
    }
}
