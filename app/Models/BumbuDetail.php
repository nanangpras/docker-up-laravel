<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BumbuDetail extends Model
{
    use SoftDeletes;
    protected $table = 'bumbu_details';

    public function bumbu()
    {
        return $this->belongsTo(Bumbu::class, 'bumbu_id', 'id');
    }

    public function freestocktemp()
    {
        return $this->hasOne(BumbuDetail::class, 'bumbu_detail_id', 'id');
    }

    public function customer_bumbu()
    {
        return $this->belongsTo(CustomerBumbu::class, 'bumbu_customer_id', 'id');
    }

    public function getTanggalAttribute()
    {
        return Carbon::parse($this->attributes['tanggal'])->format('d, M Y');
    }
    
}
