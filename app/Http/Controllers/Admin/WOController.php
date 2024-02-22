<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Abf;
use App\Models\Bom;
use App\Models\BomItem;
use App\Models\Chiller;
use App\Models\Freestock;
use App\Models\FreestockList;
use App\Models\FreestockTemp;
use App\Models\Gudang;
use App\Models\Item;
use App\Models\Netsuite;
use App\Models\Order;
use App\Models\Product_gudang;
use App\Models\Production;
use App\Models\Purchasing;
use App\Models\Retur;
use App\Models\Thawing;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WOController extends Controller
{
    //

    public function chiller_fg(Request $request){

        $tanggal = $request->tanggal ?? date('Y-m-d');

        $chiller_fg =   Chiller::where('type', 'hasil-produksi')
                        ->where('jenis', 'masuk')
                        // ->where('stock_berat', '>', 0)
                        ->whereDate('tanggal_produksi', $tanggal)
                        ->where('kategori', 1)
                        ->orderBy('tanggal_produksi', 'ASC')
                        ->get();

        return view('admin.pages.halaman_custom.chiller_fg', compact('tanggal','chiller_fg'));
    }

    public function so_list(Request $request){
        $tanggal = $request->tanggal ?? date('Y-m-d');
        $order   = Order::where('tanggal_kirim', $tanggal)->get();

        return view('admin.pages.wo.so.create', compact('order'));
    }

    public function so_process(){

    }

    public function retur_list(Request $request){
        $tanggal = $request->tanggal ?? date('Y-m-d');
        
        $retur          =   Retur::select('retur.*')
                            ->where('tanggal_retur', $tanggal)
                            ->join('retur_item', 'retur.id', '=', 'retur_item.retur_id')
                            ->orderBy('tanggal_retur', 'ASC')
                            ->get();

        return view('admin.pages.wo.retur.create', compact('retur'));
    }

    public function retur_process(){

    }

    public function wo_1_list(Request $request){
        $tanggal = $request->tanggal ?? date('Y-m-d');

        $data       =   Production::where('no_urut', '!=', NULL)
                        ->whereIn('purchasing_id', Purchasing::select('id')
                        ->whereIn('type_po', ['PO LB','PO Maklon']))
                        ->where('prod_tanggal_potong', $tanggal)
                        ->whereIn('sc_status', [1, 0])
                        ->orderByRaw('prod_tanggal_potong ASC, no_urut ASC')
                        ->get();
        if ($request->key == 'unduh_wo1') {
            return view('admin.pages.wo.wo1.download-wo1', compact('data', 'tanggal','request'));
        }else{
            return view('admin.pages.wo.wo1.create', compact('data','tanggal'));   
        }
        

    }

    public function wo_1_process(Request $request){
        $tanggal = $request->tanggal ?? date('Y-m-d');

        $thawing    =   Thawing::where('tanggal_request', $tanggal)
                        ->orderBy('id', 'DESC')
                        ->get() ;
        foreach($thawing as $a){
            echo $this->generate_wo4($a->id);
        }
    }

    public function wo_4_list(Request $request){
        $tanggal = $request->tanggal ?? date('Y-m-d');

        $thawing    =   Thawing::where('tanggal_request', $tanggal)
                        ->orderBy('id', 'DESC')
                        ->get() ;
        if ($request->key == 'unduh_wo4') {
            return view('admin.pages.wo.wo4.download-wo4', compact('thawing', 'tanggal','request'));
        } else {
            return view('admin.pages.wo.wo4.create', compact('thawing','tanggal'));
        }
        
    }

    public function wo_4_process(Request $request){
        $tanggal = $request->tanggal ?? date('Y-m-d');

        $thawing    =   Thawing::where('tanggal_request', $tanggal)
                        ->orderBy('id', 'DESC')
                        ->get() ;
        foreach($thawing as $a){
            echo $this->generate_wo4($a->id);
        }
    }

    public function generate_wo4($id){

        $nama_gudang_lb             = env('NET_SUBSIDIARY', 'CGL')." - Storage Live Bird";
        $nama_gudang_expedisi       = env('NET_SUBSIDIARY', 'CGL')." - Storage Expedisi";
        $nama_gudang_bb             = env('NET_SUBSIDIARY', 'CGL')." - Chiller Bahan Baku";
        $nama_gudang_fg             = env('NET_SUBSIDIARY', 'CGL')." - Chiller Finished Good";
        $nama_gudang_abf            = env('NET_SUBSIDIARY', 'CGL')." - Storage ABF";
        $nama_gudang_wip            = env('NET_SUBSIDIARY', 'CGL')." - Storage Produksi (WIP)";
        $nama_gudang_susut          = env('NET_SUBSIDIARY', 'CGL')." - Storage Susut";
        $nama_gudang_retur          = env('NET_SUBSIDIARY', 'CGL')." - Storage Retur";
        $nama_gudang_other          = env('NET_SUBSIDIARY', 'CGL')." - Storage Others";

        $data   =   Thawing::find($id) ;
        
        DB::beginTransaction();
        
        foreach ($data->thawing_list as $row) {
            
            if($row->berat>0){
                
                $code   =   'TW-'.$row->id ;
                $gudang             =   Product_gudang::find($row->item_id);
                $item_frozen        =   Item::find($gudang->product_id);
                $item_finish        =   Item::where('nama', str_replace(' FROZEN', '', ($gudang->nama)))->first();

                try {
                    //code...

                    $nama_gudang_cs = env('NET_SUBSIDIARY', 'CGL')." - Cold Storage";
                    $gudang_cs      =   Gudang::where('code', $nama_gudang_cs)->first();

                    if($gudang_cs){
                        $id_location    =   $gudang_cs->netsuite_internal_id;
                        $location       =   $gudang_cs->code;
                        $from           =   $id_location;
                    }else{
                        $id_location    =   Gudang::find($gudang->gudang_id)->netsuite_internal_id;
                        $location       =   Gudang::find($gudang->gudang_id)->code;
                        $from           =   $id_location;
                    }

                    $label          =   'wo-4-thawing';

                    try {
                        //code...

                        $bom_kategori = Item::find($gudang->product_id);
                        if($bom_kategori){
                            if($bom_kategori->category_id=="8"){
                                $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL')." - THAWING AYAM PARTING BROILER FROZEN")
                                ->first();

                            }elseif($bom_kategori->category_id=="9"){
                                $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL')." - THAWING AYAM PARTING MARINASI BROILER FROZEN")
                                ->first();

                            }elseif($bom_kategori->category_id=="7"){
                                $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL')." - THAWING AYAM KARKAS BROILER FROZEN")
                                ->first();

                            }elseif($bom_kategori->category_id=="11"){
                                $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL')." - THAWING BONELESS BROILER FROZEN")
                                ->first();

                            }elseif($bom_kategori->category_id=="10"){
                                $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL')." - THAWING EVIS FROZEN")
                                ->first();

                            }else{
                                $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL').' - THAWING AYAM KARKAS BROILER FROZEN')
                                ->first();

                            }

                        }else{
                            $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL').' - THAWING AYAM KARKAS BROILER FROZEN')
                            ->first();

                        }

                        $bom_id         =   $bom->id;
                        $id_assembly    =   $bom->netsuite_internal_id;
                        $item_assembly  =   $bom->bom_name ;


                        $proses =   [];
                        foreach ($bom->bomproses as $list) {
                            $proses[]   =   [
                                "type"              =>  "Component",
                                "internal_id_item"  =>  (string)Item::item_sku($list->sku)->netsuite_internal_id,
                                "item"              =>  $list->sku,
                                "description"       =>  (string)Item::item_sku($list->sku)->nama,
                                "qty"               =>  (string)Item::where('nama', 'AY - S')->first()->sku == $list->sku ? $list->qty_per_assembly : ($list->qty_per_assembly * $row->berat),
                            ];
                        }

                    } catch (\Throwable $th) {
                        //throw $th;
                        $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL').' - THAWING AYAM KARKAS BROILER FROZEN')->first();
                        $bom_id         =   $bom->id;
                        $id_assembly    =   $bom->netsuite_internal_id;
                        $item_assembly  =   $bom->bom_name ;


                        $proses =   [];
                        foreach ($bom->bomproses as $list) {
                            $proses[]   =   [
                                "type"              =>  "Component",
                                "internal_id_item"  =>  (string)Item::item_sku($list->sku)->netsuite_internal_id,
                                "item"              =>  $list->sku,
                                "description"       =>  (string)Item::item_sku($list->sku)->nama,
                                "qty"               =>  (string)Item::where('nama', 'AY - S')->first()->sku == $list->sku ? $list->qty_per_assembly : ($list->qty_per_assembly * $row->berat),
                            ];
                        }

                    }



                    $component      =   [[
                        "type"              =>  "Component",
                        "internal_id_item"  =>  (string)$item_frozen->netsuite_internal_id,
                        "item"              =>  (string)$item_frozen->sku,
                        "description"       =>  (string)$item_frozen->nama,
                        "qty"               =>  (string)$row->berat,
                    ]];


                    $plastik    =   [];
                    // if ($gudang->packaging) {
                    //     $plast      =   Item::where('nama', $gudang->packaging)->first();
                    //     if ($plast) {
                    //         $itembom    =   BomItem::select('qty_per_assembly')->where('bom_id', $bom_id)->where('item_id', $plast->id)->first();

                    //         if($itembom){
                    //             $plastik    =   [[
                    //                 "type"              =>  "Component",
                    //                 "internal_id_item"  =>  (string)$plast->netsuite_internal_id,
                    //                 "item"              =>  (string)$plast->sku,
                    //                 "description"       =>  (string)$plast->nama,
                    //                 "qty"               =>  (string)($itembom->qty_per_assembly * $row->berat),
                    //             ]];
                    //         }
                    //     }
                    // }

                    // MASUK CHILLER

                    if ($item_finish->category_id == "1") {


                        $label_ti   =   "ti_storage" . $gudang->gudang_id . "_chillerbb-thawing";
                        $to         =   Gudang::gudang_netid($nama_gudang_bb);
                        $transfer   =   [
                            [
                                "internal_id_item"  =>  (string)Item::item_sku("1100000001")->netsuite_internal_id ,
                                "item"              =>  "1100000001",
                                "qty_to_transfer"   =>  (string)$row->berat
                            ]
                        ];

                        $finished_good  =   [[
                            "type"              =>  "Finished Goods",
                            "internal_id_item"  =>  (string)Item::item_sku("1100000001")->netsuite_internal_id ,
                            "item"              =>  "1100000001",
                            "description"       =>  "AYAM KARKAS BROILER (RM)",
                            "qty"               =>  (string)$row->berat
                        ]];

                    }else{

                        $label_ti   =   "ti_storage" . $gudang->gudang_id . "_chillerfg";
                        $to         =   Gudang::gudang_netid($nama_gudang_fg);
                        $transfer   =   [
                            [
                                "internal_id_item"  =>  (string)Item::item_sku($item_finish->sku)->netsuite_internal_id ,
                                "item"              =>  $item_finish->sku,
                                "qty_to_transfer"   =>  (string)$row->berat
                            ]
                        ];

                        $finished_good  =   [[
                            "type"              =>  "Finished Goods",
                            "internal_id_item"  =>  (string)Item::item_sku($item_finish->sku)->netsuite_internal_id ,
                            "item"              =>  $item_finish->sku,
                            "description"       =>  $item_finish->nama,
                            "qty"               =>  (string)$row->berat
                        ]];

                    }


                    $produksi       =   array_merge($component, $proses, $plastik, $finished_good);

                    $nama_tabel     =   'thawing_requestlist';
                    $id_tabel       =   $row->id;

                    $wo = Netsuite::work_order_doc($nama_tabel, $id_tabel, $label, $id_assembly, $item_assembly, $id_location, $location, $produksi, null, $data->tanggal_request, $code);

                    $label          =   'wo-4-build-thawing';
                    $total          =   $row->berat;
                    $wop = Netsuite::wo_build_doc($nama_tabel, $id_tabel, $label, $id_assembly, $item_assembly, $id_location, $location, $total, $produksi, $wo->id, $data->tanggal_request, $code);

                    Netsuite::transfer_inventory_doc($nama_tabel, $id_tabel, $label_ti, $id_location, $location, $from, $to, $transfer, $wop->id, $data->tanggal_request, $code);

                    DB::Commit();

                } catch (\Throwable $th) {
                    //throw $th;

                    DB::rollBack();
                    return "FAILED ".$th->getMessage()."<br>";
                }

            }else{
                DB::rollBack() ;
                return "Berat kosong";
            }

        }

        return "OK ".$code."<br>";

    }

    public function wo_3_list(Request $request){
        $tanggal = $request->tanggal ?? date('Y-m-d');
        $abf = Abf::where('tanggal_masuk', $tanggal)->get();

        if ($request->key == 'unduh_wo3') {
            return view('admin.pages.wo.wo3.download-wo3', compact('abf','tanggal','request'));
        } else {
            return view('admin.pages.wo.wo3.create', compact('abf','tanggal'));
        }
        
    }

    public function wo_2_list(Request $request){
        $tanggal = $request->tanggal ?? date('Y-m-d');

        $regu_all           = ['boneless', 'parting', 'marinasi', 'whole', 'frozen', 'byproduct'] ;
        $produksi           = [];

        foreach($regu_all as $ra){

            $regu = $ra;

            $bahan_baku =   FreestockList::select(DB::raw("SUM(berat) AS kg"), DB::raw("SUM(qty) AS jumlah"), 'free_stocklist.item_id', 'free_stocklist.freestock_id', 'items.nama', 'items.sku', 'chiller.type')
                            
                            ->leftJoin('items', 'items.id', '=', 'free_stocklist.item_id')
                            ->leftJoin('chiller', 'chiller.id', '=', 'free_stocklist.chiller_id')
                            ->leftJoin('free_stock', 'free_stock.id', '=', 'free_stocklist.freestock_id')

                            ->where('free_stock.regu', $regu)
                            ->where('free_stock.status', '3')
                            ->where('free_stock.tanggal', $tanggal)

                            ->whereNull('free_stock.netsuite_send')
                            ->orderBy('items.nama')
                            ->groupBy('items.nama')
                            ->groupBy('chiller.type')
                            ->get() ;
    
            $fg   =   FreestockTemp::select(DB::raw("SUM(qty) AS jumlah"), DB::raw("SUM(berat) AS kg"), 'item_id', 'items.nama', 'free_stocktemp.*')
                            
                            ->leftJoin('items', 'items.id', '=', 'free_stocktemp.item_id')
                            ->leftJoin('free_stock', 'free_stock.id', '=', 'free_stocktemp.freestock_id')

                            ->where('free_stock.regu', $regu)
                            ->where('free_stock.status', '3')
                            ->where('free_stock.tanggal', $tanggal)

                            ->whereNull('free_stock.netsuite_send')
                            ->orderBy('items.nama')
                            ->groupBy('items.nama')
                            ->get() ;

            $produksi[] = array(
                'regu' => $regu,
                'bb'   => $bahan_baku,
                'fg'   => $fg
            );

            $bahan_baku = [];
            $fg         = [];
            
        }

        if ($request->key == 'unduh_wo2') {
            return view('admin.pages.wo.wo2.download-wo2', compact('produksi','tanggal','request'));
        } else {
            # code...
            return view('admin.pages.wo.wo2.create', compact('tanggal', 'produksi'));
        }
        

    }

    public function wo_3_process(Request $request){
        $tanggal = $request->tanggal ?? date('Y-m-d');

        $abf = Abf::where('tanggal_masuk', $tanggal)->get();
        foreach($abf as $a){
            if(count($a->hasil_timbang_selesai)>0){
                echo $this->generate_wo3($a->id);
                
            }
        }
    }

    public function generate_wo3($id)
    {

        DB::beginTransaction();
        $abf = Abf::where('id', $id)->first();

        $tanggal = $abf->tanggal_masuk;

        // HITUNG SELESAI ABF
        $data               =   Abf::find($id);
        $gudang_selesai =   Product_gudang::where('table_name', 'abf')
                    ->where('table_id', $id)
                    ->where('status', '2')
                    ->get();


        $total_qty      = 0;
        $total_berat    = 0;

        $karung         = [];
        foreach ($gudang_selesai as $row) {
            $total_qty = $total_qty+$row->qty_awal;
            $total_berat = $total_berat+$row->berat_awal;

            $row->production_date     =   $tanggal;
            $row->status     =   2;
            $row->save() ;

            if($row->karung!=""){
                $karung[]   =   [
                    "type"              =>  "Component",
                    "internal_id_item"  =>  (string)Item::item_sku($row->karung)->netsuite_internal_id,
                    "item"              =>  $row->karung,
                    "description"       =>  (string)Item::item_sku($row->karung)->nama,
                    "qty"               =>  (string)$row->karung_qty,
                ];
            }

        }

        // Netsuite

            $finished_good          =   [] ;
            $component              =   [] ;
            $proses                 =   [] ;
            $transfer_awal          =   [] ;

            $location       =   env('NET_SUBSIDIARY', 'CGL')." - Storage ABF" ;
            $id_location    =   Gudang::gudang_netid($location) ;

            $label          =   'wo-3-abf-cs';

            $bom_kategori = Item::find($abf->item_id);
            $item = Item::find($abf->item_id);
            $item_assembly = env('NET_SUBSIDIARY', 'CGL')." - AYAM KARKAS FROZEN";

            $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL')." - AYAM KARKAS FROZEN")
                    ->first();

            if($bom_kategori){
                if($bom_kategori->category_id=="5"){
                    $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL')." - BONELESS BROILER FROZEN")
                    ->first();
                    $item_assembly = env('NET_SUBSIDIARY', 'CGL')." - BONELESS BROILER FROZEN";

                }elseif($bom_kategori->category_id=="3"){
                    $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL')." - AYAM PARTING MARINASI BROILER FROZEN")
                    ->first();
                    $item_assembly = env('NET_SUBSIDIARY', 'CGL')." - AYAM PARTING MARINASI BROILER FROZEN";

                }elseif($bom_kategori->category_id=="2"){
                    $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL')." - AYAM PARTING BROILER FROZEN")
                    ->first();
                    $item_assembly = env('NET_SUBSIDIARY', 'CGL')." - AYAM PARTING BROILER FROZEN";

                }else{
                    $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL')." - AYAM KARKAS FROZEN")
                    ->first();
                    $item_assembly = env('NET_SUBSIDIARY', 'CGL')." - AYAM KARKAS FROZEN";

                }
            }

            $nama_assembly  =   $bom->bom_name ;
            $id_assembly    =   $bom->netsuite_internal_id ;
            $bom_id         =   $bom->id;

            $transfer_awal[] =   [
                "internal_id_item"  =>  (string)$item->netsuite_internal_id,
                "item"              =>  (string)$item->sku,
                "qty_to_transfer"   =>  (string)$total_berat
            ];

            $component[]        =   [
                "type"              =>  "Component",
                "internal_id_item"  =>  (string)$item->netsuite_internal_id,
                "item"              =>  (string)$item->sku,
                "description"       =>  (string)$item->nama,
                "qty"               =>  (string)$total_berat,
            ];

            $item_baru   =   Item::where('nama', '=', str_replace(" FROZEN", "", $item->nama) . ' FROZEN')->first();

            if ($item_baru == '') {
                return 'Item Kosong';
            }

            $wo_id = NULL;
            $wob_id = NULL;
            if (strpos($item->nama, 'FROZEN') !== false) {

                if($data->asal_tujuan=="kepala_produksi"){

                     // ===================    TRANSFER INVENTORY IN FINISHED GOOD TO ABF    ===================
                    $nama_tabel     =   "chiller";
                    $id_tabel       =   $abf->table_id;

                    $from           =   Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Chiller Finished Good");

                    $label          =   "ti_fg_abf";
                    $to             =   Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Storage ABF");
                    $location_from       =   env('NET_SUBSIDIARY', 'CGL')." - Chiller Finished Good" ;
                    $id_location_from    =   Gudang::gudang_netid($location) ;

                    $ti_awal  = Netsuite::transfer_inventory_doc($nama_tabel, $id_tabel, $label, $id_location_from, $location_from, $from, $to, $transfer_awal, null, $tanggal, "ABF-".$abf->id);

                }

            }else{

                foreach ($bom->bomproses as $row) {
                    $proses[]   =   [
                        "type"              =>  "Component",
                        "internal_id_item"  =>  (string)Item::item_sku($row->sku)->netsuite_internal_id,
                        "item"              =>  $row->sku,
                        "description"       =>  (string)Item::item_sku($row->sku)->nama,
                        "qty"               =>  round(($row->qty_per_assembly * $abf->berat_awal),2)
                    ];
                }

                $finished_good[]         =   [
                    "type"              =>  "Finished Goods",
                    "internal_id_item"  =>  (string)$item_baru->netsuite_internal_id,
                    "item"              =>  (string)$item_baru->sku,
                    "description"       =>  (string)$item_baru->nama,
                    "qty"               =>  (string)$total_berat,
                ];


                $produksi       =   array_merge($component, $proses, $finished_good, $karung);


                // ===================    TRANSFER INVENTORY IN FINISHED GOOD    ===================
                $nama_tabel     =   "chiller";
                $id_tabel       =   $abf->table_id;

                $from           =   Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Chiller Finished Good");

                $label          =   "ti_fg_abf";
                $to             =   Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Storage ABF");
                $location_from       =   env('NET_SUBSIDIARY', 'CGL')." - Chiller Finished Good" ;
                $id_location_from    =   Gudang::gudang_netid($location) ;

                $ti_awal  = Netsuite::transfer_inventory_doc($nama_tabel, $id_tabel, $label, $id_location_from, $location_from, $from, $to, $transfer_awal, null, $tanggal, "ABF-".$abf->id);

                $label  =   'wo-3-abf-cs';
                $wo     =   Netsuite::work_order_doc('abf', $abf->id, $label, $id_assembly, $item_assembly, $id_location, $location, $produksi, $ti_awal->id, $tanggal, "ABF-".$abf->id);

                $label  =   'wo-3-build-abf-cs';
                $total  =   $total_berat;
                $wob    =   Netsuite::wo_build_doc('abf', $abf->id, $label, $id_assembly, $item_assembly, $id_location, $location, $total, $produksi, $wo->id, $tanggal, "ABF-".$abf->id);

                $wob_id = $wob->id;
            }

            foreach ($gudang_selesai as $row) {

                $transfer_akhir = [];
                // ===================    TRANSFER INVENTORY IN FINISHED GOOD    ===================
                $nama_tabel     =   "product_gudang";
                $id_tabel       =   $row->id;

                $from           =   Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Storage ABF");

                $nama_gudang_cs = env('NET_SUBSIDIARY', 'CGL')." - Cold Storage";
                $gudang_baru    =   Gudang::where('code', $nama_gudang_cs)->first();
                $label          =   "ti_abf_cs_".str_replace(" ","-",str_replace("-","",strtolower($gudang_baru->code)));
                $to             =   $gudang_baru->netsuite_internal_id;
                $location_from       =   env('NET_SUBSIDIARY', 'CGL')." - Storage ABF" ;
                $id_location_from    =   Gudang::gudang_netid($location) ;

                $transfer_akhir[] =   [
                    "internal_id_item"  =>  (string)$item_baru->netsuite_internal_id,
                    "item"              =>  (string)$item_baru->sku,
                    "qty_to_transfer"   =>  (string)$row->berat_awal
                ];

                Netsuite::transfer_inventory_doc($nama_tabel, $id_tabel, $label, $id_location_from, $location_from, $from, $to, $transfer_akhir, $wob_id, $tanggal, "ABF-".$abf->id);

            }

            DB::commit();

            return $id."-Ok<br>";
    }

    public function create_wo3(Request $request){

        $tanggal = $request->tanggal ?? date('Y-m-d');

        // return $request->all();

        $chiller = $request->chiller_id;
        $item_id = $request->item_id;
        $chiller_qty = $request->chiller_qty;
        $chiller_berat = $request->chiller_berat;

        foreach($chiller as $no => $cl):

            $finished_good          =   [] ;
            $component              =   [] ;
            $proses                 =   [] ;
            $transfer_awal          =   [] ;

            $location       =   env('NET_SUBSIDIARY', 'CGL')." - Storage ABF" ;
            $id_location    =   Gudang::gudang_netid($location) ;

            $label          =   'wo-3-abf-custom';

            $bom_kategori = Item::find($item_id[$no]);
            $item = Item::find($item_id[$no]);
            $item_assembly = env('NET_SUBSIDIARY', 'CGL')." - AYAM KARKAS FROZEN";

            $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL')." - AYAM KARKAS FROZEN")
                    ->first();

            if($bom_kategori){
                if($bom_kategori->category_id=="5"){
                    $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL')." - BONELESS BROILER FROZEN")
                    ->first();
                    $item_assembly = env('NET_SUBSIDIARY', 'CGL')." - BONELESS BROILER FROZEN";

                }elseif($bom_kategori->category_id=="3"){
                    $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL')." - AYAM PARTING MARINASI BROILER FROZEN")
                    ->first();
                    $item_assembly = env('NET_SUBSIDIARY', 'CGL')." - AYAM PARTING MARINASI BROILER FROZEN";

                }elseif($bom_kategori->category_id=="2"){
                    $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL')." - AYAM PARTING BROILER FROZEN")
                    ->first();
                    $item_assembly = env('NET_SUBSIDIARY', 'CGL')." - AYAM PARTING BROILER FROZEN";

                }else{
                    $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL')." - AYAM KARKAS FROZEN")
                    ->first();
                    $item_assembly = env('NET_SUBSIDIARY', 'CGL')." - AYAM KARKAS FROZEN";

                }
            }

            $nama_assembly  =   $bom->bom_name ;
            $id_assembly    =   $bom->netsuite_internal_id ;
            $bom_id         =   $bom->id;

            $transfer_awal[] =   [
                "internal_id_item"  =>  (string)$item->netsuite_internal_id,
                "item"              =>  (string)$item->sku,
                "qty_to_transfer"   =>  (string)$chiller_berat[$no]
            ];

            $component[]        =   [
                "type"              =>  "Component",
                "internal_id_item"  =>  (string)$item->netsuite_internal_id,
                "item"              =>  (string)$item->sku,
                "description"       =>  (string)$item->nama,
                "qty"               =>  (string)$chiller_berat[$no],
            ];

            $item_baru   =   Item::where('nama', '=', str_replace(" frozen", "", $item->nama) . ' frozen')->first();

            if ($item_baru == '') {
                return 'Item Kosong';
            }

            foreach ($bom->bomproses as $row) {
                $proses[]   =   [
                    "type"              =>  "Component",
                    "internal_id_item"  =>  (string)Item::item_sku($row->sku)->netsuite_internal_id,
                    "item"              =>  $row->sku,
                    "description"       =>  (string)Item::item_sku($row->sku)->nama,
                    "qty"               =>  (string)Item::where('nama', 'AY - S')->first()->sku == $row->sku ? $row->qty_per_assembly : ($row->qty_per_assembly * $chiller_berat[$no]),
                ];
            }

            $finished_good[]         =   [
                "type"              =>  "Finished Goods",
                "internal_id_item"  =>  (string)$item_baru->netsuite_internal_id,
                "item"              =>  (string)$item_baru->sku,
                "description"       =>  (string)$item_baru->nama,
                "qty"               =>  (string)$chiller_berat[$no],
            ];


            $produksi       =   array_merge($component, $proses, $finished_good);

            $wo     =   Netsuite::work_order_date('chiller', $chiller[$no], $label, $id_assembly, $item_assembly, $id_location, $location, $produksi, null, $tanggal);

            $label  =   'wo-3-build-abf-custom';
            $total  =   $chiller_berat[$no];
            $wob    =   Netsuite::wo_build_date('chiller', $chiller[$no], $label, $id_assembly, $item_assembly, $id_location, $location, $total, $produksi, $wo->id, $tanggal);

            // ===================    TRANSFER INVENTORY IN FINISHED GOOD    ===================
            $nama_tabel     =   "chiller";
            $id_tabel       =   $chiller[$no];

            $from           =  Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Chiller Finished Good");

            $label          =   "ti_fg_abf_custom";
            $to             =   Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Storage ABF");
            $location_from       =   env('NET_SUBSIDIARY', 'CGL')." - Chilller Finished Good" ;
            $id_location_from    =   Gudang::gudang_netid($location) ;

            Netsuite::transfer_inventory_date($nama_tabel, $id_tabel, $label, $id_location_from, $location_from, $from, $to, $transfer_awal, $wob->id, $tanggal);

            $chiller_proses                =   Chiller::find($chiller[$no]);

            if($chiller_proses){

                $exp                    =   json_decode($chiller_proses->label);

                $abf                    =   new Abf();
                $abf->production_id     =   $chiller_proses->production_id;
                $abf->table_name        =   'chiller';
                $abf->table_id          =   $chiller_proses->id;
                $abf->tanggal_masuk     =   date('Y-m-d');
                $abf->asal_tujuan       =   'kepala_regu';
                $abf->no_mobil          =   $chiller_proses->no_mobil;
                $abf->item_id           =   $item->id;
                $abf->item_id_lama      =   $chiller_proses->item_id;
                $abf->item_name         =   $item->nama;
                $abf->packaging         =   $exp->plastik->jenis ?? NULL ;
                $abf->jenis             =   'masuk';
                $abf->type              =   'free';
                $abf->qty_awal          =   $chiller_qty[$no] ;
                $abf->berat_awal        =   $chiller_berat[$no] ;
                $abf->qty_item          =   $chiller_qty[$no] ;
                $abf->berat_item        =   $chiller_berat[$no] ;
                $abf->created_at        =   $tanggal ;
                $abf->updated_at        =   $tanggal ;
                $abf->status            =   '1';

                $abf->save();
            }

        endforeach;

        return redirect(url('admin/sync'));

    }


    public function create(Request $request){
        $tanggal        =   $request->tanggal ?? date('Y-m-d');
        $regu           =   $request->regu ;


        $bahan_baku     =   Freestock::getDataWONONWO($regu, $tanggal, $tanggal, 'getDataWOBB', NULL, NULL, NULL);
        $produksi       =   Freestock::getDataWONONWO($regu, $tanggal, $tanggal, NULL, 'getDataWOFG', NULL, NULL);
        

        // dd($bahan_baku);
        // dd($produksi);


        // $bahan_baku =   FreestockList::select(DB::raw("SUM(berat) AS kg"), DB::raw("SUM(qty) AS jumlah"), 'free_stocklist.item_id', 'nama', 'sku', 'netsuite_internal_id', 'chiller.type')

        //                 ->leftJoin('items', 'items.id', '=', 'free_stocklist.item_id')
        //                 ->leftJoin('chiller', 'chiller.id', '=', 'free_stocklist.chiller_id')
        //                 ->leftJoin('free_stock', 'free_stock.id', '=', 'free_stocklist.freestock_id')


        //                 ->where('free_stock.regu', $regu)
        //                 ->where('free_stock.status', '3')
        //                 ->where('free_stock.tanggal', $tanggal)
        //                 ->where('free_stock.netsuite_id', null)
                        
        //                 ->whereNull('free_stock.netsuite_send')
        //                 ->orderBy('items.nama')
        //                 ->groupBy('items.nama')
        //                 ->groupBy('chiller.type')
        //                 ->get() ;

        // $produksi   =   FreestockTemp::select(DB::raw("SUM(qty) AS jumlah"), DB::raw("SUM(berat) AS kg"), 'item_id', 'nama', 'sku', 'netsuite_internal_id')
                       
        //                 ->leftJoin('items', 'items.id', '=', 'free_stocktemp.item_id')
        //                 ->leftJoin('free_stock', 'free_stock.id', '=', 'free_stocktemp.freestock_id')

        //                 ->where('free_stock.regu', $regu)
        //                 ->where('free_stock.status', '3')
        //                 ->where('free_stock.tanggal', $tanggal)
        //                 ->where('free_stock.netsuite_id', null)

        //                 ->whereNull('free_stock.netsuite_send')
        //                 ->orderBy('items.nama')
        //                 ->groupBy('items.nama')
        //                 ->get() ;

        $label      =   FreestockTemp::whereIn('free_stocktemp.freestock_id', Freestock::select('id')->whereDate('tanggal', $tanggal)->where('netsuite_id', null))
                        ->where('regu', $regu)
                        ->get() ;

                        // return $label;
        $plastik = [];

        if ($regu == 'boneless') {
            $bom   =   Bom::where('bom_name', env("NET_SUBSIDIARY", "CGL") . " - KARKAS - BONELESS BROILER")->first();
        } else
        if ($regu == 'parting') {
            $bom   =   Bom::where('bom_name', env("NET_SUBSIDIARY", "CGL") . " - AYAM PARTING BROILER")->first();
        } else
        if ($regu == 'marinasi') {
            $bom   =   Bom::where('bom_name', env("NET_SUBSIDIARY", "CGL") . " - AYAM PARTING MARINASI BROILER")->first();
        } else
        if ($regu == 'whole') {
            $bom   =   Bom::where('bom_name', env("NET_SUBSIDIARY", "CGL") . " - AYAM KARKAS BROILER")->first();
        } else
        if ($regu == 'frozen') {
            $bom   =   Bom::where('bom_name', env("NET_SUBSIDIARY", "CGL") . " - AYAM KARKAS FROZEN")->first();
        } else
        if ($regu == 'byproduct') {
            $bom   =   Bom::where('bom_name', env("NET_SUBSIDIARY", "CGL") . " - EVIS")->first();
        }else{
            $bom   =   Bom::where('bom_name', env("NET_SUBSIDIARY", "CGL") . " - KARKAS - BONELESS BROILER")->first();
        }


        $sku_plastk = [];
        foreach($label as $l):

            if($l->plastik_sku!="" || $l->plastik_sku!="0000000000000"){
                $itm    = Item::where('sku',$l->plastik_sku)->first();
                $sku_plastk[] = $itm;

                if($itm){
                    if(env('NET_SUBSIDIARY', 'EBA')=='EBA'){
                            $plastik[]  =   [
                                "type"              =>  "Component",
                                "internal_id_item"  =>  (string)$itm->netsuite_internal_id,
                                "item"              =>  $l->plastik_sku,
                                "description"       =>  $l->plastik_nama,
                                "qty"               =>  (string)(($itm->berat_kali ?? 0.004) * $l->plastik_qty),
                            ];
                    }
                    if(env('NET_SUBSIDIARY', 'CGL')=='CGL'){
                        $item_bom   =   BomItem::select('qty_per_assembly')
                                        ->where('bom_id', $bom->id)
                                        ->where('sku', $l->plastik_sku)
                                        ->first();
        
                        if($item_bom){
                            $plastik[]  =   [
                                "type"              =>  "Component",
                                "internal_id_item"  =>  (string)$itm->netsuite_internal_id,
                                "item"              =>  $l->plastik_sku,
                                "description"       =>  $l->plastik_nama,
                                "qty"               =>  (string)($item_bom->qty_per_assembly * $l->plastik_qty),
                            ];
                        }
                    }
                }
            }

        endforeach;

        // return $sku_plastk;

        return view('admin.pages.wo.create', compact('regu', 'tanggal', 'bahan_baku', 'produksi', 'plastik', 'bom'));
    }

    public function store(Request $request){


        // dd($request->all());
        $tanggal        =   $request->tanggal ?? date('Y-m-d');
        $regu           =   $request->regu ;

        // return $this->generateWOGlobal($tanggal, $regu, FALSE);

        // CEK APAKAH SUDAH BUAT WO ATAU BELUM
        $cekWO          = Freestock::where('tanggal', $tanggal)->where('regu', $regu)->where('status', 3)->first();
        if ($cekWO) {
            //  CEK DI NS APAKAH JSONNYA KEDELETE ATAU BELUM
            $cekDokumenWO = Netsuite::where('id', $cekWO->netsuite_id)->first();
            
            if (!$cekDokumenWO) {
                $this->generateWOGlobal($tanggal, $regu, FALSE);
                return back()->with('status', 1)->with('message', 'Pembuatan WO Berhasil');
                
            }
            return back()->with('status', 2)->with('message', "Gagal, WO telah terbuat - ".$regu);
            
            
        } else {
            return back()->with('status', 2)->with('message', "Gagal, WO telah terbuat - ".$regu);

        }

    }

    public static function generateWOGlobal($tanggal, $regu, $console = FALSE){


        $bahan_baku     =   Freestock::getDataWONONWO($regu, $tanggal, $tanggal, 'getDataWOBB', NULL, NULL, NULL);
        $produksi       =   Freestock::getDataWONONWO($regu, $tanggal, $tanggal, NULL, 'getDataWOFG', NULL, NULL);

        if(count($bahan_baku)>0 && count($produksi)>0){
            DB::beginTransaction();


            if ($regu == 'boneless') {
                $bom   =   Bom::where('bom_name', env("NET_SUBSIDIARY", "CGL") . " - KARKAS - BONELESS BROILER")->first();
                $id_assembly    =  $bom->netsuite_internal_id;
            } else
            if ($regu == 'parting') {
                $bom   =   Bom::where('bom_name', env("NET_SUBSIDIARY", "CGL") . " - AYAM PARTING BROILER")->first();
                $id_assembly    =  $bom->netsuite_internal_id;
            } else
            if ($regu == 'marinasi') {
                $bom   =   Bom::where('bom_name', env("NET_SUBSIDIARY", "CGL") . " - AYAM PARTING MARINASI BROILER")->first();
                $id_assembly    =  $bom->netsuite_internal_id;
            } else
            if ($regu == 'whole') {
                $bom   =   Bom::where('bom_name', env("NET_SUBSIDIARY", "CGL") . " - AYAM KARKAS BROILER")->first();
                $id_assembly    =  $bom->netsuite_internal_id;
            } else
            if ($regu == 'frozen') {
                $bom   =   Bom::where('bom_name', env("NET_SUBSIDIARY", "CGL") . " - AYAM KARKAS FROZEN")->first();
                $id_assembly    =  $bom->netsuite_internal_id;
            }else
            if ($regu == 'byproduct') {
                $bom   =   Bom::where('bom_name', env("NET_SUBSIDIARY", "CGL") . " - EVIS")->first();
                $id_assembly    =  $bom->netsuite_internal_id;
            }else{
                $bom   =   Bom::where('bom_name', env("NET_SUBSIDIARY", "CGL") . " - KARKAS - BONELESS BROILER")->first();
                $id_assembly    =  $bom->netsuite_internal_id;
            }

            $code = 'wo-2-'.$regu.'-'.time();

            $gabung_plastik = FreestockTemp::select(DB::raw("SUM(plastik_qty) AS plastik_qty"), 'plastik_sku', 'plastik_nama')
                        ->leftJoin('items', 'items.id', '=', 'free_stocktemp.item_id')
                        ->leftJoin('free_stock', 'free_stock.id', '=', 'free_stocktemp.freestock_id')

                        ->where('free_stock.regu', $regu)
                        ->where('free_stock.status', '3')
                        ->where('free_stock.tanggal', $tanggal)
                        ->where('free_stock.netsuite_id', null)

                        ->whereNull('free_stock.netsuite_send')
                        ->orderBy('plastik_nama')
                        ->groupBy('plastik_sku')
                        ->get() ;

            $plastik = [];
            $sku_plastk = [];

            foreach($gabung_plastik as $pls):

                    $itm    = Item::where('sku',$pls->plastik_sku)->first();
                    $sku_plastk[] = $itm;

                    if($itm){
                        if(env('NET_SUBSIDIARY', 'EBA')=='EBA'){
                                $plastik[]  =   [
                                    "type"              =>  "Component",
                                    "internal_id_item"  =>  (string)$itm->netsuite_internal_id,
                                    "item"              =>  $pls->plastik_sku,
                                    "description"       =>  $pls->plastik_nama,
                                    "qty"               =>  (string)(($itm->berat_kali ?? 0.004) * $pls->plastik_qty),
                                ];
                        }
                        if(env('NET_SUBSIDIARY', 'CGL')=='CGL'){
                            $item_bom   =   BomItem::select('qty_per_assembly')
                                            ->where('bom_id', $bom->id)
                                            ->where('sku', $pls->plastik_sku)
                                            ->first();
            
                            if($item_bom){
                                $plastik[]  =   [
                                    "type"              =>  "Component",
                                    "internal_id_item"  =>  (string)$itm->netsuite_internal_id,
                                    "item"              =>  $pls->plastik_sku,
                                    "description"       =>  $pls->plastik_nama,
                                    "qty"               =>  (string)($item_bom->qty_per_assembly * $pls->plastik_qty),
                                ];
                            }
                        }

                    }

            endforeach;


            $nama_gudang_lb             = env('NET_SUBSIDIARY', 'CGL')." - Storage Live Bird";
            $nama_gudang_expedisi       = env('NET_SUBSIDIARY', 'CGL')." - Storage Expedisi";
            $nama_gudang_bb             = env('NET_SUBSIDIARY', 'CGL')." - Chiller Bahan Baku";
            $nama_gudang_fg             = env('NET_SUBSIDIARY', 'CGL')." - Chiller Finished Good";
            $nama_gudang_abf            = env('NET_SUBSIDIARY', 'CGL')." - Storage ABF";
            $nama_gudang_wip            = env('NET_SUBSIDIARY', 'CGL')." - Storage Produksi (WIP)";
            $nama_gudang_susut          = env('NET_SUBSIDIARY', 'CGL')." - Storage Susut";
            $nama_gudang_retur          = env('NET_SUBSIDIARY', 'CGL')." - Storage Retur";
            $nama_gudang_other          = env('NET_SUBSIDIARY', 'CGL')." - Storage Others";

            // ===================    TRANSFER INVENTORY IN WIP    ===================

            $nama_tabel     =   "free_stock";
            $id_tabel       =   NULL;

            $location       =   $nama_gudang_bb;
            $id_location    =   Gudang::gudang_netid($location);
            $from           =   $id_location;
            $to             =   Gudang::gudang_netid($nama_gudang_wip);

            // untuk data kepala regu
            $arr_trf            =   [];
            $arr_bb             =   [];
            $berat_rm           =   0;
            $berat_memar        =   0;
            $berat_parent       =   0;
            $berat_pejantan     =   0;
            $berat_kampung      =   0;
            $berat_fg           =   0;
            $arr_trf_fg         =   [];
            $trans              =   [];
            $bahanbaku          =   [];

            // Untuk data evis
            $evis_berat_bb      =   0;
            $evis_berat_fg      =   0;
            $evis_array_fg      =   [];
            $evis_array_bb      =   [];

            // Loop untuk component

            $jenis_bahan_baku           = "";
            foreach ($bahan_baku as $row) {

                $jenis_bahan_baku       = "";

                // Check ayam KARKAS dan ayam utuh
                if (substr($row->sku, 0, 5) == "12111" || substr($row->sku, 0, 5) == "12112") {

                    // $getSKUItem = 

                    if($row->type=="hasil-produksi"){
                        // $berat_fg           += $row->kg;
                        $berat_fg           += $row->berat;

                        $arr_trf_fg[]  =   [
                            'internal_id_item'   =>  (string)Item::item_sku($row->sku)->netsuite_internal_id,
                            "item"               =>  (string)$row->sku,
                            "qty_to_transfer"    =>  (string)$row->berat,
                        ];
    
                        $arr_bb[]   =   [
                            "type"              =>  "Component",
                            "internal_id_item"  =>  (string)Item::item_sku($row->sku)->netsuite_internal_id,
                            "item"              =>  (string)$row->sku,
                            "description"       =>  (string)Item::item_sku($row->sku)->nama,
                            "qty"               =>  (string)$row->berat,
                        ];
                    }else{
                        $berat_rm           +=  $row->berat;
                    }
                //check ayam MEMAR
                } elseif (substr($row->sku, 0, 5) == "12113") {
                    if($row->type=="hasil-produksi"){
                        $berat_fg           += $row->berat;

                        $arr_trf_fg[]  =   [
                            'internal_id_item'   =>  (string)Item::item_sku($row->sku)->netsuite_internal_id,
                            "item"               =>  (string)$row->sku,
                            "qty_to_transfer"    =>  (string)$row->berat,
                        ];
    
                        $arr_bb[]   =   [
                            "type"              =>  "Component",
                            "internal_id_item"  =>  (string)Item::item_sku($row->sku)->netsuite_internal_id,
                            "item"              =>  (string)$row->sku,
                            "description"       =>  (string)Item::item_sku($row->sku)->nama,
                            "qty"               =>  (string)$row->berat,
                        ];
                    }else{
                        $berat_memar        +=  $row->berat;
                    }
                // check ayam KAMPUNG
                } elseif (substr($row->sku, 0, 5) == "12122" || substr($row->sku, 0, 5) == "12121") {
                    if($row->type=="hasil-produksi"){
                        $berat_fg           += $row->berat;

                        $arr_trf_fg[]  =   [
                            'internal_id_item'   =>  (string)Item::item_sku($row->sku)->netsuite_internal_id,
                            "item"               =>  (string)$row->sku,
                            "qty_to_transfer"    =>  (string)$row->berat,
                        ];
    
                        $arr_bb[]   =   [
                            "type"              =>  "Component",
                            "internal_id_item"  =>  (string)Item::item_sku($row->sku)->netsuite_internal_id,
                            "item"              =>  (string)$row->sku,
                            "description"       =>  (string)Item::item_sku($row->sku)->nama,
                            "qty"               =>  (string)$row->berat,
                        ];
                    }else{
                        $berat_kampung      +=  $row->berat;
                    }
                // check ayam PEJANTAN
                } elseif (substr($row->sku, 0, 5) == "12131" || substr($row->sku, 0, 5) == "12132") {
                    if($row->type=="hasil-produksi"){
                        $berat_fg           += $row->berat;

                        $arr_trf_fg[]  =   [
                            'internal_id_item'   =>  (string)Item::item_sku($row->sku)->netsuite_internal_id,
                            "item"               =>  (string)$row->sku,
                            "qty_to_transfer"    =>  (string)$row->berat,
                        ];
    
                        $arr_bb[]   =   [
                            "type"              =>  "Component",
                            "internal_id_item"  =>  (string)Item::item_sku($row->sku)->netsuite_internal_id,
                            "item"              =>  (string)$row->sku,
                            "description"       =>  (string)Item::item_sku($row->sku)->nama,
                            "qty"               =>  (string)$row->berat,
                        ];
                    }else{
                        $berat_pejantan     +=  $row->berat;
                    }
                // check ayam PARENT
                } elseif (substr($row->sku, 0, 5) == "12142" || substr($row->sku, 0, 5) == "12141") {
                    if($row->type=="hasil-produksi"){
                        $berat_fg           += $row->berat;

                        $arr_trf_fg[]  =   [
                            'internal_id_item'   =>  (string)Item::item_sku($row->sku)->netsuite_internal_id,
                            "item"               =>  (string)$row->sku,
                            "qty_to_transfer"    =>  (string)$row->berat,
                        ];
    
                        $arr_bb[]   =   [
                            "type"              =>  "Component",
                            "internal_id_item"  =>  (string)Item::item_sku($row->sku)->netsuite_internal_id,
                            "item"              =>  (string)$row->sku,
                            "description"       =>  (string)Item::item_sku($row->sku)->nama,
                            "qty"               =>  (string)$row->berat,
                        ];
                    }else{
                        $berat_parent       +=  $row->berat;
                    }
                }else {

                    // Logic untuk TI WO-2 evis
                    if($regu!="byproduct"){

                        $berat_fg           += $row->berat;
    
                        $arr_trf_fg[]  =   [
                            'internal_id_item'   =>  (string)Item::item_sku($row->sku)->netsuite_internal_id,
                            "item"               =>  (string)$row->sku,
                            "qty_to_transfer"    =>  (string)$row->berat,
                        ];

                        $arr_bb[]   =   [
                            "type"              =>  "Component",
                            "internal_id_item"  =>  (string)Item::item_sku($row->sku)->netsuite_internal_id,
                            "item"              =>  (string)$row->sku,
                            "description"       =>  (string)Item::item_sku($row->sku)->nama,
                            "qty"               =>  (string)$row->berat,
                        ];
    

                    }else{

                        if($row->type=="bahan-baku"){
                            $evis_berat_bb           += $row->berat;
                            $evis_array_bb[]  =   [
                                'internal_id_item'   =>  (string)Item::item_sku($row->sku)->netsuite_internal_id,
                                "item"               =>  (string)$row->sku,
                                "qty_to_transfer"    =>  (string)$row->berat,
                            ];
                        }else{

                            $evis_berat_fg           += $row->berat;
                            $evis_array_fg[]  =   [
                                'internal_id_item'   =>  (string)Item::item_sku($row->sku)->netsuite_internal_id,
                                "item"               =>  (string)$row->sku,
                                "qty_to_transfer"    =>  (string)$row->berat,
                            ];
                        }

                        $arr_bb[]   =   [
                            "type"              =>  "Component",
                            "internal_id_item"  =>  (string)Item::item_sku($row->sku)->netsuite_internal_id,
                            "item"              =>  (string)$row->sku,
                            "description"       =>  (string)Item::item_sku($row->sku)->nama,
                            "qty"               =>  (string)$row->berat,
                        ];

                    }

                }
            }

            //Konversi Component to RM
            if($berat_rm!="0" || $berat_memar!="0" || $berat_kampung!="0" || $berat_pejantan!="0" || $berat_parent!="0"){

                $arr_gabung = [];
                if($berat_rm!="0"){
                    $arr_gabung[] = [
                        "internal_id_item"  =>  (string)Item::item_sku("1100000001")->netsuite_internal_id ,
                        "item"              =>  "1100000001" ,
                        "qty_to_transfer"   =>  "$berat_rm"
                    ];
                }

                if($berat_memar!="0"){
                    $arr_gabung[] = [
                        "internal_id_item"  =>  (string)Item::item_sku("1100000003")->netsuite_internal_id ,
                        "item"              =>  "1100000003" ,
                        "qty_to_transfer"   =>  "$berat_memar"
                    ];
                }

                if($berat_kampung!="0"){
                    $arr_gabung[] = [
                        "internal_id_item"  =>  (string)Item::item_sku("1100000004")->netsuite_internal_id ,
                        "item"              =>  "1100000004" ,
                        "qty_to_transfer"   =>  "$berat_kampung"
                    ];
                }

                if($berat_pejantan!="0"){
                    $arr_gabung[] = [
                        "internal_id_item"  =>  (string)Item::item_sku("1100000005")->netsuite_internal_id ,
                        "item"              =>  "1100000005" ,
                        "qty_to_transfer"   =>  "$berat_pejantan"
                    ];
                }

                if($berat_parent!="0"){
                    $arr_gabung[] = [
                        "internal_id_item"  =>  (string)Item::item_sku("1100000009")->netsuite_internal_id ,
                        "item"              =>  "1100000009" ,
                        "qty_to_transfer"   =>  "$berat_parent"
                    ];
                }

                $trans  =   array_merge($arr_gabung, $arr_trf) ;

            }

            // Transfer FG to WIP non Karkas

            if($regu!="byproduct"){

                if($berat_fg>0){

                    $location   =   $nama_gudang_fg ;
                    $id_location=   Gudang::gudang_netid($location) ;
                    $ti = Netsuite::transfer_inventory_doc(NULL, NULL, 'ti_fg_prod_'.$regu, $id_location, $location, $id_location, Gudang::gudang_netid($nama_gudang_wip), $arr_trf_fg, null, $tanggal, $code);

                }

            }else{
                if($evis_berat_bb>0){
                    $location   =   $nama_gudang_bb ;
                    $id_location=   Gudang::gudang_netid($location) ;
                    $ti = Netsuite::transfer_inventory_doc(NULL, NULL, 'ti_bb_prod_'.$regu, $id_location, $location, $id_location, Gudang::gudang_netid($nama_gudang_wip), $evis_array_bb, null, $tanggal, $code);
                }
                if($evis_berat_fg>0){
                    $location   =   $nama_gudang_fg ;
                    $id_location=   Gudang::gudang_netid($location) ;
                    $ti = Netsuite::transfer_inventory_doc(NULL, NULL, 'ti_fg_prod_'.$regu, $id_location, $location, $id_location, Gudang::gudang_netid($nama_gudang_wip), $evis_array_fg, null, $tanggal, $code);
                }
            }


            if($berat_rm!="0" || $berat_memar!="0" || $berat_kampung!="0" || $berat_pejantan!="0" || $berat_parent!="0" || $berat_fg!="0"){

                $bb_gabung = [];
                if($berat_rm!="0"){
                    $bb_gabung[] = [
                        "type"              =>  "Component",
                        "internal_id_item"  =>  (string)Item::item_sku("1100000001")->netsuite_internal_id,
                        "item"              =>  "1100000001",
                        "description"       =>  "AYAM KARKAS BROILER (RM)",
                        "qty"               =>  "$berat_rm"
                    ];
                }
                if($berat_memar!="0"){
                    $bb_gabung[]  =   [
                        "type"              =>  "Component",
                        "internal_id_item"  =>  (string)Item::item_sku("1100000003")->netsuite_internal_id,
                        "item"              =>  "1100000003",
                        "description"       =>  "AYAM MEMAR (RM)",
                        "qty"               =>  "$berat_memar"
                    ];
                }
                if($berat_kampung!="0"){
                    $bb_gabung[]  =   [
                        "type"              =>  "Component",
                        "internal_id_item"  =>  (string)Item::item_sku("1100000004")->netsuite_internal_id,
                        "item"              =>  "1100000004",
                        "description"       =>  "AYAM KAMPUNG (RM)",
                        "qty"               =>  "$berat_kampung"
                    ];
                }

                if($berat_pejantan!="0"){
                    $bb_gabung[]  =   [
                        "type"              =>  "Component",
                        "internal_id_item"  =>  (string)Item::item_sku("1100000005")->netsuite_internal_id,
                        "item"              =>  "1100000005",
                        "description"       =>  "AYAM PEJANTAN (RM)",
                        "qty"               =>  "$berat_pejantan"
                    ];
                }
                if($berat_parent!="0"){
                    $bb_gabung[]  =   [
                        "type"              =>  "Component",
                        "internal_id_item"  =>  (string)Item::item_sku("1100000009")->netsuite_internal_id,
                        "item"              =>  "1100000009",
                        "description"       =>  "AYAM PARENT (RM)",
                        "qty"               =>  "$berat_parent"
                    ];
                }
                $bahanbaku  =   array_merge($bb_gabung, $arr_bb);

            }

            if($regu!="byproduct"){
                if($trans){
                    $label          =   "ti_bb_prod_".$regu;
                    $ti = Netsuite::transfer_inventory_doc($nama_tabel, $id_tabel, $label, $id_location, $location, $from, $to, $trans, null, $tanggal, $code);
                }
            }else{
                 $bahanbaku  =   $arr_bb;
            }

            if($ti ?? FALSE){
                // ===================    WORK ORDER    ===================

                $bom            =   Bom::where('netsuite_internal_id', $id_assembly)
                                    ->first();

                $nama_assembly  =   $bom->bom_name;

                $data_produksi  =   [];
                $transfer       =   [];
                $total          =   0;
                foreach ($produksi as $row) {
                    // $exp    =   json_decode($row->label);

                    if(Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL').' - EVIS')->first()->netsuite_internal_id==$id_assembly || Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL').' - NON KARKAS - BONELESS BROILER')->first()->netsuite_internal_id==$id_assembly){

                        $total    +=  $row->berat;
                        $data_produksi[]    =   [
                            "type"              =>  "Finished Goods",
                            "internal_id_item"  =>  (string)Item::item_sku($row->sku)->netsuite_internal_id,
                            "item"              =>  (string)$row->sku,
                            "description"       =>  (string)Item::item_sku($row->sku)->nama,
                            "qty"               =>  (string)$row->berat,
                        ];

                    }else{

                        $bom_item = BomItem::where('sku', $row->sku)->where('bom_id', $bom->id)->first();
                        if($bom_item){
                            if($bom_item->kategori=="By Product"){
                                $total;
                            }else{
                                $total    +=  $row->berat;
                            }
                        }else{
                            $total    +=  $row->berat;
                        }

                        $item_cat = Item::find($row->item_id);

                        $type = (($item_cat->category_id == 4) OR ($item_cat->category_id == 6) OR ($item_cat->category_id == 10) OR ($item_cat->category_id == 16)) ? "By Product" : "Finished Goods";
                        if($bom_item){
                            $type = $bom_item->kategori;
                        }

                        $data_produksi[]    =   [
                            "type"              =>  $type,
                            "internal_id_item"  =>  (string)Item::item_sku($row->sku)->netsuite_internal_id,
                            "item"              =>  (string)$row->sku,
                            "description"       =>  (string)Item::item_sku($row->sku)->nama,
                            "qty"               =>  (string)$row->berat,
                        ];

                    }


                    $transfer[] =   [
                        "internal_id_item"  =>  (string)Item::item_sku($row->sku)->netsuite_internal_id,
                        "item"              =>  (string)$row->sku,
                        "qty_to_transfer"   =>  (string)$row->berat
                    ];

                }

                $location       =   $nama_gudang_wip;
                $id_location    =   Gudang::gudang_netid($location);

                $component      =   [];

                // return $total;

                foreach ($bom->bomproses as $row) {
                    if($total==0){
                        $total = 1;
                    }
                    $component[] =   [
                        "type"              =>  "Component",
                        "internal_id_item"  =>  (string)Item::item_sku($row->sku)->netsuite_internal_id,
                        "item"              =>  $row->sku,
                        "description"       =>  (string)Item::item_sku($row->sku)->nama,
                        "qty"               =>  (string)Item::where('nama', 'AY - S')->first()->sku == $row->sku ? $row->qty_per_assembly : ($row->qty_per_assembly * $total),
                    ];
                }

                // dd($component, $plastik);
                $produksi       =   array_merge($bahanbaku, $plastik, $component, $data_produksi);

                $label          =   "wo-2-".$regu;
                $wo = Netsuite::work_order_doc($nama_tabel, $id_tabel, $label, $id_assembly, $nama_assembly, $id_location, $location, $produksi, $ti->id, $tanggal, $code);

                // ===================    WO - 2 - BUILD    ===================

                $label          =   "wo-2-build-".$regu;
                $wob = Netsuite::wo_build_doc($nama_tabel, $id_tabel, $label, $id_assembly, $nama_assembly, $id_location, $location, $total, $produksi, $wo->id, $tanggal, $code);

                // ===================    TRANSFER INVENTORY IN FINISHED GOOD    ===================

                $nama_tabel     =   "free_stock";
                $id_tabel       =   NULL;

                $from           =   $id_location ;

                $label          =   "ti_prod_fg_".$regu;
                $to             =   Gudang::gudang_netid($nama_gudang_fg);
                Netsuite::transfer_inventory_doc($nama_tabel, $id_tabel, $label, $id_location, $location, $from, $to, $transfer, $wob->id, $tanggal, $code);

                Freestock::whereDate('tanggal', $tanggal)->where('regu', $regu)->where('status', '3')->where('netsuite_send', NULL)->update(['netsuite_id' => $wo->id]);

                DB::commit();

            }

            return true;

        }else{

            return 'TIDAK ADA DATA';

        }

    }

    // public function store(Request $request){

    //     // return $request->all();


    //     $plastik        = $request->plastik ?? [];
    //     $finished_goods = $request->finished_goods ?? [];
    //     $component      = $request->component ?? [];
    //     $karkas         = $request->karkas ?? [];
    //     $memar          = $request->memar ?? [];
    //     $regu           = $request->regu ?? "admin";
    //     $tanggal        = $request->tanggal ?? date('Y-m-d');

    //     // Karkas
    //     $total_normal   = 0;
    //     foreach($karkas as $no => $k):
    //         $k_data = json_decode($k);
    //         $total_normal = $total_normal+(float)$k_data->qty;
    //     endforeach;

    //     // Memar
    //     $total_memar    = 0;
    //     foreach($memar as $no =>  $k):
    //         $k_data = json_decode($k);
    //         $total_memar = $total_memar+(float)$k_data->qty;
    //     endforeach;

    //     // Data Component
    //     $data_component = [];
    //     $total_component    = 0;
    //     foreach($component as $k):
    //         $k_data = json_decode($k);
    //         $data_component[]       = json_decode($k);
    //         $total_component = $total_component+(float)$k_data->qty;
    //     endforeach;

    //     // Finished Goods
    //     $data_finished_goods = [];
    //     $transfer_finished_goods = [];
    //     $total_fg = 0;

    //     foreach($finished_goods as $no => $k):
    //         $k_data = json_decode($k);
    //         $data_finished_goods[]  = json_decode($k);
    //         $total_fg = $total_fg+(float)$k_data->qty;
    //         // echo ++$no.".".$k_data->item." - ".$k_data->qty."<br>";
    //         $transfer_finished_goods[] =   [
    //             "internal_id_item"  =>  (string)Item::item_sku($k_data->item)->netsuite_internal_id,
    //             "item"              =>  $k_data->item,
    //             "qty_to_transfer"   =>  $k_data->qty
    //         ];

    //     endforeach;

    //     // Data plastik
    //     $data_plastik = [];
    //     foreach($plastik as $k):
    //         $data_plastik[]         = json_decode($k);
    //     endforeach;

    //     // Data Karkas
    //     $data_karkas     = [];
    //     $transfer_karkas = [];
    //     if($total_normal>0){
    //         $data_karkas[]  =   [
    //             "type"              =>  "Component",
    //             "internal_id_item"  =>  (string)Item::item_sku("1100000001")->netsuite_internal_id,
    //             "item"              =>  "1100000001",
    //             "description"       =>  "AYAM KARKAS BROILER (RM)",
    //             "qty"               =>  $total_normal,
    //         ];

    //         $transfer_karkas[] =   [
    //             "internal_id_item"  =>  (string)Item::item_sku("1100000001")->netsuite_internal_id,
    //             "item"              =>  "1100000001",
    //             "qty_to_transfer"   =>  $total_normal
    //         ];
    //     }

    //     // Data Memar
    //     $data_memar     = [];
    //     $transfer_memar = [];
    //     if($total_memar>0){
    //         $data_memar[]  =   [
    //             "type"              =>  "Component",
    //             "internal_id_item"  =>  (string)Item::item_sku("1100000003")->netsuite_internal_id,
    //             "item"              =>  "1100000003",
    //             "description"       =>  "AYAM MEMAR (RM)",
    //             "qty"               =>  $total_memar,
    //         ];

    //         $transfer_memar[] =   [
    //             "internal_id_item"  =>  (string)Item::item_sku("1100000003")->netsuite_internal_id,
    //             "item"              =>  "1100000003",
    //             "qty_to_transfer"   =>  $total_memar
    //         ];
    //     }

    //     if(count($transfer_karkas)>0){
    //         $location_fg       =   "CGL - Chiller Bahan Baku";
    //         $id_location_fg    =   Gudang::gudang_netid($location_fg);

    //         $label          =   "ti_bb_prod_".$regu;
    //         $to             =   Gudang::gudang_netid("CGL - Storage Produksi (WIP)");
    //         if($request->type!="export"){
    //             Netsuite::transfer_inventory_date(null, null, $label, $id_location_fg, $location_fg, $id_location_fg, $to, $transfer_karkas, NULL, $tanggal);
    //         }
    //     }
    //     if(count($transfer_memar)>0){
    //         $location_fg       =   "CGL - Chiller Bahan Baku";
    //         $id_location_fg    =   Gudang::gudang_netid($location_fg);

    //         $label          =   "ti_bb_prod_".$regu;
    //         $to             =   Gudang::gudang_netid("CGL - Storage Produksi (WIP)");
    //         if($request->type!="export"){
    //             Netsuite::transfer_inventory_date(null, null, $label, $id_location_fg, $location_fg, $id_location_fg, $to, $transfer_memar, NULL, $tanggal);
    //         }
    //     }

    //     $transfer_component = [];
    //     foreach($component as $k):
    //         $k_data = json_decode($k);
    //         $transfer_component[] =   [
    //             "internal_id_item"  =>  (string)Item::item_sku($k_data->item)->netsuite_internal_id,
    //             "item"              =>  $k_data->item,
    //             "qty_to_transfer"   =>  $k_data->qty
    //         ];
    //     endforeach;


    //     if(count($transfer_component)>0){
    //         $location_fg       =   "CGL - Chiller Finished Good";
    //         $id_location_fg    =   Gudang::gudang_netid($location_fg);

    //         $label          =   "ti_fg_prod_".$regu;
    //         $to             =   Gudang::gudang_netid("CGL - Storage Produksi (WIP)");
    //         if($request->type!="export"){
    //             Netsuite::transfer_inventory_date(null, null, $label, $id_location_fg, $location_fg, $id_location_fg, $to, $transfer_component, NULL, $tanggal);
    //         }
    //     }


    //     $bom            =   Bom::where('bom_name', $request->bom_name)->first();
    //     $id_assembly    =   $bom->netsuite_internal_id;
    //     $nama_assembly  =   $bom->bom_name ;

    //     $location       =   "CGL - Storage Produksi (WIP)";
    //     $id_location    =   Gudang::gudang_netid($location);


    //     $mp_oh      =   [];
    //     foreach ($bom->bomproses as $row) {

    //         $mp_oh[] =   [
    //             "type"              =>  "Component",
    //             "internal_id_item"  =>  (string)Item::item_sku($row->sku)->netsuite_internal_id,
    //             "item"              =>  $row->sku,
    //             "description"       =>  (string)Item::item_sku($row->sku)->nama,
    //             "qty"               =>  Item::where('nama', 'AY - S')->first()->sku == $row->sku ? $row->qty_per_assembly : ($row->qty_per_assembly * $total_fg),
    //         ];
    //     }

    //     $produksi       =   array_merge($data_karkas, $data_memar, $data_component, $data_finished_goods, $data_plastik, $mp_oh);

    //     // return $produksi;

    //     if($request->type=="export"){

    //         header("Content-type: application/csv");
    //         header("Content-Disposition: attachment; filename=".$regu."-wo-export-" . $tanggal . ".csv");
    //         $fp = fopen('php://output', 'w');
    //         fputcsv($fp, ["sep=,"]);

    //         $dds = array(
    //             "Type",
    //             "Internal ID Item",
    //             "Item",
    //             "Deskripsi",
    //             "Qty"
    //         );
    //         fputcsv($fp, $dds);

    //         $urut = 0;
    //         foreach($produksi as $no => $o):

    //             $dds = array(
    //                 $o->type ?? "",
    //                 $o->internal_id_item ?? "",
    //                 $o->item ?? "",
    //                 $o->description ?? "",
    //                 $o->qty ?? 0
    //             );
    //             fputcsv($fp, $dds);
    //         endforeach;

    //     }else{

    //         $label  =   "wo-2-".$regu;
    //         $wo = Netsuite::work_order_date(null, null, $label, $id_assembly, $nama_assembly, $id_location, $location, $produksi, null, $tanggal);

    //         // ===================    WO - 2 - BUILD    ===================

    //         $label  =   "wo-2-build-".$regu;
    //         $wob = Netsuite::wo_build_date(null, null, $label, $id_assembly, $nama_assembly, $id_location, $location, $total_fg, $produksi, $wo->id, $tanggal);

    //         // ===================    TRANSFER INVENTORY IN FINISHED GOOD    ===================
    //         $label          =   "ti_prod_fg_".$regu;
    //         $to             =   Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Chiller Finished Good");

    //         $from   =   $id_location;
    //         Netsuite::transfer_inventory_date(null, null, $label, $id_location, $location, $from, $to, $transfer_finished_goods, $wob->id, $tanggal);

    //         Freestock::whereDate('tanggal', $tanggal)->where('regu', $regu)->where('netsuite_id', null)->update(['netsuite_id' => $wo->id]);

    //         return back()->with('status', 1)->with('message', 'Pembuatan WO Berhasil');
    //     }

    // }

    public function export(Request $request){

        // return $request->all();

        $plastik        = $request->plastik ?? [];
        $finished_goods = $request->finished_goods ?? [];
        $component      = $request->component ?? [];
        $karkas         = $request->karkas ?? [];
        $memar          = $request->memar ?? [];
        $regu           = $request->regu ?? "admin";
        $tanggal        = $request->tanggal ?? date('Y-m-d');

        // Karkas
        $total_normal   = 0;
        foreach($karkas as $no => $k):
            $k_data = json_decode($k);
            $total_normal = $total_normal+(float)$k_data->qty;
        endforeach;

        // Memar
        $total_memar    = 0;
        foreach($memar as $no =>  $k):
            $k_data = json_decode($k);
            $total_memar = $total_memar+(float)$k_data->qty;
        endforeach;

        // Data Component
        $data_component = [];
        $total_component    = 0;
        foreach($component as $k):
            $k_data = json_decode($k);
            $data_component[]       = json_decode($k);
            $total_component = $total_component+(float)$k_data->qty;
        endforeach;

        // Finished Goods
        $data_finished_goods = [];
        $transfer_finished_goods = [];
        $total_fg = 0;

        foreach($finished_goods as $no => $k):
            $k_data = json_decode($k);
            $data_finished_goods[]  = json_decode($k);
            $total_fg = $total_fg+(float)$k_data->qty;
            // echo ++$no.".".$k_data->item." - ".$k_data->qty."<br>";
            $transfer_finished_goods[] =   [
                "internal_id_item"  =>  (string)Item::item_sku($k_data->item)->netsuite_internal_id,
                "item"              =>  $k_data->item,
                "qty_to_transfer"   =>  $k_data->qty
            ];

        endforeach;

        // Data plastik
        $data_plastik = [];
        foreach($plastik as $k):
            $data_plastik[]         = json_decode($k);
        endforeach;

        // Data Karkas
        $data_karkas     = [];
        $transfer_karkas = [];
        if($total_normal>0){
            $data_karkas[]  =   [
                "type"              =>  "Component",
                "internal_id_item"  =>  (string)Item::item_sku("1100000001")->netsuite_internal_id,
                "item"              =>  "1100000001",
                "description"       =>  "AYAM KARKAS BROILER (RM)",
                "qty"               =>  "$total_normal",
            ];

            $transfer_karkas[] =   [
                "internal_id_item"  =>  (string)Item::item_sku("1100000001")->netsuite_internal_id,
                "item"              =>  "1100000001",
                "qty_to_transfer"   =>  "$total_normal"
            ];
        }

        // Data Memar
        $data_memar     = [];
        $transfer_memar = [];
        if($total_memar>0){
            $data_memar[]  =   [
                "type"              =>  "Component",
                "internal_id_item"  =>  (string)Item::item_sku("1100000003")->netsuite_internal_id,
                "item"              =>  "1100000003",
                "description"       =>  "AYAM MEMAR (RM)",
                "qty"               =>  "$total_memar",
            ];

            $transfer_memar[] =   [
                "internal_id_item"  =>  (string)Item::item_sku("1100000003")->netsuite_internal_id,
                "item"              =>  "1100000003",
                "qty_to_transfer"   =>  "$total_memar"
            ];
        }

        if(count($transfer_karkas)>0){
            $location_fg       =   env('NET_SUBSIDIARY', 'CGL')." - Chiller Bahan Baku";
            $id_location_fg    =   Gudang::gudang_netid($location_fg);

            $label          =   "ti_bb_prod_".$regu;
            $to             =   Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Storage Produksi (WIP)");
            Netsuite::transfer_inventory_date(null, null, $label, $id_location_fg, $location_fg, $id_location_fg, $to, $transfer_karkas, NULL, $tanggal);
        }
        if(count($transfer_memar)>0){
            $location_fg       =   env('NET_SUBSIDIARY', 'CGL')." - Chiller Bahan Baku";
            $id_location_fg    =   Gudang::gudang_netid($location_fg);

            $label          =   "ti_bb_prod_".$regu;
            $to             =   Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Storage Produksi (WIP)");
            Netsuite::transfer_inventory_date(null, null, $label, $id_location_fg, $location_fg, $id_location_fg, $to, $transfer_memar, NULL, $tanggal);
        }

        $transfer_component = [];
        foreach($component as $k):
            $k_data = json_decode($k);
            $transfer_component[] =   [
                "internal_id_item"  =>  (string)Item::item_sku($k_data->item)->netsuite_internal_id,
                "item"              =>  $k_data->item,
                "qty_to_transfer"   =>  $k_data->qty
            ];
        endforeach;


        if(count($transfer_component)>0){
            $location_fg       =   env('NET_SUBSIDIARY', 'CGL')." - Chiller Finished Good";
            $id_location_fg    =   Gudang::gudang_netid($location_fg);

            $label          =   "ti_fg_prod_".$regu;
            $to             =   Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Storage Produksi (WIP)");
            Netsuite::transfer_inventory_date(null, null, $label, $id_location_fg, $location_fg, $id_location_fg, $to, $transfer_component, NULL, $tanggal);
        }


        $bom            =   Bom::where('bom_name', $request->bom_name)->first();
        $id_assembly    =   $bom->netsuite_internal_id;
        $nama_assembly  =   $bom->bom_name ;

        $location       =   env('NET_SUBSIDIARY', 'CGL')." - Storage Produksi (WIP)";
        $id_location    =   Gudang::gudang_netid($location);


        $mp_oh      =   [];
        foreach ($bom->bomproses as $row) {

            $mp_oh[] =   [
                "type"              =>  "Component",
                "internal_id_item"  =>  (string)Item::item_sku($row->sku)->netsuite_internal_id,
                "item"              =>  $row->sku,
                "description"       =>  (string)Item::item_sku($row->sku)->nama,
                "qty"               =>  (string)Item::where('nama', 'AY - S')->first()->sku == $row->sku ? $row->qty_per_assembly : ($row->qty_per_assembly * $total_fg),
            ];
        }

        $produksi       =   array_merge($data_karkas, $data_memar, $data_component, $data_finished_goods, $data_plastik, $mp_oh);


        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=".$regu."-wo-export-" . $tanggal . ".csv");
        $fp = fopen('php://output', 'w');
        fputcsv($fp, ["sep=,"]);

        $data = array(
            "No",
            "Type",
            "Internal ID Item",
            "Item",
            "Deskripsi",
            "Qty"
        );
        fputcsv($fp, $data);

        $urut = 0;
        foreach($produksi as $no => $o):
            fputcsv($fp, $o);
        endforeach;

        return "";

    }
}
