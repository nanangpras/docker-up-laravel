<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Order;
use App\Models\Item;
use App\Models\Bahanbaku;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


class OrderItem extends Model
{
    //
    protected $table = 'order_items';
    protected $appends  =   ['status_tujuan'];
    use SoftDeletes;

    public function itemorder()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function free_stock()
    {
        return $this->belongsTo(Freestock::class, 'id', 'orderitem_id') ;
    }

    public function free_stock_multi()
    {
        return $this->hasMany(Freestock::class, 'orderitem_id', 'id') ;
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    public function bahan_baku()
    {
        return $this->hasMany(Bahanbaku::class, 'order_item_id', 'id');
    }

    public function order_item_bb()
    {
        return $this->belongsTo(Bahanbaku::class, 'id', 'order_item_id');
    }

    public function chiller_data()
    {
        return $this->belongsTo(Chiller::class, 'id', 'chiller_out');
    }

    public function chillorder()
    {
        return $this->belongsTo(Chiller::class, 'id', 'table_id');
    }

    public function order_item_log()
    {
        return $this->hasMany(OrderItemLog::class, 'order_item_id', 'id');
    }

    public function getNetsuite() {
        return $this->hasOne(Netsuite::class, 'tabel_id', 'id')->where('label', 'itemfulfill')->where('tabel', 'orders');
    }

    // public function cekDataOrderBahanBaku(){
    //     return $this->hasMany(Bahanbaku::class,'order_item_id','id')->where('status', 1);
    // }

    public function getDeletedBahanBaku(){
        return $this->hasOne(Adminedit::class,'table_id', 'id')->where('table_name', 'order_items')->where('activity', 'delete_bb');
    }

    public function getHistoryReset(){
        return $this->hasOne(Adminedit::class,'table_id', 'id')->where('table_name','order_bahan_baku')->where('type','reset');
    }

    // public function fulfillNetsuite() {
    //     return $this->hasOne(Netsuite::class, 'tabel_id', 'id')->where('label', 'itemfulfill')->where('tabel', 'orders')->select('id','failed','status','response_id','document_code','trans_date','document_no','count','respon_time','tabel_id');
    // }
    public static function persen_order($id)
    {

            $countfull  = OrderItem::where('order_id', $id)->where('retur_status', null)->where('status','>=',2)->count();

            $countitem  = OrderItem::where('order_id', $id)->where('retur_status', null)->count();

            $hasil = $countitem != 0 ? ($countfull/$countitem) * 100 : 0;

            return $hasil;
    }

    public function getStatusTujuanAttribute()
    {
        if ($this->retur_tujuan == 'chiller') {
            return 'Chiller';
        } elseif ($this->retur_tujuan == 'gudang') {
            return 'Gudang';
        } elseif ($this->retur_tujuan == 'frozen') {
            return 'Frozen';
        } elseif ($this->retur_tujuan == 'musnahkan') {
            return 'Musnahkan';
        }
    }

    public static function recalculate_fulfill_qty($order_item_id)
    {
        $qty = Bahanbaku::where('order_item_id', $order_item_id)->get()->sum('bb_item');
        return $qty ?? 0;
    }

    public static function recalculate_fulfill_berat($order_item_id)
    {
        $berat = Bahanbaku::where('order_item_id', $order_item_id)->get()->sum('bb_berat');
        return $berat ?? 0;

    }

    public static function fulfillUlangCreditLimit($order_item_id) {
        DB::beginTransaction();

        $order_item     = OrderItem::find($order_item_id);

        if($order_item){

            $order           = Order::find($order_item->order_id);

            $multi_pemenuhan = Bahanbaku::where('order_item_id', $order_item_id)->where('deleted_at', NULL)->get();

            $total_berat     = 0;
            $total_item      = 0;

            if(count($multi_pemenuhan)==0){
                DB::rollBack() ;
                $data['status'] =   400 ;
                $data['msg']    =   'Belum pilih item' ;
                return $data ;
            }

            foreach($multi_pemenuhan as $bahan){
                if($bahan->proses_ambil=="frozen"){
                    
                    $gudang                 =   Product_gudang::find($bahan->chiller_out);
                    $nama_item              =   Item::find($gudang->product_id);
                    
                    if ($bahan->nama != $order_item->nama_detail) {
                        DB::rollBack() ;
                        $data['status'] =   400 ;
                        $data['msg']    =   'Terjadi kesalahan, Item Order berbeda dengan fulfill' ;
                        return $data ;
                    }

                    // CARI GUDANG JIKA SUDAH ADA KELUAR
                    $cekDataGudang                  =   Product_gudang::where('order_bb_id', $bahan->id)->where('type', 'siapkirim')->first();

                    if ($cekDataGudang) {

                        // KEMBALIKAN QTY DAN BERAT GUDANG
                        
                        // CONTOH GUDANG 100, BB 50, SETELAH UPDATE BB JADI 70
                        // 100 - 50 = 50 (AWALNYA)
                        // SISA 50 

                        // KETIKA INGIN UPDATE, KEMBALIKAN 50 NYA JADI 100 LAGI DENGAN DITAMBAH DARI BB
                        // VARIABLE CEKDATAGUDANG YANG MEMILIKI QTY/BERAT 50

                        $gudang->qty                    =   $gudang->qty + $cekDataGudang->qty;
                        $gudang->berat                  =   $gudang->berat + $cekDataGudang->berat;

                        if (!$gudang->save()) {
                            DB::rollBack();
                            $data['status'] =   400;
                            $data['msg']    =   'Terjadi kesalahan pengembalian stock gudang';
                            return $data;
                        }

                        // END


                        // UPDATE DATA GUDANG DENGAN STOCK BARU

                        $cekDataGudang->qty_awal        =   $bahan->bb_item;
                        $cekDataGudang->berat_awal      =   $bahan->bb_berat;
                        $cekDataGudang->qty             =   $bahan->bb_item;
                        $cekDataGudang->berat           =   $bahan->bb_berat;

                        if (!$cekDataGudang->save()) {
                            DB::rollBack();
                            $data['status'] =   400;
                            $data['msg']    =   'Terjadi kesalahan proses order';
                            return $data;
                        }

                        // END SAVE GUDANG YANG ADA
    
                        $bahan->chiller_alokasi         =   $cekDataGudang->id;
                        $bahan->save();

                        // END SAVE ID CHILLER ALOKASI BAHAN BAKU


                        // CONTOH GUDANG 100, BB 50, SETELAH UPDATE BB JADI 70
                        // 100 - 50 = 50 (AWALNYA)
                        // 50 
    
                        $gudang->qty                    =   $gudang->qty - $bahan->bb_item;
                        $gudang->berat                  =   $gudang->berat - $bahan->bb_berat;
    
                        if (!$gudang->save()) {
                            DB::rollBack();
                            $data['status'] =   400;
                            $data['msg']    =   'Terjadi kesalahan proses order';
                            return $data;
                        }
    
                        $total_item     =   $total_item + $bahan->bb_item;
                        $total_berat    =   $total_berat + $bahan->bb_berat;
    
                        $bahan->status  =   2;
                        if (!$bahan->save()) {
                            DB::rollBack();
                            $data['status'] =   400;
                            $data['msg']    =   'Terjadi kesalahan proses order';
                            return $data;
                        }
    
    
                        $nama_gudang_cs = env('NET_SUBSIDIARY', 'CGL')." - Cold Storage";
                        $nama_gudang_susut = env('NET_SUBSIDIARY', 'CGL')." - Storage Susut";
    
                        $data_gudang = Gudang::where('code', $nama_gudang_cs)->first();
    
                        $net  =   [
                            "nama_tabel"    =>  "product_gudang" ,
                            "id_tabel"      =>  $gudang->id ,
                            "document_code" =>  $order->no_so ?? $order_item_id,
                            "label"         =>  "ti_". strtolower(str_replace(" ", "", $data_gudang->code)) ."_ekspedisi" ,
                            "id_location"   =>  Gudang::gudang_netid($data_gudang->code) ,
                            "location"      =>  $data_gudang->code ,
                            "from"          =>  Gudang::gudang_netid($data_gudang->code) ,
                            "to"            =>  Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Storage Expedisi") ,
                            "transfer"      =>  [
                                [
                                    "internal_id_item"  =>  (string)$gudang->productitems->netsuite_internal_id ,
                                    "item"              =>  (string)$gudang->productitems->sku ,
                                    "qty_to_transfer"   =>  (string)$bahan->bb_berat
                                ]
                            ]
                        ] ;
    
                        if($gudang->productitems->sku!="1310000002" && $gudang->productitems->sku!="300800A002"){
                            $ti = Netsuite::transfer_inventory_doc($net['nama_tabel'], $net['id_tabel'], $net['label'], $net['id_location'], $net['location'], $net['from'], $net['to'], $net['transfer'], NULL, $bahan->bahanbborder->tanggal_kirim ,$net['document_code']) ;
                        }

                    } else {
                        
                        $gdg_baru                       =   new Product_gudang;
                        $gdg_baru->table_name           =   $gudang->table_name;
                        $gdg_baru->table_id             =   $gudang->table_id;
                        $gdg_baru->product_id           =   $gudang->product_id;
                        $gdg_baru->nama                 =   $nama_item->nama ?? "";
                        $gdg_baru->customer_id          =   $gudang->customer_id;
                        $gdg_baru->qty_awal             =   $bahan->bb_item;
                        $gdg_baru->berat_awal           =   $bahan->bb_berat;
                        $gdg_baru->qty                  =   $bahan->bb_item;
                        $gdg_baru->berat                =   $bahan->bb_berat;
                        $gdg_baru->karung_qty           =   $bahan->keranjang;
                        $gdg_baru->packaging            =   $gudang->packaging;
                        $gdg_baru->palete               =   $gudang->palete;
    
                        // BARU
                        $gdg_baru->sub_item             =   $gudang->sub_item;
                        $gdg_baru->plastik_group        =   $gudang->plastik_group;
                        $gdg_baru->subpack              =   $gudang->subpack;
                        $gdg_baru->parting              =   $gudang->parting ?? 0;
                        $gdg_baru->order_bb_id          =   $bahan->id;
    
                        // END BARU
                        $gdg_baru->expired              =   $gudang->expired;
                        $gdg_baru->production_date      =   $bahan->bahanbborder->tanggal_kirim;
                        $gdg_baru->order_id             =   $order->id;
                        $gdg_baru->no_so                =   $order->no_so;
                        $gdg_baru->order_item_id        =   $order_item_id;
                        $gdg_baru->type                 =   "siapkirim";
                        $gdg_baru->stock_type           =   $gudang->stock_type;
                        $gdg_baru->jenis_trans          =   'keluar';
                        $gdg_baru->gudang_id            =   $gudang->gudang_id;
                        $gdg_baru->gudang_id_keluar     =   $gudang->id;
                        $gdg_baru->status               =   4;
    
                        if (!$gdg_baru->save()) {
                            DB::rollBack();
                            $data['status'] =   400;
                            $data['msg']    =   'Terjadi kesalahan proses order';
                            return $data;
                        }
    
                        $bahan->chiller_alokasi         = $gdg_baru->id;
                        $bahan->save();
    
                        $gudang->qty        =   $gudang->qty - $bahan->bb_item;
                        $gudang->berat      =   $gudang->berat - $bahan->bb_berat;
    
                        if (!$gudang->save()) {
                            DB::rollBack();
                            $data['status'] =   400;
                            $data['msg']    =   'Terjadi kesalahan proses order';
                            return $data;
                        }
    
                        if (!$gdg_baru->save()) {
                            DB::rollBack() ;
                            $data['status'] =   400 ;
                            $data['msg']    =   'Terjadi kesalahan proses order' ;
                            return $data ;
                        }
    
                        $total_item     =   $total_item+$bahan->bb_item;
                        $total_berat    =   $total_berat+$bahan->bb_berat;
    
                        $bahan->status  =   2;
                        if (!$bahan->save()) {
                            DB::rollBack();
                            $data['status'] =   400;
                            $data['msg']    =   'Terjadi kesalahan proses order';
                            return $data;
                        }
    
    
                        $nama_gudang_cs = env('NET_SUBSIDIARY', 'CGL')." - Cold Storage";
                        $nama_gudang_susut = env('NET_SUBSIDIARY', 'CGL')." - Storage Susut";
    
                        $data_gudang = Gudang::where('code', $nama_gudang_cs)->first();
    
                        $net  =   [
                            "nama_tabel"    =>  "product_gudang" ,
                            "id_tabel"      =>  $gudang->id ,
                            "document_code" =>  $order->no_so ?? $order_item_id,
                            "label"         =>  "ti_". strtolower(str_replace(" ", "", $data_gudang->code)) ."_ekspedisi" ,
                            "id_location"   =>  Gudang::gudang_netid($data_gudang->code) ,
                            "location"      =>  $data_gudang->code ,
                            "from"          =>  Gudang::gudang_netid($data_gudang->code) ,
                            "to"            =>  Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Storage Expedisi") ,
                            "transfer"      =>  [
                                [
                                    "internal_id_item"  =>  (string)$gudang->productitems->netsuite_internal_id ,
                                    "item"              =>  (string)$gudang->productitems->sku ,
                                    "qty_to_transfer"   =>  (string)$bahan->bb_berat
                                ]
                            ]
                        ] ;
    
                        if($gudang->productitems->sku!="1310000002" && $gudang->productitems->sku!="300800A002"){
                            $ti = Netsuite::transfer_inventory_doc($net['nama_tabel'], $net['id_tabel'], $net['label'], $net['id_location'], $net['location'], $net['from'], $net['to'], $net['transfer'], NULL, $bahan->bahanbborder->tanggal_kirim ,$net['document_code']) ;
                        }
                    }


                } else {

                    $cekchiller                 =   Chiller::find($bahan->chiller_out);

                    if ($bahan->nama != $order_item->nama_detail) {
                        DB::rollBack() ;
                        $data['status'] =   400 ;
                        $data['msg']    =   'Terjadi kesalahan, Item Order berbeda dengan fulfill' ;
                        return $data ;
                    }

                    $chiler             =   Chiller::where('table_name', 'order_bahanbaku')->where('table_id', $bahan->id)->where('asal_tujuan', 'siapkirim')->first();

                    if ($chiler) {
                        
                        // PENGEMBALIAN STOCK

                        $cekchiller->stock_berat    =   $cekchiller->stock_berat + $chiler->berat_item ;
                        $cekchiller->stock_item     =   $cekchiller->stock_item + $chiler->qty_item ;

                        if (!$cekchiller->save()) {
                            DB::rollBack() ;
                            $data['status'] =   400 ;
                            $data['msg']    =   'Terjadi kesalahan proses order' ;
                            return $data ;
                        }

                        $chiler->qty_item           =   $bahan->bb_item;
                        $chiler->berat_item         =   $bahan->bb_berat;

                        if (!$chiler->save()) {
                            DB::rollBack() ;
                            $data['status'] =   400 ;
                            $data['msg']    =   'Terjadi kesalahan proses order' ;
                            return $data ;
                        }



                    } else {
                        $chiler                     =   new Chiller;
                        $chiler->table_name         =   'order_bahanbaku';
                        $chiler->table_id           =   $bahan->id;
                        $chiler->asal_tujuan        =   'siapkirim';
                        $chiler->item_id            =   $bahan->orderitem->item_id;
                        $chiler->item_name          =   $bahan->orderitem->nama_detail;
                        $chiler->qty_item           =   $bahan->bb_item;
                        $chiler->berat_item         =   $bahan->bb_berat;
                        $chiler->jenis              =   'keluar';
                        $chiler->type               =   'alokasi-order';
                        $chiler->kategori           =   $cekchiller->kategori;
                        $chiler->tanggal_potong     =   Carbon::now();
                        $chiler->tanggal_produksi   =   Carbon::now();
                        $chiler->status             =   4;
                        if (!$chiler->save()) {
                            DB::rollBack() ;
                            $data['status'] =   400 ;
                            $data['msg']    =   'Terjadi kesalahan proses order' ;
                            return $data ;
                        }
    
                        $bahan->chiller_alokasi      = $chiler->id;
                        $bahan->save();

                    }


                    if($cekchiller->type=="hasil-produksi"){
                        $net  =   [
                            "nama_tabel"    =>  "chiller" ,
                            "id_tabel"      =>  $chiler->id ,
                            "document_code" =>  $order->no_so ?? $order_item_id,
                            "label"         =>  "ti_finishedgood_ekspedisi" ,
                            "id_location"   =>  Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Chiller Finished Good") ,
                            "location"      =>  env('NET_SUBSIDIARY', 'CGL')." - Chiller Finished Good" ,
                            "from"          =>  Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Chiller Finished Good") ,
                            "to"            =>  Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Storage Expedisi") ,
                            "transfer"      =>  [
                                [
                                    "internal_id_item"  =>  (string)$chiler->chillitem->netsuite_internal_id ,
                                    "item"              =>  (string)$chiler->chillitem->sku ,
                                    "qty_to_transfer"   =>  (string)$bahan->bb_berat
                                ]
                            ]
                        ] ;

                        if($chiler->chillitem->sku!="1310000002" && $chiler->chillitem->sku!="300800A002"){
                            $ti = Netsuite::transfer_inventory_doc($net['nama_tabel'], $net['id_tabel'], $net['label'], $net['id_location'], $net['location'], $net['from'], $net['to'], $net['transfer'], NULL, $bahan->bahanbborder->tanggal_kirim, $net['document_code']) ;
                        }
                    } else {

                        $item       =   Item::find($chiler->chillitem->id);
                        $cekGrading =   Grading::where('item_id', $item->id)->orderBy('id', 'DESC')->first(); 

                        
                        if ($cekGrading) {
                            if ($item->name != 'TELUR MUDA PARENT') {
    
                                $id_location    =   Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Chiller Bahan Baku") ;
                                $location       =   env('NET_SUBSIDIARY', 'CGL')." - Chiller Bahan Baku" ;
                                $from           =   $id_location;
        
                                $label          =   'wo-2';
        
                                $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL')." - AYAM KARKAS BROILER")
                                                    ->first();
        
                                $nama_assembly  =   $bom->bom_name ;
                                $id_assembly    =   $bom->netsuite_internal_id ;
        
                                $bom_id         =   $bom->id;
                                $item_assembly  =   env('NET_SUBSIDIARY', 'CGL')." - AYAM KARKAS BROILER";
        
                                
                                if ($cekGrading->jenis_karkas == 'normal') {
                                    $component      =   [[
                                        "type"              =>  "Component",
                                        "internal_id_item"  =>  (string)Item::item_sku("1100000001")->netsuite_internal_id,
                                        "item"              =>  "1100000001",
                                        "description"       =>  "AYAM KARKAS BROILER (RM)",
                                        "qty"               =>  $bahan->bb_berat
                                    ]];
        
                            } else if ($cekGrading->jenis_karkas == 'utuh') {
                                $component      =   [[
                                    "type"              =>  "Component",
                                    "internal_id_item"  =>  (string)Item::item_sku("1100000002")->netsuite_internal_id ,
                                    "item"              =>  "1100000002",
                                    "description"       =>  "AYAM UTUH (RM)",
                                    "qty"               =>  $bahan->bb_berat
                                ]];

                                } else if ($cekGrading->grade_item == 'memar') {
                                    $component      =   [[
                                        "type"              =>  "Component",
                                        "internal_id_item"  =>  (string)Item::item_sku("1100000003")->netsuite_internal_id ,
                                        "item"              =>  "1100000003",
                                        "description"       =>  "AYAM MEMAR (RM)",
                                        "qty"               =>  $bahan->bb_berat
                                    ]];
        
                                } else if ($cekGrading->grade_item == 'pejantan') {
                                    $component      =   [[
                                        "type"              =>  "Component",
                                        "internal_id_item"  =>  (string)Item::item_sku("1100000005")->netsuite_internal_id ,
                                        "item"              =>  "1100000005",
                                        "description"       =>  "AYAM PEJANTAN (RM)",
                                        "qty"               =>  $bahan->bb_berat
                                    ]];
        
                                } else if ($cekGrading->grade_item == 'kampung') {
                                    $component      =   [[
                                        "type"              =>  "Component",
                                        "internal_id_item"  =>  (string)Item::item_sku("1100000004")->netsuite_internal_id,
                                        "item"              =>  "1100000004",
                                        "description"       =>  "AYAM KAMPUNG (RM)",
                                        "qty"               =>  $bahan->bb_berat
                                    ]];

        
                                } else if ($cekGrading->grade_item == 'parent') {
                                    $component      =   [[
                                        "type"              =>  "Component",
                                        "internal_id_item"  =>  (string)Item::item_sku("1100000009")->netsuite_internal_id ,
                                        "item"              =>  "1100000009",
                                        "description"       =>  "AYAM PARENT (RM)",
                                        "qty"               =>  $bahan->bb_berat
                                    ]];
        
                                }
        
                                $proses =   [];
        
                                foreach ($bom->bomproses as $row) {
                                    $proses[]   =   [
                                        "type"              =>  "Component",
                                        "internal_id_item"  =>  (string)Item::item_sku($row->sku)->netsuite_internal_id,
                                        "item"              =>  $row->sku,
                                        "description"       =>  (string)Item::item_sku($row->sku)->nama,
                                        "qty"               =>  (string)Item::where('nama', 'AY - S')->first()->sku == $row->sku ? $row->qty_per_assembly : ($row->qty_per_assembly * $bahan->bb_berat),
                                    ];
                                }
        
                                $finished_good  =   [[
                                    "type"              =>  "Finished Goods",
                                    "internal_id_item"  =>  (string)$item->netsuite_internal_id,
                                    "item"              =>  (string)$item->sku,
                                    "description"       =>  (string)Item::item_sku($item->sku)->nama,
                                    "qty"               =>  (string)$bahan->bb_berat
                                ]];
        
                                $produksi       =   array_merge($component, $proses, $finished_good);
                                $nama_tabel     =   'chiller';
                                $id_tabel       =   $chiler->id;
        
                                $wo = Netsuite::work_order($nama_tabel, $id_tabel, $label, $id_assembly, $item_assembly, $id_location, $location, $produksi, null);
        
                                $label          =   'wo-2-build';
                                $total          =   $bahan->bb_berat;
                                $wob = Netsuite::wo_build($nama_tabel, $id_tabel, $label, $id_assembly, $item_assembly, $id_location, $location, $total, $produksi, $wo->id);
        
                                $net  =   [
                                    "nama_tabel"    =>  "chiller" ,
                                    "id_tabel"      =>  $chiler->id ,
                                    "label"         =>  "ti_bb_ekspedisi" ,
                                    "document_code" =>  $order->no_so ?? $order_item_id,
                                    "id_location"   =>  Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Chiller Bahan Baku") ,
                                    "location"      =>  env('NET_SUBSIDIARY', 'CGL')." - Chiller Bahan Baku" ,
                                    "from"          =>  Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Chiller Bahan Baku") ,
                                    "to"            =>  Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Storage Expedisi") ,
                                    "transfer"      =>  [
                                        [
                                            "internal_id_item"  =>  (string)$chiler->chillitem->netsuite_internal_id ,
                                            "item"              =>  (string)$chiler->chillitem->sku ,
                                            "qty_to_transfer"   =>  (string)$bahan->bb_berat
                                        ]
                                    ]
                                ] ;
        
                                if ($chiler->chillitem->sku!="1310000002" && $chiler->chillitem->sku!="300800A002") {
                                    $ti = Netsuite::transfer_inventory_doc($net['nama_tabel'], $net['id_tabel'], $net['label'], $net['id_location'], $net['location'], $net['from'], $net['to'], $net['transfer'], $wob->id, $bahan->bahanbborder->tanggal_kirim, $net['document_code']) ;
                                }

                            }

                        } else {

                            $net  =   [
                                "nama_tabel"    =>  "chiller" ,
                                "id_tabel"      =>  $chiler->id ,
                                "label"         =>  "ti_bb_ekspedisi" ,
                                "document_code" =>  $order->no_so ?? $order_item_id,
                                "id_location"   =>  Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Chiller Bahan Baku") ,
                                "location"      =>  env('NET_SUBSIDIARY', 'CGL')." - Chiller Bahan Baku" ,
                                "from"          =>  Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Chiller Bahan Baku") ,
                                "to"            =>  Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Storage Expedisi") ,
                                "transfer"      =>  [
                                    [
                                        "internal_id_item"  =>  (string)$chiler->chillitem->netsuite_internal_id ,
                                        "item"              =>  (string)$chiler->chillitem->sku ,
                                        "qty_to_transfer"   =>  (string)$bahan->bb_berat
                                    ]
                                ]
                            ] ;

                            if($chiler->chillitem->sku!="1310000002" && $chiler->chillitem->sku!="300800A002"){
                                $ti = Netsuite::transfer_inventory_doc($net['nama_tabel'], $net['id_tabel'], $net['label'], $net['id_location'], $net['location'], $net['from'], $net['to'], $net['transfer'], NULL, $bahan->bahanbborder->tanggal_kirim, $net['document_code']) ;
                            }
                        }


                    }

                    // PENGURANGAN STOCK

                    $cekchiller->stock_berat    =   $cekchiller->stock_berat - $chiler->berat_item ;
                    $cekchiller->stock_item     =   $cekchiller->stock_item - $chiler->qty_item ;


                    if (!$cekchiller->save()) {
                        DB::rollBack() ;
                        $data['status']         =  400 ;
                        $data['msg']            =  'Terjadi kesalahan proses order' ;
                        return $data ;
                    }

                    $total_item                 =  $total_item + $bahan->bb_item;
                    $total_berat                =  $total_berat + $bahan->bb_berat;

                }

                $bahan->status                  =  2;
                $bahan->netsuite_id             =  $ti->id ?? 0;

                if (!$bahan->save()) {
                    DB::rollBack();
                    $data['status']             =  400;
                    $data['msg']                =  'Terjadi kesalahan proses order';
                    return $data;
                }

            }

            $order_item->fulfillment_berat      =  OrderItem::recalculate_fulfill_berat($order_item->id);
            $order_item->fulfillment_qty        =  OrderItem::recalculate_fulfill_qty($order_item->id);
            $order_item->status                 =  3;

            if (!$order_item->save()) {
                DB::rollBack() ;
                $data['status'] =   400 ;
                $data['msg']    =   'Terjadi kesalahan proses order' ;
                return $data ;
            }

            DB::commit();

            $data['status']     =   200 ;
            $data['msg']        =   'Data telah diproses' ;
            $data['data']       =   array(
                'order_id'      => $order_item->order_id,
                'order_item_id' => $order_item->id
            ) ;

            return $data ;
        }
    }

    public static function fulfillItem($order_item_id){

        DB::beginTransaction();

        $order_item     = OrderItem::find($order_item_id);

        if($order_item){

            $order = Order::find($order_item->order_id);

            $multi_pemenuhan = Bahanbaku::where('order_item_id', $order_item_id)->whereNull('netsuite_id')->where('deleted_at', NULL)->get();

            $total_berat     = 0;
            $total_item      = 0;

            if(count($multi_pemenuhan)==0){
                DB::rollBack() ;
                $data['status'] =   400 ;
                $data['msg']    =   'Belum pilih item' ;
                return $data ;
            }

            foreach($multi_pemenuhan as $bahan){
                if($bahan->proses_ambil=="frozen"){
                    
                    $gudang                 =   Product_gudang::find($bahan->chiller_out);
                    $nama_item              =   Item::find($gudang->product_id);
                    
                    if ($bahan->nama != $order_item->nama_detail) {
                        DB::rollBack() ;
                        $data['status'] =   400 ;
                        $data['msg']    =   'Terjadi kesalahan, Item Order berbeda dengan fulfill' ;
                        return $data ;
                    }

                    $gdg_baru                       =   new Product_gudang;
                    $gdg_baru->table_name           =   $gudang->table_name;
                    $gdg_baru->table_id             =   $gudang->table_id;
                    $gdg_baru->product_id           =   $gudang->product_id;
                    $gdg_baru->nama                 =   $nama_item->nama ?? "";
                    $gdg_baru->customer_id          =   $gudang->customer_id;
                    $gdg_baru->qty_awal             =   $bahan->bb_item;
                    $gdg_baru->berat_awal           =   $bahan->bb_berat;
                    $gdg_baru->qty                  =   $bahan->bb_item;
                    $gdg_baru->berat                =   $bahan->bb_berat;
                    $gdg_baru->karung_qty           =   $bahan->keranjang;
                    $gdg_baru->packaging            =   $gudang->packaging;
                    $gdg_baru->palete               =   $gudang->palete;

                    // BARU
                    $gdg_baru->sub_item             =   $gudang->sub_item;
                    $gdg_baru->plastik_group        =   $gudang->plastik_group;
                    $gdg_baru->subpack              =   $gudang->subpack;
                    $gdg_baru->parting              =   $gudang->parting ?? 0;
                    $gdg_baru->order_bb_id          =   $bahan->id;

                    // END BARU
                    $gdg_baru->expired              =   $gudang->expired;
                    $gdg_baru->production_date      =   $bahan->bahanbborder->tanggal_kirim;
                    $gdg_baru->order_id             =   $order->id;
                    $gdg_baru->no_so                =   $order->no_so;
                    $gdg_baru->order_item_id        =   $order_item_id;
                    $gdg_baru->type                 =   "siapkirim";
                    $gdg_baru->stock_type           =   $gudang->stock_type;
                    $gdg_baru->jenis_trans          =   'keluar';
                    $gdg_baru->gudang_id            =   $gudang->gudang_id;
                    $gdg_baru->gudang_id_keluar     =   $gudang->id;
                    $gdg_baru->status               =   4;

                    if (!$gdg_baru->save()) {
                        DB::rollBack();
                        $data['status'] =   400;
                        $data['msg']    =   'Terjadi kesalahan proses order';
                        return $data;
                    }

                    $bahan->chiller_alokasi         = $gdg_baru->id;
                    $bahan->save();

                    $gudang->qty        =   $gudang->qty - $bahan->bb_item;
                    $gudang->berat      =   $gudang->berat - $bahan->bb_berat;

                    if (!$gudang->save()) {
                        DB::rollBack();
                        $data['status'] =   400;
                        $data['msg']    =   'Terjadi kesalahan proses order';
                        return $data;
                    }

                    if (!$gdg_baru->save()) {
                        DB::rollBack() ;
                        $data['status'] =   400 ;
                        $data['msg']    =   'Terjadi kesalahan proses order' ;
                        return $data ;
                    }

                    $total_item     =   $total_item+$bahan->bb_item;
                    $total_berat    =   $total_berat+$bahan->bb_berat;

                    $bahan->status  =   2;
                    if (!$bahan->save()) {
                        DB::rollBack();
                        $data['status'] =   400;
                        $data['msg']    =   'Terjadi kesalahan proses order';
                        return $data;
                    }


                    $nama_gudang_cs = env('NET_SUBSIDIARY', 'CGL')." - Cold Storage";
                    $nama_gudang_susut = env('NET_SUBSIDIARY', 'CGL')." - Storage Susut";

                    $data_gudang = Gudang::where('code', $nama_gudang_cs)->first();

                    $net  =   [
                        "nama_tabel"    =>  "product_gudang" ,
                        "id_tabel"      =>  $gudang->id ,
                        "document_code" =>  $order->no_so ?? $order_item_id,
                        "label"         =>  "ti_". strtolower(str_replace(" ", "", $data_gudang->code)) ."_ekspedisi" ,
                        "id_location"   =>  Gudang::gudang_netid($data_gudang->code) ,
                        "location"      =>  $data_gudang->code ,
                        "from"          =>  Gudang::gudang_netid($data_gudang->code) ,
                        "to"            =>  Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Storage Expedisi") ,
                        "transfer"      =>  [
                            [
                                "internal_id_item"  =>  (string)$gudang->productitems->netsuite_internal_id ,
                                "item"              =>  (string)$gudang->productitems->sku ,
                                "qty_to_transfer"   =>  (string)$bahan->bb_berat
                            ]
                        ]
                    ] ;

                    if($gudang->productitems->sku!="1310000002" && $gudang->productitems->sku!="300800A002"){
                        $ti = Netsuite::transfer_inventory_doc($net['nama_tabel'], $net['id_tabel'], $net['label'], $net['id_location'], $net['location'], $net['from'], $net['to'], $net['transfer'], NULL, $bahan->bahanbborder->tanggal_kirim ,$net['document_code']) ;
                    }

                } else {

                    $cekchiller                 =   Chiller::find($bahan->chiller_out);

                    if ($bahan->nama != $order_item->nama_detail) {
                        DB::rollBack() ;
                        $data['status'] =   400 ;
                        $data['msg']    =   'Terjadi kesalahan, Item Order berbeda dengan fulfill' ;
                        return $data ;
                    }

                    $chiler                     =   new Chiller;
                    $chiler->table_name         =   'order_bahanbaku';
                    $chiler->table_id           =   $bahan->id;
                    $chiler->asal_tujuan        =   'siapkirim';
                    $chiler->item_id            =   $bahan->orderitem->item_id;
                    $chiler->item_name          =   $bahan->orderitem->nama_detail;
                    $chiler->qty_item           =   $bahan->bb_item;
                    $chiler->berat_item         =   $bahan->bb_berat;
                    $chiler->jenis              =   'keluar';
                    $chiler->type               =   'alokasi-order';
                    $chiler->kategori           =   $cekchiller->kategori;
                    $chiler->tanggal_potong     =   Carbon::now();
                    $chiler->tanggal_produksi   =   Carbon::now();
                    $chiler->status             =   4;
                    if (!$chiler->save()) {
                        DB::rollBack() ;
                        $data['status'] =   400 ;
                        $data['msg']    =   'Terjadi kesalahan proses order' ;
                        return $data ;
                    }

                    $bahan->chiller_alokasi      = $chiler->id;
                    $bahan->save();

                    if($cekchiller->type=="hasil-produksi"){
                        $net  =   [
                            "nama_tabel"    =>  "chiller" ,
                            "id_tabel"      =>  $chiler->id ,
                            "document_code" =>  $order->no_so ?? $order_item_id,
                            "label"         =>  "ti_finishedgood_ekspedisi" ,
                            "id_location"   =>  Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Chiller Finished Good") ,
                            "location"      =>  env('NET_SUBSIDIARY', 'CGL')." - Chiller Finished Good" ,
                            "from"          =>  Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Chiller Finished Good") ,
                            "to"            =>  Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Storage Expedisi") ,
                            "transfer"      =>  [
                                [
                                    "internal_id_item"  =>  (string)$chiler->chillitem->netsuite_internal_id ,
                                    "item"              =>  (string)$chiler->chillitem->sku ,
                                    "qty_to_transfer"   =>  (string)$bahan->bb_berat
                                ]
                            ]
                        ] ;

                        if($chiler->chillitem->sku!="1310000002" && $chiler->chillitem->sku!="300800A002"){
                            $ti = Netsuite::transfer_inventory_doc($net['nama_tabel'], $net['id_tabel'], $net['label'], $net['id_location'], $net['location'], $net['from'], $net['to'], $net['transfer'], NULL, $bahan->bahanbborder->tanggal_kirim, $net['document_code']) ;
                        }
                    } else {

                        $item       =   Item::find($chiler->chillitem->id);
                        $cekGrading =   Grading::where('item_id', $item->id)->orderBy('id', 'DESC')->first(); 

                        if ($cekGrading) {
                            $id_location    =   Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Chiller Bahan Baku") ;
                            $location       =   env('NET_SUBSIDIARY', 'CGL')." - Chiller Bahan Baku" ;
                            $from           =   $id_location;
    
                            $label          =   'wo-2';
    
                            $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL')." - AYAM KARKAS BROILER")
                                                ->first();
    
                            $nama_assembly  =   $bom->bom_name ;
                            $id_assembly    =   $bom->netsuite_internal_id ;
    
                            $bom_id         =   $bom->id;
                            $item_assembly  =   env('NET_SUBSIDIARY', 'CGL')." - AYAM KARKAS BROILER";
    
    
                            if ($cekGrading->jenis_karkas == 'normal') {
                                $component      =   [[
                                    "type"              =>  "Component",
                                    "internal_id_item"  =>  (string)Item::item_sku("1100000001")->netsuite_internal_id,
                                    "item"              =>  "1100000001",
                                    "description"       =>  "AYAM KARKAS BROILER (RM)",
                                    "qty"               =>  $bahan->bb_berat
                                ]];

                            } else if ($cekGrading->jenis_karkas == 'utuh') {
                                $component      =   [[
                                    "type"              =>  "Component",
                                    "internal_id_item"  =>  (string)Item::item_sku("1100000002")->netsuite_internal_id ,
                                    "item"              =>  "1100000002",
                                    "description"       =>  "AYAM UTUH (RM)",
                                    "qty"               =>  $bahan->bb_berat
                                ]];
    
                            } else if ($cekGrading->grade_item == 'memar') {
                                $component      =   [[
                                    "type"              =>  "Component",
                                    "internal_id_item"  =>  (string)Item::item_sku("1100000003")->netsuite_internal_id ,
                                    "item"              =>  "1100000003",
                                    "description"       =>  "AYAM MEMAR (RM)",
                                    "qty"               =>  $bahan->bb_berat
                                ]];
    
                            } else if ($cekGrading->grade_item == 'pejantan') {
                                $component      =   [[
                                    "type"              =>  "Component",
                                    "internal_id_item"  =>  (string)Item::item_sku("1100000005")->netsuite_internal_id ,
                                    "item"              =>  "1100000005",
                                    "description"       =>  "AYAM PEJANTAN (RM)",
                                    "qty"               =>  $bahan->bb_berat
                                ]];
    
                            } else if ($cekGrading->grade_item == 'kampung') {
                                $component      =   [[
                                    "type"              =>  "Component",
                                    "internal_id_item"  =>  (string)Item::item_sku("1100000004")->netsuite_internal_id,
                                    "item"              =>  "1100000004",
                                    "description"       =>  "AYAM KAMPUNG (RM)",
                                    "qty"               =>  $bahan->bb_berat
                                ]];

    
                            } else if ($cekGrading->grade_item == 'parent') {
                                $component      =   [[
                                    "type"              =>  "Component",
                                    "internal_id_item"  =>  (string)Item::item_sku("1100000009")->netsuite_internal_id ,
                                    "item"              =>  "1100000009",
                                    "description"       =>  "AYAM PARENT (RM)",
                                    "qty"               =>  $bahan->bb_berat
                                ]];
    
                            }
    
                            $proses =   [];
    
                            foreach ($bom->bomproses as $row) {
                                $proses[]   =   [
                                    "type"              =>  "Component",
                                    "internal_id_item"  =>  (string)Item::item_sku($row->sku)->netsuite_internal_id,
                                    "item"              =>  $row->sku,
                                    "description"       =>  (string)Item::item_sku($row->sku)->nama,
                                    "qty"               =>  (string)Item::where('nama', 'AY - S')->first()->sku == $row->sku ? $row->qty_per_assembly : ($row->qty_per_assembly * $bahan->bb_berat),
                                ];
                            }
    
                            $finished_good  =   [[
                                "type"              =>  "Finished Goods",
                                "internal_id_item"  =>  (string)$item->netsuite_internal_id,
                                "item"              =>  (string)$item->sku,
                                "description"       =>  (string)Item::item_sku($item->sku)->nama,
                                "qty"               =>  (string)$bahan->bb_berat
                            ]];
    
                            $produksi       =   array_merge($component, $proses, $finished_good);
                            $nama_tabel     =   'chiller';
                            $id_tabel       =   $chiler->id;
    
                            $wo = Netsuite::work_order($nama_tabel, $id_tabel, $label, $id_assembly, $item_assembly, $id_location, $location, $produksi, null);
    
                            $label          =   'wo-2-build';
                            $total          =   $bahan->bb_berat;
                            $wob = Netsuite::wo_build($nama_tabel, $id_tabel, $label, $id_assembly, $item_assembly, $id_location, $location, $total, $produksi, $wo->id);
    
                            $net  =   [
                                "nama_tabel"    =>  "chiller" ,
                                "id_tabel"      =>  $chiler->id ,
                                "label"         =>  "ti_bb_ekspedisi" ,
                                "document_code" =>  $order->no_so ?? $order_item_id,
                                "id_location"   =>  Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Chiller Bahan Baku") ,
                                "location"      =>  env('NET_SUBSIDIARY', 'CGL')." - Chiller Bahan Baku" ,
                                "from"          =>  Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Chiller Bahan Baku") ,
                                "to"            =>  Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Storage Expedisi") ,
                                "transfer"      =>  [
                                    [
                                        "internal_id_item"  =>  (string)$chiler->chillitem->netsuite_internal_id ,
                                        "item"              =>  (string)$chiler->chillitem->sku ,
                                        "qty_to_transfer"   =>  (string)$bahan->bb_berat
                                    ]
                                ]
                            ] ;
    
                            if ($chiler->chillitem->sku!="1310000002" && $chiler->chillitem->sku!="300800A002") {
                                $ti = Netsuite::transfer_inventory_doc($net['nama_tabel'], $net['id_tabel'], $net['label'], $net['id_location'], $net['location'], $net['from'], $net['to'], $net['transfer'], $wob->id, $bahan->bahanbborder->tanggal_kirim, $net['document_code']) ;
                            }

                        } else{

                            $net  =   [
                                "nama_tabel"    =>  "chiller" ,
                                "id_tabel"      =>  $chiler->id ,
                                "label"         =>  "ti_bb_ekspedisi" ,
                                "document_code" =>  $order->no_so ?? $order_item_id,
                                "id_location"   =>  Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Chiller Bahan Baku") ,
                                "location"      =>  env('NET_SUBSIDIARY', 'CGL')." - Chiller Bahan Baku" ,
                                "from"          =>  Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Chiller Bahan Baku") ,
                                "to"            =>  Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL')." - Storage Expedisi") ,
                                "transfer"      =>  [
                                    [
                                        "internal_id_item"  =>  (string)$chiler->chillitem->netsuite_internal_id ,
                                        "item"              =>  (string)$chiler->chillitem->sku ,
                                        "qty_to_transfer"   =>  (string)$bahan->bb_berat
                                    ]
                                ]
                            ] ;

                            if($chiler->chillitem->sku!="1310000002" && $chiler->chillitem->sku!="300800A002"){
                                $ti = Netsuite::transfer_inventory_doc($net['nama_tabel'], $net['id_tabel'], $net['label'], $net['id_location'], $net['location'], $net['from'], $net['to'], $net['transfer'], NULL, $bahan->bahanbborder->tanggal_kirim, $net['document_code']) ;
                            }
                        }


                    }

                    $cekchiller->stock_berat    =   $cekchiller->stock_berat - $chiler->berat_item ;
                    $cekchiller->stock_item     =   $cekchiller->stock_item - $chiler->qty_item ;


                    if (!$cekchiller->save()) {
                        DB::rollBack() ;
                        $data['status'] =   400 ;
                        $data['msg']    =   'Terjadi kesalahan proses order' ;
                        return $data ;
                    }

                    $total_item      = $total_item+$bahan->bb_item;
                    $total_berat     = $total_berat+$bahan->bb_berat;

                }

                $bahan->status   = 2;
                $bahan->netsuite_id         = $ti->id ?? 0;

                if (!$bahan->save()) {
                    DB::rollBack();
                    $data['status'] =   400;
                    $data['msg']    =   'Terjadi kesalahan proses order';
                    return $data;
                }

            }

            $order_item->fulfillment_berat  =   OrderItem::recalculate_fulfill_berat($order_item->id);
            $order_item->fulfillment_qty    =   OrderItem::recalculate_fulfill_qty($order_item->id);
            $order_item->status             =   3;

            if (!$order_item->save()) {
                DB::rollBack() ;
                $data['status'] =   400 ;
                $data['msg']    =   'Terjadi kesalahan proses order' ;
                return $data ;
            }

            DB::commit();
            $data['status']     =   200 ;
            $data['msg']        =   'Data telah diproses' ;
            $data['data']       =   array(
                'order_id'      => $order_item->order_id,
                'order_item_id' => $order_item->id
            ) ;
            return $data ;
        }

    }

    public function order_fulfill(Request $request){
        $order_id   =   $request->order_id;
        $order      =   Order::find($order_id);

        if($order){
            foreach($order->daftar_order_full as $row){
                // dd($row);
                if ($row->order_item_bb != null) {
                    if ($row->order_item_bb->status == 1) {

                        $data['status']     =   200 ;
                        $data['msg']        =   'Data Item Belum Tersimpan' ;
                        $data['data']       =   array(
                            'order_id'      => $order_id,
                            'order_item_id' => $row->id
                        ) ;
                        return $data ;

                    }
                }
                elseif($row->status==1){
                    $req = array(
                        'order_item_id' => $row->id
                    );
                    $request = new Request($req);
                    $this->fulfillItem($request);

                    $row->status = 2;
                    $row->save();
                }
            }

            $order->status = 10;
            $order->save();

            $net_fulfill = Netsuite::item_fulfill_sampingan('orders', $request->order_id, 'itemfulfill', null, null);

            foreach($order->daftar_order_full as $row){
                if($row->status==3){
                    $row->status = 2;
                    $row->save();
                }
            }

            return back()->with('status', 1)->with('message', 'Data telah diproses');
        }

    }

}
