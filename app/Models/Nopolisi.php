<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Nopolisi extends Model
{
    use SoftDeletes ;
    protected $table    =   'no_polisi';
}
