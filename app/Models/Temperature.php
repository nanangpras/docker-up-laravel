<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Temperature extends Model
{
    //
    use SoftDeletes;
    protected $table = 'tb_suhu';
    protected $fillable = ['timestamp','suhu','lembab','lokasi','nama_sensor','kode_perangkat'];
    

}
