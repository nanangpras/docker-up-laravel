<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Adminedit;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Customer_address;
use App\Models\DataOption;
use App\Models\Hargakontrak;
use App\Models\Item;
use App\Models\MarketingSO;
use App\Models\MarketingSOList;
use App\Models\Netsuite;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Excel;
use Maatwebsite\Excel\Excel as ExcelExcel;

class GenerateSO extends Controller
{
    public function index(Request $request)
    {

        // $customer           =   Customer::where('nama', '!=', '')
        //                         ->where('netsuite_internal_id', '!=', NULL)
        //                         ->where('netsuite_internal_id', '!=', 0)
        //                         ->where(function($query) {
        //                             $query->where('is_parent', 0)->orWhere('is_parent', NULL);
        //                         })
        //                         ->where('deleted_at', NULL)
        //                         ->where('kategori', '!=', NULL)
        //                         ->orderBy('nama')->get();

        $customer           =   Customer::where('nama', '!=', '')
                                ->where('kode', 'like', '%'. Session::get('subsidiary'). '%')
                                ->where('netsuite_internal_id', '!=', NULL)
                                ->where('netsuite_internal_id', '!=', 0)
                                ->where(function($query) {
                                    $query->where('is_parent', 0)->orWhere('is_parent', NULL);
                                })
                                ->where('deleted_at', NULL)
                                ->where('kategori', '!=', NULL)
                                ->orderBy('nama')->get();

        $getDataOptionFresh =   DataOption::getOption('item_fresh_so');
        $dataOptionFresh    =   explode(', ', $getDataOptionFresh);


        $cloneallitem       =   Item::select('items.*')->join('category', 'category.id', '=', 'items.category_id')->where(function($query) {
                                    $query->orWhere('category_id', '<=', 20);
                                    $query->orWhere('category.slug', 'like', 'ags');
                                    $query->orWhere('category.slug', 'like', 'ags%');
                                })
                                ->where(function ($query) use ($dataOptionFresh) {
                                    foreach ($dataOptionFresh as $data) {
                                        $query->where('items.nama', 'not like', '%'.$data.'%');
                                    }
                                })
                                ->where('items.nama', 'not like', 'AYAM KARKAS BROILER')
                                ->where('items.status', '1');

        $cloneitem          = clone $cloneallitem;
        $clonefrozen        = clone $cloneallitem;
        $clonefresh         = clone $cloneallitem;

        $item               = $cloneitem->get();

        $frozen             = $clonefrozen->where('items.nama', 'like', '%FROZEN%')->orWhere(function ($query) {
                                    $query->orWhere('items.nama', 'like', 'AY - S%');
                                })->where('items.status', '1')->get();
        
        $fresh              = $clonefresh->where('items.nama', 'not like', '%FROZEN%')->orWhere(function ($query) {
                                    $query->orWhere('items.nama', 'like', 'AY - S%');
                                })->where('items.status', '1')->get();



        
        if ($request->key == 'getMostOrdered') {
            $cloneGetElseMostOrdered = clone $cloneallitem;

            $cloneData  = MarketingSOList::join('items', 'items.id', '=', 'marketing_so_list.item_id')->join('marketing_so', 'marketing_so.id', '=', 'marketing_so_list.marketing_so_id')
                            ->where(function ($query) use ($request) {
                                $query ->whereBetween('marketing_so_list.created_at', [Carbon::now()->subMonth(1), Carbon::now()]);
                                if ($request->type == 'fresh') {
                                    $query->where('nama', 'not like', '%FROZEN%');
                                } else {
                                    $query->where('nama', 'like', '%FROZEN%');
                                }
                            })
                            ->where('customer_id', $request->customerId)
                            ->groupBy('item_id');

            $getClone   = clone $cloneData;
            $getClone2  = clone $cloneData;

            $getData    = $getClone->select('item_nama', 'sku', 'item_id', DB::raw('count(marketing_so_list.item_id) as mostItems'))->orderBy('mostItems', 'DESC')->get();
            $getArray   = $getClone2->select('item_nama')->get()->toArray();


            $getItemElseMostOrdered  = $cloneGetElseMostOrdered
                                            ->whereNotIn('items.nama', $getArray)
                                            
                                            ->where(function ($query) use ($request) {
                                                if ($request->type == 'fresh') {
                                                    $query->where('items.nama', 'not like', '%FROZEN%');
                                                } else {
                                                    $query->where('items.nama', 'like', '%FROZEN%');
                                                }
                                            })
                                            
                                            ->orWhere(function ($query) {
                                                $query->orWhere('items.nama', 'like', 'AY - S%');
                                            })->get();

            // dd($getItemElseMostOrdered);


            return response()->json([
                'dataMostOrdered'       => $getData,
                'dataElseMostOrdered'   => $getItemElseMostOrdered
            ]);
        }




        $plastik            =   Item::where('category_id', 25)->where('subsidiary', 'like', '%'. Session::get('subsidiary'). '%')->pluck('nama', 'id') ;
        $search             =   $request->search ?? '';
        $filterCustomer     =   $request->customer ?? '';
        $filterMarketing    =   $request->marketing ?? '';
        $tanggalKirim       =   $request->tanggalkirim ?? 0;
        $filterbatalso      =   $request->filterbatalso ?? 0;
        $filterbatalitemso  =   $request->filterbatalitemso ?? 0;
        $filtereditso       =   $request->filtereditso ?? 0;
        $filterpendingso    =   $request->filterpendingso ?? 0;
        $filtergagalso      =   $request->filtergagalso ?? 0;
        $filterholdso       =   $request->filterholdso ?? 0;
        $filterbyproduct    =   $request->filterbyproduct ?? 0;
        $filterjenis        =   $request->filterjenis ?? 'semua';
        // dd($filterjenis);

        $cust               =   MarketingSO::where(function($query) use ($request) {
                                    if (Auth::user()->account_role != 'superadmin') {
                                        if ($request->key != 'marketing') {
                                            if (User::setIjin(38) && !User::setIjin(40) && !User::setIjin(41)) {
                                                $query->where('user_id', Auth::user()->id) ;
                                            }
                                        }
                                    }
                                })
                                ->where(function($query) use ($search) {
                                    if($search) {
                                        $query->orWhere('memo', 'like', '%' . $search . '%') ;
                                        $query->orWhere('po_number', 'like', '%' . $search . '%') ;
                                        $query->orWhere('no_so', 'like', '%' . $search . '%') ;
                                    }
                                })
                                ->where('marketing_so.subsidiary', Session::get('subsidiary'));
        
        // $cloneCustomer      = clone $cust;
        // $cloneMarketing     = clone $cust;
        
        // $dataCustomer       =   $cloneCustomer->where(function ($qu) use ($tanggalKirim, $request) {
        //                             if($tanggalKirim == "1"){
        //                                 $qu->whereBetween('tanggal_kirim', [($request->tanggal_awal ?? date("Y-m-d")), ($request->tanggal_akhir ?? date("Y-m-d"))]);
        //                             } else {
        //                                 $qu->whereBetween('tanggal_so', [($request->tanggal_awal ?? date("Y-m-d")), ($request->tanggal_akhir ?? date("Y-m-d"))]);
        //                             }
        //                         })
        //                         ->whereNotNull('customer_id')
        //                         ->groupBy('customer_id')
        //                         ->get();

        // $dataMarketing      =   $cloneMarketing->where(function ($qu) use ($tanggalKirim, $request) {
        //                             if($tanggalKirim == "1"){
        //                                 $qu->whereBetween('tanggal_kirim', [($request->tanggal_awal ?? date("Y-m-d")), ($request->tanggal_akhir ?? date("Y-m-d"))]);
        //                             } else {
        //                                 $qu->whereBetween('tanggal_so', [($request->tanggal_awal ?? date("Y-m-d")), ($request->tanggal_akhir ?? date("Y-m-d"))]);
        //                             }
        //                         })
        //                         ->whereNotNull('user_id')
        //                         ->groupBy('user_id')
        //                         ->get();

        if ($request->key == 'harga_kontrak') {
            $data   =   Hargakontrak::where(function($query) use ($request) {
                            $custom =   Customer::find($request->customer) ;
                            if ($custom->parent_id) {
                                $query->whereIn('customer_id', Customer::select('parent_id')->where('id', $custom->id)) ;
                            } else {
                                $query->where('customer_id', $custom->id) ;
                            }
                        })
                        ->where('item_id', $request->item)
                        ->where(function($query) use ($request) {
                            if($request->tanggalawal >= date("Y-m-d")) {
                                $query->where('mulai', '>=', $request->tanggalawal)
                                    ->orWhere('mulai', '<=', $request->tanggalakhir)
                                    ->orWhere('mulai', '>=', $request->tanggalakhir);
                            } else {
                                $query
                                    ->where('mulai', '<=', $request->tanggalakhir);
                            }
                        })
                        ->where('subsidiary', 'like', '%'.Session::get('subsidiary').'%')
                        ->where(function($query) use ($request) {
                            $query->where('sampai', '>=', $request->tanggalawal);
                        })
                        ->get() ;
                
            return view('admin.pages.generate_so.harga_kontrak', compact('data')) ;
        } 
        // else if ($request->key == 'summary') {
            
        //     $clone          =   MarketingSO::whereHas('listItem', function($query) use ($filterjenis, $filterbatalitemso, $filterbyproduct) {
        //                                         if($filterjenis == 'semua' && $filterbyproduct == '1'){
        //                                             $query->whereIn('marketing_so_list.item_id', Item::select('id')->whereIn('category_id',[4,10]) );
        //                                         }
                                                
        //                                         if($filterjenis == 'frozen' && $filterbyproduct == '1'){
        //                                             $query->where('marketing_so_list.item_nama','LIKE','%FROZEN%');
        //                                             $query->whereIn('marketing_so_list.item_id', Item::select('id')->whereIn('category_id',[10]) );
        //                                         }

        //                                         if($filterjenis == 'fresh' && $filterbyproduct == '1'){
        //                                             $query->where('marketing_so_list.item_nama','NOT LIKE','%FROZEN%');
        //                                             $query->whereIn('marketing_so_list.item_id', Item::select('id')->whereIn('category_id',[4]) );
        //                                         }

        //                                         if($filterjenis == 'frozen' && $filterbyproduct == '0'){
        //                                             $query->where('marketing_so_list.item_nama','LIKE','%FROZEN%');
        //                                         } 
                                                
        //                                         if($filterjenis =='fresh' && $filterbyproduct == '0'){
        //                                             $query->where('marketing_so_list.item_nama','NOT LIKE', '%FROZEN%');
        //                                         }

        //                                         if($filterbatalitemso == 1){
        //                                             $query->where('marketing_so_list.deleted_at', '!=' , NULL);
        //                                         }
        //                                     })
        //                                     ->where('marketing_so.subsidiary', Session::get('subsidiary'))
        //                                     ->where(function($query) use ($filterCustomer, $filterMarketing) {
        //                                         if (Auth::user()->account_role != 'superadmin') {
        //                                             if (!User::setIjin(40) && !User::setIjin(41)) {
        //                                                 $query->where('user_id', Auth::user()->id) ;
        //                                             }
        //                                         }
        //                                         if($filterCustomer !== '') {
        //                                             $query->where('customer_id', $filterCustomer) ;
        //                                         }
        //                                         if($filterMarketing !== '') {
        //                                             $query->where('user_id', $filterMarketing);
        //                                         }
        //                                     })
        //                                     ->where(function ($query) use ($tanggalKirim, $request) {
        //                                         if($tanggalKirim == "1"){
        //                                             $query->whereBetween('tanggal_kirim', [($request->tanggal_awal ?? date("Y-m-d")), ($request->tanggal_akhir ?? date("Y-m-d"))]);
        //                                         } else {
        //                                             $query->whereBetween('tanggal_so', [($request->tanggal_awal ?? date("Y-m-d")), ($request->tanggal_akhir ?? date("Y-m-d"))]);
        //                                         }
        //                                     })
        //                                     ->where(function ($query) use ($filterbatalso, $filterpendingso, $filtereditso, $filtergagalso, $filterholdso){
        //                                         if($filterbatalso == "1"){
        //                                             $query->where('status', '0');
        //                                         }
        //                                         if($filterpendingso == "1"){
        //                                             $query->where('status', '1');
        //                                         }
        //                                         if($filtereditso == "1"){
        //                                             $query->where('edited', '>','0');
        //                                         }
        //                                         if($filtergagalso == "1"){
        //                                             $query->where('netsuite_status','0');
        //                                         }
        //                                         if($filterholdso == "1"){
        //                                             $query->where('netsuite_status','4');
        //                                         }
                                            
        //                                     })
        //                                     ->where(function($query) use ($search) {
        //                                         $query->whereHas('socustomer', function ($query) use ($search) {
        //                                             $query->where('nama', 'like', '%' . $search . '%') ;
        //                                         })->orWhere(function($q) use ($search){
        //                                             $q->orWhere('memo', 'like', '%' . $search . '%') ;
        //                                             $q->orWhere('po_number', 'like', '%' . $search . '%') ;
        //                                             $q->orWhere('no_so', 'like', '%' . $search . '%') ;
        //                                         });
        //                                     })
        //                                     ->orderBy('id', 'desc');
        //                                     // dd(Auth::user()->group_role);
        //     $clonedata      = clone $clone;
        //     $clonesemuadata = $clonedata->get();
        //     $clonesumedit   = clone $clone;
        //     $clonesumbatal  = clone $clone;
        //     $conesumpending = clone $clone;
        //     $conesumapprove = clone $clone;
        //     $conesumgagal   = clone $clone;
        //     $conesumhold    = clone $clone;
        //     $cloneberat     = clone $clone;
        //     $clonebatal     = clone $clone;
        //     $clonetotorder  = clone $clone;
        //     $datatotorder   = $clonetotorder->count();
            
        //     $databatal      = $clonebatal->where('status',0)->get();
        //     $sumedit        = $clonesumedit->where('edited', '>', 0)->count();
        //     $sumbatal       = $clonesumbatal->where('status', 0)->count();
        //     $sumpending     = $conesumpending->where('status', 1)->count();
        //     $sumapprove     = $conesumapprove->where('status', 3)->count();
        //     $sumgagal       = $conesumgagal->where('netsuite_status','0')->count();
        //     $sumhold        = $conesumhold->where('netsuite_status','4')->count();
            
        //     $data           = $clonedata->paginate(10);
        //     $totalberat     = 0;
        //     $totalqty       = 0;
        //     $totalbatal     = 0;
        //     $totalfrozen    = 0;
            
        //     $dataArray      = array();
        //     foreach($clonesemuadata as $databaru){
        //         $dataArray[] = $databaru->id;
        //     }
        //     $newData        = $dataArray;
        //     if($newData){
        //         $stringData             = implode(",",$newData);
        //         $ArrayMarketingSOList   = DB::table('marketing_so_list')->select('*')->whereRaw("marketing_so_id IN (".$stringData.")");

        //         $cloneTotalBerat        = clone $ArrayMarketingSOList;
        //         $cloneTotalQty          = clone $ArrayMarketingSOList;
        //         $cloneTotalBatal        = clone $ArrayMarketingSOList;
        //         $totalberat             = $cloneTotalBerat->where('deleted_at',null)->get()->sum('berat');
        //         $totalqty               = $cloneTotalQty->where('deleted_at',null)->get()->sum('qty');
        //         $totalbatal             = $cloneTotalBatal->where('deleted_at','!=',null)->get()->sum('berat');
        //     }

        //     $datatotalfrosen = MarketingSOList::join('marketing_so','marketing_so_list.marketing_so_id','=','marketing_so.id')
        //                                         ->select(DB::raw("SUM(marketing_so_list.berat) AS tot_berat_frozen"))
        //                                         ->where('marketing_so.subsidiary', Session::get('subsidiary'))
        //                                         ->where(function ($query) use ($tanggalKirim, $request) {
        //                                             if($tanggalKirim == "1"){
        //                                                 $query->whereBetween('tanggal_kirim', [($request->tanggal_awal ?? date("Y-m-d")), ($request->tanggal_akhir ?? date("Y-m-d"))]);
        //                                             } else {
        //                                                 $query->whereBetween('tanggal_so', [($request->tanggal_awal ?? date("Y-m-d")), ($request->tanggal_akhir ?? date("Y-m-d"))]);
        //                                             }
        //                                         })
        //                                         ->where(function($query) use($filterbyproduct) {
        //                                             if($filterbyproduct == '1'){
        //                                                 $query->where('marketing_so_list.item_nama','LIKE','%FROZEN%');
        //                                                 $query->whereIn('marketing_so_list.item_id', Item::select('id')->where('category_id','10') );
        //                                             }
        //                                             if($filterbyproduct == '0'){
        //                                                 $query->where('marketing_so_list.item_nama','LIKE','%FROZEN%');
        //                                             }
        //                                         })
        //                                         ->orderBy('marketing_so.id', 'desc')->get();

        //     $datatotalfres  = MarketingSOList::join('marketing_so','marketing_so_list.marketing_so_id','=','marketing_so.id')
        //                                         ->select(DB::raw("SUM(marketing_so_list.berat) AS tot_berat_fres"))
        //                                         ->where('marketing_so.subsidiary', Session::get('subsidiary'))
        //                                         ->where(function ($query) use ($tanggalKirim, $request) {
        //                                             if($tanggalKirim == "1"){
        //                                                 $query->whereBetween('tanggal_kirim', [($request->tanggal_awal ?? date("Y-m-d")), ($request->tanggal_akhir ?? date("Y-m-d"))]);
        //                                             } else {
        //                                                 $query->whereBetween('tanggal_so', [($request->tanggal_awal ?? date("Y-m-d")), ($request->tanggal_akhir ?? date("Y-m-d"))]);
        //                                             }
        //                                         })
        //                                         ->where(function($query) use($filterbyproduct) {
        //                                             if($filterbyproduct == '1'){
        //                                                 $query->where('marketing_so_list.item_nama','NOT LIKE','%FROZEN%');
        //                                                 $query->whereIn('marketing_so_list.item_id', Item::select('id')->where('category_id','4') );
        //                                             }
        //                                             if($filterbyproduct == '0'){
        //                                                 $query->where('marketing_so_list.item_nama','NOT LIKE','%FROZEN%');
        //                                             }
        //                                         })
        //                                         ->orderBy('marketing_so.id', 'desc')->get();

            
        //     if($request->subkey == 'riwayateditSO'){
        //         $id     = $request->id;
        //         return view('admin.pages.generate_so.component.summary.riwayat-so',compact('id'));
        //     } else if ($request->subkey == 'riwayatbatalso'){
        //         $id     = $request->id;
        //         return view('admin.pages.generate_so.component.summary.riwayat-batal', compact('id'));
        //     } else
        //     if($request->subkey == 'downloadsummaryso'){
        //         return response()->json([
        //             'status'    => 1,
        //         ]);
        //     }else
        //     if($request->subkey =='unduhdata'){
        //         $clonedatadownload      = clone $clone;
        //         $data                   = $clonedatadownload->get();

        //         $outputId               = array();
        //         foreach ($data as $key => $row) {
        //             $outputId[]         = $row->id;
        //         }
        //         $stringData             = implode(",",$outputId);
        //         $itemArray              =  DB::table('marketing_so_list as a')
        //                                         ->leftJoin('marketing_so as b','a.marketing_so_id','b.id')
        //                                         ->whereRaw("a.marketing_so_id IN (".$stringData.")")
        //                                         ->select('a.*','b.id as id_marketing_so','b.no_so','b.customer_id','b.status as status_so','b.no_so','b.tanggal_kirim','b.memo as memo_so','b.deleted_at as so_deleted_at','b.po_number','b.tanggal_so','b.user_id')
        //                                         ->orderBy('a.marketing_so_id','DESC')
        //                                         ->get();
        //         $output                 = array();
        //         foreach ($itemArray as $row) {
        //             $getSKU             = Item::getDataFromItem($row->item_id,'sku');
        //             $getNama            = Item::getDataFromItem($row->item_id,'nama');
        //             $getNamaCustomer    = Customer::getDataFromCustomer($row->customer_id,'nama');
        //             $getKategoriProduk  = Item::getDataFromItem($row->item_id,'category_id');
        //             $getNamaMarketing   = User::getDataFromUser($row->user_id,'name');
        //             $getResponTime      = Netsuite::getTimeResponse($row->id_marketing_so,'respon_time');
        //             $getFailedTime      = Netsuite::getTimeResponse($row->id_marketing_so,'failed_time');
        //             $getUpdatedTime     = Netsuite::getTimeResponse($row->id_marketing_so,'update_time');
        //             $output[]           = array(
        //                 'id'                => $row->id,
        //                 'line_id'           => $row->line_id,
        //                 'marketing_so_id'   => $row->marketing_so_id,
        //                 'internal_id_item'  => $row->internal_id_item,
        //                 'item_id'           => $row->item_id,
        //                 'sku'               => $getSKU,
        //                 'nama_item'         => $getNama,
        //                 'item_nama'         => $row->item_nama,
        //                 'parting'           => $row->parting,
        //                 'plastik'           => $row->plastik,
        //                 'bumbu'             => $row->bumbu,
        //                 'memo'              => $row->memo,
        //                 'qty'               => $row->qty,
        //                 'berat'             => $row->berat,
        //                 'harga'             => $row->harga,
        //                 'harga_cetakan'     => $row->harga_cetakan,
        //                 'sales_channel_item'=> $row->sales_channel_item,
        //                 'status'            => $row->status,
        //                 'edited'            => $row->edited,
        //                 'created_at'        => date($row->created_at),
        //                 'updated_at'        => date($row->updated_at),
        //                 'deleted_at'        => $row->deleted_at,
        //                 'customer'          => $getNamaCustomer,
        //                 'status_so'         => $row->status_so,
        //                 'tanggal_so'        => $row->tanggal_so,
        //                 'no_so'             => $row->no_so,
        //                 'tanggal_kirim'     => $row->tanggal_kirim,
        //                 'kategori'          => $getKategoriProduk,
        //                 'no_po'             => $row->po_number,
        //                 'memo_so'           => $row->memo_so,
        //                 'so_deleted_at'     => $row->so_deleted_at,
        //                 'marketing'         => $getNamaMarketing,
        //                 'response_time'     => $getResponTime,
        //                 'failed_time'       => $getFailedTime,
        //                 'updated_time'      => $getUpdatedTime,
        //                 'created_so'        => $row->created_at
        //             );
        //         }
        //         return view('admin.pages.generate_so.component.summary.download-summary-so',compact('output'));
        //     }
            
        //     return view('admin.pages.generate_so.component.summary.detail-summary-so', compact('data','customer','item','plastik', 'cust', 'sumedit', 'sumbatal' ,'sumpending', 'sumapprove', 'sumgagal', 'sumhold','totalberat','totalqty','totalbatal','datatotorder','filterjenis','totalfrozen','datatotalfrosen','datatotalfres'));
        //     // return view('admin.pages.generate_so.summary', compact('data','customer','item','plastik', 'cust', 'sumedit', 'sumbatal' ,'sumpending', 'sumapprove', 'sumgagal', 'sumhold','totalberat','totalqty','totalbatal','datatotorder','filterjenis','totalfrozen','datatotalfrosen','datatotalfres'));

        // } 
        else if ($request->key == 'summary') {
            $clone          =   MarketingSOList::LEFTJOIN('marketing_so','marketing_so.id','marketing_so_list.marketing_so_id')
                                                ->LEFTJOIN('items','items.id','marketing_so_list.item_id')
                                                ->LEFTJOIN('customers','customers.id','marketing_so.customer_id')
                                                ->LEFTJOIN('users','users.id','marketing_so.user_id')
                                                ->select(
                                                    'marketing_so.*',
                                                    'marketing_so_list.id AS id_so_list',
                                                    'marketing_so_list.marketing_so_id AS marketing_so_id_so_list',
                                                    'marketing_so_list.internal_id_item AS internal_id_item_so_list',
                                                    'marketing_so_list.item_id AS item_id_so_list',
                                                    'marketing_so_list.item_nama AS item_nama_so_list',
                                                    'marketing_so_list.parting AS parting_so_list',
                                                    'marketing_so_list.bumbu AS bumbu_so_list',
                                                    'marketing_so_list.plastik AS plastik_so_list',
                                                    'marketing_so_list.memo AS memo_so_list',
                                                    'marketing_so_list.description_item AS description_item_so_list',
                                                    'marketing_so_list.internal_memo AS internal_memo_so_list',
                                                    'marketing_so_list.qty AS qty_so_list',
                                                    'marketing_so_list.berat AS berat_so_list',
                                                    'marketing_so_list.status AS status_so_list',
                                                    'marketing_so_list.edited AS edited_so_list',
                                                    'marketing_so_list.created_at AS created_at_so_list',
                                                    'marketing_so_list.updated_at AS updated_at_so_list',
                                                    'marketing_so_list.deleted_at AS deleted_at_so_list',
                                                    'marketing_so_list.line_id AS line_id_so_list',
                                                    'marketing_so_list.harga AS harga_so_list',
                                                    'marketing_so_list.harga_cetakan AS harga_cetakan_so_list',
                                                    'marketing_so_list.sales_channel_item AS sales_channel_item_so_list',
                                                    'items.nama AS nama_items',
                                                    'items.sku AS sku_items',
                                                    'items.category_id AS category_id_items',
                                                    'customers.nama AS nama_customers',
                                                    'customers.netsuite_internal_id AS netsuite_internal_id_customers',
                                                    'users.name as nama_users'
                                                    )
                                                ->where(function($query) use ($filterjenis, $filterbatalitemso, $filterbyproduct) {
                                                    if($filterjenis == 'semua' && $filterbyproduct == '1'){
                                                        $query->whereIn('category_id',[4,10]);
                                                    }
                                                    
                                                    if($filterjenis == 'frozen' && $filterbyproduct == '1'){
                                                        $query->where('marketing_so_list.item_nama','LIKE','%FROZEN%');
                                                        $query->whereIn('category_id',[10]);
                                                    }

                                                    if($filterjenis == 'fresh' && $filterbyproduct == '1'){
                                                        $query->where('marketing_so_list.item_nama','NOT LIKE','%FROZEN%');
                                                        $query->whereIn('category_id',[4]);
                                                    }

                                                    if($filterjenis == 'frozen' && $filterbyproduct == '0'){
                                                        $query->where('marketing_so_list.item_nama','LIKE','%FROZEN%');
                                                    } 
                                                    
                                                    if($filterjenis =='fresh' && $filterbyproduct == '0'){
                                                        $query->where('marketing_so_list.item_nama','NOT LIKE', '%FROZEN%');
                                                    }

                                                    if($filterbatalitemso == 1){
                                                        $query->where('marketing_so_list.deleted_at', '!=' , NULL);
                                                    }
                                                })
                                                ->where('marketing_so.subsidiary', Session::get('subsidiary'))
                                                ->where(function($query) use ($filterCustomer, $filterMarketing) {
                                                    if (Auth::user()->account_role != 'superadmin') {
                                                        if (!User::setIjin(40) && !User::setIjin(41)) {
                                                            $query->where('marketing_so.user_id', Auth::user()->id) ;
                                                        }
                                                    }
                                                    if($filterCustomer !== '') {
                                                        $query->where('marketing_so.customer_id', $filterCustomer) ;
                                                    }
                                                    if($filterMarketing !== '') {
                                                        $query->where('marketing_so.user_id', $filterMarketing);
                                                    }
                                                })
                                                ->where(function ($query) use ($tanggalKirim, $request) {
                                                    if($tanggalKirim == "1"){
                                                        $query->whereBetween('tanggal_kirim', [($request->tanggal_awal ?? date("Y-m-d")), ($request->tanggal_akhir ?? date("Y-m-d"))]);
                                                    } else {
                                                        $query->whereBetween('tanggal_so', [($request->tanggal_awal ?? date("Y-m-d")), ($request->tanggal_akhir ?? date("Y-m-d"))]);
                                                    }
                                                })
                                                ->where(function ($query) use ($filterbatalso, $filterpendingso, $filtereditso, $filtergagalso, $filterholdso){
                                                    if($filterbatalso == "1"){
                                                        $query->where('marketing_so.status', '0');
                                                    }
                                                    if($filterpendingso == "1"){
                                                        $query->where('marketing_so.status', '1');
                                                    }
                                                    if($filtereditso == "1"){
                                                        $query->where('marketing_so.edited', '>','0');
                                                    }
                                                    if($filtergagalso == "1"){
                                                        $query->where('marketing_so.netsuite_status','0');
                                                    }
                                                    if($filterholdso == "1"){
                                                        $query->where('marketing_so.netsuite_status','4');
                                                    }
                                                
                                                })
                                                ->where(function($query) use ($search) {
                                                    if($search){
                                                        $query->where('customers.nama', 'like', '%' . $search . '%') ;
                                                        $query->orWhere('marketing_so.memo', 'like', '%' . $search . '%') ;
                                                        $query->orWhere('marketing_so.po_number', 'like', '%' . $search . '%') ;
                                                        $query->orWhere('marketing_so.no_so', 'like', '%' . $search . '%') ;
                                                    }
                                                })
                                                ->orderBy('marketing_so.id', 'desc');

            $clonedata                      = clone $clone;
            $clonedataAll                   = clone $clone;
            
            $cloneDataWithTrash             = clone $clone;
            $cloneDataNonTrash              = clone $clone;

            $clonetotalorder                = clone $clone;
            
            $dataGroupBy                    = $clonedata->groupBy('marketing_so.id')->withTrashed()->paginate(10);
            $datatotorder                   = $clonetotalorder->groupBy('marketing_so.id')->withTrashed()->get()->count();
            $data                           = $clonedataAll->withTrashed()->get();
            
            $queryNonTrashed                = $cloneDataNonTrash->get();
            $totalberat                     = 0;
            $totalqty                       = 0;
            $totalbatal                     = 0;
            $datatotalfrozen                = 0;
            $datatotalfresh                 = 0;

            foreach($data as $caridata){
                if($caridata->deleted_at_so_list != NULL){
                    $totalbatal             += $caridata->berat_so_list;
                }else{
                    $totalberat             += $caridata->berat_so_list;
                    $totalqty               += $caridata->qty_so_list;
                }
            }

            foreach($queryNonTrashed as $valuedata){
                if(str_contains($valuedata->item_nama_so_list,'FROZEN')){
                    $datatotalfrozen        += $valuedata->berat_so_list;
                }else{
                    $datatotalfresh         += $valuedata->berat_so_list;
                }
            }  
            
            $sql                            = MarketingSo::select(
                                                DB::raw("
                                                    SUM(CASE WHEN `marketing_so`.`status` = 0 THEN 1 ELSE 0 END) AS databatal,
                                                    SUM(CASE WHEN `marketing_so`.`status` = 1 THEN 1 ELSE 0 END) AS datapending,
                                                    SUM(CASE WHEN `marketing_so`.`status` = 3 THEN 1 ELSE 0 END) AS dataapprove,
                                                    SUM(CASE WHEN `marketing_so`.`edited` > 0 THEN 1 ELSE 0 END) AS dataedit,
                                                    SUM(CASE WHEN `netsuite_status` = 0 THEN 1 ELSE 0 END) AS datagagal,
                                                    SUM(CASE WHEN `netsuite_status` = 4 THEN 1 ELSE 0 END) AS datahold"
                                                )
                                            )
                                            ->where(function ($qu) use ($tanggalKirim, $request) {
                                                if($tanggalKirim == "1"){
                                                    $qu->whereBetween('tanggal_kirim', [($request->tanggal_awal ?? date("Y-m-d")), ($request->tanggal_akhir ?? date("Y-m-d"))]);
                                                } else {
                                                    $qu->whereBetween('tanggal_so', [($request->tanggal_awal ?? date("Y-m-d")), ($request->tanggal_akhir ?? date("Y-m-d"))]);
                                                }
                                            })
                                            ->where('marketing_so.subsidiary', Session::get('subsidiary'))
                                            ->get();

            foreach($sql as $item){
                $databatal                  = $item->databatal;
                $datapending                = $item->datapending;
                $dataapprove                = $item->dataapprove;
                $dataedit                   = $item->dataedit;
                $datagagal                  = $item->datagagal;
                $datahold                   = $item->datahold;
            }
            
            $hitung                         = array(
                'databatal'                 => $databatal ?? 0,
                'datapending'               => $datapending ?? 0,
                'dataapprove'               => $dataapprove ?? 0,
                'dataedit'                  => $dataedit ?? 0,
                'datagagal'                 => $datagagal ?? 0,
                'datahold'                  => $datahold ?? 0,
                'datatotalfrozen'           => $datatotalfrozen,
                'datatotalfresh'            => $datatotalfresh,
                'totalberat'                => $totalberat,
                'totalqty'                  => $totalqty,
                'totalbatal'                => $totalbatal,
                'datatotorder'              => $datatotorder
            );
            
            // $cloneCustomer                  = clone $cust;
            // $cloneMarketing                 = clone $cust;
            
            // $dataCustomer                   =   $cloneCustomer->where(function ($qu) use ($tanggalKirim, $request) {
            //                                         if($tanggalKirim == "1"){
            //                                             $qu->whereBetween('tanggal_kirim', [($request->tanggal_awal ?? date("Y-m-d")), ($request->tanggal_akhir ?? date("Y-m-d"))]);
            //                                         } else {
            //                                             $qu->whereBetween('tanggal_so', [($request->tanggal_awal ?? date("Y-m-d")), ($request->tanggal_akhir ?? date("Y-m-d"))]);
            //                                         }
            //                                     })
            //                                     ->whereNotNull('customer_id')
            //                                     ->groupBy('customer_id')
            //                                     ->get();

            // $dataMarketing                  =   $cloneMarketing->where(function ($qu) use ($tanggalKirim, $request) {
            //                                         if($tanggalKirim == "1"){
            //                                             $qu->whereBetween('tanggal_kirim', [($request->tanggal_awal ?? date("Y-m-d")), ($request->tanggal_akhir ?? date("Y-m-d"))]);
            //                                         } else {
            //                                             $qu->whereBetween('tanggal_so', [($request->tanggal_awal ?? date("Y-m-d")), ($request->tanggal_akhir ?? date("Y-m-d"))]);
            //                                         }
            //                                     })
            //                                     ->whereNotNull('user_id')
            //                                     ->groupBy('user_id')
            //                                     ->get();
                                    
            if($request->subkey == "getfiltercustomer"){
                $cust                       =   $cust->where(function ($qu) use ($tanggalKirim, $request) {
                                                    if($tanggalKirim == "1"){
                                                        $qu->whereBetween('tanggal_kirim', [($request->tanggal_awal ?? date("Y-m-d")), ($request->tanggal_akhir ?? date("Y-m-d"))]);
                                                    } else {
                                                        $qu->whereBetween('tanggal_so', [($request->tanggal_awal ?? date("Y-m-d")), ($request->tanggal_akhir ?? date("Y-m-d"))]);
                                                    }
                                                })
                                                ->whereNotNull('customer_id')
                                                ->groupBy('customer_id')
                                                ->get();
                return view('admin.pages.generate_so.filter_customer', compact('cust','filterCustomer'));
            } else
            if($request->subkey == "getfiltermarketing"){
                $cust                       =   $cust->where(function ($qu) use ($tanggalKirim, $request) {
                                                        if($tanggalKirim == "1"){
                                                            $qu->whereBetween('tanggal_kirim', [($request->tanggal_awal ?? date("Y-m-d")), ($request->tanggal_akhir ?? date("Y-m-d"))]);
                                                        } else {
                                                            $qu->whereBetween('tanggal_so', [($request->tanggal_awal ?? date("Y-m-d")), ($request->tanggal_akhir ?? date("Y-m-d"))]);
                                                        }
                                                    })
                                                    ->whereNotNull('user_id')
                                                    ->groupBy('user_id')
                                                    ->get();
                return view('admin.pages.generate_so.filter_marketing', compact('cust','filterMarketing'));
            }else
            if($request->subkey == 'riwayateditSO'){
                $id     = $request->id;
                return view('admin.pages.generate_so.component.summary.riwayat-so',compact('id'));
            } else if ($request->subkey == 'riwayatbatalso'){
                $id     = $request->id;
                return view('admin.pages.generate_so.component.summary.riwayat-batal', compact('id'));
            } else
            if($request->subkey == 'downloadsummaryso'){
                return response()->json([
                    'status'    => 1,
                ]);
            }else
            if($request->subkey =='unduhdata'){
                $clonedatadownload                  = clone $clone;
                $data                               = $clonedatadownload->get();
                // $clonedatadownloadwithnetsuite      = clone $clone;
                // $datawithnetsuite                   = $clonedatadownloadwithnetsuite->LEFTJOIN('netsuite','netsuite.tabel_id','marketing_so.id')->select('marketing_so.id','respon_time','failed_time','update_time')->get();
                
                foreach ($data as $row) {
                    // foreach($datawithnetsuite as $newdata){
                    //     if($newdata->id == $row->marketing_so_id_so_list){
                    //         $getResponTime     = $newdata->respon_time;
                    //         $getFailedTime     = $newdata->failed_time;
                    //         $getUpdatedTime    = $newdata->update_time;
                    //     }
                    // }
                    $output[]           = array(
                        'id'                => $row->id_so_list,
                        'line_id'           => $row->line_id_so_list,
                        'marketing_so_id'   => $row->marketing_so_id_so_list,
                        'internal_id_item'  => $row->internal_id_item_so_list,
                        'item_id'           => $row->item_id_so_list,
                        'sku'               => $row->sku_items,
                        'nama_item'         => $row->nama_items,
                        'item_nama'         => $row->item_nama_so_list,
                        'parting'           => $row->parting_so_list,
                        'plastik'           => $row->plastik_so_list,
                        'bumbu'             => $row->bumbu_so_list,
                        'memo'              => $row->memo_so_list,
                        'qty'               => $row->qty_so_list,
                        'berat'             => $row->berat_so_list,
                        'harga'             => $row->harga_so_list,
                        'harga_cetakan'     => $row->harga_cetakan_so_list,
                        'sales_channel_item'=> $row->sales_channel_item_so_list,
                        'status'            => $row->status_so_list,
                        'edited'            => $row->edited_so_list,
                        'created_at'        => date($row->created_at_so_list),
                        'updated_at'        => date($row->updated_at_so_list),
                        'deleted_at'        => $row->deleted_at_so_list,
                        'customer'          => $row->nama_customers,
                        'status_so'         => $row->status,
                        'tanggal_so'        => $row->tanggal_so,
                        'no_so'             => $row->no_so,
                        'tanggal_kirim'     => $row->tanggal_kirim,
                        'kategori'          => $row->category_id_items,
                        'no_po'             => $row->po_number,
                        'memo_so'           => $row->memo,
                        'so_deleted_at'     => $row->deleted_at,
                        'marketing'         => $row->nama_users,
                        // 'response_time'     => $getResponTime ?? NULL,
                        // 'failed_time'       => $getFailedTime ?? NULL,
                        // 'updated_time'      => $getUpdatedTime ?? NULL,
                        'created_so'        => $row->created_at
                    );
                }
                return view('admin.pages.generate_so.component.summary.download-summary-so-new',compact('output'));
            }
            
            return view('admin.pages.generate_so.component.summary.detail-summary-so-new', compact('dataGroupBy','data','customer','item','plastik','cust','filterjenis','hitung'));
        }
        else if($request->key == 'editsummary'){
            $data       = MarketingSO::select('marketing_so.*', 'customers.id as customer_id')
                            ->where('marketing_so.id', $request->id)
                            ->where('marketing_so.subsidiary', Session::get('subsidiary'))
                            ->join('customers', 'customers.netsuite_internal_id', '=', 'marketing_so.internal_id_customer')
                            ->first();
            $data_so_list = MarketingSOList::where('marketing_so_id', $request->id)
                            ->get();
            if($data){
                return view('admin.pages.generate_so.component.summary.editsummary', compact('data','customer','item','plastik','data_so_list', 'fresh', 'frozen'));
            }else{
                return back();
            }

        } elseif($request->key == 'editdatasummary'){
            $data       = MarketingSOList::select('marketing_so_list.*', 'items.id as item_id')
                            ->where('marketing_so_list.id', $request->iddatasummary)
                            ->join('items', 'items.netsuite_internal_id', '=', 'marketing_so_list.internal_id_item')
                            ->first();

            return response()->json($data);
        } elseif($request->key == 'customer'){
            $cust = $cust->whereNotNull('customer_id')->groupBy('customer_id')->get();
            return view('admin.pages.generate_so.filter_customer', compact('cust','filterCustomer','filterMarketing'));
        } elseif($request->key == 'marketing'){
            $cust = $cust->groupBy('user_id')->get();
            return view('admin.pages.generate_so.filter_marketing', compact('cust','filterCustomer','filterMarketing'));

        } elseif($request->key == 'batalkanso'){
            DB::beginTransaction() ;

            $dataSO                        = MarketingSO::find($request->id);
            $dataSO->status                = '0';
            $dataSO->netsuite_status       = '0';

            $dataListSO           = MarketingSOList::where('marketing_so_id', $request->id);

            $ns                   = Netsuite::where('tabel', 'marketing_so')->where('tabel_id', $dataSO->id)->first();
            if($ns){
                if($ns->status!="1"){
                    dd($ns);
                    $ns->status = 3;
                    $ns->save();

                    
                }

                $soawal = MarketingSO::find($request->id);
                    $solist             =   MarketingSOList::where('marketing_so_id', $request->id)->withTrashed()->get();
                    $log                =   new Adminedit ;
                    $log->user_id       =   Auth::user()->id ;
                    $log->table_name    =   'marketing_so' ;
                    $log->table_id      =   $request->id ;
                    $log->type          =   'batal' ;
                    $log->activity      =   'marketing' ;
                    $log->content       =   'Data Batal';
                    $log->data          =   json_encode([
                            'header' => $soawal,
                            'list' => $solist
                    ]) ;

                    if (!$log->save()) {
                        DB::rollBack();
                        return back()->with('status', 2)->with('message', 'Proses simpan log gagal');
                    }
            }

            if (!$dataSO->save()) {
                DB::rollBack();
                return back()->with('status', 2)->with('message', 'Proses gagal');
            }

            if(!$dataListSO->delete()){
                DB::rollBack();
                return back()->with('status', 2)->with('message', 'Proses gagal');
            }

            DB::commit();
            return response()->json([
                'msg' => 'Pembatalan SO berhasil',
                'status' => 1
            ]);


        } else
        if($request->key == 'riwayat') {
            $data = Adminedit::where('table_id', $request->id)->get();
            return response()->json($data);

        } else
        if($request->key == 'cekItemRetailCustomer') {
            $dataIdRetail = MarketingSO::where('id', $request->id)->where('customer_id', $request->custValue)->select('id')->where('cabang', 'retailsampingan')->orderBy('id', 'DESC')->first();
            if ($dataIdRetail) {
                $data = MarketingSOList::whereIn('marketing_so_id', $dataIdRetail)->where('cabang_item', 'retail')->get();
                // $data = MarketingSOList::whereIn('marketing_so_id', $dataIdRetail)->get();
                return response()->json($data);

            } else {
                return response()->json(0);
            }

        } else
        if($request->key == 'cekHistoryItemRetailCustomer') {
            $dataIdRetail = MarketingSO::where('customer_id', $request->custValue)->where('cabang', 'retailsampingan')->orderBy('id', 'DESC')->limit(5)->get();
            if ($dataIdRetail) {
                $customer = $request->custValue;
                $keys     = $request->keys;
                return view('admin.pages.generate_so.historySampingan', compact('dataIdRetail', 'keys', 'customer'));

            } else {
                return view('admin.pages.generate_so.historySampingan', compact('keys', 'customer'));
            }

        }else 
        if($request->key == 'memo_autocomplete'){
            $search         = $request->q;
            $getsubsidiary  = Session::get('subsidiary');
            $memoauto       = MarketingSO::getMemoAutocomplete($search,$getsubsidiary);
            return $memoauto;
        } else {
            $item           =   Item::where(function($query) {
                                    $query->where('category_id', '<=', 20);
                                    $query->orWhere('nama', 'like', '%(AGS)%');
                                    $query->orWhere('nama', 'like', '%(AGS)% FROZEN');
                                })
                                // ->whereNotIn('category_id', ['20', '19', '12', '13', '14', '15'])
                                ->where('nama', 'not like', '%(RM)%')
                                ->where('nama', 'not like', '%(RM)%')
                                ->where('nama', 'not like', '%AYAM UTUH%')
                                ->where('nama', 'not like', '%REPACK%')
                                ->where('nama', 'not like', '%THAWING%')
                                ->where('nama', 'not like', '%KAMPUNG%')
                                // ->where('nama', 'not like', '%PEJANTAN%')
                                ->where('nama', 'not like', '%PARENT%')
                                ->where('status', '1')
                                ->get();

            $itemRetail     =   Item::where(function($query) {
                                    $query->where('category_id', '<=', 20);
                                    $query->orWhere('nama', 'like', '%(AGS)%');
                                    $query->orWhere('nama', 'like', '%(AGS)% FROZEN');
                                })
                                // ->whereNotIn('category_id', ['4', '10', '30'])
                                ->where('nama', 'not like', '%(RM)%')
                                ->where('nama', 'not like', '%(RM)%')
                                ->where('nama', 'not like', '%AYAM UTUH%')
                                ->where('nama', 'not like', '%REPACK%')
                                ->where('nama', 'not like', '%THAWING%')
                                ->where('nama', 'not like', '%KAMPUNG%')
                                // ->where('nama', 'not like', '%PEJANTAN%')
                                ->where('nama', 'not like', '%PARENT%')
                                ->where('status', '1')
                                ->get();

            $plastik        =   Item::where('category_id', 25)->where('subsidiary', 'like', '%'. Session::get('subsidiary'). '%')->where('status', '1')->pluck('nama', 'id') ;

            $customerSampingan   =   Customer::where('nama', '!=', '')
                                    ->where('netsuite_internal_id', '!=', NULL)
                                    ->where('netsuite_internal_id', '!=', 0)
                                    ->where(function($query) {
                                        $query->where('is_parent', 0)->orWhere('is_parent', NULL);
                                    })
                                    ->where('deleted_at', NULL)
                                    ->where('kategori', '!=', NULL)
                                    ->whereIn('id', Hargakontrak::where('keterangan', 'Customer Sampingan')->where('subsidiary', Session::get('subsidiary'))->select('customer_id'))
                                    ->orderBy('nama')->get();

            $itemSampingan  =  Item::whereIn('category_id', [4,10])->
                                where(function ($query) use ($dataOptionFresh) {
                                    foreach ($dataOptionFresh as $data) {
                                        $query->where('nama', 'not like', '%'.$data.'%');
                                    }
                                    $query->where('nama', 'not like', '%PEJANTAN%');
                                    $query->where('nama', 'not like', '%TELUR%');
                                })
                                ->orWhere(function ($query) use ($dataOptionFresh) {
                                    $query->orWhere('nama', 'like', 'AY - S%');
                                })
                                // ->where('subsidiary', 'like', '%'.Session::get('subsidiary').'%')
                                ->where('status', '1')->get();     

            $getChecked     =   DataOption::getOption('so_sampingan');
            $checkedItem    =   explode(', ', $getChecked);

            $getHarga       =   DataOption::getOption('harga_sampingan');
            $hargaItem      =   explode(', ', $getHarga);

            return view('admin.pages.generate_so.index', compact('item', 'plastik', 'customer','frozen', 'fresh','filterjenis', 'itemSampingan', 'checkedItem', 'hargaItem', 'itemRetail'));
        }

    }

    
    public function tanggalImport($date) {
        $slash = explode('/',$date);
        if(count($slash) == 3) {
            $date = $slash[2].'-'.$slash[1].'-'.$slash[0];
        }
        return $date;


    }

    public function store(Request $request)
    {

        if ($request->key == 'excelSOSampingan') {


            if ($request->hasFile('file')) {
                DB::beginTransaction();
                
                $path = $request->file('file');
                
                try {
                    //code...
                    $importSO = Excel::toArray([],$path);
                    // dd($importSO[0]);
                } catch (\Throwable $th) {
                    DB::rollBack() ;
                    //throw $th;
                    return "Format Tidak didukung, ulangi lagi dengan format csv";
                }
    
                $resp = [];

                foreach ($importSO[0] as $urut => $line) {
    
                    if ($urut != 0) {
    
                        $array                                           =  explode(';',$line[0]);
                        $customer                                        =  Customer::where('netsuite_internal_id', $array[0])->first();
    
                        // dd($array);
                        if ($customer) {

                            $cekOrderID                                  =  $array[1];
                            $resp[]                                      =  $cekOrderID;

                            if ($urut != 1) {

                                $cekRowSebelumnya                        =  $resp[$urut-2];
                                
                                if ($cekRowSebelumnya != $cekOrderID) {

                                    $SOSampingan                         =   new MarketingSO();
                                    $SOSampingan->tanggal_so             =   $this->tanggalImport($array[3]) ?? date('Y-m-d') ;
                                    $SOSampingan->tanggal_kirim          =   $this->tanggalImport($array[5]) ?? $this->tanggalImport($array[3]) ;
                                    $SOSampingan->customer_id            =   $customer->id ;
                                    $SOSampingan->internal_id_customer   =   $array[0] ;
                                    $SOSampingan->user_id                =   Auth::user()->id ;
                                    $SOSampingan->memo                   =   $array[4] ?? NULL ;
                                    $SOSampingan->po_number              =   '-' ;
                                    $SOSampingan->subsidiary             =   Session::get('subsidiary');
                                    $SOSampingan->gudang                 =   Session::get('subsidiary') == "CGL" ? "32" : "33" ;
                    
                                    $customer_address                    =   Customer_address::where('customer_id', $customer->id)->first();
    
                                    if($customer_address){
                                        $SOSampingan->wilayah            =   $customer_address->wilayah ;
                                    }

                                    if (!$SOSampingan->save()) {
                                        DB::rollBack();
                                        return back()->with('status', 2)->with('message', 'Proses gagal');
                                    }


                                    $getIDSO = $SOSampingan->id;

                                }

                            } else {

                                $SOSampingan                         =   new MarketingSO();
                                $SOSampingan->tanggal_so             =   $this->tanggalImport($array[3]) ?? date('Y-m-d') ;
                                $SOSampingan->tanggal_kirim          =   $this->tanggalImport($array[5]) ?? $this->tanggalImport($array[3]) ;
                                $SOSampingan->customer_id            =   $customer->id ;
                                $SOSampingan->internal_id_customer   =   $array[0] ;
                                $SOSampingan->user_id                =   Auth::user()->id ;
                                $SOSampingan->memo                   =   $array[4] ?? NULL ;
                                $SOSampingan->po_number              =   '-' ;
                                $SOSampingan->subsidiary             =   Session::get('subsidiary');
                                $SOSampingan->gudang                 =   Session::get('subsidiary') == "CGL" ? "32" : "33" ;
                
                                $customer_address                    =   Customer_address::where('customer_id', $customer->id)->first();

                                if($customer_address){
                                    $SOSampingan->wilayah            =   $customer_address->wilayah ;
                                }

                                if (!$SOSampingan->save()) {
                                    DB::rollBack();
                                    return back()->with('status', 2)->with('message', 'Proses gagal');
                                }

                                $getIDSO = $SOSampingan->id;

                            }
                            // dd($SOSampingan->id);


                            $itemRetail                   =   Item::where('sku', $array[7])->first();

                            $listRetail                   =   new MarketingSOList ;
                            $listRetail->internal_id_item =   $itemRetail->netsuite_internal_id ;
                            $listRetail->marketing_so_id  =   $SOSampingan->id ;
                            $listRetail->item_id          =   $itemRetail->id ;
                            $listRetail->item_nama        =   $itemRetail->nama ;
                            $listRetail->description_item =   $array[8] ;
                            $listRetail->qty              =   $array[13] ?? 0;
                            $listRetail->berat            =   $array[9] ?? 0;
                            $listRetail->harga            =   $array[11] ?? 0 ;
                            $listRetail->harga_cetakan    =   $array[12] == 'Kg' ? 1 : 2 ;
                            $listRetail->parting          =   $array[15] ?? 0 ;
                            $listRetail->plastik          =   $array[16] ?? NULL ;
                            $listRetail->bumbu            =   $array[17] ?? NULL ;
                            $memo = $array[18] ?? NULL ;
                            if ($array[18] == "") {
                                $listRetail->memo         =   $array[10] ;
                            } else {
                                $listRetail->memo         =   str_replace("ekor", "", $array[18]) . " || ".$array[10] ;
                            }

                            $listRetail->status           =   1 ;

                            if (!$listRetail->save()) {
                                DB::rollBack() ;
                                return back()->with('status', 2)->with('message', 'Import gagal');
                            }


                        }
                    }
                }

                DB::commit();


                DB::beginTransaction();

                $findMarketingSO = MarketingSO::where('netsuite_status', NULL)->where('netsuite_id', NULL)->where('status', NULL)->get();
                if ($findMarketingSO) {
                    foreach ($findMarketingSO as $appsToNS) {


                        // CARI PERBEDAAN HARI INI DENGAN TANGGAL KIRIM
                        // $date1      = new DateTime(date('Y-m-d'));
                        // $date2      = new DateTime($appsToNS->tanggal_kirim);
                        // $interval   = $date1->diff($date2);

                        // if ($interval->days <= 1) {
                            $ns                           = Netsuite::sales_order("marketing_so", $appsToNS->id, $appsToNS->app_po, NULL, $appsToNS->tanggal_so);
    
                            $appsToNS->netsuite_status    =   1;
                            $appsToNS->netsuite_id        =   $ns->id ;
                            $appsToNS->status             =   1 ;
                
                            $appsToNS->save();
                        // }
                    }
                }

                DB::commit();
    
                return back()->with('status', 1)->with('message', 'Import berhasil');


            } else{
                DB::rollBack() ;
                return back()->with('status', 2)->with('message', 'Import gagal');
            }
        
        } else if ($request->key == 'saveItemSampingan') {
            $dataOption                     = DataOption::where('option_name', 'so_sampingan')->first();
            $dataHarga                      = DataOption::where('option_name', 'harga_sampingan')->first();
            if (!$dataHarga) {
                $dataHarga                  = new DataOption;
                $dataHarga->slug            = 'harga_sampingan';
                $dataHarga->option_name     = 'harga_sampingan';
            }

            $explodeHarga    = implode(", ", $request->newHarga);
            $explodeItems    = implode(", ", $request->items);

            if ($dataOption) {
                $dataOption->option_value   = $explodeItems;
                $dataHarga->option_value    = $explodeHarga;
                
                $dataOption->save();
                $dataHarga->save();
                return response()->json([
                    'status' => 200,
                    'msg'    => 'Berhasil edit daftar sampingan'
                ]);

            } else {
                return response()->json([
                    'status' => 400,
                    'msg'    => 'Gagal edit daftar sampingan'
                ]);
            }           

        } else if ($request->key == 'SOSampingan') {
            $datas      = $request->datas;
            
            
            // return response()->json($itemRetailArray);
            
            $getSampingan       =   DataOption::getOption('so_sampingan');
            $itemSampingan      =   explode(', ', $getSampingan);
            
            $getHarga           =   DataOption::getOption('harga_sampingan');
            $itemHarga          =   explode(', ', $getHarga);
            
            DB::beginTransaction() ;
            $totalArray = 0;
            $totalAwal  = 0;
            foreach ($datas as $totalsemuaarray) {
                $totalAwal += $totalsemuaarray;
            }  

            for ($x = 0; $x < COUNT($request->customer); $x++) {

                $header                         =   new MarketingSO ;
                $header->tanggal_so             =   $request->tanggalakhir ;
                $header->tanggal_kirim          =   $request->tanggalakhir ;
                $header->customer_id            =   $request->customer[$x] ;
                $header->internal_id_customer   =   Customer::find($request->customer[$x])->netsuite_internal_id ;
                $header->user_id                =   Auth::user()->id ;
                $header->memo                   =   '-' ;
                $header->po_number              =   '-' ;
                $header->subsidiary             =   Session::get('subsidiary');
                $header->gudang                 =   Session::get('subsidiary') == "CGL" ? "32" : "33" ;

        
                $customer_address               =   Customer_address::where('customer_id', $request->customer[$x])->first();
                if($customer_address){
                    $header->wilayah            =   $customer_address->wilayah ;
                }
        
                if (!$header->save()) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 400,
                        'msg'    => 'Proses gagal'
                    ]);
                }

                for ($y = 0; $y < COUNT($itemSampingan); $y++) {
                    $item                           =   Item::where('sku', $itemSampingan[$y])->first();
                    if ($item) {
                        $itemQtyBerat               =   Hargakontrak::where('item_id', $item->id)->where('keterangan', 'Customer Sampingan')->where('subsidiary', Session::get('subsidiary'))
                                                            ->where('customer_id', $request->customer[$x])->first();
                        if ($itemQtyBerat) {
                            $list                   =   new MarketingSOList ;
                            $list->internal_id_item =   $itemQtyBerat->item->netsuite_internal_id ;
                            $list->marketing_so_id  =   $header->id ;
                            $list->item_id          =   $itemQtyBerat->item->id ;
                            $list->item_nama        =   $itemQtyBerat->item->nama ;
                            $list->description_item =   $itemQtyBerat->item->nama ;
                            $list->qty              =   $itemQtyBerat->min_qty ;
                            $list->berat            =   $itemQtyBerat->min_berat ;
                            $list->harga            =   $itemHarga[$y] ?? 0 ;
                            $list->harga_cetakan    =   1 ;
                            $list->status           =   1 ;
                            $list->cabang_item      =   'paketan';
                            if (!$list->save()) {
                                DB::rollBack() ;
                                return response()->json([
                                    'status' => 400,
                                    'msg'    => 'Proses gagal'
                                ]);
                            }
                        }
                    }
                }
                
                if ($datas[$x] > 0) {
                    $hargaRetailArray   = $request->hargas;
                    $itemRetailArray    = $request->items;
                    $beratRetailArray   = $request->berats;
                    $qtyRetailArray     = $request->qtys;

                    for ($itx = $totalArray; $itx < $totalArray+$datas[$x]; $itx++) {
                        $itemRetail                       =   Item::where('id', $itemRetailArray[$itx])->first();
                        if ($itemRetail) {
                            $listRetail                   =   new MarketingSOList ;
                            $listRetail->internal_id_item =   $itemRetail->netsuite_internal_id ;
                            $listRetail->marketing_so_id  =   $header->id ;
                            $listRetail->item_id          =   $itemRetail->id ;
                            $listRetail->item_nama        =   $itemRetail->nama ;
                            $listRetail->description_item =   $itemRetail->nama ;
                            $listRetail->qty              =   $qtyRetailArray[$itx] ?? 0;
                            $listRetail->berat            =   $beratRetailArray[$itx] ?? 0;
                            $listRetail->harga            =   $hargaRetailArray[$itx] ?? 0 ;
                            $listRetail->harga_cetakan    =   1 ;
                            $listRetail->status           =   1 ;
                            $listRetail->cabang_item      =   'retail';
                            if (!$listRetail->save()) {
                                DB::rollBack() ;
                                return response()->json([
                                    'status' => 400,
                                    'msg'    => 'Proses gagal'
                                ]);
                            }

                        }

                    }

                    $totalArray += $datas[$x];
                    $header->cabang                 =   'retailsampingan' ;
                }



                $ns = Netsuite::sales_order("marketing_so", $header->id, $header->app_po, NULL, $header->tanggal_so);

                if ($ns) {
        
                    $header->netsuite_status    =   1;
                    $header->netsuite_id        =   $ns->id ;
                    $header->status             =   1 ;
        
                    $header->save();
        
                } else {
                    DB::rollBack();
                    return response()->json([
                        'status' => 400,
                        'msg'    => 'Proses integrasi gagal',
                    ]);
                }
            }

            DB::commit();
            return response()->json([
                'status' => 200,
                'msg'    => 'Tambah sales order berhasil',
            ]);


            
        } else {
            DB::beginTransaction() ;
            if (Session::get('subsidiary') == 'EBA'){
                $dataMarketingSO = MarketingSO::where('customer_id', $request->customer)
                                    ->where('tanggal_so', $request->tanggal_so)->where('tanggal_kirim', $request->tanggal_kirim)
                                    ->where('memo', $request->memo_head)->where('po_number', $request->po_number)
                                    ->where('subsidiary', Session::get('subsidiary')) //Sesuai subsidiary
                                    ->get(); // cari data yang sama
        
        
                $itemInginDibeli = []; // array untuk menampung item yang ingin dibeli
                $plastikInginDibeli = []; // array untuk menampung plastik yang ingin dibeli
                $unitInginDibeli = []; // array untuk menampung unit yang ingin dibeli
                $hargaInginDibeli = []; // array untuk menampung harga yang ingin dibeli
                $hargaCetakanInginDibeli = []; // array untuk menampung harga cetakan yang ingin dibeli
                $beratInginDibeli = []; // array untuk menampung berat yang ingin dibeli
                $bumbuInginDibeli = []; // array untuk menampung bumbu yang ingin dibeli
                $memoInginDibeli = []; // array untuk menampung memo yang ingin dibeli
                $partingInginDibeli = []; // array untuk menampung parting yang ingin dibeli
        
        
                for ($x=0; $x < COUNT($request->item); $x++) { // looping item yang ingin dibeli
                    if($request->memo[$x]==""){
                        $memo     =   $request->qty_unit[$x] ;
                    }else{
                        $memo     =   str_replace("ekor", "", $request->memo[$x]) . " || ".$request->qty_unit[$x] ;
                    }
                    $item       =   Item::find($request->item[$x]) ; // cari item
                    $itemInginDibeli[$item->id] = (int)$request->qty[$x]; // masukkan ke array
                    $plastikInginDibeli[$item->id] = (int)$request->plastik[$x]; // masukkan ke array
                    $hargaInginDibeli[$item->id] = str_replace(".","",$request->harga[$x]) ; // masukkan ke array
                    $hargaCetakanInginDibeli[$item->id] = $request->harga_cetakan[$x]; // masukkan ke array
                    $beratInginDibeli[$item->id] = $request->berat[$x]; // masukkan ke array
                    $bumbuInginDibeli[$item->id] = $request->bumbu[$x]; // masukkan ke array
                    $memoInginDibeli[$item->id] = $memo; // masukkan ke array
                    $partingInginDibeli[$item->id] = $request->parting[$x]; // masukkan ke array
                }
        
                $jumlahItem = count($itemInginDibeli); // jumlah item yang ingin dibeli jumlahnya sama atau tidak
                $jumlahPlastik = count($plastikInginDibeli); // jumlah plastik yang ingin dibeli jumlahnya sama atau tidak
                $jumlahHarga = count($hargaInginDibeli); // jumlah harga yang ingin dibeli jumlahnya sama atau tidak
                $jumlahHargaCetakan = count($hargaCetakanInginDibeli); // jumlah harga cetakan yang ingin dibeli jumlahnya sama atau tidak
                $jumlahBerat = count($beratInginDibeli); // jumlah berat yang ingin dibeli jumlahnya sama atau tidak
                $jumlahBumbu = count($bumbuInginDibeli); // jumlah bumbu yang ingin dibeli jumlahnya sama atau tidak
                $jumlahMemo = count($memoInginDibeli); // jumlah memo yang ingin dibeli jumlahnya sama atau tidak
                $jumlahParting = count($partingInginDibeli); // jumlah parting yang ingin dibeli jumlahnya sama atau tidak
        
                if($dataMarketingSO){ //Jika terdapat customer yang sama dengan tanggal yang sama.
                    $arrayIDSO = []; //array untuk menampung id so 
                    foreach ($dataMarketingSO as $dataSO){ //looping data so
                        $arrayIDSO[] = $dataSO->id; //menampung id so
                    }
                    if(count($arrayIDSO) > 0){ //jika ada data id so
                        $hasilListSO = []; //array untuk menampung hasil list so
                        $plastikListSO = []; //array untuk menampung plastik list so
                        $hargaListSO = []; //array untuk menampung harga list so
                        $hargaCetakanListSO = []; //array untuk menampung harga cetakan list so
                        $beratListSO = []; //array untuk menampung berat list so
                        $bumbuListSO = []; //array untuk menampung bumbu list so
                        $memoListSO = []; //array untuk menampung memo list so
                        $partingListSO = []; //array untuk menampung parting list so
                        foreach($arrayIDSO as $array){ //looping id so
                            $item = MarketingSOList::where('marketing_so_id', $array)->get(); //cari item yang sama
                            foreach($item as $i){ //looping item yang sama
                                $hasilListSO[$array][$i->item_id] = (int)$i->qty; //menampung item yang sama
                                $plastikListSO[$array][$i->item_id] = (int)$i->plastik; //menampung plastik yang sama
                                $hargaListSO[$array][$i->item_id] = (int)$i->harga; //menampung harga yang sama
                                $hargaCetakanListSO[$array][$i->item_id] = $i->harga_cetakan; //menampung harga cetakan yang sama
                                $beratListSO[$array][$i->item_id] = $i->berat; //menampung berat yang sama
                                $bumbuListSO[$array][$i->item_id] = $i->bumbu; //menampung bumbu yang sama
                                $memoListSO[$array][$i->item_id] = $i->memo; //menampung memo yang sama
                                $partingListSO[$array][$i->item_id] = $i->parting; //menampung parting yang sama
                            }
                        }
                        foreach($hasilListSO as $h){ //looping hasil list so
                            $cek = array_intersect_assoc($itemInginDibeli, $h); //mencocokkan array item yang ingin dibeli dengan array item yang sudah ada di so
                            $test = count($cek); //menghitung jumlah item yang sama
                            if ($jumlahItem == $test) { //jika jumlah item yang ingin dibeli sama dengan jumlah item yang sudah ada di so
                                foreach($plastikListSO as $p){
                                    $cek2 = array_intersect_assoc($plastikInginDibeli, $p);
                                    $test2 = count($cek2);
                                    if($jumlahPlastik == $test2){
                                        foreach($hargaListSO as $hl){
                                            $cek3 = array_intersect_assoc($hargaInginDibeli, $hl);
                                            $test3 = count($cek3);
                                            if($jumlahHarga == $test3){
                                                foreach($hargaCetakanListSO as $hc){
                                                    $cek4 = array_intersect_assoc($hargaCetakanInginDibeli, $hc);
                                                    $test4 = count($cek4);
                                                    if($jumlahHargaCetakan == $test4){
                                                        foreach($beratListSO as $b){
                                                            $cek5 = array_intersect_assoc($beratInginDibeli, $b);
                                                            $test5 = count($cek5);
                                                            if($jumlahBerat == $test5){
                                                                foreach($bumbuListSO as $bumbu){
                                                                    $cek6 = array_intersect_assoc($bumbuInginDibeli, $bumbu);
                                                                    $test6 = count($cek6);
                                                                    if($jumlahBumbu == $test6){
                                                                        foreach($memoListSO as $memo){
                                                                            $cek7 = array_intersect_assoc($memoInginDibeli, $memo);
                                                                            $test7 = count($cek7);
                                                                            if($jumlahMemo == $test7){
                                                                                foreach($partingListSO as $parting){
                                                                                    $cek8 = array_intersect_assoc($partingInginDibeli, $parting);
                                                                                    $test8 = count($cek8);
                                                                                    if($jumlahParting == $test8){
                                                                                        DB::rollBack() ;
                                                                                        return back()->with('status', 2)->with('message', 'Proses gagal karena memesan barang yang sama pada SO sebelumnya');
                                                                                    }
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
    
            $header                         =   new MarketingSO ;
            $header->tanggal_so             =   $request->tanggal_so ;
            $header->tanggal_kirim          =   $request->tanggal_kirim ;
            $header->customer_id            =   $request->customer ;
            $header->internal_id_customer   =   Customer::find($request->customer)->netsuite_internal_id ;
            $header->user_id                =   Auth::user()->id ;
            $header->memo                   =   $request->memo_head ;
            $header->sales_channel          =   $request->sales_channel ;
            $header->po_number              =   $request->po_number ;
            $header->subsidiary             =   Session::get('subsidiary');
            $header->gudang                 =   Session::get('subsidiary') == "CGL" ? "32" : "33" ;
    
            $customer_address               =   Customer_address::where('customer_id', $request->customer)->first();
            if($customer_address){
                $header->wilayah            =   $customer_address->wilayah ;
            }
    
            if (!$header->save()) {
                DB::rollBack();
                return back()->with('status', 2)->with('message', 'Proses gagal');
            }
    
            if($request->item > 0){
                for ($x=0; $x < COUNT($request->item); $x++) {
                    $item       =   Item::find($request->item[$x]) ;
    
                    if ($item) {
    
                        if($request->memo[$x]==""){
                            $memo     =   $request->qty_unit[$x] ;
                        }else{
                            $memo     =   str_replace("ekor", "", $request->memo[$x]) . " || ".$request->qty_unit[$x] ;
                        }
    
                        if (Session::get('subsidiary') == 'CGL') {
                            $check = 0;
                        } else {
                            $check  =   MarketingSOList::where('marketing_so_id', $header->id)
                                        ->where('item_id', $item->id)
                                        ->where('parting', $request->parting[$x])
                                        ->where('plastik', $request->plastik[$x] ?? NULL)
                                        ->where('bumbu', $request->bumbu[$x])
                                        ->where('qty', $request->qty[$x] ?? "0")
                                        ->where('berat', $request->berat[$x])
                                        ->where('harga', str_replace(".","",$request->harga[$x]))
                                        ->where('harga_cetakan', $request->harga_cetakan[$x])
                                        ->where('memo', $memo)
                                        ->count() ;
                        }
    
                        
                        if ($check < 1) {
                            $list                   =   new MarketingSOList ;
                            $list->internal_id_item =   $item->netsuite_internal_id ;
                            $list->marketing_so_id  =   $header->id ;
                            $list->item_id          =   $item->id ;
                            $list->item_nama        =   $item->nama ;
                            $list->parting          =   $request->parting[$x] ?? 0;
                            $list->plastik          =   $request->plastik[$x] ?? NULL ;
                            $list->bumbu            =   $request->bumbu[$x] ;
                            if($request->qty[$x]=="0" || $request->qty[$x]==""){
                                $list->memo         =   $request->memo[$x];
                            }else{
                                $list->memo     =   $memo ;
                            }
                            $list->description_item =   $request->description_item[$x] ?? NULL;
                            $list->internal_memo    =   $request->internal_memo[$x] ?? NULL;
                            $list->qty              =   $request->qty[$x] ?? "0" ;
                            $list->berat            =   $request->berat[$x] ;
                            $list->harga            =   str_replace(".","",$request->harga[$x]) ;
                            $list->harga_cetakan    =   $request->harga_cetakan[$x] ;
                            $list->status           =   1 ;
                            if (!$list->save()) {
                                DB::rollBack() ;
                                return back()->with('status', 2)->with('message', 'Proses gagal') ;
                            }
                        }
                    }
                }
            } else {
                DB::rollBack() ;
                return back()->with('status', 2)->with('message', 'Proses gagal') ;
            }


            // $date1      = new DateTime(date('Y-m-d'));
            // $date2      = new DateTime($header->tanggal_kirim);
            // $interval   = $date1->diff($date2);

            
            // if ($interval->days <= 1) {
                $ns = Netsuite::sales_order("marketing_so", $header->id, $header->app_po, NULL, $header->tanggal_so);
                if($ns){
        
                    $header->netsuite_status    =   1;
                    $header->netsuite_id        =   $ns->id ;
                    $header->status             =   1 ;
        
                    $header->save();
        
                }else{
                    DB::rollBack();
                    return back()->with('status', 2)->with('message', 'Proses integrasi gagal');
                }
            // }
    
            DB::commit();
            return back()->with('status', 1)->with('message', 'Tambah sales order berhasil');

        }

    }


    public function update(Request $request){
        DB::beginTransaction() ;

        $so     =   MarketingSO::find($request->idsummary);
        $net    =   Netsuite::find($so->netsuite_id);

        if($net->status=='2' || $net->status=='4'){
            return back()->with('status', 2)->with('message', 'SO Edit Proses gagal, proses integrasi masih pending, tunggu sampai selesai');
        }
        

        //  Validasi Inputan agar tidak masuk log
        $dataMarketingSO = MarketingSO::find($request->idsummary);
        if($request->tanggal_so == $dataMarketingSO->tanggal_so &&
            $request->tanggal_kirim == $dataMarketingSO->tanggal_kirim &&
            $request->customer == $dataMarketingSO->customer_id &&
            $request->memo_head == $dataMarketingSO->memo &&
            $request->sales_channel == $dataMarketingSO->sales_channel &&
            $request->po_number == $dataMarketingSO->po_number){

             // Data Database
            $listUpdate = MarketingSOList::whereIn('id', $request->ideditdatasummary)->withTrashed()->get();
            $listarray = [];
            $listdatabase = [];
            foreach($listUpdate as $list){
                $listdatabase[] = [
                    'qty' => $list->qty,
                    'berat' => $list->berat,
                    'harga' => $list->harga,
                    'harga_cetakan' => $list->harga_cetakan,
                    'parting' => $list->parting,
                    'plastik' => $list->plastik,
                    'bumbu' => $list->bumbu,
                    'memo' => $list->memo,
                    'description_item' => $list->description_item,
                    'internal_memo' => $list->internal_memo,
                ];
            }
            for($x=0; $x < COUNT($request->item); $x++){
                $listarray[$x] = [
                    'qty' => $request->qty[$x],
                    'berat' => $request->berat[$x],
                    'harga' => str_replace(".","",$request->harga[$x]),
                    'harga_cetakan' =>$request->harga_cetakan[$x],
                    'parting' => $request->parting[$x] ?? 0,
                    'plastik' => $request->plastik[$x],
                    'bumbu' => $request->bumbu[$x],
                    'memo' => $request->memo[$x],
                    'description_item' => $request->description_item[$x] ?? NULL,
                    'internal_memo' => $request->internal_memo[$x] ?? NULL,
                ];
            }

            // if($listarray == $listdatabase){
            //     return back()->with('status', 2)->with('message', 'Gagal simpan, tidak ada perubahan');
            // }
        }


        try {
            $soawal = MarketingSO::find($request->idsummary);
            $ceklog = Adminedit::where('table_id', $request->idsummary)->where('table_name', 'marketing_so')->where('type', 'edit')->where('content', 'Data Awal (Original)')->count();
            // Log activity
            // Item awal/original
            if($ceklog < 1){
                $solist             =   MarketingSOList::where('marketing_so_id', $request->idsummary)->withTrashed()->get();
                $log                =   new Adminedit ;
                $log->user_id       =   Auth::user()->id ;
                $log->table_name    =   'marketing_so' ;
                $log->table_id      =   $request->idsummary ;
                $log->type          =   'edit' ;
                $log->activity      =   'marketing' ;
                $log->content       =   'Data Awal (Original)';
                $log->data          =   json_encode([
                        'header' => $soawal,
                        'list' => $solist
                ]) ;
                if (!$log->save()) {
                    DB::rollBack();
                    return back()->with('status', 2)->with('message', 'Proses simpan log gagal');
                }
            }

            //code...
            $update = MarketingSO::where('id', $request->idsummary)
                        ->update([
                            'tanggal_so'            => $request->tanggal_so,
                            'tanggal_kirim'         => $request->tanggal_kirim,
                            'memo'                  => $request->memo_head,
                            'customer_id'           => $request->customer,
                            'internal_id_customer'  => Customer::find($request->customer)->netsuite_internal_id,
                            'sales_channel'         => $request->sales_channel,
                            'po_number'             => $request->po_number,
                            'status'                => 1
                        ]);
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            return back()->with('status', 2)->with('message', 'SO Edit Proses gagal '.$th->getMessage());
        }


        $so     =   MarketingSO::find($request->idsummary);
        $net    =   Netsuite::find($so->netsuite_id);

        $count_data_update = 0;

        if($request->item > 0){
            for ($x=0; $x < COUNT($request->item); $x++) {
                $item       =   Item::find($request->item[$x]) ;
                if ($item) {
                    if($request->ideditdatasummary[$x] != ''){

                        try {
                            //code...

                            $listUpdate = MarketingSOList::find($request->ideditdatasummary[$x]);
                            if($listUpdate){
                                if($listUpdate->qty     ==$request->qty[$x] &&
                                $listUpdate->berat      ==$request->berat[$x] &&
                                $listUpdate->parting    ==$request->parting[$x] &&
                                $listUpdate->bumbu      ==$request->bumbu[$x] &&
                                $listUpdate->plastik    ==$request->plastik[$x] &&
                                $listUpdate->harga_cetakan==$request->harga_cetakan[$x] &&
                                $listUpdate->harga      ==$request->harga[$x] &&
                                $listUpdate->memo       ==$request->memo[$x]
                                ){
                                    // Kosong --
                                }else{
                                    $listUpdate = MarketingSOList::where('id', $request->ideditdatasummary[$x])->first()
                                    ->update([
                                        'qty'                   => $request->qty[$x],
                                        'berat'                 => $request->berat[$x],
                                        'parting'               => $request->parting[$x] ?? 0,
                                        'bumbu'                 => $request->bumbu[$x],
                                        'plastik'               => $request->plastik[$x],
                                        'internal_id_item'      => $item->netsuite_internal_id,
                                        'memo'                  => $request->memo[$x],
                                        'description_item'      => $request->description_item[$x] ?? NULL,
                                        'internal_memo'         => $request->internal_memo[$x] ?? NULL,
                                        'harga_cetakan'         => $request->harga_cetakan[$x],
                                        'harga'                 => str_replace(".","",$request->harga[$x]),
                                        'edited'                => ($listUpdate->edited ?? 0)+1,

                                    ]);

                                    $count_data_update++;
                                }
                            }


                        } catch (\Throwable $th) {
                            //throw $th;
                            DB::rollBack() ;
                            return back()->with('status', 2)->with('message', 'Item Edit Proses gagal '.$th->getMessage()) ;
                        }
                    } else {
                        $list                   =   new MarketingSOList ;
                        $list->internal_id_item =   $item->netsuite_internal_id ;
                        $list->marketing_so_id  =   $request->idsummary ;
                        $list->item_id          =   $item->id ;
                        $list->item_nama        =   $item->nama ;
                        $list->parting          =   $request->parting[$x] ?? 0;
                        $list->plastik          =   $request->plastik[$x] ?? NULL ;
                        $list->bumbu            =   $request->bumbu[$x] ;
                        $list->memo             =   $request->memo[$x] ;
                        $list->description_item =   $request->description_item[$x] ?? NULL;
                        $list->internal_memo    =   $request->internal_memo[$x] ?? NULL;
                        $list->qty              =   $request->qty[$x] ?? "0";
                        $list->berat            =   $request->berat[$x] ;
                        $list->harga            =   str_replace(".","",$request->harga[$x]) ;
                        $list->harga_cetakan    =   $request->harga_cetakan[$x] ;
                        $list->status           =   1 ;
                        if (!$list->save()) {
                            DB::rollBack() ;
                            return back()->with('status', 2)->with('message', 'Item tambah Proses gagal') ;
                        }
                    }
                }
            }
        } else {
            DB::rollBack() ;
            return back()->with('status', 2)->with('message', 'Proses gagal') ;
        }

        //Data log setelah edit
        $banyaklog                 =   Adminedit::where('table_name', 'marketing_so')->where('table_id', $request->idsummary)->where('type', 'data')->count() ;
        // dd($banyaklog)  ;
        $listsetelah               =   MarketingSOList::where('marketing_so_id', $request->idsummary)->get();
        $logsetelah                =   new Adminedit ;
        $logsetelah->user_id       =   Auth::user()->id ;
        $logsetelah->table_name    =   'marketing_so' ;
        $logsetelah->table_id      =   $request->idsummary ;
        $logsetelah->type          =   'data' ;
        $logsetelah->activity      =   'marketing' ;
        $logsetelah->content       =   'Data Edit Ke '. ($banyaklog+1) ;
        $logsetelah->data          =   json_encode([
                'header' => $so,
                'list' => $listsetelah
        ]) ;
        if (!$logsetelah->save()) {
            DB::rollBack();
            return back()->with('status', 2)->with('message', 'Proses simpan log gagal');
        }

        if($count_data_update>0){
            $so->edited = $so->edited+1;
            $so->save();
        }



        DB::commit() ;

        // $date1      = new DateTime(date('Y-m-d'));
        // $date2      = new DateTime($dataMarketingSO->tanggal_kirim);
        // $interval   = $date1->diff($date2);

        
        // if ($interval->days <= 1) {
            $ns = Netsuite::update_sales_order("marketing_so", $so->id, $so->po_number, NULL, $so->tanggal_so);

            if($ns){
        
                $dataMarketingSO->netsuite_status    =   1;
                $dataMarketingSO->netsuite_id        =   $ns->id ;
                $dataMarketingSO->status             =   1 ;
    
                $dataMarketingSO->save();
    
            }else{
                DB::rollBack();
                return back()->with('status', 2)->with('message', 'Proses integrasi gagal');
            }

        // }

        return back()->with('status', 1)->with('message', 'Edit sales order berhasil');
    }

    public function destroy(Request $request) {

        $delete = MarketingSOList::where('id', $request->idSOList)->delete();
        if (!$delete) {
            return response()->json([
                'msg' => 'Gagal hapus',
                'status' => 'error'
            ]);
        } else {
            // Log activity
            // $so = MarketingSO::find($request->id_so);
            $so = MarketingSO::find($request->id_so);
            $ceklog = Adminedit::where('table_id', $request->id_so)->where('table_name', 'marketing_so')->where('type', 'edit')->where('content', 'Data Awal (Original)')->count();
            $solist = MarketingSOList::where('marketing_so_id', $request->id_so)->withTrashed()->get();
            if($ceklog < 1){
                $log                =   new Adminedit ;
                $log->user_id       =   Auth::user()->id ;
                $log->table_name    =   'marketing_so' ;
                $log->table_id      =   $request->id_so ;
                $log->type          =   'edit' ;
                $log->activity      =   'marketing' ;
                $log->content       =   'Data Awal (Original)';
                $log->data          =   json_encode([
                        'header' => $so,
                        'list' => $solist
                ]) ;
                if (!$log->save()) {
                    DB::rollBack();
                    return back()->with('status', 2)->with('message', 'Proses simpan log gagal');
                }
            }
            $ns                 =   Netsuite::update_sales_order("marketing_so", $so->id, $so->po_number, NULL, $so->tanggal_so);
            $so->status         =   1;
            $so->save();
            $log                =   new Adminedit ;
            $log->user_id       =   Auth::user()->id ;
            $log->table_name    =   'marketing_so' ;
            $log->table_id      =   $request->id_so ;
            $log->type          =   'hapus' ;
            $log->activity      =   'marketing' ;
            $log->content       =   'Penghapusan Item';
            $log->data          =   json_encode([
                    'header' => $so,
                    'list' => $solist
            ]) ;

            $log->save();

            return response()->json([
                'msg' => 'Berhasil hapus',
                'status' => 'success'
            ]);

        }
    }

    public function netsuite_retry($id){

        $ns     = Netsuite::find($id);
        if($ns){
            $ns->status = 2;
            $ns->save();
            return back()->with('status', 1)->with('message', 'Proses Berhasil') ;
            // return response()->json([
            //     'msg' => 'Berhasil retry',
            //     'status' => 'success'
            // ]);
        }else{
            return back()->with('status', 2)->with('message', 'Proses gagal') ;
            // return response()->json([
            //     'msg' => 'Gagal proses',
            //     'status' => 'failed'
            // ]);
        }
    }
    
}
