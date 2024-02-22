<?php

namespace App\Http\Controllers\Admin;

use App\Classes\Applib;
use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Chiller;
use App\Models\Customer;
use App\Models\Freestock;
use App\Models\FreestockTemp;
use App\Models\Item;
use App\Models\LaporanRendemen;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product_gudang;
use App\Models\Production;
use App\Models\Purchasing;
use App\Models\Retur;
use App\Models\Supplier;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {

        if ($request->key == 'setSubsidiary') {
            if ($request->user != '' && $request->subsidiary != '') {

                $user                       = $request->user;
                $newSubsidiary              = $request->subsidiary;

                $findUser                   = User::find($user);

                if ($findUser) {
                    $findUser->company_id   = $newSubsidiary == 'CGL' ? '1' : '2';
                    $findUser->save();
                    return response()->json([
                        'status'    => 200,
                        'message'   => 'success',
                        'data'      => $findUser
                    ]);
                } else {
                    return response()->json([
                        'status'    => 400,
                        'message'   => 'error',
                    ]);
                }

            } else {
                $subsidiary = $request->value;
                $updateSubsidiary = User::find(Auth::user()->id);
                if ($updateSubsidiary) {
                    $updateSubsidiary->company_id = $subsidiary == 'CGL' ? '1' : '2';
                    $updateSubsidiary->save();
                    
                    Session::put('subsidiary', $subsidiary);
                    Session::put('subsidiary_id', $subsidiary == 'CGL' ? '1' : '2');
    
                    return redirect()->back()->with('status', 1)->with('message', 'Berhasil Mengganti Subsidiary');
                } else {
                    return redirect()->back()->with('status', 2)->with('message', 'Gagal Mengganti Subsidiary');
                }
            }

        } else {

            if(env('APP_CODE')=="cloud"){
                return redirect('report/laporan-dashboard');
            }
    
            if (User::setIjin('superadmin') or User::setIjin(7)):
    
            $tanggal    =   $request->tanggal ?? date('Y-m-d');
            $count_purchase = Purchasing::where('tanggal_potong', $tanggal)->count();
            $count_production = Production::whereIn('purchasing_id', Purchasing::select('id')->where('tanggal_potong', $tanggal))->count();
            $count_customer = Customer::count();
            $count_supplier = Supplier::count();
            $count_so = Order::where('tanggal_so', $tanggal)->count();
            $count_so_pending = Order::where('tanggal_so', $tanggal)->where('status', '1')->count();
            $count_so_kp = Order::where('tanggal_so', $tanggal)->where('status', '2')->count();
            $count_so_kr = Order::where('tanggal_so', $tanggal)->where('status', '3')->count();
            $count_so_chiller = Order::where('tanggal_so', $tanggal)->where('status', '4')->count();
            $count_so_ekspedisi = Order::where('tanggal_so', $tanggal)->where('status', '5')->count();
            $count_so_loading = Order::where('tanggal_so', $tanggal)->where('status', '6')->count();
            $count_so_pengantaran = Order::where('tanggal_so', $tanggal)->where('status', '7')->count();
            $count_so_qc = Order::where('tanggal_so', $tanggal)->where('status', '8')->count();
            $count_so_selesai = Order::where('tanggal_so', $tanggal)->where('status', '9')->count();
    
            $top_item = OrderItem::whereIn('order_id', Order::select('id')->where('tanggal_so', $tanggal))
                        ->leftJoin('items', 'items.id', '=', 'order_items.item_id')
                        ->groupBy('order_items.item_id')
                        ->selectRaw('sum(order_items.qty) as total_qty, items.nama, order_items.item_id')
                        ->orderBy('total_qty', 'DESC')->limit(10)
                        ->get();
    
            return view('admin/pages/dashboard')
                    ->with('count_production', $count_production)
                    ->with('count_customer', $count_customer)
                    ->with('count_supplier', $count_supplier)
                    ->with('top_item', $top_item)
                    ->with('count_so', $count_so)
                    ->with('count_so_pending', $count_so_pending)
                    ->with('count_so_kp', $count_so_kp)
                    ->with('count_so_kr', $count_so_kr)
                    ->with('count_so_chiller', $count_so_chiller)
                    ->with('count_so_ekspedisi', $count_so_ekspedisi)
                    ->with('count_so_loading', $count_so_loading)
                    ->with('count_so_pengantaran', $count_so_pengantaran)
                    ->with('count_so_qc', $count_so_qc)
                    ->with('count_so_selesai', $count_so_selesai)
                    ->with('count_purchase', $count_purchase)
                    ->with('tanggal', $tanggal);
            endif;
    
            
    
            if(User::setIjin(1)){
                return redirect()->route("purchasing.index");
            }
            if(User::setIjin(2)){
                return redirect()->route("security.index");
            }
            if(User::setIjin(3)){
                return redirect()->route("lpah.index");
            }
            if(User::setIjin(4)){
                return redirect()->route("qc.index");
            }
            if(User::setIjin(5)){
                return redirect()->route("grading.index");
            }
            if(User::setIjin(6)){
                return redirect()->route("evis.index");
            }
            if(User::setIjin(7)){
                return redirect()->route("kepalaproduksi.index");
            }
            if(User::setIjin(8) || User::setIjin(9) || User::setIjin(10) || User::setIjin(11) || User::setIjin(12)){
                return redirect()->route("regu.index");
            }
            if(User::setIjin(13)){
                return redirect()->route("chiller.index");
            }
            if(User::setIjin(14)){
                return redirect()->route("hasil-produksi.index");
            }
            if(User::setIjin(15)){
                return redirect()->route("warehouse.index");
            }
            if(User::setIjin(16)){
                return redirect()->route("abf.index");
            }
            if(User::setIjin(18)){
                return redirect()->route("supplier.index");
            }
            if(User::setIjin(19)){
                return redirect()->route("driver.index");
            }
            if(User::setIjin(25)){
                return redirect()->route("index.netsuite");
            }
            if(User::setIjin(23)){
                return redirect()->route("marketing.index");
            }
            if(User::setIjin(26)){
                return redirect()->route("fulfillment.index");
            }
            if(User::setIjin(27)){
                return redirect()->route("fulfillment.index");
            }
            if(User::setIjin(30)){
                return redirect()->route("ekspedisi.index");
            }
            if(User::setIjin(31)){
                return redirect('admin/fulfillment?search=meyer');
            }
            if(User::setIjin(32)){
                return redirect('admin/laporanadmin');
            }
    
            if (User::setIjin(38) || User::setIjin(40) || User::setIjin(41)) {
                return redirect()->route('buatso.index');
            }
    
            if (User::setIjin(35) || User::setIjin(42) || User::setIjin(43)) {
                return redirect()->route('pembelian.index');
            }

        }


    }

    public function dashboardatas(Request $request)
    {
        if (User::setIjin('superadmin')):

            $tanggal    =   $request->tanggal ?? date('Y-m-d');
            $count_purchase = Purchasing::where('tanggal_potong', $tanggal)->count();
            $count_production = Production::whereIn('purchasing_id', Purchasing::select('id')->where('tanggal_potong', $tanggal))->count();
            $count_customer = Customer::count();
            $count_supplier = Supplier::count();
            $count_so = Order::where('tanggal_so', $tanggal)->count();
            $count_so_pending = Order::where('tanggal_so', $tanggal)->where('status', '1')->count();
            $count_so_kp = Order::where('tanggal_so', $tanggal)->where('status', '2')->count();
            $count_so_kr = Order::where('tanggal_so', $tanggal)->where('status', '3')->count();
            $count_so_chiller = Order::where('tanggal_so', $tanggal)->where('status', '4')->count();
            $count_so_ekspedisi = Order::where('tanggal_so', $tanggal)->where('status', '5')->count();
            $count_so_loading = Order::where('tanggal_so', $tanggal)->where('status', '6')->count();
            $count_so_pengantaran = Order::where('tanggal_so', $tanggal)->where('status', '7')->count();
            $count_so_qc = Order::where('tanggal_so', $tanggal)->where('status', '8')->count();
            $count_so_selesai = Order::where('tanggal_so', $tanggal)->where('status', '9')->count();

            $top_item = OrderItem::whereIn('order_id', Order::select('id')->where('tanggal_so', $tanggal))
                        ->leftJoin('items', 'items.id', '=', 'order_items.item_id')
                        ->groupBy('order_items.item_id')
                        ->selectRaw('sum(order_items.qty) as total_qty, items.nama, order_items.item_id')
                        ->orderBy('total_qty', 'DESC')->limit(10)
                        ->get();

            return view('admin.pages.laporan.dashboardatas')
                    ->with('count_production', $count_production)
                    ->with('count_customer', $count_customer)
                    ->with('count_supplier', $count_supplier)
                    ->with('top_item', $top_item)
                    ->with('count_so', $count_so)
                    ->with('count_so_pending', $count_so_pending)
                    ->with('count_so_kp', $count_so_kp)
                    ->with('count_so_kr', $count_so_kr)
                    ->with('count_so_chiller', $count_so_chiller)
                    ->with('count_so_ekspedisi', $count_so_ekspedisi)
                    ->with('count_so_loading', $count_so_loading)
                    ->with('count_so_pengantaran', $count_so_pengantaran)
                    ->with('count_so_qc', $count_so_qc)
                    ->with('count_so_selesai', $count_so_selesai)
                    ->with('count_purchase', $count_purchase)
                    ->with('tanggal', $tanggal);
            endif;

    }

    public function new_chat(){

        if(Auth::user()){
            $user_id    = Auth::user()->id;
            $new_msg    = Chat::where('receiver_id', $user_id)->where('status', '1')->count();

            return "<span class='badge badge-danger'> ".$new_msg." </span>" ?? "";
        }

    }

    public function read_chat($sender){

        if(Auth::user()){

            $user_id    = Auth::user()->id;
            $new_msg    = Chat::where('receiver_id', $user_id)->where('sender_id', $sender)->where('status', '1')->get();

            $u_sender   = User::find($sender);

            foreach($new_msg as $new){
                $new->status = '2';
                $new->save();
            }

            $user_id    = Auth::user()->id;
            $chat       =   Chat::where(function($q) use($user_id, $sender){
                                $q->where('receiver_id', $user_id)
                                ->where('sender_id', $sender);
                            })
                            ->orWhere(function($q) use($user_id, $sender) {
                                $q->where('sender_id', $user_id)
                                ->where('receiver_id', $sender);
                            })

                            ->orderBy('id', 'asc')
                            ->limit(100)
                            ->get();
            return view('admin.pages.chat.chat-detail', compact('chat', 'u_sender'));

        }

    }

    public function chat(Request $request)
    {

        if(Auth::user()){

            $user_id    = Auth::user()->id;
            $data       =   Chat::where('sender_id', $user_id)
                            ->orWhere('receiver_id', $user_id)
                            ->orderBy('id', 'desc')
                            ->get();

            $new_msg    = Chat::where('receiver_id', $user_id)->whereIn('status', [1,2])->count();

            $lawan_bicara = [];

            $lawan_bicara = array();

            foreach ($data as $key) {

                if($key->sender_id !== $user_id){

                    if(!in_array($key->sender_id, $lawan_bicara)){

                        $lawan_bicara[] = $key->sender_id;
                    }
                }

                if($key->receiver_id !== $user_id){

                    if(!in_array($key->receiver_id, $lawan_bicara)){

                        $lawan_bicara[] = $key->receiver_id;
                    }

                }

            }

            return view('admin.pages.chat.riwayat', compact('lawan_bicara', 'new_msg'));

        }
    }

    public function sendchat(Request $request)
    {

        if($request->msg !="" && $request->pengguna!=""){
            $pesan              =   new Chat ;
            $pesan->content     =   $request->msg ;
            $pesan->sender_id   =   Auth::user()->id ;
            $pesan->receiver_id =   $request->pengguna ;
            $pesan->status      =   1 ;
            $pesan->save() ;
        }
    }

    public function laporan_stock(Request $request)
    {
        $mulai  =   $request->mulai ?? date("Y-m-d");
        $akhir  =   $request->akhir ?? date("Y-m-d");

        if ($request->key == 'laporan_alokasi') {
            $alokasi    =   Chiller::select("item_id", "item_name", "jenis", "type", "tanggal_produksi", DB::raw("round(sum(qty_item), 2) AS qty"), DB::raw("round(sum(berat_item), 2) AS berat"))
                            ->where('type', 'alokasi-order')
                            ->whereBetween('tanggal_produksi', [$mulai, $akhir])
                            ->groupBy('item_id', 'item_name', 'jenis', 'type', 'tanggal_produksi')
                            ->get();

            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=Data Stock Alokasi-" . $mulai . "_" . $akhir . ".csv");
            $fp = fopen('php://output', 'w');
            fputcsv($fp, ["sep=,"]);

            $data = array(
                "No",
                "Tanggal Produksi",
                "Item",
                "SKU",
                "Jenis",
                "Tipe",
                "Qty",
                "Berat",
            );
            fputcsv($fp, $data);

            foreach ($alokasi as $i => $row) :
                $data = array(
                    $i + 1,
                    $row->tanggal_produksi,
                    $row->item_name,
                    Item::find($row->item_id)->sku,
                    $row->jenis,
                    $row->type,
                    str_replace(".", ",", $row->qty),
                    str_replace(".", ",", $row->berat),
                );
                fputcsv($fp, $data);
            endforeach;

            fclose($fp);

            return "";
        } else

        if ($request->key == 'laporan_ambilbb') {
            $ambilbb =   Chiller::select("item_id", "item_name", "jenis", "type", "tanggal_produksi", DB::raw("round(sum(qty_item), 2) AS qty"), DB::raw("round(sum(berat_item), 2) AS berat"), DB::raw("round(sum(stock_item), 2) AS stock_qty"), DB::raw("round(sum(stock_berat), 2) AS stock_berat"))
            ->where("jenis", "keluar")
            ->where('type', "pengambilan-bahan-baku")
            ->whereBetween('tanggal_produksi', [$mulai, $akhir])
            ->groupBy('item_id', 'item_name', 'jenis', 'type', 'tanggal_produksi')
            ->orderBy('item_id')
            ->get();

            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=Data Stock Ambil Bahan Baku-" . $mulai . "_" . $akhir . ".csv");
            $fp = fopen('php://output', 'w');
            fputcsv($fp, ["sep=,"]);

            $data = array(
                "No",
                "Tanggal Produksi",
                "Item",
                "SKU",
                "Jenis",
                "Tipe",
                "Qty Masuk",
                "Berat Masuk",
                "Qty Stock",
                "Berat Stock",
            );
            fputcsv($fp, $data);

            foreach ($ambilbb as $i => $row) :
                $data = array(
                    $i + 1,
                    $row->tanggal_produksi,
                    $row->item_name,
                    Item::find($row->item_id)->sku,
                    $row->jenis,
                    $row->type,
                    str_replace(".", ",", $row->qty),
                    str_replace(".", ",", $row->berat),
                    str_replace(".", ",", $row->stock_qty),
                    str_replace(".", ",", $row->stock_berat),
                );
                fputcsv($fp, $data);
            endforeach;

            fclose($fp);

            return "";
        } else

        if ($request->key == 'bahan_baku') {
            $bahanbaku  =   Chiller::select("item_id", "item_name", "tanggal_produksi", "jenis", "type", "asal_tujuan", DB::raw("round(sum(qty_item), 2) AS qty"), DB::raw("round(sum(berat_item), 2) AS berat"), DB::raw("round(sum(stock_item), 2) AS stock_qty"), DB::raw("round(sum(stock_berat), 2) AS stock_berat"))
            ->where('jenis', 'masuk')
            ->where('type', 'bahan-baku')
            ->whereBetween('tanggal_produksi', [$mulai, $akhir])
            ->groupBy("item_id", "item_name", "jenis", "type", "tanggal_produksi", "asal_tujuan")
            ->orderBy('item_id')
            ->get();

            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=Data Stock Bahan Baku-" . $mulai . "_" . $akhir . ".csv");
            $fp = fopen('php://output', 'w');
            fputcsv($fp, ["sep=,"]);

            $data = array(
                "No",
                "Tanggal Produksi",
                "Item",
                "SKU",
                "Jenis",
                "Tipe",
                "Qty Masuk",
                "Berat Masuk",
                "Qty Stock",
                "Berat Stock",
            );
            fputcsv($fp, $data);

            foreach ($bahanbaku as $i => $row) :
                $data = array(
                    $i + 1,
                    $row->tanggal_produksi,
                    $row->item_name,
                    Item::find($row->item_id)->sku,
                    $row->jenis,
                    $row->type,
                    str_replace(".", ",", $row->qty),
                    str_replace(".", ",", $row->berat),
                    str_replace(".", ",", $row->stock_qty),
                    str_replace(".", ",", $row->stock_berat),
                );
                fputcsv($fp, $data);
            endforeach;

            fclose($fp);

            return "";
        } else

        if ($request->key == 'produksi_masuk') {
            $prod_masuk =   Chiller::select("item_id", "item_name", "type", "jenis", "tanggal_produksi", DB::raw("round(sum(qty_item), 2) AS qty"), DB::raw("round(sum(berat_item), 2) AS berat"), DB::raw("round(sum(stock_item), 2) AS stock_qty"), DB::raw("round(sum(stock_berat), 2) AS stock_berat"))
            ->where("jenis", "masuk")
            ->where('type', 'hasil-produksi')
            ->whereBetween('tanggal_produksi', [$mulai, $akhir])
            ->groupBy("item_id", "item_name", "jenis", "type", "tanggal_produksi", "asal_tujuan")
            ->orderBy('item_id')
            ->get();

            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=Data Stock Produksi Masuk-" . $mulai . "_" . $akhir . ".csv");
            $fp = fopen('php://output', 'w');
            fputcsv($fp, ["sep=,"]);

            $data = array(
                "No",
                "Tanggal Produksi",
                "Item",
                "SKU",
                "Jenis",
                "Tipe",
                "Qty Masuk",
                "Berat Masuk",
                "Qty Stock",
                "Berat Stock",
            );
            fputcsv($fp, $data);

            foreach ($prod_masuk as $i => $row) :
                $data = array(
                    $i + 1,
                    $row->tanggal_produksi,
                    $row->item_name,
                    Item::find($row->item_id)->sku,
                    $row->jenis,
                    $row->type,
                    str_replace(".", ",", $row->qty),
                    str_replace(".", ",", $row->berat),
                    str_replace(".", ",", $row->stock_qty),
                    str_replace(".", ",", $row->stock_berat),
                );
                fputcsv($fp, $data);
            endforeach;

            fclose($fp);

            return "";
        } else

        if ($request->key == 'lpah') {
            $lpah       =   Production::select("purchasing.no_po AS no_po", "purchasing.tanggal_potong AS tanggal_potong", "purchasing.type_po AS type_po", "productions.sc_tanggal_masuk AS sc_tanggal_masuk", "productions.sc_jam_masuk AS sc_jam_masuk", "productions.sc_no_polisi AS sc_no_polisi", "productions.sc_pengemudi AS sc_pengemudi", "productions.sc_alamat_kandang AS sc_alamat_kandang", "productions.sc_nama_kandang AS sc_nama_kandang", "productions.no_urut AS no_urut", "productions.no_lpah AS no_lpah", "productions.lpah_jam_bongkar AS lpah_jam_bongkar", "productions.lpah_tanggal_potong AS lpah_tanggal_potong", "productions.lpah_berat_susut AS lpah_berat_susut", "productions.lpah_berat_terima AS lpah_berat_terima", "productions.lpah_rerata_terima AS lpah_rerata_terima", "productions.lpah_jumlah_keranjang AS lpah_jumlah_keranjang", "productions.lpah_berat_keranjang AS lpah_berat_keranjang", "productions.lpah_jam_potong AS lpah_jam_potong", "productions.lpah_berat_kotor AS lpah_berat_kotor", "productions.ekoran_seckle AS ekoran_seckle")
            ->join("purchasing", "productions.purchasing_id", "=", "purchasing.id")
            ->where("productions.sc_status", 1)
            ->whereBetween("productions.lpah_tanggal_potong", [$mulai, $akhir])
            ->get();

            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=Data Stock LPAH-" . $mulai . "_" . $akhir . ".csv");
            $fp = fopen('php://output', 'w');
            fputcsv($fp, ["sep=,"]);

            $data = array(
                "No",
                "Nomor PO",
                "Tanggal Potong",
                "Tipe PO",
                "Tanggal Masuk",
                "Jam",
                "Nomor Polisi",
                "Supir",
                "Alamat Kandang",
                "Nama Kandang",
                "Nomor Urut",
                "Nomor LPAH",
                "Jam Bongkar",
                "Tanggal Potong",
                "Berat Susut",
                "Berat Terima",
                "Rerata Terima",
                "Jumlah Keranjang",
                "Berat Keranjang",
                "Jam Potong",
                "Berat Kotor",
                "Ekoran Seckle",
            );
            fputcsv($fp, $data);

            foreach ($lpah as $i => $row) :
                $data = array(
                    $i + 1,
                    $row->no_po,
                    $row->tanggal_potong,
                    $row->type_po,
                    $row->sc_tanggal_masuk,
                    $row->sc_jam_masuk,
                    $row->sc_no_polisi,
                    $row->sc_pengemudi,
                    $row->sc_alamat_kandang,
                    $row->sc_nama_kandang,
                    $row->no_urut,
                    $row->no_lpah,
                    $row->lpah_jam_bongkar,
                    $row->lpah_tanggal_potong,
                    str_replace(".", ",", $row->lpah_berat_susut),
                    str_replace(".", ",", $row->lpah_berat_terima),
                    str_replace(".", ",", $row->lpah_rerata_terima),
                    str_replace(".", ",", $row->lpah_jumlah_keranjang),
                    str_replace(".", ",", $row->lpah_berat_keranjang),
                    str_replace(".", ",", $row->lpah_jam_potong),
                    str_replace(".", ",", $row->lpah_berat_kotor),
                    str_replace(".", ",", $row->ekoran_seckle),
                );
                fputcsv($fp, $data);
            endforeach;

            fclose($fp);

            return "";
        } else

        if ($request->key == 'open_balance') {
            $open       =   DB::select("SELECT a.idb AS idb, a.nama AS nama, a.beratmasuk AS beratmasuk, a.qtymasuk AS qtymasuk, c.beratkeluar AS beratkeluar, c.qtykeluar AS qtykeluar, b.beratop AS beratop, b.qtyop AS qtyop, round((a.beratmasuk - c.beratkeluar), 2) AS total_berat_stock, round((a.qtymasuk - c.qtykeluar), 2) AS total_qty_stock FROM (((select items.id AS idb, items.nama AS nama, round(sum(chiller.qty_item), 2) AS beratmasuk, round(sum(chiller.berat_item), 2) AS qtymasuk FROM (items left join chiller on((chiller.item_id = items.id))) WHERE ((chiller.asal_tujuan <> 'open_balance') and (chiller.type = 'bahan-baku') and (chiller.jenis = 'masuk')) group by items.nama, items.id, items.id order by chiller.item_id) a left join (select items.nama AS nama, items.id AS idc, round(sum(chiller.qty_item), 2) AS beratkeluar, round(sum(chiller.berat_item), 2) AS qtykeluar FROM (items left join chiller on((chiller.item_id = items.id))) WHERE ((chiller.asal_tujuan <> 'open_balance') and (chiller.jenis = 'keluar')) group by items.nama, items.id, items.id) c on((a.idb = c.idc))) left join (select items.nama AS nama, items.id AS ida, round(sum(chiller.qty_item), 2) AS beratop, round(sum(chiller.berat_item), 2) AS qtyop FROM (items left join chiller on((chiller.item_id = items.id))) WHERE (chiller.asal_tujuan = 'open_balance') group by items.nama, items.id, items.id) b on((a.idb = b.ida)))");

            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=Data Stock Open Balance-" . $mulai . "_" . $akhir . ".csv");
            $fp = fopen('php://output', 'w');
            fputcsv($fp, ["sep=,"]);

            $data = array(
                "No",
                "Item",
                "SKU",
                "Berat Masuk",
                "Qty Masuk",
                "Berat Keluar",
                "Qty Keluar",
                "Berat Open Balance",
                "Qty Open Balance",
                "Berat Sisa",
                "Qty Sisa",
            );
            fputcsv($fp, $data);

            foreach ($open as $i => $row) :
                $data = array(
                    $i + 1,
                    $row->nama,
                    Item::find($row->idb)->sku,
                    str_replace(".", ",", $row->beratmasuk),
                    str_replace(".", ",", $row->qtymasuk),
                    str_replace(".", ",", $row->beratkeluar),
                    str_replace(".", ",", $row->qtykeluar),
                    str_replace(".", ",", $row->beratop),
                    str_replace(".", ",", $row->qtyop),
                    str_replace(".", ",", $row->total_berat_stock),
                    str_replace(".", ",", $row->total_qty_stock),
                );
                fputcsv($fp, $data);
            endforeach;

            fclose($fp);

            return "";
        } else

        {

            $alokasi=   Chiller::select("item_id", "item_name", "jenis", "type", "tanggal_produksi", DB::raw("round(sum(qty_item), 2) AS qty"), DB::raw("round(sum(berat_item), 2) AS berat"))
                        ->where('type', 'alokasi-order')
                        ->whereBetween('tanggal_produksi', [$mulai, $akhir])
                        ->groupBy('item_id', 'item_name', 'jenis', 'type', 'tanggal_produksi')
                        ->get();

            $ambilbb=   Chiller::select("item_id", "item_name", "jenis", "type", "tanggal_produksi", DB::raw("round(sum(qty_item), 2) AS qty"), DB::raw("round(sum(berat_item), 2) AS berat"), DB::raw("round(sum(stock_item), 2) AS stock_qty"), DB::raw("round(sum(stock_berat), 2) AS stock_berat"))
            ->where("jenis", "keluar")
            ->where('type', "pengambilan-bahan-baku")
            ->whereBetween('tanggal_produksi', [$mulai, $akhir])
            ->groupBy('item_id', 'item_name', 'jenis', 'type', 'tanggal_produksi')
            ->orderBy('item_id')
            ->get() ;

            $bahanbaku  =   Chiller::select("item_id", "item_name", "tanggal_produksi", "jenis", "type", "asal_tujuan", DB::raw("round(sum(qty_item), 2) AS qty"), DB::raw("round(sum(berat_item), 2) AS berat"), DB::raw("round(sum(stock_item), 2) AS stock_qty"), DB::raw("round(sum(stock_berat), 2) AS stock_berat"))
            ->where('jenis', 'masuk')
            ->where('type', 'bahan-baku')
            ->whereBetween('tanggal_produksi', [$mulai, $akhir])
            ->groupBy("item_id", "item_name", "jenis", "type", "tanggal_produksi", "asal_tujuan")
            ->orderBy('item_id')
            ->get() ;


            $prod_masuk =   Chiller::select("item_id", "item_name", "type", "jenis", "tanggal_produksi", DB::raw("round(sum(qty_item), 2) AS qty"), DB::raw("round(sum(berat_item), 2) AS berat"), DB::raw("round(sum(stock_item), 2) AS stock_qty"), DB::raw("round(sum(stock_berat), 2) AS stock_berat"))
            ->where("jenis", "masuk")
            ->where('type', 'hasil-produksi')
            ->whereBetween('tanggal_produksi', [$mulai, $akhir])
            ->groupBy("item_id", "item_name", "jenis", "type", "tanggal_produksi", "asal_tujuan")
            ->orderBy('item_id')
            ->get();

            $lpah       =   Production::select("purchasing.no_po AS no_po", "purchasing.tanggal_potong AS tanggal_potong", "purchasing.type_po AS type_po", "productions.sc_tanggal_masuk AS sc_tanggal_masuk", "productions.sc_jam_masuk AS sc_jam_masuk", "productions.sc_no_polisi AS sc_no_polisi", "productions.sc_pengemudi AS sc_pengemudi", "productions.sc_alamat_kandang AS sc_alamat_kandang", "productions.sc_nama_kandang AS sc_nama_kandang", "productions.no_urut AS no_urut", "productions.no_lpah AS no_lpah", "productions.lpah_jam_bongkar AS lpah_jam_bongkar", "productions.lpah_tanggal_potong AS lpah_tanggal_potong", "productions.lpah_berat_susut AS lpah_berat_susut", "productions.lpah_berat_terima AS lpah_berat_terima", "productions.lpah_rerata_terima AS lpah_rerata_terima", "productions.lpah_jumlah_keranjang AS lpah_jumlah_keranjang", "productions.lpah_berat_keranjang AS lpah_berat_keranjang", "productions.lpah_jam_potong AS lpah_jam_potong", "productions.lpah_berat_kotor AS lpah_berat_kotor", "productions.ekoran_seckle AS ekoran_seckle")
            ->join("purchasing", "productions.purchasing_id", "=", "purchasing.id")
            ->where("productions.sc_status", 1)
            ->whereBetween("productions.lpah_tanggal_potong", [$mulai, $akhir])
            ->get() ;

            $open       =   DB::select("SELECT a.idb AS idb, a.nama AS nama, a.beratmasuk AS beratmasuk, a.qtymasuk AS qtymasuk, c.beratkeluar AS beratkeluar, c.qtykeluar AS qtykeluar, b.beratop AS beratop, b.qtyop AS qtyop, round((a.beratmasuk - c.beratkeluar), 2) AS total_berat_stock, round((a.qtymasuk - c.qtykeluar), 2) AS total_qty_stock FROM (((select items.id AS idb, items.nama AS nama, round(sum(chiller.qty_item), 2) AS beratmasuk, round(sum(chiller.berat_item), 2) AS qtymasuk FROM (items left join chiller on((chiller.item_id = items.id))) WHERE ((chiller.asal_tujuan <> 'open_balance') and (chiller.type = 'bahan-baku') and (chiller.jenis = 'masuk')) group by items.nama, items.id, items.id order by chiller.item_id) a left join (select items.nama AS nama, items.id AS idc, round(sum(chiller.qty_item), 2) AS beratkeluar, round(sum(chiller.berat_item), 2) AS qtykeluar FROM (items left join chiller on((chiller.item_id = items.id))) WHERE ((chiller.asal_tujuan <> 'open_balance') and (chiller.jenis = 'keluar')) group by items.nama, items.id, items.id) c on((a.idb = c.idc))) left join (select items.nama AS nama, items.id AS ida, round(sum(chiller.qty_item), 2) AS beratop, round(sum(chiller.berat_item), 2) AS qtyop FROM (items left join chiller on((chiller.item_id = items.id))) WHERE (chiller.asal_tujuan = 'open_balance') group by items.nama, items.id, items.id) b on((a.idb = b.ida)))") ;

            $sales_odr  =   DB::select("SELECT a.no_so AS no_so, a.ida AS ida, a.nama_detail AS nama_detail, a.qtyitem AS qtyitem, a.fulfillqty AS fulfillqty, a.fullfilberat AS fullfilberat, a.beratitem AS beratitem, a.statusitem AS statusitem, a.statusorder AS statusorder, a.tanggal_kirim AS tanggal_kirim, b.qtyorder AS qtyorder, b.bborder AS bborder, b.statusbb AS statusbb
            FROM ((select orders.no_so AS no_so, order_items.id AS ida, order_items.nama_detail AS nama_detail, order_items.qty AS qtyitem, order_items.fulfillment_qty AS fulfillqty, order_items.fulfillment_berat AS fullfilberat, order_items.berat AS beratitem, order_items.status AS statusitem, orders.status AS statusorder, orders.tanggal_kirim AS tanggal_kirim
            FROM (order_items left join orders on((order_items.order_id = orders.id)))) a left join (select sum(order_bahan_baku.bb_item) AS qtyorder, sum(order_bahan_baku.bb_berat) AS bborder, order_bahan_baku.status AS statusbb, order_items.id AS idb from (order_bahan_baku join order_items on((order_bahan_baku.order_item_id = order_items.id))) group by order_bahan_baku.order_item_id, order_bahan_baku.status, order_bahan_baku.order_id) b on((a.ida = b.idb)))");

            return view('admin.pages.laporan.datastock.index', compact('mulai', 'akhir', 'alokasi', 'ambilbb', 'bahanbaku', 'prod_masuk', 'lpah', 'open', 'sales_odr'));
        }

    }


    public function datastock()
    {
        $data   =   DB::select("SELECT a.*, c.beratkeluar, c.qtykeluar, b.beratop, b.qtyop FROM ( SELECT items.nama, items.id AS idb, Sum( chiller.stock_berat ) AS beratmasuk, Sum( chiller.stock_item ) AS qtymasuk FROM items LEFT JOIN chiller ON chiller.item_id = items.id WHERE chiller.asal_tujuan != 'open_balance' AND chiller.type = 'bahan-baku' AND chiller.jenis = 'masuk' GROUP BY items.nama, items.id, items.id ) AS a LEFT JOIN ( SELECT items.nama, items.id AS idc, Sum( chiller.stock_berat ) AS beratkeluar, Sum( chiller.stock_item ) AS qtykeluar FROM items LEFT JOIN chiller ON chiller.item_id = items.id WHERE chiller.asal_tujuan != 'open_balance' AND chiller.jenis = 'keluar' GROUP BY items.nama, items.id, items.id ) AS c ON a.idb = c.idc LEFT JOIN ( SELECT items.nama, items.id AS ida, Sum( chiller.stock_berat ) AS beratop, Sum( chiller.stock_item ) AS qtyop FROM items LEFT JOIN chiller ON chiller.item_id = items.id WHERE chiller.asal_tujuan = 'open_balance' GROUP BY items.nama, items.id, items.id ) AS b ON a.idb = b.ida");

        return view('admin.pages.datastock', compact('data'));
    }


    public function fulfillment(Request $request)
    {
        $tanggal    =   $request->tanggal ?? date("Y-m-d") ;
        return view('admin.pages.laporan.fulfillment.index', compact('tanggal'));
    }


    public function detailproduksi(Request $request)
    {
        $data   =   FreestockTemp::select('free_stocktemp.*', 'customers.id AS idn')
                    ->where('item_id', $request->item)
                    ->whereBetween('tanggal_produksi', [$request->tanggal_awal, $request->tanggal_akhir])
                    ->whereIn('freestock_id', Freestock::select('id')->where('status', 3));

        if ($data->count()) {
            if ($request->key == 'view') {
                $data   =   $data
                            ->leftJoin('customers', 'customers.id', '=', 'free_stocktemp.customer_id')
                            ->where(function($query) use ($request){
                                if ($request->cari) {
                                    $query->orWhere('regu', 'like', '%' . $request->cari . '%') ;
                                    $query->orWhere('nama', 'like', '%' . $request->cari . '%') ;
                                    $query->orWhere('tanggal_produksi', 'like', '%' . $request->cari . '%') ;
                                    $query->orWhere('free_stocktemp.id', 'like', '%' . $request->cari . '%') ;
                                    $query->orWhere('qty', 'like', '%' . $request->cari . '%') ;
                                    $query->orWhere('berat', 'like', '%' . $request->cari . '%') ;
                                    $query->orWhere('plastik_nama', 'like', '%' . $request->cari . '%') ;
                                    $query->orWhere('prod_nama', 'like', '%' . $request->cari . '%') ;
                                }
                            })
                            ->paginate(10) ;

                return view('admin.pages.laporan.track_produksi.data', compact('data'));
            } else {
                return view('admin.pages.laporan.track_produksi.index', compact('request'));
            }

        }
        return redirect()->route('dashboard');
    }


    public function konsumenorder(Request $request)
    {
        $data   =   Order::where('customer_id', $request->konsumen)
                    ->whereBetween('tanggal_so', [$request->tanggal_awal, $request->tanggal_akhir]);

        if ($data->count()) {
            if ($request->key == 'view') {
                $data   =   $data->where(function($query) use ($request){
                                if ($request->cari) {
                                    $query->orWhere('no_so', 'like', '%' . $request->cari . '%') ;
                                    $query->orWhere('id', 'like', '%' . $request->cari . '%') ;
                                    $query->orWhere('netsuite_internal_id', 'like', '%' . $request->cari . '%') ;
                                    $query->orWhere('tanggal_so', 'like', '%' . $request->cari . '%') ;
                                    $query->orWhere('tanggal_kirim', 'like', '%' . $request->cari . '%') ;
                                    $query->orWhere('nama', 'like', '%' . $request->cari . '%') ;
                                    $query->orWhere('alamat_kirim', 'like', '%' . $request->cari . '%') ;
                                    $query->orWhere('sales_channel', 'like', '%' . $request->cari . '%') ;
                                }
                            })
                            ->paginate(10) ;

                return view('admin.pages.laporan.track_konsumen.data', compact('data'));
            } else {
                return view('admin.pages.laporan.track_konsumen.index', compact('request'));
            }
        }
        return redirect()->route('dashboard');
    }


    public function konsumenretur(Request $request)
    {
        $data   =   Retur::whereBetween('tanggal_retur', [$request->tanggal_awal, $request->tanggal_akhir])
                    ->where('customer_id', $request->konsumen);

        if ($data->count()) {
            if ($request->key == 'view') {
                $data   =   $data->where(function($query) use ($request){
                                if ($request->cari) {
                                    $query->orWhere('tanggal_retur', 'like', '%' . $request->cari . '%') ;
                                    $query->orWhere('id', 'like', '%' . $request->cari . '%') ;
                                    $query->orWhere('operator', 'like', '%' . $request->cari . '%') ;
                                    $query->orWhere('operator', 'like', '%' . $request->cari . '%') ;
                                    $query->orWhere('no_so', 'like', '%' . $request->cari . '%') ;
                                    $query->orWhere('no_ra', 'like', '%' . $request->cari . '%') ;
                                    $query->orWhere('id_so', 'like', '%' . $request->cari . '%') ;
                                }
                            })
                            ->paginate(10);

                return view('admin.pages.laporan.track_retur.data', compact('data'));
            } else {
                return view('admin.pages.laporan.track_retur.index', compact('request'));
            }
        }
        return redirect()->route('dashboard');
    }


    public function produksiplastik(Request $request)
    {
        $data   =   FreestockTemp::where('regu', $request->regu)
                    ->whereIn('freestock_id', Freestock::select('id')->whereBetween('tanggal', [$request->tanggal_awal, $request->tanggal_akhir])->where('status', 3))
                    ->orderBy('id', 'desc');

        if ($data->count()) {
            if ($request->key == 'view') {
                $data   =   $data->where(function($query) use ($request){
                                if ($request->cari) {
                                    $query->orWhere('id', 'like', '%' . $request->cari . '%') ;
                                    $query->orWhere('freestock_id', 'like', '%' . $request->cari . '%') ;
                                    $query->orWhere('tanggal_produksi', 'like', '%' . $request->cari . '%') ;
                                    $query->orWhere('prod_nama', 'like', '%' . $request->cari . '%') ;
                                    $query->orWhere('plastik_nama', 'like', '%' . $request->cari . '%') ;
                                }
                            });

                $data2  =   $data->get();
                $data   =   $data->paginate(15);
                return view('admin.pages.laporan.produksixplastik.data', compact('data', 'data2', 'request'));
            } else if($request->key == 'unduh') {
                // $data_clone =  $data;
                // $data_unduh = $data->get();
                $data_unduh                 = DB::table('VW_PlasticProduction')->where('regu',$request->regu)->whereBetween('tanggal',[$request->tanggal_awal, $request->tanggal_akhir])->orderBy('id','DESC')->get();
                return view('admin.pages.laporan.produksixplastik.data_download',compact('data_unduh','request'));
            }else {
                return view('admin.pages.laporan.produksixplastik.index', compact('request'));
            }
        }
        return redirect()->route('dashboard');
    }


    public function itempending(Request $request)
    {
        $data   =   OrderItem::where('item_id', $request->item)
                    ->where('order_items.status', NULL)
                    ->leftJoin('orders', 'orders.id', '=', 'order_items.order_id')
                    ->whereBetween('tanggal_kirim', [$request->tanggal_awal, $request->tanggal_akhir]);

        if ($data->count()) {
            if ($request->key == 'view') {
                $data   =   $data->where(function ($query) use ($request) {
                    if ($request->cari) {
                        $query->orWhere('orders.no_so', 'like', '%' . $request->cari . '%');
                        $query->orWhere('tanggal_so', 'like', '%' . $request->cari . '%');
                        $query->orWhere('order_id', 'like', '%' . $request->cari . '%');
                        $query->orWhere('nama', 'like', '%' . $request->cari . '%');
                        $query->orWhere('orders.alamat_kirim', 'like', '%' . $request->cari . '%');
                        $query->orWhere('sales_channel', 'like', '%' . $request->cari . '%');
                        $query->orWhere('qty', 'like', '%' . $request->cari . '%');
                        $query->orWhere('berat', 'like', '%' . $request->cari . '%');
                    }
                });

                $data2  =   $data->get();
                $data   =   $data->paginate(15);
                $item   =   Item::find($request->item);
                return view('admin.pages.laporan.item_pending.data', compact('request', 'data', 'data2', 'item'));
            } else {
                return view('admin.pages.laporan.item_pending.index', compact('request'));
            }
        }
        return redirect()->route('dashboard');
    }


    public function itemalokasi(Request $request)
    {
        $data   =   OrderItem::where('item_id', $request->item)
                    ->where('order_items.status', '!=', NULL)
                    ->leftJoin('orders', 'orders.id', '=', 'order_items.order_id')
                    ->whereBetween('tanggal_so', [$request->tanggal_awal, $request->tanggal_akhir]);

        if ($data->count()) {
            if ($request->key == 'view') {
                $data   =   $data->where(function ($query) use ($request) {
                    if ($request->cari) {
                        $query->orWhere('orders.no_so', 'like', '%' . $request->cari . '%');
                        $query->orWhere('tanggal_so', 'like', '%' . $request->cari . '%');
                        $query->orWhere('no_do', 'like', '%' . $request->cari . '%');
                        $query->orWhere('tanggal_kirim', 'like', '%' . $request->cari . '%');
                        $query->orWhere('order_id', 'like', '%' . $request->cari . '%');
                        $query->orWhere('nama', 'like', '%' . $request->cari . '%');
                        $query->orWhere('orders.alamat_kirim', 'like', '%' . $request->cari . '%');
                        $query->orWhere('sales_channel', 'like', '%' . $request->cari . '%');
                        $query->orWhere('qty', 'like', '%' . $request->cari . '%');
                        $query->orWhere('berat', 'like', '%' . $request->cari . '%');
                    }
                });

                $data2  =   $data->get();
                $data   =   $data->paginate(15);
                $item   =   Item::find($request->item);
                return view('admin.pages.laporan.item_alokasi.data', compact('request', 'data', 'data2', 'item'));
            } else {
                return view('admin.pages.laporan.item_alokasi.index', compact('request'));
            }
        }
        return redirect()->route('dashboard');
    }


    public function cashonhand(Request $request)
    {
        if ($request->key == 'view') {
            $data   =   Chiller::whereBetween('tanggal_produksi', [$request->tanggal_awal, $request->tanggal_akhir])
                        ->where('table_name', 'free_stocktemp')
                        ->where('created_at','>=',Applib::DefaultTanggalAudit())
                        ->where('regu', $request->regu);

            if ($data->count()) {
                $data   =   $data->where(function($query) use ($request) {
                                $query->orWhere('item_name', 'like', '%' . $request->cari . '%');
                                $query->orWhere('tanggal_produksi', 'like', '%' . $request->cari . '%');
                                $query->orWhere('label', 'like', '%' . $request->cari . '%');
                                $query->orWhere('qty_item', 'like', '%' . $request->cari . '%');
                                $query->orWhere('berat_item', 'like', '%' . $request->cari . '%');
                                $query->orWhere('stock_item', 'like', '%' . $request->cari . '%');
                                $query->orWhere('stock_berat', 'like', '%' . $request->cari . '%');
                            }) ;

                $data2  =   $data->get() ;
                $data   =   $data->paginate(5) ;
                return view('admin.pages.laporan.cashonhand.data', compact('request', 'data', 'data2'));
            }

        } else {
            return view('admin.pages.laporan.cashonhand.index', compact('request'));
        }

    }


    public function cashonhandgudang(Request $request)
    {
        if ($request->key == 'view') {
            $data   =   Product_gudang::whereBetween('production_date', [$request->tanggal_awal, $request->tanggal_akhir])
                        ->where('status', 2)
                        ->where('table_name', 'abf')
                        ->whereIn('product_id', Item::select('id')->where(function($query) use ($request) {
                            if ($request->regu == 'whole') {
                                $query->where('category_id', 7);
                            }
                            if ($request->regu == 'marinasi') {
                                $query->where('category_id', 9);
                            }
                            if ($request->regu == 'parting') {
                                $query->where('category_id', 8);
                            }
                            if ($request->regu == 'boneless') {
                                $query->where('category_id', 11);
                            }
                            if ($request->regu == 'byproduct') {
                                $query->where('category_id', 10);
                            }
                        }));

            $data2  =   $data->get();
            $data   =   $data->paginate(5);
            return view('admin.pages.laporan.cashonhand_gudang.data', compact('request', 'data', 'data2'));
        } else {
            return view('admin.pages.laporan.cashonhand_gudang.index', compact('request'));
        }
    }


    public function saleschannel(Request $request)
    {
        $data   =   Order::whereIn('tanggal_kirim', [$request->tanggal_awal, $request->tanggal_akhir])
                    ->where('sales_channel', $request->channel);

        if ($data->count()) {
            if ($request->key == 'view') {
                $data   =   $data->where(function($query) use ($request) {
                                $query->orWhere('id', 'like', '%' . $request->cari . '%');
                                $query->orWhere('no_so', 'like', '%' . $request->cari . '%');
                                $query->orWhere('nama', 'like', '%' . $request->cari . '%');
                                $query->orWhere('tanggal_so', 'like', '%' . $request->cari . '%');
                                $query->orWhere('tanggal_kirim', 'like', '%' . $request->cari . '%');
                                $query->orWhere('alamat_kirim', 'like', '%' . $request->cari . '%');
                            }) ;

                $data   =   $data->where(function($query) use ($request) {
                                if ($request->status) {
                                    if ($request->status == 'selesai') {
                                        $query->where('status', '!=', NULL) ;
                                    }
                                    if ($request->status == 'pending') {
                                        $query->where('status', NULL) ;
                                    }
                                }
                            }) ;

                $data2  =   $data->get() ;
                $data   =   $data->paginate(5) ;

                return view('admin.pages.laporan.saleschannel.data', compact('data', 'data2', 'request'));
            } else {
                return view('admin.pages.laporan.saleschannel.index', compact('request'));
            }
        }
        return redirect()->route('dashboard');

    }


    public function mingguan(Request $request)
    {
        if ($request->key == 'view') {
            $tanggal    =   $request->tanggal ?? date('Y-m-d');

            $result =   [
                'now_rendemen'      =>  LaporanRendemen::report_rendem($tanggal),
                'last_rendemen'     =>  LaporanRendemen::report_rendem(Carbon::parse($tanggal)->subDay(7)->format('Y-m-d')),
            ];

            return view('admin.pages.laporan.perbandingan_mingguan.data', compact('result', 'tanggal'));
        } else {
            return view('admin.pages.laporan.perbandingan_mingguan.index');
        }

    }
}
