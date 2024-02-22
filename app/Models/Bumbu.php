<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bumbu extends Model
{
    use SoftDeletes;
    protected $table = 'bumbu';
    
    public function freetemp()
    {
        return $this->hasMany(FreestockTemp::class,'bumbu_id','id')->select('bumbu_id','bumbu_berat','created_at','regu');
    }

    public function bumbu_detail()
    {
        return $this->hasMany(BumbuDetail::class,'bumbu_id','id');
    }

    public function customer_bumbu()
    {
        return $this->hasMany(CustomerBumbu::class, 'bumbu_id', 'id');
    }

    // Di dalam model Bumbu
    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'customer_bumbu', 'bumbu_id', 'customer_id');
    }

}
