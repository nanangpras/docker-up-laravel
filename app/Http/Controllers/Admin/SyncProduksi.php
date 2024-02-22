<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Freestock;
use Illuminate\Http\Request;

class SyncProduksi extends Controller
{
    public function index(Request $request)
    {
        if ($request->key == 'view') {
            $mulai      =   $request->mulai ?? date('Y-m-d') ;
            $akhir      =   $request->akhir ?? date('Y-m-d') ;
            $produksi   =   Freestock::where(function($query) use ($request, $mulai, $akhir) {
                                if ($request->paket) {
                                    $query->where('netsuite_id', $request->paket);
                                } else {
                                    $query->whereBetween('tanggal', [$mulai, $akhir]);
                                }
                            })
                            ->where('status', 3)
                            ->whereNotIn('regu', ['frozen', 'frozen_ppic'])
                            ->orderBy('id', 'ASC')
                            ->paginate(10) ;

            return view('admin.pages.laporan.sync_produksi.data', compact('produksi'));
        } else {
            return view('admin.pages.laporan.sync_produksi.index', compact('request'));
        }

    }
}
