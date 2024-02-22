<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chiller;
use App\Models\Driver;
use App\Models\Ekspedisi;
use App\Models\FreestockTemp;
use App\Models\Item;
use App\Models\Marketing;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MarketingController extends Controller
{

    public function index(Request $request)
    {
        $tanggal        =   $request->tanggal ?? Carbon::now()->format('Y-m-d');

        $fulfillment    =   Order::whereIn('id', OrderItem::select('order_id')
                            ->where('status', '>=', 2))
                            ->where('tanggal_kirim', $tanggal)
                            ->get();

        $ekspedisi      =   Ekspedisi::get();

        $retur          =   OrderItem::where('retur_status', 1)->get();

        $stock          =   Chiller::whereIn('type',['bahan-baku','retur'])->get();


        if($request->key == 'marketinglist'){
            $data       =   Marketing::all();
            return view('admin.pages.marketing.marketinglist', compact('data'));
        }


        return view('admin.pages.marketing.index',compact('fulfillment','tanggal','ekspedisi','retur','stock'));
    }
    public function dashboard(Request $request)
    {
        $data   =   FreestockTemp::where('tanggal_produksi', '=', date('Y-m-d'))
                    // ->where('plastik_sku', 'like', "2%")
                    ->get() ;

        return view('admin.pages.marketing.dashboard', compact('data'));
    }
    public function fulfillment(Request $request)
    {
        return view('admin.pages.marketing.fulfillment');
    }
    public function stock(Request $request)
    {
        return view('admin.pages.marketing.stock-cs');
    }

    public function detail($id)
    {

            $ekspedisi  =   Ekspedisi::where('driver_id', $id)
                            ->where('kembali', NULL)
                            ->first();
            // return $ekspedisi;
        return view('admin.pages.marketing.ekspedisidetail', compact('ekspedisi'));

    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        $namamarketing          =       $request->namamarketing ?? '';
        $namaalias              =       $request->namaalias ?? '';


        $marketing              =       new Marketing;
        $marketing->nama        =       $namamarketing;
        $marketing->nama_alias  =       $namaalias;

        if(!$marketing->save()){
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan data');
        }

        DB::commit();
        return response()->json([
            'msg' => 'Berhasil menyimpan data',
            'status' => 'success'
        ]);

    }

    public function show($id)
    {
        //
    }

    public function edit(Request $request)
    {
        $data               =   Marketing::where('id',$request->id)->first();
        return response()->json([
            'data' => $data,
            'status' => 'success'
        ]);

    }

    public function update(Request $request)
    {
        DB::beginTransaction();
        $namamarketing                              =       $request->namamarketing ?? '';
        $namaalias                                  =       $request->namaalias ?? '';
        $id                                         =       $request->id ?? '';

        $marketing                                  =       Marketing::find($id);
        if($marketing){
            $marketing->nama                        =       $namamarketing;
            $marketing->nama_alias                  =       $namaalias;
            $marketing->netsuite_internal_id        =       $request->netsuite_internal_id;
        } else {
            DB::rollBack() ;
            return response()->json([
                'msg' => 'Data tidak ditemukan',
                'status' => 'error'
            ]);
        }

        if(!$marketing->save()){
            DB::rollBack();
            return response()->json([
                'msg' => 'Gagal menyimpan data',
                'status' => 'error'
            ]);
        }

        DB::commit();
        return response()->json([
            'msg' => 'Berhasil menyimpan data',
            'status' => 'success'
        ]);
    }

    public function destroy($id)
    {
        //
    }
}
