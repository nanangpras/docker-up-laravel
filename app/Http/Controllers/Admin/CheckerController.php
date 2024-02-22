<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Adminedit;
use App\Models\AppKey;
use App\Models\Chiller;
use App\Models\Evis;
use App\Models\Grading;
use App\Models\Item;
use App\Models\Lpah;
use App\Models\Netsuite;
use App\Models\Production;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckerController extends Controller
{
    //
    public function produksi(Request $request, $id){

        $data       =   Lpah::where('type', 'isi')
                        ->where('production_id', $id)
                        ->orderBy('id', 'DESC')
                        ->first();

        $produksi   =   Production::where('id', $id)->first();
        $item       =   Item::whereIn('category_id',[1,12, 14, 17])
                            ->where('nama', 'not like', '%(RM)%')
                            // ->where('nama', 'not like', '%AYAM UTUH BROILER%')
                            ->where('nama', 'not like', '%UTUH REPACK%')
                            ->where('nama', 'not like', '%UTUH THAWING%')
                            ->where('nama', 'not like', '%UTUH KAMPUNG%')
                            ->where('nama', 'not like', '%UTUH PARENT%')
                            ->where('nama', 'not like', '%REPACK%')
                            ->where('nama', 'not like', '%THAWING%')
                            ->where('nama', 'not like', '%KAMPUNG%')
                            // ->where('nama', 'not like', '%PEJANTAN%')
                            ->where('nama', 'not like', '%SUSUT PENJUALAN%')
                            // ->where('nama', 'not like', '%PARENT%')
                            ->get();

        $track      =   Adminedit::where('activity', 'checker')
                        ->where('key', $id)
                        ->get();
        $ceklogedit   = Adminedit::where('table_id', $id)->where('table_name', 'productions')->where('type', 'edit')->count();
        
        if ($produksi) {
            return view('admin.pages.checker.index', compact('data','produksi','item', 'track','ceklogedit'));
        } 
        else {
        return redirect()->route('index');
        }

    }

    public function produksiEdit(Request $request){
        if($request->key == "editlpah"){
            $id             = $request->id;
            $jenis          = $request->jenis;
            $datalpah       = Lpah::where('id',$id)->first();
            return view ('admin.pages.lpah.form.edit-lpah',compact('datalpah','id','jenis'));
        }else
        if($request->key == "editevis"){
            $id             = $request->id;
            $dataevis       = Evis::where('id',$id)->first();
            return view ('admin.pages.lpah.form.edit-evis',compact('dataevis','id'));
        }else
        if ($request->key == "editgrading") {
            $id             = $request->id;
            $produksi       = $request->produksi;
            $datagrading    = Grading::where('id',$id)->first();
            return view ('admin.pages.lpah.form.edit-grading',compact('produksi','datagrading','id'));
        }
    }
    public function netsuite($id)
    {
        $netsuite   =   Netsuite::where('tabel', 'productions')->where('tabel_id', $id)->get();

        return view('admin.pages.checker.netsuite', compact('netsuite'));
    }

    public function create_itemreceipt_wo1(Request $request){
        $id = $request->id;
    
        $produksi = Production::find($id);

        if ($request->jenis == 'keterangan_benchmark') {
            // dd($request->all());

            $produksi->keterangan_benchmark = $request->keterangan_benchmark;
            $produksi->save();
            return back()->with('status', 1)->with('message', 'Proses Simpan Keterangan Benchmark Berhasil');
            
        } else {
    
            if($produksi){
    
                $lpah_stat = 0;
                if ($produksi->lpah_netsuite_status == null && $produksi->lpah_status == "1") {
                    Netsuite::item_receipt_lpah($produksi->id);
                    $lpah_stat = 1;
                }
    
                $wo_stat = 0;
                if ($produksi->wo_netsuite_status == null) {
                    Netsuite::wo_1($produksi->id);
                    $wo_stat = 1;
                }
    
                if($lpah_stat==1 && $wo_stat==1){
                    return back()->with('status', 1)->with('message', 'Proses Kirim Netsuite Berhasil');
                }else{
                    return back()->with('status', 2)->with('message', 'Tidak ada yg diproses');
                }
    
    
            }else{
                return back()->with('status', 2)->with('message', 'Proses gagal');
            }

        }


    }

    public function addgrading(Request $request)
    {
        DB::beginTransaction();

        $grad       =   new Grading;
        $grading    =   Grading::where('trans_id', $request->idproduksi)->first();
        $item       =   Item::find($request->item);
        // dd($item);
        $exp        =   explode('-', $item->slug);
        $production =   Production::find($request->idproduksi);

        $jenis_karkas = "normal";
        if($exp[1]=="karkas"){
            $jenis_karkas = "normal";
        }else{
            $jenis_karkas = $exp[1];
        }

        // dd( Production::find($request->idproduksi)->grading_selesai ?? Production::find($request->idproduksi)->prod_tanggal_potong);
        $grad->trans_id         =   $request->idproduksi;
        $grad->item_id          =   $request->item;
        $grad->jenis_karkas     =   $jenis_karkas;
        $grad->grade_item       =   Item::item_jenis($item->id) ?? NULL;
        $grad->total_item       =   $request->qty;
        $grad->tanggal_potong   =   Production::find($request->idproduksi)->grading_selesai ?? Production::find($request->idproduksi)->prod_tanggal_potong ;
        $grad->berat_item       =   $request->berat;
        $grad->stock_item       =   $request->qty;
        $grad->stock_berat      =   $request->berat;
        $grad->keranjang        =   0;
        if (!$grad->save()) {
            DB::rollBack() ;
            return back()->with('status', 2)->with('message', 'Proses gagal');
        }

        if ($production->grading_status == 1) {
            $chill  =   Chiller::whereDate('tanggal_potong', $production->prod_tanggal_potong)
                        ->where('item_id', $request->item)
                        ->where('status', 2)
                        ->where('type', 'bahan-baku')
                        ->where('asal_tujuan', 'gradinggabungan')
                        ->first();

            // dd($grad->berat_item);

            if ($chill) {
                $chill->berat_keranjang      =   $chill->berat_keranjang + $grad->berat_keranjang;
                $chill->qty_item             =   $chill->qty_item + $grad->total_item;
                $chill->stock_item           =   $chill->stock_item + $grad->total_item;
                $chill->berat_item           =   $chill->berat_item + $grad->berat_item;
                $chill->stock_berat          =   $chill->stock_berat + $grad->berat_item;
                if (!$chill->save()) {
                    DB::rollBack();
                    return back()->with('status', 2)->with('message', 'Proses gagal');
                }

            } else {
                $chill                      =   new Chiller;
                $chill->asal_tujuan         =   'gradinggabungan';
                $chill->type                =   'bahan-baku';
                $chill->item_id             =   $grad->item_id;
                $chill->item_name           =   $grad->graditem->nama;
                $chill->tanggal_potong      =   $production->prod_tanggal_potong;
                $chill->tanggal_produksi    =   $production->prod_tanggal_potong;
                $chill->keranjang           =   $grad->keranjang;
                $chill->berat_keranjang     =   $grad->berat_keranjang;
                $chill->qty_item            =   $grad->total_item;
                $chill->stock_item          =   $grad->total_item;
                $chill->berat_item          =   $grad->berat_item;
                $chill->stock_berat         =   $grad->berat_item;
                $chill->status              =   2;
                $chill->jenis               =   'masuk';
                if (!$chill->save()) {
                    DB::rollBack();
                    return back()->with('status', 2)->with('message', 'Proses gagal');
                }
            }
        }

        $edit                       =   new Adminedit ;
        $edit->user_id              =   Auth::user()->id ;
        $edit->table_name           =   'grading' ;
        $edit->table_id             =   $grad->id ;
        $edit->activity             =   'checker' ;
        $edit->content              =   'TAMBAH ITEM ' . $grad->graditem->nama;
        $edit->data                 =   json_encode($grad);
        $edit->type                 =   'tambah' ;
        $edit->key                  =   $request->idproduksi ;
        $edit->status               =   1 ;
        if (!$edit->save()) {
            DB::rollBack();
            return back()->with('status', 2)->with('message', 'Proses gagal');
        }

        DB::commit() ;
        return back()->with('status', 1)->with('message', 'Berhasil Simpan');
    }


    public function addevis(Request $request)
    {
        // dd($request->all());    
        $evis                   =   new Evis; //evis
        $item                   =   Item::find($request->item);
        $exp                    =   explode('-', $item->slug);
        $production             =   Production::find($request->idproduksi);
        // dd($production);


        $evis->production_id    =   $request->idproduksi;
        $evis->item_id          =   $request->item;
        $evis->jenis            =   'mobil';
        $evis->peruntukan       =   'stock';
        // $evis->tanggal_potong   =   Production::find($request->idproduksi)->evis_selesai ;
        $evis->tanggal_potong   =   Production::find($request->idproduksi)->prod_tanggal_potong ;
        $evis->total_item       =   $request->qty;
        $evis->berat_item       =   $request->berat;
        $evis->stock_item       =   $request->qty;
        $evis->berat_stock      =   $request->berat;
        $evis->keranjang        =   0;
        if (!$evis->save()) {
            return back()->with('status', 2)->with('message', 'Proses gagal');
        }

        if($production->evis_status == 1){
            // Chiller
            $chill  =   Chiller::whereDate('tanggal_potong', $production->prod_tanggal_potong)
                        ->where('item_id', $request->item)
                        ->where('status', 2)
                        ->where('type', 'bahan-baku')
                        ->where('asal_tujuan', 'evisgabungan')
                        ->first();
            // dd($chill);
            if ($chill) {
                // $chill->berat_keranjang      =   $chill->berat_keranjang + $evis->berat_keranjang;
                $chill->qty_item             =   $chill->qty_item + $evis->total_item;
                $chill->stock_item           =   $chill->stock_item + $evis->total_item;
                $chill->berat_item           =   $chill->berat_item + $evis->berat_item;
                $chill->stock_berat          =   $chill->stock_berat + $evis->berat_item;

                if (!$chill->save()) {
                    return back()->with('status', 2)->with('message', 'Proses gagal');
                }


            } else {
                $chillerInsert                      =   new Chiller;
                $chillerInsert->asal_tujuan         =   'evisgabungan';
                $chillerInsert->type                =   'bahan-baku';
                $chillerInsert->regu                =   'byproduct';
                $chillerInsert->item_id             =   $evis->item_id;
                $chillerInsert->item_name           =   $item->nama;
                $chillerInsert->tanggal_potong      =   $production->prod_tanggal_potong;
                $chillerInsert->tanggal_produksi    =   $production->prod_tanggal_potong;
                // $chillerInsert->keranjang           =   $evis->keranjang;
                // $chillerInsert->berat_keranjang     =   $evis->berat_keranjang;
                $chillerInsert->qty_item            =   $evis->total_item;
                $chillerInsert->stock_item          =   $evis->total_item;
                $chillerInsert->berat_item          =   $evis->berat_item;
                $chillerInsert->stock_berat         =   $evis->berat_item;
                $chillerInsert->status              =   2;
                $chillerInsert->jenis               =   'masuk';

                if (!$chillerInsert->save()) {
                    return back()->with('status', 2)->with('message', 'Proses gagal');
                }
            }
        }

        $edit                       =   new Adminedit ;
        $edit->user_id              =   Auth::user()->id ;
        $edit->table_name           =   'evis' ;
        $edit->table_id             =   $evis->id ;
        $edit->activity             =   'checker' ;
        $edit->content              =   'TAMBAH ITEM ' . $evis->eviitem->nama;
        $edit->data                 =   json_encode($evis);
        $edit->type                 =   'tambah' ;
        $edit->key                  =   $request->idproduksi ;
        $edit->status               =   1 ;
        if (!$edit->save()) {
            DB::rollBack();
            return back()->with('status', 2)->with('message', 'Proses gagal');
        }
        DB::commit() ;
        
        try {
            Chiller::recalculate_chiller($chill->id);
        } catch (\Throwable $th) {
            
        }
        
        return back()->with('status', 1)->with('message', 'Berhasil Simpan');




    }
}

