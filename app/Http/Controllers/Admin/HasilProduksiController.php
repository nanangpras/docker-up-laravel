<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Abf;
use App\Models\Chiller;
use App\Models\Customer;
use App\Models\Freestock;
use App\Models\FreestockTemp;
use App\Models\Item;
use App\Models\Netsuite;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Production;
use App\Models\Purchasing;
use App\Models\Retur;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HasilProduksiController extends Controller
{

    public function index(Request $request)
    {
        if(User::setIjin(14) || User::setIjin(8) || User::setIjin(9) || User::setIjin(10) || User::setIjin(11) || User::setIjin(12)){

            $tanggal         = $request->tanggal ?? date('Y-m-d');
            $tanggalakhir    = $request->tanggalakhir ?? date('Y-m-d');
            $lokasi          = $request->lokasi ?? '';
            $kosong          = $request->kosong;


            if($request->key == 'stockall'){
                $sql         =   Chiller::whereIn('status', [1, 2])
                                ->where('type', 'hasil-produksi')
                                ->where(function($query) use($kosong){
                                    if($kosong == "true"){
                                        $query->where('stock_berat','<=', '0');
                                    }else{
                                        $query->where('stock_berat','>', '0');
                                    }
                                })
                                ->whereBetween('tanggal_produksi', [$tanggal, $tanggalakhir])
                                ->where(function ($q) use ($lokasi){
                                    // abf
                                    if ($lokasi) {
                                        if ($lokasi == 1) {  
                                            $q->where('kategori',1);
                                        }else if ($lokasi == 2) {
                                            $q->where('kategori',2);
                                        }else if ($lokasi == 3) {
                                            $q->where('kategori',3);
                                        }else if ($lokasi == 'NULL' || $lokasi == 0) {
                                            $q->where('kategori',NULL);
                                            $q->Orwhere('kategori',0);
                                        }
                                    }
                                })
                                ->orderByRaw('item_name ASC, asal_tujuan ASC');
                                
                $master         = clone $sql;
                $arrayData      = $master->get();

                $arrayId        = array();
                foreach($arrayData as $item){
                    $arrayId[]  = $item->id;
                }
                $stringData     = implode(",",$arrayId);
                // dd($stringData); 
                if($stringData){
                    $alokasi    = DB::select("select chiller_out,SUM(bb_item) AS total_qty_alokasi, ROUND(sum(bb_berat),2) AS total_berat_alokasi 
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
                $modification          = [];
                foreach($arrayData as $data){
                    $total_qty_alokasi      = 0;
                    $total_berat_alokasi    = 0;
                    $total_qty_abf          = 0;
                    $total_berat_abf        = 0;
                    $total_qty_freestock    = 0;
                    $total_berat_freestock  = 0;
                    $total_qty_musnahkan    = 0;
                    $total_berat_musnahkan  = 0;
                    $customer ="";
                    $retur      = Retur::with('to_customer')->where('id','=', $data->table_id)->get();

                    foreach($alokasi as $val){
                        if($data->id == $val->chiller_out){
                            $total_qty_alokasi      = $val->total_qty_alokasi;
                            $total_berat_alokasi    = floatval($val->total_berat_alokasi) ?? 0;
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

                    foreach($retur as $retur)
                    {
                        if($data->table_id == $retur->id){
                            $customer = $retur->to_customer->nama;
                        }
                    }

                    $modification[] = [
                        'id'                        => $data->id,
                        'tanggal_produksi'          => $data->tanggal_produksi,
                        'item_name'                 => $data->item_name,
                        'asal_tujuan'               => $data->asal_tujuan,
                        'label'                     => $data->label,
                        'qty_item'                  => $data->qty_item,
                        'selonjor'                  => $data->selonjor,
                        'label'                     => $data->label,
                        'customer_name'             => $customer ?? "#",
                        'plastik_nama'              => $data->plastik_nama,
                        'plastik_qty'               => $data->plastik_qty,
                        'kategori'                  => $data->kategori,
                        'qty_item'                  => $data->qty_item,
                        'status'                    => $data->status,
                        'berat_item'                => floatval($data->berat_item),
                        'total_qty_alokasi'         => $total_qty_alokasi,
                        'total_berat_alokasi'       => $total_berat_alokasi,
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
                $stock                              = json_decode(json_encode($modification));
                // $stock                 = $this->paginate($stock,10);
                // dd($stock);
                return view('admin.pages.chillerfg.stockall', compact('stock', 'tanggal', 'tanggalakhir'));
            }


            if($request->key == 'parting'){
                $parting    =   Chiller::whereIn('table_name', ['free_stocktemp', 'production'])
                                ->where('type', 'hasil-produksi')
                                ->where(function($query) use($kosong){
                                    if($kosong == "true"){
                                        $query->where('stock_berat','<=', '0');
                                    }else{
                                        $query->where('stock_berat','>', '0');
                                    }
                                })
                                ->whereBetween('tanggal_produksi', [$tanggal, $tanggalakhir])
                                ->where('regu', 'parting')
                                ->orderByRaw('item_name ASC, asal_tujuan ASC')
                                ->get();
                return view('admin.pages.chillerfg.parting', compact('parting', 'tanggal', 'tanggalakhir'));
            }


            if($request->key == 'marinasi'){
                $marinasi   =   Chiller::whereIn('table_name', ['free_stocktemp', 'production'])
                                ->where('type', 'hasil-produksi')
                                ->where(function($query) use($kosong){
                                    if($kosong == "true"){
                                        $query->where('stock_berat','<=', '0');
                                    }else{
                                        $query->where('stock_berat','>', '0');
                                    }
                                })
                                ->whereBetween('tanggal_produksi', [$tanggal, $tanggalakhir])
                                ->where('regu', 'marinasi')
                                ->orderByRaw('item_name ASC, asal_tujuan ASC')
                                ->get();
                return view('admin.pages.chillerfg.marinasi', compact('marinasi', 'tanggal', 'tanggalakhir'));
            }


            if($request->key == 'whole'){
                $whole      =   Chiller::whereIn('table_name', ['free_stocktemp', 'production'])
                                ->where('type', 'hasil-produksi')
                                ->where(function($query) use($kosong){
                                    if($kosong == "true"){
                                        $query->where('stock_berat','<=', '0');
                                    }else{
                                        $query->where('stock_berat','>', '0');
                                    }
                                })
                                ->whereBetween('tanggal_produksi', [$tanggal, $tanggalakhir])
                                ->where('regu', 'whole')
                                ->orderByRaw('item_name ASC, asal_tujuan ASC')
                                ->get();
                return view('admin.pages.chillerfg.whole', compact('whole', 'tanggal', 'tanggalakhir'));
            }

            if($request->key == 'frozen'){

                $frozen     =   Abf::whereIn('table_name', ['free_stocktemp', 'production'])
                                ->where('type', 'free-stock')
                                ->where(function($query) use($kosong){
                                    if($kosong == "true"){
                                        $query->where('stock_berat','<=', '0');
                                    }else{
                                        $query->where('stock_berat','>', '0');
                                    }
                                })
                                ->whereDate('created_at', $tanggal)
                                ->orderByRaw('item_name ASC, asal_tujuan ASC')
                                ->get();
                return view('admin.pages.chillerfg.frozen', compact('frozen', 'tanggal', 'tanggalakhir'));
            }


            if($request->key == 'boneless'){
                $bonless    =   Chiller::whereIn('table_name', ['free_stocktemp', 'production'])
                                ->where('type', 'hasil-produksi')
                                ->where(function($query) use($kosong){
                                    if($kosong == "true"){
                                        $query->where('stock_berat','<=', '0');
                                    }else{
                                        $query->where('stock_berat','>', '0');
                                    }
                                })
                                ->whereBetween('tanggal_produksi', [$tanggal, $tanggalakhir])
                                ->where('regu', 'boneless')
                                ->orderByRaw('item_name ASC, asal_tujuan ASC')
                                ->get();
                return view('admin.pages.chillerfg.boneless', compact('bonless', 'tanggal', 'tanggalakhir'));
            }

            if($request->key == 'evis'){
                $evis       =   Chiller::whereIn('table_name', ['free_stocktemp', 'production'])
                                ->where('type', 'hasil-produksi')
                                ->where(function($query) use($kosong){
                                    if($kosong == "true"){
                                        $query->where('stock_berat','<=', '0');
                                    }else{
                                        $query->where('stock_berat','>', '0');
                                    }
                                })
                                ->whereBetween('tanggal_produksi', [$tanggal, $tanggalakhir])
                                ->where('regu', 'byproduct')
                                ->orderByRaw('item_name ASC, asal_tujuan ASC')
                                ->get();
                return view('admin.pages.chillerfg.evis', compact('evis', 'tanggal', 'tanggalakhir'));
            }

            if ($request->key == 'unduh') {
                if ($request->tipe == 'all') {
                    $stock      =   Chiller::whereIn('status', [1, 2])
                    ->where('type', 'hasil-produksi')
                    ->whereBetween('tanggal_produksi', [$tanggal, $tanggalakhir])
                    // ->orderByRaw('item_name ASC, asal_tujuan ASC')
                    ->orderBy('tanggal_produksi' ,'DESC')
                    ->orderBy('item_name')
                    ->orderBy('asal_tujuan')
                    ->get();
                    header("Content-type: application/csv");
                    header("Content-Disposition: attachment; filename=Chiller Finished Good dari tanggal " . $tanggal . " - ". $tanggalakhir . ".csv");
                    $fp = fopen('php://output', 'w');
                    fputcsv($fp, ["sep=,"]);

                    $data = array(
                        "No",
                        "Tanggal",
                        "Item",
                        "Plastik",
                        "Jumlah Plastik",
                        "Parting",
                        "Customer",
                        "Ekor Awal",
                        "Berat Awal",
                        "Ekor Sisa",
                        "Berat Sisa",
                        "Status",
                        "Alokasi"
                    );
                    fputcsv($fp, $data);

                    foreach ($stock as $i => $item) {
                        $exp = json_decode($item->label);
                        if ($item->kategori == "1") {
                            $kategori ="ABF";
                        }elseif($item->kategori == "2"){
                            $kategori ="EKSPEDISI";
                        }elseif($item->kategori == "3"){
                            $kategori ="TITIP CS";
                        }else{
                            $kategori ="CHILLER";
                        }
                        $data = array(
                            ++$i,
                            $item->tanggal_produksi,
                            $item->item_name,
                            $exp ? ($exp->plastik->jenis ?? "") : "",
                            $exp ? ($exp->plastik->qty ?? "") : "",
                            $exp ? ($exp->parting->qty ?? "") : "",
                            $exp ? ($exp->sub_item ?? "") : "",
                            number_format($item->qty_item),
                            number_format($item->berat_item, 2),
                            number_format($item->stock_item),
                            number_format($item->stock_berat,2),
                            $item->tujuan,
                            $kategori
                        );
                        fputcsv($fp, $data);
                    }

                    fclose($fp);
                    return "";
                } else

                if ($request->tipe == 'boneless') {
                    $bonless    =   Chiller::whereIn('table_name', ['free_stocktemp', 'production'])
                    ->where('type', 'hasil-produksi')
                    ->whereBetween('tanggal_produksi', [$tanggal, $tanggalakhir])
                    ->where('regu', 'boneless')
                    ->orderByRaw('item_name ASC, asal_tujuan ASC')
                    ->get();

                    header("Content-type: application/csv");
                    header("Content-Disposition: attachment; filename=Chiller Finished Good Boneless dari tanggal " . $tanggal . " - ". $tanggalakhir . ".csv");
                    $fp = fopen('php://output', 'w');
                    fputcsv($fp, ["sep=,"]);

                    $data = array(
                        "No",
                        "Tanggal",
                        "Item",
                        "Plastik",
                        "Jumlah Plastik",
                        "Customer",
                        "Ekor Sisa",
                        "Berat Sisa",
                        "Status",
                    );
                    fputcsv($fp, $data);

                    foreach ($bonless as $i => $item) {
                        $exp = json_decode($item->label);

                        $data = array(
                            ++$i,
                            $item->tanggal_produksi,
                            $item->item_name,
                            $exp ? ($exp->plastik->jenis ?? "") : "",
                            $exp ? ($exp->plastik->qty ?? "") : "",
                            $exp ? ($exp->sub_item) : "",
                            str_replace(".", ",", $item->stock_item),
                            str_replace(".", ",", $item->stock_berat),
                            $item->tujuan,
                        );
                        fputcsv($fp, $data);
                    }

                    fclose($fp);
                    return "";
                } else

                if ($request->tipe == 'parting') {
                    $parting    =   Chiller::whereIn('table_name', ['free_stocktemp', 'production'])
                    ->where('type', 'hasil-produksi')
                    ->whereBetween('tanggal_produksi', [$tanggal, $tanggalakhir])
                    ->where('regu', 'parting')
                    ->orderByRaw('item_name ASC, asal_tujuan ASC')
                    ->get();

                    header("Content-type: application/csv");
                    header("Content-Disposition: attachment; filename=Chiller Finished Good Parting dari tanggal " . $tanggal . " - ". $tanggalakhir . ".csv");
                    $fp = fopen('php://output', 'w');
                    fputcsv($fp, ["sep=,"]);

                    $data = array(
                        "No",
                        "Tanggal",
                        "Item",
                        "Plastik",
                        "Jumlah Plastik",
                        "Parting",
                        "Customer",
                        "Ekor Sisa",
                        "Berat Sisa",
                        "Status",
                    );
                    fputcsv($fp, $data);

                    foreach ($parting as $i => $item) {
                        $exp = json_decode($item->label);

                        $data = array(
                            ++$i,
                            $item->tanggal_produksi,
                            $item->item_name,
                            $exp ? ($exp->plastik->jenis ?? "") : "",
                            $exp ? ($exp->plastik->qty ?? "") : "",
                            $exp ? ($exp->parting->qty ?? "") : "",
                            $exp ? ($exp->sub_item) : "",
                            str_replace(".", ",", $item->stock_item),
                            str_replace(".", ",", $item->stock_berat),
                            $item->tujuan,
                        );
                        fputcsv($fp, $data);
                    }

                    fclose($fp);
                    return "";
                } else

                if ($request->tipe == 'marinasi') {
                    $marinasi   =   Chiller::whereIn('table_name', ['free_stocktemp', 'production'])
                    ->where('type', 'hasil-produksi')
                    ->whereBetween('tanggal_produksi', [$tanggal, $tanggalakhir])
                    ->where('regu', 'marinasi')
                    ->orderByRaw('item_name ASC, asal_tujuan ASC')
                    ->get();

                    header("Content-type: application/csv");
                    header("Content-Disposition: attachment; filename=Chiller Finished Good Parting Marinasi dari tanggal " . $tanggal . " - ". $tanggalakhir . ".csv");
                    $fp = fopen('php://output', 'w');
                    fputcsv($fp, ["sep=,"]);

                    $data = array(
                        "No",
                        "Tanggal",
                        "Item",
                        "Plastik",
                        "Jumlah Plastik",
                        "Parting",
                        "Customer",
                        "Ekor Sisa",
                        "Berat Sisa",
                        "Status",
                    );
                    fputcsv($fp, $data);

                    foreach ($marinasi as $i => $item) {
                        $exp = json_decode($item->label);

                        $data = array(
                            ++$i,
                            $item->tanggal_produksi,
                            $item->item_name,
                            $exp ? ($exp->plastik->jenis ?? "") : "",
                            $exp ? ($exp->plastik->qty ?? "") : "",
                            $exp ? ($exp->parting->qty ?? "") : "",
                            $exp ? ($exp->sub_item) : "",
                            str_replace(".", ",", $item->stock_item),
                            str_replace(".", ",", $item->stock_berat),
                            $item->tujuan,
                        );
                        fputcsv($fp, $data);
                    }

                    fclose($fp);
                    return "";
                } else

                if ($request->tipe == 'whole') {
                    $whole      =   Chiller::whereIn('table_name', ['free_stocktemp', 'production'])
                    ->where('type', 'hasil-produksi')
                    ->whereBetween('tanggal_produksi', [$tanggal, $tanggalakhir])
                    ->where('regu', 'whole')
                    ->orderByRaw('item_name ASC, asal_tujuan ASC')
                    ->get();

                    header("Content-type: application/csv");
                    header("Content-Disposition: attachment; filename=Chiller Finished Good Whole Chicken dari tanggal " . $tanggal . " - ". $tanggalakhir . ".csv");
                    $fp = fopen('php://output', 'w');
                    fputcsv($fp, ["sep=,"]);

                    $data = array(
                        "No",
                        "Tanggal",
                        "Item",
                        "Plastik",
                        "Jumlah Plastik",
                        "Customer",
                        "Ekor Sisa",
                        "Berat Sisa",
                        "Status",
                    );
                    fputcsv($fp, $data);

                    foreach ($whole as $i => $item) {
                        $exp = json_decode($item->label);

                        $data = array(
                            ++$i,
                            $item->tanggal_produksi,
                            $item->item_name,
                            $exp ? ($exp->plastik->jenis ?? "") : "",
                            $exp ? ($exp->plastik->qty ?? "") : "",
                            $exp ? ($exp->sub_item) : "",
                            str_replace(".", ",", $item->stock_item),
                            str_replace(".", ",", $item->stock_berat),
                            $item->tujuan,
                        );
                        fputcsv($fp, $data);
                    }

                    fclose($fp);
                    return "";
                } else

                if ($request->tipe == 'evis') {
                    $evis       =   Chiller::whereIn('table_name', ['free_stocktemp', 'production'])
                    ->where('type', 'hasil-produksi')
                    ->whereBetween('tanggal_produksi', [$tanggal, $tanggalakhir])
                    ->where('regu', 'byproduct')
                    ->orderByRaw('item_name ASC, asal_tujuan ASC')
                    ->get();

                    header("Content-type: application/csv");
                    header("Content-Disposition: attachment; filename=Chiller Finished Good Evis dari tanggal " . $tanggal . " - ". $tanggalakhir . ".csv");
                    $fp = fopen('php://output', 'w');
                    fputcsv($fp, ["sep=,"]);

                    $data = array(
                        "No",
                        "Tanggal",
                        "Item",
                        "Ekor Sisa",
                        "Berat Sisa",
                        "Status",
                    );
                    fputcsv($fp, $data);

                    foreach ($evis as $i => $item) {
                        $data = array(
                            ++$i,
                            $item->tanggal_produksi,
                            $item->item_name,
                            str_replace(".", ",", $item->stock_item),
                            str_replace(".", ",", $item->stock_berat),
                            $item->tujuan,
                        );
                        fputcsv($fp, $data);
                    }

                    fclose($fp);
                    return "";
                }
            } else {
                return view('admin/pages/hasil-produksi', compact('tanggal', 'tanggalakhir'));
            }
        }
        return redirect()->route("index");
    }



    public function nonlb(Request $request){

        $tanggal                = $request->tanggal;
        $tanggalakhir           = $request->tanggalakhir;
        $data                   =   Production::whereIn('purchasing_id', Purchasing::select('id')
                                ->whereBetween('tanggal_potong', [$tanggal, $tanggalakhir])
                                ->whereIn('type_po', ['PO Karkas', 'PO Non Karkas', 'PO Evis'])
                                )
                                ->whereIn('ppic_tujuan', ['chiller'])
                                ->whereIn('ppic_acc', [2,3])
                                ->where('lpah_status', 1)
                                ->where('evis_status', 1)
                                ->where('grading_status', 1)
                                ->orderBy('no_urut', 'ASC')
                                ->get();

        $counttransaction        = $data->count() ?? '0';

        $countsuccesstransaction =   Production::whereIn('purchasing_id', Purchasing::select('id')
                            // ->whereDate('tanggal_potong', $tanggal)
                                ->whereBetween(DB::raw('DATE(tanggal_potong)'), [$tanggal, $tanggalakhir])
                                ->whereIn('type_po', ['PO Karkas', 'PO non Karkas']))
                                ->whereIn('ppic_tujuan', ['abf','chiller'])
                                ->whereIn('ppic_acc', [3])
                                ->where('lpah_status', 1)
                                ->where('evis_status', 1)
                                ->where('grading_status', 1)
                                ->orderByRaw('prod_tanggal_potong ASC, no_urut ASC')
                                ->count();

        $countprocesstransaction =   Production::whereIn('purchasing_id', Purchasing::select('id')
                                // ->whereDate('tanggal_potong', $tanggal)
                                ->whereBetween(DB::raw('DATE(tanggal_potong)'), [$tanggal, $tanggalakhir])
                                ->whereIn('type_po', ['PO Karkas', 'PO non Karkas']))
                                ->whereIn('ppic_tujuan', ['abf','chiller'])
                                ->whereIn('ppic_acc', [2])
                                ->where('lpah_status', 1)
                                ->where('evis_status', 1)
                                ->where('grading_status', 1)
                                ->orderByRaw('prod_tanggal_potong ASC, no_urut ASC')
                                ->count();

        // return view('admin.pages.chiler.list_non_lb', compact('tanggal', 'data')) ;
        return view('admin.pages.non_karkas.timbang', compact('tanggal', 'data','counttransaction','countsuccesstransaction','countprocesstransaction')) ;

    }

    public function edit()
    {
        return view('admin.pages.hasilproduksi.index');
    }

    public function editshow(Request $request)
    {
        $tanggal = $request->tanggal ?? date('Y-m-d');

        $data   =   Chiller::where('table_name', 'free_stocktemp')->where('jenis', 'masuk')->where('type', 'hasil-produksi')->where('tanggal_produksi', $tanggal)->get();
        $item   =   Item::whereNotIn('category_id', ['21','22','23','24','25','26', '27', '28', '29', '30'])->get();

        return view('admin.pages.hasilproduksi.show', compact('data','item','tanggal'));
    }

    public function editstore(Request $request)
    {
        $chiller                =   Chiller::find($request->id);
        $item                   =   Item::find($request->item);

        if ($chiller->item_id != $item->id) {
            $transfer   =   [
                [
                    'internal_id_item'  =>  $chiller->chillitem->netsuite_internal_id ,
                    'item'              =>  $chiller->chillitem->sku ,
                    'qty_to_transfer'   =>  $chiller->stock_berat
                ] ,
                [
                    'internal_id_item'  =>  $item->netsuite_internal_id ,
                    'item'              =>  $item->sku ,
                    'qty_to_transfer'   =>  $request->berat
                ]
            ];

            Netsuite::wo_tukaritem('chiller', $chiller->id, $transfer);
        }

        $chiller->item_id       =   $request->item;
        $chiller->item_name     =   $item->nama;
        $chiller->qty_item      =   $request->qty;
        $chiller->berat_item    =   $request->berat;
        $chiller->stock_item    =   $request->qty;
        $chiller->stock_berat   =   $request->berat;
        $chiller->save();

        try {
            Chiller::recalculate_chiller($chiller->id);
        } catch (\Throwable $th) {

        }

        $freestocktemp          =   FreestockTemp::find($chiller->table_id);
        $freestocktemp->item_id =   $request->item;
        $freestocktemp->qty     =   $request->qty;
        $freestocktemp->berat   =   $request->berat;
        $freestocktemp->save();

        return back()->with('status'. 1)->with('message', 'Berhasil Update');
    }

    public function customer_stock(Request $request)
    {
        // if(User::setIjin(14) || User::setIjin(8) || User::setIjin(9) || User::setIjin(10) || User::setIjin(11) || User::setIjin(12)){
            $tanggal         = $request->tanggalawal ?? date('Y-m-d');
            $tanggalakhir    = $request->tanggalakhir ?? date('Y-m-d');
            $filter_customer = $request->customer ?? '';

            // dd($filter_customer);

            if (isset($filter_customer)) {
                $stok           = Chiller::select('chiller.item_name','chiller.label','chiller.item_name','chiller.customer_id','chiller.stock_berat','chiller.stock_item','customers.nama')
                            ->join('customers','chiller.customer_id','=','customers.id')
                            ->whereBetween('chiller.tanggal_produksi',[$tanggal,$tanggalakhir])
                            // ->whereIn('chiller.customer_id',[$filter_customer])
                            ->where('chiller.stock_item','>',0)
                            ->where('chiller.stock_berat','>',0)
                            ->where(function($q) use ($filter_customer){
                                if ($filter_customer !== '' ) {
                                    $q->where('customer_id',$filter_customer);
                                }
                            })
                            ->orderBy('chiller.tanggal_produksi','desc');
                if ($filter_customer == 'all') {
                    $stok           = Chiller::select('chiller.item_name','chiller.label','chiller.item_name','chiller.customer_id','chiller.stock_berat','chiller.stock_item','customers.nama')
                            ->join('customers','chiller.customer_id','=','customers.id')
                            ->whereBetween('chiller.tanggal_produksi',[$tanggal,$tanggalakhir])
                            // ->whereIn('chiller.customer_id',[$filter_customer])
                            ->where('chiller.stock_item','>',0)
                            ->where('chiller.stock_berat','>',0)
                            ->orderBy('chiller.tanggal_produksi','desc');
                }
            }


            if ($request->key == 'getcustomer') {
                $stok = $stok->groupBy('chiller.customer_id')->get();
                // dd($filter_customer);
                return view('admin.pages.kepala_produksi.component.get_customer',compact('stok','filter_customer','tanggal','tanggalakhir'));
            } else if ($request->key == 'data_customer_stock'){
                $clone_stok = clone $stok;
                $stok = $clone_stok->groupBy('customers.nama')->get();
                return view('admin.pages.kepala_produksi.component.data_customer_stock',compact('stok','tanggal','tanggalakhir','filter_customer'));
            }
            else {
                // $customer       = self::dd_customer($tanggal,$tanggalakhir);
                return view('admin.pages.kepala_produksi.customer_stock',compact('tanggal','tanggalakhir','filter_customer'));
            }
        // }

    }

    public static function dd_customer($tanggal,$tanggalakhir)
    {
        if ($tanggal === '' && $tanggalakhir === '') {
            $dd['']                     = 'Customer';
            return $dd;
        }
        $query           = Chiller::select('customers.id','customers.nama')
                            ->join('customers','chiller.customer_id','=','customers.id')
                            ->whereBetween('chiller.tanggal_produksi',[$tanggal,$tanggalakhir])
                            ->orderBy('chiller.tanggal_produksi','asc')
                            ->get();
        // dd($query);
        $dd['']                         = 'Customer';
        if ($query->count() > 0) {
            foreach ($query as $row) {
                $dd[$row->id]    = $row->nama;
            }
        }
        return $dd;
    }

}
