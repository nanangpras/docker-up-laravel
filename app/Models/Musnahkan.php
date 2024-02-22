<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Musnahkan extends Model
{
    use SoftDeletes;
    protected $table    =   'musnahkan';

    public function list_data()
    {
        return $this->hasMany(Musnahkantemp::class, 'musnahkan_id', 'id');
    }

    public function netsuite()
    {
        return $this->hasMany(Netsuite::class, 'tabel_id', 'id')->where('tabel', 'musnahkan');
    }
}
