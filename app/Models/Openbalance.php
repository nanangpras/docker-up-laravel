<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Openbalance extends Model
{
    use SoftDeletes;
    protected $table    =   'openbalance';

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

}
