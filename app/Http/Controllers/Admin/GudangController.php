<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gudang;
use App\Models\User;
use Illuminate\Http\Request;

class GudangController extends Controller
{
    public function index(Request $request)
    {
        $kategori = Gudang::select('kategori')->groupBy('kategori')->where('kategori','!=',NULL)->get();
        // dd($kategori);
        if (User::setIjin(44)) {
            if ($request->key == 'view') {
                $data   =   Gudang::where('subsidiary', env('NET_SUBSIDIARY', 'CGL'))
                            ->where(function($query) use ($request) {
                                if ($request->cari) {
                                    $query->orWhere('netsuite_internal_id', 'like', '%' . $request->cari . '%') ;
                                    $query->orWhere('code', 'like', '%' . $request->cari . '%') ;
                                }
                                if ($request->status) {
                                    $query->where('status', $request->status);
                                }
                            })
                            ->paginate(20) ;

                return view('admin.pages.gudang.data', compact('data'));
            } else {
                return view('admin.pages.gudang.index',compact('kategori'));
            }
        }
        return redirect()->route("index");
    }

    public function show(Request $request)
    {
        $data = Gudang::find($request->id);
        if ($request->key == 'detail') {
            return view('admin.pages.gudang.modal.detail', compact('data'));
        }
        if ($request->key == 'edit') {
            return view('admin.pages.gudang.modal.edit',compact('data'));
        }
        if ($request->key == 'delete') {
            return view('admin.pages.gudang.modal.delete',compact('data'));
        }

    }

    public function store(Request $request)
    {
        // dd($request->all());
        $new_gudang = new Gudang();
        $new_gudang->code       = $request->nama_gudang;
        $new_gudang->kategori   = $request->kategori;
        $new_gudang->subsidiary = $request->subsidiary;
        if ($request->subsidiary == 'EBA') {
            $new_gudang->subsidiary_id = 5;
        }
        if ($request->subsidiary == 'CGL') {
            $new_gudang->subsidiary_id = 2;
        }
        if ($request->subsidiary == 'MPP') {
            $new_gudang->subsidiary_id = 1;
        }
        $new_gudang->status     = $request->status;
        $new_gudang->netsuite_internal_id       = $request->netsuite_id ?? NULL;
        $new_gudang->save();
        return back()->with('status', 1)->with('message', 'Gudang berhasil ditambahkan');
    }

    public function update ($id,Request $request)
    {
        $update                       = Gudang::find($id);
        $update->status               = $request->status;
        $update->code                 = $request->nama_gudang;
        $update->netsuite_internal_id = $request->netid;
        $update->subsidiary           = $request->subsidiary;
        $update->save();
        return back()->with('status', 1)->with('message', 'Gudang berhasil diperbarui');

    }

    public function delete ($id,Request $request)
    {
        $delete = Gudang::find($id);
        if ($delete->netsuite_internal_id != NULL) {
            return back()->with('status', 2)->with('message', 'Tidak dapat dihapus karena ada netsuite internal ID');
        }else {
            $delete->delete();
            return back()->with('status', 1)->with('message', 'Gudang berhasil dihapus');
        }
    }
}
