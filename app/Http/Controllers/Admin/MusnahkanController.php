<?php

namespace App\Http\Controllers\Admin;

use App\Classes\Applib;
use App\Http\Controllers\Controller;
use App\Models\Chiller;
use App\Models\Gudang;
use App\Models\Item;
use App\Models\Musnahkan;
use App\Models\Musnahkantemp;
use App\Models\Netsuite;
use App\Models\Product_gudang;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MusnahkanController extends Controller
{

    public function index(Request $request)
    {
        if ($request->key == 'view') {
            $tanggal        =   $request->tanggal ?? date('Y-m-d');
            $tanggal_akhir  =   $request->tanggal_akhir ?? date('Y-m-d');
            $cari           =   $request->cari;
            $id             =   $request->id ?? '';

            // if ($request->id == 2) {
            //     $gudang     =   Chiller::where('jenis', 'masuk')
            //                     ->where('type', 'bahan-baku')
            //                     ->where('tanggal_produksi', [$tanggal, $tanggal_akhir])
            //                     ->where('item_name', 'LIKE', '%'.$cari.'%')
            //                     ->get();

            //     $pindah     =   Gudang::where('kategori', 'Production')
            //                     ->where('status', 1)
            //                     ->where('id', 13)
            //                     ->get();
            // } else

            // if ($request->id == 4) {
            //     $gudang     =   Chiller::where('jenis', 'masuk')
            //                     ->where('type', 'hasil-produksi')
            //                     ->where('tanggal_produksi', $tanggal)
            //                     ->get();

            //     $pindah     =   Gudang::where('kategori', 'Production')
            //                     ->where('status', 1)
            //                     ->where('id', 13)
            //                     ->get();
            // }else if($request->id === 'fg' || $request->id === 'bb'){
            //     $gudang     = Chiller::whereIn('status',[1,2])
            //                     ->whereBetween('tanggal_produksi', [$tanggal, $tanggal_akhir])
            //                     ->where('item_name', 'LIKE', '%'.$cari.'%')
            //                     ->where('jenis','masuk')
            //                     ->where(function ($q) use ($request){
            //                         if ($request->id == 'fg') {
            //                             $q->where('type','hasil-produksi');
            //                         }
            //                         if ($request->id == 'bb') {
            //                             $q->where('type','bahan-baku');
            //                         }
            //                     })
            //                     ->where('stock_berat','>','0')
            //                     ->orderBy('tanggal_produksi', 'ASC')
            //                     ->orderBy('item_name', 'ASC')
            //                     ->get();
            //     $pindah     =   Gudang::where('kategori', 'Production')
            //                     ->where('status', 1)
            //                     ->where('id', 13)
            //                     ->get();
            //                     // dd($data_bb_fg);
            // } else if ($request->id == ""){
            //     $gudang     = Chiller::whereIn('status',[1,2])
            //                     ->where('jenis','masuk')
            //                     ->where('item_name', 'LIKE', '%'.$cari.'%')
            //                     ->where('stock_berat','>','0')
            //                     ->orderBy('item_name', 'ASC')
            //                     ->whereBetween('tanggal_produksi', [$tanggal, $tanggal_akhir])
            //                     ->get();
            //     $pindah     =   Gudang::where('kategori', 'Production')
            //                     ->where('status', 1)
            //                     ->where('id', 13)
            //                     ->get();
            // } else {
            //     $gudang     =   Product_gudang::where('gudang_id', $request->id)
            //                     ->whereBetweem('created_at', [$tanggal, $tanggal_akhir])
            //                     ->where('status', '2')
            //                     ->get();
            //     $pindah     =   Gudang::where('kategori', 'Production')
            //                     ->where('status', 1)
            //                     ->where('id', 13)
            //                     ->get();
            // }
            
            if(empty($request->id)){
                $gudang = collect([]);
                $productgudang = Product_gudang::whereBetween('created_at', [$tanggal, $tanggal_akhir])
                    ->where('nama', 'LIKE', '%' . $cari . '%')
                    ->where('status', 2)
                    ->orderBy('nama', 'ASC')
                    ->orderBy('production_date','ASC')
                    ->get();
            
                $chiller = Chiller::whereIn('status', [1, 2])
                    ->whereBetween('tanggal_produksi', [$tanggal, $tanggal_akhir])
                    ->where('item_name', 'LIKE', '%' . $cari . '%')
                    ->where('jenis', 'masuk')
                    ->where('stock_berat', '>', '0')
                    ->orderBy('tanggal_produksi', 'ASC')
                    ->orderBy('item_name', 'ASC')
                    ->get();

                $pindah     =   Gudang::where('kategori', 'Production')
                                ->where('status', 1)
                                ->where('id', 13)
                                ->get();

                $gudang = $gudang->merge($productgudang)->merge($chiller)->paginate(15);
            }else if($request->id == 2 || $request->id == 3 || $request->id == 23 || $request->id == 24 || $request->id == 4){
                $sql     = Chiller::whereIn('status',[1,2])
                                ->whereBetween('tanggal_produksi', [$tanggal, $tanggal_akhir])
                                ->where('item_name', 'LIKE', '%'.$cari.'%')
                                ->where('jenis','masuk')
                                ->where(function ($q) use ($request){
                                    if ($request->id == '4' || $request->id == '24') {
                                        $q->where('type','hasil-produksi');
                                    }
                                    if ($request->id == '2' || $request->id == '23') {
                                        $q->where('type','bahan-baku');
                                    }
                                })
                                ->where('stock_berat','>','0')
                                ->orderBy('tanggal_produksi', 'ASC')
                                ->orderBy('item_name', 'ASC');
                                // ->paginate(15);

                                
                $master         = clone $sql;
                $arrayData      = $master->get();
                
                // dd($arrayData);
                $arrayId        = array();
                foreach($arrayData as $item){
                    $arrayId[]  = $item->id;
                }
                $stringData     = implode(",",$arrayId);
                // dd($stringData);
                if($stringData){
                    $alokasi    = DB::select("select chiller_out,SUM(bb_item) AS total_qty_alokasi, ROUND(sum(bb_berat),2) AS total_berat_alokasi, sum(keranjang) as total_keranjang
                                        FROM order_bahan_baku WHERE chiller_out IN(".$stringData.") 
                                        AND `status` IN(1,2) AND deleted_at IS NULL 
                                        GROUP BY chiller_out");
                    
                    $ambilabf   = DB::select("select table_id, sum(qty_awal) as total_qty_abf, round(sum(berat_awal),2) as total_berat_abf 
                                        FROM abf where table_name='chiller' AND table_id IN(".$stringData.")
                                        AND deleted_at IS NULL GROUP BY table_id");
                    $ambilbb    = DB::select("select chiller_id, sum(qty) as total_qty_freestock, round(sum(berat),2) AS total_berat_freestock
                                        FROM free_stocklist JOIN free_stock ON free_stocklist.freestock_id=free_stock.id 
                                        WHERE free_stocklist.chiller_id IN(".$stringData.") and free_stock.status IN (1,2,3)
                                        AND free_stock.deleted_at IS NULL AND free_stocklist.deleted_at IS NULL
                                        GROUP BY chiller_id");
                    $musnahkan  = DB::select("select item_id, SUM(qty) AS total_qty_musnahkan, ROUND(sum(berat),2) AS total_berat_musnahkan 
                                        FROM musnahkan_temp JOIN musnahkan on musnahkan.id=musnahkan_temp.musnahkan_id WHERE gudang_id IN (2,4,23,24) AND item_id IN(".$stringData.")
                                        AND musnahkan.deleted_at IS NULL GROUP BY item_id ");
                }
                $arraymodification      = [];
                foreach($arrayData as $data){
                    $total_qty_alokasi      = 0;
                    $total_berat_alokasi    = 0;
                    $total_keranjang        = 0;
                    $total_qty_abf          = 0;
                    $total_berat_abf        = 0;
                    $total_qty_freestock    = 0;
                    $total_berat_freestock  = 0;
                    $total_qty_musnahkan    = 0;
                    $total_berat_musnahkan  = 0;
        
                    foreach($alokasi as $val){
                        if($data->id == $val->chiller_out){
                            $total_qty_alokasi      = $val->total_qty_alokasi;
                            $total_berat_alokasi    = floatval($val->total_berat_alokasi) ?? 0;
                            $total_keranjang        = $val->total_keranjang ?? 0;
                        }
                    }
                    foreach($ambilabf as $valabf){
                        if($data->id == $valabf->table_id){
                            $total_qty_abf          = $valabf->total_qty_abf;
                            $total_berat_abf        = floatval($valabf->total_berat_abf) ?? 0;
                        }
                    }
                    foreach($ambilbb as $valbb){
                        if($data->id == $valbb->chiller_id){
                            $total_qty_freestock    = $valbb->total_qty_freestock;
                            $total_berat_freestock  = floatval($valbb->total_berat_freestock) ?? 0;
                        }
                    }
                    foreach($musnahkan as $valmus){
                        if($data->id == $valmus->item_id){
                            $total_qty_musnahkan    = $valmus->total_qty_musnahkan;
                            $total_berat_musnahkan  = floatval($valmus->total_berat_musnahkan) ?? 0;
                        }
                    }
        
                    $arraymodification[] = [
                        "id"                        => $data->id,
                        "production_id"             => $data->production_id,
                        "table_name"                => $data->table_name,
                        "table_id"                  => $data->table_id,
                        "asal_tujuan"               => $data->asal_tujuan,
                        "item_id"                   => $data->item_id,
                        "item_name"                 => $data->item_name,
                        "jenis"                     => $data->jenis,
                        "type"                      => $data->type,
                        "kategori"                  => $data->kategori,
                        "regu"                      => $data->regu,
                        "label"                     => $data->label,
                        "plastik_sku"               => $data->plastik_sku,
                        "plastik_nama"              => $data->plastik_nama,
                        "plastik_qty"               => $data->plastik_qty,
                        "plastik_group"             => $data->plastik_group,
                        "parting"                   => $data->parting,
                        "sub_item"                  => $data->sub_item,
                        "selonjor"                  => $data->selonjor,
                        "kode_produksi"             => $data->kode_produksi,
                        "unit"                      => $data->unit,
                        "customer_id"               => $data->customer_id,
                        "packaging"                 => $data->packaging,
                        "customer_name"             => $data->konsumen->nama ?? "#",
                        "qty_item"                  => floatval($data->qty_item),
                        "berat_item"                => floatval($data->berat_item),
                        "tanggal_potong"            => $data->tanggal_potong,
                        "no_mobil"                  => $data->no_mobil,
                        "tanggal_produksi"          => $data->tanggal_produksi,
                        "keranjang"                 => $data->keranjang,
                        "berat_keranjang"           => $data->berat_keranjang,
                        "stock_item"                => $data->stock_item,
                        "stock_berat"               => floatval($data->stock_berat),
                        "status"                    => $data->status,
                        "status_cutoff"             => $data->status_cutoff,
                        "key"                       => $data->key,
                        "created_at"                => $data->created_at ? date('Y-m-d H:i:s', strtotime($data->created_at)) : null,
                        "updated_at"                => $data->updated_at ? date('Y-m-d H:i:s', strtotime($data->updated_at)) : null,
                        "deleted_at"                => $data->deleted_at ? date('Y-m-d H:i:s', strtotime($data->deleted_at)) : null,
                        'total_qty_alokasi'         => $total_qty_alokasi,
                        'total_berat_alokasi'       => $total_berat_alokasi,
                        'total_qty_abf'             => $total_qty_abf,
                        'total_berat_abf'           => $total_berat_abf,
                        'total_qty_freestock'       => $total_qty_freestock,
                        'total_berat_freestock'     => $total_berat_freestock,
                        'total_keranjang'           => $total_keranjang,
                        'total_qty_musnahkan'       => $total_qty_musnahkan,
                        'total_berat_musnahkan'     => $total_berat_musnahkan,
                        'sisaQty'                   => $data->qty_item - $total_qty_alokasi - $total_qty_abf - $total_qty_freestock - $total_qty_musnahkan,
                        'sisaBerat'                 => $data->berat_item - $total_berat_alokasi - $total_berat_abf - $total_berat_freestock - $total_berat_musnahkan
                    ];
                }
                $stock                              = json_decode(json_encode($arraymodification));      
                // dd($stock);
                $gudang                             = Applib::paginate($stock,15);
                // dd($gudang);          
                $pindah     =   Gudang::where('kategori', 'Production')
                                ->where('status', 1)
                                ->where('id', 13)
                                ->get();
                                // dd($data_bb_fg);
            } else {
                    
                $sql        =   Product_gudang::with('productgudang')
                                                ->whereNotIn('type',['inventory_adjustment'])
                                                // ->where('gudang_id', $request->id)
                                                ->whereBetween('production_date', [$tanggal, $tanggal_akhir])
                                                ->where('nama', 'LIKE', '%' . $cari . '%')
                                                ->where('status', '2')
                                                ->where('production_date','>=',Applib::BatasMinimalTanggal())
                                                ->orderBy('production_date', 'ASC')
                                                ->orderBy('nama', 'ASC');
                                                // ->paginate(15);

                $master         = clone $sql;
                $arrayData      = $master->get();
                
                // dd($arrayData);
                $arrayId        = array();
                foreach($arrayData as $item){
                    $arrayId[]  = $item->id;
                }
                $stringData     = implode(",",$arrayId);
                // dd($stringData);
                if($stringData){
                    $alokasi            = DB::select("select chiller_out,SUM(bb_item) AS total_qty_alokasi, ROUND(sum(bb_berat),2) AS total_berat_alokasi, sum(keranjang) as total_keranjang
                                                        FROM order_bahan_baku WHERE chiller_out IN(".$stringData.") 
                                                        AND `status` IN(1,2) AND proses_ambil ='frozen' AND deleted_at IS NULL GROUP BY chiller_out");
                    $alokasithawing     = DB::select("select item_id,SUM(qty) AS total_qty_orderthawing, ROUND(SUM(berat),2) AS total_berat_orderthawing
                                                        FROM thawing_requestlist WHERE item_id IN(".$stringData.") 
                                                        GROUP BY item_id");
                    $regrading          = DB::select("select gudang_id_keluar,SUM(qty_awal) AS total_qty_regrading, ROUND(sum(berat_awal),2) AS total_berat_regrading
                                                        FROM product_gudang WHERE gudang_id_keluar IN(".$stringData.") 
                                                        AND `status`='4' AND `type`='grading_ulang'
                                                        GROUP BY gudang_id_keluar");
                    $musnahkan          = DB::select("select item_id, SUM(qty) AS total_qty_musnahkan, ROUND(sum(berat),2) AS total_berat_musnahkan 
                                                        FROM musnahkan_temp JOIN musnahkan on musnahkan.id=musnahkan_temp.musnahkan_id WHERE gudang_id NOT IN (2,4,23,24) AND item_id IN(".$stringData.")
                                                        AND musnahkan.deleted_at IS NULL GROUP BY item_id ");
                }
        
                $arraymodification      = [];
                foreach($arrayData as $data){
                    $total_qty_alokasi          = 0;
                    $total_berat_alokasi        = 0;
                    $total_keranjang            = 0;
                    $total_qty_orderthawing     = 0;
                    $total_berat_orderthawing   = 0;
                    $total_qty_regrading        = 0;
                    $total_berat_regrading      = 0;
                    $total_qty_musnahkan        = 0;
                    $total_berat_musnahkan      = 0;
        
                    foreach($alokasi as $val){
                        if($data->id == $val->chiller_out){
                            $total_qty_alokasi              = $val->total_qty_alokasi;
                            $total_berat_alokasi            = floatval($val->total_berat_alokasi) ?? 0;
                            $total_keranjang                = $val->total_keranjang ?? 0;
                        }
                    }
                    foreach($alokasithawing as $valthawing){
                        if($data->id == $valthawing->item_id){
                            $total_qty_orderthawing          = $valthawing->total_qty_orderthawing;
                            $total_berat_orderthawing        = floatval($valthawing->total_berat_orderthawing) ?? 0;
                        }
                    }
                    foreach($regrading as $val2){
                        if($data->id == $val2->gudang_id_keluar){
                            $total_qty_regrading            = $val2->total_qty_regrading;
                            $total_berat_regrading          = floatval($val2->total_berat_regrading) ?? 0;
                        }
                    }
                    foreach($musnahkan as $valmus){
                        if($data->id == $valmus->item_id){
                            $total_qty_musnahkan            = $valmus->total_qty_musnahkan;
                            $total_berat_musnahkan          = floatval($valmus->total_berat_musnahkan) ?? 0;
                        }
                    }
        
                    $arraymodification[] = [
                        "id"                        => $data->id,
                        "product_id"                => $data->product_id,
                        "nama"                      => $data->nama,
                        "kategori"                  => $data->kategori,
                        "sub_item"                  => $data->sub_item,
                        "table_name"                => $data->table_name,
                        "table_id"                  => $data->table_id,
                        "no_so"                     => $data->no_so,
                        "order_id"                  => $data->order_id,
                        "order_item_id"             => $data->order_item_id,
                        "order_bb_id"               => $data->order_bb_id,
                        "qty_awal"                  => $data->qty_awal,
                        "berat_awal"                => floatval($data->berat_awal),
                        "qty"                       => $data->qty,
                        "berat_timbang"             => $data->berat_timbang,
                        "berat"                     => floatval($data->berat),
                        "notes"                     => $data->notes,
                        "label"                     => $data->label,
                        "subpack"                   => $data->subpack,
                        "packaging"                 => $data->packaging,
                        "plastik_group"             => $data->plastik_group,
                        "plastik_qty"               => $data->plastik_qty,
                        "plastik_nama"              => $data->plastik_nama,
                        "keterangan"                => $data->keterangan,
                        "grade_item"                => $data->grade_item,
                        "parting"                   => $data->parting,
                        "karung"                    => $data->karung,
                        "karung_qty"                => $data->karung_qty,
                        "karung_isi"                => $data->karung_isi,
                        "karung_awal"               => $data->karung_awal,
                        "selonjor"                  => $data->selonjor,
                        "customer_id"               => $data->customer_id,
                        "customer_name"             => $data->konsumen->nama ?? "",
                        "palete"                    => $data->palete,
                        "potong"                    => $data->potong,
                        "expired"                   => $data->expired,
                        "production_date"           => $data->production_date,
                        "tanggal_kemasan"           => $data->tanggal_kemasan,
                        "production_code"           => $data->production_code,
                        "type"                      => $data->type,
                        "request_thawing"           => $data->request_thawing,
                        "stock_type"                => $data->stock_type,
                        "jenis_trans"               => $data->jenis_trans,
                        "abf_id"                    => $data->abf_id,
                        "gudang_id"                 => $data->gudang_id,
                        "kode_gudang"               => $data->productgudang->code ?? '#',
                        "asal_abf"                  => $data->asal_abf,
                        "barang_titipan"            => $data->barang_titipan,
                        "no_urut"                   => $data->no_urut,
                        "chiller_id"                => $data->chiller_id,
                        "gudang_id_keluar"          => $data->gudang_id_keluar,
                        "status"                    => $data->status,
                        "key"                       => $data->key,
                        "created_at"                => $data->created_at ? date('Y-m-d H:i:s', strtotime($data->created_at)) : null,
                        "updated_at"                => $data->updated_at ? date('Y-m-d H:i:s', strtotime($data->updated_at)) : null,
                        "deleted_at"                => $data->deleted_at ? date('Y-m-d H:i:s', strtotime($data->deleted_at)) : null,
                        "total_qty_alokasi"         => $total_qty_alokasi,
                        "total_berat_alokasi"       => $total_berat_alokasi,
                        "total_keranjang"           => $total_keranjang,
                        "total_qty_orderthawing"    => $total_qty_orderthawing,
                        "total_berat_orderthawing"  => $total_berat_orderthawing,
                        "total_qty_regrading"       => $total_qty_regrading,
                        "total_berat_regrading"     => $total_berat_regrading,
                        "total_qty_musnahkan"       => $total_qty_musnahkan,
                        "total_berat_musnahkan"     => $total_berat_musnahkan,
                        'sisaQty'                   => $data->qty_awal - $total_qty_alokasi - $total_qty_orderthawing - $total_qty_regrading - $total_qty_musnahkan,
                        'sisaBerat'                 => $data->berat_awal - $total_berat_alokasi - $total_berat_orderthawing - $total_berat_regrading - $total_berat_musnahkan
                    ];
                }
                $collection                         = json_decode(json_encode($arraymodification));
                $stock                              = array_filter($collection, function($vn){
                    return $vn->sisaBerat > 0;
                });
                $gudang                             = Applib::paginate($stock,15);

                $pindah     =   Gudang::where('kategori', 'Production')
                                ->where('status', 1)
                                ->where('id', 13)
                                ->get();
            } 

            // dd($gudang);
            return view('admin.pages.musnahkan.show', compact('gudang', 'pindah', 'id'));
        } else

        if ($request->key == 'list') {
            $data   =   Musnahkan::where('status', 1)->where('type', $request->type)->first() ;
            // dd($data);
            return view('admin.pages.musnahkan.list', compact('data', 'request'));
        }else if($request->key == 'cekavailablebap'){ 
            $bap                = $request->beritaacara;
            $cek                = Musnahkan::select('no_berita_acara')->where('no_berita_acara',$bap)->get();
            if($cek->count() == 0)
            {
                echo "true";
            }else{
                echo "false";
            }
        }
        else {
            $cold   =   Gudang::where('kategori', 'warehouse')
                        ->where('status', 1)
                        ->where('subsidiary', env('NET_SUBSIDIARY'))
                        ->get();

            $cold1  =   Gudang::where('status', 1)
                        ->whereIn('id', [2, 4,24,23])
                        ->where('subsidiary', env('NET_SUBSIDIARY'))
                        ->get();
            
            $gudang = collect([]);            
            $chiller     = Chiller::whereIn('status',[1,2])
                        ->where('jenis','masuk')
                        ->where('stock_berat','>','0')
                        ->orderBy('item_name', 'ASC')
                        ->groupBy('item_name')
                        ->get();

            $productgudang = Product_gudang::where('status', 2)
                        ->orderBy('nama', 'ASC')
                        ->groupBy('nama')
                        ->get();

            $gudang = $gudang->merge($chiller)->merge($productgudang);
                        

            return view('admin.pages.musnahkan.index', compact('cold', 'cold1','gudang'));
        }

    }

    public function store(Request $request)
    {
        if ($request->key == 'temporary' || $request->key == 'submit_list') {
            DB::beginTransaction();

            $musnah             =   Musnahkan::where('status', 1)->where('type', ($request->key == 'temporary' ? 'gudang' : 'item'))->first() ?? new Musnahkan ;
            $musnah->user_id    =   Auth::user()->id ;
            $musnah->type       =   ($request->key == 'temporary' ? 'gudang' : 'item') ;
            $musnah->status     =   1 ;
            if (!$musnah->save()) {
                DB::rollBack() ;
                $result['status']   =   400 ;
                $result['msg']      =   'Proses gagal' ;
                return $result ;
            }

            // if (COUNT($musnah->list_data)) {
            //     foreach ($musnah->list_data as $row) {
            //         $gudang = Gudang::where('id', $row->gudang_id)->first();
            //     }


            //     if ($request->cold !== $row->gudang_id) {
            //         DB::rollBack() ;
            //         $result['status']   =   400;
            //         $result['msg']      =   'Penambahan item pemusnahan saat ini untuk ' . $gudang->code;
            //         return $result;
            //     }

            // }
            if($request->berat == 0){
                DB::rollBack();
                $result['status']   =   400;
                $result['msg']      =   "Pengambilan tidak boleh 0";
                return $result;
            }

            if($request->berat > 0){

                if($request->cold == 2 || $request->cold == 4 || $request->cold == 23 || $request->cold == 24){
                    $sisaqty            = Chiller::ambilsisachiller($request->id,'qty_item','qty','bb_item');
                    $sisaberat          = Chiller::ambilsisachiller($request->id,'berat_item','berat','bb_berat');
                    $convertSisaBerat   = number_format((float)$sisaberat, 2, '.', '');
                    
                }else{
                    $sisaqty            = Product_gudang::ambilsisaproductgudang($request->id,'qty_awal','qty','bb_item');
                    $sisaberat          = Product_gudang::ambilsisaproductgudang($request->id,'berat_awal','berat','bb_berat');
                    $convertSisaBerat   = number_format((float)$sisaberat, 2, '.', '');

                }
                if ($request->qty > $sisaqty || $request->berat > $convertSisaBerat) {
                    DB::rollBack();
                    $result['status']   =   400;
                    $result['msg']      =   "Proses gagal, pengambilan melebihi stock. silakan di refresh halaman dahulu";
                    return $result;
                }
               
                $musnah_temp                =   new Musnahkantemp ;
                $musnah_temp->musnahkan_id  =   $musnah->id ;
                $musnah_temp->gudang_id     =   $request->cold ;
                $musnah_temp->item_id       =   $request->id ;
                $musnah_temp->qty           =   $request->qty ;
                $musnah_temp->berat         =   $request->berat ;
                if (!$musnah_temp->save()) {
                    DB::rollBack();
                    $result['status']   =   400;
                    $result['msg']      =   'Proses gagal';
                    return $result;
                }
            }

            DB::commit();
            if($request->berat > 0){
                $result['status']   =   200;
                $result['msg']      =   'Berhasil ditambahkan ke list';
            }
            else{
                $result['status']   =   400;
                $result['msg']      =   'Pastikan sudah input data dengan benar';
            }
            return $result;
        }

        if ($request->key == 'hapus') {
            return Musnahkantemp::find($request->id)->delete();
        }

        if ($request->key == 'batal_semua') {
            DB::beginTransaction();

            $data       =   Musnahkan::find($request->id) ;
            $MethodType =   $data->type;
            foreach ($data->list_data as $row) {
                if ($row->gudang_id == 2 or $row->gudang_id == 4 || $row->gudang_id == 23 or $row->gudang_id == 24) {

                    $chiller                =   Chiller::find($row->item_id);
                    $chiller->stock_item    =   $chiller->stock_item + $row->qty;
                    $chiller->stock_berat   =   $chiller->stock_berat + $row->berat;
                

                    if (!$chiller->save()) {
                        DB::rollBack();
                        $result['status']   =   400;
                        $result['msg']      =   'Proses gagal';
                        return $result;
                    }
                    
                    if($row->item_out){
                        $deleted = Chiller::find($row->item_out);
                        $deleted->delete() ;
                    }

                } else {
                    
                    $gudang         =   $MethodType == 'gudang' ? Product_gudang::find($row->item_id) : Product_gudang::where('product_id',$row->item_id)->first();
                    $gudang->qty    =   $gudang->qty + $row->qty;
                    $gudang->berat  =   $gudang->berat + $row->berat;
                    if (!$gudang->save()) {
                        DB::rollBack();
                        $result['status']   =   400;
                        $result['msg']      =   'Proses gagal';
                        return $result;
                    }

                    if($row->item_out){
                        $deleted = Product_gudang::find($row->item_out);
                        $deleted->delete() ;
                    }

                }
                
            }

            $data->delete();

            DB::commit();
            try {
                Chiller::recalculate_chiller($chiller->id);
            } catch (\Throwable $th) {
                
            }
            $result['status']   =   200;
            $result['msg']      =   'Pemusnahan dibatalkan';
            return $result;

        }

        if ($request->key == 'batal_item') {
            DB::beginTransaction() ;

            $data   =   Musnahkantemp::find($request->id) ;

            if ($data->gudang_id == 2 or $data->gudang_id == 4 or $data->gudang_id == 23 or $data->gudang_id == 24 ) {

                $chiller                =   Chiller::find($data->item_id);
                $chiller->stock_item    =   $chiller->stock_item + $data->qty;
                $chiller->stock_berat   =   $chiller->stock_berat + $data->berat;

                if (!$chiller->save()) {
                    DB::rollBack();
                    $result['status']   =   400;
                    $result['msg']      =   'Proses gagal';
                    return $result;
                }

                if($data->item_out){
                    $deleted = Chiller::find($data->item_out);
                    $deleted->delete() ;
                }

            } else {

                $gudang         =   Product_gudang::find($data->item_id);
                $gudang->qty    =   $gudang->qty + $data->qty;
                $gudang->berat  =   $gudang->berat + $data->berat;
                if (!$gudang->save()) {
                    DB::rollBack();
                    $result['status']   =   400;
                    $result['msg']      =   'Proses gagal';
                    return $result;
                }

                if($data->item_out){
                    $deleted = Product_gudang::find($data->item_out);
                    $deleted->delete() ;
                }

            }

            $data->delete() ;

            DB::commit();
            try {
                Chiller::recalculate_chiller($chiller->id);
            } catch (\Throwable $th) {
                
            }
            $result['status']   =   200;
            $result['msg']      =   'Batalkan item dimusnahkan berhasil';
            return $result;
        }

        if ($request->key == 'selesaikan') {
            $musnah             =   Musnahkan::where('status', 1)->first() ;
            if ($musnah) {

                if (COUNT($musnah->list_data) < 1) {
                    $result['status']   =   400;
                    $result['msg']      =   'Tidak ada data';
                    return $result;
                }

                DB::beginTransaction() ;

                foreach ($musnah->list_data as $row) {
                    if ($row->gudang_id == 2 or $row->gudang_id == 4) {

                        $chiller                    =   Chiller::find($row->item_id);
                        $chill                      =   new Chiller;
                        $chill->table_name          =   'chiller';
                        $chill->table_id            =   $chiller->id;
                        $chill->asal_tujuan         =   $chiller->asal_tujuan;
                        $chill->item_id             =   $chiller->item_id;
                        $chill->item_name           =   $chiller->item_name;
                        $chill->jenis               =   'keluar';
                        $chill->type                =   'musnahkan';
                        $chill->qty_item            =   $row->qty;
                        $chill->stock_item          =   $row->qty;
                        $chill->berat_item          =   $row->berat;
                        $chill->stock_berat         =   $row->berat;
                        $chill->tanggal_potong      =   $chiller->tanggal_potong;
                        $chill->tanggal_produksi    =   date('Y-m-d');
                        $chill->no_mobil            =   $chiller->no_mobil;
                        $chill->status              =   4;
                        if (!$chill->save()) {
                            DB::rollBack() ;
                            $result['status']   =   400;
                            $result['msg']      =   'Proses gagal';
                            return $result;
                        }

                        $row->item_out              =   $chill->id;
                        if (!$row->save()) {
                            DB::rollBack();
                            $result['status']   =   400;
                            $result['msg']      =   'Proses gagal';
                            return $result;
                        }

                        $chiller->stock_item        =   $chiller->stock_item - $row->qty;
                        $chiller->stock_berat       =   $chiller->stock_berat - $row->berat;
                        if (!$chiller->save()) {
                            DB::rollBack() ;
                            $result['status']   =   400;
                            $result['msg']      =   'Proses gagal';
                            return $result;
                        }

                    } else {

                        $gudang =   Product_gudang::find($row->item_id);

                        if ($gudang) {

                            $data               =   new Product_gudang;
                            $data->table_name   =   $gudang->table_name;
                            $data->table_id     =   $gudang->table_id;
                            $data->product_id   =   $gudang->product_id;
                            $data->qty_awal     =   $row->qty;
                            $data->qty          =   $row->qty;
                            $data->berat_awal   =   $row->berat;
                            $data->berat        =   $row->berat;
                            $data->packaging    =   $gudang->packaging;
                            $data->palete       =   $gudang->palete;
                            $data->expired      =   $gudang->expired;
                            $data->type         =   $gudang->type;
                            $data->stock_type   =   'musnahkan';
                            $data->jenis_trans  =   'keluar';
                            $data->status       =   3;
                            if (!$data->save()) {
                                DB::rollBack() ;
                                $result['status']   =   400;
                                $result['msg']      =   'Proses gagal';
                                return $result;
                            }

                            $row->item_out      =   $data->id;
                            if (!$row->save()) {
                                DB::rollBack();
                                $result['status']   =   400;
                                $result['msg']      =   'Proses gagal';
                                return $result;
                            }

                            $gudang->qty        =   $gudang->qty - $row->qty;
                            $gudang->berat      =   $gudang->berat - $row->berat;
                            if (!$gudang->save()) {
                                DB::rollBack() ;
                                $result['status']   =   400;
                                $result['msg']      =   'Proses gagal';
                                return $result;
                            }
                        }

                    }
                }


                $musnah->tanggal            =   $request->tanggal ;
                $musnah->keterangan         =   $request->keterangan ;
                $musnah->no_berita_acara    =   $request->beritaacara ;
                $musnah->status             =   2 ;
                if (!$musnah->save()) {
                    DB::rollBack() ;
                    $result['status']   =   400;
                    $result['msg']      =   'Proses gagal';
                    return $result;
                }

                DB::commit();

                
                try {
                    Chiller::recalculate_chiller($chiller->id);
                } catch (\Throwable $th) {
                    
                }

                $result['status']   =   200;
                $result['msg']      =   'Berhasil Diselesaikan';
                return $result;
            }
            $result['status']   =   400;
            $result['msg']      =   'Tidak ada data';
            return $result;
        }


        // if ($request->key == 'kirim_netsuite') {
        //     $data           =   Musnahkan::find($request->id) ;

        //     $transfer       =   [];

        //     foreach ($data->list_data as $row) {
        //         // $getGudangId    =   Gudang::where('id', $row->gudang_id)->first();

        //         $item       =   Item::find($data->type == 'gudang' ? ($row->gudang->product_id ?? $row->chiller->item_id) : $row->item_id);

        //         $transfer[] =   [
        //             "internal_id_item"  =>  (string)$item->netsuite_internal_id,
        //             "item"              =>  (string)$item->sku,
        //             "qty_to_transfer"   =>  (string)$row->berat
        //         ];
        //     }

        //     $gdg            =   Gudang::where('id', $row->gudang_id)->first();
        //     $gdg_baru       =   Gudang::where('code', env('NET_SUBSIDIARY', 'CGL') . " - Storage Susut")->first();

        //     $nama_tabel     =   "musnahkan" ;
        //     $id_tabel       =   $data->id ;
        //     $location       =   $gdg->code ;
        //     $from           =   Gudang::gudang_netid($location);
        //     $to             =   Gudang::gudang_netid($gdg_baru->code);

        //     $id_location    =   Gudang::gudang_netid($location);
        //     $label          =   strtolower("ti_" . str_replace(" ", "", $gdg->code) . "_" . str_replace(" ", "", $gdg_baru->code));

        //     $data->status   =   3 ;
        //     $data->save() ;

        //     // return Netsuite::transfer_inventory($nama_tabel, $id_tabel, $label, $id_location, $location, $from, $to, $transfer, null);
        //     $netsuite                   =   new Netsuite;
        //     $netsuite->record_type      =   "transfer_inventory";
        //     $netsuite->label            =   "$label";
        //     $netsuite->trans_date       =   $data->tanggal ?? Carbon::now();
        //     $netsuite->user_id          =   Auth::user()->id ?? NULL;
        //     $netsuite->tabel            =   "$nama_tabel";
        //     $netsuite->paket_id         =   NULL;
        //     $netsuite->tabel_id         =   $id_tabel;
        //     $netsuite->subsidiary_id    =   env("NET_SUBSIDIARY_ID", "2");
        //     $netsuite->subsidiary       =   env("NET_SUBSIDIARY", "CGL");
        //     $netsuite->id_location      =   "$id_location";
        //     $netsuite->location         =   "$location";
    
        //     if (!$netsuite->save()) {
        //         DB::rollBack();
        //         return back()->with('status', 2)->with('message', 'Proses gagal');
        //     }
    
        //     $net    =   Netsuite::find($netsuite->id);
    
        //     $result =   [
        //         "record_type"   =>  "transfer_inventory",
        //         "data"          =>  [
        //             [
        //                 "appsid"                    =>  env('NET_SUBSIDIARY', 'CGL')."-".(string)$netsuite->id,
        //                 "transaction_date"          =>  date("d-M-Y"),
        //                 "memo"                      =>  "",
        //                 "from_gudang"               =>  "$from",
        //                 "to_gudang"                 =>  "$to",
        //                 "internal_id_subsidiary"    =>  env("NET_SUBSIDIARY_ID", "2"),
        //                 "subsidiary"                =>  env("NET_SUBSIDIARY", "CGL"),
        //                 "line"                      =>  $data,
        //             ]
        //         ]
        //     ];
    
        //     $net->script            =   '214';
        //     $net->deploy            =   '1';
        //     $net->data_content      =   json_encode($result);
        //     $net->status            =   5;
    
        //     if (!$net->save()) {
        //         DB::rollBack();
        //         return back()->with('status', 2)->with('message', 'Proses gagal');
        //     }
    
        //     return $net;
        // }
        if ($request->key == 'kirim_netsuite') {
            $data           =   Musnahkan::find($request->id) ;
            
            for($x=0; $x < Count($data->list_data); $x++){
                $transfer       =   [];
                // $getGudangId    =   Gudang::where('id', $row->gudang_id)->first();
                if($data->type == 'gudang'){
                    if($data->list_data[$x]->gudang_id == '2' || $data->list_data[$x]->gudang_id == '4' || 
                        $data->list_data[$x]->gudang_id == '23' || $data->list_data[$x]->gudang_id == '24' ){
                            $searchId   = $data->list_data[$x]->chiller->item_id;
                    }else{
                            $searchId   = $data->list_data[$x]->gudang->product_id;
                    }
                }else{
                            $searchId   =  $data->list_data[$x]->item_id;
                }
                // $item       =   Item::find($data->type == 'gudang' ? ($data->list_data[$x]->gudang->product_id ?? $data->list_data[$x]->chiller->item_id) : $data->list_data[$x]->item_id);
                $item       =   Item::find($searchId);

                $transfer[] =   [
                    "internal_id_item"  =>  (string)$item->netsuite_internal_id,
                    "item"              =>  (string)$item->sku,
                    "qty_to_transfer"   =>  (string)$data->list_data[$x]->berat
                ];

                $gdg            =   Gudang::where('id', $data->list_data[$x]->gudang_id)->first();
                $gdg_baru       =   Gudang::where('code', env('NET_SUBSIDIARY', 'CGL') . " - Storage Susut")->first();

                $nama_tabel     =   "musnahkan" ;
                $id_tabel       =   $data->id ;

                if (env("NET_SUBSIDIARY", "CGL") == 'EBA') {
                    if (str_contains($gdg->code, 'Cold Storage')) {
                        $location       =   env("NET_SUBSIDIARY", "CGL") . ' - Cold Storage' ;
                    } else {
                        $location       =   $gdg->code ;
                    }

                } else {
                    $location       =   $gdg->code ;

                }

                $from           =   Gudang::gudang_netid($location);
                $to             =   Gudang::gudang_netid($gdg_baru->code);

                $id_location    =   Gudang::gudang_netid($location);
                $label          =   strtolower("ti_" . str_replace(" ", "", $gdg->code) . "_" . str_replace(" ", "", $gdg_baru->code));

                

                // return Netsuite::transfer_inventory($nama_tabel, $id_tabel, $label, $id_location, $location, $from, $to, $transfer, null);
                $netsuite                   =   new Netsuite;
                $netsuite->record_type      =   "transfer_inventory";
                $netsuite->label            =   "$label";
                $netsuite->trans_date       =   date("Y-m-d", strtotime($data->tanggal)) ?? date("Y-m-d");
                $netsuite->user_id          =   Auth::user()->id ?? NULL;
                $netsuite->tabel            =   "$nama_tabel";
                $netsuite->paket_id         =   0;
                $netsuite->tabel_id         =   $id_tabel;
                $netsuite->subsidiary_id    =   env("NET_SUBSIDIARY_ID", "2");
                $netsuite->subsidiary       =   env("NET_SUBSIDIARY", "CGL");
                $netsuite->id_location      =   "$id_location";
                $netsuite->location         =   "$location";
        
                if (!$netsuite->save()) {
                    DB::rollBack();
                    return back()->with('status', 2)->with('message', 'Proses gagal');
                }
        
                $net    =   Netsuite::find($netsuite->id);
        
                $result =   [
                    "record_type"   =>  "transfer_inventory",
                    "data"          =>  [
                        [
                            "appsid"                    =>  env('NET_SUBSIDIARY', 'CGL')."-".(string)$netsuite->id,
                            "transaction_date"          =>  date("d-M-Y", strtotime($data->tanggal)) ?? date("d-M-Y"),
                            "memo"                      =>  "",
                            "from_gudang"               =>  "$from",
                            "to_gudang"                 =>  "$to",
                            "internal_id_subsidiary"    =>  env("NET_SUBSIDIARY_ID", "2"),
                            "subsidiary"                =>  env("NET_SUBSIDIARY", "CGL"),
                            "line"                      =>  $transfer,
                        ]
                    ]
                ];
        
                $net->script            =   '214';
                $net->deploy            =   '1';
                $net->data_content      =   json_encode($result);
                $net->status            =   5;
        
                if (!$net->save()) {
                    DB::rollBack();
                    return back()->with('status', 2)->with('message', 'Proses gagal');
                }
        
                // return $net;
            }
            
            $data->status   =   3 ;
            $data->save() ;
        }


        if ($request->key == 'netsuite_rollback') {
            $data           =   Musnahkan::find($request->id) ;

            $transfer       =   [];

            foreach ($data->list_data as $row) {

                // $getGudangId    =   Gudang::where('id', $row->gudang_id)->first();


                $item           =   Item::find($row->gudang->product_id ?? $row->chiller->item_id);

                $transfer[] =   [
                    "internal_id_item"  =>  (string)$item->netsuite_internal_id,
                    "item"              =>  (string)$item->sku,
                    "qty_to_transfer"   =>  (string)$row->berat
                ];
            }

            $gdg            =   Gudang::where('code', env('NET_SUBSIDIARY', 'CGL') . " - Storage Susut")->first();
            $gdg_baru       =   Gudang::where('id', $row->gudang_id)->first();

            $nama_tabel     =   "musnahkan" ;
            $id_tabel       =   $data->id ;
            if (env("NET_SUBSIDIARY", "CGL") == 'EBA') {
                if (str_contains($gdg->code, 'Cold Storage')) {
                    $location       =   env("NET_SUBSIDIARY", "CGL") . ' - Cold Storage' ;
                }

            } else {
                $location       =   $gdg->code ;

            }
            $from           =   Gudang::gudang_netid($location);
            $to             =   Gudang::gudang_netid($gdg_baru->code);

            $id_location    =   Gudang::gudang_netid($location);
            $label          =   strtolower("ti_" . str_replace(" ", "", $gdg->code) . "_" . str_replace(" ", "", $gdg_baru->code));

            $data->status   =   2 ;
            $data->save() ;

            // return Netsuite::transfer_inventory($nama_tabel, $id_tabel, $label, $id_location, $location, $from, $to, $transfer, null);
            $netsuite                   =   new Netsuite;
            $netsuite->record_type      =   "transfer_inventory";
            $netsuite->label            =   "$label";
            $netsuite->trans_date       =   date("Y-m-d", strtotime($data->tanggal)) ?? date("Y-m-d");
            $netsuite->user_id          =   Auth::user()->id ?? NULL;
            $netsuite->tabel            =   "$nama_tabel";
            $netsuite->paket_id         =   0;
            $netsuite->tabel_id         =   $id_tabel;
            $netsuite->subsidiary_id    =   env("NET_SUBSIDIARY_ID", "2");
            $netsuite->subsidiary       =   env("NET_SUBSIDIARY", "CGL");
            $netsuite->id_location      =   "$id_location";
            $netsuite->location         =   "$location";
    
            if (!$netsuite->save()) {
                DB::rollBack();
                return back()->with('status', 2)->with('message', 'Proses gagal');
            }
    
            $net    =   Netsuite::find($netsuite->id);
    
            $result =   [
                "record_type"   =>  "transfer_inventory",
                "data"          =>  [
                    [
                        "appsid"                    =>  env('NET_SUBSIDIARY', 'CGL')."-".(string)$netsuite->id,
                        "transaction_date"          =>  date("d-M-Y", strtotime($data->tanggal)) ?? date("d-M-Y"),
                        "memo"                      =>  "",
                        "from_gudang"               =>  "$from",
                        "to_gudang"                 =>  "$to",
                        "internal_id_subsidiary"    =>  env("NET_SUBSIDIARY_ID", "2"),
                        "subsidiary"                =>  env("NET_SUBSIDIARY", "CGL"),
                        "line"                      =>  $data,
                    ]
                ]
            ];
    
            $net->script            =   '214';
            $net->deploy            =   '1';
            $net->data_content      =   json_encode($result);
            $net->status            =   5;
    
            if (!$net->save()) {
                DB::rollBack();
                return back()->with('status', 2)->with('message', 'Proses gagal');
            }
    
            return $net;
        }
    }

    public function riwayat(Request $request)
    {
        if ($request->key == 'view') {
            $data   =   Musnahkan::whereBetween('tanggal', [$request->awal ?? date("Y-m-d"), $request->akhir ?? date("Y-m-d")])
                        ->get();

            return view('admin.pages.musnahkan.riwayat.data', compact('data'));
        } else {
            return view('admin.pages.musnahkan.riwayat.index');
        }
    }

    public function item(Request $request)
    {
        $item       =   Item::get();
        $warehouse  =   Gudang::whereIn('kategori', ['warehouse', 'Production'])
                        ->where('status', 1)
                        ->where('subsidiary', env('NET_SUBSIDIARY', 'CGL'))
                        ->get();

        return view('admin.pages.musnahkan.item.index', compact('item', 'warehouse'));
    }
}
