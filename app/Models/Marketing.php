<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Marketing extends Model
{
    //
    protected $table = 'marketing';

    public function marketingcustomer()
    {
        return $this->hasMany(Customer::class, 'id', 'marketing_id')->withTrashed();
    }
}
