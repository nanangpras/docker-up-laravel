<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Supplier;

class Supplier_address extends Model
{
    //

    protected $table = 'supplier_address';

    public function customaddsupplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id')->withTrashed();
    }
}
