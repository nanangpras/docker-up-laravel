<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Production;
use App\Models\Item;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class Netsuite extends Model
{
    protected $table    =   'netsuite';
    use SoftDeletes;

    public function data_parent()
    {
        return $this->belongsTo('App\Models\Netsuite', 'paket_id');
    }

    public function dataUsers() {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function dataOrders() {
        return $this->belongsTo(Order::class, 'tabel_id', 'id');
    }

    public function dataProductions() {
        return $this->belongsTo(Production::class, 'tabel_id', 'id');
    }

    public function dataRetur() {
        return $this->belongsTo(Retur::class, 'tabel_id', 'id');
    }

    public function dataChillerTI() {
        return $this->belongsTo(Chiller::class, 'tabel_id', 'id');
    }

    public function dataProductGudang() {
        return $this->belongsTo(Product_gudang::class, 'tabel_id', 'id');
    }

    public function dataBahanBakuTI() {
        return $this->belongsTo(Bahanbaku::class, 'tabel_id', 'netsuite_id');
    }

    


    public function produksi()
    {
        return $this->belongsTo(Freestock::class, 'paket_id', 'netsuite_id');
    }

    public function data_children()
    {
        return $this->hasOne(Netsuite::class, 'paket_id', 'id')->with('data_children');
    }


    public static function header_netsuite($nonce, $script, $deploy)
    {
        $base_string =
            "POST&" . urlencode(env("NET_LINK", "https://6484226-sb1.restlets.api.netsuite.com/app/site/hosting/restlet.nl")) . "&" .
            urlencode(
                "deploy=" . $deploy
                    . "&oauth_consumer_key=" . env("NET_CONSUMER_KEY", "fbb25c6ad898fd159bc8c2a6a5d99fcc7f0e7b6b166fc36d5f8cb6f9750d2857")
                    . "&oauth_nonce=" . $nonce
                    . "&oauth_signature_method=" . 'HMAC-SHA256'
                    . "&oauth_timestamp=" . time()
                    . "&oauth_token=" . env("NET_TOKEN_ID", "1ef4cc048c4ce8d7895cf23a56aa00d254ffc98b4efb0f9ed8a54085f9c2f0b4")
                    . "&oauth_version=" . "1.0"
                    . "&realm=" . env("NET_ACCOUNT", "6484226_SB1")
                    . "&script=" . $script
            );
        $sig_string = urlencode(env("NET_CONSUMER_SECRET", "2fbbd63fc5dd7355f956f01b4790bf4553076f4689a475fc98b583a357e719a3")) . '&' . urlencode(env("NET_TOKEN_SECRET", "d3e785a6f8c5c3807ff0866b1c8c59445d3bea492cf276f01d1e1fc10e7c81af"));
        $signature = base64_encode(hash_hmac("SHA256", $base_string, $sig_string, true));

        $auth_header = "OAuth "
            . 'oauth_signature="' . rawurlencode($signature) . '", '
            . 'oauth_version="' . rawurlencode("1.0") . '", '
            . 'oauth_nonce="' . rawurlencode($nonce) . '", '
            . 'oauth_signature_method="' . rawurlencode('HMAC-SHA256') . '", '
            . 'oauth_consumer_key="' . rawurlencode(env("NET_CONSUMER_KEY", "fbb25c6ad898fd159bc8c2a6a5d99fcc7f0e7b6b166fc36d5f8cb6f9750d2857")) . '", '
            . 'oauth_token="' . rawurlencode(env("NET_TOKEN_ID", "1ef4cc048c4ce8d7895cf23a56aa00d254ffc98b4efb0f9ed8a54085f9c2f0b4")) . '", '
            . 'oauth_timestamp="' . rawurlencode(time()) . '", '
            . 'realm="' . rawurlencode(env("NET_ACCOUNT", "6484226_SB1")) . '"';

        return $auth_header;
    }


    public static function wo_1($id)
    {

        $nama_gudang_lb     = env('NET_SUBSIDIARY', 'CGL')." - Storage Live Bird";
        $nama_gudang_expedisi     = env('NET_SUBSIDIARY', 'CGL')." - Storage Expedisi";
        $nama_gudang_bb           = env('NET_SUBSIDIARY', 'CGL')." - Chiller Bahan Baku";
        $nama_gudang_fg           = env('NET_SUBSIDIARY', 'CGL')." - Chiller Finished Good";
        $nama_gudang_abf          = env('NET_SUBSIDIARY', 'CGL')." - Storage ABF";
        $nama_gudang_wip          = env('NET_SUBSIDIARY', 'CGL')." - Storage Produksi (WIP)";
        $nama_gudang_susut          = env('NET_SUBSIDIARY', 'CGL')." - Storage Susut";
        $nama_gudang_retur          = env('NET_SUBSIDIARY', 'CGL')." - Storage Retur";
        $nama_gudang_other          = env('NET_SUBSIDIARY', 'CGL')." - Storage Others";

        $data   =   Production::find($id);

        if (($data->evis_status == 1) and ($data->grading_status == 1) and ($data->lpah_status == 1)) {
            DB::beginTransaction();

            $tanggal    = date("Y-m-d", strtotime($data->prod_tanggal_potong));

            $normal =   Grading::where('trans_id', $data->id)
                        ->where('item_id', '!=', NULL)
                        ->where('jenis_karkas', 'normal')
                        ->where('grade_item', 'normal')
                        ->sum('berat_item');

            $utuh   =   Grading::where('trans_id', $data->id)
                        ->where('item_id', '!=', NULL)
                        ->where('jenis_karkas', 'utuh')
                        ->sum('berat_item');

            $memar  =   Grading::where('trans_id', $data->id)
                        ->where('item_id', '!=', NULL)
                        ->where('jenis_karkas', 'memar')
                        ->where('grade_item', 'memar')
                        ->sum('berat_item');

            $total  =   Grading::where('trans_id', $data->id)
                        ->where('item_id', '!=', NULL)
                        ->sum('berat_item');

            $pejantan =   Grading::where('trans_id', $data->id)
                        ->where('item_id', '!=', NULL)
                        ->where('grade_item', 'pejantan')
                        ->sum('berat_item');

            $kampung  =   Grading::where('trans_id', $data->id)
                        ->where('item_id', '!=', NULL)
                        ->where('grade_item', 'kampung')
                        ->sum('berat_item');

            $parent  =   Grading::where('trans_id', $data->id)
                        ->where('item_id', '!=', NULL)
                        ->where('grade_item', 'parent')
                        ->sum('berat_item');

            // INITIAL AWAL GRADING
            $data_grading       = array();
            $transfer_grading   = array();

            // FINISHED GOODS AYAM KARYAS BROILER (RM)
            if ($normal) {
                $data_grading[]  =   [
                    "type"              =>  "Finished Goods",
                    "internal_id_item"  =>  (string)Item::item_sku("1100000001")->netsuite_internal_id,
                    "item"              =>  "1100000001",
                    "description"       =>  "AYAM KARKAS BROILER (RM)",
                    "qty"               =>  "$normal",
                ];

                $transfer_grading[] =   [
                    "internal_id_item"  =>  (string)Item::item_sku("1100000001")->netsuite_internal_id,
                    "item"              =>  "1100000001",
                    "qty_to_transfer"   =>  "$normal"
                ];
            }

            // FINISHED GOODS AYAM UTUH (RM)
            if ($utuh) {
                $data_grading[]  =   [
                    "type"              =>  "Finished Goods",
                    "internal_id_item"  =>  (string)Item::item_sku("1100000002")->netsuite_internal_id,
                    "item"              =>  "1100000002",
                    "description"       =>  "AYAM UTUH (RM)",
                    "qty"               =>  "$utuh",
                ];

                $transfer_grading[] =   [
                    "internal_id_item"  =>  (string)Item::item_sku("1100000002")->netsuite_internal_id,
                    "item"              =>  "1100000002",
                    "qty_to_transfer"   =>  "$utuh"
                ];
            }

            // FINISHED GOOD AYAM MEMAR (RM) /
            if ($memar) {
                $data_grading[]  =   [
                    "type"              =>  "Finished Goods",
                    "internal_id_item"  =>  (string)Item::item_sku("1100000003")->netsuite_internal_id,
                    "item"              =>  "1100000003",
                    "description"       =>  "AYAM MEMAR (RM)",
                    "qty"               =>  "$memar",
                ];

                $transfer_grading[] =   [
                    "internal_id_item"  =>  (string)Item::item_sku("1100000003")->netsuite_internal_id,
                    "item"              =>  "1100000003",
                    "qty_to_transfer"   =>  "$memar"
                ];
            }

            // FINISHED GOODS AYAM KAMPUNG (RM)
            if ($kampung) {
                $data_grading[]  =   [
                    "type"              =>  "Finished Goods",
                    "internal_id_item"  =>  (string)Item::item_sku("1100000004")->netsuite_internal_id,
                    "item"              =>  "1100000004",
                    "description"       =>  "AYAM KAMPUNG (RM)",
                    "qty"               =>  "$kampung",
                ];

                $transfer_grading[] =   [
                    "internal_id_item"  =>  (string)Item::item_sku("1100000004")->netsuite_internal_id,
                    "item"              =>  "1100000004",
                    "qty_to_transfer"   =>  "$kampung"
                ];
            }

            // FINISHED GOODS AYAM KARYAS PEJANTAN (RM)
            if ($pejantan) {
                $data_grading[]  =   [
                    "type"              =>  "Finished Goods",
                    "internal_id_item"  =>  (string)Item::item_sku("1100000005")->netsuite_internal_id,
                    "item"              =>  "1100000005",
                    "description"       =>  "AYAM PEJANTAN (RM)",
                    "qty"               =>  "$pejantan",
                ];

                $transfer_grading[] =   [
                    "internal_id_item"  =>  (string)Item::item_sku("1100000005")->netsuite_internal_id,
                    "item"              =>  "1100000005",
                    "qty_to_transfer"   =>  "$pejantan"
                ];
            }

            // FINISHED GOODS AYAM PARENT (RM)
            if ($parent) {
                $data_grading[]  =   [
                    "type"              =>  "Finished Goods",
                    "internal_id_item"  =>  (string)Item::item_sku("1100000009")->netsuite_internal_id,
                    "item"              =>  "1100000009",
                    "description"       =>  "AYAM PARENT (RM)",
                    "qty"               =>  "$parent",
                ];

                $transfer_grading[] =   [
                    "internal_id_item"  =>  (string)Item::item_sku("1100000009")->netsuite_internal_id,
                    "item"              =>  "1100000009",
                    "qty_to_transfer"   =>  "$parent"
                ];
            }

            // ======================================================================
            // ==============================   EVIS   ==============================
            // ======================================================================

            $evis       =   Evis::where('production_id', $data->id)
                            ->get();

            $data_evis      =   [];
            $transfer_evis  =   [];
            foreach ($evis as $row) {
                $data_evis[]    =   [
                    "type"              =>  "By Product",
                    "internal_id_item"  =>  (string)$row->eviitem->netsuite_internal_id,
                    "item"              =>  (string)$row->eviitem->sku,
                    "description"       =>  (string)$row->eviitem->nama,
                    "qty"               =>  (string)$row->berat_item,
                ];

                $bom_item = BomItem::where('sku', $row->eviitem->sku)->first();
                if ($bom_item) {
                    if ($bom_item->kategori == "By Product") {
                        $total;
                    } else {
                        $total    +=  $row->berat;
                    }
                }

                $transfer_evis[]    =   [
                    "internal_id_item"  =>  (string)$row->eviitem->netsuite_internal_id,
                    "item"              =>  (string)$row->eviitem->sku,
                    "qty_to_transfer"   =>  (string)$row->berat_item
                ];
            }

            // ==========================     BOM     ==========================

            $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL')." - LIVEBIRD - AYAM KARKAS")
                                ->first();

            $nama_assembly  =   $bom->bom_name;
            $id_assembly    =   $bom->netsuite_internal_id;

            $component      =   [];
            foreach ($bom->bomproses as $row) {
                $component[] =   [
                    "type"              =>  "Component",
                    "internal_id_item"  =>  (string)Item::item_sku($row->sku)->netsuite_internal_id,
                    "item"              =>  $row->sku,
                    "description"       =>  (string)Item::item_sku($row->sku)->nama,
                    "qty"               =>  (string)Item::where('nama', 'ES BALOK')->first()->sku == $row->sku ? $row->qty_per_assembly : ($row->qty_per_assembly * $total),
                ];
            }

            $berat_wb = 0;

            if($data->po_jenis_ekspedisi == 'tangkap'){
                $berat_wb = $data->sc_berat_do;
            }else{
                $berat_wb = $data->lpah_berat_terima;
                // $berat_wb = $data->berat_bersih_lpah;
            }

            $component[] = [
                "type"              =>  "Component",
                "internal_id_item"  =>  (string)Item::item_sku($data->prodpur->item_po)->netsuite_internal_id,
                "item"              =>  $data->prodpur->item_po,
                "description"       =>  (string)Item::item_sku($data->prodpur->item_po)->nama,
                "qty"               =>  $berat_wb,
            ];

            $produksi       =   array_merge($component, $data_grading, $data_evis);
            $transfer       =   array_merge($transfer_grading, $transfer_evis);

            $nama_tabel     =   "productions";
            $id_tabel       =   $data->id;
            $location       =   env('NET_SUBSIDIARY', 'CGL')." - Storage Live Bird";
            $id_location    =   Gudang::gudang_netid($location);

            $label          =   "wo-1";
            $wo             =   Netsuite::work_order_doc($nama_tabel, $id_tabel, $label, $id_assembly, $nama_assembly, $id_location, $location, $produksi, null, $tanggal, $data->no_po);

            $label          =   "wo-1-build";
            $wob            =   Netsuite::wo_build_doc($nama_tabel, $id_tabel, $label, $id_assembly, $nama_assembly, $id_location, $location, $total, $produksi, $wo->id, $tanggal, $data->no_po);

            $label          =   "ti_livebird_bahanbaku";
            $from           =   $id_location;
            $to             =   Gudang::gudang_netid($nama_gudang_bb);
            $ti             =   Netsuite::transfer_inventory_doc($nama_tabel, $id_tabel, $label, $id_location, $location, $from, $to, $transfer, $wob->id, $tanggal, $data->no_po);

            $data->wo_netsuite_status = 1;
            $data->save();

            DB::commit();
        }
    }



    public static function wo_2($id, $id_assembly)
    {

        $nama_gudang_lb     = env('NET_SUBSIDIARY', 'CGL')." - Storage Live Bird";
        $nama_gudang_expedisi     = env('NET_SUBSIDIARY', 'CGL')." - Storage Expedisi";
        $nama_gudang_bb           = env('NET_SUBSIDIARY', 'CGL')." - Chiller Bahan Baku";
        $nama_gudang_fg           = env('NET_SUBSIDIARY', 'CGL')." - Chiller Finished Good";
        $nama_gudang_abf          = env('NET_SUBSIDIARY', 'CGL')." - Storage ABF";
        $nama_gudang_wip          = env('NET_SUBSIDIARY', 'CGL')." - Storage Produksi (WIP)";
        $nama_gudang_susut          = env('NET_SUBSIDIARY', 'CGL')." - Storage Susut";
        $nama_gudang_retur          = env('NET_SUBSIDIARY', 'CGL')." - Storage Retur";
        $nama_gudang_other          = env('NET_SUBSIDIARY', 'CGL')." - Storage Others";

        DB::beginTransaction();
        // ===================    TRANSFER INVENTORY IN WIP    ===================

        $data           =   Freestock::find($id);

        $code           =   'wo-2-'.$data->regu.'-'.$id;
        $regu           =   $data->regu;

        $nama_tabel     =   "free_stock";
        $id_tabel       =   $data->id;

        $location       =   $nama_gudang_bb;
        $id_location    =   Gudang::gudang_netid($location);
        $from           =   $id_location;
        $to             =   Gudang::gudang_netid($nama_gudang_wip);


        $arr_trf            =   [];
        $arr_bb             =   [];
        $berat_rm           =   0;
        $berat_memar        =   0;
        $berat_parent       =   0;
        $berat_pejantan     =   0;
        $berat_kampung      =   0;

        // Loop untuk component

        $jenis_bahan_baku = "";
        $kategori_bom     = "";
        foreach ($data->listfreestock as $row) {

            $jenis_bahan_baku = "";

            // Check ayam KARKAS dan ayam utuh
            if (substr($row->item->sku, 0, 5) == "12111" || substr($row->item->sku, 0, 5) == "12112") {
                $berat_rm   +=  $row->berat;
            //check ayam MEMAR
            } elseif (substr($row->item->sku, 0, 5) == "12113") {
                $berat_memar   +=  $row->berat;
            // check ayam KAMPUNG
            } elseif (substr($row->item->sku, 0, 5) == "12122" || substr($row->item->sku, 0, 5) == "12121") {
                $berat_kampung   +=  $row->berat;
            // check ayam PEJANTAN  
            } elseif (substr($row->item->sku, 0, 5) == "12131" || substr($row->item->sku, 0, 5) == "12132") {
                $berat_pejantan   +=  $row->berat;
            // check ayam PARENT
            } elseif (substr($row->item->sku, 0, 5) == "12142" || substr($row->item->sku, 0, 5) == "12141") {
                $berat_parent   +=  $row->berat;
            }else {
                $arr_trf[]  =   [
                    'internal_id_item'   =>  (string)$row->item->netsuite_internal_id,
                    "item"               =>  (string)$row->item->sku,
                    "qty_to_transfer"    =>  (string)$row->berat,
                ];

                $arr_bb[]   =   [
                    "type"              =>  "Component",
                    "internal_id_item"  =>  (string)$row->item->netsuite_internal_id,
                    "item"              =>  (string)$row->item->sku,
                    "description"       =>  (string)Item::item_sku($row->item->sku)->nama,
                    "qty"               =>  (string)$row->berat,
                ];

                if($row->chiller->type=='hasil-produksi'){

                    $jenis_bahan_baku = $row->chiller->type;
                    $dt_tr = [[
                        "internal_id_item"  =>  (string)$row->item->netsuite_internal_id ,
                        "item"              =>  (string)$row->item->sku ,
                        "qty_to_transfer"   =>  (string)$row->berat
                    ]];
                    $location   =   $nama_gudang_fg ;
                    $id_location=   Gudang::gudang_netid($location) ;
                    $ti = Netsuite::transfer_inventory_doc('chiller', ($row->chiller->id ?? NULL), 'ti_fg_bb_'.$regu, $id_location, $location, $id_location, Gudang::gudang_netid($nama_gudang_bb), $dt_tr, null, $data->tanggal ?? date('Y-m-d'), $code);
                }
            }

            if($row->item->category_id=="4"){
                $kategori_bom     = "bom-evis";
            }
        }
        // dd($berat_rm);

        if($jenis_bahan_baku=="hasil-produksi"){

            if($kategori_bom == "bom-evis"){
                $id_assembly    =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL').' - EVIS')->first()->netsuite_internal_id ?? "6170";
            }else{
                $id_assembly    =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL').' - NON KARKAS - BONELESS BROILER')->first()->netsuite_internal_id ?? "6151";
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

        }else{
            $trans = $arr_trf;
        }


        if($berat_rm!="0" || $berat_memar!="0" || $berat_kampung!="0" || $berat_pejantan!="0" || $berat_parent!="0"){

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

        }else{

            if(Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL').' - EVIS')->first()->netsuite_internal_id==$id_assembly){
                $bahanbaku = $arr_bb;
            }else{
                $bahanbaku = $arr_bb;
            }
        }

        $label          =   "ti_bb_prod_".$regu;
        $ti = Netsuite::transfer_inventory_doc($nama_tabel, $id_tabel, $label, $id_location, $location, $from, $to, $trans, null, $data->tanggal ?? date('Y-m-d'), $code);

        // ===================    WORK ORDER    ===================

        $bom            =   Bom::where('netsuite_internal_id', $id_assembly)
                            ->first();

        $nama_assembly  =   $bom->bom_name;


        $plastik        =   [];
        $data_produksi  =   [];
        $transfer       =   [];
        $total          =   0;
        foreach ($data->freetemp as $row) {
            $exp    =   json_decode($row->label);

            if ($exp->plastik->sku != NULL) {
                $item_bom   =   BomItem::select('qty_per_assembly')
                                ->where('bom_id', $bom->id)
                                ->where('sku', $exp->plastik->sku)
                                ->first();

                $plastik[]  =   [
                    "type"              =>  "Component",
                    "internal_id_item"  =>  (string)Item::item_sku($exp->plastik->sku)->netsuite_internal_id,
                    "item"              =>  $exp->plastik->sku,
                    "description"       =>  $exp->plastik->jenis,
                    "qty"               =>  (string)($item_bom->qty_per_assembly * $exp->plastik->qty),
                ];
            }

            if(Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL').' - EVIS')->first()->netsuite_internal_id==$id_assembly || Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL').' - NON KARKAS - BONELESS BROILER')->first()->netsuite_internal_id==$id_assembly){

                $total    +=  $row->berat;
                $data_produksi[]    =   [
                    "type"              =>  "Finished Goods",
                    "internal_id_item"  =>  (string)$row->item->netsuite_internal_id,
                    "item"              =>  (string)$row->item->sku,
                    "description"       =>  (string)$row->item->nama,
                    "qty"               =>  (string)$row->berat,
                ];

            }else{

                $bom_item = BomItem::where('sku', $row->item->sku)->where('bom_id', $bom->id)->first();
                if($bom_item){
                    if($bom_item->kategori=="By Product"){
                        $total;
                    }else{
                        $total    +=  $row->berat;
                    }
                }

                $type = (($row->item->category_id == 4) OR ($row->item->category_id == 6) OR ($row->item->category_id == 10) OR ($row->item->category_id == 16)) ? "By Product" : "Finished Goods";
                if($bom_item){
                    $type = $bom_item->kategori;
                }

                $data_produksi[]    =   [
                    "type"              =>  $type,
                    "internal_id_item"  =>  (string)$row->item->netsuite_internal_id,
                    "item"              =>  (string)$row->item->sku,
                    "description"       =>  (string)$row->item->nama,
                    "qty"               =>  (string)$row->berat,
                ];

            }


            $transfer[] =   [
                "internal_id_item"  =>  (string)$row->item->netsuite_internal_id,
                "item"              =>  (string)$row->item->sku,
                "qty_to_transfer"   =>  (string)$row->berat
            ];

        }

        $location       =   $nama_gudang_wip;
        $id_location    =   Gudang::gudang_netid($location);

        $component      =   [];
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

        $produksi       =   array_merge($bahanbaku, $plastik, $component, $data_produksi);

        $label          =   "wo-2-".$regu;
        $wo = Netsuite::work_order_doc($nama_tabel, $id_tabel, $label, $id_assembly, $nama_assembly, $id_location, $location, $produksi, $ti->id, $data->tanggal ?? date('Y-m-d'), $code);

        // ===================    WO - 2 - BUILD    ===================

        $label          =   "wo-2-build-".$regu;
        $wob = Netsuite::wo_build_doc($nama_tabel, $id_tabel, $label, $id_assembly, $nama_assembly, $id_location, $location, $total, $produksi, $wo->id, $data->tanggal ?? date('Y-m-d'), $code);

        // ===================    TRANSFER INVENTORY IN FINISHED GOOD    ===================

        $nama_tabel     =   "free_stock";
        $id_tabel       =   $data->id;

        $from           =   $id_location ;

        $label          =   "ti_prod_fg_".$regu;
        $to             =   Gudang::gudang_netid($nama_gudang_fg);
        Netsuite::transfer_inventory_doc($nama_tabel, $id_tabel, $label, $id_location, $location, $from, $to, $transfer, $wob->id, $data->tanggal ?? date('Y-m-d'), $code);

        DB::commit();
    }

    public static function wo_chiller_abf($chiller_id, $id)
    {

        $nama_gudang_lb     = env('NET_SUBSIDIARY', 'CGL')." - Storage Live Bird";
        $nama_gudang_expedisi     = env('NET_SUBSIDIARY', 'CGL')." - Storage Expedisi";
        $nama_gudang_bb           = env('NET_SUBSIDIARY', 'CGL')." - Chiller Bahan Baku";
        $nama_gudang_fg           = env('NET_SUBSIDIARY', 'CGL')." - Chiller Finished Good";
        $nama_gudang_abf          = env('NET_SUBSIDIARY', 'CGL')." - Storage ABF";
        $nama_gudang_wip          = env('NET_SUBSIDIARY', 'CGL')." - Storage Produksi (WIP)";
        $nama_gudang_susut          = env('NET_SUBSIDIARY', 'CGL')." - Storage Susut";
        $nama_gudang_retur          = env('NET_SUBSIDIARY', 'CGL')." - Storage Retur";
        $nama_gudang_other          = env('NET_SUBSIDIARY', 'CGL')." - Storage Others";

        DB::beginTransaction();

        // ===================    TRANSFER INVENTORY IN WIP    ===================

        $data           =   Freestock::find($id);

        $nama_tabel     =   "free_stock";
        $id_tabel       =   $data->id;

        $location       =   $nama_gudang_fg;
        $id_location    =   Gudang::gudang_netid($location);
        $from           =   $id_location ;
        $to             =   Gudang::gudang_netid($nama_gudang_wip);


        $arr_trf    =   [];
        $arr_bb     =   [];
        $berat_rm   =   0;
        foreach ($data->listfreestock as $row) {

            if ($row->item->category_id ==  1) {
                $berat_rm   +=  $row->berat;
            } else {
                $arr_trf[]  =   [
                    'net_id'        =>  (string)$row->item->netsuite_internal_id,
                    "item"          =>  (string)$row->item->sku,
                    "description"   =>  (string)Item::item_sku($row->item->sku)->nama,
                    "qty"           =>  (string)$row->berat,
                ];

                $arr_bb[]   =   [
                    "type"              =>  "Component",
                    "internal_id_item"  =>  (string)$row->item->netsuite_internal_id,
                    "item"              =>  (string)$row->item->sku,
                    "description"       =>  (string)Item::item_sku($row->item->sku)->nama,
                    "qty"               =>  (string)$row->berat,
                ];
            }
        }

        $trans  =   array_merge([[
                        "internal_id_item"  =>  (string)Item::item_sku("1100000001")->netsuite_internal_id ,
                        "item"              =>  "1100000001" ,
                        "qty_to_transfer"   =>  "$berat_rm"
                    ]], $arr_trf) ;

        $bahanbaku  =   array_merge([[
            "type"              =>  "Component",
            "internal_id_item"  =>  (string)Item::item_sku("1100000001")->netsuite_internal_id ,
            "item"              =>  "1100000001",
            "description"       =>  "AYAM KARKAS BROILER (RM)",
            "qty"               =>  "$berat_rm"
        ]], $arr_bb);

        $label          =   "ti_bb_prod";
        $ti = Netsuite::transfer_inventory($nama_tabel, $id_tabel, $label, $id_location, $location, $from, $to, $trans, null);

        // ===================    WORK ORDER    ===================

        $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL')." - AYAM KARKAS BROILER")
                            ->first();

        $nama_assembly  =   $bom->bom_name ;
        $id_assembly    =   $bom->netsuite_internal_id ;


        $plastik        =   [];
        $data_produksi  =   [];
        $transfer       =   [];
        $total          =   0;
        foreach ($data->listfreestock as $row) {

            $bom_item = BomItem::where('sku', $row->item->sku)->where('bom_id', $bom->id)->first();
            if($bom_item){
                if($bom_item->kategori=="By Product"){
                    $total;
                }else{
                    $total    +=  $row->berat;
                }
            }

            $type = (($row->item->category_id == 4) OR ($row->item->category_id == 6) OR ($row->item->category_id == 10) OR ($row->item->category_id == 16)) ? "By Product" : "Finished Goods";
            if($bom_item){
                $type = $bom_item->kategori;
            }

                $data_produksi[]    =   [
                    "type"              =>  $type,
                    "internal_id_item"  =>  (string)$row->item->netsuite_internal_id,
                    "item"              =>  (string)$row->item->sku,
                    "description"       =>  (string)$row->item->nama,
                    "qty"               =>  (string)$row->berat,
                ];

                $transfer[] =   [
                    "internal_id_item"  =>  (string)$row->item->netsuite_internal_id,
                    "item"              =>  (string)$row->item->sku,
                    "qty_to_transfer"   =>  (string)$row->berat
                ];
        }

        $location       =   $nama_gudang_wip;
        $id_location    =   Gudang::gudang_netid($location);

        $component      =   [];
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

        $produksi       =   array_merge($bahanbaku, $plastik, $component, $data_produksi);

        $label          =   "wo-2";
        $wo = Netsuite::work_order($nama_tabel, $id_tabel, $label, $id_assembly, $nama_assembly, $id_location, $location, $produksi, $ti->id);

        // ===================    WO - 2 - BUILD    ===================

        $label          =   "wo-2-build";
        $wob = Netsuite::wo_build($nama_tabel, $id_tabel, $label, $id_assembly, $nama_assembly, $id_location, $location, $total, $produksi, $wo->id);

        // ===================    TRANSFER INVENTORY IN FINISHED GOOD    ===================

        $nama_tabel     =   "free_stock";
        $id_tabel       =   $data->id;

        $from           =   $id_location;

        $label          =   "ti_prod_fg";
        $to             =   Gudang::gudang_netid($nama_gudang_fg);
        $ti = Netsuite::transfer_inventory($nama_tabel, $id_tabel, $label, $id_location, $location, $from, $to, $transfer, $wob->id);


        // WO - 3

        // ===================    TRANSFER INVENTORY IN WIP    ===================

        $data           =   Freestock::find($id);

        $nama_tabel     =   "wo_fg_abf";
        $id_tabel       =   $data->id;

        $location       =   $nama_gudang_fg;
        $id_location    =   Gudang::gudang_netid($location);
        $from           =   $id_location;
        $to             =   Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Storage ABF");


        $arr_trf    =   [];
        $arr_bb     =   [];
        $berat_rm   =   0;
        foreach ($data->listfreestock as $row) {

                $arr_trf[] =   [
                    "internal_id_item"  =>  (string)$row->item->netsuite_internal_id,
                    "item"              =>  (string)$row->item->sku,
                    "qty_to_transfer"   =>  (string)$row->berat
                ];

                $arr_bb[]   =   [
                    "type"              =>  "Component",
                    "internal_id_item"  =>  (string)$row->item->netsuite_internal_id,
                    "item"              =>  (string)$row->item->sku,
                    "description"       =>  (string)Item::item_sku($row->item->sku)->nama,
                    "qty"               =>  (string)$row->berat,
                ];
        }

        $trans  =   array_merge([], $arr_trf) ;

        $bahanbaku  =   array_merge([], $arr_bb);

        $label          =   "ti_fg_abf";
        $ti = Netsuite::transfer_inventory($nama_tabel, $id_tabel, $label, $id_location, $location, $from, $to, $trans, $ti->id);

        // ===================    WORK ORDER    ===================

        $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL')." - AYAM KARKAS FROZEN")
                            ->first();

        $nama_assembly  =   $bom->bom_name ;
        $id_assembly    =   $bom->netsuite_internal_id ;


        $plastik        =   [];
        $data_produksi  =   [];
        $transfer       =   [];
        $total          =   0;
        foreach ($data->freetemp as $row) {

                $total    +=  $row->berat;

                $data_produksi[]    =   [
                    "type"              =>  'Finished Goods',
                    "internal_id_item"  =>  (string)$row->item->netsuite_internal_id,
                    "item"              =>  (string)$row->item->sku,
                    "description"       =>  (string)$row->item->nama,
                    "qty"               =>  (string)$row->berat,
                ];

                $transfer[] =   [
                    "internal_id_item"  =>  (string)$row->item->netsuite_internal_id,
                    "item"              =>  (string)$row->item->sku,
                    "qty_to_transfer"   =>  (string)$row->berat
                ];
        }

        $location       =   env('NET_SUBSIDIARY', 'CGL')." - Storage ABF";
        $id_location    =   Gudang::gudang_netid($location);

        $component      =   [];
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

        $produksi       =   array_merge($bahanbaku, $plastik, $component, $data_produksi);

        $label          =   "wo-3";
        $wo = Netsuite::work_order($nama_tabel, $id_tabel, $label, $id_assembly, $nama_assembly, $id_location, $location, $produksi, $ti->id);

        // ===================    WO - 2 - BUILD    ===================

        $label          =   "wo-3-build";
        $wob = Netsuite::wo_build($nama_tabel, $id_tabel, $label, $id_assembly, $nama_assembly, $id_location, $location, $total, $produksi, $wo->id);

        // ===================    TRANSFER INVENTORY IN FINISHED GOOD    ===================
        // $nama_tabel     =   "wo_fg_abf";
        // $id_tabel       =   $data->id;

        // $from           =   "120";

        // $label          =   "ti_produksi_abf";
        // $to             =   "123";
        // Netsuite::transfer_inventory($nama_tabel, $id_tabel, $label, $id_location, $location, $from, $to, $transfer, $wob->id);

        DB::commit();
    }

    public static function wo_chiller_abf_fg($chiller_id, $id)
    {

        $nama_gudang_lb     = env('NET_SUBSIDIARY', 'CGL')." - Storage Live Bird";
        $nama_gudang_expedisi     = env('NET_SUBSIDIARY', 'CGL')." - Storage Expedisi";
        $nama_gudang_bb           = env('NET_SUBSIDIARY', 'CGL')." - Chiller Bahan Baku";
        $nama_gudang_fg           = env('NET_SUBSIDIARY', 'CGL')." - Chiller Finished Good";
        $nama_gudang_abf          = env('NET_SUBSIDIARY', 'CGL')." - Storage ABF";
        $nama_gudang_wip          = env('NET_SUBSIDIARY', 'CGL')." - Storage Produksi (WIP)";
        $nama_gudang_susut          = env('NET_SUBSIDIARY', 'CGL')." - Storage Susut";
        $nama_gudang_retur          = env('NET_SUBSIDIARY', 'CGL')." - Storage Retur";
        $nama_gudang_other          = env('NET_SUBSIDIARY', 'CGL')." - Storage Others";

        DB::beginTransaction();

        // WO - 3

        // ===================    TRANSFER INVENTORY IN WIP    ===================

        $data           =   Freestock::find($id);

        $nama_tabel     =   "wo_fg_abf";
        $id_tabel       =   $data->id;

        $location       =   $nama_gudang_fg;
        $id_location    =   Gudang::gudang_netid($location);
        $from           =   $id_location;
        $to             =   Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Storage ABF");


        $arr_trf    =   [];
        $arr_bb     =   [];
        $berat_rm   =   0;
        $bom_kategori = "";
        foreach ($data->listfreestock as $row) {
                $bom_kategori = $row->item->category_id;
                $arr_trf[] =   [
                    "internal_id_item"  =>  (string)$row->item->netsuite_internal_id,
                    "item"              =>  (string)$row->item->sku,
                    "qty_to_transfer"   =>  (string)$row->berat
                ];

                $arr_bb[]   =   [
                    "type"              =>  "Component",
                    "internal_id_item"  =>  (string)$row->item->netsuite_internal_id,
                    "item"              =>  (string)$row->item->sku,
                    "description"       =>  (string)Item::item_sku($row->item->sku)->nama,
                    "qty"               =>  (string)$row->berat,
                ];
        }

        $trans  =   array_merge([], $arr_trf) ;

        $bahanbaku  =   array_merge([], $arr_bb);

        $label          =   "ti_fg_abf";
        $ti = Netsuite::transfer_inventory($nama_tabel, $id_tabel, $label, $id_location, $location, $from, $to, $trans, NULL);

        // ===================    WORK ORDER    ===================

        $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL')." - AYAM KARKAS FROZEN")
        ->first();
        if($bom_kategori!=""){
            if($bom_kategori=="5"){
                $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL')." - BONELESS BROILER FROZEN")
                ->first();
            }elseif($bom_kategori=="3"){
                $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL')." - AYAM PARTING MARINASI BROILER FROZEN")
                ->first();
            }elseif($bom_kategori=="2"){
                $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL')." - AYAM PARTING BROILER FROZEN")
                ->first();
            }else{
                $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL')." - AYAM KARKAS FROZEN")
                ->first();
            }
        }

        $nama_assembly  =   $bom->bom_name ;
        $id_assembly    =   $bom->netsuite_internal_id ;


        $plastik        =   [];
        $data_produksi  =   [];
        $transfer       =   [];
        $total          =   0;
        foreach ($data->freetemp as $row) {

                $total    +=  $row->berat;

                $data_produksi[]    =   [
                    "type"              =>  'Finished Goods',
                    "internal_id_item"  =>  (string)$row->item->netsuite_internal_id,
                    "item"              =>  (string)$row->item->sku,
                    "description"       =>  (string)$row->item->nama,
                    "qty"               =>  (string)$row->berat,
                ];

                $transfer[] =   [
                    "internal_id_item"  =>  (string)$row->item->netsuite_internal_id,
                    "item"              =>  (string)$row->item->sku,
                    "qty_to_transfer"   =>  (string)$row->berat
                ];
        }

        $location       =   env('NET_SUBSIDIARY', 'CGL')." - Storage ABF";
        $id_location    =   Gudang::gudang_netid($location);

        $component      =   [];
        foreach ($bom->bomproses as $row) {
            if($total==0){
                $total = 1;
            }
            $component[] =   [
                "type"              =>  "Component",
                "internal_id_item"  =>  (string)Item::item_sku($row->sku)->netsuite_internal_id,
                "item"              =>  $row->sku,
                "description"       =>  (string)Item::item_sku($row->sku)->nama,
                "qty"               =>  (string)(Item::where('nama', 'AY - S')->first()->sku == $row->sku) ? $row->qty_per_assembly : ($row->qty_per_assembly * $total),
            ];
        }

        $produksi       =   array_merge($bahanbaku, $plastik, $component, $data_produksi);

        $label          =   "wo-3";
        $wo = Netsuite::work_order($nama_tabel, $id_tabel, $label, $id_assembly, $nama_assembly, $id_location, $location, $produksi, $ti->id);

        // ===================    WO - 2 - BUILD    ===================

        $label          =   "wo-3-build";
        $wob = Netsuite::wo_build($nama_tabel, $id_tabel, $label, $id_assembly, $nama_assembly, $id_location, $location, $total, $produksi, $wo->id);

        // ===================    TRANSFER INVENTORY IN FINISHED GOOD    ===================
        // $nama_tabel     =   "wo_fg_abf";
        // $id_tabel       =   $data->id;

        // $from           =   "120";

        // $label          =   "ti_produksi_abf";
        // $to             =   "123";
        // Netsuite::transfer_inventory($nama_tabel, $id_tabel, $label, $id_location, $location, $from, $to, $transfer, $wob->id);

        DB::commit();
    }




    public static function work_order($table, $id, $label, $id_assembly, $item_assembly, $id_location, $location, $component, $paket_id)
    {
        $netsuite                   =   new Netsuite;
        $netsuite->record_type      =   "work_order";
        $netsuite->label            =   "$label";
        $netsuite->trans_date       =   Carbon::now();
        $netsuite->user_id          =   Auth::user()->id ?? NULL;
        $netsuite->tabel            =   "$table";
        $netsuite->paket_id         =   "$paket_id";
        $netsuite->tabel_id         =   $id;
        $netsuite->subsidiary_id    =   env("NET_SUBSIDIARY_ID", "2");
        $netsuite->subsidiary       =   env("NET_SUBSIDIARY", "CGL");
        $netsuite->id_location      =   "$id_location";
        $netsuite->location         =   "$location";

        if (!$netsuite->save()) {
            DB::rollBack();
            return back()->with('status', 2)->with('message', 'Proses gagal');
        }

        $net        =   Netsuite::find($netsuite->id);

        $total = 0;

        $data_component = json_decode(json_encode($component));

        foreach($data_component as $row):
            if($row->type=="Finished Goods"){
                $total = $total+$row->qty;
            }
        endforeach;

        $data_wo    =   [
            "record_type"     =>     "work_order",
            "data"          =>  [
                [
                    "appsid"                    =>  env('NET_SUBSIDIARY', 'CGL')."-".(string)$netsuite->id,
                    "internal_id_subsidiary"    =>  env("NET_SUBSIDIARY_ID", "2"),
                    "subsidiary"                =>  env("NET_SUBSIDIARY", "CGL"),
                    "transaction_date"          =>  date("d-M-Y"),
                    "internal_id_customer"      =>  "",
                    "customer"                  =>  "",
                    "id_item_assembly"          =>  "$id_assembly",
                    "item_assembly"             =>  "$item_assembly",
                    "id_location"               =>  "$id_location",
                    "location"                  =>  "$location",
                    "plan_qty"                  =>  "$total",
                    "items"                     =>  $component
                ]
            ]
        ];

        $net->script            =   '209';
        $net->deploy            =   '1';
        $net->data_content      =   json_encode($data_wo);
        $net->status            =   5;

        if (!$net->save()) {
            DB::rollBack();
            return back()->with('status', 2)->with('message', 'Proses gagal');
        }

        return $net;
    }




    public static function wo_build($tabel, $id, $label, $id_assembly, $item_assembly, $id_location, $location, $total, $array, $paket_id)
    {
        $netsuite                   =   new Netsuite;
        $netsuite->record_type      =   "wo_build";
        $netsuite->label            =   "$label";
        $netsuite->trans_date       =   Carbon::now();
        $netsuite->user_id          =   Auth::user()->id ?? NULL;
        $netsuite->tabel            =   "$tabel";
        $netsuite->paket_id         =   "$paket_id";
        $netsuite->tabel_id         =   $id;
        $netsuite->subsidiary_id    =   env("NET_SUBSIDIARY_ID", "2");
        $netsuite->subsidiary       =   env("NET_SUBSIDIARY", "CGL");
        $netsuite->id_location      =   "$id_location";
        $netsuite->location         =   "$location";

        if (!$netsuite->save()) {
            DB::rollBack();
            return back()->with('status', 2)->with('message', 'Proses gagal');
        }

        $net    =   Netsuite::find($netsuite->id);

        $data_component = json_decode(json_encode($array));

        $qty_to_build = 0;
        foreach($data_component as $row):
            if($row->type=="Finished Goods"){
                $qty_to_build = $qty_to_build+$row->qty;
            }
        endforeach;

        $result =   [
            "record_type"     =>     "wo_build",
            "data"          =>  [
                [
                    "appsid"                    =>  env('NET_SUBSIDIARY', 'CGL')."-".(string)$netsuite->id,
                    "transaction_date"          =>  date("d-M-Y"),
                    "qty_to_build"              =>  "$qty_to_build",
                    "created_from_wo"           =>  "",
                    "items"                     =>  $array
                ]
            ]
        ];

        $net->script            =   '215';
        $net->deploy            =   '1';
        $net->data_content      =   json_encode($result);
        $net->status            =   5;

        if (!$net->save()) {
            DB::rollBack();
            return back()->with('status', 2)->with('message', 'Proses gagal');
        }
        return $net;
    }

    public static function work_order_doc($table, $id, $label, $id_assembly, $item_assembly, $id_location, $location, $component, $paket_id, $tanggal, $doc)
    {
        $netsuite                   =   new Netsuite;
        $netsuite->record_type      =   "work_order";
        $netsuite->label            =   "$label";
        $netsuite->document_code    =   "$doc";
        $netsuite->trans_date       =   date("Y-m-d", strtotime($tanggal));
        $netsuite->user_id          =   Auth::user()->id ?? NULL;
        $netsuite->tabel            =   "$table";
        $netsuite->paket_id         =   "$paket_id";
        $netsuite->tabel_id         =   $id;
        $netsuite->subsidiary_id    =   env("NET_SUBSIDIARY_ID", "2");
        $netsuite->subsidiary       =   env("NET_SUBSIDIARY", "CGL");
        $netsuite->id_location      =   "$id_location";
        $netsuite->location         =   "$location";

        if (!$netsuite->save()) {
            DB::rollBack();
            return back()->with('status', 2)->with('message', 'Proses gagal');
        }

        $net        =   Netsuite::find($netsuite->id);

        $total = 0;

        $data_component = json_decode(json_encode($component));

        foreach($data_component as $row):
            if($row->type=="Finished Goods"){
                $total = $total+(float)$row->qty;
            }
        endforeach;

        $data_wo    =   [
            "record_type"     =>     "work_order",
            "data"          =>  [
                [
                    "appsid"                    =>  env('NET_SUBSIDIARY', 'CGL')."-".(string)$netsuite->id,
                    "internal_id_subsidiary"    =>  env("NET_SUBSIDIARY_ID", "2"),
                    "subsidiary"                =>  env("NET_SUBSIDIARY", "CGL"),
                    "transaction_date"          =>  date("d-M-Y", strtotime($tanggal)),
                    "internal_id_customer"      =>  "",
                    "customer"                  =>  "",
                    "id_item_assembly"          =>  "$id_assembly",
                    "item_assembly"             =>  "$item_assembly",
                    "id_location"               =>  "$id_location",
                    "location"                  =>  "$location",
                    "plan_qty"                  =>  "$total",
                    "items"                     =>  $component
                ]
            ]
        ];

        $net->script            =   '209';
        $net->deploy            =   '1';
        $net->data_content      =   json_encode($data_wo);
        $net->status            =   5;

        if (!$net->save()) {
            DB::rollBack();
            return back()->with('status', 2)->with('message', 'Proses gagal');
        }

        return $net;
    }




    public static function wo_build_doc($tabel, $id, $label, $id_assembly, $item_assembly, $id_location, $location, $total, $array, $paket_id, $tanggal, $doc)
    {
        $netsuite                   =   new Netsuite;
        $netsuite->record_type      =   "wo_build";
        $netsuite->label            =   "$label";
        $netsuite->document_code    =   "$doc";
        $netsuite->trans_date       =   date("Y-m-d", strtotime($tanggal));
        $netsuite->user_id          =   Auth::user()->id ?? NULL;
        $netsuite->tabel            =   "$tabel";
        $netsuite->paket_id         =   "$paket_id";
        $netsuite->tabel_id         =   $id;
        $netsuite->subsidiary_id    =   env("NET_SUBSIDIARY_ID", "2");
        $netsuite->subsidiary       =   env("NET_SUBSIDIARY", "CGL");
        $netsuite->id_location      =   "$id_location";
        $netsuite->location         =   "$location";

        if (!$netsuite->save()) {
            DB::rollBack();
            return back()->with('status', 2)->with('message', 'Proses gagal');
        }

        $net    =   Netsuite::find($netsuite->id);

        $data_component = json_decode(json_encode($array));

        $qty_to_build = 0;
        foreach($data_component as $row):
            if($row->type=="Finished Goods"){
                $qty_to_build = $qty_to_build+(integer)$row->qty;
            }
        endforeach;

        $result =   [
            "record_type"     =>     "wo_build",
            "data"          =>  [
                [
                    "appsid"                    =>  env('NET_SUBSIDIARY', 'CGL')."-".(string)$netsuite->id,
                    "transaction_date"          =>  date("d-M-Y", strtotime($tanggal)),
                    "qty_to_build"              =>  "$qty_to_build",
                    "created_from_wo"           =>  "",
                    "items"                     =>  $array
                ]
            ]
        ];

        $net->script            =   '215';
        $net->deploy            =   '1';
        $net->data_content      =   json_encode($result);
        $net->status            =   5;

        if (!$net->save()) {
            DB::rollBack();
            return back()->with('status', 2)->with('message', 'Proses gagal');
        }
        return $net;
    }

    public static function transfer_inventory($tabel, $id, $label, $id_location, $location, $from, $to, $data, $paket_id)
    {
        $netsuite                   =   new Netsuite;
        $netsuite->record_type      =   "transfer_inventory";
        $netsuite->label            =   "$label";
        $netsuite->trans_date       =   Carbon::now();
        $netsuite->user_id          =   Auth::user()->id ?? NULL;
        $netsuite->tabel            =   "$tabel";
        $netsuite->paket_id         =   "$paket_id";
        $netsuite->tabel_id         =   $id;
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
                    "transaction_date"          =>  date("d-M-Y"),
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

    public static function transfer_inventory_doc($tabel, $id, $label, $id_location, $location, $from, $to, $data, $paket_id, $tanggal, $doc)
    {
        $netsuite                   =   new Netsuite;
        $netsuite->record_type      =   "transfer_inventory";
        $netsuite->label            =   "$label";
        $netsuite->trans_date       =   date("Y-m-d", strtotime($tanggal));
        $netsuite->user_id          =   Auth::user()->id ?? NULL;
        $netsuite->tabel            =   "$tabel";
        $netsuite->document_code    =   "$doc";
        $netsuite->paket_id         =   "$paket_id";
        $netsuite->tabel_id         =   $id;
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
                    "transaction_date"          =>  date("d-M-Y", strtotime($tanggal)),
                    "memo"                      =>  "$doc",
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

    public static function retur($retur_id){

        $nama_gudang_lb     = env('NET_SUBSIDIARY', 'CGL')." - Storage Live Bird";
        $nama_gudang_expedisi     = env('NET_SUBSIDIARY', 'CGL')." - Storage Expedisi";
        $nama_gudang_bb           = env('NET_SUBSIDIARY', 'CGL')." - Chiller Bahan Baku";
        $nama_gudang_fg           = env('NET_SUBSIDIARY', 'CGL')." - Chiller Finished Good";
        $nama_gudang_abf          = env('NET_SUBSIDIARY', 'CGL')." - Storage ABF";
        $nama_gudang_wip          = env('NET_SUBSIDIARY', 'CGL')." - Storage Produksi (WIP)";
        $nama_gudang_susut          = env('NET_SUBSIDIARY', 'CGL')." - Storage Susut";
        $nama_gudang_retur          = env('NET_SUBSIDIARY', 'CGL')." - Storage Retur";
        $nama_gudang_other          = env('NET_SUBSIDIARY', 'CGL')." - Storage Others";

        DB::beginTransaction();

        $retur           =   Retur::find($retur_id);
        $code            =   "RETUR-".$retur->id;

        // ===================    RETUR AUTHORIZATION    ===================

        $nama_tabel             = "retur";
        $id_tabel               = $retur_id;
        $label                  = "return_authorization";
        $ra = Netsuite::return_authorization($nama_tabel, $id_tabel, $label, null, $retur->tanggal_retur ?? date('Y-m-d'), $code);

        // ===================    RECEIPT RETURN   ===================

        $id_tabel_receipt               = $retur_id;
        $nama_tabel_receipt             = "retur";
        $label_receipt                  = "receipt_return";
        $rr = Netsuite::receipt_return($nama_tabel_receipt, $id_tabel_receipt, $label_receipt, $ra->id, $retur->tanggal_retur ?? date('Y-m-d'), $code);

        // ===================    TRANSFER INVENTORY IN ITEM    ===================

        $nama_tabel     =   "retur";
        $id_tabel       =   $retur->id;

        if ($retur->id_so != NULL || $retur->id_so != "") {
            foreach($retur->grup_item_retur as $row):
    
                $location       =   $nama_gudang_retur;
                $id_location    =   Gudang::gudang_netid($location);
                $from           =   $id_location ;
    
                $transfer = array();
                // $retur_item_baru = ReturItem::where('item_id', $row->item_id)->where('line_request', $row->line_request)->where('retur_id', $retur->id)->first();
                $retur_item_baru = ReturItem::where('line_request', $row->line_request)->where('retur_id', $retur->id)->first();
    
                // $cekLog = Adminedit::where('table_name', 'retur_item')->where('type','retur')
                //             ->where('table_id', $row->orderitem->item_id)->where('activity', 'Retur Salah Item/Tidak Sesuai Pesanan')->first();
    
                //Cek untuk tukar item, item aslinya
                $item_retur      = Item::item_sku($row->sku);
                // dd($retur_item_baru);
    
                $transfer[] = array(
                    "internal_id_item"      => (string)$item_retur->netsuite_internal_id,
                    "item"                  => (string)$item_retur->sku,
                    "qty_to_transfer"       => round($row->abot, 2)
                );
                
    
                if($retur_item_baru && ($retur_item_baru->catatan == 'Salah Item' || $retur_item_baru->catatan == 'Barang Tidak Sesuai Pesanan/Order')){
                    $cekLog     = Adminedit::where('table_name', 'retur_item')->where('type','retur')
                                                            ->where('table_id', $retur_item_baru->id)->where('activity', 'Retur Salah Item/Tidak Sesuai Pesanan')->first();
                    if($cekLog){
                            Netsuite::wo_retur_tukaritem($nama_tabel, $id_tabel, $transfer, $retur->tanggal_retur ?? date('Y-m-d'), $code, $row->line_request, $retur_item_baru->item_id, $row->unit);
                            // dd($retur_item_baru->item_id);
                    }
                }

    
    
                if ($row->unit == 'chillerfg') {
                    $label          =   "ti_retur_fg";
                    $to             =   Gudang::gudang_netid($nama_gudang_fg);
                    Netsuite::transfer_inventory_doc($nama_tabel, $id_tabel, $label, $id_location, $location, $from, $to, $transfer, $rr->id, $retur->tanggal_retur ?? date('Y-m-d'), $code);
                }elseif($row->unit == 'chillerbb') {
                    $label          =   "ti_retur_chillerbb";
                    $to             =   Gudang::gudang_netid($nama_gudang_bb);
                    Netsuite::transfer_inventory_doc($nama_tabel, $id_tabel, $label, $id_location, $location, $from, $to, $transfer, $rr->id, $retur->tanggal_retur ?? date('Y-m-d'), $code);
                    if($item_retur->category_id==1){
                        Netsuite::wo_retur($nama_tabel, $id_tabel, $transfer, $retur->tanggal_retur ?? date('Y-m-d'), $code);
                    }
    
                }elseif($row->unit == 'musnahkan') {
    
                    $label          =   "ti_retur_storage_susut";
                    $to             =   Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Storage Susut");
                    Netsuite::transfer_inventory_doc($nama_tabel, $id_tabel, $label, $id_location, $location, $from, $to, $transfer, $rr->id, $retur->tanggal_retur ?? date('Y-m-d'), $code);
    
                } else {
                    $label          =   "ti_retur_abf";
                    $to             =   Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Storage ABF");
                    Netsuite::transfer_inventory_doc($nama_tabel, $id_tabel, $label, $id_location, $location, $from, $to, $transfer, $rr->id, $retur->tanggal_retur ?? date('Y-m-d'), $code);
                }
    
            endforeach;
        
        } else {
            foreach($retur->grup_retur_nonso as $row):
    
                $location       =   $nama_gudang_retur;
                $id_location    =   Gudang::gudang_netid($location);
                $from           =   $id_location ;
    
                $transfer = array();

                $item_retur      = Item::item_sku($row->sku);
                // dd($retur_item_baru);
    
                $transfer[] = array(
                    "internal_id_item"      => (string)$item_retur->netsuite_internal_id,
                    "item"                  => (string)$item_retur->sku,
                    "qty_to_transfer"       => round($row->abot, 2)
                );
                
    
                if ($row->unit == 'chillerfg') {
                    $label          =   "ti_retur_fg";
                    $to             =   Gudang::gudang_netid($nama_gudang_fg);
                    Netsuite::transfer_inventory_doc($nama_tabel, $id_tabel, $label, $id_location, $location, $from, $to, $transfer, $rr->id, $retur->tanggal_retur ?? date('Y-m-d'), $code);
                }elseif($row->unit == 'chillerbb') {
                    $label          =   "ti_retur_chillerbb";
                    $to             =   Gudang::gudang_netid($nama_gudang_bb);
                    Netsuite::transfer_inventory_doc($nama_tabel, $id_tabel, $label, $id_location, $location, $from, $to, $transfer, $rr->id, $retur->tanggal_retur ?? date('Y-m-d'), $code);
                    if($item_retur->category_id==1){
                        Netsuite::wo_retur($nama_tabel, $id_tabel, $transfer, $retur->tanggal_retur ?? date('Y-m-d'), $code);
                    }
    
                }elseif($row->unit == 'musnahkan') {
    
                    $label          =   "ti_retur_storage_susut";
                    $to             =   Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Storage Susut");
                    Netsuite::transfer_inventory_doc($nama_tabel, $id_tabel, $label, $id_location, $location, $from, $to, $transfer, $rr->id, $retur->tanggal_retur ?? date('Y-m-d'), $code);
    
                } else {
                    $label          =   "ti_retur_abf";
                    $to             =   Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Storage ABF");
                    Netsuite::transfer_inventory_doc($nama_tabel, $id_tabel, $label, $id_location, $location, $from, $to, $transfer, $rr->id, $retur->tanggal_retur ?? date('Y-m-d'), $code);
                }
    
            endforeach;
        }

        DB::commit();

    }

    public static function return_authorization($tabel_retur, $id_retur, $label, $paket_id, $tanggal, $code){

        $nama_gudang_lb     = env('NET_SUBSIDIARY', 'CGL')." - Storage Live Bird";
        $nama_gudang_expedisi     = env('NET_SUBSIDIARY', 'CGL')." - Storage Expedisi";
        $nama_gudang_bb           = env('NET_SUBSIDIARY', 'CGL')." - Chiller Bahan Baku";
        $nama_gudang_fg           = env('NET_SUBSIDIARY', 'CGL')." - Chiller Finished Good";
        $nama_gudang_abf          = env('NET_SUBSIDIARY', 'CGL')." - Storage ABF";
        $nama_gudang_wip          = env('NET_SUBSIDIARY', 'CGL')." - Storage Produksi (WIP)";
        $nama_gudang_susut          = env('NET_SUBSIDIARY', 'CGL')." - Storage Susut";
        $nama_gudang_retur          = env('NET_SUBSIDIARY', 'CGL')." - Storage Retur";
        $nama_gudang_other          = env('NET_SUBSIDIARY', 'CGL')." - Storage Others";

        DB::beginTransaction();

        $location          =   $nama_gudang_retur;
        $id_location       =   Gudang::gudang_netid($location);

        $netsuite                   =   new Netsuite ;
        $netsuite->record_type      =   "return_authorization" ;
        $netsuite->label            =   "$label" ;
        $netsuite->document_code    =   "$code" ;
        $netsuite->paket_id         =   "$paket_id";
        $netsuite->trans_date       =   $tanggal ;
        $netsuite->user_id          =   Auth::user()->id ?? NULL ;
        $netsuite->tabel            =   $tabel_retur ;
        $netsuite->tabel_id         =   $id_retur ;
        $netsuite->subsidiary_id    =   env("NET_SUBSIDIARY_ID", "2") ;
        $netsuite->subsidiary       =   env("NET_SUBSIDIARY", "CGL") ;
        $netsuite->id_location      =   $id_location ;
        $netsuite->location         =   $location;

        if (!$netsuite->save()) {
            DB::rollBack() ;
            return back()->with('status', 2)->with('message', 'Proses gagal');
        }

        $net            = Netsuite::find($netsuite->id) ;
        $retur          = Retur::find($id_retur);
        $customer       = Customer::find($retur->customer_id);
        $data_items     = array();

        if ($retur->id_so != NULL || $retur->id_so != '') {
            foreach($retur->grup_ra_item_retur as $row):
    
                if ($row->unit == 'chiller') {
                    $internal_id_gudang             =   Gudang::gudang_netid($nama_gudang_bb) ;
                } else {
                    $internal_id_gudang             =   Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Storage ABF") ;
                }
    
                //Cek untuk tukar item, item aslinya
                $retur_item_baru = ReturItem::where('item_id', $row->item_id)->where('line_request', $row->line_request)->where('retur_id', $retur->id)->first();
                $item_retur      = Item::item_sku($row->sku);
    
                // if($retur_item_baru && ($retur_item_baru->catatan == 'Salah Item' || $retur_item_baru->catatan == 'Barang Tidak Sesuai Pesanan/Order')){
                //     $cekLog     = Adminedit::where('table_name', 'retur_item')->where('type','retur')
                //                                             ->where('table_id', $retur_item_baru->id)->where('activity', 'Retur Salah Item/Tidak Sesuai Pesanan')->first();
                //     if($cekLog){
                //         $item_sebelum_ditukar = Item::where('nama', $cekLog->data)->withTrashed()->first();
                //         if($item_sebelum_ditukar){
                //             $item_retur = Item::item_sku($item_sebelum_ditukar->sku);
                //         }
                //     }
                // }
    
                $data_items[] = [
                    "line"                      => $row->line_request ?? "",
                    "internal_id_item"          => (string)$item_retur->netsuite_internal_id,
                    "sku"                       => (string)$item_retur->sku,
                    "description"               => $row->description,
                    "part"                      => "0",
                    "qty"                       => round($row->abot, 2) ?? "1",
                    "unit"                      => "kg",
                    "rate"                      => $row->rate ?? "1",
                    "internal_id_gudang"        => $id_location,
                    "qty_in_ekr_pcs_pack"       => $row->total ?? "1",
                    "harga_in_ekr_pcs_pack"     => $row->harga
                ];
    
            endforeach;

        } else {
            foreach ($retur->grup_retur_nonso as $row) {
                if ($row->unit == 'chiller') {
                    $internal_id_gudang             =   Gudang::gudang_netid($nama_gudang_bb) ;
                } else {
                    $internal_id_gudang             =   Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Storage ABF") ;
                }
    
                $item_retur      = Item::item_sku($row->sku);
    
                $data_items[] = [
                    "line"                      => $row->line_request ?? "",
                    "internal_id_item"          => (string)$item_retur->netsuite_internal_id,
                    "sku"                       => (string)$item_retur->sku,
                    "description"               => $row->description,
                    "part"                      => "0",
                    "qty"                       => round($row->abot, 2) ?? "1",
                    "unit"                      => "kg",
                    "rate"                      => $row->rate ?? "1",
                    "internal_id_gudang"        => $id_location,
                    "qty_in_ekr_pcs_pack"       => $row->total ?? "1",
                    "harga_in_ekr_pcs_pack"     => $row->harga
                ];
            }
        }

        $result =   [
            "record_type"   =>  "return_authorization",
            "data"          =>  [
                [
                    "appsid"                       =>  env('NET_SUBSIDIARY', 'CGL')."-".(string)$netsuite->id,
                    "created_from_so"              =>  $retur->id_so,
                    "nomor_po"                     =>  "",
                    "internal_id_customer"         =>  $customer->netsuite_internal_id,
                    "tanggal_ra"                   =>  date("d-M-Y", strtotime($tanggal)),
                    "customer_partner"             =>  "" ,
                    "alamat_customer_partner"      =>  "" ,
                    "wilayah"                      =>  "" ,
                    "internal_id_sales_rep"        =>  "" ,
                    "memo"                         =>  "" ,
                    "sales_channel"                =>  1 ,
                    "alamat_ship_to"               =>  " - " ,
                    "items"                        =>  $data_items ,
                ]
            ]
        ];

        $net->script            =   '213' ;
        $net->deploy            =   '1' ;
        $net->data_content      =   json_encode($result) ;
        $net->status            =   2 ;

        if (!$net->save()) {
            DB::rollBack() ;
            return back()->with('status', 2)->with('message', 'Proses gagal');
        }
        DB::commit();

        return $net;

    }

    public static function receipt_return($tabel_retur, $id_retur, $label, $paket_id, $tanggal, $code){

        $nama_gudang_lb     = env('NET_SUBSIDIARY', 'CGL')." - Storage Live Bird";
        $nama_gudang_expedisi     = env('NET_SUBSIDIARY', 'CGL')." - Storage Expedisi";
        $nama_gudang_bb           = env('NET_SUBSIDIARY', 'CGL')." - Chiller Bahan Baku";
        $nama_gudang_fg           = env('NET_SUBSIDIARY', 'CGL')." - Chiller Finished Good";
        $nama_gudang_abf          = env('NET_SUBSIDIARY', 'CGL')." - Storage ABF";
        $nama_gudang_wip          = env('NET_SUBSIDIARY', 'CGL')." - Storage Produksi (WIP)";
        $nama_gudang_susut          = env('NET_SUBSIDIARY', 'CGL')." - Storage Susut";
        $nama_gudang_retur          = env('NET_SUBSIDIARY', 'CGL')." - Storage Retur";
        $nama_gudang_other          = env('NET_SUBSIDIARY', 'CGL')." - Storage Others";

        DB::beginTransaction();

        $location          =   $nama_gudang_retur;
        $id_location       =   Gudang::gudang_netid($location) ;

        $netsuite                   =   new Netsuite ;
        $netsuite->record_type      =   "receipt_return" ;
        $netsuite->label            =   "$label" ;
        $netsuite->document_code    =   "$code" ;
        $netsuite->paket_id         =   "$paket_id";
        $netsuite->trans_date       =   $tanggal ;
        $netsuite->user_id          =   Auth::user()->id ?? NULL ;
        $netsuite->tabel            =   $tabel_retur ;
        $netsuite->tabel_id         =   $id_retur ;
        $netsuite->subsidiary_id    =   env("NET_SUBSIDIARY_ID", "2") ;
        $netsuite->subsidiary       =   env("NET_SUBSIDIARY", "CGL") ;
        $netsuite->id_location      =   $id_location ;
        $netsuite->location         =   $location;

        if (!$netsuite->save()) {
            DB::rollBack() ;
            return back()->with('status', 2)->with('message', 'Proses gagal');
        }

        $net            = Netsuite::find($netsuite->id) ;
        $retur          = Retur::find($id_retur);
        $customer       = Customer::find($retur->customer_id);
        $data_items     = array();
        if ($retur->id_so != NULL || $retur->id_so != '') {
            foreach($retur->grup_ra_item_retur as $row):
    
                if ($row->unit == 'chiller') {
                    $gudang                         =   $nama_gudang_retur;
                    $internal_id_gudang             =   Gudang::gudang_netid($gudang);
                } else {
                    $gudang                         =   $nama_gudang_retur;
                    $internal_id_gudang             =   Gudang::gudang_netid($gudang);
                }
    
                  //Cek untuk tukar item, item aslinya
                $retur_item_baru = ReturItem::where('item_id', $row->item_id)->where('line_request', $row->line_request)->where('retur_id', $retur->id)->first();
                $item_retur      = Item::item_sku($row->sku);
    
                // if($retur_item_baru && ($retur_item_baru->catatan == 'Salah Item' || $retur_item_baru->catatan == 'Barang Tidak Sesuai Pesanan/Order')){
                //     $cekLog     = Adminedit::where('table_name', 'retur_item')->where('type','retur')
                //                                             ->where('table_id', $retur_item_baru->id)->where('activity', 'Retur Salah Item/Tidak Sesuai Pesanan')->first();
                //     if($cekLog){
                //         $item_sebelum_ditukar = Item::where('nama', $cekLog->data)->withTrashed()->first();
                //         if($item_sebelum_ditukar){
                //             $item_retur = Item::item_sku($item_sebelum_ditukar->sku);
                //         }
                //     }
                // }
    
                $data_items[] = [
                    "line"                        => "",
                    "internal_id_item"            => (string)$item_retur->netsuite_internal_id,
                    "item_code"                   => (string)$item_retur->sku,
                    "qty"                         => round($row->abot, 2),
                    "qty_in_ekor"                 => $row->total,
                    "internal_id_location"        => $internal_id_gudang,
                    "gudang"                      => $gudang
                ];
    
            endforeach;

        } else {
            foreach ($retur->grup_retur_nonso as $row) {
                if ($row->unit == 'chiller') {
                    $gudang                         =   $nama_gudang_retur;
                    $internal_id_gudang             =   Gudang::gudang_netid($gudang);
                } else {
                    $gudang                         =   $nama_gudang_retur;
                    $internal_id_gudang             =   Gudang::gudang_netid($gudang);
                }
                $item_retur      = Item::item_sku($row->sku);
                $data_items[] = [
                    "line"                        => "",
                    "internal_id_item"            => (string)$item_retur->netsuite_internal_id,
                    "item_code"                   => (string)$item_retur->sku,
                    "qty"                         => round($row->abot, 2),
                    "qty_in_ekor"                 => $row->total,
                    "internal_id_location"        => $internal_id_gudang,
                    "gudang"                      => $gudang
                ];
            }
        }

        $result =   [
            "record_type"   =>  "receipt_return",
            "data"          =>  [
                [
                    "appsid"                      =>  env('NET_SUBSIDIARY', 'CGL')."-".(string)$netsuite->id,
                    "internal_id_ra"              =>  "",
                    "ra_number"                   =>  "",
                    "date"                        =>  date("d-M-Y", strtotime($tanggal)),
                    "memo"                        =>  "" ,
                    "no_nota"                     =>  "",
                    "tanggal_nota"                =>  date("d-M-Y", strtotime($tanggal)),
                    "line"                        =>  $data_items
                ]
            ]
        ];

        $net->script            =   '212' ;
        $net->deploy            =   '1' ;
        $net->data_content      =   json_encode($result) ;
        $net->status            =   2 ;

        if (!$net->save()) {
            DB::rollBack() ;
            return back()->with('status', 2)->with('message', 'Proses gagal');
        }
        DB::commit();

        return $net;

    }

    public static function item_fulfill($tabel_so, $id_so, $label, $id_ekspedisi, $paket_id){

        $nama_gudang_lb     = env('NET_SUBSIDIARY', 'CGL')." - Storage Live Bird";
        $nama_gudang_expedisi     = env('NET_SUBSIDIARY', 'CGL')." - Storage Expedisi";
        $nama_gudang_bb           = env('NET_SUBSIDIARY', 'CGL')." - Chiller Bahan Baku";
        $nama_gudang_fg           = env('NET_SUBSIDIARY', 'CGL')." - Chiller Finished Good";
        $nama_gudang_abf          = env('NET_SUBSIDIARY', 'CGL')." - Storage ABF";
        $nama_gudang_wip          = env('NET_SUBSIDIARY', 'CGL')." - Storage Produksi (WIP)";
        $nama_gudang_susut          = env('NET_SUBSIDIARY', 'CGL')." - Storage Susut";
        $nama_gudang_retur          = env('NET_SUBSIDIARY', 'CGL')." - Storage Retur";
        $nama_gudang_other          = env('NET_SUBSIDIARY', 'CGL')." - Storage Others";

        DB::beginTransaction();

        $ekspedisi      = Ekspedisi::find($id_ekspedisi);
        $sales_order    = Order::find($id_so);

        $netsuite                   =   new Netsuite ;
        $netsuite->record_type      =   "item_fulfill" ;
        $netsuite->label            =   "$label" ;
        $netsuite->document_code    =   $sales_order->no_so ;
        $netsuite->paket_id         =   "$paket_id";
        // $netsuite->trans_date       =   Carbon::now() ;
        $netsuite->trans_date       =   $sales_order->tanggal_kirim ;
        $netsuite->user_id          =   Auth::user()->id ?? NULL ;
        $netsuite->tabel            =   $tabel_so ;
        $netsuite->tabel_id         =   $id_so ;
        $netsuite->subsidiary_id    =   env("NET_SUBSIDIARY_ID", "2") ;
        $netsuite->subsidiary       =   env("NET_SUBSIDIARY", "CGL") ;
        $netsuite->id_location      =   Gudang::gudang_netid($nama_gudang_expedisi) ;
        $netsuite->location         =   $nama_gudang_expedisi ;

        if (!$netsuite->save()) {
            DB::rollBack() ;
            return back()->with('status', 2)->with('message', 'Proses gagal');
        }

        $net    =   Netsuite::find($netsuite->id) ;


        $data_items = array();

        foreach($sales_order->daftar_order as $row):

            if($row->fulfillment_berat>0){
                // fulfillment total gabungan dari order_bahan_baku
                $order_bahan_baku = Bahanbaku::select(DB::raw('SUM(bb_item) as qty, SUM(bb_berat) as berat, SUM(keranjang) as krj'))->where('order_item_id', $row->id)->whereNull('type')->groupBy(['order_item_id'])->first();

                if($order_bahan_baku){

                    // $harga      = NULL;
                    // if($row->unit=="Kilogram"){
                    //     $harga  =   $row->rate ?? NULL;
                    // }else{
                    //     $harga  =   $row->harga ?? NULL;
                    // }

                    $krj = "";

                    if($order_bahan_baku->krj>0){
                        $krj = " (Krj\Krg\Krtn ".$order_bahan_baku->krj.")";
                    }
                    
                    $data_items[] = [
                        "line"                   => $row->line_id,
                        "internal_id_item"          => Item::get_item_internal_id_by_id($row->item_id),
                        "item"                      => $row->nama_detail,
                        // "description"               => $row->description.$krj,
                        "description"               => $row->description_item ?? $krj,
                        "part"                      => $row->part,
                        "keterangan"                => $row->bumbu == NULL ? $row->memo.$krj : 'Bumbu ' . $row->bumbu . ' - ' . $row->memo.$krj,
                        "qty"                       => $order_bahan_baku->berat ?? 0,
                        "internal_id_gudang"        => Gudang::gudang_netid($nama_gudang_expedisi),
                        "gudang"                    => $nama_gudang_expedisi,
                        "qty_in_ekr_pcs_pack"       => $order_bahan_baku->qty ?? 0,
                        "harga_in_ekr_pcs_pack"     => $row->rate ?? "1",
                    ];

                }

            }

            // update status bahan baku telah terfulfill
            $order_bahan_baku_satuan = Bahanbaku::where('order_item_id', $row->id)->whereNull('type')->update(array('type' => 'order-fulfillment'));

            if(env('NET_SUBSIDIARY', 'EBA')=='EBA'){
                if ($row->sku == '1310000002' || $row->sku == '300800A002') {
                    $data_items[] = [
                        "line"                   => $row->line_id,
                        "internal_id_item"          => Item::where('nama', 'AY - S')->first()->netsuite_internal_id,
                        "item"                      => "AY - S",
                        "description"               => "",
                        "part"                      => "",
                        "keterangan"                => "",
                        "qty"                       => 1,
                        "internal_id_gudang"        => Gudang::gudang_netid($nama_gudang_expedisi),
                        "gudang"                    => $nama_gudang_expedisi,
                        "qty_in_ekr_pcs_pack"       => 1,
                        "harga_in_ekr_pcs_pack"     => 1,
                    ];

                }
            }

        endforeach;


        if(count($data_items)>0){

            $date_so = date("d-M-Y", strtotime($sales_order->tanggal_kirim ));

             // DIMATIIN KARENA REQUEST PAK HERI
            // if(strtotime($sales_order->tanggal_kirim) < strtotime('today')){
            //     $date_so = date('d-M-Y');
            // }

            $result =   [
                "record_type"   =>  "item_fulfill",
                "data"          =>  [
                    [
                        "appsid"                        =>  env('NET_SUBSIDIARY', 'CGL')."-".(string)$netsuite->id,
                        "internal_id_so"                =>  $sales_order->id_so,
                        "so_number"                     =>  $sales_order->no_so,
                        "date_so"                       =>  $date_so,
                        "memo"                          =>  $sales_order->keterangan ,
                        "nama_supir"                    =>  $ekspedisi->nama ?? "",
                        "no_plat_kendaraan"             =>  $ekspedisi->no_polisi ?? "",
                        "items"                         =>  $data_items ,
                        ]
                        ]
                    ];

                    $net->script            =   '210' ;
                    $net->deploy            =   '1' ;
                    $net->data_content      =   json_encode($result) ;
                    $net->status            =   2 ;

                    if (!$net->save()) {
                        DB::rollBack() ;
                        return back()->with('status', 2)->with('message', 'Proses gagal');
                    }
        }

        DB::commit();

        return $net;

    }

    public static function item_fulfill_sampingan($tabel_so, $id_so, $label, $id_ekspedisi, $paket_id){

        $nama_gudang_lb     = env('NET_SUBSIDIARY', 'CGL')." - Storage Live Bird";
        $nama_gudang_expedisi     = env('NET_SUBSIDIARY', 'CGL')." - Storage Expedisi";
        $nama_gudang_bb           = env('NET_SUBSIDIARY', 'CGL')." - Chiller Bahan Baku";
        $nama_gudang_fg           = env('NET_SUBSIDIARY', 'CGL')." - Chiller Finished Good";
        $nama_gudang_abf          = env('NET_SUBSIDIARY', 'CGL')." - Storage ABF";
        $nama_gudang_wip          = env('NET_SUBSIDIARY', 'CGL')." - Storage Produksi (WIP)";
        $nama_gudang_susut          = env('NET_SUBSIDIARY', 'CGL')." - Storage Susut";
        $nama_gudang_retur          = env('NET_SUBSIDIARY', 'CGL')." - Storage Retur";
        $nama_gudang_other          = env('NET_SUBSIDIARY', 'CGL')." - Storage Others";

        DB::beginTransaction();

        $ekspedisi      = Ekspedisi::find($id_ekspedisi);
        $sales_order    = Order::find($id_so);

        $netsuite                   =   new Netsuite ;
        $netsuite->record_type      =   "item_fulfill" ;
        $netsuite->label            =   "$label" ;
        $netsuite->paket_id         =   "$paket_id";
        $netsuite->document_code    =   $sales_order->no_so ;
        // $netsuite->trans_date       =   Carbon::now() ;
        $netsuite->trans_date       =   $sales_order->tanggal_kirim ;
        $netsuite->user_id          =   Auth::user()->id ?? NULL ;
        $netsuite->tabel            =   $tabel_so ;
        $netsuite->tabel_id         =   $id_so ;
        $netsuite->subsidiary_id    =   env("NET_SUBSIDIARY_ID", "2") ;
        $netsuite->subsidiary       =   env("NET_SUBSIDIARY", "CGL") ;
        $netsuite->id_location      =   Gudang::gudang_netid($nama_gudang_expedisi) ;
        $netsuite->location         =   $nama_gudang_expedisi ;

        if (!$netsuite->save()) {
            DB::rollBack() ;
            return back()->with('status', 2)->with('message', 'Proses gagal');
        }

        $net    =   Netsuite::find($netsuite->id) ;

        $data_items = array();

        foreach($sales_order->daftar_order as $row):

            if($row->fulfillment_berat>0){
                // fulfillment total gabungan dari order_bahan_baku
                $order_bahan_baku = Bahanbaku::select(DB::raw('SUM(bb_item) as qty, SUM(bb_berat) as berat, SUM(keranjang) as krj'))->where('order_item_id', $row->id)->whereNull('type')->groupBy(['order_item_id'])->first();

                if($order_bahan_baku){

                    // $harga      = NULL;
                    // if($row->unit=="Kilogram"){
                    //     $harga  =   $row->rate ?? NULL;
                    // }else{
                    //     $harga  =   $row->harga ?? NULL;
                    // }

                    $krj = "";

                    if($order_bahan_baku->krj>0){
                        $krj = " (Krj\Krg\Krtn ".$order_bahan_baku->krj.")";
                    }
                    
                    $data_items[] = [
                        "line"                   => $row->line_id,
                        "internal_id_item"          => Item::get_item_internal_id_by_id($row->item_id),
                        "item"                      => $row->nama_detail,
                        "description"               => $row->description_item ?? $krj,
                        "part"                      => $row->part,
                        "keterangan"                => $row->bumbu == NULL ? $row->memo.$krj : 'Bumbu ' . $row->bumbu . ' - ' . $row->memo.$krj,
                        "qty"                       => $order_bahan_baku->berat ?? 0,
                        "internal_id_gudang"        => Gudang::gudang_netid($nama_gudang_expedisi),
                        "gudang"                    => $nama_gudang_expedisi,
                        "qty_in_ekr_pcs_pack"       => $order_bahan_baku->qty ?? 0,
                        "harga_in_ekr_pcs_pack"     => $row->rate ?? "1",
                    ];

                }

            }

            // update status bahan baku telah terfulfill
            $order_bahan_baku_satuan = Bahanbaku::where('order_item_id', $row->id)->whereNull('type')->update(array('type' => 'order-fulfillment'));

            if(env('NET_SUBSIDIARY', 'EBA')=='EBA'){

                if ($row->sku == '1310000002' || $row->sku == '300800A002') {
                    $data_items[] = [
                        "line"                   => $row->line_id,
                        "internal_id_item"          => Item::where('nama', 'AY - S')->first()->netsuite_internal_id,
                        "item"                      => "AY - S",
                        "description"               => "",
                        "part"                      => "",
                        "keterangan"                => "",
                        "qty"                       => 1,
                        "internal_id_gudang"        => Gudang::gudang_netid($nama_gudang_expedisi),
                        "gudang"                    => $nama_gudang_expedisi,
                        "qty_in_ekr_pcs_pack"       => 1,
                        "harga_in_ekr_pcs_pack"     => 1,
                    ];
                }

            }

        endforeach;

        if(count($data_items)>0){

            $date_so = date("d-M-Y", strtotime($sales_order->tanggal_kirim ));

            // DIMATIIN KARENA REQUEST PAK HERI
            // if(strtotime($sales_order->tanggal_kirim) < strtotime('today')){
            //     $date_so = date('d-M-Y');
            // }

            $result =   [
                "record_type"   =>  "item_fulfill",
                "data"          =>  [
                    [
                        "appsid"                        =>  env('NET_SUBSIDIARY', 'CGL')."-".(string)$netsuite->id,
                        "internal_id_so"                =>  $sales_order->id_so,
                        "so_number"                     =>  $sales_order->no_so,
                        "date_so"                       =>  $date_so,
                        "memo"                          =>  $sales_order->keterangan ,
                        "nama_supir"                    =>  $ekspedisi->nama ?? "",
                        "no_plat_kendaraan"             =>  $ekspedisi->no_polisi ?? "",
                        "items"                         =>  $data_items ,
                        ]
                        ]
                    ];

                    $net->script            =   '210' ;
                    $net->deploy            =   '1' ;
                    $net->data_content      =   json_encode($result) ;
                    $net->status            =   2 ;

                    if (!$net->save()) {
                        DB::rollBack() ;
                        return back()->with('status', 2)->with('message', 'Proses gagal');
                    }
        }

        DB::commit();

        return $net;

    }

    public static function item_fulfill_creditlimit($tabel_so, $id_so, $label, $id_ekspedisi, $paket_id){

        $nama_gudang_lb     = env('NET_SUBSIDIARY', 'CGL')." - Storage Live Bird";
        $nama_gudang_expedisi     = env('NET_SUBSIDIARY', 'CGL')." - Storage Expedisi";
        $nama_gudang_bb           = env('NET_SUBSIDIARY', 'CGL')." - Chiller Bahan Baku";
        $nama_gudang_fg           = env('NET_SUBSIDIARY', 'CGL')." - Chiller Finished Good";
        $nama_gudang_abf          = env('NET_SUBSIDIARY', 'CGL')." - Storage ABF";
        $nama_gudang_wip          = env('NET_SUBSIDIARY', 'CGL')." - Storage Produksi (WIP)";
        $nama_gudang_susut          = env('NET_SUBSIDIARY', 'CGL')." - Storage Susut";
        $nama_gudang_retur          = env('NET_SUBSIDIARY', 'CGL')." - Storage Retur";
        $nama_gudang_other          = env('NET_SUBSIDIARY', 'CGL')." - Storage Others";

        DB::beginTransaction();


        $ekspedisi      = Ekspedisi::find($id_ekspedisi);
        $sales_order    = Order::find($id_so);

        $netsuite                   =   new Netsuite ;
        $netsuite->record_type      =   "item_fulfill" ;
        $netsuite->label            =   "$label" ;
        $netsuite->paket_id         =   "$paket_id";
        $netsuite->document_code    =   $sales_order->no_so ;
        // $netsuite->trans_date       =   Carbon::now() ;
        $netsuite->trans_date       =   $sales_order->tanggal_kirim ;
        $netsuite->user_id          =   Auth::user()->id ?? NULL ;
        $netsuite->tabel            =   $tabel_so ;
        $netsuite->tabel_id         =   $id_so ;
        $netsuite->subsidiary_id    =   env("NET_SUBSIDIARY_ID", "2") ;
        $netsuite->subsidiary       =   env("NET_SUBSIDIARY", "CGL") ;
        $netsuite->id_location      =   Gudang::gudang_netid($nama_gudang_expedisi) ;
        $netsuite->location         =   $nama_gudang_expedisi ;

        if (!$netsuite->save()) {
            DB::rollBack() ;
            return back()->with('status', 2)->with('message', 'Proses gagal');
        }

        $net    =   Netsuite::find($netsuite->id) ;

        $data_items = array();

        foreach($sales_order->list_daftar_order as $row):

            if($row->fulfillment_berat>0){
                // fulfillment total gabungan dari order_bahan_baku
                $order_bahan_baku = Bahanbaku::select(DB::raw('SUM(bb_item) as qty, SUM(bb_berat) as berat, SUM(keranjang) as krj'))
                                    ->where('order_item_id', $row->id)
                                    // ->whereNull('type')
                                    ->where('deleted_at', NULL)
                                    ->groupBy(['order_item_id'])->first();

                if($order_bahan_baku){

                    $krj = "";

                    if($order_bahan_baku->krj>0){
                        $krj = " (Krj\Krg\Krtn ".$order_bahan_baku->krj.")";
                    }

                    $data_items[] = [
                        "line"                   => $row->line_id,
                        "internal_id_item"          => Item::get_item_internal_id_by_id($row->item_id),
                        "item"                      => $row->nama_detail,
                        "description"               => $row->description_item ?? $krj,
                        "part"                      => $row->part,
                        "keterangan"                => $row->bumbu == NULL ? $row->memo.$krj : 'Bumbu ' . $row->bumbu . ' - ' . $row->memo.$krj,
                        "qty"                       => $order_bahan_baku->berat ?? 0,
                        "internal_id_gudang"        => Gudang::gudang_netid($nama_gudang_expedisi),
                        "gudang"                    => $nama_gudang_expedisi,
                        "qty_in_ekr_pcs_pack"       => $order_bahan_baku->qty ?? 0,
                        "harga_in_ekr_pcs_pack"     => $row->rate ?? "1",
                    ];

                }

            }

            // update status bahan baku telah terfulfill
            $order_bahan_baku_satuan = Bahanbaku::where('order_item_id', $row->id)->whereNull('type')->update(array('type' => 'order-fulfillment'));

            if(env('NET_SUBSIDIARY', 'EBA')=='EBA'){
                if ($row->sku == '1310000002' || $row->sku == '300800A002') {
                    $data_items[] = [
                        "line"                   => $row->line_id,
                        "internal_id_item"          => Item::where('nama', 'AY - S')->first()->netsuite_internal_id,
                        "item"                      => "AY - S",
                        "description"               => "",
                        "part"                      => "",
                        "keterangan"                => "",
                        "qty"                       => 1,
                        "internal_id_gudang"        => Gudang::gudang_netid($nama_gudang_expedisi),
                        "gudang"                    => $nama_gudang_expedisi,
                        "qty_in_ekr_pcs_pack"       => 1,
                        "harga_in_ekr_pcs_pack"     => 1,
                    ];
                }
            }

        endforeach;

        if(count($data_items)>0){

            $date_so = date("d-M-Y", strtotime($sales_order->tanggal_kirim ));
            
            // DIMATIIN KARENA REQUEST PAK HERI
            // if(strtotime($sales_order->tanggal_kirim) < strtotime('today')){
            //     $date_so = date('d-M-Y');
            // }

            $result =   [
                "record_type"   =>  "item_fulfill",
                "data"          =>  [
                    [
                        "appsid"                        =>  env('NET_SUBSIDIARY', 'CGL')."-".(string)$netsuite->id,
                        "internal_id_so"                =>  $sales_order->id_so,
                        "so_number"                     =>  $sales_order->no_so,
                        "date_so"                       =>  $date_so,
                        "memo"                          =>  $sales_order->keterangan ,
                        "nama_supir"                    =>  $ekspedisi->nama ?? "",
                        "no_plat_kendaraan"             =>  $ekspedisi->no_polisi ?? "",
                        "items"                         =>  $data_items ,
                        ]
                        ]
                    ];

                    $net->script            =   '210' ;
                    $net->deploy            =   '1' ;
                    $net->data_content      =   json_encode($result) ;
                    $net->status            =   2 ;

                    if (!$net->save()) {
                        DB::rollBack() ;
                        return back()->with('status', 2)->with('message', 'Proses gagal');
                    }
        }

        DB::commit();

        return $net;

    }

    public static function item_fulfill_tambahan($tabel_so, $id_so, $label, $id_ekspedisi, $paket_id){

        $nama_gudang_lb     = env('NET_SUBSIDIARY', 'CGL')." - Storage Live Bird";
        $nama_gudang_expedisi     = env('NET_SUBSIDIARY', 'CGL')." - Storage Expedisi";
        $nama_gudang_bb           = env('NET_SUBSIDIARY', 'CGL')." - Chiller Bahan Baku";
        $nama_gudang_fg           = env('NET_SUBSIDIARY', 'CGL')." - Chiller Finished Good";
        $nama_gudang_abf          = env('NET_SUBSIDIARY', 'CGL')." - Storage ABF";
        $nama_gudang_wip          = env('NET_SUBSIDIARY', 'CGL')." - Storage Produksi (WIP)";
        $nama_gudang_susut          = env('NET_SUBSIDIARY', 'CGL')." - Storage Susut";
        $nama_gudang_retur          = env('NET_SUBSIDIARY', 'CGL')." - Storage Retur";
        $nama_gudang_other          = env('NET_SUBSIDIARY', 'CGL')." - Storage Others";

        DB::beginTransaction();


        $ekspedisi      = Ekspedisi::find($id_ekspedisi);
        $sales_order    = Order::find($id_so);

        $netsuite                   =   new Netsuite ;
        $netsuite->record_type      =   "item_fulfill" ;
        $netsuite->label            =   "$label" ;
        $netsuite->paket_id         =   "$paket_id";
        $netsuite->document_code    =   $sales_order->no_so ;
        // $netsuite->trans_date       =   Carbon::now() ;
        $netsuite->trans_date       =   $sales_order->tanggal_kirim ;
        $netsuite->user_id          =   Auth::user()->id ?? NULL ;
        $netsuite->tabel            =   $tabel_so ;
        $netsuite->tabel_id         =   $id_so ;
        $netsuite->subsidiary_id    =   env("NET_SUBSIDIARY_ID", "2") ;
        $netsuite->subsidiary       =   env("NET_SUBSIDIARY", "CGL") ;
        $netsuite->id_location      =   Gudang::gudang_netid($nama_gudang_expedisi) ;
        $netsuite->location         =   $nama_gudang_expedisi ;

        if (!$netsuite->save()) {
            DB::rollBack() ;
            return back()->with('status', 2)->with('message', 'Proses gagal');
        }

        $net    =   Netsuite::find($netsuite->id) ;

        $data_items = array();

        foreach($sales_order->list_daftar_order as $row):

            if($row->fulfillment_berat>0){
                // fulfillment total gabungan dari order_bahan_baku
                $order_bahan_baku = Bahanbaku::select(DB::raw('SUM(bb_item) as qty, SUM(bb_berat) as berat, SUM(keranjang) as krj'))->where('order_item_id', $row->id)->whereNull('type')->where('deleted_at', NULL)->groupBy(['order_item_id'])->first();

                if($order_bahan_baku){

                    // $harga      = NULL;
                    // if($row->unit=="Kilogram"){
                    //     $harga  =   $row->rate ?? NULL;
                    // }else{
                    //     $harga  =   $row->harga ?? NULL;
                    // }

                    $krj = "";

                    if($order_bahan_baku->krj>0){
                        $krj = " (Krj\Krg\Krtn ".$order_bahan_baku->krj.")";
                    }

                    $data_items[] = [
                        "line"                   => $row->line_id,
                        "internal_id_item"          => Item::get_item_internal_id_by_id($row->item_id),
                        "item"                      => $row->nama_detail,
                        "description"               => $row->description_item ?? $krj,
                        "part"                      => $row->part,
                        "keterangan"                => $row->bumbu == NULL ? $row->memo.$krj : 'Bumbu ' . $row->bumbu . ' - ' . $row->memo.$krj,
                        "qty"                       => $order_bahan_baku->berat ?? 0,
                        "internal_id_gudang"        => Gudang::gudang_netid($nama_gudang_expedisi),
                        "gudang"                    => $nama_gudang_expedisi,
                        "qty_in_ekr_pcs_pack"       => $order_bahan_baku->qty ?? 0,
                        "harga_in_ekr_pcs_pack"     => $row->rate ?? "1",
                    ];

                }

            }

            // update status bahan baku telah terfulfill
            $order_bahan_baku_satuan = Bahanbaku::where('order_item_id', $row->id)->whereNull('type')->update(array('type' => 'order-fulfillment'));

            if(env('NET_SUBSIDIARY', 'EBA')=='EBA'){
                if ($row->sku == '1310000002' || $row->sku == '300800A002') {
                    $data_items[] = [
                        "line"                   => $row->line_id,
                        "internal_id_item"          => Item::where('nama', 'AY - S')->first()->netsuite_internal_id,
                        "item"                      => "AY - S",
                        "description"               => "",
                        "part"                      => "",
                        "keterangan"                => "",
                        "qty"                       => 1,
                        "internal_id_gudang"        => Gudang::gudang_netid($nama_gudang_expedisi),
                        "gudang"                    => $nama_gudang_expedisi,
                        "qty_in_ekr_pcs_pack"       => 1,
                        "harga_in_ekr_pcs_pack"     => 1,
                    ];
                }
            }

        endforeach;

        if(count($data_items)>0){

            $date_so = date("d-M-Y", strtotime($sales_order->tanggal_kirim ));
            
            // DIMATIIN KARENA REQUEST PAK HERI
            // if(strtotime($sales_order->tanggal_kirim) < strtotime('today')){
            //     $date_so = date('d-M-Y');
            // }

            $result =   [
                "record_type"   =>  "item_fulfill",
                "data"          =>  [
                    [
                        "appsid"                        =>  env('NET_SUBSIDIARY', 'CGL')."-".(string)$netsuite->id,
                        "internal_id_so"                =>  $sales_order->id_so,
                        "so_number"                     =>  $sales_order->no_so,
                        "date_so"                       =>  $date_so,
                        "memo"                          =>  $sales_order->keterangan ,
                        "nama_supir"                    =>  $ekspedisi->nama ?? "",
                        "no_plat_kendaraan"             =>  $ekspedisi->no_polisi ?? "",
                        "items"                         =>  $data_items ,
                        ]
                        ]
                    ];

                    $net->script            =   '210' ;
                    $net->deploy            =   '1' ;
                    $net->data_content      =   json_encode($result) ;
                    $net->status            =   2 ;

                    if (!$net->save()) {
                        DB::rollBack() ;
                        return back()->with('status', 2)->with('message', 'Proses gagal');
                    }
        }

        DB::commit();

        return $net;

    }

    public static function item_receipt_lpah($produksi_id)
    {
        $lpah   =   Production::find($produksi_id);

        DB::beginTransaction() ;

        if ($lpah->lpah_netsuite_status == null) {

            if ($lpah->prodpur->type_po != "PO Maklon") {
                $netsuite                   =   new Netsuite;
                $netsuite->record_type      =   "itemreceipt";
                $netsuite->label            =   "item_receipt_lpah";
                $netsuite->document_code    =   $lpah->no_po;
                $netsuite->trans_date       =   $lpah->prod_tanggal_potong;
                $netsuite->user_id          =   Auth::user()->id ?? NULL;
                $netsuite->tabel            =   "productions";
                $netsuite->paket_id         =   "0";
                $netsuite->tabel_id         =   $lpah->id;
                $netsuite->subsidiary_id    =   env("NET_SUBSIDIARY_ID", "6");
                $netsuite->subsidiary       =   env("NET_SUBSIDIARY", "CGL");
                $netsuite->id_location      =   Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Storage Live Bird");
                $netsuite->location         =   env('NET_SUBSIDIARY', 'CGL')." - Storage Live Bird";

                if (!$netsuite->save()) {
                    DB::rollBack();
                    return back()->with('status', 2)->with('message', 'Proses gagal');
                }

                $purchase_item = PurchaseItem::where('purchasing_id', $lpah->prodpur->id)->first();

                $net    =   Netsuite::find($netsuite->id);
                $data   =   [
                    "record_type"   =>  "itemreceipt",
                    "data"          =>  [
                        [
                            "appsid"            =>  env('NET_SUBSIDIARY', 'CGL')."-".$net->id,
                            "internal_id_po"    =>  $lpah->prodpur->internal_id_po ?? "",
                            "po_number"         =>  $lpah->no_po,
                            "date"              =>  date("d-M-Y", strtotime($lpah->prod_tanggal_potong)),
                            "memo"              =>  $lpah->id."-".$lpah->no_lpah,
                            "no_nota"           =>  $lpah->no_do,
                            "tanggal_nota"      =>  date("d-M-Y", strtotime($lpah->prod_tanggal_potong)),
                            "line"              =>  [
                                [
                                    "line"                  =>  $purchase_item->internal_id_po ?? '1',
                                    "internal_id_item"      =>  (string)Item::item_sku(($purchase_item->item_po ?? "1100000011"))->netsuite_internal_id,
                                    "item_code"             =>  ($purchase_item->item_po ?? "1100000011"),
                                    "qty"                   =>  $lpah->po_jenis_ekspedisi == 'tangkap' ? $lpah->sc_berat_do : "$lpah->lpah_berat_terima",
                                    "qty_in_ekor"           =>  $lpah->po_jenis_ekspedisi == 'tangkap' ? $lpah->sc_ekor_do : $lpah->ekoran_seckle,
                                    "internal_id_location"  =>  (string)Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Storage Live Bird"),
                                    "gudang"                =>  env('NET_SUBSIDIARY', 'CGL')." - Storage Live Bird",
                                ]
                            ]
                        ]
                    ]
                ];

                $net->script            =   '211';
                $net->deploy            =   '1';
                $net->data_content      =   json_encode($data);
                $net->status            =   2;
                if (!$net->save()) {
                    DB::rollBack();
                    return back()->with('status', 2)->with('message', 'Proses gagal');
                }
            }
        }

        $lpah->lpah_netsuite_status         =   1;
        if (!$lpah->save()) {
            DB::rollBack();
            return back()->with('status', 2)->with('message', 'Proses gagal');
        }

        DB::commit() ;
    }

    public static function item_receipt_grading($produksi_id){

        $nama_gudang_lb     = env('NET_SUBSIDIARY', 'CGL')." - Storage Live Bird";
        $nama_gudang_expedisi     = env('NET_SUBSIDIARY', 'CGL')." - Storage Expedisi";
        $nama_gudang_bb           = env('NET_SUBSIDIARY', 'CGL')." - Chiller Bahan Baku";
        $nama_gudang_fg           = env('NET_SUBSIDIARY', 'CGL')." - Chiller Finished Good";
        $nama_gudang_abf          = env('NET_SUBSIDIARY', 'CGL')." - Storage ABF";
        $nama_gudang_wip          = env('NET_SUBSIDIARY', 'CGL')." - Storage Produksi (WIP)";
        $nama_gudang_susut          = env('NET_SUBSIDIARY', 'CGL')." - Storage Susut";
        $nama_gudang_retur          = env('NET_SUBSIDIARY', 'CGL')." - Storage Retur";
        $nama_gudang_other          = env('NET_SUBSIDIARY', 'CGL')." - Storage Others";

        $produksi = Production::find($produksi_id);

        if($produksi){

            $is_frozen  =   Item::where('sku', $produksi->prodpur->item_po)
                            ->where('nama','like', '%FROZEN%')
                            ->withTrashed()
                            ->first();

            if(!$is_frozen){

                $grading    =   Grading::where('trans_id', $produksi->id)->where('item_id', '!=', "")->whereNull('status')->get();
                // dd($grading);

                DB::beginTransaction();
                foreach($grading as $gd):
        



                    if ($produksi->prodpur->jenis_po == 'PO Karkas') {
                        
                        $abjadFrozen = 6; // Number of characters to extract from the end
                        $checkIsFrozen = substr($gd->keterangan, -$abjadFrozen);

                        if ($checkIsFrozen == 'FROZEN') {
                            $purchase_item = PurchaseItem::where('purchasing_id', $produksi->prodpur->id)
                                                ->where(function($query) use ($checkIsFrozen) {
                                                    $query->where('description', 'like', 'AYAM KARKAS BROILER '. $checkIsFrozen. '%');
                                                    $query->orWhere('keterangan', 'like', 'AYAM KARKAS BROILER '. $checkIsFrozen. '%');
                                                })
                                                ->first();
                            
                        } else {
                            $abjadFresh = 5; // Number of characters to extract from the end
                            $checkUkuranKarkas = substr($gd->keterangan, -$abjadFresh);
                            $purchase_item = PurchaseItem::where('purchasing_id', $produksi->prodpur->id)
                                                ->where(function($query) use ($checkUkuranKarkas) {
                                                    $query->where('description', 'AYAM KARKAS BROILER (RM) '. $checkUkuranKarkas);
                                                    $query->orWhere('keterangan', 'AYAM KARKAS BROILER (RM) '. $checkUkuranKarkas);
                                                })
                                                ->first();
                        }

                    } else {
                        $purchase_item = PurchaseItem::where('purchasing_id', $produksi->prodpur->id)->first();

                    }
                    // dd($purchase_item, $produksi->prodpur->id, $gd->gradngToitem->nama);
                    if($purchase_item){
                        $netsuite                   =   new Netsuite;
                        $netsuite->record_type      =   "itemreceipt";
                        $netsuite->label            =   "item_receipt_fresh";
                        $netsuite->document_code    =   $produksi->no_po;
                        $netsuite->trans_date       =   $gd->tanggal_potong ?? $produksi->prod_tanggal_potong;
                        $netsuite->user_id          =   Auth::user()->id ?? NULL;
                        $netsuite->tabel            =   "productions";
                        $netsuite->paket_id         =   "0";
                        $netsuite->tabel_id         =   $produksi->id;
                        $netsuite->subsidiary_id    =   env("NET_SUBSIDIARY_ID", "6");
                        $netsuite->subsidiary       =   env("NET_SUBSIDIARY", "CGL");
                        $netsuite->id_location      =   Gudang::gudang_netid($nama_gudang_bb);
                        $netsuite->location         =   $nama_gudang_bb;
        
                        if (!$netsuite->save()) {
                            DB::rollBack();
                            return back()->with('status', 2)->with('message', 'Proses gagal');
                        }


                        $items = [];
                        $items[] = array(
                            "line"                  =>  $purchase_item->internal_id_po ?? '1',
                            "internal_id_item"      =>  (string)Item::item_sku(($purchase_item->item_po ?? "1100000011"))->netsuite_internal_id,
                            "item_code"             =>  ($purchase_item->item_po ?? "1100000011"),
                            "qty"                   =>  $gd->berat_item ,
                            "qty_in_ekor"           =>  $gd->total_item ,
                            "internal_id_location"  =>  (string)Gudang::gudang_netid($nama_gudang_bb),
                            "gudang"                =>  $nama_gudang_bb,
                            "keterangan"            =>  $gd->gradngToitem->nama
                        );
        
                        $net    =   Netsuite::find($netsuite->id);
                        $data   =   [
                            "record_type"   =>  "itemreceipt",
                            "data"          =>  [
                                [
                                    "appsid"            =>  env('NET_SUBSIDIARY', 'CGL')."-".$net->id,
                                    "internal_id_po"    =>  $produksi->prodpur->internal_id_po ?? "",
                                    "po_number"         =>  $produksi->no_po,
                                    "date"              =>  $gd->tanggal_potong ?? $produksi->prod_tanggal_potong,
                                    "date"              =>  date("d-M-Y", strtotime($gd->tanggal_potong ?? $produksi->prod_tanggal_potong)),
                                    "memo"              =>  $produksi->id."-".$produksi->no_lpah,
                                    "no_nota"           =>  $produksi->no_do,
                                    "tanggal_nota"      =>  date("d-M-Y", strtotime($gd->tanggal_potong ?? $produksi->prod_tanggal_potong)),
                                    "line"              =>  $items
                                ]
                            ]
                        ];
                        // dd($data);
        
                        $net->script            =   '211';
                        $net->deploy            =   '1';
                        $net->data_content      =   json_encode($data);
                        $net->status            =   2;
        
                        $net->save();
    
                        
                        $gd->status = 1;
                        $gd->save();
        
                    }
                    
                endforeach;
                DB::commit();


            }

        }
    }


    public static function wo_retur($nama_tabel, $id_tabel, $transfer, $tanggal, $code)
    {

            $nama_gudang_lb     = env('NET_SUBSIDIARY', 'CGL')." - Storage Live Bird";
            $nama_gudang_expedisi     = env('NET_SUBSIDIARY', 'CGL')." - Storage Expedisi";
            $nama_gudang_bb           = env('NET_SUBSIDIARY', 'CGL')." - Chiller Bahan Baku";
            $nama_gudang_fg           = env('NET_SUBSIDIARY', 'CGL')." - Chiller Finished Good";
            $nama_gudang_abf          = env('NET_SUBSIDIARY', 'CGL')." - Storage ABF";
            $nama_gudang_wip          = env('NET_SUBSIDIARY', 'CGL')." - Storage Produksi (WIP)";
            $nama_gudang_susut          = env('NET_SUBSIDIARY', 'CGL')." - Storage Susut";
            $nama_gudang_retur          = env('NET_SUBSIDIARY', 'CGL')." - Storage Retur";
            $nama_gudang_other          = env('NET_SUBSIDIARY', 'CGL')." - Storage Others";

            $item           = $transfer[0];
            // WO retur
            $id_location    =   Gudang::gudang_netid($nama_gudang_bb) ;
            $location       =   $nama_gudang_bb ;
            $from           =   $id_location;

            $label          =   'wo-6';

            $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL')." - RETUR KARKAS KE BAHAN BAKU")
                                ->first();

            $nama_assembly  =   $bom->bom_name ;
            $id_assembly    =   $bom->netsuite_internal_id ;

            $bom_id         =   $bom->id;
            $item_assembly  =   env('NET_SUBSIDIARY', 'CGL')." - RETUR KARKAS KE BAHAN BAKU";

            $component      =   [[
                "type"              =>  "Component",
                "internal_id_item"  =>  (string)$item['internal_id_item'],
                "item"              =>  (string)$item['item'],
                "description"       =>  (string)Item::item_sku($item['item'])->nama,
                "qty"               =>  (string)$item['qty_to_transfer'],
            ]];

            $proses =   [];
            foreach ($bom->bomproses as $row) {
                $proses[]   =   [
                    "type"              =>  "Component",
                    "internal_id_item"  =>  (string)Item::item_sku($row->sku)->netsuite_internal_id,
                    "item"              =>  $row->sku,
                    "description"       =>  (string)Item::item_sku($row->sku)->nama,
                    "qty"               =>  (string)Item::where('nama', 'AY - S')->first()->sku == $row->sku ? $row->qty_per_assembly : ($row->qty_per_assembly * $item['qty_to_transfer']),
                ];
            }

            $finished_good  =   [[
                "type"              =>  "Finished Goods",
                "internal_id_item"  =>  (string)Item::item_sku("1100000001")->netsuite_internal_id ,
                "item"              =>  "1100000001",
                "description"       =>  "AYAM KARKAS BROILER (RM)",
                "qty"               =>  $item['qty_to_transfer']
            ]];

            $produksi       =   array_merge($component, $proses, $finished_good);
            $nama_tabel     =   $nama_tabel;
            $id_tabel       =   $id_tabel;

            $wo = Netsuite::work_order_doc($nama_tabel, $id_tabel, $label, $id_assembly, $item_assembly, $id_location, $location, $produksi, null, $tanggal, $code);

            $label          =   'wo-6-build';
            $total          =   $item['qty_to_transfer'];
            $wob = Netsuite::wo_build_doc($nama_tabel, $id_tabel, $label, $id_assembly, $item_assembly, $id_location, $location, $total, $produksi, $wo->id, $tanggal, $code);


    }

    public static function wo_retur_tukaritem($nama_tabel, $id_tabel, $transfer, $tanggal, $code, $line, $id_item, $tujuan)
    {

            $nama_gudang_lb             = env('NET_SUBSIDIARY', 'CGL')." - Storage Live Bird";
            $nama_gudang_expedisi       = env('NET_SUBSIDIARY', 'CGL')." - Storage Expedisi";
            $nama_gudang_bb             = env('NET_SUBSIDIARY', 'CGL')." - Chiller Bahan Baku";
            $nama_gudang_fg             = env('NET_SUBSIDIARY', 'CGL')." - Chiller Finished Good";
            $nama_gudang_abf            = env('NET_SUBSIDIARY', 'CGL')." - Storage ABF";
            $nama_gudang_wip            = env('NET_SUBSIDIARY', 'CGL')." - Storage Produksi (WIP)";
            $nama_gudang_susut          = env('NET_SUBSIDIARY', 'CGL')." - Storage Susut";
            $nama_gudang_retur          = env('NET_SUBSIDIARY', 'CGL')." - Storage Retur";
            $nama_gudang_other          = env('NET_SUBSIDIARY', 'CGL')." - Storage Others";

            // ITEM LAMA
            $item                       = $transfer[0];

            // TI retur
            if ($tujuan == 'chillerfg') {
                $to             =   Gudang::gudang_netid($nama_gudang_fg);
                $location       =   $nama_gudang_fg ;
                
            } elseif ($tujuan == 'chillerbb') {
                $to             =   Gudang::gudang_netid($nama_gudang_bb);
                $location       =   $nama_gudang_bb ;

            } elseif ($tujuan == 'musnahkan') {
                $to             =   Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Storage Susut");
                $location       =   $nama_gudang_susut ;

            } else {
                $to             =   Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Storage ABF");
                $location       =   $nama_gudang_abf ;
            }
            

            // CARI ITEM BARU DI RETUR ITEM
            $retur_item_baru            =   ReturItem::where('item_id', $id_item)->where('line_request', $line)->where('retur_id', $id_tabel)->first();

            // CARI ITEM LAMA
            $item_retur                 =   Item::item_sku($item['item']);

            $label                      =   'wo-7';
            $type = (($retur_item_baru->to_item->category_id == 4) OR ($retur_item_baru->to_item->category_id == 6) OR ($retur_item_baru->to_item->category_id == 10) OR ($retur_item_baru->to_item->category_id == 16)) ? "BY PRODUCT" : "KARKAS";

            $bom                        =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL')." - TUKAR ITEM ". $type)
                                                ->first();
            $nama_assembly              =   $bom->bom_name ;
            $id_assembly                =   $bom->netsuite_internal_id ;

            $bom_id                     =   $bom->id;
            $item_assembly              =   env('NET_SUBSIDIARY', 'CGL')." - TUKAR ITEM ". $type;

            $proses =   [];

            // ITEM LAMA SEBAGAI COMPONENT
            $component      =   [[
                "type"              =>  "Component",
                "internal_id_item"  =>  (string)$item_retur->netsuite_internal_id,
                "item"              =>  (string)$item_retur->sku,
                "description"       =>  (string)$item_retur->nama,
                "qty"               =>  (string)$item['qty_to_transfer'],
            ]];

            // ITEM BARU SEBAGAI FINISHED GOOD
            $finished_good  =   [[
                "type"              =>  "Finished Goods",
                "internal_id_item"  =>  (string)$retur_item_baru->to_item->netsuite_internal_id ,
                "item"              =>  (string)$retur_item_baru->sku,
                "description"       =>  (string)$retur_item_baru->to_item->nama,
                "qty"               =>  $item['qty_to_transfer']
            ]];

            $produksi       =   array_merge($component, $proses, $finished_good);
            $nama_tabel     =   $nama_tabel;
            $id_tabel       =   $id_tabel;

            $wo = Netsuite::work_order_doc($nama_tabel, $id_tabel, $label, $id_assembly, $item_assembly, $to, $location, $produksi, null, $tanggal, $code);

            $label          =   'wo-7-build';
            $total          =   $item['qty_to_transfer'];
            $wob = Netsuite::wo_build_doc($nama_tabel, $id_tabel, $label, $id_assembly, $item_assembly, $to, $location, $total, $produksi, $wo->id, $tanggal, $code);


    }


    public static function wo_tukaritem($nama_tabel, $id_tabel, $item)
    {

        $nama_gudang_lb     = env('NET_SUBSIDIARY', 'CGL')." - Storage Live Bird";
        $nama_gudang_expedisi     = env('NET_SUBSIDIARY', 'CGL')." - Storage Expedisi";
        $nama_gudang_bb           = env('NET_SUBSIDIARY', 'CGL')." - Chiller Bahan Baku";
        $nama_gudang_fg           = env('NET_SUBSIDIARY', 'CGL')." - Chiller Finished Good";
        $nama_gudang_abf          = env('NET_SUBSIDIARY', 'CGL')." - Storage ABF";
        $nama_gudang_wip          = env('NET_SUBSIDIARY', 'CGL')." - Storage Produksi (WIP)";
        $nama_gudang_susut          = env('NET_SUBSIDIARY', 'CGL')." - Storage Susut";
        $nama_gudang_retur          = env('NET_SUBSIDIARY', 'CGL')." - Storage Retur";
        $nama_gudang_other          = env('NET_SUBSIDIARY', 'CGL')." - Storage Others";

        $array          =   [] ;

        $location       =   $nama_gudang_fg ;
        $id_location    =   Gudang::gudang_netid($location) ;

        $label          =   'wo-7';

        $item_assembly  =   env('NET_SUBSIDIARY', 'CGL')." - TUKAR ITEM KARKAS";
        $bom            =   Bom::where('bom_name', $item_assembly)->first();

        $nama_assembly  =   $bom->bom_name ;
        $id_assembly    =   $bom->netsuite_internal_id ;
        $bom_id         =   $bom->id;

        $array[]        =   [
            "type"              =>  "Component",
            "internal_id_item"  =>  (string)$item[0]['internal_id_item'],
            "item"              =>  (string)$item[0]['item'],
            "description"       =>  (string)Item::item_sku($item[0]['item'])->nama,
            "qty"               =>  (string)$item[0]['qty_to_transfer'],
        ];

        $array[]         =   [
            "type"              =>  "Finished Goods",
            "internal_id_item"  =>  (string)$item[1]['internal_id_item'],
            "item"              =>  (string)$item[1]['item'],
            "description"       =>  (string)Item::item_sku($item[1]['item'])->nama,
            "qty"               =>  (string)$item[1]['qty_to_transfer'],
        ];

        $wo     =   Netsuite::work_order($nama_tabel, $id_tabel, $label, $id_assembly, $item_assembly, $id_location, $location, $array, null);

        $label  =   'wo-7-build';
        $total  =   $item[0]['qty_to_transfer'];
        $wob    =   Netsuite::wo_build($nama_tabel, $id_tabel, $label, $id_assembly, $item_assembly, $id_location, $location, $total, $array, $wo->id);
    }

    public static function wo_tukar_bb($nama_tabel, $id_tabel, $chiller_out, $chiller_in)
    {

        $nama_gudang_lb     = env('NET_SUBSIDIARY', 'CGL')." - Storage Live Bird";
        $nama_gudang_expedisi     = env('NET_SUBSIDIARY', 'CGL')." - Storage Expedisi";
        $nama_gudang_bb           = env('NET_SUBSIDIARY', 'CGL')." - Chiller Bahan Baku";
        $nama_gudang_fg           = env('NET_SUBSIDIARY', 'CGL')." - Chiller Finished Good";
        $nama_gudang_abf          = env('NET_SUBSIDIARY', 'CGL')." - Storage ABF";
        $nama_gudang_wip          = env('NET_SUBSIDIARY', 'CGL')." - Storage Produksi (WIP)";
        $nama_gudang_susut          = env('NET_SUBSIDIARY', 'CGL')." - Storage Susut";
        $nama_gudang_retur          = env('NET_SUBSIDIARY', 'CGL')." - Storage Retur";
        $nama_gudang_other          = env('NET_SUBSIDIARY', 'CGL')." - Storage Others";

        $array          =   [] ;

        $location       =   $nama_gudang_bb ;
        $id_location    =   Gudang::gudang_netid($location) ;

        $label          =   'wo-7';

        $item_assembly  =   env('NET_SUBSIDIARY', 'CGL')." - TUKAR ITEM KARKAS";
        $bom            =   Bom::where('bom_name', $item_assembly)->first();

        $nama_assembly  =   $bom->bom_name ;
        $id_assembly    =   $bom->netsuite_internal_id ;
        $bom_id         =   $bom->id;

        $berat_rm       = 0;
        $berat_memar    = 0;
        $array          = [];

        if (substr($chiller_out->chillitem->sku, 0, 5) == "12111") {


            $berat_rm       =  $chiller_out->berat_item;
            $berat_memar    =  $chiller_in->berat_item;

            $array[]  =   [
                "type"              =>  "Component",
                "internal_id_item"  =>  (string)Item::item_sku("1100000001")->netsuite_internal_id,
                "item"              =>  "1100000001",
                "description"       =>  "AYAM KARKAS BROILER (RM)",
                "qty"               =>  "$berat_rm",
            ];

            // FINISHED GOOD AYAM MEMAR (RM) /

            $array[]  =   [
                "type"              =>  "Finished Goods",
                "internal_id_item"  =>  (string)Item::item_sku("1100000003")->netsuite_internal_id,
                "item"              =>  "1100000003",
                "description"       =>  "AYAM MEMAR (RM)",
                "qty"               =>  "$berat_memar",
            ];


        } elseif (substr($chiller_out->chillitem->sku, 0, 5) == "12113") {


            $berat_memar    =  $chiller_out->berat_item;
            $berat_rm       =  $chiller_in->berat_item;


            // FINISHED GOOD AYAM MEMAR (RM) /

            $array[]  =   [
                "type"              =>  "Component",
                "internal_id_item"  =>  (string)Item::item_sku("1100000003")->netsuite_internal_id,
                "item"              =>  "1100000003",
                "description"       =>  "AYAM MEMAR (RM)",
                "qty"               =>  "$berat_memar",
            ];


            $array[]  =   [
                "type"              =>  "Finished Goods",
                "internal_id_item"  =>  (string)Item::item_sku("1100000001")->netsuite_internal_id,
                "item"              =>  "1100000001",
                "description"       =>  "AYAM KARKAS BROILER (RM)",
                "qty"               =>  "$berat_rm",
            ];

        }


        $wo     =   Netsuite::work_order($nama_tabel, $id_tabel, $label, $id_assembly, $item_assembly, $id_location, $location, $array, null);

        $label  =   'wo-7-build';
        $total  =   $chiller_in->berat_item;
        $wob    =   Netsuite::wo_build($nama_tabel, $id_tabel, $label, $id_assembly, $item_assembly, $id_location, $location, $total, $array, $wo->id);
    }

    public static function work_order_date($table, $id, $label, $id_assembly, $item_assembly, $id_location, $location, $component, $paket_id, $tanggal)
    {

        $nama_gudang_lb     = env('NET_SUBSIDIARY', 'CGL')." - Storage Live Bird";
        $nama_gudang_expedisi     = env('NET_SUBSIDIARY', 'CGL')." - Storage Expedisi";
        $nama_gudang_bb           = env('NET_SUBSIDIARY', 'CGL')." - Chiller Bahan Baku";
        $nama_gudang_fg           = env('NET_SUBSIDIARY', 'CGL')." - Chiller Finished Good";
        $nama_gudang_abf          = env('NET_SUBSIDIARY', 'CGL')." - Storage ABF";
        $nama_gudang_wip          = env('NET_SUBSIDIARY', 'CGL')." - Storage Produksi (WIP)";
        $nama_gudang_susut          = env('NET_SUBSIDIARY', 'CGL')." - Storage Susut";
        $nama_gudang_retur          = env('NET_SUBSIDIARY', 'CGL')." - Storage Retur";
        $nama_gudang_other          = env('NET_SUBSIDIARY', 'CGL')." - Storage Others";

        $netsuite                   =   new Netsuite;
        $netsuite->record_type      =   "work_order";
        $netsuite->label            =   "$label";
        $netsuite->trans_date       =   date("Y-m-d", strtotime($tanggal));
        $netsuite->user_id          =   Auth::user()->id ?? NULL;
        $netsuite->tabel            =   "$table";
        $netsuite->paket_id         =   "$paket_id";
        $netsuite->tabel_id         =   $id;
        $netsuite->subsidiary_id    =   env("NET_SUBSIDIARY_ID", "2");
        $netsuite->subsidiary       =   env("NET_SUBSIDIARY", "CGL");
        $netsuite->id_location      =   "$id_location";
        $netsuite->location         =   "$location";

        if (!$netsuite->save()) {
            DB::rollBack();
            return back()->with('status', 2)->with('message', 'Proses gagal');
        }

        $net        =   Netsuite::find($netsuite->id);

        $total = 0;

        $data_component = json_decode(json_encode($component));

        foreach($data_component as $row):
            if($row->type=="Finished Goods"){
                $total = $total+$row->qty;
            }
        endforeach;

        $data_wo    =   [
            "record_type"     =>     "work_order",
            "data"          =>  [
                [
                    "appsid"                    =>  env('NET_SUBSIDIARY', 'CGL')."-".(string)$netsuite->id,
                    "internal_id_subsidiary"    =>  env("NET_SUBSIDIARY_ID", "2"),
                    "subsidiary"                =>  env("NET_SUBSIDIARY", "CGL"),
                    "transaction_date"          =>  date("d-M-Y", strtotime($tanggal)),
                    "internal_id_customer"      =>  "",
                    "customer"                  =>  "",
                    "id_item_assembly"          =>  "$id_assembly",
                    "item_assembly"             =>  "$item_assembly",
                    "id_location"               =>  "$id_location",
                    "location"                  =>  "$location",
                    "plan_qty"                  =>  "$total",
                    "items"                     =>  $component
                ]
            ]
        ];

        $net->script            =   '209';
        $net->deploy            =   '1';
        $net->data_content      =   json_encode($data_wo);
        $net->status            =   5;

        if (!$net->save()) {
            DB::rollBack();
            return back()->with('status', 2)->with('message', 'Proses gagal');
        }

        return $net;
    }




    public static function wo_build_date($tabel, $id, $label, $id_assembly, $item_assembly, $id_location, $location, $total, $array, $paket_id, $tanggal)
    {

        $nama_gudang_lb     = env('NET_SUBSIDIARY', 'CGL')." - Storage Live Bird";
        $nama_gudang_expedisi     = env('NET_SUBSIDIARY', 'CGL')." - Storage Expedisi";
        $nama_gudang_bb           = env('NET_SUBSIDIARY', 'CGL')." - Chiller Bahan Baku";
        $nama_gudang_fg           = env('NET_SUBSIDIARY', 'CGL')." - Chiller Finished Good";
        $nama_gudang_abf          = env('NET_SUBSIDIARY', 'CGL')." - Storage ABF";
        $nama_gudang_wip          = env('NET_SUBSIDIARY', 'CGL')." - Storage Produksi (WIP)";
        $nama_gudang_susut          = env('NET_SUBSIDIARY', 'CGL')." - Storage Susut";
        $nama_gudang_retur          = env('NET_SUBSIDIARY', 'CGL')." - Storage Retur";
        $nama_gudang_other          = env('NET_SUBSIDIARY', 'CGL')." - Storage Others";

        $netsuite                   =   new Netsuite;
        $netsuite->record_type      =   "wo_build";
        $netsuite->label            =   "$label";
        $netsuite->trans_date       =   date("Y-m-d", strtotime($tanggal));
        $netsuite->user_id          =   Auth::user()->id ?? NULL;
        $netsuite->tabel            =   "$tabel";
        $netsuite->paket_id         =   "$paket_id";
        $netsuite->tabel_id         =   $id;
        $netsuite->subsidiary_id    =   env("NET_SUBSIDIARY_ID", "2");
        $netsuite->subsidiary       =   env("NET_SUBSIDIARY", "CGL");
        $netsuite->id_location      =   "$id_location";
        $netsuite->location         =   "$location";

        if (!$netsuite->save()) {
            DB::rollBack();
            return back()->with('status', 2)->with('message', 'Proses gagal');
        }

        $net    =   Netsuite::find($netsuite->id);

        $data_component = json_decode(json_encode($array));

        $qty_to_build = 0;
        foreach($data_component as $row):
            if($row->type=="Finished Goods"){
                $qty_to_build = $qty_to_build+$row->qty;
            }
        endforeach;

        $result =   [
            "record_type"     =>     "wo_build",
            "data"          =>  [
                [
                    "appsid"                    =>  env('NET_SUBSIDIARY', 'CGL')."-".(string)$netsuite->id,
                    "transaction_date"          =>  date("d-M-Y", strtotime($tanggal)),
                    "qty_to_build"              =>  "$qty_to_build",
                    "created_from_wo"           =>  "",
                    "items"                     =>  $array
                ]
            ]
        ];

        $net->script            =   '215';
        $net->deploy            =   '1';
        $net->data_content      =   json_encode($result);
        $net->status            =   5;

        if (!$net->save()) {
            DB::rollBack();
            return back()->with('status', 2)->with('message', 'Proses gagal');
        }
        return $net;
    }




    public static function transfer_inventory_date($tabel, $id, $label, $id_location, $location, $from, $to, $data, $paket_id, $tanggal)
    {

        $nama_gudang_lb     = env('NET_SUBSIDIARY', 'CGL')." - Storage Live Bird";
        $nama_gudang_expedisi     = env('NET_SUBSIDIARY', 'CGL')." - Storage Expedisi";
        $nama_gudang_bb           = env('NET_SUBSIDIARY', 'CGL')." - Chiller Bahan Baku";
        $nama_gudang_fg           = env('NET_SUBSIDIARY', 'CGL')." - Chiller Finished Good";
        $nama_gudang_abf          = env('NET_SUBSIDIARY', 'CGL')." - Storage ABF";
        $nama_gudang_wip          = env('NET_SUBSIDIARY', 'CGL')." - Storage Produksi (WIP)";
        $nama_gudang_susut          = env('NET_SUBSIDIARY', 'CGL')." - Storage Susut";
        $nama_gudang_retur          = env('NET_SUBSIDIARY', 'CGL')." - Storage Retur";
        $nama_gudang_other          = env('NET_SUBSIDIARY', 'CGL')." - Storage Others";

        $netsuite                   =   new Netsuite;
        $netsuite->record_type      =   "transfer_inventory";
        $netsuite->label            =   "$label";
        $netsuite->trans_date       =   date("Y-m-d", strtotime($tanggal));
        $netsuite->user_id          =   Auth::user()->id ?? NULL;
        $netsuite->tabel            =   "$tabel";
        $netsuite->paket_id         =   "$paket_id";
        $netsuite->tabel_id         =   $id;
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
                    "transaction_date"          =>  date("d-M-Y", strtotime($tanggal)),
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

    public static function purchase_order($tabel, $id, $label, $paket_id, $tanggal)
    {

        DB::beginTransaction();

        $nama_gudang_lb              = env('NET_SUBSIDIARY', 'CGL')." - Storage Live Bird";
        $nama_gudang_expedisi        = env('NET_SUBSIDIARY', 'CGL')." - Storage Expedisi";
        $nama_gudang_bb              = env('NET_SUBSIDIARY', 'CGL')." - Chiller Bahan Baku";
        $nama_gudang_fg              = env('NET_SUBSIDIARY', 'CGL')." - Chiller Finished Good";
        $nama_gudang_abf             = env('NET_SUBSIDIARY', 'CGL')." - Storage ABF";
        $nama_gudang_wip             = env('NET_SUBSIDIARY', 'CGL')." - Storage Produksi (WIP)";
        $nama_gudang_susut           = env('NET_SUBSIDIARY', 'CGL')." - Storage Susut";
        $nama_gudang_retur           = env('NET_SUBSIDIARY', 'CGL')." - Storage Retur";
        $nama_gudang_other           = env('NET_SUBSIDIARY', 'CGL')." - Storage Others";

        $netsuite                   =   new Netsuite;
        $netsuite->record_type      =   "purchase_order";
        $netsuite->label            =   "$label";
        $netsuite->trans_date       =   date("Y-m-d", strtotime($tanggal));
        $netsuite->user_id          =   Auth::user()->id ?? NULL;
        $netsuite->tabel            =   "$tabel";
        $netsuite->paket_id         =   "$paket_id";
        $netsuite->tabel_id         =   $id;
        // $netsuite->subsidiary_id    =   env("NET_SUBSIDIARY_ID", "2");
        $netsuite->subsidiary_id    =   Session::get('subsidiary_id');
        // $netsuite->subsidiary       =   env("NET_SUBSIDIARY", "CGL");
        $netsuite->subsidiary       =   Session::get('subsidiary');
        $netsuite->id_location      =   "";
        $netsuite->location         =   "";

        if (!$netsuite->save()) {
            DB::rollBack();
            return back()->with('status', 2)->with('message', 'Proses gagal');
        }

        $net    =   Netsuite::find($netsuite->id);
        $po     =   Pembelianheader::find($id);

        if($po){

            $items = [];

            // $ukuran = ['  ', '< 1.1', ' 1.1-1.3', '1.2-1.4', '1.3-1.5', '1.4-1.6', '1.5-1.7', '1.6-1.8', '1.7-1.9', ' 1.8-2.0', '1.9-2.1', '2.0-2.2', ' 2.2 Up', '1.2-1.5', '1.3-1.6', '1.5-1.8'];
            // $ukuran = ['  ', '< 1.1', ' 1.1-1.3', '1.2-1.4', '1.3-1.5', '1.4-1.6', '1.5-1.7', '1.6-1.8', '1.7-1.9', ' 1.8-2.0', '1.9-2.1', '2.0-2.2', ' 2.2 Up', '1.2-1.5', '1.3-1.6', '1.5-1.8', '2.0-2.5', '2.5-3.0', '3.0 Up', '1.4-1.7', '4.0 up'];
            $memo_header = [];
            foreach($po->list_pembelian as $p){
                if ($p->ukuran_ayam == '1') {
                    $ukuran = '< 1.1';
                } 
                else if ($p->ukuran_ayam == '2') {
                    $ukuran = '1.1-1.3';
                }
                else if ($p->ukuran_ayam == '3') {
                    $ukuran = '1.2-1.4';
                }
                else if ($p->ukuran_ayam == '4') {
                    $ukuran = '1.3-1.5';
                }
                else if ($p->ukuran_ayam == '5') {
                    $ukuran = '1.4-1.6';
                }
                else if ($p->ukuran_ayam == '6') {
                    $ukuran = '1.5-1.7';
                }
                else if ($p->ukuran_ayam == '7') {
                    $ukuran = '1.6-1.8';
                }
                else if ($p->ukuran_ayam == '8') {
                    $ukuran = '1.7-1.9';
                }
                else if ($p->ukuran_ayam == '9') {
                    $ukuran = '1.8-2.0';
                }
                else if ($p->ukuran_ayam == '10') {
                    $ukuran = '1.9-2.1';
                }
                else if ($p->ukuran_ayam == '15') {
                    $ukuran = '1.3-1.6';
                }
                else if ($p->ukuran_ayam == '16') {
                    $ukuran = '1.4-1.7';
                }
                else if ($p->ukuran_ayam == '17') {
                    $ukuran = '1.5-1.8';
                }
                else if ($p->ukuran_ayam == '18') {
                    $ukuran = '2.0-2.5';
                }
                else if ($p->ukuran_ayam == '19') {
                    $ukuran = '2.5-3.0';
                }
                else if ($p->ukuran_ayam == '20') {
                    $ukuran = '3.0 up';
                }
                else if ($p->ukuran_ayam == '21') {
                    $ukuran = '4.0 up';
                }

            // dd($p);

                $item_list = Item::find($p->item_id);

                $pembelian_item_parent = Pembelianlist::find($p->parent);
                if($pembelian_item_parent){
                    $memo_header[] = $pembelian_item_parent->keterangan;
                }
                
                $item_keterangan = null;

                if($p->keterangan){
                    $item_keterangan = $item_list->nama." ".($p->keterangan ?? "");
                }else{
                    $item_keterangan = null;
                }

                if($po->type_po=="PO LB" || $po->type_po=="PO Maklon"){

                    $item_keterangan = $item_list->nama." ".($ukuran ?? "")." ".($p->keterangan ?? "");
                    // dd($ukuran[$p->ukuran_ayam]);
                }

                if($item_list){
                    $items[]  = array(
                        "line_id"           =>  null,
                        "internal_id_item"  =>  $item_list->netsuite_internal_id,
                        "sku"               =>  $item_list->sku,
                        "nama"              =>  $item_list->nama,
                        "qty_in_ekor"       =>  $p->qty,
                        "unit_cetakan"      =>  $p->unit_cetakan,
                        "ukuran_ayam"       =>  $p->ukuran_ayam ?? 1,
                        "jumlah_do"         =>  $p->jumlah_do ?? 1,
                        "qty_ekr_pcs_pack"  =>  $p->qty,
                        "harga_ekr_pcs_pack"=>  $p->harga,
                        "harga_cetakan"     =>  $p->unit_cetakan,
                        "quantity"          =>  $p->berat ?? $p->qty,
                        "rate"              =>  $p->harga,
                        "keterangan"        =>  $item_keterangan,
                        "description"       =>  $item_keterangan,
                        "gudang"            =>  $p->gudang,
                        "isdelete"          =>  $p->deleted_at ? 1 : 0
                    );
                }
            }
           
            if($po->memo){
                $final_memo_header = $po->memo." ".implode(',', $memo_header);
            }else{
                $final_memo_header = implode(',', $memo_header);
            }

            if(strlen($final_memo_header)>270){
                $final_memo_header = substr($final_memo_header,270) . " ...";
            }
    
            $result =   [
                "record_type"   =>  "purchase_order",
                "data"          =>  [
                    [
                        "appsid"                => "CLOUD".".".Session::get('subsidiary').".PO.".(string)$netsuite->id,
                        "internal_id_vendor"    => Supplier::find($po->supplier_id)->netsuite_internal_id ?? NULL,
                        "nama_vendor"           => $po->vendor_name,
                        "tipe_purchase_order"   => $po->type_po,
                        "form_id"               => $po->form_id,
                        "no_pr"                 => $po->no_pr,
                        "date"                  => date("d-M-Y", strtotime($po->tanggal)),
                        "jenis_expedisi"        => $po->jenis_ekspedisi,
                        "memo"                  => $final_memo_header,
                        "attachment_link"       => $po->link_url ?? NULL,
                        "franco_loco"           => $po->franco_loco ?? NULL,
                        "count"                 => 0,
                        "tanggal_kirim"         => date("d-M-Y", strtotime($po->tanggal_kirim)),
                        "createdby"             => Auth::user()->netsuite_internal_id ?? '278504',
                        "line"                  => $items
                    ]
    
                ]
            ];
    
            $net->script            =   '281';
            $net->deploy            =   '1';
            $net->data_content      =   json_encode($result);
            $net->status            =   2;
            $net->count             =   0;

        }

        if (!$net->save()) {
            DB::rollBack();
            return false;
        }

        DB::commit();
        return $net;
    }

    public static function update_purchase_order($tabel, $id, $label, $paket_id, $tanggal)
    {

        DB::beginTransaction();

        $netsuite                   =   Netsuite::where('record_type', 'purchase_order')
                                        ->where('tabel_id', $id)
                                        ->first();

        if (!$netsuite) {
            DB::rollBack();
            return FALSE;
        }

        $net    =   Netsuite::find($netsuite->id);
        $po     =   Pembelianheader::find($id);

        if($po){

            $items = [];
            // $ukuran = ['  ', '< 1.1', ' 1.1-1.3', '1.2-1.4', '1.3-1.5', '1.4-1.6', '1.5-1.7', '1.6-1.8', '1.7-1.9', ' 1.8-2.0', '1.9-2.1', '2.0-2.2', ' 2.2 Up', '1.2-1.5', '1.3-1.6', '1.5-1.8'];
            foreach($po->list_pembelian as $p){
                if ($p->ukuran_ayam == '1') {
                    $ukuran = '< 1.1';
                } 
                else if ($p->ukuran_ayam == '2') {
                    $ukuran = '1.1-1.3';
                }
                else if ($p->ukuran_ayam == '3') {
                    $ukuran = '1.2-1.4';
                }
                else if ($p->ukuran_ayam == '4') {
                    $ukuran = '1.3-1.5';
                }
                else if ($p->ukuran_ayam == '5') {
                    $ukuran = '1.4-1.6';
                }
                else if ($p->ukuran_ayam == '6') {
                    $ukuran = '1.5-1.7';
                }
                else if ($p->ukuran_ayam == '7') {
                    $ukuran = '1.6-1.8';
                }
                else if ($p->ukuran_ayam == '8') {
                    $ukuran = '1.7-1.9';
                }
                else if ($p->ukuran_ayam == '9') {
                    $ukuran = '1.8-2.0';
                }
                else if ($p->ukuran_ayam == '10') {
                    $ukuran = '1.9-2.1';
                }
                else if ($p->ukuran_ayam == '15') {
                    $ukuran = '1.3-1.6';
                }
                else if ($p->ukuran_ayam == '16') {
                    $ukuran = '1.4-1.7';
                }
                else if ($p->ukuran_ayam == '17') {
                    $ukuran = '1.5-1.8';
                }
                else if ($p->ukuran_ayam == '18') {
                    $ukuran = '2.0-2.5';
                }
                else if ($p->ukuran_ayam == '19') {
                    $ukuran = '2.5-3.0';
                }
                else if ($p->ukuran_ayam == '20') {
                    $ukuran = '3.0 up';
                }
                else if ($p->ukuran_ayam == '21') {
                    $ukuran = '4.0 up';
                }

                $item_list = Item::find($p->item_id);

                $item_keterangan = null;

                if($p->keterangan){
                    $item_keterangan = $item_list->nama." ".($p->keterangan ?? "");
                }else{
                    $item_keterangan = null;
                }

                if($po->type_po=="PO LB" || $po->type_po=="PO Maklon"){
                    $item_keterangan = $item_list->nama." ".($ukuran ?? "")." ".($p->keterangan ?? "");
                }

                if($item_list){
                    $items[]  = array(
                        "line_id"           =>  $p->line_id,
                        "internal_id_item"  =>  $item_list->netsuite_internal_id,
                        "sku"               =>  $item_list->sku,
                        "nama"              =>  $item_list->nama,
                        "qty_in_ekor"       =>  $p->qty,
                        "unit_cetakan"      =>  $p->unit_cetakan,
                        "ukuran_ayam"       =>  $p->ukuran_ayam ?? 1,
                        "jumlah_do"         =>  $p->jumlah_do ?? 1,
                        "qty_ekr_pcs_pack"  =>  $p->qty,
                        "harga_ekr_pcs_pack"=>  $p->harga,
                        "harga_cetakan"     =>  $p->unit_cetakan,
                        "quantity"          =>  $p->berat ?? $p->qty,
                        "rate"              =>  $p->harga,
                        "keterangan"        =>  $item_keterangan,
                        "description"       =>  $item_keterangan,
                        "gudang"            =>  $p->gudang,
                        "isdelete"          =>  $p->deleted_at ? 1 : 0,
                    );
                }
            }
           
    
            $result =   [
                "record_type"   =>  "purchase_order",
                "data"          =>  [
                    [
                        "appsid"                => "CLOUD".".".Session::get('subsidiary').".PO.".(string)$netsuite->id,
                        "internal_id_vendor"    => Supplier::find($po->supplier_id)->netsuite_internal_id ?? NULL,
                        "nama_vendor"           => $po->vendor_name,
                        "tipe_purchase_order"   => $po->type_po,
                        "form_id"               => $po->form_id,
                        "no_pr"                 => $po->no_pr,
                        "date"                  => date("d-M-Y", strtotime($po->tanggal)),
                        "jenis_expedisi"        => $po->jenis_ekspedisi,
                        "memo"                  => $po->memo,
                        "attachment_link"       => $po->link_url ?? NULL,
                        "franco_loco"           => $po->franco_loco ?? NULL,
                        "count"                 => (integer)$net->count,
                        "tanggal_kirim"         => date("d-M-Y", strtotime($po->tanggal_kirim)),
                        "createdby"             => Auth::user()->netsuite_internal_id ?? '278504',
                        "line"                  => $items
                    ]
    
                ]
            ];
    
            $net->data_content      =   json_encode($result);
            $net->status            =   2;

        }

        if (!$net->save()) {
            DB::rollBack();
            return false;
        }

        DB::commit();
        return $net;
    }

    public static function sales_order($tabel, $id, $label, $paket_id, $tanggal)
    {

        DB::beginTransaction();

        $so     =   MarketingSO::find($id);

        $nama_gudang_lb              = env('NET_SUBSIDIARY', 'CGL')." - Storage Live Bird";
        $nama_gudang_expedisi        = $so->subsidiary." - Storage Expedisi";
        $nama_gudang_bb              = env('NET_SUBSIDIARY', 'CGL')." - Chiller Bahan Baku";
        $nama_gudang_fg              = env('NET_SUBSIDIARY', 'CGL')." - Chiller Finished Good";
        $nama_gudang_abf             = env('NET_SUBSIDIARY', 'CGL')." - Storage ABF";
        $nama_gudang_wip             = env('NET_SUBSIDIARY', 'CGL')." - Storage Produksi (WIP)";
        $nama_gudang_susut           = env('NET_SUBSIDIARY', 'CGL')." - Storage Susut";
        $nama_gudang_retur           = env('NET_SUBSIDIARY', 'CGL')." - Storage Retur";
        $nama_gudang_other           = env('NET_SUBSIDIARY', 'CGL')." - Storage Others";

        $netsuite                   =   new Netsuite;
        $netsuite->record_type      =   "sales_order";
        $netsuite->label            =   "CLOUD".".".$so->subsidiary.".SO.".(string)$id;
        $netsuite->trans_date       =   date("Y-m-d", strtotime($so->tanggal_kirim));
        $netsuite->user_id          =   Auth::user()->id ?? $so->user_id;
        $netsuite->tabel            =   "$tabel";
        $netsuite->paket_id         =   "$paket_id";
        $netsuite->tabel_id         =   $id;
        // $netsuite->subsidiary_id    =   Session::get('subsidiary_id');
        $netsuite->subsidiary_id    =   $so->subsidiary == 'CGL' ? '1' : '2';
        $netsuite->subsidiary       =   $so->subsidiary;
        $netsuite->id_location      =   Gudang::gudang_netid($nama_gudang_expedisi);
        $netsuite->location         =   $nama_gudang_expedisi;

        if (!$netsuite->save()) {
            DB::rollBack();
            return back()->with('status', 2)->with('message', 'Proses gagal');
        }

        $net    =   Netsuite::find($netsuite->id);
        
        if($so){

            $items = [];
            foreach($so->listItem as $p){

                $item_list = Item::find($p->item_id);

                if($item_list){
                    $items[]  = array(
                        "line_id"           =>  $p->line_id,
                        "internal_id_item"  =>  $item_list->netsuite_internal_id,
                        "sku"               =>  $item_list->sku,
                        "nama"              =>  $item_list->nama,
                        "description"       =>  $p->description_item,
                        "quantity"          =>  $p->berat,
                        "qty_ekr_pcs_pack"  =>  $p->qty,
                        "rate"              =>  ($p->harga_cetakan == '1' ? $p->harga : ($p->harga+123)),
                        "parting"           =>  $p->parting,
                        "plastik"           =>  $p->plastik,
                        "bumbu"             =>  $p->bumbu,
                        "harga_ekr_pcs_pack"=>  ($p->harga_cetakan == '2' ? $p->harga : NULL),
                        "harga_cetakan"     =>  $p->harga_cetakan,
                        "memo"              =>  $p->memo,
                        "isdelete"          =>  $p->deleted_at ? 1 : 0
                    );
                }
            }
    
            $result =   [
                "record_type"   =>  "sales_order",
                "data"          =>  [
                    [
                        "appsid"                => "CLOUD".".".$so->subsidiary.".SO.".(string)$netsuite->id,
                        "form_id"               => $so->subsidiary == "CGL" ?  "143" : "158",
                        "internal_id_customer"  => Customer::find($so->customer_id)->netsuite_internal_id ?? NULL,
                        "date"                  => date("d-M-Y", strtotime($so->tanggal_kirim)),
                        "memo"                  => $so->memo,
                        "count"                 => 0,
                        "po_number"             => $so->po_number,
                        "tanggal_kirim"         => date("d-M-Y", strtotime($so->tanggal_kirim)),
                        "wilayah"               => $so->wilayah,
                        "gudang"                => Gudang::gudang_netid($nama_gudang_expedisi),
                        "createdby"             => $so->subsidiary == "CGL" ? '74768' : '123141',
                        "line"                  => $items
                    ]
    
                ]
            ];
    
            $net->script            =   '283';
            $net->deploy            =   '1';
            $net->data_content      =   json_encode($result);
            $net->status            =   2;
            $net->count             =   0;

        }

        if (!$net->save()) {
            DB::rollBack();
            return false;
        }

        DB::commit();
        return $net;
    }

    public static function update_sales_order($tabel, $id, $label, $paket_id, $tanggal)
    {

        DB::beginTransaction();

        $so     =   MarketingSO::find($id);


        $nama_gudang_lb              = env('NET_SUBSIDIARY', 'CGL')." - Storage Live Bird";
        $nama_gudang_expedisi        = $so->subsidiary." - Storage Expedisi";
        $nama_gudang_bb              = env('NET_SUBSIDIARY', 'CGL')." - Chiller Bahan Baku";
        $nama_gudang_fg              = env('NET_SUBSIDIARY', 'CGL')." - Chiller Finished Good";
        $nama_gudang_abf             = env('NET_SUBSIDIARY', 'CGL')." - Storage ABF";
        $nama_gudang_wip             = env('NET_SUBSIDIARY', 'CGL')." - Storage Produksi (WIP)";
        $nama_gudang_susut           = env('NET_SUBSIDIARY', 'CGL')." - Storage Susut";
        $nama_gudang_retur           = env('NET_SUBSIDIARY', 'CGL')." - Storage Retur";
        $nama_gudang_other           = env('NET_SUBSIDIARY', 'CGL')." - Storage Others";

        $netsuite                   =   Netsuite::where('record_type', 'sales_order')
                                        ->where('tabel_id', $id)
                                        ->first();

        if (!$netsuite) {
            DB::rollBack();
            return FALSE;
        }

        $net    =   Netsuite::find($netsuite->id);

        if($so){


            $exp            = json_decode($net->response) ;

            $items = [];
            foreach($so->listItem as $p){

                $item_list = Item::find($p->item_id);
                    if($net->response==""){
                        if($p->deleted_at==""){
                            $items[]  = array(
                                "line_id"           =>  $p->line_id,
                                "internal_id_item"  =>  $item_list->netsuite_internal_id,
                                "sku"               =>  $item_list->sku,
                                "nama"              =>  $item_list->nama,
                                "quantity"          =>  $p->berat,
                                "qty_ekr_pcs_pack"  =>  $p->qty,
                                "rate"              =>  ($p->harga_cetakan == '1' ? $p->harga : ($p->harga+123)),
                                "parting"           =>  $p->parting,
                                "plastik"           =>  $p->plastik,
                                "bumbu"             =>  $p->bumbu,
                                "harga_ekr_pcs_pack"=>  ($p->harga_cetakan == '2' ? $p->harga : NULL),
                                "harga_cetakan"     =>  $p->harga_cetakan,
                                "memo"              =>  $p->memo,
                                "description"       =>  $p->description_item,
                                "isdelete"          =>  0
                            );
                        }
                    }else{
                        $items[]  = array(
                                "line_id"           =>  $p->line_id,
                                "internal_id_item"  =>  $item_list->netsuite_internal_id,
                                "sku"               =>  $item_list->sku,
                                "nama"              =>  $item_list->nama,
                                "quantity"          =>  $p->berat,
                                "qty_ekr_pcs_pack"  =>  $p->qty,
                                "rate"              =>  ($p->harga_cetakan == '1' ? $p->harga : ($p->harga+123)),
                                "parting"           =>  $p->parting,
                                "plastik"           =>  $p->plastik,
                                "bumbu"             =>  $p->bumbu,
                                "harga_ekr_pcs_pack"=>  ($p->harga_cetakan == '2' ? $p->harga : NULL),
                                "harga_cetakan"     =>  $p->harga_cetakan,
                                "memo"              =>  $p->memo,
                                "description"       =>  $p->description_item,
                                "isdelete"          =>  $p->deleted_at ? 1 : 0
                            );
                    }

            }
           
    
            $result =   [
                "record_type"   =>  "sales_order",
                "data"          =>  [
                    [
                        "appsid"                => "CLOUD".".".$so->subsidiary.".SO.".(string)$netsuite->id,
                        "form_id"               => $so->subsidiary == "CGL" ?  "143" : "158",
                        "internal_id_customer"  => Customer::find($so->customer_id)->netsuite_internal_id ?? NULL,
                        "date"                  => date("d-M-Y", strtotime($so->tanggal_kirim)),
                        "memo"                  => $so->memo,
                        "count"                 => (integer)$net->count,
                        "po_number"             => $so->po_number,
                        "tanggal_kirim"         => date("d-M-Y", strtotime($so->tanggal_kirim)),
                        "wilayah"               => $so->wilayah,
                        "gudang"                => Gudang::gudang_netid($nama_gudang_expedisi),
                        "createdby"             => $so->subsidiary == "CGL" ? '74768' : '123141',
                        "line"                  => $items
                    ]
    
                ]
            ];
    
            $net->data_content      =   json_encode($result);
            $net->status            =   2;

        }

        if (!$net->save()) {
            DB::rollBack();
            return false;
        }

        DB::commit();
        return $net;
    }


    public static function update_no_do($netsuite_id){

        $netsuite = DB::select("UPDATE netsuite join chiller c on c.id = netsuite.tabel_id 
            join order_bahan_baku ob on c.table_id = ob.id
            set netsuite.no_do = ob.no_do, netsuite.order_id = ob.order_id, netsuite.order_item_id = ob.order_item_id
            where record_type = 'transfer_inventory' and netsuite.label like '%ekspedisi%'
            and ob.no_do is not null
            and netsuite.id = '".$netsuite_id."'");

        return $netsuite;
        
    }

    public static function getTimeResponse($params, $search){
        $query          = Netsuite::select($search)->where('tabel_id',$params)->get();
        if($query->count() > 0){
            foreach($query as $value){
                $hasil  = $value->$search;
            }
        }else{
            $hasil      = '';
        }

        return $hasil;

    }


}
