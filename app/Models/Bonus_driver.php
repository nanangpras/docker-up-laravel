<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Driver;
use App\Models\Production;

class Bonus_driver extends Model
{
    //

    protected $table = 'bonus_driver';

    public function bonusdriver()
    {
        return $this->belongsTo(Driver::class, 'driver_id', 'id')->withTrashed();
    }

    public function bonusproduction()
    {
        return $this->belongsTo(Production::class, 'trans_id', 'id')->withTrashed();
    }
}
