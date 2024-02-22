<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Customer;
use App\Models\Sales_invoice_item;

class Sales_invoice extends Model
{
    //
    protected $table = 'sales_invoice';

    public function salescustomer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id')->withTrashed();
    }

    public function salesitem()
    {
        return $this->hasMany(Sales_invoice_item::class, 'id', 'sales_invoice_id')->withTrashed();
    }
}
