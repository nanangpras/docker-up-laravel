<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\MarketingSO;
use Illuminate\Http\Request;

class MarketingSOController extends Controller
{
    //
    public function getDataMarketingSO(Request $request){
        $subsidiary = $request->subsidiary ?? 'CGL';
        $tanggal    = $request->tanggal ?? date('Y-m-d');
        
        $data = MarketingSO::whereDate('updated_at', $tanggal)
                            ->where('subsidiary', $subsidiary)
                            ->with('listItem')
                            ->get();
        
        return $data;
    }
}
