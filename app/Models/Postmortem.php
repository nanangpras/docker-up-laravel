<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Postmortem extends Model
{
    //
    protected $table = 'table_qc_postmortem';


    public function postmortem_prod()
    {
        return $this->belongsTo(Production::class, 'production_id', 'id');
    }

}
