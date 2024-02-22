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
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class KepalaReguController extends Controller
{

    public function index()
    {
        if(User::setIjin(8) OR User::setIjin(9) OR User::setIjin(10) OR User::setIjin(11) OR User::setIjin(12)){
            return view('admin/pages/kepala_regu.index');
        }
        return redirect()->route("index");
    }

    public function boneles()
    {
        if (User::setIjin(8)) {
            return view('admin.pages.kepala_regu.bonles.index');
        }
        return redirect()->route("index");
    }

    public function bonelesshow()
    {
        if (User::setIjin(8)) {
            $order      =   Order::whereIn('status', [2, 3, 4, 5])
                            ->whereIn('id', OrderItem::select('order_id')->whereIn('item_id',Item::select('id')->where('category_id', 5)))
                            ->get();

            $pending    =   Order::where('status', 1)
                            ->whereIn('id',OrderItem::select('order_id')->whereIn('item_id', Item::select('id')->where('category_id', 5)))
                            ->get();

            $bahan      =   Item::whereIn('id', Chiller::select('item_id')
                                ->where('status', 2)
                                ->where('jenis', 'masuk')
                                ->where('stock_item', '>', 0)
                                ->pluck('item_id'))
                            ->get();

            return view('admin.pages.kepala_regu.bonles.bonless_show', compact('order', 'bahan'));
        }
        return redirect()->route("index");
    }

    public function bonelespending()
    {
        if(User::setIjin(8)){
            $pending    =   Order::where('status', 1)
                            ->whereIn('id', OrderItem::select('order_id')
                                ->whereIn('item_id', Item::select('id')
                                    ->where('category_id', 5)
                                )
                            )
                            ->orderBy('nama', 'ASC')
                            ->paginate(10);

            return view('admin.pages.kepala_regu.bonles.pending', compact('pending'));
        }
        return redirect()->route("index");
    }

    public function bahanbakubonless()
    {
        if (User::setIjin(8)) {
            $bonless        =   Order::whereIn('status', [2, 3, 4, 5])
                                ->whereIn('id', OrderItem::select('order_id')->where('status', 1)
                                    ->whereIn('item_id', Item::select('id')->where('category_id', 5))
                                )
                                ->get();

            $bahanbaku      =   Bahanbaku::where('status', 2)->get();

            $bhnbb = [];
            foreach ($bahanbaku as $i => $bahan) {
                $de = json_decode($bahan->data_chiller, FALSE);
                foreach ($de as $key) {
                    $bhnbb[] = Chiller::where('id', $key[0])->get();
                }
            }

            $item           =   Item::whereIn('category_id', [2, 4])->get();

            $free           =   Chiller::where('type','free')->get();

            return view('admin.pages.kepala_regu.bonles.bahanbonles', compact('bhnbb', 'bahanbaku', 'bonless', 'item','free'));
        }
        return redirect()->route("index");
    }

    public function storeboneles(Request $request)
    {
        if (User::setIjin(8)) {
            $berat  =   0;
            $data   =   [];
            for ($x = 0; $x < COUNT($request->x_code); $x++) {
                if ($request->qty[$x] > 0) {
                    $berat      +=  $request->qty[$x];
                    $item       =   Chiller::find($request->x_code[$x]);

                    $chiler                     =   new Chiller;
                    $chiler->production_id      =   $item->production_id;
                    $chiler->asal_tujuan        =   'krboneless';
                    $chiler->item_id            =   $item->item_id;
                    $chiler->item_name          =   $item->item_name;
                    $chiler->qty_item           =   $request->qty[$x];
                    $chiler->status             =   3;
                    $chiler->jenis              =   'keluar';
                    $chiler->save();

                    $data[]     =   [$chiler->id, $request->qty[$x]];
                }
            }

            $waiting    =   Order::select('id')
                ->where('status', 2)
                ->whereIn(
                    'id',
                    OrderItem::select('order_id')
                        ->whereIn('item_id', Item::select('id')->where('category_id', 5))
                )
                ->get();

            $order      =   [];
            foreach ($waiting as $row) {
                $order[]    =   $row->id;
            }

            $order_item =   [];
            $item       =   OrderItem::select('id')
                ->whereIn('item_id', Item::select('id')->where('category_id', 5))
                ->where('status', 1)
                ->get();

            foreach ($item as $row) {
                $order_item[]    =   $row->id;
            }

            $bahan                      =   new Bahanbaku;
            $bahan->data_chiller        =   json_encode($data);
            $bahan->data_order          =   json_encode($order);
            $bahan->data_order_item     =   json_encode($order_item);
            $bahan->bb_item             =   $berat;
            $bahan->save();

            return back()->with('status', 1)->with('message', 'Pengajuan bahan baku berhasil');
        }
        return redirect()->route("index");
    }

    public function bonelesproses(Request $request)
    {
        if (User::setIjin(8)) {
            $data                        =   OrderItem::find($request->row_id);
            $data->kr_proses             =   Carbon::now();
            $data->status                =   2;
            $data->save();


            $log_item                   = new OrderItemLog();
            $log_item->activity         = "kepala-regu-proses-bonless";
            $log_item->order_item_id    = $data->id;
            $log_item->user_id          = Auth::user()->id;
            $log_item->key              = AppKey::generate();
            $log_item->save();
        }
        return redirect()->route("index");
    }

    public function freestock(Request $request)
    {
        $item  =   Item::find($request->item);

        $chiller                =   new Chiller;
        $chiller->item_id       =   $request->item;
        $chiller->item_name     =   $item->nama;
        $chiller->qty_item      =   $request->qty;
        $chiller->berat_item    =   $request->berat;
        $chiller->stock_item    =   $request->qty;
        $chiller->stock_berat   =   $request->berat;
        $chiller->jenis         =   'masuk';
        $chiller->type          =   'free';
        $chiller->status        =   1;
        $chiller->save();

    }

    public function parting()
    {
        if (User::setIjin(9)) {
            return view('admin.pages.kepala_regu.parting.index');
        }
        return redirect()->route("index");
    }
    public function partingshow()
    {
        if(User::setIjin(9)){
            $order  =   Order::whereIn('status', [2,3,4,5])
                        ->whereIn('id', OrderItem::select('order_id')
                        ->whereIn('item_id', Item::select('id')
                        ->where('category_id', 2)))
                        ->get();

            return view('admin.pages.kepala_regu.parting.parting_show', compact('order'));
        }
        return redirect()->route("index");
    }

    public function partingpending()
    {
        if (User::setIjin(9)) {
            $pending    =   Order::where('status', 1)
                            ->whereIn('id', OrderItem::select('order_id')
                            ->whereIn('item_id', Item::select('id')
                            ->where('category_id', 2)))
                            ->orderBy('nama', 'ASC')
                            ->paginate(10);

            return view('admin.pages.kepala_regu.parting.pending', compact('pending'));
        }
        return redirect()->route("index");
    }

    public function partingfreestock(Request $request)
    {
        if(User::setIjin(9)){
            if ($request->id) {
                $data   =   FreestockTemp::where('freestock_id', $request->id)->get();

                return view('admin.pages.kepala_regu.parting.freestock.temporary', compact('data'));
            } else
            if ($request->list) {
                $list   =   FreestockList::where('freestock_id', $request->list)
                            ->get();

                return view('admin.pages.kepala_regu.parting.freestock.liststock', compact('list'));
            } else {
                $fresh  =   Freestock::where('kategori', 2)
                            ->where('status', 1)
                            ->paginate(10);

                $items  =   Item::where('category_id', 2)
                            ->orderBy('nama', 'ASC')
                            ->pluck('nama', 'id');

                return view('admin.pages.kepala_regu.parting.freestock.index', compact('fresh', 'items'));
            }

        }
        return redirect()->route("index");
    }

    public function partingfreestockdelete(Request $request)
    {
        if(User::setIjin(9)) {
            $stocklist              =   FreestockList::find($request->row_id);
            $stocklist->sisa        =   $stocklist->sisa + $request->qty;
            $stocklist->save();

            FreestockTemp::where('freestock_id', $request->row_id)
            ->where('freestocklist_id', $request->list)
            ->where('item_id', $request->item)
            ->where('qty', $request->qty)
            ->delete();
        }
        return redirect()->route("index");
    }

    public function partingfreestockstore(Request $request)
    {
        if(User::setIjin(9)){
            $request->validate([
                "row_id"        =>  ['required', Rule::exists('free_stock', 'id')->where('kategori', 2)],
                "freestock"     =>  ['required', Rule::exists('free_stocklist', 'id')->where('freestock_id', $request->row_id)],
                "item"          =>  ['required', Rule::exists('items', 'id')->where('category_id', 2)],
                "qty"           =>  'required|numeric|max:'. FreestockList::find($request->freestock)->sisa
            ]);

            $stocklist              =   FreestockList::find($request->freestock) ;
            $stocklist->sisa        =   $stocklist->sisa - $request->qty ;
            $stocklist->save() ;

            $temp                   =   new FreestockTemp ;
            $temp->freestock_id     =   $request->row_id ;
            $temp->freestocklist_id =   $request->freestock ;
            $temp->item_id          =   $request->item ;
            $temp->qty              =   $request->qty ;
            $temp->save() ;
        }
        return redirect()->route("index");
    }

    public function partingfreestockselesai(Request $request)
    {
        if(User::setIjin(9)){
            $freestock              =   Freestock::find($request->row_id) ;

            $temp   =   FreestockTemp::where('freestock_id', $freestock->id)->get();

            foreach ($temp as $row) {
                $chiler                     =   new Chiller ;
                $chiler->table_name         =   'free_stock' ;
                $chiler->table_id           =   $freestock->id ;
                $chiler->asal_tujuan        =   'krparting' ;
                $chiler->item_id            =   $row->item_id ;
                $chiler->item_name          =   $row->item->nama;
                $chiler->no_mobil           =   $row->stocklist->chiller->no_mobil;
                $chiler->jenis              =   'masuk' ;
                $chiler->type               =   'hasil-produksi';
                $chiler->tanggal_potong     =   Carbon::now();
                $chiler->tanggal_produksi   =   Carbon::now();
                $chiler->qty_item           =   $row->qty;
                $chiler->stock_item         =   $row->qty;
                $chiler->status             =   1;
                $chiler->save() ;
            }

            FreestockTemp::where('freestock_id', $freestock->id)->delete();

            $list   =   FreestockList::where('freestock_id', $freestock->id)->get();

            foreach ($list as $row) {
                if ($row->sisa > 0) {
                    $chiler                     =   new Chiller;
                    $chiler->table_name         =   'free_stock';
                    $chiler->table_id           =   $freestock->id;
                    $chiler->asal_tujuan        =   'belum_terpakai';
                    $chiler->item_id            =   $row->item_id;
                    $chiler->item_name          =   $row->item->nama;
                    $chiler->jenis              =   'masuk';
                    $chiler->no_mobil           =   $row->chiller->no_mobil;
                    $chiler->type               =   'bahan-baku';
                    $chiler->tanggal_potong     =   Carbon::now();
                    $chiler->tanggal_produksi   =   Carbon::now();
                    $chiler->qty_item           =   $row->sisa;
                    $chiler->stock_item         =   $row->sisa;
                    $chiler->status             =   1;
                    $chiler->save();
                }
            }

            $freestock->status      =   2 ;
            $freestock->save() ;
        }
        return redirect()->route("index");
    }

    public function partingdetail(Request $request)
    {
        if (User::setIjin(9)) {
            $chiller =  Chiller::where('jenis', 'masuk')->where('table_name', 'grading')->where('stock_item', '>', 0)->orderBy('item_id', 'ASC')->get();
            $detail  =  Order::find($request->customer);
            $item    =  OrderItem::find($request->item);
            return view('admin.pages.kepala_regu.parting.partingproses', compact('chiller', 'detail', 'item'));
        }
        return redirect()->route("index");
    }

    public function marinasi()
    {
        if(User::setIjin(10)){
            return view('admin.pages.kepala_regu.marinasi.index');
        }
        return redirect()->route("index");
    }

    public function marinasishow()
    {
        if(User::setIjin(10)){
            $order  =   Order::whereIn('status', [2,3,4,5])
                        ->whereIn('id', OrderItem::select('order_id')
                        ->whereIn('item_id', Item::select('id')
                        ->where('category_id', 3)))
                        ->get();

            return view('admin.pages.kepala_regu.marinasi.marinasi_show', compact('order'));
        }
        return redirect()->route("index");
    }

    public function marinasifreestock(Request $request)
    {
        if (User::setIjin(10)) {
            if ($request->id) {
                $data   =   FreestockTemp::where('freestock_id', $request->id)->get();

                return view('admin.pages.kepala_regu.marinasi.freestock.temporary', compact('data'));
            } else
            if ($request->list) {
                $list   =   FreestockList::where('freestock_id', $request->list)
                            ->get();

                return view('admin.pages.kepala_regu.marinasi.freestock.liststock', compact('list'));
            } else {
                $fresh  =   Freestock::where('kategori', 3)
                            ->where('status', 1)
                            ->paginate(10);

                $items  =   Item::where('category_id', 3)
                            ->orderBy('nama', 'ASC')
                            ->pluck('nama', 'id');

                return view('admin.pages.kepala_regu.marinasi.freestock.index', compact('fresh', 'items'));
            }
        }
        return redirect()->route("index");
    }

    public function marinasifreestockstore(Request $request)
    {
        if (User::setIjin(10)) {
            $request->validate([
                "row_id"        =>  ['required', Rule::exists('free_stock', 'id')->where('kategori', 3)],
                "freestock"     =>  ['required', Rule::exists('free_stocklist', 'id')->where('freestock_id', $request->row_id)],
                "item"          =>  ['required', Rule::exists('items', 'id')->where('category_id', 3)],
                "qty"           =>  'required|numeric|max:' . FreestockList::find($request->freestock)->sisa
            ]);

            $stocklist              =   FreestockList::find($request->freestock);
            $stocklist->sisa        =   $stocklist->sisa - $request->qty;
            $stocklist->save();

            $temp                   =   new FreestockTemp;
            $temp->freestock_id     =   $request->row_id;
            $temp->freestocklist_id =   $request->freestock;
            $temp->item_id          =   $request->item;
            $temp->qty              =   $request->qty;
            $temp->save();
        }
        return redirect()->route("index");
    }

    public function marinasifreestockselesai(Request $request)
    {
        if (User::setIjin(10)) {
            $freestock              =   Freestock::find($request->row_id);

            $temp   =   FreestockTemp::where('freestock_id', $freestock->id)->get();

            foreach ($temp as $row) {
                $chiler                     =   new Chiller;
                $chiler->table_name         =   'free_stock';
                $chiler->table_id           =   $freestock->id;
                $chiler->asal_tujuan        =   'krpartingmarinasi';
                $chiler->item_id            =   $row->item_id;
                $chiler->item_name          =   $row->item->nama;
                $chiler->jenis              =   'masuk';
                $chiler->no_mobil           =   $row->stocklist->chiller->no_mobil;
                $chiler->type               =   'hasil-produksi';
                $chiler->tanggal_potong     =   Carbon::now();
                $chiler->tanggal_produksi   =   Carbon::now();
                $chiler->qty_item           =   $row->qty;
                $chiler->stock_item         =   $row->qty;
                $chiler->status             =   1;
                $chiler->save();
            }

            FreestockTemp::where('freestock_id', $freestock->id)->delete();

            $list   =   FreestockList::where('freestock_id', $freestock->id)->get();

            foreach ($list as $row) {
                if ($row->sisa > 0) {
                    $chiler                     =   new Chiller;
                    $chiler->table_name         =   'free_stock';
                    $chiler->table_id           =   $freestock->id;
                    $chiler->asal_tujuan        =   'belum_terpakai';
                    $chiler->item_id            =   $row->item_id;
                    $chiler->item_name          =   $row->item->nama;
                    $chiler->jenis              =   'masuk';
                    $chiler->no_mobil           =   $row->chiller->no_mobil;
                    $chiler->type               =   'bahan-baku';
                    $chiler->tanggal_potong     =   Carbon::now();
                    $chiler->tanggal_produksi   =   Carbon::now();
                    $chiler->qty_item           =   $row->sisa;
                    $chiler->stock_item         =   $row->sisa;
                    $chiler->status             =   1;
                    $chiler->save();
                }
            }

            $freestock->status      =   2;
            $freestock->save();
        }
        return redirect()->route("index");
    }

    public function marinasifreestockdelete(Request $request)
    {
        if (User::setIjin(10)) {
            $stocklist              =   FreestockList::find($request->row_id);
            $stocklist->sisa        =   $stocklist->sisa + $request->qty;
            $stocklist->save();

            FreestockTemp::where('freestock_id', $request->row_id)
            ->where('freestocklist_id', $request->list)
            ->where('item_id', $request->item)
            ->where('qty', $request->qty)
            ->delete();
        }
        return redirect()->route("index");
    }

    public function marinasipending()
    {
        if(User::setIjin(10)){
            $pending    =   Order::where('status', 1)
                            ->whereIn('id', OrderItem::select('order_id')
                            ->whereIn('item_id', Item::select('id')
                            ->where('category_id', 3)))
                            ->get();

            return view('admin.pages.kepala_regu.marinasi.pending', compact('pending'));
        }
        return redirect()->route("index");
    }

    public function marinasidetail(Request $request)
    {
        if (User::setIjin(10)) {
            $chiller =  Chiller::where('jenis', 'masuk')->where('table_name', 'grading')->where('stock_item', '>', 0)->orderBy('item_id', 'ASC')->get();
            $detail  =  Order::find($request->customer);
            $item    =  OrderItem::find($request->item);
            return view('admin.pages.kepala_regu.marinasi.marinasiproses', compact('chiller', 'detail', 'item'));
        }
        return redirect()->route("index");
    }

    public function whole()
    {
        if(User::setIjin(11)){
            return view('admin.pages.kepala_regu.whole_chicken.index');
        }
        return redirect()->route("index");
    }

    public function wholeshow()
    {
        if(User::setIjin(11)){
            $order  =   Order::whereIn('status', [2,3,4,5])
                        ->whereIn('id', OrderItem::select('order_id')
                        ->whereIn('item_id', Item::select('id')
                        ->where('category_id', 1)))
                        ->get();

            return view('admin.pages.kepala_regu.whole_chicken.whole_show', compact('order'));
        }
        return redirect()->route("index");
    }

    public function wholespending()
    {
        if(User::setIjin(11)){
            $pending    =   Order::where('status', 1)
                            ->whereIn('id', OrderItem::select('order_id')
                            ->whereIn('item_id', Item::select('id')
                            ->where('category_id', 1)))
                            ->orderBy('nama', 'ASC')
                            ->paginate(10);

            return view('admin.pages.kepala_regu.whole_chicken.pending', compact('pending'));
        }
        return redirect()->route("index");
    }

    public function wholefreestock(Request $request)
    {
        if (User::setIjin(11)) {
            if ($request->id) {
                $data   =   FreestockTemp::where('freestock_id', $request->id)->get();

                return view('admin.pages.kepala_regu.whole_chicken.freestock.temporary', compact('data'));
            } else
            if ($request->list) {
                $list   =   FreestockList::where('freestock_id', $request->list)
                            ->get();

                return view('admin.pages.kepala_regu.whole_chicken.freestock.liststock', compact('list'));
            } else {
                $fresh  =   Freestock::where('kategori', 1)
                            ->where('status', 1)
                            ->paginate(10);

                $items  =   Item::where('category_id', 1)
                            ->orderBy('nama', 'ASC')
                            ->pluck('nama', 'id');

                return view('admin.pages.kepala_regu.whole_chicken.freestock.index', compact('fresh', 'items'));
            }
        }
        return redirect()->route("index");
    }

    public function wholefreestockstore(Request $request)
    {
        if (User::setIjin(11)) {
            $request->validate([
                "row_id"        =>  ['required', Rule::exists('free_stock', 'id')->where('kategori', 1)],
                "freestock"     =>  ['required', Rule::exists('free_stocklist', 'id')->where('freestock_id', $request->row_id)],
                "item"          =>  ['required', Rule::exists('items', 'id')->where('category_id', 1)],
                "qty"           =>  'required|numeric|max:' . FreestockList::find($request->freestock)->sisa
            ]);

            $stocklist              =   FreestockList::find($request->freestock);
            $stocklist->sisa        =   $stocklist->sisa - $request->qty;
            $stocklist->save();

            $temp                   =   new FreestockTemp;
            $temp->freestock_id     =   $request->row_id;
            $temp->freestocklist_id =   $request->freestock;
            $temp->item_id          =   $request->item;
            $temp->qty              =   $request->qty;
            $temp->save();
        }
        return redirect()->route("index");
    }

    public function wholefreestockselesai(Request $request)
    {
        if (User::setIjin(11)) {
            $freestock              =   Freestock::find($request->row_id);

            $temp   =   FreestockTemp::where('freestock_id', $freestock->id)->get();

            foreach ($temp as $row) {
                $chiler                     =   new Chiller;
                $chiler->table_name         =   'free_stock';
                $chiler->table_id           =   $freestock->id;
                $chiler->asal_tujuan        =   'krwhole';
                $chiler->item_id            =   $row->item_id;
                $chiler->item_name          =   $row->item->nama;
                $chiler->jenis              =   'masuk';
                $chiler->no_mobil           =   $row->stocklist->chiller->no_mobil;
                $chiler->type               =   'hasil-produksi';
                $chiler->tanggal_potong     =   Carbon::now();
                $chiler->tanggal_produksi   =   Carbon::now();
                $chiler->qty_item           =   $row->qty;
                $chiler->stock_item         =   $row->qty;
                $chiler->status             =   1;
                $chiler->save();
            }

            FreestockTemp::where('freestock_id', $freestock->id)->delete();

            $list   =   FreestockList::where('freestock_id', $freestock->id)->get();

            foreach ($list as $row) {
                if ($row->sisa > 0) {
                    $chiler                     =   new Chiller;
                    $chiler->table_name         =   'free_stock';
                    $chiler->table_id           =   $freestock->id;
                    $chiler->asal_tujuan        =   'belum_terpakai';
                    $chiler->item_id            =   $row->item_id;
                    $chiler->item_name          =   $row->item->nama;
                    $chiler->jenis              =   'masuk';
                    $chiler->no_mobil           =   $row->chiller->no_mobil;
                    $chiler->type               =   'bahan-baku';
                    $chiler->tanggal_potong     =   Carbon::now();
                    $chiler->tanggal_produksi   =   Carbon::now();
                    $chiler->qty_item           =   $row->sisa;
                    $chiler->stock_item         =   $row->sisa;
                    $chiler->status             =   1;
                    $chiler->save();
                }
            }

            $freestock->status      =   2;
            $freestock->save();
        }
        return redirect()->route("index");
    }

    public function wholefreestockdelete(Request $request)
    {
        if (User::setIjin(11)) {
            $stocklist              =   FreestockList::find($request->row_id);
            $stocklist->sisa        =   $stocklist->sisa + $request->qty;
            $stocklist->save();

            FreestockTemp::where('freestock_id', $request->row_id)
            ->where('freestocklist_id', $request->list)
            ->where('item_id', $request->item)
            ->where('qty', $request->qty)
            ->delete();
        }
        return redirect()->route("index");
    }

    public function wholedetail(Request $request)
    {
        if (User::setIjin(11)) {
            $chiller =  Chiller::where('jenis', 'masuk')->where('table_name', 'grading')->where('stock_item', '>', 0)->orderBy('item_id', 'ASC')->get();
            $detail  =  Order::find($request->customer);
            $item    =  OrderItem::find($request->item);
            return view('admin.pages.kepala_regu.whole_chicken.wholeproses', compact('chiller', 'detail', 'item'));
        }
        return redirect()->route("index");
    }

    public function frozen()
    {
        if(User::setIjin(12)){
            return view('admin.pages.kepala_regu.frozen.index');
        }
        return redirect()->route("index");
    }

    public function frozenshow()
    {
        if(User::setIjin(12)){
            $order      =   Order::whereIn('status', [2,3,4,5])
                            ->whereIn('id', OrderItem::select('order_id')
                            ->whereIn('item_id', Item::select('id')
                            ->whereIn('category_id', [7, 8, 9, 10, 11])))
                            ->get();

            return view('admin.pages.kepala_regu.frozen.frozen_show', compact('order'));
        }
        return redirect()->route("index");
    }

    public function frozenpending()
    {
        if(User::setIjin(12)){
            $pending    =   Order::where('status', 1)
                            ->whereIn('id', OrderItem::select('order_id')
                            ->whereIn('item_id', Item::select('id')
                            ->whereIn('category_id', [7, 8, 9, 10, 11])))
                            ->orderBy('nama', 'ASC')
                            ->paginate(10);

            return view('admin.pages.kepala_regu.frozen.pending', compact('pending'));
        }
        return redirect()->route("index");
    }

    public function frozenfreestock(Request $request)
    {
        if (User::setIjin(12)) {
            if ($request->id) {
                $data   =   FreestockTemp::where('freestock_id', $request->id)->get();

                return view('admin.pages.kepala_regu.frozen.freestock.temporary', compact('data'));
            } else
            if ($request->list) {
                $list   =   FreestockList::where('freestock_id', $request->list)
                            ->get();

                return view('admin.pages.kepala_regu.frozen.freestock.liststock', compact('list'));
            } else {
                $fresh  =   Freestock::whereIn('kategori', [7, 8, 9, 10, 11])
                            ->where('status', 1)
                            ->paginate(10);

                $items  =   Item::where('category_id', [7, 8, 9, 10, 11])
                            ->orderBy('nama', 'ASC')
                            ->pluck('nama', 'id');

                return view('admin.pages.kepala_regu.frozen.freestock.index', compact('fresh', 'items'));
            }
        }
        return redirect()->route("index");
    }

    public function frozenfreestockstore(Request $request)
    {
        if (User::setIjin(12)) {
            $request->validate([
                "row_id"        =>  ['required', Rule::exists('free_stock', 'id')->whereIn('kategori', [7, 8, 9, 10, 11])],
                "freestock"     =>  ['required', Rule::exists('free_stocklist', 'id')->where('freestock_id', $request->row_id)],
                "item"          =>  ['required', Rule::exists('items', 'id')->where('category_id', [7, 8, 9, 10, 11])],
                "qty"           =>  'required|numeric|max:' . FreestockList::find($request->freestock)->sisa
            ]);

            $stocklist              =   FreestockList::find($request->freestock);
            $stocklist->sisa        =   $stocklist->sisa - $request->qty;
            $stocklist->save();

            $temp                   =   new FreestockTemp;
            $temp->freestock_id     =   $request->row_id;
            $temp->freestocklist_id =   $request->freestock;
            $temp->item_id          =   $request->item;
            $temp->qty              =   $request->qty;
            $temp->save();
        }
        return redirect()->route("index");
    }

    public function frozenfreestockselesai(Request $request)
    {
        if (User::setIjin(12)) {
            $freestock              =   Freestock::find($request->row_id);

            $temp   =   FreestockTemp::where('freestock_id', $freestock->id)->get();

            foreach ($temp as $row) {
                $chiler                     =   new Chiller;
                $chiler->table_name         =   'free_stock';
                $chiler->table_id           =   $freestock->id;
                $chiler->asal_tujuan        =   'krfrozen';
                $chiler->item_id            =   $row->item_id;
                $chiler->item_name          =   $row->item->nama;
                $chiler->jenis              =   'masuk';
                $chiler->no_mobil           =   $row->stocklist->chiller->no_mobil;
                $chiler->type               =   'hasil-produksi';
                $chiler->tanggal_potong     =   Carbon::now();
                $chiler->tanggal_produksi   =   Carbon::now();
                $chiler->qty_item           =   $row->qty;
                $chiler->stock_item         =   $row->qty;
                $chiler->status             =   1;
                $chiler->save();
            }

            FreestockTemp::where('freestock_id', $freestock->id)->delete();

            $list   =   FreestockList::where('freestock_id', $freestock->id)->get();

            foreach ($list as $row) {
                if ($row->sisa > 0) {
                    $chiler                     =   new Chiller;
                    $chiler->table_name         =   'free_stock';
                    $chiler->table_id           =   $freestock->id;
                    $chiler->asal_tujuan        =   'belum_terpakai';
                    $chiler->item_id            =   $row->item_id;
                    $chiler->item_name          =   $row->item->nama;
                    $chiler->jenis              =   'masuk';
                    $chiler->no_mobil           =   $row->chiller->no_mobil;
                    $chiler->type               =   'bahan-baku';
                    $chiler->tanggal_potong     =   Carbon::now();
                    $chiler->tanggal_produksi   =   Carbon::now();
                    $chiler->qty_item           =   $row->sisa;
                    $chiler->stock_item         =   $row->sisa;
                    $chiler->status             =   1;
                    $chiler->save();
                }
            }

            $freestock->status      =   2;
            $freestock->save();
        }
        return redirect()->route("index");
    }

    public function frozenfreestockdelete(Request $request)
    {
        if (User::setIjin(12)) {
            $stocklist              =   FreestockList::find($request->row_id);
            $stocklist->sisa        =   $stocklist->sisa + $request->qty;
            $stocklist->save();

            FreestockTemp::where('freestock_id', $request->row_id)
            ->where('freestocklist_id', $request->list)
            ->where('item_id', $request->item)
            ->where('qty', $request->qty)
            ->delete();
        }
        return redirect()->route("index");
    }

    public function frozendetail(Request $request)
    {
        if (User::setIjin(12)) {
            $chiller =  Chiller::where('jenis', 'masuk')->where('table_name', 'grading')->where('stock_item', '>', 0)->orderBy('item_id', 'ASC')->get();
            $detail  =  Order::find($request->customer);
            $item    =  OrderItem::find($request->item);
            return view('admin.pages.kepala_regu.forzen.frozenproses', compact('chiller', 'detail', 'item'));
        }
        return redirect()->route("index");
    }

    public function store(Request $request)
    {
        if (User::setIjin(8) or User::setIjin(9) or User::setIjin(10) or User::setIjin(11) or User::setIjin(12)) {
            $data                        =   OrderItem::find($request->row_id);
            $bahan                       =   Bahanbaku::where('order_id', $data->order_id)->where('order_item_id', $data->id)->first();
            $data->kr_proses             =   Carbon::now();
            $data->fulfillment_berat     =   $bahan->bb_berat;
            $data->fulfillment_qty       =   $bahan->bb_item;
            $data->status                =   2;
            $data->save();


            $log_item                   = new OrderItemLog();
            $log_item->activity         = "kepala-regu-proses";
            $log_item->order_item_id    = $data->id;
            $log_item->user_id          = Auth::user()->id;
            $log_item->key              = AppKey::generate();
            $log_item->save();

            $order                      =   Order::find($data->order_id);
            $order->kr_selesai           =   Carbon::now();
            // $order->status              =   4;
            $order->save();
        }
        return redirect()->route("index");
    }

    public function storeall(Request $request)
    {
        if (User::setIjin(8) or User::setIjin(9) or User::setIjin(10) or User::setIjin(11) or User::setIjin(12)) {
            $data               =   OrderItem::where('order_id', $request->row_id)->get();

            foreach ($data as $row) {
                $raw               =   OrderItem::find($row->id);
                $raw->kr_proses    =   Carbon::now();
                $raw->status       =   3;
                $raw->save();

                $log_item                   = new OrderItemLog();
                $log_item->activity         = "kepala-regu-proses";
                $log_item->order_item_id    = $raw->id;
                $log_item->user_id          = Auth::user()->id;
                $log_item->key              = AppKey::generate();
                $log_item->save();

                $order              =   Order::find($raw->order_id);
                $order->kr_proses   =   Carbon::now();
                $order->save();
            }

            return back()->with('status', 1)->with('message', 'Data berhasil diperbaharui');
        }
        return redirect()->route("index");
    }

    public function selesai(Request $request)
    {
        if (User::setIjin(8) or User::setIjin(9) or User::setIjin(10) or User::setIjin(11) or User::setIjin(12)) {
            $order              =   Order::find($request->row_id);
            $order->status      =   4;
            $order->kr_selesai  =   Carbon::now();
            $order->save();

            return back()->with('status', 1)->with('message', 'Data berhasil diperbaharui');
        }
        return redirect()->route("index");
    }


    public function requestdetail(Request $request)
    {
        if (User::setIjin(7)) {
            $chiller    =   Chiller::whereIn('status', [1, 2])
                            ->where('jenis', 'masuk')
                            ->whereIn('table_name', ['grading', 'free_stock'])
                            ->where('stock_item', '>', 0)
                            ->orderBy('item_id', 'ASC')
                            ->where('status', 2)
                            ->get();

            $detail     =   Order::find($request->customer);
            $item       =   OrderItem::find($request->item);

            $bahan      =   Bahanbaku::select(DB::raw("SUM(bb_berat) AS jml_berat"), DB::raw("SUM(bb_item) AS jml_item"))
                            ->where('order_id', $request->customer)
                            ->where('order_item_id', $request->item)
                            ->first();

            // Redirect back to kepala regu masing-masing
            if ($item->item->category_id == 2) {
                $redirect   =  route("kepalaregu.parting");
            } else if ($item->item->category_id == 3) {
                $redirect   =  route("kepalaregu.marinasi");
            } else if (($item->item->category_id == 7) AND ($item->item->category_id == 8) AND ($item->item->category_id == 9) AND ($item->item->category_id == 10)) {
                $redirect   =  route("kepalaregu.frozen");
            } else if ($item->item->category_id == 1) {
                $redirect   =  route("kepalaregu.whole");
            } else {
                $redirect   =  '#';
            }

            return view('admin.pages.kepala_regu.requestbahanbaku', compact('chiller', 'detail', 'item', 'bahan', 'redirect'));
        }
        return redirect()->route("index");
    }

    public function storerequestbahanbaku(Request $request)
    {
        if (User::setIjin(12)) {

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
                $chiller->asal_tujuan       =   'kepala_regu';
                $chiller->no_mobil          =   $datchill->no_mobil;
                $chiller->item_id           =   $datchill->item_id;
                $chiller->item_name         =   $datchill->item_name;
                $chiller->jenis             =   'keluar';
                $chiller->type              =   'pengambilan-bahan-baku';
                $chiller->tanggal_potong    =   Carbon::now();
                $chiller->tanggal_produksi  =   Carbon::now();
                $chiller->qty_item          =   $request->qty;
                $chiller->berat_item        =   $request->berat;
                $chiller->status            =   4;
                $chiller->save();


                $bahanbaku                  =   new Bahanbaku;
                $bahanbaku->chiller_id      =   $request->chiller;
                $bahanbaku->chiller_out     =   $chiller->id;
                $bahanbaku->order_id        =   $request->order;
                $bahanbaku->order_item_id   =   $data->id;
                $bahanbaku->bb_item         =   $request->qty;
                $bahanbaku->bb_berat        =   $request->berat;
                $bahanbaku->save();

                $log_item                   = new OrderItemLog();
                $log_item->activity         = "kepala-regu-bahan-baku";
                $log_item->order_item_id    = $data->id;
                $log_item->user_id          = Auth::user()->id;
                $log_item->key              = AppKey::generate();
                $log_item->save();

                $datchill->stock_berat      =   $datchill->stock_berat - $request->berat;
                $datchill->stock_item       =   $datchill->stock_item - $request->qty;
                $datchill->save();

                DB::commit();

                return back()->with('status', 1)->with('message', 'Data berhasil diperbaharui');
            } else {
                DB::rollBack() ;
                return back()->with('status', 2)->with('message', 'Input melebihi stock tersedia');
            }
        }
        return redirect()->route("index");
    }

    public function sendabf(Request $request)
    {
        if (User::setIjin(12)) {
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

                $chiller                    =   new Abf();
                $chiller->table_name        =   'order_item';
                $chiller->table_id          =   $data->id;
                $chiller->item_id           =   $item->id;
                $chiller->item_name         =    $item->nama;
                $chiller->tanggal_masuk     =   date('Y-m-d');
                $chiller->asal_tujuan       =   $category->nama;
                $chiller->jenis             =   'masuk';
                $chiller->type              =   'hasil-produksi';
                $chiller->qty_awal          =   $data->qty;
                $chiller->berat_awal        =   $data->berat;
                $chiller->qty_item          =   $data->qty;
                $chiller->berat_item        =   $data->berat;
                $chiller->status            =   '2';
                $chiller->save();

                $data->kr_selesai           =   Carbon::now();
                $data->status               =   2;
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

                $log_item                   =   new OrderItemLog();
                $log_item->activity         =   "kepala-regu-kirim-abf";
                $log_item->order_item_id    =   $data->id;
                $log_item->user_id          =   Auth::user()->id;
                $log_item->key              =   AppKey::generate();
                $log_item->save();

                $item                       =   Item::find($data->item_id);

                $category                   =   Category::find($item->category_id);

                $chiller                    =   new Abf;
                $chiller->production_id     =   $datchill->production_id;
                $chiller->table_name        =   'order_item';
                $chiller->table_id          =   $data->id;
                $chiller->tanggal_masuk     =   date('Y-m-d');
                $chiller->asal_tujuan       =   $category->nama;
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
                $item->status               =   2;
                $item->fulfillment_berat    =   $berat;
                $item->fulfillment_qty      =   $qty;
                $item->save();
            }
        }
        return redirect()->route("index");
    }

    public function sendchiller(Request $request)
    {

        DB::beginTransaction();

        if (User::setIjin(12)) {
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
                $chiller->asal_tujuan       =   'kepala_regu';
                $chiller->jenis             =   'masuk';
                $chiller->type              =   'hasil-produksi';
                $chiller->kategori          =   $data->item->itemkat->nama;
                $chiller->tanggal_potong    =   Carbon::now();
                $chiller->tanggal_produksi  =   Carbon::now();
                $chiller->qty_item          =   $data->qty;
                $chiller->berat_item        =   $data->berat;
                $chiller->status            =   2;
                $chiller->save();

                $data->kr_selesai           =   Carbon::now();
                $data->status               =   2;
                $data->fulfillment_berat    =   $berat;
                $data->fulfillment_qty      =   $qty;
                $data->save();

                DB::commit();
            } elseif ($order->order_id != '') {

                $berat                      =   Bahanbaku::where('order_id', $request->order)->where('order_item_id', $request->item)->sum('bb_berat');
                $qty                        =   Bahanbaku::where('order_id', $request->order)->where('order_item_id', $request->item)->sum('bb_item');

                $datchill                   =   Chiller::find($order->chiller_id);

                $data                       =   OrderItem::find($request->item);

                $log_item                   =   new OrderItemLog();
                $log_item->activity         =   "kepala-regu-kirim-chiller";
                $log_item->order_item_id    =   $data->id;
                $log_item->user_id          =   Auth::user()->id;
                $log_item->key              =   AppKey::generate();
                $log_item->save();

                $item                       =   Item::find($data->item_id);

                $category                   =   Category::find($item->category_id);

                $chiller                    =   new Chiller;
                $chiller->production_id     =   $datchill->production_id;
                $chiller->table_name        =   'order_item';
                $chiller->table_id          =   $data->id;
                $chiller->asal_tujuan       =   'kepala_regu';
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
                $chiller->status            =   2;
                $chiller->save();

                $item                       =   OrderItem::where('order_id', $request->order)->where('id', $request->item)->first();
                $item->kr_selesai           =   Carbon::now();
                $item->status               =   2;
                $item->fulfillment_berat    =   $berat;
                $item->fulfillment_qty      =   $qty;
                $item->save();
                DB::commit();
            }
        }
        return redirect()->route("index");
    }

    public function bbbonless()
    {
        if (User::setIjin(12)) {
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

            return view('admin.pages.kepala_regu.bonles.bahanbakubonles', compact('bhnbb', 'bahanbaku', 'bonless', 'bahanbonles', 'free', 'bahanbakuselesai'));
        }
        return redirect()->route("index");
    }
    public function ambilbbbonless()
    {
        if (User::setIjin(12)) {

            $bahanbonles    =   Item::whereIn('id', Chiller::select('item_id')
                                ->where('table_name', 'grading')
                                ->where('status', 2)
                                ->where('jenis', 'masuk')
                                ->where('stock_item', '>', 0)
                                ->pluck('item_id'))
                                ->get();

            $free   =   Chiller::where('jenis', 'masuk')->where('type', 'free')->get();


            $data   =   Freestock::where('kategori', 5)->where('status',1)->get();

            return view('admin.pages.kepala_regu.bonles.ambilbb', compact('data', 'bahanbonles', 'free'));
        }
        return redirect()->route("index");
    }

    public function requestdetailboneles(Request $request)
    {
        if (User::setIjin(7)) {
            $chiller =  Chiller::whereIn('status', [1, 2])
                ->where('jenis', 'masuk')
                ->whereIn('table_name', ['free_stock_tmp'])
                ->where('stock_berat', '>', 0)
                ->orderBy('item_id', 'ASC')
                ->get();


            $detail  =  Order::find($request->customer);
            $item    =  OrderItem::find($request->item);

            $bahan   =  Bahanbaku::select(DB::raw("SUM(bb_berat) AS jml_berat"), DB::raw("SUM(bb_item) AS jml_item"))
                        ->where('order_id', $request->customer)
                        ->where('order_item_id', $request->item)
                        ->first();

            $redirect=  route("kepalaregu.boneles");

            return view('admin.pages.kepala_regu.requestbahanbaku', compact('chiller', 'detail', 'item', 'bahan', 'redirect'));
        }
        return redirect()->route("index");
    }

    public function hasilproduksibonless()
    {
        $bonlesselesai   =   Freestock::where('kategori', 5)->where('status', 2)->get();

        return view('admin.pages.kepala_regu.bonles.hasil', compact('bonlesselesai'));
    }

    public function databahan()
    {
        $data   =   Freestock::where('kategori', 5)->where('status', 1)->get();

        return view('admin.pages.kepala_regu.bonles.databahan', compact('data'));
    }


    public function broiler()
    {

        $item  =   Item::where('category_id', 5)->where('slug', 'LIKE','%broiler')->get();

        return view('admin.pages.kepala_regu.bonles.itembb', compact('item'));
    }
    public function pejantan()
    {

        $item  =   Item::where('category_id', 5)->where('slug', 'LIKE','%pejantan')->get();

        return view('admin.pages.kepala_regu.bonles.itembb', compact('item'));
    }
    public function kampung()
    {

        $item  =   Item::where('category_id', 5)->where('slug', 'LIKE','%kampung')->get();

        return view('admin.pages.kepala_regu.bonles.itembb', compact('item'));
    }
    public function parent()
    {

        $item  =   Item::where('category_id', 5)->where('slug', 'LIKE','%parent')->get();

        return view('admin.pages.kepala_regu.bonles.itembb', compact('item'));
    }

    public function storefreeboneles(Request $request)
    {
        if (User::setIjin(7)) {
            $total  =   0;
            $lebih  =   FALSE;
            $stock  =   Freestock::where('kategori',5)->where('status', 1)->first();


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
                        $outchiller->table_name         =   'free_stock_boneless';
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
                        $outchiller->status             =   4;
                        $outchiller->save();

                        $chiller->stock_berat           =   $request->qty[$x];
                        $chiller->save();

                    } else {
                        $lebih  =   TRUE;
                    }
                }
            }

            if ($total > 0 and empty($stock->status)) {
                $freestock              =   new Freestock;
                $freestock->nomor       =   Freestock::get_nomor();
                $freestock->tanggal     =   Carbon::now();
                $freestock->user_id     =   Auth::user()->id;
                $freestock->kategori    =   5;
                $freestock->status      =   1;
                $freestock->save();

                FreestockList::where('freestock_id')->update([
                    'freestock_id'  =>  $freestock->id
                ]);

                return redirect()->to(url()->previous() . "#custom-tabs-three-bonles")->with('status', 1)->with('message', 'Buat free stock berhasil');
            } elseif ($total > 0 and $stock->status == 1) {

                $stock->tanggal     =   Carbon::now();
                $stock->save();

                FreestockList::where('freestock_id')->update([
                    'freestock_id'  =>  $stock->id
                ]);

                return redirect()->to(url()->previous() . "#custom-tabs-three-bonles")->with('status', 1)->with('message', 'Buat free stock berhasil');
            } else {
                return redirect()->to(url()->previous() . "#custom-tabs-three-bonles")->with('status', 2)->with('message', $lebih ? 'Permintaan bahan baku melebihi stock tersedia' : 'Buat free stock gagal');
            }

            return redirect()->to(url()->previous() . "#custom-tabs-three-bonles");
        }
        return redirect()->route("index");
    }

    public function bonelessreestockstore(Request $request)
    {
        if (User::setIjin(11)) {
            $request->validate([

                "item"          =>  'required',
                "qty"           =>  'required|numeric',
            ]);

            $stock                  =   Freestock::find($request->id);

            $temp                   =   new FreestockTemp;
            $temp->freestock_id     =   $request->id;
            $temp->item_id          =   $request->item;
            $temp->qty              =   $request->qty;
            $temp->tanggal_produksi =   Carbon::now();
            $temp->kategori          =   $stock->kategori;
            $temp->save();
        }
        return redirect()->route("index");
    }

    public function temporary()
    {
        $data   =   FreestockTemp::where('kategori', 5)->whereIn('freestock_id', Freestock::select('id')->where('status', 1))->get();

        return view('admin.pages.kepala_regu.bonles.temporary', compact('data'));
    }

    public function bonelessfreestockdelete(Request $request)
    {
        if (User::setIjin(11)) {
            // $stocklist              =   FreestockList::find($request->row_id);
            // $stocklist->sisa        =   $stocklist->sisa + $request->qty;
            // $stocklist->save();

            FreestockTemp::where('id', $request->row_id)
                ->delete();
        }
        return redirect()->route("index");
    }

    public function storebon(Request $request)
    {
        if (User::setIjin(12)) {
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

        $temp       =   FreestockTemp::where('freestock_id', $request->bahan)->get();

        $stock      =   Freestock::find($request->bahan);

        foreach ($temp as $row) {
            $chiller                    =   new Chiller;
            $chiller->table_name        =   'free_stock_tmp';
            $chiller->table_id          =   $row->id;
            $chiller->asal_tujuan       =   'free_boneless';
            $chiller->item_id           =   $row->item_id;
            $chiller->item_name         =   $row->item->nama;
            $chiller->jenis             =   'masuk';
            $chiller->type              =   'hasil-produksi';
            $chiller->berat_item        =   $row->qty;
            $chiller->tanggal_produksi  =   $row->tanggal_produksi;
            $chiller->status            =   2;
            $chiller->save();

        }

        $stock->status  =   2;
        $stock->save();


    }


}
