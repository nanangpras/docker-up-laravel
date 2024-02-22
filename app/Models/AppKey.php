<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppKey extends Model
{
    //
    public static function generate(){
        $app_code = env('APP_CODE','cgl');
        return $app_code.'-'.base_convert(sha1(uniqid(mt_rand())), 16, 36).'-'.time();
    }
}
