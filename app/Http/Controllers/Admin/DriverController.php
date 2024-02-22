<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppKey;
use App\Models\Chiller;
use App\Models\Driver;
use App\Models\Ekspedisi;
use App\Models\Ekspedisi_rute;
use App\Models\Netsuite;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemLog;
use App\Models\Product_gudang;
use App\Models\Production;
use App\Models\Retur;
use App\Models\ReturItem;
use App\Models\Target;
use App\Models\User;
use App\Models\Wilayah;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class DriverController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (User::setIjin(19)) {
            if ($request->key == 'view_data') {
                $driver = Driver::where('id', $request->id)->first();
                if (isset($driver)) {
                    $response = [
                        'status_code' => Response::HTTP_FOUND,
                        'success' => true,
                        'message' => "success",
                        'data' => $driver,
                    ];
                } else {
                    $response = [
                        'status_code' => Response::HTTP_NOT_FOUND,
                        'success' => false,
                        'message' => "Data Not Found",
                    ];
                }
                return response()->json($response);
            } else {
                $q      =   $request->q ?? '';
                $jenisdriver  =   $request->jenisdriver ?? '';
                $data   =   Driver::orderBy('id', 'DESC')->get();
    
                $data   =   $data->filter(function ($item) use ($q) {
                    $res = true;
                    if ($q != "") {
                        $res =  (false !== stripos($item->nama, $q)) ||
                            (false !== stripos($item->jenis, $q)) ||
                            (false !== stripos($item->no_polisi, $q)) ||
                            (false !== stripos($item->telp, $q)) ||
                            (false !== stripos($item->code_item, $q)) ||
                            (false !== stripos(($item->itemkat->nama ?? ''), $q));
                    }
                    return $res;
                });
    
                return view('admin/pages/driver', compact('data', 'q'));
            }
        }
        return redirect()->route("index");
    }

    public function store(Request $request)
    {
        if (User::setIjin(19)) {

            $driver             =   new Driver;
            $driver->nama       =   $request->namasopir;
            $driver->telp       =   $request->notelp;
            $driver->no_polisi  =   $request->no_polisi;
            // $driver->alamat     =   $request->alamat;
            // $driver->kelurahan  =   $request->kelurahan;
            // $driver->kecamatan  =   $request->kecamatan;
            // $driver->kota       =   $request->kota;
            // $driver->provinsi   =   $request->provinsi;
            // $driver->kode_pos  =   $request->kodepos;
            if ($request->jenis == 'kirim') {
                $driver->driver_kirim       =   1 ;
            } else {
                $driver->driver_exspedisi   =   1;
            }
            $driver->save();

            return back()->with('status', 1)->with('message', 'Data berhasil ditambahkan');
        }
        return redirect()->route("index");
    }

    public function update(Request $request)
    {
        if (User::setIjin(19)) {

            $driver             =   Driver::find($request->x_code);
            $driver->nama       =   $request->namasopir;
            $driver->telp       =   $request->notelp;
            $driver->no_polisi       =   $request->no_polisi;
            // $driver->alamat     =   $request->alamat;
            // $driver->kelurahan  =   $request->kelurahan;
            // $driver->kecamatan  =   $request->kecamatan;
            // $driver->kota       =   $request->kota;
            // $driver->provinsi   =   $request->provinsi;
            // $driver->kode_pos  =   $request->kodepos;
            if ($request->jenis == 'kirim') {
                $driver->driver_exspedisi   =   NULL;
                $driver->driver_kirim       =   1;
            } else {
                $driver->driver_exspedisi   =   1;
                $driver->driver_kirim       =   NULL;
            }
            $driver->save();

            return back()->with('status', 1)->with('message', 'Data berhasil diubah');
        }
        return redirect()->route("index");
    }


    public function addekspedisi(Request $request, $id)
    {
        if (User::setIjin(19)) {
            $data   =   Driver::find($id);

            if ($data) {
                $request->validate([
                    'tanggal'   =>  'required|date',
                    'no_polisi' =>  'required',
                    'wilayah'   =>  ['required', Rule::exists('wilayah', 'id')]
                ]);

                $actived    =   Ekspedisi::where('driver_id', $data->id)
                    ->where('kembali', NULL)
                    ->where('status', 1)
                    ->first();

                $ekspedisi              =   $actived ? Ekspedisi::find($actived->id) : new Ekspedisi;
                $ekspedisi->driver_id   =   $data->id;
                $ekspedisi->nama        =   $data->nama;
                $ekspedisi->no_polisi   =   $request->no_polisi;
                $ekspedisi->wilayah_id  =   $request->wilayah;
                $ekspedisi->keluar      =   $request->tanggal;
                $ekspedisi->status      =   1;
                $ekspedisi->save();

                foreach ($ekspedisi->ekspedisirute as $row) {
                    foreach ($row->ruteorder->daftar_order as $list) {

                        $log_item                   = new OrderItemLog;
                        $log_item->activity         = "ekspedisi-selesai";
                        $log_item->order_item_id    = $list->id;
                        $log_item->user_id          = Auth::user()->id;
                        $log_item->key              = AppKey::generate();
                        $log_item->save();
                    }
                }
            }

            return back();
        }
        return redirect()->route("index");
    }

    public function show($id)
    {
        if (User::setIjin(19)) {
            $data   =   Driver::find($id);

            if ($data) {
                $ekspedisi  =   Ekspedisi::where('driver_id', $data->id)
                    ->where('kembali', NULL)
                    ->first();

                if ($data->pickup) {
                    return view('admin.pages.driver.summary', compact('data', 'ekspedisi'));
                } else {
                    $wilayah    =   Wilayah::pluck('nama', 'id');

                    return view('admin.pages.driver.show', compact('data', 'ekspedisi', 'wilayah'));
                }
            }

            return redirect()->route('driver.index');
        }
        return redirect()->route("index");
    }

    public function order($id)
    {
        if (User::setIjin(19)) {
            // $order      =   Order::where('status', 5)
            //     ->whereNotIn('id', Ekspedisi_rute::select('order_id'))
            //     ->get();
            $itemorders =   OrderItem::where('status', 1)
                            ->whereIn('order_id', Order::select('id'))
                            ->whereNotIn('id', Ekspedisi_rute::select('order_item_id'))
                            ->get();

            $ekspedisi  =   Ekspedisi::where('driver_id', $id)
                            ->where('kembali', NULL)
                            ->where('status', 1)
                            ->first();

            return view('admin.pages.driver.customer_order', compact('ekspedisi', 'itemorders'));
        }
        return redirect()->route("index");
    }

    public function result($id)
    {
        if (User::setIjin(19)) {
            $ekspedisi  =   Ekspedisi::where('driver_id', $id)
                ->where('kembali', NULL)
                ->where('status', 1)
                ->first();

            $route      =   Ekspedisi_rute::where('ekspedisi_id', $ekspedisi->id)
                ->orderBy('id', 'DESC')
                ->get();

            $total_item     =   0;
            $total_berat    =   0;
            $total_qty      =   0;
            foreach ($route as $row) {
                $hitung     =   OrderItem::select(DB::raw("SUM(qty) AS total_item"), DB::raw("SUM(berat) AS total_berat"), DB::raw("SUM(qty) AS total_qty"))
                    ->where('id', $row->order_item_id)
                    ->first();

                $total_item     +=  $hitung->total_item;
                $total_berat    +=  $hitung->total_berat;
                $total_qty      +=  $hitung->total_qty;
            }

            return view('admin.pages.driver.result', compact('id', 'total_item', 'total_berat', 'total_qty'));
        }
        return redirect()->route("index");
    }

    public function addorder(Request $request, $id)
    {
        if (User::setIjin(19)) {
            $ekspedisi  =   Ekspedisi::select('id')
                ->where('driver_id', $id)
                ->where('kembali', NULL)
                ->where('status', 1)
                ->first();


            $itemorders  =   OrderItem::where('id', $request->row_id)->where('status', 1)->first();

            $order      =   Order::where('id', $itemorders->order_id)
                ->first();


            // foreach ($itemorders as $item) {

            $route                  =   new Ekspedisi_rute;
            $route->ekspedisi_id    =   $ekspedisi->id;
            $route->nama            =   $order->nama;
            $route->alamat          =   $order->alamat;
            $route->telp            =   $order->telp;
            $route->kelurahan       =   $order->kelurahan;
            $route->kecamatan       =   $order->kecamatan;
            $route->kota            =   $order->kota;
            $route->provinsi        =   $order->provinsi;
            $route->kode_pos        =   $order->kode_pos;
            $route->order_id        =   $order->id;
            $route->order_item_id   =   $itemorders->id;
            $route->wilayah_id      =   $ekspedisi->wilayah_id;
            $route->status          =   1;
            $route->save();
            // }

            $itemorders->status =   2;
            $itemorders->save();
        }
        return redirect()->route("index");
    }

    public function route($id)
    {
        if (User::setIjin(19)) {
            $ekspedisi  =   Ekspedisi::where('driver_id', $id)
                ->where('kembali', NULL)
                ->where('status', 1)
                ->first();

            $route  =   '' ;
            if ($ekspedisi) {
                $route  =   Ekspedisi_rute::where('ekspedisi_id', $ekspedisi->id)
                            ->orderBy('id', 'DESC')
                            ->get();
            }

            return view('admin.pages.driver.delivery_route', compact('ekspedisi', 'route'));
        }
        return redirect()->route("index");
    }


    public function batalroute(Request $request, $id)
    {
        if (User::setIjin(19)) {
            $ekspedisi  =   Ekspedisi::select('id')
                ->where('driver_id', $id)
                ->where('kembali', NULL)
                ->where('status', 1)
                ->first();

            if ($ekspedisi) {
                $rute               =   Ekspedisi_rute::find($request->row_id);

                $orderitem          =   OrderItem::find($rute->order_item_id);
                $orderitem->status  =   1;
                $orderitem->save();

                $rute->delete();
            }
        }
        return redirect()->route("index");
    }

    public function ready(Request $request, $id)
    {
        if (User::setIjin(19)) {

            DB::beginTransaction();

            $ekspedisi  =   Ekspedisi::select('id', 'status', 'driver_id')
                ->where('driver_id', $id)
                ->where('kembali', NULL)
                ->whereIn('status', [1, 2])
                ->first();

            if ($ekspedisi) {
                if ($ekspedisi->status == 1) {
                    $berat  =   0;
                    $total  =   0;

                    $order_id = Ekspedisi_rute::where('ekspedisi_id', $ekspedisi->id)->get();

                    foreach ($order_id as $row) {
                        $cekorder                       =   OrderItem::where('order_id', $row->order_id)->where('status', 1)->count();
                        $order                          =   Order::find($row->order_id);
                        if ($cekorder == '') {
                            $order->no_invoice          =   Order::nomor_invoice(Carbon::now());
                            $order->invoice_created_at  =   Carbon::now();
                            $order->status              =   6;
                            $order->save();
                        } else {
                            $order->no_invoice          =   Order::nomor_invoice(Carbon::now());
                            $order->invoice_created_at  =   Carbon::now();
                            $order->status              =   5;
                            $order->save();
                        }

                    }

                    $order_id_ns = Ekspedisi_rute::where('ekspedisi_id', $ekspedisi->id)->groupBy('order_id')->get();

                    // foreach($order_id_ns as $row):
                    //     Netsuite::item_fulfill('orders', $row->order_id, 'itemfulfill', $ekspedisi->id, null);
                    // endforeach;

                    foreach ($ekspedisi->ekspedisirute as $row) {

                        $list = OrderItem::find($row->order_item_id);

                        $berat  +=  $list->fulfillment_berat;
                        $total  +=  $list->fulfillment_qty;

                        $log_item                   = new OrderItemLog;
                        $log_item->activity         = "ekspedisi-proses";
                        $log_item->order_item_id    = $row->order_item_id;
                        $log_item->user_id          = Auth::user()->id;
                        $log_item->key              = AppKey::generate();
                        $log_item->save();
                    }

                    $ready                  =   Ekspedisi::find($ekspedisi->id);
                    $ready->no_urut         =   Ekspedisi::nomor_do($ekspedisi->keluar);
                    $ready->berat           =   $berat;
                    $ready->qty             =   $total;
                    $ready->status          =   2;
                    $ready->save();

                    Ekspedisi_rute::where('ekspedisi_id', $ekspedisi->id)
                        ->update([
                            'delivery_date' =>  Carbon::now(),
                            'delivery_time' =>  Carbon::now(),
                            'wilayah_id'    =>  $ready->wilayah_id,
                        ]);
                    DB::commit();
                    return redirect()->route('driver.index')->with('status', 1)->with('message', 'Ekspedisi pengiriman berhasil diselesaikan');
                }

                if ($ekspedisi->status == 2) {
                    $cek    =   FALSE;
                    foreach ($ekspedisi->ekspedisirute as $row) {
                        if ($row->order_id == $request->x_code) $cek    =   TRUE;
                    }

                    if ($cek) {
                        $selesaikan             =   Order::find($request->x_code);
                        $selesaikan->status     =   10;
                        $selesaikan->save();

                        $route                  =   Ekspedisi_rute::where('order_id', $request->x_code)->first();
                        $route->status          =   2;
                        $route->save();

                        if ($ekspedisi->ekspedisidriver->jumlah_pengiriman == 0) {
                            $ekspedisi->status  =   3;
                            $ekspedisi->kembali =   Carbon::now();
                            $ekspedisi->save();

                            DB::commit();
                            return redirect()->route('driver.index')->with('status', 1)->with('message', 'Ekspedisi pengiriman berhasil diselesaikan');
                        }
                        DB::commit();
                        return back()->with('status', 1)->with('message', 'Delivery route berhasil diselesaikan');
                    }
                }
            }
            DB::rollBack();
            return back();
        }
        return redirect()->route("index");
    }

    public function complete($id)
    {
        if (User::setIjin(19)) {
            $ekspedisi  =   Ekspedisi::find($id);
            if ($ekspedisi) {
                foreach ($ekspedisi->ekspedisirute as $row) {
                    if ($row->status == 1) {
                        $selesaikan             =   Order::find($row->order_id);
                        $selesaikan->status     =   10;
                        $selesaikan->save();

                        foreach ($row->ruteorder->daftar_order as $list) {

                            $log_item                   = new OrderItemLog;
                            $log_item->activity         = "ekspedisi-selesai";
                            $log_item->order_item_id    = $list->id;
                            $log_item->user_id          = Auth::user()->id;
                            $log_item->key              = AppKey::generate();
                            $log_item->save();
                        }
                        $route                  =   Ekspedisi_rute::where('order_id', $row->order_id)->first();
                        $route->status          =   2;
                        $route->save();
                    }
                }

                $ekspedisi->status  =   3;
                $ekspedisi->kembali =   Carbon::now();
                $ekspedisi->save();

                return redirect()->route('driver.index')->with('status', 1)->with('message', 'Ekspedisi pengiriman berhasil diselesaikan');
            }

            return back();
        }
        return redirect()->route("index");
    }


    public function destroy($id)
    {
        if (User::setIjin(19)) {
            $ekspedisi  =   Ekspedisi::select('id')
                ->where('driver_id', $id)
                ->where('kembali', NULL)
                ->where('status', 1)
                ->first();

            if ($ekspedisi) {
                Ekspedisi_rute::where('ekspedisi_id', $ekspedisi->id)->delete();
                Ekspedisi::find($ekspedisi->id)->delete();
            }

            return back();
        }
        return redirect()->route("index");
    }

    public function cart()
    {
        if (User::setIjin(19)) {
            $data   =   Driver::orderBy('id', 'DESC')->get();
            return view('admin.pages.driver.keranjang', compact('data'));
        }
        return redirect()->route("index");
    }

    public function laporan()
    {
        return view('admin.pages.driver.laporan.index');
    }

    public function loadlaporan(Request $request){
        if($request->key == 'cari'){
            $q          =   $request->q ?? '';
            $data    =   Driver::select(DB::raw('driver.*'), DB::raw('count(productions.id) as countambil'), DB::raw('count(ekspedisi.id) as countantar'))
                ->leftJoin('productions', 'productions.sc_pengemudi_id', '=', 'driver.id')
                ->leftJoin('ekspedisi', 'ekspedisi.driver_id', '=', 'driver.id')
                ->groupBy('driver.nama')
                ->where(function($query) use ($request) {
                    if ($request->jenisdriver == 'tangkap') {
                        $query->Where('driver_kirim', NULL) ;
                    }
                    if ($request->jenisdriver == 'kirim') {
                        $query->Where('driver_kirim', '1') ;
                    }
                    if($request->pencarian !== ''){
                        $query->where('driver.nama', 'like', '%' . $request->pencarian . '%');
                    }
                })
                
                ->get();
    
    
            $data   =   $data->filter(function ($item) use ($q) {
                $res = true;
                if ($q != "") {
                    $res =  (false !== stripos($item->nama, $q)) ||
                        (false !== stripos($item->alamat, $q)) ||
                        (false !== stripos($item->no_polisi, $q)) ||
                        (false !== stripos($item->telp, $q)) ||
                        (false !== stripos($item->kelurahan, $q)) ||
                        (false !== stripos($item->driver_kirim, $q)) ||
                        (false !== stripos($item->kecamatan, $q)) ||
                        (false !== stripos($item->kota, $q)) ||
                        (false !== stripos($item->provinsi, $q)) ||
                        (false !== stripos($item->kode_pos, $q));
                }
                return $res;
            });
    
    
            $data   =   $data->paginate(30);
            return view('admin.pages.driver.laporan.data_index', compact('q', 'data'));   
        } else {
            $q      =   $request->q ?? '';
            $data    =   Driver::select(DB::raw('driver.*'), DB::raw('count(productions.id) as countambil'), DB::raw('count(ekspedisi.id) as countantar'))
                ->leftJoin('productions', 'productions.sc_pengemudi_id', '=', 'driver.id')
                ->leftJoin('ekspedisi', 'ekspedisi.driver_id', '=', 'driver.id')
                ->groupBy('driver.nama')
                ->where(function($query) use ($request) {
                    if ($request->jenisdriver == 'tangkap') {
                        $query->orWhere('driver_kirim', NULL) ;
                    }
                    if ($request->jenisdriver == 'kirim') {
                        $query->orWhere('driver_kirim', '1') ;
                    }
                })
                ->get();
    
    
            $data   =   $data->filter(function ($item) use ($q) {
                $res = true;
                if ($q != "") {
                    $res =  (false !== stripos($item->nama, $q)) ||
                        (false !== stripos($item->alamat, $q)) ||
                        (false !== stripos($item->no_polisi, $q)) ||
                        (false !== stripos($item->telp, $q)) ||
                        (false !== stripos($item->kelurahan, $q)) ||
                        (false !== stripos($item->driver_kirim, $q)) ||
                        (false !== stripos($item->kecamatan, $q)) ||
                        (false !== stripos($item->kota, $q)) ||
                        (false !== stripos($item->provinsi, $q)) ||
                        (false !== stripos($item->kode_pos, $q));
                }
                return $res;
            });
    
    
            $data   =   $data->paginate(30);
            return view('admin.pages.driver.laporan.data_index', compact('q', 'data'));
        }
    }

    public function cari_driver(Request $request){
        $driver         =   Driver::select(DB::raw('driver.*'), DB::raw('count(productions.id) as countambil'), DB::raw('count(ekspedisi.id) as countantar'))
                            ->leftJoin('productions', 'productions.sc_pengemudi_id', '=', 'driver.id')
                            ->leftJoin('ekspedisi', 'ekspedisi.driver_id', '=', 'driver.id')
                            ->groupBy('driver.nama')
                            ->where('driver.id', $request->id)
                            ->first();

        $id             = $request->id;

        if ($driver) {
            $mulai      =   $request->tanggal_mulai ?? date('Y-m-01');
            $akhir      =   $request->tanggal_akhir ?? date('Y-m-d');
            $order      =   Ekspedisi::with('ekspedisirute')->whereIn('id', Ekspedisi_rute::select('ekspedisi_id')->where('driver_id', $request->id)->where('status', '>=', 2))
                            ->orderBy('tanggal', 'DESC')
                            ->where(function ($query) use ($mulai, $akhir) {
                                if ($mulai || $akhir) { 
                                    $query->whereBetween('tanggal', [$mulai, $akhir]);
                                }
                            })
                            ->paginate(15);
            $ekspedisi = Ekspedisi::selectRaw('*,count(wilayah_id) as jumlah')->where('status', '>=', 2)->where('driver_id', $request->id)->groupBy('wilayah_id')->get();
            // -----------------------------------------------

            $delivery   =   Production::where('sc_pengemudi_id', $request->id)
                            ->join('driver', 'driver.id', '=', 'productions.sc_pengemudi_id')
                            ->orderBy('sc_tanggal_masuk', 'DESC')
                            ->where(function ($query) use ($mulai, $akhir) {
                                if ($mulai || $akhir) {
                                    $query->whereBetween('sc_tanggal_masuk', [$mulai, $akhir]);
                                }
                            })
                            ->paginate(30);


            if($request->key == 'loadorder'){
                $datachart =    Production::whereBetween('sc_tanggal_masuk', [$mulai, $akhir])
                                ->where('po_jenis_ekspedisi', 'tangkap')
                                ->where(function($query) use ($driver) {
                                    if ($driver->nama != 'all') {
                                        $query->where('sc_pengemudi', $driver->nama) ;
                                    }
                                })
                                ->orderBy('sc_tanggal_masuk', 'ASC');
    
                $toleransi_chart    =   [] ;
                $susut_chart        =   [] ;
                $drvr               =   [] ;
                foreach ($datachart->get() as $key => $value) {
                    $toleransi = Target::where('alamat', 'like', '%' . preg_replace('/\s+/', '', $value->sc_wilayah) . '%')->orderBy('id', 'DESC')->first()->target ?? 0 ;
                    $toleransi_chart[] =  $value->lpah_persen_susut ? $toleransi : 0;
                    $susut_chart[] = $value->lpah_persen_susut ?? 0;
                    $drvr[] = $value->sc_pengemudi . '+'. $value->sc_tanggal_masuk;
                }
    
                $datachart   = $datachart->selectRaw('CONCAT(sc_pengemudi , " ", sc_tanggal_masuk) as nama')->pluck('nama');
                $alokasi     =   "[{name: 'Toleransi',data: ";
                $alokasi    .=  json_encode($toleransi_chart) ;
                $alokasi    .=  "}, {name: 'Susut',data: ";
                $alokasi    .=  json_encode($susut_chart);
                $alokasi    .=  "}]";

                return view('admin.pages.driver.laporan.data_order', compact('id', 'delivery', 'mulai', 'akhir', 'driver', 'datachart', 'alokasi'));

            } elseif($request->key == 'loadkirim') {
                return view('admin.pages.driver.laporan.data_kirim', compact('id', 'order', 'mulai', 'akhir', 'driver', 'ekspedisi'));
            } elseif($request->key == 'loaddatakirimdanorder'){
                return view('admin.pages.driver.laporan.data_kirimdanorder', compact('id', 'order', 'mulai', 'akhir', 'driver', 'ekspedisi','delivery'));
            }
            // dd($request->key);
            // return view('admin.pages.driver.laporan.data_order', compact('id', 'delivery', 'order', 'mulai', 'akhir', 'driver'));
        }
        return redirect()->route('driver.laporan');
    }
    public function detail_laporan(Request $request, $id)
    {
        $driver    =   Driver::select(DB::raw('driver.*'), DB::raw('count(productions.id) as countambil'), DB::raw('count(ekspedisi.id) as countantar'))
        ->leftJoin('productions', 'productions.sc_pengemudi_id', '=', 'driver.id')
        ->leftJoin('ekspedisi', 'ekspedisi.driver_id', '=', 'driver.id')
        ->groupBy('driver.nama')
        ->where('driver.id', $id)
        ->first();
        $mulai      =   $request->tanggal_mulai ?? date('Y-m-01');
        $akhir      =   $request->tanggal_akhir ?? date('Y-m-d');
        if($driver){
            return view('admin.pages.driver.laporan.detail', compact('id', 'driver', 'mulai', 'akhir'));
        } else {
            return redirect()->route('driver.laporan');
        }
    }

    public function retur($id)
    {
        if (User::setIjin(19)) {
            $data   =   Order::where('id', $id)->first();

            if ($data) {
                $list   =   OrderItem::where('order_id', $data->id)->where('retur_tujuan', null)->get();
                return view('admin.pages.driver.retur', compact('data', 'list'));
            }
            return redirect()->route('driver.laporan')->with('status', 2)->with('message', 'Data tidak ditemukan');
        }
        return redirect()->route('index')->with('error', 'Anda tidak memiliki hak akses ke halaman tersebut');
    }

    public function returadd(Request $request)
    {
        if (User::setIjin(19)) {

            DB::beginTransaction();

            $data                   =   OrderItem::find($request->item);

            $data->retur_qty        =   $request->qty;
            $data->retur_berat      =   $request->berat;
            $data->retur_tujuan     =   $request->tujuan;
            $data->retur_notes      =   $request->alasan;
            $data->retur_status     =   1;

            $order                  =   Order::find($data->order_id);

            $retur                  =   Retur::where('id_so', $order->id_so)->first() ?? new Retur;

            $retur->customer_id     =   $order->customer_id;
            $retur->id_so           =   $order->netsuite_internal_id;
            $retur->qc_id           =   Auth::user()->id;
            $retur->tanggal_retur   =   Carbon::now();

            $retur->status          =   1;
            if (!$retur->save()) {
                DB::rollBack() ;
                $result['status']   =   400 ;
                $result['msg']      =   "Proses Gagal" ;
                return $result ;
            }


            $item_retur             =   ReturItem::where('orderitem_id', $data->id)
                                        ->first() ?? new ReturItem;

            $item_retur->retur_id   =   $retur->id;
            $item_retur->item_id    =   $data->item_id;
            $item_retur->sku        =   $data->sku;
            $item_retur->orderitem_id     =   $request->orderitem_id;
            $item_retur->qty        =   $request->qty;
            $item_retur->unit       =   $request->tujuan;
            $item_retur->berat      =   $request->berat;
            $item_retur->rate       =   $data->rate;

            $item_retur->status     =   1;
            if (!$item_retur->save()) {
                DB::rollBack() ;
                $result['status']   =   400 ;
                $result['msg']      =   "Proses Gagal" ;
                return $result ;
            }


            $log_item                   = new OrderItemLog;
            $log_item->activity         = "retur-proses";
            $log_item->order_item_id    = $data->id;
            $log_item->user_id          = Auth::user()->id;
            $log_item->key              = AppKey::generate();
            if (!$log_item->save()) {
                DB::rollBack() ;
                $result['status']   =   400 ;
                $result['msg']      =   "Proses Gagal" ;
                return $result ;
            }

            $data->fulfillment_qty      =   $data->fulfillment_qty - $request->qty;
            $data->fulfillment_berat    =   $data->fulfillment_berat - $request->berat;
            if (!$data->save()) {
                DB::rollBack() ;
                $result['status']   =   400 ;
                $result['msg']      =   "Proses Gagal" ;
                return $result ;
            }

            DB::commit();
        }
        return redirect()->route('index')->with('error', 'Anda tidak memiliki hak akses ke halaman tersebut');
    }
}
