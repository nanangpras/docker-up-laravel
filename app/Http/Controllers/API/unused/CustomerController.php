<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function store(Request $request)
    {
        $konsumen                   =   new Customer;
        $konsumen->kode             =   $request->kode;
        $konsumen->nama             =   $request->nama;
        $konsumen->nama_marketing   =   $request->nama_marketing;
        $konsumen->kategori         =   $request->kategori;
        $konsumen->type_customer    =   $request->jenis_konsumen;
        $konsumen->save();

        $response['meta']["status"]     =   200;
        $response['meta']["message"]    =   "OK";
        $response["response"] =
        [
            "kode"              =>  $request->kode,
            "nama"              =>  $request->nama,
            "nama_marketing"    =>  $request->nama_marketing,
            "kategori"          =>  $request->kategori,
            "jenis_konsumen"    =>  $request->jenis_konsumen,
        ];

        return response()->json($response, $response['meta']["status"]);
    }
}
