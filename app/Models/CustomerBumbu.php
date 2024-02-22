<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerBumbu extends Model
{
    use SoftDeletes;

    protected $table = 'customer_bumbu';

    public function customers()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function bumbu()
    {
        return $this->belongsTo(Bumbu::class, 'bumbu_id', 'id');
    }

    public function detail_bumbu()
    {
        return $this->hasOne(BumbuDetail::class, 'customer_id','id');
    }

    public function getStatusBumbuAttribute()
    {
        return $this->attributes['status_bumbu'] == 1 ? 'active' : 'inactive';
    }
}
