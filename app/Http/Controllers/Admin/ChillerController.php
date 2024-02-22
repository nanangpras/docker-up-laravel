<?php

namespace App\Http\Controllers\Admin;

use App\Classes\Applib;
use App\Http\Controllers\Controller;
use App\Models\Abf;
use App\Models\Adminedit;
use App\Models\Bahanbaku;
use App\Models\Chiller;
use App\Models\Customer;
use App\Models\Evis;
use App\Models\Freestock;
use App\Models\FreestockList;
use App\Models\FreestockTemp;
use App\Models\Grading;
use App\Models\Item;
use App\Models\Log;
use App\Models\Netsuite;
use App\Models\OrderItem;
use App\Models\Product_gudang;
use App\Models\Production;
use App\Models\Purchasing;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChillerController extends Controller
{
    public function index(Request $request)
    {
        if (User::setIjin(13) || User::setIjin(8) || User::setIjin(9) || User::setIjin(10) || User::setIjin(11) || User::setIjin(12) || User::setIjin(5)) {
            $tgl    =   $request->tglpotong ?? Carbon::now()->format('Y-m-d');

            $masuk  =   Chiller::whereIn('status', [1, 2])
                        ->whereDate('tanggal_produksi', $tgl)
                        ->where('type', 'bahan-baku')
                        ->whereIn('asal_tujuan', ['evisgabungan','gradinggabungan', 'karkasbeli','thawing','retur'])
                        ->get();

            $stock  =   Chiller::whereIn('status', [1, 2])
                        ->where('type', 'bahan-baku')
                        ->where('stock_berat', '>', '0')
                        ->orderBy('item_name', 'ASC')
                        ->get();

            $jumlah_item  =   Chiller::whereIn('status', [1, 2])
                        ->where('type', 'bahan-baku')
                        ->where('stock_berat', '>', '0')
                        ->distinct('item_id')
                        ->count();

            $keluar =   Chiller::whereIn('status', [3, 4])
                        ->whereDate('tanggal_produksi', $tgl)
                        ->where('type', 'pengambilan-bahan-baku')
                        ->get();

            $ekor   =   Chiller::select('qty_item')
                        ->whereIn('status', [1, 2, 3])
                        ->whereDate('tanggal_produksi', $tgl)
                        ->where('stock_item', '<>', '0')
                        ->sum('stock_item');

            $berat  =   Chiller::select('berat_item')
                        ->whereIn('status', [1, 2, 3])
                        ->whereDate('tanggal_produksi', $tgl)
                        ->where('stock_item', '<>', '0')
                        ->sum('stock_berat');

            return view('admin/pages/chiler/index', compact('masuk', 'keluar', 'ekor', 'berat', 'tgl', 'stock', 'jumlah_item'));
        }
        return redirect()->route("index");
    }

    public function store(Request $request)
    {
        if (User::setIjin(13)) {
            DB::beginTransaction();

            $data   =   Chiller::select('id', 'status', 'item_id', 'asal_tujuan', 'jenis', 'table_id')
                        ->where('id', $request->x_code)
                        ->whereIn('status', [1,3])
                        ->first();
                // return $data;

            $hash = "";

            if ($data) {
                $data->status   =   $data->status == 1 ? 2 : 4;

                if ($data->jenis == "keluar") {
                    $hash       = "#custom-tabs-keluar";
                } else {
                    $hash       = "#custom-tabs-masuk";
                }

                if ($data->asal_tujuan == 'free_stock') {
                    $order_bb = FreestockList::where('id', $data->table_id)->first();
                } else {
                    $order_bb = Bahanbaku::where('order_item_id', $data->table_id)->first();
                }

                if ($data->jenis == 'keluar') {

                    $data_all = [];
                    if ($order_bb->chiller_id == null) {
                        $bahan                  =   Bahanbaku::where('status', 1)->first();
                        $out                    =   json_decode($bahan->data_chiller);
                        $out                    =  (array) $out;

                        $cok = [];
                        $dus = [];
                        $nom = [];
                        for ($i = 0; $i < count($out); $i++) {
                            $dus[] = $out[$i][0];
                            $cok[] = $out[$i][1];
                            $nom[] = $out[$i][2];

                            if ($out[$i][0] == $data->id) {
                                $gabung = array(
                                    $out[$i][0], $out[$i][1], $request->qty
                                );

                                $data_all[] = $gabung;
                            } else {
                                $gabung = array(
                                    $out[$i][0], $out[$i][1], $out[$i][2]
                                );

                                $data_all[] = $gabung;
                            }

                        }

                        $bahan->data_chiller = json_encode($data_all);
                        $bahan->save();

                        foreach($cok as $su){
                            $potong                 =   Chiller::find($su);
                            $potong->stock_item     =   ($potong->stock_item - $request->qty);
                            $potong->stock_berat    =   ($potong->stock_berat - $request->berat);
                            $potong->save();

                        }

                    } else {
                        $potong                 =   Chiller::find($order_bb->chiller_id);
                        $potong->stock_item     =   ($potong->stock_item - $request->qty);
                        $potong->stock_berat    =   ($potong->stock_berat - $request->berat);
                        $potong->save();

                        $order_it               =   Bahanbaku::where('chiller_out', $data->id)->first();
                        if ($order_it) {
                            $order_it->bb_item      =   $request->qty;
                            $order_it->bb_berat     =   $request->berat;
                            $order_it->save();

                        }
                    }
                } else {
                    $data->stock_item           =   $request->qty;
                    $data->stock_berat          =   $request->berat;
                }

                $data->qty_item     =   $request->qty;
                $data->berat_item   =   $request->berat;
                $data->save();

                DB::commit();
                return redirect()->to(url()->previous() . $hash)->with('status', 1)->with('message', 'Data berhasil dikonfirmasi');
            }
            DB::rollback();
            return redirect()->to(url()->previous() . $hash)->with('status', 2)->with('message', 'Data tidak ditemukan');
        }
        return redirect()->route("index");
    }

    public function chilleradjustment(Request $request){

        DB::beginTransaction() ;
        $data                   =   Chiller::where('id', $request->chiller_id)->first();

        $sisa_qty               =   $request->ubah_qty - $data->stock_item ;
        $sisa_berat             =   $request->ubah_berat - $data->stock_berat ;

        // Edit dari form jadi berapa sesuai dengan inputan
        $data->stock_item             =   $request->ubah_qty;
        $data->stock_berat            =   $request->ubah_berat;
        $data->save();

        $chiller_baru                 =   new Chiller();
        $chiller_baru->item_id        =   $data->item_id;
        $chiller_baru->item_name      =   $data->item_name;
        $chiller_baru->qty_item       =   $sisa_qty;
        $chiller_baru->berat_item     =   $sisa_berat;
        $chiller_baru->stock_item     =   $sisa_berat;
        $chiller_baru->stock_berat    =   $sisa_berat;
        $chiller_baru->table_name     =   'chiller';
        $chiller_baru->table_id       =   $data->id;
        $chiller_baru->tanggal_produksi    =   date('Y-m-d');
        $chiller_baru->type           =   "inventory_adjustment";

        if ($sisa_qty < 0 || $sisa_berat < 0) {
            $chiller_baru->jenis=   "keluar" ;
            $chiller_baru->asal_tujuan =   "negatif" ;
            $chiller_baru->status     =   4 ;
        } else {
            $chiller_baru->status     =   2 ;
            $chiller_baru->jenis=   "masuk" ;
            $chiller_baru->asal_tujuan =   "positif" ;
        }

        if (!$chiller_baru->save()) {
            DB::rollBack() ;
        }

        DB::commit() ;


        return back()->with('status', 1)->with('message', 'Data Diupdate');

    }

    public function show($id)
    {
        // Chiller::recalculate_chiller($id);
        // if (User::setIjin(13)) {
            $data       =   Chiller::where('id', $id)->with('countNonLB')->first();
            $month  = date('m', strtotime($data->tanggal_produksi));
            $year   = date('Y', strtotime($data->tanggal_produksi));
            if ($data) {
                // Chiller::recalculate_chiller($id);
                $data_lpah  =   Production::where('id', $data->production_id)->first();
                $tukar_item =   Chiller::where('table_id', $data->id)->where('asal_tujuan', 'tukar_item')->get();

                $cekCutOff  =   Chiller::whereBetween('tanggal_produksi',[$data->tanggal_produksi,$data->tanggal_produksi])
                                        ->where('status_cutoff', 1)
                                        ->count();
                $transid = [];
                foreach($data->countNonLB as $new){
                    $n = Chiller::getIdPurc($new->purchasing_id);
                    if($n){
                        $transid[] = Chiller::getProdId($n);
                    }
                }
                // dd($transid);
                $stringData         = implode(",",$transid);
                $sumTotalItemNonLB  = Grading::whereIn('trans_id',[$stringData])->where('item_id',$data->item_id)->where('tanggal_potong',$data->tanggal_produksi)->where('status',1)->sum('total_item');
                $sumTotalBeratNonLB = Grading::whereIn('trans_id',[$stringData])->where('item_id',$data->item_id)->where('tanggal_potong',$data->tanggal_produksi)->where('status',1)->sum('berat_item');

                return view('admin.pages.chiler.chiller-tracing', compact('data', 'data_lpah', 'tukar_item','cekCutOff','sumTotalItemNonLB','sumTotalBeratNonLB'));
            }
            return redirect()->route('chiller.index')->with('status', 2)->with('message', 'Item tidak ditemukan');
        // }
        // return redirect()->route("index");
    }

    public function recalculate($id, Request $request){

        if($request->key=="penggunaan"){
            $recalculate = Chiller::recalculate_chiller($id);
            if($recalculate){
                return back()->with('status', 1)->with('message', 'Berhasil Recalculate Chiller');
            }else{
                return back()->with('status', 2)->with('message', 'Gagal Recalculate Chiller');
            }
        }

        if($request->key=="stockawal"){
            $recalculate = Chiller::recalculate_chiller_stock($id);

            if($recalculate){
                return back()->with('status', 1)->with('message', 'Berhasil Recalculate Chiller');
            }else{
                return back()->with('status', 2)->with('message', 'Gagal Recalculate Chiller');
            }
        }

    }

    public function update(Request $request, $id)
    {
        if (User::setIjin(13)) {
            $request->validate([
                "result"            =>  'required|numeric',
                "jumlah_keranjang"  =>  'required|numeric',
                "berat"             =>  'required|numeric',
                "berat_bersih"      =>  'required|numeric',
                "transaksi"         =>  'required|in:masuk,sisa',
                "asal"              =>  'required|in:baru,retur,sampingan,frozen,penyiapan_utuh,penyiapan_parting,penyiapan_marinasi,boneless,abf,free_stock,belum_terpakai,karyawan'
            ]);

            $chiler                 =   Chiller::find($id);
            $chiler->qty_item       =   $request->result;
            $chiler->keranjang      =   $request->jumlah_keranjang;
            $chiler->berat_item     =   $request->berat;
            $chiler->stock_berat    =   $request->berat_bersih;
            $chiler->jenis          =   $request->transaksi;
            $chiler->asal_tujuan    =   $request->asal;
            $chiler->status         =   2;
            $chiler->save();

            return redirect()->route('chiller.index')->with('status', 1)->with('message', 'Chiller berhasil diperbaharui');
        }
        return redirect()->route("index");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (User::setIjin(13)) {
            //
        }
        return redirect()->route("index");
    }

    public function chiller_export(Request $request){

        $stock  =   Chiller::whereIn('status', [1, 2])
                        ->where('type', 'bahan-baku')
                        ->where('stock_berat', '>', '0')
                        ->get();


        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=cgl-chiller-".date('Y-m-d-H:i:s').".csv");
        $fp = fopen('php://output', 'w');
        fputcsv($fp,["sep=,"]);

        $data = array(
            "No",
            "Nama",
            "No Mobil",
            "Tanggal Produksi",
            "Qty",
            "Berat",
            "Asal Tujuan"
        );
        fputcsv($fp,$data);

        foreach($stock as $no => $item):

            $data = array(
                    $no+1,
                    $item->item_name,
                    $item->no_mobil ?? '',
                    $item->tanggal_produksi,
                    $item->stock_item,
                    str_replace(".", ",",$item->stock_berat),
                    $item->tujuan,
            );
            fputcsv($fp,$data);
        endforeach;

        fclose($fp);
    }

    public function chiller_masuk(Request $request)
    {

        $mulai      =   $request->mulai ?? date('Y-m-d');
        $sampai     =   $request->sampai ?? date('Y-m-d');
        $masukcari  =   $request->masukcari ?? '' ;

        $masuk      =   Chiller::where('type', 'bahan-baku')
                        // ->leftJoin('customers', 'customers.id', '=', 'chiller.customer_id')
                        ->whereIn('asal_tujuan', ['evisgabungan','gradinggabungan', 'karkasbeli','thawing','retur', 'tukar_item'])
                        ->whereBetween('tanggal_produksi', [$mulai,$sampai])
                        ->where(function($query) use ($masukcari) {
                            if ($masukcari) {
                                $query->orWhere('item_name', 'like', '%' . $masukcari . '%') ;
                                $query->orWhere('tanggal_produksi', 'like', '%' . $masukcari . '%') ;
                                $query->orWhere('label', 'like', '%' . $masukcari . '%') ;
                            }
                        })
                        ->orderBy('item_name', 'ASC')
                        ->orderBy('tanggal_produksi', 'ASC')
                        ->get();

        if ($request->key == 'unduh') {
            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=Chiller Bahan Baku Masuk " . $mulai . " - " . $sampai . ".csv");
            $fp = fopen('php://output', 'w');
            fputcsv($fp, ["sep=,"]);

            $data = array(
                "No",
                "Tanggal",
                "Item",
                "Regu",
                "Ekor",
                "Berat",
                "Asal",
                "Status",
                "Tipe",
            );
            fputcsv($fp, $data);

            foreach ($masuk as $i => $item) {
                $data = array(
                    ++$i,
                    $item->tanggal_produksi,
                    $item->item_name,
                    ($item->regu ?? ''),
                    // str_replace(".", ",", $item->qty_item),
                    // str_replace(".", ",", $item->berat_item),
                    number_format($item->qty_item,2),
                    number_format($item->berat_item,2),
                    $item->jenis ?: $item->chilprod->prodpur->nama_po,
                    $item->tujuan,
                    str_replace("-", " ", $item->type)
                );
                fputcsv($fp, $data);
            }

            fclose($fp);
            return "";
        } else {
            return view('admin.pages.chiler.masuk', compact('masuk', 'mulai', 'sampai', 'masukcari'));
        }
    }

    public function chiller_keluar(Request $request)
    {

        $mulai      =   $request->mulai ?? date('Y-m-d');
        $sampai     =   $request->sampai ?? date('Y-m-d');
        $regu       =   $request->regu ?? '' ;
        $asaltujuan =   $request->asal_tujuan ?? '' ;
        $type       =   $request->type ?? '' ;

        $keluar      =   Chiller::whereIn('type', ['pengambilan-bahan-baku', 'alokasi-order', 'musnahkan'])
                        ->whereBetween('tanggal_produksi', [$mulai,$sampai])
                        ->where(function($query) use ($regu){
                            if ($regu) {
                                $query->where('regu', $regu);
                            }
                        })
                        ->where(function($query) use ($asaltujuan){
                            if ($asaltujuan) {
                                $query->where('asal_tujuan', $asaltujuan);
                            }
                        })
                        ->where(function($query) use ($type){
                            if ($type) {
                                $query->where('type', $type);
                            }
                        })
                        ->orderBy('item_name', 'ASC')
                        ->orderBy('tanggal_produksi', 'ASC')
                        ->get();

        if ($request->key == 'unduh') {
            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=Chiller Bahan Baku Keluar " . $mulai . " - " . $sampai . ".csv");
            $fp = fopen('php://output', 'w');
            fputcsv($fp, ["sep=,"]);

            $data = array(
                "No",
                "Tanggal",
                "Item",
                "Regu",
                "Ekor",
                "Berat",
                "Asal",
                "Status",
                "Tipe",
            );
            fputcsv($fp, $data);

            foreach ($keluar as $i => $item) {
                $data = array(
                    ++$i,
                    $item->tanggal_produksi,
                    $item->item_name,
                    ($item->regu ?? ''),
                    // str_replace(".", ",", $item->qty_item),
                    // str_replace(".", ",", $item->berat_item),
                    number_format($item->qty_item,2),
                    number_format($item->berat_item,2),
                    $item->jenis ? : $item->chilprod->prodpur->nama_po,
                    $item->tujuan,
                    str_replace("-", " ", $item->type)
                );
                fputcsv($fp, $data);
            }

            fclose($fp);
            return "";
        } else {
            return view('admin.pages.chiler.keluar', compact('keluar', 'mulai', 'sampai'));
        }
    }

    public function chiller_stock(Request $request)
    {
        // return $request->all();
        $mulai      =   $request->mulai ?? date('Y-m-d');
        $sampai     =   $request->sampai ?? date('Y-m-d');
        $kosong     =   $request->kosong;

        $sql      =   Chiller::where('type', 'bahan-baku')
                        ->where(function($query) use ($kosong){
                            if ($kosong == "true") {
                                $query->where('stock_berat', '<=', '0') ;
                            } else {
                                $query->where('stock_berat', '>', '0') ;
                            }
                        })
                        ->whereIn('asal_tujuan', ['evisgabungan','gradinggabungan', 'karkasbeli','thawing','retur'])
                        ->whereBetween('tanggal_produksi', [$mulai,$sampai])
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
                                GROUP BY chiller_id
                                ");
            $musnahkan  = DB::select("select item_id, SUM(qty) AS total_qty_musnahkan, ROUND(sum(berat),2) AS total_berat_musnahkan 
                                FROM musnahkan_temp JOIN musnahkan on musnahkan.id=musnahkan_temp.musnahkan_id WHERE gudang_id IN (2,4,23,24) AND item_id IN(".$stringData.")
                                AND musnahkan.deleted_at IS NULL GROUP BY item_id ");
        }
        $arraymodification      = [];
        foreach($arrayData as $data){
            $total_qty_alokasi      = 0;
            $total_berat_alokasi    = 0;
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
                'id'                    => $data->id,
                'tanggal_produksi'      => $data->tanggal_produksi,
                'item_name'             => $data->item_name,
                'asal_tujuan'           => $data->asal_tujuan,
                'label'                 => $data->label,
                'no_mobil'              => $data->no_mobil,
                'qty_item'              => $data->qty_item,
                'berat_item'            => floatval($data->berat_item),
                'total_qty_alokasi'     => $total_qty_alokasi,
                'total_berat_alokasi'   => $total_berat_alokasi,
                'total_qty_abf'         => $total_qty_abf,
                'total_berat_abf'       => $total_berat_abf,
                'total_qty_freestock'   => $total_qty_freestock,
                'total_berat_freestock' => $total_berat_freestock,
                'total_qty_musnahkan'   => $total_qty_musnahkan,
                'total_berat_musnahkan' => $total_berat_musnahkan,
                'sisaQty'               => $data->qty_item - $total_qty_alokasi - $total_qty_abf - $total_qty_freestock - $total_qty_musnahkan,
                'sisaBerat'             => $data->berat_item - $total_berat_alokasi - $total_berat_abf - $total_berat_freestock - $total_berat_musnahkan
            ];
        }
        $stock                      = json_decode(json_encode($arraymodification));
        // $stock                 = $this->paginate($stock,10);
        // dd($stock);
        if ($request->key == 'unduh') {
            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=Chiller Bahan Baku " . $mulai . " - " . $sampai . ".csv");
            $fp = fopen('php://output', 'w');
            fputcsv($fp, ["sep=,"]);

            $data = array(
                "No",
                "Tanggal",
                "Item",
                "Ekor Awal",
                "Berat Awal",
                "Ekor Sisa",
                "Berat Sisa",
                "Status",
            );
            fputcsv($fp, $data);

            foreach ($stock as $i => $item) {
                $data = array(
                    ++$i,
                    $item->tanggal_produksi,
                    $item->item_name,
                    number_format($item->qty_item,2),
                    number_format($item->berat_item,2),
                    number_format($item->qty_item - $item->total_qty_alokasi - $item->total_qty_abf - $item->total_qty_freestock - $item->total_qty_musnahkan ?? '0',2),
                    $item->berat_item - $item->total_berat_alokasi - $item->total_berat_abf - $item->total_berat_freestock - $item->total_berat_musnahkan,
                    $item->tujuan ?? '',
                );
                fputcsv($fp, $data);
            }

            fclose($fp);
            return "";
        } else {
            return view('admin.pages.chiler.stock', compact('stock', 'mulai', 'sampai','kosong'));
        }

    }

    public function stockindex()
    {
        return view('admin.pages.chillerstock.index');
    }

    public function stockchiller(Request $request)
    {
        $tglmulai = $request->tglmulai ?? date('Y-m-d');
        $tglend = $request->tglend ?? date('Y-m-d');
        $stock  =   Chiller::whereIn('status', [1, 2])
                        ->whereIn('asal_tujuan', ['gradinggabungan', 'karkasbeli', 'evisgabungan'])
                        ->where('type', 'bahan-baku')
                        ->whereBetween('tanggal_produksi', [$tglmulai, $tglend])
                        ->get();

        return view('admin.pages.chillerstock.chillerstock', compact('stock', 'tglmulai','tglend'));
    }

    public function stockedit(Request $request)
    {
        $data = Chiller::find($request->id);

        $data->stock_berat = $request->berat;
        $data->stock_item = $request->qty;
        $data->save();

        return back()->with('status', 1)->with('message', 'Berhasil Edit');
    }


    public function injectevis(Request $request)
    {

        $tanggal = $request->tanggal ?? date('Y-m-d');
        $data   =   Chiller::where('asal_tujuan', 'evisgabungan')
                    ->where('tanggal_produksi', '=', $tanggal)
                    ->get();

        foreach ($data as $row) {
            $total  =   Evis::where('item_id', $row->item_id)
                        ->where('tanggal_potong', $row->tanggal_potong)
                        ->sum('total_item');

            $berat  =   Evis::where('item_id', $row->item_id)
                        ->where('tanggal_potong', $row->tanggal_potong)
                        ->sum('berat_item');

            $row->qty_item     =   $total ;
            $row->berat_item   =   $berat ;
            $row->save() ;
        }

        $data   =   Chiller::where('asal_tujuan', 'gradinggabungan')
                    ->where('tanggal_produksi', '=', $tanggal)
                    ->get();

        foreach ($data as $row) {
            $total  =   Grading::whereIn('trans_id', Production::select('id')->where('prod_tanggal_potong', $row->tanggal_potong))
                        ->where('item_id', $row->item_id)
                        ->sum('total_item');

            $berat  =   Grading::whereIn('trans_id', Production::select('id')->where('prod_tanggal_potong', $row->tanggal_potong))
                        ->where('item_id', $row->item_id)
                        ->sum('berat_item');

            $row->qty_item     =   $total ;
            $row->berat_item   =   $berat ;
            $row->save() ;
        }

        return $data ;
    }

    public function inject_chiller(Request $request)
    {

        $tanggal        = $request->tanggal ?? date('Y-m-d');
        $asaltujuan     = explode(",", $request->asaltujuan) ?? ['gradinggabungan'];

        $data   =   Chiller::whereIn('asal_tujuan', $asaltujuan)
                    ->where('tanggal_produksi', '=', $tanggal)
                    ->get();

        foreach ($data as $row) {
            Chiller::recalculate_chiller($row->id);
        }

        return $data ;
    }

    public function tukarindex($id)
    {
        $data   =   Chiller::find($id);
        $item   =   Item::where('nama', 'like', '%memar%')->where('category_id', '1')->get();
        return view('admin.pages.chiler.tukaritem', compact('data','item'));
    }

    public function tukarstore(Request $request, $id)
    {



        DB::beginTransaction();
        $data   =   Chiller::find($id);
        $data->stock_berat = $data->stock_berat - $request->beratbaru;
        $data->stock_item = $data->stock_item - $request->qtybaru;


        $keluar = new Chiller;
        $item_lama   =   Item::find($data->item_id);
        $item   =   Item::find($request->itembaru);

        if (substr($data->chillitem->sku, 0, 5) == "12111" || substr($item->sku, 0, 5) == "12113") {

        } else {
            return back()->with('status', 2)->with('message', 'Item tukar hanya karkas dan memar!');
        }

        $keluar->table_name         =   'chiller';
        $keluar->table_id           =   $data->id;
        $keluar->asal_tujuan        =   'tukar_item';
        $keluar->item_id            =   $item_lama->id;
        $keluar->item_name          =   $item_lama->nama;
        $keluar->jenis              =   'keluar';
        $keluar->type               =   'pengambilan-bahan-baku';
        $keluar->tanggal_potong     =   $data->tanggal_potong;
        $keluar->tanggal_produksi   =   $data->tanggal_potong;
        $keluar->qty_item           =   $request->qtybaru;
        $keluar->berat_item         =   $request->beratbaru;
        $keluar->stock_item         =   $request->qtybaru;
        $keluar->stock_berat        =   $request->beratbaru;
        $keluar->status             =   4;
        $keluar->save();

        $masuk = new Chiller;
        $masuk->table_name         =   'chiller';
        $masuk->table_id           =   $data->id;
        $masuk->asal_tujuan        =   'tukar_item';
        $masuk->item_id            =   $request->itembaru;
        $masuk->item_name          =   $item->nama;
        $masuk->jenis              =   'masuk';
        $masuk->type               =   'bahan-baku';
        $masuk->tanggal_potong     =   $data->tanggal_potong;
        $masuk->tanggal_produksi   =   $data->tanggal_potong;
        $masuk->qty_item           =   $request->qtybaru;
        $masuk->berat_item         =   $request->beratbaru;
        $masuk->stock_item         =   $request->qtybaru;
        $masuk->stock_berat        =   $request->beratbaru;
        $masuk->status             =   2;
        $masuk->save();
        $data->save();

        $gabungan   =   Chiller::where('asal_tujuan', 'gradinggabungan')->where('item_id', $request->itembaru)->where('tanggal_potong', $data->tanggal_potong)->first();

        if(!$gabungan){

            $gabungan = new Chiller;
            $gabungan->table_name         =   'chiller';
            $gabungan->table_id           =   $data->id;
            $gabungan->asal_tujuan        =   'gradinggabungan';
            $gabungan->item_id            =   $request->itembaru;
            $gabungan->item_name          =   $item->nama;
            $gabungan->jenis              =   'masuk';
            $gabungan->type               =   'bahan-baku';
            $gabungan->tanggal_potong     =   $data->tanggal_potong;
            $gabungan->tanggal_produksi   =   $data->tanggal_potong;
            $gabungan->qty_item           =   0;
            $gabungan->berat_item         =   0;
            $gabungan->stock_item         =   0;
            $gabungan->stock_berat        =   0;
            $gabungan->status             =   2;
            $gabungan->save();

            // DB::rollBack();
            // return back()->with('status', 2)->with('message', 'Item baru tidak tersedia!');
            // $gabungan = $data->replicate();
            // $gabungan->item_id = $masuk->item_id; // the new project_id
            // $gabungan->item_id = $request->itembaru; // the new project_id
            // $gabungan->save();
        }

        $gabungan->stock_item         =   $gabungan->stock_item + $request->qtybaru;
        $gabungan->stock_berat        =   $gabungan->stock_berat + $request->beratbaru;
        $gabungan->save();

        Netsuite::wo_tukar_bb('chiller', $data->id, $keluar, $masuk);

        DB::commit();

        return back()->with('status', 1)->with('message', 'Berhasil Tukar Item');
    }

    public function tracing_bahan_baku(Request $request){

        $tanggal            =   $request->tanggal ?? date('Y-m-d');
        $gradinggabungan    =   DB::table('VW_Tracing_Item_Chiller')->where('asal_tujuan', 'gradinggabungan')
                                ->where('tanggal_produksi', $tanggal)
                                ->get();
        $evisgabungan    =   DB::table('VW_Tracing_Item_Chiller')->where('asal_tujuan', 'evisgabungan')
                                ->where('tanggal_produksi', $tanggal)
                                ->get();

        $stock  =   DB::table('VW_Tracing_Item_Chiller')->whereIn('status', [1, 2])
                ->whereDate('tanggal_produksi', $tanggal)
                ->whereIn('type', ['bahan-baku', 'hasil-produksi'])
                ->whereIn('asal_tujuan', ['karkasbeli','thawing','retur', 'free_stock'])
                ->get();

        return view('admin.pages.chiler.tracing_bahan_baku', compact('tanggal', 'gradinggabungan','evisgabungan', 'stock'));
    }


    public function recustomer(Request $request)
    {
        if ($request->key == 'selonjor') {

            foreach (Chiller::whereDate('tanggal_produksi', $request->tanggal)->where('label', '!=', NULL)->get() as $list) {
                $label  =   json_decode($list->label);
                if (isset($label->sub_item)) {

                    if (($label->sub_item == 'selonjor') || ($label->sub_item == 'Selonjor')) {
                        $list->selonjor     =   1 ;

                        $label      =   [
                            'plastik'       =>  [
                                'sku'       =>  $label->plastik->sku,
                                'jenis'     =>  $label->plastik->jenis,
                                'qty'       =>  $label->plastik->qty
                            ],
                            'parting'       =>  [
                                'qty'       =>  $label->parting->qty
                            ],
                            'additional'    =>  [
                                'tunggir'   =>  $label->additional->tunggir,
                                'lemak'     =>  $label->additional->lemak,
                                'maras'     =>  $label->additional->maras,
                            ],
                            'sub_item'      =>  NULL
                        ];

                        $list->label        =   json_encode($label);
                        $list->save() ;

                        $temp               =   $list->chillertofreestocktemp;
                        if ($temp) {
                            $temp->label    =   $list->label;
                            $temp->save();
                        }

                        foreach ($list->ambil_abf as $row) {
                            $row->selonjor  =   $list->selonjor ;
                            $row->save() ;

                            foreach ($row->abf_gudang as $item) {
                                $item->selonjor =   $row->selonjor ;
                                $item->save() ;
                            }
                        }
                    }

                }
            }

            return 'OK' ;

        } else {

            foreach (FreestockTemp::whereDate('tanggal_produksi', $request->tanggal)->where('regu', '!=', 'byproduct')->whereIn('freestock_id', Freestock::select('id')->whereIn('status', [1, 2]))->get() as $row) {
                $label  =   json_decode($row->label);
                $exp    =   explode(' || ', $label->sub_item);

                if (isset($exp[1])) {
                    $customer   =   (str_replace('||', '', $exp[0]) == '') ? 0 : str_replace('||', '', $exp[0]) ;
                    $keterangan =   $exp[1] ? $exp[1] : NULL ;
                } else {
                    $etp    =   explode('||', $label->sub_item);
                    $customer   =   $etp[0];
                    $keterangan =   $etp[1] ?? '';
                }

                $array  =   json_encode([
                            'plastik'       =>  [
                                'sku'       =>  $label->plastik->sku,
                                'jenis'     =>  $label->plastik->jenis,
                                'qty'       =>  $label->plastik->qty
                            ],
                            'parting'       =>  [
                                'qty'       =>  $label->parting->qty
                            ],
                            'additional'    =>  [
                                'tunggir'   =>  $label->additional->tunggir,
                                'lemak'     =>  $label->additional->lemak,
                                'maras'     =>  $label->additional->maras,
                            ],
                            'sub_item'      =>  $keterangan
                        ]);

                $row->label                 =   $array ;
                $row->customer_id           =   $row->customer_id ? $row->customer_id : (Customer::where('nama', $customer)->first()->id ?? 0) ;
                $row->save() ;
            }

            foreach (Chiller::where('tanggal_produksi', NULL)->whereDate('created_at', $request->tanggal)->get() as $list) {
                $list->tanggal_produksi         =   $request->tanggal ;
                $list->save() ;
            }

            foreach (Chiller::whereDate('tanggal_produksi', $request->tanggal)->where('label', '!=', NULL)->get() as $list) {
                $label  =   json_decode($list->label) ;
                if (isset($label->sub_item)) {

                    if (($list->customer_id == '') AND ($list->customer_id == NULL)) {
                        $exp    =   explode(' || ', $label->sub_item);
                        if (isset($exp[1])) {
                            $customer   =   (str_replace('||', '', $exp[0]) == '') ? 0 : str_replace('||', '', $exp[0]) ;
                            $keterangan =   $exp[1] ? $exp[1] : NULL ;
                        } else {
                            $etp    =   explode('||', $label->sub_item);
                            $customer   =   $etp[0];
                            $keterangan =   $etp[1] ?? '';
                        }

                        $label      =   [
                            'plastik'       =>  [
                                'sku'       =>  $label->plastik->sku,
                                'jenis'     =>  $label->plastik->jenis,
                                'qty'       =>  $label->plastik->qty
                            ],
                            'parting'       =>  [
                                'qty'       =>  $label->parting->qty
                            ],
                            'additional'    =>  [
                                'tunggir'   =>  $label->additional->tunggir,
                                'lemak'     =>  $label->additional->lemak,
                                'maras'     =>  $label->additional->maras,
                            ],
                            'sub_item'      =>  $keterangan
                        ];

                        $list->label        =   json_encode($label) ;

                        $list->customer_id  =   $customer ? (Customer::where('nama', $customer)->first()->id ?? 0) : ($list->customer_id ?? 0) ;
                        $list->save() ;

                        $temp               =   $list->chillertofreestocktemp ;
                        if ($temp) {
                            $temp->label        =   $list->label ;
                            $temp->save() ;
                        }
                    }

                }

            }

            // Inject customer ABF
            foreach (Abf::whereDate('tanggal_masuk', $request->tanggal)->whereIn('table_name', ['free_stocktemp', 'chiller'])->get() as $row) {
                if ($row->table_name == 'free_stocktemp') {
                    $data   =   $row->abf_freetemp;

                    if ($data) {
                        $label  =   json_decode($data->label);
                        $exp    =   explode(' || ', $label->sub_item);

                        if (isset($exp[1])) {
                            $customer   =   (str_replace('||', '', $exp[0]) == '') ? NULL : str_replace('||', '', $exp[0]);
                            $keterangan =   $exp[1] ? (str_replace('||', '', $exp[1]) == '') : NULL;
                        } else {
                            $etp    =   explode('||', $label->sub_item);
                            $customer   =   $etp[0];
                            $keterangan =   isset($etp[1]) ? (str_replace('||', '', $etp[1]) == '') : '';
                        }

                        $row->customer_id   =   $data->customer_id ?? ($customer ? (Customer::where('nama', $customer)->first()->id ?? NULL) : $data->customer_id);
                        $row->save();

                        $array      =   [
                            'plastik'       =>  [
                                'sku'       =>  $label->plastik->sku,
                                'jenis'     =>  $label->plastik->jenis,
                                'qty'       =>  $label->plastik->qty
                            ],
                            'parting'       =>  [
                                'qty'       =>  $label->parting->qty
                            ],
                            'additional'    =>  [
                                'tunggir'   =>  $label->additional->tunggir,
                                'lemak'     =>  $label->additional->lemak,
                                'maras'     =>  $label->additional->maras,
                            ],
                            'sub_item'      =>  $keterangan
                        ];

                        $data->label            =   json_encode($array) ;
                        $data->customer_id      =   $row->customer_id ;
                        $data->save() ;
                    }

                }

                if ($row->table_name == 'chiller') {
                    $data               =   $row->abf_chiller ;
                    $row->customer_id   =   $data->customer_id ;
                    $row->save() ;
                }
            }

            // Inject customer Product Gudang
            foreach (Product_gudang::whereDate('production_date', $request->tanggal)->get() as $row) {
                $data                   =   $row->gudangabf ;
                if ($data) {
                    $row->customer_id       =   $data->customer_id ;

                    if ($data->table_name == 'chiller') {
                        $label          =   json_decode($data->abf_chiller->label);
                        $row->sub_item  =   $label->sub_item ?? NULL ;
                    }

                    if ($data->table_name == 'free_stocktemp') {
                        $label          =   json_decode($data->abf_freetemp->label);
                        $row->sub_item  =   $label->sub_item ?? NULL;
                    }

                } else {
                    $row->kategori      =   '###' ;
                }
                $row->save();
            }

        }

        return 'OK' ;
    }

    public function soh(Request $request)
    {
        if ($request->key == 'view') {
            $tanggal=   $request->tanggal ? $request->tanggal : date("Y-m-d") ;
            // $data   =   Chiller::select('item_id', 'item_name', 'type', 'customer_id',)
            //             ->where('tanggal_produksi', '<=', $tanggal)
            //             ->where('status', 2)
            //             ->whereIn('type', ['bahan-baku', 'hasil-produksi'])
            //             ->groupBy('item_id')
            //             ->groupBy('type')
            //             ->groupBy('customer_id')
            //             ->orderBy('types')
            //             ->get() ;
            
            $data   = Chiller::chiller_soh_update($tanggal);
            // $data       = DB::select("CALL getChillerSOH('$tanggal')");
            
            return view('admin.pages.chiler.soh.data', compact('data', 'tanggal'));
        } else {
            return view('admin.pages.chiler.soh.index');
        }
    }

    public function injectRecalculate(Request $request){
        $filter     = $request->filter;
        $data       = Chiller::select('id','asal_tujuan','item_name','tanggal_produksi','stock_berat')
                        ->where('tanggal_produksi','>=','2023-12-01')
                        // ->where('tanggal_produksi','>=',Applib::DefaultTanggalAudit())
                        ->where(function($q) use ($filter){
                            if($filter != null){
                                    $explode    = explode(",",$filter);
                                    $stringData = sprintf("'%s'", implode("', '", $explode));
                                    $q->whereRaw('asal_tujuan IN ('.$stringData.')');
                                }
                            })
                        ->whereRaw('LENGTH(stock_berat) > 9') 
                        ->where('status',2)
                        ->whereNull('deleted_at')
                        ->take(10)
                        ->orderBy('id','DESC')
                        ->get();

        if($request->key == "view"){
            $output['data'] = [];
            foreach ($data as $val) {
                $output['data'][] =
                    array(
                        $val->id,
                        $val->item_name,
                        $val->asal_tujuan,
                        $val->tanggal_produksi,
                        $val->stock_berat
                    );
            }
            return response()->json($output);
        }   

        if($request->key == "inject"){
            if($data){
                foreach($data as $key => $val){
                    Chiller::injectRecalculateData($val->id);
                }
            }
            if($data->count() > 0){
                $output['status'] = 200;
                $output['msg']    = 'Berhasil di recalculate';
                return $output;
            }else{
                $output['status'] = 400;
                $output['msg']    = 'Tidak Ada Data Yang di Recalculate';
                return $output;
            }
        }
        
    }
}
