<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Abf;
use App\Models\AppKey;
use App\Models\Chiller;
use App\Models\Gudang;
use App\Models\Item;
use App\Models\Netsuite;
use App\Models\Production;
use App\Models\PurchaseItem;
use App\Models\Purchasing;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TerimaNonKarkas extends Controller
{

    public $nama_gudang_expedisi;
    public $nama_gudang_bb;
    public $nama_gudang_fg;
    public $nama_gudang_abf;

    public function __construct(Request $request)
    {
        $this->nama_gudang_expedisi     = env('NET_SUBSIDIARY', 'CGL')." - Storage Expedisi";
        $this->nama_gudang_bb           = env('NET_SUBSIDIARY', 'CGL')." - Chiller Bahan Baku";
        $this->nama_gudang_fg           = env('NET_SUBSIDIARY', 'CGL')." - Chiller Finished Good";
        $this->nama_gudang_abf          = env('NET_SUBSIDIARY', 'CGL')." - Storage ABF";
    }

    public function index(Request $request)
    {
        $tanggalawal    =   $request->tanggalawal ?? date('Y-m-d') ;
        $tanggalakhir   =   $request->tanggalakhir ?? date('Y-m-d') ;
        $nomor_po       =   $request->nomor_po ?? '';
        
        $data       =   Production::whereIn('purchasing_id', Purchasing::select('id')
        // ->whereDate('tanggal_potong', $tanggal)
        ->whereBetween(DB::raw('DATE(tanggal_potong)'), [$tanggalawal, $tanggalakhir])
        ->whereIn('type_po', ['PO Karkas', 'PO non Karkas']))
        ->whereIn('ppic_tujuan', ['abf','chiller'])
        ->whereIn('ppic_acc', [2,3])
        ->where('lpah_status', 1)
        ->where('evis_status', 1)
        ->where('grading_status', 1)
        ->where('no_po','LIKE','%'.$nomor_po.'%')
        ->orderByRaw('prod_tanggal_potong ASC, no_urut ASC')
        ->get();

        $datasuccess       =   Production::whereIn('purchasing_id', Purchasing::select('id')
        // ->whereDate('tanggal_potong', $tanggal)
        ->whereBetween(DB::raw('DATE(tanggal_potong)'), [$tanggalawal, $tanggalakhir])
        ->whereIn('type_po', ['PO Karkas', 'PO non Karkas']))
        ->whereIn('ppic_tujuan', ['abf','chiller'])
        ->whereIn('ppic_acc', [3])
        ->where('lpah_status', 1)
        ->where('evis_status', 1)
        ->where('grading_status', 1)
        ->where('no_po','LIKE','%'.$nomor_po.'%')
        ->orderByRaw('prod_tanggal_potong ASC, no_urut ASC')
        ->get();

        $dataprocess       =   Production::whereIn('purchasing_id', Purchasing::select('id')
        // ->whereDate('tanggal_potong', $tanggal)
        ->whereBetween(DB::raw('DATE(tanggal_potong)'), [$tanggalawal, $tanggalakhir])
        ->whereIn('type_po', ['PO Karkas', 'PO non Karkas']))
        ->whereIn('ppic_tujuan', ['abf','chiller'])
        ->whereIn('ppic_acc', [2])
        ->where('lpah_status', 1)
        ->where('evis_status', 1)
        ->where('grading_status', 1)
        ->where('no_po','LIKE','%'.$nomor_po.'%')
        ->orderByRaw('prod_tanggal_potong ASC, no_urut ASC')
        ->get();

        $counttransaction           = $data->count() ?? '0';
        $countsuccesstransaction    = $datasuccess->count() ?? '0';
        $countprocesstransaction    = $dataprocess->count() ?? '0';
        
        if($request->view == 'non_lb'){
            $purchase   =   Purchasing::whereBetween(DB::raw('DATE(tanggal_potong)'), [$tanggalawal, $tanggalakhir])
            ->whereNotIn('type_po',['PO LB', 'PO Maklon'])
            ->where('no_po','LIKE','%'.$nomor_po.'%')
            ->paginate(5);

            return view('admin.pages.non_karkas.non_lb_view', compact('tanggalawal', 'purchase','tanggalakhir'));
        } elseif($request->view == 'timbang'){
            return view('admin.pages.non_karkas.timbang', compact('tanggalawal', 'tanggalakhir', 'data','counttransaction','countsuccesstransaction','countprocesstransaction')) ;
        } else {
        return view('admin.pages.non_karkas.index', compact('tanggalawal', 'tanggalakhir', 'data')) ;
        }
        // $tanggal    =   $request->tanggal ?? date('Y-m-d') ;        
    }


    public function show(Request $request, $id)
    {
        $data   =   Production::whereIn('purchasing_id', Purchasing::select('id')
                        ->whereIn('type_po', ['PO Karkas', 'PO non Karkas', 'PO Evis'])
                    )
                    ->whereHas('prodpur.purchasing_item', function($query) {
                        // $query->groupBy('purchase_item.item_po', 'purchase_item.description');
                    })
                    ->where('id', $id)
                    ->whereIn('ppic_acc', [2,3])
                    ->first() ;

        if ($data) {
            if ($request->key == 'input_data') {
                return view('admin.pages.non_karkas.input_data', compact('data')) ;
            } else {
                return view('admin.pages.non_karkas.show', compact('data')) ;
            }

        }

        return redirect()->route('nonkarkas.index') ;
    }


    public function store(Request $request)
    {
        // dd($request->all());

        $array_qty      =   json_decode(json_encode($request->qty), TRUE);
        $array_berat    =   json_decode(json_encode($request->berat), TRUE);
        $item           =   json_decode(json_encode($request->item), TRUE);
        $arah7an        =   json_decode(json_encode($request->arah7an), TRUE);
        $tanggal        =   $request->tanggal;

        $data       =   Production::find($request->production_id) ;


        // if (!$request->item) {

        //     $countDataLooping = '';
        //     if (count($data->prodpur->purchasing_item->where('status', 1)) == 0) {
        //         $countDataLooping = $data->prodpur->purchasing_item->where('status', NULL);
        //     } else {
        //         $countDataLooping = $data->prodpur->purchasing_item->where('status', 1);
        //     }

        //     foreach ($countDataLooping as $itemAwal) {

        //         // $findItemLama                           = PurchaseItem::where('purchasing_id', $itemAwal->purchasing_id)->where('status', NULL)->get();

        //         // foreach ($findItemLama as $saveStatus) {
        //         //     $saveStatus->status                 = 1;
        //         //     $saveStatus->save();
        //         // }

        //         $newPurchaseItem                        = new PurchaseItem;
        //         $newPurchaseItem->item_po               = $itemAwal->item_po;
        //         $newPurchaseItem->internal_id_po        = $itemAwal->internal_id_po;
        //         $newPurchaseItem->purchasing_id         = $itemAwal->purchasing_id;
        //         $newPurchaseItem->harga                 = $itemAwal->harga;
        //         $newPurchaseItem->ukuran_ayam           = $itemAwal->ukuran_ayam;
        //         $newPurchaseItem->jumlah_do             = $itemAwal->jumlah_do;
        //         $newPurchaseItem->jenis_ayam            = $itemAwal->jenis_ayam;
        //         $newPurchaseItem->berat_ayam            = $itemAwal->berat_ayam;
        //         $newPurchaseItem->jumlah_ayam           = $itemAwal->jumlah_ayam;
        //         $newPurchaseItem->description           = $itemAwal->description;
        //         $newPurchaseItem->keterangan            = $itemAwal->keterangan;
        //         $newPurchaseItem->save();

        //     }

        //     $result['status']   =   200 ;
        //     return $result ; 

        // } else if ((!$array_berat)) {
        //     $result['status']   =   400 ;
        //     $result['msg']      =   'Lengkapi data' ;
        //     return $result ;
        // }


        DB::beginTransaction() ;

        $item_receipt_global = [];

        // Perunahan tanggal potong sesuai input user, bukan dari PO

        if($tanggal){
            $data->prod_tanggal_potong = date('Y-m-d', strtotime($tanggal));
            $data->save();
        }

        $cekStatusReceipt                   =   PurchaseItem::where('purchasing_id', $data->purchasing_id)->orderBy('status', 'desc')->first();

        foreach($item as $no => $r):

            if ($array_berat[$no] != NULL) {
                $berat  =   $array_berat[$no];
                $qty    =   $array_qty[$no];

                
                $purchase_item                      =   PurchaseItem::find($r);
                // $purchase_item->terima_berat_item   =   $berat;
                // $purchase_item->terima_jumlah_item  =   $qty;
                // $purchase_item->status              =   $cekStatusReceipt->status == NULL ? 1 : $cekStatusReceipt->status + 1;


                $newPurchaseItem                        = new PurchaseItem;
                $newPurchaseItem->item_po               = $purchase_item->item_po;
                $newPurchaseItem->internal_id_po        = $purchase_item->internal_id_po;
                $newPurchaseItem->purchasing_id         = $purchase_item->purchasing_id;
                $newPurchaseItem->harga                 = $purchase_item->harga;
                $newPurchaseItem->ukuran_ayam           = $purchase_item->ukuran_ayam;
                $newPurchaseItem->jumlah_do             = $purchase_item->jumlah_do;
                $newPurchaseItem->jenis_ayam            = $purchase_item->jenis_ayam;
                $newPurchaseItem->berat_ayam            = $purchase_item->berat_ayam;
                $newPurchaseItem->jumlah_ayam           = $purchase_item->jumlah_ayam;
                $newPurchaseItem->description           = $purchase_item->description;
                $newPurchaseItem->keterangan            = $purchase_item->keterangan;
                $newPurchaseItem->terima_berat_item     = $berat;
                $newPurchaseItem->terima_jumlah_item    = $qty;
                $newPurchaseItem->status                = $cekStatusReceipt->status + 1;
                $newPurchaseItem->save();
                

                $item   =   Item::where('sku', $purchase_item->item_po)->first();

                if ($request->tujuan == 'abf') {
                    $abf                    =   new Abf ;
                    $abf->table_name        =   'purchase_item';
                    $abf->table_id          =   $newPurchaseItem->id;
                    $abf->asal_tujuan       =   'order_karkas_frozen';
                    $abf->item_id           =   $item->id;
                    $abf->tanggal_masuk     =   date('Y-m-d', strtotime($tanggal));
                    $abf->item_id_lama      =   Item::where('nama', str_replace(" FROZEN", "", $item->nama))->first()->id ?? NULL;
                    $abf->item_name         =   $item->nama;
                    $abf->packaging         =   NULL;
                    $abf->qty_awal          =   $qty;
                    $abf->berat_awal        =   $berat;
                    $abf->qty_item          =   $qty;
                    $abf->berat_item        =   $berat;
                    $abf->jenis             =   'masuk';
                    $abf->type              =   'po-frozen';
                    $abf->status            =   1;
                    if (!$abf->save()) {
                        DB::rollBack();
                        $result['status']   =   400 ;
                        $result['msg']      =   'Proses Gagal' ;
                        return $result ;
                    }
                    $purchase_item->tujuan    =   'abf' ;
                    $newPurchaseItem->tujuan  =   'abf' ;

                }

                if ($request->tujuan == 'chiller') {
                    $chiler                     =   new Chiller;
                    $chiler->production_id      =   $data->id;
                    $chiler->table_name         =   'production';
                    $chiler->table_id           =   $data->id;
                    $chiler->asal_tujuan        =   $arah7an[$no] . 'beli' ;
                    $chiler->type               =   'hasil-produksi';
                    $chiler->item_id            =   $item->id;
                    $chiler->item_name          =   $item->nama;
                    $chiler->qty_item           =   $qty;
                    $chiler->tanggal_potong     =   date('Y-m-d', strtotime($tanggal));
                    $chiler->berat_item         =   $berat;
                    $chiler->tanggal_produksi   =   date('Y-m-d', strtotime($tanggal)) ;
                    $chiler->stock_item         =   $qty;
                    $chiler->stock_berat        =   $berat;
                    $chiler->status             =   2;
                    $chiler->key                =   AppKey::generate();
                    $chiler->jenis              =   'masuk';

                    if (!$chiler->save()) {
                        DB::rollBack() ;
                        $data['status'] =   400 ;
                        $data['msg']    =   'Proses Gagal' ;
                        return $data ;
                    }
                    $purchase_item->tujuan      =   $arah7an[$no];
                    $newPurchaseItem->tujuan    =   $arah7an[$no];

                }


                if ($request->tujuan == 'evis') {

                    $item   =   Item::where('sku', $purchase_item->item_po)
                                ->first() ;

                    $chiler                     =   new Chiller;
                    $chiler->production_id      =   $data->id;
                    $chiler->table_name         =   'production';
                    $chiler->table_id           =   $data->id;
                    $chiler->asal_tujuan        =   'baru';
                    $chiler->type               =   'hasil-produksi';
                    $chiler->item_id            =   $item->id;
                    $chiler->item_name          =   $item->nama;
                    $chiler->qty_item           =   $qty;
                    $chiler->tanggal_potong     =   date('Y-m-d', strtotime($tanggal));
                    $chiler->berat_item         =   $berat;
                    $chiler->tanggal_produksi   =   date('Y-m-d', strtotime($tanggal));
                    $chiler->stock_item         =   $qty;
                    $chiler->stock_berat        =   $berat;
                    $chiler->status             =   2;
                    $chiler->key                =   AppKey::generate();
                    $chiler->jenis              =   'masuk';

                    if (!$chiler->save()) {
                        DB::rollBack() ;
                        $data['status'] =   400 ;
                        $data['msg']    =   'Proses Gagal' ;
                        return $data ;
                    }

                }


                if ($request->tujuan == 'abf') {

                    $item_receipt_global[] = array(
                        "line"                  =>  $purchase_item->internal_id_po,
                        "internal_id_item"      =>  (string)Item::item_sku($purchase_item->item_po)->netsuite_internal_id,
                        "item_code"             =>  $purchase_item->item_po,
                        "qty"                   =>  $berat,
                        "qty_in_ekor"           =>  $qty,
                        "internal_id_location"  =>  (string)Gudang::gudang_netid($this->nama_gudang_abf),
                        "gudang"                =>  $this->nama_gudang_abf,
                    );

                }else
                if ($request->tujuan == 'chiller') {

                    $item_receipt_global[] = array(
                        "line"                  =>  $purchase_item->internal_id_po,
                        "internal_id_item"      =>  (string)Item::item_sku($purchase_item->item_po)->netsuite_internal_id,
                        "item_code"             =>  $purchase_item->item_po,
                        "qty"                   =>  $berat,
                        "qty_in_ekor"           =>  $qty,
                        "internal_id_location"  =>  (string)Gudang::gudang_netid($this->nama_gudang_fg),
                        "gudang"                =>  $this->nama_gudang_fg,
                    );

                }
                // else
                // if ($request->tujuan == 'evis') {

                //     $item_receipt_global[] = array(
                //         "line"                  =>  $purchase_item->internal_id_po,
                //         "internal_id_item"      =>  (string)Item::item_sku($purchase_item->item_po)->netsuite_internal_id,
                //         "item_code"             =>  $purchase_item->item_po,
                //         "qty"                   =>  $berat,
                //         "qty_in_ekor"           =>  $qty,
                //         "internal_id_location"  =>  (string)Gudang::gudang_netid($this->nama_gudang_fg),
                //         "gudang"                =>  $this->nama_gudang_fg,
                //     );

                // }

                $purchase_item->save();
                $newPurchaseItem->save();
            } 

            
        endforeach;

        
        $netsuite                   =   new Netsuite ;
        $netsuite->record_type      =   "itemreceipt";
        $netsuite->trans_date       =   $data->prod_tanggal_potong;
        $netsuite->user_id          =   Auth::user()->id;
        $netsuite->tabel            =   "productions";
        $netsuite->paket_id         =   "0";
        $netsuite->tabel_id         =   $data->id;
        $netsuite->subsidiary_id    =   env("NET_SUBSIDIARY_ID", "6");
        $netsuite->subsidiary       =   env("NET_SUBSIDIARY", "CGL");


        if ($request->tujuan == 'abf') {

            $netsuite->label            =   "item_receipt_frozen";
            $netsuite->id_location      =   Gudang::gudang_netid($this->nama_gudang_abf);
            $netsuite->location         =   $this->nama_gudang_abf;

        } else

        if ($request->tujuan == 'chiller') {

            $netsuite->label            =   "item_receipt_chiller";
            $netsuite->id_location      =   Gudang::gudang_netid($this->nama_gudang_fg);
            $netsuite->location         =   $this->nama_gudang_fg;

        }
        // else

        // if ($request->tujuan == 'evis') {

        //     $netsuite->label            =   "item_receipt_evis";
        //     $netsuite->id_location      =   Gudang::gudang_netid($this->nama_gudang_fg);
        //     $netsuite->location         =   $this->nama_gudang_fg;

        // }

        if (!$netsuite->save()) {
            DB::rollBack();
            $result['status']   =   400 ;
            $result['msg']      =   'Proses Gagal' ;
            return $result ;
        }

        $net    =   Netsuite::find($netsuite->id);
        $arr   =   [
            "record_type"   =>  "itemreceipt",
            "data"          =>  [
                [
                    "appsid"            =>  env('NET_SUBSIDIARY', 'CGL')."-".$net->id,
                    "internal_id_po"    =>  $data->prodpur->internal_id_po ?? "",
                    "po_number"         =>  $data->no_po,
                    "date"              =>  date("d-M-Y", strtotime($data->prod_tanggal_potong)),
                    "memo"              =>  $netsuite->label,
                    "no_nota"           =>  $data->no_do,
                    "tanggal_nota"      =>  date("d-M-Y", strtotime($data->prod_tanggal_potong)),
                    "line"              =>  $item_receipt_global
                ]
            ]
        ];


        $net->script            =   '211';
        $net->deploy            =   '1';
        $net->data_content      =   json_encode($arr);
        $net->status            =   2;

        if (!$net->save()) {
            DB::rollBack();
            $result['status']   =   400 ;
            $result['msg']      =   'Proses Gagal' ;
            return $result ;
        }

        $data->ppic_acc     =   3 ;
        if (!$data->save()) {
            DB::rollBack();
            $result['status']   =   400 ;
            $result['msg']      =   'Proses Gagal' ;
            return $result ;
        }

        DB::commit() ;
    }

    public function update(Request $request)
    {
        $nonkarkas = Purchasing::find($request->idpurchase);
        
        if ($nonkarkas) {
            DB::beginTransaction();
            $purchaseitem = PurchaseItem::where('purchasing_id', $nonkarkas->id)->where('id',$request->idpurchaseitem)->first();
            $production = Production::where('purchasing_id',$nonkarkas->id)->first();
            $purchaseitem->terima_berat_item   = $request->berat;
            $purchaseitem->terima_jumlah_item  = $request->jumlah;

            if (!$purchaseitem->save()) {
                DB::rollBack();
                $result['status']   =   400;
                $result['msg']      =   "Proses gagal";
                return $result;
            }

            if ($update_chiler = Chiller::where('production_id',$production->id)->where('id',$request->idchiller)->first()) {
                $update_chiler->qty_item    = $request->jumlah;
                $update_chiler->berat_item  = $request->berat;
                $update_chiler->save();

                if (!$update_chiler->save()) {
                    DB::rollBack();
                    $result['status']   =   400;
                    $result['msg']      =   "Proses gagal";
                    return $result;
                }
            } else
            
            if ($update_abf = Abf::where('table_name','purchase_item')->where('table_id',$request->idpurchaseitem)->first()) {
                $update_abf->qty_awal = $request->jumlah;
                $update_abf->berat_awal = $request->berat;
                $update_abf->save();
                Abf::recalculate_abf($update_abf->id);
                if (!$update_abf->save()) {
                    DB::rollBack();
                    $result['status']   =   400;
                    $result['msg']      =   "Proses gagal";
                    return $result;
                }

            }
            DB::commit();
            try {
                Chiller::recalculate_chiller($update_chiler->id);
            } catch (\Throwable $th) {
                
            }
            
            return back()->with('status', 1)->with('message', 'Ubah Non LB berahsil');


        } 
        return back()->with('status', 2)->with('message', 'Ubah Non LB gagal');
    }
}
