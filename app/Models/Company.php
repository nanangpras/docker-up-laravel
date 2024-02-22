<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Gudang;

class Company extends Model
{
    //

    protected $table = 'company';

    public function companyuser()
    {
        return $this->hasMany(User::class, 'id', 'company_id')->withTrashed();
    }

    public function companygudang()
    {
        return $this->hasMany(Gudang::class, 'id', 'company_id')->withTrashed();
    }


}
