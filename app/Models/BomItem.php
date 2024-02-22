<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BomItem extends Model
{
    protected $table = 'bom_item';

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    public function bom()
    {
        return $this->belongsTo(Bom::class, 'bom_id', 'id');
    }
}
