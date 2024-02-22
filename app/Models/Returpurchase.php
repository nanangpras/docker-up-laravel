<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Returpurchase extends Model
{
    use SoftDeletes ;
    protected $table = 'retur_purchase';

    public function purchase()
    {
        return $this->belongsTo(Purchasing::class, 'purchasing_id', 'id') ;
    }

    public function purchase_item()
    {
        return $this->belongsTo(PurchaseItem::class, 'purchaseitem_id', 'id') ;
    }

    public function get_alasan()
    {
        return $this->belongsTo(Returalasan::class, 'alasan', 'id') ;
    }
}
