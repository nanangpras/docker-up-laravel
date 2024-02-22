<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Adminedit;
use App\Models\Bom;
use App\Models\Chiller;
use App\Models\Customer;
use App\Models\Evis;
use App\Models\Evisproses;
use App\Models\Freestock;
use App\Models\FreestockList;
use App\Models\FreestockTemp;
use App\Models\Grading;
use App\Models\Gudang;
use App\Models\Item;
use App\Models\Log;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Production;
use App\Models\Purchasing;
use App\Models\Netsuite;
use App\Models\User;
use BaconQrCodeTest\Common\ReedSolomonTest;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use PHPUnit\Framework\Constraint\Count;

use function PHPUnit\Framework\isEmpty;

class EvisController extends Controller
{

    public function index(Request $request)
    {
        if (User::setIjin(6)) {
            // $tanggal    =   $request->tanggal ?? Carbon::now()->format('Y-m-d');
            $tanggalawal        =   $request->tanggalawal ?? Carbon::now()->format('Y-m-d');
            $tanggalakhir       =   $request->tanggalakhir ?? Carbon::now()->format('Y-m-d');

            $evis       =   Production::where('no_urut', '!=', NULL)
                ->where('po_jenis_ekspedisi', '!=', 'other')
                ->whereIn('purchasing_id', Purchasing::select('id')
                    // ->whereDate('tanggal_potong', $tanggal)
                    ->whereIn('type_po', ['PO LB', 'PO Maklon']))
                // ->where('lpah_tanggal_potong', $tanggal)
                ->whereBetween('lpah_tanggal_potong', [$tanggalawal, $tanggalakhir])
                ->whereIn('lpah_status', [1, 2, 3])
                ->orderByRaw('lpah_tanggal_potong ASC, no_urut ASC')
                ->get();

            $evisnonlb  =   Production::whereIn('ppic_acc', [1, 2, 3])
                ->where('ppic_tujuan', 'evis')
                ->whereIn('purchasing_id', Purchasing::select('id')
                    ->whereBetween('tanggal_potong', [$tanggalawal, $tanggalakhir])
                    // ->where('tanggal_potong', $tanggal)
                    ->where('type_po', '!=', 'PO LB')
                    ->where('type_po', '!=', 'PO Maklon'))
                // ->where('lpah_tanggal_potong', $tanggal)
                ->get();

            $count      =   Production::whereIn('lpah_status', [1, 2, 3])
                ->where('po_jenis_ekspedisi', '!=', 'other')
                ->whereIn('purchasing_id', Purchasing::select('id')
                    // ->whereDate('tanggal_potong', $tanggal)
                    ->whereIn('type_po', ['PO LB', 'PO Maklon']))
                ->where(function ($query) {
                    $query->orWhere('evis_status', NULL);
                    $query->orWhere('evis_status', 1);
                    $query->orWhere('evis_status', 2);
                })
                ->whereBetween('lpah_tanggal_potong', [$tanggalawal, $tanggalakhir])
                ->where('ppic_acc', 1)
                ->count('id');

            $sumberat   =   Production::select('sc_berat_do')
                ->whereIn('lpah_status', [1, 2, 3])
                ->where('po_jenis_ekspedisi', '!=', 'other')
                ->whereIn('purchasing_id', Purchasing::select('id')
                    // ->whereDate('tanggal_potong', $tanggal)
                    ->whereIn('type_po', ['PO LB', 'PO Maklon']))
                ->where(function ($query) {
                    $query->orWhere('evis_status', NULL);
                    $query->orWhere('evis_status', 1);
                    $query->orWhere('evis_status', 2);
                })
                ->whereBetween('lpah_tanggal_potong', [$tanggalawal, $tanggalakhir])
                ->where('ppic_acc', 1)
                ->sum('sc_berat_do');

            $sumekor    =   Production::select('sc_ekor_do')
                ->whereIn('lpah_status', [1, 2, 3])
                ->where('po_jenis_ekspedisi', '!=', 'other')
                ->whereIn('purchasing_id', Purchasing::select('id')
                    // ->whereDate('tanggal_potong', $tanggal)
                    ->whereIn('type_po', ['PO LB', 'PO Maklon']))
                ->where(function ($query) {
                    $query->orWhere('evis_status', NULL);
                    $query->orWhere('evis_status', 1);
                    $query->orWhere('evis_status', 2);
                })
                ->whereBetween('lpah_tanggal_potong', [$tanggalawal, $tanggalakhir])
                ->where('ppic_acc', 1)
                ->sum('sc_ekor_do');

            $countnonlb     =   Production::whereIn('lpah_status', [1, 2, 3])
                ->where('ppic_tujuan', 'evis')
                ->whereIn('purchasing_id', Purchasing::select('id')
                    // ->whereDate('tanggal_potong', $tanggal))
                    ->whereBetween('tanggal_potong', [$tanggalawal, $tanggalakhir]))
                // ->where('lpah_tanggal_potong', $tanggal)
                ->where(function ($query) {
                    $query->orWhere('evis_status', NULL);
                    $query->orWhere('evis_status', 1);
                    $query->orWhere('evis_status', 2);
                })
                ->count('id');

            $sumberatnonlb  =   Production::select('sc_berat_do')
                ->where('ppic_tujuan', 'evis')
                ->whereIn('lpah_status', [1, 2, 3])
                ->whereIn('purchasing_id', Purchasing::select('id')
                    ->whereBetween('tanggal_potong', [$tanggalawal, $tanggalakhir]))
                // ->whereDate('tanggal_potong', $tanggal))
                // ->where('lpah_tanggal_potong', $tanggal)
                ->where(function ($query) {
                    $query->orWhere('evis_status', NULL);
                    $query->orWhere('evis_status', 1);
                    $query->orWhere('evis_status', 2);
                })
                ->sum('sc_berat_do');

            $sumekornonlb   =   Production::select('sc_ekor_do')
                ->where('ppic_tujuan', 'evis')
                ->whereIn('lpah_status', [1, 2, 3])
                ->whereIn('purchasing_id', Purchasing::select('id')
                    // ->whereDate('tanggal_potong', $tanggal))
                    ->whereBetween('tanggal_potong', [$tanggalawal, $tanggalakhir]))
                // ->where('lpah_tanggal_potong', $tanggal)
                ->where(function ($query) {
                    $query->orWhere('evis_status', NULL);
                    $query->orWhere('evis_status', 1);
                    $query->orWhere('evis_status', 2);
                })
                ->sum('sc_ekor_do');

            $mobil_lama     =   Production::where('no_urut', '!=', NULL)
                ->whereIn(
                    'purchasing_id',
                    Purchasing::select('id')
                        ->whereIn('type_po', ['PO LB', 'PO Maklon'])
                    // ->where(function ($query) use ($tanggal) {
                    //     $query->whereDate('tanggal_potong', date('Y-m-d', strtotime('yesterday')));
                    // })
                )
                ->where('sc_status', 1)
                ->whereIn('lpah_status', [1, 2])
                ->whereNull('evis_status')
                ->get();

            $total  =   [
                'jumlah'        =>  $count,
                'berat'         =>  $sumberat,
                'ekor'          =>  $sumekor,
                'jumlahnonlb'   =>  $countnonlb,
                'beratnonlb'    =>  $sumberatnonlb,
                'ekornonlb'     =>  $sumekornonlb
            ];

            return view('admin/pages/evis/index', compact('evis', 'tanggalawal', 'tanggalakhir', 'total', 'evisnonlb', 'mobil_lama'));
        }
        return redirect()->route("index");
    }

    public function order()
    {
        $order  =   Order::whereIn('id', OrderItem::select('order_id')
            ->whereIn(
                'item_id',
                Item::select('id')
                // ->whereIn('category_id', [4,6])
            ))
            ->orderBy('nama', 'ASC')
            ->paginate(15);

        return view('admin.pages.evis.order', compact('order'));
    }

    public function summary(Request $request)
    {
        if (User::setIjin(6)) {

            $tanggal    =   $request->tanggal ?? date('Y-m-d');
            $lpah       =   $request->lpah ?? "";

            $evis       =   Evis::select(DB::raw('SUM(total_item) AS total'), DB::raw("SUM(berat_item) AS berat"), 'item_id', 'jenis')
                ->whereDate('tanggal_potong', $tanggal)
                ->groupBy('item_id')
                ->groupBy('jenis')
                ->get();


            $evisselesai    =   Freestock::where('regu', 'byproduct')
                ->whereDate('tanggal', $tanggal)
                ->where('status', 3)
                ->get();
            $tot_bb_pcs = 0;
            $tot_bb_kg = 0;
            $tot_hp_pcs = 0;
            $tot_hp_kg = 0;
            foreach ($evisselesai as $ev) {
                foreach ($ev->listfreestock as $key) {
                    $tot_bb_pcs += $key->qty;
                    $tot_bb_kg += $key->berat;
                }
                foreach ($ev->freetemp as $bb) {
                    $tot_hp_pcs += $bb->qty;
                    $tot_hp_kg += $bb->berat;
                }
            }

            // dd($tot);
            // dd($tot_hp);

            return view('admin.pages.evis.summary', compact('evis', 'evisselesai', 'tanggal', 'tot_bb_pcs', 'tot_bb_kg', 'tot_hp_pcs', 'tot_hp_kg'));
        }
        return redirect()->route("index");
    }

    public function store(Request $request)
    {
        if (User::setIjin(6)) {
            DB::beginTransaction();

            $prod                   =   Production::find($request->x_code);
            $prod->evis_proses      =   Carbon::now();
            $prod->evis_status      =   2;
            $prod->evis_user_id     =   Auth::user()->id;
            if (!$prod->save()) {
                DB::rollBack();
            }

            DB::commit();

            return redirect()->route('evis.show', $prod->id)->with('status', 1)->with('message', 'Selesaikan Timbang');
        }
        return redirect()->route("index");
    }


    public function show($id)
    {
        if (User::setIjin(6)) {
            $data       =   Production::where('id', $id)
                ->whereIn('evis_status', [1, 2, 3])
                ->first();

            $item       =   Item::whereIn('category_id', [4, 6])
                ->whereOr('by_product', 1)
                ->get();

            $summary    =   Evis::where('production_id', $id)
                ->get();

            $grading    =   Grading::where('trans_id', $id)->sum('berat_item');

            $count      =   0;
            $sumberat   =   0;
            $sumekor    =   0;
            foreach ($summary as $row) {
                $count      +=  1;
                $sumberat   +=  $row->berat_stock;
                $sumekor    +=  $row->total_item;
            }

            $total  =   [
                'jumlah'    =>  $count,
                'berat'     =>  $sumberat,
                'ekor'      =>  $sumekor
            ];

            if ($data) {
                return view('admin.pages.evis.evis-timbang', compact('data', 'summary', 'item', 'total', 'grading'));
            }

            return redirect()->route('evis.index')->with('status', 2)->with('message', 'Data tidak ditemukan');
        }
        return redirect()->route("index");
    }

    public function add(Request $request)
    {
        if (User::setIjin(6)) {

            $data   =   Production::select('id', 'sc_tanggal_masuk', 'no_urut', 'lpah_tanggal_potong')
                ->whereIn('evis_status', [1, 2])
                ->where('id', $request->row_id)
                ->first();

            if ($request->idedit == NULL) {

                DB::beginTransaction();

                $evis                       =   new Evis;
                $evis->production_id        =   $request->row_id;
                $evis->total_item           =   $request->result;
                $evis->berat_item           =   $request->berat;
                $evis->stock_item           =   $request->result;
                $evis->berat_stock          =   $request->berat;
                $evis->item_id              =   $request->part;
                $evis->peruntukan           =   'stock';
                $evis->jenis                =   $request->jenis;
                $evis->tanggal_potong       =   $data->lpah_tanggal_potong;
                if (!$evis->save()) {
                    DB::rollBack();
                    return back()->with('status', 2)->with('message', 'Proses gagal');
                }

                DB::commit();
                return back()->with('status', 1)->with('message', 'Data berhasil ditambahkan');
            }

            if ($request->idedit) {
                $evis                   =   Evis::find($request->idedit);
                $evis->total_item       =   $request->result;
                $evis->berat_item       =   $request->berat;
                $evis->stock_item       =   $request->result;
                $evis->berat_stock      =   $request->berat;
                $evis->item_id          =   $request->part;
                $evis->peruntukan       =   'stock';
                $evis->jenis            =   $request->jenis;
                if (!$evis->save()) {
                    DB::rollBack();
                    return back()->with('status', 2)->with('message', 'Proses gagal');
                }

                DB::commit();
                return back()->with('status', 1)->with('message', 'Data berhasil diperbaharui');
            }
        }
        return redirect()->route("index");
    }

    public function edit(Request $request, $id)
    {
        if (User::setIjin(6)) {
            if ($request->key == 'petugas') {
                $data                   =   Production::find($id);
                $data->evis_user_name   =   $request->nama_petugas;
                $data->save();

                return '';
            } else {
                return Evis::find($request->row_id);
            }
        }
        return redirect()->route("index");
    }

    public function editgabung(Request $request)
    {
        if (User::setIjin(6)) {
            return Evis::find($request->row_id);
        }
        return redirect()->route("index");
    }

    public function update(Request $request, $id)
    {
        if (User::setIjin(6)) {
            $data   =   Production::select('id', 'sc_tanggal_masuk', 'no_urut', 'lpah_tanggal_potong', 'purchasing_id')
                        ->whereIn('evis_status', [2, 3])
                        ->where('id', $id)
                        ->first();

            if ($data) {
                if ($request->key == 'back') {
                    $data->evis_status  =   2;
                    $data->save();

                    return back();
                } else

                if ($request->key == 'send') {
                    DB::beginTransaction();

                    $evis   =   Evis::where('production_id', $data->id)->get();

                    foreach ($evis as $row) {

                        // $evisStore   =   Chiller::whereDate('tanggal_potong', $data->lpah_tanggal_potong)
                        //                     ->where('item_id', $row->item_id)
                        //                     ->where('status', 2)
                        //                     ->where('type', 'bahan-baku')
                        //                     ->where('asal_tujuan', 'evisgabungan')
                        //                     ->first();

                        $evisStore   =   Chiller::updateOrCreate([
                            'item_id'           => $row->item_id,
                            'status'            => 2,
                            'type'              => 'bahan-baku',
                            'asal_tujuan'       => 'evisgabungan',
                            'tanggal_potong'    => $data->lpah_tanggal_potong
                        ]);

                        $evisStore->qty_item             =   $evisStore->qty_item + $row->total_item;
                        $evisStore->stock_item           =   $evisStore->stock_item + $row->total_item;
                        $evisStore->berat_item           =   $evisStore->berat_item + $row->berat_item;
                        $evisStore->stock_berat          =   $evisStore->stock_berat + $row->berat_item;
                        $evisStore->regu                 =   'byproduct';
                        $evisStore->item_name            =   $row->eviitem->nama;
                        $evisStore->tanggal_produksi     =   $data->lpah_tanggal_potong;
                        $evisStore->tanggal_potong       =   $data->lpah_tanggal_potong;
                        $evisStore->asal_tujuan          =   'evisgabungan';
                        $evisStore->type                 =   'bahan-baku';
                        $evisStore->status               =   2;
                        $evisStore->jenis                =   'masuk';

                        if (!$evisStore->save()) {
                            DB::rollBack();
                            return back()->with('status', 2)->with('message', 'Proses gagal');
                        }

                        // if ($evisStore) {
                        //     // $evisStore->berat_keranjang      =   $evisStore->berat_keranjang + $row->berat_keranjang;

                        //     if (!$evisStore->save()) {
                        //         DB::rollBack();
                        //         return back()->with('status', 2)->with('message', 'Proses gagal');
                        //     }
                        // } else {
                        //     $newchiler                      =   new Chiller;
                        //     $newchiler->asal_tujuan         =   'evisgabungan';
                        //     $newchiler->type                =   'bahan-baku';
                        //     $newchiler->regu                =   'byproduct';
                        //     $newchiler->item_id             =   $row->item_id;
                        //     $newchiler->item_name           =   $row->eviitem->nama;
                        //     $newchiler->tanggal_potong      =   $data->lpah_tanggal_potong;
                        //     $newchiler->tanggal_produksi    =   $data->lpah_tanggal_potong;
                        //     // $newchiler->keranjang           =   $row->keranjang;
                        //     // $newchiler->berat_keranjang     =   $row->berat_keranjang;
                        //     $newchiler->qty_item            =   $row->total_item;
                        //     $newchiler->stock_item          =   $row->total_item;
                        //     $newchiler->berat_item          =   $row->berat_item;
                        //     $newchiler->stock_berat         =   $row->berat_item;
                        //     $newchiler->status              =   2;
                        //     $newchiler->jenis               =   'masuk';
                        //     if (!$newchiler->save()) {
                        //         DB::rollBack();
                        //         return back()->with('status', 2)->with('message', 'Proses gagal');
                        //     }
                        // }
                    }

                    $data->evis_selesai     =   Carbon::now();
                    $data->evis_status      =   1;
                    $data->evis_user_id     =   Auth::user()->id;
                    if (!$data->save()) {
                        DB::rollBack();
                        return redirect()->route('evis.index')->with('status', 2)->with('message', 'Terjadi kesalahan saat menyelesaikan evis');
                    }

                    DB::commit();

                    try {
                        Chiller::recalculate_chiller($evisStore->id);
                    } catch (\Throwable $th) {
                    }

                    try {
                        Chiller::recalculate_chiller($evis->id);
                    } catch (\Throwable $th) {
                    }



                    // if ($data->lpah_status == 1) {
                    // Netsuite::wo_1($data->id);
                    // }

                    return back()->with('status', 1)->with('message', 'Proses berhasil diselesaikan');
                } else {
                    $data->evis_status  =   3;
                    $data->save();

                    return back()->with('status', 1)->with('message', 'Data disimpan');
                }
            }
            return back()->with('status', 2)->with('message', 'Terjadi kesalahan saat menyelesaikan timbangan evis');
        }
        return redirect()->route("index");
    }

    public function cart($id)
    {
        if (User::setIjin(6)) {
            $prod   =   Production::find($id);
            $data   =   Evis::where('production_id', $id)->get();
            return view('admin.pages.evis.keranjang', compact('data', 'prod'));
        }
        return redirect()->route("index");
    }

    public function cartgabung()
    {
        if (User::setIjin(6)) {
            $gabungan   =   Evis::where('jenis', 'gabungan')->orderBy('id', 'DESC')->get();
            // return $list;
            return view('admin.pages.evis.keranjanggabung', compact('gabungan'));
        }
        return redirect()->route("index");
    }

    public function gabung()
    {
        if (User::setIjin(6)) {
            $item = Item::whereIn('category_id', [4, 6])->whereOr('by_product', 1)->get();

            $chiller =  Chiller::whereIn('status', [1, 2])
                ->where('jenis', 'masuk')
                ->where('table_name', 'evis')
                ->where('type', 'bahan-baku')
                ->where('stock_item', '>', 0)
                ->orderBy('item_id', 'ASC')
                ->get();

            $summary   =   Evis::where('jenis', 'gabungan')->orderBy('id', 'DESC')->get();
            $count      =   0;
            $sumberat   =   0;
            $sumekor    =   0;
            foreach ($summary as $row) {
                $count      +=  1;
                $sumberat   +=  $row->berat_stock;
                $sumekor    +=  $row->total_item;
            }

            $total  =   [
                'jumlah'    =>  $count,
                'berat'     =>  $sumberat,
                'ekor'      =>  $sumekor
            ];
            return view('admin.pages.evis.gabungan', compact('item', 'summary', 'total', 'chiller'));
        }
        return redirect()->route("index");
    }

    public function bbevis(Request $request)
    {
        // dd($request->all());
        $chiller    =   Chiller::where('tanggal_produksi', $request->tanggal ?? date('Y-m-d'))
            ->whereIn('status', [1, 2])
            ->where(function ($query) use ($request) {
                if ($request->bbtype == 'evis_fg') {
                    $query->where(function ($query2) {
                        $query2->orWhere('asal_tujuan', 'evisbeli');
                        $query2->orWhere('asal_tujuan', 'hasilbeli');
                        $query2->orWhere('asal_tujuan', 'free_stock');
                        $query2->orWhere('asal_tujuan', 'retur');
                        $query2->orWhere('asal_tujuan', 'thawing');
                    });
                    $query->where('type', 'hasil-produksi');
                    $query->whereIn('item_id', Item::select('id')->whereIn('category_id', [4, 6, 10]));
                } else if ($request->bbtype == 'evis_karkas') {
                    // $query->orWhere('asal_tujuan','gradinggabungan');
                    $query->orWhere('asal_tujuan', 'evisgabungan');
                    $query->where('type', 'bahan-baku');
                    $query->whereIn('item_id', Item::select('id')->whereIn('category_id', [4, 6, 10]));
                } else if ($request->bbtype == 'evis_thawing') {
                    $query->where('asal_tujuan', 'thawing');
                    $query->where('type', 'hasil-produksi');
                    $query->whereIn('item_id', Item::select('id')->whereIn('category_id', [4, 6, 10]));
                } else {
                    $query->where('type', 'bahan-baku');
                    $query->where('asal_tujuan', 'evisgabungan');
                }
            })
            ->where(function($qs){
                $qs->Orwhere('chiller.stock_item', '>', 0);
                $qs->Orwhere('chiller.stock_berat', '>', 0);
                $qs->where('chiller.stock_berat', 'NOT LIKE','%-%');
            })
            ->where('status_cutoff',NULL)
            ->orderBy('item_name', 'ASC');

        // try {
        //     //code...
        //     $bb  =  $chiller->orderBy('item_name', 'ASC')->get();
        //     foreach ($bb as $row) {
        //         for ($x = 0; $x <= Count($bb); $x++) {
        //             Chiller::recalculate_chiller_stock($bb[$x]['id']);
        //             Chiller::recalculate_chiller($bb[$x]['id']);
        //         }
        //     }
        // } catch (\Throwable $th) {
        //     //throw $th;
        // }

        // $chiller = $chiller->orderBy('item_name', 'ASC')
        //                     ->withCount(['countEvis as countQty' => function($query) {
        //                         $query->select(DB::raw('sum(qty)'))->whereIn('freestock_id', Freestock::select('id')->whereIn('status',[1,2]));
        //                     }])
        //                     ->withCount(['countEvis as countBerat' => function($query) {
        //                         $query->select(DB::raw('sum(berat)'))->whereIn('freestock_id', Freestock::select('id')->whereIn('status',[1,2]));
        //                     }])
        //                     ->get();

        // dd($chiller);

        $chillerDatas    = clone $chiller;
        $arrayDatas     = $chillerDatas->get();
        $datasId        = array();

        foreach ($arrayDatas as $item) {
            $datasId[]  = $item->id;
        }

        $convertDatas   = implode(",", $datasId);

        if($convertDatas){
            $data_alokasi = DB::select("select chiller_out,SUM(bb_item) AS total_qty_alokasi, ROUND(sum(bb_berat),2) AS total_berat_alokasi 
            FROM order_bahan_baku WHERE chiller_out IN(".$convertDatas.") 
            AND `status` IN(1,2) AND deleted_at IS NULL 
            GROUP BY chiller_out");

            $data_ambil_abf   = DB::select("select table_id, sum(qty_awal) as total_qty_abf, round(sum(berat_awal),2) as total_berat_abf
            FROM abf where table_name='chiller' AND table_id IN(".$convertDatas.")
            AND deleted_at IS NULL GROUP BY table_id");
            
            $data_ambil_bb    = DB::select("select chiller_id, sum(qty) as total_qty_freestock, round(sum(berat),2) AS total_berat_freestock, status
            FROM free_stocklist JOIN free_stock ON free_stocklist.freestock_id=free_stock.id 
            WHERE free_stocklist.chiller_id IN(".$convertDatas.") and free_stock.status IN (1,2,3)
            AND free_stock.deleted_at IS NULL AND free_stocklist.deleted_at IS NULL
            GROUP BY chiller_id");

            $data_musnahkan  = DB::select("select item_id, SUM(qty) AS total_qty_musnahkan, ROUND(sum(berat),2) AS total_berat_musnahkan 
            FROM musnahkan_temp JOIN musnahkan on musnahkan.id=musnahkan_temp.musnahkan_id WHERE gudang_id IN (2,4,23,24) AND item_id IN(".$convertDatas.")
            AND musnahkan.deleted_at IS NULL GROUP BY item_id ");
        }

        $dataModifications  = [];
        foreach($arrayDatas as $data){
            $total_qty_alokasi          = 0;
            $total_berat_alokasi        = 0;
            $total_qty_abf              = 0;
            $total_berat_abf            = 0;
            $total_qty_freestock        = 0;
            $total_berat_freestock      = 0;
            $total_qty_musnahkan        = 0;
            $total_berat_musnahkan      = 0; 
            $status_freestock           = 0;

            // data alokasi 
            foreach($data_alokasi as $alokasi){
                if($data->id == $alokasi->chiller_out){
                    $total_qty_alokasi      = intval($alokasi->total_qty_alokasi);
                    $total_berat_alokasi    = floatval($alokasi->total_berat_alokasi) ?? 0;
                }
            }

            // data abf
            foreach($data_ambil_abf as $dataAbf){
                if($data->id == $dataAbf->table_id){
                    $total_qty_abf          = intval($dataAbf->total_qty_abf);
                    $total_berat_abf        = floatval($dataAbf->total_berat_abf) ?? 0;
                }
            }

            // data bahanbaku
            foreach($data_ambil_bb as $dataBb){
                if($data->id == $dataBb->chiller_id){
                    $total_qty_freestock    = intval($dataBb->total_qty_freestock);
                    $total_berat_freestock  = floatval($dataBb->total_berat_freestock) ?? 0;
                    $status_freestock       = $dataBb->status;
                }
            }

            // data musnahkan
            foreach ($data_musnahkan as $musnahkan) {
                if($data->id == $musnahkan->item_id){
                    $total_qty_musnahkan    = intval($musnahkan->total_qty_musnahkan);
                    $total_berat_musnahkan  = floatval($musnahkan->total_berat_musnahkan) ?? 0;
                }
            }

            $dataModifications[] = [
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
                    "qty_item"                  => floatval($data->qty_item),
                    "berat_item"                => floatval($data->berat_item),
                    "tanggal_potong"            => $data->tanggal_potong,
                    "no_mobil"                  => $data->no_mobil,
                    "tanggal_produksi"          => $data->tanggal_produksi,
                    "keranjang"                 => $data->keranjang,
                    "berat_keranjang"           => $data->berat_keranjang,
                    "stock_item"                => $data->stock_item,
                    "stock_berat"               => $data->stock_berat,
                    "status"                    => $data->status,
                    "status_cutoff"             => $data->status_cutoff,
                    "key"                       => $data->key,
                    "created_at"                => $data->created_at ? date('Y-m-d H:i:s', strtotime($data->created_at)) : null,
                    "updated_at"                => $data->updated_at ? date('Y-m-d H:i:s', strtotime($data->updated_at)) : null,
                    "deleted_at"                => $data->deleted_at ? date('Y-m-d H:i:s', strtotime($data->deleted_at)) : null,
                    "nama"                          => $data->nama,
                    'total_qty_alokasi'             => $total_qty_alokasi,
                    'total_berat_alokasi'           => $total_berat_alokasi,
                    'total_qty_abf'                 => $total_qty_abf,
                    'total_berat_abf'               => $total_berat_abf,
                    'total_qty_freestock'           => $total_qty_freestock,
                    'total_berat_freestock'         => $total_berat_freestock,
                    'total_qty_musnahkan'           => $total_qty_musnahkan,
                    'total_berat_musnahkan'         => $total_berat_musnahkan,
                    'status_stock'                  => $status_freestock,
                    'sisaQty'                       => $data->qty_item - $total_qty_alokasi - $total_qty_abf - $total_qty_freestock - $total_qty_musnahkan,
                    'sisaBerat'                     => $data->berat_item - $total_berat_alokasi - $total_berat_abf - $total_berat_freestock - $total_berat_musnahkan
            ];

        }
        $dataChillers   = json_decode(json_encode($dataModifications));

        
        // dd($dataChillers);

        return view('admin.pages.evis.bbevisgabung', compact('chiller', 'request','dataChillers'));
    }

    public function addgabung(Request $request)
    {
        if (User::setIjin(6)) {

            $production             = Production::find($request->row_id);

            $evis                   =   new Evis;
            $evis->production_id    =   $request->row_id;
            $evis->total_item       =   $request->result;
            $evis->berat_item       =   $request->berat;
            $evis->stock_item       =   $request->result;
            $evis->berat_stock      =   $request->berat;
            $evis->item_id          =   $request->part;
            $evis->peruntukan       =   $request->peruntukan;
            $evis->jenis            =   $request->jenis;
            // $evis->keranjang        =   ($request->jumlah_keranjang / 2);
            // $evis->berat_keranjang  =   $request->jumlah_keranjang;
            $evis->tanggal_potong   =   $production->lpah_tanggal_potong;
            $evis->save();

            $proses                 =   Evis::latest()->first();

            for ($i = 0; $i < count($request->item); $i++) {

                $chiller            =   Chiller::find($request->item[$i]);

                $evisproses                     =   new Evisproses;
                $evisproses->item_id            =   $chiller->item_id;
                $evisproses->evis_id            =   $proses->id;
                $evisproses->chiller_id         =   $chiller->id;
                $evisproses->total_item         =   $chiller->qty_item;
                $evisproses->berat_item         =   $chiller->berat_item;
                $evisproses->keranjang          =   $chiller->keranjang;
                $evisproses->berat_keranjang    =   $chiller->berat_keranjang;
                $evisproses->stock_item         =   $chiller->stock_item;
                $evisproses->berat_stock        =   $chiller->stock_berat;
                $evisproses->status             =   1;
                $evisproses->save();

                $chiller->status                =   3;
                $chiller->save();

                try {
                    Chiller::recalculate_chiller($chiller->id);
                } catch (\Throwable $th) {
                }
            }

            $newchiller                     =   new Chiller;
            $newchiller->production_id      =   $proses->production_id;
            $newchiller->table_name         =   'evis';
            $newchiller->table_id           =   $proses->id;
            $newchiller->asal_tujuan        =   $proses->peruntukan;
            $newchiller->item_id            =   $proses->item_id;
            $newchiller->item_name          =   $proses->eviitem->nama;
            $newchiller->qty_item           =   $proses->total_item;
            $newchiller->jenis              =   'masuk';
            $newchiller->type               =   'hasil-produksi';
            $newchiller->regu               =   'byproduct';
            $newchiller->berat_item         =   $proses->berat_item;
            $newchiller->tanggal_produksi   =   $proses->tanggal_potong;
            // $newchiller->keranjang          =   $proses->keranjang;
            // $newchiller->berat_keranjang    =   $proses->berat_keranjang;
            $newchiller->stock_item         =   $proses->stock_item;
            $newchiller->stock_berat        =   $proses->berat_stock;
            $newchiller->status             =   2;
            $newchiller->save();
        }
        return redirect()->route("index");
    }

    public function updategabung(Request $request)
    {
        if (User::setIjin(6)) {
            $evis   =   Evis::where('production_id', NULL)->where('jenis', 'gabungan')->where('status', NULL)->get();

            DB::beginTransaction();

            foreach ($evis as $item) {
                $chiler                     =   new Chiller;
                $chiler->production_id      =   $item->production_id;
                $chiler->table_name         =   'evis';
                $chiler->table_id           =   $item->id;
                $chiler->asal_tujuan        =   'sampingan';
                $chiler->item_id            =   $item->item_id;
                $chiler->item_name          =   $item->eviitem->nama;
                $chiler->regu               =   'byproduct';
                $chiler->qty_item           =   $item->total_item;
                $chiler->berat_item         =   $item->berat_item;
                $chiler->tanggal_produksi   =   $item->tanggal_potong;
                // $chiler->keranjang          =   $item->keranjang;
                // $chiler->berat_keranjang    =   $item->berat_keranjang;
                $chiler->stock_item         =   $item->stock_item;
                $chiler->stock_berat        =   $item->berat_stock;
                $chiler->status             =   2;
                $chiler->jenis              =   'masuk';

                if ($chiler->save()) {
                    $proceed    =   true;
                }

                // Update Evis
                $item->status   = 1;
                if ($item->save()) {
                    $proceed    =   true;
                }
            }

            if ($proceed == true) {
                DB::commit();
                return redirect()->route('evis.index')->with('status', 1)->with('message', 'Proses berhasil diselesaikan');
            } else {
                DB::rollBack();
                return redirect()->route('evis.index')->with('status', 2)->with('message', 'Terjadi kesalahan saat menyelesaikan evis');
            }

            return redirect()->route('evis.index')->with('status', 1)->with('message', 'Proses berhasil diselesaikan');
        }
        return redirect()->route("index");
    }

    public function destroy($id)
    {
        if (User::setIjin(6)) {
            //
        }
        return redirect()->route("index");
    }

    public function laporan(Request $request)
    {
        if (User::setIjin(6)) {
            if ($request->key == 'laporan_produksi') {

                $mulai      =   $request->mulai;
                $selesai    =   $request->selesai;

                header("Content-type: application/csv");
                header("Content-Disposition: attachment; filename=Laporan_Produksi_Evis-" . $mulai . "_" . $selesai . ".csv");
                $fp = fopen('php://output', 'w');
                fputcsv($fp, ["sep=,"]);

                $data = array(
                    "No",
                    "Tanggal",
                    "Nomor LPAH",
                    "Kandang",
                    "Supir",
                    "Nomor Urut",
                    "Item",
                    "SKU",
                    "Qty",
                    "Berat",
                );
                fputcsv($fp, $data);

                $produksi   =   Production::whereBetween('evis_selesai', [$mulai . " 00:00:00", $selesai . " 23:59:59"])
                    ->orderByRaw('evis_selesai ASC, no_urut ASC')
                    ->get();

                foreach ($produksi as $list) {
                    $evis   =   Evis::where('production_id', $list->id)
                        ->get();

                    foreach ($evis as $i => $row) :
                        $data = array(
                            $i + 1,
                            $row->tanggal_potong,
                            $list->no_lpah ?? '###',
                            $list->sc_nama_kandang ?? '###',
                            $list->sc_pengemudi ?? '###',
                            $list->no_urut ?? '###',
                            $row->eviitem->nama,
                            $row->eviitem->sku,
                            str_replace(".", ",", $row->total_item),
                            str_replace(".", ",", $row->berat_item),
                        );
                        fputcsv($fp, $data);
                    endforeach;
                }

                fclose($fp);

                return "";
            } else if ($request->key == 'laporanUmum') {
                $mulai      =   $request->tanggalMulai ?? date("Y-m-01");
                $selesai    =   $request->tanggalSelesai ?? date("Y-m-d");

                $produksi   =   Evis::select(DB::raw("SUM(berat_item) AS total"), 'item_id')
                    ->where(function ($query) use ($mulai, $selesai) {
                        if ($mulai and $selesai) {
                            $query->whereBetween('tanggal_potong', [$mulai, $selesai]);
                        }
                    })
                    ->groupBy('item_id')
                    ->get();

                $peruntukan =   Evis::select(DB::raw("SUM(berat_item) AS total"), 'peruntukan')
                    ->where(function ($query) use ($mulai, $selesai) {
                        if ($mulai and $selesai) {
                            $query->whereBetween('tanggal_potong', [$mulai, $selesai]);
                        }
                    })
                    ->groupBy('peruntukan')
                    ->get();

                $penjualan  =   OrderItem::select('item_id', DB::raw("SUM(qty) AS jumlah"), DB::raw("SUM(berat) AS bobot"))
                    ->whereIn('item_id', Item::select('id')->where('by_product', 1))
                    ->groupBy('item_id')
                    ->get();

                $sampingan  =   Production::select(
                    'prod_tanggal_potong AS tanggal',
                    DB::raw("SUM(productions.ekoran_seckle) AS ekor_lb"),
                    DB::raw("ROUND(SUM(productions.lpah_berat_terima), 2) AS berat_lb"),
                    // DB::raw("ROUND(SUM(evis.berat_item),2) AS berat_ny")
                )
                    // ->leftJoin('evis', 'evis.production_id', '=', 'productions.id')
                    ->where(function ($query) use ($mulai, $selesai) {
                        if ($mulai and $selesai) {
                            $query->whereBetween('productions.prod_tanggal_potong', [$mulai, $selesai]);
                        }
                    })
                    ->groupBy('tanggal')
                    ->orderByRaw('prod_tanggal_potong ASC, no_urut ASC')
                    ->get()
                    ->each
                    ->setAppends([]);

                $period     =   CarbonPeriod::create($mulai, $selesai);

                return view('admin.pages.laporan.evis.laporan-umum', compact('produksi', 'peruntukan', 'penjualan', 'period', 'sampingan', 'mulai', 'selesai'));
            } else if ($request->key == 'laporanPerminggu') {
                $mulai      = $request->tanggalMulai ?? date('Y-m-d');
                $selesai    = $request->tanggalSelesai ?? date("Y-m-d");

                $start                      = new DateTime($mulai);
                $last                       = new DateTime($selesai);
                $last->modify('+1 days');
                $interval                   = DateInterval::createFromDateString ('+1 day') ;
                // $periods                    = new DatePeriod($start, CarbonInterval::day(), $last);
                $periods                    = new DatePeriod($start, $interval, $last);
                $seminggu   = abs(6*86400);
                $awal       = strtotime($mulai);
                $akhir      = strtotime($mulai)+$seminggu;
                $date_awal  = date('Y-m-d',$awal);
                $date_akhir = date('Y-m-d',$akhir);

                $lpah = Production::whereBetween('prod_tanggal_potong',[$mulai,$selesai])
                                    ->select('prod_tanggal_potong',DB::raw('SUM(lpah_berat_terima)AS jml_potong_lpah'), DB::raw('COUNT(sc_no_polisi)AS jml_mobil'))
                                    ->where('grading_status', 1)
                                    ->groupBy('prod_tanggal_potong')
                                    ->get();
                // dd($lpah);
                //baru
                $ati_ampela         = Evis::getArrayProduksi($mulai,$selesai,'',[171],'baru');
                // $hati               = Evis::getArrayProduksi($mulai,$selesai,'',[169,170]);
                // $ampela             = Evis::getArrayProduksi($mulai,$selesai,'',[167,168]);
                $kepala_baru_mgg    = Evis::getArrayProduksi($mulai,$selesai,'',[184],'baru');
                $kaki_baru_mgg      = Evis::getArrayProduksi($mulai,$selesai,'',[181],'baru');
                $usus_baru_mgg      = Evis::getArrayProduksi($mulai,$selesai,'',[179],'baru');
                // $hati_ampela_baru = $ati_ampela+$hati+$ampela;
                // dd($kepala_baru_mgg,''),'';

                $hati_berish        = Evis::getArrayProduksi($mulai,$selesai,'',[172],'');

                // lama
                $ati_ampela_lama    = Evis::getArrayProduksi($mulai,$selesai,'',[171],'lama');
                $kepala_lama        = Evis::getArrayProduksi($mulai,$selesai,'',[184],'lama');
                $kaki_lama          = Evis::getArrayProduksi($mulai,$selesai,'',[181],'lama');
                $usus_lama          = Evis::getArrayProduksi($mulai,$selesai,'',[179],'lama');
                // dd($hati_berish);
                // $ati_ampela_lama    = Evis::getArrayProduksi(date('Y-m-d', strtotime('-2 days', strtotime($mulai))),date('Y-m-d', strtotime('-2 days', strtotime($selesai))),'',[171],'hati_ampela_lama');
                //penjualan
                $sell_ati_ampela    = Evis::getArrayPenjualanItem($mulai,$selesai,'',[171],'sell_ati_ampela');
                // $sell_ati           = Evis::getArrayPenjualanItem($mulai,$selesai,'',[169,170],'sell_ati');
                // $sell_ampela        = Evis::getArrayPenjualanItem($mulai,$selesai,'',[167,168],'sell_ampela');
                $sell_kepala        = Evis::getArrayPenjualanItem($mulai,$selesai,'',[184],'sell_kepala');
                // dd($sell_kepala);
                $sell_kaki          = Evis::getArrayPenjualanItem($mulai,$selesai,'',[181],'sell_kaki');
                $sell_usus          = Evis::getArrayPenjualanItem($mulai,$selesai,'',[179],'sell_usus');

                // stok frozen
                $frz_ati_ampela     = Evis::getStockFrozen($mulai,$selesai,'HATI AMPELA KOTOR BROILER FROZEN','');
                // $frz_hati           = Evis::getStockFrozen($mulai,$selesai,'',[169,170]);
                // $frz_ampela         = Evis::getStockFrozen($mulai,$selesai,'',[167,168]);
                $frz_kepala         = Evis::getStockFrozen($mulai,$selesai,'KEPALA LEHER BROILER FROZEN','');
                $frz_kaki           = Evis::getStockFrozen($mulai,$selesai,'KAKI KOTOR BROILER FROZEN','');

                foreach ($periods as $pp) {
                    $jml_mobil=0;
                    $jml_potong=0;
                    foreach ($lpah as $itemlpah) {
                        if ($pp->format('Y-m-d') == $itemlpah->prod_tanggal_potong) {
                            $jml_mobil = $itemlpah->jml_mobil;
                            $jml_potong = $itemlpah->jml_potong_lpah;
                        }
                    }
                    $prod_atiampela=0;
                    foreach ($ati_ampela as $atiampela) {
                        if ($pp->format('Y-m-d') == $atiampela['tanggal_produksi']) {
                            if($atiampela['kondisi'] == 'baru' || $atiampela['kondisi'] == ''){
                                $prod_atiampela = $atiampela['berat_item'];
                            }
                        }
                    }

                    $prod_kepala_mggu = 0;
                    foreach ($kepala_baru_mgg as $kbmgg) {
                        if ($pp->format('Y-m-d') == $kbmgg['tanggal_produksi']) {
                            if($kbmgg['kondisi'] == 'baru' || $kbmgg['kondisi'] == ''){
                                $prod_kepala_mggu = $kbmgg['berat_item'];
                            }
                        }
                    }
                    $prod_kaki_mggu = 0;
                    foreach ($kaki_baru_mgg as $kakimgg) {
                        if ($pp->format('Y-m-d') == $kakimgg['tanggal_produksi']) {
                            if($kakimgg['kondisi'] == 'baru' || $kakimgg['kondisi'] == ''){
                                $prod_kaki_mggu = $kakimgg['berat_item'];
                            }
                        }
                    }
                    $prod_usus_mggu = 0;
                    foreach ($usus_baru_mgg as $usbmgg) {
                        if ($pp->format('Y-m-d') == $usbmgg['tanggal_produksi']) {
                            if($usbmgg['kondisi'] == 'baru' || $usbmgg['kondisi'] == ''){
                                $prod_usus_mggu = $usbmgg['berat_item'];
                            }
                        }
                    }

                    $total_hati_bersih=0;
                    foreach ($hati_berish as $item_hati_berish) {
                        if ($pp->format('Y-m-d') == $item_hati_berish['tanggal_produksi']) {
                            $total_hati_bersih = $item_hati_berish['berat_item'];
                        }
                    }

                    $total_atiampela_lama=0;
                    foreach ($ati_ampela_lama as $aal) {
                        if ($pp->format('Y-m-d') == $aal['tanggal_produksi']) {
                            if($aal['kondisi'] == 'lama' ){
                                $total_atiampela_lama = $aal['berat_item'];
                            }
                        }
                    }
                    $total_kepala_lama=0;
                    foreach ($kepala_lama as $kpla) {
                        if ($pp->format('Y-m-d') == $kpla['tanggal_produksi']) {
                            if($kpla['kondisi'] == 'lama' ){
                                $total_kepala_lama = $kpla['berat_item'];
                            }
                        }
                    }
                    $total_kaki_lama=0;
                    foreach ($kaki_lama as $kakil) {
                        if ($pp->format('Y-m-d') == $kakil['tanggal_produksi']) {
                            if($kakil['kondisi'] == 'lama' ){
                                $total_kaki_lama = $kakil['berat_item'];
                            }
                        }
                    }

                    $pnj_ati_ampela = 0;
                    foreach($sell_ati_ampela as $saa){
                        if($pp->format('Y-m-d') == $saa->tanggal_kirim){
                            $pnj_ati_ampela    = $saa->sell_ati_ampela;
                        }
                    }
                    $pnj_kepala = 0;
                    foreach($sell_kepala as $skepala){
                        if($pp->format('Y-m-d') == $skepala->tanggal_kirim){
                            $pnj_kepala    = $skepala->sell_kepala;
                        }
                    }
                    $pnj_kaki = 0;
                    foreach($sell_kaki as $skaki){
                        if($pp->format('Y-m-d') == $skaki->tanggal_kirim){
                            $pnj_kaki    = $skaki->sell_kaki;
                        }
                    }
                    $pnj_usus = 0;
                    foreach($sell_usus as $susus){
                        if($pp->format('Y-m-d') == $susus->tanggal_kirim){
                            $pnj_usus    = $susus->sell_usus;
                        }
                    }
                    $stock_frz_atiampela = 0;
                    foreach ($frz_ati_ampela as $atiamp) {
                        if ($pp->format('Y-m-d') == $atiamp->tanggal_so) {
                            $stock_frz_atiampela = $atiamp->berat_frozen;
                        }
                    }
                    $stock_frz_kepala = 0;
                    foreach ($frz_kepala as $kepala) {
                        if ($pp->format('Y-m-d') == $kepala->tanggal_so) {
                            $stock_frz_kepala = $kepala->berat_frozen;
                        }
                    }
                    $stock_frz_kaki = 0;
                    foreach ($frz_kaki as $kaki) {
                        if ($pp->format('Y-m-d') == $kaki->tanggal_so) {
                            $stock_frz_kaki = $kaki->berat_frozen;
                        }
                    }

                    $dataMingguan[]=array(
                        'tanggal'               => $pp->format('Y-m-d'),
                        'jml_mobil'             => $jml_mobil,
                        'jml_potong_lpah'       => $jml_potong,
                        'bb_ati_ampela'         => $prod_atiampela,
                        'bb_prod_kepala'        => $prod_kepala_mggu,
                        'bb_prod_kaki'          => $prod_kaki_mggu,
                        'bb_prod_usus'          => $prod_usus_mggu,
                        'hati_bersih'           => $total_hati_bersih,
                        'ati_ampela_lama'       => $total_atiampela_lama,
                        'kepala_lama'           => $total_kepala_lama,
                        'kaki_lama'             => $total_kaki_lama,
                        'penjualan_atiampela'   => $pnj_ati_ampela,
                        'penjualan_kepala'      => $pnj_kepala,
                        'penjualan_kaki'        => $pnj_kaki,
                        'penjualan_usus'        => $pnj_usus,
                        'frz_ati_ampela'        => $stock_frz_atiampela,
                        'frz_kepala'            => $stock_frz_kepala,
                        'frz_kaki'              => $stock_frz_kaki

                    );
                }



                $dtgl = Carbon::parse($mulai);
                // dd($dtgl->weekOfMonth);
                if ($request->subkey == 'download') {
                    // dd($request->all());
                    $download = true;
                    return view('admin.pages.laporan.evis.laporan-perminggu',compact('awal','akhir','mulai','dataMingguan','periods','download'));
                }else{
                    $download = false;
                    return view('admin.pages.laporan.evis.laporan-perminggu',compact('awal','akhir','mulai','dataMingguan','periods','download'));
                }

            } else if ($request->key == 'laporanPerbandingan') {


                $mulai           =  $request->tanggalMulai ?? date("Y-m-d");
                $selesai         =  $request->tanggalSelesai ?? date("Y-m-d");

                // $ati_ampela      = Evis::getTotalProduksi($mulai,$selesai,[171],['baru'],'hatiampela');
                // $kepala_baru_mgg = Evis::getTotalProduksi($mulai,$selesai,[184],['baru'],'kepala_baru');
                // $kaki_baru_mgg   = Evis::getTotalProduksi($mulai,$selesai,[181],['baru'],'kaki_baru');
                // $usus_baru_mgg   = Evis::getTotalProduksi($mulai,$selesai,[179],['baru'],'usus_baru');
                // dd($ati_ampela, $kepala_baru_mgg, $kaki_baru_mgg, $usus_baru_mgg);
                $period          = CarbonPeriod::create($mulai, $selesai);
                $tanggal         = [];
                foreach ($period as $date) {
                    $tanggal[] = $date->format('Y-m-d');
                }


                $dataEvis       =  [];
                foreach ($tanggal as $tanggalPer) {
                    $dataEvis[$tanggalPer]     =  DB::Select("SELECT
                    SUM(CASE WHEN(orders.keterangan = 'ECERAN' AND proses_ambil = 'sampingan') THEN
                        CASE WHEN (order_bahan_baku.nama like 'HATI AMPELA KOTOR BROILER' and order_bahan_baku.nama not like '%FROZEN%')THEN bb_berat  END
                        ELSE 0 END) as HATIAMPELAECERAN,
                        SUM(CASE WHEN(orders.keterangan = 'ECERAN' AND proses_ambil = 'sampingan') THEN
                        CASE WHEN (order_bahan_baku.nama like 'KEPALA LEHER BROILER' and order_bahan_baku.nama not like '%FROZEN%')THEN bb_berat  END
                        ELSE 0 END) as KEPALAECERAN,
                        SUM(CASE WHEN(orders.keterangan = 'ECERAN' AND proses_ambil = 'sampingan') THEN
                        CASE WHEN (order_bahan_baku.nama like 'USUS BROILER' and order_bahan_baku.nama not like '%FROZEN%')THEN bb_berat  END
                        ELSE 0 END) as USUSECERAN,
                        SUM(CASE WHEN(orders.keterangan = 'ECERAN' AND proses_ambil = 'sampingan') THEN
                        CASE WHEN (order_bahan_baku.nama like 'KAKI KOTOR BROILER' and order_bahan_baku.nama not like '%FROZEN%')THEN bb_berat  END
                        ELSE 0 END) as KAKIECERAN,

                        SUM(CASE WHEN(sales_channel = 'By Product - Paket' AND proses_ambil = 'sampingan') THEN
                        CASE WHEN (order_bahan_baku.nama like 'HATI AMPELA KOTOR BROILER')THEN bb_berat  END
                        ELSE 0 END) as HATIAMPELAPAKET,
                        SUM(CASE WHEN(sales_channel = 'By Product - Paket' AND proses_ambil = 'sampingan') THEN
                        CASE WHEN (order_bahan_baku.nama like 'KEPALA LEHER BROILER' and order_bahan_baku.nama not like '%FROZEN%')THEN bb_berat  END
                        ELSE 0 END) as KEPALAPAKET,
                        SUM(CASE WHEN(sales_channel = 'By Product - Paket' AND proses_ambil = 'sampingan') THEN
                        CASE WHEN (order_bahan_baku.nama like 'USUS BROILER' and order_bahan_baku.nama not like '%FROZEN%')THEN bb_berat  END
                        ELSE 0 END) as USUSPAKET,
                        SUM(CASE WHEN(sales_channel = 'By Product - Paket' AND proses_ambil = 'sampingan') THEN
                        CASE WHEN (order_bahan_baku.nama like 'KAKI KOTOR BROILER' and order_bahan_baku.nama not like '%FROZEN%')THEN bb_berat  END
                        ELSE 0 END) as KAKIPAKET,

                        SUM(CASE WHEN((orders.keterangan != 'ECERAN' or orders.keterangan IS NULL) and orders.sales_channel = 'By Product - Retail') THEN
                        CASE WHEN (order_bahan_baku.nama like 'HATI AMPELA KOTOR BROILER' and order_bahan_baku.nama not like '%FROZEN%')THEN bb_berat  END
                        ELSE 0 END) as HATIAMPELAKIRIMAN,
                        SUM(CASE WHEN((orders.keterangan != 'ECERAN' or orders.keterangan IS NULL) and orders.sales_channel = 'By Product - Retail') THEN
                        CASE WHEN (order_bahan_baku.nama like 'KEPALA LEHER BROILER' and order_bahan_baku.nama not like '%FROZEN%')THEN bb_berat  END
                        ELSE 0 END) as KEPALAKIRIMAN,
                        SUM(CASE WHEN((orders.keterangan != 'ECERAN' or orders.keterangan IS NULL) and orders.sales_channel = 'By Product - Retail') THEN
                        CASE WHEN (order_bahan_baku.nama like 'USUS BROILER' and order_bahan_baku.nama not like '%FROZEN%')THEN bb_berat  END
                        ELSE 0 END) as USUSKIRIMAN,
                        SUM(CASE WHEN((orders.keterangan != 'ECERAN' or orders.keterangan IS NULL) and orders.sales_channel = 'By Product - Retail') THEN
                        CASE WHEN (order_bahan_baku.nama like 'KAKI KOTOR BROILER' and order_bahan_baku.nama not like '%FROZEN%')THEN bb_berat  END
                        ELSE 0 END) as KAKIKIRIMAN,


                        SUM(CASE WHEN (order_bahan_baku.nama like 'HATI AMPELA BERSIH%' )THEN bb_berat ELSE 0 END) as KUPAS,
--
                        SUM(CASE WHEN (order_bahan_baku.nama like 'HATI AMPELA KOTOR BROILER FROZEN' AND proses_ambil = 'frozen' )THEN bb_berat
                        ELSE 0 END) as HATIAMPELAFROZEN,
                        SUM(CASE WHEN (order_bahan_baku.nama like 'KEPALA LEHER BROILER FROZEN' AND proses_ambil = 'frozen' )THEN bb_berat
                        ELSE 0 END) as KEPALAFROZEN,
                        SUM(CASE WHEN (order_bahan_baku.nama like 'KAKI KOTOR BROILER FROZEN' AND proses_ambil = 'frozen' )THEN bb_berat
                        ELSE 0 END) as KAKIFROZEN

                        FROM
                        order_bahan_baku
                        join orders ON order_bahan_baku.order_id = orders.id
                        where orders.tanggal_so = '".$tanggalPer."' and order_bahan_baku.deleted_at is null");
                    }
                // dd($dataEvis);

                if ($request->subkey == 'download') {
                    // dd($request->all());
                    $download = true;
                    return view('admin.pages.laporan.evis.laporan-perbandingan', compact('dataEvis', 'download'));
                } else {
                    $download = false;
                    return view('admin.pages.laporan.evis.laporan-perbandingan', compact('dataEvis', 'download'));
                }



            } else if ($request->key == 'laporanPersentase') {
                $mulai                      =   $request->tanggalMulai ?? date("Y-m-d");
                $selesai                    =   $request->tanggalSelesai ?? date("Y-m-d");

                $start                      = new DateTime($mulai);
                $last                       = new DateTime($selesai);
                $last->modify('+1 day');
                $interval                   = DateInterval::createFromDateString ('+1 day') ;
                $periods                    = new DatePeriod($start, $interval, $last) ;

                //baru
                $ati_ampela_baru            = Evis::getArrayProduksi($mulai,$selesai,'',[171],'baru');
                $total_ati_ampela_baru      = 0;
                foreach($ati_ampela_baru as $ttatiampelabaru){
                    if($ttatiampelabaru['kondisi'] == 'baru' || $ttatiampelabaru['kondisi'] == ''){
                        $total_ati_ampela_baru += $ttatiampelabaru['berat_item'];
                    }
                }

                $kepala_baru                = Evis::getArrayProduksi($mulai,$selesai,'',[184],'baru');
                $total_kepala_baru          = 0;
                foreach($kepala_baru as $kpBaru){
                    if($kpBaru['kondisi'] == 'baru' || $kpBaru['kondisi'] == ''){
                        $total_kepala_baru += $kpBaru['berat_item'];
                    }
                }

                $kaki_baru                  = Evis::getArrayProduksi($mulai,$selesai,'',[181],'baru');
                $total_kaki_baru            = 0;
                foreach($kaki_baru as $kakikBaru){
                    if($kakikBaru['kondisi'] == 'baru' || $kakikBaru['kondisi'] == ''){
                        $total_kaki_baru += $kakikBaru['berat_item'];
                    }
                }

                $usus_baru                  = Evis::getArrayProduksi($mulai,$selesai,'',[179],'baru');
                $total_usus_baru            = 0;
                foreach($usus_baru as $UsusUsusBaru){
                    if($UsusUsusBaru['kondisi'] == 'baru' || $UsusUsusBaru['kondisi'] == ''){
                        $total_usus_baru += $UsusUsusBaru['berat_item'];
                    }
                }

                //lama
                $ati_ampela_lama            = Evis::getArrayProduksi($mulai,$selesai,'',[171],'lama');
                $total_atiampela_lama       = 0;
                foreach($ati_ampela_lama as $ttatiampelalama){
                    if($ttatiampelalama['kondisi'] == 'lama'){
                        $total_atiampela_lama += $ttatiampelalama['berat_item'];
                    }
                }

                $kepala_lama                = Evis::getArrayProduksi($mulai,$selesai,'',[184],'lama');
                $total_kepala_lama          = 0;
                foreach($kepala_lama as $keplalama){
                    if($keplalama['kondisi'] == 'lama'){
                        $total_kepala_lama += $keplalama['berat_item'];
                    }
                }

                $kaki_lama                  = Evis::getArrayProduksi($mulai,$selesai,'',[181],'lama');
                $total_kaki_lama            = 0;
                foreach($kaki_lama as $kakiklama){
                    if($kakiklama['kondisi'] == 'lama'){
                        $total_kaki_lama += $kakiklama['berat_item'];
                    }
                }
                $usus_lama                  = Evis::getArrayProduksi($mulai,$selesai,'',[179],'lama');
                $total_usus_lama            = 0;
                foreach($usus_lama as $UsusUsusLama){
                    if($UsusUsusLama['kondisi'] == 'lama'){
                        $total_usus_lama += $UsusUsusLama['berat_item'];
                    }
                }

                //paket eceran
                $ecer_retail_atiampela      = Evis::EcerPaket($mulai,$selesai,'',[171]);
                $ecer_retail_kepala         = Evis::EcerPaket($mulai,$selesai,'',[184]);
                $ecer_retail_kaki           = Evis::EcerPaket($mulai,$selesai,'',[181]);
                $ecer_retail_usus           = Evis::EcerPaket($mulai,$selesai,'',[179]);

                // stok frozen
                $frz_ati_ampela             = Evis::getStockFrozen($mulai,$selesai,'HATI AMPELA KOTOR BROILER FROZEN','')->sum('berat_frozen');
                $frz_kepala                 = Evis::getStockFrozen($mulai,$selesai,'KEPALA LEHER BROILER FROZEN','')->sum('berat_frozen');
                $frz_kaki                   = Evis::getStockFrozen($mulai,$selesai,'KAKI KOTOR BROILER FROZEN','')->sum('berat_frozen');
                $frz_usus                   = Evis::getStockFrozen($mulai,$selesai,'USUS BROILER FROZEN','')->sum('berat_frozen');

                // //order bahan baku untuk sisa
                $stock_chiller_atiampela    = Evis::StockChiller($mulai,$selesai,'',[171]);
                $stock_chiller_kepala       = Evis::StockChiller($mulai,$selesai,'',[184]);
                $stock_chiller_kaki         = Evis::StockChiller($mulai,$selesai,'',[181]);
                $stock_chiller_usus         = Evis::StockChiller($mulai,$selesai,'',[179]);

                // stok frozen
                $stock_warehouse_ati_ampela = Evis::StockWarehouse($mulai,$selesai,'HATI AMPELA KOTOR BROILER FROZEN','')->sum('berat');
                $stock_warehouse_kepala     = Evis::StockWarehouse($mulai,$selesai,'KEPALA LEHER BROILER FROZEN','')->sum('berat');
                $stock_warehouse_kaki       = Evis::StockWarehouse($mulai,$selesai,'KAKI KOTOR BROILER FROZEN','')->sum('berat');
                $stock_warehouse_usus       = Evis::StockWarehouse($mulai,$selesai,'USUS BROILER FROZEN','')->sum('berat');

                $dataAtas[] = array(
                    'ati_ampela_baru'           => $total_ati_ampela_baru,
                    'kepala_baru'               => $total_kepala_baru,
                    'kaki_baru'                 => $total_kaki_baru,
                    'usus_baru'                 => $total_usus_baru,
                    'ati_ampela_lama'           => $total_atiampela_lama + $frz_ati_ampela,
                    'kepala_lama'               => $total_kepala_lama + $frz_kepala,
                    'kaki_lama'                 => $total_kaki_lama + $frz_kaki,
                    'usus_lama'                 => $total_usus_lama + $frz_usus,
                    'total_produksi_ati_ampela' => $total_ati_ampela_baru + $total_atiampela_lama + $frz_ati_ampela,
                    'total_produksi_kepala'     => $total_kepala_baru + $total_kepala_lama + $frz_kepala,
                    'total_produksi_kaki'       => $total_kaki_baru + $total_kaki_lama + $frz_kaki,
                    'total_produksi_usus'       => $total_usus_baru + $total_usus_lama + $frz_usus,
                    'ecer_retail_atiampela'     => $ecer_retail_atiampela,
                    'ecer_retail_kepala'        => $ecer_retail_kepala,
                    'ecer_retail_kaki'          => $ecer_retail_kaki,
                    'ecer_retail_usus'          => $ecer_retail_usus,
                    'stock_frz_ampela'          => $frz_ati_ampela,
                    'stock_frz_kepala'          => $frz_kepala,
                    'stock_frz_kaki'            => $frz_kaki,
                    'stock_frz_usus'            => $frz_usus,
                    'sisa_ati_ampela'           => ($total_ati_ampela_baru + $total_atiampela_lama + $frz_ati_ampela) - $ecer_retail_atiampela - $frz_ati_ampela,
                    'sisa_kepala'               => ($total_kepala_baru + $total_kepala_lama + $frz_kepala) - $ecer_retail_kepala - $frz_kepala,
                    'sisa_kaki'                 => ($total_kaki_baru + $total_kaki_lama + $frz_kaki) - $ecer_retail_kaki - $frz_kaki,
                    'sisa_usus'                 => ($total_usus_baru + $total_usus_lama + $frz_usus) - $ecer_retail_usus - $frz_usus,
                    'stock_chiller_atiampela'   => $stock_chiller_atiampela + $stock_warehouse_ati_ampela,
                    'stock_chiller_kepala'      => $stock_chiller_kepala + $stock_warehouse_kepala,
                    'stock_chiller_kaki'        => $stock_chiller_kaki +$stock_warehouse_kaki,
                    'stock_chiller_usus'        => $stock_chiller_usus + $stock_warehouse_usus
                );

                $ati_ampela_dayly           = Evis::getArrayProduksi($mulai,$selesai,'',[171],'baru');
                $kepala                     = Evis::getArrayProduksi($mulai,$selesai,'',[184],'baru');
                $kaki                       = Evis::getArrayProduksi($mulai,$selesai,'',[181],'baru');
                $usus                       = Evis::getArrayProduksi($mulai,$selesai,'',[179],'baru');

                $hatiampelabersih           = Evis::getArrayProduksi($mulai,$selesai,'',[172],'baru');

                $sell_ati_ampela            = Evis::getArrayPenjualanItem($mulai,$selesai,'',[171],'sell_ati_ampela');
                $sell_kepala                = Evis::getArrayPenjualanItem($mulai,$selesai,'',[184],'sell_kepala');
                $sell_kaki                  = Evis::getArrayPenjualanItem($mulai,$selesai,'',[181],'sell_kaki');
                $sell_usus                  = Evis::getArrayPenjualanItem($mulai,$selesai,'',[179],'sell_usus');

                foreach($periods as $dt){
                    //PRODUKSI HARIAN
                    $prod_ati_ampela        = 0;
                    foreach($ati_ampela_dayly as $aa){
                        if($dt->format('Y-m-d') == $aa['tanggal_produksi']){
                            $prod_ati_ampela    = $aa['berat_item'];
                        }
                    }
                    $prod_kepala = 0;
                    foreach($kepala as $ndas){
                        if($dt->format('Y-m-d') == $ndas['tanggal_produksi']){
                            $prod_kepala        = $ndas['berat_item'];
                        }
                    }
                    $prod_kaki = 0;
                    foreach($kaki as $sikil){
                        if($dt->format('Y-m-d') == $sikil['tanggal_produksi']){
                            $prod_kaki          = $sikil['berat_item'];
                        }
                    }
                    $prod_usus = 0;
                    foreach($usus as $uu){
                        if($dt->format('Y-m-d') == $uu['tanggal_produksi']){
                            $prod_usus          = $uu['berat_item'];
                        }
                    }
                    //PRODUKSI HARIAN HATI BERSIH
                    $prod_hatiampelabersih = 0;
                    foreach($hatiampelabersih as $hb){
                        if($dt->format('Y-m-d') == $hb['tanggal_produksi']){
                            $prod_hatiampelabersih    = $hb['berat_item'];
                        }
                    }

                    //PENJUALAN HARIAN
                    $jual_ati_ampela = 0;
                    foreach($sell_ati_ampela as $saa){
                        if($dt->format('Y-m-d') == $saa->tanggal_kirim){
                            $jual_ati_ampela    = $saa->sell_ati_ampela;
                        }
                    }
                    $jual_kepala    = 0;
                    foreach($sell_kepala as $sk){
                        if($dt->format('Y-m-d') == $sk->tanggal_kirim){
                            $jual_kepala            = $sk->sell_kepala;
                        }
                    }
                    $jual_kaki      = 0;
                    foreach($sell_kaki as $skk){
                        if($dt->format('Y-m-d') == $skk->tanggal_kirim){
                            $jual_kaki              = $skk->sell_kaki;
                        }
                    }
                    $jual_usus      = 0;
                    foreach($sell_usus as $sus){
                        if($dt->format('Y-m-d') == $sus->tanggal_kirim){
                            $jual_usus              = $sus->sell_usus;
                        }
                    }
                    $dataPersentase[] = array(
                        'tanggal'           => $dt->format('Y-m-d'),
                        'bb_ati_ampela'     => $prod_ati_ampela,
                        'bb_kepala'         => $prod_kepala,
                        'bb_kaki'           => $prod_kaki,
                        'bb_usus'           => $prod_usus,
                        'hatibersih'        => $prod_hatiampelabersih,
                        'sell_ati_ampela'   => $jual_ati_ampela,
                        'sell_kepala'       => $jual_kepala,
                        'sell_kaki'         => $jual_kaki,
                        'sell_usus'         => $jual_usus,
                        'stok_ati_ampela'   => $prod_ati_ampela - $prod_hatiampelabersih - $jual_ati_ampela,
                        'stok_kepala'       => $prod_kepala - $jual_kepala,
                        'stok_kaki'         => $prod_kaki - $jual_kaki,
                        'stok_usus'         => $prod_usus - $jual_usus,
                    );
                }
                if ($request->subkey == 'download') {
                    $download = true;
                    return view('admin.pages.laporan.evis.laporan-persentase',compact('mulai','selesai', 'dataAtas','dataPersentase', 'download'));
                } else {
                    $download = false;
                    return view('admin.pages.laporan.evis.laporan-persentase',compact('mulai','selesai', 'dataAtas','dataPersentase', 'download'));
                }
            }
            else if($request->key == 'laporanPenjualan'){
                $mulai      = $request->tanggalMulai ?? date('Y-m-d');
                $akhir    = $request->tanggalSelesai ?? date("Y-m-d");


                $start                      = new DateTime($mulai);
                $last                       = new DateTime($akhir);
                $last->modify('+1 days');
                $interval                   = DateInterval::createFromDateString ('+1 day') ;
                $periodst                   = new DatePeriod($start, $interval, $last);
                // dd($periodst);
                $data        = [];

                for ($no=0; $no<5; $no++) {
                    $tanggal_awal       = date('Y-m-d', strtotime("-6 Day", strtotime($akhir)));

                    $awal               = $tanggal_awal;

                    $lpah               = Production::whereBetween('prod_tanggal_potong',[$awal,$akhir])
                                            ->select('prod_tanggal_potong',DB::raw('SUM(lpah_berat_terima)AS jml_potong_lpah'), DB::raw('COUNT(sc_no_polisi)AS jml_mobil'))
                                            ->where('grading_status', 1)
                                            ->first();

                    //start hasil produksi
                    $hp_ati_ampela      = Evis::getArrayProduksi($awal,$akhir,'',[171],'baru');
                    $prod_atiampela     = 0;
                    foreach ($hp_ati_ampela as $row) {
                        $prod_atiampela += $row['berat_item'];
                    }

                    $hp_kepala          = Evis::getArrayProduksi($awal,$akhir,'',[184],'baru');
                    $hp_kepala_total    = 0;
                    foreach ($hp_kepala as $row) {
                        $hp_kepala_total += $row['berat_item'];
                    }

                    $hp_kaki            = Evis::getArrayProduksi($awal,$akhir,'',[181],'baru');
                    $hp_kaki_total      = 0;
                    foreach ($hp_kaki as $row) {
                        $hp_kaki_total += $row['berat_item'];
                    }

                    $hp_usus            = Evis::getArrayProduksi($awal,$akhir,'',[179],'baru');
                    $hp_usus_total      = 0;
                    foreach ($hp_usus as $row) {
                        $hp_usus_total += $row['berat_item'];
                    }

                    $hp_hati_berish     = Evis::getArrayProduksi($awal,$akhir,'',[172],'');
                    $hati_berish_total  = 0;
                    foreach ($hp_hati_berish as $row) {
                        $hati_berish_total += $row['berat_item'];
                    }
                    //end hasil produksi

                    //start penjualan
                    $penj_ati_ampela    = Evis::getArrayPenjualanItem($awal,$akhir,'',[171],'penj_ati_ampela');
                    $total_penj_ati_ampela = 0;
                    foreach ($penj_ati_ampela as $row) {
                        $total_penj_ati_ampela += $row->penj_ati_ampela;
                    }

                    $penj_kepala        = Evis::getArrayPenjualanItem($awal,$akhir,'',[184],'penj_kepala');
                    $penj_kepala_total  = 0;
                    foreach ($penj_kepala as $row) {
                        $penj_kepala_total += $row->penj_kepala;
                    }

                    $penj_kaki          = Evis::getArrayPenjualanItem($awal,$akhir,'',[181],'penj_kaki');
                    $penj_kaki_total    = 0;
                    foreach ($penj_kaki as $row) {
                        $penj_kaki_total += $row->penj_kaki;
                    }

                    $penj_usus          = Evis::getArrayPenjualanItem($awal,$akhir,'',[179],'penj_usus');
                    $penj_usus_total    = 0;
                    foreach ($penj_usus as $row) {
                        $penj_usus_total += $row->penj_usus;
                    }
                    //end penjualan

                    $summary = array(
                        'tanggal'           => date('d M Y', strtotime($awal)) . "-" . date('d M Y', strtotime($akhir)),
                        'jml_mobil'         => $lpah->jml_mobil,
                        'jml_potong'        => $lpah->jml_potong_lpah,
                        'hp_ati_ampela'     => $prod_atiampela,
                        'hp_kepala'         => $hp_kepala_total,
                        'hp_kaki'           => $hp_kaki_total,
                        'hp_usus'           => $hp_usus_total,
                        'hp_hati_berish'    => $hati_berish_total,
                        'penj_ati_ampela'   => $total_penj_ati_ampela,
                        'penj_kepala'       => $penj_kepala_total,
                        'penj_kaki'         => $penj_kaki_total,
                        'penj_usus'         => $penj_usus_total,
                        'sisa_atiampela'    => $prod_atiampela - $total_penj_ati_ampela - $hati_berish_total,
                        'sisa_kepala'       => $hp_kepala_total - $penj_kepala_total,
                        'sisa_kaki'         => $hp_kaki_total - $penj_kaki_total,

                    );

                    $data[] = $summary;
                    $akhir  = date('Y-m-d', strtotime("-1 Day", strtotime($tanggal_awal)));
                }

                $date_range = $data;
                // dd($date_range);

                //kebutuhan benchmark data (LPAH)
                $bm_lpah           = Production::whereBetween('prod_tanggal_potong',[$request->tanggalMulai,$request->tanggalSelesai])
                                    ->select('prod_tanggal_potong',DB::raw('SUM(lpah_berat_terima)AS jml_potong'))
                                    ->get();
                $bm_ati_ampela  = Evis::getArrayProduksi($request->tanggalMulai,$request->tanggalSelesai,'',[171],'baru');
                $bm_kepala      = Evis::getArrayProduksi($request->tanggalMulai,$request->tanggalSelesai,'',[184],'baru');
                $bm_kaki        = Evis::getArrayProduksi($request->tanggalMulai,$request->tanggalSelesai,'',[181],'baru');
                $bm_usus        = Evis::getArrayProduksi($request->tanggalMulai,$request->tanggalSelesai,'',[179],'baru');

                $total_lpah_potong=0;
                foreach ($bm_lpah as $row) {
                        $total_lpah_potong = $row->jml_potong;
                }

                $hp_total_atiampela=0;
                foreach ($bm_ati_ampela as $atiampela) {
                        $hp_total_atiampela += $atiampela['berat_item'];
                }

                $hp_total_kepala=0;
                foreach ($bm_kepala as $kepala) {
                        $hp_total_kepala += $kepala['berat_item'];
                }

                $hp_total_kaki=0;
                foreach ($bm_kaki as $kaki) {
                        $hp_total_kaki += $kaki['berat_item'];
                }

                $hp_total_usus=0;
                foreach ($bm_usus as $usus) {
                        $hp_total_usus += $usus['berat_item'];

                }

                //kebutuhan bawah
                $benchmak = array_slice($date_range,1);
                $benchmak_hp_atiampela  =0;
                $benchmak_hp_kepala     =0;
                $benchmak_hp_kaki       =0;
                $benchmak_hp_usus       =0;
                $benchmak_lpah_total    =0;
                foreach ($benchmak as $i => $row) {
                    $benchmak_hp_atiampela += $row['hp_ati_ampela'];
                    $benchmak_hp_kepala    += $row['hp_kepala'];
                    $benchmak_hp_kaki      += $row['hp_kaki'];
                    $benchmak_hp_usus      += $row['hp_usus'];
                    $benchmak_lpah_total   += $row['jml_potong'];
                }

                $data_benchmark[] = array(
                    'tgl_awal'              => $request->tanggalMulai,
                    'tgl_akhir'             => $request->tanggalSelesai,
                    'total_potong_lpah'     => $total_lpah_potong,
                    'hp_total_atiampela'    => $hp_total_atiampela,
                    'hp_total_kepala'       => $hp_total_kepala,
                    'hp_total_kaki'         => $hp_total_kaki,
                    'hp_total_usus'         => $hp_total_usus,
                    'persen_atiampela'      => ($hp_total_atiampela / $total_lpah_potong) * 100,
                    'persen_kepala'         => ($hp_total_kepala / $total_lpah_potong) * 100,
                    'persen_kaki'           => ($hp_total_kaki / $total_lpah_potong) * 100,
                    'persen_usus'           => ($hp_total_usus / $total_lpah_potong )* 100,
                    'bm_bawah_atiampela'    => ($benchmak_hp_atiampela/$benchmak_lpah_total) *100,
                    'bm_bawah_kepala'       => ($benchmak_hp_kepala/$benchmak_lpah_total)*100,
                    'bm_bawah_kaki'         => ($benchmak_hp_kaki/$benchmak_lpah_total)*100,
                    'bm_bawah_usus'         => ($benchmak_hp_usus/$benchmak_lpah_total)*100,
                    'persenan_bawah_ati'    => (($hp_total_atiampela / $total_lpah_potong) - ($benchmak_hp_atiampela/$benchmak_lpah_total)) * 100,
                    'persenan_bawah_kepala' => (($hp_total_kepala / $total_lpah_potong) - ($benchmak_hp_kepala/$benchmak_lpah_total)) * 100,
                    'persenan_bawah_kaki'   => (($hp_total_kaki / $total_lpah_potong) - ($benchmak_hp_kaki/$benchmak_lpah_total)) * 100,
                    'persenan_bawah_usus'   => (($hp_total_usus / $total_lpah_potong ) - ($benchmak_hp_usus/$benchmak_lpah_total)) * 100

                );

                //end benchmark

                if ($request->subkey == 'download') {
                    $download = true;
                    return view('admin.pages.laporan.evis.laporan-penjualan',compact('mulai','akhir','download','date_range','data_benchmark'));
                } else {
                    $download = false;
                    return view('admin.pages.laporan.evis.laporan-penjualan',compact('mulai','akhir','download','date_range','data_benchmark'));
                }

            } else {
                $mulai      =   $request->mulai ?? date("Y-m-01");
                $selesai    =   $request->selesai ?? date("Y-m-d");
                $period     =   CarbonPeriod::create($mulai, $selesai);

                return view('admin.pages.laporan.evis.laporan-evis', compact('mulai', 'selesai'));
            }
        }
        return redirect()->route("index");
    }

    public function result($id)
    {
        if (User::setIjin(5)) {
            $data   =   Production::where('id', $id)
                ->whereIn('evis_status', [1, 2])
                ->first();

            $summary    =   Evis::where('production_id', $id)->orderBy('id', 'DESC')->get();

            $count      =   0;
            $sumberat   =   0;
            $sumekor    =   0;
            foreach ($summary as $row) {
                $count      +=  1;
                $sumberat   +=  $row->berat_item;
                $sumekor    +=  $row->total_item;
            }

            $total  =   [
                'jumlah'    =>  $count,
                'berat'     =>  $sumberat,
                'ekor'      =>  $sumekor
            ];

            return view('admin.pages.grading.result', compact('data', 'total'));
        }
        return redirect()->route("index");
    }


    public function peruntukan(Request $request)
    {
        if (User::setIjin(6)) {

            if ($request->key == 'selesai') {
                $bahanbaku      =   Freestock::where('regu', 'byproduct')
                    ->whereDate('tanggal', date('Y-m-d'))
                    ->where('status', 1)
                    ->first();

                return view('admin.pages.evis.selesaikan_peruntukan', compact('bahanbaku'));
            } else {

                if ($request->produksi) {
                    $data           =   Freestock::find($request->produksi);

                    if ($data) {
                        if ($request->view == 'data_produksi') {
                            return view('admin.pages.evis.detail_produksi', compact('data'));
                        } else {
                            $item   =   Item::whereIn('category_id', [4, 6])
                                ->get();

                            $bom    =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL') . ' - EVIS')->first();

                            return view('admin.pages.evis.detail', compact('data', 'item', 'bom'));
                        }
                    }
                    return redirect()->route('evis.peruntukan');
                } else {
                    $tanggal        = $request->tanggal ?? date('Y-m-d');

                    $item           =   Item::where(function($q){
                                                $q->whereIn('category_id', [4, 6]);
                                                $q->orwhere('access','LIKE','%evis%');
                                            })
                                            ->get();

                    $chiller        =   Chiller::whereIn('status', [1, 2])
                        ->where('jenis', 'masuk')
                        ->where('table_name', 'evis')
                        ->where('type', 'bahan-baku')
                        ->where('stock_item', '>', 0)
                        ->orderBy('item_id', 'ASC')
                        ->get();

                    $summary        =   Evis::where('jenis', 'gabungan')
                        ->orderBy('id', 'DESC')
                        ->get();

                    $count          =   0;
                    $sumberat       =   0;
                    $sumekor        =   0;

                    $freestock      =   Freestock::where('regu', 'byproduct')
                        ->where('tanggal', date('Y-m-d'))
                        ->where('status', 1)
                        ->first();

                    $evisselesai    =   Freestock::where('regu', 'byproduct')
                        ->whereDate('tanggal', date('Y-m-d'))
                        ->where('status', 2)
                        ->get();

                    $customer   =   Customer::all();

                    $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL') . ' - EVIS')->first();

                    foreach ($summary as $row) {
                        $count      +=  1;
                        $sumberat   +=  $row->berat_stock;
                        $sumekor    +=  $row->total_item;
                    }

                    $total  =   [
                        'jumlah'    =>  $count,
                        'berat'     =>  $sumberat,
                        'ekor'      =>  $sumekor
                    ];

                    return view('admin.pages.evis.peruntukan', compact('item', 'summary', 'total', 'chiller', 'freestock', 'evisselesai', 'tanggal', 'bom', 'customer'));
                }
            }
        }
        return redirect()->route("index");
    }

    public function bbperuntukan()
    {
        $bahanbaku      =   Freestock::where('regu', 'byproduct')
            ->where('tanggal', date('Y-m-d'))
            ->where('status', 1)
            ->first();

        return view('admin.pages.evis.bb', compact('bahanbaku'));
    }

    public function hasil_peruntukan(Request $request)
    {
        if (User::setIjin(6)) {

            $tanggal = $request->tanggal ?? date('Y-m-d');

            $evisselesai    =   Freestock::where('regu', 'byproduct')
                ->whereDate('tanggal', $tanggal)
                ->whereIn('status', [2, 3])
                ->get();

            $progress       =   Freestock::where('regu', 'byproduct')
                ->where('status', 1)
                ->whereDate('tanggal', $tanggal)
                ->count();
            $regu           = 'byproduct';
            return view('admin.pages.evis.timbang_peruntukan.hasil_peruntukan', compact('evisselesai', 'progress', 'tanggal', 'regu'));
        }
        return redirect()->route("index");
    }

    public function updateperuntukan(Request $request)
    {
        if ($request->key == 'bahan_baku') {
            // dd($request->all());
            $freestocklist  =   FreestockList::find($request->x_code);
            // dd($freestocklist);
            if ($freestocklist) {
                DB::beginTransaction();

                $sisaQty    = Chiller::ambilsisachiller($freestocklist->chiller_id,'qty_item','qty','bb_item',$request->x_code);
                $sisaBerat  = Chiller::ambilsisachiller($freestocklist->chiller_id,'berat_item','berat','bb_berat',$request->x_code);
                $convertSisaBerat   = number_format((float)$sisaBerat, 2, '.', '');

                // if ($request->qty != NULL) {
                //     if ($request->qty > $sisaQty) {
                //         DB::rollBack();
                //         return back()->with('status', 2)->with('message', 'Qty melebihi batas maksimum!');
                //     }
                // }

                if ($request->berat != NULL) {
                    if ($request->berat > $convertSisaBerat) {
                        DB::rollBack();
                        return back()->with('status', 2)->with('message', 'Berat melebihi batas maksimum!');
                    }
                }


                if ($freestocklist->outchiller) {
                    $outchiller                     =   Chiller::find($freestocklist->outchiller);
                    $origin = [
                        'freestock_id'              =>  $freestocklist->freestock_id,
                        'chiller_id'                =>  $freestocklist->chiller_id,
                        'item_id'                   =>   $outchiller->item_id,
                        'qty'                       =>   $outchiller->qty_item,
                        'berat'                     =>   $outchiller->berat_item,
                    ];
                    $originitem                     =   $outchiller->item_id;
                    $originqty                      =   $outchiller->qty_item;
                    $originweight                   =   $outchiller->berat_item;
                    // $outchiller->qty_item           =   ($outchiller->qty_item - $freestocklist->qty) + $request->qty;
                    // $outchiller->berat_item         =   ($outchiller->berat_item - $freestocklist->berat) + $request->berat;
                    $outchiller->stock_item         =   ($outchiller->stock_item - $freestocklist->qty) + $request->qty;
                    $outchiller->stock_berat        =   ($outchiller->stock_berat - $freestocklist->berat) + $request->berat;
                    if (!$outchiller->save()) {
                        DB::rollBack();
                        return back()->with('status', 2)->with('message', 'Proses gagal');
                    }

                    $getchiller                     =   Chiller::find($freestocklist->chiller_id);
                    // $getchiller->qty_item           =   ($getchiller->qty_item + $freestocklist->qty) - $request->qty ;
                    // $getchiller->berat_item         =   ($getchiller->berat_item + $freestocklist->berat) - $request->berat ;
                    // $getchiller->qty_item           =   (($getchiller->qty_item + ($freestocklist->qty - $request->qty)) - ($freestocklist->qty - $request->qty));
                    // $getchiller->berat_item         =   (($getchiller->berat_item + ($freestocklist->berat - $request->berat)) - ($freestocklist->berat - $request->berat));
                    $getchiller->stock_item         =   ($getchiller->stock_item + $freestocklist->qty) - $request->qty;
                    $getchiller->stock_berat        =   ($getchiller->stock_berat + $freestocklist->berat) - $request->berat;
                    if (!$getchiller->save()) {
                        DB::rollBack();
                        return back()->with('status', 2)->with('message', 'Proses gagal');
                    }
                }

                $freestocklist->qty     =   $request->qty;
                $freestocklist->sisa    =   $request->qty;
                $freestocklist->berat   =   $request->berat;

                $updated = [
                    'freestock_id'      =>  $freestocklist->freestock_id,
                    'chiller_id'        =>  $freestocklist->chiller_id,
                    'item_id'          =>   $freestocklist->item_id,
                    'qty'              =>   $freestocklist->qty,
                    'berat'            =>   $freestocklist->berat,
                ];

                if (!$freestocklist->save()) {
                    DB::rollBack();
                    return back()->with('status', 2)->with('message', 'Proses gagal');
                }

                // $ceklog = Adminedit::where('table_id', $freestocklist->id)->where('table_name', 'chiller')->where('type', 'edit')->count();
                $freestock_status =  $freestocklist->free_stock->status;
                if ($freestock_status != "2") {
                    $log                        =   new Adminedit;
                    $log->user_id               =   Auth::user()->id;
                    $log->table_name            =   'chiller';
                    $log->table_id              =   $freestocklist->id;
                    $log->type                  =   'edit';
                    $log->activity              =   'evis';
                    $log->content               =   'Edit Pengambilan Bahan Baku';
                    $log->data                  =   json_encode([
                        'before_update'     => $origin,
                        'after_update'      => $updated
                    ]);
                    if (!$log->save()) {
                        DB::rollBack();
                        return back()->with('status', 2)->with('message', 'Proses simpan log gagal');
                    }
                }

                DB::commit();

                // try {
                //     Chiller::recalculate_chiller($outchiller->id);
                // } catch (\Throwable $th) {
                // }


                // try {
                //     Chiller::recalculate_chiller($getchiller->id);
                // } catch (\Throwable $th) {
                // }

                // try {
                //     Chiller::recalculate_chiller($freestocklist->chiller_id);
                // } catch (\Throwable $th) {
                // }

                return back()->with('status', 1)->with('message', 'Ubah ambil bahan baku berahsil');
            }
            return back()->with('status', 2)->with('message', 'Proses gagal');
        } else

        if ($request->key == 'hasil_produksi') {
            $freestocktemp  =   FreestockTemp::find($request->x_code);
            if ($freestocktemp) {
                DB::beginTransaction();

                if (Freestock::find($freestocktemp->freestock_id)->status == 3) {
                    $chiller                =   Chiller::where('table_name', 'free_stocktemp')
                        ->where('table_id', $freestocktemp->id)
                        ->first();

                    if ($chiller) {
                        $item_name              = Item::find($request->item);
                        if ($item_name) {
                            $chiller->item_id   =   $request->item;
                            $chiller->item_name =   $item_name->nama;
                        }
                        $chiller->qty_item      =   ($chiller->qty_item - $freestocktemp->qty) + $request->qty;
                        $chiller->berat_item    =   ($chiller->berat_item - $freestocktemp->berat) + $request->berat;
                        $chiller->stock_item    =   ($chiller->stock_item - $freestocktemp->qty) + $request->qty;
                        $chiller->stock_berat   =   ($chiller->stock_berat - $freestocktemp->berat) + $request->berat;
                        $chiller->kategori      = $request->kategori;

                        if (!$chiller->save()) {
                            DB::rollBack();
                            return back()->with('status', 2)->with('message', 'Proses gagal');
                        }
                    }
                }

                $freestocktemp->item_id     =   $request->item;
                $freestocktemp->qty         =   $request->qty;
                $freestocktemp->berat       =   $request->berat;
                $freestocktemp->kategori      = $request->kategori;
                if (!$freestocktemp->save()) {
                    DB::rollBack();
                    return back()->with('status', 2)->with('message', 'Proses gagal');
                }

                DB::commit();

                try {
                    Chiller::recalculate_chiller($chiller->id);
                } catch (\Throwable $th) {
                }

                return back()->with('status', 1)->with('message', 'Ubah hasil produksi berahsil');
            }
            return back()->with('status', 2)->with('message', 'Proses gagal');
        }
    }

    public function delete(Request $request)
    {
        $stocklist  =   FreestockList::find($request->x_code);
        $freestock  =   $stocklist->freestock_id;

        if ($stocklist->free_stock->status == 3) {
            $chiller                =   Chiller::find($stocklist->chiller_id);
            $chiller->stock_item    =   $chiller->stock_item + $stocklist->qty;
            $chiller->stock_berat   =   $chiller->stock_berat + $stocklist->berat;


            $chiller->save();

            try {
                Chiller::recalculate_chiller($chiller->id);
            } catch (\Throwable $th) {
            }
        }

        $stocklist->delete();

        if (FreestockList::where('freestock_id', $freestock)->count() < 1) {
            Freestock::find($freestock)->delete();
            $result['freestock_id'] =   NULL;
            return $result;
        }

        $result['freestock_id'] =   $freestock;
        return $result;
    }

    public function deleteitem(Request $request, $id)
    {
        // return response()->json(Evis::recalculate($request->x_code));

        // return response()->json($request->all());
        $data   =   Evis::find($request->x_code);
        $prod   =   Production::find($id);

        if ($prod->evis_status == 1) {
            Evis::recalculate($request->x_code);
        }

        $edit                       =   new Adminedit;
        $edit->user_id              =   Auth::user()->id;
        $edit->table_name           =   'evis';
        $edit->table_id             =   $data->id;
        $edit->activity             =   'checker';
        $edit->content              =   'HAPUS ITEM ' . $data->eviitem->nama;
        $edit->data                 =   json_encode($data);
        $edit->type                 =   'hapus';
        $edit->key                  =   $prod->id;
        $edit->status               =   1;
        $edit->save();

        return $data->delete();
    }

    public function put(Request $request)
    {
        FreestockTemp::find($request->x_code)->delete();

        return back();
    }

    public function simpanbahanbaku(Request $request)
    {
        if (User::setIjin(6)) {

            DB::beginTransaction();

            $freestock  =   Freestock::find($request->free_stock) ?? Freestock::where('regu', 'byproduct')
                ->whereDate('tanggal', date('Y-m-d'))
                ->where('status', 1)
                ->first();
            $x_code     =   json_decode(json_encode($request->x_code, FALSE));
            $x_item_id  =   json_decode(json_encode($request->x_item_id, FALSE));
            $qty        =   json_decode(json_encode($request->qty, FALSE));
            $berat      =   json_decode(json_encode($request->berat, FALSE));

            if (!$freestock) {
                $freestock              =   new Freestock;
                $freestock->nomor       =   Freestock::get_nomor();
                $freestock->tanggal     =   Carbon::now();
                $freestock->user_id     =   Auth::user()->id;
                $freestock->regu        =   'byproduct';
                $freestock->status      =   1;
                if (!$freestock->save()) {
                    DB::rollBack();
                    return redirect()->to(url()->previous())->with('status', 2)->with('message', 'Buat free stock gagal');
                }
            }
            $available_type = '';
            foreach ($freestock->listfreestock as $row) :
                if ($row->chiller->type ?? '') {
                    $available_type = $row->chiller->type ?? '';
                }
            endforeach;

            for ($x = 0; $x < COUNT($x_item_id); $x++) {
                if ($berat[$x] > 0 ) {

                    if($berat[$x] == 0 || $berat[$x] == "" ){
                        DB::rollBack();
                        $data['status'] =   400;
                        $data['msg']    =   'Berat tidak boleh kosong';
                        return $data;
                    }
        
                    // if($qty[$x] == 0 || $qty[$x] == "" ){
                    //     DB::rollBack();
                    //     $data['status'] =   400;
                    //     $data['msg']    =   'Qty tidak boleh kosong';
                    //     return $data;
                    // }
                    
                    // Pengecekan Type di table Chiller berdasarkan ID
                    $chiller                = Chiller::find($request->x_code[$x]);
                    $sisaBeratChiller       = Chiller::ambilsisachiller($chiller->id,'berat_item','berat','bb_berat');
                    $convertSisaBerat       = number_format((float)$sisaBeratChiller, 2, '.', '');
                    if($berat[$x] > $convertSisaBerat){
                        DB::rollBack();
                        $data['status'] =   400;
                        $data['msg']    =   'Bahan baku kurang, silakan refresh untuk lihat stock yang ada';
                        return $data;
                    }

                    if ($available_type != '') {
                        if ($chiller->type != $available_type) {
                            $result['status']   =   400;
                            $result['msg']      =   "Proses gagal";
                            return $result;
                        } else {
                            $listfree                   =   new FreestockList;
                            $listfree->chiller_id       =   $x_code[$x];
                            $listfree->freestock_id     =   $freestock->id;
                            $listfree->item_id          =   $x_item_id[$x];
                            $listfree->qty              =   $qty[$x];
                            $listfree->regu             =   'byproduct';
                            $listfree->sisa             =   $berat[$x];
                            $listfree->berat            =   $berat[$x];
                            if ($chiller->asal_tujuan == "evisgabungan") {
                                if ($chiller->tanggal_produksi >= date('Y-m-d', strtotime($freestock->tanggal))) {
                                    $listfree->bb_kondisi       =   "baru";
                                } else {
                                    $listfree->bb_kondisi       =   "lama";
                                }
                            } else {
                                $listfree->bb_kondisi           =   $chiller->asal_tujuan;
                            }
                            if (!$listfree->save()) {
                                DB::rollBack();
                                return back()->with('status', 2)->with('message', 'Proses gagal');
                            }

                            if ($freestock->status == 3) {
                                $outchiller                     =   new Chiller;
                                $outchiller->table_name         =   'free_stocklist';
                                $outchiller->table_id           =   $listfree->id;
                                $outchiller->asal_tujuan        =   'free_stock';
                                $outchiller->item_id            =   $listfree->item_id;
                                $outchiller->item_name          =   $listfree->item->nama;
                                $outchiller->jenis              =   'keluar';
                                $outchiller->type               =   'pengambilan-bahan-baku';
                                $outchiller->regu               =   $freestock->regu;
                                $outchiller->no_mobil           =   $listfree->chiller->no_mobil;
                                $outchiller->qty_item           =   $listfree->qty;
                                $outchiller->berat_item         =   $listfree->berat;
                                $outchiller->stock_item         =   $listfree->qty;
                                $outchiller->stock_berat        =   $listfree->berat;
                                $outchiller->tanggal_potong     =   $freestock->tanggal;
                                $outchiller->tanggal_produksi   =   $freestock->tanggal;
                                $outchiller->status             =   4;
                                if (!$outchiller->save()) {
                                    DB::rollBack();
                                    return back()->with('status', 2)->with('message', 'Proses gagal');
                                }



                                $listfree->outchiller           =   $outchiller->id;
                                if (!$listfree->save()) {
                                    DB::rollBack();
                                    return back()->with('status', 2)->with('message', 'Proses gagal');
                                }

                                $chill                          =   Chiller::find($listfree->chiller_id);
                                $chill->stock_berat             =   $chill->stock_berat - $listfree->berat;
                                $chill->stock_item              =   $chill->stock_item - $listfree->qty;
                                if (!$chill->save()) {
                                    DB::rollBack();
                                    return back()->with('status', 2)->with('message', 'Proses gagal');
                                }
                            }
                        }
                    }
                    if ($available_type == '') {
                        $listfree                   =   new FreestockList;
                        $listfree->chiller_id       =   $x_code[$x];
                        $listfree->freestock_id     =   $freestock->id;
                        $listfree->item_id          =   $x_item_id[$x];
                        $listfree->qty              =   $qty[$x];
                        $listfree->regu             =   'byproduct';
                        $listfree->sisa             =   $berat[$x];
                        $listfree->berat            =   $berat[$x];
                        if ($chiller->asal_tujuan == "evisgabungan") {
                            if ($chiller->tanggal_produksi >= date('Y-m-d', strtotime($freestock->tanggal))) {
                                $listfree->bb_kondisi       =   "baru";
                            } else {
                                $listfree->bb_kondisi       =   "lama";
                            }
                        } else {
                            $listfree->bb_kondisi           =   $chiller->asal_tujuan;
                        }
                        if (!$listfree->save()) {
                            DB::rollBack();
                            return back()->with('status', 2)->with('message', 'Proses gagal');
                        }

                        if ($freestock->status == 3) {
                            $outchiller                     =   new Chiller;
                            $outchiller->table_name         =   'free_stocklist';
                            $outchiller->table_id           =   $listfree->id;
                            $outchiller->asal_tujuan        =   'free_stock';
                            $outchiller->item_id            =   $listfree->item_id;
                            $outchiller->item_name          =   $listfree->item->nama;
                            $outchiller->jenis              =   'keluar';
                            $outchiller->type               =   'pengambilan-bahan-baku';
                            $outchiller->regu               =   $freestock->regu;
                            $outchiller->no_mobil           =   $listfree->chiller->no_mobil;
                            $outchiller->qty_item           =   $listfree->qty;
                            $outchiller->berat_item         =   $listfree->berat;
                            $outchiller->stock_item         =   $listfree->qty;
                            $outchiller->stock_berat        =   $listfree->berat;
                            $outchiller->tanggal_potong     =   $freestock->tanggal;
                            $outchiller->tanggal_produksi   =   $freestock->tanggal;
                            $outchiller->status             =   4;
                            if (!$outchiller->save()) {
                                DB::rollBack();
                                return back()->with('status', 2)->with('message', 'Proses gagal');
                            }

                            $listfree->outchiller           =   $outchiller->id;
                            if (!$listfree->save()) {
                                DB::rollBack();
                                return back()->with('status', 2)->with('message', 'Proses gagal');
                            }

                            $chill                          =   Chiller::find($listfree->chiller_id);
                            $chill->stock_berat             =   $chill->stock_berat - $listfree->berat;
                            $chill->stock_item              =   $chill->stock_item - $listfree->qty;

                            if (!$chill->save()) {
                                DB::rollBack();
                                return back()->with('status', 2)->with('message', 'Proses gagal');
                            }
                        }
                    }
                } 
                
            }

            DB::commit();

            try {
                Chiller::recalculate_chiller($chill->id);
            } catch (\Throwable $th) {
            }

            $return['freestock_id'] =   $freestock->id;
            return $return;
        }
        return redirect()->route("index");
    }

    public function hasilproduksi(Request $request)
    {
        $tanggal = $request->tanggal ?? date('Y-m-d');
        $bahanbaku      =   Freestock::whereDate('tanggal', $tanggal)
            ->where('status', 1)
            ->where('regu', 'byproduct')
            ->get();


        return view('admin.pages.evis.hasilproduksi', compact('bahanbaku'));
    }

    public function evisfreestockstore(Request $request)
    {
        // dd($request->all());
        if (User::setIjin(6)) {
            // $validator  =    Validator::make($request->all(), [
            //     'item'      =>  ['required', Rule::exists('items', 'id')->whereIn('category_id', [4, 6, 2])],
            // ]);

            // if ($validator->fails()) {
            //     $data['status'] =   400;
            //     $data['msg']    =   'Data tidak lengkap';
            //     return $data;
            // }
            $freestock  =   Freestock::where('regu', 'byproduct')
                ->where(function ($query) use ($request) {
                    if ($request->freestock_id) {
                        $query->where('id', $request->freestock_id);
                    } else {
                        $query->whereDate('tanggal', date('Y-m-d'))->where('status', 1);
                    }
                })
                ->first();

            if ($freestock->id) {

                if ($request->plastik != 'Curah') {
                    $plastik =   Item::find($request->plastik);
                }

                $label      =   [
                    'plastik'       =>  [
                        'sku'       =>  $plastik->sku ?? NULL,
                        'jenis'     =>  $plastik->nama ?? NULL,
                        'qty'       =>  $request->qtyplastik ?? NULL
                    ]
                ];

                $item = Item::find($request->item);

                $freestock              =   Freestock::find($request->freestock_id);
                $temp                   =   new FreestockTemp();
                $temp->freestock_id     =   $freestock->id;
                $temp->item_id          =   $request->item;
                $temp->prod_nama        =   $item->nama ?? NULL;
                $temp->plastik_sku      =   $plastik->sku ?? NULL;
                $temp->plastik_nama     =   $plastik->nama ?? NULL;
                $temp->plastik_qty      =   $request->qtyplastik ?? NULL;
                $temp->qty              =   $request->qty;
                $temp->berat            =   $request->berat;
                $temp->tanggal_produksi =   $freestock->tanggal;
                $temp->label            =   json_encode($label);
                $temp->regu             =   'byproduct';
                $temp->kategori         =   $request->tujuan_produksi;
                $temp->customer_id      =   $request->customer;
                $temp->keterangan       =   $request->keterangan ?? NULL;
                $temp->save();

                if (Freestock::find($freestock->id)->status == 3) {
                    if ($request->act == 'tambahan') {
                        $chiller                    =   new Chiller;
                        $chiller->table_name        =   'free_stocktemp';
                        $chiller->table_id          =   $temp->id;
                        $chiller->asal_tujuan       =   'free_stock';
                        $chiller->item_id           =   $temp->item_id;
                        $chiller->item_name         =   $temp->item->nama;
                        $chiller->jenis             =   'masuk';
                        $chiller->type              =   'hasil-produksi';
                        $chiller->regu              =   'byproduct';
                        $chiller->label             =   $temp->label;
                        $chiller->berat_item        =   $temp->berat;
                        $chiller->qty_item          =   $temp->qty;
                        $chiller->stock_berat       =   $chiller->berat_item;
                        $chiller->stock_item        =   $chiller->qty_item;
                        $chiller->tanggal_produksi  =   $temp->tanggal_produksi;
                        $chiller->kategori          =   $temp->kategori;
                        $chiller->status            =   2;
                        $chiller->save();
                    }
                }
            } else {
                $data['status'] =   400;
                $data['msg']    =   'Data tidak lengkap';
                return $data;
            }
        }

        return redirect()->route("index");
    }

    public function peruntukanselesai(Request $request)
    {
        if (User::setIjin(6)) {
            $freestock  =   Freestock::find($request->freestock_id);

            if (!$freestock) {
                $data['status'] =   400;
                $data['msg']    =   'Data tidak ditemukan';
                return $data;
            }

            DB::beginTransaction();

            if ($freestock->status == 1) {

                if (env('NET_SUBSIDIARY') == 'CGL') {
                    if (FreestockTemp::where('freestock_id', $freestock->id)->count() < 1) {
                        DB::rollBack();
                        $data['status'] =   400;
                        $data['msg']    =   'Hasil produksi belum ada';
                        return $data;
                    }
                }

                $freestock->status      =   2;
                // if ($request->netsuite_send != "") {
                //     if ($request->netsuite_send == "FALSE") {
                //         $freestock->netsuite_send      =   NULL;
                //     } else {
                //         $freestock->netsuite_send      =   0;
                //     }
                // }

                if (!$freestock->save()) {
                    DB::rollBack();
                    $data['status'] =   400;
                    $data['msg']    =   'Gagal menyimpan proses timbang gabungan';
                    return $data;
                }

                DB::commit();
            } else {

                if ($request->key == 'edit') {
                    $freestock->status      =   1;

                    if (!$freestock->save()) {
                        DB::rollBack();
                        $data['status'] =   400;
                        $data['msg']    =   'Gagal menyimpan proses timbang gabungan';
                        return $data;
                    }

                    DB::commit();
                } else {
                    $list       =   FreestockList::where('freestock_id', $freestock->id)->get();

                    foreach ($list as $row) {
                        // Insert ke chiller untuk mengambil bahan baku
                        $outchiller                     =   new Chiller;
                        $outchiller->table_name         =   'free_stocklist';
                        $outchiller->table_id           =   $row->id;
                        $outchiller->asal_tujuan        =   'free_stock';
                        $outchiller->item_id            =   $row->item_id;
                        $outchiller->item_name          =   $row->item->nama;
                        $outchiller->jenis              =   'keluar';
                        $outchiller->type               =   'pengambilan-bahan-baku';
                        $outchiller->regu               =   'byproduct';
                        $outchiller->no_mobil           =   $row->chiller->no_mobil ?? "";
                        $outchiller->qty_item           =   $row->qty;
                        $outchiller->stock_item         =   $row->qty;
                        $outchiller->berat_item         =   $row->berat;
                        $outchiller->stock_berat        =   $row->berat;
                        $outchiller->tanggal_potong     =   $freestock->tanggal;
                        $outchiller->tanggal_produksi   =   $freestock->tanggal;
                        $outchiller->status             =   4;
                        if (!$outchiller->save()) {
                            DB::rollBack();
                            $data['status'] =   400;
                            $data['msg']    =   'Gagal menyelesaikan proses timbang gabungan';
                            return $data;
                        }

                        // Memperbaharui field outchiller di tabel freestocklist
                        $row->outchiller                =   $outchiller->id;
                        if (!$row->save()) {
                            DB::rollBack();
                            $data['status'] =   400;
                            $data['msg']    =   'Gagal menyelesaikan proses timbang gabungan';
                            return $data;
                        }

                        // Memperbaharui data chiller yang itemnya diambil untuk dikurangi stocknya
                        $chiller                        =   Chiller::find($row->chiller_id);
                        if ($chiller) {

                            $chiller->stock_berat           =   $chiller->stock_berat - $outchiller->berat_item;
                            $chiller->stock_item            =   $chiller->stock_item - $outchiller->qty_item;

                            // $chiller->berat_item            =   $chiller->berat_item - $outchiller->berat_item ;
                            // $chiller->qty_item              =   $chiller->qty_item - $outchiller->qty_item ;
                            if (!$chiller->save()) {
                                DB::rollBack();
                                $data['status'] =   400;
                                $data['msg']    =   'Gagal menyelesaikan proses timbang gabungan';
                                return $data;
                            }
                        }
                    }

                    $temp       =   FreestockTemp::where('freestock_id', $freestock->id)->get();

                    foreach ($temp as $row) {
                        // Insert ke chiller sebagai hasil produksi dari data freestocktemp
                        $chiler                     =   new Chiller;
                        $chiler->table_name         =   'free_stocktemp';
                        $chiler->table_id           =   $row->id;
                        $chiler->asal_tujuan        =   'free_stock';
                        $chiler->item_id            =   $row->item_id;
                        $chiler->item_name          =   $row->item->nama;
                        $chiler->jenis              =   'masuk';
                        $chiler->type               =   'hasil-produksi';
                        $chiler->regu               =   'byproduct';
                        $chiler->label              =   $row->label;
                        $chiler->stock_berat        =   $row->berat;
                        $chiler->berat_item         =   $row->berat;
                        $chiler->stock_item         =   $row->qty;
                        $chiler->qty_item           =   $row->qty;
                        $chiler->plastik_nama       =   $row->plastik_nama ;
                        $chiler->plastik_sku        =   $row->plastik_sku ?? NULL;
                        $chiler->plastik_qty        =   $row->plastik_qty ;
                        $chiler->tanggal_potong     =   $freestock->tanggal;
                        $chiler->tanggal_produksi   =   $freestock->tanggal;
                        $chiler->kategori           =   $row->kategori;
                        $chiler->status             =   2;



                        if (!$chiler->save()) {
                            DB::rollBack();
                            $data['status'] =   400;
                            $data['msg']    =   'Gagal menyelesaikan proses timbang gabungan';
                            return $data;
                        }
                    }

                    // Memperbaharui/mengakhiri header free_stock dengan merubah status menjadi 2
                    $freestock->status      =   3;

                    if (!$freestock->save()) {
                        DB::rollBack();
                        $data['status'] =   400;
                        $data['msg']    =   'Gagal menyelesaikan proses timbang gabungan';
                        return $data;
                    }

                    DB::commit();

                    // try {
                    //     Chiller::recalculate_chiller($chiller->id);
                    // } catch (\Throwable $th) {
                    // }

                    // WO 2 khusus untuk CGL
                    // if(env('NET_SUBSIDIARY', 'EBA')=='CGL'){
                    //     Netsuite::wo_2($freestock->id, Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL').' - EVIS')->first()->netsuite_internal_id) ;
                    // }
                }
            }
        }
        return redirect()->route("index");
    }

    public function salesOrder(Request $request)
    {

        $tanggal        =   $request->tanggal ?? date('Y-m-d');
        $search         =   $request->search;
        $kategori       =   4;
        $pending        =   Order::where(function ($query) {
            $query->orWhere('status', "<", 6);
            $query->orWhere('status', NULL);
        })
            ->whereIn('id', OrderItem::select('order_id')
                ->whereIn('item_id', Item::select('id')
                    ->where('category_id', $kategori)))
            ->whereDate('tanggal_kirim', $tanggal);


        //Search
        if ($search != "") {
            $pending = $pending->where(function ($query) use ($search) {
                $query->where('nama', 'like', '%' . $search . '%');
                $query->orWhere('no_so', 'like', '%' . $search . '%');
                $query->orWhere('keterangan', 'like', '%' . $search . '%');
                $query->orWhere('sales_channel', 'like', '%' . $search . '%');
            });
        }

        $pending    =   $pending->paginate(10);

        return view('admin.pages.evis.orderevis', compact('pending', 'tanggal', 'kategori', 'search'));
    }

    public function editevis(Request $request)
    {
        $evis   =   Evis::find($request->idedit);
        $item   =   Item::find($evis->item_id);
        if ($request->key == 'checker') {
            $old    =   Evis::find($request->idedit);
        }

        $chiller                =   Chiller::where('asal_tujuan', 'evisgabungan')->where('item_id', $evis->item_id)->where('tanggal_potong', $evis->tanggal_potong)->first();
        if ($chiller) {
            $chiller->qty_item      =   ($chiller->qty_item ? ($chiller->qty_item - $evis->total_item) : 0) + $request->qty;
            $chiller->berat_item    =   ($chiller->berat_item ? ($chiller->berat_item - $evis->berat_item) : 0) + $request->berat;
            $chiller->stock_item    =   ($chiller->stock_item ? ($chiller->stock_item - $evis->total_item) : 0) + $request->qty;
            $chiller->stock_berat   =   ($chiller->stock_berat ? ($chiller->stock_berat - $evis->berat_item) : 0) + $request->berat;
            $chiller->save();
        } else {
            $chillerInsert                      =   new Chiller;
            $chillerInsert->asal_tujuan         =   'evisgabungan';
            $chillerInsert->type                =   'bahan-baku';
            $chillerInsert->regu                =   'byproduct';
            $chillerInsert->item_id             =   $evis->item_id;
            $chillerInsert->item_name           =   $item->nama;
            $chillerInsert->tanggal_potong      =   $evis->tanggal_potong;
            $chillerInsert->tanggal_produksi    =   $evis->tanggal_potong;
            // $chillerInsert->keranjang           =   $evis->keranjang;
            // $chillerInsert->berat_keranjang     =   $evis->berat_keranjang;
            $chillerInsert->qty_item            =   $evis->total_item;
            $chillerInsert->stock_item          =   $evis->total_item;
            $chillerInsert->berat_item          =   $evis->berat_item;
            $chillerInsert->stock_berat         =   $evis->berat_item;
            $chillerInsert->status              =   2;
            $chillerInsert->jenis               =   'masuk';
            // if (!$chillerInsert->save()) {
            //     return back()->with('status', 2)->with('message', 'Proses gagal');
            // }
            $chillerInsert->save();
        }

        $evis->total_item   =   $request->qty;
        $evis->berat_item   =   $request->berat;
        $evis->stock_item   =   $request->qty;
        $evis->berat_stock  =   $request->berat;

        $evis->save();

        if ($request->key == 'checker') {
            $json   =   [
                'item_lama' =>  $old,
                'item_baru' =>  $evis
            ];

            $edit                       =   new Adminedit;
            $edit->user_id              =   Auth::user()->id;
            $edit->table_name           =   'evis';
            $edit->table_id             =   $evis->id;
            $edit->activity             =   'checker';
            $edit->content              =   'EDIT ITEM ' . $evis->eviitem->nama;
            $edit->data                 =   json_encode($json);
            $edit->type                 =   'edit';
            $edit->key                  =   $evis->production_id;
            $edit->status               =   1;
            $edit->save();
        }

        try {
            Chiller::recalculate_chiller($chiller->id);
        } catch (\Throwable $th) {
        }

        return back()->with('status', 1)->with('message', 'Berhasil Edit Data');
    }

    public function inputorder(Request $request)
    {
        return view('admin.pages.evis.by_order.index');
    }
}
