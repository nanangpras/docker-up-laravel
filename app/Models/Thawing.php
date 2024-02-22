<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Thawing extends Model
{
    use SoftDeletes ;
    protected $table = 'thawing_request';

    public function thawing_list()
    {
        return $this->hasMany(Thawinglist::class, 'thawing_id', 'id');
    }
    
    public function thawing_listUpdate()
    {
        return $this->hasMany(Thawinglist::class, 'thawing_id', 'id')->where('status', NULL);
    }
}
