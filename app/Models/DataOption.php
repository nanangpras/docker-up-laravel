<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DataOption extends Model
{
    use SoftDeletes;
    protected $dates =['deleted_at'];

    protected $table = "options";
    protected $guarded = [];

    public static function getOption($param)
    {
        $data = false;
        
        $value = DataOption::where('slug', $param)->first();
        if($value){
            $data = $value->option_value;
        }
        return $data;
    }

    public static function getIcon($param)
    {
        $data = false;
        
        $value = DataOption::where('slug', $param)->first();
        if($value){
            $data = asset($value->icon);
        }
        return $data;
    }

}
