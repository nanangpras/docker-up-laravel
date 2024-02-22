<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Adminedit extends Model
{
    //
    protected $table = 'admin_edit';

    public function getCreatedAtAttribute()
    {
        return Carbon::parse($this->attributes['created_at'])->format('d, M Y H:i');
    }

    public function getUser() 
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}

