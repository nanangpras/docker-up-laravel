<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Adminedit;
use App\Models\Bahanbaku;
use App\Models\Bom;
use App\Models\Chiller;
use App\Models\Gudang;
use App\Models\Item;
use App\Models\Netsuite;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product_gudang;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EditSoController extends Controller
{

    // OPTIMALISASI WITH ORM LARAVEL MULTIPLE COMPONENT
    public function index($id)
    {
        $order          =   Order::with(['daftar_order_full','daftar_order_full.getDeletedBahanBaku','daftar_order_full.getHistoryReset'])->find($id);
        return view('admin.pages.editso.optimalisasi.index', compact('order'));
    }

    // OPTIMALISASI WITH ORM LARAVEL ONE COMPONENT
    public function indexOld($id)
    {
        $order          =   Order::with(['daftar_order_full','daftar_order_full.getDeletedBahanBaku','daftar_order_full.getHistoryReset','cekDataOrderBahanBaku','getNetsuite'])->find($id);
        return view('admin.pages.editso.index', compact('order'));
    }

    // OPTIMALISASI NON ORM LARAVEL
    public function indexNew($id)
    {
        $order                              =   Order::with(['daftar_order_full'])->find($id);
        $storeData                          =   array();
        $arrayId                            =   array();
        foreach($order->daftar_order_full as $val){
            $arrayId[]                      =   $val->id; 
        }
        $stringData                         =   implode(",",$arrayId);
        if($stringData){
            
            $getOrderBahanBaku              =   Adminedit::select('table_id',DB::raw('count(table_id) as jumlah'))
                                                            ->where('table_name', 'order_items')
                                                            ->whereRaw('table_id IN('.$stringData.')')
                                                            ->where('activity', 'delete_bb')
                                                            ->groupBy('table_id')
                                                            ->get();
            $jumlahreset                    =   Adminedit::select('table_id',DB::raw('count(table_id) as jumlah'))
                                                            ->where('table_name','order_bahan_baku')
                                                            ->whereRaw('table_id IN('.$stringData.')')
                                                            ->where('type','reset')
                                                            ->groupBy('table_id')
                                                            ->get();
        }
        foreach($order->daftar_order_full as $val){   

            $historyDeleteBB                = 0;
            foreach($getOrderBahanBaku as $val1){
                if($val->id == $val1->table_id){
                    $historyDeleteBB        = $val1->jumlah;
                }
            }

            $historyreset                   = 0;
            foreach($jumlahreset as $val2){
                if($val2->table_id == $val->id){
                    $historyreset           = $val2->jumlah;
                }
            }    

            // $internalMemo                   = "";

            $internalMemo                   = Order::getInternalMemo($order->no_so, $val->id);
            $category_id                    = $val->item->category_id;
            $storeData[] = [
                "id"                        => $val->id,
                "order_id"                  => $val->order_id,
                "line_id"                   => $val->line_id,
                "netsuite_send"             => $val->netsuite_send,
                "item_id"                   => $val->item_id,
                "nama_detail"               => $val->nama_detail,
                "no_so"                     => $val->no_so,
                "partner"                   => $val->partner,
                "alamat_kirim"              => $val->alamat_kirim,
                "wilayah"                   => $val->wilayah,
                "part"                      => $val->part,
                "bumbu"                     => $val->bumbu,
                "memo"                      => $val->memo,
                "description_item"          => $val->description_item,
                "unit"                      => $val->unit,
                "rate"                      => $val->rate,
                "sku"                       => $val->sku,
                "potong"                    => $val->potong,
                "keterangan"                => $val->keterangan,
                "kode"                      => $val->kode,
                "qty"                       => $val->qty,
                "fulfillment_qty"           => $val->fulfillment_qty,
                "berat"                     => $val->berat,
                "fulfillment_berat"         => $val->fulfillment_berat,
                "harga"                     => $val->harga,
                "kr_proses"                 => $val->kr_proses,
                "kr_selesai"                => $val->kr_selesai,
                "retur_tujuan"              => $val->retur_tujuan,
                "retur_status"              => $val->retur_status,
                "retur_qty"                 => $val->retur_qty,
                "tidak_terkirim"            => $val->tidak_terkirim,
                "tidak_terkirim_catatan"    => $val->tidak_terkirim_catatan,
                "retur_berat"               => $val->retur_berat,
                "retur_notes"               => $val->retur_notes,
                "status"                    => $val->status,
                "key"                       => $val->key,
                "created_at"                => $val->created_at ? date('Y-m-d H:i:s', strtotime($val->created_at)) : null,
                "updated_at"                => $val->updated_at ? date('Y-m-d H:i:s', strtotime($val->updated_at)) : null,
                "deleted_at"                => $val->deleted_at ? date('Y-m-d H:i:s', strtotime($val->deleted_at)) : null,
                "edited"                    => $val->edited,
                "category_id"               => $category_id,
                "internalMemo"              => $internalMemo ?? "#",
                "historyDeleteBB"           => $historyDeleteBB,
                "historyreset"              => $historyreset
            ];
        }
        $store                              = json_decode(json_encode($storeData));
        return view('admin.pages.editso.index-new', compact('order','store'));
    }

    public function getIntegrasiNetsuite(Request $request){
        if($request->key == 'integrasinetsuitewithorm'){
            $order                              =   Order::with(['cekDataOrderBahanBaku','getNetsuite'])->find($request->id);
            return view('admin.pages.editso.optimalisasi.integrasi-netsuite',compact('order'));
        }else{
            $order                              =   Order::find($request->id);
            $bbwithstatussatu                   =   Bahanbaku::where('status', 1)->where('order_id', $request->id)->count();
            $ns                                 =   Netsuite::select('id','failed','status','response_id','document_code','trans_date','document_no','count','respon_time','tabel_id')->where('tabel_id', $request->id)->where('label', 'itemfulfill')->where('tabel', 'orders')->first();
            return view('admin.pages.editso.integrasi-netsuite',compact('order','bbwithstatussatu','ns'));
        }
    }

    public function sampingan($id)
    {
        $order      =   Order::find($id);

        return view('admin.pages.editso.sampingan', compact('order'));
    }

    public function pemenuhanAlokasi(Request $request)
    {

        $order_item_id = $request->order_item_id;
        if ($request->key == 'info') {
            $order  =   OrderItem::find($order_item_id);
            $qty    =   Bahanbaku::where('order_item_id', $order_item_id)->sum('bb_item');
            $berat  =   Bahanbaku::where('order_item_id', $order_item_id)->sum('bb_berat');
            return view('admin.pages.penyiapan.info_order', compact('order', 'qty', 'berat'));
        } 
        if($request->key == "viewmodaleditpemenuhan"){
            if($request->prosesambil == 'frozen'){
                $sisaqty            = Product_gudang::ambilsisaproductgudang($request->chiller_out,'qty_awal','qty','bb_item',$request->id);
                $sisaberat          = Product_gudang::ambilsisaproductgudang($request->chiller_out,'berat_awal','berat','bb_berat',$request->id);
                $convertSisaBerat   = number_format((float)$sisaberat, 2, '.', '');
            }else{
                $sisaqty            = Chiller::ambilsisachiller($request->chiller_out,'qty_item','qty','bb_item',$request->id);
                $sisaberat          = Chiller::ambilsisachiller($request->chiller_out,'berat_item','berat','bb_berat',$request->id);
                $convertSisaBerat   = number_format((float)$sisaberat, 2, '.', '');

            }
            $data = [
                'id'            => $request->id,
                'netsuite_id'   => $request->netsuite_id,
                'orderitemid'   => $request->orderitemid,
                'nama'          => $request->nama,
                'no_do'         => $request->nodo,
                'bb_item'       => $request->bb_item,
                'bb_berat'      => $request->bb_berat,
                'prosesambil'   => $request->prosesambil,
                'chillerout'    => $request->chillerout,
                'sisaqty'       => $sisaqty,
                'sisaberat'     => $convertSisaBerat
            ];
            return view('admin.pages.editso.modal_edit_pemenuhan',compact('data'));
        }

        if($request->key == "createWO2SiapKirim"){
            $item               =   Item::find($request->idItem);
            $getOrderBahanBaku  =   Bahanbaku::find($request->bahanBakuId);

            // WO2 langsung kirim

            if ($getOrderBahanBaku) {
                if (Item::item_jenis($item->id) == 'normal' || Item::item_jenis($item->id) == 'parent' || Item::item_jenis($item->id) == 'pejantan') {

                    $id_location    =   Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Chiller Bahan Baku") ;
                    $location       =   env('NET_SUBSIDIARY', 'CGL')." - Chiller Bahan Baku" ;
                    $from           =   $id_location;
    
                    $label          =   'wo-2';
    
                    $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL')." - AYAM KARKAS BROILER")->first();
    
                    $nama_assembly  =   $bom->bom_name ;
                    $id_assembly    =   $bom->netsuite_internal_id ;
    
                    $bom_id         =   $bom->id;
                    $item_assembly  =   env('NET_SUBSIDIARY', 'CGL')." - AYAM KARKAS BROILER";
    
                    if (substr($item->sku, 0, 5) == "12111") {
    
                        $component      =   [[
                            "type"              =>  "Component",
                            "internal_id_item"  =>  (string)Item::item_sku("1100000001")->netsuite_internal_id,
                            "item"              =>  "1100000001",
                            "description"       =>  "AYAM KARKAS BROILER (RM)",
                            "qty"               =>  $getOrderBahanBaku->bb_berat
                        ]];
    
                    } elseif (substr($item->sku, 0, 5) == "12113") {
    
                        $component      =   [[
                            "type"              =>  "Component",
                            "internal_id_item"  =>  (string)Item::item_sku("1100000003")->netsuite_internal_id ,
                            "item"              =>  "1100000003",
                            "description"       =>  "AYAM MEMAR (RM)",
                            "qty"               =>  $getOrderBahanBaku->bb_berat
                        ]];
                    } elseif (substr($item->sku, 0, 4) == "1213" || substr($item->sku, 0, 4) == "1223") {
    
                        $component      =   [[
                            "type"              =>  "Component",
                            "internal_id_item"  =>  (string)Item::item_sku("1100000005")->netsuite_internal_id ,
                            "item"              =>  "1100000005",
                            "description"       =>  "AYAM PEJANTAN (RM)",
                            "qty"               =>  $getOrderBahanBaku->bb_berat
                        ]];
                        
                    } elseif (substr($item->sku, 0, 4) == "1214" || substr($item->sku, 0, 4) == "1224") {
    
                        $component      =   [[
                            "type"              =>  "Component",
                            "internal_id_item"  =>  (string)Item::item_sku("1100000009")->netsuite_internal_id ,
                            "item"              =>  "1100000009",
                            "description"       =>  "AYAM PARENT (RM)",
                            "qty"               =>  $getOrderBahanBaku->bb_berat
                        ]];
                    } elseif (substr($item->sku, 0, 5) == "12112") {
    
                        $component      =   [[
                            "type"              =>  "Component",
                            "internal_id_item"  =>  (string)Item::item_sku("1100000002")->netsuite_internal_id ,
                            "item"              =>  "1100000002",
                            "description"       =>  "AYAM UTUH (RM)",
                            "qty"               =>  $getOrderBahanBaku->bb_berat
                        ]];
                    } 
    
                    $proses =   [];
    
                    foreach ($bom->bomproses as $row) {
                        $proses[]   =   [
                            "type"              =>  "Component",
                            "internal_id_item"  =>  (string)Item::item_sku($row->sku)->netsuite_internal_id,
                            "item"              =>  $row->sku,
                            "description"       =>  (string)Item::item_sku($row->sku)->nama,
                            "qty"               =>  (string)Item::where('nama', 'AY - S')->first()->sku == $row->sku ? $row->qty_per_assembly : ($row->qty_per_assembly * $getOrderBahanBaku->bb_berat),
                        ];
                    }
    
                    $finished_good  =   [[
                        "type"              =>  "Finished Goods",
                        "internal_id_item"  =>  (string)$item->netsuite_internal_id,
                        "item"              =>  (string)$item->sku,
                        "description"       =>  (string)Item::item_sku($item->sku)->nama,
                        "qty"               =>  (string)$getOrderBahanBaku->bb_berat
                    ]];
    
                    $produksi       =   array_merge($component, $proses, $finished_good);
                    $nama_tabel     =   'chiller';
                    $getChillerId   =   Chiller::where('table_id', $request->bahanBakuId)->where('table_name', 'order_bahanbaku')->where('type', 'alokasi-order')->first();
                    $id_tabel       =   $getChillerId->id;
    
                    $wo = Netsuite::work_order($nama_tabel, $id_tabel, $label, $id_assembly, $item_assembly, $id_location, $location, $produksi, null);
    
                    $label          =   'wo-2-build';
                    $total          =   $getOrderBahanBaku->bb_berat;
                    $wob = Netsuite::wo_build($nama_tabel, $id_tabel, $label, $id_assembly, $item_assembly, $id_location, $location, $total, $produksi, $wo->id);

                    $data['status'] =   200;
                    $data['msg']    =   'WO 2 '. $item->nama. ' Berhasil dibuat';
                    return $data;
                }
            }
        }


        else {
            $view           =   $request->view ?? '' ;
            $prosesambil    =   $request->prosesambil ?? '' ;
            $pemenuhan      =   Bahanbaku::where('order_item_id', $order_item_id)->get();

            return view('admin.pages.editso.pemenuhan', compact('pemenuhan', 'view'));
        }
    }

    public function pemenuhanAlokasiSampingan(Request $request)
    {
        $order_item_id  =   $request->order_item_id;
        if ($request->key == 'info') {
            $order  =   OrderItem::find($order_item_id);
            $qty    =   Bahanbaku::where('order_item_id', $order_item_id)->sum('bb_item');
            $berat  =   Bahanbaku::where('order_item_id', $order_item_id)->sum('bb_berat');
            return view('admin.pages.sampingan.info_order', compact('order', 'qty', 'berat'));
        } else {
            $pemenuhan = Bahanbaku::where('order_item_id', $order_item_id)->get();
            return view('admin.pages.editso.pemenuhansampingan', compact('pemenuhan'));
        }
    }

    public function deleteAlokasi(Request $request)
    {

        DB::beginTransaction();
        $pemenuhan              = Bahanbaku::find($request->id);
        
        if ($pemenuhan) {

            // dd($pemenuhan->proses_ambil);
            if ($pemenuhan->proses_ambil == 'frozen') {
                $gudang             =   Product_gudang::find($pemenuhan->chiller_out);
                if($pemenuhan->status == '1'){
                    $gudang->berat      =   $gudang->berat ;
                    $gudang->qty        =   $gudang->qty ;
                    $gudang->save();

                }else{
                    $gudang->berat      =   $gudang->berat + $pemenuhan->bb_berat;
                    $gudang->qty        =   $gudang->qty + $pemenuhan->bb_item;
                    $gudang->save();
                    
                }
    
            } else {
    
                $chiller                =   Chiller::find($pemenuhan->chiller_out);
                $chiller->stock_berat   =   $chiller->stock_berat + $pemenuhan->bb_berat;
                $chiller->stock_item    =   $chiller->stock_item + $pemenuhan->bb_item;
                $chiller->save();
            }
    
            $chil       =   Chiller::where('table_id', $pemenuhan->id)->where('table_name', 'order_bahanbaku')->first();
    
            if ($chil) {
                $chil->delete();
            }
    
            
            $pemenuhan->delete();
            
            $fulfill_item_id = OrderItem::find($pemenuhan->order_item_id);
    
            if($fulfill_item_id){

    
                $fulfill_item_id->fulfillment_qty       = OrderItem::recalculate_fulfill_qty($fulfill_item_id->id);
                $fulfill_item_id->fulfillment_berat     = OrderItem::recalculate_fulfill_berat($fulfill_item_id->id);
                // $fulfill_item_id->status                = NULL;
                // $fulfill_item_id->fulfillment_berat     = NULL;
                // $fulfill_item_id->fulfillment_qty       = NULL;
                $fulfill_item_id->save();


                if ($fulfill_item_id->fulfillment_berat == 0 && $fulfill_item_id->fulfillment_qty == 0) {
                    $fulfill_item_id->status                = NULL;
                    $fulfill_item_id->save();
                }


                
            // LOG DELETE PEMENUHAN

            $log                        =   new Adminedit ;
            $log->user_id               =   Auth::user()->id ;
            $log->table_name            =   'order_items' ;
            $log->table_id              =   $pemenuhan->order_item_id ;
            $log->type                  =   'delete' ;
            $log->activity              =   'delete_bb' ;
            $log->content               =   'Data Bahan Baku';
            $log->data                  =   json_encode([
                    'header' => $fulfill_item_id,
                    'list' => $pemenuhan
            ]) ;

            if (!$log->save()) {
                DB::rollBack();
                return back()->with('status', 2)->with('message', 'Proses simpan log gagal');
            }



            } else {
                DB::rollBack();
                $data['status'] =   400;
                $data['msg']    =   'Terdapat error pada Order Item';
                return $data;
            }
    
            try {
                Chiller::recalculate_chiller($chiller->id);
            } catch (\Throwable $th) {
                
            }
            
            try {
                Chiller::recalculate_chiller($chil->id);
            } catch (\Throwable $th) {
                
            }


            DB::commit();

            $data['status'] =   200;
            $data['msg']    =   'Data telah dihapus';
            return $data;

        } else {
            DB::rollBack();
            $data['status'] =   400;
            $data['msg']    =   'Terdapat error, data bahan baku tidak ditemukan';
            $data['data']   =  $pemenuhan;
            $data['id']     =  $request->id;
            return $data;

        }
    }

    public function simpanAlokasi(Request $request)
    {

        DB::beginTransaction();

        if ($request->berat == null) {
            $data['status'] =   400;
            $data['msg']    =   'Isi Bahan Baku';
            return $data;
        }

        $order_item_id  = $request->order_item_id;
        $order_item     = OrderItem::find($order_item_id);
        $total_berat    = 0;
        $total_item     = 0;

        for ($x = 0; $x < COUNT($request->x_code); $x++) {
            
            if ($request->berat[$x]) {

                $proses_ambil = $request->lokasi_asal;

                if ($proses_ambil == "chillerfg" or $proses_ambil == 'sampingan') {

                    $chiller                    = Chiller::find($request->x_code[$x]);
                    // $sisaQtyChiller             = Chiller::ambilsisachiller($chiller->id,'qty_item','qty','bb_item');
                    $sisaBeratChiller           = Chiller::ambilsisachiller($chiller->id,'berat_item','berat','bb_berat');
                    $convertSisaBerat           = number_format((float)$sisaBeratChiller, 2, '.', '');
                    // if($kategori != 'boneless'){
                    //     if ($request->qty[$x] > $sisaQtyChiller) {
                    //         DB::rollBack() ;
                    //         return back()->with('status', 2)->with('message', 'Proses gagal, bahan baku mungkin sudah digunakan regu lain')->with('tabs', $request->type_input);
                    //     }
                    // }
    
                    if ($request->berat[$x] > $convertSisaBerat) {
                        DB::rollBack() ;
                        return back()->with('status', 2)->with('message', 'Proses gagal, bahan baku mungkin sudah digunakan regu lain');
                    }
                    
                    if ($order_item) {
                        if ($chiller->item_name != $order_item->nama_detail) {
                            DB::rollBack() ;
                            $data['status'] =   400 ;
                            $data['msg']    =   'Terjadi kesalahan, Item Order berbeda dengan fulfill' ;
                            return $data ;
                        }

                    } else {
                        DB::rollBack() ;
                        $data['status'] =   400 ;
                        $data['msg']    =   'Terjadi kesalahan, Item Order tidak ditemukan' ;
                        return $data ;
                    }

                    $orderBahanBaku                     =   new Bahanbaku;
                    $orderBahanBaku->chiller_out        =   $chiller->id;
                    $orderBahanBaku->order_id           =   $request->order_id;
                    $orderBahanBaku->nama               =   $chiller->item_name;
                    $orderBahanBaku->proses_ambil       =   $proses_ambil;
                    $orderBahanBaku->order_item_id      =   $request->order_item_id;
                    $orderBahanBaku->bb_item            =   $request->qty[$x];
                    $orderBahanBaku->bb_berat           =   $request->berat[$x];
                    $orderBahanBaku->status             =   1;
                    if (!$orderBahanBaku->save()) {
                        DB::rollBack();
                        $data['status'] =   400;
                        $data['msg']    =   'Terjadi kesalahan proses order';
                        return $data;
                    }


                    $chiler                     =   new Chiller;
                    $chiler->table_name         =   'order_bahanbaku';
                    $chiler->table_id           =   $orderBahanBaku->id;
                    $chiler->asal_tujuan        =   'siapkirim';
                    $chiler->item_id            =   $orderBahanBaku->orderitem->item_id;
                    $chiler->item_name          =   $orderBahanBaku->orderitem->nama_detail;
                    $chiler->qty_item           =   $orderBahanBaku->bb_item;
                    $chiler->berat_item         =   $orderBahanBaku->bb_berat;
                    $chiler->jenis              =   'keluar';
                    $chiler->type               =   'alokasi-order';
                    $chiler->kategori           =   $chiller->kategori;
                    $chiler->tanggal_potong     =   Carbon::now();
                    $chiler->tanggal_produksi   =   Carbon::now();
                    $chiler->status             =   4;

                    if (!$chiler->save()) {
                        DB::rollBack();
                        $data['status'] =   400;
                        $data['msg']    =   'Terjadi kesalahan proses order';
                        return $data;
                    }


                    $orderBahanBaku->chiller_id = $chiler->id;
                    $orderBahanBaku->save();

                    $chiller->stock_berat    =   $chiller->stock_berat - $chiler->berat_item;
                    $chiller->stock_item     =   $chiller->stock_item - $chiler->qty_item;


                    if (!$chiller->save()) {
                        DB::rollBack();
                        $data['status'] =   400;
                        $data['msg']    =   'Terjadi kesalahan proses order';
                        return $data;
                    }

                    $total_item      = $total_item + $orderBahanBaku->bb_item;
                    $total_berat     = $total_berat + $orderBahanBaku->bb_berat;
                } elseif ($proses_ambil == "frozen") {
                    $storage                        =   Product_gudang::find($request->x_code[$x]);

                    $sisaBeratGudang                = Product_gudang::ambilsisaproductgudang($storage->id,'berat_awal','berat','bb_berat');
                    $convertSisaBerat               = number_format((float)$sisaBeratGudang, 2, '.', '');
                    // dd(number_format($sisaBeratGudang,2));
                    // if($kategori != 'boneless'){
                    //     if ($request->qty[$x] > $sisaQtyChiller) {
                    //         DB::rollBack() ;
                    //         return back()->with('status', 2)->with('message', 'Proses gagal, bahan baku mungkin sudah digunakan regu lain')->with('tabs', $request->type_input);
                    //     }
                    // }
    
                    if ($request->berat[$x] > $convertSisaBerat) {
                        // DB::rollBack() ;
                        $data['status'] =   400 ;
                        $data['msg']    =   'Pengambilan Lebih Besar dari Stock' ;
                        return $data ;
                        // DB::rollBack() ;
                        // return back()->with('status', 2)->with('message', 'Proses gagal, Berat bahan baku kurang');
                    }

                    if ($request->berat[$x] == 0) {
                        DB::rollBack();
                        $data['status'] = 400;
                        $data['msg'] = 'Pengambilan tidak boleh 0';
                        return $data;
                    }
                    
                    if ($order_item) {
                        if ($storage->nama != $order_item->nama_detail) {
                            DB::rollBack() ;
                            $data['status'] =   400 ;
                            $data['msg']    =   'Terjadi kesalahan, Item Order berbeda dengan fulfill' ;
                            return $data ;
                        }

                    } else {
                        DB::rollBack() ;
                        $data['status'] =   400 ;
                        $data['msg']    =   'Terjadi kesalahan, Item Order tidak ditemukan' ;
                        return $data ;
                    }

                    $orderBahanBaku                     =   new Bahanbaku;
                    $orderBahanBaku->chiller_out        =   $storage->id;
                    $orderBahanBaku->order_id           =   $request->order_id;
                    $orderBahanBaku->nama               =   $storage->productitems->nama;
                    $orderBahanBaku->proses_ambil       =   $proses_ambil;
                    $orderBahanBaku->order_item_id      =   $request->order_item_id;
                    $orderBahanBaku->bb_item            =   $request->qty[$x];
                    $orderBahanBaku->bb_berat           =   $request->berat[$x];
                    $orderBahanBaku->status             =   1;
                    if (!$orderBahanBaku->save()) {
                        DB::rollBack();
                        $data['status'] =   400;
                        $data['msg']    =   'Terjadi kesalahan proses order';
                        return $data;
                    }

                    $order     = Order::find($order_item->order_id);

                    // $gdg_baru               =   new Product_gudang;
                    // $gdg_baru->table_name   =   $storage->table_name;
                    // $gdg_baru->table_id     =   $storage->table_id;
                    // $gdg_baru->product_id   =   $storage->product_id;
                    // $gdg_baru->nama         =   $storage->nama ?? "";
                    // $gdg_baru->qty_awal     =   $request->bb_item;
                    // $gdg_baru->berat_awal   =   $request->bb_berat;
                    // $gdg_baru->qty          =   $request->bb_item;
                    // $gdg_baru->berat        =   $request->bb_berat;
                    // $gdg_baru->packaging    =   $storage->packaging;
                    // $gdg_baru->order_id     =   $order->id;
                    // $gdg_baru->order_item_id     =   $order_item_id;
                    // $gdg_baru->palete       =   $storage->palete;
                    // $gdg_baru->expired      =   $storage->expired;
                    // $gdg_baru->type         =   "siapkirim";
                    // $gdg_baru->stock_type   =   $storage->stock_type;
                    // $gdg_baru->jenis_trans  =   'keluar';
                    // $gdg_baru->sub_item     =    $storage->sub_item;
                    // $gdg_baru->status       =   4;
                    // $gdg_baru->save();

                    // if (!$gdg_baru->save()) {
                    //     DB::rollBack();
                    //     $data['status'] =   400;
                    //     $data['msg']    =   'Terjadi kesalahan proses order';
                    //     return $data;
                    // }


                    // $orderBahanBaku->chiller_id = $gdg_baru->id;
                    $orderBahanBaku->save();

                    $storage->berat    =   $storage->berat - $request->bb_berat;
                    $storage->qty     =   $storage->qty - $request->bb_item;

                    if (!$storage->save()) {
                        DB::rollBack();
                        $data['status'] =   400;
                        $data['msg']    =   'Terjadi kesalahan proses order';
                        return $data;
                    }

                    $total_item      = $total_item + $orderBahanBaku->bb_item;
                    $total_berat     = $total_berat + $orderBahanBaku->bb_berat;
                }
            }
        }

        $order_item->fulfillment_berat  =   OrderItem::recalculate_fulfill_berat($order_item->id);
        $order_item->fulfillment_qty    =   OrderItem::recalculate_fulfill_qty($order_item->id);
        $order_item->status             =   3;

        if (!$order_item->save()) {
            DB::rollBack();
            $data['status'] =   400;
            $data['msg']    =   'Terjadi kesalahan proses order';
            return $data;
        }

        DB::commit();
        
        try {
            Chiller::recalculate_chiller($chiller->id);
        } catch (\Throwable $th) {
            
        }

        $data['status'] =   200;
        $data['msg']    =   'Data telah diproses';
        return $data;
    }

    public function editSoOrderBahanBaku(Request $request)
    {
        DB::beginTransaction();
        // data bb awal
        $order_bb_awal          =   Bahanbaku::find($request->order_bb_id);
        // data After Edit
        $order_bb_awal_original =   Bahanbaku::find($request->order_bb_id);

        $dataOrder              =   Order::where('id', $order_bb_awal->order_id)->first();
        $order_bb_awal->no_do   =   $request->no_do;
        $chiller_awal           =   Chiller::find($order_bb_awal->chiller_out);

        // if($order_bb_awal->proses_ambil == 'frozen'){
        //      // DATA BARU;
        //      $gudang             = Product_gudang::find($order_bb_awal->chiller_out) ;
        //      $sisaQtyGudang      = Product_gudang::ambilsisaproductgudang($gudang->id,'qty_awal','qty','bb_item',$request->order_bb_id);
        //      $sisaBeratGudang    = Product_gudang::ambilsisaproductgudang($gudang->id,'berat_awal','berat','bb_berat',$request->order_bb_id);
        // }else{
        //     $sisaQtyChiller      = Chiller::ambilsisachiller($chiller_awal->id,'qty_item','qty','bb_item',$request->order_bb_id);
        //     $sisaBeratChiller    = Chiller::ambilsisachiller($chiller_awal->id,'berat_item','berat','bb_berat',$request->order_bb_id);
        // }

        if ($order_bb_awal->proses_ambil == 'frozen') {
            
            // DATA BARU;
            $gudang             = Product_gudang::find($order_bb_awal->chiller_out) ;
            $sisaQtyGudang      = Product_gudang::ambilsisaproductgudang($gudang->id,'qty_awal','qty','bb_item',$request->order_bb_id);
            $sisaBeratGudang    = Product_gudang::ambilsisaproductgudang($gudang->id,'berat_awal','berat','bb_berat',$request->order_bb_id);
            $convertSisaBerat   = number_format((float)$sisaBeratGudang, 2, '.', '');
           
            if ($request->bb_item > $sisaQtyGudang) {
                DB::rollBack() ;
                return back()->with('status', 2)->with('message', 'Proses gagal, Qty bahan baku kurang ');
            }

            if ($request->bb_berat > $convertSisaBerat) {
                DB::rollBack() ;
                return back()->with('status', 2)->with('message', 'Proses gagal, Berat bahan baku kurang');
            }

            if ($dataOrder) {
                if ($dataOrder->status == '10') {
                    $gudang->qty        =   ($gudang->qty + $order_bb_awal->bb_item) - $request->bb_item ;
                    $gudang->berat      =   ($gudang->berat + $order_bb_awal->bb_berat) - $request->bb_berat ;

                    // DATA PERUBAHAN 
                    $dataBBdigudang = Product_gudang::where('order_bb_id', $order_bb_awal->id)->first();
                    if ($dataBBdigudang) {
                        $dataBBdigudang->qty        = $request->bb_item ;
                        $dataBBdigudang->berat      = $request->bb_berat ;
                        $dataBBdigudang->qty_awal   = $request->bb_item ;
                        $dataBBdigudang->berat_awal =  $request->bb_berat ;
                        if (!$dataBBdigudang->save()) {
                            DB::rollBack();
                            return back()->with('status', 1)->with('message', 'Proses gagal');
                        }
                    }
                }
            }

            if (!$gudang->save()) {
                DB::rollBack();
                return back()->with('status', 1)->with('message', 'Proses gagal');
            }
        }
        else {
            $sisaQtyChiller      = Chiller::ambilsisachiller($chiller_awal->id,'qty_item','qty','bb_item',$request->order_bb_id);
            $sisaBeratChiller    = Chiller::ambilsisachiller($chiller_awal->id,'berat_item','berat','bb_berat',$request->order_bb_id);
            $convertSisaBerat    = number_format((float)$sisaBeratChiller, 2, '.', '');

            if ($request->bb_item > $sisaQtyChiller) {
                DB::rollBack() ;
                return back()->with('status', 2)->with('message', 'Proses gagal, Qty bahan baku kurang ');
            }
    
            if ($request->bb_berat > $convertSisaBerat) {
                DB::rollBack() ;
                return back()->with('status', 2)->with('message', 'Proses gagal, Berat bahan baku kurang');
            }
            if ($order_bb_awal->chiller_alokasi) {
                // Jika sudah disimpan
                if($chiller_awal){
                    $chiller_awal->stock_item   =   ($chiller_awal->stock_item + $order_bb_awal->bb_item) - $request->bb_item ;
                    $chiller_awal->stock_berat  =   ($chiller_awal->stock_berat + $order_bb_awal->bb_berat) - $request->bb_berat ;
                    if (!$chiller_awal->save()) {
                        DB::rollBack();
                        return back()->with('status', 1)->with('message', 'Proses gagal');
                    }
                }
    
                $chiller_keluar             =   Chiller::find($order_bb_awal->chiller_alokasi);
                if($chiller_keluar){
                    $chiller_keluar->qty_item   =   $request->bb_item;
                    $chiller_keluar->berat_item =   $request->bb_berat;
                    if (!$chiller_keluar->save()) {
                        DB::rollBack();
                        return back()->with('status', 1)->with('message', 'Proses gagal');
                    }
                }
            }   
        }

        $order_bb_awal->bb_item     =   $request->bb_item;
        $order_bb_awal->bb_berat    =   $request->bb_berat;
        if (!$order_bb_awal->save()) {
            DB::rollBack();
            return back()->with('status', 1)->with('message', 'Proses gagal');
        }

        // Recalculate fulfill item
        $order_item                         =   OrderItem::find($order_bb_awal->order_item_id);
        if($order_item){
            $order_item->fulfillment_berat  =   OrderItem::recalculate_fulfill_berat($order_item->id);
            $order_item->fulfillment_qty    =   OrderItem::recalculate_fulfill_qty($order_item->id);
            if (!$order_item->save()) {
                DB::rollBack();
                return back()->with('status', 1)->with('message', 'Proses gagal');
            }
        }

        // if($chiller_awal){
        // }
        
        // Log activity
        // Item awal/original
        $ceklog = Adminedit::where('table_id', $order_bb_awal->id)->where('table_name', 'order_bahan_baku')->where('type', 'edit')->count();
        if($ceklog < 1){
            $log                        =   new Adminedit ;
            $log->user_id               =   Auth::user()->id ;
            $log->table_name            =   'order_bahan_baku' ;
            $log->table_id              =   $order_bb_awal->id ;
            $log->type                  =   'edit' ;
            $log->activity              =   'sales_order' ;
            $log->content               =   'Data Awal (Original)';
            $log->data                  =   json_encode([
                    'header' => $order_bb_awal_original,
                    'list' => []
            ]) ;
            if (!$log->save()) {
                DB::rollBack();
                return back()->with('status', 2)->with('message', 'Proses simpan log gagal');
            }

        }

        $log                 =   new Adminedit ;
        $log->user_id        =   Auth::user()->id ;
        $log->table_name     =   'order_bahan_baku' ;
        $log->table_id       =   $order_bb_awal->id ;
        $log->type           =   'edit' ;
        $log->activity       =   'sales_order' ;
        $log->content        =   'Data Setelah Edit';
        $log->data           =   json_encode([
                'header' => $order_bb_awal,
                'list' => []
        ]) ;
        if (!$log->save()) {
            DB::rollBack();
            return back()->with('status', 2)->with('message', 'Proses simpan log gagal');
        }
            
        $netsuite = Netsuite::find($order_bb_awal->netsuite_id);

        if($netsuite){
            if($netsuite->status=="5"){

                try {
                    //code...
                    $json_data                                      = json_decode($netsuite->data_content);
                    $json_data->data[0]->line[0]->qty_to_transfer   = $order_bb_awal->bb_berat;
                    $final_json                                     = json_encode($json_data);
                    $netsuite->data_content                         = $final_json;
                    $netsuite->save();

                } catch (\Throwable $th) {
                    //throw $th;
                }

            }
        }

        DB::commit();

        try {
            Chiller::recalculate_chiller($chiller_awal->id);
        } catch (\Throwable $th) {
            
        }

        return back()->with('status', 1)->with('message', 'Berhasil Diedit ya');

    }

    public function batalkan(Request $request, $id)
    {

        DB::beginTransaction();
        $order_item                     =   OrderItem::find($id);
        $order_item->fulfillment_berat  =   null;
        $order_item->fulfillment_qty    =   null;
        $order_item->status             =   null;

        $item       =   Bahanbaku::where('order_item_id', $id)->get();

        // dd($item);
        foreach($item as $it){

            if ($it->proses_ambil == 'frozen' or $it->proses_ambil == null) {
                $gudang         =   Product_gudang::find($it->chiller_out);
                $gudang->berat  =   $gudang->berat + $it->bb_berat;
                $gudang->qty    =   $gudang->qty + $it->bb_item;
                $gudang->save();
            } else {

                $chiler              =   Chiller::find($it->chiller_out);

                if($chiler){
                    $chiler->stock_berat =   $chiler->stock_berat + $it->bb_berat;
                    $chiler->stock_item  =   $chiler->stock_item + $it->bb_item;
                    $chiler->save();
                }
            }    

            $log                 =   new Adminedit ;
            $log->user_id        =   Auth::user()->id ;
            $log->table_name     =   'order_bahan_baku' ;
            $log->table_id       =   $it->order_item_id ;
            $log->type           =   'reset' ;
            $log->activity       =   'sales_order_reset' ;
            $log->content        =   'Data Setelah Edit';
            $log->data           =   json_encode([
                    'header' => $item,
                    'list' => []
            ]) ;
            if (!$log->save()) {
                DB::rollBack();
                return back()->with('status', 2)->with('message', 'Proses simpan log gagal');
            }

            $netsuite = Netsuite::find($it->netsuite_id);

            if($netsuite){
                if($netsuite->status=="5"){
                    $netsuite->delete();
                }elseif($netsuite->status=="1"){
                    DB::rollBack();
                    return back()->with('status', 2)->with('message', 'Reset gagal, TI sudah terkirim ke netsuite, mohon hapus TI terlebih dahulu');
                }
            }

            $it->delete();
        }

        $order_item->save();
        DB::commit();

        try {
            Chiller::recalculate_chiller($chiler->id);
        } catch (\Throwable $th) {
            
        }

        return back()->with('status', 1)->with('message', 'Berhasil Dibatalkan');
    }

    public function soHistory(Request $request)
    {
        if ($request->key == "riwayat_edit_sodo") {
            $orderid = $request->id;
            return view('admin.pages.editso.history.history-sodo',compact('orderid'));

        } else 
        if ($request->key == "riwayat_reset") {
            $table_id   = $request->id;
            $log_reset  = Adminedit::where('table_id',$table_id)->where('table_name','order_bahan_baku')->where('type', 'reset')->get();
            // dd($log_reset);
            return view('admin.pages.editso.history.history-reset',compact('table_id'));

        } else if ($request->key == 'historyDeleteBB') {
            $table_id   = $request->id;
            $log_reset  = Adminedit::where('table_id', $table_id)->where('table_name','order_items')->where('type', 'delete')->get();
            return view('admin.pages.editso.history.historyDeleteBB',compact('table_id'));
        }
        
    }

    public function kirimti(Request $request){

        DB::beginTransaction();

        $order_id   =   $request->order_id;
        $order      =   Order::find($order_id);

        $order_item = OrderItem::where('order_id', $order_id)->get();

            foreach($order_item as $oi):
                OrderItem::fulfillItem($oi->id);
            endforeach;

        DB::commit();

        return back()->with('status', 1)->with('message', 'TI Ditransfer');

    }

    public function kirimfulfill(Request $request){

        DB::beginTransaction();

        
        $order_id   =   $request->order_id;
        $order      =   Order::find($order_id);

        $order_item = OrderItem::where('order_id', $order_id)->get();
        
        if ($request->key == 'kirimUlangCreditLimit') {
    
            // DELETE ITEM FULFILL CL YANG ERROR
            $getDataKirimanCL       =   Netsuite::where('tabel', 'orders')->where('tabel_id', $order_id)->where('failed', '!=', NULL)->first();
            // dd($getDataKirimanCL);


            if ($getDataKirimanCL) {

                // DELETE TI YANG BELUM DIKIRIM
                $getDataKirimanCL->delete();
                $getDataTI          = Netsuite::where('document_code', $order->no_so)->get();

                foreach($getDataTI as $ns):

                    // $getDataTI = Netsuite::where('order_item_id', $ns->id)->where('order_id', $order_id)->first();
                    if ($ns) {
                        $ns->delete();
                    }

                endforeach;


                // NS baru
                foreach($order_item as $oi):
                    OrderItem::fulfillUlangCreditLimit($oi->id);
                endforeach;

                $net_fulfill = Netsuite::item_fulfill_creditlimit('orders', $request->order_id, 'itemfulfill', null, null);


            } else {

                return back()->with('status', 2)->with('message', 'Gagal, data kiriman tidak ada yang perlu dikirim ulang');

            }

            

        } else {
    
                foreach($order_item as $oi):
                    OrderItem::fulfillItem($oi->id);
                endforeach;
    
    
                
                $net_fulfill = Netsuite::item_fulfill_tambahan('orders', $request->order_id, 'itemfulfill', null, null);
        }
            


        DB::commit();

        return back()->with('status', 1)->with('message', 'Fulfill tambahan telah terbentuk');

    }
}
