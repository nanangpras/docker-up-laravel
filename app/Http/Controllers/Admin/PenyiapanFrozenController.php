<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Abf;
use App\Models\Bahanbaku;
use App\Models\Category;
use App\Models\Chiller;
use App\Models\Gudang;
use App\Models\Item;
use App\Models\Netsuite;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product_gudang;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenyiapanFrozenController extends Controller
{
    //

    public function index(){
        $tanggal = date('Y-m-d');
        return view('admin.pages.penyiapanfrozen.index', compact('tanggal'));
    }

    public function penyiapanfrozenOrder(Request $request)
    {

        $regu       = strtolower($request->regu);

        $tanggal        =   $request->tanggal ?? date('Y-m-d');
        $search         =   $request->search;
        $regu_select    =   ["frozen"];
        $kategori       =   Category::where('nama', 'LIKE', "%frozen%")->pluck('id');;


        $pending    =   Order::whereIn('id', OrderItem::select('order_id')
                        ->whereIn('item_id', Item::select('id')
                        ->whereIn('category_id', $kategori)))
                        ->where('sales_channel', "!=","By Product - Paket")
                        ->where('sales_channel', "!=","By Product - Retail")
                        ->where('tanggal_so', $tanggal)
                        ->orderBy('updated_at', 'desc');

        if($search!=""){
            $pending = $pending->where(function($query) use ($search) {
                    $query->where('nama', 'like', '%'.$search.'%');
                    $query->orWhere('no_so', 'like', '%'.$search.'%');
                    $query->orWhere('sales_channel', 'like', '%'.$search.'%');
            });
        }
        $pending = $pending->paginate(10);


        $produk     =   Chiller::whereIn('asal_tujuan', ['free_stock', 'evisampingan'])
                        ->where('jenis', 'masuk')
                        ->whereIn('type', ['hasil-produksi', 'bahan-baku'])
                        ->where(function($query) use ($kategori, $regu_select) {
                            $query->whereIn('kategori', $kategori);
                            $query->orWhereIn('regu', $regu_select);
                            })
                        ->get();

        return view('admin.pages.penyiapanfrozen.order', compact('pending', 'produk', 'regu', 'tanggal', 'kategori'));
    }

    public function penyiapanfrozenStorage(Request $request){
        $tanggal        = $request->tanggal ?? date('Y-m-d');
        $tanggal_akhir  = $request->tanggal_akhir ?? $tanggal;
        $item_id        = $request->item_id ?? "";
        $pencarian      = $request->pencarian ?? "";


        $sql            = Product_gudang::where('jenis_trans', 'masuk')
                                        ->whereIn('status', [2])
                                        ->whereNotIn('type',['inventory_adjustment'])
                                        ->where(function($query) use ($pencarian){
                                            if($pencarian !=""){
                                                $query->orWhere('nama', 'like', '%'.$pencarian.'%');
                                                $query->orWhere('sub_item', 'like', '%'.$pencarian.'%');
                                                $query->orWhere('packaging', 'like', '%'.$pencarian.'%');
                                                $query->orWhere('label', 'like', '%'.$pencarian.'%');
                                            }
                                        })
                                        ->where(function($query2) use ($item_id){
                                            if($item_id !=""){
                                                $query2->where('product_id', $item_id);
                                            }
                                        })
                                        ->whereBetween('production_date', [$tanggal, $tanggal_akhir])
                                        ->orderBy('production_date', 'desc');
        $master         = clone $sql;
        $arrayData      = $master->get();
        
        $arrayId        = array();
        foreach($arrayData as $item){
            $arrayId[]  = $item->id;
        }
        $stringData     = implode(",",$arrayId);

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

        return view('admin.pages.penyiapanfrozen.storage-penyiapan', compact(['stock']));
    }


    public function simpanAlokasi(Request $request){


        DB::beginTransaction();

        if ($request->berat == null) {
            DB::rollBack() ;
            $data['status'] =   400 ;
            $data['msg']    =   'Isi Bahan Baku' ;
            return $data ;
        }
        for ($x = 0; $x < COUNT($request->x_code); $x++) {
            if ($request->berat[$x]) {
                $proses_ambil = $request->lokasi_asal;

                $storage                        =   Product_gudang::find($request->x_code[$x]);

                // if ($request->berat[$x] > $storage->berat) {

                //     $data['status'] =   400 ;
                //     $data['msg']    =   'Pengambilan Lebih Besar dari Stock' ;
                //     return $data ;
                // }

                $orderBahanBaku                     =   new Bahanbaku;
                $orderBahanBaku->chiller_out        =   $storage->id;
                $orderBahanBaku->order_id           =   $request->order_id;
                $orderBahanBaku->nama               =   $storage->productitems->nama;
                $orderBahanBaku->order_item_id      =   $request->order_item_id;
                $orderBahanBaku->bb_item            =   $request->qty[$x];
                $orderBahanBaku->bb_berat           =   $request->berat[$x];
                $orderBahanBaku->proses_ambil       =   $proses_ambil;
                $orderBahanBaku->status             =   1;
                if (!$orderBahanBaku->save()) {
                    DB::rollBack() ;
                    $data['status'] =   400 ;
                    $data['msg']    =   'Terjadi kesalahan proses order' ;
                    return $data ;
                }

            }
        }

        DB::commit();
        $data['status'] =   200 ;
        $data['msg']    =   'Data telah diproses' ;
        return $data ;

    }

    public function pemenuhanAlokasi(Request $request){

        $order_item_id = $request->order_item_id;
        $pemenuhan = Bahanbaku::where('order_item_id', $order_item_id)->get();

        return view('admin.pages.penyiapanfrozen.pemenuhan', compact('pemenuhan'));
    }

    public function fulfillItem(Request $request){

        return OrderItem::fulfillItem($request->order_item_id);


    }


    public function deleteAlokasi(Request $request){
        $pemenuhan = Bahanbaku::find($request->id);
        $pemenuhan->delete();
        $data['status'] =   200 ;
        $data['msg']    =   'Data telah diproses' ;
        return $data ;
    }

    public function storeprosesorder(Request $request)
    {
        // return $data['status']  =   400 ;
        // if (User::setIjin(7)) {
        $qty    =  json_decode(json_encode($request->qty, FALSE));
        $berat  =  json_decode(json_encode($request->berat, FALSE));
        $item   =  json_decode(json_encode($request->item, FALSE));
        $order  =  json_decode(json_encode($request->order, FALSE));
        $xcode  =  json_decode(json_encode($request->xcode, FALSE));

        $nama_gudang_expedisi = env('NET_SUBSIDIARY', 'CGL')." - Storage Expedisi";
        $nama_gudang_bb = env('NET_SUBSIDIARY', 'CGL')." - Chiller Bahan Baku";
        $nama_gudang_fg = env('NET_SUBSIDIARY', 'CGL')." - Chiller Finished Good";

        DB::beginTransaction();

        $total  =   0 ;
        $net    =   [] ;
        for ($x = 0; $x < COUNT($order); $x++) {
            if ($berat[$x] > 0) {

                if ($qty[$x] < 1) {
                    DB::rollBack() ;
                    $data['status'] =   400 ;
                    $data['msg']    =   'Qty tidak boleh kosong' ;
                    return $data ;
                }

                $total  +=  1 ;

                $chiler                     =   new Bahanbaku();
                $chiler->chiller_out        =   $item[$x];
                $chiler->order_id           =   $xcode[$x];
                $chiler->order_item_id      =   $order[$x];
                $chiler->bb_item            =   $qty[$x];
                $chiler->bb_berat           =   $berat[$x];
                $chiler->status             =   1;
                if (!$chiler->save()) {
                    DB::rollBack() ;
                    $data['status'] =   400;
                    $data['msg']    =   'Terjadi kesalahan proses order' ;
                    return $data ;
                }

                $bahan                      =   Bahanbaku::where('chiller_out', $item[$x])
                                                ->where('order_id', $xcode[$x])
                                                ->where('order_item_id', $order[$x])
                                                ->first();

                $cekchiller                 =   Chiller::find($bahan->chiller_out);

                if ($request->regu == 'Frozen') {
                    $decode                 =   json_decode(Chiller::find($bahan->orderitem->item_id)->label) ;

                    $abf                    =   new Abf();
                    $abf->table_name        =   'order_bahanbaku';
                    $abf->table_id          =   $bahan->id;
                    $abf->asal_tujuan       =   'orderproduksi';
                    $abf->tanggal_masuk     =   date('Y-m-d');
                    $abf->item_id           =   $bahan->orderitem->item_id;
                    $abf->item_id_lama      =   $bahan->orderitem->item_id;
                    $abf->item_name         =   $bahan->orderitem->nama_detail;
                    $abf->packaging         =   $decode->plastik->jenis ?? NULL ;
                    $abf->qty_awal          =   $qty[$x];
                    $abf->berat_awal        =   $berat[$x];
                    $abf->qty_item          =   $qty[$x];
                    $abf->berat_item        =   $berat[$x];
                    $abf->jenis             =   'masuk';
                    $abf->type              =   'hasil-produksi';
                    $abf->status            =   1;
                    if (!$abf->save()) {
                        DB::rollBack() ;
                        $data['status'] =   400;
                        $data['msg']    =   'Terjadi kesalahan proses order' ;
                        return $data ;
                    }

                    $label  =   "ti_order_abf" ;
                    $to     =   "123" ;
                } else {

                    $chiler                     =   new Chiller();
                    $chiler->table_name         =   'order_bahanbaku';
                    $chiler->table_id           =   $bahan->id;
                    $chiler->asal_tujuan        =   'orderproduksi';
                    $chiler->item_id            =   $bahan->orderitem->item_id;
                    $chiler->item_name          =   $bahan->orderitem->nama_detail;
                    $chiler->qty_item           =   $qty[$x];
                    $chiler->berat_item         =   $berat[$x];
                    $chiler->jenis              =   'masuk';
                    $chiler->type               =   'hasil-produksi';
                    $chiler->kategori           =   $cekchiller->kategori;
                    $chiler->tanggal_potong     =   Carbon::now();
                    $chiler->tanggal_produksi   =   Carbon::now();
                    $chiler->status             =   4;
                    $chiler->save();
                    if (!$chiler->save()) {
                        DB::rollBack() ;
                        $data['status'] =   400;
                        $data['msg']    =   'Terjadi kesalahan proses order' ;
                        return $data ;
                    }

                    $cekchiller->stock_berat    =   $cekchiller->stock_berat - $chiler->berat_item ;
                    $cekchiller->berat_item     =   $cekchiller->berat_item - $chiler->berat_item ;

                    $cekchiller->stock_item     =   $cekchiller->stock_item - $chiler->qty_item ;
                    $cekchiller->qty_item       =   $cekchiller->qty_item - $chiler->qty_item ;
                    if (!$cekchiller->save()) {
                        DB::rollBack() ;
                        $data['status'] =   400;
                        $data['msg']    =   'Terjadi kesalahan proses order' ;
                        return $data ;
                    }

                    $label  =   "ti_fg_ekspedisi" ;
                    $to     =   Gudang::gudang_netid($nama_gudang_expedisi) ;
                }

                $so                         =   Order::find($xcode[$x]);
                $so->status                 =   5;
                if (!$so->save()) {
                    DB::rollBack() ;
                    $data['status'] =   400;
                    $data['msg']    =   'Terjadi kesalahan proses order' ;
                    return $data ;
                }

                $oritem                     =   OrderItem::find($order[$x]);
                $oritem->fulfillment_berat  =   $berat[$x];
                $oritem->fulfillment_qty    =   $qty[$x];
                $oritem->status             =   1;
                if (!$oritem->save()) {
                    DB::rollBack() ;
                    $data['status'] =   400;
                    $data['msg']    =   'Terjadi kesalahan proses order' ;
                    return $data ;
                }

                $net[]  =   [
                    'nama_tabel'    =>  "order_items" ,
                    "id_tabel"      =>  $oritem->id ,
                    "label"         =>  $label ,
                    "id_location"   =>  Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL').' - Chiller Finished Good') ,
                    "location"      =>  env('NET_SUBSIDIARY', 'CGL')." - Chiller Finished Good" ,
                    "from"          =>  Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL').' - Chiller Finished Good') ,
                    "to"            =>  $to ,
                    "transfer"      =>  [
                        [
                            "internal_id_item"  =>  (string)$oritem->item->netsuite_internal_id ,
                            "item"              =>  (string)$oritem->item->sku ,
                            "qty_to_transfer"   =>  (string)$berat[$x]
                        ]
                    ]
                ] ;

            }
        }

        if ($total == 0) {
            DB::rollBack() ;
            $data['status'] =   400 ;
            $data['msg']    =   'Order Kosong' ;
            return $data ;
        }

        DB::commit();

        for ($x=0; $x < COUNT($net); $x++) {
            Netsuite::transfer_inventory($net[$x]['nama_tabel'], $net[$x]['id_tabel'], $net[$x]['label'], $net[$x]['id_location'], $net[$x]['location'], $net[$x]['from'], $net[$x]['to'], $net[$x]['transfer'], NULL) ;
        }

    }

}
