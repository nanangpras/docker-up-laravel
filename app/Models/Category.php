<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Item;

class Category extends Model
{
    //
    protected $table = 'category';

    public function catitem()
    {
        return $this->hasMany(Item::class, 'id', 'category_id');
    }
}
