<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Supplier;

class Kandang extends Model
{
    //
    protected $table = 'kandang';

    public function kansupp()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id')->withTrashed();
    }
}
