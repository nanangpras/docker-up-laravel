<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Production;

class Lpah extends Model
{
    //
    protected $table    =   'lpah';

    public function lpahprod()
    {
        return $this->belongsTo(Production::class, 'production_id', 'id')->withTrashed();
    }

    public static function prosentase($id)
    {
        $production =   Production::find($id) ;
        $grading    =   Grading::where('trans_id', $production->id)->sum('berat_item');
        $evis       =   Evis::where('production_id', $production->id)->sum('berat_item');

        if($production->po_jenis_ekspedisi=="tangkap"){
            $row    =   "<span class='status status-info'>" . number_format((((($production->lpah_berat_terima-$production->qc_berat_ayam_mati) - ($grading + $evis)) / ($production->lpah_berat_terima-$production->qc_berat_ayam_mati)) * 100), 2) . ' %</span> || ' ;
            $row    .=  "<span class='status status-success'>" . number_format(($production->lpah_berat_terima-$production->qc_berat_ayam_mati) - ($grading + $evis), 2) . ' KG</span>' ;
        }else{
            $row    =   "<span class='status status-info'>" . number_format((((($production->lpah_berat_terima) - ($grading + $evis)) / ($production->lpah_berat_terima)) * 100), 2) . ' %</span> || ' ;
            $row    .=  "<span class='status status-success'>" . number_format(($production->lpah_berat_terima) - ($grading + $evis), 2) . ' KG</span>' ;
        }

        return $row ;
    }
}
