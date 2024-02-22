<?php

namespace App\Http\Controllers\Admin;
use App\Classes\Applib;
use App\Http\Controllers\Controller;
use App\Models\Abf;
use App\Models\Adminedit;
use App\Models\Bahanbaku;
use App\Models\Bom;
use App\Models\BomItem;
use App\Models\Chiller;
use App\Models\Customer;
use App\Models\DataOption;
use App\Models\Freestock;
use App\Models\Gudang;
use App\Models\Item;
use App\Models\Netsuite;
use App\Models\Openbalance;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product_gudang;
use App\Models\Production;
use App\Models\Purchasing;
use App\Models\Thawing;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class AbfController extends Controller
{
    public function index(Request $request)
    {
        if ($request->key == 'open') {
            $item       =   Item::whereIn('category_id', [1, 2, 3, 4, 5, 6, 12, 14, 17, 19])->get();
            $plastik    =   Item::where('category_id', '25')->where('subsidiary', env('NET_SUBSIDIARY', 'EBA'))->where('status', '1')->get();

            return view('admin.pages.abf.open_balance', compact('item', 'plastik'));
        } else
        if ($request->key == 'summary') {
            $clone    =   Product_gudang::with('gudangabf')
                ->where('product_gudang.table_name', 'abf')
                ->select('product_gudang.*')
                // ->join('abf', 'abf.id', '=', 'product_gudang.table_id')
                ->where('product_gudang.berat', '!=', NULL)
                ->where(function ($query) use ($request) {
                    if ($request->tglprod == 'true') {
                        if ($request->mulai || $request->akhir) {
                            $query->whereBetween('abf.tanggal_masuk', [$request->mulai, $request->akhir]);

                        } else {
                            $query->whereDate('product_gudang.production_date', Carbon::now());
                        }
                    } else if ($request->tglprod == 'false') {
                        if ($request->mulai || $request->akhir) {
                            // $query->whereBetween('created_at', [$request->mulai . " 00:00:00", $request->akhir . " 23:59:59"]);
                            $query->whereBetween('product_gudang.production_date', [$request->mulai, $request->akhir]);
                        } else {
                            $query->whereDate('product_gudang.created_at', Carbon::now());
                        }
                    }
                })
                ->where(function ($query) use ($request) {
                    if ($request->gudang != 'null') {
                        $query->where('product_gudang.gudang_id', $request->gudang);
                    }
                })
                ->where(function ($query) use ($request) {
                    if ($request->plastik != 'null') {
                        $query->where('product_gudang.packaging', 'like', '%' . $request->plastik . '%');
                    }
                })
                ->where(function ($query) use ($request) {
                    if ($request->cari) {
                        $query->orWhere('product_gudang.sub_item', 'like', '%' . $request->cari . '%');
                        $query->orWhere('product_gudang.nama', 'like', '%' . $request->cari . '%');
                        $query->orWhere('product_gudang.packaging', 'like', '%' . $request->cari . '%');
                        $query->orWhere('product_gudang.production_date', 'like', '%' . $request->cari . '%');
                        $query->orWhere('product_gudang.tanggal_kemasan', 'like', '%' . $request->cari . '%');
                        $query->orWhere('product_gudang.qty', 'like', '%' . $request->cari . '%');
                        $query->orWhere('product_gudang.berat', 'like', '%' . $request->cari . '%');
                        $query->orWhere('product_gudang.palete', 'like', '%' . $request->cari . '%');
                        $query->orWhere('product_gudang.expired', 'like', '%' . $request->cari . '%');
                        $query->orWhere('product_gudang.stock_type', 'like', '%' . $request->cari . '%');
                    }
                })
                ->where('product_gudang.jenis_trans', 'masuk')
                ->where('product_gudang.status', '!=', 0)
                ->orderByRaw('product_gudang.production_date DESC, product_gudang.id DESC');

            if ($request->get == 'unduh') {
                $summary    =   $clone->get();
                return view('admin.pages.abf.summary_excel', compact('summary'));
            } else {
                $clonesummary    =   clone $clone;
                $cloneqty        =   clone $clone;
                $cloneberat      =   clone $clone;
                $summary         =   $clonesummary->paginate(15);
                $qty             =   $cloneqty->sum('product_gudang.qty');
                $berat           =   $cloneberat->sum('product_gudang.berat_awal');

                $sisa_qty       = $cloneqty->sum('product_gudang.qty_awal');
                $sisa_berat     = $cloneberat->sum('product_gudang.qty_awal');


                return view('admin.pages.abf.summary', compact('summary', 'qty', 'berat', 'sisa_qty', 'sisa_berat'));
            }


            
        } else if ($request->key == 'data-gradul') {
            $clone    =   Product_gudang::where('product_gudang.table_name', 'abf')
                ->select('product_gudang.*')
                // ->join('abf', 'abf.id', '=', 'product_gudang.table_id')
                ->where('product_gudang.berat', '!=', NULL)
                ->whereBetween('product_gudang.production_date', [$request->mulai, $request->akhir])
                ->where(function ($query) use ($request) {
                    if ($request->gudang != 'null') {
                        $query->where('product_gudang.gudang_id', $request->gudang);
                    }
                })
                ->where(function ($query) use ($request) {
                    if ($request->plastik != 'null') {
                        $query->where('product_gudang.packaging', 'like', '%' . $request->plastik . '%');
                    }
                })
                ->where(function ($query) use ($request) {
                    if ($request->cari != "null") {
                        $query->orWhere('product_gudang.sub_item', 'like', '%' . $request->cari . '%');
                        $query->orWhere('product_gudang.nama', 'like', '%' . $request->cari . '%');
                        $query->orWhere('product_gudang.packaging', 'like', '%' . $request->cari . '%');
                        $query->orWhere('product_gudang.production_date', 'like', '%' . $request->cari . '%');
                        $query->orWhere('product_gudang.tanggal_kemasan', 'like', '%' . $request->cari . '%');
                        $query->orWhere('product_gudang.qty', 'like', '%' . $request->cari . '%');
                        $query->orWhere('product_gudang.berat', 'like', '%' . $request->cari . '%');
                        $query->orWhere('product_gudang.palete', 'like', '%' . $request->cari . '%');
                        $query->orWhere('product_gudang.expired', 'like', '%' . $request->cari . '%');
                        $query->orWhere('product_gudang.stock_type', 'like', '%' . $request->cari . '%');
                    }
                })
                ->where('product_gudang.jenis_trans', 'masuk')
                ->where('product_gudang.status', '=', 2)
                ->orderByRaw('product_gudang.production_date DESC, product_gudang.id DESC');

            if ($request->get == 'unduh') {
                $summary    =   $clone->get();
                return view('admin.pages.abf.grading_ulang_excel', compact('summary'));
            } else {
                $clonesummary    =   clone $clone;
                $cloneqty        =   clone $clone;
                $cloneberat      =   clone $clone;
                $summary         =   $clonesummary->paginate(15);
                $qty             =   $cloneqty->sum('product_gudang.qty');
                $berat           =   $cloneberat->sum('product_gudang.berat_awal');

                $sisa_qty       = $cloneqty->sum('product_gudang.qty_awal');
                $sisa_berat     = $cloneberat->sum('product_gudang.qty_awal');

                return view('admin.pages.abf.data-gradingUlang', compact('summary', 'qty', 'berat', 'sisa_qty', 'sisa_berat'));
            }


            
        }else if ($request->key == "grading-ulang"){
            $plastik            =   Item::where('category_id', '25')->where('subsidiary', env('NET_SUBSIDIARY'))->where('status', '1')->get();
            $gudang             =   Gudang::where('subsidiary', env('NET_SUBSIDIARY'))
                                    ->where('kategori', 'Warehouse')
                                    ->get();
            $product            =   Product_gudang::groupBy('nama')->orderBy('nama', 'ASC')->get();
            
            return view('admin.pages.abf.gradingUlang', compact('plastik', 'gudang','product'));
        }else
        if ($request->key == 'summaryGradingUlang') {
            $id = $request->id;
            if($id){
                $clone    =   Product_gudang::where('id', $id)
                ->select('product_gudang.*');
            }else {
                $clone    =   Product_gudang::where('product_gudang.notes', 'grading_ulang')
                ->select('product_gudang.*')
                ->where('product_gudang.berat', '!=', NULL)
                ->where(function ($query) use ($request) {
                    if ($request->gudang != 'null') {
                        $query->where('product_gudang.gudang_id', $request->gudang);
                    }
                })
                ->where(function ($query) use ($request) {
                    if ($request->plastik != 'null') {
                        $query->where('product_gudang.packaging', 'like', '%' . $request->plastik . '%');
                    }
                })
                ->where(function ($query) use ($request) {
                    if ($request->cari != 'null') {
                        $query->orWhere('product_gudang.sub_item', 'like', '%' . $request->cari . '%');
                        $query->orWhere('product_gudang.nama', 'like', '%' . $request->cari . '%');
                        $query->orWhere('product_gudang.packaging', 'like', '%' . $request->cari . '%');
                        // $query->orWhere('product_gudang.production_date', 'like', '%' . $request->cari . '%');
                        $query->orWhere('product_gudang.tanggal_kemasan', 'like', '%' . $request->cari . '%');
                        $query->orWhere('product_gudang.qty', 'like', '%' . $request->cari . '%');
                        $query->orWhere('product_gudang.berat', 'like', '%' . $request->cari . '%');
                        $query->orWhere('product_gudang.palete', 'like', '%' . $request->cari . '%');
                        $query->orWhere('product_gudang.expired', 'like', '%' . $request->cari . '%');
                        $query->orWhere('product_gudang.stock_type', 'like', '%' . $request->cari . '%');
                    }
                })
                ->where('product_gudang.jenis_trans', 'masuk')
                ->whereBetween('product_gudang.production_date', [$request->mulai, $request->akhir])
                ->orderByRaw('product_gudang.production_date DESC, product_gudang.id DESC');
            }
           
                            
        if ($request->get == 'unduh') {
            $summary    =   $clone->get();
            return view('admin.pages.abf.grading_ulang_excel', compact('summary'));
        } else {
            $clonesummary    =   clone $clone;
            $cloneqty        =   clone $clone;
            $cloneberat      =   clone $clone;
            $summary         =   $clonesummary->paginate(15);
            $qty             =   $cloneqty->sum('product_gudang.qty');
            $berat           =   $cloneberat->sum('product_gudang.berat_awal');

            $sisa_qty       =   $cloneqty->sum('product_gudang.qty_awal');
            $sisa_berat     =   $cloneberat->sum('product_gudang.qty_awal');


            return view('admin.pages.abf.summaryGradingUlang', compact('summary', 'qty', 'berat', 'sisa_qty', 'sisa_berat'));
        }


        } else
        if ($request->key == 'editabftimbang') {
            $data = Product_gudang::find($request->id);
            return response()->json([
                'result' => $data
            ]);


        } else 
        if ($request->key == 'loadItemNamePaginate') {

            $itemPaginate =    Adminedit::where('type','item_name')
                                ->where(function($query) use ($request) {
                                    if ($request->subKey == 'searchItemName') {
                                        $query->where('data', 'like', '%'. $request->search . '%');
                                    }
                                })
                                ->orderBy('data', 'asc')->paginate(5);
            
            return view('admin.pages.abf.listItemName', compact('itemPaginate'));


        } else 
        if ($request->key == 'deleteItemName') {

            $dataItemName   = Adminedit::where('type', 'item_name')->where('id', $request->idItemName)->first();

            if ($dataItemName) {

                $cekData        = Product_gudang::where('sub_item', $dataItemName->data)->first();

                if ($cekData) {

                    return response()->json([
                        'msg' => 'Item Name masih digunakan di item lain',
                        'status' => 400,
                        'req' => $request->all()
                    ]);

                } else {

                    $dataItemName->delete();

                    return response()->json([
                        'msg' => 'Berhasil delete Item Name',
                        'status' => 200,
                        'req' => $request->all()
                    ]);
                }

                return response()->json([
                    'msg' => 'Item Name tidak ditemukan',
                    'status' => 400,
                    'req' => $request->all()
                ]);

            }



        } else 
        if ($request->key == 'loadPlastikGroupPaginate') {
            $plastikGroupPaginate =    Adminedit::where('type','plastik_group')->paginate(5);
            return view('admin.pages.abf.listPlastikGroup', compact('plastikGroupPaginate'));


        } else 
        if ($request->key == 'deletePlastikGroup') {
            Adminedit::where('type', 'plastik_group')->where('id', $request->idPlastikGroup)->delete();

            return response()->json([
                'msg' => 'Berhasil delete Plastik Group',
                'status' => 200,
                'req' => $request->all()
            ]);
            


        } else 
        if ($request->key == 'warehouseGrading') {
            // dd($request->all());
            $getItemWarehouse       = Product_gudang::findOrFail($request->id);

            $getAllItem             = Item::where('nama', 'like', '%FROZEN%')->get();

            // $item_list  = Item::select('id','nama')->where('category_id',$item->category_id)->get();


            $sub_item   = Adminedit::where('type','item_name')->get();
            $plastik    = Item::where('category_id',25)->where('subsidiary', env('NET_SUBSIDIARY', 'EBA'))->where('status', '1')->get();
            $karung     = Item::where('subsidiary', 'like', '%'.Session::get('subsidiary').'%')
                                                ->where(function ($item) {
                                                    $item->where('nama', 'like', 'KARUNG%')->orWhere('nama', 'like', 'KARTON%');
                                                    $item->orWhere('nama', 'like', 'KARUNG%')->orWhere('nama', 'like', 'KARUNG%');
                                                })
                                                ->get();

            $warehouse  =   Gudang::where('kategori', 'warehouse')
                                    ->where('subsidiary', Session::get('subsidiary'))
                                    ->where('code', 'NOT LIKE', "%abf%")
                                    ->where('status', 1)
                                    ->get();

            $customer           =   Customer::all();

            $item_name          =    Adminedit::where('type','item_name')->get();
            $plastikGroup       =    Adminedit::where('type','plastik_group')->get();

            return view('admin.pages.abf.warehouseGrading',compact('getItemWarehouse','sub_item','plastik','karung', 'getAllItem', 'warehouse', 'customer', 'item_name', 'plastikGroup'));            

        } else {
            $tanggal            =   $request->tanggal ?? date('Y-m-d');
            $tanggal_akhir      =   $request->tanggal_akhir ?? date('Y-m-d');
            $pending            =   Product_gudang::where('table_name', 'abf')
                                    ->where('berat', '!=', NULL)
                                    ->where('status', 0)
                                    ->orderBy('id', 'DESC')
                                    ->get();

            $plastik            =   Item::where('category_id', '25')->where('subsidiary', env('NET_SUBSIDIARY', 'EBA'))->where('status', '1')->get();
            $gudang             =   Gudang::where('subsidiary_id', env('NET_SUBSIDIARY_ID', '2'))
                                    ->where('kategori', 'Warehouse')
                                    ->get();

                
            $thawing             =   Thawing::where('status', 1)->where('tanggal_request', date('Y-m-d'))->count() ;


            $summaryGradingUlang =   Product_gudang::where('notes', 'grading_ulang')->get();

            return view('admin.pages.abf.index', compact('tanggal', 'pending', 'gudang', 'tanggal_akhir', 'plastik', 'thawing', 'summaryGradingUlang'));
        }
    }

    public function store(Request $request)
    {

        // dd($request->all());
        if ($request->key == 'open') {

            if (!$request->tanggal) {
                $result['status']   =   400;
                $result['msg']      =   'Tanggal belum di pilih';
                return $result;
            }

            if (!$request->item) {
                $result['status']   =   400;
                $result['msg']      =   'Item belum di pilih';
                return $result;
            }

            if ($request->berat < 0) {
                $result['status']   =   400;
                $result['msg']      =   'Berat wajib diisikan';
                return $result;
            }

            if ($request->item_plastik) {
                $plastik    =   Item::select('nama')
                    ->where('id', $request->item_plastik)
                    ->where('category_id', 25)
                    ->where('subsidiary', env('NET_SUBSIDIARY', 'EBA'))
                    ->first();

                if (!$plastik) {
                    $result['status']   =   400;
                    $result['msg']      =   'Plastik tidak ditemukan';
                    return $result;
                }
            }


            $item   =   Item::find($request->item);

            if (!$item) {
                $result['status']   =   400;
                $result['msg']      =   'Item tidak ditemukan';
                return $result;
            }

            DB::beginTransaction();

            $open               =   new Openbalance;
            $open->user_id      =   Auth::user()->id;
            $open->gudang       =   'abf';
            $open->item_id      =   $request->item;
            $open->tipe_item    =   'hasil-produksi';
            $open->tanggal      =   $request->tanggal;
            $open->qty          =   $request->qty;
            $open->berat        =   $request->berat;
            if (!$open->save()) {
                DB::rollBack();
                $result['status']   =   400;
                $result['msg']      =   "Proses gagal";
                return $result;
            }

            $abf                    =   new Abf;
            $abf->table_name        =   'openbalance';
            $abf->table_id          =   $open->id;
            $abf->asal_tujuan       =   'abf';
            $abf->tanggal_masuk     =   $request->tanggal;
            $abf->item_id           =   $request->item;
            $abf->item_id_lama      =   $item->id;
            $abf->item_name         =   $item->nama;
            $abf->jenis             =   'masuk';
            $abf->type              =   $open->tipe_item;
            $abf->qty_awal          =   $open->qty;
            $abf->packaging         =   $plastik->nama ?? NULL;
            $abf->berat_awal        =   $open->berat;
            $abf->qty_item          =   $open->qty;
            $abf->berat_item        =   $open->berat;
            $abf->status            =   1;
            if (!$abf->save()) {
                DB::rollBack();
                $result['status']   =   400;
                $result['msg']      =   "Proses gagal";
                return $result;
            }

            DB::commit();

            $result['status']   =   200;
            $result['msg']      =   'Input berhasil';
            return $result;
        } elseif ($request->key == 'updateabftimbang') {
            DB::beginTransaction();
            $data                   =   Product_gudang::find($request->id);
            $data->qty              = $request->qty;
            $data->berat            = $request->berat;
            $data->production_code  = $request->production_code;
            if (!$data->save()) {
                DB::rollBack();
            }
            DB::commit();

            return response()->json([
                'status' => 200,
                'msg' => 'Update berhasil'
            ]);
        } elseif ($request->key == 'updatebongkarabf') {
            // dd($request->all());
            // DB::beginTransaction() ;
            $abf                = Abf::find($request->id);
            $abf->qty_item      = $request->qty_item;
            $abf->berat_item    = $request->berat_item;
            $abf->packaging     = $request->packaging;
            $abf->save();
            // if(!$abf->save()){
            //     DB::rollBack();
            // }
            // DB::commit() ;
            return response()->json([
                'status' => 200,
                'msg' => 'Update Bongkar CS - ABF berhasil'
            ]);

            
        } else if ($request->key == 'storeWarehouseGrading') {
            // dd($request->all()); 
            $gudang_lama = Product_gudang::find($request->id_product_gudang);
            // dd($id);


            DB::beginTransaction();
            if ($gudang_lama) {
                
                $totalQty   = 0; 
                $totalBerat = 0;
                foreach ($request->item as $no => $row) {

                    // dd( $request->input('tujuan'.$no)[0]);

                    $getItem               = Item::where('id',$request->item[$no])->first();


                    $data = array (
                        'product_id'        => $request->item[$no],
                        'nama'              => $getItem->nama,
                        'kategori'          => NULL,
                        'table_name'        => 'product_gudang',
                        'table_id'          => $request->id_product_gudang,
                        'sub_item'          => Adminedit::where('type', 'item_name')->where('id',$request->subitem[$no])->first()->data ?? 'NONE',
                        'qty_awal'          => $request->qty[$no],
                        'berat_awal'        => $request->berat[$no],
                        'qty'               => $request->qty[$no],
                        'berat'             => $request->berat[$no],
                        'packaging'         => $request->packaging[$no] ?? NULL,
                        'plastik_group'     => $request->plastik[$no] ?? NULL,
                        'parting'           => $request->parting[$no] ?? 0,
                        'karung'            => $request->karung[$no],
                        'karung_qty'        => $request->karung_qty[$no],
                        'karung_isi'        => $request->karung_isi[$no],
                        'customer_id'       => $request->konsumen[$no],
                        'tanggal_kemasan'   => $request->tanggal_kemasan[$no] ?? NULL,
                        'production_date'   => $request->tanggal_input[$no] ?? date('Y-m-d'),
                        'expired'           => $request->expired[$no] ?? $request->expired_custom[$no],
                        'asal_abf'          => $request->input('asal_abf'.$no)[0] ?? NULL,
                        'gudang_id'         => $request->input('tujuan'.$no)[0],
                        'gudang_id_keluar'  => NULL,
                        'type'              => $gudang_lama->type,
                        'stock_type'        => $request->stock[$no] ?? $gudang_lama->stock_type,
                        'jenis_trans'       => 'masuk',
                        'status'            => 2,
                        'created_at'        => Carbon::now(),
                        'updated_at'        => Carbon::now(),
                        'notes'             => 'grading_ulang'
                    );
                    
                    
                    $idWarehouseGrading     = Product_gudang::insertGetId($data);
        
                    $totalQty               += $request->qty[$no];
                    $totalBerat             += $request->berat[$no];


                    // BUAT OUTBOUND / GUDANG KELUAR
                    $gudang_keluar                       =   new Product_gudang;
                    $gudang_keluar->product_id           =   $gudang_lama->product_id;
                    $gudang_keluar->nama                 =   $gudang_lama->nama;
                    $gudang_keluar->sub_item             =   $gudang_lama->sub_item;
                    $gudang_keluar->table_id             =   $gudang_lama->table_id;
                    $gudang_keluar->table_name           =   $gudang_lama->table_name;
                    $gudang_keluar->order_id             =   $gudang_lama->order_id;
                    $gudang_keluar->order_item_id        =   $gudang_lama->order_item_id;
                    $gudang_keluar->parting              =   $gudang_lama->parting ?? 0;
                    $gudang_keluar->qty_awal             =   $request->qty[$no];
                    $gudang_keluar->berat_awal           =   $request->berat[$no];
                    $gudang_keluar->qty                  =   $request->qty[$no];
                    $gudang_keluar->berat                =   $request->berat[$no];
                    $gudang_keluar->packaging            =   $gudang_lama->packaging;
                    $gudang_keluar->plastik_group        =   Item::plastik_group($gudang_lama->packaging);
                    $gudang_keluar->production_date      =   date('Y-m-d');
                    $gudang_keluar->type                 =   'grading_ulang';
                    // $gudang_keluar->request_thawing      =   $row->id ;
                    $gudang_keluar->gudang_id            =   $gudang_lama->gudang_id;
                    $gudang_keluar->stock_type           =   $gudang_lama->stock_type;
                    $gudang_keluar->jenis_trans          =   'keluar';
                    $gudang_keluar->gudang_id_keluar     =   $gudang_lama->id;
                    $gudang_keluar->status               =   4;
                    $gudang_keluar->save();


                    // END OUTBOUND

                    // ------------------------------------------------------------------------------------------------
                    // KIRIMAN NS 
                    // BUAT DATA KIRIMAN THAWING DULU

                    $nama_gudang_bb         = env('NET_SUBSIDIARY', 'CGL')." - Chiller Bahan Baku";
                    $nama_gudang_fg         = env('NET_SUBSIDIARY', 'CGL')." - Chiller Finished Good";
                    // dd($idWarehouseGrading);
                    if($request->berat[$no] > 0){
                        
                        $code                   =   'TW-' . $idWarehouseGrading ;
                        $gudang                 =   Product_gudang::find($idWarehouseGrading);
                        $item_frozen            =   Item::find($request->item[$no]);
                        $item_finish            =   Item::where('nama', str_replace(' FROZEN', '', ($getItem->nama)))->first();
        
                        try {
                            //code...
        
                            $nama_gudang_cs     =   env('NET_SUBSIDIARY', 'CGL')." - Cold Storage";
                            $gudang_cs          =   Gudang::where('code', $nama_gudang_cs)->first();
        
                            if ($gudang_cs) {
                                $id_location    =   $gudang_cs->netsuite_internal_id;
                                $location       =   $gudang_cs->code;
                                $from           =   $id_location;

                            } else {

                                $id_location    =   Gudang::find($gudang->gudang_id)->netsuite_internal_id;
                                $location       =   Gudang::find($gudang->gudang_id)->code;
                                $from           =   $id_location;
                            }
        
                            $label          =   'wo-4-thawing';
        
                            try {
                                //code...
        
                                $bom_kategori = Item::find($gudang->product_id);
                                if ($bom_kategori) {
                                    if ($bom_kategori->category_id=="8") {

                                        $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL')." - THAWING AYAM PARTING BROILER FROZEN")
                                        ->first();
        
                                    } elseif ($bom_kategori->category_id=="9") {
                                        $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL')." - THAWING AYAM PARTING MARINASI BROILER FROZEN")
                                        ->first();
        
                                    } elseif ($bom_kategori->category_id=="7") {
                                        $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL')." - THAWING AYAM KARKAS BROILER FROZEN")
                                        ->first();
        
                                    } elseif ($bom_kategori->category_id=="11") {
                                        $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL')." - THAWING BONELESS BROILER FROZEN")
                                        ->first();
        
                                    } elseif ($bom_kategori->category_id=="10") {
                                        $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL')." - THAWING EVIS FROZEN")
                                        ->first();
        
                                    } else {
                                        $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL').' - THAWING AYAM KARKAS BROILER FROZEN')
                                        ->first();
        
                                    }
        
                                } else {
                                    $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL').' - THAWING AYAM KARKAS BROILER FROZEN')
                                    ->first();
        
                                }
        
                                $bom_id         =   $bom->id;
                                $id_assembly    =   $bom->netsuite_internal_id;
                                $item_assembly  =   $bom->bom_name ;

                            } catch (\Throwable $th) {
                                //throw $th;
                                $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL').' - THAWING AYAM KARKAS BROILER FROZEN')->first();
                                $bom_id         =   $bom->id;
                                $id_assembly    =   $bom->netsuite_internal_id;
                                $item_assembly  =   $bom->bom_name ;
                
                            }

                            $getItemGudangLama      =   Item::where('id', $gudang_lama->product_id)->first();
        
                            $component      =   [[
                                "type"              =>  "Component",
                                "internal_id_item"  =>  (string)$getItemGudangLama->netsuite_internal_id,
                                "item"              =>  (string)$getItemGudangLama->sku,
                                "description"       =>  (string)$getItemGudangLama->nama,
                                "qty"               =>  (string)$request->berat[$no],
                            ]];


                            $label = $gudang->gudangabf != NULL ? $gudang->gudangabf->tujuan : $gudang->type;
                            if ($item_finish->category_id == '1') {

                                
                                $label_ti   =   "ti_storage" . $gudang->type . "_chillerbb-thawing";
                                $to         =   Gudang::gudang_netid($nama_gudang_bb);


                                if (substr($item_finish->sku, 0, 5) == "12111") {
                                    $transfer   =   [
                                        [
                                            "internal_id_item"  =>  (string)Item::item_sku("1100000001")->netsuite_internal_id ,
                                            "item"              =>  "1100000001",
                                            "qty_to_transfer"   =>  (string)$request->berat[$no]
                                        ]
                                    ];

                                    $finished_good  =   [[
                                        "type"              =>  "Finished Goods",
                                        "internal_id_item"  =>  (string)Item::item_sku("1100000001")->netsuite_internal_id ,
                                        "item"              =>  "1100000001",
                                        "description"       =>  "AYAM KARKAS BROILER (RM)",
                                        "qty"               =>  (string)$request->berat[$no]
                                    ]];
                                //check ayam MEMAR
                                } elseif (substr($item_finish->sku, 0, 5) == "12113") {
                                    $transfer   =   [
                                        [
                                            "internal_id_item"  =>  (string)Item::item_sku("1100000003")->netsuite_internal_id ,
                                            "item"              =>  "1100000003",
                                            "qty_to_transfer"   => (string)$request->berat[$no]
                                        ]
                                    ];

                                    $finished_good  =   [[
                                        "type"              =>  "Finished Goods",
                                        "internal_id_item"  =>  (string)Item::item_sku("1100000003")->netsuite_internal_id ,
                                        "item"              =>  "1100000003",
                                        "description"       =>  "AYAM MEMAR (RM)",
                                        "qty"               =>  (string)$request->berat[$no]
                                    ]];

                                } elseif (substr($item_finish->sku, 0, 5) == "12112") {
                                    $transfer   =   [
                                        [
                                            "internal_id_item"  =>  (string)Item::item_sku("1100000002")->netsuite_internal_id ,
                                            "item"              =>  "1100000002",
                                            "qty_to_transfer"   => (string)$request->berat[$no]
                                        ]
                                    ];

                                    $finished_good  =   [[
                                        "type"              =>  "Finished Goods",
                                        "internal_id_item"  =>  (string)Item::item_sku("1100000002")->netsuite_internal_id ,
                                        "item"              =>  "1100000002",
                                        "description"       =>  "AYAM UTUH (RM)",
                                        "qty"               =>  (string)$request->berat[$no]
                                    ]];



                                // check ayam PEJANTAN

                            } elseif (substr($item_finish->sku, 0, 4) == "1213" || substr($item_finish->sku, 0, 4) == "1223") {
                                // $jenis = "pejantan";
                                $transfer   =   [
                                    [
                                        "internal_id_item"  =>  (string)Item::item_sku("1100000005")->netsuite_internal_id ,
                                        "item"              =>  "1100000005",
                                        "qty_to_transfer"   => (string)$request->berat[$no]
                                    ]
                                ];

                                $finished_good  =   [[
                                    "type"              =>  "Finished Goods",
                                    "internal_id_item"  =>  (string)Item::item_sku("1100000005")->netsuite_internal_id ,
                                    "item"              =>  "1100000005",
                                    "description"       =>  "AYAM PEJANTAN (RM)",
                                    "qty"               =>  (string)$request->berat[$no]
                                ]];

                            // Check ayam parent
                            } elseif (substr($item_finish->sku, 0, 4) == "1214" || substr($item_finish->sku, 0, 4) == "1224") {
                                // $jenis = "parent";
                                $transfer   =   [
                                    [
                                        "internal_id_item"  =>  (string)Item::item_sku("1100000009")->netsuite_internal_id ,
                                        "item"              =>  "1100000009",
                                        "qty_to_transfer"   => (string)$request->berat[$no]
                                    ]
                                ];

                                $finished_good  =   [[
                                    "type"              =>  "Finished Goods",
                                    "internal_id_item"  =>  (string)Item::item_sku("1100000009")->netsuite_internal_id ,
                                    "item"              =>  "1100000009",
                                    "description"       =>  "AYAM PARENT (RM)",
                                    "qty"               => (string)$request->berat[$no]
                                ]];

                            // Check ayam kampung
                            } elseif (substr($item_finish->sku, 0, 4) == "1212" || substr($item_finish->sku, 0, 4) == "1222") {
                                // $jenis = "kampung";
                                $transfer   =   [
                                    [
                                        "internal_id_item"  =>  (string)Item::item_sku("1100000004")->netsuite_internal_id ,
                                        "item"              =>  "1100000004",
                                        "qty_to_transfer"   => (string)$request->berat[$no]
                                    ]
                                ];

                                $finished_good  =   [[
                                    "type"              =>  "Finished Goods",
                                    "internal_id_item"  =>  (string)Item::item_sku("1100000004")->netsuite_internal_id ,
                                    "item"              =>  "1100000004",
                                    "description"       =>  "AYAM KAMPUNG (RM)",
                                    "qty"               => (string)$request->berat[$no]
                                ]];

                            } elseif (substr($item_finish->sku, 0, 5) == "12115") {

                                $transfer   =   [
                                    [
                                        "internal_id_item"  =>  (string)Item::item_sku("1100000007")->netsuite_internal_id ,
                                        "item"              =>  "1100000007",
                                        "qty_to_transfer"   => (string)$request->berat[$no]
                                    ]
                                ];

                                $finished_good  =   [[
                                    "type"              =>  "Finished Goods",
                                    "internal_id_item"  =>  (string)Item::item_sku("1100000007")->netsuite_internal_id ,
                                    "item"              =>  "1100000007",
                                    "description"       =>  "AYAM PARTING (M) (RM)",
                                    "qty"               => (string)$request->berat[$no]
                                ]];
    
    
                            } elseif (substr($item_finish->sku, 0, 5) == "12114") {

                                $transfer   =   [
                                    [
                                        "internal_id_item"  =>  (string)Item::item_sku("1100000006")->netsuite_internal_id ,
                                        "item"              =>  "1100000006",
                                        "qty_to_transfer"   => (string)$request->berat[$no]
                                    ]
                                ];
    
                                $finished_good  =   [[
                                    "type"              =>  "Finished Goods",
                                    "internal_id_item"  =>  (string)Item::item_sku("1100000006")->netsuite_internal_id ,
                                    "item"              =>  "1100000006",
                                    "description"       =>  "AYAM PARTING (RM)",
                                    "qty"               => (string)$request->berat[$no]
                                ]];

                                }

                            
                            
                            } else {

                                $label_ti   =   "ti_storage" .  $label . "_chillerfg-thawing";
                                $to         =   Gudang::gudang_netid($nama_gudang_fg);
                                $transfer   =   [
                                    [
                                        "internal_id_item"  =>  (string)Item::item_sku($item_finish->sku)->netsuite_internal_id ,
                                        "item"              =>  $item_finish->sku,
                                        "qty_to_transfer"   => (string)$request->berat[$no]
                                    ]
                                ];

                                $finished_good  =   [[
                                    "type"              =>  "Finished Goods",
                                    "internal_id_item"  =>  (string)Item::item_sku($item_finish->sku)->netsuite_internal_id ,
                                    "item"              =>  $item_finish->sku,
                                    "description"       =>  $item_finish->nama,
                                    "qty"               => (string)$request->berat[$no]
                                ]];

                            }

                            // MASUK CHILLER
        
                            // $finished_good  =   [[
                            //     "type"              =>  "Finished Goods",
                            //     "internal_id_item"  =>  (string)Item::item_sku($item_finish->sku)->netsuite_internal_id ,
                            //     "item"              =>  $item_finish->sku,
                            //     "description"       =>  $item_finish->nama,
                            //     "qty"               =>  (string)$request->berat[$no]
                            // ]];
        
        
                            $produksi       =   array_merge($component, $finished_good);
        
                            $nama_tabel     =   'reGrading';
                            $id_tabel       =   $idWarehouseGrading;
                            $label          =   'wo-4-thawing';
        
                            $wo             =   Netsuite::work_order_doc($nama_tabel, $id_tabel, $label, $id_assembly, $item_assembly, $id_location, $location, $produksi, null, $request->tanggal_input[$no], $code);
        
                            $label          =   'wo-4-build-thawing';
                            $total          =   $request->berat[$no];
                            $wop            =   Netsuite::wo_build_doc($nama_tabel, $id_tabel, $label, $id_assembly, $item_assembly, $id_location, $location, $total, $produksi, $wo->id, $request->tanggal_input[$no], $code);
        
                            if ($item_finish->category_id == 1) {
                                // IT(CS-BB)
                                $tiCStoBB       =   Netsuite::transfer_inventory_doc($nama_tabel, $id_tabel, $label_ti, $id_location, $location, $from, $to, $transfer, $wop->id, $request->tanggal_input[$no] , $code);
                            }
        
                            // DB::Commit();
        
                        } catch (\Throwable $th) {
                            //throw $th;
        
                            DB::rollBack();
                            return "FAILED ".$th->getMessage()."<br>";
                        }
        
                    } else {
                        DB::rollBack() ;
                        return "Berat kosong";
                    }

                    // END KIRIMAN THAWING


                    // BUAT DATA KIRIMAN WO 2 FROZEN
                    if ($item_finish->category_id == '1') {
                        $regu               = 'frozen';
                        $bom                =   Bom::where('bom_name', env("NET_SUBSIDIARY", "CGL") . " - AYAM KARKAS FROZEN")->first();
                        $id_assembly        =  $bom->netsuite_internal_id;
                        $nama_assembly      =  $bom->bom_name ;
    
                        $code               = 'wo-2-'.$regu.'-'.time();
    
                        $nama_gudang_wip    = env('NET_SUBSIDIARY', 'CGL')." - Storage Produksi (WIP)";
    
                        // ===================    TRANSFER INVENTORY IN WIP    ===================
    
                        $nama_tabel         =   "reGrading";
                        $id_tabel           =   $idWarehouseGrading;
    
                        $location           =   $nama_gudang_bb;
                        $id_location        =   Gudang::gudang_netid($location);
                        $from               =   $id_location;
                        $to                 =   Gudang::gudang_netid($nama_gudang_wip);
    
                        // untuk data kepala regu
                        $arr_trf            =   [];
                        $arr_bb             =   [];
                        $berat_rm           =   0;
                        $berat_memar        =   0;
                        $berat_parent       =   0;
                        $berat_pejantan     =   0;
                        $berat_kampung      =   0;
                        $berat_fg           =   0;
                        $arr_trf_fg         =   [];
                        $trans              =   [];
                        $bahanbaku          =   [];
    
                        // ARRAY BB
                        $bb_gabung = [];
    
                        // $bb_gabung[]  =   [
                        //     "type"              =>  "Component",
                        //     "internal_id_item"  =>  (string)$item_finish->netsuite_internal_id,
                        //     "item"              =>  $item_finish->sku,
                        //     "description"       =>  $item_finish->nama,
                        //     "qty"               =>  $request->berat[$no]
                        // ];

                    }
                    if ($item_finish->category_id == '1') {

                        if (substr($item_finish->sku, 0, 5) == "12111" || substr($item_finish->sku, 0, 5) == "12112") {

                            $bb_gabung  =   [[
                                "type"              =>  "Component",
                                "internal_id_item"  =>  (string)Item::item_sku("1100000001")->netsuite_internal_id ,
                                "item"              =>  "1100000001",
                                "description"       =>  "AYAM KARKAS BROILER (RM)",
                                "qty"               =>  (string)$request->berat[$no]
                            ]];

                        //check ayam MEMAR
                        } elseif (substr($item_finish->sku, 0, 5) == "12113") {

                            $bb_gabung  =   [[
                                "type"              =>  "Component",
                                "internal_id_item"  =>  (string)Item::item_sku("1100000003")->netsuite_internal_id ,
                                "item"              =>  "1100000003",
                                "description"       =>  "AYAM MEMAR (RM)",
                                "qty"               =>  (string)$request->berat[$no]
                            ]];

                            // check ayam PEJANTAN

                        } elseif (substr($item_finish->sku, 0, 4) == "1213" || substr($item_finish->sku, 0, 4) == "1223") {
                            // $jenis = "pejantan";

                            $bb_gabung  =   [[
                                "type"              =>  "Component",
                                "internal_id_item"  =>  (string)Item::item_sku("1100000005")->netsuite_internal_id ,
                                "item"              =>  "1100000005",
                                "description"       =>  "AYAM PEJANTAN (RM)",
                                "qty"               =>  (string)$request->berat[$no]
                            ]];

                        // Check ayam parent
                        } elseif (substr($item_finish->sku, 0, 4) == "1214" || substr($item_finish->sku, 0, 4) == "1224") {
                            // $jenis = "parent";

                            $bb_gabung  =   [[
                                "type"              =>  "Component",
                                "internal_id_item"  =>  (string)Item::item_sku("1100000009")->netsuite_internal_id ,
                                "item"              =>  "1100000009",
                                "description"       =>  "AYAM PARENT (RM)",
                                "qty"               => (string)$request->berat[$no]
                            ]];

                        // Check ayam kampung
                        } elseif (substr($item_finish->sku, 0, 4) == "1212" || substr($item_finish->sku, 0, 4) == "1222") {
                            // $jenis = "kampung";

                            $bb_gabung  =   [[
                                "type"              =>  "Component",
                                "internal_id_item"  =>  (string)Item::item_sku("1100000004")->netsuite_internal_id ,
                                "item"              =>  "1100000004",
                                "description"       =>  "AYAM KAMPUNG (RM)",
                                "qty"               => (string)$request->berat[$no]
                            ]];
                        
                        } elseif (substr($item_finish->sku, 0, 5) == "12115") {

                            $bb_gabung  =   [[
                                "type"              =>  "Component",
                                "internal_id_item"  =>  (string)Item::item_sku("1100000007")->netsuite_internal_id ,
                                "item"              =>  "1100000007",
                                "description"       =>  "AYAM PARTING (M) (RM)",
                                "qty"               => (string)$request->berat[$no]
                            ]];


                        } elseif (substr($item_finish->sku, 0, 5) == "12114") {

                            $bb_gabung  =   [[
                                "type"              =>  "Component",
                                "internal_id_item"  =>  (string)Item::item_sku("1100000006")->netsuite_internal_id ,
                                "item"              =>  "1100000006",
                                "description"       =>  "AYAM PARTING (RM)",
                                "qty"               => (string)$request->berat[$no]
                            ]];



                        } else {

                            $finished_good  =   [[
                                "type"              =>  "Component",
                                "internal_id_item"  =>  (string)Item::item_sku($item_finish->sku)->netsuite_internal_id ,
                                "item"              =>  $item_finish->sku,
                                "description"       =>  $item_finish->nama,
                                "qty"               => (string)$request->berat[$no]
                            ]];



                        }

                        $bahanbaku  =   array_merge($bb_gabung, $arr_bb);
    
    
                        $label          =   "ti_bb_prod_".$regu;
    
                        // Check ayam KARKAS dan ayam utuh
                        if (substr($item_finish->sku, 0, 5) == "12111" || substr($item_finish->sku, 0, 5) == "12112") {
    
                            $berat_rm           +=  $request->berat[$no];
                        //check ayam MEMAR
                        } elseif (substr($item_finish->sku, 0, 5) == "12113") {
                            $berat_memar        +=  $request->berat[$no];
                        // check ayam KAMPUNG
                        } elseif (substr($item_finish->sku, 0, 5) == "12122" || substr($item_finish->sku, 0, 5) == "12121") {
                            $berat_kampung      +=  $request->berat[$no];
                        // check ayam PEJANTAN
                        } elseif (substr($item_finish->sku, 0, 5) == "12131" || substr($item_finish->sku, 0, 5) == "12132") {
                            $berat_pejantan     +=  $request->berat[$no];
                        // check ayam PARENT
                        } elseif (substr($item_finish->sku, 0, 5) == "12142" || substr($item_finish->sku, 0, 5) == "12141") {
                            $berat_parent       +=  $request->berat[$no];
                        }
    
                               //Konversi Component to RM
                        if($berat_rm!="0" || $berat_memar!="0" || $berat_kampung!="0" || $berat_pejantan!="0" || $berat_parent!="0"){
    
                            $arr_gabung = [];
                            if($berat_rm!="0"){
                                $arr_gabung[] = [
                                    "internal_id_item"  =>  (string)Item::item_sku("1100000001")->netsuite_internal_id ,
                                    "item"              =>  "1100000001" ,
                                    "qty_to_transfer"   =>  "$berat_rm"
                                ];
                            }
    
                            if($berat_memar!="0"){
                                $arr_gabung[] = [
                                    "internal_id_item"  =>  (string)Item::item_sku("1100000003")->netsuite_internal_id ,
                                    "item"              =>  "1100000003" ,
                                    "qty_to_transfer"   =>  "$berat_memar"
                                ];
                            }
    
                            if($berat_kampung!="0"){
                                $arr_gabung[] = [
                                    "internal_id_item"  =>  (string)Item::item_sku("1100000004")->netsuite_internal_id ,
                                    "item"              =>  "1100000004" ,
                                    "qty_to_transfer"   =>  "$berat_kampung"
                                ];
                            }
    
                            if($berat_pejantan!="0"){
                                $arr_gabung[] = [
                                    "internal_id_item"  =>  (string)Item::item_sku("1100000005")->netsuite_internal_id ,
                                    "item"              =>  "1100000005" ,
                                    "qty_to_transfer"   =>  "$berat_pejantan"
                                ];
                            }
    
                            if($berat_parent!="0"){
                                $arr_gabung[] = [
                                    "internal_id_item"  =>  (string)Item::item_sku("1100000009")->netsuite_internal_id ,
                                    "item"              =>  "1100000009" ,
                                    "qty_to_transfer"   =>  "$berat_parent"
                                ];
                            }
    
                            $trans  =   array_merge($arr_gabung, $arr_trf) ;
    
                        }
    
                        // IT(BB-WIP)
                        $ti = Netsuite::transfer_inventory_doc($nama_tabel, $id_tabel, $label, $id_location, $location, $from, $to, $trans, $tiCStoBB->id, $request->tanggal_input[$no], $code);
    
                        // ARRAY FINISHED GOODS
                        $data_produksi  =   [];
    
                        if(Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL').' - EVIS')->first()->netsuite_internal_id == $id_assembly || Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL').' - NON KARKAS - BONELESS BROILER')->first()->netsuite_internal_id == $id_assembly){
    
                            $total    +=  $request->berat[$no];
                            $data_produksi[]    =   [
                                "type"              =>  "Finished Goods",
                                "internal_id_item"  =>  (string)Item::item_sku($getItem->sku)->netsuite_internal_id,
                                "item"              =>  (string)$getItem->sku,
                                "description"       =>  (string)Item::item_sku($getItem->sku)->nama,
                                "qty"               =>  (string)$request->berat[$no],
                            ];
    
                        } else {
    
                            $bom_item = BomItem::where('sku', $getItem->sku)->where('bom_id', $bom->id)->first();
                            if($bom_item){
                                if($bom_item->kategori=="By Product"){
                                    $total;
                                }else{
                                    $total    +=  $request->berat[$no];
                                }
                            }else{
                                $total    +=  $request->berat[$no];
                            }
    
                            $item_cat = Item::find($getItem->id);
    
                            $type = (($item_cat->category_id == 4) OR ($item_cat->category_id == 6) OR ($item_cat->category_id == 10) OR ($item_cat->category_id == 16)) ? "By Product" : "Finished Goods";
                            
                            if($bom_item){
                                $type = $bom_item->kategori;
                            }
    
                            $data_produksi[]    =   [
                                "type"              =>  $type,
                                "internal_id_item"  =>  (string)Item::item_sku($getItem->sku)->netsuite_internal_id,
                                "item"              =>  (string)$getItem->sku,
                                "description"       =>  (string)Item::item_sku($getItem->sku)->nama,
                                "qty"               =>  (string)$request->berat[$no],
                            ];
    
                        }
    
                        $produksi       =   array_merge($bahanbaku, $data_produksi);
    
                        $label          =   "wo-2-".$regu;
                        $wo             =   Netsuite::work_order_doc($nama_tabel, $id_tabel, $label, $id_assembly, $nama_assembly, $to, $nama_gudang_wip, $produksi, $ti->id, $request->tanggal_input[$no], $code);
    
                        // ===================    WO - 2 - BUILD    ===================
    
                        $label          =   "wo-2-build-".$regu;
                        $wob            =   Netsuite::wo_build_doc($nama_tabel, $id_tabel, $label, $id_assembly, $nama_assembly, $to, $nama_gudang_wip, $total, $produksi, $wo->id, $request->tanggal_input[$no], $code);
                        

                        $nama_tabel     =   "reGrading";
                        $id_tabel       =    $idWarehouseGrading;
        

                        // WIP TO FG
                        $from           =   Gudang::gudang_netid($nama_gudang_wip);
                        $label          =   "ti_prod_fg_".$regu;
                        $to             =   Gudang::gudang_netid($nama_gudang_fg);

                        $transfer_wiptofg[]    =   [
                            "internal_id_item"  =>  (string)Item::item_sku($getItem->sku)->netsuite_internal_id,
                            "item"              =>  (string)$getItem->sku,
                            "qty_to_transfer"   =>  (string)$gudang->berat_awal,
                        ];

                        $tiWIPtoFG      =   Netsuite::transfer_inventory_doc($nama_tabel, $id_tabel, $label, $from, $nama_gudang_wip, $from, $to, $transfer_wiptofg, $wob->id, $request->tanggal_input[$no], $code);

                        // END WO 2 


                        // TI / WO 3

                        // ===================    TRANSFER INVENTORY IN FINISHED GOOD TO ABF    ===================


                        // $transfer_awalCat1[] =   [
                        //     "internal_id_item"  =>  (string)Item::item_sku($getItem->sku)->netsuite_internal_id,
                        //     "item"              =>  (string)$getItem->sku,
                        //     "qty_to_transfer"   =>  (string)$request->berat[$no]
                        // ];

                        // $transfer_akhir[]    =   [
                        //     "internal_id_item"  =>  (string)Item::item_sku($getItem->sku)->netsuite_internal_id,
                        //     "item"              =>  (string)$getItem->sku,
                        //     "qty_to_transfer"   =>  (string)$request->berat[$no],
                        // ];

                        // $nama_tabel          =   "reGrading";
                        // $id_tabel            =   $idWarehouseGrading;

                        // $from                =   Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL') . " - Chiller Finished Good");

                        // $label               =   "ti_fg_abf";
                        // $to                  =   Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL') . " - Storage ABF");
                        // $location_from       =   env('NET_SUBSIDIARY', 'CGL') . " - Chiller Finished Good";
                        // $id_location_from    =   Gudang::gudang_netid($location_from);

                        // $gudangABF           =   env('NET_SUBSIDIARY', 'CGL') . " - Storage ABF";
                        // $idgudangABF         =   Gudang::gudang_netid($gudangABF);
                        // $tiFGtoABF           =   Netsuite::transfer_inventory_doc($nama_tabel, $id_tabel, $label, $idgudangABF, $location_from, $from, $to, $transfer_akhir, $tiWIPtoFG->id, $request->tanggal_input[$no], NULL);

                    } else {

                        // TI CS TO ABF
                        $nama_tabel          =   "reGrading";
                        $id_tabel            =   $idWarehouseGrading;

                        $from                =   Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL') . " - Chiller Finished Good");

                        $gudangABF           =   env('NET_SUBSIDIARY', 'CGL') . " - Storage ABF";
                        $idgudangABF         =   Gudang::gudang_netid($gudangABF);
                                
                        $nama_gudang_cs     =   env('NET_SUBSIDIARY', 'CGL')." - Cold Storage";
                        $gudang_cs          =   Gudang::where('code', $nama_gudang_cs)->first();

                        $label_ti            =   "ti_storage_abf";

                        $transfer_awal[] =   [
                            "internal_id_item"  =>  (string)Item::item_sku($getItem->sku)->netsuite_internal_id,
                            "item"              =>  (string)$getItem->sku,
                            "qty_to_transfer"   =>  (string)$request->berat[$no]
                        ];

                        // TI CS TO ABF
                        $tiCStoABF           =   Netsuite::transfer_inventory_doc($nama_tabel, $id_tabel, $label_ti, $gudang_cs->netsuite_internal_id, $nama_gudang_cs, $gudang_cs->netsuite_internal_id, $idgudangABF, $transfer_awal, $wop->id, $request->tanggal_input[$no], NULL);

                        $bom                 =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL') . " - AYAM KARKAS FROZEN")
                                                    ->first();

                        $bom_kategori        =   Item::find($item_finish->id);
                        // dd($bom_kategori);

                        if ($bom_kategori) {
                            if ($bom_kategori->category_id == "5") {
                                $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL') . " - BONELESS BROILER FROZEN")
                                    ->first();
                                $item_assembly = env('NET_SUBSIDIARY', 'CGL') . " - BONELESS BROILER FROZEN";
                            } elseif ($bom_kategori->category_id == "3") {
                                $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL') . " - AYAM PARTING MARINASI BROILER FROZEN")
                                    ->first();
                                $item_assembly = env('NET_SUBSIDIARY', 'CGL') . " - AYAM PARTING MARINASI BROILER FROZEN";
                            } elseif ($bom_kategori->category_id == "2") {
                                $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL') . " - AYAM PARTING BROILER FROZEN")
                                    ->first();
                                $item_assembly = env('NET_SUBSIDIARY', 'CGL') . " - AYAM PARTING BROILER FROZEN";
                            } elseif ($bom_kategori->category_id == "4" || $bom_kategori->category_id == "6") {

                                $bom_list = [
                                    env('NET_SUBSIDIARY') . ' - BY PRODUCT 1 FROZEN',
                                    env('NET_SUBSIDIARY') . ' - BY PRODUCT 2 FROZEN',
                                    env('NET_SUBSIDIARY') . ' - BY PRODUCT 3 FROZEN',
                                    env('NET_SUBSIDIARY') . ' - BY PRODUCT 4 FROZEN',
                                    env('NET_SUBSIDIARY') . ' - BY PRODUCT 5 FROZEN',
                                    env('NET_SUBSIDIARY') . ' - BY PRODUCT 6 FROZEN',
                                    env('NET_SUBSIDIARY') . ' - BY PRODUCT 7 FROZEN',
                                    env('NET_SUBSIDIARY') . ' - BY PRODUCT 8 FROZEN'
                                ];

                                try {
                                    //BOm untuk item by product, dari evis maupun boneless

                                    $bom            =   Bom::select('bom.*')
                                                        ->join('bom_item', 'bom.id', '=', 'bom_item.bom_id')
                                                        ->whereIn('bom_name', $bom_list)
                                                        ->where('bom_item.sku', $item_finish->sku)
                                                        ->first();

                                    $item_assembly  = $bom->bom_name;
                                } catch (\Throwable $th) {
                                    //throw $th;
                                    $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL') . " - EVIS FROZEN")
                                                        ->first();
                                    $item_assembly = env('NET_SUBSIDIARY', 'CGL') . " - EVIS FROZEN";
                                }

                            } else {
                                $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL') . " - AYAM KARKAS FROZEN")
                                    ->first();
                                $item_assembly = env('NET_SUBSIDIARY', 'CGL') . " - AYAM KARKAS FROZEN";
                            }

                        }

                        $nama_tabel          =   "reGrading";
                        $id_tabel            =   $idWarehouseGrading;
            
                        $from                =   Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL') . " - Chiller Finished Good");
            
                        $label               =   "ti_fg_abf";
                        $to                  =   Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL') . " - Storage ABF");


                        $location_from       =   env('NET_SUBSIDIARY', 'CGL') . " - Chiller Finished Good";
                        $id_location_from    =   Gudang::gudang_netid($location);

                        $nama_assembly       =   $bom->bom_name;
                        $id_assembly         =   $bom->netsuite_internal_id;
                        $bom_id              =   $bom->id;

                        $location            =   env('NET_SUBSIDIARY', 'CGL') . " - Storage ABF";
                        $id_location         =   Gudang::gudang_netid($location);

                        // JIKA BUKAN KARKAS, MAKA PAKAI WO 3
                        $label  =   'wo-3-abf-cs';

                        $component[]        =   [
                            "type"              =>  "Component",
                            "internal_id_item"  =>  (string)$item_finish->netsuite_internal_id,
                            "item"              =>  (string)$item_finish->sku,
                            "description"       =>  (string)$item_finish->nama,
                            "qty"               =>  (string)$request->berat[$no],
                        ];
                        

                        $finished_good[]         =   [
                            "type"              =>  "Finished Goods",
                            "internal_id_item"  =>  (string)$getItem->netsuite_internal_id,
                            "item"              =>  (string)$getItem->sku,
                            "description"       =>  (string)$getItem->nama,
                            "qty"               =>  (string)$request->berat[$no],
                        ];

                        
                        $wo     =   Netsuite::work_order_doc('reGrading', $id_tabel, $label, $id_assembly, $item_assembly, $id_location, $location, $produksi, $tiCStoABF->id, $request->tanggal_input[$no], $label);
                        
                        $label  =   'wo-3-build-abf-cs';

                        $wob    =   Netsuite::wo_build_doc('reGrading', $id_tabel, $label, $id_assembly, $item_assembly, $id_location, $location, $request->berat[$no], $produksi, $wo->id, $request->tanggal_input[$no], $label);
            

                    }


                    // ===================    TRANSFER INVENTORY IN ABF TO CS    ===================

                    if ($item_finish->category_id == 1) {
                        $datatifgabf      =   [];
                        $nama_tabel          =   "reGrading";
                        $id_tabel            =   $idWarehouseGrading;

                        $from                =   Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL') . " - Chiller Finished Good");

                        $label               =   "ti_fg_abf";
                        $to                  =   Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL') . " - Storage ABF");
                        $location_from       =   env('NET_SUBSIDIARY', 'CGL') . " - Chiller Finished Good";
                        $id_location_from    =   Gudang::gudang_netid($location_from);
            
                        $datatifgabf[]    =   [
                            "internal_id_item"  =>  (string)Item::item_sku($getItem->sku)->netsuite_internal_id,
                            "item"              =>  (string)$getItem->sku,
                            "qty_to_transfer"   =>  (string)$request->berat[$no],
                        ];
    
    
                        $tiFGtoABF = Netsuite::transfer_inventory_doc($nama_tabel, $id_tabel, $label, $id_location_from, $location_from, $from, $to, $datatifgabf, $tiWIPtoFG->id ?? $wob->id, $request->tanggal_input[$no], NULL);

                    }

                    $transfer_akhir      =   [];
                    $nama_tabel          =   "reGrading";
                    $id_tabel            =  $idWarehouseGrading;
        
                    $from                =   Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL') . " - Storage ABF");
        
                    $nama_gudang_cs      =   env('NET_SUBSIDIARY', 'CGL') . " - Cold Storage";
                    $gudang_baru         =   Gudang::where('code', $nama_gudang_cs)->first();
                    $label               =   "ti_abf_cs_" . str_replace(" ", "-", str_replace("-", "", strtolower($gudang_baru->code)));
                    $to                  =   $gudang_baru->netsuite_internal_id;
                    $location_from       =   env('NET_SUBSIDIARY', 'CGL') . " - Storage ABF";
                    $id_location_from    =   Gudang::gudang_netid($location_from);
        
                    $transfer_akhir[]    =   [
                        "internal_id_item"  =>  (string)Item::item_sku($getItem->sku)->netsuite_internal_id,
                        "item"              =>  (string)$getItem->sku,
                        "qty_to_transfer"   =>  (string)$request->berat[$no],
                    ];


                    Netsuite::transfer_inventory_doc($nama_tabel, $id_tabel, $label, $id_location_from, $location_from, $from, $to, $transfer_akhir, $tiFGtoABF->id ?? $wob->id, $request->tanggal_input[$no], NULL);

                }


                $gudang_lama->qty       -= $totalQty;
                $gudang_lama->berat     -= $totalBerat;
                $gudang_lama->save();
    
                $url = $request->url;


                DB::commit();
    
                return redirect()->route('abf.index', ['key' => 'grading-ulang'])->with('status', 1)->with('message', 'Berhasil Grading ulang item ' . $gudang_lama->nama)->withFragment('#custom-tabs-sumgradul');
            } else {
                DB::rollBack();
                return redirect()->back()->with('status', 2)->with('message', 'Data tidak ditemukan');
            }



        } else if ($request->key == 'injectGradingUlang') {

            // return 'oke';
            $gudang         = Product_gudang::find($request->id);
            $getItem        = Item::where('id',$gudang->product_id)->first();
            $gudang_lama    = Product_gudang::where('id', $gudang->table_id)->first();

            // dd($id);


            DB::beginTransaction();

            if ($gudang) {

            $nama_gudang_bb         = env('NET_SUBSIDIARY', 'CGL')." - Chiller Bahan Baku";
            $nama_gudang_fg         = env('NET_SUBSIDIARY', 'CGL')." - Chiller Finished Good";
            // dd($idWarehouseGrading);

            $idWarehouseGrading     = $gudang->id;
            if($gudang->berat_awal > 0){
                
                $code                   =   'TW-' . $idWarehouseGrading ;
                $gudang                 =   Product_gudang::find($idWarehouseGrading);
                $item_frozen            =   Item::find($gudang->nama);
                $item_finish            =   Item::where('nama', str_replace(' FROZEN', '', ($gudang->nama)))->first();

                try {
                    //code...

                    $nama_gudang_cs     =   env('NET_SUBSIDIARY', 'CGL')." - Cold Storage";
                    $gudang_cs          =   Gudang::where('code', $nama_gudang_cs)->first();

                    if ($gudang_cs) {
                        $id_location    =   $gudang_cs->netsuite_internal_id;
                        $location       =   $gudang_cs->code;
                        $from           =   $id_location;

                    } else {

                        $id_location    =   Gudang::find($gudang->gudang_id)->netsuite_internal_id;
                        $location       =   Gudang::find($gudang->gudang_id)->code;
                        $from           =   $id_location;
                    }

                    $label          =   'wo-4-thawing';

                    try {
                        //code...

                        $bom_kategori = Item::find($gudang->product_id);
                        if ($bom_kategori) {
                            if ($bom_kategori->category_id=="8") {

                                $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL')." - THAWING AYAM PARTING BROILER FROZEN")
                                ->first();

                            } elseif ($bom_kategori->category_id=="9") {
                                $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL')." - THAWING AYAM PARTING MARINASI BROILER FROZEN")
                                ->first();

                            } elseif ($bom_kategori->category_id=="7") {
                                $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL')." - THAWING AYAM KARKAS BROILER FROZEN")
                                ->first();

                            } elseif ($bom_kategori->category_id=="11") {
                                $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL')." - THAWING BONELESS BROILER FROZEN")
                                ->first();

                            } elseif ($bom_kategori->category_id=="10") {
                                $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL')." - THAWING EVIS FROZEN")
                                ->first();

                            } else {
                                $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL').' - THAWING AYAM KARKAS BROILER FROZEN')
                                ->first();

                            }

                        } else {
                            $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL').' - THAWING AYAM KARKAS BROILER FROZEN')
                            ->first();

                        }

                        $bom_id         =   $bom->id;
                        $id_assembly    =   $bom->netsuite_internal_id;
                        $item_assembly  =   $bom->bom_name ;

                    } catch (\Throwable $th) {
                        //throw $th;
                        $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL').' - THAWING AYAM KARKAS BROILER FROZEN')->first();
                        $bom_id         =   $bom->id;
                        $id_assembly    =   $bom->netsuite_internal_id;
                        $item_assembly  =   $bom->bom_name ;
        
                    }

                    $getItemGudangLama      =   Item::where('id', $gudang_lama->product_id)->first();

                    $component      =   [[
                        "type"              =>  "Component",
                        "internal_id_item"  =>  (string)$getItemGudangLama->netsuite_internal_id,
                        "item"              =>  (string)$getItemGudangLama->sku,
                        "description"       =>  (string)$getItemGudangLama->nama,
                        "qty"               =>  (string)$gudang->berat_awal,
                    ]];


                    $label = $gudang->gudangabf != NULL ? $gudang->gudangabf->tujuan : $gudang->type;
                    if ($item_finish->category_id == '1') {

                        
                        $label_ti   =   "ti_storage" . $gudang->type . "_chillerbb-thawing";
                        $to         =   Gudang::gudang_netid($nama_gudang_bb);


                        if (substr($item_finish->sku, 0, 5) == "12111") {
                            $transfer   =   [
                                [
                                    "internal_id_item"  =>  (string)Item::item_sku("1100000001")->netsuite_internal_id ,
                                    "item"              =>  "1100000001",
                                    "qty_to_transfer"   =>  (string)$gudang->berat_awal
                                ]
                            ];

                            $finished_good  =   [[
                                "type"              =>  "Finished Goods",
                                "internal_id_item"  =>  (string)Item::item_sku("1100000001")->netsuite_internal_id ,
                                "item"              =>  "1100000001",
                                "description"       =>  "AYAM KARKAS BROILER (RM)",
                                "qty"               =>  (string)$gudang->berat_awal
                            ]];
                        //check ayam MEMAR
                        } elseif (substr($item_finish->sku, 0, 5) == "12113") {
                            $transfer   =   [
                                [
                                    "internal_id_item"  =>  (string)Item::item_sku("1100000003")->netsuite_internal_id ,
                                    "item"              =>  "1100000003",
                                    "qty_to_transfer"   => (string)$gudang->berat_awal
                                ]
                            ];

                            $finished_good  =   [[
                                "type"              =>  "Finished Goods",
                                "internal_id_item"  =>  (string)Item::item_sku("1100000003")->netsuite_internal_id ,
                                "item"              =>  "1100000003",
                                "description"       =>  "AYAM MEMAR (RM)",
                                "qty"               =>  (string)$gudang->berat_awal
                            ]];

                        } elseif (substr($item_finish->sku, 0, 5) == "12112") {
                            $transfer   =   [
                                [
                                    "internal_id_item"  =>  (string)Item::item_sku("1100000002")->netsuite_internal_id ,
                                    "item"              =>  "1100000002",
                                    "qty_to_transfer"   => (string)$gudang->berat_awal
                                ]
                            ];

                            $finished_good  =   [[
                                "type"              =>  "Finished Goods",
                                "internal_id_item"  =>  (string)Item::item_sku("1100000002")->netsuite_internal_id ,
                                "item"              =>  "1100000002",
                                "description"       =>  "AYAM UTUH (RM)",
                                "qty"               =>  (string)$gudang->berat_awal
                            ]];



                        // check ayam PEJANTAN

                    } elseif (substr($item_finish->sku, 0, 4) == "1213" || substr($item_finish->sku, 0, 4) == "1223") {
                        // $jenis = "pejantan";
                        $transfer   =   [
                            [
                                "internal_id_item"  =>  (string)Item::item_sku("1100000005")->netsuite_internal_id ,
                                "item"              =>  "1100000005",
                                "qty_to_transfer"   => (string)$gudang->berat_awal
                            ]
                        ];

                        $finished_good  =   [[
                            "type"              =>  "Finished Goods",
                            "internal_id_item"  =>  (string)Item::item_sku("1100000005")->netsuite_internal_id ,
                            "item"              =>  "1100000005",
                            "description"       =>  "AYAM PEJANTAN (RM)",
                            "qty"               =>  (string)$gudang->berat_awal
                        ]];

                    // Check ayam parent
                    } elseif (substr($item_finish->sku, 0, 4) == "1214" || substr($item_finish->sku, 0, 4) == "1224") {
                        // $jenis = "parent";
                        $transfer   =   [
                            [
                                "internal_id_item"  =>  (string)Item::item_sku("1100000009")->netsuite_internal_id ,
                                "item"              =>  "1100000009",
                                "qty_to_transfer"   => (string)$gudang->berat_awal
                            ]
                        ];

                        $finished_good  =   [[
                            "type"              =>  "Finished Goods",
                            "internal_id_item"  =>  (string)Item::item_sku("1100000009")->netsuite_internal_id ,
                            "item"              =>  "1100000009",
                            "description"       =>  "AYAM PARENT (RM)",
                            "qty"               => (string)$gudang->berat_awal
                        ]];

                    // Check ayam kampung
                    } elseif (substr($item_finish->sku, 0, 4) == "1212" || substr($item_finish->sku, 0, 4) == "1222") {
                        // $jenis = "kampung";
                        $transfer   =   [
                            [
                                "internal_id_item"  =>  (string)Item::item_sku("1100000004")->netsuite_internal_id ,
                                "item"              =>  "1100000004",
                                "qty_to_transfer"   => (string)$gudang->berat_awal
                            ]
                        ];

                        $finished_good  =   [[
                            "type"              =>  "Finished Goods",
                            "internal_id_item"  =>  (string)Item::item_sku("1100000004")->netsuite_internal_id ,
                            "item"              =>  "1100000004",
                            "description"       =>  "AYAM KAMPUNG (RM)",
                            "qty"               => (string)$gudang->berat_awal
                        ]];

                    } elseif (substr($item_finish->sku, 0, 5) == "12115") {

                        $transfer   =   [
                            [
                                "internal_id_item"  =>  (string)Item::item_sku("1100000007")->netsuite_internal_id ,
                                "item"              =>  "1100000007",
                                "qty_to_transfer"   => (string)$gudang->berat_awal
                            ]
                        ];

                        $finished_good  =   [[
                            "type"              =>  "Finished Goods",
                            "internal_id_item"  =>  (string)Item::item_sku("1100000007")->netsuite_internal_id ,
                            "item"              =>  "1100000007",
                            "description"       =>  "AYAM PARTING (M) (RM)",
                            "qty"               => (string)$gudang->berat_awal
                        ]];


                    } elseif (substr($item_finish->sku, 0, 5) == "12114") {

                        $transfer   =   [
                            [
                                "internal_id_item"  =>  (string)Item::item_sku("1100000006")->netsuite_internal_id ,
                                "item"              =>  "1100000006",
                                "qty_to_transfer"   => (string)$gudang->berat_awal
                            ]
                        ];

                        $finished_good  =   [[
                            "type"              =>  "Finished Goods",
                            "internal_id_item"  =>  (string)Item::item_sku("1100000006")->netsuite_internal_id ,
                            "item"              =>  "1100000006",
                            "description"       =>  "AYAM PARTING (RM)",
                            "qty"               => (string)$gudang->berat_awal
                        ]];

                        }

                    
                    
                    } else {

                        $label_ti   =   "ti_storage" .  $label . "_chillerfg-thawing";
                        $to         =   Gudang::gudang_netid($nama_gudang_fg);
                        $transfer   =   [
                            [
                                "internal_id_item"  =>  (string)Item::item_sku($item_finish->sku)->netsuite_internal_id ,
                                "item"              =>  $item_finish->sku,
                                "qty_to_transfer"   => (string)$gudang->berat_awal
                            ]
                        ];

                        $finished_good  =   [[
                            "type"              =>  "Finished Goods",
                            "internal_id_item"  =>  (string)Item::item_sku($item_finish->sku)->netsuite_internal_id ,
                            "item"              =>  $item_finish->sku,
                            "description"       =>  $item_finish->nama,
                            "qty"               => (string)$gudang->berat_awal
                        ]];

                    }

                    // MASUK CHILLER



                    $produksi       =   array_merge($component, $finished_good);

                    $nama_tabel     =   'reGrading';
                    $id_tabel       =   $idWarehouseGrading;
                    $label          =   'wo-4-thawing';

                    $wo             =   Netsuite::work_order_doc($nama_tabel, $id_tabel, $label, $id_assembly, $item_assembly, $id_location, $location, $produksi, null, $gudang->production_date, $code);

                    $label          =   'wo-4-build-thawing';
                    $total          =   $gudang->berat_awal;
                    $wop            =   Netsuite::wo_build_doc($nama_tabel, $id_tabel, $label, $id_assembly, $item_assembly, $id_location, $location, $total, $produksi, $wo->id, $gudang->production_date, $code);

                    if ($item_finish->category_id == 1) {
                        // IT(CS-BB)
                        $tiCStoBB       =   Netsuite::transfer_inventory_doc($nama_tabel, $id_tabel, $label_ti, $id_location, $location, $from, $to, $transfer, $wop->id, $gudang->production_date , $code);
                    }

                    // DB::Commit();

                } catch (\Throwable $th) {
                    //throw $th;

                    DB::rollBack();
                    return "FAILED ".$th->getMessage()."<br>";
                }

            } else {
                DB::rollBack() ;
                return "Berat kosong";
            }

            // END KIRIMAN THAWING


            // BUAT DATA KIRIMAN WO 2 FROZEN
            if ($item_finish->category_id == '1') {
                $regu               = 'frozen';
                $bom                =   Bom::where('bom_name', env("NET_SUBSIDIARY", "CGL") . " - AYAM KARKAS FROZEN")->first();
                $id_assembly        =  $bom->netsuite_internal_id;
                $nama_assembly      =  $bom->bom_name ;

                $code               = 'wo-2-'.$regu.'-'.time();

                $nama_gudang_wip    = env('NET_SUBSIDIARY', 'CGL')." - Storage Produksi (WIP)";

                // ===================    TRANSFER INVENTORY IN WIP    ===================

                $nama_tabel         =   "reGrading";
                $id_tabel           =   $idWarehouseGrading;

                $location           =   $nama_gudang_bb;
                $id_location        =   Gudang::gudang_netid($location);
                $from               =   $id_location;
                $to                 =   Gudang::gudang_netid($nama_gudang_wip);

                // untuk data kepala regu
                $arr_trf            =   [];
                $arr_bb             =   [];
                $berat_rm           =   0;
                $berat_memar        =   0;
                $berat_parent       =   0;
                $berat_pejantan     =   0;
                $berat_kampung      =   0;
                $berat_fg           =   0;
                $arr_trf_fg         =   [];
                $trans              =   [];
                $bahanbaku          =   [];

                // ARRAY BB
                $bb_gabung = [];

                // $bb_gabung[]  =   [
                //     "type"              =>  "Component",
                //     "internal_id_item"  =>  (string)$item_finish->netsuite_internal_id,
                //     "item"              =>  $item_finish->sku,
                //     "description"       =>  $item_finish->nama,
                //     "qty"               =>  $gudang->berat_awal
                // ];

            }
            if ($item_finish->category_id == '1') {

                if (substr($item_finish->sku, 0, 5) == "12111" || substr($item_finish->sku, 0, 5) == "12112") {

                    $bb_gabung  =   [[
                        "type"              =>  "Component",
                        "internal_id_item"  =>  (string)Item::item_sku("1100000001")->netsuite_internal_id ,
                        "item"              =>  "1100000001",
                        "description"       =>  "AYAM KARKAS BROILER (RM)",
                        "qty"               =>  (string)$gudang->berat_awal
                    ]];

                //check ayam MEMAR
                } elseif (substr($item_finish->sku, 0, 5) == "12113") {

                    $bb_gabung  =   [[
                        "type"              =>  "Component",
                        "internal_id_item"  =>  (string)Item::item_sku("1100000003")->netsuite_internal_id ,
                        "item"              =>  "1100000003",
                        "description"       =>  "AYAM MEMAR (RM)",
                        "qty"               =>  (string)$gudang->berat_awal
                    ]];

                    // check ayam PEJANTAN

                } elseif (substr($item_finish->sku, 0, 4) == "1213" || substr($item_finish->sku, 0, 4) == "1223") {
                    // $jenis = "pejantan";

                    $bb_gabung  =   [[
                        "type"              =>  "Component",
                        "internal_id_item"  =>  (string)Item::item_sku("1100000005")->netsuite_internal_id ,
                        "item"              =>  "1100000005",
                        "description"       =>  "AYAM PEJANTAN (RM)",
                        "qty"               =>  (string)$gudang->berat_awal
                    ]];

                // Check ayam parent
                } elseif (substr($item_finish->sku, 0, 4) == "1214" || substr($item_finish->sku, 0, 4) == "1224") {
                    // $jenis = "parent";

                    $bb_gabung  =   [[
                        "type"              =>  "Component",
                        "internal_id_item"  =>  (string)Item::item_sku("1100000009")->netsuite_internal_id ,
                        "item"              =>  "1100000009",
                        "description"       =>  "AYAM PARENT (RM)",
                        "qty"               => (string)$gudang->berat_awal
                    ]];

                // Check ayam kampung
                } elseif (substr($item_finish->sku, 0, 4) == "1212" || substr($item_finish->sku, 0, 4) == "1222") {
                    // $jenis = "kampung";

                    $bb_gabung  =   [[
                        "type"              =>  "Component",
                        "internal_id_item"  =>  (string)Item::item_sku("1100000004")->netsuite_internal_id ,
                        "item"              =>  "1100000004",
                        "description"       =>  "AYAM KAMPUNG (RM)",
                        "qty"               => (string)$gudang->berat_awal
                    ]];
                
                } elseif (substr($item_finish->sku, 0, 5) == "12115") {

                    $bb_gabung  =   [[
                        "type"              =>  "Component",
                        "internal_id_item"  =>  (string)Item::item_sku("1100000007")->netsuite_internal_id ,
                        "item"              =>  "1100000007",
                        "description"       =>  "AYAM PARTING (M) (RM)",
                        "qty"               => (string)$gudang->berat_awal
                    ]];


                } elseif (substr($item_finish->sku, 0, 5) == "12114") {

                    $bb_gabung  =   [[
                        "type"              =>  "Component",
                        "internal_id_item"  =>  (string)Item::item_sku("1100000006")->netsuite_internal_id ,
                        "item"              =>  "1100000006",
                        "description"       =>  "AYAM PARTING (RM)",
                        "qty"               => (string)$gudang->berat_awal
                    ]];



                } else {

                    $finished_good  =   [[
                        "type"              =>  "Component",
                        "internal_id_item"  =>  (string)Item::item_sku($item_finish->sku)->netsuite_internal_id ,
                        "item"              =>  $item_finish->sku,
                        "description"       =>  $item_finish->nama,
                        "qty"               => (string)$gudang->berat_awal
                    ]];



                }

                $bahanbaku  =   array_merge($bb_gabung, $arr_bb);


                $label          =   "ti_bb_prod_".$regu;

                // Check ayam KARKAS dan ayam utuh
                if (substr($item_finish->sku, 0, 5) == "12111" || substr($item_finish->sku, 0, 5) == "12112") {

                    $berat_rm           +=  $gudang->berat_awal;
                //check ayam MEMAR
                } elseif (substr($item_finish->sku, 0, 5) == "12113") {
                    $berat_memar        +=  $gudang->berat_awal;
                // check ayam KAMPUNG
                } elseif (substr($item_finish->sku, 0, 5) == "12122" || substr($item_finish->sku, 0, 5) == "12121") {
                    $berat_kampung      +=  $gudang->berat_awal;
                // check ayam PEJANTAN
                } elseif (substr($item_finish->sku, 0, 5) == "12131" || substr($item_finish->sku, 0, 5) == "12132") {
                    $berat_pejantan     +=  $gudang->berat_awal;
                // check ayam PARENT
                } elseif (substr($item_finish->sku, 0, 5) == "12142" || substr($item_finish->sku, 0, 5) == "12141") {
                    $berat_parent       +=  $gudang->berat_awal;
                }

                       //Konversi Component to RM
                if($berat_rm!="0" || $berat_memar!="0" || $berat_kampung!="0" || $berat_pejantan!="0" || $berat_parent!="0"){

                    $arr_gabung = [];
                    if($berat_rm!="0"){
                        $arr_gabung[] = [
                            "internal_id_item"  =>  (string)Item::item_sku("1100000001")->netsuite_internal_id ,
                            "item"              =>  "1100000001" ,
                            "qty_to_transfer"   =>  "$berat_rm"
                        ];
                    }

                    if($berat_memar!="0"){
                        $arr_gabung[] = [
                            "internal_id_item"  =>  (string)Item::item_sku("1100000003")->netsuite_internal_id ,
                            "item"              =>  "1100000003" ,
                            "qty_to_transfer"   =>  "$berat_memar"
                        ];
                    }

                    if($berat_kampung!="0"){
                        $arr_gabung[] = [
                            "internal_id_item"  =>  (string)Item::item_sku("1100000004")->netsuite_internal_id ,
                            "item"              =>  "1100000004" ,
                            "qty_to_transfer"   =>  "$berat_kampung"
                        ];
                    }

                    if($berat_pejantan!="0"){
                        $arr_gabung[] = [
                            "internal_id_item"  =>  (string)Item::item_sku("1100000005")->netsuite_internal_id ,
                            "item"              =>  "1100000005" ,
                            "qty_to_transfer"   =>  "$berat_pejantan"
                        ];
                    }

                    if($berat_parent!="0"){
                        $arr_gabung[] = [
                            "internal_id_item"  =>  (string)Item::item_sku("1100000009")->netsuite_internal_id ,
                            "item"              =>  "1100000009" ,
                            "qty_to_transfer"   =>  "$berat_parent"
                        ];
                    }

                    $trans  =   array_merge($arr_gabung, $arr_trf) ;

                }

                // IT(BB-WIP)
                $ti = Netsuite::transfer_inventory_doc($nama_tabel, $id_tabel, $label, $id_location, $location, $from, $to, $trans, $tiCStoBB->id, $gudang->production_date, $code);

                // ARRAY FINISHED GOODS
                $data_produksi  =   [];

                if(Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL').' - EVIS')->first()->netsuite_internal_id == $id_assembly || Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL').' - NON KARKAS - BONELESS BROILER')->first()->netsuite_internal_id == $id_assembly){

                    $total    +=  $gudang->berat_awal;
                    $data_produksi[]    =   [
                        "type"              =>  "Finished Goods",
                        "internal_id_item"  =>  (string)Item::item_sku($getItem->sku)->netsuite_internal_id,
                        "item"              =>  (string)$getItem->sku,
                        "description"       =>  (string)Item::item_sku($getItem->sku)->nama,
                        "qty"               =>  (string)$gudang->berat_awal,
                    ];

                } else {

                    $bom_item = BomItem::where('sku', $getItem->sku)->where('bom_id', $bom->id)->first();
                    if($bom_item){
                        if($bom_item->kategori=="By Product"){
                            $total;
                        }else{
                            $total    +=  $gudang->berat_awal;
                        }
                    }else{
                        $total    +=  $gudang->berat_awal;
                    }

                    $item_cat = Item::find($getItem->id);

                    $type = (($item_cat->category_id == 4) OR ($item_cat->category_id == 6) OR ($item_cat->category_id == 10) OR ($item_cat->category_id == 16)) ? "By Product" : "Finished Goods";
                    
                    if($bom_item){
                        $type = $bom_item->kategori;
                    }

                    $data_produksi[]    =   [
                        "type"              =>  $type,
                        "internal_id_item"  =>  (string)Item::item_sku($getItem->sku)->netsuite_internal_id,
                        "item"              =>  (string)$getItem->sku,
                        "description"       =>  (string)Item::item_sku($getItem->sku)->nama,
                        "qty"               =>  (string)$gudang->berat_awal,
                    ];

                }

                $produksi       =   array_merge($bahanbaku, $data_produksi);

                $label          =   "wo-2-".$regu;
                $wo             =   Netsuite::work_order_doc($nama_tabel, $id_tabel, $label, $id_assembly, $nama_assembly, $to, $nama_gudang_wip, $produksi, $ti->id, $gudang->production_date, $code);

                // ===================    WO - 2 - BUILD    ===================

                $label          =   "wo-2-build-".$regu;
                $wob            =   Netsuite::wo_build_doc($nama_tabel, $id_tabel, $label, $id_assembly, $nama_assembly, $to, $nama_gudang_wip, $total, $produksi, $wo->id, $gudang->production_date, $code);
                

                $nama_tabel     =   "reGrading";
                $id_tabel       =    $idWarehouseGrading;


                // WIP TO FG
                $from           =   Gudang::gudang_netid($nama_gudang_wip);
                $label          =   "ti_prod_fg_".$regu;
                $to             =   Gudang::gudang_netid($nama_gudang_fg);

                $transfer_wiptofg[]    =   [
                    "internal_id_item"  =>  (string)Item::item_sku($getItem->sku)->netsuite_internal_id,
                    "item"              =>  (string)$getItem->sku,
                    "qty_to_transfer"   =>  (string)$gudang->berat_awal,
                ];
                $tiWIPtoFG      =   Netsuite::transfer_inventory_doc($nama_tabel, $id_tabel, $label, $from, $nama_gudang_wip, $from, $to, $transfer_wiptofg, $wob->id, $gudang->production_date, $code);

                // END WO 2 


                // TI / WO 3

                // ===================    TRANSFER INVENTORY IN FINISHED GOOD TO ABF    ===================

            } else {

                // TI CS TO ABF
                $nama_tabel          =   "reGrading";
                $id_tabel            =   $idWarehouseGrading;

                $from                =   Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL') . " - Chiller Finished Good");

                $gudangABF           =   env('NET_SUBSIDIARY', 'CGL') . " - Storage ABF";
                $idgudangABF         =   Gudang::gudang_netid($gudangABF);
                        
                $nama_gudang_cs     =   env('NET_SUBSIDIARY', 'CGL')." - Cold Storage";
                $gudang_cs          =   Gudang::where('code', $nama_gudang_cs)->first();

                $label_ti            =   "ti_storage_abf";

                $transfer_awal[] =   [
                    "internal_id_item"  =>  (string)Item::item_sku($getItem->sku)->netsuite_internal_id,
                    "item"              =>  (string)$getItem->sku,
                    "qty_to_transfer"   =>  (string)$gudang->berat_awal
                ];

                // TI CS TO ABF
                $tiCStoABF           =   Netsuite::transfer_inventory_doc($nama_tabel, $id_tabel, $label_ti, $gudang_cs->netsuite_internal_id, $nama_gudang_cs, $gudang_cs->netsuite_internal_id, $idgudangABF, $transfer_awal, $wop->id, $gudang->production_date, NULL);

                $bom                 =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL') . " - AYAM KARKAS FROZEN")
                                            ->first();

                $bom_kategori        =   Item::find($item_finish->id);
                // dd($bom_kategori);

                if ($bom_kategori) {
                    if ($bom_kategori->category_id == "5") {
                        $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL') . " - BONELESS BROILER FROZEN")
                            ->first();
                        $item_assembly = env('NET_SUBSIDIARY', 'CGL') . " - BONELESS BROILER FROZEN";
                    } elseif ($bom_kategori->category_id == "3") {
                        $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL') . " - AYAM PARTING MARINASI BROILER FROZEN")
                            ->first();
                        $item_assembly = env('NET_SUBSIDIARY', 'CGL') . " - AYAM PARTING MARINASI BROILER FROZEN";
                    } elseif ($bom_kategori->category_id == "2") {
                        $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL') . " - AYAM PARTING BROILER FROZEN")
                            ->first();
                        $item_assembly = env('NET_SUBSIDIARY', 'CGL') . " - AYAM PARTING BROILER FROZEN";
                    } elseif ($bom_kategori->category_id == "4" || $bom_kategori->category_id == "6") {

                        $bom_list = [
                            env('NET_SUBSIDIARY') . ' - BY PRODUCT 1 FROZEN',
                            env('NET_SUBSIDIARY') . ' - BY PRODUCT 2 FROZEN',
                            env('NET_SUBSIDIARY') . ' - BY PRODUCT 3 FROZEN',
                            env('NET_SUBSIDIARY') . ' - BY PRODUCT 4 FROZEN',
                            env('NET_SUBSIDIARY') . ' - BY PRODUCT 5 FROZEN',
                            env('NET_SUBSIDIARY') . ' - BY PRODUCT 6 FROZEN',
                            env('NET_SUBSIDIARY') . ' - BY PRODUCT 7 FROZEN',
                            env('NET_SUBSIDIARY') . ' - BY PRODUCT 8 FROZEN'
                        ];

                        try {
                            //BOm untuk item by product, dari evis maupun boneless

                            $bom            =   Bom::select('bom.*')
                                                ->join('bom_item', 'bom.id', '=', 'bom_item.bom_id')
                                                ->whereIn('bom_name', $bom_list)
                                                ->where('bom_item.sku', $item_finish->sku)
                                                ->first();

                            $item_assembly  = $bom->bom_name;
                        } catch (\Throwable $th) {
                            //throw $th;
                            $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL') . " - EVIS FROZEN")
                                                ->first();
                            $item_assembly = env('NET_SUBSIDIARY', 'CGL') . " - EVIS FROZEN";
                        }

                    } else {
                        $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL') . " - AYAM KARKAS FROZEN")
                            ->first();
                        $item_assembly = env('NET_SUBSIDIARY', 'CGL') . " - AYAM KARKAS FROZEN";
                    }

                }

                $nama_tabel          =   "reGrading";
                $id_tabel            =   $idWarehouseGrading;
    
                $from                =   Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL') . " - Chiller Finished Good");
    
                $label               =   "ti_fg_abf";
                $to                  =   Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL') . " - Storage ABF");


                $location_from       =   env('NET_SUBSIDIARY', 'CGL') . " - Chiller Finished Good";
                $id_location_from    =   Gudang::gudang_netid($location);

                $nama_assembly       =   $bom->bom_name;
                $id_assembly         =   $bom->netsuite_internal_id;
                $bom_id              =   $bom->id;

                $location            =   env('NET_SUBSIDIARY', 'CGL') . " - Storage ABF";
                $id_location         =   Gudang::gudang_netid($location);

                // JIKA BUKAN KARKAS, MAKA PAKAI WO 3
                $label  =   'wo-3-abf-cs';

                $component[]        =   [
                    "type"              =>  "Component",
                    "internal_id_item"  =>  (string)$item_finish->netsuite_internal_id,
                    "item"              =>  (string)$item_finish->sku,
                    "description"       =>  (string)$item_finish->nama,
                    "qty"               =>  (string)$gudang->berat_awal,
                ];
                

                $finished_good[]         =   [
                    "type"              =>  "Finished Goods",
                    "internal_id_item"  =>  (string)$getItem->netsuite_internal_id,
                    "item"              =>  (string)$getItem->sku,
                    "description"       =>  (string)$getItem->nama,
                    "qty"               =>  (string)$gudang->berat_awal,
                ];

                
                $wo     =   Netsuite::work_order_doc('reGrading', $id_tabel, $label, $id_assembly, $item_assembly, $id_location, $location, $produksi, $tiCStoABF->id, $gudang->production_date, $label);
                
                $label  =   'wo-3-build-abf-cs';

                $wob    =   Netsuite::wo_build_doc('reGrading', $id_tabel, $label, $id_assembly, $item_assembly, $id_location, $location, $gudang->berat_awal, $produksi, $wo->id, $gudang->production_date, $label);
    

            }


            // ===================    TRANSFER INVENTORY IN ABF TO CS    ===================

            if ($item_finish->category_id == 1) {
                $datatifgabf      =   [];
                $nama_tabel          =   "reGrading";
                $id_tabel            =   $idWarehouseGrading;

                $from                =   Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL') . " - Chiller Finished Good");

                $label               =   "ti_fg_abf";
                $to                  =   Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL') . " - Storage ABF");
                $location_from       =   env('NET_SUBSIDIARY', 'CGL') . " - Chiller Finished Good";
                $id_location_from    =   Gudang::gudang_netid($location_from);
    
                $datatifgabf[]    =   [
                    "internal_id_item"  =>  (string)Item::item_sku($getItem->sku)->netsuite_internal_id,
                    "item"              =>  (string)$getItem->sku,
                    "qty_to_transfer"   =>  (string)$gudang->berat_awal,
                ];


                $tiFGtoABF = Netsuite::transfer_inventory_doc($nama_tabel, $id_tabel, $label, $id_location_from, $location_from, $from, $to, $datatifgabf, $tiWIPtoFG->id ?? $wob->id, $gudang->production_date, NULL);

            }

            $transfer_akhir      =   [];
            $nama_tabel          =   "reGrading";
            $id_tabel            =  $idWarehouseGrading;

            $from                =   Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL') . " - Storage ABF");

            $nama_gudang_cs      =   env('NET_SUBSIDIARY', 'CGL') . " - Cold Storage";
            $gudang_baru         =   Gudang::where('code', $nama_gudang_cs)->first();
            $label               =   "ti_abf_cs_" . str_replace(" ", "-", str_replace("-", "", strtolower($gudang_baru->code)));
            $to                  =   $gudang_baru->netsuite_internal_id;
            $location_from       =   env('NET_SUBSIDIARY', 'CGL') . " - Storage ABF";
            $id_location_from    =   Gudang::gudang_netid($location_from);

            $transfer_akhir[]    =   [
                "internal_id_item"  =>  (string)Item::item_sku($getItem->sku)->netsuite_internal_id,
                "item"              =>  (string)$getItem->sku,
                "qty_to_transfer"   =>  (string)$gudang->berat_awal,
            ];


            Netsuite::transfer_inventory_doc($nama_tabel, $id_tabel, $label, $id_location_from, $location_from, $from, $to, $transfer_akhir, $tiFGtoABF->id ?? $wob->id, $gudang->production_date, NULL);

            DB::commit();
    
            // return redirect()->route('abf.index', ['key' => 'grading-ulang'])->with('status', 1)->with('message', 'Berhasil Grading ulang item ' . $gudang_lama->nama)->withFragment('#custom-tabs-sumgradul');

            return response()->json([
                'status' => 200,
                'msg'    => 'Berhasil Buat Ulang JSON'
            ]);

            } else {

                DB::rollBack();
                return response()->json([
                    'status' => 400,
                    'msg'    => 'Gagal Buat Ulang JSON'
                ]);
                // return redirect()->back()->with('status', 2)->with('message', 'Data tidak ditemukan');
            }



            // END INJECT





        } else {
            $abf            =   Abf::find($request->kode);
            $abf->status    =   2;
            $abf->save();


            if ($abf->table_name != 'chiller') {
                $orderitem      =   OrderItem::find($abf->table_id);
                $bahanbaku      =   Bahanbaku::where('order_id', $orderitem->order_id)->where('order_item_id', $orderitem->id)->first();
                $chiller        =   Chiller::find($bahanbaku->chiller_id);
                $order          =   Order::find($orderitem->order_id);

                $gudang                    =   new Product_gudang;
                $gudang->product_id        =   $abf->production_id;
                $gudang->table_name        =   $abf->table_name;
                $gudang->table_id          =   $abf->table_id;
                $gudang->no_so             =   $order->id;
                $gudang->order_id          =   $orderitem->order_id;
                $gudang->order_item_id     =   $orderitem->id;
                $gudang->qty_awal          =   $abf->qty_item;
                $gudang->berat_awal        =   $abf->berat_item;
                $gudang->qty               =   $abf->qty_item;
                $gudang->berat             =   $abf->berat_item;
                $gudang->parting           =   $abf->abf_chiller->parting ?? 0;
                $gudang->jenis_trans       =   'masuk';
                $gudang->abf_id            =   $abf->id;
                $gudang->chiller_id        =   $chiller->id;
                $gudang->type              =   $abf->type;
                $gudang->grade_item        =   $abf->grade_item;
                $gudang->status            =   1;
                $gudang->save();
            } else {

                $gudang                    =   new Product_gudang;
                $gudang->product_id        =   $abf->production_id;
                $gudang->table_name        =   $abf->table_name;
                $gudang->table_id          =   $abf->table_id;
                $gudang->qty_awal          =   $abf->qty_item;
                $gudang->berat_awal        =   $abf->berat_item;
                $gudang->qty               =   $abf->qty_item;
                $gudang->berat             =   $abf->berat_item;
                $gudang->parting           =   $abf->abf_chiller->parting ?? 0;
                $gudang->jenis_trans       =   'masuk';
                $gudang->abf_id            =   $abf->id;
                $gudang->chiller_id        =   $abf->table_id;
                $gudang->type              =   $abf->type;
                $gudang->grade_item        =   $abf->grade_item;
                $gudang->status            =   1;
                $gudang->save();
            }

            $gudang->production_code        =   Gudang::kode_produksi($gudang->id);
            $gudang->save();
        }
    }

    public function show()
    {
        $data       =   Abf::orderBy('id', 'DESC')->get();
        return view('admin.pages.abf.data_show', compact('data'));
    }

    public function timbang(Request $request, $id)
    {
    
        $data               =   Abf::find($id);
        $sub_item           =   ($data->abf_chiller->label ?? "") ? json_decode($data->abf_chiller->label) : '';
        $customer           =   Customer::all();
        // $subsidiary =   Session::get('subsidiary');
        $warehouse          =   Gudang::where('kategori', 'warehouse')
                                ->where('subsidiary', Session::get('subsidiary'))
                                ->where('code', 'NOT LIKE', "%abf%")
                                ->where('status', 1)
                                ->get();


        $item_name          =    Adminedit::where('type','item_name')->get();
        $plastikGroup       =    Adminedit::where('type','plastik_group')->get();

        return view('admin.pages.abf.timbang', compact('data', 'warehouse', 'sub_item','customer', 'item_name', 'plastikGroup'));

    }

    public function storetimbang(Request $request)
    {

        // dd($request->all());
        DB::beginTransaction();

        if ($request->key == 'commit') {
            $gudang             =   Product_gudang::find($request->id);

            $data               =   Abf::find($gudang->table_id);
            $berat              =   $data->berat_item - $gudang->berat_timbang;
            $qty                =   $data->qty_item - $gudang->qty;
            $data->berat_item   =   $berat;
            $data->qty_item     =   $qty;
            $data->status       =   ($qty < 1) ? 2 : 1;

            if (!$data->save()) {
                DB::rollBack();
                return back()->with('status', 2)->with('message', 'Kesalahan simpan data');
            }

            $gudang->status     =   1;

            if (!$gudang->save()) {
                DB::rollBack();
                return back()->with('status', 2)->with('message', 'Kesalahan simpan data');
            }

            DB::commit();
            return redirect()->route('abf.index')->with('status', 1)->with('message', 'Berhasil Simpan');

        } else if ($request->key == 'itemname') {
                $cekDuplicate                           = Adminedit::where('type', 'item_name')->where('data', $request->itemname)->first();
                if ($cekDuplicate){
                    DB::rollBack();
                    return response()->json([
                        'status' => 400,
                        'msg'    => 'Proses gagal, terdapat data yang sama'
                    ]);
                }
                $cekItemNameOption                      =  new Adminedit;
                $cekItemNameOption->type                = 'item_name';
                $cekItemNameOption->user_id             = Auth::user()->id;
                $cekItemNameOption->data                = $request->itemname;
                
                $cekItemNameOption->save();


                if (!$cekItemNameOption->save()) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 400,
                        'msg'    => 'Proses gagal'
                    ]);
                }


                DB::commit();
                return response()->json([
                    'msg'       => 'Berhasil Simpan Item Name',
                    'status'    => 200,
                    'id'        => $cekItemNameOption->id
                ]);

        } else if ($request->key == 'plastikGroup') {
                $cekDuplicate                           = Adminedit::where('type', 'plastik_group')->where('data', $request->plastikGroup)->first();
                if ($cekDuplicate){
                    DB::rollBack();
                    return response()->json([
                        'status' => 400,
                        'msg'    => 'Proses gagal, terdapat data yang sama'
                    ]);
                }
                $cekItemNameOption                      =  new Adminedit;
                $cekItemNameOption->type                = 'plastik_group';
                $cekItemNameOption->user_id             = Auth::user()->id;
                $cekItemNameOption->data                = $request->plastikGroup;
                
                $cekItemNameOption->save();


                if (!$cekItemNameOption->save()) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 400,
                        'msg'    => 'Proses gagal'
                    ]);
                }


                DB::commit();
                return response()->json([
                    'msg'           => 'Berhasil Simpan Plastik Group',
                    'status'        => 200,
                    'id'            => $cekItemNameOption->id
                ]);

        } else {
            // dd($request->all());

            if ($request->tujuan == "") {
                DB::rollBack();
                return back()->with('status', 2)->with('message', 'Gudang belum dipilih');
            }

            if ($request->result_abf == "") {
                DB::rollBack();
                return back()->with('status', 2)->with('message', 'Berat tidak boleh kosong');
            }

            if ($request->id) {
                $data                           =   Abf::find($request->id);
                if ($data) {
                    $item_finish                =   Item::where('nama', str_replace(' FROZEN', '', $data->item_name) . " FROZEN")
                    
                                                    ->where(function($query){
                                                        $query->where('status', 1);
                                                        $query->orWhere('deleted_at', NULL);
                                                    })->first();
                    // dd($item_finish);
                    // dd(str_replace(' FROZEN', '', $data->item_name)." FROZEN" . ' || ' . $data->item_name);

                    if (!$item_finish) {
                        $sku_lama = Item::find($data->item_id)->sku ?? 0;

                        $sku_baru = substr($sku_lama, 0, 2) . "2" . substr($sku_lama, 3, 7);
                        $item_finish = Item::where('sku', $sku_baru)->where(function($query){
                                            $query->where('status', 1);
                                            $query->orWhere('deleted_at', NULL);
                                        })
                                        ->first();
                    }

                    if ($item_finish) {
                        $parting                    = $request->parting ?? '0';

                        $gudang                     =   Product_gudang::where('table_name', 'abf')->where('table_id', $data->id)->where('status', 0)->first() ?? new Product_gudang;

                        if ($parting != '0') {
                            $gudang->parting            =   $parting;
                        } else {
                            $gudang->parting            =   $data->abf_chiller->parting ?? $parting;
                        }
                        
                        $gudang->table_name         =   'abf';
                        $gudang->sub_item           =   Adminedit::where('type', 'item_name')->where('id',$request->subitem)->first()->data ?? 'NONE' ;
                        $gudang->table_id           =   $data->id ;
                        $gudang->product_id         =   $item_finish->id ;
                        $gudang->nama               =   $item_finish->nama ;
                        $gudang->qty_awal           =   $request->qty ;
                        $gudang->berat_awal         =   $request->result_abf ;
                        $gudang->qty                =   $request->qty ;
                        $gudang->berat              =   $request->result_abf ;
                        $gudang->subpack            =   $request->subpack ;
                        $gudang->karung             =   $request->karung ;
                        $gudang->karung_qty         =   $request->karung_qty ?? 1;
                        $gudang->karung_isi         =   $request->karung_isi ?? NULL;
                        $gudang->packaging          =   $request->packaging ;
                        // $gudang->parting            =   $request->parting ?? $data->abf_chiller->parting;
                        $gudang->plastik_group      =   Adminedit::where('type', 'plastik_group')->where('id',$request->plastik_group)->first()->data ?? Item::plastik_group($request->packaging);
                        $gudang->selonjor           =   $data->selonjor ;

                        if ($data->table_name == 'openbalance' && $data->asal_tujuan == 'open_balance') {
                            $gudang->customer_id    =   $request->konsumen ;
                        }else{
                            $gudang->customer_id    =   $data->customer_id ?? $request->konsumen ;
                        }

                        $gudang->palete             =   $request->pallete ;
                        $gudang->expired            =   ($request->expired_custom == "") ? $request->expired : $request->expired_custom;
                        $gudang->gudang_id          =   $request->tujuan ;
                        $gudang->production_date    =   $data->tanggal_masuk ;
                        $gudang->tanggal_kemasan    =   $request->tanggal_kemasan ;
                        $gudang->type               =   ($data->table_name == 'chiller') ? 'bahan-baku' : 'hasil-produksi';
                        $gudang->stock_type         =   $request->stock ;
                        $gudang->asal_abf           =   $request->asal_abf ;
                        $gudang->barang_titipan     =   $request->barang_titipan ;
                        $gudang->grade_item         =   $data->grade_item ;
                        $gudang->jenis_trans        =   'masuk';
                        $gudang->status             =   0;

                        if (!$gudang->save()) {
                            DB::rollBack();
                            return back()->with('status', 2)->with('message', 'Kesalahan simpan data');
                        }

                        $gudang->production_code        =   $request->kode_produksi ?? Gudang::kode_produksi($gudang->id);
                        if (!$gudang->save()) {
                            DB::rollBack();
                            return back()->with('status', 2)->with('message', 'Kesalahan simpan data');
                        }

                        DB::commit();
                        return back()->with('status', 1)->with('message', 'Berhasil Simpan');
                    } else {
                        DB::rollBack();
                        return back()->with('status', 2)->with('message', 'Tidak terdapat item ' . str_replace(' FROZEN', '', $data->item_name) . " FROZEN");
                    }
                } else {
                    DB::rollBack();
                    return back()->with('status', 2)->with('message', 'Kesalahan simpan data');
                }
            } else {
                DB::rollBack();
                return back()->with('status', 2)->with('message', 'Error, tidak ada Request ID');
            }
        }
    }

    public function destroy(Request $request)
    {
        $data   =   Product_gudang::where('id', $request->id)
            ->where('status', 0)
            ->first();

        if ($data) {
            $data->delete();
            return redirect()->route('abf.index')->with('status', 1)->with('message', 'Berhasil dihapus');
        }

        return redirect()->route('abf.index')->with('status', 2)->with('message', 'Data tidak ditemukan');
    }

    public function hapustimbang(Request $request, $id)
    {
        return Product_gudang::find($request->id)->delete();
    }

    public function abf_stock(Request $request)
    {

        $mulai  =   $request->mulai ?? date('Y-m-d', strtotime("-7 days", time()));
        $sampai =   $request->sampai ?? date('Y-m-d');

        $stock  =   Abf::where('jenis', 'masuk')
            ->whereBetween('tanggal_masuk', [$mulai, $sampai])
            ->whereIn('status', [1, 2, 3])
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.pages.abf.abf_diterima', compact('stock', 'mulai', 'sampai'));
    }


    public function abf_diterima(Request $request)
    {
        $mulai  =   $request->tanggal ?? date('Y-m-d', strtotime("-7 days", time()));
        $sampai =   $request->tanggalend ?? date('Y-m-d');

        if ($request->view == "data") {
            // dd($request->all());
            $abf_diterima =   Abf::select('abf.*')
                ->where('abf.jenis', 'masuk')
                ->where(function ($query) use ($request, $mulai, $sampai) {
                    if ($request->tglprodacc == 'true') {
                        $query->whereBetween('abf.created_at', [$mulai . " 00:00:00", $sampai . " 23:59:59"]);
                    } else {
                        $query->whereBetween('abf.tanggal_masuk', [$mulai, $sampai]);
                    }
                })
                ->where(function ($query) use ($request) {
                    if ($request->status == '1') {
                        $query->where('abf.status', $request->status)->where('parent_abf', '=', NULL);
                    }
                    if ($request->status == '2') {
                        $query->where('abf.status', $request->status);
                    }
                })
                ->where(function ($query) use ($request) {
                    if ($request->selonjor == 'true') {
                        $query->where('selonjor', '!=', 'NULL');
                    }
                })
                ->where(function ($query) use ($request) {
                    if ($request->boneless == 'true') {
                        $query->where('item_name', 'like', '%boneless%');
                    }
                })
                ->where(function ($query) use ($request) {
                    if ($request->parting == 'true') {
                        $query->where('item_name', 'like', '%parting%');
                    }
                })
                ->where(function ($query) use ($request) {
                    if ($request->memar == 'true') {
                        $query->where('label', 'like', '%memar%');
                        $query->orWhere('item_name', 'like', '%memar%');
                    }
                })
                ->whereIn('abf.status', [1, 2, 3])
                ->leftJoin('customers', 'customers.id', '=', 'abf.customer_id')
                ->leftJoin('items', 'items.id', '=', 'abf.item_id')
                ->where(function ($query) use ($request) {
                    if ($request->abf_asal_tujuan != '0') {
                        $query->where('abf.asal_tujuan', 'like', '%' . $request->abf_asal_tujuan . '%');
                    }
                })
                ->where(function ($query) use ($request) {
                    if ($request->kategori != '0') {
                        $query->where('items.category_id', $request->kategori);
                    }
                })
                ->where(function ($query) use ($request) {
                    if ($request->cari) {
                        $query->orWhere('item_name', 'like', '%' . $request->cari . '%');
                        $query->orWhere('qty_item', 'like', '%' . $request->cari . '%');
                        $query->orWhere('berat_item', 'like', '%' . $request->cari . '%');
                        $query->orWhere('label', 'like', '%' . $request->cari . '%');
                        $query->orWhere('customers.nama', 'like', '%' . $request->cari . '%');
                    }
                });
            if ($request->field == 'item_name') {
                $abf_diterima =   $abf_diterima->orderBy("item_name", $request->orderby == 'asc' ? 'ASC' : 'DESC');
            }
            if ($request->field == 'stock_qty') {
                $abf_diterima =   $abf_diterima->orderBy("qty_item", $request->orderby == 'asc' ? 'ASC' : 'DESC');
            }
            if ($request->field == 'stock_berat') {
                $abf_diterima =   $abf_diterima->orderBy("berat_item", $request->orderby == 'asc' ? 'ASC' : 'DESC');
            }

            $abf_diterima   =   $abf_diterima->orderBy('abf.id', 'desc');
            $abf_diterima   =  $abf_diterima->paginate(30);

            $total_qty      = $abf_diterima->sum('qty_awal');
            $total_berat    = $abf_diterima->sum('berat_awal');

            $sisa_qty_abf   = 0;
            $sisa_berat_abf = 0;

            foreach ($abf_diterima as $key => $value) {
                $sisa_qty_abf += $value->qty_item;
                $sisa_berat_abf += $value->berat_item;
            }

            return view('admin.pages.abf.component.abf_diterima_view', compact('abf_diterima', 'mulai', 'sampai', 'total_qty', 'total_berat', 'sisa_qty_abf', 'sisa_berat_abf'));
        } else if ($request->key == 'editAbf') {
            $id         = $request->id;
            $data_abf   = Abf::find($id);
            $plastik    = Item::where('category_id', '25')->where('subsidiary', env('NET_SUBSIDIARY', 'EBA'))->where('status', '1')->get();
            // $data_abf   = Abf::with('hasil_timbang_selesai')->where('id',$id)->first();
            // return dd($data_abf);
            return view('admin.pages.abf.component.form_edit_abf', compact('id', 'data_abf', 'plastik'));
        } else {
            return view('admin.pages.abf.component.abf_diterima', compact('mulai', 'sampai'));
        }
    }

    public function batalkan($id)
    {
        $data   =   Abf::find($id);
        $chiller    =   Chiller::where('id', $data->table_id)->first();
        if ($chiller) {
            $chiller->stock_berat   =   $chiller->stock_berat + $data->berat_item;
            $chiller->stock_item    =   $chiller->stock_item + $data->qty_item;
            $chiller->save();
            $data->delete();


            try {
                Chiller::recalculate_chiller($chiller->id);
            } catch (\Throwable $th) {
            }
            return back()->with('status', 1)->with('message', 'Berhasil Dibatalkan, stock dikembalikan');
        } else {
            $data->delete();
            return back()->with('status', 1)->with('message', 'Batalkan timbang ABF');
        }
    }

    public function abf_export(Request $request)
    {

        $stock  = Abf::where('jenis_trans', 'masuk')->where('status', '2')->get();


        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=cgl-chiller-" . date('Y-m-d-H:i:s') . ".csv");
        $fp = fopen('php://output', 'w');
        fputcsv($fp, ["sep=,"]);

        $data = array(
            "No",
            "Nama",
            "No Mobil",
            "Tanggal Produksi",
            "Qty",
            "Berat",
            "Asal Tujuan"
        );
        fputcsv($fp, $data);

        foreach ($stock as $no => $item) :

            $data = array(
                $no + 1,
                $item->item_name,
                $item->no_mobil ?? '',
                $item->tanggal_produksi,
                $item->stock_item,
                str_replace(".", ",", $item->stock_berat),
                $item->tujuan,
            );
            fputcsv($fp, $data);
        endforeach;

        fclose($fp);
    }

    public function abf_nonlb(Request $request)
    {

        $tanggal            =   $request->tanggal ?? date('Y-m-d');
        $tanggal_akhir      =   $request->tanggal_akhir ?? date('Y-m-d');

        if (env('NET_SUBSIDIARY', 'CGL') == 'CGL') {
            $data       =   Production::whereIn('purchasing_id', Purchasing::select('id')->whereIn('type_po', ['PO Karkas', 'PO non Karkas']))
                ->whereBetween('prod_tanggal_potong', [$tanggal, $tanggal_akhir])
                ->whereIn('ppic_tujuan', ['abf'])
                ->whereIn('ppic_acc', [2, 3])
                ->where('lpah_status', 1)
                ->where('evis_status', 1)
                ->where('grading_status', 1)
                ->orderBy('no_urut', 'ASC')
                ->get();
        } else {

            $data       =   Production::whereIn('purchasing_id', Purchasing::select('id')->whereIn('type_po', ['PO Karkas', 'PO non Karkas']))
                ->whereBetween('prod_tanggal_potong', [$tanggal, $tanggal_akhir])
                ->whereIn('ppic_tujuan', ['abf', 'chiller'])
                ->whereIn('ppic_acc', [2, 3])
                ->where('lpah_status', 1)
                ->where('evis_status', 1)
                ->where('grading_status', 1)
                ->orderBy('no_urut', 'ASC')
                ->get();
        }

        return view('admin.pages.abf.non_karkas', compact('tanggal', 'data', 'tanggal_akhir'));
    }

    public function selesai(Request $request, $id)
    {

        DB::beginTransaction();
        $gudang =   Product_gudang::where('table_name', 'abf')
            ->where('table_id', $id)
            ->where('status', '0')
            ->get();

        $abf = Abf::where('id', $id)->first();

        $tanggal = $request->tanggal ?? date('Y-m-d');

        $total_qty      = 0;
        $total_berat    = 0;

        $karung         = [];
        foreach ($gudang as $row) {
            $total_qty = $total_qty + $row->qty_awal;
            $total_berat = $total_berat + $row->berat_awal;

            $row->production_date     =   $tanggal;
            $row->status     =   2;
            $row->save();

            if ($row->karung != "") {

                if (Item::item_sku($row->karung)) {
                    $karung[]   =   [
                        "type"              =>  "Component",
                        "internal_id_item"  =>  (string)Item::item_sku($row->karung)->netsuite_internal_id,
                        "item"              =>  $row->karung,
                        "description"       =>  (string)Item::item_sku($row->karung)->nama,
                        "qty"               =>  (string)$row->karung_qty,
                    ];
                }
            }
        }

        // HITUNG SELESAI ABF
        $data               =   Abf::find($id);
        $gudang_selesai     =   Product_gudang::where('table_name', 'abf')
                                ->where('table_id', $id)
                                ->where('status', '2')
                                ->get();

        $total_qty_selesai      = 0;
        $total_berat_selesai    = 0;
        foreach ($gudang_selesai as $row) {
            $total_qty_selesai      = $total_qty_selesai + $row->qty_awal;
            $total_berat_selesai    = $total_berat_selesai + $row->berat_awal;
        }

        $data->qty_item     =   $data->qty_awal - $total_qty_selesai;
        $data->berat_item   =   $data->berat_awal - $total_berat_selesai;
        $data->status       =   (($data->total_berat_selesai - $total_berat_selesai) < 1) ? 2 : 1;
        $data->save();

        // Netsuite

        $finished_good          =   [];
        $component              =   [];
        $proses                 =   [];
        $transfer_awal          =   [];

        $location       =   env('NET_SUBSIDIARY', 'CGL') . " - Storage ABF";
        $id_location    =   Gudang::gudang_netid($location);

        $label          =   'wo-3-abf-cs';

        $bom_kategori   =   Item::find($abf->item_id);
        $item           =   Item::find($abf->item_id);
        $item_assembly  =   env('NET_SUBSIDIARY', 'CGL') . " - AYAM KARKAS FROZEN";

        $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL') . " - AYAM KARKAS FROZEN")
                            ->first();

        if ($bom_kategori) {
            if ($bom_kategori->category_id == "5") {
                $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL') . " - BONELESS BROILER FROZEN")
                    ->first();
                $item_assembly = env('NET_SUBSIDIARY', 'CGL') . " - BONELESS BROILER FROZEN";
            } elseif ($bom_kategori->category_id == "3") {
                $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL') . " - AYAM PARTING MARINASI BROILER FROZEN")
                    ->first();
                $item_assembly = env('NET_SUBSIDIARY', 'CGL') . " - AYAM PARTING MARINASI BROILER FROZEN";
            } elseif ($bom_kategori->category_id == "2") {
                $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL') . " - AYAM PARTING BROILER FROZEN")
                    ->first();
                $item_assembly = env('NET_SUBSIDIARY', 'CGL') . " - AYAM PARTING BROILER FROZEN";
            } elseif ($bom_kategori->category_id == "4" || $bom_kategori->category_id == "6") {

                $bom_list = [
                    env('NET_SUBSIDIARY') . ' - BY PRODUCT 1 FROZEN',
                    env('NET_SUBSIDIARY') . ' - BY PRODUCT 2 FROZEN',
                    env('NET_SUBSIDIARY') . ' - BY PRODUCT 3 FROZEN',
                    env('NET_SUBSIDIARY') . ' - BY PRODUCT 4 FROZEN',
                    env('NET_SUBSIDIARY') . ' - BY PRODUCT 5 FROZEN',
                    env('NET_SUBSIDIARY') . ' - BY PRODUCT 6 FROZEN',
                    env('NET_SUBSIDIARY') . ' - BY PRODUCT 7 FROZEN',
                    env('NET_SUBSIDIARY') . ' - BY PRODUCT 8 FROZEN'
                ];

                try {
                    //BOm untuk item by product, dari evis maupun boneless

                    $bom            =   Bom::select('bom.*')
                        ->join('bom_item', 'bom.id', '=', 'bom_item.bom_id')
                        ->whereIn('bom_name', $bom_list)
                        ->where('bom_item.sku', $item->sku)
                        ->first();

                    $item_assembly  = $bom->bom_name;
                } catch (\Throwable $th) {
                    //throw $th;
                    $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL') . " - EVIS FROZEN")
                        ->first();
                    $item_assembly = env('NET_SUBSIDIARY', 'CGL') . " - EVIS FROZEN";
                }
            } else {
                $bom            =   Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL') . " - AYAM KARKAS FROZEN")
                    ->first();
                $item_assembly = env('NET_SUBSIDIARY', 'CGL') . " - AYAM KARKAS FROZEN";
            }
        }

        // return $bom;

        $nama_assembly  =   $bom->bom_name;
        $id_assembly    =   $bom->netsuite_internal_id;
        $bom_id         =   $bom->id;

        $transfer_awal[] =   [
            "internal_id_item"  =>  (string)$item->netsuite_internal_id,
            "item"              =>  (string)$item->sku,
            "qty_to_transfer"   =>  (string)$total_berat
        ];

        $component[]        =   [
            "type"              =>  "Component",
            "internal_id_item"  =>  (string)$item->netsuite_internal_id,
            "item"              =>  (string)$item->sku,
            "description"       =>  (string)$item->nama,
            "qty"               =>  (string)$total_berat,
        ];

        $item_baru   =   Item::where('nama', str_replace(" FROZEN", "", $item->nama) . ' FROZEN')->first();

        if (!$item_baru) {
            $sku_lama = Item::find($data->item_id)->sku ?? 0;

            $sku_baru = substr($sku_lama, 0, 2) . "2" . substr($sku_lama, 3, 7);
            $item_baru = Item::where('sku', $sku_baru)->where(function($query){
                            $query->where('status', 1);
                            $query->orWhere('deleted_at', NULL);
                        })->first();
        }

        $wo_id = NULL;
        $wob_id = NULL;
        // dd($abf->abf_chiller->tanggal_produksi);
        if (strpos($item->nama, 'FROZEN') !== false) {

            if ($data->asal_tujuan == "kepala_produksi" || $data->type == "gabungan") {

                // ===================    TRANSFER INVENTORY IN FINISHED GOOD TO ABF    ===================
                $nama_tabel     =   "chiller";
                $id_tabel       =   $abf->table_id;

                $from           =   Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL') . " - Chiller Finished Good");

                $label          =   "ti_fg_abf";
                $to             =   Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL') . " - Storage ABF");
                $location_from       =   env('NET_SUBSIDIARY', 'CGL') . " - Chiller Finished Good";
                $id_location_from    =   Gudang::gudang_netid($location);
                if ($data->asal_tujuan != 'order_karkas_frozen') {

                    if ($data->asal_tujuan == 'retur' || $data->asal_tujuan == 'open_balance') {
                        $ti_awal  = Netsuite::transfer_inventory_doc($nama_tabel, $id_tabel, $label, $id_location_from, $location_from, $from, $to, $transfer_awal, null, $abf->tanggal_masuk ?? date('Y-m-d'), "ABF-" . $abf->id);

                    } else {
                        $ti_awal  = Netsuite::transfer_inventory_doc($nama_tabel, $id_tabel, $label, $id_location_from, $location_from, $from, $to, $transfer_awal, null, $abf->abf_chiller->tanggal_produksi ?? $abf->tanggal_masuk, "ABF-" . $abf->id);

                    }
                    
                    
                } else {
                    $ti_awal  = Netsuite::transfer_inventory_doc($nama_tabel, $id_tabel, $label, $id_location_from, $location_from, $from, $to, $transfer_awal, null, $abf->tanggal_masuk ?? date('Y-m-d'), "ABF-" . $abf->id);
                }
            }

        } else {

            // dd($total_berat_selesai);
            $beratGudangAkhir     =   Product_gudang::where('table_name', 'abf')
                                        ->select('berat_awal')
                                        ->where('table_id', $id)
                                        ->where('status', '2')
                                        ->orderBy('id', 'desc')
                                        ->first();

            foreach ($bom->bomproses as $row) {
                $proses[]   =   [
                    "type"              =>  "Component",
                    "internal_id_item"  =>  (string)Item::item_sku($row->sku)->netsuite_internal_id,
                    "item"              =>  $row->sku,
                    "description"       =>  (string)Item::item_sku($row->sku)->nama,
                    // "qty"               =>  ($row->qty_per_assembly * $abf->berat_item) INI LAMA
                    "qty"               =>  ($row->qty_per_assembly * $beratGudangAkhir->berat_awal)
                ];
            }

            $finished_good[]         =   [
                "type"              =>  "Finished Goods",
                "internal_id_item"  =>  (string)$item_baru->netsuite_internal_id,
                "item"              =>  (string)$item_baru->sku,
                "description"       =>  (string)$item_baru->nama,
                "qty"               =>  (string)$total_berat,
            ];


            $produksi       =   array_merge($component, $proses, $finished_good, $karung);


            // ===================    TRANSFER INVENTORY IN FINISHED GOOD    ===================
            $nama_tabel     =   "chiller";
            $id_tabel       =   $abf->table_id;

            $from           =   Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL') . " - Chiller Finished Good");

            $label          =   "ti_fg_abf";
            $to             =   Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL') . " - Storage ABF");
            $location_from       =   env('NET_SUBSIDIARY', 'CGL') . " - Chiller Finished Good";
            $id_location_from    =   Gudang::gudang_netid($location);

            if ($data->asal_tujuan != 'retur' && $data->asal_tujuan !=' open_balance') {
                if ($data->asal_tujuan != 'order_karkas_frozen') {

                    if ($data->asal_tujuan == 'retur' || $data->asal_tujuan == 'open_balance') {
                        $ti_awal  = Netsuite::transfer_inventory_doc($nama_tabel, $id_tabel, $label, $id_location_from, $location_from, $from, $to, $transfer_awal, null, $abf->tanggal_masuk ?? date('Y-m-d'), "ABF-" . $abf->id);

                    } else {
                        $ti_awal  = Netsuite::transfer_inventory_doc($nama_tabel, $id_tabel, $label, $id_location_from, $location_from, $from, $to, $transfer_awal, null, $abf->abf_chiller->tanggal_produksi ?? $abf->tanggal_masuk, "ABF-" . $abf->id);

                    }

                } else {
                    $ti_awal  = Netsuite::transfer_inventory_doc($nama_tabel, $id_tabel, $label, $id_location_from, $location_from, $from, $to, $transfer_awal, null, $abf->tanggal_masuk ?? date('Y-m-d'), "ABF-" . $abf->id);
                }
            }
            $label  =   'wo-3-abf-cs';
            if ($data->asal_tujuan == 'retur') {
                $wo     =   Netsuite::work_order_doc('abf', $abf->id, $label, $id_assembly, $item_assembly, $id_location, $location, $produksi, null, $data->returitem->to_retur->tanggal_retur, "ABF-" . $abf->id);

            } else {
                if ($data->asal_tujuan != 'order_karkas_frozen') {
                    if ($data->asal_tujuan == 'retur' || $data->asal_tujuan == 'open_balance') {
                        $wo     =   Netsuite::work_order_doc('abf', $abf->id, $label, $id_assembly, $item_assembly, $id_location, $location, $produksi, $ti_awal->id, $abf->tanggal_masuk ?? date('Y-m-d'), "ABF-" . $abf->id);

                    } else {
                        $wo     =   Netsuite::work_order_doc('abf', $abf->id, $label, $id_assembly, $item_assembly, $id_location, $location, $produksi, $ti_awal->id, $abf->abf_chiller->tanggal_produksi ?? $abf->tanggal_masuk, "ABF-" . $abf->id);

                    }

                } else {
                    $wo     =   Netsuite::work_order_doc('abf', $abf->id, $label, $id_assembly, $item_assembly, $id_location, $location, $produksi, $ti_awal->id, $abf->tanggal_masuk ?? date('Y-m-d'), "ABF-" . $abf->id);
                }
            }
            $label  =   'wo-3-build-abf-cs';
            $total  =   $total_berat;

            if ($data->asal_tujuan != 'order_karkas_frozen') {

                if ($data->asal_tujuan == 'retur' || $data->asal_tujuan == 'open_balance') {
                    $wob    =   Netsuite::wo_build_doc('abf', $abf->id, $label, $id_assembly, $item_assembly, $id_location, $location, $total, $produksi, $wo->id, $abf->tanggal_masuk ?? date('Y-m-d'), "ABF-" . $abf->id);
                } else {
                    $wob    =   Netsuite::wo_build_doc('abf', $abf->id, $label, $id_assembly, $item_assembly, $id_location, $location, $total, $produksi, $wo->id, $abf->abf_chiller->tanggal_produksi ?? $abf->tanggal_masuk, "ABF-" . $abf->id);

                }

            } else {
                $wob    =   Netsuite::wo_build_doc('abf', $abf->id, $label, $id_assembly, $item_assembly, $id_location, $location, $total, $produksi, $wo->id, $abf->tanggal_masuk ?? date('Y-m-d'), "ABF-" . $abf->id);

            }

            $wob_id = $wob->id;
        }

        foreach ($gudang as $row) {

            $transfer_akhir = [];
            // ===================    TRANSFER INVENTORY IN FINISHED GOOD    ===================
            $nama_tabel     =   "product_gudang";
            $id_tabel       =   $row->id;

            $from           =   Gudang::gudang_netid(env('NET_SUBSIDIARY', 'CGL') . " - Storage ABF");

            $nama_gudang_cs = env('NET_SUBSIDIARY', 'CGL') . " - Cold Storage";
            $gudang_baru    =   Gudang::where('code', $nama_gudang_cs)->first();
            $label          =   "ti_abf_cs_" . str_replace(" ", "-", str_replace("-", "", strtolower($gudang_baru->code)));
            $to             =   $gudang_baru->netsuite_internal_id;
            $location_from       =   env('NET_SUBSIDIARY', 'CGL') . " - Storage ABF";
            $id_location_from    =   Gudang::gudang_netid($location);

            $transfer_akhir[] =   [
                "internal_id_item"  =>  (string)$item_baru->netsuite_internal_id,
                "item"              =>  (string)$item_baru->sku,
                "qty_to_transfer"   =>  (string)$row->berat_awal
            ];
            if ($data->asal_tujuan != 'order_karkas_frozen') {
                if ($data->asal_tujuan == 'retur' || $data->asal_tujuan == 'open_balance') {
                    Netsuite::transfer_inventory_doc($nama_tabel, $id_tabel, $label, $id_location_from, $location_from, $from, $to, $transfer_akhir, $wob_id, $abf->tanggal_masuk ?? date('Y-m-d'), "ABF-" . $abf->id);

                } else {
                    Netsuite::transfer_inventory_doc($nama_tabel, $id_tabel, $label, $id_location_from, $location_from, $from, $to, $transfer_akhir, $wob_id, $abf->abf_chiller->tanggal_produksi ?? $abf->tanggal_masuk, "ABF-" . $abf->id);

                }

            } else {
                Netsuite::transfer_inventory_doc($nama_tabel, $id_tabel, $label, $id_location_from, $location_from, $from, $to, $transfer_akhir, $wob_id, $abf->tanggal_masuk ?? date('Y-m-d'), "ABF-" . $abf->id);
            }
        }

        DB::commit();

        return redirect(url('admin/abf#custom-tabs-diterima'))->with('status', 1)->with('message', 'Data Berhasil diproses');
    }

    public function inject()
    {
        foreach (Abf::where('tanggal_masuk', NULL)->get() as $row) {
            $row->tanggal_masuk =   $row->created_at;
            $row->save();
        }
        return redirect()->route('abf.index');
    }

    public function netsuite(Request $request)
    {
        $tanggal1   =   $request->tanggalmulai ?? date('Y-m-d', strtotime("-7 days", time()));
        $tanggal2   =   $request->tanggalend ?? date('Y-m-d');
        $netsuite   =   Netsuite::whereBetween('trans_date', [$tanggal1, $tanggal2])
            ->where(function ($query) {
                $query->orWhere('label', 'like', '%ti_abf_cs%')
                    ->orWhere('label', 'ti_fg_abf_cs')
                    ->orWhere('label', 'like', '%wo-3%');
            })
            ->get();

        return view('admin.pages.checker.netsuite', compact('netsuite', 'request'));
    }

    public function chiller_abf_stock(Request $request)
    {
        // dd($request->all());
        $tanggal        =   $request->tanggal ?? date('Y-m-d');
        $tanggalend     =   $request->tanggalend ?? date('Y-m-d');
        
        if ($request->field == 'item_name') {
            $ordering   =   "item_name";
        }else
        if ($request->field == 'stock_item') {
            $ordering   =   "stock_item";
        }else
        if ($request->field == 'stock_berat') {
            $ordering   =   "stock_berat";
        }else
        if ($request->field == 'tanggal_produksi') {
            $ordering   =   "tanggal_produksi";
        }else{
            $ordering   =   "item_name";
        }

        $orderby        =   $request->orderby == 'asc' ? 'ASC' : 'DESC';
        $filterstatus   =   $request->status == 'ready' ? 'ready' : 'dipindahkan';

        $sql            =   Chiller::select('chiller.*')
                                    ->leftJoin('items', 'items.id', '=', 'chiller.item_id')
                                    ->leftJoin('customers', 'customers.id', '=', 'chiller.customer_id')
                                    ->whereBetween('tanggal_produksi', [$tanggal, $tanggalend])
                                    ->where('chiller.type','hasil-produksi')
                                    ->where('chiller.jenis','masuk')
                                    ->where(function($query) use ($request){
                                        if ($request->abf == "true") {
                                            $query->where('chiller.kategori', NULL);
                                        } else {
                                            $query->where('chiller.kategori', "1");
                                        }
                                    })
                                    ->where(function ($query) use ($request) {
                                        if ($request->selonjor == 'true') {
                                            $query->where('selonjor', '!=', NULL);
                                        }
                                    })
                                    ->where(function ($query) use ($request) {
                                        if ($request->memar == 'true') {
                                            $query->where('label', 'like', '%memar%');
                                            $query->orWhere('item_name', 'like', '%memar%');
                                        }
                                    })
                                    ->where(function ($query) use ($request) {
                                        if ($request->parting == 'true') {
                                            $query->where('item_name', 'like', '%parting%');
                                        }
                                    })
                                    ->where(function ($query) use ($request) {
                                        if ($request->boneless == 'true') {
                                            $query->where('item_name', 'like', '%boneless%');
                                        }
                                    })
                                    ->where(function ($query) use ($request) {
                                        if ($request->kategori != '0') {
                                            $query->where('items.category_id', $request->kategori);
                                        }
                                    })
                                    ->where(function($query) use ($request){
                                        if ($request->cari) {
                                            $query->orWhere('item_name', 'like', '%' . $request->cari . '%');
                                            // $query->orWhere('stock_item', 'like', '%' . $request->cari . '%');
                                            // $query->orWhere('stock_berat', 'like', '%' . $request->cari . '%');
                                            // $query->orWhere('tanggal_produksi', 'like', '%' . $request->cari . '%');
                                            $query->orWhere('label', 'like', '%' . $request->cari . '%');
                                            $query->orWhere('customers.nama', 'like', '%' . $request->cari . '%');
                                        }
                                    })
                                    ->where(function($query) use ($request){
                                        if ($request->status == 'ready') {
                                            $query->where('stock_berat', '>', 0);
                                        }
                                        if ($request->status == 'dipindahkan') {
                                            $query->where('stock_berat', '<=', 0);
                                        }
                                    })
                                    // ->whereNull('status_cutoff')
                                    ->orderBy($ordering,$orderby);
                                    
        if ($request->action == "data") {

            $master         = clone $sql;
            $arrayData      = $master->get();
            // dd($arrayData);
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
                $ambilabf   = DB::select("select table_id, sum(qty_awal) as total_qty_abf, round(sum(berat_awal),2) as total_berat_abf , count(*) as total_pengambilan
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
                $total_pengambilan      = 0;
                $total_qty_freestock    = 0;
                $total_berat_freestock  = 0;
                $total_qty_musnahkan    = 0;
                $total_berat_musnahkan  = 0;
    
                foreach($alokasi as $val){
                    if($data->id == $val->chiller_out){
                        $total_qty_alokasi  = $val->total_qty_alokasi;
                        $total_berat_alokasi= floatval($val->total_berat_alokasi) ?? 0;
                    }
                }
                foreach($ambilabf as $valabf){
                    if($data->id == $valabf->table_id){
                        $total_qty_abf      = $valabf->total_qty_abf;
                        $total_berat_abf    = floatval($valabf->total_berat_abf) ?? 0;
                        $total_pengambilan  = floatval($valabf->total_pengambilan) ?? 0;
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
                    "id"                    => $data->id,
                    "production_id"         => $data->production_id,
                    "table_name"            => $data->table_name,
                    "table_id"              => $data->table_id,
                    "asal_tujuan"           => $data->asal_tujuan,
                    "item_id"               => $data->item_id,
                    "item_name"             => $data->item_name,
                    "jenis"                 => $data->jenis,
                    "type"                  => $data->type,
                    "kategori"              => $data->kategori,
                    "regu"                  => $data->regu,
                    "label"                 => $data->label,
                    "plastik_sku"           => $data->plastik_sku,
                    "plastik_nama"          => $data->plastik_nama,
                    "plastik_qty"           => $data->plastik_qty,
                    "plastik_group"         => $data->plastik_group,
                    "parting"               => $data->parting,
                    "sub_item"              => $data->sub_item,
                    "selonjor"              => $data->selonjor,
                    "kode_produksi"         => $data->kode_produksi,
                    "unit"                  => $data->unit,
                    "customer_id"           => $data->customer_id,
                    "customer_name"         => $data->konsumen->nama ?? "#",
                    "qty_item"              => $data->qty_item,
                    "berat_item"            => floatval($data->berat_item),
                    "tanggal_potong"        => $data->tanggal_potong,
                    "no_mobil"              => $data->no_mobil,
                    "tanggal_produksi"      => $data->tanggal_produksi,
                    "keranjang"             => $data->keranjang,
                    "berat_keranjang"       => $data->berat_keranjang,
                    "stock_item"            => $data->stock_item,
                    "stock_berat"           => floatval($data->stock_berat),
                    "status"                => $data->status,
                    "status_cutoff"         => $data->status_cutoff,
                    "key"                   => $data->key,
                    "created_at"            => $data->created_at ? date('Y-m-d H:i:s', strtotime($data->created_at)) : null,
                    "updated_at"            => $data->updated_at ? date('Y-m-d H:i:s', strtotime($data->updated_at)) : null,
                    "deleted_at"            => $data->deleted_at ? date('Y-m-d H:i:s', strtotime($data->deleted_at)) : null,
                    "nama"                  => $data->nama,
                    'ambil_abf'             => $total_pengambilan,
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
            // dd($stock);
            $chiller_fg                 = Applib::paginate($stock,10);
            // dd($chiller_fg);

            $sumqty                     = 0;
            $sumberat                   = 0;
            $sisaqty                    = 0;
            $sisaberat                  = 0;
            $inboundqtyabf              = 0;
            $inboundberatabf            = 0;
            foreach($chiller_fg as $item){
                $sumqty                 += $item->qty_item;
                $sumberat               += $item->berat_item;
                $sisaqty                += $item->qty_item - $item->total_qty_alokasi - $item->total_qty_freestock - $item->total_qty_abf - $item->total_qty_musnahkan;
                $sisaberat              += $item->berat_item - $item->total_berat_alokasi - $item->total_berat_freestock - $item->total_berat_abf - $item->total_berat_musnahkan;
                $inboundqtyabf          += $item->total_qty_abf;
                $inboundberatabf        += $item->total_berat_abf;
            }
            return view('admin.pages.abf.component.chiller_fg_view', compact('tanggal', 'tanggalend', 'chiller_fg','sumqty','sumberat','sisaqty','sisaberat','inboundqtyabf','inboundberatabf','filterstatus'));
        }
    }

    public function chiller_abf_stock_lama(Request $request)
    {

        $tanggal        =   $request->tanggal ?? date('Y-m-d');
        $tanggalend     =   $request->tanggalend ?? date('Y-m-d');

        $chiller_fg =   Chiller::select('chiller.*')
            ->where(function ($query) use ($request) {
                if ($request->action == 'unduh') {
                    $query->where(function ($query) use ($request) {
                        $query->where('chiller.jenis', 'keluar');
                        $query->where('asal_tujuan', 'abf');
                    });

                    $query->orWhere(function($query) use ($request) {
                        $query->where('chiller.type', 'hasil-produksi');
                        $query->where('chiller.jenis', 'masuk');
                    });

                    $query->orderBy('table_id', 'ASC');

                } else {
                    $query->where('chiller.type', 'hasil-produksi');
                    $query->where('chiller.jenis', 'masuk');
                }
            })

            ->leftJoin('items', 'items.id', '=', 'chiller.item_id')
            ->leftJoin('customers', 'customers.id', '=', 'chiller.customer_id')
            ->whereBetween('tanggal_produksi', [$tanggal, $tanggalend])
            ->where(function ($query) use ($request) {
                if ($request->action == 'unduh') {
                    
                } else {
                    if ($request->abf == "true") {
                        $query->where('chiller.kategori', NULL);
                    } else {
                        $query->where('chiller.kategori', "1");
                    }
                }
            })
            ->where(function ($query) use ($request) {
                if ($request->selonjor == 'true') {
                    $query->where('selonjor', '!=', NULL);
                }
            })
            ->where(function ($query) use ($request) {
                if ($request->memar == 'true') {
                    $query->where('label', 'like', '%memar%');
                    $query->orWhere('item_name', 'like', '%memar%');
                }
            })
            ->where(function ($query) use ($request) {
                if ($request->parting == 'true') {
                    $query->where('item_name', 'like', '%parting%');
                }
            })
            ->where(function ($query) use ($request) {
                if ($request->boneless == 'true') {
                    $query->where('item_name', 'like', '%boneless%');
                }
            })
            ->where(function ($query) use ($request) {
                if ($request->kategori != '0') {
                    $query->where('items.category_id', $request->kategori);
                }
            })
            ->where(function ($query) use ($request) {
                if ($request->cari) {
                    $query->orWhere('item_name', 'like', '%' . $request->cari . '%');
                    $query->orWhere('stock_item', 'like', '%' . $request->cari . '%');
                    $query->orWhere('stock_berat', 'like', '%' . $request->cari . '%');
                    $query->orWhere('tanggal_produksi', 'like', '%' . $request->cari . '%');
                    $query->orWhere('label', 'like', '%' . $request->cari . '%');
                    $query->orWhere('customers.nama', 'like', '%' . $request->cari . '%');
                }
            })
            ->where(function ($query) use ($request) {
                if ($request->action != 'unduh') {
                    if ($request->status == 'ready') {
                        $query->where('stock_berat', '>', 0);
                    }
                    if ($request->status == 'dipindahkan') {
                        $query->where('stock_berat', '<=', 0);
                    }
                }
            });

        if ($request->field == 'item_name') {
            $chiller_fg =   $chiller_fg->orderBy("item_name", $request->orderby == 'asc' ? 'ASC' : 'DESC');
        }

        if ($request->field == 'stock_item') {
            $chiller_fg =   $chiller_fg->orderBy("stock_item", $request->orderby == 'asc' ? 'ASC' : 'DESC');
        }

        if ($request->field == 'stock_berat') {
            $chiller_fg =   $chiller_fg->orderBy("stock_berat", $request->orderby == 'asc' ? 'ASC' : 'DESC');
        }

        if ($request->field == 'tanggal_produksi') {
            $chiller_fg =   $chiller_fg->orderBy("tanggal_produksi", $request->orderby == 'asc' ? 'ASC' : 'DESC');
        }

        if ($request->action == 'unduh') {
            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=Chiller to ABF " . $tanggal . " - " . $tanggalend . ".csv");
            $fp = fopen('php://output', 'w');
            fputcsv($fp, ["sep=,"]);

            $data = array(
                "No",
                "Chiller ID",
                "Tanggal",
                "Item",
                "Customer",
                "Plastik",
                "Regu",
                "Qty/Ekor/Pcs",
                "Berat Item",
                "Sisa Qty/Ekor/Pcs",
                "Sisa Berat Item",
                "Asal",
                "Status",
                "Tipe",
            );
            fputcsv($fp, $data);

            foreach ($chiller_fg->get() as $i => $item) {

                $exp = json_decode($item->label);

                $plastik = "";
                $customer = "";
                if ($exp) {
                    if ($exp->plastik->jenis ?? '') :
                        $plastik = $exp->plastik->jenis . " ( " . $exp->plastik->qty . ")";
                    endif;
                    if (isset($exp->additional)) :
                        $customer = $exp->sub_item;
                    endif;

                    $data = array(
                        ++$i,
                        $item->id,
                        $item->tanggal_produksi,
                        $item->item_name,
                        $customer,
                        $plastik,
                        ($item->regu ?? ''),
                        str_replace(".", ",", $item->qty_item),
                        str_replace(".", ",", $item->berat_item),
                        str_replace(".", ",", $item->stock_item),
                        str_replace(".", ",", $item->stock_berat),
                        $item->jenis ?: $item->chilprod->prodpur->nama_po,
                        $item->tujuan,
                        str_replace("-", " ", $item->type)
                    );
                    fputcsv($fp, $data);
                }
            }

            fclose($fp);
            return "";
        }                            
        if ($request->action == "data") {

            $cloneqty        =   clone $chiller_fg->get();
            $cloneberat      =   clone $chiller_fg->get();

            // dd($cloneberat);
            $sisaqty = 0;
            $sisaberat = 0;
            foreach ($cloneqty as $key => $value) {
                $sisaqty += $value->stock_item;
                $sisaberat += $value->stock_berat;
            }

            $sumqty        =   $cloneqty->sum('qty_item');
            $sumberat      =   $cloneberat->sum('berat_item');

            $chiller_fg =   $chiller_fg->paginate(15);
            // dd($chiller_fg);
            return view('admin.pages.abf.component.chiller_fg_view', compact('tanggal', 'chiller_fg', 'tanggalend', 'sumqty', 'sumberat', 'sisaqty', 'sisaberat'));
        }
    }

    public function chiller_kirim_abf(Request $request)
    {
        $item_jumlah            =   $request->item_jumlah;
        $item_berat             =   $request->item_berat;

        DB::beginTransaction();


        $chiller                =   Chiller::find($request->chiller);
        // return response()->json(
        // [
        //     'request' => $request->all(),
        //     'chiller' => $chiller
        // ]
        // );

        $sisaQty                = Chiller::ambilsisachiller($chiller->id,'qty_item','qty','bb_item');  
        $sisaberat              = Chiller::ambilsisachiller($chiller->id,'berat_item','berat','bb_berat');  
        $convertSisaBerat       = number_format((float)$sisaberat, 2, '.', '');
        if ($item_jumlah > $sisaQty || $item_berat > $convertSisaBerat) {
            DB::rollBack();
            $result['status']   =   400;
            $result['msg']      =   "Proses gagal, pengambilan melebihi stock";
            return $result;

        // if (strval($item_jumlah) > strval($chiller->stock_item) || strval($item_berat) > strval($chiller->stock_berat)) {
        //     DB::rollBack();
        //     $result['status']   =   400;
        //     $result['msg']      =   "Proses gagal, pengambilan melebihi stock";
        //     return $result;

        } else {
            $tanggal                =   $request->tanggal ?? $chiller->tanggal_produksi;
    
            $exp                    =   json_decode($chiller->label);
    
            $abf                    =   new Abf;
            $abf->production_id     =   $chiller->production_id;
            $abf->table_name        =   'chiller';
            $abf->tanggal_masuk     =   $tanggal;
            $abf->table_id          =   $chiller->id;
            $abf->asal_tujuan       =   'kepala_produksi';
            $abf->no_mobil          =   $chiller->no_mobil;
            $abf->item_id           =   $chiller->item_id;
            $abf->item_id_lama      =   $chiller->item_id;
            $abf->item_name         =   $chiller->item_name;
            $abf->packaging         =   $exp->plastik->jenis ?? "";
            $abf->selonjor          =   $chiller->selonjor;
            $abf->customer_id       =   $chiller->customer_id;
            $abf->jenis             =   'masuk';
            $abf->type              =   'free';
            $abf->qty_awal          =   $item_jumlah;
            $abf->berat_awal        =   $item_berat;
            $abf->qty_item          =   $item_jumlah;
            $abf->berat_item        =   $item_berat;
    
            $abf->status            =   '3';
    
            if (!$abf->save()) {
                DB::rollBack();
                $result['status']   =   400;
                $result['msg']      =   "Proses gagal";
                return $result;
            }
    
            DB::commit();
    
            $this->chiller_kirim_abf_acc($abf->id);
    
            $result['status']   =   200;
            $result['msg']      =   "Proses Selesai";
            return $result;
        }

    }

    public function chiller_kirim_abf_acc($abf_id)
    {

        DB::beginTransaction();

        $abf = Abf::find($abf_id);

        if ($abf) {

            $chiller                =   Chiller::find($abf->table_id);
            // recalculate stock chiller
            $chiller->stock_berat   =   (float)($chiller->stock_berat - $abf->berat_item);
            $chiller->stock_item    =   (float)($chiller->stock_item - $abf->qty_item);


            if (!$chiller->save()) {
                DB::rollBack();
                $result['status']   =   400;
                $result['msg']      =   "Proses gagal";
                return $result;
            }

            // chiller keluar
            $new_chiller                     =   new Chiller;
            $new_chiller->table_name         =   $chiller->table_name;
            $new_chiller->table_id           =   $chiller->table_id;
            $new_chiller->asal_tujuan        =   'abf';
            $new_chiller->item_id            =   $chiller->item_id;
            $new_chiller->item_name          =   $chiller->item_name;
            $new_chiller->jenis              =   'keluar';
            $new_chiller->label              =   $chiller->label;
            $new_chiller->customer_id        =   $chiller->customer_id;
            $new_chiller->selonjor           =   $chiller->selonjor;
            $new_chiller->tanggal_produksi   =   date("Y-m-d");
            $new_chiller->stock_berat        =   number_format($abf->berat_item,2);
            $new_chiller->berat_item         =   number_format($abf->berat_item,2);
            $new_chiller->stock_item         =   $abf->qty_item;
            $new_chiller->qty_item           =   $abf->qty_item;
            $new_chiller->status             =   4;

            if (!$new_chiller->save()) {
                // DB::rollBack();
                // return redirect()->to(url()->previous() . '#custom-tabs-diterima')->with('status', 2)->with('message', 'Gagal simpan chiller') ;
                DB::rollBack();
                $result['status']   =   400;
                $result['msg']      =   "Proses gagal";
                return $result;
            }

            $abf->status            =   '1';

            if (!$abf->save()) {
                // DB::rollBack();
                // return redirect()->to(url()->previous() . '#custom-tabs-diterima')->with('status', 2)->with('message', 'Gagal simpan abf') ;
                DB::rollBack();
                $result['status']   =   400;
                $result['msg']      =   "Proses gagal";
                return $result;
            }

            DB::commit();

            try {
                Chiller::recalculate_chiller($abf->table_id);
            } catch (\Throwable $th) {
            }

            // return redirect()->to(url()->previous() . '#custom-tabs-diterima')->with('status', 1)->with('message', 'ABF telah dikonfirmasi') ;
            $result['status']   =   200;
            $result['msg']      =   "ABF telah dikonfirmasi";
            return $result;
        }
    }

    public function abf_gabung_item(Request $request)
    {

        if (!$request->selected_id) {
            return redirect(url('admin/abf#custom-tabs-diterima'))->with('status', '2')->with('message', 'Silahkan pilih item yang ingin digabung');
        } else {

            if ($request->key == "gabung") {
                $data_item  = $request->selected_id;
                // $explodeData = explode(',', $data_item);
                // $cekDataAsal = Abf::whereIn('id', $data_id)->groupBy('asal_tujuan')->get('asal_tujuan');
                // if (count($cekDataAsal) > 1) {
                //     return redirect(url('admin/abf#custom-tabs-diterima'))->with('status', '2')->with('message', 'Gagal, asal item berbeda');
                // }
                $cariTanggalTerbaru = Abf::select('tanggal_masuk')->whereIn('id',$data_item)->orderBy('tanggal_masuk','DESC')->first();
                // dd($cariTanggalTerbaru->tanggal_masuk);
                $cekDataAbf = Abf::whereIn('id',$data_item)->groupBy('grade_item')->get('grade_item');
                // dd($cekDataAbf);
                if (count($cekDataAbf) > 1) {
                    return redirect(url('admin/abf#custom-tabs-diterima'))->with('status', '2')->with('message', 'Gagal, Grade Item Berbeda');
                }
                
                DB::beginTransaction();

                $gabung_qty     = 0;
                $gabung_berat   = 0;

                $item_awal      = "";


                foreach ($data_item as $no => $id) {
                    $abf_lama = Abf::find($id);

                    if ($no == "0") {

                        $item_awal      = $abf_lama->item_id;

                        $abf                    =   new Abf;
                        $abf->production_id     =   NULL;
                        $abf->table_name        =   'abf';
                        $abf->table_id          =   NULL;
                        $abf->asal_tujuan       =   'abf';
                        $abf->tanggal_masuk     =   $cariTanggalTerbaru->tanggal_masuk ?? date('Y-m-d');
                        $abf->no_mobil          =   $abf_lama->no_mobil;
                        $abf->item_id           =   $abf_lama->item_id;
                        $abf->item_id_lama      =   $abf_lama->item_id;
                        $abf->item_name         =   $abf_lama->item_name;
                        $abf->packaging         =   $abf_lama->packaging ?? "";
                        $abf->selonjor          =   $abf_lama->selonjor;
                        $abf->customer_id       =   $abf_lama->customer_id;
                        $abf->jenis             =   'masuk';
                        $abf->type              =   'gabungan';
                        $abf->grade_item        =   $abf_lama->grade_item ?? NULL;

                        $abf->status            =   '2';
                        $abf->save();
                    } else {

                        if ($item_awal == $abf_lama->item_id) {
                        } else {
                            DB::rollBack();
                            return redirect(url('admin/abf#custom-tabs-diterima'))->with('status', '2')->with('message', 'Gagal, item berbeda');
                        }
                    }


                    $gabung_qty     = $gabung_qty + $abf_lama->qty_item;
                    $gabung_berat   = $gabung_berat + $abf_lama->berat_item;

                    $abf_lama->tanggal_keluar   = $cariTanggalTerbaru->tanggal_masuk ?? date('Y-m-d');
                    $abf_lama->parent_abf       = $abf->id ?? NULL;
                    $abf_lama->gabung_qty       = $abf_lama->qty_item ?? NULL;
                    $abf_lama->gabung_berat     = $abf_lama->berat_item ?? NULL;
                    $abf_lama->qty_item         = 0;
                    $abf_lama->berat_item       = 0;
                    $abf_lama->save();
                }

                if ($abf) {
                    $abf->qty_item         = $gabung_qty;
                    $abf->berat_item       = $gabung_berat;
                    $abf->qty_awal         = $gabung_qty;
                    $abf->berat_awal       = $gabung_berat;

                    $abf->save();
                }

                DB::commit();

                return redirect(url('admin/abf#custom-tabs-diterima'))->with('status', '1')->with('message', 'Item telah digabungkan');
            } else {
                
                $data_item  = $request->selected_id;
                $explodeData = explode(',', $data_item);
                $data_abf   = Abf::whereIn('id',$explodeData)->get();

                return view('admin.pages.abf.component.abf_gabung_item')
                    ->with('data_abf', $data_abf);
            }
        }
    }

    public function data_tracing_abf($id)
    {
        $product_gudang = Product_gudang::where('table_id', $id)->where('table_name', 'abf')->first();

        if ($product_gudang) {
            return view('admin.pages.abf.data-tracing-abf', compact('product_gudang'));
        }
    }
}
