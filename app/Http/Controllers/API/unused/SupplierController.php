<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function store(Request $request)
    {
        $post               =   new Supplier;
        $post->kode         =   $request->internal_id ;
        $post->nama         =   $request->nama_vendor ;
        $post->alamat       =   $request->alamat ;
        $post->telp         =   $request->no_telp ;
        $post->kategori     =   $request->jenis_ekspedisi ;
        $post->peruntukan   =   $request->subsidiary ;
        $post->wilayah      =   $request->wilayah_vendor ;
        $post->save();

        $response['meta']["status"]     =   200;
        $response['meta']["message"]    =   "OK";
        $response["response"] =
        [
            "nama_vendor"   =>  $request->nama_vendor,
            "alamat"        =>  $request->alamat,
            "nomor_telepon" =>  $request->no_telp,
            "kode"          =>  $request->internal_id,
            "kategori"      =>  $request->jenis_ekspedisi,
            "peruntukan"    =>  $request->subsidiary,
            "wilayah"       =>  $request->wilayah_vendor,
        ];

        return response()->json($response, $response['meta']["status"]);
    }
}
