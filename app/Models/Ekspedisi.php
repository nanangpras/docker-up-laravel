<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Ekspedisi_rute;
use App\Models\Driver;
use App\Models\Wilayah;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Ekspedisi extends Model
{
    use SoftDeletes ;
    protected $table    =   'ekspedisi';
    protected $appends  =   ['nomor_do'];

    public function getNomorDoAttribute()
    {
        return "CGL.RK." . date('Y.m.', strtotime($this->tanggal)) . str_pad((string)$this->no_urut, 4, "0", STR_PAD_LEFT);
    }

    public function ekspedisirute()
    {
        return $this->hasMany(Ekspedisi_rute::class, 'ekspedisi_id', 'id');
    }

    public function eksrute()
    {
        return $this->hasMany(Ekspedisi_rute::class, 'ekspedisi_id', 'id')->select('id','no_so', DB::raw("SUM(berat) AS berat"), DB::raw("SUM(qty) AS qty"), 'status')->groupBy('no_so');
    }

    public function groupByRute()
    {
        return $this->hasMany(Ekspedisi_rute::class, 'ekspedisi_id', 'id')->select('id','no_so', DB::raw("SUM(berat) AS berat"), DB::raw("SUM(qty) AS qty"), 'status')->groupBy('no_do','no_so');
    }

    public function ekspedisidriver()
    {
        return $this->belongsTo(Driver::class, 'driver_id', 'id');
    }

    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class, 'wilayah_id', 'id');
    }

    public function nopol()
    {
        return $this->belongsTo(Nopolisi::class, 'no_polisi', 'id') ;
    }

    public static function nomor_do($tanggal)
    {
        $nomor  =   Ekspedisi::select('no_urut')
                    ->whereDate('tanggal', $tanggal)
                    ->orderBy('no_urut', 'DESC')
                    ->limit(1)
                    ->first();

        return $nomor ? $nomor->no_urut + 1 : 1 ;
    }

}
