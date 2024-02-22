<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pembelian extends Model
{
    use SoftDeletes ;
    protected $table    =   'pembelian';

    public function list_beli()
    {
        return $this->hasMany(Pembelianlist::class, 'pembelian_id', 'id')->where('headbeli_id', NULL)->withTrashed();
    }

    public function list_beliDownload()
    {
        return $this->hasMany(Pembelianlist::class, 'pembelian_id', 'id')->where('headbeli_id', NULL);
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function header()
    {
        return $this->belongsTo(Pembelianheader::class, 'id', 'pembelian_id') ;
    }

    public function pr_po()
    {
        return $this->hasMany(Pembelianheader::class, 'pembelian_id', 'id') ;
    }
}
