<?php

namespace App\Models;

use App\Classes\Applib;
use Illuminate\Database\Eloquent\Model;
use App\Models\Company;
use App\Models\Product_gudang;

class Gudang extends Model
{
    //
    protected $table = 'gudang';

    public function gudangcompany()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id')->withTrashed();
    }

    public function gudangproduct()
    {
        return $this->hasMany(Product_gudang::class, 'id', 'gudang_id')->withTrashed();
    }

    public static function gudang_netid($key)
    {
        return Gudang::where('code', $key)->first()->netsuite_internal_id ;
    }

    public static function gudang_id($key)
    {
        return Gudang::where('code', $key)->first()->id ;
    }

    public static function gudang_code($code){
        return Gudang::where('netsuite_internal_id', $code)->first()->code ?? "###";
    }

    public static function namaGudangWithID($code) {
        return Gudang::where('id', $code)->first()->code ?? "###";
    }

    public static function stock_masuk($awal, $akhir, $gudang, $type, $sum)
    {
        return Product_gudang::where(function($query) use ($awal,$akhir) {
            if ($awal == $akhir) {
                $query->where('production_date', '<=', $awal) ;
            } else {
                $query->where('production_date', '<=', $akhir) ;
            }
            // $query->whereBetween('production_date', [$awal, $akhir]);
        })
        ->where('production_date', '>=', Applib::BatasMinimalTanggal())
        ->where('gudang_id', $gudang)
        ->where('jenis_trans', $type)
        ->sum($sum);
    }

    public static function kode_produksi($id)
    {
        $data       =   Product_gudang::find($id) ;
        $tanggal    =   $data->production_date ;

        $kode_awal  =   str_replace(
                            ['01','02','03','04','05','06','07','08','09','10','11','12','1','2','3','4','5','6','7','8','9'],
                            ['A','B','C','D','E','F','G','H','I','J','K','L', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'],
                            trim(date('m', strtotime($tanggal)))
                        );

        $akhir      =   strtotime("+$data->expired month", strtotime($tanggal)) ;

        $kode_akhir =   str_replace(
                            ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '1', '2', '3', '4', '5', '6', '7', '8', '9'],
                            ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'],
                            trim(date('m', $akhir))
                        );

        return $kode_awal . date("d", strtotime($tanggal)) . date('y', $akhir) . $kode_akhir ;
    }
}
