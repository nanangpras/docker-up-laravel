<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;

class User extends Model
{
    public function usercompany()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id')->withTrashed();
    }

    public static function setIjin($param)
    {
        $permission = false;

        if($param=='superadmin'){
            if (Auth::user()->account_role == 'superadmin') {
                $permission = TRUE;
            }else{
                $permission = FALSE;
            }
        }else{
            if (Auth::user()->account_role == 'superadmin') {
                $permission = TRUE;
            } else {
                foreach (explode(',', Auth::user()->group_role) as $item) {
                    if ($item == $param) $permission = TRUE;
                }
            }
        }
        return $permission;
    }

    public static function getDataFromUser($id,$data){
        $query          = User::select($data)->where('id',$id)->get();
        if($query->count() > 0){
            foreach($query as $value){
                $hasil  = $value->$data;
            }
        }else{
            $hasil      = '';
        }

        return $hasil;
    }
}
