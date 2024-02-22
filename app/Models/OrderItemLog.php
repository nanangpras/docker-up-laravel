<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Order;
use App\Models\Item;
use App\Models\Bahanbaku;

class OrderItemLog extends Model
{
    //
    protected $table = 'order_item_log';

    public function item()
    {
        return $this->belongsTo(Item::class, 'order_item_id', 'id');
    }

}
