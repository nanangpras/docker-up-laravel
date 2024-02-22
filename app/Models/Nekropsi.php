<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Nekropsi extends Model
{
    //
    protected $table    =   'qc_nekropsi';

    public static function nosurat()
    {
        $bulan  =   date('m');

        $count  =   Nekropsi::whereMonth('created_at', $bulan)->count('nomor_surat');
        $urut   =   Nekropsi::select(DB::raw('max(SUBSTR(nomor_surat,1,3)) as max'))->whereMonth('created_at', $bulan)->first();

        if ($bulan == 1) {
            $romawi =   'I';
        } elseif ($bulan == 2) {
            $romawi = 'II';
        } elseif ($bulan == 3) {
            $romawi = 'III';
        } elseif ($bulan == 4) {
            $romawi = 'IV';
        } elseif ($bulan == 5) {
            $romawi = 'V';
        } elseif ($bulan == 6) {
            $romawi = 'VI';
        } elseif ($bulan == 7) {
            $romawi = 'VII';
        } elseif ($bulan == 8) {
            $romawi = 'VIII';
        } elseif ($bulan == 9) {
            $romawi = 'IX';
        } elseif ($bulan == 10) {
            $romawi = 'X';
        } elseif ($bulan == 11) {
            $romawi = 'XI';
        } else {
            $romawi = 'XII';
        }

        if ($urut->max == null) {
            $nomor  =   sprintf("%03s", ($count+1)) . '/CGL/BAN/QC/' . $romawi . '/' . date('Y');
        } else {
            $nomor  =    sprintf("%03s", ($urut->max +1)) . '/CGL/BAN/QC/' . $romawi . '/' . date('Y');
        }

        return $nomor;
    }
}
