<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Production;
use App\Models\Purchasing;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        if(User::setIjin(18)){

            if ($request->key == 'view') {
                $data   =   Supplier::select("netsuite_internal_id", "kode", "nama", "id", "deleted_at")
                            ->where(function($query) use ($request) {
                                if ($request->cari) {
                                    $query->orWhere('netsuite_internal_id', 'like', '%' . $request->cari . '%') ;
                                    $query->orWhere('nama', 'like', '%' . $request->cari . '%') ;
                                    $query->orWhere('kode', 'like', '%' . $request->cari . '%') ;
                                }
                            })
                            ->orderBy('nama', 'ASC')
                            ->withTrashed()
                            ->paginate(20);

                return view('admin.pages.supplier.show', compact('data'));

            } else if ($request->key == 'editSupplier') {
                $data   = Supplier::where('id', $request->id)->withTrashed()->first();
                return view('admin.pages.supplier.modalEditSupplier',compact('data'));

            } else if ($request->key == 'updateSupplier') {
                $data   = Supplier::where('id', $request->id)->withTrashed()->first();
                if ($data) {
                    if ($request->status == 1) {
                        $data->kode       = $request->kode;
                        $data->restore();
                        $return['status']   =   200;
                        $return['msg']      =   "Update berhasil";
                        return $return;
                    } else {
                        $data->delete();
                        $return['status']   =   200;
                        $return['msg']      =   "Update berhasil";
                        return $return;
                    }
                } else {
                    $return['status']   =   400;
                    $return['msg']      =   "Gagal update, supplier tidak ditemukan";
                    return $return;
                }



            } else {
                return view('admin.pages.supplier.index');
            }

        }
        return redirect()->route("index");
    }
    public function show(Request $request, $id)
    {
        if (User::setIjin(18)) {

            $data   =   Supplier::find($id) ;
            if ($data) {
                if ($request->key == 'ayam_hidup') {
                    $purchase   =   Purchasing::where('supplier_id', $data->id)
                                    ->where(function($query) use ($request) {
                                        if ($request->cari) {
                                            $query->where('no_po', 'like', '%' . $request->cari . '%') ;
                                        }
                                    })
                                    ->orderBy('tanggal_potong')
                                    ->paginate(15) ;

                    return view('admin.pages.supplier.detail.ayam_hidup', compact('data', 'purchase'));
                } else {
                    return view('admin.pages.supplier.detail.index', compact('data'));
                }

            }
            return redirect()->route("supplier.index");

        }
        return redirect()->route("index");
    }

    public function tukar(Request $request)
    {
        if ($request->key == 'view') {
            $diterima           =   Production::where('sc_status', '!=', NULL)
                                    ->whereIn('purchasing_id', Purchasing::select('id')
                                        ->whereIn('type_po', ['PO LB', 'PO Maklon'])
                                    )
                                    ->whereDate('prod_tanggal_potong', ($request->tanggal_supplier ?? date("Y-m-d")))
                                    ->orderBy('no_urut', 'ASC')
                                    ->get();

            return view('admin.pages.supplier.tukar.view', compact('diterima', 'request')) ;
        } else {
            return view('admin.pages.supplier.tukar.index') ;
        }
    }
}
