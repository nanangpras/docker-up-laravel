<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Sales_invoice;
use App\Models\Order;

class Sales_invoice_item extends Model
{
    //
    protected $table = 'sales_invoice_item';

    public function salesitemsales()
    {
        return $this->belongsTo(Sales_invoice::class, 'sales_invoice_id', 'id')->withTrashed();
    }

    public function salesitemorder()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id')->withTrashed();
    }
}
