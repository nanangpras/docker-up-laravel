<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product_retur extends Model
{
    //

    protected $table = 'product_retur';

    public function productreturorder()
    {
        return $this->belongsTo(Order::class,'order_id', 'id')->withTrashed();
    }

}
