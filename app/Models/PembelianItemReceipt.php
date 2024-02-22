<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PembelianItemReceipt extends Model
{
    // use SoftDeletes ;
    protected $table    = 'pembelian_item_receipt';
    protected $guarded  = [];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class, 'pembelian_id', 'id');
    }
    public function header(){
        return $this->belongsTo(Pembelianheader::class, 'pembelian_header_id', 'id');
    }
}
