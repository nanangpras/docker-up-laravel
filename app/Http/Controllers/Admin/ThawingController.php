<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Abf;
use App\Models\Chiller;
use App\Models\Gudang;
use App\Models\Item;
use App\Models\Product_gudang;
use App\Models\Thawing as ModelsThawing;
use App\Models\Thawinglist;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Sum;

class ThawingController extends Controller
{

    public function index(Request $request)
    {
        $frozen     =   Item::select('items.*')
                        ->where('items.nama', 'like', '%frozen%')
                        ->where('items.nama', 'not like', '%repack%')
                        ->join('category', 'category.id', '=', 'items.category_id')->where(function($query) {
                            $query->orWhere('category_id', '<=', 20);
                            $query->orWhere('category.slug', 'like', 'ags');
                            $query->orWhere('category.slug', 'like', 'ags%');
                        })
                        ->get() ;

        $tanggal    =   $request->tanggal ?? date('Y-m-d');
        $keluar     =   Product_gudang::whereIn('status', [3, 4])->where('type', 'thawing')->where('production_date', $tanggal)->get();
        $request    =   ModelsThawing::where('tanggal_request', $tanggal)->get();

        return view('admin.pages.kepala_regu.thawing', compact('tanggal', 'keluar', 'frozen', 'request'));
    }

    public function show(Request $request)
    {
        $tanggal    =   $request->tanggal ?? date('Y-m-d');
        $data       =   Product_gudang::where('jenis_trans', 'masuk')
                        ->where('berat', '>', 0)
                        ->where('stock_type', $request->id)
                        ->whereDate('production_date', $tanggal)
                        ->where('status', 2)
                        ->get();

        return view('admin.pages.kepala_regu.thawingshow', compact('data'));
    }

    public function keluar(Request $request)
    {
        $tanggal    =   $request->tanggal ?? date('Y-m-d');
        $keluar =   Product_gudang::whereIn('status', [3, 4])->where('type', 'thawing')->where('jenis_trans','keluar')->where('production_date', $tanggal)->get();

        return view('admin.pages.kepala_regu.thawingshowkeluar', compact('keluar'));
    }

    public function store(Request $request)
    {
        if ($request->type == 'free') {
            DB::beginTransaction();

            $array  =   [];

            for ($x = 0; $x < COUNT($request->item); $x++) {
                $item   =   Item::select('items.*')
                            ->where('items.nama', 'like', '%frozen%')
                            ->where('items.nama', 'not like', '%repack%')
                            ->join('category', 'category.id', '=', 'items.category_id')->where(function($query) {
                                $query->orWhere('category_id', '<=', 20);
                                $query->orWhere('category.slug', 'like', 'ags');
                                $query->orWhere('category.slug', 'like', 'ags%');
                            })
                            ->where('items.id', $request->item[$x])
                            ->first();

                if ($item) {
                    $array[]    =   [
                        'item'              =>  $item->id,
                        'qty'               =>  $request->qty[$x],
                        'tanggal_request'   =>  $request->tanggal_request[$x],
                        'berat'             =>  $request->berat[$x],
                        'keterangan'        =>  $request->keterangan[$x]
                    ];
                }
            }


            if(COUNT($array)<1){
                DB::rollBack() ;
                return back()->with('status', 2)->with('message', 'Proses gagal, item kosong');
            }

            $thawing                    =   new ModelsThawing;
            $thawing->tanggal_request   =   $request->tanggal_thawing ?? Carbon::now();
            $thawing->item              =   json_encode($array);
            $thawing->regu              =   $request->regu;
            $thawing->status            =   1;


            if (!$thawing->save()) {
                DB::rollBack();
                return back()->with('status', 2)->with('message', 'Proses gagal');
            }

            DB::commit();

            return back()->with('status', 1)->with('message', 'Berhasil Request Thawing');
        } else {
            DB::beginTransaction();
            $gudang =   Product_gudang::find($request->x_code);

            $data                       =   new Product_gudang;
            $data->product_id           =   $gudang->product_id;
            $data->nama                 =   $gudang->nama;
            $data->sub_item             =   $gudang->sub_item;
            $data->table_id             =   $gudang->table_id;
            $data->table_name           =   $gudang->table_name;
            $data->order_id             =   $gudang->order_id;
            $data->order_item_id        =   $gudang->order_item_id;
            $data->qty_awal             =   $request->qty;
            $data->berat_awal           =   $request->berat;
            $data->qty                  =   $request->qty;
            $data->berat                =   $request->berat;
            $data->packaging            =   $gudang->packaging;
            $data->production_date      =   Carbon::now();
            $data->type                 =   'thawing';
            $data->gudang_id            =   $gudang->gudang_id;
            $data->stock_type           =   $gudang->stock_type;
            $data->jenis_trans          =   'keluar';
            $data->gudang_id_keluar     =   $request->x_code;
            $data->status               =   4;

            if (!$data->save()) {
                DB::rollBack();
                return back()->with('status', 2)->with('message', 'Proses gagal');
            }

            DB::commit();

            return back()->with('status', 1)->with('message', 'Thawing selesai diproses');
        }
    }


    public function proses(Request $request)
    {
        $gudang     =   Gudang::where('subsidiary', env('NET_SUBSIDIARY', 'CGL'))
                        ->where('kategori', 'warehouse')
                        ->where('status', '1')
                        ->get();

        $thawing        =   ModelsThawing::where('status', 1)->where('tanggal_request', date('Y-m-d'))->count() ;
        
        return view('admin.pages.thawing.index', compact('gudang', 'thawing'));
    }

    public function requestthawing(Request $request)
    {
        $mulai  = $request->mulai ?? date('Y-m-d');
        $sampai = $request->sampai ?? date('Y-m-d');

        $thawing    =   ModelsThawing::whereBetween('tanggal_request', [$mulai, $sampai])
                        ->orderBy('id', 'DESC')
                        ->withTrashed()
                        ->get();

        $stock      =   Product_gudang::where('jenis_trans', 'masuk')
                        ->whereIn('status', [2])
                        // ->whereBetween('production_date', [$mulai, $sampai])
                        ->get();

        $data_item  =   Item::select('items.*')
                        ->where('items.nama', 'like', '%frozen%')
                        ->where('items.nama', 'not like', '%repack%')
                        ->join('category', 'category.id', '=', 'items.category_id')->where(function($query) {
                            $query->orWhere('category_id', '<=', 20);
                            $query->orWhere('category.slug', 'like', 'ags');
                            $query->orWhere('category.slug', 'like', 'ags%');
                        })
                        ->get() ;

        return view('admin.pages.thawing.requestthawing', compact('mulai', 'sampai', 'thawing', 'stock', 'data_item'));
    }

    public function delete(Request $request)
    {
        $chiller = Chiller::find($request->id);
        if(count($chiller->ambil_chiller)>0){
            $result['status']   =   400;
            $result['msg']      =   'Thawing sudah diambil, tidak bisa dibatalkan';
            return $result ;
        }else{
            $chiller->delete() ;

            try {
                Chiller::recalculate_chiller($chiller->id);
            } catch (\Throwable $th) {
                
            }

            $result['status']   =   200;
            $result['msg']      =   'Thawing dibatalkan';
            return $result ;
        }

    }

    public function update(Request $request)
    {
        if ($request->key == 'batal') {
            $data   =   ModelsThawing::find($request->id) ;
            if ($data) {
                $data->delete() ;

                $result['status']   =   200 ;
                $result['msg']      =   "Request thawing dibatalkan" ;
                return $result ;
            }

            $result['status']   =   400 ;
            $result['msg']      =   "Proses gagal" ;
            return $result ;
        } else {
            $array  =   [];

            for ($x = 0; $x < COUNT($request->item); $x++) {
                $item   =   Item::select('items.*')
                            ->where('items.nama', 'like', '%frozen%')
                            ->where('items.nama', 'not like', '%repack%')
                            ->join('category', 'category.id', '=', 'items.category_id')->where(function($query) {
                                $query->orWhere('category_id', '<=', 20);
                                $query->orWhere('category.slug', 'like', 'ags');
                                $query->orWhere('category.slug', 'like', 'ags%');
                            })
                            ->where('items.id', $request->item[$x])
                            ->first();

                if ($item) {
                    $array[]    =   [
                        'item'              =>  $item->id,
                        'tanggal_request'   =>  $request->tanggal_request[$x],
                        'qty'               =>  $request->qty[$x],
                        'berat'             =>  $request->berat[$x],
                        'keterangan'        =>  $request->keterangan[$x]
                    ];
                }
            }

            $thawing                    =   ModelsThawing::find($request->id);
            $thawing->item              =   json_encode($array);
            $thawing->regu              =   $request->regu;
            $thawing->edited            =   $thawing->edited + 1 ;
            $thawing->save() ;

            return back()->with('status', 1)->with('message', 'Ubah request berhasil') ;
        }
    }
}
