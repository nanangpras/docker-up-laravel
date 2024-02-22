<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pembelianlist extends Model
{
    use SoftDeletes ;
    protected $table    = 'pembelian_list';
    protected $guarded  = [];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class, 'pembelian_id', 'id');
    }

    public function headbeli()
    {
        return $this->belongsTo(Pembelianheader::class, 'headbeli_id', 'id');
    }

    public static function getParentId($pembelian_id, $id){
        $sql = Pembelianlist::where(function($q) use ($pembelian_id, $id) {
                                $q->where('pembelian_id',$pembelian_id);
                                $q->where('parent',$id);
                            })
                            ->get();
        if($sql->count() > 0){
            foreach($sql as $row){
                $hasil  = $row->headbeli_id;
            }
        }else{
            $hasil      = NULL;
        }
        return $hasil;
    }
    
}
