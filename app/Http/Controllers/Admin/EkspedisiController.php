<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppKey;
use App\Models\Bahanbaku;
use App\Models\DataOption;
use App\Models\Driver;
use App\Models\Ekspedisi;
use App\Models\Ekspedisi_rute;
use App\Models\MarketingSO;
use App\Models\Netsuite;
use App\Models\Nopolisi;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemLog;
use App\Models\User;
use App\Models\Wilayah;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;

class EkspedisiController extends Controller
{
    public function index(Request $request)
    {
        if ($request->key == 'pdf') {
            $data   =   Ekspedisi::where('id', $request->id)
                        ->where('status', '!=', 1)
                        ->first() ;

            if ($data) {
                $pdf    = PDF::loadView('admin.pages.ekspedisi.riwayat.pdf', compact('data'));

                return $pdf->stream('pdf');
                // return view('admin.pages.ekspedisi.riwayat.pdf', compact('data'));
            }
            return back()->with('status', 2)->with('message', 'Data tidak ditemukan') ;
        } else

        if ($request->key == 'unduh') {
            $ekspedisi   =   Ekspedisi::where('id', $request->id)
                        ->where('status', '!=', 1)
                        ->first() ;

            if ($ekspedisi) {

                $no_so = [];
                foreach($ekspedisi->ekspedisirute as $r){
                    $no_so[] = $r->no_so;
                }

                $data   =   OrderItem::select('order_items.*', 'orders.nama', 'orders.no_so', 'orders.tanggal_kirim', 'orders.sales_id',
                            DB::raw("orders.nama as cust_nama"),
                            DB::raw("orders.created_at as created_at_order"),
                            DB::raw("order_items.edited as edit_item"),
                            DB::raw("order_items.deleted_at as delete_at_item"),
                            DB::raw("order_items.edited as edit_item"),
                            DB::raw("order_items.id as id"),
                            DB::raw("orders.status_so as order_status_so"),
                            DB::raw("marketing.nama_alias as marketing_nama"),
                            DB::raw("order_items.deleted_at as delete_at_item"))
                ->leftJoin('orders', 'orders.id', '=', 'order_items.order_id')
                ->leftJoin('items', 'items.id', '=', 'order_items.item_id')
                ->leftJoin('marketing', 'marketing.netsuite_internal_id', '=', 'orders.sales_id')
                ->whereIn('orders.no_so', $no_so)
                ->get() ;

                // return $data;
                return view('admin.pages.ekspedisi.riwayat.rute', compact('data', 'ekspedisi'));
            }
            return back()->with('status', 2)->with('message', 'Data tidak ditemukan') ;
        } else

        if ($request->key == 'show_rute') {
            $rute   =   Ekspedisi_rute::where(function($query) use($request) {
                            if ($request->id) {
                                $query->where('ekspedisi_id', $request->id) ;
                            } else {
                                $query->whereIn('ekspedisi_id', Ekspedisi::select('id')->where('status', 1)) ;
                            }
                        })
                        ->get() ;

            return view('admin.pages.ekspedisi.show_rute', compact('request', 'rute'));
        } else

        if ($request->key == 'sales_order') {
            // $order  =   Order::whereDate('orders.tanggal_kirim', $request->tanggal_kirim ?? date("Y-m-d", strtotime('+1 days', time())))
                        
            //             ->where(function($query) {
            //                 if (env('NET_SUBSIDIARY', 'EBA') == 'EBA') {
            //                     $query->whereNotIn('no_so', Ekspedisi_rute::select('no_so')) ;
            //                 }
            //                 $query->whereNotIn('orders.sales_channel', ["By Product - Paket", "By Product - Retail"]);
            //             })
            //             ->where(function($query) use ($request) {
            //                 if ($request->cari) {
            //                     $query->orWhere('nama', 'like', '%' . $request->cari . '%') ;
            //                     $query->orWhere('no_so', 'like', '%' . $request->cari . '%') ;
            //                 }
            //             })
            //             ->leftJoin('marketing_so','marketing_so.no_so', '=', 'orders.no_so')
            //             ->where('marketing_so.status', '!=', '0')
            //             ->paginate(10);
                        // ->with('ordercustomer')
                        // dd($order);
            
            
            
            $order  =   MarketingSO::select('marketing_so.*', 'customers.nama AS namaCustomer')->where('marketing_so.tanggal_kirim', $request->tanggal_kirim ?? date("Y-m-d", strtotime('+1 days', time())))
                        ->join('customers', 'customers.id', '=', 'marketing_so.customer_id')

                        ->where(function($query) {
                            if (env('NET_SUBSIDIARY', 'EBA') == 'EBA') {
                                $query->whereNotIn('no_so', Ekspedisi_rute::select('no_so')) ;
                            }
                        })

                        ->where(function($query) use ($request) {
                            if ($request->cari) {
                                $query->orWhere('customers.nama', 'like', '%' . $request->cari . '%') ;
                                $query->orWhere('marketing_so.no_so', 'like', '%' . $request->cari . '%') ;
                            }
                        })

                        ->where('no_so', '!=', NULL)
                        ->whereIn('marketing_so.status', [1, 3])
                        ->paginate(10);

            // dd($order);
            $driver =   Driver::where('driver_kirim', 1)->orderBy('nama', 'ASC')->get();


            return view('admin.pages.ekspedisi.sales_order', compact('order', 'request','driver'));
        } else
        if($request->key == 'renderbypass'){
            $data   =   Ekspedisi::whereHas('ekspedisirute', function ($query) {
                            $query->where('ekspedisi_rute.status', 1);
                        })
                        ->where('tanggal', $request->tanggal_kirim ?? date("Y-m-d"))
                        ->where('nama', NULL)
                        ->where('no_polisi', NULL)
                        ->orderByRaw('tanggal ASC, no_urut ASC')
                        ->get();
                        // dd($data);

            return view('admin.pages.ekspedisi.renderbypass', compact('data'));

        } else {
            $wilayah    =   Wilayah::orderBy('nama', 'ASC')->get();
            $driver     =   Driver::where('driver_kirim', 1)->orderBy('nama', 'ASC')->get();
            $nopol      =   Nopolisi::pluck('nama', 'id');

            $today = Carbon::today();
            $nextday=[];
            for ($i=0; $i < 7; $i++) {
                $nextday[]=$today->format('Y-m-d');
                $today->addDay();
            }

            return view('admin.pages.ekspedisi.index', compact('wilayah', 'driver', 'nopol','nextday'));
        }
    }


    public function show(Request $request, $id)
    {
        $ekspedisi  =   Ekspedisi::find($id) ;
        if ($ekspedisi) {
            $wilayah    =   Wilayah::orderBy('nama', 'ASC')->get();
            $driver     =   Driver::where('driver_kirim', 1)->orderBy('nama', 'ASC')->get();
            $nopol      =   Nopolisi::pluck('nama', 'id');
            return view('admin.pages.ekspedisi.show.index', compact('ekspedisi', 'wilayah', 'driver', 'nopol'));
        }
        return redirect()->route('ekspedisi.index') ;
    }


    public function riwayat(Request $request)
    {
        $data           =   Ekspedisi::whereBetween('tanggal', [$request->tanggal_awal ?? date("Y-m-d"), $request->tanggal_akhir ?? date("Y-m-d", strtotime('+1 day'))])
                            ->where('nama', '!=', NULL)
                            ->orderByRaw('tanggal ASC, no_urut ASC')
                            ->groupBy('nama')
                            ->get();

        
        $order          = MarketingSO::where('no_so', 'like', "%" . $request->nomer_so . "%")->first();
        $order_detail   = OrderItem::where('order_id', $order->id)->get();

        if ($request->key == 'data_riwayat') {

            if ($request->get == 'unduh') {

                return view('admin.pages.ekspedisi.riwayat.unduh_allrute', compact('data', 'request')) ;

            } else if ($request->get == 'detail_item') {

                return view('admin.pages.ekspedisi.riwayat.modal_detail',compact('order','order_detail'));

            } else {

                return view('admin.pages.ekspedisi.riwayat.data', compact('data')) ;
            }
        } else

        if($request->key == 'edit'){
            $data = Ekspedisi_rute::where('id', $request->ideksrute)->first();
            return response()->json($data);
        } else {
            return view('admin.pages.ekspedisi.riwayat.index') ;
        }
    }

    public function update(Request $request){
        if($request->key == 'update'){
            Ekspedisi_rute::where('id', $request->idsummaryrute)
            ->update([
                'berat' => $request->berat,
                'qty' => $request->qty
            ]);
            return response()->json([
                'msg' => 'Berhasil update',
                'status' => 'success'
            ]);
        }
    }
    public function store(Request $request)
    {
        if ($request->key == 'pindah_supir') {

            if (!$request->pindah) {
                $result['status']   =   400;
                $result['msg']      =   "Pilih lokasi pindah ekspedisi";
                return $result;
            }

            $rute   =   Ekspedisi_rute::find($request->id) ;

            if ($rute) {
                $ekspedisi              =   Ekspedisi::find($rute->ekspedisi_id) ;
                $rute->ekspedisi_id     =   $request->pindah ;
                $rute->save() ;

                $qty    =   0;
                $berat  =   0;
                foreach ($ekspedisi->ekspedisirute as $row) {
                    $qty            +=  $row->qty;
                    $berat          +=  $row->berat;
                }

                $ekspedisi->qty     =   $qty ;
                $ekspedisi->berat   =   $berat ;
                $ekspedisi->save() ;

                $result['status']   =   200 ;
                $result['msg']      =   "Pindah ekspedisi berhasil" ;
                return $result;
            }

            $result['status']   =   400 ;
            $result['msg']      =   "Item tidak ditemukan" ;
            return $result ;

        } else

        if ($request->key == 'temporary') {

            $getSO              =   MarketingSO::where('no_so', '=', $request->id)
                                    ->first();

            // dd($order->list_order);

            $ekspedisi          =   Ekspedisi::find($request->ekspedisi) ?? Ekspedisi::where('status', 1)->first() ?? new Ekspedisi ;
            if (!$request->ekspedisi) {
                $ekspedisi->status  =   1 ;
                if (!$ekspedisi->save() ) {
                    DB::rollBack() ;
                    $result['status']   =   400;
                    $result['msg']      =   "Proses gagal";
                    return $result;
                }
            }


            $order_berat        = 0;
            $order_qty          = 0;


            if ($getSO->status == 3) {
                $order              =   Order::where('orders.no_so', '=', $request->id)
                ->with('list_order','marketing_so')
                ->first();

                foreach($order->list_order as $o){
                    $order_berat        += $o->fulfillment_berat;
                    $order_qty          += $o->fulfillment_qty;
                }

            } else {

                $order_berat        += $getSO->itemActual->sum('berat');
                $order_qty          += $getSO->itemActual->sum('qty');

            }


            

            $rute               =   new Ekspedisi_rute ;
            $rute->ekspedisi_id =   $ekspedisi->id ;
            $rute->no_so        =   $getSO->no_so ;
            $rute->no_do        =   NULL ;
            $rute->qty          =   $order_qty ;
            $rute->berat        =   $order_berat ;
            $rute->status       =   1 ;
            if (!$rute->save()) {
                DB::rollBack();
                $result['status']   =   400;
                $result['msg']      =   "Proses gagal";
                return $result;
            }

            if ($request->ekspedisi) {
                $qty    =   0;
                $berat  =   0;
                foreach ($ekspedisi->ekspedisirute as $row) {
                    $qty            +=  $row->qty;
                    $berat          +=  $row->berat;
                }

                $ekspedisi->qty     =   $qty;
                $ekspedisi->berat   =   $berat;
                if (!$ekspedisi->save()) {
                    DB::rollBack();
                    $result['status']   =   400;
                    $result['msg']      =   "Proses gagal";
                    return $result;
                }
            }


            $result['status']   =   200;
            $result['msg']      =   "Berhasil ditambahkan ke list";
            return $result;
        } else

        if ($request->key == 'bypass_rute') {
            $order              =   Order::find($request->id);

            $order_berat        =   0;
            $order_qty          =   0;

            foreach ($order->list_order as $o) {
                $order_berat    =   $order_berat + $o->berat;
                $order_qty      =   $order_qty + $o->qty;
            }

            DB::beginTransaction() ;

            $ekspedisi          =   Ekspedisi::where('no_urut', $request->no_urut)
                                    ->whereDate('tanggal', $request->tanggal)
                                    ->first() ?? new Ekspedisi ;

            $ekspedisi->tanggal     =   $request->tanggal;
            $ekspedisi->no_urut     =   $request->no_urut;
            $ekspedisi->status      =   2;
            if (!$ekspedisi->save()) {
                DB::rollBack();
                $result['status']   =   400 ;
                $result['msg']      =   'Proses gagal' ;
                return $result ;
            }

            $rute                   =   new Ekspedisi_rute;
            $rute->ekspedisi_id     =   $ekspedisi->id;
            $rute->no_so            =   $order->no_so;
            $rute->no_do            =   NULL;
            $rute->qty              =   $order_qty;
            $rute->berat            =   $order_berat;
            $rute->status           =   1;
            if (!$rute->save()) {
                DB::rollBack();
                $result['status']   =   400;
                $result['msg']      =   'Proses gagal';
                return $result;
            }

            DB::commit();
            $result['status']   =   200;
            $result['msg']      =   'Buat rute bypass berhasil';
            return $result;

        } else

        if ($request->key == 'batal_do') {
            Ekspedisi_rute::find($request->id)->delete();

            if (!$request->ekspedisi) {
                if (Ekspedisi_rute::whereIn('ekspedisi_id', Ekspedisi::select('id')->where('status', 1))->count() < 1) {
                    Ekspedisi::where('status', 1)->delete() ;
                }
            } else {
                $ekspedisi  =   Ekspedisi::find($request->ekspedisi) ;
                $qty        =   0 ;
                $berat      =   0 ;
                foreach ($ekspedisi->ekspedisirute as $row) {
                    $qty            +=  $row->qty ;
                    $berat          +=  $row->berat ;
                }

                $ekspedisi->qty     =   $qty;
                $ekspedisi->berat   =   $berat;
                $ekspedisi->save();
            }

            $result['status']   =   200;
            $result['msg']      =   "DO berhasil berhasil dihapus dalam list";
            return $result;
        } else

        if ($request->key == 'selesaikan') {
            if (!$request->input_driver) {
                $driver =   Driver::find($request->driver);
                if (!$driver) {
                    $result['status']   =   400;
                    $result['msg']      =   "Driver belum dipilih";
                    return $result;
                }
            } else {
                if (!$request->telp_driver) {
                    $result['status']   =   400;
                    $result['msg']      =   "Nomor telepon belum diisikan";
                    return $result;
                }
            }

            if (!$request->input_nopol) {
                if (!$request->no_polisi) {
                    $result['status']   =   400;
                    $result['msg']      =   "Nomor polisi belum diisikan";
                    return $result;
                }
            }

            if (!$request->input_wilayah) {
                if (!$request->wilayah) {
                    $result['status']   =   400;
                    $result['msg']      =   "Wilayah belum dipilih";
                    return $result;
                }
            }

            $ekspedisi              =   Ekspedisi::find($request->ekspedisi) ?? Ekspedisi::where('status', 1)->first() ;

            DB::beginTransaction() ;

            if ($request->input_driver) {
                $driver                 =   new Driver;
                $driver->nama           =   $request->input_driver;
                $driver->telp           =   $request->telp_driver;
                $driver->driver_kirim   =   1;
                if (!$driver->save()) {
                    DB::rollBack();
                    $result['status']   =   400;
                    $result['msg']      =   "Proses gagal";
                    return $result;
                }
            }

            if ($request->input_nopol) {
                DB::rollBack() ;
                $nopol              =   new Nopolisi ;
                $nopol->nama        =   $request->input_nopol ;
                if (!$nopol->save()) {
                    DB::rollBack();
                    $result['status']   =   400;
                    $result['msg']      =   "Proses gagal";
                    return $result;
                }
            }

            if ($request->input_wilayah) {
                $wilayah            =   new Wilayah;
                $wilayah->nama      =   $request->input_wilayah ;
                $wilayah->status    =   1 ;
                if (!$wilayah->save()) {
                    DB::rollBack();
                    $result['status']   =   400;
                    $result['msg']      =   "Proses gagal";
                    return $result;
                }
            }

            $ekspedisi->driver_id   =   $driver->id ;
            $ekspedisi->nama        =   $driver->nama ;
            $ekspedisi->kernek      =   $request->kernek ?? NULL ;
            $ekspedisi->tanggal     =   $request->tanggal ;
            $ekspedisi->no_polisi   =   $nopol->nama ?? $request->no_polisi ;
            $ekspedisi->wilayah_id  =   $wilayah->id ?? $request->wilayah ;
            $ekspedisi->status      =   2 ;

            $qty    =   0 ;
            $berat  =   0 ;
            foreach ($ekspedisi->ekspedisirute as $row) {
                $qty            +=  $row->qty ;
                $berat          +=  $row->berat ;
                $row->status    =   2 ;
                if (!$row->save()) {
                    DB::rollBack();
                    $result['status']   =   400;
                    $result['msg']      =   "Proses gagal";
                    return $result;
                }
            }

            $ekspedisi->qty     =   $qty ;
            $ekspedisi->berat   =   $berat ;
            if (!$ekspedisi->no_urut) {
            $ekspedisi->no_urut =   Ekspedisi::nomor_do($ekspedisi->tanggal);
            }
            if (!$ekspedisi->save()) {
                DB::rollBack();
                $result['status']   =   400;
                $result['msg']      =   "Proses gagal";
                return $result;
            }

            DB::commit() ;
            $result['status']   =   200;
            $result['msg']      =   $request->ekspedisi ? "Ekspedisi berhasil diperbaharui" : "Ekspedisi berhasil dibuat";
            return $result;
        }

        if ($request->key == 'hapus_ekspedisi') {
            $data   =   Ekspedisi::find($request->id);

            if (!$data) {
                $result['status']   =   400;
                $result['msg']      =   "Ekspedisi tidak ditemukan";
                return $result;
            }

            foreach ($data->ekspedisirute as $row) {
                foreach (Bahanbaku::where('no_do', $row->no_do)->get() as $list) {
                    $list->ekspedisi    =   NULL;
                    $list->save();

                    $esp                =   Order::find($list->order_id);
                    $esp->ekspedisi     =   NULL;
                    $esp->save();
                }

                $row->delete();
            }

            $data->delete() ;

            $result['status']   =   200;
            $result['msg']      =   "Proses kirim ekspedisi dihapuskan" ;
            return $result;
        }

        if ($request->key == 'kirim') {
            $data   =   Ekspedisi::where('id', $request->id)
                        ->where('status', 2)
                        ->first();

            if (!$data) {
                $result['status']   =   400;
                $result['msg']      =   "Ekspedisi tidak ditemukan";
                return $result;
            }

            $data->status   =   3 ;
            $data->keluar   =   Carbon::now() ;
            $data->save() ;

            foreach ($data->ekspedisirute as $row) {
                $row->status    =   3;
                $row->save();
            }

            $result['status']   =   200;
            $result['msg']      =   "Proses kirim ekspedisi berlangsung" ;
            return $result;
        }

        if ($request->key == 'batal_kirim') {
            $data   =   Ekspedisi::where('id', $request->id)
                        ->where('status', 3)
                        ->first();

            if (!$data) {
                $result['status']   =   400;
                $result['msg']      =   "Ekspedisi tidak ditemukan";
                return $result;
            }

            $data->status   =   2 ;
            $data->keluar   =   NULL ;
            $data->save() ;

            foreach ($data->ekspedisirute as $row) {
                $row->status    =   2;
                $row->save();
            }

            $result['status']   =   200;
            $result['msg']      =   "Proses kirim ekspedisi dibatalkan" ;
            return $result;
        }

        if ($request->key == 'selesai') {
            $data   =   Ekspedisi::where('id', $request->id)
                        ->where('status', 3)
                        ->first();

            if (!$data) {
                $result['status']   =   400;
                $result['msg']      =   "Ekspedisi tidak ditemukan";
                return $result;
            }

            $data->status   =   4;
            $data->kembali  =   Carbon::now();
            $data->save();

            foreach ($data->ekspedisirute as $row) {
                $row->status    =   4;
                $row->save();
            }

            $result['status']   =   200;
            $result['msg']      =   "Proses kirim ekspedisi selesai";
            return $result;
        }
    }
}
