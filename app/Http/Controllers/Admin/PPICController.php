<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Abf;
use App\Models\AppKey;
use App\Models\Bahanbaku;
use App\Models\Category;
use App\Models\Chiller;
use App\Models\Freestock;
use App\Models\FreestockList;
use App\Models\FreestockTemp;
use App\Models\Grading;
use App\Models\Gudang;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Item;
use App\Models\Log;
use App\Models\Netsuite;
use App\Models\OrderItemLog;
use App\Models\Product_gudang;
use App\Models\Production;
use App\Models\Purchasing;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PPICController extends Controller
{

    public function index(Request $request)
    {
        // if (User::setIjin(7)) {
            $tanggal        =   $request->tanggal ?? date('Y-m-d');
            $tanggalend     =   $request->tanggalend ?? date('Y-m-d');

            if ($request->view == 'order_pending') {

                $pending        =   Order::whereBetween('tanggal_so', [$tanggal, $tanggalend])
                                    ->whereNull('status')
                                    ->orderBy('no_so', 'ASC')
                                    ->paginate(10);

                return view('admin.pages.ppic.component.order_pending_view', compact('pending', 'tanggal','tanggalend'));
            } else

            if ($request->view  ==  'order_view') {

                $order      =   Order::whereBetween('tanggal_so', [$tanggal, $tanggalend])
                                ->whereIn('status', [2, 3, 4, 5, 6, 7, 8, 9, 10])
                                ->orderBy('id', 'DESC')
                                ->paginate(10);

                return view('admin.pages.ppic.component.order_view', compact('tanggal', 'order','tanggalend'));
            } else

            if ($request->view == 'sisa_chiller') {

                $chiller    =   Chiller::whereIn('asal_tujuan', ['gradinggabungan', 'free_stock'])
                                ->where('type', 'bahan-baku')
                                ->where('stock_item', '>', 0)
                                ->whereBetween('tanggal_potong', [$tanggal, $tanggalend])
                                ->orderBy('item_name', 'ASC')
                                ->get();

                $plastik    =   Item::where('category_id', 25)
                                ->where('subsidiary', env('NET_SUBSIDIARY', 'EBA'))
                                ->get();

                return view('admin.pages.ppic.component.sisa_chiller_view', compact('tanggal', 'chiller', 'plastik','tanggalend'));
            } else
            if ($request->view == 'chiller_fg') {

                $chiller_fg =   Chiller::where('type', 'hasil-produksi')
                                ->where('jenis', 'masuk')
                                ->whereBetween('tanggal_produksi', [$tanggal, $tanggalend])
                                ->where(function($query) use ($request){
                                    if ($request->abf == "true") {
                                        $query->where('kategori', NULL);
                                    } else {
                                        $query->where('kategori', "1");
                                    }
                                })
                                ->where(function($query) use ($request) {
                                    if ($request->selonjor == 'true') {
                                        $query->where('selonjor', '!=', NULL) ;
                                    }
                                })
                                ->where(function($query) use ($request){
                                    if ($request->cari) {
                                        $query->orWhere('item_name', 'like', '%' . $request->cari . '%');
                                        $query->orWhere('stock_item', 'like', '%' . $request->cari . '%');
                                        $query->orWhere('stock_berat', 'like', '%' . $request->cari . '%');
                                        $query->orWhere('tanggal_produksi', 'like', '%' . $request->cari . '%');
                                        $query->orWhere('label', 'like', '%' . $request->cari . '%');
                                    }
                                })
                                ->where(function($query) use ($request) {
                                    if ($request->status == 'ready') {
                                        $query->where('stock_berat', '>', 0);
                                    }
                                    if ($request->status == 'dipindahkan') {
                                        $query->where('stock_berat', '<=', 0);
                                    }
                                });

                if ($request->field == 'item_name') {
                    $chiller_fg =   $chiller_fg->orderBy("item_name", $request->orderby == 'asc' ? 'ASC' : 'DESC') ;
                }

                if ($request->field == 'stock_item') {
                    $chiller_fg =   $chiller_fg->orderBy("stock_item", $request->orderby == 'asc' ? 'ASC' : 'DESC') ;
                }

                if ($request->field == 'stock_berat') {
                    $chiller_fg =   $chiller_fg->orderBy("stock_berat", $request->orderby == 'asc' ? 'ASC' : 'DESC') ;
                }

                if ($request->field == 'tanggal_produksi') {
                    $chiller_fg =   $chiller_fg->orderBy("tanggal_produksi", $request->orderby == 'asc' ? 'ASC' : 'DESC') ;
                }

                if ($request->action == 'unduh') {
                    header("Content-type: application/csv");
                    header("Content-Disposition: attachment; filename=Chiller to ABF " . $tanggal . " - " . $tanggalend . ".csv");
                    $fp = fopen('php://output', 'w');
                    fputcsv($fp, ["sep=,"]);

                    $data = array(
                        "No",
                        "Tanggal",
                        "Item",
                        "Customer",
                        "Plastik",
                        "Regu",
                        "Ekor",
                        "Berat",
                        "Asal",
                        "Status",
                        "Tipe",
                    );
                    fputcsv($fp, $data);

                    foreach ($chiller_fg->get() as $i => $item) {

                        $exp = json_decode($item->label);

                        $plastik = "";
                        $customer = "";
                        if ($exp){
                            if ($exp->plastik->jenis ?? ''):
                                $plastik = $exp->plastik->jenis. " ( ".$exp->plastik->qty.")";
                            endif;
                            if (isset($exp->additional)):
                                $customer = $exp->sub_item;
                            endif;

                            $data = array(
                                ++$i,
                                $item->tanggal_produksi,
                                $item->item_name,
                                $customer,
                                $plastik,
                                ($item->regu ?? ''),
                                str_replace(".", ",", $item->qty_item),
                                str_replace(".", ",", $item->berat_item),
                                $item->jenis ?: $item->chilprod->prodpur->nama_po,
                                $item->tujuan,
                                str_replace("-", " ", $item->type)
                            );
                            fputcsv($fp, $data);
                        }
                    }

                    fclose($fp);
                    return "";
                } else {
                    $chiller_fg =   $chiller_fg->paginate(15);

                    return view('admin.pages.ppic.component.chiller_fg_view', compact('tanggal', 'chiller_fg','tanggalend'));
                }
            } else

            if ($request->view == 'chiller_penyiapan') {

                $chiller_penyiapan  =   Chiller::whereIn('table_id', FreestockTemp::select('id')->where('kategori', 0))
                                        ->whereIn('table_name', ['free_stocktemp'])
                                        ->where('type', 'hasil-produksi')
                                        ->whereBetween('tanggal_produksi', [$tanggal, $tanggalend])
                                        ->orderBy('item_name', 'ASC')
                                        ->get();

                return view('admin.pages.ppic.component.chiller_penyiapan_view', compact('tanggal', 'chiller_penyiapan','tanggalend'));
            } else
            if ($request->view == 'non_lb') {

                $purchase   =   Purchasing::whereBetween('tanggal_potong', [$tanggal, $tanggalend])
                                ->whereIn('type_po',['PO Karkas', 'PO Non Karkas', 'PO Evis'])
                                ->get();

                return view('admin.pages.ppic.component.non_lb_view', compact('tanggal', 'purchase','tanggalend'));
            } else
            if ($request->view == 'lb') {

                $purchase   =   Purchasing::whereBetween('tanggal_potong', [$tanggal, $tanggalend])
                                ->where('type_po', 'PO LB')
                                ->get();

                return view('admin.pages.ppic.component.lb', compact('tanggal', 'purchase','tanggalend'));
            } else {

                $lb         =   Purchasing::whereBetween('tanggal_potong', [$tanggal, $tanggalend])
                                ->where('type_po', 'PO LB')
                                ->get();

                $ukuran     =   Production::where('no_urut', '!=', NULL)
                                ->whereBetween('lpah_tanggal_potong', [$tanggal, $tanggalend])
                                ->where('po_jenis_ekspedisi', '<>', null)
                                ->where('sc_status', 3)
                                ->orderBy('no_urut', 'ASC')
                                ->orderBy('sc_tanggal_masuk', 'ASC')
                                ->get();

                $chiller    =   Chiller::select('item_name', 'item_id', 'stock_berat', 'stock_item')
                                // ->sum('stock_item')
                                ->whereBetween('tanggal_produksi', [Carbon::parse($tanggal)->addDays(-3), $tanggal])
                                ->orderBy('item_name', 'ASC')
                                ->groupBy('item_name', 'item_id')
                                ->where('asal_tujuan', 'gradinggabungan')
                                ->get();
                                // dd($chiller);

                $thawing    =   Chiller::select('item_name', 'item_id', 'stock_berat', 'stock_item')
                                ->whereBetween('tanggal_produksi', [Carbon::parse($tanggal)->addDays(-3), $tanggal])
                                ->orderBy('item_name', 'ASC')
                                ->groupBy('item_name', 'item_id')
                                ->where('asal_tujuan', 'thawing')
                                ->get();


                return view('admin.pages.ppic.index', compact('tanggal', 'lb', 'ukuran', 'chiller', 'thawing','tanggalend'));
            }

        // }
        // return redirect()->route("index");
    }



    public function kepala(Request $request)
    {
        if (User::setIjin(7)) {
            $tanggal =  $request->tanggal ?? date('Y-m-d');
            $order          =   Order::whereDate('tanggal_so', $tanggal)
                                ->whereIn('status', [2, 3, 4, 5, 6, 7, 8, 9, 10])
                                ->orderBy('id', 'DESC')
                                ->get();

            $pending        =   Order::where('status', 1)
                                ->orderBy('id', 'DESC')
                                ->get();

            $chiller        =   Chiller::whereIn('table_name', ['grading', 'free_stock'])
                                ->where('type', 'bahan-baku')
                                ->whereNotIn('asal_tujuan', ['evisampingan','baru'])
                                ->where('stock_item', '>', 0)
                                ->where('tanggal_potong', $tanggal)
                                ->get();

            $plastik        =   Item::where('category_id', 25)
                                ->where('subsidiary', env('NET_SUBSIDIARY', 'EBA'))
                                ->get();

            $chiller_fg     =   Chiller::whereIn('table_name', ['free_stock_temp'])
                                ->where('type', 'hasil-produksi')
                                ->where('tanggal_potong', $tanggal)
                                ->get();

            $fulfillment    =   Order::whereIn('id', OrderItem::select('order_id')
                                ->where('status', '>=', 2))
                                ->get();

            return view('admin/pages/ppic/kepala_show', compact('order', 'pending', 'chiller', 'fulfillment', 'tanggal', 'plastik', 'chiller_fg'));
        }
        return redirect()->route("index");
    }

    public function evaluasi(Request $request)
    {
        if (User::setIjin(7)) {

            $parting    =   FreestockTemp::select(DB::raw("SUM(qty) AS jumlah", DB::raw("SUM(berat) AS kg")))
                            ->whereIn('id', Chiller::select('table_id')
                            ->where('table_name', 'free_stocktemp')
                            ->where('type', 'hasil-produksi')
                            ->where('regu', 'parting'))
                            ->whereDate('tanggal_produksi', $request->tanggal)
                            ->first();

            $marinasi   =   FreestockTemp::select(DB::raw("SUM(qty) AS jumlah", DB::raw("SUM(berat) AS kg")))
                            ->whereIn('id', Chiller::select('table_id')
                            ->where('table_name', 'free_stocktemp')
                            ->where('type', 'hasil-produksi')
                            ->where('regu', 'marinasi'))
                            ->whereDate('tanggal_produksi', $request->tanggal)
                            ->first();

            $whole      =   FreestockTemp::select(DB::raw("SUM(qty) AS jumlah", DB::raw("SUM(berat) AS kg")))
                            ->whereIn('id', Chiller::select('table_id')
                            ->where('table_name', 'free_stocktemp')
                            ->where('type', 'hasil-produksi')
                            ->where('regu', 'whole'))
                            ->whereDate('tanggal_produksi', $request->tanggal)
                            ->first();

            $frozen     =   FreestockTemp::select(DB::raw("SUM(qty) AS jumlah", DB::raw("SUM(berat) AS kg")))
                            ->whereIn('id', Abf::select('table_id')
                            ->where('table_name', 'free_stocktemp')
                            ->where('type', 'free-stock'))
                            ->whereDate('tanggal_produksi', $request->tanggal)
                            ->first();

            $bonless    =   FreestockTemp::select(DB::raw("SUM(qty) AS jumlah", DB::raw("SUM(berat) AS kg")))
                            ->whereIn('id', Chiller::select('table_id')
                            ->where('table_name', 'free_stocktemp')
                            ->where('type', 'hasil-produksi')
                            ->where('regu', 'boneless'))
                            ->whereDate('tanggal_produksi', $request->tanggal)
                            ->first();

            $evis       =   Chiller::select(DB::raw("SUM(stock_item) AS qty"), DB::raw("SUM(stock_berat) AS berat"))
                            ->where('table_name', 'free_stocktemp')
                            ->where('jenis', 'masuk')
                            ->where('type', 'hasil-produksi')
                            ->whereDate('tanggal_produksi', $request->tanggal)
                            ->first();

            $total = array(
                'partitem'      =>  $parting->jumlah,
                'partberat'     =>  $parting->kg,
                'marinasiitem'  =>  $marinasi->jumlah,
                'marinasiberat' =>  $marinasi->kg,
                'wholeitem'     =>  $whole->jumlah,
                'wholeberat'    =>  $whole->kg,
                'frozenitem'    =>  $frozen->jumlah,
                'frozenberat'   =>  $frozen->kg,
                'bonlessitem'   =>  $bonless->jumlah,
                'bonlessberat'  =>  $bonless->kg,
                'evisitem'      =>  $evis->qty,
                'evisberat'     =>  $evis->berat
            );

            return view('admin.pages.kepala_produksi.evaluasi', compact('total'));
        }
        return redirect()->route("index");
    }


    public function orderpendingshow(Request $request)
    {
        if (User::setIjin(7)) {
            $pending    =   Order::where('status', 1)
                ->orderBy('id', 'DESC')
                ->get();

            return view('admin/pages/kepala_produksi/order_pending_show', compact('pending'));
        }
        return redirect()->route("index");
    }

    public function ukuran()
    {
        if (User::setIjin(7)) {

            $data       =   Production::where('no_urut', '!=', NULL)
                // ->where(function ($query) use ($tanggal) {
                //     if ($tanggal != '') {
                //         $query->whereDate('sc_tanggal_masuk', $tanggal);
                //     } else {
                //         $query->whereDate('sc_tanggal_masuk', Carbon::now());
                //     }
                // })
                ->where('po_jenis_ekspedisi', '<>', null)
                ->where('sc_status', 3)
                ->orderBy('no_urut', 'ASC')
                ->orderBy('sc_tanggal_masuk', 'ASC')
                ->get();

            return view('admin.pages.kepala_produksi.ukuran', compact('data'));
        }
        return redirect()->route("index");
    }

    public function prosesukuran(Request $request)
    {
        if (User::setIjin(7)) {

            $data               =   Production::find($request->kode);
            $data->sc_status    =   1;
            $data->save();
        }
        return redirect()->route("index");
    }

    public function prosesukuranbatal(Request $request)
    {
        if (User::setIjin(7)) {

            $data               =   Production::find($request->kode);
            $data->sc_status    =   2;
            $data->save();
        }
        return redirect()->route("index");
    }

    public function store(Request $request)
    {
        if (User::setIjin(7)) {

            DB::beginTransaction();
            $data = Order::find($request->kode);

            $data->kp_proses        =   Carbon::now();
            $data->status           =   2;
            $data->keterangan       =   $request->catatan;
            $data->save();

            foreach ($data->daftar_order as $od) :

                $log_item                   = new OrderItemLog();
                $log_item->activity         = "kepala-produksi-proses";
                $log_item->order_item_id    = $od->id;
                $log_item->user_id          = Auth::user()->id;
                $log_item->key              = AppKey::generate();
                $log_item->save();

            endforeach;

            DB::commit();

            return back()->with('status', 1)->with('message', 'Data berhasil diperbaharui');
        }
        return redirect()->route("index");
    }

    public function storefreestock(Request $request)
    {
        if (User::setIjin(7)) {
            $total  =   0;
            $lebih  =   FALSE;
            for ($x = 0; $x < COUNT($request->x_code); $x++) {
                if ($request->qty[$x] > 0) {
                    $chiller    =   Chiller::find($request->x_code[$x]);
                    if (($chiller->stock_item - $chiller->request_pending) >= $request->qty[$x]) {
                        $total  +=  1;

                        $listfree                       =   new FreestockList;
                        $listfree->chiller_id           =   $request->x_code[$x];
                        $listfree->item_id              =   $chiller->item_id;
                        $listfree->qty                  =   $request->qty[$x];
                        $listfree->sisa                 =   $request->qty[$x];
                        $listfree->save();

                        $outchiller                     =   new Chiller;
                        $outchiller->table_name         =   'free_stocklist';
                        $outchiller->table_id           =   $listfree->id;
                        $outchiller->asal_tujuan        =   'free_stock';
                        $outchiller->item_id            =   $chiller->item_id;
                        $outchiller->item_name          =   $chiller->item_name;
                        $outchiller->jenis              =   'keluar';
                        $outchiller->type               =   'pengambilan-bahan-baku';
                        $outchiller->no_mobil           =   $chiller->no_mobil;
                        $outchiller->qty_item           =   $request->qty[$x];
                        $outchiller->tanggal_potong     =   Carbon::now();
                        $outchiller->tanggal_produksi   =   Carbon::now();
                        $outchiller->status             =   3;
                        $outchiller->save();
                    } else {
                        $lebih  =   TRUE;
                    }
                }
            }

            if ($total > 0) {
                $freestock              =   new Freestock;
                $freestock->nomor       =   Freestock::get_nomor();
                $freestock->tanggal     =   Carbon::now();
                $freestock->user_id     =   Auth::user()->id;
                $freestock->kategori    =   $request->kategori;
                $freestock->status      =   1;
                $freestock->save();

                FreestockList::where('freestock_id')->update([
                    'freestock_id'  =>  $freestock->id
                ]);

                return redirect()->to(url()->previous() . "#custom-tabs-freestock")->with('status', 1)->with('message', 'Buat free stock berhasil');
            } else {
                return redirect()->to(url()->previous() . "#custom-tabs-freestock")->with('status', 2)->with('message', $lebih ? 'Permintaan bahan baku melebihi stock tersedia' : 'Buat free stock gagal');
            }

            return redirect()->to(url()->previous() . "#custom-tabs-freestock");
        }
        return redirect()->route("index");
    }

    public function cartpending()
    {
        if (User::setIjin(7)) {
            $order  =   Order::where('status', NULL)
                ->get();

            return view('admin.pages.kepala-produksi.pending', compact('order'));
        }
        return redirect()->route("index");
    }

    public function lainnya()
    {
        if (User::setIjin(7)) {
            $lainnya        = Purchasing::whereIn('jenis_po', ['karkasfresh', 'ayamfrozen', 'pejantan', 'maklon'])->get();

            return view('admin.pages.kepala_produksi.lainnya', compact('lainnya'));
        }
        return redirect()->route("index");
    }

    public function laingrading(Request $request)
    {
        if (User::setIjin(7)) {
            $prod                =  Production::where('purchasing_id', $request->kode)->first();
            $purch               =  Purchasing::find($prod->purchasing_id);

            $grad                =   new Grading;
            $grad->trans_id      =   $prod->id;
            $grad->total_item    =   $prod->sc_ekor_do;
            $grad->berat_item    =   $prod->sc_berat_do;
            $grad->stock_item    =   $prod->sc_ekor_do;
            $grad->stock_berat   =   $prod->sc_berat_do;
            $grad->save();

            $purch->status   =  3;
            $purch->save();
        }
        return redirect()->route("index");
    }

    public function lainchiller(Request $request)
    {
        if (User::setIjin(7)) {
            $prod            =  Production::where('purchasing_id', $request->kode)->first();

            $purch           =  Purchasing::find($prod->purchasing_id);

            $chiller                    =   new Chiller;
            $chiller->production_id     =   $prod->id;
            $chiller->table_name        =   'production';
            $chiller->table_id          =   $prod->id;
            $chiller->asal_tujuan       =   $purch->jenis_po;
            $chiller->qty_item          =   $prod->sc_ekor_do;
            $chiller->berat_item        =   $prod->sc_berat_do;
            $chiller->stock_item        =   $prod->sc_ekor_do;
            $chiller->stock_berat       =   $prod->sc_berat_do;
            $chiller->tanggal_produksi  =   $purch->tanggal_potong;
            $chiller->no_mobil          =   $prod->no_urut;
            $chiller->status            =   1;
            $chiller->save();

            $purch->status   =  3;
            $purch->save();
        }
        return redirect()->route("index");
    }

    public function laingudang(Request $request)
    {
        if (User::setIjin(7)) {
            $prod            =  Production::where('purchasing_id', $request->kode)->first();

            $purch           =  Purchasing::find($prod->purchasing_id);

            $gudang                 =   new Product_gudang;
            $gudang->product_id     =   $prod->id;
            $gudang->table_name     =   'production';
            $gudang->table_id       =   $prod->id;
            $gudang->jenis_trans    =   'masuk';
            $gudang->qty_awal       =   $prod->sc_ekor_do;
            $gudang->berat_awal     =   $prod->sc_berat_do;
            $gudang->qty            =   $prod->sc_ekor_do;
            $gudang->berat          =   $prod->sc_berat_do;
            $gudang->status         =   1;
            $gudang->save();

            $purch->status   =  3;
            $purch->save();
        }
        return redirect()->route("index");
    }
    public function bahanbakubonless()
    {
        if (User::setIjin(7)) {
            $bonless        =   Order::whereIn('status', [2, 3, 4, 5])
                ->whereIn('id', OrderItem::select('order_id')
                    ->where('status', null)
                    ->whereIn('item_id', Item::select('id')
                        ->where('category_id', 5)))
                ->get();

            $bahanbaku              =   Bahanbaku::where('status', 1)->get();

            $bahanbakuselesai              =   Bahanbaku::where('status', 2)->get();

            $bhnbb = [];
            foreach ($bahanbaku as $i => $bahan) {
                $de = json_decode($bahan->data_chiller, FALSE);
                foreach ($de as $key) {
                    $bhnbb[] = Chiller::where('id', $key[0])->get();
                }
            }

            $bahanbonles    =   Item::whereIn('id', Chiller::select('item_id')
                ->where('table_name', 'grading')
                ->where('status', 2)
                ->where('jenis', 'masuk')
                ->where('stock_item', '>', 0)
                ->pluck('item_id'))
                ->get();

            $free   =   Chiller::where('jenis', 'masuk')->where('type', 'free')->get();

            return view('admin.pages.kepala_produksi.bahanbakubonles', compact('bhnbb', 'bahanbaku', 'bonless', 'bahanbonles', 'free', 'bahanbakuselesai'));
        }
        return redirect()->route("index");
    }

    public function bahanbakushow()
    {
        if (User::setIjin(7)) {
            $parting        =   Order::whereIn('status', [2, 3, 4, 5])
                ->whereIn('id', OrderItem::select('order_id')
                    ->whereIn('item_id', Item::select('id')
                        ->where('category_id', 2)))
                ->get();

            $marinasi       =   Order::whereIn('status', [2, 3, 4, 5])
                ->whereIn('id', OrderItem::select('order_id')
                    ->whereIn('item_id', Item::select('id')
                        ->where('category_id', 3)))
                ->get();

            $whole          =   Order::whereIn('status', [2, 3, 4, 5])
                ->whereIn('id', OrderItem::select('order_id')
                    ->whereIn('item_id', Item::select('id')
                        ->where('category_id', 1)))
                ->get();

            $frozen         =   Order::whereIn('status', [2, 3, 4, 5])
                ->whereIn('id', OrderItem::select('order_id')
                    ->whereIn('item_id', Item::select('id')
                        ->whereIn('category_id', [7, 8, 9, 10, 11])))
                ->get();

            $bonless        =   Order::whereIn('status', [2, 3, 4, 5])
                ->whereIn('id', OrderItem::select('order_id')
                    ->whereIn('item_id', Item::select('id')
                        ->where('category_id', 5)))
                ->get();

            $bahanbonles    =   Item::whereIn('id', Chiller::select('item_id')
                ->where('table_name', 'grading')
                ->where('status', 2)
                ->where('jenis', 'masuk')
                ->where('stock_item', '>', 0)
                ->pluck('item_id'))
                ->get();

            $free   =   Chiller::where('jenis', 'masuk')->where('type', 'free')->get();

            return view('admin/pages/kepala_produksi/data_show', compact('parting', 'marinasi', 'whole', 'frozen', 'bonless', 'bahanbonles', 'free'));
        }
        return redirect()->route("index");
    }

    public function storeboneles(Request $request)
    {
        if (User::setIjin(7)) {
            $x_code = json_decode(json_encode($request->x_code, FALSE));
            $qty =  json_decode(json_encode($request->qty, FALSE));

            $berat  =   0;
            $data   =   [];
            for ($x = 0; $x < COUNT($x_code); $x++) {
                if ($qty[$x] > 0) {
                    $berat      +=  $qty[$x];
                    $item       =   Chiller::find($x_code[$x]);

                    // $item->stock_item = ($item->stock_item - $qty[$x]);
                    // $item->save();

                    $chiler                     =   new Chiller;
                    $chiler->production_id      =   $item->production_id;
                    $chiler->asal_tujuan        =   'kepala_produksi';
                    $chiler->item_id            =   $item->item_id;
                    $chiler->item_name          =   $item->item_name;
                    $chiler->no_mobil           =   $item->no_mobil;
                    $chiler->qty_item           =   $qty[$x];
                    $chiler->jenis              =   'keluar';
                    $chiler->type               =   'pengambilan-bahan-baku';
                    $chiler->tanggal_potong     =   Carbon::now();
                    $chiler->tanggal_produksi   =   Carbon::now();
                    $chiler->status             =   3;
                    $chiler->save();

                    $data[]     =   [$chiler->id, $item->id, $qty[$x]];
                }
            }

            $bahan                          =   Bahanbaku::where('status', 1)->first();
            if ($bahan > '') {
                $out                        =   json_decode($bahan->data_chiller);
                $bahan->data_chiller        =   array_merge($out, $data);
                $bahan->status              =   1;
                $bahan->bb_item             =   $bahan->bb_item + $berat;
                $bahan->save();
            } else {
                $newbahan                      =   new Bahanbaku;
                $newbahan->data_chiller        =   json_encode($data);
                $newbahan->status              =   1;
                $newbahan->bb_item             =   $berat;
                $newbahan->save();
            }

            return back()->with('status', 1)->with('message', 'Pengajuan bahan baku berhasil');
        }
        return redirect()->route("index");
    }

    public function prosesbonless(Request $request)
    {
        $waiting    =   Order::select('id')
            ->where('status', 2)
            ->whereIn('id', OrderItem::select('order_id')->whereIn('item_id', Item::select('id')->where('category_id', 5)))
            ->get();

        $order      =   [];
        foreach ($waiting as $row) {
            $order[]    =   $row->id;
        }

        $order_item =   [];
        $item   =   OrderItem::select('id')
            ->whereIn('order_id', $order)->whereIn('item_id', Item::select('id')->where('category_id', 5))
            ->get();

        foreach ($item as $val) {
            $log_item                   = new OrderItemLog();
            $log_item->activity         = "kepala-produksi-bahanbaku-bonless";
            $log_item->order_item_id    = $val->id;
            $log_item->user_id          = Auth::user()->id;
            $log_item->key              = AppKey::generate();
            $log_item->save();

            $order_item[]               =   $val->id;
            $uporderitem                =   OrderItem::find($val->id);
            $uporderitem->status        =   1;
            $uporderitem->save();
        }


        $bahan                      =   Bahanbaku::where('id', $request->bahan)->first();
        $bahan->data_order          =   json_encode($order);
        $bahan->data_order_item     =   json_encode($order_item);
        $bahan->status              =   2;
        $bahan->save();
    }

    public function bahanbakudetail(Request $request)
    {
        if (User::setIjin(7)) {
            $chiller =  Chiller::whereIn('status', [1, 2])
                ->where('jenis', 'masuk')
                ->whereIn('table_name', ['grading', 'free_stock'])
                ->where('stock_item', '>', 0)
                ->orderBy('item_id', 'ASC')
                ->where('status', 2)
                ->get();

            $detail  =  Order::find($request->customer);
            $item    =  OrderItem::find($request->item);
            return view('admin/pages/kepala_produksi/bahanbakuproses', compact('chiller', 'detail', 'item'));
        }
        return redirect()->route("index");
    }

    public function requestdetail(Request $request)
    {
        if (User::setIjin(7)) {
            $chiller =  Chiller::whereIn('status', [1, 2])
                ->where('jenis', 'masuk')
                ->whereIn('table_name', ['grading', 'free_stock'])
                ->where('stock_item', '>', 0)
                ->orderBy('item_id', 'ASC')
                ->where('status', 2)
                ->get();

            $detail  =  Order::find($request->customer);
            $item    =  OrderItem::find($request->item);

            $bahan   =  Bahanbaku::select(DB::raw("SUM(bb_berat) AS jml_berat"), DB::raw("SUM(bb_item) AS jml_item"))
                ->where('order_id', $request->customer)
                ->where('order_item_id', $request->item)
                ->first();

            return view('admin.pages.kepala_produksi.requestbahanbaku', compact('chiller', 'detail', 'item', 'bahan'));
        }
        return redirect()->route("index");
    }

    public function requestbonless()
    {

        if (User::setIjin(7)) {
            $bonless        =   Order::whereIn('status', [2, 3, 4, 5])
                ->whereIn('id', OrderItem::select('order_id')
                    ->where('status', null)
                    ->whereIn('item_id', Item::select('id')->where('category_id', 5)))
                ->get();

            $bahanbaku      =   Bahanbaku::where('status', 1)->get();

            $bhnbb = [];
            foreach ($bahanbaku as $i => $bahan) {
                $de = json_decode($bahan->data_chiller, FALSE);
                foreach ($de as $key) {
                    $bhnbb[] = Chiller::where('id', $key[0])->get();
                }
            }


            return view('admin.pages.kepala_produksi.bahanbakubonles', compact('bhnbb', 'bahanbaku', 'bonless'));
        }
        return redirect()->route("index");
    }

    public function storerequestbahanbaku(Request $request)
    {
        if (User::setIjin(7)) {

            DB::beginTransaction();

            $datchill                    =  Chiller::find($request->chiller);

            if (($datchill->stock_item - $datchill->request_pending) >= $request->qty) {
                $data                        =   OrderItem::find($request->item);
                $data->kr_proses             =   Carbon::now();
                $data->status                =   1;
                $data->save();

                $chiller                    =   new Chiller;
                $chiller->production_id     =   $datchill->production_id;
                $chiller->table_name        =   'order_item';
                $chiller->table_id          =   $data->id;
                $chiller->asal_tujuan       =   'kepala_produksi';
                $chiller->no_mobil          =   $datchill->no_mobil;
                $chiller->item_id           =   $datchill->item_id;
                $chiller->item_name         =   $datchill->item_name;
                $chiller->jenis             =   'keluar';
                $chiller->type              =   'pengambilan-bahan-baku';
                $chiller->tanggal_potong    =   Carbon::now();
                $chiller->tanggal_produksi  =   Carbon::now();
                $chiller->qty_item          =   $request->qty;
                $chiller->status            =   3;
                $chiller->save();

                $bahanbaku                  =   new Bahanbaku;
                $bahanbaku->chiller_id      =   $request->chiller;
                $bahanbaku->chiller_out     =   $chiller->id;
                $bahanbaku->order_id        =   $request->order;
                $bahanbaku->order_item_id   =   $data->id;
                $bahanbaku->bb_item         =   $request->qty;
                $bahanbaku->save();

                $log_item                   = new OrderItemLog();
                $log_item->activity         = "kepala-produksi-bahan-baku";
                $log_item->order_item_id    = $data->id;
                $log_item->user_id          = Auth::user()->id;
                $log_item->key              = AppKey::generate();
                $log_item->save();

                DB::commit();

                return back()->with('status', 1)->with('message', 'Data berhasil diperbaharui');
            } else {
                DB::rollBack() ;
                return back()->with('status', 2)->with('message', 'Input melebihi stock tersedia');
            }
        }
        return redirect()->route("index");
    }


    public function sendchiller(Request $request)
    {

        DB::beginTransaction();

        if (User::setIjin(7)) {
            $order                     =   Bahanbaku::where('order_id', $request->order)->where('order_item_id', $request->item)->first();
            $berat                     =   Bahanbaku::where('order_id', $request->order)->where('order_item_id', $request->item)->sum('bb_berat');
            $qty                       =   Bahanbaku::where('order_id', $request->order)->where('order_item_id', $request->item)->sum('bb_item');

            if ($order == '') {
                $bahanbaku      =   Bahanbaku::where('order_id', null)->get();

                $idchill        = [];
                $idorder        = [];
                $idorderitem    = [];
                foreach ($bahanbaku as $i => $bahan) {
                    $idchiller  = json_decode($bahan->data_chiller, FALSE);
                    $idor       = json_decode($bahan->data_order, FALSE);
                    $idoritem   = json_decode($bahan->data_order_item, FALSE);

                    $idor       =  (array) $idor;
                    $idoritem   =  (array) $idoritem;

                    for ($i = 0; $i < count($idor); $i++) {
                        if ($idor[$i] == $request->order) {
                            $idorder[] = $idor[$i];
                        }
                    }

                    for ($i = 0; $i < count($idoritem); $i++) {
                        if ($idoritem[$i] == $request->item) {
                            $idorderitem[] = $idoritem[$i];

                            $log_item                   = new OrderItemLog();
                            $log_item->activity         = "kepala-produksi-kirim-chiller";
                            $log_item->order_item_id    = $idoritem[$i];
                            $log_item->user_id          = Auth::user()->id;
                            $log_item->key              = AppKey::generate();
                            $log_item->save();
                        }
                    }

                    foreach ($idchiller as $cok) {
                        $idchill[] = $cok[0];
                    }
                }

                foreach ($idorder as $key => $value) {
                    foreach ($idorderitem as $key => $val) {
                        $data     =   OrderItem::where('order_id', $value)->where('id', $val)->first();
                    }
                }

                $item                       =   Item::find($data->item_id);

                $category                   =   Category::find($item->category_id);

                $chiller                    =   new Chiller;
                $chiller->item_id           =   $item->id;
                $chiller->item_name         =   $item->nama;
                $chiller->table_name        =   'order_item';
                $chiller->table_id          =   $data->id;
                $chiller->asal_tujuan       =   'kepala_produksi';
                $chiller->jenis             =   'masuk';
                $chiller->type              =   'hasil-produksi';
                $chiller->kategori          =   $data->item->itemkat->nama;
                $chiller->tanggal_potong    =   Carbon::now();
                $chiller->tanggal_produksi  =   Carbon::now();
                $chiller->qty_item          =   $data->qty;
                $chiller->berat_item        =   $data->berat;
                $chiller->status            =   1;
                $chiller->save();

                $data->kr_selesai           =   Carbon::now();
                $data->status               =   3;
                $data->fulfillment_berat    =   $berat;
                $data->fulfillment_qty      =   $qty;
                $data->save();

                DB::commit();
            } elseif ($order->order_id != '') {

                $berat                      =   Bahanbaku::where('order_id', $request->order)->where('order_item_id', $request->item)->sum('bb_berat');
                $qty                        =   Bahanbaku::where('order_id', $request->order)->where('order_item_id', $request->item)->sum('bb_item');

                $datchill                   =   Chiller::find($order->chiller_id);

                $data                       =   OrderItem::find($request->item);

                $log_item                   = new OrderItemLog();
                $log_item->activity         = "kepala-produksi-kirim-chiller";
                $log_item->order_item_id    = $data->id;
                $log_item->user_id          = Auth::user()->id;
                $log_item->key              = AppKey::generate();
                $log_item->save();

                $item                       =   Item::find($data->item_id);

                $category                   =   Category::find($item->category_id);

                $chiller                    =   new Chiller;
                $chiller->production_id     =   $datchill->production_id;
                $chiller->table_name        =   'order_item';
                $chiller->table_id          =   $data->id;
                $chiller->asal_tujuan       =   'kepala_produksi';
                $chiller->no_mobil          =   $datchill->no_mobil;
                $chiller->item_id           =   $datchill->item_id;
                $chiller->item_name         =   $datchill->item_name;
                $chiller->jenis             =   'masuk';
                $chiller->type              =   'hasil-produksi';
                $chiller->kategori          =   $data->item->itemkat->nama;
                $chiller->tanggal_potong    =   Carbon::now();
                $chiller->tanggal_produksi  =   Carbon::now();
                $chiller->qty_item          =   $qty;
                $chiller->berat_item        =   $berat;
                $chiller->status            =   1;
                $chiller->save();

                $item                       =   OrderItem::where('order_id', $request->order)->where('id', $request->item)->first();
                $item->kr_selesai           =   Carbon::now();
                $item->status               =   3;
                $item->fulfillment_berat    =   $berat;
                $item->fulfillment_qty      =   $qty;
                $item->save();
                DB::commit();
            }
        }
        return redirect()->route("index");
    }

    public function sendabf(Request $request)
    {
        if (User::setIjin(7)) {
            $order                  =   Bahanbaku::where('order_id', $request->order)->where('order_item_id', $request->item)->first();
            $berat                  =   Bahanbaku::where('order_id', $request->order)->where('order_item_id', $request->item)->sum('bb_berat');
            $qty                    =   Bahanbaku::where('order_id', $request->order)->where('order_item_id', $request->item)->sum('bb_item');

            if ($order == '') {
                $bahanbaku      =   Bahanbaku::where('order_id', null)->get();

                $idchill        = [];
                $idorder        = [];
                $idorderitem    = [];
                foreach ($bahanbaku as $i => $bahan) {
                    $idchiller  = json_decode($bahan->data_chiller, FALSE);
                    $idor       = json_decode($bahan->data_order, FALSE);
                    $idoritem   = json_decode($bahan->data_order_item, FALSE);

                    $idor       =  (array) $idor;
                    $idoritem   =  (array) $idoritem;

                    for ($i = 0; $i < count($idor); $i++) {
                        if ($idor[$i] == $request->order) {
                            $idorder[] = $idor[$i];
                        }
                    }

                    for ($i = 0; $i < count($idoritem); $i++) {
                        if ($idoritem[$i] == $request->item) {
                            $idorderitem[] = $idoritem[$i];

                            $log_item                   = new OrderItemLog();
                            $log_item->activity         = "kepala-produksi-kirim-abf";
                            $log_item->order_item_id    = $idoritem[$i];
                            $log_item->user_id          = Auth::user()->id;
                            $log_item->key              = AppKey::generate();
                            $log_item->save();
                        }
                    }

                    foreach ($idchiller as $cok) {
                        $idchill[] = $cok[0];
                    }
                }

                foreach ($idorder as $key => $value) {
                    foreach ($idorderitem as $key => $val) {
                        $data     =   OrderItem::where('order_id', $value)->where('id', $val)->first();
                    }
                }

                $item                       =   Item::find($data->item_id);

                $category                   =   Category::find($item->category_id);

                $chiller                    =   new Abf;
                $chiller->table_name        =   'order_item';
                $chiller->table_id          =   $data->id;
                $chiller->item_id           =   $item->id;
                $chiller->item_name        =    $item->nama;
                $chiller->tanggal_masuk     =   date('Y-m-d');
                $chiller->asal_tujuan       =   $category->nama;
                $chiller->jenis             =   'masuk';
                $chiller->type              =   'hasil-produksi';
                $chiller->qty_awal          =   $data->qty;
                $chiller->berat_awal        =   $data->berat;
                $chiller->qty_item          =   $data->qty;
                $chiller->berat_item        =   $data->berat;
                $chiller->status            =   '1';
                $chiller->save();

                $data->kr_selesai           =   Carbon::now();
                $data->status               =   3;
                $data->fulfillment_berat    =   $berat;
                $data->fulfillment_qty      =   $qty;
                $data->save();
            } else
            if ($order->order_id != '') {

                $berat                      =   Bahanbaku::where('order_id', $request->order)
                    ->where('order_item_id', $request->item)
                    ->sum('bb_berat');

                $qty                        =   Bahanbaku::where('order_id', $request->order)
                    ->where('order_item_id', $request->item)
                    ->sum('bb_item');

                $datchill                   =   Chiller::find($order->chiller_id);

                $data                       =   OrderItem::find($request->item);

                $log_item                   = new OrderItemLog();
                $log_item->activity         = "kepala-produksi-kirim-abf";
                $log_item->order_item_id    = $data->id;
                $log_item->user_id          = Auth::user()->id;
                $log_item->key              = AppKey::generate();
                $log_item->save();

                $item                       =   Item::find($data->item_id);

                $category                   =   Category::find($item->category_id);

                $chiller                    =   new Abf;
                $chiller->production_id     =   $datchill->production_id;
                $chiller->table_name        =   'order_item';
                $chiller->table_id          =   $data->id;
                $chiller->asal_tujuan       =   $category->nama;
                $chiller->tanggal_masuk     =   date('Y-m-d');
                $chiller->item_id           =   $datchill->item_id;
                $chiller->item_name         =   $datchill->item_name;
                $chiller->jenis             =   'masuk';
                $chiller->type              =   'hasil-produksi';
                $chiller->qty_awal          =    $qty;
                $chiller->berat_awal        =    $berat;
                $chiller->qty_item          =    $qty;
                $chiller->berat_item        =    $berat;
                $chiller->status            =   '1';
                $chiller->save();

                $item                       =   OrderItem::where('order_id', $request->order)->where('id', $request->item)->first();
                $item->kr_selesai           =   Carbon::now();
                $item->status               =   3;
                $item->fulfillment_berat    =   $berat;
                $item->fulfillment_qty      =   $qty;
                $item->save();
            }
        }
        return redirect()->route("index");
    }

    public function toabfchiller(Request $request)
    {
        $chiller                =   Chiller::find($request->chiller);
        $chiller->status        =   3;
        $chiller->save();

        $abf                    =   new Abf;
        $abf->production_id     =   $chiller->production_id;
        $abf->table_name        =   'chiller';
        $abf->table_id          =   $chiller->id;
        $abf->asal_tujuan       =   'kepala_produksi';
        $abf->tanggal_masuk     =   date('Y-m-d');
        $abf->no_mobil          =   $chiller->no_mobil;
        $abf->item_id           =   $chiller->item_id;
        $abf->item_name         =   $chiller->item_name;
        $abf->jenis             =   'masuk';
        $abf->type              =   'free';
        $abf->qty_awal          =   $chiller->stock_item;
        $abf->berat_awal        =    $chiller->stock_berat;
        $abf->qty_item          =   $chiller->stock_item;
        $abf->berat_item        =    $chiller->stock_berat;
        $abf->status            =   '1';
        $abf->save();
    }

    public function selesai(Request $request)
    {
        if (User::setIjin(7)) {
            $data               =   OrderItem::where('order_id', $request->row_id)->get();

            foreach ($data as $row) {
                $raw                =   OrderItem::find($row->id);
                $raw->kr_proses     =   Carbon::now();
                $raw->status        =   2;
                $raw->save();
            }

            $order                  =   Order::find($request->row_id);
            $order->status          =   3;
            $order->kp_selesai      =   Carbon::now();
            $order->save();

            return back()->with('status', 1)->with('message', 'Data berhasil diperbaharui');
        }
        return redirect()->route("index");
    }

    public function selesaiproses(Request $request)
    {
        if (User::setIjin(7)) {
            $order                  =   Order::find($request->row_id);
            $order->status          =   5;
            $order->kp_selesai      =   Carbon::now();
            $order->save();
        }
        return redirect()->route("index");
    }

    public function chiller_fg(Request $request)
    {

        $tanggal = $request->tanggal ?? date('Y-m-d');
        $chiller_fg     =   Chiller::whereIn('table_id', FreestockTemp::select('id')->where('kategori', 1))
            ->whereIn('table_name', ['free_stocktemp'])
            ->where('type', 'hasil-produksi')
            ->where('tanggal_produksi', $tanggal)
            ->get();

        return view('admin/pages/ppic/chiller_fg', compact('chiller_fg'));
    }
    public function chiller_penyiapan(Request $request)
    {

        $tanggal = $request->tanggal ?? date('Y-m-d');
        $chiller_penyiapan     =   Chiller::whereIn('table_id', FreestockTemp::select('id')->where('kategori', null))
            ->whereIn('table_name', ['free_stocktemp'])
            ->where('type', 'hasil-produksi')
            ->where('tanggal_produksi', $tanggal)
            ->get();

        return view('admin/pages/ppic/chiller_penyiapan', compact('chiller_penyiapan'));
    }

    public function nonlb(Request $request)
    {
        $tanggal    =   $request->tanggal ?? date('Y-m-d');
        // $data       =   Production::whereIn('purchasing_id', Purchasing::select('id')->where('tanggal_potong', $tanggal)->whereNotIn('type_po',['PO LB', 'PO Maklon']))->get();
        $purchase   =   Purchasing::where('tanggal_potong', $tanggal)
                        ->whereNotIn('type_po',['PO LB', 'PO Maklon'])
                        ->paginate(10);

        return view('admin.pages.ppic.nonlb', compact('purchase', 'tanggal'));
    }

    public function lb(Request $request)
    {
        $tanggal    =   $request->tanggal ?? date('Y-m-d');
        // $data       =   Production::whereIn('purchasing_id', Purchasing::select('id')->where('tanggal_potong', $tanggal)->whereNotIn('type_po',['PO LB', 'PO Maklon']))->get();
        $purchase       = Purchasing::where('tanggal_potong', $tanggal)->where('type_po','PO LB')->paginate(10);

        return view('admin.pages.ppic.lb', compact('purchase', 'tanggal'));
    }

    public function ppic_acc(Request $request)
    {
        $production_id   =   $request->id;
        $tujuan          =   $request->tujuan;
        $prod            =   Production::find($production_id);

        if ($tujuan == 'evis') {
            $prod->grading_status   =   1;
            $prod->evis_status      =   null;
            $prod->lpah_status      =   1;
            $prod->ppic_acc         =   2;
            $prod->ppic_tujuan      =   $tujuan;
        } elseif ($tujuan == 'grading') {
            $prod->grading_status   =   null;
            $prod->evis_status      =   1;
            $prod->lpah_status      =   1;
            $prod->ppic_acc         =   2;
            $prod->ppic_tujuan      =   $tujuan;
        } elseif ($tujuan == 'abf') {
            $prod->grading_status   =   1;
            $prod->evis_status      =   1;
            $prod->lpah_status      =   1;
            $prod->ppic_acc         =   2;
            $prod->ppic_tujuan      =   $tujuan;
        } elseif ($tujuan == 'chiller') {
            $prod->grading_status   =   1;
            $prod->evis_status      =   1;
            $prod->lpah_status      =   1;
            $prod->ppic_acc         =   2;
            $prod->ppic_tujuan      =   $tujuan;
        }
        $prod->sc_status            =   1 ;
        // $prod->no_urut              =   Production::nomor_urut_nolb($request->tanggal) ;
        $prod->sc_tanggal_masuk     =   Carbon::now();
        $prod->sc_jam_masuk         =   Carbon::now();
        $prod->sc_hari              =   date('l');
        $prod->save();

        $purchase           =   Purchasing::find($prod->purchasing_id) ;
        $purchase->status   =   1 ;
        $purchase->save() ;
    }

    public function ppic_batal(Request $request)
    {
        $production_id   =   $request->id;
        $prod            =   Production::find($production_id);

        // $prod->grading_status   =   null;
        // $prod->evis_status      =   null;
        // $prod->lpah_status      =   null;
        $prod->ppic_acc         =   1;
        $prod->ppic_tujuan      =   null;
        $prod->save();

        $purchase           =   Purchasing::find($prod->purchasing_id);
        $purchase->status   =   2;
        $purchase->save();
    }

    public function toabf_fg(Request $request)
    {
        $item_jumlah            =   $request->item_jumlah;
        $item_berat             =   $request->item_berat;

        DB::beginTransaction();
        $chiller                =   Chiller::find($request->chiller);
        $exp                    =   json_decode($chiller->label);

        $chiller->stock_berat   =   (float)($chiller->stock_berat - $item_berat) ;
        $chiller->stock_item    =   (float)($chiller->stock_item - $item_jumlah) ;

        $tanggal                =   $request->tanggal ?? $chiller->tanggal_produksi;

        if (!$chiller->save()) {
            DB::rollBack();
            $result['status']   =   400 ;
            $result['msg']      =   "Proses gagal" ;
            return $result ;
        }

        // chiller keluar
        $new_chiller                     =   new Chiller;
        $new_chiller->table_name         =   $chiller->table_name;
        $new_chiller->table_id           =   $chiller->table_id;
        $new_chiller->asal_tujuan        =   'abf';
        $new_chiller->item_id            =   $chiller->item_id;
        $new_chiller->item_name          =   $chiller->item_name;
        $new_chiller->jenis              =   'keluar';
        $new_chiller->label              =   $chiller->label ;
        $new_chiller->customer_id        =   $chiller->customer_id ;
        $new_chiller->selonjor           =   $chiller->selonjor ;
        $new_chiller->tanggal_produksi   =   date("Y-m-d");
        $new_chiller->stock_berat        =   $item_berat;
        $new_chiller->berat_item         =   $item_berat;
        $new_chiller->stock_item         =   $item_jumlah;
        $new_chiller->qty_item           =   $item_jumlah;
        $new_chiller->status             =   4;
        if (!$new_chiller->save()) {
            DB::rollBack();
            $result['status']   =   400 ;
            $result['msg']      =   "Proses gagal" ;
            return $result ;
        }

        $abf                    =   new Abf;
        $abf->production_id     =   $chiller->production_id;
        $abf->table_name        =   'chiller';
        $abf->tanggal_masuk     =   $chiller->tanggal_produksi;
        $abf->table_id          =   $chiller->id;
        $abf->asal_tujuan       =   'kepala_produksi';
        $abf->tanggal_masuk     =   date('Y-m-d');
        $abf->no_mobil          =   $chiller->no_mobil;
        $abf->item_id           =   $chiller->item_id;
        $abf->item_id_lama      =   $chiller->item_id;
        $abf->item_name         =   $chiller->item_name;
        $abf->packaging         =   $exp->plastik->jenis ?? "";
        $abf->selonjor          =   $chiller->selonjor ;
        $abf->customer_id       =   $chiller->customer_id ;
        $abf->jenis             =   'masuk';
        $abf->type              =   'free';
        $abf->qty_awal          =   $item_jumlah ;
        $abf->berat_awal        =   $item_berat ;
        $abf->qty_item          =   $item_jumlah ;
        $abf->berat_item        =   $item_berat ;
        $abf->status            =   '1';
        if (!$abf->save()) {
            DB::rollBack();
            $result['status']   =   400 ;
            $result['msg']      =   "Proses gagal" ;
            return $result ;
        }

        DB::commit();
    }
}
