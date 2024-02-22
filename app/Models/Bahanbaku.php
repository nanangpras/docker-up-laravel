<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Chiller;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bahanbaku extends Model
{
    //
    protected $table = 'order_bahan_baku';
    use SoftDeletes;
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    public function chiler_data()
    {
        return $this->belongsTo(Chiller::class, 'chiller_id', 'id');
    }

    public function to_chiller()
    {
        return $this->belongsTo(Chiller::class, 'chiller_out', 'id');
    }
    public function to_product_gudang()
    {
        return $this->belongsTo(Product_gudang::class, 'chiller_out', 'id');
    }
    public function chiller_out()
    {
        return $this->hasOne(Chiller::class, 'chiller_out', 'id');
    }

    public function orderitem()
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id', 'id');
    }

    public static function total_keranjang($order_item_id)
    {
        $krj = Bahanbaku::where('order_item_id', $order_item_id)->sum('keranjang');

        return $krj ?? 0;
    }

    public function bahanbborder()
    {
        return $this->belongsTo(Order::class,'order_id', 'id');
    }

    public function relasi_chiller_out()
    {
        return $this->hasOne(Chiller::class, 'id', 'chiller_out');
    }
    public function relasi_gudang_out()
    {
        return $this->hasOne(Product_gudang::class,'id', 'chiller_out');
    }
    public function relasi_netsuite()
    {
        return $this->hasOne(Netsuite::class,'id', 'netsuite_id')->where('record_type', 'transfer_inventory');
    }
    public function relasi_netsuite_one()
    {
        return $this->hasOne(Netsuite::class,'id', 'netsuite_id')->where('record_type', 'transfer_inventory');
    }

}
