<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Retur;
use App\Models\ReturItem;
use App\Models\Returpurchase;
use Illuminate\Http\Request;

class LaporanAccounting extends Controller
{
    public function index(Request $request)
    {
        if ($request->key == 'view') {
            $retur_qc   =   ReturItem::whereIn('retur_id', Retur::select('id')->whereBetween('tanggal_retur', [($request->tanggal_awal ?? date("Y-m-d")), ($request->tanggal_akhir ?? date("Y-m-d"))]))
                            ->paginate(10) ;

            return view('admin.pages.laporan.accounting.data', compact('retur_qc'));
        } else

        if ($request->key == 'purchase') {
            $retur_po   =   Returpurchase::whereBetween('tanggal', [($request->tanggal_awal ?? date("Y-m-d")), ($request->tanggal_akhir ?? date("Y-m-d"))])
                            ->paginate(10) ;

            return view('admin.pages.laporan.accounting.purchasing', compact('retur_po'));
        } else {
            return view('admin.pages.laporan.accounting.index');
        }
    }
}
