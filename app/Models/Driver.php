<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Ekspedisi;
use App\Models\Bonus_driver;

class Driver extends Model
{
    //

    protected $table    =   'driver';
    protected $appends  =   ['status_ekspedisi', 'pickup', 'summary_route', 'summary_ekor', 'summary_berat', 'jumlah_pengiriman'];

    public function getJumlahPengirimanAttribute()
    {
        $ekspedisi  =   Ekspedisi::select('id')
                        ->where('driver_id', $this->id)
                        ->where('kembali', NULL)
                        ->whereIn('status', [1,2])
                        ->first();

        return $ekspedisi ? COUNT($ekspedisi->ekspedisirute) : "";
    }

    public static function expedisi_no_polisi($id)
    {
        $data   =   Ekspedisi::select('no_polisi')
                    ->whereIn('status', [1,2])
                    ->where('kembali', NULL)
                    ->where('driver_id', $id)
                    ->first();

        return $data->no_polisi ?? "" ;
    }

    public function getSummaryRouteAttribute()
    {
        $data   =   Ekspedisi::select('wilayah_id')
                    ->whereIn('status', [1,2])
                    ->where('kembali', NULL)
                    ->where('driver_id', $this->id)
                    ->first();

        return $data->wilayah->nama ?? '###' ;
    }

    public function getSummaryBeratAttribute()
    {
        $data   =   Ekspedisi::select('berat')
                    ->whereIn('status', [1,2])
                    ->where('kembali', NULL)
                    ->where('driver_id', $this->id)
                    ->first();

        return $data->berat ?? '###';
    }

    public function getSummaryEkorAttribute()
    {
        $data   =   Ekspedisi::select('qty')
                    ->whereIn('status', [1,2])
                    ->where('kembali', NULL)
                    ->where('driver_id', $this->id)
                    ->first();

        return $data->qty ?? '###' ;
    }

    public function getStatusEkspedisiAttribute()
    {
        $ekspedisi  =   Ekspedisi::where('driver_id', $this->id)->first();

        if ($ekspedisi) {
            if ($ekspedisi->kembali == NULL) {
                if ($ekspedisi->status == 1) {
                    return "<span class='status status-warning'>Loading</span>";
                } else {
                    return "<span class='status status-primary'>Ready</span>";
                }
            } else {
                return "<span class='status status-success'>Active</span>";
            }
        } else {
            return "<span class='status status-success'>Active</span>";
        }
    }

    public function getPickupAttribute()
    {
        $status =   FALSE ;
        foreach (Ekspedisi::where('driver_id', $this->id)->get() as $row) {
            if ($row->status == 2) $status =    TRUE;
        }
        return $status ;
    }

    public static function Ekspe($id)
    {
        return Ekspedisi_rute::whereIn('ekspedisi_id', Ekspedisi::select('id')->where('driver_id', $id)->where('kembali', null)->where('status', 1))->count();
    }

    public function pengiriman()
    {
        return $this->belongsTo(Ekspedisi::class, 'id', 'driver_id')->where('kembali', NULL) ;
    }

    public function driverekspedisi()
    {
        return $this->hasMany(Ekspedisi::class, 'id', 'driver_id')->withTrashed();
    }

    public function driverbonus()
    {
        return $this->hasMany(Bonus_driver::class, 'id', 'driver_id')->withTrashed();
    }

    public static function hitungKerja($id, $tipe){
        if($tipe == 'tangkap'){
           return Driver::leftJoin('productions', 'productions.sc_pengemudi_id','=' ,'driver.id')
            ->where('driver.id', $id)
            ->groupBy('driver.nama')
            ->count('productions.id');

            // return $data->countambil ?? "0" ;
        }

        if($tipe == 'kirim'){
           return Driver::leftJoin('ekspedisi', 'ekspedisi.driver_id','=' ,'driver.id')
            ->where('deleted_at' ,'=', NULL)
            ->where('driver.id', $id)
            ->where('ekspedisi.status', '>=', 2)
            ->groupBy('driver.nama')
            ->count('ekspedisi.id');

            // return $data->countantar ?? "0";
        }
    }
}
