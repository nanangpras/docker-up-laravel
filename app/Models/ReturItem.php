<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReturItem extends Model
{
    //
    use SoftDeletes;
    
    protected $table = 'retur_item';
    protected $appends  =   ['tujuan_retur'];
    public function to_item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    public function to_retur()
    {
        return $this->belongsTo(Retur::class, 'retur_id', 'id');
    }

    public function getTujuanReturAttribute()
    {
        if ($this->unit == 'gudang') {
            return 'Gudang';
        }elseif ($this->unit == 'chiller') {
            return 'Chiller';
        }elseif ($this->unit == 'musnahkan') {
            return 'Musnahkan';
        } elseif ($this->unit == 'frozen') {
            return 'Frozen';
        }
    }

    public function todriver()
    {
        return $this->belongsTo(Driver::class, 'driver', 'id');
    }

    public function orderitem() {
        return $this->belongsTo(OrderItem::class, 'orderitem_id', 'id');
    }
}
