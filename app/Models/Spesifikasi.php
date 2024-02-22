<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Spesifikasi extends Model
{
    //
    protected $table = 'spesifikasi';

    public function spesbom()
    {
        return $this->belongsTo(Bom::class, 'bom', 'id');
    }

    public function spesitem()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }
    public function spescus()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }


}
