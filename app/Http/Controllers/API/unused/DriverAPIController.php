<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Driver;

class DriverAPIController extends Controller
{
    public function store(Request $request)
    {
        $driver             =   new Driver;
        $driver->nama       =   $request->nama_supir;
        $driver->alamat     =   $request->alamat;
        $driver->telp       =   $request->nomor_telepon;
        $driver->kelurahan  =   $request->kelurahan;
        $driver->kecamatan  =   $request->kecamatan;
        $driver->kota       =   $request->kota;
        $driver->provinsi   =   $request->provinsi;
        $driver->kode_pos   =   $request->kode_pos;
        $driver->save();

        $response['meta']["status"]     =   200;
        $response['meta']["message"]    =   "OK";
        $response["response"] =
        [
            "nama_supir"    =>  $request->nama_supir,
            "alamat"        =>  $request->alamat,
            "nomor_telepon" =>  $request->nomor_telepon,
            "kelurahan"     =>  $request->kelurahan,
            "kecamatan"     =>  $request->kecamatan,
            "kota"          =>  $request->kota,
            "provinsi"      =>  $request->provinsi,
            "kode_pos"      =>  $request->kode_pos,
        ];

        return response()->json($response, $response['meta']["status"]);
    }
}
