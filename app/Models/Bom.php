<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bom extends Model
{
    protected $table = 'bom';

    public function bomlist()
    {
        return $this->hasMany(BomItem::class, 'bom_id', 'id')->orderByRaw('kategori DESC, sku ASC') ;
    }

    public function bomplastik()
    {
        return $this->hasMany(BomItem::class, 'bom_id', 'id')->whereIn('item_id', Item::select('id')->where('category_id', 25)) ;
    }

    public function bomproses()
    {
        return $this->hasMany(BomItem::class, 'bom_id', 'id')->whereIn('item_id', Item::select('id')->whereIn('nama', ['BIAYA OVERHEAD','BIAYA TENAGA KERJA', 'ES BALOK'])) ;
    }

    public static function bom_netid($key)
    {
        return Bom::where('bom_name', $key)->first()->netsuite_internal_id ?? "1" ;
    }

    public static function repack_plastik($id)
    {
        $data   =   Item::find($id) ;
        if($data){
            if ($data->category_id == 8) {
                return Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL').' - REPACK AYAM PARTING BROILER FROZEN')->first()->bomplastik ;
            } else
            if ($data->category_id == 9) {
                return Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL').' - REPACK AYAM PARTING MARINASI BROILER FROZEN')->first()->bomplastik ;
            } else {
                return Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL').' - REPACK AYAM BROILER FROZEN')->first()->bomplastik ;
            }
        }else{
            return Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL').' - REPACK AYAM BROILER FROZEN')->first()->bomplastik ;
        }
    }
}
