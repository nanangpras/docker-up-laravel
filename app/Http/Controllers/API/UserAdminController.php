<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserAdminController extends Controller
{
    //

    
    public function getDataUser(Request $request){

        $subsidiary = $request->subsidiary ?? 'CGL';
        
        if($request->tanggal == "all"){
            $data = User::get();
        }else{
            $tanggal = $request->tanggal ?? date('Y-m-d');
            $data = User::where('last_update', $tanggal)->where('subsidiary', $subsidiary)->get();
        }
        
        return $data;
    }
}
