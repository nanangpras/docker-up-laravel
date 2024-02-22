<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Pembelian;
use App\Models\Pembelianheader;
use App\Models\Pembelianlist;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Helpers\StoreImage;
use App\Models\Adminedit;
use App\Models\Gudang;
use App\Models\Netsuite;
use App\Models\User;
use Illuminate\Support\Facades\Session;

class PembelianbarangController extends Controller
{
    public function index(Request $request)
    {
        $subsidiary = Session::get('subsidiary');

        // $item       =   Item::where('subsidiary', Session::get('subsidiary'))->where('category_id', '>=', 20)->where('type', '!=', null)->get();
        $item       =   Item::where('subsidiary', 'like', '%' . Session::get('subsidiary') . '%')->where('category_id', '>=', 20)->get();
        $id         = $request->id ?? Pembelian::latest()->first()->id ?? '1';

        if ($request->id) {
            $pembelian  =    Pembelian::where('status', 9)->where('pembelian.subsidiary', 'like', '%' . Session::get('subsidiary') . '%')->where('id', $id)->first();
            return view('admin.pages.pembelian_barang.input_detail', compact('pembelian', 'item', 'id'));
        }

        if ($request->key == 'list') {
            $list       =   Pembelianlist::where('pembelian_id', $request->id_list)
                ->where('harga', '=', NULL)
                ->get();

            return view('admin.pages.pembelian_barang.list', compact('list', 'item'));
        } else

        if ($request->key == 'editPR') {
            $data       =   Pembelianlist::where('id', $request->idlist)->first();
            return response()->json($data);
        } else

        if ($request->key == 'listPendingPR') {
            $pembelian  =    Pembelian::where('pembelian.status', 9)
                ->whereHas('user', function ($q) {
                    $q->where('users.company_id', Session::get('subsidiary_id'));
                    $q->where('users.id', Auth::user()->id);
                })
                ->where('subsidiary', Session::get('subsidiary'))
                ->paginate(10);
            // dd($pembelian);
            return view('admin.pages.pembelian_barang.list_pending', compact('item', 'id', 'pembelian'));
        } else

        if ($request->key == 'listitempr') {
            $pencarian = $request->pencarian ?? '';
            $item = Item::whereHas('itemkat', function ($query) use ($pencarian) {
                $query->where('sku', 'like', '%' . $pencarian . '%')
                    ->orWhere('items.nama', 'like', '%' . $pencarian . '%')
                    ->orWhere('subsidiary', 'like', '%' . $pencarian . '%')
                    ->orWhere('category.nama', 'like', '%' . $pencarian . '%');
            })
                ->where('subsidiary', 'like', '%' . Session::get('subsidiary') . '%');
                // ->paginate(10);
            $item_clone = clone $item;
            if ($request->subkey == 'download') {
                $download = true;
                $item = $item_clone->get();
                // dd($request->all());
                return view('admin.pages.pembelian_barang.listitempr', compact('item', 'download'));
            } else {
                $download = false;
                $item = $item_clone->paginate(10);
                return view('admin.pages.pembelian_barang.listitempr', compact('item', 'download'));
            }
            
        } else {
            $pembelian  =    Pembelian::where('status', 9)->where('user_id', Auth::user()->id)->where('pembelian.subsidiary', 'like', '%' . Session::get('subsidiary') . '%')->paginate(10);

            try {
                //code...
                $id         =    Pembelian::where('pembelian.subsidiary', 'like', '%' . Session::get('subsidiary') . '%')->latest()->first()->id + 1 ?? '1';
            } catch (\Throwable $th) {
                $id         =    '1';
                //throw $th;
            }
            return view('admin.pages.pembelian_barang.index', compact('item', 'id', 'pembelian'));
        }
    }

    public function store(Request $request)
    {
        // $item       =   Item::where('subsidiary', Session::get('subsidiary'))->where('category_id', '>=', 20)->where('type', '!=', null)->get();
        $item       =   Item::where('subsidiary', 'like', '%' . Session::get('subsidiary') . '%')->where('category_id', '>=', 20)->get();

        if ($request->key == 'updatePR') {

            $item           =   Item::where('id', $request->item)
                ->first();
            $dataawal       =   Pembelianlist::where('id', $request->id)->first();

            $data = Pembelianlist::where('id', $request->id)->update([
                'item_id'       => $request->item,
                'qty'           => $request->qty,
                'sisa'          => ($request->qty - $dataawal->qty) + $dataawal->sisa,
                'keterangan'    => $request->keterangan,
                'link_url'      => $request->url,
                'unit'          => $item->type ?? NULL,
            ]);
            return response()->json([
                'data' => $data,
                'msg' => 'Data berhasil diupdate',
            ]);
        } else

        if ($request->key == 'add_item') {
            DB::beginTransaction();

            if ($request->item > 0) {
                for ($x = 0; $x < COUNT($request->item); $x++) {
                    $item   =   Item::where('id', $request->item[$x])
                        ->first();

                    // $pembelian                  =   Pembelian::where('status', 9)->where('user_id', Auth::user()->id)->where('id', $request->detail_id)->first();
                    $pembelian                  =   Pembelian::where('id', $request->detail_id)->where('pembelian.subsidiary', 'like', '%' . Session::get('subsidiary') . '%')->first();
                    $list_beli                  =   new Pembelianlist;
                    $list_beli->pembelian_id    =   $pembelian->id;
                    $list_beli->item_id         =   $item->id;
                    $list_beli->qty             =   $request->qty[$x];
                    $list_beli->sisa            =   $request->qty[$x];
                    $list_beli->unit            =   $item->type ?? NULL;
                    $list_beli->keterangan      =   $request->keterangan[$x];
                    $list_beli->link_url        =   $request->link_url[$x] ?? NULL;
                    $list_beli->status          =   1;
                    // $list_beli->harga           =   $request->harga[$x] ;
                    // $list_beli->estiamsi        =   $request->estiamsi[$x] ;
                    if (!$list_beli->save()) {
                        DB::rollBack();
                        return back()->with('status', 2)->with('message', 'Proses gagal');
                    }
                }
            } else {
                DB::rollBack();
                return back()->with('status', 2)->with('message', 'Proses gagal');
            }

            DB::commit();
            return back()->with('status', 1)->with('message', 'Tambah item berhasil');
        }

        if ($request->key == 'approve') {

            $data           =   Pembelianlist::find($request->id);
            $data->status   =   1;
            $data->save();

            $result['status']   =   200;
            $result['msg']      =   "Approve pembelian berhasil";
            return $result;
        }

        if ($request->key == 'hapus_item') {
            Pembelianlist::find($request->id)->delete();

            $return['status']   =   200;
            $return['msg']      =   'Hapus item pembelian berhasil';
            return $return;
        }

        if ($request->key == 'batal_semua') {
            Pembelian::find($request->id)->delete();

            $return['status']   =   200;
            $return['msg']      =   'Pembelian berhasil dibatalkan';
            return $return;
        }

        if ($request->key == 'batalkanPR') {
            $data           =   Pembelian::find($request->id);
            $listdata       =   Pembelianlist::where('pembelian_id', $request->id)->where('deleted_at', NULL)->delete();
            $data->delete();

            $return['status']   =   200;
            $return['msg']      =   'PR berhasil dibatalkan';
            return response()->json($return);
        }

        if ($request->key == 'selesaikan') {
            // Validasi setelah input nomor PR
            DB::beginTransaction();
            $validate = Pembelian::where('no_pr', $request->no_pr)->where('pembelian.subsidiary', 'like', '%' . Session::get('subsidiary') . '%')->where('id', '!=', $request->id_submit)->first();
            if ($validate) {
                DB::rollBack();
                return back()->with('status', 2)->with('message', 'Nomor PR sudah digunakan');
            } else {
                $pembelian              =   Pembelian::where('status', 9)->where('user_id', Auth::user()->id)
                    ->where('id', $request->id_submit)
                    ->where('pembelian.subsidiary', 'like', '%' . Session::get('subsidiary') . '%')
                    // ->whereHas('user',function($q){
                    //     $q->where('users.company_id',Session::get('subsidiary_id'));
                    // })
                    ->first();

                if ($pembelian) {
                    $pembelian_list = Pembelianlist::where('pembelian_id', $pembelian->id)
                        ->get();

                    if (count($pembelian_list) == 0) {
                        DB::rollBack();
                        return back()->with('status', 2)->with('message', 'Proses gagal, item masih kosong');
                    }

                    $pembelian->divisi      =   $request->keterangan;
                    $pembelian->tanggal     =   $request->tanggal ?? date('Y-m-d');
                    $pembelian->no_pr       =   $request->no_pr ?? "";
                    $pembelian->status      =   2;
                    if ($request->hasFile('file')) {
                        // image's folder
                        $folder             = 'purchase_request/' . date('Y');
                        // image's filename
                        $newName            = "pr-" . $request->no_pr . "-" . date('Ymd-His');
                        // image's form field name
                        $form_name          = 'file';
                        $pembelian->foto    = StoreImage::saveImage($request, $folder, $newName, $form_name);
                    }
                    if (!$pembelian->save()) {
                        DB::rollBack();
                        return back()->with('status', 2)->with('message', 'Proses gagal');
                    }
                    DB::commit();
                    return redirect()->route('pembelian.index')->with('status', 1)->with('message', 'Request pembelian berhasil dibuat');
                } else {
                    DB::rollBack();
                    return back()->with('status', 2)->with('message', 'Pembelian Gagal');
                }
            }
        }

        if ($request->key == 'buat_pembelian') {
            // dd($request->all());
            // Validasi nomor form PR
            DB::beginTransaction();
            $pembelian = Pembelian::where('no_pr', $request->no_pr)->where('pembelian.subsidiary', 'like', '%' . Session::get('subsidiary') . '%')->first();
            if ($pembelian) {
                DB::rollBack();
                return back()->with('status', 2)->with('message', 'Nomor PR sudah digunakan');
            } else {
                $pembelian              =   new Pembelian;
                $pembelian->user_id     =   Auth::user()->id;
                $pembelian->divisi      =   $request->keterangan;
                $pembelian->tanggal     =   $request->tanggal ?? date('Y-m-d');
                $pembelian->no_pr       =   $request->no_pr ?? "";
                $pembelian->subsidiary  =   Session::get('subsidiary');

                if ($request->hasFile('file')) {
                    // image's folder
                    $folder             = 'purchase_request/' . date('Y');
                    // image's filename
                    $newName            = "pr-" . $request->no_pr . "-" . date('Ymd-His');
                    // image's form field name
                    $form_name          = 'file';
                    $pembelian->foto    = StoreImage::saveImage($request, $folder, $newName, $form_name);
                }

                $pembelian->status      =   9;
                if (!$pembelian->save()) {
                    DB::rollBack();
                    return back()->with('status', 2)->with('message', 'Proses gagal');
                }
                $id = $pembelian->id;
                DB::commit();
                return view('admin.pages.pembelian_barang.input_detail', compact('pembelian', 'item', 'id'));
            }

            // return redirect()->route('pembelian.detail', $pembelian->id)->with('status', 1)->with('message', 'Keterangan pembelian berhasil dibuat. Silahkan tambahkan item');
            // return back()->with('status', 1)->with('message', 'Keterangan pembelian berhasil dibuat. Silahkan tambahkan item');
        }

        if ($request->key == 'updatesummaryPR') {
            DB::beginTransaction();
            // Validasi nomor form PR
            $validate = Pembelian::where('no_pr', $request->no_pr)->where('pembelian.subsidiary', 'like', '%' . Session::get('subsidiary') . '%')->where('id', '!=', $request->id_submit)->first();
            if ($validate) {
                DB::rollBack();
                return back()->with('status', 2)->with('message', 'Nomor PR sudah digunakan');
            } else {
                $pembelian              = Pembelian::find($request->id_submit);
                $pembelian->divisi      =   $request->keterangan;
                $pembelian->tanggal     =   $request->tanggal ?? date('Y-m-d');
                $pembelian->no_pr       =   $request->no_pr ?? "";
                if ($request->hasFile('file')) {
                    // image's folder
                    $folder             = 'purchase_request/' . date('Y');
                    // image's filename
                    $newName            = "pr-" . $request->no_pr . "-" . date('Ymd-His');
                    // image's form field name
                    $form_name          = 'file';
                    $pembelian->foto    = StoreImage::saveImage($request, $folder, $newName, $form_name);
                }
                if (!$pembelian->save()) {
                    DB::rollBack();
                    return back()->with('status', 2)->with('message', 'Proses gagal');
                }
                DB::commit();
                return back()->with('status', 1)->with('message', 'Request pembelian berhasil diubah');
            }
        }
    }

    public function riwayat(Request $request)
    {
        $filterSummaryPR = $request->filterSummaryPR ?? '';
        $tanggal_awal    = $request->awal ?? date("Y-m-d");
        $tanggal_akhir   =  $request->akhir ?? date("Y-m-d");
        $clone           =   Pembelian::whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])
                                ->where('subsidiary', Session::get('subsidiary'))
                                ->where(function ($query) use ($filterSummaryPR) {
                                    if (Auth::user()->account_role != 'superadmin' && User::setIjin(50)) {
                                        $query->where('pembelian.divisi', 'gudang');
                                    }
                                    if (Auth::user()->account_role != 'superadmin' && User::setIjin(51)) {
                                        $query->where('pembelian.divisi', 'produksi');
                                    }
                                    if (Auth::user()->account_role != 'superadmin' && User::setIjin(52)) {
                                        $query->where('pembelian.divisi', 'pembangunan');
                                    }
                                    if (Auth::user()->account_role != 'superadmin' && User::setIjin(53)) {
                                        $query->where('pembelian.divisi', 'accounting');
                                    }
                                    // if(Auth::user()->account_role != 'superadmin' && User::setIjin(54)){
                                    //     $query->where('pembelian.divisi', 'purchasing');
                                    // }
                                    if (Auth::user()->account_role != 'superadmin' && User::setIjin(55)) {
                                        $query->where('pembelian.divisi', 'marketing');
                                    }
                                    if (Auth::user()->account_role != 'superadmin' && User::setIjin(56)) {
                                        $query->where('pembelian.divisi', 'engineering');
                                    }
                                    if (Auth::user()->account_role != 'superadmin' && User::setIjin(57)) {
                                        $query->where('pembelian.divisi', 'direktur');
                                    }
                                })
                                ->where(function ($query) use ($filterSummaryPR) {
                                    $query->whereHas('pr_po', function ($query) use ($filterSummaryPR) {
                                        $query->where('pembelianheader.document_number', 'like', '%' . $filterSummaryPR . '%');
                                        $query->where('pembelianheader.subsidiary', Session::get('subsidiary'));
                                    })
                                        ->orwhereHas('list_beli.item', function ($q) use ($filterSummaryPR) {
                                            $q->where('items.nama', 'like', '%' . $filterSummaryPR . '%');
                                        })
                                        ->orWhere('pembelian.divisi', 'like', '%' . $filterSummaryPR . '%')
                                        ->orWhere('pembelian.no_pr', 'like', '%' . $filterSummaryPR . '%');;
                                })
                                ->where('status', '2')
                                ->orderBy('pembelian.id', 'desc');

            $cloneDataPR     =  clone $clone;
            $cloneSummaryPR  =  clone $clone;      
            
            $data        =  $cloneDataPR->withTrashed()->get();
            $dataDownload        =  $cloneSummaryPR->get();

            if ($request->key == 'view') {
                if ($request->get == 'unduh') {
                return view('admin.pages.pembelian_barang.riwayat.excel', compact('dataDownload', 'request'));
            } else {
                return view('admin.pages.pembelian_barang.riwayat.index', compact('data', 'tanggal_awal', 'tanggal_akhir'));
            }
        } else if ($request->key == 'summaryPR') {
            if ($request->get == 'unduh') {
                return view('admin.pages.pembelian_barang.riwayat.excel', compact('dataDownload', 'request'));
            } else {
                return view('admin.pages.pembelian_barang.riwayat.summaryPR', compact('data', 'tanggal_awal', 'tanggal_akhir'));
            }
        } else if ($request->key == 'editsummaryPR') {
            // $item       = Item::where('subsidiary', Session::get('subsidiary'))->where('category_id', '>', 20)->where('type', '!=', null)->get();
            $item       = Item::where('subsidiary', 'like', '%' . Session::get('subsidiary') . '%')->where('category_id', '>=', 20)->get();
            $pembelian      =   Pembelian::find($request->id);
            $pembelianList  =   Pembelianlist::where('pembelian_id', $request->id)->get();
            return view('admin.pages.pembelian_barang.riwayat.editriwayatpr', compact('pembelian', 'item', 'pembelianList'));
            // dd($request->all());

        }
        // else {
        //     return view('admin.pages.pembelian_barang.riwayat.index', compact('data','tanggal_awal', 'tanggal_akhir'));
        // }
    }

    public function purchase(Request $request)
    {
        if ($request->key == 'view') {
            // dd(Pembelianlist::where('pembelian_id', '198')->where('sisa', '>', 0)->where('headbeli_id', NULL)->get());
            $tanggal_mulai_view = $request->tanggal_mulai_data_view ?? date('Y-m-d');
            $tanggal_akhir_view = $request->tanggal_akhir_data_view ?? date('Y-m-d');
            $filterListPR       = $request->filterListPR ?? '';
            $itemsisa           = $request->itemsisa;

            $header             =    Pembelianheader::where('status', 1)
                ->where('user_id', Auth::user()->id)
                ->where('subsidiary', Session::get('subsidiary'))
                ->first();

            $clone              =   Pembelian::select('pembelian.*')
                ->join('pembelian_list', 'pembelian_list.pembelian_id', 'pembelian.id')
                ->where('pembelian.status', 2)
                ->where('pembelian_list.status', 1)
                ->where('pembelian.subsidiary', 'like', '%' . Session::get('subsidiary') . '%')
                ->whereBetween('tanggal', [$tanggal_mulai_view, $tanggal_akhir_view])
                ->where(function ($query) use ($filterListPR) {
                    $query->orWhere('no_pr', 'like', '%' . $filterListPR . '%');
                    $query->orWhere('divisi', 'like', '%' . $filterListPR . '%');
                })
                ->where(function ($query) use ($itemsisa, $tanggal_mulai_view, $tanggal_akhir_view) {
                    if ($itemsisa == 'true') {
                        $query->where('pembelian_list.headbeli_id', NULL);
                        $query->where('pembelian_list.sisa', '>', 0);
                        $query->whereBetween('pembelian_list.created_at', [$tanggal_mulai_view . " 00:00:00", $tanggal_akhir_view . " 23:59:59"]);
                        $query->where('pembelian_list.deleted_at', NULL);
                    }
                })
                ->groupBy('no_pr');

            $clonedata          = clone $clone;
            $cloneitemsisa      = clone $clone;
            $data               = $clonedata->paginate(10);
            $dataitemsisa       = $cloneitemsisa->count();

            return view('admin.pages.pembelian_barang.purchase.view', compact('data', 'header', 'dataitemsisa', 'itemsisa', 'tanggal_mulai_view', 'tanggal_akhir_view', 'filterListPR'));
        } else
        if ($request->key == 'viewperitem') {
            $tanggal_mulai_view = $request->tanggal_mulai_data_view ?? date('Y-m-d');
            $tanggal_akhir_view = $request->tanggal_akhir_data_view ?? date('Y-m-d');
            $filterListPR       = $request->filterListPR ?? '';
            $itemsisa           = $request->itemsisa;

            $header     =   Pembelianheader::where('status', 1)->where('subsidiary', 'like', '%' . Session::get('subsidiary') . '%')->where('user_id', Auth::user()->id)->first();

            $clone       =   PembelianList::select('pembelian_list.*', 'items.id as itemID')
                ->join('pembelian', 'pembelian_list.pembelian_id', 'pembelian.id')
                ->join('items', 'pembelian_list.item_id', 'items.id')
                ->where('pembelian.status', 2)
                ->where('pembelian_list.status', 1)
                ->where('pembelian.subsidiary', 'like', '%' . Session::get('subsidiary') . '%')
                // ->whereBetween('tanggal', [$tanggal_mulai_view, $tanggal_akhir_view])
                ->whereBetween('pembelian_list.created_at', [$tanggal_mulai_view . " 00:00:00", $tanggal_akhir_view . " 23:59:59"])
                ->where(function ($query) use ($filterListPR) {
                    $query->orWhere('items.nama', 'like', '%' . $filterListPR . '%');
                    $query->orWhere('items.sku', 'like', '%' . $filterListPR . '%');
                })
                ->where(function ($query) use ($itemsisa, $tanggal_mulai_view, $tanggal_akhir_view) {
                    if ($itemsisa == 'true') {
                        $query->where('pembelian_list.sisa', '>', 0);
                        $query->where('pembelian_list.deleted_at', NULL);
                    }
                })
                ->groupBy('item_id');

            $clonedata          = clone $clone;
            $cloneitemsisa      = clone $clone;
            $data               = $clonedata->paginate(10);
            $dataitemsisa       = $cloneitemsisa->count();
            // dd($data);
            return view('admin.pages.pembelian_barang.purchase.viewperitem', compact('data', 'header', 'dataitemsisa', 'itemsisa', 'tanggal_mulai_view', 'tanggal_akhir_view', 'filterListPR'));
        } else
        if ($request->key == 'info') {
            $header     =   Pembelianheader::where('status', 1)
                ->where(function ($query) {
                    $query->where('user_id', Auth::user()->id);
                })
                ->where('subsidiary', 'like', '%' . Session::get('subsidiary') . '%')
                ->first();
            $supplier   =   Supplier::where('peruntukan', 'like', '%' . Session::get('subsidiary') . '%')
                ->whereNotNull('netsuite_internal_id')
                ->orderBy('nama')
                ->get();
            return view('admin.pages.pembelian_barang.purchase.form-po-umum', compact('header', 'supplier'));
        } else

        if ($request->key == 'list') {
            $item       =   Item::get();
            $filteritemdraft = $request->filteritemdraft;
            $header =   Pembelianheader::where('status', 1)->where('user_id', Auth::user()->id)->where('subsidiary', Session::get('subsidiary'))->first();
            if ($header) {
                $data   =   Pembelianlist::where('headbeli_id', $header->id)
                    ->whereHas('item', function ($query) use ($filteritemdraft) {
                        if ($filteritemdraft !== '') {
                            $query->where('items.nama', 'like', '%' . $filteritemdraft . '%')
                                ->orWhere('items.sku', 'like', '%' . $filteritemdraft . '%')
                                ->orWhere('qty', 'like', '%' . $filteritemdraft . '%')
                                ->orWhere('harga', 'like', '%' . $filteritemdraft . '%');
                        }
                    })->get();
                return view('admin.pages.pembelian_barang.purchase.list', compact('header', 'data', 'item'));
            } else {
                return '';
            }
        } else

        if ($request->key == 'summary') {
            $filterSummaryPO    =   $request->filterSummaryPO ?? '';
            $vendorRequest      =   $request->vendorPO ?? '';
            $filterNetsuite     =   $request->netsuiteFilterPO ?? '';
            $data   =   Pembelianheader::whereBetween('tanggal', [($request->tanggal_mulai ?? date("Y-m-d")), ($request->tanggal_akhir ?? date("Y-m-d"))])
                ->where('subsidiary', Session::get('subsidiary'))
                ->where(function ($query) use ($filterSummaryPO, $vendorRequest) {
                    if ($filterSummaryPO) {
                        $query->orWhere('no_pr', 'like', '%' . $filterSummaryPO . '%');
                        $query->orWhere('document_number', 'like', '%' . $filterSummaryPO . '%');
                        $query->orWhere('type_po', 'like', '%' . $filterSummaryPO . '%');
                    }
                })
                ->where(function ($query) use ($vendorRequest) {
                    if ($vendorRequest) {
                        $query->where('supplier_id', $vendorRequest);
                    }
                })
                ->where(function ($query) use ($filterNetsuite) {
                    if ($filterNetsuite !== '') {
                        if ($filterNetsuite == '4' || $filterNetsuite == '9') {
                            $query->where('status', $filterNetsuite);
                        } else {
                            $query->where('netsuite_status', $filterNetsuite);
                        }
                    }
                })
                ->withTrashed()
                ->orderByDesc('id')
                ->paginate(15);

            if ($request->get == 'unduh') {
                return view('admin.pages.pembelian_barang.purchase.excel', compact('data'));
            } else {
                return view('admin.pages.pembelian_barang.purchase.summary', compact('data'));
            }
        } else
        if ($request->key == 'vendorPO') {
            $filterSummaryPO    =   $request->filterSummaryPO ?? '';
            $vendorRequest      =   $request->vendorPO ?? '';
            $vendorPO           =   Pembelianheader::whereBetween('tanggal', [($request->tanggal_mulai ?? date("Y-m-d")), ($request->tanggal_akhir ?? date("Y-m-d"))])
                ->where('subsidiary', Session::get('subsidiary'))
                ->where(function ($query) use ($filterSummaryPO) {
                    if ($filterSummaryPO) {
                        $query->orWhere('no_pr', 'like', '%' . $filterSummaryPO . '%');
                        $query->orWhere('document_number', 'like', '%' . $filterSummaryPO . '%');
                        $query->orWhere('type_po', 'like', '%' . $filterSummaryPO . '%');
                    }
                })
                ->groupBy('supplier_id')
                ->get('supplier_id');

            return view('admin.pages.pembelian_barang.purchase.vendor', compact('vendorPO', 'vendorRequest'));
        } else

        if ($request->key == 'detail') {
            $data               =   Pembelianheader::where('id', $request->id)->where('subsidiary', 'like', '%' . Session::get('subsidiary') . '%')->first();
            $list               =   Pembelianlist::where('headbeli_id', $request->id)->get();
            $supplier           =   Supplier::orderBy('nama')
                                    ->where('peruntukan', 'like', '%' . Session::get('subsidiary') . '%')
                                    ->whereNotNull('netsuite_internal_id')
                                    ->get();
            $items              =   Item::where('nama', 'like', '%ayam%')->where('category_id', '=', 22)->select('id','nama','category_id')->get();
            $daftar_pembelian   =   Pembelianlist::where('headbeli_id', NULL)
                                    ->select('pembelian_list.*')
                                    ->join('pembelian', 'pembelian.id', '=', 'pembelian_list.pembelian_id')
                                    ->where('pembelian.subsidiary', Session::get('subsidiary'))
                                    ->where('pembelian_list.status', 1)
                                    ->where('sisa', '>', 0)
                                    ->get();
                                    
            $gudangPO           =   Gudang::whereIn('code', [Session::get('subsidiary') . ' - Chiller Bahan Baku', Session::get('subsidiary') . ' - Chiller Finished Good', Session::get('subsidiary') . ' - Storage ABF'])->get();

            if ($request->type !== 'PO LB' && $request->type !== 'PO Non Karkas' && $request->type !== 'PO Karkas' && $request->type !== 'PO Evis' && $request->type !== 'PO Transit') {
                return view('admin.pages.pembelian_barang.purchase.detail-purchase', compact('data', 'list', 'supplier', 'items', 'daftar_pembelian'));
            } elseif ($request->type == 'PO Karkas') {
                return view('admin.pages.pembelian_barang.purchase.detail-POKarkas', compact('data', 'list', 'supplier', 'items', 'daftar_pembelian', 'gudangPO'));
            } elseif ($request->type == 'PO LB' || $request->type == 'PO Transit') {
                return view('admin.pages.pembelian_barang.purchase.detail-POLB', compact('data', 'list', 'supplier', 'items', 'daftar_pembelian'));
            } elseif ($request->type == 'PO Non Karkas' || $request->type == 'PO Evis') {
                return view('admin.pages.pembelian_barang.purchase.detail-PONonKarkas', compact('data', 'list', 'supplier', 'items', 'daftar_pembelian', 'gudangPO'));
            }
        } else

        if ($request->key == 'historyPO') {
            // dd($request->all());
            // return response()->json($request->all());
            $subsidary = Session::get('subsidiary');
            $item_id  =  $request->item_id ?? '';
            $idrow    =  $request->idrow ?? '';
            $items    =  Item::find($item_id);
            $subkey   =  $request->subkey ?? '';
            if ($subkey) {
                if ($subkey == 'polb') {
                    $subkey         = 'PO LB';
                } else if ($subkey == 'pokarkas') {
                    $subkey         = 'PO Karkas';
                } else if ($subkey == 'pononkarkas') {
                    $subkey         = 'PO Non Karkas';
                } else if ($subkey == 'pokarkasfrozen') {
                    $subkey         = 'PO Karkas Frozen';
                }
                $history  =   Pembelianlist::whereHas('headbeli', function ($query) use ($subkey, $item_id, $subsidary) {
                    $query->where('subsidiary', $subsidary);
                    if ($subkey == 'PO LB') {
                        $query->where('type_PO', 'PO LB');
                        $query->orWhere('type_PO', 'PO Transit');
                    } else if ($subkey == 'PO Karkas') {
                        $query->where('type_PO', 'PO Karkas');
                    } else if ($subkey == 'PO Non Karkas') {
                        $query->where('type_PO', 'PO Non Karkas');
                        $query->where('item_id', $item_id);
                        $query->orWhere('type_po', 'PO Evis');
                    } else if ($subkey == 'PO Karkas Frozen') {
                        $query->where('type_PO', 'PO Karkas');
                        $query->where('item_id', $item_id);
                    } else {
                        $query->where('item_id', $item_id)->where('headbeli_id', '!=', '');
                    }
                })
                    ->orderBy('id', 'desc')->paginate(5);
                return view('admin.pages.pembelian_barang.purchase.history.history-po', compact('history', 'subkey', 'items', 'idrow'));
            }
        } else {
            $header         =   Pembelianheader::where('status', 1)->where('subsidiary', 'like', '%' . Session::get('subsidiary') . '%')->first();
            $supplier       =   Supplier::orderBy('nama')
                ->where('peruntukan', 'like', '%' . Session::get('subsidiary') . '%')
                ->whereNotNull('netsuite_internal_id')
                ->get();

                $pembelian  =    Pembelian::where('status', 9)->where('user_id', Auth::user()->id)->where('pembelian.subsidiary', 'like', '%' . Session::get('subsidiary') . '%')->paginate(10);

                $items       = Item::where('nama', 'LIKE', '%AYAM%')->where('category_id', '=',22)->select('id','nama','category_id')->get();

            return view('admin.pages.pembelian_barang.purchase.index', compact('supplier', 'header', 'items'));
        }
    }


    public function purchaselb(Request $request)
    {

        if ($request->jumlah_do > 50) {
            return back()->with('status', 2)->with('message', 'Jumlah DO Melebihi 50');
        } else {
            DB::beginTransaction();

            $header                 =   new Pembelianheader();
            $header->supplier_id    =   $request->supplier;
            $header->tanggal        =   $request->tanggal;
            $header->tanggal_kirim  =   $request->tanggal_kirim;
            $header->type_po        =   $request->type_po;
            $header->memo           =   $request->memo;
            $header->form_id        =   $request->form_id;
            $header->link_url       =   $request->url_link;
            $header->subsidiary     =   Session::get('subsidiary');
            if ($request->form_id == "156") {
                $header->form_name  = "EBA - Form Purchase Order Ayam";
            } else if ($request->form_id == "157") {
                $header->form_name  = "EBA - Form Purchase Order Non Ayam";
            } else if ($request->form_id == "131") {
                $header->form_name  = "CGL - Form Purchase Order Ayam";
            } else if ($request->form_id == "132") {
                $header->form_name  = "CGL - Form Purchase Order Non Ayam";
            }
            $header->jenis_ekspedisi    =   $request->jenis_ekspedisi;
            $header->status             =   2;
            $header->save();

            if ($header) {

                // if ($request->ukuran_ayam == '21') {
                //     $data_item          = Item::where('sku', '1100000009')->first();

                // } else {
                //     $data_item          = Item::where('sku', '1100000011')->first();

                // }

                $list               =   new Pembelianlist;
                $list->parent       =   NULL;
                $list->headbeli_id  =   $header->id;
                $list->pembelian_id =   NULL;
                $list->item_id      =   $request->item;
                $list->unit         =   'Rit';
                $list->qty          =   $request->qty;
                $list->berat        =   $request->berat;
                $list->harga        =   str_replace(".", "", $request->harga);
                $list->estimasi     =   NULL;
                $list->gudang       =   Gudang::gudang_netid(Session::get('subsidiary') . " - Storage Live Bird");
                $list->jumlah_do            =   $request->jumlah_do;
                $list->ukuran_ayam          =   $request->ukuran_ayam;
                $list->unit_cetakan         =   $request->unit_cetakan;

                if ($list->save()) {

                    DB::commit();

                    $push_ns = array(
                        'key' => 'proses_netsuite',
                        'id'  => $header->id
                    );
                    $req = new Request($push_ns);
                    return $this->purchasestore($req);
                }
            } else {
                DB::rollBack();
            }
        }
    }

    public function pononkarkas(Request $request)
    {
        DB::beginTransaction();

        // return $request->all();

        $header                 =   new Pembelianheader();
        $header->supplier_id    =   $request->supplier;
        $header->tanggal        =   $request->tanggal;
        $header->tanggal_kirim  =   $request->tanggal_kirim;
        $header->type_po        =   $request->type_po;
        $header->memo           =   $request->memo;
        $header->form_id        =   $request->form_id;
        $header->vendor_name    =   $request->vendor_name;
        $header->subsidiary     =   Session::get('subsidiary');
        if ($request->form_id == "156") {
            $header->form_name  = "EBA - Form Purchase Order Ayam";
        } else if ($request->form_id == "157") {
            $header->form_name  = "EBA - Form Purchase Order Non Ayam";
        } else if ($request->form_id == "131") {
            $header->form_name  = "CGL - Form Purchase Order Ayam";
        } else if ($request->form_id == "132") {
            $header->form_name  = "CGL - Form Purchase Order Non Ayam";
        }
        $header->jenis_ekspedisi    =   $request->jenis_ekspedisi;
        $header->link_url           = $request->url_link;
        $header->status             =   2;

        if ($header->save()) {

            if ($request->item > 0) {
                for ($x = 0; $x < COUNT($request->item); $x++) {

                    $data_item          = Item::find($request->item[$x]);

                    $list               =   new Pembelianlist;

                    $list->parent       =   NULL;
                    $list->headbeli_id  =   $header->id;
                    $list->pembelian_id =   NULL;
                    $list->item_id      =   $data_item->id;
                    $list->qty          =   $request->qty[$x];
                    $list->berat        =   $request->berat[$x];
                    $list->unit_cetakan =   $request->unit_cetakan[$x];
                    $list->keterangan   =   $request->keterangan[$x];
                    $list->ukuran_ayam  =   "1";
                    $list->jumlah_do    =   "1";
                    $list->harga        =   str_replace(".", "", $request->harga[$x]);
                    $list->estimasi     =   NULL;
                    $list->gudang       =   Gudang::gudang_netid($request->gudang[$x]);
                    $list->save();
                }
            } else {
                DB::rollBack();
                return back()->with('status', 2)->with('message', 'Proses Gagal');
            }

            if ($header) {

                DB::commit();

                $push_ns = array(
                    'key' => 'proses_netsuite',
                    'id'  => $header->id
                );
                $req = new Request($push_ns);
                return $this->purchasestore($req);
            } else {
                DB::rollBack();
                return back()->with('status', 2)->with('message', 'Proses Gagal');
            }
        }
    }

    public function pokarkas(Request $request)
    {
        // return $request->all();

        DB::beginTransaction();


        $header                 =   new Pembelianheader();
        $header->supplier_id    =   $request->supplier;
        $header->tanggal        =   $request->tanggal;
        $header->tanggal_kirim  =   $request->tanggal_kirim;
        $header->type_po        =   $request->type_po;
        $header->memo           =   $request->memo;
        $header->link_url       =   $request->url_link;
        $header->form_id        =   $request->form_id;
        $header->subsidiary     =   Session::get('subsidiary');
        if ($request->form_id == "156") {
            $header->form_name  = "EBA - Form Purchase Order Ayam";
        } else if ($request->form_id == "157") {
            $header->form_name  = "EBA - Form Purchase Order Non Ayam";
        } else if ($request->form_id == "131") {
            $header->form_name  = "CGL - Form Purchase Order Ayam";
        } else if ($request->form_id == "132") {
            $header->form_name  = "CGL - Form Purchase Order Non Ayam";
        }
        $header->jenis_ekspedisi    =   $request->jenis_ekspedisi;
        $header->status             =   2;

        if ($header->save()) {

            if ($request->item > 0) {
                for ($x = 0; $x < COUNT($request->item); $x++) {

                    $data_item          = Item::find($request->item[$x]);

                    $list               =   new Pembelianlist;

                    $list->parent       =   NULL;
                    $list->headbeli_id  =   $header->id;
                    $list->pembelian_id =   NULL;
                    $list->item_id      =   $data_item->id;
                    $list->qty          =   $request->qty[$x];
                    $list->berat        =   $request->berat[$x];
                    $list->unit_cetakan =   $request->unit_cetakan[$x];
                    $list->ukuran_ayam  =   "1";
                    $list->jumlah_do    =   "1";
                    $list->keterangan   =   $request->keterangan[$x];
                    $list->harga        =   str_replace(".", "", $request->harga[$x]);
                    $list->estimasi     =   NULL;
                    $list->gudang       =   Gudang::gudang_netid($request->gudang[$x]);
                    $list->save();
                }
            } else {
                DB::rollBack();
                return back()->with('status', 2)->with('message', 'Proses Gagal');
            }

            if ($header) {

                DB::commit();

                $push_ns = array(
                    'key' => 'proses_netsuite',
                    'id'  => $header->id
                );
                $req = new Request($push_ns);
                return $this->purchasestore($req);
            } else {
                DB::rollBack();
                return back()->with('status', 2)->with('message', 'Proses Gagal');
            }
        }
    }

    public function purchasestore(Request $request)
    {

        if ($request->key == 'batal_hapus_po') {
            foreach ($request->id as $key => $val) {
                Pembelianlist::withTrashed()->find($val)->restore();
            }
            return response()->json([
                'status'    =>  '200',
                'message'   =>  'Berhasil mengembalikan data'
            ]);
            // return response()->json($request->all());
        }

        if ($request->key == 'tambah_item') {
            if (!$request->qty) {
                $result['status']   =   400;
                $result['msg']      =   "Qty belum dimasukan";
                return $result;
            }

            if (!$request->harga) {
                $result['status']   =   400;
                $result['msg']      =   "Harga belum dimasukan";
                return $result;
            }

            DB::beginTransaction();

            $header             =   Pembelianheader::where('status', 1)->where('subsidiary', 'like', '%' . Session::get('subsidiary') . '%')->where('user_id', Auth::user()->id)->first() ?? new Pembelianheader;
            $header->app_po     =   $header->nomor_po_app;
            $header->status     =   1;
            if (!$header->save()) {
                DB::rollBack();
                $result['status']   =   400;
                $result['msg']      =   "Proses gagal";
                return $result;
            }


            $pembelian_list         =   Pembelianlist::find($request->id);
            if ($request->qty > $pembelian_list->sisa) {
                DB::rollBack();
                $result['status']   =   400;
                $result['msg']      =   "Proses gagal";
                return $result;
            }

            $pembelian_list->sisa   =   $pembelian_list->sisa - $request->qty;
            if (!$pembelian_list->save()) {
                DB::rollBack();
                $result['status']   =   400;
                $result['msg']      =   "Proses gagal";
                return $result;
            }

            $pembelian              =   Pembelian::find($pembelian_list->pembelian_id);

            if ($header->no_pr == "") {
                $header->no_pr          = $pembelian->no_pr;
                $header->pembelian_id   = $pembelian->id;
                $header->user_id        = Auth::user()->id ?? NULL;
                $header->save();
            } else {
                if ($header->pembelian_id == $pembelian->id) {
                } else {
                    DB::rollBack();
                    $result['status']   =   400;
                    $result['msg']      =   "No PR " . $header->pr . " berbeda!";
                    return $result;
                }
            }

            $data_item          = Item::find($pembelian_list->item_id);
            $list               =   new Pembelianlist;
            $list->parent       =   $pembelian_list->id;
            // $list->pembelian_id =   $header->id;
            $list->pembelian_id =   $pembelian->id;
            $list->item_id      =   $pembelian_list->item_id;
            $list->unit         =   $pembelian_list->unit;
            $list->qty          =   $request->qty;
            $list->berat        =   $request->berat;
            $list->harga        =   $request->harga;
            $list->estimasi     =   $request->estimasi;
            $list->gudang       =   $request->gudang;
            $list->keterangan   =   $request->keterangan;

            if ($data_item->category_id < 23) {
                $list->jumlah_do            =   $request->jumlah_do;
                $list->ukuran_ayam          =   $request->ukuran_ayam;
                $list->unit_cetakan         =   $request->unit_cetakan;
            }

            $list->headbeli_id  =   $header->id;
            if (!$list->save()) {
                DB::rollBack();
                $result['status']   =   400;
                $result['msg']      =   "Proses gagal";
                return $result;
            }

            DB::commit();
            $return['status']   =   200;
            $return['msg']      =   'Tambah item pembelian berhasil';
            return $return;
        }
        if ($request->key == 'destroy_edit_list') {
            DB::beginTransaction();

            $delete     =   Pembelianlist::find($request->idPOList);
            $old_beli   =   Pembelianlist::find($delete->parent);
            if ($old_beli) {
                $old_beli->sisa =   $old_beli->sisa + $delete->qty;
                if (!$old_beli->save()) {
                    DB::rollBack();
                    return back()->with('status', 2)->with('message', 'Proses gagal');
                }
            }


            $delete->delete();
            if (!$delete) {
                DB::rollBack();
                return response()->json([
                    'msg' => 'Gagal hapus',
                    'status' => 'error'
                ]);
            } else {


                $idheader = Pembelianlist::where('id', $request->idPOList)->withTrashed()->first()->headbeli_id;
                $pembelianlist = Pembelianlist::where('headbeli_id', $idheader)->withTrashed()->get();



                $cekdatalog = Adminedit::where('table_id', $idheader)->where('table_name', 'pembelian')->orderBy('id', 'DESC')->first();
                $pembelianheader = Pembelianheader::find($idheader);
                // Pembuata Log Data Awal
                if (!$cekdatalog) {
                    $pembelianheader = Pembelianheader::find($idheader);
                    $logawal                =   new Adminedit;
                    $logawal->user_id       =   Auth::user()->id;
                    $logawal->table_name    =   'pembelian';
                    $logawal->table_id      =   $idheader;
                    $logawal->type          =   'edit';
                    $logawal->activity      =   'pembelian';
                    $logawal->content       =   'Data Awal (Original)';
                    $logawal->data          =   json_encode([
                        'header' => $pembelianheader,
                        'list' => $pembelianlist
                    ]);
                    if (!$logawal->save()) {
                        DB::rollBack();
                        return back()->with('status', 2)->with('message', 'Proses simpan log gagal');
                    }
                }

                // Jika Sudah ada data awal
                $log                =   new Adminedit;
                $log->user_id       =   Auth::user()->id;
                $log->table_name    =   'pembelian';
                $log->table_id      =   $idheader;
                $log->type          =   'delete';
                $log->activity      =   'pembelian';
                $log->data          =   json_encode([
                    'header' => $pembelianheader,
                    'list' => $pembelianlist
                ]);
                // return response()->json($pembelianheader);

                if (!$cekdatalog) {
                    $log->content       =   'Data Edit Ke 1';
                } else {
                    $explodeedit        =   explode(' ', $cekdatalog->content);
                    if ($explodeedit[1] == "Awal") {
                        $log->content       =   'Data Edit Ke 1';
                    } else {
                        $log->content       =   'Data Edit Ke ' . ($explodeedit[3] + 1);
                    }
                }

                if (!$log->save()) {
                    DB::rollBack();
                    return back()->with('status', 2)->with('message', 'Proses simpan log gagal');
                }

                DB::commit();
                return response()->json([
                    'msg' => 'Berhasil hapus',
                    'status' => 'success'
                ]);
            }
        }
        if ($request->key == 'hapus_item') {
            $list               =   Pembelianlist::find($request->id);
            $pembelian          =   Pembelianlist::find($list->parent);
            $pembelian->sisa    =   $pembelian->sisa + $list->qty;
            $header              = Pembelianheader::find($list->headbeli_id);
            $pembelianheader = Pembelianheader::find(1);
            
            $header->update([
                'no_pr' => null
            ]);
            $pembelian->save();
            $list->delete();
            $return['status']   =   200;
            $return['msg']      =   'Hapus item pembelian berhasil';
            return $return;
        }

        if ($request->key == 'get_list') {
            $list               =   Pembelianlist::find($request->id);
            return response()->json($list);
        }

        if ($request->key == 'updatePurchaseList') {
            $list               =   Pembelianlist::find($request->id);
            if ($list) {


                $old                =   Pembelianlist::find($list->parent);
                if ($old) {
                    if ($request->qty > $old->qty) {
                        $result['status']   =   400;
                        $result['msg']      =   "Proses gagal. Jumlah melebihi PR";
                        return $result;
                    }

                    $dataGabungan = Pembelianlist::where('parent', $list->parent)->where('id', '!=', $request->id)->sum('qty');
                    if ($dataGabungan + $request->qty > $old->qty) {

                        $result['status']   =   400;
                        $result['msg']      =   "Proses gagal. Jumlah melebihi PR";
                        return $result;
                    }

                    $old->sisa          =   $old->sisa + $list->qty;
                     $old->save();


                    $list->qty          =   $request->qty;
                    $list->harga        =   $request->harga;
                    $list->item_id      =   $request->item_id;
                    $list->save();

                    $old->sisa          =   $old->sisa - $list->qty;
                    $old->save();

                    $pembelian          =   Pembelianheader::find($list->headbeli_id);

                    if ($pembelian) {
                        $pembelian->netsuite_status = 2;
                        $pembelian->save();
                    }

                    $return['status']   =   200;
                    $return['msg']      =   'Update item pembelian berhasil';
                    $return['data']     =   $pembelian;
                    return $return;
                }

                $result['status']   =   400;
                $result['msg']      =   "Proses gagal. Data tidak ditemukan";
                return $result;
            } else {
                $result['status']   =   400;
                $result['msg']      =   "Proses gagal. Data tidak ditemukan";
                return $result;
            }
        }

        if ($request->key == 'updateAllPurchaseList') {
            // dd($request->all
            DB::beginTransaction();
            //    Log Awal
            $cekdatalog             =   Adminedit::where('table_id', $request->ideditpurchase)->where('table_name', 'pembelian')->first();
            
            if (!$cekdatalog) {
                $cekdatalogbanyak       =   Adminedit::where('table_id', $request->ideditpurchase)->where('table_name', 'pembelian')->where('type', 'edit')->get();
                $pembelianheaderawal    =   Pembelianheader::find($request->ideditpurchase);
                $pembelianlistawal      =   Pembelianlist::where('headbeli_id', $request->ideditpurchase)->withTrashed()->get();
                $logawal                =   new Adminedit;
                $logawal->user_id       =   Auth::user()->id;
                $logawal->table_name    =   'pembelian';
                $logawal->table_id      =   $request->ideditpurchase;
                $logawal->type          =   'data';
                $logawal->activity      =   'pembelian';
                $logawal->data          =   json_encode([
                    'header'    =>  $pembelianheaderawal,
                    'list'      =>  $pembelianlistawal
                ]);

                $logawal->content       =   'Data Awal (Original)';
                if (!$logawal->save()) {
                    DB::rollBack();
                    return back()->with('status', 2)->with('message', 'Proses simpan log gagal');
                }
            }

            $header                     =   Pembelianheader::find($request->ideditpurchase);
            $header->supplier_id        =   $request->supplier;
            if ($header->tanggal == $request->tanggal ) {
                $header->tanggal        =   $header->tanggal;
            }else if ( $request->tanggal < date('Y-m-d')){
                return back()->with('status', 2)->with('message', 'Tanggal PO tidak bisa backdate');
            } else{
                $header->tanggal        =   $request->tanggal;
            }
            $header->tanggal_kirim      =   $request->tanggal_kirim;
            $header->type_po            =   $request->type_po;
            $header->memo               =   $request->keterangan_header;
            $header->form_id            =   $request->form_id;
            $header->franco_loco        =   $request->franko_loko;
            $header->link_url           =   $request->url_link;
            $header->jenis_ekspedisi    =   $request->jenis_ekspedisi;
            $header->vendor_name        =   $request->vendor_name;
            if ($request->pending != 'pending') {
                $header->netsuite_status     =   2;
                if ($request->form_id == "156") {
                    $header->form_name  = "EBA - Form Purchase Order Ayam";
                } else if ($request->form_id == "157") {
                    $header->form_name  = "EBA - Form Purchase Order Non Ayam";
                } else if ($request->form_id == "131") {
                    $header->form_name  = "CGL - Form Purchase Order Ayam";
                } else if ($request->form_id == "132") {
                    $header->form_name  = "CGL - Form Purchase Order Non Ayam";
                }
            }
            if (!$header->save()) {
                DB::rollBack();
                return back()->with('status', 2)->with('message', 'Proses gagal');
            }

            if ($request->hapusOngkir == 'on') {

                $list                   =   Pembelianlist::where('headbeli_id', $header->id)
                    ->whereIn('item_id', Item::select('id')->whereIn('sku', ['7000000009', '7000000011', '7000000012']))
                    ->first();
                if ($list) {
                    if (!$list->delete()) {
                        DB::rollBack();
                        return back()->with('status', 2)->with('message', 'Proses gagal');
                    }
                }
            } else {


                if ($request->ongkir > 0) {
                    $list                   =   Pembelianlist::where('headbeli_id', $header->id)
                        ->whereIn('item_id', Item::select('id')->whereIn('sku', ['7000000009', '7000000011', '7000000012']))
                        ->first() ?? new Pembelianlist;

                    $list->parent          =   NULL;
                    $list->headbeli_id     =   $header->id;
                    $list->item_id         =   Item::item_sku($request->ongkir_sku)->id;
                    $list->qty             =   1;
                    $list->gudang          =   Session::get('subsidiary') == 'CGL' ? '45' : '65';
                    $list->harga           =   str_replace(".", "", $request->ongkir);
                    $list->status          =   1;
                    if (!$list->save()) {
                        DB::rollBack();
                        return back()->with('status', 2)->with('message', 'Proses gagal');
                    }
                }
            }

            if ($request->item > 0) {
                for ($x = 0; $x < COUNT($request->item); $x++) {
                    $data_item             =   Item::find($request->item[$x]);
                    if ($request->idlistpo[$x] != '') {
                        $list_beli              =   Pembelianlist::where('id', $request->idlistpo[$x])->where('deleted_at', NULL)->first();
                        if ($list_beli) {
                            $old_beli           =   Pembelianlist::find($list_beli->parent);
                            if ($old_beli) {
                                $old_beli->sisa =   $old_beli->sisa + $list_beli->qty;
                                if (!$old_beli->save()) {
                                    DB::rollBack();
                                    return back()->with('status', 2)->with('message', 'Proses gagal');
                                }
                            }

                            $list_beli->qty             =   $request->qty[$x];
                            $list_beli->sisa            =   $request->qty[$x];
                            $list_beli->unit            =   $data_item->type;
                            $list_beli->keterangan      =   $request->keterangan[$x];
                            $list_beli->link_url        =   $request->link_url[$x] ?? NULL;
                            $list_beli->harga           =   $request->harga[$x];
                            $list_beli->jumlah_do       =   $request->jumlah_do[$x] ?? NULL;
                            $list_beli->ukuran_ayam     =   $request->ukuran_ayam[$x] ?? NULL;
                            $list_beli->unit_cetakan    =   $request->unit_cetakan[$x] ?? NULL;
                            $list_beli->berat           =   $request->berat[$x] ?? NULL;
                            $list_beli->gudang          =   $request->gudang[$x];

                            if ($old_beli) {
                                $old_beli->sisa             =   $old_beli->sisa - $list_beli->qty;
                                if (!$old_beli->save()) {
                                    DB::rollBack();
                                    return back()->with('status', 2)->with('message', 'Proses gagal');
                                }
                            }

                            if (!$list_beli->save()) {
                                DB::rollBack();
                                return back()->with('status', 2)->with('message', 'Proses gagal');
                            }
                        }
                    } else {
                        $list                  =   new Pembelianlist;
                        $list->parent          =   $request->id_list[$x];
                        $list->headbeli_id     =   $header->id;
                        $list->pembelian_id    =   $header->pembelian_id;
                        $list->item_id         =   $request->item[$x];
                        $list->qty             =   $request->qty[$x];
                        $list->sisa            =   $request->qty[$x];
                        $list->unit            =   $data_item->type;
                        $list->keterangan      =   $request->keterangan[$x];
                        $list->harga           =   $request->harga[$x];
                        $list->gudang          =   $request->gudang[$x];
                        $list->jumlah_do       =   $request->jumlah_do[$x] ?? NULL;
                        $list->ukuran_ayam     =   $request->ukuran_ayam[$x] ?? NULL;
                        $list->unit_cetakan    =   $request->unit_cetakan[$x] ?? NULL;
                        $list->berat           =   $request->berat[$x] ?? NULL;

                        $list->link_url        =   $request->link_url[$x] ?? NULL;
                        $list->status          =   1;
                        if (!$list->save()) {
                            DB::rollBack();
                            return back()->with('status', 2)->with('message', 'Proses gagal');
                        }

                        $old        =   Pembelianlist::find($request->id_list[$x]);
                        $old->sisa  =   $old->sisa - $list->qty;
                        if (!$old->save()) {
                            DB::rollBack();
                            return back()->with('status', 2)->with('message', 'Proses gagal');
                        }
                    }
                }
            } else {
                DB::rollBack();
                return back()->with('status', 2)->with('message', 'Proses gagal');
            }



            
            $cekdatalogsetelah        = Adminedit::where('table_name', 'pembelian')->where('table_id', $header->id)->orderBy('id', 'desc')->first();
            // dd($cekdatalogsetelah);
            if ($cekdatalogsetelah) {
                // Log setelah edit
    
                $pembelianheader           =   Pembelianheader::find($header->id);
                $pembelianlist             =   Pembelianlist::where('headbeli_id', $header->id)->withTrashed()->get();
                $logsetelah                =   new Adminedit;
                $logsetelah->user_id       =   Auth::user()->id;
                $logsetelah->table_name    =   'pembelian';
                $logsetelah->table_id      =   $header->id;
                $logsetelah->type          =   'edit';
                $logsetelah->activity      =   'pembelian';
                $logsetelah->data          =   json_encode([
                    'header'               =>  $pembelianheader,
                    'list'                 =>  $pembelianlist
                ]);
                
                $explodeedit        =   explode(' ', $cekdatalogsetelah->content);
                if ($explodeedit[1] == 'Awal') {
                    $logsetelah->content       =   'Data Edit Ke 1';
                } else {
                    $logsetelah->content       =   'Data Edit Ke ' . ($explodeedit[3] + 1);
                }
            }

            if (!$logsetelah->save()) {
                DB::rollBack();
                return back()->with('status', 2)->with('message', 'Proses simpan log gagal');
            }

            DB::commit();

            if ($request->pending != 'pending') {
                $push_ns = array(
                    'key' => 'proses_netsuite',
                    'id'  => $header->id
                );
                $req = new Request($push_ns);
                $this->purchasestore($req);
            }

            return back()->with('status', 1)->with('message', 'Edit purchase pembelian barang berhasil');
        }

        if ($request->key == 'updatePOKarkas') {
            DB::beginTransaction();
            // dd($request->all());
            //    Log Awal
            $cekdatalog             =   Adminedit::where('table_id', $request->idEditPOKarkas)->where('table_name', 'pembelian')->first();
            
            if (!$cekdatalog) {
                $cekdatalogbanyak       =   Adminedit::where('table_id', $request->idEditPOKarkas)->where('table_name', 'pembelian')->where('type', 'edit')->get();
                $pembelianheaderawal    =   Pembelianheader::find($request->idEditPOKarkas);
                $pembelianlistawal      =   Pembelianlist::where('headbeli_id', $request->idEditPOKarkas)->withTrashed()->get();
                $logawal                =   new Adminedit;
                $logawal->user_id       =   Auth::user()->id;
                $logawal->table_name    =   'pembelian';
                $logawal->table_id      =   $request->idEditPOKarkas;
                $logawal->type          =   'data';
                $logawal->activity      =   'pembelian';
                $logawal->data          =   json_encode([
                    'header'    =>  $pembelianheaderawal,
                    'list'      =>  $pembelianlistawal
                ]);
                $logawal->content       =   'Data Awal (Original)';
                if (!$logawal->save()) {
                    DB::rollBack();
                    return back()->with('status', 2)->with('message', 'Proses simpan log gagal');
                }
            }


            $header                 =   Pembelianheader::find($request->idEditPOKarkas);
            $header->supplier_id    =   $request->supplier;
            $header->tanggal        =   $request->tanggal;
            $header->tanggal_kirim  =   $request->tanggal_kirim;
            $header->type_po        =   $request->type_po;
            $header->memo           =   $request->memo;
            $header->form_id        =   $request->form_id;
            $header->link_url       =   $request->url_link;
            $header->netsuite_status     =   2;
            if ($request->form_id == "156") {
                $header->form_name  = "EBA - Form Purchase Order Ayam";
            } else if ($request->form_id == "157") {
                $header->form_name  = "EBA - Form Purchase Order Non Ayam";
            } else if ($request->form_id == "131") {
                $header->form_name  = "CGL - Form Purchase Order Ayam";
            } else if ($request->form_id == "132") {
                $header->form_name  = "CGL - Form Purchase Order Non Ayam";
            }
            $header->jenis_ekspedisi    =   $request->jenis_ekspedisi;
            if (!$header->save()) {
                DB::rollBack();
                return back()->with('status', 2)->with('message', 'Proses gagal');
            }

            // return $request->all();

            if (count($request->item) > 0) {
                for ($i = 0; $i < count($request->item); $i++) {
                    if (!isset($request->idListPOKarkas[$i])) {
                        $list               =  new Pembelianlist;
                    } else {
                        $list               =  Pembelianlist::find($request->idListPOKarkas[$i]);
                    }
                    $list->headbeli_id      =  $header->id;
                    $list->ukuran_ayam      =   "1";
                    $list->jumlah_do        =   "1";
                    $list->item_id          =  $request->item[$i];
                    $list->qty              =  $request->qty[$i];
                    $list->berat            =  $request->berat[$i];
                    $list->harga            =  str_replace(".", "", $request->harga[$i]);
                    $list->unit_cetakan     =  $request->unit_cetakan[$i];
                    $list->gudang           =  Gudang::gudang_netid($request->gudang[$i]);
                    $list->keterangan       = $request->keterangan[$i];
                    if (!$list->save()) {
                        DB::rollBack();
                        return back()->with('status', 2)->with('message', 'Proses gagal');
                    }
                }
            }

             // Log setelah edit

            $pembelianheader           =   Pembelianheader::find($header->id);
            $pembelianlist             =   Pembelianlist::where('headbeli_id', $header->id)->withTrashed()->get();
            $logsetelah                =   new Adminedit;
            $logsetelah->user_id       =   Auth::user()->id;
            $logsetelah->table_name    =   'pembelian';
            $logsetelah->table_id      =   $header->id;
            $logsetelah->type          =   'edit';
            $logsetelah->activity      =   'pembelian';
            $logsetelah->data          =   json_encode([
                'header'    =>  $pembelianheader,
                'list'      =>  $pembelianlist
            ]);

            $cekdatalogsetelah        = Adminedit::where('table_name', 'pembelian')->where('table_id', $header->id)->orderBy('id', 'desc')->first();
            // dd($cekdatalogsetelah);
            if ($cekdatalogsetelah) {
                $explodeedit        =   explode(' ', $cekdatalogsetelah->content);
                if ($explodeedit[1] == 'Awal') {
                    $logsetelah->content       =   'Data Edit Ke 1';
                } else {
                    $logsetelah->content       =   'Data Edit Ke ' . ($explodeedit[3] + 1);
                }
            }

            if (!$logsetelah->save()) {
                DB::rollBack();
                return back()->with('status', 2)->with('message', 'Proses simpan log gagal');
            }



            DB::commit();

            $push_ns = array(
                'key' => 'proses_netsuite',
                'id'  => $header->id
            );
            $req = new Request($push_ns);
            $this->purchasestore($req);

            return back()->with('status', 1)->with('message', 'Edit PO Karkas berhasil');
        }

        if ($request->key == 'updatePOLB') {

            // dd($request->all());
            if ($request->jumlah_do > 50) {
                return back()->with('status', 2)->with('message', 'Jumlah DO Melebihi 50');
            } else {
                DB::beginTransaction();

                //    Log Awal
                $cekdatalog             =   Adminedit::where('table_id', $request->idEditPOLB)->where('table_name', 'pembelian')->first();
                
                if (!$cekdatalog) {
                    $cekdatalogbanyak       =   Adminedit::where('table_id', $request->idEditPOLB)->where('table_name', 'pembelian')->where('type', 'edit')->get();
                    $pembelianheaderawal    =   Pembelianheader::find($request->idEditPOLB);
                    $pembelianlistawal      =   Pembelianlist::where('headbeli_id', $request->idEditPOLB)->withTrashed()->get();
                    $logawal                =   new Adminedit;
                    $logawal->user_id       =   Auth::user()->id;
                    $logawal->table_name    =   'pembelian';
                    $logawal->table_id      =   $request->idEditPOLB;
                    $logawal->type          =   'data';
                    $logawal->activity      =   'pembelian';
                    $logawal->data          =   json_encode([
                        'header'    =>  $pembelianheaderawal,
                        'list'      =>  $pembelianlistawal
                    ]);
                    $logawal->content       =   'Data Awal (Original)';
                    if (!$logawal->save()) {
                        DB::rollBack();
                        return back()->with('status', 2)->with('message', 'Proses simpan log gagal');
                    }
                }


                $header                 =   Pembelianheader::find($request->idEditPOLB);
                $header->supplier_id    =   $request->supplier;
                if ($header->tanggal == $request->tanggal ) {
                    $header->tanggal        =   $header->tanggal;
                }else if ( $request->tanggal < date('Y-m-d')){
                    return back()->with('status', 2)->with('message', 'Tanggal PO tidak bisa backdate');
                } else{
                    $header->tanggal        =   $request->tanggal;
                }
                $header->tanggal_kirim  =   $request->tanggal_kirim;
                $header->type_po        =   $request->type_po;
                $header->memo           =   $request->memo;
                $header->form_id        =   $request->form_id;
                $header->link_url       =   $request->url_link;
                $header->netsuite_status     =   2;
                if ($request->form_id == "156") {
                    $header->form_name  = "EBA - Form Purchase Order Ayam";
                } else if ($request->form_id == "157") {
                    $header->form_name  = "EBA - Form Purchase Order Non Ayam";
                } else if ($request->form_id == "131") {
                    $header->form_name  = "CGL - Form Purchase Order Ayam";
                } else if ($request->form_id == "132") {
                    $header->form_name  = "CGL - Form Purchase Order Non Ayam";
                }
                $header->jenis_ekspedisi    =   $request->jenis_ekspedisi;
                if (!$header->save()) {
                    DB::rollBack();
                    return back()->with('status', 2)->with('message', 'Proses gagal');
                }

                $list                   =  Pembelianlist::find($request->idListPOLB);
                $list->qty              =  $request->qty;
                $list->berat            =  $request->berat;
                $list->item_id          =  $request->item ?? $list->item_id;
                $list->harga            =  str_replace(".", "", $request->harga);
                $list->unit_cetakan     =  $request->unit_cetakan;
                $list->jumlah_do        =  $request->jumlah_do;
                $list->ukuran_ayam      =  $request->ukuran_ayam;
                $list->keterangan       =  $request->keterangan;
                
                // if ($request->ukuran_ayam == '21') {
                //     $list->item_id           = Item::where('sku', '1100000009')->first()->id;

                // } else {
                //     $list->item_id           = Item::where('sku', '1100000011')->first()->id;

                // }
                
                if (!$list->save()) {
                    DB::rollBack();
                    return back()->with('status', 2)->with('message', 'Proses gagal');
                }


                // Log setelah edit
                $pembelianheader           =   Pembelianheader::find($header->id);
                $pembelianlist             =   Pembelianlist::where('headbeli_id', $header->id)->withTrashed()->get();
                $logsetelah                =   new Adminedit;
                $logsetelah->user_id       =   Auth::user()->id;
                $logsetelah->table_name    =   'pembelian';
                $logsetelah->table_id      =   $header->id;
                $logsetelah->type          =   'edit';
                $logsetelah->activity      =   'pembelian';
                $logsetelah->data          =   json_encode([
                    'header'    =>  $pembelianheader,
                    'list'      =>  $pembelianlist
                ]);

                $cekdatalogsetelah        = Adminedit::where('table_name', 'pembelian')->where('table_id', $header->id)->orderBy('id', 'desc')->first();
                // dd($cekdatalogsetelah);
                if ($cekdatalogsetelah) {
                    $explodeedit        =   explode(' ', $cekdatalogsetelah->content);
                    if ($explodeedit[1] == 'Awal') {
                        $logsetelah->content       =   'Data Edit Ke 1';
                    } else {
                        $logsetelah->content       =   'Data Edit Ke ' . ($explodeedit[3] + 1);
                    }
                }

                if (!$logsetelah->save()) {
                    DB::rollBack();
                    return back()->with('status', 2)->with('message', 'Proses simpan log gagal');
                }

                DB::commit();

                $push_ns = array(
                    'key' => 'proses_netsuite',
                    'id'  => $header->id
                );
                $req = new Request($push_ns);
                $this->purchasestore($req);

                return back()->with('status', 1)->with('message', 'Edit PO berhasil');
            }
        }

        if ($request->key == 'updatePONonKarkas') {
            DB::beginTransaction();

                        //    Log Awal
            $cekdatalog             =   Adminedit::where('table_id', $request->idEditPONonKarkas)->where('table_name', 'pembelian')->first();

            if (!$cekdatalog) {
                $cekdatalogbanyak       =   Adminedit::where('table_id', $request->idEditPONonKarkas)->where('table_name', 'pembelian')->where('type', 'edit')->get();
                $pembelianheaderawal    =   Pembelianheader::find($request->idEditPONonKarkas);
                $pembelianlistawal      =   Pembelianlist::where('headbeli_id', $request->idEditPONonKarkas)->withTrashed()->get();
                $logawal                =   new Adminedit;
                $logawal->user_id       =   Auth::user()->id;
                $logawal->table_name    =   'pembelian';
                $logawal->table_id      =   $request->idEditPONonKarkas;
                $logawal->type          =   'data';
                $logawal->activity      =   'pembelian';
                $logawal->data          =   json_encode([
                    'header'    =>  $pembelianheaderawal,
                    'list'      =>  $pembelianlistawal
                ]);
                $logawal->content       =   'Data Awal (Original)';
                if (!$logawal->save()) {
                    DB::rollBack();
                    return back()->with('status', 2)->with('message', 'Proses simpan log gagal');
                }
            }


            $header                 =   Pembelianheader::find($request->idEditPONonKarkas);
            $header->supplier_id    =   $request->supplier;
            if ($header->tanggal == $request->tanggal ) {
                $header->tanggal        =   $header->tanggal;
            }else if ( $request->tanggal < date('Y-m-d')){
                return back()->with('status', 2)->with('message', 'Tanggal PO tidak bisa backdate');
            } else{
                $header->tanggal        =   $request->tanggal;
            }
            $header->tanggal_kirim  =   $request->tanggal_kirim;
            $header->type_po        =   $request->type_po;
            $header->form_id        =   $request->form_id;
            $header->link_url       =   $request->url_link;
            $header->netsuite_status     =   2;
            if ($request->form_id == "156") {
                $header->form_name  = "EBA - Form Purchase Order Ayam";
            } else if ($request->form_id == "157") {
                $header->form_name  = "EBA - Form Purchase Order Non Ayam";
            } else if ($request->form_id == "131") {
                $header->form_name  = "CGL - Form Purchase Order Ayam";
            } else if ($request->form_id == "132") {
                $header->form_name  = "CGL - Form Purchase Order Non Ayam";
            }
            $header->jenis_ekspedisi    =   $request->jenis_ekspedisi;
            if (!$header->save()) {
                DB::rollBack();
                return back()->with('status', 2)->with('message', 'Proses gagal');
            }
            if ($request->item > 0) {
                for ($x = 0; $x < COUNT($request->item); $x++) {
                    if ($request->idlistpononkarkas[$x] != '') {
                        $list_beli                  =   Pembelianlist::where('id', $request->idlistpononkarkas[$x])->where('deleted_at', NULL)->first();
                        if ($list_beli) {
                            $list_beli->item_id         =   $request->item[$x];
                            $list_beli->qty             =   $request->qty[$x];
                            $list_beli->berat           =   $request->berat[$x];
                            $list_beli->harga           =   str_replace(".", "", $request->harga[$x]);
                            $list_beli->unit_cetakan    =   $request->unit_cetakan[$x];
                            $list_beli->keterangan      =   $request->keterangan[$x];
                            $list_beli->gudang          =   Gudang::gudang_netid($request->gudang[$x]);
                            if (!$list_beli->save()) {
                                DB::rollBack();
                                return back()->with('status', 2)->with('message', 'Proses gagal');
                            }
                        }
                    } else {
                        $list                  =   new Pembelianlist;
                        $list->parent          =   NULL;
                        $list->headbeli_id     =   $header->id;
                        $list->pembelian_id    =   NULL;
                        $list->item_id         =   $request->item[$x];
                        $list->qty             =   $request->qty[$x];
                        $list->berat           =   $request->berat[$x];
                        $list->unit_cetakan    =   $request->unit_cetakan[$x];
                        $list->keterangan      =   $request->keterangan[$x];
                        $list->harga           =   str_replace(".", "", $request->harga[$x]);
                        $list->gudang          =   Gudang::gudang_netid($request->gudang[$x]);
                        $list->estimasi        =   NULL;
                        $list->status          =   1;
                        if (!$list->save()) {
                            DB::rollBack();
                            return back()->with('status', 2)->with('message', 'Proses gagal');
                        }
                    }
                }
            } else {
                DB::rollBack();
                return back()->with('status', 2)->with('message', 'Proses gagal');
            }

            // Log setelah edit

            $pembelianheader           =   Pembelianheader::find($header->id);
            $pembelianlist             =   Pembelianlist::where('headbeli_id', $header->id)->withTrashed()->get();
            $logsetelah                =   new Adminedit;
            $logsetelah->user_id       =   Auth::user()->id;
            $logsetelah->table_name    =   'pembelian';
            $logsetelah->table_id      =   $header->id;
            $logsetelah->type          =   'edit';
            $logsetelah->activity      =   'pembelian';
            $logsetelah->data          =   json_encode([
                'header'    =>  $pembelianheader,
                'list'      =>  $pembelianlist
            ]);

            $cekdatalogsetelah        = Adminedit::where('table_name', 'pembelian')->where('table_id', $header->id)->orderBy('id', 'desc')->first();
            // dd($cekdatalogsetelah);
            if ($cekdatalogsetelah) {
                $explodeedit        =   explode(' ', $cekdatalogsetelah->content);
                if ($explodeedit[1] == 'Awal') {
                    $logsetelah->content       =   'Data Edit Ke 1';
                } else {
                    $logsetelah->content       =   'Data Edit Ke ' . ($explodeedit[3] + 1);
                }
            }

            if (!$logsetelah->save()) {
                DB::rollBack();
                return back()->with('status', 2)->with('message', 'Proses simpan log gagal');
            }

            DB::commit();

            $push_ns = array(
                'key' => 'proses_netsuite',
                'id'  => $header->id
            );
            $req = new Request($push_ns);
            $this->purchasestore($req);

            return back()->with('status', 1)->with('message', 'Edit PO Non Karkas berhasil');
        }

        if ($request->key == 'buat_header' || $request->key == 'update_header') {
            DB::beginTransaction();
            if (!$request->supplier) {
                DB::rollBack();
                $return['status']   =   400;
                $return['msg']      =   'Supplier belum dipilih';
                return $return;
            }

            if (!$request->tanggal) {
                DB::rollBack();
                $return['status']   =   400;
                $return['msg']      =   'Tanggal belum dipilih';
                return $return;
            }

            if ($request->key == 'buat_header') {
                $header             =   new Pembelianheader;
            } else {
                $header             =   Pembelianheader::where('user_id', Auth::user()->id)->where('subsidiary', 'like', '%' . Session::get('subsidiary') . '%')->where('status', 1)->first();
            }
            $header->supplier_id    =   $request->supplier;
            $header->vendor_name    =   $request->vendor_name;
            $header->tanggal        =   $request->tanggal;
            $header->tanggal_kirim  =   $request->tanggal_kirim;
            $header->type_po        =   $request->type_po;
            $header->memo           =   $request->keterangan;
            $header->form_id        =   $request->form_id;
            $header->franco_loco    =   $request->franko_loko;
            $header->link_url       =   $request->link_url;
            $header->subsidiary     =   Session::get('subsidiary');
            if ($request->form_id == "156") {
                $header->form_name  = "EBA - Form Purchase Order Ayam";
            } else if ($request->form_id == "157") {
                $header->form_name  = "EBA - Form Purchase Order Non Ayam";
            } else if ($request->form_id == "131") {
                $header->form_name  = "CGL - Form Purchase Order Ayam";
            } else if ($request->form_id == "132") {
                $header->form_name  = "CGL - Form Purchase Order Non Ayam";
            }
            $header->jenis_ekspedisi    =   $request->jenis_ekspedisi;
            $header->user_id            = Auth::user()->id;
            $header->status             =   1;
            if (!$header->save()) {
                DB::rollBack();
                return back()->with('status', 2)->with('message', 'Proses gagal');
            }
            DB::commit();
            $return['status']   =   200;
            if ($request->key == 'buat_header') {
                $return['msg']      =   'Buat Header PO Berhasil';
            } else {
                $return['msg']      =   'Edit Header PO Berhasil';
            }
            return $return;
        }


        if ($request->key == 'batal_header') {
            $header                 =   Pembelianheader::where(function ($query) use ($request) {
                if ($request->id) {
                    $query->where('id', $request->id);
                } else {
                    $query->where('status', 1)->where('user_id', Auth::user()->id);
                }
            })
                ->where('subsidiary', 'like', '%' . Session::get('subsidiary') . '%')
                ->first();

            foreach (Pembelianlist::where('headbeli_id', $header->id)->get() as $row) {
                $list       =   Pembelianlist::where('id', $row->parent)->first();
                if ($list) {
                    $list->sisa =   $list->sisa + $row->qty;
                    $list->save();
                }
                $row->delete();
            }

            $header->delete();
            $return['status']   =   200;
            $return['msg']      =   'Batal buat PO berhasil';
            return $return;
        }

        if ($request->key == 'submit_pembelian') {
            if (!$request->supplier) {
                $return['status']   =   400;
                $return['msg']      =   'Supplier belum dipilih';
                return $return;
            }

            if (!$request->tanggal) {
                $return['status']   =   400;
                $return['msg']      =   'Tanggal belum dipilih';
                return $return;
            }

            $header                 =   Pembelianheader::where('status', 1)->where('user_id', Auth::user()->id)->where('subsidiary', 'like', '%' . Session::get('subsidiary') . '%')->first();
            $header->supplier_id    =   $request->supplier;
            $header->vendor_name    =   $request->vendor_name;
            $header->tanggal        =   $request->tanggal;
            $header->tanggal_kirim  =   $request->tanggal_kirim;
            $header->type_po        =   $request->type_po;
            $header->memo           =   $request->keterangan;
            $header->form_id        =   $request->form_id;
            $header->franco_loco    =   $request->franko_loko;
            $header->link_url       =   $request->link_url;
            $header->subsidiary     =   Session::get('subsidiary');
            if ($request->form_id == "156") {
                $header->form_name  = "EBA - Form Purchase Order Ayam";
            } else if ($request->form_id == "157") {
                $header->form_name  = "EBA - Form Purchase Order Non Ayam";
            } else if ($request->form_id == "131") {
                $header->form_name  = "CGL - Form Purchase Order Ayam";
            } else if ($request->form_id == "132") {
                $header->form_name  = "CGL - Form Purchase Order Non Ayam";
            }
            $header->jenis_ekspedisi    =   $request->jenis_ekspedisi;
            $header->status             =   $request->tipe == 'simpan' ? 2 : 9;
            $header->save();

            // if ($header->status == 9) {
            if ($request->ongkir > 0) {
                $list                  =   PembelianList::where('item_id', Item::item_sku($request->ongkir_sku)->id)->where('headbeli_id', $header->id)->first() ?? new Pembelianlist;
                $list->parent          =   NULL;
                $list->headbeli_id     =   $header->id;
                $list->pembelian_id    =   NULL;
                $list->item_id         =   Item::item_sku($request->ongkir_sku)->id;
                $list->qty             =   1;
                $list->gudang          =   Session::get('subsidiary') == 'CGL' ? '45' : '65';
                $list->harga           =   str_replace(".", "", $request->ongkir);
                $list->status          =   1;
                $list->save();
            }
            // }

            if ($header->status == 2) {
                if ($header) {

                    $netsuite = Netsuite::where('tabel_id', $header->id)->where('subsidiary', Session::get('subsidiary'))->where('record_type', 'purchase_order')->first();
                    if ($netsuite) {

                        $ns = Netsuite::update_purchase_order("pembelianheader", $header->id, $header->app_po, NULL, $header->tanggal);
                        $return['status']   =   200;
                        $return['msg']      =   'Purchase NS Diupdate';
                        return $return;
                    } else {

                        $ns = Netsuite::purchase_order("pembelianheader", $header->id, $header->app_po, NULL, $header->tanggal);

                        if ($ns) {

                            $header->netsuite_status    =   1;
                            $header->netsuite_id        =   $ns->id;
                            $header->status             =   3;

                            $header->save();

                            $return['status']   =   200;
                            $return['msg']      =   'Purchase berhasil';
                            return $return;
                        } else {
                            $return['status']   =   400;
                            $return['msg']      =   'Integrasi Gagal';
                            return $return;
                        }
                    }
                } else {
                    $return['status']   =   400;
                    $return['msg']      =   'Integrasi Gagal';
                    return $return;
                }
            }

            $return['status']   =   200;
            $return['msg']      =   'Purchase berhasil';
            return $return;
        }

        if ($request->key == 'proses_netsuite') {

            $header                 =   Pembelianheader::where('id', $request->id)->first();

            if ($header) {

                $netsuite = Netsuite::where('tabel_id', $header->id)->where('subsidiary', Session::get('subsidiary'))->where('record_type', 'purchase_order')->first();
                if ($netsuite) {

                    $ns = Netsuite::update_purchase_order("pembelianheader", $header->id, $header->app_po, NULL, $header->tanggal);
                    return back()->with('status', 1)->with('message', 'Netsuite Diupdate');
                } else {

                    $ns = Netsuite::purchase_order("pembelianheader", $header->id, $header->app_po, NULL, $header->tanggal);

                    if ($ns) {

                        $header->netsuite_status    =   1;
                        $header->netsuite_id        =   $ns->id;
                        $header->status             =   3;

                        $header->save();

                        return back()->with('status', 1)->with('message', 'Netsuite Terbentuk');
                    } else {
                        return back()->with('status', 2)->with('message', 'Proses integrasi gagal');
                    }
                }
            } else {
                return back()->with('status', 2)->with('message', 'Proses integrasi gagal');
            }
        }
    }

    public function destroy(Request $request)
    {
        $delete = Pembelianlist::where('id', $request->id)->delete();
        if (!$delete) {
            return response()->json([
                'msg' => 'Gagal hapus',
                'status' => 'error'
            ]);
        } else {
            return response()->json([
                'msg' => 'Berhasil hapus',
                'status' => 'success'
            ]);
        }
    }

    public function updateItem(Request $request)
    {

        $data           = Item::find($request->id);
        $data->type     = $request->unit;
        $data->status   = $request->status;
        $data->save();

        return back()->with('status', 1)->with('message', 'Update sukses');
    }

    public function inject()
    {
        $pr = Pembelian::join('pembelianheader', 'pembelianheader.pembelian_id', '=', 'pembelian.id')
            ->where('pembelian.subsidiary', 'like', '%' . Session::get('subsidiary') . '%')
            ->whereNotNull('document_number')
            ->get();

        return $pr;
    }
}
