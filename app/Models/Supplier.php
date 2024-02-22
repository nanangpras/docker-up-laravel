<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Purchasing;
use App\Models\Kandang;
use Illuminate\Database\Eloquent\SoftDeletes;
class Supplier extends Model
{
    //
    use SoftDeletes;
    protected $table = 'supplier';

    public function suppurc()
    {
        return $this->hasMany(Purchasing::class, 'supplier_id', 'id');
    }

    public function supkan()
    {
        return $this->hasMany(Kandang::class, 'id', 'supplier_id')->withTrashed();
    }

    public static function supplierlogPO($id){
        $nama = Supplier::where('id', $id)->first();
        return $nama->nama ?? "###";
    }

}
