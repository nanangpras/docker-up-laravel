<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Order;
use App\Models\Ekspedisi;

class Ekspedisi_rute extends Model
{
    //

    protected $table = 'ekspedisi_rute';

    public function ruteekspesidi()
    {
        return $this->belongsTo(Ekspedisi::class, 'ekspedisi_id', 'id')->withTrashed();
    }

    public function ruteorder()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function ruteorderitem()
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id', 'id');
    }

    public function bahanbaku()
    {
        return $this->belongsTo(Bahanbaku::class, 'no_do', 'no_do') ;
    }

    public function order_so()
    {
        return $this->belongsTo(Order::class, 'no_so', 'no_so');
    }

    public function marketing_so() 
    {
        return $this->belongsTo(MarketingSO::class, 'no_so', 'no_so');
        
    }

    public function listbb()
    {
        return $this->hasMany(Bahanbaku::class, 'no_do', 'no_do') ;
    }

    public function returorderitem()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id')->where('retur_tujuan', null);
    }

    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class, 'wilayah_id', 'id');
    }
}
