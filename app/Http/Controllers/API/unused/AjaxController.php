<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product_gudang;
use Illuminate\Http\Request;

class AjaxController extends Controller
{
    //

    public function warehouseStock(Request $request)
    {

        $mulai  = $request->mulai ?? date('Y-m-d');
        $sampai = $request->sampai ?? date('Y-m-d');
        $status = $request->status;

        $stock  = Product_gudang::where('jenis_trans', 'masuk');

        if ($status == "") {
            $stock = $stock->whereIn('status', [2]);
        } else {
            $stock = $stock->whereIn('status', [$status]);
        }

        if ($mulai == "" && $sampai == "") {
        } elseif ($mulai != "" && $sampai == "") {
            $stock = $stock->where('created_at', '>', $mulai . " 00:00:01");
        } elseif ($mulai == "" && $sampai != "") {
            $stock = $stock->where('created_at', '<', $sampai . " 23:59:59");
        } elseif ($mulai != "" && $sampai != "") {
            $stock = $stock->whereBetween('created_at', [$mulai . " 00:00:01", $sampai . " 23:59:59"]);
        }
        $stock          = $stock->get();

        $data = [];
        foreach($stock as $row):
            $ar[] = $row->id;
            $ar[] = $row->id;
            $ar[] = $row->id;
            $ar[] = $row->id;
            $ar[] = $row->id;
            $ar[] =  $row->id;
            $data[] = $ar;

            $ar = [];
        endforeach;

        $resp = array(
            'data' => $data
        );
        echo json_encode($resp);

    }

}
