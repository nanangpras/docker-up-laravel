<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Abf;
use App\Models\AppKey;
use App\Models\Bahanbaku;
use App\Models\Category;
use App\Models\Chiller;
use App\Models\Driver;
use App\Models\Ekspedisi;
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

class KepalaProduksiController extends Controller
{

    public function index(Request $request)
    {
        if (User::setIjin(7)) {
            $tanggal    =   $request->tanggal ?? date('Y-m-d');
            $tanggalend =   $request->tanggalend ?? date('Y-m-d');

            if ($request->key == 'ekspedisi') {
                $ekspedisi  =   Ekspedisi::where('driver_id', $request->driver)
                                ->where('kembali', NULL)
                                ->first();

                return view('admin.pages.kepala_produksi.component.ekspedisi_view', compact('request', 'ekspedisi')) ;
            } else

            if ($request->key == 'order') {


                $order      =   Order::whereBetween('tanggal_so', [$request->tanggal,$tanggalend])
                                ->whereIn('status', [2, 3, 4, 5, 6, 7, 8, 9, 10])
                                ->orderBy('id', 'DESC')
                                ->get();

                return view('admin.pages.kepala_produksi.component.order_view', compact('tanggal', 'order'));
            }
            else {
                $chiller        =   Chiller::whereIn('table_name', ['grading', 'free_stock'])
                                    ->where('type', 'bahan-baku')
                                    ->whereNotIn('asal_tujuan', ['evisampingan','baru'])
                                    ->where('stock_item', '>', 0)
                                    ->whereBetween('tanggal_potong', [$tanggal,$tanggalend])
                                    ->orderBy('item_name', 'ASC')
                                    ->get();

                $driver         =   Driver::get() ;

                return view('admin.pages.kepala_produksi.index', compact('tanggal', 'chiller', 'driver','tanggalend'));
            }



        }
        return redirect()->route("index");
    }

    public function inventory()
    {

        $cold1      =   Gudang::where('kategori', 'Production')->whereIn('id', [2, 4])->where('status', 1)->get();

        return view('admin.pages.kepala_produksi.component.inventory', compact( 'cold1'));
    }

    public function showinventory(Request $request)
    {
        $tanggal    =   $request->tanggal ?? date('Y-m-d');
        $id         =   $request->id ?? '';
        if ($request->id == 2) {
            $gudang     =   Chiller::where('jenis', 'masuk')
                                    ->where('type', 'bahan-baku')
                                    ->whereNotIn('asal_tujuan', ['evisampingan','baru'])
                                    ->where('stock_berat','>', 0)
                                    ->where('tanggal_produksi', $tanggal)
                                    ->orderBy('item_name', 'ASC')
                                    ->get();

            $pindah     =   Gudang::where('kategori', 'Production')
                            ->whereIn('id', [2, 4])
                            ->where('status', 1)
                            ->get();

        } elseif ($request->id == 4) {
            $gudang     =   Chiller::where('jenis', 'masuk')->where('type', 'hasil-produksi')->where('stock_berat','>', 0)->orderBy('item_name', 'ASC')->where('tanggal_produksi', $tanggal)->get();
            $pindah     =   Gudang::where('kategori', 'Production')
                            ->whereIn('id', [2, 4])
                            ->where('status', 1)
                            ->get();
        } else {
            $gudang     =   Product_gudang::where('gudang_id', $request->id)->where('created_at', 'like', '%' . $tanggal . '%')->where('status', '2')->get();
            $pindah     =   Gudang::where('kategori', 'Warehouse')->where('status', 1)->get();
        }
        return view('admin.pages.kepala_produksi.component.inventory_show', compact('gudang', 'pindah', 'id'));
    }

    public function storeinventory(Request $request)
    {

        DB::beginTransaction();

            $chiller    =   Chiller::find($request->id);
            $chill      =   new Chiller;
            $chill->table_name          =   'chiller';
            $chill->table_id            =   $chiller->id;
            $chill->asal_tujuan         =   $chiller->asal_tujuan;
            $chill->item_id             =   $chiller->item_id;
            $chill->item_name           =   $chiller->item_name;
            $chill->jenis               =   $chiller->jenis;
            $chill->type                =   $chiller->type;
            $chill->kategori            =   $request->tujuan;
            $chill->qty_item            =   $request->qty;
            $chill->berat_item          =   $request->berat;
            $chill->stock_item          =   $request->qty;
            $chill->stock_berat         =   $request->berat;
            $chill->tanggal_potong      =   $chiller->tanggal_potong;
            $chill->tanggal_produksi    =   $chiller->tanggal_produksi;
            $chill->no_mobil            =   $chiller->no_mobil;
            $chill->status              =   2;
            $chill->save();

            $chiller->stock_item        =   $chiller->stock_item - $request->qty;
            $chiller->stock_berat       =   $chiller->stock_item - $request->berat;
            $chiller->save();


            $gdg            =   Gudang::find($request->tujuan);
            $gdg_baru       =   Gudang::find($request->tujuan);

            $nama_tabel     =   "chiller";
            $id_tabel       =   $chill->id;
            $location       =   $gdg->code;
            $from           =   Gudang::gudang_netid($location);

            $to         =   Gudang::gudang_netid($gdg_baru->code);
            $idgudang   =   Gudang::gudang_id($gdg_baru->code);

            $id_location    =   Gudang::gudang_netid($location);
            $label          =   strtolower("ti_" . str_replace(" ", "", $gdg->code) . "_" . str_replace(" ", "", $gdg_baru->code));

            $item           = Item::find($chill->item_id);

            $transfer       =   [
                [
                    "internal_id_item"  =>  (string)$item->netsuite_internal_id,
                    "item"              =>  (string)$item->sku,
                    "qty_to_transfer"   =>  (string)$chill->stock_berat
                ]
            ];

            DB::commit();

            try {
                Chiller::recalculate_chiller($chiller->id);
            } catch (\Throwable $th) {
                
            }

            return Netsuite::transfer_inventory($nama_tabel, $id_tabel, $label, $id_location, $location, $from, $to, $transfer, null);

    }

    public function kepala(Request $request)
    {
        if (User::setIjin(7)) {
            $tanggal    =   $request->tanggal ?? date('Y-m-d');
            $order          =   Order::whereIn('status', [2, 3, 4, 5, 6, 7, 8, 9, 10])
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
                ->orderBy('item_name', 'ASC')
                ->get();

            $fulfillment    =   Order::whereIn('id', OrderItem::select('order_id')
                ->where('status', '>=', 2))
                ->get();

            return view('admin/pages/kepala_produksi/kepala_show', compact('order', 'pending', 'chiller', 'fulfillment', 'tanggal'));
        }
        return redirect()->route("index");
    }

    public function evaluasi()
    {
        if (User::setIjin(7)) {
            $parting    =   FreestockTemp::whereIn('id', Chiller::select('table_id')
                            ->where('table_name', 'free_stocktemp')
                            ->where('type', 'hasil-produksi')
                            ->where('regu', 'parting'))
                            ->get();

            $marinasi   =   FreestockTemp::whereIn('id', Chiller::select('table_id')
                            ->where('table_name', 'free_stocktemp')
                            ->where('type', 'hasil-produksi')
                            ->where('regu', 'marinasi'))
                            ->get();

            $whole      =   FreestockTemp::whereIn('id', Chiller::select('table_id')
                            ->where('table_name', 'free_stocktemp')
                            ->where('type', 'hasil-produksi')
                            ->where('regu', 'whole'))
                            ->get();

            $frozen     =   FreestockTemp::whereIn('id', Abf::select('table_id')
                            ->where('table_name', 'free_stocktemp')
                            ->where('type', 'free-stock'))
                            ->get();

            $bonless    =   FreestockTemp::whereIn('id', Chiller::select('table_id')
                            ->where('table_name', 'free_stocktemp')
                            ->where('type', 'hasil-produksi')
                            ->where('regu', 'boneless'))
                            ->get();

            $evis       =   Chiller::where('table_name', 'free_stocktemp')
                            ->where('jenis', 'masuk')
                            ->where('type', 'hasil-produksi')
                            ->orderBy('item_name', 'ASC')
                            ->get();

            $partitem   = 0;
            $partberat  = 0;
            $marinasiitem   = 0;
            $marinasiberat  = 0;
            $wholeitem   = 0;
            $wholeberat  = 0;
            $frozenitem   = 0;
            $frozenberat  = 0;
            $bonlessitem   = 0;
            $bonlessberat  = 0;
            $evisitem   = 0;
            $evisberat  = 0;


            foreach ($parting as $part) {
                $partitem += $part->qty;
                $partberat += $part->berat;
            }

            foreach ($marinasi as $mar) {
                $marinasiitem += $mar->qty;
                $marinasiberat += $mar->berat;
            }

            foreach ($whole as $whol) {
                $wholeitem += $whol->qty;
                $wholeberat += $whol->berat;
            }

            foreach ($frozen as $froz) {
                $frozenitem += $froz->qty;
                $frozenberat += $froz->berat;
            }

            foreach ($bonless as $bon) {
                $bonlessitem += $bon->qty;
                $bonlessberat += $bon->berat;
            }

            foreach ($evis as $ev) {
                $evisitem += $ev->stock_item;
                $evisberat += $ev->stock_berat;
            }

            $total = array(
                'partitem'  =>  $partitem,
                'partberat' =>  $partberat,
                'marinasiitem'  =>  $marinasiitem,
                'marinasiberat' =>  $marinasiberat,
                'wholeitem'     =>  $wholeitem,
                'wholeberat'    =>  $wholeberat,
                'frozenitem'    =>  $frozenitem,
                'frozenberat'   =>  $frozenberat,
                'bonlessitem'   =>  $bonlessitem,
                'bonlessberat'  =>  $bonlessberat,
                'evisitem'      =>  $evisitem,
                'evisberat'     =>  $evisberat
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
                ->paginate(15);

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

            $free   =   Chiller::where('jenis', 'masuk')->where('type', 'free')->orderBy('item_name', 'ASC')->get();

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

            $free   =   Chiller::where('jenis', 'masuk')->where('type', 'free')->orderBy('item_name', 'ASC')->get();

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
                $bahanbaku->chiller_alokasi      =   $request->chiller;
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

        $item_jumlah     = $request->item_jumlah;
        $item_berat      = $request->item_berat;

        DB::beginTransaction();
        $chiller                =   Chiller::find($request->chiller);
        $chiller->stock_berat   =   (int)($chiller->stock_berat - $item_berat);
        $chiller->stock_item    =   (int)($chiller->stock_item - $item_jumlah);

        if (!$chiller->save()) {
            DB::rollBack();
            $result['code']     =   400 ;
            $result['msg']      =   "Proses gagal" ;
            return $result ;
        }

        // Data chiller keluar
        $new_chiller                     =   new Chiller;
        $new_chiller->table_name         =   $chiller->table_name;
        $new_chiller->table_id           =   $chiller->table_id;
        $new_chiller->asal_tujuan        =   'abf';
        $new_chiller->item_id            =   $chiller->item_id;
        $new_chiller->item_name          =   $chiller->item_name;
        $new_chiller->jenis              =   'keluar';
        $new_chiller->tanggal_potong     =   Carbon::now() ;
        $new_chiller->tanggal_produksi   =   Carbon::now() ;
        $new_chiller->stock_berat        =   $item_berat;
        $new_chiller->berat_item         =   $item_berat;
        $new_chiller->stock_item         =   $item_jumlah;
        $new_chiller->qty_item           =   $item_jumlah;
        $new_chiller->status             =   4;
        if (!$new_chiller->save()) {
            DB::rollBack();
            $result['code']     =   400;
            $result['msg']      =   "Proses gagal";
            return $result;
        }


        $item                   =   Item::where('nama', 'like', '%' . $chiller->item_name . ' frozen%')->first();

        if ($item == '') {
            DB::rollBack();
            $result['code']     =   400;
            $result['msg']      =   "Proses gagal";
            return $result;
        }

        $freestock                  =   new Freestock;
        $freestock->nomor           =   Freestock::get_nomor();
        $freestock->tanggal         =   Carbon::now();
        $freestock->user_id         =   Auth::user()->id;
        $freestock->regu            =   'frozen';
        $freestock->status          =   2;
        if (!$freestock->save()) {
            DB::rollBack();
            $result['code']     =   400;
            $result['msg']      =   "Proses gagal";
            return $result;
        }

        $list                       =   new FreestockList;
        $list->freestock_id         =   $freestock->id;
        $list->chiller_id           =   $chiller->id;
        $list->item_id              =   $chiller->item_id;
        $list->qty                  =   $item_jumlah;
        $list->regu                 =   'frozen';
        $list->berat                =   $item_berat;
        $list->sisa                 =   $list->qty;
        if (!$list->save()) {
            DB::rollBack();
            $result['code']     =   400;
            $result['msg']      =   "Proses gagal";
            return $result;
        }

        if ($request->plastik != 'Curah') {
            $plastik    =   Item::find($request->plastik);
        }

        $label = json_encode([
            'plastik'       =>  [
                'sku'       =>  $plastik->sku ?? NULL ,
                'jenis'     =>  $plastik->nama ?? NULL ,
                'qty'       =>  $request->jumlah ?? NULL
            ],
            'parting'       =>  [
                'qty'       =>  NULL
            ],
            'additional'    =>  [
                'tunggir'   =>  FALSE,
                'lemak'     =>  FALSE,
                'maras'     =>  FALSE,
            ]
        ]);

        $temp                   =   new FreestockTemp;
        $temp->freestock_id     =   $freestock->id;
        $temp->item_id          =   $item->id;
        $temp->regu             =   'frozen';
        $temp->tanggal_produksi =   Carbon::now();
        $temp->label            =   $label;
        $temp->berat            =   $item_jumlah;
        $temp->qty              =   $item_berat;
        if (!$temp->save()) {
            DB::rollBack();
            $result['code']     =   400;
            $result['msg']      =   "Proses gagal";
            return $result;
        }

        //chiller id & freestock id
        // Ditutup karena perubahan proses abf (21/10/2021)
        // Netsuite::wo_chiller_abf($chiller->id, $freestock->id);

        $abf                    =   new Abf;
        $abf->production_id     =   $chiller->production_id;
        $abf->table_name        =   'chiller';
        $abf->tanggal_masuk     =   Carbon::now() ;
        $abf->table_id          =   $chiller->id;
        $abf->asal_tujuan       =   'kepala_produksi';
        $abf->no_mobil          =   $chiller->no_mobil;
        $abf->item_id           =   $item->id;
        $abf->item_id_lama      =   $chiller->item_id;
        $abf->item_name         =   $item->nama;
        $abf->jenis             =   'masuk';
        $abf->type              =   'free';
        $abf->packaging         =   ($plastik->nama ?? NULL) ;
        $abf->qty_awal          =   $item_jumlah ;
        $abf->berat_awal        =   $item_berat ;
        $abf->qty_item          =   $item_jumlah ;
        $abf->berat_item        =   $item_berat ;
        $abf->status            =   '1';
        if (!$abf->save()) {
            DB::rollBack();
            $result['code']     =   400;
            $result['msg']      =   "Proses gagal";
            return $result;
        }

        DB::commit();

        try {
            Chiller::recalculate_chiller($chiller->id);
        } catch (\Throwable $th) {
            
        }
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

    public function summary(Request $request)
    {
        $tanggal        =   $request->tanggal ?? date('Y-m-d');
        $tanggalakhir   =   $request->tanggalend ?? date('Y-m-d');
        $regu           =   $request->regu ;

        if($regu==""){
            $regu = ['marinasi', 'whole', 'boneless', 'parting'];
        }

        $bahan_baku =   FreestockList::select(DB::raw("SUM(berat) AS kg"), DB::raw("SUM(qty) AS jumlah"), 'item_id', 'nama')
                        ->whereIn('free_stocklist.freestock_id', Freestock::select('id')->whereBetween(DB::raw('DATE(tanggal)'), [$tanggal, $tanggalakhir]))
                        ->whereIn('regu', $regu)
                        ->leftJoin('items', 'items.id', '=', 'free_stocklist.item_id')
                        ->orderBy('items.nama', 'ASC')
                        ->groupBy('items.nama')
                        ->get() ;

        $produksi   =   FreestockTemp::select(DB::raw("SUM(qty) AS jumlah"), DB::raw("SUM(berat) AS kg"), 'item_id', 'nama')
                        ->whereIn('free_stocktemp.freestock_id', Freestock::select('id')->whereBetween(DB::raw('DATE(tanggal)'), [$tanggal, $tanggalakhir]))
                        ->whereIn('regu', $regu)
                        ->leftJoin('items', 'items.id', '=', 'free_stocktemp.item_id')
                        ->orderBy('items.nama', 'ASC')
                        ->groupBy('items.nama')
                        ->get() ;

        return view('admin.pages.kepala_produksi.component.summary', compact('regu', 'tanggal', 'bahan_baku', 'produksi'));
    }

    public function hasilpotong(Request $request)
    {
        $tanggal        =   $request->tanggal ?? date('Y-m-d');
        $tanggalend     =   $request->tanggalend ?? date('Y-m-d');
        $selisih        =   Production::whereBetween(DB::raw('DATE(lpah_tanggal_potong)'),[$tanggal, $tanggalend])->orderBy('id', 'ASC')->get();
        if ($request->key == 'unduh') {
            $selisih    =   clone $selisih;
            $selisih;
            $tanggal    =   $request->tanggal ?? date('Y-m-d');
            $tanggalend =   $request->tanggalend ?? date('Y-m-d');
            $filename   =  "laporan-selisih-lpah-dan-grading-tanggal-". $tanggal . "-" . $tanggalend . ".xls";
            return view('admin.pages.kepala_produksi.component.selisih-lpah-grading',compact('selisih','request','tanggal','tanggalend','filename'));
        }
        return view('admin.pages.kepala_produksi.component.hasilpotong', compact('tanggal','tanggalend','selisih'));
    }
}
