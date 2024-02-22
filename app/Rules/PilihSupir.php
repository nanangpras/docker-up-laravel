<?php

namespace App\Rules;

use App\Models\Driver;
use App\Models\Purchasing;
use Illuminate\Contracts\Validation\Rule;

class PilihSupir implements Rule
{
    public $id;

    public function __construct($id)
    {
        $this->id   =   $id;
    }

    public function passes($attribute, $value)
    {
        if ($this->id == 'tangkap') {
            return (Driver::where('id', $value)->count() > 0) ? TRUE : FALSE ;
        } else {
            return TRUE ;
        }
    }

    public function message()
    {
        return 'Input supir salah';
    }
}
