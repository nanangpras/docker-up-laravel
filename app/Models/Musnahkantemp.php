<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Musnahkantemp extends Model
{
    protected $table    =   'musnahkan_temp';

    public function gudang()
    {
        return $this->belongsTo(Product_gudang::class, 'item_id', 'id');
    }
    public function musnahkan()
    {
        return $this->belongsTo(Musnahkan::class, 'musnahkan_id', 'id');
    }

    public function chiller()
    {
        return $this->belongsTo(Chiller::class, 'item_id', 'id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Gudang::class, 'gudang_id', 'id');
    }

    public static function hitung($item, $type)
    {
        return Musnahkantemp::where('item_id', $item)->whereIn('musnahkan_id', Musnahkan::select('id')->where('status', 1))->sum($type);
    }

    public function items()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }
}
