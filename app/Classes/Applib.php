<?php

namespace App\Classes;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Applib
{

    public static function DefaultTanggalAudit()
    {
        return "2023-01-01";
    }

    public static function DefaultTanggalAuditPlusTujuh()
    {
        return "2022-12-25";
    }
   
    public static function BatasMinimalTanggal()
    {
        if(env('NET_SUBSIDIARY') == 'CGL'){
            $batas = "2023-05-27";
        }else{
            $batas = "2023-05-05";
        }
        return $batas;
    }

    public static function getIdOption($table,$id,$type){
        $query                     = DB::table($table)->where('slug',$id)->get();
        // dd($query->count());
        if ($query->count() > 0) {
            foreach ($query as $h) {
                $hasil = $h->$type;
            }
        } else {
            $hasil = '';
        }
        return $hasil;
    }

    public static function getName($table,$id,$type){
        $query                     = DB::table($table)->where('id',$id)->get();
        // dd($query->count());
        if ($query->count() > 0) {
            foreach ($query as $h) {
                $hasil = $h->$type;
            }
        } else {
            $hasil = '';
        }
        return $hasil;
    }

    public static function paginate($items, $perPage = 10, $page = null, $options = [])
    {
        $options = [
            'path'      => LengthAwarePaginator::resolveCurrentPath(),
            'pageName'  => 'page'
        ];
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

}