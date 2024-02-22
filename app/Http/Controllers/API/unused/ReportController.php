<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Chiller;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    //
    public function getChillerData(Request $request){
        $mulai  = $request->mulai ?? date('Y-m-d');
        $sampai  = $request->sampai ?? date('Y-m-d');
        $data = Chiller::whereBetween('tanggal_produksi', [$mulai, $sampai])->get()->makeHidden(['tujuan', 'status_chiler', 'request_pending', 'status_free']);

        return $data;
    }
}
