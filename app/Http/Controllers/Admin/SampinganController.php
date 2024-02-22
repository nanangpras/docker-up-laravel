<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppKey;
use App\Models\Bahanbaku;
use App\Models\Bom;
use App\Models\Category;
use App\Models\Chiller;
use App\Models\Evis;
use App\Models\FreestockTemp;
use App\Models\Gudang;
use App\Models\Item;
use App\Models\Netsuite;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemLog;
use App\Models\Product_gudang;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SampinganController extends Controller
{

    public function index(Request $request)
    {
        if(User::setIjin(13) || User::setIjin(26)){
            if ($request->key == 'unduh') {

                header("Content-type: application/csv");
                header("Content-Disposition: attachment; filename=sampingan-" . $request->tanggal . ".csv");
                $fp = fopen('php://output', 'w');
                fputcsv($fp, ["sep=,"]);

                $data = array(
                        "No",
                        "Nomor SO",
                        "Sales Channel",
                        "Tanggal SO",
                        "Tanggal Kirim",
                        "Nama",
                        "Alamat Kirim",
                        "Keterangan",
                        "Item",
                        "SKU",
                        "Order Item",
                        "Order Berat",
                        "Fulfillment Item",
                        "Fulfillment Berat"
                    );
                fputcsv($fp, $data);

                $fulfillment    =   OrderItem::whereIn('order_id', Order::select('id')->whereDate('tanggal_so', $request->tanggal))
                                    ->get();

                foreach ($fulfillment as $i => $full) :
                    $data = array(
                        $i + 1,
                        $full->itemorder->no_so,
                        $full->itemorder->sales_channel,
                        $full->itemorder->tanggal_so,
                        $full->itemorder->tanggal_kirim,
                        $full->itemorder->nama,
                        $full->itemorder->alamat_kirim,
                        $full->itemorder->keterangan,
                        $full->line_id . '. ' . $full->nama_detail . ($full->memo ? " (" . $full->memo . ")" : ""),
                        $full->item->sku,
                        str_replace(".", ",", $full->qty),
                        str_replace(".", ",", $full->berat),
                        str_replace(".", ",", $full->fulfillment_qty),
                        str_replace(".", ",", $full->fulfillment_berat),
                    );
                    fputcsv($fp, $data);
                endforeach;

                fclose($fp);

                return "";

            } else {

                $regu       = strtolower($request->regu);
                $customer   = $request->customer ?? "";
                $search     = $request->search ?? "";
                $key        = $request->key ?? "";

                $tanggal        =   $request->tanggal ?? date('Y-m-d');
                $kategori       = [1,2,3,5,7,8,9,13];

                return view('admin.pages.sampingan.index', compact('tanggal', 'kategori', 'customer', 'search'));
            }
        }
        return redirect()->route("index");
    }

    public function store(Request $request)
    {
        if(User::setIjin(13) || User::setIjin(26)){
            $datchill                    =  Chiller::find($request->chiller);
            $datchill->stock_item        =  ($datchill->stock_item - $request->qty);
            $datchill->stock_berat       =  ($datchill->stock_berat - $request->berat);
            $datchill->save();

            $data                        =   OrderItem::find($request->item);

            $item                        =   Item::find($data->item_id);

            $category                    =   Category::find($item->category_id);

            $chiller                     =  new Chiller;
            $chiller->production_id      =  $datchill->production_id;
            $chiller->table_name         =  'order_item';
            $chiller->table_id           =  $data->id;
            $chiller->asal_tujuan        =  'sampingan';
            $chiller->no_mobil           =  $datchill->no_mobil;
            $chiller->item_id            =  $datchill->item_id;
            $chiller->item_name          =  $datchill->item_name;
            $chiller->tanggal_produksi   =  Carbon::now();
            $chiller->jenis              =  'keluar';
            $chiller->type               =  'pengambilan-bahan-baku';
            $chiller->qty_item           =  $request->qty;
            $chiller->berat_item         =  $request->berat;
            $chiller->status             =  3;
            $chiller->save();

            $item                       =   OrderItem::find($request->item);
            $item->kr_selesai           =   Carbon::now();
            $item->status               =   3;
            $item->save();

            $bahanbaku                  =   new Bahanbaku;
            $bahanbaku->chiller_alokasi      =   $request->chiller;
            $bahanbaku->order_id        =   $request->order;
            $bahanbaku->order_item_id   =   $data->id;
            $bahanbaku->bb_item         =   $request->qty;
            $bahanbaku->bb_berat        =   $request->berat;
            $bahanbaku->save();

            $log_item                   =   new OrderItemLog;
            $log_item->activity         =   "sampingan-bahanbaku-proses";
            $log_item->order_item_id    =   $data->id;
            $log_item->user_id          =   Auth::user()->id;
            $log_item->key              =   AppKey::generate();
            $log_item->save();
        }
        return redirect()->route("index");
    }

    public function show($id)
    {
        if(User::setIjin(13) || User::setIjin(26)){
            //
        }
        return redirect()->route("index");
    }

    public function sampinganChiller(Request $request){
        $tanggal = $request->tanggal ?? date('Y-m-d');
        $item_id = $request->item_id ?? "";


        $sql     =   Chiller::whereIn('asal_tujuan', ['evisgabungan', 'retur', 'open_balance', 'thawing', 'gradinggabungan'])
                        ->where('jenis', 'masuk')
                        ->whereIn('type', ['hasil-produksi', 'bahan-baku'])
                        ->where('stock_berat', '>', 0)
                        ->where('status_cutoff', NULL)
                        ->where('item_id', $item_id)
                        ->whereDate('tanggal_produksi', $tanggal);
        
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
            $total_qty_alokasi          = 0;
            $total_berat_alokasi        = 0;
            $total_keranjang            = 0;
            $total_qty_abf              = 0;
            $total_berat_abf            = 0;
            $total_qty_freestock        = 0;
            $total_berat_freestock      = 0;
            $total_qty_musnahkan        = 0;
            $total_berat_musnahkan      = 0;

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
                    $total_qty_freestock   = $valbb->total_qty_freestock;
                    $total_berat_freestock  = floatval($valbb->total_berat_freestock) ?? 0;
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
        // dd($produk);         
        // ->get();

        return view('admin.pages.sampingan.chiller-sampingan', compact('produk', 'item_id'));
    }

    public function datashow(Request $request)
    {
        $tanggal    =   $request->tanggal ?? date('Y-m-d');
        $search     =   $request->search ?? "";
        $customer   =   $request->customer ?? "";
        $key        =   $request->key ?? "";

        $summary    =   Chiller::whereIn('asal_tujuan', ['jualsampingan','open_balance'])->where('type', 'hasil-produksi')->get();
        $evis       =   Evis::where('peruntukan', 'evissampingan')->get();

        return view('admin.pages.sampingan.show', compact('summary','evis','tanggal', 'customer', 'search', 'key'));

    }

    public function order(Request $request)
    {

            $regu       =   strtolower($request->regu);
            $customer   =   $request->customer ?? "";
            $search     =   $request->search ?? "";
            $key        =   $request->key ?? "";
            $tanggal    =   $request->tanggal ?? date('Y-m-d');
            $kategori   =   [1,2,3,5,7,8,9,13];

            $sampingan  =   Order::whereIn('sales_channel', ["By Product - Paket", "By Product - Retail"])
                            ->whereDate('tanggal_so', $tanggal)
                            ->where(function($query) use ($customer, $search, $key) {
                                if ($customer) {
                                    $query->where('customer_id', $customer);
                                }
                                if ($search) {
                                    $query->orWhere('nama', 'like', '%'.$search.'%');
                                    $query->orWhere('no_so', 'like', '%'.$search.'%');
                                    $query->orWhere('keterangan', 'like', '%'.$search.'%');
                                    $query->orWhere('sales_channel', 'like', '%'.$search.'%');
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

            $semua_order    =   Order::whereIn('sales_channel', ["By Product - Paket", "By Product - Retail"])
                                ->whereDate('tanggal_so', $tanggal)
                                ->orderBy('id', 'desc')
                                ->count();

            $selesai_order  =   Order::whereIn('sales_channel', ["By Product - Paket", "By Product - Retail"])
                                ->whereDate('tanggal_so', $tanggal)
                                ->orderBy('id', 'desc')
                                ->where('status', '10')
                                ->count();

            $pending_order  =   Order::whereIn('sales_channel', ["By Product - Paket", "By Product - Retail"])
                                ->whereDate('tanggal_so', $tanggal)
                                ->orderBy('id', 'desc')
                                ->whereNull('status')
                                ->count();

            $status_order   =   [
                'semua_order'   =>  $semua_order ,
                'selesai_order' =>  $selesai_order ,
                'pending_order' =>  $pending_order ,
            ] ;

            return view('admin.pages.sampingan.jual-sampingan', compact('sampingan', 'tanggal', 'customer', 'search', 'status_order', 'key'));

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

                if($proses_ambil=="chillerfg"){

                    $chiller                        =   Chiller::find($request->x_code[$x]);

                    // if ($request->berat[$x] > $chiller->stock_berat) {

                    //     $data['status'] =   400 ;
                    //     $data['msg']    =   'Pengambilan Lebih Besar dari Stock' ;
                    //     return $data ;
                    // }

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
                        DB::rollBack() ;
                        $data['status'] =   400 ;
                        $data['msg']    =   'Terjadi kesalahan proses order' ;
                        return $data ;
                    }

                }elseif($proses_ambil=="frozen"){

                    $storage                        =   Product_gudang::find($request->x_code[$x]);

                    // if ($request->berat[$x] > $storage->berat) {

                    //     $data['status'] =   400 ;
                    //     $data['msg']    =   'Pengambilan Lebih Besar dari Stock' ;
                    //     return $data ;
                    // }

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

                    $orderBahanBaku                     =   new Bahanbaku();
                    $orderBahanBaku->chiller_out        =   $storage->id;
                    $orderBahanBaku->order_id           =   $request->order_id;
                    $orderBahanBaku->nama               =   $storage->productitems->nama;
                    $orderBahanBaku->proses_ambil       =   $proses_ambil;
                    $orderBahanBaku->order_item_id      =   $request->order_item_id;
                    $orderBahanBaku->bb_item            =   $request->qty[$x];
                    $orderBahanBaku->bb_berat           =   $request->berat[$x];
                    $orderBahanBaku->status             =   1;
                    if (!$orderBahanBaku->save()) {
                        DB::rollBack() ;
                        $data['status'] =   400 ;
                        $data['msg']    =   'Terjadi kesalahan proses order' ;
                        return $data ;
                    }

                }elseif($proses_ambil=="sampingan"){
                    $chiller                        =   Chiller::find($request->x_code[$x]);

                    // if ($request->berat[$x] > $chiller->stock_berat) {

                    //     $data['status'] =   400 ;
                    //     $data['msg']    =   'Pengambilan Lebih Besar dari Stock' ;
                    //     return $data ;
                    // }

                    
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
                        DB::rollBack() ;
                        $data['status'] =   400 ;
                        $data['msg']    =   'Terjadi kesalahan proses order' ;
                        return $data ;
                    }
                }

            }
        }

        DB::commit();
        $data['status'] =   200 ;
        $data['msg']    =   'Data telah diproses' ;
        return $data ;

    }

    public function pemenuhanAlokasi(Request $request)
    {
        $order_item_id  =   $request->order_item_id;

        if ($request->key == 'fg') {
            $data   =   OrderItem::find($order_item_id) ;

            $fgood  =   Chiller::whereIn('table_id', FreestockTemp::select('id')->where('customer_id', $data->itemorder->customer_id))
                        ->where('table_name', 'free_stocktemp')
                        ->where('item_id', $data->item_id)
                        ->where('stock_berat', '>', 0)
                        ->get();

            return view('admin.pages.sampingan.data_finishedgood', compact('fgood')) ;
        } else

        if ($request->key == 'info') {
            $order  =   OrderItem::find($order_item_id);
            $qty    =   Bahanbaku::where('order_item_id', $order_item_id)->sum('bb_item');
            $berat  =   Bahanbaku::where('order_item_id', $order_item_id)->sum('bb_berat');
            return view('admin.pages.sampingan.info_order', compact('order', 'qty', 'berat')) ;
        } else {
            $pemenuhan = Bahanbaku::where('order_item_id', $order_item_id)->get();
            return view('admin.pages.sampingan.jual-sampingan-pemenuhan', compact('pemenuhan'));
        }

    }

    public function fulfillItem(Request $request){

        return OrderItem::fulfillItem($request->order_item_id);

    }

    public function closeOrder(Request $request){
        $order_id   = $request->order_id;
        $order      =   Order::find($order_id);

        if($order){
            foreach($order->daftar_order_full as $row){
                if ($row->order_item_bb != null) {
                    if ($row->order_item_bb->status == 1) {

                        return back()->with('status', 2)->with('message', 'Data Item Belum Tersimpan');
                    }
                }
            }

            // close order jual sampingan
            $order->status = 10;
            $order->save();

            $net_fulfill = Netsuite::item_fulfill_sampingan('orders', $request->order_id, 'itemfulfill', null, null);

            foreach($order->daftar_order_full as $row){
                if($row->status==3){
                    $row->status = 2;
                    $row->save();
                }
            }

            return back()->with('status', 1)->with('message', 'Penyelesaian berhasil');;
        }

    }

    public function deleteAlokasi(Request $request){
        $pemenuhan = Bahanbaku::find($request->id);
        $pemenuhan->delete();
        $data['status'] =   200 ;
        $data['msg']    =   'Data telah diproses' ;
        return $data ;
    }

    public function summary(Request $request)
    {
        if (User::setIjin(13) || User::setIjin(26)) {
            $tanggal    =   $request->tanggal ?? date('Y-m-d') ;
            $search     =   $request->search ?? '' ;

            $summary    =   Chiller::where('asal_tujuan', 'jualsampingan')
                            ->leftJoin('order_bahan_baku', 'order_bahan_baku.id', '=', 'chiller.table_id')
                            ->leftJoin('orders', 'orders.id', '=', 'order_bahan_baku.order_id')
                            ->where('chiller.type', 'alokasi-order')
                            ->whereDate('tanggal_produksi', $tanggal)
                            ->where(function($query) use ($search) {
                                $query->orWhere('chiller.item_name', 'LIKE', "%" . $search . "%") ;
                                $query->orWhere('orders.nama', 'LIKE', "%" . $search . "%") ;
                            })
                            ->get();

            return view('admin.pages.sampingan.summary', compact('summary'));
        }
        return redirect()->route("index");
    }

    public function storejualsampingan(Request $request)
    {
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

                        $qty[$x] = 0;
                        // DB::rollBack() ;
                        // $data['status'] =   400 ;
                        // $data['msg']    =   'Qty tidak boleh kosong' ;
                        // return $data ;
                    }

                    $total  +=  1 ;

                    $chiler                     =   new Bahanbaku;
                    $chiler->chiller_out        =   $item[$x];
                    $chiler->order_id           =   $xcode[$x];
                    $chiler->order_item_id      =   $order[$x];
                    $chiler->bb_item            =   $qty[$x];
                    $chiler->bb_berat           =   $berat[$x];
                    $chiler->status             =   1;
                    if (!$chiler->save()) {
                        DB::rollBack() ;
                        $data['status'] =   400 ;
                        $data['msg']    =   'Terjadi kesalahan proses order' ;
                        return $data ;
                    }

                    $bahan                      =   Bahanbaku::where('chiller_out', $item[$x])->where('order_id', $xcode[$x])->where('order_item_id', $order[$x])->first();
                    $cekchiller                 =   Chiller::find($bahan->chiller_out);

                    $chiler                     =   new Chiller;
                    $chiler->table_name         =   'order_bahanbaku';
                    $chiler->table_id           =   $bahan->id;
                    $chiler->asal_tujuan        =   'jualsampingan';
                    $chiler->item_id            =   $bahan->orderitem->item_id;
                    $chiler->item_name          =   $bahan->orderitem->nama_detail;
                    $chiler->qty_item           =   $qty[$x];
                    $chiler->berat_item         =   $berat[$x];
                    $chiler->jenis              =   'keluar';
                    $chiler->type               =   'hasil-produksi';
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

                    $data_order = Order::find($xcode[$x]);

                    if($chiler->chillitem->sku!="1310000002" && $chiler->chillitem->sku!= '300800A002'){
                        if($cekchiller->type=="hasil-produksi"){
                            $net[]  =   [
                                "nama_tabel"    =>  "chiller" ,
                                "id_tabel"      =>  $chiler->id ,
                                "document_code" =>  $data_order->no_so ?? $order[$x],
                                "label"         =>  "ti_finishedgood_ekspedisi" ,
                                "id_location"   =>  Gudang::gudang_netid($nama_gudang_fg) ,
                                "location"      =>  $nama_gudang_fg ,
                                "from"          =>  Gudang::gudang_netid($nama_gudang_fg) ,
                                "to"            =>  Gudang::gudang_netid($nama_gudang_expedisi) ,
                                "transfer"      =>  [
                                    [
                                        "internal_id_item"  =>  (string)$chiler->chillitem->netsuite_internal_id ,
                                        "item"              =>  (string)$chiler->chillitem->sku ,
                                        "qty_to_transfer"   =>  (string)$berat[$x]
                                    ]
                                ]
                            ] ;
                        }else{
                            $net[]  =   [
                                "nama_tabel"    =>  "chiller" ,
                                "id_tabel"      =>  $chiler->id ,
                                "document_code" =>  $order->no_so ?? $order[$x],
                                "label"         =>  "ti_bb_ekspedisi" ,
                                "id_location"   =>  Gudang::gudang_netid($nama_gudang_bb) ,
                                "location"      =>  $nama_gudang_bb ,
                                "from"          =>  Gudang::gudang_netid($nama_gudang_bb) ,
                                "to"            =>  Gudang::gudang_netid($nama_gudang_expedisi) ,
                                "transfer"      =>  [
                                    [
                                        "internal_id_item"  =>  (string)$chiler->chillitem->netsuite_internal_id ,
                                        "item"              =>  (string)$chiler->chillitem->sku ,
                                        "qty_to_transfer"   =>  (string)$berat[$x]
                                    ]
                                ]
                            ] ;
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

                    $so                         =   Order::find($xcode[$x]);
                    $so->status                 =   5;
                    if (!$so->save()) {
                        DB::rollBack() ;
                        $data['status'] =   400 ;
                        $data['msg']    =   'Terjadi kesalahan proses order' ;
                        return $data ;
                    }

                    $oritem                     =   OrderItem::find($order[$x]);
                    $oritem->fulfillment_berat  =   $berat[$x];
                    $oritem->fulfillment_qty    =   $qty[$x];
                    $oritem->status             =   1;
                    if (!$oritem->save()) {
                        DB::rollBack() ;
                        $data['status'] =   400 ;
                        $data['msg']    =   'Terjadi kesalahan proses order' ;
                        return $data ;
                    }
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
                Netsuite::transfer_inventory_doc($net[$x]['nama_tabel'], $net[$x]['id_tabel'], $net[$x]['label'], $net[$x]['id_location'], $net[$x]['location'], $net[$x]['from'], $net[$x]['to'], $net[$x]['transfer'], NULL, date('Y-m-d'), $net[$x]['document_code']) ;
            }

        // }
        // return redirect()->route("index");
    }

    public function bahanbaku(Request $request)
    {
        // if(User::setIjin(13) || User::setIjin(26)){
            $chiller =  Chiller::where('jenis', 'masuk')->where('table_name', 'evis')->where('stock_item', '>', 0)->orderBy('item_id', 'ASC')->get();
            $detail  =  Order::find($request->customer);
            $item    =  OrderItem::find($request->item);
            $bahan   =  Bahanbaku::select(DB::raw("SUM(bb_berat) AS jml_berat"), DB::raw("SUM(bb_item) AS jml_item"))
                        ->where('order_id', $request->customer)
                        ->where('order_item_id', $request->item)
                        ->first();
            return view('admin.pages.sampingan.proses', compact('chiller', 'detail', 'item', 'bahan'));
        // }
        // return redirect()->route("index");
    }

    public function batalorder(Request $request)
    {
        $order              =   Order::find($request->id);
        if ($request->key == 'close') {
            // Close Order
            $order->status = $order->status == 0 ? 6 : 0;
            $order->save();
            $data['status'] =   400;
            $data['msg']    =   'Berhasil Close Order';
            return $data;
        } else {
            // Batalkan Fulfill
            $order->status = 6;
            $order->save();
            $data['status'] =   400;
            $data['msg']    =   'Berhasil Batalkan Fulfill';
            return $data;
        }

    }
}
