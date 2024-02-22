<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LaporanRendemen extends Model
{
    protected $table = 'laporan_rendemen';

    public static function report_rendem($tanggal)
    {
        return LaporanRendemen::select(
            DB::raw("ROUND((SUM(rendemen_total) / SUM(IF(rendemen_total,1,0))), 2) AS rendemen_total"),
            DB::raw("ROUND((SUM(rendemen_tangkap) / SUM(IF(rendemen_total,1,0))), 2) AS rendemen_tangkap"),
            DB::raw("ROUND((SUM(rendemen_kirim) / SUM(IF(rendemen_total,1,0))), 2) AS rendemen_kirim"),
            DB::raw("ROUND((SUM(berat_rpa)), 2) AS berat_rpa"),
            DB::raw("ROUND((SUM(berat_grading)), 2) AS berat_grading"),
            DB::raw("ROUND((SUM(berat_evis)), 2) AS berat_evis"),
            DB::raw("SUM(darah_bulu) AS darah_bulu"),
            DB::raw("SUM(ekor_rpa) AS ekor_rpa"),
            DB::raw("SUM(ekor_grading) AS ekor_grading"),
            DB::raw("SUM(selisih_ekor) AS selisih_ekor"),
            DB::raw("SUM(jumlah_supplier) AS jumlah_supplier"),
            DB::raw("SUM(jumlah_po_mobil) AS jumlah_po_mobil"),
            DB::raw("SUM(selesai_potong) AS selesai_potong"),
            DB::raw("SUM(ekor_do) AS ekor_do"),
            DB::raw("SUM(berat_do) AS berat_do"),
            DB::raw("ROUND((SUM(rerata_do) / SUM(IF(rendemen_total,1,0))), 2) AS rerata_do"),
            DB::raw("SUM(ekoran_seckel) AS ekoran_seckel"),
            DB::raw("SUM(kg_terima) AS kg_terima"),
            DB::raw("ROUND((SUM(rerata_terima_lb) / SUM(IF(rendemen_total,1,0))), 2) AS rerata_terima_lb"),
            DB::raw("SUM(susut_tangkap) AS susut_tangkap"),
            DB::raw("SUM(susut_kirim) AS susut_kirim"),
            DB::raw("SUM(susut_seckel) AS susut_seckel"),
            DB::raw("SUM(ekoran_grading) AS ekoran_grading"),
            DB::raw("SUM(berat_grading) AS berat_grading"),
            DB::raw("SUM(selisih_seckel_grading) AS selisih_seckel_grading"),
            DB::raw("ROUND((SUM(rerata_grading) / SUM(IF(rendemen_total,1,0))), 2) AS rerata_grading"),
        )
        ->where('subsidiary_id', 2)
        ->whereBetween('tanggal', [Carbon::parse($tanggal)->subDay(6)->format('Y-m-d'), $tanggal])
        ->first() ;
    }
}
