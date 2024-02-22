<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Abf;
use App\Models\Bahanbaku;
use App\Models\Bom;
use App\Models\Chiller;
use App\Models\Customer;
use App\Models\Freestock;
use App\Models\Gudang;
use App\Models\Item;
use App\Models\MarketingSO;
use App\Models\Netsuite;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product_gudang;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenyiapanController extends Controller
{
    public function index(Request $request)
    {

        if (User::setIjin(27) || User::setIjin(31)) {
            $tanggal    =   $request->tanggal ?? date('Y-m-d');
            $search    =   $request->search ?? "";
            if ($request->key == 'unduh') {

                return view('admin.pages.penyiapan.export');
            } else {
                $customer   =   $request->customer ?? "";
                $search   =   $request->search ?? "";
                $key        = $request->key ?? "";
                return view('admin.pages.penyiapan.index', compact('tanggal', 'customer', 'search', 'key'));
            }
        } else {
            return redirect()->route("index");
        }
    }

    public function penyiapanOrder(Request $request)
    {

        $regu           =   strtolower($request->regu);
        $customer       =   $request->customer ?? "";
        $search         =   $request->search ?? "";
        $key            =   $request->key ?? "";
        $tanggal        =   $request->tanggal ?? date('Y-m-d');
        $kategori       =   [1, 2, 3, 5, 7, 8, 9, 13];
        $regu_select    =   ["boneless", "marinasi", "parting", "whole", "frozen"];

        $pending        =   Order::whereNotIn('sales_channel', ["By Product - Paket", "By Product - Retail"])
            ->where('tanggal_so', $tanggal)
            ->where(function ($query) use ($customer, $search, $key) {
                if ($customer) {
                    $query->where('customer_id', $customer);
                }
                if ($search) {
                    $query->orWhere('nama', 'like', '%' . $search . '%');
                    $query->orWhere('no_so', 'like', '%' . $search . '%');
                    $query->orWhere('keterangan', 'like', '%' . $search . '%');
                    $query->orWhere('sales_channel', 'like', '%' . $search . '%');
                }
                if ($key) {
                    if ($key == 'selesai') {
                        $query->where('status', 10);
                    } else
                                    if ($key == 'proses') {
                        $query->where('status', null);
                    } else
                                    if ($key == 'batal') {
                        $query->where('status', 0);
                    } else
                                    if ($key == 'gagal') {
                        $query->where('status', 10)->where('no_do', null);
                    }
                }
            })
            ->orderBy('no_so', 'ASC')
            ->paginate(10);

        $semua_order    =   Order::whereNotIn('sales_channel', ["By Product - Paket", "By Product - Retail"])
            ->whereDate('tanggal_so', $tanggal)
            ->orderBy('id', 'desc')
            ->count();

        $selesai_order  =   Order::whereNotIn('sales_channel', ["By Product - Paket", "By Product - Retail"])
            ->whereDate('tanggal_so', $tanggal)
            ->orderBy('id', 'desc')
            ->where('status', '10')
            ->count();

        $pending_order  =   Order::whereNotIn('sales_channel', ["By Product - Paket", "By Product - Retail"])
            ->whereDate('tanggal_so', $tanggal)
            ->orderBy('id', 'desc')
            ->whereNull('status')
            ->count();

        $status_order   =   [
            'semua_order'       =>  $semua_order,
            'selesai_order'     =>  $selesai_order,
            'pending_order'     =>  $pending_order,
        ];

        return view('admin.pages.penyiapan.order', compact('pending', 'regu', 'tanggal', 'kategori', 'customer', 'search', 'status_order', 'key'));
    }

    public function penyiapanChiller(Request $request)
    {
        $tanggal        =   $request->tanggal ?? date('Y-m-d');
        $tanggal_akhir  =   $request->tanggal_akhir ?? $tanggal;
        $item_id        =   $request->item_id ?? "";
        $pencarian      =   $request->pencarian ?? "";

        $sql            =   Chiller::whereIn('asal_tujuan', ['free_stock', 'retur', 'karkasbeli', 'evisbeli', 'hasilbeli', 'open_balance', 'thawing'])
                                ->where('jenis', 'masuk')
                                ->whereIn('type', ['hasil-produksi', 'bahan-baku'])
                                ->where('item_id', $item_id)
                                ->whereBetween('tanggal_produksi', [$tanggal, $tanggal_akhir])
                                ->where('stock_berat', '>', 0)
                                ->where('status_cutoff', NULL)
                                ->where(function ($query) use ($pencarian) {
                                    if ($pencarian) {
                                        $query->orWhere('item_name', 'like', '%' . $pencarian . '%');
                                        $query->orWhere('label', 'like', '%' . $pencarian . '%');
                                    }
                                })
                                ->orderBy('tanggal_produksi', 'desc');

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
                "customer_name"             => $data->konsumen->nama ?? "FREE STOCK",
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
                "nama"                      => $data->nama,
                'total_qty_alokasi'         => $total_qty_alokasi,
                'total_berat_alokasi'       => $total_berat_alokasi,
                'total_keranjang'           => $total_keranjang,
                'total_qty_abf'             => $total_qty_abf,
                'total_berat_abf'           => $total_berat_abf,
                'total_qty_freestock'       => $total_qty_freestock,
                'total_berat_freestock'     => $total_berat_freestock,
                'total_qty_musnahkan'       => $total_qty_musnahkan,
                'total_berat_musnahkan'     => $total_berat_musnahkan,
                'sisaQty'                   => $data->qty_item - $total_qty_alokasi - $total_qty_abf - $total_qty_freestock - $total_qty_musnahkan,
                'sisaBerat'                 => $data->berat_item - $total_berat_alokasi - $total_berat_abf - $total_berat_freestock - $total_berat_musnahkan
            ];
        }
        $produk                     = json_decode(json_encode($arraymodification));

        return view('admin.pages.penyiapan.chiller-penyiapan', compact('produk'));
    }


    public function simpanAlokasi(Request $request)
    {

        DB::beginTransaction();

        if ($request->berat == null) {
            DB::rollBack();
            $data['status'] =   400;
            $data['msg']    =   'Isi Bahan Baku';
            return $data;
        }
        for ($x = 0; $x < COUNT($request->x_code); $x++) {
            if ($request->berat[$x]) {

                $proses_ambil = $request->lokasi_asal;

                if ($proses_ambil == "chillerfg") {

                    $chiller                        =   Chiller::find($request->x_code[$x]);

                    if ($request->berat[$x] > $chiller->stock_berat) {
                        DB::rollBack();
                        $data['status'] =   400;
                        $data['msg']    =   'Pengambilan Lebih Besar dari Stock';
                        return $data;
                    }

                    $orderItem                          = OrderItem::find($request->order_item_id);
                    if ($orderItem) {
                        if ($chiller->item_name != $orderItem->nama_detail) {
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
                } elseif ($proses_ambil == "frozen") {

                    $storage                        =   Product_gudang::find($request->x_code[$x]);

                    if ($request->berat[$x] > $storage->berat) {
                        DB::rollBack();
                        $data['status'] =   400;
                        $data['msg']    =   'Pengambilan Lebih Besar dari Stock';
                        return $data;
                    }

                    $orderItem                          = OrderItem::find($request->order_item_id);
                    if ($orderItem) {
                        if ($storage->nama != $orderItem->nama_detail) {
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
                } elseif ($proses_ambil == "sampingan") {
                    $chiller                        =   Chiller::find($request->x_code[$x]);

                    if ($request->berat[$x] > $chiller->stock_berat) {
                        DB::rollBack();
                        $data['status'] =   400;
                        $data['msg']    =   'Pengambilan Lebih Besar dari Stock';
                        return $data;
                    }

                    $orderItem                          = OrderItem::find($request->order_item_id);
                    if ($orderItem) {
                        if ($chiller->item_name != $orderItem->nama_detail) {
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
                }
            }
        }

        DB::commit();
        $data['status'] =   200;
        $data['msg']    =   'Data telah diproses';
        return $data;
    }

    public function pemenuhanAlokasi(Request $request)
    {

        $order_item_id = $request->order_item_id;
        if ($request->key == 'info') {
            $order  =   OrderItem::find($order_item_id);
            $qty    =   Bahanbaku::where('order_item_id', $order_item_id)->sum('bb_item');
            $berat  =   Bahanbaku::where('order_item_id', $order_item_id)->sum('bb_berat');
            return view('admin.pages.penyiapan.info_order', compact('order', 'qty', 'berat'));
        } else {
            $pemenuhan = Bahanbaku::where('order_item_id', $order_item_id)->get();
            return view('admin.pages.penyiapan.pemenuhan', compact('pemenuhan'));
        }
    }

    public function fulfillItem(Request $request)
    {

        return OrderItem::fulfillItem($request->order_item_id);
    }

    public function closeOrder(Request $request)
    {
        $order_id   =   $request->order_id;
        $order      =   Order::find($order_id);

        if ($order) {
            foreach ($order->daftar_order_full as $row) {
                // dd($row);
                if ($row->order_item_bb != null) {
                    if ($row->order_item_bb->status == 1) {

                        return back()->with('status', 2)->with('message', 'Data Item Belum Tersimpan');
                    }
                }
            }

            $order->status = 10;
            $order->save();

            $net_fulfill = Netsuite::item_fulfill_sampingan('orders', $request->order_id, 'itemfulfill', null, null);

            foreach ($order->daftar_order_full as $row) {
                if ($row->status == 3) {
                    $row->status = 2;
                    $row->save();
                }
            }

            return back()->with('status', 1)->with('message', 'Data telah diproses');
        }
    }

    public function deleteAlokasi(Request $request)
    {
        DB::beginTransaction();

        $pemenuhan              = Bahanbaku::find($request->id);

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
            $fulfill_item_id->status                = NULL;
            $fulfill_item_id->fulfillment_berat     = NULL;
            $fulfill_item_id->fulfillment_qty       = NULL;
            $fulfill_item_id->save();
        }
        
        DB::commit();

        try {
            Chiller::recalculate_chiller($chiller->id);
        } catch (\Throwable $th) {
            
        }
        
        try {
            Chiller::recalculate_chiller($chil->id);
        } catch (\Throwable $th) {
            
        }

        $data['status'] =   200;
        $data['msg']    =   'Data telah diproses';
        return $data;
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

        $nama_gudang_expedisi = env('NET_SUBSIDIARY', 'CGL') . " - Storage Expedisi";

        DB::beginTransaction();

        $total  =   0;
        $net    =   [];
        for ($x = 0; $x < COUNT($order); $x++) {
            if ($berat[$x] > 0) {

                if ($qty[$x] < 1) {
                    DB::rollBack();
                    $data['status'] =   400;
                    $data['msg']    =   'Qty tidak boleh kosong';
                    return $data;
                }

                $total  +=  1;

                $chiler                     =   new Bahanbaku();
                $chiler->chiller_out        =   $item[$x];
                $chiler->order_id           =   $xcode[$x];
                $chiler->order_item_id      =   $order[$x];
                $chiler->bb_item            =   $qty[$x];
                $chiler->bb_berat           =   $berat[$x];
                $chiler->status             =   1;
                if (!$chiler->save()) {
                    DB::rollBack();
                    $data['status'] =   400;
                    $data['msg']    =   'Terjadi kesalahan proses order';
                    return $data;
                }

                $bahan                      =   Bahanbaku::where('chiller_out', $item[$x])
                    ->where('order_id', $xcode[$x])
                    ->where('order_item_id', $order[$x])
                    ->first();

                $cekchiller                 =   Chiller::find($bahan->chiller_out);

                if ($request->regu == 'Frozen') {
                    $decode                 =   json_decode(Chiller::find($bahan->orderitem->item_id)->label);

                    $abf                    =   new Abf;
                    $abf->table_name        =   'order_bahanbaku';
                    $abf->table_id          =   $bahan->id;
                    $abf->asal_tujuan       =   'orderproduksi';
                    $abf->tanggal_masuk     =   date('Y-m-d');
                    $abf->item_id           =   $bahan->orderitem->item_id;
                    $abf->item_id_lama      =   $bahan->orderitem->item_id;
                    $abf->item_name         =   $bahan->orderitem->nama_detail;
                    $abf->packaging         =   $decode->plastik->jenis ?? NULL;
                    $abf->qty_awal          =   $qty[$x];
                    $abf->berat_awal        =   $berat[$x];
                    $abf->qty_item          =   $qty[$x];
                    $abf->berat_item        =   $berat[$x];
                    $abf->jenis             =   'masuk';
                    $abf->type              =   'hasil-produksi';
                    $abf->status            =   1;
                    if (!$abf->save()) {
                        DB::rollBack();
                        $data['status'] =   400;
                        $data['msg']    =   'Terjadi kesalahan proses order';
                        return $data;
                    }

                    $label  =   "ti_order_abf";
                    $to     =   "123";
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
                        DB::rollBack();
                        $data['status'] =   400;
                        $data['msg']    =   'Terjadi kesalahan proses order';
                        return $data;
                    }

                    $cekchiller->stock_berat    =   $cekchiller->stock_berat - $chiler->berat_item;

                    $cekchiller->stock_item     =   $cekchiller->stock_item - $chiler->qty_item;

                    if (!$cekchiller->save()) {
                        DB::rollBack();
                        $data['status'] =   400;
                        $data['msg']    =   'Terjadi kesalahan proses order';
                        return $data;
                    }

                    $label  =   "ti_fg_ekspedisi";
                    $to     =   Gudang::gudang_netid($nama_gudang_expedisi);
                }

                $so                         =   Order::find($xcode[$x]);
                $so->status                 =   5;
                if (!$so->save()) {
                    DB::rollBack();
                    $data['status'] =   400;
                    $data['msg']    =   'Terjadi kesalahan proses order';
                    return $data;
                }

                $oritem                     =   OrderItem::find($order[$x]);
                $oritem->fulfillment_berat  =   $berat[$x];
                $oritem->fulfillment_qty    =   $qty[$x];
                $oritem->status             =   1;
                $order = Order::find($oritem->order_id);

                if (!$oritem->save()) {
                    DB::rollBack();
                    $data['status'] =   400;
                    $data['msg']    =   'Terjadi kesalahan proses order';
                    return $data;
                }

                $net[]  =   [
                    'nama_tabel'    =>  "order_items",
                    "id_tabel"      =>  $oritem->id,
                    "label"         =>  $label,
                    "document_code" =>  $order->no_so ?? $order[$x],
                    "id_location"   =>  Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL') . ' - Chiller Finished Good'),
                    "location"      =>  env('NET_SUBSIDIARY', 'CGL') . " - Chiller Finished Good",
                    "from"          =>  Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL') . ' - Chiller Finished Good'),
                    "to"            =>  $to,
                    "transfer"      =>  [
                        [
                            "internal_id_item"  =>  (string)$oritem->item->netsuite_internal_id,
                            "item"              =>  (string)$oritem->item->sku,
                            "qty_to_transfer"   =>  (string)$berat[$x]
                        ]
                    ]
                ];
            }
        }

        if ($total == 0) {
            DB::rollBack();
            $data['status'] =   400;
            $data['msg']    =   'Order Kosong';
            return $data;
        }

        DB::commit();

        for ($x = 0; $x < COUNT($net); $x++) {
            Netsuite::transfer_inventory_doc($net[$x]['nama_tabel'], $net[$x]['id_tabel'], $net[$x]['label'], $net[$x]['id_location'], $net[$x]['location'], $net[$x]['from'], $net[$x]['to'], $net[$x]['transfer'], NULL, date('Y-m-d'), $net[$x]['document_code']);
        }
    }

    public function batalorder(Request $request)
    {
        $order   =   Order::find($request->id);

        if ($request->key == 'close') {
            // Close Order
            $order->status = $order->status == '0' ? NULL : '0';
            $data['msg']    =   'Berhasil Close Order';
        } else {
            // Batalkan Fulfill
            $order->status = 6;
            $data['msg']    =   'Berhasil Batalkan Fulfill';
            return $data;
        }

        $data['status'] =   400;
        $order->save();
        return $data;
    }

    public function siapKirimExport(Request $request)
    {
        $awal                   = $request->awal ?? date('Y-m-d');
        $akhir                  = $request->akhir ?? date('Y-m-d');
        $keterangan             = $request->keterangan ?? "";
        $jenis_export           = $request->jenis ?? "semua";
        $jenisItem              = $request->jenisitem;
        $jenisTanggal           = $request->jenistanggal;
        $customer               = $request->nama_customer ?? "";
        $kategori               = $request->nama_kategori ?? "";

        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=siapkirim-" . $awal . "-" . $akhir . "-" . $jenis_export . ".csv");
        $fp = fopen('php://output', 'w');
        fputcsv($fp, ["sep=,"]);
        if ($keterangan == 'terkirim') {
            $data = array(
                "No",
                "No SO",
                "No DO",
                "Nama",
                "Channel",
                "Tanggal SO",
                "Tanggal Kirim",
                "Keterangan Header",
                "SKU",
                "Item",
                "Part",
                "Bumbu",
                "Memo",
                "Order Item",
                "Order Berat",
                "Fulfillment Item",
                "Fulfillment Berat",
                "Fresh/Frozen",
                "Tidak Terkirim",
                "Tidak Terkirim Item",
                "Tidak Terkirim Berat",
                "Status Kiriman",
                "Status SO"
            );

        } else if($keterangan == 'batal'){
            $data = array(
                "No",
                "No SO",
                "No DO",
                "Nama",
                "Channel",
                "Tanggal SO",
                "Tanggal Kirim",
                "Keterangan Header",
                "SKU",
                "Item",
                "Part",
                "Bumbu",
                "Memo",
                "Order Item",
                "Order Berat",
                "Fulfillment Item",
                "Fulfillment Berat",
                "Fresh/Frozen",
                "Tidak Terkirim",
                "Tidak Terkirim Item",
                "Tidak Terkirim Berat",
                "Status Kiriman",
                "Status SO"
            );
        }
        else {
            $data = array(
                "No",
                "No SO",
                "No DO",
                "Nama",
                "Channel",
                "Tanggal SO",
                "Tanggal Kirim",
                "Keterangan Header",
                "SKU",
                "Item",
                "Part",
                "Bumbu",
                "Memo",
                "Order Item",
                "Order Berat",
                "Fulfillment Item",
                "Fulfillment Berat",
                "Fresh/Frozen",
                "Tidak Terkirim",
                "Tidak Terkirim Item",
                "Tidak Terkirim Berat",
                "Status Kiriman",
                "Status SO"
            );
        }
        fputcsv($fp, $data);

        $order = Order::where(function ($query) use ($jenisItem) {
            if ($jenisItem == 'tanpasampingan') {
                $query->whereNotIn('sales_channel', ["By Product - Paket", "By Product - Retail"]);
            } else if ($jenisItem == 'sampingan') {
                $query->whereIn('sales_channel', ["By Product - Paket", "By Product - Retail"]);
            }
        })
            ->where(function ($query) use ($jenisTanggal, $awal, $akhir) {
                if ($jenisTanggal == 'so') {
                    $query->whereBetween('tanggal_so', [$awal, $akhir]);
                } else {
                    $query->whereBetween('tanggal_kirim', [$awal, $akhir]);
                }
            })
            ->where(function ($q) use ($customer){
                if ($customer !== '') {
                    $q->where('customer_id',$customer);
                }
            })
            ->with('netsuite_closed')
            ->with('order_batal')
            ->get();
            // dd($order);

        $urut = 0;
        if ($keterangan == 'terkirim') {

            $order_item = DB::table('order_bahan_baku')
                            ->join('order_items', 'order_bahan_baku.order_item_id', '=', 'order_items.id')
                            ->join('orders', 'order_bahan_baku.order_id', '=', 'orders.id')
                            ->select(
                                'orders.id',
                                'order_items.fulfillment_berat',
                                'order_items.fulfillment_qty',
                                'order_items.berat',
                                'order_items.qty',
                                'orders.no_so',
                                'orders.no_do',
                                'orders.nama',
                                'orders.sales_channel',
                                'orders.tanggal_so',
                                'orders.tanggal_kirim',
                                'order_items.sku',
                                'order_items.part',
                                'order_items.memo',
                                'order_items.keterangan',
                                'order_items.bumbu',
                                'order_items.tidak_terkirim_catatan',
                                'order_bahan_baku.nama AS item_name',
                                'order_bahan_baku.id',
                                'order_bahan_baku.bb_berat',
                                'order_bahan_baku.bb_item',
                                'order_bahan_baku.order_id',
                                DB::raw("SUM(order_bahan_baku.bb_berat) as bb_berat_total"),
                                DB::raw("SUM(order_bahan_baku.bb_item) as bb_berat_item")
                            )
                            ->where(function ($query) use ($jenisItem) {
                                if ($jenisItem == 'tanpasampingan') {
                                    $query->whereNotIn(
                                        'order_bahan_baku.nama',
                                        [
                                            "AMPELA BERSIH BROILER",
                                            "AY - S",
                                            "HATI AMPELA BERSIH BROILER",
                                            "HATI AMPELA KOTOR BROILER",
                                            "HATI AMPELA KOTOR BROILER FROZEN",
                                            "HATI BERSIH BROILER",
                                            "KAKI BERSIH BROILER",
                                            "KAKI KOTOR BROILER",
                                            "KEPALA LEHER BROILER",
                                            "USUS BROILER",
                                            "TEMBOLOK"
                                        ]
                                    );
                                    $query->whereNotIn('sales_channel', ["By Product - Paket", "By Product - Retail"]);
                                } else {
                                    $query->whereIn('sales_channel', ["By Product - Paket", "By Product - Retail"]);
                                }
                            })
                            ->where(function ($query) use ($jenis_export) {
                                if ($jenis_export == "fresh") {
                                    $query->where('order_bahan_baku.nama', 'NOT LIKE', '%FROZEN%');
                                } else if ($jenis_export == "frozen") {
                                    $query->where('order_bahan_baku.nama', 'LIKE', '%FROZEN%');
                                }
                            })
                            ->where(function ($query) use ($jenisTanggal, $awal, $akhir) {
                                if ($jenisTanggal == 'so') {
                                    $query->whereBetween('tanggal_so', [$awal, $akhir]);
                                } else {
                                    $query->whereBetween('tanggal_kirim', [$awal, $akhir]);
                                }
                            })
                            ->where('orders.status', 10)
                            ->where('order_bahan_baku.deleted_at' , null)
                            ->orderBy('order_bahan_baku.id', 'DESC')
                            ->groupBy('order_bahan_baku.order_item_id')
                            ->groupBy('order_bahan_baku.order_id')
                            ->groupBy('order_bahan_baku.nama')
                            ->get();

            foreach ($order_item as $key => $value) {

                $tidak_terkirim_qty     = ($value->qty - $value->bb_berat_item);
                $tidak_terkirim_berat   = ($value->berat - $value->bb_berat_total);
                $tidak_terkirim         = "Terpenuhi";
                $jenis                  = "FRESH";

                if (str_contains($value->item_name, 'FROZEN')) {
                    $jenis = "FROZEN";
                }

                $data = [
                    ($urut + 1),
                    $value->no_so,
                    $value->no_do,
                    $value->nama,
                    $value->sales_channel,
                    $value->tanggal_so,
                    $value->tanggal_kirim,
                    $value->memo,
                    $value->sku,
                    $value->item_name,
                    $value->part,
                    $value->bumbu,
                    $value->memo,
                    $value->qty,
                    number_format($value->berat,1,',', '.'),
                    $value->bb_berat_item,
                    number_format($value->bb_berat_total,1,',', '.'),
                    $jenis,
                    $value->tidak_terkirim_catatan,
                    $tidak_terkirim_qty,
                    number_format($tidak_terkirim_berat,1,',', '.'),
                ];
                fputcsv($fp, $data);
                $urut++;
            }

        } else if($keterangan == 'batal') {
            foreach ($order as $no => $o) :
                $order_item = OrderItem::where('order_id', $o->id)
                ->join('orders','orders.id','=','order_items.order_id')
                ->join('items','order_items.item_id', '=','items.id')
                ->join('category','items.category_id','=','category.id')
                    // ->where(function($q) use ($keterangan){
                    //     if($keterangan == 'tidak-terkirim'){
                    //         $q->whereIn('orders.sales_channel', ["By Product - Paket", "By Product - Retail"]);
                    //     }
                    // })
                    ->where(function ($query) use ($jenisItem,$keterangan) {
                        if ($jenisItem == 'tanpasampingan') {
                            $query->whereNotIn(
                                'nama_detail',
                                [
                                    "AMPELA BERSIH BROILER",
                                    "AY - S",
                                    "HATI AMPELA BERSIH BROILER",
                                    "HATI AMPELA KOTOR BROILER",
                                    "HATI AMPELA KOTOR BROILER FROZEN",
                                    "HATI BERSIH BROILER",
                                    "KAKI BERSIH BROILER",
                                    "KAKI KOTOR BROILER",
                                    "KEPALA LEHER BROILER",
                                    "USUS BROILER",
                                    "TEMBOLOK"
                                ]
                            );
                        }
                        // if($keterangan == 'batal'){
                        //     $query->whereIn('orders.no_so', MarketingSO::select('no_so')->where('status', '0'));
                        // }
                        // if ($keterangan == "tidak-terkirim") {
                        //     $query->whereIn('nama_detail',[
                        //         "LEMAK AMPLA REPACK FROZEN",
                        //         "LEHER REPACK FROZEN",
                        //         "LEMAK LEHER REPACK FROZEN",
                        //         "LEHER PEJANTAN FROZEN",
                        //         "LEMAK LEHER PEJANTAN FROZEN",
                        //         "LEHER PARENT FROZEN",
                        //         "LEMAK LEHER PARENT FROZEN",
                        //         "LEMAK LEHER BROILER FROZEN",
                        //         "LEMAK AMPLA KAMPUNG FROZEN",
                        //         "MARAS KAMPUNG FROZEN",
                        //         "LEMAK LEHER KAMPUNG FROZEN",
                        //         "LEMAK AMPLA PEJANTAN FROZEN",
                        //         "MARAS BROILER FROZEN",
                        //         "LEHER BROILER FROZEN",
                        //         "LEHER BROILER",
                        //         "LEHER KAMPUNG",
                        //         "LEHER PARENT",
                        //         "LEHER PEJANTAN",
                        //         "LEHER REPACK",
                        //         "LEHER THAWING",
                        //         "LEMAK AMPLA BROILER",
                        //         "LEMAK AMPLA KAMPUNG",
                        //         "LEMAK AMPLA PARENT",
                        //         "LEMAK AMPLA PEJANTAN",
                        //         "LEMAK AMPLA REPACK",
                        //         "LEMAK AMPLA THAWING",
                        //         "LEMAK LEHER BROILER",
                        //         "LEMAK LEHER KAMPUNG",
                        //         "LEMAK LEHER PARENT",
                        //         "LEMAK LEHER PEJANTAN",
                        //         "LEMAK LEHER REPACK",
                        //         "LEMAK LEHER THAWING",
                        //         "MARAS BROILER",
                        //         "MARAS KAMPUNG",
                        //         "MARAS PARENT",
                        //         "MARAS PEJANTAN",
                        //         "MARAS REPACK",
                        //         "MARAS THAWING",
                        //     ]);
                        // }
                    })
                    ->where(function($q) use ($kategori){
                        if ($kategori) {
                            $q->where('items.category_id',$kategori);
                        }
                    })
                    ->get();

                foreach ($order_item as $sub_no => $item) :

                    $tidak_terkirim_qty     = ($item->qty - $item->fulfillment_qty);
                    $tidak_terkirim_berat   = ($item->berat - $item->fulfillment_berat);
                    $tidak_terkirim         = "Terpenuhi";
                    
                    $orderBatal             = $o->order_batal->status ?? '';

                    if($orderBatal == '0'){
                        $batal      = 'Batal';
                    }else{
                        $batal      = '';
                    }

                    if ($tidak_terkirim_qty != "0") {
                        $tidak_terkirim_qty = $tidak_terkirim_qty * (-1);
                    }
                    if ($tidak_terkirim_berat != "0") {
                        $tidak_terkirim_berat = $tidak_terkirim_berat * (-1);
                    }


                    if (($item->fulfillment_berat == 0 || $item->fulfillment_berat == "") && ($orderBatal != '0')) {
                        $tidak_terkirim = "Tidak Terpenuhi";
                    } else if($orderBatal == '0'){
                        $tidak_terkirim = 'Batal';
                    }else {
                        if ($item->qty != 0 || $item->qty != "") {
                            if ($item->fulfillment_berat >= $item->berat) {
                                $tidak_terkirim         = "Terpenuhi";
                            } else if ($item->fulfillment_qty >= $item->qty) {
                                $tidak_terkirim         = "Terpenuhi";
                            } else {
                                $tidak_terkirim         = "Tidak Terpenuhi";
                            }
                        } else {
                            // Jika orderan QTY == 0
                            if ($item->berat <= $item->fulfillment_berat) {
                                $tidak_terkirim         = "Terpenuhi";
                            } else {
                                $tidak_terkirim         = "Tidak Terpenuhi";
                            }
                        }
                    }



                    if ($jenis_export == "semua") {
                        $jenis = "FRESH";

                        if (str_contains($item->nama_detail, 'FROZEN')) {
                            $jenis = "FROZEN";
                        }

                        if ($keterangan == "tidak-terkirim" || $keterangan == "batal") {

                            if ($item->tidak_terkirim == "1" || $item->fulfillment_berat == "") {
                                $data = array(
                                    ($urut + 1),
                                    $o->no_so,
                                    $o->no_do,
                                    $o->nama,
                                    $o->sales_channel,
                                    $o->tanggal_so,
                                    $o->tanggal_kirim,
                                    $o->keterangan,
                                    $item->sku,
                                    $item->nama_detail,
                                    $item->part,
                                    $item->bumbu,
                                    $item->memo,
                                    $item->qty,
                                    number_format($item->berat,1,',', '.'),
                                    $item->fulfillment_qty,
                                    number_format($item->fulfillment_berat,1,',', '.'),
                                    $jenis,
                                    $item->tidak_terkirim_catatan,
                                    $tidak_terkirim_qty,
                                    number_format($tidak_terkirim_berat,1,',', '.'),
                                    $tidak_terkirim,
                                    $o->netsuite_closed->netsuite_closed_status ?? '' ,
                                    // $batal
                                );
                                fputcsv($fp, $data);
                                $urut++;
                            }
                        } else {
                            $data = array(
                                ($urut + 1),
                                $o->no_so,
                                $o->no_do,
                                $o->nama,
                                $o->sales_channel,
                                $o->tanggal_so,
                                $o->tanggal_kirim,
                                $o->keterangan,
                                $item->sku,
                                $item->nama_detail,
                                $item->part,
                                $item->bumbu,
                                $item->memo,
                                $item->qty,
                                number_format($item->berat,1,',', '.'),
                                $item->fulfillment_qty,
                                number_format($item->fulfillment_berat,1,',', '.'),
                                $jenis,
                                "",
                                $tidak_terkirim_qty,
                                number_format($tidak_terkirim_berat,1,',', '.'),
                                $tidak_terkirim,
                                $o->netsuite_closed->netsuite_closed_status ?? '',
                                // $batal 
                            );
                            fputcsv($fp, $data);
                            $urut++;
                        }
                    } elseif ($jenis_export == "frozen") {

                        if (str_contains($item->nama_detail, 'FROZEN')) {

                            if ($keterangan == "tidak-terkirim") {

                                if ($item->tidak_terkirim == "1") {
                                    $data = array(
                                        ($urut + 1),
                                        $o->no_so,
                                        $o->no_do,
                                        $o->nama,
                                        $o->sales_channel,
                                        $o->tanggal_so,
                                        $o->tanggal_kirim,
                                        $o->keterangan,
                                        $item->sku,
                                        $item->nama_detail,
                                        $item->part,
                                        $item->bumbu,
                                        $item->memo,
                                        $item->qty,
                                        number_format($item->berat,1,',', '.'),
                                        $item->fulfillment_qty,
                                        number_format($item->fulfillment_berat,1,',', '.'),
                                        "FROZEN",
                                        $item->tidak_terkirim_catatan,
                                        $tidak_terkirim_qty,
                                        number_format($tidak_terkirim_berat,1,',', '.'),
                                        $tidak_terkirim,
                                        $o->netsuite_closed->netsuite_closed_status ?? '',
                                        // $batal 
                                    );
                                    fputcsv($fp, $data);
                                    $urut++;
                                }
                            } else {
                                $data = array(
                                    ($urut + 1),
                                    $o->no_so,
                                    $o->no_do,
                                    $o->nama,
                                    $o->sales_channel,
                                    $o->tanggal_so,
                                    $o->tanggal_kirim,
                                    $o->keterangan,
                                    $item->sku,
                                    $item->nama_detail,
                                    $item->part,
                                    $item->bumbu,
                                    $item->memo,
                                    $item->qty,
                                    number_format($item->berat,1,',', '.'),
                                    $item->fulfillment_qty,
                                    number_format($item->fulfillment_berat,1,',', '.'),
                                    "FROZEN",
                                    "",
                                    $tidak_terkirim_qty,
                                    number_format($tidak_terkirim_berat,1,',', '.'),
                                    $tidak_terkirim,
                                    $o->netsuite_closed->netsuite_closed_status ?? '',
                                    // $batal 
                                );
                                fputcsv($fp, $data);
                                $urut++;
                            }
                        }
                    } elseif ($jenis_export == "fresh") {

                        if (!str_contains($item->nama_detail, 'FROZEN')) {

                            if ($keterangan == "tidak-terkirim") {

                                if ($item->tidak_terkirim == "1") {
                                    $data = array(
                                        ($urut + 1),
                                        $o->no_so,
                                        $o->no_do,
                                        $o->nama,
                                        $o->sales_channel,
                                        $o->tanggal_so,
                                        $o->tanggal_kirim,
                                        $o->keterangan,
                                        $item->sku,
                                        $item->nama_detail,
                                        $item->part,
                                        $item->bumbu,
                                        $item->memo,
                                        $item->qty,
                                        number_format($item->berat,1,',', '.'),
                                        $item->fulfillment_qty,
                                        number_format($item->fulfillment_berat,1,',', '.'),
                                        "FRESH",
                                        $item->tidak_terkirim_catatan,
                                        $tidak_terkirim_qty,
                                        number_format($tidak_terkirim_berat,1,',', '.'),
                                        $tidak_terkirim,
                                        $o->netsuite_closed->netsuite_closed_status ?? '',
                                        // $batal 
                                    );
                                    fputcsv($fp, $data);
                                    $urut++;
                                }
                            } else {
                                $data = array(
                                    ($urut + 1),
                                    $o->no_so,
                                    $o->no_do,
                                    $o->nama,
                                    $o->sales_channel,
                                    $o->tanggal_so,
                                    $o->tanggal_kirim,
                                    $o->keterangan,
                                    $item->sku,
                                    $item->nama_detail,
                                    $item->part,
                                    $item->bumbu,
                                    $item->memo,
                                    $item->qty,
                                    number_format($item->berat,1,',', '.'),
                                    $item->fulfillment_qty,
                                    number_format($item->fulfillment_berat,1,',', '.'),
                                    "FRESH",
                                    "",
                                    $tidak_terkirim_qty,
                                    number_format($tidak_terkirim_berat,1,',', '.'),
                                    $tidak_terkirim,
                                    $o->netsuite_closed->netsuite_closed_status ?? '',
                                    // $batal 
                                );
                                fputcsv($fp, $data);
                                $urut++;
                            }
                        }
                    }

                endforeach;

            endforeach;
        } else {
            foreach ($order as $no => $o) :
                $order_item = OrderItem::where('order_id', $o->id)
                ->join('orders','orders.id','=','order_items.order_id')
                ->join('items','order_items.item_id', '=','items.id')
                ->join('category','items.category_id','=','category.id')
                    // ->where(function($q) use ($keterangan){
                    //     if($keterangan == 'tidak-terkirim'){
                    //         $q->whereIn('orders.sales_channel', ["By Product - Paket", "By Product - Retail"]);
                    //     }
                    // })
                    ->where(function ($query) use ($jenisItem,$keterangan) {
                        if ($jenisItem == 'tanpasampingan') {
                            $query->whereNotIn(
                                'nama_detail',
                                [
                                    "AMPELA BERSIH BROILER",
                                    "AY - S",
                                    "HATI AMPELA BERSIH BROILER",
                                    "HATI AMPELA KOTOR BROILER",
                                    "HATI AMPELA KOTOR BROILER FROZEN",
                                    "HATI BERSIH BROILER",
                                    "KAKI BERSIH BROILER",
                                    "KAKI KOTOR BROILER",
                                    "KEPALA LEHER BROILER",
                                    "USUS BROILER",
                                    "TEMBOLOK"
                                ]
                            );
                        }
                        // if ($keterangan == "tidak-terkirim") {
                        //     $query->whereIn('nama_detail',[
                        //         "LEMAK AMPLA REPACK FROZEN",
                        //         "LEHER REPACK FROZEN",
                        //         "LEMAK LEHER REPACK FROZEN",
                        //         "LEHER PEJANTAN FROZEN",
                        //         "LEMAK LEHER PEJANTAN FROZEN",
                        //         "LEHER PARENT FROZEN",
                        //         "LEMAK LEHER PARENT FROZEN",
                        //         "LEMAK LEHER BROILER FROZEN",
                        //         "LEMAK AMPLA KAMPUNG FROZEN",
                        //         "MARAS KAMPUNG FROZEN",
                        //         "LEMAK LEHER KAMPUNG FROZEN",
                        //         "LEMAK AMPLA PEJANTAN FROZEN",
                        //         "MARAS BROILER FROZEN",
                        //         "LEHER BROILER FROZEN",
                        //         "LEHER BROILER",
                        //         "LEHER KAMPUNG",
                        //         "LEHER PARENT",
                        //         "LEHER PEJANTAN",
                        //         "LEHER REPACK",
                        //         "LEHER THAWING",
                        //         "LEMAK AMPLA BROILER",
                        //         "LEMAK AMPLA KAMPUNG",
                        //         "LEMAK AMPLA PARENT",
                        //         "LEMAK AMPLA PEJANTAN",
                        //         "LEMAK AMPLA REPACK",
                        //         "LEMAK AMPLA THAWING",
                        //         "LEMAK LEHER BROILER",
                        //         "LEMAK LEHER KAMPUNG",
                        //         "LEMAK LEHER PARENT",
                        //         "LEMAK LEHER PEJANTAN",
                        //         "LEMAK LEHER REPACK",
                        //         "LEMAK LEHER THAWING",
                        //         "MARAS BROILER",
                        //         "MARAS KAMPUNG",
                        //         "MARAS PARENT",
                        //         "MARAS PEJANTAN",
                        //         "MARAS REPACK",
                        //         "MARAS THAWING",
                        //     ]);
                        // }
                    })
                    ->where(function($q) use ($kategori){
                        if ($kategori) {
                            $q->where('items.category_id',$kategori);
                        }
                    })
                    ->get();

                foreach ($order_item as $sub_no => $item) :

                    $tidak_terkirim_qty     = ($item->qty - $item->fulfillment_qty);
                    $tidak_terkirim_berat   = ($item->berat - $item->fulfillment_berat);
                    $tidak_terkirim         = "Terpenuhi";

                    if ($tidak_terkirim_qty != "0") {
                        $tidak_terkirim_qty = $tidak_terkirim_qty * (-1);
                    }
                    if ($tidak_terkirim_berat != "0") {
                        $tidak_terkirim_berat = $tidak_terkirim_berat * (-1);
                    }

                    $orderBatal             = $o->order_batal->status ?? '';

                    if($orderBatal == '0'){
                        $batal      = 'Batal';
                    }else{
                        $batal      = '';
                    }

                    if (($item->fulfillment_berat == 0 || $item->fulfillment_berat == "") && ($orderBatal != '0')) {
                        $tidak_terkirim = "Tidak Terpenuhi";
                    } else if($orderBatal == '0'){
                        $tidak_terkirim = 'Batal';
                    }else {
                        if ($item->qty != 0 || $item->qty != "") {
                            if ($item->fulfillment_berat >= $item->berat) {
                                $tidak_terkirim         = "Terpenuhi";
                            } else if ($item->fulfillment_qty >= $item->qty) {
                                $tidak_terkirim         = "Terpenuhi";
                            } else {
                                $tidak_terkirim         = "Tidak Terpenuhi";
                            }
                        } else {
                            // Jika orderan QTY == 0
                            if ($item->berat <= $item->fulfillment_berat) {
                                $tidak_terkirim         = "Terpenuhi";
                            } else {
                                $tidak_terkirim         = "Tidak Terpenuhi";
                            }
                        }
                    }



                    if ($jenis_export == "semua") {
                        $jenis = "FRESH";

                        if (str_contains($item->nama_detail, 'FROZEN')) {
                            $jenis = "FROZEN";
                        }

                        if ($keterangan == "tidak-terkirim") {

                            if ($item->tidak_terkirim == "1" || $item->fulfillment_berat == "") {
                                $data = array(
                                    ($urut + 1),
                                    $o->no_so,
                                    $o->no_do,
                                    $o->nama,
                                    $o->sales_channel,
                                    $o->tanggal_so,
                                    $o->tanggal_kirim,
                                    $o->keterangan,
                                    $item->sku,
                                    $item->nama_detail,
                                    $item->part,
                                    $item->bumbu,
                                    $item->memo,
                                    $item->qty,
                                    number_format($item->berat,1,',', '.'),
                                    $item->fulfillment_qty,
                                    number_format($item->fulfillment_berat,1,',', '.'),
                                    $jenis,
                                    $item->tidak_terkirim_catatan,
                                    $tidak_terkirim_qty,
                                    number_format($tidak_terkirim_berat,1,',', '.'),
                                    $tidak_terkirim,
                                    $o->netsuite_closed->netsuite_closed_status ?? "",
                                    // $batal
                                );
                                fputcsv($fp, $data);
                                $urut++;
                            }
                        } else {
                            $data = array(
                                ($urut + 1),
                                $o->no_so,
                                $o->no_do,
                                $o->nama,
                                $o->sales_channel,
                                $o->tanggal_so,
                                $o->tanggal_kirim,
                                $o->keterangan,
                                $item->sku,
                                $item->nama_detail,
                                $item->part,
                                $item->bumbu,
                                $item->memo,
                                $item->qty,
                                number_format($item->berat,1,',', '.'),
                                $item->fulfillment_qty,
                                number_format($item->fulfillment_berat,1,',', '.'),
                                $jenis,
                                "",
                                $tidak_terkirim_qty,
                                number_format($tidak_terkirim_berat,1,',', '.'),
                                $tidak_terkirim,
                                $o->netsuite_closed->netsuite_closed_status ?? "",
                                // $batal
                            );
                            fputcsv($fp, $data);
                            $urut++;
                        }
                    } elseif ($jenis_export == "frozen") {

                        if (str_contains($item->nama_detail, 'FROZEN')) {

                            if ($keterangan == "tidak-terkirim") {

                                if ($item->tidak_terkirim == "1") {
                                    $data = array(
                                        ($urut + 1),
                                        $o->no_so,
                                        $o->no_do,
                                        $o->nama,
                                        $o->sales_channel,
                                        $o->tanggal_so,
                                        $o->tanggal_kirim,
                                        $o->keterangan,
                                        $item->sku,
                                        $item->nama_detail,
                                        $item->part,
                                        $item->bumbu,
                                        $item->memo,
                                        $item->qty,
                                        number_format($item->berat,1,',', '.'),
                                        $item->fulfillment_qty,
                                        number_format($item->fulfillment_berat,1,',', '.'),
                                        "FROZEN",
                                        $item->tidak_terkirim_catatan,
                                        $tidak_terkirim_qty,
                                        number_format($tidak_terkirim_berat,1,',', '.'),
                                        $tidak_terkirim,
                                        $o->netsuite_closed->netsuite_closed_status ?? '',
                                        // $batal

                                    );
                                    fputcsv($fp, $data);
                                    $urut++;
                                }
                            } else {
                                $data = array(
                                    ($urut + 1),
                                    $o->no_so,
                                    $o->no_do,
                                    $o->nama,
                                    $o->sales_channel,
                                    $o->tanggal_so,
                                    $o->tanggal_kirim,
                                    $o->keterangan,
                                    $item->sku,
                                    $item->nama_detail,
                                    $item->part,
                                    $item->bumbu,
                                    $item->memo,
                                    $item->qty,
                                    number_format($item->berat,1,',', '.'),
                                    $item->fulfillment_qty,
                                    number_format($item->fulfillment_berat,1,',', '.'),
                                    "FROZEN",
                                    "",
                                    $tidak_terkirim_qty,
                                    number_format($tidak_terkirim_berat,1,',', '.'),
                                    $tidak_terkirim,
                                    $o->netsuite_closed->netsuite_closed_status ?? '',
                                    // $batal
                                );
                                fputcsv($fp, $data);
                                $urut++;
                            }
                        }
                    } elseif ($jenis_export == "fresh") {

                        if (!str_contains($item->nama_detail, 'FROZEN')) {

                            if ($keterangan == "tidak-terkirim") {

                                if ($item->tidak_terkirim == "1") {
                                    $data = array(
                                        ($urut + 1),
                                        $o->no_so,
                                        $o->no_do,
                                        $o->nama,
                                        $o->sales_channel,
                                        $o->tanggal_so,
                                        $o->tanggal_kirim,
                                        $o->keterangan,
                                        $item->sku,
                                        $item->nama_detail,
                                        $item->part,
                                        $item->bumbu,
                                        $item->memo,
                                        $item->qty,
                                        number_format($item->berat,1,',', '.'),
                                        $item->fulfillment_qty,
                                        number_format($item->fulfillment_berat,1,',', '.'),
                                        "FRESH",
                                        $item->tidak_terkirim_catatan,
                                        $tidak_terkirim_qty,
                                        number_format($tidak_terkirim_berat,1,',', '.'),
                                        $tidak_terkirim,
                                        $o->netsuite_closed->netsuite_closed_status ?? '',
                                        // $batal
                                    );
                                    fputcsv($fp, $data);
                                    $urut++;
                                }
                            } else {
                                $data = array(
                                    ($urut + 1),
                                    $o->no_so,
                                    $o->no_do,
                                    $o->nama,
                                    $o->sales_channel,
                                    $o->tanggal_so,
                                    $o->tanggal_kirim,
                                    $o->keterangan,
                                    $item->sku,
                                    $item->nama_detail,
                                    $item->part,
                                    $item->bumbu,
                                    $item->memo,
                                    $item->qty,
                                    number_format($item->berat,1,',', '.'),
                                    $item->fulfillment_qty,
                                    number_format($item->fulfillment_berat,1,',', '.'),
                                    "FRESH",
                                    "",
                                    $tidak_terkirim_qty,
                                    number_format($tidak_terkirim_berat,1,',', '.'),
                                    $tidak_terkirim,
                                    $o->netsuite_closed->netsuite_closed_status ?? '',
                                    // $batal
                                );
                                fputcsv($fp, $data);
                                $urut++;
                            }
                        }
                    }

                endforeach;

            endforeach;
        }


        return "";
    }

    public function siapKirimData(Request $request)
    {
        $awal           = $request->awal ?? date('Y-m-d');
        $akhir          = $request->akhir ?? date('Y-m-d');
        $keterangan     = $request->keterangan ?? "";
        $jenis_export   = $request->jenis_export ?? "semua";
        $customer       = $request->nama_customer ?? "";
        $kategori       = $request->nama_kategori ?? "";

        $products = Order::whereBetween('tanggal_kirim', [$awal . " 00:00:00", $akhir . " 23:59:59"])
            ->whereNotIn('sales_channel', ["By Product - Paket", "By Product - Retail"])
            ->where(function($q) use ($customer){
                if ($customer != '') {
                    $q->where('customer_id',$customer);
                }
            })
            ->get();
        
        $customer_ids = $products->pluck('customer_id')->toArray();
        $customer = Customer::whereIn('id',$customer_ids)->get();


        $data = [];

        $urut = 0;
        foreach ($products as $no => $o) :

            foreach (OrderItem::where('order_items.order_id', $o->id)
                ->leftJoin('items','order_items.item_id', '=','items.id')
                ->leftJoin('category','items.category_id','=','category.id')
                ->where(function ($query) use ($jenis_export,$kategori) {
                    if ($jenis_export == "frozen") {
                        $query->where('order_items.nama_detail', 'like', '%FROZEN%');
                    } elseif ($jenis_export == "fresh") {
                        $query->where('order_items.nama_detail', 'not like', '%FROZEN%');
                    } elseif ($kategori){
                        $query->where('items.category_id',$kategori);
                    }
                })
                ->get() as $sub_no => $item) :

                $tidak_terkirim_qty     = ($item->qty - $item->fulfillment_qty);
                $tidak_terkirim_berat   = ($item->berat - $item->fulfillment_berat);
                $tidak_terkirim         = "Terpenuhi";

                if ($tidak_terkirim_qty != "0") {
                    $tidak_terkirim_qty = $tidak_terkirim_qty * (-1);
                }
                if ($tidak_terkirim_berat != "0") {
                    $tidak_terkirim_berat = $tidak_terkirim_berat * (-1);
                }



                if ($item->qty != 0 || $item->qty != "") {
                    if ($item->fulfillment_berat >= $item->berat) {
                        $tidak_terkirim         = "Terpenuhi";
                    } else if ($item->fulfillment_qty >= $item->qty) {
                        $tidak_terkirim         = "Terpenuhi";
                    } else {
                        $tidak_terkirim         = "Tidak Terpenuhi";
                    }
                } else {
                    // Jika orderan QTY == 0
                    if ($item->berat <= $item->fulfillment_berat) {
                        $tidak_terkirim         = "Terpenuhi";
                    } else {
                        $tidak_terkirim         = "Tidak Terpenuhi";
                    }
                }

                $nama_marketing                 = $o->marketingnama ? $o->marketingnama->nama : "#";

                if ($jenis_export == "semua") {
                    $jenis = "FRESH";

                    if (str_contains($item->nama_detail, 'FROZEN')) {
                        $jenis = "FROZEN";
                    }

                    if ($keterangan == "tidak-terkirim") {

                        if ($item->tidak_terkirim == "1" || $item->fulfillment_berat == "") {
                            $data[] = array(
                                ($urut + 1),
                                $o->no_so,
                                $o->no_do,
                                $o->nama,
                                $o->sales_channel,
                                $o->tanggal_so,
                                $o->tanggal_kirim,
                                $o->keterangan,
                                $item->sku,
                                $item->nama_detail,
                                $item->part,
                                $item->bumbu,
                                $item->memo,
                                $item->qty,
                                str_replace(".", ",", $item->berat),
                                $item->fulfillment_qty,
                                str_replace(".", ",", $item->fulfillment_berat),
                                $jenis,
                                $item->tidak_terkirim_catatan,
                                $tidak_terkirim_qty,
                                str_replace(".", ",", $tidak_terkirim_berat),
                                $tidak_terkirim,
                                $nama_marketing
                            );
                            $urut++;
                        }
                    } else {
                        $data[] = array(
                            ($urut + 1),
                            $o->no_so,
                            $o->no_do,
                            $o->nama,
                            $o->sales_channel,
                            $o->tanggal_so,
                            $o->tanggal_kirim,
                            $o->keterangan,
                            $item->sku,
                            $item->nama_detail,
                            $item->part,
                            $item->bumbu,
                            $item->memo,
                            $item->qty,
                            str_replace(".", ",", $item->berat),
                            $item->fulfillment_qty,
                            str_replace(".", ",", $item->fulfillment_berat),
                            $jenis,
                            "",
                            $tidak_terkirim_qty,
                            str_replace(".", ",", $tidak_terkirim_berat),
                            $tidak_terkirim,
                            $nama_marketing
                        );
                        $urut++;
                    }
                } elseif ($jenis_export == "frozen") {

                    if (str_contains($item->nama_detail, 'FROZEN')) {

                        if ($keterangan == "tidak-terkirim") {

                            if ($item->tidak_terkirim == "1") {
                                $data[] = array(
                                    ($urut + 1),
                                    $o->no_so,
                                    $o->no_do,
                                    $o->nama,
                                    $o->sales_channel,
                                    $o->tanggal_so,
                                    $o->tanggal_kirim,
                                    $o->keterangan,
                                    $item->sku,
                                    $item->nama_detail,
                                    $item->part,
                                    $item->bumbu,
                                    $item->memo,
                                    $item->qty,
                                    str_replace(".", ",", $item->berat),
                                    $item->fulfillment_qty,
                                    str_replace(".", ",", $item->fulfillment_berat),
                                    "FROZEN",
                                    $item->tidak_terkirim_catatan,
                                    $tidak_terkirim_qty,
                                    str_replace(".", ",", $tidak_terkirim_berat),
                                    $tidak_terkirim,
                                    $nama_marketing
                                );
                                $urut++;
                            }
                        } else {
                            $data[] = array(
                                ($urut + 1),
                                $o->no_so,
                                $o->no_do,
                                $o->nama,
                                $o->sales_channel,
                                $o->tanggal_so,
                                $o->tanggal_kirim,
                                $o->keterangan,
                                $item->sku,
                                $item->nama_detail,
                                $item->part,
                                $item->bumbu,
                                $item->memo,
                                $item->qty,
                                str_replace(".", ",", $item->berat),
                                $item->fulfillment_qty,
                                str_replace(".", ",", $item->fulfillment_berat),
                                "FROZEN",
                                "",
                                $tidak_terkirim_qty,
                                str_replace(".", ",", $tidak_terkirim_berat),
                                $tidak_terkirim,
                                $nama_marketing
                            );
                            $urut++;
                        }
                    }
                } elseif ($jenis_export == "fresh") {

                    if (!str_contains($item->nama_detail, 'FROZEN')) {

                        if ($keterangan == "tidak-terkirim") {

                            if ($item->tidak_terkirim == "1") {
                                $data[] = array(
                                    ($urut + 1),
                                    $o->no_so,
                                    $o->no_do,
                                    $o->nama,
                                    $o->sales_channel,
                                    $o->tanggal_so,
                                    $o->tanggal_kirim,
                                    $o->keterangan,
                                    $item->sku,
                                    $item->nama_detail,
                                    $item->part,
                                    $item->bumbu,
                                    $item->memo,
                                    $item->qty,
                                    str_replace(".", ",", $item->berat),
                                    $item->fulfillment_qty,
                                    str_replace(".", ",", $item->fulfillment_berat),
                                    "FRESH",
                                    $item->tidak_terkirim_catatan,
                                    $tidak_terkirim_qty,
                                    str_replace(".", ",", $tidak_terkirim_berat),
                                    $tidak_terkirim,
                                    $nama_marketing
                                );
                                $urut++;
                            }
                        } else {
                            $data[] = array(
                                ($urut + 1),
                                $o->no_so,
                                $o->no_do,
                                $o->nama,
                                $o->sales_channel,
                                $o->tanggal_so,
                                $o->tanggal_kirim,
                                $o->keterangan,
                                $item->sku,
                                $item->nama_detail,
                                $item->part,
                                $item->bumbu,
                                $item->memo,
                                $item->qty,
                                str_replace(".", ",", $item->berat),
                                $item->fulfillment_qty,
                                str_replace(".", ",", $item->fulfillment_berat),
                                "FRESH",
                                "",
                                $tidak_terkirim_qty,
                                str_replace(".", ",", $tidak_terkirim_berat),
                                $tidak_terkirim,
                                $nama_marketing
                            );
                            $urut++;
                        }
                    }
                }

            endforeach;

        endforeach;

        if ($request->key == 'json') {
            // return dd($customer);
            return response()->json($data);
        }else if($request->key == 'json_customer'){
            return response()->json($customer);
        }else if ($request->key == 'json_category'){
            $id_order           = $products->pluck('id')->toArray();
            $order_item         = OrderItem::whereIn('order_id', $id_order)->get();
            $id_item            = $order_item->pluck('item_id')->toArray();
            $list_category_item = DB::table('items')->select('items.id','category.id as id_category','category.nama as nama_category')
                                        ->join('category','items.category_id','=','category.id')
                                        ->whereIn('items.id',$id_item)
                                        ->groupBy('category.id')
                                        ->get();
            return response()->json($list_category_item);
        }else {
            return view('admin.pages.menu_order.rekap_order', compact('data', 'awal', 'akhir', 'keterangan', 'jenis_export'));
        }
    }


    public function simpanketerangan(Request $request)
    {

        $order_item = OrderItem::where('order_id', $request->order_id)
            ->where('id', $request->id)
            ->where('item_id', $request->item_id);

        $change_status = $order_item->update([
            'tidak_terkirim' => 1,
            'tidak_terkirim_catatan' => $request->keterangan
        ]);

        return back()->with('status', 1)->with('message', 'Data Tersimpan');
    }

    public function siapKirimCsv(Request $request)
    {
        $awal                   = $request->awal ?? date('Y-m-d');
        $akhir                  = $request->akhir ?? date('Y-m-d');
        $keterangan             = $request->keterangan ?? "";
        $jenis_export           = $request->jenis ?? "semua";
        $jenisItem              = $request->jenisitem;
        $jenisTanggal           = $request->jenistanggal;
        $customer               = $request->nama_customer ?? "";
        $kategori               = $request->nama_kategori ?? "";
        $status                 = $request->status;
        $urut = 0;

            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=siapkirim-" . $awal . "-" . $akhir . "-" . $jenis_export . ".csv");
            $fp = fopen('php://output', 'w');
            fputcsv($fp, ["sep=,"]);
            $data = array(
                "No",
                "No SO",
                "No DO",
                "Nama Customer",
                "Marketing",
                "Channel",
                "Tanggal SO",
                "Tanggal Kirim",
                "Keterangan Header",
                "SKU",
                "Item",
                "Part",
                "Bumbu",
                "Memo",
                "Order Item",
                "Order Berat",
                "Fulfillment Item",
                "Fulfillment Berat",
                "Fresh/Frozen",
                "Tidak Terkirim",
                "Tidak Terkirim Item",
                "Tidak Terkirim Berat",
                "Status Kiriman",
                "Status SO"
            );
            fputcsv($fp, $data);

            $data_terkirim  =  $order_item = DB::table('order_bahan_baku')
                        ->join('order_items', 'order_bahan_baku.order_item_id', '=', 'order_items.id')
                        ->join('orders', 'order_bahan_baku.order_id', '=', 'orders.id')
                        ->join('marketing', 'marketing.netsuite_internal_id', '=', 'orders.sales_id')
                        ->select(
                            'orders.id',
                            'order_items.fulfillment_berat',
                            'order_items.fulfillment_qty',
                            'order_items.berat',
                            'order_items.qty',
                            'orders.no_so',
                            'orders.no_do',
                            'orders.nama as nama_customer',
                            'marketing.nama as nama_marketing',
                            'orders.sales_channel',
                            'orders.tanggal_so',
                            'orders.tanggal_kirim',
                            'order_items.sku',
                            'order_items.part',
                            'order_items.memo',
                            'order_items.keterangan',
                            'order_items.bumbu',
                            'order_items.tidak_terkirim_catatan',
                            'order_bahan_baku.nama AS item_name',
                            'order_bahan_baku.id',
                            'order_bahan_baku.bb_berat',
                            'order_bahan_baku.bb_item',
                            'order_bahan_baku.order_id',
                            DB::raw("SUM(order_bahan_baku.bb_berat) as bb_berat_total"),
                            DB::raw("SUM(order_bahan_baku.bb_item) as bb_berat_item")
                        )
                    ->where(function ($query) use ($jenisItem) {
                            if ($jenisItem == 'nonsampingan') {
                                $query->whereNotIn(
                                    'order_bahan_baku.nama',
                                    [
                                        "AMPELA BERSIH BROILER",
                                        "AY - S",
                                        "HATI AMPELA BERSIH BROILER",
                                        "HATI AMPELA KOTOR BROILER",
                                        "HATI AMPELA KOTOR BROILER FROZEN",
                                        "HATI BERSIH BROILER",
                                        "KAKI BERSIH BROILER",
                                        "KAKI KOTOR BROILER",
                                        "KEPALA LEHER BROILER",
                                        "USUS BROILER",
                                        "TEMBOLOK"
                                    ]
                                );
                                $query->whereNotIn('sales_channel', ["By Product - Paket", "By Product - Retail"]);
                            } else {
                                $query->whereIn('sales_channel', ["By Product - Paket", "By Product - Retail"]);
                            }
                    })
                    ->where(function ($query) use ($jenis_export) {
                            if ($jenis_export == "fresh") {
                                $query->where('order_bahan_baku.nama', 'NOT LIKE', '%FROZEN%');
                            } else if ($jenis_export == "frozen") {
                                $query->where('order_bahan_baku.nama', 'LIKE', '%FROZEN%');
                            }
                    })
                    ->where(function ($query) use ($jenisTanggal, $awal, $akhir) {
                            if ($jenisTanggal == 'so') {
                                $query->whereBetween('tanggal_so', [$awal, $akhir]);
                            } else {
                                $query->whereBetween('tanggal_kirim', [$awal, $akhir]);
                            }
                    })
                    ->where('orders.status', 10)
                    ->whereNull('order_bahan_baku.deleted_at')
                    ->orderBy('order_bahan_baku.id', 'DESC')
                    ->groupBy('order_bahan_baku.order_item_id')
                    ->groupBy('order_bahan_baku.order_id')
                    ->groupBy('order_bahan_baku.nama')
            ->get();
            // dd($data_terkirim);

            $data_batal     = OrderItem::join('orders', 'orders.id', '=', 'order_items.order_id')
                ->join('items', 'order_items.item_id', 'items.id')
                ->join('marketing', 'marketing.netsuite_internal_id', '=', 'orders.sales_id')
                ->with('itemorder.netsuite_closed')
                ->with('itemorder.ordercustomer')
                ->where(function ($query) use ($status) {
                    if($status == 1){
                        $query->where('orders.status', 10);
                    } else {
                        
                    }
                })
                ->where(function($query) use ($request) {
                    if ($request->jenisitem == 'sampingan') {
                        $query->whereIn('sales_channel', ["By Product - Paket", "By Product - Retail"]);
                    } else if ($request->jenisitem == 'nonsampingan') {
                        $query->whereNotIn(
                            'nama_detail',
                            [
                                "AMPELA BERSIH BROILER",
                                "AY - S",
                                "HATI AMPELA BERSIH BROILER",
                                "HATI AMPELA KOTOR BROILER",
                                "HATI AMPELA KOTOR BROILER FROZEN",
                                "HATI BERSIH BROILER",
                                "KAKI BERSIH BROILER",
                                "KAKI KOTOR BROILER",
                                "KEPALA LEHER BROILER",
                                "USUS BROILER",
                                "TEMBOLOK"
                            ]
                        );
                        // $query->whereNotIn('items.category_id', ['4', '10', '16']);
                        $query->whereNotIn('sales_channel', ["By Product - Paket", "By Product - Retail"]);
                    }
                })
                ->where(function ($query) use ($jenis_export) {
                    if ($jenis_export == "fresh") {
                        $query->where('items.nama', 'NOT LIKE', '%FROZEN%');
                    } else if ($jenis_export == "frozen") {
                        $query->where('items.nama', 'LIKE', '%FROZEN%');
                    }
                })
                ->where(function ($query) use ($jenisTanggal, $awal, $akhir) {
                        if ($jenisTanggal == 'so') {
                            $query->whereBetween('orders.tanggal_so', [$awal, $akhir]);
                        } else {
                            $query->whereBetween('orders.tanggal_kirim', [$awal, $akhir]);
                        }
                })
                // ->where(function ($q) use ($kategori) {
                //     if ($kategori) {
                //         $q->where('items.category_id', $kategori);
                //     }
                // })
                // ->whereHas('itemorder.marketing_so', function ($query) {
                //     $query->where('status', 0);
                // })
                // ->where('order_items.tidak_terkirim',1)         
            ->get();


        if ($keterangan == 'terkirim') {
            $order_item = $data_terkirim;
            // dd($order_item);
            
            foreach ($order_item as $key => $value) {
                $tidak_terkirim_qty     = ($value->qty - $value->bb_berat_item);
                $tidak_terkirim_berat   = ($value->berat - $value->bb_berat_total);
                $tidak_terkirim         = "Terpenuhi";
                $jenis                  = str_contains($value->item_name, 'FROZEN') ? "FROZEN" : "FRESH";

                $data = [
                    ($urut + 1),
                    $value->no_so,
                    $value->no_do,
                    $value->nama_customer,
                    $value->nama_marketing,
                    $value->sales_channel,
                    $value->tanggal_so,
                    $value->tanggal_kirim,
                    $value->memo,
                    $value->sku,
                    $value->item_name,
                    $value->part,
                    $value->bumbu,
                    $value->memo,
                    $value->qty,
                    number_format($value->berat,1,',', '.'),
                    $value->bb_berat_item,
                    number_format($value->bb_berat_total,1,',', '.'),
                    $jenis,
                    $value->tidak_terkirim_catatan,
                    $tidak_terkirim_qty,
                    number_format($tidak_terkirim_berat,1,',', '.'),
                ];
                fputcsv($fp, $data);
                $urut++;
            }
        } else if ($keterangan == 'batal') {
            $order_items = $data_batal;
                // dd($order_items[0]);

                foreach ($order_items as $sub_no => $item) {
                    $tidak_terkirim_qty     = ($item->qty - $item->fulfillment_qty);
                    $tidak_terkirim_berat   = ($item->berat - $item->fulfillment_berat);
                    $tidak_terkirim         = "Terpenuhi";
                    $jenis                  = str_contains($item->nama_detail, 'FROZEN') ? "FROZEN" : "FRESH";
                    $orderBatal             = $item->itemorder->netsuite_closed->status ?? '';
                    $netsuite_status        = $item->itemorder->netsuite_closed->netsuite_closed_status ?? '';
        
                    if($orderBatal == '0'){
                        $batal      = 'Batal';
                    }else{
                        $batal      = '';
                    }

                    if ($tidak_terkirim_qty != "0") {
                        $tidak_terkirim_qty *= (-1);
                    }
                    if ($tidak_terkirim_berat != "0") {
                        $tidak_terkirim_berat *= (-1);
                    }
        
        
                    if (($item->fulfillment_berat == 0 || $item->fulfillment_berat == "") && ($orderBatal != '0')) {
                        $tidak_terkirim = "Tidak Terpenuhi";
                    } else if($orderBatal == '0'){
                        $tidak_terkirim = 'Batal';
                    }else {
                        if ($item->qty != 0 || $item->qty != "") {
                            if ($item->fulfillment_berat >= $item->berat) {
                                $tidak_terkirim         = "Terpenuhi";
                            } else if ($item->fulfillment_qty >= $item->qty) {
                                $tidak_terkirim         = "Terpenuhi";
                            } else {
                                $tidak_terkirim         = "Tidak Terpenuhi";
                            }
                        } else {
                            // Jika orderan QTY == 0
                            if ($item->berat <= $item->fulfillment_berat) {
                                $tidak_terkirim         = "Terpenuhi";
                            } else {
                                $tidak_terkirim         = "Tidak Terpenuhi";
                            }
                        }
                    }
        
                    if($orderBatal == '0'){
                        $data = array(
                            ($urut + 1),
                            $item->no_so,
                            $item->no_do,
                            $item->itemorder->nama,
                            $item->nama_marketing ?? $item->nama,
                            $item->sales_channel,
                            $item->tanggal_so,
                            $item->tanggal_kirim,
                            $item->keterangan,
                            $item->sku,
                            $item->nama_detail,
                            $item->part,
                            $item->bumbu,
                            $item->memo,
                            $item->qty,
                            number_format($item->berat,1,',', '.'),
                            $item->fulfillment_qty,
                            number_format($item->fulfillment_berat,1,',', '.'),
                            $jenis,
                            $item->tidak_terkirim_catatan,
                            $tidak_terkirim_qty,
                            number_format($tidak_terkirim_berat,1,',', '.'),
                            $tidak_terkirim,
                            $netsuite_status,
                        );
                        fputcsv($fp, $data);
                        $urut++;
                    }
                }
                    
        } else if ($keterangan == 'tidak-terkirim') {
            $order_items = $data_batal;
            // dd($order_items->sum('fulfillment_berat'));

                foreach ($order_items as $sub_no => $item) {
                    $tidak_terkirim_qty     = ($item->qty - $item->fulfillment_qty);
                    $tidak_terkirim_berat   = ($item->berat - $item->fulfillment_berat);
                    $tidak_terkirim         = "Terpenuhi";
                    $jenis                  = str_contains($item->nama_detail, 'FROZEN') ? "FROZEN" : "FRESH";
                    $orderBatal             = $item->itemorder->netsuite_closed->status ?? '';
                    $netsuite_status        = $item->itemorder->netsuite_closed->netsuite_closed_status ?? '';
        
                    if($orderBatal == '0'){
                        $batal      = 'Batal';
                    }else{
                        $batal      = '';
                    }

                    if ($tidak_terkirim_qty != "0") {
                        $tidak_terkirim_qty *= (-1);
                    }
                    if ($tidak_terkirim_berat != "0") {
                        $tidak_terkirim_berat *= (-1);
                    }
        
        
                    if (($item->fulfillment_berat == 0 || $item->fulfillment_berat == "") && ($orderBatal != '0')) {
                        $tidak_terkirim = "Tidak Terpenuhi";
                    } else if($orderBatal == '0'){
                        $tidak_terkirim = 'Batal';
                    }else {
                        if ($item->qty != 0 || $item->qty != "") {
                            if ($item->fulfillment_berat >= $item->berat) {
                                $tidak_terkirim         = "Terpenuhi";
                            } else if ($item->fulfillment_qty >= $item->qty) {
                                $tidak_terkirim         = "Terpenuhi";
                            } else {
                                $tidak_terkirim         = "Tidak Terpenuhi";
                            }
                        } else {
                            // Jika orderan QTY == 0
                            if ($item->berat <= $item->fulfillment_berat) {
                                $tidak_terkirim         = "Terpenuhi";
                            } else {
                                $tidak_terkirim         = "Tidak Terpenuhi";
                            }
                        }
                    }
        
                    if($item->tidak_terkirim == '1' || $item->fulfillment_berat == ''){
                        $data = array(
                            ($urut + 1),
                            $item->no_so,
                            $item->no_do,
                            $item->itemorder->nama,
                            $item->nama_marketing ?? $item->nama,
                            $item->sales_channel,
                            $item->tanggal_so,
                            $item->tanggal_kirim,
                            $item->keterangan,
                            $item->sku,
                            $item->nama_detail,
                            $item->part,
                            $item->bumbu,
                            $item->memo,
                            $item->qty,
                            number_format($item->berat,1,',', '.'),
                            $item->fulfillment_qty,
                            number_format($item->fulfillment_berat,1,',', '.'),
                            $jenis,
                            $item->tidak_terkirim_catatan,
                            $tidak_terkirim_qty,
                            number_format($tidak_terkirim_berat,1,',', '.'),
                            $tidak_terkirim,
                            $netsuite_status,
                        );
                        fputcsv($fp, $data);
                        $urut++;
                    }
                }
                    
        } else {
            $order_items = $data_batal->union($data_terkirim);
            // dd($order_items[0]);

            foreach ($order_items as $sub_no => $item) {
                $tidak_terkirim_qty     = ($item->qty - $item->fulfillment_qty) ?? ($item->qty - $item->bb_berat_item);
                $tidak_terkirim_berat   = ($item->berat - $item->fulfillment_berat) ?? ($item->berat - $item->bb_berat_total);
                $tidak_terkirim         = "Terpenuhi";
                $jenis = str_contains($item->nama_detail ?? $item->item_name, 'FROZEN') ? "FROZEN" : "FRESH";

                if(method_exists($item, 'itemOrder')){
                    $orderBatal             = $item->itemorder->netsuite_closed->status ?? '';
                    $netsuite_status        = $item->itemorder->netsuite_closed->netsuite_closed_status ?? '';
                    if($orderBatal == '0'){
                        $batal      = 'Batal';
                    }else{
                        $batal      = '';
                    }
                }

                if ($tidak_terkirim_qty != "0") {
                    $tidak_terkirim_qty *= (-1);
                }
                if ($tidak_terkirim_berat != "0") {
                    $tidak_terkirim_berat *= (-1);
                }
    
    
                if (($item->fulfillment_berat == 0 || $item->fulfillment_berat == "") && ($orderBatal != '0')) {
                    $tidak_terkirim = "Tidak Terpenuhi";
                } else if($orderBatal == '0'){
                    $tidak_terkirim = 'Batal';
                }else {
                    if ($item->qty != 0 || $item->qty != "") {
                        if ($item->fulfillment_berat >= $item->berat) {
                            $tidak_terkirim         = "Terpenuhi";
                        } else if ($item->fulfillment_qty >= $item->qty) {
                            $tidak_terkirim         = "Terpenuhi";
                        } else {
                            $tidak_terkirim         = "Tidak Terpenuhi";
                        }
                    } else {
                        // Jika orderan QTY == 0
                        if ($item->berat <= $item->fulfillment_berat) {
                            $tidak_terkirim         = "Terpenuhi";
                        } else {
                            $tidak_terkirim         = "Tidak Terpenuhi";
                        }
                    }
                }
    
                $data = array(
                    ($urut + 1),
                    $item->no_so,
                    $item->no_do,
                    $item->nama_customer ?? $item->itemorder->nama,
                    $item->nama_marketing ?? $item->nama,
                    $item->sales_channel,
                    $item->tanggal_so,
                    $item->tanggal_kirim,
                    $item->keterangan,
                    $item->sku,
                    $item->nama_detail ?? $item->item_name,
                    $item->part,
                    $item->bumbu,
                    $item->memo,
                    $item->qty,
                    number_format($item->berat,1,',', '.') ?? number_format($item->berat,1,',', '.'),
                    $item->fulfillment_qty ?? $item->bb_berat_item,
                    number_format($item->fulfillment_berat,1,',', '.') ?? number_format($item->bb_berat_total,1,',', '.'),
                    $jenis,
                    $item->tidak_terkirim_catatan,
                    $tidak_terkirim_qty,
                    number_format($tidak_terkirim_berat,1,',', '.'),
                    $tidak_terkirim,
                    $netsuite_status,
                );
                fputcsv($fp, $data);
                $urut++;
            }
        }
        
    }

    public function siapKirimCsvUpdate(Request $request)
    {
        $awal                   = $request->awal ?? date('Y-m-d');
        $akhir                  = $request->akhir ?? date('Y-m-d');
        $jenisTanggal           = $request->jenistanggal;
        $jenisItem              = $request->jenisitem;
        $jenis_export           = $request->jenis ?? "semua";
        $keterangan             = $request->keterangan ?? "";
        $status                 = $request->status ?? "semua";
        $urut                   = 0;
        // dd($keterangan);

        $masterQuery            =  OrderItem::with(['itemorder','bahan_baku','itemorder.marketingnama','itemorder.netsuite_closed'])
                                    ->where(function ($query) use ($jenisTanggal, $awal, $akhir) {
                                        if ($jenisTanggal == 'so') {
                                            $query->whereIn('order_id', Order::select('id')->whereBetween('tanggal_so', [$awal, $akhir]));
                                        } else {
                                            $query->whereIn('order_id', Order::select('id')->whereBetween('tanggal_kirim', [$awal, $akhir]));
                                        }
                                    })
                                    ->where(function($query2) use ($jenisItem) {
                                        if ($jenisItem == 'sampingan') {
                                            $query2->whereIn('item_id', Item::select('id')->where('category_id',[4,6,10,16]));
                                        } else if ($jenisItem == 'nonsampingan') {
                                            $query2->whereNotIn('item_id', Item::select('id')->where('category_id',[4,6,10,16]));
                                        }
                                    })
                                    ->where(function($query3) use ($jenis_export) {
                                        if($jenis_export == 'fresh'){
                                            $query3->where('nama_detail', 'NOT LIKE','%FROZEN%');
                                        }
                                        if($jenis_export == 'frozen'){
                                            $query3->where('nama_detail', 'LIKE','%FROZEN%');
                                            // $query3->orwhere('nama_detail', 'LIKE','%AY - S%');
                                        }
                                    })
                                    ->where(function($query4) use ($status) {
                                        if($status == "0"){
                                            $query4->whereIn('order_id', Order::select('id')->whereNull('status'));
                                        }else
                                        if($status == "1"){
                                            $query4->whereIn('order_id', Order::select('id')->where('status',10));
                                        }else
                                        if($status == "2"){
                                            $query4->whereIn('order_id', Order::select('id')->where('status_so','Closed'));
                                        }
                                        else{
                                            $query4->whereIn('order_id', Order::select('id')->where('status',10));
                                            $query4->orwhereIn('order_id', Order::select('id')->where('status',0));
                                            $query4->orwhereIn('order_id', Order::select('id')->whereNull('status'));
                                        }
                                    })
                                    ->whereIn('order_id', Order::select('id')->where('status',10))
                                    ->orderBy('order_id','ASC')
                                    ->get();
        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=siapkirim-" . $awal . "-" . $akhir . "-" . $jenis_export . ".csv");
        $fp = fopen('php://output', 'w');
        fputcsv($fp, ["sep=,"]);
        // if ($keterangan == NULL || $keterangan == "" || $keterangan == "tidak-terkirim") {
            $data = array(
                "No",
                "No SO",
                "No DO",
                "Nama Customer",
                "Marketing",
                "Channel",
                "Tanggal SO",
                "Tanggal Kirim",
                "Keterangan Header",
                "SKU",
                "Item",
                "Part",
                "Bumbu",
                "Memo",
                "Order Qty",
                "Order Berat",
                "Fulfillment Qty",
                "Fulfillment Berat",
                "Fresh/Frozen",
                "Status Kiriman",
                "Keterangan Tidak Terkirim",
                "Tidak Terkirim Qty",
                "Tidak Terkirim Berat",
                "Status SO"
            );
            fputcsv($fp, $data);
        // }else{
        //     $data = array(
        //         "No",
        //         "No SO",
        //         "No DO",
        //         "Nama Customer",
        //         "Marketing",
        //         "Channel",
        //         "Tanggal SO",
        //         "Tanggal Kirim",
        //         "Keterangan Header",
        //         "SKU",
        //         "Item",
        //         "Part",
        //         "Bumbu",
        //         "Memo",
        //         "Order Qty",
        //         "Order Berat",
        //         "Fulfillment Qty",
        //         "Fulfillment Berat",
        //         "Fresh/Frozen",
        //         "Status Kiriman",
        //         "Status SO"
        //     );
        //     fputcsv($fp, $data);
        // }

        
        foreach($masterQuery as $value){
            $tidak_terkirim_qty             = ($value->qty - $value->fulfillment_qty);
            $tidak_terkirim_berat           = ($value->berat - $value->fulfillment_berat);
            $statusfulfill                  = "";
            $jenis                          = str_contains($value->nama_detail, 'FROZEN') ? "FROZEN" : "FRESH";

        
            if($value->itemorder->status != 0 || $keterangan != 'batal'){
                if ($value->qty != 0 || $value->qty != "") {
                    if ($value->fulfillment_berat >= $value->berat) {
                        $statusfulfill          = "Terpenuhi";
                    } else if ($value->fulfillment_qty >= $value->qty && $value->fulfillment_berat >= $value->berat ) {
                        $statusfulfill          = "Terpenuhi";
                    } else {
                        $statusfulfill          = "Tidak Terpenuhi";
                    }
                } else {
                    // Jika orderan QTY == 0
                    if ($value->berat <= $value->fulfillment_berat) {
                        $statusfulfill          = "Terpenuhi";
                    } else {
                        $statusfulfill          = "Tidak Terpenuhi";
                        $tidak_terkirim_qty     = $tidak_terkirim_qty;
                        $tidak_terkirim_berat   = $tidak_terkirim_berat;
                    }
                }

                if ($tidak_terkirim_qty != "0") {
                    $tidak_terkirim_qty *= (-1);
                }
                if ($tidak_terkirim_berat != "0") {
                    $tidak_terkirim_berat *= (-1);
                }
            }
            else{
                $statusfulfill                  = "Batal";
            }

            // if ($keterangan == NULL || $keterangan == "" || $keterangan == "batal" || $keterangan == "tidak-terkirim" ) {
                $data = [
                    ($urut + 1),
                    $value->itemorder->no_so,
                    $value->itemorder->no_do,
                    $value->itemorder->nama,
                    $value->itemorder->marketingnama->nama ?? "#",
                    $value->itemorder->sales_channel,
                    $value->itemorder->tanggal_so,
                    $value->itemorder->tanggal_kirim,
                    $value->itemorder->keterangan,
                    $value->sku,
                    $value->nama_detail,
                    $value->part,
                    $value->bumbu,
                    $value->memo,
                    $value->qty,
                    number_format($value->berat,2,',', '.'),
                    $value->fulfillment_qty,
                    number_format($value->fulfillment_berat,2,',', '.'),
                    $jenis,
                    $statusfulfill,
                    $value->tidak_terkirim_catatan,
                    $tidak_terkirim_qty,
                    number_format($tidak_terkirim_berat,2,',', '.'),
                    $value->itemorder->netsuite_closed->netsuite_closed_status ?? $value->itemorder->status_so
                ];
                fputcsv($fp, $data);
                $urut++;
            // }
            // if($keterangan == 'terkirim'){
            //     $data = [
            //         ($urut + 1),
            //         $value->itemorder->no_so,
            //         $value->itemorder->no_do,
            //         $value->itemorder->nama,
            //         $value->marketingnama->nama,
            //         $value->itemorder->sales_channel,
            //         $value->itemorder->tanggal_so,
            //         $value->itemorder->tanggal_kirim,
            //         $value->itemorder->keterangan,
            //         $value->sku,
            //         $value->nama_detail,
            //         $value->part,
            //         $value->bumbu,
            //         $value->memo,
            //         $value->qty,
            //         number_format($value->berat,2,',', '.'),
            //         $value->fulfillment_qty,
            //         number_format($value->fulfillment_berat,2,',', '.'),
            //         $jenis,
            //         $statusfulfill,
            //         $value->itemorder->netsuite_closed->netsuite_closed_status ?? $value->itemorder->status_so

            //     ];
            //     fputcsv($fp, $data);
            //     $urut++;
            // }
        } 
    }
}
