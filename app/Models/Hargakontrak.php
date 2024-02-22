<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hargakontrak extends Model
{
    use SoftDeletes;
    protected $table = 'customer_hargakontrak';

    public function konsumen()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id') ;
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id') ;
    }
}
