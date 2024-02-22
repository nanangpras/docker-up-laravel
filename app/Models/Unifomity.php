<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Production;

class Unifomity extends Model
{
    //
    protected $table = 'table_qc_uniformity';

    public function uniprod()
    {
        return $this->belongsTo(Production::class, 'production_id', 'id');
    }
}
