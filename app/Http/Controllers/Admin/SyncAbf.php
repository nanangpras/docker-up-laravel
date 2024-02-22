<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Abf;
use Illuminate\Http\Request;

class SyncAbf extends Controller
{
    public function index(Request $request)
    {
        if ($request->key == 'view') {
            $mulai      =   $request->mulai ?? date('Y-m-d');
            $akhir      =   $request->akhir ?? date('Y-m-d');
            $abf    =   Abf::whereBetween('tanggal_masuk', [$mulai, $akhir])
                        ->paginate(15);

            return view('admin.pages.laporan.sync_abf.data', compact('abf'));
        } else {
            return view('admin.pages.laporan.sync_abf.index');
        }
    }
}
